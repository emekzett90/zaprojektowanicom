<?php
if (!defined('ABSPATH')) { exit; }

$zp_contact_ajax = admin_url('admin-ajax.php');
$zp_contact_nonce = wp_create_nonce('zp_suite_contact');
$zp_contact_phone = preg_replace('/\D+/', '', zp_suite_opt('brand.phone', '501054253'));
if (!$zp_contact_phone) { $zp_contact_phone = '501054253'; }
$zp_contact_phone_link = '+48' . ltrim($zp_contact_phone, '0');
$zp_contact_email = zp_suite_opt('brand.admin_email', 'kontakt@zaprojektowani.com');

if (!function_exists('zp_suite_contact_page_html')) {
function zp_suite_contact_page_html($file){
  if (!file_exists($file)) { return current_user_can('manage_options') ? '<!-- Brak pliku: '.esc_html(basename($file)).' -->' : ''; }
  $html = file_get_contents($file);
  if ($html === false) { return ''; }
  return $html;
}
}


// Guarded: Rank Math renders every page for its image sitemap in one request (2.6.1).
if (!function_exists('zp_suite_strip_contact_visual_submit_bridge')) {
function zp_suite_strip_contact_visual_submit_bridge($html) {
  return preg_replace(
    '/\s*form\.addEventListener\(\s*[\'"]submit[\'"]\s*,\s*function\s*\(\s*e\s*\)\s*\{\s*if\s*\(\s*form\.getAttribute\(\s*[\'"]action[\'"]\s*\)\s*===\s*[\'"]#[\'"]\s*\)\s*\{\s*e\.preventDefault\(\s*\)\s*;\s*alert\([\s\S]*?\)\s*;\s*\}\s*\}\s*\)\s*;\s*/',
    "\n        /* submit obsługuje bridge AJAX z wtyczki */\n",
    $html
  );
}
}

$hero_html = zp_suite_contact_page_html(__DIR__ . '/contact-page/hero.html');
if (function_exists('zp_suite_mobile_hero_strip_video')) {
  $hero_html = zp_suite_mobile_hero_strip_video($hero_html);
}
if (function_exists('zp_suite_defer_hero_video_sources')) {
  $hero_html = zp_suite_defer_hero_video_sources($hero_html);
}
$form_html = zp_suite_contact_page_html(__DIR__ . '/contact-page/form.html');
$form_html = zp_suite_strip_contact_visual_submit_bridge($form_html);

// Hero: podpinamy scroll i telefon bez zmiany wyglądu.
$hero_html = str_replace('href="tel:+48500000000"', 'href="tel:+48501054253"', $hero_html);
$hero_html = str_replace('href="#formularz-kontaktowy"', 'href="#formularz-kontaktowy" data-zp-contact-scroll', $hero_html);

// Formularz: zostawiamy 1:1 wygląd, a submit obsługuje poniższy bridge JS.
$form_html = str_replace('action="#"', 'action="#" data-zp-contact-page-form="1"', $form_html);
$form_html = '<div id="formularz-kontaktowy" style="position:relative;top:-92px;height:0;pointer-events:none" aria-hidden="true"></div>' . $form_html;

?>
<div class="zpContactPageShortcode" data-zp-contact-page>
  <?php echo $hero_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
  <?php echo $form_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

  <div class="zpContactPageModal" data-zp-page-modal hidden aria-hidden="true">
    <div class="zpContactPageModal__backdrop" data-zp-page-modal-close></div>
    <div class="zpContactPageModal__box" role="dialog" aria-modal="true" aria-live="polite">
      <button class="zpContactPageModal__close" type="button" data-zp-page-modal-close aria-label="Zamknij komunikat">×</button>
      <span class="zpContactPageModal__icon"><i data-lucide="sparkles"></i></span>
      <h3 data-zp-page-modal-title>Komunikat</h3>
      <div data-zp-page-modal-text></div>
      <button class="zpContactPageModal__btn" type="button" data-zp-page-modal-close>Rozumiem</button>
    </div>
  </div>
</div>

