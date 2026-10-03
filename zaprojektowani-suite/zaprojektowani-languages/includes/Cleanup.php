<?php
namespace ZPL;

if (!defined('ABSPATH')) { exit; }

/** Previous translation plugins used on zaprojektowani.com and their stored data. */
final class Cleanup {
    const FILES = [
        'zaprojektowani-ultimate-english-ai/zaprojektowani-ultimate-english-ai.php',
        'zaprojektowani-ai-translator/zaprojektowani-ai-translator.php',
        'zaprojektowani-ultimate-ai-languages/zaprojektowani-ultimate-ai-languages.php',
    ];
    const NAMES = '~^Zaprojektowani (?:Ultimate English AI|AI Languages|AI Translator|Ultimate AI Languages)\b~i';
    const TABLE_PREFIXES = ['zue_', 'zul_', 'zul2_', 'zpat_'];
    const OPTION_PREFIXES = ['zue_', 'zul_', 'zul2_', 'zpat_'];
    const CRON = ['zul_process_jobs', 'zul_worker', 'zul_jobs_tick', 'zue_worker', 'zue_tick', 'zpat_cron'];

    /** Old translator still running in this request (its classes are loaded). */
    public static function conflict(): bool {
        return class_exists('ZUE\\Plugin', false) || class_exists('ZUL\\Runtime', false) || class_exists('ZPAT_Router', false);
    }

    public static function found(): array {
        if (!function_exists('get_plugins')) { require_once ABSPATH . 'wp-admin/includes/plugin.php'; }
        $out = [];
        foreach (get_plugins() as $file => $data) {
            if ($file === plugin_basename(ZPL_FILE)) { continue; }
            if (in_array($file, self::FILES, true) || preg_match(self::NAMES, (string) ($data['Name'] ?? ''))) {
                $out[$file] = ['name' => (string) $data['Name'], 'version' => (string) ($data['Version'] ?? ''), 'active' => is_plugin_active($file)];
            }
        }
        return $out;
    }

    public static function deactivate(): array {
        if (!function_exists('deactivate_plugins')) { require_once ABSPATH . 'wp-admin/includes/plugin.php'; }
        $active = array_keys(array_filter(self::found(), static function ($p) { return $p['active']; }));
        if ($active) { deactivate_plugins($active, true, is_multisite() && is_network_admin()); }
        foreach (self::CRON as $hook) { wp_clear_scheduled_hook($hook); }
        return $active;
    }

    /** Tables and options left behind by the old plugins. */
    public static function leftovers(): array {
        global $wpdb;
        $tables = [];
        foreach (self::TABLE_PREFIXES as $p) {
            $like = $wpdb->esc_like($wpdb->prefix . $p) . '%';
            foreach ((array) $wpdb->get_col($wpdb->prepare('SHOW TABLES LIKE %s', $like)) as $t) { $tables[] = (string) $t; }
        }
        $options = [];
        foreach (self::OPTION_PREFIXES as $p) {
            $like = $wpdb->esc_like($p) . '%';
            foreach ((array) $wpdb->get_col($wpdb->prepare("SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s", $like)) as $o) { $options[] = (string) $o; }
            $tl = $wpdb->esc_like('_transient_' . $p) . '%';
            foreach ((array) $wpdb->get_col($wpdb->prepare("SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s", $tl)) as $o) { $options[] = (string) $o; }
            $tt = $wpdb->esc_like('_transient_timeout_' . $p) . '%';
            foreach ((array) $wpdb->get_col($wpdb->prepare("SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s", $tt)) as $o) { $options[] = (string) $o; }
        }
        return ['tables' => array_values(array_unique($tables)), 'options' => array_values(array_unique($options))];
    }

    /** Delete old plugin files and their data (admin action, requires explicit confirmation). */
    public static function purge(): array {
        global $wpdb;
        self::deactivate();
        $report = ['plugins' => [], 'tables' => [], 'options' => [], 'errors' => []];
        if (!function_exists('delete_plugins')) { require_once ABSPATH . 'wp-admin/includes/plugin.php'; require_once ABSPATH . 'wp-admin/includes/file.php'; }
        $files = array_keys(self::found());
        if ($files) {
            $res = delete_plugins($files);
            if (is_wp_error($res)) { $report['errors'][] = $res->get_error_message(); } else { $report['plugins'] = $files; }
        }
        $left = self::leftovers();
        foreach ($left['tables'] as $t) {
            if (!preg_match('~^[A-Za-z0-9_]+$~', $t)) { continue; }
            $wpdb->query("DROP TABLE IF EXISTS `{$t}`"); // phpcs:ignore
            $report['tables'][] = $t;
        }
        foreach ($left['options'] as $o) { delete_option($o); $report['options'][] = $o; }
        foreach (self::CRON as $hook) { wp_clear_scheduled_hook($hook); }
        flush_rewrite_rules(false);
        return $report;
    }
}
