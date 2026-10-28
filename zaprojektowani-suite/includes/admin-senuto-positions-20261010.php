<?php
/** Private read-only Senuto Rank Tracker reporting. No SERP jobs/project mutations or frontend API. */
if (!defined('ABSPATH')) { exit; }
final class ZP_Senuto_Positions_20261010 {
    public const PAGE='zp-suite-positions';
    public const CONFIG='zps_senuto_positions_20261010_config';
    public const DATA='zps_senuto_positions_20261010_data';
    public const QUEUE='zps_senuto_positions_20261010_queue';
    public const LOCK='zps_senuto_positions_20261010_lock';
    public const WORKER='zp_senuto_positions_worker_20261010';
    public const DAILY='zp_senuto_positions_daily_20261010';
    private const TOKEN='zps_content_prep_20261010_senuto_token';
    private const MAX_PAGES=4;
    private const LIMIT=500;
    public static function allowed(): bool { return current_user_can('manage_options'); }
    private static function token(): string {
        $v=defined('SENUTO_API_TOKEN')?constant('SENUTO_API_TOKEN'):get_option(self::TOKEN,'');
        return is_string($v)&&preg_match('/^[A-Za-z0-9_.:-]{10,1000}$/D',$v)?$v:'';
    }
    private static function fingerprint(): string { return hash('sha256',self::token()); }
    private static function save(string $key,array $v): void { update_option($key,$v,false);if(get_option($key,null)!==$v)throw new RuntimeException('storage_failed'); }
    public static function integer($v,int $min=0,int $max=100000000): ?int {
        if(is_int($v))return $v>=$min&&$v<=$max?$v:null;
        if(is_string($v)&&preg_match('/^\d{1,10}$/D',$v)){ $n=(int)$v;return $n>=$min&&$n<=$max?$n:null; }return null;
    }
    /** Provider keyword IDs are identifiers, independent of metric bounds. Cast only after overflow proof. */
    public static function row_identifier($value): ?int {
        if(is_int($value))return $value>0?$value:null;if(!is_string($value)||strlen($value)>strlen((string)PHP_INT_MAX)+1||!preg_match('/^\d+$/D',$value))return null;
        $number=ltrim($value,'0');$max=(string)PHP_INT_MAX;if($number===''||strlen($number)>strlen($max)||(strlen($number)===strlen($max)&&strcmp($number,$max)>0))return null;return (int)$number;
    }
    public static function day($s): ?string {
        if(!is_string($s)||!preg_match('/^\d{4}-\d{2}-\d{2}$/D',$s))return null;
        $d=DateTimeImmutable::createFromFormat('!Y-m-d',$s,new DateTimeZone('Europe/Warsaw'));
        return $d&&$d->format('Y-m-d')===$s?$s:null;
    }
    private static function today(): string { return wp_date('Y-m-d',time(),new DateTimeZone('Europe/Warsaw')); }
    private static function host(string $s): string {
        $p=parse_url(str_contains($s,'://')?$s:'https://'.$s);
        if(!is_array($p)||isset($p['user'])||isset($p['pass'])||isset($p['port']))return '';
        return preg_replace('/^www\./','',strtolower((string)($p['host']??'')));
    }
    public static function config(): array {
        $v=get_option(self::CONFIG,[]);$v=is_array($v)?$v:[];
        return ['polska'=>self::integer($v['polska']??184990,1),'katowice'=>self::integer($v['katowice']??184991,1),'daily'=>($v['daily']??true)===true,'actor'=>self::integer($v['actor']??null,1)];
    }
    public static function cached(): array {
        return self::allowed()?self::state():[];
    }
    private static function state(): array {
        $v=get_option(self::DATA,[]);
        return is_array($v)&&($v['fingerprint']??'')===self::fingerprint()?$v:[];
    }
    private static function ready(array $cfg,array $data): bool {
        if(self::token()===''||!$cfg['actor']||$cfg['polska']===$cfg['katowice'])return false;
        foreach(['polska','katowice'] as $slot){if(!$cfg[$slot]||!isset($data['projects'][$cfg[$slot]]))return false;}return true;
    }
    private static function claim(): ?string {
        $token=wp_generate_uuid4();$raw=wp_json_encode(['token'=>$token,'expires'=>time()+180]);
        if(add_option(self::LOCK,$raw,'',false))return $token;$old=get_option(self::LOCK,'');$v=is_string($old)?json_decode($old,true):null;
        if(is_array($v)&&($v['expires']??PHP_INT_MAX)<time()){
            global $wpdb;if($wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->options} WHERE option_name=%s AND option_value=%s",self::LOCK,$old))){wp_cache_delete(self::LOCK,'options');wp_cache_delete('notoptions','options');if(add_option(self::LOCK,$raw,'',false))return $token;}
        }return null;
    }
    private static function release(string $token): void {
        $raw=get_option(self::LOCK,'');$v=is_string($raw)?json_decode($raw,true):null;
        if(is_array($v)&&hash_equals((string)($v['token']??''),$token)){global $wpdb;$wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->options} WHERE option_name=%s AND option_value=%s",self::LOCK,$raw));wp_cache_delete(self::LOCK,'options');wp_cache_delete('notoptions','options');}
    }
    public static function projects(array $response): array {
        if(($response['success']??null)!==true||!is_array($response['data']??null)||count($response['data'])>100)throw new RuntimeException('invalid_projects');
        $out=[];$host=self::host(home_url('/'));
        foreach($response['data'] as $row){if(!is_array($row)||!is_array($row['project']??null))throw new RuntimeException('invalid_projects');$p=$row['project'];$id=self::integer($p['id']??null,1);
            if(!$id||!is_string($p['domain']??null)||self::host($p['domain'])!==$host)continue;
            if(!is_string($p['name']??null)||strlen($p['name'])>240)throw new RuntimeException('invalid_projects');
            $last=self::day($row['dates']['last_date']['formatted']??null);if($last!==null&&$last>self::today())throw new RuntimeException('invalid_project_date');
            $out[$id]=['id'=>$id,'name'=>$p['name'],'domain'=>$p['domain'],'country_id'=>self::integer($p['country_id']??null),'region_id'=>self::integer($p['region_id']??null),'last_date'=>$last,'keywords_count'=>self::integer($row['statistics']['keywords_count']['recent_value']??null),'is_owner'=>is_bool($p['is_owner']??null)?$p['is_owner']:null];
        }return $out;
    }
    public static function range(string $last,int $days=30): array {
        if(!self::day($last)||$last>self::today()||!in_array($days,[7,30],true))throw new RuntimeException('invalid_dates');
        $d=new DateTimeImmutable($last,new DateTimeZone('Europe/Warsaw'));return ['start'=>$d->modify('-'.($days-1).' days')->format('Y-m-d'),'end'=>$last];
    }
    public static function request_spec(array $task): array {
        $page=self::integer($task['page']??null,1,self::MAX_PAGES);if(!$page)throw new RuntimeException('invalid_task');
        if(($task['kind']??'')==='projects'){
            // Pin an explicit recent window; provider defaults must not choose the report day.
            $range=$task['range']??self::range(self::today());$device=$task['device']??'desktop';
            if(!is_array($range)||!self::day($range['end']??null)||$range['end']>self::today()||self::range($range['end'])!==$range||!in_array($device,['desktop','mobile'],true))throw new RuntimeException('invalid_task');
            return ['method'=>'GET','url'=>'https://api.senuto.com/api/rank_tracker/management/projects/getListWithExtendedData?'.http_build_query(['limit'=>100,'page'=>$page,'date_min'=>$range['start'],'date_max'=>$range['end'],'mode'=>$device]),'body'=>null];
        }
        $id=self::integer($task['project']??null,1);$device=$task['device']??'';$range=$task['range']??[];
        if(($task['kind']??'')!=='positions'||!$id||!in_array($device,['desktop','mobile'],true)||!self::day($range['start']??null)||!self::day($range['end']??null)||$range['start']>$range['end']||$range['end']>self::today()||self::range($range['end'])!==$range)throw new RuntimeException('invalid_task');
        return ['method'=>'POST','url'=>'https://api.senuto.com/api/rank_tracker/reports/positions/getData','body'=>['project_id'=>$id,'date_min'=>$range['start'],'date_max'=>$range['end'],'group_id'=>0,'mode'=>$device,'limit'=>self::LIMIT,'page'=>$page]];
    }
    private static function request(array $task): array {
        $token=self::token();if($token==='')throw new RuntimeException('missing_token');$s=self::request_spec($task);
        $args=['timeout'=>45,'redirection'=>0,'sslverify'=>true,'limit_response_size'=>8000000,'headers'=>['Authorization'=>'Bearer '.$token,'Content-Type'=>'application/json']];
        if($s['method']==='POST'){$args['body']=wp_json_encode($s['body']);$r=wp_remote_post($s['url'],$args);}else{$r=wp_remote_get($s['url'],$args);}
        if(is_wp_error($r))throw new RuntimeException('transport_failed');$code=wp_remote_retrieve_response_code($r);
        if($code===429){$retry=wp_remote_retrieve_header($r,'retry-after');$seconds=self::integer($retry,60,3600)??900;throw new RuntimeException('rate_limited_'.$seconds);}
        if($code!==200)throw new RuntimeException(in_array($code,[401,403,418],true)?'http_'.$code:'http_failed');
        $v=json_decode(wp_remote_retrieve_body($r),true);if(!is_array($v)||($v['success']??null)!==true)throw new RuntimeException('invalid_response');return $v;
    }
    public static function pagination(array $response,int $page): array {
        $p=$response['pagination']??null;
        if(!is_array($p)||self::integer($p['current_page']??null,1)!==$page||!is_bool($p['has_next_page']??null))throw new RuntimeException('invalid_pagination');
        return ['next'=>$p['has_next_page'],'total'=>self::integer($p['count']??null)];
    }
    /** Enumerated validation only. No provider response values are logged. */
    private static function report_problem(array $response,string $device): array {
        if(($response['success']??null)!==true)return ['cause'=>'success_type'];
        if(!is_array($response['data']??null))return ['cause'=>'data_type'];
        if(count($response['data'])>self::LIMIT)return ['cause'=>'row_limit'];
        if(!in_array($device,['desktop','mobile'],true))return ['cause'=>'device'];
        $seen=[];$index=0;foreach($response['data'] as $r){
            if(!is_array($r))return ['cause'=>'row_type','row_index'=>$index];
            if(!is_string($r['keyword']??null))return ['cause'=>'keyword_type','row_index'=>$index];
            if(strlen($r['keyword'])>500)return ['cause'=>'keyword_length','row_index'=>$index];
            $id=self::row_identifier($r['id']??null);if(!$id)return ['cause'=>'row_id','row_index'=>$index];
            if(isset($seen[$id]))return ['cause'=>'duplicate_id','row_index'=>$index];$seen[$id]=true;
            foreach(['positions'=>$r['positions']??null,'device_positions_history'=>$r[$device]['positions_history']??null,'device_history'=>$r[$device]['history']??null] as $name=>$map){if(is_array($map)&&count($map)>1000)return ['cause'=>'history_limit','row_index'=>$index,'map'=>$name];}
            $index++;
        }return [];
    }
    private static function shape_key($key): string {
        if(is_int($key))return '[integer key]';
        $known=['success','data','pagination','statistics','parameters','country_id','date_min','date_max','mode','group_id','project_id','limit','page','current_page','page_count','count','has_next_page','has_previous_page','total','id','keyword','kid','searches','url','status','updated','date','positions','desktop','mobile','positions_history','history','pos','position','current_position','previous_position','diff_position','average_position','positions_date_range','first','last','diff','cpc','difficulty','created','row_id','keyword_id','keywordId','rowId'];
        return is_string($key)&&in_array($key,$known,true)?$key:'[other key]';
    }
    private static function shape_keys(array $value): array {
        $out=[];foreach(array_slice(array_keys($value),0,32) as $key)$out[]=self::shape_key($key);return $out;
    }
    private static function shape_types(array $value): array {
        $out=[];foreach(array_slice($value,0,32,true) as $key=>$v)$out[self::shape_key($key)]=get_debug_type($v);return $out;
    }
    private static function id_shape($value): array {
        $kind=is_string($value)?(preg_match('/^\d+$/D',$value)?(self::row_identifier($value)!==null?'accepted_decimal':'decimal_out_of_range'):'not_decimal'):(is_int($value)?(self::row_identifier($value)!==null?'accepted_integer':'integer_out_of_range'):'not_integer');
        return ['type'=>get_debug_type($value),'kind'=>$kind,'string_length'=>is_string($value)?strlen($value):null];
    }
    private static function report_shape(array $response,string $device): array {
        $problem=self::report_problem($response,$device);$data=$response['data']??null;$first=null;$invalid=null;
        if(is_array($data)){$index=0;foreach($data as $row){if($index===0)$first=$row;if(isset($problem['row_index'])&&$index===$problem['row_index'])$invalid=$row;if(++$index>self::LIMIT)break;}}
        return ['cause'=>$problem['cause']??'unknown','top_keys'=>self::shape_keys($response),'top_types'=>self::shape_types($response),'data_type'=>get_debug_type($data),'data_count'=>is_array($data)?count($data):null,'data_is_list'=>is_array($data)?array_is_list($data):null,'first_row_type'=>get_debug_type($first),'first_row_keys'=>is_array($first)?self::shape_keys($first):[],'first_row_types'=>is_array($first)?self::shape_types($first):[],'first_row_id'=>self::id_shape(is_array($first)?($first['id']??null):null),'invalid_row_index'=>$problem['row_index']??null,'invalid_row_id'=>self::id_shape(is_array($invalid)?($invalid['id']??null):null),'invalid_map'=>$problem['map']??null];
    }
    public static function rows(array $response,string $device,array $range): array {
        $problem=self::report_problem($response,$device);if($problem)throw new RuntimeException('invalid_report_'.$problem['cause']);
        $out=[];foreach($response['data'] as $r){if(!is_array($r)||!is_string($r['keyword']??null)||strlen($r['keyword'])>500)throw new RuntimeException('invalid_report');$id=self::row_identifier($r['id']??null);if(!$id||isset($out[$id]))throw new RuntimeException('invalid_report');
            $history=[];$maps=[];if(is_array($r['positions']??null))$maps[]=$r['positions'];if(is_array($r[$device]['positions_history']??null))$maps[]=$r[$device]['positions_history'];
            if(is_array($r[$device]['history']??null)){$map=[];foreach($r[$device]['history'] as $day=>$value){$map[$day]=is_array($value)?($value['pos']??null):null;}$maps[]=$map;}
            foreach($maps as $map){if(count($map)>1000)throw new RuntimeException('invalid_report');foreach($map as $day=>$v){if(self::day($day)&&$day>=$range['start']&&$day<=$range['end'])$history[$day]=self::integer($v,1,1000);}}
            ksort($history);$url=$r['url']??null;if(!is_string($url)||strlen($url)>2000||!in_array(parse_url($url,PHP_URL_SCHEME),['http','https'],true)||parse_url($url,PHP_URL_USER)!==null||parse_url($url,PHP_URL_PASS)!==null)$url=null;
            $out[$id]=['id'=>$id,'keyword'=>$r['keyword'],'volume'=>self::integer($r['searches']??($r['statistics']['searches']['current']??null)),'history'=>$history,'position'=>$history[$range['end']]??null,'url'=>$url,'status'=>is_string($r['status']??null)?substr($r['status'],0,40):null,'updated'=>self::day($r['updated']['date']??null)];
        }return $out;
    }
    private static function schedule_worker(): void { if(!wp_next_scheduled(self::WORKER)){$r=wp_schedule_single_event(time()+5,self::WORKER,[],true);if($r===false||is_wp_error($r))throw new RuntimeException('cron_failed');} }
    public static function enqueue(string $mode='full'): string {
        if(!self::allowed())return 'forbidden';return self::enqueue_as($mode,get_current_user_id());
    }
    private static function enqueue_as(string $mode,int $actor): string {
        if(!in_array($mode,['projects','full'],true)||self::token()==='')return 'missing_token';$lock=self::claim();if(!$lock)return 'busy';
        try{$old=get_option(self::QUEUE,[]);if(is_array($old)&&in_array($old['status']??'',['queued','running'],true)&&($old['updated_at']??0)>time()-600)return 'busy';
            if(is_array($old)&&($old['not_before']??0)>time())return 'rate_limited';if(is_array($old)&&($old['started_at']??0)>time()-60)return 'cooldown';
            $cfg=self::config();$data=self::state();if($mode==='full'&&!self::ready($cfg,$data))return 'configure_projects';
            $user=$actor?get_userdata($actor):false;if(!$user||!user_can($user,'manage_options'))return 'forbidden';
            $q=['run'=>wp_generate_uuid4(),'mode'=>$mode,'actor'=>$actor,'config'=>$cfg,'fingerprint'=>self::fingerprint(),'status'=>'queued','started_at'=>time(),'updated_at'=>time(),'tasks'=>[['kind'=>'projects','page'=>1,'device'=>'desktop','range'=>self::range(self::today())]],'projects'=>[],'reports'=>[],'errors'=>[]];self::save(self::QUEUE,$q);self::schedule_worker();return 'queued';
        }catch(Throwable $e){return 'storage_failed';}finally{self::release($lock);}
    }
    public static function worker(): void {
        $lock=self::claim();if(!$lock)return;
        try{$q=get_option(self::QUEUE,[]);if(!is_array($q)||($q['status']??'')!=='queued'||empty($q['tasks']))return;
            if(($q['fingerprint']??'')!==self::fingerprint())throw new RuntimeException('connection_changed');$u=get_userdata($q['actor']);if(!$u||!user_can($u,'manage_options'))throw new RuntimeException('forbidden');
            $task=array_shift($q['tasks']);$q['status']='running';$q['updated_at']=time();self::save(self::QUEUE,$q);$response=self::request($task);$pagination=self::pagination($response,$task['page']);
            if($task['kind']==='projects'){
                foreach(self::projects($response) as $id=>$p)$q['projects'][$id]=$p;
                if($pagination['next']&&$task['page']<self::MAX_PAGES){$task['page']++;array_unshift($q['tasks'],$task);}else{
                    $data=self::state();$data['fingerprint']=self::fingerprint();$data['projects']=$q['projects'];$data['projects_checked_at']=time();$data['projects_limited']=$pagination['next'];self::save(self::DATA,$data);
                    if($q['mode']==='full'){foreach(['polska','katowice'] as $slot){$id=$q['config'][$slot];$p=$q['projects'][$id]??null;if(!$p||!$p['last_date']){$q['errors'][$slot]='no_project_measurement';continue;}
                        foreach(['desktop','mobile'] as $device)$q['tasks'][]=['kind'=>'positions','project'=>$id,'slot'=>$slot,'device'=>$device,'range'=>self::range($p['last_date']),'page'=>1];}
                    }
                }
            }else{
                $key=$task['project'].'_'.$task['device'];$rows=self::rows($response,$task['device'],$task['range']);$report=$q['reports'][$key]??['project'=>$task['project'],'device'=>$task['device'],'range'=>$task['range'],'rows'=>[]];
                foreach($rows as $id=>$r){if(isset($report['rows'][$id]))throw new RuntimeException('duplicate_page');$report['rows'][$id]=$r;}$q['reports'][$key]=$report;
                if($pagination['next']&&$task['page']<self::MAX_PAGES){$task['page']++;array_unshift($q['tasks'],$task);}else{$report['total']=$pagination['total'];$report['limited']=$pagination['next'];$report['fetched_at']=time();$report['source']='Senuto Rank Tracker';$data=self::state();$data['reports'][$key]=$report;$data['fingerprint']=self::fingerprint();self::save(self::DATA,$data);unset($q['reports'][$key]);}
            }
            $q['status']=$q['tasks']?'queued':($q['errors']?'partial':'complete');$q['updated_at']=time();self::save(self::QUEUE,$q);if($q['tasks'])self::schedule_worker();
        }catch(Throwable $e){if(isset($q)&&is_array($q)){$code=$e instanceof RuntimeException?$e->getMessage():'unexpected';$allowed=['connection_changed','forbidden','missing_token','transport_failed','http_401','http_403','http_418','http_failed','invalid_response','invalid_projects','invalid_project_date','invalid_pagination','invalid_report','duplicate_page','storage_failed','cron_failed'];
                if(isset($response,$task)&&is_array($response)&&($task['kind']??'')==='positions'&&in_array($code,['invalid_report_success_type','invalid_report_data_type','invalid_report_row_limit','invalid_report_device','invalid_report_row_type','invalid_report_keyword_type','invalid_report_keyword_length','invalid_report_row_id','invalid_report_duplicate_id','invalid_report_history_limit'],true)){$q['report_diagnostic']=self::report_shape($response,$task['device']);$code='invalid_report';}
                if(preg_match('/^rate_limited_(\d+)$/D',$code,$m)){$q['not_before']=time()+(int)$m[1];$code='rate_limited';}elseif(!in_array($code,$allowed,true))$code='unexpected';$q['status']='failed';$q['errors'][]=$code;$q['updated_at']=time();try{self::save(self::QUEUE,$q);}catch(Throwable $ignored){} }
        }finally{self::release($lock);}
    }
    public static function schedules(): void {
        if(!self::allowed())return;$c=self::config();$d=self::cached();
        if(!$c['daily']||!self::ready($c,$d)){wp_clear_scheduled_hook(self::DAILY);return;}if(!wp_next_scheduled(self::DAILY))self::next_daily();
    }
    private static function next_daily(): void { $d=new DateTimeImmutable('now',new DateTimeZone('Europe/Warsaw'));$next=$d->setTime(8,45);if($next<=$d)$next=$next->modify('+1 day');wp_schedule_single_event($next->getTimestamp(),self::DAILY); }
    public static function daily(): void {
        $c=self::config();$d=get_option(self::DATA,[]);if(!$c['daily']||!is_array($d)||($d['fingerprint']??'')!==self::fingerprint()||!self::ready($c,$d))return;
        $u=get_userdata($c['actor']);if(!$u||!user_can($u,'manage_options'))return;self::next_daily();self::enqueue_as('full',$c['actor']);
    }
    public static function filters(array $query): array {
        $get=static fn($k,$d)=>isset($query[$k])&&is_string($query[$k])?wp_unslash($query[$k]):$d;
        $pick=static fn($v,$choices,$d)=>in_array($v,$choices,true)?$v:$d;
        return ['slot'=>$pick($get('sp_project','polska'),['polska','katowice'],'polska'),'device'=>$pick($get('sp_device','desktop'),['desktop','mobile'],'desktop'),'days'=>(int)$pick($get('sp_days','30'),['7','30'],'30'),'q'=>mb_substr(sanitize_text_field($get('sp_q','')),0,120),'url'=>mb_substr(sanitize_text_field($get('sp_url','')),0,200),'presence'=>$pick($get('sp_presence','all'),['all','known','missing'],'all'),'change'=>$pick($get('sp_change','all'),['all','rise','fall','same','entered','left'],'all'),'volume'=>$pick($get('sp_volume','all'),['all','high','low','zero','unknown'],'all'),'bucket'=>$pick($get('sp_bucket','all'),['all','3','10','20','50'],'all'),'sort'=>$pick($get('sp_sort','volume'),['volume','position','change','decline'],'volume'),'page'=>max(1,min(40,(int)$get('sp_page','1')))];
    }
    public static function change(?int $before,?int $after): array {
        if($before===null||$after===null)return ['kind'=>'unknown','delta'=>null];
        if($before>50&&$after<=50)return ['kind'=>'entered','delta'=>null];if($before<=50&&$after>50)return ['kind'=>'left','delta'=>null];
        if($before>50||$after>50)return ['kind'=>'unknown','delta'=>null];$delta=$before-$after;return ['kind'=>$delta>0?'rise':($delta<0?'fall':'same'),'delta'=>$delta];
    }
    public static function filter_rows(array $report,array $filters): array {
        if(!self::day($report['range']['end']??null))return [];$start=self::range($report['range']['end'],$filters['days'])['start'];$out=[];
        foreach($report['rows']??[] as $r){$r['change']=self::change($r['history'][$start]??null,$r['position']);$v=$r['volume'];$p=$r['position'];
            if(($filters['url']??'')!==''&&mb_stripos($r['url']??'',$filters['url'])===false)continue;
            if((($filters['presence']??'all')==='known'&&$p===null)||(($filters['presence']??'all')==='missing'&&$p!==null))continue;
            if($filters['q']!==''&&mb_stripos($r['keyword'],$filters['q'])===false)continue;if($filters['change']!=='all'&&$r['change']['kind']!==$filters['change'])continue;
            if($filters['bucket']!=='all'&&($p===null||$p>(int)$filters['bucket']))continue;
            if(($filters['volume']==='high'&&($v===null||$v<100))||($filters['volume']==='low'&&($v===null||$v<1||$v>=100))||($filters['volume']==='zero'&&$v!==0)||($filters['volume']==='unknown'&&$v!==null))continue;$out[]=$r;
        }
        usort($out,static function($a,$b)use($filters){if($filters['sort']==='position')return ($a['position']??PHP_INT_MAX)<=>($b['position']??PHP_INT_MAX);if($filters['sort']==='decline')return ($a['change']['delta']??PHP_INT_MAX)<=>($b['change']['delta']??PHP_INT_MAX);if($filters['sort']==='change')return ($b['change']['delta']??-PHP_INT_MAX)<=>($a['change']['delta']??-PHP_INT_MAX);return ($b['volume']??-1)<=>($a['volume']??-1);});return $out;
    }
    public static function history(array $report,int $days): array {
        if(!self::day($report['range']['end']??null))return [];$range=self::range($report['range']['end'],$days);$out=[];$d=new DateTimeImmutable($range['start']);
        for($i=0;$i<$days;$i++){$day=$d->modify('+'.$i.' days')->format('Y-m-d');$known=0;$buckets=[3=>0,10=>0,20=>0,50=>0];foreach($report['rows']??[] as $r){$p=$r['history'][$day]??null;if($p===null)continue;$known++;foreach($buckets as $n=>$count){if($p<=$n)$buckets[$n]++;}}
            $out[]=['day'=>$day,'known'=>$known,'top3'=>$known?$buckets[3]:null,'top10'=>$known?$buckets[10]:null,'top20'=>$known?$buckets[20]:null,'top50'=>$known?$buckets[50]:null];}return $out;
    }
    public static function chart(array $history): string {
        if(!$history)return '<p>Brak historii pozycji.</p>';$n=count($history);$max=1;foreach($history as $h){if($h['top10']!==null)$max=max($max,$h['top10']);}$segments=[];$current=[];$dots=[];
        foreach($history as $i=>$h){if($h['top10']===null){if($current)$segments[]=implode(' ',$current);$current=[];continue;}$x=40+($n>1?$i*720/($n-1):0);$y=190-$h['top10']*150/$max;$point=round($x,2).','.round($y,2);$current[]=$point;$dots[]='<circle cx="'.round($x,2).'" cy="'.round($y,2).'" r="3"><title>'.esc_html($h['day'].' · TOP10 '.$h['top10'].' · znane pozycje '.$h['known']).'</title></circle>';}
        if($current)$segments[]=implode(' ',$current);$s='<svg viewBox="0 0 800 230" role="img" aria-label="Liczba pobranych fraz w TOP10, luki oznaczają brak danych"><path d="M40 20V190H760" fill="none" stroke="#d9e1eb"/>';
        foreach($segments as $segment)$s.='<polyline points="'.esc_attr($segment).'" fill="none" stroke="#2583cf" stroke-width="3"/>';$s.='<g fill="#2583cf">'.implode('',$dots).'</g><text x="40" y="218" fill="#60718a">'.esc_html($history[0]['day']).'</text><text x="760" y="218" text-anchor="end" fill="#60718a">'.esc_html($history[$n-1]['day']).'</text></svg>';return $s;
    }
    private static function scalar(string $name): string { return isset($_POST[$name])&&is_string($_POST[$name])?wp_unslash($_POST[$name]):''; }
    public static function handle(string $action): void {
        if(!self::allowed()||($_SERVER['REQUEST_METHOD']??'')!=='POST'||!wp_verify_nonce(self::scalar('_wpnonce'),$action))wp_die('Brak uprawnień.',403);
        $code='invalid_input';
        if($action==='zp_senuto_positions_refresh'){$mode=self::scalar('mode');$code=in_array($mode,['projects','full'],true)?self::enqueue($mode):'invalid_input';}
        elseif($action==='zp_senuto_positions_config'){
            $data=self::cached();$ids=[];foreach(['polska','katowice'] as $slot)$ids[$slot]=self::integer(self::scalar($slot),1);
            if($ids['polska']&&$ids['katowice']&&$ids['polska']!==$ids['katowice']&&isset($data['projects'][$ids['polska']],$data['projects'][$ids['katowice']])){try{self::save(self::CONFIG,$ids+['daily'=>self::scalar('daily')==='1','actor'=>get_current_user_id()]);wp_clear_scheduled_hook(self::DAILY);self::schedules();$code='saved';}catch(Throwable $e){$code='storage_failed';}}
        }
        wp_safe_redirect(add_query_arg(['page'=>self::PAGE,'sp_notice'=>$code],admin_url('admin.php')));exit;
    }
    private static function form(string $action): void {echo '<form method="post" action="'.esc_url(admin_url('admin-post.php')).'"><input type="hidden" name="action" value="'.esc_attr($action).'">';wp_nonce_field($action);}
    private static function num(?int $n): string {return $n===null?'—':number_format_i18n($n);}
    public static function assets(): void {if(!self::allowed()||!isset($_GET['page'])||$_GET['page']!==self::PAGE)return;wp_enqueue_style('zp-senuto-positions-20261010',ZP_SUITE_URL.'assets/senuto-positions/admin.css',[],ZP_SUITE_VERSION);}
    public static function render(): void {
        if(!self::allowed())return;$cfg=self::config();$data=self::cached();$filters=self::filters($_GET);$q=get_option(self::QUEUE,[]);$q=is_array($q)?$q:[];
        if(function_exists('zp_admin20_open'))zp_admin20_open('Pozycje w Senuto','positions','Projekty Polska i Katowice · pozycje monitorowanych fraz');else echo '<div class="wrap zpAdmin20"><h1>Pozycje w Senuto</h1>';
        echo '<div class="zpSenuto"><section class="zpAdmin20__card zpSenuto__intro"><div><span class="zpSenuto__eyebrow">SENUTO · RANK TRACKER</span><h2>Pozycje i zmiany</h2><p>Raport pobieramy z istniejących projektów. Nie uruchamiamy nowego pomiaru SERP.</p></div>';self::form('zp_senuto_positions_refresh');echo '<input type="hidden" name="mode" value="full"><button class="button button-primary"'.(!self::ready($cfg,$data)?' disabled':'').'>Aktualizuj raporty</button></form></section>';
        if(isset($_GET['sp_notice'])&&is_string($_GET['sp_notice'])){$code=sanitize_key(wp_unslash($_GET['sp_notice']));$labels=['queued'=>'Aktualizacja jest w kolejce. Odśwież panel po chwili.','saved'=>'Zapisano projekty i harmonogram.','busy'=>'Poprzednie pobieranie trwa.','missing_token'=>'Najpierw zapisz token Senuto w Przygotuj treści.','configure_projects'=>'Pobierz listę i przypisz oba projekty.','cooldown'=>'Poczekaj minutę przed następnym pobraniem.','rate_limited'=>'Senuto ograniczyło zapytania. Poczekaj na zakończenie przerwy.'];echo '<p class="zpSenuto__notice">'.esc_html($labels[$code]??'Nie wykonano operacji. Sprawdź konfigurację.').'</p>';}
        if(in_array($q['status']??'',['queued','running'],true))echo '<p class="zpSenuto__notice">Pobieranie w tle: '.esc_html($q['status']).'. Pozostałe kroki: '.count($q['tasks']??[]).'. Widoczny zapis może pochodzić z poprzedniej aktualizacji.</p>';
        if(!empty($q['errors'])){$errors=['http_401'=>'token wygasł lub został odrzucony — zapisz nowy token','http_403'=>'konto nie ma dostępu do raportu','http_418'=>'Senuto odrzuciło parametry lub dostęp projektu','rate_limited'=>'limit zapytań — pobieranie wstrzymane','no_project_measurement'=>'projekt nie ma potwierdzonej daty pomiaru','transport_failed'=>'połączenie z Senuto nie powiodło się','connection_changed'=>'zmieniono token w trakcie pobierania'];$messages=[];foreach($q['errors'] as $error)$messages[]=is_string($error)?($errors[$error]??$error):'nieprawidłowy stan';echo '<p class="zpSenuto__notice">Ostatnia aktualizacja wymaga sprawdzenia: '.esc_html(implode(', ',$messages)).'. Poprzednich danych nie zamieniamy na zera.</p>';}
        if(($q['status']??'')==='failed'&&is_array($q['report_diagnostic']??null))echo '<details class="zpAdmin20__card"><summary>Diagnostyka struktury odpowiedzi Senuto</summary><p class="zpAdmin20__muted">Wyłącznie nazwy pól, typy, liczba wierszy i indeks walidacji. Bez wartości odpowiedzi, tokenów i nagłówków.</p><pre>'.esc_html(wp_json_encode($q['report_diagnostic'],JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE)).'</pre></details>';
        echo '<details class="zpAdmin20__card zpSenuto__settings"'.(!self::ready($cfg,$data)?' open':'').'><summary>Połączenie i projekty</summary><p>Token: <b>'.(self::token()!==''?'zapisany':'brak').'</b> · <a href="'.esc_url(function_exists('zp_admin20_url')?zp_admin20_url('zp-suite-prepare'):admin_url('admin.php?page=zp-suite-prepare')).'">Zmień prywatny token w Przygotuj treści</a></p>';
        self::form('zp_senuto_positions_refresh');echo '<input type="hidden" name="mode" value="projects"><button class="button">Pobierz listę projektów z Senuto</button></form>';
        self::form('zp_senuto_positions_config');echo '<div class="zpSenuto__settingsGrid">';foreach(['polska'=>'Projekt Polska','katowice'=>'Projekt Katowice'] as $slot=>$label){echo '<label>'.esc_html($label).'<select name="'.esc_attr($slot).'" required><option value="">Wybierz projekt</option>';foreach($data['projects']??[] as $p)echo '<option value="'.$p['id'].'"'.selected($cfg[$slot],$p['id'],false).'>'.esc_html($p['name'].' · #'.$p['id']).'</option>';echo '</select></label>';}
        echo '</div><label><input type="checkbox" name="daily" value="1"'.checked($cfg['daily'],true,false).'> Pobieraj codziennie o 08:45 czasu Warszawy po skonfigurowaniu obu projektów</label><p class="zpAdmin20__muted">Nazwy „Polska” i „Katowice” są przypisaniami projektów w panelu. Faktyczny kraj i region pokazujemy osobno z API. Codzienne pobieranie nie przyspiesza pomiarów Senuto.</p><button class="button">Zapisz projekty</button></form></details>';
        echo '<form method="get" action="'.esc_url(admin_url('admin.php')).'" class="zpSenuto__filters"><input type="hidden" name="page" value="'.self::PAGE.'">';
        $select=static function($name,$label,$values,$active){echo '<label>'.esc_html($label).'<select name="'.esc_attr($name).'">';foreach($values as $value=>$text)echo '<option value="'.esc_attr($value).'"'.selected((string)$active,(string)$value,false).'>'.esc_html($text).'</option>';echo '</select></label>';};
        $select('sp_project','Projekt',['polska'=>'Polska','katowice'=>'Katowice'],$filters['slot']);$select('sp_device','Urządzenie',['desktop'=>'Desktop','mobile'=>'Mobile'],$filters['device']);$select('sp_days','Okres',['7'=>'7 dni','30'=>'30 dni'],$filters['days']);
        echo '<label>Fraza<input type="search" name="sp_q" value="'.esc_attr($filters['q']).'" placeholder="Szukaj frazy"></label><label>Rankujący URL<input type="search" name="sp_url" value="'.esc_attr($filters['url']).'" placeholder="Fragment adresu"></label>';
        $select('sp_presence','Dostępność pozycji',['all'=>'Wszystkie','known'=>'Pozycja znana','missing'=>'Brak pomiaru w tym dniu'],$filters['presence']);
        $select('sp_change','Zmiana',['all'=>'Wszystkie','rise'=>'Wzrosty','fall'=>'Spadki','same'=>'Bez zmian','entered'=>'Wejścia do TOP50','left'=>'Wyjścia z TOP50'],$filters['change']);$select('sp_volume','Wolumen',['all'=>'Wszystkie','high'=>'100+ wyszukiwań','low'=>'1–99','zero'=>'0 z API','unknown'=>'Brak danych'],$filters['volume']);$select('sp_bucket','Pozycje',['all'=>'Wszystkie','3'=>'TOP3','10'=>'TOP10','20'=>'TOP20','50'=>'TOP50'],$filters['bucket']);$select('sp_sort','Sortuj',['volume'=>'Wolumen malejąco','position'=>'Najlepsza pozycja','change'=>'Największy wzrost','decline'=>'Największy spadek'],$filters['sort']);echo '<button class="button">Pokaż</button></form>';
        $project=$data['projects'][$cfg[$filters['slot']]]??null;$report=$data['reports'][$cfg[$filters['slot']].'_'.$filters['device']]??null;
        if(!$project||!is_array($report)){echo '<section class="zpAdmin20__card"><h2>Raport czeka na dane</h2><p>Pobierz listę projektów, przypisz oba projekty i uruchom aktualizację raportów. Brak danych nie oznacza pozycji0.</p></section></div>';if(function_exists('zp_admin20_close'))zp_admin20_close();else echo '</div>';return;}
        $end=$report['range']['end'];$range=self::range($end,$filters['days']);$age=(int)(new DateTimeImmutable($end))->diff(new DateTimeImmutable(self::today()))->days;
        echo '<section class="zpAdmin20__card zpSenuto__source"><h2>'.esc_html($project['name']).'</h2><p>Projekt #'.(int)$project['id'].' · '.esc_html($project['domain']).' · '.esc_html($filters['device']).' · krajAPI: '.esc_html(self::num($project['country_id'])).' · regionAPI: '.esc_html(self::num($project['region_id'])).'</p><p><b>Dzień raportu Senuto: '.esc_html($end).'</b> · zakres wykresu '.esc_html($range['start'].'–'.$end).' · pobrano do panelu '.esc_html(wp_date('d.m.Y H:i',(int)$report['fetched_at'],new DateTimeZone('Europe/Warsaw'))).'</p>';
        $other=$data['projects'][$cfg[$filters['slot']==='polska'?'katowice':'polska']]??null;
        if(is_array($other)&&$project['id']!==$other['id']&&$project['country_id']!==null&&$project['region_id']!==null&&$project['country_id']===$other['country_id']&&$project['region_id']===$other['region_id'])echo '<p class="zpSenuto__notice">Oba projekty mają w API ten sam kraj i region. Nazwy „Polska” i „Katowice” nie potwierdzają dwóch różnych obszarów pomiaru.</p>';
        echo '<p class="zpAdmin20__muted">Data ostatniego pomiaru w metadanych projektu: '.esc_html($project['last_date']??'—').'. Tabela pokazuje wyłącznie pozycje zwrócone dla dnia raportu i wybranego urządzenia; braków nie uzupełniamy wcześniejszą pozycją.</p>';
        if($filters['slot']==='katowice'&&(!$project['region_id']))echo '<p class="zpAdmin20__muted">API nie potwierdza ustawionego miasta dla tego projektu. Nazwa „Katowice” sama nie potwierdza lokalnego pomiaru.</p>';
        if($age>2)echo '<p class="zpSenuto__notice">Dane pomiarowe mają '.$age.' dni. Dzisiejsze pobranie nie jest dzisiejszym pomiarem. Sprawdź aktualizację i plan projektu w Senuto.</p>';
        if(($report['limited']??false)||$project['last_date']!==$end)echo '<p class="zpSenuto__notice">Raport jest częściowy lub pochodzi z wcześniejszego pomiaru. Liczniki dotyczą pobranych fraz.</p>';echo '<p class="zpAdmin20__muted">Pobrane frazy: '.count($report['rows']).' / '.esc_html(self::num($report['total']??null)).'. Wolumen miesięczny pochodzi z bazy fraz Senuto; projekt lokalny nie zmienia go w miejski wolumen.</p></section>';
        $history=self::history($report,$filters['days']);$latest=$history[count($history)-1];echo '<div class="zpSenuto__kpis">';foreach([3,10,20,50] as $n)echo '<section class="zpAdmin20__card"><span>TOP'.$n.'</span><strong>'.esc_html(self::num($latest['top'.$n])).'</strong><small>z pobranych fraz · '.$end.'</small></section>';echo '</div><section class="zpAdmin20__card"><h2>Jak zmieniała się liczba fraz w TOP10</h2><p class="zpAdmin20__muted">Wyłącznie istniejące pomiary. Luki pozostają lukami; wielkość próbki może się zmieniać.</p>'.self::chart($history).'<details><summary>Historia w tabeli</summary><div class="zpAdmin20__tableWrap"><table class="zpAdmin20__table"><thead><tr><th>Data</th><th>Znane pozycje</th><th>TOP3</th><th>TOP10</th><th>TOP20</th><th>TOP50</th></tr></thead><tbody>';foreach($history as $h){echo '<tr><td>'.esc_html($h['day']).'</td>';foreach(['known','top3','top10','top20','top50'] as $key)echo '<td>'.esc_html(self::num($h[$key])).'</td>';echo '</tr>';}echo '</tbody></table></div></details></section>';
        $rows=self::filter_rows($report,$filters);$page=min($filters['page'],max(1,(int)ceil(count($rows)/50)));$visible=array_slice($rows,($page-1)*50,50);$changeLabels=['unknown'=>'—','entered'=>'Wejście do TOP50','left'=>'Wyjście z TOP50','rise'=>'Wzrost','fall'=>'Spadek','same'=>'Bez zmian'];
        echo '<section class="zpAdmin20__card"><h2>Monitorowane frazy</h2><p>'.count($rows).' fraz po filtrach · zmiana od '.esc_html($range['start']).' do '.esc_html($end).'. Plus oznacza poprawę pozycji.</p><div class="zpAdmin20__tableWrap"><table class="zpAdmin20__table"><thead><tr><th>Fraza</th><th>Wolumen/mies.</th><th>Pozycja '.$end.'</th><th>Zmiana okresu</th><th>Rankujący URL</th></tr></thead><tbody>';
        foreach($visible as $r){$delta=$r['change']['delta'];$kind=$r['change']['kind'];echo '<tr><td><b>'.esc_html($r['keyword']).'</b></td><td>'.esc_html(self::num($r['volume'])).'</td><td>'.esc_html($r['position']!==null&&$r['position']>50?'50+':self::num($r['position'])).'</td><td><span class="zpSenuto__change zpSenuto__change--'.esc_attr($kind).'">'.esc_html($delta!==null?($delta>0?'+':'').$delta:$changeLabels[$kind]).'</span></td><td>';if($r['url'])echo '<a href="'.esc_url($r['url']).'" target="_blank" rel="noopener">'.esc_html(mb_substr($r['url'],0,100)).'</a>';else echo '—';echo '</td></tr>';}
        if(!$visible)echo '<tr><td colspan="5">Brak fraz spełniających wybrane filtry.</td></tr>';echo '</tbody></table></div><div class="zpSenuto__pager">';
        foreach([max(1,$page-1)=>'Poprzednia',min(max(1,(int)ceil(count($rows)/50)),$page+1)=>'Następna'] as $p=>$label){$args=['page'=>self::PAGE,'sp_project'=>$filters['slot'],'sp_device'=>$filters['device'],'sp_days'=>$filters['days'],'sp_q'=>$filters['q'],'sp_url'=>$filters['url'],'sp_presence'=>$filters['presence'],'sp_change'=>$filters['change'],'sp_volume'=>$filters['volume'],'sp_bucket'=>$filters['bucket'],'sp_sort'=>$filters['sort'],'sp_page'=>$p];if(function_exists('zp_suite_workspace_active')&&zp_suite_workspace_active())$args['zp_workspace']='1';echo '<a class="button" href="'.esc_url(add_query_arg($args,admin_url('admin.php'))).'">'.esc_html($label).'</a>';}
        echo '<span>Strona '.$page.' / '.max(1,(int)ceil(count($rows)/50)).'</span></div><p class="zpAdmin20__muted">„—” oznacza brak porównywalnego pomiaru. Przy50+ nie pokazujemy pozornej dokładnej zmiany. To pozycje Senuto, a nie średnia pozycja GSC ani liczba wizyt.</p></section></div>';
        if(function_exists('zp_admin20_close'))zp_admin20_close();else echo '</div>';
    }
}
foreach(['zp_senuto_positions_refresh','zp_senuto_positions_config'] as $zp_sp_action){add_action('admin_post_'.$zp_sp_action,static function()use($zp_sp_action){ZP_Senuto_Positions_20261010::handle($zp_sp_action);});}unset($zp_sp_action);
add_action('admin_enqueue_scripts',[ZP_Senuto_Positions_20261010::class,'assets']);
add_action('admin_init',[ZP_Senuto_Positions_20261010::class,'schedules']);
add_action(ZP_Senuto_Positions_20261010::WORKER,[ZP_Senuto_Positions_20261010::class,'worker']);
add_action(ZP_Senuto_Positions_20261010::DAILY,[ZP_Senuto_Positions_20261010::class,'daily']);
function zp_admin_senuto_positions_render(): void {ZP_Senuto_Positions_20261010::render();}
