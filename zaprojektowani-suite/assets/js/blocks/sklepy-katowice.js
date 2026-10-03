
/* ===== hero ===== */
(function(){
      'use strict';

      function init(){
        var root = document.getElementById('zpShopHeroKat');
        if(!root || root.dataset.initialized === '1') return;
        if(root.getAttribute('data-ready') !== '1') root.setAttribute('data-ready','0');

        root.dataset.initialized = '1';

        function initIcons(){
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
        }

        initIcons();
        window.addEventListener('load', initIcons, {once:true, passive:true});

        root.querySelectorAll('a[href^="#"]').forEach(function(link){
          link.addEventListener('click', function(e){
            var id = link.getAttribute('href');
            if(!id || id === '#') return;

            var target = document.querySelector(id);
            if(!target) return;

            e.preventDefault();

            window.scrollTo({
              top: target.getBoundingClientRect().top + window.pageYOffset - 92,
              behavior:'smooth'
            });
          });
        });

        var video = root.querySelector('.zpShopCockpit__video');
        if(video){
          try{
            video.muted = true;
            video.playsInline = true;
            var playPromise = video.play();
            if(playPromise && typeof playPromise.catch === 'function'){
              playPromise.catch(function(){});
            }
          }catch(e){}
        }

        window.requestAnimationFrame(function(){
          var visual = root.querySelector('.zpShopCockpit__visual');
            root.setAttribute('data-ready','1');
            if(visual){
              visual.classList.remove('is-anti-flash-hidden');
              visual.style.opacity = '';
              visual.style.visibility = '';
              visual.style.pointerEvents = '';
            }
        });
      }

      if(document.readyState === 'loading'){
        document.addEventListener('DOMContentLoaded', init, {once:true});
      }else{
        init();
      }

      window.addEventListener('load', init, {once:true});
    })();

/* ===== why ===== */
(function(){
      'use strict';

      function init(){
        var root = document.getElementById('zpShopWhyLight');
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
              behavior:'smooth'
            });
          });
        });

        var reveal = function(){
          root.setAttribute('data-ready','1');
        };

        if('IntersectionObserver' in window){
          var io = new IntersectionObserver(function(entries){
            entries.forEach(function(entry){
              if(entry.isIntersecting){
                reveal();
                io.disconnect();
              }
            });
          }, {threshold:.16});
          io.observe(root);
        }else{
          setTimeout(reveal, 80);
        }
      }

      if(document.readyState === 'loading'){
        document.addEventListener('DOMContentLoaded', init, {once:true});
      }else{
        init();
      }

      window.addEventListener('load', init, {once:true});
    })();

/* ===== trust ===== */
(function(){'use strict';function init(){var root=document.getElementById('zpEcomTrust');if(!root||root.dataset.initialized==='1')return;root.dataset.initialized='1';function initIcons(){if(window.lucide&&typeof window.lucide.createIcons==='function'){try{window.lucide.createIcons({attrs:{'stroke-width':2,'stroke-linecap':'round','stroke-linejoin':'round','aria-hidden':'true'}})}catch(e){}}}initIcons();window.addEventListener('load',initIcons,{once:true,passive:true});root.querySelectorAll('a[href^="#"]').forEach(function(link){link.addEventListener('click',function(e){var id=link.getAttribute('href');if(!id||id==='#')return;var target=document.querySelector(id);if(!target)return;e.preventDefault();window.scrollTo({top:target.getBoundingClientRect().top+window.pageYOffset-92,behavior:'smooth'})})});var reduced=false;try{reduced=window.matchMedia('(prefers-reduced-motion: reduce)').matches}catch(e){}if(reduced){root.setAttribute('data-ready','1');return}if('IntersectionObserver'in window){var io=new IntersectionObserver(function(entries){entries.forEach(function(entry){if(entry.isIntersecting){root.setAttribute('data-ready','1');io.disconnect()}})},{threshold:.14,rootMargin:'0px 0px -8% 0px'});io.observe(root)}else{root.setAttribute('data-ready','1')}}if(document.readyState==='loading'){document.addEventListener('DOMContentLoaded',init,{once:true})}else{init()}})();

