<?php
if (!defined('ABSPATH')) exit;
final class ZPT2_Source {
    public static $last_fetch=[];
    public static function descriptor(int $id): array {
        clean_post_cache($id);wp_cache_delete($id,'post_meta');$p=get_post($id);
        if (!$p||$p->post_status!=='publish'||!in_array($p->post_type,['post','page'],true)||(string)$p->post_password!=='') throw new RuntimeException('source_private');
        $source=ZP_EN_Rewrite_20261011::path(wp_parse_url(get_permalink($id),PHP_URL_PATH));
        if ((int)get_option('page_on_front')===$id) $source='/';
        if ($source===''||get_permalink($id)!==home_url($source)||preg_match('~^/(?:en|oferty|offers|briefy|briefs|dziekujemy)(?:/|$)~i',$source)||\ZPL\Router::never($source)||\ZPL\Router::is_excluded($source)) throw new RuntimeException('source_private');
        if (function_exists('zp_seo_plan_is_private_path')&&zp_seo_plan_is_private_path($source)) throw new RuntimeException('source_private');
        if (preg_match('/\[(?:zp_offer|zp_brief|zaprojektowani_offer|zaprojektowani_brief)\b/i',$p->post_content)) throw new RuntimeException('source_private');
        $robots=get_post_meta($id,'rank_math_robots',true);if ($robots===''||$robots===false) $robots=[];if (is_string($robots)) $robots=preg_split('/[\s,]+/',strtolower($robots));
        if (!is_array($robots)) throw new RuntimeException('source_noindex');
        foreach ($robots as $r) if (!is_string($r)||in_array(strtolower($r),['noindex','noarchive'],true)) throw new RuntimeException('source_noindex');
        $routes=\ZPL\Router::routes();$en=$routes[$source]??('/en'.$source);
        if (ZP_EN_Rewrite_20261011::path($en)!==$en||strpos($en,'/en/')!==0||($source!=='/'&&$en==='/en/')) throw new RuntimeException('route_invalid');
        foreach ($routes as $pl=>$target) if ($target===$en&&$pl!==$source) throw new RuntimeException('route_collision');
        $physical=(int)url_to_postid(home_url($en));if ($physical&&$physical!==$id) throw new RuntimeException('route_collision');
        return ['source'=>$source,'en'=>$en,'post_id'=>$id,'title'=>(string)$p->post_title];
    }
    /** Only fields capable of changing the published message; editor timestamps/focus keywords are absent. */
    public static function native(int $id): string {
        $p=get_post($id);if (!$p) throw new RuntimeException('source_private');$out=[];
        foreach (['post_title','post_content','post_excerpt','post_name','post_parent','post_status','post_password','post_type'] as $k) $out[$k]=$p->$k;
        foreach (['_elementor_data','rank_math_title','rank_math_description','rank_math_canonical_url','rank_math_robots','rank_math_schema_Article','rank_math_schema_FAQPage','_thumbnail_id'] as $k) $out[$k]=get_post_meta($id,$k,true);
        return ZPT2_Store::hash($out);
    }
    public static function fetch(string $path, string $lang='pl',string $probe=''): string {
        self::$last_fetch=['path'=>$path,'lang'=>$lang];
        if (ZP_EN_Rewrite_20261011::path($path)!==$path) throw new RuntimeException('route_invalid');
        $headers=['Accept'=>'text/html','Accept-Language'=>$lang==='pl'?'pl-PL':'en-GB','Cache-Control'=>'no-cache'];if(preg_match('/^[a-f0-9]{64}$/D',$probe))$headers['X-ZPT2-Probe']=$probe;
        $url=home_url($path);
        if($lang==='pl'&&preg_match('/^[a-f0-9]{64}$/D',$probe)&&class_exists('ZPT2_Engine')){
            $p=ZPT2_Engine::active($path);$job=$p?ZPT2_Store::get('job_'.$p['id']):[];
            if($p&&($job['id']??null)===$p['id']&&($job['source']??null)===$path&&($job['state']??'')==='checking'&&($job['publication_only']??false)===true&&($job['commit']['target']??null)===['id'=>$p['id'],'digest'=>$p['digest']]&&hash_equals(hash_hmac('sha256','zpt2-serving|'.$p['id'].'|'.$p['digest'],wp_salt('auth')),$probe)){$url=add_query_arg('zpt2_check',bin2hex(random_bytes(16)),$url);self::$last_fetch['fresh_query']=true;}
        }
        $r=wp_remote_get($url,['timeout'=>8,'redirection'=>0,'sslverify'=>true,'limit_response_size'=>8000000,'cookies'=>['zpl_lang_pref'=>$lang],'headers'=>$headers,'user-agent'=>'ZP-Translator-v2/2.0']);
        self::$last_fetch['http']=is_wp_error($r)?null:wp_remote_retrieve_response_code($r);
        if(!is_wp_error($r)&&preg_match('/^[a-f0-9]{64}$/D',$probe)){
            if(self::$last_fetch['http']===500){
                $body=wp_remote_retrieve_body($r);$facts=['body_bytes'=>is_string($body)?strlen($body):null,'body_sha256'=>is_string($body)?hash('sha256',$body):null,'headers'=>[]];
                foreach(['content-type','server','via','x-cache','cf-ray']as$h){$value=wp_remote_retrieve_header($r,$h);if(is_string($value)&&$value!==''&&!preg_match('~authorization|bearer|api[_ -]?key|password|secret|token|\bsk-~i',$value))$facts['headers'][$h]=mb_substr(preg_replace('/[\x00-\x1f\x7f]/',' ',$value)??'',0,200,'UTF-8');}
                $facts['wp_critical_notice']=is_string($body)&&(stripos($body,'critical error')!==false||stripos($body,'błąd krytyczny')!==false||stripos($body,'blad krytyczny')!==false);
                $facts['wp_error_page']=is_string($body)&&(stripos($body,'id="error-page"')!==false||stripos($body,"id='error-page'")!==false);
                $facts['php_error_notice']=is_string($body)&&(stripos($body,'Fatal error')!==false||stripos($body,'Parse error')!==false||stripos($body,'Uncaught ')!==false);
                $facts['edge_notice']=is_string($body)&&(stripos($body,'cloudflare')!==false||stripos($body,'Bad Gateway')!==false||stripos($body,'Service Unavailable')!==false);self::$last_fetch['response_500']=$facts;
                $encoded=wp_remote_retrieve_header($r,'x-zpt2-observer');if(is_string($encoded)&&strlen($encoded)<4000){$raw=base64_decode($encoded,true);$observer=is_string($raw)?json_decode($raw,true):null;if(is_array($observer)&&($observer['token']??null)===$probe&&($observer['source']??null)===$path&&($observer['armed']??null)===true){unset($observer['token']);self::$last_fetch['fatal_observer']=$observer;}}
            }
            $fatal=null;$encoded=wp_remote_retrieve_header($r,'x-zpt2-fatal');if(is_string($encoded)&&strlen($encoded)<8000){$raw=base64_decode($encoded,true);if(is_string($raw))$fatal=json_decode($raw,true);}
            if(!is_array($fatal)){$body=wp_remote_retrieve_body($r);if(is_string($body)&&preg_match('~<pre id="zpt2-fatal-probe">(.{1,8000}?)</pre>~s',$body,$fm))$fatal=json_decode(html_entity_decode($fm[1],ENT_QUOTES|ENT_HTML5,'UTF-8'),true);}
            if(is_array($fatal)&&($fatal['token']??null)===$probe&&is_array($fatal['php_fatal']??null))self::$last_fetch['php_fatal']=$fatal['php_fatal'];
        }
        if($lang==='pl'&&self::$last_fetch['http']===500&&!empty(self::$last_fetch['response_500']['wp_critical_notice'])&&!isset(self::$last_fetch['fatal_observer'])&&!isset(self::$last_fetch['php_fatal'])&&preg_match('/^[a-f0-9]{64}$/D',$probe)){
            $p=class_exists('ZPT2_Engine')?ZPT2_Engine::active($path):[];$job=$p?ZPT2_Store::get('job_'.$p['id']):[];
            if($p&&($job['id']??null)===$p['id']&&($job['source']??null)===$path&&($job['state']??'')==='checking'&&($job['publication_only']??false)===true&&($job['commit']['target']??null)===['id'=>$p['id'],'digest'=>$p['digest']]&&hash_equals(hash_hmac('sha256','zpt2-serving|'.$p['id'].'|'.$p['digest'],wp_salt('auth')),$probe))self::$last_fetch['native_sandbox']=self::native_sandbox($path,$headers);
        }
        if (is_wp_error($r)||wp_remote_retrieve_response_code($r)!==200) throw new RuntimeException('source_unavailable');
        $html=wp_remote_retrieve_body($r);$type=wp_remote_retrieve_header($r,'content-type');
        if (!is_string($html)||strlen($html)>=8000000||!is_string($type)||stripos($type,'text/html')===false||stripos($html,'</head>')===false||strpos($html,'id="wpadminbar"')!==false) throw new RuntimeException('source_invalid');
        self::$last_fetch['x_robots_blocked']=false;foreach((array)wp_remote_retrieve_header($r,'x-robots-tag')as$tag)if(preg_match('/\b(?:noindex|noarchive)\b/i',(string)$tag))self::$last_fetch['x_robots_blocked']=true;
        self::$last_fetch['overlay_present']=strpos($html,'id="zp-en-approved-overlay"')!==false;
        if($probe!==''&&preg_match('~<script type="application/json" id="zpt2-serving-probe">(.{1,12000}?)</script>~s',$html,$pm)){$pd=json_decode($pm[1],true);if(is_array($pd)&&($pd['token']??null)===$probe)self::$last_fetch['runtime']=$pd;}
        $canon=[];preg_match_all('/<(?:meta|link)\b[^>]*>/i',$html,$m);
        foreach ($m[0] as $tag) { $a=\ZPL\Html::attrs($tag);if (in_array(strtolower($a['name']??''),['robots','googlebot','bingbot'],true)&&preg_match('/\b(?:noindex|noarchive)\b/i',$a['content']??'')) self::$last_fetch['meta_robots_blocked']=true;if (strtolower($a['rel']??'')==='canonical') $canon[]=$a['href']??''; }
        self::$last_fetch['canonical_match']=$canon===[home_url($path)];
        if(self::$last_fetch['x_robots_blocked']||!empty(self::$last_fetch['meta_robots_blocked']))throw new RuntimeException('source_noindex');
        if ($canon!==[home_url($path)]) throw new RuntimeException('canonical_invalid');
        return $html;
    }
    /** Diagnostic only: native WordPress scraper is armed before active plugins; original failure always remains authoritative. */
    private static function native_sandbox(string $path,array $headers): array {
        $key=bin2hex(random_bytes(16));$nonce=bin2hex(random_bytes(16));$name='scrape_key_'.$key;
        try{
            if(!set_transient($name,$nonce,60)||get_transient($name)!==$nonce)return['status'=>'nonce_storage_failed'];
            $url=add_query_arg(['wp_scrape_key'=>$key,'wp_scrape_nonce'=>$nonce],home_url($path));$r=wp_remote_get($url,['timeout'=>8,'redirection'=>0,'sslverify'=>true,'limit_response_size'=>8000000,'cookies'=>['zpl_lang_pref'=>'pl'],'headers'=>$headers,'user-agent'=>'ZP-Translator-v2/2.0']);
            $out=['http'=>is_wp_error($r)?null:wp_remote_retrieve_response_code($r)];if(is_wp_error($r))return$out+['status'=>'loopback_failed'];$body=wp_remote_retrieve_body($r);
            $start="###### wp_scraping_result_start:$key ######";$end="###### wp_scraping_result_end:$key ######";$pos=is_string($body)?strpos($body,$start):false;if($pos===false)return$out+['status'=>'marker_missing'];
            $payload=substr($body,$pos+strlen($start));$last=strpos($payload,$end);if($last===false||$last>20000)return$out+['status'=>'marker_invalid'];$data=json_decode(trim(substr($payload,0,$last)),true);
            if($data===true)return$out+['status'=>'no_fatal_reported'];if(!is_array($data)||!in_array((int)($data['type']??0),[E_ERROR,E_PARSE,E_CORE_ERROR,E_COMPILE_ERROR,E_USER_ERROR,E_RECOVERABLE_ERROR],true))return$out+['status'=>'scrape_error'];
            $message=(string)($data['message']??'');if(preg_match('~authorization|bearer|api[_ -]?key|password|secret|token|\bsk-[A-Za-z0-9_-]{10,}~i',$message))$message='';$message=preg_split('/\r?\n(?:Stack trace:|#\d|\s*thrown in)/',$message,2)[0];$message=preg_replace('~"[^"\r\n]*"|\'[^\'\r\n]*\'~','[quoted]',$message)??'';$message=str_replace(ABSPATH,'[ABSPATH]/',$message);$message=mb_substr(preg_replace('/[\x00-\x1f\x7f]/',' ',$message)??'',0,1200,'UTF-8');
            $file=(string)($data['file']??'');$file=strpos($file,ABSPATH)===0?substr($file,strlen(ABSPATH)):$file;if(strpos($file,'..')!==false||str_starts_with($file,'/')||strpos($file,'\\')!==false)$file=basename($file);
            return$out+['status'=>'fatal','php_fatal'=>['type'=>(int)$data['type'],'file'=>mb_substr($file,0,300,'UTF-8'),'line'=>(int)($data['line']??0),'message'=>$message]];
        }catch(Throwable$e){return['status'=>'capture_failed'];}finally{delete_transient($name);}
    }
    /** Elementor single-post template: only the exact post-content widget, never the template chrome. */
    private static function post_scope(array $nodes,int $id): array {
        if($id<1)return[];
        $has=static fn($n,$class)=>in_array($class,preg_split('/\s+/',trim($n['attrs']['class']??'')),true);
        $inside=static fn($n,$p)=>$n['s']>$p['s']&&$n['e']<=$p['e'];
        $bodies=array_values(array_filter($nodes,static fn($n)=>$n['tag']==='body'&&$has($n,'single-post')&&$has($n,'postid-'.$id)));
        $templates=array_values(array_filter($nodes,static fn($n)=>($n['attrs']['data-elementor-type']??'')==='single-post'&&$has($n,'elementor-location-single')&&$has($n,'post-'.$id)));
        if(count($bodies)!==1||count($templates)!==1||!$inside($templates[0],$bodies[0]))return[];$template=$templates[0];
        $roots=array_values(array_filter($nodes,static fn($n)=>$n['tag']==='div'&&($n['attrs']['data-elementor-type']??'')==='wp-post'&&($n['attrs']['data-elementor-id']??'')===(string)$id&&($n['attrs']['data-elementor-post-type']??'')==='post'&&$inside($n,$template)));
        if(count($roots)!==1)return[];$root=$roots[0];
        $widgets=array_filter($nodes,static fn($n)=>$has($n,'elementor-widget-theme-post-content')&&$inside($n,$template)&&$inside($root,$n));if(count($widgets)!==1)return[];
        $extra=[];$excluded=[];
        // Theme-owned H1/lead are outside wp-post; their exact classes identify article fields.
        foreach(['zpSinglePostHero__title','zpSinglePostHero__lead']as$class){
            $fields=array_values(array_filter($nodes,static fn($n)=>$has($n,$class)&&$inside($n,$template)&&!$inside($n,$root)));
            if(count($fields)>1)return[];
            if($fields&&(($class==='zpSinglePostHero__title'&&$fields[0]['tag']==='h1')||($class==='zpSinglePostHero__lead'&&$fields[0]['tag']==='p')))$extra[]=$fields[0];
        }
        foreach($nodes as$n){
            if(!$inside($n,$root))continue;
            if(in_array($n['tag'],['nav','footer'],true)||$has($n,'zpu-related')||$has($n,'zpRelPosts')||$has($n,'zpRelatedGrid')){
                $block=$n;
                if($has($n,'zpRelatedGrid'))foreach($nodes as$parent)if($parent['tag']==='section'&&$inside($parent,$root)&&$inside($n,$parent)&&($block===$n||$parent['s']>$block['s']))$block=$parent;
                $excluded[]=$block;
            }
        }
        return['root'=>$root,'extra'=>$extra,'excluded'=>$excluded];
    }
    public static function extract(string $html,int $post_id=0): array {
        \ZPL\Html::$extra=array_values(array_filter((array)get_option('zpl_extra_attrs',[]),static fn($v)=>is_string($v)&&preg_match('/^data-[a-z0-9-]{1,40}$/D',$v)));
        $root=\ZPL\Html::parse($html);$ranges=[];$nodes=[];$main=[];$elementor=[];
        $walk=null;$walk=static function($n,$path='')use(&$walk,&$ranges,&$nodes,&$main,&$elementor,$html,$post_id) {
            if (($n['t']??0)!==1) return;
            $a=\ZPL\Html::attrs(substr($html,$n['s'],$n['te']-$n['s']));$parentPath=$path;$path.='/'.$n['n'].(isset($a['id'])?'#'.$a['id']:'');
            $nodes[]=['s'=>$n['s'],'e'=>$n['e'],'path'=>$path,'tag'=>$n['n'],'attrs'=>$a];
            if ($n['n']==='main'||($a['role']??'')==='main') $main[]=$n;
            if ($post_id>0&&$n['n']==='div'&&str_ends_with($parentPath,'/body')&&($a['data-elementor-type']??'')==='wp-page'&&($a['data-elementor-id']??'')===(string)$post_id&&in_array($a['data-elementor-post-type']??'',['page','post'],true)&&!preg_match('/(?:^|\s)elementor-location-(?:header|footer)(?:\s|$)/',$a['class']??'')) $elementor[]=$n;
            if ($n['n']==='head') $ranges[]=['s'=>$n['s'],'e'=>$n['e'],'scope'=>'meta'];
            foreach ($n['c']??[] as $c) $walk($c,$path);
        };$walk($root);
        // Ambiguous or missing main is a source error; never quietly send the entire site chrome.
        if (!$main&&count($elementor)===1) $main=$elementor;
        $excluded=[];
        if(!$main&&!$elementor){$post=self::post_scope($nodes,$post_id);if($post){$main=[$post['root']];$excluded=$post['excluded'];foreach($post['extra']as$field)$ranges[]=['s'=>$field['s'],'e'=>$field['e'],'scope'=>'body'];}}
        if (count($main)!==1) throw new RuntimeException('main_scope_missing');
        $ranges[]=['s'=>$main[0]['s'],'e'=>$main[0]['e'],'scope'=>'body'];$units=[];
        $scope=static function(int $s)use($ranges,$excluded):string{foreach($excluded as$r)if($s>=$r['s']&&$s<$r['e'])return'';foreach($ranges as $r)if($s>=$r['s']&&$s<$r['e'])return$r['scope'];return'';};
        $context=static function(int $s)use($nodes):string{$best='';foreach($nodes as$n)if($s>=$n['s']&&$s<$n['e']&&strlen($n['path'])>strlen($best))$best=$n['path'];return$best;};
        $add=static function(string $key,string $kind,int $s,string $extra='')use(&$units,$scope,$context) {
            $part=$scope($s);if ($part==='') return;
            if (preg_match('/^(?:[a-f0-9]{8,}|[A-Za-z0-9_-]{24,})$/iD',$key)) return;
            if (strlen($key)>16000) throw new RuntimeException('unit_too_large');
            $tpl=\ZPL\Dict::template($key);$key=$tpl!==null?$tpl[0]:$key;
            $id='u'.substr(hash('sha256',$key),0,30);$ctx=$kind.':'.$context($s).$extra;
            if (!isset($units[$id])) $units[$id]=['id'=>$id,'pl'=>$key,'kind'=>$kind,'component'=>$part,'contexts'=>[]];
            if (!in_array($ctx,$units[$id]['contexts'],true)) $units[$id]['contexts'][]=$ctx;
        };
        // Html::walk supplies exact offsets for segment and attribute context while preserving runtime marker rules.
        \ZPL\Html::walk($root,static function($seg)use($add){$n=$seg['root']??$seg['node'];$add($seg['k'],$seg['type']==='title'?'title':'text',$n['s']);},static function($el,$a)use($add,$scope,$html){
            if ($scope($el['s'])==='') return;$a=$a??\ZPL\Html::attrs(substr($html,$el['s'],$el['te']-$el['s']));
            if ($el['n']==='script') {
                if (strtolower($a['type']??'')==='application/ld+json'&&!empty($el['c'])) {
                    $v=json_decode(substr($html,$el['c'][0]['s'],$el['c'][0]['e']-$el['c'][0]['s']),true);$walk=null;
                    $walk=static function($v,$key='',$path='')use(&$walk,$add,$el){if(is_array($v)){foreach($v as$k=>$x)$walk($x,is_int($k)?$key:(string)$k,$path.'/'.$k);return;}if(is_string($v)&&!empty(\ZPL\Page::SCHEMA_KEYS[$key])){$text=\ZPL\Html::norm(strip_tags($v));if(\ZPL\Html::human($text))$add($text,'schema',$el['s'],' JSON-LD'.$path);}};$walk($v);
                }
                return;
            }
            foreach (\ZPL\Page::attr_items($el,$a) as $it) $add($it[1],$it[2],$el['s'],'@'.$it[0]);
        });
        // Approved Suite window.zp* data is interpreted by the existing parser, never arbitrary JavaScript.
        $mainContent=substr($html,$main[0]['s'],$main[0]['e']-$main[0]['s']);
        usort($excluded,static fn($a,$b)=>$a['s']<=>$b['s']);$outer=[];$end=-1;foreach($excluded as$block){if($block['s']<$end)continue;$outer[]=$block;$end=$block['e'];}
        foreach(array_reverse($outer)as$block)$mainContent=substr_replace($mainContent,'',$block['s']-$main[0]['s'],$block['e']-$block['s']);
        $mainHtml='<html><head></head><body>'.$mainContent.'</body></html>';
        $stats=\ZPL\Page::$stats;$missing=\ZPL\Page::$missing;$had=array_key_exists('zpl_schema_lang',$GLOBALS);$previous=$GLOBALS['zpl_schema_lang']??null;
        try {\ZPL\Page::translate($mainHtml,static function($key,$kind)use($add,$main){if($kind==='data')$add($key,'data',$main[0]['s'],' Suite structured data');return null;},static fn($url)=>$url,['lang'=>'en']);}
        finally {\ZPL\Page::$stats=$stats;\ZPL\Page::$missing=$missing;if($had)$GLOBALS['zpl_schema_lang']=$previous;else unset($GLOBALS['zpl_schema_lang']);}
        if (!$units||count($units)>4000||strlen(wp_json_encode($units))>1500000) throw new RuntimeException('source_incomplete');
        ksort($units,SORT_STRING);return $units;
    }
    public static function capture(int $id,string $probe=''): array {
        $d=self::descriptor($id);$before=self::native($id);$units=self::extract(self::fetch($d['source'],'pl',$probe),$id);
        if ($before!==self::native($id)||$d!==self::descriptor($id)) throw new RuntimeException('source_changed');
        $parts=['body'=>[],'meta'=>[]];foreach($units as$key=>$u)$parts[$u['component']][$key]=$u;
        return $d+['units'=>$units,'native'=>$before,'hash'=>ZPT2_Store::hash([$d['source'],$d['en'],$units]),'body_hash'=>ZPT2_Store::hash($parts['body']),'meta_hash'=>ZPT2_Store::hash($parts['meta']),'captured_at'=>time()];
    }
    public static function checks(string $pl,$raw,string $kind,array $context=[]): array {
        if(!is_string($raw))return['valid'=>false,'failed'=>['missing_or_type']];
        $en=\ZPL\Html::norm(ZP_EN_Rewrite_20261011::normalise_money_spacing($raw));$failed=[];$details=[];
        if($en===''||!mb_check_encoding($en,'UTF-8'))$failed[]='empty_or_encoding';
        if(strlen($en)>strlen($pl)*4+100||(strlen($pl)>60&&strlen($en)<strlen($pl)*0.25))$failed[]='length';
        if(!\ZPL\Html::markers_match($pl,$en))$failed[]='markers';
        if(!ZP_EN_Rewrite_20261011::decorative_identity($pl,$en,$kind)&&preg_match('/<(?!\/?\d+\/?\>)[^>]*>/',$en))$failed[]='markup';
        $strip=static fn($v)=>preg_replace('/<\/?\d+\/?>/','',$v)??$v;
        foreach(['urls_emails'=>'~https?://(?=[\pL\pN\[])[^\s<>]+|(?<![\pL\pN_])www\.(?=[\pL\pN-]+\.[\pL]{2,}\b)[^\s<>]+|[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}~iu','placeholders'=>'/\{\d+\}/','entities'=>'/&(?:[A-Za-z][A-Za-z0-9]+|#[0-9]+|#x[0-9a-fA-F]+);/','numbers'=>'/(?<![\pL\d])\d+(?:[.,]\d+)*(?![\pL\d])/u']as$label=>$rx){preg_match_all($rx,$strip($pl),$a);preg_match_all($rx,$strip($en),$b);$a=$a[0];$b=$b[0];sort($a);sort($b);if($a!==$b){$failed[]=$label;$details[$label]=['pl'=>$a,'en'=>$b];}}
        $a=ZP_EN_Rewrite_20261011::currency_tokens($pl);$b=ZP_EN_Rewrite_20261011::currency_tokens($en);if($a!==$b){$failed[]='currency';$details['currency']=['pl'=>$a,'en'=>$b];}
        foreach(ZP_EN_Rewrite_20261011::brand_counts($pl,$en)as$brand=>$counts)if(($counts['pl']>0)!==($counts['en']>0)){$failed[]='brand:'.$brand;$details['brands'][$brand]=$counts;}
        if(preg_match_all('/(?<!\pL)(?:się|jest|dla|oraz|że|który|która|które|naszą|twoje|będzie|można|podstrony|sprawdź|zamów)(?!\pL)/iu',$en)>=2)$failed[]='polish_residue';
        if(!ZP_EN_Rewrite_20261011::decorative_identity($pl,$en,$kind)&&$pl===$en&&preg_match('/(?<!\pL)(?:strona|strony|projektowanie|sprawdź|zamów|podstrony|dla|się|jest|oraz)(?!\pL)/iu',$pl))$failed[]='untranslated';
        $core=ZP_EN_Rewrite_20261011::valid_translation($pl,$en,$kind);$valid=$core;$alias=false;
        // Only this semantic alias, only with explicit Facebook in this page's own title.
        // PL, EN, hashes and all factual gates remain unchanged; no global brand whitelist.
        if(!$core&&$failed===['brand:Facebook']&&$kind==='text'&&preg_match('/(?<!\pL)Facebook(?:u|a|iem|owi)?(?!\pL)/iu',(string)($context['title']??''))){
            $fan=preg_match_all('/(?<![\pL\pN._\/@-])fanpage(?![\pL\pN._\/@-])/iu',$pl);
            $pages=preg_match_all('/(?<!\pL)Facebook[ \t]+Pages?(?!\pL)/iu',$en);
            $brands=ZP_EN_Rewrite_20261011::brand_counts($pl,$en);
            if($fan>0&&$pages>0&&$pages<=$fan&&$brands['Facebook']['pl']===0&&$brands['Facebook']['en']===$pages){
                $comparison=preg_replace('/(?<!\pL)Facebook[ \t]+Pages?(?!\pL)/iu','fanpage',$en);
                if(ZP_EN_Rewrite_20261011::valid_translation($pl,$comparison,$kind)){$valid=true;$alias=true;$failed=[];}
            }
        }
        if(!$valid&&!$failed)$failed[]='core_other';
        return['valid'=>$valid,'core_valid'=>$core,'context_alias'=>$alias?'fanpage_facebook_page':'','failed'=>$failed,'details'=>$details];
    }
    private static function repair_reason(array $check): string {
        $labels=['missing_or_type'=>'Return a string for this exact ID','empty_or_encoding'=>'Return nonempty valid UTF-8 text','length'=>'Preserve the full original meaning without adding or omitting material','markers'=>'Preserve every numbered inline marker in matching order','markup'=>'Add no HTML or new tags','urls_emails'=>'Keep every original URL and email exactly','placeholders'=>'Keep every numbered {n} placeholder exactly','entities'=>'Keep every original HTML entity exactly','numbers'=>'Keep every original numeric token exactly, including decimal punctuation','currency'=>'Keep original currencies and amounts; do not convert','polish_residue'=>'Translate the remaining Polish words naturally','untranslated'=>'Translate the Polish sentence into English','core_other'=>'Preserve all facts, brands, links, numeric tokens and markers'];$out=[];
        foreach($check['failed']as$code){$text=$labels[$code]??(strpos($code,'brand:')===0?'Preserve the original brand reference: '.substr($code,6):$code);if(isset($check['details'][$code]))$text.='; expected/source and received: '.wp_json_encode($check['details'][$code],JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);$out[]=$text;}
        return mb_substr(implode('. ',$out),0,2000,'UTF-8');
    }
    public static function validate(array $items,array $result,array $context=[]): array {
        $valid=[];$invalid=[];
        foreach ($items as $id=>$u) {
            $en=$result[$id]??null;
            if (!is_string($en)) {$invalid[$id]='missing_or_type';continue;}
            $en=\ZPL\Html::norm(ZP_EN_Rewrite_20261011::normalise_money_spacing($en));
            $check=self::checks($u['pl'],$en,$u['kind'],$context);if(!$check['valid']){$invalid[$id]=self::repair_reason($check);continue;}
            $valid[$id]=$en;
        }
        // Extraneous output is rejected as a contract violation; never inserted into a page map.
        return ['valid'=>$valid,'invalid'=>$invalid,'extra'=>array_keys(array_diff_key($result,$items))];
    }
}
