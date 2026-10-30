<?php
/**
 * 3.2.94 — Wiedza (/wiedza/, /en/knowledge/), Mat 29.10.2026: „sekcja po lewej ten jakby featured post jest strasznie
 * ogromna trzeba to zmniejszyc duzo przy kategoriach dodaj ikonki pasujace do kategorii”.
 *
 * - Sekcja nad indeksem (div.zpKBFeatured: „W centrum uwagi” + „Zanim zaczniesz projekt.”) jest niższa i wyważona:
 *   zdjęcie wyróżnionego wpisu ma stałą wysokość (object-fit:cover, niezależnie od proporcji zdjęcia), lista
 *   czterech poradników jest zwarta, obie kolumny kończą się na tej samej wysokości. Same teksty, linki i kolory
 *   bez zmian — tylko CSS (assets/css/wyglad-3294/wiedza.css).
 * - Ikonki kategorii w chipach: pasek „Szybkie ścieżki” w hero (nav.zh__quickTopics) i „Wybierz obszar” w indeksie
 *   (a.zpKX__tag[data-zp-cat]). Inline SVG (Lucide 0.574, ta sama mapa kategoria → ikonka co karty tematów
 *   w templates/wiedza-blog.php), wstawiane po stronie serwera filtrem do_shortcode_tag do HTML [zp_wiedza_blog]:
 *   bez biblioteki JS i z jawnym rozmiarem, więc nic nie przeskakuje po załadowaniu. wiedza.js (3.2.93) tylko
 *   przełącza klasy chipów, więc ikonki zostają. Wersja EN tłumaczy teksty w buforze strony: SVG zostaje bez zmian,
 *   a nazwa z licznikiem w indeksie siedzi w osobnym spanie, żeby klucz słownika („Strony internetowe <1>N</1>”) się nie zmienił.
 */
if (!defined('ABSPATH')) { exit; }

/** The knowledge index page (the same test as the 3.2.93 module: the page holding [zp_wiedza_blog]). */
function zp_w94_wiedza_is_index(): bool {
  return function_exists('zp_w93_wiedza_is_index_request') && zp_w93_wiedza_is_index_request();
}

function zp_w94_wiedza_enqueue(): void {
  if (!zp_w94_wiedza_is_index()) { return; }
  wp_enqueue_style('zp-w94-wiedza', ZP_SUITE_URL . 'assets/css/wyglad-3294/wiedza.css', [], ZP_SUITE_VERSION);
}
add_action('wp_enqueue_scripts', 'zp_w94_wiedza_enqueue', 21);

/** Topic key => Lucide icon: the topic cards' map (templates/wiedza-blog.php) plus „Wszystkie”. Unknown keys get book-open, as there. */
function zp_w94_wiedza_icon_name(string $key): string {
  $map = [
    'all'                => 'layers',
    'strony-internetowe' => 'layout-template',
    'sklepy-internetowe' => 'shopping-bag',
    'logo-branding'      => 'sparkles',
    'seo-konwersja'      => 'search',
    'kampanie-reklamowe' => 'megaphone',
    'case-study'         => 'briefcase-business',
  ];
  return $map[$key] ?? 'book-open';
}

/** One inline Lucide icon (24×24 grid, stroke = text colour of the chip), with an explicit size so nothing shifts. */
function zp_w94_wiedza_icon(string $name, int $size): string {
  // Path data copied from assets/js/vendor/lucide.min.js (Lucide 0.574.0, ISC licence) so the icons match the topic cards.
  $nodes = [
    'layers'             => '<path d="M12.83 2.18a2 2 0 0 0-1.66 0L2.6 6.08a1 1 0 0 0 0 1.83l8.58 3.91a2 2 0 0 0 1.66 0l8.58-3.9a1 1 0 0 0 0-1.83z"/><path d="M2 12a1 1 0 0 0 .58.91l8.6 3.91a2 2 0 0 0 1.65 0l8.58-3.9A1 1 0 0 0 22 12"/><path d="M2 17a1 1 0 0 0 .58.91l8.6 3.91a2 2 0 0 0 1.65 0l8.58-3.9A1 1 0 0 0 22 17"/>',
    'layout-template'    => '<rect width="18" height="7" x="3" y="3" rx="1"/><rect width="9" height="7" x="3" y="14" rx="1"/><rect width="5" height="7" x="16" y="14" rx="1"/>',
    'shopping-bag'       => '<path d="M16 10a4 4 0 0 1-8 0"/><path d="M3.103 6.034h17.794"/><path d="M3.4 5.467a2 2 0 0 0-.4 1.2V20a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6.667a2 2 0 0 0-.4-1.2l-2-2.667A2 2 0 0 0 17 2H7a2 2 0 0 0-1.6.8z"/>',
    'sparkles'           => '<path d="M11.017 2.814a1 1 0 0 1 1.966 0l1.051 5.558a2 2 0 0 0 1.594 1.594l5.558 1.051a1 1 0 0 1 0 1.966l-5.558 1.051a2 2 0 0 0-1.594 1.594l-1.051 5.558a1 1 0 0 1-1.966 0l-1.051-5.558a2 2 0 0 0-1.594-1.594l-5.558-1.051a1 1 0 0 1 0-1.966l5.558-1.051a2 2 0 0 0 1.594-1.594z"/><path d="M20 2v4"/><path d="M22 4h-4"/><circle cx="4" cy="20" r="2"/>',
    'search'             => '<path d="m21 21-4.34-4.34"/><circle cx="11" cy="11" r="8"/>',
    'megaphone'          => '<path d="M11 6a13 13 0 0 0 8.4-2.8A1 1 0 0 1 21 4v12a1 1 0 0 1-1.6.8A13 13 0 0 0 11 14H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2z"/><path d="M6 14a12 12 0 0 0 2.4 7.2 2 2 0 0 0 3.2-2.4A8 8 0 0 1 10 14"/><path d="M8 6v8"/>',
    'briefcase-business' => '<path d="M12 12h.01"/><path d="M16 6V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/><path d="M22 13a18.15 18.15 0 0 1-20 0"/><rect width="20" height="14" x="2" y="6" rx="2"/>',
    'book-open'          => '<path d="M12 7v14"/><path d="M3 18a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h5a4 4 0 0 1 4 4 4 4 0 0 1 4-4h5a1 1 0 0 1 1 1v13a1 1 0 0 1-1 1h-6a3 3 0 0 0-3 3 3 3 0 0 0-3-3z"/>',
  ];
  if (!isset($nodes[$name])) { $name = 'book-open'; }
  return '<svg class="zpW94Ico" data-ico="' . $name . '" xmlns="http://www.w3.org/2000/svg" width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $nodes[$name] . '</svg>';
}

