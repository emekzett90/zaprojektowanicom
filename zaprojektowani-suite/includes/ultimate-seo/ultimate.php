<?php
if (!defined('ABSPATH')) { exit; }
final class ZPSSEO_Editorial {
    public static function init() {
        add_filter('the_content',[__CLASS__,'related'],30);
        add_filter('rank_math/json_ld',[__CLASS__,'schema'],99);
        foreach (['rank_math/frontend/title','wpseo_title','pre_get_document_title'] as $hook) { add_filter($hook,[__CLASS__,'title'],999); }
        foreach (['rank_math/frontend/description','wpseo_metadesc'] as $hook) { add_filter($hook,[__CLASS__,'description'],999); }
    }
    public static function enabled() { return ZPSSEO_Repair::allowed() && ZPSSEO_Repair::state()['enabled']; }
    public static function editorial() { static $data; if ($data===null) { $data=json_decode(file_get_contents(__DIR__.'/editorial.json'),true); } return $data; }
    public static function services() {
        return [
            '/strony-internetowe-katowice/'=>['h1'=>'Strony internetowe Katowice — projektowanie i tworzenie stron WWW','name'=>'Strony internetowe','intro'=>'Projekt strony firmowej łączy treść, wygląd i wygodę korzystania. Zaprojektowani przygotowują strony WordPress, strukturę informacji, UX/UI oraz wdrożenie. Punktem wyjścia są Twoja oferta i działania, które klient ma wykonać: poznać usługę, obejrzeć realizację lub wysłać zapytanie.','detail'=>'Przed wyceną warto określić liczbę podstron, potrzebne formularze, wersje językowe i integracje. Materiały o firmie, zdjęcia oraz opis usług pomagają dopasować strukturę do odbiorców. Strona powinna jasno przedstawiać zakres współpracy i prowadzić do kontaktu także na telefonie.'],
            '/sklepy-internetowe-katowice/'=>['h1'=>'Sklepy internetowe Katowice — projekt i wdrożenie WooCommerce','name'=>'Sklepy WooCommerce','intro'=>'Sklep internetowy powinien ułatwiać znalezienie produktu i przejście przez zakup. Zaprojektowani zajmują się projektowaniem oraz wdrażaniem sklepów WooCommerce. Plan obejmuje strukturę kategorii, karty produktów, koszyk, płatności i dostawy, zgodnie z potrzebami konkretnego sklepu.','detail'=>'Do rozmowy o projekcie przygotuj liczbę produktów, warianty, docelowych klientów i listę niezbędnych integracji. Znaczenie ma zarówno wygląd, jak i sposób prezentowania ceny, dostępności oraz warunków dostawy. Zakres sklepu i jego koszt zależą od tych decyzji, a nie wyłącznie od liczby podstron.'],
            '/logo-branding-katowice/'=>['h1'=>'Logo i branding Katowice — identyfikacja wizualna firmy','name'=>'Logo i branding','intro'=>'Logo, kolory i typografia pomagają budować spójny wizerunek firmy. Zaprojektowani projektują logo i identyfikację wizualną z myślą o stronie internetowej, materiałach drukowanych oraz komunikacji marki. Zakres prac warto dopasować do miejsc, w których znak będzie używany.','detail'=>'Przygotowanie briefu pozwala opisać odbiorców, ofertę, charakter marki i potrzebne materiały. Wycena może obejmować sam znak lub szerszy system wizualny. Przed rozpoczęciem współpracy warto ustalić liczbę koncepcji, zakres poprawek, formaty plików oraz zasady używania identyfikacji.'],
            '/kampanie-reklamowe/'=>['h1'=>'Kampanie reklamowe — Meta Ads i Google Ads dla firm','name'=>'Kampanie reklamowe','intro'=>'Kampania reklamowa potrzebuje spójnej oferty, kreacji i strony docelowej. Zaprojektowani łączą działania reklamowe z projektowaniem stron oraz brandingiem. Przed uruchomieniem reklamy warto określić cel, budżet testu i sposób pomiaru zapytań.','detail'=>'Kliknięcie reklamy nie jest jeszcze pozyskanym klientem. Strona docelowa powinna rozwijać obietnicę reklamy i ułatwiać kontakt. Przy planowaniu kosztów oddziel budżet mediowy od przygotowania kreacji, obsługi kampanii oraz ewentualnych prac nad landing page.']
        ];
    }
    public static function rule($path = null) {
        // 2.3.0: titles, descriptions and H1s live in Rank Math and the post title (keyword plan).
        if (function_exists('zp_seo_plan_active') && zp_seo_plan_active()) { return null; }
        if (!self::enabled() || is_admin()) { return null; }
        $path=$path ?? ZPSSEO_Repair::path(wp_unslash($_SERVER['REQUEST_URI'] ?? ''));
        if (is_404() || !is_singular() || is_preview() || post_password_required()) { return null; }
        $id=get_queried_object_id();
        if (!$id || ZPSSEO_Repair::path(get_permalink($id))!==$path) { return null; }
        foreach (self::editorial() as $r) { if ($r['path']===$path) {
            $backup=(array)get_option('zpsseo_meta_backup',[]);
            foreach(['title'=>'rank_math_title','description'=>'rank_math_description'] as $field=>$key){
                $slot=$id.':'.$key;
                if(isset($backup[$slot])){$current=get_post_meta($id,$key,true);if(is_string($current)&&$current!==''&&$current!==$backup[$slot]['written']){$r[$field]=$current;}}
            }
            return $r;
        } }
        if ($path==='/') { return ['path'=>'/','h1'=>'Strony internetowe Katowice, sklepy WooCommerce i branding','title'=>'Strony internetowe Katowice, sklepy i branding | Zaprojektowani','description'=>'Projektujemy strony internetowe w Katowicach, sklepy WooCommerce oraz logo i branding. Poznaj realizacje Zaprojektowani i porozmawiajmy o Twoim projekcie.','links'=>[]]; }
        $services=self::services();
        if (isset($services[$path])) { $r=$services[$path];return ['path'=>$path,'h1'=>$r['h1'],'title'=>['/strony-internetowe-katowice/'=>'Strony internetowe Katowice | Zaprojektowani','/sklepy-internetowe-katowice/'=>'Sklepy internetowe Katowice — WooCommerce | Zaprojektowani','/logo-branding-katowice/'=>'Logo i branding Katowice | Zaprojektowani','/kampanie-reklamowe/'=>'Kampanie Meta Ads i Google Ads | Zaprojektowani'][$path],'description'=>['/strony-internetowe-katowice/'=>'Strony internetowe dla firm: WordPress, projekt UX/UI, treści i wdrożenie. Poznaj ofertę Zaprojektowani z Katowic i opisz zakres swojego projektu.','/sklepy-internetowe-katowice/'=>'Projekt i wdrożenie sklepu WooCommerce: kategorie, produkty, płatności i dostawy. Poznaj ofertę Zaprojektowani i porozmawiajmy o Twoim sklepie.','/logo-branding-katowice/'=>'Projektowanie logo i identyfikacji wizualnej dla firm. Poznaj zakres brandingu, proces współpracy oraz realizacje studia Zaprojektowani z Katowic.','/kampanie-reklamowe/'=>'Kampanie Meta Ads i Google Ads: oferta, kreacje, strona docelowa i pomiar zapytań. Poznaj ofertę Zaprojektowani i zaplanuj działania reklamowe.'][$path],'links'=>[]]; }
        if ($path==='/kontakt/') { return ['path'=>$path,'h1'=>'Kontakt z Zaprojektowani — opowiedz o swoim projekcie','title'=>'Kontakt | Zaprojektowani — strony, sklepy i branding','description'=>'Skontaktuj się z Zaprojektowani w sprawie strony internetowej, sklepu WooCommerce lub brandingu. Opisz potrzeby i zakres swojego projektu.','links'=>[]]; }
        return null;
    }
    public static function title($value) { $r=self::rule();return $r ? $r['title'] : $value; }
    public static function description($value) { $r=self::rule();return $r ? $r['description'] : $value; }
    public static function schema($data) {
        $rule=self::rule();if(!$rule || !is_array($data)){return $data;}
        foreach($data as &$node){
            if(!is_array($node)){continue;}$types=(array)($node['@type']??[]);
            if(array_intersect($types,['Article','BlogPosting','NewsArticle'])){$node['headline']=$rule['h1'];}
        }unset($node);return $data;
    }
    public static function link($path,$label) {
        if (!ZPSSEO_Repair::public_page($path)) { return ''; }
        return '<a href="'.esc_url(home_url($path)).'">'.esc_html($label).'</a>';
    }
    public static function related($content) {
        if (!self::enabled() || is_admin() || !is_main_query() || !in_the_loop() || !is_singular('post') || strpos($content,'zpu-related')!==false) { return $content; }
        $r=self::rule(); if (!$r || empty($r['links'])) { return $content; }
        // 2.7.2: when the keyword-plan module is present, reuse its image-led cards so the
        // legacy repair mode never falls back to the old unstyled bullet list.
        if (function_exists('zp_seo_related_render')) {
            $related = zp_seo_related_render((array) $r['links'], (string) ($r['path'] ?? ''));
            return $related !== '' ? $content . $related : $content;
        }
        $items='';foreach($r['links'] as $l){$link=self::link($l['path'],$l['text']);if($link){$items.='<li>'.$link.'</li>';}}
        return $items ? $content.'<section class="zpu-related" aria-label="Powiązane materiały"><h2>Powiązane usługi i poradniki</h2><ul>'.$items.'</ul></section>' : $content;
    }
    public static function metadata_job($path) {
        $r=null;foreach(self::editorial() as $entry){if($entry['path']===$path){$r=$entry;break;}}
        if (!$r || !ZPSSEO_Repair::public_page($path)) { return 'PLAN ZACHOWANY: pod podanym adresem nie ma opublikowanego wpisu. Brief nie został opublikowany jako artykuł.'; }
        $id=url_to_postid(home_url($path));
        $values=['rank_math_title'=>$r['title'],'rank_math_description'=>$r['description'],'rank_math_focus_keyword'=>$r['focus']];
        if (defined('WPSEO_VERSION')) { $values+=['_yoast_wpseo_title'=>$r['title'],'_yoast_wpseo_metadesc'=>$r['description'],'_yoast_wpseo_focuskw'=>$r['focus']]; }
        $backup=(array)get_option('zpsseo_meta_backup',[]);
        foreach($values as $key=>$value){
            if (!self::enabled()) { self::restore_metadata(); return 'WSTRZYMANO: wtyczka została wyłączona.'; }
            $slot=$id.':'.$key;
            if (isset($backup[$slot])) { continue; } // Respect later manual edits, keep first snapshot.
            $backup[$slot]=['id'=>$id,'key'=>$key,'existed'=>metadata_exists('post',$id,$key),'old'=>get_post_meta($id,$key,true),'written'=>$value];
            update_option('zpsseo_meta_backup',$backup,false);
            update_post_meta($id,$key,$value);
        }
        return 'WDROŻONO: tytuł SEO, opis i fraza z briefu; H1 w publicznym HTML; linki wyłącznie do opublikowanych celów. URL i treść wpisu pozostają zachowane.';
    }
    public static function normalize($html, $rule = null) {
        $pattern='~<!--[\s\S]*?-->|<(script|style|textarea|title|noscript)\b(?:"[^"]*"|\x27[^\x27]*\x27|[^\x27">])*?>[\s\S]*?</\1\s*>|<(h[1-6])\b(?:"[^"]*"|\x27[^\x27]*\x27|[^\x27">])*?>[\s\S]*?</\2\s*>|<(?:"[^"]*"|\x27[^\x27]*\x27|[^\x27">])+>~i';
        if (!preg_match_all($pattern,$html,$found,PREG_OFFSET_CAPTURE)) { return $html; }
        $tokens=$found[0];$stack=[];$head=false;$headings=[];$main=-1;$edits=[];$seenTitle=false;$seenDescription=false;$leadStart=null;$leadEdits=[];
        foreach($tokens as $i=>$item){
            $tag=$item[0];if(strpos($tag,'<!--')===0){continue;}
            if(preg_match('~^</([a-z0-9-]+)~i',$tag,$close)){
                $name=strtoupper($close[1]);if($name==='HEAD'){$head=false;}
                if($name==='P' && $leadStart!==null && $rule){
                    $start=$tokens[$leadStart][1]+strlen($tokens[$leadStart][0]);$length=$item[1]-$start;
                    if($length>=0 && strpos(substr($html,$start,$length),'<')===false){$leadEdits[]=['start'=>$start,'length'=>$length,'text'=>esc_html($rule['description'])];}
                    $leadStart=null;
                }
                for($k=count($stack)-1;$k>=0;$k--){if($stack[$k]['name']===$name){$stack=array_slice($stack,0,$k);break;}}
                continue;
            }
            $p=new WP_HTML_Tag_Processor($tag);if(!$p->next_tag()){continue;}$name=$p->get_tag();
            if($name==='HEAD'){$head=true;}
            if($name==='P' && $rule && strpos((string)$p->get_attribute('class'),'zpSinglePostHero__lead')!==false){$leadStart=$i;}
            $hidden=false;$excluded=false;
            foreach($stack as $entry){$hidden=$hidden||$entry['hidden'];$excluded=$excluded||$entry['excluded'];}
            $style=(string)$p->get_attribute('style');
            $ownHidden=$p->get_attribute('hidden')!==null || strtolower((string)$p->get_attribute('aria-hidden'))==='true' || preg_match('/display\s*:\s*none|visibility\s*:\s*hidden/i',$style);
            $inContent=false;foreach($stack as $ancestor){if(in_array($ancestor['name'],['ARTICLE','MAIN'],true)){$inContent=true;}}
            $ownExcluded=(($name==='HEADER' && !$inContent)||in_array($name,['FOOTER','NAV','ASIDE','FORM','DIALOG','TEMPLATE','NOSCRIPT'],true)) || $p->get_attribute('role')==='dialog';
            if(!$excluded&&!$hidden&&!$ownHidden && ($name==='MAIN' || $name==='ARTICLE' || in_array($p->get_attribute('data-elementor-type'),['wp-page','wp-post'],true)) && $main<0){$main=$i;}
            if($name==='TITLE' && $head && $rule){
                $edits[$i]=$seenTitle ? '' : '<title>'.esc_html($rule['title']).'</title>';$seenTitle=true;continue;
            }
            if($name==='META' && $head && $rule){
                $n=strtolower((string)$p->get_attribute('name'));$prop=strtolower((string)$p->get_attribute('property'));
                if($n==='description'){
                    if($seenDescription){$edits[$i]='';continue;}$seenDescription=true;$p->set_attribute('content',$rule['description']);
                } elseif(in_array($prop,['og:title','twitter:title'],true)||$n==='twitter:title'){$p->set_attribute('content',$rule['title']);}
                elseif(in_array($prop,['og:description','twitter:description'],true)||$n==='twitter:description'){$p->set_attribute('content',$rule['description']);}
                $edits[$i]=$p->get_updated_html();
            }
            if(preg_match('/^H([1-6])$/',$name,$h) && !$head && !$excluded && !$hidden && !$ownHidden && !$ownExcluded){$headings[]=['index'=>$i,'level'=>(int)$h[1]];continue;}
            if($name==='IMG' && $p->get_attribute('alt')===null){
                $alt=null;
                if($hidden||$ownHidden||strpos((string)$p->get_attribute('class'),'zp-exit-v42__')!==false){$alt='';}
                else{
                    $src=$p->get_attribute('src');
                    if(is_string($src)&&ZPSSEO_Repair::path($src)){
                        $id=attachment_url_to_postid($src);$value=$id ? get_post_meta($id,'_wp_attachment_image_alt',true) : '';
                        if(is_string($value)&&trim($value)!==''){$alt=$value;}
                    }
                }
                if($alt!==null){$p->set_attribute('alt',$alt);$edits[$i]=$p->get_updated_html();}
            }
            // Raw-text elements and headings above are complete tokens; do not put them on the stack.
            if(in_array($name,['SCRIPT','STYLE','TEXTAREA','TITLE','NOSCRIPT'],true)||preg_match('/^H[1-6]$/',$name)){continue;}
            if(!in_array($name,['AREA','BASE','BR','COL','EMBED','HR','IMG','INPUT','LINK','META','PARAM','SOURCE','TRACK','WBR'],true)&&!preg_match('~/\s*>$~',$tag)){
                $stack[]=['name'=>$name,'hidden'=>(bool)$ownHidden,'excluded'=>(bool)$ownExcluded];
            }
        }
        $primary=null;foreach($headings as $h){if($h['level']===1){$primary=$h['index'];break;}}
        if($primary===null && $headings && $main>=0){$primary=$headings[0]['index'];}
        $previous=1;
        foreach($headings as $h){
            $i=$h['index'];$original=$tokens[$i][0];$level=$i===$primary ? 1 : max(2,min($h['level'],$previous+1));$previous=$level;
            $new=preg_replace('~^(<)h[1-6](\b)~i','$1h'.$level.'$2',$original);
            $new=preg_replace('~</h[1-6]\s*>$~i','</h'.$level.'>',$new);
            if($rule && $i===$primary && trim(html_entity_decode(wp_strip_all_tags($new), ENT_QUOTES, 'UTF-8'))!==$rule['h1']){$open=new WP_HTML_Tag_Processor($new);$open->next_tag();
                // Retain the opening tag's IDs/classes; only replace the heading's text.
                if(preg_match('~^<h1\b(?:"[^"]*"|\x27[^\x27]*\x27|[^\x27">])*?>~i',$new,$m)){$new=$m[0].esc_html($rule['h1']).'</h1>';}
            }
            if($new!==$original){$edits[$i]=$new;}
        }
        if($primary===null && $rule && $main>=0){$edits[$main]=($edits[$main]??$tokens[$main][0]).'<h1 class="zpu-generated-heading">'.esc_html($rule['h1']).'</h1>';}
        // Add a missing description only inside a real head, never to fragments or JSON.
        if($rule && !$seenDescription){foreach($tokens as $i=>$item){if(preg_match('~^</head\s*>$~i',$item[0])){$edits[$i]='<meta name="description" content="'.esc_attr($rule['description']).'">'.$item[0];break;}}}
        $changes=$leadEdits;
        foreach($edits as $i=>$replacement){$changes[]=['start'=>$tokens[$i][1],'length'=>strlen($tokens[$i][0]),'text'=>$replacement];}
        usort($changes,function($a,$b){return $b['start']<=>$a['start'];});
        foreach($changes as $change){$html=substr_replace($html,$change['text'],$change['start'],$change['length']);}
        return $html;
    }
    public static function output($html) {
        if(!self::enabled() || is_admin() || is_404() || !is_singular() || is_preview() || post_password_required() || stripos($html,'<html')===false){return $html;}
        $path=ZPSSEO_Repair::path(wp_unslash($_SERVER['REQUEST_URI']??''));
        if(preg_match('~^/(oferty|briefy|prace|dziekujemy|rodo|cookies|regulamin|polityka-prywatnosci)(/|-)~',$path)){return $html;}
        return self::normalize($html,self::rule());
    }
    public static function verify($path) {
        $response=wp_safe_remote_get(home_url($path),['timeout'=>12,'redirection'=>2,'limit_response_size'=>2*1024*1024]);
        if(is_wp_error($response)){return 'DO SPRAWDZENIA: kontrola HTTP — '.$response->get_error_message();}
        $code=wp_remote_retrieve_response_code($response);$html=wp_remote_retrieve_body($response);
        $visible=preg_replace('~<(script|style|noscript|template)\b[^>]*>[\s\S]*?</\1>~i','',$html);
        preg_match_all('/<h1\b/i',$visible,$h1);preg_match_all('/<a\b[^>]*\bhref\s*=/i',$visible,$links);
        $shortcodes=preg_match('/\[zp_(header|footer|home_full|strony_internetowe_katowice|sklepy_internetowe_katowice|logo_branding_katowice|contact_page)\]/',$visible);
        $ok=$code===200 && count($h1[0])===1 && !$shortcodes;
        return ($ok?'KONTROLA OK':'DO SPRAWDZENIA').': HTTP '.$code.', H1: '.count($h1[0]).', odnośniki: '.count($links[0]).', nierozwinięte shortcode: '.($shortcodes?'tak':'nie').'.'.($ok?'':' Możliwy cache/CDN lub błąd modułu strony.');
    }
    public static function restore_metadata() {
        $backup=(array)get_option('zpsseo_meta_backup',[]);
        foreach($backup as $entry){
            if (get_post_meta($entry['id'],$entry['key'],true)!==$entry['written']) { continue; }
            if ($entry['existed']) { update_post_meta($entry['id'],$entry['key'],$entry['old']); }
            else { delete_post_meta($entry['id'],$entry['key']); }
        }
        delete_option('zpsseo_meta_backup');
    }
}
ZPSSEO_Editorial::init();
