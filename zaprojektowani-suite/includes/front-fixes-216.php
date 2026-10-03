<?php
if (!defined('ABSPATH')) { exit; }

/**
 * ZP Suite v2.2.216 — remove dark gap between global contact widget and footer.
 * Keeps footer/team overflow behaviour, but when the footer follows the global contact block,
 * the old spacer is collapsed so the page does not show the dark initial background between widgets.
 */
add_action('wp_footer', function(){
  if (is_admin()) { return; }
  ?>
  <script id="zp-suite-2-2-216-contact-footer-gap-js">
  (function(){
    function markContactFooterGap(){
      try{
        var contact = document.querySelector('.zpContactPageShortcode[data-zp-home-contact-form], .zpContactPageShortcode[data-zp-contact-page], [data-zp-home-contact-form]');
        var footer = document.querySelector('#zpMegaFooter, .zpMegaFooter');
        var gap = document.querySelector('.zpFooterPreGap');
        if(!contact || !footer || !gap){ return; }
        document.documentElement.classList.add('zp-contact-before-footer-gap-off');
        document.body.classList.add('zp-contact-before-footer-gap-off');
      }catch(e){}
    }
    if(document.readyState === 'loading'){
      document.addEventListener('DOMContentLoaded', markContactFooterGap, {once:true});
    }else{
      markContactFooterGap();
    }
    window.addEventListener('load', markContactFooterGap, {once:true});
  })();
  </script>
  <style id="zp-suite-2-2-216-contact-footer-gap-fix">html.zp-contact-before-footer-gap-off body .zpFooterPreGap,body.zp-contact-before-footer-gap-off .zpFooterPreGap{height:0!important;min-height:0!important;max-height:0!important;margin:0!important;padding:0!important;border:0!important;background:#fff!important;overflow:hidden!important}html.zp-contact-before-footer-gap-off body .zpContactPageShortcode[data-zp-home-contact-form],body.zp-contact-before-footer-gap-off .zpContactPageShortcode[data-zp-home-contact-form]{margin-bottom:0!important;padding-bottom:0!important;background:#fff!important}html.zp-contact-before-footer-gap-off body .zpContactPageShortcode[data-zp-home-contact-form] + .zpFooterPreGap,body.zp-contact-before-footer-gap-off .zpContactPageShortcode[data-zp-home-contact-form] + .zpFooterPreGap{display:none!important}html.zp-contact-before-footer-gap-off body #zpMegaFooter,body.zp-contact-before-footer-gap-off #zpMegaFooter,html.zp-contact-before-footer-gap-off body .zpMegaFooter,body.zp-contact-before-footer-gap-off .zpMegaFooter{margin-top:0!important;position:relative!important;overflow:visible!important;clip-path:none!important;contain:initial!important}html.zp-contact-before-footer-gap-off body .zpMegaFooter__teamPhoto,body.zp-contact-before-footer-gap-off .zpMegaFooter__teamPhoto{overflow:visible!important;z-index:80!important}</style>
  <?php
}, 2147483647);
