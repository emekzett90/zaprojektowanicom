<?php
/**
 * ZP Suite panel: menu order, shared look of the Command Center and Zapytania screens,
 * and a soft landing for links to screens removed in the panel clean-up.
 */
if (!defined('ABSPATH')) { exit; }

/** Screens removed in the clean-up => where an old link or bookmark lands instead. */
function zp_panel_removed_screens(): array {
  return [
    'zp-suite-leads' => ['page' => 'zp-suite-zapytania', 'typ' => 'form'],
    'zp-studio-orders' => ['page' => 'zp-suite-zapytania', 'typ' => 'studio'],
    'zp-suite-stats' => ['page' => 'zp-suite', 'zp_msg' => 'removed'],
    'zp-suite-pages' => ['page' => 'zp-suite', 'zp_msg' => 'removed'],
    'zp-suite-shop-katowice' => ['page' => 'zp-suite', 'zp_msg' => 'removed'],
    'zp-suite-faq' => ['page' => 'zp-suite', 'zp_msg' => 'removed'],
    'zp-suite-realizacje' => ['page' => 'zp-suite', 'zp_msg' => 'removed'],
    'zp-studio-packages' => ['page' => 'zp-suite', 'zp_msg' => 'removed'],
    'zp-suite-tools' => ['page' => 'zp-suite', 'zp_msg' => 'removed'],
    'zp-suite-redirects' => ['page' => 'zp-suite', 'zp_msg' => 'removed'],
    'zp-suite-images' => ['page' => 'zp-suite', 'zp_msg' => 'removed'],
    'zp-suite-front-polish' => ['page' => 'zp-suite', 'zp_msg' => 'removed'],
    'zp-suite-design' => ['page' => 'zp-suite', 'zp_msg' => 'removed'],
    'zp-suite-front-check' => ['page' => 'zp-suite', 'zp_msg' => 'removed'],
    'zp-suite-crm' => ['page' => 'zp-suite', 'zp_msg' => 'removed'],
    'zp-suite-media-usage' => ['page' => 'zp-suite', 'zp_msg' => 'removed'],
    'zp-suite-history' => ['page' => 'zp-suite', 'zp_msg' => 'removed'],
  ];
}

// WordPress refuses an unregistered page before admin_init runs; this hook fires right before that refusal.
add_action('admin_page_access_denied', function () {
  $page = isset($_GET['page']) ? sanitize_key(wp_unslash($_GET['page'])) : '';
  $map = zp_panel_removed_screens();
  if ($page === '' || !isset($map[$page]) || !current_user_can('manage_options')) { return; }
  wp_safe_redirect(add_query_arg($map[$page], admin_url('admin.php')));
  exit;
});

/** ZP Suite submenu in a fixed order; items not listed keep their place at the end. */
add_action('admin_menu', function () {
  global $submenu;
  if (empty($submenu['zp-suite']) || !is_array($submenu['zp-suite'])) { return; }
  $order = ['zp-suite', 'zp-suite-zapytania', 'zp-suite-home-cms', 'zp-tlumacz-en', 'zpl-languages', 'zp-suite-publikacja', 'zp-suite-ai', 'zp-suite-speed'];
  $rank = array_flip($order);
  $items = array_values($submenu['zp-suite']);
  $pos = [];
  foreach ($items as $i => $item) { $pos[$i] = $rank[$item[2] ?? ''] ?? (count($order) + $i); }
  array_multisort($pos, SORT_ASC, SORT_NUMERIC, $items);
  $submenu['zp-suite'] = $items;
}, PHP_INT_MAX - 10);

/** True on the panel screens that use the shared zpx look. */
function zp_panel_is_screen(): bool {
  $page = isset($_GET['page']) ? sanitize_key(wp_unslash($_GET['page'])) : '';
  return is_admin() && in_array($page, ['zp-suite', 'zp-suite-zapytania', 'zp-suite-home-cms'], true);
}

add_action('admin_head', function () {
  if (!zp_panel_is_screen()) { return; }
  echo '<style id="zp-panel-css">' . zp_panel_css() . '</style>';
});

