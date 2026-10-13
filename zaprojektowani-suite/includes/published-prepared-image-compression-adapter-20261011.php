<?php
/** Explicit zero-API compression of current published5792/5837; original media and receipts retained. */
if (!defined('ABSPATH')) { exit; }

final class ZP_Published_Prepared_Compression_Adapter_20261011 {
    public const DATA = 'zps_published_prepared_compression_20261011_data';
    public const PREFIX = '_zp_published_compression_20261011_';
    public const PILOT = 5792;
    private const TARGETS = [5792,5837];
    private const NEW_TARGETS = [5792,5837];
    private const IDS = [5792,5837];
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
        foreach(['ID','post_type','post_status','post_title','post_name','post_excerpt','post_author','post_password','post_date','post_date_gmt','post_modified','post_modified_gmt','post_parent','menu_order','comment_status','ping_status','to_ping','pinged','guid','post_content_filtered','post_mime_type','comment_count'] as $key)$out[$key]=$p->$key??null;
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
    /** Fresh observed stored bytes, never a fabricated historical provider receipt. */
    private static function source_media(array $job,string $role): array {
        $id=$job[$role.'_id']??null;if(!is_int($id)||$id<1)throw new RuntimeException('images_changed');self::fresh($id);$p=get_post($id);$m=wp_get_attachment_metadata($id);$file=get_attached_file($id);$base=realpath(wp_get_upload_dir()['basedir']??'');$real=is_string($file)?realpath($file):false;
        if(!$p||$p->post_type!=='attachment'||$p->post_status!=='inherit'||get_post_mime_type($id)!=='image/webp'||(int)($p->post_parent??0)!==0||!is_array($m)||(int)($m['width']??0)!==1536||(int)($m['height']??0)!==1024||!is_string($file)||is_link($file)||!$real||!$base||!str_starts_with($real,$base.DIRECTORY_SEPARATOR)||!is_readable($real))throw new RuntimeException('images_changed');
        $size=@getimagesize($real);$bytes=filesize($real);if(!is_array($size)||($size['mime']??'')!=='image/webp'||$size[0]!==1536||$size[1]!==1024||!is_int($bytes)||$bytes<100||$bytes>10000000)throw new RuntimeException('source_too_large');$sha=hash_file('sha256',$real);$prefix=ZP_Content_Preparation_20261010::PREFIX;$mode='';$history=null;
        if(($job['post_id']??null)===5837){
            $stem='zp-poradnik-'.substr($job['id'],0,12).'-'.$role;if(!preg_match('~^'.preg_quote($stem,'~').'(?:-\d+)?\.webp$~D',basename($real))||(int)$p->post_author!==(int)$job['actor'])throw new RuntimeException('images_changed');
            foreach(['job'=>$job['id'],'role'=>$role,'model'=>$job['config']['image_model']??null,'generated'=>'1']as$k=>$expected)if(!is_scalar($expected)||(string)get_post_meta($id,$prefix.$k,true)!==(string)$expected)throw new RuntimeException('images_changed');
            $stored=get_post_meta($id,$prefix.'encoded_sha256',true);if($stored==='')$stored=get_post_meta($id,$prefix.'provider_sha256',true);if($stored!==''&&(!is_string($stored)||!preg_match('/^[a-f0-9]{64}$/D',$stored)||!hash_equals($stored,$sha)))throw new RuntimeException('source_file_changed');$mode='observed_core_stored_bytes';
        }elseif(($job['post_id']??null)===5792){
            $d=self::read_option('zps_prepared_photos_20261010_data',[]);$jobs=is_array($d)?($d['jobs']??null):null;if(!is_array($jobs)||count($jobs)>50)throw new RuntimeException('images_changed');$hits=[];foreach($jobs as$j)if(is_array($j)&&($j['post_id']??null)===5792&&($j['prepared_id']??null)===$job['id']&&($j['state']??null)==='applied'&&($j['cover_id']??null)===$job['cover_id']&&($j['inline_id']??null)===$job['inline_id'])$hits[]=$j;if(count($hits)!==1)throw new RuntimeException('images_changed');$j=$hits[0];$snap=$j['snapshot']??null;$proposal=$j['proposal']??null;if(!is_array($snap)||!is_array($proposal))throw new RuntimeException('images_changed');$copy=$snap;unset($copy['hash']);if(($snap['id']??null)!==5792||($snap['prepared_id']??null)!==$job['id']||($snap['hash']??null)!==self::sha($copy)||($j['proposal_sha']??null)!==self::sha($proposal))throw new RuntimeException('images_changed');
            $matched=false;foreach($job['photo_history']??[]as$h)if(is_array($h)&&($h['refresh_id']??null)===$j['id']&&($h['new_cover_id']??null)===$job['cover_id']&&($h['new_inline_id']??null)===$job['inline_id'])$matched=true;if(!$matched)throw new RuntimeException('images_changed');$prefix='_zp_prepared_photos_20261010_';$scene=$snap['scene_plan'][$role]??[];
            foreach(['job'=>$j['id'],'role'=>$role,'model'=>$j['config']['model']??null,'source_hash'=>$snap['hash']]as$k=>$expected)if(!is_string($expected)||get_post_meta($id,$prefix.$k,true)!==$expected)throw new RuntimeException('images_changed');
            if((int)$p->post_author!==(int)$j['actor']||$p->post_title!==($scene['alt']??null)||$p->post_content!==($scene['caption']??null)||$p->post_excerpt!==($scene['caption']??null)||get_post_meta($id,'_wp_attachment_image_alt',true)!==($scene['alt']??null)||get_post_meta($id,$prefix.'original_file',true)!==$file||get_post_meta($id,$prefix.'generated_sha',true)!==$sha||!metadata_exists('post',$id,$prefix.'expected_metadata')||get_post_meta($id,$prefix.'expected_metadata',true)!==$m)throw new RuntimeException('source_file_changed');$mode='known_applied_draft_refresh';$history=['job'=>$j['id'],'snapshot_sha'=>$snap['hash'],'proposal_sha'=>$j['proposal_sha']];
        }else throw new RuntimeException('scope_excluded');
        $all=get_post_meta($id);if(!is_array($all))throw new RuntimeException('images_changed');ksort($all);$fields=[];foreach(['ID','post_type','post_status','post_title','post_content','post_excerpt','post_author','post_parent','post_date','post_date_gmt','post_modified','post_modified_gmt']as$k)$fields[$k]=$p->$k??null;
        return['id'=>$id,'url'=>self::url($id),'file'=>$real,'sha'=>$sha,'bytes'=>$bytes,'metadata'=>$m,'all_meta'=>$all,'fields'=>$fields,'alt'=>get_post_meta($id,'_wp_attachment_image_alt',true),'signature'=>self::sha([$fields,$all,$real,$sha]),'source_mode'=>$mode,'source_history'=>$history,'provider_hash_present'=>false];
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
    /** Current explicit published baseline; missing historical publish_attempt stays absent. */
    public static function inspect(int $id): array {
        if(!self::allowed($id))throw new RuntimeException('forbidden');self::fresh($id);$p=get_post($id);$j=self::core_job($id);
        if(!$p||$p->post_type!=='post'||$p->post_status!=='publish'||$p->post_password!==''||wp_next_scheduled('publish_future_post',[$id])!==false||get_post_meta($id,ZP_Content_Preparation_20261010::PREFIX.'job',true)!==($j['id']??null)||get_post_meta($id,ZP_Content_Preparation_20261010::PREFIX.'batch',true)!==($j['batch']??null)||!is_string($j['id']??null)||!preg_match('/^[A-Za-z0-9_-]{1,80}$/D',$j['id'])||!in_array($j['state']??null,['ready','published'],true))throw new RuntimeException('ownership_changed');
        self::no_photos($j,$id);$ready=ZP_Content_Preparation_20261010::fingerprint($id);if(!is_string($j['ready_hash']??null)||!hash_equals($j['ready_hash'],$ready)||get_post_meta($id,ZP_Content_Preparation_20261010::PREFIX.'ready_hash',true)!==$ready)throw new RuntimeException('post_changed');
        $raw=get_post_meta($id,'_elementor_data',true);$html=$p->post_content;if(!is_string($raw)||strlen($raw)>800000||!is_string($html)||strlen($html)>300000||self::widgets(json_decode($raw,true))!==[ZP_Content_Preparation_20261010::style().$html])throw new RuntimeException('unsupported_article');ZP_Article_Photo_Refresh_20261010::untouched_signature($html);
        $media=[];foreach(['cover','inline']as$role)$media[$role]=self::source_media($j,$role);if($media['cover']['id']===$media['inline']['id']||(int)get_post_thumbnail_id($id)!==$media['cover']['id'])throw new RuntimeException('images_changed');$tags=new WP_HTML_Tag_Processor($html);$actual=[];while($tags->next_tag('IMG'))$actual[]=$tags->get_attribute('src');if($actual!==[$media['cover']['url'],$media['inline']['url']])throw new RuntimeException('images_changed');
        if(get_post_meta($id,'rank_math_facebook_image',true)!==$media['cover']['url']||(string)get_post_meta($id,'rank_math_facebook_image_id',true)!==(string)$media['cover']['id'])throw new RuntimeException('static_seo_image');self::static_refs($id,$media);
        if($id===5792){$d=self::read_option('zps_prepared_photos_20261010_data',[]);$r=$d['jobs'][$media['cover']['source_history']['job']]??null;if(!is_array($r)||($r['proposal']['html']??null)!==$html)throw new RuntimeException('post_changed');foreach(['_elementor_data','_thumbnail_id','rank_math_facebook_image','rank_math_facebook_image_id']as$k)if(get_post_meta($id,$k,true)!==($r['proposal']['meta'][$k]??null))throw new RuntimeException('post_changed');}
        $meta=[];foreach(self::PHOTO_META as$k)$meta[$k]=get_post_meta($id,$k,true);$hashmeta=[];foreach(self::HASH_META as$k)$hashmeta[$k]=get_post_meta($id,$k,true);$url=get_permalink($id);$u=parse_url($url);$h=parse_url(home_url('/'));if(!is_array($u)||($u['scheme']??null)!=='https'||($u['host']??null)!==($h['host']??null)||array_intersect(['user','pass','query','fragment','port'],array_keys($u)))throw new RuntimeException('unsupported_publication');
        $s=['baseline_kind'=>'explicit_current_published_snapshot','public_url'=>$url,'hash_meta'=>$hashmeta,'hash_categories'=>wp_get_post_categories($id),'id'=>$id,'job'=>$j,'fields'=>self::fields($id),'other_meta'=>self::other_meta($id),'photo_meta'=>$meta,'html'=>$html,'widget'=>ZP_Content_Preparation_20261010::style().$html,'media'=>$media,'before_hash'=>$ready];$s['sha']=self::sha($s);return$s;
    }

    public static function inventory(): array {
        if(!self::allowed())return[];$out=[];
        foreach(self::IDS as$id){$r=['id'=>$id,'proof'=>false,'reason'=>'forbidden','title'=>'','status'=>'missing','snapshot'=>null,'images'=>[]];if(!self::allowed($id)){$out[]=$r;continue;}self::fresh($id);$p=get_post($id);$r['title']=$p?mb_substr((string)$p->post_title,0,300):'';$r['status']=$p?($p->post_status==='publish'?'publish':'not_published'):'missing';
            try{$active=null;foreach(self::data()['operations']??[]as$o)if(($o['snapshot']['id']??null)===$id&&($o['state']??null)==='applied')$active=$o;if($active){self::variant($active,true);$r['proof']=true;$r['reason']='applied';foreach($active['media']as$role=>$m)$r['images'][$role]=['id'=>$m['id'],'bytes'=>$m['bytes']];}else{$s=self::inspect($id);$r['proof']=true;$r['reason']='current_published_baseline';$r['snapshot']=$s['sha'];foreach($s['media']as$role=>$m)$r['images'][$role]=['id'=>$m['id'],'bytes'=>$m['bytes']];}}catch(Throwable$e){$r['reason']=self::code($e);}$out[]=$r;
        }return$out;
    }

    /** Read-only comparison for the single stopped native pilot. Never adopts a new baseline. */
    public static function diagnostics(): array {
        if(!self::allowed(5792))return[];
        $hits=[];foreach(self::data()['operations']??[]as$o)if(is_array($o)&&($o['snapshot']['id']??null)===5792&&($o['state']??null)==='attention')$hits[]=$o;
        if(count($hits)!==1)return[];$op=$hits[0];if(($op['actor']??null)!==get_current_user_id())return[];
        try{
            $s=self::snapshot($op);$p=$op['proposal']??null;if(!is_array($p)||($op['proposal_sha']??null)!==self::sha($p)||$p!==self::proposal($op))return['proposal_valid'=>false];
            self::fresh(5792);$post=get_post(5792);if(!$post)return['proposal_valid'=>true,'post_exists'=>false];$fields=self::fields(5792);$meta=self::other_meta(5792);
            $classify=static function($v,$old,$next):string{return$v===$old?'old':($v===$next?'own_new':'other');};
            $names=static function(array$a):array{$r=[];foreach(array_keys($a)as$k){$safe=is_string($k)&&preg_match('/^[_A-Za-z][_A-Za-z0-9-]{0,99}$/D',$k)&&!preg_match('/(?:sk-[A-Za-z0-9]|[a-f0-9]{32}|eyJ[A-Za-z0-9_-]{12})/i',$k);$r[]=$safe?$k:'[ukryta nazwa klucza]';}sort($r);return array_slice(array_values(array_unique($r)),0,100);};
            $fieldDiff=[];foreach($s['fields']as$k=>$v)if(!array_key_exists($k,$fields)||$fields[$k]!==$v)$fieldDiff[$k]=true;
            $added=array_diff_key($meta,$s['other_meta']);$removed=array_diff_key($s['other_meta'],$meta);$changed=[];foreach(array_intersect_key($meta,$s['other_meta'])as$k=>$v)if($v!==$s['other_meta'][$k])$changed[$k]=true;
            $photos=[];foreach(self::PHOTO_META as$k)$photos[$k]=$classify(get_post_meta(5792,$k,true),$s['photo_meta'][$k],$p['photos'][$k]);
            $core=self::core_job(5792);$ready=ZP_Content_Preparation_20261010::fingerprint(5792);$sourceSame=true;$newSame=true;
            try{self::source_unchanged($s);}catch(Throwable$ignored){$sourceSame=false;}
            try{foreach(['cover','inline']as$role){$r=$op['media'][$role]??[];if(!is_int($r['id']??null)||!is_string($r['signature']??null)||!hash_equals($r['signature'],self::new_signature($r['id'])))throw new RuntimeException('media_changed');}}catch(Throwable$ignored){$newSame=false;}
            $body=$post->post_content;$shape=['original_bytes'=>strlen($s['html']),'proposal_bytes'=>strlen($p['html']),'current_bytes'=>strlen($body)];
            $stage=$op['write_stage']??null;$stages=['body','body_readback','_elementor_data','_thumbnail_id','rank_math_facebook_image','rank_math_facebook_image_id','_zp_prepared_20261010_ready_hash','core_job','final_readback'];
            $d=['proposal_valid'=>true,'post_exists'=>true,'native_fields_same'=>$fields===$s['fields'],'native_field_differences'=>$names($fieldDiff),'public_url_same'=>get_permalink(5792)===$s['public_url'],'nonphoto_meta_same'=>$meta===$s['other_meta'],'meta_added'=>$names($added),'meta_removed'=>$names($removed),'meta_changed'=>$names($changed),'meta_changes_count'=>count($added)+count($removed)+count($changed),'body'=>$classify($body,$s['html'],$p['html']),'photos'=>$photos,'core_job'=>$classify($core,$s['job'],$p['job']),'original_media_same'=>$sourceSame,'new_media_same'=>$newSame,'ready_matches_old'=>$ready===$s['before_hash'],'ready_matches_proposal'=>$ready===$p['ready_hash'],'body_shape'=>$shape,'last_write_stage'=>is_string($stage)&&in_array($stage,$stages,true)?$stage:null];
            self::fresh(5792);if(self::data()['operations'][$op['id']]!==$op||get_post(5792)->post_content!==$body||self::fields(5792)!==$fields||self::other_meta(5792)!==$meta||self::core_job(5792)!==$core)return['unstable'=>true];return$d;
        }catch(Throwable$ignored){return['diagnostic_unavailable'=>true];}
    }

    private static function render_diagnostics(): void {
        $d=self::diagnostics();if(!$d)return;
        echo '<details><summary>Diagnostyka zatrzymanej kompresji #5792 · tylko odczyt</summary><p>Porównanie z zatwierdzoną migawką; bez przyjęcia zmian i bez ponawiania kompresji. OLD oznacza wartość pierwotną, OWN_NEW dokładnie zapisaną własną propozycję, OTHER inną wartość.</p><dl>';
        foreach(['proposal_valid','post_exists','native_fields_same','public_url_same','nonphoto_meta_same','original_media_same','new_media_same','ready_matches_old','ready_matches_proposal','unstable','diagnostic_unavailable']as$k)if(array_key_exists($k,$d))echo '<dt>'.esc_html($k).'</dt><dd>'.($d[$k]?'TAK':'NIE').'</dd>';
        foreach(['native_field_differences','meta_added','meta_removed','meta_changed']as$k)if(isset($d[$k]))echo '<dt>'.esc_html($k).'</dt><dd>'.esc_html($d[$k]?implode(', ',$d[$k]):'brak').'</dd>';
        foreach(['body','core_job','last_write_stage']as$k)if(isset($d[$k]))echo '<dt>'.esc_html($k).'</dt><dd>'.esc_html(strtoupper($d[$k])).'</dd>';
        foreach($d['photos']??[]as$k=>$v)echo '<dt>'.esc_html($k).'</dt><dd>'.esc_html(strtoupper($v)).'</dd>';
        if(isset($d['body_shape']))echo '<dt>Długość treści w bajtach · oryginał / propozycja / aktualna</dt><dd>'.(int)$d['body_shape']['original_bytes'].' / '.(int)$d['body_shape']['proposal_bytes'].' / '.(int)$d['body_shape']['current_bytes'].'</dd>';
        echo '</dl><p>Pokazujemy nazwy zmienionych pól, bez ich wartości. Wynik nie uprawnia do obejścia kontroli zapisu.</p></details>';
    }

    private const HASH_META = ['_elementor_edit_mode','_elementor_template_type','_elementor_version','_elementor_pro_version','_elementor_page_settings','_wp_page_template','_elementor_data','_thumbnail_id','rank_math_title','rank_math_description','rank_math_focus_keyword','rank_math_canonical_url','rank_math_robots','rank_math_schema_Article','_zp_prepared_20261010_faq'];
    private static function save(array $d): void {
        if(!self::allowed())throw new RuntimeException('forbidden');if(strlen(wp_json_encode($d))>16000000)throw new RuntimeException('storage_limit');update_option(self::DATA,$d,false);if(self::read_option(self::DATA,null)!==$d)throw new RuntimeException('storage_failed');
    }
    public static function data(): array {if(!self::allowed())return[];$d=self::read_option(self::DATA,[]);if(!is_array($d)||count($d['operations']??[])>50)throw new RuntimeException('storage_failed');return$d;}
    private static function store_operation(array $op): void {
        self::actor_capabilities($op);$d=self::data();$old=$d['operations'][$op['id']]??null;if($old && (($old['snapshot']['sha']??null)!==$op['snapshot']['sha'] || ($old['actor']??null)!==$op['actor']))throw new RuntimeException('storage_failed');$d['operations'][$op['id']]=$op;self::save($d);
    }
    private static function snapshot(array $op): array {
        $s=$op['snapshot']??null;if(!is_array($s)||!is_int($s['id']??null)||!in_array($s['id'],self::TARGETS,true)||!is_string($s['sha']??null))throw new RuntimeException('storage_failed');$copy=$s;unset($copy['sha']);if(!hash_equals($s['sha'],self::sha($copy)))throw new RuntimeException('storage_failed');return$s;
    }
    private static function operation(string $id): array {
        $op=self::data()['operations'][$id]??null;if(!is_array($op)||($op['id']??null)!==$id||!is_int($op['actor']??null)||$op['actor']!==get_current_user_id())throw new RuntimeException('no_operation');self::snapshot($op);return$op;
    }
    private static function original(array $s): void { if(!hash_equals($s['sha'],self::inspect($s['id'])['sha']))throw new RuntimeException('post_changed'); }
    private static function posted_id(): int {$raw=self::scalar('post_id');if(!preg_match('/^[1-9]\d{0,7}$/D',$raw)||!in_array((int)$raw,self::TARGETS,true))throw new RuntimeException('invalid_input');return(int)$raw;}

    private static function deadline(array $fields): void {if(($fields['post_status']??null)!=='publish'||($fields['future_cron']??null)!==false)throw new RuntimeException('scope_excluded');}

    private static function actor_capabilities(array $op): void {
        $s=self::snapshot($op);$actor=$op['actor']??null;if(!is_int($actor)||get_current_user_id()!==$actor||!self::allowed($s['id']))throw new RuntimeException('forbidden');wp_cache_delete($actor,'users');wp_cache_delete($actor,'user_meta');$u=get_userdata($actor);foreach(['manage_options','edit_posts','upload_files','unfiltered_html']as$cap)if(!$u||!user_can($u,$cap))throw new RuntimeException('forbidden');if(!user_can($u,'edit_post',$s['id']))throw new RuntimeException('forbidden');
    }
    private static function actor(array $op): void {self::actor_capabilities($op);$s=self::snapshot($op);self::fresh($s['id']);self::deadline(self::fields($s['id']));}


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
            $upload=wp_upload_bits('zp-published-compressed-'.$s['id'].'-'.substr(hash('sha256',$op['id']),0,12).'-'.$role.'.webp',null,$encoded['bytes']);if(!empty($upload['error'])||!is_string($upload['file']??null))throw new RuntimeException('metadata_failed');$r['file']=$upload['file'];$r['url']=$upload['url']??null;$op['media'][$role]=$r;self::store_operation($op);
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
        $core['published_stored_image_compression']=['operation'=>$op['id'],'snapshot_sha'=>$s['sha'],'before_hash'=>$s['before_hash'],'after_hash'=>$ready,'source_ids'=>[$s['media']['cover']['id'],$s['media']['inline']['id']],'attachment_ids'=>[$m['cover']['id'],$m['inline']['id']],'at'=>$op['commit_at']];return['html'=>$html,'photos'=>$photos,'job'=>$core,'ready_hash'=>$ready];
    }
    private static function variant(array $op,?bool $new=null): void {
        self::actor($op);$s=self::snapshot($op);$id=$s['id'];self::fresh($id);if(self::fields($id)!==$s['fields']||get_permalink($id)!==$s['public_url']||self::other_meta($id)!==$s['other_meta'])throw new RuntimeException('post_changed');self::source_unchanged($s);
        $p=$op['proposal']??null;if(!is_array($p))throw new RuntimeException('recovery_required');if(($op['proposal_sha']??null)!==self::sha($p)||$p!==self::proposal($op))throw new RuntimeException('recovery_required');$current=get_post($id)->post_content;if($new===null?!in_array($current,[$s['html'],$p['html']],true):$current!==($new?$p['html']:$s['html']))throw new RuntimeException('post_changed');
        foreach(self::PHOTO_META as$k){$v=get_post_meta($id,$k,true);$old=$s['photo_meta'][$k];$next=$p['photos'][$k];if($new===null?!in_array($v,[$old,$next],true):$v!==($new?$next:$old))throw new RuntimeException('post_changed');}
        $j=self::core_job($id);if($new===null?!in_array($j,[$s['job'],$p['job']],true):$j!==($new?$p['job']:$s['job']))throw new RuntimeException('ownership_changed');
        foreach(['cover','inline']as$role){$r=$op['media'][$role];if(!hash_equals($r['signature']??'',self::new_signature($r['id'])))throw new RuntimeException('media_changed');}
    }
    private static function native_content(array $s,string $html): void {
        $id=$s['id'];$filter=static function($data,$postarr)use($s,$id){if((int)($postarr['ID']??0)===$id)foreach($s['fields']as$k=>$v)if(str_starts_with($k,'post_')||in_array($k,['menu_order','comment_status','ping_status','to_ping','pinged','guid'],true))$data[$k]=is_string($v)?wp_slash($v):$v;return$data;};add_filter('wp_insert_post_data',$filter,PHP_INT_MAX,2);
        try{$r=wp_update_post(wp_slash(['ID'=>$id,'post_content'=>$html]),true);if(is_wp_error($r)||$r!==$id)throw new RuntimeException('metadata_failed');}finally{remove_filter('wp_insert_post_data',$filter,PHP_INT_MAX);}self::fresh($id);
    }
    private static function write_core(array $op,bool $new): void {
        $s=self::snapshot($op);$p=$op['proposal'];$d=self::read_option(ZP_Content_Preparation_20261010::DATA,[]);$key=$s['job']['id'];if(!is_array($d)||!in_array($d['jobs'][$key]??null,[$s['job'],$p['job']],true))throw new RuntimeException('ownership_changed');$d['jobs'][$key]=$new?$p['job']:$s['job'];update_option(ZP_Content_Preparation_20261010::DATA,$d,false);if(self::read_option(ZP_Content_Preparation_20261010::DATA,null)!==$d)throw new RuntimeException('storage_failed');
    }
    private static function apply_operation(array &$op,bool $new): void {
        $s=self::snapshot($op);$id=$s['id'];self::variant($op);$p=$op['proposal'];$op['state']=$new?'applying':'restoring';$op['updated_at']=time();self::store_operation($op);
        // A known partially saved proposal must not repeat an already completed native publish->publish update.
        $op['write_stage']='body';self::store_operation($op);self::variant($op);$html=$new?$p['html']:$s['html'];if(get_post($id)->post_content!==$html)self::native_content($s,$html);$op['write_stage']='body_readback';self::store_operation($op);
        foreach(self::PHOTO_META as$k){$op['write_stage']=$k;self::store_operation($op);self::variant($op);$target=$new?$p['photos'][$k]:$s['photo_meta'][$k];if(get_post_meta($id,$k,true)!==$target)self::meta_write($id,$k,$target);}
        $op['write_stage']='core_job';self::store_operation($op);self::variant($op);if(self::core_job($id)!==($new?$p['job']:$s['job']))self::write_core($op,$new);$op['write_stage']='final_readback';self::store_operation($op);self::variant($op,$new);
        self::finish_operation($op,$new);
    }
    /** Exact final readback; known own-new recovery needs no repeated native article writes. */
    private static function finish_operation(array &$op,bool $new): void {
        self::variant($op,$new);$s=self::snapshot($op);$expected=$new?$op['proposal']['ready_hash']:$s['before_hash'];if(ZP_Content_Preparation_20261010::fingerprint($s['id'])!==$expected)throw new RuntimeException('post_changed');
        foreach(['_elementor_css','_elementor_element_cache','_elementor_page_assets']as$k)delete_post_meta($s['id'],$k);self::fresh($s['id']);self::variant($op,$new);$op['state']=$new?'applied':'rolled_back';$op['error']='';$op['updated_at']=time();self::store_operation($op);
    }
    public static function compress(string $expected,int $post_id=self::PILOT): string {
        if(!self::allowed($post_id)||!in_array($post_id,self::TARGETS,true)||!preg_match('/^[a-f0-9]{64}$/D',$expected))throw new RuntimeException('forbidden');return ZP_Content_Preparation_20261010::exclusive(static function()use($expected,$post_id){
            $count=0;foreach(self::data()['operations']??[]as$op){$s=self::snapshot($op);if($s['id']!==$post_id)continue;$count++;if(($op['state']??'')==='applied'){self::variant($op,true);return$op['id'];}if(($op['state']??'')!=='rolled_back')throw new RuntimeException('recovery_required');}
            // Seal only the nonce-authorized current published baseline, not a historical publication receipt.
            if(!in_array($post_id,self::TARGETS,true))throw new RuntimeException('scope_excluded');
            $s=self::inspect($post_id);if($s['fields']['post_status']!=='publish')throw new RuntimeException('scope_excluded');self::deadline($s['fields']);if(!hash_equals($s['sha'],$expected))throw new RuntimeException('post_changed');if(max(array_column($s['media'],'bytes'))<=512000)throw new RuntimeException('already_light');
            $d=self::data();if(count($d['operations']??[])>=50||$count>=5)throw new RuntimeException('storage_limit');$op=['id'=>wp_generate_uuid4(),'actor'=>get_current_user_id(),'snapshot'=>$s,'state'=>'preparing','media'=>[],'created_at'=>time(),'updated_at'=>time(),'error'=>''];self::store_operation($op);return self::continue_operation($op);
        });
    }
    private static function continue_operation(array $op): string {
        try{self::actor($op);if(!isset($op['proposal'])){foreach(['cover','inline']as$role)self::finish_media($op,$role);self::original(self::snapshot($op));$op['commit_at']=$op['commit_at']??time();$op['proposal']=self::proposal($op);$op['proposal_sha']=self::sha($op['proposal']);$op['state']='ready';self::store_operation($op);}$exact=false;try{self::variant($op,true);$exact=ZP_Content_Preparation_20261010::fingerprint($op['snapshot']['id'])===$op['proposal']['ready_hash'];}catch(Throwable$ignored){}if($exact)self::finish_operation($op,true);else self::apply_operation($op,true);return$op['id'];}
        catch(Throwable$e){$op['error']=self::code($e);$op['state']='attention';$op['updated_at']=time();try{self::store_operation($op);}catch(Throwable$ignored){}throw new RuntimeException($op['error']);}
    }
    public static function recover(string $id): void {if(!self::allowed())throw new RuntimeException('forbidden');ZP_Content_Preparation_20261010::exclusive(static function()use($id){$op=self::operation($id);if($op['state']==='applied'){self::variant($op,true);return;}if($op['state']==='restoring'||!empty($op['restoring']))self::apply_operation($op,false);elseif(in_array($op['state'],['preparing','ready','applying','attention'],true))self::continue_operation($op);else throw new RuntimeException('no_operation');});}
    public static function rollback(string $id): void {if(!self::allowed())throw new RuntimeException('forbidden');ZP_Content_Preparation_20261010::exclusive(static function()use($id){$op=self::operation($id);if($op['state']==='rolled_back'){self::variant($op,false);return;}if(!isset($op['proposal']))throw new RuntimeException('recovery_required');self::variant($op,$op['state']==='applied'?true:null);$op['restoring']=true;self::store_operation($op);self::apply_operation($op,false);});}
    public static function guard(string $action): void {if(!self::allowed()||($_SERVER['REQUEST_METHOD']??'')!=='POST'||!is_string($_POST['_wpnonce']??null)||!wp_verify_nonce(wp_unslash($_POST['_wpnonce']),$action))wp_die('Brak uprawnień lub prawidłowego potwierdzenia.','',['response'=>403]);}
    private static function scalar(string $key): string {if(!is_string($_POST[$key]??null))throw new RuntimeException('invalid_input');return trim(wp_unslash($_POST[$key]));}
    public static function handler(): void {
        $action=is_string($_POST['action']??null)?wp_unslash($_POST['action']):'';if(!in_array($action,['zp_published_compress_start','zp_published_compress_recover','zp_published_compress_rollback'],true))wp_die('Nieprawidłowa operacja.','',['response'=>403]);self::guard($action);
        try{$post_id=self::posted_id();if($action==='zp_published_compress_start'){if(self::scalar('baseline_ack')!=='1')throw new RuntimeException('invalid_input');self::compress(self::scalar('snapshot'),$post_id);}else{$key=self::scalar('operation');if(self::snapshot(self::operation($key))['id']!==$post_id)throw new RuntimeException('invalid_input');if($action==='zp_published_compress_recover')self::recover($key);else self::rollback($key);}$status=$action==='zp_published_compress_rollback'?'rolled_back':'applied';}catch(Throwable$e){$status=self::code($e);}wp_safe_redirect(admin_url('admin.php?page=zp-suite-prepare&zp_published_compress='.$status));exit;
    }
    private static function controls(array $rows): void {
        $ops=self::data()['operations']??[];
        foreach($rows as$r){$id=$r['id'];if(!self::allowed($id))continue;$active=null;foreach($ops as$o)if(($o['snapshot']['id']??null)===$id&&($o['state']??null)!=='rolled_back')$active=$o;
            if($active){echo '<p>#'.(int)$id.' · '.esc_html($active['state']).' · '.esc_html(self::label(($active['error']??'')===''?$active['state']:$active['error'])).'</p>';if(($active['actor']??null)===get_current_user_id()&&$r['status']==='publish'){if($active['state']!=='applied')self::form('zp_published_compress_recover',['post_id'=>$id,'operation'=>$active['id']],'Dokończ znaną kompresję bez API');if(isset($active['proposal']))self::form('zp_published_compress_rollback',['post_id'=>$id,'operation'=>$active['id']],'Przywróć oryginalną parę zdjęć #'.$id);}continue;}
            if($r['proof']&&$r['status']==='publish'&&is_string($r['snapshot'])&&max(array_column($r['images'],'bytes'))>512000){self::form('zp_published_compress_start',['post_id'=>$id,'snapshot'=>$r['snapshot'],'baseline_ack'=>'1'],'Skompresuj istniejącą parę #'.$id.' bez API');}
        }
    }

