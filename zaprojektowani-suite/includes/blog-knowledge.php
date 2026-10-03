<?php
if (!defined('ABSPATH')) { exit; }

function zp_suite_blog_categories(){
  return [
    'Poradniki' => 'poradniki',
    'Strony internetowe' => 'strony-internetowe',
    'Sklepy internetowe' => 'sklepy-internetowe',
    'Logo i branding' => 'logo-branding',
    'SEO' => 'seo',
    'Reklamy' => 'reklamy',
    'Case study' => 'case-study',
  ];
}

/**
 * Curated topic clusters used by /wiedza/.
 *
 * We intentionally group legacy/duplicate category slugs under a single
 * user-facing topic. This keeps the knowledge hub understandable even before
 * old categories are manually consolidated in WordPress.
 */
function zp_suite_wiedza_topic_groups(){
  return [
    'strony-internetowe' => [
      'label' => 'Strony internetowe',
      'slugs' => ['strony-internetowe','strony-www'],
      'description' => 'Planowanie strony, UX, struktura oferty, landing page, WordPress i konwersja.',
      'service_url' => home_url('/strony-internetowe-katowice/'),
    ],
    'sklepy-internetowe' => [
      'label' => 'Sklepy internetowe',
      'slugs' => ['sklepy-internetowe'],
      'description' => 'WooCommerce, architektura sklepu, koszyk, checkout, płatności i rozwój sprzedaży.',
      'service_url' => home_url('/sklepy-internetowe-katowice/'),
    ],
    'logo-branding' => [
      'label' => 'Logo i branding',
      'slugs' => ['logo-branding','logo-i-branding'],
      'description' => 'Logo, identyfikacja wizualna, księga znaku, brandbook i rebranding.',
      'service_url' => home_url('/logo-branding-katowice/'),
    ],
    'seo-konwersja' => [
      'label' => 'SEO i konwersja',
      'slugs' => ['seo','seo-i-konwersja','seo-content-marketing','ux-cro-analityka'],
      'description' => 'Widoczność w Google, content, architektura informacji, CRO, analityka i jakość leadów.',
      'service_url' => home_url('/wiedza/'),
    ],
    'kampanie-reklamowe' => [
      'label' => 'Kampanie reklamowe',
      'slugs' => ['reklamy','meta-ads','kampanie-reklamowe-meta-ads'],
      'description' => 'Meta Ads, przygotowanie strony pod kampanie, landing page i pomiar zapytań.',
      'service_url' => home_url('/kampanie-reklamowe/'),
    ],
    'case-study' => [
      'label' => 'Case study',
      'slugs' => ['case-study'],
      'description' => 'Przykłady wdrożeń, decyzji projektowych i połączenia designu z wynikiem biznesowym.',
      'service_url' => home_url('/realizacje/'),
    ],
  ];
}

function zp_suite_wiedza_topic_groups_resolved(){
  static $memo = null;
  if (is_array($memo)) { return $memo; }

  $out = [];
  foreach (zp_suite_wiedza_topic_groups() as $key => $group) {
    $term_ids = [];
    $archive_url = '';
    $primary_term = null;

    foreach ((array)($group['slugs'] ?? []) as $slug) {
      $term = get_term_by('slug', $slug, 'category');
      if (!$term || is_wp_error($term)) { continue; }
      $term_ids[] = (int) $term->term_id;
      if (!$primary_term) { $primary_term = $term; }
      if ($archive_url === '') {
        $link = get_category_link($term);
        if (!is_wp_error($link)) { $archive_url = $link; }
      }
    }

    $term_ids = array_values(array_unique(array_filter(array_map('intval', $term_ids))));
    if (!$term_ids) { continue; }

    /* Count unique published posts, not the sum of term->count values. A post can
       belong to two legacy categories from the same topic and must count once. */
    $ids = get_posts([
      'post_type'              => 'post',
      'post_status'            => 'publish',
      'posts_per_page'         => -1,
      'fields'                 => 'ids',
      'category__in'           => $term_ids,
      'orderby'                => 'ID',
      'order'                  => 'ASC',
      'ignore_sticky_posts'    => true,
      'no_found_rows'          => true,
      'suppress_filters'       => false,
      'update_post_meta_cache' => false,
      'update_post_term_cache' => false,
    ]);
    $count = count(array_unique(array_map('intval', (array) $ids)));
    if ($count < 1) { continue; }

    $out[$key] = array_merge($group, [
      'term_ids' => $term_ids,
      'count' => $count,
      'url' => $archive_url ?: home_url('/wiedza/'),
      'primary_term_id' => $primary_term ? (int) $primary_term->term_id : 0,
      'primary_slug' => $primary_term ? (string) $primary_term->slug : '',
    ]);
  }
  return $memo = $out;
}

function zp_suite_wiedza_group_for_term($term){
  if (!$term || is_wp_error($term) || empty($term->term_id)) { return null; }
  $term_id = (int) $term->term_id;
  foreach (zp_suite_wiedza_topic_groups_resolved() as $key => $group) {
    if (in_array($term_id, (array)($group['term_ids'] ?? []), true)) {
      $group['key'] = $key;
      return $group;
    }
  }
  return null;
}

function zp_suite_wiedza_filter_term_ids($filter){
  $filter = sanitize_key((string) $filter);
  $groups = zp_suite_wiedza_topic_groups_resolved();
  if (isset($groups[$filter])) {
    return (array) ($groups[$filter]['term_ids'] ?? []);
  }
  $term = get_term_by('slug', $filter, 'category');
  return ($term && !is_wp_error($term)) ? [(int)$term->term_id] : [];
}

