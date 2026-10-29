<?php
/**
 * ZP Suite v2.2.809 — desktop Lighthouse / main-thread pass.
 * - cheaper first paint for the poster (Spline replaces it immediately on real desktop users),
 * - content-visibility only on known self-contained lower home sections,
 * - no layout or visual-position changes to the hero/header.
 */
if (!defined('ABSPATH')) { exit; }

if (!function_exists('zp_suite_323_desktop_perf_css')) {
  function zp_suite_323_desktop_perf_css() {
    if (is_admin()) { return; }
    ?>
<style id="zp-suite-323-desktop-perf">
@media (min-width:1101px){
  /* The poster is the LCP fallback. drop-shadow/filter makes Chrome rasterize a 1000x1100 layer
     before it can be painted. Spline takes over right after first paint on real desktop users. */
  html body.home #zhHero:not(.is-spline-loaded) .zh__poster,
  html body.front-page #zhHero:not(.is-spline-loaded) .zh__poster{
    filter:none!important;
    -webkit-filter:none!important;
    will-change:auto!important;
  }
  /* Blur in the H1 entrance is expensive and barely visible at desktop scale. Keep the
     opacity/translate entrance, remove only the raster-heavy blur. */
  html body.home #zhHero .zh__title,
  html body.front-page #zhHero .zh__title{
    filter:none!important;
  }

  /* These blocks are thousands of pixels below the fold and do not need initial layout/paint.
     Scope is intentionally conservative: sections with intentional overflow/sticky portfolio
     effects are excluded. */
  html body.home :is(#zpHomeFeaturedPackages,#zpLaptopShowcase,#zpShowcaseServices,.zpRevEd,#zpHomeFaqKatNavy,#zpHomeSeo,#zpHomeAuditCta,#zpContactFormLight),
  html body.front-page :is(#zpHomeFeaturedPackages,#zpLaptopShowcase,#zpShowcaseServices,.zpRevEd,#zpHomeFaqKatNavy,#zpHomeSeo,#zpHomeAuditCta,#zpContactFormLight){
    content-visibility:auto!important;
    contain-intrinsic-size:auto 1100px!important;
  }
  html body.home #zpLaptopShowcase,
  html body.front-page #zpLaptopShowcase{
    contain-intrinsic-size:auto 1700px!important;
  }
  html body.home #zpShowcaseServices,
  html body.front-page #zpShowcaseServices{
    contain-intrinsic-size:auto 2200px!important;
  }
  html body.home .zpRevEd,
  html body.front-page .zpRevEd{
    contain-intrinsic-size:auto 1800px!important;
  }
}
</style>
    <?php
  }
}
add_action('wp_head', 'zp_suite_323_desktop_perf_css', PHP_INT_MAX);
