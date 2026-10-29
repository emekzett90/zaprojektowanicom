<?php
/** Google discovery for ordinary articles: sitemap submission and genuine URL Inspection, never the restricted Indexing API. */
if (!defined('ABSPATH')) { exit; }

final class ZP_Article_Google_20261010 {
    public const QUEUE='zps_article_google_20261010_queue';
    public const LOCK='zps_article_google_20261010_lock';
    public const WORKER='zp_article_google_worker_20261010';
    public const META='_zp_article_google_20261010';
    public static function allowed(int $id): bool {return current_user_can('manage_options')&&current_user_can('edit_post',$id)&&class_exists('ZP_Admin_Stats_20261009')&&ZP_Admin_Stats_20261009::allowed();}
    private static function lock(): ?string {
        $token=wp_generate_uuid4();$entry=['token'=>$token,'expires'=>time()+120];
        if(add_option(self::LOCK,$entry,'',false))return $token;
        $old=get_option(self::LOCK,[]);if(!is_array($old)||(int)($old['expires']??PHP_INT_MAX)>=time())return null;
        global $wpdb;if(!isset($wpdb)||!$wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->options} WHERE option_name = %s AND option_value = %s",self::LOCK,maybe_serialize($old))))return null;
        wp_cache_delete(self::LOCK,'options');return add_option(self::LOCK,$entry,'',false)?$token:null;
    }
    private static function owns(string $token): bool {$old=get_option(self::LOCK,[]);return is_array($old)&&($old['token']??'')===$token&&(int)($old['expires']??0)>time();}
    private static function unlock(string $token): void {$old=get_option(self::LOCK,[]);if(!is_array($old)||($old['token']??'')!==$token)return;global $wpdb;if(isset($wpdb)&&$wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->options} WHERE option_name = %s AND option_value = %s",self::LOCK,maybe_serialize($old))))wp_cache_delete(self::LOCK,'options');}
    private static function save(string $name,array $data): void {update_option($name,$data,false);if(get_option($name,null)!==$data)throw new RuntimeException('storage_failed');}
    private static function record(int $id,array $value): void {update_post_meta($id,self::META,$value);if(get_post_meta($id,self::META,true)!==$value)throw new RuntimeException('storage_failed');}
    public static function public_url(int $id): string {
        $p=get_post($id);if((string)get_option('blog_public','1')!=='1')throw new RuntimeException('not_indexable');if(!$p||$p->post_type!=='post'||$p->post_status!=='publish'||$p->post_password!==''||wp_is_post_revision($id)||wp_is_post_autosave($id))throw new RuntimeException('not_public');
        $robots=get_post_meta($id,'rank_math_robots',true);if(is_array($robots)&&in_array('noindex',$robots,true))throw new RuntimeException('not_indexable');
        if(function_exists('zp_ai_post_urls')&&!zp_ai_post_urls($p))throw new RuntimeException('not_indexable');
        $url=get_permalink($id);$parts=is_string($url)?wp_parse_url($url):false;$home=wp_parse_url(home_url('/'));
        if(!is_array($parts)||($parts['scheme']??'')!=='https'||($parts['host']??'')!==($home['host']??'')||isset($parts['port'])||isset($parts['user'])||isset($parts['pass'])||isset($parts['query'])||isset($parts['fragment'])||!str_starts_with($parts['path']??'', $home['path']??'/'))throw new RuntimeException('invalid_url');
        $canonical=get_post_meta($id,'rank_math_canonical_url',true);if(is_string($canonical)&&$canonical!==''&&$canonical!==$url)throw new RuntimeException('canonical_mismatch');return $url;
    }
    public static function transition(string $new,string $old,$post): void {
        if($new!=='publish'||$old==='publish'||!$post instanceof WP_Post||$post->post_type!=='post')return;
        try{self::enqueue((int)$post->ID);}catch(Throwable $ignored){update_post_meta((int)$post->ID,self::META,['state'=>'needs_attention','error'=>'queue_failed','at'=>time()]);}
    }
    public static function enqueue(int $id): void {
        $url=self::public_url($id);$token=self::lock();if(!$token)throw new RuntimeException('busy');
        try{$q=get_option(self::QUEUE,[]);$q=is_array($q)?$q:[];if(count($q)>=100&&!isset($q[$id]))throw new RuntimeException('queue_limit');$q[$id]=['url'=>$url,'at'=>time(),'event'=>wp_generate_uuid4()];self::save(self::QUEUE,$q);$old=get_post_meta($id,self::META,true);self::record($id,array_merge(is_array($old)?$old:[],['state'=>'queued','url'=>$url,'at'=>time(),'error'=>'']));if(!wp_next_scheduled(self::WORKER)){wp_schedule_single_event(time()+5,self::WORKER);if(!wp_next_scheduled(self::WORKER))throw new RuntimeException('cron_failed');}}
        finally{self::unlock($token);}
    }
    private static function connection(): array {
        if(!class_exists('ZP_Admin_Stats_20261009'))throw new RuntimeException('needs_connection');$c=ZP_Admin_Stats_20261009::connection();
        if(empty($c['authorized'])||empty($c['gsc_configured'])||($c['gsc_property']??'')!==trailingslashit(home_url('/'))||!method_exists('RankMath\\Google\\Api','http_put')||!method_exists('RankMath\\Google\\Api','get_response'))throw new RuntimeException('needs_connection');return $c;
    }
    public static function no_debug_log($value): bool {return false;}
    private static function request(string $method,string $url,array $body=[]): array {
        $api=\RankMath\Google\Api::get();add_filter('rank_math/analytics/log_response',[self::class,'no_debug_log'],PHP_INT_MAX);
        try{$api->set_workflow('');$value=$method==='PUT'?$api->http_put($url,[],10):$api->http_post($url,$body,10);$response=$api->get_response();$code=is_wp_error($response)?0:(int)wp_remote_retrieve_response_code($response);
            if(!$api->is_success()||!in_array($code,[200,204],true))throw new RuntimeException(in_array($code,[401,403,429,500,502,503,504],true)?'google_'.$code:'google_transport');return ['code'=>$code,'value'=>$value];
        }finally{$api->set_workflow('');remove_filter('rank_math/analytics/log_response',[self::class,'no_debug_log'],PHP_INT_MAX);}
    }
    public static function worker(): void {
        $token=self::lock();if(!$token){wp_schedule_single_event(time()+30,self::WORKER);return;}
        try{$q=get_option(self::QUEUE,[]);if(!is_array($q)||!$q)return;$active=[];
            foreach($q as $id=>$item){if(!self::owns($token))return;try{$url=self::public_url((int)$id);if(!is_array($item)||$url!==($item['url']??''))throw new RuntimeException('url_changed');$active[(int)$id]=$url;}catch(Throwable $e){self::record((int)$id,['state'=>'needs_attention','error'=>'not_public_or_changed','at'=>time()]);}}
            // One idempotent sitemap PUT for the coalesced batch. No per-article fabricated submission.
            if($active){try{$c=self::connection();$sitemap=home_url('/sitemap_index.xml');$check=wp_remote_get($sitemap,['timeout'=>10,'redirection'=>0,'limit_response_size'=>1048576]);$xml=is_wp_error($check)?'':wp_remote_retrieve_body($check);if(is_wp_error($check)||wp_remote_retrieve_response_code($check)!==200||!is_string($xml)||!preg_match('~<sitemapindex\b~',$xml)||!str_contains($xml,'<loc>'))throw new RuntimeException('sitemap_unavailable');
                    $sent=self::request('PUT','https://www.googleapis.com/webmasters/v3/sites/'.rawurlencode($c['gsc_property']).'/sitemaps/'.rawurlencode($sitemap));
                    foreach($active as $id=>$url){if(!self::owns($token))return;$prior=get_post_meta($id,self::META,true);self::record($id,array_merge(is_array($prior)?$prior:[],['state'=>'sitemap_submitted','url'=>$url,'sitemap'=>$sitemap,'submitted_at'=>time(),'http_code'=>$sent['code'],'error'=>'']));}
                }catch(Throwable $e){$safe=$e instanceof RuntimeException?$e->getMessage():'';if(!in_array($safe,['needs_connection','sitemap_unavailable','google_401','google_403','google_429','google_500','google_502','google_503','google_504','google_transport','storage_failed'],true))$safe='google_transport';foreach($active as $id=>$url){if(!self::owns($token))return;self::record($id,['state'=>'needs_attention','url'=>$url,'error'=>$safe,'at'=>time()]);}}}
            // Failed/ambiguous network actions are shown explicitly; no automatic replay.
            if(!self::owns($token))return;$latest=get_option(self::QUEUE,[]);$latest=is_array($latest)?$latest:[];foreach($q as $id=>$item){if(($latest[$id]['event']??null)===($item['event']??null))unset($latest[$id]);}self::save(self::QUEUE,$latest);if($latest&&!wp_next_scheduled(self::WORKER))wp_schedule_single_event(time()+5,self::WORKER);
        }finally{self::unlock($token);}
    }
    public static function parse_inspection(array $response,string $url): array {
        $r=$response['inspectionResult']??null;$s=is_array($r)?($r['indexStatusResult']??null):null;if(!is_array($s))throw new RuntimeException('invalid_inspection');
        $v=$s['verdict']??null;if(!in_array($v,['VERDICT_UNSPECIFIED','PASS','PARTIAL','FAIL','NEUTRAL'],true))throw new RuntimeException('invalid_inspection');
        $text=static function($value,int $limit): string {if($value===null)return '';if(!is_string($value)||strlen($value)>$limit||preg_match('/[\x00-\x08\x0b\x0c\x0e-\x1f]/',$value))throw new RuntimeException('invalid_inspection');return $value;};
        $link=$text($r['inspectionResultLink']??null,3000);if($link!==''&&(!str_starts_with($link,'https://search.google.com/search-console/')||isset(wp_parse_url($link)['user'])||isset(wp_parse_url($link)['pass'])))throw new RuntimeException('invalid_inspection');
        $last=$text($s['lastCrawlTime']??null,80);if($last!==''&&!preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(?:\.\d+)?Z$/D',$last))throw new RuntimeException('invalid_inspection');
        return ['url'=>$url,'checked_at'=>time(),'verdict'=>$v,'coverage'=>$text($s['coverageState']??null,500),'last_crawl'=>$last,'google_canonical'=>$text($s['googleCanonical']??null,3000),'inspection_link'=>$link];
    }
    public static function inspect(int $id): void {
        if(!self::allowed($id))throw new RuntimeException('forbidden');$url=self::public_url($id);$c=self::connection();$old=get_post_meta($id,self::META,true);$old=is_array($old)?$old:[];
        if(($old['inspection']['url']??'')===$url&&(int)($old['inspection']['checked_at']??0)>time()-300)return;
        $result=self::request('POST','https://searchconsole.googleapis.com/v1/urlInspection/index:inspect',['inspectionUrl'=>$url,'siteUrl'=>$c['gsc_property'],'languageCode'=>'pl-PL']);if(!is_array($result['value']))throw new RuntimeException('invalid_inspection');$inspection=self::parse_inspection($result['value'],$url);
        if(self::public_url($id)!==$url)throw new RuntimeException('url_changed');$latest=get_post_meta($id,self::META,true);self::record($id,array_merge(is_array($latest)?$latest:[],['inspection'=>$inspection,'inspection_error'=>'']));
    }
    public static function handle(string $action): void {
        $raw=$_POST['post_id']??null;$id=is_string($raw)&&ctype_digit($raw)?(int)$raw:0;
        if(!$id||($_SERVER['REQUEST_METHOD']??'')!=='POST'||!self::allowed($id)||!isset($_POST['_wpnonce'])||!is_string($_POST['_wpnonce'])||!wp_verify_nonce(wp_unslash($_POST['_wpnonce']),$action.'_'.$id))wp_die('Brak prawidłowego potwierdzenia operacji.','',['response'=>403]);
        $result='';try{if($action==='zp_article_google_submit'){self::enqueue($id);$result='queued';}elseif($action==='zp_article_google_inspect'){self::inspect($id);$result='checked';}else throw new RuntimeException('invalid_action');}catch(Throwable $e){$result='attention';$old=get_post_meta($id,self::META,true);$old=is_array($old)?$old:[];$safe=$e instanceof RuntimeException?$e->getMessage():'';if(!in_array($safe,['not_public','not_indexable','invalid_url','canonical_mismatch','needs_connection','google_401','google_403','google_429','google_500','google_502','google_503','google_504','google_transport','invalid_inspection','url_changed','busy','queue_limit','storage_failed','cron_failed'],true))$safe='operation_failed';update_post_meta($id,self::META,array_merge($old,['inspection_error'=>$safe,'at'=>time()]));}
        wp_safe_redirect(add_query_arg(['page'=>'zp-suite-prepare','zp_google_post'=>$id,'zp_google_result'=>$result],admin_url('admin.php')));exit;
    }
    public static function error_label(string $code): string {return ['needs_connection'=>'Sprawdź połączenie Google w Rank Math','sitemap_unavailable'=>'Mapa witryny nie zwróciła poprawnego dokumentu','google_401'=>'Połączenie Google wymaga ponownego zalogowania','google_403'=>'Google odmówił dostępu — sprawdź uprawnienia Search Console','google_429'=>'Osiągnięto limit Google — spróbuj później','google_transport'=>'Nie udało się potwierdzić odpowiedzi Google','invalid_inspection'=>'Google zwrócił niepełny wynik kontroli URL','not_public_or_changed'=>'Artykuł nie jest już publiczny pod zgłoszonym adresem','busy'=>'Trwa inne zgłoszenie','cron_failed'=>'Kolejka WordPress nie potwierdziła uruchomienia','queue_failed'=>'Nie zapisano automatycznego zgłoszenia','storage_failed'=>'WordPress nie potwierdził zapisu wyniku'][$code]??'Nie potwierdzono operacji — sprawdź połączenie i spróbuj ponownie';}
    public static function controls(int $id): void {
        if(!$id||!self::allowed($id))return;$p=get_post($id);if(!$p)return;
        if($p->post_status==='future'){echo '<p class="zpAdmin20__muted">Google: mapa zostanie zgłoszona automatycznie po publikacji.</p>';return;}
        try{$url=self::public_url($id);}catch(Throwable $ignored){return;}$d=get_post_meta($id,self::META,true);$d=is_array($d)?$d:[];
        echo '<details'.(((string)($_GET['zp_google_post']??''))===(string)$id?' open':'').'><summary>Indeksowanie w Google</summary><p class="zpAdmin20__muted">Zgłoszenie mapy pomaga Google odkryć artykuł. Nie oznacza zaindeksowania. Prośbę dla pojedynczego URL dokończ w Search Console.</p>';
        if(($d['url']??'')===$url&&($d['state']??'')==='sitemap_submitted')echo '<p>Mapa zgłoszona: '.esc_html(wp_date('d.m.Y H:i',(int)$d['submitted_at'],new DateTimeZone('Europe/Warsaw'))).' · HTTP '.(int)$d['http_code'].'.</p>';
        elseif(($d['state']??'')==='queued')echo '<p>Zgłoszenie mapy oczekuje w kolejce WordPress.</p>';
        elseif(($d['state']??'')==='needs_attention')echo '<p>Zgłoszenie wymaga sprawdzenia: '.esc_html(self::error_label((string)($d['error']??''))).'.</p>';
        $i=$d['inspection']??[];if(is_array($i)&&($i['url']??'')===$url&&isset($i['checked_at'])){echo '<p>Odczyt Google: '.esc_html(($i['verdict']??'')==='PASS'?'URL jest w indeksie Google':(($i['verdict']??'')==='VERDICT_UNSPECIFIED'?'Brak rozstrzygnięcia Google':'Google nie potwierdził obecności w indeksie')).' · '.esc_html(wp_date('d.m.Y H:i',(int)$i['checked_at'],new DateTimeZone('Europe/Warsaw'))).'</p><p>'.esc_html((string)($i['coverage']??'')).'</p>';if(!empty($i['last_crawl']))echo '<p>Ostatnie pobranie przez Google: '.esc_html($i['last_crawl']).'</p>';if(!empty($i['inspection_link']))echo '<a class="button" href="'.esc_url($i['inspection_link']).'" target="_blank" rel="noopener">Poproś o indeksowanie w Search Console ↗</a>';}
        if(!empty($d['inspection_error']))echo '<p>Kontrola URL wymaga sprawdzenia: '.esc_html(self::error_label((string)$d['inspection_error'])).'.</p>';
        foreach(['zp_article_google_submit'=>'Zgłoś mapę do Google','zp_article_google_inspect'=>'Sprawdź indeksowanie w Google'] as $action=>$label){echo '<form method="post" action="'.esc_url(admin_url('admin-post.php')).'"><input type="hidden" name="action" value="'.esc_attr($action).'"><input type="hidden" name="post_id" value="'.$id.'">';wp_nonce_field($action.'_'.$id);echo '<p><button class="button">'.esc_html($label).'</button></p></form>';}echo '</details>';
    }
    public static function recent(): void {
        if(!current_user_can('manage_options'))return;$posts=get_posts(['post_type'=>'post','post_status'=>'publish','numberposts'=>12,'orderby'=>'date','order'=>'DESC']);
        echo '<section class="zpAdmin20__card"><h2>Ostatnio opublikowane · Google</h2><p>Po każdej nowej publikacji, również zaplanowanej, automatycznie zgłaszamy aktualną mapę witryny przez połączenie Rank Math. Status indeksu pobieramy z Google po kliknięciu.</p>';
        foreach($posts as $p){try{$url=self::public_url((int)$p->ID);}catch(Throwable $ignored){continue;}echo '<div class="zpAdmin20__row"><h3><a target="_blank" rel="noopener" href="'.esc_url($url).'">'.esc_html($p->post_title).'</a></h3>';self::controls((int)$p->ID);echo '</div>';}echo '</section>';
    }
    public static function register(): void {add_action('transition_post_status',[self::class,'transition'],60,3);add_action(self::WORKER,[self::class,'worker']);foreach(['zp_article_google_submit','zp_article_google_inspect'] as $action)add_action('admin_post_'.$action,static function()use($action){self::handle($action);});}
}
function zp_article_google_render_recent(): void {ZP_Article_Google_20261010::recent();}
