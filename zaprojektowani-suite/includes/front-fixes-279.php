<?php
/**
 * v2.2.470 — Logo mobile hero typography/image quality final polish.
 * Aligns cloned logo mobile hero typography with Strony/Sklepy mobile hero,
 * removes unwanted light blobs, and forces the full-size hero image instead of thumbnails.
 */
if (!defined('ABSPATH')) { exit; }

add_action('wp_head', function () {
  if (is_admin()) { return; }
  ?>
  <style id="zp-suite-279-logo-mobile-type-image-final">@media (max-width:760px){html body #zpBrandHeroMobileKat.zpBrandHeroMobileKat{background:linear-gradient(180deg,#070910 0%,#05070d 52%,#05070d 100%)!important;padding-top:118px!important}html body #zpBrandHeroMobileKat .zpWebHeroKat__glow,html body #zpBrandHeroMobileKat .zpWebHeroKat__orbital,html body #zpBrandHeroMobileKat .zpWebHeroKat__beam,html body #zpBrandHeroMobileKat .zpWebHeroKat__screenAura{display:none!important;opacity:0!important;background:none!important}html body #zpBrandHeroMobileKat .zpWebHeroKat__videoWrap{opacity:.16!important;filter:brightness(.62) saturate(.84)!important}html body #zpBrandHeroMobileKat .zpWebHeroKat__video{filter:brightness(.5) saturate(.9) contrast(1.04)!important}html body #zpBrandHeroMobileKat .zpWebHeroKat__eyebrow{margin:0 0 18px!important;gap:12px!important;font-size:10.5px!important;line-height:1.1!important;font-weight:800!important;letter-spacing:.18em!important}html body #zpBrandHeroMobileKat .zpWebHeroKat__eyebrow::before{width:34px!important;flex-basis:34px!important}html body #zpBrandHeroMobileKat .zpServiceHeroH1{font-size:clamp(26px,8.9vw,44px)!important;line-height:1.06!important;letter-spacing:-.032em!important;font-weight:520!important;font-variation-settings:"wght" 520!important;max-width:100%!important;text-wrap:balance!important}html body #zpBrandHeroMobileKat .zpWebHeroKat__lead{margin-top:20px!important;font-size:15px!important;line-height:1.66!important;letter-spacing:-.012em!important;font-weight:400!important;color:rgba(255,255,255,.76)!important}html body #zpBrandHeroMobileKat .zpWebHeroKat__lead strong{font-weight:760!important;color:#fff!important}html body #zpBrandHeroMobileKat .zpWebHeroKat__actions{gap:10px!important;margin-top:26px!important}html body #zpBrandHeroMobileKat .zpWebHeroKat__btn{min-height:58px!important;padding:0 24px!important;font-size:14px!important;line-height:1!important;font-weight:800!important;letter-spacing:-.012em!important}html body #zpBrandHeroMobileKat .zpWebHeroKat__btn svg{width:18px!important;height:18px!important;flex-basis:18px!important}html body #zpBrandHeroMobileKat .zpWebHeroKat__proof{gap:8px!important;margin-top:20px!important}html body #zpBrandHeroMobileKat .zpWebHeroKat__pill{min-height:44px!important;padding:0 12px!important;gap:8px!important;font-size:11px!important;font-weight:780!important;letter-spacing:-.018em!important}html body #zpBrandHeroMobileKat .zpWebHeroKat__pill svg{width:15px!important;height:15px!important;flex-basis:15px!important}html body #zpBrandHeroMobileKat .zpWebHeroKat__visual{margin-top:20px!important;height:590px!important;min-height:590px!important;overflow:hidden!important;background:transparent!important}html body #zpBrandHeroMobileKat .zpWebHeroKat__visualStage{min-height:590px!important;overflow:visible!important}html body #zpBrandHeroMobileKat .zpBrandHeroMobileKat__mock{content:url('https://zaprojektowani.com/wp-content/uploads/2026/05/zaprojektowani_logo_branding_zaufali_widget_alt_premium.webp')!important;top:42px!important;left:50%!important;width:122vw!important;min-width:122vw!important;max-width:none!important;height:auto!important;transform:translate3d(-50%,0,0)!important;image-rendering:auto!important;filter:brightness(.9) contrast(1.05) saturate(1.02)!important}html body #zpBrandHeroMobileKat .zpBrandHeroMobileKat__floatPill{min-height:34px!important;padding:8px 12px!important;gap:7px!important;font-size:10.5px!important;line-height:1!important;font-weight:800!important;letter-spacing:-.01em!important;background:rgba(255,255,255,.12)!important}html body #zpBrandHeroMobileKat .zpBrandHeroMobileKat__floatPill svg{width:15px!important;height:15px!important;flex-basis:15px!important}html body #zpBrandHeroMobileKat .zpBrandHeroMobileKat__floatPill--print{top:154px!important;left:0!important}html body #zpBrandHeroMobileKat .zpBrandHeroMobileKat__floatPill--seo{top:102px!important;right:4px!important}html body #zpBrandHeroMobileKat .zpBrandHeroMobileKat__floatPill--book{top:218px!important;left:18px!important}html body #zpBrandHeroMobileKat .zpBrandHeroMobileKat__floatPill--brand{top:192px!important;right:8px!important}html body #zpBrandHeroMobileKat .zpBrandHeroMobileKat__miniCard{top:274px!important;left:22px!important;width:min(340px,calc(100vw - 58px))!important;max-width:calc(100vw - 58px)!important;padding:16px!important;border-radius:22px!important}html body #zpBrandHeroMobileKat .zpWebHeroKat__miniTop{margin-bottom:11px!important}html body #zpBrandHeroMobileKat .zpWebHeroKat__miniTop span{font-size:10px!important;letter-spacing:.15em!important;font-weight:850!important}html body #zpBrandHeroMobileKat .zpWebHeroKat__miniTop svg{width:19px!important;height:19px!important}html body #zpBrandHeroMobileKat .zpBrandHeroMobileKat__miniCard strong{font-size:20px!important;line-height:1.08!important;letter-spacing:-.04em!important;font-weight:780!important;margin-bottom:7px!important}html body #zpBrandHeroMobileKat .zpBrandHeroMobileKat__miniCard p{font-size:13px!important;line-height:1.46!important;letter-spacing:-.018em!important;font-weight:520!important}}</style>
  <?php
}, PHP_INT_MAX);

add_action('wp_footer', function () {
  if (is_admin()) { return; }
  ?>
  <script id="zp-suite-279-logo-mobile-full-image-guard">
  (function(){
    var full='https://zaprojektowani.com/wp-content/uploads/2026/05/zaprojektowani_logo_branding_zaufali_widget_alt_premium.webp';
    function fix(){
      var root=document.getElementById('zpBrandHeroMobileKat');
      if(!root) return;
      var img=root.querySelector('.zpBrandHeroMobileKat__mock,.zpWebHeroKat__mock');
      if(!img) return;
      try{
        img.classList.add('skip-lazy','no-lazy','no-litespeed-lazyload');
        img.setAttribute('loading','eager');
        img.setAttribute('fetchpriority','high');
        img.setAttribute('decoding','async');
        img.setAttribute('data-no-lazy','1');
        img.setAttribute('data-skip-lazy','1');
        img.setAttribute('data-nitro-no-lazy','1');
        img.setAttribute('sizes','100vw');
        img.setAttribute('srcset', full + ' 1800w');
        img.setAttribute('data-srcset', full + ' 1800w');
        img.setAttribute('data-src', full);
        img.setAttribute('data-large_image', full);
        if(String(img.getAttribute('src')||'').indexOf('-332x225')>-1 || img.getAttribute('src')!==full){ img.setAttribute('src', full); }
      }catch(e){}
    }
    if(document.readyState==='loading'){document.addEventListener('DOMContentLoaded',fix,{once:true});}else{fix();}
    window.addEventListener('load',fix,{once:true,passive:true});
    setTimeout(fix,350); setTimeout(fix,1100);
  })();
  </script>
  <?php
}, PHP_INT_MAX);
