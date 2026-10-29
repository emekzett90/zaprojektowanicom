<?php
/** Reviewed project case studies. The release upgrade is the only write path. */
if (!defined('ABSPATH')) { exit; }

final class ZP_Case_Studies_20261015 {
    public const VERSION = '3.2.11';
    public const STATE = 'zp_case_studies_20261015';
    private const LOCK = 'zp_case_studies_20261015_lock';
    private const MARKER = '_zp_case_study_20261015';
    private const COPY = ['_elementor_edit_mode','_elementor_template_type','_elementor_version','_elementor_pro_version','_elementor_page_settings','_wp_page_template'];
    private const META = ['_elementor_data','_thumbnail_id','rank_math_title','rank_math_description','rank_math_focus_keyword','rank_math_primary_category','rank_math_robots','rank_math_rich_snippet','rank_math_snippet_article_type','rank_math_schema_Article','rank_math_facebook_image','rank_math_facebook_image_id','rank_math_twitter_image','rank_math_twitter_image_id','rank_math_twitter_use_facebook',self::MARKER];
    private static $english_identity = [];

    public static function english_identity(string $key): array { return self::$english_identity[$key] ?? []; }
    public static function prepare_english_identity(string $key, string $html): void {
        if (!class_exists('ZPL\\Page')) { return; }
        $map = [];
        foreach (\ZPL\Page::extract('<body>' . $html . '</body>') as $unit) { if (isset($unit['k']) && $unit['k'] !== '') { $map[$unit['k']] = $unit['k']; } }
        self::$english_identity[$key] = $map;
    }

    public static function data(): array {
        static $data = null;
        if ($data !== null) { return $data; }
        $data = [];
        foreach (['siemianowski','apartament'] as $key) {
            $file = ZP_SUITE_PATH . 'templates/case-studies-20261015/' . $key . '.json';
            $row = is_file($file) ? json_decode((string) file_get_contents($file), true) : null;
            if (is_array($row) && !empty($row['pl']['sections']) && !empty($row['en']['sections'])) { $data[$key] = $row; }
        }
        return $data;
    }

