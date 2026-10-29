<?php
/**
 * 3.2.81: the exact inert desktop portfolio controller lives in a static asset.
 * The existing near-section loader already supports data-zp-src. Execution time,
 * SSR content, mobile rail, consent and styles do not change. An edited controller,
 * missing/changed package asset or different translated body stays inline.
 */
if (!defined('ABSPATH')) { exit; }

function zp_home_portfolio_payload_3281_html(string $html): string {
    $path=wp_parse_url((string)($_SERVER['REQUEST_URI']??'/'),PHP_URL_PATH);
    if (!in_array($path,['/','/en/'],true) || !function_exists('zp_home_first_paint_enabled') || !zp_home_first_paint_enabled()
        || strpos($html,'id="zhHero"')===false || strpos($html,'id="zp-home-first-paint-3233"')===false) { return $html; }
    try {
        foreach(headers_list() as $header){if(stripos($header,'Content-Security-Policy:')===0){return $html;}}
        $file='assets/js/home-portfolio-core-3281.04e9943ed122d642.js';
        $hash='04e9943ed122d64292746f1499a449ac05fb36fef4769a3be2a24ad39042fc53';
        $root=realpath(ZP_SUITE_PATH);$candidate=ZP_SUITE_PATH.$file;$real=realpath($candidate);
        if(!$root||!$real||is_link($candidate)||!is_file($real)||!is_readable($real)
            ||strpos($real,$root.DIRECTORY_SEPARATOR)!==0||filesize($real)!==111558
            ||!hash_equals($hash,(string)hash_file('sha256',$real))){return $html;}
        $scan=zp_speed_scan($html);if(!$scan){return $html;}$matches=[];
        if(preg_match_all('~<meta\b[^>]*>~i',$html,$metas,PREG_OFFSET_CAPTURE)){
            foreach($metas[0] as [$node,$at]){
                if($at>=$scan['head_end']||zp_speed_in_skip($scan['skip'],$at)){continue;}
                $attrs=zp_css_delivery_attributes($node);
                if(strtolower($attrs['http-equiv']??'')==='content-security-policy'){return $html;}
            }
        }
        foreach($scan['skip'] as [$start,$end]){
            if(strncasecmp(substr($html,$start,7),'<script',7)!==0){continue;}
            $gt=zp_speed_tag_end($html,$start);if($gt<0){return $html;}
            $open=substr($html,$start,$gt+1-$start);$a=zp_css_delivery_attributes($open);
            if(($a['id']??'')!=='zp-home-portfolio-core-js'){continue;}
            $matches[]=[$start,$end,$gt,$open,$a];
        }
        if(count($matches)!==1){return $html;}
        [$start,$end,$gt,$open,$a]=$matches[0];
        if(($a['type']??'')!=='text/plain'||($a['data-zp-delay-home-portfolio']??'')!=='1'
            ||array_diff(array_keys($a),['id','type','data-zp-delay-home-portfolio'])){return $html;}
        $contents=substr($html,$gt+1,$end-$gt-1);$close=strripos($contents,'</script');
        if($close===false){return $html;}$body=substr($contents,0,$close);
        // PL and EN do not currently have identical controller bodies. Only the
        // reviewed exact bytes use this PL asset; translated/edited text falls back.
        if(strlen($body)!==111558||!hash_equals($hash,hash('sha256',$body))){return $html;}
        $replacement=substr($open,0,-1).' data-zp-src="'.esc_attr(ZP_SUITE_URL.$file).'">'.substr($contents,$close);
        return substr($html,0,$start).$replacement.substr($html,$end);
    }catch(\Throwable $error){return $html;}
}
