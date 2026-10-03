/* ZAPROJEKTOWANI — FAQ HUB v2.2.719 */
(function(){
  function normalize(str){
    return (str || '').toString().toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g,'').replace(/ł/g,'l');
  }

  function initFaq(root){
    if(!root || root.__zpFaqReady) return;
    root.__zpFaqReady = true;

    var search = root.querySelector('[data-faq-search]');
    var items = Array.prototype.slice.call(root.querySelectorAll('[data-faq-item]'));
    var cats = Array.prototype.slice.call(root.querySelectorAll('[data-faq-category]'));
    var counter = root.querySelector('[data-faq-counter]');
    var noResults = root.querySelector('[data-faq-no-results]');

    function applySearch(){
      var q = normalize(search ? search.value : '');
      var visible = 0;

      items.forEach(function(item){
        var text = normalize(item.textContent);
        var show = !q || text.indexOf(q) !== -1;
        item.hidden = !show;
        if(show) visible++;
      });

      cats.forEach(function(cat){
        var hasVisible = Array.prototype.slice.call(cat.querySelectorAll('[data-faq-item]')).some(function(item){ return !item.hidden; });
        cat.hidden = !hasVisible;
      });

      if(counter){
        counter.textContent = q ? (visible === 1 ? '1 pasująca odpowiedź' : visible + ' pasujących odpowiedzi') : 'Wszystkie pytania';
      }

      if(noResults){
        noResults.classList.toggle('isVisible', visible === 0);
      }
    }

    if(search){
      search.addEventListener('input', applySearch);

      document.addEventListener('keydown', function(e){
        if(e.key === '/' && document.activeElement !== search && !/input|textarea|select/i.test((document.activeElement || {}).tagName || '')){
          e.preventDefault();
          search.focus();
        }
      });
    }

    root.querySelectorAll('a[href^="#faq-"]').forEach(function(anchor){
      anchor.addEventListener('click', function(e){
        var target = root.querySelector(this.getAttribute('href'));
        if(!target) return;
        e.preventDefault();
        target.scrollIntoView({behavior:'smooth', block:'start'});
        history.replaceState(null, '', this.getAttribute('href'));
      });
    });

    if(window.location.hash && window.location.hash.indexOf('#faq-') === 0){
      setTimeout(function(){
        var target = root.querySelector(window.location.hash);
        if(target) target.scrollIntoView({behavior:'smooth', block:'start'});
      }, 250);
    }

    if(window.lucide && typeof window.lucide.createIcons === 'function'){
      try{ window.lucide.createIcons({attrs:{'stroke-width':1.8}}); }catch(e){}
    }

    applySearch();
  }

  function boot(){
    document.querySelectorAll('[data-zp-faq-page]').forEach(initFaq);
  }

  if(document.readyState === 'loading'){
    document.addEventListener('DOMContentLoaded', boot, {once:true});
  }else{
    boot();
  }
})();
