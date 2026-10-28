<?php
/** Altiora Linea renderer and SEO integration; publication is handled by the one-shot 3.2.37 importer. */
if (!defined('ABSPATH')) { exit; }
final class ZP_Altiora_Article_3236 {
    public const OPTION = 'zp_altiora_article_3236';
    public const MARKER = '_zp_altiora_article_3236';
    public static function data(): array {
        static $data;
        if ($data === null) {
            $file = ZP_SUITE_PATH . 'content/altiora-linea/article.json';
            $data = is_readable($file) ? json_decode((string) file_get_contents($file), true) : [];
            if (!is_array($data)) { $data = []; }
        }
        return $data;
    }
    private static function href(string $path, string $lang): string {
        if ($lang === 'en' && class_exists('ZPL\\Router')) { $path = \ZPL\Router::en_path($path); }
        return home_url($path);
    }
    private static function links(string $html, string $lang): string {
        return (string) preg_replace_callback('~href="(/[^"<>]*)"~', static function ($m) use ($lang) {
            return 'href="' . esc_url(self::href($m[1], $lang)) . '"';
        }, $html);
    }
    private static function cta(array $copy, string $lang, bool $team = false): string {
        $en = $lang === 'en';
        $html = '<aside class="zpArticleNewCTA zpAltioraCase__cta" aria-label="' . ($en ? 'Discuss a website project' : 'Porozmawiajmy o Twojej stronie') . '">';
        if ($team) { $html .= '<img class="zpAltioraCase__team" src="' . esc_url(ZP_SUITE_URL . 'assets/img/guide/team-trio.webp') . '" alt="' . ($en ? 'The Zaprojektowani team' : 'Zespół Zaprojektowani') . '" loading="lazy" decoding="async" width="760" height="776">'; }
        return $html . '<div><p class="zpArticleNewCTA__kicker">Zaprojektowani.com</p><h2>' . esc_html($copy[0]) . '</h2><p>' . esc_html($copy[1]) . '</p><div class="zpArticleNewCTA__actions"><a href="' . esc_url(self::href('/studio-wyceny/', $lang)) . '">' . esc_html($copy[2]) . '</a><a href="' . esc_url(self::href('/kontakt/', $lang)) . '">' . ($en ? 'Contact us' : 'Zapytaj o projekt') . '</a></div></div></aside>';
    }
    public static function render(string $lang = 'pl'): string {
        $d = self::data(); $lang = $lang === 'en' ? 'en' : 'pl'; $c = $d[$lang] ?? []; $en = $lang === 'en';
        if (!$c) { return ''; }
        $h = '<article lang="' . $lang . '" class="zpArticleNew zpArticlePremium zpAltioraCase" data-zp-article data-zp-altiora-case="3236" data-rankmath-main="' . esc_attr(explode(',', $c['keywords'])[0]) . '">';
        $h .= '<header class="zpArticleNew__hero"><div class="zpArticleNew__heroGrid"><div class="zpArticleNew__heroCopy"><div class="zpArticleNew__eyebrow"><span>Case study</span><span>WordPress · PL / EN / IT</span></div><h1>' . esc_html($c['title']) . '</h1><p class="zpArticleNew__lead">' . esc_html($c['lead']) . '</p><p class="zpArticleNew__intro">' . esc_html($c['intro']) . '</p></div><aside class="zpArticleNew__heroCard"><span class="zpArticleNew__heroCardKicker">' . ($en ? 'Project overview' : 'Karta realizacji') . '</span><strong>Altiora Linea</strong><div class="zpArticleNew__heroStats">';
        foreach ($c['facts'] as [$label,$value]) { $h .= '<span><b>' . esc_html($label) . '</b>' . esc_html($value) . '</span>'; }
        $h .= '</div><a class="zpAltioraCase__live" href="' . esc_url($d['live']) . '" target="_blank" rel="noopener">' . ($en ? 'Visit the website live ↗' : 'Zobacz stronę na żywo ↗') . '</a></aside></div></header>';
        $h .= '<figure class="zpArticleNew__imageBlock zpAltioraCase__cover"><img class="zpArticleNew__img" src="' . esc_url($d['image']) . '" width="2560" height="1707" alt="' . esc_attr($c['image_alt']) . '" loading="lazy" decoding="async"><figcaption>' . esc_html($c['image_caption']) . '</figcaption></figure>';
        $h .= '<nav class="zpArticleNewTOC" aria-label="' . ($en ? 'Contents' : 'Spis treści') . '"><strong>' . ($en ? 'In this case study' : 'W tym artykule') . '</strong><div>';
        foreach ($c['sections'] as $s) { $h .= '<a href="#altiora-' . esc_attr($s[0]) . '">' . esc_html($s[1]) . '</a>'; }
        $h .= '<a href="#altiora-faq">FAQ</a></div></nav><div class="zpArticleNew__layout"><div class="zpArticleNew__main">';
        foreach ($c['sections'] as $i => [$id,$title,$body]) {
            $h .= '<section id="altiora-' . esc_attr($id) . '" class="zpArticleNew__section"><p class="zpArticleNew__label">' . sprintf('%02d', $i+1) . '</p><h2>' . esc_html($title) . '</h2>' . self::links($body, $lang) . '</section>';
            if ($i === 2) { $h .= self::cta($c['ctas'][0], $lang); }
            if ($i === 5) { $h .= self::cta($c['ctas'][1], $lang); }
        }
        $h .= '<section class="zpArticleNew__section" id="altiora-faq"><h2>' . ($en ? 'Questions about the project' : 'Pytania o realizację Altiora Linea') . '</h2>';
        foreach ($c['faq'] as [$q,$a]) { $h .= '<details class="zpArticleNewFAQ"><summary>' . esc_html($q) . '</summary><p>' . esc_html($a) . '</p></details>'; }
        return $h . '</section>' . self::cta($c['ctas'][2], $lang, true) . '</div></div></article>';
    }
    public static function post_id(): int {
        $state = get_option(self::OPTION, []); $id = is_array($state) ? (int) ($state['id'] ?? 0) : 0;
        return $id && get_post_meta($id, self::MARKER, true) === '1' ? $id : 0;
    }
    public static function public_url(): string {
        $id = self::post_id();
        return $id && get_post_status($id) === 'publish' ? (string) get_permalink($id) : '';
    }
    /** 3.2.37: retain this entry point for upgrades; importer resumes existing drafts. */
    public static function prepare(): void {
        if (class_exists('ZP_Altiora_Autopublish_3237')) { ZP_Altiora_Autopublish_3237::run(); }
    }
    public static function current(): bool {
        return is_singular('post') && get_post_meta((int)get_queried_object_id(),self::MARKER,true) === '1';
    }
}
add_action('admin_init',[ZP_Altiora_Article_3236::class,'prepare'],135);
add_action('admin_notices',static function (): void {
    if (class_exists('ZP_Altiora_Autopublish_3237')) { ZP_Altiora_Autopublish_3237::notice(); }
});
add_filter('body_class',static function ($classes) { if (ZP_Altiora_Article_3236::current()) { $classes[]='zp-altiora-case-native'; } return $classes; });
add_action('wp_enqueue_scripts',static function (): void {
    if (!ZP_Altiora_Article_3236::current()) { return; }
    wp_enqueue_style('zp-altiora-case-template',ZP_SUITE_URL.'assets/article-preparation/template.css',[],ZP_SUITE_VERSION);
    wp_enqueue_style('zp-editorial-3236',ZP_SUITE_URL.'assets/css/editorial-3236.css',[],ZP_SUITE_VERSION);
},45);
// Rank Math remains the only JSON-LD publisher; no duplicate manual Article/FAQ script.
add_filter('rank_math/json_ld',static function ($graph) {
    if (!is_array($graph) || !ZP_Altiora_Article_3236::current()) { return $graph; }
    $id=get_queried_object_id(); $url=get_permalink($id); $d=ZP_Altiora_Article_3236::data();
    foreach ($graph as &$node) {
        if (!is_array($node) || !array_intersect((array)($node['@type']??[]),['Article','BlogPosting','NewsArticle'])) { continue; }
        $node['@type']='BlogPosting'; unset($node['articleType']);
        $node['headline']=get_the_title($id); $node['url']=$url;
        $node['datePublished']=get_post_time(DATE_W3C,false,$id);
        $node['dateModified']=get_post_modified_time(DATE_W3C,false,$id);
        $node['image']=['@type'=>'ImageObject','url'=>$d['image']];
        $node['inLanguage']='pl-PL';
    }
    unset($node); return $graph;
},140);

// Keep the requested topic in native category-based permalinks, without a
// hardcoded canonical URL or a new global rewrite rule.
add_filter('post_link_category', static function ($category,$categories,$post) {
    if (!is_object($post) || get_post_meta((int)$post->ID,ZP_Altiora_Article_3236::MARKER,true)!=='1') { return $category; }
    foreach ((array)$categories as $candidate) { if (($candidate->slug??'')==='strony-internetowe') { return $candidate; } }
    return $category;
},110,3);

add_action('transition_post_status',static function($new,$old,$post): void {
    if ($new!==$old && is_object($post) && get_post_meta((int)$post->ID,ZP_Altiora_Article_3236::MARKER,true)==='1') { do_action('zp_html_cache_purge'); }
},100,3);
