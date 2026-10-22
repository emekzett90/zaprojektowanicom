<?php
/**
 * Audit 21.10.2026, item 02 (Mat: "3 ok ale ładnie wizualnie"): /strony-internetowe-katowice/ and
 * /sklepy-internetowe-katowice/ repeated most sections of the nationwide pages, so Google had little reason to treat
 * them as local pages. The Polish pages get:
 * - one local section after the portfolio: a studio card (address, hours, directions, phone) and four cards on what a
 *   firm from Katowice gets, in the light/navy style of the shared coverage section, with no new image file;
 * - local wording for the headings the pages shared with the nationwide ones ('replace' in service-pages.php);
 * - the shared coverage section (service-coverage-20261015.php) speaking about the Katowice studio.
 * English Katowice pages keep their templates (zp_seo_service_variant() is '' there).
 */
if (!defined('ABSPATH')) { exit; }

/** Google Maps directions to the studio's Business Profile (same place as 'hasMap' in schema.php). */
const ZP_SEO_KATOWICE_ROUTE = 'https://www.google.com/maps/dir/?api=1&destination=Zaprojektowani%2C%20Modelarska%2018%2F2%2C%20Katowice&destination_place_id=ChIJh0fbrHpbFkcRPEegRF9aX_I';

/** Copy of the local section per page kind ('strony' or 'sklepy'). */
function zp_seo_katowice_local_copy(string $kind): array {
  if ($kind === 'sklepy') {
    return [
      'eyebrow' => 'Sklepy internetowe Katowice',
      'title' => 'Sklep dla firmy z Katowic. <strong>Sprzedaż online i w Twoim punkcie.</strong>',
      'lead' => 'Tworzymy sklepy WooCommerce dla firm z Katowic i Śląska w naszym studiu przy ul. Modelarskiej 18/2. Sklep internetowy kosztuje u nas od <strong>6 499 zł</strong>, a zakres ustalamy na spotkaniu: produkty, płatności, dostawy i obsługę zamówień. Jeśli prowadzisz sklep stacjonarny, łączymy go ze sprzedażą w internecie.',
      'cards' => [
        ['Punkt i online', 'store', 'Sklep internetowy dla firmy z punktem stacjonarnym', 'Odbiór osobisty w Twoim punkcie jako jedna z metod dostawy, stany magazynowe w WooCommerce i jasna informacja, co jest dostępne od ręki.'],
        ['Warsztat', 'table', 'Warsztat sprzedażowy w studiu', 'Przy Modelarskiej 18/2 układamy kategorie, warianty, płatności i dostawy, zanim powstanie projekt. Kolejne etapy możemy prowadzić online.'],
        ['Google', 'search', 'Produkty i sklep widoczne w Google', 'SEO kategorii i kart produktów, Search Console i analityka od pierwszego dnia, a w pakiecie Sklep Rozwojowy feed produktowy do Google Merchant Center.'],
        ['Po starcie', 'growth', 'Instrukcja i rozwój po publikacji', 'Sklep przekazujemy z instrukcją obsługi. Potem pomagamy w rozbudowie, treściach i kampaniach Google Ads i Meta Ads od 1 200 zł miesięcznie.'],
      ],
    ];
  }
  return [
    'eyebrow' => 'Strony internetowe Katowice',
    'title' => 'Strona dla firmy z Katowic. <strong>Ułożona pod klientów z okolicy.</strong>',
    'lead' => 'Projektujemy strony internetowe dla firm z Katowic i Śląska w naszym studiu przy ul. Modelarskiej 18/2. Strona firmowa kosztuje u nas od <strong>3 999 zł</strong> z domeną i hostingiem w cenie i powstaje zwykle w 2–4 tygodnie. Układamy ją tak, żeby klient z okolicy od razu znalazł usługę, opinie, dojazd i telefon.',
    'cards' => [
      ['Wizytówka Google', 'pin', 'Strona i Profil Firmy w Google mówią jednym głosem', 'Ta sama nazwa, adres, telefon i godziny na stronie i w wizytówce, a do tego dane strukturalne firmy, dzięki którym Google łatwiej łączy stronę z Twoim profilem.'],
      ['Podstrony usług', 'pages', 'Każda ważna usługa ma swoją podstronę', 'Klient szuka konkretnej usługi, często z nazwą miasta. Dlatego najważniejsze usługi dostają osobne podstrony z opisem, zakresem, realizacjami i odpowiedziami na pytania.'],
      ['Zaufanie', 'star', 'Opinie i realizacje obok przycisku kontaktu', 'Zanim zadzwoni, klient z okolicy porównuje kilka firm. Opinie z Google, zdjęcia realizacji i zespół pokazujemy tam, gdzie zapada decyzja, a nie na osobnej podstronie.'],
      ['Telefon', 'phone', 'Telefon, dojazd i formularz pod kciukiem', 'Na telefonie klient dzwoni albo wyznacza trasę jednym dotknięciem, a formularz ma tylko potrzebne pola. Kliknięcia w telefon i wysłane formularze mierzymy w GA4.'],
    ],
  ];
}

