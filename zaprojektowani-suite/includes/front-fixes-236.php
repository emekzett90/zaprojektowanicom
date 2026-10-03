<?php
/**
 * ZAPROJEKTOWANI Suite — front fixes v2.2.336
 * Old font retained, SVG signature, faster reveal, headings/trust/contact/mobile polish.
 */
if (!defined('ABSPATH')) exit;

add_action('wp_head', function(){
  if (is_admin()) return;
  ?>
  <style id="zp-suite-front-fixes-236">html body .zpNewHero .zpNewHero__signatureQuote .zpNewHero__signatureSign--svg,html body .zpNewHero .zpNewHero__signatureSign.zpNewHero__signatureSign--svg{display:inline-block!important;width:clamp(158px,14vw,238px)!important;height:clamp(30px,2.7vw,48px)!important;min-width:0!important;min-height:0!important;margin-top:clamp(7px,.7vw,12px)!important;background:#fff!important;background-color:#fff!important;-webkit-mask:url('https://zaprojektowani.com/wp-content/uploads/zaprojektowani.svg') center/contain no-repeat!important;mask:url('https://zaprojektowani.com/wp-content/uploads/zaprojektowani.svg') center/contain no-repeat!important;color:transparent!important;-webkit-text-fill-color:transparent!important;font-size:0!important;line-height:0!important;text-indent:-9999px!important;text-shadow:none!important;letter-spacing:0!important;filter:drop-shadow(0 12px 30px rgba(0,0,0,.34))!important;transform:rotate(var(--signatureSignRotate,-1.8deg))!important}html body .zpSSSignature__sign.zpSSSignature__sign--svg{display:block!important;width:clamp(170px,13vw,260px)!important;height:clamp(34px,2.9vw,56px)!important;margin:clamp(14px,1.2vw,20px) 0 0!important;background:#071426!important;-webkit-mask:url('https://zaprojektowani.com/wp-content/uploads/zaprojektowani.svg') left center/contain no-repeat!important;mask:url('https://zaprojektowani.com/wp-content/uploads/zaprojektowani.svg') left center/contain no-repeat!important;color:transparent!important;-webkit-text-fill-color:transparent!important;font-size:0!important;line-height:0!important;text-indent:-9999px!important;overflow:hidden!important;letter-spacing:0!important;text-transform:none!important;font-family:inherit!important;transform:none!important;filter:none!important}html body .zpLaptopShowcase h2,html body .zpLaptopShowcase #zpLaptopShowcaseTitle{font-family:var(--zpl-font,var(--zp-font,"Plus Jakarta Sans Local",system-ui,-apple-system,"Segoe UI",sans-serif))!important;max-width:1040px!important;font-size:clamp(34px,4.05vw,56px)!important;line-height:1.04!important;letter-spacing:-.042em!important;font-weight:650!important;font-variation-settings:"wght" 650!important;text-wrap:balance!important}html body .zpLaptopShowcase h2 span,html body .zpLaptopShowcase h2 strong{font:inherit!important;letter-spacing:inherit!important;color:inherit!important}@media(max-width:760px){html body .zpLaptopShowcase h2,html body .zpLaptopShowcase #zpLaptopShowcaseTitle{font-size:clamp(30px,9vw,40px)!important;line-height:1.06!important;letter-spacing:-.036em!important}}html body .zpTrustCert__reviews{gap:15px!important;row-gap:15px!important;column-gap:15px!important}html body .zpTrustCert__review{margin-top:0!important}html body .zpTrustCert__review--a,html body .zpTrustCert__review--b,html body .zpTrustCert__review--c{margin-top:0!important}html body .zpTrustCert__reviewIntro p,html body .zpTrustCert__review p{font-size:clamp(13px,.92vw,16px)!important;line-height:1.52!important;letter-spacing:-.018em!important}html body .zpTrustCert__reviewIntro h3,html body .zpTrustCert__reviewIntro p,html body .zpRevEd__intro h3,html body .zpRevEd__intro p{font-size:calc(1em - 4px)!important}@media(max-width:760px){html body .zpTrustCert__reviews{gap:15px!important}html body .zpTrustCert__review p{font-size:13px!important;line-height:1.52!important}html body .zpTrustCert__reviewIntro p{font-size:13px!important;line-height:1.52!important}}html body .zpHomeSeoIntro h2,html body #zpHomeSeoIntroTitle{font-family:var(--zp-font,"Plus Jakarta Sans Local",system-ui,-apple-system,"Segoe UI",sans-serif)!important;font-size:clamp(32px,4.1vw,58px)!important;line-height:1.04!important;letter-spacing:-.043em!important;font-weight:650!important;font-variation-settings:"wght" 650!important;max-width:1000px!important}html body #zpContactSystemLight,html body .zpContactSystemLight[data-zp-contact-system-light]{margin-bottom:40px!important}html body #zpContactSystemLight + .zpHomeSeoIntro,html body .zpContactSystemLight + .zpHomeSeoIntro,html body .zpContactSystemLight[data-zp-contact-system-light] + .zpHomeSeoIntro{margin-top:40px!important}@media(max-width:860px){html body .zpSSCard--shop .zpSSCard__laptop3d{top:calc(4% + 15px)!important;bottom:calc(-4% - 15px)!important}html body .zpSSCard--shop .zpSSCard__laptop3d img{transform:translateZ(18px) scale(.70)!important;transform-origin:center center!important}html body .zpSSCard--shop:hover .zpSSCard__laptop3d img{transform:translateZ(18px) scale(.74)!important}}html body #zpRealizacjePage .zpRealizacjePage__title,html body .zpFaqPage h1,html body #zpAboutPage .zpAboutPage__title{font-family:var(--zp-font,"Plus Jakarta Sans Local",system-ui,-apple-system,"Segoe UI",sans-serif)!important;font-size:clamp(38px,4.75vw,68px)!important;line-height:1.03!important;letter-spacing:-.042em!important;font-weight:650!important;font-variation-settings:"wght" 650!important;text-wrap:balance!important;color:#05070b!important}html body #zpRealizacjePage .zpRealizacjePage__title span,html body #zpAboutPage .zpAboutPage__title span{color:#111827!important;font:inherit!important}@media(max-width:760px){html body #zpRealizacjePage .zpRealizacjePage__title,html body .zpFaqPage h1,html body #zpAboutPage .zpAboutPage__title{font-size:clamp(31px,9.2vw,42px)!important;line-height:1.06!important;letter-spacing:-.034em!important}}</style>
  <?php
}, 2147483647);

add_action('wp_footer', function(){
  if (is_admin()) return;
  ?>
  <script id="zp-suite-front-fixes-236-js">
  (function(){
    'use strict';
    function revealEarly(){
      var selectors=['#zpShowcaseServices .zpSSCard','#zpLaptopShowcase','.zpLaptopShowcase','.zpTrustCert'];
      selectors.forEach(function(sel){
        document.querySelectorAll(sel).forEach(function(el){
          var r=el.getBoundingClientRect();
          var vh=window.innerHeight||800;
          if(r.top<vh*1.18 && r.bottom>-80){
            el.classList.add('isVisible','is-inview','is-in');
            if(el.id==='zpLaptopShowcase' || el.classList.contains('zpLaptopShowcase')) el.setAttribute('data-ready','1');
          }
        });
      });
    }
    var revealRaf=0;
    function scheduleReveal(){
      if(revealRaf) return;
      revealRaf=requestAnimationFrame(function(){
        revealRaf=0;
        revealEarly();
      });
    }
    if(document.readyState==='loading') document.addEventListener('DOMContentLoaded',revealEarly,{once:true}); else revealEarly();
    window.addEventListener('scroll',scheduleReveal,{passive:true});
    window.addEventListener('resize',scheduleReveal,{passive:true});
  })();
  </script>
  <?php
}, 2147483647);
