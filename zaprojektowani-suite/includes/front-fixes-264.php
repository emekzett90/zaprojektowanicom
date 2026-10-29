<?php
/**
 * v2.2.447 — CTA team photo positioning + home signature + first showcase image scale.
 * - /strony-internetowe-katowice portfolio CTA: team_kontakt image lower, more left, 20% smaller.
 * - Home showcase CTA: team_kontakt image 15% smaller only.
 * - Home showcase first card: Apartament Piekną visual 30% larger and still attached right.
 * - Home signature text fallback under the quote.
 */
if (!defined('ABSPATH')) { exit; }

function zp_suite_264_team_photo_home_signature_css_js() {
  ?>
<style id="zp-suite-front-fixes-264-v447">@media (min-width:981px){html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--portfolioCta{--zpCtaPhotoXDesktop:-112px!important;--zpCtaPhotoYDesktop:54px!important;--zpCtaPhotoScaleDesktop:.96!important}html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--portfolioCta .zpSSCard__mock--contact,html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--portfolioCta:hover .zpSSCard__mock--contact,html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--portfolioCta.isActive .zpSSCard__mock--contact,html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--portfolioCta.isVisible .zpSSCard__mock--contact{transform:translate3d(var(--zpCtaPhotoXDesktop),calc(var(--zpContactPhotoUpDesktop,0px) + var(--zpCtaPhotoYDesktop)),0)!important;overflow:visible!important}html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--portfolioCta .zpSSCard__mock--contact img,html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--portfolioCta:hover .zpSSCard__mock--contact img,html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--portfolioCta.isActive .zpSSCard__mock--contact img,html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--portfolioCta.isVisible .zpSSCard__mock--contact img{transform:scale(var(--zpCtaPhotoScaleDesktop))!important;transform-origin:center bottom!important;object-position:center bottom!important;max-width:none!important}html body #zpShowcaseServices .zpSSCard--contact .zpSSCard__mock--contact img,html body #zpShowcaseServices .zpSSCard--contact:hover .zpSSCard__mock--contact img,html body #zpShowcaseServices .zpSSCard--contact.isActive .zpSSCard__mock--contact img,html body #zpShowcaseServices .zpSSCard--contact.isVisible .zpSSCard__mock--contact img,html body .zpShowcaseServices .zpSSCard--contact .zpSSCard__mock--contact img,html body .zpShowcaseServices .zpSSCard--contact:hover .zpSSCard__mock--contact img,html body .zpShowcaseServices .zpSSCard--contact.isActive .zpSSCard__mock--contact img,html body .zpShowcaseServices .zpSSCard--contact.isVisible .zpSSCard__mock--contact img{transform:scale(.85)!important;transform-origin:center bottom!important}html body #zpShowcaseServices .zpSSCard--web .zpSSCard__mock--webLaptop,html body .zpShowcaseServices .zpSSCard--web .zpSSCard__mock--webLaptop{inset:-11% -24% -18% 9%!important;overflow:visible!important;transform:translate3d(0,var(--move,0px),0) rotate(-1deg)!important;transform-origin:right center!important}html body #zpShowcaseServices .zpSSCard--web .zpSSCard__mock--webLaptop img,html body #zpShowcaseServices .zpSSCard--web:hover .zpSSCard__mock--webLaptop img,html body #zpShowcaseServices .zpSSCard--web.isActive .zpSSCard__mock--webLaptop img,html body #zpShowcaseServices .zpSSCard--web.isVisible .zpSSCard__mock--webLaptop img,html body .zpShowcaseServices .zpSSCard--web .zpSSCard__mock--webLaptop img,html body .zpShowcaseServices .zpSSCard--web:hover .zpSSCard__mock--webLaptop img,html body .zpShowcaseServices .zpSSCard--web.isActive .zpSSCard__mock--webLaptop img,html body .zpShowcaseServices .zpSSCard--web.isVisible .zpSSCard__mock--webLaptop img{transform:scale(1.34)!important;transform-origin:right center!important;object-position:right center!important;max-width:none!important}html body #zpShowcaseServices .zpSSSignature__sign,html body .zpShowcaseServices .zpSSSignature__sign{display:block!important;margin-top:18px!important;min-height:1em!important;opacity:1!important;visibility:visible!important;color:#102a4f!important;-webkit-text-fill-color:#102a4f!important;font-family:var(--signatureFont,"Plus Jakarta Sans Local",system-ui,sans-serif)!important;font-size:clamp(22px,2.15vw,38px)!important;line-height:1!important;letter-spacing:-.02em!important;font-weight:400!important}html body #zpShowcaseServices .zpSSSignature__sign:empty::before,html body .zpShowcaseServices .zpSSSignature__sign:empty::before{content:'Zespół Zaprojektowani'}}</style>
<script id="zp-suite-front-fixes-264-v447-js">
(function(){
  var team='https://zaprojektowani.com/wp-content/uploads/2026/06/team_kontakt.webp';
  var apt='https://zaprojektowani.com/wp-content/uploads/2026/05/apartament_piekna_pokaz-1536x1357.webp';
  function fix(){
    document.querySelectorAll('.zpSSCard--portfolioCta .zpSSCard__mock--contact img, .zpShowcaseServices .zpSSCard--contact .zpSSCard__mock--contact img, #zpShowcaseServices .zpSSCard--contact .zpSSCard__mock--contact img').forEach(function(img){
      if(!img) return;
      if(img.getAttribute('src')!==team){ img.setAttribute('src',team); }
      img.removeAttribute('srcset'); img.removeAttribute('sizes');
    });
    document.querySelectorAll('#zpShowcaseServices .zpSSCard--web .zpSSCard__mock--webLaptop img, .zpShowcaseServices .zpSSCard--web .zpSSCard__mock--webLaptop img').forEach(function(img){
      if(!img) return;
      if(img.getAttribute('src')!==apt){ img.setAttribute('src',apt); }
      img.removeAttribute('srcset'); img.removeAttribute('sizes');
    });
    document.querySelectorAll('#zpShowcaseServices .zpSSSignature__sign, .zpShowcaseServices .zpSSSignature__sign').forEach(function(el){
      if(el && !el.textContent.trim()) el.textContent='Zespół Zaprojektowani';
    });
  }
  if(document.readyState==='loading') document.addEventListener('DOMContentLoaded',fix,{once:true}); else fix();
  window.addEventListener('load',fix,{once:true});
})();
</script>
  <?php
}
add_action('wp_head', 'zp_suite_264_team_photo_home_signature_css_js', PHP_INT_MAX);
add_action('wp_footer', 'zp_suite_264_team_photo_home_signature_css_js', PHP_INT_MAX);
