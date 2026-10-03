<?php
if (!defined('ABSPATH')) { exit; }

function zp_suite_analytics_default(){
  return [
    'days' => [],
    'pages' => [],
    'recent' => [],
    'perf' => [
      'samples' => 0,
      'load_sum' => 0,
      'dom_sum' => 0,
      'ttfb_sum' => 0,
      'weight_sum' => 0,
      'last_load' => 0,
      'last_weight' => 0,
      'last_path' => '',
      'last_at' => '',
    ],
  ];
}

function zp_suite_analytics_get(){
  $data = get_option('zp_suite_analytics', []);
  if (!is_array($data)) $data = [];
  return array_replace_recursive(zp_suite_analytics_default(), $data);
}

function zp_suite_analytics_save($data){
  update_option('zp_suite_analytics', $data, false);
}

function zp_suite_hash_visitor($raw){
  $raw = (string)$raw;
  if ($raw === '') $raw = wp_generate_uuid4();
  return substr(hash('sha256', wp_salt('auth') . '|' . $raw), 0, 24);
}

add_action('wp_ajax_zp_suite_track', 'zp_suite_track_ajax');
add_action('wp_ajax_nopriv_zp_suite_track', 'zp_suite_track_ajax');
function zp_suite_track_ajax(){
  $path = isset($_POST['path']) ? esc_url_raw(wp_unslash($_POST['path'])) : '/';
  $title = isset($_POST['title']) ? sanitize_text_field(wp_unslash($_POST['title'])) : '';
  $ref = isset($_POST['ref']) ? esc_url_raw(wp_unslash($_POST['ref'])) : '';
  $vid = isset($_POST['vid']) ? sanitize_text_field(wp_unslash($_POST['vid'])) : '';
  $visitor = zp_suite_hash_visitor($vid ?: ($_COOKIE['zp_suite_vid'] ?? ''));
  $day = current_time('Y-m-d');
  $now = current_time('mysql');

  $load = isset($_POST['load']) ? max(0, (int) $_POST['load']) : 0;
  $dom = isset($_POST['dom']) ? max(0, (int) $_POST['dom']) : 0;
  $ttfb = isset($_POST['ttfb']) ? max(0, (int) $_POST['ttfb']) : 0;
  $weight = isset($_POST['weight']) ? max(0, (int) $_POST['weight']) : 0;

  $data = zp_suite_analytics_get();

  if (!isset($data['days'][$day])) $data['days'][$day] = ['views'=>0,'visitors'=>[],'load_sum'=>0,'samples'=>0,'weight_sum'=>0];
  $data['days'][$day]['views']++;
  $data['days'][$day]['visitors'][$visitor] = 1;
  if ($load) { $data['days'][$day]['load_sum'] += $load; $data['days'][$day]['samples']++; }
  if ($weight) { $data['days'][$day]['weight_sum'] += $weight; }

  $key = $path ?: '/';
  if (!isset($data['pages'][$key])) $data['pages'][$key] = ['path'=>$key,'title'=>$title,'views'=>0,'visitors'=>[],'load_sum'=>0,'samples'=>0,'weight_sum'=>0,'last_at'=>''];
  $data['pages'][$key]['views']++;
  $data['pages'][$key]['visitors'][$visitor] = 1;
  if ($title) $data['pages'][$key]['title'] = $title;
  if ($load) { $data['pages'][$key]['load_sum'] += $load; $data['pages'][$key]['samples']++; }
  if ($weight) $data['pages'][$key]['weight_sum'] += $weight;
  $data['pages'][$key]['last_at'] = $now;

  if ($load || $weight || $ttfb || $dom) {
    $data['perf']['samples'] = (int)($data['perf']['samples'] ?? 0) + 1;
    $data['perf']['load_sum'] = (int)($data['perf']['load_sum'] ?? 0) + $load;
    $data['perf']['dom_sum'] = (int)($data['perf']['dom_sum'] ?? 0) + $dom;
    $data['perf']['ttfb_sum'] = (int)($data['perf']['ttfb_sum'] ?? 0) + $ttfb;
    $data['perf']['weight_sum'] = (int)($data['perf']['weight_sum'] ?? 0) + $weight;
    $data['perf']['last_load'] = $load;
    $data['perf']['last_weight'] = $weight;
    $data['perf']['last_path'] = $path;
    $data['perf']['last_at'] = $now;
  }

  array_unshift($data['recent'], ['at'=>$now,'path'=>$path,'title'=>$title,'ref'=>$ref,'load'=>$load,'weight'=>$weight]);
  $data['recent'] = array_slice($data['recent'], 0, 80);
  if (count($data['days']) > 90) { ksort($data['days']); $data['days'] = array_slice($data['days'], -90, null, true); }
  if (count($data['pages']) > 300) {
    uasort($data['pages'], fn($a,$b)=>($b['views']??0)<=>($a['views']??0));
    $data['pages'] = array_slice($data['pages'], 0, 300, true);
  }
  zp_suite_analytics_save($data);
  wp_send_json_success(['ok'=>1]);
}

