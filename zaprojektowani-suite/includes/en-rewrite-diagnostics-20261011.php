<?php
/** Read-only inspection of an already journalled EN result; never fetches or retries a provider. */
if (!defined('ABSPATH')) { exit; }
final class ZP_EN_Rewrite_Diagnostics_20261011 {
    // Request-local diagnostic label only: never persisted and never changes a proof exception or guard.
    private static string $proof_stage='';
    private const PROOF_STAGES=["manifest", "page_record", "page_identity", "actor_capabilities", "units_shape", "units_hash", "native_hash", "manual_shape", "manual_hash", "manual_version", "next_chunk", "chunk_record", "journal_record", "chunk_identity", "request_shape", "request_body", "request_metadata", "request_policy", "items_identity", "items_bound", "request_input", "journal_identity", "journal_response", "key_identity", "journal_policy", "terminal_status", "terminal_identity", "terminal_receipt", "local_analysis", "revision_reread", "page_reread", "chunk_reread", "journal_reread", "native_hash_reread", "current_idx"];
    private const KINDS=['text','title','attr','meta','schema','data'];
    private static function hash($value): string { return hash('sha256',(string)wp_json_encode($value)); }
    private static function read(string $kind,string $id): array {
        if (!in_array($kind,['r','p','c','j'],true)||preg_match('/^[a-f0-9]{32,64}$/D',$id)!==1) throw new RuntimeException('proof_unavailable');
        $name='zps_en_rewrite_20261011_'.$kind.'_'.$id;wp_cache_delete($name,'options');$value=get_option($name,[]);
        if (!is_array($value)) throw new RuntimeException('proof_unavailable');return $value;
    }
    private static function allowed(int $post_id=0): bool {
        return current_user_can('manage_options')&&current_user_can('edit_posts')&&current_user_can('unfiltered_html')&&($post_id===0||current_user_can('edit_post',$post_id));
    }
    /** The same checks as the installed validator, with static reason names instead of values. */
    public static function reasons(string $pl,string $en,string $kind=''): array {
        $out=[];
        if ($en==='') $out[]='empty_translation';
        if (!mb_check_encoding($en,'UTF-8')) return ['invalid_utf8'];
        if (strlen($en)>strlen($pl)*4+100) $out[]='translation_too_long';
        if (strlen($pl)>60&&strlen($en)<strlen($pl)*0.25) $out[]='translation_too_short';
        if (!\ZPL\Html::markers_match($pl,$en)) $out[]='inline_markers';
        $strip=static fn($s)=>preg_replace('/<\/?\d+\/?>/','',$s)??$s;$identity=ZP_EN_Rewrite_20261011::decorative_identity($pl,$en,$kind);
        if (!$identity && preg_match('/<(?!\/?\d+\/?\>)[^>]*>/',$en)) $out[]='unexpected_html';
        if (preg_match('/(?:https?:\/\/(?=[\pL\pN\[])|(?<![\pL\pN_])www\.(?=[\pL\pN-]+\.[\pL]{2,}\b))/iu',$en)!==preg_match('/(?:https?:\/\/(?=[\pL\pN\[])|(?<![\pL\pN_])www\.(?=[\pL\pN-]+\.[\pL]{2,}\b))/iu',$pl)) $out[]='url_presence';
        $checks=['url_email_tokens'=>'~https?://(?=[\pL\pN\[])[^\s<>]+|(?<![\pL\pN_])www\.(?=[\pL\pN-]+\.[\pL]{2,}\b)[^\s<>]+|[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}~iu','number_placeholders'=>'/\{\d+\}/','entity_tokens'=>'/&(?:[A-Za-z][A-Za-z0-9]+|#[0-9]+|#x[0-9a-fA-F]+);/','number_tokens'=>'/(?<![\pL\d])\d+(?:[.,]\d+)*(?![\pL\d])/u'];
        foreach ($checks as $name=>$rx) { preg_match_all($rx,$strip($pl),$a);preg_match_all($rx,$strip($en),$b);$a=$a[0];$b=$b[0];sort($a);sort($b);if ($a!==$b) $out[]=$name; }
        if (ZP_EN_Rewrite_20261011::currency_tokens($pl)!==ZP_EN_Rewrite_20261011::currency_tokens($en)) $out[]='currency_tokens';
        foreach (ZP_EN_Rewrite_20261011::brand_counts($pl,$en) as $brand=>$counts) if (($counts['pl']>0)!==($counts['en']>0)) $out[]='brand_'.$brand;
        if (preg_match_all('/(?<!\pL)(?:się|jest|dla|oraz|że|który|która|które|naszą|twoje|będzie|można|podstrony|sprawdź|zamów)(?!\pL)/iu',$en)>=2) $out[]='polish_words';
        if (!$identity&&$pl===$en&&preg_match('/(?<!\pL)(?:strona|strony|projektowanie|sprawdź|zamów|podstrony|dla|się|jest|oraz)(?!\pL)/iu',$pl)) $out[]='unchanged_polish';
        if (($out===[])!==ZP_EN_Rewrite_20261011::valid_translation($pl,$en,$kind)) return ['validator_rule_changed'];
        return $out;
    }
    private static function excerpt(string $value): string {
        $value=preg_replace_callback('/<(\/?)(\d+)(\/?)>/',static fn($m)=>'⟦'.$m[1].$m[2].$m[3].'⟧',$value)??'';$raw=$value;$value=wp_strip_all_tags($value);
        $fallback=trim($value)==='';if ($fallback) {
            // Display code-only data as inert, escaped text; never accept it as a translation.
            $value=$raw;
        }
        $value=html_entity_decode($value,ENT_QUOTES|ENT_HTML5,'UTF-8');
        if ($fallback) $value=preg_replace('~["\']?(?:api[_ -]?key|access[_ -]?token|refresh[_ -]?token|client[_ -]?secret|authorization|token|password|hasło|nonce)["\']?\s*[:=]\s*(?:"(?:\\\\.|[^"\\\\])*"|\'(?:\\\\.|[^\'\\\\])*\'|[^\s,;}\]]+)~iu','[ukryte]',$value)??'';
        $value=preg_replace('~https?://[^\s<>]+|www\.[^\s<>]+|[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}~','[adres]',$value)??'';
        $value=preg_replace('/\b(?:Bearer\s+\S+|(?:sk-|resp_|req_)[A-Za-z0-9_-]+|[A-Za-z0-9_-]{24,})\b/i','[ukryte]',$value)??'';
        $value=preg_replace('/(?:api[_ -]?key|token|password|hasło|nonce)\s*[:=]\s*\S+/iu','[ukryte]',$value)??'';
        $value=preg_replace('/[\x00-\x1f\x7f]|\s+/u',' ',$value)??'';
        $value=trim($value);return mb_strlen($value,'UTF-8')>180?mb_substr($value,0,180,'UTF-8').'…':$value;
    }
    /** Bounded extra-only inspection, not acceptance: expected IDs and translations must already be complete and valid. */
    public static function inspect_extra($result,array $items): array {
        $out=['eligible'=>false,'units'=>[]];
        if (!is_array($result)||array_keys($result)!==['items']||!is_array($result['items'])||!array_is_list($result['items'])||!$items||count($items)>40
            ||count($result['items'])<=count($items)||count($result['items'])>count($items)+4||count($result['items'])>44) return $out;
        $seen=[];$kept=['items'=>[]];$extras=[];
        foreach ($result['items'] as $row) {
            if (!is_array($row)) return $out;$keys=array_keys($row);sort($keys);
            if ($keys!==['en','id']||!is_string($row['id'])||preg_match('/^t[1-9][0-9]{0,5}$/D',$row['id'])!==1||isset($seen[$row['id']])||!is_string($row['en'])||strlen($row['en'])>8000||!mb_check_encoding($row['en'],'UTF-8')) return $out;
            $seen[$row['id']]=true;if (isset($items[$row['id']])) $kept['items'][]=$row;else $extras[]=['id'=>$row['id'],'kind'=>'not_requested','pl'=>null,'en'=>self::excerpt($row['en'])];
        }
        if (!$extras||count($extras)>4||array_diff_key($items,$seen)) return $out;
        try { ZP_EN_Rewrite_20261011::validate_result($kept,$items); } catch(Throwable $ignored) { return $out; }
        return ['eligible'=>true,'units'=>$extras];
    }
    /** Read-only classification even when the extra-only repair cannot be proven. Never deduplicates or accepts output. */
    public static function inspect_overflow($result,array $items): array {
        $out=['rows'=>[],'reasons'=>[]];
        if (!is_array($result)||array_keys($result)!==['items']||!is_array($result['items'])||!array_is_list($result['items'])||!$items||count($items)>40) return $out;
        if (count($result['items'])<=count($items)) return $out;
        if (count($result['items'])>44) { $out['reasons'][]='result_bound';return $out; }
        $seen=[];
        foreach ($result['items'] as $n=>$row) {
            $id=is_array($row)?($row['id']??null):null;$en=is_array($row)?($row['en']??null):null;$keys=is_array($row)?array_keys($row):[];sort($keys);
            $id_valid=is_string($id)&&preg_match('/^t[1-9][0-9]{0,5}$/D',$id)===1;$expected=$id_valid&&isset($items[$id]);$duplicate=$id_valid&&isset($seen[$id]);$reasons=[];
            if (!is_array($row)||$keys!==['en','id']||!is_string($en)) $reasons[]='item_shape';
            if (!$id_valid) $reasons[]='malformed_id';
            if ($duplicate) $reasons[]='duplicate_id';
            if (is_string($en)&&(!mb_check_encoding($en,'UTF-8')||strlen($en)>8000)) $reasons[]='extra_text_bound';
            if ($id_valid) $seen[$id]=true;
            if (!$expected||$duplicate||$reasons) {
                $kind=!$id_valid?'malformed':($duplicate?($expected?'duplicate_expected':'duplicate_extra'):'not_requested');
                if ($expected&&!$duplicate) $kind='malformed_expected';
                $out['rows'][]=['ordinal'=>$n+1,'id'=>is_string($id)?self::excerpt($id):'—','classification'=>$kind,'reasons'=>$reasons,'pl'=>$expected?self::excerpt($items[$id]['pl']):null,'en'=>is_string($en)?self::excerpt($en):'—'];
            }
            $out['reasons']=array_merge($out['reasons'],$reasons);
        }
        if (array_diff_key($items,$seen)) $out['reasons'][]='missing_id';$out['reasons']=array_values(array_unique($out['reasons']));return $out;
    }
    /** Same redaction as excerpts, with a separate 8000-byte ceiling for up to four protected full-unit details. */
    private static function full_text(string $value): string {
        if (strlen($value)>8000||!mb_check_encoding($value,'UTF-8')) return '[fragment przekracza bezpieczny limit]';
        $value=preg_replace_callback('/<(\/?)(\d+)(\/?)>/',static fn($m)=>'⟦'.$m[1].$m[2].$m[3].'⟧',$value)??'';
        $value=html_entity_decode($value,ENT_QUOTES|ENT_HTML5,'UTF-8');
        $value=preg_replace('~["\']?(?:api[_ -]?key|access[_ -]?token|refresh[_ -]?token|client[_ -]?secret|authorization|token|password|hasło|nonce)["\']?\s*[:=]\s*(?:"(?:\\\\.|[^"\\\\])*"|\'(?:\\\\.|[^\'\\\\])*\'|[^\s,;}\]]+)~iu','[ukryte]',$value)??'';
        $value=preg_replace('~https?://[^\s<>]+|www\.[^\s<>]+|[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}~i','[adres]',$value)??'';
        $value=preg_replace('/\b(?:Bearer\s+\S+|(?:sk-|resp_|req_)[A-Za-z0-9_-]+|[A-Za-z0-9_-]{24,})\b/i','[ukryte]',$value)??'';
        $value=preg_replace('/[\x00-\x08\x0b\x0c\x0e-\x1f\x7f]/u',' ',$value)??'';return trim($value);
    }
    private static function overflow_full_units(array $result,array $items): array {
        if (count($result['items'])<=count($items)||count($result['items'])>44) return [];$out=[];$seen=[];
        foreach ($result['items'] as $n=>$row) {
            if (count($out)>=4) break;if (!is_array($row)||!is_string($row['id']??null)||!is_string($row['en']??null)) continue;
            $id=$row['id'];$expected=isset($items[$id]);$duplicate=isset($seen[$id]);$seen[$id]=true;
            $bad=$expected?self::reasons($items[$id]['pl'],\ZPL\Html::norm($row['en']),$items[$id]['kind']):[];
            if ($expected&&!$duplicate&&!$bad) continue;
            $out[]=['ordinal'=>$n+1,'id'=>self::excerpt($id),'kind'=>$expected?$items[$id]['kind']:'not_requested','reasons'=>$duplicate?array_merge(['duplicate_id'],$bad):$bad,'pl'=>$expected?self::full_text($items[$id]['pl']):null,'en'=>self::full_text($row['en'])];
        }return $out;
    }
    /** Pure local shape analysis. Does not repair, settle, persist or accept anything. */
    public static function analyse($result,array $items): array {
        $out=['shape'=>[],'requested_count'=>count($items),'returned_count'=>null,'failed_count'=>0,'missing_count'=>0,'missing_units'=>[],'rows'=>[],'name_checks'=>[],'local_parser_valid'=>false];$out['extra_inspection']=self::inspect_extra($result,$items);$out['overflow_inspection']=self::inspect_overflow($result,$items);$out['overflow_full_units']=[];
        if (!is_array($result)||array_keys($result)!==['items']||!is_array($result['items']??null)||!array_is_list($result['items'])) { $out['shape'][]='result_shape';return $out; }
        $out['returned_count']=count($result['items']);if (count($result['items'])!==count($items)) $out['shape'][]='item_count';
        if (count($result['items'])>40) $out['shape'][]='result_bound';
        if (count($result['items'])>44) return $out;
        $seen=[];
        foreach ($result['items'] as $item) {
            if (!is_array($item)) { $out['shape'][]='item_shape';continue; }$keys=array_keys($item);sort($keys);
            if ($keys!==['en','id']||!is_string($item['id']??null)||!is_string($item['en']??null)) { $out['shape'][]='item_shape';continue; }
            $id=$item['id'];if (!isset($items[$id])) { $out['shape'][]='unknown_id';continue; }if (isset($seen[$id])) { $out['shape'][]='duplicate_id';continue; }$seen[$id]=true;
            $en=\ZPL\Html::norm($item['en']);$pl=$items[$id]['pl'];$kind=$items[$id]['kind']??'';$reasons=self::reasons($pl,$en,is_string($kind)?$kind:'');$counts=ZP_EN_Rewrite_20261011::brand_counts($pl,$en);$present=array_filter($counts,static fn($c)=>$c['pl']>0||$c['en']>0);
            if ($present&&count($out['name_checks'])<40) $out['name_checks'][]=['id'=>$id,'counts'=>$present];
            if ($reasons) { $out['failed_count']++;if (count($out['rows'])<12) $out['rows'][]=['id'=>$id,'kind'=>$items[$id]['kind'],'reasons'=>$reasons,'brand_counts'=>array_filter($counts,static fn($c)=>$c['pl']>0||$c['en']>0),'pl'=>self::excerpt($pl),'en'=>self::excerpt($en)]; }
        }
        if (count($seen)!==count($items)) {
            $out['shape'][]='missing_id';
            foreach (array_diff_key($items,$seen) as $id=>$unit) {
                $out['missing_count']++;
                if (count($out['missing_units'])<40) $out['missing_units'][]=['id'=>$id,'kind'=>$unit['kind'],'pl'=>self::excerpt($unit['pl'])];
            }
        }$out['shape']=array_values(array_unique($out['shape']));
        try { ZP_EN_Rewrite_20261011::validate_result($result,$items);$out['local_parser_valid']=true; } catch(Throwable $ignored) {}
        $out['overflow_full_units']=self::overflow_full_units($result,$items);return $out;
    }
    private static function page(array $revision,string $source,bool $proof=false): array {
        self::$proof_stage='manifest';$manifest=ZP_EN_Rewrite_20261011::manifest();if (!isset($manifest[$source])) throw new RuntimeException('proof_unavailable');
        self::$proof_stage='page_record';$page=self::read('p',hash('sha256',$revision['id'].'|'.$source));$post_id=$manifest[$source]['post_id'];if ($post_id===0) $post_id=(int)url_to_postid(home_url($source));
        if ($source==='/kampanie-reklamowe/'&&$post_id===0) { $native=get_page_by_path('kampanie-reklamowe',OBJECT,'page');if (is_object($native)&&is_int($native->ID??null)&&$native->ID>0) $post_id=$native->ID; }
        self::$proof_stage='page_identity';if (($page['revision']??null)!==$revision['id']||($page['source']??null)!==$source||($page['en']??null)!==$manifest[$source]['en']||($page['post_id']??null)!==$post_id) throw new RuntimeException('proof_unavailable');
        self::$proof_stage='actor_capabilities';if (!self::allowed($post_id)||($page['actor']??null)!==$revision['actor']||!user_can($revision['actor'],'manage_options')||!user_can($revision['actor'],'edit_posts')||!user_can($revision['actor'],'unfiltered_html')||!user_can($revision['actor'],'edit_post',$post_id)) throw new RuntimeException('proof_unavailable');
        self::$proof_stage='units_shape';if (!is_array($page['units']??null)||count($page['units'])>1500||strlen((string)wp_json_encode($page['units']))>1000000) throw new RuntimeException('proof_unavailable');
        self::$proof_stage='units_hash';if (($page['source_hash']??null)!==self::hash($page['units'])) throw new RuntimeException('proof_unavailable');
        self::$proof_stage='native_hash';if (!is_string($page['native_hash']??null)||!hash_equals($page['native_hash'],ZP_EN_Rewrite_20261011::native_hash($source))) throw new RuntimeException('proof_unavailable');
        self::$proof_stage='manual_shape';$manual=get_option('zpl_overrides',[]);if (!is_array($manual)) throw new RuntimeException('proof_unavailable');foreach ($manual as $k=>$v) if (!is_string($k)||!is_string($v)) throw new RuntimeException('proof_unavailable');ksort($manual,SORT_STRING);
        self::$proof_stage='manual_hash';if (($page['manual_hash']??null)!==self::hash($manual)) throw new RuntimeException('proof_unavailable');
        self::$proof_stage='manual_version';if (($page['manual_version']??null)!==(string)get_option('zpl_overrides_version','')) throw new RuntimeException('proof_unavailable');
        self::$proof_stage='next_chunk';$next=$page['next']??null;if (!is_int($next)||$next<0||!is_array($page['chunks']??null)||!array_is_list($page['chunks'])||count($page['chunks'])>1500||!isset($page['chunks'][$next])||!is_string($page['chunks'][$next])) throw new RuntimeException('proof_unavailable');
        $id=$page['chunks'][$next];self::$proof_stage='chunk_record';$chunk=self::read('c',$id);self::$proof_stage='journal_record';$journal=self::read('j',$id);
        self::$proof_stage='chunk_identity';foreach (['id'=>$id,'revision'=>$revision['id'],'source'=>$source] as $key=>$value) if (($chunk[$key]??null)!==$value) throw new RuntimeException('proof_unavailable');
        $request=$chunk['request']??null;$items=$chunk['items']??null;
        self::$proof_stage='request_shape';if (!is_array($request)||!is_array($items)||!$items||count($items)>40||strlen((string)wp_json_encode($items))>100000) throw new RuntimeException('proof_unavailable');
        self::$proof_stage='request_body';if (($chunk['request_hash']??null)!==ZP_EN_Rewrite_Transport_20261011::request_hash($request)) throw new RuntimeException('proof_unavailable');
        self::$proof_stage='request_metadata';if (($request['metadata']['zp_en_request_hash']??null)!==$chunk['request_hash']||($request['metadata']['zp_en_chunk']??null)!==$id||($request['metadata']['zp_en_revision']??null)!==$revision['id']) throw new RuntimeException('proof_unavailable');
        self::$proof_stage='request_policy';if (ZP_EN_Rewrite_Transport_20261011::chunk_policy($chunk)!==ZP_EN_Rewrite_20261011::model_policy($revision)||($request['reasoning']['effort']??null)!=='high'||($request['service_tier']??null)!=='default'||($request['background']??null)!==true||($request['store']??null)!==true) throw new RuntimeException('proof_unavailable');
        self::$proof_stage='items_identity';$bytes=0;foreach ($items as $key=>$item) {
            if (!is_string($key)||preg_match('/^t[1-9][0-9]{0,5}$/D',$key)!==1||!is_array($item)||($item['id']??null)!==$key||!is_string($item['pl']??null)||strlen($item['pl'])>8000||!in_array($item['kind']??null,self::KINDS,true)||($page['units'][$item['pl']]??null)!==$item['kind']) throw new RuntimeException('proof_unavailable');$bytes+=strlen($item['pl']);
        }
        self::$proof_stage='items_bound';if ($bytes>12000) throw new RuntimeException('proof_unavailable');
        self::$proof_stage='request_input';$content=$request['input'][1]['content']??null;$decoded=is_string($content)?json_decode($content,true,32):null;
        if (!is_array($decoded)||($decoded['page_title']??null)!==($page['title']??null)||($decoded['items']??null)!==array_values($items)) throw new RuntimeException('proof_unavailable');
        self::$proof_stage='journal_identity';foreach (['chunk_id'=>'id','revision'=>'revision','request_hash'=>'request_hash','key_fingerprint'=>'key_fingerprint'] as $field=>$target) if (($journal[$field]??null)!==($chunk[$target]??null)) throw new RuntimeException('proof_unavailable');
        self::$proof_stage='journal_response';if (!ZP_EN_Rewrite_Transport_20261011::valid_id($journal['response_id']??null)||!is_int($journal['received_at']??null)||($chunk['response_id']??null)!==$journal['response_id']) throw new RuntimeException('proof_unavailable');
        self::$proof_stage='key_identity';if (($revision['key_fingerprint']??null)!==($chunk['key_fingerprint']??null)) throw new RuntimeException('proof_unavailable');
        self::$proof_stage='journal_policy';if (isset($chunk['policy_hash'])&&($journal['policy_hash']??null)!==$chunk['policy_hash']) throw new RuntimeException('proof_unavailable');
        self::$proof_stage='terminal_status';$terminal=$journal['terminal']??null;if (!is_array($terminal)||($terminal['status']??null)!=='completed'||($terminal['waiting']??null)!==false||!empty($terminal['error'])||strlen((string)wp_json_encode($terminal))>300000) throw new RuntimeException('proof_unavailable');
        self::$proof_stage='terminal_identity';if (isset($chunk['terminal'])&&self::hash($chunk['terminal'])!==self::hash($terminal)) throw new RuntimeException('proof_unavailable');
        self::$proof_stage='terminal_receipt';foreach (['chunk_id'=>'id','revision'=>'revision','request_hash'=>'request_hash','key_fingerprint'=>'key_fingerprint','response_id'=>'response_id'] as $field=>$target) if (($terminal['receipt'][$field]??null)!==($journal[$field]??null)) throw new RuntimeException('proof_unavailable');
        self::$proof_stage='local_analysis';$analysis=self::analyse($terminal['result']??null,$items);
        // Refuse a mixed read if a worker, editor or another tab advanced the stored page meanwhile.
        self::$proof_stage='revision_reread';if (self::read('r',$revision['id'])!==$revision) throw new RuntimeException('proof_unavailable');
        self::$proof_stage='page_reread';if (self::read('p',hash('sha256',$revision['id'].'|'.$source))!==$page) throw new RuntimeException('proof_unavailable');
        self::$proof_stage='chunk_reread';if (self::read('c',$id)!==$chunk) throw new RuntimeException('proof_unavailable');
        self::$proof_stage='journal_reread';if (self::read('j',$id)!==$journal) throw new RuntimeException('proof_unavailable');
        self::$proof_stage='native_hash_reread';if (!hash_equals($page['native_hash'],ZP_EN_Rewrite_20261011::native_hash($source))) throw new RuntimeException('proof_unavailable');
        self::$proof_stage='current_idx';wp_cache_delete(ZP_EN_Rewrite_20261011::INDEX,'options');$current=get_option(ZP_EN_Rewrite_20261011::INDEX,[]);if (($current['current']??null)!==$revision['id']) throw new RuntimeException('proof_unavailable');
        $out=['source'=>$source,'status'=>'available','completed_journal'=>true,'current_source'=>true,'analysis'=>$analysis];
        if ($proof) $out['proof']=['revision'=>$revision,'page'=>$page,'chunk'=>$chunk,'journal'=>$journal];
        self::$proof_stage='';return $out;
    }
    /** PHP-only authenticated context; never added to data()/AJAX or public output. */
    public static function repair_context(string $revision_id,string $source,bool $attention=true): array {
        if (!self::allowed()||preg_match('/^[a-f0-9]{32}$/D',$revision_id)!==1||ZP_EN_Rewrite_20261011::path($source)!==$source) throw new RuntimeException('proof_unavailable');
        wp_cache_delete(ZP_EN_Rewrite_20261011::INDEX,'options');$index=get_option(ZP_EN_Rewrite_20261011::INDEX,[]);
        if (($index['current']??null)!==$revision_id) throw new RuntimeException('queue_changed');$revision=self::read('r',$revision_id);$status=$revision['pages'][$source]??null;
        if (($revision['id']??null)!==$revision_id||($revision['actor']??null)!==(int)get_current_user_id()||!is_array($status)
            ||($attention&&(!in_array($revision['state']??null,['attention','processing','complete_with_issues'],true)||$status!==['state'=>'attention','error'=>'translation_invalid']))
            ||(!$attention&&!in_array($revision['state']??null,['attention','processing','complete_with_issues','translating'],true))) throw new RuntimeException('queue_changed');
        $row=self::page($revision,$source,true);if (($row['proof']['page']['state']??null)!=='translating') throw new RuntimeException('queue_changed');return $row['proof'];
    }
    /** Same bounded current revision read, shared by the diagnostic and its native GET selector. */
    private static function current_revision(): array {
        if (!self::allowed()||!class_exists('ZP_EN_Rewrite_20261011')||!class_exists('ZP_EN_Rewrite_Transport_20261011')||!class_exists('ZPL\\Html')) return [];
        wp_cache_delete(ZP_EN_Rewrite_20261011::INDEX,'options');$index=get_option(ZP_EN_Rewrite_20261011::INDEX,[]);$id=$index['current']??null;
        if (!is_string($id)||preg_match('/^[a-f0-9]{32}$/D',$id)!==1) return [];$revision=self::read('r',$id);
        if (($revision['id']??null)!==$id||!is_int($revision['actor']??null)||!is_array($revision['pages']??null)||count($revision['pages'])>137||!in_array($revision['state']??null,['attention','processing','complete_with_issues'],true)) return [];
        return $revision;
    }
    private static function attention_sources(array $revision): array {
        $out=[];if (!method_exists('ZP_EN_Rewrite_20261011','source_descriptor')) return $out;
        foreach ($revision['pages']??[] as $source=>$status) {
            if (!is_string($source)||!is_array($status)||($status['state']??null)!=='attention'||($status['error']??null)!=='translation_invalid'
                ||preg_match('~^/(?:oferty|briefy)(?:/|$)~i',$source)||ZP_EN_Rewrite_20261011::path($source)!==$source||!isset(ZP_EN_Rewrite_20261011::manifest()[$source])) continue;
            $row=ZP_EN_Rewrite_20261011::source_descriptor($source);
            if (!is_int($row['post_id']??null)||$row['post_id']<=0||!self::allowed($row['post_id'])) continue;
            $out[$source]=true;
        }return $out;
    }
    private static function selected_source(array $sources): ?string {
        if (!is_admin()||($_SERVER['REQUEST_METHOD']??'')!=='GET'||($_GET['page']??'')!=='zp-tlumacz-en'||(function_exists('wp_doing_ajax')&&wp_doing_ajax())||(defined('REST_REQUEST')&&REST_REQUEST)||(function_exists('wp_doing_cron')&&wp_doing_cron())||!array_key_exists('zp_en_diag_source',$_GET)) return null;
        $raw=$_GET['zp_en_diag_source'];if (!is_string($raw)||strlen($raw)>240) throw new RuntimeException('proof_unavailable');$source=wp_unslash($raw);
        if ($source==='') return null;if (!isset($sources[$source])) throw new RuntimeException('proof_unavailable');return $source;
    }
    private static function render_selection(array $sources): void {
        if (!$sources) return;$invalid=false;try { $selected=self::selected_source($sources); } catch(Throwable $ignored) { $selected=null;$invalid=true; }
        echo '<form method="get" action="'.esc_url(admin_url('admin.php')).'"><input type="hidden" name="page" value="zp-tlumacz-en"><input type="hidden" name="tab" value="status"><label>Strona z błędem tłumaczenia <select name="zp_en_diag_source"><option value="">Pierwsze trzy strony</option>';
        foreach ($sources as $source=>$present) echo '<option value="'.esc_attr($source).'"'.($selected===$source?' selected':'').'>'.esc_html($source).'</option>';
        echo '</select></label>';if (($_GET['zp_workspace']??null)==='1') echo '<input type="hidden" name="zp_workspace" value="1">';echo ' <button class="button" type="submit">Odczytaj zapisany wynik</button></form>';
        if ($invalid) echo '<p>Wybrany adres nie jest obecnie dostępny w tej diagnostyce. Nie odczytano jego fragmentów.</p>';
    }
    private static function proof_reason(): ?string {
        if (!is_admin()||($_SERVER['REQUEST_METHOD']??'')!=='GET'||($_GET['page']??'')!=='zp-tlumacz-en'||!self::allowed()||(function_exists('wp_doing_ajax')&&wp_doing_ajax())||(defined('REST_REQUEST')&&REST_REQUEST)||(function_exists('wp_doing_cron')&&wp_doing_cron())) return null;
        return in_array(self::$proof_stage,self::PROOF_STAGES,true)?self::$proof_stage:null;
    }
    public static function data(): array {
        try {
            $revision=self::current_revision();if (!$revision) return [];$sources=self::attention_sources($revision);$selected=self::selected_source($sources);
            $out=[];foreach ($revision['pages'] as $source=>$status) {
                if (!isset($sources[$source])||($selected!==null&&$source!==$selected)) continue;
                try { $out[]=self::page($revision,$source); } catch(Throwable $ignored) { $row=['source'=>$source,'status'=>'proof_unavailable'];$reason=self::proof_reason();if ($reason!==null) $row['proof_reason']=$reason;$out[]=$row; }
                if (count($out)>=3) break;
            }return $out;
        } catch(Throwable $ignored) { return []; }
    }
    private static function label(string $name): string {
        $labels=['malformed_id'=>'nieprawidłowy format ID','extra_text_bound'=>'nieprawidłowe kodowanie lub długość dodatkowego tekstu','duplicate_expected'=>'powtórzone oczekiwane ID','duplicate_extra'=>'powtórzone dodatkowe ID','malformed_expected'=>'oczekiwane ID z nieprawidłową strukturą','malformed'=>'nieprawidłowa struktura ID','not_requested'=>'ID nieobecne w żądaniu','empty_translation'=>'pusty tekst EN','invalid_utf8'=>'nieprawidłowe kodowanie','translation_too_long'=>'przekroczona długość','translation_too_short'=>'zbyt krótki tekst','inline_markers'=>'znaczniki linków lub formatowania','unexpected_html'=>'dodany HTML','url_presence'=>'obecność adresu WWW','url_email_tokens'=>'adres WWW lub e-mail','number_placeholders'=>'placeholder liczbowy','entity_tokens'=>'encje HTML','number_tokens'=>'liczby','currency_tokens'=>'waluta','polish_words'=>'pozostawione polskie słowa','unchanged_polish'=>'nieprzetłumaczony polski tekst','validator_rule_changed'=>'reguły wymagają ponownego odczytu','result_shape'=>'struktura wyniku','item_count'=>'liczba jednostek','result_bound'=>'zbyt wiele jednostek','item_shape'=>'struktura jednostki','unknown_id'=>'nieznane ID jednostki','duplicate_id'=>'powtórzone ID jednostki','missing_id'=>'brakujące ID jednostki'];
        return $labels[$name]??(str_starts_with($name,'brand_')?'nazwa własna: '.substr($name,6):'niespójność wyniku');
    }
    /** Complete protected comparison by physical row order only; no implied ID alignment or acceptance. */
    private static function full_pair_table(string $source): void {
        try {
            wp_cache_delete(ZP_EN_Rewrite_20261011::INDEX,'options');$index=get_option(ZP_EN_Rewrite_20261011::INDEX,[]);$id=$index['current']??null;if (!is_string($id)) return;
            $context=self::repair_context($id,$source);$items=array_values($context['chunk']['items']);$result=$context['journal']['terminal']['result']??null;
            if (!is_array($result)||array_keys($result)!==['items']||!is_array($result['items'])||!array_is_list($result['items'])||count($result['items'])>44||count($items)>40) return;
            echo '<details><summary>Pełne żądanie PL i wszystkie wiersze EN — bez przypisania</summary><p>Kolumny pokazują wyłącznie kolejność żądania i odpowiedzi. ID odpowiedzi może być przesunięte lub powtórzone. Nie przypisano ani nie zaakceptowano żadnego wiersza. Sprawdź znaczenie wszystkich '.count($items).' źródeł PL; poprawne znaczniki lub liczby same nie potwierdzają sensu tłumaczenia. Teksty są maskowane i ograniczone do 8000 bajtów.</p><div style="overflow-x:auto"><table class="widefat"><thead><tr><th>Pozycja żądania / ID / kontekst</th><th>PL — cały fragment</th><th>Wiersz odpowiedzi / ID modelu</th><th>EN — cały fragment</th></tr></thead><tbody>';
            for ($n=0;$n<max(count($items),count($result['items']));$n++) {
                $pl=$items[$n]??null;$en=$result['items'][$n]??null;
                echo '<tr><td>'.($pl===null?'—':(int)($n+1).' / '.esc_html($pl['id'].' / '.$pl['kind'])).'</td><td><pre style="white-space:pre-wrap">'.($pl===null?'Brak źródłowego wiersza na tej pozycji.':esc_html(self::full_text($pl['pl']))).'</pre></td><td>'.(int)($n+1).' / '.(is_array($en)&&is_string($en['id']??null)?esc_html(self::excerpt($en['id'])):'—').'</td><td><pre style="white-space:pre-wrap">'.(is_array($en)&&is_string($en['en']??null)?esc_html(self::full_text($en['en'])):'Nieprawidłowa struktura wiersza.').'</pre></td></tr>';
            }echo '</tbody></table></div></details>';
        } catch(Throwable $ignored) { echo '<p>Pełne porównanie nie jest dostępne bez aktualnego dowodu tej partii. Niczego nie przypisano ani nie zaakceptowano.</p>'; }
    }
    public static function render(): void {
        if (!is_admin()||($_SERVER['REQUEST_METHOD']??'')!=='GET'||!self::allowed()||(function_exists('wp_doing_ajax')&&wp_doing_ajax())||(defined('REST_REQUEST')&&REST_REQUEST)||(function_exists('wp_doing_cron')&&wp_doing_cron())) return;
        try { $sources=self::attention_sources(self::current_revision()); } catch(Throwable $ignored) { $sources=[]; }
        $rows=self::data();if (!$rows&&!$sources) return;
        echo '<section class="zpAdmin20__card" id="zpEnRewriteDiagnostics"><h2>Sprawdzenie zapisanego wyniku EN</h2><p>Odczyt lokalnego, już opłaconego wyniku. Ta karta niczego nie tłumaczy ponownie, nie zmienia kolejki i nie zatwierdza odrzuconych fragmentów.</p>';
        self::render_selection($sources);if (!$rows) echo '<p>Brak aktualnego, potwierdzonego odczytu wybranej strony. Niczego nie zaakceptowano ani nie ponowiono.</p>';
        foreach ($rows as $row) {
            echo '<h3>'.esc_html($row['source']).'</h3>';if ($row['status']!=='available') { echo '<p>Nie potwierdzono zgodności lokalnego zapisu i aktualnego źródła. Nie pokazano fragmentów ani nie uruchomiono API.</p>';if (is_string($row['proof_reason']??null)&&in_array($row['proof_reason'],self::PROOF_STAGES,true)) echo '<p>Niepotwierdzony warunek dowodu: <code>'.esc_html($row['proof_reason']).'</code>. To pierwszy niespełniony warunek lokalnego odczytu; nie sprawdzono publicznego HTML ani pozostałych warunków.</p>';continue; }
            $a=$row['analysis'];echo '<p>Ukończony journal: potwierdzony. Aktualne źródło: zgodne. Jednostki oczekiwane: '.(int)$a['requested_count'].'; zwrócone: '.($a['returned_count']===null?'—':(int)$a['returned_count']).'; odrzucone przez reguły treści: '.(int)$a['failed_count'].'. Lokalny parser: '.($a['local_parser_valid']?'wynik poprawny':'wynik odrzucony').'.</p>';
            if (!empty($a['name_checks'])) { echo '<details><summary>Liczba wystąpień nazw w zapisanym wyniku</summary><p>Naturalna redakcja może ograniczyć powtórzenia. Każda nazwa obecna w PL musi pozostać w EN; nowa nazwa nie może być dodana.</p><ul>';foreach ($a['name_checks'] as $entry) foreach ($entry['counts'] as $brand=>$count) echo '<li>'.esc_html($entry['id'].' · '.$brand).' · PL: '.(int)$count['pl'].' · EN: '.(int)$count['en'].'</li>';echo '</ul></details>'; }
            if ($a['shape']) echo '<p>Struktura: '.esc_html(implode(', ',array_map([self::class,'label'],$a['shape']))).'.</p>';
            if ($a['rows']) { echo '<div style="overflow-x:auto"><table class="widefat"><thead><tr><th>ID / kontekst</th><th>Reguła</th><th>PL — fragment</th><th>EN — fragment</th></tr></thead><tbody>';foreach ($a['rows'] as $unit) echo '<tr><td>'.esc_html($unit['id'].' / '.$unit['kind']).'</td><td>'.esc_html(implode(', ',array_map([self::class,'label'],$unit['reasons']))).'</td><td>'.esc_html($unit['pl']).'</td><td>'.esc_html($unit['en']).'</td></tr>';echo '</tbody></table></div>'; }
            if (!empty($a['missing_units'])) {
                echo '<h4>Brakujące jednostki z dokładnego żądania</h4><table class="widefat"><thead><tr><th>ID / kontekst</th><th>PL — fragment źródła</th></tr></thead><tbody>';
                foreach ($a['missing_units'] as $unit) echo '<tr><td>'.esc_html($unit['id'].' / '.$unit['kind']).'</td><td>'.esc_html($unit['pl']).'</td></tr>';
                echo '</tbody></table><p>To oczekiwane ID bez odpowiedzi EN; nie uzupełniono ich automatycznie. Fragmenty mają limit 180 znaków i zachowują maskowanie.</p>';
            }
            if (($a['extra_inspection']['eligible']??null)===true) {
                echo '<h4>Dodatkowe ID nieobecne w dokładnym żądaniu</h4><p>Wszystkie oczekiwane ID są obecne dokładnie raz i przechodzą pełną walidację. Parser nadal odrzuca oryginalną strukturę; niczego nie usunięto automatycznie.</p><table class="widefat"><thead><tr><th>ID</th><th>PL</th><th>EN — fragment</th></tr></thead><tbody>';
                foreach ($a['extra_inspection']['units'] as $extra) echo '<tr><td>'.esc_html($extra['id']).'</td><td>Brak w żądaniu — nie ma źródła PL dla tego ID.</td><td>'.esc_html($extra['en']).'</td></tr>';
                echo '</tbody></table>';
            }
            if ($a['returned_count']>40) {
                echo '<h4>Odczyt nadmiarowej odpowiedzi</h4><p>Analiza wierszy działa do limitu 44; większej odpowiedzi nie analizujemy. Walidator nadal odrzuca oryginalny wynik; nie usunięto, nie połączono ani nie zaakceptowano żadnego ID.</p>';
                if ($a['overflow_inspection']['reasons']) echo '<p>Powody: '.esc_html(implode(', ',array_map([self::class,'label'],$a['overflow_inspection']['reasons']))).'.</p>';
                if ($a['overflow_inspection']['rows']) { echo '<table class="widefat"><thead><tr><th>Wiersz / ID</th><th>Klasa / problem</th><th>PL</th><th>EN — fragment</th></tr></thead><tbody>';foreach ($a['overflow_inspection']['rows'] as $unit) echo '<tr><td>'.(int)$unit['ordinal'].' / '.esc_html($unit['id']).'</td><td>'.esc_html(self::label($unit['classification']).($unit['reasons']?' · '.implode(', ',array_map([self::class,'label'],$unit['reasons'])):'')).'</td><td>'.($unit['pl']===null?'Brak w żądaniu — źródło PL nie istnieje.':esc_html($unit['pl'])).'</td><td>'.esc_html($unit['en']).'</td></tr>';echo '</tbody></table>'; }
                foreach ($a['overflow_full_units'] as $unit) echo '<details><summary>Pełny maskowany tekst: wiersz '.(int)$unit['ordinal'].' / '.esc_html($unit['id'].' / '.$unit['kind']).'</summary><p>PL</p><pre style="white-space:pre-wrap">'.($unit['pl']===null?'Brak źródła PL dla tego ID.':esc_html($unit['pl'])).'</pre><p>EN</p><pre style="white-space:pre-wrap">'.esc_html($unit['en']).'</pre></details>';
                if (($a['extra_inspection']['eligible']??null)!==true) echo '<p>Nie potwierdzono możliwości samego odrzucenia dodatkowych ID. Wymaga to kompletu unikalnych, poprawnych oczekiwanych odpowiedzi; przyczyny pokazano powyżej. Nie zmieniono reguł korekty.</p>';
            }
            self::full_pair_table($row['source']);
            if (class_exists('ZP_EN_Local_Repair_20261011')) ZP_EN_Local_Repair_20261011::render($row['source']);
            if ($a['failed_count']>count($a['rows'])) echo '<p>Pokazano pierwsze 12 odrzuconych jednostek.</p>';
        }echo '</section>';
    }
    public static function register(): void { add_action('zpte_rewrite_card',[self::class,'render'],20); }
}
