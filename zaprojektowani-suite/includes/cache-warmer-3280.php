<?php
/** Private, bounded background warming of the already-cacheable home/knowledge indexes. */
if (!defined('ABSPATH')) { exit; }

final class ZP_Cache_Warmer_3280 {
    public const EVENT = 'zp_html_cache_warm_3280';
    public const LOCK = 'zp_html_cache_warm_lock_3280';
    public const STATUS = 'zp_html_cache_warm_status_3280';
    public const UA = 'ZP-Suite-Cache-Warmer/1.0';

    /** An installed foreign/disabled/old cache never gets loopback requests. */
    public static function policy(): ?array {
        if (!defined('WP_CACHE') || !WP_CACHE || empty(get_option(ZP_HTML_Cache_20261009::STATE, [])['enabled'])) { return null; }
        $dir = ZP_HTML_Cache_20261009::dir(); $file = $dir . '/policy.php';
        $dropin = ZP_HTML_Cache_20261009::dropin(); $template = ZP_HTML_Cache_20261009::template();
        $backup = get_option(ZP_HTML_Cache_20261009::BACKUP, []);
        if (is_link($dir) || is_link($file) || !is_file($file) || filesize($file) > 1048576
            || is_link($dropin) || !is_file($dropin) || !is_file($template)
            || ($backup['dropin_installed_sha'] ?? '') !== hash_file('sha256', $dropin)
            || hash_file('sha256', $dropin) !== hash_file('sha256', $template)) { return null; }
        $raw = @file_get_contents($file);
        if (!is_string($raw) || strpos($raw, ZP_HTML_Cache_20261009::PREFIX) !== 0) { return null; }
        $p = json_decode(substr($raw, strlen(ZP_HTML_Cache_20261009::PREFIX)), true);
        $home = wp_parse_url(home_url('/'));
        if (!is_array($p) || ($p['schema'] ?? null) !== 1 || ($p['enabled'] ?? null) !== true
            || !is_int($p['ttl'] ?? null) || $p['ttl'] < 60 || $p['ttl'] > 1800
            || !is_string($p['generation'] ?? null) || !preg_match('/^[a-f0-9]{32}$/D', $p['generation'])
            || !is_string($p['warm_secret'] ?? null) || !preg_match('/^[a-f0-9]{64}$/D', $p['warm_secret'])
            || !is_array($p['paths'] ?? null) || !is_array($p['warm_paths'] ?? null) || count($p['warm_paths']) > 4
            || !is_int($p['next_publication'] ?? null) || ($p['next_publication'] && $p['next_publication'] <= time())
            || ($home['scheme'] ?? '') !== 'https' || isset($home['port']) || ($p['host'] ?? '') !== ($home['host'] ?? '')) { return null; }
        return $p;
    }

    /** No cron request from disabled cache, repeated queue calls or the cron worker itself. */
    public static function queue(): void {
        if (!self::policy() || wp_next_scheduled(self::EVENT)) { return; }
        $queued = wp_schedule_single_event(time(), self::EVENT, [], true);
        if (!$queued || is_wp_error($queued)) { return; }
        if ((!defined('DISABLE_WP_CRON') || !DISABLE_WP_CRON) && !wp_doing_cron() && function_exists('spawn_cron')) { spawn_cron(); }
    }

    public static function cancel(): void { wp_clear_scheduled_hook(self::EVENT); }

    /** Same key as the drop-in; a translated page's current version is included. */
    public static function key(array $p, string $path, string $device): string {
        $entry = $p['paths'][$path] ?? true;
        return hash('sha256', $p['generation'] . "\nhttps\n" . $p['host'] . "\n" . $path . "\n" . $device . (is_string($entry) ? "\n" . $entry : ''));
    }

    public static function signature(array $p, string $path, string $device, ?int $now = null): string {
        $stamp = (string) ($now ?? time());
        $mac = hash_hmac('sha256', "zp-cache-warm-v1\n" . $stamp . "\n" . self::key($p, $path, $device), $p['warm_secret']);
        return 'v1:' . $stamp . ':' . $device . ':' . $p['generation'] . ':' . $mac;
    }

