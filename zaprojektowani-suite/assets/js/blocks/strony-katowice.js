/* ZP Strony Katowice — extracted optimized JS. Lucide is enqueued once by the plugin. */
(function(){
  if(window.__zpStronyKatowiceInitOnce){return;}
  window.__zpStronyKatowiceInitOnce=1;
})();



/* ===== 1_hero.html ===== */
(function(){
      'use strict';

      function init(){
        var root = document.getElementById('zpWebHeroKat');

        if(!root || root.dataset.initialized === '1'){
          return;
        }

        root.dataset.initialized = '1';

        if(window.lucide && typeof window.lucide.createIcons === 'function'){
          try{
            window.lucide.createIcons({
              attrs:{
                'stroke-width':2,
                'stroke-linecap':'round',
                'stroke-linejoin':'round',
                'aria-hidden':'true'
              }
            });
          }catch(e){}
        }

        root.querySelectorAll('a[href^="#"]').forEach(function(link){
          link.addEventListener('click', function(e){
            var id = link.getAttribute('href');

            if(!id || id === '#'){
              return;
            }

            var target = document.querySelector(id);

            if(!target){
              return;
            }

            e.preventDefault();

            window.scrollTo({
              top: target.getBoundingClientRect().top + window.pageYOffset - 92,
              behavior: 'smooth'
            });
          });
        });

        var video = root.querySelector('.zpWebHeroKat__video');
        if(video){
          try{
            video.muted = true;
            video.defaultMuted = true;
            video.playsInline = true;
            video.autoplay = true;
            video.loop = true;
            video.setAttribute('muted','');
            video.setAttribute('playsinline','');
            video.setAttribute('webkit-playsinline','');
            video.setAttribute('autoplay','');
            video.setAttribute('loop','');
            video.setAttribute('preload','none');

            var src = video.getAttribute('data-src') || video.getAttribute('src') || '';
            Array.prototype.slice.call(video.querySelectorAll('source')).forEach(function(source){
              var s = source.getAttribute('data-src') || source.getAttribute('src') || '';
              if(!src && s) src = s;
            });

            var play = function(){
              try{
                video.muted = true;
                video.defaultMuted = true;
                var p = video.play();
                if(p && p.catch) p.catch(function(){});
              }catch(e){}
            };

            var loadVideo = function(){
              try{
                if(src && video.getAttribute('src') !== src){
                  video.setAttribute('src', src);
                }
                Array.prototype.slice.call(video.querySelectorAll('source[data-src]')).forEach(function(source){
                  if(!source.getAttribute('src')) source.setAttribute('src', source.getAttribute('data-src'));
                });
                if(video.dataset.zpKatVideoLoaded !== '1'){
                  video.dataset.zpKatVideoLoaded = '1';
                  try{ video.load(); }catch(e){}
                }
                play();
              }catch(e){}
            };

            var isAuditBot = false;
            try{ isAuditBot = false /* v711: identical production behavior in browser audits */; }catch(e){}
            var isMobile = false;
            try{ isMobile = window.matchMedia && window.matchMedia('(max-width: 780px)').matches; }catch(e){}

            video.addEventListener('canplay', play, {once:true});
            video.addEventListener('loadeddata', play, {once:true});

            if(isAuditBot){
              video.removeAttribute('src');
              video.setAttribute('preload','none');
              return;
            }

            var scheduleVideo = function(){
              var delay = isMobile ? 1800 : 420;
              if('requestIdleCallback' in window){
                window.requestIdleCallback(loadVideo, {timeout: delay + 900});
              }else{
                setTimeout(loadVideo, delay);
              }
              setTimeout(play, delay + 360);
            };

            if(document.readyState === 'complete'){
              scheduleVideo();
            }else{
              window.addEventListener('load', scheduleVideo, {once:true, passive:true});
            }
            ['pointerdown','touchstart','keydown','scroll'].forEach(function(ev){
              window.addEventListener(ev, function(){ loadVideo(); play(); }, {once:true, passive:true});
            });
            window.addEventListener('pageshow', function(){ if(!isMobile){loadVideo(); play();} }, {passive:true});
            document.addEventListener('visibilitychange', function(){ if(!document.hidden && !isMobile){ loadVideo(); play(); }});
          }catch(e){}
        }

        /* v2.2.144: reveal the right visual only after the mockup image is ready.
           This prevents decorative shadow/fade layers from flashing as rectangles before image load. */
        (function(){
          var mock = root.querySelector('.zpWebHeroKat__mock');
          var marked = false;
          function markReady(){
            if(marked) return;
            marked = true;
            requestAnimationFrame(function(){
              if(root.getAttribute('data-ready') !== '1') root.setAttribute('data-ready','1');
            });
          }
          root.setAttribute('data-ready','1');
          if(mock){
            if(mock.complete && mock.naturalWidth > 0){
              markReady();
            }else{
              mock.addEventListener('load', markReady, {once:true});
              mock.addEventListener('error', markReady, {once:true});
              setTimeout(markReady, 2200);
            }
          }else{
            markReady();
          }
        })();
      }

      if(document.readyState === 'loading'){
        document.addEventListener('DOMContentLoaded', init, {once:true});
      }else{
        init();
      }

      window.addEventListener('load', init, {once:true});
    })();

