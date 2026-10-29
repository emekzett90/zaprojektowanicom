<?php
/** One-time cache refresh and honest sitemap dates for this content update. */
if (!defined('ABSPATH')) { exit; }

add_action('wp_loaded', static function (): void {
    if (get_option('zp_suite_growth_cache_20261017', '') !== 'pending') { return; }
    // Uses the existing cache generation mechanism and respects a cache disabled by its owner.
    do_action('zp_html_cache_purge');
    update_option('zp_suite_growth_cache_20261017', 'queued', true);
}, 110);

add_action('admin_init', static function (): void {
    if (!current_user_can('manage_options')
        || get_option('zp_suite_growth_dates_20261017', '') === '3.2.32'
        || !function_exists('zp_seo_plan_active') || !zp_seo_plan_active()
        || !function_exists('zp_ai_on') || !zp_ai_on()
        || !function_exists('zp_ai_changed_at_display')
        || !function_exists('zp_article_growth_20261017_paths')
        || !function_exists('zp_industry_work_plan_20261015')) { return; }
    $paths = array_merge(zp_article_growth_20261017_paths(), array_keys(zp_industry_work_plan_20261015()));
    // Only substantive new article/industry sections get a date, once, after installation.
    // The helper resolves published URLs and invalidates existing sitemap caches; native post dates stay intact.
    zp_ai_changed_at_display($paths, 'przykłady, zdjęcia i zakres stron branżowych — aktualizacja 3.2.32', false);
    update_option('zp_suite_growth_dates_20261017', '3.2.32', false);
}, 120);
