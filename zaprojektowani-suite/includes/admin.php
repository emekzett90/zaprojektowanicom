<?php
if (!defined('ABSPATH')) { exit; }

add_action('admin_menu', function () {
  $new = function_exists('zp_suite_leads_new_count') ? (int) zp_suite_leads_new_count() : 0;
  $badge = $new > 0 ? ' <span class="awaiting-mod"><span class="pending-count">'.esc_html($new).'</span></span>' : '';
  add_menu_page('Zaprojektowani Suite','ZP Suite'.$badge,'manage_options','zp-suite','zp_suite_render_command_center_page','dashicons-art',58);
  add_submenu_page('zp-suite','Command Center','Command Center','manage_options','zp-suite','zp_suite_render_command_center_page');
  add_submenu_page('zp-suite','Strona główna CMS','Strona główna CMS','manage_options','zp-suite-home-cms','zp_suite_render_admin_page');
  add_submenu_page('zp-suite','Formularze / leady','Formularze / leady'.$badge,'manage_options','zp-suite-leads','zp_suite_render_leads_page');
});

add_action('admin_enqueue_scripts', function($hook){
  if (strpos($hook, 'zp-suite') === false) return;
  wp_enqueue_media();
  wp_enqueue_script('jquery-ui-sortable');
  wp_add_inline_script('jquery', zp_suite_admin_js());
});


add_action('admin_init', function(){
  if (!current_user_can('manage_options')) return;
  if (!empty($_GET['page']) && $_GET['page']==='zp-suite-leads' && !empty($_GET['zp_leads_csv']) && check_admin_referer('zp_suite_leads_csv')) {
    $leads = function_exists('zp_suite_leads_all') ? zp_suite_leads_all() : [];
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=zp-suite-leads-'.gmdate('Ymd-His').'.csv');
    $out = fopen('php://output','w');
    fputcsv($out, ['data','status','imie','telefon','email','uslugi','zrodlo','wiadomosc']);
    foreach($leads as $l){ fputcsv($out, [$l['created_at']??'', $l['status']??'', $l['name']??'', $l['phone']??'', $l['email']??'', $l['services']??'', $l['source']??'', $l['message']??'']); }
    fclose($out); exit;
  }
});

add_action('admin_head', function(){
  $screen = function_exists('get_current_screen') ? get_current_screen() : null;
  $id = $screen ? $screen->id : '';
  if (strpos($id, 'zp-suite') === false) return;
  echo '<style id="zp-suite-admin-css">'.zp_suite_admin_css().'</style>';
});

