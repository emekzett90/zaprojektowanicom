<?php
/**
 * ZP Suite — the server builds each page with far fewer database queries (same HTML).
 *
 * Lighthouse on the live site (8.10.2026) waited about 1 s for the first byte of every page;
 * nothing caches the pages in front of WordPress. A copy of the site ran about 173 queries
 * per page, and about 150 of them came from two places:
 * - about 66 settings read one at a time (version markers of earlier migrations, section
 *   content). They are read in one query when the page starts (zp_speed_prime_options).
 * - zp_seo_plan_link_is_live() asked WordPress, for each of about 27 planned pages, whether
 *   it is published (url_to_postid(): rewrite rules, get_page_by_path(), WP_Query; about
 *   3 queries each). The answers are kept between page views in the option
 *   zp_speed_live_paths and dropped whenever a post or page is saved, changes status, is
 *   trashed or deleted, when permalinks change, after a plugin or theme change, with a new
 *   suite version and after 12 hours.
 * The HTML of every page is byte for byte the same; the copy answers 35–45% sooner.
 *
 * Off switch: the one from 2.9.3 (ZP Suite → Przyspieszenie, option zp_speed_off) also turns
 * this off; ?zp_speed=0 turns it off for one page view, to compare.
 * Diagnostics: ?zp_timing=1 adds a Server-Timing header with the time spent loading WordPress
 * and the plugins, building the page and running the HTML filters, plus the number of queries.
 */
if (!defined('ABSPATH')) { exit; }

