<?php
if (!defined('ABSPATH')) { exit; }

/**
 * ZAPROJEKTOWANI SUITE — Strony internetowe Katowice SEO/CMS v1.8.15
 * - pełny content domyślny z wersji HTML widgetów
 * - edytory: FAQ, opinie/trust, portfolio, pakiety
 * - seed/migracja danych, żeby po aktualizacji panel nie był pusty
 */


/* v2.2.500: force refresh hero section — nowe hero Strony internetowe Katowice 1:1, bez starego hero z CMS. */
add_action('init', function(){
  $ver_key = 'zp_suite_katowice_hero_497_version';
  if (get_option($ver_key) !== '2.2.500') {
    $sections = get_option('zp_suite_katowice_sections', []);
    if (!is_array($sections)) { $sections = []; }
    $sections['hero'] = zp_suite_katowice_default_section('hero');
    update_option('zp_suite_katowice_sections', $sections, false);
    update_option($ver_key, '2.2.500', false);
  }
}, 4);

function zp_suite_katowice_sections_map(){
  return [
    'hero'      => ['label'=>'Hero', 'file'=>'hero.html'],
    'portfolio' => ['label'=>'Portfolio', 'file'=>'portfolio.html'],
    'seo_boost' => ['label'=>'SEO boost / strony WWW', 'file'=>'seo-boost.html'],
    'trust'     => ['label'=>'Trust / opinie', 'file'=>'trust.html'],
    'proces'    => ['label'=>'Proces', 'file'=>'proces.html'],
    'klienci'   => ['label'=>'Klienci', 'file'=>'klienci.html'],
    'pakiety'   => ['label'=>'Pakiety', 'file'=>'pakiety.html'],
    'branze'    => ['label'=>'Branże', 'file'=>'branze.html'],
    'faq'       => ['label'=>'FAQ', 'file'=>'faq.html'],
  ];
}

function zp_suite_katowice_default_section($key){
  $m = zp_suite_katowice_sections_map();
  $file = ZP_SUITE_PATH.'templates/katowice/sections/'.($m[$key]['file'] ?? '');
  return file_exists($file) ? file_get_contents($file) : '';
}

function zp_suite_katowice_get_section($key){
  // Proces renderujemy z aktualnego template'u, żeby po aktualizacji nie trzymał starego HTML z opcji.
  // Treści kroków nadal są edytowalne w CMS i podstawiane niżej przez apply_process().
  if ($key === 'proces') {
    return zp_suite_katowice_default_section($key);
  }
  $saved = get_option('zp_suite_katowice_sections', []);
  if (is_array($saved) && isset($saved[$key]) && trim((string)$saved[$key]) !== '') {
    return (string)$saved[$key];
  }
  return zp_suite_katowice_default_section($key);
}

function zp_kat_e($v){ return esc_html((string)$v); }

function zp_kat_h1_format($v){
  // v2.2.409: clean H1 + one controlled emphasis span for the SEO phrase.
  $t = trim(wp_strip_all_tags((string)$v));
  if ($t === '') { return ''; }

  $known = [
    'Strony internetowe Katowice',
    'Sklepy internetowe Katowice',
    'Projektowanie logo Katowice',
  ];

  foreach ($known as $phrase) {
    if (mb_strpos($t, $phrase) === 0) {
      $rest = mb_substr($t, mb_strlen($phrase));
      return '<span class="zpH1Emphasis">'.esc_html($phrase).'</span>'.esc_html($rest);
    }
  }

  return esc_html($t);
}
function zp_kat_a($v){ return esc_attr((string)$v); }
function zp_kat_url($v){ return esc_url((string)$v); }

function zp_suite_katowice_clean_html($html){
  $html = str_replace('http://zaprojektowani.com/', 'https://zaprojektowani.com/', $html);
  $html = str_replace('<script src="/wp-content/web-font/lucide.min.js"></script>', '', $html);
  $html = str_replace("<script src='/wp-content/web-font/lucide.min.js'></script>", '', $html);
  return $html;
}

