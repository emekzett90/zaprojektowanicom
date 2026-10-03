<?php
/**
 * v2.2.440 — precise follow-up:
 * 1) /logo-branding-katowice/ process cards rail full-bleed like Strony process cards.
 * 2) no global width overrides, no hero changes.
 */
if (!defined('ABSPATH')) { exit; }

function zp_suite_257_logo_process_full_bleed_cards_css() {
  $uri = isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '';
  if (strpos($uri, '/logo-branding-katowice') === false) { return; }
  ?>
<style id="zp-suite-front-fixes-257-logo-process-full-bleed-v440">@media (min-width:981px){html body.zp-logo-branding-page #zpShopProcessFlow,html body.zp-logo-branding-shortcode #zpShopProcessFlow{--zpLogoProcessWrap:min(1750px,calc(100vw - clamp(28px,6vw,112px)));--zpLogoProcessSide:calc((100vw - var(--zpLogoProcessWrap)) / 2)}html body.zp-logo-branding-page #zpShopProcessFlow .zpShopProcessFlow__inner,html body.zp-logo-branding-shortcode #zpShopProcessFlow .zpShopProcessFlow__inner,html body.zp-logo-branding-page #zpShopProcessFlow .zpShopProcessFlow__pinInner,html body.zp-logo-branding-shortcode #zpShopProcessFlow .zpShopProcessFlow__pinInner,html body.zp-logo-branding-page #zpShopProcessFlow .zpShopProcessFlow__inner--bottom,html body.zp-logo-branding-shortcode #zpShopProcessFlow .zpShopProcessFlow__inner--bottom{width:var(--zpLogoProcessWrap)!important;max-width:1750px!important;margin-left:auto!important;margin-right:auto!important}html body.zp-logo-branding-page #zpShopProcessFlow .zpShopProcessFlow__viewport,html body.zp-logo-branding-shortcode #zpShopProcessFlow .zpShopProcessFlow__viewport{width:100vw!important;max-width:100vw!important;margin-left:calc(50% - 50vw)!important;margin-right:calc(50% - 50vw)!important;padding-left:0!important;padding-right:0!important;overflow:hidden!important}html body.zp-logo-branding-page #zpShopProcessFlow .zpShopProcessFlow__track,html body.zp-logo-branding-shortcode #zpShopProcessFlow .zpShopProcessFlow__track{padding-left:max(28px,var(--zpLogoProcessSide))!important;padding-right:max(28px,var(--zpLogoProcessSide))!important}}@media (min-width:761px) and (max-width:980px){html body.zp-logo-branding-page #zpShopProcessFlow .zpShopProcessFlow__viewport,html body.zp-logo-branding-shortcode #zpShopProcessFlow .zpShopProcessFlow__viewport{width:100vw!important;max-width:100vw!important;margin-left:calc(50% - 50vw)!important;margin-right:calc(50% - 50vw)!important;padding-left:0!important;padding-right:0!important;overflow-x:auto!important;-webkit-overflow-scrolling:touch!important}html body.zp-logo-branding-page #zpShopProcessFlow .zpShopProcessFlow__track,html body.zp-logo-branding-shortcode #zpShopProcessFlow .zpShopProcessFlow__track{padding-left:24px!important;padding-right:24px!important}}</style>
  <?php
}
add_action('wp_head', 'zp_suite_257_logo_process_full_bleed_cards_css', 134);
