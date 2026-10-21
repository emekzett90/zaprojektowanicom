<?php
/**
 * 3.2.36: ship first-screen CSS first, not the entire historical style stack.
 * Compile-time, exact-hash snapshots; no network calls, live CSS parsing, extra
 * output buffer, cache policy changes, consent changes or UA-specific HTML.
 * Unknown/edited styles and missing assets retain their original delivery.
 */
if (!defined('ABSPATH')) { exit; }
function zp_first_screen_delivery_map_3236(): array {
    static $map;
    if ($map === null) {
        $file = ZP_SUITE_PATH . 'content/first-screen-3236.json';
        $map = is_readable($file) ? json_decode((string)file_get_contents($file), true) : [];
        if (!is_array($map)) { $map = []; }
    }
    return $map;
}
function zp_first_screen_delivery_gap_3236(string $gap): bool {
    $gap = preg_replace('~<!--.*?-->~s', '', $gap);
    $gap = preg_replace_callback('~<script\b[^>]*>.*?</script\s*>~is', static function ($m) {
        return preg_match('~createElement|insertAdjacent|document\.write|styleSheets|insertRule|\bsrc\s*=~i', $m[0]) ? $m[0] : '';
    }, $gap);
    return trim($gap) === '';
}
function zp_first_screen_delivery_asset_3236(array $entry): ?string {
    static $ok = [];
    $name = $entry['file'] ?? '';
    if (!is_string($name) || !preg_match('~^[sg]-[a-f0-9]{20}\.css$~D', $name)) { return null; }
    if (!array_key_exists($name, $ok)) {
        $file = ZP_SUITE_PATH . 'assets/css/first-screen-3236/' . $name;
        $ok[$name] = is_file($file) && !is_link($file) && is_readable($file)
            && filesize($file) === ($entry['bytes'] ?? -1)
            && hash_equals((string)($entry['sha256'] ?? ''), (string)hash_file('sha256', $file));
    }
    return $ok[$name] ? ZP_SUITE_URL . 'assets/css/first-screen-3236/' . $name : null;
}
function zp_first_screen_delivery_3236(string $html): string {
    if (!function_exists('zp_home_first_paint_enabled') || !zp_home_first_paint_enabled()
        || parse_url((string)($_SERVER['REQUEST_URI']??'/'), PHP_URL_PATH) !== '/'
        || get_option('zp_first_screen_delivery_off') === '1'
        || (isset($_GET['zp_first_screen']) && (string)$_GET['zp_first_screen'] === '0')
        || strpos($html, 'data-zp-first-screen-delivery="3236"') !== false) { return $html; }
    try {
        $map = zp_first_screen_delivery_map_3236();
        $scan = $map ? zp_speed_scan($html) : null;
        if (!$scan || strpos($html, 'id="zhHero"') === false || strpos($html, 'id="zpNewNav"') === false) { return $html; }
        $nodes = []; $scripts = '';
        foreach ($scan['skip'] as [$a,$b]) { if (strncasecmp(substr($html,$a,7),'<script',7)===0) { $scripts .= substr($html,$a,$b-$a); } }
        foreach ($scan['styles'] as [$start,$end]) {
            if ($start >= $scan['head_end']) { continue; }
            $node = substr($html,$start,$end-$start); $gt = zp_speed_tag_end($node,0);
            if ($gt < 0) { continue; }
            $open = substr($node,0,$gt+1); $attrs = zp_css_delivery_attributes($open);
            $css = substr($node,$gt+1,strripos($node,'</style')-$gt-1); $hash=hash('sha256',$css);
            $entry=$map['styles'][$hash]??null;
            if (!$entry || array_diff(array_keys($attrs),['id','type'])
                || (isset($attrs['id']) && strpos($scripts,$attrs['id'])!==false)
                || (isset($attrs['type']) && strtolower($attrs['type'])!=='text/css')) { continue; }
            $nodes[]=['start'=>$start,'end'=>$end,'open'=>$open,'hash'=>$hash,'entry'=>$entry,'css'=>$css];
        }
        $ops=[]; $count=0; $saved=0;
        for ($i=0;$i<count($nodes);$i++) {
            $first=$nodes[$i]; $group=[$first]; $entry=$first['entry'];
            foreach ($map['groups'][$first['hash']]??[] as $candidate) {
                $matches=[];
                foreach($candidate['hashes'] as $j=>$hash) {
                    $n=$nodes[$i+$j]??null;
                    if (!$n || $n['hash']!==$hash || ($j>0 && !zp_first_screen_delivery_gap_3236(substr($html,$matches[$j-1]['end'],$n['start']-$matches[$j-1]['end'])))) { $matches=[];break; }
                    $matches[]=$n;
                }
                if (count($matches)>count($group)) { $group=$matches;$entry=$candidate; }
            }
            $rawBytes=array_sum(array_map(static fn($n)=>strlen($n['css']),$group));
            $critical=(string)($entry['critical']??'');
            if ((count($group)===1 && $rawBytes<16000) || $rawBytes-strlen($critical)<3000 || strpos($critical,'</style')!==false) { continue; }
            $url=zp_first_screen_delivery_asset_3236($entry); if(!$url) {continue;}
            $safe=esc_url($url);
            // Normal blocking delivery above 1100px. On phones only the reviewed
            // first-screen subset is blocking; full CSS keeps its cascade position.
            $link='<link rel="stylesheet" href="'.$safe.'" media="(min-width:1101px)" fetchpriority="low" data-zp-full-css-3236 onload="this.media=\'all\'">';
            $fallback='<noscript><link rel="stylesheet" href="'.$safe.'" media="(max-width:1100px)"></noscript>';
            foreach ($group as $j=>$n) {
                // Unnamed STYLE nodes remain style nodes; named ones retain their ID.
                $replacement=$n['open'].($j===0?'@media(max-width:1100px){'.$critical.'}':'').'</style>';
                if($j===count($group)-1) { $replacement.=$link.$fallback; }
                $ops[]=[$n['start'],$n['end'],$replacement];
            }
            $count++;$saved+=$rawBytes-strlen($critical);$i+=count($group)-1;
        }
        if (!$count) { return $html; }
        $loader='<script defer data-zp-first-screen-delivery="3236" data-zp-css-files="'.$count.'" data-zp-css-initial-saved="'.$saved.'" src="'.esc_url(ZP_SUITE_URL.'assets/js/home-css-delivery-3236.js').'" data-cfasync="false"></script>';
        $ops[]=[$scan['head_end'],$scan['head_end'],$loader];
        return zp_header_ssr_edits_3235($html,$ops);
    } catch (Throwable $error) { return $html; }
}
// This release invalidates Suite's own copies once; it never changes consent
// keys or enables HTML caching for authenticated or cookie-bearing requests.
add_action('admin_init',static function (): void {
    if (!current_user_can('manage_options') || get_option('zp_release_3236_cache')==='done') { return; }
    do_action('zp_html_cache_purge');update_option('zp_release_3236_cache','done',false);
},170);