function zp_suite_blog_seed_posts(){
  return [
    [
      'title'=>'Ile kosztuje strona internetowa w 2026 roku i od czego realnie zależy cena?',
      'cat'=>['Poradniki','Strony internetowe'],
      'img'=>'https://zaprojektowani.com/wp-content/uploads/2026/05/proscarves_realizacja_nowa.webp',
      'excerpt'=>'Konkretny przewodnik po wycenie strony: zakres, projekt graficzny, liczba podstron, SEO, copywriting, integracje i elementy, które zwykle podnoszą koszt realizacji.',
      'read'=>'9 min',
      'tag'=>'Poradnik miesiąca',
    ],
    [
      'title'=>'Strona firmowa czy landing page — co wybrać, gdy zależy Ci na zapytaniach?',
      'cat'=>['Strony internetowe','Poradniki'],
      'img'=>'https://zaprojektowani.com/wp-content/uploads/2026/05/siemianowski_realizacja_nowa.webp',
      'excerpt'=>'Różnice między stroną firmową a landing page’em, przykłady zastosowań oraz szybka checklista wyboru pod kampanię, SEO i sprzedaż usług.',
      'read'=>'7 min',
      'tag'=>'WWW',
    ],
    [
      'title'=>'Sklep internetowy, który sprzedaje — 12 elementów, których nie warto pomijać',
      'cat'=>['Sklepy internetowe','Poradniki'],
      'img'=>'https://zaprojektowani.com/wp-content/uploads/2026/05/EL-FUEGO-2.webp',
      'excerpt'=>'Od karty produktu i koszyka po płatności, dostawy, remarketing i analitykę. Zobacz, co powinno znaleźć się w nowoczesnym sklepie WooCommerce.',
      'read'=>'10 min',
      'tag'=>'E-commerce',
    ],
    [
      'title'=>'Logo premium bez przesady — jak zaprojektować znak, który nie zestarzeje się po sezonie?',
      'cat'=>['Logo i branding','Poradniki'],
      'img'=>'https://zaprojektowani.com/wp-content/uploads/2026/06/zp_sygnet_ciemny-scaled.webp',
      'excerpt'=>'O prostocie, typografii, sygnetach, wariantach kolorystycznych i tym, dlaczego dobry branding musi działać na stronie, wizytówce i social mediach.',
      'read'=>'8 min',
      'tag'=>'Branding',
    ],
    [
      'title'=>'SEO dla strony usługowej — jak ułożyć strukturę, żeby Google rozumiało ofertę?',
      'cat'=>['SEO','Strony internetowe'],
      'img'=>'https://zaprojektowani.com/wp-content/uploads/2026/05/strony_internetowe_dla_prawnikow.webp',
      'excerpt'=>'Praktyczny schemat: strona główna, podstrony usług, lokalizacje, FAQ, linkowanie wewnętrzne i treści, które nie brzmią jak sztuczne upychanie fraz.',
      'read'=>'11 min',
      'tag'=>'SEO',
    ],
    [
      'title'=>'Meta Ads dla usług lokalnych — jak przygotować stronę, żeby kampania miała sens?',
      'cat'=>['Reklamy','Poradniki'],
      'img'=>'https://zaprojektowani.com/wp-content/uploads/2026/05/zaprojektowani_kontakt.webp',
      'excerpt'=>'Reklama nie naprawi słabej strony. Sprawdź, jak dopracować komunikat, formularz, CTA, social proof i landing page przed uruchomieniem kampanii.',
      'read'=>'6 min',
      'tag'=>'Meta Ads',
    ],
    [
      'title'=>'Brief do strony internetowej — co przygotować, żeby projekt ruszył szybciej?',
      'cat'=>['Poradniki','Strony internetowe'],
      'img'=>'https://zaprojektowani.com/wp-content/uploads/2026/05/proscarves_web5.webp',
      'excerpt'=>'Lista materiałów, inspiracji i decyzji, które skracają etap startowy projektu: oferta, grupy klientów, zdjęcia, konkurencja, funkcje i priorytety.',
      'read'=>'5 min',
      'tag'=>'Proces',
    ],
    [
      'title'=>'Case study ProScarves — sklep detaliczny i zapytania B2B w jednym systemie',
      'cat'=>['Case study','Sklepy internetowe'],
      'img'=>'https://zaprojektowani.com/wp-content/uploads/2026/05/proscarves_realizacja_nowa.webp',
      'excerpt'=>'Jak można połączyć gotowe produkty dla fanów z systemem zapytań hurtowych dla klubów, organizacji i firm zamawiających customowe produkty.',
      'read'=>'8 min',
      'tag'=>'Case study',
    ],
    [
      'title'=>'Case study Siemianowski — elegancka strona kancelarii i system budowania zaufania',
      'cat'=>['Case study','Strony internetowe'],
      'img'=>'https://zaprojektowani.com/wp-content/uploads/2026/05/siemianowski_realizacja_nowa.webp',
      'excerpt'=>'O tym, jak strona kancelarii może prowadzić użytkownika przez specjalizacje, doświadczenie, kontakt i umawianie wizyt bez krzykliwej sprzedaży.',
      'read'=>'7 min',
      'tag'=>'Case study',
    ],
    [
      'title'=>'Jak pisać teksty na stronę, żeby nie brzmiały jak katalog usług?',
      'cat'=>['Poradniki','SEO'],
      'img'=>'https://zaprojektowani.com/wp-content/uploads/2026/05/strona_internetowa_dla_branzy_beauty.webp',
      'excerpt'=>'Prosty sposób na lepsze copy: mniej ogólników, więcej konkretu, jasne nagłówki, krótsze akapity i treści dopasowane do realnych pytań klientów.',
      'read'=>'6 min',
      'tag'=>'Copywriting',
    ],
  ];
}

function zp_suite_blog_create_demo_posts(){
  if (!function_exists('wp_insert_post')) { return; }

  foreach (zp_suite_blog_categories() as $name => $slug) {
    if (!term_exists($slug, 'category')) {
      wp_insert_term($name, 'category', ['slug'=>$slug]);
    }
  }

  foreach (zp_suite_blog_seed_posts() as $item) {
    $exists = get_page_by_title($item['title'], OBJECT, 'post');
    if ($exists && !empty($exists->ID)) {
      update_post_meta($exists->ID, '_zp_blog_img', esc_url_raw($item['img']));
      update_post_meta($exists->ID, '_zp_blog_read', sanitize_text_field($item['read']));
      update_post_meta($exists->ID, '_zp_blog_tag', sanitize_text_field($item['tag']));
      continue;
    }

    $term_ids = [];
    foreach ($item['cat'] as $catName) {
      $term = term_exists(zp_suite_blog_categories()[$catName] ?? sanitize_title($catName), 'category');
      if (is_array($term) && !empty($term['term_id'])) $term_ids[] = (int)$term['term_id'];
    }

    $content = zp_suite_blog_demo_post_content($item);
    $post_id = wp_insert_post([
      'post_title' => wp_strip_all_tags($item['title']),
      'post_status' => 'publish',
      'post_type' => 'post',
      'post_excerpt' => $item['excerpt'],
      'post_content' => $content,
      'post_category' => $term_ids,
    ], true);

    if (!is_wp_error($post_id) && $post_id) {
      update_post_meta($post_id, '_zp_blog_img', esc_url_raw($item['img']));
      update_post_meta($post_id, '_zp_blog_read', sanitize_text_field($item['read']));
      update_post_meta($post_id, '_zp_blog_tag', sanitize_text_field($item['tag']));
      update_post_meta($post_id, '_zp_demo_post', '1');
    }
  }

  /* The option name already versions this seed schema. Do not tie its value to
     the plugin release: a frontend-only version bump must never re-seed or
     overwrite metadata on existing articles. */
  update_option('zp_suite_blog_demo_seeded_v2', '1', false);
}

