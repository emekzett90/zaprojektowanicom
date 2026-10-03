<?php
/**
 * v2.2.433 — Process tablet swipe polish.
 * - Restores person image on tablet/small laptop after swipe rewrite.
 * - Removes clipped card shadows in tablet swipe mode.
 * - Aligns “Przesuń w prawo” with card rail.
 * - Adds rocket/progress UI to strony-internetowe-katowice process section.
 */
if (!defined('ABSPATH')) { exit; }

add_action('wp_head', function () {
  ?>
<style id="zp-suite-front-fixes-250-process-tablet-polish-v433">@media (min-width:881px) and (max-width:1500px){.zpProcessFlow,.zpShopProcessFlow{--zpProcessTabletPad:clamp(44px,5vw,84px)}.zpProcessFlow__head,.zpShopProcessFlow__head{position:relative!important;display:grid!important;grid-template-columns:minmax(0,1fr) minmax(230px,32vw)!important;gap:clamp(22px,3.2vw,54px)!important;align-items:center!important;min-height:clamp(190px,24vw,315px)!important;overflow:visible!important}.zpProcessFlow__person,.zpShopProcessFlow__person{display:block!important;visibility:visible!important;opacity:1!important;position:absolute!important;z-index:20!important;right:clamp(0px,2vw,38px)!important;top:clamp(-36px,-2.4vw,-12px)!important;width:clamp(230px,28vw,410px)!important;min-width:0!important;max-width:none!important;height:auto!important;min-height:0!important;max-height:none!important;margin:0!important;padding:0!important;overflow:visible!important;pointer-events:none!important;transform:translate3d(4%,-4%,0) scale(.96)!important;transform-origin:center top!important;filter:none!important;animation:none!important}.zpProcessFlow__person img,.zpShopProcessFlow__person img{display:block!important;visibility:visible!important;opacity:1!important;width:100%!important;height:auto!important;min-width:0!important;max-width:none!important;min-height:0!important;max-height:none!important;margin:0!important;padding:0!important;object-fit:contain!important;object-position:center top!important;filter:none!important;transform:none!important}.zpProcessFlow__person::before,.zpProcessFlow__person::after,.zpShopProcessFlow__person::before,.zpShopProcessFlow__person::after{display:none!important;content:none!important}.zpProcessFlow__copy,.zpShopProcessFlow__copy{max-width:min(720px,calc(100% - clamp(190px,24vw,350px)))!important}.zpProcessFlow__mobileHint,.zpShopProcessFlow__mobileHint{display:flex!important;width:auto!important;max-width:none!important;margin:0 var(--zpProcessTabletPad) 14px!important;padding:0!important;justify-content:flex-start!important}.zpProcessFlow__track,.zpShopProcessFlow__track{padding-left:var(--zpProcessTabletPad)!important;padding-right:var(--zpProcessTabletPad)!important;scroll-padding-left:var(--zpProcessTabletPad)!important}.zpProcessFlow__card,.zpShopProcessFlow__card,.zpProcessFlow__card:hover,.zpShopProcessFlow__card:hover,.zpProcessFlow__card.is-active,.zpShopProcessFlow__card.is-active{box-shadow:none!important;filter:none!important}.zpProcessFlow__card::before,.zpProcessFlow__card::after,.zpShopProcessFlow__card::before,.zpShopProcessFlow__card::after{box-shadow:none!important;filter:none!important}.zpProcessFlow__progress.zpProcessFlow__progress--legacy{display:none!important}.zpProcessFlow__rocketProgress{--rocketSize:52px;--rocketLift:-20px;--rocketRotate:42deg;position:relative!important;display:block!important;width:100%!important;min-width:260px!important;align-self:end!important}.zpProcessFlow__rocketTrack{position:relative;height:56px;margin:0 0 8px}.zpProcessFlow__rocketTrack::before{content:"";position:absolute;left:0;right:0;top:50%;height:1px;border-radius:999px;background:rgba(7,17,31,.11)}.zpProcessFlow__rocketLine{position:absolute;left:0;top:50%;width:calc(var(--processProgress) * 100%);height:2px;border-radius:999px;background:linear-gradient(90deg,#05070b,#0b1830,#102a4f,#1c477a);transform:translateY(-50%);transition:width .08s linear}.zpProcessFlow__rocketGlow{position:absolute;top:50%;left:calc(var(--processProgress) * (100% - var(--rocketSize)));width:var(--rocketSize);height:var(--rocketSize);border-radius:999px;background:rgba(16,42,79,.13);filter:blur(18px);transform:translateY(-50%);pointer-events:none}.zpProcessFlow__rocketIcon{position:absolute;top:50%;left:calc(var(--processProgress) * (100% - var(--rocketSize)));width:var(--rocketSize);height:var(--rocketSize);border-radius:999px;display:grid;place-items:center;color:#fff;background:linear-gradient(135deg,#05070b,#0b1830,#102a4f,#1c477a);transform:translateY(calc(-50% + var(--rocketLift))) rotate(var(--rocketRotate));box-shadow:none!important;transition:left .08s linear}.zpProcessFlow__rocketIcon svg{width:24px;height:24px;stroke-width:2.15}.zpProcessFlow__rocketMeta{display:flex;align-items:center;justify-content:space-between;gap:14px;color:#07111f}.zpProcessFlow__rocketMeta span{color:#667386;font-size:9px;line-height:1;letter-spacing:.13em;font-weight:850;text-transform:uppercase}.zpProcessFlow__rocketMeta strong{display:inline-flex;align-items:baseline;gap:4px;color:#07111f;font-size:12px;line-height:1;letter-spacing:.045em;font-weight:850;text-transform:uppercase;white-space:nowrap}.zpProcessFlow__rocketMeta em{font-style:normal;color:#102a4f}}@media (min-width:881px) and (max-width:1120px){.zpProcessFlow__head,.zpShopProcessFlow__head{grid-template-columns:minmax(0,1fr) minmax(190px,28vw)!important;min-height:clamp(170px,25vw,260px)!important}.zpProcessFlow__person,.zpShopProcessFlow__person{width:clamp(200px,27vw,300px)!important;top:-18px!important;right:0!important;transform:translate3d(8%,-2%,0) scale(.92)!important}.zpProcessFlow__copy,.zpShopProcessFlow__copy{max-width:min(660px,calc(100% - 190px))!important}.zpProcessFlow__rocketProgress{--rocketSize:44px;--rocketLift:-17px}}</style>
<script id="zp-suite-front-fixes-250-process-rocket-js-v433">
(function(){
  'use strict';
  if(window.__zpProcessRocketV433) return;
  window.__zpProcessRocketV433 = true;

  function ready(fn){
    if(document.readyState === 'loading') document.addEventListener('DOMContentLoaded', fn, {once:true});
    else fn();
  }

  function injectRocket(){
    var root = document.getElementById('zpProcessFlow');
    if(!root || root.dataset.zpRocketProgressReady === '1') return;
    var topBar = root.querySelector('.zpProcessFlow__topBar');
    if(!topBar) return;

    var oldProgress = topBar.querySelector('.zpProcessFlow__progress');
    if(topBar.querySelector('.zpProcessFlow__rocketProgress')){
      root.dataset.zpRocketProgressReady = '1';
      return;
    }

    var rocket = document.createElement('div');
    rocket.className = 'zpProcessFlow__rocketProgress';
    rocket.setAttribute('aria-hidden','true');
    rocket.innerHTML = '<div class="zpProcessFlow__rocketTrack"><span class="zpProcessFlow__rocketLine"></span><span class="zpProcessFlow__rocketGlow"></span><span class="zpProcessFlow__rocketIcon"><i data-lucide="rocket"></i></span></div><div class="zpProcessFlow__rocketMeta"><span>Postęp procesu</span><strong><em data-zp-process-progress-label>0%</em> gotowe</strong></div>';

    if(oldProgress){
      oldProgress.classList.add('zpProcessFlow__progress--legacy');
      oldProgress.insertAdjacentElement('afterend', rocket);
    }else{
      topBar.appendChild(rocket);
    }

    root.dataset.zpRocketProgressReady = '1';
    if(window.lucide && typeof window.lucide.createIcons === 'function'){
      try{ window.lucide.createIcons({attrs:{'stroke-width':2,'stroke-linecap':'round','stroke-linejoin':'round','aria-hidden':'true'}}); }catch(e){}
    }
  }

  function clamp(n,min,max){ return Math.min(Math.max(n,min),max); }

  var raf = 0;
  function syncLabel(){
    raf = 0;
    var root = document.getElementById('zpProcessFlow');
    if(!root) return;
    var label = root.querySelector('[data-zp-process-progress-label]');
    if(!label) return;
    var raw = getComputedStyle(root).getPropertyValue('--processProgress') || '0';
    var p = parseFloat(String(raw).trim());
    if(!isFinite(p)) p = 0;
    label.textContent = Math.round(clamp(p,0,1) * 100) + '%';
  }
  function requestSync(){
    if(raf) return;
    raf = requestAnimationFrame(syncLabel);
  }

  ready(function(){
    injectRocket();
    requestSync();
    var root = document.getElementById('zpProcessFlow');
    var track = root ? root.querySelector('[data-zp-process-track]') : null;
    if(track) track.addEventListener('scroll', requestSync, {passive:true});
    window.addEventListener('scroll', requestSync, {passive:true});
    window.addEventListener('resize', requestSync, {passive:true});
    window.addEventListener('load', function(){ injectRocket(); requestSync(); }, {once:true, passive:true});
  });
})();
</script>
  <?php
}, 1002);
