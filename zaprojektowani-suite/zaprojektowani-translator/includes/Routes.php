<?php
namespace ZPTE;

if (!defined('ABSPATH')) { exit; }

/**
 * English addresses. Every Polish page with an English version has exactly one English address, built from
 * English words only: /en/<english category>/<english slug>/. An address that changes keeps working: the old
 * one answers with a permanent redirect (301) to the new one.
 *
 * Public routes live in two small options read on every request: zpte2_routes (autoloaded, Polish => English)
 * and zpte2_legacy (old English => current English). The language module asks for them with the filters
 * zp_en_rewrite_routes and zpl_legacy_routes (Serve.php).
 */
final class Routes {
    const OPTION = 'zpte2_routes';
    const LEGACY = 'zpte2_legacy';

    /** Category and section segments of Polish addresses that already have an English name on this site. */
    const SEGMENTS = [
        'strony-internetowe' => 'websites',
        'strony-www' => 'websites',
        'sklepy-internetowe' => 'online-stores',
        'seo-i-konwersja' => 'seo-and-conversion',
        'seo' => 'seo',
        'logo-branding' => 'logo-branding',
        'kampanie-reklamowe' => 'advertising-campaigns',
        'kampanie-reklamowe-meta-ads' => 'meta-ads-campaigns',
        'meta-ads' => 'meta-ads',
        'ux-cro-analityka' => 'ux-cro-analytics',
        'poradniki' => 'guides',
        'seo-content-marketing' => 'seo-content-marketing',
        'reklamy' => 'advertising',
        'wiedza' => 'insights',
        'bez-kategorii' => 'uncategorized',
        'category' => 'category',
    ];

    /**
     * English addresses chosen for the three posts that the previous translator published under Polish
     * addresses (13.10.2026). Their old addresses redirect here.
     */
    const FIXED = [
        '/logo-branding/gadzety-z-logo-firmy-rozne-kolory-porownanie-probek/' => '/en/logo-branding/branded-merchandise-logo-color-differences/',
        '/strony-internetowe/strona-firmowa-na-facebooku-czy-wlasna-witryna-podzial-zadan/' => '/en/websites/facebook-page-vs-business-website/',
        '/logo-branding/logo-firmy-odziezowej-marka-i-kolekcje-na-metkach-oraz-w-sklepie/' => '/en/logo-branding/clothing-brand-logo-labels-and-collections/',
    ];

    private static $routes = null;

    /** Public routes of this module: Polish path => English path. */
    public static function all(): array {
        if (self::$routes === null) {
            $o = get_option(self::OPTION, []);
            self::$routes = is_array($o) && is_array($o['r'] ?? null) ? $o['r'] : [];
        }
        return self::$routes;
    }

    public static function version(): int {
        $o = get_option(self::OPTION, []);
        return is_array($o) ? (int) ($o['v'] ?? 0) : 0;
    }

    /** Old English address => current English address. */
    public static function legacy(): array {
        $o = get_option(self::LEGACY, []);
        return is_array($o) ? $o : [];
    }

    /** Rebuild the public route list from the pages table (after any address change). */
    public static function rebuild(): void {
        global $wpdb;
        $rows = $wpdb->get_results('SELECT path, en_path FROM ' . Store::table('pages') . " WHERE en_path <> '' AND status <> 'off' ORDER BY id ASC", ARRAY_N);
        $r = [];
        $taken = [];
        foreach ((array) $rows as $row) {
            [$pl, $en] = [(string) $row[0], (string) $row[1]];
            if (!self::valid_en($en) || isset($taken[$en]) || self::external($pl)) { continue; }
            $r[$pl] = $en;
            $taken[$en] = true;
        }
        update_option(self::OPTION, ['v' => time(), 'r' => $r], true);
        self::$routes = $r;
        self::flush();
    }

    /** Remember that $old (English) now lives at $new; earlier redirects to $old follow along. */
    public static function moved(string $old, string $new): void {
        if ($old === '' || $old === $new || !self::valid_en($old) || !self::valid_en($new)) { return; }
        $l = self::legacy();
        foreach ($l as $k => $v) { if ($v === $old) { $l[$k] = $new; } }
        unset($l[$new]);
        $l[$old] = $new;
        update_option(self::LEGACY, $l, false);
        self::flush();
    }

    public static function flush(): void {
        if (class_exists('ZPL\\Router')) { \ZPL\Router::reset(); }
        if (class_exists('ZPL\\Dict')) { \ZPL\Dict::flush(); }
        if (class_exists('ZPL\\Sitemap') && method_exists('ZPL\\Sitemap', 'forget')) { \ZPL\Sitemap::forget(); }
        wp_cache_delete('routes', 'zpte2');
    }

    /**
     * The address comes from the language module itself (its shipped data or its routes option, which the owner
     * can edit in Języki PL/EN → Adresy URL): this module never changes it.
     */
    public static function external(string $pl): bool {
        $data = \ZPL\Router::data();
        if (isset($data['routes'][$pl])) { return true; }
        $custom = get_option('zpl_routes', []);
        return is_array($custom) && isset($custom[$pl]) && is_string($custom[$pl]) && $custom[$pl] !== '';
    }

