<?php
/** Ensure the approved Realizacje page is available at /realizacje/. */
if (!defined('ABSPATH')) { exit; }

function zp_suite_ensure_realizacje_page_v20(){
  if (!function_exists('get_page_by_path') || !function_exists('wp_insert_post')) { return; }
  $page = get_page_by_path('realizacje', OBJECT, 'page');
  if (!$page) {
    wp_insert_post([
      'post_title' => 'Realizacje',
      'post_name' => 'realizacje',
      'post_status' => 'publish',
      'post_type' => 'page',
      'post_content' => '[zp_realizacje]',
      'comment_status' => 'closed',
    ]);
  }
}
add_action('init', function(){
  if (get_option('zp_suite_realizacje_route_version') !== '2.2.673') {
    zp_suite_ensure_realizacje_page_v20();
    update_option('zp_suite_realizacje_route_version','2.2.673',false);
    if (function_exists('flush_rewrite_rules')) { flush_rewrite_rules(false); }
  }
}, 30);


add_action('wp_enqueue_scripts', function(){
  if (!is_admin() && function_exists('is_page') && is_page('realizacje') && function_exists('zp_suite_enqueue_block_assets')) {
    zp_suite_enqueue_block_assets('zp_realizacje');
  }
}, 5);

add_filter('template_include', function($template){
  if (!is_admin() && function_exists('is_page') && is_page('realizacje')) {
    $custom = ZP_SUITE_PATH . 'templates/page-realizacje-route.php';
    if (file_exists($custom)) { return $custom; }
  }
  return $template;
}, 99);

/**
 * Realizacje v2.2.720 — SEO dla strony kolekcji.
 * Rank Math zachowuje WebPage/Organization/Breadcrumbs; dokładamy tylko ItemList projektów.
 */
function zp_suite_is_realizacje_front_v220(){
  return !is_admin() && function_exists('is_page') && is_page('realizacje');
}

add_filter('rank_math/frontend/title', function($title){
  if (!zp_suite_is_realizacje_front_v220()) { return $title; }
  return 'Realizacje stron, sklepów i brandingu | Zaprojektowani';
}, 30);

add_filter('rank_math/frontend/description', function($description){
  if (!zp_suite_is_realizacje_front_v220()) { return $description; }
  return 'Zobacz realizacje Zaprojektowani: strony internetowe, sklepy WooCommerce, logo i branding. Poznaj zakres, proces i efekty konkretnych projektów.';
}, 30);

add_filter('rank_math/frontend/canonical', function($canonical){
  if (!zp_suite_is_realizacje_front_v220()) { return $canonical; }
  return home_url('/realizacje/');
}, 30);

add_filter('rank_math/json_ld', function($data, $jsonld){
  if (!zp_suite_is_realizacje_front_v220() || !function_exists('zp_suite_realizacje_projects')) { return $data; }

  $projects = zp_suite_realizacje_projects();
  if (empty($projects)) { return $data; }

  $items = [];
  $position = 1;
  foreach ($projects as $project) {
    $brand = trim((string)($project['brand'] ?? ''));
    if ($brand === '') { continue; }
    $slug = sanitize_title($brand . '-' . (string)($project['type'] ?? 'projekt'));
    $items[] = [
      '@type' => 'ListItem',
      'position' => $position++,
      'url' => home_url('/realizacje/#case-' . $slug),
      'name' => $brand,
    ];
    if ($position > 60) { break; }
  }

  if ($items) {
    $data['zp_realizacje_itemlist'] = [
      '@type' => 'ItemList',
      '@id' => home_url('/realizacje/#portfolio'),
      'name' => 'Realizacje Zaprojektowani',
      'numberOfItems' => count($items),
      'itemListOrder' => 'https://schema.org/ItemListOrderDescending',
      'itemListElement' => $items,
    ];
  }

  return $data;
}, 35, 2);
