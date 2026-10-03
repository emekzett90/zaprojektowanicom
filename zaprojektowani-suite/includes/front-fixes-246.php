<?php
if (!defined('ABSPATH')) exit;
/**
 * ZP Suite v2.2.428 — home tablet fixes:
 * 1) Portfolio under home: tablet/small laptop uses a static horizontal rail, not desktop sticky spacer.
 *    This removes the huge empty gap and keeps the dark CTA visible after portfolio.
 * 2) Services section: on tablet/small laptop the Stanisław photo is moved down so it does not enter
 *    the logo marquee/trust section above.
 */
add_action('wp_head', function(){
  if (is_admin()) return;
  // v2.2.492: CSS is body.home-scoped and the JS targets the home portfolio rail.
  // Print only on home or on pages that actually render the portfolio shortcode.
  $zp246_show = is_front_page() || is_home();
  if (!$zp246_show && is_singular()) {
    global $post;
    $zp246_content = $post && !empty($post->post_content) ? (string) $post->post_content : '';
    $zp246_show = $zp246_content !== '' && (has_shortcode($zp246_content, 'zp_showcase_portfolio') || has_shortcode($zp246_content, 'zp_home_full') || has_shortcode($zp246_content, 'zp_home'));
  }
  if (!$zp246_show) return;
  ?>
<style id="zp-suite-front-fixes-246-home-tablet-portfolio-services-v428">@media (min-width:761px) and (max-width:1500px){html body.home #zpShowcaseWhite,html body.front-page #zpShowcaseWhite{overflow:hidden!important;overflow-anchor:none!important;background:#fff!important}html body.home #zpShowcaseWhite .zpShowcaseWhite__spacer,html body.front-page #zpShowcaseWhite .zpShowcaseWhite__spacer{min-height:0!important;height:auto!important;position:relative!important;overflow:visible!important}html body.home #zpShowcaseWhite .zpShowcaseWhite__sticky,html body.front-page #zpShowcaseWhite .zpShowcaseWhite__sticky{position:relative!important;top:auto!important;height:auto!important;min-height:0!important;overflow:visible!important;contain:none!important;display:block!important;background:#fff!important}html body.home #zpShowcaseWhite .zpShowcaseWhite__inner,html body.front-page #zpShowcaseWhite .zpShowcaseWhite__inner{width:100%!important;max-width:100%!important;min-height:0!important;height:auto!important;padding:clamp(58px,6vw,86px) 0 clamp(54px,6vw,82px)!important;overflow:visible!important}html body.home #zpShowcaseWhite .zpShowcaseWhite__head,html body.front-page #zpShowcaseWhite .zpShowcaseWhite__head,html body.home #zpShowcaseWhite.is-flowing .zpShowcaseWhite__head,html body.front-page #zpShowcaseWhite.is-flowing .zpShowcaseWhite__head{width:min(1500px,calc(100% - 48px))!important;max-width:1500px!important;margin:0 auto clamp(24px,3vw,38px)!important;grid-template-columns:minmax(0,1.08fr) minmax(320px,.72fr)!important;gap:clamp(24px,4vw,58px)!important;align-items:end!important;opacity:1!important;transform:none!important;pointer-events:auto!important;max-height:none!important;overflow:visible!important}html body.home #zpShowcaseWhite .zpShowcaseWhite__title,html body.front-page #zpShowcaseWhite .zpShowcaseWhite__title{font-size:clamp(42px,4.7vw,68px)!important;line-height:.95!important}html body.home #zpShowcaseWhite .zpShowcaseWhite__lead,html body.front-page #zpShowcaseWhite .zpShowcaseWhite__lead{max-width:620px!important}html body.home #zpShowcaseWhite .zpShowcaseWhite__floatingProgress,html body.front-page #zpShowcaseWhite .zpShowcaseWhite__floatingProgress{display:none!important}html body.home #zpShowcaseWhite .zpShowcaseWhite__stage,html body.front-page #zpShowcaseWhite .zpShowcaseWhite__stage{width:100vw!important;max-width:100vw!important;margin-left:calc(50% - 50vw)!important;margin-top:0!important;overflow-x:auto!important;overflow-y:hidden!important;-webkit-overflow-scrolling:touch!important;scroll-snap-type:x mandatory!important;scroll-padding-left:clamp(28px,5vw,72px)!important;overscroll-behavior-x:contain!important;overscroll-behavior-y:auto!important;touch-action:pan-x pan-y!important;scrollbar-width:none!important;min-height:0!important;height:auto!important;padding:2px 0 clamp(18px,2vw,28px)!important;contain:layout paint!important;transform:none!important;opacity:1!important}html body.home #zpShowcaseWhite .zpShowcaseWhite__stage::-webkit-scrollbar,html body.front-page #zpShowcaseWhite .zpShowcaseWhite__stage::-webkit-scrollbar{display:none!important}html body.home #zpShowcaseWhite .zpShowcaseWhite__rail,html body.front-page #zpShowcaseWhite .zpShowcaseWhite__rail{width:max-content!important;min-width:max-content!important;display:flex!important;align-items:flex-start!important;gap:clamp(16px,1.8vw,24px)!important;padding:0 clamp(28px,5vw,72px) 0 clamp(28px,5vw,72px)!important;transform:none!important;transition:none!important;will-change:auto!important;contain:layout paint!important;min-height:0!important}html body.home #zpShowcaseWhite .zpShowcaseWhite__card,html body.front-page #zpShowcaseWhite .zpShowcaseWhite__card,html body.home #zpShowcaseWhite.isReady .zpShowcaseWhite__card,html body.front-page #zpShowcaseWhite.isReady .zpShowcaseWhite__card,html body.home #zpShowcaseWhite.isReady .zpShowcaseWhite__card.isVisible,html body.front-page #zpShowcaseWhite.isReady .zpShowcaseWhite__card.isVisible,html body.home #zpShowcaseWhite.isReady .zpShowcaseWhite__card.isActive,html body.front-page #zpShowcaseWhite.isReady .zpShowcaseWhite__card.isActive{flex:0 0 min(72vw,760px)!important;width:min(72vw,760px)!important;min-width:min(72vw,760px)!important;aspect-ratio:2560/1707!important;max-height:none!important;height:auto!important;min-height:0!important;opacity:1!important;transform:none!important;filter:none!important;scroll-snap-align:start!important;scroll-snap-stop:always!important;transition:none!important}html body.home #zpShowcaseWhite[data-mode="logo"] .zpShowcaseWhite__card,html body.front-page #zpShowcaseWhite[data-mode="logo"] .zpShowcaseWhite__card{flex-basis:min(56vw,620px)!important;width:min(56vw,620px)!important;min-width:min(56vw,620px)!important;aspect-ratio:1/1!important}html body.home #zpShowcaseWhite .zpShowcaseWhite__media img,html body.front-page #zpShowcaseWhite .zpShowcaseWhite__media img{transform:none!important;object-fit:cover!important;object-position:center center!important}html body.home #zpShowcaseWhite .zpShowcaseWhite__bottom,html body.front-page #zpShowcaseWhite .zpShowcaseWhite__bottom,html body.home #zpShowcaseWhite.is-flowing .zpShowcaseWhite__bottom,html body.front-page #zpShowcaseWhite.is-flowing .zpShowcaseWhite__bottom{width:min(1500px,calc(100% - 48px))!important;margin:clamp(18px,2.4vw,30px) auto 0!important;display:flex!important;opacity:1!important;transform:none!important;pointer-events:auto!important}html body.home .zpShowcaseDarkCta,html body.front-page .zpShowcaseDarkCta{display:block!important;visibility:visible!important;height:auto!important;min-height:0!important;max-height:none!important;margin:0!important;padding:clamp(74px,7vw,108px) 0 clamp(82px,7vw,118px)!important;overflow:hidden!important;pointer-events:auto!important}html body.home .zpShowcaseDarkCta__inner,html body.front-page .zpShowcaseDarkCta__inner{width:min(1500px,calc(100% - 48px))!important;max-width:1500px!important;grid-template-columns:minmax(0,1fr) minmax(360px,520px)!important;gap:clamp(28px,5vw,72px)!important;align-items:center!important}html body.home .zpShowcaseDarkCta h2,html body.front-page .zpShowcaseDarkCta h2{font-size:clamp(42px,5vw,72px)!important;line-height:.96!important}html body.home .zpShowcaseDarkCta__frame,html body.front-page .zpShowcaseDarkCta__frame{min-height:clamp(260px,25vw,340px)!important}html body.home #zpServicesPath,html body.front-page #zpServicesPath{padding-top:clamp(132px,10.5vw,178px)!important;overflow:hidden!important}html body.home #zpServicesPath .zpServicesPath__person,html body.front-page #zpServicesPath .zpServicesPath__person{top:clamp(36px,4.8vw,72px)!important;transform:translateX(var(--zp-person-x)) translateY(0) scale(calc(var(--zp-person-scale) * .92))!important}html body.home #zpServicesPath.is-inview .zpServicesPath__person,html body.front-page #zpServicesPath.is-inview .zpServicesPath__person{transform:translateX(var(--zp-person-x)) translateY(0) scale(calc(var(--zp-person-scale) * .92))!important}}@media (min-width:761px) and (max-width:1180px){html body.home .zpShowcaseDarkCta__inner,html body.front-page .zpShowcaseDarkCta__inner{grid-template-columns:1fr!important}html body.home .zpShowcaseDarkCta__panel,html body.front-page .zpShowcaseDarkCta__panel{max-width:680px!important}}</style>
<script id="zp-suite-front-fixes-246-home-tablet-portfolio-js-v428">
(function(){
  function isTabletPortfolio(){
    var w = window.innerWidth || document.documentElement.clientWidth || 0;
    return w >= 761 && w <= 1500;
  }
  function stabilize(){
    if(!isTabletPortfolio()) return;
    var root = document.getElementById('zpShowcaseWhite');
    if(!root) return;
    var spacer = root.querySelector('[data-zp-showcase-spacer], .zpShowcaseWhite__spacer');
    var sticky = root.querySelector('[data-zp-showcase-sticky], .zpShowcaseWhite__sticky');
    var rail = root.querySelector('[data-zp-showcase-rail], .zpShowcaseWhite__rail');
    var stage = root.querySelector('[data-zp-showcase-stage], .zpShowcaseWhite__stage');
    root.classList.add('is-visible');
    root.classList.remove('is-flowing');
    if(spacer){ spacer.style.minHeight = '0px'; spacer.style.height = 'auto'; }
    if(sticky){ sticky.style.position = 'relative'; sticky.style.height = 'auto'; sticky.style.minHeight = '0px'; }
    if(rail){ rail.style.transform = 'none'; }
    if(stage){ stage.style.overflowX = 'auto'; stage.style.overflowY = 'hidden'; }
  }
  if(document.readyState === 'loading') document.addEventListener('DOMContentLoaded', stabilize, {once:true});
  else stabilize();
  window.addEventListener('load', stabilize, {once:true});
  window.addEventListener('resize', function(){ window.requestAnimationFrame(stabilize); }, {passive:true});
})();
</script>
  <?php
}, 2147483647);