add_action('wp_footer', function(){
  if (is_admin()) return;
  $ajax = admin_url('admin-ajax.php');
  ?>
  <script id="zp-suite-analytics-js">
  (function(){
    try{
      var key='zp_suite_vid';
      var vid=localStorage.getItem(key);
      if(!vid){ vid=(crypto&&crypto.randomUUID?crypto.randomUUID():String(Date.now())+Math.random().toString(16).slice(2)); localStorage.setItem(key,vid); }
      function collectWeight(){
        var total=0;
        if(performance && performance.getEntriesByType){
          performance.getEntriesByType('resource').forEach(function(r){ if(r.transferSize) total += r.transferSize; });
          var nav=performance.getEntriesByType('navigation')[0]; if(nav && nav.transferSize) total += nav.transferSize;
        }
        return Math.round(total/1024);
      }
      function send(){
        var nav=(performance&&performance.getEntriesByType)?performance.getEntriesByType('navigation')[0]:null;
        var load=0,dom=0,ttfb=0;
        if(nav){ load=Math.round(nav.loadEventEnd-nav.startTime); dom=Math.round(nav.domContentLoadedEventEnd-nav.startTime); ttfb=Math.round(nav.responseStart-nav.requestStart); }
        var data=new FormData();
        data.append('action','zp_suite_track');
        data.append('path',location.pathname+location.search);
        data.append('title',document.title||'');
        data.append('ref',document.referrer||'');
        data.append('vid',vid);
        data.append('load',Math.max(0,load));
        data.append('dom',Math.max(0,dom));
        data.append('ttfb',Math.max(0,ttfb));
        data.append('weight',collectWeight());
        if(navigator.sendBeacon){ navigator.sendBeacon('<?php echo esc_url($ajax); ?>', data); }
        else { fetch('<?php echo esc_url($ajax); ?>',{method:'POST',body:data,keepalive:true,credentials:'same-origin'}).catch(function(){}); }
      }
      window.addEventListener('load',function(){ setTimeout(send, window.zpSuiteAnalyticsDelay || 2200); },{once:true,passive:true});
    }catch(e){}
  })();
  </script>
  <?php
}, 99);

add_action('admin_menu', function(){
  add_submenu_page('zp-suite','Statystyki i szybkość','Statystyki i szybkość','manage_options','zp-suite-stats','zp_suite_render_stats_page');
}, 20);

function zp_suite_fmt_ms($v){ $v=(int)$v; return $v ? number_format($v/1000,2,',',' ').' s' : '—'; }
function zp_suite_fmt_kb($v){ $v=(int)$v; if(!$v) return '—'; return $v>1024 ? number_format($v/1024,2,',',' ').' MB' : number_format($v,0,',',' ').' KB'; }
function zp_suite_avg($sum,$n){ return $n ? (int)round($sum/$n) : 0; }

