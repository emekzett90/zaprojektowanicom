<?php
if (!defined('ABSPATH')) { exit; }

/**
 * Stopka v3 (biały pas stopki): linki pod plan fraz, ceny usług, pas lokalny Katowice.
 *
 * - Kolumny linków prowadzą do stron ogólnopolskich, stron dla firm i branż, cenników i
 *   poradników z planu fraz, bez dubli (wcześniej 3 linki do tej samej usługi, 4 do studia
 *   wyceny, „SEO i treści” do studia wyceny, prawne linki 2 razy).
 * - Każdy link do strony z planu pokazuje się dopiero, gdy ta strona jest opublikowana
 *   (zp_seo_plan_url), więc stopka nigdy nie linkuje do 404.
 * - Pas „Studio w Katowicach” zastępuje „Popularne tematy”: linki do 3 stron lokalnych
 *   (data-zp-local, żeby przepinanie linków Katowice → ogólnopolskie ich nie ruszało).
 * - Tytuły kolumn nie są nagłówkami H3 (porządek nagłówków na każdej podstronie).
 * - Na telefonie kolumny to akordeon (Usługi otwarte); bez JS wszystko jest rozwinięte.
 * - Wersja EN: tłumaczenia nowych tekstów dochodzą przez zpl_overrides (ręczne wpisy wygrywają).
 */

const ZP_FOOTER_V3_PLACE_ID = 'ChIJh0fbrHpbFkcRPEegRF9aX_I';

/** Href for a footer item: the planned page while it is published, else the fallback ('' = hide). */
function zp_footer_v3_href(array $item): string {
  if (!empty($item['always'])) { return (string) $item['path']; }
  $fallback = (string) ($item['fallback'] ?? '');
  if (!function_exists('zp_seo_plan_url')) { return $fallback; }
  $url = zp_seo_plan_url((string) $item['path'], '');
  return $url !== '' ? $url : $fallback;
}

