<?php
namespace ZPTE;

if (!defined('ABSPATH')) { exit; }

/**
 * Small autoloaded list of the Polish pages that have an English version from Tłumacz EN, so every request can
 * answer "does this page have an English version?" (hreflang, noindex, sitemap, visitor language) without a query.
 */
final class Index {
    const OPTION = 'zpte2_index';

    private static $idx = null;

    private static function get(): array {
        if (self::$idx === null) {
            $o = get_option(self::OPTION, []);
            self::$idx = is_array($o) && is_array($o['p'] ?? null) ? $o['p'] : [];
        }
        return self::$idx;
    }

    public static function has(string $source): bool { return isset(self::get()[$source]); }

    /** @return string[] Polish paths */
    public static function pages(): array { return array_map('strval', array_keys(self::get())); }

    public static function count(): int { return count(self::get()); }

    /** Pages with translations and an English address (this module's or the language module's own). */
    public static function rebuild(): void {
        global $wpdb;
        if (!Store::ready()) { return; }
        \ZPL\Router::reset();
        $routes = \ZPL\Router::routes();
        $rows = $wpdb->get_results('SELECT path FROM ' . Store::table('pages') . " WHERE status IN ('done','partial') AND keys_done > 0", ARRAY_N);
        $p = [];
        foreach ((array) $rows as $r) {
            $path = (string) $r[0];
            if (isset($routes[$path]) && strpos((string) $routes[$path], '/en/') === 0) { $p[$path] = 1; }
        }
        ksort($p);
        $old = self::get();
        update_option(self::OPTION, ['v' => time(), 'p' => $p], true);
        self::$idx = $p;
        if ($old !== $p && class_exists('ZPL\\Sitemap') && method_exists('ZPL\\Sitemap', 'forget')) { \ZPL\Sitemap::forget(); }
    }
}
