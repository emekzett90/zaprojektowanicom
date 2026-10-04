<?php
namespace ZPL;

if (!defined('ABSPATH')) { exit; }

/**
 * English sitemaps, built like the Polish ones (1.0.14): english-page-sitemap.xml and
 * english-post-sitemap.xml sit next to Rank Math's page-sitemap.xml and post-sitemap.xml in
 * sitemap_index.xml, use the same stylesheet and list the English version of every Polish entry
 * that has one, with that entry's date and images (taken from Rank Math, so both languages match).
 *
 * Older English sitemap addresses (/sitemap-en.xml until 2.6.4, /english-sitemap.xml in 2.6.4–2.7.0)
 * redirect to sitemap_index.xml, which lists both English maps.
 */
final class Sitemap {
    const MAPS = ['page' => '/english-page-sitemap.xml', 'post' => '/english-post-sitemap.xml'];
    const LEGACY = ['/sitemap-en.xml', '/english-sitemap.xml'];
    /** @deprecated 1.0.14: kept for modules that still read it; the page map. */
    const PATH = '/english-page-sitemap.xml';
    const CACHE = 'zpl_sitemap_v2';

    /** The English page map (older callers expect one URL). */
    public static function url(): string { return home_url(self::MAPS['page']); }

    /** Every English map: type => URL. */
    public static function urls(): array {
        $out = [];
        foreach (self::MAPS as $type => $path) { $out[$type] = home_url($path); }
        return $out;
    }

    /** Map type for a request path ('' when it is not an English map). */
    public static function type_for(string $rel): string {
        $type = array_search($rel, self::MAPS, true);
        return $type === false ? '' : (string) $type;
    }

    public static function redirect(): void {
        wp_redirect(home_url('/sitemap_index.xml'), 301, 'Zaprojektowani Languages');
        exit;
    }

    /** Polish pages that have an English version: source path => true. */
    private static function translated(): array {
        $pages = (array) (Dict::meta()['pages'] ?? []);
        // Pages translated after the dictionary was built (e.g. by Tłumacz EN).
        $pages = array_merge($pages, (array) apply_filters('zpl_sitemap_pages', []));
        $routes = Router::routes();
        $out = [];
        foreach ($pages as $source) {
            $source = Router::norm_path((string) $source);
            if (!isset($routes[$source]) || Router::is_excluded($source) || strpos($source, '/page/') !== false || strpos($source, '/dziekujemy') === 0) { continue; }
            $out[$source] = true;
        }
        return $out;
    }

    /**
     * The Polish entries of one type as Rank Math lists them (loc, mod, images), or null when Rank Math's
     * sitemap classes are not available.
     */
    private static function rank_math_links(string $type): ?array {
        if (!class_exists('RankMath\\Sitemap\\Providers\\Post_Type')) { return null; }
        try {
            $provider = new \RankMath\Sitemap\Providers\Post_Type();
            if (!$provider->handles_type($type)) { return []; }
            return (array) $provider->get_sitemap_links($type, 5000, 1);
        } catch (\Throwable $e) {
            return null;
        }
    }

    /** Fallback without Rank Math: published, public posts of the type, no images. */
    private static function core_links(string $type): array {
        $ids = get_posts(['post_type' => $type, 'post_status' => 'publish', 'has_password' => false, 'numberposts' => 5000, 'fields' => 'ids', 'orderby' => 'modified', 'order' => 'DESC']);
        $out = [];
        foreach ($ids as $id) {
            $p = get_post($id);
            if (!$p) { continue; }
            $out[] = ['loc' => get_permalink($p), 'mod' => max($p->post_modified_gmt, $p->post_date_gmt), 'images' => []];
        }
        return $out;
    }

    /** English entries of one type: [en, pl, mod (GMT), images[]]. Cached until content changes. */
    public static function entries(string $type): array {
        if (!isset(self::MAPS[$type])) { return []; }
        $key = self::CACHE . '_' . $type;
        $version = self::version();
        $cached = get_transient($key);
        if (is_array($cached) && ($cached['v'] ?? '') === $version) { return (array) $cached['rows']; }

        $translated = self::translated();
        $routes = Router::routes();
        $links = self::rank_math_links($type);
        if ($links === null) { $links = self::core_links($type); }
        $rows = [];
        foreach ($links as $l) {
            if (!is_array($l) || empty($l['loc'])) { continue; }
            $path = Router::rel(Router::norm_path((string) wp_parse_url((string) $l['loc'], PHP_URL_PATH)));
            if (!isset($translated[$path])) { continue; }
            $images = [];
            foreach ((array) ($l['images'] ?? []) as $img) {
                $src = is_array($img) ? (string) ($img['src'] ?? '') : (string) $img;
                if ($src !== '') { $images[] = $src; }
            }
            $rows[] = [
                'en' => home_url($routes[$path]),
                'pl' => (string) $l['loc'],
                'mod' => (string) ($l['mod'] ?? ''),
                'images' => array_values(array_unique($images)),
            ];
        }
        set_transient($key, ['v' => $version, 'rows' => $rows], 12 * HOUR_IN_SECONDS);
        return $rows;
    }