/** Link groups of the white band, resolved (unpublished targets dropped, empty groups dropped). */
function zp_footer_v3_groups(): array {
  $groups = [
    'uslugi' => [
      'title' => 'Usługi',
      'open' => true,
      'items' => [
        ['text' => 'Tworzenie stron internetowych', 'path' => '/tworzenie-stron-internetowych/', 'fallback' => '/strony-internetowe-katowice/', 'price' => 'od 3 999 zł'],
        ['text' => 'Tworzenie sklepów internetowych', 'path' => '/tworzenie-sklepow-internetowych/', 'fallback' => '/sklepy-internetowe-katowice/', 'price' => 'od 6 499 zł'],
        ['text' => 'Projektowanie logo', 'path' => '/projektowanie-logo/', 'fallback' => '/logo-branding-katowice/', 'price' => 'od 999 zł'],
        ['text' => 'Identyfikacja wizualna', 'path' => '/identyfikacja-wizualna/'],
        ['text' => 'Kampanie reklamowe', 'path' => '/kampanie-reklamowe/', 'always' => true, 'price' => 'od 1 200 zł/mies.'],
        ['text' => 'Opieka WordPress', 'path' => '/opieka-wordpress/'],
      ],
    ],
    'strony' => [
      'title' => 'Strony dla firm',
      'items' => [
        ['text' => 'Strona wizytówka', 'path' => '/strona-wizytowka/'],
        ['text' => 'Landing page', 'path' => '/tworzenie-landing-page/'],
        ['text' => 'Strony WordPress', 'path' => '/strony-wordpress/'],
        ['text' => 'Strony dla deweloperów', 'path' => '/strony-internetowe-dla-deweloperow/'],
        ['text' => 'Strony dla kancelarii', 'path' => '/strony-internetowe-dla-kancelarii/'],
        ['text' => 'Strony dla lekarzy', 'path' => '/strony-internetowe-dla-lekarzy/'],
      ],
    ],
    'poradniki' => [
      'title' => 'Ceny i poradniki',
      'items' => [
        ['text' => 'Ile kosztuje strona internetowa', 'path' => '/strony-www/ile-kosztuje-stworzenie-strony-internetowej-dla-firmy/'],
        ['text' => 'Ile kosztuje sklep internetowy', 'path' => '/seo-i-konwersja/ile-kosztuje-sklep-internetowy-woocommerce-w-2026-roku/'],
        ['text' => 'Ile kosztuje logo', 'path' => '/logo-branding/ile-kosztuje-logo-dla-firmy-i-co-obejmuje-cena/'],
        ['text' => 'Jak stworzyć stronę internetową', 'path' => '/strony-internetowe/jak-stworzyc-strone-internetowa/'],
        ['text' => 'Co to jest landing page', 'path' => '/strony-internetowe/landing-page-co-to/'],
        ['text' => 'Co zawiera brandbook', 'path' => '/logo-branding/brandbook-co-powinien-zawierac-i-jak-go-czytac/'],
      ],
    ],
    'firma' => [
      'title' => 'Firma',
      'items' => [
        ['text' => 'O nas', 'path' => '/o-nas/', 'always' => true],
        ['text' => 'Realizacje', 'path' => '/realizacje/', 'always' => true],
        ['text' => 'Cennik i wycena', 'path' => '/studio-wyceny/', 'always' => true],
        ['text' => 'Opinie klientów', 'path' => 'https://search.google.com/local/reviews?placeid=' . ZP_FOOTER_V3_PLACE_ID, 'always' => true, 'external' => true],
        ['text' => 'Blog i poradniki', 'path' => '/wiedza/', 'always' => true],
        ['text' => 'FAQ', 'path' => '/najczesciej-zadawane-pytania/', 'fallback' => '/#faq'],
        ['text' => 'Kontakt', 'path' => '/kontakt/', 'always' => true],
      ],
    ],
  ];

  // /en/ maps every page for businesses to the same English page, so that column would be 6 copies of one link.
  if (function_exists('zp_seo_plan_is_en') && zp_seo_plan_is_en()) { unset($groups['strony']); }

  $out = [];
  foreach ($groups as $key => $group) {
    $items = [];
    foreach ($group['items'] as $item) {
      $href = zp_footer_v3_href($item);
      if ($href === '') { continue; }
      $items[] = $item + ['href' => $href];
    }
    if ($items) { $out[$key] = ['title' => $group['title'], 'open' => !empty($group['open']), 'items' => $items]; }
  }
  return $out;
}

/** Local pages strip (always linked; data-zp-local keeps them off the nationwide remap). */
function zp_footer_v3_local_links(): array {
  return [
    ['text' => 'Strony internetowe Katowice', 'path' => '/strony-internetowe-katowice/'],
    ['text' => 'Sklepy internetowe Katowice', 'path' => '/sklepy-internetowe-katowice/'],
    ['text' => 'Projektowanie logo Katowice', 'path' => '/logo-branding-katowice/'],
  ];
}

/** "od 3 999 zł" with non-breaking spaces so a price never wraps. */
function zp_footer_v3_price(string $price): string {
  return str_replace(' ', "\u{00A0}", $price);
}