/* ===== portfolio ===== */
(function(){
      'use strict';

      var root = document.getElementById('portfolio-sklepy');
      if(!root || root.dataset.initialized === '1') return;
      root.dataset.initialized = '1';

      function initIcons(){
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
      }

      initIcons();
      window.addEventListener('load', initIcons, {once:true, passive:true});

      var cards = Array.prototype.slice.call(root.querySelectorAll('[data-zp-shop-card]'));
      var stack = root.querySelector('.zpShopPortfolio__stack');
      var progressCurrent = root.querySelector('.zpShopPortfolio__progressCount strong');
      var progressTotal = root.querySelector('.zpShopPortfolio__progressCount em');
      var totalCards = cards.length;
      var mqMobile = window.matchMedia ? window.matchMedia('(max-width: 860px)') : {matches:false};
      var raf = 0;
      var lastActive = -1;
      var lastProgress = -1;

      if(progressTotal) progressTotal.textContent = '/' + String(totalCards).padStart(2,'0');

      function setActive(idx){
        if(idx === lastActive) return;
        lastActive = idx;
        cards.forEach(function(card, i){
          card.classList.toggle('is-active', i === idx);
        });
        if(progressCurrent && idx > -1){
          progressCurrent.textContent = String(idx + 1).padStart(2,'0');
        }
      }

      function update(){
        raf = 0;
        var vh = window.innerHeight || 800;
        var rootRect = root.getBoundingClientRect();

        var start = vh * .72;
        var end = -(rootRect.height - vh * .72);
        var total = Math.max(1, start - end);
        var value = Math.max(0, Math.min(1, (start - rootRect.top) / total));
        var rounded = Math.round(value * 1000) / 1000;

        if(rounded !== lastProgress){
          lastProgress = rounded;
          root.style.setProperty('--zpShopPortfolioProgress', String(rounded));
        }

        root.classList.toggle('is-progress-visible', rootRect.top < vh * .80 && rootRect.bottom > vh * .20 && !mqMobile.matches);

        if(mqMobile.matches) return;

        var center = vh * .52;
        var activeIdx = -1;
        var best = Infinity;

        cards.forEach(function(card, i){
          var rect = card.getBoundingClientRect();
          if(rect.bottom < 0 || rect.top > vh) return;
          var dist = Math.abs((rect.top + rect.height * .5) - center);
          if(dist < best){
            best = dist;
            activeIdx = i;
          }
        });

        setActive(activeIdx);
      }

      function requestUpdate(){
        if(raf) return;
        raf = window.requestAnimationFrame(update);
      }

      function revealMobile(){
        if(mqMobile.matches){
          cards.forEach(function(card){card.classList.add('is-visible');});
        }
      }

      function start(){
        root.setAttribute('data-ready','1');

        if('IntersectionObserver' in window && !mqMobile.matches){
          var revealIo = new IntersectionObserver(function(entries){
            entries.forEach(function(entry){
              if(entry.isIntersecting){
                entry.target.classList.add('is-visible');
                revealIo.unobserve(entry.target);
                requestUpdate();
              }
            });
          }, {threshold:.10, rootMargin:'0px 0px -5% 0px'});

          cards.forEach(function(card){ revealIo.observe(card); });
        }else{
          cards.forEach(function(card){card.classList.add('is-visible');});
        }

        requestUpdate();
      }

      if(stack){
        var stackRaf = 0;
        var snapTimer = 0;

        function nearestPortfolioIndex(){
          var left = stack.scrollLeft;
          var idx = 0;
          var best = Infinity;
          cards.forEach(function(card, i){
            var d = Math.abs(card.offsetLeft - left - 16);
            if(d < best){ best = d; idx = i; }
          });
          return idx;
        }

        function snapPortfolioCard(){
          return;
          if(!mqMobile.matches || !cards.length) return;
          var idx = nearestPortfolioIndex();
          var target = Math.max(0, cards[idx].offsetLeft - 16);
          try{
            stack.scrollTo({left:target, behavior:'smooth'});
          }catch(e){
            stack.scrollLeft = target;
          }
          if(progressCurrent) progressCurrent.textContent = String(idx + 1).padStart(2,'0');
        }

        stack.addEventListener('scroll', function(){
          if(!mqMobile.matches || stackRaf) return;
          stackRaf = requestAnimationFrame(function(){
            stackRaf = 0;
            var idx = nearestPortfolioIndex();
            if(progressCurrent) progressCurrent.textContent = String(idx + 1).padStart(2,'0');
          });
          window.clearTimeout(snapTimer);
          snapTimer = window.setTimeout(snapPortfolioCard, 130);
        }, {passive:true});

        stack.addEventListener('touchend', function(){
          window.clearTimeout(snapTimer);
          snapTimer = window.setTimeout(snapPortfolioCard, 90);
        }, {passive:true});

        stack.addEventListener('pointerup', function(){
          window.clearTimeout(snapTimer);
          snapTimer = window.setTimeout(snapPortfolioCard, 90);
        }, {passive:true});
      }

      window.addEventListener('scroll', requestUpdate, {passive:true});
      window.addEventListener('resize', function(){
        revealMobile();
        lastActive = -1;
        requestUpdate();
      }, {passive:true});

      root.querySelectorAll('a[href^="#"]').forEach(function(link){
        link.addEventListener('click', function(e){
          var id = link.getAttribute('href');
          if(!id || id === '#') return;
          var target = document.querySelector(id);
          if(!target) return;
          e.preventDefault();
          window.scrollTo({
            top: target.getBoundingClientRect().top + window.pageYOffset - 92,
            behavior:'smooth'
          });
        });
      });

      revealMobile();
      requestAnimationFrame(start);
      window.addEventListener('load', requestUpdate, {once:true, passive:true});
    })();