    /** Read only cache metadata; never load or keep response HTML in the worker. */
    private static function due(array $p, string $path, string $device): bool {
        $key = self::key($p, $path, $device); $file = ZP_HTML_Cache_20261009::dir() . '/zpc-' . $key . '.php';
        if (is_link($file) || !is_file($file) || filesize($file) > 4194304 + 8192) { return true; }
        $handle = @fopen($file, 'rb'); if (!$handle) { return true; }
        try { $prefix = fgets($handle, 32); $line = fgets($handle, 8193); }
        finally { fclose($handle); }
        if ($prefix !== ZP_HTML_Cache_20261009::PREFIX || !is_string($line)) { return true; }
        $meta = json_decode($line, true);
        return !is_array($meta) || ($meta['kind'] ?? '') !== 'zp-public-html-v1' || ($meta['key'] ?? '') !== $key
            || ($meta['generation'] ?? '') !== $p['generation'] || !is_int($meta['created'] ?? null)
            || !is_int($meta['expires'] ?? null) || $meta['created'] > time()
            || $meta['expires'] > $meta['created'] + $p['ttl']
            || $meta['expires'] <= time() + min(900, max(30, intdiv($p['ttl'], 2)));
    }

    /** A 200/MISS is only a render: the normal store can still reject cookies,
     * a noncanonical response or an invalidation. Prove the new private copy
     * exists, is current and has the promised body bytes without keeping HTML. */
    private static function stored(array $p, string $path, string $device, int $not_before): bool {
        $live = self::policy();
        if (!$live || $live['generation'] !== $p['generation'] || ($live['paths'][$path] ?? null) !== ($p['paths'][$path] ?? null)) { return false; }
        $key = self::key($live, $path, $device); $file = ZP_HTML_Cache_20261009::dir() . '/zpc-' . $key . '.php';
        clearstatcache(true, $file);
        if (is_link($file) || !is_file($file) || filesize($file) > 4194304 + 8192) { return false; }
        $handle = @fopen($file, 'rb'); if (!$handle) { return false; }
        try {
            $prefix = fgets($handle, 32); $line = fgets($handle, 8193);
            if ($prefix !== ZP_HTML_Cache_20261009::PREFIX || !is_string($line)) { return false; }
            $meta = json_decode($line, true); $now = time();
            if (!is_array($meta) || ($meta['kind'] ?? '') !== 'zp-public-html-v1' || ($meta['key'] ?? '') !== $key
                || ($meta['generation'] ?? '') !== $live['generation'] || !is_int($meta['created'] ?? null)
                || $meta['created'] < $not_before || $meta['created'] > $now || !is_int($meta['expires'] ?? null)
                || $meta['expires'] <= $now || $meta['expires'] > $meta['created'] + $live['ttl']
                || !is_int($meta['length'] ?? null) || $meta['length'] < 1 || $meta['length'] > 4194304
                || !is_string($meta['html_sha256'] ?? null) || !preg_match('/^[a-f0-9]{64}$/D', $meta['html_sha256'])) { return false; }
            $hash = hash_init('sha256'); $length = 0;
            while (!feof($handle)) {
                $chunk = fread($handle, 65536);
                if (!is_string($chunk) || ($chunk === '' && !feof($handle))) { return false; }
                $length += strlen($chunk); if ($length > 4194304) { return false; }
                hash_update($hash, $chunk);
            }
            return $length === $meta['length'] && hash_equals($meta['html_sha256'], hash_final($hash));
        } finally { fclose($handle); }
    }

