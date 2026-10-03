<?php
namespace ZPTE;

if (!defined('ABSPATH')) { exit; }

/**
 * Minimal OpenAI Chat Completions client (wp_remote_post, no SDK).
 * Result: ['ok' => bool, 'data' => array|null, 'error' => string, 'fatal' => bool, 'retry' => bool,
 *          'truncated' => bool, 'in' => int, 'out' => int]
 *   fatal — only the administrator can fix it (key, credit, model): stop until the settings change;
 *   retry — temporary (network, rate limit, server error): try again on the next run.
 */
final class OpenAI {
    public static function base(): string {
        // ZPTE_OPENAI_BASE (wp-config.php) points the module at a compatible endpoint or a test server.
        $b = defined('ZPTE_OPENAI_BASE') && is_string(ZPTE_OPENAI_BASE) && ZPTE_OPENAI_BASE !== '' ? ZPTE_OPENAI_BASE : 'https://api.openai.com/v1';
        return rtrim($b, '/');
    }

    private static function result(array $r): array {
        return array_merge(['ok' => false, 'data' => null, 'error' => '', 'fatal' => false, 'retry' => false, 'truncated' => false, 'in' => 0, 'out' => 0], $r);
    }

    /** Reasoning models (o-series, gpt-5) reject a custom temperature. */
    private static function takes_temperature(string $model): bool {
        return !preg_match('~^(?:o\d|gpt-5)~i', $model);
    }

    /**
     * One structured-output request. $schema is a JSON schema for the answer object.
     */
    public static function json(array $messages, string $name, array $schema, int $max_tokens = 12000): array {
        $key = Settings::api_key();
        if ($key === '') { return self::result(['error' => 'Brak klucza API OpenAI — wklej go w ZP Suite → Tłumacz EN → Ustawienia.', 'fatal' => true]); }
        $model = Settings::model();
        $body = [
            'model' => $model,
            'messages' => $messages,
            'response_format' => ['type' => 'json_schema', 'json_schema' => ['name' => $name, 'strict' => true, 'schema' => $schema]],
            'max_completion_tokens' => $max_tokens,
        ];
        if (self::takes_temperature($model)) { $body['temperature'] = 0.2; }

        for ($attempt = 0; $attempt < 3; $attempt++) {
            $res = wp_remote_post(self::base() . '/chat/completions', [
                'timeout' => 150,
                'headers' => ['Authorization' => 'Bearer ' . $key, 'Content-Type' => 'application/json'],
                'body' => wp_json_encode($body),
                'data_format' => 'body',
            ]);
            if (is_wp_error($res)) {
                return self::result(['error' => 'Brak połączenia z OpenAI: ' . $res->get_error_message(), 'retry' => true]);
            }
            $code = (int) wp_remote_retrieve_response_code($res);
            $json = json_decode((string) wp_remote_retrieve_body($res), true);
            $err = is_array($json) && isset($json['error']) && is_array($json['error']) ? $json['error'] : [];
            $msg = (string) ($err['message'] ?? '');
            $errCode = (string) ($err['code'] ?? '');
            $param = (string) ($err['param'] ?? '');

            if ($code === 400 && isset($body['temperature']) && ($param === 'temperature' || stripos($msg, 'temperature') !== false)) {
                unset($body['temperature']);
                continue;
            }
            if ($code === 400 && ($body['response_format']['type'] ?? '') === 'json_schema' && ($param === 'response_format' || stripos($msg, 'response_format') !== false || stripos($msg, 'json_schema') !== false)) {
                // Older models: plain JSON mode, the answer is still validated by Translator.
                $body['response_format'] = ['type' => 'json_object'];
                $body['messages'][0]['content'] .= "\n\nAnswer with one JSON object only, shaped like this JSON schema: " . wp_json_encode($schema);
                continue;
            }
            if ($code === 401) {
                return self::result(['error' => 'OpenAI odrzuciło klucz API (401). Sprawdź, czy klucz jest aktywny, i wklej go ponownie.', 'fatal' => true]);
            }
            if ($code === 403) {
                return self::result(['error' => 'OpenAI odmówiło dostępu (403): ' . self::short($msg), 'fatal' => true]);
            }
            if ($code === 404) {
                return self::result(['error' => 'Model „' . $model . '” jest niedostępny dla tego klucza (404). Wybierz inny model w ustawieniach.', 'fatal' => true]);
            }
            if ($code === 429) {
                if ($errCode === 'insufficient_quota' || stripos($msg, 'quota') !== false || stripos($msg, 'billing') !== false) {
                    return self::result(['error' => 'Na koncie OpenAI skończyły się środki lub limit (429 insufficient_quota). Doładuj konto na platform.openai.com.', 'fatal' => true]);
                }
                return self::result(['error' => 'OpenAI ogranicza liczbę zapytań (429) — ponowię przy następnym uruchomieniu.', 'retry' => true]);
            }
            if ($code >= 500 || $code === 408 || $code === 0) {
                return self::result(['error' => 'Błąd po stronie OpenAI (' . $code . ') — ponowię przy następnym uruchomieniu.', 'retry' => true]);
            }
            if ($code !== 200 || !is_array($json)) {
                return self::result(['error' => 'OpenAI: błąd ' . $code . ($msg !== '' ? ' — ' . self::short($msg) : '')]);
            }

            $choice = $json['choices'][0] ?? [];
            $in = (int) ($json['usage']['prompt_tokens'] ?? 0);
            $out = (int) ($json['usage']['completion_tokens'] ?? 0);
            $content = $choice['message']['content'] ?? null;
            if (!empty($choice['message']['refusal'])) {
                return self::result(['error' => 'Model odmówił odpowiedzi: ' . self::short((string) $choice['message']['refusal']), 'in' => $in, 'out' => $out]);
            }
            if (($choice['finish_reason'] ?? '') === 'length') {
                return self::result(['error' => 'Odpowiedź za długa', 'truncated' => true, 'in' => $in, 'out' => $out]);
            }
            $data = is_string($content) ? json_decode($content, true) : null;
            if (!is_array($data)) {
                return self::result(['error' => 'OpenAI zwróciło odpowiedź, której nie da się odczytać.', 'in' => $in, 'out' => $out]);
            }
            return self::result(['ok' => true, 'data' => $data, 'in' => $in, 'out' => $out]);
        }
        return self::result(['error' => 'OpenAI nie przyjęło zapytania.']);
    }

