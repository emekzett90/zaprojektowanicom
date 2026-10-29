<?php
/** Approved Suite workspace. Presentation and read-only summaries; native providers own all work. */
if (!defined('ABSPATH')) { exit; }

function zp_astra_preferences(): array {
    $v = get_user_meta(get_current_user_id(), 'zp_astra_preferences_20261013', true);
    $v = is_array($v) ? $v : [];
    return ['density' => ($v['density'] ?? '') === 'compact' ? 'compact' : 'comfortable', 'live' => !isset($v['live']) || $v['live'] === true];
}
function zp_astra_translator_page(): string { return class_exists('ZPT2_Admin') ? 'zp-translator-v2' : 'zp-tlumacz-en'; }
function zp_astra_icon(string $name): string {
    $icons = ['dashboard'=>'dashboard','content'=>'media-document','visibility'=>'chart-line','inquiries'=>'email-alt','settings'=>'admin-generic','calendar'=>'calendar-alt','photos'=>'format-image','translations'=>'translation','health'=>'heart','arrow'=>'arrow-up-alt2','search'=>'search','menu'=>'menu','refresh'=>'update','history'=>'backup','budget'=>'money-alt','check'=>'yes-alt'];
    return '<span class="dashicons dashicons-' . esc_attr($icons[$name] ?? 'admin-generic') . '" aria-hidden="true"></span>';
}
function zp_astra_groups(): array {
    return [
        'dashboard'=>['Pulpit','zp-suite',['dashboard'=>['Pulpit','zp-suite']]],
        'content'=>['Treści','zp-suite-prepare',['prepare'=>['Artykuły','zp-suite-prepare'],'calendar'=>['Kalendarz','zp-suite-calendar'],'articlephotos'=>['Zdjęcia','zp-suite-article-photos'],'translations'=>['Tłumaczenia EN',zp_astra_translator_page()]]],
        'visibility'=>['Widoczność','zp-suite-stats',['stats'=>['Statystyki','zp-suite-stats'],'positions'=>['Pozycje i zmiany','zp-suite-positions'],'plan'=>['Plan SEO','zp-suite-seo-plan'],'ai'=>['Widoczność AI','zp-suite-ai']]],
        'inquiries'=>['Zapytania','zp-suite-zapytania',['inquiries'=>['Skrzynka zapytań','zp-suite-zapytania']]],
        'settings'=>['Ustawienia','zp-suite-settings',['settings'=>['Ustawienia','zp-suite-settings'],'performance'=>['Wydajność','zp-suite-performance'],'home'=>['Strona główna CMS','zp-suite-home-cms']]],
    ];
}
function zp_astra_open(string $title, string $active='dashboard', string $subtitle=''): void {
    if (!current_user_can('manage_options')) { return; }
    // 3.0.0: every Suite screen uses the flat frame with the section bar (includes/suite3/admin-ui.php).
    if (function_exists('zp_suite3_open')) { zp_suite3_open($title, $subtitle); return; }
    if ($active==='prepare' && ($_GET['page'] ?? '')==='zp-suite-article-photos') { $active='articlephotos'; }
    if (($_GET['page'] ?? '')==='zp-suite-home-cms') { $active='home'; }
    $groups=zp_astra_groups(); $group='dashboard';
    foreach ($groups as $key=>$item) { if (isset($item[2][$active])) { $group=$key; break; } }
    $workspace=function_exists('zp_suite_workspace_active') && zp_suite_workspace_active();
    $prefs=zp_astra_preferences(); $logo=ZP_SUITE_URL.'assets/campaigns/zp-sygnet-ciemny.webp';
    echo '<div class="wrap zpAdmin20 zpA app" data-workspace="'.($workspace?'1':'0').'" data-density="'.esc_attr($prefs['density']).'" data-live="'.($prefs['live']?'1':'0').'"><a class="skip" href="#zp-astra-main">Przejdź do treści</a><aside class="sidebar" id="zp-astra-sidebar"><a class="brand" href="'.esc_url(zp_admin20_url('zp-suite')).'"><img src="'.esc_url($logo).'" alt="Zaprojektowani" width="47" height="45"><div><strong>ZP Suite</strong><span>przestrzeń pracy</span></div></a><div class="nav-caption">TWOJA STRONA</div><nav class="nav" aria-label="ZP Suite">';
    foreach ($groups as $key=>$item) { echo '<a href="'.esc_url(zp_admin20_url($item[1])).'"'.($key===$group?' class="active" aria-current="'.($active===$key?'page':'true').'"':'').'>'.zp_astra_icon($key).'<span>'.esc_html($item[0]).'</span></a>'; }
    echo '</nav><div class="side-bottom"><a class="side-site" href="'.esc_url(home_url('/')).'" target="_blank" rel="noopener">'.zp_astra_icon('arrow').'Zobacz stronę</a><p class="side-note">'.esc_html((string) wp_parse_url(home_url('/'),PHP_URL_HOST)).'<br>Wszystko na swoim miejscu.</p></div></aside><header class="topbar"><div class="breadcrumb">Zaprojektowani <span aria-hidden="true">›</span> <b>'.esc_html($groups[$group][0]).'</b></div><div class="mobile-brand"><img src="'.esc_url($logo).'" alt="Zaprojektowani" width="34" height="34"><span>ZP Suite</span></div><div class="toplinks"><button type="button" class="search" data-zp-command-open>'.zp_astra_icon('search').'<span>Znajdź moduł lub ustawienie</span><kbd>⌘K</kbd></button><a href="'.esc_url(home_url('/')).'" target="_blank" rel="noopener">Zobacz stronę ↗</a><span class="tag">'.esc_html(defined('ZP_SUITE_VERSION')?ZP_SUITE_VERSION:'ZP Suite').'</span></div><button type="button" class="menu" aria-label="Otwórz menu" aria-expanded="false" aria-controls="zp-astra-sidebar">'.zp_astra_icon('menu').'</button></header><main class="main" id="zp-astra-main"><div class="intro"><div><div class="eyebrow">'.esc_html($groups[$group][0]).' / ZAPROJEKTOWANI</div><h1>'.esc_html($title).'</h1>'.($subtitle!==''?'<p>'.esc_html($subtitle).'</p>':'').'</div></div>';
    if (count($groups[$group][2])>1) {
        echo '<nav class="subnav" aria-label="'.esc_attr($groups[$group][0]).'">';
        foreach ($groups[$group][2] as $key=>$item) { echo '<a href="'.esc_url(zp_admin20_url($item[1])).'"'.($key===$active?' class="active" aria-current="page"':'').'>'.esc_html($item[0]).'</a>'; }
        echo '</nav>';
    }
    echo '<hr class="wp-header-end"><dialog class="zpA-command" aria-labelledby="zpA-command-title"><div class="zpA-command-head"><h2 id="zpA-command-title">Przejdź do modułu</h2><button type="button" class="button" data-zp-command-close aria-label="Zamknij wyszukiwanie">×</button></div><label class="screen-reader-text" for="zpA-command-search">Znajdź moduł</label><input id="zpA-command-search" type="search" placeholder="Wpisz nazwę modułu…"><nav aria-label="Wyniki wyszukiwania">';
    foreach ($groups as $item) { foreach ($item[2] as $module) { echo '<a href="'.esc_url(zp_admin20_url($module[1])).'">'.esc_html($module[0]).'<small>'.esc_html($item[0]).'</small></a>'; } }
    echo '</nav><p data-zp-command-empty hidden>Brak pasujących modułów.</p></dialog>';
}
function zp_astra_close(): void {
    if (function_exists('zp_suite3_close')) { zp_suite3_close(); return; }
    do_action('zp_admin20_close');
    echo '<footer class="footer"><span>ZP Suite · '.esc_html((string) wp_parse_url(home_url('/'),PHP_URL_HOST)).'</span><span>Dane i harmonogram w Twoim WordPressie.</span></footer></main></div>';
}
function zp_astra_translator_status(): array {
    if (!class_exists('ZPT2_Engine') || !ZPT2_Engine::ready()) { return []; }
    try { return ZPT2_Engine::status(); } catch (Throwable $e) { return []; }
}
function zp_astra_tag(string $label,string $color=''): string { return '<span class="tag '.esc_attr(in_array($color,['blue','green','amber'],true)?$color:'').'">'.esc_html($label).'</span>'; }
function zp_astra_link(string $label,string $page,string $class='button',array $extra=[]): string { return '<a class="'.esc_attr($class).'" href="'.esc_url(zp_admin20_url($page,$extra)).'">'.esc_html($label).' ↗</a>'; }
function zp_astra_action(string $title,string $description,string $page,string $icon='health',array $extra=[]): string {
    return '<a class="action-row" href="'.esc_url(zp_admin20_url($page,$extra)).'"><span class="mini-icon">'.zp_astra_icon($icon).'</span><div class="body"><h3>'.esc_html($title).'</h3><p>'.esc_html($description).'</p></div><span class="row-arrow" aria-hidden="true">↗</span></a>';
}
function zp_astra_counts(array $status): array {
    $counts=['new'=>0,'changed'=>0,'published'=>0,'active'=>0,'attention'=>0,'total'=>0];
    foreach (($status['rows'] ?? []) as $row) { if (!is_array($row)) { continue; } $state=(string)($row['state']??'unknown'); $counts['total']++;
        if (in_array($state,['new','changed'],true)) { $counts[$state]++; }
        elseif (in_array($state,['published','legacy'],true)) { $counts['published']++; }
        elseif (in_array($state,['queued','translating','repairing','checking','deferred'],true)) { $counts['active']++; }
        else { $counts['attention']++; }
    }
    return $counts;
}
function zp_astra_summary(): array {
    $data=zp_admin20_stats(); $translation=zp_astra_translator_status();
    $queues=class_exists('ZP_Admin_Queue_Health_20261010') ? ZP_Admin_Queue_Health_20261010::data() : [];
    $counts=zp_astra_counts($translation); $todo=[]; $review=0; $known=false;
    foreach (($queues['queues'] ?? []) as $key=>$queue) { if (!is_array($queue)) { continue; }
        if (is_array($queue['counts'] ?? null)) { $known=true; foreach (['ready','needs_retry','needs_attention','publish_attention','policy_hold','schedule_attention'] as $st) { $review+=(int)($queue['counts'][$st]??0); } }
        if (!empty($queue['issues'])) { $todo[]=zp_astra_action((string)$queue['label'],'Sprawdź zapisany stan kolejki i przyczynę zatrzymania.',$key==='photos'?'zp-suite-article-photos':'zp-suite-prepare','health'); }
    }
    if ($counts['new']+$counts['changed']>0) { $todo[]=zp_astra_action('Treści oczekują na tłumaczenie EN',$counts['new'].' nowych · '.$counts['changed'].' zmienionych',zp_astra_translator_page(),'translations'); }
    if ($counts['attention']>0) { $todo[]=zp_astra_action('Tłumaczenia wymagają sprawdzenia',$counts['attention'].' treści z komunikatem lub nieznanym wynikiem',zp_astra_translator_page(),'translations'); }
    $inquiries=function_exists('zp_inbox_new_count')?zp_inbox_new_count():null;
    if ($inquiries>0) { $todo[]=zp_astra_action('Nowe zapytania od klientów',$inquiries.' nowych wiadomości we wspólnej skrzynce','zp-suite-zapytania','inquiries',['status'=>'new']); }
    if (!empty($data['sync']['last_safe_error']) || !empty($data['sync']['health']['issues'])) { $todo[]=zp_astra_action('Sprawdź aktualizacje Google','Ostatni raport lub jego harmonogram wymaga uwagi.','zp-suite-stats','visibility'); }
    return ['data'=>$data,'translation'=>$translation,'counts'=>$counts,'queues'=>$queues,'review'=>$known?$review:null,'inquiries'=>$inquiries,'todo'=>$todo];
}
function zp_astra_metric(string $label,$value,string $source,string $note,string $icon): void {
    echo '<section class="metric"><div class="metric-head"><span>'.esc_html($label).'</span>'.zp_astra_icon($icon).'</div><div class="metric-value">'.esc_html(zp_admin20_number($value)).'</div><div class="metric-foot">'.esc_html($source).' · '.esc_html($note).'</div></section>';
}
function zp_astra_dashboard(): void {
    if (!current_user_can('manage_options')) { return; }
    if (function_exists('zp_suite3_dashboard')) { zp_suite3_dashboard(); return; }
    $s=zp_astra_summary(); $data=$s['data']; $period=zp_admin20_period(); $ga4=$data['ga4']['periods'][$period]??[]; $ga4=is_array($ga4)?$ga4:[];
    $sources=zp_admin20_source_counts(!empty($data['ga4']['sourceHealth'][$period]['totals_usable']) && ($ga4['status']??'')!=='awaiting_data' ? zp_admin20_sources($data,$period) : null);
    zp_admin20_open('Pulpit','dashboard','Najważniejsze sprawy, w jednym miejscu.');
    echo '<div class="zpA-dashboard-tools"><form method="get" action="'.esc_url(admin_url('admin.php')).'"><input type="hidden" name="page" value="zp-suite"><label class="screen-reader-text" for="zpA-period">Okres GA4</label><select id="zpA-period" name="zp_period">';
    foreach (['today'=>'Dziś','7d'=>'Ostatnie 7 dni','28d'=>'Ostatnie 28 dni'] as $key=>$label) { echo '<option value="'.esc_attr($key).'"'.selected($period,$key,false).'>'.esc_html($label).'</option>'; }
    echo '</select><button class="button">Pokaż</button></form>'.zp_astra_link('Odśwież zapisane stany','zp-suite','button',['zp_period'=>$period]).'</div>';
    do_action('zp_astra_dashboard_metrics',$period);
    echo '<section class="metrics" aria-label="Podsumowanie strony">';
    zp_astra_metric('Ruch z Google',$sources['google'],'sesje organiczne GA4',zp_admin20_date($ga4['fetched_at']??null),'visibility');
    zp_astra_metric('Sesje na stronie',($ga4['status']??'')==='awaiting_data'?null:($ga4['sessions']??null),'GA4',zp_admin20_date($ga4['fetched_at']??null),'visibility');
    zp_astra_metric('Treści do przejrzenia',$s['review'],'kolejki treści','zapisany stan','content');
    zp_astra_metric('Nowe zapytania',$s['inquiries'],'skrzynka Suite','stan bieżący','inquiries');
    echo '</section>';
    if (!empty($ga4['stale'])) { echo '<p class="zpAdmin20__muted">'.esc_html(zp_admin23_freshness($ga4)).'</p>'; }
    echo '<div class="columns"><section class="card"><header class="card-head"><div><h2>Do zrobienia</h2><p>Sprawy wynikające z zapisanych stanów modułów.</p></div>'.zp_astra_tag((string)count($s['todo']).' spraw').'</header><div class="action-list">';
    if ($s['todo']) { echo implode('',array_slice($s['todo'],0,8)); }
    else { echo '<div class="zpA-empty"><b>Brak spraw wymagających działania</b><p>W odczytanych modułach nie ma zgłoszonych problemów ani nowych zapytań. Dostępność danych sprawdzisz obok.</p></div>'; }
    echo '</div><div class="subtle-foot">Odczyt '.esc_html(wp_date('d.m.Y H:i')).'. Otwarcie pulpitu nie uruchamia zadań API.</div></section><aside class="side-stack">';
    zp_astra_health($s);
    echo '<section class="navy-card"><div class="eyebrow">KONTROLA KOSZTÓW</div><h2>Automatyzacje pod Twoją kontrolą.</h2><p>Model, wspólny budżet i harmonogram w jednym miejscu.</p>'.zp_astra_link('API i koszty','zp-suite-settings','button',['zp_section'=>'api']).'<div class="navy-line"></div></section></aside></div><div class="quick-grid">';
    foreach ([['visibility','Widoczność w Google','Wyniki, pozycje fraz i plan SEO.','zp-suite-stats'],['calendar','Kalendarz publikacji','Zapisane terminy przygotowanych artykułów.','zp-suite-calendar'],['history','Historia działań','Zmiany ustawień i ostatnie zadania.','zp-suite-settings']] as $q) { echo '<a class="quick" href="'.esc_url(zp_admin20_url($q[3],$q[0]==='history'?['zp_section'=>'history']:[])).'">'.zp_astra_icon($q[0]).'<div><h3>'.esc_html($q[1]).'</h3><p>'.esc_html($q[2]).'</p></div></a>'; }
    echo '</div>'; zp_admin20_close();
}
function zp_astra_health(array $s): void {
    $data=$s['data']; $sync=is_array($data['sync']??null)?$data['sync']:[];
    $t=$s['translation']; $c=$s['counts']; $scan=is_array($t['scan']??null)?$t['scan']:[];
    $queueIssues=0;$queueKnown=0;$active=0;
    foreach ($s['queues']['queues']??[] as $queue) { if (is_array($queue['counts']??null)) {$queueKnown++;} $queueIssues+=count($queue['issues']??[]);$active+=(int)($queue['active']??0); }
    $google=empty($sync['updated_at'])?'Brak pomiaru':(!empty($sync['queued'])?'Aktualizacja w tle':(!empty($sync['last_safe_error'])?'Wymaga sprawdzenia':'Raport zapisany'));
    $rows=[['Publikacje',$queueKnown?($queueIssues?'Sprawdź kolejki':($active?'Praca w toku':'Brak aktywnej kolejki')):'Brak odczytu','zp-suite-prepare',null],['Tłumaczenia EN',$t?($c['attention']?'Wymaga sprawdzenia':($c['active']?'Praca w toku':'Stan odczytany')):'Moduł niedostępny',zp_astra_translator_page(),$scan['at']??null],['Raporty Google',$google,'zp-suite-stats',$sync['updated_at']??null]];
    $lh=get_option('zps_admin_lighthouse_20261010_queue',[]); $lh=is_array($lh)?$lh:[];
    $rows[]=['Wydajność',empty($lh)?'Brak zapisu kolejki':(($lh['status']??$lh['state']??'')==='failed'?'Wymaga sprawdzenia':'Zobacz ostatni pomiar'),'zp-suite-performance',$lh['updated_at']??null];
    echo '<section class="card"><header class="card-head"><div><h2>Stan modułów</h2><p>Stan zapisu i dostępność odczytu.</p></div>'.zp_astra_icon('health').'</header><div class="status-list">';
    foreach ($rows as [$label,$status,$page,$at]) { echo '<a class="status-row" href="'.esc_url(zp_admin20_url($page)).'"><span><b>'.esc_html($label).'</b>'.($at?'<small>'.esc_html(zp_admin20_date($at)).'</small>':'').'</span>'.zp_astra_tag($status,str_contains($status,'Sprawdź')||str_contains($status,'sprawdzenia')?'amber':'').'</a>'; }
    echo '</div><div class="subtle-foot">Zapis harmonogramu nie potwierdza wykonania zadania.</div></section>';
}
function zp_astra_usd($micro): string { return is_numeric($micro) && (float)$micro>=0 ? number_format_i18n((float)$micro/1000000,3).' USD' : 'Brak odczytu'; }
function zp_astra_budget(array $s): void {
    $b=is_array($s['budget']??null)?$s['budget']:[];
    echo '<section class="run-card"><div class="run-head"><h3>Budżet tłumaczeń</h3>'.zp_astra_icon('budget').'</div>';
    foreach (['settled'=>'Wykorzystano','reserved'=>'Zarezerwowano','limit'=>'Wspólny limit kampanii'] as $key=>$label) { echo '<div class="spend"><span>'.esc_html($label).'</span><strong data-zpt2-budget="'.esc_attr($key).'">'.esc_html(zp_astra_usd($b[$key]??null)).'</strong></div>'; }
    $pct=!empty($b['limit'])?max(0,min(100,100*((float)($b['settled']??0)+(float)($b['reserved']??0))/(float)$b['limit'])):0;
    echo '<div class="budget-track" aria-hidden="true"><span data-zpt2-budget-bar style="width:'.esc_attr((string)$pct).'%"></span></div><p class="band-meta">Wspólny rejestr kosztów. Limit nie odnawia się codziennie ani miesięcznie.</p></section>';
}
function zp_astra_nonce(string $operation,array $config): void {
    echo '<input type="hidden" name="action" value="zpt2_action"><input type="hidden" name="operation" value="'.esc_attr($operation).'"><input type="hidden" name="_wpnonce" value="'.esc_attr($config['nonce']).'">';
    if (function_exists('zp_suite_workspace_active') && zp_suite_workspace_active()) { echo '<input type="hidden" name="_zp_workspace" value="1">'; }
}
function zp_astra_translation_label(string $state): string {
    return ['new'=>'Nowa treść','changed'=>'Zmieniona PL','published'=>'Gotowe EN','legacy'=>'Gotowe EN','queued'=>'W kolejce','translating'=>'Tłumaczenie','repairing'=>'Naprawa fragmentu','checking'=>'Kontrola wyniku','deferred'=>'Czeka na limit znaków','attention'=>'Wymaga sprawdzenia','unknown'=>'Nieznany wynik'][$state]??'Nieznany stan';
}
function zp_astra_public_url($path): string {
    if (!is_string($path) || $path==='' || $path[0]!=='/' || str_starts_with($path,'//') || preg_match('/[\x00-\x20\\\\]/',$path)) { return ''; }
    return home_url($path);
}
function zp_astra_translation_row(array $row): void {
    $id=absint($row['post_id']??0);if (!$id) {return;}
    $state=(string)($row['state']??'unknown');$eligible=in_array($state,['new','changed'],true);$title=(string)($row['title']??'Treść');
    $color=in_array($state,['published','legacy'],true)?'green':($state==='new'?'blue':'amber');
    echo '<tr data-zpt2-row="'.$id.'" data-state="'.esc_attr($state).'"><td><input type="checkbox" name="ids[]" value="'.$id.'" aria-label="'.esc_attr('Zaznacz: '.$title).'"'.($eligible?'':' disabled').'></td><td><strong>'.esc_html($title).'</strong><small>'.esc_html((string)($row['source']??'')).'</small></td><td><span class="zpt2-state tag '.$color.'">'.esc_html(zp_astra_translation_label($state)).'</span><small class="zpt2-error">'.esc_html((string)($row['error']??'')).'</small></td><td class="zpt2-progress">'.(is_numeric($row['total']??null)?esc_html((string)($row['done']??0).' / '.$row['total']):'—').'</td><td class="zpA-row-links">';
    foreach (['source'=>'PL','en'=>'EN'] as $key=>$label) { $url=zp_astra_public_url($row[$key]??null); if ($url!=='' && ($key==='source' || in_array($state,['published','legacy'],true))) { echo '<a href="'.esc_url($url).'" target="_blank" rel="noopener" aria-label="'.esc_attr($title.' — '.$label).'">'.esc_html($label).' ↗</a>'; } }
    echo '</td></tr>';
}
function zp_suite_render_translator_v2(array $s,array $config): void {
    if (!current_user_can('manage_options')) { return; }
    zp_admin20_open('Tłumaczenia EN','translations','Nowe treści i aktualizacje polskiej wersji — w jednej kolejce.');
    $counts=zp_astra_counts($s);$scan=is_array($s['scan']??null)?$s['scan']:[];
    echo '<div id="zpt2-admin"><div class="zpA-dashboard-tools"><form method="post" action="'.esc_url($config['action_url']).'">';zp_astra_nonce('scan',$config);echo '<button class="button">'.zp_astra_icon('refresh').'Skanuj zmiany</button></form><button type="button" class="button" data-zpt2-refresh>Odśwież status</button></div>';
    $notice=is_string($_GET['zpt2_notice']??null)?sanitize_key(wp_unslash($_GET['zpt2_notice'])):'';
    if ($notice!=='') { $messages=['done'=>'Zapisano dyspozycję. Aktualny stan widzisz poniżej.','busy'=>'Inne zadanie właśnie zapisuje stan. Spróbuj ponownie za chwilę.','budget_limit'=>'Osiągnięto wspólny limit kosztów. Zadanie wymaga sprawdzenia.','daily_chars_limit'=>'Dzienny limit znaków został wykorzystany. Zadanie poczeka na kolejny okres.','invalid_selection'=>'Wybierz od 1 do 10 nowych lub zmienionych treści.'];echo '<div class="zpAdmin20__notice" role="status">'.esc_html($messages[$notice]??('Nie wykonano dyspozycji. Kod: '.$notice)).'</div>'; }
    if (!empty($s['setup_error'])) {echo '<div class="zpAdmin20__notice" role="status">Konfiguracja wymaga sprawdzenia: '.esc_html((string)$s['setup_error']).'</div>';}
    echo '<section class="summary-band zpt2-summary"><div><p class="band-label">Ostatnie skanowanie</p><div class="band-value" data-zpt2-scan>'.esc_html(zp_admin20_date($scan['completed_at']??$scan['at']??null)).'</div><p class="band-meta">Porównanie głównej treści i pól SEO</p></div><div><p class="band-label">Model tłumaczenia</p><div class="band-value">'.esc_html((string)($s['policy']['model']??'Brak odczytu')).' '.zp_astra_tag((string)($s['policy']['reasoning']??''),'blue').'</div><p class="band-meta">Zapisana polityka API</p></div><div><p class="band-label">Codzienny skan zmian</p><div class="band-value">'.esc_html(!empty($s['daily_enabled'])?'Włączony':'Wyłączony').'</div><p class="band-meta">Następny: '.esc_html(!empty($s['next_scan'])?zp_admin20_date($s['next_scan']):'brak terminu').'</p></div></section><div class="translator-columns"><section class="card"><header class="card-head"><div><h2>Treści do tłumaczenia</h2><p>Wybierz nowe lub zmienione treści. Gotowe EN pozostają dostępne podczas pracy.</p></div>'.zp_astra_tag('do 10 naraz').'</header><div class="list-tabs" role="group" aria-label="Filtr treści">';
    foreach (['todo'=>'Do działania','new'=>'Nowe','changed'=>'Zmienione','published'=>'Gotowe EN'] as $key=>$label) { $n=$key==='todo'?$counts['total']-$counts['published']:$counts[$key];echo '<button type="button" data-zpt2-filter="'.esc_attr($key).'" aria-pressed="'.($key==='todo'?'true':'false').'"'.($key==='todo'?' class="active"':'').'>'.esc_html($label).' <span data-zpt2-count="'.esc_attr($key).'">'.$n.'</span></button>'; }
    echo '</div><div class="table-tools"><label class="search">'.zp_astra_icon('search').'<input id="zpt2-search" type="search" placeholder="Znajdź treść…" aria-label="Znajdź treść"></label><span>PL → EN</span></div><form method="post" action="'.esc_url($config['action_url']).'" data-zpt2-translate>';zp_astra_nonce('translate',$config);
    echo '<div class="table-scroller" tabindex="0" role="region" aria-label="Lista treści, przewijana poziomo"><table class="content-table zpt2-table"><thead><tr><th><input type="checkbox" data-zpt2-select-all aria-label="Zaznacz widoczne treści (maksymalnie 10)"></th><th>Treść</th><th>Status</th><th>Postęp</th><th>Podgląd</th></tr></thead><tbody>';
    foreach ($s['rows']??[] as $row) {if(is_array($row))zp_astra_translation_row($row);}
    echo '</tbody></table></div><div class="zpA-empty" data-zpt2-empty'.(!empty($s['rows'])?' hidden':'').'><b>Brak treści w tym widoku</b><p>Skanuj zmiany lub wybierz inny filtr.</p></div><div class="selection-bar"><div><strong data-zpt2-selection>Wybierz treści do tłumaczenia</strong><p data-zpt2-selection-note>Limit: 10 treści w jednym zadaniu.</p></div><button class="button primary" data-zpt2-submit disabled>Przetłumacz zaznaczone →</button></div></form></section><aside class="side-stack"><section class="run-card"><div class="run-head"><h3>Przebieg zadania</h3>'.zp_astra_icon('health').'</div><div class="quiet-empty" id="zpt2-live" aria-live="polite">'.esc_html($counts['active']?$counts['active'].' treści w kolejce lub w trakcie pracy.':'Brak aktywnego zadania.').'</div><div class="run-steps"><div class="step"><i>1</i>Tłumaczenie treści</div><div class="step"><i>2</i>Kontrola kompletności</div><div class="step"><i>3</i>Publikacja wersji EN</div></div><p class="run-desc" data-zpt2-eta>'.esc_html(is_numeric($s['eta_seconds']??null)?'Mediana ukończonego zadania: '.(int)$s['eta_seconds'].' s.':'Czas realizacji pojawi się po pierwszych pomiarach.').'</p><p class="band-meta" data-zpt2-poll-status>Automatyczny odczyt statusu nie wywołuje modeli API.</p></section>';zp_astra_budget($s);echo '</aside></div>';
    zp_astra_job_history();
    echo '<details class="details"><summary><span>Ustawienia i automatyzacja</span><span aria-hidden="true">⌄</span></summary><form method="post" action="'.esc_url($config['action_url']).'">';zp_astra_nonce('scan_toggle',$config);
    echo '<label class="zpAdmin20__check"><input type="checkbox" name="enabled" value="1"'.checked(!empty($s['daily_enabled']),true,false).'> Codzienny skan o 03:00 (Warszawa)</label><label class="zpAdmin20__check"><input type="checkbox" name="auto_enabled" value="1"'.checked(!empty($s['auto_enabled']),true,false).'> Po skanie tłumacz nowe i zmienione treści</label><p>Automatyczne tłumaczenie włącz po sprawdzeniu pierwszej ręcznie zleconej treści. Pracuje w granicach wspólnego budżetu i dziennego limitu znaków.</p><button class="button">Zapisz automatyzację</button></form>';
    if (is_callable(['ZPT2_Admin','render_limits'])) { ZPT2_Admin::render_limits($s,$config); }
    echo '<p class="band-meta">Ostatnia aktywność wykonawcy: '.esc_html(zp_admin20_date($s['worker_last']??null)).'. Harmonogram WordPressa nie potwierdza wykonania crona na hostingu.</p></details><script type="application/json" id="zpA-translator-config">'.wp_json_encode($config+['home_url'=>home_url('/')],JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT).'</script></div>';zp_admin20_close();
}
function zp_astra_job_history(): void {
    if (!class_exists('ZPT2_Store')) {return;}
    $history=ZPT2_Store::get('jobs');if(!is_array($history))$history=[];
    $history=array_values(array_filter($history,'is_array'));usort($history,static fn($a,$b)=>(int)($b['updated_at']??0)<=>(int)($a['updated_at']??0));$history=array_slice($history,0,20);
    echo '<details class="details"><summary><span>Historia ostatnich zadań</span><span aria-hidden="true">⌄</span></summary>';
    if (!$history) {echo '<p>Nie ma jeszcze zapisanych zadań nowego modułu. Wcześniejsze gotowe tłumaczenia pozostają dostępne.</p>';}
    else {echo '<div class="table-scroller"><table class="content-table"><thead><tr><th>Treść</th><th>Ostatni zapis</th><th>Stan</th></tr></thead><tbody>';foreach($history as $row){echo '<tr><td>'.esc_html((string)($row['source']??'')).'</td><td>'.esc_html(zp_admin20_date($row['updated_at']??null)).'</td><td>'.esc_html(zp_astra_translation_label((string)($row['state']??'unknown'))).'</td></tr>';}echo '</tbody></table></div>';}
    echo '</details>';
}
function zp_astra_audit(string $field,string $before,string $after): void {
    if ($before===$after) {return;}
    $allowed=['Gęstość panelu','Automatyczne odświeżanie statusu','Codzienny skan EN','Automatyczne tłumaczenie EN'];
    if (!in_array($field,$allowed,true)) {return;}
    $history=get_option('zp_astra_settings_history_20261013',[]);$history=is_array($history)?$history:[];
    $history[]=['at'=>time(),'actor'=>get_current_user_id(),'field'=>$field,'before'=>$before,'after'=>$after];
    update_option('zp_astra_settings_history_20261013',array_slice($history,-60),false);
}
function zp_astra_preferences_save(): void {
    if (!current_user_can('manage_options')) {wp_die('Brak uprawnień.','ZP Suite',['response'=>403]);}
    check_admin_referer('zp_astra_preferences');$old=zp_astra_preferences();
    $density=is_string($_POST['density']??null)?sanitize_key(wp_unslash($_POST['density'])):'';
    if (!in_array($density,['comfortable','compact'],true)) {wp_die('Nieprawidłowa gęstość panelu.','ZP Suite',['response'=>400]);}
    $new=['density'=>$density,'live'=>isset($_POST['live']) && $_POST['live']==='1'];
    update_user_meta(get_current_user_id(),'zp_astra_preferences_20261013',$new);
    if (zp_astra_preferences()!==$new) {wp_die('Nie udało się zapisać ustawień.','ZP Suite',['response'=>500]);}
    zp_astra_audit('Gęstość panelu',$old['density']==='compact'?'Zwarta':'Przestronna',$density==='compact'?'Zwarta':'Przestronna');
    zp_astra_audit('Automatyczne odświeżanie statusu',$old['live']?'Włączone':'Wyłączone',$new['live']?'Włączone':'Wyłączone');
    wp_safe_redirect(zp_admin20_url('zp-suite-settings',['zp_saved'=>'1']));exit;
}
function zp_astra_settings(): void {
    if (!current_user_can('manage_options')) {return;}
    $section=is_string($_GET['zp_section']??null)?sanitize_key(wp_unslash($_GET['zp_section'])):'general';
    $sections=['general'=>'Ogólne','api'=>'API i koszty','automation'=>'Automatyzacje','integrations'=>'Integracje','history'=>'Historia zmian'];
    if (!isset($sections[$section])) {$section='general';}
    zp_admin20_open('Ustawienia','settings','Jasne zasady działania dla całego ZP Suite.');
    if (($_GET['zp_saved']??'')==='1') {echo '<div class="zpAdmin20__notice" role="status">Zapisano preferencje panelu dla Twojego konta.</div>';}
    if (function_exists('zp_panel_notice')) {zp_panel_notice();}
    echo '<div class="settings-layout"><nav class="settings-nav" aria-label="Kategorie ustawień">';
    foreach ($sections as $key=>$label) {echo '<a href="'.esc_url(zp_admin20_url('zp-suite-settings',['zp_section'=>$key])).'"'.($key===$section?' class="active" aria-current="page"':'').'>'.esc_html($label).'</a>';}
    echo '</nav><div>';
    if ($section==='general') {
        $prefs=zp_astra_preferences();echo '<section class="settings-card"><h2>Twój panel</h2><p>Preferencje dotyczą Twojego konta. Pozostali administratorzy mogą pracować po swojemu.</p><form method="post" action="'.esc_url(admin_url('admin-post.php')).'"><input type="hidden" name="action" value="zp_astra_preferences">';wp_nonce_field('zp_astra_preferences');
        echo '<div class="setting-row"><div><h3><label for="zpA-density">Gęstość panelu</label></h3><p>Przestronny układ lub więcej danych widocznych jednocześnie.</p></div><select id="zpA-density" name="density"><option value="comfortable"'.selected($prefs['density'],'comfortable',false).'>Przestronna</option><option value="compact"'.selected($prefs['density'],'compact',false).'>Zwarta</option></select></div><div class="setting-row"><div><h3>Automatyczne odświeżanie statusu</h3><p>Odczytuj postęp tłumaczeń, gdy panel jest otwarty. Odczyt nie wywołuje modeli API.</p></div><label class="zpAdmin20__check"><input type="checkbox" name="live" value="1"'.checked($prefs['live'],true,false).'> Włączone</label></div><div class="settings-save"><button class="button primary">Zapisz preferencje</button></div></form></section>';
        $cc=function_exists('zp_suite_cc_get')?zp_suite_cc_get():[];$set=is_array($cc['settings']??null)?$cc['settings']:[];
        if (function_exists('zp_suite_cc_get')) {echo '<section class="settings-card"><h2>Kontakt i popup</h2><p>Ustawienia istniejącego formularza kontaktowego.</p><form method="post" action="'.esc_url(admin_url('admin-post.php')).'"><input type="hidden" name="action" value="zp_cc_settings">';wp_nonce_field('zp_cc_settings');echo '<label class="zpAdmin20__check"><input type="checkbox" name="exit_popup_enabled" value="1"'.checked(($set['exit_popup_enabled']??'1'),'1',false).'> Popup „Zanim zamkniesz stronę” z prośbą o telefon</label><label class="zpAdmin20__field">Pokaż po (sekundach)<input type="number" name="exit_popup_delay" min="2" max="120" step="0.5" value="'.esc_attr((string)(max(2000,(int)($set['exit_popup_delay']??8500))/1000)).'"></label><label class="zpAdmin20__check"><input type="checkbox" name="safe_mode" value="1"'.checked(($set['safe_mode']??'0'),'1',false).'> Tryb awaryjny — wyłącz animacje na stronie</label><button class="button">Zapisz kontakt i popup</button></form></section>';}
    } elseif ($section==='api') {
        $s=zp_astra_translator_status();echo '<section class="settings-card"><h2>Model i polityka API</h2><p>Tłumaczenia korzystają z istniejącego modelu i wspólnego rejestru kosztów. Koszt zarezerwowany zmniejsza dostępny budżet.</p>';
        foreach (['Model tłumaczenia'=>$s['policy']['model']??'Brak odczytu','Poziom rozumowania'=>$s['policy']['reasoning']??'Brak odczytu'] as $label=>$value) {echo '<div class="setting-row"><h3>'.esc_html($label).'</h3><span class="locked">'.esc_html((string)$value).'</span></div>';}
        $chars=class_exists('ZPTE\\Settings')?\ZPTE\Settings::get('daily_chars'):null;
        echo '<div class="setting-row"><div><h3>Dzienny limit znaków</h3><p>Po wykorzystaniu limitu zadanie zachowuje postęp i czeka na następny dzień.</p></div><span class="locked">'.esc_html(zp_admin20_number($chars)).'</span></div><div class="setting-note">'.zp_astra_icon('budget').'<span>Limit budżetu kampanii i dzienny limit znaków to dwa odrębne ograniczenia. Ten ekran nie zeruje wykorzystanego budżetu.</span></div></section>';zp_astra_budget($s);echo '<p>'.zp_astra_link('Otwórz tłumaczenia',zp_astra_translator_page()).'</p>';
    } elseif ($section==='automation') {
        echo '<section class="settings-card"><h2>Harmonogramy i wykonanie</h2><p>Terminy pochodzą z harmonogramu WordPressa. Zarządzanie odbywa się w odpowiednim module.</p>';
        $jobs=[['Skan i tłumaczenia EN','zpt2_daily_scan',zp_astra_translator_page()],['Raporty Google','zp_admin_stats_daily_20261009','zp-suite-stats'],['Ruch GA4 dziś','zp_admin_stats_hourly_20261009','zp-suite-stats'],['Pozycje Senuto','zp_senuto_positions_daily_20261010','zp-suite-positions'],['Lighthouse','zp_admin_lighthouse_daily_20261010','zp-suite-performance']];
        foreach($jobs as [$label,$hook,$page]){echo '<div class="setting-row"><div><h3>'.esc_html($label).'</h3><p>Następny termin: '.esc_html(zp_admin20_date(wp_next_scheduled($hook)?:null)).'</p></div>'.zp_astra_link('Otwórz',$page).'</div>';}
        echo '</section><section class="settings-card"><h2>Publikacje artykułów</h2><p>Kalendarz pokazuje zaplanowane terminy i etapy przygotowania. Zmiana wyglądu panelu nie tworzy nowego harmonogramu.</p>'.zp_astra_link('Otwórz kalendarz','zp-suite-calendar').'</section>';zp_astra_health(zp_astra_summary());
    } elseif ($section==='integrations') {
        echo '<section class="settings-card"><h2>Źródła danych</h2><p>Każda integracja zachowuje własne źródło, zakres i datę pomiaru.</p><div class="setting-row"><div><h3>Google Analytics i Search Console</h3><p>Połączenie zarządzane przez Rank Math. GA4 raportuje ruch, Search Console wyniki wyszukiwania.</p></div><a class="button" href="'.esc_url(admin_url('admin.php?page=rank-math-options-general&view=analytics')).'">Połączenie Google ↗</a></div><div class="setting-row"><div><h3>Senuto</h3><p>Pozycje fraz z oznaczeniem projektu, urządzenia i daty pomiaru.</p></div>'.zp_astra_link('Otwórz pozycje','zp-suite-positions').'</div></section>';
        zp_admin23_modules();
    } else {
        echo '<section class="settings-card"><h2>Historia zmian ustawień</h2><p>Nowe zmiany preferencji panelu i automatyzacji EN. Rejestr nie zawiera kluczy API, promptów ani wcześniejszej, odtworzonej historii.</p>';
        $h=get_option('zp_astra_settings_history_20261013',[]);$h=is_array($h)?array_reverse($h):[];
        if(!$h){echo '<div class="empty-history"><div><strong>Nie ma jeszcze zapisanych zmian</strong>Historia zacznie się od pierwszej zmiany tych ustawień.</div></div>';}
        else{echo '<div class="table-scroller"><table class="content-table"><thead><tr><th>Data i autor</th><th>Ustawienie</th><th>Przed → po</th></tr></thead><tbody>';foreach(array_slice($h,0,60)as$row){if(!is_array($row))continue;$user=get_userdata((int)($row['actor']??0));echo '<tr><td>'.esc_html(zp_admin20_date($row['at']??null)).'<small>'.esc_html($user?(string)$user->display_name:'Automatycznie').'</small></td><td>'.esc_html((string)($row['field']??'')).'</td><td>'.esc_html((string)($row['before']??'')).' → '.esc_html((string)($row['after']??'')).'</td></tr>';}echo '</tbody></table></div>';}
        echo '</section>';zp_astra_job_history();
    }
    echo '</div></div>';zp_admin20_close();
}
function zp_astra_route(): bool {
    $page=is_string($_GET['page']??null)?sanitize_key(wp_unslash($_GET['page'])):'';
    return is_admin() && current_user_can('manage_options') && !(function_exists('wp_doing_ajax')&&wp_doing_ajax()) && (str_starts_with($page,'zp-suite')||in_array($page,['zp-translator-v2','zp-tlumacz-en','zpl-languages','zpl-skaner'],true));
}
add_action('admin_post_zp_astra_preferences','zp_astra_preferences_save');
add_action('updated_option',static function($option,$old,$new):void{
    if($option!=='zpt2_config'||!is_array($old)||!is_array($new)){return;}
    foreach(['daily_enabled'=>'Codzienny skan EN','auto_enabled'=>'Automatyczne tłumaczenie EN']as$key=>$label){zp_astra_audit($label,!empty($old[$key])?'Włączone':'Wyłączone',!empty($new[$key])?'Włączone':'Wyłączone');}
},10,3);
add_action('admin_head',static function():void{
    // 3.0.0: assets/admin/zp-suite-3.css replaces this stylesheet when the new panel is loaded.
    if(!zp_astra_route()||function_exists('zp_suite3_open')){return;}
    $file=__DIR__.'/../assets/admin-astra-workspace-20261013.css';
    if(is_readable($file)){echo '<style id="zp-astra-workspace-css">'.file_get_contents($file).'</style>';}
},PHP_INT_MAX);
add_action('admin_footer',static function():void{
    if(!zp_astra_route()){return;}
    echo '<script src="'.esc_url(ZP_SUITE_URL.'assets/admin-astra-workspace-20261013.js?ver=20261013.1').'"></script>';
},PHP_INT_MAX);
