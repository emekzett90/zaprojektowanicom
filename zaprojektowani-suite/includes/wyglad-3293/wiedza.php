<?php
/**
 * 3.2.93 — Wiedza: przebudowany indeks poradników (jasna część /wiedza/ i /en/knowledge/ pod hero).
 *
 * Sortowanie (najnowsze / najpopularniejsze / najstarsze / najdłuższe / najkrótsze / A–Z), chipy kategorii,
 * szukajka i „Pokaż więcej”. Wszystkie wpisy są w HTML (SEO), stan ?sort=&kat=&q= jest honorowany też po stronie
 * serwera (kolejność + ukrycie niepasujących kart), JS tylko przestawia i ukrywa karty. Hero, nagłówek, stopka,
 * szablony wpisów i śledzenie czytań bez zmian.
 */
if (!defined('ABSPATH')) { exit; }

/** True when the visitor sees the English version. */
function zp_w93_wiedza_is_en(): bool {
  return function_exists('zp_seo_plan_is_en') && zp_seo_plan_is_en();
}

/** PL/EN pair; every new string of the index goes through here (nothing new for the dictionaries). */
function zp_w93_wiedza_t(string $pl, string $en): string {
  return zp_w93_wiedza_is_en() ? $en : $pl;
}

/** The knowledge index page: the page that holds [zp_wiedza_blog] (PL /wiedza/, EN /en/knowledge/). */
function zp_w93_wiedza_is_index_request(): bool {
  static $memo = null;
  if ($memo !== null) { return $memo; }
  if (is_admin() || !is_page()) { return $memo = false; }
  $post = get_queried_object();
  if (!($post instanceof WP_Post)) { return $memo = false; }
  if ($post->post_name === 'wiedza') { return $memo = true; }
  return $memo = (function_exists('has_shortcode') && has_shortcode((string) $post->post_content, 'zp_wiedza_blog'));
}

function zp_w93_wiedza_enqueue(): void {
  if (!zp_w93_wiedza_is_index_request()) { return; }
  wp_enqueue_style('zp-w93-wiedza', ZP_SUITE_URL . 'assets/css/wyglad-3293/wiedza.css', [], ZP_SUITE_VERSION);
  wp_enqueue_script('zp-w93-wiedza', ZP_SUITE_URL . 'assets/js/wyglad-3293/wiedza.js', [], ZP_SUITE_VERSION, ['in_footer' => true, 'strategy' => 'defer']);
}
add_action('wp_enqueue_scripts', 'zp_w93_wiedza_enqueue', 20);

/** Sort keys of the index: key => [PL label, EN label]. The first one is the default (SEO-stable order). */
function zp_w93_wiedza_sorts(): array {
  return [
    'newest'   => ['Najnowsze', 'Newest'],
    'popular'  => ['Najpopularniejsze', 'Most popular'],
    'oldest'   => ['Najstarsze', 'Oldest'],
    'longest'  => ['Najdłuższe', 'Longest read'],
    'shortest' => ['Najkrótsze', 'Shortest read'],
    'az'       => ['A–Z', 'A–Z'],
  ];
}

/** ?sort= of the current request, whitelisted. */
function zp_w93_wiedza_requested_sort(): string {
  $sort = isset($_GET['sort']) ? sanitize_key(wp_unslash((string) $_GET['sort'])) : '';
  return array_key_exists($sort, zp_w93_wiedza_sorts()) ? $sort : 'newest';
}

/** ?kat= (topic group key) of the current request, or 'all'. `cat` is a WordPress query var, so the index uses `kat`. */
function zp_w93_wiedza_requested_topic(): string {
  $kat = isset($_GET['kat']) ? sanitize_key(wp_unslash((string) $_GET['kat'])) : '';
  if ($kat === '' || $kat === 'all') { return 'all'; }
  $groups = function_exists('zp_suite_wiedza_topic_groups_resolved') ? zp_suite_wiedza_topic_groups_resolved() : [];
  return isset($groups[$kat]) ? $kat : 'all';
}

/** ?q= of the current request (search phrase), trimmed to 80 characters. */
function zp_w93_wiedza_requested_query(): string {
  $q = isset($_GET['q']) ? sanitize_text_field(wp_unslash((string) $_GET['q'])) : '';
  return mb_substr(trim($q), 0, 80);
}

