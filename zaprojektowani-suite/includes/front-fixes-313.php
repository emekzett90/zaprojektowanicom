<?php
/**
 * ZP Suite v2.2.797 — mobile header staged entrance, sticky dock smooth reveal,
 * and darker home robot on mobile for better title legibility.
 */
if (!defined('ABSPATH')) { exit; }

if (!function_exists('zp_suite_313_mobile_ui_head')) {
  function zp_suite_313_mobile_ui_head() {
    if (is_admin()) { return; }
    ?>
<script id="zp-suite-313-mobile-ui-pre">document.documentElement.classList.add('zp-mobile-ui-pending');</script>
<style id="zp-suite-313-mobile-ui-css">
@media (max-width:1100px){
  html body #zpNewNav .zpNewNav__mobileBar,
  html body #zpNewNav .zpNewNav__mobileCall,
  html body #zpNewNav .zpNewNav__mobileBrand,
  html body #zpNewNav .zpNewNav__mobileActions,
  html body #zpNewNav .zpNewNav__mobileActions > *,
  html body #zpNewNav .zpl-switch--chip{
    font-family:var(--zp-font,"Plus Jakarta Sans Local","Plus Jakarta Sans",system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif)!important;
  }

  /* Hold the whole mobile header for a brief moment so it can enter as one finished unit
     instead of flashing only the consultation button with a fallback font. */
  html.zp-mobile-ui-pending body #zpNewNav .zpNewNav__mobileBar{
    opacity:0!important;
    visibility:hidden!important;
    pointer-events:none!important;
    transform:translate3d(0,-10px,0)!important;
  }
  html body #zpNewNav .zpNewNav__mobileBar{
    transition:opacity .38s cubic-bezier(.16,1,.3,1),transform .46s cubic-bezier(.16,1,.3,1),visibility 0s linear!important;
    will-change:opacity,transform!important;
  }
  html.zp-mobile-header-ready body #zpNewNav .zpNewNav__mobileBar{
    opacity:1!important;
    visibility:visible!important;
    pointer-events:auto!important;
    transform:translate3d(0,0,0)!important;
  }

  /* Reserve icon boxes before Lucide swaps <i> to <svg> to avoid micro-jumps. */
  html body #zpNewNav .zpNewNav__mobileCall i[data-lucide],
  html body #zpNewNav .zpNewNav__burger i[data-lucide]{
    display:inline-flex!important;
    align-items:center!important;
    justify-content:center!important;
    width:15px!important;
    min-width:15px!important;
    height:15px!important;
    min-height:15px!important;
    line-height:1!important;
    color:currentColor!important;
    vertical-align:middle!important;
    overflow:hidden!important;
  }
  html body #zpNewNav .zpNewNav__burger i[data-lucide]{width:20px!important;min-width:20px!important;height:20px!important;min-height:20px!important;}
}

@media (max-width:760px){
  /* Reveal the bottom dock only after the page is ready so Safari does not stall
     on the final loading phase when the sticky UI is mounted. */
  html.zp-mobile-ui-pending body .zpMobileStickyCta,
  html:not(.zp-mobile-dock-ready) body .zpMobileStickyCta{
    opacity:0!important;
    visibility:hidden!important;
    pointer-events:none!important;
    transform:translate3d(0,14px,0)!important;
  }
  html body .zpMobileStickyCta{
    transition:opacity .34s cubic-bezier(.16,1,.3,1),transform .42s cubic-bezier(.16,1,.3,1),visibility 0s linear!important;
    will-change:opacity,transform!important;
  }
  html.zp-mobile-dock-ready body .zpMobileStickyCta{
    opacity:1!important;
    visibility:visible!important;
    pointer-events:auto!important;
    transform:translate3d(0,0,0)!important;
  }

  html.zp-mobile-ui-pending body .zpFloatUx__top,
  html:not(.zp-mobile-dock-ready) body .zpFloatUx__top{
    opacity:0!important;
    transform:translate3d(0,10px,0) scale(.94)!important;
    pointer-events:none!important;
  }

  /* Better readability of the main title over the robot on mobile. */
  html body #zhHero .zh__poster:not(#zpx){
    filter:brightness(.62) saturate(.92)!important;
  }
  html body #zhHero::before{
    content:"";
    position:absolute;
    inset:0;
    pointer-events:none;
    z-index:3;
    background:linear-gradient(90deg,rgba(2,4,7,.42) 0%,rgba(2,4,7,.38) 36%,rgba(2,4,7,.18) 58%,rgba(2,4,7,0) 78%);
  }
  html body #zhHero .zh__copy,
  html body #zhHero .zh__side,
  html body #zhHero .zh__crew,
  html body #zhHero .zh__eb,
  html body #zhHero .zh__actions,
  html body #zhHero .zh__chips{
    position:relative!important;
    z-index:6!important;
  }
}
</style>
    <?php
  }
}
add_action('wp_head', 'zp_suite_313_mobile_ui_head', PHP_INT_MAX);