function zp_suite_admin_css(){ return <<<'CSS'
.zpSuiteAdmin{--bg:#05070b;--panel:#071426;--panel2:#0b1830;--blue:#1c477a;--line:rgba(255,255,255,.13);--text:#f7f9ff;--muted:#aab4c4;--pink:#1c477a;font-family:Inter,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;margin:20px 20px 0 0;color:#06101e}.zpSuiteHero{position:relative;overflow:hidden;border-radius:28px;background:radial-gradient(circle at 10% 0%,rgba(28,71,122,.44),transparent 34%),radial-gradient(circle at 90% 10%,rgba(28,71,122,.16),transparent 30%),linear-gradient(135deg,#05070b,#071426 58%,#102a4f);color:#fff;padding:34px 38px;box-shadow:0 26px 70px rgba(7,20,38,.18)}.zpSuiteHero h1{font-size:34px;line-height:1.02;letter-spacing:-.045em;margin:10px 0 10px;max-width:900px}.zpSuiteHero p{font-size:14px;line-height:1.6;color:rgba(255,255,255,.74);max-width:880px}.zpSuiteBadge{display:inline-flex;padding:8px 12px;border:1px solid rgba(255,255,255,.15);border-radius:999px;text-transform:uppercase;letter-spacing:.14em;font-size:10px;font-weight:800;color:rgba(255,255,255,.78)}.zpStatus{display:flex;gap:12px;flex-wrap:wrap;margin-top:22px}.zpStat{min-width:150px;border:1px solid rgba(255,255,255,.12);background:rgba(255,255,255,.06);border-radius:18px;padding:14px}.zpStat strong{display:block;font-size:20px}.zpStat span{display:block;font-size:11px;text-transform:uppercase;letter-spacing:.12em;color:rgba(255,255,255,.58);margin-top:4px}.zpTabs{position:sticky;top:32px;z-index:20;display:flex;gap:8px;overflow:auto;padding:14px 0 12px;background:#f0f0f1}.zpTabs button{border:1px solid #d8dde6;background:#fff;border-radius:999px;padding:10px 16px;cursor:pointer;font-weight:800;color:#071426}.zpTabs button.is-active{background:#071426;color:#fff;border-color:#071426}.zpPanel{display:none}.zpPanel.is-active{display:block}.zpGrid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px}.zpGrid.zpGrid1{grid-template-columns:1fr}.zpCard{background:#fff;border:1px solid #dfe4ec;border-radius:24px;padding:24px;margin-bottom:18px;box-shadow:0 14px 44px rgba(7,20,38,.045)}.zpCard h2{margin:0 0 14px;font-size:21px;letter-spacing:-.03em}.zpCard p{color:#5a6574}.zpField{display:block;margin:0 0 14px}.zpField strong{display:block;margin:0 0 7px;font-size:12px;text-transform:uppercase;letter-spacing:.08em;color:#455165}.zpField input,.zpField textarea,.zpField select{width:100%;max-width:100%;border-radius:12px;border:1px solid #d9e0eb;padding:10px 12px;background:#f8fafc}.zpField textarea{min-height:92px;font-family:ui-monospace,SFMono-Regular,Menlo,monospace;font-size:12px}.zpField small{display:block;margin-top:6px;color:#7a8594}.zpMediaRow{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:8px}.zpRepeater{display:grid;gap:12px}.zpRepeater .ui-sortable-placeholder{visibility:visible!important;min-height:74px;border:2px dashed #1c477a;border-radius:18px;background:#eef6ff}.zpDragHandle{cursor:grab;display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:999px;background:#e9f0f8;color:#102a4f;font-weight:900}.zpSwitchGrid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px}.zpSwitch{display:flex;align-items:center;gap:8px;border:1px solid #dfe4ec;border-radius:14px;background:#f8fafc;padding:11px 12px;font-weight:800}.zpSwitch input{width:auto!important}.zpToolsBar{display:flex;gap:8px;flex-wrap:wrap;margin:12px 0 0}.zpRepeater{display:grid;gap:12px}.zpRepeatItem{position:relative;border:1px solid #dfe4ec;border-radius:22px;background:linear-gradient(180deg,#fff,#f8fafc);padding:18px}.zpRepeatItem details{display:block}.zpRepeatItem summary{list-style:none;cursor:pointer;display:flex;align-items:center;justify-content:space-between;gap:12px;margin:-18px -18px 14px;padding:16px 18px;border-radius:22px 22px 0 0;background:#f8fafc;border-bottom:1px solid #e6ebf3}.zpRepeatItem summary::-webkit-details-marker{display:none}.zpRepeatItem:not(:has(details[open])){padding-bottom:0}.zpRepeatSummaryTitle{display:flex;align-items:center;gap:12px;min-width:0}.zpRepeatThumb{width:54px;height:38px;border-radius:9px;object-fit:cover;background:#edf2f7;border:1px solid #dfe4ec}.zpRepeatSummaryText{min-width:0}.zpRepeatSummaryText strong{display:block;font-size:14px;color:#071426;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.zpRepeatSummaryText span{display:block;font-size:11px;color:#6d7688;margin-top:3px}.zpRepeatChevron{font-size:18px;color:#102a4f;transition:transform .18s ease}.zpRepeatItem details[open] .zpRepeatChevron{transform:rotate(90deg)}.zpRepeatTop{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:12px}.zpRepeatTop strong{font-size:14px;text-transform:uppercase;letter-spacing:.08em;color:#071426}.zpRepeatGrid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px}.zpRepeatGrid .wide{grid-column:1/-1}.zpRemoveRow{border:1px solid #b9cbe3!important;color:#102a4f!important;background:#f4f8fd!important}.zpAddRow{border-radius:999px!important;background:#071426!important;color:#fff!important;border-color:#071426!important}.zpSave{position:sticky;bottom:0;background:rgba(240,240,241,.92);backdrop-filter: none !important;padding:14px 0;z-index:30}.zpSave .button{min-height:46px;border-radius:999px;padding:0 26px;font-weight:800}.zpNotice{padding:14px 18px;border-radius:16px;background:#e8fff2;border:1px solid #a7efc5;margin:18px 0;color:#14532d;font-weight:700}.zpPreviewImg{max-width:120px;max-height:70px;border-radius:10px;display:block;margin-top:8px;object-fit:contain;background:#f3f6fb;border:1px solid #e2e8f0}.zpHelpBox{border-radius:18px;background:#071426;color:#fff;padding:18px}.zpHelpBox code{background:rgba(255,255,255,.1);color:#fff}.zpLeadItem{border:1px solid #dfe4ec;border-radius:22px;background:#fff;padding:18px;margin:0 0 14px}.zpLeadItem.is-new{border-color:#ef4444;background:linear-gradient(180deg,#fff,#fff7f7)}.zpLeadHead{display:flex;align-items:center;justify-content:space-between;gap:14px}.zpLeadHead h3{margin:4px 0 0;font-size:19px}.zpLeadHead span{font-size:11px;color:#6b7280;text-transform:uppercase;letter-spacing:.1em}.zpLeadBadge{background:#ef4444;color:#fff;border-radius:999px;padding:7px 10px;font-size:10px;letter-spacing:.12em}.zpLeadMeta{display:flex;gap:8px;flex-wrap:wrap;margin:12px 0}.zpLeadMeta span{border:1px solid #e2e8f0;background:#f8fafc;border-radius:999px;padding:8px 10px}.zpLeadActions{display:flex;gap:8px;flex-wrap:wrap;margin-top:14px}.zpLeadNote textarea{min-height:72px}.zpLeadFilters{display:flex;gap:8px;flex-wrap:wrap;align-items:flex-end;margin:12px 0 20px;padding:14px;border:1px solid #e2e8f0;border-radius:18px;background:#f8fafc}.zpLeadFilters label{font-weight:800;color:#071426}.zpLeadFilters input,.zpLeadFilters select{border-radius:10px;border:1px solid #d9e0eb;min-height:38px}
.zpOrderList{display:grid;gap:9px;margin:12px 0}.zpOrderItem{display:grid;grid-template-columns:34px 1fr auto;gap:10px;align-items:center;padding:12px 14px;border:1px solid #dfe4ec;border-radius:16px;background:#f8fafc}.zpOrderItem b{color:#071426}.zpOrderItem small{color:#647084}.zpOrderItem .zpDragHandle{cursor:grab}.zpChartBars{display:grid;gap:8px}.zpChartRow{display:grid;grid-template-columns:92px 1fr 70px;align-items:center;gap:10px}.zpChartTrack{height:12px;border-radius:999px;background:#e8eef6;overflow:hidden}.zpChartFill{height:100%;border-radius:999px;background:linear-gradient(90deg,#071426,#102a4f,#1c477a)}.zpKpiGrid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px}.zpKpi{border:1px solid #e2e8f0;background:#f8fafc;border-radius:18px;padding:14px}.zpKpi strong{display:block;font-size:22px;color:#071426}.zpKpi span{display:block;margin-top:4px;font-size:10px;text-transform:uppercase;letter-spacing:.12em;color:#647084;font-weight:850}.zpNoticeMini{padding:12px 14px;border-radius:14px;background:#f8fafc;border:1px solid #e2e8f0;margin:8px 0;color:#475569}.zpNoticeMini.bad{background:#fff7ed;border-color:#fed7aa;color:#9a3412}.zpNoticeMini.ok{background:#f0fdf4;border-color:#bbf7d0;color:#166534}@media(max-width:1100px){.zpKpiGrid{grid-template-columns:1fr 1fr}.zpChartRow{grid-template-columns:72px 1fr 54px}}
@media(max-width:1100px){.zpGrid,.zpRepeatGrid{grid-template-columns:1fr}.zpTabs{top:0}.zpSuiteHero{padding:26px}.zpSuiteHero h1{font-size:28px}}
CSS; }

function zp_suite_admin_js(){ return <<<'JS'
jQuery(function($){
  function openMedia(target){ var frame=wp.media({title:'Wybierz plik',multiple:false}); frame.on('select',function(){ var file=frame.state().get('selection').first().toJSON(); $(target).val(file.url).trigger('change'); }); frame.open(); }
  $(document).on('click','.zpPickMedia',function(e){ e.preventDefault(); openMedia($(this).data('target')); });
  $(document).on('click','.zpTabs button',function(){ var id=$(this).data('tab'); $('.zpTabs button').removeClass('is-active'); $(this).addClass('is-active'); $('.zpPanel').removeClass('is-active'); $('#'+id).addClass('is-active'); window.location.hash=id; });
  if(window.location.hash && $(window.location.hash).length){ $('.zpTabs button[data-tab="'+window.location.hash.substring(1)+'"]').trigger('click'); }
  $(document).on('click','.zpAddRow',function(e){ e.preventDefault(); var rep=$($(this).data('repeater')); var tpl=$($(this).data('template')).html(); var index=Date.now()+''+Math.floor(Math.random()*999); tpl=tpl.replaceAll('__i__', index); rep.append(tpl); });
  $(document).on('click','.zpRemoveRow',function(e){ e.preventDefault(); if(confirm('Usunąć ten element?')) $(this).closest('.zpRepeatItem').remove(); });
  $('.zpRepeater').sortable({handle:'.zpDragHandle, summary', placeholder:'ui-sortable-placeholder', forcePlaceholderSize:true, tolerance:'pointer'});
  $(document).on('click','.zpExpandAll',function(e){e.preventDefault(); $($(this).data('target')).find('details').attr('open',true);});
  $(document).on('click','.zpCollapseAll',function(e){e.preventDefault(); $($(this).data('target')).find('details').removeAttr('open');});
  $(document).on('input change','.zpMediaInput',function(){ var v=$(this).val(); var img=$(this).closest('.zpField').find('.zpPreviewImg'); if(v){ if(!img.length) $(this).closest('.zpField').append('<img class="zpPreviewImg" alt="">'); $(this).closest('.zpField').find('.zpPreviewImg').attr('src',v); } });
});
JS; }

function zp_suite_save_admin(){
  if (empty($_POST['zp_suite_save']) || !current_user_can('manage_options')) return;
  check_admin_referer('zp_suite_save');
  $opts = zp_suite_options();
  $visibility_keys = ['trust_logos','services_path','showcase_portfolio','laptop_showcase','about_experience','showcase_services','reviews_section','seo_faq','seo_industries','home_audit_cta','contact_system'];
  foreach ($visibility_keys as $vk) { $opts['visibility'][$vk] = '0'; }
  $security_switches = ['turnstile_enabled','turnstile_fail_open','honeypot_enabled','min_seconds_enabled'];
  foreach ($security_switches as $sk) { $opts['security'][$sk] = '0'; }
  if (!empty($_POST['zp_opts']) && is_array($_POST['zp_opts'])) {
    foreach($_POST['zp_opts'] as $section=>$fields){
      $section=sanitize_key($section); if(!isset($opts[$section]) || !is_array($fields)) continue;
      foreach($fields as $key=>$val){
        $key=sanitize_key($key);
        $opts[$section][$key] = is_array($val) ? '' : wp_kses_post(wp_unslash($val));
      }
    }
    if (!empty($opts['stats']) && is_array($opts['stats'])) {
      $opts['hero']['projects_count'] = $opts['stats']['projects_count'] ?? ($opts['hero']['projects_count'] ?? '114');
      $opts['hero']['fb_reviews'] = $opts['stats']['facebook_reviews'] ?? ($opts['hero']['fb_reviews'] ?? '43');
      $opts['hero']['google_reviews'] = $opts['stats']['google_reviews'] ?? ($opts['hero']['google_reviews'] ?? '22');
    }
    update_option('zp_suite_options', $opts, false);
  }
  $cms = zp_suite_cms();
  if (!empty($_POST['zp_cms']) && is_array($_POST['zp_cms'])) {
    $raw = wp_unslash($_POST['zp_cms']);
    $cms['portfolio']['web'] = zp_suite_sanitize_repeater($raw['portfolio']['web'] ?? []);
    $cms['portfolio']['logo'] = zp_suite_sanitize_repeater($raw['portfolio']['logo'] ?? []);
    $cms['reviews'] = zp_suite_sanitize_repeater($raw['reviews'] ?? []);
    $cms['logos'] = zp_suite_sanitize_repeater($raw['logos'] ?? []);
    $cms['faq'] = zp_suite_sanitize_repeater($raw['faq'] ?? []);
    $cms['industries'] = zp_suite_sanitize_repeater($raw['industries'] ?? []);
    update_option('zp_suite_cms', $cms, false);
  }
  echo '<div class="zpNotice">Zapisano ustawienia Zaprojektowani Suite 1.6.0. Wyczyść LiteSpeed Cache, jeśli testujesz front.</div>';
}

function zp_field($section,$key,$label,$type='text',$help=''){
  $v = zp_suite_opt($section.'.'.$key, '');
  echo '<label class="zpField"><strong>'.esc_html($label).'</strong>';
  if($type==='textarea') echo '<textarea name="zp_opts['.esc_attr($section).']['.esc_attr($key).']">'.esc_textarea($v).'</textarea>';
  elseif($type==='media') echo '<div class="zpMediaRow"><input class="zpMediaInput" type="text" name="zp_opts['.esc_attr($section).']['.esc_attr($key).']" value="'.esc_attr($v).'"><button class="button zpPickMedia" data-target="input[name=&quot;zp_opts['.esc_attr($section).']['.esc_attr($key).']&quot;]">Wybierz</button></div>'.($v?'<img class="zpPreviewImg" src="'.esc_url($v).'" alt="">':'');
  elseif($type==='number') echo '<input type="number" step="any" name="zp_opts['.esc_attr($section).']['.esc_attr($key).']" value="'.esc_attr($v).'">';
  elseif($type==='email') echo '<input type="email" name="zp_opts['.esc_attr($section).']['.esc_attr($key).']" value="'.esc_attr($v).'">';
  else echo '<input type="text" name="zp_opts['.esc_attr($section).']['.esc_attr($key).']" value="'.esc_attr($v).'">';
  if($help) echo '<small>'.esc_html($help).'</small>'; echo '</label>';
}

function zp_checkbox($section,$key,$label){
  $v = zp_suite_opt($section.'.'.$key, '1');
  echo '<label class="zpSwitch"><input type="checkbox" name="zp_opts['.esc_attr($section).']['.esc_attr($key).']" value="1" '.checked((string)$v,'1',false).'> <span>'.esc_html($label).'</span></label>';
}
function zp_admin_input($name,$label,$value='',$type='text',$wide=false){
  $cls=$wide?'zpField wide':'zpField'; echo '<label class="'.$cls.'"><strong>'.esc_html($label).'</strong>';
  if($type==='textarea') echo '<textarea name="'.esc_attr($name).'">'.esc_textarea($value).'</textarea>';
  elseif($type==='media') echo '<div class="zpMediaRow"><input class="zpMediaInput" type="text" name="'.esc_attr($name).'" value="'.esc_attr($value).'"><button class="button zpPickMedia" data-target="input[name=&quot;'.esc_attr($name).'&quot;]">Wybierz</button></div>'.($value?'<img class="zpPreviewImg" src="'.esc_url($value).'" alt="">':'');
  else echo '<input type="text" name="'.esc_attr($name).'" value="'.esc_attr($value).'">';
  echo '</label>';
}

function zp_portfolio_row($mode,$i,$item=[]){ ob_start(); 
  $title = $item['brand'] ?? 'Nowy projekt';
  $sub = $item['sub'] ?? ($item['type'] ?? '');
  $img = $item['img'] ?? '';
?>
  <div class="zpRepeatItem zpRepeatItem--portfolio">
    <details>
      <summary>
        <span class="zpRepeatSummaryTitle">
          <?php if($img): ?><img class="zpRepeatThumb" src="<?php echo esc_url($img); ?>" alt=""><?php endif; ?>
          <span class="zpRepeatSummaryText"><strong><?php echo esc_html($title); ?></strong><span><?php echo esc_html($mode === 'web' ? 'WWW / sklep / system' : 'Logo / branding'); ?><?php echo $sub ? ' • '.esc_html($sub) : ''; ?></span></span>
        </span>
        <span class="zpRepeatChevron">›</span>
      </summary>
      <div class="zpRepeatTop"><strong>Detale projektu</strong><button class="button zpRemoveRow">Usuń</button></div>
      <div class="zpRepeatGrid">
        <?php zp_admin_input("zp_cms[portfolio][$mode][$i][visible]",'Pokaż na stronie? 1/0',$item['visible']??'1'); ?><?php zp_admin_input("zp_cms[portfolio][$mode][$i][brand]",'Nazwa projektu',$item['brand']??''); ?>
        <?php zp_admin_input("zp_cms[portfolio][$mode][$i][sub]",'Krótki opis pod nazwą',$item['sub']??''); ?>
        <?php zp_admin_input("zp_cms[portfolio][$mode][$i][type]",'Typ / pill',$item['type']??''); ?>
        <?php zp_admin_input("zp_cms[portfolio][$mode][$i][year]",'Rok',$item['year']??''); ?>
        <?php zp_admin_input("zp_cms[portfolio][$mode][$i][tag]",'Kategoria / branża',$item['tag']??''); ?>
        <?php zp_admin_input("zp_cms[portfolio][$mode][$i][live]",'Link live',$item['live']??''); ?>
        <?php zp_admin_input("zp_cms[portfolio][$mode][$i][img]",'Obraz / mockup',$item['img']??'','media',true); ?>
        <?php zp_admin_input("zp_cms[portfolio][$mode][$i][client]",'Klient',$item['client']??''); ?>
        <?php zp_admin_input("zp_cms[portfolio][$mode][$i][services]",'Usługi',$item['services']??''); ?>
        <?php zp_admin_input("zp_cms[portfolio][$mode][$i][meta]",'Tagi na karcie, oddziel |',$item['meta']??'','text',true); ?>
        <?php zp_admin_input("zp_cms[portfolio][$mode][$i][desc]",'Opis w oknie szczegółów',$item['desc']??'','textarea',true); ?>
        <?php zp_admin_input("zp_cms[portfolio][$mode][$i][scope]",'Zakres prac, oddziel |',$item['scope']??'','textarea',true); ?>
      </div>
    </details>
  </div>
<?php return ob_get_clean(); }
function zp_review_row($i,$item=[]){ ob_start(); ?>
  <div class="zpRepeatItem"><div class="zpRepeatTop"><strong>Opinia</strong><button class="button zpRemoveRow">Usuń</button></div><div class="zpRepeatGrid">
    <?php zp_admin_input("zp_cms[reviews][$i][visible]",'Pokaż na stronie? 1/0',$item['visible']??'1'); ?><?php zp_admin_input("zp_cms[reviews][$i][name]",'Imię / nazwa',$item['name']??''); ?><?php zp_admin_input("zp_cms[reviews][$i][source]",'Źródło Google/Facebook',$item['source']??'Google'); ?><?php zp_admin_input("zp_cms[reviews][$i][rating]",'Ocena',$item['rating']??'5.0'); ?>
    <?php zp_admin_input("zp_cms[reviews][$i][date]",'Data / opis czasu',$item['date']??''); ?><?php zp_admin_input("zp_cms[reviews][$i][avatar]",'Inicjały',$item['avatar']??''); ?><?php zp_admin_input("zp_cms[reviews][$i][featured]",'Wyróżniona? 1/0',$item['featured']??'0'); ?>
    <?php zp_admin_input("zp_cms[reviews][$i][text]",'Treść opinii',$item['text']??'','textarea',true); ?>
  </div></div>
<?php return ob_get_clean(); }
function zp_logo_row($i,$item=[]){ ob_start(); ?>
  <div class="zpRepeatItem"><div class="zpRepeatTop"><strong>Logo klienta</strong><button class="button zpRemoveRow">Usuń</button></div><div class="zpRepeatGrid">
    <?php zp_admin_input("zp_cms[logos][$i][visible]",'Pokaż na stronie? 1/0',$item['visible']??'1'); ?><?php zp_admin_input("zp_cms[logos][$i][name]",'Nazwa',$item['name']??''); ?><?php zp_admin_input("zp_cms[logos][$i][url]",'Link opcjonalnie',$item['url']??''); ?><?php zp_admin_input("zp_cms[logos][$i][image]",'Plik logo',$item['image']??'','media',true); ?>
  </div></div>
<?php return ob_get_clean(); }
function zp_faq_row($i,$item=[]){ ob_start(); ?>
  <div class="zpRepeatItem"><div class="zpRepeatTop"><strong>FAQ</strong><button class="button zpRemoveRow">Usuń</button></div><div class="zpRepeatGrid">
    <?php zp_admin_input("zp_cms[faq][$i][visible]",'Pokaż na stronie? 1/0',$item['visible']??'1'); ?><?php zp_admin_input("zp_cms[faq][$i][q]",'Pytanie',$item['q']??'','text',true); ?><?php zp_admin_input("zp_cms[faq][$i][a]",'Odpowiedź',$item['a']??'','textarea',true); ?>
  </div></div>
<?php return ob_get_clean(); }
function zp_industry_row($i,$item=[]){ ob_start(); ?>
  <div class="zpRepeatItem"><div class="zpRepeatTop"><strong>Branża</strong><button class="button zpRemoveRow">Usuń</button></div><div class="zpRepeatGrid">
    <?php zp_admin_input("zp_cms[industries][$i][visible]",'Pokaż na stronie? 1/0',$item['visible']??'1'); ?><?php zp_admin_input("zp_cms[industries][$i][num]",'Numer',$item['num']??''); ?><?php zp_admin_input("zp_cms[industries][$i][icon]",'Ikona Lucide',$item['icon']??'briefcase'); ?><?php zp_admin_input("zp_cms[industries][$i][title]",'Tytuł',$item['title']??''); ?>
    <?php zp_admin_input("zp_cms[industries][$i][url]",'URL',$item['url']??''); ?><?php zp_admin_input("zp_cms[industries][$i][image]",'Mockup / obraz',$item['image']??'','media',true); ?><?php zp_admin_input("zp_cms[industries][$i][text]",'Opis',$item['text']??'','textarea',true); ?>
  </div></div>
<?php return ob_get_clean(); }

function zp_suite_render_admin_page(){
  zp_suite_save_admin(); $cms=zp_suite_cms(); ?>
  <div class="zpSuiteAdmin">
    <div class="zpSuiteHero"><span class="zpSuiteBadge">Zaprojektowani Suite 1.9.04 • Backend Polish</span><h1>Panel zarządzania stroną główną bez grzebania w kodzie.</h1><p>Edytujesz header, hero, logotypy, portfolio, opinie, FAQ, branże, formularz i SEO. Front zachowuje obecny wygląd — zmieniają się tylko dane podstawiane do widgetów.</p><div class="zpStatus"><div class="zpStat"><strong>1.9.04</strong><span>wersja CMS</span></div><div class="zpStat"><strong>Media</strong><span>wgrywanie z dysku</span></div><div class="zpStat"><strong>AJAX</strong><span>formularz kontaktowy</span></div><div class="zpStat"><strong>SEO</strong><span>home + schema</span></div></div></div>
    <form method="post"><?php wp_nonce_field('zp_suite_save'); ?><input type="hidden" name="zp_suite_save" value="1">
      <div class="zpTabs"><button type="button" class="is-active" data-tab="zpTabHeader">Header</button><button type="button" data-tab="zpTabHero">Hero</button><button type="button" data-tab="zpTabLogos">Logotypy</button><button type="button" data-tab="zpTabPortfolio">Portfolio</button><button type="button" data-tab="zpTabReviews">Opinie</button><button type="button" data-tab="zpTabFaq">FAQ</button><button type="button" data-tab="zpTabIndustries">Branże</button><button type="button" data-tab="zpTabContact">Formularz</button><button type="button" data-tab="zpTabFooter">Stopka</button><button type="button" data-tab="zpTabStats">Liczby / widoczność</button>
      <button type="button" data-tab="zpTabOrder">Kolejność sekcji</button><button type="button" data-tab="zpTabEmails">E-maile</button><button type="button" data-tab="zpTabSeo">SEO</button><button type="button" data-tab="zpTabPerformance">Wydajność</button></div>
      <section id="zpTabHeader" class="zpPanel is-active"><div class="zpGrid"><div class="zpCard"><h2>Logo i rozmiary</h2><?php zp_field('brand','logo_light','Logo główne','media'); zp_field('brand','logo_dark','Logo alternatywne','media'); zp_field('header','logo_desktop_h','Wysokość logo desktop px','number'); zp_field('header','logo_mobile_h','Wysokość logo mobile px','number'); zp_field('header','logo_mobile_x','Przesunięcie logo mobile X','number'); zp_field('header','desktop_height','Wysokość header desktop','number'); zp_field('header','mobile_height','Wysokość header mobile','number'); ?><div class="zpHelpBox" style="margin-top:14px">Rozmiary logo są teraz sterowane wyłącznie z tych pól. Desktop i sticky mają tę samą wysokość, więc logo nie powinno zmniejszać się po załadowaniu ani po scrollu. Po zmianie wyczyść cache/Nitro.</div></div><div class="zpCard"><h2>Zdjęcie w hero Wiedza</h2><?php zp_field('wiedza','hero_person_scale_desktop','Desktop — skala zdjęcia np. 1.08'); zp_field('wiedza','hero_person_y_desktop','Desktop — góra/dół px','number'); zp_field('wiedza','hero_person_x_desktop','Desktop — lewo/prawo px','number'); zp_field('wiedza','hero_person_scale_mobile','Mobile — skala zdjęcia np. 1.12'); zp_field('wiedza','hero_person_y_mobile','Mobile — góra/dół px','number'); zp_field('wiedza','hero_person_x_mobile','Mobile — lewo/prawo px','number'); ?><hr style="border:0;border-top:1px solid rgba(7,17,31,.10);margin:18px 0"><h3 style="margin:0 0 12px;font-size:15px">Video w hero Wiedza</h3><?php zp_field('wiedza','hero_video_opacity','Widoczność video 0–100','number','Domyślnie 74. Więcej = mocniej widać video.'); zp_field('wiedza','hero_video_brightness','Jasność video 0–100','number','Domyślnie 74. Mniej = ciemniej pod tekstem.'); zp_field('wiedza','hero_video_navy_mask','Maska navy 0–100','number','Domyślnie 82. Więcej = mocniejszy granatowy odcień, mniej czerwieni.'); zp_field('wiedza','hero_text_shadow','Cień pod heading/CTA 0–100','number','Domyślnie 72. Więcej = mniej świeci pod tekstem.'); ?><div class="zpHelpBox" style="margin-top:14px">Oddzielne sterowanie zdjęciem po prawej stronie hero Wiedza oraz odcieniem video. Pola video działają jako CSS variables generowane bezpośrednio w shortcodzie, więc po zmianie wyczyść cache/Nitro.</div></div><div class="zpCard"><h2>Menu i CTA</h2><?php zp_field('header','menu_start','Pozycje menu label|url','textarea'); zp_field('header','cta_text','Tekst CTA'); zp_field('header','cta_url','URL CTA'); zp_field('header','cta_icon','Ikona CTA'); zp_field('header','contact_url','URL kontaktu'); zp_field('header','mobile_glass_opacity','Przezroczystość mobile header','number'); zp_field('header','sticky_white_strength','Sticky header — biel / przezroczystość 1–100','number','1 = bardzo przezroczysty, 100 = prawie pełna biel. Domyślnie 85.'); zp_field('header','overlay_divider_width','Divider static hero — szerokość desktop px','number'); zp_field('header','overlay_divider_opacity','Divider static hero — wtopienie/opacity 0–1','number'); zp_field('header','header_line_width','NOWA linia headera — szerokość desktop px','number','Domyślnie 1850. Mobile jest pełna szerokość + 40px.'); zp_field('header','header_line_opacity','NOWA linia headera — widoczność static 0–1','number','Domyślnie 0.38. Zwiększ np. do 0.45 jeśli ma być mocniejsza.'); zp_field('header','header_line_scrolled_opacity','NOWA linia headera — widoczność po scrollu 0–1','number','Domyślnie 0.24.'); ?><div class="zpHelpBox">Docelowe URL: <code>/strony-internetowe-katowice/</code>, <code>/sklepy-internetowe-katowice/</code>, <code>/logo-branding-katowice/</code>, <code>/kampanie-reklamowe/</code>.</div></div></div></section>
      <section id="zpTabHero" class="zpPanel"><div class="zpGrid"><div class="zpCard"><h2>Treści hero</h2><?php zp_field('hero','eyebrow','Kicker'); zp_field('hero','h1_html','H1 HTML','textarea'); zp_field('hero','lead','Lead','textarea'); zp_field('hero','chips','Pille numer|tekst','textarea'); zp_field('hero','primary_text','CTA główne'); zp_field('hero','primary_url','CTA główne URL'); zp_field('hero','secondary_text','CTA drugie'); zp_field('hero','secondary_url','CTA drugie URL'); ?></div><div class="zpCard"><h2>Media i wygląd</h2><?php zp_field('hero','video_url','Video hero','media'); zp_field('hero','person_url','Zdjęcie zespołu','media'); zp_field('hero','video_dim','Przyciemnienie video','number'); zp_field('hero','video_color_opacity','Odcień/maska video','number'); zp_field('hero','video_shadow_opacity','Ciemność cienia pod tekstem / H1','number'); zp_field('hero','font_desktop','H1 desktop px','number'); zp_field('hero','font_mobile','H1 mobile px','number'); zp_field('hero','desktop_top_space','Desktop: odstęp nad zdjęciem / hero top +px','number'); zp_field('hero','desktop_bottom_space','Desktop: odstęp za podpisem / hero bottom +px','number'); zp_field('hero','person_width_desktop','Zdjęcie zespołu desktop: szerokość px','number'); zp_field('hero','person_x_desktop','Zdjęcie zespołu desktop: lewo/prawo px','number'); zp_field('hero','person_y_desktop','Zdjęcie zespołu desktop: góra/dół px','number'); zp_field('hero','person_scale_desktop','Zdjęcie zespołu desktop: skala np. 1.13'); zp_field('hero','copy_y_desktop','Lewa kolumna hero desktop: góra/dół px (ujemna wartość podnosi)','number'); zp_field('hero','signature_y_desktop','Cytat + podpis w hero desktop: góra/dół px (ujemna wartość podnosi i skraca hero)','number','Ustaw np. -60, aby podnieść cytat i podpis. Hero automatycznie skróci się od dołu.'); zp_field('hero','signature_y_mobile','Cytat + podpis w hero mobile: góra/dół px','number','Na obecnym mobile podpis jest zwykle ukryty, ale pole zostaje na przyszłość.'); ?></div><div class="zpCard"><h2>Karty opinii w hero — desktop</h2><?php zp_field('hero','review_label_size','Desktop: label / nazwa źródła px','number'); zp_field('hero','review_rating_size','Desktop: ocena 5.0 px','number'); zp_field('hero','review_stars_size','Desktop: gwiazdki px','number'); zp_field('hero','review_opinions_size','Desktop: liczba opinii px','number'); zp_field('hero','review_desc_size','Desktop: opis pod opiniami px','number'); zp_field('hero','review_link_size','Desktop: link CTA px','number'); zp_field('hero','review_logo_width','Desktop: logo w karcie px','number'); zp_field('hero','review_card_scale','Desktop: skala kart np. 1 albo .94'); zp_field('hero','review_card_bg_opacity','Desktop: przezroczystość tła kart 0–100','number','Kolor kart zostaje taki jak wcześniej. Zmieniasz tylko moc/przezroczystość tła: 0 = prawie przezroczyste, 72 = obecny wygląd, 100 = najmocniejsze tło.'); ?><p>Te pola sterują kartami Trustindex / Facebook w hero na desktopie.</p></div><div class="zpCard"><h2>Karty opinii w hero — mobile</h2><?php zp_field('hero','review_label_size_mobile','Mobile: label / nazwa źródła px','number'); zp_field('hero','review_rating_size_mobile','Mobile: ocena 5.0 px','number'); zp_field('hero','review_stars_size_mobile','Mobile: gwiazdki px','number'); zp_field('hero','review_opinions_size_mobile','Mobile: liczba opinii px','number'); zp_field('hero','review_desc_size_mobile','Mobile: opis pod opiniami px','number'); zp_field('hero','review_link_size_mobile','Mobile: link CTA px','number'); zp_field('hero','review_logo_width_mobile','Mobile: logo w karcie px','number'); zp_field('hero','review_card_scale_mobile','Mobile: skala kart np. 1 albo .94'); ?><p>Osobne wartości dla telefonu, żeby nie ruszać wyglądu desktopu.</p></div><div class="zpCard"><h2>Liczby w hero</h2><?php zp_field('hero','fb_reviews','Liczba opinii Facebook','number'); zp_field('hero','google_reviews','Liczba opinii Google','number'); zp_field('hero','projects_count','Liczba realizacji','number'); ?></div></div></section>
      <section id="zpTabLogos" class="zpPanel"><div class="zpGrid"><div class="zpCard"><h2>Teksty widgetu</h2><?php zp_field('trust_logos','eyebrow','Kicker'); zp_field('trust_logos','heading','Heading HTML'); zp_field('trust_logos','lead','Opis','textarea'); ?></div><div class="zpCard"><h2>Logotypy klientów</h2><div class="zpToolsBar"><button class="button zpExpandAll" data-target="#zpLogosRep">Rozwiń</button><button class="button zpCollapseAll" data-target="#zpLogosRep">Zwiń</button></div><div id="zpLogosRep" class="zpRepeater"><?php foreach($cms['logos'] as $i=>$row) echo zp_logo_row($i,$row); ?></div><button class="button zpAddRow" data-repeater="#zpLogosRep" data-template="#zpTplLogo">Dodaj logo</button></div></div></section>
      <section id="zpTabPortfolio" class="zpPanel"><div class="zpGrid"><div class="zpCard"><h2>Strony / sklepy / systemy</h2><p>Lista jest teraz w akordeonie — kliknij projekt, aby rozwinąć pełne detale. Tu dodajesz wszystko, co pokazuje się w portfolio WEB. Nie używasz obrazka wyróżniającego WordPressa — obraz wybierasz w polu projektu.</p><div class="zpToolsBar"><button class="button zpExpandAll" data-target="#zpPortWeb">Rozwiń wszystko</button><button class="button zpCollapseAll" data-target="#zpPortWeb">Zwiń wszystko</button></div><div id="zpPortWeb" class="zpRepeater"><?php foreach($cms['portfolio']['web'] as $i=>$row) echo zp_portfolio_row('web',$i,$row); ?></div><button class="button zpAddRow" data-repeater="#zpPortWeb" data-template="#zpTplPortfolioWeb">Dodaj projekt WEB</button></div><div class="zpCard"><h2>Logo / branding</h2><div class="zpToolsBar"><button class="button zpExpandAll" data-target="#zpPortLogo">Rozwiń wszystko</button><button class="button zpCollapseAll" data-target="#zpPortLogo">Zwiń wszystko</button></div><div id="zpPortLogo" class="zpRepeater"><?php foreach($cms['portfolio']['logo'] as $i=>$row) echo zp_portfolio_row('logo',$i,$row); ?></div><button class="button zpAddRow" data-repeater="#zpPortLogo" data-template="#zpTplPortfolioLogo">Dodaj projekt logo</button></div></div></section>
      <section id="zpTabReviews" class="zpPanel"><div class="zpGrid zpGrid1"><div class="zpCard"><h2>Opinie</h2><p>Dodane opinie pokazują się w sekcji opinii: wyróżniona opinia, trzy większe opinie i poziomy slider. Źródło Google/Facebook automatycznie dobiera ikonę.</p><div class="zpToolsBar"><button class="button zpExpandAll" data-target="#zpReviewsRep">Rozwiń</button><button class="button zpCollapseAll" data-target="#zpReviewsRep">Zwiń</button></div><div id="zpReviewsRep" class="zpRepeater"><?php foreach($cms['reviews'] as $i=>$row) echo zp_review_row($i,$row); ?></div><button class="button zpAddRow" data-repeater="#zpReviewsRep" data-template="#zpTplReview">Dodaj opinię</button></div></div></section>
      <section id="zpTabFaq" class="zpPanel"><div class="zpGrid zpGrid1"><div class="zpCard"><h2>FAQ jasne nad branżami</h2><div id="zpFaqRep" class="zpRepeater"><?php foreach($cms['faq'] as $i=>$row) echo zp_faq_row($i,$row); ?></div><button class="button zpAddRow" data-repeater="#zpFaqRep" data-template="#zpTplFaq">Dodaj pytanie</button></div></div></section>
      <section id="zpTabIndustries" class="zpPanel"><div class="zpGrid zpGrid1"><div class="zpCard"><h2>Branże — ciemna sekcja</h2><div id="zpIndustriesRep" class="zpRepeater"><?php foreach($cms['industries'] as $i=>$row) echo zp_industry_row($i,$row); ?></div><button class="button zpAddRow" data-repeater="#zpIndustriesRep" data-template="#zpTplIndustry">Dodaj branżę</button></div></div></section>
      <section id="zpTabContact" class="zpPanel"><div class="zpGrid"><div class="zpCard"><h2>Formularz kontaktowy</h2><?php zp_field('contact','heading_html','Heading HTML','textarea'); zp_field('contact','lead','Opis','textarea'); zp_field('contact','privacy_text','Zgoda'); zp_field('brand','admin_email','E-mail odbiorcy','email'); zp_field('brand','from_email','E-mail nadawcy','email'); zp_field('brand','from_name','Nazwa nadawcy'); zp_field('brand','whatsapp','Numer WhatsApp'); ?></div><div class="zpCard"><h2>Komunikaty</h2><?php zp_field('contact','success_title','Tytuł sukcesu'); zp_field('contact','success_text','Treść sukcesu','textarea'); zp_field('contact','error_title','Tytuł błędu'); ?><p>Walidacja działa przez modal/toast na środku z rozmytym tłem. E-mail do klienta i do Zaprojektowani jest wysyłany w HTML.</p></div><div class="zpCard"><h2>Antyspam — Cloudflare Turnstile</h2><div class="zpSwitchGrid"><?php zp_checkbox('security','turnstile_enabled','Włącz Cloudflare Turnstile dla formularzy'); zp_checkbox('security','honeypot_enabled','Włącz ukryte pole honeypot'); zp_checkbox('security','min_seconds_enabled','Blokuj zbyt szybkie wysyłki'); zp_checkbox('security','turnstile_fail_open','Nie blokuj realnych klientów przy chwilowym błędzie Cloudflare'); ?></div><?php zp_field('security','turnstile_site_key','Turnstile Site Key'); zp_field('security','turnstile_secret_key','Turnstile Secret Key'); zp_field('security','min_seconds','Minimalny czas przed wysyłką w sekundach','number'); ?><p>Rekomendacja: Turnstile w trybie niewidocznym + honeypot + minimum 3 sekundy. Użytkownik nie dostaje obrazków ani ciężkiej Captchy, a boty trafiają na walidację serwerową.</p></div></div></section>
      <section id="zpTabFooter" class="zpPanel"><div class="zpGrid"><div class="zpCard"><h2>Stopka / CTA</h2><?php zp_field('footer','cta_eyebrow','Kicker CTA'); zp_field('footer','cta_heading','Nagłówek CTA HTML','textarea'); zp_field('footer','cta_lead','Opis CTA','textarea'); zp_field('footer','team_photo','Zdjęcie zespołu','media'); ?></div><div class="zpCard"><h2>Buttony i opis</h2><?php zp_field('footer','cta_primary_text','Tekst głównego buttona'); zp_field('footer','cta_primary_url','URL głównego buttona'); zp_field('footer','cta_secondary_text','Tekst WhatsApp'); zp_field('footer','about','Opis marki w stopce','textarea'); ?><p>Numer WhatsApp pobierany jest z zakładki Formularz / marka.</p></div></div></section>

      <section id="zpTabStats" class="zpPanel"><div class="zpGrid"><div class="zpCard"><h2>Zdjęcie nad sekcją O Zaprojektowani</h2><?php zp_field('about','team_image','Zdjęcie zespołu','media'); zp_field('about','team_width','Szerokość zdjęcia desktop px','number'); zp_field('about','team_top','Pozycja góra/dół zdjęcia px','number'); zp_field('about','team_right','Pozycja lewo/prawo zdjęcia px','number'); zp_field('about','team_scale','Skala zdjęcia np. 1.18'); zp_field('about','team_grayscale','Czarno-białe 0/1','number'); zp_field('about','team_opacity','Opacity 0–1'); zp_field('about','cards_top','Karty pod zdjęciem — góra/dół px','number'); zp_field('about_page','top_gap_desktop','O nas — cała sekcja/kicker góra-dół desktop px','number'); zp_field('about_page','top_gap_mobile','O nas — cała sekcja/kicker góra-dół mobile px','number'); zp_field('about_page','hero_visual_y_desktop','O nas — zdjęcie po prawej góra-dół desktop px','number'); zp_field('about_page','hero_visual_y_mobile','O nas — zdjęcie po prawej góra-dół mobile px','number'); zp_field('about_page','hero_image_y_desktop','O nas — kadrowanie zdjęcia wewnątrz desktop px','number'); zp_field('about_page','hero_image_y_mobile','O nas — kadrowanie zdjęcia wewnątrz mobile px','number'); ?><p>Zdjęcie jest ustawiane nad kartami w sekcji "O Zaprojektowani". Dolny fade zostaje, ale obraz jest kolorowy przy wartości 0 w polu czarno-białe.</p></div><div class="zpCard"><h2>Globalne liczby zaufania</h2><?php zp_field('stats','projects_count','Liczba realizacji','number'); zp_field('stats','facebook_rating','Ocena Facebook'); zp_field('stats','facebook_reviews','Liczba opinii Facebook','number'); zp_field('stats','google_rating','Ocena Google'); zp_field('stats','google_reviews','Liczba opinii Google','number'); zp_field('stats','recommendations','Liczba poleceń / trzecia statystyka','number'); ?><p>Te liczby są wspólne dla hero, widgetu logotypów i kolejnych sekcji. Dzięki temu nie będzie rozjazdów typu 114 / 420+.</p></div><div class="zpCard"><h2>Widoczność sekcji na home</h2><div class="zpSwitchGrid"><?php zp_checkbox('visibility','trust_logos','Logotypy'); zp_checkbox('visibility','services_path','Zakres usług'); zp_checkbox('visibility','showcase_portfolio','Portfolio'); zp_checkbox('visibility','laptop_showcase','Laptop showcase'); zp_checkbox('visibility','about_experience','O Zaprojektowani'); zp_checkbox('visibility','showcase_services','Showcase usług'); zp_checkbox('visibility','reviews_section','Opinie'); zp_checkbox('visibility','seo_faq','FAQ'); zp_checkbox('visibility','seo_industries','Branże'); zp_checkbox('visibility','home_audit_cta','Mini-audyt CTA'); zp_checkbox('visibility','contact_system','Kontakt'); ?></div><p>Dotyczy shortcode’u <code>[zp_home_full]</code>. Header i footer są zewnętrznie osobno.</p></div></div><?php if(function_exists('zp_suite_render_stats_embed')) zp_suite_render_stats_embed(); ?></section>
      
      <section id="zpTabPerformance" class="zpPanel">
        <div class="zpGrid">
          <div class="zpCard">
            <h2>Performance Lite — strona główna</h2>
            <div class="zpSwitchGrid">
              <?php zp_checkbox('performance','home_lite_enabled','Włącz Performance Lite na home'); ?>
              <?php zp_checkbox('performance','home_lite_mobile_enabled','Mocniejsze odciążenie na mobile'); ?>
              <?php zp_checkbox('performance','disable_blur','Ogranicz blur / backdrop-filter'); ?>
              <?php zp_checkbox('performance','reduce_reveals','Uprość reveal’e / wejścia sekcji'); ?>
              <?php zp_checkbox('performance','reduce_shadows_watermarks','Lżejsze cienie i watermarki'); ?>
              <?php zp_checkbox('performance','disable_mobile_heavy_motion','Wyłącz ciężkie animacje na mobile'); ?>
              <?php zp_checkbox('performance','respect_reduced_motion','Szanuj prefers-reduced-motion'); ?>
            </div>
            <p>Tryb działa tylko na stronie głównej. Nie zmienia układu sekcji — odciąża blur, animacje, reveal’e, ciężkie cienie i ruchome dekoracje.</p>
          </div>
          <div class="zpCard">
            <h2>Performance Lite — strony internetowe Katowice</h2>
            <div class="zpSwitchGrid">
              <?php zp_checkbox('performance','katowice_lite_enabled','Włącz Performance Lite na /strony-internetowe-katowice/'); ?>
              <?php zp_checkbox('performance','katowice_lite_mobile_enabled','Mocniejsze odciążenie na mobile'); ?>
              <?php zp_checkbox('performance','katowice_disable_blur','Ogranicz blur / backdrop-filter'); ?>
              <?php zp_checkbox('performance','katowice_reduce_reveals','Uprość reveal’e / wejścia sekcji'); ?>
              <?php zp_checkbox('performance','katowice_reduce_shadows_watermarks','Lżejsze cienie, poświaty i watermarki'); ?>
              <?php zp_checkbox('performance','katowice_disable_mobile_heavy_motion','Wyłącz ciężkie animacje na mobile'); ?>
              <?php zp_checkbox('performance','katowice_respect_reduced_motion','Szanuj prefers-reduced-motion'); ?>
            </div>
            <p>Tryb dotyczy tylko podstrony <code>/strony-internetowe-katowice/</code>. Układ i treści zostają bez zmian — odciążamy GPU, dekoracje, animacje i blur.</p>
          </div>
          <div class="zpCard">
            <h2>Performance Lite — sklepy internetowe Katowice</h2>
            <div class="zpSwitchGrid">
              <?php zp_checkbox('performance','shop_katowice_lite_enabled','Włącz Performance Lite na /sklepy-internetowe-katowice/'); ?>
              <?php zp_checkbox('performance','shop_katowice_lite_mobile_enabled','Mocniejsze odciążenie na mobile'); ?>
              <?php zp_checkbox('performance','shop_katowice_disable_blur','Ogranicz blur / backdrop-filter'); ?>
              <?php zp_checkbox('performance','shop_katowice_reduce_reveals','Uprość reveal’e / wejścia sekcji'); ?>
              <?php zp_checkbox('performance','shop_katowice_reduce_shadows_watermarks','Lżejsze cienie, poświaty i watermarki'); ?>
              <?php zp_checkbox('performance','shop_katowice_disable_mobile_heavy_motion','Wyłącz ciężkie animacje na mobile'); ?>
              <?php zp_checkbox('performance','shop_katowice_respect_reduced_motion','Szanuj prefers-reduced-motion'); ?>
            </div>
            <p>Tryb dotyczy tylko podstrony <code>/sklepy-internetowe-katowice/</code>. Zostawia układ i treści, ale odciąża hero, portfolio, opinie, proces, watermarki, blur i animacje mobile.</p>
          </div>
          <div class="zpCard">
            <h2>Co dokładnie robi?</h2>
            <p><strong>Bezpieczny etap 1:</strong> usuwa najcięższe efekty GPU, skraca animacje wejścia, ogranicza <code>backdrop-filter</code>, redukuje <code>will-change</code> i wyłącza dekoracyjne animacje na telefonach. Sliderów, linków i formularzy nie rusza.</p>
            <p>Gdy po wdrożeniu będzie OK, można w kolejnym etapie zrobić lazy init portfolio/opinii.</p>
          </div>
        </div>
      </section>
<section id="zpTabOrder" class="zpPanel"><div class="zpGrid zpGrid1"><div class="zpCard"><h2>Kolejność sekcji na stronie głównej</h2><p>Przeciągnij sekcje myszką. Zmiana dotyczy shortcode’u <code>[zp_home_full]</code>. Header i footer nadal dodajesz osobno.</p><input type="hidden" id="zpHomeOrderInput" name="zp_opts[home_order][order]" value="<?php echo esc_attr(zp_suite_opt('home_order.order','')); ?>"><div id="zpHomeOrder" class="zpOrderList"><?php $map = function_exists('zp_suite_home_sections_map') ? zp_suite_home_sections_map() : []; foreach((function_exists('zp_suite_home_order') ? zp_suite_home_order() : array_keys($map)) as $key): if(empty($map[$key])) continue; ?><div class="zpOrderItem" data-key="<?php echo esc_attr($key); ?>"><span class="zpDragHandle">☰</span><div><b><?php echo esc_html($map[$key]['label']); ?></b><small><br><?php echo esc_html($key); ?></small></div><span><?php echo empty($map[$key]['visibility']) || zp_suite_section_enabled($map[$key]['visibility']) ? 'aktywna' : 'ukryta'; ?></span></div><?php endforeach; ?></div><p class="zpNoticeMini">Tip: jeżeli sekcja jest ukryta w „Liczby / widoczność”, jej kolejność zostanie zapisana, ale nie pokaże się na froncie.</p></div></div></section>
      <section id="zpTabEmails" class="zpPanel"><div class="zpGrid"><div class="zpCard"><h2>Szablony e-mail</h2><?php zp_field('email_templates','admin_subject_form','Temat maila do admina — formularz'); zp_field('email_templates','admin_subject_phone','Temat maila do admina — prośba o telefon'); zp_field('email_templates','client_subject','Temat potwierdzenia do klienta'); zp_field('email_templates','client_intro','Treść wstępu potwierdzenia','textarea'); zp_field('email_templates','email_footer','Stopka maila'); ?></div><div class="zpCard"><h2>Bezpieczeństwo</h2><p>Formularze używają nonce, walidacji, zapisu leadów w bazie oraz maili HTML. W kolejnym kroku można dodać Turnstile/reCAPTCHA i eksport leadów do CSV/CRM.</p></div></div></section>
      <section id="zpTabSeo" class="zpPanel"><div class="zpGrid"><div class="zpCard"><h2>Rank Math / Home SEO</h2><?php zp_field('seo','home_title','Meta title'); zp_field('seo','home_description','Meta description','textarea'); zp_field('seo','focus_keywords','Focus keywords','textarea'); ?></div><div class="zpCard"><h2>Shortcode strony głównej</h2><p>Najbezpieczniej:</p><pre>[zp_header]
[zp_home_full]
[zp_footer]</pre><p><strong>[zp_home_full]</strong> nie zawiera headera ani stopki.</p></div></div></section>
      <div class="zpSave"><button class="button button-primary button-large">Zapisz wszystkie ustawienia</button></div>
    </form>
    <template id="zpTplLogo"><?php echo zp_logo_row('__i__', []); ?></template><template id="zpTplPortfolioWeb"><?php echo zp_portfolio_row('web','__i__', []); ?></template><template id="zpTplPortfolioLogo"><?php echo zp_portfolio_row('logo','__i__', []); ?></template><template id="zpTplReview"><?php echo zp_review_row('__i__', []); ?></template><template id="zpTplFaq"><?php echo zp_faq_row('__i__', []); ?></template><template id="zpTplIndustry"><?php echo zp_industry_row('__i__', []); ?></template>
  </div>
<?php }

function zp_suite_render_leads_page(){
  if (!current_user_can('manage_options')) { return; }

  $statuses = [
    'new'      => 'Nowe',
    'read'     => 'Przeczytane',
    'progress' => 'W trakcie',
    'offer'    => 'Oferta wysłana',
    'closed'   => 'Zamknięte',
    'spam'     => 'Spam',
  ];

  $leads = function_exists('zp_suite_leads_all') ? zp_suite_leads_all() : [];
  if (!is_array($leads)) { $leads = []; }

  $notice = '';

  if (!empty($_POST['zp_leads_simple_action']) && check_admin_referer('zp_suite_leads_simple_action')) {
    $action  = sanitize_key(wp_unslash($_POST['zp_leads_simple_action']));
    $lead_id = sanitize_text_field(wp_unslash($_POST['lead_id'] ?? ''));
    $before  = count($leads);

    if ($action === 'delete' && $lead_id !== '') {
      $leads = array_values(array_filter($leads, function($lead) use ($lead_id){
        return (string)($lead['id'] ?? '') !== $lead_id;
      }));
      update_option('zp_suite_leads', $leads, false);
      $notice = 'Usunięto zgłoszenie.';
    }

    if ($action === 'save' && $lead_id !== '') {
      $new_status = sanitize_key(wp_unslash($_POST['lead_status'] ?? 'read'));
      $note = sanitize_textarea_field(wp_unslash($_POST['lead_note'] ?? ''));
      foreach ($leads as $i => $lead) {
        if ((string)($lead['id'] ?? '') === $lead_id) {
          $leads[$i]['status'] = isset($statuses[$new_status]) ? $new_status : 'read';
          $leads[$i]['note'] = $note;
          $leads[$i]['updated_at'] = current_time('mysql');
          break;
        }
      }
      update_option('zp_suite_leads', $leads, false);
      $notice = 'Zapisano status i notatkę.';
    }

    if ($action === 'clear_closed') {
      $leads = array_values(array_filter($leads, function($lead){
        return !in_array(($lead['status'] ?? 'new'), ['closed','spam'], true);
      }));
      update_option('zp_suite_leads', $leads, false);
      $notice = 'Usunięto ' . max(0, $before - count($leads)) . ' zamkniętych/spam zgłoszeń.';
    }

    if ($action === 'clear_all') {
      $leads = [];
      update_option('zp_suite_leads', $leads, false);
      $notice = 'Wyczyszczono wszystkie zgłoszenia.';
    }

    if (!headers_sent()) {
      wp_safe_redirect(add_query_arg('zp_notice', rawurlencode($notice), admin_url('admin.php?page=zp-suite-leads')));
      exit;
    }
  }

  // Backward compatibility for old action links.
  if (!empty($_GET['zp_lead_action']) && !empty($_GET['lead_id']) && check_admin_referer('zp_suite_lead_action')) {
    $action = sanitize_key(wp_unslash($_GET['zp_lead_action']));
    $lead_id = sanitize_text_field(wp_unslash($_GET['lead_id']));
    foreach ($leads as $i => $lead) {
      if ((string)($lead['id'] ?? '') === $lead_id) {
        if ($action === 'delete') { unset($leads[$i]); }
        elseif ($action === 'new') { $leads[$i]['status'] = 'new'; }
        elseif ($action === 'read') { $leads[$i]['status'] = 'read'; }
        elseif (isset($statuses[$action])) { $leads[$i]['status'] = $action; }
        break;
      }
    }
    update_option('zp_suite_leads', array_values($leads), false);
    wp_safe_redirect(admin_url('admin.php?page=zp-suite-leads'));
    exit;
  }

  $filter_status = sanitize_key(wp_unslash($_GET['lead_status'] ?? ''));
  $filter_q = sanitize_text_field(wp_unslash($_GET['lead_q'] ?? ''));

  $all_leads = $leads;
  if ($filter_status || $filter_q) {
    $leads = array_values(array_filter($leads, function($lead) use ($filter_status, $filter_q){
      if ($filter_status && ($lead['status'] ?? 'new') !== $filter_status) { return false; }
      if ($filter_q) {
        $hay = strtolower(wp_json_encode($lead, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
        if (strpos($hay, strtolower($filter_q)) === false) { return false; }
      }
      return true;
    }));
  }

  $counts = ['all' => count($all_leads)];
  foreach ($statuses as $key => $label) { $counts[$key] = 0; }
  foreach ($all_leads as $lead) {
    $st = $lead['status'] ?? 'new';
    if (isset($counts[$st])) { $counts[$st]++; }
  }

  $notice = isset($_GET['zp_notice']) ? sanitize_text_field(wp_unslash($_GET['zp_notice'])) : '';

  ?>
  <div class="wrap zpLeadsSimple">
    <style>.zpLeadsSimple{max-width:1440px}.zpLeadsHero{margin:18px 0 18px;padding:26px 28px;border-radius:22px;background:linear-gradient(135deg,#05070b,#071426 55%,#102a4f);color:#fff}.zpLeadsHero h1{margin:0 0 8px;font-size:30px;line-height:1.05;letter-spacing:-.035em;color:#fff}.zpLeadsHero p{margin:0;color:rgba(255,255,255,.72);font-size:14px;line-height:1.6;max-width:860px}.zpLeadsStats{display:flex;gap:10px;flex-wrap:wrap;margin-top:18px}.zpLeadsStat{border:1px solid rgba(255,255,255,.15);background:rgba(255,255,255,.06);border-radius:16px;padding:11px 14px;min-width:130px}.zpLeadsStat strong{display:block;font-size:20px;line-height:1;color:#fff}.zpLeadsStat span{display:block;margin-top:4px;font-size:10px;text-transform:uppercase;letter-spacing:.12em;color:rgba(255,255,255,.58)}.zpLeadsToolbar{display:flex;gap:10px;align-items:end;flex-wrap:wrap;background:#fff;border:1px solid #dcdcde;border-radius:18px;padding:14px;margin-bottom:14px}.zpLeadsToolbar label{font-weight:700;color:#1d2327;font-size:12px}.zpLeadsToolbar select,.zpLeadsToolbar input[type=search]{min-height:36px;min-width:190px}.zpLeadList{display:grid;gap:14px}.zpLeadCard{background:#fff;border:1px solid #dcdcde;border-radius:20px;overflow:hidden;box-shadow:0 10px 28px rgba(7,20,38,.05)}.zpLeadCard.is-new{border-color:#1c477a;box-shadow:0 0 0 1px rgba(28,71,122,.18),0 10px 28px rgba(7,20,38,.07)}.zpLeadTop{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:18px;align-items:start;padding:18px 20px;border-bottom:1px solid #eef0f3;background:#fbfcfe}.zpLeadTitle h2{margin:0 0 5px;font-size:20px;line-height:1.15;color:#071426}.zpLeadTitle small{display:block;color:#667085}.zpLeadBadge{display:inline-flex;align-items:center;border-radius:999px;padding:6px 9px;font-size:11px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;background:#eaf2ff;color:#0b3b75}.zpLeadBody{display:grid;grid-template-columns:minmax(0,1fr) 310px;gap:18px;padding:18px 20px}.zpLeadContact{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px;margin-bottom:14px}.zpLeadContactBox{border:1px solid #e3e7ed;border-radius:14px;padding:11px 12px;background:#fff}.zpLeadContactBox span{display:block;font-size:10px;text-transform:uppercase;letter-spacing:.12em;font-weight:800;color:#7a8493;margin-bottom:5px}.zpLeadContactBox strong{display:block;color:#071426;word-break:break-word;font-size:14px}.zpLeadMessage{white-space:pre-wrap;background:#f7f9fc;border:1px solid #e7ebf1;border-radius:14px;padding:14px;color:#273142;line-height:1.55;margin:0 0 14px}.zpLeadDetails{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px}.zpLeadDetail{border:1px solid #edf0f4;border-radius:12px;padding:10px;background:#fff}.zpLeadDetail span{display:block;color:#7a8493;font-size:10px;letter-spacing:.1em;text-transform:uppercase;font-weight:800;margin-bottom:4px}.zpLeadDetail strong{display:block;color:#111827;font-size:13px;line-height:1.45;word-break:break-word}.zpLeadDetail--full{grid-column:1/-1}.zpLeadSide{border-left:1px solid #edf0f4;padding-left:18px}.zpLeadActions{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:14px}.zpLeadActions .button{margin:0}.zpLeadDelete{color:#b42318!important;border-color:#f3b8b2!important}.zpLeadNote textarea{width:100%;min-height:90px;margin:8px 0}.zpLeadFiles a{display:block;margin:4px 0}@media(max-width:1100px){.zpLeadBody{grid-template-columns:1fr}.zpLeadSide{border-left:0;border-top:1px solid #edf0f4;padding-left:0;padding-top:16px}.zpLeadContact{grid-template-columns:1fr}.zpLeadDetails{grid-template-columns:1fr}}</style>

    <div class="zpLeadsHero">
      <h1>Leady i formularze</h1>
      <p>Uproszczony panel: wszystkie dane z formularzy/briefów w jednym miejscu, szybki podgląd kontaktu, status, notatka i działające usuwanie.</p>
      <div class="zpLeadsStats">
        <div class="zpLeadsStat"><strong><?php echo esc_html($counts['all']); ?></strong><span>wszystkie</span></div>
        <div class="zpLeadsStat"><strong><?php echo esc_html($counts['new']); ?></strong><span>nowe</span></div>
        <div class="zpLeadsStat"><strong><?php echo esc_html($counts['progress']); ?></strong><span>w trakcie</span></div>
      </div>
    </div>

    <?php if ($notice) : ?><div class="notice notice-success is-dismissible"><p><?php echo esc_html($notice); ?></p></div><?php endif; ?>

    <form class="zpLeadsToolbar" method="get">
      <input type="hidden" name="page" value="zp-suite-leads">
      <label>Status<br><select name="lead_status"><option value="">Wszystkie</option><?php foreach($statuses as $k=>$v): ?><option value="<?php echo esc_attr($k); ?>" <?php selected($filter_status,$k); ?>><?php echo esc_html($v); ?></option><?php endforeach; ?></select></label>
      <label>Szukaj<br><input type="search" name="lead_q" value="<?php echo esc_attr($filter_q); ?>" placeholder="imię, telefon, e-mail, usługa..."></label>
      <button class="button button-primary">Filtruj</button>
      <a class="button" href="<?php echo esc_url(admin_url('admin.php?page=zp-suite-leads')); ?>">Wyczyść filtr</a>
      <a class="button" href="<?php echo esc_url(wp_nonce_url(admin_url('admin.php?page=zp-suite-leads&zp_leads_csv=1'), 'zp_suite_leads_csv')); ?>">Eksport CSV</a>
      <span style="flex:1"></span>
    </form>

    <div class="zpLeadsToolbar" style="justify-content:flex-end">
      <form method="post" onsubmit="return confirm('Usunąć zgłoszenia zamknięte oraz spam?')"><?php wp_nonce_field('zp_suite_leads_simple_action'); ?><button class="button" name="zp_leads_simple_action" value="clear_closed">Usuń zamknięte/spam</button></form>
      <form method="post" onsubmit="return confirm('Na pewno usunąć WSZYSTKIE zgłoszenia? Tej akcji nie da się cofnąć.')"><?php wp_nonce_field('zp_suite_leads_simple_action'); ?><button class="button zpLeadDelete" name="zp_leads_simple_action" value="clear_all">Wyczyść wszystkie</button></form>
    </div>

    <?php if (empty($leads)) : ?>
      <div class="zpLeadCard"><div class="zpLeadTop"><div class="zpLeadTitle"><h2>Brak zgłoszeń</h2><small>Nowe leady pojawią się tutaj automatycznie.</small></div></div></div>
    <?php else : ?>
      <div class="zpLeadList">
        <?php foreach ($leads as $lead) :
          $id = (string)($lead['id'] ?? '');
          $status = $lead['status'] ?? 'new';
          $name = $lead['name'] ?? ($lead['company'] ?? 'Bez nazwy');
          $email = $lead['email'] ?? '';
          $phone = $lead['phone'] ?? '';
          $message = $lead['message'] ?? ($lead['Brief'] ?? '');
          $source = $lead['source'] ?? 'Formularz';
          $created = $lead['created_at'] ?? '';
          $skip = ['id','created_at','updated_at','status','note','ip','user_agent','name','company','phone','email','message','files'];
        ?>
        <article class="zpLeadCard <?php echo $status === 'new' ? 'is-new' : ''; ?>">
          <div class="zpLeadTop">
            <div class="zpLeadTitle">
              <h2><?php echo esc_html($name); ?></h2>
              <small><?php echo esc_html($created); ?> · <?php echo esc_html($source); ?> · ID: <?php echo esc_html($id); ?></small>
            </div>
            <span class="zpLeadBadge"><?php echo esc_html($statuses[$status] ?? $status); ?></span>
          </div>
          <div class="zpLeadBody">
            <main>
              <div class="zpLeadContact">
                <div class="zpLeadContactBox"><span>Telefon</span><strong><?php echo esc_html($phone ?: '—'); ?></strong></div>
                <div class="zpLeadContactBox"><span>E-mail</span><strong><?php echo esc_html($email ?: '—'); ?></strong></div>
                <div class="zpLeadContactBox"><span>Firma / usługi</span><strong><?php echo esc_html(($lead['company'] ?? '') ?: ($lead['services'] ?? '—')); ?></strong></div>
              </div>

              <?php if ($message) : ?><p class="zpLeadMessage"><?php echo esc_html($message); ?></p><?php endif; ?>

              <div class="zpLeadDetails">
                <?php foreach ($lead as $k => $v) :
                  if (in_array($k, $skip, true) || $v === '' || $v === null || is_array($v)) { continue; }
                  $full = in_array($k, ['services','source','website','budget','deadline','contact_mode','callback_time','callback_topic'], true) ? '' : '';
                ?>
                  <div class="zpLeadDetail <?php echo esc_attr($full); ?>"><span><?php echo esc_html($k); ?></span><strong><?php echo nl2br(esc_html((string)$v)); ?></strong></div>
                <?php endforeach; ?>
                <?php if (!empty($lead['files']) && is_array($lead['files'])) : ?>
                  <div class="zpLeadDetail zpLeadDetail--full zpLeadFiles"><span>Załączniki</span><strong>
                    <?php foreach($lead['files'] as $file) : if(empty($file['url'])) continue; ?>
                      <a href="<?php echo esc_url($file['url']); ?>" target="_blank" rel="noopener"><?php echo esc_html($file['name'] ?? 'plik'); ?></a>
                    <?php endforeach; ?>
                  </strong></div>
                <?php endif; ?>
              </div>
            </main>

            <aside class="zpLeadSide">
              <div class="zpLeadActions">
                <?php if ($email) : ?><a class="button button-primary" href="mailto:<?php echo esc_attr($email); ?>">Odpisz</a><?php endif; ?>
                <?php if ($phone) : ?><a class="button" href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $phone)); ?>">Zadzwoń</a><?php endif; ?>
                <?php if (!empty($lead['website'])) : ?><a class="button" target="_blank" rel="noopener" href="<?php echo esc_url($lead['website']); ?>">Otwórz link</a><?php endif; ?>
              </div>

              <form method="post" class="zpLeadNote">
                <?php wp_nonce_field('zp_suite_leads_simple_action'); ?>
                <input type="hidden" name="lead_id" value="<?php echo esc_attr($id); ?>">
                <label><strong>Status</strong><br><select name="lead_status"><?php foreach($statuses as $k=>$v): ?><option value="<?php echo esc_attr($k); ?>" <?php selected($status,$k); ?>><?php echo esc_html($v); ?></option><?php endforeach; ?></select></label>
                <textarea name="lead_note" placeholder="Notatka wewnętrzna po rozmowie / wycenie..."><?php echo esc_textarea($lead['note'] ?? ''); ?></textarea>
                <p><button class="button button-primary" name="zp_leads_simple_action" value="save">Zapisz</button></p>
              </form>

              <form method="post" onsubmit="return confirm('Usunąć to zgłoszenie?')">
                <?php wp_nonce_field('zp_suite_leads_simple_action'); ?>
                <input type="hidden" name="lead_id" value="<?php echo esc_attr($id); ?>">
                <button class="button zpLeadDelete" name="zp_leads_simple_action" value="delete">Usuń zgłoszenie</button>
              </form>
            </aside>
          </div>
        </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
<?php }
