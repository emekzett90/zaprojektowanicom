<?php
if (!defined('ABSPATH')) exit;
/** New state owns no legacy identity. The existing campaign lease serialises both engines. */
final class ZPT2_Store {
    const PREFIX='zpt2_';
    private static $token='';
    public static function hash($v): string { return hash('sha256', wp_json_encode($v, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)); }
    /** WordPress returns un-serialized DB scalars as strings after a cache miss. */
    private static function decode(string $key,$value) {
        if($value===['__zpt2_null_v1'=>true])return null;
        if($key==='takeover'){if($value==='1')return true;if($value==='')return false;}
        if(in_array($key,['launch_clock','cursor','worker_last'],true)&&is_string($value)&&preg_match('/^(?:0|[1-9][0-9]*)$/D',$value)&&(string)(int)$value===$value)return(int)$value;
        // Compatibility with an empty SQL value from a previous null pointer rollback.
        if(strpos($key,'active_')===0&&$value==='')return null;
        return$value;
    }
    private static function encode($value) {return$value===null?['__zpt2_null_v1'=>true]:$value;}
    public static function get(string $key, $default=[]) { wp_cache_delete(self::PREFIX.$key,'options');return self::decode($key,get_option(self::PREFIX.$key,$default)); }
    public static function put(string $key, $value): void {
        update_option(self::PREFIX.$key,self::encode($value),false);
        // Serialized equality preserves types and object properties across WordPress unserialize.
        if (serialize(self::get($key,null))!==serialize($value)) throw new RuntimeException('storage_failed');
    }
    public static function legacy(string $name, $default=[]) { wp_cache_delete($name,'options');return get_option($name,$default); }
    public static function legacy_prefix(): string { return 'zps_en_rewrite_20261011_'; }
    public static function lock(callable $fn) {
        if (self::$token!=='') return $fn();
        global $wpdb;$name=ZP_EN_Rewrite_20261011::LOCK;$old=self::legacy($name,null);
        if (is_array($old)&&is_int($old['until']??null)&&$old['until']<time()) {
            $wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->options} WHERE option_name=%s AND option_value=%s",$name,maybe_serialize($old)));wp_cache_delete($name,'options');
        }
        $token=bin2hex(random_bytes(16));$value=['token'=>$token,'until'=>time()+120];
        if (!add_option($name,$value,'',false)) throw new RuntimeException('busy');
        self::$token=$token;
        try { return $fn(); } finally {
            $wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->options} WHERE option_name=%s AND option_value=%s",$name,maybe_serialize($value)));wp_cache_delete($name,'options');self::$token='';
        }
    }
    public static function assert_lock(): void {
        $l=self::legacy(ZP_EN_Rewrite_20261011::LOCK);
        if (self::$token===''||($l['token']??null)!==self::$token||($l['until']??0)<time()) throw new RuntimeException('lease_lost');
    }
    public static function cas(string $key, $old, $new): void {
        self::assert_lock();
        if (self::get($key,null)!==$old) throw new RuntimeException('pointer_changed');
        $raw=self::legacy(self::PREFIX.$key,'__zpt2_missing__');$encoded=self::encode($new);
        if ($old===null && $raw==='__zpt2_missing__') { if (!add_option(self::PREFIX.$key,$encoded,'',false)) throw new RuntimeException('pointer_changed'); }
        else {
            global $wpdb;$n=$wpdb->query($wpdb->prepare("UPDATE {$wpdb->options} SET option_value=%s WHERE option_name=%s AND option_value=%s",maybe_serialize($encoded),self::PREFIX.$key,maybe_serialize($raw)));
            wp_cache_delete(self::PREFIX.$key,'options');
            if ($n!==1) throw new RuntimeException('pointer_changed');
        }
        if (self::get($key,null)!==$new) throw new RuntimeException('storage_failed');
    }
    public static function migration(): array {
        $m=self::get('migration');if ($m) return $m;
        self::assert_lock();$p=self::legacy_prefix();$idx=self::legacy(ZP_EN_Rewrite_20261011::INDEX);
        $id=$idx['current']??'';if (!is_string($id)||!preg_match('/^[a-f0-9]{32}$/D',$id)) throw new RuntimeException('legacy_campaign_missing');
        $r=self::legacy($p.'r_'.$id);$campaign=$r['campaign']??'';
        if (!is_string($campaign)||!preg_match('/^[a-f0-9]{32}$/D',$campaign)) throw new RuntimeException('legacy_campaign_missing');
        $m=['version'=>1,'campaign'=>$campaign,'legacy_current'=>$id,'legacy_index_hash'=>self::hash($idx),'bootstrap'=>self::legacy('zps_en_nightly_20261011_bootstrap'),'created_at'=>time()];
        self::budget($m);self::put('migration',$m);return $m;
    }
    public static function budget(?array $m=null): array {
        $m=$m??self::migration();$b=self::legacy(self::legacy_prefix().'b_'.$m['campaign']);
        if (($b['id']??null)!==$m['campaign']||!is_int($b['limit']??null)||$b['limit']<1||$b['limit']>15000000||!is_int($b['settled']??null)||$b['settled']<0||!is_array($b['reservations']??null)) throw new RuntimeException('budget_invalid');
        foreach ($b['reservations'] as $id=>$amount) if (!is_string($id)||!preg_match('/^[a-f0-9]{32}$/D',$id)||!is_int($amount)||$amount<0) throw new RuntimeException('budget_invalid');
        if ($b['settled']+array_sum($b['reservations'])>$b['limit']) throw new RuntimeException('budget_invalid');
        if (isset($b['zpt2_settled'])&&!is_array($b['zpt2_settled'])) throw new RuntimeException('budget_invalid');
        return $b;
    }
    private static function save_budget(array $b): void {
        self::assert_lock();$name=self::legacy_prefix().'b_'.$b['id'];update_option($name,$b,false);
        if (self::legacy($name)!==$b) throw new RuntimeException('storage_failed');
    }
    public static function reserve(array $r): void {
        $b=self::budget();$id=$r['id'];$amount=ZPT2_API::reservation($r['request']);
        if (isset($b['zpt2_settled'][$id])) throw new RuntimeException('request_changed');
        if (isset($b['reservations'][$id])) { if ($b['reservations'][$id]!==$amount) throw new RuntimeException('request_changed');return; }
        if ($b['settled']+array_sum($b['reservations'])+$amount>$b['limit']) throw new RuntimeException('budget_limit');
        $b['reservations'][$id]=$amount;self::save_budget($b);
    }
    public static function settle(array $r, ?array $usage): void {
        if ($usage===null) return; // Missing usage remains fully reserved, including terminal failures.
        $b=self::budget();$id=$r['id'];$proof=['hash'=>$r['hash'],'usage'=>$usage];
        if (isset($b['zpt2_settled'][$id])) { if ($b['zpt2_settled'][$id]!==$proof) throw new RuntimeException('budget_invalid');return; }
        if (!isset($b['reservations'][$id])||$usage['micro_usd']>$b['reservations'][$id]) throw new RuntimeException('budget_invalid');
        $b['settled']+=$usage['micro_usd'];unset($b['reservations'][$id]);$b['zpt2_settled'][$id]=$proof;self::save_budget($b);
    }
    /** One admission clock, shared with the original Core under its unchanged lease. */
    public static function launch_clock(): int {
        $clock=self::get('launch_clock',0);if(!is_int($clock)||$clock<0||$clock>time()+60)throw new RuntimeException('launch_clock_invalid');return$clock;
    }
    public static function last_launch(): int {
        $last=self::launch_clock();$prefix=self::legacy_prefix();$idx=self::legacy(ZP_EN_Rewrite_20261011::INDEX);$rev=$idx['current']??'';
        if(!is_string($rev)||!preg_match('/^[a-f0-9]{32}$/D',$rev))throw new RuntimeException('legacy_campaign_missing');$r=self::legacy($prefix.'r_'.$rev);
        foreach($r['pages']??[]as$source=>$status){$p=self::legacy($prefix.'p_'.hash('sha256',$rev.'|'.$source));$next=$p['next']??0;foreach(array_unique([max(0,$next-1),$next])as$n){$id=$p['chunks'][$n]??'';if(!$id)continue;$c=self::legacy($prefix.'c_'.$id);if(isset($c['requested_at'])){if(!is_int($c['requested_at'])||$c['requested_at']<1||$c['requested_at']>time()+60)throw new RuntimeException('launch_clock_invalid');$last=max($last,$c['requested_at']);}}}
        return$last;
    }
    public static function can_launch(): bool {return self::last_launch()<=time()-5;}
    public static function legacy_launch(): bool {
        $lock=self::legacy(ZP_EN_Rewrite_20261011::LOCK);if(!is_array($lock)||!is_string($lock['token']??null)||!preg_match('/^[a-f0-9]{32}$/D',$lock['token'])||($lock['until']??0)<=time())throw new RuntimeException('busy');
        if(!self::can_launch())return false;
        // Ceiling preserves at least five elapsed seconds despite the legacy integer timestamp contract.
        self::put('launch_clock',(int)ceil(microtime(true)));return true;
    }
    /** Read only: the same Warsaw day and both admission ledgers used by v2. */
    public static function chars_status(): array {
        $date=(new DateTimeImmutable('now',new DateTimeZone('Europe/Warsaw')))->format('Y-m-d');
        $out=['date'=>$date,'limit'=>null,'used'=>null,'legacy_used'=>null,'v2_used'=>null,'remaining'=>null,'error'=>''];
        try {
            $limit=\ZPTE\Settings::get('daily_chars');
            if(!is_int($limit)||$limit<1000||$limit>2000000)throw new RuntimeException('chars_config');
            $out['limit']=$limit;$totals=[];
            foreach(['legacy_used'=>self::legacy('zps_en_nightly_20261011_chars'),'v2_used'=>self::get('chars')]as$key=>$ledger){
                if(!is_array($ledger)||!is_array($ledger[$date]??[]))throw new RuntimeException('chars_config');
                $totals[$key]=0;
                foreach($ledger[$date]??[]as$entry){
                    if(!is_array($entry)||!is_int($entry['chars']??null)||$entry['chars']<0)throw new RuntimeException('chars_config');
                    $totals[$key]+=$entry['chars'];
                    if(!is_int($totals[$key]))throw new RuntimeException('chars_config');
                }
            }
            $used=$totals['legacy_used']+$totals['v2_used'];if(!is_int($used))throw new RuntimeException('chars_config');
            $out=array_merge($out,$totals,['used'=>$used,'remaining'=>max(0,$limit-$used)]);
        }catch(Throwable$e){$out['error']='chars_config';}
        return$out;
    }
    public static function chars(array $r): void {
        self::assert_lock();$date=(new DateTimeImmutable('now',new DateTimeZone('Europe/Warsaw')))->format('Y-m-d');
        $limit=\ZPTE\Settings::get('daily_chars');if (!is_int($limit)||$limit<1000||$limit>2000000) throw new RuntimeException('chars_config');
        $all=self::get('chars');$legacy=self::legacy('zps_en_nightly_20261011_chars');$day=$all[$date]??[];$count=0;$used=0;
        foreach ($r['items'] as $u) $count+=mb_strlen($u['pl'],'UTF-8');
        $entry=['hash'=>$r['hash'],'chars'=>$count];
        if (isset($day[$r['id']])) { if ($day[$r['id']]!==$entry) throw new RuntimeException('request_changed');return; }
        foreach (array_merge(array_values($day),array_values($legacy[$date]??[])) as $v) { if (!is_array($v)||!is_int($v['chars']??null)||$v['chars']<0) throw new RuntimeException('chars_config');$used+=$v['chars']; }
        if ($used+$count>$limit) throw new RuntimeException('daily_chars_limit');
        $all[$date][$r['id']]=$entry;self::put('chars',$all);
    }
}
