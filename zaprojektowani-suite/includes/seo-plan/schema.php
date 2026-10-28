<?php
if (!defined('ABSPATH')) { exit; }

/**
 * Structured data: one business entity.
 *
 * Before 2.3.0 the page had up to three graphs with the same @ids (Rank Math, the home
 * graph from seo.php and the Katowice page graph), two business types and no street
 * address. Now Rank Math's graph is the only one and its #organization node becomes a
 * ProfessionalService with the full address and the real social profiles. Service pages
 * get one Service node (nationwide pages: Poland, Katowice pages: Katowice and Silesia).
 */

function zp_seo_plan_business_node(): array {
  $site = home_url('/');
  $logo = 'https://zaprojektowani.com/wp-content/uploads/2026/08/znak_wodny_czarny.png'; // 14.10.2026: the 2026/05 file never existed (Google got 404)
  // Filtered by ai.php (prices as offers, team, more profiles).
  return (array) apply_filters('zp_seo_plan_business_node', [
    '@type' => 'ProfessionalService',
    '@id' => $site . '#organization',
    'name' => 'Zaprojektowani',
    'alternateName' => 'Zaprojektowani.com',
    'url' => $site,
    'logo' => ['@type' => 'ImageObject', '@id' => $site . '#logo', 'url' => $logo, 'contentUrl' => $logo],
    'image' => ['@id' => $site . '#logo'],
    'description' => 'Studio projektowe: strony internetowe, sklepy internetowe WooCommerce, logo i identyfikacja wizualna dla firm z całej Polski.',
    'email' => 'kontakt@zaprojektowani.com',
    'telephone' => '+48501054253',
    'vatID' => 'PL9930682613',
    'address' => [
      '@type' => 'PostalAddress',
      'streetAddress' => 'ul. Modelarska 18/2',
      'postalCode' => '40-142',
      'addressLocality' => 'Katowice',
      'addressRegion' => 'śląskie',
      'addressCountry' => 'PL',
    ],
    'areaServed' => ['@type' => 'Country', 'name' => 'Polska'],
    'knowsAbout' => ['tworzenie stron internetowych', 'tworzenie sklepów internetowych', 'WooCommerce', 'projektowanie logo', 'identyfikacja wizualna', 'branding'],
    'hasMap' => 'https://www.google.com/maps/place/?q=place_id:ChIJh0fbrHpbFkcRPEegRF9aX_I',
    'foundingDate' => '2022',
    'openingHoursSpecification' => [[
      '@type' => 'OpeningHoursSpecification',
      'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
      'opens' => '09:00',
      'closes' => '17:00',
    ]],
    'sameAs' => ['https://www.instagram.com/zaprojektowanicom', 'https://www.facebook.com/zaprojektowanicom'],
  ]);
}

/** Service variant => [name, serviceType] of its Service node (content pages: their own 'schema'). */
function zp_seo_plan_service_names(string $variant): ?array {
  $names = [
    'strony-national' => ['Tworzenie stron internetowych', 'Projektowanie i tworzenie stron internetowych'],
    'sklepy-national' => ['Tworzenie sklepów internetowych', 'Tworzenie sklepów internetowych WooCommerce'],
    'logo-national' => ['Projektowanie logo', 'Projektowanie logo i logotypów'],
    'identity-national' => ['Identyfikacja wizualna', 'Projektowanie identyfikacji wizualnej i branding'],
    'strony-local' => ['Strony internetowe Katowice', 'Projektowanie i tworzenie stron internetowych'],
    'sklepy-local' => ['Sklepy internetowe Katowice', 'Tworzenie sklepów internetowych WooCommerce'],
    'logo-local' => ['Logo i branding Katowice', 'Projektowanie logo i identyfikacji wizualnej'],
  ];
  $content = zp_seo_content_page_by_variant($variant);
  if ($content) { $names[$variant] = [$content['schema']['name'], $content['schema']['serviceType']]; }
  return $names[$variant] ?? null;
}

