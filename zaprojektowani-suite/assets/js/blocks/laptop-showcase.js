(function(){
    function initZpLaptopShowcase(){
      var root = document.getElementById('zpLaptopShowcase');
      if (!root || root.dataset.zpReady === '1') return;
      root.dataset.zpReady = '1';

      root.classList.add('is-animatable');

      if (window.lucide && typeof window.lucide.createIcons === 'function') {
        try {
          window.lucide.createIcons({
            attrs: {
              'stroke-width': 1.85,
              'stroke-linecap': 'round',
              'stroke-linejoin': 'round'
            }
          });
        } catch(e) {}
      }

      var reduced = false;
      var mobileMq = null;

      try {
        reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        mobileMq = window.matchMedia('(max-width: 760px)');
      } catch(e) {}

      function isMobile(){
        return !!(mobileMq && mobileMq.matches);
      }

      function show(){
        root.classList.add('is-visible');
      }

      function clearOutro(){
        root.classList.remove(
          'is-outro',
          'is-outro-1',
          'is-outro-2',
          'is-outro-3',
          'is-outro-4',
          'is-outro-5',
          'is-outro-6'
        );
      }

      if (reduced) {
        show();
        clearOutro();
        return;
      }

      if ('IntersectionObserver' in window) {
        var observer = new IntersectionObserver(function(entries){
          entries.forEach(function(entry){
            if (entry.isIntersecting) {
              show();
              observer.disconnect();
            }
          });
        }, {
          threshold: 0.16,
          rootMargin: '0px 0px -8% 0px'
        });

        observer.observe(root);
      } else {
        show();
      }

      var ticking = false;

      function clamp(n, min, max){
        return Math.min(Math.max(n, min), max);
      }

      function setClass(name, state){
        if (state) {
          root.classList.add(name);
        } else {
          root.classList.remove(name);
        }
      }

      function updateOutro(){
        ticking = false;

        if (!root.classList.contains('is-visible')) return;

        /*
          Mobile: nie używamy outro/chowania elementów.
          To usuwa skoki przy przewijaniu i zostawia stabilną sekcję.
        */
        if (isMobile()) {
          clearOutro();
          return;
        }

        var rect = root.getBoundingClientRect();

        /*
          Desktop: start dużo później niż w v1.2, więc sekcja dłużej zostaje czytelna.
          Blur/opacity są też osłabione w CSS.
        */
        var raw = (-rect.top - rect.height * 0.62) / Math.max(1, rect.height * 0.72);
        var progress = clamp(raw, 0, 1);

        setClass('is-outro', progress > 0.12);
        setClass('is-outro-1', progress > 0.32);
        setClass('is-outro-2', progress > 0.43);
        setClass('is-outro-3', progress > 0.54);
        setClass('is-outro-4', progress > 0.65);
        setClass('is-outro-5', progress > 0.76);
        setClass('is-outro-6', progress > 0.87);

        if (progress <= 0.10) {
          clearOutro();
        }
      }

      function requestUpdate(){
        if (ticking) return;
        ticking = true;
        window.requestAnimationFrame(updateOutro);
      }

      window.addEventListener('scroll', requestUpdate, { passive: true });
      window.addEventListener('resize', requestUpdate, { passive: true });
      window.addEventListener('orientationchange', function(){
        clearOutro();
        setTimeout(requestUpdate, 160);
      }, { passive: true });

      if (mobileMq && typeof mobileMq.addEventListener === 'function') {
        mobileMq.addEventListener('change', function(){
          clearOutro();
          requestUpdate();
        });
      } else if (mobileMq && typeof mobileMq.addListener === 'function') {
        mobileMq.addListener(function(){
          clearOutro();
          requestUpdate();
        });
      }

      requestUpdate();
    }

    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', initZpLaptopShowcase);
    } else {
      initZpLaptopShowcase();
    }
  })();
