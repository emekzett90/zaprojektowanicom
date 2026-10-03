(function(){
    function initZpServicesPath(){
      var root = document.getElementById('zpServicesPath');
      if(!root || root.dataset.zpReady === '1') return;
      root.dataset.zpReady = '1';

      var cards = Array.prototype.slice.call(root.querySelectorAll('.zpServicesPath__item'));
      var reduced = false;

      try {
        reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
      } catch(e) {}

      if(window.lucide && typeof window.lucide.createIcons === 'function'){
        try {
          window.lucide.createIcons({
            attrs: {
              'stroke-width': 1.75,
              'stroke-linecap': 'round',
              'stroke-linejoin': 'round'
            }
          });
        } catch(e) {}
      }

      root.classList.add('is-ready');

      if(reduced){
        root.classList.add('is-inview');
        cards.forEach(function(card){
          card.style.setProperty('--drift', '0');
        });
        return;
      }

      function reveal(){
        root.classList.add('is-inview');
      }

      if('IntersectionObserver' in window){
        var io = new IntersectionObserver(function(entries){
          entries.forEach(function(entry){
            if(entry.isIntersecting){
              reveal();
              io.disconnect();
            }
          });
        }, {
          threshold: 0.16,
          rootMargin: '0px 0px -8% 0px'
        });

        io.observe(root);
      } else {
        reveal();
      }

      var ticking = false;
      var lastProgress = -1;
      var mobileLimit = 760;
      var nearViewport = true;
      var mobileReset = false;
      if('IntersectionObserver' in window){
        var activityObserver = new IntersectionObserver(function(entries){
          nearViewport = entries[0].isIntersecting;
          if(nearViewport) requestUpdate();
        }, {rootMargin:'200px 0px'});
        activityObserver.observe(root);
      }

      function clamp(n, min, max){
        return Math.min(Math.max(n, min), max);
      }

      function smoothstep(t){
        t = clamp(t, 0, 1);
        return t * t * (3 - 2 * t);
      }

      function updateScrollDrift(){
        ticking = false;

        var vw = window.innerWidth || document.documentElement.clientWidth;
        var isMobile = vw <= mobileLimit;
        if(isMobile){
          if(!mobileReset){
            cards.forEach(function(card){card.style.setProperty('--drift','0');card.style.pointerEvents='';});
            root.style.setProperty('--zp-section-progress','0');
            root.classList.remove('is-scrolled');
            lastProgress = -1;
            mobileReset = true;
          }
          return;
        }
        mobileReset = false;
        if(!nearViewport || document.hidden) return;

        var rect = root.getBoundingClientRect();
        var vh = window.innerHeight || document.documentElement.clientHeight;

        var start = vh * 0.06;
        var distance = Math.max(1, rect.height * 1.72);
        var raw = (-rect.top + start) / distance;
        var progress = clamp(raw, 0, 1);

        if(Math.abs(progress - lastProgress) < 0.0018 && !isMobile) return;
        lastProgress = progress;

        root.style.setProperty('--zp-section-progress', progress.toFixed(4));
        root.classList.toggle('is-scrolled', progress > 0.16);

        cards.forEach(function(card, index){
          var startAt = 0.42 + (index * 0.105);
          var endAt = startAt + 0.58;
          var cardRaw = (progress - startAt) / (endAt - startAt);
          var drift = smoothstep(cardRaw);

          card.style.setProperty('--drift', drift.toFixed(4));

          if(drift > 0.965){
            card.style.pointerEvents = 'none';
          }else{
            card.style.pointerEvents = '';
          }
        });
      }

      function requestUpdate(){
        if(ticking || document.hidden) return;
        if((window.innerWidth || document.documentElement.clientWidth) <= mobileLimit && mobileReset) return;
        if((window.innerWidth || document.documentElement.clientWidth) > mobileLimit && !nearViewport) return;
        ticking = true;
        window.requestAnimationFrame(updateScrollDrift);
      }

      window.addEventListener('scroll', requestUpdate, { passive: true });
      window.addEventListener('resize', requestUpdate, { passive: true });
      window.addEventListener('orientationchange', function(){
        setTimeout(requestUpdate, 140);
      }, { passive: true });

      requestUpdate();

      setTimeout(requestUpdate, 250);
      setTimeout(requestUpdate, 800);
    }

    function bootWhenNear(){
      var root = document.getElementById('zpServicesPath');
      if(!root || root.dataset.zpReady === '1') return;

      if('IntersectionObserver' in window){
        var starter = new IntersectionObserver(function(entries){
          entries.forEach(function(entry){
            if(entry.isIntersecting){
              starter.disconnect();
              initZpServicesPath();
            }
          });
        }, {
          threshold: 0,
          rootMargin: '900px 0px 900px 0px'
        });
        starter.observe(root);
        return;
      }

      if('requestIdleCallback' in window){
        requestIdleCallback(initZpServicesPath, { timeout: 1400 });
      }else{
        setTimeout(initZpServicesPath, 450);
      }
    }

    if(document.readyState === 'loading'){
      document.addEventListener('DOMContentLoaded', bootWhenNear);
    }else{
      bootWhenNear();
    }
  })();
