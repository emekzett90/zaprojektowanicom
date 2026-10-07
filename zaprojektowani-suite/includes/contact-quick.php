<?php
if (!defined('ABSPATH')) { exit; }

/**
 * ZP Suite 2.8.0 — krótki formularz kontaktowy w środku strony.
 *
 * Mat (7.10.2026): obok długiego formularza z wyborem „Oddzwońcie / Szybki kontakt / Pełny brief”
 * ma być krótka wersja: imię, telefon albo e-mail i temat jednym kliknięciem. Po podglądzie Mat
 * wolał ją niżej niż zaraz pod hero, ale nie przy samym dole (tam jest pełny formularz) i bez cienia,
 * więc sekcja stoi pod pakietami z cenami. Wysyła do tego samego handlera co długi formularz
 * (zp_suite_contact, tryb "short"), więc zgłoszenie trafia do Leadów, na maila i do zdarzeń Meta/GTM
 * tak samo jak dotąd.
 *
 * Gdzie: strona główna (sekcja „Krótki formularz kontaktowy” w kolejności sekcji, po „Polecanych
 * pakietach”), podstrony zbudowane na szablonach stron, sklepów i logo (ogólnopolskie, katowickie,
 * branżowe; bez pakietów: po drugiej sekcji pod hero) oraz /kampanie-reklamowe/. Włączniki:
 * Zaprojektowani Suite → Widoczność sekcji → „Krótki formularz kontaktowy — strona główna”
 * i „— podstrony usług i kampanie”.
 */

/** Strona główna ma własny włącznik, podstrony usług i kampanie wspólny drugi. */
function zp_suite_quick_contact_enabled(string $context = 'home'): bool {
  $key = $context === 'home' ? 'visibility.quick_contact' : 'visibility.quick_contact_pages';
  $on = (string) zp_suite_opt($key, '1') !== '0';
  return (bool) apply_filters('zp_suite_quick_contact_enabled', $on, $context);
}

/** Temat => [napis na przycisku, wartość wysyłana w polu services]. */
function zp_suite_quick_contact_topics(): array {
  return [
    'strony'   => ['Strona www', 'Strona internetowa'],
    'sklepy'   => ['Sklep', 'Sklep internetowy'],
    'logo'     => ['Logo i branding', 'Logo i branding'],
    'kampanie' => ['Kampanie', 'Kampanie reklamowe'],
    'inne'     => ['Coś innego', 'Inny temat'],
  ];
}

/**
 * Sekcja z krótkim formularzem. $context: home | strony | sklepy | logo | kampanie
 * (na podstronach usług od razu zaznacza pasujący temat).
 */
