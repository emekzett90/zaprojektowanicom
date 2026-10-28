<?php
/** Native case-study routes. Existing posts, permalink pins and translator state remain owned by their original modules. */
if (!defined('ABSPATH')) { exit; }

final class ZP_Case_Pages_20261018 {
    public const STATE = 'zp_case_pages_20261018';
    public const MARKER = '_zp_case_pages_20261018';
    private const LOCK = 'zp_case_pages_20261018_lock';
    private const ERROR = 'zp_case_pages_20261018_error';
    private static $prepared = [];
    private static $pairs = [];

    public static function data(): array { return function_exists('zp_cs18_data') ? zp_cs18_data() : []; }

    /** Router boots on plugins_loaded, before WordPress creates its rewrite object.
     * Native ID/owner/slug/status checks still apply then; full permalink verification
     * becomes available before any main-query template, schema or header is selected.
     */
    private static function permalink_matches(int $id, string $path): bool {
        if (!isset($GLOBALS['wp_rewrite']) || !is_object($GLOBALS['wp_rewrite'])) { return true; }
        return get_permalink($id) === home_url($path);
    }

    /** Native identity checks work before the main query and never change the four posts. */
    public static function registry(): array {
        $data = self::data(); $out = [];
        $state = class_exists('ZP_Case_Studies_20261015') ? ZP_Case_Studies_20261015::state() : [];
        foreach (['siemianowski'=>'siemianowski', 'apartament-piekna'=>'apartament', 'altiora-linea'=>'altiora', 'swiat-grilli'=>'grilli'] as $key=>$owner) {
            if (!isset($data[$key]['path'])) { continue; }
            $id = 0;
            if (in_array($owner, ['siemianowski','apartament'], true)) {
                $id = (int) ($state['rows'][$owner]['id'] ?? 0);
                if (!$id || get_post_meta($id, '_zp_case_study_20261015', true) !== $owner) { continue; }
            } elseif ($owner === 'altiora') {
                $id = class_exists('ZP_Altiora_Article_3236') ? ZP_Altiora_Article_3236::post_id() : 0;
                if (!$id || get_post_meta($id, '_zp_altiora_article_3236', true) !== '1') { continue; }
            } else { $id = 4551; }
            $post = get_post($id);
            if (!$post instanceof WP_Post || $post->post_type !== 'post' || $post->post_status !== 'publish'
                || $post->post_password !== '' || $post->post_name !== basename($data[$key]['path'])
                || !self::permalink_matches($id, $data[$key]['path'])) { continue; }
            $canonical = get_post_meta($id, 'rank_math_canonical_url', true);
            if (is_string($canonical) && $canonical !== '' && $canonical !== home_url($data[$key]['path'])) { continue; }
            $out[$key] = ['id'=>$id, 'path'=>$data[$key]['path']];
        }
        return $out;
    }

    public static function index_id(bool $published = true): int {
        $state = get_option(self::STATE, []); $id = is_array($state) ? (int) ($state['id'] ?? 0) : 0;
        $post = $id ? get_post($id) : null;
        if (!$post instanceof WP_Post || $post->post_type !== 'page' || $post->post_name !== 'case-studies'
            || (int)$post->post_parent !== 0 || $post->post_password !== ''
            || get_post_meta($id, self::MARKER, true) !== 'index') { return 0; }
        if ($published && ($post->post_status !== 'publish' || !self::permalink_matches($id, '/case-studies/'))) { return 0; }
        return $id;
    }

    public static function index_url(): string {
        if (!self::index_id()) { return ''; }
        $url = home_url('/case-studies/');
        return function_exists('zpl_url') ? zpl_url($url) : $url;
    }

    public static function current_key(): string {
        if (is_admin() || wp_doing_ajax() || is_feed() || is_preview() || is_404()) { return ''; }
        // Detail breadcrumbs require the same verified hub as the native header link.
        // Until migration succeeds (or if the owner withdraws it), keep the original pages.
        $index=self::index_id(); if(!$index) { return ''; }
        $id = (int) get_queried_object_id();
        if ($id && is_page($id) && $id === $index) { return 'index'; }
        if (!is_singular('post')) { return ''; }
        foreach (self::registry() as $key=>$row) { if ($id === $row['id']) { return $key; } }
        return '';
    }

