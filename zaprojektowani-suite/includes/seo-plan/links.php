<?php
if (!defined('ABSPATH')) { exit; }

/**
 * "Powiązane usługi i poradniki" under blog posts.
 *
 * Posts from the keyword plan link to the service page they support (anchor text from the
 * plan); other posts link to the service page of their category. Same markup as the block
 * from the editorial module, so it looks the same. Targets that are not published yet
 * (pages planned for later phases) are skipped.
 */

function zp_seo_plan_category_service(int $post_id): ?array {
  $map = [
    'sklepy-internetowe' => ['tworzenie sklepów internetowych', '/tworzenie-sklepow-internetowych/'],
    'logo-branding' => ['projektowanie logo', '/projektowanie-logo/'],
    'strony-internetowe' => ['tworzenie stron internetowych', '/tworzenie-stron-internetowych/'],
    'strony-www' => ['tworzenie stron internetowych', '/tworzenie-stron-internetowych/'],
    'ux-cro-analityka' => ['projektowanie stron internetowych', '/tworzenie-stron-internetowych/'],
    'seo-i-konwersja' => ['tworzenie stron internetowych', '/tworzenie-stron-internetowych/'],
    'seo-content-marketing' => ['tworzenie stron internetowych', '/tworzenie-stron-internetowych/'],
    'meta-ads' => ['kampanie reklamowe Meta Ads i Google Ads', '/kampanie-reklamowe/'],
    'kampanie-reklamowe' => ['kampanie reklamowe Meta Ads i Google Ads', '/kampanie-reklamowe/'],
    'kampanie-reklamowe-meta-ads' => ['kampanie reklamowe Meta Ads i Google Ads', '/kampanie-reklamowe/'],
    'reklamy' => ['kampanie reklamowe Meta Ads i Google Ads', '/kampanie-reklamowe/'],
  ];
  foreach (wp_get_post_categories($post_id, ['fields' => 'slugs']) as $slug) {
    if (isset($map[$slug])) { return ['text' => $map[$slug][0], 'path' => $map[$slug][1]]; }
  }
  return null;
}

function zp_seo_plan_link_is_live(string $path): bool {
  static $cache = [];
  if (!isset($cache[$path])) { $cache[$path] = zp_seo_plan_published_path($path); }
  return $cache[$path];
}

add_filter('the_content', function ($content) {
  if (!zp_seo_plan_active() || is_admin() || zp_seo_plan_is_en() || !is_singular('post') || !in_the_loop() || !is_main_query()) { return $content; }
  if (strpos((string) $content, 'zpu-related') !== false) { return $content; }
  $id = get_the_ID();
  $self = zp_seo_plan_path((string) get_permalink($id));
  $entry = zp_seo_plan_entry($self);
  $links = $entry['links'] ?? [];
  $service = zp_seo_plan_category_service((int) $id);
  if ($service) {
    $has_service = false;
    foreach ($links as $l) { if (strpos($l['path'], '/', 1) === strlen($l['path']) - 1) { $has_service = true; break; } }
    if (!$has_service) { $links[] = $service; }
  }
  $items = '';
  $seen = [];
  foreach ($links as $l) {
    $path = (string) ($l['path'] ?? '');
    if ($path === '' || $path === $self || isset($seen[$path]) || !zp_seo_plan_link_is_live($path)) { continue; }
    $seen[$path] = true;
    $items .= '<li><a href="' . esc_url(home_url($path)) . '">' . esc_html(zp_seo_plan_ucfirst((string) $l['text'])) . '</a></li>';
  }
  if ($items === '') { return $content; }
  return $content . '<section class="zpu-related" aria-label="Powiązane materiały"><h2>Powiązane usługi i poradniki</h2><ul>' . $items . '</ul></section>';
}, 30);

