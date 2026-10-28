<?php
if (!defined('ABSPATH')) { exit; }

/**
 * Indexation clean-up from the audit:
 * - thank-you pages: noindex, out of the sitemaps,
 * - blog category archives (/strony-internetowe/, /sklepy-internetowe/ …): noindex, follow and out
 *   of the sitemap, so they stop competing with the service pages for the same phrases,
 * - pages made for one client (/rutpoz-brief/, /dottore-logo-showcase/): noindex, nofollow and out of the
 *   sitemaps, Polish and English (2.8.0),
 * - Elementor template URLs (?elementor_library=…) return 404 to visitors,
 * - old sitemaps of a previous translation plugin (pl-sitemap.xml, en-sitemap.xml) and the earlier English
 *   maps (sitemap-en.xml, english-sitemap.xml): out of the index and robots.txt and redirected to
 *   sitemap_index.xml (before 2.6.4: 410); the English page and post maps are always in the index (2.7.1).
 */

function zp_seo_plan_is_thank_you_path(string $path): bool {
  return (bool) preg_match('~^/dziekujemy[a-z0-9-]*/$~', $path);
}

/**
 * Pages made for one client (questionnaires, presentations), 2.8.0: reachable by link, but noindex and
 * out of the Polish and English sitemaps, and the Tłumacz EN module does not translate them. More can be
 * added with the zp_seo_plan_private_pages filter, or per page in Rank Math (noindex has the same effect).
 */
function zp_seo_plan_private_pages(): array {
  return (array) apply_filters('zp_seo_plan_private_pages', ['/rutpoz-brief/', '/dottore-logo-showcase/']);
}

function zp_seo_plan_is_private_path(string $path): bool {
  return $path !== '' && in_array($path, zp_seo_plan_private_pages(), true);
}

/** Kept out of Google: thank-you pages and private client pages. */
function zp_seo_plan_is_hidden_path(string $path): bool {
  return zp_seo_plan_is_thank_you_path($path) || zp_seo_plan_is_private_path($path);
}

/** Old language sitemaps: pl-/en-sitemap.xml (previous translation plugin), sitemap-pl/-en.xml and
 * english-sitemap.xml (the single English map of 2.6.4–2.7.0). */
const ZP_SEO_PLAN_OLD_SITEMAPS = '(?:(?:pl|en)-sitemap|sitemap-(?:pl|en)|english-sitemap)\.xml';

/** English sitemaps of the languages module: type => URL (empty without the module). */
function zp_seo_plan_en_sitemap_urls(): array {
  if (method_exists('ZPL\\Sitemap', 'urls')) { return \ZPL\Sitemap::urls(); }
  return method_exists('ZPL\\Sitemap', 'url') ? ['page' => \ZPL\Sitemap::url()] : [];
}

// 2.6.4: the old maps redirect to live ones (before: 410), so old Search Console entries and manual
// checks land on a working map. Since 2.7.1 both go to the index, which lists the Polish and English maps.
add_action('init', function () {
  if (!zp_seo_plan_active() || is_admin()) { return; }
  $path = zp_seo_plan_path();
  if ($path !== '/pl-sitemap.xml' && $path !== '/en-sitemap.xml') { return; }
  $to = $path === '/en-sitemap.xml' && !zp_seo_plan_en_sitemap_urls() ? '' : home_url('/sitemap_index.xml');
  if ($to === '') {
    status_header(410);
    header('Content-Type: text/plain; charset=UTF-8');
    header('X-Robots-Tag: noindex');
    echo "410 Gone\n";
    exit;
  }
  wp_redirect($to, 301, 'Zaprojektowani SEO 2.6.4');
  exit;
}, 1);

// Last in line (2.6.4; before: priority 100), because the old plugin adds its lines later than that.
add_filter('robots_txt', function ($output) {
  if (!zp_seo_plan_active()) { return $output; }
  $lines = preg_split('~\r?\n~', (string) $output);
  $lines = array_filter($lines, static function ($line) { return !preg_match('~/' . ZP_SEO_PLAN_OLD_SITEMAPS . '\s*$~i', trim($line)); });
  // The English maps are in sitemap_index.xml (2.7.1), so robots.txt names only the index.
  return implode("\n", $lines);
}, PHP_INT_MAX);

