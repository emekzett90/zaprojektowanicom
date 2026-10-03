<?php
if (!defined('ABSPATH')) { exit; }
add_action('admin_menu', function(){
  // Zostawiamy tylko główne pozycje. Narzędzia techniczne są dostępne z Command Center,
  // ale nie zaśmiecają lewego menu WP Admina.
  $keep = ['zp-suite','zp-suite-home-cms','zp-suite-realizacje','zp-suite-faq','zp-suite-pages','zp-suite-shop-katowice','zp-suite-leads','zp-studio-orders','zp-studio-packages'];
  global $submenu;
  if (empty($submenu['zp-suite']) || !is_array($submenu['zp-suite'])) return;
  foreach ($submenu['zp-suite'] as $idx => $item) {
    $slug = $item[2] ?? '';
    if (!in_array($slug, $keep, true)) unset($submenu['zp-suite'][$idx]);
  }
  $submenu['zp-suite'] = array_values($submenu['zp-suite']);
}, 9999);

add_action('wp_head', function(){
  if (is_admin()) return;
  $icon = function_exists('zp_suite_opt') ? zp_suite_opt('brand.logo_light', 'https://zaprojektowani.com/wp-content/uploads/2026/05/zaproktowani_znak_wodny_czarny.png') : 'https://zaprojektowani.com/wp-content/uploads/2026/05/zaproktowani_znak_wodny_czarny.png';
  if ($icon) echo '<link rel="icon" href="'.esc_url($icon).'" sizes="any">' . "\n";
}, 1);
