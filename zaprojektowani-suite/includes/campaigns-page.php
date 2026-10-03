<?php
/**
 * Niezależny routing podstrony /kampanie-reklamowe/.
 *
 * Trasa ma pierwszeństwo przed kategoriami i innymi regułami WordPressa,
 * dzięki czemu działa również wtedy, gdy kategoria blogowa ma podobny slug.
 */
if (!defined('ABSPATH')) { exit; }

function zp_suite_campaigns_request_path(){
  $uri = isset($_SERVER['REQUEST_URI']) ? (string) wp_unslash($_SERVER['REQUEST_URI']) : '';
  $path = (string) wp_parse_url($uri, PHP_URL_PATH);
  if ($path === '') { return '/'; }
  return '/' . trim($path, '/') . '/';
}

function zp_suite_is_campaigns_request(){
  if ((string) get_query_var('zp_campaigns_page', '') === '1') { return true; }
  return zp_suite_campaigns_request_path() === '/kampanie-reklamowe/';
}

function zp_suite_campaigns_register_route(){
  add_rewrite_tag('%zp_campaigns_page%', '([01])');
  add_rewrite_rule('^kampanie-reklamowe/?$', 'index.php?zp_campaigns_page=1', 'top');
}
add_action('init', 'zp_suite_campaigns_register_route', 1);

add_filter('query_vars', function($vars){
  $vars[] = 'zp_campaigns_page';
  return array_values(array_unique($vars));
});

/** Renderuje landing i kończy request, zanim WordPress uruchomi redirecty. */
function zp_suite_campaigns_render_and_exit(){
  $custom = ZP_SUITE_PATH . 'templates/page-campaigns-route.php';
  if (!file_exists($custom)) { return false; }

  global $wp_query;
  if ($wp_query instanceof WP_Query) {
    $wp_query->is_404 = false;
    $wp_query->is_archive = false;
    $wp_query->is_category = false;
    $wp_query->is_page = true;
  }

  status_header(200);
  include $custom;
  exit;
}

/**
 * Najwcześniejsze możliwe przejęcie trasy. Działa przed query, canonical,
 * starymi slugami kategorii, Rank Math i menedżerami przekierowań.
 */
add_action('parse_request', function(){
  if (!is_admin() && zp_suite_campaigns_request_path() === '/kampanie-reklamowe/') {
    zp_suite_campaigns_render_and_exit();
  }
}, PHP_INT_MIN);

/**
 * Zabezpieczenie awaryjne: dla dokładnego adresu kampanii żadne przekierowanie
 * wykonywane przez wp_redirect/wp_safe_redirect nie jest dozwolone.
 */
add_filter('wp_redirect', function($location){
  if (!is_admin() && zp_suite_campaigns_request_path() === '/kampanie-reklamowe/') {
    return false;
  }
  return $location;
}, PHP_INT_MIN, 1);

/** Fallback, gdy środowisko pomija hook parse_request. */
add_action('template_redirect', function(){
  if (!is_admin() && zp_suite_campaigns_request_path() === '/kampanie-reklamowe/') {
    zp_suite_campaigns_render_and_exit();
  }
}, PHP_INT_MIN);

function zp_suite_ensure_campaigns_page(){
  if (!function_exists('get_page_by_path') || !function_exists('wp_insert_post')) { return 0; }

  $page = get_page_by_path('kampanie-reklamowe', OBJECT, 'page');
  if (!$page) {
    /* Uwzględnij również stronę w koszu, która potrafi blokować ponowne użycie slugu. */
    $trashed = get_posts([
      'post_type' => 'page',
      'post_status' => 'trash',
      'name' => 'kampanie-reklamowe',
      'posts_per_page' => 1,
      'fields' => 'ids',
      'suppress_filters' => true,
    ]);
    if (!empty($trashed[0])) {
      wp_untrash_post((int) $trashed[0]);
      $page = get_post((int) $trashed[0]);
    }
  }

  if (!$page) {
    $inserted = wp_insert_post([
      'post_title' => 'Kampanie reklamowe',
      'post_name' => 'kampanie-reklamowe',
      'post_status' => 'publish',
      'post_type' => 'page',
      'post_content' => '',
      'comment_status' => 'closed',
      'ping_status' => 'closed',
    ], true);
    return is_wp_error($inserted) ? 0 : (int) $inserted;
  }

  $updates = ['ID' => (int) $page->ID];
  $needs_update = false;
  if ($page->post_status !== 'publish') {
    $updates['post_status'] = 'publish';
    $needs_update = true;
  }
  if ($page->post_title !== 'Kampanie reklamowe') {
    $updates['post_title'] = 'Kampanie reklamowe';
    $needs_update = true;
  }
  if ($page->post_name !== 'kampanie-reklamowe') {
    $updates['post_name'] = 'kampanie-reklamowe';
    $needs_update = true;
  }
  if ($needs_update) { wp_update_post($updates); }
  return (int) $page->ID;
}

