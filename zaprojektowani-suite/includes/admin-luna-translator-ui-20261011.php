<?php
/** Luna presentation only. Existing rewrite/nightly handlers, ledgers and models own all work. */
if (!defined('ABSPATH')) { exit; }
final class ZP_Luna_Translator_UI_20261011 {
    public const PAGE = 'zp-tlumacz-en';
    public const AJAX = 'zp_en_luna_ui_status';
    private const PREFIX = 'zps_en_rewrite_20261011_';
    private const RUNNING = ['queued','translating','activating','processing'];
    private const LABELS = ['queued'=>'W kolejce','translating'=>'Tłumaczenie','staged'=>'Gotowe do aktywacji','activation_prepared'=>'Aktywacja','checking_serving'=>'Kontrola publicznego EN','verified'=>'EN potwierdzone','attention'=>'Wymaga uwagi','skipped'=>'Pominięta','processing'=>'Przetwarzanie','activating'=>'Aktywacja','complete_with_issues'=>'Zakończono z błędami','rolled_back'=>'Przywrócono poprzednią wersję','cancelled'=>'Anulowano','idle'=>'Nie rozpoczęto','unknown'=>'Stan niedostępny'];
    private const ERRORS = ['storage_failed','busy','forbidden','dependency_missing','scope_invalid','source_changed','source_unavailable','source_invalid','source_incomplete','manual_changed','connection_changed','budget_limit','unknown_paid_attempt','request_changed','response_identity','response_expired','poll_limit','poll_failed','response_not_found','response_invalid','response_failed','response_cancelled','response_incomplete','response_refusal','translation_json','translation_invalid','api_http_401','api_http_403','api_http_429','api_http','serving_unverified','pointer_changed','canary_required','queue_changed','source_private','source_noindex','source_noarchive','source_unmapped','footer_proof_unavailable','daily_chars_limit'];
    public static function allowed(): bool { return current_user_can('manage_options') && current_user_can('edit_posts') && current_user_can('unfiltered_html'); }
    private static function owned(): bool { return is_string($_GET['page']??null) && $_GET['page']===self::PAGE; }
    private static function read(string $name) { wp_cache_delete($name,'options'); return get_option($name,null); }
    private static function id($id): bool { return is_string($id) && preg_match('/^[a-f0-9]{32}$/D',$id)===1; }
    private static function state($state): string { return is_string($state) && isset(self::LABELS[$state]) ? $state : 'unknown'; }
    public static function label(string $state): string { return self::LABELS[$state]??self::LABELS['unknown']; }
    private static function error($error): string { return is_string($error) && in_array($error,self::ERRORS,true) ? $error : ($error===''||$error===null ? '' : 'unknown'); }
    private static function integer($value): ?int { return is_int($value) && $value>=0 ? $value : null; }
    private static function stamp($value): ?int { return is_int($value) && $value>0 ? $value : null; }
    private static function date(?int $value): string { return $value===null?'—':wp_date('d.m.Y H:i:s',$value,wp_timezone()); }
    private static function money(?int $value): string { return $value===null?'—':number_format($value/1000000,3,'.','').' USD'; }
    private static function title(string $source,array $descriptor): string {
        $id=$descriptor['post_id']??0;
        if (is_int($id)&&$id>0&&current_user_can('edit_post',$id)) {
            $post=get_post($id);
            if (is_object($post)&&($post->post_status??null)==='publish'&&($post->post_password??null)===''&&in_array($post->post_type??null,['post','page'],true)) {
                $title=$post->post_title??'';if (is_string($title)&&$title!=='') return wp_strip_all_tags($title);
            }
        }
        return $source==='/'?'Strona główna':$source;
    }
    /** Privileged, read-only projection. No raw page map, journal, prompt, token or API key leaves this method. */
    public static function snapshot(): array {
        if (!self::allowed()||!class_exists('ZP_EN_Rewrite_20261011')) return ['available'=>false,'reason'=>'forbidden_or_dependency'];
        try {
            $index=self::read(ZP_EN_Rewrite_20261011::INDEX);$manifest=ZP_EN_Rewrite_20261011::manifest();
            if ($index===null) $index=[];
            if (!is_array($index)||!is_array($manifest)||count($manifest)>1000) return ['available'=>false,'reason'=>'queue_changed'];
            $id=$index['current']??'';$revision=null;
            if ($id!=='') {
                if (!self::id($id)) return ['available'=>false,'reason'=>'queue_changed'];
                $revision=self::read(self::PREFIX.'r_'.$id);
                if (!is_array($revision)||($revision['id']??null)!==$id||!is_array($revision['pages']??null)||count($revision['pages'])>137) return ['available'=>false,'reason'=>'queue_changed'];
            }
            $summary=ZP_EN_Rewrite_20261011::status();$rows=[];$counts=[];$missing=0;$unknown_maps=0;
            $budget=$revision&&self::id($revision['campaign']??null)?self::read(self::PREFIX.'b_'.$revision['campaign']):null;
            $settled=is_array($budget)?self::integer($budget['settled']??null):null;$limit=is_array($budget)?self::integer($budget['limit']??null):null;$reserved=null;
            if (is_array($budget)&&is_array($budget['reservations']??null)) { $reserved=0;foreach ($budget['reservations'] as $value) { if (self::integer($value)===null||$value>PHP_INT_MAX-$reserved) { $reserved=null;break; }$reserved+=$value; } }
            $pages=$revision['pages']??array_fill_keys(array_keys($manifest),['state'=>'idle']);
            $dynamic=class_exists('ZP_EN_Rewrite_Nightly_20261011')?ZP_EN_Rewrite_Nightly_20261011::descriptors():[];
            foreach ($pages as $source=>$row) {
                if (!is_string($source)||!is_array($row)||preg_match('~^/(?:oferty|briefy)(?:/|$)~i',$source)) return ['available'=>false,'reason'=>'queue_changed'];
                $descriptor=$manifest[$source]??($dynamic[$source]??null);
                if (!is_array($descriptor)||!is_string($descriptor['en']??null)||!str_starts_with($descriptor['en'],'/en/')||strpbrk($descriptor['en'],"?#\\\r\n")!==false) return ['available'=>false,'reason'=>'queue_changed'];
                $state=self::state($row['state']??null);$counts[$state]=($counts[$state]??0)+1;
                $has_en=null;
                if ($state!=='skipped'&&class_exists('ZPL\\Dict')&&method_exists('ZPL\\Dict','has_page')) $has_en=\ZPL\Dict::has_page($source);
                if ($has_en===false) $missing++;elseif ($has_en===null&&$state!=='skipped') $unknown_maps++;
                $rows[]=['source'=>$source,'title'=>$state==='skipped'?$source:self::title($source,$descriptor),'state'=>$state,'label'=>self::label($state),'error'=>self::error($row['error']??''),'has_en'=>$has_en,'pl_url'=>home_url($source),'en_url'=>home_url($descriptor['en']),'preview_url'=>$revision&&in_array($state,['staged','activation_prepared','checking_serving','verified'],true)?add_query_arg('zp_en_rewrite_preview',$id,home_url($descriptor['en'])):''];
            }
            // Do not mix rows/costs from a revision replaced while this read-only screen was loading.
            $after=self::read(ZP_EN_Rewrite_20261011::INDEX);if ($after===null) $after=[];
            if (!is_array($after)||($after['current']??'')!==$id||($revision!==null&&self::read(self::PREFIX.'r_'.$id)!==$revision)) return ['available'=>false,'reason'=>'queue_changed'];
            $total=count($rows)-(int)($counts['skipped']??0);$verified=(int)($counts['verified']??0);$idle=$revision===null;
            return ['available'=>true,'heading'=>$idle||($revision['scope']??'')==='luna_full'?'Pełna wersja angielska':'Aktualizacja nowych i zmienionych stron','state'=>$idle?'idle':self::state($revision['state']??null),'label'=>$idle?self::label('idle'):self::label(self::state($revision['state']??null)),'model'=>$idle?'gpt-6-luna':(($summary['model']??null)==='gpt-6-luna'?'gpt-6-luna':'other_campaign'),'completed'=>$verified,'total'=>$total,'manifest_total'=>count($manifest),'percent'=>$total>0?round(100*$verified/$total,1):null,'counts'=>$counts,'rows'=>$rows,'missing_en_maps'=>$missing,'unknown_en_maps'=>$unknown_maps,'settled'=>$settled,'reserved'=>$reserved,'limit'=>$limit,'completed_chunks'=>self::integer($summary['completed_chunks']??null),'prepared_chunks'=>self::integer($summary['prepared_chunks']??null),'updated_at'=>self::stamp($summary['updated_at']??null),'error'=>self::error($revision['error']??($summary['error']??'')),'tick_wait_ms'=>self::integer($summary['tick_wait_ms']??null),'can_tick'=>$revision!==null&&($revision['actor']??null)===(int)get_current_user_id()&&in_array($revision['state']??null,self::RUNNING,true),'revision'=>$id,'scope'=>$revision['scope']??'','actor_owns'=>$revision!==null&&($revision['actor']??null)===(int)get_current_user_id(),'campaign_unmapped'=>$revision!==null&&($revision['pages']['/kampanie-reklamowe/']??null)===['state'=>'skipped','error'=>'source_unmapped'],'footer_candidate'=>$revision!==null&&($revision['scope']??null)==='luna_full'&&($revision['state']??null)==='attention'&&($revision['error']??null)==='source_changed'&&($revision['pages']['/agencja-reklamowa-katowice/']['state']??null)==='attention'];
        } catch (Throwable $ignored) { return ['available'=>false,'reason'=>'status_unavailable']; }
    }
    public static function ajax(): void {
        if (($_SERVER['REQUEST_METHOD']??'')!=='GET'||!self::allowed()) { wp_send_json_error(['error'=>'forbidden'],403);return; }
        check_ajax_referer(self::AJAX,'_wpnonce');$data=self::snapshot();
        // Opaque native revision is only needed by the server-rendered existing forms.
        unset($data['revision'],$data['scope'],$data['actor_owns'],$data['campaign_unmapped'],$data['footer_candidate']);
        wp_send_json_success($data);
    }
    private static function form(string $action,string $label,string $revision='',bool $budget=false): void {
        echo '<form class="zpLunaUI__form" method="post" action="'.esc_url(admin_url('admin-post.php')).'"><input type="hidden" name="action" value="'.esc_attr($action).'">';wp_nonce_field($action);
        if (function_exists('zp_suite_workspace_active')&&zp_suite_workspace_active()) echo '<input type="hidden" name="_zp_workspace" value="1">';
        if ($revision!=='') echo '<input type="hidden" name="revision" value="'.esc_attr($revision).'">';
        if ($budget) echo '<label>Limit całej kampanii <input name="budget_usd" type="number" min="1" max="15" step="0.5" value="15" required> USD</label>';
        echo '<button type="submit" class="button'.($budget?' button-primary':'').'">'.esc_html($label).'</button></form>';
    }
    public static function controls(array $data): void {
        if (!self::allowed()||empty($data['available'])) return;$state=$data['state'];$id=$data['revision'];
        if ($state==='attention'||$state==='complete_with_issues') self::form('zp_en_rewrite_resume','Wznów istniejącą kolejkę',$id);elseif (in_array($state,self::RUNNING,true)) self::form('zp_en_rewrite_resume','Przywróć harmonogram istniejącej kolejki',$id);
        if ($state==='staged') self::form('zp_en_rewrite_activate','Aktywuj przygotowaną wersję EN',$id);
        if (!empty($data['footer_candidate'])&&!empty($data['actor_owns'])) self::form('zp_en_rewrite_footer_proof','Sprawdź zmianę kart w stopce',$id);
        if (!empty($data['campaign_unmapped'])) self::form('zp_en_rewrite_resolve_campaign','Dołącz Kampanie reklamowe',$id);
        if ($state==='idle') self::form('zp_en_rewrite_luna_full','Rozpocznij pełne tłumaczenie — Luna high','',true);
        if (in_array($state,['attention','verified','rolled_back','cancelled'],true)) { echo '<details><summary>Nowa pełna kampania lub przywrócenie poprzedniej wersji</summary><p>Nowa kampania to nowy płatny proces. Do kontynuowania obecnych wyników służy przycisk „Wznów”.</p>';self::form('zp_en_rewrite_luna_full','Nowa pełna kampania — Luna high','',true);if (in_array($state,['verified','attention'],true)) self::form('zp_en_rewrite_rollback','Przywróć poprzednią wersję EN',$id);echo '</details>'; }
    }
    /** Outside the full137 cohort. Reuse the existing native publication/privacy predicate, never fetch HTML. */
    public static function missing_sources(): array {
        if (!self::allowed()||!class_exists('ZP_EN_Rewrite_Nightly_20261011')||!class_exists('ZPL\\Dict')) return ['available'=>false];
        try {
            $manifest=ZP_EN_Rewrite_20261011::manifest();$ids=get_posts(['post_type'=>['post','page'],'post_status'=>'publish','has_password'=>false,'posts_per_page'=>501,'fields'=>'ids','orderby'=>'date','order'=>'DESC','suppress_filters'=>true,'no_found_rows'=>true]);
            if (!is_array($ids)) return ['available'=>false];$limited=count($ids)>500;$rows=[];$checked=0;$seen=[];
            foreach (array_slice($ids,0,500) as $id) {
                if (!is_int($id)||$id<=0||!current_user_can('edit_post',$id)) continue;
                $post=ZP_EN_Rewrite_Nightly_20261011::public_post($id);if (!$post) continue;
                $source=ZP_EN_Rewrite_20261011::path(wp_parse_url(get_permalink($id),PHP_URL_PATH));if ((int)get_option('page_on_front',0)===$id) $source='/';
                if ($source===''||isset($manifest[$source])||isset($seen[$source])) continue;$seen[$source]=true;$checked++;
                if (\ZPL\Dict::has_page($source)) continue;
                $proof=ZP_EN_Rewrite_Nightly_20261011::missing_source($id);if (!$proof) continue;
                $rows[]=['source'=>$source,'title'=>wp_strip_all_tags((string)$post->post_title),'pl_url'=>home_url($source),'post_id'=>$id,'source_proof'=>$proof['proof']];
            }
            return ['available'=>true,'rows'=>$rows,'checked_outside_manifest'=>$checked,'limited'=>$limited];
        } catch (Throwable $ignored) { return ['available'=>false]; }
    }
    private static function missing(): void {
        $d=self::missing_sources();echo '<section class="zpAdmin20__card"><h2>Nowe publiczne strony bez mapy EN</h2><p>Odczyt WordPressa poza pierwotną kolejką 137 stron. Nie jest to licznik stron oczekujących na poprawioną wersję Luna.</p>';
        if (empty($d['available'])) echo '<p>Nie można potwierdzić nowych braków. Potrzebny jest moduł codziennego skanowania i odczyt publikacji.</p>';
        else {
            echo '<p>Sprawdzono '.(int)$d['checked_outside_manifest'].' publicznych źródeł poza pierwszą kolejką. Bez mapy EN: '.count($d['rows']).'.'.($d['limited']?' Przegląd ograniczony do 500 najnowszych publikacji.':'').'</p>';
            if (!$d['rows']) echo '<p>W sprawdzonym zakresie nie wykryto nowych brakujących map EN.</p>';
            else {
                $context=ZP_EN_Rewrite_Nightly_20261011::missing_form_context();$ready=!empty($context['ready']);$notice=$_GET['zp_en_missing_notice']??'';
                if (is_string($notice)&&in_array($notice,['queued','busy','bootstrap_required','forbidden','queue_changed','source_changed','source_invalid','scope_invalid','storage_failed'],true)) echo '<p role="status">Wybrane nowe strony: <code>'.esc_html($notice).'</code>.</p>';
                if (!$ready) echo '<p>Przycisk oczekuje na potwierdzenie pełnej kampanii 137 stron i wolną kolejkę. Sprawdź aktualny stan codziennego skanowania poniżej.</p>';
                echo '<form method="post" action="'.esc_url(admin_url('admin-post.php')).'"><input type="hidden" name="action" value="zp_en_nightly_scan"><input type="hidden" name="missing_request" value="1"><input type="hidden" name="missing_current" value="'.esc_attr($context['current']??'').'"><input type="hidden" name="missing_scan_proof" value="'.esc_attr($context['scan_proof']??'').'">';wp_nonce_field('zp_en_nightly_scan');
                if (function_exists('zp_suite_workspace_active')&&zp_suite_workspace_active()) echo '<input type="hidden" name="_zp_workspace" value="1">';echo '<ul>';
                foreach ($d['rows'] as $row) echo '<li><label><input type="checkbox" name="missing_post_ids[]" value="'.(int)$row['post_id'].'"'.($ready?'':' disabled').'> <a href="'.esc_url($row['pl_url']).'" target="_blank" rel="noopener">'.esc_html($row['title']).'</a></label><small> · '.esc_html($row['source']).'</small><input type="hidden" name="missing_proofs['.(int)$row['post_id'].']" value="'.esc_attr($row['source_proof']).'"><button class="button" type="submit" name="missing_post_id" value="'.(int)$row['post_id'].'"'.($ready?'':' disabled').'>Przetłumacz</button></li>';
                echo '</ul><button class="button button-primary" type="submit"'.($ready?'':' disabled').'>Przetłumacz zaznaczone (maks. 10)</button><p>Wybrane strony trafią do istniejącej kolejki Luna high. Obowiązuje ten sam limit kosztu; pełna kampania nie jest restartowana.</p></form>';
            }
        }
        echo '<p>Strony noindex, prywatne oferty, briefy i publikacje z hasłem są wykluczone. Sam odczyt tej listy nie uruchamia tłumaczeń.</p></section>';
    }
    private static function daily(): void {
        echo '<section class="zpAdmin20__card zpLunaUI__daily"><h2>Nowe strony i codzienne aktualizacje</h2>';
        if (!class_exists('ZP_EN_Rewrite_Nightly_20261011')) { echo '<p>Moduł codziennych aktualizacji Luna oczekuje wdrożenia. Ten widok nie potwierdza aktywnego harmonogramu.</p></section>';return; }
        $enabled=ZP_EN_Rewrite_Nightly_20261011::enabled();$boot=ZP_EN_Rewrite_Nightly_20261011::bootstrap();$next=wp_next_scheduled(ZP_EN_Rewrite_Nightly_20261011::DAILY);$state=self::read(ZP_EN_Rewrite_Nightly_20261011::STATE);$state=is_array($state)?$state:[];
        $label=!$enabled?'Wyłączone':(!$boot?'Oczekuje na pełne tłumaczenie i kontrolę 137 stron':(!$next?'Brak zaplanowanego zdarzenia WP-Cron':'Harmonogram włączony'));
        echo '<p><strong>'.esc_html($label).'</strong></p><p>Następne zapisane sprawdzenie: '.esc_html(self::date(self::stamp($next))).'. Plan modułu: 03:00 Europe/Warsaw. WP-Cron wymaga ruchu albo zadania na hostingu.</p><p>Nowe i zmienione publiczne strony zostaną sprawdzone przez skaner. Wpisy z hasłem, noindex, oferty i briefy są pomijane. Zmiana ustawienia nie ponawia pełnej kampanii.</p><p>Ostatni zapis skanera: '.esc_html(self::date(self::stamp($state['at']??null))).'. Wykryte nowe lub zmienione źródła: '.(is_array($state['changed']??null)?count($state['changed']):'—').'. Brak wyniku skanu nie oznacza braku nowych stron.</p>';
        echo '<form class="zpLunaUI__form" method="post" action="'.esc_url(admin_url('admin-post.php')).'"><input type="hidden" name="action" value="zp_en_nightly_settings">';wp_nonce_field('zp_en_nightly_settings');if (function_exists('zp_suite_workspace_active')&&zp_suite_workspace_active()) echo '<input type="hidden" name="_zp_workspace" value="1">';echo '<label><input type="checkbox" name="enabled" value="1"'.($enabled?' checked':'').'> Codzienne aktualizacje Luna</label><button class="button" type="submit">Zapisz ustawienie</button></form>';self::form('zp_en_nightly_scan','Sprawdź nowe i zmienione strony');echo '</section>';
    }
    /** Chrome opens the Suite shell before dispatching this native renderer. No query flag alone proves it. */
    private static function shell_open(): bool {
        if (!is_admin()||!self::owned()||!class_exists('ZP_Admin_Workspace_Chrome_20261010',false)) return false;
        foreach (debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS,16) as $frame) {
            if (($frame['class']??'')==='ZP_Admin_Workspace_Chrome_20261010'&&($frame['function']??'')==='render') return true;
        }
        return false;
    }
    public static function render(): void {
        if (!is_admin()||($_SERVER['REQUEST_METHOD']??'')!=='GET'||!self::allowed()||(function_exists('wp_doing_ajax')&&wp_doing_ajax())||(defined('REST_REQUEST')&&REST_REQUEST)) return;
        $ownShell=!self::shell_open();
        if ($ownShell) {
            if (function_exists('zp_admin20_open')) zp_admin20_open('Tłumacz EN','translations','Luna high · naturalny angielski UK/US');else echo '<div class="wrap zpAdmin20"><h1>Tłumacz EN · Luna high</h1>';
        }
        $d=self::snapshot();echo '<div id="zpLunaUI" class="zpLunaUI">';
        if (empty($d['available'])) { echo '<section class="zpAdmin20__card"><h2>Odczyt kolejki niedostępny</h2><p>Nie pokazujemy zer zamiast brakujących danych. Odśwież widok; nie uruchomiono nowej kampanii.</p></section>'; }
        else {
            echo '<section class="zpAdmin20__card zpLunaUI__overview"><div class="zpLunaUI__heading"><div><span class="zpAdmin20__pill">Luna · high</span><h2 id="zpLunaHeading">'.esc_html($d['heading']).'</h2><p>Potwierdzone strony EN, a nie liczba rozpoczętych zadań.</p></div><strong id="zpLunaState">'.esc_html($d['label']).'</strong></div><div class="zpLunaUI__progressTitle"><b id="zpLunaProgressText">'.(int)$d['completed'].' / '.(int)$d['total'].' stron</b><span id="zpLunaPercent">'.esc_html($d['percent']===null?'—':number_format($d['percent'],1,',','').' %').'</span></div><progress id="zpLunaProgress" max="'.max(1,(int)$d['total']).'" value="'.(int)$d['completed'].'" aria-label="Potwierdzone strony EN"></progress><p id="zpLunaLive" role="status" aria-live="polite">Ostatni zapis kolejki: '.esc_html(self::date($d['updated_at'])).'</p><div class="zpLunaUI__metrics">';
            foreach (['verified'=>'EN potwierdzone','translating'=>'Tłumaczenie','attention'=>'Wymaga uwagi','queued'=>'W kolejce'] as $key=>$label) echo '<div><b data-luna-count="'.esc_attr($key).'">'.(int)($d['counts'][$key]??0).'</b><span>'.esc_html($label).'</span></div>';
            echo '</div><div id="zpLunaError" class="zpLunaUI__error"'.($d['error']===''?' hidden':'').'><strong>Kolejka wymaga uwagi</strong><p>Kod: <code>'.esc_html($d['error']).'</code>. Błąd pozostaje zapisany. Nieznane płatne żądanie nie jest ponawiane automatycznie.</p></div>';
            $notice=self::error($_GET['zp_en_rewrite_notice']??'');if ($notice!==''&&$notice!=='unknown') echo '<p role="status">Ostatnia operacja: <code>'.esc_html($notice).'</code>.</p>';
            if ($d['model']!=='gpt-6-luna') echo '<p>Ta zapisana kampania nie jest kampanią Luna. Zmiana widoku nie przepisuje jej przyjętych żądań.</p>';
            echo '<div class="zpLunaUI__controls">';self::controls($d);echo '</div><p class="zpLunaUI__hint">Nowa pełna kampania oznacza nowe płatne tłumaczenie. „Wznów” korzysta z istniejącej kolejki i zachowuje jej wyniki. Ręczne korekty mają pierwszeństwo.</p></section>';
            echo '<section class="zpAdmin20__card zpLunaUI__cost"><h2>Koszt tej kampanii</h2><div class="zpLunaUI__metrics"><div><b id="zpLunaCost">'.esc_html(self::money($d['settled'])).'</b><span>Rozliczone wg danych tokenów</span></div><div><b id="zpLunaReserved">'.esc_html(self::money($d['reserved'])).'</b><span>Rezerwacje / nieznany koszt</span></div><div><b id="zpLunaLimit">'.esc_html(self::money($d['limit'])).'</b><span>Limit całej kampanii</span></div></div><p>Oszacowanie według stawek modelu, nie saldo konta OpenAI. Rezerwacje są pokazane oddzielnie; nie są ponownie dodawane do rozliczonego kosztu. Partie: <span id="zpLunaChunks">'.esc_html(($d['completed_chunks']??'—').' / '.($d['prepared_chunks']??'—')).'</span>.</p></section>';
            echo '<section class="zpAdmin20__card"><div class="zpLunaUI__heading"><div><h2>Strony w bieżącej kolejce</h2><p>Filtr „Bez mapy EN” dotyczy dostępności map w modułach. Oczekiwanie na Lunę nie oznacza braku wcześniejszego EN.</p></div><span id="zpLunaRowsCount">'.count($d['rows']).' pozycji</span></div><div class="zpLunaUI__filters"><label>Szukaj strony<input id="zpLunaSearch" type="search" placeholder="Tytuł lub adres"></label><label>Pokaż<select id="zpLunaFilter"><option value="all">Wszystkie strony</option><option value="attention">Wymagają uwagi</option><option value="active">Aktywne</option><option value="queued">W kolejce</option><option value="verified">EN potwierdzone</option><option value="missing">Bez mapy EN</option></select></label></div><div class="zpLunaUI__tableWrap"><table class="widefat zpLunaUI__table"><thead><tr><th>Strona</th><th>Postęp Luna</th><th>Mapa EN</th><th>Podgląd</th></tr></thead><tbody id="zpLunaRows">';
            foreach ($d['rows'] as $r) self::row($r);echo '</tbody></table></div><p id="zpLunaEmpty" hidden>Brak stron pasujących do filtra.</p></section>';
        }
        self::missing();self::daily();echo '<section class="zpAdmin20__card"><h2>Ręczne korekty</h2><p>Poprawki wpisane ręcznie pozostają nadrzędne względem tłumaczeń Luna.</p><a class="button" href="'.esc_url(function_exists('zp_admin20_url')?zp_admin20_url('zpl-languages',['tab'=>'overrides']):add_query_arg(['page'=>'zpl-languages','tab'=>'overrides'],admin_url('admin.php'))).'">Otwórz korekty języka</a></section></div>';
        if (class_exists('ZP_EN_Rewrite_Diagnostics_20261011')) { echo '<details class="zpAdmin20__card"><summary>Diagnostyka i kontrolowane naprawy</summary>';ZP_EN_Rewrite_Diagnostics_20261011::render();if (class_exists('ZP_EN_Serving_Recovery_20261011')) ZP_EN_Serving_Recovery_20261011::render();if (class_exists('ZP_EN_Bulk_Known_Rearm_20261012')) ZP_EN_Bulk_Known_Rearm_20261012::render();if(class_exists('ZP_EN_Semantic_Repair_20261012')) ZP_EN_Semantic_Repair_20261012::render();echo '</details>'; }
        if ($ownShell) { if (function_exists('zp_admin20_close')) zp_admin20_close();else echo '</div>'; }
    }
    private static function row(array $r): void {
        echo '<tr data-luna-state="'.esc_attr($r['state']).'" data-luna-missing="'.($r['has_en']===false?'1':'0').'"><td><strong>'.esc_html($r['title']).'</strong><small>'.esc_html($r['source']).'</small></td><td><span class="zpLunaUI__badge is-'.esc_attr($r['state']).'">'.esc_html($r['label']).'</span>'.($r['error']!==''?'<small><code>'.esc_html($r['error']).'</code></small>':'').'</td><td>'.esc_html($r['has_en']===true?'Dostępna':($r['has_en']===false?'Brak mapy':'—')).'</td><td><a href="'.esc_url($r['pl_url']).'" target="_blank" rel="noopener">PL</a> '.($r['has_en']===true?'<a href="'.esc_url($r['en_url']).'" target="_blank" rel="noopener">EN</a>':'').($r['preview_url']!==''?' <a href="'.esc_url($r['preview_url']).'" target="_blank" rel="noopener">Podgląd wersji</a>':'').'</td></tr>';
    }
    /** Replace the exact native page callback; retain its original actions/nonces/backend history. */
    public static function menu(): void {
        if (!is_admin()||!current_user_can('manage_options')) return;
        foreach (['zp-suite','tools.php'] as $parent) { $hook=get_plugin_page_hookname(self::PAGE,$parent);remove_action($hook,['ZPTE\\Admin','page']); }
        $hook=get_plugin_page_hookname(self::PAGE,'zp-suite');remove_action($hook,[self::class,'render']);add_action($hook,[self::class,'render']);
    }
    public static function assets(): void {
        if (!is_admin()||!self::owned()||!self::allowed()) return;
        wp_dequeue_script('zpte-admin');wp_dequeue_script('zp-single-translator-20261010');wp_dequeue_style('zpte-admin');
        wp_enqueue_style('zp-luna-translator-ui',ZP_SUITE_URL.'assets/admin-luna-translator-ui-20261011.css',[],'20261011.1');
        wp_enqueue_script('zp-luna-translator-ui',ZP_SUITE_URL.'assets/admin-luna-translator-ui-20261011.js',[],'20261012.active-reader2',true);
        wp_localize_script('zp-luna-translator-ui','zpLunaUIConfig',['url'=>admin_url('admin-ajax.php'),'status_action'=>self::AJAX,'status_nonce'=>wp_create_nonce(self::AJAX),'tick_nonce'=>wp_create_nonce('zp_en_rewrite_tick')]);
    }
    public static function register(): void {
        static $registered=false;if ($registered||!is_admin()) return;$registered=true;
        add_action('admin_menu',[self::class,'menu'],PHP_INT_MAX);add_action('admin_enqueue_scripts',[self::class,'assets'],PHP_INT_MAX);add_action('wp_ajax_'.self::AJAX,[self::class,'ajax']);
    }
}

