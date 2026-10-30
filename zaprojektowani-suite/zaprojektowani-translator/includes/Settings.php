<?php
namespace ZPTE;

if (!defined('ABSPATH')) { exit; }

/**
 * Settings (option zpte2_settings) and the OpenAI API key. The key is the one already saved for the
 * translator (option zpte_api_key, encrypted with the site salts, never autoloaded or shown again);
 * wp-config.php can define ZPTE_OPENAI_API_KEY instead.
 * 2.1.0: each language has its own settings (zpte2_settings for English, zpte2_de_settings…, see Lang::opt); the key
 * is one for all. German and Spanish start with GPT-6.1 Sol, the most capable GPT-6.1 model, at high reasoning.
 */
final class Settings {
    const OPTION = 'zpte2_settings';
    const KEY_OPTION = 'zpte_api_key';
    const DEFAULT_MODEL = 'gpt-6-luna';
    const DEFAULT_EFFORT = 'high';
    const AI_MODEL = 'gpt-6.1-sol';
    const EFFORTS = ['low' => 'niskie (najszybciej)', 'medium' => 'średnie', 'high' => 'wysokie (zalecane)', 'xhigh' => 'bardzo wysokie (droższe)', 'max' => 'maksymalne (najdroższe)'];

    /** Prices per million tokens (USD) for the cost counter; other models count with the Luna rate. */
    const PRICES = [
        'gpt-6-luna' => ['in' => 0.10, 'out' => 0.50],
        'gpt-6.1-sol' => ['in' => 2.00, 'out' => 10.00],
        'gpt-6.1-terra' => ['in' => 1.00, 'out' => 5.00],
        'gpt-6-astra' => ['in' => 10.00, 'out' => 50.00],
    ];

    private static function option(): string { return Lang::opt(self::OPTION); }

    public static function defaults(): array {
        if (!Lang::is_en()) {
            return [
                'on' => 1,                   // German / Spanish version of the site is made and kept up to date
                'public' => 'auto',          // shown to visitors and Google after the first full translation ('on', 'off')
                'enabled' => 1,
                'auto' => 1,
                'model' => self::AI_MODEL,
                'effort' => self::DEFAULT_EFFORT,
                'types' => ['post', 'page'],
                'terms' => 1,
                'recheck' => 1,
                'daily_chars' => 2500000,    // the whole site (about 2.1 million Polish characters) in one day
                'monthly_usd' => 60,         // the first full translation of the site costs about $20–30
                'instructions' => '',
            ];
        }
        return [
            'enabled' => 1,              // daily check at 03:00 (Europe/Warsaw)
            'auto' => 1,                 // translate a post right after it is published or updated
            'model' => self::DEFAULT_MODEL,
            'effort' => self::DEFAULT_EFFORT,
            'types' => ['post', 'page'],
            'terms' => 1,                // category pages get English versions too
            'recheck' => 1,              // the daily check reads every English page again (menus, footer, lists)
            'daily_chars' => 1500000,    // Polish characters sent to OpenAI per day
            'monthly_usd' => 20,         // spending cap per calendar month (0 = none)
            'instructions' => '',        // extra instructions from the site owner
        ];
    }

    public static function all(): array {
        $o = get_option(self::option(), []);
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
        update_option(self::option(), array_merge(self::all(), $values), true);
    }

    public static function model(): string {
        $m = trim((string) self::get('model'));
        return preg_match('~^[A-Za-z0-9._:-]{2,80}$~', $m) ? $m : (Lang::is_en() ? self::DEFAULT_MODEL : self::AI_MODEL);
    }

    public static function effort(): string {
        $e = (string) self::get('effort');
        return isset(self::EFFORTS[$e]) ? $e : self::DEFAULT_EFFORT;
    }

    public static function price(string $model): array {
        return self::PRICES[$model] ?? self::PRICES[self::DEFAULT_MODEL];
    }

    /** Post types offered in the settings: public, with their own URLs. */
    public static function public_types(): array {
        $out = [];
        foreach (get_post_types(['public' => true], 'objects') as $name => $obj) {
            if ($name === 'attachment' || (empty($obj->publicly_queryable) && $name !== 'page')) { continue; }
            $out[$name] = (string) ($obj->labels->name ?? $name);
        }
        return $out;
    }

    public static function types(): array {
        $public = self::public_types();
        return array_values(array_filter(array_map('strval', (array) self::get('types')), static function ($t) use ($public) { return isset($public[$t]); }));
    }

    // ------------------------------------------------------------------ API key

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

    /** A key is saved but can no longer be decrypted (WordPress salts changed). */
    public static function key_unreadable(): bool {
        $raw = get_option(self::KEY_OPTION, '');
        return is_string($raw) && $raw !== '' && self::decrypt($raw) === '';
    }

    public static function save_key(string $key): void {
        update_option(self::KEY_OPTION, self::encrypt($key), false);
    }

    public static function delete_key(): void { delete_option(self::KEY_OPTION); }

    /** "sk-…a1B2": enough to recognise the key, useless to anyone reading the screen. */
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