/** Brand glyphs for the social buttons (Lucide no longer ships brand icons). */
function zp_footer_v3_icon(string $name): string {
  $icons = [
    'instagram' => '<svg class="zpFootIcon zpFootIcon--line" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><rect x="3" y="3" width="18" height="18" rx="5.2"/><circle cx="12" cy="12" r="4.1"/><circle class="zpFootIcon__dot" cx="17.35" cy="6.65" r="1.05"/></svg>',
    'facebook' => '<svg class="zpFootIcon zpFootIcon--fill" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M9.101 23.691v-7.98H6.627v-3.667h2.474v-1.58c0-4.085 1.848-5.978 5.858-5.978.401 0 .955.042 1.468.103a8.68 8.68 0 0 1 1.141.195v3.325a8.623 8.623 0 0 0-.653-.036 26.805 26.805 0 0 0-.733-.009c-.707 0-1.259.096-1.675.309a1.686 1.686 0 0 0-.679.622c-.258.42-.374.995-.374 1.752v1.297h3.919l-.386 2.103-.287 1.564h-3.246v8.245C19.396 23.238 24 18.179 24 12.044c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.628 3.874 10.35 9.101 11.647Z"/></svg>',
    'google' => '<svg class="zpFootIcon zpFootIcon--fill" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12.48 10.92v3.28h7.84c-.24 1.84-.853 3.187-1.787 4.133-1.147 1.147-2.933 2.4-6.053 2.4-4.827 0-8.6-3.893-8.6-8.72s3.773-8.72 8.6-8.72c2.6 0 4.507 1.027 5.907 2.347l2.307-2.307C18.747 1.44 16.133 0 12.48 0 5.867 0 .307 5.387.307 12s5.56 12 12.173 12c3.573 0 6.267-1.173 8.373-3.36 2.16-2.16 2.84-5.213 2.84-7.667 0-.76-.053-1.467-.173-2.053H12.48z"/></svg>',
    'whatsapp' => '<svg class="zpFootIcon zpFootIcon--line" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M3.6 20.4l1.25-4.1A8.6 8.6 0 1 1 8.2 19.4z"/><path d="M9.2 8.4c.2-.5.6-.5.9-.5h.4c.2 0 .4 0 .5.4l.6 1.4c.1.2 0 .4-.1.6l-.5.6c.6 1.2 1.6 2.1 2.8 2.7l.6-.6c.2-.2.4-.2.6-.1l1.4.6c.3.1.4.3.4.5v.4c0 .4-.2.8-.6 1-.5.3-1.3.4-2.2.1a7.9 7.9 0 0 1-4.6-4.6c-.3-.9-.2-1.6.1-2.1z"/></svg>',
    'pin' => '<svg class="zpFootIcon zpFootIcon--line" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 21.2s-6.6-5.6-6.6-11A6.6 6.6 0 0 1 18.6 10.2c0 5.4-6.6 11-6.6 11z"/><circle cx="12" cy="10.2" r="2.4"/></svg>',
  ];
  return $icons[$name] ?? '';
}

/* ------------------------------------------------------------------ styles + accordion */

