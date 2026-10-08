<?php
if (!defined('ABSPATH')) { exit; }

/* =========================================================
 * ZAPROJEKTOWANI SUITE v1.7.0
 * CMS Stabilization + Design System + Analytics Pro
 * ========================================================= */

function zp_suite_170_design_defaults(){
  return [
    'navy' => '#071426',
    'navy_dark' => '#05070b',
    'navy_mid' => '#102a4f',
    'blue' => '#1c477a',
    'light_bg' => '#f6f8fb',
    'text' => '#071426',
    'radius_button' => '999px',
    'radius_card' => '24px',
    'button_style' => 'unified',
    'animations' => '1',
    'safe_mode' => '0',
    'editor_mode' => 'production',
    'no_pink_guard' => '1',
  ];
}

function zp_suite_170_design(){
  $saved = get_option('zp_suite_design_system', []);
  if (!is_array($saved)) $saved = [];
  return array_merge(zp_suite_170_design_defaults(), $saved);
}

function zp_suite_170_sanitize_color($v, $fallback){
  $v = trim((string)$v);
  return preg_match('/^#[0-9a-fA-F]{6}$/', $v) ? $v : $fallback;
}

function zp_suite_170_log($title, $context = ''){
  $log = get_option('zp_suite_activity_log', []);
  if (!is_array($log)) $log = [];
  array_unshift($log, [
    'at' => current_time('mysql'),
    'title' => sanitize_text_field($title),
    'context' => sanitize_text_field($context),
    'user' => function_exists('wp_get_current_user') ? wp_get_current_user()->user_login : '',
  ]);
  update_option('zp_suite_activity_log', array_slice($log, 0, 120), false);
}

function zp_suite_170_snapshot($reason = 'snapshot'){
  $snaps = get_option('zp_suite_snapshots', []);
  if (!is_array($snaps)) $snaps = [];
  array_unshift($snaps, [
    'at' => current_time('mysql'),
    'reason' => sanitize_text_field($reason),
    'options' => get_option('zp_suite_options', []),
    'cms' => get_option('zp_suite_cms', []),
    'design' => get_option('zp_suite_design_system', []),
  ]);
  update_option('zp_suite_snapshots', array_slice($snaps, 0, 35), false);
}

add_action('admin_init', function(){
  if (!current_user_can('manage_options')) return;
  if (!empty($_POST['zp_suite_save'])) {
    zp_suite_170_snapshot('Automatyczny snapshot przed zapisem CMS');
    zp_suite_170_log('Zapisano ustawienia strony głównej CMS', 'snapshot przed zapisem');
  }
}, 1);

add_action('admin_menu', function(){
  add_submenu_page('zp-suite','Statystyki','Statystyki','manage_options','zp-suite-stats','zp_suite_170_render_stats_page');
  add_submenu_page('zp-suite','Design System','Design System','manage_options','zp-suite-design','zp_suite_170_render_design_page');
  add_submenu_page('zp-suite','Kontrola frontu','Kontrola frontu','manage_options','zp-suite-front-check','zp_suite_170_render_front_check_page');
  add_submenu_page('zp-suite','CRM Pro','CRM Pro','manage_options','zp-suite-crm','zp_suite_170_render_crm_page');
  add_submenu_page('zp-suite','Media użyte','Media użyte','manage_options','zp-suite-media-usage','zp_suite_170_render_media_usage_page');
  add_submenu_page('zp-suite','Historia zmian','Historia zmian','manage_options','zp-suite-history','zp_suite_170_render_history_page');
}, 90);

add_action('admin_init', function(){
  if (!current_user_can('manage_options')) return;
  if (!empty($_POST['zp_suite_design_save']) && check_admin_referer('zp_suite_design_save')) {
    $def = zp_suite_170_design_defaults();
    $raw = isset($_POST['zp_design']) && is_array($_POST['zp_design']) ? wp_unslash($_POST['zp_design']) : [];
    $out = [];
    foreach($def as $k=>$v){
      $val = $raw[$k] ?? $v;
      if (in_array($k, ['navy','navy_dark','navy_mid','blue','light_bg','text'], true)) $out[$k] = zp_suite_170_sanitize_color($val, $v);
      elseif (in_array($k, ['animations','safe_mode','no_pink_guard'], true)) $out[$k] = !empty($val) ? '1' : '0';
      else $out[$k] = sanitize_text_field($val);
    }
    zp_suite_170_snapshot('Snapshot przed zapisem Design System');
    update_option('zp_suite_design_system', $out, false);
    zp_suite_170_log('Zapisano Design System', 'kolory, buttony, tryb pracy');
    wp_safe_redirect(add_query_arg(['page'=>'zp-suite-design','saved'=>'1'], admin_url('admin.php'))); exit;
  }

  if (!empty($_POST['zp_suite_restore_snapshot']) && check_admin_referer('zp_suite_restore_snapshot')) {
    $idx = isset($_POST['snapshot_index']) ? (int) $_POST['snapshot_index'] : -1;
    $snaps = get_option('zp_suite_snapshots', []);
    if (isset($snaps[$idx]) && is_array($snaps[$idx])) {
      zp_suite_170_snapshot('Snapshot przed przywróceniem historii');
      update_option('zp_suite_options', $snaps[$idx]['options'] ?? [], false);
      update_option('zp_suite_cms', $snaps[$idx]['cms'] ?? [], false);
      update_option('zp_suite_design_system', $snaps[$idx]['design'] ?? [], false);
      zp_suite_170_log('Przywrócono snapshot CMS', $snaps[$idx]['at'] ?? '');
    }
    wp_safe_redirect(add_query_arg(['page'=>'zp-suite-history','restored'=>'1'], admin_url('admin.php'))); exit;
  }

  if (!empty($_POST['zp_suite_crm_save']) && check_admin_referer('zp_suite_crm_save')) {
    $leads = function_exists('zp_suite_leads_all') ? zp_suite_leads_all() : [];
    $raw = isset($_POST['lead']) && is_array($_POST['lead']) ? wp_unslash($_POST['lead']) : [];
    foreach($leads as $i=>$lead){
      $id = $lead['id'] ?? '';
      if (!$id || empty($raw[$id]) || !is_array($raw[$id])) continue;
      $r = $raw[$id];
      $leads[$i]['status'] = sanitize_key($r['status'] ?? ($lead['status'] ?? 'new'));
      $leads[$i]['priority'] = sanitize_key($r['priority'] ?? ($lead['priority'] ?? 'medium'));
      $leads[$i]['tags'] = sanitize_text_field($r['tags'] ?? ($lead['tags'] ?? ''));
      $leads[$i]['reminder'] = sanitize_text_field($r['reminder'] ?? ($lead['reminder'] ?? ''));
      $leads[$i]['note'] = sanitize_textarea_field($r['note'] ?? ($lead['note'] ?? ''));
      $leads[$i]['updated_at'] = current_time('mysql');
    }
    update_option('zp_suite_leads', $leads, false);
    zp_suite_170_log('Zapisano CRM leadów', 'statusy, priorytety, notatki');
    wp_safe_redirect(add_query_arg(['page'=>'zp-suite-crm','saved'=>'1'], admin_url('admin.php'))); exit;
  }
});