/** Line icons in the style of the website template (24 px grid, stroke only). */
function zp_seo_katowice_local_icon(string $name): string {
  $paths = [
    'pin' => '<path d="M12 21s-7-6.2-7-11.5A7 7 0 0 1 19 9.5C19 14.8 12 21 12 21z"/><circle cx="12" cy="9.5" r="2.5"/>',
    'pages' => '<rect x="4" y="3" width="12" height="15" rx="2"/><path d="M8 21h10a2 2 0 0 0 2-2V7M8 8h4M8 12h4"/>',
    'star' => '<path d="m12 3 2.7 5.6 6.1.9-4.4 4.3 1 6.1L12 17l-5.4 2.9 1-6.1-4.4-4.3 6.1-.9z"/>',
    'phone' => '<rect x="7" y="2.5" width="10" height="19" rx="2.5"/><path d="M11 18.5h2"/>',
    'store' => '<path d="M4 9.5 5.5 4h13L20 9.5M4 9.5h16M4 9.5v10h16v-10M9.5 19.5v-5h5v5"/>',
    'table' => '<rect x="3" y="4" width="18" height="12" rx="2"/><path d="M8 20h8M12 16v4M7 9h6M7 12h10"/>',
    'search' => '<circle cx="11" cy="11" r="6.5"/><path d="m20 20-4.3-4.3"/>',
    'growth' => '<path d="m4 16 5-5 4 3 7-8M15 6h5v5"/>',
  ];
  return '<svg viewBox="0 0 24 24" aria-hidden="true">' . ($paths[$name] ?? '') . '</svg>';
}