function zp_suite_katowice_defaults_struct(){
  $json = <<<'JSON'
{
  "page": [
    {
      "hero_kicker": "Strony internetowe Katowice / WordPress / SEO",
      "hero_h1": "Strony internetowe Katowice — strony firmowe gotowe na SEO i zapytania",
      "hero_lead": "Tworzymy strony internetowe dla firm z Katowic, Śląska i całej Polski: od strategii, struktury treści i projektu UX/UI, po wdrożenie WordPress, SEO techniczne, analitykę, szybkie formularze oraz przygotowanie pod Google Ads i kampanie Meta Ads.",
      "hero_mobile_video_dim": "10",
      "hero_mobile_video_navy": "48",
      "hero_mobile_video_opacity": "100",
      "hero_mobile_height": "620",
      "hero_cta_1": "Otrzymaj wycenę strony",
      "hero_cta_1_url": "/studio-wyceny/",
      "hero_cta_2": "Zobacz ofertę stron",
      "hero_cta_2_url": "#zpConfigGate",
      "portfolio_kicker": "Realizacje stron internetowych",
      "portfolio_title": "Strony internetowe i sklepy, które budują zaufanie przed pierwszym kontaktem.",
      "portfolio_lead": "Zobacz wybrane projekty stron firmowych, sklepów WooCommerce i serwisów usługowych. Każda realizacja ma własny cel: więcej zapytań, lepszy wizerunek, czytelniejszą ofertę, lokalne SEO albo sprzedaż online.",
      "trust_kicker": "Opinie i proces",
      "trust_title": "Tworzenie stron internetowych w Katowicach wymaga nie tylko dobrego wyglądu, ale też jasnego procesu i treści, które prowadzą do kontaktu.",
      "trust_lead": "Przy projekcie strony firmowej ważny jest spokój: wiesz, co robimy, po co to robimy i jaki będzie kolejny krok. Łączymy UX, WordPress, SEO, mobile i konwersję, żeby strona była gotowa do pracy po wdrożeniu.",
      "process_kicker": "Proces projektowania strony",
      "process_title": "Jak wygląda tworzenie strony internetowej w Zaprojektowani — od briefu do wdrożenia WordPress?",
      "process_top_label": "Tworzenie stron internetowych Katowice",
      "process_top_title": "Strategia, projekt UX/UI, treści, WordPress, SEO i przygotowanie pod zapytania",
      "logos_kicker": "Zaufali nam",
      "logos_title": "Projektowaliśmy strony internetowe, sklepy WooCommerce i branding dla firm z różnych branż.",
      "logos_lead": "Od usług lokalnych i kancelarii, przez beauty i medycynę estetyczną, po e-commerce i firmy B2B — pomagamy uporządkować ofertę, zaprojektować stronę internetową i przygotować fundament pod SEO oraz kampanie reklamowe.",
      "packages_kicker": "Oferta stron internetowych",
      "packages_title": "Wybierz zakres strony internetowej w Katowicach — landing page, strona firmowa, serwis premium albo projekt indywidualny.",
      "packages_lead": "Nie każda firma potrzebuje takiego samego zakresu. Dlatego pokazujemy kilka punktów startu: szybki landing page pod kampanię, stronę firmową WordPress, rozbudowany wariant premium lub indywidualną wycenę większego serwisu.",
      "industries_kicker": "Strony internetowe dla branż",
      "industries_title": "Projektujemy strony internetowe dla firm, które potrzebują zaufania, widoczności w Google i jasnej ścieżki do kontaktu.",
      "industries_lead": "Inaczej projektuje się stronę kancelarii, inaczej kliniki, salonu beauty, dewelopera, producenta czy sklepu premium. Dopasowujemy strukturę, treści, UX, SEO, formularze i CTA do branży, żeby strona pomagała użytkownikowi szybko zrozumieć ofertę.",
      "faq_kicker": "FAQ / Strony internetowe Katowice",
      "faq_title": "Najczęstsze pytania o projektowanie stron internetowych w Katowicach, WordPress, SEO i wycenę.",
      "faq_lead": "Zebraliśmy konkretne odpowiedzi o zakresie, procesie, treściach, SEO, sklepach WooCommerce, czasie realizacji i tym, jak przygotować się do stworzenia skutecznej strony firmowej."
    }
  ],
  "process": [
    {
      "icon": "target",
      "label": "Cel i strategia",
      "title": "Ustalamy cel strony internetowej i najważniejsze frazy SEO",
      "text": "Zaczynamy od tego, co strona ma realnie robić: generować zapytania, prezentować usługi, wspierać SEO lokalne, prowadzić kampanie reklamowe albo sprzedawać online. Na tym etapie określamy też główne frazy, strukturę podstron i najważniejsze CTA."
    },
    {
      "icon": "layout-template",
      "label": "Struktura",
      "title": "Projektujemy architekturę strony firmowej",
      "text": "Układamy logiczną strukturę: strona główna, oferta, podstrony usług, realizacje, FAQ, kontakt i ewentualne landingi branżowe. Dzięki temu użytkownik szybko rozumie ofertę, a Google łatwiej odczytuje tematykę strony."
    },
    {
      "icon": "pen-tool",
      "label": "UX/UI",
      "title": "Tworzymy indywidualny projekt UX/UI pod markę i konwersję",
      "text": "Projekt graficzny dopasowujemy do charakteru firmy, branży i celu strony. Dbamy o pierwsze wrażenie, czytelne nagłówki, mocne sekcje zaufania, wygodne formularze i wygląd, który nie przypomina przypadkowego szablonu."
    },
    {
      "icon": "file-text",
      "label": "Treści SEO",
      "title": "Przygotowujemy nagłówki, opisy usług i teksty na stronę",
      "text": "Strona internetowa potrzebuje nie tylko ładnych ekranów, ale też treści. Pomagamy napisać H1, H2, opisy usług, mikrocopy, FAQ i sekcje sprzedażowe tak, żeby były naturalne dla użytkownika i jednocześnie wspierały pozycjonowanie."
    },
    {
      "icon": "code-2",
      "label": "WordPress",
      "title": "Wdrażamy stronę w WordPress i dopracowujemy mobile",
      "text": "Po akceptacji projektu wdrażamy stronę w WordPress, ustawiamy responsywność, formularze, podstawowe integracje, animacje, szybkość ładowania i techniczne elementy potrzebne do stabilnego działania strony."
    },
    {
      "icon": "search-check",
      "label": "SEO techniczne",
      "title": "Przygotowujemy stronę pod indeksację, schema i dalsze pozycjonowanie",
      "text": "Pilnujemy struktury nagłówków, altów obrazków, linkowania wewnętrznego, szybkości, canonicali, danych schema, FAQ i podstaw Rank Math. To ważne, żeby strona miała dobry fundament pod widoczność w Google i odpowiedziach AI."
    },
    {
      "icon": "rocket",
      "label": "Start i rozwój",
      "title": "Uruchamiamy stronę i przygotowujemy ją pod kampanie oraz rozwój",
      "text": "Po publikacji strona może pracować dalej: pod SEO, Google Ads, kampanie Meta Ads, rozbudowę bloga, nowe landing pages i kolejne podstrony usługowe. Projekt traktujemy jako system, który można rozwijać razem z firmą."
    }
  ],
  "industries": [
    {
      "icon": "scale",
      "title": "Strony internetowe dla kancelarii",
      "label": "Kancelarie i usługi eksperckie",
      "short": "Profesjonalny wizerunek, zaufanie i jasna ścieżka do kontaktu.",
      "text": "Dla adwokatów, radców prawnych, doradców restrukturyzacyjnych i firm doradczych tworzymy strony, które budują wiarygodność, pokazują specjalizacje i prowadzą użytkownika do szybkiego kontaktu.",
      "items": "prezentacja specjalizacji i zespołu|podstrony usług pod SEO lokalne|formularz konsultacji i jasne CTA",
      "img": "https://zaprojektowani.com/wp-content/uploads/2026/05/strony_internetowe_dla_prawnikow.webp",
      "alt": "Strony internetowe Katowice dla kancelarii i prawników — projekt strony firmowej",
      "url": "/strony-internetowe-dla-kancelarii/"
    },
    {
      "icon": "building-2",
      "title": "Strony dla inwestycji i deweloperów",
      "label": "Deweloperzy i nieruchomości",
      "short": "Prezentacja inwestycji, lokali, lokalizacji i zapytań sprzedażowych.",
      "text": "Projektujemy strony inwestycji, osiedli i firm deweloperskich z naciskiem na prezentację lokali, lokalizację, leady sprzedażowe, formularze zapytań i wiarygodny wizerunek inwestora.",
      "items": "prezentacja inwestycji i etapów|karty lokali, rzuty, mapy, formularze|landing page pod kampanie reklamowe",
      "img": "https://zaprojektowani.com/wp-content/uploads/2026/05/strona_internetowa_dla_developera.webp",
      "alt": "Strony internetowe dla deweloperów i inwestycji — projektowanie stron www",
      "url": "/strony-internetowe-dla-deweloperow/"
    },
    {
      "icon": "sparkles",
      "title": "Strony dla salonów beauty",
      "label": "Beauty, medycyna estetyczna, kosmetologia",
      "short": "Subtelny wygląd premium, oferta zabiegów i rezerwacje.",
      "text": "W branży beauty strona musi jednocześnie wyglądać elegancko, wyjaśniać usługi i szybko prowadzić do rezerwacji. Dbamy o opisy zabiegów, opinie, efekty, FAQ i lokalne frazy SEO.",
      "items": "oferta zabiegów i lokalne SEO|sekcje zaufania, opinie, efekty, FAQ|CTA do rezerwacji, telefonu lub formularza",
      "img": "https://zaprojektowani.com/wp-content/uploads/2026/05/strona_internetowa_dla_branzy_beauty.webp",
      "alt": "Strony internetowe dla salonów beauty — projekt strony pod SEO lokalne",
      "url": "/strony-internetowe-dla-salonow-beauty/"
    },
    {
      "icon": "stethoscope",
      "title": "Strony internetowe dla lekarzy",
      "label": "Lekarze, gabinety, placówki medyczne",
      "short": "Czytelna ścieżka pacjenta, zaufanie i kontakt.",
      "text": "Dla gabinetów i specjalistów medycznych projektujemy strony, które jasno pokazują usługi, lokalizację, sposób rejestracji i najważniejsze odpowiedzi pacjenta przed pierwszym kontaktem.",
      "items": "usługi, specjalizacje i ścieżka pacjenta|FAQ, lokalizacja, rejestracja, telefon|responsywność i szybkość ładowania",
      "img": "https://zaprojektowani.com/wp-content/uploads/2026/05/strona_internetowa_dla_lekarza.webp",
      "alt": "Strony internetowe dla lekarzy i gabinetów medycznych — WordPress i SEO",
      "url": "/strony-internetowe-dla-lekarzy/"
    },
    {
      "icon": "factory",
      "title": "Strony i sklepy dla producentów",
      "label": "Producenci i B2B",
      "short": "Katalog produktów, zapytania ofertowe i wiarygodność B2B.",
      "text": "Producent potrzebuje strony, która porządkuje ofertę, pokazuje produkty i ułatwia zapytania. Często łączymy katalog, formularze, WooCommerce, integracje i treści pod konkretne grupy produktów.",
      "items": "katalog produktów i zapytania B2B|struktura pod kategorie i frazy produktowe|WooCommerce, formularze, integracje",
      "img": "https://zaprojektowani.com/wp-content/uploads/2026/05/strony_i_sklepy_dla_producentow.webp",
      "alt": "Strony i sklepy internetowe dla producentów — WooCommerce i zapytania B2B",
      "url": "/sklep-internetowy-dla-producenta/"
    },
    {
      "icon": "gem",
      "title": "Sklepy dla marek premium",
      "label": "E-commerce premium",
      "short": "Prezentacja produktu, storytelling i wygodny zakup.",
      "text": "Dla marek premium projektujemy e-commerce, który łączy estetykę, storytelling, szybkość, koszyk, płatności i kampanie. Strona ma nie tylko wyglądać drogo, ale pomagać sprzedawać z zaufaniem.",
      "items": "premium product storytelling|karty produktów, koszyk, płatności, dostawy|SEO, Meta Ads, Google Ads, remarketing",
      "img": "https://zaprojektowani.com/wp-content/uploads/2026/05/sklepy_internetowe_dla_marek_premium.webp",
      "alt": "Sklepy internetowe dla marek premium — WooCommerce, UX i sprzedaż online",
      "url": "/sklep-internetowy-dla-marki-premium/"
    }
  ],
  "faq": [
    {
      "q": "Czy projektujecie strony internetowe w Katowicach?",
      "a": "Tak. Projektujemy strony internetowe dla firm z Katowic, Śląska i całej Polski. Łączymy strategię, UX/UI, treści, WordPress, SEO techniczne, responsywność i analitykę, żeby strona firmowa nie była tylko wizytówką, ale realnym narzędziem do zdobywania zapytań."
    },
    {
      "q": "Czym różni się strona internetowa przygotowana pod SEO od zwykłej strony firmowej?",
      "a": "Strona przygotowana pod SEO ma zaplanowaną strukturę nagłówków, logiczne podstrony usługowe, linkowanie wewnętrzne, opisy usług, FAQ, alty obrazków, szybkość ładowania, schema i poprawne ustawienia indeksacji. Zwykła strona często kończy się na wyglądzie, a później wymaga kosztownych poprawek, żeby mogła skutecznie pozycjonować się w Google."
    },
    {
      "q": "Jakie strony internetowe tworzycie najczęściej?",
      "a": "Najczęściej tworzymy strony firmowe WordPress, landing page’e pod kampanie, strony usługowe, portfolio, serwisy premium, strony dla kancelarii, lekarzy, salonów beauty, deweloperów, producentów oraz sklepy internetowe WooCommerce. Każdy projekt dopasowujemy do branży, celu i sposobu pozyskiwania klientów."
    },
    {
      "q": "Czy pomagacie przygotować teksty na stronę internetową?",
      "a": "Tak. Przygotowujemy lub porządkujemy treści na stronę: H1, H2, opisy usług, sekcje sprzedażowe, CTA, FAQ, mikrocopy formularzy i teksty SEO. To ważne, bo dobrze napisana strona szybciej wyjaśnia ofertę, buduje zaufanie i ułatwia użytkownikowi wysłanie zapytania."
    },
    {
      "q": "Czy strona będzie działać dobrze na telefonie?",
      "a": "Tak. Projektujemy mobile-first: dbamy o czytelne nagłówki, wygodne menu, szybkie formularze, duże CTA, odpowiednie odstępy, lekkie animacje i szybkość ładowania. W wielu branżach większość pierwszych wejść pochodzi z telefonu, dlatego mobile nie jest dodatkiem, tylko jednym z kluczowych widoków."
    },
    {
      "q": "Czy wykonujecie sklepy internetowe WooCommerce?",
      "a": "Tak. Projektujemy sklepy WooCommerce, katalogi produktów, koszyki, checkouty, formularze B2B, proste konfiguratory, warianty produktów, płatności i dostawy. Jeśli sklep ma być głównym kanałem sprzedaży, rekomendujemy osobną strukturę SEO i dedykowaną podstronę pod frazy związane ze sklepami internetowymi."
    },
    {
      "q": "Ile kosztuje stworzenie strony internetowej?",
      "a": "Cena zależy od zakresu: liczby podstron, indywidualnego projektu graficznego, treści, animacji, integracji, formularzy, bloga, SEO, sklepu WooCommerce lub dodatkowych funkcji. Najprościej rozpocząć od Studia Wyceny, gdzie wybierasz typ projektu i opisujesz cel strony."
    },
    {
      "q": "Jak długo trwa projekt strony internetowej?",
      "a": "Czas realizacji zależy od zakresu i dostępności materiałów. Prosty landing page może powstać szybciej, a rozbudowana strona firmowa lub serwis premium wymaga więcej czasu na strukturę, treści, projekt, wdrożenie, mobile i testy. Na początku współpracy określamy realny harmonogram."
    },
    {
      "q": "Czy możecie odświeżyć starą stronę, zamiast robić wszystko od zera?",
      "a": "Tak, ale najpierw sprawdzamy, czy obecna strona ma sensowną bazę techniczną, strukturę i wydajność. Czasem wystarczy redesign i poprawa treści, a czasem lepszym rozwiązaniem jest nowy projekt, bo stara strona ogranicza SEO, szybkość, bezpieczeństwo lub rozwój oferty."
    },
    {
      "q": "Od czego najlepiej zacząć współpracę przy stronie internetowej?",
      "a": "Najlepiej zacząć od określenia celu strony, branży, najważniejszych usług, przykładów stylistyki i materiałów, które już masz. Możesz wejść w Studio Wyceny, wybrać zakres projektu i opisać, czy zależy Ci bardziej na wizerunku, SEO, zapytaniach, sprzedaży online czy kampaniach reklamowych."
    }
  ],
  "trust": [
    {
      "initial": "M",
      "name": "Magdalena Kasińska",
      "meta": "5.0/5 · Google",
      "text": "Najbardziej doceniam komunikację. Od początku wiedzieliśmy, co jest po naszej stronie, co robi zespół i kiedy zobaczymy kolejny etap. Strona wygląda profesjonalnie, ale ważniejsze jest to, że cały proces był spokojny i konkretny."
    },
    {
      "initial": "A",
      "name": "Aneta Waszkiewicz",
      "meta": "5.0/5 · Google · 2 miesiące temu",
      "text": "Profesjonalne podejście, świetny kontakt i nowoczesny projekt strony www. Wszystko wykonane sprawnie, z wyczuciem stylu i bez przeciągania decyzji. Efekt końcowy naprawdę robi wrażenie."
    },
    {
      "initial": "D",
      "name": "Dana Studniarek",
      "meta": "5.0/5 · Google · 4 miesiące temu",
      "text": "Największy plus: proces. Strona była układana krok po kroku, a nie przypadkowo. Najpierw struktura, potem wygląd, treści i wdrożenie. Dzięki temu od początku było wiadomo, dokąd zmierzamy."
    },
    {
      "initial": "K",
      "name": "Krzysztof Motylski",
      "meta": "5.0/5 · Google",
      "text": "Polecam z całego serca. Zrobili nam nową stronę i odświeżyli wizerunek. Wszystko jest spójne, eleganckie i w końcu na poziomie tego, co robimy na co dzień."
    },
    {
      "initial": "S",
      "name": "Sylwia Gasentzer",
      "meta": "5.0/5 · Google",
      "text": "Potrzebowałam strony, która będzie prosta w odbiorze, ale nie będzie wyglądała jak gotowy szablon. Udało się połączyć estetykę, czytelną ofertę i wygodny kontakt z klientem."
    }
  ],
  "portfolio": [
    {
      "slug": "ap",
      "icon": "monitor-smartphone",
      "title": "Apartament Piękna",
      "label": "Beauty premium / medycyna estetyczna",
      "strong": "Strona + rezerwacje + SEO lokalne",
      "desc": "Dla salonu medycyny estetycznej przygotowaliśmy stronę, która prowadzi klientkę od pierwszego wrażenia do rezerwacji wizyty. Kluczowe były: subtelny wygląd premium, jasne opisanie usług, lokalne SEO i wygodny system umawiania wizyt.",
      "url": "https://www.apartamentpiekna.pl/",
      "img": "https://zaprojektowani.com/wp-content/uploads/2026/06/apartament_mockup-scaled.webp",
      "alt": "Mockup strony internetowej Apartament Piękna — medycyna estetyczna Tarnów",
      "accent": "#e7a9b8",
      "bg": "radial-gradient(circle at 82% 10%,rgba(155,79,100,.22),transparent 34%),linear-gradient(135deg,#191013 0%,#2a171d 45%,#4b2430 100%)",
      "badges": "rezerwacje online, SEO lokalne, premium beauty",
      "review": "Zakres: UX/UI, WordPress, system rezerwacji, formularze, mobile, lokalne SEO i uporządkowanie komunikacji usług beauty."
    },
    {
      "slug": "siemianowski",
      "icon": "scale",
      "title": "Siemianowski",
      "label": "Kancelaria premium / prawo i restrukturyzacja",
      "strong": "Strona firmowa + branding + wizyty",
      "desc": "Dla kancelarii przygotowaliśmy elegancki system wizerunkowy i rozbudowaną stronę, która ma budować zaufanie jeszcze przed pierwszym telefonem. Struktura prowadzi przez specjalizacje, doświadczenie, kontakt i umawianie wizyt.",
      "url": "https://siemianowski.pl/",
      "img": "https://zaprojektowani.com/wp-content/uploads/2026/06/siemianowski_mockup.webp",
      "alt": "Mockup strony kancelarii Siemianowski — strona internetowa i branding premium",
      "accent": "#d8b579",
      "bg": "radial-gradient(circle at 82% 10%,rgba(191,142,79,.20),transparent 34%),radial-gradient(circle at 35% 75%,rgba(8,40,71,.28),transparent 42%),linear-gradient(135deg,#06101c 0%,#0b2138 48%,#102f4e 100%)",
      "badges": "branding premium, wiele podstron, umawianie wizyt",
      "review": "Zakres: branding, identyfikacja, UX/UI, WordPress, podstrony usługowe, system wizyt i dopracowanie mobile."
    },
    {
      "slug": "gravia",
      "icon": "hard-hat",
      "title": "Gravia",
      "label": "Infrastruktura / branża drogowa",
      "strong": "Strona B2B + usługi + kariera",
      "desc": "Dla biura projektów infrastrukturalnych zbudowaliśmy techniczny, mocny wizualnie serwis B2B. Strona porządkuje ofertę, pokazuje skalę realizacji i wzmacnia wiarygodność w kontaktach z inwestorami oraz kandydatami.",
      "url": "https://gravia.pl/",
      "img": "https://zaprojektowani.com/wp-content/uploads/2026/06/gravia_mockup.webp",
      "alt": "Mockup strony internetowej Gravia — biuro projektów infrastrukturalnych",
      "accent": "#ff5d56",
      "bg": "radial-gradient(circle at 82% 10%,rgba(218,9,0,.24),transparent 34%),linear-gradient(135deg,#08090b 0%,#15171b 46%,#3a0806 100%)",
      "badges": "techniczny UI, kariera, B2B",
      "review": "Zakres: architektura informacji, UX/UI, WordPress, prezentacja usług, realizacje, kariera i wizerunek premium dla B2B."
    },
    {
      "slug": "shothome",
      "icon": "home",
      "title": "ShotHome",
      "label": "Drzwi / podłogi / wykończenia",
      "strong": "Strona + SEO + darmowa wycena",
      "desc": "Dla ShotHome stworzyliśmy od podstaw stronę usługową, która skraca drogę od przeglądania oferty do wysłania zapytania. Projekt objął indywidualny UI, wdrożenie WordPress, strukturę SEO pod Warszawę, blog, customową darmową wycenę online, system realizacji oraz katalog produktów podłogowych z możliwością dodawania i edycji w panelu.",
      "url": "https://www.shothome.pl/",
      "img": "https://zaprojektowani.com/wp-content/uploads/2026/06/shothome_mockup.webp",
      "alt": "Mockup strony ShotHome — drzwi podłogi i wykończenia Warszawa",
      "accent": "#80897f",
      "bg": "radial-gradient(circle at 82% 10%,rgba(128,137,127,.30),transparent 35%),linear-gradient(135deg,#0b1110 0%,#19231d 48%,#80897f 100%)",
      "badges": "darmowa wycena, SEO Warszawa, panel realizacji",
      "review": "Zakres: projekt graficzny, WordPress, customowa wycena online, system realizacji, blog, SEO Warszawa i katalog produktów podłogowych."
    },
    {
      "slug": "proscarves",
      "icon": "shopping-cart",
      "title": "ProScarves",
      "label": "B2B custom / sklep sportowy",
      "strong": "E-commerce + zamówienia hurtowe",
      "desc": "Dla ProScarves projektujemy sklep pod dwa scenariusze sprzedaży: gotowe produkty dla fanów oraz zapytania B2B dla klubów i organizacji. Struktura łączy e-commerce, konfigurację zamówienia i prezentację produkcji customowej.",
      "url": "https://www.proscarves.com/",
      "img": "https://zaprojektowani.com/wp-content/uploads/2026/06/proscarves_mockup-scaled.webp",
      "alt": "Mockup sklepu internetowego ProScarves — szaliki i zamówienia B2B",
      "accent": "#55cfe3",
      "bg": "radial-gradient(circle at 82% 10%,rgba(73,190,207,.30),transparent 36%),radial-gradient(circle at 35% 72%,rgba(10,86,102,.24),transparent 42%),linear-gradient(135deg,#061011 0%,#092127 48%,#0b6571 100%)",
      "badges": "B2B custom, konfigurator, sklep detaliczny",
      "review": "Zakres: WooCommerce, ścieżka B2B, produkty gotowe, konfigurator zapytań, UX koszyka, mobile i prezentacja oferty."
    },
    {
      "slug": "krawiec",
      "icon": "scissors",
      "title": "Krawiec z dojazdem",
      "label": "Usługi premium / lokalne SEO",
      "strong": "Strona + rebranding + system wizyt",
      "desc": "Dla marki usługowej uporządkowaliśmy wizerunek, ofertę i proces umawiania wizyt. Strona wyjaśnia usługę krok po kroku, buduje zaufanie i wspiera lokalne SEO w wielu miastach.",
      "url": "https://krawieczdojazdem.pl/",
      "img": "https://zaprojektowani.com/wp-content/uploads/2026/06/kaminski_mockup-scaled.webp",
      "alt": "Mockup strony Krawiec z dojazdem — usługi krawieckie i system wizyt",
      "accent": "#d6b06b",
      "bg": "radial-gradient(circle at 82% 10%,rgba(205,165,88,.22),transparent 34%),linear-gradient(135deg,#070707 0%,#14110c 48%,#3a2b13 100%)",
      "badges": "system wizyt, rebranding, SEO lokalne",
      "review": "Zakres: rebranding, UX/UI, WordPress, system umawiania, formularze, treści usługowe, mobile i SEO lokalne."
    },
    {
      "slug": "papeterio",
      "icon": "shopping-bag",
      "title": "Papeterio",
      "label": "Papeteria / e-commerce",
      "strong": "Sklep WooCommerce",
      "desc": "Dla delikatnej marki produktowej przygotowaliśmy sklep, który nie przytłacza użytkownika. Ważne były zdjęcia, warianty, personalizacja, przejrzysty koszyk i spokojny, elegancki charakter zakupu.",
      "url": "https://papeterio.pl/",
      "img": "https://zaprojektowani.com/wp-content/uploads/2026/06/papeterio_mockup-scaled.webp",
      "alt": "Mockup sklepu WooCommerce Papeterio — papeteria i personalizacja produktów",
      "accent": "#e3b7a8",
      "bg": "radial-gradient(circle at 82% 10%,rgba(198,142,125,.22),transparent 34%),linear-gradient(135deg,#140d0d 0%,#261818 48%,#58352f 100%)",
      "badges": "WooCommerce, personalizacja, mobile UX",
      "review": "Zakres: sklep WooCommerce, warianty produktów, płatności, koszyk, mobile UX i estetyczna prezentacja marki."
    },
    {
      "slug": "bransoletka",
      "icon": "gem",
      "title": "Bransoletka24",
      "label": "E-commerce / detal + hurt",
      "strong": "Sklep WooCommerce B2B/B2C",
      "desc": "Dla Bransoletka24 przygotowaliśmy sklep, który obsługuje sprzedaż detaliczną i hurtową. Priorytetem była szybka nawigacja po kategoriach, wygodny checkout, warianty produktów i czytelna logika B2B.",
      "url": "https://bransoletka24.pl/",
      "img": "https://zaprojektowani.com/wp-content/uploads/2026/06/bransoletka24_mockup-scaled.webp",
      "alt": "Mockup sklepu internetowego Bransoletka24 — sprzedaż detaliczna i hurtowa",
      "accent": "#c69be0",
      "bg": "radial-gradient(circle at 82% 10%,rgba(151,95,168,.24),transparent 34%),linear-gradient(135deg,#070713 0%,#141329 48%,#35204a 100%)",
      "badges": "WooCommerce, checkout, B2B + B2C",
      "review": "Zakres: WooCommerce, logika hurtowa, kategorie, checkout, wishlist, mobile i przygotowanie pod kampanie sprzedażowe."
    },
    {
      "slug": "rutpoz",
      "icon": "flame",
      "title": "RUTPOŻ",
      "label": "PPOŻ / system zgłoszeń + AI",
      "strong": "Strona + system + AI",
      "desc": "Dla specjalistycznej firmy B2B połączyliśmy stronę ofertową z funkcjami, które pomagają obsługiwać zapytania: formularzami serwisowymi, kalkulatorami, raportami PDF i asystentem AI.",
      "url": "https://rutpoz.pl/",
      "img": "https://zaprojektowani.com/wp-content/uploads/2026/06/rutpoz_mockuP-scaled.webp",
      "alt": "Mockup strony RUTPOŻ — system PPOŻ formularze i asystent AI",
      "accent": "#ff8a42",
      "bg": "radial-gradient(circle at 82% 10%,rgba(255,102,21,.28),transparent 34%),linear-gradient(135deg,#100706 0%,#20100e 48%,#5b1510 100%)",
      "badges": "AI chatbot, kalkulatory, B2B",
      "review": "Zakres: strona firmowa, formularze serwisowe, kalkulatory, raporty PDF, asystent AI, SEO i prezentacja specjalistycznej oferty."
    }
  ],
  "pakiety": [
    {
      "slug": "landing",
      "icon": "mouse-pointer-click",
      "tag": "Szybka kampania / jedna oferta",
      "title": "Landing Page",
      "desc": "Dla firm, które chcą wypromować jedną usługę, produkt lub kampanię. Skupiamy się na prostym komunikacie, mocnym CTA i układzie, który prowadzi użytkownika do zapytania bez rozpraszania.",
      "best": "Najlepszy, gdy liczy się szybki start, jeden cel i wysoka konwersja.",
      "items": "jedna długa strona sprzedażowa|sekcje problemu, oferty, korzyści, procesu i FAQ|formularz kontaktowy lub zapytanie ofertowe|wersja mobilna, podstawowe SEO, analityka i szybkie ładowanie",
      "url": "/konfigurator-strony/?typ=landing-page"
    },
    {
      "slug": "starter",
      "icon": "building-2",
      "tag": "Mała firma / czytelna obecność online",
      "title": "Strona firmowa Starter",
      "desc": "Dla lokalnych firm, specjalistów i usługodawców, którzy potrzebują profesjonalnej strony z jasną ofertą, zaufaniem i prostą drogą do kontaktu. To solidna baza pod dalszy rozwój SEO.",
      "best": "Najlepszy, gdy chcesz wyglądać profesjonalnie i mieć uporządkowaną ofertę online.",
      "items": "strona główna + podstawowe podstrony|oferta, o firmie, realizacje/opinie i kontakt|responsywny projekt dopasowany do marki|podstawowa struktura SEO, formularz i optymalizacja szybkości",
      "url": "/konfigurator-strony/?typ=strona-firmowa-starter"
    },
    {
      "slug": "premium",
      "icon": "sparkles",
      "tag": "Marka premium / SEO i sprzedaż",
      "title": "Strona firmowa Premium",
      "desc": "Dla firm, które chcą nie tylko dobrze wyglądać, ale też mocniej wejść w Google, lepiej pokazać ofertę, budować zaufanie i przygotować stronę pod kampanie reklamowe.",
      "best": "Najlepszy, gdy strona ma pracować długofalowo na markę, zapytania i pozycjonowanie.",
      "items": "rozbudowana struktura pod usługi, branże i lokalizacje|mocny hero, sekcje zaufania, portfolio, proces, FAQ i CTA|copywriting, UX/UI i układ pod zapytania z Google oraz reklam|SEO techniczne, szybkość, mobile, analityka i przygotowanie pod kampanie",
      "url": "/konfigurator-strony/?typ=strona-firmowa-premium"
    },
    {
      "slug": "custom",
      "icon": "settings-2",
      "tag": "Nietypowy zakres / osobna wycena",
      "title": "Projekt indywidualny",
      "desc": "Dla większych wdrożeń: serwisów firmowych, sklepów WooCommerce, konfiguratorów, rozbudowanych formularzy, integracji, automatyzacji lub architektury SEO z wieloma podstronami.",
      "best": "Najlepszy, gdy standardowa strona firmowa nie wystarczy i potrzebna jest osobna logika.",
      "items": "indywidualna analiza celu, struktury i funkcji|sklep WooCommerce, landing pages lub serwis wielostronicowy|formularze, integracje, automatyzacje i konfiguratory|osobna wycena po opisie projektu i materiałach",
      "url": "/konfigurator-strony/?typ=projekt-indywidualny"
    }
  ]
}
JSON;
  $data = json_decode($json, true);
  return is_array($data) ? $data : [];
}

