<?php
if (!defined('ABSPATH')) { exit; }

/**
 * ZP Suite 2.7.1 — Studio wyceny as the price list page (keyword plan: "wycena strony internetowej",
 * "strona internetowa cennik", "strony internetowe cennik").
 *
 * What a visitor sees first is the chooser, a separate document in an iframe, and the contact form, so the
 * page itself had almost no text about prices and its H1 sat in the hidden brief. Now:
 *  - below the contact form there is a price list in the layout of the "Poradnik" sections on the service
 *    pages (guide.php): the prices Mat confirmed, what they include, what the quote depends on, how the
 *    quote works and questions about prices, with links to the cost guides and the service pages; it hides
 *    while the brief is open, like the contact form,
 *  - those questions go into the schema as FAQPage,
 *  - the H1 keeps the plan's wording until the visitor opens the brief (the brief still names the service),
 *  - the chooser gets the project count from the settings (?n=); its own file is noindex, indexifembedded,
 *    because Search Console listed chooser-v19.html?v=… as a separate page.
 * Polish version only: the English pages are translated from the Polish HTML with a dictionary.
 */

function zp_seo_studio_on(): bool {
  return zp_seo_plan_active() && !zp_seo_plan_is_en() && !is_admin();
}

function zp_seo_studio_is_page(): bool {
  if (!function_exists('is_page') || !did_action('wp')) { return false; }
  if (is_page('studio-wyceny')) { return true; }
  if (!is_singular()) { return false; }
  $content = (string) get_post_field('post_content', get_queried_object_id());
  return $content !== '' && (has_shortcode($content, 'zp_studio_wyceny') || has_shortcode($content, 'zp_studio_wyceny_cms'));
}

/**
 * zp_seo_plan_link_is_live() that also knows pages served by their own route: url_to_postid() gives 0 for
 * /kampanie-reklamowe/ (rewrite rule zp_campaigns_page), so its links would turn into plain text.
 */
function zp_seo_plan_path_is_live(string $path): bool {
  if (in_array($path, ['/kampanie-reklamowe/', '/studio-wyceny/', '/o-nas/', '/kontakt/', '/realizacje/', '/wiedza/'], true)) { return true; }
  if (zp_seo_plan_link_is_live($path)) { return true; }
  $page = get_page_by_path(trim($path, '/'));
  return $page instanceof WP_Post && $page->post_status === 'publish';
}

/**
 * A link to a page of the site when it is published, else to the fallback page, else plain text
 * (also used by faq.php and about.php). $attrs: extra attributes, e.g. data-zp-local="1".
 */
function zp_seo_plan_live_link(string $path, string $text, string $fallback = '', string $attrs = ''): string {
  $to = zp_seo_plan_path_is_live($path) ? $path : (($fallback !== '' && zp_seo_plan_path_is_live($fallback)) ? $fallback : '');
  return $to === '' ? $text : '<a ' . ($attrs !== '' ? $attrs . ' ' : '') . 'href="' . esc_url(home_url($to)) . '">' . $text . '</a>';
}

/** Questions about prices: shown in the last card and sent as FAQPage. */
function zp_seo_studio_faq(): array {
  return [
    ['Czy wycena jest bezpłatna?', 'Tak. Wycena strony internetowej, sklepu albo kampanii jest bezpłatna i nie zobowiązuje do zamówienia. Pakiety logo mają stałe ceny, więc w ich przypadku od razu wysyłasz zgłoszenie zamówienia, a my potwierdzamy szczegóły.'],
    ['Jak szybko dostanę wycenę?', 'W ciągu 48–72 godzin od wysłania briefu. Jeśli zostawisz numer telefonu, możemy też oddzwonić i doprecyzować zakres w rozmowie.'],
    ['Dlaczego ceny w cenniku są podane „od”?', 'Bo strona wizytówka i serwis z kilkudziesięcioma podstronami to zupełnie inna ilość pracy. Cena „od” to najmniejszy kompletny projekt danego typu, a końcową kwotę ustalamy na podstawie zakresu z briefu, zanim zaczniemy pracę.'],
    ['Czy cena strony obejmuje domenę i hosting?', 'Nie. Domena i hosting to koszty stałe, które opłacasz osobno, najlepiej na dane swojej firmy, żeby mieć do nich pełny dostęp. Pomożemy je wybrać i skonfigurować, a stronę publikujemy na Twojej domenie i przekazujemy z pełnymi dostępami.'],
    ['Czy mogę zamówić stronę, logo i kampanię w jednym projekcie?', 'Tak. Przygotujemy wtedy jedną wycenę i kolejność prac, żeby logo, strona i reklamy powstały w jednym stylu. Wybierz w Studio Wyceny główną usługę i dopisz resztę w opisie albo napisz do nas przez formularz kontaktowy.'],
  ];
}

