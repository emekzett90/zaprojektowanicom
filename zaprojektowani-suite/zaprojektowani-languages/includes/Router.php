<?php
namespace ZPL;

if (!defined('ABSPATH') && !defined('ZPL_CLI')) { exit; }

/** /en/ routing: translated slugs <-> Polish source paths. */
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

    /** Polish source path => English path (with /en prefix). */
    public static function routes(): array {
        if (self::$routes === null) {
            $r = (array) (self::data()['routes'] ?? []);
            $custom = get_option('zpl_routes', []);
            if (is_array($custom)) { foreach ($custom as $pl => $en) { if (is_string($pl) && is_string($en) && $en !== '') { $r[$pl] = $en; } } }
            self::$routes = (array) apply_filters('zp_en_rewrite_routes', $r);
        }
        return self::$routes;
    }

    public static function reverse(): array {
        if (self::$reverse === null) { self::$reverse = array_flip(self::routes()); }
        return self::$reverse;
    }

    /** Old English URLs (previous translator, or an English address Tłumacz EN changed) => current English URL. */
    public static function legacy(): array {
        if (self::$legacy === null) {
            $l = (array) (self::data()['legacy'] ?? []);
            $extra = function_exists('apply_filters') ? apply_filters('zpl_legacy_routes', []) : [];
            foreach (is_array($extra) ? $extra : [] as $old => $new) {
                if (is_string($old) && is_string($new) && $old !== $new && !isset($l[$old]) && strpos($old, '/en/') === 0 && strpos($new, '/en/') === 0) { $l[$old] = $new; }
            }
            self::$legacy = $l;
        }
        return self::$legacy;
    }

    public static function reset(): void { self::$routes = self::$reverse = self::$legacy = null; }

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
        if ($path !== '/en/' && strpos($path, '/en/') !== 0) {
            return ['pl', $path, isset(self::routes()[$path]) || self::paged_en($path) !== null, null];
        }
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

    private static function paged_en(string $pl): ?string {
        if (preg_match('~^(.*/)page/(\d+)/$~', $pl, $m) && isset(self::routes()[$m[1]])) {
            return self::routes()[$m[1]] . 'page/' . $m[2] . '/';
        }
        return null;
    }

    /** The Polish path has an English address of its own (route, alias or paged route). */
    public static function has_route(string $pl): bool {
        $pl = self::norm_path($pl);
        $r = self::routes();
        return isset($r[$pl]) || (isset(self::ALIASES[$pl]) && isset($r[self::ALIASES[$pl]])) || self::paged_en($pl) !== null;
    }

    /** English path for a Polish source path (fallback: /en + path). */
    public static function en_path(string $pl): string {
        $pl = self::norm_path($pl);
        if (isset(self::routes()[$pl])) { return self::routes()[$pl]; }
        if (isset(self::ALIASES[$pl]) && isset(self::routes()[self::ALIASES[$pl]])) { return self::routes()[self::ALIASES[$pl]]; }
        $paged = self::paged_en($pl);
        return $paged ?? ('/en' . $pl);
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
        if ($lang === 'en') {
            if ($rel === '/en/' || strpos($rel, '/en/') === 0) { return $url; }
            if (self::is_excluded($rel)) { return $url; }
            // No English version yet: the link keeps the Polish address instead of an /en/ page in Polish.
            if (!self::has_route($rel)) { return $url; }
            $new = self::en_path($rel);
        } else {
            if ($rel !== '/en/' && strpos($rel, '/en/') !== 0) { return $url; }
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
