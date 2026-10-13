<?php
/** Explicit, reversible photo-only adapter for the already published owned article5788. */
if (!defined('ABSPATH')) { exit; }

final class ZP_Published_Prepared_Photo_Refresh_20261010 {
    public const POST_ID = 5788;
    public const DATA = 'zps_published_prepared_photos_20261010_data';
    public const LEDGER = 'zps_published_prepared_photos_20261010_ledger';
    public const WORKER = 'zp_published_prepared_photos_worker_20261010';
    public const WATCHDOG = 'zp_published_prepared_photos_watchdog_20261010';
    public const PREFIX = '_zp_published_prepared_photos_20261010_';
    private const PHOTO_META = ['_elementor_data','_thumbnail_id','rank_math_facebook_image','rank_math_facebook_image_id','_zp_prepared_20261010_ready_hash'];
    private const HASH_META = ['_elementor_edit_mode','_elementor_template_type','_elementor_version','_elementor_pro_version','_elementor_page_settings','_wp_page_template','_elementor_data','_thumbnail_id','rank_math_title','rank_math_description','rank_math_focus_keyword','rank_math_canonical_url','rank_math_robots','rank_math_schema_Article','_zp_prepared_20261010_faq'];
    private const MODELS = ['gpt-image-2.5-sunburst','gpt-image-2.5-flare'];

