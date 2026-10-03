/* ZAPROJEKTOWANI — Motion Lite v2.2.347
   Lightweight global microinteractions: viewport reveal, CTA magnet,
   subtle mockup parallax, beams and active TOC. No visual layout changes. */
(function(w,d){
  'use strict';

  var root = d.documentElement;
  var reduce = false;
  var isAudit = false;
  var homePage = false;
  try{ reduce = !!(w.matchMedia && w.matchMedia('(prefers-reduced-motion: reduce)').matches); }catch(e){}
  try{ isAudit = !!w.zpSyntheticAudit || !!w.__zpSyntheticAudit || root.classList.contains('zp-synthetic-audit') || false /* v711: identical production behavior in browser audits */ || false; }catch(e){}
  try{ homePage = ((w.location&&w.location.pathname||'/').replace(/\/+$/,'')||'/')==='/'; }catch(e){}
  if(reduce){ root.classList.add('zp-reduced-motion'); return; }
  if(isAudit){ root.classList.add('zp-synthetic-audit'); return; }
  if(root.classList.contains('zp-motion-lite-ready')) return;
  root.classList.add('zp-motion-lite-ready');

  var SELECTORS = {
    headings: [
      '.elementor-shortcode main h1', '.elementor-shortcode main h2', '.elementor-shortcode main h3',
      'main [class^="zp"] h1', 'main [class^="zp"] h2', 'main [class^="zp"] h3',
      'main [class*=" zp"] h1', 'main [class*=" zp"] h2', 'main [class*=" zp"] h3'
    ].join(','),
    cards: [
      'main article[class*="Card"]','main div[class*="Card"]','main article[class*="card"]','main div[class*="card"]',
      'main article[class*="Tile"]','main div[class*="Tile"]','main article[class*="Karta"]','main div[class*="Karta"]'
    ].join(','),
    ctas: [
      'main a[class*="btn"]','main button[class*="btn"]','main a[class*="Btn"]','main button[class*="Btn"]',
      'main a[class*="cta"]','main button[class*="cta"]','main a[class*="CTA"]','main button[class*="CTA"]',
      'main .elementor-button'
    ].join(','),
    chips: [
      'main [class*="chip"]','main [class*="Chip"]','main [class*="badge"]','main [class*="Badge"]','main [class*="pill"]','main [class*="Pill"]'
    ].join(','),
    icons: [
      'main [class*="icon"]','main [class*="Icon"]','main [data-lucide]'
    ].join(','),
    beams: [
      'main [class*="beam"]','main [class*="Beam"]','main [class*="underline"]','main [class*="Underline"]','main [class*="line"]','main [class*="Line"]'
    ].join(','),
    parallax: [
      'main [class*="mock"]','main [class*="Mock"]','main [class*="phone"]','main [class*="Phone"]','main [class*="laptop"]','main [class*="Laptop"]','main [class*="visual"]','main [class*="Visual"]'
    ].join(','),
    spy: [
      'main [class*="toc"]','main [class*="Toc"]','main [class*="sidebar"]','main [class*="Sidebar"]'
    ].join(',')
  };

  function qsa(sel, scope){
    try{return Array.prototype.slice.call((scope||d).querySelectorAll(sel));}catch(e){return [];}
  }
  function closest(el, sel){
    try{return el && el.closest(sel);}catch(e){return null;}
  }
  function isBadMotionTarget(el){
    if(!el || el.dataset.zpMotionSkip === '1') return true;
    if(closest(el, '#wpadminbar,.zpNewNav,.zpNewNav__mobilePanel,.zpNewNav__mega,.zpNewNav__drop,.zpSearchOverlay,.elementor-editor-active')) return true;
    /* v2.2.347: large home/portfolio sections have their own animation engines.
       Global motion must not stack additional observers/transforms on them. */
    if(closest(el, '#zhHero,#zpShowcaseWhite,#zpShowcaseServices,.zpRobotHero,[data-zp-robot-hero],[data-zp-trust-pinned],#zpServicesPath')) return true;
    /* v2.2.346: service hero mockups have their own fixed sizing/transforms. Do not decorate them globally. */
    if(closest(el, '#zpWebHeroKat .zpWebHeroKat__visual,#zpShopHeroKat .zpShopCockpit__visual,#zpBrandHeroKatSafe .zpBrandHeroSafe__visual,#zpContactHeroKat .zpWebHeroKat__visual')) return true;
    return false;
  }
  function setOnce(el, name, value){
    if(!el || el.hasAttribute(name)) return;
    el.setAttribute(name, value || '');
  }
  function markReveal(el, type, delay){
    if(isBadMotionTarget(el)) return;
    if(el.hasAttribute('data-zp-motion')) return;
    // Never hide text already on screen when an idle callback runs late.
    var rect = el.getBoundingClientRect();
    if(rect.top < (w.innerHeight || 800) && rect.bottom > 0) el.classList.add('is-zp-visible');
    setOnce(el, 'data-zp-motion', type || 'item');
    if(delay) setOnce(el, 'data-zp-motion-delay', String(delay));
  }
  function markDepth(el){
    if(isBadMotionTarget(el)) return;
    if(el.matches && el.matches('svg,img,video,canvas,[data-lucide]')) return;
    setOnce(el, 'data-zp-depth', '');
  }
  function markMagnet(el){
    if(isBadMotionTarget(el)) return;
    if(el.matches && !el.matches('a,button,[role="button"],input[type="button"],input[type="submit"]')) return;
    setOnce(el, 'data-zp-magnet', '');
  }
  function markBeam(el){
    if(isBadMotionTarget(el)) return;
    setOnce(el, 'data-zp-motion-beam', '');
  }
  function isMobileViewport(){
    try{return !!(w.matchMedia && w.matchMedia('(max-width: 880px)').matches);}catch(e){return false;}
  }

  function isParallaxDisabledPage(){
    /* Parallax stays available on desktop. On mobile it is disabled to prevent
       service-hero mockups from jumping during scroll. */
    return isMobileViewport();
  }

  function markParallax(el){
    if(isParallaxDisabledPage()) return;
    if(isBadMotionTarget(el)) return;
    setOnce(el, 'data-zp-parallax', '');
  }

  function decorate(scope){
    scope = scope || d;
    qsa(SELECTORS.headings, scope).forEach(function(el){ markReveal(el, 'heading'); });
    qsa(SELECTORS.cards, scope).forEach(function(el, i){ markReveal(el, 'card', (i % 4) + 1); markDepth(el); });
    qsa(SELECTORS.ctas, scope).forEach(function(el){ markMagnet(el); });
    qsa(SELECTORS.chips, scope).forEach(function(el, i){ markReveal(el, 'chip', (i % 5) + 1); });
    qsa(SELECTORS.icons, scope).forEach(function(el, i){ if(!closest(el,'svg')) markReveal(el, 'icon', (i % 4) + 1); });
    qsa(SELECTORS.beams, scope).forEach(markBeam);
    qsa(SELECTORS.parallax, scope).forEach(markParallax);
    qsa(SELECTORS.spy, scope).forEach(function(el){ setOnce(el, 'data-zp-motion-spy', ''); });
  }

  var revealObserver;
  var revealSeen = new WeakSet();
  function initReveal(scope){
    var items = qsa('[data-zp-motion],[data-zp-motion-beam]',scope||d);
    if(!items.length) return;
    root.classList.add('zp-motion-ready');
    if(!('IntersectionObserver' in w)){
      items.forEach(function(el){ el.classList.add('is-zp-visible'); });
      return;
    }
    if(!revealObserver) revealObserver = new IntersectionObserver(function(entries){
      entries.forEach(function(entry){
        if(entry.isIntersecting || entry.intersectionRatio > 0){
          entry.target.classList.add('is-zp-visible');
          revealObserver.unobserve(entry.target);
        }
      });
    }, {root:null, rootMargin:'0px 0px -2% 0px', threshold:0.03});
    /* v2.2.666: reveal fires niemal na krawędzi wejścia (było -8% / .08) — na słabym
       sprzęcie elementy stały puste zauważalnie długo po wejściu w viewport. */
    items.forEach(function(el){
      if(revealSeen.has(el) || el.classList.contains('is-zp-visible')) return;
      revealSeen.add(el);
      revealObserver.observe(el);
    });
  }

  function initMagnet(scope){
    if(!w.matchMedia || !w.matchMedia('(hover:hover) and (pointer:fine)').matches) return;
    qsa('[data-zp-magnet]', scope).forEach(function(el){
      if(el.__zpMagnet) return;
      el.__zpMagnet = true;
      var raf = 0, tx = 0, ty = 0;
      function apply(){ raf = 0; el.style.setProperty('--zp-mag-x', tx.toFixed(2)+'px'); el.style.setProperty('--zp-mag-y', ty.toFixed(2)+'px'); }
      el.addEventListener('pointermove', function(e){
        if(e.pointerType === 'touch') return;
        var r = el.getBoundingClientRect();
        var x = (e.clientX - r.left) / Math.max(1,r.width) - .5;
        var y = (e.clientY - r.top) / Math.max(1,r.height) - .5;
        tx = Math.max(-5, Math.min(5, x * 8));
        ty = Math.max(-4, Math.min(4, y * 6));
        if(!raf) raf = w.requestAnimationFrame(apply);
      }, {passive:true});
      el.addEventListener('pointerleave', function(){
        tx = 0; ty = 0;
        if(!raf) raf = w.requestAnimationFrame(apply);
      }, {passive:true});
    });
  }

  var parallaxObserver;
  var parallaxSeen = new WeakSet();
  function initParallax(scope){
    if(isParallaxDisabledPage()){
      qsa('[data-zp-parallax]', scope).forEach(function(el){
        el.removeAttribute('data-zp-parallax');
        el.style.removeProperty('--zp-parallax-y');
      });
      return;
    }
    var nodes = qsa('[data-zp-parallax]', scope).filter(function(el){return !parallaxSeen.has(el);});
    if(!nodes.length || !('IntersectionObserver' in w)) return;
    if(parallaxObserver){
      nodes.forEach(function(el){parallaxSeen.add(el);parallaxObserver.observe(el);});
      return;
    }
    var visible = [];
    var ticking = false;
    var io = new IntersectionObserver(function(entries){
      entries.forEach(function(entry){
        var el = entry.target;
        var idx = visible.indexOf(el);
        if(entry.isIntersecting && idx === -1) visible.push(el);
        else if(!entry.isIntersecting && idx !== -1){ visible.splice(idx,1); el.style.removeProperty('--zp-parallax-y'); }
      });
      schedule();
    }, {rootMargin:'18% 0px 18% 0px', threshold:0});

    function update(){
      ticking = false;
      if(!visible.length || d.hidden) return;
      if(isMobileViewport()){
        visible.forEach(function(el){el.style.removeProperty('--zp-parallax-y');});
        return;
      }
      var vh = w.innerHeight || d.documentElement.clientHeight || 800;
      var offsets = visible.map(function(el){
        var r = el.getBoundingClientRect();
        var mid = r.top + r.height / 2;
        var progress = (mid - vh / 2) / vh;
        var y = Math.max(-8, Math.min(8, progress * -10));
        return y.toFixed(2)+'px';
      });
      visible.forEach(function(el,i){el.style.setProperty('--zp-parallax-y', offsets[i]);});
    }
    function schedule(){ if(!ticking && visible.length && !d.hidden){ ticking = true; w.requestAnimationFrame(update); } }
    parallaxObserver = io;
    nodes.forEach(function(el){ parallaxSeen.add(el); io.observe(el); });
    w.addEventListener('scroll', schedule, {passive:true});
    w.addEventListener('resize', schedule, {passive:true});
  }

  var spySeen = new WeakSet();
  function initSpy(scope){
    var spies = qsa('[data-zp-motion-spy]', scope).filter(function(el){
      if(spySeen.has(el)) return false;
      spySeen.add(el); return true;
    });
    if(!spies.length || !('IntersectionObserver' in w)) return;
    var links = [];
    spies.forEach(function(spy){
      qsa('a[href^="#"],button[data-target]', spy).forEach(function(a){
        var id = (a.getAttribute('href') || a.getAttribute('data-target') || '').replace(/^#/, '');
        if(!id) return;
        var sec = d.getElementById(id);
        if(sec) links.push({link:a, sec:sec});
      });
    });
    if(!links.length) return;
    var io = new IntersectionObserver(function(entries){
      entries.forEach(function(entry){
        if(!entry.isIntersecting) return;
        links.forEach(function(pair){ pair.link.classList.toggle('is-zp-active', pair.sec === entry.target); });
      });
    }, {rootMargin:'-35% 0px -55% 0px', threshold:0});
    links.forEach(function(pair){ io.observe(pair.sec); });
  }

  // One shared scheduler on home and service pages. Nested sections are handled
  // by their outer section, never decorated twice. Explicit refresh is idempotent.
  var sectionSeen = new WeakSet();
  var sectionObserver;
  function runSection(section){
    var run = function(){
      if(Date.now() < (w.__zpNavUiBusyUntil || 0)){w.setTimeout(run,180);return;}
      decorate(section);
      initReveal(section);
      initMagnet(section);
      if(!homePage){initParallax(section);initSpy(section);}
    };
    if('requestIdleCallback' in w) w.requestIdleCallback(run,{timeout:900});
    else w.setTimeout(run,80);
  }
  function refresh(force){
    var sections=qsa('main section,main [data-zp-home-contact-form],main footer');
    sections=sections.filter(function(section){
      return !closest(section.parentElement,'main section,main [data-zp-home-contact-form],main footer');
    });
    if(!sections.length) sections=qsa('main');
    if(!sections.length) return;
    if('IntersectionObserver' in w && !sectionObserver){
      sectionObserver=new IntersectionObserver(function(entries){
        entries.forEach(function(entry){
          if(!entry.isIntersecting) return;
          sectionObserver.unobserve(entry.target);
          runSection(entry.target);
        });
      },{rootMargin:'640px 0px',threshold:0});
    }
    sections.forEach(function(section){
      if(sectionSeen.has(section)){
        if(force === true) runSection(section);
        return;
      }
      sectionSeen.add(section);
      if(sectionObserver) sectionObserver.observe(section);
      else runSection(section);
    });
  }
  if(d.readyState === 'loading') d.addEventListener('DOMContentLoaded',refresh,{once:true});
  else refresh();
  // Elementor may ask to decorate an inserted subtree without a document-wide scan.
  w.zpMotionLiteRefresh = function(scope){
    if(scope && scope.nodeType === 1) runSection(scope);
    else refresh(true);
  };
})(window,document);