/* ===== 2_portfolio.html ===== */
(function(){
      'use strict';

      const root = document.getElementById('zpPortfolioReveal');
      if(!root || root.dataset.zpPortfolioInit === '1') return;
      root.dataset.zpPortfolioInit = '1';

      const initIcons = () => {
        if(window.lucide && typeof window.lucide.createIcons === 'function'){
          try{
            window.lucide.createIcons({
              attrs:{
                'stroke-width':2,
                'stroke-linecap':'round',
                'stroke-linejoin':'round',
                'aria-hidden':'true'
              }
            });
          }catch(e){}
        }
      };

      // v2.2.549: don't create the portfolio's (page-wide) Lucide icon set synchronously
      // here — this IIFE runs in the defer queue *before* header.js, and a synchronous
      // createIcons() pass over the whole document delayed header.js setActive() (the
      // active-link underline). Defer it one frame; icons are below the fold / inside cards,
      // so there is no first-viewport flash. Above-the-fold (hero/header) icons are already
      // created by the hero controller on DOMContentLoaded.
      if(window.lucide){requestAnimationFrame(initIcons)}else{setTimeout(initIcons,260)}
      window.addEventListener('load', initIcons, {once:true, passive:true});

      const cards = Array.from(root.querySelectorAll('[data-zp-ss-card]'));
      const stack = root.querySelector('.zpSS__stack');
      const progressCurrent = root.querySelector('.zpPRProgress__count strong');
      const progressTotal = root.querySelector('.zpPRProgress__count em');
      const totalCards = cards.length;

      if(progressTotal) progressTotal.textContent = '/' + String(totalCards).padStart(2,'0');
      if(!cards.length) return;

      const mqMobile = window.matchMedia ? window.matchMedia('(max-width: 860px)') : {matches:false};
      let raf = 0;
      let resizeRaf = 0;
      let lastActive = -1;
      let lastProgress = -1;

      const moveBadgesForViewport = () => {
        const isMobile = mqMobile.matches;

        cards.forEach(card => {
          const copy = card.querySelector('.zpSSCard__copy');
          const actions = card.querySelector('.zpSSCard__actions');
          const review = card.querySelector(':scope > .zpSSReview, :scope > .zpSSSignature');
          const badges = card.querySelector('.zpSSBadges');
          if(!copy || !actions || !review || !badges) return;

          if(isMobile){
            if(badges.parentElement !== card){review.insertAdjacentElement('afterend', badges)}
          }else{
            if(badges.parentElement !== copy){actions.insertAdjacentElement('afterend', badges)}
          }
        });

        initIcons();
      };

      const setActive = (idx) => {
        if(idx === lastActive) return;
        lastActive = idx;

        cards.forEach((card, i) => card.classList.toggle('isActive', i === idx));
        if(progressCurrent && idx > -1){progressCurrent.textContent = String(idx + 1).padStart(2,'0')}
      };

      const update = () => {
        raf = 0;
        const vh = window.innerHeight || 800;
        const rootRect = root.getBoundingClientRect();

        const start = vh * .72;
        const end = -(rootRect.height - vh * .72);
        const total = Math.max(1, start - end);
        const value = Math.max(0, Math.min(1, (start - rootRect.top) / total));
        const rounded = Math.round(value * 1000) / 1000;

        if(rounded !== lastProgress){
          lastProgress = rounded;
          root.style.setProperty('--zpPRProgress', String(rounded));
        }

        root.classList.toggle('isProgressVisible', rootRect.top < vh * .80 && rootRect.bottom > vh * .20 && !mqMobile.matches);
        if(mqMobile.matches) return;

        const center = vh * .52;
        let activeIdx = -1;
        let best = Infinity;

        for(let i = 0; i < cards.length; i++){
          const rect = cards[i].getBoundingClientRect();
          if(rect.bottom < 0 || rect.top > vh) continue;
          const dist = Math.abs((rect.top + rect.height * .5) - center);
          if(dist < best){best = dist;activeIdx = i}
        }

        setActive(activeIdx);
      };

      const requestUpdate = () => {
        if(raf) return;
        raf = window.requestAnimationFrame(update);
      };

      const revealAllMobile = () => {
        if(mqMobile.matches){cards.forEach(card => card.classList.add('isVisible'))}
      };

      const start = () => {
        root.classList.add('isReady');

        if('IntersectionObserver' in window && !mqMobile.matches){
          // v2.2.549: the first portfolio card sits directly under the hero, ~600px below the
          // desktop fold, and is DOUBLE-gated — by this carousel (.isVisible reveals card
          // content/mock) AND by the global motion-lite reveal (data-zp-motion -> .is-zp-visible
          // reveals the card container opacity/transform). Both normally wait for scroll, so a
          // fast scroll right after the hero lands on an empty / half-faded first widget. Prime
          // BOTH reveal flags on card #1 so it is painted on load. motion-lite only ever ADDS
          // .is-zp-visible (never removes) and its safety pass skips already-visible nodes, so
          // this survives once motion-lite initializes. Lower cards keep their on-scroll reveal
          // (unchanged). Final look is identical — only card #1's entrance moves on-scroll -> on-load.
          if(cards[0]) cards[0].classList.add('isVisible', 'is-zp-visible');

          const revealIo = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
              if(entry.isIntersecting){
                entry.target.classList.add('isVisible');
                revealIo.unobserve(entry.target);
                requestUpdate();
              }
            });
          }, {threshold:.10,rootMargin:'0px 0px -5% 0px'});

          cards.forEach((card, i) => { if(i > 0) revealIo.observe(card); });
        }else{
          cards.forEach(card => card.classList.add('isVisible'));
        }

        requestUpdate();
      };

      if(stack){
        let stackRaf = 0;
        stack.addEventListener('scroll', () => {
          if(!mqMobile.matches || stackRaf) return;
          stackRaf = requestAnimationFrame(() => {
            stackRaf = 0;
            const left = stack.scrollLeft;
            let idx = 0;
            let best = Infinity;

            cards.forEach((card, i) => {
              const d = Math.abs(card.offsetLeft - left - 16);
              if(d < best){best = d;idx = i}
            });

            if(progressCurrent) progressCurrent.textContent = String(idx + 1).padStart(2,'0');
          });
        }, {passive:true});
      }

      window.addEventListener('scroll', requestUpdate, {passive:true});
      window.addEventListener('resize', () => {
        if(resizeRaf) return;
        resizeRaf = requestAnimationFrame(() => {
          resizeRaf = 0;
          moveBadgesForViewport();
          revealAllMobile();
          lastActive = -1;
          requestUpdate();
        });
      }, {passive:true});

      revealAllMobile();
      if(mqMobile.matches){
        // Mobile keeps its existing immediate behavior (cards already revealed above).
        moveBadgesForViewport();
        requestAnimationFrame(start);
      }else{
        // v2.2.549 desktop: this controller is on the eager defer path (kept eager since
        // v2.2.548 for the next-fold block) and runs *before* header.js. Defer the heavy
        // badge relayout + icon work to the next frame so header.js setActive() (active-link
        // underline) is not blocked by this controller. start() also stayed rAF-gated before.
        requestAnimationFrame(function(){ moveBadgesForViewport(); start(); });
      }
      window.addEventListener('load', requestUpdate, {once:true, passive:true});
    })();

