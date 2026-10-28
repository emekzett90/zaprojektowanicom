<?php
/**
 * ZP Suite v2.2.805 — authoritative crew/content positioning.
 *
 * Desktop:
 * - whole text stack (kicker/H1/lead/CTA/chips) 50 px lower than 2.2.804,
 * - crew visibly higher above the kicker using relative top instead of another transform,
 * - preserve +20 px horizontal crew offset and float animations.
 * Mobile:
 * - crew moved clearly lower so heads stay away from the fixed header.
 */
if (!defined('ABSPATH')) { exit; }

if (!function_exists('zp_suite_320_final_crew_content_position_css')) {
  function zp_suite_320_final_crew_content_position_css() {
    if (is_admin()) { return; }
    ?>
<style id="zp-suite-320-final-crew-content-position">
/* Mobile: lower only the team artwork; header/kicker/title stay untouched. */
@media (max-width:1100px){
  html body #zhHero .zh__crew:not(#zpx){
    position:relative!important;
    top:30px!important;
    left:0!important;
    transform:none!important;
    overflow:visible!important;
  }
}

/* Desktop: text stack lower, team visibly higher and slightly to the right. */
@media (min-width:1101px){
  html body #zhHero .zh__copy:not(#zpx){
    translate:0 calc(min(0px,calc(102px - var(--zpHeaderH,89px) - var(--zhHeaderGap,80px))) + 80px)!important;
  }

  html body #zhHero .zh__crew:not(#zpx){
    position:relative!important;
    top:-130px!important;
    left:20px!important;
    transform:none!important;
    height:238px!important;
    margin-bottom:-105px!important;
    overflow:visible!important;
    z-index:9!important;
    -webkit-mask-image:linear-gradient(#000 0%,#000 68%,rgba(0,0,0,.62) 84%,rgba(0,0,0,0) 99%)!important;
    mask-image:linear-gradient(#000 0%,#000 68%,rgba(0,0,0,.62) 84%,rgba(0,0,0,0) 99%)!important;
  }

  html body #zhHero .zh__crewImg:not(#zpx){
    height:214px!important;
    max-height:214px!important;
    transform-origin:50% 100%!important;
  }
}
</style>
    <?php
  }
}
add_action('wp_head', 'zp_suite_320_final_crew_content_position_css', PHP_INT_MAX);