if (!function_exists('zp_suite_313_mobile_ui_footer')) {
  function zp_suite_313_mobile_ui_footer() {
    if (is_admin()) { return; }
    ?>
<script id="zp-suite-313-mobile-ui-js">
(function(w,d){
  'use strict';
  var html=d.documentElement;
  function mq(q){ try{return w.matchMedia(q).matches;}catch(e){ return false; } }
  function isMobileHeader(){ return mq('(max-width: 1100px)'); }
  function isMobileDock(){ return mq('(max-width: 760px)'); }
  function twoRaf(cb){ w.requestAnimationFrame(function(){ w.requestAnimationFrame(cb); }); }
  function delay(ms){ return new Promise(function(resolve){ setTimeout(resolve, ms||0); }); }
  function whenFontsReady(){
    try{
      if(d.fonts && d.fonts.ready && typeof d.fonts.ready.then === 'function'){
        return Promise.race([d.fonts.ready['catch'](function(){}), delay(650)]);
      }
    }catch(e){}
    return delay(180);
  }
  function whenImagesReady(nodes){
    nodes=[].slice.call(nodes||[]).filter(Boolean);
    if(!nodes.length) return Promise.resolve();
    return Promise.race([
      Promise.all(nodes.map(function(img){
        if(img.complete && img.naturalWidth>0) return Promise.resolve();
        return new Promise(function(resolve){
          var done=function(){ resolve(); };
          img.addEventListener('load',done,{once:true});
          img.addEventListener('error',done,{once:true});
          setTimeout(done,360);
        });
      })),
      delay(420)
    ]);
  }
  function revealHeader(){
    if(!isMobileHeader()){
      html.classList.add('zp-mobile-header-ready');
      return;
    }
    var nav=d.getElementById('zpNewNav');
    var imgs=nav ? nav.querySelectorAll('.zpNewNav__mobileLogo') : [];
    Promise.all([whenFontsReady(), whenImagesReady(imgs), delay(40)]).then(function(){
      twoRaf(function(){
        html.classList.add('zp-mobile-header-ready');
        html.classList.remove('zp-mobile-ui-pending');
      });
    });
  }
  function revealDock(){
    if(!isMobileDock()) return;
    twoRaf(function(){ html.classList.add('zp-mobile-dock-ready'); });
  }
  if(d.readyState === 'loading') d.addEventListener('DOMContentLoaded', revealHeader, {once:true});
  else revealHeader();
  if(d.readyState === 'complete') setTimeout(revealDock, 70);
  else w.addEventListener('load', function(){ setTimeout(revealDock, 70); }, {once:true, passive:true});
  setTimeout(function(){ html.classList.remove('zp-mobile-ui-pending'); html.classList.add('zp-mobile-header-ready'); }, 1300);
  setTimeout(function(){ html.classList.add('zp-mobile-dock-ready'); }, 2100);
})(window,document);
</script>
    <?php
  }
}
add_action('wp_footer', 'zp_suite_313_mobile_ui_footer', 1);
