<?php
/** Explicitly managed public HTML cache. Loading this module never enables caching or edits wp-config. */
if (!defined('ABSPATH')) { exit; }

final class ZP_HTML_Cache_20261009 {
    public const PAGE = 'zp-suite-html-cache';
    public const BACKUP = 'zp_html_cache_20261009_backup';
    public const STATE = 'zp_html_cache_20261009_state';
    public const PREFIX = "<?php exit; ?>\n";
    private static $purge_queued = false;

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
    public static function policy(bool $enabled = true): array {
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
        if (!$paths) { throw new RuntimeException('Brak publicznych kanonicznych ścieżek do cache.'); } ksort($paths);
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
        return ['schema' => 1, 'enabled' => $enabled, 'ttl' => $ttl, 'host' => $home['host'], 'paths' => $paths, 'geo_polish_safe' => !$geo_enabled || $polish_stays, 'next_publication' => $next, 'generation' => bin2hex(random_bytes(16))];
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
    private static function write_policy(array $policy): void {
        $file = self::dir() . '/policy.php'; self::atomic($file, self::PREFIX . wp_json_encode($policy), 0600, is_file($file) ? (string) hash_file('sha256', $file) : hash('sha256', ''));
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
    private static function disabled_policy(): array { return ['schema' => 1, 'enabled' => false, 'ttl' => 1800, 'host' => '', 'paths' => [], 'geo_polish_safe' => false, 'next_publication' => 0, 'generation' => bin2hex(random_bytes(16))]; }
    public static function purge(): void {
        $state = get_option(self::STATE, []); if (empty($state['enabled'])) { return; }
        $backup = get_option(self::BACKUP, []);
        if (is_link(self::dropin()) || !is_file(self::dropin()) || hash_file('sha256', self::dropin()) !== ($backup['dropin_installed_sha'] ?? '')) { return; }
        try {
            self::write_policy(self::policy());
            $files = glob(self::dir() . '/zpc-*.php') ?: []; $count = 0;
            foreach ($files as $file) { if (++$count > 1000) { break; } if (!is_link($file) && preg_match('/\/zpc-[a-f0-9]{64}\.php$/D', $file)) { @unlink($file); } }
        } catch (Throwable $error) {
            // Existing TTL/deadline guard still limits stale content; failure visible to admins.
            update_option('zp_html_cache_20261009_error', 'Nie odświeżono polityki cache; sprawdź uprawnienia katalogu.', false);
        }
    }
    public static function queue_purge(): void {
        if (self::$purge_queued || empty(get_option(self::STATE, [])['enabled'])) { return; }
        self::$purge_queued = true; add_action('shutdown', [self::class, 'purge'], PHP_INT_MAX);
    }
    public static function option_changed(string $option): void {
        // Counters, admin progress/logs and lookup memoization do not change rendered content.
        if (in_array($option, ['zp_ai_stats', 'zpte_log', 'zpte_usage', 'zpte_progress', 'zpte_last_plan', 'zp_speed_img_sizes', 'zp_speed_live_paths'], true)) { return; }
        if (preg_match('/^(?:zp_(?!html_cache_)|zpl_|zpte_|rank-math-|theme_mods_|widget_|sidebars_widgets|page_on_front|show_on_front|blogname|blogdescription|permalink_structure)/', $option)) { self::queue_purge(); }
    }
    public static function meta_changed($meta_id, $post_id, $key): void { if (!in_array($key, ['_elementor_css', '_elementor_element_cache', '_elementor_page_assets', '_edit_lock', '_edit_last', '_zp_post_reads', '_zp_post_views'], true)) { self::queue_purge(); } }
    public static function handle(): void {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST' || !self::allowed() || !wp_verify_nonce(is_string($_POST['_wpnonce'] ?? null) ? wp_unslash($_POST['_wpnonce']) : '', 'zp_html_cache_20261009')) { wp_die('Brak uprawnień lub błędny nonce.', '', ['response' => 403]); }
        try {
            $command = $_POST['command'] ?? '';
            if ($command === 'enable') { self::enable(is_string($_POST['review_hash'] ?? null) ? wp_unslash($_POST['review_hash']) : ''); }
            elseif ($command === 'restore') { self::restore(); }
            elseif ($command === 'purge') { self::purge(); }
            elseif ($command === 'disable') { self::disable(); }
            else { throw new RuntimeException('Niedozwolone działanie.'); }
        } catch (Throwable $error) { set_transient('zp_html_cache_notice_' . get_current_user_id(), $error->getMessage(), 600); }
        wp_safe_redirect(admin_url('admin.php?page=' . self::PAGE)); exit;
    }
    public static function page(): void {
        if (!self::allowed()) { wp_die('Brak uprawnień administratora.'); }
        echo '<div class="wrap"><h1>ZP Suite — Cache HTML</h1><p>Cache własnego kodu HTML: 1800 sekund, publiczne kanoniczne ścieżki PL, anonimowy GET bez parametrów i cookies, polski Accept-Language. Panel, REST/AJAX, formularze POST, podglądy, EN, Studio Wyceny i strony prywatne są pomijane. Wygląd i animacje są zachowane. Przy terminie publikacji omijamy HIT, aby WordPress uruchomił cron.</p>';
        $notice = get_transient('zp_html_cache_notice_' . get_current_user_id()); if ($notice) { echo '<div class="notice notice-error"><p>' . esc_html($notice) . '</p></div>'; delete_transient('zp_html_cache_notice_' . get_current_user_id()); }
        $state = get_option(self::STATE, []); $error = get_option('zp_html_cache_20261009_error', ''); if ($error) { echo '<div class="notice notice-error"><p>' . esc_html($error) . '</p></div>'; }
        $flags = ['WP_CACHE' => defined('WP_CACHE') && WP_CACHE, 'OPcache enable' => filter_var(ini_get('opcache.enable'), FILTER_VALIDATE_BOOLEAN), 'OPcache timestamps' => filter_var(ini_get('opcache.validate_timestamps'), FILTER_VALIDATE_BOOLEAN), 'OPcache restrict_api set' => (string) ini_get('opcache.restrict_api') !== '', 'OPcache status readable' => function_exists('opcache_get_status') && @opcache_get_status(false) !== false];
        echo '<table class="widefat"><tr><th>Bezpieczny raport</th><th>Wartość</th></tr>'; foreach ($flags as $key => $value) { echo '<tr><td>' . esc_html($key) . '</td><td>' . ($value ? 'tak' : 'nie') . '</td></tr>'; } echo '</table>';
        try {
            $dropin = self::read(self::dropin(), true); echo '<p>advanced-cache.php: ' . strlen($dropin) . ' B; SHA-256 <code>' . esc_html(hash('sha256', $dropin)) . '</code></p><details><summary>Treść drop-in z ukrytymi liniami poświadczeń</summary><pre style="white-space:pre-wrap;max-height:500px;overflow:auto">' . esc_html(self::redact($dropin)) . '</pre></details>';
            if (empty($state['enabled'])) {
                $review = self::preflight(); echo '<p>Gotowe: ' . count($review['policy']['paths']) . ' ścieżek. Zmiana wp-config.php obejmuje wyłącznie WP_CACHE=false → true albo dodanie jednej deklaracji. Treści wp-config.php nie wyświetlamy. Oba oryginalne pliki trafiają przed zapisem do prywatnej opcji bazy (autoload=false). Konfiguracja SHA <code>' . esc_html($review['config_sha']) . '</code>.</p>';
                self::form('enable', 'Włącz własny cache HTML z prywatną kopią i kontrolą hashów', $review['review_hash']);
            } else { echo '<p><strong>Cache włączony.</strong> Ścieżki: ' . (int) ($state['paths'] ?? 0) . '; TTL 1800 s. Dowód odpowiedzi: X-ZP-Cache HIT/MISS/BYPASS.</p>'; self::form('purge', 'Odśwież własny cache'); }
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
foreach (['save_post', 'deleted_post', 'trashed_post', 'untrashed_post', 'transition_post_status', 'wp_update_nav_menu', 'switch_theme', 'upgrader_process_complete', 'created_term', 'edited_term', 'delete_term'] as $hook) { add_action($hook, [ZP_HTML_Cache_20261009::class, 'queue_purge'], PHP_INT_MAX, 0); }
foreach (['updated_post_meta', 'added_post_meta', 'deleted_post_meta'] as $hook) { add_action($hook, [ZP_HTML_Cache_20261009::class, 'meta_changed'], PHP_INT_MAX, 3); }
add_action('updated_option', [ZP_HTML_Cache_20261009::class, 'option_changed'], PHP_INT_MAX, 1);
if (function_exists('register_deactivation_hook')) { register_deactivation_hook(dirname(__DIR__) . '/zaprojektowani-suite.php', [ZP_HTML_Cache_20261009::class, 'deactivate']); }
