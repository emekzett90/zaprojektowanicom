<?php
if (!defined('ABSPATH')) { exit; }

/**
 * ZP Suite v2.2.207 — SEO boost dla /strony-internetowe-katowice/.
 * Wzmacnia stronę pod frazy z monitoringu bez przebudowy wyglądu istniejących sekcji.
 */
add_action('plugins_loaded', function(){
  $ver_key = 'zp_suite_katowice_stage2_seo_207_version';
  if (get_option($ver_key) === '2.2.207') return;

  // Aktualizacja podstawowych nagłówków/leadów w CMS tej podstrony — bez ruszania portfolio i layoutu.
  $page = get_option('zp_suite_katowice_page', []);
  if (!is_array($page)) $page = [];
  $row = (!empty($page[0]) && is_array($page[0])) ? $page[0] : [];
  $merge = [
    'hero_h1' => 'Strony internetowe Katowice — projektujemy strony firmowe, które wyglądają profesjonalnie, wspierają SEO i zdobywają zapytania.',
    'hero_lead' => 'Tworzymy strony internetowe dla firm z Katowic, Śląska i całej Polski: strategia, struktura treści, UX/UI, WordPress, SEO techniczne, analityka, formularze oraz przygotowanie pod Google Ads i kampanie Meta Ads.',
    'portfolio_title' => 'Strony internetowe, sklepy i serwisy firmowe, które pokazują ofertę jasno i budują zaufanie przed pierwszym kontaktem.',
    'trust_title' => 'Projektowanie stron internetowych w Katowicach to nie tylko wygląd — liczy się proces, treść, mobile i droga do zapytania.',
    'process_title' => 'Jak wygląda tworzenie strony internetowej w Katowicach — od strategii, przez UX/UI, po WordPress i SEO techniczne?',
    'packages_title' => 'Wybierz zakres strony internetowej — landing page, strona firmowa WordPress, serwis premium albo projekt indywidualny.',
    'faq_title' => 'FAQ — strony internetowe Katowice, WordPress, tworzenie stron, SEO i wycena.',
  ];
  $page[0] = array_merge($row, $merge);
  update_option('zp_suite_katowice_page', $page, false);

  // FAQ pod frazy z raportu Senuto: strony internetowe/tworzenie/projektowanie/WordPress/Katowice.
  $faq = get_option('zp_suite_katowice_faq', []);
  if (!is_array($faq)) $faq = [];
  $new = [
    [
      'q' => 'Czy tworzenie stron internetowych Katowice obejmuje też SEO?',
      'a' => 'Tak. Przy stronach internetowych w Katowicach planujemy nie tylko wygląd, ale też strukturę pod SEO: nagłówki H1/H2, opisy usług, linkowanie wewnętrzne, FAQ, alty obrazków, schema, szybkość, mobile i podstawowe ustawienia Rank Math. Dzięki temu strona ma lepszy fundament pod indeksację i dalsze pozycjonowanie.'
    ],
    [
      'q' => 'Czy projektowanie stron internetowych Katowice różni się od zwykłego wdrożenia szablonu?',
      'a' => 'Tak. Nie zaczynamy od przypadkowego szablonu. Najpierw porządkujemy ofertę, cel strony, grupę odbiorców i ścieżkę kontaktu, a dopiero potem projektujemy UX/UI i wdrażamy WordPress. Dzięki temu strona firmowa jest spójna z marką, czytelna dla klienta i łatwiejsza do rozwijania pod SEO.'
    ],
    [
      'q' => 'Czy robicie strony WordPress dla firm lokalnych z Katowic i Śląska?',
      'a' => 'Tak. Tworzymy strony WordPress dla firm z Katowic, Śląska i całej Polski. W projektach lokalnych zwracamy uwagę na kontakt, lokalizację, usługi, opinie, realizacje, FAQ i frazy lokalne, żeby użytkownik szybko rozumiał, komu może zaufać i jak wysłać zapytanie.'
    ],
    [
      'q' => 'Czy strona internetowa może później zostać rozbudowana o sklep, blog albo kampanie?',
      'a' => 'Tak. Projektujemy strony tak, żeby można było je rozwijać etapami: o sklep WooCommerce, blog SEO, landing page pod reklamy, kolejne podstrony usługowe, formularze, analitykę, Google Ads, Meta Ads i automatyzacje. Dzięki temu strona nie kończy się w dniu publikacji.'
    ],
    [
      'q' => 'Czy przygotowujecie landing page pod kampanię reklamową?',
      'a' => 'Tak. Przygotowujemy landing page pod kampanie Meta Ads, Google Ads, promocje usług, zapisy i zapytania. Taki landing ma mocniejszy fokus na konwersję: jeden cel, czytelne sekcje zaufania, krótszą ścieżkę użytkownika i formularz dopasowany do kampanii.'
    ],
  ];
  $existing = [];
  foreach ($faq as $r) { if (!empty($r['q'])) $existing[trim(wp_strip_all_tags($r['q']))] = true; }
  foreach ($new as $r) {
    if (empty($existing[$r['q']])) $faq[] = $r;
  }
  update_option('zp_suite_katowice_faq', $faq, false);
  update_option($ver_key, '2.2.207', false);
}, 30);

