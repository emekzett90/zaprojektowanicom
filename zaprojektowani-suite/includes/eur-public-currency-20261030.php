<?php
if (!defined('ABSPATH')) exit;

/**
 * 3.2.97: presentation only, like the dollar prices of the English pages (en-public-currency-20261011.php): German and
 * Spanish pages show prices in euro the way agencies there write them ("ab 940 €", "desde 1.550 €/mes"), converted at
 * the NBP mid-rate and rounded to 1 / 5 / 10 / 50 € by price level. Translations keep the amounts in złoty (Tłumacz AI is
 * told to copy them), and form values and the budget sent with a brief stay in złoty. Only German and Spanish pages
 * with their own version and their Quote Studio frame; Polish and English pages are never touched.
 */
final class ZP_EUR_Public_Currency_20261030 {
    public const RATE='zps_eur_public_currency_20261030_rate';
    public const ATTEMPT='zps_eur_public_currency_20261030_attempt';
    public const DAILY='zp_eur_public_currency_daily_20261030';
    public const REFRESH='zp_eur_public_currency_refresh_20261030';
    public const URL='https://api.nbp.pl/api/exchangerates/rates/a/eur/?format=json';
    public const POLICY='eur_nbp_human_v1';
    /** Changes with assets/eur-public-currency-20261030.js, so browsers fetch the new file. */
    public const SCRIPT='20261030';
    public const LANGS=['de','es'];
    private const ERRORS=['http_failed','http_status','rate_invalid','storage_failed'];
    /** One amount: Polish "3 999", German "3.999", plain "3999", with or without ",50". */
    private const NUM='(?:[0-9]{1,3}(?:[ .\x{00A0}\x{202F}][0-9]{3})+|[0-9]+)(?:,[0-9]{1,2})?';