    public static function is_en(): bool { return function_exists('zpl_language') && zpl_language() === 'en'; }
    private static function norm(string $text): string { return strtolower(remove_accents(trim(wp_strip_all_tags($text)))); }
    public static function state(): array { $state = get_option(self::STATE, []); return is_array($state) ? $state : []; }
    public static function row(string $key): array { return (array) (self::state()['rows'][$key] ?? []); }
    public static function url(string $path, string $lang = ''): string {
        if (($lang === 'en' || ($lang === '' && self::is_en())) && class_exists('ZPL\\Router')) { $path = \ZPL\Router::en_path($path); }
        return home_url($path);
    }
    public static function case_url(string $key, string $lang = ''): string {
        $row = self::row($key); $id = (int) ($row['id'] ?? 0);
        if (!$id || get_post_status($id) !== 'publish' || get_post_meta($id, self::MARKER, true) !== $key) { return ''; }
        $path = (string) ($row['path'] ?? '');
        if ($path === '') { $path = (string) wp_parse_url(get_permalink($id), PHP_URL_PATH); }
        if ($path === '' || $path[0] !== '/') { return ''; }
        if ($lang === 'en' || ($lang === '' && self::is_en())) {
            // Tlumacz EN can assign its approved route after a case is published.
            // Read the resolved map here, outside the routes filter, to avoid recursion.
            $mapped = class_exists('ZPL\\Router') ? (\ZPL\Router::routes()[$path] ?? '') : '';
            if (!is_string($mapped) || strpos($mapped, '/en/') !== 0) { $mapped = self::data()[$key]['en_path']; }
            return home_url($mapped);
        }
        return home_url($path);
    }
    public static function legacy_routes($legacy): array {
        $legacy = is_array($legacy) ? $legacy : [];
        if (!class_exists('ZPL\\Router')) { return $legacy; }
        foreach (self::data() as $key=>$data) {
            $url = self::case_url($key, 'en'); $actual = $url !== '' ? (string) wp_parse_url($url, PHP_URL_PATH) : '';
            $old = $data['en_path'];
            if ($actual === '' || $actual === $old || strpos($actual, '/en/') !== 0 || isset(\ZPL\Router::reverse()[$old])) { continue; }
            // Never replace another module's explicit alias or a real English page.
            if (!isset($legacy[$old])) { $legacy[$old] = $actual; }
        }
        return $legacy;
    }
    private static function asset(string $path): string { return ZP_SUITE_URL . ltrim($path, '/'); }
    private static function html_links(string $html, string $lang): string {
        return (string) preg_replace_callback('~href="(/[^"<>]*)"~', static function ($m) use ($lang) { return 'href="' . esc_url(self::url($m[1], $lang)) . '"'; }, $html);
    }
    private static function image(string $asset, string $alt, string $caption): string {
        $size = @getimagesize(ZP_SUITE_PATH . $asset); $width = (int) ($size[0] ?? 0); $height = (int) ($size[1] ?? 0);
        return '<figure class="zpArticleNew__imageBlock"><img class="zpArticleNew__img" src="' . esc_url(self::asset($asset)) . '" alt="' . esc_attr($alt) . '" width="' . $width . '" height="' . $height . '" loading="lazy" decoding="async"><figcaption>' . esc_html($caption) . '</figcaption></figure>';
    }
    public static function render(string $key, string $lang = 'pl'): string {
        $row = self::data()[$key] ?? null;
        if (!$row || !in_array($lang, ['pl','en'], true)) { return ''; }
        $c = $row[$lang]; $en = $lang === 'en';
        $html = '<article lang="' . $lang . '" class="zpArticleNew zpArticlePremium zpCaseStudy" data-zp-article data-zp-case-study="' . esc_attr($key) . '" data-rankmath-main="' . esc_attr($c['keyword']) . '">'
            . '<header class="zpArticleNew__hero"><div class="zpArticleNew__heroGrid"><div class="zpArticleNew__heroCopy"><div class="zpArticleNew__eyebrow"><span>Case study</span><span>' . esc_html($c['tag']) . '</span></div><h1>' . esc_html($c['title']) . '</h1><p class="zpArticleNew__lead">' . esc_html($c['lead']) . '</p><p class="zpArticleNew__intro">' . esc_html($c['intro']) . '</p></div>'
            . '<aside class="zpArticleNew__heroCard" aria-label="' . ($en ? 'Project overview' : 'Informacje o projekcie') . '"><span class="zpArticleNew__heroCardKicker">' . ($en ? 'Project overview' : 'Karta realizacji') . '</span><strong>' . esc_html($c['card_title']) . '</strong><p>' . esc_html($c['card_copy']) . '</p><a href="' . esc_url($row['live']) . '" target="_blank" rel="noopener">' . ($en ? 'Visit the client website' : 'Zobacz stronę klienta') . '</a></aside></div></header>';
        $html .= self::image($row['cover'], $c['cover_alt'], $c['cover_caption']);
        $html .= '<nav class="zpArticleNewTOC" aria-label="' . ($en ? 'Contents' : 'Spis treści') . '"><strong>' . ($en ? 'Contents' : 'Spis treści') . '</strong><div>';
        foreach ($c['sections'] as $section) { $html .= '<a href="#' . $key . '-' . esc_attr($section[0]) . '">' . esc_html($section[1]) . '</a>'; }
        $html .= '<a href="#' . $key . '-faq">FAQ</a></div></nav><div class="zpArticleNew__layout"><div class="zpArticleNew__main">';
        $html .= '<section class="zpArticleNew__section" id="' . $key . '-zakres"><p class="zpArticleNew__label">' . ($en ? 'Deliverables' : 'Zakres prac') . '</p><h2>' . ($en ? 'What we designed and implemented' : 'Co zaprojektowaliśmy i wdrożyliśmy') . '</h2><ul>';
        foreach ($c['scope'] as $scope) { $html .= '<li>' . esc_html($scope) . '</li>'; }
        $html .= '</ul></section>';
        foreach ($c['sections'] as $index => $section) {
            $html .= '<section class="zpArticleNew__section" id="' . $key . '-' . esc_attr($section[0]) . '"><p class="zpArticleNew__label">' . sprintf('%02d', $index + 1) . '</p><h2>' . esc_html($section[1]) . '</h2>' . self::html_links($section[2], $lang) . '</section>';
            if ($index === 1) { $html .= self::image($row['second'], $c['second_alt'], $c['second_caption']); }
        }
        $html .= '<section class="zpArticleNew__section" id="' . $key . '-faq"><h2>' . ($en ? 'Questions about the project' : 'Pytania o realizację') . '</h2>';
        foreach ($c['faq'] as [$question, $answer]) { $html .= '<details class="zpArticleNewFAQ"><summary>' . esc_html($question) . '</summary><p>' . esc_html($answer) . '</p></details>'; }
        $html .= '</section><section class="zpArticleNew__section"><h2>' . ($en ? 'Explore related work and services' : 'Zobacz powiązane realizacje i usługi') . '</h2><p><a href="' . esc_url(self::url('/realizacje/', $lang)) . '">' . ($en ? 'Our portfolio' : 'Nasze portfolio') . '</a> · <a href="' . esc_url(self::url('/tworzenie-stron-internetowych/', $lang)) . '">' . ($en ? 'Website development' : 'Tworzenie stron internetowych') . '</a> · <a href="' . esc_url(self::url('/identyfikacja-wizualna/', $lang)) . '">' . ($en ? 'Visual identity' : 'Identyfikacja wizualna') . '</a></p></section>';
        $html .= '<section class="zpArticleNewCTA"><div><p class="zpArticleNewCTA__kicker">Zaprojektowani · Case study</p><h2>' . esc_html($c['cta']) . '</h2><p>' . esc_html($c['cta_text']) . '</p><div class="zpArticleNewCTA__actions"><a href="' . esc_url(self::url('/studio-wyceny/', $lang)) . '">' . ($en ? 'Discuss your project' : 'Omów projekt') . '</a><a href="' . esc_url(self::url('/kontakt/', $lang)) . '">' . ($en ? 'Contact us' : 'Kontakt') . '</a></div></div></section></div></div></article>';
        if ($en && class_exists('ZPL\\Page')) {
            // Prepare before Runtime starts its document walk; Html's parser is stateful.
            self::prepare_english_identity($key, $html);
        }
        return $html;
    }

