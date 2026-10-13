<?php
/** Analysis input only. Does not write posts, Elementor data, metadata or scores. */
if (!defined('ABSPATH')) { exit; }

final class ZP_RankMath_Content_Bridge_20261011 {
    const VERSION = 'editor-main-v4-block-boundaries';
    const ACTION = 'zp_rankmath_rendered_content_20261011';
    const ORIGIN = 'https://zaprojektowani.com';
    const MAX_HTML = 4000000;
    const MAX_PROJECTION = 350000;
    private static $enqueued = false;

    public static function register(): void {
        add_action('admin_enqueue_scripts', [self::class, 'enqueue'], 100);
        add_action('enqueue_block_editor_assets', [self::class, 'block_enqueue'], 100);
        add_action('wp_ajax_'.self::ACTION, [self::class, 'ajax']);
        add_action('template_redirect', [self::class, 'probe_headers'], 0);
        // Campaign route exits at template_redirect/PHP_INT_MIN; wp runs after its native page query.
        add_action('wp', [self::class, 'campaign_probe_headers'], 0);
    }

    /** Exact public native pages observed in the immutable 59 audit. No client routes. */
    public static function pages(): array {
        return [665=>'/',1431=>'/wiedza/',5593=>'/agencja-reklamowa-katowice/',
            5561=>'/strony-internetowe-dla-salonow-beauty/',5562=>'/strony-internetowe-dla-trenerow-personalnych/',
            5560=>'/strony-internetowe-dla-restauracji/',5558=>'/strony-internetowe-dla-fotografow/',5559=>'/strony-internetowe-dla-hoteli/',
            5393=>'/tworzenie-landing-page/',5391=>'/strony-internetowe-dla-lekarzy/',5392=>'/strony-wordpress/',
            5390=>'/strony-internetowe-dla-kancelarii/',5388=>'/strona-wizytowka/',5389=>'/strony-internetowe-dla-deweloperow/',
            5387=>'/opieka-wordpress/',5375=>'/identyfikacja-wizualna/',5373=>'/tworzenie-sklepow-internetowych/',
            5374=>'/projektowanie-logo/',5372=>'/tworzenie-stron-internetowych/',2198=>'/logo-branding-katowice/',
            4266=>'/kampanie-reklamowe/',1006=>'/studio-wyceny/',2010=>'/o-nas/',1319=>'/sklepy-internetowe-katowice/',
            935=>'/kontakt/',744=>'/strony-internetowe-katowice/',1986=>'/najczesciej-zadawane-pytania/'];
    }

    public static function shortcode_stub(string $content): ?string {
        if (strlen($content)>12000) { return null; }
        // Native Kontakt/FAQ contain a trailing empty Gutenberg paragraph; never remove real text or arbitrary comments.
        $stub=preg_replace('~(?:\s*<!-- wp:paragraph -->\s*<p>\s*</p>\s*<!-- /wp:paragraph -->\s*)+$~D','',$content);
        $plain=trim(html_entity_decode(preg_replace('~</?p\s*>|<br\s*/?>~i','',$stub),ENT_QUOTES|ENT_HTML5,'UTF-8'));
        $tags=['zp_home_full','zp_home','zp_zaprojektowani_home','zp_wiedza_blog','zp_studio_wyceny','zp_studio_wyceny_cms',
            'zp_o_nas','zp_page_o_nas','zp_about_page','zp_contact_page','zp_kontakt','zp_faq_page','zp_page_faq',
            'zp_strony_internetowe_katowice','zp_page_strony_katowice','zp_page_strony_internetowe_katowice',
            'zp_sklepy_internetowe_katowice','zp_page_sklepy_katowice','zp_logo_branding_katowice','zp_page_logo_branding_katowice'];
        if (!preg_match('~^(?:\[(?:'.implode('|',$tags).')(?:\s+[^\[\]<>]*)?\]\s*)+$~D',$plain)) { return null; }
        return $plain;
    }

    /** One exact route renders from a PHP template and intentionally stores an empty body. */
    public static function routed_empty_page(int $id, string $content): bool {
        return $id===4266 && $content==='' && defined('ZP_SUITE_PATH')
            && function_exists('zp_seo_plan_active') && zp_seo_plan_active()
            && function_exists('zp_suite_campaigns_render_and_exit')
            && is_readable(ZP_SUITE_PATH.'includes/campaigns-page.php')
            && is_readable(ZP_SUITE_PATH.'templates/page-campaigns-route.php');
    }

    /** Normal requests return without headers; signed requests retain every probe check. */
    public static function campaign_probe_headers(): void {
        if (get_queried_object_id()===4266) { self::probe_headers(); }
    }

