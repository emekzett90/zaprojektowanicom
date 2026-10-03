(function(){
  function initZpMegaFooter(){
    var root = document.getElementById('zpMegaFooter');
    if(!root || root.dataset.zpReady === '1') return;
    root.dataset.zpReady = '1';

    function createIcons(){
      if(window.lucide && typeof window.lucide.createIcons === 'function'){
        try{
          window.lucide.createIcons({
            attrs: {
              'stroke-width': 1.75,
              'stroke-linecap': 'round',
              'stroke-linejoin': 'round'
            }
          });
        }catch(e){}
      }
    }

    var reduced = false;

    try {
      reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    } catch(e) {}

    function activate(){
      if(root.dataset.zpActivated === '1') return;
      root.dataset.zpActivated = '1';
      root.classList.add('is-animatable');

      var year = root.querySelector('[data-zp-year]');
      if(year){
        year.textContent = String(new Date().getFullYear());
      }

      var topBtn = root.querySelector('[data-zp-footer-top]');
      if(topBtn){
        topBtn.addEventListener('click', function(){
          try{
            window.scrollTo({
              top: 0,
              behavior: 'smooth'
            });
          }catch(e){
            window.scrollTo(0, 0);
          }
        });
      }

      var cookieSettings = root.querySelector('[data-zp-cookie-settings]');
      if(cookieSettings){
        cookieSettings.addEventListener('click', function(event){
          if(window.Cookiebot && typeof window.Cookiebot.show === 'function'){
            event.preventDefault();
            window.Cookiebot.show();
            return;
          }
          if(window.Cookiebot && typeof window.Cookiebot.renew === 'function'){
            event.preventDefault();
            window.Cookiebot.renew();
          }
        });
      }

      var postsRail = root.querySelector('[data-zp-footer-posts]');
      var postsPrev = root.querySelector('[data-zp-footer-posts-prev]');
      var postsNext = root.querySelector('[data-zp-footer-posts-next]');
      function scrollPosts(dir){
        if(!postsRail) return;
        var first = postsRail.querySelector('.zpMegaFooter__post');
        var step = first ? Math.round(first.getBoundingClientRect().width + 12) : Math.round(postsRail.clientWidth * 0.86);
        try{ postsRail.scrollBy({left: dir * step, behavior: 'smooth'}); }
        catch(e){ postsRail.scrollLeft += dir * step; }
      }
      if(postsPrev) postsPrev.addEventListener('click', function(){ scrollPosts(-1); });
      if(postsNext) postsNext.addEventListener('click', function(){ scrollPosts(1); });

      createIcons();
      window.addEventListener('load', createIcons, { once: true });
    }

    function show(){
      activate();
      root.classList.add('is-visible');
    }

    if(reduced){
      show();
      return;
    }

    if('IntersectionObserver' in window){
      var observer = new IntersectionObserver(function(entries){
        entries.forEach(function(entry){
          if(entry.isIntersecting){
            show();
            observer.disconnect();
          }
        });
      }, {
        threshold: 0.06,
        rootMargin: '0px 0px -8% 0px'
      });

      observer.observe(root);
    }else{
      show();
    }
  }

  if(document.readyState === 'loading'){
    document.addEventListener('DOMContentLoaded', initZpMegaFooter);
  }else{
    initZpMegaFooter();
  }
})();
