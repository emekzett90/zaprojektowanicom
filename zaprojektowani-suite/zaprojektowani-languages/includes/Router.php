<?php
namespace ZPL;

if (!defined('ABSPATH') && !defined('ZPL_CLI')) { exit; }

/** /en/, /de/ and /es/ routing: translated slugs <-> Polish source paths. */
final class Router {
    public static $lang = 'pl';
    public static $source = '/';
    public static $external = '/';
    public static $mapped = false;
    public static $excluded = false;
    private static $routes = null;
    private static $reverse = null;
    private static $legacy = null;
    private static $home = null;
    /** 1.2.0: German and Spanish routes, reverse routes and old addresses (from Tłumacz AI), per language. */
    private static $lroutes = [];
    private static $lreverse = [];
    private static $llegacy = [];

    const DEFAULT_EXCLUDE = ['/oferty/', '/briefy/'];
    /**
     * Polish pages with no English version of their own => the English page that covers them (suite 2.3.0).
     * Since 1.0.15 all of them have English routes in data/routes.php (e.g. /en/website-development/), so an
     * alias only applies again if its route is removed.
     */
    const ALIASES = [
        '/tworzenie-stron-internetowych/' => '/strony-internetowe-katowice/',
        '/tworzenie-sklepow-internetowych/' => '/sklepy-internetowe-katowice/',
        '/projektowanie-logo/' => '/logo-branding-katowice/',
        '/identyfikacja-wizualna/' => '/logo-branding-katowice/',
        // suite 2.4.0: website pages written in Polish only.
        '/strony-wordpress/' => '/strony-internetowe-katowice/',
        '/opieka-wordpress/' => '/strony-internetowe-katowice/',
        '/strona-wizytowka/' => '/strony-internetowe-katowice/',
        '/tworzenie-landing-page/' => '/strony-internetowe-katowice/',
        '/strony-internetowe-dla-kancelarii/' => '/strony-internetowe-katowice/',
        '/strony-internetowe-dla-lekarzy/' => '/strony-internetowe-katowice/',
        '/strony-internetowe-dla-deweloperow/' => '/strony-internetowe-katowice/',
        // suite 2.9.0: local page from content batch 5, Polish only.
        '/agencja-reklamowa-katowice/' => '/strony-internetowe-katowice/',
        // suite 2.9.6: the landing page post (it showed the premium shop article) redirects to the service page.
        '/sklepy-internetowe/landing-page-pod-kampanie-meta-ads/' => '/tworzenie-landing-page/',
        '/wiedza/landing-page-pod-kampanie-meta-ads/' => '/tworzenie-landing-page/',
    ];
    /** Exact public aliases audited on 2026-10-11; source and target must still be the owned route. */
    const AUDITED_LEGACY_EN = [
        '/en/kampanie-reklamowe/jak-przygotowac-strone-pod-reklamy-facebook-i-instagram/' => ['/kampanie-reklamowe-meta-ads/jak-przygotowac-strone-pod-reklamy-facebook-i-instagram/', '/en/meta-ads-campaigns/how-to-prepare-your-website-for-facebook-and-instagram-ads/'],
        '/en/wiedza/strona-internetowa-dla-firmy-co-powinna-zawierac/' => ['/strony-internetowe/strona-internetowa-dla-firmy-co-powinna-zawierac/', '/en/websites/business-website-essentials/'],
        '/en/wiedza/jak-sprawdzic-czy-strona-internetowa-dziala-skutecznie/' => ['/strony-www/jak-sprawdzic-czy-strona-internetowa-dziala-skutecznie/', '/en/websites/how-to-measure-website-effectiveness/'],
        '/en/wiedza/jak-przygotowac-brief-do-projektowania-strony-internetowej/' => ['/strony-www/jak-przygotowac-brief-do-projektowania-strony-internetowej/', '/en/websites/how-to-write-a-web-design-brief/'],
        '/en/wiedza/jak-zwiekszyc-sprzedaz-dzieki-facebook-ads/' => ['/meta-ads/jak-zwiekszyc-sprzedaz-dzieki-facebook-ads/', '/en/meta-ads/how-to-increase-sales-with-facebook-ads/'],
        '/en/wiedza/jak-mierzyc-konwersje-z-formularza-kontaktowego/' => ['/kampanie-reklamowe-meta-ads/jak-mierzyc-konwersje-z-formularza-kontaktowego/', '/en/meta-ads-campaigns/how-to-track-contact-form-conversions/'],
        '/en/wiedza-strony-internetowe-seo-i-konwersja/dlaczego-strona-ma-ruch-ale-nie-generuje-zapytan/' => ['/seo-i-konwersja/dlaczego-strona-ma-ruch-ale-nie-generuje-zapytan/', '/en/seo-and-conversion/why-your-website-gets-traffic-but-no-inquiries/'],
        '/en/wiedza/case-studies-na-stronie-firmowej-jak-pokazac-realizacje/' => ['/ux-cro-analityka/case-studies-na-stronie-firmowej-jak-pokazac-realizacje/', '/en/ux-cro-analytics/how-to-showcase-case-studies-on-your-website/'],
        '/en/wiedza/dlaczego-profesjonalne-logo-ma-znaczenie-dla-firmy/' => ['/logo-branding/dlaczego-profesjonalne-logo-ma-znaczenie-dla-firmy/', '/en/logo-branding/why-a-professional-logo-matters-for-your-business/'],
        '/en/wiedza/seo-lokalne-w-katowicach-jak-pozyskiwac-klientow-z-miasta-i-okolic/' => ['/sklepy-internetowe/seo-lokalne-w-katowicach-jak-pozyskiwac-klientow-z-miasta-i-okolic/', '/en/online-stores/local-seo-katowice-how-to-attract-customers/'],
        '/en/wiedza/ile-kosztuje-logo-dla-firmy-i-co-obejmuje-cena/' => ['/logo-branding/ile-kosztuje-logo-dla-firmy-i-co-obejmuje-cena/', '/en/logo-branding/logo-design-costs-and-whats-included/'],
        '/en/wiedza/ile-kosztuje-sklep-internetowy-woocommerce-w-2026-roku/' => ['/seo-i-konwersja/ile-kosztuje-sklep-internetowy-woocommerce-w-2026-roku/', '/en/seo-and-conversion/woocommerce-store-cost-2026/'],
        '/en/wiedza-logo-branding/ksiega-znaku-mini-brandbook-pelna-identyfikacja-wizualna-co-wybrac/' => ['/logo-branding/ksiega-znaku-mini-brandbook-pelna-identyfikacja-wizualna-co-wybrac/', '/en/logo-branding/logo-guidelines-mini-brand-book-or-full-visual-identity/'],
        '/en/wiedza-logo-branding/brandbook-co-powinien-zawierac-i-jak-go-czytac/' => ['/logo-branding/brandbook-co-powinien-zawierac-i-jak-go-czytac/', '/en/logo-branding/brand-book-essentials-and-how-to-use-it/'],
        '/en/wiedza/jakie-cechy-powinno-miec-dobre-logo-firmowe/' => ['/logo-branding/jakie-cechy-powinno-miec-dobre-logo-firmowe/', '/en/logo-branding/qualities-of-a-good-company-logo/'],
        '/en/wiedza/dlaczego-strona-ma-ruch-ale-nie-generuje-zapytan/' => ['/seo-i-konwersja/dlaczego-strona-ma-ruch-ale-nie-generuje-zapytan/', '/en/seo-and-conversion/why-your-website-gets-traffic-but-no-inquiries/'],
        '/en/wiedza/projekt-logo-firmy-od-czego-zaczac-i-czego-unikac/' => ['/logo-branding/projekt-logo-firmy-od-czego-zaczac-i-czego-unikac/', '/en/logo-branding/company-logo-design-where-to-start-and-what-to-avoid/'],
    ];

