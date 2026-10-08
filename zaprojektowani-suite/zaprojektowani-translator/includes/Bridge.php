<?php
namespace ZPTE;

if (!defined('ABSPATH')) { exit; }

/**
 * Link with the language module (zaprojektowani-languages, namespace ZPL):
 *  - zpl_dictionary: machine translations, asked for last (shipped dictionary and manual corrections win);
 *  - zpl_has_page:   a translated new page counts as having an English version (hreflang, no noindex);
 *  - zpl_sitemap_pages: translated new pages go into the English sitemaps (/english-page-sitemap.xml, /english-post-sitemap.xml);
 *  - English URLs are saved as ordinary routes in zpl_routes (visible in ZP Suite → Języki PL/EN → Adresy URL).
 */
final class Bridge {
    const INDEX = 'zpte_index';

    /** Switched on while the module checks what the shipped dictionary already covers. */
    public static $off = false;
    private static $maps = [];

    public static function ready(): bool {
        return defined('ZPL_VERSION') && class_exists('ZPL\\Dict') && class_exists('ZPL\\Page') && class_exists('ZPL\\Router') && class_exists('ZPL\\Html');
    }

    /** The language module asks for the filters below (Suite with zaprojektowani-languages 1.0.13+). */
    public static function hooks_ready(): bool {
        return self::ready() && defined('ZPL_API') && (int) ZPL_API >= 1;
    }

    public static function boot(): void {
        if (!self::ready()) { return; }
        add_filter('zpl_dictionary', [self::class, 'dictionary'], 10, 3);
        add_filter('zpl_has_page', [self::class, 'has_page'], 10, 2);
        add_filter('zpl_sitemap_pages', [self::class, 'sitemap_pages']);
    }

    // ---------------------------------------------------------------- filters

    /** @param mixed $dict */
    public static function dictionary($dict, $lang, $source) {
        if (self::$off) { return $dict; }
        $map = self::map((string) $source);
        if (!$map) { return $dict; }
        if ($lang === 'pl') {
            $rev = [];
            foreach ($map as $pl => $en) { $rev[\ZPL\Html::norm((string) $en)] = (string) $pl; }
            $map = $rev;
        }
        return is_array($dict) && $dict ? $dict + $map : $map;
    }

    /** @param mixed $has */
    public static function has_page($has, $source) {
        if ($has || self::$off) { return $has; }
        $idx = self::index();
        return isset($idx['new'][md5(\ZPL\Router::norm_path((string) $source))]);
    }

    /** @param mixed $pages */
    public static function sitemap_pages($pages) {
        $pages = is_array($pages) ? $pages : [];
        foreach ((array) (self::index()['sitemap'] ?? []) as $p) { $pages[] = (string) $p; }
        return $pages;
    }

    /** Translations for one page (cached per request and in the object cache). */
    public static function map(string $source): array {
        $source = \ZPL\Router::norm_path($source);
        $preview = self::preview();
        $h = md5($source);
        $ck = $h . ($preview ? ':p' : '');
        if (isset(self::$maps[$ck])) { return self::$maps[$ck]; }
        $idx = self::index();
        if (!$preview && empty($idx['global']) && empty($idx['pages'][$h])) { return self::$maps[$ck] = []; }
        $cacheKey = 'map:' . $ck . ':' . (int) ($idx['v'] ?? 0);
        $m = $preview ? false : wp_cache_get($cacheKey, 'zpte');
        if (!is_array($m)) {
            $m = Store::map_for($h, $preview);
            if (!$preview) { wp_cache_set($cacheKey, $m, 'zpte', HOUR_IN_SECONDS); }
        }
        return self::$maps[$ck] = $m;
    }

    /** Administrator preview of a draft: English URL + ?zpte_preview=1. */
    private static function preview(): bool {
        return isset($_GET['zpte_preview']) && function_exists('current_user_can') && did_action('set_current_user') && current_user_can('manage_options');
    }

    /** Small autoloaded summary so pages without machine translations cost no query. */
    public static function index(): array {
        $i = get_option(self::INDEX, []);
        return is_array($i) ? $i : [];
    }

    public static function rebuild_index(): void {
        $pages = [];
        foreach (Store::published_hashes() as $h) { $pages[$h] = 1; }
        $new = [];
        $sitemap = [];
        foreach (Store::paths(['status' => 'published', 'kind' => ['new', 'older']]) as $row) {
            $new[(string) $row->path_hash] = 1;
            $sitemap[] = (string) $row->path;
        }
        update_option(self::INDEX, ['v' => time(), 'global' => Store::has_globals() ? 1 : 0, 'pages' => $pages, 'new' => $new, 'sitemap' => $sitemap], true);
        self::$maps = [];
        if (class_exists('ZPL\\Dict')) { \ZPL\Dict::flush(); }
    }

    // ------------------------------------------------------------------ paths

