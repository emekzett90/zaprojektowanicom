<?php
if (!defined('ABSPATH')) { exit; }

/**
 * ZP Suite 2.4.0 — content pages from the thread "Treści i podstrony usługowe".
 *
 * Each page (data/pages.php) is a copy of the website template (/strony-internetowe-katowice/),
 * like /tworzenie-stron-internetowych/. The template keeps its look: the pages swap the copy
 * of existing sections (hero, scope cards, process, section headings, FAQ, final CTA), drop the
 * sections they do not need, change the section order and add one guide section in the style
 * of the guide on /tworzenie-sklepow-internetowych/.
 */

function zp_seo_content_pages(): array {
  // 2.9.7: the price list and the industry cards take their places in the order (industries.php).
  static $pages = null;
  if ($pages !== null) { return $pages; }
  if (!function_exists('zp_seo_industry_pages_layout')) { return zp_seo_plan_data('pages'); }
  return $pages = zp_seo_industry_pages_layout(zp_seo_plan_data('pages'));
}

function zp_seo_content_page_by_variant(string $variant): ?array {
  foreach (zp_seo_content_pages() as $path => $page) {
    if (($page['variant'] ?? '') === $variant) { return $page + ['path' => $path]; }
  }
  return null;
}

/** Opening-tag marker of each template section a content page refers to. */
function zp_seo_content_markers(): array {
  return [
    'hero' => 'id="start"', 'manifesto' => 'class="section manifesto"', 'oferta' => 'id="oferta"', 'pakiety' => 'id="pakiety"',
    'portfolio' => 'id="portfolio"', 'client-trust' => 'class="client-trust"', 'branze' => 'id="branze"', 'pointing' => 'class="pointing"',
    'proces' => 'id="proces"', 'analityka' => 'id="analityka"', 'realizacje' => 'id="realizacje"', 'opinie' => 'id="opinie"',
    'zakres' => 'id="zakres"', 'team-section' => 'class="team-section"', 'zasieg' => 'id="zasieg"', 'faq' => 'id="faq"', 'cta' => 'id="next-step"',
  ];
}

/**
 * Site-relative links in the content point to this site. A <span data-zp-live> holds a
 * sentence that links to an article published by the plan: it shows once that article is
 * live and is left out until then (2.6.0).
 */
function zp_seo_content_urls(string $html): string {
  $html = (string) preg_replace_callback('~<span data-zp-live>(.*?)</span>~s', static function ($m) {
    return preg_match('~href="(/[^"]*)"~', $m[1], $href) && zp_seo_plan_link_is_live($href[1]) ? $m[1] : '';
  }, $html);
  return (string) preg_replace_callback('~href="(/[^"]*)"~', static function ($m) {
    return 'href="' . esc_url(home_url($m[1])) . '"';
  }, $html);
}

/** Applies $fn to the HTML of the first section whose opening tag contains $marker. */
function zp_seo_content_in_section(string $html, string $marker, callable $fn): string {
  $range = zp_seo_html_section_range($html, $marker);
  if (!$range) { return $html; }
  $part = substr($html, $range[0], $range[1] - $range[0]);
  return substr($html, 0, $range[0]) . (string) $fn($part) . substr($html, $range[1]);
}

/** Replaces the inner HTML of the first element matched by $open_re (an opening tag) up to $close. */
function zp_seo_content_set_inner(string $html, string $open_re, string $close, string $inner): string {
  $out = preg_replace_callback('~(' . $open_re . ')(.*?)(' . preg_quote($close, '~') . ')~s', static function ($m) use ($inner) {
    return $m[1] . $inner . $m[count($m) - 1];
  }, $html, 1);
  return is_string($out) ? $out : $html;
}

/** Section kicker: keeps the template's icon, swaps the text. */
function zp_seo_content_kicker(string $html, string $text): string {
  if ($text === '') { return $html; }
  $out = preg_replace_callback('~(<p class="section-kicker[^"]*">)((?:\s*<svg\b.*?</svg>)?)(.*?)(</p>)~s', static function ($m) use ($text) {
    return $m[1] . $m[2] . $text . $m[4];
  }, $html, 1);
  return is_string($out) ? $out : $html;
}

