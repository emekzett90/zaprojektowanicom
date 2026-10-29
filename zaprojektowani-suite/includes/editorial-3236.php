<?php
/** New portfolio illustrations + contextual offers inside the nine existing guides. */
if (!defined('ABSPATH')) { exit; }
function zp_editorial_data_3236(): array {
    static $data; if ($data === null) { $data=json_decode((string)file_get_contents(ZP_SUITE_PATH.'content/editorial-3236.json'),true); if(!is_array($data))$data=[]; } return $data;
}
function zp_editorial_block_3236(array $entry, int $index): string {
    $c=$entry['cases'][$index]; $path='assets/img/wpisy/'.basename($c['image']);
    $file=ZP_SUITE_PATH.$path; if(!is_file($file))return '';
    $size=getimagesize($file); if(!$size)return '';
    $id='zp-editorial-3236-'.($index+1);
    $h='<aside class="zpEditorialCase" data-zp-editorial="3236" aria-labelledby="'.$id.'"><p class="zpEditorialCase__kicker">Przykład z naszego portfolio</p><h3 id="'.$id.'">'.esc_html($c['title']).'</h3>';
    $h.='<figure><a href="'.esc_url(home_url($c['link'])).'" aria-label="'.esc_attr('Zobacz więcej: '.$c['alt']).'"><img src="'.esc_url(ZP_SUITE_URL.$path).'" alt="'.esc_attr($c['alt']).'" width="'.(int)$size[0].'" height="'.(int)$size[1].'" loading="lazy" decoding="async"></a><figcaption>'.esc_html($c['alt']).' · Zaprojektowani.com</figcaption></figure><p>'.esc_html($c['text']).'</p>';
    if($index===1){
        $h.='<div class="zpEditorialCase__check"><strong>Do przełożenia na własny projekt</strong><dl>';
        foreach($entry['table']as[$label,$text,$note]){$h.='<div><dt>'.esc_html($label).'</dt><dd>'.esc_html($text).'<small>'.esc_html($note).'</small></dd></div>';}
        $h.='</dl></div>';
    }
    $h.='<div class="zpEditorialOffer"><div><strong>'.($index===0?'Potrzebujesz podobnego rozwiązania?':'Przełóżmy te wskazówki na Twoją markę.').'</strong><p>Ustalimy zakres, materiały i kolejne kroki — bez dobierania funkcji na zapas.</p></div><a href="'.esc_url(home_url($index===0?$entry['service']:'/studio-wyceny/')).'">'.esc_html($entry['button']).'<span aria-hidden="true">↗</span></a></div></aside>';
    return $h;
}
function zp_editorial_transform_3236(string $html,string $path): string {
    $d=zp_editorial_data_3236()[$path]??null;
    if(!$d||strpos($html,'data-zp-editorial="3236"')!==false)return $html;
    $outer=zp_article_growth_20261017_range($html,'article','class','zpArticleNew');if(!$outer)return $html;
    $article=substr($html,$outer[0],$outer[1]-$outer[0]);
    $body=zp_article_growth_20261017_range($article,'main','class','zpArticleNew__main')??zp_article_growth_20261017_range($article,'div','class','zpArticleNew__main');if(!$body)return $html;
    $inner=substr($article,$body[2],$body[3]-$body[2]);$points=[];$offset=0;
    // Walk complete sibling sections; never insert into a table, form or FAQ answer.
    while(preg_match('~<section\b[^>]*>~i',$inner,$m,PREG_OFFSET_CAPTURE,$offset)){
        $start=$m[0][1];$open=$start+strlen($m[0][0]);$end=zp_speed_nested_end($inner,'section',$open);if($end<0)break;
        if(!preg_match('~faq|pytani|summary|podsum~i',$m[0][0]))$points[]=$end;
        $offset=$end;
    }
    if(!$points)return $html;
    $last=count($points)-1;$at1=$points[max(0,(int)floor($last*.28))];$at2=$points[max(0,(int)floor($last*.65))];
    $ops=[[$at1,zp_editorial_block_3236($d,0)],[$at2,zp_editorial_block_3236($d,1)]];
    // Insert backwards. Same-offset blocks retain their intended order.
    for($i=count($ops)-1;$i>=0;$i--){$inner=substr_replace($inner,$ops[$i][1],$ops[$i][0],0);}
    $article=substr_replace($article,$inner,$body[2],$body[3]-$body[2]);
    return substr_replace($html,$article,$outer[0],$outer[1]-$outer[0]);
}
function zp_editorial_filter_3236($html){
    if(!is_string($html)||strpos($html,'zpArticleNew')===false)return $html;
    $path=zp_article_growth_20261017_context();return $path?zp_editorial_transform_3236($html,$path):$html;
}
add_filter('the_content','zp_editorial_filter_3236',32);
add_filter('elementor/frontend/the_content','zp_editorial_filter_3236',32);
add_action('wp_enqueue_scripts',static function(){if(zp_article_growth_20261017_context()!=='')wp_enqueue_style('zp-editorial-3236',ZP_SUITE_URL.'assets/css/editorial-3236.css',[],ZP_SUITE_VERSION);},45);