    /** Only real case titles or the dedicated marker qualify; mentions in guides never do. */
    public static function candidates(string $key, array $row): array {
        $found = [];
        foreach (get_posts(['post_type'=>'post','post_status'=>['publish','draft','pending','private','future'],'numberposts'=>-1,'suppress_filters'=>true]) as $post) {
            $marker = (string) get_post_meta($post->ID, self::MARKER, true);
            $title = self::norm((string) $post->post_title);
            $brand = self::norm($row['brand']);
            $known = in_array($title, array_map([self::class, 'norm'], $row['legacy_titles']), true);
            $case_title = strpos($title, $brand) !== false && (strpos($title, 'case study') !== false || strpos($title, 'strona internetowa') === 0 || $title === self::norm($row['pl']['title']));
            if ($marker === $key || $known || $case_title) { $found[(int) $post->ID] = $post; }
        }
        return $found;
    }
    private static function snapshot(int $id): array {
        $post = get_post($id, OBJECT, 'raw'); $meta = [];
        foreach (array_unique(array_merge(self::COPY, self::META)) as $field) { $meta[$field] = get_post_meta($id, $field, false); }
        return ['post'=>array_intersect_key((array) $post, array_flip(['post_title','post_content','post_excerpt','post_status','post_name','post_date','post_date_gmt'])),'categories'=>wp_get_post_categories($id),'meta'=>$meta];
    }
    private static function restore(int $id, array $before): void {
        zp_seo_plan_update_post(['ID'=>$id, 'edit_date'=>true] + $before['post'], true);
        wp_set_post_categories($id, $before['categories'], false);
        foreach ($before['meta'] as $field=>$values) { delete_post_meta($id, $field); foreach ($values as $value) { add_post_meta($id, $field, wp_slash($value)); } }
        clean_post_cache($id);
    }
    private static function cover(string $key, array $row): int {
        $map = (array) get_option('zp_case_studies_20261015_media', []);
        $file = ZP_SUITE_PATH . $row['cover']; $digest = is_file($file) ? hash_file('sha256', $file) : '';
        if (!$digest) { throw new RuntimeException('missing_cover_' . $key); }
        $saved = (array) ($map[$digest] ?? []);
        if (!empty($saved['id']) && get_post_type((int) $saved['id']) === 'attachment') { return (int) $saved['id']; }
        $upload = wp_upload_bits('case-study-' . $key . '.webp', null, (string) file_get_contents($file));
        if (!empty($upload['error'])) { throw new RuntimeException('cover_upload_failed_' . $key); }
        $id = wp_insert_attachment(['post_mime_type'=>'image/webp','post_title'=>$row['brand'] . ' — case study','post_status'=>'inherit'], $upload['file'], 0, true);
        if (is_wp_error($id) || !$id) { throw new RuntimeException('cover_attachment_failed_' . $key); }
        if (!function_exists('wp_generate_attachment_metadata')) { require_once ABSPATH . 'wp-admin/includes/image.php'; }
        wp_update_attachment_metadata($id, wp_generate_attachment_metadata($id, $upload['file']));
        update_post_meta($id, '_wp_attachment_image_alt', $row['pl']['cover_alt']);
        $map[$digest] = ['id'=>(int) $id, 'key'=>$key]; update_option('zp_case_studies_20261015_media', $map, false);
        return (int) $id;
    }
    public static function migrate(): array {
        $state = self::state();
        if (($state['version'] ?? '') === self::VERSION) { return $state; }
        $state += ['rows'=>[], 'errors'=>[]]; $state['errors'] = [];
        $source = get_post(4551); // The actual published Świat Grilli case layout, never its copy or demo.
        if (!$source || $source->post_type !== 'post' || $source->post_status !== 'publish' || $source->post_name !== 'sklep-internetowy-swiat-grilli' || !function_exists('zp_seo_articles_elementor')) { $state['errors'][] = 'missing_reviewed_source_4551'; update_option(self::STATE, $state, false); return $state; }
        foreach (self::data() as $key=>$row) {
            if (($state['rows'][$key]['state'] ?? '') === 'applied') { continue; }
            $id = 0; $created = false; $before = null;
            try {
                $found = self::candidates($key, $row);
                if (count($found) > 1) { throw new RuntimeException('ambiguous_existing_case_' . $key); }
                $existing = $found ? reset($found) : null;
                $id = $existing ? (int) $existing->ID : 0;
                $taken = get_page_by_path($row['slug'], OBJECT, ['post','page']);
                if (!$id && $taken) { throw new RuntimeException('slug_owned_by_other_content_' . $key); }
                $case = get_term_by('slug', 'case-study', 'category');
                if (!$case) { $made = wp_insert_term('Case study', 'category', ['slug'=>'case-study']); if (is_wp_error($made)) { throw new RuntimeException('case_category_failed'); } $case = get_term((int) $made['term_id'], 'category'); }
                $web = get_term_by('slug', 'strony-internetowe', 'category');
                if (!$web || !$case || is_wp_error($case)) { throw new RuntimeException('missing_web_category'); }
                $article = self::render($key, 'pl'); $notes = [];
                $layout = zp_seo_articles_elementor($id ?: 4551, $article, $notes);
                if ($layout === null && $id) { $layout = zp_seo_articles_elementor(4551, $article, $notes); }
                if ($layout === null) { throw new RuntimeException('missing_approved_elementor_article_widget'); }
                $thumb = self::cover($key, $row);
                $old_url = $id ? (string) get_permalink($id) : '';
                // Draft/private/future permalinks may be /?p=ID; never pin that as the public root route.
                $old_path = $id && get_post_status($id) === 'publish' ? (string) wp_parse_url($old_url, PHP_URL_PATH) : '';
                if ($id) {
                    $before = self::snapshot($id);
                    $state['rows'][$key] = ['id'=>$id,'state'=>'prepared','before'=>$before,'path'=>$old_path,'created'=>false]; update_option(self::STATE, $state, false);
                    $result = zp_seo_plan_update_post(['ID'=>$id,'post_title'=>$row['pl']['title'],'post_content'=>$article,'post_excerpt'=>$row['pl']['description'],'post_date'=>$before['post']['post_date'],'post_date_gmt'=>$before['post']['post_date_gmt'],'edit_date'=>true], true);
                } else {
                    $result = wp_insert_post(wp_slash(['post_type'=>'post','post_status'=>'draft','post_name'=>$row['slug'],'post_title'=>$row['pl']['title'],'post_content'=>$article,'post_excerpt'=>$row['pl']['description'],'post_author'=>(int) $source->post_author,'post_category'=>[(int) $web->term_id,(int) $case->term_id],'comment_status'=>'closed','ping_status'=>'closed']), true);
                    if (!is_wp_error($result) && $result) { $id = (int) $result; $created = true; update_post_meta($id, self::MARKER, $key); $state['rows'][$key] = ['id'=>$id,'state'=>'prepared','before'=>null,'created'=>true]; update_option(self::STATE, $state, false); }
                }
                if (is_wp_error($result) || !$id) { throw new RuntimeException('post_write_failed_' . $key); }
                $terms = array_unique(array_merge(wp_get_post_categories($id), [(int) $web->term_id,(int) $case->term_id]));
                wp_set_post_categories($id, $terms, false);
                // Existing URL categories are preserved; new posts deliberately use the WWW route like Świat Grilli.
                $primary = $created ? (int) $web->term_id : (int) get_post_meta($id, 'rank_math_primary_category', true);
                if (!$primary || !in_array($primary, $terms, true)) { $primary = (int) $web->term_id; }
                foreach (self::COPY as $field) { if ($created || get_post_meta($id, $field, true) === '') { $value = get_post_meta(4551, $field, true); if ($value !== '') { update_post_meta($id, $field, wp_slash($value)); } } }
                $meta = ['_elementor_data'=>$layout,self::MARKER=>$key,'_thumbnail_id'=>$thumb,'rank_math_title'=>$row['pl']['seo_title'],'rank_math_description'=>$row['pl']['description'],'rank_math_focus_keyword'=>$row['pl']['keyword'],'rank_math_primary_category'=>$primary,'rank_math_robots'=>['index'],'rank_math_rich_snippet'=>'article','rank_math_snippet_article_type'=>'BlogPosting','rank_math_schema_Article'=>['@type'=>'Article','articleType'=>'BlogPosting','headline'=>'%seo_title%','description'=>'%seo_description%','image'=>'%post_thumbnail%','datePublished'=>'%date(Y-m-dTH:i:sP)%','dateModified'=>'%modified(Y-m-dTH:i:sP)%','metadata'=>['title'=>'Article','type'=>'template','isPrimary'=>true,'shortcode'=>'zp-case-study-20261015']],'rank_math_facebook_image'=>wp_get_attachment_url($thumb),'rank_math_facebook_image_id'=>$thumb,'rank_math_twitter_image'=>wp_get_attachment_url($thumb),'rank_math_twitter_image_id'=>$thumb,'rank_math_twitter_use_facebook'=>'on'];
                foreach ($meta as $field=>$value) { update_post_meta($id, $field, wp_slash($value)); if (get_post_meta($id, $field, true) != $value) { throw new RuntimeException('metadata_readback_failed_' . $field); } }
                if ($created) { $published = zp_seo_plan_update_post(['ID'=>$id,'post_status'=>'publish'], true); if (is_wp_error($published)) { throw new RuntimeException('publish_failed'); } }
                clean_post_cache($id); $after_url = (string) get_permalink($id);
                $path = get_post_status($id) === 'publish' ? (string) wp_parse_url($after_url, PHP_URL_PATH) : '';
                if (!$created && $old_url !== $after_url) { throw new RuntimeException('existing_permalink_changed'); }
                if (!$created) {
                    foreach (['post_status','post_name','post_date','post_date_gmt'] as $field) { if ((string) get_post_field($field, $id, 'raw') !== (string) $before['post'][$field]) { throw new RuntimeException('existing_identity_changed_' . $field); } }
                }
                $status = get_post_status($id);
                $state['rows'][$key] = ['id'=>$id,'state'=>'applied','before'=>$before,'created'=>$created,'path'=>$path,'status'=>$status,'needs_publication'=>$status !== 'publish','at'=>current_time('mysql')];
                zp_seo_articles_elementor_flush([$id]);
            } catch (Throwable $error) {
                if ($id && $before) { self::restore($id, $before); }
                elseif ($id && $created) { zp_seo_plan_update_post(['ID'=>$id,'post_status'=>'draft'], true); }
                $state['errors'][$key] = $error->getMessage();
                $state['rows'][$key] = ['id'=>$id,'state'=>'needs_attention','before'=>$before,'created'=>$created,'error'=>$error->getMessage()];
            }
            update_option(self::STATE, $state, false);
        }
        if (!$state['errors'] && count($state['rows']) === 2 && count(array_filter($state['rows'], static fn($r)=>($r['state'] ?? '') === 'applied')) === 2) {
            $state['version'] = self::VERSION; update_option(self::STATE, $state, false);
            if (class_exists('ZPL\\Router')) { \ZPL\Router::reset(); }
            if (class_exists('ZPL\\Dict')) { \ZPL\Dict::flush(); }
            if (class_exists('ZPL\\Sitemap')) { \ZPL\Sitemap::forget(); }
            if (function_exists('zp_suite_wiedza_flush_index_cache')) { zp_suite_wiedza_flush_index_cache(); }
            if (class_exists('RankMath\\Sitemap\\Cache')) { \RankMath\Sitemap\Cache::invalidate_storage(); }
            do_action('litespeed_purge_all');
        }
        return $state;
    }
    public static function maybe_upgrade(): void {
        if (!defined('ZP_SUITE_VERSION') || version_compare(ZP_SUITE_VERSION, self::VERSION, '<') || !is_admin() || wp_doing_ajax() || !current_user_can('manage_options') || !current_user_can('edit_posts') || !current_user_can('unfiltered_html') || (self::state()['version'] ?? '') === self::VERSION) { return; }
        // A failed partial run requires review, rather than silently replacing later editor changes.
        if (!empty(self::state()['errors'])) { return; }
        foreach (self::state()['rows'] ?? [] as $row) { if (($row['state'] ?? '') !== 'applied') { return; } }
        if (!add_option(self::LOCK, time(), '', false)) { return; }
        try { self::migrate(); } finally { delete_option(self::LOCK); }
    }
    public static function current_key(): string {
        if (is_admin() || !is_singular('post')) { return ''; }
        $key = (string) get_post_meta(get_queried_object_id(), self::MARKER, true);
        return isset(self::data()[$key]) && (int) (self::row($key)['id'] ?? 0) === (int) get_queried_object_id() ? $key : '';
    }
    public static function translate_article($html) {
        $key = self::current_key();
        if (!$key || !self::is_en() || !is_string($html) || strpos($html, 'data-zp-case-study="' . $key . '"') === false) { return $html; }
        return preg_replace_callback('~<article\b[^>]*data-zp-case-study="' . preg_quote($key, '~') . '"[^>]*>.*?</article>~s', static function ($match) use ($key) {
            $translated = self::render($key, 'en');
            // The global article hero may already own H1. Keep its heading demotion.
            if (preg_match('~<h([12])\b([^>]*)>~i', $match[0], $heading) && $heading[1] === '2') {
                $translated = preg_replace_callback('~<h1\b[^>]*>(.*?)</h1>~is', static fn($title)=>'<h2' . $heading[2] . '>' . $title[1] . '</h2>', $translated, 1);
            }
            return $translated;
        }, $html, 1);
    }
    public static function routes($routes): array {
        $routes = is_array($routes) ? $routes : [];
        foreach (self::data() as $key=>$data) {
            $row = self::row($key); $id = (int) ($row['id'] ?? 0); $path = (string) ($row['path'] ?? '');
            if ($id && $path === '' && get_post_status($id) === 'publish') { $path = (string) wp_parse_url(get_permalink($id), PHP_URL_PATH); }
            if (!$id || get_post_status($id) !== 'publish' || $path === '' || get_post_meta($id, self::MARKER, true) !== $key) { continue; }
            if ((!isset($routes[$path]) || $routes[$path] === $data['en_path']) && (!in_array($data['en_path'], $routes, true) || ($routes[$path] ?? '') === $data['en_path'])) { $routes[$path] = $data['en_path']; }
        }
        return $routes;
    }
    public static function schema($graph): array {
        if (!is_array($graph)) { return []; }
        $key = self::current_key(); if (!$key) { return $graph; }
        $id = get_queried_object_id(); $post = get_post($id); if (!$post || $post->post_status !== 'publish') { return $graph; }
        $en = self::is_en(); $row = self::data()[$key]; $copy = $row[$en ? 'en' : 'pl']; $url = self::case_url($key); $publisher = null; $author = null;
        foreach ($graph as $name=>$node) {
            if (!is_array($node)) { continue; }
            if (array_intersect((array) ($node['@type'] ?? []), ['Article','BlogPosting','NewsArticle'])) { $publisher = $node['publisher'] ?? $publisher; $author = $node['author'] ?? $author; unset($graph[$name]); }
        }
        $image = wp_get_attachment_image_src(get_post_thumbnail_id($id), 'full');
        $node = ['@type'=>'BlogPosting','@id'=>$url . '#blogposting','url'=>$url,'headline'=>$copy['title'],'description'=>$copy['description'],'inLanguage'=>$en ? 'en-GB' : 'pl-PL','datePublished'=>get_post_time(DATE_W3C, false, $post),'dateModified'=>get_post_modified_time(DATE_W3C, false, $post),'mainEntityOfPage'=>['@id'=>$url . '#webpage'],'author'=>$author ?: ['@type'=>'Organization','name'=>'Zaprojektowani','url'=>home_url('/')]];
        if ($publisher) { $node['publisher'] = $publisher; }
        if ($image) { $node['image'] = ['@type'=>'ImageObject','url'=>$image[0],'width'=>(int) $image[1],'height'=>(int) $image[2]]; }
        $graph['zp_case_study_20261015'] = $node;
        return $graph;
    }
    public static function projects($projects, $mode = ''): array {
        if (!is_array($projects)) { return []; }
        foreach ($projects as &$project) {
            if (!is_array($project) || empty($project['brand'])) { continue; }
            $brand = self::norm($project['brand']);
            $key = $brand === 'siemianowski' ? 'siemianowski' : ($brand === 'apartament piekna' ? 'apartament' : '');
            if ($key && ($url = self::case_url($key))) { $project['case_url'] = $url; $project['caseStudyUrl'] = $url; }
        }
        unset($project); return $projects;
    }
    public static function permalink($url, $post, $leavename = false) {
        if ($leavename || !is_object($post) || ($post->post_type ?? '') !== 'post') { return $url; }
        foreach (self::state()['rows'] ?? [] as $key=>$row) {
            $path = (string) ($row['path'] ?? '');
            if ((int) ($row['id'] ?? 0) === (int) $post->ID && $path !== '' && $path[0] === '/' && get_post_meta($post->ID, self::MARKER, true) === $key) { return home_url($path); }
        }
        return $url;
    }
    public static function sitemap_url($url, $object) {
        if (!is_string($url) || !is_object($object) || empty($object->ID)) { return $url; }
        $post = get_post((int) $object->ID);
        return $post && $post->post_status === 'publish' ? self::permalink($url, $post) : $url;
    }
}

