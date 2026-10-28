<?php
/** One OpenAI translation entry. Native translators/language data and handlers are unchanged. */
if (!defined('ABSPATH')) { exit; }

final class ZP_Single_Translator_20261010 {
    public static function allowed(): bool {return is_admin() && current_user_can('manage_options') && class_exists('ZPTE\\Admin');}
    private static function page(): string {return is_string($_GET['page']??null)?wp_unslash($_GET['page']):'';}
    /** Fixed owned URLs only; the existing workspace helper preserves its supported frame context. */
    public static function url(string $page,array $args=[]): string {
        if(!in_array($page,['zp-tlumacz-en','zpl-languages'],true))$page='zp-tlumacz-en';
        return function_exists('zp_admin20_url')?zp_admin20_url($page,$args):add_query_arg(array_merge(['page'=>$page],$args),admin_url('admin.php'));
    }
    /** Old bookmarks preserve manual language tools; never invoke a scanner/paid translator here. */
    public static function destination(string $page,string $tab=''): ?string {
        if($page==='zpl-skaner')return self::url('zpl-languages',['tab'=>'missing']);
        if($page!=='zp-suite-translations')return null;
        if($tab==='corrections')return self::url('zpl-languages',['tab'=>'overrides']);
        if($tab==='language')return self::url('zpl-languages',['tab'=>'geo']);
        if($tab==='scanner')return self::url('zpl-languages',['tab'=>'missing']);
        if($tab==='attention')return self::url('zp-tlumacz-en',['tab'=>'pages','f'=>'error']);
        return self::url('zp-tlumacz-en',['tab'=>'status']);
    }
    public static function redirect(): void {
        if(!self::allowed() || ($_SERVER['REQUEST_METHOD']??'')!=='GET' || (function_exists('wp_doing_ajax')&&wp_doing_ajax()) || (function_exists('wp_doing_cron')&&wp_doing_cron()) || (defined('REST_REQUEST')&&REST_REQUEST))return;
        $tab=is_string($_GET['tab']??null)?wp_unslash($_GET['tab']):'';
        $url=self::destination(self::page(),$tab);if($url!==null){wp_safe_redirect($url,302);exit;}
    }
    /** Do not unregister native callbacks: original forms, nonces, AJAX and load hooks still own the work. */
    public static function menu(): void {
        if(!self::allowed())return;
        global $submenu;
        foreach(array_keys(is_array($submenu)?$submenu:[]) as $parent){remove_submenu_page($parent,'zp-suite-translations');remove_submenu_page($parent,'zpl-skaner');remove_submenu_page($parent,'zp-tlumacz-en');}
        remove_menu_page('zpl-skaner');
        add_submenu_page('zp-suite','Tłumacz EN (OpenAI)','Tłumacz EN','manage_options','zp-tlumacz-en',['ZPTE\\Admin','page']);
        if(function_exists('zp_admin20_nav') && is_array($submenu['zp-suite']??null)){$rank=array_flip(array_column(zp_admin20_nav(),1));usort($submenu['zp-suite'],static fn($a,$b)=>($rank[$a[2]??'']??99)<=>($rank[$b[2]??'']??99));}
    }
    public static function assets(): void {
        if(!self::allowed() || !in_array(self::page(),['zp-tlumacz-en','zpl-languages'],true))return;
        wp_enqueue_script('zp-single-translator-20261010',ZP_SUITE_URL.'assets/admin-single-translator.js',[], '20261010.1',true);
        wp_localize_script('zp-single-translator-20261010','zpSingleTranslator',[
            'page'=>self::page(),
            'tools'=>[
                ['title'=>'Ręczne korekty języka','url'=>self::url('zpl-languages',['tab'=>'overrides'])],
                ['title'=>'Brakujące fragmenty','url'=>self::url('zpl-languages',['tab'=>'missing'])],
                ['title'=>'Adresy PL / EN','url'=>self::url('zpl-languages',['tab'=>'routes'])],
                ['title'=>'Język odwiedzających','url'=>self::url('zpl-languages',['tab'=>'geo'])],
            ],
        ]);
    }
    public static function register(): void {
        static $registered=false;if($registered || !is_admin())return;$registered=true;
        add_action('admin_init',[self::class,'redirect'],100);
        // Core can reject an unregistered legacy scanner page before admin_init.
        add_action('admin_page_access_denied',[self::class,'redirect'],100);
        add_action('admin_menu',[self::class,'menu'],PHP_INT_MAX);
        add_action('admin_enqueue_scripts',[self::class,'assets'],100);
    }
}
