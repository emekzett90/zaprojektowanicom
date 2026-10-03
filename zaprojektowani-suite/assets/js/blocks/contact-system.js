(function(){
  function initZpContactSystemLight(){
    var root = document.getElementById('zpContactSystemLight');
    if(!root || root.dataset.zpReady === '1') return;
    root.dataset.zpReady = '1';
    root.classList.add('is-animatable');

    var services = Array.prototype.slice.call(root.querySelectorAll('.zpContactSystemLight__service'));
    var tabs = Array.prototype.slice.call(root.querySelectorAll('[data-contact-tab]'));
    var panels = Array.prototype.slice.call(root.querySelectorAll('[data-contact-panel]'));
    var selectedEls = Array.prototype.slice.call(root.querySelectorAll('[data-zp-contact-selected], [data-zp-contact-selected-wa]'));
    var waLink = root.querySelector('[data-zp-contact-wa]');
    var form = root.querySelector('[data-zp-contact-form]');
    var notice = root.querySelector('[data-zp-contact-notice]');
    var demoBtns = Array.prototype.slice.call(root.querySelectorAll('[data-zp-contact-demo]'));
    var phoneNumber = (window.zpSuiteContact && window.zpSuiteContact.whatsapp) ? window.zpSuiteContact.whatsapp : '48501054253';
    var modal = root.querySelector('[data-zp-contact-modal]');
    var modalTitle = root.querySelector('[data-zp-contact-modal-title]');
    var modalText = root.querySelector('[data-zp-contact-modal-text]');
    var modalClose = Array.prototype.slice.call(root.querySelectorAll('[data-zp-contact-modal-close]'));

    function createIcons(){
      if(window.lucide && typeof window.lucide.createIcons === 'function'){
        try{ window.lucide.createIcons({attrs:{'stroke-width':1.85,'stroke-linecap':'round','stroke-linejoin':'round'}}); }catch(e){}
      }
    }

    function getSelected(){
      var selected = services.filter(function(btn){ return btn.classList.contains('is-active'); }).map(function(btn){ return btn.getAttribute('data-service'); }).filter(Boolean);
      return selected.length ? selected : ['Jeszcze nie wybrano usługi'];
    }

    function updateSelected(){
      var text = getSelected().join(', ');
      selectedEls.forEach(function(el){ el.textContent = text; });
      if(waLink){
        var message = 'Cześć, chciałbym porozmawiać o projekcie. Interesuje mnie: ' + text + '.';
        waLink.href = 'https://wa.me/' + phoneNumber + '?text=' + encodeURIComponent(message);
      }
    }

    function showModal(title, html, type){
      if(!modal) return;
      modalTitle.textContent = title || 'Komunikat';
      modalText.innerHTML = html || '';
      modal.hidden = false;
      modal.classList.add('is-visible', type === 'success' ? 'is-success' : 'is-error');
      modal.setAttribute('aria-hidden','false');
      root.classList.add('is-blurred');
      document.documentElement.classList.add('zpContactModalOpen');
      createIcons();
    }

    function hideModal(){
      if(!modal) return;
      modal.classList.remove('is-visible','is-success','is-error');
      modal.setAttribute('aria-hidden','true');
      modal.hidden = true;
      root.classList.remove('is-blurred');
      document.documentElement.classList.remove('zpContactModalOpen');
    }

    modalClose.forEach(function(btn){ btn.addEventListener('click', hideModal); });
    document.addEventListener('keydown', function(e){ if(e.key === 'Escape') hideModal(); });

    services.forEach(function(btn){
      btn.addEventListener('click', function(){
        btn.classList.toggle('is-active');
        updateSelected();
        var isMobile = false; try { isMobile = window.matchMedia('(max-width: 760px)').matches; } catch(e) {}
        if(!isMobile){ try { btn.animate([{transform:'translateY(0) scale(1)'},{transform:'translateY(-2px) scale(.985)'},{transform:'translateY(0) scale(1)'}],{duration:230,easing:'cubic-bezier(.16,1,.3,1)'}); } catch(e) {} }
      });
    });

    tabs.forEach(function(tab){
      tab.addEventListener('click', function(){
        var target = tab.getAttribute('data-contact-tab');
        tabs.forEach(function(item){ var active=item===tab; item.classList.toggle('is-active', active); item.setAttribute('aria-selected', active ? 'true' : 'false'); });
        panels.forEach(function(panel){ var activePanel=panel.getAttribute('data-contact-panel') === target; panel.classList.toggle('is-active', activePanel); panel.hidden = !activePanel; });
        createIcons();
      });
    });

    if(form){
      form.addEventListener('submit', function(e){
        e.preventDefault();
        var btn = form.querySelector('.zpContactSystemLight__btn');
        var fd = new FormData(form);
        fd.append('action','zp_suite_contact');
        fd.set('nonce', (window.zpSuiteContact && window.zpSuiteContact.nonce) || fd.get('zp_nonce') || '');
        fd.set('services', getSelected().join(', '));
        fd.set('consent', form.querySelector('input[name="consent"]:checked') ? '1' : '0');

        if(btn) btn.classList.add('is-loading');
        if(notice){ notice.classList.remove('is-visible'); notice.textContent = ''; }

        (window.zpSuiteSecureFormData ? window.zpSuiteSecureFormData(fd) : Promise.resolve(fd))
          .then(function(secureFd){ return fetch((window.zpSuiteContact && window.zpSuiteContact.ajaxUrl) || '/wp-admin/admin-ajax.php', {method:'POST', credentials:'same-origin', body:secureFd}); })
          .then(function(r){ return r.json().catch(function(){ return {success:false,data:{title:'Błąd',messages:['Nie udało się odczytać odpowiedzi serwera.']}}; }); })
          .then(function(res){
            if(res && res.success){
              showModal(res.data.title || 'Dziękujemy', '<p>'+(res.data.message || 'Wiadomość została wysłana.')+'</p>', 'success');
              form.reset();
              services.forEach(function(s,i){ s.classList.toggle('is-active', i===0 || i===2); });
              updateSelected();
            } else {
              var data = (res && res.data) || {};
              var msgs = data.messages || ['Sprawdź pola formularza i spróbuj ponownie.'];
              showModal(data.title || 'Uzupełnij dane', '<ul><li>'+msgs.map(function(m){return String(m).replace(/[<>&]/g,function(c){return {'<':'&lt;','>':'&gt;','&':'&amp;'}[c];});}).join('</li><li>')+'</li></ul>', 'error');
            }
          })
          .catch(function(){ showModal('Nie udało się wysłać wiadomości','<p>Sprawdź połączenie i spróbuj ponownie albo napisz na kontakt@zaprojektowani.com.</p>','error'); })
          .finally(function(){ if(btn) btn.classList.remove('is-loading'); });
      });
    }

    function escapeHtml(str){
      return String(str || '').replace(/[<>&]/g,function(c){return {'<':'&lt;','>':'&gt;','&':'&amp;'}[c];});
    }

    var phoneRequestBtn = root.querySelector('[data-zp-phone-request]');
    if(phoneRequestBtn){
      phoneRequestBtn.addEventListener('click', function(){
        var panel = root.querySelector('[data-contact-panel="phone"]');
        var name = panel && panel.querySelector('[data-zp-phone-name]') ? panel.querySelector('[data-zp-phone-name]').value.trim() : '';
        var phone = panel && panel.querySelector('[data-zp-phone-input]') ? panel.querySelector('[data-zp-phone-input]').value.trim() : '';
        var message = panel && panel.querySelector('[data-zp-phone-message]') ? panel.querySelector('[data-zp-phone-message]').value.trim() : '';
        if(!phone){
          showModal('Podaj numer telefonu', '<p>Wpisz numer, na który mamy oddzwonić. Możesz też dopisać krótką notatkę o projekcie.</p>', 'error');
          return;
        }
        var fd = new FormData();
        fd.append('action','zp_suite_contact');
        fd.append('nonce', (window.zpSuiteContact && window.zpSuiteContact.nonce) || '');
        fd.append('name', name || 'Prośba o telefon');
        fd.append('phone', phone);
        fd.append('email', '');
        fd.append('services', getSelected().join(', '));
        fd.append('message', message || 'Prośba o telefon z sekcji kontaktowej.');
        fd.append('consent', '1');
        fd.append('contact_mode', 'phone');

        phoneRequestBtn.classList.add('is-loading');
        (window.zpSuiteSecureFormData ? window.zpSuiteSecureFormData(fd) : Promise.resolve(fd))
          .then(function(secureFd){ return fetch((window.zpSuiteContact && window.zpSuiteContact.ajaxUrl) || '/wp-admin/admin-ajax.php', {method:'POST', credentials:'same-origin', body:secureFd}); })
          .then(function(r){ return r.json().catch(function(){ return {success:false,data:{title:'Błąd',messages:['Nie udało się odczytać odpowiedzi serwera.']}}; }); })
          .then(function(res){
            if(res && res.success){
              showModal(res.data.title || 'Przyjęliśmy prośbę o telefon', '<p>'+(res.data.message || 'Oddzwonimy możliwie szybko.')+'</p>', 'success');
              if(panel){
                var fields = panel.querySelectorAll('input, textarea');
                fields.forEach(function(el){ el.value = ''; });
              }
            }else{
              var data = (res && res.data) || {};
              var msgs = data.messages || ['Sprawdź numer telefonu i spróbuj ponownie.'];
              showModal(data.title || 'Uzupełnij dane', '<ul><li>'+msgs.map(escapeHtml).join('</li><li>')+'</li></ul>', 'error');
            }
          })
          .catch(function(){
            showModal('Nie udało się wysłać prośby', '<p>Sprawdź połączenie albo napisz na kontakt@zaprojektowani.com.</p>', 'error');
          })
          .finally(function(){ phoneRequestBtn.classList.remove('is-loading'); });
      });
    }

    demoBtns.forEach(function(btn){ btn.addEventListener('click', function(){ showModal('Zostaw numer w formularzu', '<p>Najpewniejsza ścieżka to zakładka Formularz — wpisz telefon i krótki opis, a wrócimy z odpowiedzią.</p>', 'success'); }); });

    createIcons(); updateSelected();

    var reduced = false, mobileMq = null;
    try { reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches; mobileMq = window.matchMedia('(max-width: 760px)'); } catch(e) {}
    function isMobile(){ return !!(mobileMq && mobileMq.matches); }
    function show(){ root.classList.add('is-visible'); }
    function clearOutro(){ root.classList.remove('is-outro'); }
    if(reduced){ show(); clearOutro(); return; }
    if('IntersectionObserver' in window){ var observer = new IntersectionObserver(function(entries){ entries.forEach(function(entry){ if(entry.isIntersecting){ show(); observer.disconnect(); } }); }, {threshold:0.14,rootMargin:'0px 0px -8% 0px'}); observer.observe(root); } else { show(); }
    var ticking = false; function clamp(n,min,max){ return Math.min(Math.max(n,min),max); } function setClass(name,state){ state ? root.classList.add(name) : root.classList.remove(name); }
    function updateOutro(){ ticking=false; if(!root.classList.contains('is-visible')) return; if(isMobile()){ clearOutro(); return; } var rect=root.getBoundingClientRect(); var raw=(-rect.top - rect.height*.62)/Math.max(1, rect.height*.70); var progress=clamp(raw,0,1); setClass('is-outro', progress>.14); if(progress<=.10) clearOutro(); }
    function requestUpdate(){ if(ticking) return; ticking=true; window.requestAnimationFrame(updateOutro); }
    window.addEventListener('scroll', function(){if(!isMobile() && !document.hidden) requestUpdate();}, {passive:true}); window.addEventListener('resize', requestUpdate, {passive:true}); window.addEventListener('orientationchange', function(){ clearOutro(); setTimeout(requestUpdate,160); }, {passive:true});
    if(mobileMq && typeof mobileMq.addEventListener === 'function'){ mobileMq.addEventListener('change', function(){ clearOutro(); requestUpdate(); }); } else if(mobileMq && typeof mobileMq.addListener === 'function'){ mobileMq.addListener(function(){ clearOutro(); requestUpdate(); }); }
    requestUpdate();
  }
  if(document.readyState === 'loading'){ document.addEventListener('DOMContentLoaded', initZpContactSystemLight); } else { initZpContactSystemLight(); }
})();