/* ===== 3_trust.html ===== */
(function(){
    function initZpTrustCert(){
      var root=document.getElementById('zpTrustCert');
      if(!root || root.dataset.zpTrustCertReady==='1') return;
      root.dataset.zpTrustCertReady='1';

      function initIcons(){
        if(window.lucide && typeof window.lucide.createIcons==='function'){
          try{
            window.lucide.createIcons({
              attrs:{
                'stroke-width':2,
                'stroke-linecap':'round',
                'stroke-linejoin':'round',
                'aria-hidden':'true'
              }
            });
          }catch(e){}
        }
      }

      initIcons();
      window.addEventListener('load',initIcons,{once:true,passive:true});

      var reduced=false;
      try{reduced=window.matchMedia('(prefers-reduced-motion: reduce)').matches}catch(e){}

      if(reduced){
        root.classList.add('is-inview');
        return;
      }

      if('IntersectionObserver' in window){
        var io=new IntersectionObserver(function(entries){
          entries.forEach(function(entry){
            if(entry.isIntersecting){
              root.classList.add('is-inview');
              io.disconnect();
            }
          });
        },{threshold:.16,rootMargin:'0px 0px -8% 0px'});

        io.observe(root);
      }else{
        root.classList.add('is-inview');
      }
    }

    if(document.readyState==='loading'){
      document.addEventListener('DOMContentLoaded',initZpTrustCert);
    }else{
      initZpTrustCert();
    }
  })();

