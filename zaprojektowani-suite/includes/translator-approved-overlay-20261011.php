<?php
if (!defined('ABSPATH')) { exit; }

/** Read-only serving layer. The rewrite module owns versions, approval and rollback. */
final class ZP_Translator_Approved_Overlay_20261011 {
    const PREVIEW = 'zp_en_rewrite_preview';
    const MAX_KEYS = 6000;
    const MAX_BYTES = 2097152;
    private static $pages = [];

    public static function register(): void {
        add_action('rest_api_init', [self::class, 'routes']);
        add_action('wp_head', [self::class, 'preview_config'], 2);
        add_action('template_redirect', [self::class, 'preview_headers'], 0);
        add_filter('script_loader_src', [self::class, 'script_source'], PHP_INT_MAX, 2);
    }

    public static function flush(): void { self::$pages = []; }

    public static function public_source(string $source): int {
        if (!class_exists('ZPL\\Router') || !class_exists('ZPL\\Html') || $source === '' || strlen($source) > 512
            || $source[0] !== '/' || strpos($source, '?') !== false || strpos($source, '#') !== false
            || preg_match('~[\\x00-\\x20\\\\]~', $source) || preg_match('~%(?:2f|5c|00)~i', $source)) { return 0; }
        if (\ZPL\Router::norm_path($source) !== $source || \ZPL\Router::never($source)
            || \ZPL\Router::is_excluded($source) || preg_match('~^/(?:en|oferty|offers|wp-admin|wp-json|wp-content|wp-includes|wp-login\.php|wp-cron\.php)(?:/|$)~i', $source)) { return 0; }
        $routes = \ZPL\Router::routes();
        if (!isset($routes[$source]) || !is_string($routes[$source]) || strpos($routes[$source], '/en/') !== 0) { return 0; }
        $id = (int) url_to_postid(home_url($source));
        if ($source === '/' && !$id) { $id = (int) get_option('page_on_front'); }
        if ($source === '/kampanie-reklamowe/' && !$id) {
            $native = get_page_by_path('kampanie-reklamowe', OBJECT, 'page');
            if (!is_object($native) || !is_int($native->ID ?? null) || $native->ID <= 0) { return 0; }
            $id = $native->ID;
            clean_post_cache($id); wp_cache_delete($id, 'post_meta');
            if (get_page_uri($id) !== 'kampanie-reklamowe' || get_permalink($id) !== home_url($source)) { return 0; }
        }
        if (!$id && class_exists('ZP_EN_Rewrite_Nightly_20261011')) {
            $d = ZP_EN_Rewrite_Nightly_20261011::descriptor($source);
            if ($d && get_permalink($d['post_id']) === home_url($source)) { $id = $d['post_id']; }
        }
        $p = $id ? get_post($id) : null;
        if (!$p || $p->post_status !== 'publish' || !in_array($p->post_type, ['post', 'page'], true) || (string) $p->post_password !== '') { return 0; }
        $robots = get_post_meta($id, 'rank_math_robots', true);
        if (is_string($robots)) { $robots = preg_split('~[\s,]+~', strtolower($robots)); }
        if (!is_array($robots)) { return 0; }
        foreach ($robots as $r) {
            if (!is_string($r) || in_array(strtolower(trim($r)), ['noindex', 'noarchive'], true)) { return 0; }
        }
        return $id;
    }

    private static function preview_version(): string {
        if (!isset($_GET[self::PREVIEW]) || !is_string($_GET[self::PREVIEW]) || !current_user_can('manage_options')) { return ''; }
        $v = (string) wp_unslash($_GET[self::PREVIEW]);
        return preg_match('~^[A-Za-z0-9][A-Za-z0-9_-]{0,95}$~D', $v) ? $v : '';
    }

