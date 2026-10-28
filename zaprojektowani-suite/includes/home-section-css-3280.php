<?php
/**
 * 3.2.80: fetch reviewed below-screen component CSS as its real section approaches.
 *
 * Eight exact-content files, not a generic lazy-CSS rule. Each selector belongs to
 * the listed component family; home-refresh's ZP Review Signature face is used by
 * the below-screen review quote only. Header, hero, focus/global/footer styles,
 * trust-logo spacer, services-path, laptop and base portfolio CSS are excluded.
 * Existing SSR content, critical subsets, cascade positions and noscript links
 * are retained. No timers, audit detection, consent or cache-policy changes.
 */
if (!defined('ABSPATH')) { exit; }

function zp_home_section_css_3280_manifest(): array {
    return [
        ['id'=>'zp-suite-home-featured-packages-css','file'=>'assets/css/blocks/home-featured-packages.min.css','roots'=>['#zpHomeFeaturedPackages'],'bytes'=>26918,'sha256'=>'2977223257806536936d5147fb9912d4e5cfe412e3da761303016cfdd541a75a'],
        ['id'=>'zp-suite-showcase-services-css','file'=>'assets/css/blocks/showcase-services.css','roots'=>['#zpShowcaseServices'],'bytes'=>38129,'sha256'=>'44eb43ad8b4122f90a2b00f011e1af86e05e3c3c705c94496791f77b5e11910e'],
        ['id'=>'zp-service-coverage-css','file'=>'assets/css/service-coverage-20261015.min.css','roots'=>['#zasieg'],'bytes'=>10193,'sha256'=>'62e4178bcaff2edd5c01deedbb19535e9aa68b4f9bc6835420240a94f7de0fc9'],
        ['id'=>'zp-home-refine-3240-css','file'=>'assets/css/home-refine-3240.css','roots'=>['#zpShowcaseWhite','#seo-strony-internetowe-katowice','#zasieg'],'bytes'=>5665,'sha256'=>'a4e40f739f9d7bab1e6408c84a70c666db543d3575c0170a996ac5c23201f4db'],
        ['id'=>'','file'=>'assets/css/home-refresh-20261016.css','roots'=>['#zpRevEd','#zpHomeFaqKatNavy','.zpClient24'],'bytes'=>23085,'sha256'=>'7f65ba4b94df5e5e9d547a2f08fd64bee55c450a9f551c6cddb8c22e9260a5fe'],
        ['id'=>'zp-suite-footer-cta','file'=>'assets/css/snapshots-20261015/zp-suite-footer-cta.c65675aec68509eeb6ad.css','roots'=>['#zpMegaFooter'],'bytes'=>8517,'sha256'=>'c65675aec68509eeb6adb4088441210845a411536087d28ae889cbeeef4b167e'],
        ['id'=>'zp-suite-front-fixes-304-contact-card','file'=>'assets/css/front-fixes-304.css','roots'=>['#zpShowcaseServices','#zpPortfolioReveal'],'bytes'=>1342,'sha256'=>'827668357128dd07647e053bcf4dc38a56adec1952ba493c44b33b1e0611e36d'],
        ['id'=>'zp-suite-307-home-branding-popup-polish-v2782','file'=>'assets/css/snapshots-20261015/zp-suite-307-home-branding-popup-polish-v2782.1ee560dc2aaaf10b1bc9.css','roots'=>['#zpShowcaseWhite'],'bytes'=>8809,'sha256'=>'1ee560dc2aaaf10b1bc9f87cae42ed9177fc5525169a6dff875882a9032f6005'],
    ];
}

