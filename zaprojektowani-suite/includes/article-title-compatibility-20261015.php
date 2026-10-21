<?php
/** Repair one verified historical English title translation without altering the applied article manifest. */
if (!defined('ABSPATH')) { exit; }

function zp_article_title_compatibility_20261015($layers, $lang, $source): array {
    $layers = is_array($layers) ? $layers : [];
    $path = '/logo-branding/logo-na-koszulce-kiedy-uproscic-do-haftu-lub-nadruku/';
    $pl = 'Logo na koszulce: kiedy uprościć znak do haftu?';
    if ($lang !== 'en' || $source !== $path || is_admin() || !is_singular('post')
        || (int)get_queried_object_id() !== 5788 || !class_exists('ZPL\\Html') || !class_exists('ZPL\\Page')) { return $layers; }
    $post = get_post(5788);
    if (!$post instanceof WP_Post || $post->post_status !== 'publish' || $post->post_password !== ''
        || (string)wp_parse_url(get_permalink(5788), PHP_URL_PATH) !== $path
        || (string)get_post_meta(5788, 'rank_math_title', true) !== $pl) { return $layers; }
    $key = \ZPL\Html::norm($pl);
    $en = 'T-shirt logo: when should you simplify it for embroidery?';
    array_unshift($layers, [$key=>$en, \ZPL\Page::variant('title',$key)=>$en, \ZPL\Page::variant('meta',$key)=>$en]);
    // Dict::priority prepends the owner's explicit overrides ahead of all module layers.
    return $layers;
}
add_filter('zpl_priority_dictionary', 'zp_article_title_compatibility_20261015', PHP_INT_MAX, 3);
