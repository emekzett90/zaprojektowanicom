<?php
/**
 * 3.2.82: the audited home jQuery chain executes in native defer order.
 * Moving the exact inline UI compatibility flag to a static file keeps its
 * position before UI core while releasing the parser from jQuery core's fetch.
 * Any unreviewed dependency, source, attributes or CSP keeps original delivery.
 */
if (!defined('ABSPATH')) { exit; }

function zp_home_jquery_defer_3282_html(string $html): string {
    $path=wp_parse_url((string)($_SERVER['REQUEST_URI']??'/'),PHP_URL_PATH);
    if(!in_array($path,['/','/en/'],true)||!function_exists('zp_home_first_paint_enabled')||!zp_home_first_paint_enabled()
        ||strpos($html,'id="zhHero"')===false||strpos($html,'id="zp-home-first-paint-3233"')===false){return $html;}
    try {
        foreach(headers_list() as $header){if(stripos($header,'Content-Security-Policy:')===0){return $html;}}
        $file='assets/js/jquery-ui-backcompat-3282.7396ff75aa389aad.js';
        $hash='7396ff75aa389aadaf1be546f86be2f6b64f79b5bd7f0e3d682c4e19d246f60e';
        $root=realpath(ZP_SUITE_PATH);$candidate=ZP_SUITE_PATH.$file;$real=realpath($candidate);
        if(!$root||!$real||is_link($candidate)||!is_file($real)||!is_readable($real)||strpos($real,$root.DIRECTORY_SEPARATOR)!==0
            ||filesize($real)!==68||!hash_equals($hash,(string)hash_file('sha256',$real))){return $html;}
        $scan=zp_speed_scan($html);if(!$scan){return $html;}
        $attributes=static function(string $tag): ?array {
            $out=[];
            if(preg_match_all('~\\s+([^\\s=/>\"\\\']+)(?:\\s*=\\s*(?:\"([^\"]*)\"|\\\'([^\\\']*)\\\'|([^\\s>\"\\\']+)))?~',$tag,$matches,PREG_SET_ORDER)){
                foreach($matches as $m){$key=strtolower($m[1]);if(array_key_exists($key,$out)){return null;}
                    $out[$key]=html_entity_decode(($m[2]??'').($m[3]??'').($m[4]??''),ENT_QUOTES|ENT_HTML5,'UTF-8');}
            }
            return $out;
        };
        if(preg_match_all('~<meta(?=[\s/>])~i',$html,$metas,PREG_OFFSET_CAPTURE)){
            foreach($metas[0] as [$token,$at]){if($at>=$scan['head_end']||zp_speed_in_skip($scan['skip'],$at)){continue;}
                $gt=zp_speed_tag_end($html,$at);if($gt<0){return $html;}$node=substr($html,$at,$gt+1-$at);
                $a=$attributes($node);if($a===null){return $html;}if(strtolower($a['http-equiv']??'')==='content-security-policy'){return $html;}}
        }
        $site=rtrim(site_url('/'),'/');$plugin=rtrim(ZP_SUITE_URL,'/');
        // All currently active external scripts were audited for synchronous
        // jQuery use. New plugin/dependency IDs or paths fail closed as a unit.
        $external=[
            'Cookiebot'=>'~^https://consent\\.cookiebot\\.com/uc\\.js$~',
            'jquery-core-js'=>'~^'.preg_quote($site.'/wp-includes/js/jquery/jquery.min.js?ver=3.7.1','~').'$~',
            'jquery-ui-core-js'=>'~^'.preg_quote($site.'/wp-includes/js/jquery/ui/core.min.js?ver=1.14.2','~').'$~',
            'zpl-switch-js'=>'~^'.preg_quote($plugin.'/zaprojektowani-languages/assets/zpl-switch.min.js?ver=1.1.3-suite-','~').'3\\.2\\.\\d+$~',
            'zpl-core-js'=>'~^'.preg_quote($plugin.'/zaprojektowani-languages/assets/zpl-core.min.js?ver=1.1.3','~').'$~',
            'zpl-js'=>'~^'.preg_quote($plugin.'/zaprojektowani-languages/assets/zpl.min.js?ver=1.1.3','~').'$~',
            'hello-theme-frontend-js'=>'~^'.preg_quote($site.'/wp-content/themes/hello-elementor/assets/js/hello-frontend.js?ver=3.4.7','~').'$~',
            'zp-suite-global-js'=>'~^'.preg_quote($site.'/wp-content/uploads/zpl-cache/js/assets-js-zp-global-js-','~').'[0-9a-f]{10}\\.js\\?ver=\\d+$~',
            'zp-suite-header-js'=>'~^'.preg_quote($site.'/wp-content/uploads/zpl-cache/js/assets-js-blocks-header-js-','~').'[0-9a-f]{10}\\.js\\?ver=\\d+$~',
            'wp-hooks-js'=>'~^'.preg_quote($site.'/wp-includes/js/dist/hooks.min.js?ver=f0f188028580e8dc1255','~').'$~',
            'wp-i18n-js'=>'~^'.preg_quote($site.'/wp-includes/js/dist/i18n.min.js?ver=1dfe7db3940c23ea9216','~').'$~',
        ];
        foreach([
            'zp-suite-lucide-js'=>'vendor/lucide.min.js',
            'zp-suite-trust-logos-js'=>'blocks/trust-logos.js',
            'zp-suite-services-path-js'=>'blocks/services-path.js',
            'zp-suite-home-featured-packages-js'=>'blocks/home-featured-packages.js',
            'zp-suite-front-polish-180-js'=>'zp-front-polish-180.js',
            'zp-suite-meta-verified-js'=>'blocks/meta-verified.js',
        ] as $id=>$asset){$external[$id]='~^'.preg_quote($plugin.'/assets/js/'.$asset.'?ver=','~').'\\d+$~';}
        foreach([
            'zp-home-service-mobile-3236-js'=>'home-service-mobile-3236.js',
            'zp-service-coverage-js'=>'service-coverage-20261015.js',
        ] as $id=>$asset){$external[$id]='~^'.preg_quote($plugin.'/assets/js/'.$asset.'?ver=3.2.','~').'\\d+$~';}
        $external['zp-case-studies-20261015-js']='~^'.preg_quote($plugin.'/assets/js/case-studies-20261015.js?ver=3.2.11','~').'$~';
        $anonymous=[
            '~^'.preg_quote($plugin.'/assets/js/home-css-delivery-3236.js','~').'$~',
            '~^'.preg_quote($plugin.'/assets/js/home-css-delivery-3236.min.js','~').'$~',
            '~^'.preg_quote($plugin.'/assets/js/home-reviews-20261016.js?ver=3.2.','~').'\\d+$~',
            '~^'.preg_quote($plugin.'/assets/js/home-reviews-20261016.min.js?ver=3.2.','~').'\\d+$~',
            '~^'.preg_quote($plugin.'/includes/../assets/en-public-currency-20261011.js?ver=usd_nbp_human_v2.20261014','~').'$~',
        ];
        $found=[];
        foreach($scan['skip'] as [$start,$end]){
            if(strncasecmp(substr($html,$start,7),'<script',7)!==0){continue;}
            $gt=zp_speed_tag_end($html,$start);if($gt<0){return $html;}
            $open=substr($html,$start,$gt+1-$start);$a=$attributes($open);if($a===null){return $html;}
            $type=strtolower(trim($a['type']??''));
            if(in_array($type,['text/plain','application/ld+json','application/json','speculationrules'],true)){continue;}
            if(!in_array($type,['','text/javascript','application/javascript'],true)){return $html;}
            $contents=substr($html,$gt+1,$end-$gt-1);$close=strripos($contents,'</script');if($close===false){return $html;}
            $body=substr($contents,0,$close);$id=$a['id']??'';
            if(isset($a['src'])){
                $valid=false;
                if($id!==''){$valid=isset($external[$id])&&preg_match($external[$id],$a['src'])===1;}
                else{foreach($anonymous as $pattern){if(preg_match($pattern,$a['src'])===1){$valid=true;break;}}}
                if(!$valid){return $html;}
                // ZPL regenerates only these two audited cache filenames when
                // a release changes asset URLs. Both must remain ordered defer.
                if(in_array($id,['zp-suite-global-js','zp-suite-header-js'],true)&&!array_key_exists('defer',$a)){return $html;}
            }elseif($id!=='jquery-ui-core-js-before'&&preg_match('~\bjQuery\b|(?<![\w$])\$\s*(?:\(|\.)~',$body)){return $html;}
            if(!in_array($id,['jquery-core-js','jquery-ui-core-js-before','jquery-ui-core-js'],true)){continue;}
            if(isset($found[$id])||array_diff(array_keys($a),['id','src','type'])||trim($a['type']??'text/javascript')!=='text/javascript'){return $html;}
            if($id==='jquery-ui-core-js-before'){
                if(isset($a['src'])||strlen($body)!==68||!hash_equals($hash,hash('sha256',$body))){return $html;}
            }elseif(!isset($a['src'])||trim($body)!==''){return $html;}
            $found[$id]=[$start,$end,$open,$contents,$close];
        }
        if(count($found)!==3){return $html;}
        $core=$found['jquery-core-js'];$bridge=$found['jquery-ui-core-js-before'];$ui=$found['jquery-ui-core-js'];
        if(!($core[0]<$bridge[0]&&$bridge[0]<$ui[0])){return $html;}
        $edits=[];
        foreach([$core,$ui] as $node){[$start,$end,$open,$contents]=$node;$edits[]=[$start,$end,substr($open,0,-1).' defer>'.$contents];}
        [$start,$end,$open,$contents,$close]=$bridge;
        $edits[]=[$start,$end,substr($open,0,-1).' src="'.esc_attr(ZP_SUITE_URL.$file).'" defer>'.substr($contents,$close)];
        usort($edits,static function($a,$b){return $b[0]<=>$a[0];});
        foreach($edits as [$start,$end,$replacement]){$html=substr($html,0,$start).$replacement.substr($html,$end);}
        return $html;
    }catch(\Throwable $error){return $html;}
}
