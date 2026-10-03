<?php
if (!defined('ABSPATH')) { exit; }

/**
 * ZAPROJEKTOWANI SUITE v1.6.5
 * Backend polish + subtle sticky estimate bar + richer stats helpers.
 */

function zp_suite_xp_opts(){
  $opts = get_option('zp_suite_options', []);
  return is_array($opts) ? $opts : [];
}

function zp_suite_xp_get($path, $default = ''){
  if (function_exists('zp_suite_opt')) {
    return zp_suite_opt($path, $default);
  }
  $opts = zp_suite_xp_opts();
  $parts = explode('.', $path);
  $cur = $opts;
  foreach ($parts as $p) {
    if (!is_array($cur) || !array_key_exists($p, $cur)) return $default;
    $cur = $cur[$p];
  }
  return $cur;
}

function zp_suite_xp_enabled($key, $default = '1'){
  return (string) zp_suite_xp_get($key, $default) !== '0';
}

add_action('wp_footer', function(){
  if (is_admin()) return;
  if (!zp_suite_xp_enabled('sticky_estimate.enabled', '1')) return;
  // Homepage intentionally has no desktop sticky estimate bar.
  if (is_front_page() || is_home()) return;
  if (!apply_filters('zp_suite_show_sticky_estimate_bar', false)) return;

  $ajax = admin_url('admin-ajax.php');
  $nonce = wp_create_nonce('zp_suite_contact');
  $title = zp_suite_xp_get('sticky_estimate.title', 'Chcesz otrzymać darmową wycenę strony, sklepu, logo lub brandingu?');
  $text = zp_suite_xp_get('sticky_estimate.text', 'Opisz krótko projekt — oddzwonimy albo odpiszemy mailowo z rekomendacją najlepszej ścieżki działania.');
  $cta = zp_suite_xp_get('sticky_estimate.cta', 'Poproś o bezpłatną wycenę');
  $mail = zp_suite_xp_get('sticky_estimate.mail_cta', 'Wolę kontakt mailowy');
  ?>
  <aside class="zpStickyEstimate" id="zpStickyEstimate" aria-label="Bezpłatna wycena Zaprojektowani">
    <div class="zpStickyEstimate__inner">
      <div class="zpStickyEstimate__dot" aria-hidden="true"></div>
      <div class="zpStickyEstimate__copy">
        <strong><?php echo esc_html($title); ?></strong>
        <span><?php echo esc_html($text); ?></span>
      </div>
      <div class="zpStickyEstimate__actions">
        <button type="button" class="zpStickyEstimate__btn" data-zp-estimate-open><i data-lucide="sparkles"></i><span><?php echo esc_html($cta); ?></span></button>
        <a class="zpStickyEstimate__mail" href="mailto:kontakt@zaprojektowani.com?subject=Zapytanie%20o%20wycen%C4%99%20projektu"><i data-lucide="mail"></i><span><?php echo esc_html($mail); ?></span></a>
      </div>
      <button class="zpStickyEstimate__close" type="button" data-zp-estimate-closebar aria-label="Ukryj pasek">×</button>
    </div>
  </aside>

  <div class="zpEstimateModal" id="zpEstimateModal" aria-hidden="true">
    <div class="zpEstimateModal__shade" data-zp-estimate-close></div>
    <section class="zpEstimateModal__panel" role="dialog" aria-modal="true" aria-labelledby="zpEstimateTitle">
      <button class="zpEstimateModal__close" type="button" data-zp-estimate-close aria-label="Zamknij">×</button>
      <div class="zpEstimateModal__top">
        <span class="zpEstimateModal__status"><i></i> jesteśmy online</span>
        <h2 id="zpEstimateTitle">Zostaw kontakt — wrócimy z pierwszą rekomendacją.</h2>
        <p>Możesz wybrać telefon albo odpowiedź mailową. Wystarczy kilka zdań o projekcie: strona, sklep, logo, branding, SEO lub kampania.</p>
      </div>
      <form class="zpEstimateModal__form" data-zp-estimate-form>
        <input type="hidden" name="action" value="zp_suite_contact">
        <input type="hidden" name="nonce" value="<?php echo esc_attr($nonce); ?>">
        <input type="hidden" name="services" value="Bezpłatna wycena / sticky bar">
        <input type="hidden" name="contact_mode" value="form" data-zp-estimate-mode>
        <label><span>Imię / firma</span><input name="name" type="text" placeholder="np. Mateusz / nazwa firmy" required></label>
        <div class="zpEstimateModal__grid">
          <label><span>Telefon</span><input name="phone" type="tel" placeholder="np. 500 600 700"></label>
          <label><span>E-mail</span><input name="email" type="email" placeholder="np. kontakt@firma.pl"></label>
        </div>
        <label><span>Krótko o projekcie</span><textarea name="message" placeholder="Np. potrzebujemy strony dla kancelarii, sklepu WooCommerce albo odświeżenia logo i brandingu." required></textarea></label>
        <div class="zpEstimateModal__choice" role="group" aria-label="Preferowany kontakt">
          <button type="button" class="is-active" data-zp-estimate-contact="form"><i data-lucide="messages-square"></i><span>Telefon lub e-mail</span></button>
          <button type="button" data-zp-estimate-contact="phone"><i data-lucide="phone-call"></i><span>Proszę o telefon</span></button>
          <button type="button" data-zp-estimate-contact="email"><i data-lucide="mail"></i><span>Wolę e-mail</span></button>
        </div>
        <label class="zpEstimateModal__consent"><input type="checkbox" name="consent" value="1" required checked> <span>Wyrażam zgodę na kontakt w sprawie zapytania.</span></label>
        <button class="zpEstimateModal__submit" type="submit">Wyślij prośbę o wycenę</button>
        <div class="zpEstimateModal__msg" aria-live="polite"></div>
      </form>
    </section>
  </div>
  <style id="zp-sticky-estimate-css">.zpStickyEstimate,.zpStickyEstimate *,.zpEstimateModal,.zpEstimateModal *{box-sizing:border-box}.zpStickyEstimate{position:fixed;left:50%;bottom:18px;z-index:999930;transform:translateX(-50%);width:min(1120px,calc(100% - 28px));font-family:var(--zp-font,"Plus Jakarta Sans Local",system-ui,sans-serif);color:#071426}.zpStickyEstimate__inner{position:relative;display:grid;grid-template-columns:auto minmax(0,1fr) auto auto;gap:14px;align-items:center;padding:12px 12px 12px 17px;border:1px solid rgba(7,20,38,.10);border-radius:999px;background:rgba(255,255,255,.88);box-shadow:0 18px 54px rgba(7,20,38,.13),inset 0 1px 0 rgba(255,255,255,.86);backdrop-filter: none;-webkit-backdrop-filter:none saturate(130%)}.zpStickyEstimate__dot{width:10px;height:10px;border-radius:999px;background:#22c55e;box-shadow:0 0 0 6px rgba(34,197,94,.12)}.zpStickyEstimate__copy{min-width:0;display:flex;flex-direction:column;gap:3px}.zpStickyEstimate__copy strong{display:block;font-size:14px;line-height:1.12;letter-spacing:-.025em;color:#071426}.zpStickyEstimate__copy span{display:block;font-size:12px;line-height:1.35;color:#647084;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.zpStickyEstimate__actions{display:flex;align-items:center;gap:8px}.zpStickyEstimate__btn{min-height:42px;border:0;border-radius:999px;padding:0 18px;background:linear-gradient(90deg,#05070b,#0b1830 42%,#102a4f 72%,#1c477a);color:#fff!important;font-size:13px;font-weight:850;letter-spacing:-.012em;cursor:pointer;box-shadow:0 10px 24px rgba(16,42,79,.18)}.zpStickyEstimate__mail{min-height:42px;display:inline-flex;align-items:center;justify-content:center;border-radius:999px;padding:0 14px;background:#f3f6fa;color:#071426!important;border:1px solid rgba(7,20,38,.08);font-size:12px;font-weight:800;text-decoration:none!important;white-space:nowrap}.zpStickyEstimate__close{width:34px;height:34px;border:0;border-radius:999px;background:#eef2f7;color:#071426;font-size:20px;line-height:1;cursor:pointer}.zpStickyEstimate.is-hidden{display:none}.zpEstimateModal{position:fixed;inset:0;z-index:999940;display:none;font-family:var(--zp-font,"Plus Jakarta Sans Local",system-ui,sans-serif)}.zpEstimateModal.is-open{display:block}.zpEstimateModal__shade{position:absolute;inset:0;background:rgba(3,5,9,.48);backdrop-filter: none;-webkit-backdrop-filter:none}.zpEstimateModal__panel{position:absolute;left:50%;top:50%;transform:translate(-50%,-50%);width:min(640px,calc(100% - 28px));max-height:calc(100vh - 34px);overflow:auto;border-radius:30px;background:#fff;color:#071426;border:1px solid rgba(255,255,255,.18);box-shadow:0 34px 100px rgba(3,7,13,.38)}.zpEstimateModal__panel:before{content:"";position:absolute;inset:0 0 auto;height:6px;background:linear-gradient(90deg,#05070b,#102a4f,#1c477a)}.zpEstimateModal__close{position:absolute;right:16px;top:16px;z-index:2;width:36px;height:36px;border:0;border-radius:999px;background:#eef2f7;color:#071426;font-size:22px;cursor:pointer}.zpEstimateModal__top{padding:32px 34px 18px;background:radial-gradient(circle at 92% 0%,rgba(28,71,122,.12),transparent 34%),linear-gradient(180deg,#fff,#f8fafc)}.zpEstimateModal__status{display:inline-flex;align-items:center;gap:8px;margin-bottom:14px;text-transform:uppercase;letter-spacing:.13em;font-size:10px;font-weight:850;color:#1c477a}.zpEstimateModal__status i{width:8px;height:8px;border-radius:999px;background:#22c55e;box-shadow:0 0 0 5px rgba(34,197,94,.12)}.zpEstimateModal h2{max-width:520px;margin:0;font-size:clamp(30px,5vw,48px);line-height:.96;letter-spacing:-.06em;color:#071426}.zpEstimateModal p{max-width:540px;margin:14px 0 0;color:#647084;font-size:14px;line-height:1.58}.zpEstimateModal__form{padding:22px 34px 34px;display:grid;gap:13px}.zpEstimateModal label span{display:block;margin:0 0 7px;font-size:10px;text-transform:uppercase;letter-spacing:.13em;font-weight:850;color:#647084}.zpEstimateModal input,.zpEstimateModal textarea{width:100%;border:1px solid #dfe5ee!important;background:#fff!important;border-radius:18px!important;padding:13px 15px!important;color:#071426!important;outline:none!important;box-shadow:none!important;font-weight:650}.zpEstimateModal textarea{min-height:96px;resize:vertical}.zpEstimateModal input:focus,.zpEstimateModal textarea:focus{border-color:#102a4f!important;box-shadow:0 0 0 4px rgba(16,42,79,.10)!important}.zpEstimateModal__grid{display:grid;grid-template-columns:1fr 1fr;gap:12px}.zpEstimateModal__choice{display:flex;flex-wrap:wrap;gap:8px}.zpEstimateModal__choice button{border:1px solid #dfe5ee;border-radius:999px;background:#f7fafc;color:#071426;padding:10px 13px;font-weight:850;font-size:12px;cursor:pointer}.zpEstimateModal__choice button.is-active{background:#071426;color:#fff;border-color:#071426}.zpEstimateModal__consent{display:flex!important;gap:10px;align-items:flex-start;padding:12px 14px;border-radius:16px;background:#f8fafc;border:1px solid #e5ebf3}.zpEstimateModal__consent input{width:auto!important;margin-top:1px}.zpEstimateModal__consent span{margin:0!important;text-transform:none!important;letter-spacing:0!important;font-size:12px!important;line-height:1.45;color:#647084!important}.zpEstimateModal__submit{min-height:54px;border:0;border-radius:999px;background:linear-gradient(90deg,#05070b,#0b1830 42%,#102a4f 72%,#1c477a);color:#fff;font-weight:900;cursor:pointer;box-shadow:0 12px 28px rgba(16,42,79,.18)}.zpEstimateModal__msg{display:none;border-radius:16px;padding:13px 15px;font-size:13px;font-weight:800}.zpEstimateModal__msg.is-ok{display:block;background:#ecfdf5;color:#166534}.zpEstimateModal__msg.is-bad{display:block;background:#fff7ed;color:#9a3412}@media(max-width:760px){.zpStickyEstimate{bottom:calc(86px + env(safe-area-inset-bottom));width:calc(100% - 18px)}.zpStickyEstimate__inner{grid-template-columns:auto 1fr auto;border-radius:24px;padding:13px}.zpStickyEstimate__copy span{display:none}.zpStickyEstimate__actions{grid-column:1/-1;width:100%;display:grid;grid-template-columns:1fr 1fr}.zpStickyEstimate__btn,.zpStickyEstimate__mail{width:100%}.zpEstimateModal__top,.zpEstimateModal__form{padding-left:22px;padding-right:22px}.zpEstimateModal__grid{grid-template-columns:1fr}}</style>
  <script id="zp-sticky-estimate-js">
  (function(){
    var bar=document.getElementById('zpStickyEstimate'), modal=document.getElementById('zpEstimateModal'); if(!bar||!modal)return;
    var ajax='<?php echo esc_js($ajax); ?>';
    function open(){modal.classList.add('is-open');modal.setAttribute('aria-hidden','false');setTimeout(function(){var i=modal.querySelector('input[name="name"]'); if(i)i.focus();},90)}
    function close(){modal.classList.remove('is-open');modal.setAttribute('aria-hidden','true')}
    document.addEventListener('click',function(e){if(e.target.closest('[data-zp-estimate-open]')){open()} if(e.target.closest('[data-zp-estimate-close]')){close()} if(e.target.closest('[data-zp-estimate-closebar]')){bar.classList.add('is-hidden');try{sessionStorage.setItem('zpStickyEstimateHidden','1')}catch(err){}} var ch=e.target.closest('[data-zp-estimate-contact]'); if(ch){var mode=ch.getAttribute('data-zp-estimate-contact'); modal.querySelectorAll('[data-zp-estimate-contact]').forEach(function(b){b.classList.remove('is-active')}); ch.classList.add('is-active'); var input=modal.querySelector('[data-zp-estimate-mode]'); if(input) input.value=mode; }});
    try{ if(sessionStorage.getItem('zpStickyEstimateHidden')==='1') bar.classList.add('is-hidden'); }catch(e){}
    var form=modal.querySelector('[data-zp-estimate-form]'), msg=modal.querySelector('.zpEstimateModal__msg');
    if(form){form.addEventListener('submit',function(e){e.preventDefault(); var btn=form.querySelector('button[type="submit"]'); if(btn)btn.disabled=true; msg.className='zpEstimateModal__msg'; msg.textContent=''; (window.zpSuiteSecureFormData?window.zpSuiteSecureFormData(new FormData(form)):Promise.resolve(new FormData(form))).then(function(secureFd){return fetch(ajax,{method:'POST',body:secureFd,credentials:'same-origin'});}).then(function(r){return r.json()}).then(function(j){ if(j&&j.success){msg.className='zpEstimateModal__msg is-ok'; msg.textContent=(j.data&&j.data.message)||'Dziękujemy — odezwiemy się w sprawie wyceny.'; form.reset(); setTimeout(close,1800);} else {var arr=(j&&j.data&&j.data.messages)||['Sprawdź pola i spróbuj ponownie.']; msg.className='zpEstimateModal__msg is-bad'; msg.textContent=arr.join(' ');} }).catch(function(){msg.className='zpEstimateModal__msg is-bad';msg.textContent='Nie udało się wysłać. Spróbuj ponownie.'}).finally(function(){if(btn)btn.disabled=false;});});}
  })();
  </script>
  <?php
}, 121);

function zp_suite_render_admin_notifications(){
  if (!current_user_can('manage_options')) return;
  $an = function_exists('zp_suite_analytics_get') ? zp_suite_analytics_get() : [];
  $cc = function_exists('zp_suite_cc_get') ? zp_suite_cc_get() : [];
  $leads_new = function_exists('zp_suite_leads_new_count') ? (int) zp_suite_leads_new_count() : 0;
  $perf = $an['perf'] ?? [];
  $samples = max(1, (int)($perf['samples'] ?? 0));
  $avg_load = round(($perf['load_sum'] ?? 0)/$samples);
  $avg_weight = round(($perf['weight_sum'] ?? 0)/$samples);
  $score = function_exists('zp_suite_cc_speed_score') ? zp_suite_cc_speed_score($avg_load, $avg_weight, round(($perf['ttfb_sum'] ?? 0)/$samples)) : 0;
  $notices = [];
  if ($leads_new > 0) $notices[] = ['Nowe leady', 'Masz '.$leads_new.' nowych zgłoszeń do obsłużenia.', 'bad'];
  if ($score && $score < 70) $notices[] = ['Szybkość strony', 'Speed score spadł do '.$score.'/100 — sprawdź ciężkie obrazy i cache.', 'warn'];
  if (!empty($cc['404'])) $notices[] = ['Monitor 404', 'Wykryto '.count($cc['404']).' adresów 404. Warto dodać przekierowania.', 'warn'];
  $imgs = get_posts(['post_type'=>'attachment','post_mime_type'=>'image','posts_per_page'=>20,'fields'=>'ids']);
  $missing = 0; foreach($imgs as $id){ if(trim((string)get_post_meta($id,'_wp_attachment_image_alt',true))==='') $missing++; }
  if ($missing) $notices[] = ['ALT obrazów', $missing.' z ostatnich obrazów nie ma opisu ALT.', 'warn'];
  if (!$notices) $notices[] = ['System gotowy', 'Nie widzę krytycznych alertów. CMS, leady i tracking są aktywne.', 'ok'];
  echo '<div class="zpCard zpAdminAlerts"><h2>Alerty i rekomendacje</h2><div class="zpAdminAlerts__grid">';
  foreach($notices as $n){ echo '<div class="zpAdminAlert '.esc_attr($n[2]).'"><strong>'.esc_html($n[0]).'</strong><span>'.esc_html($n[1]).'</span></div>'; }
  echo '</div></div>';
}

function zp_suite_render_stats_embed(){
  if (!current_user_can('manage_options')) return;
  $an = function_exists('zp_suite_analytics_get') ? zp_suite_analytics_get() : [];
  $cc = function_exists('zp_suite_cc_get') ? zp_suite_cc_get() : [];
  $leads = function_exists('zp_suite_leads_all') ? zp_suite_leads_all() : [];
  if (!function_exists('zp_suite_cc_recent_days')) return;

  $daily = zp_suite_cc_recent_days($an['days'] ?? [], 7);
  $lead_days = function_exists('zp_suite_cc_count_leads_by_day') ? zp_suite_cc_count_leads_by_day($leads) : [];
  $cta_days = function_exists('zp_suite_cc_count_events_by_day') ? zp_suite_cc_count_events_by_day($cc['events'] ?? [], 'cta') : [];
  foreach($daily as &$r){
    $r['leads']=(int)($lead_days[$r['date']] ?? 0);
    $r['cta']=(int)($cta_days[$r['date']] ?? 0);
    $r['score']=function_exists('zp_suite_cc_speed_score')?zp_suite_cc_speed_score($r['load'],$r['weight']):0;
  }
  unset($r);

  $perf=$an['perf']??[];
  $samples=max(1,(int)($perf['samples']??0));
  $avg_load=round(($perf['load_sum']??0)/$samples);
  $avg_weight=round(($perf['weight_sum']??0)/$samples);
  $avg_ttfb=round(($perf['ttfb_sum']??0)/$samples);
  $score=function_exists('zp_suite_cc_speed_score')?zp_suite_cc_speed_score($avg_load,$avg_weight,$avg_ttfb):0;
  $speed_label=$score>=85?'szybka':($score>=65?'średnia':'do poprawy');

  $views=0; $vis=[];
  foreach(($an['days']??[]) as $d){
    $views+=(int)($d['views']??0);
    foreach(($d['visitors']??[]) as $k=>$v){$vis[$k]=1;}
  }
  $new = function_exists('zp_suite_leads_new_count') ? zp_suite_leads_new_count() : 0;

  echo '<div class="zpStatsPro">';
  echo '<div class="zpStatsPro__hero"><div><span>Statystyki i szybkość</span><h2>Centrum danych strony głównej</h2><p>Te same dane, które widzisz w Command Center: ruch, leady, CTA, dni tygodnia, speed score, waga strony i jakość ładowania.</p></div><strong>'.esc_html($score).'<small>/100</small></strong></div>';

  echo '<div class="zpKpiGrid zpStatsKpis">';
  $kpis = [
    ['Odsłony', $views],
    ['Unikalni użytkownicy', count($vis)],
    ['Nowe leady', $new],
    ['Średni load', function_exists('zp_suite_fmt_ms')?zp_suite_fmt_ms($avg_load):$avg_load.' ms'],
    ['Średnia waga', function_exists('zp_suite_fmt_kb')?zp_suite_fmt_kb($avg_weight):$avg_weight.' KB'],
    ['Speed score', $score.'/100 — '.$speed_label],
  ];
  foreach($kpis as $k){ echo '<div class="zpKpi"><strong>'.esc_html($k[1]).'</strong><span>'.esc_html($k[0]).'</span></div>'; }
  echo '</div>';

  echo '<div class="zpCard"><h2>Tydzień dzień po dniu</h2><div class="zpWeekGrid">';
  foreach($daily as $r){
    echo '<div class="zpWeekDay"><b>'.esc_html($r['weekday']).'</b><small>'.esc_html($r['date']).'</small><strong>'.esc_html($r['views']).'</strong><span>odsłony</span><em>'.esc_html($r['unique']).' unikalnych • '.esc_html($r['leads']).' leadów • '.esc_html($r['cta']).' CTA</em></div>';
  }
  echo '</div></div>';

  echo '<div class="zpMiniCharts zpStatsCharts">';
  if(function_exists('zp_suite_cc_chart_rows')){
    echo '<div class="zpCard"><h2>Odsłony</h2>'; zp_suite_cc_chart_rows($daily,'views'); echo '</div>';
    echo '<div class="zpCard"><h2>CTA</h2>'; zp_suite_cc_chart_rows($daily,'cta'); echo '</div>';
    echo '<div class="zpCard"><h2>Leady</h2>'; zp_suite_cc_chart_rows($daily,'leads'); echo '</div>';
    echo '<div class="zpCard"><h2>Speed score</h2>'; zp_suite_cc_chart_rows($daily,'score','/100'); echo '</div>';
  }
  echo '</div>';

  $pages=$an['pages']??[]; uasort($pages, fn($a,$b)=>($b['views']??0)<=>($a['views']??0)); $top=array_slice($pages,0,8,true);
  echo '<div class="zpCard"><h2>Najczęściej odwiedzane podstrony</h2><table class="widefat striped"><thead><tr><th>URL</th><th>Odsłony</th><th>Unikalni</th><th>Średni load</th><th>Waga</th></tr></thead><tbody>';
  foreach($top as $path=>$r){
    $samples=max(1,(int)($r['samples']??1));
    $load=round(($r['load_sum']??0)/$samples);
    $weight=round(($r['weight_sum']??0)/$samples);
    echo '<tr><td><code>'.esc_html($path).'</code></td><td>'.esc_html($r['views']??0).'</td><td>'.esc_html(count($r['visitors']??[])).'</td><td>'.esc_html(function_exists('zp_suite_fmt_ms')?zp_suite_fmt_ms($load):$load.' ms').'</td><td>'.esc_html(function_exists('zp_suite_fmt_kb')?zp_suite_fmt_kb($weight):$weight.' KB').'</td></tr>';
  }
  if(!$top){ echo '<tr><td colspan="5">Brak danych — odwiedź stronę kilka razy w trybie incognito.</td></tr>'; }
  echo '</tbody></table></div>';
  echo '</div>';
}

add_action('admin_head', function(){
  $screen = function_exists('get_current_screen') ? get_current_screen() : null;
  $id = $screen ? $screen->id : '';
  if (strpos($id, 'zp-suite') === false) return;
  ?>
  <style id="zp-suite-admin-polish-165">.zpSuiteAdmin{max-width:1540px}.zpSuiteAdmin .button-primary{background:#071426!important;border-color:#071426!important}.zpSuiteAdmin .button-primary:hover{background:#102a4f!important;border-color:#102a4f!important}.zpSuiteHero{border:1px solid rgba(255,255,255,.12)}.zpCard{box-shadow:0 18px 58px rgba(7,20,38,.055)!important}.zpTabs{gap:9px}.zpTabs button{transition:background .18s ease,color .18s ease,transform .18s ease}.zpTabs button:hover{transform:translateY(-1px)}.zpAdminAlerts{margin-top:18px}.zpAdminAlerts__grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px}.zpAdminAlert{border:1px solid #e1e7f0;border-radius:18px;padding:15px;background:#f8fafc}.zpAdminAlert strong{display:block;font-size:14px;color:#071426;margin-bottom:6px}.zpAdminAlert span{display:block;color:#657084;font-size:12px;line-height:1.45}.zpAdminAlert.ok{background:#ecfdf5;border-color:#bbf7d0}.zpAdminAlert.warn{background:#fff7ed;border-color:#fed7aa}.zpAdminAlert.bad{background:#fef2f2;border-color:#fecaca}.zpKpi small{display:block;margin-top:7px;color:#6d7688;font-size:11px;line-height:1.3}.zpSpeedBox{display:flex;align-items:center;justify-content:space-between;gap:18px;border:1px solid #dfe6ef;background:linear-gradient(135deg,#071426,#102a4f);color:#fff;border-radius:22px;padding:20px;margin-bottom:16px}.zpSpeedBox strong{font-size:42px;letter-spacing:-.05em}.zpSpeedBox span{color:rgba(255,255,255,.72)}.zpMiniCharts{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px}.zpMiniCharts h3{margin:0 0 10px;font-size:13px;text-transform:uppercase;letter-spacing:.1em;color:#536071}@media(max-width:1100px){.zpAdminAlerts__grid,.zpMiniCharts{grid-template-columns:1fr}.zpGrid{grid-template-columns:1fr!important}}</style>
  <?php
}, 99);
