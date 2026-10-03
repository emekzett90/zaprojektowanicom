<?php
if (!defined('ABSPATH')) { exit; }

/**
 * ZP Suite v2.2.217 — restore safe footer pre-gap after contact/footer gap test.
 * Przywraca biały odstęp przed stopką, bez rozciągania ciemnego tła stopki.
 */
add_action('wp_footer', function(){
  if (is_admin()) { return; }
  ?>
  <style id="zp-suite-2-2-217-restore-footer-pregap">html body .zpFooterPreGap,html.zp-contact-before-footer-gap-off body .zpFooterPreGap,body.zp-contact-before-footer-gap-off .zpFooterPreGap,html.zp-contact-before-footer-gap-off body .zpContactPageShortcode[data-zp-home-contact-form] + .zpFooterPreGap,body.zp-contact-before-footer-gap-off .zpContactPageShortcode[data-zp-home-contact-form] + .zpFooterPreGap,html.zp-contact-before-footer-gap-off body .zpContactPageShortcode[data-zp-contact-page] + .zpFooterPreGap,body.zp-contact-before-footer-gap-off .zpContactPageShortcode[data-zp-contact-page] + .zpFooterPreGap{display:block!important;width:100%!important;height:250px!important;min-height:250px!important;max-height:250px!important;margin:0!important;padding:0!important;border:0!important;background:#fff!important;background-image:none!important;position:relative!important;z-index:0!important;pointer-events:none!important;overflow:hidden!important;box-sizing:border-box!important;opacity:1!important;visibility:visible!important}html body .zpMegaFooter,html body #zpMegaFooter{margin-top:0!important;position:relative!important;z-index:20!important;overflow:visible!important;clip-path:none!important;contain:initial!important;isolation:auto!important;transform:none!important}html body .zpMegaFooter__darkBand,html body .zpMegaFooter__darkBand--cta,html body .zpMegaFooter__inner--cta,html body .zpMegaFooter__cta,html body .zpMegaFooter__teamPhoto{overflow:visible!important;clip-path:none!important;contain:initial!important}html body .zpMegaFooter__teamPhoto{z-index:80!important}@media(max-width:760px){html body .zpFooterPreGap,html.zp-contact-before-footer-gap-off body .zpFooterPreGap,body.zp-contact-before-footer-gap-off .zpFooterPreGap,html.zp-contact-before-footer-gap-off body .zpContactPageShortcode[data-zp-home-contact-form] + .zpFooterPreGap,body.zp-contact-before-footer-gap-off .zpContactPageShortcode[data-zp-home-contact-form] + .zpFooterPreGap,html.zp-contact-before-footer-gap-off body .zpContactPageShortcode[data-zp-contact-page] + .zpFooterPreGap,body.zp-contact-before-footer-gap-off .zpContactPageShortcode[data-zp-contact-page] + .zpFooterPreGap{height:160px!important;min-height:160px!important;max-height:160px!important}}</style>
  <?php
}, 2147483647);
