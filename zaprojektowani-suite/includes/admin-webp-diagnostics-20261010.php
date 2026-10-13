<?php
/** Bounded read-only encoder diagnostics. No image processing, network, scheduling or storage writes. */
if (!defined('ABSPATH')) { exit; }
final class ZP_Admin_WebP_Diagnostics_20261010 {
    private const PAGES=['zp-suite-prepare','zp-suite-article-photos'];
    private const ERRORS=['webp_encoder_unavailable','webp_dimensions','webp_input','webp_temp','webp_editor','webp_quality','webp_resize','webp_output','webp_weight_limit','webp_source','webp_source_changed','webp_encode_failed','webp_too_large','metadata_failed','media_changed','image_ambiguous','image_storage','native_post_changed'];
    public static function allowed(): bool {
        return is_admin() && current_user_can('manage_options') && !((function_exists('wp_doing_ajax')&&wp_doing_ajax()) || (function_exists('wp_doing_cron')&&wp_doing_cron()) || (defined('REST_REQUEST')&&REST_REQUEST));
    }
    public static function data(): array {
        if(!self::allowed())return [];
        $preferred='unknown';$supported=null;
        try {
            $args=['mime_type'=>'image/webp','methods'=>['load','save','resize','set_quality']];
            if(function_exists('_wp_image_editor_choose')){$chosen=_wp_image_editor_choose($args);$preferred=['WP_Image_Editor_Imagick'=>'imagick','WP_Image_Editor_GD'=>'gd'][$chosen]??($chosen?'other':'none');}
            if(function_exists('wp_image_editor_supports'))$supported=(bool)wp_image_editor_supports($args);
        } catch(Throwable $ignored) {}
        $imagickWebP=null;if(class_exists('Imagick',false)){try{$imagickWebP=(bool)Imagick::queryFormats('WEBP');}catch(Throwable $ignored){}}
        $errors=[];$limited=false;$valid=true;
        foreach(['old44'=>'zps_article_photos_20261010_state','published5788'=>'zps_published_prepared_photos_20261010_data'] as $queue=>$option){
            $stored=get_option($option,[]);$jobs=is_array($stored)?(array_key_exists('jobs',$stored)?$stored['jobs']:[]):null;
            if(!is_array($jobs)){$valid=false;continue;}$limited=$limited||count($jobs)>100;
            foreach(array_slice($jobs,-100,null,true)as$job){
                if(!is_array($job)||!is_string($job['error']??null)||!in_array($job['error'],self::ERRORS,true))continue;
                $post=$queue==='published5788'?5788:($job['post_id']??null);if(!is_int($post)||$post<1)continue;
                $at=$job['updated_at']??null;$at=is_int($at)&&$at>0&&$at<=time()+300?$at:null;
                $errors[]=['queue'=>$queue,'post_id'=>$post,'code'=>$job['error'],'updated_at'=>$at];
            }
        }
        usort($errors,static fn($a,$b)=>($b['updated_at']??0)<=>($a['updated_at']??0));if(count($errors)>20)$limited=true;
        return ['preferred_backend'=>$preferred,'wp_webp_supported'=>$supported,'gd_webp_decode'=>function_exists('imagecreatefromwebp'),'gd_webp_encode'=>function_exists('imagewebp'),'imagick_loaded'=>extension_loaded('imagick'),'imagick_webp_supported'=>$imagickWebP,'encoder_version'=>class_exists('ZP_Managed_WebP_20261010')?ZP_Managed_WebP_20261010::VERSION:null,'cap_bytes'=>512000,'errors'=>array_slice($errors,0,20),'limited'=>$limited,'data_valid'=>$valid];
    }
    private static function yes($value): string{return $value===null?'—':($value?'tak':'nie');}
    /** Fixed owned media only: this local probe never uploads, updates a post or calls a provider. */
    public static function probe_data(): array {
        if(!self::allowed()||!current_user_can('upload_files'))throw new RuntimeException('forbidden');
        $id=5858;$prefix='_zp_article_photos_20261010_';$post=get_post($id);$jobId=get_post_meta($id,$prefix.'job',true);$role=get_post_meta($id,$prefix.'role',true);
        $stored=get_option('zps_article_photos_20261010_state',[]);$job=is_array($stored)&&is_string($jobId)?($stored['jobs'][$jobId]??null):null;
        if(!$post||$post->post_type!=='attachment'||$post->post_status!=='inherit'||get_post_mime_type($id)!=='image/webp'||!is_array($job)||($job['id']??null)!==$jobId||$role!=='cover'||!is_int($job['actor']??null)||(int)$post->post_author!==$job['actor']||!is_int($job['post_id']??null)||!current_user_can('edit_post',$job['post_id'])||!is_string($job['before_hash']??null)||get_post_meta($id,$prefix.'source_hash',true)!==$job['before_hash']||!is_string($job['config']['model']??null)||get_post_meta($id,$prefix.'model',true)!==$job['config']['model'])throw new RuntimeException('webp_source');
        $file=get_post_meta($id,$prefix.'original_file',true);$sha=get_post_meta($id,$prefix.'generated_sha',true);
        if(!is_string($file)||!is_string($sha)||!class_exists('ZP_Managed_WebP_20261010'))throw new RuntimeException('webp_source');
        if(!function_exists('wp_tempnam'))require_once ABSPATH.'wp-admin/includes/file.php';
        $r=ZP_Managed_WebP_20261010::encode_file($file,$sha,1536,1024);
        return ['attachment_id'=>5858,'input_bytes'=>filesize($file),'output_bytes'=>$r['byte_count'],'quality'=>$r['quality'],'width'=>$r['width'],'height'=>$r['height'],'preferred_backend'=>self::data()['preferred_backend']];
    }
    public static function probe(): void {
        if(!self::allowed()||!current_user_can('upload_files')||($_SERVER['REQUEST_METHOD']??'')!=='POST'||!is_string($_POST['_wpnonce']??null)||!wp_verify_nonce(wp_unslash($_POST['_wpnonce']),'zp_webp_local_probe'))wp_die('Brak uprawnień lub prawidłowego potwierdzenia.','',['response'=>403]);
        try{$r=self::probe_data();$html='<h1>Lokalna próba WebP · #5858</h1><p>Wejście: '.number_format($r['input_bytes']/1024,1).' KiB; wynik: '.number_format($r['output_bytes']/1024,1).' KiB. Jakość: '.(int)$r['quality'].'; wymiary: '.(int)$r['width'].' × '.(int)$r['height'].'. Preferowany edytor: '.esc_html($r['preferred_backend']).'.</p><p>Bez płatnej generacji, wgrywania plików i zmiany wpisów. Pliki tymczasowe usunięto.</p>';}
        catch(Throwable $e){$code=$e instanceof RuntimeException&&in_array($e->getMessage(),self::ERRORS,true)?$e->getMessage():'operation_failed';$html='<h1>Lokalna próba WebP zatrzymana</h1><p>Kod: <code>'.esc_html($code).'</code>. Nie ponowiono płatnej generacji ani nie zmieniono wpisów.</p>';}
        wp_die($html,'Lokalna próba WebP',['response'=>200,'back_link'=>true]);
    }
    public static function render(): void {
        $page=is_string($_GET['page']??null)?wp_unslash($_GET['page']):'';
        if(!in_array($page,self::PAGES,true)||($_SERVER['REQUEST_METHOD']??'')!=='GET'||!self::allowed())return;$d=self::data();
        echo '<details class="notice notice-info" style="padding:12px"><summary><b>WebP · diagnostyka bez generowania</b></summary><p>Preferowany edytor WordPress: '.esc_html($d['preferred_backend']).'; obsługa WebP: '.self::yes($d['wp_webp_supported']).'. GD: odczyt '.self::yes($d['gd_webp_decode']).', zapis '.self::yes($d['gd_webp_encode']).'. Imagick: '.self::yes($d['imagick_loaded']).', WebP '.self::yes($d['imagick_webp_supported']).'.</p><p>Enkoder: '.esc_html($d['encoder_version']??'brak').'; limit pliku 500 KiB. Możliwości serwera nie potwierdzają wyniku konkretnego zdjęcia. Ten panel niczego nie koduje i nie ponawia płatnego żądania.</p>';
        if(!$d['data_valid'])echo '<p>Nie można odczytać pełnego stanu zapisanych błędów.</p>';
        foreach($d['errors']as$error)echo '<p>#'.(int)$error['post_id'].' · '.esc_html($error['queue']).' · kod: <code>'.esc_html($error['code']).'</code></p>';
        if(!$d['errors']&&$d['data_valid'])echo '<p>Brak zapisanych błędów z listy diagnostycznej.</p>';if($d['limited'])echo '<p>Pokazano ograniczony zestaw zapisów.</p>';
        if(current_user_can('upload_files')){echo '<form method="post" action="'.esc_url(admin_url('admin-post.php')).'"><input type="hidden" name="action" value="zp_webp_local_probe">';wp_nonce_field('zp_webp_local_probe');echo '<p><button class="button">Sprawdź lokalnie istniejące zdjęcie #5858 · bez kosztu API</button></p></form>';}
        echo '</details>';
    }
    public static function register(): void {static $registered=false;if($registered||!is_admin())return;$registered=true;add_action('admin_notices',[self::class,'render'],51);add_action('admin_post_zp_webp_local_probe',[self::class,'probe']);}
}