<style>.zpContactPageShortcode{position:relative;isolation:isolate;overflow:visible!important}.zpContactPageShortcode.is-blurred > .zpWebHeroKat,.zpContactPageShortcode.is-blurred > .zpContactFormLight{filter: saturate(.86);transform:scale(.998);transition:filter .28s ease,transform .28s ease}.zpContactPageModal[hidden]{display:none!important}.zpContactPageModal{position:fixed;inset:0;z-index:999999;display:grid;place-items:center;padding:20px;font-family:"Plus Jakarta Sans Local",system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif}.zpContactPageModal__backdrop{position:absolute;inset:0;background:rgba(5,7,11,.45);backdrop-filter: none;-webkit-backdrop-filter:none saturate(120%)}.zpContactPageModal__box{position:relative;width:min(520px,100%);border-radius:30px;background:#fff;border:1px solid rgba(7,17,31,.12);box-shadow:0 38px 120px rgba(7,17,31,.28);padding:30px;color:#071426;transform:translateY(12px) scale(.97);opacity:0;animation:zpContactModalIn .34s cubic-bezier(.16,1,.3,1) forwards;text-align:left}@keyframes zpContactModalIn{to{transform:translateY(0) scale(1);opacity:1}}.zpContactPageModal__close{position:absolute;right:14px;top:14px;width:38px;height:38px;border:0;border-radius:999px;background:#f4f6f8;color:#071426;font-size:24px;line-height:1;cursor:pointer}.zpContactPageModal__icon{width:54px;height:54px;display:grid;place-items:center;border-radius:18px;background:linear-gradient(135deg,#06101e,#102a4f,#1c477a);color:#fff;margin:0 0 18px}.zpContactPageModal__icon svg{width:24px;height:24px;stroke:#fff!important;color:#fff!important}.zpContactPageModal__box h3{margin:0 40px 10px 0;color:#071426;font-size:clamp(24px,3vw,34px);line-height:1.05;letter-spacing:-.045em;font-weight:760}.zpContactPageModal__box p,.zpContactPageModal__box li{color:#5f6b7d;font-size:15px;line-height:1.58;margin:0 0 8px}.zpContactPageModal__box ul{margin:8px 0 0;padding-left:20px}.zpContactPageModal__btn{cursor:pointer;margin-top:18px;min-height:50px;display:inline-flex;align-items:center;justify-content:center;border-radius:999px;border:1px solid #071426;background:#071426;color:#fff;padding:0 22px;font-weight:800;letter-spacing:.02em}.zpContactPageModal.is-success .zpContactPageModal__icon{background:linear-gradient(135deg,#06101e,#102a4f,#1c477a)}.zpContactPageModal.is-error .zpContactPageModal__icon{background:linear-gradient(135deg,#24070b,#6b111f,#a32035)}.zpContactFormLight__submit.is-loading{pointer-events:none;opacity:.76}.zpContactFormLight__submit.is-loading span{opacity:.55}.zpContactFormLight__submit.is-loading:after{content:"";width:16px;height:16px;border-radius:50%;border:2px solid rgba(255,255,255,.35);border-top-color:#fff;animation:zpSpin .7s linear infinite;margin-left:2px}@keyframes zpSpin{to{transform:rotate(360deg)}}</style>

<script>
  (function(){
    var cfg = {
      ajaxUrl: <?php echo wp_json_encode($zp_contact_ajax); ?>,
      nonce: <?php echo wp_json_encode($zp_contact_nonce); ?>,
      email: <?php echo wp_json_encode($zp_contact_email); ?>,
      phone: <?php echo wp_json_encode($zp_contact_phone_link); ?>
    };
    window.zpSuiteContact = window.zpSuiteContact || {};
    window.zpSuiteContact.ajaxUrl = window.zpSuiteContact.ajaxUrl || cfg.ajaxUrl;
    window.zpSuiteContact.nonce = window.zpSuiteContact.nonce || cfg.nonce;
    window.zpSuiteContact.whatsapp = window.zpSuiteContact.whatsapp || '48501054253';

    function initContactPage(){
      var page = document.querySelector('[data-zp-contact-page]');
      var form = page && page.querySelector('#zpContactFormMain');
      var modal = page && page.querySelector('[data-zp-page-modal]');
      if(!page || !form || page.dataset.zpBridgeReady === '1') return;
      page.dataset.zpBridgeReady = '1';

      function icons(){
        if(window.lucide && typeof window.lucide.createIcons === 'function'){
          try{ window.lucide.createIcons({attrs:{'stroke-width':2,'stroke-linecap':'round','stroke-linejoin':'round','aria-hidden':'true'}}); }catch(e){}
        }
      }
      icons();
      window.addEventListener('load', icons, {once:true, passive:true});

      page.querySelectorAll('a[href="tel:+48500000000"], a[href="tel:+4850000000"], a[href*="500000000"]').forEach(function(a){ a.setAttribute('href','tel:+48501054253'); });
      page.querySelectorAll('[data-zp-contact-scroll], a[href="#formularz-kontaktowy"]').forEach(function(a){
        a.addEventListener('click', function(e){
          var target = document.getElementById('formularz-kontaktowy') || document.getElementById('zpContactFormLight');
          if(!target) return;
          e.preventDefault();
          var y = target.getBoundingClientRect().top + window.pageYOffset - 92;
          window.scrollTo({top:y, behavior:'smooth'});
        });
      });

      function escapeHtml(str){ return String(str || '').replace(/[<>&]/g,function(c){return {'<':'&lt;','>':'&gt;','&':'&amp;'}[c];}); }
      function showModal(title, html, type){
        if(!modal) return;
        modal.hidden = false;
        modal.setAttribute('aria-hidden','false');
        modal.classList.remove('is-success','is-error');
        modal.classList.add(type === 'success' ? 'is-success' : 'is-error');
        var t = modal.querySelector('[data-zp-page-modal-title]');
        var tx = modal.querySelector('[data-zp-page-modal-text]');
        if(t) t.textContent = title || 'Komunikat';
        if(tx) tx.innerHTML = html || '';
        page.classList.add('is-blurred');
        document.documentElement.classList.add('zpContactModalOpen');
        icons();
      }
      function hideModal(){
        if(!modal) return;
        modal.hidden = true;
        modal.setAttribute('aria-hidden','true');
        modal.classList.remove('is-success','is-error');
        page.classList.remove('is-blurred');
        document.documentElement.classList.remove('zpContactModalOpen');
      }
      page.querySelectorAll('[data-zp-page-modal-close]').forEach(function(btn){ btn.addEventListener('click', hideModal); });
      document.addEventListener('keydown', function(e){ if(e.key === 'Escape') hideModal(); });

      function currentModeKey(){ var checked = form.querySelector('input[name="contact_mode"]:checked'); return checked ? (checked.getAttribute('data-mode-value') || checked.value || 'quick') : 'quick'; }
      function normalizeMode(){
        var key = currentModeKey();
        if(key === 'call') return 'phone';
        if(key === 'brief') return 'brief';
        return 'quick';
      }
      function selectedServices(){
        var checked = Array.prototype.slice.call(form.querySelectorAll('input[name="services[]"]:checked'));
        return checked.map(function(i){ return i.value; }).filter(Boolean).join(', ');
      }
      function setRequired(){
        var mode = normalizeMode();
        var name = form.querySelector('[name="name"]');
        var phone = form.querySelector('[name="phone"]');
        var email = form.querySelector('[name="email"]');
        var msg = form.querySelector('[name="message"]');
        [name, phone, email, msg].forEach(function(el){ if(el) el.required = false; });
        if(name) name.required = true;
        if(mode === 'phone'){
          if(phone) phone.required = true;
        }else{
          if(email) email.required = false;
          if(msg) msg.required = true;
        }
      }
      function contactSourceLabel(){
        var path = window.location && window.location.pathname ? window.location.pathname : '/';
        if(path === '/' || path === '') return 'Strona główna — formularz kontaktowy';
        if(path.indexOf('/strony-internetowe-katowice/') !== -1) return 'Strony internetowe Katowice — formularz kontaktowy';
        if(path.indexOf('/sklepy-internetowe-katowice/') !== -1) return 'Sklepy internetowe Katowice — formularz kontaktowy';
        if(path.indexOf('/logo-branding-katowice/') !== -1) return 'Logo i branding Katowice — formularz kontaktowy';
        if(path.indexOf('/kontakt/') !== -1) return 'Kontakt — formularz kontaktowy';
        return (document.title ? document.title.replace(/\s+[–|-]\s+.*$/,'') : 'Zaprojektowani.com') + ' — formularz kontaktowy';
      }
      function pushContactSubmit(res, mode, services){
        try{
          window.dataLayer = window.dataLayer || [];
          var leadId = res && res.data && res.data.lead_id ? res.data.lead_id : '';
          var source = contactSourceLabel();
          window.dataLayer.push({
            event: 'zp_contact_form_submit',
            event_category: 'lead',
            event_label: source,
            form_name: 'global_footer_contact',
            form_id: form.getAttribute('id') || 'zpContactFormMain',
            lead_type: 'contact_form',
            contact_mode: mode || normalizeMode(),
            services: services || selectedServices(),
            source: source,
            page_path: window.location.pathname,
            page_location: window.location.href,
            lead_id: leadId,
            value: 250,
            currency: 'PLN'
          });
        }catch(err){}
      }

      form.addEventListener('change', setRequired);
      setRequired();

      form.addEventListener('submit', function(e){
        e.preventDefault();
        e.stopImmediatePropagation();

        var btn = form.querySelector('.zpContactFormLight__submit');
        var fd = new FormData(form);
        var mode = normalizeMode();
        fd.set('action','zp_suite_contact');
        fd.set('nonce', cfg.nonce);
        fd.set('contact_mode', mode);
        fd.set('consent', form.querySelector('input[name="consent"]:checked') ? '1' : '0');
        fd.set('services', selectedServices());
        fd.set('source', contactSourceLabel());
        fd.set('zp_feedback', 'modal'); // 2.8.0: okno „Dziękujemy” wystarczy, bez drugiego takiego samego komunikatu w rogu

        var callbackTime = form.querySelector('[name="callback_time"]');
        var callbackTopic = form.querySelector('[name="callback_topic"]');
        if(callbackTime) fd.set('callback_time', callbackTime.value || '');
        if(callbackTopic) fd.set('callback_topic', callbackTopic.value || '');

        if(btn) btn.classList.add('is-loading');
        (window.zpSuiteSecureFormData ? window.zpSuiteSecureFormData(fd) : Promise.resolve(fd))
          .then(function(secureFd){ return fetch(cfg.ajaxUrl, {method:'POST', credentials:'same-origin', body:secureFd}); })
          .then(function(r){ return r.json().catch(function(){ return {success:false,data:{title:'Błąd',messages:['Nie udało się odczytać odpowiedzi serwera.']}}; }); })
          .then(function(res){
            if(res && res.success){
              try{ sessionStorage.setItem('zpLeadSent','1'); }catch(err){}
              pushContactSubmit(res, mode, selectedServices());
              showModal(res.data.title || 'Dziękujemy', '<p>' + escapeHtml(res.data.message || 'Wiadomość została wysłana.') + '</p>', 'success');
              form.reset();
              try{
                var first = form.querySelector('input[name="contact_mode"][data-mode-value="quick"]');
                if(first){ first.checked = true; first.dispatchEvent(new Event('change', {bubbles:true})); }
              }catch(err){}
              if(window.zpContactFormLightUpdate){ try{ window.zpContactFormLightUpdate(); }catch(err){} }
            }else{
              var data = (res && res.data) || {};
              var msgs = data.messages || ['Sprawdź pola formularza i spróbuj ponownie.'];
              showModal(data.title || 'Uzupełnij dane', '<ul><li>'+msgs.map(escapeHtml).join('</li><li>')+'</li></ul>', 'error');
            }
          })
          .catch(function(){ showModal('Nie udało się wysłać wiadomości','<p>Sprawdź połączenie i spróbuj ponownie albo napisz na kontakt@zaprojektowani.com.</p>','error'); })
          .finally(function(){ if(btn) btn.classList.remove('is-loading'); });
      }, true);
    }
    if(document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initContactPage);
    else initContactPage();
  })();
</script>
