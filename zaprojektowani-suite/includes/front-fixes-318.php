<?php
/**
 * ZP Suite v2.2.803 — hard mobile header-first gate + crew 30 px higher.
 *
 * - Entire home hero is withheld from first paint on mobile until the complete header exists.
 * - Header reveal waits for call button, logo, language chip and burger.
 * - Hero then fades/slides in as the second stage.
 * - Mobile crew is visually lifted another 30 px without pulling the kicker/title upward.
 */
if (!defined('ABSPATH')) { exit; }

if (!function_exists('zp_suite_318_header_first_gate')) {
  function zp_suite_318_header_first_gate() {
    if (is_admin()) { return; }
    ?>
<script id="zp-suite-318-header-first-gate-js">
(function(w,d){
  'use strict';
  try{
    if(!w.matchMedia || !w.matchMedia('(max-width:1100px)').matches) return;
    var h=d.documentElement, done=false, mo=null, safety=0;
    h.classList.add('zp-mobile-hard-gate');
    h.classList.add('zp-mobile-ui-pending');
    h.classList.remove('zp-mobile-header-ready');
    h.classList.remove('zp-mobile-hero-ready');

    function completeHeaderPresent(){
      var n=d.getElementById('zpNewNav');
      if(!n) return false;
      return !!(
        n.querySelector('.zpNewNav__mobileBar') &&
        n.querySelector('.zpNewNav__mobileCall') &&
        n.querySelector('.zpNewNav__mobileBrand') &&
        n.querySelector('.zpl-switch--chip') &&
        n.querySelector('.zpNewNav__burger')
      );
    }
    function reveal(){
      if(done) return;
      done=true;
      if(mo){try{mo.disconnect();}catch(e){} mo=null;}
      w.clearTimeout(safety);
      w.requestAnimationFrame(function(){
        h.classList.add('zp-mobile-header-ready');
        h.classList.remove('zp-mobile-ui-pending');
        w.requestAnimationFrame(function(){
          w.setTimeout(function(){
            h.classList.add('zp-mobile-hero-ready');
            h.classList.remove('zp-mobile-hard-gate');
            try{d.dispatchEvent(new CustomEvent('zp:mobile-header-ready'));}catch(e){}
          },180);
        });
      });
    }
    function tryReveal(){
      if(!completeHeaderPresent()) return;
      try{
        if(d.fonts && d.fonts.load){
          Promise.race([
            d.fonts.load('700 16px "Plus Jakarta Sans Local"','Konsultacja'),
            new Promise(function(r){w.setTimeout(r,240);})
          ]).then(reveal,reveal);
        } else reveal();
      }catch(e){reveal();}
    }
    mo=new MutationObserver(tryReveal);
    mo.observe(d.documentElement,{childList:true,subtree:true});
    tryReveal();
    safety=w.setTimeout(function(){
      /* Never leave the page locked, but keep header-first ordering even on a broken late asset. */
      if(!done) reveal();
    },950);
  }catch(e){}
})(window,document);
</script>
<style id="zp-suite-318-header-first-gate-css">
@media (max-width:1100px){
  /* Hard gate: nothing from home hero can paint before the mobile header is complete. */
  html:not(.zp-mobile-hero-ready) body.home #zhHero,
  html:not(.zp-mobile-hero-ready) body.front-page #zhHero,
  html.zp-mobile-hard-gate body.home #zhHero,
  html.zp-mobile-hard-gate body.front-page #zhHero{
    opacity:0!important;
    visibility:hidden!important;
    pointer-events:none!important;
    transform:translate3d(0,8px,0)!important;
    transition:none!important;
  }
  html.zp-mobile-hero-ready body.home #zhHero,
  html.zp-mobile-hero-ready body.front-page #zhHero{
    opacity:1!important;
    visibility:visible!important;
    pointer-events:auto!important;
    transform:translate3d(0,0,0)!important;
    transition:opacity .48s cubic-bezier(.16,1,.3,1),transform .62s cubic-bezier(.16,1,.3,1)!important;
  }

  /* Header itself stays fully above the gated hero. */
  html body #zpNewNav{
    z-index:2147483000!important;
  }

  /* Team image only: another 30 px up; layout/kicker stays where it was. */
  html body.home #zhHero .zh__crew,
  html body.front-page #zhHero .zh__crew{
    transform:translate3d(0,-30px,0)!important;
    will-change:transform!important;
  }
}

@media (prefers-reduced-motion:reduce) and (max-width:1100px){
  html.zp-mobile-hero-ready body.home #zhHero,
  html.zp-mobile-hero-ready body.front-page #zhHero{transition:none!important}
}
</style>
    <?php
  }
}
/* Run after the older head gates so this is the final sequencing authority. */
add_action('wp_head', 'zp_suite_318_header_first_gate', PHP_INT_MAX);