/** Lower-case, accent-free text for matching (the JS does the same with NFD). */
function zp_w93_wiedza_fold(string $text): string {
  $text = mb_strtolower(wp_strip_all_tags(html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8')), 'UTF-8');
  $text = strtr($text, ['ą' => 'a', 'ć' => 'c', 'ę' => 'e', 'ł' => 'l', 'ń' => 'n', 'ó' => 'o', 'ś' => 's', 'ź' => 'z', 'ż' => 'z']);
  $text = remove_accents($text);
  return trim(preg_replace('/\s+/u', ' ', $text));
}

/** Topic-group keys (chips) of a post, resolved from its categories. */
function zp_w93_wiedza_post_groups(int $post_id): array {
  $cats = get_the_category($post_id);
  $keys = [];
  if (!empty($cats) && !is_wp_error($cats) && function_exists('zp_suite_wiedza_group_for_term')) {
    foreach ($cats as $cat) {
      $group = zp_suite_wiedza_group_for_term($cat);
      if ($group && !empty($group['key'])) { $keys[(string) $group['key']] = true; }
    }
  }
  return array_keys($keys);
}

/** Per-render slot for the numeric read count (data-reads); like zp_suite_wiedza_read_slot(), never found in escaped text. */
function zp_w93_wiedza_reads_slot(int $post_id): string {
  return '<!--zp-kb-reads-n:' . $post_id . '-->';
}

/** Reading time of a card as the visitor sees it (the "x min" of zp_suite_wiedza_read_time()), so Najdłuższe/Najkrótsze follow the shown value. */
function zp_w93_wiedza_shown_minutes(int $post_id): int {
  $shown = function_exists('zp_suite_wiedza_read_time') ? (string) zp_suite_wiedza_read_time($post_id) : '';
  return preg_match('/\d+/', $shown, $m) ? max(1, (int) $m[0]) : 1;
}

/**
 * Sort/filter data of one card, kept with the index: everything but the read counts, which change with every reader.
 * The search text is title + excerpt only; the topic chips already cover the categories.
 */
function zp_w93_wiedza_card_meta(int $post_id): array {
  $title = get_the_title($post_id);
  // The same 30 words the card shows, so the server-side ?q= match equals the client-side one.
  $excerpt = function_exists('zp_suite_wiedza_fast_excerpt') ? wp_trim_words(zp_suite_wiedza_fast_excerpt($post_id, 42), 30, '') : '';
  return [
    'date'    => (int) get_post_time('U', false, $post_id),
    'minutes' => zp_w93_wiedza_shown_minutes($post_id),
    'title'   => zp_w93_wiedza_fold($title),
    'cats'    => zp_w93_wiedza_post_groups($post_id),
    'search'  => zp_w93_wiedza_fold($title . ' ' . $excerpt),
  ];
}

/**
 * One card of the index (image-led, whole card clickable through the title link, one tab stop per card).
 * With $read_slot the read count and its number are left as slots, filled on every render (zp_suite_wiedza_index_payload()).
 */
function zp_w93_wiedza_card_markup(int $post_id, int $i = 0, bool $read_slot = true): string {
  $cats = get_the_category($post_id);
  $cats = (!empty($cats) && !is_wp_error($cats)) ? $cats : [];
  $first_cat = $cats ? $cats[0]->name : 'Wiedza';
  $title = get_the_title($post_id);
  $url = get_permalink($post_id);
  $excerpt = wp_trim_words(zp_suite_wiedza_fast_excerpt($post_id, 42), 30, '…');
  $read = zp_suite_wiedza_read_time($post_id);
  $meta = zp_w93_wiedza_card_meta($post_id);
  $views = $read_slot ? zp_suite_wiedza_read_slot($post_id) : esc_html(zp_suite_wiedza_read_label(zp_suite_wiedza_read_count($post_id)));
  $reads = $read_slot ? zp_w93_wiedza_reads_slot($post_id) : (string) zp_suite_wiedza_read_count($post_id);
  $img = zp_suite_wiedza_image_markup($post_id, 'medium_large', 'zpKX__img', $title, '(max-width: 760px) calc(100vw - 32px), (max-width: 1080px) 50vw, 440px');
  ob_start();
  ?>
<article class="zpKX__item" data-zp-post data-id="<?php echo (int) $post_id; ?>" data-reads="<?php echo $reads; ?>" data-date="<?php echo (int) $meta['date']; ?>" data-minutes="<?php echo (int) $meta['minutes']; ?>" data-cat="<?php echo esc_attr(implode(' ', $meta['cats'])); ?>" data-title="<?php echo esc_attr($meta['title']); ?>">
  <a class="zpKX__media" href="<?php echo esc_url($url); ?>" tabindex="-1" aria-hidden="true"><?php echo $img; ?></a>
  <div class="zpKX__body">
    <span class="zpKX__cat"><?php echo esc_html($first_cat); ?></span>
    <h3 class="zpKX__title"><a href="<?php echo esc_url($url); ?>"><?php echo esc_html($title); ?></a></h3>
    <p class="zpKX__excerpt"><?php echo esc_html($excerpt); ?></p>
    <div class="zpKX__meta"><time datetime="<?php echo esc_attr(get_the_date('Y-m-d', $post_id)); ?>"><?php echo esc_html(get_the_date('d.m.Y', $post_id)); ?></time><span><?php echo esc_html($read); ?></span><span class="zpKX__views" data-zp-views><i data-lucide="eye"></i><?php echo $views; ?></span></div>
  </div>
</article>
<?php
  return ob_get_clean();
}

/** English read labels on /en/knowledge/ (the Polish ones come from zp_suite_wiedza_read_label()). */
function zp_w93_wiedza_read_label_filter($label, $count) {
  if (!zp_w93_wiedza_is_en()) { return $label; }
  $count = max(0, (int) $count);
  if ($count < 1) { return 'New post'; }
  return number_format_i18n($count) . ($count === 1 ? ' read' : ' reads');
}
add_filter('zp_suite_wiedza_read_label', 'zp_w93_wiedza_read_label_filter', 10, 2);

/**
 * Order of the cards for one sort key. $meta: id => card meta, $reads: id => read count. Ties fall back to newest first.
 */
function zp_w93_wiedza_order_ids(array $ids, array $meta, array $reads, string $sort): array {
  $ids = array_values(array_map('intval', $ids));
  $cmp_date = static function ($a, $b) use ($meta) { return ($meta[$b]['date'] ?? 0) <=> ($meta[$a]['date'] ?? 0); };
  switch ($sort) {
    case 'popular':
      usort($ids, static function ($a, $b) use ($reads, $cmp_date) { return (($reads[$b] ?? 0) <=> ($reads[$a] ?? 0)) ?: $cmp_date($a, $b); });
      break;
    case 'oldest':
      usort($ids, static function ($a, $b) use ($meta) { return ($meta[$a]['date'] ?? 0) <=> ($meta[$b]['date'] ?? 0); });
      break;
    case 'longest':
      usort($ids, static function ($a, $b) use ($meta, $cmp_date) { return (($meta[$b]['minutes'] ?? 0) <=> ($meta[$a]['minutes'] ?? 0)) ?: $cmp_date($a, $b); });
      break;
    case 'shortest':
      usort($ids, static function ($a, $b) use ($meta, $cmp_date) { return (($meta[$a]['minutes'] ?? 0) <=> ($meta[$b]['minutes'] ?? 0)) ?: $cmp_date($a, $b); });
      break;
    case 'az':
      usort($ids, static function ($a, $b) use ($meta, $cmp_date) { return strcmp((string) ($meta[$a]['title'] ?? ''), (string) ($meta[$b]['title'] ?? '')) ?: $cmp_date($a, $b); });
      break;
    default:
      usort($ids, $cmp_date);
  }
  return $ids;
}

/** Whether one card matches the requested topic and search phrase (server-side honouring of ?kat= and ?q=). */
function zp_w93_wiedza_card_matches(array $meta, string $kat, string $q): bool {
  if ($kat !== 'all' && !in_array($kat, (array) ($meta['cats'] ?? []), true)) { return false; }
  if ($q !== '') {
    $hay = (string) ($meta['search'] ?? '');
    foreach (preg_split('/\s+/u', zp_w93_wiedza_fold($q), -1, PREG_SPLIT_NO_EMPTY) ?: [] as $token) {
      if (strpos($hay, $token) === false) { return false; }
    }
  }
  return true;
}

/**
 * The index HTML for the current request: cards in the requested order, non-matching cards hidden (the JS removes
 * `hidden` again when the visitor changes the filter), plus the number of matches. Used by zp_suite_wiedza_index_payload().
 */
function zp_w93_wiedza_index_html(array $cards, array $meta, array $reads): array {
  $sort = zp_w93_wiedza_requested_sort();
  $kat = zp_w93_wiedza_requested_topic();
  $q = zp_w93_wiedza_requested_query();
  $ids = zp_w93_wiedza_order_ids(array_keys($cards), $meta, $reads, $sort);
  $html = '';
  $matches = 0;
  foreach ($ids as $id) {
    $card = (string) ($cards[$id] ?? '');
    if ($card === '') { continue; }
    if (zp_w93_wiedza_card_matches((array) ($meta[$id] ?? []), $kat, $q)) {
      $matches++;
    } else {
      $card = preg_replace('/^(\s*<article\b)/', '$1 hidden', $card, 1);
    }
    $html .= $card;
  }
  return ['html' => $html, 'matches' => $matches, 'sort' => $sort, 'kat' => $kat, 'q' => $q];
}

/** Polish/English plural forms of "wpis" used by the live counter. */
function zp_w93_wiedza_plural_forms(): array {
  return zp_w93_wiedza_is_en() ? ['post', 'posts', 'posts'] : ['wpis', 'wpisy', 'wpisów'];
}

function zp_w93_wiedza_plural(int $n): string {
  [$one, $few, $many] = zp_w93_wiedza_plural_forms();
  if ($n === 1) { return $one; }
  $m10 = $n % 10; $m100 = $n % 100;
  if ($m10 >= 2 && $m10 <= 4 && !($m100 >= 12 && $m100 <= 14)) { return $few; }
  return $many;
}