/** Notice after a redirect (?zp_msg=...). Printed after the header, so it never lands inside the dark hero. */
function zp_panel_notice(): void {
  $msg = isset($_GET['zp_msg']) ? sanitize_key(wp_unslash($_GET['zp_msg'])) : '';
  $n = isset($_GET['zp_n']) ? max(0, (int) $_GET['zp_n']) : 0;
  $texts = [
    'saved' => ['success', 'Zapisano.'],
    'deleted' => ['success', 'Usunięto zapytanie.'],
    'bulk' => ['success', 'Zmieniono zapytania: ' . $n . '.'],
    'bulk-deleted' => ['success', 'Usunięto zapytania: ' . $n . '.'],
    'none' => ['warning', 'Nie zaznaczono żadnego zapytania.'],
    'missing' => ['warning', 'Nie znaleziono tego zapytania. Mogło zostać usunięte w innym oknie.'],
    'removed' => ['info', 'Tego ekranu już nie ma w ZP Suite. Treści stron, które edytował, zostały w bazie i wyglądają tak samo; zmiany w nich robimy teraz w kodzie wtyczki.'],
  ];
  if (!isset($texts[$msg])) { return; }
  printf('<div class="notice notice-%s is-dismissible"><p>%s</p></div>', esc_attr($texts[$msg][0]), esc_html($texts[$msg][1]));
}

