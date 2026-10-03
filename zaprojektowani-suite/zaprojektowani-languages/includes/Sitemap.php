<?php
namespace ZPL;

if (!defined('ABSPATH')) { exit; }

final class Sitemap {
    /**
     * Address of the English sitemap. Until Suite 2.6.4 it was /sitemap-en.xml, but on the live site
     * that address (and /sitemap-pl.xml) answers 410 before WordPress gets to it, most likely because of
     * the old "Zaprojektowani Ultimate English AI" plugin. The old address now redirects here.
     */
    const PATH = '/english-sitemap.xml';
    const LEGACY = '/sitemap-en.xml';

    public static function url(): string { return home_url(self::PATH); }

    public static function redirect(): void {
        wp_redirect(self::url(), 301, 'Zaprojektowani Languages');
        exit;
    }

    public static function render(): void {
        status_header(200);
        header('Content-Type: application/xml; charset=UTF-8');
        header('X-Robots-Tag: noindex, follow');
        $pages = (array) (Dict::meta()['pages'] ?? []);
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">' . "\n";
        foreach ($pages as $source) {
            $source = (string) $source;
            if (Router::is_excluded($source) || strpos($source, '/page/') !== false || strpos($source, '/dziekujemy') === 0) { continue; }
            $routes = Router::routes();
            if (!isset($routes[$source])) { continue; }
            $en = esc_url(home_url($routes[$source]));
            $pl = esc_url(home_url($source));
            echo '<url><loc>' . $en . '</loc>'
                . '<xhtml:link rel="alternate" hreflang="en" href="' . $en . '"/>'
                . '<xhtml:link rel="alternate" hreflang="pl" href="' . $pl . '"/>'
                . '<xhtml:link rel="alternate" hreflang="x-default" href="' . $pl . '"/></url>' . "\n";
        }
        echo '</urlset>';
        exit;
    }

    public static function boot(): void {
        add_filter('robots_txt', static function ($out) { return rtrim((string) $out) . "\nSitemap: " . self::url() . "\n"; }, 99);
        add_filter('rank_math/sitemap/index', static function ($xml) {
            return $xml . '<sitemap><loc>' . esc_url(self::url()) . '</loc><lastmod>' . esc_html(gmdate('c', (int) (Dict::meta()['built'] ?? time()))) . '</lastmod></sitemap>';
        });
    }
}