function zp_suite_katowice_get_struct($type){
  $defaults = zp_suite_katowice_defaults_struct();
  $default_rows = $defaults[$type] ?? [];
  $saved = get_option('zp_suite_katowice_'.$type, []);
  if (!is_array($saved) || empty($saved)) return $default_rows;
  if (count($saved) < count($default_rows)) return $default_rows;
  return array_values($saved);
}

function zp_suite_katowice_seed_content($force = false){
  $defaults = zp_suite_katowice_defaults_struct();
  foreach ($defaults as $type => $rows) {
    $current = get_option('zp_suite_katowice_'.$type, []);
    if ($force || !is_array($current) || count($current) < count($rows)) {
      update_option('zp_suite_katowice_'.$type, $rows, false);
    }
  }
  update_option('zp_suite_katowice_seed_version', '1.8.14', false);
}

add_action('plugins_loaded', function(){
  if (get_option('zp_suite_katowice_seed_version') !== '1.8.14') {
    // v1.8.14: odświeżamy seed procesu 1:1, ustawienia video mobile i poprawione obrazy branż.
    zp_suite_katowice_seed_content(true);
  }
}, 20);


add_action('plugins_loaded', function(){
  $rows = get_option('zp_suite_katowice_page', []);
  if (!is_array($rows) || empty($rows[0]) || !is_array($rows[0])) { return; }
  $old = trim(wp_strip_all_tags((string)($rows[0]['hero_h1'] ?? '')));
  if ($old === 'Strony internetowe Katowice — projektowanie stron WWW') {
    $rows[0]['hero_h1'] = 'Strony internetowe Katowice — strony firmowe gotowe na SEO i zapytania';
    update_option('zp_suite_katowice_page', $rows, false);
  }
}, 21);

function zp_suite_katowice_page_settings(){
  $rows = zp_suite_katowice_get_struct('page');
  return is_array($rows) && !empty($rows[0]) && is_array($rows[0]) ? $rows[0] : [];
}

function zp_suite_katowice_replace_first($pattern, $replacement, $html){
  $new = preg_replace($pattern, $replacement, $html, 1);
  return $new === null ? $html : $new;
}

