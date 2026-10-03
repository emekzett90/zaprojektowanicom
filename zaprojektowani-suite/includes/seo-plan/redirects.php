<?php
if (!defined('ABSPATH')) { exit; }

/**
 * 301 redirects from the keyword plan and the audit:
 * - merged duplicate posts → the post that stays (audyt/polaczenia-wpisow.csv),
 * - the shop post and its "-2" copy → /tworzenie-sklepow-internetowych/,
 * - old prefixes (/wiedza/…, /poradniki/…, /kampanie-meta-ads/…) → current URLs,
 * - on 404 only: same slug under another category, "-2" copies, /de/ leftovers (301 or 410).
 * English URLs are handled by the languages router (data/routes.php "legacy").
 */
add_action('template_redirect', 'zp_seo_plan_redirect', -1000);

function zp_seo_plan_redirect(): void {
  if (!zp_seo_plan_active() || is_admin() || wp_doing_ajax() || is_preview() || zp_seo_plan_is_en()) { return; }
  if (!in_array($_SERVER['REQUEST_METHOD'] ?? 'GET', ['GET', 'HEAD'], true)) { return; }
  $path = zp_seo_plan_path();
  $to = zp_seo_plan_redirect_target($path, is_404());
  if ($to === null) { return; }
  if ($to === '@410') {
    status_header(410);
    nocache_headers();
    return;
  }
  if ($to === $path || headers_sent()) { return; }
  $query = (string) wp_parse_url((string) ($_SERVER['REQUEST_URI'] ?? ''), PHP_URL_QUERY);
  wp_redirect(home_url($to) . ($query !== '' ? '?' . $query : ''), 301, 'Zaprojektowani SEO 2.3.0');
  exit;
}

/** Target path for a request path, '@410' for a gone URL, or null. */
function zp_seo_plan_redirect_target(string $path, bool $is404): ?string {
  $r = zp_seo_plan_data('redirects');
  if (isset($r['exact'][$path])) { return $r['exact'][$path]; }
  if (!$is404) { return null; }

  // Old URLs mapped by the earlier repair module whose target is now merged: one hop to the final post.
  if (class_exists('ZPSSEO_Repair') && ZPSSEO_Repair::allowed()) {
    $maps = (array) (ZPSSEO_Repair::state()['maps'] ?? []);
    $mapped = $maps[$path]['target'] ?? '';
    if (is_string($mapped) && $mapped !== '' && ($maps[$path]['kind'] ?? '') === 'page') {
      $mapped = zp_seo_plan_path($mapped);
      if (isset($r['exact'][$mapped])) { return $r['exact'][$mapped]; }
    }
  }

  // Leftovers of a removed German version: same page without /de/, or 410.
  if (strpos($path, '/de/') === 0) {
    $rest = substr($path, 3);
    if ($rest === '/' || isset($r['exact'][$rest]) || zp_seo_plan_published_path($rest)) {
      return $r['exact'][$rest] ?? $rest;
    }
    $guess = zp_seo_plan_slug_target($rest, $r);
    return $guess ?? '@410';
  }
  return zp_seo_plan_slug_target($path, $r);
}

/** A 404 URL whose last segment is a merged or existing post slug (old category prefix, "-2" copy). */
function zp_seo_plan_slug_target(string $path, array $r): ?string {
  $parts = array_values(array_filter(explode('/', trim($path, '/')), 'strlen'));
  if (count($parts) < 2 || preg_match('~^(wp-|feed$|page$|en$)~', $parts[0])) { return null; }
  $slug = sanitize_title(end($parts));
  if ($slug === '' || $slug === 'page' || ctype_digit($slug)) { return null; }
  if (isset($r['slugs'][$slug])) { return $r['slugs'][$slug]; }
  foreach ([$slug, preg_replace('~-\d$~', '', $slug)] as $candidate) {
    $found = get_posts(['name' => $candidate, 'post_type' => ['post', 'page'], 'post_status' => 'publish', 'numberposts' => 2, 'fields' => 'ids', 'suppress_filters' => true]);
    if (count($found) === 1) {
      $to = zp_seo_plan_path((string) get_permalink($found[0]));
      if ($to !== $path) { return $to; }
    }
  }
  return null;
}

function zp_seo_plan_published_path(string $path): bool {
  if ($path === '/') { return true; }
  $id = url_to_postid(home_url($path));
  return $id > 0 && get_post_status($id) === 'publish';
}