/**
 * The index without entries for the old language sitemaps (see above). The finished index also gets the
 * English maps if nothing listed them: on the live site the English map was missing (2.6.4).
 * $in_handler: called from an output buffer handler, where nothing may render a page (no new buffers).
 */
function zp_seo_plan_strip_gone_sitemaps(string $xml, bool $in_handler = false): string {
  $out = preg_replace('~<sitemap>\s*<loc>[^<]*/' . ZP_SEO_PLAN_OLD_SITEMAPS . '</loc>.*?</sitemap>\s*~is', '', $xml);
  if (!is_string($out)) { return $xml; }
  if (strpos($out, '</sitemapindex>') === false) { return $out; }
  foreach (zp_seo_plan_en_sitemap_urls() as $type => $en) {
    if (strpos($out, '/' . basename($en) . '</loc>') !== false) { continue; }
    $mod = method_exists('ZPL\\Sitemap', 'lastmod') ? \ZPL\Sitemap::lastmod((string) $type, $in_handler) : 0;
    $entry = "\t<sitemap>\n\t\t<loc>" . esc_url($en) . "</loc>\n" . ($mod ? "\t\t<lastmod>" . gmdate('c', $mod) . "</lastmod>\n" : '') . "\t</sitemap>\n";
    $out = str_replace('</sitemapindex>', $entry . '</sitemapindex>', $out);
  }
  return $out;
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
  ob_start(static function ($out) { return is_string($out) ? zp_seo_plan_strip_gone_sitemaps($out, true) : $out; });
}, 0);

add_filter('rank_math/frontend/robots', function ($robots) {
  if (!zp_seo_plan_active() || !is_array($robots)) { return $robots; }
  $path = is_page() ? zp_seo_plan_path(get_permalink()) : '';
  if (is_category() || zp_seo_plan_is_thank_you_path($path)) {
    $robots['index'] = 'noindex';
    $robots['follow'] = 'follow';
  } elseif (zp_seo_plan_is_private_path($path)) {
    $robots['index'] = 'noindex';
    $robots['follow'] = 'nofollow';
  }
  return $robots;
}, 30);

// Without Rank Math (or with its robots meta off) the same rule as a plain tag.
add_action('wp_head', function () {
  if (!zp_seo_plan_active() || defined('RANK_MATH_VERSION')) { return; }
  $path = is_page() ? zp_seo_plan_path(get_permalink()) : '';
  if (is_category() || zp_seo_plan_is_hidden_path($path)) {
    echo '<meta name="robots" content="noindex, ' . (zp_seo_plan_is_private_path($path) ? 'nofollow' : 'follow') . '">' . "\n";
  }
}, 1);

add_filter('rank_math/sitemap/exclude_taxonomy', function ($exclude, $taxonomy) {
  return (zp_seo_plan_active() && $taxonomy === 'category') ? true : $exclude;
}, 10, 2);

add_filter('rank_math/sitemap/entry', function ($url, $type, $object) {
  if (!zp_seo_plan_active() || !is_array($url) || empty($url['loc'])) { return $url; }
  return zp_seo_plan_is_hidden_path(zp_seo_plan_path($url['loc'])) ? false : $url;
}, 10, 3);

// Core sitemaps, in case Rank Math's are switched off.
add_filter('wp_sitemaps_taxonomies', function ($taxonomies) {
  if (zp_seo_plan_active()) { unset($taxonomies['category']); }
  return $taxonomies;
});
add_filter('wp_sitemaps_posts_query_args', function ($args, $post_type) {
  if (!zp_seo_plan_active() || $post_type !== 'page') { return $args; }
  $slugs = array_merge(zp_seo_plan_thank_you_slugs(), array_map(static function ($p) { return trim((string) $p, '/'); }, zp_seo_plan_private_pages()));
  $ids = get_posts(['post_type' => 'page', 'post_status' => 'publish', 'numberposts' => 40, 'fields' => 'ids', 'suppress_filters' => true, 'post_name__in' => $slugs]);
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