    public static function valid_en(string $en): bool {
        return (bool) preg_match('~^/en/(?:[a-z0-9]+(?:-[a-z0-9]+)*/)*$~', $en);
    }

    /** Last segment of a path ('' for the home page). */
    public static function last(string $path): string {
        $parts = array_values(array_filter(explode('/', trim($path, '/')), 'strlen'));
        return $parts ? (string) end($parts) : '';
    }

    /** The English address still contains Polish words (an earlier translator's fallback). */
    public static function polish(string $en, string $pl = ''): bool {
        if (strpos($en, '/en/') !== 0) { return false; }
        $segs = array_values(array_filter(explode('/', trim(substr($en, 3), '/')), 'strlen'));
        foreach ($segs as $s) {
            if (isset(self::SEGMENTS[$s]) && self::SEGMENTS[$s] !== $s) { return true; }
            if (Translator::polish_slug($s) || preg_match('~[^a-z0-9-]~', $s)) { return true; }
        }
        return false;
    }

    /**
     * English parent path for a Polish parent ('/strony-internetowe/' => '/en/websites/'), learned from the routes
     * the site already has, then from the fixed segment names. '' when a segment has no English name yet.
     */
    public static function parent(string $parent): string {
        if ($parent === '/' || $parent === '') { return '/en/'; }
        $routes = \ZPL\Router::routes();
        $count = [];
        foreach ($routes as $pl => $en) {
            $pl = (string) $pl;
            if ($pl === $parent || strpos($pl, $parent) !== 0 || trailingslashit(dirname($pl)) !== $parent) { continue; }
            $ep = trailingslashit(dirname((string) $en));
            if ($ep === '/en/' || strpos($ep, '/en/') !== 0 || self::polish($ep, $parent)) { continue; }
            $count[$ep] = ($count[$ep] ?? 0) + 1;
        }
        if ($count) { arsort($count); return (string) array_key_first($count); }
        $out = '/en/';
        foreach (array_filter(explode('/', trim($parent, '/')), 'strlen') as $seg) {
            $en = self::SEGMENTS[$seg] ?? (string) (self::learned_segments()[$seg] ?? '');
            if ($en === '') { return ''; }
            $out .= $en . '/';
        }
        return $out;
    }

    /** Polish segments of a parent path that have no English name yet (asked from the model with the slug). */
    public static function unknown_segments(string $parent): array {
        if (self::parent($parent) !== '') { return []; }
        $out = [];
        foreach (array_filter(explode('/', trim($parent, '/')), 'strlen') as $seg) {
            if (!isset(self::SEGMENTS[$seg]) && !isset(self::learned_segments()[$seg])) { $out[] = $seg; }
        }
        return $out;
    }

    /** English segment names given by the model earlier (option zpte2_segments). */
    public static function learned_segments(): array {
        $o = get_option('zpte2_segments', []);
        return is_array($o) ? $o : [];
    }

    public static function learn_segments(array $map): void {
        if (!$map) { return; }
        $o = self::learned_segments();
        foreach ($map as $pl => $en) {
            if (is_string($pl) && is_string($en) && $en !== '' && !isset(self::SEGMENTS[$pl])) { $o[$pl] = $en; }
        }
        update_option('zpte2_segments', $o, false);
    }

    /** Parent path of a Polish path ('/a/b/' => '/a/'). */
    public static function parent_of(string $pl): string {
        $d = trailingslashit(dirname(untrailingslashit($pl)));
        return $d === '//' || $d === '\\/' ? '/' : $d;
    }

    /**
     * English address for a Polish page from an English slug. Categories keep the /en/category/ base.
     * '' when the parent has no English name yet.
     */
    public static function build(string $pl, string $slug): string {
        $slug = Translator::sanitize_slug($slug);
        if ($slug === '') { return ''; }
        $parent = self::parent(self::parent_of($pl));
        if ($parent === '') { return ''; }
        return self::free($parent . $slug . '/', $pl);
    }

    /** First free English address (adds -2, -3… when another Polish page already uses it). */
    public static function free(string $en, string $pl): string {
        $reverse = \ZPL\Router::reverse();
        $legacy = \ZPL\Router::legacy();
        $base = rtrim($en, '/');
        $try = $base . '/';
        for ($i = 2; (isset($reverse[$try]) && $reverse[$try] !== $pl) || isset($legacy[$try]); $i++) {
            $try = $base . '-' . $i . '/';
        }
        return $try;
    }

    /**
     * Give a page its English address. A different earlier address (of this page) redirects to the new one.
     * Returns false when the address is taken by another page.
     */
    public static function assign(int $page_id, string $en): bool {
        $row = Store::page($page_id);
        if (!$row || !self::valid_en($en)) { return false; }
        $pl = (string) $row->path;
        $reverse = \ZPL\Router::reverse();
        if (isset($reverse[$en]) && $reverse[$en] !== $pl) { return false; }
        $old = (string) $row->en_path;
        if ($old === '') { $old = (string) (\ZPL\Router::routes()[$pl] ?? ''); }
        Store::page_update($page_id, ['en_path' => $en]);
        self::rebuild();
        if ($old !== '' && $old !== $en) { self::moved($old, $en); }
        return true;
    }
}
