<?php
/** Suppress only Hello's excerpt description after a proved native RankMath description in this head. */
if (!defined('ABSPATH')) { exit; }

final class ZP_Preparation_SEO_Description_20261010 {
    private static bool $registered=false;
    private static array $observed=[];

    /** The current frontend post/page only; no stored content, settings or ownership changes. */
    private static function context(): ?array {
        if(is_admin() || !is_singular(['post','page']) || !defined('RANK_MATH_VERSION')
            || !class_exists('RankMath\\Frontend\\Head',false) || !function_exists('hello_elementor_add_description_meta_tag'))return null;
        if((function_exists('wp_doing_ajax')&&wp_doing_ajax())||(function_exists('wp_doing_cron')&&wp_doing_cron())||(defined('REST_REQUEST')&&REST_REQUEST))return null;
        $id=(int)get_queried_object_id();$post=$id>0?get_post($id):null;
        if(!$post || (int)$post->ID!==$id || !in_array($post->post_type,['post','page'],true)
            || !in_array($post->post_status,['draft','future','publish'],true) || $post->post_password!=='')return null;
        return ['id'=>$id,'type'=>$post->post_type,'status'=>$post->post_status,'head'=>did_action('wp_head')];
    }
    private static function final_attribute_observer(): bool {
        global $wp_filter;$hook=$wp_filter['attribute_escape']??null;
        if(!($hook instanceof WP_Hook)||empty($hook->callbacks[PHP_INT_MAX]))return false;
        $callbacks=$hook->callbacks[PHP_INT_MAX];$last=end($callbacks);
        return is_array($last)&&($last['function']??null)===[__CLASS__,'observe_attribute'];
    }
    /** Paper's earlier esc_attr and arbitrary getter/filter calls are not evidence of a printed tag. */
    private static function native_description_caller(): bool {
        $trace=debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS,12);
        foreach($trace as $index=>$frame){if(($frame['function']??null)!=='esc_attr'||isset($frame['class']))continue;
            $caller=$trace[$index+1]??[];
            return ($caller['class']??null)==='RankMath\\Frontend\\Head'&&($caller['function']??null)==='metadesc';
        }return false;
    }
    /** Observe the final escaped value at the exact native Head::metadesc echo expression, without buffering. */
    public static function observe_attribute($escaped,$original=null) {
        if(!doing_action('wp_head')||!doing_action('rank_math/head')||!self::final_attribute_observer()||!self::native_description_caller())return $escaped;
        $context=self::context();if(!$context)return $escaped;unset(self::$observed[$context['id']]);
        if(is_string($escaped)&&strlen($escaped)<=10000&&trim($escaped)!=='')self::$observed[$context['id']]=$context;
        return $escaped;
    }
    /** Hello Elementor 3.4.7 asks this filter immediately before printing its excerpt tag. */
    public static function hello_description($enabled) {
        if(!$enabled||!doing_action('wp_head'))return $enabled;
        $context=self::context();$seen=$context?(self::$observed[$context['id']]??null):null;
        return $context&&$seen===$context?false:$enabled;
    }
    /** Explicit main-module integration; merely including this file has no hooks or effects. */
    public static function register(): void {
        if(self::$registered)return;self::$registered=true;
        add_filter('attribute_escape',[__CLASS__,'observe_attribute'],PHP_INT_MAX,2);
        add_filter('hello_elementor_description_meta_tag',[__CLASS__,'hello_description'],PHP_INT_MAX,1);
    }
}
