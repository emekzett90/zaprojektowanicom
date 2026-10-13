<?php
if (!defined('ABSPATH')) exit;
final class ZPT2_Admin {
    public static function menu(): void {add_submenu_page('zp-suite','Tłumacz EN','Tłumacz EN v2','manage_options','zp-translator-v2',[self::class,'render']);}
    public static function legacy_url(): string {return add_query_arg(['page'=>'zp-tlumacz-en','zpt2_legacy'=>'1'],admin_url('admin.php'));}
    /** Route ordinary GET visits only; legacy form submissions and diagnostic access keep their handlers. */
    public static function legacy_target(): string {
        global $pagenow;
        if(($pagenow??'')!=='admin.php'||($_SERVER['REQUEST_METHOD']??'')!=='GET'||($_GET['page']??null)!=='zp-tlumacz-en'||($_GET['zpt2_legacy']??null)==='1'||(function_exists('wp_doing_ajax')&&wp_doing_ajax())||!ZPT2_Engine::ready()||!ZPT2_Engine::can(get_current_user_id()))return'';
        return add_query_arg(['page'=>'zp-translator-v2'],admin_url('admin.php'));
    }
    public static function redirect_legacy(): void {$target=self::legacy_target();if($target!==''){wp_safe_redirect($target);exit;}}
    public static function hide_legacy_menu(): void {if(ZPT2_Engine::ready())remove_submenu_page('zp-suite','zp-tlumacz-en');}
    private static function auth(): void {if(!ZPT2_Engine::can(get_current_user_id()))wp_die('Brak uprawnień.',403);}
    public static function handle(): void {
        self::auth();check_admin_referer('zpt2_action');$op=is_string($_POST['operation']??null)?sanitize_key($_POST['operation']):'';$notice='done';
        try{
            if(!ZPT2_Engine::ready())throw new RuntimeException('dependency_missing');
            if($op==='scan')ZPT2_Engine::start_scan();
            elseif($op==='translate'){$ids=$_POST['ids']??[];if(!is_array($ids))throw new RuntimeException('invalid_selection');ZPT2_Engine::enqueue($ids,get_current_user_id());}
            elseif($op==='promote_current')self::promote_current($_POST);
            elseif($op==='chars_limit')self::save_daily_chars($_POST['daily_chars']??null);
            elseif($op==='recheck_saved'){if(!is_string($_POST['job_id']??null)||!is_string($_POST['evidence']??null))throw new RuntimeException('invalid_selection');ZPT2_Engine::recheck_saved($_POST['job_id'],$_POST['evidence'],get_current_user_id());}
            elseif($op==='scan_toggle'){
                ZPT2_Store::lock(static function(){ZPT2_Store::put('config',['daily_enabled'=>isset($_POST['enabled'])&&$_POST['enabled']==='1','auto_enabled'=>isset($_POST['auto_enabled'])&&$_POST['auto_enabled']==='1','actor'=>get_current_user_id()]);});wp_clear_scheduled_hook(ZPT2_Engine::DAILY);ZPT2_Engine::schedule();
            }else throw new RuntimeException('invalid_operation');
        }catch(Throwable$e){$notice=ZPT2_Engine::error($e);}
        wp_safe_redirect(add_query_arg(['page'=>'zp-translator-v2','zpt2_notice'=>$notice],admin_url('admin.php')));exit;
    }
    /** Explicit settings action only; never resumes, scans, schedules or submits API work. */
    public static function save_daily_chars($value): void {
        self::auth();check_admin_referer('zpt2_action');
        if(!is_string($value)||!in_array($value,['100000','200000','400000','1000000'],true))throw new RuntimeException('invalid_daily_chars');
        ZPT2_Store::lock(static function()use($value){
            \ZPTE\Settings::update(['daily_chars'=>(int)$value]);
            if(\ZPTE\Settings::get('daily_chars')!==(int)$value)throw new RuntimeException('storage_failed');
        });
    }
    /** Shared by the Suite shell and the standalone fallback. */
    public static function render_limits(array $status,array $config): void {
        $d=$status['daily_chars']??ZPT2_Store::chars_status();$fmt=static function($n){return number_format((int)$n,0,',',' ');};
        echo '<section class="zpAdmin20__card zpt2-limits"><h2>Dzienny limit znaków</h2><p>Dzień '.esc_html($d['date']).' · Europe/Warsaw. Wspólne zużycie tłumacza legacy i v2.</p>';
        if($d['used']!==null&&$d['limit']!==null){
            echo '<p>Wykorzystano <strong>'.esc_html($fmt($d['used'])).'</strong> z '.esc_html($fmt($d['limit'])).' znaków. Pozostało: '.esc_html($fmt($d['remaining'])).'.</p><p>Legacy: '.esc_html($fmt($d['legacy_used'])).' · v2: '.esc_html($fmt($d['v2_used'])).'. Licznik obejmuje tekst zarezerwowany do wysłania w żądaniach.</p>';
        }else echo '<p role="status">Odczyt dziennego zużycia jest niedostępny. Licznik nie został zresetowany.</p>';
        echo '<form method="post" action="'.esc_url($config['action_url']).'"><input type="hidden" name="action" value="zpt2_action"><input type="hidden" name="operation" value="chars_limit"><input type="hidden" name="_wpnonce" value="'.esc_attr($config['nonce']).'"><label for="zpt2-daily-chars">Limit na dzień </label><select name="daily_chars" id="zpt2-daily-chars" required>';
        if(!in_array($d['limit'],[100000,200000,400000,1000000],true))echo '<option value="" selected disabled>'.($d['limit']===null?'Wybierz limit':'Obecny: '.esc_html($fmt($d['limit']))).'</option>';
        foreach([100000,200000,400000,1000000]as$limit)echo '<option value="'.esc_attr($limit).'"'.($d['limit']===$limit?' selected':'').'>'.esc_html($fmt($limit)).' znaków</option>';
        echo '</select> <button class="button">Zapisz limit</button><p>Zapis dotyczy tylko limitu znaków. Nie resetuje zużycia ani budżetu 15 USD i nie uruchamia tłumaczenia. Istniejąca kolejka nadal działa według swojego harmonogramu.</p></form><p class="band-meta"><a href="'.esc_url(self::legacy_url()).'">Diagnostyka poprzedniej kolejki</a></p></section>';
        self::render_diagnostics();
    }
    /** Administrator-only, read-only view of persisted evidence; no API, enqueue or repair. */
    public static function diagnostic_checks(string $pl,$raw,string $kind,array $context=[]): array {return ZPT2_Source::checks($pl,$raw,$kind,$context);}
    public static function diagnostics(): array {
        self::auth();$index=ZPT2_Store::get('jobs');$index=array_filter($index,static fn($r)=>in_array($r['state']??'',['attention','unknown'],true));uasort($index,static fn($a,$b)=>($b['updated_at']??0)<=>($a['updated_at']??0));$out=[];
        foreach(array_slice($index,0,5,true)as$id=>$row){
            $job=ZPT2_Store::get('job_'.$id);if(!$job||!ZPT2_Engine::can(get_current_user_id(),(int)($job['post_id']??0)))continue;
            try{ZPT2_Source::descriptor((int)$job['post_id']);}catch(Throwable$e){$out[]=['source'=>$job['source'],'error'=>ZPT2_Engine::error($e)];continue;}
            $missing=array_diff_key($job['snapshot']['units'],$job['effective']);$report=['job_id'=>$id,'source'=>$job['source'],'state'=>$job['state'],'error'=>$job['error']??'','missing_total'=>count($missing),'serving_diagnostic'=>$job['serving_diagnostic']??null,'units'=>[]];
            $requests=[];foreach(array_slice($job['requests'],0,160)as$rid)$requests[$rid]=ZPT2_Store::get('request_'.$rid);
            foreach(array_slice($missing,0,10,true)as$uid=>$unit){
                $item=['unit_id'=>$uid,'pl'=>$unit['pl'],'kind'=>$unit['kind'],'contexts'=>$unit['contexts'],'repair_attempts'=>$job['repair_attempts'][$uid]??0,'attempts'=>[]];
                foreach($requests as$rid=>$r){if(!isset($r['items'][$uid]))continue;$terminal=$r['terminal']??[];$receipt=ZPT2_Store::get('receipt_'.$rid);$en=$terminal['result'][$uid]??null;
                    $item['attempts'][]=['request_id'=>$rid,'parent'=>$r['parent']??'','state'=>$r['state']??'','response_id'=>$receipt['response_id']??($r['response_id']??''),'receipt_matches'=>($receipt['job']??null)===$id&&($receipt['id']??null)===$rid&&($receipt['request_hash']??null)===($r['hash']??null)&&($terminal['receipt']??null)===$receipt,'terminal_status'=>$terminal['status']??'','terminal_hash'=>$terminal?ZPT2_Store::hash($terminal):'','en'=>is_string($en)?mb_substr($en,0,64000,'UTF-8'):null,'checks'=>self::diagnostic_checks($unit['pl'],$en,$unit['kind'],$job['snapshot'])];
                }$report['units'][]=$item;
            }
            try{$known=ZPT2_Engine::saved_context($id,get_current_user_id());$report['recheck_evidence']=$known['fingerprint'];}catch(Throwable$e){$report['recheck_unavailable']=ZPT2_Engine::error($e);}
            $out[]=$report;
        }return$out;
    }
    public static function render_diagnostics(): void {
        $rows=self::diagnostics();if(!$rows)return;
        echo '<details class="zpt2-diagnostics"><summary>Zatrzymane fragmenty — zapisane odpowiedzi</summary><p>Oryginał PL, zapisane odpowiedzi i wynik obecnego walidatora. Samo otwarcie niczego nie wznawia.</p><pre style="white-space:pre-wrap;overflow-wrap:anywhere;max-height:65vh;overflow:auto">'.esc_html(wp_json_encode($rows,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)).'</pre>';
        foreach($rows as$row)if(!empty($row['recheck_evidence'])){echo '<form method="post" action="'.esc_url(admin_url('admin-post.php')).'">';self::form('recheck_saved');echo '<input type="hidden" name="job_id" value="'.esc_attr($row['job_id']).'"><input type="hidden" name="evidence" value="'.esc_attr($row['recheck_evidence']).'"><p>'.esc_html($row['source']).'</p><button class="button">Sprawdź zapisane odpowiedzi — bez nowego API</button><p>Sprawdzi aktualne źródło i ostatnie kompletne odpowiedzi. Wznowi publikację tylko po pełnym PASS. Nie zeruje prób i nie tworzy żądań.</p></form>';}
        echo '</details>';
    }
    /** Authenticated single-post GET observation; never admits work or touches provider state. */
    public static function preflight(int $post): array {self::auth();if(($_SERVER['REQUEST_METHOD']??'')!=='GET')throw new RuntimeException('forbidden');return self::inspect_current($post);}
    private static function inspect_current(int $post): array {
        self::auth();if($post<1||!ZPT2_Engine::ready()||!ZPT2_Engine::can(get_current_user_id(),$post))throw new RuntimeException('forbidden');
        $snap=ZPT2_Source::capture($post);$source=$snap['source'];$key=hash('sha256',$source);$active=ZPT2_Engine::active($source);$ref=ZPT2_Engine::legacy_reference($source);$legacy=ZP_EN_Rewrite_20261011::approved_page($source);
        $stored=ZPT2_Store::get('legacy_'.$key);$baseline=ZPT2_Store::get('baseline_'.$key);$priorSnapshot=$active['snapshot']??($ref['snapshot']??[]);$reused=0;$rows=[];$proof=[];
        foreach($snap['units']as$uid=>$u){
            // Exactly the admission comparison: full unit (source/kind/contexts), accepted string, current factual validator.
            $prior=$active['snapshot']['units'][$uid]??($ref['snapshot']['units'][$uid]??null);$accepted=$active['effective'][$uid]??($ref['map'][$u['pl']]??null);$same=$prior===$u;$string=is_string($accepted);
            $checks=$same&&$string?ZPT2_Source::checks($u['pl'],$accepted,$u['kind'],$snap):null;$reuse=$same&&$string&&($checks['valid']??false);if($reuse)$reused++;
            $proof[$uid]=['same_unit'=>$same,'accepted_string'=>$string,'accepted_hash'=>$string?hash('sha256',$accepted):null,'checks'=>$checks,'reuse'=>$reuse];
            if(count($rows)<400)$rows[]=['unit_id'=>$uid,'current'=>$u,'prior'=>$prior,'accepted_en'=>$string?$accepted:null]+$proof[$uid];
        }
        $version=$active['id']??($ref['revision']??'');$revision=$active?ZPT2_Store::get('job_'.$version):($ref?ZPT2_Store::legacy(ZPT2_Store::legacy_prefix().'r_'.$version):[]);
        $fields=array_flip(['source','en','post_id','title','units','native','hash','body_hash','meta_hash','captured_at']);$safeSnapshot=static fn($s)=>is_array($s)?array_intersect_key($s,$fields):[];
        $map=$active?$active['map']:($legacy['map']?:($ref['map']??[]));$total=count($snap['units']);
        return ['post_id'=>$post,'source'=>$source,'current_snapshot'=>$safeSnapshot($snap),'prior_kind'=>$active?'active':($ref?'verified_legacy_reference':'none'),'prior_revision'=>$version,
            'prior_snapshot'=>$safeSnapshot($priorSnapshot),'baseline_snapshot'=>$safeSnapshot($baseline),'baseline_source_match'=>($baseline['source']??null)===$source,'prior_source_match'=>($priorSnapshot['source']??null)===$source,'prior_post_match'=>($priorSnapshot['post_id']??null)===$post,
            'prior_en_match'=>($priorSnapshot['en']??null)===$snap['en'],'prior_native_match'=>($priorSnapshot['native']??null)===$snap['native'],'prior_snapshot_hash_match'=>($priorSnapshot['hash']??null)===$snap['hash'],
            'active_digest'=>$active['digest']??null,'verified_legacy_reference'=>(bool)$ref,'legacy_map_hash_verified'=>$ref?ZPT2_Store::hash($ref['map'])===($stored['map_hash']??null):false,'stored_legacy_map_hash'=>$stored['map_hash']??null,
            'model'=>$revision['model']??($revision['policy']['model']??null),'reasoning'=>$revision['reasoning']??($revision['policy']['reasoning']??null),'policy_hash'=>$revision['policy_hash']??(isset($revision['policy'])?ZPT2_Store::hash($revision['policy']):null),
            'map_entries'=>count($map),'map_hash'=>ZPT2_Store::hash($map),'total_units'=>$total,'reusable_units'=>$reused,'new_api_units'=>$total-$reused,'all_units_reusable'=>$total>0&&$reused===$total,
            'unit_proof_hash'=>ZPT2_Store::hash($proof),'unit_reports'=>$rows,'unit_reports_total'=>$total,'unit_reports_truncated'=>$total>count($rows),'read_only'=>true];
    }
    public static function promotion_eligible(array $r): bool {
        $total=$r['total_units']??0;$needed=$r['new_api_units']??-1;
        return is_int($total)&&$total>0&&is_int($needed)&&$needed>=0&&$needed<=20&&$needed*20<=$total&&($r['reusable_units']??-1)===$total-$needed
            &&!empty($r['prior_source_match'])&&!empty($r['prior_post_match'])&&!empty($r['prior_en_match'])&&!empty($r['prior_native_match'])&&!empty($r['prior_snapshot_hash_match'])
            &&(($r['prior_kind']??'')==='active'&&!empty($r['active_digest'])||($r['prior_kind']??'')==='verified_legacy_reference'&&!empty($r['verified_legacy_reference'])&&!empty($r['legacy_map_hash_verified']));
    }
    /** Existing admission owns policy, provider requests and budget; this POST only guards one reviewed source. */
    public static function promote_current(array $input): void {
        self::auth();check_admin_referer('zpt2_action');$raw=$input['post_id']??null;
        if(($_SERVER['REQUEST_METHOD']??'')!=='POST'||!is_string($raw)||!preg_match('/^[1-9][0-9]{0,18}$/D',$raw)||(string)(int)$raw!==$raw)throw new RuntimeException('invalid_selection');
        foreach(['source_hash','native_hash','map_hash']as$k)if(!is_string($input[$k]??null)||!preg_match('/^[a-f0-9]{64}$/D',$input[$k]))throw new RuntimeException('invalid_selection');
        if(!is_string($input['prior_revision']??null)||!preg_match('/^[a-f0-9]{32}$/D',$input['prior_revision']))throw new RuntimeException('invalid_selection');
        ZPT2_Store::lock(static function()use($input,$raw){$r=self::inspect_current((int)$raw);
            if(($r['current_snapshot']['hash']??null)!==$input['source_hash']||($r['current_snapshot']['native']??null)!==$input['native_hash'])throw new RuntimeException('source_changed');
            if($r['map_hash']!==$input['map_hash']||$r['prior_revision']!==$input['prior_revision'])throw new RuntimeException('reference_changed');
            if(!self::promotion_eligible($r))throw new RuntimeException('promotion_unavailable');ZPT2_Store::budget();ZPT2_Engine::enqueue([(int)$raw],get_current_user_id());
        });
    }
    public static function render_preflight(): bool {
        if(!array_key_exists('zpt2_preflight',$_GET))return false;self::auth();
        try{$raw=$_GET['zpt2_preflight'];if(($_SERVER['REQUEST_METHOD']??'')!=='GET'||!is_string($raw)||!preg_match('/^[1-9][0-9]{0,18}$/D',$raw)||(string)(int)$raw!==$raw)throw new RuntimeException('invalid_selection');$report=self::preflight((int)$raw);}
        catch(Throwable$e){$report=['error'=>ZPT2_Engine::error($e),'read_only'=>true];}
        echo '<div class="wrap"><h1>Dowód ponownego użycia zapisanych tłumaczeń</h1><p>Odczyt jednej strony. Pobiera aktualne publiczne źródło PL i porównuje zapisane jednostki; niczego nie uruchamia ani nie zapisuje.</p><pre id="zpt2-preflight-result" style="white-space:pre-wrap;overflow-wrap:anywhere">'.esc_html(wp_json_encode($report,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)).'</pre>';if(self::promotion_eligible($report)){echo '<form method="post" action="'.esc_url(admin_url('admin-post.php')).'">';self::form('promote_current');foreach(['post_id'=>$report['post_id'],'source_hash'=>$report['current_snapshot']['hash'],'native_hash'=>$report['current_snapshot']['native'],'map_hash'=>$report['map_hash'],'prior_revision'=>$report['prior_revision']]as$k=>$v)echo '<input type="hidden" name="'.esc_attr($k).'" value="'.esc_attr($v).'">';echo '<p>Do tłumaczenia: '.esc_html($report['new_api_units']).' z '.esc_html($report['total_units']).' fragmentów. Pozostałe wykorzystają zapisane EN.</p><button class="button button-primary">Utrwal EN przed zmianą źródła — tłumacz tylko brakujące fragmenty</button></form>';}echo '</div>';return true;
    }
    public static function ajax(): void {self::auth();check_ajax_referer('zpt2_status');wp_send_json_success(ZPT2_Engine::status());}
    public static function form(string $operation): void {echo '<input type="hidden" name="action" value="zpt2_action"><input type="hidden" name="operation" value="'.esc_attr($operation).'">';wp_nonce_field('zpt2_action');}
    public static function label(string $state): string {return ['new'=>'Bez EN','changed'=>'Zmienione','published'=>'Opublikowane EN','legacy'=>'Gotowe EN','queued'=>'W kolejce','translating'=>'Tłumaczenie','repairing'=>'Naprawa fragmentu','checking'=>'Sprawdzanie publikacji','deferred'=>'Odroczone — limit znaków','attention'=>'Wymaga sprawdzenia','unknown'=>'Nieznany wynik żądania'][$state]??$state;}
    public static function render(): void {
        self::auth();if(!ZPT2_Engine::ready()){echo '<div class="wrap"><h1>Tłumacz EN</h1><p>Wymagany aktywny ZP Suite z adapterem v2.</p></div>';return;}
        if(self::render_preflight())return;
        $s=ZPT2_Engine::status();$c=['action_url'=>admin_url('admin-post.php'),'nonce'=>wp_create_nonce('zpt2_action'),'status_nonce'=>wp_create_nonce('zpt2_status'),'ajax_url'=>admin_url('admin-ajax.php')];
        if(function_exists('zp_suite_render_translator_v2')){zp_suite_render_translator_v2($s,$c);return;}
        echo '<div class="wrap" id="zpt2-admin"><h1>Tłumacz EN</h1><p>Nowe i zmienione treści · Luna low · wspólny budżet</p>';
        if(isset($_GET['zpt2_notice'])&&is_string($_GET['zpt2_notice']))echo '<p role="status">'.esc_html(sanitize_key($_GET['zpt2_notice'])).'</p>';
        echo '<div class="zpt2-summary"><p>Gotowe tłumaczenia pozostają dostępne podczas pracy. Skan sprawdza główną treść i pola SEO.</p><p id="zpt2-live" aria-live="polite">';
        if($s['budget'])echo 'Koszt '.esc_html(number_format($s['budget']['settled']/1e6,3)).' USD · zarezerwowane '.esc_html(number_format($s['budget']['reserved']/1e6,3)).' USD · limit '.esc_html(number_format($s['budget']['limit']/1e6,2)).' USD';else echo 'Odczyt budżetu niedostępny';echo '</p></div>';
        echo '<div class="zpt2-toolbar"><form method="post" action="'.esc_url($c['action_url']).'">';self::form('scan');echo '<button class="button">Sprawdź teraz</button></form><form method="post" action="'.esc_url($c['action_url']).'">';self::form('scan_toggle');
        echo '<label><input type="checkbox" name="enabled" value="1" '.checked($s['daily_enabled'],true,false).'> Codzienny skan o 03:00 (Warszawa)</label> <label><input type="checkbox" name="auto_enabled" value="1" '.checked($s['auto_enabled'],true,false).'> Codziennie skanuj i tłumacz nowe oraz zmienione</label> <button class="button">Zapisz</button></form></div>';
        self::render_limits($s,$c);
        echo '<p>Następny skan: '.($s['next_scan']?esc_html(wp_date('d.m.Y H:i',$s['next_scan'],new DateTimeZone('Europe/Warsaw'))):'wyłączony').'. '.($s['eta_seconds']===null?'Czas realizacji pojawi się po pierwszych pomiarach.':'Mediana zmierzonych zadań: '.esc_html($s['eta_seconds']).' s.').'</p>';
        echo '<label>Szukaj strony <input id="zpt2-search" type="search"></label><form method="post" action="'.esc_url($c['action_url']).'">';self::form('translate');echo '<table class="widefat striped zpt2-table"><thead><tr><th>Wybierz</th><th>Strona</th><th>Stan</th><th>Postęp</th></tr></thead><tbody>';
        foreach($s['rows']as$r){echo '<tr data-zpt2-row="'.esc_attr($r['post_id']).'"><td><input type="checkbox" name="ids[]" value="'.esc_attr($r['post_id']).'" aria-label="'.esc_attr($r['title']).'"></td><td><a href="'.esc_url(home_url($r['source'])).'">'.esc_html($r['title']).'</a><br><small>'.esc_html($r['source']).'</small></td><td class="zpt2-state">'.esc_html(self::label($r['state'])).(!empty($r['error'])?'<br><small>'.esc_html($r['error']).'</small>':'').'</td><td class="zpt2-progress">'.($r['total']!==null?esc_html($r['done'].' / '.$r['total']):'—').'</td></tr>';}
        echo '</tbody></table><p><button class="button button-primary">Przetłumacz zaznaczone (maks. 10)</button></p></form><details class="zpt2-diagnostics"><summary>Diagnostyka</summary><p>Ostatni worker: '.($s['worker_last']?esc_html(wp_date('d.m H:i:s',$s['worker_last'])):'brak pomiaru').'. Serwer powinien wywoływać wp-cron.php co minutę. Harmonogram WP-Cron sam nie potwierdza konfiguracji hostingu.</p><p>Pierwszy skan zapisuje bazę obecnych map; automatyczne tłumaczenie uruchom po odbiorze pierwszej nowej strony.</p></details></div>';
        echo '<script>(function(c){var root=document.getElementById("zpt2-admin"),search=document.getElementById("zpt2-search");search.addEventListener("input",function(){root.querySelectorAll("tbody tr").forEach(function(r){r.hidden=!r.textContent.toLowerCase().includes(search.value.toLowerCase())})});root.querySelectorAll("input[name=\"ids[]\"]").forEach(function(n){n.addEventListener("change",function(){if(root.querySelectorAll("input[name=\"ids[]\"]:checked").length>10)n.checked=false})});var count=0;async function poll(){if(++count>240)return;try{var q=new URLSearchParams({action:"zpt2_status",_ajax_nonce:c.status_nonce});var r=await fetch(c.ajax_url+"?"+q,{credentials:"same-origin"});var j=await r.json();if(j.success&&j.data){j.data.rows.forEach(function(x){var r=root.querySelector("[data-zpt2-row=\""+x.post_id+"\"]");if(r&&x.total!==null)r.querySelector(".zpt2-progress").textContent=x.done+" / "+x.total});}}catch(e){}setTimeout(poll,5000)}poll()})('.wp_json_encode($c,JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT).');</script>';
    }
}