    /** A secondary query leaves the native page/post main query intact. */
    public static function index_posts(): array {
        $ids = array_column(self::registry(), 'id');
        if (!$ids) { return []; }
        $query = new WP_Query(['post_type'=>'post', 'post_status'=>'publish', 'post__in'=>$ids,
            'orderby'=>'post__in', 'posts_per_page'=>4, 'ignore_sticky_posts'=>true, 'no_found_rows'=>true, 'has_password'=>false]);
        return is_array($query->posts) ? $query->posts : [];
    }

    public static function routes($routes): array {
        $routes = is_array($routes) ? $routes : []; $data = self::data();
        $rows = self::registry();
        if (self::index_id()) { $rows = ['index'=>['path'=>'/case-studies/']] + $rows; }
        foreach ($rows as $key=>$row) {
            $en = $data[$key]['en_path'] ?? ''; $path = $row['path'];
            if ($en === '' || isset($routes[$path]) || in_array($en, $routes, true)) { continue; }
            $routes[$path] = $en;
        }
        return $routes;
    }

    private static function owned_key(string $source): string {
        if ($source === '/case-studies/' && self::index_id()) { return 'index'; }
        foreach (self::registry() as $key=>$row) { if ($row['path'] === $source) { return $key; } }
        return '';
    }

    public static function has_page($has, $source): bool {
        return (bool)$has || (is_string($source) && self::owned_key($source) !== '' && class_exists('ZPL\\Page'));
    }

    public static function sitemap_pages($pages): array {
        $pages = is_array($pages) ? $pages : [];
        foreach (self::registry() as $row) { $pages[]=$row['path']; }
        if (self::index_id()) { $pages[]='/case-studies/'; }
        return array_values(array_unique($pages));
    }

    /** Build identity units before wp_head/Runtime can cache this page's dictionary; never render a form. */
    public static function prepare(string $key): void {
        if (isset(self::$prepared[$key]) || !isset(self::data()[$key]) || !function_exists('zp_cs18_render') || !class_exists('ZPL\\Page')) { return; }
        self::$prepared[$key] = ['pl'=>zp_cs18_render($key, 'pl', false), 'en'=>zp_cs18_render($key, 'en', false)];
        self::$pairs[$key] = self::paired_units(self::$prepared[$key]['pl'], self::$prepared[$key]['en']);
    }

    /** Pair only structurally matching semantic elements, never zip two whole-page unit lists. */
    public static function paired_units(string $pl, string $en): array {
        if (!class_exists('DOMDocument') || !class_exists('ZPL\\Page')) { return []; }
        $load = static function(string $html): DOMDocument {
            $dom = new DOMDocument('1.0','UTF-8'); $before=libxml_use_internal_errors(true);
            $dom->loadHTML('<?xml encoding="UTF-8"><div id="zpCS18Pairs">'.$html.'</div>', LIBXML_HTML_NOIMPLIED|LIBXML_HTML_NODEFDTD);
            libxml_clear_errors(); libxml_use_internal_errors($before); return $dom;
        };
        $a=$load($pl); $b=$load($en); $candidates=[];
        $children=static function(DOMNode $node): array { $out=[]; foreach($node->childNodes as $child) { if ($child instanceof DOMElement) { $out[]=$child; } } return $out; };
        $shape=static function(DOMNode $node) use (&$shape): string {
            $out=$node->nodeName;
            foreach($node->childNodes as $child) { if ($child instanceof DOMElement) { $out.='('.$shape($child).')'; } elseif(trim($child->textContent)!=='') { $out.='#'; } }
            return $out;
        };
        $walk=static function(DOMElement $x, DOMElement $y) use (&$walk,$a,$b,$children,$shape,&$candidates): void {
            if ($x->tagName!==$y->tagName || $x->getAttribute('id')!==$y->getAttribute('id')) { return; }
            // The shared parser only walks document body content, not bare fragments.
            $left=\ZPL\Page::extract('<body>'.$a->saveHTML($x).'</body>'); $right=\ZPL\Page::extract('<body>'.$b->saveHTML($y).'</body>');
            if (count($left)===1 && count($right)===1 && $left[0]['kind']===$right[0]['kind'] && $shape($x)===$shape($y)) {
                $candidates[]=[(string)$left[0]['k'],(string)$right[0]['k']]; return;
            }
            $xc=$children($x); $yc=$children($y);
            if (count($xc)!==count($yc)) { return; }
            foreach($xc as $i=>$child) { $walk($child,$yc[$i]); }
        };
        $x=$a->getElementById('zpCS18Pairs'); $y=$b->getElementById('zpCS18Pairs');
        if ($x instanceof DOMElement && $y instanceof DOMElement) { $walk($x,$y); }
        $forward=[]; $reverse=[];
        foreach($candidates as [$plkey,$enkey]) { $forward[$plkey][$enkey]=true; $reverse[$enkey][$plkey]=true; }
        $out=[];
        foreach($forward as $plkey=>$values) { $enkey=(string)array_key_first($values); if(count($values)===1 && count($reverse[$enkey])===1) { $out[$plkey]=$enkey; } }
        return $out;
    }

