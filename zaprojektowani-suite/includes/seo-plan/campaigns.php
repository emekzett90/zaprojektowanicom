<?php
if (!defined('ABSPATH')) { exit; }

/**
 * /kampanie-reklamowe/ texts from content batch 2 (2.5.0): "cała Polska" above the title,
 * the hidden start of the H1, the lead, links and the price in FAQ 04 and 08, and new FAQ
 * 05, 13 and 14. The page has its own template (templates/page-campaigns-route.php), so the
 * changes are applied to its output while the plan is on; pausing the plan shows the
 * template's original texts again. The English version translates the new texts through
 * the page dictionary (zaprojektowani-languages/data/php/en/p/b728f27768b1.php).
 */
function zp_seo_campaigns_transform(string $html): string {
  if ($html === '' || !zp_seo_plan_active()) { return $html; }
  $missed = [];
  $rows = zp_seo_plan_data('campaigns');
  // SEO audit of 13.10.2026, list B: no H2 named the service. Polish only, so the English page keeps the
  // text its dictionary was made from.
  if (!zp_seo_plan_is_en()) {
    $rows = array_merge($rows, [
      ['what' => 'H2 kanałów', 'old' => '<h2 class="section-title">Google łapie intencję. Meta <strong class="gradient-text">buduje popyt.</strong></h2>', 'new' => '<h2 class="section-title">Google Ads łapie popyt. Meta <strong class="gradient-text">go buduje.</strong></h2>'],
      ['what' => 'H2 procesu', 'old' => '<h2 class="section-title">Od hipotezy do decyzji. <strong>Bez chaosu między kliknięciami.</strong></h2>', 'new' => '<h2 class="section-title">Prowadzenie kampanii Meta Ads. <strong>Bez chaosu między kliknięciami.</strong></h2>'],
      ['what' => 'H2 zakresu', 'old' => '<h2 class="section-title">Nie tylko ustawienia. <strong>Cały system kampanii.</strong></h2>', 'new' => '<h2 class="section-title">Kampanie Facebook Ads <strong>jako cały system.</strong></h2>'],
      ['what' => 'H2 FAQ', 'old' => '<h2 class="section-title">Zanim klikniesz <strong>„uruchom”.</strong></h2>', 'new' => '<h2 class="section-title">Kampanie Meta Ads <strong>bez tajemnic.</strong></h2>'],
    ]);
  }
  foreach ($rows as $r) {
    $out = zp_seo_ws_replace($html, (string) $r['old'], (string) $r['new']);
    if ($out === $html) { $missed[] = (string) $r['what']; }
    $html = $out;
  }
  if ($missed) { error_log('[zp-seo-plan] /kampanie-reklamowe/: not found in template: ' . implode(' | ', $missed)); }
  return $html;
}