/** Styles of the section; scoped to it, printed once with it (the section appears once per page). */
function zp_seo_katowice_local_css(): string {
  return '<style id="zp-local-kat-css">'
    . '.zpLocalKat{--lk-ink:#07111f;--lk-blue:#1c477a;--lk-soft:#65758b;--lk-line:#e2e7ec;box-sizing:border-box;background:#fff;color:var(--lk-ink);padding:clamp(64px,7vw,104px) 24px;font-family:"Plus Jakarta Sans Local","Plus Jakarta Sans",Jakarta,system-ui,sans-serif}'
    . '.zpLocalKat *{box-sizing:border-box}'
    . '.zpLocalKat__wrap{max-width:1750px;margin-inline:auto}'
    . '.zpLocalKat__head{display:grid;grid-template-columns:minmax(0,1.15fr) minmax(0,.85fr);gap:24px clamp(32px,5vw,72px);align-items:end;margin-bottom:clamp(32px,4vw,48px)}'
    . '.zpLocalKat__eyebrow{display:flex;align-items:center;gap:12px;margin:0 0 24px;color:var(--lk-blue);font-size:10px;font-weight:800;letter-spacing:.16em;text-transform:uppercase}'
    . '.zpLocalKat__eyebrow::before{content:"";width:28px;height:1px;background:var(--lk-blue)}'
    . 'html body .zpLocalKat h2{margin:0;color:var(--lk-ink);font-family:inherit;font-size:clamp(38px,4.2vw,60px);font-weight:500;line-height:1.09;letter-spacing:-.055em}'
    . 'html body .zpLocalKat h2 strong{display:block;font-weight:800}'
    . '.zpLocalKat__lead{margin:0;color:var(--lk-soft);font-size:16px;line-height:1.8;max-width:56ch}'
    . '.zpLocalKat__lead strong{color:#384e68;font-weight:700}'
    . '.zpLocalKat__grid{display:grid;grid-template-columns:minmax(0,.95fr) repeat(2,minmax(0,1fr));gap:16px}'
    . '.zpLocalKat__studio{position:relative;isolation:isolate;overflow:hidden;grid-row:span 2;display:flex;flex-direction:column;padding:32px;border-radius:28px;background:#0d2746;color:#fff}'
    . '.zpLocalKat__label{margin:0;color:rgba(255,255,255,.56);font-size:9px;font-weight:800;letter-spacing:.14em;text-transform:uppercase}'
    . 'html body .zpLocalKat__address{margin:28px 0 0;color:#fff;font-size:clamp(26px,2.5vw,36px);font-weight:500;line-height:1.12;letter-spacing:-.045em}'
    . 'html body .zpLocalKat__address strong{display:block;font-weight:800}'
    . '.zpLocalKat__meta{list-style:none;margin:24px 0 0;padding:0;display:grid;gap:10px;color:#c7d3e1;font-size:13px;line-height:1.5}'
    . '.zpLocalKat__meta li{display:flex;gap:10px;align-items:flex-start}'
    . '.zpLocalKat__meta li::before{content:"";flex:0 0 6px;height:6px;margin-top:7px;border-radius:50%;background:#6db2e4}'
    . '.zpLocalKat__actions{display:flex;flex-wrap:wrap;gap:12px 18px;align-items:center;margin-top:auto;padding-top:32px}'
    . 'html body .zpLocalKat a.zpLocalKat__route,html body .zpLocalKat a.zpLocalKat__route:visited{display:inline-flex;align-items:center;gap:12px;min-height:46px;padding:13px 20px;border-radius:999px;background:#fff;color:#07111f!important;-webkit-text-fill-color:#07111f;font-size:12px;font-weight:750;text-decoration:none}'
    . 'html body .zpLocalKat a.zpLocalKat__route:hover{background:#e8f0fa}'
    . 'html body .zpLocalKat a.zpLocalKat__tel,html body .zpLocalKat a.zpLocalKat__tel:visited{display:inline-flex;align-items:center;min-height:46px;color:#fff!important;-webkit-text-fill-color:#fff;font-size:13px;font-weight:750;text-decoration:underline;text-underline-offset:4px}'
    . '.zpLocalKat__mark{position:absolute;z-index:-1;right:-14%;bottom:-12%;width:62%;max-width:none;height:auto;opacity:.1;pointer-events:none}'
    . '.zpLocalKat__card{display:flex;flex-direction:column;min-width:0;padding:28px;border:1px solid var(--lk-line);border-radius:28px;background:#f7f9fb}'
    . '.zpLocalKat__num{color:#8a98a8;font-size:9px;font-weight:800;letter-spacing:.14em;text-transform:uppercase}'
    . '.zpLocalKat__icon{display:grid;place-items:center;width:46px;height:46px;margin-top:22px;border-radius:15px;background:#eaf1f8;color:var(--lk-blue)}'
    . '.zpLocalKat__icon svg{width:21px;height:21px;fill:none;stroke:currentColor;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round}'
    . 'html body .zpLocalKat__card h3{margin:26px 0 0;color:var(--lk-ink);font-family:inherit;font-size:clamp(19px,1.55vw,23px);font-weight:800;line-height:1.18;letter-spacing:-.035em}'
    . 'html body .zpLocalKat__card p{margin:12px 0 0;color:var(--lk-soft);font-size:13px;line-height:1.65}'
    . '@media(max-width:1080px){.zpLocalKat__grid{grid-template-columns:repeat(2,minmax(0,1fr))}.zpLocalKat__studio{grid-column:1/-1;grid-row:auto}.zpLocalKat__actions{padding-top:28px}}'
    . '@media(max-width:760px){.zpLocalKat{padding:56px 16px}.zpLocalKat__head{grid-template-columns:1fr}.zpLocalKat__grid{grid-template-columns:1fr;gap:12px}.zpLocalKat__studio,.zpLocalKat__card{padding:24px;border-radius:24px}.zpLocalKat__icon{width:40px;height:40px;margin-top:16px;border-radius:13px}html body .zpLocalKat__card h3{margin-top:18px}html body .zpLocalKat h2{font-size:clamp(34px,8vw,46px)}.zpLocalKat__eyebrow{font-size:9px;letter-spacing:.1em;margin-bottom:20px}.zpLocalKat__lead{font-size:15px;line-height:1.75}}'
    . '</style>';
}

/** Polish typesetting: a one-letter word and the parts of a price never end a line (prices elsewhere in the plugin use no-break spaces too). */
function zp_seo_katowice_local_nbsp(array $copy): array {
  array_walk_recursive($copy, static function (&$text) {
    $text = (string) preg_replace(
      ['~(?<![\pL\d])([aiouwz]) ~iu', '~\b(od|do) (?=(?:<strong>)?\d)~u', '~\b(ul\.) (?=\p{Lu})~u', '~(\d) (\d{3}) zł~u', '~(\d) (tygodni)~u'],
      ["$1\u{00A0}", "$1\u{00A0}", "$1\u{00A0}", "$1\u{00A0}$2\u{00A0}zł", "$1\u{00A0}$2"], (string) $text);
  });
  return $copy;
}

