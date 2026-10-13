<?php
if (!defined('ABSPATH')) exit;

/** Incremental orchestration only; the existing durable Responses worker owns all paid flights. */
final class ZP_EN_Rewrite_Nightly_20261011 {
    public const CONFIG='zps_en_nightly_20261011_config';
    public const BOOTSTRAP='zps_en_nightly_20261011_bootstrap';
    public const REGISTRY='zps_en_nightly_20261011_registry';
    public const STATE='zps_en_nightly_20261011_state';
    public const CHARS='zps_en_nightly_20261011_chars';
    public const CHECKS='zps_en_nightly_20261011_checks';
    public const QUARANTINE='zps_en_nightly_20261011_quarantine';
    public const DAILY='zp_en_nightly_daily_20261011';
    public const SCAN='zp_en_nightly_scan_20261011';
    private const PREFIX='zps_en_rewrite_20261011_';
    private const SCOPE='luna_incremental';
    private const ERRORS=['storage_failed','busy','forbidden','bootstrap_required','queue_changed','daily_chars_limit','source_changed','source_invalid','source_private','source_noindex','source_noarchive','source_unavailable','scope_invalid','connection_changed','budget_limit','request_changed','source_incomplete'];

    private static function hash($value): string { return hash('sha256',(string)wp_json_encode($value)); }
    private static function read(string $name,array $default=[]): array { wp_cache_delete($name,'options');$v=get_option($name,$default);return is_array($v)?$v:$default; }
    private static function save(string $name,array $value): void { update_option($name,$value,false);if (self::read($name)!==$value) throw new RuntimeException('storage_failed'); }
    private static function key(string $kind,string $id): string { if (!in_array($kind,['r','p'],true)||preg_match('/^[a-f0-9]{32,64}$/D',$id)!==1) throw new RuntimeException('queue_changed');return self::PREFIX.$kind.'_'.$id; }
    private static function actor_can(int $id,int $post=0): bool { wp_cache_delete($id,'users');wp_cache_delete($id,'user_meta');return $id>0&&user_can($id,'manage_options')&&user_can($id,'edit_posts')&&user_can($id,'unfiltered_html')&&(!$post||user_can($id,'edit_post',$post)); }
    public static function enabled(): bool { wp_cache_delete(self::CONFIG,'options');$v=get_option(self::CONFIG,null);return $v===null||(is_array($v)&&($v['enabled']??null)===true); }
    private static function current(): array { $idx=self::read(ZP_EN_Rewrite_20261011::INDEX);$id=$idx['current']??'';return is_string($id)&&preg_match('/^[a-f0-9]{32}$/D',$id)?self::read(self::key('r',$id)):[]; }
    private static function full_proof(array $r): bool {
        $manifest=ZP_EN_Rewrite_20261011::manifest();
        if (($r['model']??null)!=='gpt-6-luna'||($r['scope']??null)!=='luna_full'||($r['state']??null)!=='verified'||!is_string($r['id']??null)||preg_match('/^[a-f0-9]{32}$/D',$r['id'])!==1||!is_array($r['pages']??null)||array_keys($r['pages'])!==array_keys($manifest)) return false;
        try { if (ZP_EN_Rewrite_20261011::model_policy($r)!==ZP_EN_Rewrite_Transport_20261011::policy('gpt-6-luna')) return false; } catch(Throwable $ignored) { return false; }
        foreach ($r['pages'] as $row) if (!is_array($row)||($row['state']??null)!=='verified') return false;
        return is_int($r['actor']??null)&&$r['actor']>0&&is_string($r['campaign']??null)&&preg_match('/^[a-f0-9]{32}$/D',$r['campaign'])===1;
    }
    /** Immutable full137 evidence stays separate from the changing current incremental revision. */
    public static function bootstrap(): array {
        $b=self::read(self::BOOTSTRAP);$id=$b['revision']??'';
        if (!is_string($id)||preg_match('/^[a-f0-9]{32}$/D',$id)!==1||($b['manifest_hash']??null)!==self::hash(ZP_EN_Rewrite_20261011::manifest())) return [];
        $r=self::read(self::key('r',$id));
        return self::full_proof($r)&&($b['revision_hash']??null)===self::hash($r)&&is_array($b['page_proofs']??null)&&array_keys($b['page_proofs'])===array_keys(ZP_EN_Rewrite_20261011::manifest())&&($b['page_proofs_hash']??null)===self::hash($b['page_proofs'])?$b:[];
    }
    public static function record_bootstrap(array $revision): void {
        if (!self::full_proof($revision)||!self::actor_can($revision['actor'])||self::read(self::key('r',$revision['id']))!==$revision) return;
        $old=self::read(self::BOOTSTRAP);if (($old['revision']??null)===$revision['id']&&($old['revision_hash']??null)===self::hash($revision)) return;
        $proof=[];foreach ($revision['pages'] as $source=>$status) { $page=self::page($source,$revision['id']);$approved=ZP_EN_Rewrite_20261011::approved_page($source);if (($page['state']??null)!=='verified'||!is_int($page['verified_at']??null)||$page['verified_at']<=0||($approved['revision']??null)!==$revision['id']||!$approved['map']) return;$proof[$source]=self::hash(['source'=>$source,'version'=>$revision['id'],'staged_hash'=>$page['staged_hash'],'verified_at'=>$page['verified_at']]); }
        self::save(self::BOOTSTRAP,['page_proofs'=>$proof,'page_proofs_hash'=>self::hash($proof),'revision'=>$revision['id'],'revision_hash'=>self::hash($revision),'manifest_hash'=>self::hash(ZP_EN_Rewrite_20261011::manifest()),'actor'=>$revision['actor'],'campaign'=>$revision['campaign'],'verified_at'=>time()]);
    }
    /** Native publication/privacy is tested before any public source GET. */
    public static function public_post(int $id,string $source=''): ?object {
        clean_post_cache($id);wp_cache_delete($id,'post_meta');$p=get_post($id);
        if (!$p||!in_array($p->post_type,['post','page'],true)||$p->post_status!=='publish'||(string)$p->post_password!=='') return null;
        $path=ZP_EN_Rewrite_20261011::path(wp_parse_url(get_permalink($id),PHP_URL_PATH));if ((int)get_option('page_on_front',0)===$id) $path='/';if(get_permalink($id)!==home_url($path))return null;
        if ($path===''||($source!==''&&$source!==$path)||preg_match('~^/(?:en|oferty|offers|briefy|briefs|dziekujemy)(?:/|$)~i',$path)||strpos($path,'/page/')!==false||\ZPL\Router::never($path)||\ZPL\Router::is_excluded($path)) return null;
        if (function_exists('zp_seo_plan_is_private_path')&&zp_seo_plan_is_private_path($path)) return null;
        $robots=get_post_meta($id,'rank_math_robots',true);if (is_string($robots)) $robots=preg_split('/[\s,]+/',strtolower($robots));
        if (!is_array($robots)) return null;foreach ($robots as $robot) if (!is_string($robot)||in_array(strtolower(trim($robot)),['noindex','noarchive'],true)) return null;
        if (preg_match('/\[(?:zp_offer|zp_brief|zaprojektowani_offer|zaprojektowani_brief)\b/i',(string)$p->post_content)) return null;
        return $p;
    }
    private static function descriptor_row(string $source,$d): array {
        if (!is_array($d)||!is_int($d['post_id']??null)||$d['post_id']<=0||!is_string($d['en']??null)||ZP_EN_Rewrite_20261011::path($source)!==$source||ZP_EN_Rewrite_20261011::path($d['en'])!==$d['en']||!str_starts_with($d['en'],'/en/')||($d['seal']??null)!==self::hash([$source,$d['post_id'],$d['en']])||!self::public_post($d['post_id'],$source)) return [];
        return ['post_id'=>$d['post_id'],'en'=>$d['en']];
    }
    public static function descriptor(string $source): array { return self::descriptor_row($source,self::read(self::REGISTRY)[$source]??null); }
    public static function descriptors(): array { $out=[];foreach (array_slice(self::read(self::REGISTRY),0,1000,true) as $source=>$row) if (is_string($source)&&($d=self::descriptor_row($source,$row))) $out[$source]=$d;return $out; }
    private static function register_source(object $p,string $source): void {
        if (isset(ZP_EN_Rewrite_20261011::manifest()[$source])) return;
        $registry=self::read(self::REGISTRY);$routes=\ZPL\Router::routes();$en=$routes[$source]??('/en'.$source);
        if (!is_string($en)||ZP_EN_Rewrite_20261011::path($en)!==$en||!str_starts_with($en,'/en/')||($source!=='/'&&$en==='/en/')) throw new RuntimeException('scope_invalid');
        $physical=(int)url_to_postid(home_url($en));if($physical>0&&$physical!==(int)$p->ID)throw new RuntimeException('scope_invalid');
        foreach ($routes as $pl=>$target) if ($target===$en&&$pl!==$source) throw new RuntimeException('scope_invalid');
        foreach ($registry as $pl=>$d) if (($d['en']??null)===$en&&$pl!==$source) throw new RuntimeException('scope_invalid');
        $new=['post_id'=>(int)$p->ID,'en'=>$en,'seal'=>self::hash([$source,(int)$p->ID,$en])];
        if (isset($registry[$source])&&array_intersect_key($registry[$source],$new)!==$new) throw new RuntimeException('source_changed');
        if (!isset($registry[$source])) { if (count($registry)>=1000) throw new RuntimeException('scope_invalid');$registry[$source]=$new;self::save(self::REGISTRY,$registry); }
    }
    /** Called under the shared core lease before any factory mutation. */
    public static function factory_current(string $scan,array $sources,string $version): bool {
        $s=self::read(self::STATE);if(($s['id']??null)!==$scan)return false;
        if(($s['state']??null)==='factory')return is_array($s['changed']??null)&&array_slice($s['changed'],0,10)===$sources;
        return ($s['state']??null)==='waiting'&&($s['revision']??null)===$version&&($s['batch']??null)===$sources;
    }
    public static function pending_source(string $source,array $descriptor): bool { return self::bootstrap()!==[]&&self::descriptor($source)===$descriptor&&current_user_can('manage_options')&&current_user_can('edit_posts')&&current_user_can('unfiltered_html')&&current_user_can('edit_post',$descriptor['post_id']); }
    private static function page(string $source,string $version): array { return self::read(self::key('p',hash('sha256',$version.'|'.$source))); }
    /** Same components as core native_hash, without calling Router::routes from its own filter. */
    private static function native_digest(object $post,string $en): string {
        $fields=[];foreach (['ID','post_type','post_status','post_password','post_title','post_content','post_excerpt','post_name','post_parent','post_date','post_date_gmt','post_modified','post_modified_gmt'] as $k) $fields[$k]=$post->$k;
        $meta=[];foreach (['_elementor_data','_elementor_page_settings','rank_math_title','rank_math_description','rank_math_focus_keyword','rank_math_canonical_url','rank_math_robots','rank_math_schema_Article','rank_math_schema_FAQPage','_thumbnail_id'] as $k) $meta[$k]=get_post_meta($post->ID,$k,true);
        return self::hash(['fields'=>$fields,'meta'=>$meta,'route'=>$en,'attrs'=>get_option('zpl_extra_attrs',[]),'schema'=>get_option('zpl_extra_schema',[])]);
    }
    private static function complete_page(string $source,string $version,array $d,bool $verified=false): bool {
        $page=self::page($source,$version);$post=self::public_post($d['post_id'],$source);$states=$verified?['verified']:['staged','activation_prepared','checking_serving','verified'];
        if (!$post||($page['source']??null)!==$source||($page['revision']??null)!==$version||($page['post_id']??null)!==$d['post_id']||($page['en']??null)!==$d['en']||!in_array($page['state']??null,$states,true)||!is_array($page['map']??null)||!$page['map']||($page['native_hash']??null)!==self::native_digest($post,$d['en'])) return false;
        return ($page['staged_hash']??null)===self::hash(['source_hash'=>$page['source_hash']??null,'native_hash'=>$page['native_hash'],'manual_hash'=>$page['manual_hash']??null,'map'=>$page['map']]);
    }
    /** Seal complete map evidence once; Router need not read every large dictionary on every request. */
    public static function stage_route(array $page): void {
        $source=$page['source']??'';$version=$page['revision']??'';$d=is_string($source)?self::descriptor($source):[];
        if (!$d) return;if (!is_string($version)||preg_match('/^[a-f0-9]{32}$/D',$version)!==1||!self::complete_page($source,$version,$d)||!self::actor_can($page['actor']??0,$d['post_id'])) throw new RuntimeException('source_changed');
        $registry=self::read(self::REGISTRY);$entry=['version'=>$version,'native_hash'=>$page['native_hash'],'staged_hash'=>$page['staged_hash']];$entry['seal']=self::hash([$source,$d,$entry]);
        $registry[$source]['versions'][$version]=$entry;$registry[$source]['versions']=array_slice($registry[$source]['versions'],-6,null,true);self::save(self::REGISTRY,$registry);\ZPL\Router::reset();
    }
    private static function route_receipt(string $source,string $version,array $d,array $row): bool {
        $entry=$row['versions'][$version]??null;if (!is_array($entry)||($entry['version']??null)!==$version||!is_string($entry['native_hash']??null)||!is_string($entry['staged_hash']??null)) return false;
        $seal=$entry['seal']??null;unset($entry['seal']);$post=self::public_post($d['post_id'],$source);
        return $post&&$seal===self::hash([$source,$d,$entry])&&$entry['native_hash']===self::native_digest($post,$d['en']);
    }
    public static function routes(array $routes): array {
        $idx=self::read(ZP_EN_Rewrite_20261011::INDEX);$registry=self::read(self::REGISTRY);$preview=$_GET['zp_en_rewrite_preview']??($_GET['preview']??'');
        foreach (self::descriptors() as $source=>$d) {
            if (isset($routes[$source])&&$routes[$source]!==$d['en']) continue;
            $version=$idx['active'][$source]??'';$ok=is_string($version)&&preg_match('/^[a-f0-9]{32}$/D',$version)&&self::route_receipt($source,$version,$d,$registry[$source]);
            if (!$ok&&is_string($preview)&&preg_match('/^[a-f0-9]{32}$/D',$preview)&&current_user_can('manage_options')&&current_user_can('edit_post',$d['post_id'])) { $page=self::page($source,$preview);$ok=self::complete_page($source,$preview,$d); }
            if ($ok&&!in_array($d['en'],array_diff_key($routes,[$source=>true]),true)) $routes[$source]=$d['en'];
        }
        return $routes;
    }
    public static function has_page($has,string $source): bool { if ($has) return true;$d=self::descriptor($source);$idx=self::read(ZP_EN_Rewrite_20261011::INDEX);$v=$idx['active'][$source]??'';return $d&&is_string($v)&&preg_match('/^[a-f0-9]{32}$/D',$v)&&self::complete_page($source,$v,$d); }
    public static function sitemap($paths): array { $paths=is_array($paths)?$paths:[];foreach (self::descriptors() as $source=>$d) if (($idx=self::read(ZP_EN_Rewrite_20261011::INDEX))&&is_string($v=$idx['active'][$source]??null)&&preg_match('/^[a-f0-9]{32}$/D',$v)&&self::complete_page($source,$v,$d,true)) $paths[]=$source;return array_values(array_unique($paths)); }
    /** null preserves59 strict gate during full campaigns; incremental preserves other verified source maps. */
    public static function currency_gate(string $source): ?string {
        if (class_exists('ZPT2_Engine') && class_exists('ZPT2_Source')) { $v2=ZPT2_Engine::active($source);if ($v2&&ZPT2_Engine::public_pointer($source,$v2)&&ZPT2_Source::native($v2['snapshot']['post_id'])===$v2['snapshot']['native']) return 'ready'; }
        $r=self::current();if (($r['scope']??null)!==self::SCOPE) return null;if (!self::bootstrap()) return 'awaiting_translation';
        $idx=self::read(ZP_EN_Rewrite_20261011::INDEX);$v=$idx['active'][$source]??'';$d=ZP_EN_Rewrite_20261011::source_descriptor($source);
        if (!$d||!is_string($v)||preg_match('/^[a-f0-9]{32}$/D',$v)!==1||!self::complete_page($source,$v,$d,true)) return 'source_changed';
        $map=ZP_EN_Rewrite_20261011::approved_page($source);return ($map['revision']??null)===$v&&!empty($map['map'])?'ready':'source_changed';
    }
    /** Recover only this failed incremental activation's private pointer; never touch source/map/journal data. */
    public static function restore_activation(array $revision,string $source): void {
        if(($revision['scope']??null)!==self::SCOPE)return;$id=$revision['id']??'';if(!is_string($id)||preg_match('/^[a-f0-9]{32}$/D',$id)!==1)return;
        $page=self::page($source,$id);$a=$page['activation']??null;if(!is_array($a)||($a['target']??null)!==$id||!is_string($a['previous']??null)||($a['previous']!==''&&preg_match('/^[a-f0-9]{32}$/D',$a['previous'])!==1)||($page['state']??null)!=='checking_serving')return;
        $idx=self::read(ZP_EN_Rewrite_20261011::INDEX);if(($idx['current']??null)!==$id||($idx['active'][$source]??null)!==$id)return;
        if($a['previous']==='')unset($idx['active'][$source]);else $idx['active'][$source]=$a['previous'];self::save(ZP_EN_Rewrite_20261011::INDEX,$idx);
        $page['state']='activation_prepared';self::save(self::key('p',hash('sha256',$id.'|'.$source)),$page);\ZPL\Router::reset();\ZPL\Dict::flush();if(class_exists('ZP_HTML_Cache_20261009'))ZP_HTML_Cache_20261009::queue_purge();
    }
    public static function reserve_chars(array $revision,array $chunk): void {
        if (($revision['scope']??null)!==self::SCOPE) return;if (!self::enabled()||!self::bootstrap()||!self::actor_can($revision['actor']??0)) throw new RuntimeException('forbidden');
        $limit=\ZPTE\Settings::get('daily_chars');if (!is_int($limit)||$limit<1000||$limit>2000000) throw new RuntimeException('daily_chars_limit');
        $count=0;foreach ($chunk['items']??[] as $item) { if (!is_array($item)||!is_string($item['pl']??null)) throw new RuntimeException('queue_changed');$count+=mb_strlen($item['pl'],'UTF-8'); }
        $ledger=self::read(self::CHARS);$date=(new DateTimeImmutable('now',new DateTimeZone('Europe/Warsaw')))->format('Y-m-d');$id=$chunk['id']??'';$hash=$chunk['request_hash']??'';
        if (!is_string($id)||preg_match('/^[a-f0-9]{32}$/D',$id)!==1||!is_string($hash)||preg_match('/^[a-f0-9]{64}$/D',$hash)!==1) throw new RuntimeException('queue_changed');
        foreach ($ledger as $day=>$rows) if (isset($rows[$id])) { if ($rows[$id]!==['request_hash'=>$hash,'chars'=>$count]) throw new RuntimeException('request_changed');return; }
        $today=$ledger[$date]??[];$used=0;foreach ($today as $entry) { if (!is_array($entry)||!is_int($entry['chars']??null)||$entry['chars']<0) throw new RuntimeException('daily_chars_limit');$used+=$entry['chars']; }
        if ($used+$count>$limit||count($today)>=1000) throw new RuntimeException('daily_chars_limit');$today[$id]=['request_hash'=>$hash,'chars'=>$count];$ledger[$date]=$today;ksort($ledger);$ledger=array_slice($ledger,-31,null,true);self::save(self::CHARS,$ledger);
    }
    /** Per-page relevant manual proof avoids retranslating unrelated pages after a global dictionary edit. */
    private static function changed(array $capture,array $previous,string $version): bool {
        if(($previous['state']??null)!=='verified'||!(class_exists('ZP_EN_Source_Guard_20261011')?ZP_EN_Source_Guard_20261011::same_source($previous,$capture):($previous['source_hash']??null)===$capture['source_hash'])||($previous['native_hash']??null)!==$capture['native_hash'])return true;
        $manual=get_option('zpl_overrides',[]);if(!is_array($manual))throw new RuntimeException('source_invalid');$relevant=array_intersect_key($manual,$capture['units']);ksort($relevant,SORT_STRING);$scope=self::hash($relevant);
        $all=self::read(self::CHECKS);$old=$all[$capture['source']]??null;$safe=false;
        if(($previous['manual_hash']??null)===$capture['manual_hash'])$safe=true;
        elseif(is_array($old)&&($old['version']??null)===$version&&($old['native_hash']??null)===$capture['native_hash']&&($old['source_hash']??null)===$capture['source_hash']){ $seal=$old['seal']??null;unset($old['seal']);$safe=$seal===self::hash([$capture['source'],$old])&&($old['manual_scope_hash']??null)===$scope; }
        elseif(!$relevant&&!array_diff_key($previous['units']??[],$previous['map']??[]))$safe=true;
        if(!$safe)return true;$entry=['version'=>$version,'native_hash'=>$capture['native_hash'],'source_hash'=>$capture['source_hash'],'manual_scope_hash'=>$scope];$entry['seal']=self::hash([$capture['source'],$entry]);$all[$capture['source']]=$entry;if(count($all)>1200)throw new RuntimeException('scope_invalid');self::save(self::CHECKS,$all);return false;
    }
    /** Separate private receipts prevent a failed, unchanged source being purchased again next night. */
    private static function quarantines(): array {
        wp_cache_delete(self::QUARANTINE,'options');$all=get_option(self::QUARANTINE,null);
        if($all===null)return [];if(!is_array($all)||count($all)>1200)throw new RuntimeException('queue_changed');
        foreach($all as$source=>$entry){if(!is_string($source)||ZP_EN_Rewrite_20261011::path($source)!==$source||!is_array($entry)||($entry['source']??null)!==$source)throw new RuntimeException('queue_changed');$seal=$entry['seal']??null;unset($entry['seal']);if(!is_string($seal)||$seal!==self::hash($entry))throw new RuntimeException('queue_changed');}
        return $all;
    }
    private static function revision_identity(array $revision): string {
        return self::hash(array_diff_key($revision,array_flip(['pages','state','error','updated_at','last_tick_at'])));
    }
    private static function capture_receipt(array $capture): array {
        if(!is_string($capture['source']??null)||!is_array($capture['units']??null))throw new RuntimeException('source_invalid');
        foreach(['source_hash','native_hash','manual_hash'] as$key)if(!is_string($capture[$key]??null)||preg_match('/^[a-f0-9]{64}$/D',$capture[$key])!==1)throw new RuntimeException('source_invalid');
        $manual=get_option('zpl_overrides',[]);if(!is_array($manual))throw new RuntimeException('source_invalid');$scope=array_intersect_key($manual,$capture['units']);ksort($scope,SORT_STRING);
        return ['source'=>$capture['source'],'source_hash'=>$capture['source_hash'],'native_hash'=>$capture['native_hash'],'manual_hash'=>$capture['manual_hash'],'manual_scope_hash'=>self::hash($scope)];
    }
    private static function held_flights(array $revision,array $page): string {
        if(!$page)return self::hash([]);if(!is_array($page['chunks']??null)||!array_is_list($page['chunks'])||count($page['chunks'])>1500)throw new RuntimeException('queue_changed');$flights=[];
        foreach($page['chunks'] as$id){if(!is_string($id)||preg_match('/^[a-f0-9]{32}$/D',$id)!==1)throw new RuntimeException('queue_changed');$name=self::PREFIX.'c_'.$id;$c=self::read($name);wp_cache_delete(self::PREFIX.'j_'.$id,'options');$j=get_option(self::PREFIX.'j_'.$id,null);
            if(($c['id']??null)!==$id||($c['revision']??null)!==$revision['id']||($c['source']??null)!==$page['source']||($c['key_fingerprint']??null)!==$revision['key_fingerprint']||!is_array($c['request']??null)||($c['request_hash']??null)!==ZP_EN_Rewrite_Transport_20261011::request_hash($c['request'])||ZP_EN_Rewrite_Transport_20261011::chunk_policy($c)!==ZP_EN_Rewrite_20261011::model_policy($revision)||($j!==null&&!is_array($j)))throw new RuntimeException('queue_changed');
            foreach(['zp_en_revision'=>$revision['id'],'zp_en_chunk'=>$id,'zp_en_request_hash'=>$c['request_hash']] as$key=>$value)if(($c['request']['metadata'][$key]??null)!==$value)throw new RuntimeException('queue_changed');
            if($j!==null){foreach(['chunk_id'=>$id,'revision'=>$revision['id'],'request_hash'=>$c['request_hash'],'key_fingerprint'=>$c['key_fingerprint'],'policy_hash'=>$c['policy_hash']??null] as$key=>$value)if(($j[$key]??null)!==$value)throw new RuntimeException('queue_changed');if(!ZP_EN_Rewrite_Transport_20261011::valid_id($j['response_id']??null)||(!empty($c['response_id'])&&$c['response_id']!==$j['response_id']))throw new RuntimeException('queue_changed');}
            if(isset($c['terminal'])&&($j['terminal']??null)!==$c['terminal'])throw new RuntimeException('queue_changed');$flights[$id]=[$c,$j];
        }return self::hash($flights);
    }
    private static function held_identity(array $entry,array $revision,string $source): bool {
        $page=self::page($source,$revision['id']??'');
        return ($entry['revision']??null)===($revision['id']??null)&&($entry['identity_hash']??null)===self::revision_identity($revision)&&($entry['target_status']??null)===($revision['pages'][$source]??null)&&($entry['page_hash']??null)===self::hash($page)&&($entry['flights_hash']??null)===self::held_flights($revision,$page)&&ZP_EN_Rewrite_20261011::daily_isolation_safe($revision,$source,$entry['target_status']['error']??'');
    }
    /** Under the shared lease: preserve all failed request/journal/reservation fields, write only this receipt. */
    private static function quarantine_batch(array $revision,array $scan): void {
        if(($revision['scope']??null)!==self::SCOPE||($revision['state']??null)!=='complete_with_issues'||($revision['nightly_scan_id']??null)!==($scan['id']??null)||array_keys($revision['pages']??[])!==($scan['batch']??null))throw new RuntimeException('queue_changed');
        $all=self::quarantines();
        foreach($revision['pages'] as$source=>$status){
            if(($status['state']??null)!=='attention')continue;$capture=$scan['captures'][$source]??null;
            if(!is_array($capture)||($capture['source']??null)!==$source||!is_string($capture['manual_scope_hash']??null)||!ZP_EN_Rewrite_20261011::daily_isolation_safe($revision,$source,$status['error']??''))throw new RuntimeException('queue_changed');
            $page=self::page($source,$revision['id']);
            // If global manual data changed between scan and prepare, keep the failed flight held; invent no per-key history.
            $manualProof=$page&&(($page['manual_hash']??null)!==($capture['manual_hash']??null))?'unknown':'scan';
            $entry=['source'=>$source,'revision'=>$revision['id'],'identity_hash'=>self::revision_identity($revision),'target_status'=>$status,'page_hash'=>self::hash($page),'flights_hash'=>self::held_flights($revision,$page),'capture'=>$capture,'manual_proof'=>$manualProof];$entry['seal']=self::hash($entry);$all[$source]=$entry;
        }
        if(count($all)>1200)throw new RuntimeException('scope_invalid');self::save(self::QUARANTINE,$all);
    }
    /** Admission of a terminal daily batch requires already persisted, exact per-page financial receipts. */
    public static function can_advance(array $revision): bool {
        if(($revision['scope']??null)!==self::SCOPE||($revision['state']??null)!=='complete_with_issues'||!is_string($revision['id']??null)||preg_match('/^[a-f0-9]{32}$/D',$revision['id'])!==1||self::read(self::key('r',$revision['id']))!==$revision||!is_array($revision['pages']??null)||!$revision['pages'])return false;
        try{$all=self::quarantines();$issues=0;foreach($revision['pages'] as$source=>$status){if(($status['state']??null)==='attention'){$issues++;if(!isset($all[$source])||!self::held_identity($all[$source],$revision,$source))return false;}elseif(!in_array($status['state']??null,['verified','skipped'],true))return false;}return $issues>0;}catch(Throwable$ignored){return false;}
    }
    /** Fresh source changes may form a new cohort; the old paid flight and reserved cost stay untouched. */
    private static function unchanged_quarantine(array $capture): bool {
        $source=$capture['source'];$all=self::quarantines();if(!isset($all[$source]))return false;$entry=$all[$source];$revision=self::read(self::key('r',$entry['revision']??''));$page=self::page($source,$entry['revision']);
        if(($entry['identity_hash']??null)!==self::revision_identity($revision))throw new RuntimeException('queue_changed');
        if(($revision['pages'][$source]['state']??null)==='verified'){
            $d=ZP_EN_Rewrite_20261011::source_descriptor($source);if(!$d||!self::complete_page($source,$revision['id'],$d,true))throw new RuntimeException('queue_changed');unset($all[$source]);self::save(self::QUARANTINE,$all);return false;
        }
        if(!self::held_identity($entry,$revision,$source))throw new RuntimeException('queue_changed');$fresh=self::capture_receipt($capture);$old=$entry['capture'];
        $same=$page?(class_exists('ZP_EN_Source_Guard_20261011')?ZP_EN_Source_Guard_20261011::same_source($page,$capture):($page['source_hash']??null)===$capture['source_hash']):($old['source_hash']??null)===$capture['source_hash'];
        return $same&&($page['native_hash']??($old['native_hash']??null))===$capture['native_hash']&&(($entry['manual_proof']??null)==='unknown'||(($entry['manual_proof']??null)==='scan'&&($old['manual_scope_hash']??null)===$fresh['manual_scope_hash']));
    }
    private static function scan_terminal(array $scan): string { return !empty($scan['held'])?'complete_with_issues':'complete'; }
    public static function next_daily(int $now=0): int { $now=$now?:time();$at=(new DateTimeImmutable('@'.$now))->setTimezone(new DateTimeZone('Europe/Warsaw'));$next=$at->setTime(3,0);if ($next->getTimestamp()<=$now) $next=$next->modify('+1 day');return $next->getTimestamp(); }
    private static function schedule_scan(int $delay=5): void { if (!wp_next_scheduled(self::SCAN)) wp_schedule_single_event(time()+max(1,$delay),self::SCAN); }
    public static function maintain(): void {
        if (class_exists('ZPT2_Engine') || in_array(get_option('zpt2_takeover',false),[true,'1'],true)) return; // New daily admission belongs to v2; retained jobs are untouched.
        if (!self::enabled()) return;if (!wp_next_scheduled(self::DAILY)) wp_schedule_single_event(self::next_daily(),self::DAILY);
        if (current_user_can('manage_options')&&($r=self::current())&&self::full_proof($r)) { try { self::record_bootstrap($r); } catch(Throwable $ignored) {} }
        $s=self::read(self::STATE);if (in_array($s['state']??null,['scanning','factory','waiting'],true)&&!(($s['state']??null)==='waiting'&&(self::current()['state']??null)==='attention')) self::schedule_scan(30);
    }
    public static function daily(): void {
        if (class_exists('ZPT2_Engine') || in_array(get_option('zpt2_takeover',false),[true,'1'],true)) return; // New daily admission belongs to v2; retained jobs are untouched.
        if (!self::enabled()) return;if (!wp_next_scheduled(self::DAILY)) wp_schedule_single_event(self::next_daily(),self::DAILY);
        $b=self::bootstrap();if (!$b) { self::save(self::STATE,['state'=>'waiting_translation','at'=>time(),'error'=>'bootstrap_required']);return; }
        $old_user=(int)get_current_user_id();
        try {
            if(!self::actor_can($b['actor']))throw new RuntimeException('forbidden');wp_set_current_user(0);wp_set_current_user($b['actor']);$r=self::current();
            // Only a virgin flight held by the declared daily character limit may continue on the next day.
            if(($r['scope']??null)===self::SCOPE&&($r['state']??null)==='attention'&&($r['error']??null)==='daily_chars_limit')ZP_EN_Rewrite_20261011::resume($r['id']);
            ZP_EN_Rewrite_20261011::nightly_exclusive(static function()use($b):void {
                $r=self::current();$old=self::read(self::STATE);
                if(in_array($old['state']??null,['scanning','factory','waiting'],true)){self::schedule_scan();return;}
                if(!in_array($r['state']??null,['verified','rolled_back','cancelled'],true)&&!self::can_advance($r)){if(($r['state']??null)==='complete_with_issues')throw new RuntimeException('queue_changed');return;}
                $ids=[];for($page=1;$page<=11;$page++){
                    $rows=get_posts(['post_type'=>['post','page'],'post_status'=>'publish','has_password'=>false,'posts_per_page'=>500,'paged'=>$page,'fields'=>'ids','orderby'=>'ID','order'=>'ASC','suppress_filters'=>true,'no_found_rows'=>true]);
                    if(!is_array($rows))throw new RuntimeException('source_invalid');if($page===11&&$rows)throw new RuntimeException('source_incomplete');
                    foreach($rows as$id)if(is_numeric($id)&&(int)$id>0)$ids[]=(int)$id;if(count($rows)<500)break;
                }
                self::save(self::STATE,['id'=>bin2hex(random_bytes(16)),'actor'=>$b['actor'],'bootstrap'=>$b['revision'],'state'=>'scanning','ids'=>array_values(array_unique($ids)),'cursor'=>0,'changed'=>[],'captures'=>[],'held'=>[],'completed'=>0,'started_at'=>time(),'at'=>time(),'error'=>'']);self::schedule_scan();
            });
        }catch(Throwable$e){if($e->getMessage()!=='busy')self::save(self::STATE,['state'=>'attention','at'=>time(),'error'=>in_array($e->getMessage(),self::ERRORS,true)?$e->getMessage():'source_invalid']);}
        finally{wp_set_current_user(0);wp_set_current_user($old_user);}
    }
    public static function scan(): void {
        if (class_exists('ZPT2_Engine') || in_array(get_option('zpt2_takeover',false),[true,'1'],true)) return; // New daily admission belongs to v2; retained jobs are untouched.
        if(!self::enabled())return;$old_user=(int)get_current_user_id();$s=self::read(self::STATE);
        try{
            $b=self::bootstrap();if(!$b||!in_array($s['state']??null,['scanning','factory','waiting'],true))return;
            if(($s['bootstrap']??null)!==$b['revision']||!self::actor_can($s['actor']??0))throw new RuntimeException('forbidden');wp_set_current_user(0);wp_set_current_user($s['actor']);
            if($s['state']==='factory'){
                $batch=array_slice($s['changed'],0,10);if(!$batch){ZP_EN_Rewrite_20261011::nightly_exclusive(static function()use($s):void{if(self::read(self::STATE)!==$s)throw new RuntimeException('queue_changed');$s['state']=self::scan_terminal($s);self::save(self::STATE,$s);});return;}
                $id=ZP_EN_Rewrite_20261011::start_incremental($batch,$b['revision'],$s['id']);
                ZP_EN_Rewrite_20261011::nightly_exclusive(static function()use($s,$batch,$id):void{$now=self::read(self::STATE);if(($now['state']??null)==='waiting'&&($now['revision']??null)===$id)return;if($now!==$s)throw new RuntimeException('queue_changed');$s['revision']=$id;$s['batch']=$batch;$s['state']='waiting';$s['at']=time();self::save(self::STATE,$s);});self::schedule_scan(60);return;
            }
            ZP_EN_Rewrite_20261011::nightly_exclusive(static function()use(&$s):void{
                if(self::read(self::STATE)!==$s)throw new RuntimeException('queue_changed');$r=self::current();
                if($s['state']==='waiting'){
                    if(($r['nightly_scan_id']??null)!==$s['id'])throw new RuntimeException('queue_changed');if(in_array($r['state'],['rolled_back','cancelled'],true)){$s['state']='attention';$s['error']='queue_changed';self::save(self::STATE,$s);return;}if($r['state']==='complete_with_issues'){self::quarantine_batch($r,$s);if(!self::can_advance($r))throw new RuntimeException('queue_changed');foreach($r['pages'] as$source=>$status)if(($status['state']??null)==='attention')$s['held'][$source]=$r['id'];}elseif($r['state']!=='verified'){return;}
                    $s['completed']+=count(array_filter($r['pages'],static fn($row)=>($row['state']??null)==='verified'));$s['changed']=array_values(array_diff($s['changed'],$s['batch']??[]));foreach($s['batch']??[] as$source)unset($s['captures'][$source]);$s['state']=$s['changed']?'factory':self::scan_terminal($s);unset($s['batch']);$s['at']=time();self::save(self::STATE,$s);return;
                }
                if(!in_array($r['state']??null,['verified','rolled_back','cancelled'],true)&&!self::can_advance($r)){if(($r['state']??null)==='complete_with_issues')throw new RuntimeException('queue_changed');return;}$cursor=$s['cursor'];$id=$s['ids'][$cursor]??0;
                if(!$id){$s['state']=$s['changed']?'factory':self::scan_terminal($s);$s['at']=time();self::save(self::STATE,$s);return;}
                if(($post=self::public_post($id))&&self::actor_can($s['actor'],$id)){
                    $source=ZP_EN_Rewrite_20261011::path(wp_parse_url(get_permalink($id),PHP_URL_PATH));if((int)get_option('page_on_front',0)===$id)$source='/';self::register_source($post,$source);
                    $capture=ZP_EN_Rewrite_20261011::capture_incremental_source($source);$idx=self::read(ZP_EN_Rewrite_20261011::INDEX);$v=$idx['active'][$source]??'';$previous=is_string($v)&&preg_match('/^[a-f0-9]{32}$/D',$v)?self::page($source,$v):[];
                    if(self::unchanged_quarantine($capture))$s['held'][$source]=self::quarantines()[$source]['revision'];elseif(self::changed($capture,$previous,is_string($v)?$v:'')){$s['changed'][]=$source;$s['captures'][$source]=self::capture_receipt($capture);}
                }
                $s['cursor']=$cursor+1;$s['at']=time();self::save(self::STATE,$s);
            });
            $s=self::read(self::STATE);$r=self::current();if(in_array($s['state']??null,['scanning','factory'],true)||($s['state']??null)==='waiting'&&($r['state']??null)!=='attention')self::schedule_scan(30);
        }catch(Throwable$e){if($e->getMessage()==='busy'){self::schedule_scan(30);return;}$now=self::read(self::STATE);if($e->getMessage()==='queue_changed'&&$now!==$s)return;if(($now['id']??null)===($s['id']??null)){$now['state']='attention';$now['at']=time();$now['error']=in_array($e->getMessage(),self::ERRORS,true)?$e->getMessage():'source_invalid';try{self::save(self::STATE,$now);}catch(Throwable$ignored){}}}
        finally{wp_set_current_user(0);wp_set_current_user($old_user);}
    }
    public static function legacy_blocked(): bool { return true; } // Disabled nightly never revives the retired paid engine.
    /** Read-only proof for a new public source. No page body, credential or provider request is returned. */
    public static function missing_source(int $id): array {
        if (!self::actor_can((int)get_current_user_id(),$id)||!class_exists('ZPL\\Dict')||!($post=self::public_post($id))) return [];
        $source=ZP_EN_Rewrite_20261011::path(wp_parse_url(get_permalink($id),PHP_URL_PATH));if ((int)get_option('page_on_front',0)===$id) $source='/';
        if ($source===''||isset(ZP_EN_Rewrite_20261011::manifest()[$source])||\ZPL\Dict::has_page($source)) return [];
        $routes=\ZPL\Router::routes();$en=$routes[$source]??('/en'.$source);
        if (!is_string($en)||ZP_EN_Rewrite_20261011::path($en)!==$en||!str_starts_with($en,'/en/')) return [];
        return ['post_id'=>$id,'source'=>$source,'proof'=>self::native_digest($post,$en)];
    }
    private static function missing_admission(): array {
        if (!self::enabled()||!self::actor_can((int)get_current_user_id())) throw new RuntimeException('forbidden');
        $boot=self::bootstrap();if (!$boot||($boot['actor']??null)!==(int)get_current_user_id()) throw new RuntimeException('bootstrap_required');
        $index=self::read(ZP_EN_Rewrite_20261011::INDEX);$revision=self::current();$state=self::read(self::STATE);
        if (($revision['id']??null)!==($index['current']??null)) throw new RuntimeException('queue_changed');
        if (!in_array($revision['state']??null,['verified','rolled_back','cancelled'],true)&&!self::can_advance($revision)) throw new RuntimeException('busy');
        if ($state&&!in_array($state['state']??null,['complete','complete_with_issues','waiting_translation'],true)) throw new RuntimeException('busy');
        return ['current'=>$index['current'],'scan_proof'=>self::hash($state),'bootstrap'=>$boot];
    }
    public static function missing_form_context(): array {
        try { $c=self::missing_admission();return ['ready'=>true,'current'=>$c['current'],'scan_proof'=>$c['scan_proof']]; }
        catch(Throwable $e) { return ['ready'=>false,'reason'=>in_array($e->getMessage(),self::ERRORS,true)?$e->getMessage():'source_invalid']; }
    }
    /** Explicit operator selection seeds the existing scanner/factory, never starts a second paid engine. */
    public static function queue_missing(array $ids,array $proofs,string $current,string $scanProof): string {
        if (!array_is_list($ids)||!$ids||count($ids)>10||count(array_unique($ids,SORT_REGULAR))!==count($ids)||preg_match('/^[a-f0-9]{32}$/D',$current)!==1||preg_match('/^[a-f0-9]{64}$/D',$scanProof)!==1) throw new RuntimeException('scope_invalid');
        foreach ($ids as $id) if (!is_int($id)||$id<=0||!is_string($proofs[$id]??null)||preg_match('/^[a-f0-9]{64}$/D',$proofs[$id])!==1) throw new RuntimeException('scope_invalid');
        return ZP_EN_Rewrite_20261011::nightly_exclusive(static function()use($ids,$proofs,$current,$scanProof):string {
            $context=self::missing_admission();if ($context['current']!==$current||!hash_equals($context['scan_proof'],$scanProof)) throw new RuntimeException('queue_changed');
            foreach ($ids as $id) { $source=self::missing_source($id);if (!$source||!hash_equals($source['proof'],$proofs[$id])) throw new RuntimeException('source_changed'); }
            // Recheck all selected native publications and scanner identity before the one state write.
            if (self::missing_admission()!==$context) throw new RuntimeException('queue_changed');
            foreach ($ids as $id) { $source=self::missing_source($id);if (!$source||!hash_equals($source['proof'],$proofs[$id])) throw new RuntimeException('source_changed'); }
            self::save(self::STATE,['id'=>bin2hex(random_bytes(16)),'actor'=>(int)get_current_user_id(),'bootstrap'=>$context['bootstrap']['revision'],'state'=>'scanning','ids'=>$ids,'cursor'=>0,'changed'=>[],'captures'=>[],'held'=>[],'completed'=>0,'started_at'=>time(),'at'=>time(),'error'=>'']);self::schedule_scan();return 'queued';
        });
    }
    private static function missing_post(): string {
        if (($_POST['missing_request']??null)!=='1'||!is_string($_POST['missing_current']??null)||!is_string($_POST['missing_scan_proof']??null)||!is_array($_POST['missing_proofs']??null)||count($_POST['missing_proofs'])>500) throw new RuntimeException('scope_invalid');
        $raw=array_key_exists('missing_post_id',$_POST)?[$_POST['missing_post_id']]:($_POST['missing_post_ids']??null);
        if (!is_array($raw)||!array_is_list($raw)||!$raw||count($raw)>10) throw new RuntimeException('scope_invalid');$ids=[];
        foreach ($raw as $id) { if (!is_string($id)||preg_match('/^[1-9][0-9]{0,8}$/D',$id)!==1) throw new RuntimeException('scope_invalid');$ids[]=(int)$id; }
        return self::queue_missing($ids,$_POST['missing_proofs'],$_POST['missing_current'],$_POST['missing_scan_proof']);
    }
    public static function handle(): void {
        if (($_SERVER['REQUEST_METHOD']??'')!=='POST'||!current_user_can('manage_options')||!current_user_can('edit_posts')||!current_user_can('unfiltered_html')) wp_die('Brak uprawnień.');$action=$_POST['action']??'';if (!is_string($action)||!in_array($action,['zp_en_nightly_settings','zp_en_nightly_scan'],true)) wp_die('Nieprawidłowa operacja.');check_admin_referer($action);
        $notice='';if ($action==='zp_en_nightly_settings') { $value=$_POST['enabled']??'0';if (!is_string($value)||!in_array($value,['0','1'],true)) wp_die('Nieprawidłowe ustawienie.');self::save(self::CONFIG,['enabled'=>$value==='1']);self::maintain(); }
        elseif (array_key_exists('missing_request',$_POST)) { try { $notice=self::missing_post(); } catch(Throwable $e) { $notice=in_array($e->getMessage(),self::ERRORS,true)?$e->getMessage():'source_invalid'; } }
        else self::daily();
        $args=['page'=>'zp-tlumacz-en','tab'=>'status'];if ($notice!=='') $args['zp_en_missing_notice']=$notice;if (($_POST['_zp_workspace']??null)==='1') $args['zp_workspace']='1';wp_safe_redirect(add_query_arg($args,admin_url('admin.php')));exit;
    }
    public static function render(): void {
        if (!is_admin()||!current_user_can('manage_options')||($_SERVER['REQUEST_METHOD']??'GET')!=='GET'||wp_doing_ajax()||(defined('REST_REQUEST')&&REST_REQUEST)) return;
        $s=self::read(self::STATE);$b=self::bootstrap();$r=self::current();if (($s['state']??null)==='waiting'&&($r['state']??null)==='attention') { $s['state']='attention';$s['error']=$r['error']??'source_invalid'; }$labels=['scanning'=>'sprawdzamy nowe i zmienione strony','factory'=>'przygotowujemy zmienione źródła','waiting'=>'tłumaczenie zmian w trwałej kolejce','complete'=>'sprawdzenie zakończone','complete_with_issues'=>'sprawdzenie zakończone; część stron wymaga sprawdzenia','waiting_translation'=>'czekamy na pełne tłumaczenie i QA','attention'=>'sprawdzenie wstrzymane'];
        echo '<section class="zpAdmin20__card"><h2>Nocne aktualizacje EN · Luna</h2><p>Nowe i zmienione publiczne wpisy oraz strony, codziennie o 03:00 Europe/Warsaw. WP-Cron potrzebuje ruchu albo zadania cron na hostingu. Pełna kampania nie jest ponawiana.</p><p>'.esc_html(!$b?'Oczekiwanie na potwierdzone 137 stron pierwszej kampanii.':($labels[$s['state']??'']??'Gotowe do nocnego sprawdzenia.')).'</p><p>Sprawdzono: '.(int)($s['cursor']??0).' / '.count($s['ids']??[]).' · zmienione w kolejce: '.count($s['changed']??[]).' · ukończone aktualizacje: '.(int)($s['completed']??0).'. Model: gpt-6-luna, naturalny angielski UK/US. Limit dzienny: '.(int)\ZPTE\Settings::get('daily_chars').' znaków; nieznany wynik nie jest automatycznie generowany ponownie. Odłożone błędy: '.count($s['held']??[]).'.</p>';
        if (!empty($s['error'])) echo '<p>Operacja zatrzymana: '.esc_html(in_array($s['error'],self::ERRORS,true)?$s['error']:'source_invalid').'. Poprzednia poprawna mapa pozostaje w historii.</p>';
        echo '<form method="post" action="'.esc_url(admin_url('admin-post.php')).'"><input type="hidden" name="action" value="zp_en_nightly_settings">';wp_nonce_field('zp_en_nightly_settings');echo '<label><input type="checkbox" name="enabled" value="1"'.(self::enabled()?' checked':'').'> Nocne aktualizacje Luna</label> <button class="button">Zapisz</button></form><form method="post" action="'.esc_url(admin_url('admin-post.php')).'"><input type="hidden" name="action" value="zp_en_nightly_scan">';wp_nonce_field('zp_en_nightly_scan');echo '<button class="button">Sprawdź nowe i zmienione strony</button></form><p>Ręczne tłumaczenia mają pierwszeństwo. Prywatne oferty, briefy, wpisy z hasłem i noindex są pomijane. Dawna kolejka pozostaje zachowana i nie wykonuje równoległych płatnych zadań.</p></section>';
    }
    public static function register(): void {
        add_action('init',[self::class,'maintain'],30);add_action(self::DAILY,[self::class,'daily']);add_action(self::SCAN,[self::class,'scan']);add_action('zp_en_rewrite_completed',[self::class,'record_bootstrap']);add_filter('zp_en_rewrite_routes',[self::class,'routes'],20);add_filter('zpl_has_page',[self::class,'has_page'],20,2);add_filter('zpl_sitemap_pages',[self::class,'sitemap'],20);add_action('zpte_rewrite_card',[self::class,'render'],35);foreach (['zp_en_nightly_settings','zp_en_nightly_scan'] as $action) add_action('admin_post_'.$action,[self::class,'handle']);
    }
}
function zp_en_legacy_paid_blocked(): bool { return ZP_EN_Rewrite_Nightly_20261011::legacy_blocked(); }