    private static function form(string $action,array $fields,string $label): void {echo '<form method="post" action="'.esc_url(admin_url('admin-post.php')).'"><input type="hidden" name="action" value="'.esc_attr($action).'">';wp_nonce_field($action);foreach($fields as$k=>$v)echo '<input type="hidden" name="'.esc_attr($k).'" value="'.esc_attr($v).'">';echo '<p><button class="button button-secondary">'.esc_html($label).'</button></p></form>';}

    public static function register(): void { foreach(['zp_published_compress_start','zp_published_compress_recover','zp_published_compress_rollback']as$a)add_action('admin_post_'.$a,[self::class,'handler']);add_action('admin_notices',[self::class,'render'],53); }
    public static function render(): void {
        if(!self::allowed()||!is_admin()||wp_doing_ajax()||wp_doing_cron()||(defined('REST_REQUEST')&&REST_REQUEST)||($_SERVER['REQUEST_METHOD']??'GET')!=='GET'||!is_string($_GET['page']??null)||$_GET['page']!==ZP_Content_Preparation_20261010::PAGE)return;
        echo '<div class="notice notice-info"><details><summary style="padding:10px 0;font-weight:700">Kompresja dwóch już opublikowanych artykułów — bez API</summary><p>Wyłącznie #5792 i #5837. Przycisk zatwierdza bieżącą wersję artykułu do zmiany dwóch adresów zdjęć. Oryginalne pliki, treść, SEO, daty, status publikacji i historyczne potwierdzenia pozostają zachowane. Nie tworzymy brakującego potwierdzenia publikacji.</p>';foreach(self::inventory()as$r){echo '<h3>#'.(int)$r['id'].' · '.esc_html($r['title']).'</h3><p>'.esc_html($r['status']).' · '.esc_html(self::label($r['reason'])).'</p>';foreach($r['images']as$role=>$m)echo '<p>'.esc_html($role).' #'.(int)$m['id'].' · '.esc_html(number_format($m['bytes']/1024,1,',',' ')).' KiB</p>';if($r['status']==='publish')echo '<p><a href="'.esc_url(get_permalink($r['id'])).'" target="_blank" rel="noopener">Pełny artykuł ↗</a></p>';}self::controls(self::inventory());self::render_diagnostics();echo '</details></div>';
    }

