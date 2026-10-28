<?php
/** Explicit nonpaid serving checks of completed Luna pages. GET reads only; original maps/paid receipts stay unchanged. */
if (!defined('ABSPATH')) exit;
final class ZP_EN_Serving_Recovery_20261011 {
    public const ACTION='zp_en_rewrite_serving_recheck';
    private const PREFIX='zps_en_serving_check_20261011_';
    private static function hash($v): string { return hash('sha256',(string)wp_json_encode($v)); }
    private static function allowed(): bool { return current_user_can('manage_options')&&current_user_can('edit_posts')&&current_user_can('unfiltered_html'); }
    private static function read(string $name): array { wp_cache_delete($name,'options');$v=get_option($name,[]);if (!is_array($v)) throw new RuntimeException('queue_changed');return $v; }
    /** Read the existing exact chunk sidecar, without altering it or exposing it to AJAX. */
    public static function repair_receipt(string $id): array { if (preg_match('/^[a-f0-9]{32}$/D',$id)!==1) throw new RuntimeException('queue_changed');return self::read('zps_en_local_repair_20261011_'.$id); }
    public static function fingerprint(array $context): string {
        $r=$context['revision'];unset($r['pages'],$r['state'],$r['error'],$r['updated_at'],$r['last_tick_at']);
        return self::hash(['revision'=>$r,'page'=>$context['page'],'status'=>$context['target_status'],'flights'=>$context['flights'],'manual'=>$context['manual'],'pointer'=>$context['pointer']]);
    }
    /** Every completed paid output (or sealed explicit correction) must equal the stored staged map. */
    public static function validate_context(array $context): void {
        $r=$context['revision'];$p=$context['page'];$paid=[];$ids=[];$total=0;$policy=ZP_EN_Rewrite_20261011::model_policy($r);
        foreach ($context['flights'] as $n=>$flight) {
            $c=$flight['chunk'];$j=$flight['journal'];$cid=$p['chunks'][$n]??null;$items=$c['items']??null;$request=$c['request']??null;
            if (($c['id']??null)!==$cid||($c['revision']??null)!==$r['id']||($c['source']??null)!==$p['source']||($c['key_fingerprint']??null)!==$r['key_fingerprint']||!is_array($items)||!$items||count($items)>40||!is_array($request)
                ||!in_array($c['send_state']??null,['requested','known','authorized_flight'],true)||($request['background']??null)!==true||($request['store']??null)!==true||($request['service_tier']??null)!=='default'||($request['reasoning']['effort']??null)!=='high'||($request['max_output_tokens']??null)!==ZP_EN_Rewrite_Transport_20261011::MAX_OUTPUT
                ||($c['request_hash']??null)!==ZP_EN_Rewrite_Transport_20261011::request_hash($request)||ZP_EN_Rewrite_Transport_20261011::chunk_policy($c)!==$policy) throw new RuntimeException('response_identity');
            foreach (['zp_en_revision'=>$r['id'],'zp_en_chunk'=>$cid,'zp_en_request_hash'=>$c['request_hash']] as $key=>$value) if (($request['metadata'][$key]??null)!==$value) throw new RuntimeException('response_identity');
            $input=is_string($request['input'][1]['content']??null)?json_decode($request['input'][1]['content'],true,32):null;
            if (!is_array($input)||($input['page_title']??null)!==$p['title']||($input['items']??null)!==array_values($items)) throw new RuntimeException('response_identity');
            foreach ($items as $id=>$item) {
                if (!is_string($id)||preg_match('/^t[1-9][0-9]{0,5}$/D',$id)!==1||!is_array($item)||($item['id']??null)!==$id||!is_string($item['pl']??null)||strlen($item['pl'])>8000||($p['units'][$item['pl']]??null)!==($item['kind']??null)||isset($ids[$id])||array_key_exists($item['pl'],$paid)) throw new RuntimeException('response_identity');
                $ids[$id]=true;$total++;if ($total>1500) throw new RuntimeException('response_identity');
            }
            foreach (['chunk_id'=>'id','revision'=>'revision','request_hash'=>'request_hash','key_fingerprint'=>'key_fingerprint'] as $field=>$target) if (($j[$field]??null)!==($c[$target]??null)) throw new RuntimeException('response_identity');
            if (!ZP_EN_Rewrite_Transport_20261011::valid_id($j['response_id']??null)||($c['response_id']??null)!==$j['response_id']||!is_int($j['received_at']??null)||(isset($c['policy_hash'])&&($j['policy_hash']??null)!==$c['policy_hash'])) throw new RuntimeException('response_identity');
            $terminal=$j['terminal']??null;if (!is_array($terminal)||($terminal['status']??null)!=='completed'||($terminal['waiting']??null)!==false||!empty($terminal['error'])||($c['terminal']??null)!==$terminal) throw new RuntimeException('response_identity');
            foreach (['chunk_id','revision','request_hash','key_fingerprint','response_id'] as $field) if (($terminal['receipt'][$field]??null)!==($j[$field]??null)) throw new RuntimeException('response_identity');
            $result=$terminal['result']??null;if (!is_array($result)) throw new RuntimeException('translation_invalid');$repair=$flight['repair'];
            if ($repair) {
                $expected=['version'=>1,'revision'=>$r['id'],'campaign'=>$r['campaign'],'source'=>$p['source'],'post_id'=>$p['post_id'],'actor'=>$r['actor'],'next'=>$n,'chunk'=>$cid,
                    'source_hash'=>$p['source_hash'],'native_hash'=>$p['native_hash'],'manual_hash'=>$p['manual_hash'],'manual_version'=>$p['manual_version'],'request_hash'=>$c['request_hash'],
                    'policy_hash'=>self::hash($policy),'items_hash'=>self::hash($items),'terminal_hash'=>self::hash($terminal),'journal_hash'=>self::hash($j),'response_id'=>$j['response_id']];
                $keys=array_keys($repair);sort($keys);$copy=$repair;unset($copy['seal']);
                if (($repair['version']??null)===3) {
                    $expected['version']=3;
                    if ($keys!==['actor','at','identity','row_mapping','seal','semantic_review','unused_rows','version']||($repair['identity']??null)!==$expected||($repair['actor']??null)!==$r['actor']||!is_int($repair['at']??null)||$repair['at']<=0||$repair['at']>time()+60||($repair['semantic_review']??null)!==true||!is_string($repair['seal']??null)||!hash_equals(self::hash($copy),$repair['seal'])||!is_array($repair['row_mapping']??null)||!is_array($repair['unused_rows']??null)||!class_exists('ZP_EN_Local_Repair_20261011')) throw new RuntimeException('translation_invalid');
                    $plan=ZP_EN_Local_Repair_20261011::mapping_plan($result,$items,$repair['row_mapping']);if ($repair['unused_rows']!==$plan['unused_rows']) throw new RuntimeException('translation_invalid');$result=$plan['result'];
                } elseif (($repair['version']??null)===2) {
                    $expected['version']=2;
                    if ($keys!==['actor','at','discard_ids','identity','seal','version']||($repair['identity']??null)!==$expected||($repair['actor']??null)!==$r['actor']||!is_int($repair['at']??null)||$repair['at']<=0||$repair['at']>time()+60||!is_string($repair['seal']??null)||!hash_equals(self::hash($copy),$repair['seal'])||!is_array($repair['discard_ids']??null)||!class_exists('ZP_EN_Local_Repair_20261011')) throw new RuntimeException('translation_invalid');
                    $result=ZP_EN_Local_Repair_20261011::merge_extra_result($result,$items,$repair['discard_ids']);
                } else {
                    if ($keys!==['actor','at','corrections','identity','seal','version']||($repair['version']??null)!==1||($repair['identity']??null)!==$expected||($repair['actor']??null)!==$r['actor']||!is_int($repair['at']??null)||$repair['at']<=0||$repair['at']>time()+60||!is_string($repair['seal']??null)||!hash_equals(self::hash($copy),$repair['seal'])||!is_array($repair['corrections']??null)||!class_exists('ZP_EN_Local_Repair_20261011')) throw new RuntimeException('translation_invalid');
                    $result=ZP_EN_Local_Repair_20261011::merge_result($result,$items,$repair['corrections']);
                }
            }
            $map=ZP_EN_Rewrite_20261011::validate_result($result,$items);
            foreach ($map as $pl=>$en) { if (!array_key_exists($pl,$p['map'])||$p['map'][$pl]!==$en||array_key_exists($pl,$paid)) throw new RuntimeException('translation_invalid');$paid[$pl]=$en; }
        }
        foreach ($p['map'] as $pl=>$en) if (!is_string($pl)||!is_string($en)||!isset($p['units'][$pl])||!ZP_EN_Rewrite_20261011::valid_translation($pl,$en,$p['units'][$pl])||(!array_key_exists($pl,$paid)&&($context['memory'][$pl]??null)!==$en)) throw new RuntimeException('translation_invalid');
        foreach ($p['units'] as $pl=>$kind) if (!array_key_exists($pl,$p['map'])&&(!isset($context['manual']['map'][$pl])||$context['manual']['map'][$pl]==='')) throw new RuntimeException('translation_invalid');
    }
    private static function name(string $id,string $source): string { return self::PREFIX.hash('sha256',$id.'|'.$source); }
    private static function excerpt(string $text): string {
        $text=\ZPL\Html::norm(wp_strip_all_tags($text));$text=preg_replace('/\b(?:sk-[A-Za-z0-9_-]{8,}|resp_[A-Za-z0-9_-]{6,}|[a-f0-9]{32,})\b/i','[ukryto]',$text)??'';
        $text=preg_replace('/((?:nonce|api[_ -]?key|token|authorization|password|secret)\s*[:=]\s*)[^\s,;]+/i','$1[ukryto]',$text)??'';return mb_substr($text,0,180,'UTF-8');
    }
    private static function safe_report(array $report): array {
        $checks=[];foreach (['marker','lang','h1','description','samples','hreflang'] as $key) $checks[$key]=($report['checks'][$key]??null)===true;
        $rows=[];foreach ($report['samples']??[] as $row) { if (count($rows)>=6) break;if (!in_array($row['kind']??null,['title','meta','text'],true)||!is_int($row['ordinal']??null)||$row['ordinal']<1||$row['ordinal']>2||!is_bool($row['matched']??null)||!is_string($row['en']??null)) continue;$rows[]=['kind'=>$row['kind'],'ordinal'=>$row['ordinal'],'matched'=>$row['matched'],'en'=>self::excerpt($row['en'])]; }
        return ['passed'=>($report['passed']??null)===true,'checks'=>$checks,'samples'=>$rows];
    }
    public static function submit(string $id,string $source,string $fingerprint,string $nonce): array {
        if (($_SERVER['REQUEST_METHOD']??'')!=='POST'||!self::allowed()||!wp_verify_nonce($nonce,self::ACTION.'|'.$id.'|'.$source)||preg_match('/^[a-f0-9]{64}$/D',$fingerprint)!==1) throw new RuntimeException('forbidden');
        $report=ZP_EN_Rewrite_20261011::recheck_serving($id,$source,$fingerprint);$safe=self::safe_report($report);$record=['version'=>1,'at'=>time(),'actor'=>(int)get_current_user_id(),'source'=>$source,'revision'=>$id,'fingerprint'=>$fingerprint,'report'=>$safe];$record['seal']=self::hash($record);
        // Reporting is separate from guarded page completion; an uncertain write never claims a new translation.
        update_option(self::name($id,$source),$record,false);if (self::read(self::name($id,$source))!==$record) throw new RuntimeException('storage_failed');return $safe;
    }
    public static function render(): void {
        if (!is_admin()||($_SERVER['REQUEST_METHOD']??'')!=='GET'||!self::allowed()||(function_exists('wp_doing_ajax')&&wp_doing_ajax())||(defined('REST_REQUEST')&&REST_REQUEST)||(function_exists('wp_doing_cron')&&wp_doing_cron())||($_GET['page']??'')!==ZP_EN_Rewrite_20261011::PAGE) return;
        if (in_array($_GET['zp_en_rewrite_notice']??'',['serving_verified','serving_still_unverified','serving_check_unavailable'],true)) echo '<p role="status">'.esc_html(['serving_verified'=>'Potwierdzono publiczną wersję EN bez ponownego tłumaczenia.','serving_still_unverified'=>'Publiczna wersja nadal nie spełnia pełnej kontroli. Nie zmieniono statusu strony.','serving_check_unavailable'=>'Nie potwierdzono zakończenia sprawdzenia. Nie ponowiono tłumaczenia.'][$_GET['zp_en_rewrite_notice']]).'</p>';
        $index=self::read(ZP_EN_Rewrite_20261011::INDEX);$id=$index['current']??'';if (!is_string($id)||preg_match('/^[a-f0-9]{32}$/D',$id)!==1) return;$r=self::read('zps_en_rewrite_20261011_r_'.$id);$n=0;
        foreach ($r['pages']??[] as $source=>$status) {
            if (!is_string($source)||!isset(ZP_EN_Rewrite_20261011::manifest()[$source])||ZP_EN_Rewrite_20261011::path($source)!==$source||$status!==['state'=>'attention','error'=>'serving_unverified']) continue;if (++$n>3) break;
            echo '<section class="zpAdmin20__card"><h3>Potwierdzenie publicznej wersji EN</h3><p>'.esc_html($source).'</p>';
            try {
                $context=ZP_EN_Rewrite_20261011::serving_recovery_context($id,$source);echo '<p>Ukończone partie i zapisana mapa: zgodne. Wskaźnik publicznej wersji: bieżący. Ten odczyt nie pobiera witryny ani nie wywołuje API.</p>';
                $record=self::read(self::name($id,$source));$sealed=$record;unset($sealed['seal']);if (($record['version']??null)===1&&($record['revision']??null)===$id&&($record['source']??null)===$source&&($record['actor']??null)===(int)get_current_user_id()&&is_int($record['at']??null)&&$record['at']>0&&$record['at']<=time()+60&&($record['fingerprint']??null)===self::fingerprint($context)&&($record['seal']??null)===self::hash($sealed)&&is_array($record['report']??null)&&strlen((string)wp_json_encode($record))<4000) {
                    $a=$record['report'];echo '<p>Ostatnie jawne sprawdzenie: '.esc_html(wp_date('d.m.Y H:i:s',(int)$record['at'],wp_timezone())).'.</p><ul>';
                    foreach ($a['checks']??[] as $key=>$passed) if (in_array($key,['marker','lang','h1','description','samples','hreflang'],true)&&is_bool($passed)) echo '<li>'.esc_html($key).': '.($passed?'PASS':'FAIL').'</li>';echo '</ul>';
                    foreach (array_slice(is_array($a['samples']??null)?$a['samples']:[],0,6) as $row) if (($row['matched']??null)===false) echo '<p>'.esc_html(($row['kind']??'').' #'.(int)($row['ordinal']??0)).': <code>'.esc_html(self::excerpt((string)($row['en']??''))).'</code></p>';
                }
                echo '<form method="post" action="'.esc_url(admin_url('admin-post.php')).'">';foreach (['action'=>self::ACTION,'revision'=>$id,'source'=>$source,'fingerprint'=>self::fingerprint($context)] as $key=>$value) echo '<input type="hidden" name="'.esc_attr($key).'" value="'.esc_attr($value).'">';
                if (class_exists('ZP_Admin_Workspace_20261010')&&ZP_Admin_Workspace_20261010::active()) echo '<input type="hidden" name="_zp_workspace" value="1">';wp_nonce_field(self::ACTION.'|'.$id.'|'.$source);echo '<button class="button" type="submit">Sprawdź publiczną wersję EN — bez ponownego tłumaczenia</button></form><p>Tylko pełny PASS obecnych reguł kończy tę stronę. FAIL zachowuje attention; żaden płatny wynik nie jest ponawiany.</p>';
            } catch(Throwable $ignored) { echo '<p>Brak aktualnego pełnego dowodu. Nie uruchomiono sprawdzenia ani API.</p>'; }
            echo '</section>';
        }
    }
    public static function handle(): void {
        $notice='serving_check_unavailable';try { foreach (['revision','source','fingerprint','_wpnonce'] as $key) if (!is_string($_POST[$key]??null)) throw new RuntimeException('forbidden');$out=self::submit(wp_unslash($_POST['revision']),wp_unslash($_POST['source']),wp_unslash($_POST['fingerprint']),wp_unslash($_POST['_wpnonce']));$notice=$out['passed']?'serving_verified':'serving_still_unverified'; } catch(Throwable $ignored) {}
        $url=add_query_arg(['page'=>ZP_EN_Rewrite_20261011::PAGE,'tab'=>'status','zp_en_rewrite_notice'=>$notice],admin_url('admin.php'));if (class_exists('ZP_Admin_Workspace_20261010')&&ZP_Admin_Workspace_20261010::active()) $url=add_query_arg('zp_workspace','1',$url);wp_safe_redirect($url);exit;
    }
    public static function register(): void { add_action('admin_post_'.self::ACTION,[self::class,'handle']); }
}
