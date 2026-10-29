<?php
/**
 * Additional examples and acceptance checklists for the nine articles selected
 * from the 17 October 2026 visibility report. Display-only: stored Elementor
 * widgets, post titles, publication dates, slugs and canonical URLs stay intact.
 * No remote requests, database writes or JavaScript are needed to show the text.
 */
if (!defined('ABSPATH')) { exit; }

function zp_article_growth_20261017_paths(): array {
    return [
        '/strony-internetowe/jak-stworzyc-strone-internetowa/',
        '/logo-branding/identyfikacja-wizualna-firmy/',
        '/logo-branding/rebranding-firmy/',
        '/logo-branding/logo-wektorowe-pliki-logo/',
        '/seo-i-konwersja/landing-page-przyklady/',
        '/strony-www/strona-internetowa-dla-malej-firmy/',
        '/strony-internetowe/rodzaje-stron-internetowych/',
        '/strony-internetowe/tworzenie-stron-internetowych-profesjonalny-proces-od-strategii-do-wdrozenia/',
        '/strony-internetowe/strona-internetowa-dla-firmy-co-powinna-zawierac/',
    ];
}

function zp_article_growth_20261017_data(): array {
    static $data = null;
    if ($data === null) {
        $data = (array) require __DIR__ . '/article-growth-20261017-data.php';
    }
    return $data;
}

/** Return an exact published Polish article path, or an empty string. */
function zp_article_growth_20261017_context(): string {
    if (is_admin() || is_feed() || !is_singular('post')
        || (function_exists('is_preview') && is_preview())
        || (defined('REST_REQUEST') && REST_REQUEST)
        || (function_exists('wp_doing_ajax') && wp_doing_ajax())
        || (function_exists('zp_seo_plan_active') && !zp_seo_plan_active())
        || (function_exists('zp_seo_plan_is_en') && zp_seo_plan_is_en())
        || (function_exists('zpl_language') && zpl_language() === 'en')) {
        return '';
    }
    // Also protect /en/ when the language router has not set its state yet.
    $request = isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '';
    if (preg_match('~(?:^|/)en(?:/|\?|$)~i', $request)) { return ''; }
    $id = (int) get_queried_object_id();
    if (!$id || get_post_status($id) !== 'publish'
        || (function_exists('post_password_required') && post_password_required($id))
        || (in_the_loop() && (int) get_the_ID() !== $id)) { return ''; }
    $path = function_exists('zp_seo_plan_path')
        ? zp_seo_plan_path((string) get_permalink($id))
        : '/' . trim((string) wp_parse_url((string) get_permalink($id), PHP_URL_PATH), '/') . '/';
    if (!in_array($path, zp_article_growth_20261017_paths(), true)
        || (string) get_post_field('post_name', $id) !== basename($path)) { return ''; }
    return $path;
}

/** Attributes of a single opening tag; accepts either HTML quote style. */
function zp_article_growth_20261017_attribute(string $tag, string $name): string {
    if (preg_match('~\b' . preg_quote($name, '~') . '\s*=\s*(["\'])(.*?)\1~is', $tag, $m)) {
        return html_entity_decode($m[2], ENT_QUOTES, 'UTF-8');
    }
    return '';
}

/**
 * Balanced range [open start, close end, open end, close start]. Nested article,
 * section and div elements occur in the established templates. A non-greedy
 * outer-article regex would stop at a comparison card and break the layout.
 */
function zp_article_growth_20261017_range(string $html, string $tag, string $attribute, string $value): ?array {
    $offset = 0;
    $pattern = '~<' . preg_quote($tag, '~') . '\b[^>]*>~i';
    while (preg_match($pattern, $html, $m, PREG_OFFSET_CAPTURE, $offset)) {
        $opening = $m[0][0];
        $start = (int) $m[0][1];
        $offset = $start + strlen($opening);
        $actual = zp_article_growth_20261017_attribute($opening, $attribute);
        $matches = $attribute === 'class'
            ? in_array($value, preg_split('~\s+~', trim($actual)), true)
            : $actual === $value;
        if (!$matches) { continue; }
        $depth = 1;
        $scan = $offset;
        // Raw-text elements and comments are skipped so examples containing an
        // HTML tag in script/style text cannot terminate the article container.
        $tokens = '~<!--[\s\S]*?-->|<(script|style)\b[^>]*>[\s\S]*?</\1\s*>|<(/?)' . preg_quote($tag, '~') . '\b[^>]*>~i';
        while (preg_match($tokens, $html, $token, PREG_OFFSET_CAPTURE, $scan)) {
            $text = $token[0][0];
            $at = (int) $token[0][1];
            $scan = $at + strlen($text);
            if (strncmp($text, '<!--', 4) === 0 || !empty($token[1][0])) { continue; }
            $depth += isset($token[2][0]) && $token[2][0] === '/' ? -1 : 1;
            if ($depth === 0) { return [$start, $scan, $offset, $at]; }
        }
        return null;
    }
    return null;
}

