(function(){
  'use strict';

  function setupReveal(root){
    if (!root || root.dataset.zpRevealReady === '1') return;
    root.dataset.zpRevealReady = '1';

    var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (reduceMotion || !('IntersectionObserver' in window)) {
      root.classList.add('is-inview');
      return;
    }

    root.classList.add('is-reveal-ready');
    var observer = new IntersectionObserver(function(entries){
      entries.forEach(function(entry){
        if (!entry.isIntersecting) return;
        requestAnimationFrame(function(){ root.classList.add('is-inview'); });
        observer.unobserve(root);
      });
    }, {threshold:0.13, rootMargin:'0px 0px -8% 0px'});
    observer.observe(root);
  }

  function initFeaturedPackages(root){
    if (!root || root.dataset.zpReady === '1') return;
    root.dataset.zpReady = '1';
    setupReveal(root);

    var card = root.querySelector('#zpWebShopCard');
    if (!card) return;

    var buttons = Array.prototype.slice.call(card.querySelectorAll('.zpFeaturedSwitch button'));
    var title = card.querySelector('#zpWebShopTitle');
    var sub = card.querySelector('#zpWebShopSub');
    var desc = card.querySelector('#zpWebShopDesc');
    var best = card.querySelector('#zpWebShopBest');
    var list = card.querySelector('#zpWebShopList');
    var label = card.querySelector('#zpWebShopLabel');
    var cta = card.querySelector('#zpWebShopCta');
    var ctaText = card.querySelector('#zpWebShopCtaText');
    var more = card.querySelector('#zpWebShopMore');
    var badge = card.querySelector('#zpWebShopBadge');

    var data = {
      web: {
        title: 'Strona Premium',
        sub: 'Rozbudowana struktura • SEO • konwersja',
        label: 'Strony internetowe',
        badge: 'Najczęstszy wybór',
        desc: 'Dla firm, które chcą mocniej wejść w Google, lepiej pokazać ofertę i regularnie pozyskiwać zapytania ze strony.',
        best: 'Strona ma pozycjonować markę, sprzedawać ofertę i pracować długofalowo.',
        items: ['rozbudowana struktura pod SEO','copywriting i premium UX/UI','WordPress + szybka wersja mobile','analityka i układ pod zapytania'],
        cta: 'Wybieram Stronę Premium',
        href: 'https://zaprojektowani.com/studio-wyceny/?zpbs_package=Strona+Premium&zpbs_service=web&zpbs_source=home-featured',
        more: 'Zobacz wszystkie pakiety stron',
        moreHref: 'https://zaprojektowani.com/strony-internetowe-katowice/'
      },
      shop: {
        title: 'Sklep Rozwojowy',
        sub: 'UX premium • automatyzacje • analityka • SEO',
        label: 'Sklepy internetowe',
        badge: 'Najlepszy wzrost',
        desc: 'Dla marek, które chcą lepszego UX zakupowego, marketingu, SEO, automatyzacji i mocniejszego fundamentu pod dalszą sprzedaż.',
        best: 'Sklep ma nie tylko działać, ale aktywnie wspierać wzrost i późniejsze kampanie.',
        items: ['dopracowane karty produktów i koszyk','SEO, analityka i piksele reklamowe','kupony, automatyzacje i remarketing','WooCommerce gotowy do rozwoju'],
        cta: 'Wybieram Sklep Rozwojowy',
        href: 'https://zaprojektowani.com/studio-wyceny/?zpbs_package=Sklep+Rozwojowy&zpbs_service=shop&zpbs_step=1&zpbs_source=home-featured',
        more: 'Zobacz wszystkie pakiety sklepów',
        moreHref: 'https://zaprojektowani.com/sklepy-internetowe-katowice/'
      }
    };

    function setMode(mode){
      var d = data[mode];
      if (!d) return;

      buttons.forEach(function(button){
        var active = button.dataset.mode === mode;
        button.classList.toggle('is-active', active);
        button.setAttribute('aria-selected', active ? 'true' : 'false');
      });

      title.textContent = d.title;
      sub.textContent = d.sub;
      label.textContent = d.label;
      badge.textContent = d.badge;
      desc.textContent = d.desc;
      best.textContent = d.best;
      list.innerHTML = d.items.map(function(item){
        var li = document.createElement('li');
        li.textContent = item;
        return li.outerHTML;
      }).join('');
      ctaText.textContent = d.cta;
      cta.href = d.href;
      more.textContent = d.more;
      more.href = d.moreHref;
    }

    buttons.forEach(function(button){
      button.addEventListener('click', function(){ setMode(button.dataset.mode); });
    });
  }

  function boot(){
    document.querySelectorAll('.zpHomeFeaturedPackages').forEach(initFeaturedPackages);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot, {once:true});
  } else {
    boot();
  }
})();