add_action('wp_footer', function () {
  if (is_admin()) { return; }
  ?>
<style id="zp-suite-footer-v3">
html body .zpMegaFooter .zpMegaFooter__whiteBand .zpMegaFooter__links.zpFootNav{grid-template-columns:minmax(0,1.3fr) minmax(0,1fr) minmax(0,1.3fr) minmax(0,.78fr)!important;gap:clamp(24px,2.2vw,44px)!important;align-content:start!important}
.zpFootNav__group{min-width:0}
html body .zpMegaFooter .zpFootNav__title{position:relative;margin:0 0 16px;padding:0 0 13px;color:#071426;font:650 21px/1.05 var(--zpf-font,"Plus Jakarta Sans Local",system-ui,sans-serif);font-variation-settings:"wght" 650;letter-spacing:-.035em}
html body .zpMegaFooter .zpFootNav__title::before{content:"";position:absolute;left:0;bottom:0;width:42px;height:2px;border-radius:2px;background:linear-gradient(90deg,#1c477a 0%,#3b6ea8 70%,transparent 100%)}
.zpFootNav__list{list-style:none;margin:0;padding:0;display:grid;gap:2px}
.zpFootNav__list li{margin:0;padding:0;min-width:0}
html body .zpMegaFooter .zpFootNav__link{position:relative;display:inline-flex;align-items:center;gap:8px;max-width:100%;padding:7px 0;color:rgba(5,5,5,.66)!important;font:500 16px/1.3 var(--zpf-font,"Plus Jakarta Sans Local",system-ui,sans-serif);letter-spacing:-.012em;text-decoration:none!important;transition:color .22s ease}
html body .zpMegaFooter .zpFootNav__link span{background:linear-gradient(90deg,#1c477a,#3b6ea8) 0 100%/0 1px no-repeat;padding-bottom:1px;transition:background-size .32s cubic-bezier(.16,1,.3,1)}
html body .zpMegaFooter .zpFootNav__link::after{content:"";flex:0 0 auto;width:7px;height:7px;border-top:1.5px solid currentColor;border-right:1.5px solid currentColor;transform:translateX(-4px) rotate(45deg);opacity:0;transition:opacity .22s ease,transform .28s cubic-bezier(.16,1,.3,1)}
html body .zpMegaFooter .zpFootNav__link:hover{color:#1c477a!important}
html body .zpMegaFooter .zpFootNav__link:hover span{background-size:100% 1px}
html body .zpMegaFooter .zpFootNav__link:hover::after{opacity:.9;transform:translateX(0) rotate(45deg)}
html body .zpMegaFooter .zpFootNav__link--ext::after{transform:translateX(-4px) rotate(0);border:0;width:9px;height:9px;background:currentColor;-webkit-mask:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12'%3E%3Cpath d='M3 9 9 3M4 3h5v5' fill='none' stroke='%23000' stroke-width='1.6' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E") center/contain no-repeat;mask:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12'%3E%3Cpath d='M3 9 9 3M4 3h5v5' fill='none' stroke='%23000' stroke-width='1.6' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E") center/contain no-repeat;opacity:.45}
html body .zpMegaFooter .zpFootNav__link--ext:hover::after{transform:translate(1px,-1px);opacity:.9}
html body .zpMegaFooter .zpFootNav__price{display:block;margin:-4px 0 7px;color:#1c477a;font:700 12.5px/1.2 var(--zpf-font,"Plus Jakarta Sans Local",system-ui,sans-serif);letter-spacing:-.005em;font-variant-numeric:tabular-nums;opacity:.82}

/* Contact cards: <address> must not italicise, company card is now a map link */
html body .zpMegaFooter address.zpMegaFooter__contactCards{font-style:normal}
html body .zpMegaFooter .zpMegaFooter__contactCard--company small{display:block}

/* Social buttons: brand glyphs */
html body .zpMegaFooter .zpMegaFooter__socials a svg.zpFootIcon{width:17px!important;height:17px!important;flex:0 0 17px;padding:0!important;border:0!important;background:none!important;border-radius:0!important}
html body .zpMegaFooter svg.zpFootIcon--fill{fill:currentColor!important;stroke:none!important}
html body .zpMegaFooter svg.zpFootIcon--line{fill:none!important;stroke:currentColor!important;stroke-width:1.8!important}
html body .zpMegaFooter svg.zpFootIcon--line .zpFootIcon__dot{fill:currentColor;stroke:none}

/* Local strip (replaces "Popularne tematy") */
html body .zpMegaFooter .zpFootLocal{display:flex;align-items:center;justify-content:space-between;gap:18px 28px;flex-wrap:wrap;padding:24px 0 30px;border-top:1px solid rgba(5,5,5,.085)}
.zpFootLocal__text{display:flex;align-items:center;gap:14px;margin:0;color:rgba(7,20,38,.66);font:500 14.5px/1.45 var(--zpf-font,"Plus Jakarta Sans Local",system-ui,sans-serif);letter-spacing:-.01em}
.zpFootLocal__text strong{color:#071426;font-weight:700}
html body .zpMegaFooter .zpFootLocal__pin{flex:0 0 40px;width:40px;height:40px;display:grid;place-items:center;border-radius:13px;background:#f6f7f9;border:1px solid rgba(7,20,38,.08);color:#1c477a}
html body .zpMegaFooter .zpFootLocal__pin svg{width:18px;height:18px}
.zpFootLocal__links{list-style:none;margin:0;padding:0;display:flex;flex-wrap:wrap;gap:8px}
.zpFootLocal__links li{margin:0;padding:0}
html body .zpMegaFooter .zpFootLocal__links a{display:inline-flex;align-items:center;min-height:36px;padding:0 15px;border-radius:999px;background:#fff;border:1px solid rgba(7,20,38,.1);color:rgba(7,20,38,.82)!important;font:600 13px/1 var(--zpf-font,"Plus Jakarta Sans Local",system-ui,sans-serif);letter-spacing:-.01em;text-decoration:none!important;transition:color .22s ease,background .24s ease,border-color .24s ease,transform .22s ease}
html body .zpMegaFooter .zpFootLocal__links a:hover{color:#fff!important;background:linear-gradient(120deg,#071426 0%,#102a4f 58%,#1c477a 100%);border-color:rgba(28,71,122,.5);transform:translateY(-2px)}

@media (min-width:1101px) and (max-width:1599px){
  html body .zpMegaFooter .zpMegaFooter__whiteBand .zpMegaFooter__main{grid-template-columns:minmax(300px,380px) minmax(0,1fr)!important;gap:clamp(34px,4vw,60px)!important}
}
@media (max-width:1100px){
  html body .zpMegaFooter .zpMegaFooter__whiteBand .zpMegaFooter__links.zpFootNav{grid-template-columns:repeat(2,minmax(0,1fr))!important;gap:34px 26px!important}
}
@media (max-width:760px){
  html body .zpMegaFooter .zpMegaFooter__whiteBand .zpMegaFooter__links.zpFootNav{grid-template-columns:minmax(0,1fr)!important;gap:0!important;border-top:1px solid rgba(5,5,5,.085)}
  html body .zpMegaFooter .zpFootNav__group{border-bottom:1px solid rgba(5,5,5,.085)}
  html body .zpMegaFooter .zpMegaFooter__whiteBand .zpMegaFooter__main{border-bottom:0!important;padding-bottom:6px!important}
  html body .zpMegaFooter .zpFootNav__title{margin:0;padding:20px 36px 20px 0;font-size:19px;cursor:default}
  html body .zpMegaFooter .zpFootNav__title::before{content:none}
  html.zp-foot-acc body .zpMegaFooter .zpFootNav__title{cursor:pointer;-webkit-tap-highlight-color:transparent}
  html.zp-foot-acc body .zpMegaFooter .zpFootNav__title::after{content:"";position:absolute;right:8px;top:50%;width:9px;height:9px;margin-top:-7px;border-right:1.6px solid #1c477a;border-bottom:1.6px solid #1c477a;transform:rotate(45deg);transition:transform .28s cubic-bezier(.16,1,.3,1)}
  html.zp-foot-acc body .zpMegaFooter .zpFootNav__group.is-open .zpFootNav__title::after{transform:translateY(4px) rotate(-135deg)}
  html.zp-foot-acc body .zpMegaFooter .zpFootNav__group:not(.is-open) .zpFootNav__list{display:none}
  html body .zpMegaFooter .zpFootNav__list{padding:0 0 16px;gap:0}
  html body .zpMegaFooter .zpFootNav__list li{display:flex;align-items:baseline;justify-content:space-between;gap:12px}
  html body .zpMegaFooter .zpFootNav__link{padding:9px 0;font-size:16px}
  html body .zpMegaFooter .zpFootNav__price{margin:0;flex:0 0 auto;font-size:12.5px}
  html body .zpMegaFooter .zpFootLocal{display:grid;justify-content:stretch;padding:24px 0 28px;border-top:0}
  .zpFootLocal__text{align-items:flex-start;font-size:14px}
}
</style>
<script id="zp-suite-footer-v3-js">
(function(){
  var mq = window.matchMedia ? window.matchMedia('(max-width:760px)') : null;
  function groups(){ return document.querySelectorAll('[data-zp-foot-group]'); }
  function sync(){
    var acc = !!(mq && mq.matches);
    document.documentElement.classList.toggle('zp-foot-acc', acc);
    Array.prototype.forEach.call(groups(), function(g){
      var t = g.querySelector('.zpFootNav__title'), l = g.querySelector('.zpFootNav__list');
      if (!t || !l) { return; }
      if (acc) {
        t.setAttribute('role', 'button'); t.setAttribute('tabindex', '0');
        t.setAttribute('aria-controls', l.id); t.setAttribute('aria-expanded', g.classList.contains('is-open') ? 'true' : 'false');
      } else {
        ['role', 'tabindex', 'aria-controls', 'aria-expanded'].forEach(function(a){ t.removeAttribute(a); });
      }
    });
  }
  function toggle(t){
    if (!mq || !mq.matches) { return; }
    var g = t.closest('[data-zp-foot-group]'); if (!g) { return; }
    var open = !g.classList.contains('is-open');
    g.classList.toggle('is-open', open); t.setAttribute('aria-expanded', open ? 'true' : 'false');
  }
  document.addEventListener('click', function(e){ var t = e.target.closest && e.target.closest('.zpFootNav__title'); if (t) { toggle(t); } });
  document.addEventListener('keydown', function(e){ if (e.key !== 'Enter' && e.key !== ' ') { return; } var t = e.target.closest && e.target.closest('.zpFootNav__title'); if (t) { e.preventDefault(); toggle(t); } });
  if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', sync, { once: true }); } else { sync(); }
  if (mq) { if (mq.addEventListener) { mq.addEventListener('change', sync); } else if (mq.addListener) { mq.addListener(sync); } }
})();
</script>
  <?php
}, PHP_INT_MAX);

/* ------------------------------------------------------------------ English version */

/** Translations of the new footer texts for /en/ (manual entries in Languages win). */
function zp_footer_v3_en_strings(): array {
  return [
    'Tworzenie stron internetowych' => 'Website design and development',
    'Tworzenie sklepów internetowych' => 'Online store development',
    'Projektowanie logo' => 'Logo design',
    'Identyfikacja wizualna' => 'Visual identity',
    'Kampanie reklamowe' => 'Ad campaigns',
    'Opieka WordPress' => 'WordPress care',
    'Strony dla firm' => 'Websites for businesses',
    'Strona wizytówka' => 'Business card website',
    'Landing page' => 'Landing page',
    'Strony WordPress' => 'WordPress websites',
    'Strony dla deweloperów' => 'Websites for property developers',
    'Strony dla kancelarii' => 'Websites for law firms',
    'Strony dla lekarzy' => 'Websites for doctors',
    'Ceny i poradniki' => 'Prices and guides',
    'Ile kosztuje strona internetowa' => 'How much does a website cost',
    'Ile kosztuje sklep internetowy' => 'How much does an online store cost',
    'Ile kosztuje logo' => 'How much does a logo cost',
    'Jak stworzyć stronę internetową' => 'How to create a website',
    'Co to jest landing page' => 'What is a landing page',
    'Co zawiera brandbook' => 'What a brand book includes',
    'Cennik i wycena' => 'Pricing and quote',
    'Opinie klientów' => 'Client reviews',
    'Blog i poradniki' => 'Blog and guides',
    'Strony internetowe Katowice' => 'Websites Katowice',
    'Sklepy internetowe Katowice' => 'Online stores Katowice',
    'Projektowanie logo Katowice' => 'Logo design Katowice',
    'Studio w Katowicach.' => 'Studio in Katowice.',
    'Projektujemy dla firm z całej Polski.' => 'We design for companies across Poland.',
    'Mapa serwisu' => 'Site map',
    'Strony lokalne' => 'Local pages',
    'Biuro w Katowicach' => 'Office in Katowice',
    'ul. Modelarska 18/2, 40-142 Katowice' => 'ul. Modelarska 18/2, 40-142 Katowice, Poland',
    'Pon.–pt. 9:00–17:00 · NIP 9930682613' => 'Mon–Fri 9:00–17:00 · VAT ID PL9930682613',
    'od {1} zł' => 'from PLN {1}',
    'od {1} zł/mies.' => 'from PLN {1}/mo',
    'Opinie w Google' => 'Google reviews',
  ];
}

$zp_footer_v3_overrides = static function ($value) {
  $value = is_array($value) ? $value : [];
  return $value + zp_footer_v3_en_strings();
};
add_filter('option_zpl_overrides', $zp_footer_v3_overrides);
add_filter('default_option_zpl_overrides', $zp_footer_v3_overrides);