/* ---------- Front global design system ---------- */
add_action('wp_head', function(){
  if (is_admin()) return;
  $d = zp_suite_170_design();
  $navy = esc_html($d['navy']); $dark = esc_html($d['navy_dark']); $mid = esc_html($d['navy_mid']); $blue = esc_html($d['blue']); $radius = esc_html($d['radius_button']);
  ?>
  <script id="zp-safe-loader-head">document.documentElement.classList.add('zp-loading');</script>
  <style id="zp-suite-design-system-front">
    :root{--zpDSNavy:<?php echo $navy; ?>;--zpDSDark:<?php echo $dark; ?>;--zpDSMid:<?php echo $mid; ?>;--zpDSBlue:<?php echo $blue; ?>;--zpDSGrad:linear-gradient(90deg,<?php echo $dark; ?> 0%,#0b1830 42%,<?php echo $mid; ?> 72%,<?php echo $blue; ?> 100%);--zpDSRadius:<?php echo $radius; ?>;}
    html.zp-loading body{background:#fff;}
    body [class^="zp"] a[class*="btn"],body [class*=" zp"] a[class*="btn"],body [class^="zp"] button,body [class*=" zp"] button,.zp404__btn,.zpNewNav__cta,.zpNewHero__btn,.zpServicesPath__btn,.zpContactSystemLight__submit{border-radius:var(--zpDSRadius)!important;transition:color .22s ease,border-color .22s ease,background .22s ease,filter .22s ease!important;transform:none!important;box-shadow:none!important;}
    body [class^="zp"] a[class*="btn"]:hover,body [class*=" zp"] a[class*="btn"]:hover,body [class^="zp"] button:hover,body [class*=" zp"] button:hover,.zp404__btn:hover,.zpNewNav__cta:hover,.zpNewHero__btn:hover,.zpServicesPath__btn:hover,.zpContactSystemLight__submit:hover{transform:none!important;box-shadow:none!important;}
    .zpNewNav__cta,.zpNewHero__btn--primary,.zpServicesPath__btn,.zp404__btn--primary,.zpHomeAuditCta__btn,.zpContactSystemLight__submit{background:#fff!important;color:var(--zpDSNavy)!important;border-color:rgba(7,20,38,.18)!important;position:relative!important;overflow:hidden!important;}
    .zpNewNav__cta:before,.zpNewHero__btn--primary:before,.zpServicesPath__btn:before,.zp404__btn--primary:before,.zpHomeAuditCta__btn:before,.zpContactSystemLight__submit:before{content:"";position:absolute;inset:0;z-index:-1;background:var(--zpDSGrad);transform:scaleX(0);transform-origin:left center;transition:transform .28s cubic-bezier(.16,1,.3,1);}
    .zpNewNav__cta:hover:before,.zpNewHero__btn--primary:hover:before,.zpServicesPath__btn:hover:before,.zp404__btn--primary:hover:before,.zpHomeAuditCta__btn:hover:before,.zpContactSystemLight__submit:hover:before{transform:scaleX(1);}
    .zpNewNav__cta:hover,.zpNewHero__btn--primary:hover,.zpServicesPath__btn:hover,.zp404__btn--primary:hover,.zpHomeAuditCta__btn:hover,.zpContactSystemLight__submit:hover{color:#fff!important;border-color:var(--zpDSMid)!important;background:var(--zpDSDark)!important;}
    .zpNewHero__btn--ghost,.zp404__btn--ghost{background:rgba(255,255,255,.07)!important;color:#fff!important;border-color:rgba(255,255,255,.20)!important;}
    .zpNewHero__btn--ghost:hover,.zp404__btn--ghost:hover{background:var(--zpDSGrad)!important;color:#fff!important;border-color:var(--zpDSMid)!important;}
    <?php if (($d['no_pink_guard'] ?? '1') === '1'): ?>
    body [class^="zp"] *:where(a,area,button,input,select,textarea,summary,option,iframe,object,embed,audio,video,dialog,[tabindex],[contenteditable]):focus,body [class*=" zp"] *:where(a,area,button,input,select,textarea,summary,option,iframe,object,embed,audio,video,dialog,[tabindex],[contenteditable]):focus,body [class^="zp"] *:where(a,area,button,input,select,textarea,summary,option,iframe,object,embed,audio,video,dialog,[tabindex],[contenteditable]):focus-visible,body [class*=" zp"] *:where(a,area,button,input,select,textarea,summary,option,iframe,object,embed,audio,video,dialog,[tabindex],[contenteditable]):focus-visible,body *:not(a,area,button,input,select,textarea,summary,option,iframe,object,embed,audio,video,dialog,[tabindex],[contenteditable]):focus,body *:not(a,area,button,input,select,textarea,summary,option,iframe,object,embed,audio,video,dialog,[tabindex],[contenteditable]):focus-visible{outline-color:var(--zpDSMid)!important;box-shadow:0 0 0 3px rgba(16,42,79,.16)!important;border-color:rgba(16,42,79,.56)!important;}
    body [class^="zp"] .is-active,body [class*=" zp"] .is-active,body [class^="zp"] [aria-selected="true"],body [class*=" zp"] [aria-selected="true"]{--pink:var(--zpDSMid)!important;--accent:var(--zpDSMid)!important;}
    <?php endif; ?>
    <?php if (($d['animations'] ?? '1') !== '1' || ($d['safe_mode'] ?? '0') === '1'): ?>
    body [class^="zp"] *,body [class*=" zp"] *,body [class^="zp"] *:before,body [class*=" zp"] *:before,body [class^="zp"] *:after,body [class*=" zp"] *:after{animation:none!important;transition-duration:.001ms!important;scroll-behavior:auto!important;}
    <?php endif; ?>
  </style>
  <?php
}, 0);

add_action('wp_footer', function(){ if (is_admin()) return; ?><script id="zp-safe-loader-ready">(function(){function r(){document.documentElement.classList.remove('zp-loading');document.documentElement.classList.add('zp-ready')}if(document.readyState==='loading'){document.addEventListener('DOMContentLoaded',r,{once:true})}else{r()}window.addEventListener('load',r,{once:true})})();</script><?php }, 999);

/* ---------- Analytics helpers ---------- */
function zp_suite_170_weekday_pl($date){
  $map = ['Mon'=>'Poniedziałek','Tue'=>'Wtorek','Wed'=>'Środa','Thu'=>'Czwartek','Fri'=>'Piątek','Sat'=>'Sobota','Sun'=>'Niedziela'];
  $ts = strtotime($date); return $map[date('D',$ts)] ?? date('D',$ts);
}
function zp_suite_170_recent_days($days, $count = 7){
  $out=[];
  for($i=$count-1;$i>=0;$i--){
    $date = date_i18n('Y-m-d', strtotime('-'.$i.' days', current_time('timestamp')));
    $r = $days[$date] ?? [];
    $samples = max(1,(int)($r['samples'] ?? 0));
    $load = (int)round(($r['load_sum'] ?? 0)/$samples);
    $weight = (int)round(($r['weight_sum'] ?? 0)/$samples);
    $score = function_exists('zp_suite_cc_speed_score') ? zp_suite_cc_speed_score($load,$weight) : max(0, min(100, 100 - round($load/90) - round($weight/180)));
    $out[] = ['date'=>$date,'weekday'=>zp_suite_170_weekday_pl($date),'short'=>mb_substr(zp_suite_170_weekday_pl($date),0,3),'views'=>(int)($r['views']??0),'unique'=>count($r['visitors']??[]),'load'=>$load,'weight'=>$weight,'score'=>$score];
  }
  return $out;
}
function zp_suite_170_count_leads_by_day($leads){
  $out=[]; foreach($leads as $l){ $d=substr((string)($l['created_at']??''),0,10); if($d) $out[$d]=($out[$d]??0)+1; } return $out;
}
function zp_suite_170_count_events_by_day($events, $type){
  $out=[]; foreach($events as $e){ if(($e['type']??'')!==$type) continue; $d=substr((string)($e['at']??''),0,10); if($d) $out[$d]=($out[$d]??0)+1; } return $out;
}
function zp_suite_170_chart($rows, $key, $suffix=''){
  $max=1; foreach($rows as $r){ $max=max($max,(float)($r[$key]??0)); }
  echo '<div class="zp170Chart">';
  foreach($rows as $r){ $v=(float)($r[$key]??0); $w=max(2, round(($v/$max)*100)); echo '<div class="zp170Chart__row"><span>'.esc_html($r['short'] ?? $r['date']).'</span><div><i style="width:'.esc_attr($w).'%"></i></div><strong>'.esc_html($v.$suffix).'</strong></div>'; }
  echo '</div>';
}
function zp_suite_170_fmt_ms($ms){ return $ms ? round($ms/1000,2).' s' : '—'; }
function zp_suite_170_fmt_kb($kb){ return $kb > 1024 ? round($kb/1024,2).' MB' : ((int)$kb).' KB'; }

function zp_suite_170_stats_data(){
  $an = function_exists('zp_suite_analytics_get') ? zp_suite_analytics_get() : [];
  $cc = function_exists('zp_suite_cc_get') ? zp_suite_cc_get() : [];
  $leads = function_exists('zp_suite_leads_all') ? zp_suite_leads_all() : [];
  $week = zp_suite_170_recent_days($an['days'] ?? [], 7);
  $month = zp_suite_170_recent_days($an['days'] ?? [], 30);
  $lead_days = zp_suite_170_count_leads_by_day($leads);
  $cta_days = zp_suite_170_count_events_by_day($cc['events'] ?? [], 'cta');
  foreach([$week, $month] as &$set){ foreach($set as &$r){ $r['leads']=(int)($lead_days[$r['date']] ?? 0); $r['cta']=(int)($cta_days[$r['date']] ?? 0); } }
  unset($set,$r);
  return [$an,$cc,$leads,$week,$month];
}

/* ---------- Admin UI ---------- */
add_action('admin_head', function(){
  $screen = function_exists('get_current_screen') ? get_current_screen() : null;
  $id = $screen ? $screen->id : '';
  if (strpos($id, 'zp-suite') === false) return;
  echo '<style id="zp-suite-admin-v170-css">'.zp_suite_170_admin_css().'</style>';
}, 120);

function zp_suite_170_admin_css(){ return <<<'CSS'
.zpSuiteAdmin,.zp170Admin{--bg:#05070b;--panel:#071426;--panel2:#0b1830;--blue:#1c477a;--line:#dfe6ef;--soft:#f6f8fb;--text:#071426;--muted:#657084;font-family:Inter,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;color:var(--text);margin:20px 20px 0 0;max-width:1560px}.zpSuiteHero,.zp170Hero{position:relative;overflow:hidden;border-radius:30px;background:radial-gradient(circle at 10% 0%,rgba(28,71,122,.44),transparent 34%),linear-gradient(135deg,#05070b,#071426 58%,#102a4f);color:#fff;padding:34px 38px;border:1px solid rgba(255,255,255,.12);box-shadow:0 26px 70px rgba(7,20,38,.16)}.zpSuiteHero h1,.zp170Hero h1{color:#fff!important;font-size:38px!important;line-height:1!important;letter-spacing:-.055em!important;margin:8px 0 10px!important}.zpSuiteHero p,.zp170Hero p{color:rgba(255,255,255,.72)!important;max-width:920px}.zp170Badge,.zpSuiteBadge{display:inline-flex;padding:8px 12px;border:1px solid rgba(255,255,255,.15);border-radius:999px;text-transform:uppercase;letter-spacing:.14em;font-size:10px;font-weight:850;color:rgba(255,255,255,.78)}.zp170Grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px}.zp170Grid3{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px}.zp170Card,.zpCard{background:#fff;border:1px solid var(--line);border-radius:24px;padding:24px;margin:18px 0;box-shadow:0 18px 58px rgba(7,20,38,.055)}.zp170Card h2,.zpCard h2{color:#071426!important;margin:0 0 14px!important;font-size:22px!important;line-height:1.05!important;letter-spacing:-.035em!important}.zp170Card p{color:#657084;line-height:1.55}.zp170Kpi{border:1px solid #e1e8f0;background:#f8fafc;border-radius:20px;padding:16px}.zp170Kpi strong{display:block;color:#071426;font-size:26px;letter-spacing:-.045em}.zp170Kpi span{display:block;color:#667085;text-transform:uppercase;letter-spacing:.12em;font-size:10px;font-weight:850;margin-top:5px}.zp170Chart{display:grid;gap:9px}.zp170Chart__row{display:grid;grid-template-columns:96px 1fr 80px;gap:12px;align-items:center}.zp170Chart__row span{font-size:12px;color:#667085;font-weight:800}.zp170Chart__row div{height:14px;background:#e8eef6;border-radius:999px;overflow:hidden}.zp170Chart__row i{display:block;height:100%;background:linear-gradient(90deg,#05070b,#102a4f,#1c477a);border-radius:999px}.zp170Chart__row strong{text-align:right;color:#071426}.zp170Table{width:100%;border-collapse:separate;border-spacing:0 8px}.zp170Table th{text-align:left;font-size:10px;text-transform:uppercase;letter-spacing:.12em;color:#667085}.zp170Table td{background:#f8fafc;border-top:1px solid #e1e8f0;border-bottom:1px solid #e1e8f0;padding:12px}.zp170Table td:first-child{border-left:1px solid #e1e8f0;border-radius:14px 0 0 14px}.zp170Table td:last-child{border-right:1px solid #e1e8f0;border-radius:0 14px 14px 0}.zp170Field{display:block;margin:0 0 14px}.zp170Field b{display:block;margin:0 0 7px;font-size:11px;text-transform:uppercase;letter-spacing:.1em;color:#455165}.zp170Field input,.zp170Field select,.zp170Field textarea{width:100%;border-radius:14px;border:1px solid #d9e0eb;background:#f8fafc;padding:10px 12px}.zp170Field input:focus,.zp170Field select:focus,.zp170Field textarea:focus{border-color:#102a4f!important;box-shadow:0 0 0 3px rgba(16,42,79,.12)!important;outline:0!important}.zp170Actions{display:flex;gap:8px;flex-wrap:wrap}.zp170Btn,.zp170Admin .button-primary,.zpSuiteAdmin .button-primary{border-radius:999px!important;background:#071426!important;border-color:#071426!important;color:#fff!important;font-weight:850!important;box-shadow:none!important;transform:none!important}.zp170Admin .button,.zpSuiteAdmin .button{border-radius:999px!important;box-shadow:none!important;transform:none!important}.zp170Btn:hover,.zp170Admin .button-primary:hover,.zpSuiteAdmin .button-primary:hover{background:#102a4f!important;border-color:#102a4f!important;transform:none!important}.zp170Pill{display:inline-flex;align-items:center;gap:6px;padding:7px 10px;border-radius:999px;background:#eef4fb;color:#102a4f;font-size:11px;font-weight:850}.zp170Pill.red{background:#fef2f2;color:#991b1b}.zp170Pill.green{background:#ecfdf5;color:#166534}.zp170Kanban{display:grid;grid-template-columns:repeat(4,minmax(240px,1fr));gap:14px;overflow:auto;padding-bottom:10px}.zp170KanbanCol{background:#f4f7fb;border:1px solid #e1e8f0;border-radius:22px;padding:12px;min-height:260px}.zp170KanbanCol h3{margin:4px 4px 12px;color:#071426}.zp170LeadCard{background:#fff;border:1px solid #e1e8f0;border-radius:18px;padding:14px;margin-bottom:10px}.zp170LeadCard.is-new{border-color:#ef4444;background:#fffafa}.zp170LeadCard strong{display:block;color:#071426}.zp170LeadCard small{display:block;color:#667085;margin-top:4px}.zp170Score{display:inline-flex;border-radius:999px;background:#071426;color:#fff;font-size:11px;font-weight:900;padding:5px 8px;margin-top:8px}.zp170Notice{padding:13px 15px;border-radius:16px;background:#f8fafc;border:1px solid #e1e8f0;color:#475569}.zp170Notice.ok{background:#ecfdf5;border-color:#bbf7d0;color:#166534}.zp170Notice.warn{background:#fff7ed;border-color:#fed7aa;color:#9a3412}.zp170MediaGrid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px}.zp170MediaItem{background:#fff;border:1px solid #e1e8f0;border-radius:18px;padding:12px}.zp170MediaItem img{width:100%;height:92px;object-fit:contain;background:#f8fafc;border-radius:12px}.zp170MediaItem b{display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-top:8px}.zp170MediaItem small{color:#667085}.zp170StatusGrid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px}.zp170Status{border:1px solid #e1e8f0;border-radius:18px;padding:16px;background:#f8fafc}.zp170Status b{display:block;color:#071426}.zp170Status span{display:block;color:#667085;margin-top:5px}.zp170Status.ok{background:#ecfdf5;border-color:#bbf7d0}.zp170Status.warn{background:#fff7ed;border-color:#fed7aa}.zp170Status.bad{background:#fef2f2;border-color:#fecaca}@media(max-width:1200px){.zp170Grid,.zp170Grid3,.zp170StatusGrid,.zp170MediaGrid{grid-template-columns:1fr 1fr}.zp170Kanban{grid-template-columns:repeat(2,minmax(240px,1fr))}}@media(max-width:760px){.zp170Grid,.zp170Grid3,.zp170StatusGrid,.zp170MediaGrid,.zp170Kanban{grid-template-columns:1fr}.zp170Chart__row{grid-template-columns:64px 1fr 54px}}
CSS; }

function zp_suite_170_page_open($title, $lead){
  echo '<div class="zp170Admin"><div class="zp170Hero"><span class="zp170Badge">Zaprojektowani Suite 1.7.0</span><h1>'.esc_html($title).'</h1><p>'.esc_html($lead).'</p></div>';
}
function zp_suite_170_page_close(){ echo '</div>'; }

function zp_suite_170_render_stats_page(){
  if (!current_user_can('manage_options')) return;
  [$an,$cc,$leads,$week,$month] = zp_suite_170_stats_data();
  $views=0; $vis=[]; foreach(($an['days']??[]) as $r){$views+=(int)($r['views']??0); foreach(($r['visitors']??[]) as $k=>$v){$vis[$k]=1;}}
  $perf=$an['perf']??[]; $samples=max(1,(int)($perf['samples']??0)); $load=(int)round(($perf['load_sum']??0)/$samples); $weight=(int)round(($perf['weight_sum']??0)/$samples); $ttfb=(int)round(($perf['ttfb_sum']??0)/$samples); $score=function_exists('zp_suite_cc_speed_score')?zp_suite_cc_speed_score($load,$weight,$ttfb):0;
  zp_suite_170_page_open('Statystyki ruchu, leadów i szybkości', 'Pełny widok tygodnia i miesiąca: odsłony, unikalni użytkownicy, leady, CTA, speed score, waga strony i najczęściej odwiedzane URL-e.');
  echo '<div class="zp170Grid3"><div class="zp170Kpi"><strong>'.esc_html($views).'</strong><span>Odsłony łącznie</span></div><div class="zp170Kpi"><strong>'.esc_html(count($vis)).'</strong><span>Unikalni użytkownicy</span></div><div class="zp170Kpi"><strong>'.esc_html(count($leads)).'</strong><span>Leady łącznie</span></div><div class="zp170Kpi"><strong>'.esc_html($score).'/100</strong><span>Speed score</span></div><div class="zp170Kpi"><strong>'.esc_html(zp_suite_170_fmt_ms($load)).'</strong><span>Średni load</span></div><div class="zp170Kpi"><strong>'.esc_html(zp_suite_170_fmt_kb($weight)).'</strong><span>Średnia waga</span></div></div>';
  echo '<div class="zp170Card"><h2>Ostatnie 7 dni — dzień po dniu</h2><table class="zp170Table"><thead><tr><th>Dzień</th><th>Data</th><th>Odsłony</th><th>Unikalni</th><th>Leady</th><th>CTA</th><th>Speed</th></tr></thead><tbody>';
  foreach($week as $r){echo '<tr><td><b>'.esc_html($r['weekday']).'</b></td><td>'.esc_html($r['date']).'</td><td>'.esc_html($r['views']).'</td><td>'.esc_html($r['unique']).'</td><td>'.esc_html($r['leads']).'</td><td>'.esc_html($r['cta']).'</td><td>'.esc_html($r['score']).'/100</td></tr>';}
  echo '</tbody></table></div>';
  echo '<div class="zp170Grid"><div class="zp170Card"><h2>Odsłony — 7 dni</h2>'; zp_suite_170_chart($week,'views'); echo '</div><div class="zp170Card"><h2>Unikalni — 7 dni</h2>'; zp_suite_170_chart($week,'unique'); echo '</div><div class="zp170Card"><h2>Leady — 7 dni</h2>'; zp_suite_170_chart($week,'leads'); echo '</div><div class="zp170Card"><h2>CTA — 7 dni</h2>'; zp_suite_170_chart($week,'cta'); echo '</div><div class="zp170Card"><h2>Speed score — 7 dni</h2>'; zp_suite_170_chart($week,'score','/100'); echo '</div><div class="zp170Card"><h2>Waga strony — 7 dni</h2>'; zp_suite_170_chart($week,'weight',' KB'); echo '</div></div>';
  echo '<div class="zp170Grid"><div class="zp170Card"><h2>Odsłony — ostatnie 30 dni</h2>'; zp_suite_170_chart($month,'views'); echo '</div><div class="zp170Card"><h2>Speed score — ostatnie 30 dni</h2>'; zp_suite_170_chart($month,'score','/100'); echo '</div></div>';
  $pages=$an['pages']??[]; uasort($pages, function($a,$b){return ($b['views']??0)<=>($a['views']??0);}); $pages=array_slice($pages,0,12,true);
  echo '<div class="zp170Card"><h2>Najczęściej odwiedzane podstrony</h2><table class="zp170Table"><thead><tr><th>URL</th><th>Odsłony</th><th>Unikalni</th><th>Load</th><th>Waga</th><th>Ostatnio</th></tr></thead><tbody>';
  foreach($pages as $path=>$r){$s=max(1,(int)($r['samples']??1)); $l=(int)round(($r['load_sum']??0)/$s); $w=(int)round(($r['weight_sum']??0)/$s); echo '<tr><td><code>'.esc_html($path).'</code></td><td>'.esc_html($r['views']??0).'</td><td>'.esc_html(count($r['visitors']??[])).'</td><td>'.esc_html(zp_suite_170_fmt_ms($l)).'</td><td>'.esc_html(zp_suite_170_fmt_kb($w)).'</td><td>'.esc_html($r['last_at']??'').'</td></tr>';}
  if(!$pages) echo '<tr><td colspan="6">Brak danych — odwiedź stronę kilka razy w incognito.</td></tr>'; echo '</tbody></table></div>';
  $sections=$cc['sections']??[]; uasort($sections, function($a,$b){return ($b['views']??0)<=>($a['views']??0);});
  echo '<div class="zp170Card"><h2>Ranking sekcji strony głównej</h2><table class="zp170Table"><thead><tr><th>Sekcja</th><th>Wyświetlenia</th><th>Ostatnio</th></tr></thead><tbody>';
  foreach(array_slice($sections,0,12,true) as $key=>$r){echo '<tr><td>'.esc_html($r['label']??$key).'</td><td>'.esc_html($r['views']??0).'</td><td>'.esc_html($r['last_at']??'').'</td></tr>';}
  if(!$sections) echo '<tr><td colspan="3">Dane pojawią się po wejściach na stronę.</td></tr>'; echo '</tbody></table></div>';
  zp_suite_170_page_close();
}

function zp_suite_170_render_design_page(){
  if (!current_user_can('manage_options')) return; $d=zp_suite_170_design();
  zp_suite_170_page_open('Design System i globalny styl buttonów', 'Jedno miejsce dla kolorów, focusów, radiusów, animacji i trybu awaryjnego. Globalny styl buttonów: biały button z ciemnym tekstem, na hover navy gradient bez podnoszenia.');
  if (!empty($_GET['saved'])) echo '<div class="zp170Notice ok">Zapisano Design System.</div>';
  echo '<form method="post">'; wp_nonce_field('zp_suite_design_save'); echo '<input type="hidden" name="zp_suite_design_save" value="1"><div class="zp170Grid"><div class="zp170Card"><h2>Kolory globalne</h2>';
  foreach(['navy'=>'Navy główny','navy_dark'=>'Navy ciemny','navy_mid'=>'Navy hover','blue'=>'Blue akcent','light_bg'=>'Tło jasnych sekcji','text'=>'Tekst'] as $k=>$label){echo '<label class="zp170Field"><b>'.esc_html($label).'</b><input type="text" name="zp_design['.esc_attr($k).']" value="'.esc_attr($d[$k]).'"></label>';}
  echo '</div><div class="zp170Card"><h2>Buttony, animacje, tryb pracy</h2><label class="zp170Field"><b>Radius buttonów</b><input type="text" name="zp_design[radius_button]" value="'.esc_attr($d['radius_button']).'"></label><label class="zp170Field"><b>Radius kart</b><input type="text" name="zp_design[radius_card]" value="'.esc_attr($d['radius_card']).'"></label><label class="zp170Field"><b>Tryb pracy</b><select name="zp_design[editor_mode]"><option value="production" '.selected($d['editor_mode'],'production',false).'>Produkcja</option><option value="editing" '.selected($d['editor_mode'],'editing',false).'>Edycja / diagnostyka</option></select></label><label><input type="checkbox" name="zp_design[animations]" value="1" '.checked($d['animations'],'1',false).'> Animacje aktywne</label><br><label><input type="checkbox" name="zp_design[safe_mode]" value="1" '.checked($d['safe_mode'],'1',false).'> Safe Mode — wyłącz ciężkie animacje</label><br><label><input type="checkbox" name="zp_design[no_pink_guard]" value="1" '.checked($d['no_pink_guard'],'1',false).'> Globalny no-pink guard</label><p class="zp170Notice">Styl hover bez podnoszenia: button nie przesuwa się do góry, tylko odwraca kolor na navy gradient.</p></div></div><p><button class="button button-primary button-large">Zapisz Design System</button></p></form>';
  zp_suite_170_page_close();
}

function zp_suite_170_render_front_check_page(){
  if (!current_user_can('manage_options')) return;
  $cms=function_exists('zp_suite_cms')?zp_suite_cms():[]; $opts=function_exists('zp_suite_options')?zp_suite_options():[]; $order=function_exists('zp_suite_home_order')?zp_suite_home_order():[];
  zp_suite_170_page_open('Kontrola frontu i jakości home', 'Szybka diagnostyka: shortcode’y, widoczność sekcji, SEO home, liczby, FAQ, portfolio, opinie, kontakt, obrazki i spójność systemu.');
  $checks=[];
  $checks[]=['Header osobno', shortcode_exists('zp_header'), 'Shortcode [zp_header] zarejestrowany'];
  $checks[]=['Home full', shortcode_exists('zp_home_full'), 'Shortcode [zp_home_full] zarejestrowany'];
  $checks[]=['Footer osobno', shortcode_exists('zp_footer'), 'Shortcode [zp_footer] zarejestrowany'];
  $checks[]=['H1 hero', !empty($opts['hero']['title']), 'Hero ma tytuł'];
  $checks[]=['Lead hero', mb_strlen(strip_tags($opts['hero']['lead']??'')) > 40, 'Lead ma sensowną długość'];
  $checks[]=['FAQ', !empty($cms['faq']), 'FAQ istnieje w CMS'];
  $checks[]=['Portfolio', !empty($cms['portfolio']['web']) || !empty($cms['portfolio']['logo']), 'Portfolio ma projekty'];
  $checks[]=['Opinie', !empty($cms['reviews']), 'Opinie są w CMS'];
  $checks[]=['Kontakt', shortcode_exists('zp_contact_system'), 'Formularz kontaktowy zarejestrowany'];
  $checks[]=['Kolejność sekcji', count($order) >= 6, 'Home ma zapisaną kolejność sekcji'];
  echo '<div class="zp170StatusGrid">';
  foreach($checks as $c){ echo '<div class="zp170Status '.($c[1]?'ok':'bad').'"><b>'.esc_html($c[0]).'</b><span>'.esc_html($c[2]).' — '.($c[1]?'OK':'Do poprawy').'</span></div>'; }
  echo '</div>';
  echo '<div class="zp170Card"><h2>Rekomendowany układ strony głównej</h2><pre>[zp_header]\n[zp_home_full]\n[zp_footer]</pre><p>Header i footer zostają osobno. [zp_home_full] renderuje tylko środek strony według kolejności z CMS.</p></div>';
  zp_suite_170_page_close();
}

function zp_suite_170_lead_score($lead){
  $s=0; if(!empty($lead['phone']))$s+=20; if(!empty($lead['email']))$s+=12; $msg=$lead['message']??''; if(mb_strlen($msg)>120)$s+=18; if(mb_strlen($msg)>260)$s+=10; $services=$lead['services']??''; if(stripos($services,'sklep')!==false||stripos($services,'strona')!==false)$s+=20; if(substr_count($services, ',')>=2)$s+=10; if(($lead['source']??'')==='Exit popup')$s+=5; return min(100,$s);
}
function zp_suite_170_render_crm_page(){
  if (!current_user_can('manage_options')) return; $leads=function_exists('zp_suite_leads_all')?zp_suite_leads_all():[];
  $cols=['new'=>'Nowe','progress'=>'W trakcie','called'=>'Oddzwonione','offer'=>'Oferta wysłana','closed'=>'Zamknięte','spam'=>'Spam'];
  zp_suite_170_page_open('CRM Pro — leady, statusy i priorytety', 'Kanban leadów z punktacją, notatkami, tagami, priorytetem i przypomnieniami. To lekki CRM dla zapytań z formularzy, popupów i mini chatu.');
  if(!empty($_GET['saved'])) echo '<div class="zp170Notice ok">Zapisano zmiany w CRM.</div>';
  echo '<form method="post">'; wp_nonce_field('zp_suite_crm_save'); echo '<input type="hidden" name="zp_suite_crm_save" value="1"><div class="zp170Kanban">';
  foreach($cols as $status=>$label){ echo '<div class="zp170KanbanCol"><h3>'.esc_html($label).'</h3>'; $found=0; foreach($leads as $lead){ $st=$lead['status']??'new'; if($status==='progress' && $st==='read') $st='progress'; if($st!==$status) continue; $found++; $id=$lead['id']??''; $score=zp_suite_170_lead_score($lead); echo '<div class="zp170LeadCard '.(($lead['status']??'')==='new'?'is-new':'').'"><strong>'.esc_html($lead['name']??'Bez nazwy').'</strong><small>'.esc_html($lead['created_at']??'').' • '.esc_html($lead['source']??'').'</small><span class="zp170Score">'.esc_html($score).'/100</span><label class="zp170Field"><b>Status</b><select name="lead['.esc_attr($id).'][status]">'; foreach($cols as $k=>$v){echo '<option value="'.esc_attr($k).'" '.selected($st,$k,false).'>'.esc_html($v).'</option>'; } echo '</select></label><label class="zp170Field"><b>Priorytet</b><select name="lead['.esc_attr($id).'][priority]"><option value="low" '.selected(($lead['priority']??''),'low',false).'>Niski</option><option value="medium" '.selected(($lead['priority']??'medium'),'medium',false).'>Średni</option><option value="high" '.selected(($lead['priority']??''),'high',false).'>Wysoki</option></select></label><label class="zp170Field"><b>Tagi</b><input name="lead['.esc_attr($id).'][tags]" value="'.esc_attr($lead['tags']??'').'" placeholder="WWW, sklep, logo"></label><label class="zp170Field"><b>Przypomnienie</b><input name="lead['.esc_attr($id).'][reminder]" value="'.esc_attr($lead['reminder']??'').'" placeholder="np. oddzwonić jutro"></label><label class="zp170Field"><b>Notatka</b><textarea name="lead['.esc_attr($id).'][note]">'.esc_textarea($lead['note']??'').'</textarea></label><p><a class="button" href="mailto:'.esc_attr($lead['email']??'').'">E-mail</a> <a class="button" href="tel:'.esc_attr($lead['phone']??'').'">Telefon</a></p></div>'; } if(!$found) echo '<p class="zp170Notice">Brak leadów.</p>'; echo '</div>'; }
  echo '</div><p><button class="button button-primary button-large">Zapisz CRM</button></p></form>';
  zp_suite_170_page_close();
}

function zp_suite_170_render_media_usage_page(){
  if (!current_user_can('manage_options')) return; $cms=function_exists('zp_suite_cms')?zp_suite_cms():[]; $opts=function_exists('zp_suite_options')?zp_suite_options():[]; $urls=[];
  $walk=function($v) use (&$walk,&$urls){ if(is_array($v)){foreach($v as $vv)$walk($vv);} elseif(is_string($v) && preg_match('~https?://[^\s\"\']+\.(webp|png|jpe?g|svg|gif)~i',$v)) $urls[]=$v; };
  $walk($cms); $walk($opts); $urls=array_values(array_unique($urls));
  zp_suite_170_page_open('Media użyte na stronie', 'Lista obrazów wykorzystywanych przez Zaprojektowani Suite: podgląd, ALT, waga, rekomendacje i ryzyko dla PageSpeed.');
  echo '<div class="zp170MediaGrid">';
  foreach(array_slice($urls,0,80) as $url){ $name=basename(parse_url($url,PHP_URL_PATH)); $att=attachment_url_to_postid($url); $alt=$att?get_post_meta($att,'_wp_attachment_image_alt',true):''; $meta=$att?wp_get_attachment_metadata($att):[]; $w=$meta['width']??''; $h=$meta['height']??''; echo '<div class="zp170MediaItem"><img src="'.esc_url($url).'" alt=""><b title="'.esc_attr($name).'">'.esc_html($name).'</b><small>'.($w&&$h?esc_html($w.'×'.$h):'zewnętrzny URL').'</small><br>'.($alt?'<span class="zp170Pill green">ALT OK</span>':'<span class="zp170Pill red">Brak ALT / zewnętrzny</span>').'</div>'; }
  if(!$urls) echo '<p>Nie znaleziono obrazów w CMS.</p>'; echo '</div>'; zp_suite_170_page_close();
}

function zp_suite_170_render_history_page(){
  if (!current_user_can('manage_options')) return; $log=get_option('zp_suite_activity_log',[]); $snaps=get_option('zp_suite_snapshots',[]);
  zp_suite_170_page_open('Historia zmian i snapshoty', 'Każdy zapis CMS tworzy kopię bezpieczeństwa. Możesz sprawdzić aktywność i w razie potrzeby przywrócić wcześniejszy stan.');
  if(!empty($_GET['restored'])) echo '<div class="zp170Notice ok">Przywrócono snapshot.</div>';
  echo '<div class="zp170Grid"><div class="zp170Card"><h2>Activity log</h2><table class="zp170Table"><thead><tr><th>Data</th><th>Zdarzenie</th><th>Kontekst</th><th>Użytkownik</th></tr></thead><tbody>'; foreach(array_slice(is_array($log)?$log:[],0,40) as $r){echo '<tr><td>'.esc_html($r['at']??'').'</td><td>'.esc_html($r['title']??'').'</td><td>'.esc_html($r['context']??'').'</td><td>'.esc_html($r['user']??'').'</td></tr>';} if(!$log) echo '<tr><td colspan="4">Brak wpisów.</td></tr>'; echo '</tbody></table></div><div class="zp170Card"><h2>Snapshoty CMS</h2><table class="zp170Table"><thead><tr><th>Data</th><th>Powód</th><th>Akcja</th></tr></thead><tbody>'; foreach(array_slice(is_array($snaps)?$snaps:[],0,20) as $i=>$s){echo '<tr><td>'.esc_html($s['at']??'').'</td><td>'.esc_html($s['reason']??'').'</td><td><form method="post" onsubmit="return confirm(\'Przywrócić ten snapshot?\')">'; wp_nonce_field('zp_suite_restore_snapshot'); echo '<input type="hidden" name="zp_suite_restore_snapshot" value="1"><input type="hidden" name="snapshot_index" value="'.esc_attr($i).'"><button class="button">Przywróć</button></form></td></tr>';} if(!$snaps) echo '<tr><td colspan="3">Brak snapshotów.</td></tr>'; echo '</tbody></table></div></div>'; zp_suite_170_page_close();
}