/** The guide body: one H2 per card (guide.php turns them into the cards and the table of contents). */
function zp_seo_studio_guide_body(): string {
  $L = 'zp_seo_plan_live_link';
  $faq = '';
  foreach (zp_seo_studio_faq() as $qa) {
    $faq .= '<h3>' . esc_html($qa[0]) . '</h3><p>' . esc_html($qa[1]) . '</p>';
  }
  return
    '<h2 id="cennik-uslug">Cennik: strona internetowa, sklep, logo i kampanie</h2>'
    . '<p>Tyle kosztuje u nas najmniejszy kompletny projekt danego typu. W wycenie rozpisujemy każdą kwotę na konkretny zakres prac.</p>'
    . '<ul>'
    . '<li><strong>Strona internetowa od 3&nbsp;999&nbsp;zł</strong> Landing page, strona firmowa albo rozbudowany serwis na WordPressie: projekt UX/UI, wersja mobilna i podstawy SEO. Zwykle 2–4 tygodnie.</li>'
    . '<li><strong>Sklep internetowy od 6&nbsp;499&nbsp;zł</strong> Sklep WooCommerce z produktami, koszykiem, płatnościami i dostawami, gotowy do pierwszych zamówień. Zwykle 3–6 tygodni.</li>'
    . '<li><strong>Logo od 999&nbsp;zł</strong> Stałe pakiety: Logo Startowe 999&nbsp;zł, Mini Branding 1&nbsp;499&nbsp;zł i Branding Premium 2&nbsp;999&nbsp;zł. Zwykle 7–14 dni.</li>'
    . '<li><strong>Kampanie od 1&nbsp;200&nbsp;zł/mies.</strong> Stała obsługa Meta Ads i Google Ads: strategia, kreacje, pomiar konwersji i optymalizacja. Budżet mediowy ustalamy osobno. Start w 7 dni.</li>'
    . '</ul>'
    . '<p>Więcej o każdej usłudze: ' . $L('/tworzenie-stron-internetowych/', 'tworzenie stron internetowych') . ', '
    . $L('/tworzenie-sklepow-internetowych/', 'tworzenie sklepów internetowych') . ', ' . $L('/projektowanie-logo/', 'projektowanie logo')
    . ' i ' . $L('/kampanie-reklamowe/', 'kampanie reklamowe') . '.</p>'

    . '<h2 id="co-obejmuje-cena">Co obejmuje cena strony i sklepu?</h2>'
    . '<p>W cenie strony internetowej albo sklepu jest cały proces, a nie sam projekt graficzny:</p>'
    . '<ul>'
    . '<li>indywidualny projekt UX/UI pod Twoją ofertę i markę,</li>'
    . '<li>wdrożenie na WordPressie lub WooCommerce z możliwością samodzielnej edycji treści,</li>'
    . '<li>wersja mobilna dopracowana tak samo jak wersja na komputer,</li>'
    . '<li>podstawy SEO technicznego: nagłówki, adresy podstron, szybkość i mapa witryny,</li>'
    . '<li>formularze, analityka i publikacja na Twojej domenie z pełnymi dostępami.</li>'
    . '</ul>'
    . '<p>Poza ceną projektu zostają domena, hosting i ewentualna ' . $L('/opieka-wordpress/', 'opieka nad stroną WordPress')
    . '. Ile to kosztuje w skali roku, liczymy w poradniku '
    . $L('/seo-i-konwersja/ile-kosztuje-utrzymanie-strony-internetowej/', 'ile kosztuje utrzymanie strony internetowej') . '.</p>'

    . '<h2 id="od-czego-zalezy-wycena">Od czego zależy wycena strony internetowej?</h2>'
    . '<p>Na ostateczną wycenę strony internetowej wpływają przede wszystkim:</p>'
    . '<ul>'
    . '<li>rodzaj strony: ' . $L('/strona-wizytowka/', 'strona wizytówka') . ', ' . $L('/tworzenie-landing-page/', 'landing page') . ', strona firmowa czy rozbudowany serwis,</li>'
    . '<li>liczba podstron i to, kto przygotowuje teksty, zdjęcia i grafiki,</li>'
    . '<li>funkcje: formularze, rezerwacje, blog, wersje językowe i integracje z innymi systemami,</li>'
    . '<li>zakres SEO i analityki, np. treści pod konkretne frazy i pomiar konwersji,</li>'
    . '<li>termin, jeśli strona ma wystartować szybciej niż zwykle.</li>'
    . '</ul>'
    . '<p>Przy sklepie dochodzą liczba produktów i wariantów, płatności, dostawy i integracje, np. z BaseLinkerem albo systemem ERP. '
    . 'Wycena logo jest prostsza: cenę wyznacza pakiet, czyli liczba koncepcji, rund poprawek i materiałów firmowych. '
    . 'Widełki cen z przykładami opisujemy w poradnikach '
    . $L('/strony-www/ile-kosztuje-stworzenie-strony-internetowej-dla-firmy/', 'ile kosztuje strona internetowa') . ', '
    . $L('/seo-i-konwersja/ile-kosztuje-sklep-internetowy-woocommerce-w-2026-roku/', 'ile kosztuje sklep internetowy') . ' i '
    . $L('/logo-branding/ile-kosztuje-logo-dla-firmy-i-co-obejmuje-cena/', 'ile kosztuje logo') . '.</p>'

    . '<h2 id="jak-wyglada-wycena">Jak wygląda wycena w Studio Wyceny?</h2>'
    . '<ol>'
    . '<li>Wybierasz usługę na górze tej strony: stronę internetową, sklep, logo albo kampanię.</li>'
    . '<li>Zaznaczasz pakiet, potrzebne funkcje i materiały, a na końcu zostawiasz kontakt. Zajmuje to około 2 minut.</li>'
    . '<li>Analizujemy brief i wracamy z wyceną oraz rekomendowanym zakresem prac w ciągu 48–72 godzin.</li>'
    . '<li>Jeśli coś wymaga doprecyzowania, dzwonimy albo piszemy. Wycena jest bezpłatna i nie zobowiązuje do zamówienia.</li>'
    . '</ol>'
    . '<div class="zpGuide__noteBox"><p>Studio Wyceny działa trochę jak kalkulator strony internetowej, ale kwotę liczy człowiek, który zna Twój zakres. '
    . 'Pakiety logo i brandingu mają stałe ceny, więc tam od razu widzisz sumę i wysyłasz zamówienie.</p></div>'

    . '<h2 id="pytania-o-cennik">Pytania o cennik i wycenę</h2>'
    . $faq;
}