function zp_suite_katowice_apply_page_copy($html, $key){
  $p = zp_suite_katowice_page_settings();
  if (!$p) return $html;
  if ($key === 'hero') {
    $html = zp_suite_katowice_replace_first('/<div class="zpWebHeroKat__eyebrow">.*?<\/div>/s', '<div class="zpWebHeroKat__eyebrow">'.zp_kat_e($p['hero_kicker'] ?? '').'</div>', $html);
    $hero_h1 = $p['hero_h1'] ?? '';
    if (trim(wp_strip_all_tags((string)$hero_h1)) === 'Strony internetowe Katowice — projektowanie stron WWW') {
      $hero_h1 = 'Strony internetowe Katowice — strony firmowe gotowe na SEO i zapytania';
    }
    $html = zp_suite_katowice_replace_first('/<h1 id="zpWebHeroKatTitle">.*?<\/h1>/s', '<h1 id="zpWebHeroKatTitle" class="zpServiceHeroH1">'.zp_kat_h1_format($hero_h1).'</h1>', $html);
    $html = zp_suite_katowice_replace_first('/<p class="zpWebHeroKat__lead">.*?<\/p>/s', '<p class="zpWebHeroKat__lead">'.wp_kses_post($p['hero_lead'] ?? '').'</p>', $html);
    $html = zp_suite_katowice_replace_first('/<a class="zpWebHeroKat__btn zpWebHeroKat__btn--primary" href="[^"]*">\s*<span>.*?<\/span>/s', '<a class="zpWebHeroKat__btn zpWebHeroKat__btn--primary" href="/studio-wyceny/"><span>'.zp_kat_e($p['hero_cta_1'] ?? 'Otrzymaj wycenę').'</span>', $html);
    $html = zp_suite_katowice_replace_first('/<a class="zpWebHeroKat__btn zpWebHeroKat__btn--ghost" href="[^"]*">\s*<span>.*?<\/span>/s', '<a class="zpWebHeroKat__btn zpWebHeroKat__btn--ghost" href="#zpConfigGate"><span>'.zp_kat_e($p['hero_cta_2'] ?? 'Zobacz ofertę').'</span>', $html);
  }
  $map = [
    'portfolio'=>['zpSS__kicker','zpSS__title','zpSS__lead','portfolio_kicker','portfolio_title','portfolio_lead'],
    'trust'=>['zpTrustCert__kicker','zpTrustCertTitle','zpTrustCert__lead','trust_kicker','trust_title','trust_lead'],
    'proces'=>['zpProcessFlow__kicker','zpProcessFlowTitle',null,'process_kicker','process_title',null],
    'klienci'=>['zpTrustedLogos__kicker','zpTrustedLogosTitle','zpTrustedLogos__lead','logos_kicker','logos_title','logos_lead'],
    'pakiety'=>['zpConfigGate__kicker','zpConfigGateTitle','zpConfigGate__lead','packages_kicker','packages_title','packages_lead'],
    'branze'=>['zpIndustryDark__kicker','zpIndustryDarkTitle','zpIndustryDark__lead','industries_kicker','industries_title','industries_lead'],
    'faq'=>['zpFaqKatNavy__kicker','zpFaqKatNavyTitle','zpFaqKatNavy__lead','faq_kicker','faq_title','faq_lead'],
  ];
  if (isset($map[$key])) {
    [$kClass,$titleId,$leadClass,$kOpt,$tOpt,$lOpt] = $map[$key];
    if ($kClass && isset($p[$kOpt])) $html = zp_suite_katowice_replace_first('/<span class="'.preg_quote($kClass,'/').'">.*?<\/span>/s', '<span class="'.$kClass.'">'.zp_kat_e($p[$kOpt]).'</span>', $html);
    if ($titleId && isset($p[$tOpt])) $html = zp_suite_katowice_replace_first('/<h2 id="'.preg_quote($titleId,'/').'">.*?<\/h2>/s', '<h2 id="'.$titleId.'">'.wp_kses_post($p[$tOpt]).'</h2>', $html);
    if ($leadClass && isset($p[$lOpt])) $html = zp_suite_katowice_replace_first('/<p class="'.preg_quote($leadClass,'/').'">.*?<\/p>/s', '<p class="'.$leadClass.'">'.wp_kses_post($p[$lOpt]).'</p>', $html);
  }
  if ($key === 'proces') {
    if (!empty($p['process_top_label'])) $html = zp_suite_katowice_replace_first('/<div class="zpProcessFlow__topCopy">\s*<span>.*?<\/span>/s', '<div class="zpProcessFlow__topCopy"><span>'.zp_kat_e($p['process_top_label']).'</span>', $html);
    if (!empty($p['process_top_title'])) $html = zp_suite_katowice_replace_first('/<div class="zpProcessFlow__topCopy"><span>.*?<\/span>\s*<strong>.*?<\/strong>/s', '<div class="zpProcessFlow__topCopy"><span>'.zp_kat_e($p['process_top_label'] ?? '').'</span><strong>'.zp_kat_e($p['process_top_title']).'</strong>', $html);
  }
  return $html;
}

function zp_suite_katowice_apply_hero_settings($html){
  $page = zp_suite_katowice_get_struct('page');
  $page = is_array($page) && !empty($page[0]) ? $page[0] : [];
  $dim = isset($page['hero_mobile_video_dim']) ? (float)$page['hero_mobile_video_dim'] : 10;
  $navy = isset($page['hero_mobile_video_navy']) ? (float)$page['hero_mobile_video_navy'] : 48;
  $opacity = isset($page['hero_mobile_video_opacity']) ? (float)$page['hero_mobile_video_opacity'] : 100;
  $height = isset($page['hero_mobile_height']) ? (float)$page['hero_mobile_height'] : 620;
  $dim = max(0, min(100, $dim));
  $navy = max(0, min(100, $navy));
  $opacity = max(0, min(100, $opacity));
  $height = max(500, min(900, $height));

  // v2.2.343: like home, keep hero video out of the critical mobile load path.
  $hero_video = '<video class="zpWebHeroKat__video" data-src="https://zaprojektowani.com/wp-content/uploads/videos/zaprojektowani_video.mp4" muted autoplay loop playsinline webkit-playsinline preload="none" data-zp-defer-video="1"></video>';
  if (strpos($html, 'zpWebHeroKat__video') !== false) {
    $html = preg_replace('/<video\b[^>]*class="[^"]*zpWebHeroKat__video[^"]*"[^>]*>[\s\S]*?<\/video>/i', $hero_video, $html, 1);
  }

  if (function_exists('zp_suite_mobile_hero_strip_video')) {
    $html = zp_suite_mobile_hero_strip_video($html);
  }
  if (function_exists('zp_suite_defer_hero_video_sources')) {
    $html = zp_suite_defer_hero_video_sources($html);
  }

  $style = '--heroMobileVideoDim:'.$dim.';--heroMobileVideoNavy:'.($navy/100).';--heroMobileVideoOpacity:'.($opacity/100).';--heroMobileHeight:'.$height.'px;';
  if (preg_match('/<section([^>]*class="[^"]*zpWebHeroKat[^"]*"[^>]*)style="([^"]*)"/i', $html)) {
    return preg_replace('/<section([^>]*class="[^"]*zpWebHeroKat[^"]*"[^>]*)style="([^"]*)"/i', '<section$1style="$2'.$style.'"', $html, 1);
  }
  return preg_replace('/<section([^>]*class="[^"]*zpWebHeroKat[^"]*"[^>]*)>/i', '<section$1 style="'.$style.'">', $html, 1);
}
function zp_suite_katowice_apply_process($html){
  $rows = zp_suite_katowice_get_struct('process');
  if (!$rows) return $html;
  $letters = range('a','z');
  $out = '';
  foreach($rows as $i=>$r){
    $n = str_pad((string)($i+1), 2, '0', STR_PAD_LEFT);
    $cls = $letters[$i] ?? 'a';
    $icon = sanitize_key($r['icon'] ?? 'layers-3');
    $extraClass = '';
    if ($i === 4) $extraClass = ' is-dark';
    if ($i === 6) $extraClass = ' is-final';

    $out .= '<article class="zpProcessFlow__card zpProcessFlow__card--'.$cls.$extraClass.'" data-zp-process-card>';
    $out .= '<span class="zpProcessFlow__num">'.$n.'</span>';
    $out .= '<div class="zpProcessFlow__icon" aria-hidden="true"><i data-lucide="'.$icon.'"></i></div>';
    $out .= '<div class="zpProcessFlow__cardCopy"><span>'.zp_kat_e($r['label'] ?? '').'</span><h3>'.zp_kat_e($r['title'] ?? '').'</h3><p>'.wp_kses_post($r['text'] ?? '').'</p></div>';

    // Dekoracyjne doły kart przywrócone 1:1 z widgetu HTML: pille, mini-listy, statusy i finalne elementy.
    if ($i === 0) {
      $out .= '<div class="zpProcessFlow__chips" aria-hidden="true"><em>Landing Page</em><em>Starter</em><em>Premium</em><em>Indywidualnie</em></div>';
    } elseif ($i === 1) {
      $out .= '<div class="zpProcessFlow__miniList"><div><i data-lucide="wallet"></i><span>budżet</span></div><div><i data-lucide="calendar-days"></i><span>termin</span></div><div><i data-lucide="target"></i><span>cel</span></div></div>';
    } elseif ($i === 2) {
      $out .= '<div class="zpProcessFlow__fileMock" aria-hidden="true"><span><i data-lucide="file-image"></i> mockup.webp</span><span><i data-lucide="file-text"></i> brief.pdf</span></div>';
    } elseif ($i === 3) {
      $out .= '<div class="zpProcessFlow__status" aria-hidden="true"><span></span><strong>analiza briefu</strong></div>';
    } elseif ($i === 4) {
      $out .= '<div class="zpProcessFlow__darkPills" aria-hidden="true"><em>UX/UI</em><em>WordPress</em><em>SEO</em><em>Mobile</em></div>';
    } elseif ($i === 5) {
      $out .= '<div class="zpProcessFlow__satisfaction" aria-hidden="true"><strong>100%</strong><span>satysfakcji</span></div>';
    } elseif ($i === 6) {
      $out .= '<div class="zpProcessFlow__techMini" aria-hidden="true"><div><i data-lucide="smartphone"></i><span>mobile</span></div><div><i data-lucide="search-check"></i><span>SEO</span></div><div><i data-lucide="bar-chart-3"></i><span>analityka</span></div></div>';
    }
    $out .= '</article>';
  }
  return preg_replace('/<article class="zpProcessFlow__card.*<\/article>/s', $out, $html, 1);
}

function zp_suite_katowice_apply_industries($html){
  $rows = zp_suite_katowice_get_struct('industries');
  if (!$rows) return $html;
  $out = '';
  foreach($rows as $i=>$r){
    $n = str_pad((string)($i+1), 2, '0', STR_PAD_LEFT);
    $icon = sanitize_key($r['icon'] ?? 'layout-template');
    $items = array_filter(array_map('trim', explode('|', $r['items'] ?? '')));
    $lis = '';
    foreach($items as $it){ $lis .= '<li><i data-lucide="check"></i><span>'.zp_kat_e($it).'</span></li>'; }
    $featured = $i === 2 ? ' is-featured' : '';
    $img = zp_kat_url($r['img'] ?? '');
    $fallback = 'https://zaprojektowani.com/wp-content/uploads/2026/05/sklepy_internetowe_dla_marek_premium.webp';
    if (stripos((string)($r['title'] ?? ''), 'premium') === false) { $fallback = $img; }
    $out .= '<article class="zpIndustryDark__card'.$featured.'" style="--img:url(\''.$img.'\')">';
    $out .= '<img class="zpIndustryDark__img" src="'.$img.'" alt="'.zp_kat_a($r['alt'] ?? $r['title'] ?? '').'" loading="lazy" decoding="async" data-zp-fallback="'.esc_url($fallback).'">';
    $industry_url = zp_kat_url($r['url'] ?? '/studio-wyceny/');
    $out .= '<span class="zpIndustryDark__num">'.$n.'</span><div class="zpIndustryDark__icon" aria-hidden="true"><i data-lucide="'.$icon.'"></i></div><div class="zpIndustryDark__base"><span>'.zp_kat_e($r['label'] ?? '').'</span><h3>'.zp_kat_e($r['title'] ?? '').'</h3><p>'.zp_kat_e($r['short'] ?? '').'</p></div><div class="zpIndustryDark__reveal"><p>'.wp_kses_post($r['text'] ?? '').'</p><ul>'.$lis.'</ul><a class="zpIndustryDark__link" href="'.esc_url($industry_url).'"><span>Zobacz kierunek</span><i data-lucide="arrow-up-right"></i></a></div></article>';
  }
  return preg_replace('/<article class="zpIndustryDark__card.*<\/article>/s', $out, $html, 1);
}

function zp_suite_katowice_apply_faq($html){
  $rows = zp_suite_katowice_get_struct('faq');
  $out = '';
  foreach($rows as $i=>$r){
    $n = str_pad((string)($i+1), 2, '0', STR_PAD_LEFT);
    $open = $i===0 ? ' is-open' : '';
    $expanded = $i===0 ? 'true' : 'false';
    $out .= '<article class="zpFaqKatNavy__item'.$open.'"><button class="zpFaqKatNavy__question" type="button" aria-expanded="'.$expanded.'"><span>'.$n.'</span><strong>'.zp_kat_e($r['q'] ?? '').'</strong><i data-lucide="plus"></i></button><div class="zpFaqKatNavy__answer"><p>'.wp_kses_post($r['a'] ?? '').'</p></div></article>';
  }
  return preg_replace('/<div class="zpFaqKatNavy__items"([^>]*)>.*?<\/div>\s*<\/div>\s*<\/section>/s', '<div class="zpFaqKatNavy__items"$1>'.$out.'</div></div></section>', $html, 1);
}

