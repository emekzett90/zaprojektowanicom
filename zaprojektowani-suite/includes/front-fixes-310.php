<?php
/**
 * v2.2.791 — skryptowe wejście przeniesione do front-fixes-311 (CSS, bez skoku treści); tu zostaje gradient i karty opinii.
 * v2.2.790 — Mobile home hero: native gradient text and a short staged entrance.
 * Transitions intentionally override the older important first-paint declarations.
 * No letter splitting, blur animation, layout animation or permanent animation loop.
 */
if (!defined('ABSPATH')) { exit; }
function zp_suite_310_mobile_hero() {
  if (!zp_suite_308_is_home_like()) { return; }
  ?>
<style id="zp-suite-310-mobile-hero">
@media(max-width:1100px){
  /* Native text remains readable even without JavaScript or background-clip support. */
  html body #zhHero .zh__title .zh__grad:not(.zh__grad--pieces){color:#a8d8ff!important;-webkit-text-fill-color:#a8d8ff!important;animation:none!important;will-change:auto!important;filter:none!important}
  @supports ((-webkit-background-clip:text) or (background-clip:text)){
    html body #zhHero .zh__title .zh__grad:not(.zh__grad--pieces){background-image:linear-gradient(105deg,#72b5ee 0%,#a4d8ff 25%,#f2fbff 46%,#a6d8ff 68%,#78bcee 100%)!important;background-size:220% 100%!important;background-position:100% 50%!important;background-repeat:no-repeat!important;-webkit-background-clip:text!important;background-clip:text!important;color:transparent!important;-webkit-text-fill-color:transparent!important;-webkit-box-decoration-break:clone;box-decoration-break:clone;transition:background-position 1.9s cubic-bezier(.22,.61,.36,1)!important}
    html body #zhHero.zp-mobile-shine .zh__title .zh__grad:not(.zh__grad--pieces){background-position:0% 50%!important}
  }
  html body #zhHero .zh__btn--g{-webkit-backdrop-filter:none!important;backdrop-filter:none!important}
  /* Crisp review cards without a moving 28px backdrop blur. Data and links are untouched. */
  html body #zhHero .zh__rev{background:linear-gradient(145deg,#101c2c 0%,#08101c 62%,#0b1522 100%)!important;-webkit-backdrop-filter:none!important;backdrop-filter:none!important;border-color:rgba(163,208,246,.19)!important;box-shadow:0 10px 28px rgba(0,0,0,.18),inset 0 1px 0 rgba(255,255,255,.06)!important}
  html body #zhHero .zh__rev::before{background:linear-gradient(115deg,rgba(134,194,244,.07),transparent 55%)!important}
}
@media(prefers-reduced-motion:reduce){
  html body #zhHero .zh__title .zh__grad:not(.zh__grad--pieces){transition:none!important}
}
</style>

  <?php
}
add_action('wp_head', 'zp_suite_310_mobile_hero', PHP_INT_MAX);
