<?php
if (!defined('ABSPATH')) { exit; }

/**
 * ZP Suite 2.7.1 — FAQ page (/najczesciej-zadawane-pytania/).
 *
 * A few answers were written for the old May SEO plan, not for clients: "Jakie frazy są ważne dla
 * Zaprojektowani.com?", "Jakie podstrony SEO są priorytetowe…", "W majowym planie SEO…", local phrases
 * pasted into sentences ("Czy tworzycie sklepy internetowe Katowice?") and anchors "strony internetowe
 * Katowice" on links that now lead to the nationwide pages. The price questions gave no price.
 * Those answers are replaced when the page is shown: with client-facing ones, the prices Mat confirmed,
 * the answer time of Studio Wyceny and links to the service pages, the cost guides and the price list;
 * the two category headings lose "Katowice". The FAQPage schema reads the same data, so it matches.
 *
 * Only answers that still have the plugin's default wording change: an answer edited in the FAQ editor
 * (Zaprojektowani Suite → FAQ) is kept as written. The editor itself shows the stored text. Polish version
 * only (the English pages are translated from the Polish HTML with a dictionary).
 */

function zp_seo_faq_on(): bool {
  return zp_seo_plan_active() && !zp_seo_plan_is_en() && !is_admin();
}

function zp_seo_faq_norm(string $html): string {
  $text = html_entity_decode(wp_strip_all_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
  return trim((string) preg_replace('~\s+~u', ' ', str_replace("\xC2\xA0", ' ', $text)));
}

/** New category headings by slug: [old default title, new title, new name (or '')]. */
function zp_seo_faq_category_titles(): array {
  return [
    'strony' => ['Strony internetowe Katowice i projekty WWW', 'Strony internetowe i projekty WWW', ''],
    'sklepy' => ['Sklepy internetowe Katowice i WooCommerce', 'Sklepy internetowe i WooCommerce', 'Sklepy internetowe i WooCommerce'],
  ];
}

/** Index of the default item (faq-cms.php) => [new question or '' to keep it, new answer HTML]. */
function zp_seo_faq_rewrites(): array {
  $L = 'zp_seo_plan_live_link';
  $local = 'data-zp-local="1"';
  $b = static function (string $slug, string $text) use ($L): string { return $L('/strony-internetowe-dla-' . $slug . '/', $text); };
  $fb_ads = '/meta-ads/ile-kosztuje-reklama-na-facebooku/';
  return [
    0 => ['', '<p>Zaprojektowani.com to studio kreatywne z Katowic, które od 2022 roku projektuje strony internetowe, sklepy WooCommerce, logo, identyfikacje wizualne i kampanie reklamowe dla firm z całej Polski. Pomagamy firmom wyglądać profesjonalnie w internecie i budować ścieżkę od pierwszego kontaktu do sprzedaży. Nasz zespół poznasz na stronie ' . $L('/o-nas/', 'O nas') . '.</p>'],
    1 => ['', '<p>Tak. Nasze studio działa w Katowicach, więc obsługujemy firmy z Katowic i całego Śląska, a lokalną ofertę opisujemy na stronie ' . $L('/strony-internetowe-katowice/', 'strony internetowe Katowice', '', $local) . '. Pracujemy też zdalnie z klientami z całej Polski: zobacz, jak wygląda ' . $L('/tworzenie-stron-internetowych/', 'tworzenie stron internetowych') . ' krok po kroku.</p>'],
    2 => ['', '<p>Strony internetowe projektujemy od 3&nbsp;999&nbsp;zł, zwykle w 2–4 tygodnie. Ostateczna cena zależy od zakresu: liczby podstron, indywidualnego projektu, treści, formularzy, animacji, SEO i integracji. Widełki cen opisujemy w poradniku ' . $L('/strony-www/ile-kosztuje-stworzenie-strony-internetowej-dla-firmy/', 'ile kosztuje strona internetowa') . ', a cały cennik i bezpłatną wycenę znajdziesz w ' . $L('/studio-wyceny/', 'Studio Wyceny') . '.</p>'],
    6 => ['', '<p>Najczęściej warto zaplanować stronę główną, ofertę, realizacje, kontakt, FAQ oraz osobną podstronę dla każdej ważnej usługi. Dzięki temu klient od razu trafia na odpowiedź, a Google wie, czego dotyczy każda podstrona. U nas tak działają np. strony ' . $L('/tworzenie-stron-internetowych/', 'tworzenie stron internetowych') . ' i ' . $L('/tworzenie-sklepow-internetowych/', 'tworzenie sklepów internetowych') . '.</p>'],
    7 => ['', '<p>Tak. Osobny landing page lepiej odpowiada na konkretną potrzebę klienta i na konkretne zapytanie w Google albo reklamę. Inaczej buduje się stronę pod kampanię jednej usługi, a inaczej stronę dla kancelarii, gabinetu lekarskiego czy dewelopera.'
      . (zp_seo_plan_path_is_live('/tworzenie-landing-page/') ? ' Jak to robimy, opisujemy na stronie ' . $L('/tworzenie-landing-page/', 'tworzenie landing page') . '.' : '') . '</p>'],
    8 => ['', '<p>Tak. Projektujemy strony dla ' . $b('kancelarii', 'kancelarii') . ', ' . $b('lekarzy', 'gabinetów lekarskich') . ', ' . $b('deweloperow', 'deweloperów') . ', ' . $b('salonow-beauty', 'salonów beauty') . ', ' . $b('trenerow-personalnych', 'trenerów personalnych') . ', ' . $b('fotografow', 'fotografów') . ', ' . $b('restauracji', 'restauracji') . ', ' . $b('hoteli', 'hoteli') . ', producentów, firm usługowych i B2B. Strona branżowa lepiej odpowiada na pytania klientów, bo mówi ich językiem.</p>'],
    12 => ['Czy tworzycie sklepy internetowe?', '<p>Tak. Projektujemy i wdrażamy sklepy WooCommerce dla firm z całej Polski: strukturę kategorii, karty produktów, koszyk, płatności, dostawy i elementy wspierające sprzedaż. Sklep internetowy kosztuje u nas od 6&nbsp;499&nbsp;zł. Więcej piszemy na stronie ' . $L('/tworzenie-sklepow-internetowych/', 'tworzenie sklepów internetowych') . ', a firmom z regionu polecamy też stronę ' . $L('/sklepy-internetowe-katowice/', 'sklepy internetowe Katowice', '', $local) . '.</p>'],
    26 => ['Czy projekt logo to osobna usługa?', '<p>Tak. Logo możesz zamówić osobno albo jako część większego pakietu z identyfikacją wizualną. Pakiety mają stałe ceny: Logo Startowe 999&nbsp;zł, Mini Branding 1&nbsp;499&nbsp;zł i Branding Premium 2&nbsp;999&nbsp;zł. Proces opisujemy na stronie ' . $L('/projektowanie-logo/', 'projektowanie logo') . ', a szerszy system marki na stronie ' . $L('/identyfikacja-wizualna/', 'identyfikacja wizualna') . '.</p>'],
    32 => ['Jak dobieracie frazy kluczowe dla strony?', '<p>Zaczynamy od tego, jak klienci naprawdę szukają Twojej usługi: sprawdzamy popularność fraz, konkurencję w wynikach Google i dane z Search Console, jeśli strona już działa. Każdej ważnej podstronie przypisujemy jedną główną frazę, żeby podstrony nie konkurowały ze sobą, a nagłówki, treść i linki wewnętrzne układamy wokół niej.</p>'],
    35 => ['', '<p>Ogólne frazy, takie jak „strony internetowe”, mają ogromną konkurencję, a wpisujący je ludzie szukają bardzo różnych rzeczy. Bardziej konkretne zapytania, np. o usługę dla danej branży, w danym mieście albo o cenę, przyprowadzają mniej osób, ale częściej takie, które chcą zamówić projekt. Dlatego dobra strategia łączy frazy ogólne z usługowymi, branżowymi i lokalnymi.</p>'],
    36 => ['Które podstrony warto pozycjonować w pierwszej kolejności?', '<p>Najpierw te, które zarabiają: podstrony głównych usług i produktów, bo trafiają na nie osoby gotowe do zakupu. Potem podstrony branżowe i lokalne, a na końcu poradniki, które odpowiadają na pytania klientów i prowadzą do usług.'
      . (zp_seo_plan_path_is_live('/wiedza/') ? ' Przykłady takich poradników znajdziesz w naszej ' . $L('/wiedza/', 'bazie wiedzy') . '.' : '') . '</p>'],
    40 => ['Czy lokalne SEO ma sens, jeśli firma działa w całej Polsce?', '<p>Tak, jeśli firma ma siedzibę albo oddziały w konkretnych miastach. Lokalne podstrony i wizytówka w Google pomagają zdobywać klientów z okolicy, a strony usług dla całego kraju trafiają do pozostałych. Ważne, żeby nie upychać nazw miast na siłę, tylko tworzyć lokalne podstrony tam, gdzie firma naprawdę działa.</p>'],
    49 => ['Ile kosztuje prowadzenie kampanii reklamowych?', '<p>Stała obsługa kampanii Meta Ads lub Google Ads kosztuje u nas od 1&nbsp;200&nbsp;zł miesięcznie. W tej kwocie są strategia, kreacje, konfiguracja pomiaru i bieżąca optymalizacja. Budżet mediowy, czyli to, co płacisz bezpośrednio Meta albo Google, ustalamy osobno, bo zależy od celu, branży i konkurencji. Więcej na stronie ' . $L('/kampanie-reklamowe/', 'kampanie reklamowe') . '.'
      . (zp_seo_plan_path_is_live($fb_ads) ? ' Ile może kosztować sama reklama, opisujemy w poradniku ' . $L($fb_ads, 'ile kosztuje reklama na Facebooku') . '.' : '') . '</p>'],
    51 => ['', '<p>Studio Wyceny to bezpłatny konfigurator na naszej stronie: wybierasz usługę (stronę, sklep, logo albo kampanię), zaznaczasz zakres i materiały, a my wracamy z wyceną i rekomendowanym zakresem prac. Znajdziesz tam też ' . $L('/studio-wyceny/', 'cennik stron internetowych, sklepów, logo i kampanii') . '. Pakiety logo mają stałe ceny, więc możesz je zamówić od razu.</p>'],
    54 => ['', '<p>Nie. Studio mamy w Katowicach, ale współpracujemy z klientami z całej Polski. Wiele etapów projektu można przeprowadzić zdalnie: brief, konsultacje, projekt, wdrożenie, poprawki i dalszą obsługę.</p>'],
    57 => ['', '<p>W ciągu 48–72 godzin od wysłania briefu w ' . $L('/studio-wyceny/', 'Studio Wyceny') . '. Im dokładniejszy opis, tym szybciej przygotujemy sensowny zakres. Najbardziej pomaga informacja o typie projektu, liczbie podstron, funkcjach, materiałach, terminie, budżecie i przykładach stylu.</p>'],
  ];
}

/** The stored FAQ (option zp_suite_faq_cms) with the default answers above replaced. */
function zp_seo_faq_refresh($data) {
  if (!is_array($data) || !zp_seo_faq_on() || !function_exists('zp_suite_faq_default_items')) { return $data; }
  static $memo = [];
  $key = md5(serialize($data));
  if (isset($memo[$key])) { return $memo[$key]; }
  $defaults = zp_suite_faq_default_items();
  $by_question = [];
  foreach ($defaults as $i => $item) { $by_question[zp_seo_faq_norm((string) ($item['q'] ?? ''))] = $i; }
  $rewrites = null;
  foreach ((array) ($data['items'] ?? []) as $k => $item) {
    if (!is_array($item)) { continue; }
    $i = $by_question[zp_seo_faq_norm((string) ($item['q'] ?? ''))] ?? null;
    if ($i === null || zp_seo_faq_norm((string) ($item['a'] ?? '')) !== zp_seo_faq_norm((string) ($defaults[$i]['a'] ?? ''))) { continue; }
    if ($rewrites === null) { $rewrites = zp_seo_faq_rewrites(); }
    if (!isset($rewrites[$i])) { continue; }
    if ($rewrites[$i][0] !== '') { $data['items'][$k]['q'] = $rewrites[$i][0]; }
    // A real no-break space: the FAQPage schema strips tags but keeps entities, so "&nbsp;" would show in it.
    $data['items'][$k]['a'] = str_replace('&nbsp;', "\u{00A0}", $rewrites[$i][1]);
  }
  $titles = zp_seo_faq_category_titles();
  foreach ((array) ($data['categories'] ?? []) as $k => $cat) {
    $slug = is_array($cat) ? (string) ($cat['slug'] ?? '') : '';
    if (!isset($titles[$slug])) { continue; }
    [$old, $title, $name] = $titles[$slug];
    if (zp_seo_faq_norm((string) ($cat['title'] ?? '')) === $old) { $data['categories'][$k]['title'] = $title; }
    if ($name !== '' && zp_seo_faq_norm((string) ($cat['name'] ?? '')) === $old) { $data['categories'][$k]['name'] = $name; }
  }
  return $memo[$key] = $data;
}
add_filter('option_zp_suite_faq_cms', 'zp_seo_faq_refresh', 20);

/**
 * Title and description: the plan writes them into Rank Math (data/plan.php). faq-cms.php sets its own
 * from code; it now steps back when the page has a Rank Math value, so the value can be edited there.
 */
function zp_seo_faq_rank_math_owns(string $meta_key): bool {
  if (!zp_seo_plan_active() || zp_seo_plan_is_en()) { return false; }
  $id = (int) get_queried_object_id();
  return $id > 0 && trim((string) get_post_meta($id, $meta_key, true)) !== '';
}