function zp_suite_katowice_google_logo(){
  return '<span class="zpTrustCert__googleLogo" aria-label="Google"><svg aria-hidden="true" viewBox="0 0 48 48"><path fill="#FFC107" d="M43.6 20.5H42V20H24v8h11.3C33.7 32.7 29.3 36 24 36c-6.6 0-12-5.4-12-12s5.4-12 12-12c3.1 0 5.9 1.2 8 3.1l5.7-5.7C34.1 6.1 29.3 4 24 4 12.9 4 4 12.9 4 24s8.9 20 20 20 20-8.9 20-20c0-1.3-.1-2.4-.4-3.5z"/><path fill="#FF3D00" d="M6.3 14.7l6.6 4.8C14.7 15.1 19 12 24 12c3.1 0 5.9 1.2 8 3.1l5.7-5.7C34.1 6.1 29.3 4 24 4 16.3 4 9.6 8.3 6.3 14.7z"/><path fill="#4CAF50" d="M24 44c5.2 0 9.9-2 13.5-5.2l-6.2-5.2C29.2 35.1 26.7 36 24 36c-5.2 0-9.6-3.3-11.3-7.8l-6.5 5C9.5 39.6 16.2 44 24 44z"/><path fill="#1976D2" d="M43.6 20.5H42V20H24v8h11.3c-.8 2.3-2.3 4.2-4 5.6l6.2 5.2C36.9 39.3 44 34 44 24c0-1.3-.1-2.4-.4-3.5z"/></svg></span>';
}

function zp_suite_katowice_apply_trust($html){
  $rows = zp_suite_katowice_get_struct('trust');
  $classes = ['a','b','c','d','e','f','g','h'];
  $google = zp_suite_katowice_google_logo();
  $out = '';
  foreach($rows as $i=>$r){
    $c = $classes[$i] ?? 'a';
    $out .= '<article class="zpTrustCert__review zpTrustCert__review--'.$c.'"><span class="zpTrustCert__quoteMark" aria-hidden="true">“</span><div class="zpTrustCert__reviewTop"><span class="zpTrustCert__avatar">'.zp_kat_e($r['initial'] ?? '').'</span><div><strong>'.zp_kat_e($r['name'] ?? '').'</strong><small>'.zp_kat_e($r['meta'] ?? '5.0/5 · Google').'</small></div>'.$google.'</div><div class="zpTrustCert__stars">★★★★★</div><p>'.zp_kat_e($r['text'] ?? '').'</p></article>';
  }
  return preg_replace('/<div class="zpTrustCert__reviews" aria-label="Wybrane opinie Google">.*?<\/div>\s*<footer/s', '<div class="zpTrustCert__reviews" aria-label="Wybrane opinie Google">'.$out.'</div><footer', $html, 1);
}

function zp_suite_katowice_apply_portfolio($html){
  $rows = zp_suite_katowice_get_struct('portfolio');
  $contact = '';
  if (preg_match('/<article class="zpSSCard zpSSCard--contact.*?<\/article>\s*<\/div>/s', $html, $m)) {
    $contact = preg_replace('/<\/div>\s*$/', '', $m[0]);
  }
  $out = '';
  foreach($rows as $i=>$r){
    $n = str_pad((string)($i+1), 2, '0', STR_PAD_LEFT);
    $slug = sanitize_html_class($r['slug'] ?? 'custom');
    // v2.2.564: force new 16:9 transparent floating mockups even when older CMS option data is still saved in WordPress.
    $mockups_564 = [
      'ap' => 'https://zaprojektowani.com/wp-content/uploads/2026/06/apartament_mockup-scaled.webp',
      'bransoletka' => 'https://zaprojektowani.com/wp-content/uploads/2026/06/bransoletka24_mockup-scaled.webp',
      'gravia' => 'https://zaprojektowani.com/wp-content/uploads/2026/06/gravia_mockup.webp',
      'krawiec' => 'https://zaprojektowani.com/wp-content/uploads/2026/06/kaminski_mockup-scaled.webp',
      'papeterio' => 'https://zaprojektowani.com/wp-content/uploads/2026/06/papeterio_mockup-scaled.webp',
      'proscarves' => 'https://zaprojektowani.com/wp-content/uploads/2026/06/proscarves_mockup-scaled.webp',
      'rutpoz' => 'https://zaprojektowani.com/wp-content/uploads/2026/06/rutpoz_mockuP-scaled.webp',
      'shothome' => 'https://zaprojektowani.com/wp-content/uploads/2026/06/shothome_mockup.webp',
      'siemianowski' => 'https://zaprojektowani.com/wp-content/uploads/2026/06/siemianowski_mockup.webp',
    ];
    if (isset($mockups_564[$slug])) {
      $r['img'] = $mockups_564[$slug];
    }
    $accent = $r['accent'] ?? '#99b7df';
    $bg = $r['bg'] ?? 'linear-gradient(135deg,#05070b,#102a4f)';
    $icon = sanitize_key($r['icon'] ?? 'monitor-smartphone');
    $badges = array_filter(array_map('trim', explode(',', $r['badges'] ?? '')));
    $badgeHtml = '';
    foreach($badges as $b){ $badgeHtml .= '<span class="zpSSBadge"><i data-lucide="check-circle-2"></i> '.zp_kat_e($b).'</span>'; }
    $style = '--cardBg:'.esc_attr($bg).';--accent:#fff;--accent2:'.esc_attr($accent).';--line:'.esc_attr($accent).';--text:#fff;--text2:rgba(255,255,255,.78);border-color:rgba(255,255,255,.12)';
    $out .= '<article class="zpSSCard zpSSCard--portfolio zpSSCard--'.$slug.'" data-zp-ss-card data-index="'.$i.'" style="'.$style.'"><span class="zpSSCard__decor" aria-hidden="true"></span><span class="zpSSCard__decor2" aria-hidden="true"></span><span class="zpSSCard__word" aria-hidden="true">'.zp_kat_e($slug).'</span><span class="zpSSCard__word zpSSCard__word--side" aria-hidden="true">'.$n.'</span><div class="zpSSCard__content"><div class="zpSSCard__copy"><div class="zpSSCard__label"><i data-lucide="'.$icon.'"></i> '.zp_kat_e($r['label'] ?? 'Projekt strony').'</div><h3 class="zpSSCard__h3">'.zp_kat_e($r['title'] ?? 'Projekt').'</h3><p class="zpSSCard__desc"><strong>'.zp_kat_e($r['strong'] ?? 'Strona internetowa').'</strong>'.zp_kat_e($r['desc'] ?? '').'</p><div class="zpSSCard__actions"><a class="zpSSBtn" href="'.zp_kat_url($r['url'] ?? '#').'" target="_blank" rel="noopener"><span>Zobacz stronę</span><i data-lucide="arrow-up-right"></i></a><a class="zpSSBtn zpSSBtn--ghost" href="/studio-wyceny/"><span>Zamów</span><i data-lucide="arrow-right"></i></a></div><div class="zpSSBadges" aria-hidden="true">'.$badgeHtml.'</div></div><div class="zpSSCard__visual" aria-hidden="true"><div class="zpSSCard__mock zpSSCard__mock--portfolio"><img src="'.zp_kat_url($r['img'] ?? '').'" alt="'.zp_kat_a($r['alt'] ?? (($r['title'] ?? 'Projekt').' — strona internetowa Zaprojektowani')).'" loading="lazy" decoding="async" data-no-lazy="1" data-skip-lazy="1" data-no-optimize="1" draggable="false"></div></div></div><aside class="zpSSReview" aria-label="Zakres projektu"><div class="zpSSReview__top"><div class="zpSSReview__person"><span class="zpSSReview__avatar">'.zp_kat_e(mb_substr($r['title'] ?? 'P', 0, 1)).'</span><div><p class="zpSSReview__name">'.zp_kat_e($r['title'] ?? 'Projekt').'</p><span class="zpSSReview__date">'.zp_kat_e($r['strong'] ?? 'Realizacja').'</span></div></div><span class="zpSSReview__g">'.$n.'</span></div><div class="zpSSReview__stars">★★★★★</div><p class="zpSSReview__text">'.zp_kat_e($r['review'] ?? '').'</p></aside></article>';
  }
  if ($contact) $out .= $contact;
  return preg_replace('/<article class="zpSSCard zpSSCard--portfolio.*?<\/article>\s*<\/div>\s*<\/div>\s*<\/section>/s', $out.'</div></div></section>', $html, 1);
}

function zp_suite_katowice_apply_pakiety($html){
  $rows = zp_suite_katowice_get_struct('pakiety');
  $out = '';
  foreach($rows as $i=>$r){
    $n = str_pad((string)($i+1), 2, '0', STR_PAD_LEFT);
    $slug = sanitize_html_class($r['slug'] ?? 'pakiet');
    $icon = sanitize_key($r['icon'] ?? 'layout-template');
    $items = array_filter(array_map('trim', explode('|', $r['items'] ?? '')));
    $lis = '';
    foreach($items as $it){ $lis .= '<li><i data-lucide="check"></i><span>'.zp_kat_e($it).'</span></li>'; }
    $featured = $i===2 ? ' is-featured' : '';
    $pkg_name = (string)($r['title'] ?? '');
    $pkg_url = '/studio-wyceny/?zpbs_service=web&zpbs_package='.rawurlencode($pkg_name).'#zpbsUltimate';
    $out .= '<article class="zpConfigGate__card zpConfigGate__card--'.$slug.$featured.'" data-zp-config-card><div class="zpConfigGate__hoverAura" aria-hidden="true"></div><span class="zpConfigGate__num">'.$n.'</span><div class="zpConfigGate__icon" aria-hidden="true"><i data-lucide="'.$icon.'"></i></div><div class="zpConfigGate__cardContent"><span class="zpConfigGate__tag">'.zp_kat_e($r['tag'] ?? '').'</span><h3>'.zp_kat_e($r['title'] ?? 'Pakiet').'</h3><p>'.zp_kat_e($r['desc'] ?? '').'</p><div class="zpConfigGate__best"><i data-lucide="sparkles"></i><span>'.zp_kat_e($r['best'] ?? '').'</span></div><ul>'.$lis.'</ul></div><a class="zpConfigGate__cardCta" href="'.esc_url($pkg_url).'"><span>Wybierz pakiet</span><i data-lucide="arrow-right"></i></a></article>';
  }
  return preg_replace('/<article class="zpConfigGate__card.*?<\/article>\s*<\/div>/s', $out.'</div>', $html, 1);
}


function zp_suite_katowice_final_link_patch($html, $key){
  $studio = '/studio-wyceny/';
  $trust = 'https://www.trustindex.io/reviews/zaprojektowani.com';
  $fb = 'https://www.facebook.com/zaprojektowanicom/reviews';
  if ($key === 'hero') {
    $html = preg_replace('/(<a class="zpWebHeroKat__btn zpWebHeroKat__btn--primary" href=")[^"]*(")/i', '$1'.$studio.'$2', $html, 1);
    $html = preg_replace('/(<a class="zpWebHeroKat__btn zpWebHeroKat__btn--ghost" href=")[^"]*(")/i', '$1#zpConfigGate$2', $html, 1);
  }
  if ($key === 'portfolio') {
    $html = str_replace('<span>Realizacje</span>', '<span>Zamów</span>', $html);
    $html = preg_replace('/(<a class="zpSSBtn zpSSBtn--ghost" href=")[^"]*(")/i', '$1'.$studio.'$2', $html);
    $html = preg_replace('/(<article class="zpSSCard zpSSCard--contact[\s\S]*?<a class="zpSSBtn" href=")[^"]*(")/i', '$1'.$studio.'$2', $html, 1);
  }
  if ($key === 'trust') {
    $html = preg_replace('/(<a class="zpTrustCert__score zpTrustCert__score--trust" href=")[^"]*(")/i', '$1'.$trust.'$2', $html, 1);
    $html = preg_replace('/(<a class="zpTrustCert__score zpTrustCert__score--facebook" href=")[^"]*(")/i', '$1'.$fb.'$2', $html, 1);
    $html = str_replace('https://zaprojektowani.com/wp-content/uploads/2026/06/zp_sygnet_ciemny-scaled.webp', 'https://zaprojektowani.com/wp-content/uploads/2026/06/zp_sygnet-scaled.webp', $html);
    $html = preg_replace('/(<a class="zpTrustCert__btn" href=")[^"]*(")/i', '$1'.$studio.'$2', $html, 1);
  }
  if ($key === 'proces') {
    $html = preg_replace('/(<a class="zpProcessFlow__cta" href=")[^"]*(")/i', '$1'.$studio.'$2', $html, 1);
  }
  if ($key === 'klienci') {
    $html = preg_replace('/(<a class="zpTrustedLogos__cta" href=")[^"]*(")/i', '$1'.$studio.'$2', $html, 1);
  }
  if ($key === 'pakiety') {
    $html = preg_replace('/(<a class="zpConfigGate__mainCta" href=")[^"]*(")/i', '$1'.$studio.'$2', $html, 1);
  }
  if ($key === 'faq') {
    $html = preg_replace('/(<a class="zpFaqKatNavy__sideCta" href=")[^"]*(")/i', '$1'.$studio.'$2', $html, 1);
  }
  $html = str_replace('/konfigurator-strony/', $studio, $html);
  return $html;
}