/** The whole section, with its stylesheet (printed with it, like the other blocks of this page). */
function zp_seo_studio_section(): string {
  $body = zp_seo_guide_body(zp_seo_studio_guide_body(), '/studio-wyceny/');
  // The band's second button would point back at this page: it goes up to the chooser instead.
  $body = str_replace('href="' . esc_url(home_url('/studio-wyceny/')) . '">Studio Wyceny</a>', 'href="#zpStudioV19FrameShell">Wybierz usługę</a>', $body);
  $css = '#zpStudioSeo{position:relative;width:100vw;max-width:100vw;margin-left:calc(50% - 50vw);margin-right:calc(50% - 50vw);padding:clamp(70px,7vw,120px) 0;background:#fff;color:#07111f;font-family:var(--zp-font,"Plus Jakarta Sans Local","Plus Jakarta Sans",system-ui,sans-serif);-webkit-font-smoothing:antialiased;overflow:clip}'
    . '#zpStudioSeo *,#zpStudioSeo *::before,#zpStudioSeo *::after{box-sizing:border-box}'
    . '#zpStudioSeo .zpStudioSeo__inner{width:min(1740px,calc(100% - clamp(44px,6vw,112px)));margin:0 auto}'
    . '#zpStudioSeo .zpStudioSeo__head{display:grid;grid-template-columns:minmax(0,1fr) minmax(310px,470px);gap:clamp(26px,5vw,86px);align-items:end;margin:0 0 clamp(36px,4vw,58px)}'
    . '#zpStudioSeo .zpStudioSeo__kicker{display:inline-flex;align-items:center;gap:11px;color:#54718f;font-size:10px;line-height:1;font-weight:800;letter-spacing:.14em;text-transform:uppercase}'
    . '#zpStudioSeo .zpStudioSeo__kicker::before{content:"";width:30px;height:1px;background:linear-gradient(90deg,#102a4f,#8ec8f7)}'
    . '#zpStudioSeo .zpStudioSeo__title{max-width:1040px;margin:16px 0 0;color:#07111f;font-size:clamp(38px,4.6vw,68px);font-weight:500;line-height:.98;letter-spacing:-.052em;text-wrap:balance}'
    . '#zpStudioSeo .zpStudioSeo__title strong{color:#102a4f;font-weight:800}'
    . '#zpStudioSeo .zpStudioSeo__lead{margin:0;color:#6f7d90;font-size:14px;line-height:1.75}'
    . '#zpStudioSeo .zpGuide__features{grid-template-columns:repeat(2,minmax(0,1fr))}'
    // On phones the lazy team photo had a 0×0 box here, so Chrome never loaded it and the cards covered its place.
    . '#zpStudioSeo .zpGuide__team{min-width:1px;min-height:1px}'
    . '@media(max-width:760px){#zpStudioSeo{width:100%;max-width:100%;margin-left:0;margin-right:0}#zpStudioSeo .zpStudioSeo__head{grid-template-columns:1fr;gap:18px}#zpStudioSeo .zpStudioSeo__title{font-size:clamp(34px,10vw,46px)}#zpStudioSeo .zpGuide__features{grid-template-columns:1fr}}'
    . 'body.zpbs-studio-page.zpbs-studio-brief #zpStudioSeo{display:none!important}';
  return '<style id="zp-seo-studio-cennik">' . zp_seo_guide_css() . $css . '</style>'
    . '<section class="zpStudioSeo zpSeoPlanGuide" id="zpStudioSeo" aria-labelledby="zpStudioSeoTitle"><div class="zpStudioSeo__inner">'
    . '<header class="zpStudioSeo__head"><div><span class="zpStudioSeo__kicker">Cennik i wycena</span>'
    . '<h2 class="zpStudioSeo__title" id="zpStudioSeoTitle">Cennik stron internetowych, <strong>sklepów, logo i kampanii.</strong></h2></div>'
    . '<p class="zpStudioSeo__lead">Ceny „od” pokazują, od jakiej kwoty zaczynamy. Konkretną wycenę strony internetowej, sklepu albo logo przygotujemy po kilku pytaniach w Studio Wyceny, w ciągu 48–72 godzin.</p></header>'
    . $body . '</div></section>';
}