/** A page view this file speeds up: a front-end GET/HEAD, not REST, AJAX, cron or the admin. */
function zp_speed_server_on(): bool {
  static $on = null;
  if ($on === null) {
    $uri = (string) ($_SERVER['REQUEST_URI'] ?? '');
    $on = in_array(strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET')), ['GET', 'HEAD'], true)
      && !is_admin()
      && !wp_doing_ajax()
      && !wp_doing_cron()
      && !(defined('WP_CLI') && WP_CLI)
      && !(defined('XMLRPC_REQUEST') && XMLRPC_REQUEST)
      && strpos($uri, '/wp-json/') === false
      && !isset($_GET['rest_route'])
      && !(isset($_GET['zp_speed']) && (string) $_GET['zp_speed'] === '0')
      && zp_speed_enabled();
  }
  return $on;
}

/*
 * Settings that every page reads one by one (measured on 46 pages of the copy: each of these
 * on at least 42 of them). Autoloaded ones are skipped by WordPress, so on a site where some
 * of them are autoloaded this costs nothing. Settings read only by some pages (section content
 * of 25–240 KB) are left out on purpose, so no page loads more than it did.
 */
function zp_speed_prime_options(): void {
  $names = [
    'can_compress_scripts', 'site_logo',
    'zpl_extra_attrs', 'zpl_extra_schema', 'zpl_overrides_version', 'zpl_routes', 'zpl_settings', 'zpte_model_migrated',
    'zp_ai_profiles', 'zp_ai_version', 'zp_feed_settings', 'zp_feed_status', 'zp_seo_plan_migrated', 'zp_seo_plan_paused',
    'zp_speed_att_ids', 'zp_speed_live_paths',
    'zp_suite_campaigns_route_version', 'zp_suite_command_center', 'zp_suite_content_version', 'zp_suite_design_system',
    'zp_suite_front_fixes_181_done', 'zp_suite_front_fixes_182_done', 'zp_suite_front_fixes_183_done', 'zp_suite_front_fixes_237_v397',
    'zp_suite_home_faq_ai_seo_migration_2_2_109', 'zp_suite_home_faq_seo_migration_2_2_107', 'zp_suite_home_seo_migration_2_2_107',
    'zp_suite_home_visual_patch_2_2_117', 'zp_suite_katowice_hero_497_version', 'zp_suite_katowice_page', 'zp_suite_katowice_sections',
    'zp_suite_katowice_seed_version', 'zp_suite_katowice_stage2_seo_207_version', 'zp_suite_katowice_stage2_seo_208_version',
    'zp_suite_logo_branding_katowice_hero_live_version', 'zp_suite_options', 'zp_suite_outtech_home_migration',
    'zp_suite_realizacje_content_version', 'zp_suite_realizacje_outtech_migration', 'zp_suite_realizacje_route_version',
    'zp_suite_realizacje_shothome_migration', 'zp_suite_reviews_counts_2717', 'zp_suite_security_defaults_2_2_575',
    'zp_suite_security_turnstile_delivery_2_2_578', 'zp_suite_security_turnstile_fallback_2_2_577', 'zp_suite_security_turnstile_keys_2_2_576',
    'zp_suite_shop_katowice_hero_497_version', 'zp_suite_shop_katowice_hero_antiflash_version', 'zp_suite_shop_katowice_hero_consistency_709_version',
    'zp_suite_shop_katowice_meta_verified_278_version', 'zp_suite_shop_katowice_mock_settings', 'zp_suite_shop_katowice_packages_premium_819_version',
    'zp_suite_shop_katowice_portfolio_swiatgrilli_version', 'zp_suite_shop_katowice_seo_boost_version', 'zp_suite_shop_katowice_stage2_seo_172_version',
    'zp_suite_shop_katowice_stage2_visual_173_version', 'zp_suite_shop_katowice_template_version', 'zp_suite_shop_katowice_trust_280_version',
    'zp_suite_shop_katowice_visual_fix_160_version', 'zp_suite_shop_katowice_visual_fix_161_version', 'zp_suite_shop_katowice_visual_fix_162_version',
    'zp_suite_shothome_home_migration', 'zp_suite_strony_internetowe_katowice_v2_live_version', 'zp_suite_trust_logos_version',
    'zp_suite_trust_logos_version_22103', 'zp_suite_version_done',
  ];
  if (function_exists('wp_prime_option_caches')) {
    wp_prime_option_caches($names);
    return;
  }
  // WordPress before 6.4: what wp_prime_option_caches() does, in one query.
  global $wpdb;
  $all = wp_load_alloptions();
  $todo = [];
  foreach ($names as $name) {
    if (!isset($all[$name]) && wp_cache_get($name, 'options') === false) { $todo[] = $name; }
  }
  if (!$todo) { return; }
  $rows = $wpdb->get_results($wpdb->prepare(
    "SELECT option_name, option_value FROM $wpdb->options WHERE option_name IN (" . implode(',', array_fill(0, count($todo), '%s')) . ')',
    $todo
  ));
  $found = [];
  foreach ((array) $rows as $row) {
    wp_cache_add($row->option_name, $row->option_value, 'options');
    $found[$row->option_name] = true;
  }
  $not = wp_cache_get('notoptions', 'options');
  if (!is_array($not)) { $not = []; }
  foreach ($todo as $name) {
    if (!isset($found[$name])) { $not[$name] = true; }
  }
  wp_cache_set('notoptions', $not, 'options');
}

if (zp_speed_server_on()) {
  zp_speed_prime_options();
}

/*
 * Published or not, for paths asked by zp_seo_plan_link_is_live() (includes/seo-plan/links.php).
 * Stored as ['v' => suite version, 'gen' => change marker, 'until' => expiry, 'paths' => [path => bool]].
 * A change writes a new 'gen' with an empty list; a page view that worked out new answers saves
 * them only if 'gen' is still the one it started with, so an answer from before a change is
 * never kept.
 */
function zp_speed_live_load(): array {
  if (!isset($GLOBALS['zp_speed_live'])) {
    $stored = get_option('zp_speed_live_paths');
    $valid = is_array($stored)
      && ($stored['v'] ?? '') === (defined('ZP_SUITE_VERSION') ? ZP_SUITE_VERSION : '')
      && (int) ($stored['until'] ?? 0) > time()
      && is_array($stored['paths'] ?? null);
    $GLOBALS['zp_speed_live'] = $valid ? $stored['paths'] : [];
    $GLOBALS['zp_speed_live_gen'] = is_array($stored) ? (string) ($stored['gen'] ?? '') : '';
    $GLOBALS['zp_speed_live_until'] = $valid ? (int) $stored['until'] : 0;
    $GLOBALS['zp_speed_live_dirty'] = false;
  }
  return $GLOBALS['zp_speed_live'];
}

/** The stored answer for a path, or null when it has to be worked out. */
function zp_speed_live_get(string $path): ?bool {
  if (!zp_speed_server_on()) { return null; }
  $paths = zp_speed_live_load();
  return array_key_exists($path, $paths) ? (bool) $paths[$path] : null;
}

function zp_speed_live_put(string $path, bool $live): void {
  if (!zp_speed_server_on()) { return; }
  zp_speed_live_load();
  if (count($GLOBALS['zp_speed_live']) >= 500) { return; }
  $GLOBALS['zp_speed_live'][$path] = $live;
  $GLOBALS['zp_speed_live_dirty'] = true;
}

add_action('shutdown', function () {
  if (empty($GLOBALS['zp_speed_live_dirty'])) { return; }
  $GLOBALS['zp_speed_live_dirty'] = false;
  // Read again past the cache: a post saved while this page was built has written a new gen.
  wp_cache_delete('zp_speed_live_paths', 'options');
  $notoptions = wp_cache_get('notoptions', 'options');
  if (is_array($notoptions) && isset($notoptions['zp_speed_live_paths'])) {
    unset($notoptions['zp_speed_live_paths']);
    wp_cache_set('notoptions', $notoptions, 'options');
  }
  $stored = get_option('zp_speed_live_paths');
  $gen = is_array($stored) ? (string) ($stored['gen'] ?? '') : '';
  if ($gen !== $GLOBALS['zp_speed_live_gen']) { return; }
  update_option('zp_speed_live_paths', [
    'v' => defined('ZP_SUITE_VERSION') ? ZP_SUITE_VERSION : '',
    'gen' => $gen,
    'until' => $GLOBALS['zp_speed_live_until'] ?: time() + 12 * HOUR_IN_SECONDS,
    'paths' => $GLOBALS['zp_speed_live'],
  ], false);
}, 50);

function zp_speed_live_clear(): void {
  update_option('zp_speed_live_paths', ['v' => '', 'gen' => uniqid('', true), 'until' => 0, 'paths' => []], false);
  unset($GLOBALS['zp_speed_live']);
}
foreach (['save_post', 'transition_post_status', 'deleted_post', 'trashed_post', 'untrashed_post',
  'update_option_permalink_structure', 'update_option_category_base', 'update_option_tag_base', 'update_option_rewrite_rules',
  'update_option_show_on_front', 'update_option_page_on_front', 'update_option_page_for_posts', 'update_option_home', 'update_option_siteurl',
  'upgrader_process_complete', 'activated_plugin', 'deactivated_plugin', 'switch_theme'] as $zp_speed_live_hook) {
  add_action($zp_speed_live_hook, 'zp_speed_live_clear', 10, 0);
}
unset($zp_speed_live_hook);

/*
 * ?zp_timing=1: a Server-Timing header (visible in the browser's network panel or with curl -I).
 * boot = WordPress and the plugins loaded, init = the plugins' start-up hooks, page = the query
 * and the template, filters = the plugin's HTML filters run after the page is built (sent from the
 * outermost one in speed-293.php, so it covers all of them), db = number of queries.
 */
if (isset($_GET['zp_timing'])) {
  $GLOBALS['zp_speed_marks'] = [];
  foreach (['plugins_loaded' => PHP_INT_MAX, 'wp_loaded' => PHP_INT_MAX] as $zp_speed_hook => $zp_speed_prio) {
    add_action($zp_speed_hook, function () use ($zp_speed_hook) { $GLOBALS['zp_speed_marks'][$zp_speed_hook] = microtime(true); }, $zp_speed_prio);
  }
  add_action('shutdown', function () { $GLOBALS['zp_speed_marks']['shutdown'] = microtime(true); }, -PHP_INT_MAX);
  unset($zp_speed_hook, $zp_speed_prio);
}

/** The Server-Timing value, or '' when ?zp_timing=1 is not on the address. */
function zp_speed_timing_header(): string {
  if (!isset($_GET['zp_timing']) || empty($_SERVER['REQUEST_TIME_FLOAT'])) { return ''; }
  $m = (array) ($GLOBALS['zp_speed_marks'] ?? []);
  $t0 = (float) $_SERVER['REQUEST_TIME_FLOAT'];
  $now = microtime(true);
  $ms = static function ($a, $b) { return number_format(max(0, ($b - $a) * 1000), 1, '.', ''); };
  $parts = [];
  if (isset($m['plugins_loaded'])) { $parts[] = 'boot;dur=' . $ms($t0, $m['plugins_loaded']); }
  if (isset($m['plugins_loaded'], $m['wp_loaded'])) { $parts[] = 'init;dur=' . $ms($m['plugins_loaded'], $m['wp_loaded']); }
  if (isset($m['wp_loaded'], $m['shutdown'])) { $parts[] = 'page;dur=' . $ms($m['wp_loaded'], $m['shutdown']); }
  if (isset($m['shutdown'])) { $parts[] = 'filters;dur=' . $ms($m['shutdown'], $now); }
  $parts[] = 'total;dur=' . $ms($t0, $now);
  $parts[] = 'db;desc="' . (int) ($GLOBALS['wpdb']->num_queries ?? 0) . ' queries"';
  $parts[] = 'zp;desc="' . (zp_speed_server_on() ? 'on' : 'off') . '"';
  return implode(', ', $parts);
}

// Without the outer buffer (switch off) the header goes out before WordPress flushes the page.
add_action('shutdown', function () {
  if (isset($_GET['zp_timing']) && !zp_speed_enabled() && !headers_sent()) {
    $value = zp_speed_timing_header();
    if ($value !== '') { header('Server-Timing: ' . $value); }
  }
}, 0);
