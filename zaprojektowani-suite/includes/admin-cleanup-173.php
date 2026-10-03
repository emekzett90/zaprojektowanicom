<?php
if (!defined('ABSPATH')) { exit; }

/* =========================================================
 * ZAPROJEKTOWANI SUITE v1.7.3
 * Admin Cleanup + Analytics Final
 * - jedno menu Statystyki
 * - jeden finalny ekran statystyk z wykresami 7/30 dni
 * - Command Center pozostaje skrótem, bez duplikowania pełnych statystyk
 * ========================================================= */

function zp_suite_173_weekday_pl($date){
  $n = (int) date_i18n('N', strtotime($date));
  $map = [1=>'Pon',2=>'Wt',3=>'Śr',4=>'Czw',5=>'Pt',6=>'Sob',7=>'Nd'];
  return $map[$n] ?? '';
}

function zp_suite_173_date_label($date){
  return zp_suite_173_weekday_pl($date).' '.date_i18n('d.m', strtotime($date));
}

function zp_suite_173_days_range($count){
  $out = [];
  for($i=$count-1; $i>=0; $i--){
    $out[] = date_i18n('Y-m-d', strtotime('-'.$i.' days'));
  }
  return $out;
}

function zp_suite_173_leads_by_day(){
  $leads = function_exists('zp_suite_leads_all') ? zp_suite_leads_all() : get_option('zp_suite_leads', []);
  if (!is_array($leads)) $leads = [];
  $out = [];
  foreach($leads as $lead){
    $at = $lead['created_at'] ?? ($lead['at'] ?? ($lead['date'] ?? ''));
    if (!$at) continue;
    $day = date_i18n('Y-m-d', strtotime($at));
    $out[$day] = ($out[$day] ?? 0) + 1;
  }
  return $out;
}

function zp_suite_173_events_by_day($type){
  $cc = function_exists('zp_suite_cc_get') ? zp_suite_cc_get() : get_option('zp_suite_command_center', []);
  $events = $cc['events'] ?? [];
  $out = [];
  if (!is_array($events)) return $out;
  foreach($events as $e){
    if (($e['type'] ?? '') !== $type) continue;
    $at = $e['at'] ?? '';
    if (!$at) continue;
    $day = date_i18n('Y-m-d', strtotime($at));
    $out[$day] = ($out[$day] ?? 0) + 1;
  }
  return $out;
}

function zp_suite_173_speed_score($load_ms, $weight_kb){
  $load = (int)$load_ms;
  $weight = (int)$weight_kb;
  $score = 100;
  if ($load > 1200) $score -= min(42, (int)ceil(($load-1200)/95));
  if ($weight > 1600) $score -= min(34, (int)ceil(($weight-1600)/170));
  return max(0, min(100, $score));
}

function zp_suite_173_svg_line_chart($points, $color = '#1c477a', $height = 150){
  $values = array_values(array_map('intval', $points));
  $max = max(1, max($values ?: [1]));
  $count = max(1, count($values));
  $w = 720; $h = (int)$height; $pad = 18;
  $coords = [];
  foreach($values as $i=>$v){
    $x = $count <= 1 ? $pad : $pad + (($w - $pad*2) * ($i / ($count-1)));
    $y = $h - $pad - (($h - $pad*2) * ($v / $max));
    $coords[] = round($x,2).','.round($y,2);
  }
  $poly = esc_attr(implode(' ', $coords));
  $fill = esc_attr($color);
  ob_start();
  ?>
  <svg class="zp173Chart" viewBox="0 0 <?php echo esc_attr($w); ?> <?php echo esc_attr($h); ?>" role="img" aria-label="Wykres liniowy">
    <defs><linearGradient id="zp173Fill<?php echo esc_attr(abs(crc32($poly))); ?>" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="<?php echo $fill; ?>" stop-opacity=".24"/><stop offset="1" stop-color="<?php echo $fill; ?>" stop-opacity="0"/></linearGradient></defs>
    <path d="M <?php echo $poly; ?>" fill="none" stroke="<?php echo $fill; ?>" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
    <polyline points="<?php echo $poly; ?> <?php echo esc_attr(($w-$pad).','.($h-$pad).' '.$pad.','.($h-$pad)); ?>" fill="url(#zp173Fill<?php echo esc_attr(abs(crc32($poly))); ?>)" stroke="none"/>
    <?php foreach($coords as $c): ?><circle cx="<?php echo esc_attr(explode(',',$c)[0]); ?>" cy="<?php echo esc_attr(explode(',',$c)[1]); ?>" r="4" fill="#fff" stroke="<?php echo $fill; ?>" stroke-width="3"/><?php endforeach; ?>
  </svg>
  <?php
  return ob_get_clean();
}