function zp_suite_katowice_section_render($key){
  $html = zp_suite_katowice_clean_html(zp_suite_katowice_get_section($key));
  $html = zp_suite_katowice_apply_page_copy($html, $key);
  if ($key === 'hero') $html = zp_suite_katowice_apply_hero_settings($html);
  if ($key === 'faq') $html = zp_suite_katowice_apply_faq($html);
  if ($key === 'trust') $html = zp_suite_katowice_apply_trust($html);
  if ($key === 'portfolio') $html = zp_suite_katowice_apply_portfolio($html);
  if ($key === 'pakiety') $html = zp_suite_katowice_apply_pakiety($html);
  if ($key === 'proces') $html = zp_suite_katowice_apply_process($html);
  if ($key === 'branze') $html = zp_suite_katowice_apply_industries($html);
  $html = zp_suite_katowice_final_link_patch($html, $key);
  return $html;
}

function zp_suite_katowice_enqueue_assets(){
  if(function_exists('zp_suite_enqueue_global_assets')) zp_suite_enqueue_global_assets();
  if(function_exists('zp_suite_enqueue_block_assets')) zp_suite_enqueue_block_assets('zp_contact_system');
  wp_enqueue_style('zp-suite-strony-katowice', ZP_SUITE_URL.'assets/css/blocks/strony-katowice.css', ['zp-suite-global'], function_exists('zp_suite_asset_version') ? zp_suite_asset_version('assets/css/blocks/strony-katowice.css') : ZP_SUITE_VERSION);
  wp_enqueue_script('zp-suite-strony-katowice', ZP_SUITE_URL.'assets/js/blocks/strony-katowice.js', ['zp-suite-global','zp-suite-lucide'], function_exists('zp_suite_asset_version') ? zp_suite_asset_version('assets/js/blocks/strony-katowice.js') : ZP_SUITE_VERSION, true);
}

function zp_suite_katowice_schema(){
  $rows = zp_suite_katowice_get_struct('faq');
  $faqItems = [];
  foreach($rows as $r){
    $faqItems[] = [
      '@type'=>'Question',
      'name'=>wp_strip_all_tags($r['q'] ?? ''),
      'acceptedAnswer'=>['@type'=>'Answer','text'=>wp_strip_all_tags($r['a'] ?? '')]
    ];
  }
  $graph = [
    '@context'=>'https://schema.org',
    '@graph'=>[
      [
        '@type'=>'WebPage',
        '@id'=>home_url('/strony-internetowe-katowice/#webpage'),
        'url'=>home_url('/strony-internetowe-katowice/'),
        'name'=>'Strony internetowe Katowice — projektowanie stron www i WordPress | Zaprojektowani.com',
        'description'=>'Projektowanie i tworzenie stron internetowych w Katowicach: strony firmowe WordPress, landing page, UX/UI, treści SEO, szybkie formularze, analityka, schema, linkowanie wewnętrzne i przygotowanie pod pozycjonowanie oraz kampanie reklamowe.',
        'isPartOf'=>['@id'=>home_url('/#website')],
        'about'=>['@id'=>home_url('/strony-internetowe-katowice/#service')],
        'primaryImageOfPage'=>[
          '@type'=>'ImageObject',
          'url'=>'https://zaprojektowani.com/wp-content/uploads/2026/05/mockup_strony_internetowe-scaled.webp',
          'caption'=>'Strony internetowe Katowice — projektowanie stron firmowych WordPress'
        ],
        'breadcrumb'=>['@id'=>home_url('/strony-internetowe-katowice/#breadcrumb')],
        'inLanguage'=>'pl-PL'
      ],
      [
        '@type'=>'Service',
        '@id'=>home_url('/strony-internetowe-katowice/#service'),
        'name'=>'Strony internetowe Katowice',
        'serviceType'=>'Projektowanie stron internetowych Katowice, tworzenie stron internetowych Katowice, strony WordPress, UX/UI, SEO techniczne, strony firmowe i landing page',
        'description'=>'Zaprojektowani.com projektuje i tworzy strony internetowe dla firm z Katowic, Śląska i całej Polski. Usługa obejmuje strategię, strukturę strony, UX/UI, treści, WordPress, responsywność, SEO techniczne, linkowanie wewnętrzne, schema, analitykę, formularze i przygotowanie pod Google Ads oraz Meta Ads.',
        'provider'=>['@type'=>'ProfessionalService','@id'=>home_url('/#organization'),'name'=>'Zaprojektowani.com','url'=>home_url('/')],
        'areaServed'=>[
          ['@type'=>'City','name'=>'Katowice'],
          ['@type'=>'AdministrativeArea','name'=>'Śląsk'],
          ['@type'=>'Country','name'=>'Polska']
        ],
        'hasOfferCatalog'=>[
          '@type'=>'OfferCatalog',
          'name'=>'Oferta stron internetowych',
          'itemListElement'=>[
            ['@type'=>'Offer','itemOffered'=>['@type'=>'Service','name'=>'Landing page pod kampanię']],
            ['@type'=>'Offer','itemOffered'=>['@type'=>'Service','name'=>'Strona firmowa WordPress']],
            ['@type'=>'Offer','itemOffered'=>['@type'=>'Service','name'=>'Strona internetowa premium']],
            ['@type'=>'Offer','itemOffered'=>['@type'=>'Service','name'=>'Indywidualny serwis firmowy']],
            ['@type'=>'Offer','itemOffered'=>['@type'=>'Service','name'=>'Strona WordPress dla firmy lokalnej']],
            ['@type'=>'Offer','itemOffered'=>['@type'=>'Service','name'=>'Redesign strony internetowej']],
            ['@type'=>'Offer','itemOffered'=>['@type'=>'Service','name'=>'Podstrony SEO dla usług i branż']]
          ]
        ],
        'url'=>home_url('/strony-internetowe-katowice/')
      ],
      [
        '@type'=>'BreadcrumbList',
        '@id'=>home_url('/strony-internetowe-katowice/#breadcrumb'),
        'itemListElement'=>[
          ['@type'=>'ListItem','position'=>1,'name'=>'Start','item'=>home_url('/')],
          ['@type'=>'ListItem','position'=>2,'name'=>'Strony internetowe Katowice','item'=>home_url('/strony-internetowe-katowice/')]
        ]
      ],
      [
        '@type'=>'FAQPage',
        '@id'=>home_url('/strony-internetowe-katowice/#faq'),
        'mainEntity'=>$faqItems
      ]
    ]
  ];
  return '<script type="application/ld+json">'.wp_json_encode($graph, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES).'</script>';
}

function zp_suite_katowice_render(){
  zp_suite_katowice_enqueue_assets();
  $map = zp_suite_katowice_sections_map();
  ob_start();
  echo '<main id="zpStronyKatowice" class="zpStronyKatowice" data-zp-suite-page="strony-internetowe-katowice">';
  foreach($map as $key=>$info){
    echo "\n<!-- ZP Suite / ".esc_html($info['label'])." -->\n";
    echo zp_suite_katowice_section_render($key);
  }
  echo '<!-- ZP Suite / kontakt pod FAQ -->'.do_shortcode('[zp_contact_system]').zp_suite_katowice_schema().'</main>';
  return ob_get_clean();
}
add_shortcode('zp_strony_internetowe_katowice', 'zp_suite_katowice_render');
add_shortcode('zp_page_strony_katowice', 'zp_suite_katowice_render');

add_action('wp_enqueue_scripts', function(){
  if (is_admin()) return;
  if (is_singular()) {
    $post = get_post();
    if ($post && (has_shortcode($post->post_content, 'zp_strony_internetowe_katowice') || has_shortcode($post->post_content, 'zp_page_strony_katowice'))) {
      zp_suite_katowice_enqueue_assets();
    }
  }
}, 20);

add_action('admin_menu', function(){
  add_submenu_page('zp-suite', 'Podstrony', 'Podstrony', 'manage_options', 'zp-suite-pages', 'zp_suite_katowice_pages_index');
}, 35);

function zp_suite_katowice_pages_index(){
  if (!current_user_can('manage_options')) return;
  if (($_GET['sub'] ?? '') === 'strony-katowice') { zp_suite_katowice_admin_page(); return; }
  if (($_GET['sub'] ?? '') === 'sklepy-katowice') {
    if (function_exists('zp_suite_shop_katowice_admin_page')) { zp_suite_shop_katowice_admin_page(); return; }
    echo '<div class="wrap"><h1>Sklepy internetowe Katowice</h1><p>Moduł CMS nie został jeszcze załadowany. Odśwież stronę po aktywacji wtyczki.</p></div>';
    return;
  }
  echo '<div class="wrap zpSuiteAdmin zpKatAdmin"><div class="zpSuiteHero"><span class="zpSuiteBadge">Podstrony</span><h1>Centrum podstron Zaprojektowani</h1><p>Jedno miejsce na landing pages i huby SEO. Docelowo tutaj będą strony internetowe, sklepy, logo i branding, kampanie Meta Ads oraz kolejne podstrony branżowe.</p></div><div class="zpKatPageGrid"><div class="zpKatPageCard is-ready"><strong>Aktywna</strong><h2>Strony internetowe Katowice</h2><p>Shortcode: <code>[zp_strony_internetowe_katowice]</code></p><a class="button button-primary" href="'.esc_url(admin_url('admin.php?page=zp-suite-pages&sub=strony-katowice')).'">Edytuj podstronę</a></div><div class="zpKatPageCard is-ready"><strong>Aktywna</strong><h2>Sklepy internetowe Katowice</h2><p>Shortcode: <code>[zp_sklepy_internetowe_katowice]</code></p><a class="button button-primary" href="'.esc_url(admin_url('admin.php?page=zp-suite-pages&sub=sklepy-katowice')).'">Edytuj podstronę</a></div><div class="zpKatPageCard"><strong>Wkrótce</strong><h2>Logo i branding Katowice</h2><p>Przygotowane pod kolejny hub SEO.</p></div></div></div>';
}

function zp_suite_katowice_save_struct($type, $fields){
  $rows = [];
  $src = $_POST['zp_'.$type] ?? [];
  if (!is_array($src)) return;
  foreach($src as $row){
    $clean = [];
    foreach($fields as $f){
      $clean[$f] = isset($row[$f]) ? wp_kses_post(wp_unslash((string)$row[$f])) : '';
    }
    if (trim(implode('', $clean)) !== '') $rows[] = $clean;
  }
  update_option('zp_suite_katowice_'.$type, $rows, false);
}

