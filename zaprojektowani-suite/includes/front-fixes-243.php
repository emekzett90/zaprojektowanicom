<?php
if (!defined('ABSPATH')) { exit; }

/**
 * ZP Suite v2.2.423 — tablet / small laptop drawer final white mobile style.
 * The hamburger drawer opened on tablet/small laptop was still inheriting old
 * dark desktop drawer patches. This late, narrow override makes the opened
 * drawer use the same white mobile visual language, scaled for wider screens.
 */
function zp_suite_243_tablet_white_drawer_css(){
  if (is_admin()) { return; }
  ?>
<link rel="stylesheet" id="zp-suite-243-tablet-white-drawer-final" href="<?php echo esc_url(ZP_SUITE_URL . 'assets/css/front-fixes-243.css'); ?>?ver=<?php echo esc_attr(zp_suite_asset_version('assets/css/front-fixes-243.css')); ?>">
  <?php
}
add_action('wp_head', 'zp_suite_243_tablet_white_drawer_css', 1000000);
add_action('wp_footer', 'zp_suite_243_tablet_white_drawer_css', 1000000);
