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
  $logo = 'https://zaprojektowani.com/wp-content/uploads/2026/05/zaproktowani_znak_wodny_czarny.png';
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

/** Service node for the current service page, or null. */
function zp_seo_plan_service_node(): ?array {
  if (!function_exists('zp_seo_service_variant')) { return null; }
  $variant = zp_seo_service_variant();
  if ($variant === '') { return null; }
  $path = '';
  foreach (zp_seo_service_registry() as $p => $r) { if ($r['variant'] === $variant) { $path = $p; break; } }
  if ($path === '') { return null; }
  $entry = zp_seo_plan_entry($path) ?? [];
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
  $local = substr($variant, -6) === '-local';
  $url = home_url($path);
  $site = home_url('/');
  return (array) apply_filters('zp_seo_plan_service_node', [
    '@type' => 'Service',
    '@id' => $url . '#service',
    'name' => $names[$variant][0],
    'serviceType' => $names[$variant][1],
    'description' => (string) ($entry['description'] ?? ''),
    'url' => $url,
    'provider' => ['@id' => $site . '#organization'],
    'areaServed' => $local
      ? [['@type' => 'City', 'name' => 'Katowice'], ['@type' => 'AdministrativeArea', 'name' => 'województwo śląskie']]
      : ['@type' => 'Country', 'name' => 'Polska'],
  ], $variant);
}

/** Keeps one business entity in a graph and adds the Service node. */
function zp_seo_plan_filter_graph(array $graph): array {
  $site = home_url('/');
  $business = zp_seo_plan_business_node();
  $out = [];
  $placed = false;
  foreach ($graph as $key => $node) {
    if (!is_array($node)) { $out[$key] = $node; continue; }
    $types = array_map('strval', (array) ($node['@type'] ?? []));
    $id = (string) ($node['@id'] ?? '');
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
  $service = zp_seo_plan_service_node();
  if ($service) { $out['zpService'] = $service; }
  return $out;
}

add_filter('rank_math/json_ld', function ($data, $jsonld = null) {
  if (!zp_seo_plan_active() || !is_array($data) || is_admin()) { return $data; }
  $GLOBALS['zp_seo_plan_schema_done'] = true;
  return zp_seo_plan_filter_graph($data);
}, 98, 2);

// Without Rank Math schema output, print the same nodes once.
add_action('wp_footer', function () {
  if (!zp_seo_plan_active() || !empty($GLOBALS['zp_seo_plan_schema_done']) || is_admin() || is_404()) { return; }
  if (defined('RANK_MATH_VERSION') && !is_front_page() && zp_seo_plan_service_node() === null) { return; }
  $graph = [];
  if (is_front_page() || !defined('RANK_MATH_VERSION')) { $graph[] = zp_seo_plan_business_node(); }
  $service = zp_seo_plan_service_node();
  if ($service) { $graph[] = $service; }
  if (!$graph) { return; }
  echo '<script type="application/ld+json" id="zp-seo-plan-schema">' . wp_json_encode(['@context' => 'https://schema.org', '@graph' => $graph], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
}, 5);
