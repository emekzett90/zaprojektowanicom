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
            self::$routes = $r;
        }
        return self::$routes;
    }

    public static function reverse(): array {
        if (self::$reverse === null) { self::$reverse = array_flip(self::routes()); }
        return self::$reverse;
    }

    /** Old English URLs (previous translator) => current English URL. */
    public static function legacy(): array {
        if (self::$legacy === null) { self::$legacy = (array) (self::data()['legacy'] ?? []); }
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
    public static function resolve(string $path): array {
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