/** Kicker, title and lead of a template section. */
function zp_seo_content_head(string $html, array $h): string {
  $html = zp_seo_content_kicker($html, (string) ($h['kicker'] ?? ''));
  if (($h['title'] ?? '') !== '') {
    $html = zp_seo_content_set_inner($html, '<h2 class="(?:section-title|client-trust__title)"[^>]*>', '</h2>', zp_seo_content_urls($h['title']));
  }
  $lead = (string) ($h['lead'] ?? ($h['aside'] ?? ''));
  if ($lead !== '') {
    $lead = zp_seo_content_urls($lead);
    foreach (['<p class="section-lead"[^>]*>', '<p class="section-head__aside[^"]*"[^>]*>', '<p class="client-trust__lead"[^>]*>'] as $re) {
      if (preg_match('~' . $re . '~', $html)) { return zp_seo_content_set_inner($html, $re, '</p>', $lead); }
    }
  }
  return $html;
}

/** Two buttons in a .hero-actions row: label (before the icon) and href. */
function zp_seo_content_buttons(string $html, array $buttons): string {
  $i = 0;
  $out = preg_replace_callback('~<a class="btn ([^"]*)" href="[^"]*">\s*([^<]*?)\s*(<svg\b.*?</svg>)?\s*</a>~s', static function ($m) use ($buttons, &$i) {
    $b = $buttons[$i++] ?? null;
    if (!$b) { return $m[0]; }
    return '<a class="btn ' . $m[1] . '" href="' . esc_url(strpos($b['href'], '/') === 0 ? home_url($b['href']) : $b['href']) . '">' . esc_html($b['label']) . ' ' . ($m[3] ?? '') . '</a>';
  }, $html, count($buttons));
  return is_string($out) ? $out : $html;
}

/** Fills cards in order: $tag_re matches one card, $fields maps a regex for an element to the item key. */
function zp_seo_content_cards(string $html, string $card_re, array $items, array $fields): string {
  $i = 0;
  $out = preg_replace_callback($card_re, static function ($m) use ($items, $fields, &$i) {
    $item = $items[$i++] ?? null;
    if (!$item) { return $m[0]; }
    $card = $m[0];
    foreach ($fields as $key => $pair) {
      if (isset($item[$key]) && $item[$key] !== '') { $card = zp_seo_content_set_inner($card, $pair[0], $pair[1], zp_seo_content_urls($item[$key])); }
    }
    return $card;
  }, $html, count($items));
  return is_string($out) ? $out : $html;
}

function zp_seo_content_guide_html(array $g, string $path = ''): string {
  $id = sanitize_html_class($g['id']);
  return '<section class="section zpSeoPlanGuide" id="' . esc_attr($id) . '" aria-labelledby="' . esc_attr($id) . '-title"><div class="wrap">'
    . '<div class="section-head"><div class="section-head__copy reveal">'
    . '<p class="section-kicker">' . $g['kicker'] . '</p>'
    . '<h2 class="section-title" id="' . esc_attr($id) . '-title">' . $g['title'] . '</h2>'
    . ($g['lead'] !== '' ? '<p class="section-lead">' . zp_seo_content_urls($g['lead']) . '</p>' : '')
    . '</div></div>' . zp_seo_guide_body(zp_seo_content_urls($g['body']), $path) . '</div></section>';
}

