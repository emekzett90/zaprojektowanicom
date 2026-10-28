<?php
namespace ZPTE;

if (!defined('ABSPATH')) { exit; }

/**
 * OpenAI Responses API client (wp_remote_*, no SDK). Requests run in background mode: sending one takes a
 * moment, the answer is collected by later worker steps, so no page load or cron run ever waits for a model.
 *
 * Every call returns an array with at least:
 *   ok     — the request did what was asked;
 *   error  — a Polish message for the panel and the log (never contains the key);
 *   fatal  — only the administrator can fix it (key, credit, model): the queue stops until settings change;
 *   retry  — temporary (network, rate limit, server error): the same step is repeated later.
 */
final class OpenAI {
    const POST_TIMEOUT = 25;
    const GET_TIMEOUT = 15;

    public static function base(): string {
        // ZPTE_OPENAI_BASE (wp-config.php) points the module at a compatible endpoint or a test server.
        $b = defined('ZPTE_OPENAI_BASE') && is_string(ZPTE_OPENAI_BASE) && ZPTE_OPENAI_BASE !== '' ? ZPTE_OPENAI_BASE : 'https://api.openai.com/v1';
        return rtrim($b, '/');
    }

    private static function result(array $r): array {
        return array_merge(['ok' => false, 'error' => '', 'fatal' => false, 'retry' => false, 'http' => 0], $r);
    }

    private static function headers(string $key): array {
        return ['Authorization' => 'Bearer ' . $key, 'Content-Type' => 'application/json', 'Accept' => 'application/json'];
    }

