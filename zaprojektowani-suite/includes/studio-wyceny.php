<?php
/**
 * ZP Suite — Studio Wyceny CMS / Orders / Packages v2.2.824
 */
if (!defined('ABSPATH')) { exit; }

const ZP_STUDIO_OPT_PACKAGES = 'zp_studio_wyceny_packages';
const ZP_STUDIO_OPT_ORDERS   = 'zp_studio_wyceny_orders';
const ZP_STUDIO_OPT_UNREAD   = 'zp_studio_wyceny_unread_orders';
const ZP_STUDIO_LOGO_DARK    = 'https://zaprojektowani.com/wp-content/uploads/2026/06/zp_sygnet_ciemny-scaled.webp';
const ZP_STUDIO_LOGO_LIGHT   = 'https://zaprojektowani.com/wp-content/uploads/2026/06/zp_sygnet-scaled.webp';

function zp_studio_statuses(){
  return [
    'new' => 'Nowe',
    'contact' => 'W trakcie kontaktu',
    'quote_sent' => 'Wycena wysłana',
    'accepted' => 'Zaakceptowane',
    'payment_pending' => 'Oczekuje na płatność',
    'production' => 'W realizacji',
    'done' => 'Zakończone',
    'archived' => 'Odrzucone / archiwum',
  ];
}


function zp_studio_default_packages(){
  return [
    'web' => [
      'kick'=>'Strony internetowe','title'=>'Studio wyceny dla <em>strony internetowej</em>.','lead'=>'Wybierz typ strony, zaznacz potrzebne funkcje i opisz krótko swój projekt. Na tej podstawie przygotujemy konkretną wycenę oraz rekomendowany zakres prac.','subject'=>'Brief strony internetowej','final'=>'Sprawdź brief i wyślij do wyceny.','help'=>'Opisz cel, resztę przełożymy na konkretny plan.','helpCopy'=>'Nie musisz znać technicznych pojęć. Wystarczy, że napiszesz, co chcesz osiągnąć, jakie masz materiały i jaki efekt ma dawać strona. Po analizie wrócimy z wyceną w 48–72 godziny.',
      'p'=>[
        ['Landing Page','Jedna mocna strona • kampania lub oferta',null,'panel-top',['UX/UI','copywriting','SEO','GA4'],'Jedna rozbudowana strona sprzedażowa z formularzem, responsywnością, SEO technicznym, analityką i publikacją.'],
        ['Strona Starter','Strona główna + kluczowe podstrony',null,'layout-template',['WordPress','SEO','mobile','analityka'],'Profesjonalna strona firmowa z indywidualnym UX/UI, copywritingiem, formularzami, SEO technicznym i możliwością samodzielnej edycji.'],
        ['Strona Premium','Strategia • SEO • konwersja • analityka',null,'sparkles',['nielimitowane podstrony','SEO','GTM','Ads Ready'],'Rozbudowany serwis z nieograniczoną liczbą podstron, pełnym copywritingiem, zaawansowanym SEO, analityką i przygotowaniem pod kampanie.'],
        ['Projekt indywidualny','Sklep • konfigurator • integracje',null,'wand-sparkles',['custom','integracje','B2B','automatyzacje'],'Dla serwisów, sklepów, konfiguratorów, systemów rezerwacji, stref klienta, integracji API i innych niestandardowych funkcji.'],
      ],
    ],
    'shop' => [
      'kick'=>'Sklepy internetowe','title'=>'Studio wyceny dla <em>sklepu internetowego</em>.','lead'=>'Zaznacz, jak ma działać sklep: produkty, płatności, dostawy, integracje i materiały. Dzięki temu szybciej przygotujemy sensowną wycenę wdrożenia.','subject'=>'Brief sklepu internetowego','final'=>'Sprawdź brief sklepu i wyślij do wyceny.','help'=>'Pokaż nam, jak chcesz sprzedawać online.','helpCopy'=>'W briefie wybierasz najważniejsze elementy sklepu: produkty, płatności, dostawy i integracje. Po wysłaniu uporządkujemy zakres i odezwiemy się z wyceną w 48–72 godziny.',
      'p'=>[
        ['Sklep Startowy','Profesjonalny WooCommerce gotowy do pierwszych zamówień',null,'shopping-bag',['WooCommerce','płatności','dostawy','SEO Ready'],'Indywidualny sklep z UX/UI, produktami i wariantami, koszykiem, checkoutem, płatnościami, dostawami, SEO, analityką i publikacją.'],
        ['Sklep Rozwojowy','Sprzedaż • SEO • reklamy • skalowanie',null,'crown',['UX premium','Merchant','analityka','automatyzacje'],'Rozbudowany sklep pod sprzedaż i skalowanie: cross/upsell, Merchant Center, pełny tracking e-commerce, SEO techniczne, reklamy i automatyzacje.'],
        ['Sklep Indywidualny','System sprzedaży projektowany 1:1 pod biznes',null,'wand-sparkles',['B2B','integracje','konfiguratory','automatyzacje'],'Dla B2B, hurtu, konfiguratorów, ERP/CRM, BaseLinkera, API, niestandardowych płatności, logiki WooCommerce i rozwoju po wdrożeniu.'],
      ],
    ],
    'brand' => [
      'kick'=>'Logo & Branding','title'=>'Zamówienie <em>logo i brandingu</em>.','lead'=>'Wybierz pakiet, termin realizacji i dodatki do identyfikacji wizualnej. Na końcu zobaczysz podsumowanie ceny i wyślesz zgłoszenie do realizacji.','subject'=>'Zamówienie Logo & Branding','final'=>'Sprawdź zamówienie i wyślij zgłoszenie.','help'=>'Widzisz zakres i cenę przed wysłaniem.','helpCopy'=>'Wybierz pakiet logo, dodaj materiały firmowe lub social media, ustaw termin i prześlij brief marki. Potwierdzimy zgłoszenie mailowo i przeprowadzimy Cię przez kolejne kroki.',
      'p'=>[
        ['Logo Startowe','Dla startów i mikrofirm',999,'sparkles',['999 zł','4 koncepcje','5 rund','instrukcja PDF'],'Logo, podstawowy sygnet, warianty znaku, pliki do internetu i druku, favicon, avatar, znaki wodne, mockupy i prawa autorskie.'],
        ['Mini Branding','Karta stylu + materiały',1499,'layers',['1499 zł','6 koncepcji','10 rund','karta stylu'],'Rozszerzony system logo z kartą stylu, wizytówką, stopką e-mail, teczką firmową, znakami wodnymi, instrukcją PDF i prawami autorskimi.'],
        ['Branding Premium','Pełny system + księga',2999,'crown',['2999 zł','bez limitu koncepcji','pełna księga','AI + EPS'],'Nielimitowane koncepcje i poprawki, pełna księga znaku, kompletny system logo, materiały firmowe, mockupy premium, pliki źródłowe i pełne prawa autorskie.'],
        ['Oddzielna wycena','Zakres pod Twoje potrzeby',null,'wand-2',['custom','wycena indywidualna','niestandardowy zakres'],'Dla niestandardowego zestawu materiałów: szyldy, opakowania, social, druk, prezentacje albo szersze wdrożenie marki.'],
      ],
    ],
    'ads' => [
      'kick'=>'Kampanie reklamowe','title'=>'Studio wyceny dla <em>kampanii reklamowej</em>.','lead'=>'Wybierz platformy reklamowe, cel kampanii, budżet i materiały. Na tej podstawie przygotujemy propozycję działań oraz wycenę obsługi.','subject'=>'Brief kampanii reklamowej','final'=>'Sprawdź brief kampanii i wyślij do wyceny.','help'=>'Powiedz, jaki efekt ma dać kampania.','helpCopy'=>'Nie musisz znać ustawień reklam. Wystarczy cel, budżet, branża i obecne materiały. Dobierzemy strukturę kampanii, pomiar konwersji i plan testów.',
      'p'=>[
        ['Starter Ads','Szybki start kampanii i pierwsze dane',null,'rocket',['Meta Ads','setup','lead/ruch','raport'],'Dla firm, które chcą poprawnie wystartować: konfiguracja kampanii, podstawowe targetowanie, test kreacji i proste wnioski.'],
        ['Standard Performance','Stała obsługa i optymalizacja',null,'trending-up',['testy','remarketing','optymalizacja','UTM'],'Dla firm, które chcą stabilnego dopływu leadów lub sprzedaży, regularnych testów i optymalizacji budżetu.'],
        ['Premium Growth Ads','Strategia, testy i skalowanie',null,'crown',['strategia','CAPI','kreacje','skalowanie'],'Dla marek, które chcą mocniejszego procesu: wiele kątów reklamowych, remarketing, analityka, testy i szybkie iteracje.'],
      ],
    ],
  ];
}

function zp_studio_packages(){
  $defaults = zp_studio_default_packages();
  $saved = get_option(ZP_STUDIO_OPT_PACKAGES, []);
  if (!is_array($saved)) { $saved = []; }
  $data = array_replace_recursive($defaults, $saved);
  return zp_studio_normalize_public_copy($data);
}

function zp_studio_normalize_public_copy($data){
  if (!is_array($data)) { return $data; }
  $public_copy = [
    'web' => [
      'lead' => 'Wybierz typ strony, zaznacz potrzebne funkcje i opisz krótko swój projekt. Na tej podstawie przygotujemy konkretną wycenę oraz rekomendowany zakres prac.',
      'help' => 'Opisz cel, resztę przełożymy na konkretny plan.',
      'helpCopy' => 'Nie musisz znać technicznych pojęć. Wystarczy, że napiszesz, co chcesz osiągnąć, jakie masz materiały i jaki efekt ma dawać strona. Po analizie wrócimy z wyceną w 48–72 godziny.',
    ],
    'shop' => [
      'lead' => 'Zaznacz, jak ma działać sklep: produkty, płatności, dostawy, integracje i materiały. Dzięki temu szybciej przygotujemy sensowną wycenę wdrożenia.',
      'help' => 'Pokaż nam, jak chcesz sprzedawać online.',
      'helpCopy' => 'W briefie wybierasz najważniejsze elementy sklepu: produkty, płatności, dostawy i integracje. Po wysłaniu uporządkujemy zakres i odezwiemy się z wyceną w 48–72 godziny.',
    ],
    'brand' => [
      'lead' => 'Wybierz pakiet, termin realizacji i dodatki do identyfikacji wizualnej. Na końcu zobaczysz podsumowanie ceny i wyślesz zgłoszenie do realizacji.',
      'help' => 'Widzisz zakres i cenę przed wysłaniem.',
      'helpCopy' => 'Wybierz pakiet logo, dodaj materiały firmowe lub social media, ustaw termin i prześlij brief marki. Potwierdzimy zgłoszenie mailowo i przeprowadzimy Cię przez kolejne kroki.',
    ],
    'ads' => [
      'lead' => 'Wybierz platformy reklamowe, cel kampanii, budżet i materiały. Na tej podstawie przygotujemy propozycję działań oraz wycenę obsługi.',
      'help' => 'Powiedz, jaki efekt ma dać kampania.',
      'helpCopy' => 'Nie musisz znać ustawień reklam. Wystarczy cel, budżet, branża i obecne materiały. Dobierzemy strukturę kampanii, pomiar konwersji i plan testów.',
    ],
  ];
  // ZP Suite v2.2.824 — front Studio Wyceny zawsze korzysta z aktualnych pakietów
  // z /logo-branding-katowice/, /strony-internetowe-katowice/ i /sklepy-internetowe-katowice/.
  $current_packages = [
    'web' => [
      ['Landing Page','Jedna mocna strona • kampania lub oferta',null,'panel-top',['UX/UI','copywriting','SEO','GA4'],'Jedna rozbudowana strona sprzedażowa z formularzem, responsywnością, SEO technicznym, analityką i publikacją.'],
      ['Strona Starter','Strona główna + kluczowe podstrony',null,'layout-template',['WordPress','SEO','mobile','analityka'],'Profesjonalna strona firmowa z indywidualnym UX/UI, copywritingiem, formularzami, SEO technicznym i możliwością samodzielnej edycji.'],
      ['Strona Premium','Strategia • SEO • konwersja • analityka',null,'sparkles',['nielimitowane podstrony','SEO','GTM','Ads Ready'],'Rozbudowany serwis z nieograniczoną liczbą podstron, pełnym copywritingiem, zaawansowanym SEO, analityką i przygotowaniem pod kampanie.'],
      ['Projekt indywidualny','Sklep • konfigurator • integracje',null,'wand-sparkles',['custom','integracje','B2B','automatyzacje'],'Dla serwisów, sklepów, konfiguratorów, systemów rezerwacji, stref klienta, integracji API i innych niestandardowych funkcji.'],
    ],
    'shop' => [
      ['Sklep Startowy','Profesjonalny WooCommerce gotowy do pierwszych zamówień',null,'shopping-bag',['WooCommerce','płatności','dostawy','SEO Ready'],'Indywidualny sklep z UX/UI, produktami i wariantami, koszykiem, checkoutem, płatnościami, dostawami, SEO, analityką i publikacją.'],
      ['Sklep Rozwojowy','Sprzedaż • SEO • reklamy • skalowanie',null,'crown',['UX premium','Merchant','analityka','automatyzacje'],'Rozbudowany sklep pod sprzedaż i skalowanie: cross/upsell, Merchant Center, pełny tracking e-commerce, SEO techniczne, reklamy i automatyzacje.'],
      ['Sklep Indywidualny','System sprzedaży projektowany 1:1 pod biznes',null,'wand-sparkles',['B2B','integracje','konfiguratory','automatyzacje'],'Dla B2B, hurtu, konfiguratorów, ERP/CRM, BaseLinkera, API, niestandardowych płatności, logiki WooCommerce i rozwoju po wdrożeniu.'],
    ],
    'brand' => [
      ['Logo Startowe','Dla startów i mikrofirm',999,'sparkles',['999 zł','4 koncepcje','5 rund','instrukcja PDF'],'Logo, podstawowy sygnet, warianty znaku, pliki do internetu i druku, favicon, avatar, znaki wodne, mockupy i prawa autorskie.'],
      ['Mini Branding','Karta stylu + materiały',1499,'layers',['1499 zł','6 koncepcji','10 rund','karta stylu'],'Rozszerzony system logo z kartą stylu, wizytówką, stopką e-mail, teczką firmową, znakami wodnymi, instrukcją PDF i prawami autorskimi.'],
      ['Branding Premium','Pełny system + księga',2999,'crown',['2999 zł','bez limitu koncepcji','pełna księga','AI + EPS'],'Nielimitowane koncepcje i poprawki, pełna księga znaku, kompletny system logo, materiały firmowe, mockupy premium, pliki źródłowe i pełne prawa autorskie.'],
      ['Oddzielna wycena','Zakres pod Twoje potrzeby',null,'wand-2',['custom','wycena indywidualna','niestandardowy zakres'],'Dla niestandardowego zestawu materiałów: szyldy, opakowania, social, druk, prezentacje albo szersze wdrożenie marki.'],
    ],
  ];
  foreach ($current_packages as $svc => $rows) {
    $data[$svc]['p'] = [];
    foreach ($rows as $i => $row) {
      $data[$svc]['p'][$i] = $row;
    }
  }

  foreach ($public_copy as $svc => $copy) {
    if (!isset($data[$svc]) || !is_array($data[$svc])) { continue; }
    foreach ($copy as $field => $value) { $data[$svc][$field] = $value; }
  }
  if (isset($data['brand'])) {
    $old_leads = [
      'Tutaj pokazujemy konkretne pakiety cenowe. Klient wybiera zakres, termin, dodatki i wysyła zamówienie, a nie tylko luźne zapytanie.'
    ];
    if (empty($data['brand']['lead']) || in_array($data['brand']['lead'], $old_leads, true)) {
      $data['brand']['lead'] = 'Wybierz pakiet logo i brandingu, dopasuj termin realizacji, dodaj potrzebne materiały i wyślij zamówienie. Po otrzymaniu zgłoszenia wrócimy z potwierdzeniem oraz kolejnymi krokami.';
    }
    if (empty($data['brand']['help']) || $data['brand']['help'] === 'Tu mamy sztywne pakiety i ceny.') {
      $data['brand']['help'] = 'Masz jasny zakres i cenę przed wysłaniem.';
    }
    if (empty($data['brand']['helpCopy']) || $data['brand']['helpCopy'] === 'Logo & Branding działa jako zamówienie: pakiet, termin, dodatki, suma oraz brief marki. Finalne podpięcie pod CMS zrobimy po akceptacji frontu.') {
      $data['brand']['helpCopy'] = 'Wybierasz pakiet, termin oraz dodatki, a na końcu widzisz podsumowanie ceny. Po wysłaniu zgłoszenia potwierdzimy szczegóły i rozpoczniemy proces projektowy.';
    }
  }
  return $data;
}