/** Usuń zapisane przekierowania prowadzące z adresu dokładnie na ten sam adres. */
function zp_suite_campaigns_cleanup_redirects(){
  $data = get_option('zp_suite_command_center', []);
  if (!is_array($data) || empty($data['redirects']) || !is_array($data['redirects'])) { return; }

  $clean = [];
  $changed = false;
  foreach ($data['redirects'] as $row) {
    $from = '/' . trim((string) ($row['from'] ?? ''), '/') . '/';
    $to_path = (string) wp_parse_url((string) ($row['to'] ?? ''), PHP_URL_PATH);
    $to = '/' . trim($to_path, '/') . '/';
    if ($from !== '//' && $from === $to) {
      $changed = true;
      continue;
    }
    $clean[] = $row;
  }
  if ($changed) {
    $data['redirects'] = array_values($clean);
    update_option('zp_suite_command_center', $data, false);
  }
}

function zp_suite_campaigns_activate(){
  zp_suite_campaigns_register_route();
  zp_suite_ensure_campaigns_page();
  zp_suite_campaigns_cleanup_redirects();
  flush_rewrite_rules(false);
}

add_action('init', function(){
  $version_key = 'zp_suite_campaigns_route_version';
  if (get_option($version_key) !== '2.2.683') {
    zp_suite_ensure_campaigns_page();
    zp_suite_campaigns_cleanup_redirects();
    update_option($version_key, '2.2.683', false);
    if (function_exists('flush_rewrite_rules')) { flush_rewrite_rules(false); }
  }
}, 31);

/* Nie pozwól WordPressowi potraktować tej trasy jako błędu 404. */
add_filter('pre_handle_404', function($preempt){
  return zp_suite_is_campaigns_request() ? true : $preempt;
}, 1);

/* Wyłącz canonical redirect dla tej jednej, wirtualnej trasy. */
add_filter('redirect_canonical', function($redirect_url){
  return zp_suite_is_campaigns_request() ? false : $redirect_url;
}, 1);

add_action('template_redirect', function(){
  if (is_admin() || !zp_suite_is_campaigns_request()) { return; }
  global $wp_query;
  if ($wp_query instanceof WP_Query) {
    $wp_query->is_404 = false;
    $wp_query->is_archive = false;
    $wp_query->is_category = false;
  }
  status_header(200);
}, -100);

add_filter('template_include', function($template){
  if (!is_admin() && zp_suite_is_campaigns_request()) {
    $custom = ZP_SUITE_PATH . 'templates/page-campaigns-route.php';
    if (file_exists($custom)) { return $custom; }
  }
  return $template;
}, PHP_INT_MAX);

/* Stary adres usługi przekieruj wyłącznie wtedy, gdy nie jest aktualną trasą kampanii. */
add_action('template_redirect', function(){
  if (is_admin()) { return; }
  if (zp_suite_campaigns_request_path() === '/kampanie-meta-ads/') {
    wp_safe_redirect(home_url('/kampanie-reklamowe/'), 301);
    exit;
  }
}, -90);

add_filter('wp_nav_menu_objects', function($items){
  if (!is_array($items)) { return $items; }
  foreach ($items as $item) {
    if (!is_object($item) || empty($item->url)) { continue; }
    $item->url = str_replace('/kampanie-meta-ads/', '/kampanie-reklamowe/', (string) $item->url);
  }
  return $items;
});

function zp_suite_campaigns_replace_legacy_links($html){
  if (!is_string($html) || $html === '') { return $html; }
  return str_replace('/kampanie-meta-ads/', '/kampanie-reklamowe/', $html);
}
add_filter('the_content', 'zp_suite_campaigns_replace_legacy_links', 99);
add_filter('widget_text_content', 'zp_suite_campaigns_replace_legacy_links', 99);

add_filter('body_class', function($classes){
  if (!is_admin() && zp_suite_is_campaigns_request()) {
    $classes[] = 'zp-campaigns-page';
    $classes[] = 'zp-service-hero-page';
  }
  return $classes;
});
