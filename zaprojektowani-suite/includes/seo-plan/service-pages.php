<?php
if (!defined('ABSPATH')) { exit; }

/**
 * Nationwide service pages and the local Katowice pages.
 *
 * The nationwide pages are copies of the Katowice pages (same shortcode, same Elementor
 * layout), so they get every stylesheet, script and fix the Katowice pages have. What
 * differs is decided here from the URL: H1, hero copy, headings, FAQ and links follow the
 * keyword plan, and the Katowice pages drop the sections that now live on the nationwide
 * pages. English pages (/en/…) keep the original Katowice templates and translations.
 */

/** Path => what the page shows. 'source' is the Katowice page a new page is copied from. */
function zp_seo_service_registry(): array {
  static $content = null;
  if ($content === null) {
    $content = [];
    foreach (zp_seo_content_pages() as $path => $page) {
      $content[$path] = ['kind' => 'strony', 'variant' => $page['variant'], 'source' => '/strony-internetowe-katowice/', 'title' => $page['title'], 'shortcode' => 'zp_strony_internetowe_katowice'];
    }
  }
  return $content + [
    '/tworzenie-stron-internetowych/'   => ['kind' => 'strony', 'variant' => 'strony-national',   'source' => '/strony-internetowe-katowice/', 'title' => 'Tworzenie stron internetowych', 'shortcode' => 'zp_strony_internetowe_katowice'],
    '/tworzenie-sklepow-internetowych/' => ['kind' => 'sklepy', 'variant' => 'sklepy-national',   'source' => '/sklepy-internetowe-katowice/', 'title' => 'Tworzenie sklepów internetowych', 'shortcode' => 'zp_sklepy_internetowe_katowice'],
    '/projektowanie-logo/'              => ['kind' => 'logo',   'variant' => 'logo-national',     'source' => '/logo-branding-katowice/',      'title' => 'Projektowanie logo', 'shortcode' => 'zp_logo_branding_katowice'],
    '/identyfikacja-wizualna/'          => ['kind' => 'logo',   'variant' => 'identity-national', 'source' => '/logo-branding-katowice/',      'title' => 'Identyfikacja wizualna', 'shortcode' => 'zp_logo_branding_katowice'],
    '/strony-internetowe-katowice/'     => ['kind' => 'strony', 'variant' => 'strony-local'],
    '/sklepy-internetowe-katowice/'     => ['kind' => 'sklepy', 'variant' => 'sklepy-local'],
    '/logo-branding-katowice/'          => ['kind' => 'logo',   'variant' => 'logo-local'],
  ];
}

/** Shortcodes that render a service template => its kind. */
function zp_seo_service_shortcodes(): array {
  return [
    'zp_strony_internetowe_katowice' => 'strony', 'zp_page_strony_katowice' => 'strony', 'zp_page_strony_internetowe_katowice' => 'strony',
    'zp_sklepy_internetowe_katowice' => 'sklepy', 'zp_page_sklepy_katowice' => 'sklepy',
    'zp_logo_branding_katowice' => 'logo', 'zp_page_logo_branding_katowice' => 'logo',
  ];
}

/** Variant for the current request ('' = original template). */
function zp_seo_service_variant(): string {
  if (!zp_seo_plan_active() || is_admin() || zp_seo_plan_is_en() || !did_action('wp')) { return ''; }
  $path = '';
  if (is_singular()) {
    $id = get_queried_object_id();
    if ($id) { $path = zp_seo_plan_path((string) get_permalink($id)); }
  }
  if ($path === '') { $path = zp_seo_plan_path(); }
  $reg = zp_seo_service_registry();
  return $reg[$path]['variant'] ?? '';
}

add_filter('do_shortcode_tag', function ($output, $tag) {
  $kinds = zp_seo_service_shortcodes();
  if (!isset($kinds[$tag]) || !is_string($output) || $output === '') { return $output; }
  $variant = zp_seo_service_variant();
  if ($variant === '') { return $output; }
  $spec = zp_seo_service_spec($variant);
  if (!$spec || $spec['kind'] !== $kinds[$tag]) { return $output; }
  if (!empty($spec['content'])) { return zp_seo_service_remap_links(zp_seo_content_transform($output, $spec['content'])); }
  return zp_seo_service_transform($output, $spec, $variant);
}, 20, 2);

// The nationwide shop page is built from the bundled section files, not from the
// sections edited in the Katowice page's CMS, so its copy stays predictable.
add_filter('pre_option_zp_suite_shop_katowice_sections', function ($pre) {
  if (!did_action('template_redirect') || zp_seo_service_variant() !== 'sklepy-national') { return $pre; }
  return [];
});

add_action('wp_head', function () {
  if (!zp_seo_plan_active() || (zp_seo_service_variant() === '' && !is_singular('post') && !is_front_page())) { return; }
  echo '<style id="zp-seo-plan-230">'
    . '.zpSeoLink{color:inherit!important;font-weight:600;text-decoration:underline;text-decoration-thickness:1px;text-underline-offset:3px}'
    . '.zpSeoLink:hover{text-decoration-thickness:2px}'
    // Guide sections on the content pages: the template's wrapper is dark, light sections set their own background.
    . 'section.section.zpSeoPlanGuide{background:#fff}#proces~section.section.zpSeoPlanGuide{background:#f7f8fa}'
    . (zp_seo_guide_page() ? zp_seo_guide_css() : '')
    // Website pages: on phones the hero H1 was 120% wide and its first line ran past the screen edge.
    . (zp_suite_service_kind() === 'strony' ? '@media(max-width:680px){html body .hero h1{width:auto}}' : '')
    . '</style>' . "\n";
}, 40);

/* ------------------------------------------------------------------ transform */

