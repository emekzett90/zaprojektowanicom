<?php
/**
 * 3.2.88: preserve Hello's DOMContentLoaded initialization with native defer.
 * The reviewed Hello3.4.7 frontend is vanilla and has no jQuery dependency.
 * Only the exact home script is eligible; CSS, script bytes/order and all
 * other tags remain untouched. Unknown delivery or CSP retains the original.
 */
if (!defined('ABSPATH')) { exit; }

function zp_home_theme_defer_3288_html(string $html): string {
    $path=wp_parse_url((string)($_SERVER['REQUEST_URI']??'/'),PHP_URL_PATH);
    if(!in_array($path,['/','/en/'],true)||!function_exists('zp_home_first_paint_enabled')||!zp_home_first_paint_enabled()
        ||strpos($html,'id="zhHero"')===false||strpos($html,'id="zp-home-first-paint-3233"')===false){return $html;}
    try {
        foreach(headers_list() as $header){if(stripos($header,'Content-Security-Policy:')===0){return $html;}}
        $scan=zp_speed_scan($html);if(!$scan||$scan['head_end']<0){return $html;}
        // Do not consume a following attribute after a boolean one; reject
        // duplicate names rather than guessing the browser's effective value.
        $attributes=static function(string $tag): ?array {
            $out=[];
            if(preg_match_all('~\s+([^\s=/>"\']+)(?:\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|([^\s>"\']+)))?~',$tag,$matches,PREG_SET_ORDER)){
                foreach($matches as $m){$name=strtolower($m[1]);if(array_key_exists($name,$out)){return null;}
                    $out[$name]=html_entity_decode(($m[2]??'').($m[3]??'').($m[4]??''),ENT_QUOTES|ENT_HTML5,'UTF-8');}
            }
            return $out;
        };
        if(preg_match_all('~<meta(?=[\s/>])~i',$html,$metas,PREG_OFFSET_CAPTURE)){
            foreach($metas[0] as [$token,$at]){
                if($at>=$scan['head_end']||zp_speed_in_skip($scan['skip'],$at)){continue;}
                $gt=zp_speed_tag_end($html,$at);if($gt<0){return $html;}
                $a=$attributes(substr($html,$at,$gt+1-$at));
                if($a===null||strtolower(trim($a['http-equiv']??''))==='content-security-policy'){return $html;}
            }
        }
        $src=rtrim(site_url('/'),'/').'/wp-content/themes/hello-elementor/assets/js/hello-frontend.js?ver=3.4.7';
        $matches=[];
        foreach($scan['skip'] as [$start,$end]){
            if(strncasecmp(substr($html,$start,7),'<script',7)!==0){continue;}
            $gt=zp_speed_tag_end($html,$start);if($gt<0){return $html;}
            $open=substr($html,$start,$gt+1-$start);$a=$attributes($open);if($a===null){return $html;}
            if(($a['id']??'')!=='hello-theme-frontend-js'&&($a['src']??'')!==$src){continue;}
            if(($a['id']??'')!=='hello-theme-frontend-js'||($a['src']??'')!==$src
                ||array_diff(array_keys($a),['id','src','type','defer'])
                ||!in_array(strtolower(trim($a['type']??'')),['','text/javascript','application/javascript'],true)){
                return $html;
            }
            if(substr(rtrim($open),-2)==='/>' ){return $html;}
            $tail=substr($html,$gt+1,$end-$gt-1);$close=strripos($tail,'</script');
            if($close===false||trim(substr($tail,0,$close))!==''){return $html;}
            $matches[]=[$start,$gt,$open,array_key_exists('defer',$a)];
        }
        if(count($matches)!==1||$matches[0][3]){return $html;}
        [$start,$gt,$open]=$matches[0];
        return substr($html,0,$start).substr($open,0,-1).' defer>'.substr($html,$gt+1);
    }catch(\Throwable $error){return $html;}
}