/* ===== 4_proces.html ===== */
(function(){
    function initZpProcessFlow(){
      var root=document.getElementById('zpProcessFlow');
      if(!root || root.dataset.zpProcessReady==='1') return;
      root.dataset.zpProcessReady='1';

      function initIcons(){
        if(window.lucide && typeof window.lucide.createIcons==='function'){
          try{window.lucide.createIcons({attrs:{'stroke-width':2,'stroke-linecap':'round','stroke-linejoin':'round','aria-hidden':'true'}})}catch(e){}
        }
      }
      initIcons();
      window.addEventListener('load',initIcons,{once:true,passive:true});

      var reduced=false;
      try{reduced=window.matchMedia('(prefers-reduced-motion: reduce)').matches}catch(e){}
      if(reduced){root.classList.add('is-inview');return;}

      if('IntersectionObserver' in window){
        var io=new IntersectionObserver(function(entries){entries.forEach(function(entry){if(entry.isIntersecting){root.classList.add('is-inview')}})},{threshold:.10,rootMargin:'0px 0px -8% 0px'});
        io.observe(root);
      }else{root.classList.add('is-inview')}

      var pin=root.querySelector('[data-zp-process-pin]');
      var track=root.querySelector('[data-zp-process-track]');
      var progressBar=root.querySelector('.zpProcessFlow__progress span');
      var cards=Array.prototype.slice.call(root.querySelectorAll('[data-zp-process-card]'));
      var ticking=false;
      var mobileMq=window.matchMedia ? window.matchMedia('(max-width: 880px)') : {matches:false};
      var mode='';
      var mobileScrollRaf=0;

      function clamp(n,min,max){return Math.min(Math.max(n,min),max)}
      function measureMaxShift(){
        if(!track) return 0;
        var viewport=root.querySelector('.zpProcessFlow__viewport');
        if(!viewport) return 0;
        return Math.max(0,track.scrollWidth-viewport.clientWidth);
      }
      function setDynamicPinHeight(){
        if(!pin || !track || mobileMq.matches) return;
        var maxShift=measureMaxShift();
        var vw=window.innerWidth || document.documentElement.clientWidth || 1200;
        var scrollDistance=clamp(maxShift*.50,520,1320);
        root.style.setProperty('--pinScroll',scrollDistance.toFixed(0)+'px');
      }
      function setActive(index){
        cards.forEach(function(card,i){card.classList.toggle('is-active',i===index);var dist=Math.abs(i-index);card.style.setProperty('--cardLift',dist===0?-8:(dist===1?-2:0));});
      }
      function updateHorizontal(){
        ticking=false;
        if(mobileMq.matches) return;
        if(!pin || !track){root.style.setProperty('--processProgress','0');root.style.setProperty('--trackX','0px');cards.forEach(function(card){card.classList.remove('is-active')});return;}
        setDynamicPinHeight();
        var rect=pin.getBoundingClientRect();
        var vh=window.innerHeight || document.documentElement.clientHeight || 800;
        var scrollable=Math.max(1,rect.height-vh);
        var p=clamp((-rect.top)/scrollable,0,1);
        var x=measureMaxShift()*p*-1;
        root.style.setProperty('--processProgress',p.toFixed(4));
        root.style.setProperty('--trackX',x.toFixed(2)+'px');
        if(progressBar){progressBar.style.width=(p*100).toFixed(2)+'%'}
        setActive(Math.round(p*Math.max(0,cards.length-1)));
      }
      function updateMobileProgress(){
        mobileScrollRaf=0;
        if(!track || !mobileMq.matches) return;
        var max=Math.max(1,track.scrollWidth-track.clientWidth);
        var p=clamp(track.scrollLeft/max,0,1);
        root.style.setProperty('--processProgress',p.toFixed(4));
        if(progressBar){progressBar.style.width=(p*100).toFixed(2)+'%'}
        var left=track.scrollLeft;
        var idx=0;
        var best=Infinity;
        cards.forEach(function(card,i){var d=Math.abs(card.offsetLeft-left-10);if(d<best){best=d;idx=i;}});
        cards.forEach(function(card,i){card.classList.toggle('is-active',i===idx);card.style.setProperty('--cardLift',0);});
      }
      function requestMobileProgress(){
        if(mobileScrollRaf) return;
        mobileScrollRaf=window.requestAnimationFrame(updateMobileProgress);
      }
      function requestUpdate(){
        if(mobileMq.matches){requestMobileProgress();return;}
        if(ticking) return;
        ticking=true;
        window.requestAnimationFrame(updateHorizontal);
      }
      function setupMode(){
        var next=mobileMq.matches?'mobile':'desktop';
        if(next===mode) return;
        mode=next;
        root.classList.toggle('is-mobile-swipe',next==='mobile');
        root.classList.toggle('is-desktop-pin',next==='desktop');
        root.style.setProperty('--trackX','0px');
        if(track){track.scrollLeft=0;}
        if(next==='mobile'){
          root.style.setProperty('--processProgress','0');
          if(progressBar){progressBar.style.width='0%'}
          setActive(0);
        }else{
          setDynamicPinHeight();
          requestUpdate();
        }
      }

      if(track){track.addEventListener('scroll',requestMobileProgress,{passive:true});}
      window.addEventListener('scroll',requestUpdate,{passive:true});
      window.addEventListener('resize',function(){setupMode();setDynamicPinHeight();requestUpdate()},{passive:true});
      window.addEventListener('orientationchange',function(){setTimeout(function(){setupMode();setDynamicPinHeight();requestUpdate()},160)},{passive:true});

      setupMode();
      setDynamicPinHeight();
      requestUpdate();
      setTimeout(function(){setupMode();setDynamicPinHeight();requestUpdate()},250);
      setTimeout(function(){setupMode();setDynamicPinHeight();requestUpdate()},850);
    }
    if(document.readyState==='loading'){document.addEventListener('DOMContentLoaded',initZpProcessFlow)}else{initZpProcessFlow()}
  })();

