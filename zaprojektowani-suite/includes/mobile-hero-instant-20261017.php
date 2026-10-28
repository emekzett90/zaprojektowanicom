<?php
/** Mobile home/service heroes: show their finished visual state without entrance delays. */
if (!defined('ABSPATH')) { exit; }

function zp_suite_mobile_hero_instant_20261017(): void {
    if (is_admin()) { return; }
    $home = function_exists('zp_suite_308_is_home_like') && zp_suite_308_is_home_like();
    $web_service = function_exists('zp_suite_strony_internetowe_katowice_v2_request')
        && zp_suite_strony_internetowe_katowice_v2_request();
    if (!$home && !$web_service) { return; }
    $file = ZP_SUITE_PATH . 'assets/css/mobile-hero-instant-20261017.css';
    if (!is_readable($file)) { return; }
    // Critical first-screen rules deliberately stay inline. No additional HTTP request or UA-specific cache.
    echo '<style id="zp-suite-mobile-hero-instant-20261017" data-zp-critical="1">' . file_get_contents($file) . '</style>';
}

// Registered after all legacy entrance patches; media queries preserve the desktop choreography.
add_action('wp_head', 'zp_suite_mobile_hero_instant_20261017', PHP_INT_MAX);
