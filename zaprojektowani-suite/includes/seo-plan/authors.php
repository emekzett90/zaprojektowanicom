<?php
if (!defined('ABSPATH')) { exit; }

/**
 * ZP Suite 2.4.0 — post authors from the team (audit point 13).
 *
 * Posts were signed "Zaprojektowani", so neither readers nor Google could tell who wrote
 * them. Each post now names a team member by topic (category in its address first):
 * websites, shops, UX and SEO: Mateusz; logo and branding: Marta; ad campaigns: Stanisław.
 * Only the existing byline text and the Article author in Rank Math's schema change; no new
 * elements are added to the page. Posts whose WordPress author has a real name of their own
 * (not the brand) keep it. Pausing the SEO plan restores the old signature.
 */

function zp_seo_plan_team(): array {
  $about = home_url('/o-nas/');
  $img = 'https://zaprojektowani.com/wp-content/uploads/2026/09/';
  return [
    'mateusz' => ['name' => 'Mateusz', 'job' => 'Web / UX / strategia', 'image' => $img . 'mateusz_pointing.webp', 'about' => $about,
      'knows' => ['projektowanie stron internetowych', 'UX', 'WordPress', 'WooCommerce', 'SEO']],
    'marta' => ['name' => 'Marta', 'job' => 'Branding i design', 'image' => $img . 'marta_pointing.webp', 'about' => $about,
      'knows' => ['projektowanie logo', 'identyfikacja wizualna', 'branding']],
    'stanislaw' => ['name' => 'Stanisław', 'job' => 'Performance i rozwój', 'image' => $img . 'stanislaw_pointing.webp', 'about' => $about,
      'knows' => ['kampanie Meta Ads', 'Google Ads', 'analityka']],
  ];
}

/** Team member key for a post: the category in its address decides, then its other categories. */
function zp_seo_plan_post_author_key(int $post_id): string {
  static $memo = [];
  if (isset($memo[$post_id])) { return $memo[$post_id]; }
  $by_cat = [
    'logo-branding' => 'marta', 'logo-i-branding' => 'marta',
    'meta-ads' => 'stanislaw', 'kampanie-reklamowe' => 'stanislaw', 'kampanie-reklamowe-meta-ads' => 'stanislaw', 'reklamy' => 'stanislaw',
  ];
  $slugs = [];
  $first = explode('/', trim((string) wp_parse_url((string) get_permalink($post_id), PHP_URL_PATH), '/'))[0] ?? '';
  if ($first !== '') { $slugs[] = $first; }
  $primary = (int) get_post_meta($post_id, 'rank_math_primary_category', true);
  if ($primary > 0 && ($term = get_term($primary, 'category')) && !is_wp_error($term)) { $slugs[] = $term->slug; }
  $slugs = array_merge($slugs, (array) wp_get_post_categories($post_id, ['fields' => 'slugs']));
  $key = 'mateusz';
  foreach ($slugs as $slug) {
    if (isset($by_cat[$slug])) { $key = $by_cat[$slug]; break; }
    if (in_array($slug, ['strony-internetowe', 'strony-www', 'sklepy-internetowe', 'ux-cro-analityka', 'seo', 'seo-i-konwersja', 'seo-content-marketing'], true)) { break; }
  }
  return $memo[$post_id] = (string) apply_filters('zp_seo_plan_post_author_key', $key, $post_id);
}

/** True when the post's WordPress author is signed with the brand rather than a person. */
function zp_seo_plan_post_has_brand_author(WP_Post $post): bool {
  $user = get_userdata((int) $post->post_author);
  $name = $user ? (string) $user->display_name : '';
  return $name === '' || (bool) preg_match('~zaprojektowani|^admin$~i', $name);
}

/** Team member for the post on screen, or null when the plan is paused or the post has its own author. */
function zp_seo_plan_current_post_author(?int $user_id = null): ?array {
  if (is_admin() || wp_doing_ajax() || (defined('REST_REQUEST') && REST_REQUEST) || !zp_seo_plan_active()) { return null; }
  $post = get_post();
  if (!$post instanceof WP_Post || $post->post_type !== 'post') { return null; }
  if ($user_id !== null && $user_id !== (int) $post->post_author) { return null; }
  if (!zp_seo_plan_post_has_brand_author($post)) { return null; }
  $team = zp_seo_plan_team();
  return $team[zp_seo_plan_post_author_key((int) $post->ID)] ?? null;
}

function zp_seo_plan_byline(array $person): string {
  return $person['name'] . ' | Zaprojektowani';
}

// The existing byline (Elementor post info, theme templates, feeds) reads one of these two.
add_filter('get_the_author_display_name', function ($name, $user_id = 0) {
  $person = zp_seo_plan_current_post_author((int) $user_id);
  return $person ? zp_seo_plan_byline($person) : $name;
}, 20, 2);

add_filter('the_author', function ($name) {
  $person = zp_seo_plan_current_post_author();
  return $person ? zp_seo_plan_byline($person) : $name;
}, 20);

function zp_seo_plan_person_node(array $person): array {
  $id = $person['about'] . '#' . sanitize_title(remove_accents($person['name']));
  return [
    '@type' => 'Person',
    '@id' => $id,
    'name' => $person['name'],
    'jobTitle' => $person['job'],
    'url' => $person['about'],
    'image' => ['@type' => 'ImageObject', 'url' => $person['image']],
    'knowsAbout' => $person['knows'],
    'worksFor' => ['@id' => home_url('/') . '#organization'],
  ];
}

/** Rank Math schema of a post: the article's author becomes the team member. */
function zp_seo_plan_author_graph(array $graph, array $person): array {
  $node = zp_seo_plan_person_node($person);
  $ref = ['@id' => $node['@id'], 'name' => $node['name']];
  $old = [];
  foreach ($graph as $n) {
    if (!is_array($n)) { continue; }
    $types = array_map('strval', (array) ($n['@type'] ?? []));
    if (array_intersect($types, ['Article', 'BlogPosting', 'NewsArticle', 'TechArticle']) && !empty($n['author']['@id'])) {
      $old[] = (string) $n['author']['@id'];
    }
  }
  $out = [];
  $has_article = false;
  foreach ($graph as $key => $n) {
    if (!is_array($n)) { $out[$key] = $n; continue; }
    $types = array_map('strval', (array) ($n['@type'] ?? []));
    // The brand's own author node goes; the team member takes its place.
    if (in_array('Person', $types, true) && in_array((string) ($n['@id'] ?? ''), $old, true)) { continue; }
    if (array_intersect($types, ['Article', 'BlogPosting', 'NewsArticle', 'TechArticle'])) {
      $n['author'] = $ref;
      $has_article = true;
    } elseif (isset($n['author']['@id']) && in_array((string) $n['author']['@id'], $old, true)) {
      $n['author'] = $ref;
    }
    $out[$key] = $n;
  }
  if ($has_article) { $out['zpAuthor'] = $node; }
  return $out;
}

add_filter('rank_math/json_ld', function ($data, $jsonld = null) {
  if (!is_array($data) || !is_singular('post')) { return $data; }
  $person = zp_seo_plan_current_post_author();
  return $person ? zp_seo_plan_author_graph($data, $person) : $data;
}, 99, 2);