/** Service node for the current service page, or null. */
function zp_seo_plan_service_node(): ?array {
  if (!function_exists('zp_seo_service_variant')) { return null; }
  $variant = zp_seo_service_variant();
  if ($variant === '') { return null; }
  $path = '';
  foreach (zp_seo_service_registry() as $p => $r) { if ($r['variant'] === $variant) { $path = $p; break; } }
  if ($path === '') { return null; }
  $names = zp_seo_plan_service_names($variant);
  if (!$names) { return null; }
  $entry = zp_seo_plan_entry($path) ?? [];
  $local = substr($variant, -6) === '-local';
  $url = home_url($path);
  $site = home_url('/');
  return (array) apply_filters('zp_seo_plan_service_node', [
    '@type' => 'Service',
    '@id' => $url . '#service',
    'name' => $names[0],
    'serviceType' => $names[1],
    'description' => (string) ($entry['description'] ?? ''),
    'url' => $url,
    'provider' => ['@id' => $site . '#organization'],
    'areaServed' => $local
      ? [['@type' => 'City', 'name' => 'Katowice'], ['@type' => 'AdministrativeArea', 'name' => 'województwo śląskie']]
      : ['@type' => 'Country', 'name' => 'Polska'],
  ], $variant);
}

/**
 * The post's category for its breadcrumbs: the one in its address, under the name and at the address of its topic in
 * the knowledge base (blog-knowledge.php), because an alias category redirects (301) to the topic's first category.
 */
function zp_seo_plan_breadcrumb_category(int $id, string $url): ?array {
  $reviewed = apply_filters('zp_seo_plan_breadcrumb_category', null, $id, $url);
  if (is_array($reviewed)) { return $reviewed; }
  $segments = explode('/', trim(zp_seo_plan_path($url), '/'));
  $term = count($segments) > 1 ? get_term_by('slug', $segments[count($segments) - 2], 'category') : null;
  if (!$term instanceof WP_Term) {
    $cats = get_the_category($id);
    $term = $cats ? $cats[0] : null;
  }
  if (!$term instanceof WP_Term) { return null; }
  $name = (string) $term->name;
  $link = get_category_link($term);
  foreach (function_exists('zp_suite_wiedza_topic_groups') ? zp_suite_wiedza_topic_groups() : [] as $group) {
    if (!in_array($term->slug, (array) $group['slugs'], true)) { continue; }
    foreach ((array) $group['slugs'] as $slug) {
      $primary = get_term_by('slug', $slug, 'category');
      if ($primary instanceof WP_Term) { $name = (string) $group['label']; $link = get_category_link($primary); break; }
    }
    break;
  }
  return is_string($link) && $link !== '' ? ['name' => $name, 'url' => $link] : null;
}

/** Breadcrumb name of a service page (the name of its Service node), or ''. */
function zp_seo_plan_breadcrumb_service(string $path): string {
  if ($path === '/kampanie-reklamowe/') { return 'Kampanie reklamowe'; }
  $variant = function_exists('zp_seo_service_registry') ? (string) (zp_seo_service_registry()[$path]['variant'] ?? '') : '';
  return $variant !== '' ? (string) (zp_seo_plan_service_names($variant)[0] ?? '') : '';
}

/**
 * SearchFit 14.10, point 4: BreadcrumbList like the visible breadcrumbs. Posts: Strona główna › Wiedza › category,
 * service pages: Strona główna › service. In Polish, like the rest of the JSON-LD: on English pages the languages
 * module translates the names and addresses (the service names are the ones of the page's Service node). Null elsewhere.
 */
function zp_seo_plan_breadcrumb_node(): ?array {
  if (!is_singular(['post', 'page']) || is_front_page()) { return null; }
  $id = (int) get_queried_object_id();
  $url = $id ? (string) get_permalink($id) : '';
  if ($url === '') { return null; }
  $items = [['Strona główna', home_url('/')]];
  if (is_singular('post')) {
    $items[] = ['Wiedza', home_url('/wiedza/')];
    $category = zp_seo_plan_breadcrumb_category($id, $url);
    if ($category) { $items[] = [$category['name'], $category['url']]; }
  } else {
    $name = zp_seo_plan_breadcrumb_service(zp_seo_plan_path($url));
    if ($name === '') { return null; }
    $items[] = [$name, $url];
  }
  $list = [];
  foreach ($items as $i => $item) {
    $list[] = ['@type' => 'ListItem', 'position' => $i + 1, 'name' => $item[0], 'item' => $item[1]];
  }
  return ['@type' => 'BreadcrumbList', '@id' => $url . '#breadcrumb', 'itemListElement' => $list];
}

