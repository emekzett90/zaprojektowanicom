<?php
/** Nonpaid, reversible compression of existing, explicitly scoped Prepare images. */
if (!defined('ABSPATH')) { exit; }

final class ZP_Prepared_Image_Compression_20261010 {
    public const DATA = 'zps_prepared_image_compression_20261010_data';
    public const PREFIX = '_zp_prepared_image_compression_20261010_';
    public const PILOT = 5943;
    private const TARGETS = [5943,5951,5955,5957,5959,5961,5963,5965,5967,5969,5831,5833,5835,5839,5841,5843,5845,5847,5849];
    private const NEW_TARGETS = [5951,5955,5957,5959,5961,5963,5965,5967,5969,5831,5833,5835,5839,5841,5843,5845,5847,5849];
    private const IDS = [5943,5951,5955,5957,5959,5961,5963,5965,5967,5969,5831,5833,5835,5839,5841,5843,5845,5847,5849,5788,5792,5837];
    private const PHOTO_META = ['_elementor_data','_thumbnail_id','rank_math_facebook_image','rank_math_facebook_image_id','_zp_prepared_20261010_ready_hash'];
    private const EPHEMERAL = ['_edit_lock','_edit_last','_elementor_css','_elementor_element_cache','_elementor_page_assets','_zp_post_reads','_zp_post_views'];
    private const CODES = ['forbidden','scope_excluded','pilot_only','already_compressed','future_cron_changed','missing_post','ownership_changed','state_not_ready','post_changed','images_changed','source_file_changed','unsupported_article','static_seo_image','photo_active','future_too_close','unsupported_publication','source_too_large','already_light','storage_failed','storage_limit','metadata_failed','media_changed','encode_failed','encode_too_large','invalid_input','busy','recovery_required','no_operation','rollback_changed'];

