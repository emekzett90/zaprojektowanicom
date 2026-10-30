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
        if (Sitemap::type_for($rel) !== '') { add_action('wp_loaded', [Sitemap::class, 'render'], 0); return; }
        if (in_array($rel, Sitemap::LEGACY, true)) { add_action('init', [Sitemap::class, 'redirect'], 0); return; }
        if (isset($_GET['zpl_frame'])) { add_action('init', [Frame::class, 'serve'], 0); return; }
        if (isset($_GET['rest_route']) || Router::never($rel)) { return; }
        Scan::boot_frame(); // Skaner EN frame (?zpl_scan=1, administrator only)
        // Fixes made by Skaner EN: extra attributes and JSON-LD fields to translate.
        Html::$extra = self::opt_list('zpl_extra_attrs', '~^data-[a-z0-9-]{1,40}$~');
        Page::$extraSchema = self::opt_list('zpl_extra_schema', '~^[A-Za-z]{2,40}$~');
        if (preg_match('~\.[a-z0-9]{2,5}$~i', $rel) && !preg_match('~\.html?$~i', $rel)) { return; }

        $query = (string) wp_parse_url($uri, PHP_URL_QUERY);
        $public = in_array((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'), ['GET', 'HEAD'], true);
        [$lang, $source, $mapped, $redirect] = Router::resolve($rel, $query, $public);
        if ($redirect !== null) { self::redirect(Router::base() . $redirect . ($query !== '' ? '?' . $query : ''), 301); }
        if (Router::is_excluded($source)) {
            if (Lang::foreign($lang)) { self::redirect(Router::base() . $source . ($query !== '' ? '?' . $query : ''), 302); }
            Router::$excluded = true;
            return;
        }
        // 3.1.1: an English address without the closing slash ("/en", "/en/contact") gets a 301 to the one with it, as
        // WordPress gives Polish ones ("/kontakt" → "/kontakt/"). /en answered 200 as a second copy of /en/ (SearchFit, 14.10).
        if ($public && Lang::foreign($lang) && substr($rel, -1) !== '/' && !preg_match('~\.html?$~i', $rel)) {
            self::redirect(Router::base() . $rel . '/' . ($query !== '' ? '?' . $query : ''), 301);
        }

        // 3.1.1: an English address asked for without its /en/ gets a 301 to the English page, when WordPress has no page
        // there (before its own guess at a similar address, template_redirect 10).
        if ($public && $lang === 'pl') {
            add_action('template_redirect', static function () use ($rel, $query) {
                if (!is_404()) { return; }
                // 3.2.0: a 301 the owner saved for this address in ZP Suite → Błędy 404 wins (served at priority 5).
                if (function_exists('zp_suite3_redirects') && function_exists('zp_suite3_404_path') && isset(zp_suite3_redirects()[zp_suite3_404_path()]['to'])) { return; }
                $en = Router::bare_en($rel);
                if ($en === null) { return; }
                // 3.2.0: so does WordPress's own 301 for a post whose Polish address changed (normally at 10; it exits).
                if (function_exists('wp_old_slug_redirect')) { wp_old_slug_redirect(); }
                self::redirect(Router::base() . $en . ($query !== '' ? '?' . $query : ''), 301);
            }, 1);
        }

        // GEO: Poland stays on PL. A detected non-PL visitor entering a Polish URL
        // gets the equivalent /en/ URL. 302 is intentional: this is visitor-specific,
        // not a permanent SEO redirect. A manual choice of PL disables GEO routing, a choice of EN redirects.
        // Decided at plugins_loaded 21, after Tłumacz EN (20) has said which new posts have an English version.
        if ($lang === 'pl') {
            add_action('plugins_loaded', static function () use ($source, $mapped, $query) {
                $geoTarget = Geo::redirect_target('pl', $source, $mapped);
                if ($geoTarget === null) { return; }
                // Counted only for browsers that say it is a page view (scripts posing as browsers rarely do).
                if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET' && strtolower((string) ($_SERVER['HTTP_SEC_FETCH_MODE'] ?? '')) === 'navigate' && !Geo::is_speculative()) { Geo::count_redirect(); }
                if (!headers_sent()) { nocache_headers(); }
                self::redirect(Router::base() . $geoTarget . ($query !== '' ? '?' . $query : ''), 302);
            }, 21);
        }

        Router::$lang = $lang;
        Router::$source = $source;
        Router::$mapped = $mapped;

        if (Lang::foreign($lang)) {
            $internal = Router::base() . $source . ($query !== '' ? '?' . $query : '');
            $_SERVER['REQUEST_URI'] = $internal;
            foreach (['PATH_INFO', 'ORIG_PATH_INFO', 'REDIRECT_URL'] as $k) {
                if (!empty($_SERVER[$k])) { $_SERVER[$k] = Router::base() . $source; }
            }
            add_filter('redirect_canonical', '__return_false', PHP_INT_MAX);
            add_filter('wp_redirect', [self::class, 'localize_redirect'], PHP_INT_MAX);
            $htmlLang = Lang::HTML[$lang];
            add_filter('language_attributes', static function ($a) use ($htmlLang) { return preg_replace('~\blang="[^"]*"~', 'lang="' . $htmlLang . '"', (string) $a); }, PHP_INT_MAX);
            add_filter('rank_math/frontend/canonical', static function ($c) use ($lang) { return Router::url((string) $c, $lang); }, PHP_INT_MAX);
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
    private static function svg_labels(string $html, string $source, string $lang = 'en'): string {
        if (stripos($html, '<svg') === false) { return $html; }
        return preg_replace_callback('~<svg\b.*?</svg>~is', static function ($m) use ($source, $lang) {
            if (!preg_match('~<(?:text|tspan)\b~i', $m[0])) { return $m[0]; }
            return preg_replace_callback('~(<(text|tspan)\b[^>]*>)([^<]+)(</\2>)~i', static function ($x) use ($source, $lang) {
                $k = Html::norm(Html::decode($x[3]));
                if (!Html::human($k)) { return $x[0]; }
                $t = Dict::get($k, $lang, $source);
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
        $dest = Router::url($url, Router::$lang);
        $destPath = (string) wp_parse_url($dest, PHP_URL_PATH);
        $extPath = (string) wp_parse_url(Router::$external, PHP_URL_PATH);
        if (Router::norm_path(Router::rel($destPath)) === Router::norm_path(Router::rel($extPath)) && (string) wp_parse_url($dest, PHP_URL_QUERY) === (string) wp_parse_url(Router::$external, PHP_URL_QUERY)) {
            return false; // would loop back to the requested English URL
        }
        return $dest;
    }

    /** Minified copy of an asset (assets/x.min.js next to assets/x.js), the readable one with SCRIPT_DEBUG. */
    public static function asset(string $file): string {
        $min = preg_replace('~\.(js|css)$~', '.min.$1', $file) ?? $file;
        if ((defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) || !is_file(ZPL_DIR . 'assets/' . $min)) { $min = $file; }
        return ZPL_URL . 'assets/' . $min;
    }

    /** Refresh only the switch runtime when its bundled Suite copy changes. */
    private static function switch_version(): string {
        return ZPL_VERSION . (defined('ZP_SUITE_VERSION') ? '-suite-' . ZP_SUITE_VERSION : '');
    }

    public static function assets(): void {
        wp_enqueue_style('zpl', self::asset('zpl.css'), [], ZPL_VERSION);
        // data/en.css is only used by the /en/ frontend. Loading it on Polish pages
        // made it a needless render-blocking stylesheet in Lighthouse.
        if (Router::$lang === 'en' && is_file(ZPL_DIR . 'data/en.css')) {
            wp_enqueue_style('zpl-en', ZPL_URL . 'data/en.css', [], Dict::meta()['version'] ?? ZPL_VERSION);
        }
        // 1.2.0: the same texts drawn by CSS in German and Spanish (data/de.css, data/es.css).
        if (Lang::ai(Router::$lang) && is_file(ZPL_DIR . 'data/' . Router::$lang . '.css')) {
            wp_enqueue_style('zpl-' . Router::$lang, ZPL_URL . 'data/' . Router::$lang . '.css', [], ZPL_VERSION);
        }
        // The switch (both languages): the other language opens at its own address, fetched ahead, with a cross-fade.
        wp_enqueue_script('zpl-switch', self::asset('zpl-switch.js'), [], self::switch_version(), ['in_footer' => true, 'strategy' => 'defer']);
        // Pages in another language only: texts that page scripts add later are translated in the browser. Polish pages need none of it.
        if (Lang::foreign(Router::$lang)) {
            wp_enqueue_script('zpl-core', self::asset('zpl-core.js'), [], ZPL_VERSION, ['in_footer' => true, 'strategy' => 'defer']);
            wp_enqueue_script('zpl', self::asset('zpl.js'), ['zpl-switch', 'zpl-core'], ZPL_VERSION, ['in_footer' => true, 'strategy' => 'defer']);
        }
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
        if (Lang::foreign($lang)) {
            // 1.2.0: German and Spanish are translated from the page Polish visitors get, with the same last corrections
            // (zp_en_public_price_html for 'pl': the verified review count, the shortened descriptions), which is
            // the page Tłumacz AI read. English keeps its own path (shipped dictionaries and overrides).
            if (Lang::ai($lang)) { $html = (string) apply_filters('zpl_ai_source_html', $html, $lang, $source); }
            $html = Page::translate($html, static function ($k, $kind) use ($source, $lang) { return Dict::get($k, $lang, $source); }, static function ($u) use ($lang) { return Router::url($u, $lang); }, ['lang' => $lang, 'locale' => Lang::LOCALE[$lang], 'html' => Lang::HTML[$lang]]);
            $stats = Page::$stats;
            $html = Suite::patch_inline($html);
            $html = Suite::nav_active($html, $source);
            $html = self::svg_labels($html, $source, $lang);
            // Skaner EN's ::before/::after labels are English (html[lang^="en"] rules).
            $xcss = $lang === 'en' ? self::extra_css() : '';
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
        $early = Suite::early_script($source) . Switcher::early($lang);
        $html = preg_replace_callback('~<head\b[^>]*>~i', static function ($m) use ($early) { return $m[0] . $early; }, $html, 1) ?? $html;
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
        $html = self::ensure_assets($html, $lang);
        if ($lang === 'en' && $stats['miss'] > 0 && current_user_can('manage_options')) {
            Missing::remember(array_slice(Page::$missing, 0, 400, true), $source);
        }
        return (string) apply_filters('zp_en_public_price_html', $html, $lang, $source);
    }

    /**
     * 1.2.0: hreflang for every language with its own address and translations of this page: Polish, English (as
     * before) and German/Spanish once Tłumacz AI has made them public. A page that is not itself such a version
     * (no own address, not translated, noindex) lists none, as English always did.
     */
    public static function alternates(string $lang, string $source): array {
        $mapped = Router::$mapped;
        $self = $lang === 'pl' || $lang === 'en' ? true : ($mapped && Lang::is_public($lang) && Dict::has_page($source, $lang));
        if (!$self) { return []; }
        $out = ['pl' => home_url($source)];
        // English: the test it always had (this request's own route for /en/, the Polish page's route for Polish).
        $enOwn = $lang === 'pl' || $lang === 'en' ? $mapped : Router::has_route($source, 'en');
        if ($enOwn && Dict::has_page($source)) { $out['en'] = home_url(Router::en_path($source)); }
        foreach (Lang::AI as $l) {
            if (!Lang::is_public($l) || !Router::has_route($source, $l) || !Dict::has_page($source, $l)) { continue; }
            $p = Router::path_for($source, $l);
            if (Router::resolve($p)[1] === $source) { $out[$l] = home_url($p); }
        }
        // Polish alone is not an alternate (and an English page without its Polish pair lists none).
        if (count($out) < 2 || ($lang !== 'pl' && !isset($out[$lang]))) { return []; }
        return $out;
    }

    private static function seo(string $html, string $lang, string $source, array $stats): string {
        $plUrl = home_url($source);
        $selfUrl = home_url(Router::path_for($source, $lang));
        $mapped = Router::$mapped;
        $head = '';
        // Remove alternates added by earlier translators or SEO plugins; ours are authoritative.
        $html = preg_replace('~<link\b[^>]*\bhreflang\s*=[^>]*>\s*~i', '', $html) ?? $html;
        $alts = self::alternates($lang, $source);
        foreach ($alts as $l => $u) { $head .= '<link rel="alternate" hreflang="' . $l . '" href="' . esc_url($u) . '">'; }
        if ($alts) { $head .= '<link rel="alternate" hreflang="x-default" href="' . esc_url($plUrl) . '">'; }
        if (Lang::foreign($lang)) {
            $html = preg_replace('~<link\b[^>]*\brel\s*=\s*["\']?canonical["\']?[^>]*>\s*~i', '', $html) ?? $html;
            $head .= '<link rel="canonical" href="' . esc_url($selfUrl) . '">';
            $coverage = $stats['found'] > 0 ? $stats['hit'] / $stats['found'] : 1;
            if (!$mapped || !Dict::has_page($source, $lang) || $coverage < 0.9 || is_404() || !Lang::is_public($lang)) {
                $html = preg_replace('~<meta\b[^>]*\bname\s*=\s*["\']robots["\'][^>]*>\s*~i', '', $html) ?? $html;
                $head .= '<meta name="robots" content="noindex, follow">';
            }
            $head .= '<meta property="og:locale:alternate" content="pl_PL">';
            foreach (array_keys($alts) as $l) { if ($l !== 'pl' && $l !== $lang) { $head .= '<meta property="og:locale:alternate" content="' . Lang::LOCALE[$l] . '">'; } }
        } else {
            if ($mapped) { $head .= '<meta property="og:locale:alternate" content="en_US">'; }
            foreach (array_keys($alts) as $l) { if (Lang::ai($l)) { $head .= '<meta property="og:locale:alternate" content="' . Lang::LOCALE[$l] . '">'; } }
        }
        return preg_replace('~</head>~i', $head . '</head>', $html, 1) ?? $html;
    }

    /** Pages rendered without wp_footer() (custom full-document templates) still get the switch runtime. */
    private static function ensure_assets(string $html, string $lang): string {
        if (strpos($html, 'zaprojektowani-languages/assets/zpl-switch') !== false) { return $html; }
        $v = rawurlencode(ZPL_VERSION);
        $tags = '<script defer src="' . esc_url(self::asset('zpl-switch.js') . '?ver=' . rawurlencode(self::switch_version())) . '"></script>';
        if (Lang::foreign($lang)) {
            $tags .= '<script defer src="' . esc_url(self::asset('zpl-core.js') . '?ver=' . $v) . '"></script><script defer src="' . esc_url(self::asset('zpl.js') . '?ver=' . $v) . '"></script>';
        }
        $css = '<link rel="stylesheet" href="' . esc_url(self::asset('zpl.css') . '?ver=' . $v) . '">';
        if ($lang === 'en' && is_file(ZPL_DIR . 'data/en.css')) { $css .= '<link rel="stylesheet" href="' . esc_url(ZPL_URL . 'data/en.css?ver=' . rawurlencode((string) (Dict::meta()['version'] ?? $v))) . '">'; }
        if (Lang::ai($lang) && is_file(ZPL_DIR . 'data/' . $lang . '.css')) { $css .= '<link rel="stylesheet" href="' . esc_url(ZPL_URL . 'data/' . $lang . '.css?ver=' . $v) . '">'; }
        if (!preg_match('~zaprojektowani-languages/assets/zpl(?:\.min)?\.css~', $html)) { $html = preg_replace('~</head>~i', $css . '</head>', $html, 1) ?? $html; }
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
            'page' => Lang::ai($lang) ? '' : Dict::client_page_id($source),
            'ov' => (string) get_option('zpl_overrides_version', ''),
            'ex' => Router::excludes(),
            'en' => Router::en_path($source),
            // 1.2.0: the languages offered and this page's address in each (switch, visitor redirect, no-reload switch).
            'langs' => Lang::public(),
            'paths' => self::paths($source),
            // Revision of this page's translations: the browser keeps that exact answer (zpl.js readApproved).
            'rev' => $lang === 'en' ? (string) apply_filters('zpl_approved_revision', '', $source) : (Lang::ai($lang) ? (string) apply_filters('zpl_lang_revision', '', $source, $lang) : ''),
            // 1.2.0: German and Spanish texts that page scripts show after loading (Studio wyceny, forms), one cached answer.
            'dyn' => Lang::ai($lang) ? (string) apply_filters('zpl_lang_dyn', '', $lang) : '',
            'frame' => home_url('/'),
            'blobs' => preg_match_all('~<script\b[^>]*>\s*window\.(zp\w+)\s*=\s*[\[{]~', $html, $bm) ? array_values(array_unique($bm[1])) : [],
            'routes' => Lang::foreign($lang) ? Router::routes($lang) : null,
            // 1.2.0: the engine of the language change in place, loaded only when the visitor reaches for the switch.
            'soft' => ['core' => self::asset('zpl-core.js') . '?ver=' . ZPL_VERSION, 'js' => self::asset('zpl-soft.js') . '?ver=' . ZPL_VERSION],
            'debug' => current_user_can('manage_options') && isset($_GET['zpl_debug']),
            'scan' => Scan::active(),
            'attrs' => Html::$extra,
        ];
        $tag = '<script type="application/json" id="zpl-config">' . wp_json_encode($cfg, $flags) . '</script>';
        $pos = strripos($html, '</body>');
        return $pos === false ? $html . $tag : substr($html, 0, $pos) . $tag . substr($html, $pos);
    }

    /**
     * 1.2.0: this page's address in every offered language; a language without a version of this page points to its
     * home page (the switch never opens a Polish page under /de/), marked in 'home'.
     */
    public static function paths(string $source): array {
        $out = [];
        foreach (Lang::public() as $l) {
            if ($l === 'pl') { $out['pl'] = $source; continue; }
            $own = $l === 'en' ? Router::has_route($source) && Dict::has_page($source) : Router::has_route($source, $l) && Dict::has_page($source, $l);
            $out[$l] = $own ? Router::path_for($source, $l) : '/' . $l . '/';
        }
        return $out;
    }
}