/** Keeps one business entity in a graph and adds the Service node. */
function zp_seo_plan_filter_graph(array $graph): array {
  $site = home_url('/');
  $business = zp_seo_plan_business_node();
  $service = zp_seo_plan_service_node();
  $replaceSavedService = $service !== null && is_singular('page');
  $removePageArticle = is_singular('page') && ($service !== null || is_front_page());
  $out = [];
  $placed = false;
  foreach ($graph as $key => $node) {
    if (!is_array($node)) { $out[$key] = $node; continue; }
    $types = array_map('strval', (array) ($node['@type'] ?? []));
    $id = (string) ($node['@id'] ?? '');
    // Pages describing the studio or a registered service are WebPage/Service, not articles.
    // A saved per-page Article can survive Rank Math's global page default being set to None.
    if ($removePageArticle && array_intersect($types, ['Article', 'BlogPosting', 'NewsArticle'])) { continue; }
    // Saved Rank Math Service nodes duplicate the generated #service on registry service pages.
    // Keep other schemas and saved Services on posts or pages without a generated replacement.
    // The homepage keeps its existing rule: its offers already live in the business node.
    if (in_array('Service', $types, true) && (strpos((string) $key, 'schema-') === 0 || preg_match('~#schema-\d+$~', $id)) && (is_front_page() || $replaceSavedService)) { continue; }
    $isBusiness = array_intersect($types, ['Organization', 'LocalBusiness', 'ProfessionalService', 'Corporation']) || preg_match('~#(organization|localbusiness|business)$~', $id);
    if ($isBusiness && (strpos($id, rtrim($site, '/')) === 0 || $id === '')) {
      if ($placed) { continue; }
      $out[$key] = $business;
      $placed = true;
      continue;
    }
    // Remove inline copies of the business inside other nodes.
    foreach (['publisher', 'provider', 'author', 'about'] as $ref) {
      if (isset($node[$ref]['@id']) && preg_match('~#(localbusiness|business)$~', (string) $node[$ref]['@id'])) {
        $node[$ref] = ['@id' => $site . '#organization'];
      }
    }
    $out[$key] = $node;
  }
  if (!$placed) { $out['zpBusiness'] = $business; }
  if ($service) { $out['zpService'] = $service; }
  // The six audited inline duplicates are safe to remove only after a current Rank Math Article exists.
  // Content rendered before wp_head has no marker and keeps its original schema.
  if (function_exists('zp_seo_posts_duplicate_article_source') && is_singular('post')) {
    $post_id = (int) get_queried_object_id();
    $canonical = $post_id ? zp_seo_posts_schema_path((string) get_permalink($post_id)) : '';
    $source = zp_seo_posts_duplicate_article_source($canonical);
    if ($source !== '' && get_post_status($post_id) === 'publish') {
      foreach ($out as $node) {
        if (is_array($node) && zp_seo_posts_current_rankmath_article($node, $canonical)) { $GLOBALS['zp_seo_posts_rankmath_article'][$source] = true; break; }
      }
    }
  }
  return $out;
}

add_filter('rank_math/json_ld', function ($data, $jsonld = null) {
  if (!zp_seo_plan_active() || !is_array($data) || is_admin()) { return $data; }
  $GLOBALS['zp_seo_plan_schema_done'] = true;
  return zp_seo_plan_filter_graph($data);
}, 98, 2);

/** An Offer without any price: only availability, currency or address (point 8). One with its own content stays. */
function zp_seo_plan_empty_offer(array $node): bool {
  if (!array_intersect(array_map('strval', (array) ($node['@type'] ?? [])), ['Offer', 'AggregateOffer'])) { return false; }
  foreach ($node as $key => $value) {
    if (in_array($key, ['price', 'lowPrice', 'highPrice', 'priceSpecification'], true) && $value !== '' && $value !== null && $value !== []) { return false; }
    if (!in_array($key, ['@type', '@id', 'price', 'lowPrice', 'highPrice', 'priceSpecification', 'priceCurrency', 'availability', 'url', 'priceValidUntil', 'itemCondition', 'validFrom'], true)) { return false; }
  }
  return true;
}

