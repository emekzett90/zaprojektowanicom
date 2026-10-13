<?php
/** Passive metadata-shape diagnosis of the stopped owned article5421. No adoption or writes. */
if (!defined('ABSPATH')) { exit; }
final class ZP_Article_Photo_Metadata_Diagnostics_20261011 {
    private const POST_ID=5421;
    private const PHOTO_KEYS=['_elementor_data','_thumbnail_id','_zp_cb_20261009_ready_hash','_zp_cb_20261009_content_ready_hash','_zp_cb_20261009_featured_alt'];
    private const EPHEMERAL=['_elementor_css','_elementor_element_cache','_elementor_page_assets','_edit_lock','_edit_last','_zp_post_reads','_zp_post_views'];
    public static function allowed(): bool {return class_exists('ZP_Article_Photo_Refresh_20261010')&&current_user_can('manage_options')&&current_user_can('edit_posts')&&current_user_can('upload_files')&&current_user_can('unfiltered_html')&&current_user_can('edit_post',self::POST_ID);}
    private static function option(string $name,$default=[]){wp_cache_delete($name,'options');wp_cache_delete('notoptions','options');return get_option($name,$default);}
    private static function fresh():void {wp_cache_delete(self::POST_ID,'posts');wp_cache_delete(self::POST_ID,'post_meta');}
    private static function hash($v):string {$s=wp_json_encode($v);if(!is_string($s))throw new RuntimeException('unavailable');return hash('sha256',$s);}
    private static function digest($v):bool {return is_string($v)&&preg_match('/^[a-f0-9]{64}$/D',$v)===1;}
    private static function safe_key($v):bool {return is_string($v)&&preg_match('/^[_A-Za-z][_A-Za-z0-9-]{0,189}$/D',$v)===1&&!preg_match('/(?:sk-[A-Za-z0-9]|[a-f0-9]{32}|eyJ[A-Za-z0-9_-]{12})/i',$v);}
    private static function type($v):string {$t=gettype($v);return in_array($t,['NULL','boolean','integer','double','string','array','object','resource'],true)?strtolower($t):'other';}
    /** The same metadata projection as the immutable public old44 fingerprint. */
    private static function metadata():array {
        $all=get_post_meta(self::POST_ID);if(!is_array($all)||count($all)>1000)throw new RuntimeException('metadata_unavailable');$out=[];
        foreach($all as$k=>$v)if(is_string($k)&&!str_starts_with($k,ZP_Article_Photo_Refresh_20261010::PREFIX)&&!in_array($k,self::EPHEMERAL,true))$out[$k]=$v;ksort($out);return$out;
    }
    private static function fields():array {$p=get_post(self::POST_ID);if(!$p||$p->post_type!=='post')throw new RuntimeException('missing_post');$out=[];foreach(['ID','post_type','post_status','post_title','post_name','post_excerpt','post_author','post_password','post_date','post_date_gmt','post_modified','post_modified_gmt']as$k)$out[$k]=$p->$k??null;$out['post_content']=$p->post_content;return$out;}
    private static function native():array {$f=self::fields();$terms=wp_get_post_categories(self::POST_ID);if(!is_array($terms))throw new RuntimeException('unavailable');sort($terms);return[$f,$terms,self::metadata(),wp_next_scheduled('publish_future_post',[self::POST_ID])];}
    /** Original-photo reconstruction is in memory only and must have a sealed valid own proposal. */
    private static function original_photo_values(array $job,array $s,array $native):?array {
        $d=ZP_Article_Photo_Refresh_20261010::diagnostics($job['id']);$proposal=$d['proposal']??[];
        if(empty($proposal['valid']))return null;foreach(['body','elementor_raw','thumbnail']as$k)if(!in_array($proposal['fields'][$k]['state']??null,['old','own_new'],true))return null;
        $meta=$native[2];if(!is_array($meta['_elementor_data']??null)||count($meta['_elementor_data'])!==1||!is_array($meta['_thumbnail_id']??null)||count($meta['_thumbnail_id'])!==1||!is_string($s['elementor_raw']??null)||strlen($s['elementor_raw'])>800000||!is_string($s['post_content']??null)||strlen($s['post_content'])>400000||!is_int($s['thumbnail_id']??null)||$s['thumbnail_id']<1)return null;
        $native[0]['post_content']=$s['post_content'];$meta['_elementor_data']=[$s['elementor_raw']];$meta['_thumbnail_id']=[(string)$s['thumbnail_id']];
        if(!empty($s['batch_rebase'])){foreach(['featured_alt','ready_hash','content_ready_hash']as$short){$k='_zp_cb_20261009_'.$short;$v=$s['batch_hashes'][$short]??null;if(!is_string($v)||!is_array($meta[$k]??null)||count($meta[$k])!==1)return null;$meta[$k]=[$v];}}
        ksort($meta);$native[2]=$meta;return$native;
    }
    private static function comparison(array $originalPhotoNative,array $removed,array $s):array {
        $full=$originalPhotoNative;$non=$originalPhotoNative;unset($non[0]['post_content']);foreach(self::PHOTO_KEYS as$k)unset($non[2][$k]);foreach($removed as$k){unset($full[2][$k],$non[2][$k]);}
        return ['full_original_digest_matches'=>hash_equals($s['before_hash'],self::hash($full)),'nonphoto_original_digest_matches'=>hash_equals($s['unchanged_hash'],self::hash($non))];
    }
    /** Only current key names/types/counts and equality booleans escape this private read. */
    public static function report():array {
        if(!self::allowed())return[];$out=['post_id'=>self::POST_ID,'status'=>'unavailable','snapshot_verified'=>false,'own_photo_values_verified'=>false,'stable_read'=>null,'current_key_count'=>null,'scan_limited'=>false,'candidate_pool_count'=>0,'candidate_pool_limited'=>false,'attempts'=>0,'keys'=>[],'base_comparison'=>null,'matching_removals'=>[]];
        try{
            $data=self::option(ZP_Article_Photo_Refresh_20261010::STATE,[]);$jobs=is_array($data)?($data['jobs']??null):null;if(!is_array($jobs)||count($jobs)>100)return$out;$hits=[];foreach($jobs as$k=>$j)if(is_array($j)&&($j['post_id']??null)===self::POST_ID&&($j['state']??null)==='needs_attention'&&($j['id']??null)===$k)$hits[]=$j;if(count($hits)!==1){$out['status']='no_exact_stopped_job';return$out;}$job=$hits[0];
            if(!is_string($job['id']??null)||!preg_match('/^[A-Za-z0-9_-]{1,80}$/D',$job['id'])||($job['snapshot_key']??null)!=='zps_article_photos_snapshot_'.$job['id']||!self::digest($job['snapshot_sha']??null)||!self::digest($job['before_hash']??null))return$out;
            $s=self::option($job['snapshot_key'],null);if(!is_array($s)||strlen(wp_json_encode($s))>1600000||($s['id']??null)!==self::POST_ID||($s['before_hash']??null)!==$job['before_hash']||!self::digest($s['unchanged_hash']??null)||!hash_equals($job['snapshot_sha'],self::hash($s))){$out['status']='snapshot_unverified';return$out;}$out['snapshot_verified']=true;self::fresh();$native=self::native();$startHash=self::hash($native);$meta=$native[2];$out['current_key_count']=count($meta);$out['scan_limited']=count($meta)>100;$pool=[];
            foreach(array_slice($meta,0,100,true)as$k=>$v){$safe=self::safe_key($k);$types=[];$n=is_array($v)?count($v):null;foreach(is_array($v)?array_slice($v,0,20):[$v]as$value)$types[]=self::type($value);$types=array_values(array_unique($types));$row=['key'=>$safe?$k:'[ukryta nazwa klucza]','current_present'=>true,'current_container_type'=>self::type($v),'current_value_count'=>$n,'current_value_types'=>$types,'current_type_scan_limited'=>$n!==null&&$n>20,'original_present'=>null,'original_type'=>null,'equality'=>null,'single_comparison'=>null];$out['keys'][]=$row;if($safe&&!in_array($k,self::PHOTO_KEYS,true))$pool[]=$k;}
            $out['candidate_pool_limited']=count($pool)>24||$out['scan_limited'];$pool=array_slice($pool,0,24);$out['candidate_pool_count']=count($pool);$reconstructed=self::original_photo_values($job,$s,$native);if($reconstructed===null){$out['status']='own_photo_values_unverified';return$out;}$out['own_photo_values_verified']=true;$out['base_comparison']=self::comparison($reconstructed,[],$s);$matches=[];$single=[];
            foreach($pool as$k){$c=self::comparison($reconstructed,[$k],$s);$single[$k]=$c;$out['attempts']++;if($c['full_original_digest_matches']||$c['nonphoto_original_digest_matches'])$matches[]=['keys'=>[$k]]+$c;}
            for($a=0;$a<count($pool);$a++)for($b=$a+1;$b<count($pool);$b++){if($out['attempts']>=300)break 2;$ks=[$pool[$a],$pool[$b]];$c=self::comparison($reconstructed,$ks,$s);$out['attempts']++;if($c['full_original_digest_matches']||$c['nonphoto_original_digest_matches'])$matches[]=['keys'=>$ks]+$c;}
            self::fresh();$out['stable_read']=hash_equals($startHash,self::hash(self::native()))&&self::option(ZP_Article_Photo_Refresh_20261010::STATE,[])===$data&&self::option($job['snapshot_key'],null)===$s;
            if(!$out['stable_read']){$out['status']='changed_during_read';$out['base_comparison']=null;return$out;}
            foreach($out['keys']as&$row){$row['single_comparison']=$single[$row['key']]??null;}unset($row);$out['matching_removals']=$matches;$out['status']='readonly_metadata_evidence';return$out;
        }catch(Throwable$ignored){$out['status']='evidence_unavailable';return$out;}
    }
    private static function flag($v):string {return$v===true?'tak':($v===false?'nie':'—');}
    public static function render():void {
        if(!self::allowed()||!is_admin()||($_SERVER['REQUEST_METHOD']??'')!=='GET'||!is_string($_GET['page']??null)||$_GET['page']!==ZP_Article_Photo_Refresh_20261010::PAGE||(function_exists('wp_doing_ajax')&&wp_doing_ajax())||(function_exists('wp_doing_cron')&&wp_doing_cron())||(defined('REST_REQUEST')&&REST_REQUEST))return;
        $r=self::report();if(!$r)return;echo '<section class="zpAdmin20__card"><details><summary>Metadane zatrzymanego #5421 — wyłącznie odczyt</summary><p>Pierwotna pełna mapa metadanych nie została zapisana. Nieznana pierwotna obecność, typ lub zgodność klucza nie oznacza zmiany. Test usunięcia jednego klucza lub pary odbywa się wyłącznie w pamięci. Niczego nie usuwa, nie zatwierdza i nie wznawia.</p><p>Stan: '.esc_html($r['status']).'. Migawka potwierdzona: '.esc_html(self::flag($r['snapshot_verified'])).'. Własne wartości zdjęć: '.esc_html(self::flag($r['own_photo_values_verified'])).'. Stabilny odczyt: '.esc_html(self::flag($r['stable_read'])).'.<br>Aktualne klucze: '.(is_int($r['current_key_count'])?(int)$r['current_key_count']:'—').'. Pula testu: '.(int)$r['candidate_pool_count'].' z limitem24. Prób: '.(int)$r['attempts'].' z limitem300. Odczyt ograniczony: '.esc_html(self::flag($r['scan_limited']||$r['candidate_pool_limited'])).'.</p>';
        if($r['matching_removals']){echo '<ul>';foreach($r['matching_removals']as$m)echo '<li>'.esc_html(implode(' + ',$m['keys'])).' · pełny pierwotny zapis: '.esc_html(self::flag($m['full_original_digest_matches'])).' · pierwotny zapis poza zdjęciami: '.esc_html(self::flag($m['nonphoto_original_digest_matches'])).'.</li>';echo '</ul>';}elseif($r['status']==='readonly_metadata_evidence')echo '<p>Nie znaleziono zgodnej pojedynczej nazwy lub pary w badanej puli. Nie zmienia to zabezpieczeń podmiany.</p>';
        echo '<div class="zpAdmin20__tableWrap"><table class="zpAdmin20__table"><thead><tr><th>Aktualny klucz</th><th>Aktualna obecność / typy / liczba</th><th>Pierwotna obecność / typ / zgodność</th><th>Test pojedynczej nazwy: pełny / poza zdjęciami</th></tr></thead><tbody>';foreach($r['keys']as$k){$c=$k['single_comparison']??[];echo '<tr><td><code>'.esc_html($k['key']).'</code></td><td>tak · '.esc_html($k['current_container_type']).' · '.esc_html(implode(', ',$k['current_value_types'])).' · '.(is_int($k['current_value_count'])?(int)$k['current_value_count']:'—').'</td><td>nieznana · nieznany · nieustalona</td><td>'.esc_html(self::flag($c['full_original_digest_matches']??null)).' / '.esc_html(self::flag($c['nonphoto_original_digest_matches']??null)).'</td></tr>';}echo '</tbody></table></div></details></section>';
    }
    public static function register():void {add_action('admin_notices',[self::class,'render'],52);}
}