function zp_panel_css(): string {
  return <<<'CSS'
.zpx{--ink:#071426;--ink2:#102a4f;--muted:#5d6878;--line:#dfe4ec;--soft:#f6f8fb;--ok:#166534;--okbg:#ecfdf3;--warn:#9a3412;--warnbg:#fff7ed;--bad:#b42318;max-width:1240px;margin:20px 20px 0 2px;color:var(--ink);font-family:Inter,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;font-size:14px}
.zpx *{box-sizing:border-box}
.zpxHead{border-radius:24px;background:radial-gradient(circle at 12% 0%,rgba(28,71,122,.45),transparent 38%),linear-gradient(135deg,#05070b,#071426 58%,#102a4f);color:#fff;padding:26px 28px}
.zpxKicker{display:inline-block;font-size:10px;font-weight:800;letter-spacing:.14em;text-transform:uppercase;color:rgba(255,255,255,.68)}
.zpx .zpxHead h1{color:#fff;font-size:30px;font-weight:800;line-height:1.08;letter-spacing:-.03em;margin:8px 0 6px;padding:0;text-wrap:balance}
.zpxHead p{margin:0;color:rgba(255,255,255,.76);font-size:14px;line-height:1.55;max-width:760px}
.zpxStats{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:10px;margin-top:20px}
.zpxStat{display:block;text-decoration:none;border:1px solid rgba(255,255,255,.14);background:rgba(255,255,255,.06);border-radius:16px;padding:12px 14px;color:#fff;min-width:0}
a.zpxStat:hover,a.zpxStat:focus{background:rgba(255,255,255,.13);color:#fff}
.zpxStat strong{display:block;font-size:24px;line-height:1.1;font-variant-numeric:tabular-nums}
.zpxStat span{display:block;margin-top:5px;font-size:11px;letter-spacing:.07em;text-transform:uppercase;color:rgba(255,255,255,.66)}
.zpxStat.is-hot{border-color:rgba(255,255,255,.6);background:rgba(255,255,255,.15)}
.zpx .wp-header-end{margin:0;border:0;height:0}
.zpx .button{border-radius:999px}
.zpx .button-primary{background:var(--ink);border-color:var(--ink);color:#fff}
.zpx .button-primary:hover,.zpx .button-primary:focus{background:var(--ink2);border-color:var(--ink2);color:#fff}
.zpx>.notice{margin:14px 0 0}
.zpxGrid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px;margin-top:16px;align-items:start}
.zpxCard{background:#fff;border:1px solid var(--line);border-radius:20px;padding:20px 22px;min-width:0}
.zpxCard h2{margin:0 0 12px;font-size:17px;font-weight:800;letter-spacing:-.02em;color:var(--ink)}
.zpxCard p{color:var(--muted);margin:0 0 12px;line-height:1.55}
.zpxCard p:last-child{margin-bottom:0}
.zpxRows{display:grid;gap:8px}
.zpxRow{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:10px;align-items:center;padding:11px 13px;border:1px solid var(--line);border-radius:14px;background:var(--soft);text-decoration:none;color:var(--ink)}
a.zpxRow:hover,a.zpxRow:focus{border-color:#aebbd0;color:var(--ink)}
.zpxRow b{display:block;font-size:14px;overflow-wrap:anywhere}
.zpxRow small{display:block;color:var(--muted);margin-top:2px;line-height:1.45;overflow-wrap:anywhere}
.zpxRowEnd{text-align:right}
.zpxPill{display:inline-flex;align-items:center;border-radius:999px;padding:3px 9px;font-size:11px;font-weight:700;line-height:1.5;white-space:nowrap;background:#e9eef6;color:var(--ink2)}
.zpxPill.is-new{background:var(--ink);color:#fff}
.zpxPill.is-ok{background:var(--okbg);color:var(--ok)}
.zpxPill.is-warn{background:var(--warnbg);color:var(--warn)}
.zpxPill.is-off{background:#f0f1f3;color:#6b7280}
.zpxPill.is-quote{background:#eef2ff;color:#3730a3}
.zpxPill.is-spam{background:#fef3f2;color:var(--bad)}
.zpxMore{display:inline-block;margin-top:12px;font-weight:700;text-decoration:none}
.zpxForm label{display:flex;gap:10px;align-items:flex-start;margin:0 0 12px;line-height:1.45}
.zpxForm label input[type=checkbox]{margin-top:2px}
.zpxForm label small{display:block;color:var(--muted)}
.zpxForm .zpxNum{display:block}
.zpxForm .zpxNum input{width:110px;margin-top:6px}
.zpxEmpty{padding:18px;border:1px dashed var(--line);border-radius:14px;color:var(--muted);background:var(--soft)}
.zpxTabs{display:flex;flex-wrap:wrap;gap:8px;margin:16px 0 0}
.zpxTabs a{display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border:1px solid var(--line);border-radius:999px;background:#fff;color:var(--ink);text-decoration:none;font-weight:700}
.zpxTabs a.is-active{background:var(--ink);border-color:var(--ink);color:#fff}
.zpxTabs a em{font-style:normal;font-weight:600;opacity:.7;font-variant-numeric:tabular-nums}
.zpxTools{display:flex;flex-wrap:wrap;gap:10px;align-items:flex-end;margin:12px 0 0;padding:14px;border:1px solid var(--line);border-radius:16px;background:#fff}
.zpxTools label{display:grid;gap:4px;font-weight:700;font-size:12px;color:var(--muted)}
.zpxTools input[type=search]{min-width:240px}
.zpxTools .zpxGrow{flex:1}
.zpxBulk{display:flex;flex-wrap:wrap;gap:8px;align-items:center;margin:12px 0 0}
.zpxBulk .zpxAll{display:flex;gap:6px;align-items:center;font-weight:700;margin-right:6px}
.zpxList{display:grid;gap:12px;margin-top:12px}
.zpxItem{background:#fff;border:1px solid var(--line);border-radius:18px;padding:16px 18px;min-width:0}
.zpxItem.is-new{border-color:#9fb0c8;box-shadow:inset 4px 0 0 var(--ink)}
.zpxItem:target{box-shadow:0 0 0 3px rgba(28,71,122,.35)}
.zpxItem.is-new:target{box-shadow:inset 4px 0 0 var(--ink),0 0 0 3px rgba(28,71,122,.35)}
.zpxTop{display:grid;grid-template-columns:auto minmax(0,1fr) auto;gap:12px;align-items:start}
.zpxTop input[type=checkbox]{margin-top:4px}
.zpxWho b{display:block;font-size:16px;line-height:1.3;overflow-wrap:anywhere}
.zpxWho small{display:block;color:var(--muted);margin-top:2px;overflow-wrap:anywhere}
.zpxMeta{display:flex;flex-wrap:wrap;gap:6px;justify-content:flex-end;align-items:center}
.zpxMeta time{color:var(--muted);font-size:12px;white-space:nowrap;font-variant-numeric:tabular-nums}
.zpxWhat{margin:10px 0 0;color:var(--ink2);line-height:1.5;overflow-wrap:anywhere}
.zpxQuick{display:flex;flex-wrap:wrap;gap:8px;margin-top:12px}
.zpxQuick .button{display:inline-flex;align-items:center;min-height:36px}
.zpxDetails{margin-top:12px;border-top:1px solid var(--line);padding-top:10px}
.zpxDetails>summary{cursor:pointer;font-weight:700;color:var(--ink2);padding:4px 0}
.zpxBody{display:grid;grid-template-columns:minmax(0,1.5fr) minmax(0,1fr);gap:18px;margin-top:10px}
.zpxMsg{white-space:pre-wrap;line-height:1.6;background:var(--soft);border:1px solid var(--line);border-radius:14px;padding:12px 14px;margin:0 0 12px;overflow-wrap:anywhere}
.zpxDl{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px;margin:0}
.zpxDl div{background:var(--soft);border:1px solid var(--line);border-radius:12px;padding:8px 10px;min-width:0}
.zpxDl div.is-wide{grid-column:1/-1}
.zpxDl dt{font-size:11px;font-weight:700;letter-spacing:.05em;text-transform:uppercase;color:var(--muted)}
.zpxDl dd{margin:3px 0 0;white-space:pre-wrap;overflow-wrap:anywhere}
.zpxFiles{display:flex;flex-wrap:wrap;gap:8px;margin:12px 0 0}
.zpxFiles a{display:inline-flex;align-items:center;gap:6px;padding:6px 10px;border:1px solid var(--line);border-radius:10px;text-decoration:none;background:#fff;max-width:100%;overflow-wrap:anywhere}
.zpxSide{display:grid;gap:12px;align-content:start}
.zpxSide form{display:grid;gap:8px}
.zpxSide label{display:grid;gap:4px;font-weight:700;font-size:12px;color:var(--muted)}
.zpxSide select,.zpxSide textarea{width:100%;max-width:100%}
.zpxSide textarea{min-height:90px}
.zpxLog{margin:0;padding:0;list-style:none;display:grid;gap:4px;color:var(--muted);font-size:12px}
.zpxDelete{color:var(--bad)!important;border-color:#f3c7c2!important;background:#fff!important}
.zpxPager{display:flex;gap:8px;align-items:center;justify-content:center;margin:16px 0 4px;color:var(--muted)}
.zpxHint{display:flex;flex-wrap:wrap;gap:10px;align-items:center;justify-content:space-between;margin:12px 0 0;padding:12px 14px;border:1px solid #fed7aa;border-radius:14px;background:var(--warnbg);color:var(--warn);line-height:1.5}
.zpxHint span{flex:1 1 320px}
.zpxCount{margin-left:auto;color:var(--muted);font-variant-numeric:tabular-nums}
.zpxBackLink{margin:14px 0 0}
.zpxBackLink a{font-weight:700;text-decoration:none}
.zpxId{margin:12px 0 0;color:var(--muted);font-size:12px;overflow-wrap:anywhere}
.zpxQuick .button{max-width:100%;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.zpxWhat strong{color:var(--ink)}
@media(max-width:1100px){.zpxStats{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:960px){.zpxGrid,.zpxBody{grid-template-columns:1fr}}
@media(max-width:782px){.zpx{margin:12px 12px 0 0}.zpxHead{padding:20px 18px;border-radius:20px}.zpx .zpxHead h1{font-size:24px}.zpxStat strong{font-size:20px}.zpxTop{grid-template-columns:auto minmax(0,1fr)}.zpxMeta{grid-column:1/-1;justify-content:flex-start}.zpxDl{grid-template-columns:1fr}.zpxTools input[type=search]{min-width:0;width:100%}.zpxTools label,.zpxTools .zpxGrow{flex:1 1 100%}.zpxCount{flex-basis:100%;margin-left:0}.zpxQuick .button{flex:1 1 auto;justify-content:center}}
CSS;
}