/** A changed file or link contract always retains the original delivery. */
function zp_home_section_css_3280_match(string $node, array $entry): bool {
    $a = zp_css_delivery_attributes($node);
    if (($a['id'] ?? '') !== $entry['id'] || strtolower(trim($a['rel'] ?? '')) !== 'stylesheet'
        || array_diff(array_keys($a), ['rel','id','href','media','onload','fetchpriority','data-zp-home-deferred','data-zp-home-media','data-zp-lower-page-css'])
        || ($a['data-zp-home-deferred'] ?? '') !== '3233'
        || !in_array($a['media'] ?? '', ['print','(min-width:1101px)'], true)
        || !in_array($a['data-zp-home-media'] ?? '', ['print','all'], true)
        || (isset($a['fetchpriority']) && $a['fetchpriority'] !== 'low')
        || (isset($a['data-zp-lower-page-css']) && $a['data-zp-lower-page-css'] !== '3240')) { return false; }
    $handlers = [
        'this.onload=null;this.media="all";',
        "this.media='all';this.onload=null",
        "(function(l){var apply=function(){var n=document.getElementById('zpNewNav');var busy=Date.now()<(window.__zpNavUiBusyUntil||0)||(n&&n.querySelector('#zpNewNavDrawer.is-open'));if(busy){setTimeout(apply,180);return;}l.media='all';l.onload=null;};apply();})(this)",
    ];
    if (!in_array($a['onload'] ?? '', $handlers, true)) { return false; }
    $given = wp_parse_url((string) ($a['href'] ?? ''));
    $expected = wp_parse_url(ZP_SUITE_URL . $entry['file']);
    if (!is_array($given) || !is_array($expected) || empty($given['host'])
        || isset($given['user']) || isset($given['pass']) || isset($given['fragment'])
        || strcasecmp($given['host'], (string) ($expected['host'] ?? '')) !== 0
        || ($given['scheme'] ?? '') !== ($expected['scheme'] ?? '')
        || ($given['port'] ?? null) !== ($expected['port'] ?? null)
        || ($given['path'] ?? '') !== ($expected['path'] ?? '')) { return false; }
    $root = realpath(ZP_SUITE_PATH); $path = ZP_SUITE_PATH . $entry['file']; $real = realpath($path);
    return $root && $real && !is_link($path) && is_file($real) && is_readable($real)
        && strpos($real, $root . DIRECTORY_SEPARATOR) === 0
        && filesize($real) === $entry['bytes']
        && hash_equals($entry['sha256'], (string) hash_file('sha256', $real));
}

function zp_home_section_css_3280_script(): string {
    return <<<'JS'
(function(w,d){'use strict';
if(w.__zpSectionCss3280)return;w.__zpSectionCss3280=true;
var entries=[],roots=[],observer=null;
function alive(e){return e.t&&e.t.parentNode;}
function load(e){
  if(e.state||!alive(e))return;
  e.state=1;
  try{
    var original=e.t.content&&e.t.content.firstElementChild;
    if(!original||original.tagName!=='LINK')throw Error('Missing original stylesheet');
    var link=original;e.link=link;
    link.addEventListener('load',function(){e.state=2;if(alive(e))e.t.parentNode.removeChild(e.t);},{once:true});
    link.addEventListener('error',releaseAll,{once:true});
    e.t.parentNode.insertBefore(link,e.t);
  }catch(error){releaseAll();}
}
function releaseAll(){
  for(var i=0;i<entries.length;i++){
    var e=entries[i];
    if(!alive(e))continue;
    try{
      if(!e.link||!e.link.parentNode){var original=e.link||(e.t.content&&e.t.content.firstElementChild);if(original)e.t.parentNode.insertBefore(original,e.t);}
      e.t.parentNode.removeChild(e.t);e.state=2;
    }catch(error){}
  }
  // Recover templates not reached yet if initialization failed partway through.
  try{var pending=d.querySelectorAll('template[data-zp-section-css="3280"]');for(var n=0;n<pending.length;n++){var p=pending[n],source=p.content&&p.content.firstElementChild;if(source&&p.parentNode)p.parentNode.replaceChild(source,p);}}catch(error){}
  if(observer){try{observer.disconnect();}catch(error){}observer=null;}
}
function loadRoot(root){for(var i=0;i<entries.length;i++){if(entries[i].roots.indexOf(root)!==-1)load(entries[i]);}}
function intent(event){
  try{
    var target=event.target&&event.target.closest?event.target:null;if(!target)return;
    var a=target.closest('a[href]');
    if(a){var url=new URL(a.href,w.location.href);if(url.origin===w.location.origin&&url.pathname===w.location.pathname&&url.hash){releaseAll();return;}}
    for(var i=0;i<roots.length;i++){if(roots[i]===target||roots[i].contains(target))loadRoot(roots[i]);}
  }catch(error){releaseAll();}
}
function restore(event){
  // Deep links and restored scroll positions need preceding section geometry
  // too, so restore every original stylesheet immediately on those visits.
  if(w.location.hash||(event&&event.persisted)||(w.scrollY||w.pageYOffset||0)>1600){releaseAll();return;}
  // Native IO supplies initial/resize geometry; never synchronously measure
  // section rectangles or force a layout from this loader.
  if(observer){try{for(var i=0;i<roots.length;i++){observer.unobserve(roots[i]);observer.observe(roots[i]);}}catch(error){releaseAll();}}
}
try{
  var templates=d.querySelectorAll('template[data-zp-section-css="3280"]');
  for(var i=0;i<templates.length;i++){
    var t=templates[i],found=Array.prototype.slice.call(d.querySelectorAll(t.getAttribute('data-zp-section-roots')||''));
    var e={t:t,roots:found,state:0,link:null};entries.push(e);
    if(!found.length){releaseAll();return;}
    for(var j=0;j<found.length;j++){if(roots.indexOf(found[j])===-1)roots.push(found[j]);}
  }
  if(!entries.length)return;
  if(!('IntersectionObserver' in w)){releaseAll();return;}
  observer=new w.IntersectionObserver(function(items){try{for(var i=0;i<items.length;i++){if(items[i].isIntersecting)loadRoot(items[i].target);}}catch(error){releaseAll();}},{rootMargin:'1600px 0px',threshold:0});
  for(var k=0;k<roots.length;k++)observer.observe(roots[k]);
  d.addEventListener('focusin',intent,true);d.addEventListener('pointerdown',intent,true);d.addEventListener('click',intent,true);
  w.addEventListener('hashchange',restore);w.addEventListener('pageshow',restore);
  restore();
}catch(error){
  releaseAll();
}
})(window,document);
JS;
}

