<?php
/**
 * ZP Suite v2.2.798 — Home hero positioning + mobile sticky dock first-paint stabilization.
 *
 * - Mobile: robot + signet moved 50 px further up (90 px total vs the old base position).
 * - Desktop: left hero copy stays 30 px down from v2.2.795; robot + signet are 100 px up vs base (another 50 px higher than v2.2.797).
 * - Mobile sticky dock: lightweight paint/compositing guard so its first appearance does not stall the last phase of page rendering.
 */
if (!defined('ABSPATH')) { exit; }

if (!function_exists('zp_suite_312_sticky_head_css')) {
  function zp_suite_312_sticky_head_css() {
    if (is_admin()) { return; }
    ?>
<style id="zp-suite-312-sticky-first-paint">
@media (max-width:760px){
  /* The dock is almost opaque already. Avoid a full-width live backdrop blur layer
     from the very first paint; visually it stays the same, but compositing is cheaper. */
  html body .zpMobileStickyCta__inner{
    -webkit-backdrop-filter:none!important;
    backdrop-filter:none!important;
    contain:layout style!important;
    isolation:isolate!important;
    transform:translateZ(0)!important;
    backface-visibility:hidden!important;
  }
  html body .zpMobileStickyCta{
    contain:layout style!important;
  }
}
</style>
    <?php
  }
}
/* front-fixes-183 prints old sticky guards very late in wp_head; PHP_INT_MAX wins after them. */
add_action('wp_head', 'zp_suite_312_sticky_head_css', PHP_INT_MAX);

if (!function_exists('zp_suite_312_home_hero_position_css')) {
  function zp_suite_312_home_hero_position_css() {
    if (is_admin()) { return; }
    ?>
<style id="zp-suite-312-home-hero-position">
/* Final positioning overrides are emitted in <head> before the hero exists.
   !important keeps them stable even though home-hero.php prints its base CSS later in the body.
   This prevents the robot from rendering low first and jumping upward after the page finishes parsing. */
@media (max-width:1100px){
  html body #zhHero .zh__poster:not(#zpx){
    translate:0 -90px!important;
  }
  html body #zhHero .zh__stage::before,
  html body #zhHero .zh__stage::after{
    top:calc(56% - 90px)!important;
  }
}

@media (min-width:1101px){
  /* v2.2.795 position + 30 px down. */
  html body #zhHero .zh__copy:not(#zpx){
    translate:0 calc(min(0px,calc(102px - var(--zpHeaderH,89px) - var(--zhHeaderGap,80px))) + 30px)!important;
  }
  /* Move only the robot artwork, not the whole interactive stage. */
  html body #zhHero .zh__poster:not(#zpx){
    translate:0 -100px!important;
  }
  html body #zhHero .zh__stage::before,
  html body #zhHero .zh__stage::after{
    top:calc(50% - 100px)!important;
  }
}
</style>
    <?php
  }
}
add_action('wp_head', 'zp_suite_312_home_hero_position_css', PHP_INT_MAX);
