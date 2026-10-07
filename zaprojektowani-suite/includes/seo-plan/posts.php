<?php
if (!defined('ABSPATH')) { exit; }

/**
 * Article fixes applied when a post is displayed (2.5.0), so the Elementor data of Mat's
 * posts stays untouched and pausing the plan shows the posts as they were:
 *  - H1: the migration sets the plan's H1 as the post title, but Mat's articles have their
 *    <h1> written inside the article (article.zpArticleNew), so the visible heading kept the
 *    old text. The first <h1> of the article now shows the post title for posts whose plan
 *    entry sets an H1 (a title edited by hand in WordPress is shown as edited).
 *  - new sections from the content thread (data/sections.php), with a link in the article's
 *    table of contents,
 *  - links in posts that pointed to the wrong page: the old /wiedza/ landing page address
 *    (it redirects to a post about shops) and the "ampanie-…" typo address,
 *  - photos in the article are marked as article images, so they load in full size,
 *  - links from articles the plugin published to newer articles (2.8.0),
 *  - text fixes in articles the plugin published (2.8.2),
 *  - from the feed (feed.php, 2.9.0): new sections and links to new articles in older posts, and
 *    links to feed articles that are not published yet shown as plain text.
 * English pages are left alone.
 */

function zp_seo_posts_link_map(): array {
  $map = [
    '/meta-ads/ampanie-reklamowe-facebook-i-instagram-najczestsze-bledy/' => '/meta-ads/kampanie-reklamowe-facebook-i-instagram-najczestsze-bledy/',
    '/wiedza/jak-przygotowac-brief-do-projektowania-strony-internetowej/' => '/strony-www/jak-przygotowac-brief-do-projektowania-strony-internetowej/',
  ];
  if (zp_seo_plan_link_is_live('/tworzenie-landing-page/')) { $map['/wiedza/landing-page-pod-kampanie-meta-ads/'] = '/tworzenie-landing-page/'; }
  return $map;
}

/**
 * Links inside articles the plugin published (data/articles/), to articles from later
 * content batches: path => [[exact text in the article, the words that become the link, target]].
 * Nothing changes when the text was edited in WordPress or the target is not published.
 */
function zp_seo_posts_inline_links(): array {
  return [
    '/strony-internetowe/rodzaje-stron-internetowych/' => [
      ['<li>wizyty i noclegi: strona z systemem rezerwacji,</li>', 'systemem rezerwacji', '/strony-internetowe/system-rezerwacji-online/'],
    ],
    '/logo-branding/logo-wektorowe-pliki-logo/' => [
      ['Do ulotek, wizytówek, katalogów', 'wizytówek', '/logo-branding/projekt-wizytowki/'],
    ],
  ];
}

/**
 * Text fixes in articles the plugin already published (data/articles/ is used only when a post
 * is created): path => [[exact old text, new text]]. 2.8.2: the website price includes the domain
 * and hosting (Mat, 7.10). Nothing changes when the text was edited in WordPress.
 */
function zp_seo_posts_text_fixes(): array {
  return [
    '/strony-internetowe/jak-stworzyc-strone-internetowa/' => [
      // Cost table, row "Agencja lub freelancer" (cell by cell: WordPress puts line breaks between cells).
      ['<td>projekt, u nas od 3 999 zł</td>', '<td>projekt, u nas od 3 999 zł z domeną i hostingiem</td>'],
      ['<td>domena, hosting i opcjonalnie opieka techniczna</td>', '<td>zwykle domena i hosting (u nas w cenie strony), opcjonalnie opieka techniczna</td>'],
      // FAQ "Ile kosztuje stworzenie strony internetowej?" (answer and JSON-LD).
      ['u nas projekty zaczynają się od 3 999 zł. Szczegóły opisuje poradnik',
       'u nas projekty zaczynają się od 3 999 zł, z domeną i hostingiem w cenie. Szczegóły opisuje poradnik'],
    ],
  ];
}

