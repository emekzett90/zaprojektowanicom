<?php
/**
 * Prevent global-object scanners from invoking Elementor's obsolete aliases.
 * Elementor 4.0.8 still exposes three enumerable getters which schedule a
 * deprecation warning on every read. Meta's automaticmatchingregex scanner
 * reaches them through Object.keys(), without actually using the old API.
 */
if (!defined('ABSPATH')) { exit; }

add_action('elementor/common/after_register_scripts', static function () {
  if (is_admin() || !wp_script_is('elementor-common-modules', 'registered')) { return; }
  $file = ZP_SUITE_PATH . 'assets/js/elementor-compat.js';
  if (is_readable($file)) {
    // The alias object is created by this handle. Run immediately afterwards,
    // before dependent scripts; no polling and no changes to Meta's events.
    wp_add_inline_script('elementor-common-modules', file_get_contents($file), 'after');
  }
});
