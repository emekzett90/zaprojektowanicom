<?php
/**
 * Plugin Name: Zaprojektowani Languages (PL/EN)
 * Description: Angielska wersja frontendu zaprojektowani.com — adresy /en/, tłumaczenia dostarczone w pliku (bez AI), przełącznik PL/EN bez przeładowania strony, hreflang i mapa witryny EN. Zastępuje wcześniejsze wtyczki tłumaczące.
 * Version: 1.0.16
 * Requires at least: 6.3
 * Requires PHP: 7.4
 * Author: Zaprojektowani
 * License: GPL-2.0-or-later
 * Text Domain: zaprojektowani-languages
 */
if (!defined('ABSPATH')) { exit; }

// Built into Zaprojektowani Suite (ZPL_EMBEDDED = Suite main file): a separately installed copy of this
// plugin is switched off, so only one language layer runs. Its settings and translation fixes are kept (same options).
if (defined('ZPL_EMBEDDED')) {
    add_action('init', static function () {
        $standalone = 'zaprojektowani-languages/zaprojektowani-languages.php';
        if (!function_exists('is_plugin_active')) { require_once ABSPATH . 'wp-admin/includes/plugin.php'; }
        if (!is_plugin_active($standalone)) { return; }
        deactivate_plugins($standalone, true);
        set_transient('zpl_notice', 'Wersja angielska jest teraz częścią wtyczki Zaprojektowani Suite — osobną wtyczkę „Zaprojektowani Languages (PL/EN)” wyłączono. Możesz ją usunąć; ustawienia i poprawki tłumaczeń zostają.', DAY_IN_SECONDS);
    }, 1);
}
if (defined('ZPL_VERSION')) { return; }

define('ZPL_VERSION', '1.0.16');
// 1.0.16: geo checks the address on every visit, honours a chosen English, keeps search/feed addresses and browsers that know Polish on Polish; the switch's choice is set by the server for a year.
// 1.0.15: English versions of the nationwide service and content pages (own routes and page dictionaries).
// 1.0.14: no-reload switch asks the server for every missing text (batches of 400), English for SEO-plan texts (seo-271), English sitemaps split like the Polish ones.
// 1.0.13: filters zpl_dictionary, zpl_has_page and zpl_sitemap_pages let other modules add translated pages.
define('ZPL_API', 1);
define('ZPL_FILE', __FILE__);
define('ZPL_HOST', defined('ZPL_EMBEDDED') ? (string) ZPL_EMBEDDED : __FILE__); // the plugin file WordPress knows
define('ZPL_DIR', plugin_dir_path(__FILE__));
define('ZPL_URL', plugin_dir_url(__FILE__));

foreach (['Html', 'Page', 'Router', 'Dict', 'Suite', 'Switcher', 'Missing', 'Sitemap', 'Cleanup', 'Frame', 'Scan', 'Geo', 'Runtime', 'Rest', 'Admin'] as $zpl_class) {
    require_once ZPL_DIR . 'includes/' . $zpl_class . '.php';
}
unset($zpl_class);

register_activation_hook(ZPL_HOST, static function () {
    $off = \ZPL\Cleanup::deactivate();
    flush_rewrite_rules(false);
    if ($off) { set_transient('zpl_notice', 'Wyłączono poprzednie wtyczki tłumaczące: ' . implode(', ', $off) . '. Możesz je usunąć w ZP Suite → Języki PL/EN → Stare wtyczki.', 120); }
});

add_action('plugins_loaded', ['ZPL\\Runtime', 'boot'], PHP_INT_MIN);
add_action('plugins_loaded', ['ZPL\\Sitemap', 'boot']);
add_action('rest_api_init', ['ZPL\\Rest', 'register']);
if (is_admin()) { \ZPL\Admin::boot(); }

/** Public helpers. Declared conditionally: an unconditional top-level function is bound when the file is compiled,
 *  before the ZPL_VERSION guard runs, and would clash with a second copy of the plugin (stand-alone + Suite). */
if (!function_exists('zpl_language')) {
    function zpl_language(): string { return \ZPL\Router::$lang; }
}
if (!function_exists('zpl_url')) {
    function zpl_url(string $url, ?string $lang = null): string { return \ZPL\Router::url($url, $lang ?? \ZPL\Router::$lang); }
}