    /** Always a public page; a staged version additionally requires its current editor. */
    public static function page(string $source, string $preview = ''): array {
        $empty = ['source' => $source, 'revision' => '', 'map' => []];
        $id = self::public_source($source);
        if (!$id || !function_exists('zp_en_rewrite_approved_page')) { return $empty; }
        if ($preview !== '' && (!preg_match('~^[A-Za-z0-9][A-Za-z0-9_-]{0,95}$~D', $preview)
            || !current_user_can('manage_options') || !current_user_can('edit_post', $id))) { return $empty; }
        $cache = $source . '|' . $preview;
        if (array_key_exists($cache, self::$pages)) { return self::$pages[$cache]; }
        try { $p = zp_en_rewrite_approved_page($source, $preview); }
        catch (\Throwable $e) { return self::$pages[$cache] = $empty; }
        if (!is_array($p) || ($p['source'] ?? null) !== $source || !isset($p['revision'], $p['map'])
            || !is_string($p['revision']) || strlen($p['revision']) > 192 || !is_array($p['map']) || count($p['map']) > self::MAX_KEYS) { return self::$pages[$cache] = $empty; }
        $map = [];
        foreach ($p['map'] as $key => $en) {
            if (!is_string($key) || !is_string($en) || $key === '' || $en === '' || strlen($key) > 16000 || strlen($en) > 64000
                || !preg_match('//u', $key) || !preg_match('//u', $en)
                || preg_match('~[\x00-\x08\x0B\x0C\x0E-\x1F]~', $key . $en)
                || !\ZPL\Html::markers_match($key, $en)) { return self::$pages[$cache] = $empty; }
            $map[$key] = $en;
        }
        $json = wp_json_encode($map, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if (!is_string($json) || strlen($json) > self::MAX_BYTES || ($map && $p['revision'] === '')) { return self::$pages[$cache] = $empty; }
        if (!$map) { return self::$pages[$cache] = $empty; }
        // A cache digest is safe to publish; the provider's private version identifier is not returned.
        return self::$pages[$cache] = ['source' => $source,
            'revision' => hash('sha256', $source . '|' . $p['revision'] . '|' . $json), 'map' => $map];
    }

    public static function map(string $source, string $lang = 'en', string $preview = ''): array {
        if (!in_array($lang, ['en', 'pl'], true)) { return []; }
        $map = self::page($source, $preview)['map'];
        if ($lang === 'en') { return $map; }
        $reverse = [];
        foreach ($map as $pl => $en) { $k = \ZPL\Html::norm($en); if (!isset($reverse[$k])) { $reverse[$k] = $pl; } }
        return $reverse;
    }

    public static function lookup(string $key, string $lang, string $source): ?string {
        $map = self::map($source, $lang, self::preview_version());
        return isset($map[$key]) ? $map[$key] : null;
    }

    public static function routes(): void {
        register_rest_route('zpl/v1', '/approved-page', ['methods' => 'GET',
            'permission_callback' => '__return_true', 'callback' => [self::class, 'rest']]);
    }

    public static function rest($request) {
        $src = $request->get_param('src'); $lang = $request->get_param('lang'); $preview = $request->get_param('preview');
        if (!is_string($src) || !is_string($lang) || !in_array($lang, ['en', 'pl'], true)
            || ($preview !== null && !is_string($preview))) { return new WP_Error('zp_en_overlay_input', 'Nieprawidłowe parametry.', ['status' => 400]); }
        $preview = $preview ?? '';
        if ($preview !== '' && (!current_user_can('manage_options') || !preg_match('~^[A-Za-z0-9][A-Za-z0-9_-]{0,95}$~D', $preview))) {
            return new WP_Error('zp_en_overlay_preview', 'Podgląd wymaga zalogowanego administratora.', ['status' => 403]);
        }
        $id = self::public_source($src);
        if (!$id || ($preview !== '' && !current_user_can('edit_post', $id))) { return new WP_Error('zp_en_overlay_source', 'Brak publicznej wersji strony.', ['status' => 404]); }
        $p = self::page($src, $preview);
        $res = new WP_REST_Response(['ok' => true, 'source' => $src, 'lang' => $lang,
            'revision' => $p['revision'], 'map' => (object) self::map($src, $lang, $preview)]);
        $res->header('Cache-Control', 'no-store, private, max-age=0');
        $res->header('Vary', 'Cookie');
        $res->header('X-Robots-Tag', 'noindex, nofollow');
        return $res;
    }

    /** A private full-page preview cannot become a shared cached or indexed response. */
    public static function preview_headers(): void {
        if (self::preview_version() === '' || !class_exists('ZPL\\Router') || \ZPL\Router::$lang !== 'en') { return; }
        $id = self::public_source(\ZPL\Router::$source);
        if (!$id || !current_user_can('edit_post', $id)) { return; }
        if (!defined('DONOTCACHEPAGE')) { define('DONOTCACHEPAGE', true); }
        nocache_headers();
        if (!headers_sent()) { header('X-Robots-Tag: noindex, nofollow', true); }
    }

    /** Only this existing client handle receives a new URL; no new assets or dependencies. */
    public static function script_source(string $src, string $handle): string {
        if ($handle !== 'zpl' || !defined('ZPL_URL')) { return $src; }
        $expected = ZPL_URL . 'assets/zpl.js';
        if (strtok($src, '?') !== $expected) { return $src; }
        return add_query_arg('zp_en_overlay', '52', $src);
    }

    /** A public digest proves serving; only an authenticated preview gets its REST nonce. */
    public static function preview_config(): void {
        if (!class_exists('ZPL\\Router') || \ZPL\Router::$lang !== 'en') { return; }
        $source = \ZPL\Router::$source; $id = self::public_source($source);
        if (!$id) { return; }
        $v = self::preview_version();
        if ($v !== '' && !current_user_can('edit_post', $id)) { $v = ''; }
        $p = self::page($source, $v);
        $flags = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES;
        if ($p['map'] && $p['revision'] !== '') {
            $json = wp_json_encode(['source' => $source, 'revision' => $p['revision']], $flags);
            echo '<script type="application/json" id="zp-en-approved-overlay">' . $json . '</script>';
        }
        if ($v !== '') {
            $json = wp_json_encode(['source' => $source, 'version' => $v, 'nonce' => wp_create_nonce('wp_rest')], $flags);
            echo '<script type="application/json" id="zp-en-approved-preview">' . $json . '</script>';
        }
    }

}

function zp_en_approved_lookup(string $key, string $lang, string $source): ?string {
    return ZP_Translator_Approved_Overlay_20261011::lookup($key, $lang, $source);
}
