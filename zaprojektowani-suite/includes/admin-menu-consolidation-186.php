<?php
if (!defined('ABSPATH')) { exit; }
// The ZP Suite submenu used to be trimmed here to a keep-list; the hidden screens are gone (panel clean-up),
// and includes/admin-panel.php now sets the order.

add_action('wp_head', function(){
  if (is_admin()) return;
  $icon = function_exists('zp_suite_opt') ? zp_suite_opt('brand.logo_light', 'https://zaprojektowani.com/wp-content/uploads/2026/05/zaproktowani_znak_wodny_czarny.png') : 'https://zaprojektowani.com/wp-content/uploads/2026/05/zaproktowani_znak_wodny_czarny.png';
  if ($icon) echo '<link rel="icon" href="'.esc_url($icon).'" sizes="any">' . "\n";
}, 1);
