<?php
if (!defined('ABSPATH')) { exit; }
final class ZPSSEO_Automatic {
    const VERSION = '2.2.729';
    const QUEUE = 'zpsseo_auto_queue';
    const HOOK = 'zpsseo_auto_tick';
    public static function init() {
        register_activation_hook(ZP_SUITE_PATH . 'zaprojektowani-suite.php', [__CLASS__,'activate']);
        register_deactivation_hook(ZP_SUITE_PATH . 'zaprojektowani-suite.php', [__CLASS__,'deactivate']);
        add_action('init', [__CLASS__,'upgrade'], 30);
        add_action(self::HOOK, [__CLASS__,'work']);
        add_action('wp_ajax_zpsseo_background', [__CLASS__,'endpoint']);
        add_action('wp_ajax_nopriv_zpsseo_background', [__CLASS__,'endpoint']);
        add_action('admin_init', [__CLASS__,'admin_fallback'], 100);
    }
    public static function activate() {
        if (!ZPSSEO_Repair::allowed()) { return; }
        $s = ZPSSEO_Repair::state(); $s['enabled'] = true; ZPSSEO_Repair::save($s);
        self::start();
    }
    public static function start() {
        $q = ['version'=>self::VERSION,'pending'=>array_keys(ZPSSEO_Repair::jobs()),'attempts'=>[], 'done'=>false,'started'=>time(),'updated'=>time(),'token'=>wp_generate_password(48,false,false),'finished'=>0];
        update_option(self::QUEUE, $q, false);
        self::schedule();
        add_action('shutdown', [__CLASS__,'dispatch']);
    }
    public static function upgrade() {
        if (!ZPSSEO_Repair::allowed() || !ZPSSEO_Repair::state()['enabled']) { return; }
        $q = get_option(self::QUEUE, []);
        if (($q['version'] ?? '') !== self::VERSION) { self::start(); return; }
        if (!empty($q['done']) && ($q['finished'] ?? 0) < time()-7*DAY_IN_SECONDS) { self::start(); return; }
        if (empty($q['done'])) { self::schedule(); }
    }
    public static function schedule() {
        if (!wp_next_scheduled(self::HOOK)) { wp_schedule_single_event(time()+30, self::HOOK); }
    }
    public static function dispatch() {
        $q = get_option(self::QUEUE, []);
        if (empty($q['token']) || !empty($q['done']) || !ZPSSEO_Repair::state()['enabled']) { return; }
        wp_remote_post(admin_url('admin-ajax.php'), ['timeout'=>1,'blocking'=>false,'redirection'=>0,'body'=>['action'=>'zpsseo_background','token'=>$q['token']]]);
    }
    public static function endpoint() {
        $q = get_option(self::QUEUE, []);
        $token = isset($_POST['token']) && is_string($_POST['token']) ? wp_unslash($_POST['token']) : '';
        if (!$token || empty($q['token']) || !hash_equals($q['token'], $token)) { status_header(403); exit; }
        self::work(); status_header(204); exit;
    }
    public static function admin_fallback() {
        if (!current_user_can('manage_options') || wp_doing_ajax()) { return; }
        $q = get_option(self::QUEUE, []);
        // Automatic fallback on an ordinary admin visit if loopback/cron is blocked.
        if ($q && empty($q['done']) && ($q['updated'] ?? 0) < time()-90) { self::work(1); }
    }
    public static function work($limit = 3) {
        if (!ZPSSEO_Repair::allowed() || !ZPSSEO_Repair::state()['enabled']) { return; }
        $q = get_option(self::QUEUE, []);
        if (!$q || !empty($q['done'])) { return; }
        self::schedule(); // Persist a recovery event before doing work.
        $lock = (int)get_option('zpsseo_job_lock',0);
        if ($lock && $lock < time()-180) { delete_option('zpsseo_job_lock'); }
        if (!add_option('zpsseo_job_lock', time(), '', false)) { return; }
        $start = microtime(true); $jobs = ZPSSEO_Repair::jobs();
        try {
            for ($i=0; $i<max(1,min(3,(int)$limit)) && $q['pending'] && microtime(true)-$start<10; $i++) {
                $index = $q['pending'][0];
                $q['attempts'][$index] = ($q['attempts'][$index] ?? 0)+1;
                $q['updated'] = time(); update_option(self::QUEUE,$q,false);
                $s = ZPSSEO_Repair::state();
                if (!$s['enabled']) { break; }
                if ($q['attempts'][$index]>2) { $message='POMINIĘTO: dwie próby przerwane przez serwer. Oryginał pozostawiono.'; }
                else {
                    try { $message = ZPSSEO_Repair::process($jobs[$index],$s); }
                    catch (\Throwable $e) { $message='BŁĄD: '.$e->getMessage(); }
                }
                $s['results'][$jobs[$index]['source']]=$message; $s['enabled']=ZPSSEO_Repair::state()['enabled']; ZPSSEO_Repair::save($s);
                array_shift($q['pending']);
                if ($q['attempts'][$index]<2 && preg_match('/^(BŁĄD|DO SPRAWDZENIA)/u',$message)) { $q['pending'][]=$index; }
                $q['updated']=time(); update_option(self::QUEUE,$q,false);
            }
            if (!$q['pending']) {
                $q['done']=true; $q['finished']=time(); $q['token']='';
                update_option(self::QUEUE,$q,false); wp_clear_scheduled_hook(self::HOOK);
                self::purge();
            }
        } finally { delete_option('zpsseo_job_lock'); }
        if (empty($q['done'])) { self::dispatch(); }
    }
    public static function purge() {
        $log=[];
        foreach (['rocket_clean_domain','w3tc_flush_all','wp_cache_clear_cache'] as $function) {
            if (!function_exists($function)) { continue; }
            try { call_user_func($function); $log[]=$function; }
            catch (\Throwable $e) { $log[]=$function.': '.$e->getMessage(); }
        }
        if (defined('LSCWP_V')) { do_action('litespeed_purge_all','Zaprojektowani Ahrefs Repair'); $log[]='LiteSpeed'; }
        update_option('zpsseo_cache_result', ['time'=>time(),'integrations'=>$log,'external'=>'Zewnętrzny CDN/serwer: brak potwierdzenia czyszczenia; zależy od integracji hostingu.'],false);
    }
    public static function deactivate() { $s=ZPSSEO_Repair::state(); $s['enabled']=false; ZPSSEO_Repair::save($s); if (class_exists('ZPSSEO_Editorial')) { ZPSSEO_Editorial::restore_metadata(); } wp_clear_scheduled_hook(self::HOOK); delete_option(self::QUEUE); self::purge(); }
}
ZPSSEO_Automatic::init();