add_action('wp_head', function(){
  if (is_admin()) return;
  $uri = $_SERVER['REQUEST_URI'] ?? '';
  if (!zp_suite_is_service_page('strony', $uri)) return;
  ?>
  <style id="zp-suite-strony-katowice-seo-boost-207">#zpStronyKatowice .zpKatSeoBoost{background:#f7f8fb;padding:clamp(58px,7vw,112px) 0;color:#07111f;overflow:hidden}#zpStronyKatowice .zpKatSeoBoost__inner{width:min(1740px,calc(100% - clamp(32px,5vw,120px)));margin:0 auto}#zpStronyKatowice .zpKatSeoBoost__head{display:grid;grid-template-columns:minmax(0,1.05fr) minmax(320px,.72fr);gap:clamp(28px,5vw,90px);align-items:end;margin-bottom:clamp(28px,4vw,58px)}#zpStronyKatowice .zpKatSeoBoost__kicker{display:inline-flex;align-items:center;gap:12px;text-transform:uppercase;letter-spacing:.13em;font-size:10px;line-height:1;font-weight:800;color:#667084;margin-bottom:14px}#zpStronyKatowice .zpKatSeoBoost__kicker::before{content:"";width:28px;height:1px;background:#7c879a;display:block}#zpStronyKatowice .zpKatSeoBoost h2{margin:0;max-width:920px;font-family:"Plus Jakarta Sans","Instrument Sans","Outfit",system-ui,sans-serif;font-size:clamp(34px,4.35vw,68px);line-height:.98;letter-spacing:-.052em;font-weight:680;color:#07111f;text-wrap:balance}#zpStronyKatowice .zpKatSeoBoost__head p{margin:0;max-width:640px;font-size:clamp(15px,1.12vw,18px);line-height:1.7;color:#657084}#zpStronyKatowice .zpKatSeoBoost__head strong{color:#07111f;font-weight:760}#zpStronyKatowice .zpKatSeoBoost__grid{display:grid;grid-template-columns:1.15fr .85fr .85fr;gap:16px}#zpStronyKatowice .zpKatSeoBoost__card{min-height:360px;border-radius:34px;border:1px solid rgba(7,17,31,.10);background:#fff;padding:clamp(28px,3.2vw,50px);display:flex;flex-direction:column;justify-content:space-between;box-shadow:0 24px 70px rgba(7,17,31,.06);position:relative;overflow:hidden}#zpStronyKatowice .zpKatSeoBoost__card.is-dark{color:#fff;background:radial-gradient(circle at 90% 8%,rgba(34,78,124,.36),transparent 36%),linear-gradient(135deg,#03070d 0%,#06111f 48%,#020408 100%);border-color:rgba(255,255,255,.10);box-shadow:none}#zpStronyKatowice .zpKatSeoBoost__card > span{text-transform:uppercase;letter-spacing:.16em;font-size:10px;font-weight:850;color:#6f7a8c;margin-bottom:26px}#zpStronyKatowice .zpKatSeoBoost__card.is-dark > span{color:rgba(255,255,255,.62)}#zpStronyKatowice .zpKatSeoBoost__card h3{margin:0 0 18px;font-size:clamp(25px,2.35vw,42px);line-height:1.02;letter-spacing:-.048em;font-weight:680;color:inherit;text-wrap:balance}#zpStronyKatowice .zpKatSeoBoost__card p{margin:0;font-size:15px;line-height:1.68;color:#6b7485}#zpStronyKatowice .zpKatSeoBoost__card.is-dark p{color:rgba(255,255,255,.72)}#zpStronyKatowice .zpKatSeoBoost__card ul{list-style:none;margin:30px 0 0;padding:0;display:grid;gap:10px}#zpStronyKatowice .zpKatSeoBoost__card li{position:relative;padding-left:18px;color:rgba(255,255,255,.78);font-size:13px;line-height:1.35}#zpStronyKatowice .zpKatSeoBoost__card li::before{content:"";position:absolute;left:0;top:.55em;width:6px;height:6px;border-radius:999px;background:#fff;opacity:.78}#zpStronyKatowice .zpKatSeoBoost__link{margin-top:28px;display:inline-flex;width:max-content;align-items:center;gap:10px;color:#07111f;text-decoration:none;font-weight:760;border-bottom:1px solid rgba(7,17,31,.35);padding-bottom:5px}#zpStronyKatowice .zpKatSeoBoost__link svg{width:15px;height:15px}#zpStronyKatowice .zpKatSeoBoost__chips{display:flex;flex-wrap:wrap;gap:8px;margin-top:28px}#zpStronyKatowice .zpKatSeoBoost__chips a{display:inline-flex;align-items:center;min-height:36px;border-radius:999px;padding:0 14px;border:1px solid rgba(7,17,31,.10);background:#f7f8fb;color:#07111f;text-decoration:none;font-size:12px;font-weight:760}@media (max-width:980px){#zpStronyKatowice .zpKatSeoBoost__head,#zpStronyKatowice .zpKatSeoBoost__grid{grid-template-columns:1fr}#zpStronyKatowice .zpKatSeoBoost__inner{width:min(100% - 32px,1740px)}#zpStronyKatowice .zpKatSeoBoost__card{min-height:auto;border-radius:26px}}</style>
  <?php
}, 55);