    /** Key check for the settings screen: models available to the key. */
    public static function models(): array {
        $key = Settings::api_key();
        if ($key === '') { return ['ok' => false, 'error' => 'Brak klucza API.', 'ids' => []]; }
        $res = wp_remote_get(self::base() . '/models', ['timeout' => 20, 'headers' => ['Authorization' => 'Bearer ' . $key]]);
        if (is_wp_error($res)) { return ['ok' => false, 'error' => 'Brak połączenia z OpenAI: ' . $res->get_error_message(), 'ids' => []]; }
        $code = (int) wp_remote_retrieve_response_code($res);
        $json = json_decode((string) wp_remote_retrieve_body($res), true);
        if ($code === 401) { return ['ok' => false, 'error' => 'OpenAI odrzuciło klucz API (401).', 'ids' => []]; }
        if ($code !== 200 || !is_array($json)) {
            $msg = is_array($json) ? (string) ($json['error']['message'] ?? '') : '';
            return ['ok' => false, 'error' => 'OpenAI: błąd ' . $code . ($msg !== '' ? ' — ' . self::short($msg) : ''), 'ids' => []];
        }
        $ids = [];
        foreach ((array) ($json['data'] ?? []) as $m) { if (!empty($m['id'])) { $ids[] = (string) $m['id']; } }
        sort($ids);
        return ['ok' => true, 'error' => '', 'ids' => $ids];
    }

    private static function short(string $s): string {
        $s = trim(preg_replace('~\s+~', ' ', $s) ?? $s);
        // Never echo anything that looks like a key back to the screen or the log.
        $s = preg_replace('~sk-[A-Za-z0-9_\-*]{6,}~', 'sk-…', $s) ?? $s;
        return function_exists('mb_substr') ? mb_substr($s, 0, 220) : substr($s, 0, 220);
    }
}
