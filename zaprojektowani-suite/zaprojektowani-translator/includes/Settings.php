<?php
namespace ZPTE;

if (!defined('ABSPATH')) { exit; }

/** Settings (option zpte_settings) and the OpenAI API key (option zpte_api_key, encrypted, never autoloaded). */
final class Settings {
    const OPTION = 'zpte_settings';
    const KEY_OPTION = 'zpte_api_key';
    const DEFAULT_MODEL = 'gpt-4.1';

    public static function defaults(): array {
        return [
            'enabled' => 1,             // daily check on/off
            'model' => self::DEFAULT_MODEL,
            'mode' => 'publish',        // publish | draft (new pages only)
            'types' => ['post', 'page'],
            'since' => '',              // Y-m-d; content published on/after this day counts as new
            'older' => 0,               // also translate older content without an English version
            'gaps' => 1,                // fill untranslated fragments on existing English pages
            'daily_chars' => 400000,    // characters of Polish text sent to OpenAI per day
            'instructions' => '',       // extra instructions for the translator (style, glossary)
        ];
    }

    public static function all(): array {
        $o = get_option(self::OPTION, []);
        $all = array_merge(self::defaults(), is_array($o) ? $o : []);
        if (!is_array($all['types'])) { $all['types'] = self::defaults()['types']; }
        return $all;
    }

    /** @return mixed */
    public static function get(string $key) {
        $all = self::all();
        return $all[$key] ?? null;
    }

    public static function update(array $values): void {
        update_option(self::OPTION, array_merge(self::all(), $values), true);
    }

    /** First run: new content = published during the last 7 days or later. */
    public static function ensure_since(): void {
        if ((string) self::get('since') !== '') { return; }
        self::update(['since' => wp_date('Y-m-d', time() - 7 * DAY_IN_SECONDS)]);
    }

    /** Start of the "new content" window in GMT (Y-m-d H:i:s). */
    public static function since_gmt(): string {
        $day = (string) self::get('since');
        if (!preg_match('~^\d{4}-\d{2}-\d{2}$~', $day)) { $day = wp_date('Y-m-d', time() - 7 * DAY_IN_SECONDS); }
        return get_gmt_from_date($day . ' 00:00:00');
    }

    public static function model(): string {
        $m = trim((string) self::get('model'));
        return preg_match('~^[A-Za-z0-9._:-]{2,80}$~', $m) ? $m : self::DEFAULT_MODEL;
    }

    /** Post types offered in the settings: public, with their own URLs. */
    public static function public_types(): array {
        $out = [];
        foreach (get_post_types(['public' => true], 'objects') as $name => $obj) {
            if ($name === 'attachment' || empty($obj->publicly_queryable) && $name !== 'page') { continue; }
            $out[$name] = (string) ($obj->labels->name ?? $name);
        }
        return $out;
    }

    public static function types(): array {
        $public = self::public_types();
        return array_values(array_filter(array_map('strval', (array) self::get('types')), static function ($t) use ($public) { return isset($public[$t]); }));
    }

    // ------------------------------------------------------------------ API key

    /** wp-config.php can define ZPTE_OPENAI_API_KEY instead of saving the key in the database. */
    public static function key_source(): string {
        if (defined('ZPTE_OPENAI_API_KEY') && is_string(ZPTE_OPENAI_API_KEY) && trim(ZPTE_OPENAI_API_KEY) !== '') { return 'constant'; }
        return self::stored_key() !== '' ? 'option' : '';
    }

    public static function api_key(): string {
        if (self::key_source() === 'constant') { return trim((string) ZPTE_OPENAI_API_KEY); }
        return self::stored_key();
    }

    private static function stored_key(): string {
        $raw = get_option(self::KEY_OPTION, '');
        return is_string($raw) && $raw !== '' ? self::decrypt($raw) : '';
    }

    /** True when a key is saved but can no longer be decrypted (WordPress salts changed). */
    public static function key_unreadable(): bool {
        $raw = get_option(self::KEY_OPTION, '');
        return is_string($raw) && $raw !== '' && self::decrypt($raw) === '';
    }

    public static function save_key(string $key): void {
        update_option(self::KEY_OPTION, self::encrypt($key), false);
    }

    public static function delete_key(): void { delete_option(self::KEY_OPTION); }

    /** "sk-…a1B2" — enough to recognise the key, useless to anyone reading the screen. */
    public static function key_hint(): string {
        $k = self::api_key();
        if ($k === '') { return ''; }
        return (strpos($k, 'sk-') === 0 ? 'sk-' : '') . '…' . substr($k, -4);
    }

    public static function looks_like_key(string $key): bool {
        return (bool) preg_match('~^[A-Za-z0-9_\-]{20,300}$~', $key);
    }

    private static function secret(): string {
        return hash('sha256', 'zpte|' . wp_salt('auth') . '|' . (defined('AUTH_KEY') ? AUTH_KEY : ''), true);
    }

    private static function encrypt(string $plain): string {
        if (function_exists('sodium_crypto_secretbox')) {
            try {
                $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
                return 's1:' . base64_encode($nonce . sodium_crypto_secretbox($plain, $nonce, self::secret()));
            } catch (\Throwable $e) {
                // fall through to the obfuscated form below
            }
        }
        return 'b0:' . base64_encode($plain);
    }

    private static function decrypt(string $stored): string {
        if (strpos($stored, 's1:') === 0 && function_exists('sodium_crypto_secretbox_open')) {
            $bin = base64_decode(substr($stored, 3), true);
            if ($bin === false || strlen($bin) <= SODIUM_CRYPTO_SECRETBOX_NONCEBYTES) { return ''; }
            try {
                $plain = sodium_crypto_secretbox_open(substr($bin, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES), substr($bin, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES), self::secret());
            } catch (\Throwable $e) {
                return '';
            }
            return is_string($plain) ? $plain : '';
        }
        if (strpos($stored, 'b0:') === 0) {
            $plain = base64_decode(substr($stored, 3), true);
            return is_string($plain) ? $plain : '';
        }
        return '';
    }
}
