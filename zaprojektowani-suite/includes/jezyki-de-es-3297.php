<?php
/**
 * 3.2.97: the German and Spanish versions next to the rest of ZP Suite.
 *
 * Tłumacz AI reads every Polish page as visitors get it, after the last corrections that ZP Suite makes over the
 * finished HTML (filter zp_en_public_price_html for 'pl': the verified review count of home-refresh-20261016.php,
 * the descriptions shortened after the Ahrefs audit in ahrefs-20261022.php). The language module translates the
 * page before those corrections, so for German and Spanish it gets them first (filter zpl_ai_source_html): every text
 * is the one that was translated. Prices in euro: eur-public-currency-20261030.php.
 *
 * Client reviews (templates/legacy/legacy-reviews.php) are quoted in Polish. English shows its own translations of them;
 * German and Spanish translate them with the rest of the page and, like English ("Translated from Polish"), say so on
 * hover. The translator reads the page through the same step (filter zpte_ai_source_html), so the keys match.
 */
if (!defined('ABSPATH')) { exit; }

function zp_jezyki_3297_quotes($html) {
    if (!is_string($html) || strpos($html, ' lang="pl">') === false) { return $html; }
    return (string) preg_replace('~<(blockquote|span class="zpReviews24__excerpt") lang="pl">~', '<$1 lang="pl" title="Opinia przetłumaczona z języka polskiego">', $html);
}
add_filter('zpte_ai_source_html', 'zp_jezyki_3297_quotes', 10, 1);

add_filter('zpl_ai_source_html', static function ($html, $lang = '', $source = '') {
    if (!is_string($html) || !is_string($source) || $source === '' || $source[0] !== '/') { return $html; }
    try {
        // The Ahrefs rows are kept by Polish address: the module reads the address of the request.
        if (function_exists('zp_ahrefs_20261022_meta_html') && class_exists('ZPL\\Router')) {
            $was = \ZPL\Router::$external;
            \ZPL\Router::$external = (string) (wp_parse_url(home_url($source), PHP_URL_PATH) ?: $source);
            try { $html = zp_ahrefs_20261022_meta_html($html, 'pl', $source); } finally { \ZPL\Router::$external = $was; }
        }
        if (function_exists('zp_reviews_truth_document')) { $html = zp_reviews_truth_document($html, 'pl', $source); }
    } catch (\Throwable $e) {
        // The page is translated as it is.
    }
    return zp_jezyki_3297_quotes($html);
}, 10, 3);
