<?php
if (!defined('ABSPATH')) { exit; }

function zp_suite_cc_defaults(){
  return [
    'events'=>[], 'scroll'=>[], 'cta'=>[], 'sections'=>[], '404'=>[], 'redirects'=>[], 'settings'=>[
      'exit_popup_enabled'=>'1','exit_popup_delay'=>'8500','exit_popup_once'=>'1','safe_mode'=>'0'
    ]
  ];
}
function zp_suite_cc_get(){ $d=get_option('zp_suite_command_center', []); return array_replace_recursive(zp_suite_cc_defaults(), is_array($d)?$d:[]); }
function zp_suite_cc_save($d){ update_option('zp_suite_command_center', $d, false); }
function zp_suite_cc_now(){ return current_time('mysql'); }
function zp_suite_cc_path(){ return isset($_SERVER['REQUEST_URI']) ? esc_url_raw(wp_unslash($_SERVER['REQUEST_URI'])) : '/'; }

add_action('wp_ajax_zp_suite_event','zp_suite_event_ajax');
add_action('wp_ajax_nopriv_zp_suite_event','zp_suite_event_ajax');
function zp_suite_event_ajax(){
  $type = isset($_POST['type']) ? sanitize_key(wp_unslash($_POST['type'])) : '';
  $label = isset($_POST['label']) ? sanitize_text_field(wp_unslash($_POST['label'])) : '';
  $path = isset($_POST['path']) ? esc_url_raw(wp_unslash($_POST['path'])) : zp_suite_cc_path();
  $value = isset($_POST['value']) ? sanitize_text_field(wp_unslash($_POST['value'])) : '';
  if (!$type) wp_send_json_error(['ok'=>0], 400);
  $d = zp_suite_cc_get();
  if ($type === 'cta') {
    $key = sanitize_title($label ?: 'cta');
    if (!isset($d['cta'][$key])) $d['cta'][$key]=['label'=>$label,'count'=>0,'paths'=>[],'last_at'=>''];
    $d['cta'][$key]['count']++;
    $d['cta'][$key]['paths'][$path] = (int)($d['cta'][$key]['paths'][$path] ?? 0) + 1;
    $d['cta'][$key]['last_at'] = zp_suite_cc_now();
  } elseif ($type === 'scroll') {
    $bucket = in_array($value, ['25','50','75','100'], true) ? $value : '0';
    if ($bucket) {
      if (!isset($d['scroll'][$path])) $d['scroll'][$path]=['25'=>0,'50'=>0,'75'=>0,'100'=>0,'last_at'=>''];
      $d['scroll'][$path][$bucket] = (int)$d['scroll'][$path][$bucket]+1;
      $d['scroll'][$path]['last_at'] = zp_suite_cc_now();
    }
  } elseif ($type === 'section') {
    $key = sanitize_title($label ?: 'section');
    if (!isset($d['sections'][$key])) $d['sections'][$key] = ['label'=>$label,'views'=>0,'paths'=>[],'last_at'=>''];
    $d['sections'][$key]['views'] = (int)($d['sections'][$key]['views'] ?? 0) + 1;
    $d['sections'][$key]['paths'][$path] = (int)($d['sections'][$key]['paths'][$path] ?? 0) + 1;
    $d['sections'][$key]['last_at'] = zp_suite_cc_now();
  } elseif ($type === 'popup') {
    $d['events'][] = ['at'=>zp_suite_cc_now(),'type'=>'popup','label'=>$label,'path'=>$path,'value'=>$value];
  }
  $d['events'][] = ['at'=>zp_suite_cc_now(),'type'=>$type,'label'=>$label,'path'=>$path,'value'=>$value];
  $d['events'] = array_slice($d['events'], -500);
  zp_suite_cc_save($d);
  wp_send_json_success(['ok'=>1]);
}

add_action('template_redirect', function(){
  if (is_admin()) return;
  // Redirect manager first
  $d = zp_suite_cc_get();
  $req = trailingslashit(parse_url(zp_suite_cc_path(), PHP_URL_PATH) ?: '/');
  foreach (($d['redirects'] ?? []) as $i=>$r) {
    if (empty($r['from']) || empty($r['to']) || (string)($r['enabled'] ?? '1') === '0') continue;
    $from = trailingslashit('/'.trim((string)$r['from'], '/').'/');
    $target_url = home_url((string) $r['to']);
    $target_path = trailingslashit((string) (wp_parse_url($target_url, PHP_URL_PATH) ?: '/'));
    /* Nigdy nie przekierowuj adresu na dokładnie ten sam adres. */
    if ($req === $from && $req !== $target_path) {
      $d['redirects'][$i]['hits'] = (int)($d['redirects'][$i]['hits'] ?? 0)+1;
      $d['redirects'][$i]['last_at'] = zp_suite_cc_now(); zp_suite_cc_save($d);
      wp_safe_redirect($target_url, (int)($r['code'] ?? 301)); exit;
    }
  }
  // Auto SEO alt pass for raw template images.
  ob_start('zp_suite_seo_alt_buffer');
}, 0);

add_action('template_redirect', function(){
  if (is_404()) {
    $d=zp_suite_cc_get(); $path=zp_suite_cc_path();
    if (!isset($d['404'][$path])) $d['404'][$path]=['count'=>0,'referrers'=>[],'last_at'=>''];
    $d['404'][$path]['count']++;
    $ref = isset($_SERVER['HTTP_REFERER']) ? esc_url_raw(wp_unslash($_SERVER['HTTP_REFERER'])) : '';
    if ($ref) $d['404'][$path]['referrers'][$ref] = (int)($d['404'][$path]['referrers'][$ref] ?? 0)+1;
    $d['404'][$path]['last_at']=zp_suite_cc_now(); zp_suite_cc_save($d);
  }
}, 20);