    /**
     * Start a background response. $body is a complete Responses API request without "background"/"store".
     * Returns id and status; a server that answers at once (no background support) returns the parsed answer too.
     */
    public static function submit(array $body): array {
        $key = Settings::api_key();
        if ($key === '') { return self::result(['error' => 'Brak klucza API OpenAI. Wklej go w ZP Suite → Tłumacz EN → Ustawienia.', 'fatal' => true]); }
        $body['background'] = true;
        $body['store'] = true; // background responses must be stored until they are collected
        for ($attempt = 0; $attempt < 3; $attempt++) {
            $res = wp_remote_post(self::base() . '/responses', [
                'timeout' => self::POST_TIMEOUT,
                'redirection' => 0,
                'headers' => self::headers($key),
                'body' => (string) wp_json_encode($body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'data_format' => 'body',
            ]);
            if (is_wp_error($res)) {
                return self::result(['error' => 'Brak połączenia z OpenAI: ' . self::short($res->get_error_message()), 'retry' => true]);
            }
            $code = (int) wp_remote_retrieve_response_code($res);
            $json = json_decode((string) wp_remote_retrieve_body($res), true);
            if ($code === 400 && self::param_rejected($json, 'reasoning') && isset($body['reasoning'])) {
                // A model without reasoning settings: the same request without them.
                unset($body['reasoning']);
                continue;
            }
            if ($code === 400 && self::param_rejected($json, 'service_tier') && isset($body['service_tier'])) {
                unset($body['service_tier']);
                continue;
            }
            if ($code !== 200) { return self::failure($code, $json, (string) ($body['model'] ?? '')); }
            if (!is_array($json) || !is_string($json['id'] ?? null) || $json['id'] === '') {
                return self::result(['error' => 'OpenAI przyjęło zapytanie bez numeru odpowiedzi. Ponowię je.', 'retry' => true, 'http' => $code]);
            }
            $out = self::result(['ok' => true, 'id' => (string) $json['id'], 'status' => (string) ($json['status'] ?? ''), 'http' => $code]);
            if (!in_array($out['status'], ['queued', 'in_progress'], true)) { $out['answer'] = self::parse($json); }
            return $out;
        }
        return self::result(['error' => 'OpenAI nie przyjęło zapytania.', 'retry' => true]);
    }

    /**
     * State of a background response. 'done' => true once it finished (well or not); 'answer' holds the parsed
     * result then (see parse()).
     */
    public static function fetch(string $id): array {
        $key = Settings::api_key();
        if ($key === '') { return self::result(['error' => 'Brak klucza API OpenAI.', 'fatal' => true]); }
        if (!preg_match('~^[A-Za-z0-9_-]{1,200}$~', $id)) { return self::result(['error' => 'Nieprawidłowy numer odpowiedzi OpenAI.', 'lost' => true]); }
        $res = wp_remote_get(self::base() . '/responses/' . rawurlencode($id), ['timeout' => self::GET_TIMEOUT, 'redirection' => 0, 'headers' => self::headers($key)]);
        if (is_wp_error($res)) {
            return self::result(['error' => 'Brak połączenia z OpenAI: ' . self::short($res->get_error_message()), 'retry' => true]);
        }
        $code = (int) wp_remote_retrieve_response_code($res);
        $json = json_decode((string) wp_remote_retrieve_body($res), true);
        if ($code === 404) {
            // The answer is gone (expired or never stored): the batch is simply sent again.
            return self::result(['error' => 'OpenAI nie ma już tej odpowiedzi; wyślę fragmenty ponownie.', 'lost' => true, 'http' => 404]);
        }
        if ($code !== 200) { return self::failure($code, $json, ''); }
        if (!is_array($json)) { return self::result(['error' => 'Nieczytelna odpowiedź OpenAI.', 'retry' => true, 'http' => $code]); }
        $status = (string) ($json['status'] ?? '');
        if (in_array($status, ['queued', 'in_progress'], true)) {
            return self::result(['ok' => true, 'done' => false, 'status' => $status, 'http' => $code]);
        }
        return self::result(['ok' => true, 'done' => true, 'status' => $status, 'answer' => self::parse($json), 'http' => $code]);
    }

    /** Stop a background response that is no longer needed (page changed, queue cleared). Best effort. */
    public static function cancel(string $id): void {
        $key = Settings::api_key();
        if ($key === '' || !preg_match('~^[A-Za-z0-9_-]{1,200}$~', $id)) { return; }
        wp_remote_post(self::base() . '/responses/' . rawurlencode($id) . '/cancel', ['timeout' => 5, 'redirection' => 0, 'blocking' => false, 'headers' => self::headers($key), 'body' => '{}']);
    }

    /**
     * Finished response => ['status', 'data' (decoded JSON object or null), 'refusal', 'truncated', 'in', 'out', 'error'].
     */
    public static function parse(array $json): array {
        $status = (string) ($json['status'] ?? '');
        $out = ['status' => $status, 'data' => null, 'refusal' => '', 'truncated' => false, 'in' => 0, 'out' => 0, 'error' => ''];
        $usage = is_array($json['usage'] ?? null) ? $json['usage'] : [];
        $out['in'] = max(0, (int) ($usage['input_tokens'] ?? 0));
        $out['out'] = max(0, (int) ($usage['output_tokens'] ?? 0));
        if ($status === 'incomplete') {
            $reason = (string) ($json['incomplete_details']['reason'] ?? '');
            $out['truncated'] = $reason === 'max_output_tokens';
            $out['error'] = $out['truncated'] ? 'Odpowiedź za długa.' : 'OpenAI przerwało odpowiedź (' . self::short($reason ?: 'incomplete') . ').';
            return $out;
        }
        if ($status !== 'completed') {
            $msg = is_array($json['error'] ?? null) ? (string) ($json['error']['message'] ?? '') : '';
            $out['error'] = 'OpenAI: odpowiedź ' . ($status !== '' ? $status : 'bez stanu') . ($msg !== '' ? ' — ' . self::short($msg) : '') . '.';
            return $out;
        }
        $text = '';
        foreach ((array) ($json['output'] ?? []) as $item) {
            if (!is_array($item) || ($item['type'] ?? '') !== 'message') { continue; }
            foreach ((array) ($item['content'] ?? []) as $part) {
                if (!is_array($part)) { continue; }
                if (($part['type'] ?? '') === 'refusal') { $out['refusal'] = self::short((string) ($part['refusal'] ?? '')); }
                if (($part['type'] ?? '') === 'output_text' && is_string($part['text'] ?? null)) { $text .= $part['text']; }
            }
        }
        if ($text === '' && is_string($json['output_text'] ?? null)) { $text = (string) $json['output_text']; }
        if ($out['refusal'] !== '' && $text === '') { $out['error'] = 'Model odmówił odpowiedzi: ' . $out['refusal']; return $out; }
        $data = json_decode($text, true, 64);
        if (!is_array($data)) { $out['error'] = 'OpenAI zwróciło odpowiedź, której nie da się odczytać.'; return $out; }
        $out['data'] = $data;
        return $out;
    }

    /** Models available to the key (settings screen). */
    public static function models(): array {
        $key = Settings::api_key();
        if ($key === '') { return ['ok' => false, 'error' => 'Brak klucza API.', 'ids' => []]; }
        $res = wp_remote_get(self::base() . '/models', ['timeout' => 20, 'redirection' => 0, 'headers' => self::headers($key)]);
        if (is_wp_error($res)) { return ['ok' => false, 'error' => 'Brak połączenia z OpenAI: ' . self::short($res->get_error_message()), 'ids' => []]; }
        $code = (int) wp_remote_retrieve_response_code($res);
        $json = json_decode((string) wp_remote_retrieve_body($res), true);
        if ($code !== 200 || !is_array($json)) {
            $f = self::failure($code, $json, '');
            return ['ok' => false, 'error' => $f['error'], 'ids' => []];
        }
        $ids = [];
        foreach ((array) ($json['data'] ?? []) as $m) {
            $id = is_array($m) ? (string) ($m['id'] ?? '') : '';
            // Text models only: no audio, image, embedding or moderation models in the list.
            if ($id === '' || preg_match('~(?:embedding|whisper|tts|dall-e|image|audio|realtime|moderation|transcribe|search|computer-use|davinci|babbage)~i', $id)) { continue; }
            $ids[] = $id;
        }
        sort($ids);
        return ['ok' => true, 'error' => '', 'ids' => $ids];
    }

    /** @param mixed $json */
    private static function param_rejected($json, string $param): bool {
        if (!is_array($json) || !is_array($json['error'] ?? null)) { return false; }
        $p = (string) ($json['error']['param'] ?? '');
        $m = (string) ($json['error']['message'] ?? '');
        return strpos($p, $param) === 0 || stripos($m, $param) !== false;
    }

    /** @param mixed $json */
    private static function failure(int $code, $json, string $model): array {
        $err = is_array($json) && is_array($json['error'] ?? null) ? $json['error'] : [];
        $msg = (string) ($err['message'] ?? '');
        $type = (string) ($err['code'] ?? ($err['type'] ?? ''));
        if ($code === 401) {
            return self::result(['error' => 'OpenAI odrzuciło klucz API (401). Sprawdź, czy klucz jest aktywny, i wklej go ponownie.', 'fatal' => true, 'http' => $code]);
        }
        if ($code === 403) {
            return self::result(['error' => 'OpenAI odmówiło dostępu (403)' . ($msg !== '' ? ': ' . self::short($msg) : '.'), 'fatal' => true, 'http' => $code]);
        }
        if ($code === 404) {
            return self::result(['error' => $model !== '' ? 'Model „' . $model . '” jest niedostępny dla tego klucza (404). Wybierz inny model w ustawieniach.' : 'OpenAI: nie znaleziono (404).', 'fatal' => $model !== '', 'http' => $code]);
        }
        if ($code === 429) {
            if ($type === 'insufficient_quota' || stripos($msg, 'quota') !== false || stripos($msg, 'billing') !== false) {
                return self::result(['error' => 'Na koncie OpenAI skończyły się środki lub limit (429). Doładuj konto na platform.openai.com.', 'fatal' => true, 'http' => $code]);
            }
            return self::result(['error' => 'OpenAI chwilowo ogranicza liczbę zapytań (429). Ponowię automatycznie.', 'retry' => true, 'http' => $code]);
        }
        if ($code === 0 || $code === 408 || $code === 409 || $code >= 500) {
            return self::result(['error' => 'Chwilowy błąd po stronie OpenAI (' . $code . '). Ponowię automatycznie.', 'retry' => true, 'http' => $code]);
        }
        return self::result(['error' => 'OpenAI: błąd ' . $code . ($msg !== '' ? ' — ' . self::short($msg) : ''), 'fatal' => $code === 400 && $model !== '' && stripos($msg, 'model') !== false, 'bad_request' => true, 'http' => $code]);
    }

    public static function short(string $s): string {
        $s = trim(preg_replace('~\s+~', ' ', $s) ?? $s);
        // Never echo anything that looks like a key back to the screen or the log.
        $s = preg_replace('~sk-[A-Za-z0-9_\-*]{6,}~', 'sk-…', $s) ?? $s;
        return function_exists('mb_substr') ? mb_substr($s, 0, 220) : substr($s, 0, 220);
    }
}
