<?php
/**
 * ZP Suite v2.2.800 — home hero readability and final desktop stage alignment.
 *
 * - Mobile/tablet: darker local fades over robot in the text-overlap zones.
 * - Desktop: whole right stage (Spline + poster + signet) 30 px higher.
 * - Desktop: H1/copy area about 20% wider to the right.
 */
if (!defined('ABSPATH')) { exit; }

if (!function_exists('zp_suite_315_home_hero_readability_css')) {
  function zp_suite_315_home_hero_readability_css() {
    if (is_admin()) { return; }
    ?>
<style id="zp-suite-315-home-hero-readability">
@media (max-width:1100px){
  html body.home #zhHero .zh__fade::after, html body.front-page #zhHero .zh__fade::after{
    content:"";
    position:absolute;
    inset:0;
    z-index:1;
    pointer-events:none;
    background:
      radial-gradient(38% 18% at 84% 21%, rgba(1,3,9,.48) 0%, rgba(1,3,9,.42) 34%, rgba(1,3,9,.22) 54%, rgba(1,3,9,0) 78%),
      radial-gradient(36% 24% at 84% 57%, rgba(1,3,9,.62) 0%, rgba(1,3,9,.52) 36%, rgba(1,3,9,.26) 58%, rgba(1,3,9,0) 82%);
  }
}

@media (min-width:1101px){
  html body.home #zhHero .zh__stage,
  html body.front-page #zhHero .zh__stage{
    transform:translate3d(0,-30px,0)!important;
  }
  html body.home #zhHero .zh__copy,
  html body.front-page #zhHero .zh__copy{
    max-width:1200px!important;
  }
  html body.home #zhHero .zh__title,
  html body.front-page #zhHero .zh__title{
    max-width:18ch!important;
  }
}
</style>
    <?php
  }
}
add_action('wp_head', 'zp_suite_315_home_hero_readability_css', PHP_INT_MAX);