/** Kancelarie: the law-firm case study first, the beauty one second (numbers follow the order). */
function zp_seo_content_swap_cases(string $html): string {
  if (!preg_match_all('~<article class="case-card\b~', $html, $m, PREG_OFFSET_CAPTURE) || count($m[0]) !== 2) { return $html; }
  $a = (int) $m[0][0][1];
  $b = (int) $m[0][1][1];
  $card_end = static function (int $from) use ($html): int {
    $depth = 0;
    $pos = $from;
    while (preg_match('~<(/?)article\b[^>]*>~', $html, $t, PREG_OFFSET_CAPTURE, $pos)) {
      $depth += $t[1][0] === '/' ? -1 : 1;
      $pos = (int) $t[0][1] + strlen($t[0][0]);
      if ($depth === 0) { return $pos; }
    }
    return -1;
  };
  $a_end = $card_end($a);
  $b_end = $card_end($b);
  if ($a_end < 0 || $b_end < 0) { return $html; }
  $first = substr($html, $a, $a_end - $a);
  $gap = substr($html, $a_end, $b - $a_end);
  $second = substr($html, $b, $b_end - $b);
  $first = preg_replace('~(<span class="case-index"><i></i>\s*)01 /~', '${1}02 /', $first, 1);
  $second = preg_replace('~(<span class="case-index"><i></i>\s*)02 /~', '${1}01 /', $second, 1);
  return substr($html, 0, $a) . $second . $gap . $first . substr($html, $b_end);
}

/** Moves sections so the listed ones follow the page's order. */
function zp_seo_content_reorder(string $html, array $order, array $markers, string $guide_marker): string {
  $marker_of = static function (string $key) use ($markers, $guide_marker): string { return $key === 'guide' ? $guide_marker : ($markers[$key] ?? ''); };
  $listed = array_values(array_filter(array_map($marker_of, $order)));
  $prev_end = static function (string $key) use (&$html, $marker_of, $markers, $order): int {
    $range = zp_seo_html_section_range($html, $marker_of($key));
    if (!$range) { return -1; }
    // A kept "Zaufali nam" that is not in the order travels with the portfolio.
    if ($key === 'portfolio' && !in_array('client-trust', $order, true)) {
      $trust = zp_seo_html_section_range($html, $markers['client-trust']);
      if ($trust && $trust[0] >= $range[1]) { return $trust[1]; }
    }
    return $range[1];
  };
  for ($i = 1, $n = count($order); $i < $n; $i++) {
    $at = $prev_end($order[$i - 1]);
    $cur = zp_seo_html_section_range($html, $marker_of($order[$i]));
    if ($at < 0 || !$cur) { continue; }
    $in_place = $cur[0] >= $at;
    if ($in_place) {
      foreach ($listed as $marker) {
        $r = zp_seo_html_section_range($html, $marker);
        if ($r && $r[0] >= $at && $r[0] < $cur[0]) { $in_place = false; break; }
      }
    }
    if ($in_place) { continue; }
    $block = substr($html, $cur[0], $cur[1] - $cur[0]);
    $html = substr($html, 0, $cur[0]) . substr($html, $cur[1]);
    if ($cur[0] < $at) { $at -= strlen($block); }
    $html = substr($html, 0, $at) . "\n" . $block . substr($html, $at);
  }
  return $html;
}