    /** Changes when content, routes or translations change. */
    private static function version(): string {
        global $wpdb;
        $last = (string) $wpdb->get_var("SELECT MAX(post_modified_gmt) FROM {$wpdb->posts} WHERE post_status = 'publish' AND post_type IN ('post','page')");
        return md5($last . '|' . ZPL_VERSION . '|' . (Dict::meta()['version'] ?? '') . '|' . implode(',', array_keys(self::translated())));
    }

    /** Newest date in one English map (from the same entries the map lists, so index and map agree). */
    public static function lastmod(string $type): int {
        $max = 0;
        foreach (self::entries($type) as $r) {
            if ($r['mod'] !== '') { $max = max($max, (int) strtotime($r['mod'] . ' UTC')); }
        }
        return $max ?: (int) (Dict::meta()['built'] ?? time());
    }

    /** Rank Math's sitemap stylesheet, so the English maps look like the Polish ones. */
    private static function stylesheet(): string {
        if (class_exists('RankMath\\Sitemap\\Router') && method_exists('RankMath\\Sitemap\\Router', 'get_base_url')) {
            return (string) preg_replace('/^https?:/', '', \RankMath\Sitemap\Router::get_base_url('main-sitemap.xsl'));
        }
        return '';
    }

    /** Runs on wp_loaded, so Rank Math (and the page builder it asks for images) is ready. */
    public static function render(): void {
        $type = self::type_for(Router::rel((string) (wp_parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH) ?: '/')));
        if ($type === '') { return; }
        $rows = self::entries($type);
        status_header(200);
        header('Content-Type: application/xml; charset=UTF-8');
        header('X-Robots-Tag: noindex, follow');
        $xsl = self::stylesheet();
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        if ($xsl !== '') { echo '<?xml-stylesheet type="text/xsl" href="' . esc_attr($xsl) . '"?>' . "\n"; }
        echo '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" xmlns:xhtml="http://www.w3.org/1999/xhtml" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd http://www.google.com/schemas/sitemap-image/1.1 http://www.google.com/schemas/sitemap-image/1.1/sitemap-image.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($rows as $r) {
            $en = esc_url($r['en']);
            $pl = esc_url($r['pl']);
            echo "\t<url>\n\t\t<loc>" . $en . "</loc>\n";
            if ($r['mod'] !== '') { echo "\t\t<lastmod>" . esc_html(gmdate('c', (int) strtotime($r['mod'] . ' UTC'))) . "</lastmod>\n"; }
            echo "\t\t<xhtml:link rel=\"alternate\" hreflang=\"en\" href=\"" . $en . "\"/>\n"
                . "\t\t<xhtml:link rel=\"alternate\" hreflang=\"pl\" href=\"" . $pl . "\"/>\n"
                . "\t\t<xhtml:link rel=\"alternate\" hreflang=\"x-default\" href=\"" . $pl . "\"/>\n";
            foreach ($r['images'] as $img) { echo "\t\t<image:image>\n\t\t\t<image:loc>" . esc_url($img) . "</image:loc>\n\t\t</image:image>\n"; }
            echo "\t</url>\n";
        }
        echo '</urlset>';
        exit;
    }

    /** sitemap_index.xml entries for the English maps (Rank Math's index format). */
    public static function index_xml(): string {
        $out = '';
        foreach (self::MAPS as $type => $path) {
            $out .= "\t<sitemap>\n\t\t<loc>" . esc_url(home_url($path)) . "</loc>\n\t\t<lastmod>" . esc_html(gmdate('c', self::lastmod($type))) . "</lastmod>\n\t</sitemap>\n";
        }
        return $out;
    }

    public static function forget(): void {
        foreach (array_keys(self::MAPS) as $type) { delete_transient(self::CACHE . '_' . $type); }
    }

    public static function boot(): void {
        add_filter('robots_txt', static function ($out) {
            $out = rtrim((string) $out);
            // Both English maps are listed in sitemap_index.xml, so robots.txt only needs the index.
            if (stripos($out, 'sitemap_index.xml') === false) { $out .= "\nSitemap: " . home_url('/sitemap_index.xml'); }
            return $out . "\n";
        }, 99);
        add_filter('rank_math/sitemap/index', static function ($xml) { return $xml . self::index_xml(); });
        add_action('save_post', [self::class, 'forget']);
        add_action('deleted_post', [self::class, 'forget']);
        add_action('update_option_zpl_routes', [self::class, 'forget']);
    }
}