/* ===== 5_klienci.html ===== */
(function(){
    function initZpTrustedLogos(){
      var root=document.getElementById('zpTrustedLogos');
      if(!root || root.dataset.zpTrustedReady==='1') return;
      root.dataset.zpTrustedReady='1';

      function initIcons(){
        if(window.lucide && typeof window.lucide.createIcons==='function'){
          try{
            window.lucide.createIcons({
              attrs:{
                'stroke-width':2,
                'stroke-linecap':'round',
                'stroke-linejoin':'round',
                'aria-hidden':'true'
              }
            });
          }catch(e){}
        }
      }

      initIcons();
      window.addEventListener('load',initIcons,{once:true,passive:true});

      var reduced=false;
      try{reduced=window.matchMedia('(prefers-reduced-motion: reduce)').matches}catch(e){}

      if(reduced){
        root.classList.add('is-inview');
        return;
      }

      if('IntersectionObserver' in window){
        var io=new IntersectionObserver(function(entries){
          entries.forEach(function(entry){
            if(entry.isIntersecting){
              root.classList.add('is-inview');
              io.disconnect();
            }
          });
        },{threshold:.14,rootMargin:'0px 0px -8% 0px'});

        io.observe(root);
      }else{
        root.classList.add('is-inview');
      }
    }

    if(document.readyState==='loading'){
      document.addEventListener('DOMContentLoaded',initZpTrustedLogos);
    }else{
      initZpTrustedLogos();
    }
  })();