function zp_suite_seo_alt_from_src($src){
  $base = basename(parse_url($src, PHP_URL_PATH) ?: '');
  $base = preg_replace('/\.(webp|png|jpe?g|gif|svg)$/i','',$base);
  $base = preg_replace('/[-_]+/', ' ', $base);
  $base = preg_replace('/\bscaled\b|\bcopy\b|\bfinal\b|\bweb\b/i', '', $base);
  $base = trim(preg_replace('/\s+/', ' ', $base));
  if (!$base) $base = 'Zaprojektowani.com — projekt strony internetowej, sklepu lub brandingu';
  return 'Zaprojektowani.com — '.$base;
}
/**
 * 2.3.0: alt text comes from the media library; an image without one gets alt="" (decorative)
 * instead of a file name ("Zaprojektowani.com — crew mateusz"). An explicit empty alt stays empty.
 */
function zp_suite_seo_alt_library($src){
  static $cache = [];
  $src = (string) $src;
  if ($src === '' || stripos($src, '/wp-content/uploads/') === false) return '';
  if (!isset($cache[$src])) {
    $id = attachment_url_to_postid(preg_replace('/-\d+x\d+(?=\.[a-z0-9]+$)/i', '', strtok($src, '?')));
    $alt = $id ? trim((string) get_post_meta($id, '_wp_attachment_image_alt', true)) : '';
    $cache[$src] = $alt;
  }
  return $cache[$src];
}
function zp_suite_seo_alt_buffer($html){
  if (!is_string($html) || stripos($html, '<img') === false) return $html;
  if (function_exists('zp_seo_plan_active') && zp_seo_plan_active()) {
    return preg_replace_callback('/<img\b([^>]*?)>/i', function($m){
      $tag=$m[0];
      if (preg_match('/\salt\s*=/i', $tag)) return $tag;
      $src=''; if (preg_match('/\bsrc\s*=\s*(["\'])(.*?)\1/i', $tag, $sm)) $src=$sm[2];
      return preg_replace('/<img\b/i', '<img alt="'.esc_attr(zp_suite_seo_alt_library($src)).'"', $tag, 1);
    }, $html);
  }
  return preg_replace_callback('/<img\b([^>]*?)>/i', function($m){
    $tag=$m[0];
    if (preg_match('/\balt\s*=\s*(["\'])(.*?)\1/i', $tag, $am) && trim($am[2]) !== '') return $tag;
    $src=''; if (preg_match('/\bsrc\s*=\s*(["\'])(.*?)\1/i', $tag, $sm)) $src=$sm[2];
    $alt=esc_attr(zp_suite_seo_alt_from_src($src));
    if (preg_match('/\balt\s*=\s*(["\'])\s*\1/i', $tag)) return preg_replace('/\balt\s*=\s*(["\'])\s*\1/i', 'alt="'.$alt.'"', $tag, 1);
    return preg_replace('/<img\b/i', '<img alt="'.$alt.'"', $tag, 1);
  }, $html);
}


add_action('wp_head', function(){
  $d = zp_suite_cc_get();
  if ((string)($d['settings']['safe_mode'] ?? '0') !== '1') return;
  echo '<style id="zp-suite-safe-mode">html body [class*="zp"] *,html body [class*="zp"] *::before,html body [class*="zp"] *::after{animation-duration:.001ms!important;animation-iteration-count:1!important;transition-duration:.001ms!important;scroll-behavior:auto!important}html body [class*="zp"]{backdrop-filter: none;-webkit-backdrop-filter:none!important}</style>';
}, 3);