    public static function eligible(int $id): bool {
        $path=self::pages()[$id]??null;
        if ($path===null || (string)get_option('blog_public','1')!=='1') { return false; }
        if (preg_match('~^/(?:en/)?(?:oferty|briefy|wp-admin|wp-json)(?:/|$)~',$path)) { return false; }
        if (function_exists('zp_seo_plan_is_hidden_path') && zp_seo_plan_is_hidden_path($path)) { return false; }
        $excluded=apply_filters('zp_rankmath_content_bridge_excluded_paths',[]);
        if (!is_array($excluded) || in_array($path,$excluded,true)) { return false; }
        $p=get_post($id);
        if (!$p || $p->post_type!=='page' || $p->post_status!=='publish' || $p->post_password!=='') { return false; }
        if (get_permalink($id)!==self::ORIGIN.$path || home_url('/')!==self::ORIGIN.'/') { return false; }
        $robots=get_post_meta($id,'rank_math_robots',true);
        if ($robots!=='' && !is_array($robots) && !is_string($robots)) { return false; }
        foreach ((array)$robots as $r) {
            if (!is_string($r) || preg_match('~(?:^|[\s,:])noindex(?:$|[\s,])~i',$r)) { return false; }
        }
        $canonical=get_post_meta($id,'rank_math_canonical_url',true);
        if (!is_string($canonical) || ($canonical!=='' && $canonical!==self::ORIGIN.$path)) { return false; }
        return self::shortcode_stub((string)$p->post_content)!==null
            || self::routed_empty_page($id,(string)$p->post_content);
    }