    const NEVER = '~^/(?:wp-admin|wp-login\.php|wp-json|wp-content|wp-includes|wp-cron\.php|xmlrpc\.php|feed|comments/feed|wp-sitemap|sitemap|[a-z0-9_-]+-sitemap\d*\.xml|sitemap_index\.xml|robots\.txt|favicon\.ico)(?:/|$|\?)~i';

    public static function data(): array {
        static $d = null;
        if ($d === null) {
            $file = ZPL_DIR . 'data/routes.php';
            $d = is_file($file) ? (array) include $file : [];
        }
        return $d;
    }

    /** Polish source path => English path (with /en prefix); with $lang, that language's path (/de/…, /es/…). */
    public static function routes(string $lang = 'en'): array {
        if ($lang !== 'en') { return self::lang_routes($lang); }
        if (self::$routes === null) {
            $r = (array) (self::data()['routes'] ?? []);
            $custom = get_option('zpl_routes', []);
            if (is_array($custom)) { foreach ($custom as $pl => $en) { if (is_string($pl) && is_string($en) && $en !== '') { $r[$pl] = $en; } } }
            self::$routes = (array) apply_filters('zp_en_rewrite_routes', $r);
        }
        return self::$routes;
    }

    /**
     * 1.2.0: German or Spanish routes (filter zpl_lang_routes, Tłumacz AI): Polish path => /de/… or /es/…. Only paths
     * under the language's own prefix count; the home page is always /de/ (/es/). English filters are never asked.
     */
    private static function lang_routes(string $lang): array {
        if (!Lang::ai($lang)) { return []; }
        if (!isset(self::$lroutes[$lang])) {
            $pre = '/' . $lang . '/';
            $r = ['/' => $pre];
            $extra = function_exists('apply_filters') ? apply_filters('zpl_lang_routes', [], $lang) : [];
            foreach (is_array($extra) ? $extra : [] as $pl => $tr) {
                if (is_string($pl) && is_string($tr) && $pl !== '' && $pl[0] === '/' && $pl !== '/' && strpos($tr, $pre) === 0 && $tr !== $pre) { $r[$pl] = $tr; }
            }
            self::$lroutes[$lang] = $r;
        }
        return self::$lroutes[$lang];
    }

