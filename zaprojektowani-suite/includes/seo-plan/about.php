<?php
if (!defined('ABSPATH')) { exit; }

/**
 * ZP Suite 2.7.1 — O nas (/o-nas/).
 *
 * The page had no link to any service page and a schema graph made for an article: "Article" written by
 * the WordPress user "admin". Now:
 *  - four of the six cards in "Jeden zespół zamiast pięciu przypadkowych wykonawców" link to their
 *    nationwide service pages (the whole card is the link; the text stays the same, a small arrow and
 *    the hover lift of the award cards show that it can be clicked); "SEO & content" and
 *    "Analityka & rozwój" have no page of their own, so they stay as they are,
 *  - the schema says AboutPage about the studio and lists Marta, Mateusz and Stanisław (the same Person
 *    nodes the blog posts point to as authors) instead of the Article by "admin".
 * Title and description come from the plan (data/plan.php). Polish version only.
 */

function zp_seo_about_on(): bool {
  return zp_seo_plan_active() && !zp_seo_plan_is_en() && !is_admin();
}

/** Card heading => service page. */
function zp_seo_about_card_links(): array {
  return [
    'Strony internetowe' => '/tworzenie-stron-internetowych/',
    'Sklepy WooCommerce' => '/tworzenie-sklepow-internetowych/',
    'Logo & branding' => '/projektowanie-logo/',
    'Meta & Google Ads' => '/kampanie-reklamowe/',
  ];
}

function zp_seo_about_apply(string $html): string {
  if (strpos($html, 'zpAboutX__cap') === false) { return $html; }
  $links = zp_seo_about_card_links();
  $arrow = '<span class="zpAboutX__capArrow" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M7 17 17 7M8 7h9v9"/></svg></span>';
  $done = 0;
  $html = (string) preg_replace_callback('~<article class="zpAboutX__cap"([^>]*)>(.*?)</article>~s', static function ($m) use ($links, $arrow, &$done) {
    if (!preg_match('~<h3>(.*?)</h3>~s', $m[2], $h)) { return $m[0]; }
    $label = trim($h[1]);
    $path = $links[html_entity_decode($label, ENT_QUOTES | ENT_HTML5, 'UTF-8')] ?? '';
    if ($path === '' || !zp_seo_plan_path_is_live($path)) { return $m[0]; }
    $done++;
    $body = str_replace($h[0], '<h3><a class="zpAboutX__capLink" href="' . esc_url(home_url($path)) . '">' . $label . '</a></h3>', $m[2]);
    return '<article class="zpAboutX__cap zpAboutX__cap--link"' . $m[1] . '>' . $body . $arrow . '</article>';
  }, $html);
  if (!$done) { return $html; }
  $css = '#zpAboutX .zpAboutX__cap--link{position:relative;transition:opacity .78s var(--ease),transform .5s var(--ease),box-shadow .32s ease,border-color .32s ease}'
    . '#zpAboutX .zpAboutX__cap--link:hover{transform:translateY(-6px);box-shadow:0 24px 60px rgba(7,20,38,.07);border-color:rgba(28,71,122,.18)}'
    . '#zpAboutX .zpAboutX__capLink{color:inherit;text-decoration:none}'
    . '#zpAboutX .zpAboutX__capLink::after{content:"";position:absolute;inset:0;z-index:1;border-radius:26px}'
    . '#zpAboutX .zpAboutX__capLink:focus{outline:none}'
    . '#zpAboutX .zpAboutX__capLink:focus-visible::after{outline:2px solid var(--blue);outline-offset:3px}'
    . '#zpAboutX .zpAboutX__capArrow{position:absolute;top:34px;right:27px;display:grid;place-items:center;width:34px;height:34px;border:1px solid var(--line);border-radius:50%;color:var(--blue);transition:background .32s ease,border-color .32s ease,color .32s ease}'
    . '#zpAboutX .zpAboutX__capArrow svg{width:15px;height:15px;fill:none;stroke:currentColor;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}'
    . '#zpAboutX .zpAboutX__cap--link:hover .zpAboutX__capArrow,#zpAboutX .zpAboutX__cap--link:focus-within .zpAboutX__capArrow{background:var(--navy2);border-color:var(--navy2);color:#fff}';
  return $html . '<style id="zp-seo-about-links">' . $css . '</style>';
}

add_filter('do_shortcode_tag', function ($output, $tag) {
  if (!in_array($tag, ['zp_o_nas', 'zp_page_o_nas', 'zp_about_page'], true) || !is_string($output) || !zp_seo_about_on()) { return $output; }
  return zp_seo_about_apply($output);
}, 20, 2);

/** O nas in Rank Math's graph: AboutPage about the studio, the team instead of the Article by "admin". */
function zp_seo_about_graph(array $data): array {
  $org = ['@id' => home_url('/') . '#organization'];
  $drop = [];
  foreach ($data as $key => $node) {
    if (!is_array($node)) { continue; }
    $types = array_map('strval', (array) ($node['@type'] ?? []));
    if (array_intersect($types, ['Article', 'BlogPosting', 'NewsArticle'])) {
      if (!empty($node['author']['@id'])) { $drop[] = (string) $node['author']['@id']; }
      unset($data[$key]);
    }
  }
  foreach ($data as $key => $node) {
    if (!is_array($node)) { continue; }
    $types = array_map('strval', (array) ($node['@type'] ?? []));
    if (in_array('Person', $types, true) && in_array((string) ($node['@id'] ?? ''), $drop, true)) { unset($data[$key]); continue; }
    if (in_array('WebPage', $types, true)) {
      $data[$key]['@type'] = 'AboutPage';
      $data[$key]['about'] = $org;
      $data[$key]['mainEntity'] = $org;
    }
  }
  if (function_exists('zp_seo_plan_team') && function_exists('zp_seo_plan_person_node')) {
    foreach (zp_seo_plan_team() as $slug => $person) { $data['zpTeam' . ucfirst($slug)] = zp_seo_plan_person_node($person); }
  }
  return $data;
}

add_filter('rank_math/json_ld', function ($data, $jsonld = null) {
  if (!is_array($data) || !zp_seo_about_on() || !is_page('o-nas')) { return $data; }
  return zp_seo_about_graph($data);
}, 99, 2);
