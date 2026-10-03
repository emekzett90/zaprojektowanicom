<?php
/**
 * ZP Suite v2.2.799 — final robot offsets + smaller ZP signet behind the robot.
 *
 * - Desktop: robot and signet another 50 px higher vs 2.2.798.
 * - Mobile: robot and signet another 30 px higher vs 2.2.798.
 * - Background ZP signet: reduced size on both desktop and mobile for better readability.
 */
if (!defined('ABSPATH')) { exit; }

if (!function_exists('zp_suite_314_home_hero_refine_css')) {
  function zp_suite_314_home_hero_refine_css() {
    if (is_admin()) { return; }
    ?>
<style id="zp-suite-314-home-hero-refine">
@media (max-width:1100px){
  html body #zhHero .zh__poster:not(#zpx){
    translate:0 -120px!important;
  }
  html body #zhHero .zh__stage::before{
    width:420px!important;
    top:calc(56% - 120px)!important;
    transform:translate(-48%,-50%)!important;
  }
  html body #zhHero .zh__stage::after{
    width:400px!important;
    top:calc(56% - 120px)!important;
    transform:translate(-48%,-50%)!important;
  }
}

@media (min-width:1101px){
  html body #zhHero .zh__poster:not(#zpx){
    translate:0 -150px!important;
  }
  html body #zhHero .zh__stage::before{
    width:min(84%,900px)!important;
    top:calc(50% - 150px)!important;
    transform:translate(-56%,-40%)!important;
  }
  html body #zhHero .zh__stage::after{
    width:min(68%,720px)!important;
    top:calc(50% - 150px)!important;
    transform:translate(-56%,-44%)!important;
  }
}
</style>
    <?php
  }
}
add_action('wp_head', 'zp_suite_314_home_hero_refine_css', PHP_INT_MAX);