/* ===== 6_pakiety.html ===== */
(function(){
    function initZpConfigGate(){
      var root=document.getElementById('zpConfigGate');
      if(!root || root.dataset.zpConfigReady==='1') return;
      root.dataset.zpConfigReady='1';

      function initIcons(){
        if(window.lucide && typeof window.lucide.createIcons==='function'){
          try{
            window.lucide.createIcons({
              attrs:{
                'stroke-width':2,
                'stroke-linecap':'round',
                'stroke-linejoin':'round',
                'aria-hidden':'true'
              }
            });
          }catch(e){}
        }
      }

      initIcons();
      window.addEventListener('load',initIcons,{once:true,passive:true});

      var reduced=false;
      try{reduced=window.matchMedia('(prefers-reduced-motion: reduce)').matches}catch(e){}

      if(reduced){
        root.classList.add('is-inview');
        return;
      }

      if('IntersectionObserver' in window){
        var io=new IntersectionObserver(function(entries){
          entries.forEach(function(entry){
            if(entry.isIntersecting){
              root.classList.add('is-inview');
              io.disconnect();
            }
          });
        },{threshold:.14,rootMargin:'0px 0px -8% 0px'});

        io.observe(root);
      }else{
        root.classList.add('is-inview');
      }
    }

    if(document.readyState==='loading'){
      document.addEventListener('DOMContentLoaded',initZpConfigGate);
    }else{
      initZpConfigGate();
    }
  })();

/* ===== 7_branze.html ===== */
(function(){
    function initZpIndustryDark(){
      var root=document.getElementById('zpIndustryDark');
      if(!root || root.dataset.zpIndustryReady==='1') return;
      root.dataset.zpIndustryReady='1';

      function initIcons(){
        if(window.lucide && typeof window.lucide.createIcons==='function'){
          try{
            window.lucide.createIcons({
              attrs:{
                'stroke-width':2,
                'stroke-linecap':'round',
                'stroke-linejoin':'round',
                'aria-hidden':'true'
              }
            });
          }catch(e){}
        }
      }

      initIcons();
      window.addEventListener('load',initIcons,{once:true,passive:true});

      var reduced=false;
      try{
        reduced=window.matchMedia('(prefers-reduced-motion: reduce)').matches;
      }catch(e){}

      if(reduced){
        root.classList.add('is-inview');
        return;
      }

      if('IntersectionObserver' in window){
        var io=new IntersectionObserver(function(entries){
          entries.forEach(function(entry){
            if(entry.isIntersecting){
              root.classList.add('is-inview');
              io.disconnect();
            }
          });
        },{threshold:.12,rootMargin:'0px 0px -8% 0px'});

        io.observe(root);
      }else{
        root.classList.add('is-inview');
      }
    }

    if(document.readyState==='loading'){
      document.addEventListener('DOMContentLoaded',initZpIndustryDark);
    }else{
      initZpIndustryDark();
    }
  })();

/* ===== 8_faq.html ===== */
(function(){
    function initZpFaqKatNavy(){
      var root=document.getElementById('zpFaqKatNavy');
      if(!root || root.dataset.zpFaqNavyReady==='1') return;
      root.dataset.zpFaqNavyReady='1';

      function initIcons(){
        if(window.lucide && typeof window.lucide.createIcons==='function'){
          try{
            window.lucide.createIcons({
              attrs:{
                'stroke-width':2,
                'stroke-linecap':'round',
                'stroke-linejoin':'round',
                'aria-hidden':'true'
              }
            });
          }catch(e){}
        }
      }

      initIcons();
      window.addEventListener('load',initIcons,{once:true,passive:true});

      var items=Array.prototype.slice.call(root.querySelectorAll('.zpFaqKatNavy__item'));

      items.forEach(function(item){
        var btn=item.querySelector('.zpFaqKatNavy__question');
        if(!btn) return;

        btn.addEventListener('click',function(){
          var isOpen=item.classList.contains('is-open');

          items.forEach(function(other){
            other.classList.remove('is-open');
            var otherBtn=other.querySelector('.zpFaqKatNavy__question');
            if(otherBtn) otherBtn.setAttribute('aria-expanded','false');
          });

          if(!isOpen){
            item.classList.add('is-open');
            btn.setAttribute('aria-expanded','true');
          }
        });
      });

      var reduced=false;
      try{
        reduced=window.matchMedia('(prefers-reduced-motion: reduce)').matches;
      }catch(e){}

      if(reduced){
        root.classList.add('is-inview');
        return;
      }

      if('IntersectionObserver' in window){
        var io=new IntersectionObserver(function(entries){
          entries.forEach(function(entry){
            if(entry.isIntersecting){
              root.classList.add('is-inview');
              io.disconnect();
            }
          });
        },{threshold:.12,rootMargin:'0px 0px -8% 0px'});

        io.observe(root);
      }else{
        root.classList.add('is-inview');
      }
    }

    if(document.readyState==='loading'){
      document.addEventListener('DOMContentLoaded',initZpFaqKatNavy);
    }else{
      initZpFaqKatNavy();
    }
  })();

