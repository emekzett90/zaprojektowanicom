<?php
/** Authenticated Suite workspace. Native WordPress screens, data, forms and permissions remain authoritative. */
if (!defined('ABSPATH')) { exit; }

final class ZP_Admin_Workspace_20261010 {
    public static function pages(): array {
        return ['zp-translator-v2', 'zp-suite', 'zp-suite-stats', 'zp-suite-positions', 'zp-suite-seo-plan', 'zp-suite-calendar', 'zp-suite-prepare', 'zp-suite-article-photos', 'zp-suite-translations', 'zp-suite-zapytania', 'zp-suite-performance', 'zp-suite-ai', 'zp-suite-settings', 'zp-suite-home-cms', 'zp-suite-publikacja', 'zp-suite-speed', 'zp-suite-content-20261009', 'zp-suite-photos-20261009', 'zp-suite-html-cache', 'zp-suite-gsc-quality', 'zp-suite-gsc-quality-translation', 'zp-tlumacz-en', 'zpl-languages', 'zpl-skaner'];
    }

    public static function post_actions(): array {
        return ['zpt2_action', 'zp_astra_preferences', 'zp_webp_local_probe', 'zp_queue_resume_auto', 'zp_published_photo_start', 'zp_published_photo_retry', 'zp_published_photo_apply', 'zp_published_photo_reconcile', 'zp_article_photos_core_flags_reconcile', 'zp_published_compress_start', 'zp_published_compress_recover', 'zp_published_compress_rollback', 'zp_published_photo_rollback', 'zp_published_photo_recover', 'zp_published_photo_cancel', 'zp_prep_publish_now', 'zp_article_google_submit', 'zp_article_google_inspect', 'zp_prep_expansion_start', 'zp_prep_expansion_continue', 'zp_prep_expansion_articles', 'zp_prep_statistics_adopt', 'zp_prep_statistics_articles', 'zp_prep_auto_start', 'zp_prep_auto_retry', 'zp_prep_auto_reuse_research', 'zp_prep_volume_policy', 'zp_prep_repair_captions', 'zp_prep_photo_start', 'zp_prep_photo_retry', 'zp_prep_photo_cancel', 'zp_prep_photo_rollback', 'zp_article_photos_start', 'zp_article_photos_retry', 'zp_article_photos_apply', 'zp_article_photos_rollback', 'zp_article_photos_recover', 'zp_article_photos_reconcile', 'zp_article_photos_cancel', 'zp_senuto_positions_refresh', 'zp_senuto_positions_config', 'zp_prep_settings', 'zp_prep_compress_existing', 'zp_prep_compress_recover', 'zp_prep_compress_rollback', 'zp_prep_models', 'zp_prep_import', 'zp_prep_start', 'zp_prep_retry', 'zp_prep_schedule', 'zp_prep_review', 'zp_prep_senuto', 'zp_en_rewrite_canary', 'zp_en_rewrite_full', 'zp_en_rewrite_luna_full', 'zp_en_rewrite_resolve_campaign', 'zp_en_rewrite_footer_proof', 'zp_en_rewrite_local_repair', 'zp_en_rewrite_serving_recheck', 'zp_en_nightly_settings', 'zp_en_nightly_scan', 'zp_en_currency_refresh', 'zp_en_rewrite_activate', 'zp_en_rewrite_resume', 'zp_en_rewrite_rollback', 'zpte', 'zpl_save', 'zp_feed', 'zp_ai', 'zp_cc_settings', 'zp_inbox_save', 'zp_inbox_delete', 'zp_inbox_bulk', 'zp_inbox_csv', 'zp_admin_stats_refresh_20261009', 'zp_cb_20261009_schedule', 'zp_cb_20261009_photos', 'zp_html_cache_20261009', 'zp_gsc_quality_20261009', 'zp_gsc_quality_translation_20261009', 'zp_admin_lighthouse_refresh', 'zp_admin_lighthouse_key', 'zp_admin_lighthouse_import'];
    }

