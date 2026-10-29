<?php
if (!defined('ABSPATH')) { exit; }

/**
 * ZAPROJEKTOWANI SUITE — wspólne hero podstron usługowych (2.2.705)
 *
 * Ładuje assets/css/blocks/service-hero-global.css na pięciu podstronach:
 * logo-branding-katowice, kampanie-reklamowe, strony-internetowe-katowice,
 * wiedza i kontakt.
 *
 * Arkusz idzie z bardzo późnym priorytetem, żeby na pewno wypadł PO arkuszu
 * konkretnej podstrony (logo-branding-katowice.css / strony-internetowe-katowice.css
 * / campaigns.css) i mógł skorygować ich lokalne wartości.
 */

function zp_suite_service_hero_pages(){
  return [
    'logo-branding-katowice',
    'kampanie-reklamowe',
    'strony-internetowe-katowice',
    'wiedza',
    'kontakt',
  ];
}

function zp_suite_is_service_hero_request(){
  $uri = isset($_SERVER['REQUEST_URI']) ? (string) wp_unslash($_SERVER['REQUEST_URI']) : '';
  if ($uri === '') { return false; }
  // 2.6.1: the nationwide and content pages built on the website and logo templates get the same hero.
  if (function_exists('zp_suite_service_kind') && in_array(zp_suite_service_kind($uri), ['strony', 'logo'], true)) { return true; }
  foreach (zp_suite_service_hero_pages() as $slug) {
    if (strpos($uri, '/' . $slug) !== false) { return true; }
  }
  return false;
}

add_action('wp_enqueue_scripts', function(){
  if (is_admin() || !zp_suite_is_service_hero_request()) { return; }

  $rel = 'assets/css/blocks/service-hero-global.css';
  $file = ZP_SUITE_PATH . $rel;
  if (!file_exists($file)) { return; }

  wp_enqueue_style(
    'zp-suite-service-hero-global',
    ZP_SUITE_URL . $rel,
    [],
    (string) filemtime($file)
  );
}, 9999);

/**
 * Podstrona /kampanie-reklamowe/ renderuje się z własnego szablonu
 * (templates/page-campaigns-route.php) i dopina style ręcznie, więc dokładamy
 * arkusz również tam — na wypadek, gdyby kolejność enqueue wypadła inaczej.
 */
add_action('wp_head', function(){
  if (is_admin() || !zp_suite_is_service_hero_request()) { return; }
  if (wp_style_is('zp-suite-service-hero-global', 'done') || wp_style_is('zp-suite-service-hero-global', 'enqueued')) {
    return;
  }
  $rel = 'assets/css/blocks/service-hero-global.css';
  $file = ZP_SUITE_PATH . $rel;
  if (!file_exists($file)) { return; }
  printf(
    '<link rel="stylesheet" id="zp-suite-service-hero-global-css" href="%s" media="all">' . "\n",
    esc_url(add_query_arg('ver', (string) filemtime($file), ZP_SUITE_URL . $rel))
  );
}, 999);
