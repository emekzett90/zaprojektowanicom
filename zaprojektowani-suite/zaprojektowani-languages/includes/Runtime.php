<?php
namespace ZPL;

if (!defined('ABSPATH')) { exit; }

/** Request bootstrap, output buffer, SEO head and front-end config. */
final class Runtime {
    private static $chunks = '';
    private static $active = false;

    public static function boot(): void {
        if (Cleanup::conflict()) { return; }
        if (defined('WP_CLI') && WP_CLI) { return; }
        if (function_exists('wp_doing_cron') && wp_doing_cron()) { return; }
        if (function_exists('wp_doing_ajax') && wp_doing_ajax()) { Suite::ajax(); return; }
        if (is_admin()) { return; }
        $uri = (string) ($_SERVER['REQUEST_URI'] ?? '/');
        Router::$external = $uri;
        $path = (string) (wp_parse_url($uri, PHP_URL_PATH) ?: '/');
        $rel = Router::rel($path);
        if ($rel === '/sitemap-en.xml') { add_action('init', [Sitemap::class, 'render'], 0); return; }
        if (isset($_GET['zpl_frame'])) { add_action('init', [Frame::class, 'serve'], 0); return; }
        if (isset($_GET['rest_route']) || Router::never($rel)) { return; }
        Scan::boot_frame(); // Skaner EN frame (?zpl_scan=1, administrator only)
        // Fixes made by Skaner EN: extra attributes and JSON-LD fields to translate.
        Html::$extra = self::opt_list('zpl_extra_attrs', '~^data-[a-z0-9-]{1,40}$~');
        Page::$extraSchema = self::opt_list('zpl_extra_schema', '~^[A-Za-z]{2,40}$~');
        if (preg_match('~\.[a-z0-9]{2,5}$~i', $rel) && !preg_match('~\.html?$~i', $rel)) { return; }

        [$lang, $source, $mapped, $redirect] = Router::resolve($rel);
        $query = (string) wp_parse_url($uri, PHP_URL_QUERY);
        if ($redirect !== null) { self::redirect(Router::base() . $redirect . ($query !== '' ? '?' . $query : ''), 301); }
        if (Router::is_excluded($source)) {
            if ($lang === 'en') { self::redirect(Router::base() . $source . ($query !== '' ? '?' . $query : ''), 302); }
            Router::$excluded = true;
            return;
        }

        // GEO: Poland stays on PL. A detected non-PL visitor entering a Polish URL
        // gets the equivalent /en/ URL. 302 is intentional: this is visitor-specific,
        // not a permanent SEO redirect. Manual language choice disables GEO routing.
        $geoTarget = Geo::redirect_target($lang, $source);
        if ($geoTarget !== null) {
            self::redirect(Router::base() . $geoTarget . ($query !== '' ? '?' . $query : ''), 302);
        }

        Router::$lang = $lang;
        Router::$source = $source;
        Router::$mapped = $mapped;

        if ($lang === 'en') {
            $internal = Router::base() . $source . ($query !== '' ? '?' . $query : '');
            $_SERVER['REQUEST_URI'] = $internal;
            foreach (['PATH_INFO', 'ORIG_PATH_INFO', 'REDIRECT_URL'] as $k) {
                if (!empty($_SERVER[$k])) { $_SERVER[$k] = Router::base() . $source; }
            }
            add_filter('redirect_canonical', '__return_false', PHP_INT_MAX);
            add_filter('wp_redirect', [self::class, 'localize_redirect'], PHP_INT_MAX);
            add_filter('language_attributes', static function ($a) { return preg_replace('~\blang="[^"]*"~', 'lang="en"', (string) $a); }, PHP_INT_MAX);
            add_filter('rank_math/frontend/canonical', static function ($c) { return Router::url((string) $c, 'en'); }, PHP_INT_MAX);
        }
        add_filter('script_loader_src', [Suite::class, 'script_url'], PHP_INT_MAX);
        add_action('wp_enqueue_scripts', [self::class, 'assets'], 5);
        self::$active = true;
        ob_start([self::class, 'buffer']);
    }

    private static function opt_list(string $name, string $rx): array {
        $v = get_option($name, []);
        if (!is_array($v)) { return []; }
        return array_values(array_unique(array_filter(array_map('strval', $v), static function ($x) use ($rx) { return (bool) preg_match($rx, $x); })));
    }

