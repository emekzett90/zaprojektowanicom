<?php
namespace ZPTE;

if (!defined('ABSPATH')) { exit; }

/**
 * What English visitors get. The language module (zaprojektowani-languages) renders every /en/ page; this class
 * hands it the translations and addresses of Tłumacz EN through its filters:
 *  - zp_en_rewrite_routes     Polish => English addresses (Routes::all());
 *  - zpl_legacy_routes        old English addresses => current ones (301);
 *  - zpl_priority_dictionary  [site-wide translations, translations of this page], above the shipped dictionary;
 *  - zpl_has_page             the page has an English version (hreflang, no noindex, sitemap);
 *  - zpl_sitemap_pages        pages listed in the English sitemaps;
 *  - REST zpl/v1/approved-page the same translations for the no-reload switch in the browser.
 * Hooks are added when the module file loads: the language module reads routes at plugins_loaded (first).
 */
final class Serve {
    /**
     * Words of the menu, footer and other parts shown on every page. Same as the earlier site dictionary, with
     * "Wiedza" and "Studio wyceny" named like their English addresses (/en/insights/, /en/quote-studio/).
     */
    const SITE_TERMS = [
        'Wiedza' => 'Insights', 'Studio wyceny' => 'Quote Studio', 'Studio Wyceny' => 'Quote Studio', 'Przejdź do Studio Wyceny' => 'Go to the Quote Studio',
        'Strona główna' => 'Home', 'Usługi' => 'Services', 'Realizacje' => 'Our work', 'O nas' => 'About us', 'Kontakt' => 'Contact', 'Menu główne' => 'Main menu',
        'Strony internetowe' => 'Websites', 'Sklepy internetowe' => 'Online stores', 'Strony & sklepy' => 'Websites & stores', 'Strony i sklepy' => 'Websites & stores', 'Strony WordPress' => 'WordPress websites',
        'Logo & branding' => 'Logo & branding', 'Logo i branding' => 'Logo & branding', 'Identyfikacja wizualna' => 'Visual identity', 'Projekt logo' => 'Logo design', 'Brandbook' => 'Brand book', 'Start marki' => 'Brand launch',
        'Kampanie reklamowe' => 'Advertising campaigns', 'Pozycjonowanie stron' => 'SEO', 'Wszystkie usługi' => 'All services', 'Zobacz wszystkie' => 'View all', 'Zobacz realizacje' => 'View our work',
        'Szukaj' => 'Search', 'Wyszukiwarka' => 'Search', 'Szukaj w Zaprojektowani' => 'Search Zaprojektowani', 'Zamknij wyszukiwarkę' => 'Close search', 'Otwórz menu' => 'Open menu', 'Zamknij menu' => 'Close menu',
        'Polityka prywatności' => 'Privacy policy', 'Polityka cookies' => 'Cookie policy', 'Najczęściej zadawane pytania' => 'Frequently asked questions', 'Dołącz do nas' => 'Join us', 'Porozmawiajmy' => 'Let’s talk',
        'Rano' => 'Morning', 'Po południu' => 'Afternoon', 'Wieczorem' => 'Evening',
    ];
    const CACHE_GROUP = 'zpte2';

    private static $pageMaps = [];
    private static $shared = null;
    private static $pageRows = null;

    public static function hooks(): void {
        add_filter('zp_en_rewrite_routes', [self::class, 'routes'], 40);
        add_filter('zpl_legacy_routes', [self::class, 'legacy']);
        add_filter('zpl_priority_dictionary', [self::class, 'dictionary'], 10, 3);
        add_filter('zpl_has_page', [self::class, 'has_page'], 10, 2);
        add_filter('zpl_sitemap_pages', [self::class, 'sitemap_pages']);
        add_filter('zpl_approved_revision', [self::class, 'revision_filter'], 10, 2);
        add_action('rest_api_init', [self::class, 'rest'], 20);
    }

    public static function flush(): void {
        self::$pageMaps = [];
        self::$shared = null;
        self::$pageRows = null;
        Legacy::forget();
        if (class_exists('ZPL\\Dict')) { \ZPL\Dict::flush(); }
    }

    // ---------------------------------------------------------------- routes

