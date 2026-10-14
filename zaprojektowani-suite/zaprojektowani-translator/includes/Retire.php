<?php
namespace ZPTE;

if (!defined('ABSPATH')) { exit; }

/**
 * One translator from now on. The earlier ones are switched off, never deleted: the separate plugin
 * ZP Translator v2 is deactivated and the scheduled runs of all of them are cleared. Their options and tables
 * stay as they were, so reinstalling the previous ZP Suite and reactivating v2 brings them back.
 */
final class Retire {
    const DONE = 'zpte2_retired';
    /** Scheduled runs of the earlier translators (Luna rewrite 11.10, nightly updates, v2, Tłumacz EN 1). */
    const HOOKS = [
        'zp_en_rewrite_worker_20261011', 'zp_en_rewrite_watchdog_20261011',
        'zp_en_nightly_daily_20261011', 'zp_en_nightly_scan_20261011',
        'zpt2_worker', 'zpt2_daily_scan', 'zpt2_scan_step',
        'zpte_daily', 'zpte_work', 'zpte_scan_retry',
    ];

    /**
     * Hooks of Codex's EN modules (11.10.2026) inside ZP Suite. The loader no longer needs them; while a build
     * still loads them, their schedules, routes and page screen stay off so only one translator works.
     */
    const CODEX = [
        'ZP_EN_Rewrite_Nightly_20261011' => [['init', 'maintain', 30], ['zp_en_nightly_daily_20261011', 'daily', 10], ['zp_en_nightly_scan_20261011', 'scan', 10],
            ['zp_en_rewrite_routes', 'routes', 20], ['zpl_has_page', 'has_page', 20], ['zpl_sitemap_pages', 'sitemap', 20]],
        'ZP_EN_Rewrite_20261011' => [['zp_en_rewrite_worker_20261011', 'worker', 10], ['zp_en_rewrite_watchdog_20261011', 'watchdog', 10]],
        'ZP_Translator_Approved_Overlay_20261011' => [['rest_api_init', 'routes', 10], ['wp_head', 'preview_config', 2], ['template_redirect', 'preview_headers', 0], ['script_loader_src', 'script_source', PHP_INT_MAX]],
        'ZP_Luna_Translator_UI_20261011' => [['admin_menu', 'menu', PHP_INT_MAX], ['admin_enqueue_scripts', 'assets', PHP_INT_MAX]],
    ];

    public static function boot(): void {
        add_action('plugins_loaded', [self::class, 'unhook'], 95);
    }

    /**
     * While v2 is still active (until the first worker run switches it off), its screens must not take over
     * the Tłumacz EN page and its filters must not change English pages.
     */
    public static function unhook(): void {
        foreach (self::CODEX as $class => $hooks) {
            if (!class_exists($class, false)) { continue; }
            foreach ($hooks as [$hook, $method, $prio]) { remove_filter($hook, [$class, $method], $prio); }
        }
        if (class_exists('ZPT2_Admin')) {
            remove_action('admin_init', ['ZPT2_Admin', 'redirect_legacy']);
            remove_action('admin_menu', ['ZPT2_Admin', 'hide_legacy_menu'], PHP_INT_MAX);
        }
        if (class_exists('ZPT2_Shared')) {
            remove_filter('zp_en_public_price_html', ['ZPT2_Shared', 'html'], 999);
            remove_action('wp_footer', ['ZPT2_Shared', 'script'], 99);
        }
        $reset = class_exists('ZP_EN_Rewrite_Nightly_20261011', false);
        if (class_exists('ZPT2_Engine')) {
            $reset = true;
            remove_filter('zp_en_public_price_html', ['ZPT2_Engine', 'serving_probe'], -100);
            remove_filter('zp_en_rewrite_routes', ['ZPT2_Engine', 'routes'], 30);
            remove_filter('zpl_has_page', ['ZPT2_Engine', 'has_page'], 30);
            remove_filter('zpl_sitemap_pages', ['ZPT2_Engine', 'sitemap'], 30);
            foreach (['zpt2_worker' => 'worker', 'zpt2_daily_scan' => 'daily', 'zpt2_scan_step' => 'scan_step'] as $hook => $m) {
                remove_action($hook, ['ZPT2_Engine', $m]);
            }
            remove_action('init', ['ZPT2_Engine', 'schedule'], 99);
        }
        if ($reset && class_exists('ZPL\\Router')) { \ZPL\Router::reset(); }
    }

    public static function done(): bool { return (bool) get_option(self::DONE); }

    /** Switch the earlier translators off (once; repeated after a reinstall of v2 is noticed). */
    public static function old_translators(): void {
        foreach (self::HOOKS as $h) { wp_unschedule_hook($h); }
        $off = [];
        if (!function_exists('deactivate_plugins') || !function_exists('is_plugin_active')) { require_once ABSPATH . 'wp-admin/includes/plugin.php'; }
        foreach ((array) get_option('active_plugins', []) as $plugin) {
            $plugin = (string) $plugin;
            if (substr($plugin, -strlen('/zp-translator-v2.php')) === '/zp-translator-v2.php') {
                // Silent: v2's own deactivation code expects classes of the old Suite; its runs are cleared above.
                deactivate_plugins($plugin, true);
                $off[] = 'ZP Translator v2';
            }
        }
        $first = !self::done();
        update_option(self::DONE, time(), false);
        if ($first || $off) {
            Log::add('Jeden tłumacz: ' . ($off ? 'wyłączono wtyczkę ' . implode(', ', $off) . ' i ' : '') . 'zatrzymano harmonogramy poprzednich tłumaczy. Ich dane zostają bez zmian (do przywrócenia z kopii).', 'info');
        }
    }

    /** v2 was activated again (e.g. after a rollback and a new install): switch it off again. */
    public static function maybe(): void {
        if (!self::done()) { self::old_translators(); return; }
        if (class_exists('ZPT2_Engine') || wp_next_scheduled('zpt2_worker') || wp_next_scheduled('zp_en_nightly_daily_20261011') || wp_next_scheduled('zpte_daily')) {
            self::old_translators();
        }
    }
}