/** All image metadata is local and verified; existing WebP assets are reused. */
function zp_article_growth_20261017_image(string $key, array $images): string {
    if (!isset($images[$key])) { return ''; }
    $image = $images[$key];
    $relative = 'assets/img/wpisy/' . basename((string) $image['file']);
    if (!is_file(ZP_SUITE_PATH . $relative)) { return ''; }
    return '<figure class="zpArticleNew__imageBlock zpArticleGrowth__figure">'
        . '<img class="zpArticleNew__img zpArticleGrowth__image" src="' . esc_url(ZP_SUITE_URL . $relative) . '"'
        . ' width="' . (int) $image['width'] . '" height="' . (int) $image['height'] . '"'
        . ' alt="' . esc_attr((string) $image['alt']) . '" loading="lazy" decoding="async"'
        . ' sizes="(max-width: 767px) calc(100vw - 40px), (max-width: 1100px) calc(100vw - 64px), 860px">'
        . '<figcaption>' . esc_html((string) $image['caption']) . '</figcaption></figure>';
}

function zp_article_growth_20261017_expand(string $html, array $images): string {
    $body = preg_replace_callback('~\{\{image:([a-z0-9-]+)\}\}~', static function ($match) use ($images) {
        return zp_article_growth_20261017_image($match[1], $images);
    }, $html);
    $body = preg_replace_callback('~\{\{case:(apartament|siemianowski)\}\}~', static function ($match) {
        $url = class_exists('ZP_Case_Studies_20261015')
            ? ZP_Case_Studies_20261015::case_url($match[1], 'pl') : '';
        return esc_url($url !== '' ? $url : home_url('/realizacje/'));
    }, (string) $body);
    $body = preg_replace_callback('~\{\{link:(/[a-z0-9/-]+/)\}\}~', static function ($match) {
        return esc_url(home_url($match[1]));
    }, (string) $body);
    return (string) $body;
}

function zp_article_growth_20261017_section(array $section, array $images): string {
    $body = zp_article_growth_20261017_expand((string) $section['html'], $images);
    return '<section id="' . esc_attr((string) $section['id']) . '"'
        . ' class="zpArticleNew__section zpArticleGrowth" data-zp-article-growth="20261017">'
        . '<p class="zpArticleNew__label">' . esc_html((string) $section['label']) . '</p>'
        . '<h2>' . esc_html((string) $section['title']) . '</h2>' . $body . '</section>';
}

/** Keep the existing TOC structure and position new anchors next to their text. */
function zp_article_growth_20261017_toc(string $article, array $section, string $after, string $before): string {
    $range = zp_article_growth_20261017_range($article, 'nav', 'class', 'zpArticleNewTOC');
    if ($range === null) { return $article; }
    $nav = substr($article, $range[0], $range[1] - $range[0]);
    $target = '#' . (string) $section['id'];
    if (preg_match('~\bhref\s*=\s*(["\'])' . preg_quote($target, '~') . '\1~i', $nav)) { return $article; }
    $link = '<a href="' . esc_attr($target) . '">' . esc_html((string) $section['toc']) . '</a>';
    $insert = null;
    foreach ([[$after, true], [$before, false]] as $placement) {
        if ($placement[0] === '') { continue; }
        if (preg_match('~<a\b[^>]*href\s*=\s*(["\'])#' . preg_quote($placement[0], '~') . '\1[^>]*>.*?</a>~is', $nav, $m, PREG_OFFSET_CAPTURE)) {
            $insert = (int) $m[0][1] + ($placement[1] ? strlen($m[0][0]) : 0);
            break;
        }
    }
    if ($insert === null) {
        // The established TOC has one link container; if it is absent, append
        // directly before </nav> rather than creating an empty or invalid div.
        $last_div = strripos($nav, '</div>');
        $insert = $last_div !== false ? $last_div : (int) strripos($nav, '</nav>');
    }
    $nav = substr_replace($nav, $link, $insert, 0);
    return substr_replace($article, $nav, $range[0], $range[1] - $range[0]);
}

