<?php
/**
 * ZAPROJEKTOWANI Suite — podstrona O nas / shortcode v2.2.232
 */
if (!defined('ABSPATH')) exit;

function zp_suite_about_page_render(){
  $file = ZP_SUITE_PATH . 'templates/pages/about-page-premium.html';
  if (!file_exists($file)) return '<!-- ZP About Page: template missing -->';
  $html = file_get_contents($file);
  if (function_exists('zp_suite_meta_verified_tokens')) {
    $html = zp_suite_meta_verified_tokens($html);
    $html = str_replace('zpMetaVerified--service', 'zpMetaVerified--about', $html);
  }
  return $html;
}

add_shortcode('zp_o_nas', 'zp_suite_about_page_render');
add_shortcode('zp_page_o_nas', 'zp_suite_about_page_render');
add_shortcode('zp_about_page', 'zp_suite_about_page_render');