(function(){
  function initIcons(){
    if(window.lucide && typeof window.lucide.createIcons==='function'){
      try{window.lucide.createIcons({attrs:{'stroke-width':2,'stroke-linecap':'round','stroke-linejoin':'round','aria-hidden':'true'}})}catch(e){}
    }
  }
  if(document.readyState==='loading'){document.addEventListener('DOMContentLoaded',initIcons,{once:true});}else{initIcons();}
  window.addEventListener('load',initIcons,{once:true,passive:true});
})();


/* ZP HOTFIX v1.8.10 — industry image fallbacks */
(function(){
  function zpIndustryFallbacks(){
    var map={
      'strony_internetowe_dla_salonow_beauty.webp':'strona_internetowa_dla_branzy_beauty.webp',
      'strony_internetowe_dla_lekarzy.webp':'strona_internetowa_dla_lekarza.webp',
      'sklep_internetowy_dla_producenta.webp':'strony_i_sklepy_dla_producentow.webp'
    };
    document.querySelectorAll('.zpIndustryDark__img').forEach(function(img){
      if(img.dataset.zpFallbackReady==='1') return;
      img.dataset.zpFallbackReady='1';
      img.addEventListener('error',function(){
        var src=img.getAttribute('src')||'';
        var explicitFallback=img.getAttribute('data-zp-fallback')||'';
        var next='';
        Object.keys(map).some(function(bad){
          if(src.indexOf(bad)>-1){
            next=src.replace(bad,map[bad]);
            return true;
          }
          return false;
        });
        if(!next && explicitFallback && explicitFallback!==src){
          next=explicitFallback;
        }
        if(next){
          img.src=next;
          var card=img.closest('.zpIndustryDark__card');
          if(card) card.style.setProperty('--img',"url('"+next+"')");
        }
      },{once:true});
    });
  }
  if(document.readyState==='loading'){document.addEventListener('DOMContentLoaded',zpIndustryFallbacks,{once:true})}else{zpIndustryFallbacks()}
})();