/** Final home adapter: exact original LINKs remain inert at their cascade slots. */
function zp_home_section_css_3280_html(string $html): string {
    $path = wp_parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH);
    if (!in_array($path, ['/','/en/'], true) || !function_exists('zp_home_first_paint_enabled') || !zp_home_first_paint_enabled()
        || strpos($html, 'id="zp-home-first-paint-3233"') === false || strpos($html, 'id="zhHero"') === false
        || strpos($html, 'data-zp-section-css="3280"') !== false) { return $html; }
    try {
        // This helper deliberately does not guess how a site's CSP authorizes
        // newly printed inline code. Any enforced CSP keeps existing delivery.
        foreach (headers_list() as $header) { if (stripos($header, 'Content-Security-Policy:') === 0) { return $html; } }
        $scan = zp_speed_scan($html); if (!$scan || $scan['head_end'] < 0) { return $html; }
        if (preg_match_all('~<meta\b[^>]*>~i', $html, $metas, PREG_OFFSET_CAPTURE)) {
            foreach ($metas[0] as [$node,$at]) {
                if ($at >= $scan['head_end'] || zp_speed_in_skip($scan['skip'],$at)) { continue; }
                $attrs=zp_css_delivery_attributes($node);
                if (strtolower($attrs['http-equiv']??'')==='content-security-policy') { return $html; }
            }
        }
        $body_end=-1;$offset=0;
        while(preg_match('~</body\s*>~i',$html,$m,PREG_OFFSET_CAPTURE,$offset)){$at=$m[0][1];$offset=$at+strlen($m[0][0]);if(!zp_speed_in_skip($scan['skip'],$at)){$body_end=$at;break;}}
        if($body_end<0){return $html;}
        $manifest=zp_home_section_css_3280_manifest();$root_presence=[];$ops=[];
        foreach($scan['sheet_links'] as $index=>$range){
            [$start,$end]=$range;$node=substr($html,$start,$end-$start);
            foreach($manifest as $entry){
                if(!zp_home_section_css_3280_match($node,$entry)){continue;}
                $found=[];
                foreach($entry['roots'] as $selector){
                    if(!array_key_exists($selector,$root_presence)){$root_presence[$selector]=(bool)zp_speed_matching_tags($html,$scan,$selector,$scan['body_open'],strlen($html));}
                    if($root_presence[$selector]){$found[]=$selector;}
                }
                if(!$found){break;}
                // Preserve the existing JS-disabled experience. Only wrap a link
                // with a real following noscript stylesheet of the same URL.
                $limit=$scan['sheet_links'][$index+1][0]??strlen($html);$fallback=false;$href=zp_css_delivery_attributes($node)['href'];
                foreach($scan['skip'] as [$a,$b]){
                    if($a<$end){continue;}if($a>=$limit){break;}
                    if(strncasecmp(substr($html,$a,9),'<noscript',9)!==0){continue;}
                    $fragment=substr($html,$a,$b-$a);
                    if(preg_match_all('~<link\b[^>]*>~i',$fragment,$links)){
                        foreach($links[0] as $link){$attrs=zp_css_delivery_attributes($link);if(strtolower($attrs['rel']??'')==='stylesheet'&&($attrs['href']??'')===$href){$fallback=true;break;}}
                    }
                    if($fallback){break;}
                }
                if(!$fallback){break;}
                $ops[]=[$start,$end,'<template data-zp-section-css="3280" data-zp-section-roots="'.esc_attr(implode(',',$found)).'">'.$node.'</template>'];
                break;
            }
        }
        if(!$ops){return $html;}
        $ops[]=[$body_end,$body_end,'<script id="zp-home-section-css-3280" data-cfasync="false">'.zp_home_section_css_3280_script().'</script>'];
        usort($ops,static fn($a,$b)=>$a[0]<=>$b[0]);$out='';$last=0;
        foreach($ops as [$start,$end,$replacement]){if($start<$last){return $html;}$out.=substr($html,$last,$start-$last).$replacement;$last=$end;}
        return $out.substr($html,$last);
    } catch(\Throwable $error){return $html;}
}
