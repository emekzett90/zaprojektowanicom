<?php
if (!defined('ABSPATH')) exit;

/** Read-only projection; only the explicit existing-core lease holder may install legacy evidence. */
final class ZP_EN_Source_Guard_20261011 {
    public const POLICY='latest_post_cards_v1';
    public const LEGACY='zps_en_source_guard_20261011_legacy';
    public const SOURCE='/agencja-reklamowa-katowice/';
    private const PREFIX='zps_en_rewrite_20261011_';
    private static function hash($v): string { return hash('sha256',(string)wp_json_encode($v)); }
    private static function has(array $a,string $class): bool { return in_array($class,preg_split('/\s+/',trim($a['class']??'')),true); }
    private static function attrs(string $html,array $n): array { return \ZPL\Html::attrs(substr($html,$n['s'],$n['te']-$n['s'])); }
    private static function closed(string $html,array $n): bool { return ($n['s']??0)<($n['te']??0)&&$n['te']<($n['es']??0)&&$n['es']<($n['e']??0)&&preg_match('~^</'.preg_quote($n['n'],'~').'\s*>$~iD',substr($html,$n['es'],$n['e']-$n['es']))===1; }
    /** Delete physical card byte ranges from a COPY only; JSON, schema and all other bytes are preserved. */
    public static function project(string $html): array {
        if (strlen($html)>8000000) throw new RuntimeException('source_incomplete');
        $tree=\ZPL\Html::parse($html);$ids=[];$blogs=[];$containers=[];$footers=[];$walk=null;
        $walk=static function(array $node,int $main=0,int $footer=0,int $blog=0,int $body=0,int $opaque=0)use(&$walk,&$ids,&$blogs,&$containers,&$footers,$html):void {
            if (($node['t']??null)!==1) return;if ($node['n']==='main') $main++;if ($node['n']==='body') $body++;if (in_array($node['n'],['head','svg','math','template','pre','code','noscript','iframe','object'],true)) $opaque++;$a=self::attrs($html,$node);
            if (($a['id']??null)==='zpMegaFooter') { $ids[]=$node;if ($body!==1||$opaque!==0||$main>0||$footer!==0||$node['n']!=='footer'||!self::has($a,'zpMegaFooter')||!self::closed($html,$node)) throw new RuntimeException('source_invalid');$footers[]=$node;$footer++; }
            if (self::has($a,'zpMegaFooter__darkBand--blog')) { $blogs[]=$node;if ($node['n']!=='section'||$footer!==1||$blog!==0||!self::closed($html,$node)) throw new RuntimeException('source_invalid');$blog++; }
            if (array_key_exists('data-zp-footer-posts',$a)||self::has($a,'zpMegaFooter__posts')) { $containers[]=$node;if ($node['n']!=='div'||$footer!==1||$blog!==1||!array_key_exists('data-zp-footer-posts',$a)||!self::has($a,'zpMegaFooter__posts')||!self::closed($html,$node)) throw new RuntimeException('source_invalid'); }
            foreach ($node['c']??[] as $child) $walk($child,$main,$footer,$blog,$body,$opaque);
        };$walk($tree);
        if (!$ids&&!$blogs&&!$containers) return ['active'=>false,'units'=>ZP_EN_Rewrite_20261011::units($html),'cards'=>0];
        if (count($ids)!==1||count($footers)!==1||count($blogs)!==1||count($containers)!==1) throw new RuntimeException('source_invalid');
        $cards=[];foreach ($containers[0]['c'] as $node) {
            if ($node['t']===3) { if (trim(substr($html,$node['s'],$node['e']-$node['s']))!=='') throw new RuntimeException('source_invalid');continue; }
            $a=self::attrs($html,$node);if ($node['n']!=='article'||!self::has($a,'zpMegaFooter__post')||!self::closed($html,$node)) throw new RuntimeException('source_invalid');
            $anchors=[];foreach ($node['c']??[] as $child) { if ($child['t']===3) { if (trim(substr($html,$child['s'],$child['e']-$child['s']))!=='') throw new RuntimeException('source_invalid'); } elseif ($child['n']==='a') $anchors[]=$child;else throw new RuntimeException('source_invalid'); }
            if (count($anchors)!==1||!self::closed($html,$anchors[0])) throw new RuntimeException('source_invalid');$href=self::attrs($html,$anchors[0])['href']??'';$parts=wp_parse_url($href);$base=wp_parse_url(home_url('/'));
            if (!is_array($parts)||($parts['scheme']??'')!==($base['scheme']??'')||($parts['host']??'')!==($base['host']??'')||isset($parts['user'])||isset($parts['pass'])||isset($parts['port'])||isset($parts['query'])||isset($parts['fragment'])||!is_string($parts['path']??null)||ZP_EN_Rewrite_20261011::path($parts['path'])!==$parts['path']||preg_match('~^/(?:en|oferty|offers|briefy|briefs)(?:/|$)~i',$parts['path'])) throw new RuntimeException('source_invalid');
            $counts=['h3'=>0,'p'=>0,'img'=>0];$check=null;$check=static function(array $n)use(&$check,&$counts,$html):void { if (($n['t']??null)!==1) return;if (in_array($n['n'],['main','form','footer','section','header','head','title','h1','h2','script','style','iframe','input','textarea','meta','link','base','svg','template','object','embed','select','option','button','canvas','video','audio','math','pre','code','noscript','xmp'],true)) throw new RuntimeException('source_invalid');if (array_key_exists($n['n'],$counts)) $counts[$n['n']]++;foreach ($n['c']??[] as $child) $check($child); };$check($node);if ($counts!==['h3'=>1,'p'=>1,'img'=>1]) throw new RuntimeException('source_invalid');$cards[]=$node;
        }
        if (!$cards||count($cards)>10) throw new RuntimeException('source_invalid');$projected=$html;
        foreach (array_reverse($cards) as $node) $projected=substr_replace($projected,'<!-- latest-post card source projection -->',$node['s'],$node['e']-$node['s']);
        return ['active'=>true,'units'=>ZP_EN_Rewrite_20261011::units($projected),'cards'=>count($cards)];
    }
    private static function source_binding(array $p): string {
        $out=[];foreach (['source','en','post_id','native_hash','manual_hash','manual_version','source_hash','units'] as $k) { if (!array_key_exists($k,$p)) throw new RuntimeException('source_changed');$out[$k]=$p[$k]; }
        if (!is_array($p['units'])||!is_string($p['source_hash'])||self::hash($p['units'])!==$p['source_hash']) throw new RuntimeException('source_changed');return self::hash($out);
    }
    private static function validate(array $entry,array $page,string $binding): array {
        if (array_keys($entry)!==['policy','binding','protected_units','protected_hash','seal']||$entry['policy']!==self::POLICY||$entry['binding']!==$binding||!is_array($entry['protected_units'])||!$entry['protected_units']||count($entry['protected_units'])>1500||$entry['protected_hash']!==self::hash($entry['protected_units'])) throw new RuntimeException('source_changed');
        $seal=$entry['seal'];unset($entry['seal']);if ($seal!==self::hash($entry)) throw new RuntimeException('source_changed');foreach ($entry['protected_units'] as $key=>$kind) if (!is_string($key)||!is_string($kind)||!in_array($kind,['text','title','attr','meta','schema','data'],true)||!array_key_exists($key,$page['units'])) throw new RuntimeException('source_changed');return $entry;
    }
    public static function capture(array $page,string $html): array {
        $p=self::project($html);if (!$p['active']) return $page;$entry=['policy'=>self::POLICY,'binding'=>self::source_binding($page),'protected_units'=>$p['units'],'protected_hash'=>self::hash($p['units'])];$entry['seal']=self::hash($entry);self::validate($entry,$page,$entry['binding']);$page['source_projection']=$entry;return $page;
    }
    private static function read(string $name): array { wp_cache_delete($name,'options');$v=get_option($name,[]);if (!is_array($v)||strlen((string)wp_json_encode($v))>4000000) throw new RuntimeException('source_changed');return $v; }
    /** Read known immutable paid flights only. This never polls or reparses a provider response. */
    public static function flight_hash(array $page): string {
        $r=self::read(self::PREFIX.'r_'.($page['revision']??''));if (($r['id']??null)!==($page['revision']??null)||($r['actor']??null)!==($page['actor']??null)) throw new RuntimeException('source_changed');$policy=ZP_EN_Rewrite_20261011::model_policy($r);
        if (!is_array($page['chunks']??null)||!array_is_list($page['chunks'])||!is_int($page['next']??null)||$page['next']!==count($page['chunks'])||!$page['chunks']||count($page['chunks'])>1500) throw new RuntimeException('source_changed');$all=[];
        foreach ($page['chunks'] as $id) { if (!is_string($id)||preg_match('/^[a-f0-9]{32}$/D',$id)!==1) throw new RuntimeException('source_changed');$c=self::read(self::PREFIX.'c_'.$id);$j=self::read(self::PREFIX.'j_'.$id);
            if (($c['id']??null)!==$id||($c['revision']??null)!==($page['revision']??null)||($c['source']??null)!==($page['source']??null)||!is_array($c['request']??null)||($c['request_hash']??null)!==ZP_EN_Rewrite_Transport_20261011::request_hash($c['request'])||ZP_EN_Rewrite_Transport_20261011::chunk_policy($c)!==$policy||($c['request']['metadata']['zp_en_chunk']??null)!==$id||($c['request']['metadata']['zp_en_revision']??null)!==$page['revision']||($j['chunk_id']??null)!==$id||($j['revision']??null)!==$page['revision']||($j['request_hash']??null)!==$c['request_hash']||($j['key_fingerprint']??null)!==($c['key_fingerprint']??null)||($c['key_fingerprint']??null)!==($r['key_fingerprint']??null)||!ZP_EN_Rewrite_Transport_20261011::valid_id($j['response_id']??null)||($c['response_id']??null)!==$j['response_id']||($c['terminal']['status']??null)!=='completed'||($j['terminal']['status']??null)!=='completed'||($c['terminal']??null)!==$j['terminal']) throw new RuntimeException('source_changed');$all[$id]=['chunk'=>$c,'journal'=>$j];
        }return self::hash($all);
    }
    private static function legacy_binding(array $page): string { $out=[];foreach (['revision','actor','map','chunks','next'] as $k) { if (!array_key_exists($k,$page)) throw new RuntimeException('source_changed');$out[$k]=$page[$k]; }return self::hash([self::source_binding($page),$out,self::flight_hash($page)]); }
    private static function legacy_key(array $page): string { if (!is_string($page['revision']??null)||preg_match('/^[a-f0-9]{32}$/D',$page['revision'])!==1||($page['source']??null)!==self::SOURCE) return '';return hash('sha256',$page['revision'].'|'.$page['source']); }
    public static function guard(array $page): ?array {
        if (array_key_exists('source_projection',$page)) return self::validate(is_array($page['source_projection'])?$page['source_projection']:[],$page,self::source_binding($page));
        $key=self::legacy_key($page);if ($key==='') return null;$all=self::read(self::LEGACY);if (!array_key_exists($key,$all)) return null;
        $r=$all[$key];if (!is_array($r)||array_keys($r)!==['revision','source','actor','at','entry','seal']||$r['revision']!==$page['revision']||$r['source']!==$page['source']||!is_int($r['actor'])||$r['actor']<=0||!is_int($r['at'])||$r['at']<=0) throw new RuntimeException('source_changed');$seal=$r['seal'];unset($r['seal']);if ($seal!==self::hash($r)) throw new RuntimeException('source_changed');return self::validate(is_array($r['entry'])?$r['entry']:[],$page,self::legacy_binding($page));
    }
    public static function matches(array $page,string $html): bool {
        $g=self::guard($page);if ($g===null) return self::hash(ZP_EN_Rewrite_20261011::units($html))===($page['source_hash']??null);
        $now=self::project($html);return $now['active']&&$g['protected_hash']===self::hash($now['units'])&&$g['protected_units']===$now['units'];
    }
    public static function protected_units(array $page): array { $g=self::guard($page);return $g===null?$page['units']:$g['protected_units']; }
    /** Pure comparator for a future Nightly bridge; its verified/native/manual checks must remain outside. */
    public static function same_source(array $previous,array $capture): bool {
        try { $a=self::guard($previous);$b=self::guard($capture);if ($a===null||$b===null) return ($previous['source_hash']??null)===($capture['source_hash']??null);return ($previous['source']??null)===($capture['source']??null)&&$a['policy']===$b['policy']&&$a['protected_hash']===$b['protected_hash']&&$a['protected_units']===$b['protected_units']; } catch(Throwable $ignored) { return false; }
    }
    public static function old_html(): string {
        $raw=require __DIR__.'/data/en-source-agency-pre-footer-20261011.php';$bytes=is_string($raw)?base64_decode($raw,true):false;$html=is_string($bytes)?gzdecode($bytes,8000000):false;
        if (!is_string($html)||hash('sha256',$html)!=='c379f9bd8c269fad4c7faaa0c5d9bfcd98795e2432a6174f4c5c0e9cbb2669cf') throw new RuntimeException('source_changed');return $html;
    }
    /** Called only by the existing core's fresh actor/nonce/shared-lease controlled action. */
    public static function install(array $page,string $currentHtml,string $token): void {
        $lease=self::read(ZP_EN_Rewrite_20261011::LOCK);if (($lease['token']??null)!==$token||!is_int($lease['until']??null)||$lease['until']<=time()) throw new RuntimeException('busy');
        if (!current_user_can('manage_options')||!current_user_can('edit_posts')||!current_user_can('unfiltered_html')||!current_user_can('edit_post',(int)($page['post_id']??0))||($page['actor']??null)!==(int)get_current_user_id()||($page['source']??null)!==self::SOURCE||array_key_exists('source_projection',$page)) throw new RuntimeException('forbidden');
        $old=self::old_html();$full=ZP_EN_Rewrite_20261011::units($old);if ($full!==($page['units']??null)||self::hash($full)!==($page['source_hash']??null)) throw new RuntimeException('footer_proof_unavailable');$a=self::project($old);$b=self::project($currentHtml);if (!$a['active']||!$b['active']||$a['units']!==$b['units']) throw new RuntimeException('source_changed');
        $entry=['policy'=>self::POLICY,'binding'=>self::legacy_binding($page),'protected_units'=>$a['units'],'protected_hash'=>self::hash($a['units'])];$entry['seal']=self::hash($entry);self::validate($entry,$page,$entry['binding']);$r=['revision'=>$page['revision'],'source'=>$page['source'],'actor'=>(int)get_current_user_id(),'at'=>time(),'entry'=>$entry];$r['seal']=self::hash($r);$all=self::read(self::LEGACY);$key=self::legacy_key($page);if ($key==='') throw new RuntimeException('source_changed');
        if (isset($all[$key])) { if (self::guard($page)!==array_diff_key($entry,['seal'=>true])) throw new RuntimeException('source_changed');return; }
        $all[$key]=$r;$all=array_slice($all,-6,null,true);update_option(self::LEGACY,$all,false);if (self::read(self::LEGACY)!==$all) throw new RuntimeException('storage_failed');
    }
}
