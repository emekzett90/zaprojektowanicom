<?php
/** Desktop hero photographs/mockups only; no home/Spline or mobile image changes. */
if (!defined('ABSPATH')) { exit; }

function zp_service_hero_image_fit_context_20261015(): bool {
    if (is_admin() || !did_action('wp') || !is_singular('page') || is_preview() || is_feed()
        || !function_exists('zp_seo_plan_active') || !zp_seo_plan_active()
        || !function_exists('zp_seo_service_registry')) { return false; }
    $id = (int)get_queried_object_id();
    if (!$id || get_post_status($id) !== 'publish' || (string)get_post_field('post_password', $id) !== '') { return false; }
    $path = class_exists('\\ZPL\\Router') ? (string)\ZPL\Router::$source : '';
    if ($path === '') { $path = zp_seo_plan_path((string)get_permalink($id)); }
    return in_array($path, ['/wiedza/', '/kampanie-reklamowe/', '/kontakt/', '/o-nas/', '/realizacje/'], true)
        || isset(zp_seo_service_registry()[$path]);
}

/** The extra desktop enlargement/alignment belongs to these eight industry pages only. */
function zp_service_hero_industry_image_fit_context_20261015(): bool {
    if (!zp_service_hero_image_fit_context_20261015()) { return false; }
    $path = class_exists('\\ZPL\\Router') ? (string)\ZPL\Router::$source : '';
    if ($path === '') { $path = zp_seo_plan_path((string)get_permalink(get_queried_object_id())); }
    return in_array($path, [
        '/strony-internetowe-dla-kancelarii/', '/strony-internetowe-dla-lekarzy/',
        '/strony-internetowe-dla-deweloperow/', '/strony-internetowe-dla-salonow-beauty/',
        '/strony-internetowe-dla-restauracji/', '/strony-internetowe-dla-hoteli/',
        '/strony-internetowe-dla-fotografow/', '/strony-internetowe-dla-trenerow-personalnych/',
    ], true);
}

function zp_service_hero_industry_image_fit_classes_20261015($classes): array {
    $classes = is_array($classes) ? $classes : [];
    if (zp_service_hero_industry_image_fit_context_20261015()) { $classes[] = 'zp-industry-hero-fit'; }
    return array_values(array_unique($classes));
}
add_filter('body_class', 'zp_service_hero_industry_image_fit_classes_20261015');

add_action('wp_enqueue_scripts', static function (): void {
    if (!zp_service_hero_image_fit_context_20261015()) { return; }
    $deps = wp_style_is('zp-suite-service-hero-global', 'registered') ? ['zp-suite-service-hero-global'] : [];
    wp_enqueue_style('zp-service-hero-image-fit', ZP_SUITE_URL . 'assets/css/service-hero-image-fit-20261015.css', $deps, ZP_SUITE_VERSION);
}, 10001);
