<?php
if (!defined('ABSPATH')) exit;

/**
 * Presentation only: prices on English pages in US dollars, American style ("$1,250 USD"), converted at the NBP
 * mid-rate and rounded. Translations and form amounts always remain PLN. Shown on every English page that has an
 * English version (Tłumacz EN 2 / language module); Polish pages are never touched.
 */
final class ZP_EN_Public_Currency_20261011 {
    public const RATE='zps_en_public_currency_20261011_rate';
    public const ATTEMPT='zps_en_public_currency_20261011_attempt';
    public const DAILY='zp_en_public_currency_daily_20261011';
    public const REFRESH='zp_en_public_currency_refresh_20261011';
    public const URL='https://api.nbp.pl/api/exchangerates/rates/a/usd/?format=json';
    public const POLICY='usd_nbp_human_v2';
    /** Changes with assets/en-public-currency-20261011.js, so browsers fetch the new file. */
    public const SCRIPT='20261014';
    private const ERRORS=['http_failed','http_status','rate_invalid','storage_failed'];

    public static function parse_rate(array $data,int $now): array {
        $rows=$data['rates']??null;
        if (($data['table']??null)!=='A'||($data['code']??null)!=='USD'||!is_array($rows)||array_keys($rows)!==[0]||!is_array($rows[0])) throw new RuntimeException('rate_invalid');
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
        try { return self::parse_rate(['table'=>'A','code'=>'USD','rates'=>[['mid'=>$r['mid_scaled']/1000000,'effectiveDate'=>$r['effective_date']??null,'no'=>$r['table_no']??null]]],$r['fetched_at']); } catch(Throwable $e) { return []; }
    }
    /** Integer cents and micro-rate avoid boundary drift between PHP and JS. */
    public static function amount(string $text): ?int {
        $text=trim(str_replace(["\xc2\xa0","\xe2\x80\xaf"],' ',$text));
        if (preg_match('/^(?:[0-9]{1,3}(?:,[0-9]{3})+|[0-9]{1,3}(?: [0-9]{3})+|[0-9]+)(?:\.[0-9]{1,2})?$/D',$text)) $text=str_replace([',',' '],'',$text);
        elseif (preg_match('/^[0-9]+,[0-9]{1,2}$/D',$text)) $text=str_replace(',','.',$text);
        else return null;
        $parts=explode('.',$text);if (strlen($parts[0])>8||(int)$parts[0]>10000000) return null;
        $cents=(int)$parts[0]*100+(int)str_pad($parts[1]??'',2,'0');return $cents<=1000000000?$cents:null;
    }
    public static function usd(int $cents,int $mid): int {
        if ($cents<0||$cents>1000000000||$mid<10000||$mid>1000000000) throw new RuntimeException('rate_invalid');
        if ($cents===0) return 0;
        $n=$cents*10000;$step=$n<10*$mid?1:($n<100*$mid?5:($n<1000*$mid?10:50));
        return max(1,intdiv($n*2+$step*$mid,2*$step*$mid)*$step);
    }
    private static function formatted(int $n): string { return number_format($n,0,'.',','); }
    /** Exact currency/amount tokens only; no dates, phones, percentages or gold adjectives. */
    public static function text_edits(string $text,int $mid): array {
        $num='(?:[0-9]{1,3}(?:,[0-9]{3})+|[0-9]{1,3}(?:[ \x{00A0}\x{202F}][0-9]{3})+|[0-9]+)(?:[.,][0-9]{1,2})?';
        $chain=$num.'(?:\s*(?:–|—|-|to|and)\s*'.$num.'|,\s+'.$num.'){0,4}';$out=[];
        $re='~(?<![\pL\pN.,-])(?:(?<unit>PLN)\s+(?<prefix>'.$chain.')|(?<suffix>'.$chain.')\s+(?<endunit>PLN|zł))(?![\pL\pN]|[.,][0-9])~iu';
        if (!preg_match_all($re,$text,$matches,PREG_SET_ORDER|PREG_OFFSET_CAPTURE)) return [];
        foreach ($matches as $m) {
            $u=($m['unit'][1]??-1)>=0?$m['unit']:$m['endunit'];$group=($m['prefix'][1]??-1)>=0?$m['prefix']:$m['suffix'];$tokens=[];$valid=true;
            preg_match_all('~'.$num.'~u',$group[0],$numbers,PREG_OFFSET_CAPTURE);
            foreach ($numbers[0] as [$raw,$offset]) { $cents=self::amount($raw);if ($cents===null) {$valid=false;break;} $tokens[]=[$group[1]+$offset,strlen($raw),self::formatted(self::usd($cents,$mid))]; }
            if (!$valid||!$tokens) continue;
            // American format: every amount with "$", the code once after the last one ("$250–$550 USD").
            foreach ($tokens as $i=>$token) $tokens[$i][2]='$'.$token[2];
            if (($m['unit'][1]??-1)>=0) { $out[]=[$u[1],$group[1]-$u[1],''];$tokens[count($tokens)-1][2].=' USD'; }
            else $out[]=[$u[1],strlen($u[0]),'USD'];
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
            $raw=$value[$key];$amount=(is_string($raw)||is_int($raw)||is_float($raw))&&!is_bool($raw)?self::amount((string)$raw):null;
            if ($amount===null) { $valid=false;break; } $numeric[$key]=self::usd($amount,$mid);
        }
        // Unsupported typed monetary shapes stay entirely PLN; never make USD with an unconverted amount.
        if (!$valid) return $value;
        foreach ($value as $key=>$v) {
            if (isset($numeric[$key])) $value[$key]=is_string($v)?(string)$numeric[$key]:$numeric[$key];
            elseif ($key!=='priceCurrency') $value[$key]=self::schema($v,$mid,$typed?$effective:'');
        }
        if ($typed&&$effective==='PLN') {
            $childUsd=strpos(wp_json_encode($value),'"priceCurrency":"USD"')!==false;
            if ($numeric||$childUsd||($value['description']??null)!==$oldDescription) $value['priceCurrency']='USD';
        }
        return $value;
    }
    /** Ready on every public page that has an English version (address and translations), whichever translator made it. */
    public static function gate(string $source): string {
        if (!class_exists('ZPL\\Html')||!class_exists('ZPL\\Router')||!class_exists('ZPL\\Dict')) return 'dependency_missing';
        if (preg_match('~^/(?:oferty|briefy)(?:/|$)~i',$source)) return 'private_or_unmapped';
        return \ZPL\Router::has_route($source)&&\ZPL\Dict::has_page($source)?'ready':'awaiting_translation';
    }
    private static function request_ok(): bool {
        if (is_admin()||(defined('REST_REQUEST')&&REST_REQUEST)||wp_doing_ajax()||wp_doing_cron()||(defined('WP_CLI')&&WP_CLI)||is_feed()||is_404()||($_SERVER['REQUEST_METHOD']??'GET')!=='GET') return false;
        foreach ($_GET as $key=>$value) {
            if (!is_string($key)||!is_string($value)||strlen($value)>256||preg_match('/[\x00-\x1f\x7f]/',$value)) return false;
            if (in_array($key,['utm_source','utm_medium','utm_campaign','utm_content','utm_term','gclid','msclkid'],true)) continue;
            if (class_exists('ZPL\\Router')&&\ZPL\Router::$source==='/studio-wyceny/'&&($key==='zpbs_service'&&in_array($value,['web','shop','brand','ads'],true)||$key==='zpbs_package'&&preg_match('/^[\pL\pN ._–-]{1,100}$/uD',$value))) continue;
            return false;
        }
        return !is_preview();
    }
    private static function indexable(string $html): bool {
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
    public static function filter(string $html,string $lang,string $source): string {
        try {
            // English pages only: the switch opens the Polish page at its own address, so Polish pages need nothing.
            if ($lang!=='en'||!self::request_ok()||!self::indexable($html)||self::gate($source)!=='ready'||!($rate=self::rate())||strpos($html,'id="zp-en-currency-config"')!==false) return $html;
            $html=self::document($html,$rate['mid_scaled']);
            $config=['policy'=>self::POLICY,'mid_scaled'=>$rate['mid_scaled'],'effective_date'=>$rate['effective_date'],'source'=>$source,'lang'=>$lang,'revision'=>hash('sha256',self::POLICY.'|'.$rate['mid_scaled'].'|'.$rate['effective_date'])];
            $tags='<script type="application/json" id="zp-en-currency-config">'.wp_json_encode($config,JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT).'</script>';
            $url=plugins_url('../assets/en-public-currency-20261011.js',__FILE__);$tags.='<script defer src="'.esc_url($url.'?ver='.self::POLICY.'.'.self::SCRIPT).'"></script>';
            $tags.='<p id="zp-en-currency-note" style="font:inherit;font-size:12px;line-height:1.5;text-align:center;padding:12px;margin:0">Prices in USD are approximate: converted from PLN at the NBP mid-rate of '.esc_html($rate['effective_date']).' and rounded. Your proposal confirms the currency and applicable taxes.</p>';
            return preg_replace('~</body>~i',$tags.'</body>',$html,1)??$html;
        } catch(Throwable $ignored) { return $html; }
    }
    public static function schedule(): void {
        if (!wp_next_scheduled(self::DAILY)) { $now=new DateTimeImmutable('now',new DateTimeZone('Europe/Warsaw'));$next=$now->setTime(14,15);if ($next<=$now) $next=$next->modify('+1 day');wp_schedule_single_event($next->getTimestamp(),self::DAILY); }
        if (current_user_can('manage_options')&&!self::rate()&&!get_option(self::ATTEMPT,[])&&!wp_next_scheduled(self::REFRESH)) wp_schedule_single_event(time()+1,self::REFRESH);
    }
    public static function refresh(): void {
        $attempt=['at'=>time(),'error'=>''];
        try {
            $response=wp_remote_get(self::URL,['timeout'=>8,'redirection'=>0,'sslverify'=>true,'limit_response_size'=>16384,'headers'=>['Accept'=>'application/json']]);
            if (is_wp_error($response)) throw new RuntimeException('http_failed');if (wp_remote_retrieve_response_code($response)!==200) throw new RuntimeException('http_status');
            $raw=wp_remote_retrieve_body($response);if (!is_string($raw)||strlen($raw)>16384) throw new RuntimeException('rate_invalid');$data=json_decode($raw,true);if (!is_array($data)) throw new RuntimeException('rate_invalid');$r=self::parse_rate($data,time());
            $old=self::rate();update_option(self::RATE,$r,false);if (get_option(self::RATE)!==$r) throw new RuntimeException('storage_failed');
            if (($old['mid_scaled']??null)!==$r['mid_scaled']||($old['effective_date']??null)!==$r['effective_date']) self::purge_cache();
        } catch(Throwable $e) { $attempt['error']=in_array($e->getMessage(),self::ERRORS,true)?$e->getMessage():'rate_invalid'; }
        update_option(self::ATTEMPT,$attempt,false);self::schedule();
    }
    public static function handle(): void {
        if (($_SERVER['REQUEST_METHOD']??'')!=='POST'||!current_user_can('manage_options')) wp_die('Brak uprawnień.');check_admin_referer('zp_en_currency_refresh');
        if (!wp_next_scheduled(self::REFRESH)) wp_schedule_single_event(time()+1,self::REFRESH);
        $args=['page'=>'zp-tlumacz-en','tab'=>'settings','zp_currency_notice'=>'queued'];if (($_POST['_zp_workspace']??null)==='1') $args['zp_workspace']='1';
        wp_safe_redirect(add_query_arg($args,admin_url('admin.php')));exit;
    }
    public static function render(): void {
        if (!is_admin()||!current_user_can('manage_options')||($_SERVER['REQUEST_METHOD']??'GET')!=='GET'||wp_doing_ajax()) return;
        $rate=self::rate();$gate=self::gate('/');$attempt=get_option(self::ATTEMPT,[]);$next=wp_next_scheduled(self::DAILY);
        echo '<section class="zpte2-card"><h2>Ceny w USD na stronach EN</h2><p>'.esc_html($rate?'Strony angielskie pokazują ceny w dolarach, jak na stronach amerykańskich: „$1,250 USD” (przeliczone kursem średnim NBP i zaokrąglone).':'Ceny w dolarach pojawią się po pobraniu kursu NBP; do tego czasu strony angielskie pokazują ceny w PLN.').'</p>';
        if ($rate&&$gate!=='ready') echo '<p>Strona główna nie ma jeszcze wersji angielskiej, więc nie pokazuje cen w USD.</p>';
        if ($rate) echo '<p>Kurs NBP: 1 USD = '.esc_html(number_format($rate['mid_scaled']/1000000,4,'.','')).' PLN · data publikacji '.esc_html($rate['effective_date']).' · pobrano '.esc_html(wp_date('d.m.Y H:i',$rate['fetched_at'],wp_timezone())).'.</p>';
        echo '<p>Aktualizacja dzienna: '.esc_html($next?wp_date('d.m.Y H:i',(int)$next,wp_timezone()):'brak zdarzenia').' (WP-Cron uruchamia się przy ruchu). Kurs ostatnio poprawny zostaje zachowany przy błędzie. Zaokrąglenie: do 1 / 5 / 10 / 50 USD według poziomu ceny.</p>';
        if (is_array($attempt)&&in_array($attempt['error']??'',self::ERRORS,true)) echo '<p>Ostatnia próba: '.esc_html($attempt['error']).'.</p>';
        echo '<form method="post" action="'.esc_url(admin_url('admin-post.php')).'"><input type="hidden" name="action" value="zp_en_currency_refresh">';wp_nonce_field('zp_en_currency_refresh');echo '<button type="submit" class="button">Zaplanuj pobranie aktualnego kursu NBP</button></form><p>Polskie ceny i wartości wysyłane z formularzy pozostają w PLN. Cztery ceny w osadzonym ekranie Studio Wyceny używają kursu strony nadrzędnej.</p></section>';
    }
    /**
     * 3.1.1: dollar prices are on English pages only, so a new NBP rate (every working day) changes the version of the English
     * pages alone (cache_version() below) and ZP Suite's HTML cache stops serving just their copies. It used to empty the
     * whole cache, Polish pages included, once a day.
     */
    private static function purge_cache(): void { do_action('zp_html_cache_refresh'); }
    /**
     * 3.1.1: the rate is part of the version of every English page in ZP Suite's HTML cache (filter zpl_cache_versions,
     * \ZPL\Router::cache_paths), so after purge_cache() above the cache stops serving the copies with the old prices.
     * @param mixed $v
     */
    public static function cache_version($v): array {
        $v=is_array($v)?$v:[];$r=self::rate();
        $v['*']=(string)($v['*']??'').'|usd:'.self::POLICY.':'.self::SCRIPT.':'.($r?$r['mid_scaled'].':'.$r['effective_date']:'-');
        return $v;
    }
    public static function register(): void {
        add_filter('zpl_cache_versions',[self::class,'cache_version']);add_filter('zp_en_public_price_html',[self::class,'filter'],10,3);add_action('init',[self::class,'schedule']);add_action(self::DAILY,[self::class,'refresh']);add_action(self::REFRESH,[self::class,'refresh']);add_action('admin_post_zp_en_currency_refresh',[self::class,'handle']);add_action('zpte2_settings_cards',[self::class,'render'],30);
    }
}
