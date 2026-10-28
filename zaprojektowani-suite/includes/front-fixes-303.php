<?php
/**
 * ZP Suite v2.2.672 — FAQ visual refinement.
 *
 * Final, page-scoped layer for a clean and consistent FAQ:
 * - plain white page background, no blue glow and no oversized FAQ watermark,
 * - smaller icons and calmer spacing,
 * - flat cards without shadows,
 * - navy gradient hover/focus states (no pink inherited effects),
 * - improved desktop, tablet and mobile rhythm.
 */
if (!defined('ABSPATH')) { exit; }

function zp_suite_303_faq_visual_refinement_css(){
  if (is_admin()) { return; }
  ?>
<link rel="stylesheet" id="zp-suite-front-fixes-303-faq-refinement" href="<?php echo esc_url(ZP_SUITE_URL . 'assets/css/front-fixes-303.css'); ?>?ver=<?php echo esc_attr(zp_suite_asset_version('assets/css/front-fixes-303.css')); ?>">
  <?php
}
add_action('wp_head', 'zp_suite_303_faq_visual_refinement_css', PHP_INT_MAX);
add_action('wp_footer', 'zp_suite_303_faq_visual_refinement_css', PHP_INT_MAX);