    /** Site-relative path of a published post, normalized like the language module does it ('' if none). */
    public static function path_of_post(int $post_id): string {
        $url = get_permalink($post_id);
        if (!is_string($url) || $url === '') { return ''; }
        $p = (string) wp_parse_url($url, PHP_URL_PATH);
        if ($p === '' || wp_parse_url($url, PHP_URL_QUERY)) { return ''; } // plain permalinks (?p=1) have no path of their own
        return \ZPL\Router::norm_path(\ZPL\Router::rel($p));
    }

    /** Why a Polish path gets no English version of its own ('' = it can have one). */
    public static function ineligible(string $path): string {
        if ($path === '' || $path === '/en/' || strpos($path, '/en/') === 0) { return 'adres angielski'; }
        if (\ZPL\Router::never($path)) { return 'adres techniczny'; }
        if (\ZPL\Router::is_excluded($path)) { return 'ścieżka wyłączona w Języki PL/EN'; }
        // A temporary English fallback must not exclude a real Polish page from
        // translation. Source::fetch still rejects redirected/deleted Polish URLs;
        // once a public page is translated, its own route overrides the fallback.
        if (strpos($path, '/dziekujemy') === 0 || strpos($path, '/page/') !== false) { return 'strona pomocnicza'; }
        // Pages made for one client (questionnaires, presentations) stay out of Google, so no English version.
        if (function_exists('zp_seo_plan_is_private_path') && zp_seo_plan_is_private_path($path)) { return 'strona dla klienta (noindex)'; }
        return '';
    }

    /** The shipped dictionary already has an English version of this page (route + page dictionary). */
    public static function shipped(string $path): bool {
        self::$off = true;
        $has = isset(\ZPL\Router::routes()[$path]) && \ZPL\Dict::has_page($path);
        self::$off = false;
        return $has;
    }

    // ----------------------------------------------------------------- routes

    public static function route(string $pl): string {
        return (string) (\ZPL\Router::routes()[$pl] ?? '');
    }

    /** English parent path for a Polish parent, learned from the existing routes ('' if unknown). */
    public static function learned_parent(string $parent): string {
        $routes = \ZPL\Router::routes();
        $count = [];
        foreach ($routes as $pl => $en) {
            if ($pl === $parent || strpos((string) $pl, $parent) !== 0) { continue; }
            if (trailingslashit(dirname((string) $pl)) !== $parent) { continue; }
            $ep = trailingslashit(dirname((string) $en));
            if ($ep === '/en/' || strpos($ep, '/en/') !== 0) { continue; }
            $count[$ep] = ($count[$ep] ?? 0) + 1;
        }
        if ($count) { arsort($count); return (string) array_key_first($count); }
        if (isset($routes[$parent])) { return (string) $routes[$parent]; }
        return '';
    }

    /** First free English path (adds -2, -3… when another Polish page uses it). */
    public static function free_en_path(string $en, string $pl): string {
        $reverse = \ZPL\Router::reverse();
        $base = rtrim($en, '/');
        $try = $base . '/';
        for ($i = 2; isset($reverse[$try]) && $reverse[$try] !== $pl; $i++) { $try = $base . '-' . $i . '/'; }
        return $try;
    }

    public static function add_route(string $pl, string $en): void {
        $custom = get_option('zpl_routes', []);
        $custom = is_array($custom) ? $custom : [];
        $custom[$pl] = $en;
        update_option('zpl_routes', $custom, true);
        \ZPL\Router::reset();
    }

    /** Remove a route only if it is still the one this module created. */
    public static function remove_route(string $pl, string $en): void {
        $custom = get_option('zpl_routes', []);
        if (!is_array($custom) || !isset($custom[$pl]) || $custom[$pl] !== $en) { return; }
        unset($custom[$pl]);
        update_option('zpl_routes', $custom, true);
        \ZPL\Router::reset();
    }

    // ------------------------------------------------------------------ cache

    /** LiteSpeed Cache (and other page caches listening to the same hooks) must forget both language versions. */
    public static function purge(array $paths, int $post_id = 0): void {
        $urls = [];
        foreach ($paths as $p) {
            $p = (string) $p;
            if ($p === '') { continue; }
            $urls[] = home_url($p);
        }
        if (method_exists('ZPL\\Sitemap', 'urls')) { $urls = array_merge($urls, array_values(\ZPL\Sitemap::urls())); \ZPL\Sitemap::forget(); }
        $urls[] = home_url('/sitemap_index.xml');
        foreach (array_unique($urls) as $u) { do_action('litespeed_purge_url', $u); }
        if ($post_id > 0) { do_action('litespeed_purge_post', $post_id); clean_post_cache($post_id); }
        wp_cache_delete('zpte', 'zpte');
    }

    /** English URL (absolute) for a Polish path. */
    public static function en_url(string $pl): string {
        return home_url(\ZPL\Router::en_path($pl));
    }
}
