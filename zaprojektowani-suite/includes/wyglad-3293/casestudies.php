<?php
/**
 * ZP Suite 3.2.93 — /case-studies/ hub (wygląd podstron, agent casestudies).
 *
 * Visual polish of the index only, without new effects:
 *  - a numbered fact strip under the hero (numbers derived from the hub's own JSON copy:
 *    public cases, Altiora's three languages, the two custom systems the FAQ names,
 *    the technologies the hub lists),
 *  - image-led project cards: the thumbnail carries a short client · scope · result row
 *    (brand, the card's tags, one delivered outcome from the case),
 *  - calmer section rhythm, a clear portfolio bridge, full-width actions on phones
 *    (assets/css/wyglad-3293/casestudies.css, enqueued on the hub in both languages).
 * The renderer (includes/case-studies-render-20261018.php, zp_cs18_index/zp_cs18_card) calls the two
 * helpers below only when they exist, so the hub degrades to its 3.2.92 markup without this file.
 * The hub is rendered live by templates/page-case-studies-route.php; the stored post_content snapshot
 * is not used for the front end, so no migration is needed.
 */
if (!defined('ABSPATH')) { exit; }

/** Polish plural form helper: 1 → $one, 2–4 (not 12–14) → $few, otherwise $many. */
function zp_w93_casestudies_plural(int $n, string $one, string $few, string $many): string {
  if ($n === 1) { return $one; }
  $m10 = $n % 10; $m100 = $n % 100;
  if ($m10 >= 2 && $m10 <= 4 && ($m100 < 12 || $m100 > 14)) { return $few; }
  return $many;
}

/** Fact strip under the hero. Every number comes from the hub's content; facts without a public source are skipped. */
function zp_w93_casestudies_facts(array $data, string $lang, array $cards): string {
  $en = $lang === 'en';
  $public = static function (string $key): bool {
    return !function_exists('zp_cs18_case_public') || zp_cs18_case_public($key);
  };
  $facts = [];
  $count = count($cards);
  if ($count > 0) {
    $facts[] = [
      $count,
      $en ? ($count === 1 ? 'case study' : 'case studies') : zp_w93_casestudies_plural($count, 'opisana realizacja', 'opisane realizacje', 'opisanych realizacji'),
      $en ? 'Branding, websites, a store and custom systems: from the brand to the launch.' : 'Branding, strony, sklep i własne systemy — od marki po wdrożenie.',
    ];
  }
  if ($public('altiora-linea')) {
    $facts[] = [
      3,
      $en ? 'languages' : 'wersje językowe',
      $en ? 'Altiora Linea: PL / EN / IT in one website structure.' : 'Altiora Linea: PL / EN / IT w jednej strukturze strony.',
    ];
  }
  $systems = (int) $public('siemianowski') + (int) $public('apartament-piekna');
  if ($systems > 0) {
    $facts[] = [
      $systems,
      $en ? ($systems === 1 ? 'custom system' : 'custom systems') : zp_w93_casestudies_plural($systems, 'autorski system', 'autorskie systemy', 'autorskich systemów'),
      $en ? 'An administrator listing system and an in-house appointment request flow.' : 'System ofert syndyka i własna ścieżka zgłoszenia wizyty.',
    ];
  }
  $tools = count($data['index']['technologies'] ?? []);
  if ($tools > 0) {
    $facts[] = [
      $tools,
      $en ? ($tools === 1 ? 'technology' : 'technologies') : zp_w93_casestudies_plural($tools, 'technologia', 'technologie', 'technologii'),
      $en ? 'Each with a specific role in the project, described below.' : 'Każda z konkretną rolą w projekcie, opisaną niżej.',
    ];
  }
  if (!$facts) { return ''; }
  $out = '<section class="zpCS93__facts" aria-label="' . esc_attr($en ? 'The projects in numbers' : 'Realizacje w liczbach') . '"><div class="zpCS18__inner"><ul class="zpCS93__factList">';
  foreach ($facts as $fact) {
    $out .= '<li><b>' . esc_html(sprintf('%02d', $fact[0])) . '</b><span>' . esc_html($fact[1]) . '</span><small>' . esc_html($fact[2]) . '</small></li>';
  }
  return $out . '</ul></div></section>';
}

/** Client · scope · result row under a project thumbnail. Scope = the card's tags, result = one delivered outcome of the case. */
function zp_w93_casestudies_card_meta(array $case, array $card, string $lang): string {
  $en = $lang === 'en';
  $brand = (string) ($case['brand'] ?? '');
  $tags = array_values(array_filter(array_map('strval', (array) ($card['tags'] ?? [])), 'strlen'));
  $outcomes = (array) ($case[$lang]['outcomes'] ?? []);
  // One outcome per case that is not already its scope tag (Altiora's first outcome repeats "PL / EN / IT").
  $pick = ['siemianowski' => 2, 'apartament-piekna' => 1, 'altiora-linea' => 2, 'swiat-grilli' => 2];
  $index = $pick[$case['key'] ?? ''] ?? 0;
  $result = (string) ($outcomes[$index][0] ?? $outcomes[0][0] ?? '');
  if ($brand === '' && !$tags && $result === '') { return ''; }
  $out = '<dl class="zpCS93__meta">';
  if ($brand !== '') { $out .= '<div><dt>' . esc_html($en ? 'Client' : 'Klient') . '</dt><dd>' . esc_html($brand) . '</dd></div>'; }
  if ($tags) { $out .= '<div><dt>' . esc_html($en ? 'Scope' : 'Zakres') . '</dt><dd>' . esc_html(implode(' · ', $tags)) . '</dd></div>'; }
  if ($result !== '') { $out .= '<div><dt>' . esc_html($en ? 'Result' : 'Efekt') . '</dt><dd>' . esc_html($result) . '</dd></div>'; }
  return $out . '</dl>';
}

/** The hub's stylesheet, after the 3.2.41–3.2.66 case files; PL and EN resolve to the same page key. */
add_action('wp_enqueue_scripts', static function (): void {
  if (!class_exists('ZP_Case_Pages_20261018') || ZP_Case_Pages_20261018::current_key() !== 'index') { return; }
  $deps = wp_style_is('zp-case-studies-20261018', 'enqueued') ? ['zp-case-studies-20261018'] : [];
  wp_enqueue_style('zp-w93-casestudies', ZP_SUITE_URL . 'assets/css/wyglad-3293/casestudies.css', $deps, ZP_SUITE_VERSION);
}, 120);