add_action('wp_footer', function(){
  if (is_admin()) return;
  $d=zp_suite_cc_get(); $s=$d['settings'] ?? [];
  $ajax=admin_url('admin-ajax.php'); $nonce=wp_create_nonce('zp_suite_contact');
  ?>
  <div class="zpExitConsult" id="zpExitConsult" aria-hidden="true">
    <div class="zpExitConsult__backdrop" data-zp-exit-close></div>
    <section class="zpExitConsult__box" role="dialog" aria-modal="true" aria-labelledby="zpExitConsultTitle">
      <button class="zpExitConsult__close" type="button" data-zp-exit-close aria-label="Zamknij">×</button>
      <span class="zpExitConsult__eyebrow">Bezpłatna konsultacja</span>
      <h2 id="zpExitConsultTitle">Zanim zamkniesz stronę — chcesz, żebyśmy oddzwonili?</h2>
      <p>Zostaw numer telefonu. Krótko dopytamy o projekt i podpowiemy, czy lepiej zacząć od strony, sklepu, brandingu, SEO czy kampanii.</p>
      <form class="zpExitConsult__form" data-zp-exit-form>
        <input type="hidden" name="action" value="zp_suite_contact"><input type="hidden" name="nonce" value="<?php echo esc_attr($nonce); ?>"><input type="hidden" name="contact_mode" value="phone"><input type="hidden" name="name" value="Popup — darmowa konsultacja"><input type="hidden" name="email" value=""><input type="hidden" name="services" value="Bezpłatna konsultacja / exit popup"><input type="hidden" name="message" value="Prośba o darmową konsultację przed wyjściem ze strony."><input type="hidden" name="consent" value="1">
        <label><span>Numer telefonu</span><input name="phone" type="tel" placeholder="np. 500 600 700" required></label>
        <button type="submit">Poproś o telefon</button>
      </form>
      <small>Bez spamu. Jeden krótki kontakt w sprawie Twojego projektu.</small>
    </section>
  </div>
  <style id="zp-exit-consult-css">.zpExitConsult,.zpExitConsult *{box-sizing:border-box}.zpExitConsult{position:fixed;inset:0;z-index:999999;display:none;font-family:var(--zp-font,"Plus Jakarta Sans Local",system-ui,sans-serif)}.zpExitConsult.is-open{display:block}.zpExitConsult__backdrop{position:absolute;inset:0;background:rgba(3,5,9,.42);backdrop-filter: none;-webkit-backdrop-filter:none}.zpExitConsult__box{position:absolute;left:50%;top:50%;width:min(520px,calc(100% - 28px));transform:translate(-50%,-50%);border-radius:28px;background:linear-gradient(180deg,#fff,#f7f9fc);border:1px solid rgba(7,20,38,.10);box-shadow:0 32px 90px rgba(7,20,38,.28);padding:28px;color:#071426;overflow:hidden}.zpExitConsult__box:before{content:"";position:absolute;inset:0 0 auto 0;height:5px;background:linear-gradient(90deg,#05070b,#102a4f,#1c477a)}.zpExitConsult__close{position:absolute;right:16px;top:14px;width:34px;height:34px;border:0;border-radius:999px;background:#edf2f7;color:#071426;font-size:22px;line-height:1;cursor:pointer}.zpExitConsult__eyebrow{display:inline-flex;margin-bottom:14px;text-transform:uppercase;letter-spacing:.14em;font-size:10px;font-weight:850;color:#1c477a}.zpExitConsult h2{margin:0;font-size:clamp(28px,5vw,42px);line-height:.98;letter-spacing:-.055em;color:#071426}.zpExitConsult p{margin:14px 0 20px;color:#5b6675;font-size:14px;line-height:1.58}.zpExitConsult__form{display:grid;grid-template-columns:1fr auto;gap:10px;align-items:end}.zpExitConsult__form label span{display:block;margin:0 0 8px;font-size:10px;text-transform:uppercase;letter-spacing:.12em;font-weight:850;color:#647084}.zpExitConsult__form input{width:100%;min-height:54px;border-radius:999px;border:1px solid #dfe5ee;background:#fff;padding:0 18px;font-weight:750;color:#071426}.zpExitConsult__form button{min-height:54px;border:0;border-radius:999px;background:linear-gradient(90deg,#05070b,#0b1830 42%,#102a4f 72%,#1c477a);color:#fff;padding:0 22px;font-weight:850;cursor:pointer;white-space:nowrap}.zpExitConsult small{display:block;margin-top:13px;color:#7a8494;font-size:11px}.zpExitConsult__msg{margin-top:12px;padding:12px 14px;border-radius:16px;background:#edf7ff;color:#102a4f;font-weight:800}@media(max-width:560px){.zpExitConsult__box{padding:24px 20px}.zpExitConsult__form{grid-template-columns:1fr}.zpExitConsult__form button{width:100%}}</style>
  <script id="zp-command-center-front-js">
  (function(){
    var ajax='<?php echo esc_js($ajax); ?>';
    function fd(type,label,value){try{var d=new FormData();d.append('action','zp_suite_event');d.append('type',type);d.append('label',label||'');d.append('value',value||'');d.append('path',location.pathname+location.search);if(navigator.sendBeacon){navigator.sendBeacon(ajax,d)}else{fetch(ajax,{method:'POST',body:d,keepalive:true,credentials:'same-origin'}).catch(function(){})}}catch(e){}}
    document.addEventListener('click',function(e){var a=e.target.closest&&e.target.closest('a,button'); if(!a) return; var txt=(a.innerText||a.getAttribute('aria-label')||a.href||'').trim().slice(0,90); if(/wycen|kontakt|telefon|whatsapp|realizac|portfolio|zamów|napisz|call|cta/i.test(txt) || (a.href&&/kontakt|tel:|mailto:|wa\.me/i.test(a.href))){fd('cta',txt,'click')}} ,{passive:true});
    var marks={25:0,50:0,75:0,100:0}; function onScroll(){var h=document.documentElement;var max=Math.max(1,h.scrollHeight-innerHeight);var p=Math.round((scrollY/max)*100);[25,50,75,100].forEach(function(m){if(p>=m&&!marks[m]){marks[m]=1;fd('scroll','scroll_depth',String(m));}})}; addEventListener('scroll',onScroll,{passive:true}); setTimeout(onScroll,1500);
    try{
      var sectionSelectors='.zpNewHero,.zpTrustPinned,#zpServicesPath,.zpShowcase,.zpLaptopShowcase,.zpShowcaseServices,.zpRevEd,.zpSeoFaq,.zpSeoIndustries,.zpContactSystem,.zpAboutExperience,#zpAboutExperience,[data-zp-section]';
      var seen={};
      var labelFor=function(el){return el.getAttribute('data-zp-section')||el.id||String(el.className||'sekcja').split(' ')[0]||'sekcja';};
      if('IntersectionObserver' in window){
        var sio=new IntersectionObserver(function(entries){entries.forEach(function(en){if(!en.isIntersecting)return;var label=labelFor(en.target);if(seen[label])return;seen[label]=1;fd('section',label,'view');});},{threshold:.35,rootMargin:'0px 0px -12% 0px'});
        document.querySelectorAll(sectionSelectors).forEach(function(el){sio.observe(el);});
      }
    }catch(e){}
    var enabled='<?php echo esc_js($s['exit_popup_enabled'] ?? '1'); ?>'==='1'; if(enabled){
      var shown=sessionStorage.getItem('zpExitConsultShown')==='1'; var box=document.getElementById('zpExitConsult'); var form=box&&box.querySelector('[data-zp-exit-form]');
      function openPopup(reason){ if(shown||!box) return; shown=true; sessionStorage.setItem('zpExitConsultShown','1'); box.classList.add('is-open'); box.setAttribute('aria-hidden','false'); fd('popup','open',reason||''); setTimeout(function(){var i=box.querySelector('input[name="phone"]'); if(i) i.focus();},120); }
      function closePopup(){ if(!box) return; box.classList.remove('is-open'); box.setAttribute('aria-hidden','true'); }
      document.addEventListener('mouseleave',function(e){ if(e.clientY<=4) openPopup('exit-intent'); });
      setTimeout(function(){ if(!shown && (window.scrollY>500 || document.visibilityState==='visible')) openPopup('time-delay'); }, parseInt('<?php echo esc_js($s['exit_popup_delay'] ?? '8500'); ?>',10)||8500);
      document.addEventListener('click',function(e){ if(e.target.matches('[data-zp-exit-close]')) closePopup(); });
      if(form){ form.addEventListener('submit',function(e){ e.preventDefault(); var btn=form.querySelector('button'); if(btn) btn.disabled=true; (window.zpSuiteSecureFormData?window.zpSuiteSecureFormData(new FormData(form)):Promise.resolve(new FormData(form))).then(function(secureFd){return fetch(ajax,{method:'POST',body:secureFd,credentials:'same-origin'});}).then(function(r){return r.json()}).then(function(j){ var m=document.createElement('div'); m.className='zpExitConsult__msg'; m.textContent=(j&&j.success)?'Dziękujemy — przyjęliśmy prośbę o telefon.':'Sprawdź numer telefonu i spróbuj ponownie.'; form.appendChild(m); fd('popup','submit',(j&&j.success)?'success':'error'); if(j&&j.success){setTimeout(closePopup,1800);} }).catch(function(){alert('Nie udało się wysłać. Spróbuj ponownie.');}).finally(function(){if(btn) btn.disabled=false;}); }); }
    }
  })();
  </script>
  <?php
}, 120);

add_action('admin_menu', function(){
  add_submenu_page('zp-suite','Redirecty i 404','Redirecty i 404','manage_options','zp-suite-redirects','zp_suite_render_redirects_page');
  add_submenu_page('zp-suite','Obrazy / ALT SEO','Obrazy / ALT SEO','manage_options','zp-suite-images','zp_suite_render_images_page');
}, 40);


function zp_suite_cc_speed_score($load, $weight, $ttfb = 0){
  $load = max(0, (int)$load);
  $weight = max(0, (int)$weight);
  $ttfb = max(0, (int)$ttfb);
  $score = 100;
  $score -= max(0, $load - 1200) / 42;
  $score -= max(0, $weight - 1400) / 42;
  $score -= max(0, $ttfb - 300) / 18;
  return max(0, min(100, (int)round($score)));
}

function zp_suite_cc_weekday_pl($date){
  $map = ['Mon'=>'Poniedziałek','Tue'=>'Wtorek','Wed'=>'Środa','Thu'=>'Czwartek','Fri'=>'Piątek','Sat'=>'Sobota','Sun'=>'Niedziela'];
  $key = gmdate('D', strtotime($date.' 12:00:00'));
  return $map[$key] ?? $date;
}

function zp_suite_cc_recent_days($days, $range = 7){
  $out = [];
  for ($i = $range - 1; $i >= 0; $i--) {
    $date = wp_date('Y-m-d', strtotime('-'.$i.' days', current_time('timestamp')));
    $d = $days[$date] ?? [];
    $samples = max(1, (int)($d['samples'] ?? 0));
    $out[] = [
      'date' => $date,
      'weekday' => zp_suite_cc_weekday_pl($date),
      'views' => (int)($d['views'] ?? 0),
      'unique' => count($d['visitors'] ?? []),
      'load' => (int)round(((int)($d['load_sum'] ?? 0)) / $samples),
      'weight' => (int)round(((int)($d['weight_sum'] ?? 0)) / $samples),
    ];
  }
  return $out;
}

function zp_suite_cc_count_leads_by_day($leads){
  $out=[];
  foreach($leads as $l){
    $at = $l['created_at'] ?? '';
    if (!$at) continue;
    $day = substr($at,0,10);
    $out[$day] = (int)($out[$day] ?? 0) + 1;
  }
  return $out;
}

function zp_suite_cc_count_events_by_day($events, $type){
  $out=[];
  foreach($events as $e){
    if (($e['type'] ?? '') !== $type) continue;
    $day = substr((string)($e['at'] ?? ''),0,10);
    if (!$day) continue;
    $out[$day] = (int)($out[$day] ?? 0) + 1;
  }
  return $out;
}

function zp_suite_cc_chart_rows($rows, $metric, $suffix = ''){
  $max = 1;
  foreach($rows as $r){ $max = max($max, (int)($r[$metric] ?? 0)); }
  echo '<div class="zpChartBars">';
  foreach($rows as $r){
    $val = (int)($r[$metric] ?? 0);
    $w = $max ? round(($val/$max)*100) : 0;
    echo '<div class="zpChartRow"><strong>'.esc_html(substr($r['weekday'],0,3)).'<br><small>'.esc_html(substr($r['date'],5)).'</small></strong><span class="zpChartTrack"><i class="zpChartFill" style="display:block;width:'.esc_attr($w).'%"></i></span><b>'.esc_html($val.$suffix).'</b></div>';
  }
  echo '</div>';
}

function zp_suite_render_home_quality_guard(){
  $opts = get_option('zp_suite_options', []);
  $cms = get_option('zp_suite_cms', []);
  $order_raw = $opts['home_order']['order'] ?? '';
  $checks = [
    'Jedno mocne H1 w hero' => !empty($opts['hero']['h1_html'] ?? ''),
    'Lead hero nie jest za długi' => mb_strlen(wp_strip_all_tags($opts['hero']['lead'] ?? '')) <= 260,
    'Meta title home ustawiony' => !empty($opts['seo']['home_title'] ?? ''),
    'Meta description home ustawiony' => !empty($opts['seo']['home_description'] ?? ''),
    'FAQ ma minimum 5 pytań' => count($cms['faq'] ?? []) >= 5,
    'Portfolio ma minimum 6 projektów' => (count($cms['portfolio']['web'] ?? []) + count($cms['portfolio']['logo'] ?? [])) >= 6,
    'Opinie są aktywne' => count($cms['reviews'] ?? []) >= 3,
    'Sekcja branż jest aktywna' => (string)($opts['visibility']['seo_industries'] ?? '1') !== '0',
    'Kontakt jest aktywny' => (string)($opts['visibility']['contact_system'] ?? '1') !== '0',
    'Kolejność sekcji zapisana' => !empty($order_raw),
  ];
  echo '<div class="zpQualityList">';
  foreach($checks as $label=>$ok){
    echo '<div class="zpNoticeMini '.($ok?'ok':'bad').'"><strong>'.($ok?'✓':'!').'</strong> '.esc_html($label).'</div>';
  }
  echo '</div>';
}

function zp_suite_render_command_center_page(){
  if (!current_user_can('manage_options')) return;
  if (!empty($_POST['zp_cc_save']) && check_admin_referer('zp_cc_save')) {
    $d_save = zp_suite_cc_get();
    $d_save['settings']['exit_popup_enabled'] = !empty($_POST['exit_popup_enabled']) ? '1' : '0';
    $d_save['settings']['safe_mode'] = !empty($_POST['safe_mode']) ? '1' : '0';
    $d_save['settings']['exit_popup_delay'] = max(2000, (int)($_POST['exit_popup_delay'] ?? 8500));
    zp_suite_cc_save($d_save);
    echo '<div class="notice notice-success"><p>Zapisano ustawienia Command Center.</p></div>';
  }
  $an = function_exists('zp_suite_analytics_get') ? zp_suite_analytics_get() : [];
  $cc = zp_suite_cc_get();
  $leads = function_exists('zp_suite_leads_all') ? zp_suite_leads_all() : [];
  $new = function_exists('zp_suite_leads_new_count') ? zp_suite_leads_new_count() : 0;
  $views = 0; $vis=[];
  foreach(($an['days']??[]) as $d){ $views+=(int)($d['views']??0); foreach(($d['visitors']??[]) as $k=>$v){$vis[$k]=1;} }
  $perf=$an['perf']??[]; $samples=max(1,(int)($perf['samples']??0));
  $avg_load=round(($perf['load_sum']??0)/$samples);
  $avg_weight=round(($perf['weight_sum']??0)/$samples);
  $avg_ttfb=round(($perf['ttfb_sum']??0)/$samples);
  $load=function_exists('zp_suite_fmt_ms')?zp_suite_fmt_ms($avg_load):'—';
  $weight=function_exists('zp_suite_fmt_kb')?zp_suite_fmt_kb($avg_weight):'—';
  $speed_score=zp_suite_cc_speed_score($avg_load,$avg_weight,$avg_ttfb);
  $speed_label=$speed_score>=85?'szybka':($speed_score>=65?'średnia':'do poprawy');
  $daily = zp_suite_cc_recent_days($an['days'] ?? [], 7);
  $lead_days = zp_suite_cc_count_leads_by_day($leads);
  $cta_days = zp_suite_cc_count_events_by_day($cc['events'] ?? [], 'cta');
  foreach($daily as &$r){ $r['leads'] = (int)($lead_days[$r['date']] ?? 0); $r['cta'] = (int)($cta_days[$r['date']] ?? 0); $r['score'] = zp_suite_cc_speed_score($r['load'], $r['weight']); }
  unset($r);
  $pages=$an['pages']??[]; uasort($pages, fn($a,$b)=>($b['views']??0)<=>($a['views']??0)); $top=array_slice($pages,0,8,true);
  $cta=$cc['cta']??[]; uasort($cta, fn($a,$b)=>($b['count']??0)<=>($a['count']??0)); $cta=array_slice($cta,0,8,true);
  $sections=$cc['sections']??[]; uasort($sections, fn($a,$b)=>($b['views']??0)<=>($a['views']??0)); $sections=array_slice($sections,0,12,true);
  ?>
  <div class="zpSuiteAdmin">
    <div class="zpSuiteHero"><span class="zpSuiteBadge">Zaprojektowani Suite 1.6.5 • Analytics Pro + Backend Polish</span><h1>Centrum dowodzenia ruchem, leadami, konwersją i jakością strony.</h1><p>Statystyki dzienne, kolejność sekcji, ranking CTA, sekcje home, scroll depth, szybkość strony i checklisty jakości przed budową kolejnych landingów.</p><div class="zpStatus"><div class="zpStat"><strong><?php echo esc_html($new); ?></strong><span>nowe leady</span></div><div class="zpStat"><strong><?php echo esc_html($views); ?></strong><span>odsłony</span></div><div class="zpStat"><strong><?php echo esc_html(count($vis)); ?></strong><span>unikalni</span></div><div class="zpStat"><strong><?php echo esc_html($load); ?></strong><span>średni load</span></div><div class="zpStat"><strong><?php echo esc_html($weight); ?></strong><span>średnia waga</span></div><div class="zpStat"><strong><?php echo esc_html($speed_score); ?>/100</strong><span>speed score — <?php echo esc_html($speed_label); ?></span></div></div></div>

    <?php if(function_exists('zp_suite_render_admin_notifications')) zp_suite_render_admin_notifications(); ?>

    <div class="zpGrid" style="margin-top:18px"><div class="zpCard"><h2>Ostatnie 7 dni — dzień po dniu</h2><div class="zpKpiGrid"><?php foreach($daily as $r): ?><div class="zpKpi"><strong><?php echo esc_html($r['views']); ?></strong><span><?php echo esc_html($r['weekday']); ?><br><?php echo esc_html($r['date']); ?></span></div><?php endforeach; ?></div></div><div class="zpCard"><h2>Home Quality Guard</h2><?php zp_suite_render_home_quality_guard(); ?></div></div>

    <div class="zpGrid"><div class="zpCard"><h2>Wykres odsłon</h2><?php zp_suite_cc_chart_rows($daily, 'views'); ?></div><div class="zpCard"><h2>Wykres leadów</h2><?php zp_suite_cc_chart_rows($daily, 'leads'); ?></div></div>
    <div class="zpGrid"><div class="zpCard"><h2>Kliknięcia CTA dzień po dniu</h2><?php zp_suite_cc_chart_rows($daily, 'cta'); ?></div><div class="zpCard"><h2>Speed score dzień po dniu</h2><?php zp_suite_cc_chart_rows($daily, 'score', '/100'); ?></div></div>

    <div class="zpGrid"><div class="zpCard"><h2>Najpopularniejsze sekcje home</h2><table class="widefat striped"><thead><tr><th>Sekcja</th><th>Widoczność</th><th>Ostatnio</th></tr></thead><tbody><?php foreach($sections as $sct): ?><tr><td><strong><?php echo esc_html($sct['label']??'sekcja'); ?></strong></td><td><?php echo esc_html($sct['views']??0); ?></td><td><?php echo esc_html($sct['last_at']??''); ?></td></tr><?php endforeach; ?></tbody></table></div><div class="zpCard"><h2>Najczęściej klikane CTA</h2><table class="widefat striped"><thead><tr><th>CTA</th><th>Kliknięcia</th></tr></thead><tbody><?php foreach($cta as $c): ?><tr><td><?php echo esc_html($c['label']??'CTA'); ?></td><td><strong><?php echo esc_html($c['count']??0); ?></strong></td></tr><?php endforeach; ?></tbody></table></div></div>

    <div class="zpGrid"><div class="zpCard"><h2>Najpopularniejsze podstrony</h2><table class="widefat striped"><thead><tr><th>URL</th><th>Odsłony</th><th>Unikalni</th><th>Load</th><th>Waga</th></tr></thead><tbody><?php foreach($top as $p): $sm=max(1,(int)($p['samples']??0)); ?><tr><td><code><?php echo esc_html($p['path']??''); ?></code><br><small><?php echo esc_html($p['title']??''); ?></small></td><td><strong><?php echo esc_html($p['views']??0); ?></strong></td><td><?php echo esc_html(count($p['visitors']??[])); ?></td><td><?php echo esc_html(function_exists('zp_suite_fmt_ms')?zp_suite_fmt_ms(round(($p['load_sum']??0)/$sm)):'—'); ?></td><td><?php echo esc_html(function_exists('zp_suite_fmt_kb')?zp_suite_fmt_kb(round(($p['weight_sum']??0)/$sm)):'—'); ?></td></tr><?php endforeach; ?></tbody></table></div><div class="zpCard"><h2>Scroll depth</h2><table class="widefat striped"><thead><tr><th>URL</th><th>25%</th><th>50%</th><th>75%</th><th>100%</th></tr></thead><tbody><?php foreach(array_slice($cc['scroll']??[],0,10,true) as $path=>$s): ?><tr><td><code><?php echo esc_html($path); ?></code></td><td><?php echo esc_html($s['25']??0); ?></td><td><?php echo esc_html($s['50']??0); ?></td><td><?php echo esc_html($s['75']??0); ?></td><td><?php echo esc_html($s['100']??0); ?></td></tr><?php endforeach; ?></tbody></table></div></div>

    <div class="zpGrid"><div class="zpCard"><h2>Exit popup / Safe Mode</h2><form method="post"><?php wp_nonce_field('zp_cc_save'); $ccs = zp_suite_cc_get(); $set = $ccs['settings'] ?? []; ?><label class="zpSwitch"><input type="checkbox" name="exit_popup_enabled" value="1" <?php checked(($set['exit_popup_enabled'] ?? '1'),'1'); ?>> <span>Popup konsultacji przed wyjściem aktywny</span></label><label class="zpSwitch"><input type="checkbox" name="safe_mode" value="1" <?php checked(($set['safe_mode'] ?? '0'),'1'); ?>> <span>Safe Mode — wyłącz ciężkie animacje/blur</span></label><label class="zpField"><strong>Opóźnienie popupu w ms</strong><input type="number" name="exit_popup_delay" value="<?php echo esc_attr($set['exit_popup_delay'] ?? 8500); ?>"></label><p><button class="button button-primary" name="zp_cc_save" value="1">Zapisz ustawienia</button></p></form></div><div class="zpCard"><h2>Activity log</h2><?php foreach(array_slice(array_reverse($cc['events']??[]),0,10) as $e): ?><p class="zpNoticeMini"><strong><?php echo esc_html($e['type']??'event'); ?></strong> — <?php echo esc_html($e['label']??''); ?><br><small><?php echo esc_html(($e['at']??'').' • '.($e['path']??'')); ?></small></p><?php endforeach; ?></div></div>
  </div>
  <?php
}

function zp_suite_render_seo_checklist(){
  $opts=get_option('zp_suite_options',[]); $cms=get_option('zp_suite_cms',[]);
  $checks=[
    'H1 hero ustawiony'=>!empty($opts['hero']['title']),
    'Meta title home'=>!empty($opts['seo']['home_title']),
    'Meta description home'=>!empty($opts['seo']['home_description']),
    'FAQ ma minimum 4 pytania'=>count($cms['faq']??[])>=4,
    'Portfolio ma projekty'=>count($cms['portfolio']['web']??[])+count($cms['portfolio']['logo']??[])>0,
    'Link do stron internetowych Katowice'=>true,
    'Link do sklepów internetowych Katowice'=>true,
    'Link do logo-branding Katowice'=>true,
    'Link do kampanie Meta Ads'=>true,
  ];
  echo '<ul class="zpSeoCheck">'; foreach($checks as $label=>$ok){ echo '<li class="'.($ok?'ok':'bad').'"><span>'.($ok?'✓':'!').'</span>'.esc_html($label).'</li>'; } echo '</ul><style>.zpSeoCheck{display:grid;gap:8px}.zpSeoCheck li{display:flex;gap:9px;align-items:center;padding:10px 12px;border-radius:14px;background:#f8fafc;border:1px solid #e2e8f0;font-weight:800}.zpSeoCheck li span{display:grid;place-items:center;width:22px;height:22px;border-radius:999px}.zpSeoCheck .ok span{background:#dcfce7;color:#166534}.zpSeoCheck .bad span{background:#fee2e2;color:#991b1b}</style>';
}

function zp_suite_render_redirects_page(){
  if (!current_user_can('manage_options')) return; $d=zp_suite_cc_get();
  if (!empty($_POST['zp_redirect_save']) && check_admin_referer('zp_redirect_save')) {
    $rows=[]; foreach(($_POST['redirects']??[]) as $r){ $from=sanitize_text_field(wp_unslash($r['from']??'')); $to=sanitize_text_field(wp_unslash($r['to']??'')); if(!$from||!$to) continue; $rows[]=['from'=>$from,'to'=>$to,'code'=>(int)($r['code']??301),'enabled'=>!empty($r['enabled'])?'1':'0','hits'=>(int)($r['hits']??0),'last_at'=>sanitize_text_field($r['last_at']??'')]; }
    $d['redirects']=$rows; zp_suite_cc_save($d); echo '<div class="zpNotice">Zapisano redirecty.</div>';
  }
  if (empty($d['redirects'])) $d['redirects']=[];
  ?>
  <div class="zpSuiteAdmin"><div class="zpSuiteHero"><span class="zpSuiteBadge">Redirecty + 404</span><h1>Kontrola przekierowań i błędów po przebudowie SEO.</h1><p>Dodaj 301 dla starych URL-i i obserwuj, które adresy wpadają w 404.</p></div><div class="zpCard" style="margin-top:18px"><h2>Redirect manager</h2><form method="post"><?php wp_nonce_field('zp_redirect_save'); ?><table class="widefat striped"><thead><tr><th>Stary URL</th><th>Nowy URL</th><th>Kod</th><th>Aktywny</th><th>Hity</th></tr></thead><tbody><?php foreach($d['redirects'] as $i=>$r): ?><tr><td><input name="redirects[<?php echo $i; ?>][from]" value="<?php echo esc_attr($r['from']??''); ?>"></td><td><input name="redirects[<?php echo $i; ?>][to]" value="<?php echo esc_attr($r['to']??''); ?>"></td><td><input type="number" name="redirects[<?php echo $i; ?>][code]" value="<?php echo esc_attr($r['code']??301); ?>" style="width:80px"></td><td><input type="checkbox" name="redirects[<?php echo $i; ?>][enabled]" value="1" <?php checked(($r['enabled']??'1'),'1'); ?>></td><td><?php echo esc_html($r['hits']??0); ?><input type="hidden" name="redirects[<?php echo $i; ?>][hits]" value="<?php echo esc_attr($r['hits']??0); ?>"><input type="hidden" name="redirects[<?php echo $i; ?>][last_at]" value="<?php echo esc_attr($r['last_at']??''); ?>"></td></tr><?php endforeach; for($j=0;$j<3;$j++): $i=count($d['redirects'])+$j; ?><tr><td><input name="redirects[<?php echo $i; ?>][from]" placeholder="/stary-url/"></td><td><input name="redirects[<?php echo $i; ?>][to]" placeholder="/nowy-url/"></td><td><input type="number" name="redirects[<?php echo $i; ?>][code]" value="301" style="width:80px"></td><td><input type="checkbox" name="redirects[<?php echo $i; ?>][enabled]" value="1" checked></td><td>0</td></tr><?php endfor; ?></tbody></table><p><button class="button button-primary" name="zp_redirect_save" value="1">Zapisz redirecty</button></p></form></div><div class="zpCard"><h2>Ostatnie 404</h2><table class="widefat striped"><thead><tr><th>URL</th><th>Liczba</th><th>Ostatnio</th></tr></thead><tbody><?php foreach(($d['404']??[]) as $path=>$r): ?><tr><td><code><?php echo esc_html($path); ?></code></td><td><?php echo esc_html($r['count']??0); ?></td><td><?php echo esc_html($r['last_at']??''); ?></td></tr><?php endforeach; ?></tbody></table></div></div>
  <?php
}

function zp_suite_render_images_page(){
  if (!current_user_can('manage_options')) return;

  if (!empty($_POST['zp_save_image_alts']) && check_admin_referer('zp_save_image_alts')) {
    $alts = isset($_POST['zp_alt']) && is_array($_POST['zp_alt']) ? $_POST['zp_alt'] : [];
    $saved = 0;
    foreach ($alts as $id => $alt) {
      $id = absint($id);
      if (!$id || get_post_type($id) !== 'attachment') continue;
      update_post_meta($id, '_wp_attachment_image_alt', sanitize_text_field(wp_unslash($alt)));
      $saved++;
    }
    echo '<div class="zpNotice">Zapisano alty SEO dla obrazów: '.esc_html($saved).'.</div>';
  }

  if (!empty($_POST['zp_generate_missing_alts']) && check_admin_referer('zp_generate_missing_alts')) {
    $imgs_all=get_posts(['post_type'=>'attachment','post_mime_type'=>'image','posts_per_page'=>-1,'fields'=>'ids']);
    $generated=0;
    foreach($imgs_all as $id){
      $alt=get_post_meta($id,'_wp_attachment_image_alt',true);
      if (trim((string)$alt) !== '') continue;
      $src=wp_get_attachment_url($id);
      update_post_meta($id,'_wp_attachment_image_alt',zp_suite_seo_alt_from_src($src));
      $generated++;
    }
    echo '<div class="zpNotice">Automatycznie uzupełniono brakujące alty: '.esc_html($generated).'.</div>';
  }

  $imgs=get_posts(['post_type'=>'attachment','post_mime_type'=>'image','posts_per_page'=>80,'orderby'=>'date','order'=>'DESC']);
  ?>
  <div class="zpSuiteAdmin">
    <div class="zpSuiteHero">
      <span class="zpSuiteBadge">Obrazy + ALT SEO</span>
      <h1>Audyt obrazów, edycja ALT i rekomendacje pod SEO.</h1>
      <p>Tu możesz ręcznie ustawić alty dla obrazów z biblioteki mediów. Puste alty na froncie nadal są zabezpieczane automatycznie, ale ręczne opisy są lepsze pod SEO i dostępność.</p>
    </div>

    <div class="zpGrid" style="margin-top:18px">
      <div class="zpCard">
        <h2>Automatyczne ALT SEO</h2>
        <p>Jeżeli obraz nie ma ALT, system może nadać opis na bazie nazwy pliku. Potem warto najważniejsze obrazy poprawić ręcznie.</p>
        <form method="post">
          <?php wp_nonce_field('zp_generate_missing_alts'); ?>
          <button class="button button-primary" name="zp_generate_missing_alts" value="1">Uzupełnij brakujące alty automatycznie</button>
        </form>
      </div>
      <div class="zpCard">
        <h2>Jak pisać alty?</h2>
        <p><strong>Dobrze:</strong> „Projekt strony internetowej dla kancelarii Siemianowski — widok hero na laptopie”.</p>
        <p><strong>Słabiej:</strong> „obrazek”, „logo”, „mockup”.</p>
        <p>Najważniejsze obrazy na home powinny wspierać frazy: strony internetowe Katowice, sklepy WooCommerce, logo i branding, projekty stron dla firm.</p>
      </div>
    </div>

    <div class="zpCard" style="margin-top:18px">
      <h2>Ostatnie obrazy z biblioteki</h2>
      <form method="post">
        <?php wp_nonce_field('zp_save_image_alts'); ?>
        <table class="widefat striped">
          <thead><tr><th>Podgląd</th><th>Plik</th><th>Wymiary</th><th>Waga</th><th>ALT SEO — edycja</th><th>Rekomendacja</th></tr></thead>
          <tbody>
          <?php foreach($imgs as $img):
            $file=get_attached_file($img->ID);
            $size=$file&&file_exists($file)?filesize($file):0;
            $meta=wp_get_attachment_metadata($img->ID);
            $alt=get_post_meta($img->ID,'_wp_attachment_image_alt',true);
            $rec=[]; if($size>700*1024)$rec[]='odchudzić'; if(empty($alt))$rec[]='uzupełnić ALT'; if(!empty($meta['width']) && (int)$meta['width']>2400)$rec[]='za szeroki';
          ?>
            <tr>
              <td><?php echo wp_get_attachment_image($img->ID,[90,58]); ?></td>
              <td><code><?php echo esc_html(basename($file?:'')); ?></code><br><small>ID: <?php echo esc_html($img->ID); ?></small></td>
              <td><?php echo esc_html(($meta['width']??'—').'×'.($meta['height']??'—')); ?></td>
              <td><?php echo esc_html(function_exists('zp_suite_fmt_kb')?zp_suite_fmt_kb(round($size/1024)):'—'); ?></td>
              <td><input style="width:100%;min-width:280px" name="zp_alt[<?php echo esc_attr($img->ID); ?>]" value="<?php echo esc_attr($alt); ?>" placeholder="Opis obrazu pod SEO i dostępność"></td>
              <td><?php echo $rec?'<b style="color:#b45309">'.esc_html(implode(', ',$rec)).'</b>':'<b style="color:#166534">OK</b>'; ?></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
        <p style="margin-top:16px"><button class="button button-primary" name="zp_save_image_alts" value="1">Zapisz alty SEO</button></p>
      </form>
    </div>
  </div>
  <?php
}