    public static function allowed(): bool {
        return class_exists('ZP_Content_Preparation_20261010') && class_exists('ZP_Prepared_Photo_Refresh_20261010') && class_exists('ZP_Article_Photo_Refresh_20261010')
            && current_user_can('manage_options') && current_user_can('edit_posts') && current_user_can('upload_files') && current_user_can('unfiltered_html') && current_user_can('edit_post', self::POST_ID);
    }
    public static function guard(string $action): void {
        if (!self::allowed() || ($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST' || !is_string($_POST['_wpnonce'] ?? null) || !wp_verify_nonce(wp_unslash($_POST['_wpnonce']), $action)) { wp_die('Brak uprawnień lub prawidłowego potwierdzenia.', '', ['response'=>403]); }
    }
    /** These private options are autoload=false; evict only their runtime entries before a guard/read-back. */
    private static function read_option(string $name,$default=[]){wp_cache_delete($name,'options');wp_cache_delete('notoptions','options');return get_option($name,$default);}
    private static function refresh_post(): void {wp_cache_delete(self::POST_ID,'posts');wp_cache_delete(self::POST_ID,'post_meta');}
    private static function save(string $name, array $data): void {
        if (strlen(wp_json_encode($data)) > 16000000) { throw new RuntimeException('storage_limit'); }
        update_option($name, $data, false);
        if (self::read_option($name, null) !== $data) { throw new RuntimeException('storage_failed'); }
    }
    public static function data(): array { $d=self::read_option(self::DATA, []); return is_array($d) ? $d : ['jobs'=>[]]; }
    private static function ledger(): array {
        $v=self::read_option(self::LEDGER,[]); if(!is_array($v)||count($v)>30)throw new RuntimeException('ledger_invalid');
        foreach($v as $key=>$e){if(!is_array($e)||!is_string($e['job']??null)||!preg_match('/^[A-Za-z0-9_-]{1,80}$/D',$e['job'])||!in_array($e['role']??null,['cover','inline'],true)||!is_int($e['attempt']??null)||$e['attempt']<1||$e['attempt']>3||$key!==$e['job'].'|'.$e['role'].'|'.$e['attempt']||!is_int($e['actor']??null)||$e['actor']<1||!is_int($e['at']??null)||$e['at']<1||$e['at']>time()+300||!in_array($e['state']??null,['requested','stored'],true))throw new RuntimeException('ledger_invalid');
            foreach(['source_sha','request_sha','connection_fingerprint']as$f)if(!is_string($e[$f]??null)||!preg_match('/^[a-f0-9]{64}$/D',$e[$f]))throw new RuntimeException('ledger_invalid');
            if(($e['state']==='stored'&&!isset($e['attachment_id']))||(isset($e['attachment_id'])&&(!is_int($e['attachment_id'])||$e['attachment_id']<1)))throw new RuntimeException('ledger_invalid');
        }$groups=[];foreach($v as$e)$groups[$e['job'].'|'.$e['role']][]=$e['attempt'];foreach($groups as$numbers){sort($numbers);if($numbers!==range(1,count($numbers)))throw new RuntimeException('ledger_invalid');}return$v;
    }
    private static function sha($data): string { return hash('sha256', wp_json_encode($data)); }
    private static function meta_write(int $id, string $key, $value): void {
        update_post_meta($id, $key, wp_slash($value));wp_cache_delete($id,'post_meta'); $saved=get_post_meta($id, $key, true);
        if (is_scalar($value) ? (string)$saved !== (string)$value : $saved !== $value) { throw new RuntimeException('metadata_failed'); }
    }
    private static function actor(array $job): void {
        $actor=(int)($job['actor']??0);wp_cache_delete($actor,'users');wp_cache_delete($actor,'user_meta');$user=get_userdata($actor);
        foreach (['manage_options','edit_posts','upload_files','unfiltered_html'] as $cap) { if (!$user || !user_can($user,$cap)) { throw new RuntimeException('actor_permissions'); } }
        if (!user_can($user,'edit_post',self::POST_ID)) { throw new RuntimeException('actor_permissions'); }
    }
    private static function key(): string {
        $v=defined('ZP_CONTENT_OPENAI_API_KEY') ? constant('ZP_CONTENT_OPENAI_API_KEY') : (self::read_option(ZP_Content_Preparation_20261010::KEY,'') ?: getenv('OPENAI_API_KEY'));
        return is_string($v) && preg_match('/^[A-Za-z0-9_.:-]{10,1000}$/D',$v) ? $v : '';
    }
    private static function config(): array {
        $key=self::key();self::read_option(ZP_Content_Preparation_20261010::SETTINGS,[]); $c=ZP_Content_Preparation_20261010::settings(); $proof=self::read_option(ZP_Content_Preparation_20261010::MODELS,[]);
        if ($key==='' || !is_array($proof) || ($proof['fingerprint']??null)!==hash('sha256',$key) || !is_int($proof['checked_at']??null) || $proof['checked_at']<time()-604800 || !in_array($c['image_model'],self::MODELS,true) || !in_array($c['image_model'],$proof['ids']??[],true)) { throw new RuntimeException('verify_models'); }
        return ['model'=>$c['image_model'],'quality'=>$c['quality'],'fingerprint'=>hash('sha256',$key)];
    }
    private static function provider(array $job): void { $c=self::config(); if (($job['config']??null)!==$c) { throw new RuntimeException('connection_changed'); } }
    private static function next(): void {
        if (!wp_next_scheduled(self::WORKER)) { $r=wp_schedule_single_event(time()+5,self::WORKER,[],true); if (is_wp_error($r)||$r===false) { throw new RuntimeException('cron_failed'); } }
        if (!wp_next_scheduled(self::WATCHDOG)) { $r=wp_schedule_event(time()+300,'zp_prep_5m',self::WATCHDOG,[],true); if (is_wp_error($r)||$r===false) { throw new RuntimeException('cron_failed'); } }
    }
    private static function cleanup(): void {
        foreach(self::data()['jobs']??[]as$j){if(in_array($j['state']??'',['queued','running','ready','applying'],true))return;}
        wp_clear_scheduled_hook(self::WATCHDOG);wp_clear_scheduled_hook(self::WORKER);
    }
    private static function fields(int $id): array {
        $p=get_post($id); if (!$p) { throw new RuntimeException('post_changed'); } $out=[];
        foreach (['ID','post_type','post_status','post_title','post_name','post_excerpt','post_author','post_password','post_date','post_date_gmt','post_modified','post_modified_gmt'] as $k) { $out[$k]=$p->$k??null; }
        $out['categories']=wp_get_post_categories($id); sort($out['categories']); $out['future_cron']=wp_next_scheduled('publish_future_post',[$id]); return $out;
    }
    private static function other_meta(int $id): array {
        $all=get_post_meta($id); if (!is_array($all)) { throw new RuntimeException('post_changed'); } $out=[];
        foreach ($all as $k=>$v) { if (!is_string($k) || str_starts_with($k,self::PREFIX) || in_array($k,array_merge(self::PHOTO_META,['_elementor_css','_elementor_element_cache','_elementor_page_assets','_edit_lock','_edit_last','_zp_post_reads','_zp_post_views']),true)) { continue; } $out[$k]=$v; }
        ksort($out); return $out;
    }
    private static function core_job(): array {
        $core=self::read_option(ZP_Content_Preparation_20261010::DATA,[]);$jobs=is_array($core)?($core['jobs']??[]):[]; $hits=[];
        foreach ($jobs as $j) { if (is_array($j) && ($j['post_id']??null)===self::POST_ID) { $hits[]=$j; } }
        if (count($hits)!==1) { throw new RuntimeException('ownership_changed'); } return $hits[0];
    }
    private static function publication($p, array $job): void {
        $a=$job['publish_attempt']??null; $url=get_permalink(self::POST_ID); $u=is_string($url)?parse_url($url):false; $home=parse_url(home_url('/'));
        if (!$p || $p->ID!==self::POST_ID || $p->post_type!=='post' || $p->post_status!=='publish' || $p->post_password!=='' || ($job['state']??'')!=='published' || !ZP_Content_Preparation_20261010::eligible_volume($job['row']??[]) || !is_array($a)
            || !is_string($a['hash']??null) || !preg_match('/^[a-f0-9]{64}$/D',$a['hash']) || !is_string($a['id']??null) || $a['id']==='' || $p->post_date!==($a['local']??null) || $p->post_date_gmt!==($a['gmt']??null) || $p->post_name!==($a['slug']??null)
            || wp_next_scheduled('publish_future_post',[self::POST_ID])!==false || !is_array($u) || ($u['scheme']??'')!=='https' || ($u['host']??'')!==($home['host']??'') || preg_match('~^/en(?:/|$)~',$u['path']??'') || array_intersect(['query','fragment','user','pass','port'],array_keys($u))
            || $url!==($a['canonical']??null) || get_post_meta(self::POST_ID,'rank_math_canonical_url',true)!==$url || in_array('noindex',(array)get_post_meta(self::POST_ID,'rank_math_robots',true),true)) { throw new RuntimeException('publication_changed'); }
        if (get_post_meta(self::POST_ID,ZP_Content_Preparation_20261010::PREFIX.'job',true)!==($job['id']??null) || get_post_meta(self::POST_ID,ZP_Content_Preparation_20261010::PREFIX.'batch',true)!==($job['batch']??null)) { throw new RuntimeException('ownership_changed'); }
    }
    private static function no_other_photos(array $job): void {
        self::read_option(ZP_Prepared_Photo_Refresh_20261010::DATA,[]);if (ZP_Prepared_Photo_Refresh_20261010::active_for($job['id'])) { throw new RuntimeException('photo_active'); }
        $v=self::read_option('zps_article_photos_20261010_state',[]);
        foreach (is_array($v)?($v['jobs']??[]):[] as $j) { if ((int)($j['post_id']??0)===self::POST_ID && !in_array($j['state']??'',['rolled_back','cancelled'],true)) { throw new RuntimeException('photo_active'); } }
    }
    private static function widget($nodes, int $depth=0): array {
        if (!is_array($nodes) || $depth>20 || count($nodes)>2000) { throw new RuntimeException('unsupported_article'); } $hits=[];
        foreach ($nodes as $n) { if (!is_array($n)) { throw new RuntimeException('unsupported_article'); } $html=$n['settings']['html']??null;
            if (($n['widgetType']??'')==='html' && is_string($html) && str_contains($html,'zpArticlePrepared')) { $hits[]=$html; }
            if (is_array($n['elements']??null)) { $hits=array_merge($hits,self::widget($n['elements'],$depth+1)); }
        } return $hits;
    }
    /** GET-safe preflight. No model checks, queue writes, provider calls or publications. */
    public static function inspect(): array {
        if (!self::allowed()) { throw new RuntimeException('forbidden'); }self::refresh_post(); $id=self::POST_ID; $p=get_post($id); $j=self::core_job(); self::publication($p,$j); self::no_other_photos($j);
        $date=DateTimeImmutable::createFromFormat('!Y-m-d H:i:s',$p->post_date_gmt,new DateTimeZone('UTC'));
        if (!$date || $date->format('Y-m-d H:i:s')!==$p->post_date_gmt || $date->getTimestamp()<time()-14*86400 || $date->getTimestamp()>time()+60) { throw new RuntimeException('outside_recent_period'); }
        $ready=ZP_Content_Preparation_20261010::fingerprint($id);
        if (!is_string($j['ready_hash']??null) || !hash_equals($j['ready_hash'],$ready) || !hash_equals($j['publish_attempt']['hash'],$ready) || get_post_meta($id,ZP_Content_Preparation_20261010::PREFIX.'ready_hash',true)!==$ready) { throw new RuntimeException('post_changed'); }
        $raw=get_post_meta($id,'_elementor_data',true); if (!is_string($raw) || strlen($raw)>800000 || get_post_meta($id,'_elementor_edit_mode',true)!=='builder') { throw new RuntimeException('unsupported_article'); }
        $hits=self::widget(json_decode($raw,true)); if (count($hits)!==1) { throw new RuntimeException('unsupported_article'); } $widget=$hits[0]; $html=$p->post_content;
        if (!is_string($html) || $widget!==ZP_Content_Preparation_20261010::style().$html) { throw new RuntimeException('unsupported_article'); }
        $slots=ZP_Article_Photo_Refresh_20261010::slots($html); if (count($slots)!==2) { throw new RuntimeException('unsupported_article'); }
        foreach (['cover','inline'] as $i=>$role) { $m=ZP_Content_Preparation_20261010::media($j[$role.'_id']??0);
            if ($slots[$i]['url']!==$m['url'] || $slots[$i]['width']!==1536 || $slots[$i]['height']!==1024 || ($role==='cover' && get_post_thumbnail_id($id)!==$m['id'])) { throw new RuntimeException('images_changed'); }
        }
        if (get_post_meta($id,'rank_math_facebook_image',true)!==wp_get_attachment_url($j['cover_id']) || (string)get_post_meta($id,'rank_math_facebook_image_id',true)!==(string)$j['cover_id']) { throw new RuntimeException('static_seo_image'); }
        $urls=array_column($slots,'url'); $refs=static function($v)use(&$refs,$urls):bool { if (is_string($v)) { if (in_array($v,$urls,true)) { return true; } if (strlen($v)<800000 && preg_match('/^\s*[\[{]/',$v)) { $a=json_decode($v,true); return is_array($a)&&$refs($a); } } elseif (is_array($v)) { foreach ($v as $part) { if ($refs($part)) { return true; } } } return false; };
        foreach (get_post_meta($id) as $key=>$values) { if (str_starts_with((string)$key,'rank_math_schema_') && $refs($values)) { throw new RuntimeException('static_seo_image'); }
            if (preg_match('/(?:twitter|rich_snippet)_image(?:_id)?$/D',(string)$key)) { foreach ((array)$values as $v) { if ($refs($v) || (is_scalar($v) && (string)$v===(string)$j['cover_id'])) { throw new RuntimeException('static_seo_image'); } } }
        }
        preg_match_all('~<script\b[^>]*type=["\']application/ld\+json["\'][^>]*>(.*?)</script>~is',$html,$scripts); foreach ($scripts[1] as $v) { if ($refs(json_decode($v,true))) { throw new RuntimeException('static_seo_image'); } }
        $plan=ZP_Prepared_Photo_Refresh_20261010::scene_plan($p->post_title,$html); if (($plan['topic']??'')!=='shirt-logo-detail') { throw new RuntimeException('unsupported_article'); }
        $meta=[]; foreach (self::PHOTO_META as $k) { $meta[$k]=get_post_meta($id,$k,true); } $hashmeta=[]; foreach (self::HASH_META as $k) { $hashmeta[$k]=get_post_meta($id,$k,true); }
        $s=['id'=>$id,'prepared_id'=>$j['id'],'prepared_job'=>$j,'fields'=>self::fields($id),'other_meta'=>self::other_meta($id),'photo_meta'=>$meta,'hash_meta'=>$hashmeta,'fingerprint_categories'=>wp_get_post_categories($id),'html'=>$html,'widget'=>$widget,'slots'=>$slots,'scene_plan'=>$plan,'before_hash'=>$ready]; $s['sha']=self::sha($s); return $s;
    }
    private static function snapshot(array $job): array { $s=$job['snapshot']??null; if (!is_array($s) || ($s['id']??null)!==self::POST_ID || !is_string($s['sha']??null)) { throw new RuntimeException('snapshot_invalid'); } $copy=$s; unset($copy['sha']); if (!hash_equals($s['sha'],self::sha($copy))) { throw new RuntimeException('snapshot_invalid'); } return $s; }
    public static function start(string $expected): string {
        if (!self::allowed() || !preg_match('/^[a-f0-9]{64}$/D',$expected)) { throw new RuntimeException('forbidden'); }
        return ZP_Content_Preparation_20261010::exclusive(static function()use($expected) { $s=self::inspect(); if (!hash_equals($s['sha'],$expected)) { throw new RuntimeException('post_changed'); } $d=self::data();
            foreach(self::ledger()as$e){if(!isset($d['jobs'][$e['job']]))throw new RuntimeException('ledger_invalid');}
            foreach ($d['jobs']??[] as $j) { if (!in_array($j['state']??'',['cancelled','rolled_back'],true)) { throw new RuntimeException('already_started'); } }
            if (count($d['jobs']??[])>=5) { throw new RuntimeException('history_limit'); } $id=wp_generate_uuid4(); $d['jobs'][$id]=['id'=>$id,'actor'=>get_current_user_id(),'snapshot'=>$s,'config'=>self::config(),'state'=>'queued','stage'=>'cover','cover_id'=>0,'inline_id'=>0,'created_at'=>time(),'updated_at'=>time(),'error'=>'']; self::save(self::DATA,$d); self::next(); return $id;
        });
    }
    private static function unchanged(array $job): void { $s=self::snapshot($job); if (!hash_equals($s['sha'],self::inspect()['sha'])) { throw new RuntimeException('post_changed'); } }
    private static function media_id(array $job,string $role): int {
        $ids=get_posts(['post_type'=>'attachment','post_status'=>'inherit','numberposts'=>2,'fields'=>'ids','meta_query'=>[['key'=>self::PREFIX.'job','value'=>$job['id']],['key'=>self::PREFIX.'role','value'=>$role]]]); if (count($ids)>1) { throw new RuntimeException('media_changed'); } return (int)($ids[0]??0);
    }
    private static function media_url(int $id): string {
        $url=wp_get_attachment_url($id);$p=is_string($url)?parse_url($url):false;$h=parse_url(home_url('/'));
        if(!is_array($p)||($p['scheme']??'')!=='https'||strtolower($p['host']??'')!==strtolower($h['host']??'')||!str_contains($p['path']??'','/wp-content/uploads/')||array_intersect(['port','query','fragment','user','pass'],array_keys($p)))throw new RuntimeException('media_changed');return$url;
    }
    private static function media_signature(int $id): string {
        $p=get_post($id); $file=get_attached_file($id); $uploads=wp_get_upload_dir(); $base=realpath($uploads['basedir']??''); $real=is_string($file)?realpath($file):false;
        if (!$p || $p->post_type!=='attachment' || $p->post_status!=='inherit' || get_post_mime_type($id)!=='image/webp' || !$base || !$real || !str_starts_with($real,$base.DIRECTORY_SEPARATOR) || !is_readable($real)) { throw new RuntimeException('media_changed'); }
        return self::sha([$p->post_title,$p->post_content,$p->post_excerpt,$p->post_author,$p->post_parent??0,self::media_url($id),$real,hash_file('sha256',$real),wp_get_attachment_metadata($id),get_post_meta($id,'_wp_attachment_image_alt',true)]);
    }
    /** Native subsize generation writes metadata incrementally; capture it until our own expected receipt is durable. */
    private static function generate_metadata(int $id,string $file): array {
        $present=metadata_exists('post',$id,'_wp_attachment_metadata'); $before=get_post_meta($id,'_wp_attachment_metadata',true); $attached=get_attached_file($id);
        $capture=static function($check,$objectId,$metaKey,$metaValue,$previous)use($id) { return (int)$objectId===$id && in_array($metaKey,['_wp_attachment_metadata','_wp_attached_file'],true) ? true : $check; };
        $threshold=static function($value,$size,$path,$objectId)use($id) { return (int)$objectId===$id ? false : $value; };
        add_filter('update_post_metadata',$capture,PHP_INT_MAX,5); add_filter('big_image_size_threshold',$threshold,PHP_INT_MAX,4);
        try { $expected=wp_generate_attachment_metadata($id,$file); }
        finally { remove_filter('update_post_metadata',$capture,PHP_INT_MAX); remove_filter('big_image_size_threshold',$threshold,PHP_INT_MAX); wp_cache_delete($id,'post_meta'); }
        $base=realpath(wp_get_upload_dir()['basedir']??''); $actual=realpath($file); $relative=$base && $actual && str_starts_with($actual,$base.DIRECTORY_SEPARATOR) ? str_replace(DIRECTORY_SEPARATOR,'/',substr($actual,strlen($base)+1)) : null;
        if (metadata_exists('post',$id,'_wp_attachment_metadata')!==$present || get_post_meta($id,'_wp_attachment_metadata',true)!==$before || get_attached_file($id)!==$attached || !is_array($expected) || !is_string($relative) || ($expected['file']??null)!==$relative || isset($expected['original_image'])) { throw new RuntimeException('metadata_failed'); }
        return $expected;
    }
    private static function finish_media(int $id,array $job,string $role): array {
        wp_cache_delete($id,'posts');wp_cache_delete($id,'post_meta');$s=self::snapshot($job); $scene=$s['scene_plan'][$role]; $url=self::media_url($id); $p=get_post($id); $file=get_attached_file($id); $sha=get_post_meta($id,self::PREFIX.'generated_sha',true);
        foreach (['job'=>$job['id'],'role'=>$role,'source_sha'=>$s['sha'],'model'=>$job['config']['model']] as $k=>$v) { if (get_post_meta($id,self::PREFIX.$k,true)!==$v) { throw new RuntimeException('media_changed'); } }
        if (!$p || $p->post_title!==$scene['alt'] || $p->post_content!==$scene['caption'] || $p->post_excerpt!==$scene['caption'] || (int)$p->post_author!==(int)$job['actor'] || (int)($p->post_parent??0)!==0 || !is_string($file) || !is_readable($file) || !is_string($sha) || !hash_equals($sha,hash_file('sha256',$file))) { throw new RuntimeException('media_changed'); }
        $original=get_post_meta($id,self::PREFIX.'original_file',true); $base=realpath(wp_get_upload_dir()['basedir']??''); $real=realpath($file); $attempt=get_post_meta($id,self::PREFIX.'attempt',true);
        if($original!==$file||!$base||!$real||!str_starts_with($real,$base.DIRECTORY_SEPARATOR)||!preg_match('/^[1-3]$/D',(string)$attempt))throw new RuntimeException('media_changed');
        $entry=self::ledger()[$job['id'].'|'.$role.'|'.$attempt]??null;
        if(!is_array($entry)||($entry['job']??null)!==$job['id']||($entry['role']??null)!==$role||($entry['actor']??null)!==$job['actor']||($entry['source_sha']??null)!==$s['sha']||($entry['request_sha']??null)!==self::sha(self::request_body($job,$role))||($entry['connection_fingerprint']??null)!==$job['config']['fingerprint']||!in_array($entry['state']??'',['requested','stored'],true)||(!empty($entry['attachment_id'])&&$entry['attachment_id']!==$id))throw new RuntimeException('ledger_invalid');
        $proof=get_post_meta($id,self::PREFIX.'finalized_hash',true); if ($proof!=='') { if (!is_string($proof) || !hash_equals($proof,self::media_signature($id))) { throw new RuntimeException('media_changed'); } return ['id'=>$id,'url'=>$url,'alt'=>$scene['alt'],'caption'=>$scene['caption'],'sha'=>$sha]; }
        if (!in_array(get_post_meta($id,'_wp_attachment_image_alt',true),['',$scene['alt']],true)) { throw new RuntimeException('media_changed'); }
        $expected=get_post_meta($id,self::PREFIX.'expected_metadata',true);
        if ($expected==='') { if (metadata_exists('post',$id,'_wp_attachment_metadata')) { throw new RuntimeException('media_changed'); } if (!function_exists('wp_generate_attachment_metadata')) { require_once ABSPATH.'wp-admin/includes/image.php'; } $expected=self::generate_metadata($id,$file); if (!is_array($expected) || (int)($expected['width']??0)!==1536 || (int)($expected['height']??0)!==1024) { throw new RuntimeException('metadata_failed'); } self::meta_write($id,self::PREFIX.'expected_metadata',$expected); }
        if(!is_array($expected)||(int)($expected['width']??0)!==1536||(int)($expected['height']??0)!==1024||strlen(wp_json_encode($expected))>200000)throw new RuntimeException('media_changed');
        $current=wp_get_attachment_metadata($id);$present=metadata_exists('post',$id,'_wp_attachment_metadata');if(($present&&$current!==$expected)||(!$present&&$current!==''&&$current!==false))throw new RuntimeException('media_changed');wp_update_attachment_metadata($id,$expected); if (wp_get_attachment_metadata($id)!==$expected) { throw new RuntimeException('metadata_failed'); } self::meta_write($id,'_wp_attachment_image_alt',$scene['alt']); self::meta_write($id,self::PREFIX.'finalized_hash',self::media_signature($id));
        return ['id'=>$id,'url'=>$url,'alt'=>$scene['alt'],'caption'=>$scene['caption'],'sha'=>$sha];
    }
    private static function request_body(array $job,string $role): array {
        if(!in_array($role,['cover','inline'],true))throw new RuntimeException('invalid_stage');$s=self::snapshot($job);
        return ['model'=>$job['config']['model'],'prompt'=>ZP_Prepared_Photo_Refresh_20261010::prompt($s,$role),'n'=>1,'size'=>'1536x1024','quality'=>$job['config']['quality'],'background'=>'opaque','output_format'=>'webp'];
    }
    private static function generate(array $job,string $role): array {
        $known=self::media_id($job,$role); if ($known) { return self::finish_media($known,$job,$role); } if (!class_exists('ZP_Managed_WebP_20261010')) { throw new RuntimeException('webp_encoder_unavailable'); } if (!function_exists('wp_tempnam')) { require_once ABSPATH.'wp-admin/includes/file.php'; } self::unchanged($job); self::provider($job); $s=self::snapshot($job);
        $body=self::request_body($job,$role); $ledger=self::ledger(); $entries=[];
        foreach ($ledger as $key=>$v) { if (is_array($v) && ($v['job']??null)===$job['id'] && ($v['role']??null)===$role) { $entries[$key]=$v; } } $attempt=count($entries)+1;
        if ($attempt>3) { throw new RuntimeException('attempt_limit'); } if ($entries && ($job['approved_attempt']??null)!==$attempt) { throw new RuntimeException('image_ambiguous'); }
        $key=$job['id'].'|'.$role.'|'.$attempt; $entry=['job'=>$job['id'],'role'=>$role,'attempt'=>$attempt,'actor'=>$job['actor'],'source_sha'=>$s['sha'],'request_sha'=>self::sha($body),'connection_fingerprint'=>$job['config']['fingerprint'],'state'=>'requested','at'=>time()]; $ledger[$key]=$entry; self::save(self::LEDGER,$ledger);
        // Durable unknown-outcome marker is written before the paid request. It prevents automatic replay even if DATA is lost.
        $r=wp_remote_post('https://api.openai.com/v1/images/generations',['timeout'=>180,'redirection'=>0,'sslverify'=>true,'limit_response_size'=>16000000,'headers'=>['Authorization'=>'Bearer '.self::key(),'Content-Type'=>'application/json'],'body'=>wp_json_encode($body)]);
        if (is_wp_error($r)) { throw new RuntimeException('image_ambiguous'); } $code=wp_remote_retrieve_response_code($r); if ($code!==200) { throw new RuntimeException(in_array($code,[401,403,429],true)?'image_http_'.$code:'image_failed'); }
        $response=json_decode(wp_remote_retrieve_body($r),true); if (!is_array($response)) { throw new RuntimeException('image_response'); } $bytes=ZP_Content_Preparation_20261010::image_bytes($response); $encoded=ZP_Managed_WebP_20261010::encode_bytes($bytes,1536,1024); if (!is_string($encoded['bytes']??null) || ($encoded['width']??null)!==1536 || ($encoded['height']??null)!==1024 || ($encoded['byte_count']??null)!==strlen($encoded['bytes']) || strlen($encoded['bytes'])>512000 || ($encoded['source_sha256']??null)!==hash('sha256',$bytes) || ($encoded['sha256']??null)!==hash('sha256',$encoded['bytes'])) { throw new RuntimeException('webp_output'); } $bytes=$encoded['bytes']; self::actor($job); if(!self::allowed())throw new RuntimeException('actor_permissions'); self::unchanged($job);
        $upload=wp_upload_bits('zp-published-5788-'.substr($job['id'],0,12).'-'.$role.'.webp',null,$bytes); if (!empty($upload['error']) || !is_string($upload['file']??null)) { throw new RuntimeException('image_storage'); } $scene=$s['scene_plan'][$role];
        $id=wp_insert_attachment(['post_mime_type'=>'image/webp','post_type'=>'attachment','post_status'=>'inherit','post_author'=>$job['actor'],'post_title'=>$scene['alt'],'post_content'=>$scene['caption'],'post_excerpt'=>$scene['caption'],'meta_input'=>[self::PREFIX.'job'=>$job['id'],self::PREFIX.'role'=>$role,self::PREFIX.'source_sha'=>$s['sha'],self::PREFIX.'model'=>$job['config']['model'],self::PREFIX.'generated_sha'=>hash('sha256',$bytes),self::PREFIX.'provider_sha256'=>$encoded['source_sha256'],self::PREFIX.'webp_quality'=>$encoded['quality'],self::PREFIX.'webp_bytes'=>$encoded['byte_count'],self::PREFIX.'original_file'=>$upload['file'],self::PREFIX.'attempt'=>$attempt]],$upload['file'],0,true);
        if (is_wp_error($id) || !$id) { throw new RuntimeException('image_storage'); } $ledger=self::ledger(); if (($ledger[$key]??null)!==$entry) { throw new RuntimeException('ledger_invalid'); } $ledger[$key]['attachment_id']=(int)$id; $ledger[$key]['state']='stored'; self::save(self::LEDGER,$ledger);
        return self::finish_media((int)$id,$job,$role);
    }
    public static function worker(): void {
        if(function_exists('set_time_limit')){@set_time_limit(240);}
        $previous=get_current_user_id(); $id=null; $job=null; $apply_id=null;
        try { ZP_Content_Preparation_20261010::exclusive(static function()use(&$id,&$job,&$apply_id) { $d=self::data(); foreach ($d['jobs']??[] as $k=>$v) { if (in_array($v['state']??'',['queued','ready'],true)) { $id=$k; $job=$v; break; } } if (!$job) { return; } self::actor($job); wp_set_current_user($job['actor']); if (!self::allowed()) { throw new RuntimeException('actor_permissions'); } self::unchanged($job);
                if ($job['state']==='ready') { $apply_id=$id; return; }
                $role=$job['stage']; if (!in_array($role,['cover','inline'],true)) { throw new RuntimeException('invalid_stage'); } if($role==='inline'){self::finish_media((int)$job['cover_id'],$job,'cover');} $job['state']='running'; $job['flight']=wp_generate_uuid4(); $job['started_at']=time(); $d['jobs'][$id]=$job; self::save(self::DATA,$d);
                $m=self::generate($job,$role); $job[$role.'_id']=$m['id']; unset($job['approved_attempt']); $job['stage']=$role==='cover'?'inline':'ready'; $job['state']=$role==='cover'?'queued':'ready'; $job['updated_at']=time(); $job['error']=''; $latest=self::data(); if (($latest['jobs'][$id]['flight']??null)!==$job['flight']) { throw new RuntimeException('storage_changed'); } unset($job['flight']); $latest['jobs'][$id]=$job; self::save(self::DATA,$latest); self::next(); if ($job['state']==='ready') { $apply_id=$id; }
            });
        } catch (Throwable $e) { if ($e instanceof RuntimeException && $e->getMessage()==='busy') { return; } if ($id!==null && $job) { try { ZP_Content_Preparation_20261010::exclusive(static function()use($id,$job,$e) { $d=self::data(); if (($d['jobs'][$id]['flight']??null)!==($job['flight']??null) || (!isset($job['flight']) && ($d['jobs'][$id]??null)!==$job)) { return; } $job['state']='attention'; $job['error']=self::safe_error($e); $job['updated_at']=time(); unset($job['flight'],$job['approved_attempt']); $d['jobs'][$id]=$job; self::save(self::DATA,$d); }); } catch (Throwable $ignored) {} } }
        finally { wp_set_current_user($previous); }
        // exclusive() has released the generation lock. Apply acquires its own lock and re-reads all guards.
        if ($apply_id!==null) { self::auto_apply($apply_id); }
    }
    private static function auto_apply(string $id): void {
        $previous=get_current_user_id();
        try {
            $j=self::data()['jobs'][$id]??null;
            if (!is_array($j) || ($j['state']??'')!=='ready') { return; }
            self::actor($j); wp_set_current_user($j['actor']);
            if (!self::allowed()) { throw new RuntimeException('actor_permissions'); }
            self::apply($id);
        } catch (Throwable $e) {
            if ($e instanceof RuntimeException && $e->getMessage()==='busy') {
                try { if ((self::data()['jobs'][$id]['state']??'')==='ready') { self::next(); } } catch (Throwable $ignored) {}
                return;
            }
            try { ZP_Content_Preparation_20261010::exclusive(static function()use($id,$e) {
                $d=self::data(); $j=$d['jobs'][$id]??null;
                // Preserve a successor's applied/cancelled state and any existing protected apply failure/proposal.
                if (!is_array($j) || !(($j['state']??'')==='ready' || (($j['state']??'')==='applying' && ($j['stage']??'')==='apply'))) { return; }
                $j['state']='attention'; $j['stage']='apply'; $j['error']=self::safe_error($e); $j['updated_at']=time();
                $d['jobs'][$id]=$j; self::save(self::DATA,$d); self::cleanup();
            }); } catch (Throwable $ignored) {}
        } finally { wp_set_current_user($previous); }
    }
    /** Repair only an existing pending event; GET never generates, rewrites a job or approves a paid replay. */
    public static function resume_pending(): void {
        if (!is_admin() || !self::allowed()) { return; }
        try {
            $ledger=self::ledger();
            foreach (array_slice(self::data()['jobs']??[],0,5,true) as $j) {
                if (!is_array($j) || !in_array($j['state']??'',['queued','ready'],true) || isset($j['flight'])) { continue; }
                self::actor($j); self::snapshot($j);
                if ($j['state']==='queued') {
                    if (!in_array($j['stage']??'',['cover','inline'],true)) { continue; }
                    $unknown=false; foreach ($ledger as $entry) { if ($entry['job']===$j['id'] && $entry['role']===$j['stage'] && $entry['state']==='requested') { $unknown=true; break; } }
                    if ($unknown) { continue; }
                }
                self::next(); return;
            }
        } catch (Throwable $ignored) {}
    }
    public static function watchdog(): void {
        try { ZP_Content_Preparation_20261010::exclusive(static function() { $d=self::data(); $changed=false; foreach ($d['jobs']??[] as $id=>$j) { if (($j['state']??'')==='running' && (int)($j['started_at']??0)<time()-600) { $d['jobs'][$id]['state']='attention'; $d['jobs'][$id]['error']='image_ambiguous'; unset($d['jobs'][$id]['flight'],$d['jobs'][$id]['approved_attempt']); $changed=true; } elseif (in_array($j['state']??'',['queued','ready'],true)) { self::next(); } } if ($changed) { self::save(self::DATA,$d); }self::cleanup(); }); } catch (Throwable $ignored) {}
    }
    public static function retry(string $id,bool $paid): void {
        if (!self::allowed()) { throw new RuntimeException('forbidden'); } ZP_Content_Preparation_20261010::exclusive(static function()use($id,$paid) { $d=self::data(); $j=$d['jobs'][$id]??null; if (!is_array($j) || ($j['state']??'')!=='attention' || !in_array($j['stage']??'',['cover','inline'],true)) { throw new RuntimeException('invalid_job'); } self::actor($j); self::unchanged($j); self::provider($j);
            if (!self::media_id($j,$j['stage']) && !$paid) { throw new RuntimeException('paid_confirmation'); } if ($paid) { $count=0; foreach(self::ledger() as $entry){if(($entry['job']??null)===$j['id']&&($entry['role']??null)===$j['stage'])$count++;} if($count>=3)throw new RuntimeException('attempt_limit'); $j['approved_attempt']=$count+1; } $j['state']='queued'; $j['error']=''; $d['jobs'][$id]=$j; self::save(self::DATA,$d); self::next(); });
    }
    private static function hash_with(array $s,string $html,array $meta): string {
        $all=$s['hash_meta']; foreach ($meta as $k=>$v) { if (array_key_exists($k,$all)) { $all[$k]=$v; } } $p=$s['fields']; return self::sha([$p['post_title'],$p['post_name'],$html,$p['post_excerpt'],$p['post_author'],$p['post_password'],$s['fingerprint_categories'],$all]);
    }
    private static function proposal(array $job): array {
        $s=self::snapshot($job); $a=self::finish_media($job['cover_id'],$job,'cover'); $b=self::finish_media($job['inline_id'],$job,'inline'); if ($a['id']===$b['id'] || $a['sha']===$b['sha']) { throw new RuntimeException('duplicate_image'); }
        $html=ZP_Article_Photo_Refresh_20261010::replace($s['html'],$s['slots'],[$a,$b]); $meta=$s['photo_meta']; $meta['_elementor_data']=ZP_Article_Photo_Refresh_20261010::replace_widget($meta['_elementor_data'],$s['widget'],ZP_Content_Preparation_20261010::style().$html); $meta['_thumbnail_id']=(string)$a['id']; $meta['rank_math_facebook_image']=$a['url']; $meta['rank_math_facebook_image_id']=(string)$a['id']; $ready=self::hash_with($s,$html,$meta); $meta[ZP_Content_Preparation_20261010::PREFIX.'ready_hash']=$ready;
        $core=$s['prepared_job']; $core['cover_id']=$a['id']; $core['inline_id']=$b['id']; $core['article']['cover_alt']=$a['alt']; $core['article']['inline_alt']=$b['alt']; $core['ready_hash']=$ready;
        $receipt=['adapter_job'=>$job['id'],'snapshot_sha'=>$s['sha'],'original_attempt_sha'=>self::sha($core['publish_attempt']),'before_hash'=>$s['before_hash'],'after_hash'=>$ready,'changed_at'=>time()]; $core['publication_photo_rebase']=$receipt;
        return ['html'=>$html,'meta'=>$meta,'core'=>$core,'receipt'=>$receipt];
    }
    /** Only old or this operation's exact proposed photo values are admissible during recovery. */
    private static function variant(array $job,array $proposal,?bool $new=null): void {
        $s=self::snapshot($job); $id=self::POST_ID;self::refresh_post(); $core=self::core_job(); self::publication(get_post($id),$core); self::no_other_photos($core);
        if (self::fields($id)!==$s['fields'] || self::other_meta($id)!==$s['other_meta']) { throw new RuntimeException('post_changed'); }
        $old=$s['photo_meta']; $current=get_post($id)->post_content; $html=$new===null?[$s['html'],$proposal['html']]:[$new?$proposal['html']:$s['html']]; if (!in_array($current,$html,true)) { throw new RuntimeException('post_changed'); }
        foreach ($old as $k=>$v) { $expected=$new===null?[$v,$proposal['meta'][$k]]:[$new?$proposal['meta'][$k]:$v]; if (!in_array(get_post_meta($id,$k,true),$expected,true)) { throw new RuntimeException('post_changed'); } }
        $receipt=get_post_meta($id,self::PREFIX.'receipt',true); $allowedReceipt=$new===null?['',$proposal['receipt']]:[$new?$proposal['receipt']:'']; if(!in_array($receipt,$allowedReceipt,true))throw new RuntimeException('post_changed');
        $expected=$new===null?[$s['prepared_job'],$proposal['core']]:[$new?$proposal['core']:$s['prepared_job']]; if (!in_array($core,$expected,true)) { throw new RuntimeException('ownership_changed'); }
    }
    private static function native_content(array $s,string $html): void {
        $id=self::POST_ID; $fields=$s['fields']; $filter=static function($data,$postarr,$unsanitized,$update)use($id,$fields) { if ($update && (int)($postarr['ID']??0)===$id) { foreach ($fields as $k=>$v) { if (str_starts_with($k,'post_') && $k!=='post_content') { $data[$k]=$v; } } } return $data; };
        add_filter('wp_insert_post_data',$filter,PHP_INT_MAX,4); try { $r=wp_update_post(wp_slash(['ID'=>$id,'post_content'=>$html]),true); if (is_wp_error($r) || (int)$r!==$id || get_post($id)->post_content!==$html || self::fields($id)!==$fields) { throw new RuntimeException('apply_failed'); } } finally { remove_filter('wp_insert_post_data',$filter,PHP_INT_MAX); }
    }
    private static function write_core(array $job,array $proposal,bool $new): void {
        $s=self::snapshot($job); $data=self::read_option(ZP_Content_Preparation_20261010::DATA,[]); $current=$data['jobs'][$s['prepared_id']]??null;
        if (!in_array($current,[$s['prepared_job'],$proposal['core']],true)) { throw new RuntimeException('ownership_changed'); } $data['jobs'][$s['prepared_id']]=$new?$proposal['core']:$s['prepared_job']; self::save(ZP_Content_Preparation_20261010::DATA,$data);
    }
    private static function saved_proposal(array $j): array { $p=$j['proposal']??null; if (!is_array($p) || !is_string($j['proposal_sha']??null) || !hash_equals($j['proposal_sha'],self::sha($p))) { throw new RuntimeException('snapshot_invalid'); } return $p; }
    private static function commit(array &$job,array $p,bool $new,?string &$phase=null): void {
        $phase='validate';self::actor($job); if (!self::allowed()) { throw new RuntimeException('forbidden'); } self::variant($job,$p); $s=self::snapshot($job); $target=$new?$p['meta']:$s['photo_meta']; $phase='native_content';self::native_content($s,$new?$p['html']:$s['html']);
        foreach (self::PHOTO_META as $k) { $phase=['_elementor_data'=>'meta_elementor','_thumbnail_id'=>'meta_thumbnail','rank_math_facebook_image'=>'meta_social','rank_math_facebook_image_id'=>'meta_social_id','_zp_prepared_20261010_ready_hash'=>'meta_ready_hash'][$k];self::meta_write(self::POST_ID,$k,$target[$k]); } $phase='core_job';self::write_core($job,$p,$new);
        $phase='photo_receipt';if ($new) { self::meta_write(self::POST_ID,self::PREFIX.'receipt',$p['receipt']); } else { $old=get_post_meta(self::POST_ID,self::PREFIX.'receipt',true); if ($old!=='' && $old!==$p['receipt']) { throw new RuntimeException('post_changed'); } delete_post_meta(self::POST_ID,self::PREFIX.'receipt'); }
        $phase='native_readback';delete_post_meta(self::POST_ID,'_elementor_element_cache'); clean_post_cache(self::POST_ID); self::variant($job,$p,$new);
        $phase='fingerprint';$expected=$new?$p['core']['ready_hash']:$s['before_hash']; if (!hash_equals($expected,ZP_Content_Preparation_20261010::fingerprint(self::POST_ID))) { throw new RuntimeException('apply_failed'); } $job['state']=$new?'applied':'rolled_back'; $job['updated_at']=time(); $job['error']='';$phase='complete';
    }
    /** Prove the already saved original publication and exact owned photo proposal; never finalize or generate media. */
    private static function reconciliation_proof(array $job): array {
        if(!self::allowed()||!in_array($job['state']??'', ['attention','applying','applied'],true)||!in_array($job['stage']??'', ['apply','complete'],true)||!empty($job['flight']))throw new RuntimeException('reconciliation_not_exact');self::actor($job);$s=self::snapshot($job);$p=self::saved_proposal($job);$d=self::proposal_diagnostics($job);
        if(empty($d['proposal_valid'])||empty($d['other_meta_same'])||in_array(false,$d['native_fields_same']??[],true)||empty($d['publication_current'])||empty($d['body_nonphoto_shape']))throw new RuntimeException('reconciliation_not_exact');foreach($d['photo_fields']??[] as $field)if(empty($field['own_new_equal']))throw new RuntimeException('reconciliation_not_exact');if(empty($d['core_job']['own_new_equal'])||empty($d['receipt']['own_new_equal']))throw new RuntimeException('reconciliation_not_exact');self::variant($job,$p,true);
        $base=realpath(wp_get_upload_dir()['basedir']??'');if(!$base)throw new RuntimeException('media_changed');$ledger=self::ledger();
        foreach(['cover','inline'] as $role){$id=$job[$role.'_id']??null;if(!is_int($id)||$id<1)throw new RuntimeException('media_changed');wp_cache_delete($id,'posts');wp_cache_delete($id,'post_meta');$post=get_post($id);$file=get_attached_file($id);$real=is_string($file)?realpath($file):false;$sha=get_post_meta($id,self::PREFIX.'generated_sha',true);$scene=$s['scene_plan'][$role];$meta=wp_get_attachment_metadata($id);$expected=get_post_meta($id,self::PREFIX.'expected_metadata',true);$final=get_post_meta($id,self::PREFIX.'finalized_hash',true);$bytes=$real?filesize($real):false;try{$size=$real?@getimagesize($real):false;}catch(Throwable $ignored){throw new RuntimeException('media_changed');}
            if(!$post||$post->post_type!=='attachment'||$post->post_status!=='inherit'||get_post_mime_type($id)!=='image/webp'||$post->post_title!==$scene['alt']||$post->post_content!==$scene['caption']||$post->post_excerpt!==$scene['caption']||(int)$post->post_author!==(int)$job['actor']||(int)($post->post_parent??0)!==0||!is_string($file)||is_link($file)||!$real||!str_starts_with($real,$base.DIRECTORY_SEPARATOR)||!is_readable($real)||!is_int($bytes)||$bytes<100||$bytes>512000||!is_string($sha)||!preg_match('/^[a-f0-9]{64}$/D',$sha)||!hash_equals($sha,hash_file('sha256',$real))||get_post_meta($id,self::PREFIX.'original_file',true)!==$file||!is_array($size)||($size['mime']??'')!=='image/webp'||$size[0]!==1536||$size[1]!==1024||!metadata_exists('post',$id,'_wp_attachment_metadata')||!is_array($meta)||$meta!==$expected||(int)($meta['width']??0)!==1536||(int)($meta['height']??0)!==1024||get_post_meta($id,'_wp_attachment_image_alt',true)!==$scene['alt']||!is_string($final)||!preg_match('/^[a-f0-9]{64}$/D',$final)||!hash_equals($final,self::media_signature($id)))throw new RuntimeException('media_changed');
            foreach(['job'=>$job['id'],'role'=>$role,'source_sha'=>$s['sha'],'model'=>$job['config']['model']] as $key=>$value)if(get_post_meta($id,self::PREFIX.$key,true)!==$value)throw new RuntimeException('media_changed');$attempt=get_post_meta($id,self::PREFIX.'attempt',true);if(!preg_match('/^[1-3]$/D',(string)$attempt))throw new RuntimeException('ledger_invalid');$entry=$ledger[$job['id'].'|'.$role.'|'.$attempt]??null;
            if(!is_array($entry)||($entry['actor']??null)!==$job['actor']||($entry['source_sha']??null)!==$s['sha']||($entry['request_sha']??null)!==self::sha(self::request_body($job,$role))||($entry['connection_fingerprint']??null)!==$job['config']['fingerprint']||($entry['state']??null)!=='stored'||($entry['attachment_id']??null)!==$id)throw new RuntimeException('ledger_invalid');
        }
        self::refresh_post();self::variant($job,$p,true);$actual=ZP_Content_Preparation_20261010::fingerprint(self::POST_ID);$core=self::core_job();if($actual!==$p['core']['ready_hash']||get_post_meta(self::POST_ID,ZP_Content_Preparation_20261010::PREFIX.'ready_hash',true)!==$actual||($core['publish_attempt']??null)!==$s['prepared_job']['publish_attempt']||self::sha($core['publish_attempt'])!==$p['receipt']['original_attempt_sha'])throw new RuntimeException('reconciliation_not_exact');return ['post_id'=>self::POST_ID,'snapshot_sha'=>$s['sha'],'proposal_sha'=>self::sha($p),'original_attempt_sha'=>self::sha($core['publish_attempt']),'after_hash'=>$actual];
    }
    private static function completion_error(string $id,string $phase,Throwable $error): void {
        try{$d=self::data();$j=$d['jobs'][$id]??null;if(!is_array($j)||!in_array($j['state']??'', ['attention','applying'],true)||!self::allowed())return;self::actor($j);self::snapshot($j);$j['last_reconcile_stage']=$phase;$j['last_reconcile_error']=self::safe_error($error);$j['state']='attention';$j['error']='reconciliation_attention';$j['updated_at']=time();$d['jobs'][$id]=$j;self::save(self::DATA,$d);}catch(Throwable $ignored){}
    }
    /** Explicit zero-cost completion. The article, native receipt, original attempt, media and proposal remain byte-identical. */
    public static function reconcile(string $id): void {
        if(!self::allowed())throw new RuntimeException('forbidden');ZP_Content_Preparation_20261010::exclusive(static function()use($id){$phase='validate';try{$d=self::data();$job=$d['jobs'][$id]??null;if(!is_array($job)||($job['id']??null)!==$id)throw new RuntimeException('invalid_job');$proof=self::reconciliation_proof($job);if($job['state']==='applied')return;
                $key='zps_published_prepared_photos_20261010_reconcile_'.$id;$receipt=['version'=>'exact-proposal-v1','job'=>$id]+$proof;$old=self::read_option($key,null);if($old!==null&&$old!==$receipt)throw new RuntimeException('reconciliation_not_exact');$phase='receipt';if($old===null)self::save($key,$receipt);$phase='readback';if(self::reconciliation_proof($job)!==$proof)throw new RuntimeException('reconciliation_not_exact');$latest=self::data();if(($latest['jobs'][$id]??null)!==$job)throw new RuntimeException('storage_changed');$job['state']='applied';$job['stage']='complete';$job['error']='';$job['updated_at']=time();$job['reconciliation_receipt']=$key;$latest['jobs'][$id]=$job;$phase='state_commit';self::save(self::DATA,$latest);self::cleanup();
            }catch(Throwable $error){self::completion_error($id,$phase,$error);throw $error;}});
    }
    public static function apply(string $id): void {
        if (!self::allowed()) { throw new RuntimeException('forbidden'); } ZP_Content_Preparation_20261010::exclusive(static function()use($id) { $d=self::data(); $j=$d['jobs'][$id]??null; if (!is_array($j)) { throw new RuntimeException('invalid_job'); }
            if (($j['state']??'')==='applied') { self::variant($j,self::saved_proposal($j),true); return; } if (($j['state']??'')!=='ready') { throw new RuntimeException('invalid_job'); } self::actor($j); self::unchanged($j); $p=self::proposal($j); $j['proposal']=$p; $j['proposal_sha']=self::sha($p); $j['state']='applying'; $j['stage']='apply'; $d['jobs'][$id]=$j; self::save(self::DATA,$d);
            $phase='validate';try { self::commit($j,$p,true,$phase); } catch (Throwable $e) { $j['state']='attention'; $j['error']=self::safe_error($e);$j['last_apply_stage']=$phase;$j['last_apply_error']=self::safe_error($e); } $d=self::data(); $d['jobs'][$id]=$j; self::save(self::DATA,$d);self::cleanup(); if ($j['state']!=='applied') { throw new RuntimeException('apply_attention'); }
        });
    }
    public static function restore(string $id): void {
        if (!self::allowed()) { throw new RuntimeException('forbidden'); } ZP_Content_Preparation_20261010::exclusive(static function()use($id) { $d=self::data(); $j=$d['jobs'][$id]??null; if (!is_array($j) || !in_array($j['state']??'',['applied','attention','applying'],true)) { throw new RuntimeException('invalid_job'); } self::actor($j); $p=self::saved_proposal($j); self::variant($j,$p,($j['state']==='applied')?true:null);
            $j['state']='applying'; $j['stage']='rollback'; $d['jobs'][$id]=$j; self::save(self::DATA,$d); try { self::commit($j,$p,false); } catch (Throwable $e) { $j['state']='attention'; $j['error']=self::safe_error($e); } $d=self::data(); $d['jobs'][$id]=$j; self::save(self::DATA,$d);self::cleanup(); if ($j['state']!=='rolled_back') { throw new RuntimeException('apply_attention'); }
        });
    }
    public static function cancel(string $id): void {
        if (!self::allowed()) { throw new RuntimeException('forbidden'); } ZP_Content_Preparation_20261010::exclusive(static function()use($id) { $d=self::data(); $j=$d['jobs'][$id]??null; if (!is_array($j) || !empty($j['proposal']) || !in_array($j['state']??'',['queued','ready','attention'],true)) { throw new RuntimeException('recover_first'); } self::actor($j); $j['state']='cancelled'; $j['error']=''; $d['jobs'][$id]=$j; self::save(self::DATA,$d);self::cleanup(); });
    }
    /** Verify original publication receipt plus this adapter's image-only rebase; never publish again. */
    public static function verify_publication(): array {
        if (!self::allowed()) { return ['verified'=>false]; }
        try { foreach (self::data()['jobs']??[] as $j) { if (($j['state']??'')==='applied') { $p=self::saved_proposal($j); self::variant($j,$p,true); $s=self::snapshot($j);
                    if (get_post_meta(self::POST_ID,self::PREFIX.'receipt',true)!==$p['receipt'] || self::sha(self::core_job()['publish_attempt'])!==$p['receipt']['original_attempt_sha'] || !hash_equals($p['receipt']['after_hash'],ZP_Content_Preparation_20261010::fingerprint(self::POST_ID))) { throw new RuntimeException('publication_changed'); }
                    return ['verified'=>true,'photo_rebased'=>true,'post_id'=>self::POST_ID,'published_gmt'=>$s['fields']['post_date_gmt']];
                } }
            self::refresh_post();$core=self::core_job();$post=get_post(self::POST_ID);self::publication($post,$core);$current=ZP_Content_Preparation_20261010::fingerprint(self::POST_ID);
            if(!hash_equals($core['publish_attempt']['hash'],$current)||($core['ready_hash']??null)!==$current||get_post_meta(self::POST_ID,ZP_Content_Preparation_20261010::PREFIX.'ready_hash',true)!==$current)throw new RuntimeException('publication_changed');
            return ['verified'=>true,'photo_rebased'=>false,'post_id'=>self::POST_ID,'published_gmt'=>$post->post_date_gmt];
        } catch (Throwable $e) { return ['verified'=>false,'reason'=>self::safe_error($e)]; }
    }
    private static function safe_error(Throwable $e): string {
        $known=['forbidden','actor_permissions','post_changed','ownership_changed','publication_changed','outside_recent_period','unsupported_article','images_changed','static_seo_image','photo_active','snapshot_invalid','verify_models','connection_changed','already_started','history_limit','ledger_invalid','image_ambiguous','image_http_401','image_http_403','image_http_429','image_failed','image_response','image_invalid','image_storage','media_changed','metadata_failed','attempt_limit','storage_failed','storage_limit','storage_changed','cron_failed','invalid_stage','invalid_job','paid_confirmation','apply_failed','apply_attention','reconciliation_not_exact','reconciliation_attention','recover_first','duplicate_image','busy','webp_encoder_unavailable','webp_dimensions','webp_input','webp_temp','webp_editor','webp_quality','webp_resize','webp_output','webp_weight_limit']; return $e instanceof RuntimeException && in_array($e->getMessage(),$known,true) ? $e->getMessage() : 'operation_failed';
    }
    private static function scalar(string $k): string { if (!is_string($_POST[$k]??null)) { throw new RuntimeException('invalid_job'); } return trim(wp_unslash($_POST[$k])); }
    public static function handle(string $action): void {
        self::guard($action); $notice='done'; try { if ($action==='zp_published_photo_start') { if (self::scalar('cost_ack')!=='1') { throw new RuntimeException('paid_confirmation'); } self::start(self::scalar('snapshot')); }
            elseif ($action==='zp_published_photo_retry') { self::retry(self::scalar('job'),self::scalar('paid_ack')==='1'); }
            elseif ($action==='zp_published_photo_apply') { if (self::scalar('review_ack')!=='1') { throw new RuntimeException('paid_confirmation'); } self::apply(self::scalar('job')); }
            elseif ($action==='zp_published_photo_reconcile') {self::reconcile(self::scalar('job'));}
            elseif (in_array($action,['zp_published_photo_rollback','zp_published_photo_recover'],true)) { self::restore(self::scalar('job')); }
            elseif ($action==='zp_published_photo_cancel') { self::cancel(self::scalar('job')); } else { throw new RuntimeException('invalid_job'); }
        } catch (Throwable $e) { $notice=self::safe_error($e); } wp_safe_redirect(add_query_arg(['page'=>'zp-suite-prepare','zp_published_photo_notice'=>$notice],admin_url('admin.php'))); exit;
    }
    private static function form(string $action,string $job=''): void { echo '<form method="post" action="'.esc_url(admin_url('admin-post.php')).'"><input type="hidden" name="action" value="'.esc_attr($action).'">'; wp_nonce_field($action); if ($job!=='') { echo '<input type="hidden" name="job" value="'.esc_attr($job).'">'; } }
    public static function render(): void {
        if (!self::allowed() || !is_admin() || !is_string($_GET['page']??null) || $_GET['page']!=='zp-suite-prepare') { return; } echo '<section class="notice zpAdmin20__card"><h2>Zdjęcia opublikowanego poradnika o logo na koszulce · #5788</h2><p>Dwie konkretne fotografie: porównanie nadruku oraz zbliżenie haftu. Podmiana zachowuje tekst, datę publikacji i SEO artykułu.</p>';
        $notice=$_GET['zp_published_photo_notice']??null; if (is_string($notice)) { echo '<p>'.esc_html($notice==='done'?'Operacja zapisana. Sprawdź stan poniżej.':self::label($notice)).'</p>'; }
        $active=false; foreach (self::data()['jobs']??[] as $j) { if (in_array($j['state']??'',['cancelled','rolled_back'],true)) { continue; } $active=true; echo '<p>Stan: '.esc_html(['queued'=>'W kolejce','running'=>'Generowanie jednego zdjęcia','ready'=>'Dwa zdjęcia gotowe; oczekują automatycznego zapisu','applied'=>'Zdjęcia zastosowane','applying'=>'Trwa zapis podmiany','attention'=>'Wymaga sprawdzenia'][$j['state']]??'Wymaga sprawdzenia').'</p>'; if (!empty($j['error'])) { echo '<p>'.esc_html(self::label($j['error'])).'</p>'; }
            if($j['state']==='attention')self::render_diagnostics($j['id']);
            if($j['state']==='applied'){$proof=self::verify_publication();echo '<p>'.esc_html(!empty($proof['verified'])?'Potwierdzono oryginalną publikację i zapis podmiany zdjęć. Daty oraz status są zachowane.':'Artykuł lub zapis podmiany zmienił się po operacji. Obecnej wersji nie nadpisujemy.').'</p>';}
            foreach (['cover','inline'] as $role) { if (!empty($j[$role.'_id'])) { $url=wp_get_attachment_url($j[$role.'_id']); if (is_string($url)) { echo '<a href="'.esc_url($url).'" target="_blank" rel="noopener">'.esc_html($role==='cover'?'Zobacz pierwsze zdjęcie':'Zobacz drugie zdjęcie').'</a> '; } } }
            if ($j['state']==='ready') { self::form('zp_published_photo_apply',$j['id']); echo '<input type="hidden" name="review_ack" value="1"><p class="zpAdmin20__muted">Kolejka zastosuje oba zdjęcia automatycznie. Możesz także dokończyć sam zapis bez ponownej generacji.</p><p><button class="button">Dokończ zapis zdjęć bez generacji</button></p></form>'; }
            if ($j['state']==='attention' && in_array($j['stage'],['cover','inline'],true)) { self::form('zp_published_photo_retry',$j['id']); echo '<input type="hidden" name="paid_ack" value="0"><label><input type="checkbox" name="paid_ack" value="1"> Świadomie ponawiam niepotwierdzoną płatną generację. Może to naliczyć koszt ponownie; istniejące media najpierw zostaną sprawdzone.</label><p><button class="button">Dokończ zapis lub ponów etap</button></p></form>'; }
            if($j['state']==='attention'&&!empty($j['proposal'])){self::form('zp_published_photo_reconcile',$j['id']);echo '<p>Zakończymy wyłącznie status podmiany, jeżeli oba zdjęcia są już zapisane dokładnie według własnej propozycji.</p><p><button class="button">Zakończ zapis potwierdzonych zdjęć bez kosztu</button></p></form>';}
            if ($j['state']==='applied' || (!empty($j['proposal']) && in_array($j['state'],['attention','applying'],true))) { self::form($j['state']==='applied'?'zp_published_photo_rollback':'zp_published_photo_recover',$j['id']); echo '<p><button class="button">Przywróć poprzednie zdjęcia bez kosztu</button></p></form>'; }
            elseif (in_array($j['state'],['queued','ready','attention'],true)) { self::form('zp_published_photo_cancel',$j['id']); echo '<p><button class="button">Anuluj tylko to zadanie zdjęć</button></p></form>'; }
        }
        if (!$active) { try { $s=self::inspect(); self::form('zp_published_photo_start'); echo '<input type="hidden" name="snapshot" value="'.esc_attr($s['sha']).'"><label><input type="checkbox" name="cost_ack" value="1" required> Zatwierdzam dwie płatne generacje realistycznych zdjęć dla tego jednego poradnika. Po przygotowaniu obu zdjęć kolejka zastosuje je automatycznie, zachowując tekst, datę i SEO.</label><p><button class="button">Przygotuj dwa zdjęcia dla #5788</button></p></form>'; } catch (Throwable $e) { echo '<p>'.esc_html(self::label(self::safe_error($e))).'</p>'; } }
        echo '</section>';
    }
    private static function diagnostic_difference($expected,$actual): array {
        if(!is_string($expected)||!is_string($actual))return ['available'=>false];$a=strlen($expected);$b=strlen($actual);if($expected===$actual)return ['available'=>true,'equal'=>true,'expected_bytes'=>$a,'current_bytes'=>$b];
        $limit=min($a,$b,800000);$at=0;while($at<$limit&&$expected[$at]===$actual[$at])$at++;
        $kind=static function(string $s,int $offset):string {if($offset>=strlen($s))return 'end';$n=ord($s[$offset]);if($n>127)return 'utf8_byte';if($n<32||$n===127)return 'control';if($n===32)return 'space';if(ctype_alpha($s[$offset]))return 'letter';if(ctype_digit($s[$offset]))return 'digit';return match($s[$offset]){'\\'=>'backslash','/'=>'slash','"'=>'double_quote',"'"=>'single_quote','<'=>'tag_open','>'=>'tag_close','='=>'equals',default=>'punctuation'};};
        $shape=static function(string $s,int $offset):string {$start=max(0,$offset-8);$chunk=substr($s,$start,20);$out='';for($i=0;$i<strlen($chunk);$i++){$c=$chunk[$i];$n=ord($c);$out.=$n>127?'U':($n<32||$n===127?'C':(ctype_alpha($c)?'L':(ctype_digit($c)?'D':$c)));}return $out;};
        return ['available'=>true,'equal'=>false,'expected_bytes'=>$a,'current_bytes'=>$b,'first_byte_offset'=>$at,'scan_limited'=>$at===800000&&$limit===800000,'expected_kind'=>$kind($expected,$at),'current_kind'=>$kind($actual,$at),'expected_shape'=>$shape($expected,$at),'current_shape'=>$shape($actual,$at)];
    }
    /** Exact persisted proposal comparisons only. Never calls apply, restore, finish_media or provider. */
    public static function diagnostics(string $jobid): array {
        if(!self::allowed())return [];$job=self::data()['jobs'][$jobid]??null;if(!is_array($job)||($job['id']??null)!==$jobid||($job['state']??'')!=='attention')return [];
        return self::proposal_diagnostics($job);
    }
    private static function proposal_diagnostics(array $job): array {
        try{$s=self::snapshot($job);self::refresh_post();$post=get_post(self::POST_ID);if(!$post)return [];$fields=self::fields(self::POST_ID);$same=[];foreach($s['fields'] as $key=>$value)$same[$key]=($fields[$key]??null)===$value;$other=self::other_meta(self::POST_ID);$names=[];foreach(array_slice(array_unique(array_merge(array_keys($s['other_meta']),array_keys($other))),0,100) as $key){if(count($names)>=100)break;if(is_string($key)&&strlen($key)<=190&&preg_match('/^[A-Za-z0-9_-]+$/D',$key)&&($s['other_meta'][$key]??null)!==($other[$key]??null))$names[]=$key;}
            $out=['post_id'=>self::POST_ID,'native_fields_same'=>$same,'other_meta_same'=>$other===$s['other_meta'],'other_meta_changed_keys'=>$names,'other_meta_scan_limited'=>count(array_unique(array_merge(array_keys($s['other_meta']),array_keys($other))))>100,'proposal_saved'=>!empty($job['proposal']),'proposal_valid'=>false,'proposal_code'=>'not_saved','photo_fields'=>[],'publication_current'=>false];$core=self::core_job();try{self::publication($post,$core);$out['publication_current']=true;}catch(Throwable $ignored){}if(empty($job['proposal']))return $out;
            try{$p=self::saved_proposal($job);}catch(Throwable $ignored){$out['proposal_code']='invalid_seal';return $out;}
            if(!is_string($p['html']??null)||strlen($p['html'])>400000||!is_array($p['meta']??null)||!is_array($p['core']??null)||!is_array($p['receipt']??null)){$out['proposal_code']='invalid_shape';return $out;}
            $media=[];foreach(['cover','inline'] as $role){$id=$job[$role.'_id']??null;if(!is_int($id)||$id<1||($p['core'][$role.'_id']??null)!==$id){$out['proposal_code']='invalid_media';return $out;}wp_cache_delete($id,'posts');wp_cache_delete($id,'post_meta');foreach(['job'=>$job['id'],'role'=>$role,'source_sha'=>$s['sha'],'model'=>$job['config']['model']] as $key=>$value)if(get_post_meta($id,self::PREFIX.$key,true)!==$value){$out['proposal_code']='invalid_media';return $out;}if(get_post_mime_type($id)!=='image/webp'){$out['proposal_code']='invalid_media';return $out;}$scene=$s['scene_plan'][$role];$media[]=['id'=>$id,'url'=>self::media_url($id),'alt'=>$scene['alt'],'caption'=>$scene['caption']];}
            if($media[0]['id']===$media[1]['id']){$out['proposal_code']='invalid_media';return $out;}$html=ZP_Article_Photo_Refresh_20261010::replace($s['html'],$s['slots'],$media);$meta=$s['photo_meta'];$meta['_elementor_data']=ZP_Article_Photo_Refresh_20261010::replace_widget($meta['_elementor_data'],$s['widget'],ZP_Content_Preparation_20261010::style().$html);$meta['_thumbnail_id']=(string)$media[0]['id'];$meta['rank_math_facebook_image']=$media[0]['url'];$meta['rank_math_facebook_image_id']=(string)$media[0]['id'];$ready=self::hash_with($s,$html,$meta);$meta[ZP_Content_Preparation_20261010::PREFIX.'ready_hash']=$ready;
            $receipt=$p['receipt'];if(($receipt['adapter_job']??null)!==$job['id']||($receipt['snapshot_sha']??null)!==$s['sha']||($receipt['original_attempt_sha']??null)!==self::sha($s['prepared_job']['publish_attempt'])||($receipt['before_hash']??null)!==$s['before_hash']||($receipt['after_hash']??null)!==$ready||!is_int($receipt['changed_at']??null)||$receipt['changed_at']<1||$receipt['changed_at']>time()+300||count($receipt)!==6){$out['proposal_code']='invalid_receipt';return $out;}
            $expectedCore=$s['prepared_job'];foreach(['cover','inline'] as $i=>$role){$expectedCore[$role.'_id']=$media[$i]['id'];$expectedCore['article'][$role.'_alt']=$media[$i]['alt'];}$expectedCore['ready_hash']=$ready;$expectedCore['publication_photo_rebase']=$receipt;
            if($p['html']!==$html||$p['meta']!==$meta||$p['core']!==$expectedCore){$out['proposal_code']='invalid_transform';return $out;}$out['proposal_valid']=true;$out['proposal_code']='valid';$classify=static fn($actual,$old,$new)=>['state'=>$actual===$old?'old':($actual===$new?'own_new':'other'),'old_equal'=>$actual===$old,'own_new_equal'=>$actual===$new];
            $row=$classify($post->post_content,$s['html'],$html);$row['from_original']=self::diagnostic_difference($s['html'],$post->post_content);$row['from_proposal']=self::diagnostic_difference($html,$post->post_content);$out['photo_fields']['body']=$row;foreach($s['photo_meta'] as $key=>$old){$actual=get_post_meta(self::POST_ID,$key,true);$row=$classify($actual,$old,$meta[$key]);if($key==='_elementor_data'){$row['from_original']=self::diagnostic_difference($old,$actual);$row['from_proposal']=self::diagnostic_difference($meta[$key],$actual);}$out['photo_fields'][$key]=$row;}$out['core_job']=$classify($core,$s['prepared_job'],$expectedCore);$out['receipt']=$classify(get_post_meta(self::POST_ID,self::PREFIX.'receipt',true),'',$receipt);$out['actual_ready_fingerprint_same']=ZP_Content_Preparation_20261010::fingerprint(self::POST_ID)===$ready;$out['original_publication_attempt_same']=($core['publish_attempt']??null)===$s['prepared_job']['publish_attempt'];
            try{$out['body_nonphoto_shape']=ZP_Article_Photo_Refresh_20261010::untouched_signature($post->post_content)===ZP_Article_Photo_Refresh_20261010::untouched_signature($s['html']);}catch(Throwable $ignored){$out['body_nonphoto_shape']=false;}return $out;
        }catch(Throwable $ignored){return [];}
    }
    private static function render_diagnostics(string $id): void {
        $d=self::diagnostics($id);if(!$d)return;$states=['old'=>'oryginalna wartość','own_new'=>'zgodne z zapisaną własną propozycją','other'=>'inna wartość'];$labels=['body'=>'Treść wpisu','_elementor_data'=>'HTML / drzewo Elementora','_thumbnail_id'=>'Obraz wyróżniający','rank_math_facebook_image'=>'Obraz udostępniania','rank_math_facebook_image_id'=>'ID obrazu udostępniania','_zp_prepared_20261010_ready_hash'=>'Zapis gotowości'];
        echo '<details><summary>Diagnostyka zapisu zdjęć · #5788</summary><p>Wyłącznie odczyt. Nie zatwierdza podmiany, przywracania ani ponowienia generacji.</p><p>Pola wpisu poza treścią: '.(!in_array(false,$d['native_fields_same'],true)?'zgodne':'różnią się').'. Metadane poza zdjęciami: '.($d['other_meta_same']?'zgodne':'różnią się').'. Potwierdzenie statusu i daty publikacji: '.($d['publication_current']?'zgodne':'różni się').'.</p>';
        $job=self::data()['jobs'][$id]??[];$allowedStages=['validate','native_content','meta_elementor','meta_thumbnail','meta_social','meta_social_id','meta_ready_hash','core_job','photo_receipt','native_readback','fingerprint','complete','receipt','readback','state_commit'];foreach(['last_apply'=>'Ostatni zapis','last_reconcile'=>'Zakończenie statusu'] as $prefix=>$label){$stage=$job[$prefix.'_stage']??'';$error=$job[$prefix.'_error']??'';if(is_string($stage)&&in_array($stage,$allowedStages,true)&&is_string($error)&&preg_match('/^[a-z_0-9]{1,60}$/D',$error))echo '<p>'.esc_html($label).': '.esc_html($stage).' · '.esc_html($error).'.</p>';}
        foreach($d['native_fields_same'] as $key=>$same)if(!$same)echo '<p>Różni się pole: <code>'.esc_html($key).'</code>.</p>';if($d['other_meta_changed_keys'])echo '<p>Różniące się klucze metadata (bez wartości): <code>'.esc_html(implode(', ',$d['other_meta_changed_keys'])).'</code>.</p>';
        echo '<p>Kontrola zapisanej własnej propozycji: <b>'.($d['proposal_valid']?'potwierdzona':'niepotwierdzona').'</b> · '.esc_html($d['proposal_code']).'.</p>';if($d['proposal_valid']){echo '<ul>';foreach($d['photo_fields'] as $key=>$row){echo '<li>'.esc_html($labels[$key]??$key).': <b>'.esc_html($states[$row['state']]).'</b>';foreach(['from_original'=>'oryginałem','from_proposal'=>'propozycją'] as $direction=>$label){$diff=$row[$direction]??[];if(!empty($diff['available'])&&empty($diff['equal']))echo '<p>Pierwsza różnica z '.esc_html($label).': bajt '.(int)$diff['first_byte_offset'].' · '.esc_html($diff['expected_kind']).' → '.esc_html($diff['current_kind']).'. Rozmiar: '.(int)$diff['expected_bytes'].' → '.(int)$diff['current_bytes'].' bajtów.<br>Maska (L=litera, D=cyfra, U=bajt UTF-8, C=znak sterujący): <code>'.esc_html($diff['expected_shape']).'</code> → <code>'.esc_html($diff['current_shape']).'</code>.</p>';}echo '</li>';}echo '</ul><p>Aktualna pełna kontrola gotowości: '.(!empty($d['actual_ready_fingerprint_same'])?'zgodna':'różni się').'. Pierwotny zapis publikacji: '.(!empty($d['original_publication_attempt_same'])?'zgodny':'różni się').'.</p><p>Zapis własnego zadania: '.esc_html($states[$d['core_job']['state']]).'. Zapis potwierdzenia podmiany: '.esc_html($states[$d['receipt']['state']]).'. Struktura treści poza zdjęciami: '.(!empty($d['body_nonphoto_shape'])?'zgodna':'różni się').'.</p>';}echo '</details>';
    }
    private static function label(string $code): string {
        return ['image_ambiguous'=>'Nie potwierdzono wyniku płatnego żądania. Niczego nie ponawiamy automatycznie; sprawdź zapisane media i rozliczenie.','post_changed'=>'Treść lub metadane zmieniły się. Podmianę zatrzymano; ręcznych zmian nie nadpisujemy.','publication_changed'=>'Status, data lub potwierdzenie publikacji zmieniły się. Podmiana nie może być kontynuowana.','ownership_changed'=>'Zapis generatora lub autorstwo wpisu nie odpowiada przygotowanemu podglądowi.','verify_models'=>'Sprawdź dostęp do modelu zdjęć w Przygotowaniu treści.','connection_changed'=>'Połączenie lub model zmieniły się od podglądu.','photo_active'=>'Trwa inne zadanie zdjęć tego artykułu.','outside_recent_period'=>'Artykuł nie mieści się w zakresie ostatnich14 dni.','static_seo_image'=>'Nietypowe odwołanie SEO do zdjęcia wymaga sprawdzenia przed kosztem.','reconciliation_not_exact'=>'Artykuł lub media nie odpowiadają w całości zapisanej własnej propozycji. Niczego nie zatwierdzono.','reconciliation_attention'=>'Dokończenie statusu wymaga sprawdzenia. Treści i zdjęć nie zmieniono.','apply_attention'=>'Zapis podmiany wymaga kontroli. Możesz przywrócić poprzednie zdjęcia; niczego nie publikujemy ponownie.','metadata_failed'=>'Nie potwierdzono pełnego zapisu metadanych zdjęcia. Znane media można dokończyć bez kolejnej generacji.','media_changed'=>'Zdjęcie lub jego opis zmieniono ręcznie. Nie nadpisujemy tej zmiany.','attempt_limit'=>'Osiągnięto limit trzech świadomych prób dla tego zdjęcia.','paid_confirmation'=>'Brakuje świadomego potwierdzenia kosztu.','recover_first'=>'Najpierw przywróć przerwaną podmianę zdjęć.','storage_failed'=>'WordPress nie potwierdził zapisu. Operację zatrzymano.','webp_encoder_unavailable'=>'Brakuje modułu optymalizacji zdjęć. Generacji nie uruchomiono.','webp_weight_limit'=>'Zdjęcie wymaga sprawdzenia rozmiaru pliku przed zastosowaniem.','webp_editor'=>'WordPress nie udostępnił edytora potrzebnego do optymalizacji zdjęcia.','busy'=>'Trwa inny etap przygotowania treści.','forbidden'=>'Brak wymaganych uprawnień.'][$code]??'Operacja wymaga sprawdzenia: '.sanitize_key($code);
    }
    public static function register(): void {
        foreach (['zp_published_photo_start','zp_published_photo_retry','zp_published_photo_apply','zp_published_photo_reconcile','zp_published_photo_rollback','zp_published_photo_recover','zp_published_photo_cancel'] as $a) { add_action('admin_post_'.$a,static function()use($a) { self::handle($a); }); }
        add_action('admin_init',[self::class,'resume_pending']); add_action(self::WORKER,[self::class,'worker']); add_action(self::WATCHDOG,[self::class,'watchdog']); add_action('admin_notices',[self::class,'render']);
    }
}