    public static function dictionary($layers, $lang, $source): array {
        $layers=is_array($layers)?$layers:[];
        if (!in_array($lang,['pl','en'],true) || !is_string($source) || !class_exists('ZPL\\Page')) { return $layers; }
        $key=self::owned_key($source); if ($key==='') { return $layers; }
        self::prepare($key); $data=self::data()[$key]; $map=['Case studies'=>'Case studies']+(self::$pairs[$key]??[]);
        foreach(['title','seo_title','description'] as $field) {
            $pl=\ZPL\Html::norm((string)($data['pl'][$field]??'')); $en=\ZPL\Html::norm((string)($data['en'][$field]??''));
            if($pl!=='' && $en!=='') { $map[$pl]=$en; foreach(['title','meta'] as $kind) { $map[\ZPL\Page::variant($kind,$pl)]=$en; } }
        }
        if($lang==='pl') {
            $inverse=[]; $ambiguous=[];
            foreach($map as $pl=>$en) { $plain=\ZPL\Page::plain($pl); if(isset($inverse[$en]) && $inverse[$en]!==$plain) { $ambiguous[$en]=true; } else { $inverse[$en]=$plain; } }
            foreach($ambiguous as $en=>$_) { unset($inverse[$en]); }
            $map=$inverse;
        } else {
            foreach(\ZPL\Page::extract('<body>'.(self::$prepared[$key]['en']??'').'</body>') as $unit) { $map[$unit['k']]=$unit['k']; }
            foreach(['title','seo_title','description'] as $field) { $en=\ZPL\Html::norm((string)($data['en'][$field]??'')); if($en!=='') { $map[$en]=$en; foreach(['title','meta'] as $kind) { $map[\ZPL\Page::variant($kind,$en)]=$en; } } }
        }
        if($map) { array_unshift($layers,$map); } return $layers;
    }

    public static function template($template) {
        $key=self::current_key(); $file=ZP_SUITE_PATH.'templates/page-case-studies-route.php';
        if($key==='' || !is_readable($file) || !function_exists('zp_cs18_render')) { return $template; }
        self::prepare($key); return $file;
    }

    public static function assets(): void {
        if(self::current_key()==='') { return; }
        $file=ZP_SUITE_PATH.'assets/css/case-studies-20261018.css';
        if(is_readable($file)) { wp_enqueue_style('zp-case-studies-20261018',ZP_SUITE_URL.'assets/css/case-studies-20261018.css',[],(string)filemtime($file)); }
    }

    public static function meta($value, string $field) {
        if(self::current_key()!=='index') { return $value; }
        $name=['seo_title'=>'rank_math_title','description'=>'rank_math_description','canonical'=>'rank_math_canonical_url'][$field]??'';
        $saved=$name!==''?get_post_meta(self::index_id(),$name,true):'';
        if(is_string($saved) && trim($saved)!=='') { return $value; }
        return $field==='canonical'?home_url('/case-studies/'):(self::data()['index']['pl'][$field]??$value);
    }

