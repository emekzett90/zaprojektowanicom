<?php
if (!defined('ABSPATH')) { exit; }

/**
 * ZP Suite v2.2.185 — safe global footer pre-gap + sticky studio layer fix.
 * - przywraca pustą przestrzeń przed stopką bez rozciągania ciemnego tła,
 * - nie przykrywa headera ani stopki,
 * - sticky bar Studio Wyceny zostaje nad stopką.
 */
add_action('wp_head', function(){
  if (is_admin()) { return; }
  ?>
  <style id="zp-suite-22185-footer-gap-layer-fix">html body .zpFooterPreGap{display:block!important;width:100%!important;height:250px!important;min-height:250px!important;max-height:250px!important;margin:0!important;padding:0!important;background:#fff!important;background-image:none!important;position:relative!important;z-index:0!important;pointer-events:none!important;overflow:hidden!important;box-sizing:border-box!important}html body .elementor-location-footer,html body footer,html body .zpMegaFooter{position:relative!important;overflow:visible!important;clip-path:none!important;contain:initial!important}html body .zpMegaFooter{z-index:20!important;isolation:auto!important;transform:none!important}html body .zpMegaFooter__darkBand,html body .zpMegaFooter__darkBand--cta,html body .zpMegaFooter__inner--cta,html body .zpMegaFooter__cta,html body .zpMegaFooter__teamPhoto{overflow:visible!important;clip-path:none!important;contain:initial!important}html body .zpMegaFooter__teamPhoto{position:relative!important;z-index:80!important}html body #zpbsUltimate{isolation:auto!important;z-index:auto!important}html body #zpbsUltimate .zpbsSticky{position:fixed!important;z-index:2147483647!important;isolation:isolate!important;pointer-events:auto!important}@media(max-width:760px){html body .zpFooterPreGap{height:160px!important;min-height:160px!important;max-height:160px!important}}</style>
  <?php
}, 2147483500);
