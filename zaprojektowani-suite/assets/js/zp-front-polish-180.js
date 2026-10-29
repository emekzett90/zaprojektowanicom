(function(w,d){
  'use strict';
  var cfg=w.zpSuiteFrontPolish||{};
  var doc=d.documentElement;
  function isIOS(){return /iPad|iPhone|iPod/.test(navigator.userAgent||'') || (navigator.platform==='MacIntel'&&navigator.maxTouchPoints>1);} 
  function isSafari(){return /^((?!chrome|android).)*safari/i.test(navigator.userAgent||'');}
  if(isIOS()) doc.classList.add('zp-ios');
  if(isSafari()) doc.classList.add('zp-safari');

  function markReady(){
    if(cfg.loader===false) return;
    doc.classList.remove('zp-loading');
    doc.classList.add('zp-ready','zp-front-ready');
  }

  function initSmoothAnchors(){
    if(!cfg.smoothScroll) return;
    d.addEventListener('click',function(e){
      if(e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
      var a=e.target.closest&&e.target.closest('a[href^="#"]');
      if(!a || a.hasAttribute('download') || (a.target && a.target !== '_self')) return;
      var href=a.getAttribute('href');
      if(!href||href==='#') return;
      var target;
      try{target=d.getElementById(decodeURIComponent(href.slice(1)));}catch(err){return;}
      if(!target) return;
      e.preventDefault();
      var offset=parseInt(cfg.headerOffset||92,10);
      var y=target.getBoundingClientRect().top+w.pageYOffset-offset;
      var reduced=w.matchMedia && w.matchMedia('(prefers-reduced-motion: reduce)').matches;
      w.scrollTo({top:Math.max(0,y),behavior:reduced?'instant':'smooth'});
      try{history.pushState(null,'',href);}catch(err){}
    },{passive:false});
  }

  function initReveal(){
    if(!cfg.reveal) return;
    var nodes=[].slice.call(d.querySelectorAll('[data-zp-reveal], .zpHomeContent > section:not(.zpNewHero):not(.zpRobotHero):not(.zpNewNav)'));
    if(!nodes.length) return;
    if(!('IntersectionObserver' in w)) {nodes.forEach(function(n){n.classList.add('zp-reveal-in');});return;}
    var io=new IntersectionObserver(function(entries){
      entries.forEach(function(en){if(en.isIntersecting){en.target.classList.add('zp-reveal-in');io.unobserve(en.target);}});
    },{threshold:.08,rootMargin:'0px 0px -7% 0px'});
    nodes.forEach(function(n){ if(!n.classList.contains('zpNewHero')&&!n.classList.contains('zpRobotHero')) io.observe(n); });
  }

  function initVideoStrategy(){
    var videos=[].slice.call(d.querySelectorAll('.zpNewHero__video[data-src]'));
    if(!videos.length) return;
    var strat=cfg.videoStrategy||'idle';
    var delay=parseInt(cfg.videoDelay||900,10);
    var isAudit=false;
    try{isAudit=false /* v711: identical production behavior in browser audits */;}catch(e){}
    if(isAudit||strat==='off') {videos.forEach(function(v){v.removeAttribute('src');});return;}
    function load(v){
      if(!v||v.dataset.zpFrontVideoLoaded==='1') return;
      var src=v.getAttribute('data-src'); if(!src) return;
      v.dataset.zpFrontVideoLoaded='1';
      v.muted=true; v.playsInline=true;
      v.setAttribute('src',src);
      try{v.load();}catch(e){}
      var play=function(){try{var p=v.play(); if(p&&p.catch)p.catch(function(){});}catch(e){}};
      v.addEventListener('canplay',play,{once:true});
      v.addEventListener('loadeddata',play,{once:true});
      setTimeout(play,120);
    }
    function loadAll(){videos.forEach(load);}
    if(strat==='interaction'){
      var once=function(){loadAll();w.removeEventListener('pointerdown',once);w.removeEventListener('keydown',once);};
      w.addEventListener('pointerdown',once,{once:true,passive:true});w.addEventListener('keydown',once,{once:true});
    }else if(strat==='visible' && 'IntersectionObserver' in w){
      var io=new IntersectionObserver(function(entries){entries.forEach(function(en){if(en.isIntersecting){load(en.target);io.unobserve(en.target);}});},{threshold:.2});
      videos.forEach(function(v){io.observe(v);});
    }else{
      if('requestIdleCallback' in w) w.requestIdleCallback(function(){setTimeout(loadAll,delay);},{timeout:2800});
      else setTimeout(loadAll,delay+400);
    }
  }

  function smartImages(){
    var imgs=[].slice.call(d.images||[]);
    imgs.forEach(function(img,i){
      // Empty alt is intentional for decorative assets. Descriptions belong in the CMS,
      // not in a filename-based keyword generator.
      if(!img.getAttribute('decoding')) img.setAttribute('decoding','async');
      var above=img.classList.contains('zpNewHero__person')||img.classList.contains('zpNewNav__logo')||i<2;
      if(!img.getAttribute('loading')) img.setAttribute('loading',above?'eager':'lazy');
      if(above&&!img.getAttribute('fetchpriority')) img.setAttribute('fetchpriority','high');
    });
  }

  function frontHealthPing(){
    try{
      var resources=performance.getEntriesByType?performance.getEntriesByType('resource'):[];
      var total=0; resources.forEach(function(r){total+=r.transferSize||0;});
      doc.dataset.zpFrontWeight=Math.round(total/1024);
      doc.dataset.zpFrontHealth='1';
    }catch(e){}
  }

  function init(){
    initSmoothAnchors();
    initReveal();
    initVideoStrategy();
    smartImages();
    setTimeout(markReady,90);
    setTimeout(frontHealthPing,1400);
  }
  if(d.readyState==='loading') d.addEventListener('DOMContentLoaded',init); else init();
  w.addEventListener('load',function(){markReady();smartImages();frontHealthPing();},{passive:true});
  setTimeout(markReady,1800);
})(window,document);