function zp_suite_katowice_admin_page(){
  if (!current_user_can('manage_options')) return;
  $map = zp_suite_katowice_sections_map();
  if (!empty($_POST['zp_katowice_nonce']) && wp_verify_nonce($_POST['zp_katowice_nonce'], 'zp_katowice_save')) {
    if (!empty($_POST['zp_seed_katowice'])) {
      zp_suite_katowice_seed_content(true);
      echo '<div class="notice notice-success"><p>Wgrano pełny domyślny content z wersji HTML widgetów.</p></div>';
    } elseif (!empty($_POST['zp_reset_katowice'])) {
      delete_option('zp_suite_katowice_sections');
      foreach(['page','faq','trust','portfolio','pakiety','process','industries'] as $t) delete_option('zp_suite_katowice_'.$t);
      zp_suite_katowice_seed_content(true);
      echo '<div class="notice notice-success"><p>Przywrócono pełne domyślne ustawienia.</p></div>';
    } else {
      $new = [];
      foreach($map as $key=>$info){ $new[$key] = isset($_POST['zp_katowice'][$key]) ? wp_unslash((string)$_POST['zp_katowice'][$key]) : ''; }
      update_option('zp_suite_katowice_sections', $new, false);
      zp_suite_katowice_save_struct('faq', ['q','a']);
      zp_suite_katowice_save_struct('trust', ['initial','name','meta','text']);
      zp_suite_katowice_save_struct('portfolio', ['slug','icon','title','label','strong','desc','url','img','alt','accent','bg','badges','review']);
      zp_suite_katowice_save_struct('pakiety', ['slug','icon','tag','title','desc','best','items','url']);
      zp_suite_katowice_save_struct('page', ['hero_kicker','hero_h1','hero_lead','hero_cta_1','hero_cta_1_url','hero_cta_2','hero_cta_2_url','hero_mobile_video_dim','hero_mobile_video_navy','hero_mobile_video_opacity','hero_mobile_height','portfolio_kicker','portfolio_title','portfolio_lead','trust_kicker','trust_title','trust_lead','process_kicker','process_title','process_top_label','process_top_title','logos_kicker','logos_title','logos_lead','packages_kicker','packages_title','packages_lead','industries_kicker','industries_title','industries_lead','faq_kicker','faq_title','faq_lead']);
      // v1.8.14: twardy zapis ustawień mobile hero video — niezależny od JS/repeaterów admina.
      if (isset($_POST['zp_page'][0]) && is_array($_POST['zp_page'][0])) {
        $zp_page_rows = get_option('zp_suite_katowice_page', []);
        $zp_page_row = (is_array($zp_page_rows) && !empty($zp_page_rows[0]) && is_array($zp_page_rows[0])) ? $zp_page_rows[0] : [];
        $zp_src_page = wp_unslash($_POST['zp_page'][0]);
        foreach (['hero_mobile_video_dim','hero_mobile_video_navy','hero_mobile_video_opacity','hero_mobile_height'] as $zp_mobile_key) {
          if (isset($zp_src_page[$zp_mobile_key])) {
            $zp_val = (float) $zp_src_page[$zp_mobile_key];
            if ($zp_mobile_key === 'hero_mobile_height') { $zp_val = max(460, min(820, $zp_val)); }
            else { $zp_val = max(0, min(100, $zp_val)); }
            $zp_page_row[$zp_mobile_key] = (string) $zp_val;
          }
        }
        $zp_page_rows[0] = $zp_page_row;
        update_option('zp_suite_katowice_page', $zp_page_rows, false);
      }
      zp_suite_katowice_save_struct('process', ['icon','label','title','text']);
      zp_suite_katowice_save_struct('industries', ['icon','label','title','short','text','items','img','alt','url']);
      echo '<div class="notice notice-success"><p>Zapisano podstronę.</p></div>';
    }
  }
  $faq = zp_suite_katowice_get_struct('faq');
  $trust = zp_suite_katowice_get_struct('trust');
  $portfolio = zp_suite_katowice_get_struct('portfolio');
  $pakiety = zp_suite_katowice_get_struct('pakiety');
  $page = zp_suite_katowice_get_struct('page');
  $page = is_array($page) && !empty($page[0]) ? $page[0] : [];
  $process = zp_suite_katowice_get_struct('process');
  $industries = zp_suite_katowice_get_struct('industries');
  echo '<div class="wrap zpSuiteAdmin zpKatAdmin"><div class="zpSuiteHero"><span class="zpSuiteBadge">Podstrony / SEO landing</span><h1>Strony internetowe Katowice</h1><p>Shortcode: <code>[zp_strony_internetowe_katowice]</code>. Treści z HTML widgetów są już wgrane do edytorów poniżej. Kontakt z głównej jest automatycznie pod FAQ.</p><div class="zpKatHeroActions"><a class="button button-primary" href="'.esc_url(home_url('/strony-internetowe-katowice/')).'" target="_blank">Zobacz podstronę</a><button class="button" form="zpKatForm" name="zp_seed_katowice" value="1">Wgraj pełny content domyślny</button></div></div><form method="post" id="zpKatForm" class="zpKatForm">';
  wp_nonce_field('zp_katowice_save','zp_katowice_nonce');
  echo '<div class="zpKatLayout"><aside class="zpKatSide"><button type="button" class="is-active" data-tab="seo">SEO</button><button type="button" data-tab="hero">Hero i nagłówki</button><button type="button" data-tab="process">Proces <em>'.count($process).'</em></button><button type="button" data-tab="industries">Branże <em>'.count($industries).'</em></button><button type="button" data-tab="portfolio">Portfolio <em>'.count($portfolio).'</em></button><button type="button" data-tab="trust">Opinie <em>'.count($trust).'</em></button><button type="button" data-tab="pakiety">Pakiety <em>'.count($pakiety).'</em></button><button type="button" data-tab="faq">FAQ <em>'.count($faq).'</em></button><button type="button" data-tab="raw">HTML sekcji</button><button type="submit" class="button button-primary zpKatSave">Zapisz całość</button></aside><main class="zpKatMain">';
  echo '<section class="zpPanel is-active" data-panel="seo"><div class="zpCard"><h2>SEO i flow tej podstrony</h2><p class="description">Ten ekran traktuj jako checklistę przed publikacją. Treści zostały ustawione pod hub: strony internetowe Katowice + tworzenie/projektowanie stron + strony firmowe + Śląsk.</p><div class="zpKatSeoGrid"><div><strong>Focus keyword</strong><p>strony internetowe Katowice</p></div><div><strong>Frazy dodatkowe</strong><p>tworzenie stron internetowych Katowice<br>projektowanie stron internetowych Katowice<br>strony firmowe Katowice<br>strony internetowe Śląsk</p></div><div><strong>Meta title</strong><p>Strony internetowe Katowice | Strony gotowe na SEO i zapytania — Zaprojektowani</p></div><div><strong>Meta description</strong><p>Projektujemy strony internetowe w Katowicach i na Śląsku: UX/UI, WordPress, SEO, landing page, strony firmowe i sklepy WooCommerce. Zobacz realizacje i zamów wycenę.</p></div><div><strong>H1</strong><p>Strony internetowe Katowice — strony firmowe gotowe na SEO i zapytania</p></div><div><strong>Canonical / URL</strong><p>/strony-internetowe-katowice/</p></div></div><h3>Ścieżka użytkownika</h3><ol class="zpKatCheck"><li>Hero: od razu mówi co robimy, dla kogo i z jakim efektem.</li><li>Portfolio: pokazuje dowody, nie tylko deklaracje.</li><li>Trust: dodaje społeczny dowód i obniża ryzyko kontaktu.</li><li>Proces: tłumaczy krok po kroku, jak będzie wyglądała współpraca.</li><li>Pakiety: porządkują decyzję bez podawania sztywnej ceny.</li><li>FAQ + kontakt: domykają obiekcje i prowadzą do wyceny.</li></ol><p class="description">Schema FAQ i Service generuje się automatycznie z zapisanych pytań FAQ. Alty portfolio edytujesz w kartach projektów.</p></div></section>';

  echo '<section class="zpPanel" data-panel="hero"><div class="zpCard"><h2>Hero i nagłówki sekcji</h2><p>Tu edytujesz teksty prowadzące użytkownika przez całą podstronę: H1, leady, CTA oraz nagłówki H2 kolejnych sekcji.</p><div class="zpRepeater" data-repeater="page">'; zp_suite_katowice_row('page',0,$page); echo '</div></div></section>';
  echo '<section class="zpPanel" data-panel="process"><div class="zpCard"><h2>Proces tworzenia strony</h2><p>Edytuj kroki procesu, które tłumaczą klientowi jak wygląda współpraca. Krótkie, konkretne kroki działają najlepiej.</p><div class="zpRepeater" data-repeater="process">'; foreach($process as $i=>$r) zp_suite_katowice_row('process',$i,$r); echo '</div><p><button type="button" class="button" data-add="process">Dodaj krok procesu</button></p></div></section>';
  echo '<section class="zpPanel" data-panel="industries"><div class="zpCard"><h2>Branże / landing pages</h2><p>Te karty budują linkowanie do przyszłych podstron branżowych. Możesz zmieniać treść, obraz, ALT i adres URL.</p><div class="zpRepeater" data-repeater="industries">'; foreach($industries as $i=>$r) zp_suite_katowice_row('industries',$i,$r); echo '</div><p><button type="button" class="button" data-add="industries">Dodaj branżę</button></p></div></section>';
  echo '<section class="zpPanel" data-panel="portfolio"><div class="zpCard"><h2>Karty portfolio</h2><p>Edytujesz projekty widoczne w scroll reveal. Możesz zmienić kolejność, tło, akcent, ikonę i zdjęcie z Media Library.</p><div class="zpRepeater" data-repeater="portfolio">'; foreach($portfolio as $i=>$r) zp_suite_katowice_row('portfolio',$i,$r); echo '</div><p><button type="button" class="button" data-add="portfolio">Dodaj projekt</button></p></div></section>';
  echo '<section class="zpPanel" data-panel="trust"><div class="zpCard"><h2>Opinie w trust</h2><p>Opinie z sekcji zaufania. Krótkie, naturalne wypowiedzi działają najlepiej.</p><div class="zpRepeater" data-repeater="trust">'; foreach($trust as $i=>$r) zp_suite_katowice_row('trust',$i,$r); echo '</div><p><button type="button" class="button" data-add="trust">Dodaj opinię</button></p></div></section>';
  echo '<section class="zpPanel" data-panel="pakiety"><div class="zpCard"><h2>Pakiety</h2><p>Zakresy przejścia do konfiguratora. Lista elementów używa separatora <code>|</code>.</p><div class="zpRepeater" data-repeater="pakiety">'; foreach($pakiety as $i=>$r) zp_suite_katowice_row('pakiety',$i,$r); echo '</div><p><button type="button" class="button" data-add="pakiety">Dodaj pakiet</button></p></div></section>';
  echo '<section class="zpPanel" data-panel="faq"><div class="zpCard"><h2>FAQ</h2><p>Te pytania zasilają zarówno sekcję FAQ, jak i schema FAQPage.</p><div class="zpRepeater" data-repeater="faq">'; foreach($faq as $i=>$r) zp_suite_katowice_row('faq',$i,$r); echo '</div><p><button type="button" class="button" data-add="faq">Dodaj pytanie</button></p></div></section>';
  echo '<section class="zpPanel" data-panel="raw"><div class="zpCard"><h2>HTML sekcji</h2><p>Zaawansowana edycja bazowych sekcji. Edytory powyżej nadpisują listy: FAQ, opinie, portfolio i pakiety.</p>'; foreach($map as $key=>$info){ echo '<details><summary><strong>'.esc_html($info['label']).'</strong></summary><textarea name="zp_katowice['.esc_attr($key).']" class="zpCodeArea">'.esc_textarea(zp_suite_katowice_get_section($key)).'</textarea></details>'; } echo '</div></section>';
  echo '</main></div><p class="zpKatBottom"><button class="button button-primary button-hero">Zapisz całość</button> <button class="button" name="zp_reset_katowice" value="1" onclick="return confirm(\'Przywrócić pełny domyślny content?\')">Reset do pełnego contentu</button></p></form>';
  echo '<template id="tpl_faq">'; zp_suite_katowice_row('faq','__i__',[]); echo '</template><template id="tpl_trust">'; zp_suite_katowice_row('trust','__i__',[]); echo '</template><template id="tpl_portfolio">'; zp_suite_katowice_row('portfolio','__i__',[]); echo '</template><template id="tpl_pakiety">'; zp_suite_katowice_row('pakiety','__i__',[]); echo '</template><template id="tpl_process">'; zp_suite_katowice_row('process','__i__',[]); echo '</template><template id="tpl_industries">'; zp_suite_katowice_row('industries','__i__',[]); echo '</template></div>';
}

function zp_suite_katowice_field($name,$label,$v,$type='input',$hint=''){
  $html = '<label class="zpField"><strong>'.esc_html($label).'</strong>';
  if ($type === 'textarea') $html .= '<textarea name="'.$name.'">'.esc_textarea($v).'</textarea>';
  elseif ($type === 'color') $html .= '<input type="color" name="'.$name.'" value="'.esc_attr($v ?: '#102a4f').'">';
  elseif ($type === 'url') $html .= '<input type="url" name="'.$name.'" value="'.esc_attr($v).'">';
  else $html .= '<input type="text" name="'.$name.'" value="'.esc_attr($v).'">';
  if ($hint) $html .= '<small>'.esc_html($hint).'</small>';
  return $html.'</label>';
}

