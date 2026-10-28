<?php
if (!defined('ABSPATH')) { exit; }
/** Migrate only the exact earlier repair plugin supplied in this project. */
function zp_suite_seo_retire_standalone() {
    if (!ZPSSEO_Repair::allowed()) { return; }
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
    $old = 'zaprojektowani-ahrefs-repair/zaprojektowani-ahrefs-repair.php';
    if (!is_plugin_active($old)) { return; }
    $previous = get_option('zp_ahrefs_repair_v1', []);
    // Its deactivation restores the original SEO fields before our own first backup.
    deactivate_plugins($old);
    // Deactivation does not remove callbacks from this already-loaded request.
    global $wp_filter;
    foreach ($wp_filter as $hook => $object) {
        foreach ($object->callbacks as $priority => $callbacks) {
            foreach ($callbacks as $entry) {
                $cb = $entry['function'];
                if (is_array($cb) && is_string($cb[0]) && in_array($cb[0], ['ZP_Ahrefs_Repair','ZPAR_Automatic','ZPUltimate'], true)) {
                    remove_filter($hook, $cb, $priority);
                }
            }
        }
    }
    if (!get_option(ZPSSEO_Repair::OPTION, false) && is_array($previous)) {
        $state = ZPSSEO_Repair::state();
        foreach (['maps','custom','empty_css','dead_links'] as $key) {
            if (isset($previous[$key]) && is_array($previous[$key])) { $state[$key] = $previous[$key]; }
        }
        ZPSSEO_Repair::save($state);
    }
    update_option('zp_suite_seo_migrated', time(), false);
}
add_action('plugins_loaded', 'zp_suite_seo_retire_standalone', -100);
register_activation_hook(ZP_SUITE_PATH . 'zaprojektowani-suite.php', 'zp_suite_seo_retire_standalone');
require_once __DIR__ . '/repair.php';