function zp_suite_render_stats_page(){
  if (!current_user_can('manage_options')) return;
  if (!empty($_POST['zp_suite_clear_stats']) && check_admin_referer('zp_suite_clear_stats')) {
    delete_option('zp_suite_analytics');
    echo '<div class="notice notice-success"><p>Statystyki ZP Suite zostały wyczyszczone.</p></div>';
  }
  $data = zp_suite_analytics_get();
  $days = $data['days']; ksort($days); $days = array_slice($days, -30, null, true);
  $pages = $data['pages']; uasort($pages, fn($a,$b)=>($b['views']??0)<=>($a['views']??0)); $top = array_slice($pages,0,15,true);
  $total_views = 0; $vis=[]; foreach($data['days'] as $d){ $total_views += (int)($d['views']??0); foreach(($d['visitors']??[]) as $k=>$x){$vis[$k]=1;} }
  $perf = $data['perf']; $samples = max(1,(int)($perf['samples']??0));
  $avg_load = zp_suite_avg($perf['load_sum']??0,$samples); $avg_dom=zp_suite_avg($perf['dom_sum']??0,$samples); $avg_ttfb=zp_suite_avg($perf['ttfb_sum']??0,$samples); $avg_weight=zp_suite_avg($perf['weight_sum']??0,$samples);
  $max_day = 1; foreach($days as $d){ $max_day=max($max_day,(int)($d['views']??0)); }
  ?>
  <div class="zpSuiteAdmin">
    <div class="zpSuiteHero"><span class="zpSuiteBadge">Zaprojektowani Suite 1.5.0 • Analytics</span><h1>Statystyki, najczęściej odwiedzane podstrony i szybkość ładowania.</h1><p>Lekkie statystyki własne bez zewnętrznych skryptów. Dane są orientacyjne i służą do kontroli ruchu, wag stron oraz realnych czasów ładowania w przeglądarkach użytkowników.</p><div class="zpStatus"><div class="zpStat"><strong><?php echo esc_html($total_views); ?></strong><span>odsłon</span></div><div class="zpStat"><strong><?php echo esc_html(count($vis)); ?></strong><span>unikatowych użytk.</span></div><div class="zpStat"><strong><?php echo esc_html(zp_suite_fmt_ms($avg_load)); ?></strong><span>średni load</span></div><div class="zpStat"><strong><?php echo esc_html(zp_suite_fmt_kb($avg_weight)); ?></strong><span>średnia waga</span></div></div></div>
    <div class="zpGrid" style="margin-top:18px">
      <div class="zpCard"><h2>Odsłony z ostatnich 30 dni</h2><div class="zpChartBars"><?php foreach($days as $day=>$d): $h=max(4,round(((int)($d['views']??0)/$max_day)*120)); ?><div class="zpBar" title="<?php echo esc_attr($day.' — '.$d['views'].' odsłon'); ?>"><i style="height:<?php echo esc_attr($h); ?>px"></i><span><?php echo esc_html(substr($day,5)); ?></span></div><?php endforeach; ?></div></div>
      <div class="zpCard"><h2>Szybkość i waga strony</h2><div class="zpPerfGrid"><div><strong><?php echo esc_html(zp_suite_fmt_ms($avg_load)); ?></strong><span>średni pełny load</span></div><div><strong><?php echo esc_html(zp_suite_fmt_ms($avg_dom)); ?></strong><span>średni DOM ready</span></div><div><strong><?php echo esc_html(zp_suite_fmt_ms($avg_ttfb)); ?></strong><span>średni TTFB</span></div><div><strong><?php echo esc_html(zp_suite_fmt_kb($avg_weight)); ?></strong><span>średni transfer</span></div></div><p>Ostatni pomiar: <strong><?php echo esc_html(zp_suite_fmt_ms($perf['last_load']??0)); ?></strong>, <?php echo esc_html(zp_suite_fmt_kb($perf['last_weight']??0)); ?>, <?php echo esc_html($perf['last_path']??''); ?></p></div>
    </div>
    <div class="zpCard"><h2>Najczęściej odwiedzane podstrony</h2><table class="widefat striped"><thead><tr><th>URL</th><th>Tytuł</th><th>Odsłony</th><th>Unikatowi</th><th>Śr. load</th><th>Śr. waga</th><th>Ostatnio</th></tr></thead><tbody><?php foreach($top as $p): $u=count($p['visitors']??[]); $avg=zp_suite_avg($p['load_sum']??0,max(1,(int)($p['samples']??0))); $w=zp_suite_avg($p['weight_sum']??0,max(1,(int)($p['views']??0))); ?><tr><td><code><?php echo esc_html($p['path']??''); ?></code></td><td><?php echo esc_html($p['title']??''); ?></td><td><strong><?php echo esc_html($p['views']??0); ?></strong></td><td><?php echo esc_html($u); ?></td><td><?php echo esc_html(zp_suite_fmt_ms($avg)); ?></td><td><?php echo esc_html(zp_suite_fmt_kb($w)); ?></td><td><?php echo esc_html($p['last_at']??''); ?></td></tr><?php endforeach; ?></tbody></table></div>
    <div class="zpGrid"><div class="zpCard"><h2>Ostatnie wejścia</h2><table class="widefat striped"><thead><tr><th>Czas</th><th>URL</th><th>Load</th><th>Waga</th></tr></thead><tbody><?php foreach(array_slice($data['recent'],0,20) as $r): ?><tr><td><?php echo esc_html($r['at']??''); ?></td><td><code><?php echo esc_html($r['path']??''); ?></code></td><td><?php echo esc_html(zp_suite_fmt_ms($r['load']??0)); ?></td><td><?php echo esc_html(zp_suite_fmt_kb($r['weight']??0)); ?></td></tr><?php endforeach; ?></tbody></table></div><div class="zpCard"><h2>Narzędzia</h2><form method="post"><?php wp_nonce_field('zp_suite_clear_stats'); ?><p>Statystyki są trzymane lokalnie w WordPressie. Wyczyść je, jeśli chcesz zacząć pomiar od nowa po większym wdrożeniu.</p><button class="button" name="zp_suite_clear_stats" value="1" onclick="return confirm('Wyczyścić statystyki ZP Suite?')">Wyczyść statystyki</button></form></div></div>
  </div>
  <style>.zpChartBars{display:flex;align-items:flex-end;gap:8px;height:160px;border-radius:18px;background:#f8fafc;border:1px solid #e2e8f0;padding:18px;overflow:auto}.zpBar{min-width:28px;text-align:center}.zpBar i{display:block;width:100%;border-radius:999px 999px 4px 4px;background:linear-gradient(180deg,#1c477a,#071426)}.zpBar span{display:block;margin-top:8px;font-size:10px;color:#64748b;transform:rotate(-38deg);white-space:nowrap}.zpPerfGrid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}.zpPerfGrid div{border:1px solid #dfe4ec;border-radius:16px;padding:14px;background:#f8fafc}.zpPerfGrid strong{display:block;font-size:24px;color:#071426}.zpPerfGrid span{display:block;font-size:11px;text-transform:uppercase;letter-spacing:.1em;color:#6d7688}</style>
  <?php
}
