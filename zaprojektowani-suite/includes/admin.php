<?php
if (!defined('ABSPATH')) { exit; }

add_action('admin_menu', function () {
  // Badge: inquiries with status "Nowe" from the contact forms and Studio wyceny (includes/zapytania.php).
  $new = function_exists('zp_inbox_new_count') ? zp_inbox_new_count() : 0;
  $badge = $new > 0 ? ' <span class="awaiting-mod"><span class="pending-count">'.esc_html($new).'</span></span>' : '';
  add_menu_page('Zaprojektowani Suite','ZP Suite'.$badge,'manage_options','zp-suite','zp_suite_render_command_center_page','dashicons-art',58);
  add_submenu_page('zp-suite','Command Center','Command Center','manage_options','zp-suite','zp_suite_render_command_center_page');
  add_submenu_page('zp-suite','Strona główna CMS','Strona główna CMS','manage_options','zp-suite-home-cms','zp_suite_render_admin_page');
});

add_action('admin_enqueue_scripts', function($hook){
  // Media picker, drag-and-drop and tabs are used only by the home page CMS.
  if (($_GET['page'] ?? '') !== 'zp-suite-home-cms') return;
  wp_enqueue_media();
  wp_enqueue_script('jquery-ui-sortable');
  wp_add_inline_script('jquery', zp_suite_admin_js());
});


add_action('admin_head', function(){
  // Styles of the home page CMS screen (tabs, cards, lists); the other panel screens use admin-panel.php.
  if (($_GET['page'] ?? '') !== 'zp-suite-home-cms') return;
  echo '<style id="zp-suite-admin-css">'.zp_suite_admin_css().'</style>';
});