function zp_seo_service_transform(string $html, array $spec, string $variant): string {
  foreach ($spec['drop'] ?? [] as $marker) { $html = zp_seo_html_drop_section($html, $marker); }
  if (!empty($spec['faq'])) { $html = zp_seo_faq_rebuild($html, $spec['faq'], $spec['faq_style'] ?? 'details'); }
  if (!empty($spec['h1'])) {
    $h1 = $spec['h1'];
    $html = preg_replace_callback('~(<h1\b[^>]*>)(.*?)(</h1>)~is', static function ($m) use ($h1) { return $m[1] . $h1 . $m[3]; }, $html, 1);
  }
  $missed = [];
  foreach ($spec['replace'] ?? [] as $pair) {
    $before = $html;
    $html = zp_seo_ws_replace($html, $pair[0], $pair[1]);
    if ($html === $before) { $missed[] = mb_substr($pair[0], 0, 70); }
  }
  foreach ($spec['regex'] ?? [] as $pair) {
    $new = preg_replace($pair[0], $pair[1], $html, 1, $count);
    if (is_string($new) && $count) { $html = $new; } else { $missed[] = $pair[0]; }
  }
  $html = zp_seo_service_remap_links($html);
  foreach ($spec['after'] ?? [] as $marker => $callback) {
    $insert = is_callable($callback) ? (string) call_user_func($callback) : '';
    if ($insert !== '') { $html = zp_seo_html_insert_after_section($html, $marker, $insert); }
  }
  // 'before': [marker of a section, callback, fallback marker to insert after when that section is missing].
  foreach ($spec['before'] ?? [] as $rule) {
    $insert = is_callable($rule[1]) ? (string) call_user_func($rule[1]) : '';
    if ($insert === '') { continue; }
    $range = zp_seo_html_section_range($html, $rule[0]);
    if ($range) { $html = substr($html, 0, $range[0]) . $insert . "\n" . substr($html, $range[0]); }
    elseif (!empty($rule[2])) { $html = zp_seo_html_insert_after_section($html, $rule[2], $insert); }
  }
  // Deliberate links to the local pages; data-zp-local keeps them out of the site-wide remap (links.php).
  $html = strtr($html, [
    'href="{{strony-katowice}}"' => 'data-zp-local="1" href="' . esc_url(home_url('/strony-internetowe-katowice/')) . '"',
    'href="{{sklepy-katowice}}"' => 'data-zp-local="1" href="' . esc_url(home_url('/sklepy-internetowe-katowice/')) . '"',
    'href="{{logo-katowice}}"'   => 'data-zp-local="1" href="' . esc_url(home_url('/logo-branding-katowice/')) . '"',
  ]);
  if ($missed && defined('WP_DEBUG') && WP_DEBUG) {
    error_log('[zp-seo-plan] ' . $variant . ': not found in template: ' . implode(' | ', $missed));
  }
  return $html;
}

/** Replace the first occurrence of $old, treating any run of whitespace as flexible. */
function zp_seo_ws_replace(string $html, string $old, string $new): string {
  $parts = preg_split('~\s+~u', trim($old));
  $re = '~' . implode('\s+', array_map(static function ($p) { return preg_quote($p, '~'); }, $parts)) . '~u';
  $out = preg_replace_callback($re, static function () use ($new) { return $new; }, $html, 1);
  return is_string($out) ? $out : $html;
}

/** Katowice service URLs inside the templates point to the nationwide pages. */
function zp_seo_service_remap_links(string $html): string {
  $map = [
    'strony-internetowe-katowice' => '/tworzenie-stron-internetowych/',
    'sklepy-internetowe-katowice' => '/tworzenie-sklepow-internetowych/',
    'logo-branding-katowice'      => '/projektowanie-logo/',
  ];
  $out = preg_replace_callback('~href=(["\'])(?:https?://(?:www\.)?zaprojektowani\.com)?/(strony-internetowe-katowice|sklepy-internetowe-katowice|logo-branding-katowice)/(#[^"\']*)?\1~i', static function ($m) use ($map) {
    return 'href=' . $m[1] . esc_url(home_url($map[strtolower($m[2])])) . ($m[3] ?? '') . $m[1];
  }, $html);
  return is_string($out) ? $out : $html;
}

/** [start, end) offsets of the first <section> whose opening tag contains $marker. */
function zp_seo_html_section_range(string $html, string $marker): ?array {
  $offset = 0;
  while (preg_match('~<section\b[^>]*>~i', $html, $m, PREG_OFFSET_CAPTURE, $offset)) {
    $tag = $m[0][0];
    $start = (int) $m[0][1];
    if (strpos($tag, $marker) === false) { $offset = $start + strlen($tag); continue; }
    $depth = 0;
    $pos = $start;
    while (preg_match('~<(/?)section\b[^>]*>~i', $html, $t, PREG_OFFSET_CAPTURE, $pos)) {
      $depth += $t[1][0] === '/' ? -1 : 1;
      $pos = (int) $t[0][1] + strlen($t[0][0]);
      if ($depth === 0) { return [$start, $pos]; }
    }
    return null;
  }
  return null;
}

function zp_seo_html_drop_section(string $html, string $marker): string {
  $range = zp_seo_html_section_range($html, $marker);
  return $range ? substr($html, 0, $range[0]) . substr($html, $range[1]) : $html;
}

function zp_seo_html_insert_after_section(string $html, string $marker, string $insert): string {
  $range = zp_seo_html_section_range($html, $marker);
  return $range ? substr($html, 0, $range[1]) . "\n" . $insert . substr($html, $range[1]) : $html;
}

/**
 * Rebuild a FAQ list. $items: ['keep', n] keeps the template's n-th question (1-based),
 * ['new', question, answer_html] adds one. Numbers are rewritten in order.
 */
function zp_seo_faq_rebuild(string $html, array $items, string $style): string {
  $re = $style === 'shop'
    ? '~<article class="zpFaqShopKatNavy__item[^"]*">.*?</article>~s'
    : '~<details class="faq-item">.*?</details>~s';
  if (!preg_match_all($re, $html, $m, PREG_OFFSET_CAPTURE) || !$m[0]) { return $html; }
  $blocks = array_map(static function ($x) { return $x[0]; }, $m[0]);
  $first = (int) $m[0][0][1];
  $lastBlock = end($m[0]);
  $end = (int) $lastBlock[1] + strlen($lastBlock[0]);
  $out = [];
  foreach ($items as $item) {
    if ($item[0] === 'keep' && isset($blocks[$item[1] - 1])) { $out[] = $blocks[$item[1] - 1]; }
    if ($item[0] === 'new') { $out[] = zp_seo_faq_item($style, $item[1], $item[2]); }
  }
  $n = 0;
  foreach ($out as $i => $block) {
    $n++;
    $no = str_pad((string) $n, 2, '0', STR_PAD_LEFT);
    if ($style === 'shop') {
      $block = preg_replace('~(<button class="zpFaqShopKatNavy__question"[^>]*>\s*<span>)\d+(</span>)~', '${1}' . $no . '${2}', $block, 1);
      $block = preg_replace('~<article class="zpFaqShopKatNavy__item[^"]*">~', '<article class="zpFaqShopKatNavy__item' . ($i === 0 ? ' is-open' : '') . '">', $block, 1);
      $block = preg_replace('~aria-expanded="(?:true|false)"~', 'aria-expanded="' . ($i === 0 ? 'true' : 'false') . '"', $block, 1);
    } else {
      $block = preg_replace('~<span class="faq-no">\d+</span>~', '<span class="faq-no">' . $no . '</span>', $block, 1);
    }
    $out[$i] = $block;
  }
  $separator = $style === 'shop' ? "\n\n        " : "\n          ";
  return substr($html, 0, $first) . implode($separator, $out) . substr($html, $end);
}

function zp_seo_faq_item(string $style, string $question, string $answer): string {
  if ($style === 'shop') {
    return '<article class="zpFaqShopKatNavy__item"><button class="zpFaqShopKatNavy__question" type="button" aria-expanded="false"><span>00</span><strong>'
      . $question . '</strong><i data-lucide="plus"></i></button><div class="zpFaqShopKatNavy__answer"><p>' . $answer . '</p></div></article>';
  }
  return '<details class="faq-item"><summary><span class="faq-no">00</span><span>' . $question
    . '</span><i class="faq-plus"></i></summary><div class="faq-answer">' . $answer . '</div></details>';
}