    public static function allowed(int $id=0): bool {
        return class_exists('ZP_Content_Preparation_20261010') && class_exists('ZP_Managed_WebP_20261010') && class_exists('ZP_Article_Photo_Refresh_20261010')
            && current_user_can('manage_options') && current_user_can('edit_posts') && current_user_can('upload_files') && current_user_can('unfiltered_html')
            && ($id ? (in_array($id,self::IDS,true) && current_user_can('edit_post',$id)) : current_user_can('edit_post',self::PILOT));
    }
    private static function fresh(int $id): void { wp_cache_delete($id,'posts'); wp_cache_delete($id,'post_meta'); }
    private static function read_option(string $name,$default=[]) { wp_cache_delete($name,'options'); wp_cache_delete('notoptions','options'); return get_option($name,$default); }
    private static function sha($data): string { return hash('sha256',wp_json_encode($data)); }
    private static function code(Throwable $e): string { return $e instanceof RuntimeException && in_array($e->getMessage(),self::CODES,true) ? $e->getMessage() : 'post_changed'; }
    private static function core_job(int $id): array {
        $d=self::read_option(ZP_Content_Preparation_20261010::DATA,[]); $jobs=is_array($d)?($d['jobs']??null):null;
        if (!is_array($jobs) || count($jobs)>500) throw new RuntimeException('ownership_changed'); $hits=[];
        foreach($jobs as $key=>$j) if(is_array($j) && ($j['post_id']??null)===$id) { if(($j['id']??null)!==$key)throw new RuntimeException('ownership_changed'); $hits[]=$j; }
        if(count($hits)!==1)throw new RuntimeException('ownership_changed'); return $hits[0];
    }
    private static function fields(int $id): array {
        $p=get_post($id);if(!$p)throw new RuntimeException('missing_post');$out=[];
        foreach(['ID','post_type','post_status','post_title','post_name','post_excerpt','post_author','post_password','post_date','post_date_gmt','post_modified','post_modified_gmt','post_parent','menu_order','comment_status','ping_status'] as $key)$out[$key]=$p->$key??null;
        $out['categories']=wp_get_post_categories($id);sort($out['categories']);$out['future_cron']=wp_next_scheduled('publish_future_post',[$id]);return$out;
    }
    private static function other_meta(int $id): array {
        $v=get_post_meta($id);if(!is_array($v))throw new RuntimeException('post_changed');$out=[];
        foreach($v as $k=>$values){if(!is_string($k)||in_array($k,array_merge(self::PHOTO_META,self::EPHEMERAL),true))continue;$out[$k]=$values;}ksort($out);return$out;
    }
    private static function url(int $id): string {
        $v=wp_get_attachment_url($id);$u=is_string($v)?parse_url($v):false;$h=parse_url(home_url('/'));
        if(!is_array($u)||($u['scheme']??'')!=='https'||strtolower($u['host']??'')!==strtolower($h['host']??'')||!str_contains($u['path']??'','/wp-content/uploads/')||array_intersect(['query','fragment','user','pass','port'],array_keys($u)))throw new RuntimeException('images_changed');return$v;
    }
    /** Current stored-file provenance; legacy uploads have no provider byte hash and we never invent one. */
    private static function source_media(array $job,string $role): array {
        $id=$job[$role.'_id']??null;if(!is_int($id)||$id<1)throw new RuntimeException('images_changed');self::fresh($id);$p=get_post($id);$m=wp_get_attachment_metadata($id);$file=get_attached_file($id);$uploads=wp_get_upload_dir();$base=realpath($uploads['basedir']??'');$real=is_string($file)?realpath($file):false;
        $prefix=ZP_Content_Preparation_20261010::PREFIX;$stem='zp-poradnik-'.substr($job['id'],0,12).'-'.$role;
        if(!$p||$p->post_type!=='attachment'||$p->post_status!=='inherit'||get_post_mime_type($id)!=='image/webp'||(int)$p->post_author!==(int)($job['actor']??0)||!is_array($m)||(int)($m['width']??0)!==1536||(int)($m['height']??0)!==1024||!is_string($file)||is_link($file)||!$real||!$base||!str_starts_with($real,$base.DIRECTORY_SEPARATOR)||!is_readable($real)||!preg_match('~^'.preg_quote($stem,'~').'(?:-\d+)?\.webp$~D',basename($real)))throw new RuntimeException('images_changed');
        foreach(['job'=>$job['id'],'role'=>$role,'model'=>$job['config']['image_model']??null,'generated'=>'1']as$k=>$expected)if(!is_scalar($expected)||(string)get_post_meta($id,$prefix.$k,true)!==(string)$expected)throw new RuntimeException('images_changed');
        $prompt=get_post_meta($id,$prefix.'prompt',true);if(!is_string($prompt)||strlen($prompt)<50||strlen($prompt)>20000)throw new RuntimeException('images_changed');
        $size=getimagesize($real);$bytes=filesize($real);if(!is_array($size)||($size['mime']??'')!=='image/webp'||$size[0]!==1536||$size[1]!==1024||!is_int($bytes)||$bytes<100||$bytes>10000000)throw new RuntimeException('source_too_large');
        $sha=hash_file('sha256',$real);$encoded=get_post_meta($id,$prefix.'encoded_sha256',true);if($encoded!==''&&(!is_string($encoded)||!hash_equals($encoded,$sha)))throw new RuntimeException('source_file_changed');
        $all=get_post_meta($id);if(!is_array($all))throw new RuntimeException('images_changed');ksort($all);$fields=[];foreach(['ID','post_type','post_status','post_title','post_content','post_excerpt','post_author','post_parent','post_date','post_date_gmt','post_modified','post_modified_gmt']as$k)$fields[$k]=$p->$k??null;
        return ['id'=>$id,'url'=>self::url($id),'file'=>$real,'sha'=>$sha,'bytes'=>$bytes,'metadata'=>$m,'all_meta'=>$all,'fields'=>$fields,'alt'=>get_post_meta($id,'_wp_attachment_image_alt',true),'signature'=>self::sha([$fields,$all,$real,$sha]),'provider_hash_present'=>is_string(get_post_meta($id,$prefix.'provider_sha256',true))&&preg_match('/^[a-f0-9]{64}$/D',get_post_meta($id,$prefix.'provider_sha256',true))===1];
    }
    private static function widgets($nodes,int $depth=0): array {
        if(!is_array($nodes)||$depth>20||count($nodes)>2000)throw new RuntimeException('unsupported_article');$hits=[];
        foreach($nodes as$n){if(!is_array($n))throw new RuntimeException('unsupported_article');if(($n['widgetType']??'')==='html'&&is_string($n['settings']['html']??null)&&str_contains($n['settings']['html'],'zpArticlePrepared'))$hits[]=$n['settings']['html'];if(is_array($n['elements']??null))$hits=array_merge($hits,self::widgets($n['elements'],$depth+1));}return$hits;
    }
    private static function no_photos(array $job,int $id): void {
        if(class_exists('ZP_Prepared_Photo_Refresh_20261010')){self::read_option(ZP_Prepared_Photo_Refresh_20261010::DATA,[]);if(ZP_Prepared_Photo_Refresh_20261010::active_for($job['id']))throw new RuntimeException('photo_active');}
        foreach(['zps_article_photos_20261010_state','zps_published_prepared_photos_20261010_data']as$name){$v=self::read_option($name,[]);foreach(is_array($v)?($v['jobs']??[]):[]as$j)if((int)($j['post_id']??$j['snapshot']['id']??0)===$id&&!in_array($j['state']??'',['cancelled','rolled_back'],true))throw new RuntimeException('photo_active');}
    }
    private static function static_refs(int $id,array $media): void {
        $urls=array_column($media,'url');$ids=array_map('strval',array_column($media,'id'));$refs=static function($v)use(&$refs,$urls,$ids):bool{if(is_scalar($v)){if(in_array((string)$v,$urls,true)||in_array((string)$v,$ids,true))return true;if(is_string($v)&&strlen($v)<800000&&preg_match('/^\s*[\[{]/',$v)){$a=json_decode($v,true);if(is_array($a))return$refs($a);}}elseif(is_array($v)){foreach($v as$part)if($refs($part))return true;}return false;};
        foreach(get_post_meta($id)as$k=>$v)if(str_starts_with((string)$k,'rank_math_')&&!in_array($k,['rank_math_facebook_image','rank_math_facebook_image_id'],true)&&$refs($v))throw new RuntimeException('static_seo_image');
    }
    /** Private complete source snapshot, only after native capability checks. No GET mutations or API calls. */
    public static function inspect(int $id): array {
        if(!self::allowed($id))throw new RuntimeException('forbidden');if(in_array($id,[5788,5792],true))throw new RuntimeException('scope_excluded');self::fresh($id);$p=get_post($id);$j=self::core_job($id);
        if(!$p||$p->post_type!=='post'||$p->post_password!==''||get_post_meta($id,ZP_Content_Preparation_20261010::PREFIX.'job',true)!==($j['id']??null)||get_post_meta($id,ZP_Content_Preparation_20261010::PREFIX.'batch',true)!==($j['batch']??null)||!is_string($j['id']??null)||!preg_match('/^[A-Za-z0-9_-]{1,80}$/D',$j['id']))throw new RuntimeException('ownership_changed');
        if(!in_array($p->post_status,['draft','future','publish'],true)||($p->post_status==='draft'&&($j['state']??'')!=='ready')||($p->post_status==='future'&&($j['state']??'')!=='scheduled')||($p->post_status==='publish'&&($j['state']??'')!=='published'))throw new RuntimeException('state_not_ready');
        self::no_photos($j,$id);$ready=ZP_Content_Preparation_20261010::fingerprint($id);if(!is_string($j['ready_hash']??null)||!hash_equals($j['ready_hash'],$ready)||get_post_meta($id,ZP_Content_Preparation_20261010::PREFIX.'ready_hash',true)!==$ready)throw new RuntimeException('post_changed');
        if($p->post_status!=='publish')ZP_Content_Preparation_20261010::quality($id,$j,false,true);
        elseif(!is_array($j['publish_attempt']??null)||($j['publish_attempt']['hash']??null)!==$ready||($j['publish_attempt']['local']??null)!==$p->post_date||($j['publish_attempt']['gmt']??null)!==$p->post_date_gmt||($j['publish_attempt']['slug']??null)!==$p->post_name||($j['publish_attempt']['canonical']??null)!==get_permalink($id)||wp_next_scheduled('publish_future_post',[$id])!==false)throw new RuntimeException('unsupported_publication');
        $raw=get_post_meta($id,'_elementor_data',true);$html=$p->post_content;if(!is_string($raw)||strlen($raw)>800000||!is_string($html)||strlen($html)>300000||self::widgets(json_decode($raw,true))!==[ZP_Content_Preparation_20261010::style().$html])throw new RuntimeException('unsupported_article');
        $media=[];foreach(['cover','inline']as$role)$media[$role]=self::source_media($j,$role);if($media['cover']['id']===$media['inline']['id']||(int)get_post_thumbnail_id($id)!==$media['cover']['id'])throw new RuntimeException('images_changed');
        $tags=new WP_HTML_Tag_Processor($html);$actual=[];while($tags->next_tag('IMG'))$actual[]=$tags->get_attribute('src');if($actual!==[$media['cover']['url'],$media['inline']['url']])throw new RuntimeException('images_changed');
        if(get_post_meta($id,'rank_math_facebook_image',true)!==$media['cover']['url']||(string)get_post_meta($id,'rank_math_facebook_image_id',true)!==(string)$media['cover']['id'])throw new RuntimeException('static_seo_image');self::static_refs($id,$media);
        $meta=[];foreach(self::PHOTO_META as$k)$meta[$k]=get_post_meta($id,$k,true);
        $hashmeta=[];foreach(self::HASH_META as$k)$hashmeta[$k]=get_post_meta($id,$k,true);$s=['hash_meta'=>$hashmeta,'hash_categories'=>wp_get_post_categories($id),'id'=>$id,'job'=>$j,'fields'=>self::fields($id),'other_meta'=>self::other_meta($id),'photo_meta'=>$meta,'html'=>$html,'widget'=>ZP_Content_Preparation_20261010::style().$html,'media'=>$media,'before_hash'=>$ready];$s['sha']=self::sha($s);return$s;
    }
    /** Only typed, bounded presentation values escape the private snapshot. */
    public static function inventory(): array {
        if(!self::allowed())return[];$out=[];
        foreach(self::IDS as$id){if(!self::allowed($id)){$out[]=['id'=>$id,'title'=>'','status'=>'unknown','state'=>null,'reviewed'=>null,'proof'=>false,'reason'=>'forbidden','images'=>[],'snapshot'=>null,'pilot'=>$id===self::PILOT];continue;}self::fresh($id);$p=get_post($id);$status=$p&&in_array($p->post_status,['draft','future','publish','private','pending','trash'],true)?$p->post_status:'missing';$r=['id'=>$id,'title'=>$p?substr((string)$p->post_title,0,300):'','status'=>$status,'state'=>null,'reviewed'=>null,'proof'=>false,'reason'=>null,'images'=>[],'snapshot'=>null,'pilot'=>$id===self::PILOT];
            try{$active=null;foreach(self::data()['operations']??[]as$existing)if(($existing['snapshot']['id']??null)===$id&&($existing['state']??'')==='applied')$active=$existing;if($active&&$id===self::PILOT){$r['compression_history_state']='applied';try{self::variant($active,true);$r['compression_history_matches_current']=true;}catch(Throwable$historyError){$r['compression_history_matches_current']=false;}$r=array_merge($r,self::pilot_readiness($active));$r['compressed']=true;$r['proof']=$r['compression_history_matches_current'];$r['reason']=$r['proof']?null:'post_changed';foreach($active['media']??[]as$role=>$m)if(in_array($role,['cover','inline'],true)&&is_array($m)&&is_int($m['id']??null)&&is_int($m['bytes']??null)&&$m['bytes']>0)$r['images'][$role]=['id'=>$m['id'],'bytes'=>$m['bytes'],'width'=>1536,'height'=>1024,'provider_hash_present'=>false];$out[]=$r;continue;}if($active){self::variant($active,true);$r['state']=$active['proposal']['job']['state'];$r['reviewed']=is_int($active['proposal']['job']['reviewed_at']??null)&&$active['proposal']['job']['reviewed_at']>0;$r['proof']=true;$r['compressed']=true;foreach($active['media']as$role=>$m)$r['images'][$role]=['id'=>$m['id'],'bytes'=>$m['bytes'],'width'=>1536,'height'=>1024,'provider_hash_present'=>false];$out[]=$r;continue;}$s=self::inspect($id);$r['state']=$s['job']['state'];$r['reviewed']=is_int($s['job']['reviewed_at']??null)&&$s['job']['reviewed_at']>0;$r['proof']=true;$r['snapshot']=in_array($id,self::NEW_TARGETS,true)&&in_array($r['status'],['draft','future'],true)?$s['sha']:null;foreach($s['media']as$role=>$m)$r['images'][$role]=['id'=>$m['id'],'bytes'=>$m['bytes'],'width'=>1536,'height'=>1024,'provider_hash_present'=>$m['provider_hash_present']];}catch(Throwable$e){$r['reason']=self::code($e);}$out[]=$r;
        }return$out;
    }
    /** Current pilot readiness is independent of the immutable historical compression proposal. No adoption or writes. */
    private static function pilot_readiness(array $op): array {
        $out=['state'=>null,'reviewed'=>null,'current_core_quality'=>'unavailable','current_core_ready'=>null,'current_review_recorded'=>null,'compressed_pair_unchanged'=>null];
        if(!self::allowed(self::PILOT))return$out;
        try{
            $s=self::snapshot($op);if($s['id']!==self::PILOT||($op['state']??null)!=='applied')return$out;self::fresh(self::PILOT);$p=get_post(self::PILOT);$j=self::core_job(self::PILOT);
            if(!$p||$p->post_type!=='post'||$p->post_password!==''||!in_array($p->post_status,['draft','future'],true)){$out['current_core_quality']='not_a_draft';return$out;}
            if(($p->post_status==='draft'&&($j['state']??null)!=='ready')||($p->post_status==='future'&&($j['state']??null)!=='scheduled')||!is_string($j['id']??null)||get_post_meta(self::PILOT,ZP_Content_Preparation_20261010::PREFIX.'job',true)!==$j['id']||!is_string($j['batch']??null)||get_post_meta(self::PILOT,ZP_Content_Preparation_20261010::PREFIX.'batch',true)!==$j['batch']){$out['current_core_quality']='ownership_changed';return$out;}
            $out['state']=$j['state'];$out['reviewed']=$out['current_review_recorded']=is_int($j['reviewed_at']??null)&&$j['reviewed_at']>0;
            $actual=ZP_Content_Preparation_20261010::fingerprint(self::PILOT);$jobHash=$j['ready_hash']??null;$metaHash=get_post_meta(self::PILOT,ZP_Content_Preparation_20261010::PREFIX.'ready_hash',true);
            $valid=static fn($v)=>is_string($v)&&preg_match('/^[a-f0-9]{64}$/D',$v)===1;$out['current_core_ready']=$valid($actual)&&$valid($jobHash)&&$valid($metaHash)?hash_equals($actual,$jobHash)&&hash_equals($actual,$metaHash):null;
            try{ZP_Content_Preparation_20261010::quality(self::PILOT,$j,false,$p->post_status==='future');$out['current_core_quality']='pass';}catch(Throwable$qualityError){$codes=['draft_changed','actor_permissions','metadata_incomplete','draft_invalid','draft_links','draft_images','article_word_count','article_faq','canonical_failed','invalid_template','invalid_template_css'];$out['current_core_quality']=$qualityError instanceof RuntimeException&&in_array($qualityError->getMessage(),$codes,true)?$qualityError->getMessage():'unavailable';}
            try{
                $media=$op['media']??null;if(!is_array($media)||!is_string($p->post_content))throw new RuntimeException('media_changed');$urls=[];
                foreach(['cover','inline']as$role){$m=$media[$role]??null;if(!is_array($m)||!is_int($m['id']??null)||!is_string($m['signature']??null)||!preg_match('/^[a-f0-9]{64}$/D',$m['signature'])||($j[$role.'_id']??null)!==$m['id']||!hash_equals($m['signature'],self::new_signature($m['id'])))throw new RuntimeException('media_changed');$urls[]=self::url($m['id']);}
                $parser=new WP_HTML_Tag_Processor($p->post_content);$actualUrls=[];while($parser->next_tag('IMG'))$actualUrls[]=$parser->get_attribute('src');
                $out['compressed_pair_unchanged']=$actualUrls===$urls&&(int)get_post_thumbnail_id(self::PILOT)===$media['cover']['id']&&get_post_meta(self::PILOT,'rank_math_facebook_image',true)===$urls[0]&&(string)get_post_meta(self::PILOT,'rank_math_facebook_image_id',true)===(string)$media['cover']['id'];
            }catch(Throwable$ignored){$out['compressed_pair_unchanged']=false;}
        }catch(Throwable$ignored){}
        return$out;
    }
    private const HASH_META = ['_elementor_edit_mode','_elementor_template_type','_elementor_version','_elementor_pro_version','_elementor_page_settings','_wp_page_template','_elementor_data','_thumbnail_id','rank_math_title','rank_math_description','rank_math_focus_keyword','rank_math_canonical_url','rank_math_robots','rank_math_schema_Article','_zp_prepared_20261010_faq'];
    private static function save(array $d): void {
        if(strlen(wp_json_encode($d))>16000000)throw new RuntimeException('storage_limit');update_option(self::DATA,$d,false);if(self::read_option(self::DATA,null)!==$d)throw new RuntimeException('storage_failed');
    }
    public static function data(): array {if(!self::allowed())return[];$d=self::read_option(self::DATA,[]);if(!is_array($d)||count($d['operations']??[])>50)throw new RuntimeException('storage_failed');return$d;}
    private static function store_operation(array $op): void {
        $d=self::data();$old=$d['operations'][$op['id']]??null;if($old && (($old['snapshot']['sha']??null)!==$op['snapshot']['sha'] || ($old['actor']??null)!==$op['actor']))throw new RuntimeException('storage_failed');$d['operations'][$op['id']]=$op;self::save($d);
    }
    private static function snapshot(array $op): array {
        $s=$op['snapshot']??null;if(!is_array($s)||!is_int($s['id']??null)||!in_array($s['id'],self::TARGETS,true)||!is_string($s['sha']??null))throw new RuntimeException('storage_failed');$copy=$s;unset($copy['sha']);if(!hash_equals($s['sha'],self::sha($copy)))throw new RuntimeException('storage_failed');return$s;
    }
    private static function operation(string $id): array {
        $op=self::data()['operations'][$id]??null;if(!is_array($op)||($op['id']??null)!==$id||!is_int($op['actor']??null)||$op['actor']!==get_current_user_id())throw new RuntimeException('no_operation');self::snapshot($op);return$op;
    }
    private static function original(array $s): void { if(!hash_equals($s['sha'],self::inspect($s['id'])['sha']))throw new RuntimeException('post_changed'); }
    private static function posted_id(): int {
        if(!isset($_POST['post_id']))return self::PILOT;$raw=self::scalar('post_id');if(!preg_match('/^[1-9]\d{0,7}$/D',$raw)||!in_array((int)$raw,self::TARGETS,true))throw new RuntimeException('invalid_input');return(int)$raw;
    }
    private static function deadline(array $fields): void {
        if(($fields['post_status']??null)==='draft')return;if(($fields['post_status']??null)!=='future')throw new RuntimeException('scope_excluded');$v=$fields['post_date_gmt']??null;
        if(!is_string($v))throw new RuntimeException('future_cron_changed');$d=DateTimeImmutable::createFromFormat('!Y-m-d H:i:s',$v,new DateTimeZone('UTC'));$e=DateTimeImmutable::getLastErrors();if(!$d||($e&&($e['warning_count']||$e['error_count']))||$d->format('Y-m-d H:i:s')!==$v||($fields['future_cron']??null)!==$d->getTimestamp())throw new RuntimeException('future_cron_changed');if($d->getTimestamp()<time()+600)throw new RuntimeException('future_too_close');
    }
    private static function actor(array $op): void {
        $s=self::snapshot($op);$id=$s['id'];if(!self::allowed($id)||get_current_user_id()!==($op['actor']??null))throw new RuntimeException('forbidden');self::fresh($id);
        if(!in_array($s['fields']['post_status'],['draft','future'],true))throw new RuntimeException('scope_excluded');self::deadline(self::fields($id));
    }
    private static function source_unchanged(array $s): void {
        foreach(['cover','inline']as$role){$m=self::source_media($s['job'],$role);if(!hash_equals($s['media'][$role]['signature'],$m['signature']))throw new RuntimeException('source_file_changed');}
    }
    /** A new attachment is never marked as a second original for core media_for(job,role). */
    private static function new_signature(int $id): string {
        self::fresh($id);$p=get_post($id);$file=get_attached_file($id);$real=is_string($file)?realpath($file):false;$base=realpath(wp_get_upload_dir()['basedir']??'');
        if(!$p||$p->post_type!=='attachment'||$p->post_status!=='inherit'||get_post_mime_type($id)!=='image/webp'||!is_string($file)||is_link($file)||!$real||!$base||!str_starts_with($real,$base.DIRECTORY_SEPARATOR)||!is_readable($real)||filesize($real)>ZP_Managed_WebP_20261010::MAX_BYTES)throw new RuntimeException('media_changed');
        $fields=[];foreach(['ID','post_type','post_status','post_title','post_content','post_excerpt','post_author','post_parent','post_date','post_date_gmt','post_modified','post_modified_gmt']as$k)$fields[$k]=$p->$k??null;$meta=get_post_meta($id);if(!is_array($meta))throw new RuntimeException('media_changed');ksort($meta);return self::sha([$fields,$meta,self::url($id),$real,hash_file('sha256',$real)]);
    }
    private static function staged_metadata(int $id,string $file): array {
        $present=metadata_exists('post',$id,'_wp_attachment_metadata');$before=get_post_meta($id,'_wp_attachment_metadata',true);$attached=get_attached_file($id);
        $capture=static function($check,$objectId,$key,$value,$previous)use($id){return(int)$objectId===$id&&in_array($key,['_wp_attachment_metadata','_wp_attached_file'],true)?true:$check;};
        $threshold=static function($value,$size,$path,$objectId)use($id){return(int)$objectId===$id?false:$value;};add_filter('update_post_metadata',$capture,PHP_INT_MAX,5);add_filter('big_image_size_threshold',$threshold,PHP_INT_MAX,4);
        try{$m=wp_generate_attachment_metadata($id,$file);}finally{remove_filter('update_post_metadata',$capture,PHP_INT_MAX);remove_filter('big_image_size_threshold',$threshold,PHP_INT_MAX);self::fresh($id);}
        $base=realpath(wp_get_upload_dir()['basedir']??'');$real=realpath($file);$relative=$base&&$real&&str_starts_with($real,$base.DIRECTORY_SEPARATOR)?str_replace(DIRECTORY_SEPARATOR,'/',substr($real,strlen($base)+1)):null;
        if(metadata_exists('post',$id,'_wp_attachment_metadata')!==$present||get_post_meta($id,'_wp_attachment_metadata',true)!==$before||get_attached_file($id)!==$attached||!is_array($m)||($m['file']??null)!==$relative||isset($m['original_image'])||(int)($m['width']??0)!==1536||(int)($m['height']??0)!==1024||strlen(wp_json_encode($m))>200000)throw new RuntimeException('metadata_failed');return$m;
    }
    private static function meta_write(int $id,string $key,$v): void {update_post_meta($id,$key,wp_slash($v));self::fresh($id);$expected=is_scalar($v)?(string)$v:$v;if(get_post_meta($id,$key,true)!==$expected)throw new RuntimeException('metadata_failed');}
    private static function known_id(array $op,string $role): int {
        $ids=get_posts(['post_type'=>'attachment','post_status'=>'inherit','numberposts'=>2,'fields'=>'ids','meta_query'=>[['key'=>self::PREFIX.'operation','value'=>$op['id']],['key'=>self::PREFIX.'role','value'=>$role]]]);if(count($ids)>1)throw new RuntimeException('media_changed');$id=(int)($ids[0]??0);$stored=(int)($op['media'][$role]['id']??0);if($stored&&$stored!==$id)throw new RuntimeException('media_changed');return$id;
    }
    private static function finish_media(array &$op,string $role): array {
        self::actor($op);$s=self::snapshot($op);self::original($s);$src=$s['media'][$role];$r=$op['media'][$role]??[];$id=self::known_id($op,$role);
        if(!$r){try{$encoded=ZP_Managed_WebP_20261010::encode_file($src['file'],$src['sha'],1536,1024);}catch(Throwable$e){throw new RuntimeException($e instanceof RuntimeException && $e->getMessage()==='webp_weight_limit'?'encode_too_large':'encode_failed');}self::original($s);
            if(!is_string($encoded['bytes']??null)||($encoded['source_sha256']??null)!==$src['sha']||strlen($encoded['bytes'])>512000||strlen($encoded['bytes'])!==($encoded['byte_count']??null)||hash('sha256',$encoded['bytes'])!==($encoded['sha256']??null)||($encoded['width']??null)!==1536||($encoded['height']??null)!==1024||!in_array($encoded['quality']??null,[80,72,64],true))throw new RuntimeException('encode_failed');
            $r=['source_id'=>$src['id'],'source_sha'=>$src['sha'],'encoded_sha'=>$encoded['sha256'],'bytes'=>$encoded['byte_count'],'quality'=>$encoded['quality'],'encoder'=>$encoded['encoder'],'id'=>0];$op['media'][$role]=$r;self::store_operation($op);
        }
        if(!isset($r['file'])){
            // Local-only retry is safe: immutable source and exact encoding hash, with a hard upload-attempt bound.
            if(($r['uploads']??0)>=3)throw new RuntimeException('storage_limit');if(!isset($encoded)){try{$encoded=ZP_Managed_WebP_20261010::encode_file($src['file'],$src['sha'],1536,1024);}catch(Throwable$e){throw new RuntimeException('encode_failed');}}
            if(($r['source_id']??null)!==$src['id']||($r['source_sha']??null)!==$src['sha']||($encoded['sha256']??null)!==($r['encoded_sha']??null)||($encoded['byte_count']??null)!==($r['bytes']??null)||($encoded['quality']??null)!==($r['quality']??null)||($encoded['encoder']??null)!==($r['encoder']??null))throw new RuntimeException('media_changed');self::original($s);$r['uploads']=($r['uploads']??0)+1;$op['media'][$role]=$r;self::store_operation($op);
            $upload=wp_upload_bits('zp-compressed-'.$s['id'].'-'.substr(hash('sha256',$op['id']),0,12).'-'.$role.'.webp',null,$encoded['bytes']);if(!empty($upload['error'])||!is_string($upload['file']??null))throw new RuntimeException('metadata_failed');$r['file']=$upload['file'];$r['url']=$upload['url']??null;$op['media'][$role]=$r;self::store_operation($op);
        }
        $base=realpath(wp_get_upload_dir()['basedir']??'');$real=is_string($r['file']??null)?realpath($r['file']):false;$size=$real?getimagesize($real):false;
        if(!$base||!$real||!str_starts_with($real,$base.DIRECTORY_SEPARATOR)||!is_readable($real)||is_link($r['file'])||filesize($real)!==$r['bytes']||$r['bytes']>512000||!hash_equals($r['encoded_sha'],hash_file('sha256',$real))||!is_array($size)||($size['mime']??'')!=='image/webp'||$size[0]!==1536||$size[1]!==1024)throw new RuntimeException('media_changed');
        if(!$id){self::original($s);$f=$src['fields'];$meta=[self::PREFIX.'operation'=>$op['id'],self::PREFIX.'role'=>$role,self::PREFIX.'source_id'=>$src['id'],self::PREFIX.'stored_source_sha'=>$src['sha'],self::PREFIX.'encoded_sha'=>$r['encoded_sha'],self::PREFIX.'bytes'=>$r['bytes'],self::PREFIX.'quality'=>$r['quality'],self::PREFIX.'encoder'=>$r['encoder'],'_wp_attachment_image_alt'=>$src['alt']];
            $id=wp_insert_attachment(wp_slash(['post_mime_type'=>'image/webp','post_title'=>$f['post_title'],'post_content'=>$f['post_content'],'post_excerpt'=>$f['post_excerpt'],'post_status'=>'inherit','post_author'=>(int)$f['post_author'],'meta_input'=>$meta]),$r['file'],0,true);if(is_wp_error($id)||!is_int($id)||$id<1)throw new RuntimeException('metadata_failed');$op['media'][$role]['id']=$id;self::store_operation($op);$r=$op['media'][$role];
        }elseif(!$r['id']){$op['media'][$role]['id']=$id;self::store_operation($op);$r=$op['media'][$role];}
        self::fresh($id);$p=get_post($id);$f=$src['fields'];if(!$p||get_attached_file($id)!==$r['file']||$p->post_title!==$f['post_title']||$p->post_content!==$f['post_content']||$p->post_excerpt!==$f['post_excerpt']||(int)$p->post_author!==(int)$f['post_author']||(int)($p->post_parent??0)!==0||get_post_meta($id,'_wp_attachment_image_alt',true)!==$src['alt'])throw new RuntimeException('media_changed');
        foreach(['operation'=>$op['id'],'role'=>$role,'source_id'=>$src['id'],'stored_source_sha'=>$src['sha'],'encoded_sha'=>$r['encoded_sha'],'bytes'=>$r['bytes'],'quality'=>$r['quality'],'encoder'=>$r['encoder']]as$k=>$v)if((string)get_post_meta($id,self::PREFIX.$k,true)!==(string)$v)throw new RuntimeException('media_changed');
        if(isset($r['signature'])){if(!hash_equals($r['signature'],self::new_signature($id)))throw new RuntimeException('media_changed');return$r;}
        if(!isset($r['expected_metadata'])){if(metadata_exists('post',$id,'_wp_attachment_metadata'))throw new RuntimeException('media_changed');if(!function_exists('wp_generate_attachment_metadata'))require_once ABSPATH.'wp-admin/includes/image.php';$r['expected_metadata']=self::staged_metadata($id,$r['file']);$op['media'][$role]=$r;self::store_operation($op);}
        self::fresh($id);$present=metadata_exists('post',$id,'_wp_attachment_metadata');$current=get_post_meta($id,'_wp_attachment_metadata',true);if(($present&&$current!==$r['expected_metadata'])||(!$present&&$current!==''&&$current!==false))throw new RuntimeException('media_changed');wp_update_attachment_metadata($id,$r['expected_metadata']);self::fresh($id);if(wp_get_attachment_metadata($id)!==$r['expected_metadata'])throw new RuntimeException('metadata_failed');self::original($s);$r['url']=self::url($id);$r['signature']=self::new_signature($id);$op['media'][$role]=$r;self::store_operation($op);return$r;
    }
    private static function changed_html(string $html,array $s,array $media): string {
        $p=new WP_HTML_Tag_Processor($html);$count=0;foreach(['cover','inline']as$role){if(!$p->next_tag('IMG')||$p->get_attribute('src')!==$s['media'][$role]['url'])throw new RuntimeException('unsupported_article');$p->set_attribute('src',$media[$role]['url']);$count++;}if($p->next_tag('IMG'))throw new RuntimeException('unsupported_article');$out=$p->get_updated_html();
        $back=new WP_HTML_Tag_Processor($out);foreach(['cover','inline']as$role){if(!$back->next_tag('IMG')||$back->get_attribute('src')!==$media[$role]['url'])throw new RuntimeException('unsupported_article');$back->set_attribute('src',$s['media'][$role]['url']);}if($back->get_updated_html()!==$html)throw new RuntimeException('unsupported_article');return$out;
    }
    private static function expected_hash(array $s,string $html,array $photos): string {
        $meta=$s['hash_meta'];foreach(['_elementor_data','_thumbnail_id']as$k)$meta[$k]=$photos[$k];$f=$s['fields'];return self::sha([$f['post_title'],$f['post_name'],$html,$f['post_excerpt'],$f['post_author'],$f['post_password'],$s['hash_categories'],$meta]);
    }
    private static function proposal(array $op): array {
        $s=self::snapshot($op);$m=$op['media'];$html=self::changed_html($s['html'],$s,$m);$raw=ZP_Article_Photo_Refresh_20261010::replace_widget($s['photo_meta']['_elementor_data'],$s['widget'],ZP_Content_Preparation_20261010::style().$html);
        $photos=['_elementor_data'=>$raw,'_thumbnail_id'=>(string)$m['cover']['id'],'rank_math_facebook_image'=>$m['cover']['url'],'rank_math_facebook_image_id'=>(string)$m['cover']['id']];$ready=self::expected_hash($s,$html,$photos);$photos[ZP_Content_Preparation_20261010::PREFIX.'ready_hash']=$ready;$core=$s['job'];$core['cover_id']=$m['cover']['id'];$core['inline_id']=$m['inline']['id'];$core['ready_hash']=$ready;
        $core['stored_image_compression']=['operation'=>$op['id'],'snapshot_sha'=>$s['sha'],'before_hash'=>$s['before_hash'],'after_hash'=>$ready,'source_ids'=>[$s['media']['cover']['id'],$s['media']['inline']['id']],'attachment_ids'=>[$m['cover']['id'],$m['inline']['id']],'at'=>time()];return['html'=>$html,'photos'=>$photos,'job'=>$core,'ready_hash'=>$ready];
    }
    private static function variant(array $op,?bool $new=null): void {
        self::actor($op);$s=self::snapshot($op);$id=$s['id'];self::fresh($id);if(self::fields($id)!==$s['fields']||self::other_meta($id)!==$s['other_meta'])throw new RuntimeException('post_changed');self::source_unchanged($s);
        $p=$op['proposal']??null;if(!is_array($p))throw new RuntimeException('recovery_required');$current=get_post($id)->post_content;if($new===null?!in_array($current,[$s['html'],$p['html']],true):$current!==($new?$p['html']:$s['html']))throw new RuntimeException('post_changed');
        foreach(self::PHOTO_META as$k){$v=get_post_meta($id,$k,true);$old=$s['photo_meta'][$k];$next=$p['photos'][$k];if($new===null?!in_array($v,[$old,$next],true):$v!==($new?$next:$old))throw new RuntimeException('post_changed');}
        $j=self::core_job($id);if($new===null?!in_array($j,[$s['job'],$p['job']],true):$j!==($new?$p['job']:$s['job']))throw new RuntimeException('ownership_changed');
        foreach(['cover','inline']as$role){$r=$op['media'][$role];if(!hash_equals($r['signature']??'',self::new_signature($r['id'])))throw new RuntimeException('media_changed');}
    }
    private static function native_content(array $s,string $html): void {
        $id=$s['id'];$filter=static function($data,$postarr)use($s,$id){if((int)($postarr['ID']??0)===$id)foreach($s['fields']as$k=>$v)if(str_starts_with($k,'post_')||$k==='menu_order'||$k==='comment_status'||$k==='ping_status')$data[$k]=is_string($v)?wp_slash($v):$v;return$data;};add_filter('wp_insert_post_data',$filter,PHP_INT_MAX,2);
        try{$r=wp_update_post(wp_slash(['ID'=>$id,'post_content'=>$html]),true);if(is_wp_error($r)||$r!==$id)throw new RuntimeException('metadata_failed');}finally{remove_filter('wp_insert_post_data',$filter,PHP_INT_MAX);}self::fresh($id);
    }
    private static function write_core(array $op,bool $new): void {
        $s=self::snapshot($op);$p=$op['proposal'];$d=self::read_option(ZP_Content_Preparation_20261010::DATA,[]);$key=$s['job']['id'];if(!is_array($d)||!in_array($d['jobs'][$key]??null,[$s['job'],$p['job']],true))throw new RuntimeException('ownership_changed');$d['jobs'][$key]=$new?$p['job']:$s['job'];update_option(ZP_Content_Preparation_20261010::DATA,$d,false);if(self::read_option(ZP_Content_Preparation_20261010::DATA,null)!==$d)throw new RuntimeException('storage_failed');
    }
    private static function apply_operation(array &$op,bool $new): void {
        $s=self::snapshot($op);$id=$s['id'];self::variant($op);$p=$op['proposal'];$op['state']=$new?'applying':'restoring';$op['updated_at']=time();self::store_operation($op);
        self::variant($op);self::native_content($s,$new?$p['html']:$s['html']);foreach(self::PHOTO_META as$k){self::variant($op);self::meta_write($id,$k,$new?$p['photos'][$k]:$s['photo_meta'][$k]);}self::variant($op);self::write_core($op,$new);self::variant($op,$new);
        $job=$new?$p['job']:$s['job'];ZP_Content_Preparation_20261010::quality($id,$job,false,true);if(ZP_Content_Preparation_20261010::fingerprint($id)!==($new?$p['ready_hash']:$s['before_hash']))throw new RuntimeException('post_changed');
        foreach(['_elementor_css','_elementor_element_cache','_elementor_page_assets']as$k)delete_post_meta($id,$k);clean_post_cache($id);$op['state']=$new?'applied':'rolled_back';$op['error']='';$op['updated_at']=time();self::store_operation($op);
    }
    public static function compress(string $expected,int $post_id=self::PILOT): string {
        if(!self::allowed($post_id)||!in_array($post_id,self::TARGETS,true)||!preg_match('/^[a-f0-9]{64}$/D',$expected))throw new RuntimeException('forbidden');return ZP_Content_Preparation_20261010::exclusive(static function()use($expected,$post_id){
            $count=0;foreach(self::data()['operations']??[]as$op){$s=self::snapshot($op);if($s['id']!==$post_id)continue;$count++;if(($op['state']??'')==='applied'){self::variant($op,true);return$op['id'];}if(($op['state']??'')!=='rolled_back')throw new RuntimeException('recovery_required');}
            // Pilot5943 is terminal in42: never create a second pair or adopt later editorial changes.
            if($post_id===self::PILOT)throw new RuntimeException('already_compressed');if(!in_array($post_id,self::NEW_TARGETS,true))throw new RuntimeException('scope_excluded');
            $s=self::inspect($post_id);if(!in_array($s['fields']['post_status'],['draft','future'],true))throw new RuntimeException('scope_excluded');self::deadline($s['fields']);if(!hash_equals($s['sha'],$expected))throw new RuntimeException('post_changed');if(max(array_column($s['media'],'bytes'))<=512000)throw new RuntimeException('already_light');
            $d=self::data();if(count($d['operations']??[])>=50||$count>=5)throw new RuntimeException('storage_limit');$op=['id'=>wp_generate_uuid4(),'actor'=>get_current_user_id(),'snapshot'=>$s,'state'=>'preparing','media'=>[],'created_at'=>time(),'updated_at'=>time(),'error'=>''];self::store_operation($op);return self::continue_operation($op);
        });
    }
    private static function continue_operation(array $op): string {
        try{self::actor($op);if(!isset($op['proposal'])){foreach(['cover','inline']as$role)self::finish_media($op,$role);self::original(self::snapshot($op));$op['proposal']=self::proposal($op);$op['state']='ready';self::store_operation($op);}self::apply_operation($op,true);return$op['id'];}
        catch(Throwable$e){$op['error']=self::code($e);$op['state']='attention';$op['updated_at']=time();try{self::store_operation($op);}catch(Throwable$ignored){}throw new RuntimeException($op['error']);}
    }
    public static function recover(string $id): void {if(!self::allowed())throw new RuntimeException('forbidden');ZP_Content_Preparation_20261010::exclusive(static function()use($id){$op=self::operation($id);if($op['state']==='applied'){self::variant($op,true);return;}if($op['state']==='restoring'||!empty($op['restoring']))self::apply_operation($op,false);elseif(in_array($op['state'],['preparing','ready','applying','attention'],true))self::continue_operation($op);else throw new RuntimeException('no_operation');});}
    public static function rollback(string $id): void {if(!self::allowed())throw new RuntimeException('forbidden');ZP_Content_Preparation_20261010::exclusive(static function()use($id){$op=self::operation($id);if($op['state']==='rolled_back'){self::variant($op,false);return;}if(!isset($op['proposal']))throw new RuntimeException('recovery_required');self::variant($op,$op['state']==='applied'?true:null);$op['restoring']=true;self::store_operation($op);self::apply_operation($op,false);});}
    public static function guard(string $action): void {if(!self::allowed()||($_SERVER['REQUEST_METHOD']??'')!=='POST'||!is_string($_POST['_wpnonce']??null)||!wp_verify_nonce(wp_unslash($_POST['_wpnonce']),$action))wp_die('Brak uprawnień lub prawidłowego potwierdzenia.','',['response'=>403]);}
    private static function scalar(string $key): string {if(!is_string($_POST[$key]??null))throw new RuntimeException('invalid_input');return trim(wp_unslash($_POST[$key]));}
    public static function handler(): void {
        $action=is_string($_POST['action']??null)?wp_unslash($_POST['action']):'';if(!in_array($action,['zp_prep_compress_existing','zp_prep_compress_recover','zp_prep_compress_rollback'],true))wp_die('Nieprawidłowa operacja.','',['response'=>403]);self::guard($action);
        try{$post_id=self::posted_id();if($action==='zp_prep_compress_existing')self::compress(self::scalar('snapshot'),$post_id);else{$key=self::scalar('operation');if(self::snapshot(self::operation($key))['id']!==$post_id)throw new RuntimeException('invalid_input');if($action==='zp_prep_compress_recover')self::recover($key);else self::rollback($key);}$status=$action==='zp_prep_compress_rollback'?'rolled_back':'applied';}catch(Throwable$e){$status=self::code($e);}wp_safe_redirect(admin_url('admin.php?page=zp-suite-prepare&zp_compress='.$status));exit;
    }
    private static function controls(array $rows): void {
        $ops=self::data()['operations']??[];echo '<h3>Optymalizuj pojedynczy wpis bez API</h3>';
        foreach($rows as$row){$id=$row['id'];if(!in_array($id,self::TARGETS,true)||!self::allowed($id))continue;$active=null;foreach($ops as$existing)if(($existing['snapshot']['id']??null)===$id&&($existing['state']??'')!=='rolled_back')$active=$existing;
            if(is_array($active)){$op=$active;echo '<details><summary>#'.(int)$id.' · '.esc_html(['preparing'=>'przygotowanie lżejszych kopii','ready'=>'lżejsze kopie gotowe','applying'=>'zapis w toku','restoring'=>'przywracanie oryginałów','applied'=>'kompresja zastosowana','attention'=>'wymaga dokończenia'][$op['state']]??'wymaga sprawdzenia').'</summary><p>'.esc_html(!empty($op['error'])?self::label($op['error']):'').'</p>';foreach(['cover','inline']as$role){$r=$op['media'][$role]??null;if(is_array($r)&&isset($r['bytes']))echo '<p>'.esc_html($role).' · '.esc_html(number_format($op['snapshot']['media'][$role]['bytes']/1024,1,',',' ')).' → '.esc_html(number_format($r['bytes']/1024,1,',',' ')).' KiB · 1536×1024 · jakość '.(int)$r['quality'].'</p>';}
                if(($op['actor']??null)===get_current_user_id()&&in_array($row['status'],['draft','future'],true)){if($op['state']!=='applied')self::form('zp_prep_compress_recover',['post_id'=>$id,'operation'=>$op['id']],'Dokończ znaną lokalną operację bez API');if(isset($op['proposal']))self::form('zp_prep_compress_rollback',['post_id'=>$id,'operation'=>$op['id']],'Przywróć oryginalne zdjęcia wpisu #'.$id);}echo '</details>';continue;}
            if($id===self::PILOT){echo '<p>#5943 · Pilot już wykonany; druga para nie będzie tworzona.</p>';continue;}
            if($row['proof']&&in_array($row['status'],['draft','future'],true)&&is_string($row['snapshot'])&&max(array_column($row['images'],'bytes'))>512000){try{self::deadline(self::fields($id));self::form('zp_prep_compress_existing',['post_id'=>$id,'snapshot'=>$row['snapshot']],'Skompresuj dwa zapisane zdjęcia wpisu #'.$id.' bez API');}catch(Throwable$e){echo '<p>#'.(int)$id.' · '.esc_html(self::label(self::code($e))).'</p>';}}
        }
    }
    private static function form(string $action,array $fields,string $label): void {echo '<form method="post" action="'.esc_url(admin_url('admin-post.php')).'"><input type="hidden" name="action" value="'.esc_attr($action).'">';wp_nonce_field($action);foreach($fields as$k=>$v)echo '<input type="hidden" name="'.esc_attr($k).'" value="'.esc_attr($v).'">';echo '<p><button class="button button-secondary">'.esc_html($label).'</button></p></form>';}

