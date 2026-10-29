<?php
/**
 * Ahrefs 22.10.2026: distinguish service/collection pages from editorial articles.
 *
 * These three native routes had og:type=article and, when Rank Math's global entities were
 * disabled for a page, no WebPage/WebSite node. Do not add an invented Article to silence
 * the audit: /kampanie-reklamowe/ sells a service; /realizacje/ and /wiedza/ list projects
 * and guides. Rank Math remains the only publisher of the graph.
 *
 * ProfessionalService is a deprecated schema.org type. On the audited routes the existing
 * business keeps its @id, facts and references and becomes Organization + LocalBusiness.
 * No page/post/option is saved, no HTTP requests run and the cache drop-in stays unchanged.
 */
if (!defined('ABSPATH')) { exit; }

/** Source paths are Polish even when ZPL translates their public English equivalents. */
function zp_ahrefs_3262_pages(): array {
  return [
    '/kampanie-reklamowe/' => ['type' => 'WebPage', 'name' => 'Kampanie Facebook Ads i Google Ads dla firm', 'service' => 'Kampanie Meta Ads i Google Ads'],
    '/realizacje/' => ['type' => 'CollectionPage', 'name' => 'Realizacje stron internetowych, sklepów i brandingu'],
    '/wiedza/' => ['type' => 'CollectionPage', 'name' => 'Baza wiedzy Zaprojektowani'],
  ];
}

/** No unrelated articles, archives, administration or paused SEO plan are changed. */
function zp_ahrefs_3262_source(): string {
  if (is_admin() || is_archive() || is_search() || is_404()
      || !function_exists('zp_seo_plan_active') || !zp_seo_plan_active()
      || !function_exists('zp_seo_plan_path') || is_singular('post')) { return ''; }
  $id = (int) get_queried_object_id();
  $path = $id > 0 ? zp_seo_plan_path((string) get_permalink($id)) : zp_seo_plan_path();
  return isset(zp_ahrefs_3262_pages()[$path]) ? $path : '';
}

/** Add page identity inside the existing graph, never a second inline schema script. */
function zp_ahrefs_3262_graph(array $graph, string $source): array {
  $row = zp_ahrefs_3262_pages()[$source] ?? null;
  if ($row === null) { return $graph; }
  $site = home_url('/');
  $url = home_url($source);
  $organization = $site . '#organization';
  $website = $site . '#website';
  $page_key = null;
  $website_key = null;
  $service_key = null;
  $list_id = '';
  $offer = null;
  foreach ($graph as $key => &$node) {
    if (!is_array($node)) { continue; }
    $types = (array) ($node['@type'] ?? []);
    $id = (string) ($node['@id'] ?? '');
    $node_url = (string) ($node['url'] ?? '');
    if ($id === $organization) {
      // The same entity: do not create another Organization or change its existing facts.
      $types = array_values(array_diff($types, ['ProfessionalService']));
      $node['@type'] = array_values(array_unique(array_merge(['Organization', 'LocalBusiness'], $types)));
      foreach ((array) ($node['makesOffer'] ?? []) as $candidate) {
        if (is_array($candidate) && (string) ($candidate['url'] ?? '') === $url) { $offer = $candidate; break; }
      }
    }
    if ($id === $website && in_array('WebSite', $types, true)) { $website_key = $key; }
    if (array_intersect($types, ['WebPage', 'CollectionPage', 'AboutPage', 'ContactPage'])
        && ($id === $url . '#webpage' || rtrim($node_url, '/') === rtrim($url, '/'))) { $page_key = $key; }
    if (in_array('Service', $types, true) && ($id === $url . '#service' || $node_url === $url)) { $service_key = $key; }
    if (in_array('ItemList', $types, true) && strpos($id, $url . '#') === 0) { $list_id = $id; }
  }
  unset($node);
  if ($website_key === null) {
    $graph['zpAuditWebSite3262'] = [
      '@type' => 'WebSite', '@id' => $website, 'url' => $site,
      'name' => 'Zaprojektowani', 'publisher' => ['@id' => $organization],
    ];
  }
  $entry = function_exists('zp_seo_plan_entry') ? (zp_seo_plan_entry($source) ?? []) : [];
  if ($page_key === null) { $page_key = 'zpAuditWebPage3262'; }
  $page = isset($graph[$page_key]) && is_array($graph[$page_key]) ? $graph[$page_key] : [];
  $page = array_replace([
    '@id' => $url . '#webpage', 'url' => $url, 'name' => $row['name'], 'inLanguage' => 'pl-PL',
  ], $page);
  $page['@type'] = $row['type'];
  $page['isPartOf'] = ['@id' => $website];
  $page['publisher'] = ['@id' => $organization];
  if (empty($page['description']) && !empty($entry['description'])) { $page['description'] = (string) $entry['description']; }
  if (isset($row['service'])) {
    if ($service_key === null) { $service_key = 'zpAuditService3262'; }
    $service = isset($graph[$service_key]) && is_array($graph[$service_key]) ? $graph[$service_key] : [];
    $service = array_replace([
      '@type' => 'Service', '@id' => $url . '#service', 'url' => $url,
      'name' => $row['service'], 'serviceType' => $row['service'],
    ], $service);
    $service['provider'] = ['@id' => $organization];
    $service['mainEntityOfPage'] = ['@id' => $page['@id']];
    if (empty($service['description']) && !empty($entry['description'])) { $service['description'] = (string) $entry['description']; }
    if (empty($service['offers']) && is_array($offer)) {
      unset($offer['itemOffered']);
      $service['offers'] = $offer;
    }
    $graph[$service_key] = $service;
    $page['mainEntity'] = ['@id' => $service['@id']];
  } elseif ($list_id !== '') {
    $page['mainEntity'] = ['@id' => $list_id];
  }
  $graph[$page_key] = $page;
  return $graph;
}