    /** Labels inside SVG graphics (<text>/<tspan>), e.g. chart captions — the page walker skips SVG. */
    private static function svg_labels(string $html, string $source): string {
        if (stripos($html, '<svg') === false) { return $html; }
        return preg_replace_callback('~<svg\b.*?</svg>~is', static function ($m) use ($source) {
            if (!preg_match('~<(?:text|tspan)\b~i', $m[0])) { return $m[0]; }
            return preg_replace_callback('~(<(text|tspan)\b[^>]*>)([^<]+)(</\2>)~i', static function ($x) use ($source) {
                $k = Html::norm(Html::decode($x[3]));
                if (!Html::human($k)) { return $x[0]; }
                $t = Dict::get($k, 'en', $source);
                if ($t === null || $t === $k || strpos($t, '<') !== false) { return $x[0]; }
                return $x[1] . Html::esc($t) . $x[4];
            }, $m[0]) ?? $m[0];
        }, $html) ?? $html;
    }

    /** English ::before/::after labels for CSS rules found by Skaner EN (option zpl_extra_css). */
    private static function extra_css(): string {
        $rules = get_option('zpl_extra_css', []);
        if (!is_array($rules) || !$rules) { return ''; }
        $out = '';
        foreach ($rules as $r) {
            if (!is_array($r) || empty($r['sel']) || !isset($r['en']) || !in_array($r['pseudo'] ?? '', ['::before', '::after'], true)) { continue; }
            $sel = trim(preg_replace('~^(?:html|:root)(?=[\s.#\[:>]|$)~i', '', trim((string) $r['sel'])) ?? '');
            if (strlen($sel) > 400 || preg_match('~[{};<@\\\\]~', $sel)) { continue; }
            $en = str_replace(['\\', '"', "\r", "\n", '<'], ['\\\\', '\\"', '', '\\A ', '\\3C '], (string) $r['en']);
            $rule = 'html[lang^="en"] ' . ($sel === '' ? '' : $sel) . $r['pseudo'] . '{content:"' . $en . '"' . (!empty($r['imp']) ? '!important' : '') . '}';
            foreach (array_reverse((array) ($r['media'] ?? [])) as $m) {
                $m = trim((string) $m);
                if ($m === '' || strlen($m) > 200 || preg_match('~[{};<\\\\]~', $m) || !preg_match('~^@(?:media|supports)\b~i', $m)) { continue 2; }
                $rule = $m . '{' . $rule . '}';
            }
            $out .= $rule . "\n";
        }
        return $out === '' ? '' : '<style id="zpl-extra-css">' . $out . '</style>';
    }

    private static function redirect(string $to, int $code): void {
        if (!headers_sent()) {
            header('Location: ' . $to, true, $code);
            header('X-Redirect-By: zaprojektowani-languages');
        }
        exit;
    }

    public static function localize_redirect($url) {
        if (!is_string($url) || $url === '') { return $url; }
        $dest = Router::url($url, 'en');
        $destPath = (string) wp_parse_url($dest, PHP_URL_PATH);
        $extPath = (string) wp_parse_url(Router::$external, PHP_URL_PATH);
        if (Router::norm_path(Router::rel($destPath)) === Router::norm_path(Router::rel($extPath)) && (string) wp_parse_url($dest, PHP_URL_QUERY) === (string) wp_parse_url(Router::$external, PHP_URL_QUERY)) {
            return false; // would loop back to the requested English URL
        }
        return $dest;
    }

    public static function assets(): void {
        wp_enqueue_style('zpl', ZPL_URL . 'assets/zpl.css', [], ZPL_VERSION);
        // data/en.css is only used by the /en/ frontend. Loading it on Polish pages
        // made it a needless render-blocking stylesheet in Lighthouse.
        if (Router::$lang === 'en' && is_file(ZPL_DIR . 'data/en.css')) {
            wp_enqueue_style('zpl-en', ZPL_URL . 'data/en.css', [], Dict::meta()['version'] ?? ZPL_VERSION);
        }
        wp_enqueue_script('zpl-core', ZPL_URL . 'assets/zpl-core.js', [], ZPL_VERSION, ['in_footer' => true, 'strategy' => 'defer']);
        wp_enqueue_script('zpl', ZPL_URL . 'assets/zpl.js', ['zpl-core'], ZPL_VERSION, ['in_footer' => true, 'strategy' => 'defer']);
    }

