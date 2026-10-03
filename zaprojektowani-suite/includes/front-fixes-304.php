<?php
/**
 * ZP Suite v2.2.776 — contact card polish.
 *
 * - Team quote card: the Zaprojektowani logo replaces the old SVG-mask signature, which rendered empty.
 * - Mobile: the team photo in the home contact card sits lower, so it no longer covers the WhatsApp button.
 */
if (!defined('ABSPATH')) { exit; }

function zp_suite_304_contact_card_css(){
  if (is_admin()) { return; }
  ?>
<link rel="stylesheet" id="zp-suite-front-fixes-304-contact-card" href="<?php echo esc_url(ZP_SUITE_URL . 'assets/css/front-fixes-304.css'); ?>?ver=<?php echo esc_attr(zp_suite_asset_version('assets/css/front-fixes-304.css')); ?>">
  <?php
}
add_action('wp_head', 'zp_suite_304_contact_card_css', PHP_INT_MAX);
add_action('wp_footer', 'zp_suite_304_contact_card_css', PHP_INT_MAX);