/* ===== process / 1:1 from uploaded HTML v2.1.67 ===== */
(function(){
    function initZpShopProcessFlow(){
      var root = document.getElementById('zpShopProcessFlow');
      if(!root || root.dataset.zpShopProcessReady === '1') return;
      root.dataset.zpShopProcessReady = '1';

      function initIcons(){
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
      }

      initIcons();
      window.addEventListener('load',initIcons,{once:true,passive:true});

      var reduced = false;
      try{
        reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
      }catch(e){}

      if(reduced){
        root.classList.add('is-inview');
        root.style.setProperty('--processProgress','1');
        return;
      }

      if('IntersectionObserver' in window){
        var io = new IntersectionObserver(function(entries){
          entries.forEach(function(entry){
            if(entry.isIntersecting){
              root.classList.add('is-inview');
            }
          });
        },{threshold:.10,rootMargin:'0px 0px -8% 0px'});
        io.observe(root);
      }else{
        root.classList.add('is-inview');
      }

      var pin = root.querySelector('[data-zp-shop-process-pin]');
      var track = root.querySelector('[data-zp-shop-process-track]');
      var progressLabel = root.querySelector('[data-zp-shop-progress-label]');
      var cards = Array.prototype.slice.call(root.querySelectorAll('[data-zp-shop-process-card]'));
      var ticking = false;
      var mobileMq = window.matchMedia ? window.matchMedia('(max-width: 880px)') : {matches:false};
      var mode = '';
      var mobileScrollRaf = 0;

      function clamp(n,min,max){
        return Math.min(Math.max(n,min),max);
      }

      function setProgress(p){
        var safe = clamp(p,0,1);
        root.style.setProperty('--processProgress',safe.toFixed(4));
        if(progressLabel){
          progressLabel.textContent = Math.round(safe * 100) + '%';
        }
      }

      function measureMaxShift(){
        if(!track) return 0;
        var viewport = root.querySelector('.zpShopProcessFlow__viewport');
        if(!viewport) return 0;
        return Math.max(0,track.scrollWidth - viewport.clientWidth);
      }

      function setDynamicPinHeight(){
        if(!pin || !track || mobileMq.matches) return;
        var maxShift = measureMaxShift();
        var scrollDistance = clamp(maxShift * .72,760,1680);
        root.style.setProperty('--pinScroll',scrollDistance.toFixed(0) + 'px');
      }

      function setActive(index){
        cards.forEach(function(card,i){
          card.classList.toggle('is-active',i === index);
          var dist = Math.abs(i - index);
          card.style.setProperty('--cardLift',dist === 0 ? -8 : (dist === 1 ? -2 : 0));
        });
      }

      function updateHorizontal(){
        ticking = false;

        if(mobileMq.matches) return;

        if(!pin || !track){
          setProgress(0);
          root.style.setProperty('--trackX','0px');
          cards.forEach(function(card){card.classList.remove('is-active')});
          return;
        }

        setDynamicPinHeight();

        var rect = pin.getBoundingClientRect();
        var vh = window.innerHeight || document.documentElement.clientHeight || 800;
        var scrollable = Math.max(1,rect.height - vh);
        var p = clamp((-rect.top) / scrollable,0,1);
        var x = measureMaxShift() * p * -1;

        setProgress(p);
        root.style.setProperty('--trackX',x.toFixed(2) + 'px');

        setActive(Math.round(p * Math.max(0,cards.length - 1)));
      }

      function updateMobileProgress(){
        mobileScrollRaf = 0;
        if(!track || !mobileMq.matches) return;

        var max = Math.max(1,track.scrollWidth - track.clientWidth);
        var p = clamp(track.scrollLeft / max,0,1);

        setProgress(p);

        var left = track.scrollLeft;
        var idx = 0;
        var best = Infinity;

        cards.forEach(function(card,i){
          var d = Math.abs(card.offsetLeft - left - 10);
          if(d < best){
            best = d;
            idx = i;
          }
        });

        cards.forEach(function(card,i){
          card.classList.toggle('is-active',i === idx);
          card.style.setProperty('--cardLift',0);
        });
      }

      function requestMobileProgress(){
        if(mobileScrollRaf) return;
        mobileScrollRaf = window.requestAnimationFrame(updateMobileProgress);
      }

      function requestUpdate(){
        if(mobileMq.matches){
          requestMobileProgress();
          return;
        }

        if(ticking) return;
        ticking = true;
        window.requestAnimationFrame(updateHorizontal);
      }

      function setupMode(){
        var next = mobileMq.matches ? 'mobile' : 'desktop';

        if(next === mode) return;

        mode = next;
        root.classList.toggle('is-mobile-swipe',next === 'mobile');
        root.classList.toggle('is-desktop-pin',next === 'desktop');
        root.style.setProperty('--trackX','0px');

        if(track){
          track.scrollLeft = 0;
        }

        if(next === 'mobile'){
          setProgress(0);
          setActive(0);
        }else{
          setDynamicPinHeight();
          requestUpdate();
        }
      }

      if(track){
        track.addEventListener('scroll',requestMobileProgress,{passive:true});
      }

      window.addEventListener('scroll',requestUpdate,{passive:true});
      window.addEventListener('resize',function(){
        setupMode();
        setDynamicPinHeight();
        requestUpdate();
      },{passive:true});

      window.addEventListener('orientationchange',function(){
        setTimeout(function(){
          setupMode();
          setDynamicPinHeight();
          requestUpdate();
        },160);
      },{passive:true});

      setupMode();
      setDynamicPinHeight();
      requestUpdate();

      setTimeout(function(){
        setupMode();
        setDynamicPinHeight();
        requestUpdate();
      },250);

      setTimeout(function(){
        setupMode();
        setDynamicPinHeight();
        requestUpdate();
      },850);
    }

    if(document.readyState === 'loading'){
      document.addEventListener('DOMContentLoaded',initZpShopProcessFlow);
    }else{
      initZpShopProcessFlow();
    }
  })();