    public static function register(): void { foreach(['zp_prep_compress_existing','zp_prep_compress_recover','zp_prep_compress_rollback']as$a)add_action('admin_post_'.$a,[self::class,'handler']);add_action('admin_notices',[self::class,'render'],49); }
    public static function render(): void {
        if(!self::allowed()||!is_admin()||wp_doing_ajax()||wp_doing_cron()||defined('REST_REQUEST')&&REST_REQUEST||($_SERVER['REQUEST_METHOD']??'GET')!=='GET'||!is_string($_GET['page']??null)||$_GET['page']!==ZP_Content_Preparation_20261010::PAGE)return;
        echo '<div class="notice notice-info" style="border-color:#6855e8"><details><summary style="cursor:pointer;padding:10px 0;font-weight:700">Kompresja zapisanych zdjęć — odczyt pochodzenia plików</summary><p>Bez generowania zdjęć. Oryginalne pliki i kadry pozostają zachowane. Optymalizacja obejmuje 19 wskazanych szkiców i wpisów zaplanowanych, po jednym wpisie na krok. Pilot #5943 już zakończony. Wpisy opublikowane #5788/#5792/#5837 mają oddzielne ścieżki.</p><table class="widefat striped"><thead><tr><th>Artykuł / stan WordPressa</th><th>Stan przygotowania</th><th>Dwa pliki źródłowe</th><th>Kontrola</th></tr></thead><tbody>';
        $rows=self::inventory();foreach($rows as$r){echo '<tr><td><b>#'.(int)$r['id'].'</b> '.esc_html($r['title']).'<br>'.esc_html($r['status']).'</td><td>'.esc_html((string)($r['state']??'—')).'<br>QA: '.esc_html($r['reviewed']===null?'—':($r['reviewed']?'zapisane':'jeszcze nie zatwierdzono')).'</td><td>';foreach($r['images']as$role=>$m)echo esc_html($role).' #'.(int)$m['id'].' — '.esc_html(number_format($m['bytes']/1024,1,',',' ')).' KiB · 1536×1024<br>';echo '</td><td>';if(isset($r['compression_history_state']))self::render_pilot_readiness($r);else echo esc_html($r['proof']?(!empty($r['compressed'])?'Kompresja zastosowana; aktualna wersja potwierdzona':'Pochodzenie i aktualna wersja potwierdzone'):self::label($r['reason']??'post_changed'));echo '</td></tr>';}
        echo '</tbody></table>';self::controls($rows);echo '<p>Odczyt nie zmienia wpisów, harmonogramów, załączników ani kolejki. Starsze pliki bez hash źródłowych API zostaną opisane jako aktualnie zapisane materiały własnego generatora.</p></details></div>';
    }
    private static function render_pilot_readiness(array $row): void {
        $flag=static fn($v)=>$v===true?'tak':($v===false?'nie':'—');
        echo '<p>Kompresja wykonana wcześniej.</p>';
        echo '<p>'.esc_html(!empty($row['compression_history_matches_current'])?'Historyczna wersja operacji jest zgodna z bieżącą treścią.':'Bieżąca wersja lub stan wpisu różni się od historycznego zapisu kompresji; potwierdzenie operacji pozostaje zachowane.').'</p>';
        $quality=$row['current_core_quality']??'unavailable';
        echo '<p>Bieżąca kontrola treści: '.esc_html($quality==='pass'?'poprawna':($quality==='unavailable'?'brak potwierdzenia':'wymaga sprawdzenia')).'.<br>Gotowość aktualnej wersji: '.esc_html($flag($row['current_core_ready']??null)).'.<br>Bieżące zatwierdzenie QA: '.esc_html($flag($row['current_review_recorded']??null)).'.<br>Zapisana skompresowana para nadal zgodna: '.esc_html($flag($row['compressed_pair_unchanged']??null)).'.</p>';
        if(empty($row['compression_history_matches_current']))echo '<p>Przywracanie historycznej wersji pozostaje zablokowane przy niezgodności z pierwotnym zapisem.</p>';
    }
    private static function label(string $code): string {
        $labels=['already_compressed'=>'Pilot #5943 już skompresowany; nie tworzymy drugiej pary','future_too_close'=>'Termin publikacji jest bliższy niż 10 minut; zachowano wpis i harmonogram','future_cron_changed'=>'Natywne zdarzenie publikacji wymaga sprawdzenia; harmonogram pozostaje bez zmian','encode_failed'=>'Nie udało się zakodować lokalnej kopii','encode_too_large'=>'Lokalna kopia nie osiągnęła limitu 500 KiB','storage_failed'=>'Zapis prywatnego potwierdzenia wymaga dokończenia','metadata_failed'=>'Zapis lokalnych plików lub metadanych wymaga dokończenia','media_changed'=>'Zdjęcie zmieniło się; zachowano ręczną zmianę','already_light'=>'Oba zapisane pliki mieszczą się w limicie 500 KiB','pilot_only'=>'Pilot obejmuje tylko szkic 5943','recovery_required'=>'Najpierw dokończ poprzednią operację','scope_excluded'=>'Oddzielna ścieżka zdjęć; poza pilotem','forbidden'=>'Brak wymaganych uprawnień','missing_post'=>'Brak wpisu','state_not_ready'=>'Wpis nie jest jeszcze gotowy','ownership_changed'=>'Niepotwierdzone pochodzenie zadania','post_changed'=>'Aktualna wersja wymaga sprawdzenia','images_changed'=>'Zdjęcia wymagają sprawdzenia pochodzenia','source_file_changed'=>'Plik zmienił się od zapisu','unsupported_article'=>'Inny układ HTML/Elementora','static_seo_image'=>'Niejednoznaczne odniesienie obrazu SEO','photo_active'=>'Trwa inna operacja zdjęć','unsupported_publication'=>'Publikacja wymaga osobnego potwierdzenia receipt','source_too_large'=>'Nieprawidłowe wymiary lub rozmiar źródła'];return$labels[$code]??'Operacja wymaga sprawdzenia';
    }
}