    public static function schema($graph) {
        $key=self::current_key(); if($key==='' || !is_array($graph)) { return $graph; }
        $data=self::data(); $lang=function_exists('zpl_language')&&zpl_language()==='en'?'en':'pl';
        $url=home_url($data[$key]['path']); if(class_exists('ZPL\\Router')) { $url=\ZPL\Router::url($url,$lang); }
        $article=[];
        foreach($graph as $name=>$node) {
            if(!is_array($node)) { continue; }
            if(array_intersect((array)($node['@type']??[]),['Article','BlogPosting','NewsArticle'])) { if(!$article) { $article=$node; } unset($graph[$name]); }
            if(in_array('FAQPage',(array)($node['@type']??[]),true)) { unset($graph[$name]); }
        }
        if($key==='index') {
            $items=[];
            foreach(self::index_posts() as $post) { foreach(self::registry() as $casekey=>$row) { if((int)$post->ID!==$row['id']) { continue; } $link=home_url($row['path']); if(class_exists('ZPL\\Router')) { $link=\ZPL\Router::url($link,$lang); } $items[]=['@type'=>'ListItem','position'=>count($items)+1,'url'=>$link,'name'=>$data[$casekey]['brand']]; } }
            $graph['zp_case_studies_20261018']=['@type'=>'ItemList','@id'=>$url.'#case-studies','name'=>$data['index'][$lang]['title'],'numberOfItems'=>count($items),'itemListElement'=>$items];
        } else {
            unset($article['articleType'],$article['mainEntity']);
            $article=array_merge($article,['@type'=>'BlogPosting','@id'=>$url.'#blogposting','url'=>$url,'headline'=>$data[$key][$lang]['title'],
                'description'=>$data[$key][$lang]['description'],'inLanguage'=>$lang==='en'?'en-GB':'pl-PL','mainEntityOfPage'=>['@id'=>$url.'#webpage'],
                'datePublished'=>get_post_time(DATE_W3C,false,get_queried_object_id()),'dateModified'=>get_post_modified_time(DATE_W3C,false,get_queried_object_id())]);
            if(empty($article['author'])) { $article['author']=['@type'=>'Organization','name'=>'Zaprojektowani','url'=>home_url('/')]; }
            $image=$data[$key]['gallery'][0]??[];
            if($image && function_exists('zp_cs18_asset')) { $article['image']=['@type'=>'ImageObject','url'=>zp_cs18_asset($image)]; }
            $graph['zp_case_study_20261018']=$article;
        }
        return $graph;
    }

    private static function error(string $message): void { update_option(self::ERROR,$message,false); }