    public static function reverse(string $lang = 'en'): array {
        if ($lang !== 'en') {
            if (!isset(self::$lreverse[$lang])) { self::$lreverse[$lang] = array_flip(self::lang_routes($lang)); }
            return self::$lreverse[$lang];
        }
        if (self::$reverse === null) { self::$reverse = array_flip(self::routes()); }
        return self::$reverse;
    }

    /** Old English URLs (previous translator, or an English address Tłumacz EN changed) => current English URL. */
    public static function legacy(string $lang = 'en'): array {
        if ($lang !== 'en') {
            if (!Lang::ai($lang)) { return []; }
            if (!isset(self::$llegacy[$lang])) {
                $pre = '/' . $lang . '/';
                $l = [];
                $extra = function_exists('apply_filters') ? apply_filters('zpl_lang_legacy', [], $lang) : [];
                foreach (is_array($extra) ? $extra : [] as $old => $new) {
                    if (is_string($old) && is_string($new) && $old !== $new && strpos($old, $pre) === 0 && strpos($new, $pre) === 0) { $l[$old] = $new; }
                }
                self::$llegacy[$lang] = $l;
            }
            return self::$llegacy[$lang];
        }
        if (self::$legacy === null) {
            $l = (array) (self::data()['legacy'] ?? []);
            $extra = function_exists('apply_filters') ? apply_filters('zpl_legacy_routes', []) : [];
            foreach (is_array($extra) ? $extra : [] as $old => $new) {
                if (is_string($old) && is_string($new) && $old !== $new && !isset($l[$old]) && strpos($old, '/en/') === 0 && strpos($new, '/en/') === 0) { $l[$old] = $new; }
            }
            self::$legacy = (array) apply_filters('zpl_resolved_legacy_routes', $l);
        }
        return self::$legacy;
    }

    public static function reset(): void {
        self::$routes = self::$reverse = self::$legacy = null;
        self::$lroutes = self::$lreverse = self::$llegacy = [];
        Lang::reset();
    }

    /** Site sub-directory ("" for root installs). */
    public static function base(): string {
        if (self::$home === null) {
            $p = (string) wp_parse_url(home_url('/'), PHP_URL_PATH);
            self::$home = rtrim($p, '/');
        }
        return self::$home;
    }

    public static function rel(string $path): string {
        $b = self::base();
        if ($b !== '' && strpos($path, $b) === 0) { $path = substr($path, strlen($b)); }
        return $path === '' ? '/' : $path;
    }