function zp_suite_blog_demo_post_content($item){
  $title = esc_html($item['title']);
  $img = esc_url($item['img']);
  $excerpt = esc_html($item['excerpt']);
  return '<article class="zpEd"><section class="zpEd__hero"><p><strong>Materiał przykładowy / baza do edycji.</strong></p><h1>'.$title.'</h1><p>'.$excerpt.'</p></section><figure><img src="'.$img.'" alt="'.$title.'" style="width:100%;height:auto;border-radius:28px;" loading="lazy" decoding="async"></figure><h2>Wprowadzenie</h2><p>Ten przykładowy wpis pokazuje kierunek treści dla bazy wiedzy Zaprojektowani.com. Docelowo możesz rozbudować go o własne przykłady, screeny z realizacji, tabele, FAQ, linkowanie wewnętrzne oraz konkretne rekomendacje dla klientów.</p><h2>Najważniejsze elementy</h2><ul><li>jasny problem użytkownika i konkretna odpowiedź,</li><li>nagłówki ułożone pod SEO i czytelność,</li><li>przykłady z projektów stron, sklepów, brandingu lub kampanii,</li><li>wezwanie do kontaktu albo przejścia do studia wyceny.</li></ul><blockquote><p>Dobra treść poradnikowa nie ma tylko zbierać wejść z Google. Ma pomagać klientowi zrozumieć decyzję i skracać drogę do kontaktu.</p></blockquote><h2>Co dalej?</h2><p>Rozbuduj wpis o case study, checklistę, FAQ oraz linki do usług. Wpisy dodawane przez WordPress będą automatycznie pojawiały się na stronie Wiedza w shortcodzie <code>[zp_wiedza_blog]</code>.</p></article>';
}

function zp_suite_blog_maybe_seed(){
  if (!is_admin()) { return; }
  /* Older releases stored their full plugin version here. Any non-empty value
     is a valid v2 marker and must remain accepted during upgrades. */
  if ((string) get_option('zp_suite_blog_demo_seeded_v2', '') !== '') { return; }
  zp_suite_blog_create_demo_posts();
}
add_action('admin_init', 'zp_suite_blog_maybe_seed', 20);

function zp_suite_blog_activate(){
  zp_suite_blog_create_demo_posts();
}

function zp_suite_blog_card_image($post_id){
  $post_id = (int) $post_id;

  // Najpierw zawsze pobieramy obrazek wyróżniający wpisu.
  // Dzięki temu miniatury w [zp_wiedza_blog] są dokładnie tymi zdjęciami,
  // które ustawisz w panelu WordPress przy konkretnym artykule.
  if (has_post_thumbnail($post_id)) {
    $thumb = get_the_post_thumbnail_url($post_id, 'full');
    if ($thumb) {
      return esc_url(function_exists('zp_suite_2260_full_upload_image_url') ? zp_suite_2260_full_upload_image_url($thumb) : $thumb);
    }
  }

  // Meta zostaje tylko jako fallback dla starszych/demo wpisów, które nie mają jeszcze obrazka wyróżniającego.
  $img = get_post_meta($post_id, '_zp_blog_img', true);
  if ($img) return esc_url($img);

  return esc_url('https://zaprojektowani.com/wp-content/uploads/2026/05/proscarves_web1.webp');
}

/**
 * Responsive image used by the knowledge index.
 *
 * Featured images keep their WordPress dimensions/srcset, so cards no longer
 * download the original upload when a 300-1000 px derivative is enough. The
 * legacy URL remains a fallback for demo posts without an attachment.
 */
function zp_suite_wiedza_image_markup($post_id, $size, $class, $alt, $sizes){
  $post_id = (int) $post_id;
  $attachment_id = get_post_thumbnail_id($post_id);
  $attrs = [
    'class'         => (string) $class,
    'alt'           => (string) $alt,
    'loading'       => 'lazy',
    'decoding'      => 'async',
    'fetchpriority' => 'low',
    'sizes'         => (string) $sizes,
  ];

  if ($attachment_id) {
    $markup = wp_get_attachment_image($attachment_id, $size, false, $attrs);
    if ($markup) { return $markup; }
  }

  return sprintf(
    '<img class="%1$s" src="%2$s" alt="%3$s" loading="lazy" decoding="async" fetchpriority="low" sizes="%4$s">',
    esc_attr($class),
    esc_url(zp_suite_blog_card_image($post_id)),
    esc_attr($alt),
    esc_attr($sizes)
  );
}

/**
 * Natural excerpt for Wiedza cards.
 *
 * v2.2.772: the excerpt is selected from THIS post only and additionally
 * checked against the current post title/category. This prevents a copied or
 * misplaced intro from another article from leaking into a card. Manual
 * WordPress excerpts are only used as a relevance-checked fallback.
 */
function zp_suite_wiedza_excerpt_text($value){
  $value = strip_shortcodes((string) $value);
  $value = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, get_bloginfo('charset') ?: 'UTF-8');
  $value = wp_strip_all_tags($value, true);
  $value = preg_replace('/\x{00A0}/u', ' ', $value);
  $value = preg_replace('/\s+/u', ' ', $value);
  return trim((string) $value);
}

function zp_suite_wiedza_excerpt_word_count($text){
  $parts = preg_split('/\s+/u', trim((string) $text), -1, PREG_SPLIT_NO_EMPTY);
  return is_array($parts) ? count($parts) : 0;
}

/** Normalize copy for lightweight relevance matching. */
function zp_suite_wiedza_excerpt_normalize($text){
  $text = zp_suite_wiedza_excerpt_text($text);
  if ($text === '') { return ''; }
  $text = remove_accents($text);
  $text = function_exists('mb_strtolower') ? mb_strtolower($text, 'UTF-8') : strtolower($text);
  $text = preg_replace('/[^a-z0-9]+/u', ' ', $text);
  $text = preg_replace('/\s+/u', ' ', $text);
  return trim((string) $text);
}

