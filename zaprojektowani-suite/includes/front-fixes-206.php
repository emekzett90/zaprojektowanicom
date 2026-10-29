<?php
/**
 * ZAPROJEKTOWANI Suite — front fixes v2.2.206
 * Footer CTA copy width polish + deterministic single mobile footer logo.
 */
if (!defined('ABSPATH')) exit;

add_action('wp_footer', function () {
  if (is_admin()) return;
  ?>
  <style id="zp-suite-front-fixes-206">@media (min-width:1081px){html body .zpMegaFooter .zpMegaFooter__darkBand--cta .zpMegaFooter__ctaCopy,html body.home .zpMegaFooter .zpMegaFooter__darkBand--cta .zpMegaFooter__ctaCopy,html body.front-page .zpMegaFooter .zpMegaFooter__darkBand--cta .zpMegaFooter__ctaCopy,html body.page-template-front-page .zpMegaFooter .zpMegaFooter__darkBand--cta .zpMegaFooter__ctaCopy{width:min(980px,62vw)!important;max-width:min(980px,62vw)!important;min-width:0!important}html body .zpMegaFooter .zpMegaFooter__darkBand--cta .zpMegaFooter__ctaCopy h2,html body .zpMegaFooter .zpMegaFooter__darkBand--cta .zpMegaFooter__ctaCopy #zpMegaFooterCtaTitle{max-width:980px!important}html body .zpMegaFooter .zpMegaFooter__darkBand--cta .zpMegaFooter__ctaCopy p{max-width:780px!important}}@media (max-width:760px){html body .zpMegaFooter .zpMegaFooter__brandIntro{display:block!important}html body .zpMegaFooter .zpMegaFooter__logo{display:block!important;position:relative!important;width:168px!important;max-width:168px!important;height:42px!important;min-height:42px!important;margin:0 0 18px!important;padding:0!important;overflow:visible!important;opacity:1!important;visibility:visible!important;background:none!important;background-image:none!important;line-height:0!important}html body .zpMegaFooter .zpMegaFooter__logo img{display:none!important;opacity:0!important;visibility:hidden!important}html body .zpMegaFooter .zpMegaFooter__logo:before{content:""!important;display:block!important;width:168px!important;height:42px!important;min-height:42px!important;background-image:url('https://zaprojektowani.com/wp-content/uploads/2026/06/zp_sygnet_ciemny-scaled.webp')!important;background-repeat:no-repeat!important;background-position:left center!important;background-size:contain!important;opacity:1!important;visibility:visible!important;filter:none!important;mix-blend-mode:normal!important;transform:none!important}}</style>
  <?php
}, 2147483647);
