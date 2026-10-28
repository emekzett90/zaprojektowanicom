<?php
/** Nonpaid, versioned correction of a small, already-completed EN result. Original receipts remain immutable. */
if (!defined('ABSPATH')) { exit; }
final class ZP_EN_Local_Repair_20261011 {
    public const ACTION='zp_en_rewrite_local_repair';
    private const PREFIX='zps_en_local_repair_20261011_';
    private const MAX_UNITS=4;
    private static function hash($value): string { return hash('sha256',(string)wp_json_encode($value)); }
    private static function allowed(): bool { return current_user_can('manage_options')&&current_user_can('edit_posts')&&current_user_can('unfiltered_html'); }
    private static function name(string $id): string { if (preg_match('/^[a-f0-9]{32}$/D',$id)!==1) throw new RuntimeException('queue_changed');return self::PREFIX.$id; }
    private static function read(string $id): array {
        $name=self::name($id);wp_cache_delete($name,'options');$value=get_option($name,[]);if (!is_array($value)) throw new RuntimeException('translation_invalid');return $value;
    }
    /** Only unique known IDs and individually valid output can be retained. */
    public static function plan(array $result,array $items): array {
        if (array_keys($result)!==['items']||!is_array($result['items'])||!array_is_list($result['items'])||count($result['items'])>count($items)||!$items||count($items)>40) throw new RuntimeException('translation_invalid');
        $seen=[];$dirty=[];
        foreach ($result['items'] as $row) {
            if (!is_array($row)) throw new RuntimeException('translation_invalid');$keys=array_keys($row);sort($keys);
            if ($keys!==['en','id']||!is_string($row['id'])||!isset($items[$row['id']])||isset($seen[$row['id']])||!is_string($row['en'])) throw new RuntimeException('translation_invalid');
            $id=$row['id'];$seen[$id]=$row;$item=$items[$id];$en=\ZPL\Html::norm(ZP_EN_Rewrite_20261011::normalise_money_spacing($row['en']));
            if (!ZP_EN_Rewrite_20261011::valid_translation($item['pl'],$en,$item['kind'])) $dirty[$id]=['kind'=>$item['kind'],'pl'=>$item['pl'],'reason'=>'invalid'];
        }
        foreach (array_diff_key($items,$seen) as $id=>$item) $dirty[$id]=['kind'=>$item['kind'],'pl'=>$item['pl'],'reason'=>'missing'];
        if (!$dirty||count($dirty)>self::MAX_UNITS) throw new RuntimeException('translation_invalid');
        return ['dirty'=>$dirty,'seen'=>$seen];
    }
    public static function merge_result(array $result,array $items,array $corrections): array {
        $plan=self::plan($result,$items);$a=array_keys($plan['dirty']);$b=array_keys($corrections);sort($a);sort($b);
        if ($a!==$b) throw new RuntimeException('translation_invalid');$out=['items'=>[]];$bytes=0;
        foreach ($items as $id=>$item) {
            if (isset($plan['dirty'][$id])) {
                $en=$corrections[$id]??null;if (!is_string($en)||strlen($en)>8000) throw new RuntimeException('translation_invalid');$bytes+=strlen($en);
                $en=\ZPL\Html::norm(ZP_EN_Rewrite_20261011::normalise_money_spacing($en));
                if (!ZP_EN_Rewrite_20261011::valid_translation($item['pl'],$en,$item['kind'])) throw new RuntimeException('translation_invalid');$row=['id'=>$id,'en'=>$en];
            } else $row=$plan['seen'][$id];
            $out['items'][]=$row;
        }
        if ($bytes>12000) throw new RuntimeException('translation_invalid');ZP_EN_Rewrite_20261011::validate_result($out,$items);return $out;
    }
    /** Only extra IDs absent from the exact request may be removed; every expected output must already be valid. */
    public static function extra_plan(array $result,array $items): array {
        if (array_keys($result)!==['items']||!is_array($result['items'])||!array_is_list($result['items'])||!$items||count($items)>40
            ||count($result['items'])<=count($items)||count($result['items'])>count($items)+4||count($result['items'])>44) throw new RuntimeException('translation_invalid');
        $seen=[];$extras=[];$kept=['items'=>[]];
        foreach ($result['items'] as $row) {
            if (!is_array($row)) throw new RuntimeException('translation_invalid');$keys=array_keys($row);sort($keys);
            if ($keys!==['en','id']||!is_string($row['id'])||preg_match('/^t[1-9][0-9]{0,5}$/D',$row['id'])!==1||isset($seen[$row['id']])||!is_string($row['en'])||strlen($row['en'])>8000||!mb_check_encoding($row['en'],'UTF-8')) throw new RuntimeException('translation_invalid');
            $seen[$row['id']]=true;if (isset($items[$row['id']])) $kept['items'][]=$row;else $extras[]=$row['id'];
        }
        if (!$extras||count($extras)>4||array_diff_key($items,$seen)) throw new RuntimeException('translation_invalid');
        ZP_EN_Rewrite_20261011::validate_result($kept,$items);return ['discard_ids'=>$extras,'kept'=>$kept];
    }
    public static function merge_extra_result(array $result,array $items,array $discard_ids): array {
        if (!array_is_list($discard_ids)||!$discard_ids||count($discard_ids)>4||count(array_unique($discard_ids,SORT_REGULAR))!==count($discard_ids)) throw new RuntimeException('translation_invalid');
        foreach ($discard_ids as $id) if (!is_string($id)||preg_match('/^t[1-9][0-9]{0,5}$/D',$id)!==1||isset($items[$id])) throw new RuntimeException('translation_invalid');
        $plan=self::extra_plan($result,$items);$a=$plan['discard_ids'];$b=$discard_ids;sort($a);sort($b);if ($a!==$b) throw new RuntimeException('translation_invalid');return $plan['kept'];
    }
    /** New extra-only POSTs refresh the manual options and key before each full proof reread. */
    private static function extra_context(string $revision,string $source,bool $attention=true): array {
        foreach (['zpl_overrides','zpl_overrides_version',\ZPTE\Settings::KEY_OPTION,'alloptions','notoptions'] as $name) wp_cache_delete($name,'options');
        $context=ZP_EN_Rewrite_Diagnostics_20261011::repair_context($revision,$source,$attention);
        if (($context['revision']['scope']??null)!=='luna_full'||!in_array($context['revision']['error']??null,['','translation_invalid'],true)
            ||($context['revision']['key_fingerprint']??null)!==hash('sha256',\ZPTE\Settings::api_key())) throw new RuntimeException('queue_changed');
        return $context;
    }
    /** A v2 receipt explicitly names every discarded extra ID; paid journal and all requested strings stay immutable. */
    public static function submit_extra(string $revision,string $source,string $fingerprint,array $discard_ids,string $nonce): string {
        if (($_SERVER['REQUEST_METHOD']??'')!=='POST'||!self::allowed()||!wp_verify_nonce($nonce,self::ACTION.'|'.$revision.'|'.$source)) throw new RuntimeException('forbidden');
        if (preg_match('/^[a-f0-9]{64}$/D',$fingerprint)!==1) throw new RuntimeException('queue_changed');$token=self::acquire();
        try {
            $context=self::extra_context($revision,$source);if (!hash_equals(self::fingerprint($context),$fingerprint)) throw new RuntimeException('queue_changed');
            self::merge_extra_result($context['journal']['terminal']['result']??[],$context['chunk']['items'],$discard_ids);ZP_EN_Rewrite_20261011::assert_local_repair_source($context['page']);
            if (self::extra_context($revision,$source)!==$context) throw new RuntimeException('queue_changed');$id=$context['chunk']['id'];$old=self::read($id);
            if ($old) { self::validate_receipt($old,$context);if (($old['version']??null)!==2||($old['discard_ids']??null)!==$discard_ids) throw new RuntimeException('queue_changed');return 'already_saved'; }
            $identity=self::identity($context);$identity['version']=2;$receipt=['version'=>2,'identity'=>$identity,'discard_ids'=>$discard_ids,'actor'=>(int)get_current_user_id(),'at'=>time()];$receipt['seal']=self::hash($receipt);$name=self::name($id);
            if (!add_option($name,$receipt,'',false)) throw new RuntimeException('storage_failed');wp_cache_delete($name,'options');if (get_option($name,[])!==$receipt) throw new RuntimeException('storage_failed');
            ZP_EN_Rewrite_20261011::assert_local_repair_source($context['page']);if (self::extra_context($revision,$source)!==$context) throw new RuntimeException('queue_changed');return 'saved';
        } finally { self::release($token); }
    }
    private static function render_extra(array $context): void {
        $plan=self::extra_plan($context['journal']['terminal']['result']??[],$context['chunk']['items']);$receipt=self::read($context['chunk']['id']);
        if ($receipt) { self::validate_receipt($receipt,$context);echo '<p><strong>Zapisano jawne odrzucenie '.count($receipt['discard_ids']??[]).' dodatkowych ID.</strong> Wszystkie oczekiwane fragmenty zachowano. Użyj istniejącego „Wznów znany odczyt lub lokalne sprawdzenie”.</p>';return; }
        echo '<details><summary>Odrzuć wyłącznie dodatkowe ID — bez API</summary><p>Wszystkie '.count($context['chunk']['items']).' oczekiwane ID są obecne dokładnie raz i przechodzą pełną walidację. Poniższe ID nie występują w żądaniu. Zapis zachowa oryginalny wynik i wszystkie oczekiwane teksty.</p><form method="post" action="'.esc_url(admin_url('admin-post.php')).'">';
        foreach (['action'=>self::ACTION,'revision'=>$context['revision']['id'],'source'=>$context['page']['source'],'fingerprint'=>self::fingerprint($context),'repair_mode'=>'discard_extras_v2'] as $key=>$value) echo '<input type="hidden" name="'.esc_attr($key).'" value="'.esc_attr($value).'">';
        foreach ($plan['discard_ids'] as $id) echo '<p><code>'.esc_html($id).'</code> — brak w żądaniu; źródło PL nie istnieje dla tego ID.</p><input type="hidden" name="discard_ids[]" value="'.esc_attr($id).'">';
        if (class_exists('ZP_Admin_Workspace_20261010')&&ZP_Admin_Workspace_20261010::active()) echo '<input type="hidden" name="_zp_workspace" value="1">';
        wp_nonce_field(self::ACTION.'|'.$context['revision']['id'].'|'.$context['page']['source']);echo '<button type="submit" class="button">Zapisz odrzucenie wyłącznie wskazanych dodatkowych ID</button></form></details>';
    }
    /** Only reviewed, first unrequested title rows may have this non-unit ID; they can never be selected. */
    private static function mapping_row_id_allowed(string $id,int $index,array $items,array $rows): bool {
        if (preg_match('/^t[1-9][0-9]{0,5}$/D',$id)===1) return true;
        if ($id!=='page_title'||$index!==0) return false;
        if (count($items)===40&&count($rows)===41) return true;
        // Exact reviewed terminal Google Ads request: four sources, five physical rows, no general short-batch exception.
        if (array_keys($items)!==['t441','t442','t443','t444']||count($rows)!==5||array_column($rows,'id')!==['page_title','t441','t442','t443','t444']
            ||($rows[0]['en']??null)!=='Google Ads not generating enquiries? Causes and diagnosis') return false;
        foreach (['t441'=>['attr','Ścieżka strony'],'t443'=>['schema','śląskie'],'t444'=>['text','Źródła']] as $unit=>$expected)
            if (($items[$unit]['kind']??null)!==$expected[0]||($items[$unit]['pl']??null)!==$expected[1]) return false;
        return ($items['t442']['kind']??null)==='text'&&is_string($items['t442']['pl']??null)
            &&hash_equals('a480ebda17e4c2a97c0110af7e190fcc92218a5a38031072732e665ddd07aeeb',hash('sha256',$items['t442']['pl']));
    }
    /** Explicit physical-row assignment only: all requested IDs must be mapped once, without inferred shifts or ID deduplication. */
    public static function mapping_plan(array $result,array $items,array $row_mapping): array {
        if (array_keys($result)!==['items']||!is_array($result['items'])||!array_is_list($result['items'])||!$items||count($items)>40
            ||count($result['items'])<=count($items)||count($result['items'])>count($items)+4||count($result['items'])>44) throw new RuntimeException('translation_invalid');
        $a=array_keys($items);$b=array_keys($row_mapping);sort($a);sort($b);if ($a!==$b) throw new RuntimeException('translation_invalid');
        foreach ($result['items'] as $index=>$row) {
            if (!is_array($row)) throw new RuntimeException('translation_invalid');$keys=array_keys($row);sort($keys);
            if ($keys!==['en','id']||!is_string($row['id'])||!self::mapping_row_id_allowed($row['id'],$index,$items,$result['items'])||!is_string($row['en'])||strlen($row['en'])>8000||!mb_check_encoding($row['en'],'UTF-8')) throw new RuntimeException('translation_invalid');
        }
        $used=[];$merged=['items'=>[]];
        foreach ($items as $id=>$item) {
            $n=$row_mapping[$id];if (!is_int($n)||$n<1||$n>count($result['items'])||isset($used[$n])) throw new RuntimeException('translation_invalid');$used[$n]=true;
            if ($result['items'][$n-1]['id']==='page_title') throw new RuntimeException('translation_invalid');
            $merged['items'][]=['id'=>$id,'en'=>$result['items'][$n-1]['en']];
        }
        ZP_EN_Rewrite_20261011::validate_result($merged,$items);$unused=[];for ($n=1;$n<=count($result['items']);$n++) if (!isset($used[$n])) $unused[]=$n;
        if (!$unused||count($unused)>4) throw new RuntimeException('translation_invalid');return ['result'=>$merged,'unused_rows'=>$unused];
    }
    /** A separate v3 receipt records an operator-reviewed complete assignment; no original response is rewritten. */
    public static function submit_mapping(string $revision,string $source,string $fingerprint,array $row_mapping,string $semantic_review,string $nonce): string {
        if (($_SERVER['REQUEST_METHOD']??'')!=='POST'||!self::allowed()||!wp_verify_nonce($nonce,self::ACTION.'|'.$revision.'|'.$source)||$semantic_review!=='all_requested_units') throw new RuntimeException('forbidden');
        if (preg_match('/^[a-f0-9]{64}$/D',$fingerprint)!==1) throw new RuntimeException('queue_changed');$token=self::acquire();
        try {
            $context=self::extra_context($revision,$source);if (!hash_equals(self::fingerprint($context),$fingerprint)) throw new RuntimeException('queue_changed');
            $plan=self::mapping_plan($context['journal']['terminal']['result']??[],$context['chunk']['items'],$row_mapping);ZP_EN_Rewrite_20261011::assert_local_repair_source($context['page']);
            if (self::extra_context($revision,$source)!==$context) throw new RuntimeException('queue_changed');$id=$context['chunk']['id'];$old=self::read($id);
            if ($old) { self::validate_receipt($old,$context);if (($old['version']??null)!==3||($old['row_mapping']??null)!==$row_mapping) throw new RuntimeException('queue_changed');return 'already_saved'; }
            $identity=self::identity($context);$identity['version']=3;$receipt=['version'=>3,'identity'=>$identity,'row_mapping'=>$row_mapping,'unused_rows'=>$plan['unused_rows'],'semantic_review'=>true,'actor'=>(int)get_current_user_id(),'at'=>time()];$receipt['seal']=self::hash($receipt);$name=self::name($id);
            if (!add_option($name,$receipt,'',false)) throw new RuntimeException('storage_failed');wp_cache_delete($name,'options');if (get_option($name,[])!==$receipt) throw new RuntimeException('storage_failed');
            ZP_EN_Rewrite_20261011::assert_local_repair_source($context['page']);if (self::extra_context($revision,$source)!==$context) throw new RuntimeException('queue_changed');return 'saved';
        } finally { self::release($token); }
    }
    private static function render_mapping(array $context): void {
        $result=$context['journal']['terminal']['result']??[];$items=$context['chunk']['items'];
        if (array_keys($result)!==['items']||!is_array($result['items'])||!array_is_list($result['items'])||count($result['items'])<=count($items)||count($result['items'])>count($items)+4||count($result['items'])>44) throw new RuntimeException('translation_invalid');
        foreach ($result['items'] as $index=>$row) { if (!is_array($row)) throw new RuntimeException('translation_invalid');$keys=array_keys($row);sort($keys);if ($keys!==['en','id']||!is_string($row['id'])||!self::mapping_row_id_allowed($row['id'],$index,$items,$result['items'])||!is_string($row['en'])||strlen($row['en'])>8000||!mb_check_encoding($row['en'],'UTF-8')) throw new RuntimeException('translation_invalid'); }
        $old=self::read($context['chunk']['id']);if ($old) { self::validate_receipt($old,$context);if (($old['version']??null)!==3) throw new RuntimeException('queue_changed');echo '<p><strong>Zapisano ręcznie sprawdzone przypisanie wszystkich '.count($items).' źródeł PL.</strong> Oryginalna odpowiedź pozostała bez zmian. Użyj istniejącego wznowienia znanego wyniku.</p>';return; }
        echo '<details><summary>Jawne przypisanie wszystkich fragmentów do wierszy odpowiedzi — bez API</summary><p>Najpierw przeczytaj pełną tabelę PL/EN powyżej. Dla każdego ID źródłowego wybierz numer rzeczywistego wiersza odpowiedzi. Numery wierszy muszą być różne. Niczego nie uzupełniamy na podstawie ID modelu ani domyślnego przesunięcia. Sprawdź znaczenie wszystkich '.count($items).' tekstów, również tych bez błędów liczbowych lub znaczników.</p><form method="post" action="'.esc_url(admin_url('admin-post.php')).'">';
        foreach (['action'=>self::ACTION,'revision'=>$context['revision']['id'],'source'=>$context['page']['source'],'fingerprint'=>self::fingerprint($context),'repair_mode'=>'row_mapping_v3'] as $key=>$value) echo '<input type="hidden" name="'.esc_attr($key).'" value="'.esc_attr($value).'">';
        if (class_exists('ZP_Admin_Workspace_20261010')&&ZP_Admin_Workspace_20261010::active()) echo '<input type="hidden" name="_zp_workspace" value="1">';wp_nonce_field(self::ACTION.'|'.$context['revision']['id'].'|'.$context['page']['source']);
        foreach ($items as $id=>$item) echo '<p><label><strong>'.esc_html($id.' / '.$item['kind']).'</strong> — numer wiersza odpowiedzi <input type="number" name="row_mapping['.esc_attr($id).']" min="1" max="'.count($result['items']).'" step="1" required></label></p>';
        echo '<p><label><input type="checkbox" name="semantic_review" value="all_requested_units" required> Przeczytałem i sprawdziłem znaczenie wszystkich '.count($items).' par PL/EN, nie tylko fragmentów oznaczonych błędem. Każdemu źródłu wybrałem odpowiadający mu wiersz.</label></p><button class="button" type="submit">Zapisz pełne, ręcznie sprawdzone przypisanie bez API</button></form></details>';
    }
    private static function identity(array $context): array {
        $r=$context['revision'];$p=$context['page'];$c=$context['chunk'];$j=$context['journal'];
        return ['version'=>1,'revision'=>$r['id'],'campaign'=>$r['campaign'],'source'=>$p['source'],'post_id'=>$p['post_id'],'actor'=>$r['actor'],'next'=>$p['next'],'chunk'=>$c['id'],
            'source_hash'=>$p['source_hash'],'native_hash'=>$p['native_hash'],'manual_hash'=>$p['manual_hash'],'manual_version'=>$p['manual_version'],'request_hash'=>$c['request_hash'],
            'policy_hash'=>self::hash(ZP_EN_Rewrite_20261011::model_policy($r)),'items_hash'=>self::hash($c['items']),'terminal_hash'=>self::hash($j['terminal']),
            'journal_hash'=>self::hash($j),'response_id'=>$j['response_id']];
    }
    public static function fingerprint(array $context): string {
        $copy=$context;$revision=$copy['revision'];
        $copy['target_status']=$revision['pages'][$copy['page']['source']]??null;
        // Other pages may progress while an editor completes this form. Their counters do not identify this paid result.
        unset($revision['pages'],$revision['state'],$revision['error'],$revision['updated_at'],$revision['last_tick_at']);
        $copy['revision']=$revision;return self::hash($copy);
    }
    private static function validate_receipt(array $receipt,array $context): array {
        if (($receipt['version']??null)===3) {
            $keys=array_keys($receipt);sort($keys);$expected=self::identity($context);$expected['version']=3;$copy=$receipt;unset($copy['seal']);
            if ($keys!==['actor','at','identity','row_mapping','seal','semantic_review','unused_rows','version']||($receipt['identity']??null)!==$expected||($receipt['actor']??null)!==$context['revision']['actor']
                ||!is_int($receipt['at']??null)||$receipt['at']<=0||$receipt['at']>time()+60||($receipt['semantic_review']??null)!==true||!is_array($receipt['row_mapping']??null)||!is_array($receipt['unused_rows']??null)||!is_string($receipt['seal']??null)||!hash_equals(self::hash($copy),$receipt['seal'])) throw new RuntimeException('translation_invalid');
            $plan=self::mapping_plan($context['journal']['terminal']['result']??[],$context['chunk']['items'],$receipt['row_mapping']);if ($receipt['unused_rows']!==$plan['unused_rows']) throw new RuntimeException('translation_invalid');return $plan['result'];
        }
        if (($receipt['version']??null)===2) {
            $keys=array_keys($receipt);sort($keys);$expected=self::identity($context);$expected['version']=2;$copy=$receipt;unset($copy['seal']);
            if ($keys!==['actor','at','discard_ids','identity','seal','version']||($receipt['identity']??null)!==$expected||($receipt['actor']??null)!==$context['revision']['actor']
                ||!is_int($receipt['at']??null)||$receipt['at']<=0||$receipt['at']>time()+60||!is_array($receipt['discard_ids']??null)||!is_string($receipt['seal']??null)||!hash_equals(self::hash($copy),$receipt['seal'])) throw new RuntimeException('translation_invalid');
            return self::merge_extra_result($context['journal']['terminal']['result']??[],$context['chunk']['items'],$receipt['discard_ids']);
        }
        $keys=array_keys($receipt);sort($keys);
        if ($keys!==['actor','at','corrections','identity','seal','version']||($receipt['version']??null)!==1||!is_array($receipt['identity']??null)||$receipt['identity']!==self::identity($context)
            ||($receipt['actor']??null)!==$context['revision']['actor']||!is_int($receipt['at']??null)||$receipt['at']<=0||$receipt['at']>time()+60||!is_array($receipt['corrections']??null)) throw new RuntimeException('translation_invalid');
        $copy=$receipt;unset($copy['seal']);if (!is_string($receipt['seal']??null)||!hash_equals(self::hash($copy),$receipt['seal'])) throw new RuntimeException('translation_invalid');
        return self::merge_result($context['journal']['terminal']['result']??[],$context['chunk']['items'],$receipt['corrections']);
    }
    /** Worker integration is read-only and cannot cause a provider request or budget change. */
    public static function effective_result(array $revision,array $page,array $chunk,array $terminal): array {
        $receipt=self::read($chunk['id']);if (!$receipt) return $terminal['result']??[];
        $context=in_array($receipt['version']??null,[2,3],true)?self::extra_context($revision['id'],$page['source'],false):ZP_EN_Rewrite_Diagnostics_20261011::repair_context($revision['id'],$page['source'],false);
        if (($context['page']['next']??null)!==$page['next']||$context['chunk']['id']!==$chunk['id']||$context['chunk']['request_hash']!==$chunk['request_hash']
            ||self::hash($context['journal']['terminal'])!==self::hash($terminal)||$context['page']['map']!==$page['map']) throw new RuntimeException('queue_changed');
        return self::validate_receipt($receipt,$context);
    }
    private static function acquire(): string {
        global $wpdb;$name=ZP_EN_Rewrite_20261011::LOCK;wp_cache_delete($name,'options');$old=get_option($name,null);
        if (is_array($old)&&is_int($old['until']??null)&&$old['until']<time()) { $wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->options} WHERE option_name=%s AND option_value=%s",$name,maybe_serialize($old)));wp_cache_delete($name,'options'); }
        $token=bin2hex(random_bytes(16));if (!add_option($name,['token'=>$token,'until'=>time()+120],'',false)) throw new RuntimeException('busy');return $token;
    }
    private static function release(string $token): void {
        global $wpdb;$name=ZP_EN_Rewrite_20261011::LOCK;wp_cache_delete($name,'options');$old=get_option($name,null);if (!is_array($old)||($old['token']??null)!==$token) return;
        $wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->options} WHERE option_name=%s AND option_value=%s",$name,maybe_serialize($old)));wp_cache_delete($name,'options');
    }
    /** Save a sidecar only. The operator can then use the existing known-result Resume action. */
    public static function submit(string $revision,string $source,string $fingerprint,array $corrections,string $nonce): string {
        if (($_SERVER['REQUEST_METHOD']??'')!=='POST'||!self::allowed()||!wp_verify_nonce($nonce,self::ACTION.'|'.$revision.'|'.$source)) throw new RuntimeException('forbidden');
        if (preg_match('/^[a-f0-9]{64}$/D',$fingerprint)!==1) throw new RuntimeException('queue_changed');$token=self::acquire();
        try {
            $context=ZP_EN_Rewrite_Diagnostics_20261011::repair_context($revision,$source);if (!hash_equals(self::fingerprint($context),$fingerprint)) throw new RuntimeException('queue_changed');
            self::merge_result($context['journal']['terminal']['result']??[],$context['chunk']['items'],$corrections);ZP_EN_Rewrite_20261011::assert_local_repair_source($context['page']);
            if (ZP_EN_Rewrite_Diagnostics_20261011::repair_context($revision,$source)!==$context) throw new RuntimeException('queue_changed');
            $id=$context['chunk']['id'];$old=self::read($id);
            if ($old) { self::validate_receipt($old,$context);if ($old['corrections']!==$corrections) throw new RuntimeException('queue_changed');return 'already_saved'; }
            $receipt=['version'=>1,'identity'=>self::identity($context),'corrections'=>$corrections,'actor'=>(int)get_current_user_id(),'at'=>time()];$receipt['seal']=self::hash($receipt);$name=self::name($id);
            if (!add_option($name,$receipt,'',false)) throw new RuntimeException('storage_failed');wp_cache_delete($name,'options');if (get_option($name,[])!==$receipt) throw new RuntimeException('storage_failed');
            ZP_EN_Rewrite_20261011::assert_local_repair_source($context['page']);if (ZP_EN_Rewrite_Diagnostics_20261011::repair_context($revision,$source)!==$context) throw new RuntimeException('queue_changed');
            return 'saved';
        } finally { self::release($token); }
    }
    public static function render(string $source): void {
        if (!self::allowed()) return;
        if (($_GET['zp_en_rewrite_notice']??'')==='local_repair_unavailable') echo '<p role="status">Nie potwierdzono zakończenia zapisu korekty. Sprawdź poniższy bieżący stan; nie ponowiono API.</p>';
        $failure=$_GET['zp_en_local_failure']??''; $messages=['busy'=>'Kolejka właśnie wykonuje inny etap. Korekta nie została zapisana; odśwież formularz po zakończeniu tego etapu.', 'forbidden'=>'Nie potwierdzono uprawnień lub ważności formularza. Odśwież panel i formularz.', 'queue_changed'=>'Zmienił się zapis kontroli tej strony. Odśwież formularz i sprawdź aktualny stan.', 'proof_unavailable'=>'Nie potwierdzono kompletnego dowodu wyniku i źródła.', 'translation_invalid'=>'Korekta nie przeszła dotychczasowej walidacji całej partii.', 'storage_failed'=>'Nie potwierdzono trwałego zapisu korekty.', 'source_changed'=>'Zmieniło się źródło strony; korekty nie przyjęto.'];
        if (is_string($failure)&&isset($messages[$failure])) echo '<p role="status">'.esc_html($messages[$failure]).' Nie ponowiono API.</p>';
        try {
            wp_cache_delete(ZP_EN_Rewrite_20261011::INDEX,'options');$index=get_option(ZP_EN_Rewrite_20261011::INDEX,[]);$revision=$index['current']??'';if (!is_string($revision)) return;
            $context=ZP_EN_Rewrite_Diagnostics_20261011::repair_context($revision,$source);
            $result=$context['journal']['terminal']['result']??[];if (is_array($result['items']??null)&&count($result['items'])>count($context['chunk']['items'])) { $context=self::extra_context($revision,$source);try { self::extra_plan($result,$context['chunk']['items']); } catch(Throwable $ignored) { self::render_mapping($context);return; }self::render_extra($context);return; }
            $plan=self::plan($result,$context['chunk']['items']);$receipt=self::read($context['chunk']['id']);
            if ($receipt) { self::validate_receipt($receipt,$context);echo '<p><strong>Zapisano lokalną korektę '.count($receipt['corrections']).' fragmentów.</strong> Oryginalny opłacony wynik zachowano. Użyj „Wznów znany odczyt lub lokalne sprawdzenie”; nie uruchamiaj kampanii ponownie.</p>';return; }
            echo '<details><summary>Lokalna korekta brakujących lub odrzuconych fragmentów — bez API</summary><p>Uzupełnij wyłącznie wskazane fragmenty. Zapis nie powtarza płatnego żądania ani nie zmienia oryginalnego wyniku. Cała partia musi przejść dotychczasowe reguły.</p><form method="post" action="'.esc_url(admin_url('admin-post.php')).'">';
            foreach (['action'=>self::ACTION,'revision'=>$revision,'source'=>$source,'fingerprint'=>self::fingerprint($context)] as $key=>$value) echo '<input type="hidden" name="'.esc_attr($key).'" value="'.esc_attr($value).'">';
            if (class_exists('ZP_Admin_Workspace_20261010')&&ZP_Admin_Workspace_20261010::active()) echo '<input type="hidden" name="_zp_workspace" value="1">';
            wp_nonce_field(self::ACTION.'|'.$revision.'|'.$source);
            $approvedPL='nie kieruj reklam na przypadkową stronę główną, jeśli reklama obiecuje konkretny problem lub usługę.';
            foreach ($plan['dirty'] as $id=>$unit) {
                $en=$unit['kind']==='text'&&$unit['pl']===$approvedPL?'Do not send ads to a random home page if the ad promises to address a specific problem or service.':'';
                echo '<p><label><strong>'.esc_html($id.' / '.$unit['kind'].' / '.($unit['reason']==='missing'?'brak odpowiedzi':'odrzucona odpowiedź')).'</strong><br><code>'.esc_html($unit['pl']).'</code><br>Poprawny tekst EN<textarea name="corrections['.esc_attr($id).']" rows="4" maxlength="8000" required style="display:block;width:100%;font:inherit">'.esc_textarea($en).'</textarea></label></p>';
            }
            echo '<button type="submit" class="button button-primary">Zapisz lokalną korektę bez API</button></form></details>';
        } catch(Throwable $ignored) { echo '<p>Lokalna korekta nie jest dostępna bez pełnego, aktualnego dowodu wyniku i źródła. Nie uruchomiono API.</p>'; }
    }
    public static function handle(): void {
        $notice='local_repair_unavailable';$failure='';
        try {
            foreach (['revision','source','fingerprint','_wpnonce'] as $key) if (!is_string($_POST[$key]??null)) throw new RuntimeException('forbidden');
            $mode=$_POST['repair_mode']??'';if (!is_string($mode)) throw new RuntimeException('translation_invalid');
            if ($mode==='row_mapping_v3') {
                $input=$_POST['row_mapping']??null;$ack=$_POST['semantic_review']??null;if (!is_array($input)||!is_string($ack)||isset($_POST['corrections'])||isset($_POST['discard_ids'])) throw new RuntimeException('translation_invalid');$mapping=[];
                foreach (wp_unslash($input) as $id=>$value) { if (!is_string($id)||!is_string($value)||preg_match('/^[1-9][0-9]?$/D',$value)!==1||(int)$value>44) throw new RuntimeException('translation_invalid');$mapping[$id]=(int)$value; }
                self::submit_mapping(wp_unslash($_POST['revision']),wp_unslash($_POST['source']),wp_unslash($_POST['fingerprint']),$mapping,wp_unslash($ack),wp_unslash($_POST['_wpnonce']));
            }
            elseif ($mode==='discard_extras_v2') { $ids=$_POST['discard_ids']??null;if (!is_array($ids)||isset($_POST['corrections'])) throw new RuntimeException('translation_invalid');self::submit_extra(wp_unslash($_POST['revision']),wp_unslash($_POST['source']),wp_unslash($_POST['fingerprint']),wp_unslash($ids),wp_unslash($_POST['_wpnonce'])); }
            elseif ($mode==='') { $corrections=$_POST['corrections']??null;if (!is_array($corrections)||isset($_POST['discard_ids'])) throw new RuntimeException('translation_invalid');self::submit(wp_unslash($_POST['revision']),wp_unslash($_POST['source']),wp_unslash($_POST['fingerprint']),wp_unslash($corrections),wp_unslash($_POST['_wpnonce'])); }
            else throw new RuntimeException('translation_invalid');$notice='local_repair_saved';
        } catch(Throwable $ignored) { $code=$ignored->getMessage();if (in_array($code,['busy','forbidden','queue_changed','proof_unavailable','translation_invalid','storage_failed','source_changed'],true)) $failure=$code; }
        $url=add_query_arg(['page'=>ZP_EN_Rewrite_20261011::PAGE,'tab'=>'status','zp_en_rewrite_notice'=>$notice,'zp_en_local_failure'=>$failure],admin_url('admin.php'));wp_safe_redirect($url);exit;
    }
    public static function register(): void { add_action('admin_post_'.self::ACTION,[self::class,'handle']); }
}

