<?php
if (!defined('ABSPATH')) { exit; }

/**
 * ZP Suite 2.5.0 — the look of the "Poradnik" sections on the service pages.
 *
 * The guide text stays the same HTML (headings, paragraphs, lists, links), only its layout
 * changes, built from the service pages' own components: every H2 becomes a scope card
 * (number, icon, the first one dark), lists get the package-list check marks, a list whose
 * items open with a bold phrase becomes a row of numbered cards, a sticky column shows a team
 * photo in the team-section style and the table of contents, and halfway down a dark band
 * with a team member links to contact.
 */

/** Team photo and the person in the contact band, per page. */
function zp_seo_guide_setup(string $path): array {
  $map = [
    '/strony-wordpress/'                    => ['team-tablet', 'mateusz'],
    '/opieka-wordpress/'                    => ['team-laptop', 'stanislaw'],
    '/strona-wizytowka/'                    => ['team-pencil', 'marta'],
    '/tworzenie-landing-page/'              => ['team-trio', 'stanislaw'],
    '/strony-internetowe-dla-kancelarii/'   => ['team-duo', 'mateusz'],
    '/strony-internetowe-dla-lekarzy/'      => ['team-laptop', 'marta'],
    '/strony-internetowe-dla-deweloperow/'  => ['team-trio', 'mateusz'],
    '/tworzenie-sklepow-internetowych/'     => ['team-tablet', 'mateusz'],
  ];
  $s = $map[$path] ?? ['team-tablet', 'mateusz'];
  return (array) apply_filters('zp_seo_guide_setup', ['photo' => $s[0], 'person' => $s[1]], $path);
}

function zp_seo_guide_icon(int $i): string {
  $paths = [
    '<circle cx="12" cy="12" r="8"/><circle cx="12" cy="12" r="3"/><path d="M12 2v2m0 16v2M2 12h2m16 0h2"/>',
    '<path d="M4 4h16v16H4zM8 15l3-3 2 2 3-4 3 5"/><circle cx="9" cy="9" r="1"/>',
    '<path d="M12 2v4m0 12v4M4.9 4.9l2.8 2.8m8.6 8.6 2.8 2.8M2 12h4m12 0h4M4.9 19.1l2.8-2.8m8.6-8.6 2.8-2.8"/><circle cx="12" cy="12" r="4"/>',
    '<path d="M13 2 3 14h7l-1 8 10-12h-7z"/>',
    '<path d="M4 19V9m5 10V5m5 14v-7m5 7V3"/>',
    '<path d="m4 16 4-4 4 3 7-8M19 7v5m0-5h-5"/>',
    '<path d="M4 5h16v14H4zM8 9h8M8 13h5"/>',
  ];
  return '<svg viewBox="0 0 24 24" aria-hidden="true">' . $paths[$i % count($paths)] . '</svg>';
}

/** [intro html, [['id','title','html'], …]] — the body split at its H2 headings. */
function zp_seo_guide_split(string $body): array {
  $parts = preg_split('~(<h2\b[^>]*>.*?</h2>)~is', $body, -1, PREG_SPLIT_DELIM_CAPTURE);
  if (!is_array($parts)) { return [$body, []]; }
  $intro = trim((string) array_shift($parts));
  $chunks = [];
  $used = [];
  for ($i = 0; $i < count($parts); $i += 2) {
    if (!preg_match('~<h2\b([^>]*)>(.*?)</h2>~is', $parts[$i], $m)) { continue; }
    $title = trim($m[2]);
    $id = preg_match('~\sid=(["\'])([^"\']+)\1~', $m[1], $im) ? $im[2] : sanitize_title(wp_strip_all_tags($title));
    if ($id === '') { $id = 'temat'; }
    $base = $id;
    for ($n = 2; isset($used[$id]); $n++) { $id = $base . '-' . $n; }
    $used[$id] = true;
    $chunks[] = ['id' => $id, 'title' => $title, 'html' => trim((string) ($parts[$i + 1] ?? ''))];
  }
  return [$intro, $chunks];
}