    /** @param mixed $routes */
    public static function routes($routes) {
        $routes = is_array($routes) ? $routes : [];
        $mine = Routes::all();
        if (!Import::done()) {
            // Until the copy is finished, the addresses of the earlier translators keep working as they did.
            foreach (Legacy::all_routes() as $pl => $en) {
                if (!isset($routes[$pl]) && !isset($mine[$pl]) && !Routes::polish($en)) { $routes[$pl] = $en; }
            }
            foreach (Routes::FIXED as $pl => $en) { if (!isset($mine[$pl])) { $routes[$pl] = $en; } }
        }
        if (!$mine) { return $routes; }
        $taken = array_flip($mine);
        foreach ($routes as $pl => $en) {
            // One English address belongs to one Polish page: this module's choice wins.
            if (isset($taken[$en]) && $taken[$en] !== $pl) { unset($routes[$pl]); }
        }
        foreach ($mine as $pl => $en) { $routes[$pl] = $en; }
        return $routes;
    }

    /** @param mixed $legacy */
    public static function legacy($legacy) {
        $legacy = is_array($legacy) ? $legacy : [];
        foreach (Routes::legacy() as $old => $new) { $legacy[$old] = $new; }
        return $legacy;
    }

    // ------------------------------------------------------------ dictionary

    /** @param mixed $layers */
    public static function dictionary($layers, $lang, $source) {
        $layers = is_array($layers) ? $layers : [];
        $source = (string) $source;
        $shared = self::shared_map();
        $page = self::page_map($source);
        if ($lang === 'pl') {
            $layers[] = self::reverse($shared + $page);
            return $layers;
        }
        $layers[] = $shared;
        if ($page) { $layers[] = $page; }
        return $layers;
    }

    /** Site-wide translations: fixed site terms, then saved shared translations (post titles, excerpts…). */
    public static function shared_map(): array {
        if (self::$shared !== null) { return self::$shared; }
        $map = self::SITE_TERMS;
        if (Store::ready()) {
            $key = 'shared:' . self::map_version(0);
            $saved = wp_cache_get($key, self::CACHE_GROUP);
            if (!is_array($saved)) {
                $saved = Store::map_only(Store::SHARED) ?? [];
                wp_cache_set($key, $saved, self::CACHE_GROUP, HOUR_IN_SECONDS);
            }
            foreach ($saved as $pl => $en) { $map[(string) $pl] = (string) $en; }
        }
        if (!Import::done()) {
            foreach (Legacy::zpte_global() as $pl => $en) { if (!isset($map[$pl])) { $map[$pl] = $en; } }
        }
        // Manual site terms stay as the owner set them in the language module (its overrides come first anyway).
        return self::$shared = $map;
    }

    /** Translations of one page (compiled map), or the earlier translators' map while the copy is running. */
    public static function page_map(string $source): array {
        if (isset(self::$pageMaps[$source])) { return self::$pageMaps[$source]; }
        $map = [];
        $row = self::row($source);
        if ($row) {
            $key = 'page:' . $row['id'] . ':' . self::map_version((int) $row['id']);
            $cached = wp_cache_get($key, self::CACHE_GROUP);
            if (is_array($cached)) {
                $map = $cached;
            } else {
                $map = Store::map_only((int) $row['id']) ?? [];
                wp_cache_set($key, $map, self::CACHE_GROUP, HOUR_IN_SECONDS);
            }
        }
        if (!$map && !Import::done()) { $map = Legacy::page_map($source); }
        return self::$pageMaps[$source] = $map;
    }

    /** Small per-request index of pages: source => [id, en, rev]. */
    private static function row(string $source): ?array {
        if (!Store::ready()) { return null; }
        if (self::$pageRows === null) { self::$pageRows = []; }
        if (array_key_exists($source, self::$pageRows)) { return self::$pageRows[$source]; }
        global $wpdb;
        $r = $wpdb->get_row($wpdb->prepare('SELECT p.id, p.en_path, m.rev FROM ' . Store::table('pages') . ' p LEFT JOIN ' . Store::table('maps') . ' m ON m.page_id = p.id WHERE p.path_hash = %s', Store::hash($source)), ARRAY_A);
        self::$pageRows[$source] = $r ? ['id' => (int) $r['id'], 'en' => (string) $r['en_path'], 'rev' => (int) $r['rev']] : null;
        return self::$pageRows[$source];
    }

