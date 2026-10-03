<?php
/**
 * ZP Suite v2.2.802 — mobile first-paint sequencing, desktop robot/crew refinement,
 * and truly transparent mobile review glass.
 */
if (!defined('ABSPATH')) { exit; }

if (!function_exists('zp_suite_317_head')) {
  function zp_suite_317_head() {
    if (is_admin()) { return; }
    ?>
<script id="zp-suite-317-mobile-header-early">
(function(w,d){
  'use strict';
  try{
    if(!w.matchMedia || !w.matchMedia('(max-width:1100px)').matches) return;
    var h=d.documentElement, started=false, timer=0;
    h.classList.add('zp-mobile-ui-pending');
    function finish(){
      if(h.classList.contains('zp-mobile-header-ready')) return;
      w.requestAnimationFrame(function(){w.requestAnimationFrame(function(){
        h.classList.add('zp-mobile-header-ready');
        h.classList.remove('zp-mobile-ui-pending');
        try{d.dispatchEvent(new CustomEvent('zp:mobile-header-ready'));}catch(e){}
      });});
    }
    function boot(){
      if(started) return;
      var nav=d.getElementById('zpNewNav');
      if(!nav) return;
      started=true;
      var done=false;
      function go(){if(done)return;done=true;w.clearTimeout(timer);finish();}
      timer=w.setTimeout(go,180);
      try{
        if(d.fonts && d.fonts.load){
          d.fonts.load('700 16px "Plus Jakarta Sans Local"','Konsultacja').then(go,go);
        }else go();
      }catch(e){go();}
    }
    var mo=new MutationObserver(function(){boot();if(started)mo.disconnect();});
    mo.observe(d.documentElement,{childList:true,subtree:true});
    boot();
    w.setTimeout(function(){boot();if(!started)finish();},650);
  }catch(e){}
})(window,document);
</script>
<style id="zp-suite-317-final-polish">
@media (max-width:1100px){
  /* Header is the first animated UI. Keep hero copy out of the first paint until header is ready. */
  html:not(.zp-mobile-header-ready) body.home #zhHero .zh__copy,
  html:not(.zp-mobile-header-ready) body.front-page #zhHero .zh__copy,
  html.zp-mobile-ui-pending body.home #zhHero .zh__copy,
  html.zp-mobile-ui-pending body.front-page #zhHero .zh__copy{
    opacity:0!important;
    visibility:hidden!important;
    transform:translate3d(0,12px,0)!important;
    transition:none!important;
  }
  html.zp-mobile-header-ready body.home #zhHero .zh__copy,
  html.zp-mobile-header-ready body.front-page #zhHero .zh__copy{
    opacity:1!important;
    visibility:visible!important;
    transform:translate3d(0,0,0)!important;
    transition:opacity .46s cubic-bezier(.16,1,.3,1) .08s,transform .58s cubic-bezier(.16,1,.3,1) .08s!important;
  }

  /* Crew must not flash before the header. Give it a dedicated entrance after header reveal. */
  html:not(.zp-mobile-header-ready) body.home #zhHero .zh__crewImg,
  html:not(.zp-mobile-header-ready) body.front-page #zhHero .zh__crewImg,
  html.zp-mobile-ui-pending body.home #zhHero .zh__crewImg,
  html.zp-mobile-ui-pending body.front-page #zhHero .zh__crewImg{
    opacity:0!important;
    animation:none!important;
    transform:translate3d(0,18px,0)!important;
  }

  @keyframes zpCrewMobileEnter802{
    from{opacity:0;transform:translate3d(0,18px,0) scale(.97)}
    to{opacity:1;transform:translate3d(0,0,0) scale(1)}
  }
  @keyframes zpCrewMobileEnterMid802{
    from{opacity:0;transform:translate3d(0,18px,0) scale(.91)}
    to{opacity:1;transform:translate3d(0,0,0) scale(.94)}
  }
  @media (prefers-reduced-motion:no-preference){
    html.zp-mobile-header-ready body.home #zhHero .zh__crewImg--1,
    html.zp-mobile-header-ready body.front-page #zhHero .zh__crewImg--1{
      animation:zpCrewMobileEnter802 .58s cubic-bezier(.16,1,.3,1) .14s both!important;
    }
    html.zp-mobile-header-ready body.home #zhHero .zh__crewImg--2,
    html.zp-mobile-header-ready body.front-page #zhHero .zh__crewImg--2{
      animation:zpCrewMobileEnterMid802 .62s cubic-bezier(.16,1,.3,1) .20s both!important;
    }
    html.zp-mobile-header-ready body.home #zhHero .zh__crewImg--3,
    html.zp-mobile-header-ready body.front-page #zhHero .zh__crewImg--3{
      animation:zpCrewMobileEnter802 .58s cubic-bezier(.16,1,.3,1) .26s both!important;
    }
  }
  @media (prefers-reduced-motion:reduce){
    html.zp-mobile-header-ready body.home #zhHero .zh__crewImg,
    html.zp-mobile-header-ready body.front-page #zhHero .zh__crewImg{
      opacity:1!important;animation:none!important;transform:none!important;
    }
  }

  /* Reviews: real transparent glass, closer to desktop; remove the colored slab from 2.2.801. */
  html body.home #zhHero .zh__rev,
  html body.front-page #zhHero .zh__rev{
    background:rgba(1,4,12,.17)!important;
    background-color:rgba(1,4,12,.17)!important;
    border:1px solid rgba(255,255,255,.10)!important;
    box-shadow:0 18px 48px rgba(0,0,0,.20),inset 0 1px 0 rgba(255,255,255,.09),inset 0 -1px 0 rgba(255,255,255,.025)!important;
    -webkit-backdrop-filter:blur(28px) saturate(1.18)!important;
    backdrop-filter:blur(28px) saturate(1.18)!important;
  }
  html body.home #zhHero .zh__rev::before,
  html body.front-page #zhHero .zh__rev::before{
    background:linear-gradient(to bottom,rgba(255,255,255,.045),rgba(255,255,255,.008) 42%,rgba(1,4,12,.055))!important;
    opacity:1!important;
  }
  html body.home #zhHero .zh__rev::after,
  html body.front-page #zhHero .zh__rev::after{
    background:linear-gradient(180deg,rgba(255,255,255,.14),rgba(142,200,247,.04) 48%,rgba(255,255,255,.025))!important;
    opacity:.58!important;
  }
}

@media (min-width:1101px){
  /* Another 30 px up vs 2.2.801: moves poster + Spline + signet together. */
  html body.home #zhHero .zh__stage,
  html body.front-page #zhHero .zh__stage{
    transform:translate3d(0,-60px,0)!important;
  }

  /* Crew: +10% visual size, +20 px right, extra headroom so float never clips heads. */
  html body.home #zhHero .zh__crew,
  html body.front-page #zhHero .zh__crew{
    height:218px!important;
    margin:0 0 -67px 12px!important;
    align-items:flex-end!important;
    overflow:visible!important;
    -webkit-mask-image:linear-gradient(#000 0%,#000 60%,rgba(0,0,0,.58) 82%,rgba(0,0,0,0) 98%)!important;
    mask-image:linear-gradient(#000 0%,#000 60%,rgba(0,0,0,.58) 82%,rgba(0,0,0,0) 98%)!important;
  }
  html body.home #zhHero .zh__crewImg,
  html body.front-page #zhHero .zh__crewImg{
    height:206px!important;
    max-height:206px!important;
    align-self:flex-end!important;
  }
}
</style>
    <?php
  }
}
add_action('wp_head', 'zp_suite_317_head', PHP_INT_MAX);