function zp_suite_quick_contact_section(string $context = 'home'): string {
  static $rendered = false;
  if ($rendered || !zp_suite_quick_contact_enabled($context)) { return ''; }
  $rendered = true;

  $topics = zp_suite_quick_contact_topics();
  $preset = isset($topics[$context]) ? $context : '';
  $nonce = wp_create_nonce('zp_suite_contact');
  $min_ms = zp_suite_opt('security.min_seconds_enabled', '1') === '0' ? 0 : (int) round(max(0, (float) zp_suite_opt('security.min_seconds', '6')) * 1000);
  $img = ZP_SUITE_URL . 'assets/img/quick-contact/';
  $people = ['marta' => 'Marta', 'mateusz' => 'Mateusz', 'stanislaw' => 'Stanisław'];
  $full_form = in_array($context, ['home', 'strony', 'sklepy', 'logo', 'kampanie'], true) ? '#formularz-kontaktowy' : home_url('/kontakt/');

  ob_start();
  ?>
<section class="zpQuick" id="szybki-kontakt" aria-labelledby="zpQuickTitle" data-zp-quick data-context="<?php echo esc_attr($context); ?>">
  <div class="zpQuick__inner">
    <div class="zpQuick__card">
      <div class="zpQuick__intro">
        <span class="zpQuick__kicker">Szybki kontakt</span>
        <p class="zpQuick__title" id="zpQuickTitle">Zostaw kontakt w 20 sekund. Resztę ustalimy w rozmowie.</p>
        <div class="zpQuick__people">
          <span class="zpQuick__faces" aria-hidden="true">
            <?php foreach ($people as $file => $name) : ?><img src="<?php echo esc_url($img . $file . '.webp'); ?>" alt="" width="40" height="40" loading="lazy" decoding="async"><?php endforeach; ?>
          </span>
          <p>Odezwie się Marta, Mateusz lub Stanisław, zwykle w ciągu 1 dnia roboczego.</p>
        </div>
      </div>

      <form class="zpQuick__form" data-zp-quick-form data-method="phone" data-min-ms="<?php echo (int) $min_ms; ?>" novalidate>
        <input type="hidden" name="nonce" value="<?php echo esc_attr($nonce); ?>">
        <div class="zpQuick__fields">
          <div class="zpQuick__field" data-quick-field="name">
            <label class="zpQuick__label" for="zpQuickName">Imię</label>
            <input id="zpQuickName" name="name" type="text" autocomplete="given-name" placeholder="np. Anna" maxlength="80" aria-describedby="zpQuickNameErr">
            <em class="zpQuick__error" id="zpQuickNameErr" aria-live="polite"></em>
          </div>
          <div class="zpQuick__field zpQuick__field--contact" data-quick-field="contact">
            <div class="zpQuick__labelRow">
              <label class="zpQuick__label" for="zpQuickPhone" data-quick-show="phone">Telefon</label>
              <label class="zpQuick__label" for="zpQuickEmail" data-quick-show="email">E-mail</label>
              <button type="button" class="zpQuick__switch" data-quick-switch>
                <span data-quick-show="phone">wolę e-mail</span><span data-quick-show="email">wolę telefon</span>
              </button>
            </div>
            <input id="zpQuickPhone" name="phone" type="tel" inputmode="tel" autocomplete="tel" placeholder="np. 500 600 700" maxlength="24" data-quick-show="phone" aria-describedby="zpQuickContactErr">
            <input id="zpQuickEmail" name="email" type="email" inputmode="email" autocomplete="email" placeholder="np. anna@firma.pl" maxlength="120" data-quick-show="email" aria-describedby="zpQuickContactErr">
            <em class="zpQuick__error" id="zpQuickContactErr" aria-live="polite"></em>
          </div>
          <button class="zpQuick__submit" type="submit">
            <span class="zpQuick__submitText">Poproś o kontakt</span><span class="zpQuick__submitBusy">Wysyłamy…</span>
            <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
          </button>
        </div>

        <fieldset class="zpQuick__topics">
          <legend>Czego dotyczy?</legend>
          <?php foreach ($topics as $key => $topic) : ?>
            <label><input type="radio" name="topic" value="<?php echo esc_attr($topic[1]); ?>"<?php checked($preset, $key); ?>><span><?php echo esc_html($topic[0]); ?></span></label>
          <?php endforeach; ?>
        </fieldset>

        <p class="zpQuick__alert" role="alert" hidden></p>
        <div class="zpQuick__foot">
          <p class="zpQuick__note">Bez zobowiązań. Wysyłając, zgadzasz się na kontakt w sprawie zapytania (<a href="<?php echo esc_url(home_url('/polityka-prywatnosci/')); ?>" target="_blank" rel="noopener">polityka prywatności</a>).</p>
          <p class="zpQuick__more">Wolisz opisać projekt dokładniej? <a href="<?php echo esc_url($full_form); ?>">Pełny formularz</a></p>
        </div>
      </form>

      <div class="zpQuick__done" data-zp-quick-done hidden tabindex="-1">
        <span class="zpQuick__doneIcon" aria-hidden="true"><svg viewBox="0 0 24 24" focusable="false"><path d="M5 12.5l4.2 4.2L19 7"/></svg></span>
        <div class="zpQuick__doneBody">
          <p class="zpQuick__doneTitle" data-done-title>Dziękujemy! Zgłoszenie dotarło.</p>
          <p class="zpQuick__doneText" data-done-text>Odezwiemy się zwykle w ciągu 1 dnia roboczego.</p>
          <p class="zpQuick__doneNote">Pilna sprawa? Zadzwoń: <a href="tel:+48501054253">501 054 253</a>.</p>
          <p class="zpQuick__doneNote">Chcesz od razu opisać projekt? <a href="<?php echo esc_url($full_form); ?>">Pełny formularz</a></p>
        </div>
      </div>

      <div class="zpQuick__msgs" hidden>
        <span data-msg="name">Wpisz imię, żebyśmy wiedzieli, jak się do Ciebie zwracać.</span>
        <span data-msg="phone">Wpisz numer telefonu, np. 500 600 700.</span>
        <span data-msg="email">Wpisz adres e-mail, np. anna@firma.pl.</span>
        <span data-msg="network">Nie udało się wysłać. Sprawdź połączenie i spróbuj ponownie albo zadzwoń: 501 054 253.</span>
        <span data-msg="done-title">Dziękujemy, %s! Zgłoszenie dotarło.</span>
        <span data-msg="done-phone">Oddzwonimy na numer %s, zwykle w ciągu 1 dnia roboczego.</span>
        <span data-msg="done-email">Odpiszemy na adres %s, zwykle w ciągu 1 dnia roboczego.</span>
      </div>
    </div>
  </div>
</section>
<?php
  return ob_get_clean() . zp_suite_quick_contact_assets();
}

