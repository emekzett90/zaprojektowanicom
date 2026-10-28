<?php
/** One first-paint header contract for phones, tablets and small laptops. */
if (!defined('ABSPATH')) { exit; }

add_action('wp_head', static function (): void {
    if (is_admin()) { return; }
    $path = ZP_SUITE_PATH . 'assets/css/header-responsive-3268.css';
    if (is_readable($path)) {
        // Small, header-only sheet. Inline delivery avoids a layout/theme change on arrival.
        echo '<style id="zp-header-responsive-3268">' . file_get_contents($path) . '</style>';
    }
}, PHP_INT_MAX);
