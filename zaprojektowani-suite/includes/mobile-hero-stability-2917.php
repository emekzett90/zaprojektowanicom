<?php
/**
 * Keep the mobile home hero in the layout from its first paint.
 *
 * The older mobile rule applies content-visibility:auto to every home section,
 * including #zhHero, with an estimated 980px height. The intended exception in
 * optimizer.php starts with "body :is(...)"; the legacy inline CSS minifier
 * removes the descendant space and makes that exception fail to match.
 * Use explicit selectors here so only the above-the-fold hero is laid out
 * immediately. Its responsive dimensions, font, visuals and animations stay
 * under their existing rules; lower sections retain their lazy rendering.
 */
if (!defined('ABSPATH')) { exit; }

function zp_suite_mobile_home_hero_stability_2917(): void {
    if (is_admin() || !(is_front_page() || is_home())) { return; }
    ?>
<style id="zp-suite-mobile-home-hero-stability-2917">
@media (max-width:1100px){
  html body.home #zhHero,
  html body.front-page #zhHero{
    content-visibility:visible!important;
    contain:none!important;
  }
}
</style>
    <?php
}

add_action('wp_head', 'zp_suite_mobile_home_hero_stability_2917', 1);