/** Native-only bounded status re-arm of explicitly reviewed existing paid results. */
final class ZP_EN_Bulk_Known_Rearm_20261012 {
    private const ACTION='zp_en_rewrite_bulk_known';
    private static function allowed(): bool { return current_user_can('manage_options')&&current_user_can('edit_posts')&&current_user_can('unfiltered_html'); }
    private static function snippet(string $s): string {
        $s=preg_replace('~https?://[^\s<>]+|[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}|\b(?:sk-|resp_|req_)[A-Za-z0-9_-]+\b~iu','[ukryte]',$s)??'';
        return mb_substr(wp_strip_all_tags($s),0,160,'UTF-8');
    }
    public static function render(): void {
        if (!self::allowed()||($_SERVER['REQUEST_METHOD']??'')!=='GET'||($_GET['page']??null)!==ZP_EN_Rewrite_20261011::PAGE||!class_exists('ZP_EN_Local_Repair_20261011')) return;
        try { $data=ZP_EN_Rewrite_20261011::bulk_known_inventory();if (!$data||!$data['rows']) return;
            echo '<section class="zpAdmin20__card"><h3>Wznów sprawdzone zapisane wyniki — maksymalnie 5 stron</h3><p>Wybierz tylko partie, których wszystkie pary PL/EN już sprawdzono. Każda zgoda dotyczy dokładnego źródła, ID partii i zakresu poniżej. Nowa partia wymaga nowego sprawdzenia. Operacja odczytuje publiczne źródła PL i zmienia wyłącznie statusy kolejki; nie wysyła ponownie żądań do dostawcy.</p>';
            if (isset($_GET['zp_en_bulk_notice'])&&is_string($_GET['zp_en_bulk_notice'])) { $labels=['saved'=>'Zapisano statusy wybranych stron.','busy'=>'Worker ma blokadę. Niczego nie zmieniono; odśwież przed kolejną świadomą próbą.','blocked'=>'Dowód zmienił się lub jest niepełny. Nie potwierdzono zapisu; odśwież i sprawdź dokładne partie.'];if (isset($labels[$_GET['zp_en_bulk_notice']])) echo '<p role="status">'.esc_html($labels[$_GET['zp_en_bulk_notice']]).'</p>'; }
            echo '<form method="post" action="'.esc_url(admin_url('admin-post.php')).'"><input type="hidden" name="action" value="'.esc_attr(self::ACTION).'"><input type="hidden" name="revision" value="'.esc_attr($data['id']).'">';wp_nonce_field(self::ACTION);
            if (($_GET['zp_workspace']??null)==='1') echo '<input type="hidden" name="_zp_workspace" value="1">';
            echo '<div style="overflow-x:auto"><table class="widefat"><thead><tr><th>Źródło i partia</th><th>Wybierz</th><th>Potwierdzenie pełnego sprawdzenia</th></tr></thead><tbody>';
            foreach ($data['rows'] as $row) { echo '<tr><td>'.esc_html($row['source']);if ($row['eligible']) { echo '<br>'.esc_html($row['first'].'–'.$row['last'].' · '.$row['count'].' jednostek · partia '.$row['chunk']).'<br>PL początek: '.esc_html(self::snippet($row['first_pl'])).'<br>PL koniec: '.esc_html(self::snippet($row['last_pl'])).'</td><td><input type="checkbox" name="selected['.esc_attr($row['source']).']" value="'.esc_attr($row['fingerprint']).'" aria-label="'.esc_attr('Wybierz '.$row['source']).'"></td><td><label><input type="checkbox" name="reviewed['.esc_attr($row['source']).']" value="1"> Sprawdziłem wszystkie pary PL/EN tej dokładnej partii.</label></td>'; } else echo '<br>'.esc_html($row['reason']).'</td><td colspan="2">Brak kompletnego lokalnego dowodu — pozostaje do sprawdzenia.</td>';echo '</tr>'; }
            echo '</tbody></table></div><p><button class="button button-primary" type="submit">Wznów wybrane sprawdzone wyniki</button></p></form></section>';
        } catch(Throwable $ignored) { echo '<p>Odczyt zbiorczego wznowienia jest niedostępny.</p>'; }
    }
    public static function handle(): void {
        if (($_SERVER['REQUEST_METHOD']??'')!=='POST'||!self::allowed()||!is_string($_POST['_wpnonce']??null)||!wp_verify_nonce(wp_unslash($_POST['_wpnonce']),self::ACTION)) wp_die('Brak uprawnień.');
        $notice='blocked';try { $id=$_POST['revision']??null;$selected=$_POST['selected']??null;$reviewed=$_POST['reviewed']??null;
            if (!is_string($id)||!is_array($selected)||!is_array($reviewed)) throw new RuntimeException('forbidden');ZP_EN_Rewrite_20261011::bulk_known_rearm(wp_unslash($id),wp_unslash($selected),wp_unslash($reviewed));$notice='saved';
        } catch(Throwable $e) { if ($e->getMessage()==='busy') $notice='busy'; }
        $args=['page'=>ZP_EN_Rewrite_20261011::PAGE,'tab'=>'status','zp_en_bulk_notice'=>$notice];if (($_POST['_zp_workspace']??null)==='1') $args['zp_workspace']='1';wp_safe_redirect(add_query_arg($args,admin_url('admin.php')));exit;
    }
    public static function register(): void { add_action('admin_post_'.self::ACTION,[self::class,'handle']); }
}
ZP_EN_Bulk_Known_Rearm_20261012::register();
