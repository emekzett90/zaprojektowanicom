<?php
if (!defined('ABSPATH')) exit;
/** Scoped display dictionary; no writes to zpl_overrides or legacy page maps. */
final class ZPT2_Shared {
    const VERSION='shared-nav-20261013-v1';
    public static function dictionary(): array {return [
        'Wiedza'=>'Knowledge','Studio wyceny'=>'Pricing Studio','Studio Wyceny'=>'Pricing Studio','Przejdź do Studio Wyceny'=>'Go to Pricing Studio',
        'Start'=>'Home','Strona główna'=>'Home','Usługi'=>'Services','Realizacje'=>'Our work','O nas'=>'About us','Kontakt'=>'Contact','Menu główne'=>'Main menu',
        'Strony internetowe'=>'Websites','Sklepy internetowe'=>'Online stores','Strony & sklepy'=>'Websites & stores','Strony i sklepy'=>'Websites & stores','Strony WordPress'=>'WordPress websites',
        'Logo & branding'=>'Logo & branding','Logo i branding'=>'Logo & branding','Identyfikacja wizualna'=>'Visual identity','Projekt logo'=>'Logo design','Brandbook'=>'Brand book','Start marki'=>'Brand launch',
        'Kampanie reklamowe'=>'Advertising campaigns','Pozycjonowanie stron'=>'SEO','Wszystkie usługi'=>'All services','Zobacz wszystkie'=>'View all','Zobacz realizacje'=>'View our work',
        'Szukaj'=>'Search','Wyszukiwarka'=>'Search','Szukaj w Zaprojektowani'=>'Search Zaprojektowani','Zamknij wyszukiwarkę'=>'Close search','Otwórz menu'=>'Open menu','Zamknij menu'=>'Close menu',
        'Polityka prywatności'=>'Privacy policy','Polityka cookies'=>'Cookie policy','Najczęściej zadawane pytania'=>'Frequently asked questions','Dołącz do nas'=>'Join us','Porozmawiajmy'=>'Let’s talk'
    ];}
    public static function projection(string $source): array {
        $manual=(array)get_option('zpl_overrides',[]);$page=function_exists('zp_en_rewrite_approved_page')?zp_en_rewrite_approved_page($source):['map'=>[]];$rows=[];
        foreach(self::dictionary()as$pl=>$en){$en=isset($manual[$pl])&&is_string($manual[$pl])?$manual[$pl]:$en;$variants=[$pl,$en];if(is_string($page['map'][$pl]??null))$variants[]=$page['map'][$pl];if($pl==='Wiedza')$variants[]='Insights';$rows[]=['pl'=>$pl,'en'=>$en,'variants'=>array_values(array_unique($variants))];}
        return ['version'=>self::VERSION,'digest'=>ZPT2_Store::hash($rows),'rows'=>$rows];
    }
    public static function html(string $html,string $lang,string $source): string {
        if(!in_array($lang,['en','pl'],true))return$html;$p=self::projection($source);$lookup=[];foreach($p['rows']as$r)foreach($r['variants']as$v)$lookup[$v]=$lang==='en'?$r['en']:$r['pl'];
        $root=\ZPL\Html::parse($html);$ranges=[];$walk=null;$walk=static function($n)use(&$walk,&$ranges,$html){if(($n['t']??0)!==1)return;$a=\ZPL\Html::attrs(substr($html,$n['s'],$n['te']-$n['s']));$classes=explode(' ',$a['class']??'');if(($a['id']??'')==='zpNewNav'||in_array('zpMegaFooter',$classes,true)){$ranges[]=$n;return;}foreach($n['c']??[]as$c)$walk($c);};$walk($root);
        usort($ranges,static fn($a,$b)=>$b['s']<=>$a['s']);
        foreach($ranges as$n){$original=substr($html,$n['s'],$n['e']-$n['s']);$wrapped='<html><head></head><body>'.$original.'</body></html>';$rendered=\ZPL\Page::translate($wrapped,static fn($key,$kind)=>$lookup[$key]??null,static fn($url)=>$url,['lang'=>$lang]);$start=strpos($rendered,'<body>');$end=strrpos($rendered,'</body>');if($start===false||$end===false)continue;$fragment=substr($rendered,$start+6,$end-$start-6);$html=substr_replace($html,$fragment,$n['s'],$n['e']-$n['s']);}
        if(stripos($html,'</head>')!==false&&strpos($html,'id="zpt2-shared-dictionary"')===false){$json=wp_json_encode($p,JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_UNESCAPED_UNICODE);$html=preg_replace('~</head>~i','<script type="application/json" id="zpt2-shared-dictionary">'.$json.'</script></head>',$html,1);}
        return$html;
    }
    public static function script(): void {
        if(is_admin())return;
        echo '<script id="zpt2-shared-runtime">(function(){var el=document.getElementById("zpt2-shared-dictionary");if(!el)return;var p;try{p=JSON.parse(el.textContent)}catch(e){return}var pending=false;function norm(s){return s.replace(/\\s+/g," ").trim()}function apply(){pending=false;var lang=document.documentElement.lang.indexOf("en")===0?"en":"pl",map={};p.rows.forEach(function(r){r.variants.forEach(function(v){map[v]=r[lang]})});document.querySelectorAll("#zpNewNav,.zpMegaFooter").forEach(function(root){var w=document.createTreeWalker(root,NodeFilter.SHOW_TEXT);var n;while(n=w.nextNode()){if(n.parentElement&&n.parentElement.closest("script,style,code,pre,textarea"))continue;var k=norm(n.nodeValue);if(map[k]&&map[k]!==k)n.nodeValue=n.nodeValue.replace(k,map[k]);}root.querySelectorAll("[aria-label],[title],[placeholder]").forEach(function(n){["aria-label","title","placeholder"].forEach(function(a){var v=n.getAttribute(a);if(v&&map[norm(v)]&&map[norm(v)]!==v)n.setAttribute(a,map[norm(v)])})})})}function schedule(){if(!pending){pending=true;requestAnimationFrame(apply)}}document.addEventListener("zpl:change",schedule);document.querySelectorAll("#zpNewNav,.zpMegaFooter").forEach(function(n){new MutationObserver(schedule).observe(n,{childList:true,subtree:true,characterData:true,attributes:true,attributeFilter:["aria-label","title","placeholder"]})});apply()})();</script>';
    }
    public static function boot(): void {add_filter('zp_en_public_price_html',[self::class,'html'],999,3);add_action('wp_footer',[self::class,'script'],99);}
}
