<?php
/**
 * 3.2.84: deliver nine audited, complete home stylesheets in their existing slots.
 * No selectors, declarations, fonts, order or media conditions are changed.
 * All files must match their frozen bytes; any uncertain contract keeps the
 * original document. Files are read locally on HTML-cache misses, never by HTTP.
 */
if (!defined('ABSPATH')) { exit; }

function zp_home_critical_vendor_css_3284_manifest(): array {
    return [
        'hello-elementor-css'=>['/wp-content/themes/hello-elementor/assets/css/reset.css',5456,'2f01a854a759c0a6a6911634e4ae5283b86a054908e4587c1fd17561fe4ce3cf'],
        'hello-elementor-theme-style-css'=>['/wp-content/themes/hello-elementor/assets/css/theme.css',5097,'9a7cfe03ec7638180859c5cd93ed18a75c0b4519c01849e2b6b2e2c90a0dea2f'],
        'hello-elementor-header-footer-css'=>['/wp-content/themes/hello-elementor/assets/css/header-footer.css',7182,'5052c28c33de995612b9c064753ee989d1e7755027e2b80292148728f444ec8d'],
        'elementor-frontend-css'=>['/wp-content/plugins/elementor/assets/css/frontend.min.css',54666,'0a5d398672e25dc7f020f69df92a985481b248a60e301fee232d0c8afd648dc5'],
        'elementor-post-6-css'=>['/wp-content/uploads/elementor/css/post-6.css',1433,'6f90335eeee006c686c4a51188aeee8fc1e0832d1e50f450b0d72ec260dbd6b0'],
        'elementor-post-665-css'=>['/wp-content/uploads/elementor/css/post-665.css',1038,'d8b47cef805eeca51b5b6160b6e36cae4c59449ae4543c98ff1c1b130dd845be'],
        'base-desktop-css'=>['/wp-content/uploads/elementor/css/base-desktop.css',3111,'b80cae96fc5bbf6cbfe8d258d72377ddd29ca7d2b8665ed456805ec639906de2'],
        'elementor-post-28-css'=>['/wp-content/uploads/elementor/css/post-28.css',803,'1dbe3c04d62a9488fd66b3ceea807f11f9f16340516357e830076eb66a646d59'],
        'elementor-post-442-css'=>['/wp-content/uploads/elementor/css/post-442.css',1021,'9e50b478014e62077983d38bcb4cf3de6a4f06ac70925f1f9d0382b3b1d3b6a4'],
    ];
}

/** Parse complete quoted attributes; duplicates or unparsed text fail closed. */
function zp_home_critical_vendor_css_3284_attributes(string $tag): ?array {
    if (!preg_match('~^<[a-z][a-z0-9:-]*\b~i', $tag, $m)) { return null; }
    $pos=strlen($m[0]); $len=strlen($tag); $out=[];
    while ($pos<$len) {
        if (preg_match('~\G\s*/?>\s*$~A', $tag, $m, 0, $pos)) { return $out; }
        if (!preg_match('~\G\s+([^\s=/>"\']+)(?:\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|([^\s>"\'=<>`]+)))?~A', $tag, $m, 0, $pos)) { return null; }
        $key=strtolower($m[1]); if (array_key_exists($key,$out)) { return null; }
        $out[$key]=html_entity_decode(($m[2]??'').($m[3]??'').($m[4]??''),ENT_QUOTES|ENT_HTML5,'UTF-8');
        $pos+=strlen($m[0]);
    }
    return null;
}

function zp_home_critical_vendor_css_3284_html(string $html): string {
    if (!function_exists('zp_home_first_paint_enabled') || !zp_home_first_paint_enabled()
        || !in_array(wp_parse_url((string)($_SERVER['REQUEST_URI']??'/'),PHP_URL_PATH),['/','/en/'],true)
        || strpos($html,'id="zhHero"')===false || strpos($html,'id="zp-home-first-paint-3233"')===false
        || strpos($html,'data-zp-critical-vendor-css="3284"')!==false) { return $html; }
    try {
        foreach (['zp_speed_scan','zp_speed_tag_end','zp_speed_in_skip','zp_speed_local_file'] as $fn) { if (!function_exists($fn)) { return $html; } }
        foreach (headers_list() as $header) { if (stripos($header,'Content-Security-Policy:')===0) { return $html; } }
        $scan=zp_speed_scan($html); if (!$scan) { return $html; }
        // Use the quote-aware tag scanner: a CSP value may contain a literal >.
        if (preg_match_all('~<meta\b~i',$html,$metas,PREG_OFFSET_CAPTURE)) {
            foreach ($metas[0] as [$token,$at]) {
                if ($at>=$scan['head_end'] || zp_speed_in_skip($scan['skip'],$at)) { continue; }
                $gt=zp_speed_tag_end($html,$at); if ($gt<0) { return $html; }
                $a=zp_home_critical_vendor_css_3284_attributes(substr($html,$at,$gt+1-$at));
                if ($a===null || strtolower(trim($a['http-equiv']??''))==='content-security-policy') { return $html; }
            }
        }
        $origin=wp_parse_url(site_url('/')); if (!is_array($origin) || empty($origin['host']) || empty($origin['scheme'])) { return $html; }
        $manifest=zp_home_critical_vendor_css_3284_manifest(); $found=[]; $edits=[];
        foreach ($scan['sheet_links'] as [$start,$end]) {
            $node=substr($html,$start,$end-$start); $a=zp_home_critical_vendor_css_3284_attributes($node);
            if ($a===null) { return $html; }
            $id=$a['id']??''; if (!isset($manifest[$id])) { continue; }
            if ($start>=$scan['head_end'] || isset($found[$id])
                || array_diff(array_keys($a),['id','rel','href','media','type'])
                || strtolower(trim($a['rel']??''))!=='stylesheet'
                || (isset($a['type']) && strtolower(trim($a['type']))!=='text/css')) { return $html; }
            $u=wp_parse_url($a['href']??''); [$expected,$bytes,$hash]=$manifest[$id];
            if (!is_array($u) || isset($u['user']) || isset($u['pass']) || isset($u['fragment'])
                || strcasecmp($u['host']??'',$origin['host'])!==0 || ($u['scheme']??'')!==$origin['scheme']
                || ($u['port']??null)!==($origin['port']??null) || ($u['path']??'')!==$expected
                || (isset($u['query']) && !preg_match('~^ver=[A-Za-z0-9._-]+$~D',$u['query']))) { return $html; }
            $path=zp_speed_local_file($a['href']); $real=$path?realpath($path):false;
            if (!$real || is_link($path) || !is_file($real) || !is_readable($real) || filesize($real)!==$bytes) { return $html; }
            $css=file_get_contents($real);
            if (!is_string($css) || strlen($css)!==$bytes || !hash_equals($hash,hash('sha256',$css))
                || preg_match('~@import\b|url\s*\(|</style~i',$css)) { return $html; }
            $style='<style id="'.esc_attr($id).'" data-zp-critical-vendor-css="3284"';
            foreach (['media','type'] as $key) { if (isset($a[$key])) { $style.=' '.$key.'="'.esc_attr($a[$key]).'"'; } }
            $edits[]=[$start,$end,$style.'>'.$css.'</style>']; $found[$id]=true;
        }
        if (count($found)!==count($manifest)) { return $html; }
        usort($edits,static function($a,$b){return $b[0]<=>$a[0];});
        foreach ($edits as [$start,$end,$replacement]) { $html=substr($html,0,$start).$replacement.substr($html,$end); }
        return $html;
    } catch (\Throwable $error) { return $html; }
}
