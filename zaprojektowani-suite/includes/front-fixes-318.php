<?php
/**
 * ZP Suite v3.2.31 — immediate mobile readiness + the existing crew position.
 *
 * - header-first.php already prints the finished header with stable dimensions,
 *   inline icons and its local label font. No asset/timer gate is needed here.
 * - Keep the readiness classes for existing entrance styles, without temporarily
 *   disabling hero links or scheduling font promises for every DOM mutation.
 * - Mobile crew is visually lifted another 30 px without pulling the kicker/title upward.
 */
if (!defined('ABSPATH')) { exit; }

if (!function_exists('zp_suite_318_header_first_gate')) {
  function zp_suite_318_header_first_gate() {
    if (is_admin()) { return; }
    ?>
<script id="zp-suite-318-header-first-gate-js">
(function(w,d){
  'use strict';
  try{
    if(!w.matchMedia || !w.matchMedia('(max-width:1100px)').matches) return;
    var h=d.documentElement;
    h.classList.add('zp-mobile-header-ready','zp-mobile-hero-ready');
    h.classList.remove('zp-mobile-hard-gate','zp-mobile-ui-pending');
  }catch(e){}
})(window,document);
</script>
<style id="zp-suite-318-header-first-gate-css">
@media (max-width:1100px){
  /* Existing visual state, ready as soon as the HTML is parsed. */
  html.zp-mobile-hero-ready body.home #zhHero,
  html.zp-mobile-hero-ready body.front-page #zhHero{
    opacity:1!important;
    visibility:visible!important;
    pointer-events:auto!important;
    transform:translate3d(0,0,0)!important;
    transition:opacity .48s cubic-bezier(.16,1,.3,1),transform .62s cubic-bezier(.16,1,.3,1)!important;
  }

  /* Keep the header above the hero. */
  html body #zpNewNav{
    z-index:2147483000!important;
  }

  /* Team image only: another 30 px up; layout/kicker stays where it was. */
  html body.home #zhHero .zh__crew,
  html body.front-page #zhHero .zh__crew{
    transform:translate3d(0,-30px,0)!important;
    will-change:transform!important;
  }
}

@media (prefers-reduced-motion:reduce) and (max-width:1100px){
  html.zp-mobile-hero-ready body.home #zhHero,
  html.zp-mobile-hero-ready body.front-page #zhHero{transition:none!important}
}
</style>
    <?php
  }
}
/* Run after the older head gates; header-first.php retains the finished header styling. */
add_action('wp_head', 'zp_suite_318_header_first_gate', PHP_INT_MAX);