    /** Conditional deletion cannot remove a newer worker's lock after this one expires. */
    private static function release(array $lock): void {
        global $wpdb;
        $wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->options} WHERE option_name = %s AND option_value = %s", self::LOCK, maybe_serialize($lock)));
        wp_cache_delete(self::LOCK, 'options');
    }

    private static function acquire(): ?array {
        $lock = ['token' => bin2hex(random_bytes(16)), 'until' => time() + 120];
        if (add_option(self::LOCK, $lock, '', false)) { return $lock; }
        $old = get_option(self::LOCK, null);
        if (!is_array($old) || !is_int($old['until'] ?? null) || $old['until'] >= time()) { return null; }
        self::release($old);
        return add_option(self::LOCK, $lock, '', false) ? $lock : null;
    }

    /** At most four policy-approved paths × two exact device classes, sequentially. */
    public static function run(): void {
        $initial = self::policy(); if (!$initial || !($lock = self::acquire())) { return; }
        $status = ['started' => time(), 'finished' => 0, 'requested' => 0, 'skipped' => 0, 'hit' => 0, 'miss' => 0, 'bypass' => 0, 'failed' => 0, 'paths' => $initial['warm_paths'], 'results' => []];
        try {
            $count = 0;
            foreach (array_unique($initial['warm_paths']) as $path) {
                if (!is_string($path) || !preg_match('~^/[A-Za-z0-9%._/-]*$~D', $path)) { continue; }
                $entry = $initial['paths'][$path] ?? null;
                if (($entry !== true && !(str_starts_with($path, '/en/') && is_string($entry) && str_starts_with($entry, 'en:')))
                    || (!str_starts_with($path, '/en/') && (!in_array($path, ['/', '/wiedza/'], true) || empty($initial['geo_polish_safe'])))) { continue; }
                foreach (['d', 'm'] as $device) {
                    if (++$count > 8) { return; }
                    // Publication, EN version or an emergency OFF can change during a slow request.
                    $p = self::policy();
                    if (!$p || $p['generation'] !== $initial['generation'] || $p['warm_secret'] !== $initial['warm_secret']
                        || ($p['paths'][$path] ?? null) !== $entry || !in_array($path, $p['warm_paths'], true)) { return; }
                    if (!self::due($p, $path, $device)) { $status['skipped']++; continue; }
                    $request_started = time();
                    $response = wp_remote_get('https://' . $p['host'] . $path, [
                        'timeout' => 10, 'redirection' => 0, 'sslverify' => true, 'limit_response_size' => 3145728,
                        'blocking' => true, 'cookies' => [], 'user-agent' => self::UA,
                        'headers' => ['Accept' => 'text/html', 'Accept-Language' => 'pl-PL,pl;q=0.9',
                            'Sec-CH-UA-Mobile' => $device === 'm' ? '?1' : '?0',
                            'X-ZP-Cache-Warm' => self::signature($p, $path, $device)],
                    ]);
                    $code = is_wp_error($response) ? 0 : (int) wp_remote_retrieve_response_code($response);
                    $cache = is_wp_error($response) ? '' : strtoupper((string) wp_remote_retrieve_header($response, 'x-zp-cache'));
                    $cache = preg_match('/^(HIT|MISS|BYPASS)(?:;|$)/D', $cache, $match) ? $match[1] : '';
                    $warmed = !is_wp_error($response) && $code === 200 && $cache === 'MISS'
                        && (string) wp_remote_retrieve_header($response, 'x-zp-cache-warm') === '1'
                        && self::stored($p, $path, $device, $request_started);
                    $status['requested']++;
                    if (in_array($cache, ['HIT', 'MISS', 'BYPASS'], true)) { $status[strtolower($cache)]++; }
                    if ($code !== 200 || !$warmed) { $status['failed']++; }
                    $status['results'][] = ['path' => $path, 'device' => $device, 'http' => $code, 'cache' => $cache, 'refreshed' => $warmed];
                    unset($response); // No response body or request credentials are retained.
                }
            }
        } finally {
            $status['finished'] = time();
            update_option(self::STATUS, $status, false);
            self::release($lock);
        }
    }

    /** Admin-only summary contains counts and canonical paths, never policy credentials. */
    public static function render(): void {
        if (!current_user_can('manage_options')) { return; }
        $on = self::policy() !== null; $last = get_option(self::STATUS, []); $next = wp_next_scheduled(self::EVENT);
        echo '<h2>Rozgrzewanie cache głównej i Wiedzy</h2><p>' . ($on ? 'Aktywne dla publicznych indeksów PL/EN i obu rodzajów urządzeń.' : 'Nieaktywne: cache jest wyłączony lub jego własna polityka/drop-in wymaga odświeżenia.') . '</p>';
        echo '<p>WP-Cron: ' . ((defined('DISABLE_WP_CRON') && DISABLE_WP_CRON) ? 'wyłączony w konfiguracji — zadania wymagają crona serwera.' : 'włączony; po odświeżeniu polityki uruchamiamy pojedyncze zadanie w tle.') . ' Ponowne rozgrzewanie korzysta z istniejącego zadania cache co 15 minut; wejścia HIT nie uruchamiają WordPressa. Termin ważności kopii i formularzy pozostaje bez zmian.</p>';
        echo '<p>Następne zadanie jednorazowe: ' . ($next ? esc_html(wp_date('d.m.Y H:i:s', $next)) : 'brak w kolejce') . ' · zadanie okresowe cache: ' . (wp_next_scheduled(ZP_HTML_Cache_20261009::SWEEP) ? 'zaplanowane' : 'brak') . '.</p>';
        if (!is_array($last) || empty($last['finished'])) { echo '<p>Ostatni przebieg: jeszcze brak.</p>'; return; }
        echo '<p>Ostatni przebieg: ' . esc_html(wp_date('d.m.Y H:i:s', (int) $last['finished'])) . ' · żądania: ' . (int) ($last['requested'] ?? 0) . ' · świeże kopie pominięte: ' . (int) ($last['skipped'] ?? 0) . ' · HIT: ' . (int) ($last['hit'] ?? 0) . ' · MISS: ' . (int) ($last['miss'] ?? 0) . ' · BYPASS: ' . (int) ($last['bypass'] ?? 0) . ' · nieodnowione/błędy: ' . (int) ($last['failed'] ?? 0) . '.</p>';
        echo '<p>Ścieżki ostatniego przebiegu: ' . implode(' · ', array_map(static fn($path): string => '<code>' . esc_html((string) $path) . '</code>', array_slice((array) ($last['paths'] ?? []), 0, 4))) . '.</p>';
        echo '<table class="widefat striped"><thead><tr><th>Ścieżka kanoniczna</th><th>Urządzenie</th><th>HTTP</th><th>Cache</th><th>Odnowiona kopia</th></tr></thead><tbody>';
        foreach (array_slice((array) ($last['results'] ?? []), 0, 8) as $row) {
            if (!is_array($row)) { continue; }
            echo '<tr><td><code>' . esc_html((string) ($row['path'] ?? '')) . '</code></td><td>' . (($row['device'] ?? '') === 'm' ? 'telefon' : 'komputer') . '</td><td>' . (int) ($row['http'] ?? 0) . '</td><td>' . esc_html((string) ($row['cache'] ?? '')) . '</td><td>' . (!empty($row['refreshed']) ? 'tak' : 'nie') . '</td></tr>';
        }
        echo '</tbody></table>';
    }
}

add_action('zp_html_cache_policy_changed_3280', [ZP_Cache_Warmer_3280::class, 'queue']);
add_action('zp_html_cache_disabled_3280', [ZP_Cache_Warmer_3280::class, 'cancel']);
add_action(ZP_Cache_Warmer_3280::EVENT, [ZP_Cache_Warmer_3280::class, 'run']);
add_action(ZP_HTML_Cache_20261009::SWEEP, [ZP_Cache_Warmer_3280::class, 'run'], 20);
if (function_exists('register_deactivation_hook')) {
    register_deactivation_hook(dirname(__DIR__) . '/zaprojektowani-suite.php', [ZP_Cache_Warmer_3280::class, 'cancel']);
}
