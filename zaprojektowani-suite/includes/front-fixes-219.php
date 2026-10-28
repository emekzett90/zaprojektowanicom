<?php
/**
 * ZAPROJEKTOWANI Suite — front fixes v2.2.219
 * - Footer mobile logo sizing fix
 * - FAQ mobile top breathing above kicker
 */
if (!defined('ABSPATH')) exit;

add_action('wp_footer', function () {
  if (is_admin()) return;
  ?>
  <style id="zp-suite-front-fixes-219">@media (max-width:760px){html body .zpMegaFooter .zpMegaFooter__brandIntro{display:block!important}html body .zpMegaFooter .zpMegaFooter__logo{display:block!important;position:relative!important;width:min(62vw,232px)!important;max-width:232px!important;height:58px!important;min-height:58px!important;margin:0 0 20px!important;padding:0!important;overflow:visible!important;opacity:1!important;visibility:visible!important;background:none!important;background-image:none!important;line-height:0!important;transform:none!important;flex:0 0 auto!important}html body .zpMegaFooter .zpMegaFooter__logo img{display:none!important;opacity:0!important;visibility:hidden!important;width:0!important;height:0!important}html body .zpMegaFooter .zpMegaFooter__logo::before{content:""!important;display:block!important;width:100%!important;height:58px!important;min-height:58px!important;background-image:url('https://zaprojektowani.com/wp-content/uploads/2026/06/zp_sygnet_ciemny-scaled.webp')!important;background-repeat:no-repeat!important;background-position:left center!important;background-size:contain!important;opacity:1!important;visibility:visible!important;filter:none!important;mix-blend-mode:normal!important;transform:none!important}html body .zpFaqPage .zpFaqPage__hero{padding-top:50px!important}html body .zpFaqPage .zpFaqPage__eyebrow{margin-top:0!important}}</style>
  <?php
}, 2147483647);
