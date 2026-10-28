<?php
/**
 * ZP Suite v2.2.806 — crew aligned to the kicker on desktop and mobile.
 *
 * Goal: the lower fade of the team should visually grow out of the kicker line,
 * while the kicker itself stays fully readable above the artwork.
 */
if (!defined('ABSPATH')) { exit; }

if (!function_exists('zp_suite_321_crew_kicker_alignment_css')) {
  function zp_suite_321_crew_kicker_alignment_css() {
    if (is_admin()) { return; }
    ?>
<style id="zp-suite-321-crew-kicker-alignment">
/* Kicker always paints above the fading lower edge of the people. */
html body #zhHero .zh__eb:not(#zpx){
  position:relative!important;
  z-index:12!important;
}

/* Mobile: 2.2.805 was too low. Lift the crew ~35 px so its lower fade only
   grazes the kicker instead of covering it. */
@media (max-width:1100px){
  html body #zhHero .zh__crew:not(#zpx){
    top:-5px!important;
    z-index:8!important;
    -webkit-mask-image:linear-gradient(to bottom,#000 0%,#000 54%,rgba(0,0,0,.72) 70%,rgba(0,0,0,.22) 84%,transparent 97%)!important;
    mask-image:linear-gradient(to bottom,#000 0%,#000 54%,rgba(0,0,0,.72) 70%,rgba(0,0,0,.22) 84%,transparent 97%)!important;
  }
}

/* Desktop: 2.2.805 was too high. Bring the crew ~50 px back down so the
   bottom of the figures visually meets / slightly overlaps the kicker line. */
@media (min-width:1101px){
  html body #zhHero .zh__crew:not(#zpx){
    top:-80px!important;
    z-index:8!important;
    -webkit-mask-image:linear-gradient(to bottom,#000 0%,#000 56%,rgba(0,0,0,.68) 72%,rgba(0,0,0,.20) 86%,transparent 98%)!important;
    mask-image:linear-gradient(to bottom,#000 0%,#000 56%,rgba(0,0,0,.68) 72%,rgba(0,0,0,.20) 86%,transparent 98%)!important;
  }
}
</style>
    <?php
  }
}
add_action('wp_head', 'zp_suite_321_crew_kicker_alignment_css', PHP_INT_MAX);
