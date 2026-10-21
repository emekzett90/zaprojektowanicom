<?php
/**
 * Upgrade steps for 3.x. Each runs once, when the stored version is older than its release, and deletes no data:
 * Codex's options, post meta and EN translations stay, so reinstalling the 2.9.66 ZIP brings everything back.
 */
if (!defined('ABSPATH')) { exit; }

/** Results of the cache step (3.0.1, 3.1.0, 3.2.0 and 3.2.31) that are settled; other results retry from wp-admin or cron. */
const ZP_SUITE3_CACHE_301_SETTLED = ['on', 'not-installed', 'dropin-updated', 'dropin-foreign', 'dropin-missing'];

function zp_suite3_upgrade(string $previous): void {
  if ($previous === '' || version_compare($previous, '3.2.35', '<')) {
    // Reuse the existing, retryable Suite cache invalidation path.
    update_option('zp_suite_mobile_shell_3234', 'pending', true);
  }
  if ($previous === '' || version_compare($previous, '3.2.34', '<')) {
    update_option('zp_suite_mobile_shell_3234', 'pending', true);
  }
  if ($previous === '' || version_compare($previous, '3.2.33', '<')) {
    update_option('zp_suite_first_paint_3233', 'pending', true);
  }
  if ($previous === '' || version_compare($previous, '3.2.32', '<')) {
    // Persist before the version marker changes. A request ending early can retry on the next load.
    update_option('zp_suite_growth_cache_20261017', 'pending', true);
  }
  if ($previous !== '' && version_compare($previous, '3.2.3', '<')) {
    // Replacement runs with the old PHP constants in memory. Refresh our own HTML cache on the first new
    // request so the new menu, portfolio and language assets are visible immediately in both languages.
    add_action('wp_loaded', static function (): void { do_action('zp_html_cache_purge'); }, 100);
  }
  if ($previous !== '' && version_compare($previous, '3.2.51', '<')) {
    // 3.2.51: a new advanced-cache.php (copies also for browsers with WordPress's leftover login cookies and for visitors
    // who chose Polish with the switch). A purge puts it in place (ZP_HTML_Cache_20261009::purge → upgrade_dropin), only
    // over this module's own drop-in; a second purge queued by the step above in the same request is not repeated.
    add_action('wp_loaded', static function (): void { do_action('zp_html_cache_purge'); }, 100);
  }
  if ($previous === '' || version_compare($previous, '3.0.0', '<')) {
    // Cron events of the one-off module that 3.0.0 removed (photo refresh of one published post, 10.10).
    foreach (['zp_published_prepared_photos_worker_20261010', 'zp_published_prepared_photos_watchdog_20261010'] as $hook) {
      wp_clear_scheduled_hook($hook);
    }
  }
  if ($previous !== '' && version_compare($previous, '3.2.31', '<')) {
    // Codex's HTML cache keyed pages by address only, while phones get different HTML (no hero video, lazy images).
    // 3.0.1 installs a drop-in that keeps one copy per device class; 3.0.0 had switched the cache off for that reason,
    // so it comes back on. 3.1.0's drop-in also answers robots and visitors with only consent or statistics cookies;
    // 3.2.0's sends no-cache headers on a MISS too and keys versioned (EN) pages by their version.
    // 3.2.31 also serves explicit PL choices. Refresh the owned drop-in and cache generation on the first new
    // request, including a manual ZIP replacement; an update request can still execute the old plugin code.
    // Runs once WordPress has finished loading (permalinks are needed for the list of pages).
    // The marker is written as pending first: a first request that ends before wp_loaded
    // (llms.txt, an EN sitemap, a Geo redirect) would otherwise lose the step, and the retry below picks it up.
    // Only the historical pre-3.2.0 upgrade may resume a cache which 3.0.0 switched off. This release never resumes
    // an existing manually disabled cache, even when an old recovery marker remains in the database.
    update_option('zp_suite3_cache_301', ['at' => '', 'result' => 'pending', 'tries' => 0, 'resume_legacy' => version_compare($previous, '3.2.0', '<')], true);
    add_action('wp_loaded', 'zp_suite3_cache_301', 99);
  }
  if ($previous !== '' && version_compare($previous, '3.2.0', '<')) {
    zp_suite3_input_vars();
  }
  if ($previous !== '' && version_compare($previous, '3.1.0', '<') && function_exists('zp_seo_plan_data')) {
    // 3.0.x logged addresses that the SEO plan answers with 410 (gone on purpose); they are not 404s to fix.
    $log = get_option('zp_suite3_404_log', []);
    $gone = (array) ((zp_seo_plan_data('redirects')['exact'] ?? []));
    if (is_array($log) && $log) {
      $kept = array_filter($log, static fn($path) => ($gone[$path] ?? '') !== '@410', ARRAY_FILTER_USE_KEY);
      if (count($kept) !== count($log)) { update_option('zp_suite3_404_log', $kept, false); }
    }
  }
}