/* --------------------------------------------------------- moved shop guide */

/**
 * The post /sklepy-internetowe/tworzenie-sklepow-internetowych-od-pomyslu-na-oferte-do-gotowego-sklepu-online/
 * (the strongest non-brand URL in Search Console) moves onto the nationwide shop page;
 * the post itself becomes a draft with a 301 here. Its text is read from the database.
 */
function zp_seo_shop_guide_html(): string {
  $id = (int) get_option('zp_seo_plan_shop_post', 0);
  $post = $id ? get_post($id) : null;
  if (!$post || $post->post_type !== 'post') { return ''; }
  $content = (string) $post->post_content;
  if ($content === '') { return ''; }
  if (function_exists('has_blocks') && has_blocks($content)) { $content = do_blocks($content); }
  $content = zp_seo_guide_clean_post_html((string) $content);
  $content = preg_replace('~<(script|style|noscript|template|form|iframe)\b[^>]*>.*?</\1>~is', '', $content);
  $content = strip_shortcodes((string) $content);
  if (stripos($content, '<p') === false) { $content = wpautop($content); }
  $content = preg_replace('~<h1\b[^>]*>.*?</h1>~is', '', $content);
  // Attributes go in zp_seo_guide_clean_post_html() (the guide classes stay); this is the fallback without DOM.
  if (!class_exists('DOMDocument')) { $content = preg_replace('~\s(?:style|class|id|data-[a-z0-9_-]+)=("[^"]*"|\'[^\']*\')~i', '', (string) $content); }
  $content = wp_kses_post((string) $content);
  $content = preg_replace('~<p>\s*(?:&nbsp;)?\s*</p>~i', '', $content);
  if (trim(wp_strip_all_tags((string) $content)) === '') { return ''; }
  $lead = 'Pełny przewodnik po tym, jak powstaje sklep: od modelu sprzedaży i oferty, przez strukturę kategorii i '
    . '<a class="zpSeoLink" href="' . esc_url(home_url('/sklepy-internetowe/karta-produktu-w-sklepie-internetowym/')) . '">kartę produktu</a>, '
    . 'po płatności, dostawy i start sprzedaży. Jeśli przenosisz istniejący sklep, przeczytaj o tym, jak wygląda '
    . '<a class="zpSeoLink" href="' . esc_url(home_url('/strony-internetowe/migracja-sklepu-na-woocommerce/')) . '">migracja sklepu internetowego</a>. '
    . 'Orientacyjne koszty opisujemy w poradniku <a class="zpSeoLink" href="' . esc_url(home_url('/seo-i-konwersja/ile-kosztuje-sklep-internetowy-woocommerce-w-2026-roku/')) . '">ile kosztuje sklep internetowy</a>, '
    . 'a <a class="zpSeoLink" href="' . esc_url(home_url('/studio-wyceny/')) . '">wycenę sklepu internetowego</a> przygotujemy po kilku pytaniach w Studio Wyceny.';
  return '<section class="zpShopSeoBoost zpSeoPlanGuide" id="poradnik-tworzenie-sklepu" aria-labelledby="zpSeoPlanGuideTitle">'
    . '<div class="zpShopSeoBoost__inner"><header class="zpShopSeoBoost__head">'
    . '<span class="zpShopSeoBoost__kicker">Poradnik / tworzenie sklepu internetowego krok po kroku</span>'
    . '<div class="zpShopSeoBoost__headline"><h2 class="zpShopSeoBoost__title" id="zpSeoPlanGuideTitle">Od pomysłu na ofertę do gotowego sklepu online.</h2>'
    . '<p class="zpShopSeoBoost__lead">' . $lead . '</p></div></header>'
    . zp_seo_guide_body((string) $content, '/tworzenie-sklepow-internetowych/') . '</div></section>';
}

/* ------------------------------------------------------------------- specs */