// Team photo and the person in the contact band of this guide.
add_filter('zp_seo_guide_setup', function ($setup, $path) {
  return $path === '/studio-wyceny/' ? ['photo' => 'team-duo', 'person' => 'mateusz'] : $setup;
}, 10, 2);

add_filter('do_shortcode_tag', function ($output, $tag) {
  if (!in_array($tag, ['zp_studio_wyceny', 'zp_studio_wyceny_cms'], true) || !is_string($output) || $output === '' || !zp_seo_studio_on() || !zp_seo_studio_is_page()) { return $output; }
  // H1: in chooser mode the brief (and its H1) is hidden, so the script keeps the plan's heading until the brief opens.
  $output = str_replace("setHTML('[data-brief-title]',c.title);", "if(root.dataset.mode==='brief'){setHTML('[data-brief-title]',c.title);}", $output);
  // The chooser shows the same project count as the rest of the site (stats.php).
  if (function_exists('zp_seo_plan_projects_count')) {
    $output = (string) preg_replace('~(chooser-v19\.html\?v=[^"\'&#\s]+)~', '${1}&#038;n=' . zp_seo_plan_projects_count(), $output, 1);
  }
  // The price list goes right after the contact form (its stylesheet follows the form), else at the end.
  $section = zp_seo_studio_section();
  $marker = '<style id="zp-studio-chooser-contact-v2619">';
  $pos = strpos($output, $marker);
  return $pos === false ? $output . $section : substr($output, 0, $pos) . $section . substr($output, $pos);
}, 20, 2);

add_filter('rank_math/json_ld', function ($data, $jsonld = null) {
  if (!is_array($data) || !zp_seo_studio_on() || !zp_seo_studio_is_page()) { return $data; }
  $url = (string) get_permalink(get_queried_object_id());
  $main = [];
  foreach (zp_seo_studio_faq() as $qa) {
    $main[] = ['@type' => 'Question', 'name' => $qa[0], 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $qa[1]]];
  }
  foreach ($data as $key => $node) {
    if (is_array($node) && in_array('FAQPage', array_map('strval', (array) ($node['@type'] ?? [])), true)) { unset($data[$key]); }
  }
  $data['zpStudioFaq'] = [
    '@type' => 'FAQPage',
    '@id' => trailingslashit($url) . '#pytania-o-cennik',
    'url' => $url,
    'name' => 'Pytania o cennik i wycenę',
    'inLanguage' => 'pl-PL',
    'mainEntity' => $main,
  ];
  return $data;
}, 99, 2);