function zp_suite_wiedza_excerpt_tokens($text){
  $plain = zp_suite_wiedza_excerpt_normalize($text);
  if ($plain === '') { return []; }

  static $stop = null;
  if ($stop === null) {
    $stop = array_fill_keys([
      'a','aby','albo','ale','ani','bo','by','byc','czy','dla','do','gdy','gdzie','i','ich','jak','jaka','jaki','jakie','jest','jesli','juz','ktora','ktore','ktory','lub','ma','maja','moze','na','nad','nie','o','od','oraz','po','pod','przed','przez','sa','sie','tak','ten','ta','te','to','tego','tej','tym','w','we','z','za','ze','bez','co','czym','kiedy','dlaczego','ile','najlepiej','warto','powinien','powinna','powinno','mozna','swoje','swoim','swoich','firmy','firma','marki','marka'
    ], true);
  }

  $parts = preg_split('/\s+/u', $plain, -1, PREG_SPLIT_NO_EMPTY);
  $tokens = [];
  foreach ((array) $parts as $part) {
    if (isset($stop[$part])) { continue; }
    $len = strlen($part);
    if ($len < 4 && !in_array($part, ['ai','ux','ui','seo','sem','ads','www','b2b','b2c'], true)) { continue; }
    $tokens[$part] = true;
  }
  return array_keys($tokens);
}

/** Polish-friendly approximate token match without a heavy stemmer. */
function zp_suite_wiedza_excerpt_token_matches($needle, $haystack){
  $needle = (string) $needle;
  $haystack = (string) $haystack;
  if ($needle === '' || $haystack === '') { return false; }
  if ($needle === $haystack) { return true; }

  $min = min(strlen($needle), strlen($haystack));
  if ($min <= 3) { return false; }
  $prefix = $min >= 7 ? 6 : ($min >= 5 ? 5 : 4);
  return substr($needle, 0, $prefix) === substr($haystack, 0, $prefix);
}

function zp_suite_wiedza_excerpt_match_count($needles, $haystack_tokens){
  $count = 0;
  foreach ((array) $needles as $needle) {
    foreach ((array) $haystack_tokens as $token) {
      if (zp_suite_wiedza_excerpt_token_matches($needle, $token)) {
        $count++;
        break;
      }
    }
  }
  return $count;
}

function zp_suite_wiedza_excerpt_is_noise($text, $title = ''){
  $text = trim((string) $text);
  if ($text === '') { return true; }

  $plain = zp_suite_wiedza_excerpt_normalize($text);

  if (preg_match('/^(ostatnia\s+aktualizacja|aktualizacja|data\s+aktualizacji|opublikowano|data\s+publikacji|spis\s+tresci|w\s+tym\s+artykule|w\s+tym\s+poradniku)\b/u', $plain)) {
    return true;
  }

  if (preg_match('/^(?:\d{1,2}[.\/-]){2}\d{2,4}(?:\s|$)/u', $plain) || preg_match('/^\d{4}-\d{2}-\d{2}(?:\s|$)/u', $plain)) {
    return true;
  }

  $words = zp_suite_wiedza_excerpt_word_count($text);
  $chars = function_exists('mb_strlen') ? mb_strlen($text, 'UTF-8') : strlen($text);
  if ($words < 10 || $chars < 72) { return true; }

  if ($title !== '') {
    $title_plain = zp_suite_wiedza_excerpt_normalize($title);
    if ($title_plain !== '') {
      if ($plain === $title_plain) { return true; }
      if (strpos($plain, $title_plain) === 0 && $words < (zp_suite_wiedza_excerpt_word_count($title) + 12)) { return true; }
    }
  }

  return false;
}

/**
 * Returns a relevance score + match counters. We intentionally give more
 * weight to title-specific terms than broad category words (e.g. "sklep").
 */
function zp_suite_wiedza_excerpt_relevance($text, $title, $category_labels = []){
  $candidate_tokens = zp_suite_wiedza_excerpt_tokens($text);
  if (!$candidate_tokens) {
    return ['score'=>-999, 'specific'=>0, 'title'=>0, 'category'=>0];
  }

  $title_tokens = zp_suite_wiedza_excerpt_tokens($title);
  $category_tokens = [];
  foreach ((array) $category_labels as $label) {
    $category_tokens = array_merge($category_tokens, zp_suite_wiedza_excerpt_tokens($label));
  }
  $category_tokens = array_values(array_unique($category_tokens));

  static $generic = null;
  if ($generic === null) {
    $generic = [
      'strona','strony','internetowa','internetowy','internetowym','www','sklep','sklepu','sklepie','internetowego','logo','branding','identyfikacja','wizualna','google','ads','kampania','kampanii','reklamowa','reklamowe','seo','poradnik','poradniki'
    ];
  }

  $specific = [];
  foreach ($title_tokens as $token) {
    $is_generic = false;
    foreach ($generic as $g) {
      if (zp_suite_wiedza_excerpt_token_matches($token, $g)) { $is_generic = true; break; }
    }
    if (!$is_generic) {
      foreach ($category_tokens as $ct) {
        if (zp_suite_wiedza_excerpt_token_matches($token, $ct)) { $is_generic = true; break; }
      }
    }
    if (!$is_generic) { $specific[] = $token; }
  }
  if (!$specific) { $specific = $title_tokens; }

  $specific_matches = zp_suite_wiedza_excerpt_match_count($specific, $candidate_tokens);
  $title_matches = zp_suite_wiedza_excerpt_match_count($title_tokens, $candidate_tokens);
  $category_matches = zp_suite_wiedza_excerpt_match_count($category_tokens, $candidate_tokens);

  $score = ($specific_matches * 6) + ($title_matches * 2) + $category_matches;

  $plain_text = zp_suite_wiedza_excerpt_normalize($text);
  $plain_title = zp_suite_wiedza_excerpt_normalize($title);
  if ($plain_title !== '' && strpos($plain_text, $plain_title) === 0) { $score += 5; }

  return [
    'score'    => $score,
    'specific' => $specific_matches,
    'title'    => $title_matches,
    'category' => $category_matches,
  ];
}

function zp_suite_wiedza_excerpt_is_relevant($rel){
  if (!is_array($rel)) { return false; }
  if (($rel['specific'] ?? 0) >= 1) { return true; }
  if (($rel['title'] ?? 0) >= 2 && ($rel['score'] ?? 0) >= 5) { return true; }
  return false;
}

