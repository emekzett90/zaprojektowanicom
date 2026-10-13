<?php
if (!defined('ABSPATH')) exit;
final class ZPT2_Engine {
    private static $route_probe=['calls'=>0,'observations'=>[]];
    const WORKER='zpt2_worker';const DAILY='zpt2_daily_scan';const SCAN='zpt2_scan_step';
    public static function ready(): bool {foreach(['ZP_EN_Rewrite_20261011','ZP_Translator_Approved_Overlay_20261011','ZPL\\Html','ZPL\\Page','ZPL\\Router','ZPTE\\Settings']as$c)if(!class_exists($c))return false;return true;}
    /** Register before ZPL Runtime resolves and caches routes at plugins_loaded PHP_INT_MIN. */
    public static function public_hooks(): void {
        add_filter('zp_en_public_price_html',[self::class,'serving_probe'],-100,3);
        add_filter('zp_en_rewrite_routes',[self::class,'routes'],30);add_filter('zpl_has_page',[self::class,'has_page'],30,2);add_filter('zpl_sitemap_pages',[self::class,'sitemap'],30);
        add_action('plugins_loaded',[self::class,'fatal_probe_boot'],PHP_INT_MIN); // wp_salt is loaded after active plugin files.
        if(class_exists('ZPL\\Router'))\ZPL\Router::reset(); // Suite may have cached routes before this companion was included.
    }
    private static function probe_token(array $map): string {return hash_hmac('sha256','zpt2-serving|'.$map['id'].'|'.$map['digest'],wp_salt('auth'));}
    /** Only the already-signed internal checking GET receives a request-local fatal observer. */
    public static function fatal_probe_boot(): void {
        $token=$_SERVER['HTTP_X_ZPT2_PROBE']??null;if(($_SERVER['REQUEST_METHOD']??'')!=='GET'||!is_string($token)||!preg_match('/^[a-f0-9]{64}$/D',$token))return;
        $uri=(string)($_SERVER['REQUEST_URI']??'');$query=wp_parse_url($uri,PHP_URL_QUERY);$fresh=$query!==null;if($fresh&&(!is_string($query)||!preg_match('/^zpt2_check=[a-f0-9]{32}$/D',$query)))return;$source=(string)wp_parse_url($uri,PHP_URL_PATH);$p=self::active($source);
        if(!$p||!hash_equals(self::probe_token($p),$token))return;$job=ZPT2_Store::get('job_'.$p['id']);if(($job['state']??'')!=='checking'||($job['source']??null)!==$source)return;
        if($fresh&&(($job['id']??null)!==$p['id']||($job['publication_only']??false)!==true||($job['commit']['target']??null)!==['id'=>$p['id'],'digest'=>$p['digest']]))return;
        $observer=wp_json_encode(['token'=>$token,'armed'=>true,'source'=>$source,'fresh_query'=>$fresh,'plugins_loaded'=>function_exists('did_action')?did_action('plugins_loaded'):null,'init'=>function_exists('did_action')?did_action('init'):null,'router_loaded'=>class_exists('ZPL\\Router',false)]);if(!headers_sent())header('X-ZPT2-Observer: '.base64_encode($observer));
        add_filter('wp_php_error_message',static function($message,$error)use($token){$json=self::fatal_json(is_array($error)?$error:[],$token);if($json==='')return$message;if(!headers_sent())header('X-ZPT2-Fatal: '.base64_encode($json));return$message.'<pre id="zpt2-fatal-probe">'.htmlspecialchars($json,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8').'</pre>';},PHP_INT_MAX,2);
        register_shutdown_function(static function()use($token){$json=self::fatal_json(error_get_last()??[],$token);if($json==='')return;if(!headers_sent())header('X-ZPT2-Fatal: '.base64_encode($json));echo '<pre id="zpt2-fatal-probe">'.htmlspecialchars($json,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8').'</pre>';});
    }
    private static function fatal_json(array $last,string $token): string {
        if(!in_array($last['type']??0,[E_ERROR,E_PARSE,E_CORE_ERROR,E_COMPILE_ERROR,E_USER_ERROR,E_RECOVERABLE_ERROR],true))return'';
        $message=(string)($last['message']??'');if(preg_match('~authorization|bearer|api[_ -]?key|password|secret|token|\bsk-[A-Za-z0-9_-]{10,}~i',$message))$message='';$message=preg_split('/\r?\n(?:Stack trace:|#\d|\s*thrown in)/',$message,2)[0];$message=preg_replace('~"[^"\r\n]*"|\'[^\'\r\n]*\'~','[quoted]',$message)??'';
        $message=preg_replace('~\b(?:sk-[A-Za-z0-9_-]{10,}|resp_[A-Za-z0-9_-]{8,}|[a-f0-9]{32,}|(?:bearer|authorization|api[_ -]?key|token|password|secret)\s*[:=]\s*\S+)~i','[redacted]',$message)??'';$message=str_replace(ABSPATH,'[ABSPATH]/',$message);$message=mb_substr(preg_replace('/[\x00-\x1f\x7f]/',' ',$message)??'',0,1200,'UTF-8');
        $file=(string)($last['file']??'');$file=strpos($file,ABSPATH)===0?substr($file,strlen(ABSPATH)):basename($file);
        $json=wp_json_encode(['token'=>$token,'php_fatal'=>['type'=>(int)$last['type'],'file'=>$file,'line'=>(int)($last['line']??0),'message'=>$message]],JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT);return is_string($json)?$json:'';
    }
    /** Signed internal GET only. Report renderer facts before later display filters; never change SEO or body text. */
    public static function serving_probe(string $html,string $lang,string $source): string {
        $token=$_SERVER['HTTP_X_ZPT2_PROBE']??'';if($lang!=='en'||!is_string($token)||!preg_match('/^[a-f0-9]{64}$/D',$token)||!self::ready())return$html;
        $p=self::active($source);if(!$p||!hash_equals(self::probe_token($p),$token))return$html;$job=ZPT2_Store::get('job_'.$p['id']);if(($job['state']??'')!=='checking')return$html;
        $stats=\ZPL\Page::$stats;$found=(int)($stats['found']??0);$hit=(int)($stats['hit']??0);$mapped=\ZPL\Router::$mapped;$has=\ZPL\Dict::has_page($source);$is404=is_404();$coverage=$found>0?$hit/$found:1;$missing=[];
        foreach(array_slice(\ZPL\Page::$missing,0,20,true)as$key=>$kind)$missing[]=['sha256'=>hash('sha256',$key),'kind'=>$kind,'bytes'=>strlen($key)];
        $probe=['token'=>$token,'mapped'=>(bool)$mapped,'has_page'=>$has,'is_404'=>$is404,'found'=>$found,'hit'=>$hit,'miss'=>(int)($stats['miss']??0),'coverage'=>$coverage,'public_pointer'=>self::public_pointer($source,$p),'runtime_noindex'=>(!$mapped||!$has||$coverage<0.9||$is404),'missing_sample'=>$missing,'early_routes'=>self::$route_probe,'late_route'=>\ZPL\Router::routes()[$source]??null];
        $tag='<script type="application/json" id="zpt2-serving-probe">'.wp_json_encode($probe,JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT).'</script>';
        return str_replace('</head>',$tag.'</head>',$html);
    }
    public static function boot(): void {
        add_action('admin_init',['ZPT2_Admin','redirect_legacy']);add_action('admin_menu',['ZPT2_Admin','hide_legacy_menu'],PHP_INT_MAX);
        add_action('admin_menu',['ZPT2_Admin','menu'],90);add_action('admin_post_zpt2_action',['ZPT2_Admin','handle']);add_action('wp_ajax_zpt2_status',['ZPT2_Admin','ajax']);
        if(!self::ready())return;
        ZPT2_Shared::boot();
        add_action(self::WORKER,[self::class,'worker']);add_action(self::DAILY,[self::class,'daily']);add_action(self::SCAN,[self::class,'scan_step']);
        add_action('init',[self::class,'schedule'],99);
    }
    public static function activate(): void {
        if(!self::ready())return;
        try{ZPT2_Store::lock(static function(){ZPT2_Store::migration();ZPT2_Store::put('takeover',true);if(!ZPT2_Store::get('config'))ZPT2_Store::put('config',['daily_enabled'=>true,'auto_enabled'=>false,'actor'=>get_current_user_id()]);ZPT2_Store::put('setup_error','');});}catch(Throwable$e){ZPT2_Store::put('setup_error',self::error($e));}
        self::schedule();
    }
    public static function deactivate(): void {foreach([self::WORKER,self::DAILY,self::SCAN]as$h)wp_clear_scheduled_hook($h);}
    public static function next_daily(): int {$now=new DateTimeImmutable('now',new DateTimeZone('Europe/Warsaw'));$next=$now->setTime(3,0);if($next<=$now)$next=$next->modify('+1 day');return $next->getTimestamp();}
    public static function schedule(): void {
        $cfg=ZPT2_Store::get('config');if(!empty($cfg['daily_enabled'])&&!wp_next_scheduled(self::DAILY))wp_schedule_single_event(self::next_daily(),self::DAILY);
        $scan=ZPT2_Store::get('scan');if(($scan['state']??'')==='running'&&!wp_next_scheduled(self::SCAN))wp_schedule_single_event(time()+2,self::SCAN);
        if(self::has_work()&&!wp_next_scheduled(self::WORKER))wp_schedule_single_event(time()+5,self::WORKER);
    }
    private static function has_work(): bool {foreach(ZPT2_Store::get('jobs')as$id=>$s)if(in_array($s['state']??'',['queued','translating','repairing','checking','deferred'],true))return true;return false;}
    public static function can(int $actor,int $post=0): bool {return $actor>0&&user_can($actor,'manage_options')&&user_can($actor,'edit_posts')&&user_can($actor,'unfiltered_html')&&(!$post||user_can($actor,'edit_post',$post));}
    public static function error(Throwable $e): string {
        $code=$e->getMessage();return preg_match('/^[a-z_0-9]{3,50}$/D',$code)?$code:'internal_error';
    }
    public static function daily(): void {try{if(!empty(ZPT2_Store::get('config')['daily_enabled']))self::start_scan();}catch(Throwable$e){ZPT2_Store::put('scan_error',self::error($e));}self::schedule();}
    public static function start_scan(): void {
        ZPT2_Store::lock(static function(){
            ZPT2_Store::migration();$old=ZPT2_Store::get('scan');if(($old['state']??'')==='running')return;
            $ids=get_posts(['post_type'=>['post','page'],'post_status'=>'publish','posts_per_page'=>-1,'fields'=>'ids','orderby'=>'ID','order'=>'ASC','suppress_filters'=>false]);
            if(count($ids)>5000)throw new RuntimeException('inventory_too_large');
            ZPT2_Store::put('scan',['state'=>'running','ids'=>array_map('intval',$ids),'cursor'=>0,'total'=>count($ids),'at'=>time(),'errors'=>0]);
        });self::schedule();
    }
    public static function scan_step(): void {
        try{ZPT2_Store::lock(static function(){
            $s=ZPT2_Store::get('scan');if(($s['state']??'')!=='running')return;$inventory=ZPT2_Store::get('inventory');$started=microtime(true);
            for($n=0;$n<3&&$s['cursor']<$s['total']&&microtime(true)-$started<23;$n++){
                $id=$s['ids'][$s['cursor']++];
                try{$snap=ZPT2_Source::capture($id);$source=$snap['source'];$key=hash('sha256',$source);$previous=ZPT2_Store::get('baseline_'.$key);$active=self::active($source);$row=$inventory[$source]??[];
                    // A legacy reference is imported only while the current native guard actually approves it.
                    $legacy=ZP_EN_Rewrite_20261011::approved_page($source);
                    if(!$previous&&!empty($legacy['map'])){ZPT2_Store::put('legacy_'.$key,['revision'=>$legacy['revision'],'map_hash'=>ZPT2_Store::hash($legacy['map']),'snapshot'=>$snap,'at'=>time()]);ZPT2_Store::put('baseline_'.$key,$snap);$previous=$snap;}
                    $state=!$previous?'new':(($previous['hash']??'')!==$snap['hash']?'changed':($active?'published':'legacy'));
                    $job=isset($row['job'])?ZPT2_Store::get('job_'.$row['job']):[];
                    if($job&&in_array($job['state'],['queued','translating','repairing','checking','deferred','attention','unknown'],true))$state=$job['state'];
                    $inventory[$source]=array_merge($row,array_intersect_key($snap,array_flip(['source','en','post_id','title'])),['state'=>$state,'error'=>'','source_hash'=>$snap['hash'],'updated_at'=>time()]);
                    ZPT2_Store::put('snapshot_'.$key,$snap);
                }catch(Throwable$e){$s['errors']++;$code=self::error($e);if(in_array($code,['source_private','source_noindex'],true)){foreach($inventory as$path=>$row)if(($row['post_id']??0)===$id)unset($inventory[$path]);}else{$s['last_error']=['post_id'=>$id,'code'=>$code];try{$d=ZPT2_Source::descriptor($id);$inventory[$d['source']]=array_merge($inventory[$d['source']]??[],$d,['state'=>'attention','error'=>$code,'updated_at'=>time()]);}catch(Throwable$ignored){}}}
            }
            if($s['cursor']>=$s['total']){$s['state']='complete';$s['completed_at']=time();$s['baseline_complete']=true;}
            ZPT2_Store::put('inventory',$inventory);ZPT2_Store::put('scan',$s);
        });}catch(Throwable$e){if(self::error($e)!=='busy')ZPT2_Store::put('scan_error',self::error($e));}self::schedule();self::auto_enqueue();
    }
    public static function auto_enqueue(): void {
        $cfg=ZPT2_Store::get('config');$scan=ZPT2_Store::get('scan');if(empty($cfg['auto_enabled'])||($scan['state']??'')!=='complete'||!self::can((int)($cfg['actor']??0)))return;
        $ids=[];foreach(ZPT2_Store::get('inventory')as$r)if(in_array($r['state']??'',['new','changed'],true)){$ids[]=$r['post_id'];if(count($ids)>=3)break;}
        foreach($ids as$id){try{self::enqueue([$id],$cfg['actor']);}catch(Throwable$e){ZPT2_Store::put('auto_error',['post_id'=>$id,'error'=>self::error($e),'at'=>time()]);}}
    }
    public static function enqueue(array $ids,int $actor): array {
        if(count($ids)<1||count($ids)>10||!self::can($actor))throw new RuntimeException('forbidden');$created=[];
        // One source per lease avoids an HTTP batch outliving the shared 120-second lease.
        foreach(array_unique(array_map('intval',$ids))as$id){
            if(!self::can($actor,$id))throw new RuntimeException('forbidden');
            $created[]=ZPT2_Store::lock(static function()use($id,$actor){
                ZPT2_Store::migration();$key=ZPT2_API::key();$snap=ZPT2_Source::capture($id);$source=$snap['source'];$k=hash('sha256',$source);$inventory=ZPT2_Store::get('inventory');
                $oldid=$inventory[$source]['job']??'';$old=$oldid?ZPT2_Store::get('job_'.$oldid):[];
                if($old&&($old['snapshot']['hash']??'')===$snap['hash'])return$old['id']; // Includes unknown/attention; repeated clicks never purchase a retry.
                if($old&&in_array($old['state'],['queued','translating','repairing','checking','deferred','unknown'],true))throw new RuntimeException('source_busy');
                $active=self::active($source);$previous=ZPT2_Store::get('baseline_'.$k);
                if($active&&($previous['hash']??'')===$snap['hash'])return$active['id'];
                $legacy=ZP_EN_Rewrite_20261011::approved_page($source);$ref=self::legacy_reference($source);$base=$active?$active['map']:($legacy['map']?:($ref['map']??[]));$effective=[];$items=[];
                // Reuse only identical source + kind + exact contexts from an accepted v2 snapshot.
                foreach($snap['units']as$uId=>$u){
                    $prior=$active['snapshot']['units'][$uId]??($ref['snapshot']['units'][$uId]??null);$accepted=$active['effective'][$uId]??($ref['map'][$u['pl']]??null);
                    if($prior===$u&&is_string($accepted)&&ZPT2_Source::checks($u['pl'],$accepted,$u['kind'],$snap)['valid'])$effective[$uId]=$accepted;else$items[$uId]=$u;
                }
                $jobid=bin2hex(random_bytes(16));$job=['id'=>$jobid,'source'=>$source,'post_id'=>$id,'actor'=>$actor,'snapshot'=>$snap,'state'=>'queued','created_at'=>time(),'updated_at'=>time(),'key_fingerprint'=>hash('sha256',$key),'policy'=>ZPT2_API::policy(),'effective'=>$effective,'base_map'=>$base,'requests'=>[],'repair_attempts'=>[],'error'=>''];
                foreach(self::batches($items)as$batch)$job['requests'][]=self::make_request($job,$batch);
                self::save_job($job);$inventory=ZPT2_Store::get('inventory');$inventory[$source]=array_merge($inventory[$source]??[],array_intersect_key($snap,array_flip(['source','en','post_id','title'])),['state'=>'queued','job'=>$jobid,'source_hash'=>$snap['hash'],'updated_at'=>time()]);ZPT2_Store::put('inventory',$inventory);return$jobid;
            });
        }self::schedule();return$created;
    }
    public static function batches(array $items): array {
        $out=[];$batch=[];$bytes=0;foreach($items as$id=>$u){$size=strlen(wp_json_encode($u));if($batch&&(count($batch)>=100||$bytes+$size>28000)){$out[]=$batch;$batch=[];$bytes=0;}$batch[$id]=$u;$bytes+=$size;}if($batch)$out[]=$batch;return$out;
    }
    private static function make_request(array $job,array $items,string $parent=''): string {
        $id=$parent!==''?substr(hash('sha256',$parent.'|repair'),0,32):bin2hex(random_bytes(16));$request=ZPT2_API::request($job,$items,$id,$parent);
        $existing=ZPT2_Store::get('request_'.$id);if($existing){if(($existing['hash']??'')!==ZPT2_API::hash($request))throw new RuntimeException('request_changed');return$id;}
        ZPT2_Store::put('request_'.$id,['id'=>$id,'job'=>$job['id'],'parent'=>$parent,'items'=>$items,'request'=>$request,'hash'=>ZPT2_API::hash($request),'key_fingerprint'=>$job['key_fingerprint'],'state'=>'virgin','created_at'=>time()]);return$id;
    }
    private static function save_job(array $job): void {
        $job['updated_at']=time();ZPT2_Store::put('job_'.$job['id'],$job);$idx=ZPT2_Store::get('jobs');$idx[$job['id']]=['source'=>$job['source'],'state'=>$job['state'],'updated_at'=>$job['updated_at']];ZPT2_Store::put('jobs',$idx);
        $i=ZPT2_Store::get('inventory');if(isset($i[$job['source']])){$i[$job['source']]['state']=$job['state'];$i[$job['source']]['job']=$job['id'];$i[$job['source']]['error']=$job['error']??'';ZPT2_Store::put('inventory',$i);}
    }
    /** Complete, already paid evidence only. This method never calls the provider or changes state. */
    public static function saved_context(string $id,int $actor): array {
        if(!preg_match('/^[a-f0-9]{32}$/D',$id)||!self::can($actor))throw new RuntimeException('forbidden');
        $job=ZPT2_Store::get('job_'.$id);
        if(!$job||($job['id']??'')!==$id||!self::can($actor,(int)($job['post_id']??0))||($job['state']??'')!=='attention')throw new RuntimeException('saved_recheck_unavailable');
        $publicationRetry=($job['error']??'')==='source_noindex'&&($job['publication_only']??false)===true&&isset($job['commit']['target'])&&!array_diff_key($job['snapshot']['units'],$job['effective']);
        $publication500=($job['error']??'')==='source_unavailable'&&($job['serving_diagnostic']['error']??'')==='source_unavailable'&&($job['serving_diagnostic']['fetch']['http']??null)===500&&($job['serving_diagnostic']['fetch']['lang']??'')==='pl'&&($job['serving_diagnostic']['fetch']['path']??null)===$job['source']&&isset($job['commit']['target'])&&array_key_exists('previous',$job['commit'])&&!array_diff_key($job['snapshot']['units'],$job['effective'])&&!array_diff_key($job['effective'],$job['snapshot']['units'])&&!array_diff($job['requests'],$job['processed']??[]);
        if($publication500){$published=ZPT2_Store::get('published_'.$id);$copy=$published;unset($copy['digest']);$publication500=($published['id']??null)===$id&&($published['source']??null)===$job['source']&&($published['snapshot']??null)===$job['snapshot']&&($published['effective']??null)===$job['effective']&&($published['digest']??null)===ZPT2_Store::hash($copy)&&$job['commit']['target']===['id'=>$id,'digest'=>$published['digest']]&&ZPT2_Store::get('active_'.hash('sha256',$job['source']),null)===$job['commit']['previous'];}
        if(($job['error']??'')!=='repair_limit'&&!$publicationRetry&&!$publication500)throw new RuntimeException('saved_recheck_unavailable');
        $d=ZPT2_Source::descriptor($job['post_id']);if($d['source']!==$job['source']||$d['en']!==$job['snapshot']['en'])throw new RuntimeException('source_changed');if($publication500&&ZPT2_Source::native($job['post_id'])!==$job['snapshot']['native'])throw new RuntimeException('source_changed');
        if(hash('sha256',ZPT2_API::key())!==$job['key_fingerprint']||ZPT2_Store::hash($job['policy'])!==ZPT2_Store::hash(ZPT2_API::policy()))throw new RuntimeException('request_changed');
        $budget=ZPT2_Store::budget();$requests=[];$receipts=[];$proofs=[];$latest=[];
        if(!$job['requests']||count($job['requests'])>160)throw new RuntimeException('response_identity');
        foreach($job['requests']as$rid){
            $r=ZPT2_Store::get('request_'.$rid);$receipt=ZPT2_Store::get('receipt_'.$rid);$terminal=$r['terminal']??[];$request=$r['request']??[];
            if(($r['id']??null)!==$rid||($r['job']??null)!==$id||($r['key_fingerprint']??null)!==$job['key_fingerprint']||!in_array($r['state']??'',['terminal','consumed'],true)||!is_array($request)||ZPT2_API::hash($request)!==($r['hash']??null))throw new RuntimeException('response_identity');
            foreach(['zpt2_job'=>$id,'zpt2_request'=>$rid,'zpt2_parent'=>$r['parent'],'zpt2_policy'=>ZPT2_Store::hash($job['policy']),'zpt2_hash'=>$r['hash']]as$key=>$value)if(($request['metadata'][$key]??null)!==$value)throw new RuntimeException('response_identity');
            if($r['parent']!==''&&(!isset($requests[$r['parent']])||$rid!==substr(hash('sha256',$r['parent'].'|repair'),0,32)))throw new RuntimeException('response_identity');
            $input=is_string($request['input']??null)?json_decode($request['input'],true):null;
            if(!is_array($r['items']??null)||!$r['items']||($input['items']??null)!==array_values($r['items'])||($input['context_only']??null)!==['title'=>$job['snapshot']['title'],'source'=>$job['source']])throw new RuntimeException('request_changed');
            foreach($r['items']as$uid=>$unit){$base=$unit;unset($base['repair_reason'],$base['previous_en']);if(($job['snapshot']['units'][$uid]??null)!==$base)throw new RuntimeException('request_changed');}
            if(($receipt['job']??null)!==$id||($receipt['id']??null)!==$rid||($receipt['request_hash']??null)!==$r['hash']||($receipt['key_fingerprint']??null)!==$job['key_fingerprint']||!is_string($receipt['response_id']??null)||!preg_match('/^resp_[A-Za-z0-9_-]{1,180}$/D',$receipt['response_id'])||($r['response_id']??null)!==$receipt['response_id']||($terminal['receipt']??null)!==$receipt)throw new RuntimeException('response_identity');
            if(($terminal['status']??null)!=='completed'||($terminal['waiting']??null)!==false||!empty($terminal['error'])||!is_array($terminal['result']??null)||array_diff_key($terminal['result'],$r['items'])||array_diff_key($r['items'],$terminal['result']))throw new RuntimeException('saved_result_incomplete');
            $settled=$budget['zpt2_settled'][$rid]??null;if(!is_array($terminal['usage']??null)||$settled!==['hash'=>$r['hash'],'usage'=>$terminal['usage']]||isset($budget['reservations'][$rid]))throw new RuntimeException('budget_invalid');
            foreach($terminal['result']as$uid=>$en)$latest[$uid]=['en'=>$en,'request_id'=>$rid,'response_id'=>$receipt['response_id']];
            $requests[$rid]=$r;$receipts[$rid]=$receipt;$proofs[$rid]=$settled;
        }
        return['job'=>$job,'latest'=>$latest,'fingerprint'=>ZPT2_Store::hash(['job'=>$job,'requests'=>$requests,'receipts'=>$receipts,'settled'=>$proofs])];
    }
    /** Revalidate unchanged saved terminals; only publication is re-armed, never another repair or API request. */
    public static function recheck_saved(string $id,string $fingerprint,int $actor): void {
        ZPT2_Store::lock(static function()use($id,$fingerprint,$actor){
            $context=self::saved_context($id,$actor);if(!preg_match('/^[a-f0-9]{64}$/D',$fingerprint)||!hash_equals($context['fingerprint'],$fingerprint))throw new RuntimeException('queue_changed');$job=$context['job'];
            $fresh=ZPT2_Source::capture($job['post_id']);if($fresh['hash']!==$job['snapshot']['hash']||$fresh['native']!==$job['snapshot']['native'])throw new RuntimeException('source_changed');
            $missing=array_diff_key($job['snapshot']['units'],$job['effective']);$candidate=$job['effective'];$responses=[];
            foreach($missing as$uid=>$unit){if(!isset($context['latest'][$uid]))throw new RuntimeException('saved_result_incomplete');$candidate[$uid]=$context['latest'][$uid]['en'];$responses[$uid]=$context['latest'][$uid]['response_id'];}
            $v=ZPT2_Source::validate($job['snapshot']['units'],$candidate,$job['snapshot']);if($v['invalid']||$v['extra'])throw new RuntimeException('saved_result_still_invalid');
            if(self::saved_context($id,$actor)['fingerprint']!==$fingerprint)throw new RuntimeException('queue_changed');
            $job['effective']=$v['valid'];$job['processed']=array_values(array_unique(array_merge($job['processed']??[],$job['requests'])));$job['state']='translating';$job['error']='';$job['publication_only']=true;
            $audit=$job['saved_rechecks']??[];$audit[]=['at'=>time(),'actor'=>$actor,'evidence'=>$fingerprint,'accepted_units'=>array_keys($missing),'responses'=>$responses];$job['saved_rechecks']=array_slice($audit,-5);self::save_job($job);
        });self::schedule();
    }
    public static function worker(): void {
        $start=microtime(true);$idx=ZPT2_Store::get('jobs');$ids=array_keys($idx);$cursor=(int)ZPT2_Store::get('cursor',0);$n=count($ids);$steps=0;$waiting=[];
        for($offset=0;$offset<$n*3&&$steps<6&&microtime(true)-$start<35;$offset++){
            $pos=($cursor+$offset)%$n;$id=$ids[$pos];if(isset($waiting[$id]))continue;if(!in_array($idx[$id]['state'],['queued','translating','repairing','checking','deferred'],true))continue;
            try{ZPT2_Store::lock(static function()use($id){self::step($id);});$steps++;$now=ZPT2_Store::get('job_'.$id);$idx[$id]['state']=$now['state'];foreach($now['requests']as$rid)if((ZPT2_Store::get('request_'.$rid)['state']??'')==='waiting'){$waiting[$id]=true;break;}ZPT2_Store::put('cursor',($pos+1)%max(1,$n));}
            catch(Throwable$e){if(self::error($e)==='busy')break;ZPT2_Store::put('worker_error',self::error($e));}
        }
        ZPT2_Store::put('worker_last',time());self::schedule();self::auto_enqueue();
    }
    private static function step(string $id): void {
        $job=ZPT2_Store::get('job_'.$id);if(!$job)return;
        try{
            if(!self::can($job['actor'],$job['post_id']))throw new RuntimeException('forbidden');
            ZPT2_Source::descriptor($job['post_id']);
            if($job['state']==='checking'){self::check_public($job);self::save_job($job);return;}
            if(!empty($job['publication_only'])){self::publish($job);self::save_job($job);return;}
            if($job['state']==='deferred'&&($job['retry_at']??0)>time())return;
            foreach($job['requests']as$rid){
                $r=ZPT2_Store::get('request_'.$rid);if(($r['state']??'')==='consumed')continue;
                if(in_array($rid,$job['processed']??[],true)){$r['state']='consumed';ZPT2_Store::put('request_'.$rid,$r);continue;}
                $receipt=ZPT2_Store::get('receipt_'.$rid);
                if($receipt){if(($receipt['request_hash']??'')!==$r['hash'])throw new RuntimeException('response_identity');$r['response_id']=$receipt['response_id'];$r['received_at']=$receipt['received_at'];}
                if(!isset($r['terminal'])){
                    if(($r['state']??'')==='virgin'){
                        if(!ZPT2_Store::can_launch())return;
                        if(ZPT2_Source::native($job['post_id'])!==$job['snapshot']['native'])throw new RuntimeException('source_changed');
                        ZPT2_Store::chars($r);ZPT2_Store::reserve($r);if(!ZPT2_Store::legacy_launch())return;$r['state']='requested';$r['requested_at']=time();ZPT2_Store::put('request_'.$rid,$r);
                        $flight=$r;$flight['state']='authorized';$out=ZPT2_API::step($flight);
                    }else{
                        if(empty($r['response_id']))throw new RuntimeException('unknown_paid_attempt');
                        if(($r['poll_count']??0)>1000)throw new RuntimeException('poll_limit');
                        $r['poll_count']=($r['poll_count']??0)+1;ZPT2_Store::put('request_'.$rid,$r);$out=ZPT2_API::step($r);
                    }
                    if(!empty($out['receipt'])){$r['response_id']=$out['receipt']['response_id'];$r['received_at']=$out['receipt']['received_at'];}
                    $r['last_diagnostic']=$out['diagnostic'];
                    if($out['waiting']){$r['state']='waiting';ZPT2_Store::put('request_'.$rid,$r);$job['state']=$r['parent']?'repairing':'translating';$job['error']='';self::save_job($job);return;}
                    $r['terminal']=$out;$r['terminal_at']=time();$r['state']='terminal';ZPT2_Store::put('request_'.$rid,$r);
                }
                $out=$r['terminal'];ZPT2_Store::settle($r,$out['usage']??null);
                if(!empty($out['error']))throw new RuntimeException($out['error']);
                $v=ZPT2_Source::validate($r['items'],$out['result']??[],$job['snapshot']);if($v['extra'])throw new RuntimeException('extra_output_ids');
                foreach($v['valid']as$uid=>$en)$job['effective'][$uid]=$en;
                $repair=[];
                foreach($v['invalid']as$uid=>$reason){$attempt=$job['repair_attempts'][$uid]??0;if($attempt>=2)throw new RuntimeException('repair_limit');$job['repair_attempts'][$uid]=$attempt+1;$u=$job['snapshot']['units'][$uid];$u['repair_reason']=$reason;$u['previous_en']=is_string($out['result'][$uid]??null)?$out['result'][$uid]:'';$repair[$uid]=$u;}
                if($repair){$job['requests'][]=self::make_request($job,$repair,$rid);$job['state']='repairing';}
                else$job['state']='translating';
                // Persist derived merge before consuming the immutable terminal. A crash safely repeats this merge.
                $job['error']='';$job['processed'][]=$rid;self::save_job($job);$r['state']='consumed';ZPT2_Store::put('request_'.$rid,$r);return;
            }
            self::publish($job);self::save_job($job);
        }catch(Throwable$e){
            $code=self::error($e);$job['error']=$code;
            if($code==='daily_chars_limit'){$job['state']='deferred';$job['retry_at']=(new DateTimeImmutable('tomorrow',new DateTimeZone('Europe/Warsaw')))->getTimestamp()+5;}
            else$job['state']=$code==='unknown_paid_attempt'?'unknown':'attention';
            self::save_job($job);
        }
    }
    public static function active(string $source): array {
        $ptr=ZPT2_Store::get('active_'.hash('sha256',$source),null);if(!is_array($ptr)||!is_string($ptr['id']??null))return[];
        $map=ZPT2_Store::get('published_'.$ptr['id']);if(($map['source']??null)!==$source||($map['digest']??'')!==($ptr['digest']??''))return[];
        $copy=$map;unset($copy['digest']);return ZPT2_Store::hash($copy)===$map['digest']?$map:[];
    }
    public static function legacy_reference(string $source): array {
        $ref=ZPT2_Store::get('legacy_'.hash('sha256',$source));$version=$ref['revision']??'';if(!is_string($version)||!preg_match('/^[a-f0-9]{32}$/D',$version))return[];
        $page=ZPT2_Store::legacy(ZPT2_Store::legacy_prefix().'p_'.hash('sha256',$version.'|'.$source));
        if(($page['source']??'')!==$source||($page['revision']??'')!==$version||!in_array($page['state']??'',['verified','checking_serving'],true)||!is_array($page['map']??null))return[];
        $seal=hash('sha256',(string)wp_json_encode(['source_hash'=>$page['source_hash']??null,'native_hash'=>$page['native_hash']??null,'manual_hash'=>$page['manual_hash']??null,'map'=>$page['map']]));
        if(($page['staged_hash']??'')!==$seal)return[];
        $map=class_exists('ZP_EN_Semantic_Repair_20261012')?ZP_EN_Semantic_Repair_20261012::map_view($page):$page['map'];if(ZPT2_Store::hash($map)!==($ref['map_hash']??''))return[];
        return ['map'=>$map,'snapshot'=>$ref['snapshot'],'revision'=>$version];
    }
    public static function approved(string $source,string $preview,array $legacy): array {
        try{
            if($preview!=='')return$legacy;$map=self::active($source);if(!$map){if(!empty($legacy['map']))return$legacy;$ref=self::legacy_reference($source);if($ref){$d=ZPT2_Source::descriptor($ref['snapshot']['post_id']);if($d['source']===$source&&$d['en']===$ref['snapshot']['en']&&ZPT2_Source::native($d['post_id'])===$ref['snapshot']['native'])return ['source'=>$source,'revision'=>$ref['revision'],'map'=>$ref['map']];}return$legacy;}
            if(!self::public_pointer($source,$map))return$legacy;$d=ZPT2_Source::descriptor($map['snapshot']['post_id']);if($d['source']!==$source||$d['en']!==$map['snapshot']['en'])return$legacy;
            // Exact source keys make an old published map a safe fallback during an edit; no timestamp blanket invalidation.
            return ['source'=>$source,'revision'=>'v2-'.$map['id'],'map'=>$map['map']];
        }catch(Throwable$e){return$legacy;}
    }
    private static function flush(): void {
        \ZPL\Router::reset();\ZPL\Dict::flush();ZP_Translator_Approved_Overlay_20261011::flush();
        if(class_exists('ZP_HTML_Cache_20261009'))ZP_HTML_Cache_20261009::queue_purge();
        do_action('zpt2_published_cache_purge');
    }
    private static function publish(array &$job): void {
        $snap=ZPT2_Source::capture($job['post_id']);if($snap['hash']!==$job['snapshot']['hash'])throw new RuntimeException('source_changed');
        $v=ZPT2_Source::validate($job['snapshot']['units'],$job['effective'],$job['snapshot']);if($v['invalid']||$v['extra'])throw new RuntimeException('translation_incomplete');
        $map=$job['base_map'];foreach($job['snapshot']['units']as$id=>$u)$map[$u['pl']]=$job['effective'][$id];ksort($map,SORT_STRING);
        $published=['id'=>$job['id'],'source'=>$job['source'],'snapshot'=>$job['snapshot'],'effective'=>$job['effective'],'map'=>$map,'created_at'=>time()];$published['digest']=ZPT2_Store::hash($published);
        $key='active_'.hash('sha256',$job['source']);$previous=ZPT2_Store::get($key,null);$target=['id'=>$job['id'],'digest'=>$published['digest']];
        ZPT2_Store::put('published_'.$job['id'],$published);$job['commit']=['previous'=>$previous,'target'=>$target,'prepared_at'=>time()];$job['state']='checking';self::save_job($job);
        ZPT2_Store::cas($key,$previous,$target);self::flush();
    }
    private static function check_public(array &$job): void {
        $key='active_'.hash('sha256',$job['source']);$target=$job['commit']['target'];$actual=ZPT2_Store::get($key,null);
        if($actual===$job['commit']['previous']){ZPT2_Store::cas($key,$actual,$target);self::flush();}
        elseif($actual!==$target)throw new RuntimeException('pointer_changed');
        try{
            $p=self::active($job['source']);$current=ZPT2_Source::capture($job['post_id'],self::probe_token($p));if($current['hash']!==$job['snapshot']['hash'])throw new RuntimeException('source_changed');
            $p=self::active($job['source']);$html=ZPT2_Source::fetch($job['snapshot']['en'],'en',self::probe_token($p));$expected=hash('sha256',$job['source'].'|v2-'.$job['id'].'|'.wp_json_encode($p['map'],JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
            $checks=['marker'=>strpos($html,$expected)!==false,'lang'=>preg_match('/<html\b[^>]*\blang=["\']en(?:-[A-Za-z]+)?["\']/i',$html)===1,'h1'=>preg_match('/<h1\b/i',$html)===1];
            $langs=[];preg_match_all('/<link\b[^>]*>/i',$html,$tags);foreach($tags[0]as$tag){$a=\ZPL\Html::attrs($tag);if(strtolower($a['rel']??'')==='alternate')$langs[$a['hreflang']??'']=$a['href']??'';}
            $checks['hreflang']=($langs['pl']??$langs['pl-PL']??'')===home_url($job['source'])&&($langs['en']??$langs['en-GB']??'')===home_url($job['snapshot']['en']);
            $seen=[];foreach(ZPT2_Source::extract($html,$job['post_id'])as$u)$seen[$u['pl']]=true;$samples=0;$manual=(array)get_option('zpl_overrides',[]);
            foreach($job['snapshot']['units']as$id=>$u){$en=$job['effective'][$id];if($samples>=5)break;if(!in_array($u['kind'],['text','title','meta'],true)||isset($manual[$u['pl']])||strlen($en)<30||preg_match('/[0-9{}]|PLN|zł/u',$en))continue;$checks['sample_'.$samples++]=isset($seen[$en]);}
            $checks['sample_present']=$samples>0;
            $rest=wp_remote_get(add_query_arg(['src'=>$job['source'],'lang'=>'en'],rest_url('zpl/v1/approved-page')),['timeout'=>8,'redirection'=>0,'sslverify'=>true,'limit_response_size'=>2500000]);
            $data=is_wp_error($rest)?null:json_decode(wp_remote_retrieve_body($rest),true);$checks['rest']=!is_wp_error($rest)&&wp_remote_retrieve_response_code($rest)===200&&($data['revision']??null)===$expected&&($data['map']??null)===$p['map'];
            if(ZPT2_Source::native($job['post_id'])!==$current['native'])throw new RuntimeException('source_changed');
            if(in_array(false,$checks,true))throw new RuntimeException('serving_unverified');
            $job['serving']=['checks'=>$checks,'digest'=>$expected,'at'=>time()];$job['state']='published';$job['error']='';$job['published_at']=time();ZPT2_Store::put('baseline_'.hash('sha256',$job['source']),$job['snapshot']);
            $samples=ZPT2_Store::get('timings');$samples[]=max(1,time()-$job['created_at']);ZPT2_Store::put('timings',array_slice($samples,-30));
        }catch(Throwable$e){
            $job['serving_diagnostic']=['at'=>time(),'error'=>self::error($e),'fetch'=>ZPT2_Source::$last_fetch];
            // Failed serving rolls back only this prepared pointer. Historical maps and receipts never change.
            if(ZPT2_Store::get($key,null)===$target){ZPT2_Store::cas($key,$target,$job['commit']['previous']);self::flush();}
            throw $e;
        }
    }
    public static function public_pointer(string $source,array $p,?array &$facts=null): bool {
        $ready=self::ready();if($facts!==null)$facts=['ready'=>$ready,'gate'=>'dependency_or_path'];
        if(!$ready||!$p||preg_match('~^/(?:en|oferty|offers|briefy|briefs|dziekujemy)(?:/|$)~i',$source)||\ZPL\Router::never($source)||\ZPL\Router::is_excluded($source))return false;
        $post=get_post($p['snapshot']['post_id']);if($facts!==null)$facts['gate']='post';
        if(!$post||$post->post_status!=='publish'||$post->post_password!==''||!in_array($post->post_type,['post','page'],true))return false;
        if($post->post_type==='page'&&!is_object($GLOBALS['wp_rewrite']??null)){
            // Early Router runs before WP_Rewrite exists; use only the sealed, unchanged native page identity.
            if($facts!==null)$facts['gate']='early_page_identity';$sealed=$p;unset($sealed['digest']);
            if(($p['source']??null)!==$source||($p['snapshot']['source']??null)!==$source||($p['snapshot']['post_id']??null)!==$post->ID||($p['digest']??null)!==ZPT2_Store::hash($sealed)||($p['snapshot']['native']??null)!==ZPT2_Source::native($post->ID))return false;
            $front=get_option('show_on_front')==='page'&&(int)get_option('page_on_front')===$post->ID;$uri=get_page_uri($post);
            if($source==='/'?!$front:($front||!is_string($uri)||'/'.trim($uri,'/').'/'!==$source))return false;
            if($facts!==null)$facts+=['post_id'=>$post->ID,'post_type'=>$post->post_type,'page_uri'=>$uri,'early_page_identity'=>true];
        }else{
            $permalink=get_permalink($post->ID);if($facts!==null)$facts+=['post_id'=>$post->ID,'post_type'=>$post->post_type,'permalink'=>$permalink,'expected_permalink'=>home_url($source)];
            if($permalink!==home_url($source)&&$permalink.'/'!==home_url($source)){if($facts!==null)$facts['gate']='permalink';return false;} // Pre-init WordPress may omit only the final slash.
        }
        if($facts!==null)$facts['gate']='private_path';if(function_exists('zp_seo_plan_is_private_path')&&zp_seo_plan_is_private_path($source))return false;
        if($facts!==null)$facts['gate']='private_shortcode';if(preg_match('/\[(?:zp_offer|zp_brief|zaprojektowani_offer|zaprojektowani_brief)\b/i',$post->post_content))return false;
        if($facts!==null)$facts['gate']='robots';$robots=get_post_meta($post->ID,'rank_math_robots',true);if($robots===''||$robots===false)$robots=[];if(is_string($robots))$robots=preg_split('/[\s,]+/',strtolower($robots));if(!is_array($robots))return false;
        foreach($robots as$r)if(!is_string($r)||in_array(strtolower($r),['noindex','noarchive'],true))return false;if($facts!==null)$facts['gate']='pass';return true;
    }
    public static function routes(array $routes): array {
        $observe=is_string($_SERVER['HTTP_X_ZPT2_PROBE']??null)&&preg_match('/^[a-f0-9]{64}$/D',$_SERVER['HTTP_X_ZPT2_PROBE']);$before=[];
        if($observe){self::$route_probe['calls']++;foreach(['ZP_EN_Rewrite_20261011','ZP_Translator_Approved_Overlay_20261011','ZPL\\Html','ZPL\\Page','ZPL\\Router','ZPTE\\Settings']as$c)$before[$c]=class_exists($c,false);}
        $inventory=ZPT2_Store::get('inventory');$loops=0;$rows=[];
        foreach($inventory as$source=>$r){$loops++;$p=self::active($source);$facts=$observe?[]:null;$public=self::public_pointer($source,$p,$facts);
            if($observe&&$p&&hash_equals(self::probe_token($p),$_SERVER['HTTP_X_ZPT2_PROBE']))$rows[]=['source'=>$source,'id'=>$p['id'],'digest'=>$p['digest'],'public_pointer'=>$public,'guard'=>$facts,'before_route'=>$routes[$source]??null,'target'=>$p['snapshot']['en']];
            if(!$public)continue;$en=$p['snapshot']['en'];if(!isset($routes[$source])&&!in_array($en,$routes,true))$routes[$source]=$en;}
        if($observe){$after=[];foreach(array_keys($before)as$c)$after[$c]=class_exists($c,false);foreach($rows as&$row)$row['after_route']=$routes[$row['source']]??null;unset($row);$observations=self::$route_probe['observations'];$observations[]=['classes_before'=>$before,'classes_after'=>$after,'plugins_loaded'=>function_exists('did_action')?did_action('plugins_loaded'):null,'init'=>function_exists('did_action')?did_action('init'):null,'inventory_count'=>count($inventory),'loops'=>$loops,'routes_count'=>count($routes),'active'=>array_slice($rows,0,2)];self::$route_probe['observations']=array_slice($observations,-3);}
        return$routes;
    }
    public static function has_page($has,string $source): bool {return(bool)$has||self::public_pointer($source,self::active($source));}
    public static function sitemap($paths): array {$paths=is_array($paths)?$paths:[];foreach(ZPT2_Store::get('inventory')as$s=>$r)if(($r['state']??'')==='published'&&self::public_pointer($s,self::active($s)))$paths[]=$s;return array_values(array_unique($paths));}
    public static function status(): array {
        $budget=null;try{$b=ZPT2_Store::budget(ZPT2_Store::get('migration'));$budget=['settled'=>$b['settled'],'reserved'=>array_sum($b['reservations']),'limit'=>$b['limit']];}catch(Throwable$e){}
        $rows=[];foreach(ZPT2_Store::get('inventory')as$row){$j=!empty($row['job'])?ZPT2_Store::get('job_'.$row['job']):[];$row['done']=$j?count($j['effective']):null;$row['total']=$j?count($j['snapshot']['units']):null;$row['error']=$j['error']??($row['error']??'');$rows[]=$row;}
        $t=ZPT2_Store::get('timings');sort($t);$eta=count($t)>=3?(int)$t[(int)floor(count($t)/2)]:null;
        return['policy'=>ZPT2_API::policy(),'rows'=>$rows,'budget'=>$budget,'daily_chars'=>ZPT2_Store::chars_status(),'scan'=>ZPT2_Store::get('scan'),'daily_enabled'=>!empty(ZPT2_Store::get('config')['daily_enabled']),'auto_enabled'=>!empty(ZPT2_Store::get('config')['auto_enabled']),'next_scan'=>wp_next_scheduled(self::DAILY)?:null,'worker_last'=>ZPT2_Store::get('worker_last',null),'eta_seconds'=>$eta,'setup_error'=>ZPT2_Store::get('setup_error','')];
    }
}
// All active plugins finish loading before this hook is evaluated; no dependency reads here.
ZPT2_Engine::public_hooks();