function zp_seo_content_transform(string $html, array $page): string {
  $markers = zp_seo_content_markers();
  $L = 'zp_seo_content_urls';

  foreach ($page['drop'] ?? [] as $key) {
    if (isset($markers[$key])) { $html = zp_seo_html_drop_section($html, $markers[$key]); }
  }

  if (!empty($page['hero'])) {
    $h = $page['hero'];
    $html = zp_seo_content_in_section($html, $markers['hero'], static function ($s) use ($h, $L) {
      $s = zp_seo_content_set_inner($s, '<p class="hero-eyebrow">', '</p>', $h['eyebrow']);
      $s = zp_seo_content_set_inner($s, '<h1\b[^>]*>', '</h1>', $h['h1']);
      $s = zp_seo_content_set_inner($s, '<p class="hero-lead">', '</p>', $L(function_exists('zp_seo_plan_apply_projects_count') ? zp_seo_plan_apply_projects_count($h['lead']) : $h['lead']));
      $s = zp_seo_content_in_div($s, 'hero-actions', static function ($d) use ($h) { return zp_seo_content_buttons($d, $h['cta']); });
      $i = 0;
      $s = preg_replace_callback('~(<span class="proof-pill"><i></i>)\s*[^<]*(</span>)~', static function ($m) use ($h, &$i) {
        $pill = $h['pills'][$i++] ?? null;
        return $pill === null ? $m[0] : $m[1] . ' ' . esc_html($pill) . $m[2];
      }, $s, 3);
      return $s;
    });
  }

  if (!empty($page['scope'])) {
    $sc = $page['scope'];
    $html = zp_seo_content_in_section($html, $markers['zakres'], static function ($s) use ($sc) {
      $s = zp_seo_content_head($s, $sc);
      return zp_seo_content_cards($s, '~<article class="scope-card\b.*?</article>~s', $sc['items'], [
        'label' => ['<span class="scope-num">', '</span>'], 'title' => ['<h3>', '</h3>'], 'text' => ['<p>', '</p>'],
      ]);
    });
  }

  if (!empty($page['process'])) {
    $pr = $page['process'];
    $html = zp_seo_content_in_section($html, $markers['proces'], static function ($s) use ($pr) {
      $s = zp_seo_content_head($s, $pr);
      return zp_seo_content_cards($s, '~<article class="process-step\b.*?</article>~s', $pr['items'], [
        'title' => ['<h3>', '</h3>'], 'text' => ['<p>', '</p>'],
      ]);
    });
  }

  foreach ($page['reuse'] ?? [] as $key => $head) {
    if (isset($markers[$key])) {
      $html = zp_seo_content_in_section($html, $markers[$key], static function ($s) use ($head) { return zp_seo_content_head($s, $head); });
    }
  }

  if (!empty($page['swap_cases'])) {
    $html = zp_seo_content_in_section($html, $markers['realizacje'], 'zp_seo_content_swap_cases');
  }

  if (!empty($page['faq'])) {
    $f = $page['faq'];
    $html = zp_seo_content_in_section($html, $markers['faq'], static function ($s) use ($f, $L) {
      $s = zp_seo_content_head($s, $f);
      $items = array_map(static function ($qa) use ($L) { return ['new', $qa[0], $L($qa[1])]; }, $f['items']);
      return zp_seo_faq_rebuild($s, $items, 'details');
    });
  }

  if (!empty($page['cta'])) {
    $c = $page['cta'];
    $html = zp_seo_content_in_section($html, $markers['cta'], static function ($s) use ($c, $L) {
      $s = zp_seo_content_kicker($s, $c['kicker']);
      $s = zp_seo_content_set_inner($s, '<h2>', '</h2>', $c['title']);
      $s = preg_replace_callback('~(</h2>\s*<p>)(.*?)(</p>)~s', static function ($m) use ($c, $L) { return $m[1] . $L($c['text']) . $m[3]; }, $s, 1);
      return zp_seo_content_in_div((string) $s, 'hero-actions', static function ($d) use ($c) { return zp_seo_content_buttons($d, $c['buttons']); });
    });
  }

  $guide_marker = '';
  if (!empty($page['guide'])) {
    $guide_marker = 'id="' . sanitize_html_class($page['guide']['id']) . '"';
    $pos = array_search('guide', $page['order'], true);
    $after = $pos ? ($page['order'][$pos - 1] ?? 'hero') : 'hero';
    $range = zp_seo_html_section_range($html, $markers[$after] ?? $markers['hero']);
    if ($range) { $html = substr($html, 0, $range[1]) . "\n" . zp_seo_content_guide_html($page['guide'], (string) ($page['path'] ?? '')) . substr($html, $range[1]); }
  }

  return zp_seo_content_reorder($html, $page['order'], $markers, $guide_marker);
}

/** Applies $fn to the first <div class="$class"> … </div> (no nested divs inside). */
function zp_seo_content_in_div(string $html, string $class, callable $fn): string {
  $out = preg_replace_callback('~<div class="' . preg_quote($class, '~') . '">.*?</div>~s', static function ($m) use ($fn) { return (string) $fn($m[0]); }, $html, 1);
  return is_string($out) ? $out : $html;
}