/**
 * SearchFit 14.10, point 8, at any depth of a node: an Offer without a price goes (saved Rank Math schemas of
 * /studio-wyceny/ and the English Katowice pages, which keep their saved Service), and every Service names its
 * provider: the business node, by reference (an inline copy of it under the same @id becomes the reference).
 */
function zp_seo_plan_tidy_node(array $node, string $org): array {
  foreach ($node as $key => $value) {
    if (!is_array($value)) { continue; }
    if ($value === [] || array_keys($value) === range(0, count($value) - 1)) {
      $kept = [];
      foreach ($value as $item) {
        if (is_array($item) && zp_seo_plan_empty_offer($item)) { continue; }
        $kept[] = is_array($item) ? zp_seo_plan_tidy_node($item, $org) : $item;
      }
      if ($kept || $value === []) { $node[$key] = $kept; } else { unset($node[$key]); }
    } elseif (zp_seo_plan_empty_offer($value)) {
      unset($node[$key]);
    } else {
      $node[$key] = zp_seo_plan_tidy_node($value, $org);
    }
  }
  if (in_array('Service', array_map('strval', (array) ($node['@type'] ?? [])), true)) {
    $provider = $node['provider'] ?? null;
    if ($provider === null || $provider === '' || $provider === []) {
      $node['provider'] = ['@id' => $org];
    } elseif (is_array($provider) && count($provider) > 1 && is_string($provider['@id'] ?? null) && strpos($provider['@id'], home_url('/')) === 0 && preg_match('~#(organization|localbusiness|business)$~', $provider['@id'])) {
      $node['provider'] = ['@id' => $org];
    }
  }
  return $node;
}

/** Last pass, after the content modules (120, 125) added their nodes: one BreadcrumbList (point 4), offers and providers (point 8). */
function zp_seo_plan_finish_graph(array $graph): array {
  $org = home_url('/') . '#organization';
  foreach ($graph as $key => $node) {
    if (!is_array($node)) { continue; }
    if (zp_seo_plan_empty_offer($node)) { unset($graph[$key]); continue; }
    $graph[$key] = zp_seo_plan_tidy_node($node, $org);
  }
  $crumbs = zp_seo_plan_breadcrumb_node();
  if ($crumbs) {
    // Ours replaces any other, e.g. Rank Math's own when its breadcrumbs get switched on.
    foreach ($graph as $key => $node) {
      if (is_array($node) && in_array('BreadcrumbList', array_map('strval', (array) ($node['@type'] ?? [])), true)) { unset($graph[$key]); }
    }
    $webpage = substr($crumbs['@id'], 0, -strlen('#breadcrumb')) . '#webpage';
    foreach ($graph as $key => $node) {
      if (is_array($node) && ($node['@id'] ?? '') === $webpage) { $graph[$key]['breadcrumb'] = ['@id' => $crumbs['@id']]; }
    }
    $graph['BreadcrumbList'] = $crumbs;
  }
  return $graph;
}

add_filter('rank_math/json_ld', function ($data, $jsonld = null) {
  if (!zp_seo_plan_active() || !is_array($data) || is_admin()) { return $data; }
  return zp_seo_plan_finish_graph($data);
}, 210, 2);

// Without Rank Math schema output, print the same nodes once.
add_action('wp_footer', function () {
  if (!zp_seo_plan_active() || !empty($GLOBALS['zp_seo_plan_schema_done']) || is_admin() || is_404()) { return; }
  if (defined('RANK_MATH_VERSION') && !is_front_page() && zp_seo_plan_service_node() === null) { return; }
  $graph = [];
  if (is_front_page() || !defined('RANK_MATH_VERSION')) { $graph[] = zp_seo_plan_business_node(); }
  $service = zp_seo_plan_service_node();
  if ($service) { $graph[] = $service; }
  $crumbs = zp_seo_plan_breadcrumb_node();
  if ($graph && $crumbs) { $graph[] = $crumbs; }
  if (!$graph) { return; }
  echo '<script type="application/ld+json" id="zp-seo-plan-schema">' . wp_json_encode(['@context' => 'https://schema.org', '@graph' => $graph], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
}, 5);