    /** Version of a compiled map (changes whenever it is saved): cache keys and the browser copy use it. */
    private static function map_version(int $page_id): int {
        $v = get_option('zpte2_map_versions', []);
        return is_array($v) ? (int) ($v[$page_id] ?? 0) : 0;
    }

    /** Called after a map is saved. Page 0 is the shared map. */
    public static function bump(int $page_id, int $rev): void {
        $v = get_option('zpte2_map_versions', []);
        $v = is_array($v) ? $v : [];
        $v[$page_id] = $rev;
        update_option('zpte2_map_versions', $v, true);
        self::flush();
    }

    private static function reverse(array $map): array {
        $out = [];
        foreach ($map as $pl => $en) {
            $k = \ZPL\Html::norm((string) $en);
            if ($k !== '' && !isset($out[$k])) { $out[$k] = (string) $pl; }
        }
        return $out;
    }

    // ------------------------------------------------------------ has page

    /** @param mixed $has */
    public static function has_page($has, $source) {
        if ($has) { return true; }
        $source = (string) $source;
        if (isset(Routes::all()[$source])) { return true; }
        if (!Import::done()) {
            $legacy = Legacy::all_routes();
            if (isset($legacy[$source]) || isset(Routes::FIXED[$source])) { return (bool) Legacy::page_map($source); }
        }
        return false;
    }

    /** @param mixed $pages */
    public static function sitemap_pages($pages) {
        $pages = is_array($pages) ? $pages : [];
        foreach (array_keys(Routes::all()) as $pl) { $pages[] = (string) $pl; }
        if (!Import::done()) {
            foreach (array_keys(Legacy::all_routes()) as $pl) { $pages[] = (string) $pl; }
        }
        return array_values(array_unique($pages));
    }

    // ------------------------------------------------------------------ REST

    public static function rest(): void {
        register_rest_route('zpl/v1', '/approved-page', [
            'methods' => 'GET',
            'permission_callback' => '__return_true',
            'callback' => [self::class, 'approved'],
        ], true);
    }

    /** Revision of what the browser gets for a page (part of the cached REST address). */
    public static function revision(string $source): string {
        $map = self::shared_map() + self::page_map($source);
        if (!$map) { return ''; }
        $row = self::row($source);
        return hash('sha256', $source . '|' . self::map_version(0) . '|' . ($row ? $row['rev'] : 0) . '|' . count($map) . '|' . (Import::done() ? 1 : 0) . '|' . md5((string) wp_json_encode(array_slice($map, 0, 50, true))));
    }

    /** @param mixed $rev */
    public static function revision_filter($rev, $source) {
        return self::revision((string) $source);
    }

    /** @param \WP_REST_Request $r */
    public static function approved($r) {
        $src = $r->get_param('src');
        $lang = $r->get_param('lang');
        if (!is_string($src) || !is_string($lang) || !in_array($lang, ['en', 'pl'], true) || $src === '' || $src[0] !== '/' || strlen($src) > 600) {
            return new \WP_Error('zpte_input', 'Nieprawidłowe parametry.', ['status' => 400]);
        }
        $src = \ZPL\Router::norm_path($src);
        if (\ZPL\Router::is_excluded($src) || \ZPL\Router::never($src) || !\ZPL\Router::has_route($src)) {
            $res = new \WP_REST_Response(['ok' => true, 'source' => $src, 'lang' => $lang, 'revision' => '', 'map' => (object) []]);
            $res->header('Cache-Control', 'public, max-age=300');
            return $res;
        }
        $map = self::shared_map() + self::page_map($src);
        $rev = self::revision($src);
        if ($lang === 'pl') { $map = self::reverse($map); }
        $res = new \WP_REST_Response(['ok' => true, 'source' => $src, 'lang' => $lang, 'revision' => $rev, 'map' => (object) $map]);
        $asked = $r->get_param('rev');
        // The page names the revision it was rendered with: that exact answer never changes, so browsers keep it.
        $res->header('Cache-Control', is_string($asked) && $asked !== '' && hash_equals($rev, $asked) ? 'public, max-age=31536000, immutable' : 'public, max-age=60');
        $res->header('X-Robots-Tag', 'noindex, nofollow');
        return $res;
    }
}
