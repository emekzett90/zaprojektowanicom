<?php
if (!defined('ABSPATH')) { exit; }

/**
 * Indexation clean-up from the audit:
 * - thank-you pages: noindex, out of the sitemaps,
 * - blog category archives (/strony-internetowe/, /sklepy-internetowe/ …): noindex, follow and out
 *   of the sitemap, so they stop competing with the service pages for the same phrases,
 * - Elementor template URLs (?elementor_library=…) return 404 to visitors,
 * - old sitemaps of a previous translation plugin (pl-sitemap.xml, en-sitemap.xml): 410 and out of robots.txt.
 */

function zp_seo_plan_is_thank_you_path(string $path): bool {
  return (bool) preg_match('~^/dziekujemy[a-z0-9-]*/$~', $path);
}

add_action('init', function () {
  if (!zp_seo_plan_active() || is_admin()) { return; }
  $path = zp_seo_plan_path();
  if ($path === '/pl-sitemap.xml' || $path === '/en-sitemap.xml') {
    status_header(410);
    header('Content-Type: text/plain; charset=UTF-8');
    header('X-Robots-Tag: noindex');
    echo "410 Gone\n";
    exit;
  }
}, 1);

add_filter('robots_txt', function ($output) {
  if (!zp_seo_plan_active()) { return $output; }
  $lines = preg_split('~\r?\n~', (string) $output);
  $lines = array_filter($lines, static function ($line) { return !preg_match('~(?:^|/)(?:pl|en)-sitemap\.xml\s*$~i', trim($line)); });
  return implode("\n", $lines);
}, 100);

/** The index without entries for the old pl/en sitemaps (they return 410 above). */
function zp_seo_plan_strip_gone_sitemaps(string $xml): string {
  $out = preg_replace('~<sitemap>\s*<loc>[^<]*/(?:pl|en)-sitemap\.xml</loc>.*?</sitemap>\s*~is', '', $xml);
  return is_string($out) ? $out : $xml;
}

// A translation plugin that is still active can append its pl/en sitemaps to Rank Math's index (2.6.1).
add_filter('rank_math/sitemap/index', function ($xml) {
  if (!zp_seo_plan_active() || !is_string($xml) || $xml === '') { return $xml; }
  return zp_seo_plan_strip_gone_sitemaps($xml);
}, 999);

// On the live site pl-sitemap.xml and en-sitemap.xml are still listed in sitemap_index.xml, so they come
// in some other way (a sitemap provider of that plugin, or Rank Math's cache). The finished index is
// filtered as a whole, whatever added them (2.6.3).
add_action('init', function () {
  if (!zp_seo_plan_active() || is_admin() || zp_seo_plan_path() !== '/sitemap_index.xml') { return; }
  ob_start(static function ($out) { return is_string($out) ? zp_seo_plan_strip_gone_sitemaps($out) : $out; });
}, 0);

add_filter('rank_math/frontend/robots', function ($robots) {
  if (!zp_seo_plan_active() || !is_array($robots)) { return $robots; }
  $noindex = is_category() || (is_page() && zp_seo_plan_is_thank_you_path(zp_seo_plan_path(get_permalink())));
  if ($noindex) {
    $robots['index'] = 'noindex';
    $robots['follow'] = 'follow';
  }
  return $robots;
}, 30);

// Without Rank Math (or with its robots meta off) the same rule as a plain tag.
add_action('wp_head', function () {
  if (!zp_seo_plan_active() || defined('RANK_MATH_VERSION')) { return; }
  if (is_category() || (is_page() && zp_seo_plan_is_thank_you_path(zp_seo_plan_path(get_permalink())))) {
    echo '<meta name="robots" content="noindex, follow">' . "\n";
  }
}, 1);

add_filter('rank_math/sitemap/exclude_taxonomy', function ($exclude, $taxonomy) {
  return (zp_seo_plan_active() && $taxonomy === 'category') ? true : $exclude;
}, 10, 2);

add_filter('rank_math/sitemap/entry', function ($url, $type, $object) {
  if (!zp_seo_plan_active() || !is_array($url) || empty($url['loc'])) { return $url; }
  return zp_seo_plan_is_thank_you_path(zp_seo_plan_path($url['loc'])) ? false : $url;
}, 10, 3);

// Core sitemaps, in case Rank Math's are switched off.
add_filter('wp_sitemaps_taxonomies', function ($taxonomies) {
  if (zp_seo_plan_active()) { unset($taxonomies['category']); }
  return $taxonomies;
});
add_filter('wp_sitemaps_posts_query_args', function ($args, $post_type) {
  if (!zp_seo_plan_active() || $post_type !== 'page') { return $args; }
  $ids = get_posts(['post_type' => 'page', 'post_status' => 'publish', 'numberposts' => 20, 'fields' => 'ids', 'suppress_filters' => true, 'post_name__in' => zp_seo_plan_thank_you_slugs()]);
  $args['post__not_in'] = array_merge((array) ($args['post__not_in'] ?? []), $ids);
  return $args;
}, 10, 2);

function zp_seo_plan_thank_you_slugs(): array {
  global $wpdb;
  $slugs = wp_cache_get('zp_seo_plan_thank_you_slugs');
  if (!is_array($slugs)) {
    $slugs = (array) $wpdb->get_col("SELECT post_name FROM {$wpdb->posts} WHERE post_type = 'page' AND post_name LIKE 'dziekujemy%'");
    wp_cache_set('zp_seo_plan_thank_you_slugs', $slugs);
  }
  return $slugs ?: ['__none__'];
}

// Elementor library items (header/footer templates) are not pages for visitors.
add_action('template_redirect', function () {
  if (!zp_seo_plan_active() || !is_singular('elementor_library') || current_user_can('edit_posts')) { return; }
  if (isset($_GET['elementor-preview']) || isset($_GET['preview'])) { return; }
  global $wp_query;
  $wp_query->set_404();
  status_header(404);
  nocache_headers();
}, -999);