    public static function norm_path(string $p): string {
        $p = '/' . ltrim(rawurldecode($p), '/');
        $p = preg_replace('~/{2,}~', '/', $p) ?? $p;
        if ($p !== '/' && substr($p, -1) !== '/' && !preg_match('~\.[a-z0-9]{2,5}$~i', $p)) { $p .= '/'; }
        return $p;
    }

    public static function excludes(): array {
        $opt = get_option('zpl_settings', []);
        $list = isset($opt['exclude']) && is_array($opt['exclude']) ? $opt['exclude'] : self::DEFAULT_EXCLUDE;
        return array_values(array_filter(array_map(static function ($x) { $x = trim((string) $x); return $x === '' ? '' : '/' . trim($x, '/') . '/'; }, $list)));
    }

    public static function is_excluded(string $plPath): bool {
        foreach (self::excludes() as $pre) { if (strpos($plPath, $pre) === 0 || $plPath . '/' === $pre) { return true; } }
        return false;
    }

    public static function never(string $path): bool { return (bool) preg_match(self::NEVER, $path); }

    /**
     * Resolve an external (site-relative) path.
     * Returns [lang, source, mapped, redirect|null].
     */
    public static function resolve(string $path, string $query = '', bool $publicRequest = false): array {
        // Redirect only these literal public paths, without preview/private/query context.
        // This precedes reverse matching so an old alias cannot render a duplicate English page.
        if ($publicRequest && $query === '' && isset(self::AUDITED_LEGACY_EN[$path])) {
            [$source, $target] = self::AUDITED_LEGACY_EN[$path];
            if ((self::routes()[$source] ?? null) === $target && !self::is_excluded($source)) {
                return ['en', '', false, $target];
            }
        }
        $path = self::norm_path($path);
        $l = Lang::of_path($path);
        if ($l === 'pl') {
            return ['pl', $path, isset(self::routes()[$path]) || self::paged_en($path) !== null, null];
        }
        if ($l !== 'en') { return self::resolve_ai($l, $path); }
        $rev = self::reverse();
        if (isset($rev[$path])) { return ['en', $rev[$path], true, null]; }
        if (preg_match('~^(.*/)page/(\d+)/$~', $path, $m) && isset($rev[$m[1]])) {
            return ['en', $rev[$m[1]] . 'page/' . $m[2] . '/', true, null];
        }
        $legacy = self::legacy();
        if (isset($legacy[$path])) { return ['en', '', false, $legacy[$path]]; }
        if (preg_match('~^(.*/)page/(\d+)/$~', $path, $m) && isset($legacy[$m[1]])) {
            return ['en', '', false, $legacy[$m[1]] . 'page/' . $m[2] . '/'];
        }
        $pl = substr($path, 3);
        if (isset(self::routes()[$pl]) && self::routes()[$pl] !== $path) { return ['en', '', false, self::routes()[$pl]]; }
        if (isset(self::ALIASES[$pl]) && !isset(self::routes()[$pl])) { return ['en', '', false, self::en_path($pl)]; }
        $paged = self::paged_en($pl);
        if ($paged !== null && $paged !== $path) { return ['en', '', false, $paged]; }
        return ['en', $pl, false, null];
    }

    /**
     * 1.2.0: a German or Spanish address. Its own route (mapped), an old address of it (301), the Polish address with
     * the prefix for a page that has a translated address (301 to it), else the Polish page under the prefix (not
     * mapped: noindex, no hreflang, until Tłumacz AI gives it its own address).
     */
    private static function resolve_ai(string $l, string $path): array {
        $rev = self::reverse($l);
        if (isset($rev[$path])) { return [$l, $rev[$path], true, null]; }
        if (preg_match('~^(.*/)page/(\d+)/$~', $path, $m) && isset($rev[$m[1]])) {
            return [$l, $rev[$m[1]] . 'page/' . $m[2] . '/', true, null];
        }
        $legacy = self::legacy($l);
        if (isset($legacy[$path])) { return [$l, '', false, $legacy[$path]]; }
        if (preg_match('~^(.*/)page/(\d+)/$~', $path, $m) && isset($legacy[$m[1]])) {
            return [$l, '', false, $legacy[$m[1]] . 'page/' . $m[2] . '/'];
        }
        $pl = substr($path, 3);
        $routes = self::routes($l);
        if (isset($routes[$pl]) && $routes[$pl] !== $path) { return [$l, '', false, $routes[$pl]]; }
        if (isset(self::ALIASES[$pl]) && !isset($routes[$pl]) && isset($routes[self::ALIASES[$pl]])) { return [$l, '', false, $routes[self::ALIASES[$pl]]]; }
        $paged = self::paged($pl, $l);
        if ($paged !== null && $paged !== $path) { return [$l, '', false, $paged]; }
        return [$l, $pl, false, null];
    }

