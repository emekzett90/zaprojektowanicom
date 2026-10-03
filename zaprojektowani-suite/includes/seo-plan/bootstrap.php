<?php
if (!defined('ABSPATH')) { exit; }

/**
 * ZP Suite 2.3.0 — keyword plan (thread "Frazy kluczowe i konkurencja", 3.10.2026).
 *
 * - SEO titles, descriptions and focus keywords live in Rank Math (editable there);
 *   code no longer overrides them on the front end.
 * - Nationwide service pages (/tworzenie-stron-internetowych/, /tworzenie-sklepow-internetowych/,
 *   /projektowanie-logo/, /identyfikacja-wizualna/) reuse the Katowice templates;
 *   the Katowice pages become shorter local pages.
 * - Duplicate posts are merged with 301 redirects, old prefixes redirect to current URLs.
 * - One business entity in structured data, cleaner sitemaps and robots rules.
 *
 * 2.4.0 adds the website pages from the content thread (content-pages.php, data/pages.php),
 * /kampanie-reklamowe/ as a Rank Math page, one project count (stats.php) and post authors
 * from the team (authors.php).
 *
 * Narzędzia → Plan SEO shows the migration log and can undo the database changes.
 */

define('ZP_SEO_PLAN_VERSION', '2.4.0');

/** The plan is on unless an administrator paused it (Narzędzia → Plan SEO). */
function zp_seo_plan_active(): bool {
  return get_option('zp_seo_plan_paused') !== '1';
}

/** data/plan.php and data/redirects.php are generated from the keyword plan. */
function zp_seo_plan_data(string $name): array {
  static $cache = [];
  if (!isset($cache[$name])) {
    $file = __DIR__ . '/data/' . basename($name) . '.php';
    $cache[$name] = is_file($file) ? (array) include $file : [];
  }
  return $cache[$name];
}

/** Normalized site-relative path: leading and trailing slash, no query. */
function zp_seo_plan_path($uri = null): string {
  if ($uri === null) { $uri = isset($_SERVER['REQUEST_URI']) ? (string) wp_unslash($_SERVER['REQUEST_URI']) : '/'; }
  $path = (string) wp_parse_url((string) $uri, PHP_URL_PATH);
  $base = rtrim((string) wp_parse_url(home_url('/'), PHP_URL_PATH), '/');
  if ($base !== '' && strpos($path, $base) === 0) { $path = substr($path, strlen($base)); }
  $path = '/' . ltrim(rawurldecode($path), '/');
  $path = preg_replace('~/{2,}~', '/', $path);
  if ($path !== '/' && substr($path, -1) !== '/' && !preg_match('~\.[a-z0-9]{2,5}$~i', $path)) { $path .= '/'; }
  return $path;
}

/** Plan entry (title, description, focus, h1, links) for a path, or null. */
function zp_seo_plan_entry(string $path): ?array {
  $plan = zp_seo_plan_data('plan');
  return isset($plan[$path]) ? $plan[$path] : null;
}

/** True when the visitor sees the English version (zaprojektowani-languages). */
function zp_seo_plan_is_en(): bool {
  return class_exists('\ZPL\Router') && \ZPL\Router::$lang === 'en';
}

require_once __DIR__ . '/guide.php';
require_once __DIR__ . '/content-pages.php';
require_once __DIR__ . '/service-pages.php';
require_once __DIR__ . '/campaigns.php';
require_once __DIR__ . '/articles.php';
require_once __DIR__ . '/posts.php';
require_once __DIR__ . '/redirects.php';
require_once __DIR__ . '/indexation.php';
require_once __DIR__ . '/schema.php';
require_once __DIR__ . '/links.php';
require_once __DIR__ . '/menu.php';
require_once __DIR__ . '/stats.php';
require_once __DIR__ . '/authors.php';
require_once __DIR__ . '/migration.php';
require_once __DIR__ . '/admin.php';
