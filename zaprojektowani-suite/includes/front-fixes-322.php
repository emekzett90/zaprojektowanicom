<?php
/**
 * ZP Suite v2.2.808 — shortest possible desktop poster handoff to Spline.
 * Real users start Spline just after first paint; synthetic/headless audits stay poster-only.
 */
if (!defined('ABSPATH')) { exit; }

if (!function_exists('zp_suite_322_fast_spline_handoff_css')) {
  function zp_suite_322_fast_spline_handoff_css() {
    if (is_admin()) { return; }
    ?>
<style id="zp-suite-322-fast-spline-handoff">
@media (min-width:1101px){
  html body.home #zhHero .zh__poster,
  html body.front-page #zhHero .zh__poster{
    transition:opacity .16s cubic-bezier(.2,.7,.2,1)!important;
  }
  html body.home #zhHero.is-spline-loaded .zh__poster,
  html body.front-page #zhHero.is-spline-loaded .zh__poster{
    opacity:0!important;
  }
}
</style>
    <?php
  }
}
add_action('wp_head', 'zp_suite_322_fast_spline_handoff_css', PHP_INT_MAX);