    public static function fingerprint(int $id): string {
        $p=get_post($id); if (!$p) { return ''; }
        $fields=[];
        foreach (['ID','post_type','post_status','post_password','post_title','post_content','post_excerpt','post_name','post_parent',
            'post_date','post_date_gmt','post_modified','post_modified_gmt'] as $k) { $fields[$k]=$p->$k??null; }
        foreach (['_elementor_data','_elementor_page_settings','rank_math_title','rank_math_description','rank_math_focus_keyword',
            'rank_math_canonical_url','rank_math_robots','_thumbnail_id'] as $k) { $fields['meta'][$k]=get_post_meta($id,$k,true); }
        $fields['suite_version']=defined('ZP_SUITE_VERSION')?ZP_SUITE_VERSION:null;
        $fields['bridge_version']=self::VERSION;
        if (self::routed_empty_page($id,(string)$p->post_content)) {
            foreach (['includes/campaigns-page.php','templates/page-campaigns-route.php'] as $file) {
                $fields['route_source'][$file]=hash_file('sha256',ZP_SUITE_PATH.$file);
            }
        }
        $json=wp_json_encode($fields,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        return is_string($json)?hash('sha256',$json):'';
    }

    private static function signature(int $id,int $at,string $hash): string {
        return hash_hmac('sha256',self::VERSION.'|'.$id.'|'.$at.'|'.$hash,wp_salt('nonce'));
    }

    /** Evict only this allowed post's object caches; never trust pre-HTTP request-local copies. */
    private static function fresh_native(int $id): void {
        wp_cache_delete($id,'posts');
        wp_cache_delete($id,'post_meta');
    }

    /** Only an ephemeral signed, public, anonymous read can bypass caches and receive proof headers. */
    public static function probe_headers(): void {
        if (is_admin() || ($_SERVER['REQUEST_METHOD']??'')!=='GET') { return; }
        $keys=['zp_rm_probe','zp_rm_post','zp_rm_at','zp_rm_sig'];
        if (count($_GET)!==count($keys)) { return; }
        foreach ($keys as $k) { if (!isset($_GET[$k]) || !is_string($_GET[$k])) { return; } }
        if ($_GET['zp_rm_probe']!=='1' || !ctype_digit($_GET['zp_rm_post']) || !ctype_digit($_GET['zp_rm_at'])
            || !preg_match('/^[a-f0-9]{64}$/D',$_GET['zp_rm_sig'])) { return; }
        $id=(int)$_GET['zp_rm_post'];$at=(int)$_GET['zp_rm_at'];
        if ($at>time()+5 || $at<time()-45 || !self::eligible($id) || !is_page($id) || get_queried_object_id()!==$id) { return; }
        $uri=$_SERVER['REQUEST_URI']??'';
        if (!is_string($uri) || !str_starts_with($uri,'/') || str_starts_with($uri,'//')
            || wp_parse_url($uri,PHP_URL_PATH)!==self::pages()[$id]) { return; }
        $hash=self::fingerprint($id);
        if (!preg_match('/^[a-f0-9]{64}$/D',$hash) || !hash_equals(self::signature($id,$at,$hash),$_GET['zp_rm_sig'])) { return; }
        if (!defined('DONOTCACHEPAGE')) { define('DONOTCACHEPAGE',true); }
        nocache_headers();
        header('X-ZP-RM-Probe: '.$_GET['zp_rm_sig']);
        header('X-ZP-RM-Source: '.$hash);
    }

    public static function enqueue(string $hook): void {
        if (self::$enqueued || $hook!=='post.php' || !current_user_can('manage_options')) { return; }
        $screen=get_current_screen();
        if (!$screen || $screen->base!=='post' || $screen->post_type!=='page') { return; }
        $action=$_GET['action']??'edit';
        if (!is_string($action) || $action!=='edit') { return; }
        $raw=$_GET['post']??null;
        if (!is_string($raw) || !ctype_digit($raw)) { return; }
        $id=(int)$raw;
        if (!current_user_can('edit_post',$id) || !self::eligible($id) || !wp_script_is('rank-math-analyzer','registered')) { return; }
        $p=get_post($id);
        $handle='zp-rankmath-content-bridge-20261011';
        self::$enqueued=true;
        wp_enqueue_script($handle,plugins_url('../assets/rankmath-content-bridge-20261011.js',__FILE__),['wp-hooks','wp-data','wp-dom-ready','rank-math-analyzer'],self::VERSION,true);
        wp_add_inline_script($handle,'window.zpRankMathContentBridge='.wp_json_encode([
            'ajax'=>admin_url('admin-ajax.php'),'action'=>self::ACTION,'postId'=>$id,'nonce'=>wp_create_nonce(self::ACTION.'|'.$id),
            'sourceHash'=>self::fingerprint($id),'savedContent'=>(string)$p->post_content,'savedTitle'=>(string)$p->post_title,
            'editorMode'=>$screen->is_block_editor()?'block':'classic',
            'allowEmptyRoute'=>self::routed_empty_page($id,(string)$p->post_content),
        ],JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT).';','before');
    }

    public static function block_enqueue(): void { self::enqueue('post.php'); }

    public static function ajax(): void {
        if (($_SERVER['REQUEST_METHOD']??'')!=='POST' || !current_user_can('manage_options')) { wp_send_json_error(['code'=>'forbidden'],403); }
        $raw=$_POST['post_id']??null;$nonce=$_POST['nonce']??null;$expected=$_POST['source_hash']??null;
        if (!is_string($raw) || !ctype_digit($raw) || !is_string($nonce) || !is_string($expected) || !preg_match('/^[a-f0-9]{64}$/D',$expected)) {
            wp_send_json_error(['code'=>'invalid_request'],400);
        }
        $id=(int)$raw;
        if (!current_user_can('edit_post',$id) || !wp_verify_nonce($nonce,self::ACTION.'|'.$id)) { wp_send_json_error(['code'=>'forbidden'],403); }
        if (!isset(self::pages()[$id])) { wp_send_json_error(['code'=>'excluded'],409); }
        self::fresh_native($id);
        if (!self::eligible($id)) { wp_send_json_error(['code'=>'excluded'],409); }
        $hash=self::fingerprint($id);
        if (!hash_equals($hash,$expected)) { wp_send_json_error(['code'=>'source_changed'],409); }
        $at=time();$sig=self::signature($id,$at,$hash);
        $url=add_query_arg(['zp_rm_probe'=>'1','zp_rm_post'=>(string)$id,'zp_rm_at'=>(string)$at,'zp_rm_sig'=>$sig],self::ORIGIN.self::pages()[$id]);
        $response=wp_safe_remote_get($url,['timeout'=>12,'redirection'=>0,'limit_response_size'=>self::MAX_HTML+1,
            'sslverify'=>true,'reject_unsafe_urls'=>true,'cookies'=>[],
            'headers'=>['Accept'=>'text/html','Accept-Language'=>'pl-PL,pl;q=0.9','Cache-Control'=>'no-cache']]);
        if (is_wp_error($response) || wp_remote_retrieve_response_code($response)!==200) { wp_send_json_error(['code'=>'public_read_failed'],502); }
        self::fresh_native($id);
        if (!current_user_can('manage_options') || !current_user_can('edit_post',$id) || !self::eligible($id)
            || !hash_equals($hash,self::fingerprint($id))) { wp_send_json_error(['code'=>'source_changed'],409); }
        if (wp_remote_retrieve_header($response,'x-zp-rm-probe')!==$sig || wp_remote_retrieve_header($response,'x-zp-rm-source')!==$hash) {
            wp_send_json_error(['code'=>'unverified_response'],502);
        }
        // Preserve actual HTTP robot restrictions. No artificial probe noindex can mask a hosting restriction.
        $httpRobots=wp_remote_retrieve_header($response,'x-robots-tag');
        if (is_array($httpRobots)) { $httpRobots=implode(',',$httpRobots); }
        if (!is_string($httpRobots) || preg_match('/(?:^|[\s,:])noindex(?:$|[\s,])/i',$httpRobots)) {
            wp_send_json_error(['code'=>'excluded_response'],409);
        }
        if (!preg_match('~^text/html(?:;|$)~i',wp_remote_retrieve_header($response,'content-type'))) {
            wp_send_json_error(['code'=>'unverified_response'],502);
        }
        $body=wp_remote_retrieve_body($response);
        try { $projection=self::projection($body,self::pages()[$id]); }
        catch (Throwable $e) { wp_send_json_error(['code'=>'projection_unavailable'],502); }
        wp_send_json_success(['postId'=>$id,'sourceHash'=>$hash,'projectionHash'=>hash('sha256',$projection),'html'=>$projection,
            'wordCount'=>count(preg_split('/\s+/u',trim(strip_tags($projection)),-1,PREG_SPLIT_NO_EMPTY)),'version'=>self::VERSION]);
    }

    public static function projection(string $html,string $path): string {
        if (!class_exists('DOMDocument') || strlen($html)>self::MAX_HTML || !str_contains(strtolower($html),'</html')) { throw new RuntimeException('invalid_html'); }
        $old=libxml_use_internal_errors(true);
        try {
            $dom=new DOMDocument();
            if (!$dom->loadHTML('<?xml encoding="UTF-8">'.$html,LIBXML_NONET|LIBXML_NOERROR|LIBXML_NOWARNING)) { throw new RuntimeException('invalid_html'); }
            $xp=new DOMXPath($dom);
            $canon=$xp->query('/html/head/link[contains(concat(" ",normalize-space(@rel)," ")," canonical ")]');
            if ($canon->length!==1 || $canon->item(0)->getAttribute('href')!==self::ORIGIN.$path) { throw new RuntimeException('canonical'); }
            $lang=$dom->documentElement->getAttribute('lang');
            if (!preg_match('/^pl(?:-|$)/i',$lang)) { throw new RuntimeException('language'); }
            foreach ($xp->query('/html/head/meta') as $m) {
                if (in_array(strtolower($m->getAttribute('name')),['robots','googlebot','bingbot'],true)
                    && preg_match('/(?:^|[\s,])noindex(?:$|[\s,])/i',$m->getAttribute('content'))) { throw new RuntimeException('noindex'); }
            }
            $expr='//main';
            if ($path==='/kontakt/') { $expr='//*[contains(concat(" ",normalize-space(@class)," ")," zpContactPageShortcode ")]'; }
            elseif ($path==='/o-nas/') { $expr='//*[@id="zpAboutX"]'; }
            elseif ($path==='/najczesciej-zadawane-pytania/') { $expr='//*[contains(concat(" ",normalize-space(@class)," ")," zpFaqPage ")]'; }
            elseif ($path==='/studio-wyceny/') { $expr='//*[@id="zpbsUltimate"]'; }
            elseif ($path==='/wiedza/') { $expr='//*[@id="zhHero"]|//*[@id="zpKBIndex"]'; }
            $roots=$xp->query($expr);
            if ($roots->length<1 || $roots->length>2 || ($path!=='/wiedza/' && $roots->length!==1)) { throw new RuntimeException('main_scope'); }
            $out='';foreach ($roots as $root) { $out.=self::project_node($root); }
            if (strlen($out)>self::MAX_PROJECTION || mb_strlen(trim(strip_tags($out)))<300 || !str_contains($out,'<h1>')) { throw new RuntimeException('insufficient_main'); }
            return $out;
        } finally { libxml_clear_errors();libxml_use_internal_errors($old); }
    }

    private static function project_node(DOMNode $node): string {
        if ($node instanceof DOMText) { return htmlspecialchars($node->textContent,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8'); }
        if (!$node instanceof DOMElement) { return ''; }
        $tag=strtolower($node->tagName);
        if (in_array($tag,['script','style','template','noscript','svg','canvas','iframe','input','select','textarea','nav','aside','footer','form'],true)
            || $node->hasAttribute('hidden') || strtolower($node->getAttribute('aria-hidden'))==='true'
            || preg_match('/(?:^|\s)(?:zh__titleGhost|wp-admin-bar|screen-reader-text)(?:\s|$)/',$node->getAttribute('class'))
            || preg_match('/(?:display\s*:\s*none|visibility\s*:\s*hidden)/i',$node->getAttribute('style'))) { return ''; }
        if ($tag==='img') {
            $src=$node->getAttribute('src');$alt=$node->getAttribute('alt');
            if (!preg_match('~^(?:https?://|/(?!/))~i',$src)) { return ''; }
            return '<img src="'.htmlspecialchars($src,ENT_QUOTES,'UTF-8').'" alt="'.htmlspecialchars($alt,ENT_QUOTES,'UTF-8').'">';
        }
        $content='';foreach ($node->childNodes as $child) { $content.=self::project_node($child); }
        if ($tag==='a') {
            $href=$node->getAttribute('href');
            if (!preg_match('~^(?:https?://|/(?!/)|#|mailto:|tel:)~i',$href)) { return $content; }
            $rel=$node->hasAttribute('rel')?' rel="'.htmlspecialchars($node->getAttribute('rel'),ENT_QUOTES,'UTF-8').'"':'';
            return '<a href="'.htmlspecialchars($href,ENT_QUOTES,'UTF-8').'"'.$rel.'>'.$content.'</a>';
        }
        if ($tag==='br') { return '<br>'; }
        if (in_array($tag,['h1','h2','h3','h4','h5','h6','p','ul','ol','li','table','caption','thead','tbody','tr','td','th','strong','em','blockquote','dl','dt','dd'],true)) {
            $sep=in_array($tag,['strong','em'],true)?'':"\n";
            return $sep.'<'.$tag.'>'.$content.'</'.$tag.'>'.$sep;
        }
        return in_array($tag,['div','section','main','header','article','figure','figcaption','details','summary'],true)?"\n<".$tag.">".$content."</".$tag.">\n":$content;
    }
}

/** RankMath Tools analysis input only. No content, focus, metadata or score writes. */
if (!defined('ABSPATH')) { exit; }

final class ZP_RankMath_Tools_Content_Bridge_20261012 {
    private static $active = false;
    private static $accepted = [];
    private static $failure = '';

    public static function register(): void {
        // Bracket RankMath\Tools\Update_Score::update_seo_score at its official priority 10.
        add_filter('rank_math/tools/update_seo_score', [self::class, 'begin'], 1, 2);
        add_filter('rank_math/recalculate_score/data', [self::class, 'input'], PHP_INT_MAX, 2);
        add_filter('rank_math/tools/update_seo_score', [self::class, 'finish'], PHP_INT_MAX, 2);
        add_filter('rank_math/recalculate_scores_batch_size', [self::class, 'batch_size']);
    }

    /** Native Tools JS uses this same size for offsets; at most one public read per batch. */
    public static function batch_size($size) {
        if (!current_user_can('manage_options')) { return $size; }
        if (is_admin() && ($_SERVER['REQUEST_METHOD'] ?? '') === 'GET'
            && isset($_GET['page']) && $_GET['page'] === 'rank-math-status') { return 1; }
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        $path = is_string($uri) ? wp_parse_url($uri, PHP_URL_PATH) : null;
        $route = $_GET['rest_route'] ?? null;
        if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST'
            && ($path === '/wp-json/rankmath/v1/toolsAction' || $route === '/rankmath/v1/toolsAction')
            && isset($_POST['action']) && $_POST['action'] === 'update_seo_score') { return 1; }
        return $size;
    }

    private static function request($request): bool {
        return $request instanceof WP_REST_Request && $request->get_method() === 'POST'
            && $request->get_route() === '/rankmath/v1/toolsAction'
            && $request->get_param('action') === 'update_seo_score'
            && current_user_can('manage_options') && class_exists('ZP_RankMath_Content_Bridge_20261011');
    }

    public static function begin($message, $request = null) {
        self::$active = self::request($request);
        self::$accepted = [];
        self::$failure = '';
        return $message;
    }

    /** Exclude native and Suite-owned effective noindex/private routes before any read. */
    private static function public_post(int $id): bool {
        if (!current_user_can('manage_options') || !current_user_can('edit_post', $id)
            || (string)get_option('blog_public', '1') !== '1'
            || home_url('/') !== ZP_RankMath_Content_Bridge_20261011::ORIGIN.'/') { return false; }
        $post = get_post($id);
        if (!$post || !in_array($post->post_type, ['page', 'post'], true)
            || $post->post_status !== 'publish' || $post->post_password !== '') { return false; }
        if (preg_match('~\[(?:zp_offer|zp_brief|zaprojektowani_offer|zaprojektowani_brief)\b~i', (string)$post->post_content)) { return false; }
        $url = get_permalink($id);
        if (!is_string($url)) { return false; }
        $path = wp_parse_url($url, PHP_URL_PATH);
        if (!is_string($path) || !str_starts_with($path, '/') || str_starts_with($path, '//')
            || $url !== ZP_RankMath_Content_Bridge_20261011::ORIGIN.$path
            || preg_match('~^/(?:en(?:/|$)|oferty(?:/|$)|briefy(?:/|$)|wp-admin(?:/|$)|wp-json(?:/|$))~', $path)
            || !function_exists('zp_seo_plan_is_hidden_path') || zp_seo_plan_is_hidden_path($path)) { return false; }
        $excluded = apply_filters('zp_rankmath_content_bridge_excluded_paths', []);
        if (!is_array($excluded) || in_array($path, $excluded, true)) { return false; }
        $robots = get_post_meta($id, 'rank_math_robots', true);
        if (!is_array($robots) && !is_string($robots)) { return false; }
        foreach ((array)$robots as $value) {
            if (!is_string($value) || preg_match('~(?:^|[\s,:])(?:noindex|none)(?:$|[\s,])~i', $value)) { return false; }
        }
        $canonical = get_post_meta($id, 'rank_math_canonical_url', true);
        $focus = get_post_meta($id, 'rank_math_focus_keyword', true);
        return is_string($canonical) && ($canonical === '' || $canonical === $url)
            && is_string($focus) && trim($focus) !== '';
    }

    public static function input($values, $rawId) {
        if (!self::$active) { return $values; }
        if (!is_int($rawId) || $rawId <= 0 || !is_array($values)) { return $values; }
        $id = $rawId;
        wp_cache_delete($id, 'posts'); wp_cache_delete($id, 'post_meta');
        if (!self::public_post($id) || !isset($values['content']) || !is_string($values['content'])) { return $values; }
        $post = get_post($id);
        $bridge = ZP_RankMath_Content_Bridge_20261011::class;
        if ($bridge::shortcode_stub((string)$post->post_content) !== null
            || $bridge::routed_empty_page($id,(string)$post->post_content)) {
            if (!$bridge::eligible($id)) { return $values; }
            try { $values['content'] = self::projection($id); }
            catch (Throwable $error) {
                $code = $error->getMessage();
                // A genuine effective noindex is excluded, not scored or reported as a read failure.
                if (!in_array($code, ['excluded_response', 'noindex'], true)) {
                    self::$failure = in_array($code, ['public_read_failed', 'source_changed', 'unverified_response'], true)
                        ? $code : 'projection_unavailable';
                }
                return $values;
            }
        } elseif (trim(strip_tags($values['content'])) === ''
            || preg_match('~^\s*\[[A-Za-z_][A-Za-z0-9_-]*(?:\s+[^\[\]<>]*)?\]\s*$~D', strip_tags((string)$post->post_content))) {
            // Never score an unrendered/unknown shortcode or empty native content.
            return $values;
        }
        self::$accepted[$id] = $values;
        return $values;
    }

    /** Identical signed proof and projection contract to the existing editor bridge. */
    private static function projection(int $id): string {
        $bridge = ZP_RankMath_Content_Bridge_20261011::class;
        $hash = $bridge::fingerprint($id); $at = time();
        $sig = hash_hmac('sha256', $bridge::VERSION.'|'.$id.'|'.$at.'|'.$hash, wp_salt('nonce'));
        $path = $bridge::pages()[$id];
        $url = add_query_arg(['zp_rm_probe'=>'1', 'zp_rm_post'=>(string)$id,
            'zp_rm_at'=>(string)$at, 'zp_rm_sig'=>$sig], $bridge::ORIGIN.$path);
        $response = wp_safe_remote_get($url, ['timeout'=>12, 'redirection'=>0,
            'limit_response_size'=>$bridge::MAX_HTML+1, 'sslverify'=>true, 'reject_unsafe_urls'=>true,
            'cookies'=>[], 'headers'=>['Accept'=>'text/html', 'Accept-Language'=>'pl-PL,pl;q=0.9', 'Cache-Control'=>'no-cache']]);
        if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) { throw new RuntimeException('public_read_failed'); }
        wp_cache_delete($id, 'posts'); wp_cache_delete($id, 'post_meta');
        if (!self::public_post($id) || !$bridge::eligible($id) || !hash_equals($hash, $bridge::fingerprint($id))) {
            throw new RuntimeException('source_changed');
        }
        if (wp_remote_retrieve_header($response, 'x-zp-rm-probe') !== $sig
            || wp_remote_retrieve_header($response, 'x-zp-rm-source') !== $hash
            || !preg_match('~^text/html(?:;|$)~i', wp_remote_retrieve_header($response, 'content-type'))) {
            throw new RuntimeException('unverified_response');
        }
        $robots = wp_remote_retrieve_header($response, 'x-robots-tag');
        if (is_array($robots)) { $robots = implode(',', $robots); }
        if (!is_string($robots) || preg_match('~(?:^|[\s,:])(?:noindex|none)(?:$|[\s,])~i', $robots)) {
            throw new RuntimeException('excluded_response');
        }
        $body = wp_remote_retrieve_body($response);
        $projection = $bridge::projection($body, $path);
        // The inherited parser already validates noindex; the equivalent "none" also excludes.
        $old = libxml_use_internal_errors(true);
        try {
            $dom = new DOMDocument(); $dom->loadHTML('<?xml encoding="UTF-8">'.$body, LIBXML_NONET|LIBXML_NOERROR|LIBXML_NOWARNING);
            foreach ((new DOMXPath($dom))->query('/html/head/meta') as $meta) {
                if (in_array(strtolower($meta->getAttribute('name')), ['robots', 'googlebot', 'bingbot'], true)
                    && preg_match('~(?:^|[\s,:])none(?:$|[\s,])~i', $meta->getAttribute('content'))) { throw new RuntimeException('excluded_response'); }
            }
        } finally { libxml_clear_errors(); libxml_use_internal_errors($old); }
        return $projection;
    }

    /** No empty fake score: official JS advances offsets for its 'complete' sentinel too. */
    public static function finish($data, $request = null) {
        if (!self::$active) { self::$accepted = []; return $data; }
        if (!self::request($request)) { self::$active = false; self::$accepted = []; return new WP_Error('zp_rm_forbidden', 'Przeliczanie zatrzymane: uprawnienia administratora uległy zmianie.', ['status'=>403]); }
        if (is_wp_error($data)) { self::$active = false; self::$accepted = []; self::$failure = ''; return $data; }
        if (!is_array($data) && $data !== 'complete') {
            self::$active = false; self::$accepted = []; self::$failure = '';
            return new WP_Error('zp_rm_unsupported_response', 'Przeliczanie zatrzymane: nieobsługiwana odpowiedź RankMath. Wcześniejsze wyniki zachowano.', ['status'=>409]);
        }
        if (self::$failure !== '') {
            $code = self::$failure; self::$active = false; self::$accepted = []; self::$failure = '';
            return new WP_Error('zp_rm_'.$code, 'Przeliczanie zatrzymane: nie potwierdzono aktualnej publicznej treści strony. Wynik nie został nadpisany.', ['status'=>409]);
        }
        $out = [];
        if (is_array($data)) {
            foreach (self::$accepted as $id => $values) {
                if (isset($data[$id]) && $data[$id] === $values && self::public_post($id)) { $out[$id] = $values; }
            }
        }
        self::$active = false; self::$accepted = [];
        return $out !== [] ? $out : 'complete';
    }
}

ZP_RankMath_Tools_Content_Bridge_20261012::register();

/** Explicit admin-only raw inventory. No source body, public rendering or metadata is changed. */
final class ZP_RankMath_Raw_Inventory_20261012 {
    public const FLAG='zp_rm_raw_inventory';
    private const KEYS=['rank_math_focus_keyword','rank_math_title','rank_math_description','rank_math_robots'];
    private static function screen(): bool {
        return is_admin()&&($_SERVER['REQUEST_METHOD']??'')==='GET'&&current_user_can('manage_options')
            &&($_GET['page']??null)==='rank-math-status'&&($_GET['view']??null)==='tools'&&($_GET[self::FLAG]??null)==='1'
            &&!(function_exists('wp_doing_ajax')&&wp_doing_ajax())&&!(defined('REST_REQUEST')&&REST_REQUEST)&&!(function_exists('wp_doing_cron')&&wp_doing_cron());
    }
    private static function robots_ok(array $raw): bool {
        foreach ($raw as $value) {
            if (is_string($value)) $tokens=preg_split('/[\s,;]+/u',strtolower($value),-1,PREG_SPLIT_NO_EMPTY);
            elseif (is_array($value)) $tokens=$value;else return false;
            foreach ($tokens as $token) if (!is_string($token)||preg_match('/(?:^|[\s,:;])(?:noindex|none)(?:$|[\s,;])/i',$token)===1) return false;
        }return true;
    }
    private static function identity(object $p,string $url): array {
        return ['ID'=>(int)$p->ID,'type'=>(string)$p->post_type,'status'=>(string)$p->post_status,'password'=>(string)$p->post_password,'title'=>(string)$p->post_title,'modified'=>(string)$p->post_modified,'modified_gmt'=>(string)$p->post_modified_gmt,'permalink'=>$url];
    }
    public static function rows(): array {
        if (!self::screen()) return [];
        $query=new WP_Query(['post_type'=>['page','post'],'post_status'=>'publish','has_password'=>false,'post__not_in'=>[5514,1493],'fields'=>'ids','posts_per_page'=>-1,'orderby'=>'ID','order'=>'ASC','ignore_sticky_posts'=>true,'no_found_rows'=>true,'suppress_filters'=>true,'update_post_meta_cache'=>false,'update_post_term_cache'=>false]);
        $home=wp_parse_url(home_url('/'));$rows=[];
        foreach ($query->posts as $value) {
            if (!is_int($value)&&!(is_string($value)&&ctype_digit($value))) continue;$id=(int)$value;if ($id<=0||in_array($id,[5514,1493],true)||isset($rows[$id])) continue;
            clean_post_cache($id);wp_cache_delete($id,'post_meta');$p=get_post($id);
            if (!$p||($p->ID??null)!==$id||!in_array($p->post_type,['post','page'],true)||$p->post_status!=='publish'||(string)$p->post_password!=='') continue;
            $url=get_permalink($id);$u=is_string($url)?wp_parse_url($url):false;$path=$u['path']??'';
            if (!is_array($u)||($u['scheme']??null)!==($home['scheme']??null)||($u['host']??null)!==($home['host']??null)||($u['port']??null)!==($home['port']??null)||!is_string($path)||$path===''||isset($u['query'])||isset($u['fragment'])||preg_match('~^/(?:en/)?(?:oferty|briefy|offers|briefs)(?:/|$)~i',$path)) continue;
            if (function_exists('zp_seo_plan_is_private_path')&&zp_seo_plan_is_private_path($path)) continue;
            $robots=get_post_meta($id,'rank_math_robots',false);if (!is_array($robots)||!self::robots_ok($robots)) continue;
            $meta=['rank_math_robots'=>$robots];foreach (self::KEYS as $key) if ($key!=='rank_math_robots') $meta[$key]=get_post_meta($id,$key,false);$before=self::identity($p,$url);
            // Each displayed row is a stable reread, rather than a mixture of concurrent edits.
            clean_post_cache($id);wp_cache_delete($id,'post_meta');$fresh=get_post($id);if (!$fresh||self::identity($fresh,(string)get_permalink($id))!==$before) continue;
            $same=true;foreach (self::KEYS as $key) if (get_post_meta($id,$key,false)!==$meta[$key]) { $same=false;break; }if (!$same||!self::screen()) continue;
            ksort($meta,SORT_STRING);$row=$before+['meta'=>$meta];$row['checksum']=hash('sha256',(string)wp_json_encode($row,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));$rows[$id]=$row;
        }return array_values($rows);
    }
    public static function render(): void {
        if (!self::screen()) return;$rows=self::rows();if (!self::screen()) return;
        echo '<section id="zpRankMathRawInventory"><h2>Surowe pola Rank Math — odczyt publicznych stron i wpisów</h2><p>Wiersze: '.count($rows).'. Pełne wartości meta pokazano jako tablice JSON: [] oznacza brak pola; [""] oznacza zapisany pusty tekst. Checksum obejmuje tożsamość, daty i surowe pola, bez treści strony. Ten widok niczego nie zapisuje.</p><div style="overflow-x:auto"><table class="widefat striped"><thead><tr>';
        foreach (['ID','Typ','Natywny tytuł','Permalink','Zmieniono / GMT','Pełny focus_keyword CSV','Surowy SEO title','Surowy SEO description','Surowe robots','Checksum pól'] as $h) echo '<th>'.esc_html($h).'</th>';echo '</tr></thead><tbody>';
        foreach ($rows as $r) {
            echo '<tr data-post-id="'.(int)$r['ID'].'"><td>'.(int)$r['ID'].'</td><td>'.esc_html($r['type']).'</td><td>'.esc_html($r['title']).'</td><td>'.esc_html($r['permalink']).'</td><td>'.esc_html($r['modified'].' / '.$r['modified_gmt']).'</td>';
            foreach (self::KEYS as $key) echo '<td><pre style="white-space:pre-wrap;min-width:180px">'.esc_html((string)wp_json_encode($r['meta'][$key],JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)).'</pre></td>';echo '<td>'.esc_html($r['checksum']).'</td></tr>';
        }echo '</tbody></table></div></section>';
    }
    public static function register(): void { add_action('admin_footer',[self::class,'render']); }
}
ZP_RankMath_Raw_Inventory_20261012::register();
