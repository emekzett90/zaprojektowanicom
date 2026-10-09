<?php
/**
 * 2.9.11 — a new article reaches search engines on its own (Mat 8.10: after an article is added, get it
 * indexed). Already done before this file: IndexNow on publish (ai.php: Bing and so ChatGPT and Copilot,
 * Yandex, Seznam, Naver), the post in Rank Math's sitemap with its date, the article in /llms.txt, and the
 * older posts that link to it get a fresh date (content feed). Added here, for Google:
 *  - WebSub: the main RSS and Atom feeds name Google's public hub (pubsubhubbub.appspot.com), and a new post
 *    makes the plugin tell the hub, a couple of minutes later, that both feeds changed. Google recommends
 *    WebSub next to sitemaps for fresh content. It is a hint for crawling, not an indexing request.
 *  - /wiedza/ lists the newest articles, so it gets a fresh sitemap date and goes to IndexNow with the
 *    article: a crawler that re-reads it finds the new link.
 * Left out on purpose: Google's Indexing API only accepts job postings and live streams, Search Console's
 * "Request indexing" has no public API, and automatic submissions to web or article directories count as
 * link spam.
 */
if (!defined('ABSPATH')) { exit; }

const ZP_WEBSUB_HUB = 'https://pubsubhubbub.appspot.com/';

/** The feeds a new post changes: the main RSS and Atom feeds. */
function zp_websub_feeds(): array {
  return array_values(array_unique(array_filter([(string) get_bloginfo('rss2_url'), (string) get_bloginfo('atom_url')])));
}

/** Only the main feeds name the hub: they are the ones the plugin announces. */
function zp_websub_main_feed(): bool {
  return zp_ai_on() && !is_archive() && !is_search() && !is_singular();
}
add_action('rss2_head', function () {
  if (zp_websub_main_feed()) { echo "\t<atom:link rel=\"hub\" href=\"" . esc_url(ZP_WEBSUB_HUB) . "\" />\n"; }
});
add_action('atom_head', function () {
  if (zp_websub_main_feed()) { echo "\t<link rel=\"hub\" href=\"" . esc_url(ZP_WEBSUB_HUB) . "\" />\n"; }
});

// A post goes live (by hand, on its schedule or from the content feed). Noindex, password-protected and
// hidden posts are left alone, as for IndexNow.
add_action('transition_post_status', function ($new, $old, $post) {
  if (!zp_ai_on() || !$post instanceof WP_Post || $new !== 'publish' || $old === 'publish' || $post->post_type !== 'post') { return; }
  if (wp_is_post_revision($post) || wp_is_post_autosave($post) || !zp_ai_post_urls($post)) { return; }
  // A few minutes later, so the feeds and any page cache already show the post when the hub reads them.
  if (!wp_next_scheduled('zp_websub_ping')) { wp_schedule_single_event(time() + 120, 'zp_websub_ping'); }
  zp_ai_changed_at_display(['/wiedza/'], 'nowy wpis ' . zp_seo_plan_path((string) get_permalink($post)));
}, 40, 3);

add_action('zp_websub_ping', function () {
  if (!zp_ai_on()) { return; }
  $out = [];
  foreach (zp_websub_feeds() as $feed) {
    $res = wp_remote_post(ZP_WEBSUB_HUB, ['timeout' => 15, 'body' => ['hub.mode' => 'publish', 'hub.url' => $feed]]);
    $code = is_wp_error($res) ? 0 : (int) wp_remote_retrieve_response_code($res);
    $out[] = zp_seo_plan_path($feed) . ': ' . ($code === 204 ? '204 przyjęte' : ($code ? 'odpowiedź ' . $code : 'błąd połączenia'));
  }
  update_option('zp_websub_last', ['at' => time(), 'msg' => implode(', ', $out)], false);
  zp_ai_log('WebSub (hub Google): ' . implode(', ', $out) . '.');
});

// ZP Suite → Widoczność AI, under IndexNow: what happens with a new article and what stays manual.
add_action('zp_ai_screen_after_indexnow', function () {
  $last = (array) get_option('zp_websub_last', []);
  echo '<h2>Google: nowe wpisy</h2><table class="widefat striped" style="max-width:1100px"><tbody>';
  echo '<tr><th style="width:240px">Mapa witryny</th><td>Nowy wpis jest w mapie od razu, z datą publikacji. /wiedza/ dostaje tę samą datę, bo pokazuje najnowsze wpisy.</td></tr>';
  echo '<tr><th>WebSub (hub Google)</th><td>Automatycznie, kilka minut po publikacji wpisu: kanały ' . esc_html(implode(' i ', array_map('zp_seo_plan_path', zp_websub_feeds()))) . '. To sygnał dla robota Google, nie gwarancja zaindeksowania.</td></tr>';
  echo '<tr><th>Ostatni sygnał</th><td>' . ($last ? esc_html(wp_date('Y-m-d H:i', (int) $last['at']) . ': ' . $last['msg']) : 'jeszcze nie') . '</td></tr>';
  echo '<tr><th>Search Console</th><td>Prośba o zindeksowanie jest tylko ręczna (Sprawdź adres URL → Poproś o zindeksowanie). Google nie udostępnia jej automatom dla zwykłych artykułów.</td></tr>';
  echo '</tbody></table>';
});
