<?php
/**
 * ZP Suite v2.2.804 — final crew positioning only.
 * - Mobile: crew 20 px lower than v2.2.803 (net -10 px vs its pre-803 position).
 * - Desktop: crew 30 px higher without changing the text/kicker position.
 */
if (!defined('ABSPATH')) { exit; }

if (!function_exists('zp_suite_319_crew_position_css')) {
  function zp_suite_319_crew_position_css() {
    if (is_admin()) { return; }
    ?>
<style id="zp-suite-319-crew-position">
@media (max-width:1100px){
  html body.home #zhHero .zh__crew,
  html body.front-page #zhHero .zh__crew{
    transform:translate3d(0,-10px,0)!important;
  }
}

@media (min-width:1101px){
  html body.home #zhHero .zh__crew,
  html body.front-page #zhHero .zh__crew{
    transform:translate3d(20px,-30px,0)!important;
  }
}
</style>
    <?php
  }
}
add_action('wp_head', 'zp_suite_319_crew_position_css', PHP_INT_MAX);