    /** 1.2.0: "/x/page/2/" of a page with an address in $lang => that address + page/2/. */
    private static function paged(string $pl, string $lang): ?string {
        if ($lang === 'en') { return self::paged_en($pl); }
        $r = self::routes($lang);
        if (preg_match('~^(.*/)page/(\d+)/$~', $pl, $m) && isset($r[$m[1]])) { return $r[$m[1]] . 'page/' . $m[2] . '/'; }
        return null;
    }

    /**
     * 3.1.1: the English address for a path that is one without its /en/ ("/websites-for-law-firms/", requested by Google
     * on 14.10), or null. Runtime asks only when WordPress has no page there, so a Polish page at that address always wins.
     */
    public static function bare_en(string $path): ?string {
        $path = self::norm_path($path);
        if ($path === '/' || strpos($path, '/en/') === 0) { return null; }
        $en = '/en' . $path;
        $rev = self::reverse();
        // 3.2.0: only an English address without its /en/. When the English page is this very Polish address's own
        // version (the same slug in both languages), the Polish page itself is missing: WordPress answers (404, old
        // slug), and Tłumacz EN retires the page instead of reading "Polski adres przekierowuje na /en/…" forever.
        if (isset($rev[$en])) { return $rev[$en] === $path || self::is_excluded((string) $rev[$en]) ? null : $en; }
        $legacy = self::legacy();
        if (!isset($legacy[$en])) { return null; }
        $to = (string) $legacy[$en];
        return ($rev[$to] ?? null) === $path ? null : $to;
    }

    private static function paged_en(string $pl): ?string {
        if (preg_match('~^(.*/)page/(\d+)/$~', $pl, $m) && isset(self::routes()[$m[1]])) {
            return self::routes()[$m[1]] . 'page/' . $m[2] . '/';
        }
        return null;
    }

    /** The Polish path has an English (or $lang) address of its own (route, alias or paged route). */
    public static function has_route(string $pl, string $lang = 'en'): bool {
        $pl = self::norm_path($pl);
        if ($lang === 'pl') { return true; }
        $r = self::routes($lang);
        return isset($r[$pl]) || (isset(self::ALIASES[$pl]) && isset($r[self::ALIASES[$pl]])) || self::paged($pl, $lang) !== null;
    }

    /**
     * English addresses for ZP Suite's HTML cache (filter zp_html_cache_policy_paths, ZP Suite 3.1.1), each with the version
     * of its English text: ['/en/x/' => 'en:<8 hex>']. Only for the Polish addresses the cache lists ($pl: path => true),
     * with the test the page itself uses: its own route that leads back to it (no alias, no redirect) and Dict::has_page
     * (hreflang, no noindex). The version changes whenever the page's English text can change (plugin versions, and
     * through zpl_cache_versions Tłumacz EN's menu and page translations and the dollar rate); after
     * do_action('zp_html_cache_refresh') the cache stops serving the old copies of just the pages whose version changed.
     * @param mixed $en
     * @param mixed $pl
     */
    public static function cache_paths($en, $pl): array {
        $out = is_array($en) ? $en : [];
        $routes = self::routes();
        $rev = self::reverse();
        $ok = [];
        foreach (is_array($pl) ? $pl : [] as $k => $p) {
            $p = self::norm_path(is_string($k) ? $k : (string) $p);
            $e = $routes[$p] ?? null;
            if (!is_string($e) || strpos($e, '/en/') !== 0 || ($rev[$e] ?? null) !== $p || isset(self::AUDITED_LEGACY_EN[$e]) || self::is_excluded($p) || !Dict::has_page($p)) { continue; }
            $ok[$p] = $e;
        }
        if (!$ok) { return $out; }
        // One version for every page ('*') and one per Polish address, read by the modules without loading any translation.
        $ver = (array) apply_filters('zpl_cache_versions', [], array_keys($ok));
        $all = (defined('ZP_SUITE_VERSION') ? ZP_SUITE_VERSION : '') . '|' . ZPL_VERSION . '|' . (string) ($ver['*'] ?? '');
        foreach ($ok as $p => $e) { $out[$e] = 'en:' . substr(hash('sha256', $all . '|' . $p . '|' . (string) ($ver[$p] ?? '')), 0, 8); }
        return $out;
    }

