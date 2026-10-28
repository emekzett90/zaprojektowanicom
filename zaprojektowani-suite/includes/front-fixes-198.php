<?php
/**
 * ZAPROJEKTOWANI Suite — front fixes v2.2.198
 * Home selected sections wide rhythm only.
 */
if (!defined('ABSPATH')) exit;

add_action('wp_head', function () {
  if (is_admin()) return;
  ?>
  <style id="zp-suite-front-fixes-198">:root{--zp-home-wide-rhythm: min(1740px,calc(100% - clamp(28px,6vw,112px)));--zp-home-wide-rhythm-mobile: min(100% - 28px,760px)}html body.home .zpServicesPath__inner,html body.front-page .zpServicesPath__inner,html body.home .zpShowcaseDarkCta__inner,html body.front-page .zpShowcaseDarkCta__inner,html body.home .zpLaptopShowcase__inner,html body.front-page .zpLaptopShowcase__inner,html body.home .zpAboutExperience__inner,html body.front-page .zpAboutExperience__inner,html body.home .zpRevEd__finalInner,html body.front-page .zpRevEd__finalInner,html body.home .zpHomeSeo__inner,html body.front-page .zpHomeSeo__inner,html body.home .zpHomeSeoIntro__inner,html body.front-page .zpHomeSeoIntro__inner{width: var(--zp-home-wide-rhythm) !important;max-width: 1740px !important;margin-left: auto !important;margin-right: auto !important;box-sizing: border-box !important}html body.home .zpServicesPath,html body.front-page .zpServicesPath,html body.home .zpShowcaseDarkCta,html body.front-page .zpShowcaseDarkCta,html body.home .zpLaptopShowcase,html body.front-page .zpLaptopShowcase,html body.home .zpAboutExperience,html body.front-page .zpAboutExperience,html body.home .zpHomeSeo,html body.front-page .zpHomeSeo,html body.home .zpHomeSeoIntro,html body.front-page .zpHomeSeoIntro{width: 100% !important;max-width: 100vw !important;overflow-x: clip !important}html body.home .zpRevEd__final,html body.front-page .zpRevEd__final{width: 100% !important;max-width: 100vw !important;margin-left: 0 !important;margin-right: 0 !important;overflow-x: clip !important}@media (max-width: 980px){html body.home .zpServicesPath__inner,html body.front-page .zpServicesPath__inner,html body.home .zpShowcaseDarkCta__inner,html body.front-page .zpShowcaseDarkCta__inner,html body.home .zpLaptopShowcase__inner,html body.front-page .zpLaptopShowcase__inner,html body.home .zpAboutExperience__inner,html body.front-page .zpAboutExperience__inner,html body.home .zpRevEd__finalInner,html body.front-page .zpRevEd__finalInner,html body.home .zpHomeSeo__inner,html body.front-page .zpHomeSeo__inner,html body.home .zpHomeSeoIntro__inner,html body.front-page .zpHomeSeoIntro__inner{width: var(--zp-home-wide-rhythm-mobile) !important;max-width: 760px !important}}</style>
  <?php
}, 2147483647);