function zp_seo_posts_transform(string $html): string {
  static $busy = false;
  if ($busy || $html === '' || !zp_seo_plan_active() || zp_seo_plan_is_en() || is_admin() || !is_singular('post')) { return $html; }
  $id = (int) get_queried_object_id();
  if (!$id || (in_the_loop() && get_the_ID() !== $id)) { return $html; }
  $busy = true;
  $path = zp_seo_plan_path((string) get_permalink($id));

  // H1 inside the article.
  $entry = zp_seo_plan_entry($path) ?? [];
  $at = strpos($html, 'class="zpArticleNew');
  if ($at !== false && ($entry['kind'] ?? '') === 'post' && (string) ($entry['h1'] ?? '') !== '') {
    $title = trim((string) get_post_field('post_title', $id));
    if ($title !== '' && preg_match('~(<h1\b[^>]*>)(.*?)(</h1>)~is', $html, $m, PREG_OFFSET_CAPTURE, $at)) {
      $now = trim(html_entity_decode(wp_strip_all_tags($m[2][0]), ENT_QUOTES, 'UTF-8'));
      if ($now !== $title) {
        $html = substr($html, 0, $m[2][1]) . esc_html($title) . substr($html, $m[2][1] + strlen($m[2][0]));
      }
    }
  }

  // New sections and their table of contents links.
  foreach (array_merge((array) (zp_seo_plan_data('sections')[$path] ?? []), zp_feed_sections_for($path)) as $s) {
    $sid = (string) $s['id'];
    if (strpos($html, 'id="' . $sid . '"') !== false || ($range = zp_seo_html_section_range($html, 'zpArticleNew__section')) === null) { continue; }
    $first = preg_match('~\bid="([^"]+)"~', substr($html, $range[0], 300), $fm) ? $fm[1] : '';
    $html = substr($html, 0, $range[1]) . "\n" . (string) $s['html'] . substr($html, $range[1]);
    if ($first !== '' && !empty($s['toc'])) {
      $html = (string) preg_replace('~(<nav\b[^>]*zpArticleNewTOC.*?<a\b[^>]*href="#' . preg_quote($first, '~') . '"[^>]*>.*?</a>)~is', '$1<a href="#' . esc_attr($sid) . '">' . esc_html((string) $s['toc']) . '</a>', $html, 1);
    }
  }

  // Photos in the article: the speed optimizer (includes/optimizer.php) recognises article
  // images by a "zpArticle" class on the <img>. Without it, a photo whose file name contains
  // e.g. "logo" was given the size hint of a small client logo (72px), so browsers loaded a
  // blurry thumbnail. Only the class is added; it carries no styles.
  $html = (string) preg_replace('~(<figure\b[^>]*\bzpArticleNew__imageBlock\b[^>]*>\s*)<img\b(?![^>]*\bclass=)~i', '$1<img class="zpArticleNew__img"', $html);

  // Links to newer articles.
  foreach ((array) (zp_seo_posts_inline_links()[$path] ?? []) as $link) {
    [$text, $words, $to] = $link;
    if (!zp_seo_plan_link_is_live($to) || ($at = strpos($html, $text)) === false) { continue; }
    $linked = (string) preg_replace('~' . preg_quote($words, '~') . '~u', '<a href="' . esc_url(home_url($to)) . '">' . $words . '</a>', $text, 1);
    $html = substr_replace($html, $linked, $at, strlen($text));
  }

  // Links to articles from the feed: only where the exact fragment is still in the post.
  foreach (zp_feed_links_for($path) as $link) {
    [$text, $words, $to] = $link;
    if ($to === $path || !zp_seo_plan_link_is_live($to) || ($at = strpos($html, $text)) === false) { continue; }
    $linked = zp_feed_link_fragment($text, $words, $to);
    if ($linked !== null) { $html = substr_replace($html, $linked, $at, strlen($text)); }
  }
  $html = zp_feed_unlink_pending($html);

  // Text fixes in published articles.
  foreach ((array) (zp_seo_posts_text_fixes()[$path] ?? []) as $fix) {
    $html = str_replace($fix[0], $fix[1], $html);
  }

  // Links to the wrong page.
  foreach (zp_seo_posts_link_map() as $from => $to) {
    $html = (string) preg_replace('~(\shref=["\'])(?:https?://(?:www\.)?zaprojektowani\.com)?' . preg_quote($from, '~') . '(?=[#?"\'])~i', '${1}' . esc_url(home_url($to)), $html);
  }
  $busy = false;
  return $html;
}

add_filter('the_content', 'zp_seo_posts_transform', 20);
add_filter('elementor/frontend/the_content', 'zp_seo_posts_transform', 20);
