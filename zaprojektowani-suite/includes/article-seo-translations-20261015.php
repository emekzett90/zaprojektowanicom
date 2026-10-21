<?php
/** Exact English equivalents of the reviewed article metadata, scoped to its current saved source. */
if (!defined('ABSPATH')) { exit; }

function zp_article_seo_translations_20261015($layers, $lang, $source): array {
    $layers = is_array($layers) ? $layers : [];
    if ($lang !== 'en' || is_admin() || !is_singular('post')
        || !function_exists('zp_article_seo_manifest_20261015') || !class_exists('ZPL\\Html')) { return $layers; }
    $id = (int)get_queried_object_id();
    $post = get_post($id);
    $spec = zp_article_seo_manifest_20261015()[$id] ?? null;
    if (!$post instanceof WP_Post || $post->post_status !== 'publish' || $post->post_password !== '' || !is_array($spec)) { return $layers; }
    $path = (string)wp_parse_url(get_permalink($id), PHP_URL_PATH);
    if (!is_string($source) || $source !== $path) { return $layers; }
    $map = [];
    foreach (['rank_math_title', 'rank_math_description'] as $key) {
        $pl = $spec['seo_meta'][$key] ?? null;
        $en = $spec['seo_meta_en'][$key] ?? null;
        if (!is_string($pl) || !is_string($en) || trim($en) === '' || strlen($en) > 1200
            || preg_match('/[<>\x00-\x1f\x7f]/u', $en)
            || (string)get_post_meta($id, $key, true) !== $pl) { continue; }
        $normalized = \ZPL\Html::norm($pl);
        $map[$normalized] = $en;
        // Page::translate checks the SEO-specific variant before the ordinary key.
        if (class_exists('ZPL\\Page')) {
            // Social title tags use the meta kind, while <title> uses title.
            foreach ($key === 'rank_math_title' ? ['title', 'meta'] : ['meta'] as $kind) {
                $variant = \ZPL\Page::variant($kind, $normalized);
                if ($variant !== '') { $map[$variant] = $en; }
            }
        }
    }
    if ($map) { array_unshift($layers, $map); }
    return $layers;
}
add_filter('zpl_priority_dictionary', 'zp_article_seo_translations_20261015', PHP_INT_MAX, 3);