function zp_suite_wiedza_excerpt_finish_sentence($text, $target_words){
  $text = trim((string) $text);
  $target_words = max(8, (int) $target_words);
  if ($text === '') { return ''; }

  $sentences = preg_split('/(?<=[.!?…])\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY);
  if (!is_array($sentences) || count($sentences) < 2) {
    return wp_trim_words($text, $target_words, '…');
  }

  $picked = [];
  $count = 0;
  $soft_min = max(12, (int) floor($target_words * 0.72));
  $hard_max = max($target_words + 12, (int) ceil($target_words * 1.35));

  foreach ($sentences as $sentence) {
    $sentence = trim((string) $sentence);
    if ($sentence === '') { continue; }
    $sentence_words = zp_suite_wiedza_excerpt_word_count($sentence);
    if ($picked && $count >= $soft_min && ($count + $sentence_words) > $hard_max) { break; }
    $picked[] = $sentence;
    $count += $sentence_words;
    if ($count >= $target_words) { break; }
  }

  $result = trim(implode(' ', $picked));
  if ($result === '') { return wp_trim_words($text, $target_words, '…'); }
  if (zp_suite_wiedza_excerpt_word_count($result) > $hard_max) {
    return wp_trim_words($result, $target_words, '…');
  }
  return $result;
}

/** Safe title-based fallback: never shows copy from an obviously different article. */
function zp_suite_wiedza_excerpt_safe_fallback($title){
  $title = trim(zp_suite_wiedza_excerpt_text($title));
  if ($title === '') { return ''; }
  return 'Praktyczny poradnik: ' . rtrim($title, '.?!…') . '. Zobacz najważniejsze informacje, przykłady i elementy, które warto sprawdzić przed podjęciem decyzji lub wdrożeniem.';
}

function zp_suite_wiedza_fast_excerpt($post_id, $words = 34){
  static $memo = [];
  $post_id = (int) $post_id;
  $words = max(8, (int) $words);
  $key = $post_id . ':' . $words;
  if (array_key_exists($key, $memo)) { return $memo[$key]; }

  $post = get_post($post_id);
  if (!$post || (int) $post->ID !== $post_id) { return $memo[$key] = ''; }

  /* Read only fields from the exact post ID passed into the card. */
  $raw = (string) get_post_field('post_content', $post_id, 'raw');
  $title = (string) get_the_title($post_id);
  $cats = get_the_category($post_id);
  $category_labels = [];
  if (!empty($cats) && !is_wp_error($cats)) {
    foreach ($cats as $cat) { $category_labels[] = (string) $cat->name; }
  }

  $candidates = [];
  if ($raw !== '' && preg_match_all('~<p\b[^>]*>(.*?)</p>~is', $raw, $matches)) {
    foreach ($matches[1] as $position => $paragraph) {
      $candidate = zp_suite_wiedza_excerpt_text($paragraph);
      if (zp_suite_wiedza_excerpt_is_noise($candidate, $title)) { continue; }

      $rel = zp_suite_wiedza_excerpt_relevance($candidate, $title, $category_labels);
      $candidates[] = [
        'text'     => $candidate,
        'score'    => (float) ($rel['score'] ?? 0) - (min((int)$position, 12) * 0.08),
        'relevant' => zp_suite_wiedza_excerpt_is_relevant($rel),
        'position' => (int) $position,
      ];

      /* 24 real paragraphs are plenty and keep archive rendering cheap. */
      if (count($candidates) >= 24) { break; }
    }
  }

  /* Classic editor / unusual markup fallback, still from this exact post. */
  if (!$candidates && $raw !== '') {
    $fallback = preg_replace('~<(script|style|nav|figure|table|form|aside)\b[^>]*>.*?</\1>~is', ' ', $raw);
    $fallback = preg_replace('~<h[1-6]\b[^>]*>.*?</h[1-6]>~is', ' ', $fallback);
    $fallback = zp_suite_wiedza_excerpt_text($fallback);
    $fallback = preg_replace('/^(?:ostatnia\s+aktualizacja|aktualizacja)\s*:?\s*\d{4}[-.\/]\d{1,2}[-.\/]\d{1,2}\s*/iu', '', $fallback);
    if (!zp_suite_wiedza_excerpt_is_noise($fallback, $title)) {
      $rel = zp_suite_wiedza_excerpt_relevance($fallback, $title, $category_labels);
      $candidates[] = ['text'=>$fallback, 'score'=>(float)($rel['score'] ?? 0), 'relevant'=>zp_suite_wiedza_excerpt_is_relevant($rel), 'position'=>0];
    }
  }

  /* Pick the best title-relevant paragraph, not simply the first <p>. */
  $relevant = array_values(array_filter($candidates, static function($item){ return !empty($item['relevant']); }));
  if ($relevant) {
    usort($relevant, static function($a, $b){
      if ($a['score'] === $b['score']) { return $a['position'] <=> $b['position']; }
      return ($a['score'] > $b['score']) ? -1 : 1;
    });

    $text = (string) $relevant[0]['text'];
    $min_for_single = max(18, (int) floor($words * 0.72));

    /* Join the next nearby relevant paragraph only when the first one is short. */
    if (zp_suite_wiedza_excerpt_word_count($text) < $min_for_single) {
      $first_pos = (int) $relevant[0]['position'];
      foreach ($relevant as $idx => $item) {
        if ($idx === 0) { continue; }
        if ((int)$item['position'] <= $first_pos || ((int)$item['position'] - $first_pos) > 3) { continue; }
        $text .= ' ' . (string) $item['text'];
        if (zp_suite_wiedza_excerpt_word_count($text) >= $min_for_single) { break; }
      }
    }

    return $memo[$key] = zp_suite_wiedza_excerpt_finish_sentence($text, $words);
  }

  /*
   * If body copy is clearly unrelated to the current title (e.g. a duplicated
   * article body), try SEO/manual copy only if it passes the same relevance test.
   */
  $fallbacks = [];
  $manual_excerpt = trim((string) get_post_field('post_excerpt', $post_id, 'raw'));
  if ($manual_excerpt !== '') { $fallbacks[] = $manual_excerpt; }

  foreach (['rank_math_description', '_yoast_wpseo_metadesc'] as $meta_key) {
    $meta = trim((string) get_post_meta($post_id, $meta_key, true));
    if ($meta !== '') { $fallbacks[] = $meta; }
  }

  foreach ($fallbacks as $fallback) {
    $fallback = zp_suite_wiedza_excerpt_text($fallback);
    if (zp_suite_wiedza_excerpt_is_noise($fallback, $title)) { continue; }
    $rel = zp_suite_wiedza_excerpt_relevance($fallback, $title, $category_labels);
    if (!zp_suite_wiedza_excerpt_is_relevant($rel)) { continue; }
    return $memo[$key] = zp_suite_wiedza_excerpt_finish_sentence($fallback, $words);
  }

  /* Never display obviously foreign article copy in a card. */
  return $memo[$key] = zp_suite_wiedza_excerpt_safe_fallback($title);
}


function zp_suite_wiedza_read_time($post_id){
  static $memo = [];
  $post_id = (int) $post_id;
  if (array_key_exists($post_id, $memo)) { return $memo[$post_id]; }
  $read = get_post_meta($post_id, '_zp_blog_read', true);
  if ($read) { return $memo[$post_id] = sanitize_text_field($read); }
  $words = str_word_count(wp_strip_all_tags(get_post_field('post_content', $post_id)));
  return $memo[$post_id] = max(3, (int)ceil($words / 220)) . ' min';
}

function zp_suite_wiedza_read_count($post_id){
  $post_id = (int) $post_id;
  if (!$post_id) { return 0; }
  $reads = (int) get_post_meta($post_id, '_zp_post_reads', true);
  if ($reads < 1) {
    /* Backwards-compatible fallback if an older installation already stored
       page views under the legacy key. */
    $reads = (int) get_post_meta($post_id, '_zp_post_views', true);
  }
  return max(0, $reads);
}

function zp_suite_wiedza_read_label($count){
  $count = max(0, (int) $count);
  if ($count < 1) { return 'Nowy wpis'; }
  $mod100 = $count % 100;
  $mod10 = $count % 10;
  if ($count === 1) { $word = 'czytanie'; }
  elseif ($mod10 >= 2 && $mod10 <= 4 && !($mod100 >= 12 && $mod100 <= 14)) { $word = 'czytania'; }
  else { $word = 'czytań'; }
  return number_format_i18n($count) . ' ' . $word;
}

function zp_suite_wiedza_card_markup($post_id, $i = 0){
  $post_id = (int)$post_id;
  $cats = get_the_category($post_id);
  $cat_slugs = (!empty($cats) && !is_wp_error($cats)) ? wp_list_pluck($cats, 'slug') : [];
  $first_cat = (!empty($cats) && !is_wp_error($cats)) ? $cats[0]->name : 'Wiedza';
  $read = zp_suite_wiedza_read_time($post_id);
  $tag = get_post_meta($post_id,'_zp_blog_tag',true) ?: $first_cat;
  $title = get_the_title($post_id);
  $excerpt = zp_suite_wiedza_fast_excerpt($post_id, 42);
  $reads = zp_suite_wiedza_read_count($post_id);
  $views_label = zp_suite_wiedza_read_label($reads);
  $cat_pills = [];
  if (!empty($cats) && !is_wp_error($cats)) {
    foreach (array_slice($cats, 0, 3) as $c) { $cat_pills[] = $c->name; }
  }
  if (empty($cat_pills)) { $cat_pills[] = $tag; }
  ob_start();
  ?>
  <article class="zpKBPost" style="--i:<?php echo (int)$i; ?>" data-zp-post data-title="<?php echo esc_attr(mb_strtolower($title.' '.$excerpt.' '.$first_cat)); ?>" data-cats="<?php echo esc_attr(implode(' ', $cat_slugs)); ?>" data-date="<?php echo esc_attr(get_the_date('U',$post_id)); ?>">
    <a class="zpKBPost__media" href="<?php echo esc_url(get_permalink($post_id)); ?>" aria-label="<?php echo esc_attr($title); ?>">
      <?php echo zp_suite_wiedza_image_markup($post_id, 'medium_large', 'zpKBPost__img', $title, '(max-width: 760px) calc(100vw - 32px), (max-width: 1280px) 50vw, 360px'); ?>
      <span class="zpKBPost__views"><i data-lucide="eye"></i><?php echo esc_html($views_label); ?></span>
      <span class="zpKBPost__tagStack">
        <?php foreach($cat_pills as $pill): ?><em><?php echo esc_html($pill); ?></em><?php endforeach; ?>
      </span>
    </a>
    <div class="zpKBPost__body">
      <div class="zpKBPost__meta"><span><?php echo esc_html(get_the_date('d.m.Y',$post_id)); ?></span><span><?php echo esc_html($read); ?></span><span><?php echo esc_html($first_cat); ?></span></div>
      <h3><a href="<?php echo esc_url(get_permalink($post_id)); ?>"><?php echo esc_html($title); ?></a></h3>
      <p><?php echo esc_html(wp_trim_words($excerpt, 32, '…')); ?></p>
      <a class="zpKBPost__link" href="<?php echo esc_url(get_permalink($post_id)); ?>"><span>Czytaj artykuł</span><i data-lucide="arrow-up-right"></i></a>
    </div>
  </article>
  <?php
  return ob_get_clean();
}

function zp_suite_wiedza_index_cache_key(){
  return 'zp_suite_wiedza_index_' . str_replace('.', '_', (string) ZP_SUITE_VERSION);
}

function zp_suite_wiedza_flush_index_cache($unused = null){
  delete_transient(zp_suite_wiedza_index_cache_key());
}

function zp_suite_wiedza_maybe_flush_post_cache($post_id){
  $post_id = (int) $post_id;
  if ($post_id && get_post_type($post_id) === 'post') {
    zp_suite_wiedza_flush_index_cache();
  }
}

function zp_suite_wiedza_maybe_flush_term_cache($object_id, $terms, $tt_ids, $taxonomy){
  if ($taxonomy === 'category' && get_post_type((int) $object_id) === 'post') {
    zp_suite_wiedza_flush_index_cache();
  }
}

function zp_suite_wiedza_maybe_flush_meta_cache($meta_id, $post_id, $meta_key){
  $watched = ['_thumbnail_id', '_zp_blog_img', '_zp_blog_read', '_zp_blog_tag', '_zp_post_reads', '_zp_post_views'];
  if (in_array((string) $meta_key, $watched, true) && get_post_type((int) $post_id) === 'post') {
    zp_suite_wiedza_flush_index_cache();
  }
}

add_action('save_post_post', 'zp_suite_wiedza_maybe_flush_post_cache', 20, 1);
add_action('trashed_post', 'zp_suite_wiedza_maybe_flush_post_cache', 10, 1);
add_action('untrashed_post', 'zp_suite_wiedza_maybe_flush_post_cache', 10, 1);
add_action('before_delete_post', 'zp_suite_wiedza_maybe_flush_post_cache', 10, 1);
add_action('set_object_terms', 'zp_suite_wiedza_maybe_flush_term_cache', 10, 4);
add_action('created_category', 'zp_suite_wiedza_flush_index_cache', 10, 0);
add_action('edited_category', 'zp_suite_wiedza_flush_index_cache', 10, 0);
add_action('delete_category', 'zp_suite_wiedza_flush_index_cache', 10, 0);
add_action('added_post_meta', 'zp_suite_wiedza_maybe_flush_meta_cache', 10, 3);
add_action('updated_post_meta', 'zp_suite_wiedza_maybe_flush_meta_cache', 10, 3);
add_action('deleted_post_meta', 'zp_suite_wiedza_maybe_flush_meta_cache', 10, 3);

/**
 * Cache only the server-rendered post grid. The page shell and AJAX nonce stay
 * dynamic, while every article/link remains present in the initial HTML.
 */
function zp_suite_wiedza_index_payload(){
  $cache_key = zp_suite_wiedza_index_cache_key();
  $cached = get_transient($cache_key);
  if (is_array($cached) && isset($cached['html'], $cached['count'])) {
    return $cached;
  }

  $query = new WP_Query([
    'post_type'              => 'post',
    'post_status'            => 'publish',
    'posts_per_page'         => -1,
    'orderby'                => 'date',
    'order'                  => 'DESC',
    'ignore_sticky_posts'    => true,
    'no_found_rows'          => true,
    'cache_results'          => true,
    'update_post_meta_cache' => true,
    'update_post_term_cache' => true,
  ]);

  $html = '';
  foreach ($query->posts as $i => $post) {
    $html .= zp_suite_wiedza_card_markup($post->ID, $i);
  }

  $payload = [
    'html'  => $html,
    'count' => (int) $query->post_count,
  ];
  set_transient($cache_key, $payload, HOUR_IN_SECONDS);
  return $payload;
}

function zp_suite_wiedza_ajax_search(){
  check_ajax_referer('zp_wiedza_nonce', 'nonce');
  $search = isset($_POST['search']) ? sanitize_text_field(wp_unslash($_POST['search'])) : '';
  $cat = isset($_POST['cat']) ? sanitize_title(wp_unslash($_POST['cat'])) : 'all';
  $sort = isset($_POST['sort']) ? sanitize_key(wp_unslash($_POST['sort'])) : 'newest';

  $args = [
    'post_type' => 'post',
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'ignore_sticky_posts' => true,
    'no_found_rows' => true,
    'cache_results' => true,
    'update_post_meta_cache' => true,
    'update_post_term_cache' => true,
  ];
  if ($search !== '') { $args['s'] = $search; }
  if ($cat && $cat !== 'all') {
    $term_ids = zp_suite_wiedza_filter_term_ids($cat);
    if ($term_ids) { $args['category__in'] = $term_ids; }
  }
  if ($sort === 'oldest') { $args['orderby']='date'; $args['order']='ASC'; }
  elseif ($sort === 'title') { $args['orderby']='title'; $args['order']='ASC'; }
  else { $args['orderby']='date'; $args['order']='DESC'; }

  $query = new WP_Query($args);
  $html = '';
  foreach ($query->posts as $i => $post) {
    $html .= zp_suite_wiedza_card_markup($post->ID, $i);
  }
  wp_send_json_success(['html'=>$html, 'count'=>(int)$query->post_count]);
}
add_action('wp_ajax_zp_wiedza_search', 'zp_suite_wiedza_ajax_search');
add_action('wp_ajax_nopriv_zp_wiedza_search', 'zp_suite_wiedza_ajax_search');

function zp_suite_render_wiedza_blog(){
  $zp_kb_index = zp_suite_wiedza_index_payload();
  $zp_kb_posts_html = $zp_kb_index['html'];
  $zp_kb_posts_count = (int) $zp_kb_index['count'];
  $zp_kb_topics = zp_suite_wiedza_topic_groups_resolved();
  ob_start();
  include ZP_SUITE_PATH . 'templates/wiedza-blog.php';
  wp_reset_postdata();
  return ob_get_clean();
}


/**
 * Qualified article reads.
 *
 * We do not increment on a raw page request. The browser reports a read only
 * after meaningful engagement (time + scroll, or a longer active stay). The
 * server additionally de-duplicates the same visitor/post for 12 hours.
 */
function zp_suite_wiedza_register_read(){
  $post_id = isset($_POST['post_id']) ? absint($_POST['post_id']) : 0;
  if (!$post_id || get_post_type($post_id) !== 'post' || get_post_status($post_id) !== 'publish') {
    wp_send_json_error(['message' => 'invalid_post'], 400);
  }

  $ua = isset($_SERVER['HTTP_USER_AGENT']) ? (string) wp_unslash($_SERVER['HTTP_USER_AGENT']) : '';
  if ($ua && preg_match('/bot|crawl|spider|slurp|bingpreview|facebookexternalhit|headless|lighthouse|pagespeed/i', $ua)) {
    wp_send_json_success(['count' => zp_suite_wiedza_read_count($post_id), 'counted' => false]);
  }

  $ref = wp_get_referer();
  if ($ref) {
    $ref_host = strtolower((string) wp_parse_url($ref, PHP_URL_HOST));
    $site_host = strtolower((string) wp_parse_url(home_url('/'), PHP_URL_HOST));
    if ($ref_host && $site_host && $ref_host !== $site_host) {
      wp_send_json_error(['message' => 'bad_origin'], 403);
    }
  }

  $ip = isset($_SERVER['REMOTE_ADDR']) ? (string) $_SERVER['REMOTE_ADDR'] : '';
  $fingerprint = hash_hmac('sha256', $post_id . '|' . $ip . '|' . $ua, wp_salt('auth'));
  $dedupe_key = 'zp_read_' . substr($fingerprint, 0, 36);
  if (get_transient($dedupe_key)) {
    wp_send_json_success(['count' => zp_suite_wiedza_read_count($post_id), 'counted' => false]);
  }

  $current = zp_suite_wiedza_read_count($post_id);
  $new = $current + 1;
  update_post_meta($post_id, '_zp_post_reads', $new);
  set_transient($dedupe_key, 1, 12 * HOUR_IN_SECONDS);
  zp_suite_wiedza_flush_index_cache();

  wp_send_json_success(['count' => $new, 'counted' => true]);
}
add_action('wp_ajax_zp_wiedza_register_read', 'zp_suite_wiedza_register_read');
add_action('wp_ajax_nopriv_zp_wiedza_register_read', 'zp_suite_wiedza_register_read');

function zp_suite_wiedza_read_tracker_script(){
  if (!is_singular('post')) { return; }
  $post_id = (int) get_queried_object_id();
  if (!$post_id || get_post_status($post_id) !== 'publish') { return; }
  $ajax = admin_url('admin-ajax.php');
  ?>
  <script id="zp-qualified-read-tracker">
  (function(){
    var postId=<?php echo (int)$post_id; ?>, endpoint=<?php echo wp_json_encode($ajax); ?>;
    if(!postId||!endpoint||!window.fetch)return;
    var key='zp_read_v2_'+postId, now=Date.now();
    try{var last=parseInt(localStorage.getItem(key)||'0',10);if(last&&now-last<43200000)return;}catch(e){}
    var active=0,depth=false,sent=false,timer=null;
    function visible(){return document.visibilityState!=='hidden';}
    function checkDepth(){
      var d=document.documentElement,b=document.body;
      var h=Math.max(d.scrollHeight,d.offsetHeight,b?b.scrollHeight:0,b?b.offsetHeight:0)-window.innerHeight;
      var y=window.scrollY||window.pageYOffset||0;
      depth=h<=0?true:(y/h)>=0.30;
      maybe();
    }
    function maybe(){if(sent)return;if((active>=15&&depth)||active>=45)send();}
    function send(){
      if(sent)return;sent=true;
      try{localStorage.setItem(key,String(Date.now()));}catch(e){}
      var fd=new FormData();fd.append('action','zp_wiedza_register_read');fd.append('post_id',String(postId));
      fetch(endpoint,{method:'POST',credentials:'same-origin',body:fd,keepalive:true}).catch(function(){});
      if(timer)clearInterval(timer);window.removeEventListener('scroll',checkDepth);
    }
    timer=setInterval(function(){if(visible())active+=1;maybe();},1000);
    window.addEventListener('scroll',checkDepth,{passive:true});
    checkDepth();
  })();
  </script>
  <?php
}
add_action('wp_footer', 'zp_suite_wiedza_read_tracker_script', 80);

/** Keep WordPress HTTP pagination and the rendered category archive in agreement. */
function zp_suite_seo_category_query($query) {
  if (is_admin() || !$query->is_main_query() || !$query->is_category() || $query->is_feed()) { return; }
  $term = $query->get_queried_object();
  if (!$term || is_wp_error($term) || empty($term->term_id)) { return; }
  $group = zp_suite_wiedza_group_for_term($term);
  $ids = !empty($group['term_ids']) ? array_map('intval', $group['term_ids']) : [(int)$term->term_id];
  // Retain the queried term object used for breadcrumbs/canonical redirects.
  $query->set('cat', 0);
  $query->set('category_name', '');
  $query->set('category__in', $ids);
  $query->set('posts_per_page', 12);
  $query->set('posts_per_archive_page', 12);
  $query->set('post_type', 'post');
  $query->set('post_status', 'publish');
  $query->set('orderby', 'date');
  $query->set('order', 'DESC');
  $query->set('ignore_sticky_posts', true);
}
add_action('pre_get_posts', 'zp_suite_seo_category_query', 90);

/** Redirect legacy aliases to the primary category URL for a topic cluster. */
function zp_suite_wiedza_redirect_legacy_category(){
  if (!is_category() || is_admin()) { return; }
  $term = get_queried_object();
  $group = zp_suite_wiedza_group_for_term($term);
  if (!$group || empty($group['primary_term_id']) || (int)$group['primary_term_id'] === (int)$term->term_id) { return; }
  $primary = get_term((int)$group['primary_term_id'], 'category');
  if (!$primary || is_wp_error($primary)) { return; }
  $url = get_category_link($primary);
  if (is_wp_error($url)) { return; }
  $paged = max(1, (int) get_query_var('paged'));
  global $wp_query;
  if ($paged > max(1, (int)$wp_query->max_num_pages)) { return; }
  if ($paged > 1) { $url = trailingslashit($url) . 'page/' . $paged . '/'; }
  wp_safe_redirect($url, 301, 'Zaprojektowani Suite');
  exit;
}
add_action('template_redirect', 'zp_suite_wiedza_redirect_legacy_category', 4);

/** Premium, crawlable category archive used by the knowledge hub. */
function zp_suite_wiedza_category_template($template){
  if (is_category()) {
    $custom = ZP_SUITE_PATH . 'templates/wiedza-category.php';
    if (file_exists($custom)) { return $custom; }
  }
  return $template;
}
add_filter('template_include', 'zp_suite_wiedza_category_template', 70);

/* Sensible Rank Math fallbacks for category archives; preserve manually set term SEO. */
add_filter('rank_math/frontend/title', function($title){
  if (!is_category()) { return $title; }
  $term = get_queried_object();
  if (!$term || is_wp_error($term)) { return $title; }
  $manual = get_term_meta((int)$term->term_id, 'rank_math_title', true);
  if ($manual) { return $title; }
  $group = zp_suite_wiedza_group_for_term($term);
  $label = $group['label'] ?? $term->name;
  return $label . ' — poradniki i wiedza | Zaprojektowani.com';
}, 80);

add_filter('rank_math/frontend/description', function($description){
  if (!is_category()) { return $description; }
  $term = get_queried_object();
  if (!$term || is_wp_error($term)) { return $description; }
  $manual = get_term_meta((int)$term->term_id, 'rank_math_description', true);
  if ($manual) { return $description; }
  $group = zp_suite_wiedza_group_for_term($term);
  if (!empty($group['description'])) {
    return $group['description'] . ' Praktyczne poradniki Zaprojektowani o projektowaniu, wdrożeniu i rozwoju marki online.';
  }
  $term_desc = trim(wp_strip_all_tags(term_description((int)$term->term_id, 'category')));
  return $term_desc ?: ('Poradniki Zaprojektowani w kategorii ' . $term->name . '. Praktyczne materiały, checklisty i przykłady dla firm.');
}, 80);

add_action('init', function(){
  add_shortcode('zp_wiedza_blog', function(){
    return zp_suite_render_wiedza_blog();
  });
});