/** Fixed, human-reviewed nonpaid semantic view of completed Luna pages. Stored maps and paid records are never rewritten. */
if (!defined('ABSPATH')) exit;
final class ZP_EN_Semantic_Repair_20261012 {
    public const ACTION='zp_en_semantic_repair_20261012';
    private const POLICY='fixed-semantic-five-v1';
    private const PREFIX='zps_en_semantic_repair_20261012_';
    /** Diagnostic enums only: never return exception messages, requests or private proofs. */
    private const ERROR_LABELS=[
        'busy'=>'Nie udało się uzyskać lub utrzymać wspólnej blokady. Korekta nie została potwierdzona.',
        'queue_changed'=>'Aktualny dowód lub stan kolejki zmienił się. Korekta nie została potwierdzona.',
        'source_changed'=>'Źródło strony różni się od zapisanego dowodu.',
        'source_unavailable'=>'Nie udało się odczytać publicznego źródła strony.',
        'source_invalid'=>'Publiczne źródło nie spełnia wymaganej postaci lub kontekstu.',
        'translation_invalid'=>'Nie potwierdzono wszystkich dokładnych par tłumaczenia.',
        'connection_changed'=>'Połączenie tłumaczenia różni się od zapisanej kampanii.',
        'manual_changed'=>'Ręczne tłumaczenia zmieniły się od zapisanego dowodu.',
        'forbidden'=>'Nie potwierdzono uprawnień lub autoryzacji tego formularza.',
        'storage_failed'=>'Nie potwierdzono trwałego zapisu korekty.',
        'proof_unavailable'=>'Nie potwierdzono kompletnego zapisanego dowodu.',
        'budget_limit'=>'Nie potwierdzono niezmienionego budżetu kampanii.',
        'response_identity'=>'Tożsamość opłaconej odpowiedzi nie odpowiada zapisanemu dowodowi.',
        'request_changed'=>'Tożsamość przyjętego żądania różni się od zapisanego dowodu.',
        'unavailable'=>'Nie rozpoznano bezpiecznego kodu przyczyny. Pierwotny wynik pozostaje zachowany.',
    ];
    private const CASES=[
        1 => ['source'=>'/strony-internetowe-dla-restauracji/', 'id'=>'t110', 'kind'=>'text', 'pl'=>'Jak chcesz się z nami skontaktować?', 'old'=>'How would you like us to contact you?', 'en'=>'How would you like to contact us?'],
        2 => ['source'=>'/strony-internetowe/woocommerce-czy-prestashop/', 'id'=>'t203', 'kind'=>'attr', 'pl'=>'Pozycjonowanie w AI: jak przygotować firmową stronę do odpowiedzi AI', 'old'=>'AI SEO: how to prepare your business website to answer AI queries', 'en'=>'AI SEO: how to prepare your business website for AI-generated answers'],
        3 => ['source'=>'/strony-internetowe/woocommerce-czy-prestashop/', 'id'=>'t205', 'kind'=>'text', 'pl'=>'Praktycznym rozwiązaniem jest przygotowanie jednej specyfikacji i przekazanie jej wykonawcom obu platform. Dzięki temu można porównywać te same funkcje, integracje i obowiązki utrzymaniowe, zamiast zestawiać dwie nieporównywalne ceny startowe.', 'old'=>'A practical approach is to prepare one specification and give it to providers of both platforms. This makes it possible to compare the same features, integrations and maintenance responsibilities, rather than comparing two like-for-like starting prices that are not actually comparable.', 'en'=>'A practical approach is to prepare one specification and give it to providers of both platforms. This makes it possible to compare the same features, integrations and maintenance responsibilities, rather than comparing two starting prices that are not directly comparable.'],
        4 => ['source'=>'/strony-wordpress/', 'id'=>'t323', 'kind'=>'text', 'pl'=>'Rano', 'old'=>'Rano', 'en'=>'Morning'],
        5 => ['source'=>'/realizacje/', 'id'=>'t819', 'kind'=>'text', 'pl'=>'Studio wyceny', 'old'=>'Valuation studio', 'en'=>'Pricing Studio'],
    ];
    private static function hash($v): string { return hash('sha256',(string)wp_json_encode($v)); }
    private static function allowed(): bool { return current_user_can('manage_options')&&current_user_can('edit_posts')&&current_user_can('unfiltered_html'); }
    private static function read(string $name): array { wp_cache_delete($name,'options');$v=get_option($name,[]);if(!is_array($v))throw new RuntimeException('queue_changed');return $v; }
    private static function name(string $revision,string $source): string { if(preg_match('/^[a-f0-9]{32}$/D',$revision)!==1||!self::cases($source))throw new RuntimeException('scope_invalid');return self::PREFIX.hash('sha256',$revision.'|'.$source); }
    private static function lease_check(array $lease): void { if(!is_string($lease['token']??null)||preg_match('/^[a-f0-9]{32}$/D',$lease['token'])!==1||!is_int($lease['until']??null)||$lease['until']<=time()||self::read(ZP_EN_Rewrite_20261011::LOCK)!==$lease)throw new RuntimeException('busy'); }
    private static function flush_frontend(): void { \ZPL\Dict::flush();if(class_exists('ZP_HTML_Cache_20261009'))ZP_HTML_Cache_20261009::queue_purge(); }
    public static function cases(string $source): array { return array_filter(self::CASES,static fn($c)=>$c['source']===$source); }
    private static function flights(array $page): array {
        if(!is_array($page['chunks']??null)||!array_is_list($page['chunks'])||!$page['chunks']||count($page['chunks'])>1500||count(array_unique($page['chunks'],SORT_STRING))!==count($page['chunks']))throw new RuntimeException('queue_changed');$out=[];$bytes=0;
        foreach($page['chunks']as$id){if(!is_string($id)||preg_match('/^[a-f0-9]{32}$/D',$id)!==1)throw new RuntimeException('queue_changed');$out[]=['chunk'=>self::read('zps_en_rewrite_20261011_c_'.$id),'journal'=>self::read('zps_en_rewrite_20261011_j_'.$id),'repair'=>ZP_EN_Serving_Recovery_20261011::repair_receipt($id)];$bytes+=strlen((string)wp_json_encode(end($out)));if($bytes>16000000)throw new RuntimeException('queue_changed');}return $out;
    }
    /** Stable identity: other pages may progress, but every original paid flight and this entire staged map must remain exact. */
    private static function identity(array $context): array {
        $r=$context['revision'];$p=$context['page'];$policy=ZP_EN_Rewrite_20261011::model_policy($r);
        if(($r['scope']??null)!=='luna_full'||($policy['model']??null)!=='gpt-6-luna'||($policy['reasoning']??null)!=='high'||!self::cases($p['source']??'')||($p['revision']??null)!==$r['id']||($p['actor']??null)!==$r['actor']
            ||!is_int($p['next']??null)||$p['next']!==count($p['chunks'])||!in_array($p['state']??null,['checking_serving','verified'],true)||($p['source_hash']??null)!==self::hash($p['units'])
            ||($p['staged_hash']??null)!==self::hash(['source_hash'=>$p['source_hash'],'native_hash'=>$p['native_hash'],'manual_hash'=>$p['manual_hash'],'map'=>$p['map']]))throw new RuntimeException('queue_changed');
        return ['revision'=>$r['id'],'campaign'=>$r['campaign'],'actor'=>$r['actor'],'key_fingerprint'=>$r['key_fingerprint'],'policy_hash'=>self::hash($policy),'source'=>$p['source'],'post_id'=>$p['post_id'],'en'=>$p['en'],'source_hash'=>$p['source_hash'],'native_hash'=>$p['native_hash'],'manual_hash'=>$p['manual_hash'],'manual_version'=>$p['manual_version'],'units_hash'=>self::hash($p['units']),'map_hash'=>self::hash($p['map']),'staged_hash'=>$p['staged_hash'],'chunks_hash'=>self::hash($p['chunks']),'flights_hash'=>self::hash($context['flights'])];
    }
    /** All fixed IDs must come from their own completed historical chunk; reconstruct its proved effective result, never infer an offset. */
    public static function derive(array $context): array {
        $p=$context['page'];$map=$p['map'];$selected=[];
        foreach(self::cases($p['source'])as$key=>$case){if(isset($context['manual']['map'][$case['pl']])||($p['units'][$case['pl']]??null)!==$case['kind']||($map[$case['pl']]??null)!==$case['old'])throw new RuntimeException('translation_invalid');$found=[];
            foreach($context['flights']as$n=>$flight){$items=$flight['chunk']['items']??[];if(isset($items[$case['id']]))$found[]=[$n,$items[$case['id']]];}
            if(count($found)!==1||$found[0][1]!==['id'=>$case['id'],'pl'=>$case['pl'],'kind'=>$case['kind']])throw new RuntimeException('translation_invalid');$n=$found[0][0];$items=$context['flights'][$n]['chunk']['items'];$result=['items'=>[]];
            foreach($items as$id=>$item){$en=$map[$item['pl']]??null;if(!is_string($en))throw new RuntimeException('translation_invalid');$result['items'][]=['id'=>$id,'en'=>$id===$case['id']?$case['en']:$en];}
            ZP_EN_Rewrite_20261011::validate_result($result,$items);$selected[$key]=['id'=>$case['id'],'chunk'=>$p['chunks'][$n],'ordinal'=>$n,'kind'=>$case['kind'],'pl'=>$case['pl'],'old'=>$case['old'],'en'=>$case['en']];
        }
        if(!$selected||count($selected)>2)throw new RuntimeException('translation_invalid');foreach($selected as$case)$map[$case['pl']]=$case['en'];
        // Validate the whole final result of every touched chunk, including two changes sharing one chunk.
        foreach(array_unique(array_column($selected,'ordinal'))as$n){$items=$context['flights'][$n]['chunk']['items'];$result=['items'=>[]];foreach($items as$id=>$item)$result['items'][]=['id'=>$id,'en'=>$map[$item['pl']]];ZP_EN_Rewrite_20261011::validate_result($result,$items);}
        return ['map'=>$map,'cases'=>$selected];
    }
    public static function fingerprint(array $context): string { return self::hash(self::identity($context)); }
    private static function check_receipt(array $receipt,array $context): array {
        $keys=array_keys($receipt);sort($keys);$copy=$receipt;unset($copy['seal']);$derived=self::derive($context);
        if($keys!==['actor','at','cases','identity','policy','seal','source_context','version']||($receipt['version']??null)!==1||($receipt['policy']??null)!==self::POLICY||($receipt['identity']??null)!==self::identity($context)||($receipt['cases']??null)!==$derived['cases']
            ||($receipt['actor']??null)!==$context['revision']['actor']||!is_int($receipt['at']??null)||$receipt['at']<=0||$receipt['at']>time()+60||!is_string($receipt['seal']??null)||!hash_equals(self::hash($copy),$receipt['seal']))throw new RuntimeException('translation_invalid');
        $proof=$receipt['source_context']??null;$source=$context['page']['source'];$policies=['/strony-wordpress/'=>['callback-time-option-v1','select_sha256'],'/realizacje/'=>['portfolio-pricing-studio-v1','anchor_sha256']];
        if(isset($policies[$source])){[$policy,$key]=$policies[$source];if(!is_array($proof)||array_keys($proof)!==['policy',$key]||$proof['policy']!==$policy||!is_string($proof[$key])||preg_match('/^[a-f0-9]{64}$/D',$proof[$key])!==1)throw new RuntimeException('translation_invalid');}elseif($proof!==[])throw new RuntimeException('translation_invalid');return $derived['map'];
    }
    private static function committed(string $name,array $receipt): bool {
        $v=self::read($name.'_commit');if(!$v)return false;$keys=array_keys($v);sort($keys);$copy=$v;unset($copy['seal']);
        if($keys!==['actor','at','identity','receipt_hash','seal','version']||($v['version']??null)!==1||($v['identity']??null)!==$receipt['identity']||($v['receipt_hash']??null)!==self::hash($receipt)||($v['actor']??null)!==$receipt['actor']||!is_int($v['at']??null)||$v['at']<=0||$v['at']>time()+60||!is_string($v['seal']??null)||!hash_equals(self::hash($copy),$v['seal']))throw new RuntimeException('translation_invalid');return true;
    }
    /** Public serving is read-only. The already-approved original page and every paid digest must still match. */
    public static function map_view(array $page): array {
        if(!self::cases($page['source']??''))return $page['map'];$name=self::name($page['revision'],$page['source']);$receipt=self::read($name);if(!$receipt||!self::committed($name,$receipt))return $page['map'];
        foreach(['zpl_overrides','zpl_overrides_version',\ZPTE\Settings::KEY_OPTION,'alloptions','notoptions']as$n)wp_cache_delete($n,'options');$r=self::read('zps_en_rewrite_20261011_r_'.$page['revision']);$index=self::read(ZP_EN_Rewrite_20261011::INDEX);$current=self::read('zps_en_rewrite_20261011_p_'.hash('sha256',$page['revision'].'|'.$page['source']));
        if($current!==$page||($index['active'][$page['source']]??null)!==$page['revision']||($page['activation']['target']??null)!==$page['revision']||!hash_equals($page['native_hash'],ZP_EN_Rewrite_20261011::native_hash($page['source'])))throw new RuntimeException('source_changed');
        $manual=self::read('zpl_overrides');foreach($manual as$k=>$v)if(!is_string($k)||!is_string($v))throw new RuntimeException('manual_changed');ksort($manual,SORT_STRING);$mv=(string)get_option('zpl_overrides_version','');$key=\ZPTE\Settings::api_key();
        if(self::hash($manual)!==$page['manual_hash']||$mv!==$page['manual_version'])throw new RuntimeException('manual_changed');if($key===''||!hash_equals($r['key_fingerprint']??'',hash('sha256',$key)))throw new RuntimeException('connection_changed');
        wp_cache_delete($r['actor']??0,'users');wp_cache_delete($r['actor']??0,'user_meta');foreach(['manage_options','edit_posts','unfiltered_html','edit_post']as$cap)if(!user_can($r['actor'],$cap,$page['post_id']))throw new RuntimeException('forbidden');
        $ctx=['revision'=>$r,'page'=>$page,'flights'=>self::flights($page),'manual'=>['map'=>$manual,'hash'=>self::hash($manual),'version'=>$mv]];$map=self::check_receipt($receipt,$ctx);
        $lastRevision=self::read('zps_en_rewrite_20261011_r_'.$page['revision']);$lastFlights=self::flights($page);$lastContext=['revision'=>$lastRevision,'page'=>$page,'flights'=>$lastFlights];
        if(self::read($name)!==$receipt||!self::committed($name,$receipt)||self::read('zps_en_rewrite_20261011_p_'.hash('sha256',$page['revision'].'|'.$page['source']))!==$page||(self::read(ZP_EN_Rewrite_20261011::INDEX)['active'][$page['source']]??null)!==$page['revision']||self::identity($lastContext)!==self::identity($ctx)||!hash_equals($page['native_hash'],ZP_EN_Rewrite_20261011::native_hash($page['source'])))throw new RuntimeException('queue_changed');
        foreach(['zpl_overrides','zpl_overrides_version',\ZPTE\Settings::KEY_OPTION,'alloptions','notoptions']as$n)wp_cache_delete($n,'options');$last=self::read('zpl_overrides');ksort($last,SORT_STRING);if($last!==$manual||(string)get_option('zpl_overrides_version','')!==$mv)throw new RuntimeException('manual_changed');if(\ZPTE\Settings::api_key()!==$key)throw new RuntimeException('connection_changed');wp_cache_delete($r['actor'],'users');wp_cache_delete($r['actor'],'user_meta');foreach(['manage_options','edit_posts','unfiltered_html','edit_post']as$cap)if(!user_can($r['actor'],$cap,$page['post_id']))throw new RuntimeException('forbidden');return $map;
    }
    private static function source_context_proof(array $page): array {
        if(!in_array($page['source'],['/strony-wordpress/','/realizacje/'],true))return [];
        $r=wp_safe_remote_get('https://zaprojektowani.com'.$page['source'],['timeout'=>8,'redirection'=>0,'sslverify'=>true,'limit_response_size'=>8000000,'headers'=>['Accept'=>'text/html','Accept-Language'=>'pl-PL'],'cookies'=>['zpl_lang_pref'=>'pl']]);
        if(is_wp_error($r)||wp_remote_retrieve_response_code($r)!==200)throw new RuntimeException('source_unavailable');$html=wp_remote_retrieve_body($r);$type=wp_remote_retrieve_header($r,'content-type');if(!is_string($html)||strlen($html)>8000000||!is_string($type)||stripos($type,'text/html')===false||str_contains($html,'id="wpadminbar"'))throw new RuntimeException('source_invalid');
        if(stripos($html,'</head>')===false)throw new RuntimeException('source_invalid');$robots=wp_remote_retrieve_header($r,'x-robots-tag');foreach(is_array($robots)?$robots:[$robots]as$v)if(is_string($v)&&preg_match('/\b(?:noindex|noarchive)\b/i',$v))throw new RuntimeException('source_invalid');
        preg_match_all('/<meta\b[^>]*>/i',$html,$tags);foreach($tags[0]as$tag){$a=\ZPL\Html::attrs($tag);if(in_array(strtolower($a['name']??''),['robots','googlebot','bingbot'],true)&&preg_match('/\b(?:noindex|noarchive)\b/i',$a['content']??''))throw new RuntimeException('source_invalid');}
        $canonical=[];preg_match_all('/<link\b[^>]*>/i',$html,$tags);foreach($tags[0]as$tag){$a=\ZPL\Html::attrs($tag);if(strtolower($a['rel']??'')==='canonical')$canonical[]=$a['href']??'';}if($canonical!==['https://zaprojektowani.com'.$page['source']])throw new RuntimeException('source_invalid');
        $same=class_exists('ZP_EN_Source_Guard_20261011')?ZP_EN_Source_Guard_20261011::matches($page,$html):self::hash(ZP_EN_Rewrite_20261011::units($html))===$page['source_hash'];if(!$same)throw new RuntimeException('source_changed');
        $d=new DOMDocument();$old=libxml_use_internal_errors(true);try{if(!$d->loadHTML('<?xml encoding="UTF-8">'.$html,LIBXML_NONET))throw new RuntimeException('source_invalid');}finally{libxml_clear_errors();libxml_use_internal_errors($old);}$x=new DOMXPath($d);
        if($page['source']==='/realizacje/'){$anchors=$x->query('//div[contains(concat(" ",normalize-space(@class)," ")," portfolioSeoBridge__cta ")]/a[@href="https://zaprojektowani.com/studio-wyceny/"]');if($anchors->length!==1||\ZPL\Html::norm($anchors->item(0)->textContent)!=='Studio wyceny')throw new RuntimeException('source_invalid');return ['policy'=>'portfolio-pricing-studio-v1','anchor_sha256'=>hash('sha256',$d->saveHTML($anchors->item(0)))];}
        $select=$x->query('//select[@name="callback_time" and @data-preview="callback_time"]');$texts=$x->query('//text()[normalize-space(.)="Rano"]');
        if($select->length!==1||$texts->length!==1||!($texts->item(0)->parentNode instanceof DOMElement)||strtolower($texts->item(0)->parentNode->tagName)!=='option'||$texts->item(0)->parentNode->parentNode!==$select->item(0))throw new RuntimeException('source_invalid');
        return ['policy'=>'callback-time-option-v1','select_sha256'=>hash('sha256',$d->saveHTML($select->item(0)))];
    }
    public static function submit(string $revision,string $source,string $fingerprint,string $ack,string $nonce): string {
        if(($_SERVER['REQUEST_METHOD']??'')!=='POST'||!self::allowed()||!self::cases($source)||$ack!=='fixed_reviewed_semantics'||!wp_verify_nonce($nonce,self::ACTION.'|'.$revision.'|'.$source)||preg_match('/^[a-f0-9]{64}$/D',$fingerprint)!==1)throw new RuntimeException('forbidden');
        return ZP_EN_Rewrite_20261011::nightly_exclusive(static function()use($revision,$source,$fingerprint){$lease=self::read(ZP_EN_Rewrite_20261011::LOCK);self::lease_check($lease);$ctx=ZP_EN_Rewrite_20261011::serving_recovery_context($revision,$source);if(!hash_equals(self::fingerprint($ctx),$fingerprint))throw new RuntimeException('queue_changed');$derived=self::derive($ctx);ZP_EN_Rewrite_20261011::assert_local_repair_source($ctx['page']);$proof=self::source_context_proof($ctx['page']);$fresh=ZP_EN_Rewrite_20261011::serving_recovery_context($revision,$source);if(self::identity($fresh)!==self::identity($ctx))throw new RuntimeException('queue_changed');$name=self::name($revision,$source);$existing=self::read($name);
            if($existing){self::check_receipt($existing,$fresh);if($existing['source_context']!==$proof)throw new RuntimeException('queue_changed');if(self::committed($name,$existing)){self::lease_check($lease);self::flush_frontend();return 'already_saved';}$receipt=$existing;}else{
            $receipt=['version'=>1,'policy'=>self::POLICY,'identity'=>self::identity($fresh),'cases'=>$derived['cases'],'source_context'=>$proof,'actor'=>(int)get_current_user_id(),'at'=>time()];$receipt['seal']=self::hash($receipt);
            self::lease_check($lease);if(!add_option($name,$receipt,'',false)||self::read($name)!==$receipt)throw new RuntimeException('storage_failed');}ZP_EN_Rewrite_20261011::assert_local_repair_source($fresh['page']);if(self::identity(ZP_EN_Rewrite_20261011::serving_recovery_context($revision,$source))!==self::identity($fresh))throw new RuntimeException('queue_changed');
            $commit=['version'=>1,'identity'=>$receipt['identity'],'receipt_hash'=>self::hash($receipt),'actor'=>$receipt['actor'],'at'=>time()];$commit['seal']=self::hash($commit);self::lease_check($lease);if(!add_option($name.'_commit',$commit,'',false)||!self::committed($name,$receipt))throw new RuntimeException('storage_failed');self::lease_check($lease);if(self::identity(ZP_EN_Rewrite_20261011::serving_recovery_context($revision,$source))!==self::identity($fresh))throw new RuntimeException('queue_changed');self::flush_frontend();return 'saved';});
    }
    public static function render(): void {
        if(!is_admin()||($_SERVER['REQUEST_METHOD']??'')!=='GET'||($_GET['page']??'')!==ZP_EN_Rewrite_20261011::PAGE||!self::allowed()||(function_exists('wp_doing_ajax')&&wp_doing_ajax())||(defined('REST_REQUEST')&&REST_REQUEST)||(function_exists('wp_doing_cron')&&wp_doing_cron()))return;
        $notices=['saved'=>'Zapisano dokładnie pokazane korekty bez API. Publiczny odbiór EN pozostaje do potwierdzenia.','already_saved'=>'Te same korekty są już zapisane. Nie ponowiono API.','unavailable'=>'Nie potwierdzono zapisu korekty. Zachowano pierwotną mapę i płatny wynik.'];if(isset($notices[$_GET['zp_en_semantic_notice']??'']))echo '<p role="status">'.esc_html($notices[$_GET['zp_en_semantic_notice']]).'</p>';
        $error=$_GET['zp_en_semantic_error']??'';if(($_GET['zp_en_semantic_notice']??'')==='unavailable'&&is_string($error)&&isset(self::ERROR_LABELS[$error]))echo '<p role="status">'.esc_html('Kod: '.$error.'. '.self::ERROR_LABELS[$error]).'</p>';
        echo '<section><h3>Pięć sprawdzonych korekt znaczenia — bez ponownego tłumaczenia</h3><p>Korekta jest dostępna po ukończeniu całej konkretnej strony. Oryginalne źródło, mapa i opłacone wyniki pozostają zapisane. Publiczny odbiór poprawionego EN należy potwierdzić oddzielnie.</p>';$index=self::read(ZP_EN_Rewrite_20261011::INDEX);$id=$index['current']??'';
        foreach(array_unique(array_column(self::CASES,'source'))as$source){echo '<details><summary>'.esc_html($source).'</summary>';try{$ctx=ZP_EN_Rewrite_20261011::serving_recovery_context($id,$source);self::derive($ctx);$old=self::read(self::name($id,$source));if($old&&self::committed(self::name($id,$source),$old)){self::check_receipt($old,$ctx);echo '<p>Korekta została trwale zapisana. Odbiór publicznej wersji EN pozostaje osobną kontrolą.</p>';}
            else{echo '<form method="post" action="'.esc_url(admin_url('admin-post.php')).'">';foreach(['action'=>self::ACTION,'revision'=>$id,'source'=>$source,'fingerprint'=>self::fingerprint($ctx)]as$k=>$v)echo '<input type="hidden" name="'.esc_attr($k).'" value="'.esc_attr($v).'">';if(class_exists('ZP_Admin_Workspace_20261010')&&ZP_Admin_Workspace_20261010::active())echo '<input type="hidden" name="_zp_workspace" value="1">';wp_nonce_field(self::ACTION.'|'.$id.'|'.$source);
                foreach(self::cases($source)as$c)echo '<p><strong>'.esc_html($c['id'].' / '.$c['kind']).'</strong><br>PL: '.esc_html($c['pl']).'<br>Przed: '.esc_html($c['old']).'<br>Po: '.esc_html($c['en']).'</p>';echo '<p><label><input type="checkbox" name="semantic_review" value="fixed_reviewed_semantics" required> Potwierdzam wszystkie pokazane podmiany znaczenia w tej stronie.</label></p><button class="button" type="submit">Zapisz dokładnie te korekty — bez API</button></form>';}
        }catch(Throwable $ignored){echo '<p>Oczekiwanie na kompletną stronę i pełny, aktualny dowód. Niczego nie zapisano ani nie ponowiono API.</p>';}echo '</details>';}echo '</section>';
    }
    public static function handle(): void {
        $notice='unavailable';$error='';try{foreach(['revision','source','fingerprint','semantic_review','_wpnonce']as$k)if(!is_string($_POST[$k]??null))throw new RuntimeException('forbidden');$notice=self::submit(wp_unslash($_POST['revision']),wp_unslash($_POST['source']),wp_unslash($_POST['fingerprint']),wp_unslash($_POST['semantic_review']),wp_unslash($_POST['_wpnonce']));}catch(Throwable $failure){$code=$failure->getMessage();$error=isset(self::ERROR_LABELS[$code])?$code:'unavailable';}
        $args=['page'=>ZP_EN_Rewrite_20261011::PAGE,'zp_en_semantic_notice'=>$notice];if($notice==='unavailable'&&$error!=='')$args['zp_en_semantic_error']=$error;
        $url=add_query_arg($args,admin_url('admin.php'));if(class_exists('ZP_Admin_Workspace_20261010')&&ZP_Admin_Workspace_20261010::active())$url=add_query_arg('zp_workspace','1',$url);wp_safe_redirect($url);exit;
    }
    public static function register(): void { add_action('admin_post_'.self::ACTION,[self::class,'handle']); }
}

ZP_EN_Semantic_Repair_20261012::register();
