<?php
/** Dated Google Lighthouse lab reports. No frontend changes and no network calls while rendering. */
if (!defined('ABSPATH')) { exit; }

final class ZP_Admin_Lighthouse_20261010 {
    public const DATA = 'zps_admin_lighthouse_20261010_data';
    public const QUEUE = 'zps_admin_lighthouse_20261010_queue';
    public const LOCK = 'zps_admin_lighthouse_20261010_lock';
    public const SETUP = 'zps_admin_lighthouse_20261010_setup';
    public const API_KEY = 'zps_admin_lighthouse_20261010_api_key';
    public const DAILY = 'zp_admin_lighthouse_daily_20261010';
    public const WORKER = 'zp_admin_lighthouse_worker_20261010';
    public const ENDPOINT = 'https://pagespeedonline.googleapis.com/pagespeedonline/v5/runPagespeed';
    private const CATEGORIES = ['performance'=>'Wydajność','accessibility'=>'Dostępność','best-practices'=>'Dobre praktyki','seo'=>'SEO'];
    private const MAX_BYTES = 8388608;

    public static function allowed(): bool { return current_user_can('manage_options'); }
    public static function target(): string {
        $url = trailingslashit(home_url('/')); $p = wp_parse_url($url);
        if (!is_array($p) || ($p['scheme'] ?? '') !== 'https' || empty($p['host']) || isset($p['query']) || isset($p['fragment']) || isset($p['user']) || isset($p['pass']) || isset($p['port'])) { throw new RuntimeException('invalid_target'); }
        return $url;
    }
    public static function day(?int $at = null): string { return (new DateTimeImmutable('@'.($at ?? time())))->setTimezone(new DateTimeZone('Europe/Warsaw'))->format('Y-m-d'); }
    public static function stamp(string $value): int {
        if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2})(?:\.\d{1,6})?(Z|[+-]\d{2}:\d{2})$/D', $value, $m) || !checkdate((int)$m[2],(int)$m[3],(int)$m[1]) || (int)$m[4]>23 || (int)$m[5]>59 || (int)$m[6]>59) { throw new RuntimeException('invalid_date'); }
        if ($m[7] !== 'Z' && ((int)substr($m[7],1,2)>14 || (int)substr($m[7],4,2)>59 || ((int)substr($m[7],1,2)===14 && (int)substr($m[7],4,2)!==0))) { throw new RuntimeException('invalid_date'); }
        try { return (new DateTimeImmutable($value))->getTimestamp(); } catch (Throwable $e) { throw new RuntimeException('invalid_date'); }
    }
    public static function parse(array $raw, string $strategy, ?int $now = null, string $source = 'api'): array {
        if (!in_array($strategy,['mobile','desktop'],true) || !in_array($source,['api','imported_google_report'],true)) { throw new RuntimeException('invalid_report'); }
        $l = $raw['lighthouseResult'] ?? $raw;
        if (!is_array($l) || isset($raw['error']) || !empty($l['runtimeError'])) { throw new RuntimeException('lighthouse_failed'); }
        $target = self::target();
        if (($l['requestedUrl'] ?? null) !== $target || ($l['finalUrl'] ?? null) !== $target) { throw new RuntimeException('wrong_target'); }
        $factor = $l['configSettings']['formFactor'] ?? $l['configSettings']['emulatedFormFactor'] ?? null;
        if ($factor !== $strategy) { throw new RuntimeException('wrong_strategy'); }
        if (!is_string($l['fetchTime'] ?? null) || !is_string($l['lighthouseVersion'] ?? null) || !preg_match('/^\d+\.\d+(?:\.\d+)?(?:[-+][A-Za-z0-9.-]+)?$/D',$l['lighthouseVersion'])) { throw new RuntimeException('invalid_report'); }
        $at = self::stamp($l['fetchTime']); $now = $now ?? time();
        if ($at > $now+600 || $at < 946684800) { throw new RuntimeException('invalid_date'); }
        $scores = [];
        foreach (self::CATEGORIES as $key=>$label) {
            if (!is_array($l['categories'][$key] ?? null) || !array_key_exists('score',$l['categories'][$key])) { throw new RuntimeException('missing_category'); }
            $score = $l['categories'][$key]['score'];
            if ($score !== null && ((!is_int($score) && !is_float($score)) || !is_finite((float)$score) || $score<0 || $score>1)) { throw new RuntimeException('invalid_score'); }
            $scores[$key] = $score === null ? null : (int) round($score*100);
        }
        $agentic = null;
        if (is_array($l['categories']['agentic-browsing'] ?? null)) {
            $category=$l['categories']['agentic-browsing'];$score=$category['score']??null;
            if ($score!==null && ((!is_int($score)&&!is_float($score))||!is_finite((float)$score)||$score<0||$score>1)) { throw new RuntimeException('invalid_score'); }
            $agentic=['score'=>$score===null?null:(int)round($score*100),'passed'=>null,'applicable'=>null];
            if(is_array($category['auditRefs']??null)&&is_array($l['audits']??null)){
                $passed=0;$applicable=0;$seen=[];
                foreach($category['auditRefs']as$ref){$id=$ref['id']??null;if(!is_string($id)||isset($seen[$id])){continue;}$seen[$id]=true;$audit=$l['audits'][$id]??null;$v=is_array($audit)?($audit['score']??null):null;
                    if(!is_array($audit)||in_array($audit['scoreDisplayMode']??'',['notApplicable','manual','informative','error'],true)||(!is_int($v)&&!is_float($v))||!is_finite((float)$v)||$v<0||$v>1){continue;}
                    $applicable++;if((float)$v===1.0){$passed++;}}
                $agentic['passed']=$passed;$agentic['applicable']=$applicable;
            }
        }
        return ['target'=>$target,'strategy'=>$strategy,'measured_at'=>$at,'fetch_time'=>$l['fetchTime'],'date'=>self::day($at),'version'=>$l['lighthouseVersion'],'scores'=>$scores,'agentic'=>$agentic,'source'=>$source,'saved_at'=>$now];
    }
    private static function persist(string $name, array $value): void {
        update_option($name,$value,false);
        if (get_option($name,null) !== $value) { throw new RuntimeException('storage_failed'); }
    }
    private static function claim(): ?string {
        $token = wp_generate_uuid4(); $new = ['token'=>$token,'expires'=>time()+120];
        if (add_option(self::LOCK,$new,'',false)) { return $token; }
        $old = get_option(self::LOCK,[]);
        if (!is_array($old) || (int)($old['expires'] ?? PHP_INT_MAX)>=time()) { return null; }
        global $wpdb;
        if (!isset($wpdb) || !$wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->options} WHERE option_name=%s AND option_value=%s",self::LOCK,maybe_serialize($old)))) { return null; }
        wp_cache_delete(self::LOCK,'options');
        return add_option(self::LOCK,$new,'',false) ? $token : null;
    }
    private static function release(string $token): void {
        $old=get_option(self::LOCK,[]); if(is_array($old)&&($old['token']??'')===$token){delete_option(self::LOCK);}
    }
    private static function key(): string {
        $key = defined('ZP_LIGHTHOUSE_PSI_API_KEY') ? constant('ZP_LIGHTHOUSE_PSI_API_KEY') : get_option(self::API_KEY,'');
        return is_string($key) && preg_match('/^[A-Za-z0-9_-]{20,160}$/D',$key) ? $key : '';
    }
    public static function request_url(string $strategy): string {
        if(!in_array($strategy,['mobile','desktop'],true)){throw new RuntimeException('wrong_strategy');}
        $url=self::ENDPOINT.'?url='.rawurlencode(self::target()).'&strategy='.$strategy.'&locale=pl';
        foreach(array_keys(self::CATEGORIES)as$key){$url.='&category='.strtoupper(str_replace('-','_',$key));}
        $key=self::key();return $key!==''?$url.'&key='.rawurlencode($key):$url;
    }
    public static function save(array $report): void {
        $d=get_option(self::DATA,[]);$d=is_array($d)&&($d['target']??null)===self::target()?$d:['target'=>self::target(),'latest'=>[],'history'=>[],'errors'=>[]];
        $strategy=$report['strategy'];
        if(($report['measured_at']??0)>=($d['latest'][$strategy]['measured_at']??0)){$d['latest'][$strategy]=$report;}
        if(($report['measured_at']??0)>=($d['history'][$report['date']][$strategy]['measured_at']??0)){$d['history'][$report['date']][$strategy]=$report;}
        $cutoff=(new DateTimeImmutable(self::day(),new DateTimeZone('Europe/Warsaw')))->modify('-29 days')->format('Y-m-d');
        foreach(array_keys($d['history'])as$date){if($date<$cutoff||$date>self::day()){unset($d['history'][$date]);}}
        ksort($d['history']); $d['updated_at']=time();
        if($report['source']==='api'){unset($d['errors'][$strategy]);$d['last_api_success_at']=time();}
        self::persist(self::DATA,$d);
    }
    private static function schedule_worker(int $at=0): void { if(!wp_next_scheduled(self::WORKER)){wp_schedule_single_event(max(time()+1,$at),self::WORKER);} }
    private static function next_daily(): void {
        $now=new DateTimeImmutable('now',new DateTimeZone('Europe/Warsaw'));$at=$now->setTime(3,30);if($at<=$now){$at=$at->modify('+1 day');}wp_schedule_single_event($at->getTimestamp(),self::DAILY);
    }
    public static function schedules(): void {
        if(!self::allowed()){return;}if(!wp_next_scheduled(self::DAILY)){self::next_daily();}
        if(!get_option(self::SETUP,false)){$status=self::enqueue(false);if($status==='queued'){update_option(self::SETUP,['at'=>time()],false);}}
    }
    public static function refresh(): string { return self::allowed()?self::enqueue(true):'forbidden'; }
    private static function enqueue(bool $manual): string {
        try{self::target();}catch(Throwable$e){return'invalid_target';}$lock=self::claim();if($lock===null){return'busy';}
        try{
            $old=get_option(self::QUEUE,[]);$old=is_array($old)?$old:[];
            if(!empty($old['tasks'])&&($old['started_at']??0)>time()-28800){self::schedule_worker();return'queued';}
            if($manual&&($old['last_manual_at']??0)>time()-900){return'cooldown';}
            $q=['target'=>self::target(),'status'=>'queued','started_at'=>time(),'last_manual_at'=>$manual?time():($old['last_manual_at']??0),'tasks'=>[['strategy'=>'mobile','attempt'=>0,'not_before'=>0],['strategy'=>'desktop','attempt'=>0,'not_before'=>0]],'completed'=>0,'errors'=>[]];
            self::persist(self::QUEUE,$q);self::schedule_worker();return'queued';
        }catch(Throwable$e){return'storage_failed';}finally{self::release($lock);}
    }
    public static function daily(): void { self::next_daily();self::enqueue(false); }
    public static function worker(): void {
        $lock=self::claim();if($lock===null){self::schedule_worker(time()+60);return;}
        try{
            $q=get_option(self::QUEUE,[]);if(!is_array($q)||empty($q['tasks'])){return;}
            if(($q['target']??null)!==self::target()){$q['tasks']=[];$q['status']='target_changed';self::persist(self::QUEUE,$q);return;}
            $i=null;foreach($q['tasks']as$k=>$task){if((int)($task['not_before']??0)<=time()){$i=$k;break;}}
            if($i===null){self::schedule_worker(min(array_column($q['tasks'],'not_before')));return;}
            $task=$q['tasks'][$i];array_splice($q['tasks'],$i,1);$strategy=$task['strategy'];
            // Persist removal before HTTP: storage failure/crash must not repeat an unrecorded request indefinitely.
            $q['status']='running';$q['updated_at']=time();self::persist(self::QUEUE,$q);
            if($q['tasks']){self::schedule_worker(time()+60);}
            try{
                $res=wp_remote_get(self::request_url($strategy),['timeout'=>55,'redirection'=>0,'sslverify'=>true,'limit_response_size'=>self::MAX_BYTES,'headers'=>['Accept'=>'application/json']]);
                if(is_wp_error($res)){throw new RuntimeException('transport_error');}$code=(int)wp_remote_retrieve_response_code($res);
                if($code!==200){throw new RuntimeException(in_array($code,[400,401,403,429,500,502,503,504],true)?'google_'.$code:'google_http_error');}
                $body=wp_remote_retrieve_body($res);if(!is_string($body)||strlen($body)>=self::MAX_BYTES){throw new RuntimeException('response_too_large');}
                $raw=json_decode($body,true,64);if(!is_array($raw)||json_last_error()!==JSON_ERROR_NONE){throw new RuntimeException('invalid_json');}
                self::save(self::parse($raw,$strategy));unset($q['errors'][$strategy]);$q['completed']++;
            }catch(Throwable$e){
                $allowed=['transport_error','google_400','google_401','google_403','google_429','google_500','google_502','google_503','google_504','google_http_error','response_too_large','invalid_json','invalid_date','invalid_report','wrong_target','wrong_strategy','invalid_score','missing_category','lighthouse_failed','storage_failed'];
                $safe=in_array($e->getMessage(),$allowed,true)?$e->getMessage():'report_failed';$error=['code'=>$safe,'at'=>time(),'attempt'=>(int)$task['attempt']+1];$q['errors'][$strategy]=$error;
                $d=get_option(self::DATA,[]);$d=is_array($d)&&($d['target']??null)===self::target()?$d:['target'=>self::target(),'latest'=>[],'history'=>[]];$d['errors'][$strategy]=$error;self::persist(self::DATA,$d);
                if(in_array($safe,['transport_error','google_429','google_500','google_502','google_503','google_504'],true)&&(int)$task['attempt']<2){$task['attempt']++;$task['not_before']=time()+($task['attempt']===1?900:3600);$q['tasks'][]=$task;}
            }
            $q['status']=$q['tasks']?'queued':($q['errors']?'error':'ready');$q['updated_at']=time();self::persist(self::QUEUE,$q);
            if($q['tasks']){self::schedule_worker(max(time()+30,min(array_column($q['tasks'],'not_before'))));}
        }catch(Throwable$e){self::schedule_worker(time()+900);}finally{self::release($lock);}
    }
    public static function data(): array {
        if(!self::allowed()){return['authorized'=>false];}
        try{$target=self::target();}catch(Throwable$e){return['authorized'=>true,'target'=>null,'latest'=>[],'history'=>[],'errors'=>[],'status'=>'invalid_target'];}
        $d=get_option(self::DATA,[]);$d=is_array($d)&&($d['target']??null)===$target?$d:['target'=>$target,'latest'=>[],'history'=>[],'errors'=>[]];$q=get_option(self::QUEUE,[]);$q=is_array($q)&&($q['target']??null)===$target?$q:[];
        return array_merge($d,['authorized'=>true,'today'=>self::day(),'api_key_configured'=>self::key()!=='','key_from_constant'=>defined('ZP_LIGHTHOUSE_PSI_API_KEY'),'status'=>$q['status']??'not_started','queued'=>!empty($q['tasks']),'next_daily'=>(int)wp_next_scheduled(self::DAILY),'cron_disabled'=>defined('DISABLE_WP_CRON')&&DISABLE_WP_CRON]);
    }
    private static function guard(string $nonce): void { if(!self::allowed()||($_SERVER['REQUEST_METHOD']??'')!=='POST'){wp_die('Brak uprawnień.','',['response'=>403]);}check_admin_referer($nonce); }
    private static function back(string $status): void { wp_safe_redirect(add_query_arg(['page'=>'zp-suite-performance','zp_lighthouse'=>$status],admin_url('admin.php')));exit; }
    public static function refresh_action(): void { self::guard('zp_admin_lighthouse_refresh');$status=self::refresh();if($status==='queued'&&function_exists('spawn_cron')){spawn_cron();}self::back($status); }
    public static function key_action(): void {
        self::guard('zp_admin_lighthouse_key');if(defined('ZP_LIGHTHOUSE_PSI_API_KEY')){self::back('key_from_constant');}
        if(!empty($_POST['remove_key'])){delete_option(self::API_KEY);self::back('key_removed');}
        $key=isset($_POST['api_key'])&&is_string($_POST['api_key'])?trim(wp_unslash($_POST['api_key'])):'';
        if($key===''){self::back('key_unchanged');}if(!preg_match('/^[A-Za-z0-9_-]{20,160}$/D',$key)){self::back('invalid_key');}
        update_option(self::API_KEY,$key,false);self::back(get_option(self::API_KEY,null)===$key?'key_saved':'storage_failed');
    }
    public static function import_action(): void {
        self::guard('zp_admin_lighthouse_import');$file=$_FILES['report']??null;
        if(!is_array($file)||($file['error']??-1)!==UPLOAD_ERR_OK||!is_string($file['tmp_name']??null)||!is_uploaded_file($file['tmp_name'])){self::back('invalid_upload');}
        $size=filesize($file['tmp_name']);if($size===false||$size<=0||$size>self::MAX_BYTES){self::back('invalid_upload');}
        $lock=self::claim();if($lock===null){self::back('busy');}$status='import_failed';
        try{$bytes=file_get_contents($file['tmp_name']);if(!is_string($bytes)||strlen($bytes)>self::MAX_BYTES){throw new RuntimeException('invalid_upload');}$raw=json_decode($bytes,true,64);if(!is_array($raw)||json_last_error()!==JSON_ERROR_NONE){throw new RuntimeException('invalid_json');}$l=$raw['lighthouseResult']??$raw;$strategy=$l['configSettings']['formFactor']??$l['configSettings']['emulatedFormFactor']??'';self::save(self::parse($raw,(string)$strategy,null,'imported_google_report'));$status='imported';}catch(Throwable$e){$status='import_failed';}finally{self::release($lock);}self::back($status);
    }
    private static function circle(?int $score,string $label): void {
        $color=$score===null?'#8a96a7':($score>=90?'#16804a':($score>=50?'#b97708':'#c54040'));$text=$score===null?'—':(string)$score;$offset=$score===null?251.33:251.33*(1-$score/100);
        echo '<div class="zpLHScore"><svg viewBox="0 0 100 100" role="img" aria-label="'.esc_attr($label.': '.($score===null?'brak wyniku':$score.' na 100')).'"><circle cx="50" cy="50" r="40" fill="none" stroke="#e9edf3" stroke-width="7"/><circle cx="50" cy="50" r="40" fill="none" stroke="'.$color.'" stroke-width="7" stroke-dasharray="251.33" stroke-dashoffset="'.esc_attr((string)$offset).'" transform="rotate(-90 50 50)"/><text x="50" y="56" text-anchor="middle" fill="'.$color.'" font-size="24" font-weight="700">'.esc_html($text).'</text></svg><span>'.esc_html($label).'</span></div>';
    }
    private static function status_label(string $status): string {
        return ['not_started'=>'oczekuje na pierwszy pomiar','queued'=>'pomiar w kolejce','running'=>'trwa pomiar','ready'=>'ostatni pomiar zakończony','error'=>'ostatnia próba API wymaga sprawdzenia','target_changed'=>'zmienił się adres strony','invalid_target'=>'nieprawidłowy adres strony'][$status]??'brak potwierdzonego stanu';
    }
    private static function error_label(string $code): string {
        return ['google_429'=>'limit zapytań Google (429)','google_400'=>'Google odrzucił parametry pomiaru (400)','google_401'=>'Google wymaga poprawnego klucza (401)','google_403'=>'Google odrzucił dostęp API (403)','transport_error'=>'nie udało się połączyć z Google','lighthouse_failed'=>'Google nie ukończył analizy strony','storage_failed'=>'nie potwierdzono zapisu raportu'][$code]??'Google nie zwrócił poprawnego pomiaru';
    }
    private static function styles(): void {
        static$done=false;if($done){return;}$done=true;echo '<style>.zpAdmin20 .zpLHGrid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px}.zpAdmin20 .zpLHCompact{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}.zpAdmin20 .zpLHCompact>div{min-width:0;overflow-wrap:anywhere}.zpAdmin20 .zpLHCompact small{display:block;font-size:11px;line-height:1.55;margin-top:7px;color:#667385}.zpAdmin20 .zpLHScore{text-align:center}.zpAdmin20 .zpLHScore svg{width:90px;max-width:100%;font-family:inherit}.zpAdmin20 .zpLHScore span{display:block;font-size:12px;color:#667385}.zpAdmin20 .zpLHChart{width:100%;height:auto;font-family:inherit}.zpAdmin20 .zpLHLegend{display:flex;gap:14px;flex-wrap:wrap;font-size:11px}.zpAdmin20 .zpLHForms{display:flex;flex-wrap:wrap;gap:15px;align-items:end;margin-top:15px}.zpAdmin20 .zpLHForms form{display:grid;gap:7px;max-width:100%}.zpAdmin20 .zpLHForms input{max-width:100%}@media(max-width:640px){.zpAdmin20 .zpLHGrid{grid-template-columns:repeat(2,minmax(0,1fr))}}</style>';
    }
    private static function cards(array $d): void {
        self::styles();echo '<div class="zpAdmin20__grid">';
        foreach(['mobile'=>'Telefon','desktop'=>'Komputer']as$strategy=>$label){$r=$d['latest'][$strategy]??null;$date=is_array($r)?wp_date('d.m.Y H:i T',(int)$r['measured_at'],new DateTimeZone('Europe/Warsaw')):'brak pomiaru';
            echo '<section class="zpAdmin20__card"><h2>Lighthouse · '.esc_html($label).'</h2><p class="zpAdmin20__muted">'.esc_html($date).(is_array($r)&&$r['date']!==$d['today']?' · ostatni wynik, nie z dzisiaj':'').'</p><div class="zpLHGrid">';foreach(self::CATEGORIES as$key=>$name){self::circle(is_array($r)?($r['scores'][$key]??null):null,$name);}echo '</div>';
            if(is_array($r)){echo '<p class="zpAdmin20__muted">'.esc_html(($r['source']==='api'?'API Google':'Import raportu Google').' · Lighthouse '.$r['version']).'</p>';}
            if(is_array($r['agentic']??null)){ $a=$r['agentic'];echo '<p><span class="zpAdmin20__pill">'.esc_html('Przeglądanie agentowe: '.($a['score']===null?'—':$a['score'].'/100').($a['applicable']!==null?' · '.$a['passed'].'/'.$a['applicable'].' testów mających zastosowanie':'')).'</span></p>'; }
            if(!empty($d['errors'][$strategy])){echo '<p class="zpAdmin20__muted">Ostatnia próba API: '.esc_html(self::error_label($d['errors'][$strategy]['code']).' · '.wp_date('d.m.Y H:i',$d['errors'][$strategy]['at'])).'. Poprzedni poprawny wynik pozostaje zachowany.</p><details><summary>Diagnostyka API</summary><code>'.esc_html($d['errors'][$strategy]['code']).'</code></details>';}
            echo '</section>';
        }echo '</div>';
    }
    /** Read-only calendar model: unknown days/scores stay null; never interpolate or change stored reports. */
    public static function history_model(array $history, int $days = 7, string $category = 'performance', ?string $today = null): array {
        $days = in_array($days, [7, 30], true) ? $days : 7;
        $category = isset(self::CATEGORIES[$category]) ? $category : 'performance';
        if ($today === null || !preg_match('/^\d{4}-\d{2}-\d{2}$/D', $today) || !checkdate((int) substr($today, 5, 2), (int) substr($today, 8, 2), (int) substr($today, 0, 4))) { $today = self::day(); }
        $end = new DateTimeImmutable($today, new DateTimeZone('Europe/Warsaw'));
        $start = $end->modify('-' . ($days - 1) . ' days');
        $out = ['days' => $days, 'category' => $category, 'label' => self::CATEGORIES[$category], 'start' => $start->format('Y-m-d'), 'end' => $today, 'dates' => [], 'series' => ['mobile' => [], 'desktop' => []], 'counts' => ['mobile' => 0, 'desktop' => 0]];
        for ($i = 0; $i < $days; $i++) {
            $date = $start->modify('+' . $i . ' days')->format('Y-m-d'); $out['dates'][] = $date;
            $devices = $history[$date] ?? null;
            foreach (['mobile', 'desktop'] as $strategy) {
                $report = is_array($devices) ? ($devices[$strategy] ?? null) : null;
                $point = ['date' => $date, 'score' => null, 'measured_at' => null, 'source' => ''];
                if (is_array($report) && ($report['date'] ?? null) === $date && is_int($report['measured_at'] ?? null) && $report['measured_at'] > 0 && self::day($report['measured_at']) === $date) {
                    $point['measured_at'] = $report['measured_at'];
                    $point['source'] = in_array($report['source'] ?? null, ['api', 'imported_google_report'], true) ? $report['source'] : '';
                    $score = is_array($report['scores'] ?? null) ? ($report['scores'][$category] ?? null) : null;
                    if (is_int($score) && $score >= 0 && $score <= 100) { $point['score'] = $score; $out['counts'][$strategy]++; }
                }
                $out['series'][$strategy][] = $point;
            }
        }
        return $out;
    }

    private static function history_chart(array $model, string $id): void {
        $labels = ['mobile' => 'Telefon', 'desktop' => 'Komputer']; $colors = ['mobile' => '#1c477a', 'desktop' => '#071426'];
        $range = (new DateTimeImmutable($model['start']))->format('d.m.Y') . ' — ' . (new DateTimeImmutable($model['end']))->format('d.m.Y');
        echo '<p class="zpAdmin20__muted">' . esc_html($range . ' · Europe/Warsaw') . '</p><div class="zpLHHistoryLegend">';
        foreach ($labels as $strategy => $label) { echo '<span class="zpLHHistoryLegend__' . $strategy . '"><i aria-hidden="true"></i>' . esc_html($label . ' · ' . $model['counts'][$strategy] . ' wyników') . '</span>'; }
        echo '</div>';
        if (!$model['counts']['mobile'] && !$model['counts']['desktop']) {
            echo '<div class="zpAdmin20__empty zpLHHistoryEmpty">Brak wyników tej kategorii w wybranym okresie. Wykres pojawi się po zapisaniu rzeczywistego raportu. Brak wyniku nie oznacza zera.</div>';
        } else {
            echo '<div class="zpLHHistoryPlot"><svg class="zpLHChart zpLHHistoryChart" viewBox="0 0 820 282" role="img" aria-labelledby="' . esc_attr($id . '-title ' . $id . '-desc') . '"><title id="' . esc_attr($id . '-title') . '">' . esc_html($model['label'] . ' · historia Lighthouse · ' . $range) . '</title><desc id="' . esc_attr($id . '-desc') . '">' . esc_html('Wyniki od 0 do 100 punktów. Telefon: ' . $model['counts']['mobile'] . ' wyników; komputer: ' . $model['counts']['desktop'] . ' wyników. Linie łączą wyłącznie kolejne dni z wynikiem. Brakujące dni pozostają przerwami. Dokładne wyniki, daty i źródła znajdują się w tabeli poniżej.') . '</desc>';
            foreach ([0, 25, 50, 75, 100] as $score) {
                $y = 226 - 190 * $score / 100;
                echo '<line x1="52" y1="' . $y . '" x2="790" y2="' . $y . '" stroke="#e1e6ee"/><text x="40" y="' . ($y + 4) . '" text-anchor="end" fill="#667385" font-size="12">' . $score . '</text>';
            }
            foreach ($model['dates'] as $i => $date) {
                if ($model['days'] === 30 && $i % 5 !== 0 && $i !== 29) { continue; }
                $x = 52 + 738 * $i / ($model['days'] - 1); $anchor = $i === 0 ? 'start' : ($i === $model['days'] - 1 ? 'end' : 'middle');
                echo '<text x="' . esc_attr((string) $x) . '" y="253" text-anchor="' . $anchor . '" fill="#667385" font-size="12">' . esc_html((new DateTimeImmutable($date))->format('d.m')) . '</text>';
            }
            foreach ($labels as $strategy => $label) {
                $segments = []; $segment = []; $points = [];
                foreach ($model['series'][$strategy] as $i => $point) {
                    if ($point['score'] === null) { if ($segment) { $segments[] = $segment; $segment = []; } continue; }
                    $x = 52 + 738 * $i / ($model['days'] - 1); $y = 226 - 190 * $point['score'] / 100;
                    $segment[] = $x . ',' . $y; $points[] = [$x, $y, $point];
                }
                if ($segment) { $segments[] = $segment; }
                foreach ($segments as $line) {
                    if (count($line) < 2) { continue; }
                    echo '<polyline data-lh-series="' . $strategy . '" points="' . esc_attr(implode(' ', $line)) . '" fill="none" stroke="' . $colors[$strategy] . '" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"' . ($strategy === 'desktop' ? ' stroke-dasharray="7 5"' : '') . '/>';
                }
                foreach ($points as [$x, $y, $point]) {
                    $title = $label . ': ' . $point['score'] . '/100 · ' . wp_date('d.m.Y H:i T', $point['measured_at'], new DateTimeZone('Europe/Warsaw'));
                    echo '<g data-lh-point="' . $strategy . '"><title>' . esc_html($title) . '</title>';
                    if ($strategy === 'mobile') { echo '<circle cx="' . esc_attr((string) $x) . '" cy="' . esc_attr((string) $y) . '" r="4.5" fill="' . $colors[$strategy] . '" stroke="#fff" stroke-width="1.5"/>'; }
                    else { echo '<rect x="' . esc_attr((string) ($x - 4)) . '" y="' . esc_attr((string) ($y - 4)) . '" width="8" height="8" transform="rotate(45 ' . esc_attr($x . ' ' . $y) . ')" fill="' . $colors[$strategy] . '" stroke="#fff" stroke-width="1.5"/>'; }
                    echo '</g>';
                }
            }
            echo '</svg></div>';
        }
        echo '<details class="zpAdmin20__details zpLHHistoryData"><summary>Dokładne wyniki i źródła w tabeli</summary><div class="zpAdmin20__tableWrap"><table class="zpAdmin20__table"><caption class="screen-reader-text">' . esc_html($model['label'] . ' — wyniki Lighthouse od ' . $model['start'] . ' do ' . $model['end']) . '</caption><thead><tr><th scope="col">Dzień</th><th scope="col">Telefon · punkty /100</th><th scope="col">Komputer · punkty /100</th></tr></thead><tbody>';
        foreach ($model['dates'] as $i => $date) {
            echo '<tr><th scope="row">' . esc_html((new DateTimeImmutable($date))->format('d.m.Y')) . '</th>';
            foreach (array_keys($labels) as $strategy) {
                $point = $model['series'][$strategy][$i]; echo '<td>' . ($point['score'] === null ? '<span aria-label="Brak wyniku">—</span>' : '<strong>' . $point['score'] . '</strong>');
                if ($point['measured_at'] !== null) { $source = $point['source'] === 'api' ? 'API Google' : ($point['source'] === 'imported_google_report' ? 'Import Google' : 'Raport Lighthouse'); echo '<br><small>' . esc_html(wp_date('H:i T', $point['measured_at'], new DateTimeZone('Europe/Warsaw')) . ' · ' . $source) . '</small>'; }
                echo '</td>';
            }
            echo '</tr>';
        }
        echo '</tbody></table></div></details>';
    }

    private static function history(array $history): void {
        static $instance = 0; $instance++; $id = 'zp-lh-history-' . $instance;
        echo '<section class="zpAdmin20__card zpLHHistory" data-zp-lh-history><h2>Jak zmieniają się wyniki Lighthouse?</h2><p class="zpAdmin20__muted">Porównaj telefon i komputer. Każdy punkt pochodzi z zapisanego raportu; przy kilku pomiarach tego samego dnia zachowujemy ostatni wynik osobno dla każdego urządzenia.</p><div class="zpLHHistoryControls"><div class="zpLHHistoryPeriods" role="group" aria-label="Zakres historii Lighthouse"><button type="button" class="button" data-lh-days="7" aria-pressed="true">7 dni</button><button type="button" class="button" data-lh-days="30" aria-pressed="false">30 dni</button></div><label for="' . esc_attr($id . '-category') . '">Kategoria<select id="' . esc_attr($id . '-category') . '" data-lh-category>';
        foreach (self::CATEGORIES as $category => $label) { echo '<option value="' . esc_attr($category) . '">' . esc_html($label) . '</option>'; }
        echo '</select></label></div><p class="zpAdmin20__muted">Skala 0–100 oznacza punkty wybranej kategorii Lighthouse. To nie wartość CLS: CLS opisuje przesunięcia układu i jest osobną miarą bez jednostki. Przerwa w linii oznacza dzień bez wyniku.</p><p class="screen-reader-text" data-lh-announcement role="status" aria-live="polite"></p>';
        foreach ([7, 30] as $days) {
            foreach (array_keys(self::CATEGORIES) as $category) {
                $model = self::history_model($history, $days, $category);
                $summary = $model['label'] . ', ' . $days . ' dni. Telefon: ' . $model['counts']['mobile'] . ' wyników. Komputer: ' . $model['counts']['desktop'] . ' wyników.';
                echo '<div class="zpLHHistoryPanel" data-lh-days-panel="' . $days . '" data-lh-category-panel="' . esc_attr($category) . '" data-lh-summary="' . esc_attr($summary) . '"' . ($days === 7 && $category === 'performance' ? '' : ' hidden') . '>';
                self::history_chart($model, $id . '-' . $days . '-' . $category); echo '</div>';
            }
        }
        echo '<noscript><p>Bez JavaScript widoczna jest wydajność z ostatnich 7 dni wraz z tabelą danych.</p></noscript></section>';
        echo '<style>.zpLHHistory [hidden]{display:none!important}.zpLHHistoryControls{display:flex;align-items:end;justify-content:space-between;flex-wrap:wrap;gap:16px;margin:20px 0 14px}.zpLHHistoryControls label{display:grid;gap:6px;font-size:12px;color:#667385}.zpLHHistoryControls select{min-width:190px;min-height:36px}.zpLHHistoryPeriods{display:flex;gap:5px;padding:4px;border:1px solid #e1e6ee;border-radius:10px;background:#f8fafc}.zpLHHistoryPeriods .button{margin:0;background:transparent;border-color:transparent;color:#667385}.zpLHHistoryPeriods .button[aria-pressed=true]{background:#071426;border-color:#071426;color:#fff}.zpLHHistoryLegend{display:flex;flex-wrap:wrap;gap:20px;margin:18px 0 4px;font-size:12px;font-weight:600;color:#526176}.zpLHHistoryLegend span{display:inline-flex;align-items:center;gap:8px}.zpLHHistoryLegend i{display:inline-block;width:24px;height:3px;background:#1c477a;position:relative}.zpLHHistoryLegend__mobile i:after{content:"";position:absolute;width:7px;height:7px;left:9px;top:-2px;border-radius:50%;background:#1c477a}.zpLHHistoryLegend__desktop i{background:repeating-linear-gradient(to right,#071426 0 6px,transparent 6px 10px)}.zpLHHistoryLegend__desktop i:after{content:"";position:absolute;width:6px;height:6px;left:9px;top:-1px;transform:rotate(45deg);background:#071426}.zpLHHistoryPlot{max-width:100%;overflow-x:auto;-webkit-overflow-scrolling:touch}.zpLHHistoryChart{display:block;max-height:320px;margin:8px 0}.zpLHHistoryData{border-top:1px solid #edf1f5;padding-top:15px}.zpLHHistoryData small{font-size:10px;color:#667385}.zpLHHistoryEmpty{margin:18px 0}.zpLHHistoryControls button:focus-visible,.zpLHHistoryControls select:focus-visible{outline:2px solid #1c477a;outline-offset:3px}@media(max-width:640px){.zpLHHistoryControls{align-items:stretch}.zpLHHistoryControls label{width:100%}.zpLHHistoryControls select{width:100%}.zpLHHistoryChart{min-width:640px}.zpLHHistoryLegend{gap:12px;font-size:11px}}</style>';
        echo <<<'JS'
<script data-zp-lh-history-script>(function(){"use strict";document.querySelectorAll("[data-zp-lh-history]").forEach(function(root){if(root.dataset.lhBound==="1")return;root.dataset.lhBound="1";let days="7";const category=root.querySelector("[data-lh-category]"),buttons=root.querySelectorAll("button[data-lh-days]"),panels=root.querySelectorAll("[data-lh-days-panel]"),announcement=root.querySelector("[data-lh-announcement]");function update(){let visible=null;panels.forEach(function(panel){const selected=panel.dataset.lhDaysPanel===days&&panel.dataset.lhCategoryPanel===category.value;panel.hidden=!selected;if(selected)visible=panel;});buttons.forEach(function(button){button.setAttribute("aria-pressed",button.dataset.lhDays===days?"true":"false");});if(visible&&announcement)announcement.textContent=visible.dataset.lhSummary||"";}buttons.forEach(function(button){button.addEventListener("click",function(){if(button.dataset.lhDays!=="7"&&button.dataset.lhDays!=="30")return;days=button.dataset.lhDays;update();});});if(category)category.addEventListener("change",update);});})();</script>
JS;
    }
    public static function compact(): void {
        if(!self::allowed()){return;}$d=self::data();if(!isset($d['today'])){return;}self::styles();
        echo '<section class="zpAdmin20__card"><h2>Lighthouse · ostatnie pomiary</h2><div class="zpLHCompact">';
        foreach(['mobile'=>'Telefon','desktop'=>'Komputer']as$strategy=>$label){$r=$d['latest'][$strategy]??null;echo '<div>';self::circle(is_array($r)?($r['scores']['performance']??null):null,$label);
            if(is_array($r)){echo '<small>'.esc_html(wp_date('d.m H:i',$r['measured_at'],new DateTimeZone('Europe/Warsaw')).' · '.($r['source']==='api'?'API Google':'Import Google').($r['date']!==$d['today']?' · starszy pomiar':'')).'</small><small>';
                $other=[];foreach(['accessibility'=>'Dostępność','best-practices'=>'Dobre praktyki','seo'=>'SEO']as$key=>$name){$v=$r['scores'][$key]??null;$other[]=$name.' '.($v===null?'—':$v);}echo esc_html(implode(' · ',$other)).'</small>';
            }else{echo '<small>Brak pomiaru</small>';}
            if(!empty($d['errors'][$strategy])){echo '<small>API: '.esc_html(self::error_label($d['errors'][$strategy]['code']).' · '.wp_date('d.m H:i',$d['errors'][$strategy]['at'])).'</small>';}echo '</div>';
        }
        echo '</div><p class="zpAdmin20__muted">Wydajność w pomiarze laboratoryjnym Google. '.esc_html(self::status_label($d['status'])).'.</p><a href="'.esc_url(add_query_arg(['page'=>'zp-suite-performance'],admin_url('admin.php'))).'">Pełny raport Lighthouse →</a></section>';
    }
    public static function render(): void {
        if(!self::allowed()){return;}$d=self::data();if(!isset($d['today'])){echo '<p>Nieprawidłowy adres strony głównej dla pomiaru.</p>';return;}
        $notice=isset($_GET['zp_lighthouse'])&&is_string($_GET['zp_lighthouse'])?sanitize_key(wp_unslash($_GET['zp_lighthouse'])):'';
        $notices=['queued'=>'Pomiar został dodany do kolejki. Wyniki pojawią się po wykonaniu zadania; odśwież panel.','cooldown'=>'Kolejny ręczny pomiar jest dostępny po 15 minutach.','busy'=>'Inne zadanie trwa. Spróbuj ponownie za chwilę.','imported'=>'Zapisano raport z jego rzeczywistą datą i strategią.','import_failed'=>'Nie zapisano importu. Sprawdź URL strony, strategię, datę i cztery kategorie raportu.','invalid_upload'=>'Nie przyjęto pliku. Wybierz poprawny JSON do 8 MiB.','key_saved'=>'Zapisano klucz API. Możesz uruchomić pomiar w tle.','key_removed'=>'Usunięto zapisany klucz; kolejne próby użyją trybu bez klucza.','key_unchanged'=>'Dotychczasowy klucz pozostaje zachowany.','invalid_key'=>'Nie zapisano klucza: niepoprawny format.','storage_failed'=>'Nie udało się potwierdzić zapisu.','key_from_constant'=>'Klucz jest zarządzany przez konfigurację serwera.'];
        if(isset($notices[$notice])){echo '<div class="notice notice-info"><p>'.esc_html($notices[$notice]).'</p></div>';}
        echo '<section class="zpAdmin20__card"><h2>Codzienny Google Lighthouse</h2><p class="zpAdmin20__muted">Pomiar laboratoryjny strony głównej przez Google PageSpeed Insights, osobno telefon i komputer. Data pochodzi z raportu Google; puste wyniki nie oznaczają zera. To nie jest pomiar Core Web Vitals rzeczywistych odwiedzających.</p><p class="zpAdmin20__muted">Harmonogram: codziennie 03:30 Europe/Warsaw. '.esc_html($d['next_daily']?'Następny termin: '.wp_date('d.m.Y H:i T',$d['next_daily'],new DateTimeZone('Europe/Warsaw')):'Termin oczekuje na uruchomienie WordPressa').'. Stan API: '.esc_html(self::status_label($d['status'])).'. '.(!empty($d['cron_disabled'])?'WP-Cron jest wyłączony; zadanie wymaga istniejącego uruchamiania cron przez serwer.':'WP-Cron może wykonać zadanie z opóźnieniem; zwłaszcza gdy cache pomija WordPressa.').'</p>';
        if(!$d['api_key_configured']){echo '<p class="zpAdmin20__muted">Tryb bez klucza. Google może odrzucić pomiar limitem 429; wtedy potrzebny jest własny istniejący klucz API PageSpeed Insights albo import oficjalnego raportu. Sam import nie uruchamia automatycznego API.</p>';}
        echo '<div class="zpLHForms"><form method="post" action="'.esc_url(admin_url('admin-post.php')).'"><input type="hidden" name="action" value="zp_admin_lighthouse_refresh">';wp_nonce_field('zp_admin_lighthouse_refresh');echo '<button class="button button-primary">Zmierz teraz w tle</button></form><a class="button" href="'.esc_url('https://pagespeed.web.dev/analysis?url='.rawurlencode($d['target'])).'" target="_blank" rel="noopener">Otwórz Google PageSpeed</a></div></section>';
        self::cards($d);
        self::history(is_array($d['history']??null)?$d['history']:[]);
        echo '<section class="zpAdmin20__card"><h2>Import oficjalnego raportu</h2><p class="zpAdmin20__muted">JSON pobrany z Google PageSpeed Insights dla tej strony, telefon albo komputer, do 8 MiB. Zachowamy datę, wersję i strategię raportu.</p><form method="post" enctype="multipart/form-data" action="'.esc_url(admin_url('admin-post.php')).'"><input type="hidden" name="action" value="zp_admin_lighthouse_import">';wp_nonce_field('zp_admin_lighthouse_import');echo '<input type="file" name="report" accept=".json,application/json" required><button class="button">Importuj raport</button></form></section>';
        echo '<section class="zpAdmin20__card"><h2>Opcjonalny klucz PageSpeed Insights</h2><p class="zpAdmin20__muted">'.esc_html($d['api_key_configured']?'Klucz skonfigurowany; jego wartość nie jest wyświetlana.':'Nie zapisano klucza. Możesz podać istniejący klucz do tego API. Nie tworzymy projektu ani nowych uprawnień.').'</p>';
        if(!$d['key_from_constant']){echo '<form method="post" action="'.esc_url(admin_url('admin-post.php')).'"><input type="hidden" name="action" value="zp_admin_lighthouse_key">';wp_nonce_field('zp_admin_lighthouse_key');echo '<input type="password" name="api_key" value="" autocomplete="new-password" aria-label="Klucz API; puste pole zachowuje dotychczasowy"><label><input type="checkbox" name="remove_key" value="1"> Usuń zapisany klucz</label><button class="button">Zapisz ustawienie klucza</button></form>';}else{echo '<p class="zpAdmin20__muted">Klucz pochodzi z konfiguracji serwera.</p>';}echo '</section>';
    }
    public static function deactivate(): void { foreach([self::DAILY,self::WORKER]as$hook){wp_clear_scheduled_hook($hook);} }
}
function zp_admin_lighthouse_data(): array { return ZP_Admin_Lighthouse_20261010::data(); }
function zp_admin_lighthouse_render(): void { ZP_Admin_Lighthouse_20261010::render(); }
function zp_admin_lighthouse_compact(): void { ZP_Admin_Lighthouse_20261010::compact(); }
add_action('admin_init',[ZP_Admin_Lighthouse_20261010::class,'schedules']);
add_action(ZP_Admin_Lighthouse_20261010::DAILY,[ZP_Admin_Lighthouse_20261010::class,'daily']);
add_action(ZP_Admin_Lighthouse_20261010::WORKER,[ZP_Admin_Lighthouse_20261010::class,'worker']);
add_action('admin_post_zp_admin_lighthouse_refresh',[ZP_Admin_Lighthouse_20261010::class,'refresh_action']);
add_action('admin_post_zp_admin_lighthouse_key',[ZP_Admin_Lighthouse_20261010::class,'key_action']);
add_action('admin_post_zp_admin_lighthouse_import',[ZP_Admin_Lighthouse_20261010::class,'import_action']);
register_deactivation_hook(dirname(__DIR__).'/zaprojektowani-suite.php',[ZP_Admin_Lighthouse_20261010::class,'deactivate']);