function zp_case_studies_cards_20261015(): string {
    $en = ZP_Case_Studies_20261015::is_en(); $cards = '';
    foreach (ZP_Case_Studies_20261015::data() as $key=>$row) {
        $url = ZP_Case_Studies_20261015::case_url($key); if ($url === '') { continue; } $copy = $row[$en ? 'en' : 'pl'];
        $features = ''; $indices = $key === 'siemianowski' ? [0,1,4] : [0,3,6];
        foreach ($indices as $index) { if (!empty($copy['scope'][$index])) { $features .= '<li>' . esc_html($copy['scope'][$index]) . '</li>'; } }
        $cards .= '<a class="zpCaseLinks__card" data-zp-case-card="' . esc_attr($key) . '" href="' . esc_url($url) . '" aria-label="' . esc_attr(($en ? 'Read the case study: ' : 'Poznaj case study: ') . $row['brand']) . '">'
            . '<div class="zpCaseLinks__visual"><img src="' . esc_url(ZP_SUITE_URL . $row['cover']) . '" alt="" width="2560" height="1707" loading="lazy" decoding="async"><span class="zpCaseLinks__projectType">Case study</span></div>'
            . '<div class="zpCaseLinks__body"><p class="zpCaseLinks__tag">' . esc_html($copy['tag']) . '</p><h3>' . esc_html($row['brand']) . '</h3><p class="zpCaseLinks__summary">' . esc_html($copy['card_title']) . '</p><ul class="zpCaseLinks__features">' . $features . '</ul>'
            . '<div class="zpCaseLinks__foot"><span>' . ($en ? 'Discover the scope and solution' : 'Zobacz zakres i rozwiązanie') . '</span><strong>' . ($en ? 'Explore the project' : 'Poznaj realizację') . '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 17 17 7M7 7h10v10"/></svg></strong></div></div></a>';
    }
    $html = $cards ? '<section class="zpCaseLinks" data-zp-case-links aria-label="' . ($en ? 'Related case studies' : 'Powiązane case studies') . '"><div class="zpCaseLinks__head"><div><p class="zpCaseLinks__kicker">' . ($en ? 'Real projects • complete scope' : 'Prawdziwe projekty • pełny zakres') . '</p><h2>' . ($en ? 'From brand identity<br><strong>to a custom system.</strong>' : 'Od identyfikacji<br><strong>do własnego systemu.</strong>') . '</h2></div><p class="zpCaseLinks__lead">' . ($en ? 'Two brands, two different needs. Explore how we connected visual identity, websites and custom tools in complete projects.' : 'Dwie marki, dwie różne potrzeby. Zobacz, jak połączyliśmy identyfikację wizualną, strony internetowe i autorskie narzędzia w kompletnych realizacjach.') . '</p></div><div class="zpCaseLinks__grid">' . $cards . '</div></section>' : '';
    if ($en && $html !== '') { ZP_Case_Studies_20261015::prepare_english_identity('cards', $html); }
    return $html;
}