function zp_suite_katowice_row($type,$i,$r){
  $title = $r['title'] ?? ($r['q'] ?? ($r['name'] ?? 'Nowy element'));
  echo '<details class="zpRepeatItem" open><summary><span class="zpDragHandle">↕</span><strong>'.esc_html($title ?: 'Nowy element').'</strong><button type="button" class="button-link-delete" data-remove>Usuń</button></summary><div class="zpRepeatFields">';
  $p = 'zp_'.$type.'['.$i.']';
  if($type==='faq'){
    echo zp_suite_katowice_field($p.'[q]','Pytanie',$r['q']??'');
    echo zp_suite_katowice_field($p.'[a]','Odpowiedź',$r['a']??'','textarea');
  }
  if($type==='trust'){
    echo '<div class="zpFieldGrid">';
    echo zp_suite_katowice_field($p.'[initial]','Inicjał',$r['initial']??'');
    echo zp_suite_katowice_field($p.'[name]','Imię / nazwa',$r['name']??'');
    echo zp_suite_katowice_field($p.'[meta]','Meta',$r['meta']??'5.0/5 · Google');
    echo '</div>';
    echo zp_suite_katowice_field($p.'[text]','Treść opinii',$r['text']??'','textarea');
  }
  if($type==='portfolio'){
    echo '<div class="zpFieldGrid">';
    echo zp_suite_katowice_field($p.'[slug]','Slug / klasa karty',$r['slug']??'');
    echo zp_suite_katowice_field($p.'[icon]','Ikona Lucide',$r['icon']??'monitor-smartphone');
    echo zp_suite_katowice_field($p.'[title]','Nazwa projektu',$r['title']??'');
    echo zp_suite_katowice_field($p.'[accent]','Kolor akcentu',$r['accent']??'#99b7df','color');
    echo '</div>';
    echo zp_suite_katowice_field($p.'[label]','Label branży',$r['label']??'');
    echo zp_suite_katowice_field($p.'[strong]','Zakres bold',$r['strong']??'');
    echo zp_suite_katowice_field($p.'[desc]','Opis projektu',$r['desc']??'','textarea');
    echo zp_suite_katowice_field($p.'[url]','Link strony',$r['url']??'','url');
    echo '<div class="zpMediaRow">'.zp_suite_katowice_field($p.'[img]','Zdjęcie/mockup URL',$r['img']??'','url').'<button type="button" class="button zpMediaPick">Wybierz zdjęcie</button></div>';
    echo zp_suite_katowice_field($p.'[alt]','ALT obrazka',$r['alt']??'','input','Krótko: co jest na mockupie + nazwa projektu/usługi.');
    echo zp_suite_katowice_field($p.'[bg]','Tło CSS karty',$r['bg']??'','textarea');
    echo zp_suite_katowice_field($p.'[badges]','Badge po przecinku',$r['badges']??'');
    echo zp_suite_katowice_field($p.'[review]','Opis w opinii / zakresie',$r['review']??'','textarea');
  }
  if($type==='pakiety'){
    echo '<div class="zpFieldGrid">';
    echo zp_suite_katowice_field($p.'[slug]','Slug pakietu',$r['slug']??'');
    echo zp_suite_katowice_field($p.'[icon]','Ikona Lucide',$r['icon']??'layout-template');
    echo zp_suite_katowice_field($p.'[tag]','Tag',$r['tag']??'');
    echo zp_suite_katowice_field($p.'[title]','Nazwa',$r['title']??'');
    echo '</div>';
    echo zp_suite_katowice_field($p.'[desc]','Opis',$r['desc']??'','textarea');
    echo zp_suite_katowice_field($p.'[best]','Najlepszy gdy...',$r['best']??'');
    echo zp_suite_katowice_field($p.'[items]','Lista elementów',$r['items']??'','textarea','Rozdzielaj elementy znakiem |');
    echo zp_suite_katowice_field($p.'[url]','Link CTA',$r['url']??'','url');
  }

  if($type==='process'){
    echo '<div class="zpFieldGrid">';
    echo zp_suite_katowice_field($p.'[icon]','Ikona Lucide',$r['icon']??'layers-3');
    echo zp_suite_katowice_field($p.'[label]','Mały label',$r['label']??'');
    echo zp_suite_katowice_field($p.'[title]','Tytuł kroku',$r['title']??'');
    echo '</div>';
    echo zp_suite_katowice_field($p.'[text]','Opis kroku',$r['text']??'','textarea');
  }
  if($type==='industries'){
    echo '<div class="zpFieldGrid">';
    echo zp_suite_katowice_field($p.'[icon]','Ikona Lucide',$r['icon']??'layout-template');
    echo zp_suite_katowice_field($p.'[label]','Label branży',$r['label']??'');
    echo zp_suite_katowice_field($p.'[title]','Tytuł karty',$r['title']??'');
    echo zp_suite_katowice_field($p.'[url]','Adres landing page',$r['url']??'','url');
    echo '</div>';
    echo zp_suite_katowice_field($p.'[short]','Krótki opis na karcie',$r['short']??'');
    echo zp_suite_katowice_field($p.'[text]','Opis po rozwinięciu',$r['text']??'','textarea');
    echo zp_suite_katowice_field($p.'[items]','Punkty listy',$r['items']??'','textarea','Rozdzielaj elementy znakiem |');
    echo '<div class="zpMediaRow">'.zp_suite_katowice_field($p.'[img]','Zdjęcie URL',$r['img']??'','url').'<button type="button" class="button zpMediaPick">Wybierz zdjęcie</button></div>';
    echo zp_suite_katowice_field($p.'[alt]','ALT zdjęcia',$r['alt']??'');
  }
  if($type==='page'){
    echo '<div class="zpFieldGrid">';
    echo zp_suite_katowice_field($p.'[hero_kicker]','Hero kicker',$r['hero_kicker']??'');
    echo zp_suite_katowice_field($p.'[hero_h1]','H1',$r['hero_h1']??'');
    echo '</div>';
    echo zp_suite_katowice_field($p.'[hero_lead]','Hero lead',$r['hero_lead']??'','textarea');
    echo '<div class="zpFieldGrid">';
    echo zp_suite_katowice_field($p.'[hero_cta_1]','CTA główne tekst',$r['hero_cta_1']??'');
    echo zp_suite_katowice_field($p.'[hero_cta_1_url]','CTA główne URL',$r['hero_cta_1_url']??'','url');
    echo zp_suite_katowice_field($p.'[hero_cta_2]','CTA drugie tekst',$r['hero_cta_2']??'');
    echo zp_suite_katowice_field($p.'[hero_cta_2_url]','CTA drugie URL',$r['hero_cta_2_url']??'','url');
    echo '</div><div class="zpSubCard"><h4>Mobile hero video</h4><div class="zpFieldGrid">';
    echo zp_suite_katowice_field($p.'[hero_mobile_video_dim]','Mobile: przyciemnienie video 0–100',$r['hero_mobile_video_dim']??'10','','10 = bardzo widoczne, 60 = mocno przyciemnione');
    echo zp_suite_katowice_field($p.'[hero_mobile_video_navy]','Mobile: navy maska 0–100',$r['hero_mobile_video_navy']??'48','','48 = mocny navy klimat, ale video zostaje widoczne');
    echo zp_suite_katowice_field($p.'[hero_mobile_video_opacity]','Mobile: opacity video 0–100',$r['hero_mobile_video_opacity']??'100','','100 = pełna widoczność samego video');
    echo zp_suite_katowice_field($p.'[hero_mobile_height]','Mobile: wysokość hero px',$r['hero_mobile_height']??'620','','np. 580–680');
    echo '</div></div><h3>Nagłówki i leady sekcji</h3>';
    foreach([
      ['portfolio','Portfolio'],['trust','Trust / opinie'],['process','Proces'],['logos','Logotypy klientów'],['packages','Pakiety'],['industries','Branże'],['faq','FAQ']
    ] as $sec){
      [$prefix,$label] = $sec;
      echo '<div class="zpSubCard"><h4>'.esc_html($label).'</h4><div class="zpFieldGrid">';
      echo zp_suite_katowice_field($p.'['.$prefix.'_kicker]','Kicker',$r[$prefix.'_kicker']??'');
      echo zp_suite_katowice_field($p.'['.$prefix.'_title]','Tytuł H2',$r[$prefix.'_title']??'');
      echo '</div>';
      if($prefix !== 'process') echo zp_suite_katowice_field($p.'['.$prefix.'_lead]','Lead sekcji',$r[$prefix.'_lead']??'','textarea');
      if($prefix === 'process'){
        echo '<div class="zpFieldGrid">';
        echo zp_suite_katowice_field($p.'[process_top_label]','Topbar label',$r['process_top_label']??'');
        echo zp_suite_katowice_field($p.'[process_top_title]','Topbar title',$r['process_top_title']??'');
        echo '</div>';
      }
      echo '</div>';
    }
  }

  echo '</div></details>';
}

add_action('admin_enqueue_scripts', function($hook){
  if (strpos((string)$hook, 'zp-suite') === false) return;
  wp_enqueue_media();
  wp_enqueue_script('jquery-ui-sortable');
  wp_add_inline_script('jquery', <<<'JS'
(function($){
  function renumber($rep,type){$rep.children('.zpRepeatItem').each(function(i){$(this).find('[name]').each(function(){this.name=this.name.replace(new RegExp('zp_'+type+'\\[[^\\]]+\\]'),'zp_'+type+'['+i+']');});});}
  $(document).on('click','.zpKatSide [data-tab]',function(){var t=$(this).data('tab');$(this).addClass('is-active').siblings('[data-tab]').removeClass('is-active');$('.zpPanel').removeClass('is-active');$('.zpPanel[data-panel="'+t+'"]').addClass('is-active');});
  $('.zpRepeater').each(function(){var $r=$(this),type=$r.data('repeater');$r.sortable({handle:'.zpDragHandle',placeholder:'ui-sortable-placeholder',update:function(){renumber($r,type);}});});
  $(document).on('click','[data-add]',function(){var type=$(this).data('add'),$r=$('[data-repeater="'+type+'"]'),tpl=$('#tpl_'+type).html();if(!tpl)return;tpl=tpl.replace(/__i__/g,$r.children().length);$r.append(tpl);renumber($r,type);});
  $(document).on('click','[data-remove]',function(e){e.preventDefault();var $r=$(this).closest('.zpRepeater'),type=$r.data('repeater');$(this).closest('.zpRepeatItem').remove();renumber($r,type);});
  $(document).on('click','.zpMediaPick',function(e){e.preventDefault();var $input=$(this).closest('.zpMediaRow').find('input');var frame=wp.media({title:'Wybierz obraz',button:{text:'Użyj obrazu'},multiple:false});frame.on('select',function(){var a=frame.state().get('selection').first().toJSON();$input.val(a.url).trigger('change');});frame.open();});
  $(document).on('input','.zpRepeatItem input[name$="[title]"], .zpRepeatItem input[name$="[q]"], .zpRepeatItem input[name$="[name]"]',function(){var $d=$(this).closest('.zpRepeatItem');$d.find('summary strong').first().text(this.value||'Nowy element');});
})(jQuery);
JS
  );
  wp_add_inline_style('common', '.zpKatAdmin{max-width:1600px}.zpKatHeroActions{display:flex;gap:10px;flex-wrap:wrap;margin-top:18px}.zpKatLayout{display:grid;grid-template-columns:260px minmax(0,1fr);gap:18px;margin-top:20px}.zpKatSide{position:sticky;top:48px;align-self:start;background:#fff;border:1px solid #dfe5ee;border-radius:24px;padding:10px;box-shadow:0 16px 48px rgba(7,20,38,.055)}.zpKatSide button:not(.button){width:100%;display:flex;justify-content:space-between;align-items:center;border:0;background:transparent;border-radius:16px;padding:13px 14px;text-align:left;font-weight:800;color:#071426;cursor:pointer}.zpKatSide button.is-active{background:linear-gradient(135deg,#071426,#102a4f);color:#fff}.zpKatSide em{font-style:normal;opacity:.65}.zpKatSave{width:100%;margin-top:10px!important;border-radius:999px!important}.zpPanel{display:none}.zpPanel.is-active{display:block}.zpKatMain .zpCard{background:#fff;border:1px solid #dfe5ee;border-radius:28px;padding:22px;box-shadow:0 16px 48px rgba(7,20,38,.055)}.zpRepeatItem{margin:0 0 12px;border:1px solid #dfe5ee;border-radius:18px;background:#fff;overflow:hidden}.zpRepeatItem>summary{display:flex;align-items:center;gap:12px;list-style:none;cursor:pointer;padding:14px 16px;background:#f8fafc}.zpRepeatItem>summary::-webkit-details-marker{display:none}.zpRepeatItem>summary strong{font-size:14px}.zpRepeatItem .button-link-delete{margin-left:auto;color:#b42318}.zpDragHandle{cursor:grab;color:#102a4f;font-weight:900}.zpRepeatFields{padding:16px;display:grid;gap:12px}.zpField{display:grid;gap:6px}.zpField strong{font-size:12px;text-transform:uppercase;letter-spacing:.08em;color:#475569}.zpField small{color:#64748b}.zpField input,.zpField textarea{width:100%;border-radius:12px;border:1px solid #d8e0eb;padding:10px 12px}.zpField textarea{min-height:92px}.zpCodeArea{width:100%;min-height:300px;font-family:ui-monospace,SFMono-Regular,Menlo,monospace;font-size:12px;border-radius:14px}.zpFieldGrid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}.zpSubCard{border:1px solid #e2e8f0;border-radius:18px;padding:14px;background:#f8fafc;margin-top:12px}.zpSubCard h4{margin:0 0 12px}.zpMediaRow{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:10px;align-items:end}.zpKatSeoGrid,.zpKatPageGrid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px}.zpKatSeoGrid>div,.zpKatPageCard{background:#f8fafc;border:1px solid #e2e8f0;border-radius:18px;padding:16px}.zpKatCheck{margin:12px 0 0 20px}.zpKatCheck li{margin:7px 0;color:#334155}.zpKatPageCard h2{margin:6px 0 8px}.zpKatPageCard strong{display:inline-flex;border-radius:999px;background:#eaf2ff;color:#102a4f;padding:4px 8px;font-size:10px;letter-spacing:.1em;text-transform:uppercase}.zpKatPageCard.is-ready strong{background:#071426;color:#fff}.ui-sortable-placeholder{visibility:visible!important;min-height:96px;border:2px dashed #1c477a!important;border-radius:18px;background:#edf6ff!important}@media(max-width:1000px){.zpKatLayout{grid-template-columns:1fr}.zpKatSide{position:relative;top:0}.zpFieldGrid,.zpKatSeoGrid,.zpKatPageGrid{grid-template-columns:1fr}.zpMediaRow{grid-template-columns:1fr}}');
});
