<?php
/** Explicitly managed public HTML cache. Loading this module never enables caching or edits wp-config. */
if (!defined('ABSPATH')) { exit; }

final class ZP_HTML_Cache_20261009 {
    public const PAGE = 'zp-suite-html-cache';
    public const BACKUP = 'zp_html_cache_20261009_backup';
    public const STATE = 'zp_html_cache_20261009_state';
    public const PREFIX = "<?php exit; ?>\n";
    public const SWEEP = 'zp_html_cache_sweep_20261009';
    public const LOG = 'zp_html_cache_20261009_log';
    private static $purge_queued = false;
    private static $refresh_queued = false;
    /** 22.10.2026: what asked for this request's purge, for the log on the panel page. */
    private static $purge_cause = '';
    /** 22.10.2026: attachments that got their first file in this request, that is new uploads (meta_changed()). */
    private static $new_uploads = [];

    public static function allowed(): bool { return current_user_can('manage_options') && current_user_can('activate_plugins'); }
    public static function dir(): string { return WP_CONTENT_DIR . '/cache/zp-suite-html'; }
    public static function dropin(): string { return WP_CONTENT_DIR . '/advanced-cache.php'; }
    public static function template(): string { return __DIR__ . '/cache-dropin-20261009.php'; }
    public static function config(): string {
        foreach ([ABSPATH . 'wp-config.php', dirname(rtrim(ABSPATH, '/')) . '/wp-config.php'] as $file) { if (is_file($file)) { return $file; } }
        throw new RuntimeException('Nie znaleziono wp-config.php.');
    }
    private static function read(string $file, bool $optional = false): string {
        if (is_link($file)) { throw new RuntimeException('Plik jest dowiązaniem; operacja zablokowana.'); }
        if (!is_file($file)) { if ($optional && !file_exists($file)) { return ''; } throw new RuntimeException('Brak wymaganego zwykłego pliku.'); }
        if (filesize($file) > 1048576 || !is_readable($file)) { throw new RuntimeException('Plik jest za duży lub nieczytelny.'); }
        $raw = file_get_contents($file); if (!is_string($raw)) { throw new RuntimeException('Nie odczytano pliku.'); } return $raw;
    }
    public static function redact(string $raw): string {
        $lines = preg_split('/\r\n|\r|\n/', $raw);
        foreach ($lines as &$line) {
            if (preg_match('/password|passwd|secret|token|(?:api|access|private)[_-]?key|DB_PASSWORD|AUTH_KEY|AUTH_SALT|NONCE_KEY|NONCE_SALT|LOGGED_IN_KEY|LOGGED_IN_SALT/i', $line)) { $line = '[ukryto linię z możliwym kluczem lub poświadczeniem]'; }
            $line = preg_replace('~(https?://)[^\s/:@]+:[^\s/@]+@~i', '$1[ukryte]@', $line);
        }
        unset($line); return implode("\n", $lines);
    }
    /** Token positions ensure comments/strings that mention WP_CACHE are never modified. */
    public static function patch_config(string $raw): string {
        $tokens = token_get_all($raw); $offset = 0; $changes = []; $declared = 0; $actual_if_offsets = [];
        $skip = static function ($token): bool { return is_array($token) && in_array($token[0], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true); };
        foreach ($tokens as $i => $token) {
            $text = is_array($token) ? $token[1] : $token;
            if (is_array($token) && $token[0] === T_IF) { $actual_if_offsets[] = $offset; }
            if (is_array($token) && $token[0] === T_STRING && strtolower($text) === 'define') {
                $j = $i + 1; while (isset($tokens[$j]) && $skip($tokens[$j])) { $j++; }
                if (($tokens[$j] ?? null) === '(') {
                    $j++; while (isset($tokens[$j]) && $skip($tokens[$j])) { $j++; }
                    $name = $tokens[$j] ?? null;
                    if (is_array($name) && $name[0] === T_CONSTANT_ENCAPSED_STRING && in_array($name[1], ["'WP_CACHE'", '"WP_CACHE"'], true)) {
                        $declared++; $j++; while (isset($tokens[$j]) && $skip($tokens[$j])) { $j++; }
                        if (($tokens[$j] ?? null) !== ',') { throw new RuntimeException('Niestandardowa deklaracja WP_CACHE.'); }
                        $j++; while (isset($tokens[$j]) && $skip($tokens[$j])) { $j++; } $value = $tokens[$j] ?? null;
                        if (!is_array($value) || $value[0] !== T_STRING || strtolower($value[1]) !== 'false') { throw new RuntimeException('WP_CACHE musi być literalnie false przed pierwszą instalacją.'); }
                        $value_offset = $offset; for ($k = $i; $k < $j; $k++) { $value_offset += strlen(is_array($tokens[$k]) ? $tokens[$k][1] : $tokens[$k]); }
                        $after = $j + 1; while (isset($tokens[$after]) && $skip($tokens[$after])) { $after++; }
                        if (($tokens[$after] ?? null) !== ')') { throw new RuntimeException('Niestandardowa wartość WP_CACHE.'); }
                        $changes[] = [$value_offset, strlen($value[1])];
                    }
                }
            }
            if (is_array($token) && $token[0] === T_CONST && preg_match('/\bWP_CACHE\b/', substr($raw, $offset, 200))) { throw new RuntimeException('Deklaracja const WP_CACHE wymaga osobnej oceny.'); }
            $offset += strlen($text);
        }
        if ($declared > 1) { throw new RuntimeException('Wiele deklaracji WP_CACHE; nie zmieniamy konfiguracji.'); }
        if ($declared === 1) { return substr_replace($raw, 'true', $changes[0][0], $changes[0][1]); }
        preg_match_all('/^([\t ]*if\s*\(\s*!\s*defined\s*\(\s*["\']ABSPATH["\']\s*\)\s*\)\s*\{)/m', $raw, $matches, PREG_OFFSET_CAPTURE);
        foreach ($matches[0] as $match) { $at = $match[1]; if (in_array($at + strspn($match[0], " \t"), $actual_if_offsets, true)) { return substr($raw, 0, $at) . "define( 'WP_CACHE', true ); // ZP Suite HTML cache 20261009\n" . substr($raw, $at); } }
        throw new RuntimeException('Brak standardowej granicy ABSPATH; konfiguracja nie będzie zmieniana.');
    }
    /** With $keep (the current policy), the generation stays: copies of pages whose entry did not change remain valid. */
    public static function policy(bool $enabled = true, ?array $keep = null): array {
        $home = wp_parse_url(home_url('/')); if (($home['scheme'] ?? '') !== 'https' || empty($home['host']) || isset($home['port'])) { throw new RuntimeException('Cache wymaga kanonicznego HTTPS bez niestandardowego portu.'); }
        $paths = []; $posts = get_posts(['post_type' => ['post', 'page'], 'post_status' => 'publish', 'numberposts' => -1, 'suppress_filters' => true]);
        foreach ($posts as $post) {
            if (!empty($post->post_password)) { continue; }
            if (preg_match('/\[[a-z0-9_:-]*(?:brief|oferta|offer|proposal|private|wycen|quote|zamow|order|dziekuj|thank)[a-z0-9_:-]*(?:\s|\])/i', (string) $post->post_content)) { continue; }
            if (in_array('noindex', (array) get_post_meta($post->ID, 'rank_math_robots', true), true)) { continue; }
            $url = wp_parse_url(get_permalink($post->ID)); $path = $url['path'] ?? '/';
            if (($url['scheme'] ?? '') !== 'https' || ($url['host'] ?? '') !== $home['host'] || isset($url['query']) || !preg_match('~^/[A-Za-z0-9%._/-]*$~D', $path) || preg_match('~^/(?:en(?:/|$)|wp-|oferta(?:/|$)|oferty(?:/|$)|offer(?:/|$)|brief(?:/|$)|studio-wyceny(?:/|$)|dziekujemy|thank-you|konto(?:/|$)|koszyk(?:/|$)|zamowienie(?:/|$))~i', $path)) { continue; }
            $paths[$path] = true;
        }
        if (!$paths) { throw new RuntimeException('Brak publicznych kanonicznych ścieżek do cache.'); }
        // 3.1.1: other modules add their own public pages with a version (the EN translator: "/en/…/" => "en:<version>").
        // Only a drop-in that knows that kind of entry serves them (3.1.0's needs true), and the version is part of the key.
        if (function_exists('apply_filters')) {
            foreach ((array) apply_filters('zp_html_cache_policy_paths', [], $paths) as $path => $entry) {
                if (is_string($path) && !isset($paths[$path]) && preg_match('~^/[A-Za-z0-9%._/-]*$~D', $path) && is_string($entry) && preg_match('/^[a-z]{2}:[A-Za-z0-9._-]{1,40}$/D', $entry)) { $paths[$path] = $entry; }
            }
        }
        ksort($paths);
        $next = 0;
        foreach (get_posts(['post_type' => ['post', 'page'], 'post_status' => 'future', 'numberposts' => -1, 'suppress_filters' => true]) as $post) {
            if ($post->post_date_gmt !== '0000-00-00 00:00:00') { $date = new DateTimeImmutable($post->post_date_gmt, new DateTimeZone('UTC')); }
            else { $date = new DateTimeImmutable($post->post_date, wp_timezone()); }
            $stamp = $date->getTimestamp(); if (!$next || $stamp < $next) { $next = $stamp; }
        }
        $geo = get_option('zpl_geo', []); $geo = is_array($geo) ? $geo : [];
        $geo_enabled = !array_key_exists('enabled', $geo) || !empty($geo['enabled']);
        $polish_stays = !array_key_exists('polish_stays', $geo) || !empty($geo['polish_stays']);
        $ttl = 1800;
        if (function_exists('apply_filters')) { foreach (['zp_suite_contact', 'zp_wiedza_nonce', 'zp_studio_submit', 'zp_studio_domain_check', 'zp_suite_live_search'] as $action) { $ttl = min($ttl, (int) floor((float) apply_filters('nonce_life', 86400, $action) / 4)); } }
        if ($ttl < 60) { throw new RuntimeException('Niestandardowy czas nonce jest zbyt krótki dla cache formularzy.'); }
        $keep = $keep && is_string($keep['generation'] ?? null) && preg_match('/^[a-f0-9]{32}$/D', $keep['generation']) ? $keep : null;
        // "since": when this generation began. Copies older than that are never read again, so the sweep deletes them.
        $policy = ['schema' => 1, 'enabled' => $enabled, 'ttl' => $ttl, 'host' => $home['host'], 'paths' => $paths, 'geo_polish_safe' => !$geo_enabled || $polish_stays, 'next_publication' => $next, 'generation' => $keep ? $keep['generation'] : bin2hex(random_bytes(16)), 'since' => $keep ? (int) ($keep['since'] ?? 0) : time()];
        // 3.1.1 (thread 25): the drop-in places a visitor the way Geo does, with the same bundled Polish address ranges.
        if (defined('ZPL_DIR')) { $policy['geo'] = ['on' => $geo_enabled, 'v4' => ZPL_DIR . 'data/geo/pl-v4.bin', 'v6' => ZPL_DIR . 'data/geo/pl-v6.bin']; }
        return $policy;
    }
    private static function filesystem(): void {
        foreach ([WP_CONTENT_DIR, WP_CONTENT_DIR . '/cache', self::dir()] as $dir) {
            if (is_link($dir) || (file_exists($dir) && !is_dir($dir))) { throw new RuntimeException('Nieprawidłowy katalog cache.'); }
        }
        if (!is_writable(WP_CONTENT_DIR)) { throw new RuntimeException('Katalog wp-content nie jest zapisywalny.'); }
    }
    public static function preflight(): array {
        if (!self::allowed()) { throw new RuntimeException('Brak wymaganych uprawnień administratora.'); }
        self::filesystem();
        if (function_exists('is_plugin_active') && is_plugin_active('wp-super-cache/wp-cache.php')) { throw new RuntimeException('Najpierw wyłącz WP Super Cache.'); }
        if (defined('WP_CACHE') && WP_CACHE) { throw new RuntimeException('WP_CACHE jest aktywne; najpierw sprawdź aktualny cache.'); }
        if (!empty($GLOBALS['cache_enabled']) || defined('LSCWP_V') || function_exists('w3tc_flush_all') || function_exists('rocket_clean_domain')) { throw new RuntimeException('Wykryto inny aktywny mechanizm cache.'); }
        $config = self::config(); $old_config = self::read($config); $new_config = self::patch_config($old_config); $old_dropin = self::read(self::dropin(), true); $new_dropin = self::read(self::template());
        if (strpos($new_dropin, '// ZP Suite managed HTML cache 20261009 v1.') === false) { throw new RuntimeException('Nieprawidłowy szablon własnego cache.'); }
        $policy = self::policy(); $fingerprint = [hash('sha256', $old_config), hash('sha256', $old_dropin), hash('sha256', $new_dropin), $policy['host'], $policy['paths'], $policy['next_publication'], $policy['geo_polish_safe'], $policy['ttl']];
        return ['review_hash' => hash('sha256', wp_json_encode($fingerprint)), 'config_path' => $config, 'config_sha' => $fingerprint[0], 'dropin_sha' => $fingerprint[1], 'dropin_bytes' => strlen($old_dropin), 'dropin_redacted' => self::redact($old_dropin), 'old_config' => $old_config, 'new_config' => $new_config, 'old_dropin' => $old_dropin, 'new_dropin' => $new_dropin, 'policy' => $policy];
    }
    public static function atomic(string $file, string $data, int $mode, string $expected): void {
        if (is_link($file) || (is_file($file) ? hash_file('sha256', $file) : hash('sha256', '')) !== $expected) { throw new RuntimeException('Plik zmieniono po przeglądzie; zapis zablokowany.'); }
        $tmp = dirname($file) . '/.zp-cache-' . bin2hex(random_bytes(12)) . '.php'; $handle = @fopen($tmp, 'xb'); if (!$handle) { throw new RuntimeException('Nie utworzono pliku tymczasowego.'); }
        @chmod($tmp, $mode); $written = fwrite($handle, $data); fflush($handle); if (function_exists('fsync')) { fsync($handle); } fclose($handle);
        if ($written !== strlen($data) || is_link($file) || (is_file($file) ? hash_file('sha256', $file) : hash('sha256', '')) !== $expected || !@rename($tmp, $file)) { @unlink($tmp); throw new RuntimeException('Atomowy zapis nie powiódł się; oryginał chroniony.'); }
        clearstatcache(true, $file); if (!hash_equals(hash('sha256', $data), (string) hash_file('sha256', $file))) { throw new RuntimeException('Weryfikacja zapisu pliku nie powiodła się.'); }
    }
    /**
     * 3.1.1: policy writes take a lock, so a purge's new generation and a refresh that keeps the old one never cross.
     * With $expected (the hash of the file the policy was composed from), the write happens only if nobody wrote since.
     * 3.2.0: with $keep_off, a policy file that says the cache is off stays as it is (checked under the same lock that
     * deactivate() writes under), so a purge that started before the owner switched the cache off cannot switch it back on.
     */
    private static function write_policy(array $policy, ?string $expected = null, bool $keep_off = false): bool {
        $file = self::dir() . '/policy.php'; $lock_file = self::dir() . '/.policy.lock';
        $lock = !is_link($lock_file) && is_dir(self::dir()) ? @fopen($lock_file, 'c') : false;
        if ($lock) { @chmod($lock_file, 0600); flock($lock, LOCK_EX); }
        try {
            clearstatcache(true, $file); $current = is_file($file) ? (string) hash_file('sha256', $file) : hash('sha256', '');
            if ($expected !== null && !hash_equals($expected, $current)) { throw new RuntimeException('Polityka cache zmieniła się w trakcie odświeżania.'); }
            if ($keep_off && is_file($file) && !is_link($file)) {
                $raw = (string) @file_get_contents($file);
                $on_disk = strpos($raw, self::PREFIX) === 0 ? json_decode(substr($raw, strlen(self::PREFIX)), true) : null;
                if (is_array($on_disk) && ($on_disk['enabled'] ?? null) === false) { return false; }
            }
            self::atomic($file, self::PREFIX . wp_json_encode($policy), 0600, $current);
            return true;
        } finally { if ($lock) { flock($lock, LOCK_UN); fclose($lock); } }
    }
    public static function enable(string $review_hash): void {
        $review = self::preflight(); if (!preg_match('/^[a-f0-9]{64}$/D', $review_hash) || !hash_equals($review['review_hash'], $review_hash)) { throw new RuntimeException('Przegląd zmienił się. Odśwież panel.'); }
        if (get_option(self::BACKUP, null) !== null) { throw new RuntimeException('Istnieje poprzednia kopia konfiguracji; użyj najpierw przywracania.'); }
        $backup = ['schema' => 1, 'config_basename' => basename($review['config_path']), 'config_original_base64' => base64_encode($review['old_config']), 'config_original_sha' => $review['config_sha'], 'config_installed_sha' => hash('sha256', $review['new_config']), 'config_mode' => fileperms($review['config_path']) & 0777, 'dropin_existed' => is_file(self::dropin()), 'dropin_original_base64' => base64_encode($review['old_dropin']), 'dropin_original_sha' => $review['dropin_sha'], 'dropin_installed_sha' => hash('sha256', $review['new_dropin']), 'dropin_mode' => is_file(self::dropin()) ? fileperms(self::dropin()) & 0777 : 0644, 'at_utc' => gmdate('c')];
        if (!add_option(self::BACKUP, $backup, '', false) || get_option(self::BACKUP) !== $backup) { throw new RuntimeException('Nie zapisano prywatnej kopii przed zmianami.'); }
        try {
            if (!is_dir(WP_CONTENT_DIR . '/cache') && !mkdir(WP_CONTENT_DIR . '/cache', 0755)) { throw new RuntimeException('Nie utworzono katalogu cache.'); }
            if (!is_dir(self::dir()) && !mkdir(self::dir(), 0700)) { throw new RuntimeException('Nie utworzono prywatnego katalogu.'); }
            foreach (['index.php' => "<?php exit;\n", '.htaccess' => "Require all denied\n"] as $name => $data) { $file = self::dir() . '/' . $name; self::atomic($file, $data, 0600, is_file($file) ? (string) hash_file('sha256', $file) : hash('sha256', '')); }
            self::write_policy(array_merge($review['policy'], ['enabled' => false]));
            self::atomic(self::dropin(), $review['new_dropin'], 0644, $review['dropin_sha']);
            self::atomic($review['config_path'], $review['new_config'], $backup['config_mode'], $review['config_sha']);
            self::write_policy($review['policy']);
            update_option(self::STATE, ['enabled' => true, 'paths' => count($review['policy']['paths']), 'ttl' => $review['policy']['ttl'], 'at_utc' => gmdate('c')], false);
            if ((get_option(self::STATE, [])['enabled'] ?? false) !== true) { throw new RuntimeException('Nie zapisano stanu cache.'); }
        } catch (Throwable $error) {
            try { self::restore(true); } catch (Throwable $rollback) { throw new RuntimeException('Instalacja przerwana. Prywatna kopia pozostaje; wymagana kontrola hashów w panelu.'); }
            throw $error;
        }
    }
    public static function restore(bool $partial = false): void {
        if (!self::allowed()) { throw new RuntimeException('Brak uprawnień.'); }
        $b = get_option(self::BACKUP, null); if (!is_array($b) || ($b['schema'] ?? null) !== 1) { throw new RuntimeException('Brak prywatnej kopii.'); }
        $config = self::config(); $current_config = self::read($config); $current_dropin = self::read(self::dropin(), true); $config_sha = hash('sha256', $current_config); $dropin_sha = hash('sha256', $current_dropin);
        $allowed_config = [$b['config_installed_sha'], $b['config_original_sha']]; $allowed_dropin = [$b['dropin_installed_sha'], $b['dropin_original_sha']];
        if (!in_array($config_sha, $allowed_config, true) || !in_array($dropin_sha, $allowed_dropin, true)) { throw new RuntimeException('Ręczna lub obca zmiana konfiguracji/drop-in: nie nadpisujemy jej.'); }
        $old_config = base64_decode($b['config_original_base64'], true); $old_dropin = base64_decode($b['dropin_original_base64'], true);
        if (!is_string($old_config) || !is_string($old_dropin) || hash('sha256', $old_config) !== $b['config_original_sha'] || hash('sha256', $old_dropin) !== $b['dropin_original_sha']) { throw new RuntimeException('Kopia nie przeszła weryfikacji hashów.'); }
        if (is_dir(self::dir())) { self::write_policy(self::disabled_policy()); }
        self::atomic($config, $old_config, $b['config_mode'], $config_sha);
        if ($b['dropin_existed']) { self::atomic(self::dropin(), $old_dropin, $b['dropin_mode'], $dropin_sha); }
        else { if (is_file(self::dropin()) && (hash_file('sha256', self::dropin()) !== $dropin_sha || !unlink(self::dropin()))) { throw new RuntimeException('Nie usunięto wyłącznie własnego drop-in.'); } }
        update_option(self::STATE, ['enabled' => false, 'restored' => true, 'at_utc' => gmdate('c')], false); delete_option(self::BACKUP);
    }
    public static function disable(): void {
        if (!self::allowed()) { throw new RuntimeException('Brak uprawnień.'); }
        self::deactivate();
    }
    /** WordPress lifecycle hook also works during CLI/recovery deactivation without a logged-in user. */
    public static function deactivate(): void {
        if (is_dir(self::dir())) { self::write_policy(self::disabled_policy()); }
        update_option(self::STATE, ['enabled' => false, 'disabled' => true, 'at_utc' => gmdate('c')], false);
        // Keep original files' private backup; no config or drop-in is touched by emergency OFF.
    }
    /**
     * ZP Suite 3.0.1: put the device-aware drop-in in place of the one this module wrote and keep the private backup's
     * hash in step, so "Przywróć" and "Odśwież" keep working. With $resume, pages are served from the cache again.
     * Only a drop-in whose hash proves it is this module's own is replaced; wp-config.php is never touched.
     */
    public static function upgrade_dropin(bool $resume): string {
        $backup = get_option(self::BACKUP, null);
        if (!is_array($backup) || ($backup['schema'] ?? null) !== 1) { return 'not-installed'; }
        if (!defined('WP_CACHE') || !WP_CACHE) { return 'wp-cache-off'; }
        $file = self::dropin();
        if (is_link($file) || !is_file($file)) { return 'dropin-missing'; }
        $current = (string) hash_file('sha256', $file);
        $template = self::read(self::template());
        $new = hash('sha256', $template);
        if ($current !== $new) {
            if ($current !== ($backup['dropin_installed_sha'] ?? '')) { return 'dropin-foreign'; }
            self::atomic($file, $template, 0644, $current);
        }
        if (($backup['dropin_installed_sha'] ?? '') !== $new) { $backup['dropin_installed_sha'] = $new; update_option(self::BACKUP, $backup, false); }
        $state = get_option(self::STATE, []);
        if (!$resume && empty($state['enabled'])) { return 'dropin-updated'; }
        self::filesystem();
        $policy = self::policy();
        // Without $resume the cache was on when this started; if the owner switched it off meanwhile, it stays off.
        if (!self::write_policy($policy, null, !$resume)) { return 'dropin-updated'; }
        update_option(self::STATE, ['enabled' => true, 'paths' => count($policy['paths']), 'ttl' => $policy['ttl'], 'at_utc' => gmdate('c')], false);
        delete_option('zp_html_cache_20261009_error');
        // Copies of the old generation are never read again; the sweep deletes them (3.1.1).
        self::schedule_sweep();
        return 'on';
    }
    /** Short state for "Stan strony": on, off, not installed, or the reason it cannot serve. */
    public static function status(): array {
        $state = get_option(self::STATE, []);
        $installed = is_array(get_option(self::BACKUP, null));
        $dropin = self::dropin();
        $sha = is_file($dropin) && !is_link($dropin) ? (string) hash_file('sha256', $dropin) : '';
        $template = is_file(self::template()) ? (string) hash_file('sha256', self::template()) : '';
        return ['installed' => $installed, 'enabled' => !empty($state['enabled']), 'paths' => (int) ($state['paths'] ?? 0), 'ttl' => (int) ($state['ttl'] ?? 0), 'wp_cache' => defined('WP_CACHE') && WP_CACHE, 'dropin_current' => $sha !== '' && $sha === $template, 'dropin_present' => $sha !== '', 'error' => (string) get_option('zp_html_cache_20261009_error', '')];
    }
    private static function disabled_policy(): array { return ['schema' => 1, 'enabled' => false, 'ttl' => 1800, 'host' => '', 'paths' => [], 'geo_polish_safe' => false, 'next_publication' => 0, 'generation' => bin2hex(random_bytes(16))]; }
    public static function purge(): void {
        // 3.1.1: read the switch fresh. A long request (a translator step of ~20 s) keeps the value it read first, and the
        // owner may have switched the cache off meanwhile; acting on the old value would switch it back on.
        if (function_exists('wp_cache_delete')) { wp_cache_delete(self::STATE, 'options'); }
        $state = get_option(self::STATE, []); if (empty($state['enabled'])) { return; }
        $backup = get_option(self::BACKUP, []);
        if (is_link(self::dropin()) || !is_file(self::dropin()) || hash_file('sha256', self::dropin()) !== ($backup['dropin_installed_sha'] ?? '')) { return; }
        try {
            // 3.0.1: an older drop-in of this module is replaced by the device-aware one (that step also refreshes).
            if (hash_file('sha256', self::dropin()) !== hash_file('sha256', self::template())) { self::upgrade_dropin(false); return; }
            $policy = self::policy();
            // A new generation makes every copy unreachable at once. 3.1.1: copies are no longer deleted here (that cost every
            // save up to 1000 file deletions); the sweep deletes them. A write by older code mid-update can fail the hash check once.
            try { $written = self::write_policy($policy, null, true); } catch (RuntimeException $race) { usleep(50000); clearstatcache(); $written = self::write_policy($policy, null, true); }
            if (!$written) { return; }
            self::log_purge();
            self::schedule_sweep();
            // 3.0.1: a later successful refresh clears an old failure note (it used to stay on the page for good).
            if (get_option('zp_html_cache_20261009_error', '') !== '') { delete_option('zp_html_cache_20261009_error'); }
        } catch (Throwable $error) {
            // Existing TTL/deadline guard still limits stale content; failure visible to admins.
            update_option('zp_html_cache_20261009_error', 'Nie odświeżono polityki cache; sprawdź uprawnienia katalogu.', false);
        }
    }
    public static function queue_purge($cause = ''): void {
        if (self::$purge_queued || empty(get_option(self::STATE, [])['enabled'])) { return; }
        $cause = is_string($cause) ? $cause : ''; // a hook callback: never trust what a hook passes
        self::$purge_queued = true; add_action('shutdown', [self::class, 'purge'], PHP_INT_MAX);
        if ($cause === '') {
            // 22.10.2026: the hook, and the first file outside WordPress that asked (directly or with do_action).
            $cause = (string) current_action();
            foreach (debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 8) as $frame) {
                $file = (string) ($frame['file'] ?? '');
                if ($file !== '' && $file !== __FILE__ && strpos($file, 'wp-includes') === false) { $cause .= ' (' . basename($file) . ')'; break; }
            }
        }
        self::$purge_cause = $cause;
    }
    /**
     * 22.10.2026: whether a change to this post can show on a public page. Not for a revision (every autosave of the block
     * editor and of Elementor is one), the internal kinds (oEmbed cache, Customizer drafts, privacy requests) or a post that
     * is neither published nor scheduled (auto-draft, draft, pending, private). Attachments and unknown posts count.
     */
    private static function post_is_public($post, string $status = ''): bool {
        if (!$post instanceof WP_Post) { return true; }
        if (in_array($post->post_type, ['revision', 'oembed_cache', 'customize_changeset', 'user_request'], true)) { return false; }
        if ($post->post_type === 'attachment') { return true; }
        return in_array($status !== '' ? $status : $post->post_status, ['publish', 'future'], true);
    }
    /**
     * 22.10.2026: save_post, transition_post_status and deleted_post empty the cache only for a post that is or was public,
     * so publishing, unpublishing, scheduling and every save of a published post still do. Before, each autosave and each
     * draft save did as well, and an editor left open with changes emptied the whole cache about once a minute.
     */
    public static function post_changed(...$args): void {
        $hook = (string) current_action();
        if ($hook === 'transition_post_status') {
            $post = $args[2] ?? null;
            if (!self::post_is_public($post, (string) ($args[0] ?? '')) && !self::post_is_public($post, (string) ($args[1] ?? ''))) { return; }
            $what = $post instanceof WP_Post ? ' #' . $post->ID . ' ' . $post->post_type . ' ' . $args[1] . '→' . $args[0] : '';
        } else {
            $post = ($args[1] ?? null) instanceof WP_Post ? $args[1] : get_post((int) ($args[0] ?? 0));
            if (!self::post_is_public($post)) { return; }
            $what = $post instanceof WP_Post ? ' #' . $post->ID . ' ' . $post->post_type . ' ' . $post->post_status : '';
        }
        self::queue_purge($hook . $what);
    }
    /** 22.10.2026: the last 60 purges with their cause and where they ran (its option never purges: zp_html_cache_ prefix). */
    private static function log_purge(): void {
        $log = get_option(self::LOG, []); $log = is_array($log) ? $log : [];
        $where = (function_exists('wp_doing_cron') && wp_doing_cron()) ? 'cron' : ((defined('REST_REQUEST') && REST_REQUEST) ? 'REST' : ((function_exists('wp_doing_ajax') && wp_doing_ajax()) ? 'AJAX' : (is_admin() ? 'panel' : 'strona')));
        array_unshift($log, ['t' => time(), 'c' => substr(self::$purge_cause !== '' ? self::$purge_cause : (string) current_action(), 0, 160), 'w' => $where]);
        update_option(self::LOG, array_slice($log, 0, 60), false);
    }
    /** 22.10.2026: how often and why the copies were emptied, newest first, in the site's time. */
    private static function purge_log_table(): void {
        $log = get_option(self::LOG, []); $log = is_array($log) ? array_values(array_filter($log, 'is_array')) : [];
        if (!$log) { echo '<p>Odświeżenia cache: jeszcze żadnego od tej aktualizacji.</p>'; return; }
        $day = count(array_filter($log, static fn($row) => (int) ($row['t'] ?? 0) > time() - DAY_IN_SECONDS));
        echo '<h2>Ostatnie odświeżenia cache</h2><p>W ostatnich 24 godzinach: ' . (int) $day . ($day >= 60 ? ' albo więcej' : '') . '. Każde unieważnia kopie wszystkich stron.</p><table class="widefat"><tr><th>Czas</th><th>Przyczyna</th><th>Skąd</th></tr>';
        foreach (array_slice($log, 0, 30) as $row) { echo '<tr><td>' . esc_html(wp_date('d.m H:i:s', (int) ($row['t'] ?? 0))) . '</td><td><code>' . esc_html((string) ($row['c'] ?? '')) . '</code></td><td>' . esc_html((string) ($row['w'] ?? '')) . '</td></tr>'; }
        echo '</table>';
    }
    /**
     * 3.1.1: for a change to some pages only (an EN translation's new version): the policy is composed again with the same
     * generation, so only the pages whose entry changed lose their copies. Once per request, at its end; a purge wins.
     */
    public static function queue_refresh(): void {
        if (self::$refresh_queued || self::$purge_queued || empty(get_option(self::STATE, [])['enabled'])) { return; }
        self::$refresh_queued = true; add_action('shutdown', [self::class, 'refresh'], PHP_INT_MAX - 1);
    }
    public static function refresh(): void {
        if (self::$purge_queued) { return; }
        if (function_exists('wp_cache_delete')) { wp_cache_delete(self::STATE, 'options'); }
        $state = get_option(self::STATE, []); if (empty($state['enabled'])) { return; }
        $backup = get_option(self::BACKUP, []);
        if (is_link(self::dropin()) || !is_file(self::dropin()) || hash_file('sha256', self::dropin()) !== ($backup['dropin_installed_sha'] ?? '') || hash_file('sha256', self::dropin()) !== hash_file('sha256', self::template())) { self::purge(); return; }
        try {
            $file = self::dir() . '/policy.php';
            for ($try = 0; $try < 3; $try++) {
                clearstatcache(true, $file); $raw = is_file($file) && !is_link($file) ? (string) @file_get_contents($file) : '';
                $current = strpos($raw, self::PREFIX) === 0 ? json_decode(substr($raw, strlen(self::PREFIX)), true) : null;
                // A cache switched off on disk stays off: only the switch in the panel turns it on again (3.1.1).
                if (is_array($current) && ($current['enabled'] ?? null) === false) { return; }
                // No valid policy to keep: a full refresh with a new generation.
                if (!is_array($current) || ($current['enabled'] ?? null) !== true || !is_string($current['generation'] ?? null) || !preg_match('/^[a-f0-9]{32}$/D', $current['generation'])) { self::purge(); return; }
                $policy = self::policy(true, $current);
                if (wp_json_encode($policy) === substr($raw, strlen(self::PREFIX))) { return; }
                // Compare-and-swap: a purge that wrote a new generation meanwhile is kept, and the policy is composed again from it.
                try { self::write_policy($policy, hash('sha256', $raw)); return; } catch (RuntimeException $race) { usleep(50000); }
            }
            throw new RuntimeException('race');
        } catch (Throwable $error) {
            update_option('zp_html_cache_20261009_error', 'Nie odświeżono polityki cache; sprawdź uprawnienia katalogu.', false);
        }
    }
    public static function schedule_sweep(): void {
        if (function_exists('wp_next_scheduled') && !wp_next_scheduled(self::SWEEP)) { wp_schedule_event(time() + 900, 'zp_html_cache_15m', self::SWEEP); }
    }
    /**
     * 3.1.1: deletes copies that are never served again, every 15 minutes: those made before the current generation began and
     * those past the longest life (1800 s, plus 5 minutes), whatever their generation or version. Temporary files of cut-off
     * writes go after 10 minutes. Up to 5000 files per run; nothing outside this folder, and never LiteSpeed or the CDN.
     */
    public static function sweep(): int {
        $dir = self::dir(); if (is_link($dir) || !is_dir($dir)) { return 0; }
        $since = 0; $raw = is_file($dir . '/policy.php') && !is_link($dir . '/policy.php') ? (string) @file_get_contents($dir . '/policy.php') : '';
        $policy = strpos($raw, self::PREFIX) === 0 ? json_decode(substr($raw, strlen(self::PREFIX)), true) : null;
        if (is_array($policy) && is_int($policy['since'] ?? null)) { $since = $policy['since']; }
        $now = time(); $deleted = 0;
        foreach (array_merge(glob($dir . '/zpc-*.php') ?: [], glob($dir . '/.zpc-*.php') ?: []) as $file) {
            if ($deleted >= 5000) { break; }
            if (is_link($file) || !preg_match('~/(?:zpc-[a-f0-9]{64}|\.zpc-[a-f0-9]{24})\.php$~D', $file)) { continue; }
            $mtime = (int) @filemtime($file); if ($mtime <= 0) { continue; }
            $old = basename($file)[0] === '.' ? $mtime < $now - 600 : ($mtime < $now - 2100 || ($since > 0 && $mtime < $since - 2));
            if ($old && @unlink($file)) { $deleted++; }
        }
        return $deleted;
    }
    /**
     * 3.2.60: Suite's own package is about to replace its folder (upgrader_source_selection runs before WordPress deletes
     * the old files). swap.php tells the drop-in to answer pages no copy covers with 503 and Retry-After until the install
     * ends, instead of pages built without Suite (404 on English addresses). The filter never changes or stops the install.
     */
    public static function swap_begin($source, $remote_source = null, $upgrader = null, $hook_extra = null) {
        try {
            $dir = self::dir(); $folder = is_string($source) ? rtrim($source, '/\\') : '';
            if ($folder !== '' && basename($folder) === 'zaprojektowani-suite' && is_file($folder . '/zaprojektowani-suite.php') && !is_link($dir) && is_dir($dir) && !is_link($dir . '/swap.php')) {
                $now = time(); $data = self::PREFIX . wp_json_encode(['since' => $now, 'until' => $now + 180]);
                // A temporary name the sweep already knows, so a leftover goes after 10 minutes.
                $temp = $dir . '/.zpc-' . bin2hex(random_bytes(12)) . '.php';
                if (@file_put_contents($temp, $data) !== strlen($data) || !@rename($temp, $dir . '/swap.php')) { @unlink($temp); }
            }
        } catch (Throwable $error) {}
        return $source;
    }
    /** 3.2.60: the install has ended, installed or not (upgrader_install_package_result); pages are built normally again. */
    public static function swap_end($result, $hook_extra = null) {
        $file = self::dir() . '/swap.php';
        if (!is_link($file) && is_file($file)) { @unlink($file); }
        return $result;
    }
    public static function option_changed(string $option): void {
        // Counters, admin progress/logs and lookup memoization do not change rendered content.
        if (in_array($option, ['zp_ai_stats', 'zpte_log', 'zpte_usage', 'zpte_progress', 'zpte_last_plan', 'zp_speed_img_sizes', 'zp_speed_live_paths'], true)) { return; }
        // 3.0.1: panel data (404 log, redirect counters, cron heartbeat), visit counters and lookup memos. Ordinary visits or
        // cron write these every few minutes, and each write used to empty the whole cache. 3.1.0: quote requests from Studio
        // wyceny and the llms.txt build time are on no page; the weekly style-file test counts only when it flips (below).
        if (strpos($option, 'zp_suite3_') === 0 || in_array($option, ['zpl_geo_stats', 'zpl_missing', 'zpte_alert', 'zp_speed_att_ids', 'zp_suite_version_done', 'zp_suite_leads', 'zp_ai_feed_seen', 'zp_ai_changed', 'zp_ai_access_test', 'zp_ai_indexnow_last', 'zp_ai_indexnow_retry', 'zp_ai_indexnow_queue', 'zp_websub_last', 'zp_feed_guard_at', 'zp_feed_status', 'zp_feed_log', 'zp_ai_log', 'zp_studio_wyceny_orders', 'zp_ai_llms_built', 'zp_speed_files_ok'], true)) { return; }
        // 3.1.1: added and deleted options count too, so the locks that cron jobs add and delete on every run are left out.
        if (preg_match('/_lock$/D', $option) || $option === 'zp_studio_wyceny_unread_orders') { return; }
        if (apply_filters('zp_html_cache_ignore_option', false, $option)) { return; }
        if (preg_match('/^(?:zp_(?!html_cache_)|zpl_|zpte_|rank-math-|theme_mods_|widget_|sidebars_widgets|page_on_front|show_on_front|blogname|blogdescription|permalink_structure)/', $option)) { self::queue_purge('opcja ' . $option); }
    }
    /** Copies made while the style files were served link to them, and copies made before carry the styles inline. */
    public static function speed_files_changed($old, $new): void {
        if (empty($old['ok']) !== empty($new['ok'])) { self::queue_purge('opcja zp_speed_files_ok'); }
    }
    public static function meta_changed($meta_id, $post_id, $key): void {
        if (in_array($key, ['_elementor_css', '_elementor_element_cache', '_elementor_page_assets', '_edit_lock', '_edit_last', '_zp_post_reads', '_zp_post_views'], true)) { return; }
        // 22.10.2026: Elementor's widget usage count (read only by its Element Manager) and Rank Math's note of the pictures in a
        // post's content (written while the page is being shown, from that same content) are on no page either.
        if (in_array($key, ['_elementor_controls_usage', 'rank_math_og_content_image'], true)) { return; }
        if ($key === 'rank_math_seo_score' && !self::seo_score_on_pages()) { return; }
        // 22.10.2026: an attachment that gets its first file is a new upload and on no page yet, so nothing of it written in the
        // same request (sizes, alt text) shows anywhere; the page that shows it purges when it is saved. A new file for an
        // existing attachment (a picture edited or replaced) is an update and still purges.
        $post_id = (int) $post_id;
        if ($key === '_wp_attached_file' && current_action() === 'added_post_meta' && get_post_type($post_id) === 'attachment') { self::$new_uploads[$post_id] = true; }
        if (isset(self::$new_uploads[$post_id])) { return; }
        // 3.0.1: other modules (the EN translator, for one) can name their own keys that never change Polish pages.
        if (apply_filters('zp_html_cache_ignore_meta', false, (string) $key, $post_id)) { return; }
        // 22.10.2026: data of a revision (every autosave) or of a draft is on no public page (post_is_public()).
        if (!self::post_is_public(get_post($post_id))) { return; }
        self::queue_purge('meta ' . $key . ' #' . $post_id);
    }
    /** 22.10.2026: Rank Math shows its SEO score on pages only with "Frontend SEO Score" switched on (off by default). */
    private static function seo_score_on_pages(): bool {
        $general = get_option('rank-math-options-general', []);
        return is_array($general) && in_array($general['frontend_seo_score'] ?? 'off', ['on', true, 1, '1'], true);
    }
    public static function handle(): void {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST' || !self::allowed() || !wp_verify_nonce(is_string($_POST['_wpnonce'] ?? null) ? wp_unslash($_POST['_wpnonce']) : '', 'zp_html_cache_20261009')) { wp_die('Brak uprawnień lub błędny nonce.', '', ['response' => 403]); }
        try {
            $command = $_POST['command'] ?? '';
            if ($command === 'enable') { self::enable(is_string($_POST['review_hash'] ?? null) ? wp_unslash($_POST['review_hash']) : ''); }
            elseif ($command === 'restore') { self::restore(); }
            elseif ($command === 'purge') { self::$purge_cause = 'przycisk „Odśwież własny cache”'; self::purge(); }
            elseif ($command === 'disable') { self::disable(); }
            elseif ($command === 'resume') { $result = self::upgrade_dropin(true); if ($result !== 'on') { throw new RuntimeException('Nie włączono ponownie: ' . $result . '.'); } }
            else { throw new RuntimeException('Niedozwolone działanie.'); }
        } catch (Throwable $error) { set_transient('zp_html_cache_notice_' . get_current_user_id(), $error->getMessage(), 600); }
        wp_safe_redirect(admin_url('admin.php?page=' . self::PAGE)); exit;
    }
    public static function page(): void {
        if (!self::allowed()) { wp_die('Brak uprawnień administratora.'); }
        echo '<div class="wrap"><h1>ZP Suite — Cache HTML</h1><p>Cache własnego kodu HTML: 1800 sekund, publiczne kanoniczne ścieżki PL, anonimowy GET bez parametrów, polski Accept-Language albo robot (Google, Bing, testy szybkości), osobna kopia dla telefonu i komputera. Cookies zgody i statystyk nie wyłączają cache, a kopie powstają tylko z wizyt bez cookies. Panel, REST/AJAX, formularze POST, podglądy, EN, Studio Wyceny i strony prywatne są pomijane. Wygląd i animacje są zachowane. Przy terminie publikacji omijamy HIT, aby WordPress uruchomił cron.</p>';
        $notice = get_transient('zp_html_cache_notice_' . get_current_user_id()); if ($notice) { echo '<div class="notice notice-error"><p>' . esc_html($notice) . '</p></div>'; delete_transient('zp_html_cache_notice_' . get_current_user_id()); }
        $state = get_option(self::STATE, []); $error = get_option('zp_html_cache_20261009_error', ''); if ($error) { echo '<div class="notice notice-error"><p>' . esc_html($error) . '</p></div>'; }
        $flags = ['WP_CACHE' => defined('WP_CACHE') && WP_CACHE, 'OPcache enable' => filter_var(ini_get('opcache.enable'), FILTER_VALIDATE_BOOLEAN), 'OPcache timestamps' => filter_var(ini_get('opcache.validate_timestamps'), FILTER_VALIDATE_BOOLEAN), 'OPcache restrict_api set' => (string) ini_get('opcache.restrict_api') !== '', 'OPcache status readable' => function_exists('opcache_get_status') && @opcache_get_status(false) !== false];
        echo '<table class="widefat"><tr><th>Bezpieczny raport</th><th>Wartość</th></tr>'; foreach ($flags as $key => $value) { echo '<tr><td>' . esc_html($key) . '</td><td>' . ($value ? 'tak' : 'nie') . '</td></tr>'; } echo '</table>';
        try {
            $dropin = self::read(self::dropin(), true); echo '<p>advanced-cache.php: ' . strlen($dropin) . ' B; SHA-256 <code>' . esc_html(hash('sha256', $dropin)) . '</code></p><details><summary>Treść drop-in z ukrytymi liniami poświadczeń</summary><pre style="white-space:pre-wrap;max-height:500px;overflow:auto">' . esc_html(self::redact($dropin)) . '</pre></details>';
            $backup = get_option(self::BACKUP, null);
            if (empty($state['enabled']) && is_array($backup) && hash('sha256', $dropin) === ($backup['dropin_installed_sha'] ?? '') && defined('WP_CACHE') && WP_CACHE) {
                // 3.0.1: switched off with the button below (or by 3.0.0), files still ours: switch back on without reinstalling.
                echo '<p><strong>Cache wyłączony.</strong> Plik advanced-cache.php i wp-config.php są nadal nasze, więc wystarczy włączyć go ponownie.</p>'; self::form('resume', 'Włącz ponownie cache HTML');
            } elseif (empty($state['enabled'])) {
                $review = self::preflight(); echo '<p>Gotowe: ' . count($review['policy']['paths']) . ' ścieżek. Zmiana wp-config.php obejmuje wyłącznie WP_CACHE=false → true albo dodanie jednej deklaracji. Treści wp-config.php nie wyświetlamy. Oba oryginalne pliki trafiają przed zapisem do prywatnej opcji bazy (autoload=false). Konfiguracja SHA <code>' . esc_html($review['config_sha']) . '</code>.</p>';
                self::form('enable', 'Włącz własny cache HTML z prywatną kopią i kontrolą hashów', $review['review_hash']);
            } else { echo '<p><strong>Cache włączony.</strong> Ścieżki: ' . (int) ($state['paths'] ?? 0) . '; TTL 1800 s. Dowód odpowiedzi: X-ZP-Cache HIT/MISS/BYPASS.</p>'; self::form('purge', 'Odśwież własny cache'); self::purge_log_table(); }
        } catch (Throwable $problem) { echo '<div class="notice notice-error"><p>' . esc_html($problem->getMessage()) . '</p></div>'; }
        if (get_option(self::BACKUP, null) !== null) { self::form('restore', 'Przywróć oryginalne pliki tylko przy zgodności naszych hashów'); }
        self::form('disable', 'Awaryjnie wyłącz podawanie HTML z cache — bez zmiany wp-config i drop-in');
        echo '</div>';
    }
    private static function form(string $command, string $label, string $hash = ''): void {
        echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '">'; wp_nonce_field('zp_html_cache_20261009'); echo '<input type="hidden" name="action" value="zp_html_cache_20261009"><input type="hidden" name="command" value="' . esc_attr($command) . '"><input type="hidden" name="review_hash" value="' . esc_attr($hash) . '">'; submit_button($label); echo '</form>';
    }
}
add_action('admin_menu', static function () { add_submenu_page('zp-suite', 'Cache HTML', 'Cache HTML', 'manage_options', ZP_HTML_Cache_20261009::PAGE, [ZP_HTML_Cache_20261009::class, 'page']); }, 10003);
add_action('admin_post_zp_html_cache_20261009', [ZP_HTML_Cache_20261009::class, 'handle']);
// 22.10.2026: post changes go through post_changed(), which leaves out revisions, drafts and the internal kinds.
foreach (['save_post' => 3, 'transition_post_status' => 3, 'deleted_post' => 2] as $hook => $count) { add_action($hook, [ZP_HTML_Cache_20261009::class, 'post_changed'], PHP_INT_MAX, $count); }
foreach (['trashed_post', 'untrashed_post', 'wp_update_nav_menu', 'switch_theme', 'upgrader_process_complete', 'created_term', 'edited_term', 'delete_term'] as $hook) { add_action($hook, [ZP_HTML_Cache_20261009::class, 'queue_purge'], PHP_INT_MAX, 0); }
foreach (['updated_post_meta', 'added_post_meta', 'deleted_post_meta'] as $hook) { add_action($hook, [ZP_HTML_Cache_20261009::class, 'meta_changed'], PHP_INT_MAX, 3); }
// 3.1.1: an option that is added or deleted changes pages as much as one that is updated.
foreach (['updated_option', 'added_option', 'deleted_option'] as $hook) { add_action($hook, [ZP_HTML_Cache_20261009::class, 'option_changed'], PHP_INT_MAX, 1); }
// 3.1.1: other modules ask for a refresh that keeps the generation (an EN page's new version) or for a full one.
add_action('zp_html_cache_refresh', [ZP_HTML_Cache_20261009::class, 'queue_refresh'], 10, 0);
add_action('zp_html_cache_purge', [ZP_HTML_Cache_20261009::class, 'queue_purge'], 10, 0);
add_filter('cron_schedules', static function (array $schedules): array { $schedules['zp_html_cache_15m'] = ['interval' => 900, 'display' => 'ZP Suite · sprzątanie cache HTML']; return $schedules; });
add_action(ZP_HTML_Cache_20261009::SWEEP, [ZP_HTML_Cache_20261009::class, 'sweep']);
add_action('update_option_zp_speed_files_ok', [ZP_HTML_Cache_20261009::class, 'speed_files_changed'], PHP_INT_MAX, 2);
if (function_exists('register_deactivation_hook')) {
    register_deactivation_hook(dirname(__DIR__) . '/zaprojektowani-suite.php', [ZP_HTML_Cache_20261009::class, 'deactivate']);
    register_deactivation_hook(dirname(__DIR__) . '/zaprojektowani-suite.php', static function (): void { wp_clear_scheduled_hook(ZP_HTML_Cache_20261009::SWEEP); });
}
// 3.2.60: while Suite's own folder is being replaced, the drop-in answers 503 instead of pages built without Suite.
add_filter('upgrader_source_selection', [ZP_HTML_Cache_20261009::class, 'swap_begin'], PHP_INT_MAX, 4);
add_filter('upgrader_install_package_result', [ZP_HTML_Cache_20261009::class, 'swap_end'], PHP_INT_MAX, 2);