/* ===== industries ===== */
(function(){
      'use strict';

      function init(){
        var root = document.getElementById('zpEcomIndustries');

        if(!root || root.dataset.initialized === '1'){
          return;
        }

        root.dataset.initialized = '1';

        function initIcons(){
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
        }

        initIcons();
        window.addEventListener('load', initIcons, {once:true, passive:true});

        var reduced = false;

        try{
          reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        }catch(e){}

        if(reduced){
          root.setAttribute('data-ready','1');
          return;
        }

        if('IntersectionObserver' in window){
          var io = new IntersectionObserver(function(entries){
            entries.forEach(function(entry){
              if(entry.isIntersecting){
                root.setAttribute('data-ready','1');
                io.disconnect();
              }
            });
          }, {threshold:.14, rootMargin:'0px 0px -8% 0px'});

          io.observe(root);
        }else{
          root.setAttribute('data-ready','1');
        }
      }

      if(document.readyState === 'loading'){
        document.addEventListener('DOMContentLoaded', init, {once:true});
      }else{
        init();
      }
    })();

/* ===== packages ===== */
(function(){
      'use strict';

      function init(){
        var root = document.getElementById('oferta-sklepow');
        if(!root || root.dataset.initialized === '1') return;

        root.dataset.initialized = '1';

        function initIcons(){
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
        }

        initIcons();
        window.addEventListener('load', initIcons, {once:true, passive:true});

        var reduced = false;
        try{
          reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        }catch(e){}

        if(reduced){
          root.setAttribute('data-ready','1');
          return;
        }

        if('IntersectionObserver' in window){
          var io = new IntersectionObserver(function(entries){
            entries.forEach(function(entry){
              if(entry.isIntersecting){
                root.setAttribute('data-ready','1');
                io.disconnect();
              }
            });
          }, {threshold:.12, rootMargin:'0px 0px -8% 0px'});

          io.observe(root);
        }else{
          root.setAttribute('data-ready','1');
        }
      }

      if(document.readyState === 'loading'){
        document.addEventListener('DOMContentLoaded', init, {once:true});
      }else{
        init();
      }
    })();