/** The local section for a page kind; '' for another kind. */
function zp_seo_katowice_local_html(string $kind): string {
  if (!in_array($kind, ['strony', 'sklepy'], true)) { return ''; }
  $c = zp_seo_katowice_local_nbsp(zp_seo_katowice_local_copy($kind));
  $cards = '';
  foreach ($c['cards'] as $i => [$label, $icon, $title, $text]) {
    $cards .= '<article class="zpLocalKat__card"><span class="zpLocalKat__num">' . sprintf('%02d', $i + 1) . ' / ' . esc_html($label) . '</span>'
      . '<span class="zpLocalKat__icon">' . zp_seo_katowice_local_icon($icon) . '</span>'
      . '<h3>' . esc_html($title) . '</h3><p>' . esc_html($text) . '</p></article>';
  }
  return '<section class="zpLocalKat" id="lokalnie" aria-labelledby="zpLocalKatTitle" data-zp-local-kat="' . esc_attr($kind) . '">'
    . zp_seo_katowice_local_css()
    . '<div class="zpLocalKat__wrap"><header class="zpLocalKat__head"><div>'
    . '<p class="zpLocalKat__eyebrow">' . esc_html($c['eyebrow']) . '</p>'
    . '<h2 id="zpLocalKatTitle">' . wp_kses($c['title'], ['strong' => []]) . '</h2></div>'
    . '<p class="zpLocalKat__lead">' . wp_kses($c['lead'], ['strong' => []]) . '</p></header>'
    . '<div class="zpLocalKat__grid">'
    . '<aside class="zpLocalKat__studio" aria-label="Studio Zaprojektowani w Katowicach">'
    . '<p class="zpLocalKat__label">Studio Zaprojektowani</p>'
    . '<p class="zpLocalKat__address"><strong>ul. Modelarska 18/2</strong>40-142 Katowice</p>'
    . '<ul class="zpLocalKat__meta"><li>Poniedziałek–piątek, 9:00–17:00</li><li>Spotkanie w studiu albo cały projekt online</li></ul>'
    . '<div class="zpLocalKat__actions"><a class="zpLocalKat__route" href="' . esc_url(ZP_SEO_KATOWICE_ROUTE) . '" target="_blank" rel="noopener">Wyznacz trasę <span aria-hidden="true">↗</span></a>'
    . '<a class="zpLocalKat__tel" href="tel:+48501054253">+48 501 054 253</a></div>'
    . '<img class="zpLocalKat__mark" src="' . esc_url(ZP_SUITE_URL . 'assets/logo-branding/zp-sygnet-jasny.webp') . '" alt="" width="240" height="240" loading="lazy" decoding="async">'
    . '</aside>' . $cards . '</div></div></section>';
}

/**
 * The shared coverage section speaks about the studio on the two Katowice pages (Polish only). Its texts come from
 * zp_coverage_markup_20261015(); a text that has changed there is simply left as it is.
 */
function zp_seo_katowice_local_coverage(string $html): string {
  if (!function_exists('zp_seo_service_variant') || zp_seo_plan_is_en()) { return $html; }
  $variant = zp_seo_service_variant();
  if (!in_array($variant, ['strony-local', 'sklepy-local'], true) || strpos($html, 'data-zp-coverage="20261015"') === false) { return $html; }
  $start = strpos($html, '<section class="zpCoverage" id="zasieg"');
  $end = $start === false ? false : strpos($html, '</section>', $start);
  if ($start === false || $end === false) { return $html; }
  $lead = $variant === 'sklepy-local'
    ? ['Tworzymy sklepy internetowe dla firm z całej Polski. Zakres, projekt i uruchomienie sprzedaży omówimy online lub w naszym studiu w Katowicach.',
       'Tworzymy sklepy internetowe dla firm z Katowic i Śląska. Warsztat, projekt i uruchomienie sprzedaży omówimy w naszym studiu albo online, jeśli tak wolisz.']
    : ['Projektujemy strony internetowe dla firm z całej Polski. Brief, prezentacje projektu i wdrożenie możemy przeprowadzić online lub omówić w naszym studiu w Katowicach.',
       'Projektujemy strony internetowe dla firm z Katowic i Śląska. Brief, prezentację projektu i odbiór strony omówimy w naszym studiu albo online, jeśli tak wolisz.'];
  $new = zp_seo_katowice_local_nbsp(['title' => 'Spotkajmy się w Katowicach.<br><strong>Albo online.</strong>', 'lead' => $lead[1]]);
  $section = strtr(substr($html, $start, $end - $start), [
    esc_html('Cała Polska · studio w Katowicach') => esc_html('Katowice · Śląsk'),
    'Dobra współpraca.<br><strong>Bez względu na odległość.</strong>' => $new['title'],
    esc_html($lead[0]) => esc_html($new['lead']),
  ]);
  return substr($html, 0, $start) . $section . substr($html, $end);
}
add_filter('zp_service_widgets_document', 'zp_seo_katowice_local_coverage', 11);
