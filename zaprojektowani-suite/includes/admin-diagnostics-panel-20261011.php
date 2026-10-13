<?php
/** Presentation only: relocate ten exact owned diagnostic notices into the Suite closing panel. */
if (!defined('ABSPATH')) { exit; }
final class ZP_Admin_Diagnostics_Panel_20261011 {
    /** Callback names/priorities verified against immutable2.9.46. No prefix-based removal. */
    private const CALLBACKS=[
        'ZP_Published_Prepared_Photo_Refresh_20261010'=>10,
        'ZP_Admin_Queue_Resume_20261010'=>49,
        'ZP_Prepared_Image_Compression_20261010'=>49,
        'ZP_Admin_Queue_Health_20261010'=>50,
        'ZP_Admin_WebP_Diagnostics_20261010'=>51,
        'ZP_Preparation_Review_Diagnostics_20261011'=>51,
        'ZP_Published_Prepared_Image_Compression_20261011'=>51,
        'ZP_Article_Photo_Metadata_Diagnostics_20261011'=>52,
        'ZP_Published_Prepared_Compression_Adapter_20261011'=>53,
        'ZP_Article_Photo_Core_Flags_Reconciliation_20261011'=>54,
    ];
    private const PAGES=['zp-suite','zp-suite-stats','zp-suite-positions','zp-suite-calendar','zp-suite-prepare','zp-suite-article-photos','zp-suite-translations','zp-suite-zapytania','zp-suite-performance','zp-suite-ai','zp-suite-settings','zp-suite-home-cms','zp-suite-publikacja','zp-suite-speed','zp-suite-content-20261009','zp-suite-photos-20261009','zp-suite-html-cache','zp-suite-gsc-quality','zp-suite-gsc-quality-translation','zp-tlumacz-en','zpl-languages','zpl-skaner'];
    private static array $moved=[];
    private static bool $rendered=false;
    private static function eligible():bool {
        return is_admin()&&current_user_can('manage_options')&&($_SERVER['REQUEST_METHOD']??'')==='GET'
            &&is_string($_GET['page']??null)&&in_array(wp_unslash($_GET['page']),self::PAGES,true)
            &&!(function_exists('wp_doing_ajax')&&wp_doing_ajax())&&!(function_exists('wp_doing_cron')&&wp_doing_cron())&&!(defined('REST_REQUEST')&&REST_REQUEST);
    }
    public static function relocate():void {
        if(!self::eligible()||self::$moved)return;global$wp_filter;$hook=$wp_filter['admin_notices']??null;
        if(!($hook instanceof WP_Hook)||!is_array($hook->callbacks))return;
        // Snapshot original priority/insertion order; ordinary success/error/third-party notices stay registered.
        $priorities=$hook->callbacks;ksort($priorities,SORT_NUMERIC);
        foreach($priorities as$priority=>$entries)foreach($entries as$entry){$cb=$entry['function']??null;
            if(!is_array($cb)||count($cb)!==2||!is_string($cb[0]??null)||($cb[1]??null)!=='render'||(self::CALLBACKS[$cb[0]]??null)!==$priority||!is_callable($cb))continue;
            if(remove_action('admin_notices',$cb,$priority))self::$moved[]=$cb;
        }
    }
    /** Native common.js moves div.notice unless it is inline; only captured owned diagnostics are processed. */
    private static function inline_notices(string $content):string {
        if(!class_exists('WP_HTML_Tag_Processor'))return$content;
        $html=new WP_HTML_Tag_Processor($content);
        while($html->next_tag(['tag_name'=>'DIV','class_name'=>'notice']))$html->add_class('inline');
        return$html->get_updated_html();
    }
    public static function render():void {
        if(!self::eligible()||self::$rendered||!self::$moved)return;self::$rendered=true;
        ob_start();try{foreach(self::$moved as$cb)if(is_callable($cb))call_user_func($cb);$content=ob_get_contents();}finally{ob_end_clean();}
        if(!is_string($content)||trim($content)==='')return;
        $content=self::inline_notices($content);
        echo '<style>.zpAdmin20 .zpAdmin20__diagnostics{font-family:inherit;margin:24px 0 0}.zpAdmin20 .zpAdmin20__diagnostics>summary{cursor:pointer;font-weight:700;padding:4px 0}.zpAdmin20 .zpAdmin20__diagnosticsBody{padding-top:12px;min-width:0}.zpAdmin20 .zpAdmin20__diagnosticsBody :where(h1,h2,h3,h4,p,summary,label,button,input,select,textarea,table,th,td,a){font-family:inherit!important}.zpAdmin20 .zpAdmin20__diagnosticsBody .notice{float:none;margin:12px 0}</style>';
        echo '<details class="zpAdmin20__card zpAdmin20__diagnostics" data-zp-diagnostics-relocated><summary>Diagnostyka i naprawy</summary><div class="zpAdmin20__diagnosticsBody">';
        echo $content;
        echo '</div></details>';
    }
    public static function register():void {
        add_action('current_screen',[self::class,'relocate'],PHP_INT_MAX);
        add_action('admin_notices',[self::class,'relocate'],PHP_INT_MIN);
        add_action('zp_admin20_close',[self::class,'render']);
    }
}
