<?php
/**
 * Audit 21.10.2026, item 05 (Mat: "1 ok"): pages built from the plugin's templates kept the date of their last save in
 * WordPress in the sitemaps (page-sitemap.xml dated /tworzenie-sklepow-internetowych/, /projektowanie-logo/ and
 * /identyfikacja-wizualna/ 03.10 although their texts were rewritten on 15.10), because a change in a template or in the
 * plugin never touches the post. The file date of a template cannot stand in for it: installing a ZIP gives every file a
 * new date.
 *
 * So each Polish page render takes a fingerprint of the text a reader sees between the site header and the footer. A new
 * text gives the page the date of that render in the Polish and English sitemaps, through the record the content feed
 * already uses (zp_ai_changed_at_display(), without an IndexNow ping). The new text must come back in a second render and
 * must not be one of the page's last five texts, so text that differs on every render or swaps back and forth never
 * moves the date. A page's first fingerprint counts as a change: when its text last changed is unknown, and after the
 * template rewrites it is later than the WordPress date. Nothing on the page changes.
 */
if (!defined('ABSPATH')) { exit; }

const ZP_SEO_LASTMOD_META = '_zp_text_fingerprint';

/** The visible text between the site header and the footer: no scripts, styles, templates, icons or comments. */
function zp_seo_lastmod_text(string $html): string {
  $nav = strpos($html, 'id="zpNewNav"');
  $from = $nav !== false ? strpos($html, '</header>', $nav) : false;
  if ($from === false) { $from = stripos($html, '<body'); }
  if ($from === false) { return ''; }
  $foot = strpos($html, 'id="zpMegaFooter"', $from);
  $to = $foot !== false ? strrpos($html, '<footer', $foot - strlen($html)) : false;
  if ($to === false || $to < $from) { $to = stripos($html, '</body>', $from); }
  if ($to === false) { $to = strlen($html); }
  $part = preg_replace(['~<(script|style|template|noscript|svg)\b.*?</\1\s*>~is', '~<!--.*?-->~s'], ' ', substr($html, $from, $to - $from));
  if (!is_string($part)) { return ''; }
  $text = html_entity_decode(strip_tags(str_replace('<', ' <', $part)), ENT_QUOTES | ENT_HTML5, 'UTF-8');
  return trim((string) preg_replace('~\s+~u', ' ', $text));
}

/** Compares the page's text with the last one seen and, when it is new and confirmed, dates the page in the sitemaps. */
function zp_seo_lastmod_check(WP_Post $post, string $path, string $text): void {
  if (strlen($text) < 200) { return; } // an error or an empty render is no text of the page
  $hash = md5($text);
  $row = get_post_meta($post->ID, ZP_SEO_LASTMOD_META, true);
  $row = is_array($row) ? $row : [];
  $current = (string) ($row['h'] ?? '');
  if ($current === $hash) {
    if (($row['c'] ?? '') !== '') { $row['c'] = ''; update_post_meta($post->ID, ZP_SEO_LASTMOD_META, $row); }
    return;
  }
  if ($current !== '' && ($row['c'] ?? '') !== $hash) { // seen once: it counts when a second render shows the same text
    $row['c'] = $hash;
    update_post_meta($post->ID, ZP_SEO_LASTMOD_META, $row);
    return;
  }
  $recent = array_values(array_filter((array) ($row['p'] ?? []), 'is_string'));
  update_post_meta($post->ID, ZP_SEO_LASTMOD_META, ['h' => $hash, 'c' => '', 'p' => array_slice(array_merge([$hash], array_diff($recent, [$hash])), 0, 5)]);
  if (!in_array($hash, $recent, true) && function_exists('zp_ai_changed_at_display')) {
    zp_ai_changed_at_display([$path], 'nowy tekst strony', false);
  }
}

/** The language module's last step over the finished HTML of every page (both languages); the HTML passes unchanged. */
function zp_seo_lastmod_watch($html, $lang = '', $source = '') {
  if (!is_string($html) || $lang !== 'pl' || !function_exists('zp_ai_on') || !zp_ai_on()) { return $html; }
  if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET' || (string) ($_SERVER['QUERY_STRING'] ?? '') !== '') { return $html; }
  if (is_user_logged_in() || is_preview() || (int) http_response_code() !== 200 || !(is_singular('page') || is_front_page())) { return $html; }
  $post = get_queried_object();
  if (!$post instanceof WP_Post || $post->post_type !== 'page' || $post->post_status !== 'publish') { return $html; }
  $path = zp_seo_plan_path((string) get_permalink($post));
  if ($path !== zp_seo_plan_path()) { return $html; } // another address of the page (a page number, an old slug)
  try {
    zp_seo_lastmod_check($post, $path, zp_seo_lastmod_text($html));
  } catch (\Throwable $e) {
    // A failed check leaves the date as it was.
  }
  return $html;
}
add_filter('zp_en_public_price_html', 'zp_seo_lastmod_watch', 99, 3);

// The fingerprint is bookkeeping, not page content: writing it must not empty the HTML cache (cache-20261009.php).
add_filter('zp_html_cache_ignore_meta', static function ($ignore, $key = '') {
  return $ignore || $key === ZP_SEO_LASTMOD_META;
}, 10, 2);
