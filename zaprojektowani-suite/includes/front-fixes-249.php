<?php
/**
 * v2.2.432 — Process sections tablet rewrite.
 * Tablet / small laptop no longer uses pinned vertical scroll.
 * Instead it uses native horizontal swipe/track scroll, like mobile, adapted to wider screens.
 */
if (!defined('ABSPATH')) { exit; }

add_action('wp_head', function () {
  ?>
<style id="zp-suite-front-fixes-249-process-tablet-swipe-v432">@media (min-width:881px) and (max-width:1500px){.zpProcessFlow,.zpShopProcessFlow{--processProgress:0;--trackX:0px;padding-top:clamp(48px,6vw,76px)!important;padding-bottom:clamp(46px,6vw,76px)!important;overflow:hidden!important;background:#fff!important}.zpProcessFlow__watermark,.zpShopProcessFlow__watermark{opacity:.018!important;bottom:-.02em!important}.zpProcessFlow__inner,.zpShopProcessFlow__inner{width:min(1320px,calc(100% - clamp(44px,5vw,84px)))!important;max-width:100%!important;overflow:visible!important}.zpProcessFlow__inner--head,.zpShopProcessFlow__inner--head{z-index:10!important}.zpProcessFlow__head,.zpShopProcessFlow__head{display:block!important;min-height:0!important;height:auto!important;margin:0!important;padding:0!important;overflow:visible!important}.zpProcessFlow__copy,.zpShopProcessFlow__copy{max-width:min(980px,100%)!important;padding:0!important;margin:0!important;transform:none!important;opacity:1!important;filter:none!important}.zpProcessFlow h2,.zpShopProcessFlow h2{max-width:980px!important;font-size:clamp(42px,5.2vw,66px)!important;line-height:.98!important;letter-spacing:-.052em!important}.zpShopProcessFlow__lead{max-width:820px!important;font-size:clamp(14px,1.25vw,17px)!important;line-height:1.62!important}.zpProcessFlow__person,.zpProcessFlow__person img,.zpProcessFlow__person::before,.zpProcessFlow__person::after,.zpShopProcessFlow__person,.zpShopProcessFlow__person img,.zpShopProcessFlow__person::before,.zpShopProcessFlow__person::after{display:none!important;visibility:hidden!important;opacity:0!important;width:0!important;height:0!important;min-width:0!important;min-height:0!important;max-width:0!important;max-height:0!important;margin:0!important;padding:0!important;overflow:hidden!important;pointer-events:none!important;transform:none!important;animation:none!important}.zpProcessFlow__pin,.zpShopProcessFlow__pin{position:relative!important;height:auto!important;min-height:0!important;margin:clamp(28px,3.4vw,46px) 0 0!important;padding:0!important;overflow:visible!important;background:#fff!important;z-index:5!important}.zpProcessFlow__pinInner,.zpShopProcessFlow__pinInner{position:relative!important;top:auto!important;height:auto!important;min-height:0!important;display:block!important;justify-content:initial!important;padding:0!important;overflow:visible!important;background:#fff!important;transform:none!important}.zpProcessFlow__pinInner::before,.zpShopProcessFlow__pinInner::before{display:none!important;content:none!important}.zpProcessFlow__topBar,.zpShopProcessFlow__topBar{width:min(1320px,calc(100% - clamp(44px,5vw,84px)))!important;margin:0 auto clamp(16px,2vw,26px)!important;padding:0!important;display:grid!important;grid-template-columns:minmax(0,1fr) minmax(300px,460px)!important;gap:clamp(22px,3vw,42px)!important;align-items:end!important;opacity:1!important;transform:none!important;filter:none!important}.zpProcessFlow__topCopy strong,.zpShopProcessFlow__topCopy strong{font-size:clamp(22px,2.35vw,34px)!important;line-height:1.05!important}.zpProcessFlow__mobileHint,.zpShopProcessFlow__mobileHint{position:relative!important;z-index:7!important;width:min(1320px,calc(100% - clamp(44px,5vw,84px)))!important;margin:0 auto 14px!important;display:inline-flex!important;align-items:center!important;gap:9px!important;color:rgba(7,17,31,.64)!important;font-size:10px!important;line-height:1!important;letter-spacing:.11em!important;font-weight:850!important;text-transform:uppercase!important;pointer-events:none!important;opacity:1!important;transform:none!important;filter:none!important}.zpProcessFlow__mobileHint svg,.zpShopProcessFlow__mobileHint svg{width:15px!important;height:15px!important;color:#102a4f!important;stroke-width:2.15!important}.zpProcessFlow__viewport,.zpShopProcessFlow__viewport{position:relative!important;overflow:visible!important;width:100%!important;margin:0!important;padding:0 0 10px!important;background:transparent!important;opacity:1!important;transform:none!important;filter:none!important}.zpProcessFlow__viewport::after,.zpShopProcessFlow__viewport::after{content:""!important;display:block!important;position:absolute!important;z-index:8!important;top:0!important;right:0!important;bottom:0!important;width:min(72px,7vw)!important;pointer-events:none!important;background:linear-gradient(270deg,rgba(255,255,255,.92),rgba(255,255,255,0))!important}.zpProcessFlow__track,.zpShopProcessFlow__track{display:flex!important;align-items:stretch!important;gap:clamp(14px,1.6vw,22px)!important;width:auto!important;max-width:none!important;overflow-x:auto!important;overflow-y:hidden!important;padding:0 clamp(44px,5vw,84px) 22px!important;transform:none!important;will-change:auto!important;scroll-snap-type:x proximity!important;scroll-padding-left:clamp(44px,5vw,84px)!important;-webkit-overflow-scrolling:touch!important;overscroll-behavior-x:contain!important;scrollbar-width:none!important;touch-action:pan-x pan-y!important;cursor:grab!important}.zpProcessFlow__track:active,.zpShopProcessFlow__track:active{cursor:grabbing!important}.zpProcessFlow__track::-webkit-scrollbar,.zpShopProcessFlow__track::-webkit-scrollbar{display:none!important}.zpProcessFlow__card,.zpShopProcessFlow__card{flex:0 0 clamp(390px,38vw,520px)!important;width:clamp(390px,38vw,520px)!important;min-width:clamp(390px,38vw,520px)!important;min-height:clamp(500px,42vw,620px)!important;height:auto!important;border-radius:clamp(24px,2vw,34px)!important;padding:clamp(24px,2.25vw,34px)!important;transform:none!important;transition:border-color .26s ease,background .26s ease,box-shadow .26s ease!important;scroll-snap-align:start!important;scroll-snap-stop:normal!important;will-change:auto!important}.zpProcessFlow__card:hover,.zpProcessFlow__card.is-active,.zpShopProcessFlow__card:hover,.zpShopProcessFlow__card.is-active{--cardScale:1!important;--cardLift:0!important;transform:none!important;border-color:rgba(7,17,31,.18)!important}.zpProcessFlow__card h3,.zpShopProcessFlow__card h3{font-size:clamp(30px,3.25vw,42px)!important;line-height:1!important;letter-spacing:-.052em!important}.zpProcessFlow__card p,.zpShopProcessFlow__card p{font-size:clamp(13px,1.08vw,15px)!important;line-height:1.56!important}.zpProcessFlow__inner--bottom,.zpShopProcessFlow__inner--bottom{width:min(1320px,calc(100% - clamp(44px,5vw,84px)))!important;margin-top:clamp(20px,2.2vw,34px)!important;z-index:8!important}.zpProcessFlow__bottom,.zpShopProcessFlow__bottom{margin-top:0!important;padding-top:clamp(18px,2vw,28px)!important;opacity:1!important;transform:none!important;filter:none!important}.elementor-widget-container:has(.zpProcessFlow),.elementor-element:has(.zpProcessFlow),.e-con:has(.zpProcessFlow),.e-con-inner:has(.zpProcessFlow),.elementor-widget-container:has(.zpShopProcessFlow),.elementor-element:has(.zpShopProcessFlow),.e-con:has(.zpShopProcessFlow),.e-con-inner:has(.zpShopProcessFlow){overflow:visible!important}}@media (min-width:881px) and (max-width:1180px){.zpProcessFlow__topBar,.zpShopProcessFlow__topBar{display:block!important}.zpProcessFlow__progress,.zpShopProcessFlow__rocketProgress{margin-top:18px!important}.zpProcessFlow__card,.zpShopProcessFlow__card{flex-basis:min(58vw,480px)!important;width:min(58vw,480px)!important;min-width:min(58vw,480px)!important}}</style>
<script id="zp-suite-front-fixes-249-process-tablet-swipe-js-v432">
(function(){
  'use strict';
  if(window.__zpProcessTabletSwipeV432) return;
  window.__zpProcessTabletSwipeV432 = true;

  var MQ = '(min-width: 881px) and (max-width: 1500px)';
  var media = window.matchMedia ? window.matchMedia(MQ) : {matches:false, addEventListener:function(){}};
  var raf = 0;

  function clamp(n,min,max){ return Math.min(Math.max(n,min),max); }

  function roots(){
    return Array.prototype.slice.call(document.querySelectorAll('#zpProcessFlow, #zpShopProcessFlow'));
  }

  function getParts(root){
    var isShop = root.id === 'zpShopProcessFlow';
    return {
      root: root,
      isShop: isShop,
      track: root.querySelector(isShop ? '[data-zp-shop-process-track]' : '[data-zp-process-track]'),
      cards: Array.prototype.slice.call(root.querySelectorAll(isShop ? '[data-zp-shop-process-card]' : '[data-zp-process-card]')),
      label: root.querySelector('[data-zp-shop-progress-label]')
    };
  }

  function setProgress(parts, p){
    var safe = clamp(p,0,1);
    parts.root.style.setProperty('--processProgress', safe.toFixed(4));
    parts.root.style.setProperty('--trackX', '0px');
    if(parts.label){ parts.label.textContent = Math.round(safe * 100) + '%'; }
  }

  function updateRoot(root){
    var parts = getParts(root);
    if(!parts.track) return;

    if(!media.matches){
      root.removeAttribute('data-zp-tablet-swipe');
      return;
    }

    root.setAttribute('data-zp-tablet-swipe','1');
    root.classList.add('is-inview','is-mobile-swipe');
    root.classList.remove('is-desktop-pin');

    var max = Math.max(1, parts.track.scrollWidth - parts.track.clientWidth);
    var p = clamp(parts.track.scrollLeft / max,0,1);
    setProgress(parts,p);

    var left = parts.track.scrollLeft;
    var idx = 0;
    var best = Infinity;
    parts.cards.forEach(function(card,i){
      var d = Math.abs(card.offsetLeft - left - 12);
      if(d < best){ best = d; idx = i; }
    });

    parts.cards.forEach(function(card,i){
      card.classList.toggle('is-active',i === idx);
      card.style.setProperty('--cardLift','0');
    });
  }

  function updateAll(){
    raf = 0;
    roots().forEach(updateRoot);
  }

  function request(){
    if(raf) return;
    raf = window.requestAnimationFrame(updateAll);
  }

  function bindRoot(root){
    if(root.dataset.zpTabletSwipeBound === '1') return;
    root.dataset.zpTabletSwipeBound = '1';
    var parts = getParts(root);
    if(parts.track){
      parts.track.addEventListener('scroll', request, {passive:true});
      parts.track.addEventListener('touchstart', request, {passive:true});
      parts.track.addEventListener('pointerdown', request, {passive:true});
    }
  }

  function init(){
    roots().forEach(bindRoot);
    request();
  }

  if(document.readyState === 'loading'){
    document.addEventListener('DOMContentLoaded', init, {once:true});
  }else{
    init();
  }
  window.addEventListener('load', init, {once:true, passive:true});
  window.addEventListener('resize', request, {passive:true});
  window.addEventListener('orientationchange', function(){ setTimeout(request,160); }, {passive:true});
  if(media.addEventListener){ media.addEventListener('change', request); }
  else if(media.addListener){ media.addListener(request); }
})();
</script>
  <?php
}, 999);