function zp_suite_admin_css(){ return <<<'CSS'
.zpSuiteAdmin{color:#06101e;font-family:Inter,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif}
.zpTabs{position:sticky;top:32px;z-index:20;display:flex;gap:8px;overflow:auto;margin:14px 0 18px;padding:12px;border-radius:22px;background:rgba(248,250,252,.94);box-shadow:0 14px 36px rgba(7,20,38,.045)}
.zpTabs button{flex:0 0 auto;min-height:40px;border:1px solid #d8dde6;background:#fff;border-radius:999px;padding:9px 16px;cursor:pointer;font-weight:800;color:#071426}
.zpTabs button:hover{background:#f4f7fb}
.zpTabs button.is-active{background:#071426;color:#fff;border-color:#071426}
.zpPanel{display:none}.zpPanel.is-active{display:block}
.zpGrid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px;align-items:start}
.zpGrid.zpGrid1{grid-template-columns:1fr}
.zpCard{background:#fff;border:1px solid #dfe4ec;border-radius:24px;padding:24px;margin-bottom:18px;box-shadow:0 14px 44px rgba(7,20,38,.045);min-width:0}
.zpCard h2{margin:0 0 14px;font-size:21px;letter-spacing:-.03em;color:#071426}
.zpCard p{color:#5a6574;line-height:1.55}
.zpCardNote{background:#f8fafc}.zpCardNote p{margin:0;color:#455165}
.zpField{display:block;margin:0 0 14px}
.zpField strong{display:block;margin:0 0 7px;font-size:12px;text-transform:uppercase;letter-spacing:.08em;color:#455165}
.zpField input,.zpField textarea,.zpField select{width:100%;max-width:100%;border-radius:12px;border:1px solid #d9e0eb;padding:10px 12px;background:#f8fafc}
.zpField textarea{min-height:92px;font-size:13px;line-height:1.5}
.zpField small{display:block;margin-top:6px;color:#7a8594;line-height:1.45}
.zpMediaRow{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:8px;align-items:end}
.zpPreviewImg{max-width:120px;max-height:70px;border-radius:10px;display:block;margin-top:8px;object-fit:contain;background:#f3f6fb;border:1px solid #e2e8f0}
.zpSwitchGrid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px}
.zpSwitchGrid.zpSwitchGrid1{grid-template-columns:1fr;margin-bottom:14px}
.zpSectionList{display:grid;gap:8px}
.zpSwitch{display:flex;align-items:center;gap:10px;border:1px solid #dfe4ec;border-radius:14px;background:#f8fafc;padding:11px 12px;font-weight:800}
.zpSwitch input{width:auto!important;margin:0}
.zpSwitch span small{display:block;margin-top:2px;font-weight:500;color:#6b7686;line-height:1.4}
.zpSwitch.is-fixed{background:#fff;color:#455165}
.zpFixed{display:inline-flex;align-items:center;justify-content:center;width:16px;height:16px;border-radius:4px;background:#e9eef6;color:#102a4f;font-size:11px;font-weight:900;flex:0 0 auto}
.zpToolsBar{display:flex;gap:8px;flex-wrap:wrap;margin:12px 0}
.zpRepeater{display:grid;gap:12px;margin-bottom:12px}
.zpRepeater .ui-sortable-placeholder{visibility:visible!important;min-height:58px;border:2px dashed #1c477a;border-radius:18px;background:#eef6ff}
.zpRepeatItem{position:relative;border:1px solid #dfe4ec;border-radius:22px;background:linear-gradient(180deg,#fff,#f8fafc);padding:18px;overflow:hidden}
.zpRepeatItem details{display:block}
.zpRepeatItem summary{list-style:none;cursor:pointer;display:flex;align-items:center;justify-content:space-between;gap:12px;margin:-18px -18px 14px;padding:14px 18px;background:#f8fafc;border-bottom:1px solid #e6ebf3}
.zpRepeatItem summary::-webkit-details-marker{display:none}
.zpRepeatItem:not(:has(details[open])){padding-bottom:0}
.zpRepeatItem details:not([open])>summary{border-bottom:0;margin-bottom:0}
.zpRepeatItem.is-hidden{background:#f6f7f9}.zpRepeatItem.is-hidden .zpRepeatThumb{opacity:.45}
.zpRepeatSummaryTitle{display:flex;align-items:center;gap:12px;min-width:0}
.zpRepeatThumb{width:54px;height:38px;border-radius:9px;object-fit:cover;background:#edf2f7;border:1px solid #dfe4ec;flex:0 0 auto}
.zpRepeatSummaryText{min-width:0}
.zpRepeatSummaryText strong{display:block;font-size:14px;color:#071426;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.zpRepeatSummaryText span{display:block;font-size:11px;color:#6d7688;margin-top:3px}
.zpRowOff{display:none;font-style:normal;color:#9a3412}.zpRepeatItem.is-hidden .zpRowOff{display:inline}
.zpRepeatChevron{font-size:18px;color:#102a4f;transition:transform .18s ease}
.zpRepeatItem details[open] .zpRepeatChevron{transform:rotate(90deg)}
.zpRepeatTop{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:12px}
.zpRowShow{display:inline-flex;align-items:center;gap:6px;font-weight:700;color:#071426}
.zpRepeatGrid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px}
.zpRepeatGrid .wide{grid-column:1/-1}
.zpRemoveRow{border-color:#f3c7c2!important;color:#b42318!important;background:#fff!important}
.zpAddRow{background:#071426!important;color:#fff!important;border-color:#071426!important}
.zpSave{position:sticky;bottom:0;z-index:30;display:flex;justify-content:flex-end;padding:14px 0;background:rgba(240,240,241,.92)}
.zpSave .button{min-height:46px;padding:0 26px;font-weight:800}
@media(max-width:1100px){.zpGrid,.zpRepeatGrid{grid-template-columns:1fr}}
@media(max-width:782px){.zpTabs{top:46px}.zpSwitchGrid{grid-template-columns:1fr}.zpMediaRow{grid-template-columns:1fr}.zpCard{padding:18px;border-radius:20px}}
CSS; }

function zp_suite_admin_js(){ return <<<'JS'
jQuery(function($){
  function openMedia(target){ var frame=wp.media({title:'Wybierz plik',multiple:false}); frame.on('select',function(){ var file=frame.state().get('selection').first().toJSON(); $(target).val(file.url).trigger('change'); }); frame.open(); }
  $(document).on('click','.zpPickMedia',function(e){ e.preventDefault(); openMedia($(this).data('target')); });
  $(document).on('click','.zpTabs button',function(){ var id=$(this).data('tab'); $('.zpTabs button').removeClass('is-active'); $(this).addClass('is-active'); $('.zpPanel').removeClass('is-active'); $('#'+id).addClass('is-active'); $('#zpCmsTab').val(id); if(history.replaceState){ history.replaceState(null,'','#'+id); } else { window.location.hash=id; } });
  if(window.location.hash && $(window.location.hash).length){ $('.zpTabs button[data-tab="'+window.location.hash.substring(1)+'"]').trigger('click'); }
  $(document).on('click','.zpAddRow',function(e){ e.preventDefault(); var rep=$($(this).data('repeater')); var tpl=$($(this).data('template')).html(); var index=Date.now()+''+Math.floor(Math.random()*999); tpl=tpl.replaceAll('__i__', index); rep.append(tpl); });
  $(document).on('click','.zpRemoveRow',function(e){ e.preventDefault(); if(confirm('Usunąć ten element? Zniknie ze strony po zapisaniu.')) $(this).closest('.zpRepeatItem').remove(); });
  $(document).on('change','.zpRowShow input[type=checkbox]',function(){ $(this).closest('.zpRepeatItem').toggleClass('is-hidden', !this.checked); });
  $(document).on('click','[data-zp-tab]',function(e){ e.preventDefault(); $('.zpTabs button[data-tab="'+$(this).data('zp-tab')+'"]').trigger('click'); var t=$('.zpTabs'); if(t.length){ window.scrollTo({top: Math.max(0, t.offset().top - 40), behavior: 'smooth'}); } });
  $('.zpRepeater').sortable({handle:'.zpDragHandle, summary', placeholder:'ui-sortable-placeholder', forcePlaceholderSize:true, tolerance:'pointer'});
  $(document).on('click','.zpExpandAll',function(e){e.preventDefault(); $($(this).data('target')).find('details').attr('open',true);});
  $(document).on('click','.zpCollapseAll',function(e){e.preventDefault(); $($(this).data('target')).find('details').removeAttr('open');});
  $(document).on('input change','.zpMediaInput',function(){ var v=$(this).val(); var img=$(this).closest('.zpField').find('.zpPreviewImg'); if(v){ if(!img.length) $(this).closest('.zpField').append('<img class="zpPreviewImg" alt="">'); $(this).closest('.zpField').find('.zpPreviewImg').attr('src',v); } });
});
JS; }


/* ----------------------------------------------------------------------------------------------
 * Strona główna CMS. Only fields that really change the site are on the screen (the rest of the stored
 * options stay as they are and keep feeding the site). Saving goes through admin-post.php and back.
 * ---------------------------------------------------------------------------------------------- */

add_action('admin_post_zp_suite_home_cms', 'zp_suite_save_admin');

function zp_suite_save_admin(){
  if (!current_user_can('manage_options')) { wp_die('Brak uprawnień.', 403); }
  check_admin_referer('zp_suite_save');

  // Only the posted fields are written; everything else keeps its stored or default value.
  $defaults = zp_suite_defaults();
  $opts = get_option('zp_suite_options', []);
  $opts = is_array($opts) ? $opts : [];
  $posted = isset($_POST['zp_opts']) && is_array($_POST['zp_opts']) ? wp_unslash($_POST['zp_opts']) : [];
  foreach ($posted as $section => $fields) {
    $section = sanitize_key($section);
    if (!isset($defaults[$section]) || !is_array($defaults[$section]) || !is_array($fields)) { continue; }
    if (!isset($opts[$section]) || !is_array($opts[$section])) { $opts[$section] = []; }
    foreach ($fields as $key => $val) {
      if (is_array($val)) { continue; }
      $key = sanitize_key($key);
      // The Turnstile secret is never printed back into the page; an empty field keeps the saved one.
      if ($section === 'security' && $key === 'turnstile_secret_key' && trim((string) $val) === '') { continue; }
      $opts[$section][$key] = wp_kses_post((string) $val);
    }
  }
  update_option('zp_suite_options', $opts, false);

  // Lists (logos, portfolio, reviews, FAQ) are stored exactly as on the screen, so a removed row stays removed.
  $lists = isset($_POST['zp_cms_lists']) && is_array($_POST['zp_cms_lists']) ? array_map('sanitize_key', wp_unslash($_POST['zp_cms_lists'])) : [];
  if ($lists) {
    $raw = isset($_POST['zp_cms']) && is_array($_POST['zp_cms']) ? wp_unslash($_POST['zp_cms']) : [];
    $cms = get_option('zp_suite_cms', []);
    $cms = is_array($cms) ? $cms : [];
    $exact = isset($cms['_exact']) && is_array($cms['_exact']) ? $cms['_exact'] : [];
    foreach (zp_suite_cms_list_paths() as $path) {
      if (!in_array(str_replace('.', '_', $path), $lists, true)) { continue; }
      zp_suite_cms_path_set($cms, $path, zp_suite_cms_clean_rows(zp_suite_cms_path_get($raw, $path)));
      $exact[$path] = '1';
    }
    $cms['_exact'] = $exact;
    update_option('zp_suite_cms', $cms, false);
  }

  $tab = sanitize_html_class((string) wp_unslash($_POST['tab'] ?? ''));
  wp_safe_redirect(add_query_arg(['page' => 'zp-suite-home-cms', 'zp_msg' => 'saved'], admin_url('admin.php')) . ($tab !== '' ? '#' . $tab : ''));
  exit;
}

/** Rows from the form: values cleaned like before; a row with nothing but its switches is dropped. */
function zp_suite_cms_clean_rows($rows){
  $clean = [];
  foreach ((array) $rows as $row) {
    if (!is_array($row)) { continue; }
    $c = [];
    foreach ($row as $k => $v) { if (!is_array($v)) { $c[sanitize_key($k)] = wp_kses_post((string) $v); } }
    $content = $c;
    unset($content['visible'], $content['featured']);
    if (array_filter($content, function ($x) { return trim((string) $x) !== ''; })) { $clean[] = $c; }
  }
  return $clean;
}

function zp_field($section,$key,$label,$type='text',$help=''){
  $v = zp_suite_opt($section.'.'.$key, '');
  $name = 'zp_opts['.esc_attr($section).']['.esc_attr($key).']';
  echo '<label class="zpField"><strong>'.esc_html($label).'</strong>';
  if($type==='textarea') echo '<textarea name="'.$name.'">'.esc_textarea($v).'</textarea>';
  elseif($type==='media') echo '<div class="zpMediaRow"><input class="zpMediaInput" type="text" name="'.$name.'" value="'.esc_attr($v).'"><button class="button zpPickMedia" data-target="input[name=&quot;'.$name.'&quot;]">Wybierz</button></div>'.($v?'<img class="zpPreviewImg" src="'.esc_url($v).'" alt="">':'');
  elseif($type==='number') echo '<input type="number" step="any" name="'.$name.'" value="'.esc_attr($v).'">';
  elseif($type==='email') echo '<input type="email" name="'.$name.'" value="'.esc_attr($v).'">';
  elseif($type==='secret') echo '<input type="password" autocomplete="new-password" name="'.$name.'" value="" placeholder="'.esc_attr($v !== '' ? 'Zapisany. Wpisz nowy, żeby zmienić.' : 'Wklej klucz').'">';
  else echo '<input type="text" name="'.$name.'" value="'.esc_attr($v).'">';
  if($help) echo '<small>'.esc_html($help).'</small>'; echo '</label>';
}

/** Switch that can also be turned off: the hidden "0" is sent when the box is not ticked. */
function zp_checkbox($section,$key,$label,$help=''){
  $v = zp_suite_opt($section.'.'.$key, '1');
  $name = 'zp_opts['.esc_attr($section).']['.esc_attr($key).']';
  echo '<label class="zpSwitch"><input type="hidden" name="'.$name.'" value="0"><input type="checkbox" name="'.$name.'" value="1" '.checked((string)$v,'1',false).'> <span>'.esc_html($label).($help ? '<small>'.esc_html($help).'</small>' : '').'</span></label>';
}

function zp_admin_input($name,$label,$value='',$type='text',$wide=false){
  $cls=$wide?'zpField wide':'zpField'; echo '<label class="'.$cls.'"><strong>'.esc_html($label).'</strong>';
  if($type==='textarea') echo '<textarea name="'.esc_attr($name).'">'.esc_textarea($value).'</textarea>';
  elseif($type==='media') echo '<div class="zpMediaRow"><input class="zpMediaInput" type="text" name="'.esc_attr($name).'" value="'.esc_attr($value).'"><button class="button zpPickMedia" data-target="input[name=&quot;'.esc_attr($name).'&quot;]">Wybierz</button></div>'.($value?'<img class="zpPreviewImg" src="'.esc_url($value).'" alt="">':'');
  elseif($type==='check') echo '<span class="zpSwitch"><input type="hidden" name="'.esc_attr($name).'" value="0"><input type="checkbox" name="'.esc_attr($name).'" value="1" '.checked((string)$value,'1',false).'> <span>Tak</span></span>';
  else echo '<input type="text" name="'.esc_attr($name).'" value="'.esc_attr($value).'">';
  echo '</label>';
}

/**
 * One list row: a collapsible card with "Pokaż na stronie", the given fields and hidden copies of the row's
 * other keys (so nothing the site reads is lost when rows are moved or saved).
 * $fields: [key, label, type (text|textarea|media|check), wide].
 */
function zp_cms_row($base, $i, array $item, $title, $sub, $thumb, array $fields){
  $p = $base.'['.$i.']';
  $shown = (string)($item['visible'] ?? '1') !== '0';
  ob_start(); ?>
  <div class="zpRepeatItem<?php echo $shown ? '' : ' is-hidden'; ?>">
    <details<?php echo $i === '__i__' ? ' open' : ''; ?>>
      <summary>
        <span class="zpRepeatSummaryTitle">
          <?php if($thumb): ?><img class="zpRepeatThumb" src="<?php echo esc_url($thumb); ?>" alt=""><?php endif; ?>
          <span class="zpRepeatSummaryText"><strong><?php echo esc_html($title); ?></strong><span><?php echo esc_html($sub); ?><em class="zpRowOff"> · ukryte na stronie</em></span></span>
        </span>
        <span class="zpRepeatChevron">›</span>
      </summary>
      <div class="zpRepeatTop">
        <label class="zpRowShow"><input type="hidden" name="<?php echo esc_attr($p.'[visible]'); ?>" value="0"><input type="checkbox" name="<?php echo esc_attr($p.'[visible]'); ?>" value="1" <?php checked($shown); ?>> Pokaż na stronie</label>
        <button class="button zpRemoveRow">Usuń</button>
      </div>
      <div class="zpRepeatGrid">
        <?php foreach($fields as $f){ zp_admin_input($p.'['.$f[0].']', $f[1], (string)($item[$f[0]] ?? ($f[2] === 'check' ? '0' : '')), $f[2], !empty($f[3])); } ?>
      </div>
      <?php
      $known = array_merge(['visible'], array_map(function($f){ return $f[0]; }, $fields));
      foreach($item as $k=>$v){ if(!in_array($k,$known,true) && !is_array($v)) echo '<input type="hidden" name="'.esc_attr($p.'['.$k.']').'" value="'.esc_attr((string)$v).'">'; }
      ?>
    </details>
  </div>
<?php return ob_get_clean(); }

function zp_portfolio_row($mode,$i,$item=[]){
  return zp_cms_row("zp_cms[portfolio][$mode]", $i, $item, $item['brand'] ?? 'Nowy projekt', ($mode === 'web' ? 'Strona / sklep' : 'Logo / branding').(!empty($item['sub']) ? ' · '.$item['sub'] : ''), $item['img'] ?? '', [
    ['brand','Nazwa projektu','text'], ['sub','Krótki opis pod nazwą','text'], ['type','Rodzaj (etykieta)','text'],
    ['year','Rok','text'], ['tag','Branża','text'], ['live','Link do strony','text'],
    ['img','Zdjęcie / mockup','media',true],
    ['client','Klient','text'], ['services','Usługi','text'], ['meta','Tagi na karcie (oddziel |)','text',true],
    ['desc','Opis w oknie szczegółów','textarea',true], ['scope','Zakres prac (oddziel |)','textarea',true],
  ]);
}
function zp_review_row($i,$item=[]){
  return zp_cms_row('zp_cms[reviews]', $i, $item, $item['name'] ?? 'Nowa opinia', trim(($item['source'] ?? 'Google').' '.($item['rating'] ?? '')).(!empty($item['featured']) && (string)$item['featured']==='1' ? ' · wyróżniona' : ''), '', [
    ['name','Imię / nazwa','text'], ['source','Źródło (Google albo Facebook)','text'], ['rating','Ocena','text'],
    ['date','Kiedy (np. 2 miesiące temu)','text'], ['avatar','Inicjały','text'], ['featured','Wyróżniona (duży cytat)','check'],
    ['text','Treść opinii','textarea',true],
  ]);
}
function zp_logo_row($i,$item=[]){
  return zp_cms_row('zp_cms[logos]', $i, $item, $item['name'] ?? 'Nowe logo', 'Logo klienta', $item['image'] ?? '', [
    ['name','Nazwa','text'], ['url','Link (opcjonalnie)','text'], ['image','Plik logo','media',true],
  ]);
}
function zp_faq_row($i,$item=[]){
  return zp_cms_row('zp_cms[faq]', $i, $item, $item['q'] ?? 'Nowe pytanie', 'Pytanie', '', [
    ['q','Pytanie','text',true], ['a','Odpowiedź','textarea',true],
  ]);
}

/** Sections of the home page in their real order, with plain names. */
function zp_suite_home_cms_sections(){
  $names = [
    'home_hero' => ['Pierwszy ekran (hero)', 'Nagłówek, film i przyciski są w kodzie strony.'],
    'trust_logos' => ['Logotypy klientów', 'Treść w zakładce Logotypy.'],
    'services_path' => ['Zakres usług', ''],
    'featured_packages' => ['Polecane pakiety', 'Trzy pakiety z linkami do Studio wyceny.'],
    'quick_contact' => ['Krótki formularz kontaktowy', ''],
    'showcase_portfolio' => ['Portfolio', 'Projekty z zakładki Portfolio.'],
    'laptop_showcase' => ['Pokaz strony na laptopie', ''],
    'about_experience' => ['O Zaprojektowani', 'Zdjęcie i liczby z zakładki Liczby i zdjęcie.'],
    'showcase_services' => ['Usługi i branże', ''],
    'reviews_section' => ['Opinie klientów', 'Treść w zakładce Opinie.'],
    'seo_faq' => ['Najczęstsze pytania (FAQ)', 'Treść w zakładce FAQ.'],
    'seo_industries' => ['Strony dla branż', 'Kafelki z linkami do stron branżowych są w kodzie.'],
    'home_audit_cta' => ['Zaproszenie na mini-audyt', ''],
    'contact_system' => ['Formularz kontaktowy', 'Ten sam formularz co na stronie Kontakt.'],
    'seo_intro' => ['Tekst o firmie nad stopką', 'Tekst pod SEO jest w kodzie strony.'],
  ];
  $map = function_exists('zp_suite_home_sections_map') ? zp_suite_home_sections_map() : [];
  $order = function_exists('zp_suite_home_order') ? zp_suite_home_order() : array_keys($map);
  $out = [];
  foreach ($order as $key) {
    if (empty($map[$key])) { continue; }
    $out[$key] = ['name' => $names[$key][0] ?? $map[$key]['label'], 'help' => $names[$key][1] ?? '', 'switch' => $map[$key]['visibility'] ?? null];
  }
  return $out;
}

function zp_suite_render_admin_page(){
  if (!current_user_can('manage_options')) { return; }
  $cms = zp_suite_cms();
  $tabs = ['zpTabSections' => 'Sekcje', 'zpTabLogos' => 'Logotypy', 'zpTabPortfolio' => 'Portfolio', 'zpTabReviews' => 'Opinie', 'zpTabFaq' => 'FAQ', 'zpTabStats' => 'Liczby i zdjęcie', 'zpTabHeader' => 'Menu i stopka', 'zpTabContact' => 'Formularze i e-maile', 'zpTabPerformance' => 'Wydajność'];
  $on = 0; $all = 0;
  foreach (zp_suite_home_cms_sections() as $s) { $all++; if (!$s['switch'] || zp_suite_section_enabled($s['switch'])) { $on++; } }
  ?>
  <div class="wrap zpx zpSuiteAdmin">
    <header class="zpxHead">
      <span class="zpxKicker">ZP Suite</span>
      <h1>Strona główna CMS</h1>
      <p>Treści i ustawienia, które naprawdę widać na stronie: sekcje strony głównej, logotypy, portfolio, opinie, FAQ, liczby, menu i stopka oraz formularze. Wygląd i układ strony zostają bez zmian.</p>
      <div class="zpxStats">
        <a class="zpxStat" href="#zpTabSections" data-zp-tab="zpTabSections"><strong><?php echo (int) $on; ?>/<?php echo (int) $all; ?></strong><span>sekcji na stronie</span></a>
        <a class="zpxStat" href="#zpTabPortfolio" data-zp-tab="zpTabPortfolio"><strong><?php echo (int) count($cms['portfolio']['web'] ?? []) + count($cms['portfolio']['logo'] ?? []); ?></strong><span>projekty w portfolio</span></a>
        <a class="zpxStat" href="#zpTabReviews" data-zp-tab="zpTabReviews"><strong><?php echo (int) count($cms['reviews'] ?? []); ?></strong><span>opinie</span></a>
        <a class="zpxStat" href="<?php echo esc_url(home_url('/')); ?>" target="_blank" rel="noopener"><strong>↗</strong><span>otwórz stronę</span></a>
      </div>
    </header>
    <hr class="wp-header-end">
    <?php zp_panel_notice(); ?>

    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
      <input type="hidden" name="action" value="zp_suite_home_cms">
      <input type="hidden" name="tab" id="zpCmsTab" value="zpTabSections">
      <?php wp_nonce_field('zp_suite_save'); ?>
      <div class="zpTabs"><?php $first = true; foreach ($tabs as $id => $label) { echo '<button type="button" class="'.($first ? 'is-active' : '').'" data-tab="'.esc_attr($id).'">'.esc_html($label).'</button>'; $first = false; } ?></div>

      <section id="zpTabSections" class="zpPanel is-active"><div class="zpGrid">
        <div class="zpCard"><h2>Sekcje strony głównej</h2><p>W kolejności jak na stronie. Odznacz sekcję, żeby ją ukryć; reszta zostaje na swoich miejscach.</p>
          <div class="zpSectionList">
            <?php foreach (zp_suite_home_cms_sections() as $key => $s) : ?>
              <?php if ($s['switch']) { zp_checkbox('visibility', $s['switch'], $s['name'], $s['help']); } else { ?>
                <div class="zpSwitch is-fixed"><span class="zpFixed">✓</span><span><?php echo esc_html($s['name']); ?><small><?php echo esc_html(trim($s['help'].' Zawsze widoczna.')); ?></small></span></div>
              <?php } ?>
            <?php endforeach; ?>
          </div>
        </div>
        <div class="zpCard"><h2>Na podstronach usług</h2><?php zp_checkbox('visibility','quick_contact_pages','Krótki formularz kontaktowy na stronach usług i kampanii','Na przykład na /tworzenie-stron-internetowych/ i stronach Katowice.'); ?>
          <p>Kolejność sekcji jest ustawiona w kodzie strony, tak jak treść pierwszego ekranu, tekstu nad stopką i stron dla branż.</p>
        </div>
      </div></section>

      <section id="zpTabLogos" class="zpPanel"><div class="zpGrid">
        <div class="zpCard"><h2>Nagłówek sekcji logotypów</h2><?php zp_field('trust_logos','eyebrow','Mały napis nad nagłówkiem'); zp_field('trust_logos','heading','Nagłówek','text','Można użyć <span>…</span>, żeby wyróżnić słowa kolorem.'); zp_field('trust_logos','lead','Opis','textarea'); ?></div>
        <div class="zpCard"><h2>Logotypy klientów</h2><p>Przeciągnij, żeby zmienić kolejność.</p><input type="hidden" name="zp_cms_lists[]" value="logos"><div class="zpToolsBar"><button class="button zpExpandAll" data-target="#zpLogosRep">Rozwiń</button><button class="button zpCollapseAll" data-target="#zpLogosRep">Zwiń</button></div><div id="zpLogosRep" class="zpRepeater"><?php foreach(($cms['logos'] ?? []) as $i=>$row) echo zp_logo_row($i,(array)$row); ?></div><button class="button zpAddRow" data-repeater="#zpLogosRep" data-template="#zpTplLogo">Dodaj logo</button></div>
      </div></section>

      <section id="zpTabPortfolio" class="zpPanel"><div class="zpGrid zpGrid1">
        <div class="zpCard zpCardNote"><p>Pięć projektów stron (OutTech, Polerstone, Świat Grilli, ProScarves, Siemianowski) i dwa projekty logo (Natalia Arciszewska, Zgórecki Nieruchomości) są wpisane w kod i zawsze idą pierwsze, więc nie ma ich na tej liście. Gdy projekt ma też opis w Realizacjach, opis, klient i zakres prac biorą się stamtąd; tutaj zmienisz nazwę, zdjęcie, link, rok, tagi i widoczność.</p></div>
      </div><div class="zpGrid">
        <div class="zpCard"><h2>Strony i sklepy</h2><input type="hidden" name="zp_cms_lists[]" value="portfolio_web"><div class="zpToolsBar"><button class="button zpExpandAll" data-target="#zpPortWeb">Rozwiń wszystko</button><button class="button zpCollapseAll" data-target="#zpPortWeb">Zwiń wszystko</button></div><div id="zpPortWeb" class="zpRepeater"><?php foreach(($cms['portfolio']['web'] ?? []) as $i=>$row) echo zp_portfolio_row('web',$i,(array)$row); ?></div><button class="button zpAddRow" data-repeater="#zpPortWeb" data-template="#zpTplPortfolioWeb">Dodaj projekt strony</button></div>
        <div class="zpCard"><h2>Logo i branding</h2><input type="hidden" name="zp_cms_lists[]" value="portfolio_logo"><div class="zpToolsBar"><button class="button zpExpandAll" data-target="#zpPortLogo">Rozwiń wszystko</button><button class="button zpCollapseAll" data-target="#zpPortLogo">Zwiń wszystko</button></div><div id="zpPortLogo" class="zpRepeater"><?php foreach(($cms['portfolio']['logo'] ?? []) as $i=>$row) echo zp_portfolio_row('logo',$i,(array)$row); ?></div><button class="button zpAddRow" data-repeater="#zpPortLogo" data-template="#zpTplPortfolioLogo">Dodaj projekt logo</button></div>
      </div></section>

      <section id="zpTabReviews" class="zpPanel"><div class="zpGrid zpGrid1"><div class="zpCard"><h2>Opinie klientów</h2><p>Pierwsza opinia oznaczona jako wyróżniona pokazuje się jako duży cytat, pozostałe w przewijanym pasku. Źródło Google albo Facebook dobiera ikonę.</p><input type="hidden" name="zp_cms_lists[]" value="reviews"><div class="zpToolsBar"><button class="button zpExpandAll" data-target="#zpReviewsRep">Rozwiń</button><button class="button zpCollapseAll" data-target="#zpReviewsRep">Zwiń</button></div><div id="zpReviewsRep" class="zpRepeater"><?php foreach(($cms['reviews'] ?? []) as $i=>$row) echo zp_review_row($i,(array)$row); ?></div><button class="button zpAddRow" data-repeater="#zpReviewsRep" data-template="#zpTplReview">Dodaj opinię</button></div></div></section>

      <section id="zpTabFaq" class="zpPanel"><div class="zpGrid zpGrid1"><div class="zpCard"><h2>Najczęstsze pytania na stronie głównej</h2><p>Strona pokazuje najpierw 12 stałych pytań przygotowanych pod Google i asystentów AI, a pod nimi Twoje pytania z tej listy. Pytania dodane kiedyś przez wtyczkę są zastąpione tymi 12, więc ich zmiana tutaj nic nie da; dopisz nowe pytanie na końcu.</p><input type="hidden" name="zp_cms_lists[]" value="faq"><div id="zpFaqRep" class="zpRepeater"><?php foreach(($cms['faq'] ?? []) as $i=>$row) echo zp_faq_row($i,(array)$row); ?></div><button class="button zpAddRow" data-repeater="#zpFaqRep" data-template="#zpTplFaq">Dodaj pytanie</button></div></div></section>

      <section id="zpTabStats" class="zpPanel"><div class="zpGrid">
        <div class="zpCard"><h2>Liczby</h2><?php zp_field('stats','projects_count','Liczba realizacji','number','Pierwszy ekran, sekcja logotypów, O Zaprojektowani, stopka na każdej stronie i strony usług.'); zp_field('stats','google_rating','Ocena Google','text','Sekcja logotypów i stopka.'); zp_field('stats','recommendations','Liczba poleceń','number','Sekcja logotypów.'); ?></div>
        <div class="zpCard"><h2>Zdjęcie w sekcji O Zaprojektowani</h2><?php zp_field('about','team_image','Zdjęcie zespołu','media'); zp_field('about','team_width','Szerokość na komputerze (px)','number'); zp_field('about','team_top','Przesunięcie w pionie (px)','number','Liczba ujemna podnosi zdjęcie.'); zp_field('about','team_right','Przesunięcie w poziomie (px)','number'); zp_field('about','team_scale','Powiększenie (np. 1.18)'); zp_field('about','team_grayscale','Czarno-białe: 1 tak, 0 nie','number'); zp_field('about','team_opacity','Krycie od 0 do 1'); zp_field('about','cards_top','Karty pod zdjęciem: przesunięcie w pionie (px)','number'); ?></div>
      </div></section>

      <section id="zpTabHeader" class="zpPanel"><div class="zpGrid">
        <div class="zpCard"><h2>Menu na górze strony</h2><?php zp_field('header','cta_text','Tekst przycisku w menu'); zp_field('header','cta_url','Link przycisku w menu'); zp_field('header','logo_desktop_h','Wysokość logo na komputerze (px)','number','Na telefonie logo ma stałe 44 px.'); zp_field('header','sticky_white_strength','Biel menu po przewinięciu, od 1 do 100','number','Tylko na komputerze. 1 to prawie przezroczyste, 100 to pełna biel.'); zp_field('header','header_line_width','Szerokość linii pod menu (px)','number','Tylko na komputerze, po przewinięciu i na podstronach.'); zp_field('brand','logo_light','Ikona w karcie przeglądarki (favicon)','media'); ?></div>
        <div class="zpCard"><h2>Stopka na każdej stronie</h2><?php zp_field('footer','cta_eyebrow','Mały napis nad nagłówkiem'); zp_field('footer','cta_heading','Nagłówek','textarea'); zp_field('footer','cta_lead','Opis','textarea'); zp_field('footer','team_photo','Zdjęcie zespołu','media'); zp_field('footer','cta_primary_text','Tekst głównego przycisku'); zp_field('footer','cta_primary_url','Link głównego przycisku'); zp_field('footer','cta_secondary_text','Tekst przycisku WhatsApp'); zp_field('brand','whatsapp','Numer WhatsApp','text','Przycisk WhatsApp w stopce.'); zp_field('footer','about','Opis firmy w stopce','textarea'); ?></div>
      </div></section>

      <section id="zpTabContact" class="zpPanel"><div class="zpGrid">
        <div class="zpCard"><h2>Dokąd idą zapytania</h2><?php zp_field('brand','admin_email','Adres, na który przychodzą zapytania','email','Kopia zawsze idzie też na kontakt@zaprojektowani.com. Zapytania widać też w ZP Suite → Zapytania.'); zp_field('brand','from_email','Adres nadawcy e-maili','email'); zp_field('brand','from_name','Nazwa nadawcy e-maili'); zp_field('brand','logo_dark','Logo w e-mailach','media'); ?></div>
        <div class="zpCard"><h2>E-maile z formularzy</h2><?php zp_field('email_templates','admin_subject_phone','Temat e-maila o prośbie o telefon'); zp_field('email_templates','client_subject','Temat potwierdzenia dla klienta'); zp_field('email_templates','client_intro','Początek potwierdzenia dla klienta','textarea'); zp_field('email_templates','email_footer','Stopka e-maili'); ?><p>Dotyczy formularzy kontaktowych. Studio wyceny wysyła własne e-maile.</p></div>
        <div class="zpCard"><h2>Komunikat po wysłaniu formularza</h2><?php zp_field('contact','success_title','Tytuł, gdy się udało'); zp_field('contact','success_text','Treść, gdy się udało','textarea','Pełny formularz i szybki kontakt. Prośba o telefon i krótki formularz mają własne teksty.'); zp_field('contact','error_title','Tytuł, gdy czegoś brakuje'); ?></div>
        <div class="zpCard"><h2>Ochrona przed spamem</h2><div class="zpSwitchGrid zpSwitchGrid1"><?php zp_checkbox('security','turnstile_enabled','Cloudflare Turnstile (niewidoczna weryfikacja)'); zp_checkbox('security','turnstile_fail_open','Przepuszczaj, gdy Cloudflare chwilowo nie odpowiada'); zp_checkbox('security','honeypot_enabled','Ukryte pole na boty'); zp_checkbox('security','min_seconds_enabled','Blokuj wysyłkę szybszą niż minimum sekund'); ?></div><?php zp_field('security','min_seconds','Minimum sekund przed wysłaniem','number'); zp_field('security','turnstile_site_key','Turnstile: klucz witryny'); zp_field('security','turnstile_secret_key','Turnstile: klucz tajny','secret','Ze względów bezpieczeństwa nie pokazujemy zapisanego klucza.'); ?></div>
      </div></section>

      <section id="zpTabPerformance" class="zpPanel"><div class="zpGrid">
        <?php
        $perf = [
          'Strona główna' => ['home_lite_enabled', 'home_lite_mobile_enabled', 'disable_blur', 'reduce_reveals', 'reduce_shadows_watermarks', 'disable_mobile_heavy_motion', 'respect_reduced_motion'],
          'Strony internetowe Katowice' => ['katowice_lite_enabled', 'katowice_lite_mobile_enabled', 'katowice_disable_blur', 'katowice_reduce_reveals', 'katowice_reduce_shadows_watermarks', 'katowice_disable_mobile_heavy_motion', 'katowice_respect_reduced_motion'],
          'Sklepy internetowe Katowice' => ['shop_katowice_lite_enabled', 'shop_katowice_lite_mobile_enabled', 'shop_katowice_disable_blur', 'shop_katowice_reduce_reveals', 'shop_katowice_reduce_shadows_watermarks', 'shop_katowice_disable_mobile_heavy_motion', 'shop_katowice_respect_reduced_motion'],
        ];
        $labels = ['Tryb lekki włączony', 'Mocniejsze odciążenie na telefonie', 'Mniej rozmyć (blur)', 'Prostsze pojawianie się sekcji', 'Lżejsze cienie i znaki wodne', 'Bez ciężkich animacji na telefonie', 'Szanuj ustawienie „ogranicz ruch” w telefonie'];
        foreach ($perf as $title => $keys) {
          echo '<div class="zpCard"><h2>'.esc_html($title).'</h2><div class="zpSwitchGrid zpSwitchGrid1">';
          foreach ($keys as $n => $key) { zp_checkbox('performance', $key, $labels[$n]); }
          echo '</div></div>';
        }
        ?>
        <div class="zpCard"><h2>Co to robi</h2><p>Tryb lekki zdejmuje z tych stron najcięższe efekty: rozmycia, długie animacje wejścia i ruchome dekoracje na telefonach. Układ, teksty, slidery, linki i formularze zostają bez zmian. Szybkość całej strony ustawia osobny moduł: ZP Suite → Przyspieszenie.</p></div>
      </div></section>

      <div class="zpSave"><button class="button button-primary button-large">Zapisz zmiany</button></div>
    </form>
    <template id="zpTplLogo"><?php echo zp_logo_row('__i__', []); ?></template><template id="zpTplPortfolioWeb"><?php echo zp_portfolio_row('web','__i__', []); ?></template><template id="zpTplPortfolioLogo"><?php echo zp_portfolio_row('logo','__i__', []); ?></template><template id="zpTplReview"><?php echo zp_review_row('__i__', []); ?></template><template id="zpTplFaq"><?php echo zp_faq_row('__i__', []); ?></template>
  </div>
<?php }