/** Lists: items that all open with a bold phrase become numbered cards, other lists get check marks or step numbers. */
function zp_seo_guide_lists(string $html): string {
  $out = preg_replace_callback('~<(ul|ol)\b[^>]*>(.*?)</\1>~is', static function ($m) {
    $tag = strtolower($m[1]);
    if (!preg_match_all('~<li\b[^>]*>(.*?)</li>~is', $m[2], $li)) { return $m[0]; }
    $bold = 0;
    foreach ($li[1] as $item) { if (preg_match('~^\s*<(strong|b)>~i', $item)) { $bold++; } }
    if ($bold === count($li[1]) && $bold >= 2) { $class = 'zpGuide__features'; }
    else { $class = $tag === 'ol' ? 'zpGuide__steps' : 'zpGuide__checks'; }
    return '<' . $tag . ' class="' . $class . '">' . $m[2] . '</' . $tag . '>';
  }, $html);
  return is_string($out) ? $out : $html;
}

function zp_seo_guide_minutes(string $html): int {
  $words = str_word_count(wp_strip_all_tags($html), 0, 'ąćęłńóśźżĄĆĘŁŃÓŚŹŻ0123456789');
  return max(2, (int) round($words / 200));
}

function zp_seo_guide_topics(int $n): string {
  if ($n === 1) { return '1 temat'; }
  $last = $n % 10;
  $teens = $n % 100 >= 12 && $n % 100 <= 14;
  return $n . (($last >= 2 && $last <= 4 && !$teens) ? ' tematy' : ' tematów');
}

/**
 * The guide body: $body is the guide's HTML (H2 sections), $path the page it sits on.
 * Returns the cards, the sticky column and the contact band; the section heading stays with the caller.
 */