    public static function route($uri = null): bool {
        $uri = $uri ?? ($_SERVER['REQUEST_URI'] ?? '');
        if (!is_string($uri) || $uri === '' || $uri[0] !== '/' || str_starts_with($uri, '//') || strpos($uri, '\\') !== false || preg_match('/[\x00-\x20\x7f]/', $uri)) { return false; }
        $path = wp_parse_url($uri, PHP_URL_PATH);
        $base = rtrim((string) wp_parse_url(home_url('/'), PHP_URL_PATH), '/');
        return $path === $base . '/zp-suite' || $path === $base . '/zp-suite/';
    }

    private static function origin(array $url): string {
        $scheme = strtolower((string) ($url['scheme'] ?? ''));
        return $scheme . '://' . strtolower((string) ($url['host'] ?? '')) . ':' . (int) ($url['port'] ?? ($scheme === 'https' ? 443 : 80));
    }

    /** Only exact same-origin admin.php and owned page slugs. Relative redirects must be root-relative. */
    public static function admin_target($url): ?array {
        if (!is_string($url) || $url === '' || strpos($url, '\\') !== false || preg_match('/[\x00-\x20\x7f]/', $url)) { return null; }
        $expected = wp_parse_url(admin_url('admin.php'));
        $parts = wp_parse_url($url);
        if (!is_array($expected) || !is_array($parts) || isset($parts['user']) || isset($parts['pass'])) { return null; }
        if (isset($parts['host']) || isset($parts['scheme'])) {
            if (self::origin($parts) !== self::origin($expected)) { return null; }
        } elseif ($url[0] !== '/' || str_starts_with($url, '//')) { return null; }
        if (($parts['path'] ?? '') !== ($expected['path'] ?? '')) { return null; }
        $query = []; parse_str((string) ($parts['query'] ?? ''), $query);
        if (!is_string($query['page'] ?? null) || !in_array($query['page'], self::pages(), true)) { return null; }
        if (isset($query['zp_workspace']) && !is_string($query['zp_workspace'])) { return null; }
        return ['page' => $query['page'], 'workspace' => ($query['zp_workspace'] ?? '') === '1'];
    }

    public static function referrer($url = null): bool {
        $url = $url ?? ($_SERVER['HTTP_REFERER'] ?? '');
        if (!is_string($url) || !is_array(wp_parse_url($url)) || !isset(wp_parse_url($url)['host'])) { return false; }
        $target = self::admin_target($url);
        return $target !== null && $target['workspace'];
    }

    private static function permitted(): bool {
        return is_user_logged_in() && current_user_can('manage_options');
    }

    /** A context marker never authenticates a visitor or grants a capability. */
    public static function active(): bool {
        if (!is_admin() || !self::permitted()) { return false; }
        if (isset($_GET['zp_workspace']) && (!is_string($_GET['zp_workspace']) || $_GET['zp_workspace'] !== '1')) { return false; }
        $target = self::admin_target($_SERVER['REQUEST_URI'] ?? '');
        if ($target !== null) { return (isset($_GET['zp_workspace']) && $_GET['zp_workspace'] === '1') || self::referrer(); }
        // Native admin-post/AJAX retain their existing nonce/capability handlers and use the child page's referrer.
        $path = wp_parse_url((string) ($_SERVER['REQUEST_URI'] ?? ''), PHP_URL_PATH);
        if ($path === wp_parse_url(admin_url('admin-ajax.php'), PHP_URL_PATH)) { return self::referrer(); }
        if ($path !== wp_parse_url(admin_url('admin-post.php'), PHP_URL_PATH)) { return false; }
        if (isset($_POST['_zp_workspace']) && (!is_string($_POST['_zp_workspace']) || $_POST['_zp_workspace'] !== '1')) { return false; }
        $marked_post = ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && ($_POST['_zp_workspace'] ?? '') === '1' && is_string($_POST['action'] ?? null) && in_array($_POST['action'], self::post_actions(), true);
        return self::referrer() || $marked_post;
    }

