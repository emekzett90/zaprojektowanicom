<?php
/** Restore missing events of an existing authorised queued task. Never creates/retries a provider request. */
if (!defined('ABSPATH')) { exit; }
final class ZP_Admin_Queue_Resume_20261010 {
    private const ACTION='zp_queue_resume_auto';
    private static function allowed(): bool {
        return current_user_can('manage_options') && current_user_can('unfiltered_html') && class_exists('ZP_Content_Auto_Preparation_20261010');
    }
    public static function eligible(): ?array {
        if (!self::allowed()) { return null; }
        $s=ZP_Content_Auto_Preparation_20261010::state();
        if (($s['state']??null)!=='queued' || !empty($s['error']) || !in_array($s['stage']??null,['research','planning','factory'],true) || !is_string($s['id']??null) || strlen($s['id'])>200 || $s['id']==='' || !is_int($s['updated_at']??null) || !is_int($s['actor']??null) || isset($s['flight'])) { return null; }
        $actor=get_userdata($s['actor']);
        if (!$actor || !user_can($actor,'manage_options') || !user_can($actor,'edit_posts') || !user_can($actor,'unfiltered_html')) { return null; }
        if (!has_action(ZP_Content_Auto_Preparation_20261010::WORKER) || !has_action(ZP_Content_Auto_Preparation_20261010::WATCHDOG)) { return null; }
        $worker=wp_next_scheduled(ZP_Content_Auto_Preparation_20261010::WORKER); $watchdog=wp_next_scheduled(ZP_Content_Auto_Preparation_20261010::WATCHDOG);
        if ($worker && $watchdog) { return null; }
        return ['digest'=>hash('sha256',$s['id'].'|'.$s['updated_at']),'worker'=>$worker?:null,'watchdog'=>$watchdog?:null];
    }
    public static function restore(string $digest): bool {
        $before=self::eligible();
        if (!$before || !hash_equals($before['digest'],$digest)) { return false; }
        // The existing watchdog owns its atomic lock and rechecks the task's state.
        // For queued tasks it only restores next worker/watchdog events, never invokes a provider.
        ZP_Content_Auto_Preparation_20261010::watchdog();
        return (bool)wp_next_scheduled(ZP_Content_Auto_Preparation_20261010::WORKER) && (bool)wp_next_scheduled(ZP_Content_Auto_Preparation_20261010::WATCHDOG);
    }
    public static function handle(): void {
        ZP_Content_Preparation_20261010::guard(self::ACTION);
        $digest=is_string($_POST['queue_digest']??null)?wp_unslash($_POST['queue_digest']):'';
        $ok=preg_match('/^[a-f0-9]{64}$/D',$digest)===1 && self::restore($digest);
        $url=function_exists('zp_admin20_url')?zp_admin20_url('zp-suite-prepare'):admin_url('admin.php?page=zp-suite-prepare');
        wp_safe_redirect(add_query_arg('zp_queue_resume',$ok?'restored':'unchanged',$url)); exit;
    }
    public static function render(): void {
        if (!is_admin() || ($_SERVER['REQUEST_METHOD']??'')!=='GET' || ($_GET['page']??null)!=='zp-suite-prepare' || (function_exists('wp_doing_ajax')&&wp_doing_ajax()) || (function_exists('wp_doing_cron')&&wp_doing_cron()) || (defined('REST_REQUEST')&&REST_REQUEST)) { return; }
        $e=self::eligible(); if (!$e) { return; }
        echo '<section class="notice notice-warning" style="padding:16px"><h2>Przywróć pracę oczekującej kolejki</h2><p>Istniejący dobór tematów czeka, ale brakuje zdarzenia wykonania lub kontroli w WordPressie. Ten przycisk naprawia harmonogram tego samego zadania. Nie tworzy nowego zlecenia, nie ponawia żądania OpenAI i nie pobiera fraz Senuto.</p><form method="post" action="'.esc_url(admin_url('admin-post.php')).'"><input type="hidden" name="action" value="'.esc_attr(self::ACTION).'"><input type="hidden" name="queue_digest" value="'.esc_attr($e['digest']).'">';
        wp_nonce_field(self::ACTION); echo '<button class="button button-primary">Przywróć harmonogram kolejki</button></form></section>';
    }
    public static function register(): void {
        add_action('admin_post_'.self::ACTION,[self::class,'handle']);
        if (is_admin()) { add_action('admin_notices',[self::class,'render'],49); }
    }
}
