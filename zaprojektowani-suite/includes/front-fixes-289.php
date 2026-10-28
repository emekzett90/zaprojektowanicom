<?php
/**
 * ZP Suite v2.2.566 — /strony-internetowe-katowice portfolio 16:9 mockups quality + softer clean edges, smaller + lower.
 * - Forces full/original mockup URLs, removes srcset/sizes that could choose thumbnails.
 * - Uses stronger outer-edge feathering and removes square/drop-shadow cuts under images.
 * - Reduces every portfolio mockup by another ~10% vs v2.2.565 and lowers visuals by 40px.
 */
if (!defined('ABSPATH')) { exit; }

function zp_suite_289_strony_portfolio_mockups_quality_css(){
  if (is_admin()) { return; }
  $uri = isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '';
  if (!zp_suite_is_service_page('strony', $uri)) { return; }
  ?>
<style id="zp-suite-front-fixes-289-portfolio-quality-v566">
@media (min-width:981px){
  html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--ap{--zpPortfolioMockScale:1.0125!important;--zpPortfolioMockHoverScale:1.0305!important}
  html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--siemianowski{--zpPortfolioMockScale:1.053!important;--zpPortfolioMockHoverScale:1.071!important}
  html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--gravia{--zpPortfolioMockScale:1.0125!important;--zpPortfolioMockHoverScale:1.0305!important}
  html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--shothome{--zpPortfolioMockScale:1.0125!important;--zpPortfolioMockHoverScale:1.0305!important}
  html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--proscarves{--zpPortfolioMockScale:.9315!important;--zpPortfolioMockHoverScale:.9495!important}
  html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--krawiec{--zpPortfolioMockScale:.9315!important;--zpPortfolioMockHoverScale:.9495!important}
  html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--papeterio{--zpPortfolioMockScale:.81!important;--zpPortfolioMockHoverScale:.828!important}
  html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--bransoletka{--zpPortfolioMockScale:.81!important;--zpPortfolioMockHoverScale:.828!important}
  html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--rutpoz{--zpPortfolioMockScale:.81!important;--zpPortfolioMockHoverScale:.828!important}

  html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--portfolio .zpSSCard__visual{
    overflow:visible!important;
    contain:none!important;
  }
  html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--portfolio .zpSSCard__mock--portfolio{
    overflow:visible!important;
    background:transparent!important;
    box-shadow:none!important;
    filter:none!important;
    -webkit-filter:none!important;
    transform-origin:center center!important;
    transform:translate3d(0,calc(var(--move,0px) + 40px),0) rotate(var(--rot,0deg)) scale(var(--zpPortfolioMockScale,.81))!important;
    will-change:transform!important;
  }
  html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--portfolio:hover .zpSSCard__mock--portfolio,
  html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--portfolio.isActive .zpSSCard__mock--portfolio,
  html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--portfolio.isVisible .zpSSCard__mock--portfolio{
    transform:translate3d(0,calc(var(--move,0px) + 32px),0) rotate(var(--rot,0deg)) scale(var(--zpPortfolioMockHoverScale,.83))!important;
  }
}
html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--portfolio .zpSSCard__mock--portfolio img{
  display:block!important;
  width:100%!important;
  height:100%!important;
  max-width:none!important;
  object-fit:contain!important;
  object-position:center!important;
  background:transparent!important;
  image-rendering:auto!important;
  transform:none!important;
  filter:blur(.18px) contrast(1.005) saturate(1.005)!important;
  backface-visibility:hidden!important;
  -webkit-backface-visibility:hidden!important;
  /* Soft edge mask: czyści ostre prostokątne/poszarpane brzegi przezroczystych mockupów bez ucinania środka grafiki. */
  -webkit-mask-image:linear-gradient(90deg,transparent 0%,rgba(0,0,0,.35) 2.2%,#000 7%,#000 93%,rgba(0,0,0,.35) 97.8%,transparent 100%),linear-gradient(180deg,transparent 0%,rgba(0,0,0,.45) 2.8%,#000 8%,#000 92%,rgba(0,0,0,.45) 97.2%,transparent 100%),radial-gradient(ellipse 72% 63% at 50% 54%,#000 0%,#000 64%,rgba(0,0,0,.88) 78%,rgba(0,0,0,.48) 90%,transparent 100%)!important;
  mask-image:linear-gradient(90deg,transparent 0%,rgba(0,0,0,.35) 2.2%,#000 7%,#000 93%,rgba(0,0,0,.35) 97.8%,transparent 100%),linear-gradient(180deg,transparent 0%,rgba(0,0,0,.45) 2.8%,#000 8%,#000 92%,rgba(0,0,0,.45) 97.2%,transparent 100%),radial-gradient(ellipse 72% 63% at 50% 54%,#000 0%,#000 64%,rgba(0,0,0,.88) 78%,rgba(0,0,0,.48) 90%,transparent 100%)!important;
  -webkit-mask-composite:source-in,source-in!important;
  mask-composite:intersect!important;
  -webkit-mask-repeat:no-repeat!important;
  mask-repeat:no-repeat!important;
  -webkit-mask-size:100% 100%!important;
  mask-size:100% 100%!important;
}
@media (max-width:980px){
  html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--portfolio .zpSSCard__mock--portfolio{
    transform:translate3d(0,calc(var(--move,0px) + 40px),0) rotate(var(--rot,0deg)) scale(.81)!important;
    filter:none!important;
    -webkit-filter:none!important;
    overflow:visible!important;
    background:transparent!important;
    box-shadow:none!important;
  }
  html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--portfolio:hover .zpSSCard__mock--portfolio,
  html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--portfolio.isActive .zpSSCard__mock--portfolio,
  html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--portfolio.isVisible .zpSSCard__mock--portfolio{
    transform:translate3d(0,calc(var(--move,0px) + 35px),0) rotate(var(--rot,0deg)) scale(.83)!important;
  }
  html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--portfolio .zpSSCard__mock--portfolio img{
    -webkit-mask-image:linear-gradient(90deg,transparent 0%,rgba(0,0,0,.35) 2.5%,#000 8%,#000 92%,rgba(0,0,0,.35) 97.5%,transparent 100%),linear-gradient(180deg,transparent 0%,rgba(0,0,0,.48) 3%,#000 9%,#000 91%,rgba(0,0,0,.48) 97%,transparent 100%),radial-gradient(ellipse 76% 68% at 50% 54%,#000 0%,#000 66%,rgba(0,0,0,.86) 79%,rgba(0,0,0,.48) 90%,transparent 100%)!important;
    mask-image:linear-gradient(90deg,transparent 0%,rgba(0,0,0,.35) 2.5%,#000 8%,#000 92%,rgba(0,0,0,.35) 97.5%,transparent 100%),linear-gradient(180deg,transparent 0%,rgba(0,0,0,.48) 3%,#000 9%,#000 91%,rgba(0,0,0,.48) 97%,transparent 100%),radial-gradient(ellipse 76% 68% at 50% 54%,#000 0%,#000 66%,rgba(0,0,0,.86) 79%,rgba(0,0,0,.48) 90%,transparent 100%)!important;
    -webkit-mask-composite:source-in,source-in!important;
    mask-composite:intersect!important;
  }
}
</style>
<?php
}
add_action('wp_head', 'zp_suite_289_strony_portfolio_mockups_quality_css', 9998);

function zp_suite_289_strony_portfolio_force_original_js(){
  if (is_admin()) { return; }
  $uri = isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '';
  if (!zp_suite_is_service_page('strony', $uri)) { return; }
  ?>
<script id="zp-suite-front-fixes-289-portfolio-original-js-v566">
(function(){
  var full = {
    ap:'https://zaprojektowani.com/wp-content/uploads/2026/06/apartament_mockup-scaled.webp?v=566',
    bransoletka:'https://zaprojektowani.com/wp-content/uploads/2026/06/bransoletka24_mockup-scaled.webp?v=566',
    gravia:'https://zaprojektowani.com/wp-content/uploads/2026/06/gravia_mockup.webp?v=566',
    krawiec:'https://zaprojektowani.com/wp-content/uploads/2026/06/kaminski_mockup-scaled.webp?v=566',
    papeterio:'https://zaprojektowani.com/wp-content/uploads/2026/06/papeterio_mockup-scaled.webp?v=566',
    proscarves:'https://zaprojektowani.com/wp-content/uploads/2026/06/proscarves_mockup-scaled.webp?v=566',
    rutpoz:'https://zaprojektowani.com/wp-content/uploads/2026/06/rutpoz_mockuP-scaled.webp?v=566',
    shothome:'https://zaprojektowani.com/wp-content/uploads/2026/06/shothome_mockup.webp?v=566',
    siemianowski:'https://zaprojektowani.com/wp-content/uploads/2026/06/siemianowski_mockup.webp?v=566'
  };
  function forceOriginal(){
    Object.keys(full).forEach(function(slug){
      var card = document.querySelector('#zpStronyKatowice #zpPortfolioReveal .zpSSCard--' + slug);
      if(!card) return;
      var img = card.querySelector('.zpSSCard__mock--portfolio img');
      if(!img) return;
      img.removeAttribute('srcset');
      img.removeAttribute('sizes');
      img.setAttribute('data-no-lazy','1');
      img.setAttribute('data-skip-lazy','1');
      img.setAttribute('data-no-optimize','1');
      img.setAttribute('decoding','async');
      img.setAttribute('draggable','false');
      if(img.getAttribute('src') !== full[slug]) img.setAttribute('src', full[slug]);
    });
  }
  forceOriginal();
  if(document.readyState === 'loading') document.addEventListener('DOMContentLoaded', forceOriginal, {once:true});
  window.addEventListener('load', forceOriginal, {once:true});
})();
</script>
<?php
}
add_action('wp_footer', 'zp_suite_289_strony_portfolio_force_original_js', 40);
