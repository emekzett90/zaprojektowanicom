<?php
if (!defined('ABSPATH')) { exit; }

/**
 * ZP Suite v2.2.218 — shop contact/footer white breathing + mega promo button contrast.
 */
add_action('wp_footer', function(){
  if (is_admin()) { return; }
  ?>
  <script id="zp-suite-2-2-218-page-class-js">
    (function(){
      try{
        var path = (window.location && window.location.pathname ? window.location.pathname : '').replace(/\/+$/,'/');
        if(path === '/sklepy-internetowe-katowice/' || path === '/tworzenie-sklepow-internetowych/'){
          document.documentElement.classList.add('zp-page-shop-katowice');
          document.body && document.body.classList.add('zp-page-shop-katowice');
        }
      }catch(e){}
    })();
  </script>
  <style id="zp-suite-2-2-218-contact-footer-mega-fix">html.zp-page-shop-katowice body .zpContactPageShortcode[data-zp-home-contact-form],body.zp-page-shop-katowice .zpContactPageShortcode[data-zp-home-contact-form],html.zp-page-shop-katowice body .zpContactPageShortcode[data-zp-contact-page],body.zp-page-shop-katowice .zpContactPageShortcode[data-zp-contact-page]{background:#fff!important;background-image:none!important;padding-bottom:calc(clamp(36px,5vw,72px) + 80px)!important;margin-bottom:0!important;position:relative!important;z-index:2!important;overflow:visible!important}html.zp-page-shop-katowice body .zpContactPageShortcode[data-zp-home-contact-form]::after,body.zp-page-shop-katowice .zpContactPageShortcode[data-zp-home-contact-form]::after,html.zp-page-shop-katowice body .zpContactPageShortcode[data-zp-contact-page]::after,body.zp-page-shop-katowice .zpContactPageShortcode[data-zp-contact-page]::after{content:""!important;display:block!important;position:absolute!important;left:0!important;right:0!important;bottom:0!important;height:80px!important;background:#fff!important;pointer-events:none!important;z-index:-1!important}@media(max-width:760px){html.zp-page-shop-katowice body .zpContactPageShortcode[data-zp-home-contact-form],body.zp-page-shop-katowice .zpContactPageShortcode[data-zp-home-contact-form],html.zp-page-shop-katowice body .zpContactPageShortcode[data-zp-contact-page],body.zp-page-shop-katowice .zpContactPageShortcode[data-zp-contact-page]{padding-bottom:calc(clamp(30px,8vw,58px) + 70px)!important}html.zp-page-shop-katowice body .zpContactPageShortcode[data-zp-home-contact-form]::after,body.zp-page-shop-katowice .zpContactPageShortcode[data-zp-home-contact-form]::after,html.zp-page-shop-katowice body .zpContactPageShortcode[data-zp-contact-page]::after,body.zp-page-shop-katowice .zpContactPageShortcode[data-zp-contact-page]::after{height:70px!important}}html body #zpNewNav .zpNewNav__promoCard .zpNewNav__promoBtn,html body .zpNewNav .zpNewNav__promoCard .zpNewNav__promoBtn,html body .zpNewNav__mega .zpNewNav__promoBtn{display:inline-flex!important;align-items:center!important;justify-content:center!important;gap:8px!important;min-height:42px!important;padding:0 18px!important;border-radius:999px!important;background:#fff!important;background-image:none!important;color:#071426!important;-webkit-text-fill-color:#071426!important;border:1px solid rgba(255,255,255,.72)!important;box-shadow:none!important;opacity:1!important;text-shadow:none!important;mix-blend-mode:normal!important;filter:none!important;font-weight:750!important;letter-spacing:-.018em!important;transition:background .22s ease,color .22s ease,border-color .22s ease,transform .22s ease!important}html body #zpNewNav .zpNewNav__promoCard .zpNewNav__promoBtn *,html body .zpNewNav .zpNewNav__promoCard .zpNewNav__promoBtn *,html body .zpNewNav__mega .zpNewNav__promoBtn *{color:inherit!important;-webkit-text-fill-color:currentColor!important;stroke:currentColor!important;opacity:1!important;filter:none!important;mix-blend-mode:normal!important}html body #zpNewNav .zpNewNav__promoCard:hover .zpNewNav__promoBtn,html body #zpNewNav .zpNewNav__promoCard:focus-visible .zpNewNav__promoBtn,html body .zpNewNav .zpNewNav__promoCard:hover .zpNewNav__promoBtn,html body .zpNewNav .zpNewNav__promoCard:focus-visible .zpNewNav__promoBtn,html body .zpNewNav__mega .zpNewNav__promoCard:hover .zpNewNav__promoBtn,html body .zpNewNav__mega .zpNewNav__promoCard:focus-visible .zpNewNav__promoBtn{background:#071426!important;color:#fff!important;-webkit-text-fill-color:#fff!important;border-color:rgba(143,184,234,.42)!important;transform:translateY(-1px)!important}</style>
  <?php
}, 2147483647);
