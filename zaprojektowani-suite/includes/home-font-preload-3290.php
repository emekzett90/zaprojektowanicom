<?php
/** 3.2.90: preload the same inline mobile hero fonts before first use.
 * Interleave each preload and its unchanged face to keep gzip's32KB dictionary
 * effective. No font-display, text, geometry or face source changes.
 */
if (!defined('ABSPATH')) { exit; }
function zp_home_font_preload_3290_html(string $html): string {
    $path=wp_parse_url((string)($_SERVER['REQUEST_URI']??'/'),PHP_URL_PATH);
    if(!in_array($path,['/','/en/'],true)||!function_exists('zp_home_first_paint_enabled')||!zp_home_first_paint_enabled()
        ||strpos($html,'id="zp-home-first-paint-3233"')===false||strpos($html,'id="zhHero"')===false
        ||strpos($html,'data-zp-font-preload="3290"')!==false){return $html;}
    try {
        foreach(headers_list()as$header){if(stripos($header,'Content-Security-Policy:')===0){return $html;}}
        $file=ZP_SUITE_PATH.'assets/css/home-mobile-fonts-3233.css';$root=realpath(ZP_SUITE_PATH);$real=realpath($file);
        if(!$root||!$real||is_link($file)||!is_file($real)||!is_readable($real)||strpos($real,$root.DIRECTORY_SEPARATOR)!==0
            ||filesize($real)!==63713||!hash_equals('fd1a2672c3ee0408b13f76282d49db1966d1705a7433ea40b41a010bafa905d3',(string)hash_file('sha256',$real))){return $html;}
        $raw=file_get_contents($real);$group=trim((string)preg_replace('~\A/\*.*?\*/\s*~s','',$raw));
        if(!preg_match_all('~@font-face\{[^{}]+\}~',$group,$fm)||count($fm[0])!==5){return $html;}
        $faces=[];
        foreach($fm[0]as$face){if(!preg_match('~font-weight:(400|500|600|700|800);~',$face,$w)
            ||!preg_match('~src:url\("(data:font/woff2;base64,[A-Za-z0-9+/=]+)"\) format\("woff2"\)~',$face,$u)){return $html;}
            $faces[(int)$w[1]]=[$face,$u[1]];}
        if(array_keys($faces)!==[400,500,600,700,800]){return $html;}
        $scan=zp_speed_scan($html);if(!$scan||$scan['head_end']<0){return $html;}
        $attributes=static function(string $tag):?array{$out=[];
            if(preg_match_all('~\s+([^\s=/>"\']+)(?:\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|([^\s>"\']+)))?~',$tag,$ms,PREG_SET_ORDER)){
                foreach($ms as$m){$name=strtolower($m[1]);if(isset($out[$name])){return null;}$out[$name]=html_entity_decode(($m[2]??'').($m[3]??'').($m[4]??''),ENT_QUOTES|ENT_HTML5,'UTF-8');}}
            return $out;};
        $charsets=[];$viewports=[];
        if(preg_match_all('~<meta(?=[\s/>])~i',$html,$metas,PREG_OFFSET_CAPTURE)){
            foreach($metas[0]as[$token,$at]){if($at>=$scan['head_end']||zp_speed_in_skip($scan['skip'],$at)){continue;}
                $gt=zp_speed_tag_end($html,$at);if($gt<0){return $html;}$a=$attributes(substr($html,$at,$gt+1-$at));
                if($a===null||strtolower(trim($a['http-equiv']??''))==='content-security-policy'){return $html;}
                if(isset($a['charset'])){if(strtolower($a['charset'])!=='utf-8'){return $html;}$charsets[]=$gt+1;}
                if(strtolower($a['name']??'')==='viewport'){$viewports[]=$gt+1;}
            }
        }
        if(count($charsets)!==1||count($viewports)!==1){return $html;}$insert=max($charsets[0],$viewports[0]);
        $slots=[];$familyFaces=0;
        foreach($scan['styles']as[$start,$end]){
            $gt=zp_speed_tag_end($html,$start);if($gt<0){return $html;}$open=substr($html,$start,$gt+1-$start);
            $css=substr($html,$gt+1,$end-$gt-1);$close=strripos($css,'</style');if($close===false){return $html;}$css=substr($css,0,$close);
            if(preg_match_all('~@font-face\{[^{}]*\bZP Hero First Paint\b[^{}]*\}~',$css,$all)){$familyFaces+=count($all[0]);}
            if(strpos($css,$group)!==0){continue;}$a=$attributes($open);
            if($a===null||($a['data-zp-geometry']??'')!=='3233'||($a['data-zp-critical']??'')!=='3233'
                ||array_diff(array_keys($a),['data-zp-geometry','data-zp-critical'])||$start>=$scan['head_end']){return $html;}
            $slots[]=[$gt+1,$css];
        }
        if(count($slots)!==1||$familyFaces!==5){return $html;}
        [$cssStart,$css]=$slots[0];$ops=[];$preloads='';
        foreach([400,500,700,800]as$weight){[$face,$url]=$faces[$weight];
            if(substr_count($css,$face)!==1){return $html;}$at=$cssStart+strpos($css,$face);$ops[]=[$at,$at+strlen($face),''];
            $preloads.='<link rel="preload" as="font" type="font/woff2" crossorigin="anonymous" media="(max-width:1100px)" data-zp-font-preload="3290" href="'.$url.'">'
                .'<style media="(max-width:1100px)" data-zp-font-face="3290-'.$weight.'">'.$face.'</style>';
        }
        $ops[]=[$insert,$insert,$preloads];usort($ops,static function($a,$b){return$b[0]<=>$a[0];});
        foreach($ops as[$start,$end,$replacement]){$html=substr($html,0,$start).$replacement.substr($html,$end);}
        return $html;
    }catch(\Throwable $error){return $html;}
}