/* ===== faq ===== */
(function(){
    function initZpFaqShopKatNavy(){
      var root=document.getElementById('zpFaqShopKatNavy');
      if(!root || root.dataset.zpFaqShopReady==='1') return;
      root.dataset.zpFaqShopReady='1';

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

      var items=Array.prototype.slice.call(root.querySelectorAll('.zpFaqShopKatNavy__item'));

      items.forEach(function(item){
        var btn=item.querySelector('.zpFaqShopKatNavy__question');
        if(!btn) return;

        btn.addEventListener('click',function(){
          var isOpen=item.classList.contains('is-open');

          items.forEach(function(other){
            other.classList.remove('is-open');
            var otherBtn=other.querySelector('.zpFaqShopKatNavy__question');
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
      document.addEventListener('DOMContentLoaded',initZpFaqShopKatNavy);
    }else{
      initZpFaqShopKatNavy();
    }
  })();

/* ===== ZP Shop Katowice deep links / CTA cleanup ===== */
(function(){
  'use strict';
  function enc(v){return encodeURIComponent(v||'Sklep Rozwojowy');}
  function normalizeShopCtas(){
    var root=document.querySelector('[data-zp-shop-katowice]');
    if(!root) return;
    var packageNames=['Sklep Startowy','Sklep Rozwojowy','Sklep Indywidualny'];
    var idx=0;
    root.querySelectorAll('a[href*="studio-wyceny"]').forEach(function(a){
      var card=a.closest('article, .zpShopPackages__card, .zpPkg, .zpShopPackage, .zpShopPackagesCard, [class*="card"], [class*="Card"]');
      var txt=(card?card.textContent:a.textContent)||'';
      var pkg='Sklep Rozwojowy';
      packageNames.forEach(function(n){ if(txt.indexOf(n)>-1) pkg=n; });
      if(!card && idx<packageNames.length && /pakiet|wybierz|wycen/i.test(a.textContent||'')){pkg=packageNames[idx++];}
      a.setAttribute('href','/studio-wyceny/?zpbs_service=shop&zpbs_step=1&zpbs_package='+enc(pkg)+'#zpbsUltimate');
    });
  }
  if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',normalizeShopCtas,{once:true}); else normalizeShopCtas();
})();


/* =========================================================
ZAPROJEKTOWANI — SKLEPY KATOWICE SCROLL ENGINE v2.2.167
- portfolio zostaje natywne,
- proces na mobile: poziomy slider + progress,
- pionowy gest na karcie przewija stronę,
- poziomy gest płynnie przeskakuje do sąsiedniej karty.
========================================================= */
(function(){
  'use strict';

  function clamp(n,min,max){ return Math.min(Math.max(n,min),max); }
  function isMobile(max){
    return !window.matchMedia || window.matchMedia('(max-width:' + (max || 880) + 'px)').matches;
  }

  function initShopPortfolioMobile(){
    var root = document.getElementById('portfolio-sklepy');
    if(!root) return;
    var stack = root.querySelector('.zpShopPortfolio__stack');
    if(!stack || stack.dataset.zpScrollEngine167Portfolio === '1') return;
    stack.dataset.zpScrollEngine167Portfolio = '1';

    stack.style.overflowX = '';
    stack.style.overflowY = '';
    stack.style.webkitOverflowScrolling = 'touch';

    var progressCurrent = root.querySelector('.zpShopPortfolio__progressCount strong');
    var cards = Array.prototype.slice.call(root.querySelectorAll('[data-zp-shop-card], .zpShopCard'));
    var raf = 0;

    function updateIndex(){
      raf = 0;
      if(!isMobile(860) || !cards.length || !progressCurrent) return;
      var left = stack.scrollLeft;
      var idx = 0;
      var best = Infinity;
      cards.forEach(function(card, i){
        var d = Math.abs(card.offsetLeft - 16 - left);
        if(d < best){ best = d; idx = i; }
      });
      progressCurrent.textContent = String(idx + 1).padStart(2,'0');
    }

    stack.addEventListener('scroll', function(){
      if(!isMobile(860) || raf) return;
      raf = requestAnimationFrame(updateIndex);
    }, {passive:true});
  }

  function initProcessMobileGesture(){
    var root = document.getElementById('zpShopProcessFlow');
    if(!root) return;
    var track = root.querySelector('[data-zp-shop-process-track]');
    if(!track || track.dataset.zpScrollEngine168Process === '1') return;
    track.dataset.zpScrollEngine168Process = '1';

    /*
      v2.2.168
      Naprawa mobile: rezygnujemy z przechwytywania touchmove/preventDefault.
      Dzięki temu pionowy scroll strony działa natywnie nawet wtedy, gdy palec
      startuje na dużej karcie. Poziomy przesuw zostaje natywnym sliderem,
      a my tylko aktualizujemy progress i łagodnie dosnapowujemy po zakończeniu.
    */
    var progressLabel = root.querySelector('[data-zp-shop-progress-label]');
    var cards = Array.prototype.slice.call(root.querySelectorAll('[data-zp-shop-process-card]'));
    if(!cards.length) return;

    var raf = 0;
    var snapTimer = 0;
    var isTouching = false;
    var lastLeft = 0;
    var lastTime = 0;
    var velocity = 0;

    function maxScroll(){
      return Math.max(0, track.scrollWidth - track.clientWidth);
    }

    function cardLeft(card){
      var pad = 10;
      return clamp(card.offsetLeft - pad, 0, maxScroll());
    }

    function nearestIndex(){
      var left = track.scrollLeft;
      var idx = 0;
      var best = Infinity;
      cards.forEach(function(card, i){
        var d = Math.abs(cardLeft(card) - left);
        if(d < best){ best = d; idx = i; }
      });
      return idx;
    }

    function updateProgress(){
      raf = 0;
      if(!isMobile(880)) return;
      var max = Math.max(1, maxScroll());
      var p = clamp(track.scrollLeft / max, 0, 1);
      root.style.setProperty('--processProgress', p.toFixed(4));
      if(progressLabel) progressLabel.textContent = Math.round(p * 100) + '%';
      var idx = nearestIndex();
      cards.forEach(function(card, i){
        card.classList.toggle('is-active', i === idx);
        card.style.setProperty('--cardLift', '0');
      });
    }

    function requestProgress(){
      if(raf) return;
      raf = requestAnimationFrame(updateProgress);
    }

    function smoothSnap(){
      if(!isMobile(880) || isTouching) return;
      var idx = nearestIndex();
      var target = cardLeft(cards[idx]);
      if(Math.abs(track.scrollLeft - target) < 4){
        updateProgress();
        return;
      }
      track.scrollTo({left: target, behavior: 'smooth'});
      requestProgress();
    }

    function scheduleSnap(){
      if(snapTimer) clearTimeout(snapTimer);
      snapTimer = setTimeout(smoothSnap, 150);
    }

    track.addEventListener('touchstart', function(){
      isTouching = true;
      if(snapTimer) clearTimeout(snapTimer);
      lastLeft = track.scrollLeft;
      lastTime = Date.now();
      velocity = 0;
    }, {passive:true});

    track.addEventListener('touchmove', function(){
      /* zero preventDefault — pion strony ma działać normalnie */
      var now = Date.now();
      var dt = Math.max(1, now - lastTime);
      velocity = (track.scrollLeft - lastLeft) / dt;
      lastLeft = track.scrollLeft;
      lastTime = now;
      requestProgress();
    }, {passive:true});

    track.addEventListener('touchend', function(){
      isTouching = false;
      scheduleSnap();
    }, {passive:true});

    track.addEventListener('touchcancel', function(){
      isTouching = false;
      scheduleSnap();
    }, {passive:true});

    track.addEventListener('scroll', function(){
      requestProgress();
      if(!isTouching) scheduleSnap();
    }, {passive:true});

    if('onscrollend' in window){
      track.addEventListener('scrollend', smoothSnap, {passive:true});
    }

    window.addEventListener('resize', requestProgress, {passive:true});
    requestProgress();
  }

  function init(){
    initShopPortfolioMobile();
    initProcessMobileGesture();
  }

  if(document.readyState === 'loading'){
    document.addEventListener('DOMContentLoaded', init, {once:true});
  }else{
    init();
  }
  window.addEventListener('load', init, {once:true, passive:true});
})();

/* v2.2.819 — pakiety sklepów: pełny zakres hover desktop + tap mobile.
 * Panel przenoszony bezpośrednio do karty, żeby nie wpadał pod zawartość/z-index. */
(function(){
  'use strict';
  function bootShopPackageScopes819(){
    var root=document.getElementById('oferta-sklepow');
    if(!root || root.dataset.scope819==='1') return;
    root.dataset.scope819='1';

    function canHover(){return !!(window.matchMedia && window.matchMedia('(hover: hover) and (pointer: fine)').matches);}
    var entries=[];

    root.querySelectorAll('.zpShopPackageScope').forEach(function(scope){
      var card=scope.closest('.zpShopPackageV2');
      var trigger=scope.querySelector('.zpShopPackageScope__trigger');
      var panel=scope.querySelector('.zpShopPackageScope__panel');
      if(!card||!trigger||!panel) return;

      card.appendChild(panel);
      panel.classList.add('zpShopPackageScope__panel--card');
      var close=panel.querySelector('.zpShopPackageScope__close');
      var entry={card:card,scope:scope,trigger:trigger,panel:panel,close:close,pinned:false,dismissed:false};
      entries.push(entry);

      function sync(open){
        card.classList.toggle('scope-open',!!open);
        trigger.setAttribute('aria-expanded',open?'true':'false');
        panel.setAttribute('aria-hidden',open?'false':'true');
      }
      function closeSelf(focus,dismiss){
        entry.pinned=false;
        entry.dismissed=!!dismiss;
        sync(false);
        if(focus){try{trigger.focus({preventScroll:true});}catch(e){trigger.focus();}}
      }
      entry.sync=sync;entry.closeSelf=closeSelf;

      trigger.addEventListener('mouseenter',function(){
        if(!canHover()||entry.dismissed||entry.pinned) return;
        entries.forEach(function(other){if(other!==entry){other.pinned=false;other.dismissed=false;other.sync(false);}});
        sync(true);
      });
      trigger.addEventListener('click',function(ev){
        ev.preventDefault();ev.stopPropagation();
        var shouldOpen=!card.classList.contains('scope-open')||!entry.pinned;
        entries.forEach(function(other){if(other!==entry){other.pinned=false;other.dismissed=false;other.sync(false);}});
        entry.dismissed=false;entry.pinned=shouldOpen;sync(shouldOpen);
      });
      card.addEventListener('mouseleave',function(){entry.dismissed=false;if(canHover()&&!entry.pinned)sync(false);});
      if(close){close.addEventListener('click',function(ev){ev.preventDefault();ev.stopPropagation();closeSelf(true,canHover());});}
      panel.addEventListener('click',function(ev){ev.stopPropagation();});
      panel.querySelectorAll('a').forEach(function(a){a.addEventListener('click',function(ev){ev.stopPropagation();});});
      card.addEventListener('keydown',function(ev){if(ev.key==='Escape'&&card.classList.contains('scope-open')){ev.preventDefault();closeSelf(true,canHover());}});
    });

    document.addEventListener('click',function(ev){
      entries.forEach(function(entry){if(entry.pinned&&!entry.card.contains(ev.target))entry.closeSelf(false,false);});
    });
    document.addEventListener('keydown',function(ev){if(ev.key!=='Escape')return;entries.forEach(function(entry){if(entry.card.classList.contains('scope-open'))entry.closeSelf(false,canHover());});});

    if(window.lucide&&typeof window.lucide.createIcons==='function'){
      try{window.lucide.createIcons({attrs:{'stroke-width':1.8,'stroke-linecap':'round','stroke-linejoin':'round','aria-hidden':'true'}});}catch(e){}
    }
  }
  if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',bootShopPackageScopes819,{once:true});else bootShopPackageScopes819();
})();