function zp_seo_guide_body(string $body, string $path): string {
  [$intro, $chunks] = zp_seo_guide_split($body);
  if (!$chunks) {
    return '<div class="zpGuide"><div class="zpGuide__main"><article class="zpGuide__card zpGuide__card--intro"><div class="zpGuide__text">'
      . zp_seo_guide_lists($body) . '</div></article></div></div>';
  }
  $setup = zp_seo_guide_setup($path);
  $team = function_exists('zp_seo_plan_team') ? zp_seo_plan_team() : [];
  $person = $team[$setup['person']] ?? ['name' => 'Mateusz', 'job' => 'Web / UX / strategia'];
  $img = ZP_SUITE_URL . 'assets/img/';
  $total = count($chunks);
  $two = static function (int $n): string { return str_pad((string) $n, 2, '0', STR_PAD_LEFT); };

  $cards = [];
  if (trim(wp_strip_all_tags($intro)) !== '') {
    $cards[] = '<article class="zpGuide__card zpGuide__card--intro"><div class="zpGuide__text">' . zp_seo_guide_lists($intro) . '</div></article>';
  }
  foreach ($chunks as $i => $c) {
    $cards[] = '<article class="zpGuide__card' . ($i === 0 ? ' zpGuide__card--accent' : '') . '" data-step="' . $two($i + 1) . '">'
      . '<div class="zpGuide__cardHead"><span class="zpGuide__num">' . $two($i + 1) . ' / ' . $two($total) . '</span>'
      . '<span class="zpGuide__icon">' . zp_seo_guide_icon($i) . '</span></div>'
      . '<div class="zpGuide__cardBody"><h2 id="' . esc_attr($c['id']) . '">' . $c['title'] . '</h2>'
      . '<div class="zpGuide__text">' . zp_seo_guide_lists($c['html']) . '</div></div></article>';
  }

  $band = '<aside class="zpGuide__band" aria-label="Kontakt z zespołem">'
    . '<img class="zpGuide__bandSignet" src="' . esc_url(ZP_SUITE_URL . 'assets/logo-branding/zp-sygnet-ciemny.webp') . '" alt="" aria-hidden="true" loading="lazy" decoding="async">'
    . '<img class="zpGuide__bandPerson" src="' . esc_url($img . 'hero-team/crew-' . $setup['person'] . '.webp') . '" alt="' . esc_attr($person['name'] . ' z zespołu Zaprojektowani') . '" width="322" height="440" loading="lazy" decoding="async">'
    . '<div class="zpGuide__bandCopy"><span class="zpGuide__bandKicker">Masz pytanie do tego tematu?</span>'
    . '<strong class="zpGuide__bandTitle">Opisz swoją sytuację, a <b>' . esc_html($person['name']) . ' podpowie, od czego zacząć.</b></strong>'
    . '<span class="zpGuide__bandRole">' . esc_html($person['name'] . ' • ' . $person['job']) . '</span></div>'
    . '<div class="zpGuide__bandActions"><a class="zpGuide__btn zpGuide__btn--light" href="' . esc_url(home_url('/kontakt/')) . '">Poznajmy Twój cel <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 17 17 7M7 7h10v10"/></svg></a>'
    . '<a class="zpGuide__btn zpGuide__btn--ghost" href="' . esc_url(home_url('/studio-wyceny/')) . '">Studio Wyceny</a></div></aside>';
  // The band sits after the middle card (not before the first two, not after the last one).
  $after = $total >= 4 ? (int) floor($total / 2) : $total;
  $offset = count($cards) - $total;
  array_splice($cards, $offset + $after, 0, [$band]);

  $toc = '';
  foreach ($chunks as $i => $c) {
    $toc .= '<li><a href="#' . esc_attr($c['id']) . '"><span>' . $two($i + 1) . '</span>' . esc_html(wp_strip_all_tags($c['title'])) . '</a></li>';
  }
  $aside = '<aside class="zpGuide__aside" aria-label="Poradnik w skrócie">'
    . '<figure class="zpGuide__photo"><img class="zpGuide__signet" src="' . esc_url(ZP_SUITE_URL . 'assets/logo-branding/zp-sygnet-ciemny.webp') . '" alt="" aria-hidden="true" loading="lazy" decoding="async">'
    . '<img class="zpGuide__team" src="' . esc_url($img . 'guide/' . $setup['photo'] . '.webp') . '" alt="Zespół Zaprojektowani" loading="lazy" decoding="async">'
    . '<figcaption class="zpGuide__note"><span>Z praktyki studia</span><strong>Tak odpowiadamy klientom przed startem projektu.</strong></figcaption>'
    . '<div class="zpGuide__meta"><span>~' . zp_seo_guide_minutes($body) . ' min czytania</span><span>' . zp_seo_guide_topics($total) . '</span></div></figure>'
    . '<nav class="zpGuide__toc" aria-label="Spis treści poradnika"><strong>Spis treści</strong><ol>' . $toc . '</ol></nav></aside>';

  return '<div class="zpGuide">' . $aside . '<div class="zpGuide__main">' . implode('', $cards) . '</div></div>';
}

/** Stylesheet of the guide, printed inline on the pages that have one. */
function zp_seo_guide_css(): string {
  static $css = null;
  if ($css === null) {
    $raw = (string) @file_get_contents(__DIR__ . '/guide.css');
    $raw = (string) preg_replace('~/\*.*?\*/~s', '', $raw);
    $css = trim((string) preg_replace(['~\s+~', '~\s*([{};,>])\s*~'], [' ', '$1'], $raw));
  }
  return $css;
}

/**
 * Text of a post shown as a guide. The live posts use the article layout (article.zpArticleNew):
 * its hero, table of contents, CTA blocks, FAQ and small labels make no sense inside another page,
 * so they go; its cards, steps, boxes, quotes, tables and images get the guide classes; every other
 * class, id, style and data attribute is dropped.
 */
