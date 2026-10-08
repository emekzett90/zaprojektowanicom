/* ZAPROJEKTOWANI SUITE — GLOBAL JS v0.1.2 SAFE */
(function(w,d){
  'use strict';

  var initTimer = null;
  var runCount = 0;
  var maxRuns = 6;
  var syntheticAudit = false;
  var serviceMobileFast = false;
  var homeIconManaged = false;
  try{syntheticAudit=w.zpSyntheticAudit===true||w.__zpSyntheticAudit===true||d.documentElement.classList.contains('zp-synthetic-audit')||false /* v711: identical production behavior in browser audits */||false;}catch(e){}
  try{serviceMobileFast=d.documentElement.classList.contains('zp-service-mobile-fast') || (/\/(strony-internetowe-katowice|sklepy-internetowe-katowice|logo-branding-katowice)\//.test(w.location&&w.location.pathname||'') && w.matchMedia && w.matchMedia('(max-width: 880px)').matches);}catch(e){}
  try{homeIconManaged=((w.location&&w.location.pathname||'/').replace(/\/+$/,'')||'/')==='/';}catch(e){}
  var defaultIconAttrs = {
    'stroke-width':1.8,
    'stroke-linecap':'round',
    'stroke-linejoin':'round'
  };

  if(syntheticAudit){
    d.documentElement.classList.add('zp-lucide-ready');
    w.zpSuiteInitLucide = function(){};
    return;
  }

  function installLucideGuard(){
    if(!w.lucide || typeof w.lucide.createIcons !== 'function' || w.lucide.__zpGuarded){
      return !!(w.lucide && w.lucide.__zpGuarded);
    }

    var original = w.lucide.createIcons.bind(w.lucide);

    function pendingIcons(){
      return Array.prototype.filter.call(d.querySelectorAll('[data-lucide]'), function(el){
        // Lucide retains data-lucide on SVGs. Its default document scan would
        // replace every finished SVG again whenever just one new icon appears.
        // A changed icon name remains eligible (Studio Wyceny updates icons).
        var name=el.getAttribute('data-lucide') || '';
        return String(el.tagName).toLowerCase()!=='svg' || !el.classList.contains('lucide') || /\s/.test(name) || !el.classList.contains('lucide-'+name);
      });
    }

    w.lucide.createIcons = function(options){
      options = options || {};

      /* Legacy home blocks still call createIcons() globally.  Once the scoped
         manager is active, only calls explicitly opened by it may scan the DOM. */
      if(homeIconManaged && typeof w.zpSuiteHydrateIcons==='function' && w.__zpAllowLucideScan!==true){
        d.documentElement.classList.add('zp-lucide-ready');
        return;
      }

      var scanOptions=options;
      // Explicit custom roots, icon sets, attributes and templates retain the API.
      // Suite scans only supply these three stroke defaults; existing SVG attributes
      // already take precedence over them in Lucide's own replacement routine.
      var standardAttrs=!options.attrs || Object.keys(options.attrs).every(function(key){
        return key==='stroke-width' || key==='stroke-linecap' || key==='stroke-linejoin';
      });
      if(!options.root && !options.nameAttr && !options.inTemplates && !options.icons && standardAttrs){
        var pending=pendingIcons();
        if(!pending.length && !d.querySelector('[icon-name]')){
          d.documentElement.classList.add('zp-lucide-ready');
          return;
        }
        scanOptions=Object.assign({},options,{root:{querySelectorAll:function(selector){
          return selector==='[data-lucide]' ? pending : d.querySelectorAll(selector);
        }}});
      }

      var result = original(scanOptions);
      d.documentElement.classList.add('zp-lucide-ready');
      return result;
    };

    w.lucide.__zpGuarded = true;
    return true;
  }

  function initLucide(){
    if(!w.lucide || typeof w.lucide.createIcons !== 'function'){
      return false;
    }

    installLucideGuard();

    if(runCount >= maxRuns){
      return true;
    }

    runCount++;

    try{
      w.lucide.createIcons({
        attrs: defaultIconAttrs
      });
      d.documentElement.classList.add('zp-lucide-ready');
      return true;
    }catch(e){
      return false;
    }
  }

  function scheduleLucide(delay){
    if(initTimer){
      w.clearTimeout(initTimer);
    }
    initTimer = w.setTimeout(initLucide, delay || 0);
  }

  installLucideGuard();

  /*
   * The home header owns icon hydration.  It keeps icons below the fold dormant
   * until their section approaches the viewport, so legacy block listeners may
   * safely call createIcons() without re-scanning hundreds of already-rendered SVGs.
   */
  if(homeIconManaged){
    w.zpSuiteInitLucide = function(scope){
      installLucideGuard();
      if(typeof w.zpSuiteHydrateIcons==='function'){
        w.zpSuiteHydrateIcons(scope || d);
      }
    };
    return;
  }

  if(serviceMobileFast){
    // v2.2.535: service mobile pages render faster when hundreds of Lucide icons are
    // hydrated after the first paint/load instead of during the first tap/DOMContentLoaded.
    // Header/chevrons have CSS/SVG fallbacks, so this does not change layout.
    var serviceIconBooted=false;
    function bootServiceIcons(delay){
      if(serviceIconBooted) return;
      serviceIconBooted=true;
      scheduleLucide(delay || 0);
      w.setTimeout(initLucide, (delay || 0) + 700);
    }
    w.addEventListener('load', function(){
      if('requestIdleCallback' in w){
        w.requestIdleCallback(function(){ bootServiceIcons(120); }, {timeout:1800});
      }else{
        w.setTimeout(function(){ bootServiceIcons(0); }, 900);
      }
    }, {once:true, passive:true});
    ['scroll','keydown'].forEach(function(ev){
      w.addEventListener(ev, function(){ bootServiceIcons(80); }, {once:true, passive:true});
    });
    w.setTimeout(function(){ bootServiceIcons(0); }, 3600);
  }else if(d.readyState === 'loading'){
    d.addEventListener('DOMContentLoaded', function(){
      scheduleLucide(0);
      w.setTimeout(initLucide, 350);
    }, {once:true});
  }else{
    scheduleLucide(0);
    w.setTimeout(initLucide, 350);
  }

  w.addEventListener('load', function(){
    if(serviceMobileFast) return;
    scheduleLucide(80);
    w.setTimeout(initLucide, 700);
  }, {passive:true});

  /* Ręcznie z konsoli: window.zpSuiteInitLucide() */
  w.zpSuiteInitLucide = function(){
    runCount = 0;
    scheduleLucide(0);
  };
})(window,document);


/* ZP Suite v2.2.343 — deferred decorative hero videos */
(function(w,d){
  'use strict';

  function isMobileViewport(){
    try{
      return !!(w.matchMedia && w.matchMedia('(max-width: 780px)').matches);
    }catch(e){
      return false;
    }
  }

  function isAuditBot(){
    try{
      return false /* v711: identical production behavior in browser audits */;
    }catch(e){
      return false;
    }
  }

  function hydrate(video){
    if(!video || video.dataset.zpDeferredVideoLoaded === '1') return;
    if(isMobileViewport() || isAuditBot()) return;

    var videoSrc = video.getAttribute('data-src') || '';
    if(videoSrc && !video.getAttribute('src')){
      video.setAttribute('src', videoSrc);
    }

    Array.prototype.slice.call(video.querySelectorAll('source[data-src]')).forEach(function(source){
      if(!source.getAttribute('src')){
        source.setAttribute('src', source.getAttribute('data-src'));
      }
    });

    video.dataset.zpDeferredVideoLoaded = '1';
    video.muted = true;
    video.defaultMuted = true;
    video.playsInline = true;
    video.setAttribute('muted','');
    video.setAttribute('playsinline','');
    video.setAttribute('webkit-playsinline','');

    try{ video.load(); }catch(e){}
    var play = function(){
      try{
        var p = video.play();
        if(p && p.catch) p.catch(function(){});
      }catch(e){}
    };
    video.addEventListener('canplay', play, {once:true});
    video.addEventListener('loadeddata', play, {once:true});
    w.setTimeout(play, 140);
  }

  function init(){
    if(isMobileViewport() || isAuditBot()) return;
    var videos = Array.prototype.slice.call(d.querySelectorAll('video[data-zp-defer-video], video source[data-src]'));
    videos = videos.map(function(node){ return node.tagName && node.tagName.toLowerCase() === 'source' ? node.parentNode : node; })
      .filter(function(video, index, list){ return video && list.indexOf(video) === index; });
    if(!videos.length) return;
    videos.forEach(hydrate);
  }

  function schedule(){
    if('requestIdleCallback' in w){
      w.requestIdleCallback(function(){ w.setTimeout(init, 450); }, {timeout:2200});
    }else{
      w.setTimeout(init, 1100);
    }
  }

  if(d.readyState === 'complete') schedule();
  else w.addEventListener('load', schedule, {once:true, passive:true});
})(window,document);



/* ZP Suite v2.2.32 — soft mobile portfolio swipe: short drag = next/prev card */
(function(w,d){
  'use strict';

  var selectors = [
    '#zpPortfolioReveal .zpSS__stack',
    '#portfolio-sklepy .zpShopPortfolio__stack'
  ].join(',');

  function visibleCards(scroller){
    var list = Array.prototype.slice.call(scroller.children || []);
    return list.filter(function(el){
      if(!el || el.nodeType !== 1) return false;
      var cs = w.getComputedStyle(el);
      return cs.display !== 'none' && cs.visibility !== 'hidden' && el.offsetWidth > 20;
    });
  }

  function sidePad(scroller){
    var cs = w.getComputedStyle(scroller);
    var v = parseFloat(cs.paddingLeft || '0');
    return isNaN(v) ? 0 : v;
  }

  function nearest(scroller, cards){
    var pad = sidePad(scroller);
    var left = scroller.scrollLeft;
    var best = 0, bestDist = Infinity;
    cards.forEach(function(card, i){
      var d = Math.abs(Math.max(0, card.offsetLeft - pad) - left);
      if(d < bestDist){ bestDist = d; best = i; }
    });
    return best;
  }

  function go(scroller, cards, index, smooth){
    if(!cards.length) return;
    index = Math.max(0, Math.min(cards.length - 1, index));
    var target = Math.max(0, cards[index].offsetLeft - sidePad(scroller));
    try{
      scroller.scrollTo({left: target, behavior: smooth ? 'smooth' : 'auto'});
    }catch(e){
      scroller.scrollLeft = target;
    }
  }

  function bind(scroller){
    if(!scroller || scroller.dataset.zpSwipeV232 === '1') return;
    scroller.dataset.zpSwipeV232 = '1';

    var sx = 0, sy = 0, startLeft = 0, startIndex = 0, isTouch = false;

    scroller.addEventListener('touchstart', function(e){
      if(!e.touches || e.touches.length !== 1 || w.innerWidth > 880) return;
      var cards = visibleCards(scroller);
      sx = e.touches[0].clientX;
      sy = e.touches[0].clientY;
      startLeft = scroller.scrollLeft;
      startIndex = nearest(scroller, cards);
      isTouch = true;
      scroller.classList.add('is-zp-swiping-soft');
    }, {passive:true});

    scroller.addEventListener('touchend', function(e){
      if(!isTouch || w.innerWidth > 880) return;
      isTouch = false;
      scroller.classList.remove('is-zp-swiping-soft');

      var t = e.changedTouches && e.changedTouches[0];
      if(!t) return;
      var dx = t.clientX - sx;
      var dy = t.clientY - sy;
      var cards = visibleCards(scroller);
      if(!cards.length) return;

      var current = nearest(scroller, cards);
      var minSwipe = Math.max(12, Math.min(26, scroller.clientWidth * 0.045));
      var mostlyHorizontal = Math.abs(dx) > Math.abs(dy) * 0.58;

      if(Math.abs(dx) >= minSwipe && mostlyHorizontal){
        go(scroller, cards, startIndex + (dx < 0 ? 1 : -1), true);
      }else if(Math.abs(scroller.scrollLeft - startLeft) > 18){
        go(scroller, cards, current, true);
      }
    }, {passive:true});

    scroller.addEventListener('wheel', function(e){
      if(w.innerWidth > 880 || Math.abs(e.deltaX) < Math.abs(e.deltaY)) return;
      var cards = visibleCards(scroller);
      if(!cards.length) return;
      var idx = nearest(scroller, cards) + (e.deltaX > 0 ? 1 : -1);
      go(scroller, cards, idx, true);
    }, {passive:true});
  }

  function init(){
    d.querySelectorAll(selectors).forEach(bind);
  }

  if(d.readyState === 'loading') d.addEventListener('DOMContentLoaded', init, {once:true});
  else init();
  w.addEventListener('load', init, {passive:true});
  w.addEventListener('resize', function(){ w.clearTimeout(w.zpSwipeV232Timer); w.zpSwipeV232Timer = w.setTimeout(init, 140); }, {passive:true});
})(window,document);