/** Pure HTML transformation also used by the release's fixture tests. */
function zp_article_growth_20261017_transform(string $html, string $path): string {
    if ($html === '' || !in_array($path, zp_article_growth_20261017_paths(), true)) { return $html; }
    $outer = zp_article_growth_20261017_range($html, 'article', 'class', 'zpArticleNew');
    if ($outer === null) { return $html; }
    $article = substr($html, $outer[0], $outer[1] - $outer[0]);
    $opening = substr($article, 0, $outer[2] - $outer[0]);
    if (preg_match('~^en(?:-|$)~i', zp_article_growth_20261017_attribute($opening, 'lang'))) { return $html; }
    $data = zp_article_growth_20261017_data();
    $entry = $data['articles'][$path] ?? [];
    $changed = false;
    foreach ((array) ($entry['text_fixes'] ?? []) as $fix) {
        if (count($fix) !== 2 || $fix[0] === '' || strpos($article, $fix[0]) === false) { continue; }
        $replacement = zp_article_growth_20261017_expand((string) $fix[1], []);
        $article = str_replace((string) $fix[0], $replacement, $article);
        $changed = true;
    }
    foreach ((array) ($entry['sections'] ?? []) as $section) {
        $sid = (string) $section['id'];
        if (preg_match('~\bid\s*=\s*(["\'])' . preg_quote($sid, '~') . '\1~i', $article)) { continue; }
        // The landing-page article uses a div; the other guides use main.
        $body_range = zp_article_growth_20261017_range($article, 'main', 'class', 'zpArticleNew__main');
        if ($body_range === null) { $body_range = zp_article_growth_20261017_range($article, 'div', 'class', 'zpArticleNew__main'); }
        if ($body_range === null) { continue; }
        $body = substr($article, $body_range[2], $body_range[3] - $body_range[2]);
        $after = (string) ($section['after'] ?? '');
        $before = '';
        $anchor = $after !== '' ? zp_article_growth_20261017_range($body, 'section', 'id', $after) : null;
        if ($anchor !== null) {
            $at = $anchor[1];
        } else {
            $after = '';
            $at = strlen($body);
            foreach (['faq', 'faq-landing-page', 'zrodla', 'podsumowanie'] as $fallback) {
                $candidate = zp_article_growth_20261017_range($body, 'section', 'id', $fallback);
                if ($candidate !== null && $candidate[0] < $at) { $at = $candidate[0]; $before = $fallback; }
            }
        }
        $block = "\n" . zp_article_growth_20261017_section($section, (array) ($data['images'] ?? [])) . "\n";
        $article = substr_replace($article, $block, $body_range[2] + $at, 0);
        $article = zp_article_growth_20261017_toc($article, $section, $after, $before);
        $changed = true;
    }
    if (!$changed) { return $html; }
    return substr_replace($html, $article, $outer[0], $outer[1] - $outer[0]);
}

function zp_article_growth_20261017_filter(string $html): string {
    static $busy = false;
    if ($busy || $html === '' || strpos($html, 'zpArticleNew') === false) { return $html; }
    $path = zp_article_growth_20261017_context();
    if ($path === '') { return $html; }
    $busy = true;
    try { return zp_article_growth_20261017_transform($html, $path); }
    finally { $busy = false; }
}

// Existing SEO sections and precise title fixes run at 20–22. These additions
// follow them and remain idempotent when Elementor then passes through WordPress.
add_filter('the_content', 'zp_article_growth_20261017_filter', 29);
add_filter('elementor/frontend/the_content', 'zp_article_growth_20261017_filter', 29);

add_action('wp_enqueue_scripts', static function () {
    if (zp_article_growth_20261017_context() === '') { return; }
    wp_enqueue_style('zp-article-growth-20261017', ZP_SUITE_URL . 'assets/css/article-growth-20261017.css', [], '20261017.1');
}, 40);
