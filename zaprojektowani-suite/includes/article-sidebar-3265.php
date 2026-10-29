<?php
/**
 * Readable article sidebar for the existing SIDEBAR BLOG Code Snippet.
 * Presentation only: preserve its selected articles, links and article content.
 */
if (!defined('ABSPATH')) { exit; }

function zp_article_sidebar_3265_styles(): void {
    if (is_admin() || !is_singular('post') || is_feed() || is_preview()) { return; }
    $path = ZP_SUITE_PATH . 'assets/css/article-sidebar-3265.css';
    if (!is_readable($path)) { return; }
    $css = file_get_contents($path);
    if (!is_string($css) || $css === '') { return; }
    // Small, route-scoped CSS in the existing response; no new blocking request.
    echo '<style id="zp-article-sidebar-3265">' . $css . '</style>';
}
add_action('wp_head', 'zp_article_sidebar_3265_styles', 99);
