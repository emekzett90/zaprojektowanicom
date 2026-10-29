<?php
/**
 * ZP Suite v2.2.777 — Logo i branding Katowice: case study art.
 *
 * The case study visual is now the Zgórecki Nieruchomości branding mockup (see templates/logo-branding-katowice/body.html).
 * Its crop is much tighter than the previous stationery render, so the art gets its own size/position per breakpoint.
 */
if (!defined('ABSPATH')) { exit; }

function zp_suite_305_brand_case_art_css(){
  if (is_admin()) { return; }
  ?>
<link rel="stylesheet" id="zp-suite-front-fixes-305-brand-case-art" href="<?php echo esc_url(ZP_SUITE_URL . 'assets/css/front-fixes-305.css'); ?>?ver=<?php echo esc_attr(zp_suite_asset_version('assets/css/front-fixes-305.css')); ?>">
  <?php
}
add_action('wp_head', 'zp_suite_305_brand_case_art_css', PHP_INT_MAX);
add_action('wp_footer', 'zp_suite_305_brand_case_art_css', PHP_INT_MAX);
