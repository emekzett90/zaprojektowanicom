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
  if ($previous !== '' && version_compare($previous, '3.2.52', '<')) {
    // 3.2.52: audit fixes change the HTML of every page (both popups inside <template>, English reviews on English pages).
    // Copies made by the old version would be served until they expire (1800 s), so the first new request empties
    // this module's own HTML cache (never LiteSpeed or the CDN); a purge queued by a step above is not repeated.
    add_action('wp_loaded', static function (): void { do_action('zp_html_cache_purge'); }, 100);
  }
  if ($previous !== '' && version_compare($previous, '3.2.54', '<')) {
    // 3.2.54: seven approved changes give new HTML on Realizacje, the shop pages, Studio wyceny, home, the Katowice
    // pages and every post (PR #39, #41, #42, #45-#48).
    // Copies made by the old version would be served until they expire (1800 s), so the first new request empties
    // this module's own HTML cache (never LiteSpeed or the CDN); a purge queued by a step above is not repeated.
    add_action('wp_loaded', static function (): void { do_action('zp_html_cache_purge'); }, 100);
  }
  if ($previous !== '' && version_compare($previous, '3.2.55', '<')) {
    // 3.2.55: the industry pages, the Tworzenie stron hero and four FAQ texts have new HTML (PR #38).
    // Copies made by the old version would be served until they expire (1800 s), so the first new request empties
    // this module's own HTML cache (never LiteSpeed or the CDN); a purge queued by a step above is not repeated.
    add_action('wp_loaded', static function (): void { do_action('zp_html_cache_purge'); }, 100);
  }
  if ($previous !== '' && version_compare($previous, '3.2.56', '<')) {
    // 3.2.56: every page has a new business schema node; the services box under posts, 45 meta texts, links to old
    // addresses and the src of the case study screenshots and two photos are new too (PR #51 and #36).
    // Copies made by the old version would be served until they expire (1800 s), so the first new request empties
    // this module's own HTML cache (never LiteSpeed or the CDN); a purge queued by a step above is not repeated.
    add_action('wp_loaded', static function (): void { do_action('zp_html_cache_purge'); }, 100);
  }
  if ($previous !== '' && version_compare($previous, '3.2.58', '<')) {
    // 3.2.58: the cards under the hero of the Polish and English home page are new (the review count under the rating,
    // the Facebook card and the link to Realizacje), the hero script writes 4,9 with a comma on Polish pages (PR #55),
    // the card styles have a new file version, and the Studio wyceny pages carry a new header style (PR #56).
    // Copies made by the old version would be served until they expire (1800 s), so the first new request empties
    // this module's own HTML cache (never LiteSpeed or the CDN); a purge queued by a step above is not repeated.
    add_action('wp_loaded', static function (): void { do_action('zp_html_cache_purge'); }, 100);
  }
  if ($previous !== '' && version_compare($previous, '3.2.59', '<')) {
    // 3.2.59: the service chooser iframe on the Studio wyceny pages carries the site's rating (data-zp-rating, PR #58),
    // which the chooser shows next to the Trustindex logo instead of 5.0; without it the English page would show 4,9.
    // Copies made by the old version would be served until they expire (1800 s), so the first new request empties
    // this module's own HTML cache (never LiteSpeed or the CDN); a purge queued by a step above is not repeated.
    add_action('wp_loaded', static function (): void { do_action('zp_html_cache_purge'); }, 100);
  }
  if ($previous !== '' && version_compare($previous, '3.2.60', '<')) {
    // 3.2.60: a new advanced-cache.php that answers 503 with Retry-After while Suite's own folder is being replaced
    // (PR #60). A purge puts it in place (ZP_HTML_Cache_20261009::purge → upgrade_dropin), only over this module's own
    // drop-in; the install's own upgrader_process_complete purge usually did it already, and a second purge queued by
    // a step above in the same request is not repeated. Page HTML does not change.
    add_action('wp_loaded', static function (): void { do_action('zp_html_cache_purge'); }, 100);
  }
  if ($previous !== '' && version_compare($previous, '3.2.61', '<')) {
    // 3.2.61: the meta tags of English pages listed in ahrefs-20261022.php change (PR #62): a title or description
    // that fits in PLN but not once its prices are in dollars gets the listed text, and the brand strategy post gets
    // its listed description instead of a 191-character manual override.
    // Copies made by the old version would be served until they expire (1800 s), so the first new request empties
    // this module's own HTML cache (never LiteSpeed or the CDN); a purge queued by a step above is not repeated.
    add_action('wp_loaded', static function (): void { do_action('zp_html_cache_purge'); }, 100);
  }
  if ($previous !== '' && version_compare($previous, '3.2.63', '<')) {
    // 3.2.63: scoped graph/OG changes, service guide accordions and portfolio hero HTML.
    // Refresh only Suite's own HTML copies; preserve the 3.2.62 cache purge rules and drop-in.
    add_action('wp_loaded', static function (): void { do_action('zp_html_cache_purge'); }, 100);
  }
  if ($previous !== '' && version_compare($previous, '3.2.64', '<')) {
    // 3.2.64: smaller equivalent CSS/HTML and versioned full-size case-study images.
    // Refresh only Suite's HTML copies; retain the 3.2.62 purge causes and 3.2.60 drop-in.
    add_action('wp_loaded', static function (): void { do_action('zp_html_cache_purge'); }, 100);
  }
  if ($previous !== '' && version_compare($previous, '3.2.65', '<')) {
    // Refresh Suite HTML after the knowledge/editorial and article sidebar presentation update.
    add_action('wp_loaded', static function (): void { do_action('zp_html_cache_purge'); }, 100);
  }
  if ($previous !== '' && version_compare($previous, '3.2.66', '<')) {
    // Refresh only Suite HTML for the case-study and portfolio presentation update.
    add_action('wp_loaded', static function (): void { do_action('zp_html_cache_purge'); }, 100);
  }
  if ($previous !== '' && version_compare($previous, '3.2.67', '<')) {
    // Refresh only Suite HTML after the project widgets and early portfolio link update.
    add_action('wp_loaded', static function (): void { do_action('zp_html_cache_purge'); }, 100);
  }
  if ($previous !== '' && version_compare($previous, '3.2.68', '<')) {
    // Refresh Suite HTML so the header stylesheet and drawer controller arrive together.
    add_action('wp_loaded', static function (): void { do_action('zp_html_cache_purge'); }, 100);
  }
  if ($previous !== '' && version_compare($previous, '3.2.69', '<')) {
    add_action('wp_loaded', static function (): void { do_action('zp_html_cache_purge'); }, 100);
  }
  if ($previous !== '' && version_compare($previous, '3.2.72', '<')) {
    add_action('wp_loaded', static function (): void { do_action('zp_html_cache_purge'); }, 100);
  }
  if ($previous !== '' && version_compare($previous, '3.2.73', '<')) {
    // Refresh cached hero CSS, article links and the reviewed portfolio translations.
    add_action('wp_loaded', static function (): void { do_action('zp_html_cache_purge'); }, 100);
  }
  if ($previous !== '' && version_compare($previous, '3.2.74', '<')) {
    // Refresh article connections after recovering the published campaigns service route.
    add_action('wp_loaded', static function (): void { do_action('zp_html_cache_purge'); }, 100);
  }
  if ($previous !== '' && version_compare($previous, '3.2.75', '<')) {
    // Refresh the home technology strip and glass portfolio labels in both languages.
    add_action('wp_loaded', static function (): void { do_action('zp_html_cache_purge'); }, 100);
  }
  if ($previous !== '' && version_compare($previous, '3.2.76', '<')) {
    // Refresh full technology wordmarks; compiled first-screen CSS now has a persistent cache.
    add_action('wp_loaded', static function (): void { do_action('zp_html_cache_purge'); }, 100);
  }
  if ($previous !== '' && version_compare($previous, '3.2.77', '<')) {
    // Refresh first-screen CSS delivery and viewport-aware home controllers.
    add_action('wp_loaded', static function (): void { do_action('zp_html_cache_purge'); }, 100);
  }
  if ($previous !== '' && version_compare($previous, '3.2.79', '<')) {
    // Refresh non-measuring viewport guards and the scoped language-switch theme initialization.
    add_action('wp_loaded', static function (): void { do_action('zp_html_cache_purge'); }, 100);
  }
  if ($previous !== '' && version_compare($previous, '3.2.80', '<')) {
    // Refresh the owned cache/drop-in for background warming and viewport-scoped CSS delivery.
    add_action('wp_loaded', static function (): void { do_action('zp_html_cache_purge'); }, 100);
  }
  if ($previous !== '' && version_compare($previous, '3.2.81', '<')) {
    // Refresh scoped initial transitions and the exact desktop portfolio payload asset.
    add_action('wp_loaded', static function (): void { do_action('zp_html_cache_purge'); }, 100);
  }
  if ($previous !== '' && version_compare($previous, '3.2.82', '<')) {
    // Refresh the exact home jQuery dependency chain without parser blocking.
    add_action('wp_loaded', static function (): void { do_action('zp_html_cache_purge'); }, 100);
  }
  if ($previous !== '' && version_compare($previous, '3.2.83', '<')) {
    // Refresh the exact exit-gesture initializer without a parser-time layout.
    add_action('wp_loaded', static function (): void { do_action('zp_html_cache_purge'); }, 100);
  }
  if ($previous !== '' && version_compare($previous, '3.2.84', '<')) {
    // Refresh exact same-position stylesheet delivery without render-blocking requests.
    add_action('wp_loaded', static function (): void { do_action('zp_html_cache_purge'); }, 100);
  }
  if ($previous !== '' && version_compare($previous, '3.2.85', '<')) {
    // Refresh stable hero parsing, nearby contact geometry and light mobile floats.
    add_action('wp_loaded', static function (): void { do_action('zp_html_cache_purge'); }, 100);
  }
  if ($previous !== '' && version_compare($previous, '3.2.86', '<')) {
    // Keep artwork paint early; create only decorative rings after settled hero flow.
    add_action('wp_loaded', static function (): void { do_action('zp_html_cache_purge'); }, 100);
  }
  if ($previous !== '' && version_compare($previous, '3.2.87', '<')) {
    // Keep desktop rings in the parser and attach mobile decorations without font geometry reads.
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