    public static function buffer($chunk, $phase = 0) {
        if (!is_string($chunk)) { return $chunk; }
        if ($phase & PHP_OUTPUT_HANDLER_CLEAN) { self::$chunks = ''; return ''; }
        self::$chunks .= $chunk;
        if (!($phase & PHP_OUTPUT_HANDLER_FINAL)) { return ''; }
        $html = self::$chunks;
        self::$chunks = '';
        try {
            return self::process($html);
        } catch (\Throwable $e) {
            if (defined('WP_DEBUG') && WP_DEBUG) { error_log('[zpl] ' . $e->getMessage() . ' @' . $e->getFile() . ':' . $e->getLine()); }
            return $html;
        }
    }

    public static function process(string $html): string {
        if ($html === '' || stripos($html, '<html') === false || stripos($html, '</head>') === false) { return $html; }
        if ((defined('REST_REQUEST') && REST_REQUEST) || (function_exists('is_feed') && is_feed())) { return $html; }
        foreach (headers_list() as $h) {
            if (stripos($h, 'content-type:') === 0 && stripos($h, 'text/html') === false) { return $html; }
            if (stripos($h, 'content-encoding:') === 0) { return $html; }
        }
        if (!preg_match('//u', $html)) { return $html; }
        $lang = Router::$lang;
        $source = Router::$source;
        $stats = ['found' => 0, 'hit' => 0, 'miss' => 0];
        if ($lang === 'en') {
            $html = Page::translate($html, static function ($k, $kind) use ($source) { return Dict::get($k, 'en', $source); }, static function ($u) { return Router::url($u, 'en'); }, ['lang' => 'en', 'locale' => 'en_US']);
            $stats = Page::$stats;
            $html = Suite::patch_inline($html);
            $html = Suite::nav_active($html, $source);
            $html = self::svg_labels($html, $source);
            $xcss = self::extra_css();
            if ($xcss !== '') { $html = preg_replace('~</head>~i', $xcss . '</head>', $html, 1) ?? $html; }
            // RSS/Atom feeds carry the Polish posts: don't advertise them on English pages.
            $html = preg_replace('~<link\b(?=[^>]*\brel=["\']alternate["\'])(?=[^>]*\btype=["\']application/(?:rss|atom)\+xml["\'])[^>]*>\s*~i', '', $html) ?? $html;
        }
        $html = Suite::rewrite_script_urls($html);
        // Cookiebot must use the same language as the URL, independent of browser language.
        // v1.0.11: use Cookiebot manual blocking so uc.js can load asynchronously instead
        // of blocking first paint; non-essential trackers remain inert until consent.
        $html = Geo::cookiebot_culture($html, $lang);
        $html = Geo::cookiebot_manual_blocking($html);
        $html = Switcher::inject($html, $lang, $source);
        $html = self::seo($html, $lang, $source, $stats);
        $html = preg_replace('~<head\b[^>]*>~i', '$0' . Suite::early_script($source), $html, 1) ?? $html;
        // If the hosting/CDN did not provide a country header, start a one-time lightweight
        // geo lookup as the first head script. It caches a first-party country cookie.
        $html = Geo::inject_client($html, $lang, $source);
        if (Scan::active() && preg_match('~<head\b[^>]*>~i', $html, $hm, PREG_OFFSET_CAPTURE)) {
            $at = $hm[0][1] + strlen($hm[0][0]);
            $html = substr($html, 0, $at) . Scan::guard_script() . substr($html, $at); // before every other script
        }
        $html = self::config($html, $lang, $source);
        // Page marker for page-scoped CSS pseudo-content translations (data/en.css).
        $html = preg_replace('~<html\b(?![^>]*\bdata-zpl-page=)~i', '<html data-zpl-page="p-' . Dict::page_id($source) . '"', $html, 1) ?? $html;
        $html = self::ensure_assets($html);
        if ($lang === 'en' && $stats['miss'] > 0 && current_user_can('manage_options')) {
            Missing::remember(array_slice(Page::$missing, 0, 400, true), $source);
        }
        return $html;
    }