/**
 * Topic key of a hero quick-path link: its URL is the topic's archive URL (zp_suite_wiedza_topic_groups_resolved(),
 * the same memoised data the template printed). The template's static fallback (no topics) links the service pages.
 */
function zp_w94_wiedza_topic_for_url(string $href, array $by_url): string {
  $href = html_entity_decode($href, ENT_QUOTES, 'UTF-8');
  if (isset($by_url[$href])) { return $by_url[$href]; }
  $fallback = ['/strony-internetowe-katowice/' => 'strony-internetowe', '/sklepy-internetowe-katowice/' => 'sklepy-internetowe', '/logo-branding-katowice/' => 'logo-branding', '/kampanie-reklamowe/' => 'kampanie-reklamowe'];
  foreach ($fallback as $path => $key) {
    if (substr(untrailingslashit((string) wp_parse_url($href, PHP_URL_PATH)) . '/', -strlen($path)) === $path) { return $key; }
  }
  return '';
}

/** Adds the category icons to the chips in the HTML of [zp_wiedza_blog]. */
function zp_w94_wiedza_chip_icons($output, $tag) {
  if ($tag !== 'zp_wiedza_blog' || !is_string($output) || $output === '' || strpos($output, 'class="zpW94Ico"') !== false) { return $output; }

  // 1) Hero strip, „Szybkie ścieżki”: <a href="TOPIC URL"> <span>Label</span> <em>N</em> <i data-lucide="arrow-up-right"></i> </a>
  //    The icon goes before the label; class zpW94Chip lets the CSS drop the trailing arrow, so every chip keeps its width.
  $by_url = [];
  foreach ((function_exists('zp_suite_wiedza_topic_groups_resolved') ? zp_suite_wiedza_topic_groups_resolved() : []) as $key => $topic) {
    if (!empty($topic['url'])) { $by_url[(string) $topic['url']] = (string) $key; }
  }
  $output = preg_replace_callback('~(<div class="zh__quickTopicsList">)(.*?)(</div>)~s', static function ($list) use ($by_url) {
    $links = preg_replace_callback('~<a href="([^"]*)"([^>]*)>(\s*)<span>~', static function ($a) use ($by_url) {
      $key = zp_w94_wiedza_topic_for_url($a[1], $by_url);
      if ($key === '' || stripos($a[2], 'class=') !== false) { return $a[0]; }
      return '<a class="zpW94Chip" href="' . $a[1] . '"' . $a[2] . '>' . $a[3] . zp_w94_wiedza_icon(zp_w94_wiedza_icon_name($key), 14) . '<span>';
    }, $list[2]);
    return $list[1] . (is_string($links) ? $links : $list[2]) . $list[3];
  }, $output, 1) ?? $output;

  // 2) Index, „Wybierz obszar”: <a class="zpKX__tag…" … data-zp-cat="KEY" …>Label <span>N</span></a> (the sort chips have data-zp-sort).
  //    The old content goes into span.zpW94Lab, so the English page still finds „Label <1>N</1>” in its dictionary
  //    (an icon next to the text inside the same element would change that key and leave the label in Polish).
  $output = preg_replace_callback('~(<a class="zpKX__tag(?:\s[^"]*)?"[^>]*\sdata-zp-cat="([^"]*)"[^>]*>)(.*?)(</a>)~s', static function ($a) {
    return $a[1] . zp_w94_wiedza_icon(zp_w94_wiedza_icon_name(html_entity_decode($a[2], ENT_QUOTES, 'UTF-8')), 15) . '<span class="zpW94Lab">' . $a[3] . '</span>' . $a[4];
  }, $output) ?? $output;

  return $output;
}
add_filter('do_shortcode_tag', 'zp_w94_wiedza_chip_icons', 10, 2);