add_filter('rank_math/json_ld', static function ($graph) {
  if (!is_array($graph) || ($source = zp_ahrefs_3262_source()) === '') { return $graph; }
  return zp_ahrefs_3262_graph($graph, $source);
}, 200);

add_filter('rank_math/opengraph/facebook/og_type', static function ($type) {
  return zp_ahrefs_3262_source() === '' ? $type : 'website';
}, 200);

// article:* applies to editorial articles, not these service/collection pages. Real posts keep it.
foreach (['article_publisher', 'article_author', 'article_published_time', 'article_modified_time', 'article_section', 'article_tag'] as $property) {
  add_filter('rank_math/opengraph/facebook/' . $property, static function ($value) {
    return zp_ahrefs_3262_source() === '' ? $value : false;
  }, 200);
}

/**
 * Legacy shop-topic alias: its first page is a real category archive, but WordPress's
 * legacy rewrite does not recognize /sklepy-internetowe/page/2/. The generic 404 repair
 * then sent that request to page 1 although five more posts exist on the real page 2.
 * Keep article content, queries and noindex policy; only use the real category URLs.
 */
function zp_ahrefs_3262_shop_archive_target(string $path): string {
  if (!preg_match('~^/sklepy-internetowe/(?:page/([1-9][0-9]*)/)?$~D', $path, $m)) { return ''; }
  $term = get_term_by('slug', 'sklepy-internetowe', 'category');
  if (!$term || is_wp_error($term)) { return ''; }
  $base = get_category_link($term);
  if (!is_string($base) || $base === '' || is_wp_error($base)) { return ''; }
  $base = trailingslashit($base);
  // If category-base removal is configured later, do not redirect back to the same alias.
  if (zp_seo_plan_path($base) === '/sklepy-internetowe/') { return ''; }
  $page = isset($m[1]) ? (int) $m[1] : 1;
  return $base . ($page > 1 ? 'page/' . $page . '/' : '');
}

/** Preserve existing query parameters and fragments while replacing only the path. */
function zp_ahrefs_3262_shop_archive_url(string $url): string {
  $target = zp_ahrefs_3262_shop_archive_target(zp_seo_plan_path($url));
  if ($target === '') { return $url; }
  foreach (['query' => '?', 'fragment' => '#'] as $part => $prefix) {
    $value = wp_parse_url($url, $part === 'query' ? PHP_URL_QUERY : PHP_URL_FRAGMENT);
    if (is_string($value) && $value !== '') { $target .= $prefix . $value; }
  }
  return $target;
}

// paginate_links() gets its base from this native WordPress filter. Only the old shop
// category alias matches; all other categories and the canonical archive stay untouched.
add_filter('get_pagenum_link', static function ($url) {
  if (!is_string($url) || is_admin() || !is_category() || !zp_seo_plan_active()) { return $url; }
  return zp_ahrefs_3262_shop_archive_url($url);
}, 20);

// Run before seo-plan/redirects.php (-1000): a legacy page N must lead to page N,
// never page 1. The canonical archive's own query still decides whether N exists.
add_action('template_redirect', static function () {
  if (is_admin() || !zp_seo_plan_active() || wp_doing_ajax() || is_preview()
      || !in_array($_SERVER['REQUEST_METHOD'] ?? 'GET', ['GET', 'HEAD'], true)) { return; }
  $path = zp_seo_plan_path();
  if (!preg_match('~^/sklepy-internetowe/page/[1-9][0-9]*/$~D', $path)) { return; }
  $target = zp_ahrefs_3262_shop_archive_target($path);
  if ($target === '' || headers_sent()) { return; }
  $query = wp_parse_url((string) ($_SERVER['REQUEST_URI'] ?? ''), PHP_URL_QUERY);
  wp_redirect($target . (is_string($query) && $query !== '' ? '?' . $query : ''), 301, 'Zaprojektowani Suite archive pagination');
  exit;
}, -1001);