    public static function parse_rate(array $data,int $now): array {
        $rows=$data['rates']??null;
        if (($data['table']??null)!=='A'||($data['code']??null)!=='EUR'||!is_array($rows)||array_keys($rows)!==[0]||!is_array($rows[0])) throw new RuntimeException('rate_invalid');
        $r=$rows[0];$mid=$r['mid']??null;$date=$r['effectiveDate']??null;
        if ((!is_int($mid)&&!is_float($mid))||!is_finite((float)$mid)||$mid<0.01||$mid>1000||!is_string($date)||!is_string($r['no']??null)||preg_match('~^[0-9]{1,4}/A/NBP/[0-9]{4}$~D',$r['no'])!==1) throw new RuntimeException('rate_invalid');
        $d=DateTimeImmutable::createFromFormat('!Y-m-d',$date,new DateTimeZone('Europe/Warsaw'));
        if (!$d||$d->format('Y-m-d')!==$date||$date>(new DateTimeImmutable('@'.$now))->setTimezone(new DateTimeZone('Europe/Warsaw'))->format('Y-m-d')) throw new RuntimeException('rate_invalid');
        $scaled=(int)round($mid*1000000);
        if ($scaled<=0||abs($scaled/1000000-$mid)>0.00000001) throw new RuntimeException('rate_invalid');
        return ['mid_scaled'=>$scaled,'effective_date'=>$date,'table_no'=>$r['no'],'fetched_at'=>$now];
    }
    public static function rate(): array {
        $r=get_option(self::RATE,[]);
        if (!is_array($r)||!is_int($r['mid_scaled']??null)||!is_int($r['fetched_at']??null)||$r['fetched_at']>time()+60) return [];
        try { return self::parse_rate(['table'=>'A','code'=>'EUR','rates'=>[['mid'=>$r['mid_scaled']/1000000,'effectiveDate'=>$r['effective_date']??null,'no'=>$r['table_no']??null]]],$r['fetched_at']); } catch(Throwable $e) { return []; }
    }
    /** Integer grosze and micro-rate, the same arithmetic as the browser script. */
    public static function amount(string $text): ?int {
        $text=trim(str_replace(["\xc2\xa0","\xe2\x80\xaf"],' ',$text));
        if (!preg_match('/^(?:[0-9]{1,3}(?:[ .][0-9]{3})+|[0-9]+)(?:,[0-9]{1,2})?$/D',$text)) return null;
        $parts=explode(',',str_replace([' ','.'],'',$text));if (strlen($parts[0])>8||(int)$parts[0]>10000000) return null;
        $cents=(int)$parts[0]*100+(int)str_pad($parts[1]??'',2,'0');return $cents<=1000000000?$cents:null;
    }
    public static function eur(int $cents,int $mid): int {
        if ($cents<0||$cents>1000000000||$mid<10000||$mid>1000000000) throw new RuntimeException('rate_invalid');
        if ($cents===0) return 0;
        $n=$cents*10000;$step=$n<10*$mid?1:($n<100*$mid?5:($n<1000*$mid?10:50));
        return max(1,intdiv($n*2+$step*$mid,2*$step*$mid)*$step);
    }
    /** German and Spanish style: 1.550 (no separator below 1.000). */
    private static function formatted(int $n): string { return number_format($n,0,',','.'); }
    /**
     * Exact złoty amounts only ("3 999 zł", "2 000–5 000 zł", "ab 1 200 zł/Monat", "PLN 999"); no dates, phones or
     * percentages. "zł" becomes "€" after the last amount, joined by a no-break space.
     */
    public static function text_edits(string $text,int $mid): array {
        $num=self::NUM;$sp='[\s\x{00A0}\x{202F}]';
        $chain=$num.'(?:'.$sp.'*(?:–|—|-|bis|und|a|y|hasta)'.$sp.'*'.$num.'|,'.$sp.'+'.$num.'){0,4}';$out=[];
        $re='~(?<![\pL\pN.,-])(?:(?<unit>PLN)'.$sp.'+(?<prefix>'.$chain.')|(?<suffix>'.$chain.')'.$sp.'*(?<endunit>PLN|zł))(?![\pL\pN]|[.,][0-9])~iu';
        if (!preg_match_all($re,$text,$matches,PREG_SET_ORDER|PREG_OFFSET_CAPTURE)) return [];
        foreach ($matches as $m) {
            $prefix=($m['unit'][1]??-1)>=0;$group=$prefix?$m['prefix']:$m['suffix'];$tokens=[];$valid=true;
            preg_match_all('~'.$num.'~u',$group[0],$numbers,PREG_OFFSET_CAPTURE);
            foreach ($numbers[0] as [$raw,$offset]) { $cents=self::amount($raw);if ($cents===null) {$valid=false;break;} $tokens[]=[$group[1]+$offset,strlen($raw),self::formatted(self::eur($cents,$mid))]; }
            if (!$valid||!$tokens) continue;
            $end=$group[1]+strlen($group[0]);
            if ($prefix) { $out[]=[$m['unit'][1],$group[1]-$m['unit'][1],''];$tokens[count($tokens)-1][2].="\u{00A0}€"; }
            else $out[]=[$end,$m['endunit'][1]+strlen($m['endunit'][0])-$end,"\u{00A0}€"];
            array_push($out,...$tokens);
        }
        usort($out,static fn($a,$b)=>$b[0]<=>$a[0]);return $out;
    }
    public static function money_text(string $text,int $mid): string {
        foreach (self::text_edits($text,$mid) as [$at,$length,$new]) $text=substr_replace($text,$new,$at,$length);
        return $text;
    }
    public static function schema($value,int $mid,string $currency='') {
        if (!is_array($value)) return is_string($value)?self::money_text($value,$mid):$value;
        $types=$value['@type']??[];$types=is_string($types)?[$types]:$types;$typed=is_array($types)&&array_intersect($types,['Offer','AggregateOffer','PriceSpecification','UnitPriceSpecification']);
        $effective=is_string($value['priceCurrency']??null)?$value['priceCurrency']:$currency;
        $numeric=[];$valid=true;$oldDescription=$value['description']??null;
        if ($typed&&$effective==='PLN') foreach (['price','minPrice','maxPrice','lowPrice','highPrice'] as $key) if (array_key_exists($key,$value)) {
            $raw=$value[$key];$amount=(is_string($raw)||is_int($raw)||is_float($raw))&&!is_bool($raw)?self::schema_amount((string)$raw):null;
            if ($amount===null) { $valid=false;break; } $numeric[$key]=self::eur($amount,$mid);
        }
        // An offer whose amounts cannot be read stays entirely in PLN: never EUR with an unconverted amount.
        if (!$valid) return $value;
        foreach ($value as $key=>$v) {
            if (isset($numeric[$key])) $value[$key]=is_string($v)?(string)$numeric[$key]:$numeric[$key];
            elseif ($key!=='priceCurrency') $value[$key]=self::schema($v,$mid,$typed?$effective:'');
        }
        if ($typed&&$effective==='PLN') {
            $childEur=strpos((string)wp_json_encode($value),'"priceCurrency":"EUR"')!==false;
            if ($numeric||$childEur||($value['description']??null)!==$oldDescription) $value['priceCurrency']='EUR';
        }
        return $value;
    }
    /** Structured data writes amounts the machine way: 3999, 3999.00. */
    private static function schema_amount(string $raw): ?int {
        if (preg_match('/^[0-9]{1,8}(?:\.[0-9]{1,2})?$/D',$raw)) { $p=explode('.',$raw);return (int)$p[0]*100+(int)str_pad($p[1]??'',2,'0'); }
        return self::amount($raw);
    }
    /** Ready on German and Spanish pages with their own version (address and translations) and in their Quote Studio frame. */
    public static function gate(string $source,string $lang): string {
        if (!in_array($lang,self::LANGS,true)) return 'other_language';
        if (!class_exists('ZPL\\Html')||!class_exists('ZPL\\Router')||!class_exists('ZPL\\Dict')) return 'dependency_missing';
        if (strpos($source,'/__frame/')===0) return 'ready';
        if (preg_match('~^/(?:oferty|briefy)(?:/|$)~i',$source)) return 'private_or_unmapped';
        return \ZPL\Router::has_route($source,$lang)&&\ZPL\Dict::has_page($source,$lang)?'ready':'awaiting_translation';
    }
    private static function frame(string $source): bool { return strpos($source,'/__frame/')===0; }
    private static function request_ok(string $source): bool {
        if (is_admin()||(defined('REST_REQUEST')&&REST_REQUEST)||wp_doing_ajax()||wp_doing_cron()||(defined('WP_CLI')&&WP_CLI)||($_SERVER['REQUEST_METHOD']??'GET')!=='GET') return false;
        $frame=self::frame($source);
        if (!$frame&&(is_feed()||is_404())) return false;
        foreach ($_GET as $key=>$value) {
            if (!is_string($key)||!is_string($value)||strlen($value)>256||preg_match('/[\x00-\x1f\x7f]/',$value)) return false;
            if (in_array($key,['utm_source','utm_medium','utm_campaign','utm_content','utm_term','gclid','msclkid','fbclid'],true)) continue;
            if ($frame&&in_array($key,['zpl_frame','zpl_lang','v','n'],true)) continue;
            if (class_exists('ZPL\\Router')&&\ZPL\Router::$source==='/studio-wyceny/'&&($key==='zpbs_service'&&in_array($value,['web','shop','brand','ads'],true)||$key==='zpbs_package'&&preg_match('/^[\pL\pN ._–-]{1,100}$/uD',$value))) continue;
            return false;
        }
        return $frame||!is_preview();
    }
    /**
     * A page the owner kept out of Google keeps its złoty. While a language is not public yet every page of it has
     * noindex (preview for the owner): those pages show euro already.
     */
    private static function indexable(string $html,string $lang): bool {
        if (class_exists('ZPL\\Lang')&&method_exists('ZPL\\Lang','is_public')&&!\ZPL\Lang::is_public($lang)) return true;
        foreach (headers_list() as $h) if (stripos($h,'X-Robots-Tag:')===0&&preg_match('/\bnoindex\b/i',$h)) return false;
        if (!preg_match('~<head\b[^>]*>(.*?)</head>~is',$html,$head)) return false;
        preg_match_all('~<meta\b[^>]*>~i',$head[1],$tags);
        foreach ($tags[0] as $tag) { $a=\ZPL\Html::attrs($tag);if (in_array(strtolower($a['name']??''),['robots','googlebot','bingbot'],true)&&preg_match('/\bnoindex\b/i',$a['content']??'')) return false; }
        return true;
    }
    private static function collect_text(array $node,string $html,array &$nodes,string &$plain): bool {
        if ($node['t']===3) { $text=\ZPL\Html::decode(substr($html,$node['s'],$node['e']-$node['s']));if ($plain!==''&&$text!==''&&!preg_match('/\s$/u',$plain)&&!preg_match('/^\s/u',$text)) $plain.=' ';$nodes[]=['node'=>$node,'offset'=>strlen($plain),'text'=>$text];$plain.=$text;return true; }
        if (!in_array($node['n'],['a','abbr','b','bdi','bdo','cite','del','em','i','ins','mark','q','s','small','span','strong','sub','sup','u','var'],true)) return false;
        $attrs=\ZPL\Html::attrs(substr($html,$node['s'],$node['te']-$node['s']));if (isset($attrs['data-zp-currency-skip'])||($attrs['id']??'')==='wpadminbar'||self::calculation_source($attrs)) return false;
        foreach ($node['c'] as $child) if (!self::collect_text($child,$html,$nodes,$plain)) return false;
        return true;
    }
    private static function attribute_updates(array $attrs,string $name,int $mid): array {
        $updates=[];foreach (['title','aria-label','aria-valuetext','label'] as $key) if (isset($attrs[$key])) { $new=self::money_text($attrs[$key],$mid);if ($new!==$attrs[$key]) $updates[$key]=$new; }
        if ($name==='meta'&&in_array(strtolower($attrs['name']??$attrs['property']??''),['description','og:title','og:description','twitter:title','twitter:description'],true)&&isset($attrs['content'])) { $new=self::money_text($attrs['content'],$mid);if ($new!==$attrs['content']) $updates['content']=$new; }
        return $updates;
    }
    /** Package prices the Quote Studio reads back for its sums: the browser script shows a euro copy next to them. */
    private static function calculation_source(array $attrs): bool { return preg_match('/(?:^|\s)(?:zpbsMiniCard__price|zpbsPkgPrice)(?:\s|$)/',$attrs['class']??'')===1; }
    private static function inline_attributes(array $node,string $html,int $mid,array &$edits): void {
        if ($node['t']!==1) return;$tag=substr($html,$node['s'],$node['te']-$node['s']);$attrs=\ZPL\Html::attrs($tag);
        if (isset($attrs['data-zp-currency-skip'])||($attrs['id']??'')==='wpadminbar'||self::calculation_source($attrs)) return;
        $updates=self::attribute_updates($attrs,$node['n'],$mid);if ($updates) $edits[]=[$node['s'],$node['te']-$node['s'],\ZPL\Html::set_attrs($tag,$updates)];
        foreach ($node['c'] as $child) self::inline_attributes($child,$html,$mid,$edits);
    }
    private static function walk(array $node,string $html,int $mid,array &$edits): void {
        if ($node['t']===3) { $raw=substr($html,$node['s'],$node['e']-$node['s']);$text=\ZPL\Html::decode($raw);$new=self::money_text($text,$mid);if ($new!==$text) $edits[]=[$node['s'],$node['e']-$node['s'],\ZPL\Html::esc($new)];return; }
        $tag=substr($html,$node['s'],$node['te']-$node['s']);$attrs=\ZPL\Html::attrs($tag);
        if (in_array($node['n'],['style','textarea','code','pre','svg','math','iframe','object','canvas','noscript','template'],true)||isset($attrs['data-zp-currency-skip'])||($attrs['id']??'')==='wpadminbar'||self::calculation_source($attrs)) return;
        if ($node['n']==='script') {
            if (strtolower($attrs['type']??'')==='application/ld+json') { $raw=substr($html,$node['te'],$node['es']-$node['te']);$json=json_decode($raw,true);if (is_array($json)) { $new=self::schema($json,$mid);if ($new!==$json) $edits[]=[$node['te'],$node['es']-$node['te'],wp_json_encode($new,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_HEX_TAG|JSON_HEX_AMP)]; } }
            return;
        }
        $updates=self::attribute_updates($attrs,$node['n'],$mid);
        $nodes=[];$plain='';$inline=!in_array($node['n'],['#root','html','head','body'],true);
        if ($inline) foreach ($node['c'] as $child) if (!self::collect_text($child,$html,$nodes,$plain)) {$inline=false;break;}
        if ($inline&&$nodes) {
            $replacements=self::text_edits($plain,$mid);$perNode=[];$valid=true;
            foreach ($replacements as [$at,$length,$new]) { $found=false;foreach ($nodes as $i=>$n) if ($at>=$n['offset']&&$at+$length<=$n['offset']+strlen($n['text'])) { $perNode[$i][]=[$at-$n['offset'],$length,$new];$found=true;break; } if (!$found) {$valid=false;break;} }
            if ($valid) foreach ($perNode as $i=>$list) { $n=$nodes[$i];$text=$n['text'];foreach ($list as [$at,$length,$new]) $text=substr_replace($text,$new,$at,$length);$edits[]=[$n['node']['s'],$n['node']['e']-$n['node']['s'],\ZPL\Html::esc($text)]; }
            if ($valid&&$perNode&&$node['n']==='option'&&!array_key_exists('value',$attrs)) $updates['value']=trim($plain);
            foreach ($node['c'] as $child) self::inline_attributes($child,$html,$mid,$edits);
        } else foreach ($node['c'] as $child) self::walk($child,$html,$mid,$edits);
        if ($updates) $edits[]=[$node['s'],$node['te']-$node['s'],\ZPL\Html::set_attrs($tag,$updates)];
    }
    public static function document(string $html,int $mid): string {
        $tree=\ZPL\Html::parse($html);$edits=[];self::walk($tree,$html,$mid,$edits);usort($edits,static fn($a,$b)=>$b[0]<=>$a[0]);
        foreach ($edits as [$at,$length,$new]) $html=substr_replace($html,$new,$at,$length);return $html;
    }
    /** @param mixed $html  @param mixed $lang  @param mixed $source */
    public static function filter($html,$lang='',$source='') {
        if (!is_string($html)||!is_string($lang)||!is_string($source)) return $html;
        try {
            if (!in_array($lang,self::LANGS,true)||!self::request_ok($source)||self::gate($source,$lang)!=='ready'||!($rate=self::rate())||strpos($html,'id="zp-eur-currency-config"')!==false) return $html;
            if (!self::frame($source)&&!self::indexable($html,$lang)) return $html;
            $html=self::document($html,$rate['mid_scaled']);
            // The Quote Studio frame gets its prices here; what its own script writes later the parent page's script converts.
            if (self::frame($source)) return $html;
            $config=['policy'=>self::POLICY,'mid_scaled'=>$rate['mid_scaled'],'effective_date'=>$rate['effective_date'],'source'=>$source,'lang'=>$lang,'revision'=>hash('sha256',self::POLICY.'|'.$rate['mid_scaled'].'|'.$rate['effective_date'])];
            $tags='<script type="application/json" id="zp-eur-currency-config">'.wp_json_encode($config,JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT).'</script>';
            $url=plugins_url('../assets/eur-public-currency-20261030.js',__FILE__);$tags.='<script defer src="'.esc_url($url.'?ver='.self::POLICY.'.'.self::SCRIPT).'"></script>';
            return preg_replace('~</body>~i',$tags.'</body>',$html,1)??$html;
        } catch(Throwable $ignored) { return $html; }
    }
    public static function schedule(): void {
        if (!wp_next_scheduled(self::DAILY)) { $now=new DateTimeImmutable('now',new DateTimeZone('Europe/Warsaw'));$next=$now->setTime(14,20);if ($next<=$now) $next=$next->modify('+1 day');wp_schedule_single_event($next->getTimestamp(),self::DAILY); }
        if (current_user_can('manage_options')&&!self::rate()&&!get_option(self::ATTEMPT,[])&&!wp_next_scheduled(self::REFRESH)) wp_schedule_single_event(time()+1,self::REFRESH);
    }
    public static function refresh(): void {
        $attempt=['at'=>time(),'error'=>''];
        try {
            $response=wp_remote_get(self::URL,['timeout'=>8,'redirection'=>0,'sslverify'=>true,'limit_response_size'=>16384,'headers'=>['Accept'=>'application/json']]);
            if (is_wp_error($response)) throw new RuntimeException('http_failed');if (wp_remote_retrieve_response_code($response)!==200) throw new RuntimeException('http_status');
            $raw=wp_remote_retrieve_body($response);if (!is_string($raw)||strlen($raw)>16384) throw new RuntimeException('rate_invalid');$data=json_decode($raw,true);if (!is_array($data)) throw new RuntimeException('rate_invalid');$r=self::parse_rate($data,time());
            update_option(self::RATE,$r,false);if (get_option(self::RATE)!==$r) throw new RuntimeException('storage_failed');
        } catch(Throwable $e) { $attempt['error']=in_array($e->getMessage(),self::ERRORS,true)?$e->getMessage():'rate_invalid'; }
        update_option(self::ATTEMPT,$attempt,false);self::schedule();
    }
    public static function handle(): void {
        if (($_SERVER['REQUEST_METHOD']??'')!=='POST'||!current_user_can('manage_options')) wp_die('Brak uprawnień.');check_admin_referer('zp_eur_currency_refresh');
        if (!wp_next_scheduled(self::REFRESH)) wp_schedule_single_event(time()+1,self::REFRESH);
        $lang=in_array($_POST['zpte_lang']??'',self::LANGS,true)?(string)$_POST['zpte_lang']:'de';
        wp_safe_redirect(add_query_arg(['page'=>'zp-tlumacz-en','zpte_lang'=>$lang,'tab'=>'settings','zp_currency_notice'=>'queued'],admin_url('admin.php')));exit;
    }
    /** Card in Tłumacz AI → DE / ES → Ustawienia (the dollar card stays on the EN tab). */
    /** @param mixed $lang */
    public static function render($lang=''): void {
        if (!is_admin()||!current_user_can('manage_options')||($_SERVER['REQUEST_METHOD']??'GET')!=='GET'||wp_doing_ajax()) return;
        if (!is_string($lang)||!in_array($lang,self::LANGS,true)) return;
        $rate=self::rate();$attempt=get_option(self::ATTEMPT,[]);$next=wp_next_scheduled(self::DAILY);
        $example=$lang==='es'?'«desde 940 €»':'„ab 940 €“';
        echo '<section class="zpte2-card"><h2>Ceny w euro na stronach '.esc_html(strtoupper($lang)).'</h2><p>'.esc_html($rate?'Strony niemieckie i hiszpańskie pokazują ceny w euro, tak jak piszą je agencje w Niemczech i Hiszpanii: '.$example.' (przeliczone kursem średnim NBP i zaokrąglone).':'Ceny w euro pojawią się po pobraniu kursu NBP; do tego czasu strony niemieckie i hiszpańskie pokazują ceny w złotych.').'</p>';
        if ($rate) echo '<p>Kurs NBP: 1 EUR = '.esc_html(number_format($rate['mid_scaled']/1000000,4,'.','')).' PLN · data publikacji '.esc_html($rate['effective_date']).' · pobrano '.esc_html(wp_date('d.m.Y H:i',$rate['fetched_at'],wp_timezone())).'.</p>';
        echo '<p>Aktualizacja dzienna: '.esc_html($next?wp_date('d.m.Y H:i',(int)$next,wp_timezone()):'brak zdarzenia').' (WP-Cron uruchamia się przy ruchu). Kurs ostatnio poprawny zostaje zachowany przy błędzie. Zaokrąglenie: do 1 / 5 / 10 / 50 € według poziomu ceny.</p>';
        if (is_array($attempt)&&in_array($attempt['error']??'',self::ERRORS,true)) echo '<p>Ostatnia próba: '.esc_html($attempt['error']).'.</p>';
        echo '<form method="post" action="'.esc_url(admin_url('admin-post.php')).'"><input type="hidden" name="action" value="zp_eur_currency_refresh"><input type="hidden" name="zpte_lang" value="'.esc_attr($lang).'">';wp_nonce_field('zp_eur_currency_refresh');echo '<button type="submit" class="button">Zaplanuj pobranie aktualnego kursu NBP</button></form><p>Polskie ceny, tłumaczenia i wartości wysyłane z formularzy pozostają w złotych.</p></section>';
    }
    public static function register(): void {
        add_filter('zp_en_public_price_html',[self::class,'filter'],10,3);add_action('init',[self::class,'schedule']);add_action(self::DAILY,[self::class,'refresh']);add_action(self::REFRESH,[self::class,'refresh']);add_action('admin_post_zp_eur_currency_refresh',[self::class,'handle']);add_action('zpte2_lang_settings_cards',[self::class,'render'],30);
    }
}