    public static function child(): bool {
        return self::active() && self::admin_target($_SERVER['REQUEST_URI'] ?? '') !== null;
    }

    public static function redirect($location, int $status = 302) {
        if (!self::active() || self::admin_target($location) === null) { return $location; }
        return add_query_arg('zp_workspace', '1', $location);
    }

    public static function headers(): void {
        nocache_headers();
        if (!headers_sent()) {
            header('Cache-Control: private, no-store, no-cache, must-revalidate, max-age=0');
            header('X-Robots-Tag: noindex, nofollow, noarchive');
            header('X-Frame-Options: SAMEORIGIN');
            // An additional frame restriction does not replace a hosting/site Content-Security-Policy.
            header("Content-Security-Policy: frame-ancestors 'self'", false);
        }
    }

    public static function serve(): void {
        if (!self::route()) { return; }
        self::headers();
        if (!is_user_logged_in()) { wp_safe_redirect(wp_login_url(home_url('/zp-suite/')), 302, 'ZP Suite'); exit; }
        if (!current_user_can('manage_options')) { wp_die('Brak uprawnień do ZP Suite.', 'ZP Suite', ['response' => 403]); exit; }
        if (!in_array($_SERVER['REQUEST_METHOD'] ?? 'GET', ['GET', 'HEAD'], true)) {
            status_header(405); header('Allow: GET, HEAD'); exit;
        }
        status_header(200);
        header('Content-Type: text/html; charset=UTF-8');
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'HEAD') { exit; }
        $frame = add_query_arg(['page' => 'zp-suite', 'zp_workspace' => '1'], admin_url('admin.php'));
        $font = defined('ZP_SUITE_URL') ? ZP_SUITE_URL . 'assets/campaigns/' : '';
        $fonts = '';
        if ($font !== '') { foreach ([400, 600, 700] as $weight) { $fonts .= '@font-face{font-family:"Plus Jakarta Sans";src:url("' . esc_url($font . 'plus-jakarta-sans-' . $weight . '.woff2') . '") format("woff2");font-weight:' . $weight . ';font-style:normal;font-display:swap}'; } }
        echo '<!doctype html><html lang="pl"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex,nofollow,noarchive"><title>ZP Suite — panel zarządzania</title><style>' . $fonts . 'html,body{height:100%;margin:0}body{display:grid;grid-template-rows:40px minmax(0,1fr);background:#f4f6f9;color:#071426;font:12px "Plus Jakarta Sans",sans-serif}.zpWorkspaceBar{display:flex;align-items:center;justify-content:space-between;padding:0 20px;background:#071426;color:#fff}.zpWorkspaceBar h1{font-size:13px;line-height:1.2;margin:0;font-weight:700}.zpWorkspaceBar a{color:#e4edf8;text-decoration:none;font-size:11px}.zpWorkspaceBar a:focus-visible{outline:2px solid #fff;outline-offset:4px}main{min-height:0}iframe{display:block;width:100%;height:100%;border:0}</style></head><body><header class="zpWorkspaceBar"><h1>ZP Suite</h1>';
        if (current_user_can('activate_plugins')) { echo '<a href="' . esc_url(admin_url('plugins.php')) . '" target="_top">Wtyczki WordPress ↗</a>'; }
        echo '</header><main><iframe src="' . esc_url($frame) . '" title="ZP Suite — zarządzanie stroną" referrerpolicy="same-origin"></iframe></main></body></html>';
        exit;
    }

    public static function child_headers(): void { if (self::child()) { self::headers(); } }

    public static function child_style(): void {
        if (!self::child()) { return; }
        echo '<style id="zp-workspace-native-chrome">html.wp-toolbar{padding-top:0!important}#wpadminbar,#adminmenumain,#adminmenuback,#adminmenuwrap,#wpfooter{display:none!important}#wpcontent,#wpfooter{margin-left:0!important}#wpbody-content{padding-bottom:20px!important}#wpcontent{padding-left:0!important}body.folded #wpcontent{margin-left:0!important}body.zpAdmin20-route .wrap.zpAdmin20{margin:24px auto 0!important;width:calc(100% - 48px);max-width:1450px}@media(max-width:782px){html.wp-toolbar{padding-top:0!important}#wpcontent{margin-left:0!important;padding-left:0!important}.auto-fold #wpcontent{position:static!important;margin-left:0!important}body.zpAdmin20-route .wrap.zpAdmin20{margin:16px auto 0!important;width:calc(100% - 24px)}}</style>';
    }

    public static function child_navigation(): void {
        if (!self::child()) { return; }
        $config = ['pages' => self::pages(), 'postActions' => self::post_actions(), 'adminPath' => wp_parse_url(admin_url('admin.php'), PHP_URL_PATH), 'postPath' => wp_parse_url(admin_url('admin-post.php'), PHP_URL_PATH), 'adminBase' => rtrim((string) wp_parse_url(admin_url('/'), PHP_URL_PATH), '/') . '/'];
        echo '<script id="zp-workspace-native-navigation">(function(){"use strict";const cfg=' . wp_json_encode($config, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) . ';const own=u=>u.origin===location.origin&&u.pathname===cfg.adminPath&&cfg.pages.includes(u.searchParams.get("page"));const current=new URL(location.href);if(own(current)){current.searchParams.set("zp_workspace","1");history.replaceState(null,"",current.href);}function link(a){try{const u=new URL(a.getAttribute("href"),location.href);if(own(u)){u.searchParams.set("zp_workspace","1");a.href=u.href;}else if(u.origin===location.origin&&u.pathname.startsWith(cfg.adminBase)&&!a.hasAttribute("download")){a.target="_top";}}catch(e){}}function marker(f,name){let input=f.querySelector("input[name="+name+"]");if(!input){input=document.createElement("input");input.type="hidden";input.name=name;f.appendChild(input);}input.value="1";}function form(f){try{const u=new URL(f.action||location.href,location.href);if(u.origin!==location.origin)return;if(f.method.toLowerCase()==="get"){const p=f.elements.namedItem("page");if(u.pathname===cfg.adminPath&&cfg.pages.includes(p&&typeof p.value==="string"?p.value:u.searchParams.get("page")))marker(f,"zp_workspace");}else if(f.method.toLowerCase()==="post"&&u.pathname===cfg.postPath){const a=f.elements.namedItem("action");if(a&&typeof a.value==="string"&&cfg.postActions.includes(a.value))marker(f,"_zp_workspace");}}catch(err){}}document.querySelectorAll("a[href]").forEach(link);document.querySelectorAll("form").forEach(form);document.addEventListener("click",e=>{const a=e.target.closest&&e.target.closest("a[href]");if(a)link(a);},true);document.addEventListener("submit",e=>{if(e.target instanceof HTMLFormElement)form(e.target);},true);})();</script>';
    }
}

function zp_suite_workspace_active(): bool { return ZP_Admin_Workspace_20261010::active(); }

// Exact route only: prevent the earlier frontend language buffer/Geo jobs from decorating the private shell.
if (ZP_Admin_Workspace_20261010::route()) { remove_action('plugins_loaded', ['ZPL\\Runtime', 'boot'], PHP_INT_MIN); }
// Auth exists at init. Serving before public init/wp_loaded jobs avoids AI logging/setup and all theme rendering.
add_action('init', [ZP_Admin_Workspace_20261010::class, 'serve'], -1000);
add_action('template_redirect', [ZP_Admin_Workspace_20261010::class, 'serve'], 0);
add_action('admin_init', [ZP_Admin_Workspace_20261010::class, 'child_headers'], 0);
add_action('admin_head', [ZP_Admin_Workspace_20261010::class, 'child_style'], PHP_INT_MAX);
add_action('admin_footer', [ZP_Admin_Workspace_20261010::class, 'child_navigation'], PHP_INT_MAX);
add_filter('wp_redirect', [ZP_Admin_Workspace_20261010::class, 'redirect'], PHP_INT_MAX, 2);