    private static function seo(string $html, string $lang, string $source, array $stats): string {
        $plUrl = home_url($source);
        $enUrl = home_url(Router::en_path($source));
        $mapped = Router::$mapped;
        $head = '';
        // Remove alternates added by earlier translators or SEO plugins; ours are authoritative.
        $html = preg_replace('~<link\b[^>]*\bhreflang\s*=[^>]*>\s*~i', '', $html) ?? $html;
        if ($mapped && Dict::has_page($source)) {
            $head .= '<link rel="alternate" hreflang="pl" href="' . esc_url($plUrl) . '">'
                . '<link rel="alternate" hreflang="en" href="' . esc_url($enUrl) . '">'
                . '<link rel="alternate" hreflang="x-default" href="' . esc_url($plUrl) . '">';
        }
        if ($lang === 'en') {
            $html = preg_replace('~<link\b[^>]*\brel\s*=\s*["\']?canonical["\']?[^>]*>\s*~i', '', $html) ?? $html;
            $q = (string) wp_parse_url(Router::$external, PHP_URL_QUERY);
            $head .= '<link rel="canonical" href="' . esc_url($enUrl) . '">';
            $coverage = $stats['found'] > 0 ? $stats['hit'] / $stats['found'] : 1;
            if (!$mapped || !Dict::has_page($source) || $coverage < 0.9 || is_404()) {
                $html = preg_replace('~<meta\b[^>]*\bname\s*=\s*["\']robots["\'][^>]*>\s*~i', '', $html) ?? $html;
                $head .= '<meta name="robots" content="noindex, follow">';
            }
            $head .= '<meta property="og:locale:alternate" content="pl_PL">';
        } elseif ($mapped) {
            $head .= '<meta property="og:locale:alternate" content="en_US">';
        }
        return preg_replace('~</head>~i', $head . '</head>', $html, 1) ?? $html;
    }

    /** Pages rendered without wp_footer() (custom full-document templates) still get the switch runtime. */
    private static function ensure_assets(string $html): string {
        if (strpos($html, 'zaprojektowani-languages/assets/zpl.js') !== false) { return $html; }
        $v = rawurlencode(ZPL_VERSION);
        $tags = '<script defer src="' . esc_url(ZPL_URL . 'assets/zpl-core.js?ver=' . $v) . '"></script><script defer src="' . esc_url(ZPL_URL . 'assets/zpl.js?ver=' . $v) . '"></script>';
        $css = '<link rel="stylesheet" href="' . esc_url(ZPL_URL . 'assets/zpl.css?ver=' . $v) . '">';
        if (is_file(ZPL_DIR . 'data/en.css')) { $css .= '<link rel="stylesheet" href="' . esc_url(ZPL_URL . 'data/en.css?ver=' . rawurlencode((string) (Dict::meta()['version'] ?? $v))) . '">'; }
        if (strpos($html, 'zaprojektowani-languages/assets/zpl.css') === false) { $html = preg_replace('~</head>~i', $css . '</head>', $html, 1) ?? $html; }
        $pos = strripos($html, '</body>');
        return $pos === false ? $html . $tags : substr($html, 0, $pos) . $tags . substr($html, $pos);
    }

    private static function config(string $html, string $lang, string $source): string {
        $flags = JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE;
        $meta = Dict::meta();
        $cfg = [
            'lang' => $lang,
            'src' => $source,
            'base' => Router::base(),
            'rest' => esc_url_raw(rest_url('zpl/v1/')),
            'data' => ZPL_URL . 'data/json/',
            'v' => (string) ($meta['version'] ?? ZPL_VERSION),
            'page' => Dict::has_page($source) ? Dict::page_id($source) : '',
            'ov' => (string) get_option('zpl_overrides_version', ''),
            'ex' => Router::excludes(),
            'en' => Router::en_path($source),
            'frame' => home_url('/'),
            'blobs' => preg_match_all('~<script\b[^>]*>\s*window\.(zp\w+)\s*=\s*[\[{]~', $html, $bm) ? array_values(array_unique($bm[1])) : [],
            'routes' => $lang === 'en' ? Router::routes() : null,
            'debug' => current_user_can('manage_options') && isset($_GET['zpl_debug']),
            'scan' => Scan::active(),
            'attrs' => Html::$extra,
        ];
        $tag = '<script type="application/json" id="zpl-config">' . wp_json_encode($cfg, $flags) . '</script>';
        $pos = strripos($html, '</body>');
        return $pos === false ? $html . $tag : substr($html, 0, $pos) . $tag . substr($html, $pos);
    }
}
