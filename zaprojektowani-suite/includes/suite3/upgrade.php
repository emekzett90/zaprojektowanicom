<?php
/**
 * Upgrade steps for 3.0.x. Each runs once, when the stored version is older than its release, and deletes no data:
 * Codex's options, post meta and EN translations stay, so reinstalling the 2.9.66 ZIP brings everything back.
 */
if (!defined('ABSPATH')) { exit; }

/** Results of the 3.0.1 cache step that are settled; any other result is retried from wp-admin or cron. */
const ZP_SUITE3_CACHE_301_SETTLED = ['on', 'not-installed', 'dropin-updated', 'dropin-foreign', 'dropin-missing'];

function zp_suite3_upgrade(string $previous): void {
  if ($previous === '' || version_compare($previous, '3.0.0', '<')) {
    // Cron events of the one-off module that 3.0.0 removed (photo refresh of one published post, 10.10).
    foreach (['zp_published_prepared_photos_worker_20261010', 'zp_published_prepared_photos_watchdog_20261010'] as $hook) {
      wp_clear_scheduled_hook($hook);
    }
  }
  if ($previous !== '' && version_compare($previous, '3.0.1', '<')) {
    // Codex's HTML cache keyed pages by address only, while phones get different HTML (no hero video, lazy images).
    // 3.0.1 installs a drop-in that keeps one copy per device class; 3.0.0 had switched the cache off for that reason,
    // so it comes back on. Runs once WordPress has finished loading (permalinks are needed for the list of pages).
    delete_option('zp_suite3_cache_301');
    add_action('wp_loaded', 'zp_suite3_cache_301', 99);
  }
}

function zp_suite3_cache_301(): void {
  if (!class_exists('ZP_HTML_Cache_20261009')) { return; }
  $before = get_option('zp_suite3_cache_301', []);
  $tries = (is_array($before) ? (int) ($before['tries'] ?? 0) : 0) + 1;
  $off = (string) get_option('zp_suite3_cache_switched_off', '');
  // Only a cache that 3.0.0 itself switched off comes back on; one switched off by hand only gets the new drop-in.
  $resume = $off !== '' && strpos($off, 'error') !== 0;
  try { $result = ZP_HTML_Cache_20261009::upgrade_dropin($resume); }
  catch (Throwable $e) { $result = 'error: ' . $e->getMessage(); }
  update_option('zp_suite3_cache_301', ['at' => gmdate('c'), 'result' => $result, 'tries' => $tries], true);
  if ($result === 'on') { delete_option('zp_suite3_cache_switched_off'); }
}

// A passing failure (a busy file, a request without WP_CACHE) is retried from wp-admin or cron, every 5 minutes, 5 times.
add_action('wp_loaded', static function (): void {
  if (!function_exists('zp_suite_backend_request') || !zp_suite_backend_request()) { return; }
  $s = get_option('zp_suite3_cache_301', null);
  if (!is_array($s) || in_array((string) ($s['result'] ?? ''), ZP_SUITE3_CACHE_301_SETTLED, true) || (int) ($s['tries'] ?? 0) >= 5) { return; }
  if (time() - (int) strtotime((string) ($s['at'] ?? '')) < 300) { return; }
  zp_suite3_cache_301();
}, 100);
