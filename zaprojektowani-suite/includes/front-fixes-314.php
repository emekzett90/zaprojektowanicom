<?php
/**
 * ZP Suite v2.2.799 — final robot offsets + smaller ZP signet behind the robot.
 *
 * - Desktop: robot and signet another 50 px higher vs 2.2.798.
 * - Mobile: robot and signet another 30 px higher vs 2.2.798.
 * - Background ZP signet: reduced size on both desktop and mobile for better readability.
 *
 * 3.1.1 (Mat 14.10): the robot's head no longer goes up under the header. The fixed lift put the head
 * 14–60 px from the top on desktop windows up to 1920×1080 (header ends at 88 px) and on tall phones and
 * tablets (412×915, 430×932, iPad Air and Pro), because the picture's height follows the window while the
 * hero's does not. The lift is now never more than the old value, but only as much as each window allows:
 * - desktop: the picture shows the head where the 3D robot that replaces it has it (14.17% of the stage
 *   height down; the picture is min(82%,860px) wide, its half height is 0.5164 of that, and the head starts
 *   6 px below the picture's top edge), so the robot no longer drops when the 3D scene takes over;
 * - phones and tablets: the head stays at least as low as on an iPhone 14 Pro (393×852), which, like
 *   2560×1440 and larger, is unchanged.
 * The plain value before each max() is for browsers without max().
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
    translate:0 max(-120px,calc(121px - 50% + 36vh))!important;
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
@media (max-width:600px){
  html body #zhHero .zh__poster:not(#zpx){
    translate:0 max(-120px,calc(129px - 50% + 35vh))!important;
  }
}

@media (min-width:1101px){
  html body #zhHero .zh__poster:not(#zpx){
    translate:0 -150px!important;
    translate:0 max(-150px,calc(clamp(423.42px,29.64vw,444.08px) - 35.83% - 76px))!important;
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
