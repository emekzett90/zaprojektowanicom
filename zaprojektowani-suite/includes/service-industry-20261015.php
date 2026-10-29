<?php
/** Keep the complete industry photo visible in the first service scope card. */
if (!defined('ABSPATH')) { exit; }

function zp_service_industry_photo_context_20261015(): bool {
    if (!function_exists('zp_seo_plan_active') || !zp_seo_plan_active() || is_admin()
        || !did_action('wp') || !is_singular('page') || is_preview() || is_feed()
        || !function_exists('zp_seo_industry_page_path') || !function_exists('zp_seo_industry_key')) { return false; }
    $id = (int)get_queried_object_id();
    return $id > 0 && get_post_status($id) === 'publish'
        && (string)get_post_field('post_password', $id) === ''
        && zp_seo_industry_key(zp_seo_industry_page_path()) !== '';
}

// The original industry stylesheet enqueues at 1001; the dependency also preserves
// order if another request changes registration order. No HTML or SEO data change.
add_action('wp_enqueue_scripts', static function (): void {
    if (!zp_service_industry_photo_context_20261015()) { return; }
    wp_enqueue_style('zp-service-industry-photo', ZP_SUITE_URL . 'assets/css/service-industry-20261015.css', ['zp-industries-297'], ZP_SUITE_VERSION);
}, 1002);
