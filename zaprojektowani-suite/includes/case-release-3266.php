<?php
/** Scoped presentation release; no content imports, remote calls or analytics changes. */
if (!defined('ABSPATH')) { exit; }
require_once __DIR__ . '/case-results-3266.php';
add_action('wp_enqueue_scripts', static function (): void {
    $key = class_exists('ZP_Case_Pages_20261018') ? ZP_Case_Pages_20261018::current_key() : '';
    if ($key === '') { return; }
    wp_enqueue_style('zp-case-refinement-3266', ZP_SUITE_URL . 'assets/css/case-studies-refinement-3266.css', ['zp-case-studies-layout-3242'], ZP_SUITE_VERSION);
    if ($key === 'apartament-piekna') {
        wp_enqueue_style('zp-case-results-3266', ZP_SUITE_URL . 'assets/css/case-results-3266.css', ['zp-case-refinement-3266'], ZP_SUITE_VERSION);
    }
}, 103);