    /** English path for a Polish source path (fallback: /en + path). */
    public static function en_path(string $pl): string {
        $pl = self::norm_path($pl);
        if (isset(self::routes()[$pl])) { return self::routes()[$pl]; }
        if (isset(self::ALIASES[$pl]) && isset(self::routes()[self::ALIASES[$pl]])) { return self::routes()[self::ALIASES[$pl]]; }
        $paged = self::paged_en($pl);
        return $paged ?? ('/en' . $pl);
    }

    /** 1.2.0: the address of a Polish source path in $lang (fallback: the prefix + the Polish path). */
    public static function path_for(string $pl, string $lang): string {
        $pl = self::norm_path($pl);
        if ($lang === 'pl' || !Lang::foreign($lang)) { return $pl; }
        if ($lang === 'en') { return self::en_path($pl); }
        $r = self::routes($lang);
        if (isset($r[$pl])) { return $r[$pl]; }
        if (isset(self::ALIASES[$pl]) && isset($r[self::ALIASES[$pl]])) { return $r[self::ALIASES[$pl]]; }
        return self::paged($pl, $lang) ?? ('/' . $lang . $pl);
    }

    /** Localize a URL/href for $lang. Leaves foreign, asset, admin and excluded URLs alone. */
    public static function url(string $url, string $lang = 'en'): string {
        $raw = trim($url);
        if ($raw === '' || $raw[0] === '#' || preg_match('~^(?:mailto|tel|sms|javascript|data|whatsapp|viber):~i', $raw)) { return $url; }
        $home = home_url('/');
        $hp = wp_parse_url($home);
        $p = wp_parse_url($raw);
        if ($p === false) { return $url; }
        if (isset($p['host'])) {
            $host = strtolower($p['host']);
            $hh = strtolower($hp['host'] ?? '');
            if ($host !== $hh && $host !== 'www.' . $hh && 'www.' . $host !== $hh) { return $url; }
        } elseif (isset($p['scheme'])) {
            return $url;
        } elseif (($raw[0] ?? '') !== '/' && ($raw[0] ?? '') !== '?') {
            return $url; // relative paths like "foo/" are rare; leave them
        }
        $path = $p['path'] ?? '/';
        if ($path === '') { $path = '/'; }
        $rel = self::rel($path);
        if (self::never($rel) || preg_match('~\.[a-z0-9]{2,5}$~i', $rel) && !preg_match('~\.html?$~i', $rel)) { return $url; }
        $rel = self::norm_path($rel);
        if (Lang::foreign($lang)) {
            // Already an address of some language (the content links to /en/ on purpose, or the menu was localized).
            if (Lang::of_path($rel) !== 'pl') { return $url; }
            if (self::is_excluded($rel)) { return $url; }
            // No version in this language yet: the link keeps the Polish address instead of a Polish page under /en/.
            if (!self::has_route($rel, $lang)) { return $url; }
            $new = self::path_for($rel, $lang);
        } else {
            if (Lang::of_path($rel) === 'pl') { return $url; }
            $r = self::resolve($rel);
            $new = $r[3] !== null ? self::resolve($r[3])[1] : $r[1];
            if ($new === '') { return $url; }
        }
        $out = self::base() . $new;
        if (isset($p['host'])) { $out = ($p['scheme'] ?? 'https') . '://' . $p['host'] . (isset($p['port']) ? ':' . $p['port'] : '') . $out; }
        if (isset($p['query'])) { $out .= '?' . $p['query']; }
        if (isset($p['fragment'])) { $out .= '#' . $p['fragment']; }
        return $out;
    }

    public static function abs(string $path): string { return home_url($path); }
}
