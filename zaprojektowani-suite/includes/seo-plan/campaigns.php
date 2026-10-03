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
  foreach (zp_seo_plan_data('campaigns') as $r) {
    $out = zp_seo_ws_replace($html, (string) $r['old'], (string) $r['new']);
    if ($out === $html) { $missed[] = (string) $r['what']; }
    $html = $out;
  }
  if ($missed) { error_log('[zp-seo-plan] /kampanie-reklamowe/: not found in template: ' . implode(' | ', $missed)); }
  return $html;
}
