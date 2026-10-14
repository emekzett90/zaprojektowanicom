<?php
/** Read-only native queue diagnostics. Never schedules, retries, runs providers or changes queue state. */
if (!defined('ABSPATH')) { exit; }
final class ZP_Admin_Queue_Health_20261010 {
    private const PAGES=['zp-suite-prepare','zp-suite-article-photos'];
    private const SPECS=[
        'auto'=>['label'=>'Dobór tematów AUTO','class'=>'ZP_Content_Auto_Preparation_20261010','option'=>'zps_content_prep_auto_20261010_state','lock'=>'zps_content_prep_auto_20261010_lock','worker'=>'zp_prep_auto_worker_20261010','watchdog'=>'zp_prep_auto_watchdog_20261010','single'=>true,'background'=>['queued','running'],'manual'=>[]],
        'prepare'=>['label'=>'Teksty i nowe zdjęcia','class'=>'ZP_Content_Preparation_20261010','option'=>'zps_content_prep_20261010_data','lock'=>'zps_content_prep_20261010_lock','worker'=>'zp_prep_worker_20261010','watchdog'=>'zp_prep_watchdog_20261010','single'=>false,'background'=>['queued','running'],'manual'=>['publishing']],
        'photos'=>['label'=>'Zdjęcia wcześniejszych artykułów','class'=>'ZP_Article_Photo_Refresh_20261010','option'=>'zps_article_photos_20261010_state','lock'=>'zps_article_photos_20261010_lock','worker'=>'zp_article_photos_worker_20261010','watchdog'=>'zp_article_photos_watchdog_20261010','single'=>false,'background'=>['queued','running','applying'],'manual'=>[]],
    ];
    private const STATES=['queued','running','applying','ready','scheduled','published','complete','applied','needs_retry','needs_attention','publish_attention','policy_hold','schedule_attention','rolled_back','cancelled','publishing'];
    private const STAGES=['research','planning','factory','text','cover','inline','inline2','write','apply','rollback','ready','complete','publication'];
    /** States that wait for the owner's decision in the module (never retried automatically). policy_hold is a kept decision, not a problem. */
    private const REVIEW=['needs_retry','needs_attention','publish_attention','schedule_attention'];
    private const ISSUES=[
        'module_missing'=>'Moduł nie jest dostępny w tym żądaniu.',
        'data_invalid'=>'Stan kolejki ma nieobsługiwaną strukturę.',
        'jobs_limited'=>'Odczyt ograniczono do 100 zadań.',
        'worker_callback_missing'=>'Brakuje zarejestrowanego callbacku wykonawcy.',
        'watchdog_callback_missing'=>'Brakuje callbacku kontrolującego przerwane zadania.',
        'worker_missing'=>'Oczekujące zadanie nie ma zaplanowanego wykonawcy.',
        'watchdog_missing'=>'Aktywna kolejka nie ma zaplanowanej kontroli.',
        'worker_overdue'=>'Termin wykonawcy minął ponad 2 minuty temu.',
        'watchdog_overdue'=>'Termin kontroli minął ponad 5 minut temu.',
        'queue_stale'=>'Aktywna kolejka nie zmieniła zapisanego stanu od ponad 15 minut.',
        'running_overdue'=>'Zapisany etap pracy trwa ponad 10 minut.',
        'lock_invalid'=>'Zapisana blokada ma nieobsługiwaną strukturę.',
        'lock_expired'=>'Zapisana blokada wygasła; kolejny wykonawca powinien sprawdzić jej przejęcie.',
        'timestamp_future'=>'Znacznik stanu jest późniejszy od aktualnego zegara.',
        'needs_review'=>'Zadanie ma stan wymagający kontroli w swoim panelu.',
        'publish_unconfirmed'=>'Ręczna publikacja nie została potwierdzona (żądanie przerwane). Sprawdź wynik w module; nic nie jest ponawiane automatycznie.',
        'wp_cron_disabled'=>'DISABLE_WP_CRON jest włączone; wykonywanie wymaga zewnętrznego harmonogramu.',
    ];
    public static function allowed(): bool {return is_admin() && current_user_can('manage_options') && !((function_exists('wp_doing_ajax')&&wp_doing_ajax()) || (function_exists('wp_doing_cron')&&wp_doing_cron()) || (defined('REST_REQUEST')&&REST_REQUEST));}
    /**
     * 3.2.0: a job in a review state whose WordPress post is already published needs no decision on the dashboard
     * (e.g. a draft published by hand after a failed scheduling keeps schedule_attention). The module page still shows it.
     * A publication that the module could not confirm (publish_attention) keeps counting.
     */
    private static function waits(array $j,string $state): bool {
        if(!in_array($state,self::REVIEW,true))return false;
        if($state==='publish_attention')return true;
        $post=is_int($j['post_id']??null)||ctype_digit((string)($j['post_id']??''))?(int)$j['post_id']:0;
        return !($post>0&&get_post_status($post)==='publish');
    }
    private static function stamp($value): ?int {return is_int($value)&&$value>0&&$value<=4102444800?$value:null;}
    private static function enum($value,array $allowed,string $fallback='unknown'): string {return is_string($value)&&in_array($value,$allowed,true)?$value:$fallback;}
    private static function response($value): bool {return is_string($value)&&preg_match('/^resp_[A-Za-z0-9_-]{1,180}$/D',$value)===1;}
    private static function callbacks(string $hook): int {
        global $wp_filter;$h=$wp_filter[$hook]??null;$groups=is_object($h)?($h->callbacks??null):null;if(!is_array($groups))return 0;$count=0;
        foreach($groups as$callbacks){if(is_array($callbacks))$count+=count($callbacks);if($count>=1000)return 1000;}return$count;
    }
    private static function event(string $hook): array {return ['next_at'=>self::stamp(wp_next_scheduled($hook)),'callbacks'=>self::callbacks($hook)];}
    private static function lock(string $option,int $now): array {
        $raw=get_option($option,null);if($raw===null||$raw===false||$raw==='')return ['status'=>'none','expires_at'=>null];
        $v=is_string($raw)&&strlen($raw)<=2048?json_decode($raw,true):null;$at=is_array($v)?self::stamp($v['expires']??null):null;
        if(!$at||!is_string($v['token']??null)||$v['token']===''||strlen($v['token'])>200)return ['status'=>'invalid','expires_at'=>null];
        return ['status'=>$at>$now?'held':'expired','expires_at'=>$at];
    }
    private static function queue(string $key,array $spec,int $now,bool $disabled): array {
        $raw=get_option($spec['option'],null);$valid=$raw===null||is_array($raw);$issues=[];$jobs=[];
        if(!$valid)$issues[]='data_invalid';elseif($spec['single']){if($raw!==null&&$raw!==[])$jobs=[$raw];}elseif(is_array($raw)){if(isset($raw['jobs'])&&!is_array($raw['jobs'])){$valid=false;$issues[]='data_invalid';}else$jobs=$raw['jobs']??[];}
        if(count($jobs)>100){$jobs=array_slice($jobs,0,100,true);$issues[]='jobs_limited';}
        $complete=$valid;$counts=$valid?array_fill_keys(self::STATES,0):null;$selected=null;$priority=100;$latest=null;$latestAt=-1;$queued=0;$active=0;$known=0;$review=0;$held=0;$manual=0;
        foreach($jobs as$j){if(!is_array($j)){$complete=false;$issues[]='data_invalid';continue;}$state=self::enum($j['state']??null,self::STATES);if($state==='unknown'){$complete=false;$issues[]='data_invalid';}else$counts[$state]++;
            $updated=self::stamp($j['updated_at']??null);if(($updated??0)>$latestAt){$latest=$j;$latestAt=$updated??0;}
            if($state==='queued')$queued++;if(in_array($state,$spec['background'],true))$active++;if(in_array($state,$spec['manual'],true))$manual++;$wait=self::waits($j,$state);if($wait)$review++;if($state==='policy_hold')$held++;if(self::response($j['response_id']??null))$known++;
            $p=$state!=='queued'&&in_array($state,$spec['background'],true)?0:($state==='queued'?1:(in_array($state,$spec['manual'],true)?2:($wait?3:100)));if($p<$priority){$selected=$j;$priority=$p;}
        }
        $selected=$selected??$latest;$state=$selected===null?($valid?'idle':'unknown'):self::enum($selected['state']??null,self::STATES);$stage=$selected===null?'none':self::enum($selected['stage']??null,self::STAGES);$updated=self::stamp($selected['updated_at']??null);$started=self::stamp($selected['started_at']??null);$poll=self::stamp($selected['response_next_poll_at']??null);$age=$updated===null?null:max(0,$now-$updated);$lock=self::lock($spec['lock'],$now);$worker=self::event($spec['worker']);$watchdog=self::event($spec['watchdog']);
        $stateResponse=self::response($selected['response_id']??null);$receiptResponse=false;
        if($key==='auto'&&is_string($selected['id']??null)&&strlen($selected['id'])<=200){$receipts=get_option('zps_content_prep_auto_20261010_response_receipts',[]);$r=is_array($receipts)?($receipts[$selected['id']]??null):null;$receiptResponse=is_array($r)&&self::response($r['response_id']??null);}
        if(!class_exists($spec['class']))$issues[]='module_missing';if($disabled&&$active)$issues[]='wp_cron_disabled';
        if($active){if(!$worker['callbacks'])$issues[]='worker_callback_missing';if(!$watchdog['callbacks'])$issues[]='watchdog_callback_missing';
            if($worker['next_at']===null){if($complete&&$queued&&$active===$queued&&$lock['status']!=='held')$issues[]='worker_missing';}elseif($worker['next_at']<$now-120&&$lock['status']!=='held')$issues[]='worker_overdue';
            if($watchdog['next_at']===null)$issues[]='watchdog_missing';elseif($watchdog['next_at']<$now-300&&$lock['status']!=='held')$issues[]='watchdog_overdue';
            if($age!==null&&$age>900&&($poll===null||$poll<=$now))$issues[]='queue_stale';
            if($state!=='queued'&&in_array($state,$spec['background'],true)&&$started!==null&&$started<$now-600)$issues[]='running_overdue';
            if(in_array($lock['status'],['invalid','expired'],true))$issues[]='lock_'.$lock['status'];
        }
        if($updated!==null&&$updated>$now+300)$issues[]='timestamp_future';if($manual&&$lock['status']!=='held')$issues[]='publish_unconfirmed';if($review)$issues[]='needs_review';
        return ['label'=>$spec['label'],'state'=>$state,'stage'=>$stage,'counts'=>$complete?$counts:null,'queued'=>$complete?$queued:null,'active'=>$complete?$active:null,'updated_at'=>$updated,'age_seconds'=>$age,'started_at'=>$started,'next_poll_at'=>$poll,'known_response'=>$stateResponse||$receiptResponse,'response_presence'=>$stateResponse?($receiptResponse?'both':'state'):($receiptResponse?'receipt':'none'),'known_response_jobs'=>$known,'review'=>$complete?$review:null,'held'=>$complete?$held:null,'manual'=>$complete?$manual:null,'worker'=>$worker,'watchdog'=>$watchdog,'lock'=>$lock,'issues'=>array_values(array_unique($issues))];
    }
    /**
     * Dashboard sentence for one queue, or null when nothing needs the owner. 'fault': the queue cannot work;
     * 'cron': background work is waiting for WP-Cron (short delays are left to the module page); 'review': a job waits
     * for the owner's decision in the module (paid steps are never repeated automatically).
     */
    public static function explain(array $q): ?array {
        $issues=array_values(array_diff((array)($q['issues']??[]),['jobs_limited']));if(!$issues)return null;
        $fault=array_values(array_intersect($issues,['data_invalid','module_missing','worker_callback_missing','watchdog_callback_missing','lock_invalid','timestamp_future']));
        // Without both events and without a lock nothing will ever reschedule the queue: that is a fault, not a delay.
        if(!$fault&&in_array('worker_missing',$issues,true)&&in_array('watchdog_missing',$issues,true)&&(($q['lock']['status']??'')!=='held'))$fault=['worker_missing'];
        // Short delays (the dashboard request itself starts WP-Cron, a lock collision waits for the watchdog) and
        // DISABLE_WP_CRON with a server cron are left to the module page; only a queue unchanged for 15 minutes counts.
        $cron=array_values(array_intersect($issues,['queue_stale','running_overdue']));
        $review=in_array('needs_review',$issues,true)?max(1,(int)($q['review']??1)):0;$manual=in_array('publish_unconfirmed',$issues,true);
        $n=$review%10;$waits=$review===1?'1 zadanie czeka':($n>=2&&$n<=4&&($review%100<12||$review%100>14)?$review.' zadania czekają':$review.' zadań czeka');
        if($fault)return ['kind'=>'fault','text'=>'Kolejka zatrzymała się: '.self::ISSUES[$fault[0]]];
        if($cron)return ['kind'=>'cron','text'=>'Praca w tle czeka na WP-Cron: '.self::ISSUES[$cron[0]].($review?' Poza tym '.$waits.' na Twoją decyzję.':'')];
        if($manual)return ['kind'=>'review','text'=>self::ISSUES['publish_unconfirmed']];
        if($review)return ['kind'=>'review','text'=>ucfirst($waits).' na Twoją decyzję (np. ponowienie etapu albo sprawdzenie publikacji). Nic nie jest ponawiane automatycznie.'];
        return null;
    }
    /** Only allowlisted, typed operational fields; no task IDs, prompts, response IDs or credentials. */
    public static function data(?int $now=null): array {
        if(!self::allowed())return ['allowed'=>false];$now=$now??time();$disabled=defined('DISABLE_WP_CRON')&&(bool)constant('DISABLE_WP_CRON');$out=['allowed'=>true,'checked_at'=>$now,'checked_warsaw'=>wp_date('Y-m-d H:i:s',$now,new DateTimeZone('Europe/Warsaw')),'cron'=>['disabled'=>$disabled,'alternate'=>defined('ALTERNATE_WP_CRON')&&(bool)constant('ALTERNATE_WP_CRON')],'queues'=>[]];
        foreach(self::SPECS as$key=>$spec)$out['queues'][$key]=self::queue($key,$spec,$now,$disabled);return$out;
    }
    private static function at(?int $stamp): string {return $stamp===null?'Brak':wp_date('d.m.Y H:i:s',$stamp,new DateTimeZone('Europe/Warsaw')).' Warszawa';}
    public static function render(): void {
        $page=is_string($_GET['page']??null)?wp_unslash($_GET['page']):'';if(!in_array($page,self::PAGES,true)||($_SERVER['REQUEST_METHOD']??'')!=='GET'||!self::allowed())return;$d=self::data();
        echo '<details class="notice notice-info" style="padding:12px"><summary><b>Kolejki i automatyzacje · diagnostyka bez kosztu</b></summary><p>Odczyt: '.esc_html($d['checked_warsaw']).' Warszawa. DISABLE_WP_CRON: '.($d['cron']['disabled']?'tak':'nie').'; ALTERNATE_WP_CRON: '.($d['cron']['alternate']?'tak':'nie').'.</p><p>Terminy są zapisanym harmonogramem, a nie dowodem wykonania. Obecność odpowiedzi oznacza zapisane potwierdzenie; jej brak nie rozstrzyga rozliczenia wcześniejszego żądania.</p><div style="overflow:auto"><table class="widefat striped"><thead><tr><th>Kolejka</th><th>Stan / etap</th><th>Ostatnia zmiana</th><th>Wykonawca</th><th>Kontrola</th><th>Blokada / odpowiedź</th></tr></thead><tbody>';
        foreach($d['queues'] as$q){echo '<tr><td>'.esc_html($q['label']).'<br>Oczekuje: '.($q['queued']===null?'—':(int)$q['queued']).'; aktywne: '.($q['active']===null?'—':(int)$q['active']).'</td><td>'.esc_html($q['state'].' / '.$q['stage']).'</td><td>'.esc_html(self::at($q['updated_at'])).'<br>Wiek zapisu: '.($q['age_seconds']===null?'—':(int)floor($q['age_seconds']/60).' min').'</td><td>'.esc_html(self::at($q['worker']['next_at'])).'<br>Callbacki: '.(int)$q['worker']['callbacks'].'</td><td>'.esc_html(self::at($q['watchdog']['next_at'])).'<br>Callbacki: '.(int)$q['watchdog']['callbacks'].'</td><td>'.esc_html($q['lock']['status']).'<br>Wygasa: '.esc_html(self::at($q['lock']['expires_at'])).'<br>Znana odpowiedź: '.($q['known_response']?'tak':'nie').' ('.esc_html($q['response_presence']).')</td></tr>';
            if($q['issues']){echo '<tr><td colspan="6"><ul>';foreach($q['issues']as$issue)echo '<li>'.esc_html(self::ISSUES[$issue]).'</li>';echo '</ul></td></tr>';}
        }echo '</tbody></table></div><p>Ten odczyt nie uruchamia ani nie ponawia zadania. Szczegóły i zatwierdzanie pozostają w panelu właściwej kolejki.</p></details>';
    }
    public static function register(): void {static $registered=false;if($registered||!is_admin())return;$registered=true;add_action('admin_notices',[self::class,'render'],50);}
}
