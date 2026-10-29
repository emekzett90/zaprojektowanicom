<?php
/** 3.2.85: tiny compositor-only mobile motion around two exact SSR hero assets. */
if (!defined('ABSPATH')) { exit; }

function zp_home_mobile_float_3285_html(string $html): string {
    $path=wp_parse_url((string)($_SERVER['REQUEST_URI']??'/'),PHP_URL_PATH);
    if(!in_array($path,['/','/en/'],true)||!function_exists('zp_home_first_paint_enabled')||!zp_home_first_paint_enabled()
        ||strpos($html,'id="zhHero"')===false||strpos($html,'id="zp-mobile-float-3285"')!==false){return $html;}
    try {
        foreach(headers_list() as $header){if(stripos($header,'Content-Security-Policy:')===0){return $html;}}
        $scan=zp_speed_scan($html);if(!$scan){return $html;}
        if(preg_match_all('~<meta(?=[\s/>])~i',$html,$metas,PREG_OFFSET_CAPTURE)){
            foreach($metas[0] as [$token,$at]){if($at>=$scan['head_end']||zp_speed_in_skip($scan['skip'],$at)){continue;}
                $gt=zp_speed_tag_end($html,$at);if($gt<0){return $html;}$a=zp_css_delivery_attributes(substr($html,$at,$gt+1-$at));
                if(strtolower($a['http-equiv']??'')==='content-security-policy'){return $html;}}
        }
        $nodes=[];
        foreach([
            'crew'=>['~<picture\b[^>]*\bclass="zh__crewMobilePicture"[^>]*>~','picture','dd16dc0c67ce4f7671f6b9eb7d466a20f9e25bc1fb5d32cf98e341e4de553082'],
            'robot'=>['~<div\b[^>]*\bid="zhPoster"[^>]*>~','div','de5fc967452e7bfb77aa2999685ed365f677765ec968189ec6fc9dd11341b470'],
        ] as $key=>[$pattern,$tag,$hash]){
            $matches=[];
            if(preg_match_all($pattern,$html,$all,PREG_OFFSET_CAPTURE)){
                foreach($all[0] as [$open,$at]){if(!zp_speed_in_skip($scan['skip'],$at)){$matches[]=[$open,$at];}}
            }
            if(count($matches)!==1){return $html;}[$open,$at]=$matches[0];
            $end=zp_speed_nested_end($html,$tag,$at+strlen($open));if($end<0){return $html;}
            $node=substr($html,$at,$end-$at);if(!hash_equals($hash,hash('sha256',$node))){return $html;}
            $nodes[$key]=[$at,$end,$node];
        }
        // The picture is a flex child of the existing crew; the poster remains
        // within its original absolute stage. Future structural edits fall back.
        $before=substr($html,max(0,$nodes['crew'][0]-220),min(220,$nodes['crew'][0]));
        if(!preg_match('~<div\b[^>]*\bclass="zh__crew"[^>]*>\s*$~',$before)){return $html;}
        if(!preg_match('~<div\b[^>]*\bid="zhStage"[^>]*>~',$html,$stage,PREG_OFFSET_CAPTURE)){return $html;}
        $stageAt=$stage[0][1];$stageEnd=zp_speed_nested_end($html,'div',$stageAt+strlen($stage[0][0]));
        if($stageEnd<0||$nodes['robot'][0]<=$stageAt||$nodes['robot'][1]>=$stageEnd){return $html;}
        $css='<style id="zp-mobile-float-3285">html body #zhHero .zpMobileFloat3285{display:contents}@media(max-width:1100px){html body #zhHero:not(#zpx) .zpMobileFloat3285--crew{display:block;width:225.333333px;height:122px;flex:0 0 auto}html body #zhHero:not(#zpx) .zpMobileFloat3285--robot{display:block;position:absolute;inset:0;z-index:1;pointer-events:none}}@media(max-width:1100px) and (prefers-reduced-motion:no-preference){html body #zhHero:not(#zpx) .zpMobileFloat3285--crew{animation:zpMobileCrewFloat3285 7.8s ease-in-out 2s infinite!important;animation-play-state:paused!important}html body #zhHero:not(#zpx) .zpMobileFloat3285--robot{animation:zpMobileRobotFloat3285 8.8s ease-in-out 2s infinite!important;animation-play-state:paused!important}html body #zhHero[data-zp-mobile-float="1"]:not(#zpx) .zpMobileFloat3285{animation-play-state:running!important}}@keyframes zpMobileCrewFloat3285{0%,100%{transform:translateY(0)}50%{transform:translateY(-3px)}}@keyframes zpMobileRobotFloat3285{0%,100%{transform:translateY(0)}50%{transform:translateY(-4px)}}</style>';
        $js=<<<'JS'
<script id="zp-mobile-float-3285-js">(function(w,d){var h=d.getElementById('zhHero'),io,near=false,m;if(!h||!w.matchMedia||!w.IntersectionObserver)return;function sync(){h.setAttribute('data-zp-mobile-float',near&&!d.hidden&&m.matches?'1':'0')}try{m=w.matchMedia('(max-width:1100px) and (prefers-reduced-motion:no-preference)');io=new IntersectionObserver(function(es){for(var i=0;i<es.length;i++)if(es[i].target===h)near=es[i].isIntersecting;sync()});io.observe(h);d.addEventListener('visibilitychange',sync);if(m.addEventListener)m.addEventListener('change',sync);else if(m.addListener)m.addListener(sync);w.addEventListener('pagehide',function(){near=false;sync()});w.addEventListener('pageshow',function(){io.unobserve(h);io.observe(h)});}catch(e){h.removeAttribute('data-zp-mobile-float');if(io)io.disconnect()}})(window,document);</script>
JS;
        $ops=[[$scan['head_end'],$scan['head_end'],$css]];
        foreach($nodes as $key=>[$at,$end,$node]){$tag=$key==='robot'?'div':'span';$ops[]=[$at,$end,'<'.$tag.' class="zpMobileFloat3285 zpMobileFloat3285--'.$key.'">'.$node.'</'.$tag.'>'];}
        $bodyClose=strripos($html,'</body>');if($bodyClose===false){return $html;}$ops[]=[$bodyClose,$bodyClose,$js];
        usort($ops,static function($a,$b){return $b[0]<=>$a[0];});
        foreach($ops as [$at,$end,$replacement]){$html=substr($html,0,$at).$replacement.substr($html,$end);}
        return $html;
    }catch(\Throwable $error){return $html;}
}