function zp_seo_service_spec(string $variant): ?array {
  $L = static function (string $path, string $text): string {
    return '<a class="zpSeoLink" href="' . esc_url(home_url($path)) . '">' . $text . '</a>';
  };
  $cost_www  = '/strony-www/ile-kosztuje-stworzenie-strony-internetowej-dla-firmy/';
  $cost_logo = '/logo-branding/ile-kosztuje-logo-dla-firmy-i-co-obejmuje-cena/';
  $local = static function (string $key, string $text): string {
    return '<a class="zpSeoLink" href="{{' . $key . '}}">' . $text . '</a>';
  };
  // Articles published by the plan (2.5.0): linked only once they are live, plain text otherwise.
  $A = static function (string $path, string $text) use ($L): string {
    return zp_seo_plan_link_is_live($path) ? $L($path, $text) : $text;
  };

  $content = zp_seo_content_page_by_variant($variant);
  if ($content) { return ['kind' => $content['kind'], 'content' => $content]; }

  switch ($variant) {

    case 'strony-national':
      return [
        'kind' => 'strony',
        'h1' => 'Tworzenie i projektowanie <strong class="gradient-text">stron internetowych dla firm</strong>',
        'faq' => [
          ['new', 'Czy tworzycie strony internetowe dla firm z całej Polski?', 'Tak. Projektujemy i tworzymy strony internetowe dla firm z całej Polski — <strong>strony firmowe, ' . $L('/strona-wizytowka/', 'strony wizytówki') . ', landing page’e i rozbudowane serwisy</strong>. Brief, prezentacje i odbiory prowadzimy online, a z firmami ze Śląska spotykamy się też w naszym biurze w Katowicach.'],
          ['keep', 2], ['keep', 3], ['keep', 4],
          ['new', 'Ile kosztuje stworzenie strony internetowej?', 'Cena zależy od zakresu: liczby podstron, poziomu projektu, treści, funkcji, SEO czy sklepu WooCommerce. Od czego zależy cena strony wizytówki, firmowej i rozbudowanej, wyjaśniamy w poradniku ' . $L($cost_www, 'ile kosztuje strona internetowa') . '. Dokładną wycenę przygotujemy po ' . $L('/studio-wyceny/', 'uzupełnieniu Studia Wyceny') . ' — dobierzemy wtedy zakres: landing page, Starter, Premium albo projekt indywidualny.'],
          ['new', 'Na czym robicie strony internetowe?', 'Najczęściej ' . $L('/strony-wordpress/', 'na WordPressie') . '. Projekt powstaje indywidualnie, bez gotowego szablonu, a wdrożenie daje <strong>łatwą edycję treści, szybkie działanie i solidne podstawy SEO</strong>. Przy prostszych projektach, takich jak strona wizytówka czy landing page, dobieramy lżejszą strukturę — zasada zostaje ta sama: strona ma prowadzić do kontaktu. Jeśli rozważasz kreator albo samodzielną pracę, przeczytaj, ' . $A('/strony-internetowe/jak-stworzyc-strone-internetowa/', 'jak stworzyć stronę internetową') . ' i kiedy warto oddać ją agencji.'],
          ['new', 'Czy wykonujecie sklepy internetowe WooCommerce?', 'Tak. ' . $L('/tworzenie-sklepow-internetowych/', 'Tworzenie sklepów internetowych') . ' na WooCommerce to nasza druga główna specjalizacja — od prostych sklepów produktowych po wdrożenia z wariantami, płatnościami, dostawami, katalogiem B2B i konfiguratorami.'],
          ['keep', 7], ['keep', 8],
          ['new', 'Ile trwa stworzenie strony internetowej?', 'Termin zależy od zakresu i tempa przekazywania materiałów. Strona wizytówka lub landing page powstaje szybciej niż serwis firmowy z kilkunastoma podstronami i treściami SEO. Po briefie podajemy harmonogram z etapami i datą publikacji.'],
        ],
        'replace' => [
          ['<p class="hero-eyebrow">Strony internetowe • Katowice</p>', '<p class="hero-eyebrow">Strony internetowe • cała Polska</p>'],
          ['Tworzymy strony dla firm z Katowic, Śląska i całej Polski: od', 'Projektujemy i tworzymy strony internetowe dla firm z całej Polski: od'],
          ['<h2 class="section-title">Wybierz zakres — od landing page po <strong>serwis premium.</strong></h2>', '<h2 class="section-title">Strona wizytówka, firmowa czy <strong>rozbudowany serwis?</strong></h2>'],
          ['<p class="section-lead">Ten wybór pomaga szybko określić punkt startu. W Studio Wyceny doprecyzujesz', '<p class="section-lead">Ten wybór pomaga szybko określić punkt startu. Od czego zależy cena, wyjaśniamy w poradniku ' . $L($cost_www, 'ile kosztuje strona internetowa') . ', a dokładną ' . $L('/studio-wyceny/', 'wycenę strony internetowej') . ' przygotujemy na podstawie Studia Wyceny. Doprecyzujesz w nim'],
          ['<h2 class="section-title">Duże projekty. <strong>Jeszcze większy kontekst.</strong></h2>', '<h2 class="section-title">Realizacje stron internetowych. <strong>Duże projekty, większy kontekst.</strong></h2>'],
          ['<h2 class="section-title">Od briefu do publikacji. <strong>Bez chaosu między etapami.</strong></h2>', '<h2 class="section-title">Etapy tworzenia strony internetowej. <strong>Od briefu do publikacji.</strong></h2>'],
          ['<strong>jasny zakres, terminy i kryterium akceptacji.</strong></p>', '<strong>jasny zakres, terminy i kryterium akceptacji.</strong> Szczegółowo opisujemy je w poradniku ' . $L('/strony-internetowe/tworzenie-stron-internetowych-profesjonalny-proces-od-strategii-do-wdrozenia/', 'etapy tworzenia strony internetowej') . '.</p>'],
          ['<h2 class="section-title">Nie tylko wygląd. <strong>Cały system strony.</strong></h2>', '<h2 class="section-title">Projektowanie stron www. <strong>UX, treści i design w jednym systemie.</strong></h2>'],
          ['Tak. Inaczej projektuje się stronę kancelarii, inaczej dewelopera, salonu beauty, lekarza czy producenta.', 'Tak. Inaczej projektuje się stronę ' . $L('/strony-internetowe-dla-kancelarii/', 'kancelarii') . ', inaczej ' . $L('/strony-internetowe-dla-deweloperow/', 'dewelopera') . ', salonu beauty, ' . $L('/strony-internetowe-dla-lekarzy/', 'lekarza') . ' czy producenta.'],
          ['<p>Aktualizacje, kopie zapasowe, drobne zmiany i rozwój: nowe sekcje, podstrony SEO, landing pages pod kampanie.</p>', '<p>Aktualizacje, kopie zapasowe i drobne zmiany w ramach usługi ' . $L('/opieka-wordpress/', 'opieka nad stroną WordPress') . ', a do tego rozwój: nowe sekcje, podstrony SEO, landing pages pod kampanie.</p>'],
          ['<p class="section-kicker">Katowice • Śląsk • cała Polska</p>', '<p class="section-kicker">Cała Polska • siedziba w Katowicach</p>'],
          ['Z Katowic projektujemy marki dla firm z całej Polski. Brief, prezentacje kierunków, konsultacje i przekazanie plików możemy przeprowadzić online — etap po etapie.', 'Pracujemy z firmami z całej Polski, a nasze studio mieści się w Katowicach. Brief, makiety, prezentacje projektu, konsultacje i odbiór strony prowadzimy online — etap po etapie. Firmy ze Śląska zapraszamy też do biura: ' . $local('strony-katowice', 'strony internetowe w Katowicach') . '.'],
        ],
      ];

    case 'strony-local':
      return [
        'kind' => 'strony',
        'drop' => ['class="section manifesto"', 'id="oferta"', 'id="pakiety"', 'id="portfolio"', 'id="branze"', 'class="pointing"', 'id="analityka"', 'id="zakres"'],
        'h1' => 'Strony internetowe <strong class="gradient-text">w Katowicach i na Śląsku</strong>',
        'faq' => [
          ['new', 'Czy możemy spotkać się w Katowicach?', 'Tak. Nasze biuro mieści się w Katowicach przy <strong>ul. Modelarskiej 18/2</strong>. Na spotkaniu omawiamy brief, cele strony i zakres prac. Kolejne etapy — makiety, projekt i odbiory — prowadzimy w biurze albo online, jak wygodniej.'],
          ['new', 'Czy tworzycie strony internetowe dla firm z całego Śląska?', 'Tak. Pracujemy z firmami z Katowic i całego województwa śląskiego, a także z innych regionów Polski. Firmom działającym lokalnie układamy strukturę treści pod <strong>wyszukiwania lokalne</strong> i łączymy stronę z Profilem Firmy w Google.'],
          ['keep', 2],
          ['new', 'Ile kosztuje strona internetowa w Katowicach?', 'Cena zależy od zakresu, a nie od miasta: liczby podstron, poziomu projektu, treści i funkcji. Od czego zależy cena, wyjaśniamy w poradniku ' . $L($cost_www, 'ile kosztuje strona internetowa') . ', a dokładną wycenę przygotujemy po uzupełnieniu ' . $L('/studio-wyceny/', 'Studia Wyceny') . '.'],
          ['keep', 3],
          ['new', 'Gdzie znajdę pełną ofertę stron internetowych?', 'Pakiety, proces, realizacje i odpowiedzi na pytania o zakres opisujemy na stronie ' . $L('/tworzenie-stron-internetowych/', 'tworzenie stron internetowych') . '. Tam też sprawdzisz, czym różni się strona wizytówka, strona firmowa i rozbudowany serwis.'],
          ['keep', 8],
        ],
        'replace' => [
          ['Tworzymy strony dla firm z Katowic, Śląska i całej Polski: od', 'Studio z Katowic, ul. Modelarska 18/2. Tworzymy strony internetowe dla firm ze Śląska: od'],
          ['<p class="section-kicker">Katowice • Śląsk • cała Polska</p>', '<p class="section-kicker">Katowice • ul. Modelarska 18/2 • Śląsk</p>'],
          ['<h2 class="section-title"> Jedno studio. <strong>Współpraca bez granic lokalizacji.</strong> </h2>', '<h2 class="section-title">Spotkajmy się <strong>w Katowicach.</strong></h2>'],
          ['Z Katowic projektujemy marki dla firm z całej Polski. Brief, prezentacje kierunków, konsultacje i przekazanie plików możemy przeprowadzić online — etap po etapie.', 'Zapraszamy do naszego biura przy ul. Modelarskiej 18/2 w Katowicach — tam omówimy brief, cele i zakres strony. Jeśli wygodniej, cały proces przeprowadzimy online. Pełny zakres usług opisujemy na stronie ' . $L('/tworzenie-stron-internetowych/', 'tworzenie stron internetowych') . '.'],
        ],
      ];

    case 'sklepy-national':
      return [
        'kind' => 'sklepy',
        'faq_style' => 'shop',
        'h1' => 'Tworzenie sklepów internetowych <span class="zh__grad">na WooCommerce</span>',
        'faq' => [
          ['new', 'Czy tworzycie sklepy internetowe dla firm z całej Polski?', 'Tak. Projektujemy i wdrażamy <strong>sklepy internetowe dla firm z całej Polski</strong>. Najczęściej pracujemy na WordPressie i WooCommerce, ponieważ daje to dużą elastyczność przy sprzedaży produktów, usług, zapytań B2B, konfiguratorów, płatności, dostaw, SEO oraz późniejszym rozwoju sklepu. Firmy ze Śląska zapraszamy też do biura — zobacz ' . $local('sklepy-katowice', 'sklepy internetowe w Katowicach') . '.'],
          ['keep', 2], ['keep', 3], ['keep', 4], ['keep', 5],
          ['new', 'Ile kosztuje stworzenie sklepu internetowego?', 'Cena zależy od zakresu: liczby produktów i kategorii, projektu graficznego, treści, integracji płatności i dostaw, wariantów, filtrów, automatyzacji i SEO. Orientacyjne kwoty opisujemy w poradniku ' . $L('/seo-i-konwersja/ile-kosztuje-sklep-internetowy-woocommerce-w-2026-roku/', 'ile kosztuje sklep internetowy') . ', a dokładną ' . $L('/studio-wyceny/', 'wycenę sklepu internetowego') . ' przygotujemy po kilku pytaniach w Studio Wyceny.'],
          ['keep', 7], ['keep', 8], ['keep', 9], ['keep', 10], ['keep', 11], ['keep', 12],
          ['new', 'Czy projektujecie sklepy WooCommerce pod konkretne branże?', 'Tak. Sklep dla producenta, marki premium, firmy B2B, salonu, dystrybutora albo marki lokalnej powinien mieć inną strukturę kategorii, kart produktów, filtrów i ścieżki zakupu. Dlatego dopasowujemy WooCommerce do produktu, marży, sposobu sprzedaży i dalszego SEO e-commerce.'],
          ['new', 'Czy przenosicie istniejące sklepy na WooCommerce?', 'Tak. Przenosimy produkty, kategorie, klientów i zamówienia, a przede wszystkim adresy URL i przekierowania, żeby sklep nie stracił widoczności w Google. Jak wygląda taki proces, opisujemy w poradniku ' . $L('/strony-internetowe/migracja-sklepu-na-woocommerce/', 'migracja sklepu na WooCommerce') . '.'],
          ['keep', 14], ['keep', 15],
        ],
        'replace' => [
          ['aria-label="Hero Sklepy internetowe Katowice"', 'aria-label="Hero Tworzenie sklepów internetowych"'],
          ['<span class="dot"></span>Sklepy internetowe • Katowice</p>', '<span class="dot"></span>Sklepy internetowe • cała Polska</p>'],
          ['Projektujemy sklepy dla firm z Katowic, Śląska i całej Polski: od', 'Tworzymy sklepy internetowe WooCommerce dla firm z całej Polski: od'],
          ['<a class="zh__chip" href="/sklepy-internetowe-katowice/"><b>01</b><span>sklepy internetowe Katowice</span></a>', '<a class="zh__chip" href="{{sklepy-katowice}}"><b>01</b><span>sklepy internetowe w Katowicach</span></a>'],
          ['Tworzenie sklepów internetowych Katowice — WooCommerce, który porządkuje ofertę i prowadzi klienta do zakupu.', 'Tworzenie sklepów internetowych — WooCommerce, który porządkuje ofertę i prowadzi klienta do zakupu.'],
          ['Katowice / Śląsk / cała Polska', 'Cała Polska / siedziba w Katowicach'],
          ['alt="Zespół Zaprojektowani.com — projektowanie sklepów internetowych Katowice"', 'alt="Zespół Zaprojektowani.com — projektowanie sklepów internetowych"'],
          ['<span class="zpShopSeoBoost__kicker">Sklepy internetowe Katowice / WooCommerce / sprzedaż online</span>', '<span class="zpShopSeoBoost__kicker">Dlaczego WooCommerce / sprzedaż online</span>'],
          ['Sklepy internetowe Katowice — WooCommerce i sprzedaż online.', 'Dlaczego WooCommerce? Sklep, który rośnie razem z firmą.'],
          ['Tworzymy sklepy internetowe dla firm z Katowic, Śląska i całej Polski. Łączymy projekt graficzny, WooCommerce, płatności online, UX koszyka, SEO i techniczne wdrożenie, żeby sklep był gotowy do sprzedaży.', 'Tworzymy sklepy internetowe dla firm z całej Polski. Łączymy projekt graficzny, WooCommerce, płatności online, UX koszyka, SEO i techniczne wdrożenie, żeby sklep był gotowy do sprzedaży. Porównania platform znajdziesz w poradnikach ' . $L('/strony-internetowe/woocommerce-czy-shopify/', 'WooCommerce czy Shopify') . ' oraz ' . $L('/strony-internetowe/woocommerce-czy-prestashop/', 'WooCommerce czy PrestaShop') . '.'],
          ['<span class="zpShopSeoBoost__cardKicker">Sklep WooCommerce Katowice</span>', '<span class="zpShopSeoBoost__cardKicker">Sklep WooCommerce</span>'],
          ['<span class="zpShopSeoBoost__cardKicker">Projektowanie sklepów internetowych Katowice</span>', '<span class="zpShopSeoBoost__cardKicker">Projektowanie sklepów internetowych</span>'],
          ['<span>Sklepy internetowe Katowice / WooCommerce</span>', '<span>Tworzenie sklepów internetowych / WooCommerce</span>'],
          ['<span class="zpFaqShopKatNavy__kicker">FAQ / Sklepy internetowe Katowice</span>', '<span class="zpFaqShopKatNavy__kicker">FAQ / Tworzenie sklepów internetowych</span>'],
          ['FAQ — sklepy internetowe Katowice i WooCommerce.', 'FAQ — tworzenie sklepów internetowych i WooCommerce.'],
          ['albo sklep lokalnej firmy z Katowic.', 'albo sklep lokalnej firmy.'],
        ],
        // The guide goes last, right above the FAQ (Mat, 2.5.0).
        'before' => [['id="zpFaqShopKatNavy"', 'zp_seo_shop_guide_html', 'id="sklepy-woocommerce-katowice"']],
      ];

    case 'sklepy-local':
      return [
        'kind' => 'sklepy',
        'faq_style' => 'shop',
        'drop' => ['id="zpShopWhyLight"', 'id="oferta-sklepow"', 'id="sklepy-woocommerce-katowice"', 'id="zpShopProcessFlow"', 'id="zpEcomIndustries"'],
        'h1' => 'Sklepy internetowe <span class="zh__grad">w Katowicach i na Śląsku</span>',
        'faq' => [
          ['new', 'Czy tworzycie sklepy internetowe dla firm z Katowic?', 'Tak. Projektujemy i wdrażamy <strong>sklepy internetowe dla firm z Katowic i całego Śląska</strong>. Zapraszamy do naszego biura przy ul. Modelarskiej 18/2 w Katowicach — tam omówimy model sprzedaży, produkty i zakres wdrożenia. Pełny zakres usług opisujemy na stronie ' . $L('/tworzenie-sklepow-internetowych/', 'tworzenie sklepów internetowych') . '.'],
          ['new', 'Czy prowadzicie projekt sklepu w biurze, czy online?', 'Jak wygodniej. Brief i warsztat sprzedażowy możemy zrobić w biurze w Katowicach, a projekt, wdrożenie i odbiory prowadzić online. Po starcie sklepu pomagamy w rozbudowie, treściach i kampaniach.'],
          ['new', 'Ile kosztuje sklep internetowy?', 'Cena zależy od zakresu: liczby produktów, integracji płatności i dostaw, projektu i SEO. Orientacyjne kwoty opisujemy w poradniku ' . $L('/seo-i-konwersja/ile-kosztuje-sklep-internetowy-woocommerce-w-2026-roku/', 'ile kosztuje sklep internetowy') . ', a dokładną wycenę przygotujemy w ' . $L('/studio-wyceny/', 'Studio Wyceny') . '.'],
          ['keep', 7], ['keep', 9], ['keep', 15],
        ],
        'replace' => [
          ['Projektujemy sklepy dla firm z Katowic, Śląska i całej Polski: od', 'Studio z Katowic, ul. Modelarska 18/2. Projektujemy sklepy WooCommerce dla firm ze Śląska: od'],
          ['<a class="zh__chip" href="/sklepy-internetowe-katowice/"><b>01</b><span>sklepy internetowe Katowice</span></a>', '<a class="zh__chip" href="/tworzenie-sklepow-internetowych/"><b>01</b><span>pełna oferta sklepów</span></a>'],
        ],
      ];

    case 'logo-national':
      return [
        'kind' => 'logo',
        'h1' => 'Projektowanie logo <strong class="gradient-text">i logotypów dla firm</strong>',
        'faq' => [
          ['new', 'Czy projektujecie logo dla firm z całej Polski?', 'Tak. Projektujemy <strong>logo i logotypy</strong> dla firm z całej Polski. Cały proces — od briefu po odbiór uporządkowanych plików — prowadzimy online, a firmy ze Śląska mogą spotkać się z nami w biurze w Katowicach.'],
          ['new', 'Czym różni się logo, logotyp i sygnet?', '<strong>Logotyp</strong> to znak zbudowany z samego napisu — nazwy firmy w dopracowanym kroju. <strong>Sygnet</strong> to symbol graficzny, który działa też samodzielnie, np. jako ikona czy avatar. <strong>Logo</strong> to potoczna nazwa całego znaku: logotypu, sygnetu albo ich połączenia. Formę dobieramy do nazwy, branży i miejsc użycia. Przykłady pokazujemy w poradniku ' . $A('/logo-branding/logo-logotyp-sygnet-roznice/', 'logo, logotyp czy sygnet') . '.'],
          ['keep', 2], ['keep', 3], ['keep', 4], ['keep', 5], ['keep', 8], ['keep', 9], ['keep', 10], ['keep', 13], ['keep', 14], ['keep', 15],
        ],
        'replace' => [
          ['<p class="hero-eyebrow">Logo &amp; branding • Katowice</p>', '<p class="hero-eyebrow">Projektowanie logo • cała Polska</p>'],
          ['Tworzymy <strong>logo, identyfikacje wizualne i brandbooki</strong>, które dobrze wyglądają nie tylko na prezentacji. Projektujemy system gotowy na stronę internetową, social media, druk, opakowania i materiały sprzedażowe.', 'Projektujemy <strong>logo i logotypy dla firm z całej Polski</strong> — od strategii i koncepcji, przez warianty znaku, po komplet plików do druku i internetu. Każdy projekt sprawdzamy na stronie, w social mediach, w druku i na materiałach sprzedażowych.'],
          ['z pełną księgą znaku i zasadami wdrożenia.', 'z pełną księgą znaku i zasadami wdrożenia. Orientacyjne kwoty opisujemy w poradniku ' . $L($cost_logo, 'ile kosztuje logo') . '.'],
          ['na dokumentach, wizytówkach, stronie i materiałach firmowych.', 'na dokumentach, wizytówkach, stronie i materiałach firmowych. Gdy znak potrzebuje całego systemu, zajmujemy się też projektem, jakim jest ' . $L('/identyfikacja-wizualna/', 'identyfikacja wizualna') . '.'],
          ['<h2 class="section-title"> Różne branże. <strong>Żadnego jednego stylu na wszystko.</strong> </h2>', '<h2 class="section-title">Realizacje logo. <strong>Żadnego jednego stylu na wszystko.</strong></h2>'],
          ['<h2 class="section-title"> Od briefu do plików <strong>gotowych do wdrożenia.</strong> </h2>', '<h2 class="section-title">Jak projektujemy logo: <strong>od briefu do plików.</strong></h2>'],
          ['Wiesz, nad czym pracujemy, po co i co wydarzy się dalej.', 'Wiesz, nad czym pracujemy, po co i co wydarzy się dalej. Krok po kroku opisujemy to w poradniku ' . $L('/logo-branding/projektowanie-logo-jak-powstaje-znak-ktory-dziala-w-internecie-druku-i-sprzedazy/', 'jak zaprojektować logo') . '.'],
          ['<h2> System gotowy do pracy. <strong class="gradient-text">Nie pojedynczy plik.</strong> </h2>', '<h2>Warianty i pliki logo. <strong class="gradient-text">Nie pojedynczy plik.</strong></h2>'],
          ['<p class="section-kicker">Katowice • Śląsk • cała Polska</p>', '<p class="section-kicker">Cała Polska • siedziba w Katowicach</p>'],
          ['Z Katowic projektujemy marki dla firm z całej Polski. Brief, prezentacje kierunków, konsultacje i przekazanie plików możemy przeprowadzić online — etap po etapie.', 'Projektujemy logo dla firm z całej Polski. Brief, prezentacje kierunków, konsultacje i przekazanie plików prowadzimy online — etap po etapie. Firmy ze Śląska zapraszamy do biura: ' . $local('logo-katowice', 'projektowanie logo w Katowicach') . '.'],
          ['<p class="section-kicker">FAQ / logo i branding</p>', '<p class="section-kicker">FAQ / projektowanie logo</p>'],
          ['dzięki którym marka pozostaje spójna na stronie, w social mediach i druku.', 'dzięki którym marka pozostaje spójna na stronie, w social mediach i druku. Ten zakres opisujemy na stronie ' . $L('/identyfikacja-wizualna/', 'identyfikacja wizualna') . '.'],
          ['Dodatkowe materiały, opakowania lub rozbudowane wdrożenie wyceniamy indywidualnie.', 'Dodatkowe materiały, opakowania lub rozbudowane wdrożenie wyceniamy indywidualnie. Więcej w poradniku ' . $L($cost_logo, 'ile kosztuje logo dla firmy') . '.'],
          ['uporządkowany zestaw gotowy dla drukarni i zespołu digital.', 'uporządkowany zestaw gotowy dla drukarni i zespołu digital. Czym jest ' . $A('/logo-branding/logo-wektorowe-pliki-logo/', 'logo wektorowe') . ' i do czego służy każdy format, wyjaśniamy w osobnym poradniku.'],
          ['przygotowany do legalnego, codziennego użycia przez firmę.', 'przygotowany do legalnego, codziennego użycia przez firmę. Jeśli chcesz mieć wyłączność na znak, sprawdź, jak wygląda ' . $A('/logo-branding/jak-zastrzec-logo/', 'zastrzeżenie logo') . ' w Urzędzie Patentowym.'],
        ],
      ];

    case 'identity-national':
      return [
        'kind' => 'logo',
        'drop' => ['class="section manifesto"'],
        'h1' => 'Identyfikacja wizualna <strong class="gradient-text">i branding dla firm</strong>',
        'faq' => [
          ['new', 'Co obejmuje identyfikacja wizualna firmy?', 'Identyfikacja wizualna to system: <strong>logo i jego warianty, kolory, typografia, styl grafik i zdjęć</strong>, wzory materiałów firmowych oraz zasady ich użycia zebrane w księdze znaku. Dzięki niemu marka wygląda tak samo na stronie, w social mediach, w ofertach i w druku. Przy kampaniach system uzupełnia ' . $A('/logo-branding/key-visual-co-to-jest/', 'key visual kampanii') . ', czyli wspólny motyw reklam. Więcej w poradniku ' . $L('/logo-branding/identyfikacja-wizualna-firmy/', 'identyfikacja wizualna firmy: co zawiera') . '.'],
          ['keep', 2],
          ['new', 'Ile kosztuje identyfikacja wizualna?', 'Zakres zaczyna się od <strong>Mini Brandingu od 1499 zł</strong> (logo, kolory i typografia), a pełny system z księgą znaku to <strong>Branding Premium od 2999 zł</strong>. Dodatkowe materiały wyceniamy indywidualnie. Szczegóły opisujemy w poradniku ' . $L($cost_logo, 'ile kosztuje logo i identyfikacja wizualna') . '.'],
          ['keep', 4], ['keep', 6], ['keep', 7], ['keep', 11], ['keep', 12], ['keep', 14],
          ['new', 'Czy pracujecie z firmami z całej Polski?', 'Tak. Identyfikację wizualną projektujemy dla firm z całej Polski — warsztat marki, prezentacje i odbiory prowadzimy online. Z firmami ze Śląska spotykamy się też w naszym biurze w Katowicach.'],
        ],
        'replace' => [
          ['<p class="hero-eyebrow">Logo &amp; branding • Katowice</p>', '<p class="hero-eyebrow">Identyfikacja wizualna • branding</p>'],
          ['Tworzymy <strong>logo, identyfikacje wizualne i brandbooki</strong>, które dobrze wyglądają nie tylko na prezentacji. Projektujemy system gotowy na stronę internetową, social media, druk, opakowania i materiały sprzedażowe.', 'Projektujemy <strong>identyfikację wizualną firm: logo, kolory, typografię i księgę znaku</strong>. Budujemy system, dzięki któremu marka wygląda spójnie na stronie, w social mediach, w druku i w materiałach sprzedażowych — dla firm z całej Polski.'],
          ['<h2 class="section-title"> Wybierz zakres dopasowany do <strong>etapu Twojej marki.</strong> </h2>', '<h2 class="section-title">Ile kosztuje identyfikacja wizualna? <strong>Wybierz zakres.</strong></h2>'],
          ['z pełną księgą znaku i zasadami wdrożenia.', 'z pełną księgą znaku i zasadami wdrożenia. Ceny logo i identyfikacji wizualnej opisujemy w poradniku ' . $L($cost_logo, 'ile kosztuje identyfikacja wizualna') . '.'],
          ['<h2 class="section-title"> Logo otwiera system. <strong class="gradient-text" >Branding pilnuje spójności.</strong > </h2>', '<h2 class="section-title">Co obejmuje identyfikacja wizualna? <strong class="gradient-text">Cały system marki.</strong></h2>'],
          ['na dokumentach, wizytówkach, stronie i materiałach firmowych.', 'na dokumentach, wizytówkach, stronie i materiałach firmowych. Punktem wyjścia jest zawsze znak — zobacz, jak wygląda ' . $L('/projektowanie-logo/', 'projektowanie logo') . '.'],
          ['<h2 class="section-title"> Różne branże. <strong>Żadnego jednego stylu na wszystko.</strong> </h2>', '<h2 class="section-title">Realizacje brandingowe. <strong>Żadnego jednego stylu na wszystko.</strong></h2>'],
          ['<h2 class="section-title"> Od briefu do plików <strong>gotowych do wdrożenia.</strong> </h2>', '<h2 class="section-title">Proces: od strategii marki <strong>do wdrożenia.</strong></h2>'],
          ['<h2> System gotowy do pracy. <strong class="gradient-text">Nie pojedynczy plik.</strong> </h2>', '<h2>Księga znaku i brandbook. <strong class="gradient-text">System gotowy do pracy.</strong></h2>'],
          ['gotowe do przekazania drukarni, webmasterowi albo zespołowi marketingu.', 'gotowe do przekazania drukarni, webmasterowi albo zespołowi marketingu. Zobacz, co zawiera ' . $L('/logo-branding/ksiega-znaku-co-to-jest-co-zawiera-i-kiedy-firma-jej-potrzebuje/', 'księga znaku') . '.'],
          ['<p class="section-kicker">Katowice • Śląsk • cała Polska</p>', '<p class="section-kicker">Cała Polska • siedziba w Katowicach</p>'],
          ['Z Katowic projektujemy marki dla firm z całej Polski. Brief, prezentacje kierunków, konsultacje i przekazanie plików możemy przeprowadzić online — etap po etapie.', 'Projektujemy identyfikację wizualną dla firm z całej Polski. Warsztat marki, prezentacje kierunków, konsultacje i przekazanie plików prowadzimy online — etap po etapie. Firmy ze Śląska zapraszamy do biura: ' . $local('logo-katowice', 'branding w Katowicach') . '.'],
          ['<p class="section-kicker">FAQ / logo i branding</p>', '<p class="section-kicker">FAQ / identyfikacja wizualna</p>'],
          ['dzięki którym marka pozostaje spójna na stronie, w social mediach i druku.', 'dzięki którym marka pozostaje spójna na stronie, w social mediach i druku. Sam znak projektujemy w ramach usługi ' . $L('/projektowanie-logo/', 'projektowanie logo') . '.'],
          ['odpowiadały obecnemu etapowi rozwoju firmy.', 'odpowiadały obecnemu etapowi rozwoju firmy. Szczegóły w poradniku ' . $L('/logo-branding/rebranding-firmy/', 'rebranding firmy') . '.'],
        ],
      ];

    case 'logo-local':
      return [
        'kind' => 'logo',
        'drop' => ['class="section manifesto"', 'id="pakiety"', 'id="system"', 'id="proces"', 'id="pliki"'],
        'h1' => 'Projektowanie logo <strong class="gradient-text">i branding w Katowicach</strong>',
        'faq' => [
          ['keep', 1],
          ['keep', 3],
          ['new', 'Czy możemy zrobić warsztat marki w Katowicach?', 'Tak. Warsztat marki prowadzimy w naszym biurze przy <strong>ul. Modelarskiej 18/2 w Katowicach</strong>. Omawiamy charakter firmy, odbiorców, konkurencję i miejsca użycia znaku. Kolejne etapy — prezentacje i odbiory — mogą odbywać się w biurze albo online.'],
          ['keep', 8], ['keep', 9],
          ['new', 'Gdzie znajdę pełną ofertę logo i brandingu?', 'Pakiety, proces i pliki, które otrzymujesz, opisujemy na stronach ' . $L('/projektowanie-logo/', 'projektowanie logo') . ' oraz ' . $L('/identyfikacja-wizualna/', 'identyfikacja wizualna') . '.'],
        ],
        'replace' => [
          ['Tworzymy <strong>logo, identyfikacje wizualne i brandbooki</strong>, które dobrze wyglądają nie tylko na prezentacji. Projektujemy system gotowy na stronę internetową, social media, druk, opakowania i materiały sprzedażowe.', 'Studio z Katowic, ul. Modelarska 18/2. Projektujemy <strong>logo i identyfikację wizualną</strong> dla firm z Katowic i Śląska — z warsztatem marki w naszym biurze albo w pełni online.'],
          ['<p class="section-kicker">Katowice • Śląsk • cała Polska</p>', '<p class="section-kicker">Katowice • ul. Modelarska 18/2 • Śląsk</p>'],
          ['<h2 class="section-title"> Jedno studio. <strong>Współpraca bez granic lokalizacji.</strong> </h2>', '<h2 class="section-title">Warsztat marki <strong>w Katowicach.</strong></h2>'],
          ['Z Katowic projektujemy marki dla firm z całej Polski. Brief, prezentacje kierunków, konsultacje i przekazanie plików możemy przeprowadzić online — etap po etapie.', 'Na warsztat marki zapraszamy do naszego biura przy ul. Modelarskiej 18/2 w Katowicach. Pełną ofertę opisujemy na stronach ' . $L('/projektowanie-logo/', 'projektowanie logo') . ' i ' . $L('/identyfikacja-wizualna/', 'identyfikacja wizualna') . '.'],
          ['Możemy spotkać się lokalnie albo przeprowadzić cały proces online — od briefu po odbiór uporządkowanych plików.', 'Spotykamy się w naszym biurze przy ul. Modelarskiej 18/2 albo prowadzimy cały proces online — od briefu po odbiór uporządkowanych plików.'],
          ['Dodatkowe materiały, opakowania lub rozbudowane wdrożenie wyceniamy indywidualnie.', 'Dodatkowe materiały, opakowania lub rozbudowane wdrożenie wyceniamy indywidualnie. Więcej w poradniku ' . $L($cost_logo, 'ile kosztuje logo') . '.'],
        ],
      ];
  }
  return null;
}