function zp_studio_sanitize_packages($input){
  $defaults = zp_studio_default_packages();
  $out = [];
  foreach ($defaults as $service => $data) {
    $src = isset($input[$service]) && is_array($input[$service]) ? $input[$service] : [];
    foreach (['kick','title','lead','subject','final','help','helpCopy'] as $field) {
      $out[$service][$field] = isset($src[$field]) ? wp_kses_post(wp_unslash($src[$field])) : $data[$field];
    }
    $out[$service]['p'] = [];
    $rows = isset($src['p']) && is_array($src['p']) ? $src['p'] : [];
    foreach ($data['p'] as $i => $defrow) {
      $r = isset($rows[$i]) && is_array($rows[$i]) ? $rows[$i] : [];
      $price = isset($r['price']) && $r['price'] !== '' ? (int) preg_replace('/[^0-9]/','', (string) $r['price']) : null;
      if ($service !== 'brand') { $price = null; }
      $promo = isset($r['promo']) ? (int) preg_replace('/[^0-9]/','', (string) $r['promo']) : 0;
      $tags = isset($r['tags']) ? array_filter(array_map('trim', explode(',', sanitize_text_field(wp_unslash($r['tags']))))) : $defrow[4];
      $out[$service]['p'][$i] = [
        sanitize_text_field(wp_unslash($r['name'] ?? $defrow[0])),
        sanitize_text_field(wp_unslash($r['note'] ?? $defrow[1])),
        $price,
        sanitize_key($r['icon'] ?? $defrow[3]),
        array_values($tags),
        wp_kses_post(wp_unslash($r['desc'] ?? $defrow[5])),
      ];
      if ($service === 'brand' && $promo > 0) {
        $out[$service]['p'][$i]['promo'] = $promo;
      }
    }
  }
  return $out;
}

function zp_studio_activate(){
  if (!get_option(ZP_STUDIO_OPT_PACKAGES)) { update_option(ZP_STUDIO_OPT_PACKAGES, zp_studio_default_packages(), false); }
  if (!get_option(ZP_STUDIO_OPT_ORDERS)) { update_option(ZP_STUDIO_OPT_ORDERS, [], false); }
}
add_action('admin_init', function(){ if (!get_option(ZP_STUDIO_OPT_PACKAGES)) { zp_studio_activate(); } });

function zp_studio_front_data(){
  $data = zp_studio_packages();
  // Drobny compatibility layer: stare JS ceny promocyjne czytało z hard-coded <small>.
  // Ceny promocyjne przekazujemy jako 7. element wiersza; patch JS niżej je odczyta przy renderze.
  return $data;
}