function zp_suite3_cache_301(): void {
  if (!class_exists('ZP_HTML_Cache_20261009')) { return; }
  $before = get_option('zp_suite3_cache_301', []);
  $tries = (is_array($before) ? (int) ($before['tries'] ?? 0) : 0) + 1;
  $off = (string) get_option('zp_suite3_cache_switched_off', '');
  $resume_legacy = is_array($before) && !empty($before['resume_legacy']);
  // Only a cache that 3.0.0 itself switched off comes back on; one switched off by hand only gets the new drop-in.
  $resume = $resume_legacy && $off !== '' && strpos($off, 'error') !== 0;
  try { $result = ZP_HTML_Cache_20261009::upgrade_dropin($resume); }
  catch (Throwable $e) { $result = 'error: ' . $e->getMessage(); }
  update_option('zp_suite3_cache_301', ['at' => gmdate('c'), 'result' => $result, 'tries' => $tries, 'resume_legacy' => $resume_legacy], true);
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

/**
 * 3.2.0: the "Strona główna" form has more fields than the host's max_input_vars (1000 on live), and the nazwa.pl panel
 * has no setting for it. PHP reads .user.ini in the WordPress folder (again every 5 minutes), so one line is added there,
 * keeping every line already in the file; the previous content is kept in an option. Nothing is written when the limit
 * is already 5000 or more, when the file sets max_input_vars itself, or when it is not a plain writable file.
 */
function zp_suite3_input_vars(): string {
  $result = (static function (): string {
    if ((int) ini_get('max_input_vars') >= 5000) { return 'ok'; }
    $file = ABSPATH . '.user.ini';
    if (is_link($file) || (file_exists($file) && !is_file($file))) { return 'not-a-file'; }
    $old = is_file($file) ? @file_get_contents($file) : '';
    if (!is_string($old) || strlen($old) > 65536) { return 'unreadable'; }
    if (preg_match('/^\s*max_input_vars\s*=/mi', $old)) { return 'set-in-file'; }
    if (!is_writable(is_file($file) ? $file : ABSPATH) || !is_writable(ABSPATH)) { return 'not-writable'; }
    $lines = "; ZP Suite 3.2.0: the home page form in wp-admin needs more than 1000 fields\nmax_input_vars = 5000\n";
    // Lines after a [HOST=…] or [PATH=…] header apply only there, so they go before the first section header, if any.
    if (preg_match('/^[ \t]*\[/m', $old, $m, PREG_OFFSET_CAPTURE)) { $new = substr($old, 0, $m[0][1]) . $lines . substr($old, $m[0][1]); }
    else { $new = $old . ($old !== '' && substr($old, -1) !== "\n" ? "\n" : '') . $lines; }
    update_option('zp_suite3_user_ini_backup', ['at' => gmdate('c'), 'existed' => is_file($file), 'content' => $old], false);
    $tmp = ABSPATH . '.zp-user-ini-' . bin2hex(random_bytes(8));
    $mode = is_file($file) ? (fileperms($file) & 0777) : 0644;
    if (@file_put_contents($tmp, $new, LOCK_EX) !== strlen($new)) { @unlink($tmp); return 'write-failed'; }
    @chmod($tmp, $mode);
    if (is_link($file) || !@rename($tmp, $file)) { @unlink($tmp); return 'write-failed'; }
    return 'added';
  })();
  update_option('zp_suite3_input_vars', ['at' => time(), 'result' => $result], false);
  return $result;
}