function zp_suite_quick_contact_assets(): string {
  $ajax = admin_url('admin-ajax.php');
  $css = <<<'CSS'
html body .zpQuick{--qInk:#05070b;--qNavy:#071426;--qMuted:#667386;--qLine:rgba(7,17,31,.105);--qGrad:linear-gradient(135deg,#020407 0%,#05070b 42%,#071426 72%,#102a4f 100%);--qGradHover:linear-gradient(135deg,#05070b 0%,#071426 48%,#102a4f 100%);position:relative!important;display:block!important;z-index:2;margin:-1px 0!important;padding:calc(clamp(20px,2.6vw,40px) + 1px) 0!important;background:#fff!important;color:var(--qInk)!important;font-family:"Plus Jakarta Sans Local",system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif!important;overflow:visible!important;scroll-margin-top:110px}
html body .zpQuick *,html body .zpQuick *::before,html body .zpQuick *::after{box-sizing:border-box}
/* Margines -1px: sekcja nachodzi na sąsiednie, więc na granicy w ułamku piksela nie prześwituje ciemne tło podstrony (cienka czarna linia). */
/* Strona główna odkłada render sekcji poza ekranem (content-visibility) z miejscem 980px; ta sekcja jest niższa. */
html body .zpHomeContent > #szybki-kontakt{contain-intrinsic-size:auto 1px auto 360px!important}
html body .zpQuick__inner{width:min(1700px,calc(100% - 96px))!important;margin:0 auto!important;padding:0!important}
html body .zpQuick__card{position:relative;display:grid!important;grid-template-columns:minmax(260px,.82fr) minmax(0,2.18fr);gap:clamp(26px,3vw,48px);align-items:center;padding:clamp(22px,2.2vw,32px) clamp(22px,2.4vw,36px)!important;border:1px solid var(--qLine)!important;border-radius:34px!important;background:#fff!important;box-shadow:none!important}
html body .zpQuick__kicker{display:inline-flex!important;align-items:center;gap:10px;margin:0 0 14px!important;color:var(--qMuted)!important;font-size:10px!important;line-height:1!important;font-weight:800!important;letter-spacing:.145em!important;text-transform:uppercase!important}
html body .zpQuick__kicker::before{content:"";width:30px;height:1px;background:var(--qNavy)}
html body .zpQuick__title{margin:0!important;max-width:460px;color:var(--qInk)!important;font-size:clamp(24px,2.05vw,31px)!important;line-height:1.08!important;letter-spacing:-.045em!important;font-weight:650!important;text-wrap:balance}
html body .zpQuick__people{display:flex!important;align-items:center;gap:12px;margin-top:18px!important}
html body .zpQuick__faces{display:flex!important;flex:0 0 auto}
html body .zpQuick__faces img{display:block!important;width:40px!important;height:40px!important;max-width:none!important;margin:0 0 0 -10px!important;border:2px solid #fff!important;border-radius:50%!important;background:#e9eef5!important;box-shadow:0 6px 16px rgba(7,17,31,.12)!important;object-fit:cover}
html body .zpQuick__faces img:first-child{margin-left:0!important}
html body .zpQuick__people p{margin:0!important;max-width:300px;color:var(--qMuted)!important;font-size:12.5px!important;line-height:1.45!important;font-weight:560!important;letter-spacing:-.01em!important}
html body .zpQuick__form{margin:0!important;padding:0!important;min-width:0}
html body .zpQuick__fields{display:grid!important;grid-template-columns:minmax(0,1fr) minmax(0,1.2fr) auto;gap:12px;align-items:start}
html body .zpQuick__field{position:relative;display:grid!important;gap:8px;min-width:0;margin:0!important}
html body .zpQuick__labelRow{display:flex!important;align-items:baseline;justify-content:space-between;gap:10px;min-height:12px}
html body .zpQuick__label{display:block!important;margin:0!important;padding:0!important;color:#4d5969!important;font-size:12px!important;line-height:1!important;font-weight:800!important;letter-spacing:.035em!important;text-transform:none!important}
html body .zpQuick__switch,html body .zpQuick__switch:hover,html body .zpQuick__switch:focus,html body .zpQuick__switch:active{display:inline-flex!important;align-items:center;min-height:0!important;margin:0!important;padding:0!important;border:0!important;border-radius:0!important;background:none!important;box-shadow:none!important;color:#1c477a!important;font:inherit!important;font-size:12px!important;line-height:1!important;font-weight:700!important;letter-spacing:0!important;text-decoration:underline!important;text-underline-offset:3px;text-transform:none!important;cursor:pointer;transform:none!important}
html body .zpQuick__switch:focus-visible{outline:2px solid #1c477a!important;outline-offset:3px}
html body .zpQuick input[type="text"],html body .zpQuick input[type="tel"],html body .zpQuick input[type="email"]{display:block;width:100%!important;max-width:none!important;min-height:58px!important;height:58px!important;margin:0!important;padding:0 18px!important;border:1px solid rgba(7,17,31,.12)!important;border-radius:18px!important;background:#fff!important;color:var(--qNavy)!important;box-shadow:none!important;outline:none!important;font-family:inherit!important;font-size:16px!important;line-height:1.3!important;font-weight:520!important;letter-spacing:-.01em!important;transition:border-color .22s ease,background .22s ease,box-shadow .22s ease}
html body .zpQuick input::placeholder{color:#9aa3af!important;opacity:1}
html body .zpQuick input:focus{border-color:rgba(7,20,38,.42)!important;background:#fbfcfe!important;box-shadow:0 0 0 4px rgba(7,20,38,.075)!important}
html body .zpQuick__form[data-method="phone"] [data-quick-show="email"],html body .zpQuick__form[data-method="email"] [data-quick-show="phone"]{display:none!important}
html body .zpQuick__field.is-invalid input{border-color:#c2410c!important;background:#fffaf7!important;box-shadow:0 0 0 4px rgba(194,65,12,.08)!important}
html body .zpQuick__error{display:none;margin:0!important;color:#b4380a!important;font-size:12px!important;line-height:1.35!important;font-style:normal!important;font-weight:650!important}
html body .zpQuick__field.is-invalid .zpQuick__error{display:block}
html body .zpQuick__submit,html body .zpQuick__submit:hover,html body .zpQuick__submit:focus,html body .zpQuick__submit:active{position:relative;isolation:isolate;overflow:hidden;align-self:end;display:inline-flex!important;align-items:center;justify-content:center;gap:10px;min-height:58px!important;height:58px!important;margin:0!important;padding:0 26px!important;border:1px solid rgba(7,20,38,.95)!important;border-radius:999px!important;background:var(--qGrad)!important;color:#fff!important;box-shadow:none;font-family:inherit!important;font-size:14px!important;line-height:1!important;font-weight:800!important;letter-spacing:-.01em!important;text-transform:none!important;white-space:nowrap;cursor:pointer;transition:transform .22s ease,box-shadow .22s ease,border-color .22s ease}
html body .zpQuick__submit::before{content:"";position:absolute;inset:0;z-index:-1;background:var(--qGradHover);opacity:0;transition:opacity .3s ease}
html body .zpQuick__submit:hover{transform:translateY(-2px)!important;border-color:#1c477a!important;box-shadow:0 20px 54px rgba(7,17,31,.18)!important}
html body .zpQuick__submit:hover::before{opacity:1}
html body .zpQuick__submit:focus-visible{outline:3px solid rgba(28,71,122,.45)!important;outline-offset:3px}
html body .zpQuick__submit svg{width:17px;height:17px;fill:none;stroke:#fff;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}
html body .zpQuick__submitBusy{display:none}
html body .zpQuick__form.is-sending .zpQuick__submitText{display:none}
html body .zpQuick__form.is-sending .zpQuick__submitBusy{display:inline}
html body .zpQuick__form.is-sending .zpQuick__submit{opacity:.82;pointer-events:none}
html body .zpQuick__topics{display:flex!important;flex-wrap:wrap;align-items:center;gap:8px;min-width:0;margin:16px 0 0!important;padding:0!important;border:0!important}
html body .zpQuick__topics legend{float:left;margin:0 6px 0 0!important;padding:0!important;color:#4d5969!important;font-size:12px!important;line-height:36px!important;font-weight:800!important;letter-spacing:.035em!important}
html body .zpQuick__topics label{position:relative;display:inline-flex!important;margin:0!important;cursor:pointer}
html body .zpQuick__topics input{position:absolute!important;opacity:0!important;width:1px!important;height:1px!important;pointer-events:none}
html body .zpQuick__topics span{display:inline-flex!important;align-items:center;min-height:36px;padding:0 15px!important;border:1px solid rgba(7,17,31,.10)!important;border-radius:999px!important;background:#f7f8fa!important;color:#344256!important;font-size:12.5px!important;line-height:1!important;font-weight:750!important;letter-spacing:-.01em!important;white-space:nowrap;transition:background .2s ease,color .2s ease,border-color .2s ease}
html body .zpQuick__topics label:hover span{border-color:rgba(16,42,79,.42)!important}
html body .zpQuick__topics input:checked + span{background:var(--qGrad)!important;border-color:rgba(16,42,79,.70)!important;color:#fff!important}
html body .zpQuick__topics input:focus-visible + span{outline:2px solid #1c477a;outline-offset:2px}
html body .zpQuick__alert{margin:14px 0 0!important;padding:12px 16px!important;border-radius:16px!important;background:#fff6f1!important;color:#9a3412!important;font-size:13px!important;line-height:1.45!important;font-weight:600!important}
html body .zpQuick__alert[hidden]{display:none!important}
html body .zpQuick__foot{display:flex!important;flex-wrap:wrap;align-items:baseline;justify-content:space-between;gap:6px 22px;margin-top:14px!important}
html body .zpQuick__foot p{margin:0!important;color:var(--qMuted)!important;font-size:12px!important;line-height:1.5!important;font-weight:520!important;letter-spacing:-.005em!important}
html body .zpQuick__foot a{color:var(--qNavy)!important;font-weight:750!important;text-decoration:underline!important;text-underline-offset:3px}
html body .zpQuick__more a::after{content:" ↓"}
html body .zpQuick__done{display:flex!important;align-items:flex-start;gap:20px;padding:clamp(22px,2vw,30px) clamp(22px,2.2vw,32px)!important;border:1px solid rgba(7,17,31,.07)!important;border-radius:26px!important;background:#f6f8fb!important;outline:none;animation:zpQuickDone .45s cubic-bezier(.2,.7,.2,1) both}
html body .zpQuick__done[hidden]{display:none!important}
html body .zpQuick__doneIcon{flex:0 0 52px;display:grid;place-items:center;width:52px;height:52px;border-radius:50%;background:var(--qGrad)}
html body .zpQuick__doneIcon svg{width:24px;height:24px;fill:none;stroke:#fff;stroke-width:2.4;stroke-linecap:round;stroke-linejoin:round}
html body .zpQuick__doneBody{min-width:0}
html body .zpQuick__doneTitle{margin:2px 0 8px!important;color:var(--qInk)!important;font-size:clamp(21px,1.7vw,26px)!important;line-height:1.15!important;letter-spacing:-.035em!important;font-weight:700!important;text-wrap:balance}
html body .zpQuick__doneText{margin:0!important;color:#344256!important;font-size:15px!important;line-height:1.55!important;font-weight:560!important;overflow-wrap:anywhere}
html body .zpQuick__doneNote{margin:10px 0 0!important;color:var(--qMuted)!important;font-size:13px!important;line-height:1.5!important}
html body .zpQuick__doneNote + .zpQuick__doneNote{margin-top:4px!important}
html body .zpQuick__doneNote a{color:var(--qNavy)!important;font-weight:750!important;text-decoration:underline!important;text-underline-offset:3px}
html body .zpQuick__doneNote a[href^="#"]::after{content:" ↓"}
@keyframes zpQuickDone{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:none}}
html body .zpQuick.is-done .zpQuick__form{display:none!important}
html body .zpQuick__msgs{display:none!important}
@media (max-width:1180px){
  html body .zpQuick__card{grid-template-columns:1fr;gap:22px}
  html body .zpQuick__title{max-width:640px}
}
@media (max-width:760px){
  html body .zpQuick{padding:15px 0!important}
  html body .zpHomeContent > #szybki-kontakt{contain-intrinsic-size:auto 1px auto 666px!important}
  html body .zpQuick__inner{width:calc(100% - 28px)!important}
  html body .zpQuick__card{padding:20px 18px 18px!important;border-radius:28px!important;gap:18px}
  html body .zpQuick__kicker{margin-bottom:12px!important}
  html body .zpQuick__title{font-size:clamp(23px,6.6vw,27px)!important;line-height:1.1!important}
  html body .zpQuick__people{margin-top:14px!important}
  html body .zpQuick__faces img{width:34px!important;height:34px!important;margin-left:-9px!important}
  html body .zpQuick__form{display:flex!important;flex-direction:column}
  html body .zpQuick__fields{display:contents!important}
  html body .zpQuick__field[data-quick-field="name"]{order:1;margin-bottom:14px!important}
  html body .zpQuick__field[data-quick-field="contact"]{order:2}
  html body .zpQuick__topics{order:3;margin-top:16px!important;gap:7px}
  html body .zpQuick__submit,html body .zpQuick__submit:hover,html body .zpQuick__submit:focus,html body .zpQuick__submit:active{order:4;width:100%!important;min-height:56px!important;height:56px!important;margin-top:18px!important}
  html body .zpQuick__alert{order:5}
  html body .zpQuick__foot{order:6;display:grid!important;gap:6px;margin-top:12px!important}
  html body .zpQuick input[type="text"],html body .zpQuick input[type="tel"],html body .zpQuick input[type="email"]{min-height:54px!important;height:54px!important}
  html body .zpQuick__topics legend{float:none;width:100%;margin:0 0 4px!important;line-height:1.2!important}
  html body .zpQuick__done{gap:14px;padding:20px 18px!important;border-radius:22px!important}
  html body .zpQuick__doneIcon{flex-basis:44px;width:44px;height:44px}
  html body .zpQuick__doneIcon svg{width:20px;height:20px}
  html body .zpQuick__doneTitle{font-size:20px!important}
  html body .zpQuick__doneText{font-size:14.5px!important}
}
@media (prefers-reduced-motion:reduce){html body .zpQuick *{transition:none!important}html body .zpQuick__done{animation:none!important}}
CSS;

  $js = <<<'JS'
(function(){
  'use strict';
  var root=document.querySelector('[data-zp-quick]');
  var form=root&&root.querySelector('[data-zp-quick-form]');
  if(!root||!form||root.dataset.ready==='1') return;
  root.dataset.ready='1';
  var done=root.querySelector('[data-zp-quick-done]');
  var alertBox=form.querySelector('.zpQuick__alert');
  var started=Date.now();
  function msg(k){ var el=root.querySelector('.zpQuick__msgs [data-msg="'+k+'"]'); return el?el.textContent.trim():''; }
  function field(name){ return form.querySelector('[data-quick-field="'+name+'"]'); }
  function setError(name,text){
    var f=field(name); if(!f) return;
    f.classList.toggle('is-invalid',!!text);
    var em=f.querySelector('.zpQuick__error'); if(em) em.textContent=text||'';
    f.querySelectorAll('input').forEach(function(i){ if(text) i.setAttribute('aria-invalid','true'); else i.removeAttribute('aria-invalid'); });
  }
  function method(){ return form.getAttribute('data-method')==='email'?'email':'phone'; }
  function input(name){ return form.querySelector('[name="'+name+'"]'); }
  function digits(v){ return String(v||'').replace(/\D+/g,''); }
  function check(){
    var ok=true, first=null;
    var name=input('name'), val=(name.value||'').trim();
    if(val.length<2){ setError('name',msg('name')); ok=false; first=first||name; } else setError('name','');
    if(method()==='phone'){
      var tel=input('phone'), d=digits(tel.value);
      if(d.length<9||d.length>15){ setError('contact',msg('phone')); ok=false; first=first||tel; } else setError('contact','');
    }else{
      var em=input('email'), ev=(em.value||'').trim();
      if(!/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(ev)){ setError('contact',msg('email')); ok=false; first=first||em; } else setError('contact','');
    }
    if(first){ try{ first.focus({preventScroll:true}); }catch(e){ first.focus(); } }
    return ok;
  }
  form.querySelectorAll('input').forEach(function(i){
    i.addEventListener('input',function(){ var f=i.closest('[data-quick-field]'); if(f&&f.classList.contains('is-invalid')) setError(f.getAttribute('data-quick-field'),''); });
  });
  var sw=form.querySelector('[data-quick-switch]');
  if(sw){ sw.addEventListener('click',function(){
    var next=method()==='phone'?'email':'phone';
    form.setAttribute('data-method',next);
    setError('contact','');
    var i=input(next==='email'?'email':'phone'); if(i){ try{ i.focus(); }catch(e){} }
  }); }
  function source(){
    var path=location.pathname||'/';
    var t=(document.title||'Zaprojektowani.com').replace(/\s+[–|-]\s+.*$/,'');
    var en=/^en\b/i.test(document.documentElement.lang||'');
    return 'Krótki formularz — '+(path==='/'||path==='/en/'?'strona główna':t)+' ('+path+')'+(en?' — wersja angielska':'');
  }
  function topic(){ var c=form.querySelector('input[name="topic"]:checked'); return c?c.value:''; }
  function sleep(ms){ return new Promise(function(r){ setTimeout(r,Math.max(0,ms)); }); }
  function fill(el,tpl,val){ if(el&&tpl&&val&&tpl.indexOf('%s')>-1){ el.textContent=tpl.replace('%s',val); } }
  form.addEventListener('submit',function(e){
    e.preventDefault();
    if(form.classList.contains('is-sending')) return;
    alertBox.hidden=true; alertBox.textContent='';
    if(!check()) return;
    form.classList.add('is-sending');
    var fd=new FormData();
    fd.set('action','zp_suite_contact');
    fd.set('nonce',form.querySelector('[name="nonce"]').value);
    fd.set('contact_mode','short');
    fd.set('name',input('name').value.trim());
    if(method()==='phone'){ fd.set('phone',input('phone').value.trim()); fd.set('email',''); }
    else { fd.set('email',input('email').value.trim()); fd.set('phone',''); }
    fd.set('services',topic());
    fd.set('message','');
    fd.set('consent','1');
    fd.set('source',source());
    var minMs=parseInt(form.getAttribute('data-min-ms')||'0',10)||0;
    var since=Date.now()-(window.zpSuiteFormStartedAt||started);
    sleep(minMs?minMs+400-since:0)
      .then(function(){ return window.zpSuiteSecureFormData?window.zpSuiteSecureFormData(fd):fd; })
      .then(function(secure){ return fetch(AJAX,{method:'POST',credentials:'same-origin',body:secure}); })
      .then(function(r){ return r.json().catch(function(){ return {success:false,data:{}}; }); })
      .then(function(res){
        if(res&&res.success){
          try{ sessionStorage.setItem('zpLeadSent','1'); }catch(err){}
          try{
            window.dataLayer=window.dataLayer||[];
            window.dataLayer.push({event:'zp_contact_form_submit',event_category:'lead',event_label:source(),form_name:'quick_contact',form_id:'zpQuickForm',lead_type:'contact_form',contact_mode:'short',services:topic(),source:source(),page_path:location.pathname,page_location:location.href,lead_id:(res.data&&res.data.lead_id)||'',value:250,currency:'PLN'});
          }catch(err){}
          root.classList.add('is-done');
          if(done){
            var first=(input('name').value||'').trim().split(/\s+/)[0]||'';
            if(first){ first=first.charAt(0).toUpperCase()+first.slice(1); }
            var via=method()==='phone'?input('phone').value.trim():input('email').value.trim();
            fill(done.querySelector('[data-done-title]'),msg('done-title'),first);
            fill(done.querySelector('[data-done-text]'),msg(method()==='phone'?'done-phone':'done-email'),via);
            done.hidden=false;
            try{ done.focus({preventScroll:true}); }catch(err){}
            var r=done.getBoundingClientRect();
            if(r.top<90||r.bottom>window.innerHeight-90){ try{ done.scrollIntoView({block:'center',behavior:'smooth'}); }catch(err){ done.scrollIntoView(); } }
          }
          return;
        }
        var d=(res&&res.data)||{};
        var list=(d.messages&&d.messages.length)?d.messages:[msg('network')];
        alertBox.textContent=list.join(' ');
        alertBox.hidden=false;
      })
      .catch(function(){ alertBox.textContent=msg('network'); alertBox.hidden=false; })
      .then(function(){ form.classList.remove('is-sending'); });
  });
})();
JS;
  $js = str_replace('AJAX', wp_json_encode($ajax), $js);
  return '<style id="zp-quick-contact-280">' . $css . '</style>'
    . '<script id="zp-quick-contact-280-js">' . $js . '</script>';
}

/* ------------------------------------------------------------- gdzie się pojawia */

add_action('init', function () {
  add_shortcode('zp_quick_contact', function ($atts) {
    $atts = shortcode_atts(['context' => 'home'], (array) $atts, 'zp_quick_contact');
    return zp_suite_quick_contact_section(sanitize_key($atts['context']));
  });
});

/** Pozycja tuż za </section> zamykającym sekcję, która zaczyna się w $start (zagnieżdżone sekcje liczone). */
function zp_suite_quick_contact_section_end(string $html, int $start): int {
  $depth = 0;
  $pos = $start;
  while (preg_match('~<(/?)section\b[^>]*>~i', $html, $t, PREG_OFFSET_CAPTURE, $pos)) {
    $depth += $t[1][0] === '/' ? -1 : 1;
    $pos = (int) $t[0][1] + strlen($t[0][0]);
    if ($depth === 0) { return $pos; }
  }
  return -1;
}

/** Początek pierwszej sekcji od $from, której znacznik otwierający zawiera $marker ('' = dowolna). */
function zp_suite_quick_contact_find_section(string $html, string $marker, int $from = 0): int {
  while (preg_match('~<section\b[^>]*>~i', $html, $m, PREG_OFFSET_CAPTURE, $from)) {
    if ($marker === '' || strpos($m[0][0], $marker) !== false) { return (int) $m[0][1]; }
    $from = (int) $m[0][1] + strlen($m[0][0]);
  }
  return -1;
}

/**
 * Wstawia sekcję za pakietami z cenami ($packages). Na stronach bez pakietów (np. branżowych)
 * za drugą sekcją pod hero ($hero), czyli w środku strony, daleko nad pełnym formularzem.
 */
function zp_suite_quick_contact_insert(string $html, string $packages, string $hero, string $context): string {
  if (strpos($html, 'data-zp-quick') !== false) { return $html; }
  $at = -1;
  $start = zp_suite_quick_contact_find_section($html, $packages);
  if ($start >= 0) {
    $at = zp_suite_quick_contact_section_end($html, $start);
  } else {
    $start = zp_suite_quick_contact_find_section($html, $hero);
    $at = $start >= 0 ? zp_suite_quick_contact_section_end($html, $start) : -1;
    for ($i = 0; $i < 2 && $at >= 0; $i++) {
      $next = zp_suite_quick_contact_find_section($html, '', $at);
      if ($next < 0) { break; }
      $at = zp_suite_quick_contact_section_end($html, $next);
    }
  }
  $insert = $at >= 0 ? zp_suite_quick_contact_section($context) : '';
  if ($insert === '') { return $html; }
  return substr($html, 0, $at) . "\n" . $insert . "\n" . substr($html, $at);
}

// Podstrony usług: szablony stron i logo (pakiety section#pakiety, hero section.hero#start)
// oraz sklepów (pakiety #oferta-sklepow, hero #zhHero).
add_filter('do_shortcode_tag', function ($output, $tag) {
  static $kinds = [
    'zp_strony_internetowe_katowice' => 'strony', 'zp_page_strony_katowice' => 'strony', 'zp_page_strony_internetowe_katowice' => 'strony',
    'zp_sklepy_internetowe_katowice' => 'sklepy', 'zp_page_sklepy_katowice' => 'sklepy',
    'zp_logo_branding_katowice' => 'logo', 'zp_page_logo_branding_katowice' => 'logo',
  ];
  if (!isset($kinds[$tag]) || !is_string($output) || $output === '' || !zp_suite_quick_contact_enabled($kinds[$tag])) { return $output; }
  $shop = $kinds[$tag] === 'sklepy';
  return zp_suite_quick_contact_insert($output, $shop ? 'id="oferta-sklepow"' : 'id="pakiety"', $shop ? 'id="zhHero"' : 'id="start"', $kinds[$tag]);
}, 60, 2);

/* ------------------------------------------------- tryb "short" i tłumaczenie EN */

/** Angielskie wersje tekstów szybkiego formularza (moduł językowy, PL → EN i z powrotem; długi formularz: contact-form-280.php). */
function zp_suite_quick_contact_dictionary(): array {
  return [
    'Szybki kontakt' => 'Quick contact',
    'Zostaw kontakt w 20 sekund. Resztę ustalimy w rozmowie.' => 'Leave your details in 20 seconds. We will sort out the rest on a call.',
    'Odezwie się Marta, Mateusz lub Stanisław, zwykle w ciągu 1 dnia roboczego.' => 'Marta, Mateusz or Stanisław will get back to you, usually within 1 business day.',
    'Imię' => 'First name',
    'np. Anna' => 'e.g. Anna',
    'Telefon' => 'Phone',
    'E-mail' => 'Email',
    'wolę e-mail' => 'I prefer email',
    'wolę telefon' => 'I prefer phone',
    'np. 500 600 700' => 'e.g. +48 500 600 700',
    'np. anna@firma.pl' => 'e.g. anna@company.com',
    'Poproś o kontakt' => 'Get in touch',
    'Wysyłamy…' => 'Sending…',
    'Czego dotyczy?' => 'What is it about?',
    'Strona www' => 'Website',
    'Sklep' => 'Online store',
    'Logo i branding' => 'Logo and branding',
    'Kampanie' => 'Ad campaigns',
    'Coś innego' => 'Something else',
    'Bez zobowiązań. Wysyłając, zgadzasz się na kontakt w sprawie zapytania (<1>polityka prywatności</1>).' => 'No obligation. By sending, you agree to be contacted about your enquiry (<1>privacy policy</1>).',
    'Wolisz opisać projekt dokładniej? <1>Pełny formularz</1>' => 'Prefer to describe your project in more detail? <1>Full form</1>',
    'Dziękujemy! Zgłoszenie dotarło.' => 'Thank you! We have received your message.',
    'Pilna sprawa? Zadzwoń: <1>501 054 253</1>.' => 'Urgent? Call us: <1>+48 501 054 253</1>.',
    'Chcesz od razu opisać projekt? <1>Pełny formularz</1>' => 'Want to describe your project right away? <1>Full form</1>',
    'Dziękujemy, %s! Zgłoszenie dotarło.' => 'Thank you, %s! We have received your message.',
    'Oddzwonimy na numer %s, zwykle w ciągu 1 dnia roboczego.' => 'We will call you back at %s, usually within 1 business day.',
    'Odpiszemy na adres %s, zwykle w ciągu 1 dnia roboczego.' => 'We will reply to %s, usually within 1 business day.',
    'Wpisz numer telefonu, np. 500 600 700.' => 'Please enter your phone number, e.g. +48 500 600 700.',
    'Wpisz adres e-mail, np. anna@firma.pl.' => 'Please enter your email address, e.g. anna@company.com.',
    'Nie udało się wysłać. Sprawdź połączenie i spróbuj ponownie albo zadzwoń: 501 054 253.' => 'We could not send it. Check your connection and try again, or call us: +48 501 054 253.',
    'Podaj telefon albo adres e-mail, żebyśmy mogli się odezwać.' => 'Please enter a phone number or an email address so we can get back to you.',
    'Numer telefonu wygląda na niepełny — wpisz 9 cyfr.' => 'The phone number looks incomplete. Please enter the full number.',
    'Odezwiemy się zwykle w ciągu 1 dnia roboczego.' => 'We usually get back to you within 1 business day.',
  ];
}

add_filter('zpl_dictionary', function ($map, $lang = 'en') {
  return zp_suite_contact_280_merge_dictionary($map, (string) $lang, zp_suite_quick_contact_dictionary());
}, 10, 2);