    private static function label(string $code): string {
        $labels=['already_compressed'=>'Ten artykuł ma już potwierdzoną lżejszą parę','future_too_close'=>'Termin publikacji jest bliższy niż 10 minut; zachowano wpis i harmonogram','future_cron_changed'=>'Natywne zdarzenie publikacji wymaga sprawdzenia; harmonogram pozostaje bez zmian','encode_failed'=>'Nie udało się zakodować lokalnej kopii','encode_too_large'=>'Lokalna kopia nie osiągnęła limitu 500 KiB','storage_failed'=>'Zapis prywatnego potwierdzenia wymaga dokończenia','metadata_failed'=>'Zapis lokalnych plików lub metadanych wymaga dokończenia','media_changed'=>'Zdjęcie zmieniło się; zachowano ręczną zmianę','already_light'=>'Oba zapisane pliki mieszczą się w limicie 500 KiB','current_published_baseline'=>'Aktualna własna wersja potwierdzona','applied'=>'Lżejsze zdjęcia zapisane w artykule','pilot_only'=>'Poza zakresem','recovery_required'=>'Najpierw dokończ poprzednią operację','scope_excluded'=>'Poza zakresem dwóch opublikowanych artykułów','forbidden'=>'Brak wymaganych uprawnień','missing_post'=>'Brak wpisu','state_not_ready'=>'Wpis nie jest jeszcze gotowy','ownership_changed'=>'Niepotwierdzone pochodzenie zadania','post_changed'=>'Aktualna wersja wymaga sprawdzenia','images_changed'=>'Zdjęcia wymagają sprawdzenia pochodzenia','source_file_changed'=>'Plik zmienił się od zapisu','unsupported_article'=>'Inny układ HTML/Elementora','static_seo_image'=>'Niejednoznaczne odniesienie obrazu SEO','photo_active'=>'Trwa inna operacja zdjęć','unsupported_publication'=>'Publikacja wymaga osobnego potwierdzenia receipt','source_too_large'=>'Nieprawidłowe wymiary lub rozmiar źródła'];return$labels[$code]??'Operacja wymaga sprawdzenia';
    }
}
