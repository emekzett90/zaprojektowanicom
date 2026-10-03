(function(w,d){
  'use strict';
  function initZpNewHero(){
    var hero=d.querySelector('[data-zp-new-hero]');
    if(!hero||hero.dataset.initialized==='1')return;
    hero.dataset.initialized='1';
    var video=hero.querySelector('.zpNewHero__video');
    if(video){
      video.muted=true;video.playsInline=true;
      if(w.zpSuiteFrontPolish){ video.dataset.zpFrontPolishControls='1'; }
      if(w.zpSuiteFrontPolish){
        // v1.8.0 Front Polish controls hero video loading strategy.
        video.addEventListener('error',function(){video.removeAttribute('src');},{once:true});
      } else {
      var src=video.getAttribute('data-src')||video.getAttribute('src')||'';
      var isAuditBot=false;
      try{isAuditBot=false /* v711: identical production behavior in browser audits */;}catch(e){}
      if(isAuditBot){video.removeAttribute('src');video.removeAttribute('data-src');return;}
      var play=function(){try{var p=video.play();if(p&&p.catch){p.catch(function(){});}}catch(e){}};
      var loadVideo=function(){
        if(!src||video.dataset.zpVideoLoaded==='1') return;
        video.dataset.zpVideoLoaded='1';
        video.setAttribute('src',src);
        try{video.load();}catch(e){}
        video.addEventListener('canplay',play,{once:true});
        video.addEventListener('loadeddata',play,{once:true});
        video.addEventListener('error',function(){video.removeAttribute('src');},{once:true});
        setTimeout(play,180);
      };
      d.addEventListener('visibilitychange',function(){if(!d.hidden){loadVideo();play();}});
      if('requestIdleCallback' in w){w.requestIdleCallback(loadVideo,{timeout:2600});}
      else{w.setTimeout(loadVideo,1400);}
      }
    }
    if(w.lucide&&typeof w.lucide.createIcons==='function'){
      try{w.lucide.createIcons({attrs:{'stroke-width':1.9,'stroke-linecap':'round','stroke-linejoin':'round'}});}catch(e){}
    }
    hero.setAttribute('data-ready','1');
  }
  if(d.readyState==='loading'){d.addEventListener('DOMContentLoaded',initZpNewHero);}else{initZpNewHero();}
  w.addEventListener('load',initZpNewHero,{passive:true});
})(window,document);