function zp_studio_render_shortcode($atts=[]){
  $template = ZP_SUITE_PATH . 'templates/studio-wyceny.html';
  if (!file_exists($template)) { return '<!-- ZP Studio Wyceny: brak template -->'; }
  $html = file_get_contents($template);
  // v2.2.759 — ekran wyboru Studio Wyceny v19 jest osobnym dokumentem iframe.
  // Dzięki temu CSS motywu, Elementora i starego konfiguratora nie może zmienić jego wyglądu.
  $html = str_replace('{{ZP_STUDIO_V19_WEB_IMG}}', esc_url(ZP_SUITE_URL . 'assets/studio-wyceny-v19/web.png'), $html);
  $html = str_replace('{{ZP_STUDIO_V19_SHOP_IMG}}', esc_url(ZP_SUITE_URL . 'assets/studio-wyceny-v19/shop.png'), $html);
  $html = str_replace('{{ZP_STUDIO_V19_FRAME_URL}}', esc_url(add_query_arg('v', ZP_SUITE_VERSION, ZP_SUITE_URL . 'assets/studio-wyceny-v19/chooser-v19.html')), $html);
  /* v2.2.595: "Zaufali nam" jako kompaktowy statyczny blok (styl WIDGET-2).
     Widget [zp_trust_logos] ze strony głównej to komponent scroll-pinowany
     (spacer 330vh + sticky 100svh z twardym białym tłem) — wstrzyknięty w studio
     zostawiał białą sekcję i ~2 ekrany pustego ciemnego tła (overflow:hidden slotu
     wyłącza position:sticky). Renderujemy te same logotypy z CMS jako zwykłą
     siatkę: bez spacera, bez sticky, bez scroll-JS. */
  $zp_trust_dark = '';
  if (function_exists('zp_suite_cms_get')) {
    $zp_logos = array_values(array_filter((array) zp_suite_cms_get('logos', []), function($l){ return is_array($l) && (!isset($l['visible']) || (string)$l['visible'] !== '0'); }));
    if ($zp_logos) {
      $zp_heading  = zp_suite_opt('trust_logos.heading', 'Marki, które powierzyły nam swój <span>wizerunek</span>');
      $zp_eyebrow  = zp_suite_opt('trust_logos.eyebrow', 'Zaufali nam');
      $zp_lead     = zp_suite_opt('trust_logos.lead', 'Projektujemy identyfikacje wizualne, strony internetowe, sklepy i kampanie dla firm, które chcą wyglądać profesjonalnie od pierwszego kontaktu.');
      $zp_tile_items = [];
      foreach ($zp_logos as $zp_logo) {
        $zp_img = esc_url($zp_logo['image'] ?? '');
        if (!$zp_img) { continue; }
        $zp_name = esc_attr($zp_logo['name'] ?? 'Logo klienta');
        $zp_nl   = strtolower((string)($zp_logo['name'] ?? '') . ' ' . (string)($zp_logo['image'] ?? ''));
        $zp_cls  = 'zpbsTrustDark__logo';
        if (strpos($zp_nl,'proscarves')!==false || strpos($zp_nl,'siemianowski')!==false || strpos($zp_nl,'polerstone')!==false || strpos($zp_nl,'piotr')!==false) { $zp_cls .= ' zpbsTrustDark__logo--wide'; }
        if (strpos($zp_nl,'vista')!==false || strpos($zp_nl,'sfera')!==false || strpos($zp_nl,'gravia')!==false || strpos($zp_nl,'apartament')!==false || strpos($zp_nl,'/ap.webp')!==false) { $zp_cls .= ' zpbsTrustDark__logo--tall'; }
        if (strpos($zp_nl,'siemianowski')!==false) { $zp_cls .= ' zpbsTrustDark__logo--siemianowski'; }
        if (strpos($zp_nl,'raxo')!==false) { $zp_cls .= ' zpbsTrustDark__logo--raxo'; }
        if (strpos($zp_nl,'gravia')!==false) { $zp_cls .= ' zpbsTrustDark__logo--gravia'; }
        $zp_tile_items[] = '<div class="'.$zp_cls.'" title="'.$zp_name.'" style="--zpbs-logo-mask:url(&quot;'.$zp_img.'&quot;)"><img src="'.$zp_img.'" alt="'.$zp_name.'" loading="lazy" decoding="async" data-no-lazy="1" data-skip-lazy="1" class="skip-lazy"></div>';
      }
      $zp_tiles = $zp_tile_items ? implode('', $zp_tile_items) : '';
      if ($zp_tiles) {
        $zp_head_html = function_exists('zp_suite_allowed_html') ? wp_kses($zp_heading, zp_suite_allowed_html()) : esc_html($zp_heading);
        $zp_trust_dark = '<section class="zpbsTrustDark" aria-labelledby="zpbsTrustDarkTitle">'
          . '<div class="zpbsTrustDark__in">'
          . '<header class="zpbsTrustDark__head">'
          . '<span class="zpbsTrustDark__eyebrow"><i aria-hidden="true"></i>'.esc_html($zp_eyebrow).'</span>'
          . '<h2 id="zpbsTrustDarkTitle" class="zpbsTrustDark__title">'.$zp_head_html.'</h2>'
          . ($zp_lead ? '<p class="zpbsTrustDark__lead">'.esc_html($zp_lead).'</p>' : '')
          . '</header>'
          . '<div class="zpbsTrustDark__grid" role="group" aria-label="Logotypy klientów">'.$zp_tiles.'</div>'
          . '<figure class="zpbsTrustDark__person" aria-hidden="true"><img src="https://zaprojektowani.com/wp-content/uploads/2026/05/mateusz_nowy.webp" alt="" loading="eager" fetchpriority="high" decoding="async" data-no-lazy="1" data-skip-lazy="1" class="skip-lazy"></figure>'
          . '</div></section>';
      }
    }
  }
  if ($zp_trust_dark) {
    $html = preg_replace('/<section class="zpbsHomeTrust"[\s\S]*?<\/section>/', '<!--ZP_TRUST_HOME_PLACEHOLDER-->', $html, 1);
    $html = str_replace('<!--ZP_TRUST_HOME_PLACEHOLDER-->', '<div class="zpbsTrustSlot" data-zpbs-trust-slot>' . $zp_trust_dark . '</div>', $html);
  }
  $data_json = wp_json_encode(zp_studio_front_data(), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
  $html = preg_replace('/var\s+DATA\s*=\s*\{.*?\};\s*\n\s*var\s+TERMS\s*=/s', 'var DATA='.$data_json.";\n\n    var TERMS=", $html, 1);
  // Usuwamy ewentualny stary button start, jeśli trafił do template'u.
  $html = preg_replace('/\s*<button class="zpbsStartBtn"[\s\S]*?<\/button>\s*/', "\n", $html, 1);
  // Gdy klik na kartę przechodzi do briefu, [data-start] może już nie istnieć — dodajemy helper JS bez ruszania wyglądu.
  $html = str_replace("var services=['web','shop','brand','ads'];", "var services=['web','shop','brand','ads'];\n    var ZPBS_AJAX=(window.zpbsStudioAjax||{});", $html);

  // Poprawna logika dodatków Logo & Branding:
  // zablokowane są tylko te elementy, które faktycznie znajdują się w danym pakiecie.
  $html = str_replace("var BRAND_BLOCKED={starter:['favicon'],standard:['favicon','businesscard','email_sig','fb_cover'],premium:['favicon','businesscard','email_sig','letterhead','fb_cover','brandbook_basic','brandbook_ext'],custom:[]};", "var BRAND_BLOCKED={starter:['favicon'],standard:['favicon','businesscard','email_sig'],premium:['favicon','businesscard','email_sig','letterhead','fb_cover','brandbook_basic','brandbook_ext','flyer','icon_set'],custom:[]};", $html);
  $html = str_replace("{key:'brandbook_ext',label:'Brandbook rozszerzony',price:1099,icon:'library',desc:'Rozbudowana księga znaku z większą liczbą przykładów zastosowań.'}", "{key:'brandbook_ext',label:'Brandbook rozszerzony',price:1099,icon:'library',desc:'Rozbudowana księga znaku z większą liczbą przykładów zastosowań.'},{key:'social_templates',label:'Szablony social',price:450,icon:'instagram',desc:'Zestaw szablonów postów/relacji do dalszej komunikacji marki.'},{key:'presentation',label:'Prezentacja PDF',price:650,icon:'presentation',desc:'Krótka prezentacja marki/oferty w estetycznym stylu identyfikacji.'}", $html);
  // Ikony do dodatków — front czyta właściwość icon; fallback zostaje check.
  $html = str_replace("{key:'favicon',label:'Favicon',price:120,desc:'Ikonka strony w karcie przeglądarki, zakładkach i wynikach wyszukiwania.'},", "{key:'favicon',label:'Favicon',price:120,icon:'badge',desc:'Ikonka strony w karcie przeglądarki, zakładkach i wynikach wyszukiwania.'},", $html);
  $html = str_replace("{key:'extra_concept',label:'Dodatkowa koncepcja',price:250,desc:'Jedna dodatkowa propozycja kierunku logo poza zakresem pakietu.'},", "{key:'extra_concept',label:'Dodatkowa koncepcja',price:250,icon:'sparkles',desc:'Jedna dodatkowa propozycja kierunku logo poza zakresem pakietu.'},", $html);
  $html = str_replace("{key:'flyer',label:'Ulotka / flyer',price:350,desc:'Projekt materiału promocyjnego do druku i publikacji online.'},", "{key:'flyer',label:'Ulotka / flyer',price:350,icon:'file-text',desc:'Projekt materiału promocyjnego do druku i publikacji online.'},", $html);
  $html = str_replace("{key:'voucher',label:'Voucher',price:300,desc:'Bon lub kupon prezentowy dopasowany do identyfikacji marki.'},", "{key:'voucher',label:'Voucher',price:300,icon:'ticket',desc:'Bon lub kupon prezentowy dopasowany do identyfikacji marki.'},", $html);
  $html = str_replace("{key:'rollup',label:'Rollup',price:350,desc:'Projekt rollupu na targi, eventy, recepcję lub showroom.'},", "{key:'rollup',label:'Rollup',price:350,icon:'panel-top',desc:'Projekt rollupu na targi, eventy, recepcję lub showroom.'},", $html);
  $html = str_replace("{key:'email_sig',label:'Stopka e-mail',price:150,desc:'Układ stopki mailowej z danymi, logo i estetycznym podpisem.'},", "{key:'email_sig',label:'Stopka e-mail',price:150,icon:'mail',desc:'Układ stopki mailowej z danymi, logo i estetycznym podpisem.'},", $html);
  $html = str_replace("{key:'fix5',label:'Dodatkowe poprawki (5)',price:250,desc:'Pakiet pięciu dodatkowych korekt po wykorzystaniu standardowych rund.'},", "{key:'fix5',label:'Dodatkowe poprawki (5)',price:250,icon:'rotate-ccw',desc:'Pakiet pięciu dodatkowych korekt po wykorzystaniu standardowych rund.'},", $html);
  $html = str_replace("{key:'businesscard',label:'Wizytówka',price:280,desc:'Projekt wizytówki — najczęściej awers i rewers, gotowy do druku.'},", "{key:'businesscard',label:'Wizytówka',price:280,icon:'contact',desc:'Projekt wizytówki — najczęściej awers i rewers, gotowy do druku.'},", $html);
  $html = str_replace("{key:'letterhead',label:'Papier firmowy',price:200,desc:'Szablon A4 do ofert, pism i dokumentów firmowych.'},", "{key:'letterhead',label:'Papier firmowy',price:200,icon:'file',desc:'Szablon A4 do ofert, pism i dokumentów firmowych.'},", $html);
  $html = str_replace("{key:'tshirts',label:'Koszulki',price:300,desc:'Projekt aplikacji logo lub nadruku na odzież firmową.'},", "{key:'tshirts',label:'Koszulki',price:300,icon:'shirt',desc:'Projekt aplikacji logo lub nadruku na odzież firmową.'},", $html);
  $html = str_replace("{key:'fb_cover',label:'Okładka FB',price:300,desc:'Grafika cover na Facebooka dopasowana do stylu marki.'},", "{key:'fb_cover',label:'Okładka FB',price:300,icon:'image',desc:'Grafika cover na Facebooka dopasowana do stylu marki.'},", $html);
  $html = str_replace("{key:'brandbook_basic',label:'Brandbook (basic)',price:799,desc:'Krótsza księga: zasady logo, kolory, typografia i przykłady.'},", "{key:'brandbook_basic',label:'Brandbook (basic)',price:799,icon:'book-open',desc:'Krótsza księga: zasady logo, kolory, typografia i przykłady.'},", $html);
  $html = str_replace("{key:'brandbook_ext',label:'Brandbook rozszerzony',price:1099,desc:'Rozbudowana księga znaku z większą liczbą przykładów zastosowań.'}", "{key:'brandbook_ext',label:'Brandbook rozszerzony',price:1099,icon:'library',desc:'Rozbudowana księga znaku z większą liczbą przykładów zastosowań.'},{key:'social_templates',label:'Szablony social',price:450,icon:'instagram',desc:'Zestaw szablonów postów/relacji do dalszej komunikacji marki.'},{key:'presentation',label:'Prezentacja PDF',price:650,icon:'presentation',desc:'Krótka prezentacja marki/oferty w estetycznym stylu identyfikacji.'}", $html);
  $html = str_replace("'<span class=\"zpbsMiniCard__check\"><i data-lucide=\"check\"></i></span><span class=\"zpbsMiniCard__body\"><strong>'+esc(a.label)+'</strong><small>'+esc(a.desc)+'</small></span><span class=\"zpbsMiniCard__price\">'+money(a.price)+'</span><span class=\"zpbsTipMark\" aria-label=\"Info\">?</span></button>'", "'<span class=\"zpbsMiniCard__check\"><i data-lucide=\"'+esc(a.icon||'plus')+'\"></i></span><span class=\"zpbsMiniCard__body\"><strong>'+esc(a.label)+'</strong><small>'+esc(a.desc)+'</small></span><span class=\"zpbsMiniCard__price\">'+money(a.price)+'</span><span class=\"zpbsTipMark\" aria-label=\"Info\">?</span></button>'", $html);

  $html = str_replace("setText('[data-start-label]',c.start);", "", $html);
  $start_handler = "var startBtn=$('[data-start]'); if(startBtn){startBtn.addEventListener('click',function(){root.dataset.mode='brief'; renderBriefShell(); renderPackages(); renderDynamicSteps(); updateStep(); setTimeout(function(){var target=$('.zpbsSlide.is-active .zpbsPackages')||$('[data-packages]')||$('.zpbsSlide.is-active .zpbsSlideHead')||$('.zpbsBrief'); if(target){var mob=window.matchMedia&&window.matchMedia('(max-width:780px)').matches;window.scrollTo({top:Math.max(0,target.getBoundingClientRect().top+window.pageYOffset-(mob?86:116)),behavior:'smooth'});}},90)});} ";
  $html = str_replace("$('[data-start]').addEventListener('click',function(){root.dataset.mode='brief'; renderBriefShell(); renderPackages(); renderDynamicSteps(); updateStep(); setTimeout(function(){var brief=$('.zpbsBrief'); if(brief)window.scrollTo({top:brief.getBoundingClientRect().top+window.pageYOffset,behavior:'smooth'});},40)});", $start_handler, $html);
  $html = str_replace("$$('[data-service]').forEach(function(btn){btn.addEventListener('click',function(){setService(btn.dataset.service)}); btn.addEventListener('mouseenter',function(){setActiveMock(btn.dataset.preview)}); btn.addEventListener('focus',function(){setActiveMock(btn.dataset.preview)});});", "$$('[data-service]').forEach(function(btn){btn.addEventListener('click',function(){setService(btn.dataset.service); root.dataset.mode='brief'; renderBriefShell(); renderPackages(); renderDynamicSteps(); updateStep(); setTimeout(function(){var target=$('.zpbsSlide.is-active .zpbsPackages')||$('[data-packages]')||$('.zpbsSlide.is-active .zpbsSlideHead')||$('.zpbsBrief'); if(target){var mob=window.matchMedia&&window.matchMedia('(max-width:780px)').matches;window.scrollTo({top:Math.max(0,target.getBoundingClientRect().top+window.pageYOffset-(mob?86:116)),behavior:'smooth'});}},90)}); btn.addEventListener('mouseenter',function(){setActiveMock(btn.dataset.preview)}); btn.addEventListener('focus',function(){setActiveMock(btn.dataset.preview)});});", $html);
  // v2.0.4 — public copy + zero budget + quote notice.
  $html = str_replace('packagePrice:null,budget:6000,', 'packagePrice:null,budget:0,', $html);
  $html = str_replace("state.package=first[0]; state.packageNote=first[1]; state.packagePrice=first[2]; state.budget=s==='ads'?3000:6000;", "state.package=first[0]; state.packageNote=first[1]; state.packagePrice=first[2]; state.budget=0;", $html);
  $html = str_replace('min="2000" max="30000" step="500"', 'min="0" max="30000" step="500"', $html);
  $html = str_replace('min="5000" max="60000" step="1000"', 'min="0" max="60000" step="1000"', $html);
  $html = str_replace('min="1000" max="20000" step="250"', 'min="0" max="20000" step="250"', $html);
  $html = str_replace("setText('[data-final-desc]',state.service==='brand'?'Frontend symuluje zamówienie. Po podpięciu CMS zapiszemy zamówienie, pliki, maile i sumę w panelu.':'Frontend symuluje wysyłkę. Po podpięciu CMS zapiszemy lead, pliki i wyślemy stylowane maile.');", "setText('[data-final-desc]',state.service==='brand'?'Sprawdź wybrany pakiet, termin, dodatki i dane kontaktowe. Po wysłaniu zgłoszenia otrzymasz potwierdzenie na e-mail.':'To jeszcze nie jest zamówienie. Wyślij prośbę o wycenę, a wrócimy z finalnym kosztem i rekomendowanym zakresem w ciągu 48–72 godzin.');", $html);
  $html = str_replace("modal(state.service==='brand'?'Zamówienie gotowe':'Brief gotowy do wysyłki','Frontend działa stabilnie. Kolejny etap to podpięcie AJAX, zapisu w panelu, plików i maili.',true);", "modal(state.service==='brand'?'Zamówienie wysłane':'Prośba o wycenę wysłana',state.service==='brand'?'Dziękujemy — otrzymaliśmy Twoje zgłoszenie. Potwierdzenie trafi również na podany adres e-mail.':'Dziękujemy — otrzymaliśmy brief. Wrócimy z finalnym kosztem i rekomendacją zakresu w ciągu 48–72 godzin.',true);", $html);
  if (strpos($html, 'data-quote-notice') === false) {
    $html = str_replace('<div class="zpbsFinal"><div class="zpbsFinalRows" data-final-summary></div></div></section>', '<div class="zpbsQuoteNotice" data-quote-notice><strong>To jest prośba o wycenę, nie zamówienie.</strong><span>Po analizie briefu wrócimy z finalnym kosztem, rekomendowanym zakresem i kolejnymi krokami w ciągu 48–72 godzin.</span></div><div class="zpbsFinal"><div class="zpbsFinalRows" data-final-summary></div></div></section>', $html);
  }
  // Promo price support for brand packages, preserving 1:1 visual style.
  $html = str_replace("+(state.service==='brand'?'<strong class=\"zpbsPkgPrice\">'+(p[0]==='Mini Branding'?'<small>1999 zł</small>':'')+(p[0]==='Branding Premium'?'<small>3999 zł</small>':'')+money(p[2])+'</strong>':'')+", "+(state.service==='brand'?'<strong class=\"zpbsPkgPrice\">'+((p.promo&&p.promo>0)?'<small>'+money(p.promo)+'</small>':'')+money(p[2])+'</strong>':'')+", $html);
  // If regex did not find because template is different, add JS fallback after shortcode.

  $zpbs_typo_css = '<style id="zpbs-manual-button-typography-v2259">html body #zpbsUltimate .zpbsStickyBtn,html body #zpbsUltimate .zpbsStickyBtn *,html body #zpbsUltimate .zpbsStartBtn,html body #zpbsUltimate .zpbsStartBtn *,html body #zpbsUltimate button,html body #zpbsUltimate button *,html body #zpbsUltimate .zpbsSuccessActions a,html body #zpbsUltimate .zpbsSuccessActions a *{text-transform:none!important;letter-spacing:-.012em!important;font-size:14px!important;font-weight:800!important;line-height:1!important}html body #zpbsUltimate .zpbsStickyBtn{min-height:50px!important}@media(max-width:760px){html body #zpbsUltimate .zpbsStickyBtn,html body #zpbsUltimate .zpbsStickyBtn *,html body #zpbsUltimate button,html body #zpbsUltimate button *{font-size:13px!important;letter-spacing:-.01em!important}}</style>';

  $zpbs_final_css = '<style id="zpbs-v22184-shortcode-final-overrides">html body #zpbsUltimate{position:relative!important;z-index:20!important;overflow:visible!important;isolation:auto!important;transform:none!important;contain:initial!important}html body .elementor-widget-shortcode:has(#zpbsUltimate),html body .elementor-element:has(#zpbsUltimate),html body .e-con:has(#zpbsUltimate),html body .elementor-section:has(#zpbsUltimate){position:relative!important;z-index:auto!important;overflow:visible!important;isolation:auto!important;transform:none!important;contain:initial!important}html body #zpbsUltimate .zpbsSticky{position:fixed!important;z-index:2147483647!important;isolation:isolate!important;will-change:transform,opacity!important;pointer-events:auto!important}html body footer,html body .elementor-location-footer,html body .zpMegaFooter{position:relative!important;z-index:30!important;overflow:visible!important;clip-path:none!important;contain:initial!important}html body .zpMegaFooter,html body .zpMegaFooter *,html body .zpMegaFooter__darkBand,html body .zpMegaFooter__darkBand--cta,html body .zpMegaFooter__inner--cta,html body .zpMegaFooter__ctaGrid,html body .zpMegaFooter__teamPhoto{overflow:visible!important;clip-path:none!important;contain:initial!important}html body .zpMegaFooter__teamPhoto{position:relative!important;z-index:40!important}html body #zpbsUltimate .zpbsBtn,html body #zpbsUltimate .zpbsBtn *,html body #zpbsUltimate .zpbsNav .zpbsBtn,html body #zpbsUltimate .zpbsNav .zpbsBtn *,html body #zpbsUltimate .zpbsStickyBtn,html body #zpbsUltimate .zpbsStickyBtn *,html body #zpbsUltimate .zpbsSmallContact,html body #zpbsUltimate .zpbsSmallContact *,html body #zpbsUltimate .zpbsSuccessActions a,html body #zpbsUltimate .zpbsSuccessActions a *{text-transform:none!important}html body #zpbsUltimate .zpbsNav .zpbsBtn,html body #zpbsUltimate .zpbsNav .zpbsBtn *{letter-spacing:-.01em!important}html body #zpbsUltimate .zpbsNav .zpbsBtn{font-size:15px!important}html body #zpbsUltimate .zpbsNav .zpbsBtn *{font-size:inherit!important}html body #zpbsUltimate .zpbsSmallContact{font-size:13px!important;letter-spacing:-.012em!important;gap:7px!important}html body #zpbsUltimate .zpbsSmallContact svg{width:14px!important;height:14px!important;stroke-width:2.35!important}html body #zpbsUltimate .zpbsTNum{display:flex!important;align-items:center!important;justify-content:center!important;text-align:center!important;text-indent:-1px!important;padding:0 1px 0 0!important}html body #zpbsUltimate .zpbsTItem.is-done .zpbsTNum{text-indent:0!important;padding:0!important}@media(max-width:760px){html body #zpbsUltimate .zpbsNav .zpbsBtn{font-size:14px!important}html body #zpbsUltimate .zpbsSmallContact{font-size:13px!important}html body #zpbsUltimate .zpbsSticky{z-index:2147483647!important}}</style>';
  $ajax_js = zp_studio_front_inline_js();
  $zpbs_header_fix_css = <<<'HTML'
<style id="zp-studio-header-clean-v308">html body.zpbs-studio-page header#zpNewNav,html body.zpbs-studio-page header#zpNewNav .zpNewNav__shell,html body.zpbs-studio-page header#zpNewNav .zpNewNav__mobileBar{box-shadow:none!important;-webkit-box-shadow:none!important;filter:none!important;border-bottom:0!important}html body.zpbs-studio-page.zpbs-studio-chooser header#zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open),html body.zpbs-studio-page.zpbs-studio-chooser header#zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) > .zpNewNav__shell,html body.zpbs-studio-page.zpbs-studio-chooser header#zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__shell{background:rgba(255,255,255,.94)!important;background-color:rgba(255,255,255,.94)!important;background-image:none!important;box-shadow:none!important;-webkit-box-shadow:none!important;filter:none!important;border-bottom:1px solid rgba(7,17,31,.08)!important}html body.zpbs-studio-page.zpbs-studio-chooser header#zpNewNav:not(.is-scrolled):not(.is-mega-open) .zpNewNav__logo--light,html body.zpbs-studio-page.zpbs-studio-chooser header#zpNewNav:not(.is-scrolled):not(.is-mega-open) .zpNewNav__mobileLogo--light{display:none!important;opacity:0!important;visibility:hidden!important}html body.zpbs-studio-page.zpbs-studio-chooser header#zpNewNav:not(.is-scrolled):not(.is-mega-open) .zpNewNav__logo--dark,html body.zpbs-studio-page.zpbs-studio-chooser header#zpNewNav:not(.is-scrolled):not(.is-mega-open) .zpNewNav__mobileLogo--dark{display:block!important;opacity:1!important;visibility:visible!important}html body.zpbs-studio-page.zpbs-studio-chooser header#zpNewNav:not(.is-scrolled):not(.is-mega-open) .zpNewNav__link,html body.zpbs-studio-page.zpbs-studio-chooser header#zpNewNav:not(.is-scrolled):not(.is-mega-open) .zpNewNav__link span,html body.zpbs-studio-page.zpbs-studio-chooser header#zpNewNav:not(.is-scrolled):not(.is-mega-open) .zpNewNav__caret,html body.zpbs-studio-page.zpbs-studio-chooser header#zpNewNav:not(.is-scrolled):not(.is-mega-open) .zpNewNav__round,html body.zpbs-studio-page.zpbs-studio-chooser header#zpNewNav:not(.is-scrolled):not(.is-mega-open) .zpNewNav__mobileSearch,html body.zpbs-studio-page.zpbs-studio-chooser header#zpNewNav:not(.is-scrolled):not(.is-mega-open) .zpNewNav__burger,html body.zpbs-studio-page.zpbs-studio-chooser header#zpNewNav:not(.is-scrolled):not(.is-mega-open) .zpNewNav__mobileSearch svg,html body.zpbs-studio-page.zpbs-studio-chooser header#zpNewNav:not(.is-scrolled):not(.is-mega-open) .zpNewNav__burger svg,html body.zpbs-studio-page.zpbs-studio-chooser header#zpNewNav:not(.is-scrolled):not(.is-mega-open) .zpNewNav__contactIcon,html body.zpbs-studio-page.zpbs-studio-chooser header#zpNewNav:not(.is-scrolled):not(.is-mega-open) .zpNewNav__contactIcon svg{color:#071426!important;stroke:#071426!important}html body.zpbs-studio-page.zpbs-studio-chooser header#zpNewNav:not(.is-scrolled):not(.is-mega-open) .zpNewNav__divider{background:rgba(16,24,40,.10)!important}html body.zpbs-studio-page.zpbs-studio-brief header#zpNewNav:not(.is-mega-open),html body.zpbs-studio-page.zpbs-studio-brief header#zpNewNav:not(.is-mega-open) > .zpNewNav__shell,html body.zpbs-studio-page.zpbs-studio-brief header#zpNewNav:not(.is-mega-open) .zpNewNav__shell,html body.zpbs-studio-page header#zpNewNav.is-scrolled:not(.is-mega-open) > .zpNewNav__shell,html body.zpbs-studio-page header#zpNewNav.is-scrolled:not(.is-mega-open) .zpNewNav__shell{background:rgba(255,255,255,.94)!important;background-color:rgba(255,255,255,.94)!important;background-image:none!important;box-shadow:none!important;-webkit-box-shadow:none!important;filter:none!important;border-bottom:0!important}html body.zpbs-studio-page.zpbs-studio-brief header#zpNewNav:not(.is-mega-open) .zpNewNav__logo--light,html body.zpbs-studio-page.zpbs-studio-brief header#zpNewNav:not(.is-mega-open) .zpNewNav__mobileLogo--light,html body.zpbs-studio-page header#zpNewNav.is-scrolled:not(.is-mega-open) .zpNewNav__logo--light,html body.zpbs-studio-page header#zpNewNav.is-scrolled:not(.is-mega-open) .zpNewNav__mobileLogo--light{display:none!important;opacity:0!important;visibility:hidden!important}html body.zpbs-studio-page.zpbs-studio-brief header#zpNewNav:not(.is-mega-open) .zpNewNav__logo--dark,html body.zpbs-studio-page.zpbs-studio-brief header#zpNewNav:not(.is-mega-open) .zpNewNav__mobileLogo--dark,html body.zpbs-studio-page header#zpNewNav.is-scrolled:not(.is-mega-open) .zpNewNav__logo--dark,html body.zpbs-studio-page header#zpNewNav.is-scrolled:not(.is-mega-open) .zpNewNav__mobileLogo--dark{display:block!important;opacity:1!important;visibility:visible!important}html body.zpbs-studio-page.zpbs-studio-brief header#zpNewNav:not(.is-mega-open) .zpNewNav__link,html body.zpbs-studio-page.zpbs-studio-brief header#zpNewNav:not(.is-mega-open) .zpNewNav__link span,html body.zpbs-studio-page.zpbs-studio-brief header#zpNewNav:not(.is-mega-open) .zpNewNav__caret,html body.zpbs-studio-page.zpbs-studio-brief header#zpNewNav:not(.is-mega-open) .zpNewNav__round,html body.zpbs-studio-page.zpbs-studio-brief header#zpNewNav:not(.is-mega-open) .zpNewNav__mobileSearch,html body.zpbs-studio-page.zpbs-studio-brief header#zpNewNav:not(.is-mega-open) .zpNewNav__burger,html body.zpbs-studio-page.zpbs-studio-brief header#zpNewNav:not(.is-mega-open) .zpNewNav__mobileSearch svg,html body.zpbs-studio-page.zpbs-studio-brief header#zpNewNav:not(.is-mega-open) .zpNewNav__burger svg{color:#071426!important;stroke:#071426!important}@media(max-width:1200px){html body.zpbs-studio-page.zpbs-studio-chooser header#zpNewNav:not(.is-scrolled):not(.is-mega-open) .zpNewNav__consultBtn,html body.zpbs-studio-page.zpbs-studio-chooser header#zpNewNav:not(.is-scrolled):not(.is-mega-open) .zpNewNav__mobileCall{background:rgba(255,255,255,.62)!important;border-color:rgba(7,17,31,.11)!important;color:#071426!important;box-shadow:none!important}html body.zpbs-studio-page.zpbs-studio-brief header#zpNewNav:not(.is-mega-open) .zpNewNav__consultBtn,html body.zpbs-studio-page.zpbs-studio-brief header#zpNewNav:not(.is-mega-open) .zpNewNav__mobileCall,html body.zpbs-studio-page header#zpNewNav.is-scrolled:not(.is-mega-open) .zpNewNav__consultBtn,html body.zpbs-studio-page header#zpNewNav.is-scrolled:not(.is-mega-open) .zpNewNav__mobileCall{background:rgba(255,255,255,.62)!important;border-color:rgba(7,17,31,.11)!important;color:#071426!important;box-shadow:none!important}html body.zpbs-studio-page.zpbs-studio-brief header#zpNewNav:not(.is-mega-open) .zpNewNav__consultBtn *,html body.zpbs-studio-page.zpbs-studio-brief header#zpNewNav:not(.is-mega-open) .zpNewNav__mobileCall *,html body.zpbs-studio-page header#zpNewNav.is-scrolled:not(.is-mega-open) .zpNewNav__consultBtn *,html body.zpbs-studio-page header#zpNewNav.is-scrolled:not(.is-mega-open) .zpNewNav__mobileCall *{color:#071426!important;stroke:#071426!important}}</style>
<script id="zp-studio-header-clean-v308-js">
(function(){
  var root=document.getElementById('zpbsUltimate');
  if(!root || !document.body) return;
  function syncStudioHeader(){
    var nav=document.getElementById('zpNewNav');
    var brief=root.getAttribute('data-mode')==='brief';
    document.body.classList.add('zpbs-studio-page');
    document.body.classList.toggle('zpbs-studio-brief', brief);
    document.body.classList.toggle('zpbs-studio-chooser', !brief);
    if(nav){
      nav.classList.add('zpNewNav--heroOverlay');
      nav.classList.toggle('zpNewNav--studioChooser', !brief);
      nav.classList.toggle('zpNewNav--studioBrief', brief);
    }
  }
  syncStudioHeader();
  try{ new MutationObserver(syncStudioHeader).observe(root,{attributes:true,attributeFilter:['data-mode']}); }catch(e){}
  document.addEventListener('click', function(){ setTimeout(syncStudioHeader,40); setTimeout(syncStudioHeader,180); }, true);
  window.addEventListener('scroll', syncStudioHeader, {passive:true});
  window.addEventListener('pageshow', syncStudioHeader);
})();
</script>
HTML;
$zp_chooser_contact = '';
  if (shortcode_exists('zp_home_contact_form')) {
    $zp_chooser_contact = '<div class="zpStudioChooserContact" data-zp-studio-chooser-contact>'
      . do_shortcode('[zp_home_contact_form]')
      . '</div>';
  }
  $zp_chooser_contact_css = '<style id="zp-studio-chooser-contact-v2619">'
    . 'body.zpbs-studio-page .zpStudioChooserContact{display:block!important;position:relative!important;z-index:10!important;width:100%!important;}'
    . 'body.zpbs-studio-page.zpbs-studio-brief .zpStudioChooserContact{display:none!important;}'
    . 'body.zpbs-studio-page.zpbs-studio-chooser .zpStudioChooserContact{display:block!important;}'
    . '</style>';
  $zp_boot_js = <<<'HTML'
<script id="zp-studio-boot-v2631">
(function(){
  var done=false;
  function boot(){
    if(done) return;
    var root=document.getElementById('zpbsUltimate');
    if(!root) return;
    done=true;
    requestAnimationFrame(function(){
      requestAnimationFrame(function(){
        requestAnimationFrame(function(){
          try{
            if(window.lucide && typeof window.lucide.createIcons==='function'){
              window.lucide.createIcons({attrs:{'stroke-width':2,'stroke-linecap':'round','stroke-linejoin':'round','aria-hidden':'true'}});
            }
          }catch(e){}
          root.setAttribute('data-zp-boot','1');
          try{window.dispatchEvent(new CustomEvent('zpbs:boot'));}catch(e){var ev=document.createEvent('Event');ev.initEvent('zpbs:boot',true,true);window.dispatchEvent(ev);}
        });
      });
    });
  }
  if(document.readyState==='loading') document.addEventListener('DOMContentLoaded',boot,{once:true});
  else boot();
  window.addEventListener('pageshow',boot,{once:true});
  setTimeout(boot,1600);
})();
</script>
HTML;
  return $html . $zpbs_typo_css . $ajax_js . $zpbs_final_css . $zpbs_header_fix_css . $zp_chooser_contact . $zp_chooser_contact_css . $zp_boot_js;
}
add_shortcode('zp_studio_wyceny', 'zp_studio_render_shortcode');
add_shortcode('zp_studio_wyceny_cms', 'zp_studio_render_shortcode');

function zp_studio_front_inline_js(){
  $nonce = wp_create_nonce('zp_studio_submit');
  $domain_nonce = wp_create_nonce('zp_studio_domain_check');
  $ajax = admin_url('admin-ajax.php');
  $home = home_url('/');

  $css = '<style id="zp-studio-runtime-v214">@media(max-width:780px){body.zpbs-studio-page .zpMobileStickyCta,body.zpbs-studio-page #zpMobileStickyCta,body.zpbs-studio-page .zpStickyEstimate,body.zpbs-studio-page #zpStickyEstimate,body.zpbs-studio-page .mobile-contact-bar,body.zpbs-studio-page .floating-contact,body.zpbs-studio-page .floating-chat,body.zpbs-studio-page [id*="lefty"],body.zpbs-studio-page [class*="lefty"],body.zpbs-studio-page [id*="chat"],body.zpbs-studio-page [class*="chat"],body.zpbs-studio-page [id*="scroll-top"],body.zpbs-studio-page [class*="scroll-top"]{display:none!important;visibility:hidden!important;opacity:0!important;pointer-events:none!important}#zpbsUltimate .zpbsTitle,#zpbsUltimate .zpbsChoose h1{font-size:calc(clamp(35px,11vw,46px) - 5px)!important}#zpbsUltimate .zpbsStickyActions{display:grid!important;grid-template-columns:1fr 1fr!important;gap:10px!important;width:100%!important}#zpbsUltimate .zpbsStickyBtn[data-back-to-chooser]{display:none!important}#zpbsUltimate .zpbsStickyBtn{min-height:48px!important;display:inline-flex!important;flex-direction:row!important;gap:8px!important;font-size:11px!important;padding:0 12px!important;white-space:nowrap!important;letter-spacing:.05em!important}#zpbsUltimate .zpbsStickyBtn svg{width:18px!important;height:18px!important}#zpbsUltimate .zpbsStickyBtn:not(.zpbsStickyBtnPrimary){background:transparent!important;color:#fff!important;border:1px solid rgba(255,255,255,.22)!important}#zpbsUltimate .zpbsStickyBtnPrimary{background:#fff!important;color:#071426!important;border-color:#fff!important}#zpbsUltimate .zpbsStickyIn{grid-template-columns:44px minmax(0,1fr)!important;gap:12px!important;padding:12px!important}#zpbsUltimate .zpbsStickyActions{grid-column:1/-1!important}#zpbsUltimate .zpbsMiniCard__body small,#zpbsUltimate .zpbsPkgText,#zpbsUltimate .zpbsServiceText span{white-space:normal!important;overflow:visible!important;text-overflow:clip!important;display:block!important;overflow-wrap:anywhere!important}#zpbsUltimate .zpbsChoosePkgCue{margin:12px 0 16px!important;min-height:28px!important}}body.zpbs-studio-page #zpbsUltimate{margin-top:0!important}body.zpbs-studio-page #zpbsUltimate .zpbsChoose{padding-top:clamp(120px,8vw,160px)!important;margin-top:-1px!important}body.zpbs-studio-page.zpbs-studio-brief #zpbsUltimate .zpbsChoose{padding-top:calc(clamp(120px,8vw,160px) + 30px)!important}#zpbsUltimate .zpbsPkgPrice{background:linear-gradient(90deg,#05070b,#102a4f,#1c477a)!important;-webkit-background-clip:text!important;background-clip:text!important;-webkit-text-fill-color:transparent!important;color:#102a4f!important}#zpbsUltimate .zpbsPkg.is-on .zpbsPkgPrice{-webkit-text-fill-color:#fff!important;color:#fff!important;background:none!important}#zpbsUltimate .zpbsBudget.is-budget-unset{display:none!important}#zpbsUltimate .zpbsBudgetUnset{margin:12px 0 0!important;display:inline-flex!important;align-items:center!important;gap:10px!important;min-height:38px!important;padding:0 13px!important;border-radius:999px!important;background:#fff!important;border:1px solid rgba(7,17,31,.12)!important;color:#071426!important;font-size:12px!important;font-weight:850!important;cursor:pointer!important}#zpbsUltimate .zpbsBudgetUnset input{appearance:none!important;width:18px!important;height:18px!important;border-radius:6px!important;border:1px solid rgba(7,17,31,.22)!important;background:#f6f8fb!important;margin:0!important;position:relative!important;display:block!important}#zpbsUltimate .zpbsBudgetUnset input:checked{background:#071426!important;border-color:#071426!important}#zpbsUltimate .zpbsBudgetUnset input:checked:after{content:""!important;position:absolute!important;left:5px!important;top:2px!important;width:5px!important;height:10px!important;border-right:2px solid #fff!important;border-bottom:2px solid #fff!important;transform:rotate(45deg)!important}#zpbsUltimate .zpSummaryRemoveList{display:flex!important;flex-wrap:wrap!important;gap:8px!important;justify-content:flex-end!important;max-width:100%!important}#zpbsUltimate .zpSummaryRemoveItem{display:inline-flex!important;align-items:center!important;gap:7px!important;min-height:32px!important;padding:0 2px 0 11px!important;border-radius:999px!important;background:#f5f7fa!important;border:1px solid rgba(7,17,31,.10)!important;color:#071426!important;font-size:12px!important;font-weight:800!important;line-height:1.1!important;max-width:100%!important;white-space:normal!important}#zpbsUltimate .zpSummaryRemoveItem b{font-weight:850!important;white-space:normal!important;overflow-wrap:anywhere!important}#zpbsUltimate .zpSummaryRemoveItem em{font-style:normal!important;color:#667386!important;font-size:11px!important;margin-left:2px!important}#zpbsUltimate .zpSummaryRemoveItem i{display:inline!important;background:transparent!important;color:#071426!important;font-style:normal!important;font-size:20px!important;line-height:1!important;font-weight:900!important;padding:0 8px!important;border:0!important;width:auto!important;height:auto!important}#zpbsUltimate .zpbsFinalTotal{margin:18px 0 0!important;padding:24px 28px!important;border-radius:26px!important;background:linear-gradient(135deg,#05070b,#071426 58%,#102a4f)!important;color:#fff!important;display:grid!important;gap:8px!important}#zpbsUltimate .zpbsFinalTotal span{font-size:11px!important;text-transform:uppercase!important;letter-spacing:.15em!important;color:rgba(255,255,255,.58)!important;font-weight:900!important}#zpbsUltimate .zpbsFinalTotal strong{font-size:clamp(36px,4vw,58px)!important;line-height:.95!important;letter-spacing:-.05em!important;color:#fff!important}#zpbsUltimate .zpbsTItem.is-done .zpbsTNum{background:linear-gradient(135deg,#1abf69,#0b8f52 58%,#096b40)!important;color:#fff!important;border-color:transparent!important;display:grid!important;place-items:center!important;line-height:1!important}#zpbsUltimate .zpbsTItem.is-done .zpbsTNum svg{width:22px!important;height:22px!important;margin:0!important;stroke-width:3!important;position:static!important;transform:none!important}#zpbsUltimate .zpStudioFilePreview{margin-top:10px!important;display:flex!important;flex-wrap:wrap!important;gap:8px!important}#zpbsUltimate .zpStudioFilePreview span{display:inline-flex!important;align-items:center!important;gap:7px!important;min-height:34px!important;padding:0 11px!important;border-radius:999px!important;background:#f4f7fb!important;border:1px solid rgba(7,17,31,.10)!important;color:#071426!important;font-size:11px!important;font-weight:800!important}#zpbsUltimate .zpStudioFilePreview img{width:24px!important;height:24px!important;border-radius:7px!important;object-fit:cover!important;display:block!important}#zpbsUltimate .zpbsDomainChoose{min-height:32px!important;padding:0 12px!important;border-radius:999px!important;background:#071426!important;color:#fff!important;font-size:10px!important;font-weight:900!important;text-transform:uppercase!important;letter-spacing:.06em!important}#zpbsUltimate .zpbsDomainRow.is-picked{border-color:#071426!important;background:#f4f7fb!important}#zpbsUltimate .zpbsUxToast{position:fixed!important;left:50%!important;top:22px!important;z-index:1000006!important;transform:translate(-50%,-18px)!important;opacity:0!important;pointer-events:none!important;max-width:min(520px,calc(100% - 30px))!important;padding:14px 18px!important;border-radius:999px!important;background:#071426!important;color:#fff!important;box-shadow:0 18px 60px rgba(7,20,38,.24)!important;font-size:13px!important;font-weight:850!important;transition:opacity .25s ease,transform .25s ease!important;text-align:center!important}#zpbsUltimate .zpbsUxToast.is-on{opacity:1!important;transform:translate(-50%,0)!important}#zpbsUltimate .zpbsSuccessScreen{position:fixed!important;inset:0!important;z-index:1000005!important;display:flex!important;align-items:center!important;justify-content:center!important;padding:22px!important;background:rgba(5,7,11,.62)!important;backdrop-filter: none;-webkit-backdrop-filter:none saturate(155%)!important}#zpbsUltimate .zpbsSuccessCard{width:min(620px,94vw)!important;border-radius:34px!important;background:#fff!important;color:#071426!important;padding:clamp(28px,4vw,46px)!important;text-align:center!important;box-shadow:0 30px 100px rgba(0,0,0,.32)!important;border:1px solid rgba(255,255,255,.70)!important;display:block!important}#zpbsUltimate .zpbsSuccessIcon{width:72px!important;height:72px!important;display:grid!important;place-items:center!important;margin:0 auto 20px!important;border-radius:26px!important;background:linear-gradient(135deg,#18bf68,#0a7e48)!important;color:#fff!important;box-shadow:0 20px 40px rgba(10,126,72,.22)!important}#zpbsUltimate .zpbsSuccessIcon svg{width:31px!important;height:31px!important;stroke-width:3!important}#zpbsUltimate .zpbsSuccessCard span{display:block!important;color:#7a8493!important;text-transform:uppercase!important;font-size:10px!important;font-weight:900!important;letter-spacing:.16em!important;margin-bottom:10px!important}#zpbsUltimate .zpbsSuccessCard h3{margin:0!important;color:#05070b!important;font-size:clamp(31px,4.6vw,54px)!important;line-height:.98!important;letter-spacing:-.052em!important;font-weight:760!important}#zpbsUltimate .zpbsSuccessCard p{max-width:500px!important;margin:16px auto 24px!important;color:#667386!important;font-size:15px!important;line-height:1.66!important}#zpbsUltimate .zpbsSuccessActions{display:flex!important;gap:10px!important;justify-content:center!important;flex-wrap:wrap!important}#zpbsUltimate .zpbsSuccessActions a,#zpbsUltimate .zpbsSuccessClose{min-height:48px!important;display:inline-flex!important;align-items:center!important;justify-content:center!important;padding:0 18px!important;border-radius:999px!important;background:#fff!important;color:#071426!important;font-size:12px!important;font-weight:900!important;text-transform:uppercase!important;letter-spacing:.06em!important;border:1px solid rgba(7,17,31,.14)!important;text-decoration:none!important}#zpbsUltimate .zpbsSuccessClose,#zpbsUltimate .zpbsSuccessActions a:first-child{background:linear-gradient(90deg,#1c477a,#102a4f,#071426,#05070b)!important;color:#fff!important;border-color:#071426!important}@media(max-width:760px){#zpbsUltimate .zpbsSuccessActions{display:grid!important;grid-template-columns:1fr!important}#zpbsUltimate .zpbsSuccessActions a,#zpbsUltimate .zpbsSuccessClose{width:100%!important}}</style>';

  $js = '<script id="zp-studio-runtime-v214-js">(function(){
    var ajax={ajax:"'.esc_js($ajax).'",nonce:"'.esc_js($nonce).'",domainNonce:"'.esc_js($domain_nonce).'"};
    var HOME="'.esc_js($home).'";
    window.zpbsStudioAjax=ajax;
    function root(){return document.getElementById("zpbsUltimate")}
    function qs(s,c){return (c||document).querySelector(s)}
    function qsa(s,c){return Array.prototype.slice.call((c||document).querySelectorAll(s))}
    function esc(s){return (s||"").toString().replace(/[&<>"]/g,function(m){return m==="&"?"&amp;":m==="<"?"&lt;":m===">"?"&gt;":"&quot;"})}
    function money(n){n=parseInt(String(n||0).replace(/[^0-9]/g,""),10)||0;return n.toLocaleString("pl-PL")+" zł"}
    function parsePrice(txt){return parseInt(String(txt||"").replace(/[^0-9]/g,""),10)||0}
    function luc(){try{window.lucide&&window.lucide.createIcons&&window.lucide.createIcons()}catch(e){}}
    function rowLabel(row){var s=qs(":scope > span",row)||qs("span",row);return s?s.textContent.trim():""}
    function rowStrong(row){return qs(":scope > strong",row)||qs("strong",row)}
    function setRow(container,label,value,html){if(!container||!label)return;value=(value||"").toString().trim();var rows=qsa(":scope > .zpbsMailRow,:scope > .zpbsFinalRow",container);var found=rows.filter(function(r){return rowLabel(r)===label})[0];if(!value){if(found)found.remove();return;}if(!found){found=document.createElement("div");found.className=container.matches("[data-mail-rows]")?"zpbsMailRow":"zpbsFinalRow";found.innerHTML="<span></span><strong></strong>";container.appendChild(found);}qs(":scope > span",found).textContent=label;var st=rowStrong(found);if(html){st.innerHTML=value}else{st.textContent=value}}
    function selected(type,r){return qsa("[data-chip=\""+type+"\"].is-on",r).map(function(x){return {label:(x.dataset.value||"").trim(),el:x,price:parsePrice((qs(".zpbsMiniCard__price",x)||{}).textContent||"")}}).filter(function(x){return x.label})}
    function service(r){return (r&&r.dataset.service)||"web"}
    function toast(msg){var r=root(); if(!r||!msg)return; var box=qs(".zpbsUxToast",r); if(!box){box=document.createElement("div");box.className="zpbsUxToast";r.appendChild(box)} box.textContent=msg; box.classList.add("is-on"); clearTimeout(box._t); box._t=setTimeout(function(){box.classList.remove("is-on")},2600)}
    function ensureBudgetUnset(r){qsa(".zpbsBudget",r).forEach(function(budget){var section=budget.closest(".zpbsSection")||budget.parentNode;if(budget.dataset.zpUnsetReady==="1")return;var hidden=qs("[data-field=\"budgetMode\"]",r);if(!hidden){hidden=document.createElement("input");hidden.type="hidden";hidden.setAttribute("data-field","budgetMode");r.appendChild(hidden)}var existing=section?qs("[data-budget-unset]",section):null;if(existing){budget.dataset.zpUnsetReady="1";budget.dataset.zpManual="1";if(existing.dataset.zpUnsetBound!=="1"){existing.dataset.zpUnsetBound="1";existing.addEventListener("change",function(){budget.classList.toggle("is-budget-unset",existing.checked);hidden.value=existing.checked?"Nie mam ustalonego budżetu":""; if(existing.checked){setRow(qs("[data-mail-rows]",r),"Budżet","Nie mam ustalonego budżetu");setRow(qs("[data-final-summary]",r),"Budżet","Nie mam ustalonego budżetu")} syncSummary()})}return;}budget.dataset.zpUnsetReady="1";budget.dataset.zpManual="1";var label=document.createElement("label");label.className="zpbsBudgetUnset";label.innerHTML="<input type=\"checkbox\" data-budget-unset> Nie mam ustalonego budżetu";budget.parentNode.insertBefore(label,budget.nextSibling);var cb=qs("[data-budget-unset]",label);cb.dataset.zpUnsetBound="1";cb.addEventListener("change",function(){budget.classList.toggle("is-budget-unset",cb.checked);hidden.value=cb.checked?"Nie mam ustalonego budżetu":""; if(cb.checked){setRow(qs("[data-mail-rows]",r),"Budżet","Nie mam ustalonego budżetu");setRow(qs("[data-final-summary]",r),"Budżet","Nie mam ustalonego budżetu")} syncSummary()})})}
    function addonHTML(items,withPrice){return "<span class=\"zpSummaryRemoveList\">"+items.map(function(it){return "<button type=\"button\" class=\"zpSummaryRemoveItem\" data-zp-remove-type=\""+esc(it.type||"addons")+"\" data-zp-remove-label=\""+esc(it.label)+"\"><b>"+esc(it.label)+"</b>"+(withPrice&&it.price?"<em>"+money(it.price)+"</em>":"")+"<i aria-hidden=\"true\">×</i></button>"}).join("")+"</span>"}
    function currentPackagePrice(r){var pkg=qs(".zpbsPkg.is-on",r); if(!pkg)return 0; return parseInt(pkg.dataset.price||"0",10)||parsePrice((qs(".zpbsPkgPrice",pkg)||{}).textContent||"")}
    function termPrice(r){var t=qs("[data-chip=\"brandTerm\"].is-on",r);return parsePrice((qs(".zpbsMiniCard__price",t)||{}).textContent||"")}
    function updateTotals(r){qsa(".zpbsFinalTotal",r).forEach(function(x){x.remove()}); if(service(r)!=="brand")return; var sum=currentPackagePrice(r)+termPrice(r)+selected("addons",r).reduce(function(a,b){return a+b.price},0); setRow(qs("[data-mail-rows]",r),"Suma",money(sum)); setRow(qs("[data-final-summary]",r),"Suma",money(sum)); var fr=qs("[data-final-summary]",r); if(fr){var div=document.createElement("div");div.className="zpbsFinalTotal";div.innerHTML="<span>Łącznie</span><strong>"+money(sum)+"</strong>";fr.appendChild(div)}}
    function syncSummary(){var r=root(); if(!r)return; ensureBudgetUnset(r); var unset=!!qs("[data-budget-unset]:checked",r); if(unset){setRow(qs("[data-mail-rows]",r),"Budżet","Nie mam ustalonego budżetu");setRow(qs("[data-final-summary]",r),"Budżet","Nie mam ustalonego budżetu")} if(service(r)==="brand"){setRow(qs("[data-mail-rows]",r),"Budżet","");setRow(qs("[data-final-summary]",r),"Budżet","")} var addons=selected("addons",r).map(function(x){x.type="addons";return x}); var features=selected("features",r).map(function(x){x.type="features";return x}); var mats=selected("materials",r).map(function(x){x.type="materials";return x}); [["Dodatki",addons,true],["Funkcje",features,false],["Materiały",mats,false]].forEach(function(row){[qs("[data-mail-rows]",r),qs("[data-final-summary]",r)].forEach(function(c){if(!c)return;if(!row[1].length)setRow(c,row[0],"");else setRow(c,row[0],addonHTML(row[1],row[2]&&service(r)==="brand"),true)})}); var pd=(qs("[data-field=\"preferredDomain\"]",r)||{}).value||""; setRow(qs("[data-mail-rows]",r),"Preferowane domeny",pd); setRow(qs("[data-final-summary]",r),"Preferowane domeny",pd); updateTotals(r); luc()}
    function removeSelection(type,label){var r=root(); if(!r)return; var chip=qsa("[data-chip=\""+type+"\"].is-on",r).filter(function(c){return (c.dataset.value||"").trim()===label})[0]; if(chip){chip.click();toast("Usunięto: "+label)} setTimeout(syncSummary,120)}
    document.addEventListener("click",function(e){var b=e.target.closest("#zpbsUltimate [data-zp-remove-label]"); if(!b)return; e.preventDefault(); e.stopPropagation(); if(e.stopImmediatePropagation)e.stopImmediatePropagation(); removeSelection(b.getAttribute("data-zp-remove-type")||"addons",b.getAttribute("data-zp-remove-label")||"")},true);
    function filePreview(r){var input=qs("#zpbsUFiles",r); if(!input||input.dataset.zpPreviewReady==="1")return; input.dataset.zpPreviewReady="1"; var box=document.createElement("div"); box.className="zpStudioFilePreview"; var upload=qs(".zpbsUpload",r); if(upload)upload.appendChild(box); input.addEventListener("change",function(){box.innerHTML="";Array.prototype.slice.call(input.files||[]).slice(0,12).forEach(function(f){var item=document.createElement("span"); if(/^image\//.test(f.type)){var img=document.createElement("img");img.src=URL.createObjectURL(f);item.appendChild(img)} item.appendChild(document.createTextNode(f.name));box.appendChild(item)})})}
    function enhanceDomains(r){qsa("[data-domain-box]",r).forEach(function(box){if(box.dataset.zpDomainReady==="1")return;box.dataset.zpDomainReady="1";box.addEventListener("click",function(e){var pick=e.target.closest("[data-domain-pick]"); if(!pick)return; e.preventDefault(); var val=pick.getAttribute("data-domain-pick")||""; var input=qs("[data-field=\"preferredDomain\"]",r); if(!input){input=document.createElement("input");input.type="hidden";input.setAttribute("data-field","preferredDomain");r.appendChild(input)} var arr=(input.value||"").split(",").map(function(x){return x.trim()}).filter(Boolean); if(arr.indexOf(val)<0)arr.push(val); input.value=arr.join(", "); qsa("[data-domain-row]",r).forEach(function(row){row.classList.toggle("is-picked",arr.indexOf(row.getAttribute("data-domain-row"))>=0)}); toast("Dodano domenę do zgłoszenia"); syncSummary()},true)})}
    function collectPayload(r){syncSummary(); var payload={}; qsa("[data-mail-rows] .zpbsMailRow,[data-final-summary] .zpbsFinalRow",r).forEach(function(row){var k=qs(":scope > span",row),v=qs(":scope > strong",row); if(!k||!v)return; var bs=qsa("[data-zp-remove-label] b",v); var val=bs.length?bs.map(function(b){return b.textContent.trim()}).join(", "):v.textContent.trim(); if(val)payload[k.textContent.trim()]=val}); qsa("[data-field]",r).forEach(function(el){var key=el.getAttribute("data-field"); if(!key||el.type==="file")return; var val=(el.value||"").trim(); if(!val)return; var map={name:"Imię i nazwisko",email:"E-mail",phone:"Telefon",company:"Firma",links:"Linki / inspiracje",message:"Brief",preferredDomain:"Preferowane domeny",domainIdea:"Pomysł na domenę",budgetMode:"Budżet",industry:"Branża",goal:"Cel",pages:"Podstrony",website:"Strona",geo:"Obszar",brandName:"Marka",slogan:"Slogan",uses:"Zastosowania"}; payload[map[key]||key]=val}); return payload}
    function showSuccess(){var r=root(); if(!r)return; qsa(".zpbsSuccessScreen",r).forEach(function(x){x.remove()}); var div=document.createElement("div"); div.className="zpbsSuccessScreen is-on"; div.innerHTML="<div class=\"zpbsSuccessCard\"><div class=\"zpbsSuccessIcon\"><i data-lucide=\"check\"></i></div><span>Studio Wyceny</span><h3>Dziękujemy — zgłoszenie zostało wysłane.</h3><p>Otrzymaliśmy brief. Potwierdzenie trafi na podany adres e-mail, a my wrócimy z odpowiedzią w ciągu 48–72 godzin.</p><div class=\"zpbsSuccessActions\"><a href=\"tel:+48501054253\">Zadzwoń</a><a href=\"https://wa.me/48501054253\" target=\"_blank\" rel=\"noopener\">WhatsApp</a><a href=\"/realizacje/\">Zobacz realizacje</a></div><button type=\"button\" class=\"zpbsSuccessClose\">Powrót na stronę główną</button></div>"; r.appendChild(div); luc(); var close=qs(".zpbsSuccessClose",div); if(close)close.addEventListener("click",function(){window.location.href=HOME}); setTimeout(function(){window.location.href=HOME},3200)}
    function sendFinal(e){var r=root(); if(!r||r.getAttribute("data-step")!=="5")return; var btn=e.target.closest("#zpbsUltimate [data-next],#zpbsUltimate [data-sticky-next]"); if(!btn)return; e.preventDefault(); e.stopPropagation(); if(e.stopImmediatePropagation)e.stopImmediatePropagation(); if(r.dataset.zpSubmitting==="1"||r.dataset.zpSent==="1")return; var payload=collectPayload(r); var email=(payload["E-mail"]||"").trim(); if(!email||email.indexOf("@")<1){toast("Uzupełnij poprawny e-mail");return} r.dataset.zpSubmitting="1"; var modal=qs("[data-modal]",r),title=qs("[data-modal-title]",r),text=qs("[data-modal-text]",r); if(modal)modal.classList.add("is-on"); if(title)title.textContent="Wysyłamy zgłoszenie…"; if(text)text.textContent="Zapisujemy zgłoszenie, załączniki i wysyłamy potwierdzenie e-mail z PDF."; var fd=new FormData(); fd.append("action","zp_studio_submit"); fd.append("nonce",ajax.nonce||""); fd.append("payload",JSON.stringify(payload)); var fi=qs("#zpbsUFiles",r); if(fi&&fi.files){Array.prototype.forEach.call(fi.files,function(f){fd.append("files[]",f,f.name)})} (window.zpSuiteSecureFormData?window.zpSuiteSecureFormData(fd):Promise.resolve(fd)).then(function(secureFd){return fetch(ajax.ajax,{method:"POST",credentials:"same-origin",body:secureFd});}).then(function(resp){return resp.json()}).then(function(res){r.dataset.zpSubmitting="0"; if(res&&res.success){r.dataset.zpSent="1"; var rd=(res.data&&res.data.redirect)?res.data.redirect:""; if(modal)modal.classList.remove("is-on"); if(rd){window.location.href=rd;}else{showSuccess();}}else{var d=(res&&res.data)||{};var msgs=d.messages||["Nie udało się wysłać zgłoszenia. Spróbuj ponownie albo skontaktuj się przez WhatsApp."]; if(title)title.textContent=d.title||"Błąd wysyłki"; if(text)text.textContent=msgs.join(" "); toast(msgs[0]||"Nie udało się wysłać zgłoszenia")}}).catch(function(err){r.dataset.zpSubmitting="0"; if(title)title.textContent="Błąd wysyłki"; if(text)text.textContent="Nie udało się połączyć z formularzem. Spróbuj ponownie albo skontaktuj się przez WhatsApp."; toast("Nie udało się połączyć z formularzem")})}
    document.addEventListener("click",sendFinal,true);
    document.addEventListener("click",function(e){var r=root(); if(!r)return; setTimeout(syncSummary,120);},true);
    document.addEventListener("input",function(){setTimeout(syncSummary,120)},true);
    function init(){var r=root(); if(!r)return; document.body.classList.add("zpbs-studio-page"); ensureBudgetUnset(r); filePreview(r); enhanceDomains(r); syncSummary(); luc()}
    if(document.readyState==="loading")document.addEventListener("DOMContentLoaded",init);else init(); setTimeout(init,400); setTimeout(init,1200);
  })();</script>';
  return $css.$js;
}


add_action('wp_ajax_zp_studio_domain_check', 'zp_studio_domain_check');
add_action('wp_ajax_nopriv_zp_studio_domain_check', 'zp_studio_domain_check');
function zp_studio_domain_check(){
  check_ajax_referer('zp_studio_domain_check','nonce');
  $domains = isset($_POST['domains']) && is_array($_POST['domains']) ? $_POST['domains'] : [];
  $items = [];
  foreach ($domains as $raw) {
    $domain = strtolower(trim(sanitize_text_field(wp_unslash($raw))));
    $domain = preg_replace('/[^a-z0-9\.\-]/', '', $domain);
    $domain = trim($domain, '.-');
    if (!$domain || strlen($domain) > 120 || substr_count($domain,'.') < 1) { continue; }
    $taken = false;
    if (function_exists('checkdnsrr')) {
      $taken = checkdnsrr($domain, 'A') || checkdnsrr($domain, 'AAAA') || checkdnsrr($domain, 'MX') || checkdnsrr($domain, 'NS') || checkdnsrr($domain, 'CNAME');
    }
    $items[] = ['domain'=>$domain, 'status'=>$taken ? 'zajeta' : 'wolna'];
    if (count($items) >= 8) { break; }
  }
  wp_send_json_success(['items'=>$items]);
}


function zp_studio_pdf_text_lines($order){
  $p = $order['payload'] ?? [];
  $lines = ['ZAPROJEKTOWANI - PODSUMOWANIE BRIEFU / ZAMOWIENIA','ID: '.($order['id'] ?? ''),'Data: '.($order['created'] ?? ''),''];
  foreach($p as $k=>$v){
    if($v === '' || is_array($v)) { continue; }
    $lines[] = $k.': '.$v;
  }
  if(!empty($order['files'])){
    $lines[]=''; $lines[]='Pliki:';
    foreach($order['files'] as $f){ $lines[]='- '.($f['name'] ?? 'plik'); }
  }
  return $lines;
}

function zp_studio_build_pdf_binary_from_lines($lines){
  $safe = function($txt){ return zp_studio_pdf_escape($txt); };
  $cmd = [];
  // Tło + brandowy header w stylu Zaprojektowani / Plus Jakarta Sans feel.
  $cmd[] = '0.975 0.982 0.992 rg 0 0 595 842 re f';
  $cmd[] = '0.02 0.03 0.05 rg 0 694 595 148 re f';
  $cmd[] = '0.04 0.10 0.20 rg 324 694 271 148 re f';
  $cmd[] = '0.10 0.28 0.48 rg 430 694 165 148 re f';
  $cmd[] = '1 1 1 rg 42 760 54 54 re f';
  $cmd[] = '0.03 0.08 0.15 rg BT /F2 24 Tf 52 776 Td (ZP) Tj ET';
  $cmd[] = '1 1 1 rg BT /F2 23 Tf 112 790 Td (ZAPROJEKTOWANI) Tj ET';
  $cmd[] = '0.75 0.79 0.86 rg BT /F1 8 Tf 113 772 Td (PROJEKTUJEMY MARKI ONLINE) Tj ET';
  $cmd[] = '0.75 0.79 0.86 rg BT /F1 9 Tf 113 742 Td (PODSUMOWANIE BRIEFU / ZAMOWIENIA) Tj ET';
  $cmd[] = '1 1 1 rg 42 666 511 2 re f';

  $y = 640;
  $row_i = 0;
  foreach((array)$lines as $line){
    $line = trim((string)$line);
    if($line === '') { $y -= 12; continue; }
    if($y < 64) { break; }

    // Pomijamy powtórzony tytuł, bo jest już w headerze.
    if(stripos(remove_accents($line), 'ZAPROJEKTOWANI - PODSUMOWANIE') !== false) { continue; }

    if(strpos($line, ':') !== false){
      [$label,$value] = array_map('trim', explode(':', $line, 2));
      $h = 42;
      $bg = ($row_i % 2 === 0) ? '1 1 1' : '0.965 0.975 0.99';
      $cmd[] = $bg.' rg 42 '.($y-$h+8).' 511 '.$h.' re f';
      $cmd[] = '0.88 0.90 0.93 RG 42 '.($y-$h+8).' 511 '.$h.' re S';
      $cmd[] = '0.45 0.50 0.58 rg BT /F2 8 Tf 58 '.($y-9).' Td ('.strtoupper($safe($label)).') Tj ET';
      $chunks = str_split($safe($value), 74);
      $yy = $y-25;
      foreach(array_slice($chunks,0,2) as $chunk){
        $cmd[] = '0.03 0.08 0.15 rg BT /F2 11 Tf 58 '.$yy.' Td ('.$chunk.') Tj ET';
        $yy -= 13;
      }
      $y -= $h + 7;
      $row_i++;
    } else {
      $cmd[] = '0.03 0.08 0.15 rg BT /F2 14 Tf 46 '.$y.' Td ('.$safe($line).') Tj ET';
      $y -= 24;
    }
  }
  $cmd[] = '0.45 0.50 0.58 rg BT /F1 8 Tf 46 30 Td (Zaprojektowani.com - projektujemy marki online) Tj ET';
  $content = implode("\n", $cmd);
  $objs=[];
  $objs[]='1 0 obj << /Type /Catalog /Pages 2 0 R >> endobj';
  $objs[]='2 0 obj << /Type /Pages /Kids [3 0 R] /Count 1 >> endobj';
  $objs[]='3 0 obj << /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 4 0 R /F2 6 0 R >> >> /Contents 5 0 R >> endobj';
  $objs[]='4 0 obj << /Type /Font /Subtype /Type1 /BaseFont /Helvetica >> endobj';
  $objs[]='5 0 obj << /Length '.strlen($content).' >> stream' . "\n" . $content . "\n" . 'endstream endobj';
  $objs[]='6 0 obj << /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >> endobj';
  $pdf="%PDF-1.4\n"; $offsets=[0];
  foreach($objs as $obj){ $offsets[]=strlen($pdf); $pdf.=$obj."\n"; }
  $xref=strlen($pdf);
  $pdf.='xref' . "\n" . '0 '.(count($objs)+1)."\n".'0000000000 65535 f ' . "\n";
  for($i=1;$i<=count($objs);$i++){ $pdf.=sprintf('%010d 00000 n ', $offsets[$i])."\n"; }
  $pdf.='trailer << /Size '.(count($objs)+1).' /Root 1 0 R >>' . "\n" . 'startxref' . "\n" . $xref . "\n%%EOF";
  return $pdf;
}

function zp_studio_generate_order_pdf_file($order){
  $uploads = wp_upload_dir();
  if(empty($uploads['basedir']) || !wp_mkdir_p($uploads['basedir'].'/zp-studio-pdf')) { return null; }
  $safe_id = sanitize_file_name($order['id'] ?? ('zp-'.time()));
  $file = trailingslashit($uploads['basedir']).'zp-studio-pdf/'.$safe_id.'-podsumowanie.pdf';
  $url  = trailingslashit($uploads['baseurl']).'zp-studio-pdf/'.$safe_id.'-podsumowanie.pdf';
  $pdf = zp_studio_build_pdf_binary_from_lines(zp_studio_pdf_text_lines($order));
  if(file_put_contents($file, $pdf) === false) { return null; }
  return ['name'=>$safe_id.'-podsumowanie.pdf','url'=>esc_url_raw($url),'file'=>$file];
}


add_action('wp_ajax_zp_studio_submit', 'zp_studio_handle_submit');
add_action('wp_ajax_nopriv_zp_studio_submit', 'zp_studio_handle_submit');

function zp_studio_thank_you_redirect_url($order){
  $payload = isset($order['payload']) && is_array($order['payload']) ? $order['payload'] : [];
  $service = isset($payload['Usługa']) ? (string)$payload['Usługa'] : '';
  $service_l = function_exists('mb_strtolower') ? mb_strtolower($service, 'UTF-8') : strtolower($service);
  if (strpos($service_l, 'logo') !== false || strpos($service_l, 'branding') !== false) {
    $path = '/dziekujemy-za-zamowienie-logo/';
  } elseif (strpos($service_l, 'sklep') !== false) {
    $path = '/dziekujemy-za-zapytanie-o-wycene-sklepu-internetowego/';
  } else {
    $path = '/dziekujemy-za-zapytanie-o-wycene-strony-internetowej/';
  }
  $url = home_url($path);
  if (!empty($order['id'])) { $url = add_query_arg('zp_order', rawurlencode((string)$order['id']), $url); }
  return $url;
}

function zp_studio_handle_submit(){
  check_ajax_referer('zp_studio_submit','nonce');
  $payload = json_decode(stripslashes($_POST['payload'] ?? '{}'), true);
  if (!is_array($payload)) { $payload = []; }
  $payload = array_map(function($v){ return is_scalar($v) ? sanitize_textarea_field((string)$v) : ''; }, $payload);

  $name = trim((string)($payload['Imię / firma'] ?? $payload['Imię i nazwisko'] ?? $payload['Firma'] ?? ''));
  $company = trim((string)($payload['Firma'] ?? ''));
  $email = sanitize_email((string)($payload['E-mail'] ?? $payload['Email'] ?? ''));
  $phone = trim((string)($payload['Telefon'] ?? ''));
  $message = trim((string)($payload['Opis projektu'] ?? $payload['Opis'] ?? $payload['Uwagi'] ?? ''));
  $website = trim((string)($payload['Link / inspiracje'] ?? $payload['Strona www'] ?? ''));
  $services = trim((string)($payload['Wybrane usługi'] ?? $payload['Usługa'] ?? ''));

  $errors = array_merge(
    function_exists('zp_suite_antispam_base_errors') ? zp_suite_antispam_base_errors('studio') : [],
    function_exists('zp_suite_antispam_content_errors') ? zp_suite_antispam_content_errors([
      'name' => $name,
      'company' => $company,
      'phone' => $phone,
      'email' => $email,
      'message' => $message,
      'website' => $website,
      'services' => $services,
    ], 7) : []
  );
  if ($email && !is_email($email)) { $errors[] = 'Podaj poprawny adres e-mail.'; }
  if ($errors) { wp_send_json_error(['title'=>'Nie udało się wysłać formularza', 'messages'=>$errors], 422); }

  $order = [
    'id' => 'ZP-' . date('Ymd-His') . '-' . wp_generate_password(4, false, false),
    'created' => current_time('mysql'),
    'ip' => sanitize_text_field($_SERVER['REMOTE_ADDR'] ?? ''),
    'payload' => $payload,
    'files' => [],
    'status' => 'new',
    'status_log' => [],
  ];
  if (!empty($_FILES['files']) && is_array($_FILES['files']['name'])) {
    require_once ABSPATH . 'wp-admin/includes/file.php';
    $count = count($_FILES['files']['name']);
    for ($i=0; $i<$count; $i++) {
      if (empty($_FILES['files']['name'][$i])) { continue; }
      $file = [
        'name'=>$_FILES['files']['name'][$i], 'type'=>$_FILES['files']['type'][$i], 'tmp_name'=>$_FILES['files']['tmp_name'][$i], 'error'=>$_FILES['files']['error'][$i], 'size'=>$_FILES['files']['size'][$i]
      ];
      $up = wp_handle_upload($file, ['test_form'=>false]);
      if (!isset($up['error'])) { $order['files'][] = ['name'=>sanitize_file_name($file['name']), 'url'=>esc_url_raw($up['url']), 'file'=>$up['file']]; }
    }
  }
  // PDF generation disabled for Studio Wyceny e-mails — orders stay clean in admin.
  $orders = get_option(ZP_STUDIO_OPT_ORDERS, []); if (!is_array($orders)) { $orders = []; }
  array_unshift($orders, $order); $orders = array_slice($orders, 0, 300);
  update_option(ZP_STUDIO_OPT_ORDERS, $orders, false);
  update_option(ZP_STUDIO_OPT_UNREAD, max(0, (int) get_option(ZP_STUDIO_OPT_UNREAD, 0)) + 1, false);
  $sent = zp_studio_send_order_email($order);
  if (empty($sent['admin'])) {
    wp_send_json_error([
      'title' => 'Nie udało się wysłać wiadomości',
      'messages' => ['Zgłoszenie zostało zapisane w panelu, ale serwer pocztowy nie przyjął wiadomości e-mail. Sprawdź konfigurację poczty albo napisz na kontakt@zaprojektowani.com.'],
      'id' => $order['id'],
      'mail' => $sent,
    ], 500);
  }
  wp_send_json_success(['id'=>$order['id'], 'mail'=>$sent, 'redirect'=>zp_studio_thank_you_redirect_url($order)]);
}

function zp_studio_email_headers(){
  $from_email = 'kontakt@zaprojektowani.com';
  $from_name = 'Zaprojektowani Studio';
  if (function_exists('zp_suite_opt')) {
    $opt_email = zp_suite_opt('brand.from_email', 'kontakt@zaprojektowani.com');
    $opt_name = zp_suite_opt('brand.from_name', 'Zaprojektowani Studio');
    if (is_email($opt_email)) { $from_email = $opt_email; }
    if ($opt_name) { $from_name = sanitize_text_field($opt_name); }
  }
  return [
    'Content-Type: text/html; charset=UTF-8',
    'From: '.$from_name.' <'.$from_email.'>',
  ];
}

function zp_studio_email_logo_url(){
  return esc_url(defined('ZP_STUDIO_LOGO_LIGHT') ? ZP_STUDIO_LOGO_LIGHT : ZP_STUDIO_LOGO_DARK);
}

function zp_studio_build_email_body($order, $mode='admin'){
  $p = $order['payload'] ?? [];
  $logo = zp_studio_email_logo_url();

  $headline = $mode === 'client' ? 'Dziękujemy — otrzymaliśmy Twoje zgłoszenie' : 'Nowe zgłoszenie z konfiguratora';
  $lead = $mode === 'client'
    ? 'Poniżej znajdziesz kopię przesłanego zgłoszenia. Jeśli wysłano prośbę o wycenę strony, sklepu lub kampanii, wrócimy z finalnym kosztem w ciągu 48–72 godzin.'
    : 'ID: '.esc_html($order['id'] ?? '').' · '.esc_html($order['created'] ?? '');

  $rows = '';
  foreach ($p as $k=>$v) {
    if ($v === '' || $v === null) { continue; }
    $rows .= '<tr>
      <td style="padding:13px 14px;border-bottom:1px solid #edf0f4;color:#7a8493;font-size:11px;line-height:1.35;text-transform:uppercase;letter-spacing:.08em;font-weight:800;vertical-align:top;width:190px;">'.esc_html($k).'</td>
      <td style="padding:13px 14px;border-bottom:1px solid #edf0f4;color:#071426;font-size:14px;line-height:1.58;font-weight:600;vertical-align:top;">'.nl2br(esc_html($v)).'</td>
    </tr>';
  }

  if (!empty($order['files']) && is_array($order['files'])) {
    $links = [];
    foreach ($order['files'] as $f) {
      if (empty($f['url'])) { continue; }
      $links[] = '<a href="'.esc_url($f['url']).'" style="color:#102a4f;text-decoration:underline;font-weight:700;">'.esc_html($f['name'] ?? 'Plik').'</a>';
    }
    if ($links) {
      $rows .= '<tr>
        <td style="padding:13px 14px;border-bottom:1px solid #edf0f4;color:#7a8493;font-size:11px;line-height:1.35;text-transform:uppercase;letter-spacing:.08em;font-weight:800;vertical-align:top;width:190px;">Pliki</td>
        <td style="padding:13px 14px;border-bottom:1px solid #edf0f4;color:#071426;font-size:14px;line-height:1.58;font-weight:600;vertical-align:top;">'.implode('<br>', $links).'</td>
      </tr>';
    }
  }

  return '<!doctype html>
<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"></head>
<body style="margin:0;padding:0;background:#f4f6f8;color:#071426;font-family:Arial,Helvetica,sans-serif;-webkit-text-size-adjust:100%;text-size-adjust:100%;">
  <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width:100%;background:#f4f6f8;border-collapse:collapse;">
    <tr>
      <td align="center" style="padding:26px 14px;">
        <table role="presentation" width="720" cellspacing="0" cellpadding="0" border="0" style="width:100%;max-width:720px;border-collapse:separate;border-spacing:0;background:#ffffff;border:1px solid #e5e9ef;border-radius:24px;overflow:hidden;">
          <tr>
            <td style="padding:26px 28px;background:#05070b;background-image:linear-gradient(135deg,#05070b 0%,#071426 55%,#102a4f 100%);color:#ffffff;">
              <img src="'.$logo.'" width="150" alt="Zaprojektowani" style="width:150px;max-width:150px;height:auto;display:block;border:0;outline:none;text-decoration:none;margin:0 0 20px 0;">
              <div style="font-size:11px;line-height:1.2;letter-spacing:.14em;text-transform:uppercase;color:#aeb8c7;font-weight:700;">Studio Wyceny</div>
              <h1 style="margin:8px 0 0 0;color:#ffffff;font-family:Arial,Helvetica,sans-serif;font-size:28px;line-height:1.08;letter-spacing:-.03em;font-weight:800;">'.$headline.'</h1>
              <p style="margin:11px 0 0 0;color:#c3ccd8;font-size:14px;line-height:1.55;font-weight:400;">'.$lead.'</p>
            </td>
          </tr>
          <tr>
            <td style="padding:24px 26px 10px 26px;background:#ffffff;">
              <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width:100%;border-collapse:collapse;background:#ffffff;border:1px solid #edf0f4;border-radius:16px;overflow:hidden;">'.$rows.'</table>
            </td>
          </tr>
          <tr>
            <td style="padding:6px 28px 28px 28px;background:#ffffff;">
              <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="border-collapse:collapse;">
                <tr>
                  <td style="padding:16px 18px;border-radius:16px;background:#f6f8fb;border:1px solid #edf0f4;color:#657285;font-size:13px;line-height:1.55;">
                    <strong style="color:#071426;">Zaprojektowani.com</strong><br>
                    Projektujemy marki online: strony, sklepy, branding i kampanie.
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>';
}


function zp_studio_send_html_mail_resilient($recipients, $subject, $body, $headers = [], $attachments = []){
  $recipients = is_array($recipients) ? $recipients : [$recipients];
  $recipients = array_values(array_unique(array_filter($recipients, 'is_email')));
  if (!$recipients) { return false; }
  $sent = wp_mail($recipients, $subject, $body, $headers, $attachments);
  if ($sent) { return true; }
  $single_ok = false;
  foreach ($recipients as $recipient) {
    if (wp_mail($recipient, $subject, $body, $headers, $attachments)) { $single_ok = true; }
  }
  return $single_ok;
}

function zp_studio_send_order_email($order){
  $admin = 'kontakt@zaprojektowani.com';
  if (function_exists('zp_suite_opt')) { $admin = zp_suite_opt('brand.admin_email', $admin); }
  if (!is_email($admin)) { $admin = get_option('admin_email'); }
  $p = $order['payload'];
  $subject = 'Nowe zgłoszenie Studio Wyceny — ' . ($p['Pakiet'] ?? $order['id']);
  $attachments = []; // PDF attachments disabled by design.
  $admin_recipients = array_unique(array_filter([$admin, 'kontakt@zaprojektowani.com'], 'is_email'));
  $admin_ok = zp_studio_send_html_mail_resilient($admin_recipients, $subject, zp_studio_build_email_body($order, 'admin'), zp_studio_email_headers(), $attachments);

  $client_ok = false;
  $client = '';
  foreach (['E-mail','Email','email'] as $k) {
    if (!empty($p[$k]) && is_email($p[$k])) { $client = $p[$k]; break; }
  }
  if ($client) {
    $client_ok = wp_mail($client, 'Potwierdzenie zgłoszenia — Studio Wyceny Zaprojektowani', zp_studio_build_email_body($order, 'client'), zp_studio_email_headers(), $attachments);
  }
  return ['admin'=>$admin_ok, 'client'=>$client_ok];
}

function zp_studio_status_label($status){
  $map = zp_studio_statuses();
  return $map[$status] ?? 'Nowe';
}
function zp_studio_status_class($status){
  $classes = ['new'=>'new','contact'=>'progress','quote_sent'=>'quote','accepted'=>'accepted','payment_pending'=>'pay','production'=>'production','done'=>'done','archived'=>'arch'];
  return $classes[$status] ?? 'new';
}
function zp_studio_status_mail_templates($status, $order){
  $p = $order['payload'] ?? [];
  $name = trim((string)($p['Imię i nazwisko'] ?? ''));
  $hello = $name ? 'Dzień dobry '.$name.',' : 'Dzień dobry,';
  $templates = [
    'quote_sent' => ['Wycena projektu — Zaprojektowani', $hello."\n\nPrzygotowaliśmy wycenę projektu na podstawie przesłanego briefu. W kolejnej wiadomości lub w załączniku znajdziesz szczegóły zakresu, rekomendacje i koszt realizacji.\n\nW razie pytań jesteśmy do dyspozycji.\n\nZespół Zaprojektowani"],
    'payment_pending' => ['Dane do płatności — Zaprojektowani', $hello."\n\nDziękujemy za akceptację zakresu. Poniżej możesz przesłać klientowi dane do płatności lub link do płatności. Po zaksięgowaniu płatności przechodzimy do realizacji.\n\nZespół Zaprojektowani"],
    'production' => ['Projekt przekazany do realizacji — Zaprojektowani', $hello."\n\nProjekt został przekazany do realizacji. Będziemy informować o kolejnych etapach prac i w razie potrzeby poprosimy o dodatkowe materiały.\n\nZespół Zaprojektowani"],
    'done' => ['Projekt zakończony — Zaprojektowani', $hello."\n\nDziękujemy za współpracę. Projekt został oznaczony jako zakończony. W razie potrzeby możesz odpowiedzieć na tę wiadomość.\n\nZespół Zaprojektowani"],
  ];
  return $templates[$status] ?? null;
}
function zp_studio_send_status_email($order, $status){
  $p = $order['payload'] ?? [];
  $client = '';
  foreach(['E-mail','Email','email'] as $k){ if(!empty($p[$k]) && is_email($p[$k])){$client=$p[$k];break;} }
  if(!$client) return false;
  $tpl = zp_studio_status_mail_templates($status, $order);
  if(!$tpl) return false;
  [$subject,$body_text] = $tpl;
  $logo = zp_studio_email_logo_url();
  $body = '<!doctype html><html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"></head><body style="margin:0;padding:0;background:#f4f6f8;color:#071426;font-family:Arial,Helvetica,sans-serif"><table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width:100%;background:#f4f6f8;border-collapse:collapse"><tr><td align="center" style="padding:26px 14px"><table role="presentation" width="720" cellspacing="0" cellpadding="0" border="0" style="width:100%;max-width:720px;border-collapse:separate;border-spacing:0;background:#fff;border:1px solid #e5e9ef;border-radius:24px;overflow:hidden"><tr><td style="padding:26px 28px;background:#05070b;background-image:linear-gradient(135deg,#05070b 0%,#071426 55%,#102a4f 100%);color:#fff"><img src="'.$logo.'" width="150" alt="Zaprojektowani" style="width:150px;max-width:150px;height:auto;display:block;border:0;margin:0 0 20px 0"><div style="font-size:11px;line-height:1.2;letter-spacing:.14em;text-transform:uppercase;color:#aeb8c7;font-weight:700">Studio Wyceny</div><h1 style="margin:8px 0 0;color:#fff;font-size:28px;line-height:1.08;letter-spacing:-.03em;font-weight:800">'.esc_html($subject).'</h1></td></tr><tr><td style="padding:26px 28px;background:#fff"><p style="white-space:pre-line;color:#071426;font-size:15px;line-height:1.72;margin:0">'.esc_html($body_text).'</p></td></tr><tr><td style="padding:0 28px 28px;background:#fff"><div style="padding:16px 18px;border-radius:16px;background:#f6f8fb;border:1px solid #edf0f4;color:#657285;font-size:13px;line-height:1.55"><strong style="color:#071426">Zaprojektowani.com</strong><br>Projektujemy marki online: strony, sklepy, branding i kampanie.</div></td></tr></table></td></tr></table></body></html>';
  return wp_mail($client, $subject, $body, zp_studio_email_headers());
}

add_action('admin_menu', function(){
  $unread = max(0, (int) get_option(ZP_STUDIO_OPT_UNREAD, 0));
  $orders_label = 'Zamówienia' . ($unread ? ' <span class="update-plugins count-'.intval($unread).'"><span class="plugin-count">'.intval($unread).'</span></span>' : '');
  add_submenu_page('zp-suite','Studio Wyceny — Zamówienia',$orders_label,'manage_options','zp-studio-orders','zp_studio_render_orders_page');
  add_submenu_page('zp-suite','Studio Wyceny — Pakiety','Pakiety','manage_options','zp-studio-packages','zp_studio_render_packages_page');
}, 40);

function zp_studio_admin_css(){
  echo '<style>.zpStudioWrap{max-width:1400px}.zpStudioHero{margin:18px 0 20px;padding:24px 26px;border-radius:26px;background:linear-gradient(135deg,#05070b,#071426 55%,#102a4f);color:#fff;display:flex;align-items:flex-end;justify-content:space-between;gap:22px;box-shadow:0 20px 54px rgba(5,10,18,.12)}.zpStudioHero h1{margin:0;font-size:34px;letter-spacing:-.04em}.zpStudioHero p{margin:8px 0 0;color:rgba(255,255,255,.72)}.zpStudioCard,.zpStudioOrder{background:#fff;border:1px solid #e6ebf0;border-radius:24px;box-shadow:0 14px 36px rgba(7,20,38,.06);overflow:hidden;margin:18px 0}.zpStudioCard{padding:22px}.zpStudioGrid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px}.zpStudioField label{display:block;font-size:11px;text-transform:uppercase;letter-spacing:.08em;font-weight:900;color:#6b7685;margin-bottom:6px}.zpStudioField input,.zpStudioField textarea{width:100%;border:1px solid #dfe5ec;border-radius:14px;padding:10px 12px}.zpStudioPkg{border:1px solid #e8edf3;border-radius:18px;padding:16px;margin:12px 0;background:#fbfcfe}.zpStudioFilters{display:flex;gap:8px;flex-wrap:wrap;margin:0 0 18px}.zpStudioFilter{display:inline-flex;align-items:center;gap:7px;padding:9px 12px;border-radius:999px;background:#fff;border:1px solid #dfe5ec;text-decoration:none;color:#071426;font-weight:800}.zpStudioFilter.is-on{background:#071426;color:#fff;border-color:#071426}.zpStudioOrderTop{display:grid;grid-template-columns:minmax(240px,.42fr) minmax(230px,.42fr) auto;gap:18px;align-items:center;padding:18px 20px;background:#fbfcfe;border-bottom:1px solid #e8edf3}.zpStudioOrderTitle strong{display:block;color:#071426;font-size:19px;line-height:1.15;letter-spacing:-.02em}.zpStudioOrderTitle code{display:inline-block;margin-top:6px;background:#eef3f8;border-radius:999px;padding:5px 9px;color:#42526a}.zpStudioClient{color:#42526a;line-height:1.45}.zpStudioClient b{color:#071426}.zpStudioStatusForm{display:flex;gap:8px;align-items:center;justify-content:flex-end;flex-wrap:wrap}.zpStudioStatusForm select{min-width:190px;border-radius:999px}.zpStudioStatusForm label{font-size:11px;color:#5c6878;font-weight:800}.zpStudioOrderBody{display:grid;grid-template-columns:minmax(0,1fr) 310px;gap:18px;padding:20px}.zpStudioDetails{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px}.zpStudioDetail{border:1px solid #edf1f5;border-radius:16px;padding:12px;background:#fff}.zpStudioDetail span{display:block;color:#7a8493;font-size:10px;text-transform:uppercase;letter-spacing:.09em;font-weight:900;margin-bottom:6px}.zpStudioDetail strong{display:block;color:#071426;font-size:13px;line-height:1.45;white-space:pre-wrap;word-break:break-word}.zpStudioDetail--full{grid-column:1/-1}.zpStudioFiles{border:1px solid #edf1f5;border-radius:18px;background:#fbfcfe;padding:14px}.zpStudioFiles h3{margin:0 0 10px;color:#071426;font-size:15px}.zpStudioFiles a.zpFile{display:grid;grid-template-columns:42px minmax(0,1fr);gap:10px;align-items:center;padding:9px 10px;background:#fff;border:1px solid #e8edf3;border-radius:12px;text-decoration:none;margin:7px 0;color:#071426}.zpThumb{width:42px;height:42px;border-radius:10px;background:#eef3f8;display:grid;place-items:center;overflow:hidden}.zpThumb img{width:100%;height:100%;object-fit:cover}.zpStudioBadge{display:inline-flex;padding:5px 10px;border-radius:999px;background:#071426;color:#fff;font-size:11px;font-weight:900;letter-spacing:.04em;text-transform:uppercase}.zpStudioBadge--new{background:#102a4f}.zpStudioBadge--progress{background:#1c477a}.zpStudioBadge--quote{background:#6a4cff}.zpStudioBadge--accepted{background:#14743c}.zpStudioBadge--pay{background:#b56b00}.zpStudioBadge--production{background:#0b6b8f}.zpStudioBadge--done{background:#14743c}.zpStudioBadge--arch{background:#667386}.zpStudioActions{display:flex;gap:8px;align-items:center;flex-wrap:wrap}.zpStudioSideBtns{display:grid;gap:8px;margin-top:12px}.zpStudioSideBtns a,.zpStudioSideBtns button{width:100%;text-align:center}.zpStatusLog{margin-top:12px;border-top:1px solid #e8edf3;padding-top:10px;color:#6b7685;font-size:12px;line-height:1.5}@media(max-width:900px){.zpStudioHero,.zpStudioOrderTop,.zpStudioOrderBody{display:block}.zpStudioStatusForm{justify-content:flex-start;margin-top:12px}.zpStudioDetails{grid-template-columns:1fr}.zpStudioGrid{grid-template-columns:1fr}}</style>';
}

function zp_studio_render_packages_page(){
  if (!current_user_can('manage_options')) return;
  if (!empty($_POST['zp_studio_save']) && check_admin_referer('zp_studio_packages')) {
    update_option(ZP_STUDIO_OPT_PACKAGES, zp_studio_sanitize_packages($_POST['studio'] ?? []), false);
    echo '<div class="notice notice-success"><p>Pakiety zapisane.</p></div>';
  }
  $data = zp_studio_packages(); zp_studio_admin_css();
  echo '<div class="wrap zpStudioWrap"><div class="zpStudioHero"><div><h1>Studio Wyceny — Pakiety</h1><p>Edytuj pakiety jak karty: nazwę, opis, ikonę Lucide, korzyści, cenę i cenę promocyjną.</p></div><code>[zp_studio_wyceny]</code></div><form method="post">'; wp_nonce_field('zp_studio_packages');
  foreach ($data as $service=>$cfg) {
    echo '<div class="zpStudioCard"><h2>'.esc_html(strtoupper($service)).'</h2><div class="zpStudioGrid">';
    foreach (['kick'=>'Kicker','title'=>'Tytuł HTML','lead'=>'Lead','subject'=>'Temat maila/podglądu','final'=>'Tytuł finału','help'=>'Boks pomocy','helpCopy'=>'Opis pomocy'] as $key=>$label) {
      $is_textarea = in_array($key,['title','lead','helpCopy'],true);
      echo '<div class="zpStudioField"><label>'.esc_html($label).'</label>';
      if ($is_textarea) echo '<textarea rows="3" name="studio['.esc_attr($service).']['.esc_attr($key).']">'.esc_textarea($cfg[$key]).'</textarea>';
      else echo '<input type="text" name="studio['.esc_attr($service).']['.esc_attr($key).']" value="'.esc_attr($cfg[$key]).'">';
      echo '</div>';
    }
    echo '</div><h3>Pakiety / karty</h3>';
    foreach ($cfg['p'] as $i=>$p) {
      echo '<div class="zpStudioPkg"><h4>'.esc_html($p[0]).'</h4><div class="zpStudioGrid">';
      $fields = ['name'=>$p[0], 'note'=>$p[1], 'icon'=>$p[3], 'tags'=>implode(', ', (array)$p[4])];
      foreach($fields as $k=>$v){ echo '<div class="zpStudioField"><label>'.esc_html($k).'</label><input type="text" name="studio['.esc_attr($service).'][p]['.$i.']['.esc_attr($k).']" value="'.esc_attr($v).'"></div>'; }
      if ($service==='brand') echo '<div class="zpStudioField"><label>Cena aktualna</label><input type="number" name="studio['.esc_attr($service).'][p]['.$i.'][price]" value="'.esc_attr($p[2]).'"></div><div class="zpStudioField"><label>Cena przekreślona / promocyjna</label><input type="number" name="studio['.esc_attr($service).'][p]['.$i.'][promo]" value="'.esc_attr($p['promo'] ?? '').'"></div>';
      echo '<div class="zpStudioField" style="grid-column:1/-1"><label>Opis</label><textarea rows="3" name="studio['.esc_attr($service).'][p]['.$i.'][desc]">'.esc_textarea($p[5]).'</textarea></div>';
      echo '</div></div>';
    }
    echo '</div>';
  }
  echo '<p><button class="button button-primary button-large" name="zp_studio_save" value="1">Zapisz pakiety</button></p></form></div>';
}

function zp_studio_get_order_by_id($id){
  $orders=get_option(ZP_STUDIO_OPT_ORDERS,[]); if(!is_array($orders)) return null;
  foreach($orders as $o){ if(($o['id']??'')===$id) return $o; }
  return null;
}
function zp_studio_order_admin_url($action,$id){ return wp_nonce_url(admin_url('admin-post.php?action='.$action.'&order_id='.rawurlencode($id)), $action.'_'.$id); }

add_action('admin_post_zp_studio_files_zip', function(){
  if(!current_user_can('manage_options')) wp_die('Brak uprawnień');
  $id=sanitize_text_field(wp_unslash($_GET['order_id']??'')); check_admin_referer('zp_studio_files_zip_'.$id);
  $o=zp_studio_get_order_by_id($id); if(!$o) wp_die('Nie znaleziono zamówienia');
  if(!class_exists('ZipArchive')) wp_die('ZipArchive nie jest dostępny na serwerze.');
  $tmp=wp_tempnam('zp-files-'.$id.'.zip'); $zip=new ZipArchive(); $zip->open($tmp, ZipArchive::OVERWRITE);
  foreach(($o['files']??[]) as $f){ if(!empty($f['file']) && file_exists($f['file'])) $zip->addFile($f['file'], sanitize_file_name($f['name']??basename($f['file']))); }
  $zip->close(); header('Content-Type: application/zip'); header('Content-Disposition: attachment; filename="'.$id.'-pliki.zip"'); header('Content-Length: '.filesize($tmp)); readfile($tmp); @unlink($tmp); exit;
});

function zp_studio_pdf_escape($txt){ return str_replace(['\\','(',')'], ['\\\\','\\(','\\)'], remove_accents((string)$txt)); }
add_action('admin_post_zp_studio_order_pdf', function(){
  if(!current_user_can('manage_options')) wp_die('Brak uprawnień');
  $id=sanitize_text_field(wp_unslash($_GET['order_id']??'')); check_admin_referer('zp_studio_order_pdf_'.$id);
  $o=zp_studio_get_order_by_id($id); if(!$o) wp_die('Nie znaleziono zamówienia');
  $pdf = zp_studio_build_pdf_binary_from_lines(zp_studio_pdf_text_lines($o));
  header('Content-Type: application/pdf'); header('Content-Disposition: attachment; filename="'.$id.'-podsumowanie.pdf"'); echo $pdf; exit;
});


function zp_studio_render_orders_page(){
  if (!current_user_can('manage_options')) { return; }

  update_option(ZP_STUDIO_OPT_UNREAD, 0, false);

  $statuses = zp_studio_statuses();
  $orders = get_option(ZP_STUDIO_OPT_ORDERS, []);
  if (!is_array($orders)) { $orders = []; }

  $notice = '';

  if (!empty($_POST['zp_studio_simple_action']) && check_admin_referer('zp_studio_simple_action')) {
    $action = sanitize_key(wp_unslash($_POST['zp_studio_simple_action']));
    $order_id = sanitize_text_field(wp_unslash($_POST['order_id'] ?? ''));

    if ($action === 'delete' && $order_id !== '') {
      $before = count($orders);
      $orders = array_values(array_filter($orders, function($order) use ($order_id){
        return (string)($order['id'] ?? '') !== $order_id;
      }));
      update_option(ZP_STUDIO_OPT_ORDERS, $orders, false);
      $notice = ($before > count($orders)) ? 'Usunięto zamówienie/brief.' : 'Nie znaleziono zamówienia do usunięcia.';
    }

    if ($action === 'save' && $order_id !== '') {
      $status = sanitize_key(wp_unslash($_POST['status'] ?? 'new'));
      if (!array_key_exists($status, $statuses)) { $status = 'new'; }
      $note = sanitize_textarea_field(wp_unslash($_POST['admin_note'] ?? ''));
      foreach ($orders as $i => $order) {
        if ((string)($order['id'] ?? '') === $order_id) {
          $orders[$i]['status'] = $status;
          $orders[$i]['admin_note'] = $note;
          if (empty($orders[$i]['status_log']) || !is_array($orders[$i]['status_log'])) { $orders[$i]['status_log'] = []; }
          $orders[$i]['status_log'][] = [
            'status' => $status,
            'date' => current_time('mysql'),
            'user' => wp_get_current_user()->display_name,
          ];
          break;
        }
      }
      update_option(ZP_STUDIO_OPT_ORDERS, $orders, false);
      $notice = 'Zapisano status/notatkę.';
    }

    if ($action === 'clear_closed') {
      $before = count($orders);
      $orders = array_values(array_filter($orders, function($order){
        return !in_array(($order['status'] ?? 'new'), ['done','archived'], true);
      }));
      update_option(ZP_STUDIO_OPT_ORDERS, $orders, false);
      $notice = 'Usunięto ' . max(0, $before - count($orders)) . ' zakończonych/archiwalnych zamówień.';
    }

    if ($action === 'clear_all') {
      $orders = [];
      update_option(ZP_STUDIO_OPT_ORDERS, $orders, false);
      $notice = 'Wyczyszczono wszystkie zamówienia.';
    }

    wp_safe_redirect(add_query_arg('zp_notice', rawurlencode($notice), admin_url('admin.php?page=zp-studio-orders')));
    exit;
  }

  // Backward compatibility with previous clear/status forms.
  if (!empty($_POST['zp_studio_status']) && check_admin_referer('zp_studio_orders_status')) {
    $id = sanitize_text_field(wp_unslash($_POST['order_id'] ?? ''));
    $status = sanitize_key(wp_unslash($_POST['status'] ?? 'new'));
    if (!array_key_exists($status, $statuses)) { $status = 'new'; }
    foreach ($orders as $i => $order) {
      if ((string)($order['id'] ?? '') === $id) { $orders[$i]['status'] = $status; break; }
    }
    update_option(ZP_STUDIO_OPT_ORDERS, $orders, false);
    $notice = 'Status zamówienia zaktualizowany.';
  }

  if (!empty($_POST['zp_studio_clear']) && check_admin_referer('zp_studio_orders_clear')) {
    $orders = [];
    update_option(ZP_STUDIO_OPT_ORDERS, [], false);
    $notice = 'Lista zamówień wyczyszczona.';
  }

  $filter = sanitize_key(wp_unslash($_GET['zp_status'] ?? 'all'));
  $query = sanitize_text_field(wp_unslash($_GET['zp_q'] ?? ''));

  $all_orders = $orders;

  if ($filter !== 'all' || $query !== '') {
    $orders = array_values(array_filter($orders, function($order) use ($filter, $query){
      if ($filter !== 'all' && ($order['status'] ?? 'new') !== $filter) { return false; }
      if ($query !== '') {
        $hay = strtolower(wp_json_encode($order, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
        if (strpos($hay, strtolower($query)) === false) { return false; }
      }
      return true;
    }));
  }

  $counts = ['all' => count($all_orders)];
  foreach ($statuses as $key => $label) { $counts[$key] = 0; }
  foreach ($all_orders as $order) {
    $st = $order['status'] ?? 'new';
    if (isset($counts[$st])) { $counts[$st]++; }
  }

  $notice = isset($_GET['zp_notice']) ? sanitize_text_field(wp_unslash($_GET['zp_notice'])) : $notice;

  ?>
  <div class="wrap zpOrdersSimple">
    <style>.zpOrdersSimple{max-width:1440px}.zpOrdersHero{margin:18px 0 18px;padding:26px 28px;border-radius:22px;background:linear-gradient(135deg,#05070b,#071426 55%,#102a4f);color:#fff}.zpOrdersHero h1{margin:0 0 8px;font-size:30px;line-height:1.05;letter-spacing:-.035em;color:#fff}.zpOrdersHero p{margin:0;color:rgba(255,255,255,.72);font-size:14px;line-height:1.6;max-width:900px}.zpOrdersStats{display:flex;gap:10px;flex-wrap:wrap;margin-top:18px}.zpOrdersStat{border:1px solid rgba(255,255,255,.15);background:rgba(255,255,255,.06);border-radius:16px;padding:11px 14px;min-width:130px}.zpOrdersStat strong{display:block;font-size:20px;line-height:1;color:#fff}.zpOrdersStat span{display:block;margin-top:4px;font-size:10px;text-transform:uppercase;letter-spacing:.12em;color:rgba(255,255,255,.58)}.zpOrdersToolbar{display:flex;gap:10px;align-items:end;flex-wrap:wrap;background:#fff;border:1px solid #dcdcde;border-radius:18px;padding:14px;margin-bottom:14px}.zpOrdersToolbar label{font-weight:700;color:#1d2327;font-size:12px}.zpOrdersToolbar select,.zpOrdersToolbar input[type=search]{min-height:36px;min-width:190px}.zpOrderList{display:grid;gap:14px}.zpOrderCard{background:#fff;border:1px solid #dcdcde;border-radius:20px;overflow:hidden;box-shadow:0 10px 28px rgba(7,20,38,.05)}.zpOrderCard.is-new{border-color:#1c477a;box-shadow:0 0 0 1px rgba(28,71,122,.18),0 10px 28px rgba(7,20,38,.07)}.zpOrderTop{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:18px;align-items:start;padding:18px 20px;border-bottom:1px solid #eef0f3;background:#fbfcfe}.zpOrderTitle h2{margin:0 0 5px;font-size:20px;line-height:1.15;color:#071426}.zpOrderTitle small{display:block;color:#667085}.zpOrderBadge{display:inline-flex;align-items:center;border-radius:999px;padding:6px 9px;font-size:11px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;background:#eaf2ff;color:#0b3b75;white-space:nowrap}.zpOrderBody{display:grid;grid-template-columns:minmax(0,1fr) 320px;gap:18px;padding:18px 20px}.zpOrderContact{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px;margin-bottom:14px}.zpOrderContactBox{border:1px solid #e3e7ed;border-radius:14px;padding:11px 12px;background:#fff}.zpOrderContactBox span{display:block;font-size:10px;text-transform:uppercase;letter-spacing:.12em;font-weight:800;color:#7a8493;margin-bottom:5px}.zpOrderContactBox strong{display:block;color:#071426;word-break:break-word;font-size:14px}.zpOrderDetails{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px}.zpOrderDetail{border:1px solid #edf0f4;border-radius:12px;padding:10px;background:#fff}.zpOrderDetail span{display:block;color:#7a8493;font-size:10px;letter-spacing:.1em;text-transform:uppercase;font-weight:800;margin-bottom:4px}.zpOrderDetail strong{display:block;color:#111827;font-size:13px;line-height:1.45;word-break:break-word}.zpOrderDetail--full{grid-column:1/-1}.zpOrderSide{border-left:1px solid #edf0f4;padding-left:18px}.zpOrderActions{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:14px}.zpOrderActions .button{margin:0}.zpOrderDelete{color:#b42318!important;border-color:#f3b8b2!important}.zpOrderNote textarea{width:100%;min-height:90px;margin:8px 0}.zpOrderFiles a{display:flex;gap:8px;align-items:center;margin:6px 0;text-decoration:none}.zpOrderFiles img{width:44px;height:44px;object-fit:cover;border-radius:8px;border:1px solid #e5e7eb}.zpStatusLog{margin-top:12px;color:#667085;font-size:12px;line-height:1.55}@media(max-width:1100px){.zpOrderBody{grid-template-columns:1fr}.zpOrderSide{border-left:0;border-top:1px solid #edf0f4;padding-left:0;padding-top:16px}.zpOrderContact{grid-template-columns:1fr}.zpOrderDetails{grid-template-columns:1fr}}</style>

    <div class="zpOrdersHero">
      <h1>Zamówienia i briefy ze Studio Wyceny</h1>
      <p>Uproszczony panel: kontakt klienta, pełny brief, pliki, status, notatka i działające usuwanie w jednym miejscu.</p>
      <div class="zpOrdersStats">
        <div class="zpOrdersStat"><strong><?php echo esc_html($counts['all']); ?></strong><span>wszystkie</span></div>
        <div class="zpOrdersStat"><strong><?php echo esc_html($counts['new'] ?? 0); ?></strong><span>nowe</span></div>
        <div class="zpOrdersStat"><strong><?php echo esc_html(($counts['contact'] ?? 0) + ($counts['quote_sent'] ?? 0)); ?></strong><span>w obsłudze</span></div>
      </div>
    </div>

    <?php if ($notice) : ?><div class="notice notice-success is-dismissible"><p><?php echo esc_html($notice); ?></p></div><?php endif; ?>

    <form class="zpOrdersToolbar" method="get">
      <input type="hidden" name="page" value="zp-studio-orders">
      <label>Status<br><select name="zp_status"><option value="all">Wszystkie</option><?php foreach($statuses as $k=>$v): ?><option value="<?php echo esc_attr($k); ?>" <?php selected($filter,$k); ?>><?php echo esc_html($v); ?></option><?php endforeach; ?></select></label>
      <label>Szukaj<br><input type="search" name="zp_q" value="<?php echo esc_attr($query); ?>" placeholder="imię, e-mail, telefon, pakiet..."></label>
      <button class="button button-primary">Filtruj</button>
      <a class="button" href="<?php echo esc_url(admin_url('admin.php?page=zp-studio-orders')); ?>">Wyczyść filtr</a>
      <span style="flex:1"></span>
    </form>

    <div class="zpOrdersToolbar" style="justify-content:flex-end">
      <form method="post" onsubmit="return confirm('Usunąć zamówienia zakończone oraz archiwalne?')"><?php wp_nonce_field('zp_studio_simple_action'); ?><button class="button" name="zp_studio_simple_action" value="clear_closed">Usuń zakończone/archiwalne</button></form>
      <form method="post" onsubmit="return confirm('Na pewno usunąć WSZYSTKIE zamówienia i briefy? Tej akcji nie da się cofnąć.')"><?php wp_nonce_field('zp_studio_simple_action'); ?><button class="button zpOrderDelete" name="zp_studio_simple_action" value="clear_all">Wyczyść wszystkie</button></form>
    </div>

    <?php if (empty($orders)) : ?>
      <div class="zpOrderCard"><div class="zpOrderTop"><div class="zpOrderTitle"><h2>Brak zamówień</h2><small>Nowe briefy ze Studio Wyceny pojawią się tutaj automatycznie.</small></div></div></div>
    <?php else : ?>
      <div class="zpOrderList">
        <?php foreach ($orders as $order) :
          $id = (string)($order['id'] ?? '');
          $status = $order['status'] ?? 'new';
          $payload = isset($order['payload']) && is_array($order['payload']) ? $order['payload'] : [];
          $client = $payload['Imię i nazwisko'] ?? ($payload['Imię'] ?? 'Klient');
          $email = $payload['E-mail'] ?? ($payload['Email'] ?? '');
          $phone = $payload['Telefon'] ?? '';
          $service = $payload['Usługa'] ?? 'Studio Wyceny';
          $pkg = $payload['Pakiet'] ?? 'Brief';
          $created = $order['created'] ?? ($order['created_at'] ?? '');
          $skip = ['Usługa','Pakiet','Imię i nazwisko','Imię','E-mail','Email','Telefon'];
        ?>
        <article class="zpOrderCard <?php echo $status === 'new' ? 'is-new' : ''; ?>">
          <div class="zpOrderTop">
            <div class="zpOrderTitle">
              <h2><?php echo esc_html($service . ' — ' . $pkg); ?></h2>
              <small><?php echo esc_html($created); ?> · ID: <?php echo esc_html($id); ?></small>
            </div>
            <span class="zpOrderBadge"><?php echo esc_html($statuses[$status] ?? $status); ?></span>
          </div>

          <div class="zpOrderBody">
            <main>
              <div class="zpOrderContact">
                <div class="zpOrderContactBox"><span>Klient</span><strong><?php echo esc_html($client ?: '—'); ?></strong></div>
                <div class="zpOrderContactBox"><span>E-mail</span><strong><?php echo esc_html($email ?: '—'); ?></strong></div>
                <div class="zpOrderContactBox"><span>Telefon</span><strong><?php echo esc_html($phone ?: '—'); ?></strong></div>
              </div>

              <div class="zpOrderDetails">
                <?php
                  $priority = ['Suma','Budżet','Termin','Dodatki','Dodatki suma','Funkcje','Materiały','Preferowana domena','Preferowane domeny','Pomysł na domenę','Branża','Cel','Podstrony','Płatności','Dostawy','Platformy','Cele','Zakres','Firma','Linki / inspiracje','Brief','message'];
                  $shown = [];
                  foreach ($priority as $key) {
                    if (!isset($payload[$key]) || $payload[$key] === '') { continue; }
                    $full = in_array($key, ['Dodatki','Funkcje','Materiały','Preferowana domena','Preferowane domeny','Brief','message','Linki / inspiracje'], true);
                    echo '<div class="zpOrderDetail '.($full?'zpOrderDetail--full':'').'"><span>'.esc_html($key).'</span><strong>'.nl2br(esc_html((string)$payload[$key])).'</strong></div>';
                    $shown[] = $key;
                  }
                  foreach ($payload as $key => $value) {
                    if (in_array($key, $skip, true) || in_array($key, $shown, true) || $value === '' || is_array($value)) { continue; }
                    echo '<div class="zpOrderDetail"><span>'.esc_html($key).'</span><strong>'.nl2br(esc_html((string)$value)).'</strong></div>';
                  }
                ?>
              </div>
            </main>

            <aside class="zpOrderSide">
              <div class="zpOrderActions">
                <?php if ($email) : ?><a class="button button-primary" href="mailto:<?php echo esc_attr($email); ?>">Odpisz</a><?php endif; ?>
                <?php if ($phone) : ?><a class="button" href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $phone)); ?>">Zadzwoń</a><?php endif; ?>
                <?php if (!empty($order['files'])) : ?><a class="button" href="<?php echo esc_url(zp_studio_order_admin_url('zp_studio_files_zip', $id)); ?>">Pobierz pliki ZIP</a><?php endif; ?>
                <a class="button" href="<?php echo esc_url(zp_studio_order_admin_url('zp_studio_order_pdf', $id)); ?>">PDF</a>
              </div>

              <?php if (!empty($order['files']) && is_array($order['files'])) : ?>
                <div class="zpOrderFiles">
                  <strong>Pliki klienta</strong>
                  <?php foreach ($order['files'] as $file) :
                    if (empty($file['url'])) { continue; }
                    $is_img = preg_match('/\.(jpe?g|png|webp|gif)$/i', $file['url']);
                  ?>
                    <a target="_blank" rel="noopener" href="<?php echo esc_url($file['url']); ?>">
                      <?php echo $is_img ? '<img src="'.esc_url($file['url']).'" alt="">' : '<span>📎</span>'; ?>
                      <span><?php echo esc_html($file['name'] ?? 'plik'); ?></span>
                    </a>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>

              <form method="post" class="zpOrderNote">
                <?php wp_nonce_field('zp_studio_simple_action'); ?>
                <input type="hidden" name="order_id" value="<?php echo esc_attr($id); ?>">
                <label><strong>Status</strong><br><select name="status"><?php foreach($statuses as $k=>$v): ?><option value="<?php echo esc_attr($k); ?>" <?php selected($status,$k); ?>><?php echo esc_html($v); ?></option><?php endforeach; ?></select></label>
                <textarea name="admin_note" placeholder="Notatka wewnętrzna po rozmowie / wycenie..."><?php echo esc_textarea($order['admin_note'] ?? ''); ?></textarea>
                <p><button class="button button-primary" name="zp_studio_simple_action" value="save">Zapisz</button></p>
              </form>

              <form method="post" onsubmit="return confirm('Usunąć to zamówienie/brief?')">
                <?php wp_nonce_field('zp_studio_simple_action'); ?>
                <input type="hidden" name="order_id" value="<?php echo esc_attr($id); ?>">
                <button class="button zpOrderDelete" name="zp_studio_simple_action" value="delete">Usuń zamówienie</button>
              </form>

              <?php if (!empty($order['status_log']) && is_array($order['status_log'])) : ?>
                <div class="zpStatusLog"><strong>Historia</strong><br>
                  <?php foreach(array_slice(array_reverse($order['status_log']), 0, 6) as $log) : ?>
                    <?php echo esc_html(($log['date'] ?? '') . ' — ' . ($statuses[$log['status'] ?? 'new'] ?? ($log['status'] ?? ''))); ?><br>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
            </aside>
          </div>
        </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
<?php }