function zp_seo_plan_ucfirst(string $text): string {
  if ($text === '') { return $text; }
  return mb_strtoupper(mb_substr($text, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr($text, 1, null, 'UTF-8');
}

/* ------------------------------------------------------------ site-wide links */

/** $path while the plan is on and the page is published, else $fallback (for templates). */
function zp_seo_plan_url(string $path, string $fallback): string {
  return (zp_seo_plan_active() && zp_seo_plan_link_is_live($path)) ? $path : $fallback;
}

/**
 * Slug => path for site-wide links: Katowice service pages => nationwide pages, and
 * industry pages planned for a later phase (linked from the home page, not published yet)
 * => the closest existing page. Each rule applies only while its target is published and,
 * for industry pages, only until the page itself is published.
 */
function zp_seo_plan_link_map(): array {
  static $map = null;
  if ($map === null) {
    $map = [];
    foreach (['strony-internetowe-katowice' => '/tworzenie-stron-internetowych/', 'sklepy-internetowe-katowice' => '/tworzenie-sklepow-internetowych/', 'logo-branding-katowice' => '/projektowanie-logo/'] as $slug => $to) {
      if (zp_seo_plan_link_is_live($to)) { $map[$slug] = $to; }
    }
    $later = [
      'strony-internetowe-dla-kancelarii' => '/tworzenie-stron-internetowych/',
      'strony-internetowe-dla-deweloperow' => '/tworzenie-stron-internetowych/',
      'strony-internetowe-dla-salonow-beauty' => '/sklepy-internetowe/strona-internetowa-dla-branzy-beauty-jakie-sekcje-zwiekszaja-zapytania/',
      'sklep-internetowy-dla-producenta' => '/tworzenie-sklepow-internetowych/',
    ];
    foreach ($later as $slug => $to) {
      if (!zp_seo_plan_link_is_live('/' . $slug . '/') && zp_seo_plan_link_is_live($to)) { $map[$slug] = $to; }
    }
  }
  return $map;
}

/**
 * Links to the Katowice service pages (menu, footer, home sections, blog posts, CMS
 * content) point to the nationwide pages, which now carry the main service phrases.
 * Links marked data-zp-local="1" stay on the local pages. Only <a href> is touched:
 * canonical, hreflang and structured data keep their own URLs.
 */
function zp_seo_plan_remap_html(string $html): string {
  $map = zp_seo_plan_link_map();
  if (!$map) { return $html; }
  $slugs = implode('|', array_map(static function ($s) { return preg_quote($s, '~'); }, array_keys($map)));
  $href = '~(\shref=)(["\'])(?:https?://(?:www\.)?zaprojektowani\.com)?/(' . $slugs . ')/(#[^"\']*)?\2~i';
  $out = preg_replace_callback('~<a\b[^>]*>~i', static function ($m) use ($map, $href) {
    $tag = $m[0];
    if (stripos($tag, 'href') === false || stripos($tag, 'data-zp-local') !== false) { return $tag; }
    $new = preg_replace_callback($href, static function ($h) use ($map) {
      $slug = strtolower($h[3]);
      return isset($map[$slug]) ? $h[1] . $h[2] . esc_url(home_url($map[$slug])) . ($h[4] ?? '') . $h[2] : $h[0];
    }, $tag);
    return is_string($new) ? $new : $tag;
  }, $html);
  return is_string($out) ? $out : $html;
}

add_action('template_redirect', function () {
  if (!zp_seo_plan_active() || is_admin() || wp_doing_ajax() || is_feed() || is_preview() || zp_seo_plan_is_en() || isset($_GET['elementor-preview'])) { return; }
  if (!in_array($_SERVER['REQUEST_METHOD'] ?? 'GET', ['GET', 'HEAD'], true)) { return; }
  ob_start(static function ($html) {
    if (!is_string($html) || $html === '' || stripos($html, '<html') === false) { return $html; }
    foreach (headers_list() as $header) {
      if (stripos($header, 'Content-Type:') === 0 && stripos($header, 'text/html') === false) { return $html; }
    }
    return zp_seo_plan_remap_html($html);
  });
}, 2);