function zp_seo_guide_clean_post_html(string $html): string {
  if (!class_exists('DOMDocument') || trim($html) === '') { return $html; }
  $prev = libxml_use_internal_errors(true);
  $doc = new DOMDocument('1.0', 'UTF-8');
  $ok = $doc->loadHTML('<?xml encoding="UTF-8"><div id="zp-guide-root">' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
  libxml_clear_errors();
  libxml_use_internal_errors($prev);
  if (!$ok) { return $html; }
  $xp = new DOMXPath($doc);
  $cls = static function (DOMElement $n): string { return ' ' . strtolower(trim((string) $n->getAttribute('class'))) . ' '; };
  $remove = static function ($n) { if ($n && $n->parentNode) { $n->parentNode->removeChild($n); } };
  $unwrap = static function (DOMElement $n) {
    $parent = $n->parentNode;
    if (!$parent) { return; }
    while ($n->firstChild) { $parent->insertBefore($n->firstChild, $n); }
    $parent->removeChild($n);
  };

  foreach (iterator_to_array($xp->query('//script|//style|//noscript|//template|//form|//iframe|//h1|//nav')) as $n) { $remove($n); }
  // FAQ items (the page has its own FAQ section) and the article's own blocks.
  foreach (iterator_to_array($xp->query('//details | //*[@id="faq"]')) as $n) { $remove($n); }
  foreach (iterator_to_array($xp->query('//*[@class]')) as $n) {
    if (!$n->parentNode) { continue; }
    if (preg_match('~(__hero\b|herocard|__eyebrow|__label\b|toc\b|__toc|cta\b|cta__|__faq|faq\b|__share|__related|__author|__meta\b|breadcrumb|__answerlead)~', $cls($n))) { $remove($n); }
  }
  // An article section left with nothing but its heading (it held only FAQ items) goes too.
  foreach (iterator_to_array($xp->query('//section')) as $n) {
    if (!$n->parentNode) { continue; }
    $text = '';
    foreach ($n->childNodes as $child) {
      if (!($child instanceof DOMElement && preg_match('~^h[2-6]$~', $child->nodeName))) { $text .= $child->textContent; }
    }
    if (trim($text) === '' && $xp->query('.//img', $n)->length === 0) { $remove($n); }
  }
  $map = [
    '__compare ' => 'zpGuide__compare', '__steps ' => 'zpGuide__stepsGrid', '__simplelist ' => 'zpGuide__box', '__softbox ' => 'zpGuide__box',
    '__note ' => 'zpGuide__noteBox', '__tablewrap ' => 'zpGuide__table', '__imageblock' => 'zpGuide__figure',
  ];
  foreach (iterator_to_array($xp->query('//*')) as $n) {
    if (!$n instanceof DOMElement || $n->getAttribute('id') === 'zp-guide-root') { continue; }
    $c = $cls($n);
    $keep = '';
    foreach ($map as $needle => $class) { if (strpos($c, $needle) !== false) { $keep = $class; break; } }
    if ($keep === '' && preg_match('~ zparticlenew(?:__layout|__main|__section)? ~', $c) && in_array($n->nodeName, ['article', 'div', 'main', 'section'], true)) {
      $unwrap($n);
      continue;
    }
    if ($keep === '' && $n->nodeName === 'main') { $unwrap($n); continue; }
    foreach (iterator_to_array($n->attributes) as $a) {
      if (in_array($a->nodeName, ['href', 'src', 'srcset', 'sizes', 'alt', 'width', 'height', 'target', 'rel', 'loading', 'decoding', 'colspan', 'rowspan', 'scope'], true)) { continue; }
      $n->removeAttribute($a->nodeName);
    }
    if ($keep !== '') { $n->setAttribute('class', $keep); }
  }
  $root = $doc->getElementById('zp-guide-root');
  if (!$root) { return $html; }
  $out = '';
  foreach ($root->childNodes as $child) { $out .= $doc->saveHTML($child); }
  return $out;
}

/** True on a page that shows a guide section (content pages and the nationwide shop page). */
function zp_seo_guide_page(): bool {
  $variant = zp_seo_service_variant();
  if ($variant === 'sklepy-national') { return true; }
  $page = $variant !== '' ? zp_seo_content_page_by_variant($variant) : null;
  return !empty($page['guide']);
}
