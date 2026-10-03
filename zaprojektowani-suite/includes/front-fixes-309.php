<?php
/**
 * v2.2.790 — Show the home hero entrance before the consent banner.
 * Only the banner's presentation waits; Cookiebot and prior-consent blocking
 * keep running normally. The hero releases this gate, with a five-second limit.
 */
if (!defined('ABSPATH')) { exit; }

if (!function_exists('zp_suite_309_hero_intro_gate')) {
  function zp_suite_309_hero_intro_gate() {
    if (!function_exists('zp_suite_308_is_home_like') || !zp_suite_308_is_home_like()) { return; }
    ?>
<style id="zp-suite-309-hero-intro-gate">
html.zp-hero-intro-pending #zp-cookiebot-banner,
html.zp-hero-intro-pending #zp-cookiebot-banner *,
html.zp-hero-intro-pending #CybotCookiebotDialog,
html.zp-hero-intro-pending #CybotCookiebotDialog *,
html.zp-hero-intro-pending #CybotCookiebotDialogBodyUnderlay,
html.zp-hero-intro-pending #CybotCookiebotDialogBodyUnderlay *{visibility:hidden!important;opacity:0!important;pointer-events:none!important}
</style>
<script id="zp-suite-309-hero-intro-gate-js">(function(d,w){'use strict';
if(w.ZPHeroIntroGate)return;
var root=d.documentElement,timer=0,gate={active:false,release:release};
w.ZPHeroIntroGate=gate;
function release(reason){
  if(!gate.active)return;
  gate.active=false;clearTimeout(timer);root.classList.remove('zp-hero-intro-pending');
  d.removeEventListener('DOMContentLoaded',ready);
  d.removeEventListener('visibilitychange',visibility);
  d.removeEventListener('pointerdown',interact,true);
  d.removeEventListener('keydown',interact,true);
  d.removeEventListener('touchstart',interact,true);
  d.removeEventListener('click',settings,true);
  d.removeEventListener('focusin',focus,true);
  w.removeEventListener('pagehide',pagehide);
  try{var detail={reason:reason||'complete'},event;
    if(typeof w.CustomEvent==='function')event=new w.CustomEvent('zp:hero-intro-release',{detail:detail});
    else{event=d.createEvent('CustomEvent');event.initCustomEvent('zp:hero-intro-release',false,false,detail);}
    d.dispatchEvent(event);
  }catch(e){}
}
function ready(){if(!d.querySelector('#zhHero .zh__title'))release('missing-hero');}
function visibility(){if(d.hidden)release('hidden');}
function interact(){release('interaction');}
function pagehide(){release('pagehide');}
function matches(target,selector){return !!(target&&target.nodeType===1&&target.closest&&target.closest(selector));}
function settings(e){if(matches(e.target,'[data-zp-cookie-settings]'))release('cookie-settings');}
function focus(e){if(matches(e.target,'#zp-cookiebot-banner,#CybotCookiebotDialog,#CybotCookiebotDialogBodyUnderlay,[data-zp-cookie-settings]'))release('cookie-focus');}
try{
  if(d.hidden||w.top!==w.self)return;
  if(w.matchMedia&&w.matchMedia('(prefers-reduced-motion:reduce)').matches)return;
  gate.active=true;
  // Register the watchdog before hiding anything, including when the hero JS is delayed.
  timer=w.setTimeout(function(){release('timeout');},5000);
  d.addEventListener('visibilitychange',visibility);
  d.addEventListener('pointerdown',interact,true);
  d.addEventListener('keydown',interact,true);
  d.addEventListener('touchstart',interact,true);
  d.addEventListener('click',settings,true);
  d.addEventListener('focusin',focus,true);
  w.addEventListener('pagehide',pagehide);
  root.classList.add('zp-hero-intro-pending');
  if(d.readyState==='loading')d.addEventListener('DOMContentLoaded',ready);
  else ready();
}catch(e){release('error');}
})(document,window);</script>
    <?php
  }
}
add_action('wp_head', 'zp_suite_309_hero_intro_gate', -100);