function zp_suite_173_bars($labels, $values, $unit = ''){
  $max = max(1, max(array_map('intval', $values ?: [1])));
  ob_start();
  ?>
  <div class="zp173Bars">
    <?php foreach($labels as $i=>$label): $v=(int)($values[$i]??0); $h=max(4, round(($v/$max)*120)); ?>
      <div class="zp173Bar" title="<?php echo esc_attr($label.' — '.$v.$unit); ?>"><i style="height:<?php echo esc_attr($h); ?>px"></i><strong><?php echo esc_html($v); ?></strong><span><?php echo esc_html($label); ?></span></div>
    <?php endforeach; ?>
  </div>
  <?php
  return ob_get_clean();
}

function zp_suite_173_admin_css(){
  ?>
  <style id="zp-suite-173-admin-css">.zpSuiteAdmin{--zpN:#071426;--zpD:#05070b;--zpM:#102a4f;--zpB:#1c477a;--zpLine:#dfe5ee;--zpSoft:#f6f8fb;--zpText:#071426;max-width:1480px;margin:20px 22px 40px 0;font-family:"Inter","Plus Jakarta Sans",system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;color:var(--zpText)}.zpSuiteHero,.zp173Hero{border-radius:30px;background:radial-gradient(circle at 92% 0%,rgba(28,71,122,.42),transparent 34%),linear-gradient(135deg,#05070b,#071426 48%,#102a4f);color:#fff;padding:30px;box-shadow:0 26px 80px rgba(7,20,38,.14);position:relative;overflow:hidden}.zpSuiteHero h1,.zp173Hero h1{color:#fff!important;margin:8px 0 10px;font-size:clamp(30px,3vw,54px);line-height:.98;letter-spacing:-.055em;font-weight:650;max-width:950px}.zpSuiteHero p,.zp173Hero p{color:rgba(255,255,255,.76);font-size:14px;line-height:1.6;max-width:860px;margin:0}.zpSuiteBadge,.zp173Badge{display:inline-flex;align-items:center;gap:10px;text-transform:uppercase;letter-spacing:.13em;font-size:10px;font-weight:850;color:rgba(255,255,255,.76)}.zp173Kpis{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px;margin-top:18px}.zp173Kpi{background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.12);border-radius:20px;padding:16px}.zp173Kpi strong{display:block;color:#fff;font-size:28px;letter-spacing:-.04em;line-height:1}.zp173Kpi span{display:block;margin-top:8px;color:rgba(255,255,255,.62);font-size:10px;text-transform:uppercase;letter-spacing:.12em;font-weight:800}.zp173Grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px;margin-top:18px}.zp173Grid3{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:18px;margin-top:18px}.zp173Card{background:#fff;border:1px solid var(--zpLine);border-radius:26px;padding:22px;box-shadow:0 16px 48px rgba(7,20,38,.055)}.zp173Card h2{font-size:22px;line-height:1.08;letter-spacing:-.04em;margin:0 0 16px;color:#071426}.zp173Card p{color:#657084}.zp173Card table{border-radius:18px;overflow:hidden;border:1px solid #e4e9f1}.zp173Card code{background:#f4f7fb;border-radius:8px;padding:3px 6px;color:#071426}.zp173Chart{width:100%;height:auto;display:block;background:linear-gradient(180deg,#f8fafc,#fff);border:1px solid #e2e8f0;border-radius:20px;padding:8px}.zp173Bars{height:190px;display:flex;align-items:flex-end;gap:10px;border:1px solid #e2e8f0;background:linear-gradient(180deg,#f8fafc,#fff);border-radius:20px;padding:18px;overflow:auto}.zp173Bar{min-width:54px;text-align:center;display:flex;flex-direction:column;align-items:center;justify-content:flex-end;gap:6px}.zp173Bar i{display:block;width:28px;border-radius:999px 999px 5px 5px;background:linear-gradient(180deg,#1c477a,#071426);box-shadow:0 10px 24px rgba(16,42,79,.16)}.zp173Bar strong{font-size:13px;color:#071426}.zp173Bar span{font-size:10px;color:#697386;white-space:nowrap}.zp173Speed{display:grid;place-items:center;min-height:214px}.zp173Ring{--p:80;width:152px;height:152px;border-radius:50%;display:grid;place-items:center;background:conic-gradient(#16a34a calc(var(--p)*1%),#e7edf5 0);position:relative}.zp173Ring:before{content:"";position:absolute;inset:12px;border-radius:inherit;background:#fff}.zp173Ring strong{position:relative;z-index:2;font-size:42px;letter-spacing:-.06em;color:#071426}.zp173Ring span{position:absolute;z-index:2;margin-top:72px;font-size:10px;color:#64748b;text-transform:uppercase;letter-spacing:.11em;font-weight:800}.zp173Notice{display:flex;gap:12px;align-items:flex-start;border:1px solid #dbe5f2;background:#f8fbff;border-radius:18px;padding:14px;color:#475569}.zp173Notice b{color:#071426}.zp173Pill{display:inline-flex;align-items:center;border-radius:999px;background:#eef4fb;color:#102a4f;padding:5px 9px;font-size:11px;font-weight:800;letter-spacing:.04em;text-transform:uppercase}.zp173Funnel{display:grid;gap:10px}.zp173FunnelRow{display:grid;grid-template-columns:160px minmax(0,1fr) 70px;gap:12px;align-items:center}.zp173FunnelRow span{font-weight:800;color:#071426}.zp173FunnelTrack{height:14px;border-radius:999px;background:#e9eef5;overflow:hidden}.zp173FunnelTrack i{display:block;height:100%;border-radius:inherit;background:linear-gradient(90deg,#071426,#1c477a)}.zp173Actions{display:flex;gap:10px;flex-wrap:wrap;margin-top:16px}.zp173Btn,.zpSuiteAdmin .button.zp173Btn{border-radius:999px!important;border:1px solid rgba(7,20,38,.16)!important;background:#fff!important;color:#071426!important;font-weight:800;padding:9px 15px!important;line-height:1!important;box-shadow:none!important;transform:none!important;text-decoration:none!important}.zp173Btn:hover{background:linear-gradient(90deg,#05070b,#102a4f,#1c477a)!important;color:#fff!important;border-color:#102a4f!important}@media(max-width:1100px){.zp173Kpis,.zp173Grid3{grid-template-columns:repeat(2,minmax(0,1fr))}.zp173Grid{grid-template-columns:1fr}}@media(max-width:720px){.zp173Kpis,.zp173Grid3{grid-template-columns:1fr}.zpSuiteAdmin{margin-right:12px}.zpSuiteHero,.zp173Hero{padding:22px}}</style>
  <?php
}
add_action('admin_head', 'zp_suite_173_admin_css', 99);

add_action('admin_menu', function(){
  global $submenu;
  if (isset($submenu['zp-suite']) && is_array($submenu['zp-suite'])) {
    $seen = [];
    foreach($submenu['zp-suite'] as $i=>$item){
      $slug = $item[2] ?? '';
      $label = wp_strip_all_tags($item[0] ?? '');
      if ($slug === 'zp-suite-stats' || stripos($label, 'Statystyki') !== false) {
        unset($submenu['zp-suite'][$i]);
        continue;
      }
      if ($slug && isset($seen[$slug])) unset($submenu['zp-suite'][$i]);
      $seen[$slug] = true;
    }
    $submenu['zp-suite'] = array_values($submenu['zp-suite']);
  }
  add_submenu_page('zp-suite','Statystyki','Statystyki','manage_options','zp-suite-stats','zp_suite_173_render_stats_page');
}, 999);

function zp_suite_173_render_stats_page(){
  if (!current_user_can('manage_options')) return;
  if (!empty($_POST['zp_suite_clear_stats']) && check_admin_referer('zp_suite_clear_stats')) {
    delete_option('zp_suite_analytics');
    $cc = function_exists('zp_suite_cc_get') ? zp_suite_cc_get() : [];
    if (is_array($cc)) { $cc['events']=[]; $cc['cta']=[]; $cc['scroll']=[]; $cc['sections']=[]; update_option('zp_suite_command_center', $cc, false); }
    echo '<div class="notice notice-success"><p>Statystyki ZP Suite zostały wyczyszczone.</p></div>';
  }
  $an = function_exists('zp_suite_analytics_get') ? zp_suite_analytics_get() : [];
  $cc = function_exists('zp_suite_cc_get') ? zp_suite_cc_get() : [];
  $days_data = $an['days'] ?? [];
  $pages = $an['pages'] ?? [];
  $perf = $an['perf'] ?? [];
  $leads_by_day = zp_suite_173_leads_by_day();
  $cta_by_day = zp_suite_173_events_by_day('cta');
  $section_events_by_day = zp_suite_173_events_by_day('section');

  $last7 = zp_suite_173_days_range(7);
  $last30 = zp_suite_173_days_range(30);
  $views7=[]; $uni7=[]; $leads7=[]; $cta7=[]; $speed7=[]; $labels7=[];
  foreach($last7 as $day){
    $d = $days_data[$day] ?? [];
    $views = (int)($d['views'] ?? 0);
    $vis = count($d['visitors'] ?? []);
    $avg_load = !empty($d['load_samples']) ? round(($d['load_sum'] ?? 0)/max(1,$d['load_samples'])) : 0;
    $avg_weight = $views ? round(($d['weight_sum'] ?? 0)/max(1,$views)) : 0;
    $views7[]=$views; $uni7[]=$vis; $leads7[]=(int)($leads_by_day[$day]??0); $cta7[]=(int)($cta_by_day[$day]??0); $speed7[]=zp_suite_173_speed_score($avg_load,$avg_weight); $labels7[]=zp_suite_173_date_label($day);
  }
  $views30=[]; $leads30=[]; $cta30=[]; $labels30=[]; $speed30=[];
  foreach($last30 as $day){
    $d = $days_data[$day] ?? [];
    $views=(int)($d['views']??0); $avg_load=!empty($d['load_samples'])?round(($d['load_sum']??0)/max(1,$d['load_samples'])):0; $avg_weight=$views?round(($d['weight_sum']??0)/max(1,$views)):0;
    $views30[]=$views; $leads30[]=(int)($leads_by_day[$day]??0); $cta30[]=(int)($cta_by_day[$day]??0); $speed30[]=zp_suite_173_speed_score($avg_load,$avg_weight); $labels30[]=date_i18n('d.m', strtotime($day));
  }

  $total_views = 0; $vis=[];
  foreach($days_data as $d){ $total_views += (int)($d['views']??0); foreach(($d['visitors']??[]) as $k=>$x){ $vis[$k]=1; } }
  $leads = function_exists('zp_suite_leads_all') ? zp_suite_leads_all() : get_option('zp_suite_leads', []);
  $lead_count = is_array($leads) ? count($leads) : 0;
  $cta_total = 0; foreach(($cc['cta'] ?? []) as $c){ $cta_total += (int)($c['count'] ?? 0); }
  $samples = max(1,(int)($perf['samples']??0));
  $avg_load = function_exists('zp_suite_avg') ? zp_suite_avg($perf['load_sum']??0,$samples) : (int)round(($perf['load_sum']??0)/$samples);
  $avg_weight = function_exists('zp_suite_avg') ? zp_suite_avg($perf['weight_sum']??0,$samples) : (int)round(($perf['weight_sum']??0)/$samples);
  $avg_dom = function_exists('zp_suite_avg') ? zp_suite_avg($perf['dom_sum']??0,$samples) : (int)round(($perf['dom_sum']??0)/$samples);
  $avg_ttfb = function_exists('zp_suite_avg') ? zp_suite_avg($perf['ttfb_sum']??0,$samples) : (int)round(($perf['ttfb_sum']??0)/$samples);
  $speed_score = zp_suite_173_speed_score($avg_load,$avg_weight);

  uasort($pages, fn($a,$b)=>($b['views']??0)<=>($a['views']??0));
  $top_pages = array_slice($pages,0,12,true);
  $slow_pages = $pages;
  uasort($slow_pages, function($a,$b){ $as=max(1,(int)($a['samples']??1)); $bs=max(1,(int)($b['samples']??1)); return (($b['load_sum']??0)/$bs) <=> (($a['load_sum']??0)/$as); });
  $slow_pages = array_slice($slow_pages,0,10,true);
  $cta = array_values($cc['cta'] ?? []); usort($cta, fn($a,$b)=>($b['count']??0)<=>($a['count']??0)); $cta=array_slice($cta,0,10);
  $sections = array_values($cc['sections'] ?? []); usort($sections, fn($a,$b)=>($b['views']??0)<=>($a['views']??0)); $sections=array_slice($sections,0,12);
  $errors404 = $cc['404'] ?? []; uasort($errors404, fn($a,$b)=>($b['count']??0)<=>($a['count']??0)); $errors404=array_slice($errors404,0,8,true);
  $scroll = $cc['scroll'] ?? [];

  $fmt_ms = function($v){ return function_exists('zp_suite_fmt_ms') ? zp_suite_fmt_ms($v) : ((int)$v ? number_format(((int)$v)/1000,2,',',' ').' s' : '—'); };
  $fmt_kb = function($v){ return function_exists('zp_suite_fmt_kb') ? zp_suite_fmt_kb($v) : ((int)$v ? number_format((int)$v,0,',',' ').' KB' : '—'); };
  $conv_cta = $total_views ? round(($cta_total/$total_views)*100, 1) : 0;
  $conv_lead = $total_views ? round(($lead_count/$total_views)*100, 2) : 0;
  ?>
  <div class="zpSuiteAdmin zpSuiteStatsFinal">
    <div class="zp173Hero">
      <span class="zp173Badge">Zaprojektowani Suite 1.7.3 • Analytics Final</span>
      <h1>Statystyki ruchu, leadów, konwersji i szybkości w jednym miejscu.</h1>
      <p>Ten widok zastępuje stare duplikaty statystyk. Command Center zostaje szybkim skrótem, a pełna analiza tygodnia, miesiąca, CTA, sekcji i wydajności jest tutaj.</p>
      <div class="zp173Kpis">
        <div class="zp173Kpi"><strong><?php echo esc_html($total_views); ?></strong><span>odsłon łącznie</span></div>
        <div class="zp173Kpi"><strong><?php echo esc_html(count($vis)); ?></strong><span>unikalni użytkownicy</span></div>
        <div class="zp173Kpi"><strong><?php echo esc_html($lead_count); ?></strong><span>leady</span></div>
        <div class="zp173Kpi"><strong><?php echo esc_html($speed_score); ?>/100</strong><span>speed score</span></div>
      </div>
    </div>

    <div class="zp173Grid3">
      <div class="zp173Card"><h2>Speed score</h2><div class="zp173Speed"><div class="zp173Ring" style="--p:<?php echo esc_attr($speed_score); ?>"><strong><?php echo esc_html($speed_score); ?></strong><span>/100</span></div></div><p>Średni load: <b><?php echo esc_html($fmt_ms($avg_load)); ?></b><br>DOM ready: <b><?php echo esc_html($fmt_ms($avg_dom)); ?></b><br>TTFB: <b><?php echo esc_html($fmt_ms($avg_ttfb)); ?></b><br>Średnia waga: <b><?php echo esc_html($fmt_kb($avg_weight)); ?></b></p></div>
      <div class="zp173Card"><h2>Lejek konwersji</h2><div class="zp173Funnel"><div class="zp173FunnelRow"><span>Odsłony</span><div class="zp173FunnelTrack"><i style="width:100%"></i></div><b><?php echo esc_html($total_views); ?></b></div><div class="zp173FunnelRow"><span>Kliknięcia CTA</span><div class="zp173FunnelTrack"><i style="width:<?php echo esc_attr(min(100,$conv_cta*4)); ?>%"></i></div><b><?php echo esc_html($cta_total); ?></b></div><div class="zp173FunnelRow"><span>Leady</span><div class="zp173FunnelTrack"><i style="width:<?php echo esc_attr(min(100,$conv_lead*20)); ?>%"></i></div><b><?php echo esc_html($lead_count); ?></b></div></div><p><span class="zp173Pill">CTA / odsłony <?php echo esc_html($conv_cta); ?>%</span> <span class="zp173Pill">lead / odsłony <?php echo esc_html($conv_lead); ?>%</span></p></div>
      <div class="zp173Card"><h2>Alerty</h2><?php if($speed_score<70): ?><div class="zp173Notice"><b>Speed:</b> wynik jest poniżej 70. Sprawdź najwolniejsze URL-e i ciężkie obrazy.</div><?php else: ?><div class="zp173Notice"><b>Speed:</b> wynik wygląda dobrze. Kontroluj wagę hero i video przy kolejnych sekcjach.</div><?php endif; ?><br><?php if(!empty($errors404)): ?><div class="zp173Notice"><b>404:</b> wykryto adresy 404. Sprawdź tabelę niżej i dodaj redirect, jeśli adres się powtarza.</div><?php else: ?><div class="zp173Notice"><b>404:</b> brak powtarzających się błędów w monitorze.</div><?php endif; ?><div class="zp173Actions"><form method="post"><?php wp_nonce_field('zp_suite_clear_stats'); ?><button class="zp173Btn" name="zp_suite_clear_stats" value="1" onclick="return confirm('Wyczyścić statystyki i eventy ZP Suite?')">Wyczyść pomiary</button></form></div></div>
    </div>

    <div class="zp173Grid">
      <div class="zp173Card"><h2>Tydzień dzień po dniu — odsłony</h2><?php echo zp_suite_173_bars($labels7,$views7); ?></div>
      <div class="zp173Card"><h2>Tydzień — leady i CTA</h2><?php echo zp_suite_173_bars($labels7,$leads7,' leadów'); ?><h2 style="margin-top:20px">Kliknięcia CTA</h2><?php echo zp_suite_173_bars($labels7,$cta7,' kliknięć'); ?></div>
    </div>

    <div class="zp173Grid">
      <div class="zp173Card"><h2>Odsłony — ostatnie 30 dni</h2><?php echo zp_suite_173_svg_line_chart($views30,'#1c477a',170); ?><div class="zp173MiniLabels"><?php echo esc_html(reset($labels30)); ?> → <?php echo esc_html(end($labels30)); ?></div></div>
      <div class="zp173Card"><h2>Speed score — ostatnie 30 dni</h2><?php echo zp_suite_173_svg_line_chart($speed30,'#16a34a',170); ?><p>Wykres opiera się na realnych pomiarach użytkowników z przeglądarki. Jeśli dany dzień nie ma pomiarów, score może być orientacyjny.</p></div>
    </div>

    <div class="zp173Grid">
      <div class="zp173Card"><h2>Najczęściej odwiedzane podstrony</h2><table class="widefat striped"><thead><tr><th>URL</th><th>Odsłony</th><th>Unikalni</th><th>Load</th><th>Waga</th><th>Ostatnio</th></tr></thead><tbody><?php foreach($top_pages as $p): $sm=max(1,(int)($p['samples']??1)); ?><tr><td><code><?php echo esc_html($p['path']??''); ?></code><br><small><?php echo esc_html($p['title']??''); ?></small></td><td><strong><?php echo esc_html($p['views']??0); ?></strong></td><td><?php echo esc_html(count($p['visitors']??[])); ?></td><td><?php echo esc_html($fmt_ms(round(($p['load_sum']??0)/$sm))); ?></td><td><?php echo esc_html($fmt_kb(round(($p['weight_sum']??0)/$sm))); ?></td><td><?php echo esc_html($p['last_at']??''); ?></td></tr><?php endforeach; ?></tbody></table></div>
      <div class="zp173Card"><h2>Najwolniejsze URL-e</h2><table class="widefat striped"><thead><tr><th>URL</th><th>Śr. load</th><th>Śr. waga</th><th>Próbki</th></tr></thead><tbody><?php foreach($slow_pages as $p): $sm=max(1,(int)($p['samples']??1)); ?><tr><td><code><?php echo esc_html($p['path']??''); ?></code></td><td><strong><?php echo esc_html($fmt_ms(round(($p['load_sum']??0)/$sm))); ?></strong></td><td><?php echo esc_html($fmt_kb(round(($p['weight_sum']??0)/$sm))); ?></td><td><?php echo esc_html($sm); ?></td></tr><?php endforeach; ?></tbody></table></div>
    </div>

    <div class="zp173Grid">
      <div class="zp173Card"><h2>Najczęściej klikane CTA</h2><table class="widefat striped"><thead><tr><th>CTA</th><th>Kliknięcia</th><th>Ostatnio</th></tr></thead><tbody><?php foreach($cta as $c): ?><tr><td><strong><?php echo esc_html($c['label']??'CTA'); ?></strong></td><td><?php echo esc_html($c['count']??0); ?></td><td><?php echo esc_html($c['last_at']??''); ?></td></tr><?php endforeach; ?></tbody></table></div>
      <div class="zp173Card"><h2>Najczęściej oglądane sekcje home</h2><table class="widefat striped"><thead><tr><th>Sekcja</th><th>Widoczność</th><th>Ostatnio</th></tr></thead><tbody><?php foreach($sections as $s): ?><tr><td><strong><?php echo esc_html($s['label']??'sekcja'); ?></strong></td><td><?php echo esc_html($s['views']??0); ?></td><td><?php echo esc_html($s['last_at']??''); ?></td></tr><?php endforeach; ?></tbody></table></div>
    </div>

    <div class="zp173Grid">
      <div class="zp173Card"><h2>Scroll depth</h2><table class="widefat striped"><thead><tr><th>URL</th><th>25%</th><th>50%</th><th>75%</th><th>100%</th></tr></thead><tbody><?php foreach(array_slice($scroll,0,12,true) as $path=>$s): ?><tr><td><code><?php echo esc_html($path); ?></code></td><td><?php echo esc_html($s['25']??0); ?></td><td><?php echo esc_html($s['50']??0); ?></td><td><?php echo esc_html($s['75']??0); ?></td><td><?php echo esc_html($s['100']??0); ?></td></tr><?php endforeach; ?></tbody></table></div>
      <div class="zp173Card"><h2>Monitor 404</h2><table class="widefat striped"><thead><tr><th>Adres</th><th>Trafienia</th><th>Ostatnio</th></tr></thead><tbody><?php foreach($errors404 as $path=>$r): ?><tr><td><code><?php echo esc_html($path); ?></code></td><td><strong><?php echo esc_html($r['count']??0); ?></strong></td><td><?php echo esc_html($r['last_at']??''); ?></td></tr><?php endforeach; ?></tbody></table></div>
    </div>
  </div>
  <?php
}