/* ===== ZP Suite v2.1.70 — strict one-card snap shared mobile portfolio ===== */
(function(){
  'use strict';

  function initOneCardSnap(rootId, stackSel, cardSel, sideVar){
    var root=document.getElementById(rootId);
    if(!root || root.dataset.zpOneCardSnap70==='1') return;
    var stack=root.querySelector(stackSel);
    if(!stack) return;
    var mq=window.matchMedia?window.matchMedia('(max-width: 860px)'):{matches:false};
    var cards=Array.prototype.slice.call(root.querySelectorAll(cardSel));
    if(!cards.length) return;
    root.dataset.zpOneCardSnap70='1';

    var startX=0,startY=0,startLeft=0,timer=0,locked=false;

    function sidePad(){
      var cs=getComputedStyle(stack);
      var v=parseFloat(cs.paddingLeft||'16');
      return isNaN(v)?16:v;
    }

    function step(){
      if(cards.length>1) return Math.max(1,cards[1].offsetLeft-cards[0].offsetLeft);
      return cards[0]?cards[0].offsetWidth+16:window.innerWidth;
    }

    function currentIndex(){
      return Math.max(0,Math.min(cards.length-1,Math.round(stack.scrollLeft/step())));
    }

    function go(idx,smooth){
      if(!mq.matches) return;
      idx=Math.max(0,Math.min(cards.length-1,idx));
      var target=Math.max(0,cards[idx].offsetLeft-sidePad());
      try{ stack.scrollTo({left:target,behavior:smooth?'smooth':'auto'}); }
      catch(e){ stack.scrollLeft=target; }
    }

    function settle(){
      if(timer) clearTimeout(timer);
      timer=setTimeout(function(){go(currentIndex(),true);},95);
    }

    stack.addEventListener('touchstart',function(e){
      if(!mq.matches || !e.touches || !e.touches.length) return;
      startX=e.touches[0].clientX;
      startY=e.touches[0].clientY;
      startLeft=stack.scrollLeft;
      if(timer) clearTimeout(timer);
    },{passive:true});

    stack.addEventListener('touchend',function(e){
      if(!mq.matches) return;
      var t=e.changedTouches&&e.changedTouches[0];
      var dx=t?t.clientX-startX:0;
      var dy=t?t.clientY-startY:0;
      if(Math.abs(dx)>30 && Math.abs(dx)>Math.abs(dy)*1.05){
        var idx=Math.round(startLeft/step()) + (dx<0?1:-1);
        go(idx,true);
      }else{
        settle();
      }
    },{passive:true});

    stack.addEventListener('scroll',function(){
      if(!mq.matches) return;
      settle();
    },{passive:true});

    stack.addEventListener('wheel',function(e){
      if(!mq.matches) return;
      if(Math.abs(e.deltaX)<Math.abs(e.deltaY)) return;
      if(locked) return;
      locked=true;
      go(currentIndex()+(e.deltaX>0?1:-1),true);
      setTimeout(function(){locked=false;},380);
    },{passive:true});

    window.addEventListener('resize',function(){ if(mq.matches) go(currentIndex(),false); },{passive:true});
  }

  function boot(){
    /* v2.2.30: native mobile horizontal scroll, no forced one-card snap */
  }

  if(document.readyState==='loading') document.addEventListener('DOMContentLoaded',boot,{once:true});
  else boot();
})();


/* =========================================================
   ZP Suite v2.1.72 — mobile snap helpers for process + portfolio
========================================================= */
(function(){
  'use strict';

  function snapNearest(scroller, selector, sidePad){
    if(!scroller) return;
    var cards = Array.prototype.slice.call(scroller.querySelectorAll(selector || ':scope > *'));
    if(!cards.length) return;
    var current = scroller.scrollLeft;
    var best = cards[0];
    var bestD = Infinity;
    cards.forEach(function(card){
      var d = Math.abs(card.offsetLeft - (sidePad || 0) - current);
      if(d < bestD){ bestD = d; best = card; }
    });
    try{
      scroller.scrollTo({left: Math.max(0, best.offsetLeft - (sidePad || 0)), behavior:'smooth'});
    }catch(e){
      scroller.scrollLeft = Math.max(0, best.offsetLeft - (sidePad || 0));
    }
  }

  function wire(scroller, selector, sidePad){
    if(!scroller || scroller.dataset.zpSnap72 === '1') return;
    scroller.dataset.zpSnap72 = '1';
    var timer = 0;
    function schedule(){
      /* v2.2.30: no auto-snap after touchend/pointerup — easier manual swipe */
      clearTimeout(timer);
    }
    scroller.addEventListener('touchend', schedule, {passive:true});
    scroller.addEventListener('pointerup', schedule, {passive:true});
  }

  function init(){
    if(!window.matchMedia || !window.matchMedia('(max-width: 880px)').matches) return;

    var processTrack = document.querySelector('#zpShopProcessFlow [data-zp-shop-process-track]');
    if(processTrack){
      processTrack.style.overflowX = 'auto';
      processTrack.style.webkitOverflowScrolling = 'touch';
      wire(processTrack, '[data-zp-shop-process-card]', 16);
    }

    var shopPortfolio = document.querySelector('#portfolio-sklepy .zpShopPortfolio__stack');
    if(shopPortfolio){
      wire(shopPortfolio, '.zpShopCard', 16);
    }

    var pagePortfolio = document.querySelector('#portfolio-strony .zpPortfolioKat__track, #portfolio-strony .zpPortfolioKat__stack, #zpPortfolioKat .zpPortfolioKat__track, #zpPortfolioKat .zpPortfolioKat__stack');
    if(pagePortfolio){
      wire(pagePortfolio, '.zpPortfolioKat__card', 16);
    }
  }

  if(document.readyState === 'loading'){
    document.addEventListener('DOMContentLoaded', init, {once:true});
  }else{
    init();
  }
  window.addEventListener('resize', init, {passive:true});
})();