/* ------------------------------------------------------------ page creation */

/**
 * Creates the nationwide pages as copies of their Katowice page (content, template and
 * Elementor layout). Existing pages are left alone. Returns log lines.
 */
function zp_seo_service_create_pages(): array {
  $log = [];
  $skip = ['_edit_lock', '_edit_last', '_wp_old_slug', '_wp_old_date', '_elementor_css', '_elementor_page_assets', '_elementor_element_cache', '_elementor_screenshot', '_wp_trash_meta_status', '_wp_trash_meta_time', '_zp_seo_plan_created'];
  foreach (zp_seo_service_registry() as $path => $r) {
    if (empty($r['source'])) { continue; }
    $slug = trim($path, '/');
    $existing = get_page_by_path($slug, OBJECT, 'page');
    if ($existing) {
      if ($existing->post_status !== 'publish' && get_post_meta($existing->ID, '_zp_seo_plan_created', true)) {
        wp_update_post(['ID' => $existing->ID, 'post_status' => 'publish']);
        $log[] = 'Opublikowano ponownie stronę ' . $path;
      } else {
        $log[] = 'Strona ' . $path . ' już istnieje (ID ' . $existing->ID . ') — bez zmian.';
      }
      continue;
    }
    $source = get_page_by_path(trim($r['source'], '/'), OBJECT, 'page');
    $content = $source ? (string) $source->post_content : '';
    if (strpos($content, '[' . $r['shortcode']) === false && !preg_match('~\[zp_page_(?:strony|sklepy|logo_branding)~', $content)) {
      $content = "[zp_header]\n[" . $r['shortcode'] . "]\n[zp_footer]";
    }
    $id = wp_insert_post(wp_slash([
      'post_type' => 'page', 'post_status' => 'publish', 'post_title' => $r['title'], 'post_name' => $slug,
      'post_content' => $content, 'post_author' => $source ? (int) $source->post_author : get_current_user_id(),
      'comment_status' => 'closed', 'ping_status' => 'closed',
    ]), true);
    if (is_wp_error($id) || !$id) {
      $log[] = 'BŁĄD: nie udało się utworzyć ' . $path . (is_wp_error($id) ? ' — ' . $id->get_error_message() : '');
      continue;
    }
    if ($source) {
      foreach (get_post_meta($source->ID) as $key => $values) {
        if (in_array($key, $skip, true) || strpos($key, 'rank_math_') === 0 || strpos($key, '_yoast_') === 0) { continue; }
        foreach ((array) $values as $value) { add_post_meta($id, $key, wp_slash(maybe_unserialize($value))); }
      }
    }
    update_post_meta($id, '_zp_seo_plan_created', ZP_SEO_PLAN_VERSION);
    $real = zp_seo_plan_path((string) get_permalink($id));
    $log[] = 'Utworzono stronę ' . $path . ' (ID ' . $id . ($source ? ', kopia ' . $r['source'] : ', prosty układ') . ')' . ($real !== $path ? ' — UWAGA: adres to ' . $real : '');
  }
  return $log;
}
