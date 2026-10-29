<?php
/** 3.2.85: keep home contact handlers live; measure distant mode panels on approach. */
if (!defined('ABSPATH')) { exit; }

function zp_home_contact_geometry_3285_runtime(): string {
    return <<<'JS'
      // zp-contact-geometry-3285: CSS supplies natural height until geometry is needed.
      var zpGeometryReady=false,zpGeometryObserver=null,zpGeometryListening=false;
      function zpGeometryStop(){
        if(zpGeometryObserver){try{zpGeometryObserver.disconnect();}catch(error){}zpGeometryObserver=null;}
        if(!zpGeometryListening)return;zpGeometryListening=false;
        ['focusin','pointerdown','click','change','submit'].forEach(function(name){root.removeEventListener(name,zpGeometryInteraction,true);});
        document.removeEventListener('click',zpGeometryAnchor,true);
        window.removeEventListener('hashchange',zpGeometryHash);
        window.removeEventListener('pageshow',zpGeometryPageShow);
      }
      function zpGeometryAllow(measure){
        if(zpGeometryReady)return;zpGeometryReady=true;zpGeometryStop();
        if(measure!==false)updateHeights();
      }
      function zpGeometryTarget(hash){
        if(!hash||hash==='#')return false;
        try{var target=document.getElementById(decodeURIComponent(hash.slice(1)));return !!(target&&(target===root||root.contains(target)||target.contains(root)));}catch(error){return false;}
      }
      function zpGeometryInteraction(){zpGeometryAllow(true);}
      function zpGeometryHash(){if(zpGeometryTarget(window.location.hash))zpGeometryAllow(true);}
      function zpGeometryPageShow(e){if(e.persisted)zpGeometryAllow(true);}
      function zpGeometryAnchor(e){
        var a=e.target&&e.target.closest?e.target.closest('a[href]'):null;if(!a)return;
        try{var u=new URL(a.getAttribute('href'),window.location.href);if(u.origin===window.location.origin&&u.pathname===window.location.pathname&&zpGeometryTarget(u.hash))zpGeometryAllow(true);}catch(error){}
      }
      if('IntersectionObserver' in window){
        try{
          zpGeometryObserver=new window.IntersectionObserver(function(entries){
            entries.forEach(function(entry){if(entry.target===root&&entry.isIntersecting)zpGeometryAllow(true);});
          },{rootMargin:'1600px 0px',threshold:0});
          zpGeometryObserver.observe(root);
        }catch(error){zpGeometryAllow(false);}
      }else{zpGeometryReady=true;}
      if(!zpGeometryReady){
        zpGeometryListening=true;
        ['focusin','pointerdown','click','change','submit'].forEach(function(name){root.addEventListener(name,zpGeometryInteraction,true);});
        document.addEventListener('click',zpGeometryAnchor,true);
        window.addEventListener('hashchange',zpGeometryHash);
        window.addEventListener('pageshow',zpGeometryPageShow);
        if(zpGeometryTarget(window.location.hash))zpGeometryAllow(false);
      }

JS;
}

function zp_home_contact_geometry_3285_html(string $html): string {
    if (!function_exists('zp_home_first_paint_enabled') || !zp_home_first_paint_enabled()
        || !in_array(wp_parse_url((string)($_SERVER['REQUEST_URI']??'/'),PHP_URL_PATH),['/','/en/'],true)
        || strpos($html,'id="zhHero"')===false || strpos($html,'id="zp-home-first-paint-3233"')===false
        || strpos($html,'zp-contact-geometry-3285:')!==false) { return $html; }
    try {
        foreach (['zp_speed_scan','zp_speed_tag_end','zp_speed_in_skip','zp_css_delivery_attributes'] as $fn) { if (!function_exists($fn)) { return $html; } }
        foreach (headers_list() as $header) { if (stripos($header,'Content-Security-Policy:')===0) { return $html; } }
        $scan=zp_speed_scan($html); if (!$scan) { return $html; }
        if (preg_match_all('~<meta\b~i',$html,$metas,PREG_OFFSET_CAPTURE)) {
            foreach ($metas[0] as [$token,$at]) {
                if ($at>=$scan['head_end'] || zp_speed_in_skip($scan['skip'],$at)) { continue; }
                $gt=zp_speed_tag_end($html,$at); if ($gt<0) { return $html; }
                $meta=substr($html,$at,$gt+1-$at);
                if (preg_match_all('~\bhttp-equiv\s*=~i',$meta)>1) { return $html; }
                $a=zp_css_delivery_attributes($meta);
                if (strtolower(trim($a['http-equiv']??''))==='content-security-policy') { return $html; }
            }
        }
        $found=null;
        foreach ($scan['skip'] as [$start,$end]) {
            if (strncasecmp(substr($html,$start,7),'<script',7)!==0) { continue; }
            $gt=zp_speed_tag_end($html,$start); if ($gt<0) { return $html; }
            $contents=substr($html,$gt+1,$end-$gt-1); $close=strripos($contents,'</script'); if ($close===false) { return $html; }
            $body=substr($contents,0,$close);
            if (strpos($body,'function initZpContactFormLight()')===false) { continue; }
            if ($found!==null || !preg_match('~^<script\s*>$~i',substr($html,$start,$gt+1-$start))
                || strlen($body)!==15633 || !hash_equals('644fe9f2b9afd6bc1843b84f285f47d510b4640e1d82386d247b3c8892b82e29',hash('sha256',$body))) { return $html; }
            $found=[$gt+1,$gt+1+$close,$body];
        }
        if ($found===null) { return $html; }
        [$start,$end,$body]=$found;
        $needle="      function updateHeights(){\n";
        $guard="        if(!zpGeometryReady)return;\n";
        $reads="        heights.forEach(function(item){\n          item.height=item.show ? item.block.scrollHeight + 42 + 'px' : '0px';\n";
        if (substr_count($body,$needle)!==1 || substr_count($body,$reads)!==1) { return $html; }
        $body=str_replace($needle,zp_home_contact_geometry_3285_runtime().$needle,$body);
        // Visibility classes still update immediately; only layout reads/writes wait.
        $body=str_replace($reads,$guard.$reads,$body);
        return substr($html,0,$start).$body.substr($html,$end);
    } catch (\Throwable $error) { return $html; }
}