    /** One authenticated native-page creation; an owned draft can resume only if its prepared content is unchanged. */
    public static function migrate(): void {
        if(!is_admin() || wp_doing_ajax() || !current_user_can('manage_options') || !current_user_can('edit_pages') || !current_user_can('publish_pages')) { return; }
        if(self::index_id()) { delete_option(self::ERROR); return; }
        if(!function_exists('zp_cs18_render') || !class_exists('ZPL\\Router') || count(self::registry())!==4) { self::error('Nie można utworzyć huba: brakuje renderera lub jednej z czterech zweryfikowanych, publicznych realizacji.'); return; }
        $token=['token'=>wp_generate_uuid4(),'started'=>time()];
        if(!add_option(self::LOCK,$token,'',false)) {
            self::error('Migracja huba ma aktywną blokadę. Jeżeli wcześniejsze uruchomienie zostało przerwane, administrator powinien sprawdzić zapisany stan i usunąć wyłącznie opcję zp_case_pages_20261018_lock przed ponowieniem.');
            return;
        }
        try {
            $state=get_option(self::STATE,[]); $state=is_array($state)?$state:[];
            $routes=\ZPL\Router::routes();
            if((isset($routes['/case-studies/']) && $routes['/case-studies/']!=='/en/case-studies/')
                || (in_array('/en/case-studies/',$routes,true) && ($routes['/case-studies/']??'')!=='/en/case-studies/')
                || get_page_by_path('en/case-studies',OBJECT,['page','post','attachment'])) { throw new RuntimeException('Adres angielski huba jest już zajęty. Istniejące trasy pozostawiono bez zmian.'); }
            $existing=get_page_by_path('case-studies',OBJECT,['page','post','attachment']);
            $id=self::index_id(false);
            if($existing && (!$id || (int)$existing->ID!==$id)) { throw new RuntimeException('Adres /case-studies/ jest już zajęty. Istniejącej strony nie nadpisano.'); }
            if(!empty($state['id']) && !$id) { throw new RuntimeException('Zapisany hub nie odpowiada znacznikowi własności. Wymaga sprawdzenia przez administratora.'); }
            $data=self::data()['index'];
            if($id) {
                $post=get_post($id);
                if(($state['state']??'')!=='prepared' || $post->post_status!=='draft'
                    || !hash_equals((string)($state['content_hash']??''),hash('sha256',(string)$post->post_content))) { throw new RuntimeException('Hub został zmieniony lub wycofany. Automatyczna publikacja została pominięta.'); }
            } else {
                $content=wp_kses_post(zp_cs18_render('index','pl',false));
                if($content==='' || strpos($content,'<h1')===false || preg_match('~<(?:form|script)\b~i',$content)) { throw new RuntimeException('Nie udało się przygotować semantycznej treści huba bez formularza.'); }
                $id=wp_insert_post(['post_type'=>'page','post_status'=>'draft','post_name'=>'case-studies','post_parent'=>0,
                    'post_title'=>$data['pl']['title'],'post_excerpt'=>$data['pl']['description'],'post_content'=>$content,
                    'comment_status'=>'closed','ping_status'=>'closed','meta_input'=>[self::MARKER=>'index']],true);
                if(is_wp_error($id) || !$id) { throw new RuntimeException('WordPress nie utworzył strony huba.'); }
                $id=(int)$id; $state=['id'=>$id,'state'=>'prepared','content_hash'=>hash('sha256',(string)get_post($id)->post_content)];
                update_option(self::STATE,$state,false);
                if(self::index_id(false)!==$id || get_post($id)->post_name!=='case-studies') { throw new RuntimeException('Nie potwierdzono własności lub oczekiwanego adresu nowej strony. Pozostaje szkicem.'); }
            }
            $result=wp_update_post(['ID'=>$id,'post_status'=>'publish'],true);
            if(is_wp_error($result) || !$result || self::index_id()!==$id) {
                if(get_post_meta($id,self::MARKER,true)==='index' && get_post_status($id)==='publish') { wp_update_post(['ID'=>$id,'post_status'=>'draft']); }
                throw new RuntimeException('WordPress nie potwierdził publikacji pod /case-studies/. Hub pozostaje szkicem.');
            }
            update_option(self::STATE,['id'=>$id,'state'=>'published','content_hash'=>$state['content_hash']],false);
            delete_option(self::ERROR); \ZPL\Router::reset();
            if(class_exists('ZPL\\Sitemap')) { \ZPL\Sitemap::forget(); }
            if(is_callable(['RankMath\\Sitemap\\Cache','invalidate_storage'])) { \RankMath\Sitemap\Cache::invalidate_storage('page'); }
        } catch(Throwable $error) { self::error($error->getMessage()); }
        finally { if(get_option(self::LOCK)===$token) { delete_option(self::LOCK); } }
    }

    public static function notice(): void {
        if(!current_user_can('manage_options')) { return; } $message=get_option(self::ERROR,'');
        if(is_string($message) && $message!=='') { echo '<div class="notice notice-error"><p><strong>Case studies:</strong> '.esc_html($message).'</p></div>'; }
    }
}

function zp_cs18_case_public(string $key): bool { return isset(ZP_Case_Pages_20261018::registry()[$key]); }

// Register before ZPL Runtime::boot (plugins_loaded/PHP_INT_MIN) resolves English routes.
add_filter('zp_en_rewrite_routes',[ZP_Case_Pages_20261018::class,'routes'],150);
add_filter('zpl_has_page',[ZP_Case_Pages_20261018::class,'has_page'],150,2);
add_filter('zpl_sitemap_pages',[ZP_Case_Pages_20261018::class,'sitemap_pages'],150);
add_filter('zpl_priority_dictionary',[ZP_Case_Pages_20261018::class,'dictionary'],150,3);
add_filter('template_include',[ZP_Case_Pages_20261018::class,'template'],99);
add_action('wp_enqueue_scripts',[ZP_Case_Pages_20261018::class,'assets'],45);
add_filter('rank_math/frontend/title',static fn($value)=>ZP_Case_Pages_20261018::meta($value,'seo_title'),30);
add_filter('rank_math/frontend/description',static fn($value)=>ZP_Case_Pages_20261018::meta($value,'description'),30);
add_filter('rank_math/frontend/canonical',static fn($value)=>ZP_Case_Pages_20261018::meta($value,'canonical'),30);
add_filter('rank_math/json_ld',[ZP_Case_Pages_20261018::class,'schema'],150);
add_action('admin_init',[ZP_Case_Pages_20261018::class,'migrate'],150);
add_action('admin_notices',[ZP_Case_Pages_20261018::class,'notice']);