add_action('admin_init', [ZP_Case_Studies_20261015::class,'maybe_upgrade'], 110);
add_filter('zp_en_rewrite_routes', [ZP_Case_Studies_20261015::class,'routes'], 120);
add_filter('zpl_legacy_routes', [ZP_Case_Studies_20261015::class,'legacy_routes'], 120);
add_filter('zpl_has_page', static function ($has, $source) { return $has || isset(ZP_Case_Studies_20261015::routes([])[$source]); }, 120, 2);
add_filter('zpl_sitemap_pages', static function ($paths) { return array_values(array_unique(array_merge(is_array($paths) ? $paths : [], array_keys(ZP_Case_Studies_20261015::routes([]))))); }, 120);
add_filter('the_content', [ZP_Case_Studies_20261015::class,'translate_article'], PHP_INT_MAX);
add_filter('elementor/widget/render_content', [ZP_Case_Studies_20261015::class,'translate_article'], PHP_INT_MAX);
add_filter('rank_math/json_ld', [ZP_Case_Studies_20261015::class,'schema'], 130);
add_filter('zp_portfolio_projects_20261015', [ZP_Case_Studies_20261015::class,'projects'], 10, 2);
add_filter('post_link', [ZP_Case_Studies_20261015::class,'permalink'], PHP_INT_MAX, 3);
add_filter('rank_math/sitemap/xml_post_url', [ZP_Case_Studies_20261015::class,'sitemap_url'], PHP_INT_MAX, 2);
add_filter('zpl_priority_dictionary', static function ($layers, $lang, $source) {
    $layers = is_array($layers) ? $layers : []; $key = ZP_Case_Studies_20261015::current_key();
    if (!$key || $lang !== 'en' || !class_exists('ZPL\\Html') || !class_exists('ZPL\\Page')) { return $layers; }
    $row = ZP_Case_Studies_20261015::data()[$key]; $map = [];
    foreach (['title','seo_title','description','cover_alt'] as $field) {
        $norm = \ZPL\Html::norm($row['pl'][$field]); $english = \ZPL\Html::norm($row['en'][$field]);
        $map[$norm] = $row['en'][$field]; $map[$english] = $english;
        foreach (['title','meta','alt'] as $kind) {
            $variant = \ZPL\Page::variant($kind, $norm); if ($variant !== '') { $map[$variant] = $row['en'][$field]; }
            $variant = \ZPL\Page::variant($kind, $english); if ($variant !== '') { $map[$variant] = $english; }
        }
    }
    // This owned article is already rendered in English before Runtime's quality gate.
    // Register only its reviewed English units, rather than exempting the page from that gate.
    $map += ZP_Case_Studies_20261015::english_identity($key);
    array_unshift($layers, $map); return $layers;
}, 120, 3);
add_filter('zpl_dictionary', static function ($map, $lang) {
    $map = is_array($map) ? $map : [];
    if ($lang === 'en') { $map += ['Pełny opis realizacji'=>'Full project case study','Zobacz pełne case study'=>'Read the full case study','Zobacz pełną realizację'=>'Explore the project']; }
    if ($lang === 'en') { $map += ZP_Case_Studies_20261015::english_identity('cards'); }
    return $map;
}, 120, 2);
add_filter('do_shortcode_tag', static function ($html, $tag) {
    if (!is_string($html) || strpos($html, 'data-zp-case-links') !== false || !in_array($tag, ['zp_strony_internetowe_katowice','zp_page_strony_katowice','zp_page_strony_internetowe_katowice','zp_logo_branding_katowice','zp_page_logo_branding_katowice'], true)) { return $html; }
    // Industry variants already display their own carefully selected work.
    if (function_exists('zp_industry_work_source_20261015') && isset(zp_industry_work_plan_20261015()[zp_industry_work_source_20261015()])) { return $html; }
    $cards = zp_case_studies_cards_20261015(); if ($cards === '') { return $html; }
    return (string) preg_replace_callback('~<section\b[^>]*\bid="faq"[^>]*>~i', static fn($m)=>$cards . $m[0], $html, 1);
}, 35, 2);
add_action('wp_enqueue_scripts', static function () {
    if (is_admin() || count(ZP_Case_Studies_20261015::state()['rows'] ?? []) < 1) { return; }
    $service = function_exists('zp_suite_is_service_page') && (zp_suite_is_service_page('strony') || zp_suite_is_service_page('logo') || zp_suite_is_service_page('sklepy'));
    if (!is_front_page() && !is_page('realizacje') && !$service && ZP_Case_Studies_20261015::current_key() === '') { return; }
    wp_enqueue_style('zp-case-studies-20261015', ZP_SUITE_URL . 'assets/css/case-studies-20261015.css', [], defined('ZP_SUITE_VERSION') ? ZP_SUITE_VERSION : ZP_Case_Studies_20261015::VERSION);
    wp_enqueue_script('zp-case-studies-20261015', ZP_SUITE_URL . 'assets/js/case-studies-20261015.js', [], ZP_Case_Studies_20261015::VERSION, true);
    $links = []; foreach (['siemianowski','apartament'] as $key) { if ($url = ZP_Case_Studies_20261015::case_url($key)) { $links[$key] = $url; } }
    wp_add_inline_script('zp-case-studies-20261015', 'window.ZP_CASE_STUDIES_20261015=' . wp_json_encode(['links'=>$links,'label'=>ZP_Case_Studies_20261015::is_en() ? 'Read the full case study' : 'Zobacz pełne case study'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) . ';', 'before');
}, 30);
add_action('wp_head', static function () {
    if (defined('RANK_MATH_VERSION') || ZP_Case_Studies_20261015::current_key() === '') { return; }
    $graph = ZP_Case_Studies_20261015::schema([]);
    if ($graph) { echo '<script type="application/ld+json" id="zp-case-study-schema">' . wp_json_encode(['@context'=>'https://schema.org','@graph'=>array_values($graph)], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>'; }
}, 35);
add_action('admin_notices', static function () {
    if (!current_user_can('manage_options')) { return; }
    $state = ZP_Case_Studies_20261015::state(); $notes = [];
    foreach ($state['rows'] ?? [] as $key=>$row) {
        if (!empty($row['needs_publication'])) { $notes[] = $key . ': zachowano istniejący status ' . ($row['status'] ?? '') . '; wpis nie został opublikowany.'; }
    }
    foreach ($state['errors'] ?? [] as $key=>$error) { $notes[] = $key . ': ' . $error; }
    if ($notes) { echo '<div class="notice notice-warning"><p><strong>Case studies 3.2.11:</strong> ' . esc_html(implode(' ', $notes)) . '</p></div>'; }
});
