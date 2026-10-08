<?php
if (!defined('ABSPATH')) { exit; }

/**
 * ZP Suite — polished floating UX widgets v1.5.4
 * - two equal navy circular buttons
 * - refined online callback modal with Mateusz photo
 * - hard navy overrides for unwanted pink focus/active styles
 */

add_action('wp_head', function(){
  if (is_admin()) return;
  ?>
  <style id="zp-suite-polished-floating-and-no-pink-css">:root{--zpNavy:#071426;--zpNavy2:#0b1830;--zpNavy3:#102a4f;--zpBlue:#1c477a;--zpGlass:rgba(255,255,255,.86);--zpGradNavy:linear-gradient(135deg,#05070b 0%,#071426 48%,#102a4f 100%);--zpGradNavyWide:linear-gradient(90deg,#05070b 0%,#071426 42%,#102a4f 72%,#1c477a 100%)}html body [class^="zp"] :is(a,button,input,textarea,select):focus,html body [class*=" zp"] :is(a,button,input,textarea,select):focus,html body [class^="zp"] :is(a,button,input,textarea,select):focus-visible,html body [class*=" zp"] :is(a,button,input,textarea,select):focus-visible{outline:none!important;border-color:rgba(16,42,79,.48)!important;box-shadow:0 0 0 4px rgba(16,42,79,.10)!important;caret-color:#102a4f!important;accent-color:#102a4f!important}html body .zpContactSystemLight__service.is-active,html body .zpContactSystemLight__service.is-active:hover{outline:0!important;box-shadow:inset 0 1px 0 rgba(255,255,255,.18),0 18px 42px rgba(7,20,38,.14)!important;border-color:rgba(16,42,79,.58)!important;background:linear-gradient(135deg,#05070b 0%,#071426 48%,#102a4f 100%)!important;color:#fff!important}html body .zpContactSystemLight__tab.is-active,html body .zpContactSystemLight__tab.is-active:hover{outline:0!important;box-shadow:inset 0 1px 0 rgba(255,255,255,.82)!important;border-color:rgba(7,20,38,.12)!important;background:#fff!important;color:#071426!important}html body .zpContactSystemLight__tab.is-active span,html body .zpContactSystemLight__tab.is-active span::before,html body .zpContactSystemLight__service.is-active .zpContactSystemLight__serviceCheck,html body .zpContactSystemLight__submit,html body .zpContactSystemLight__btn,html body .zpContactModal__icon,html body .zpContactModal__btn{border-color:transparent!important}html body .zpContactSystemLight__submit,html body .zpContactSystemLight__btn--primary,html body .zpContactModal__btn{background:var(--zpGradNavyWide)!important;color:#fff!important}html body .zpContactSystemLight *{accent-color:#102a4f!important}html body .zpContactSystemLight input[type="checkbox"]:checked,html body .zpContactSystemLight input[type="radio"]:checked{background-color:#102a4f!important;border-color:#102a4f!important}.zpFloatUx,.zpFloatUx *,.zpFloatUx *::before,.zpFloatUx *::after,.zpMiniChatOverlay,.zpMiniChatOverlay *,.zpMiniChatOverlay *::before,.zpMiniChatOverlay *::after{box-sizing:border-box!important}.zpFloatUx{--font:"Plus Jakarta Sans Local",system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;position:fixed!important;right:22px!important;bottom:24px!important;z-index:2147483000!important;display:flex!important;flex-direction:column!important;gap:12px!important;align-items:flex-end!important;pointer-events:none!important;font-family:var(--font)!important;-webkit-font-smoothing:antialiased!important;text-rendering:geometricPrecision!important}.zpFloatUx__btn{all:unset!important;pointer-events:auto!important;width:58px!important;height:58px!important;min-width:58px!important;min-height:58px!important;border-radius:999px!important;display:grid!important;place-items:center!important;position:relative!important;cursor:pointer!important;color:#fff!important;background:var(--zpGradNavy)!important;border:1px solid rgba(255,255,255,.16)!important;box-shadow:0 18px 46px rgba(7,20,38,.24),inset 0 1px 0 rgba(255,255,255,.12)!important;backdrop-filter: none;-webkit-backdrop-filter: none;transform:translateZ(0)!important;transition:transform .22s cubic-bezier(.16,1,.3,1),box-shadow .22s ease,filter .22s ease,opacity .22s ease!important;isolation:isolate!important;overflow:visible!important;text-decoration:none!important;-webkit-tap-highlight-color:transparent!important}.zpFloatUx__btn::before{content:""!important;position:absolute!important;inset:-1px!important;z-index:-1!important;border-radius:inherit!important;background:radial-gradient(circle at 24% 0%,rgba(255,255,255,.18),transparent 38%),linear-gradient(135deg,rgba(255,255,255,.08),rgba(255,255,255,0))!important;opacity:.72!important;pointer-events:none!important}.zpFloatUx__btn:hover,.zpFloatUx__btn:focus-visible{transform:translate3d(0,-3px,0)!important;filter:saturate(1.08) brightness(1.04)!important;box-shadow:0 22px 54px rgba(7,20,38,.30),inset 0 1px 0 rgba(255,255,255,.16)!important;outline:none!important;border-color:rgba(255,255,255,.22)!important}.zpFloatUx__btn svg{width:23px!important;height:23px!important;display:block!important;stroke:currentColor!important;stroke-width:1.95!important}.zpFloatUx__top{opacity:0!important;transform:translate3d(0,10px,0) scale(.94)!important;pointer-events:none!important}.zpFloatUx__top.is-visible{opacity:1!important;transform:translate3d(0,0,0) scale(1)!important;pointer-events:auto!important}.zpFloatUx__chat .zpFloatUx__avatar{width:58px!important;height:58px!important;display:grid!important;place-items:center!important;position:relative!important;color:#fff!important}.zpFloatUx__chat .zpFloatUx__avatar::after{content:""!important;position:absolute!important;right:8px!important;bottom:8px!important;width:12px!important;height:12px!important;border-radius:999px!important;background:#22c55e!important;border:3px solid #071426!important;box-shadow:0 0 0 5px rgba(34,197,94,.16)!important}.zpFloatUx__txt{display:none!important}.zpFloatUx__tooltip{position:absolute!important;right:68px!important;top:50%!important;transform:translate3d(8px,-50%,0)!important;opacity:0!important;pointer-events:none!important;white-space:nowrap!important;min-height:36px!important;display:inline-flex!important;align-items:center!important;padding:0 13px!important;border-radius:999px!important;background:rgba(7,20,38,.94)!important;color:#fff!important;font-size:12px!important;line-height:1!important;font-weight:750!important;letter-spacing:-.01em!important;box-shadow:0 14px 30px rgba(7,20,38,.20)!important;transition:opacity .2s ease,transform .2s ease!important}.zpFloatUx__btn:hover .zpFloatUx__tooltip{opacity:1!important;transform:translate3d(0,-50%,0)!important}.zpMiniChatOverlay{position:fixed!important;inset:0!important;z-index:2147483600!important;display:none!important;place-items:center!important;padding:20px!important;background:rgba(3,5,9,.62)!important;backdrop-filter: none;-webkit-backdrop-filter: none;font-family:"Plus Jakarta Sans Local",system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif!important}.zpMiniChatOverlay.is-open{display:grid!important}.zpMiniChat{width:min(920px,100%)!important;min-height:520px!important;display:grid!important;grid-template-columns:minmax(0,.92fr) minmax(340px,1fr)!important;border-radius:34px!important;overflow:hidden!important;background:#fff!important;color:#071426!important;border:1px solid rgba(255,255,255,.22)!important;box-shadow:0 44px 120px rgba(0,0,0,.42)!important;transform:translateZ(0)!important;animation:zpMiniChatIn .34s cubic-bezier(.16,1,.3,1) both!important}@keyframes zpMiniChatIn{from{opacity:0;transform:translate3d(0,18px,0) scale(.975);filter:none}to{opacity:1;transform:translate3d(0,0,0) scale(1);filter:none}}.zpMiniChat__visual{position:relative!important;min-height:100%!important;background:radial-gradient(circle at 20% 0%,rgba(28,71,122,.34),transparent 36%),linear-gradient(135deg,#05070b 0%,#071426 52%,#102a4f 100%)!important;color:#fff!important;padding:30px!important;overflow:hidden!important;display:flex!important;flex-direction:column!important;justify-content:space-between!important;isolation:isolate!important}.zpMiniChat__visual::before{content:""!important;position:absolute!important;left:-18%!important;right:-18%!important;bottom:-18%!important;height:54%!important;background:radial-gradient(ellipse at 50% 100%,rgba(28,71,122,.44),transparent 64%)!important;filter: none;pointer-events:none!important;z-index:-1!important}.zpMiniChat__status{display:inline-flex!important;align-items:center!important;gap:10px!important;width:max-content!important;font-size:10px!important;line-height:1!important;text-transform:uppercase!important;letter-spacing:.14em!important;color:rgba(255,255,255,.72)!important;font-weight:850!important}.zpMiniChat__status::before{content:""!important;width:11px!important;height:11px!important;border-radius:999px!important;background:#22c55e!important;box-shadow:0 0 0 7px rgba(34,197,94,.13)!important}.zpMiniChat__visual h3{margin:22px 0 0!important;max-width:410px!important;font-size:clamp(38px,4.4vw,62px)!important;line-height:.92!important;letter-spacing:-.066em!important;font-weight:560!important;color:#fff!important}.zpMiniChat__visual p{margin:16px 0 0!important;max-width:390px!important;color:rgba(255,255,255,.72)!important;font-size:14px!important;line-height:1.62!important;letter-spacing:-.01em!important}.zpMiniChat__person{position:absolute!important;right:-22px!important;bottom:-22px!important;width:min(310px,48%)!important;height:auto!important;display:block!important;pointer-events:none!important;filter:saturate(1.02) contrast(1.03)!important;-webkit-mask-image:linear-gradient(180deg,#000 0%,#000 72%,rgba(0,0,0,.72) 84%,transparent 100%)!important;mask-image:linear-gradient(180deg,#000 0%,#000 72%,rgba(0,0,0,.72) 84%,transparent 100%)!important}.zpMiniChat__chips{display:flex!important;flex-wrap:wrap!important;gap:8px!important;max-width:300px!important;position:relative!important;z-index:2!important}.zpMiniChat__chips span{min-height:28px!important;padding:0 10px!important;border-radius:999px!important;display:inline-flex!important;align-items:center!important;background:rgba(255,255,255,.08)!important;border:1px solid rgba(255,255,255,.12)!important;color:rgba(255,255,255,.78)!important;font-size:10px!important;font-weight:750!important;letter-spacing:.02em!important}.zpMiniChat__formWrap{position:relative!important;background:linear-gradient(180deg,#fff 0%,#f8fafc 100%)!important;padding:32px!important;display:flex!important;flex-direction:column!important;justify-content:center!important}.zpMiniChat__close{all:unset!important;position:absolute!important;right:18px!important;top:18px!important;width:42px!important;height:42px!important;border-radius:999px!important;display:grid!important;place-items:center!important;background:#eef2f7!important;color:#071426!important;cursor:pointer!important;font-size:26px!important;line-height:1!important;border:1px solid rgba(7,20,38,.08)!important}.zpMiniChat__formKicker{margin:0 0 8px!important;font-size:10px!important;line-height:1!important;text-transform:uppercase!important;letter-spacing:.14em!important;color:#657083!important;font-weight:850!important}.zpMiniChat__formTitle{margin:0 0 20px!important;max-width:360px!important;color:#071426!important;font-size:clamp(28px,3.2vw,42px)!important;line-height:.98!important;letter-spacing:-.056em!important;font-weight:560!important}.zpMiniChat form{display:grid!important;gap:13px!important;padding:0!important;margin:0!important}.zpMiniChat label{display:grid!important;gap:7px!important;color:#667085!important;font-size:10px!important;line-height:1!important;text-transform:uppercase!important;letter-spacing:.13em!important;font-weight:850!important}.zpMiniChat input,.zpMiniChat textarea{width:100%!important;border-radius:17px!important;border:1px solid #dbe3ee!important;background:#fff!important;color:#071426!important;padding:15px 16px!important;font-size:14px!important;line-height:1.2!important;font-weight:500!important;outline:none!important;box-shadow:inset 0 1px 0 rgba(255,255,255,.82)!important;-webkit-appearance:none!important;appearance:none!important}.zpMiniChat textarea{min-height:104px!important;resize:vertical!important}.zpMiniChat input:focus,.zpMiniChat textarea:focus{border-color:rgba(16,42,79,.45)!important;box-shadow:0 0 0 4px rgba(16,42,79,.10),inset 0 1px 0 rgba(255,255,255,.92)!important}.zpMiniChat__submit{all:unset!important;min-height:54px!important;border-radius:999px!important;display:flex!important;align-items:center!important;justify-content:center!important;gap:9px!important;background:var(--zpGradNavyWide)!important;color:#fff!important;font-weight:850!important;font-size:14px!important;letter-spacing:-.01em!important;cursor:pointer!important;margin-top:4px!important;box-shadow:0 18px 36px rgba(7,20,38,.16)!important}.zpMiniChat__submit:hover{filter:saturate(1.08) brightness(1.04)!important}.zpMiniChat__note{margin:0!important;color:#747f90!important;font-size:11px!important;line-height:1.48!important}.zpMiniChat__msg{display:none!important;margin:0 0 12px!important;padding:12px 14px!important;border-radius:16px!important;background:#eef6ff!important;color:#102a4f!important;border:1px solid #d9eafd!important;font-size:13px!important;line-height:1.35!important;font-weight:750!important}.zpMiniChat__msg.is-show{display:block!important}@media(max-width:900px){.zpMiniChat{grid-template-columns:1fr!important;width:min(520px,100%)!important;max-height:calc(100vh - 24px)!important;overflow:auto!important}.zpMiniChat__visual{min-height:260px!important}.zpMiniChat__person{width:210px!important;right:-18px!important}.zpMiniChat__chips{max-width:240px!important}.zpMiniChat__formWrap{padding:26px!important}}@media(max-width:760px){.zpFloatUx{right:12px!important;bottom:calc(92px + env(safe-area-inset-bottom))!important;gap:10px!important}.zpFloatUx__btn{width:52px!important;height:52px!important;min-width:52px!important;min-height:52px!important}.zpFloatUx__btn svg{width:21px!important;height:21px!important}.zpFloatUx__chat .zpFloatUx__avatar{width:52px!important;height:52px!important}.zpFloatUx__tooltip{display:none!important}.zpMiniChatOverlay{padding:10px!important}.zpMiniChat{border-radius:26px!important}.zpMiniChat__visual{padding:24px!important}.zpMiniChat__visual h3{font-size:40px!important}.zpMiniChat__visual p{font-size:13px!important}.zpMiniChat__formWrap{padding:22px!important}.zpMiniChat__formTitle{font-size:31px!important}}</style>
  <?php
}, 3);

add_action('wp_footer', function(){
  if (is_admin()) return;
  $ajax = admin_url('admin-ajax.php');
  $nonce = wp_create_nonce('zp_suite_contact');
  ?>
  <div class="zpFloatUx" aria-label="Szybki kontakt i powrót do góry">
    <button class="zpFloatUx__btn zpFloatUx__top" type="button" data-zp-backtop aria-label="Wróć do góry">
      <i data-lucide="arrow-up"></i>
      <span class="zpFloatUx__tooltip">Wróć do góry</span>
    </button>
    <button class="zpFloatUx__btn zpFloatUx__chat" type="button" data-zp-mini-chat-open aria-label="Otwórz szybki kontakt">
      <span class="zpFloatUx__avatar"><i data-lucide="message-circle"></i></span>
      <span class="zpFloatUx__txt"><small>online</small><strong>Zapytaj o projekt</strong></span>
      <span class="zpFloatUx__tooltip">Zapytaj o projekt</span>
    </button>
  </div>

  <div class="zpMiniChatOverlay" data-zp-mini-chat aria-hidden="true">
    <div class="zpMiniChat" role="dialog" aria-modal="true" aria-labelledby="zpMiniChatTitle">
      <div class="zpMiniChat__visual">
        <div>
          <span class="zpMiniChat__status">Jesteśmy online</span>
          <h3 id="zpMiniChatTitle">Chcesz, żebyśmy oddzwonili?</h3>
          <p>Zostaw numer telefonu i krótką informację o projekcie. Oddzwonimy z konkretną propozycją dalszego kroku.</p>
        </div>
        <div class="zpMiniChat__chips" aria-hidden="true">
          <span>strony www</span><span>sklepy</span><span>branding</span><span>Meta Ads</span>
        </div>
        <img class="zpMiniChat__person" src="https://zaprojektowani.com/wp-content/uploads/2026/05/mateusz_nowy.webp" alt="" loading="lazy" decoding="async">
      </div>
      <div class="zpMiniChat__formWrap">
        <button class="zpMiniChat__close" type="button" data-zp-mini-chat-close aria-label="Zamknij">×</button>
        <p class="zpMiniChat__formKicker">Szybka konsultacja</p>
        <h4 class="zpMiniChat__formTitle">Opisz krótko projekt — wrócimy z propozycją.</h4>
        <form data-zp-mini-chat-form>
          <p class="zpMiniChat__msg" data-zp-mini-chat-msg></p>
          <label>Telefon
            <input type="tel" name="phone" autocomplete="tel" placeholder="Twój numer telefonu" required>
          </label>
          <label>Imię / firma
            <input type="text" name="name" autocomplete="name" placeholder="Jak się do Ciebie zwracać?">
          </label>
          <label>Krótko o projekcie
            <textarea name="message" placeholder="Strona, sklep, logo, branding, kampania..."></textarea>
          </label>
          <input type="hidden" name="email" value="">
          <input type="hidden" name="services" value="Mini chat / prośba o telefon">
          <input type="hidden" name="contact_mode" value="phone">
          <input type="hidden" name="consent" value="1">
          <button class="zpMiniChat__submit" type="submit">Poproś o telefon <span aria-hidden="true">→</span></button>
          <p class="zpMiniChat__note">Dane wykorzystamy wyłącznie do odpowiedzi w sprawie projektu.</p>
        </form>
      </div>
    </div>
  </div>

  <script id="zp-suite-floating-widgets-js">
  (function(){
    var ajaxUrl=<?php echo wp_json_encode($ajax); ?>;
    var nonce=<?php echo wp_json_encode($nonce); ?>;
    function boot(){
    var topBtn=document.querySelector('[data-zp-backtop]');
    var openBtn=document.querySelector('[data-zp-mini-chat-open]');
    var overlay=document.querySelector('[data-zp-mini-chat]');
    var closeBtn=document.querySelector('[data-zp-mini-chat-close]');
    var form=document.querySelector('[data-zp-mini-chat-form]');
    var msg=document.querySelector('[data-zp-mini-chat-msg]');

    function refreshTop(){
      if(!topBtn) return;
      topBtn.classList.toggle('is-visible', (window.scrollY||0) > 580);
    }
    window.addEventListener('scroll', refreshTop, {passive:true}); refreshTop();
    if(topBtn){ topBtn.addEventListener('click', function(){ window.scrollTo({top:0, behavior:'smooth'}); }); }

    function open(){ if(overlay){ overlay.classList.add('is-open'); overlay.setAttribute('aria-hidden','false'); document.documentElement.classList.add('zp-mini-chat-open'); setTimeout(function(){ var i=overlay.querySelector('input[name="phone"]'); if(i) i.focus(); },120); } }
    function close(){ if(overlay){ overlay.classList.remove('is-open'); overlay.setAttribute('aria-hidden','true'); document.documentElement.classList.remove('zp-mini-chat-open'); } }
    if(openBtn){ openBtn.addEventListener('click', open); }
    if(closeBtn){ closeBtn.addEventListener('click', close); }
    if(overlay){ overlay.addEventListener('click', function(e){ if(e.target===overlay) close(); }); }
    document.addEventListener('keydown', function(e){ if(e.key==='Escape') close(); });

    if(form){
      form.addEventListener('submit', function(e){
        e.preventDefault();
        if(msg){ msg.className='zpMiniChat__msg is-show'; msg.textContent='Wysyłamy prośbę o telefon...'; }
        var fd=new FormData(form);
        fd.append('action','zp_suite_contact');
        fd.append('nonce',nonce);
        (window.zpSuiteSecureFormData?window.zpSuiteSecureFormData(fd):Promise.resolve(fd)).then(function(secureFd){return fetch(ajaxUrl,{method:'POST',body:secureFd,credentials:'same-origin'});}).then(function(r){return r.json();}).then(function(res){
          if(res && res.success){
            if(msg){ msg.textContent='Dziękujemy — zgłoszenie zapisane. Odezwemy się możliwie szybko.'; }
            form.reset();
            setTimeout(close,2800);
          }else{
            var m='Nie udało się wysłać. Sprawdź numer telefonu i spróbuj ponownie.';
            if(res && res.data && res.data.messages){ m=res.data.messages.join(' '); }
            if(msg){ msg.textContent=m; }
          }
        }).catch(function(){ if(msg){ msg.textContent='Wystąpił problem z połączeniem. Spróbuj ponownie za chwilę.'; } });
      });
    }

    if(window.zpSuiteInitLucide){ setTimeout(window.zpSuiteInitLucide,80); }
    }
    // v2.2.547: below-the-fold / interaction-driven widget — hydrate after load+idle so it adds
    // no parse-time work to the first render. Back-to-top only matters after the user scrolls,
    // and the chat opens on click, so a short idle delay is invisible to real users.
    function sched(){ if('requestIdleCallback' in window){ window.requestIdleCallback(boot,{timeout:3000}); } else { window.setTimeout(boot,800); } }
    if(document.readyState==='complete'){ sched(); } else { window.addEventListener('load',sched,{once:true,passive:true}); }
  })();
  </script>
  <?php
}, 98);
