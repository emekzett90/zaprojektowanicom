<?php
/**
 * 3.0.0 upgrade steps. They run once, when the stored version is older than 3.0.0, and delete no data:
 * Codex's options, post meta and EN translations stay, so reinstalling the 2.9.66 ZIP brings everything back.
 */
if (!defined('ABSPATH')) { exit; }

function zp_suite3_upgrade(string $previous): void {
  if ($previous !== '' && version_compare($previous, '3.0.0', '>=')) { return; }

  // Cron events of the one-off module that 3.0.0 removed (photo refresh of one published post, 10.10).
  foreach (['zp_published_prepared_photos_worker_20261010', 'zp_published_prepared_photos_watchdog_20261010'] as $hook) {
    wp_clear_scheduled_hook($hook);
  }

  // Codex's own HTML cache keys pages by address only, so a phone could get the desktop HTML. If it was ever
  // switched on, switch it off the way its own "off" button does: wp-config.php and advanced-cache.php stay as they
  // are, the drop-in just stops serving. The panel's "Wydajność" page can switch it back on.
  $cache = get_option('zp_html_cache_20261009_state', []);
  if (is_array($cache) && !empty($cache['enabled']) && class_exists('ZP_HTML_Cache_20261009')) {
    try { ZP_HTML_Cache_20261009::deactivate(); update_option('zp_suite3_cache_switched_off', gmdate('c'), false); }
    catch (Throwable $e) { update_option('zp_suite3_cache_switched_off', 'error: ' . $e->getMessage(), false); }
  }
}
