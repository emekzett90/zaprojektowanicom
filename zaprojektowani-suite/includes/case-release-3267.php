<?php
/** Scoped project experiences; no third-party scripts, requests or storage. */
if (!defined('ABSPATH')) { exit; }
require_once __DIR__ . '/case-experience-3267.php';
add_action('wp_enqueue_scripts', static function (): void {
    $key = class_exists('ZP_Case_Pages_20261018') ? ZP_Case_Pages_20261018::current_key() : '';
    if (!in_array($key, ['swiat-grilli', 'altiora-linea', 'siemianowski'], true)) { return; }
    wp_enqueue_style('zp-case-experience-3267', ZP_SUITE_URL . 'assets/css/case-experience-3267.css', ['zp-case-refinement-3266'], ZP_SUITE_VERSION);
}, 104);
