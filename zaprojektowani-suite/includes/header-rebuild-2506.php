<?php
/**
 * ZP Suite v2.2.506 — Header rebuild (clean header.css authoritative).
 *
 * The new assets/css/blocks/header.css fully describes the header in every state
 * (overlay / sticky / mega / mobile / light pages). To stop the old, scattered
 * !important override blocks from fighting it, this file removes those legacy
 * header-only <style> blocks from the final HTML by id (output-buffer, surgical:
 * only the listed header style blocks are removed — nothing else is touched).
 *
 * EXPERIMENTAL / STAGING: verify on staging before production. To roll back,
 * simply remove this require + restore the previous header.css (or use v2.2.505).
 */
if (!defined('ABSPATH')) {
  exit;
}

if (!defined('ZP_SUITE_HEADER_REBUILD_2506')) {
  define('ZP_SUITE_HEADER_REBUILD_2506', true);
}

add_action('template_redirect', function () {
  if (is_admin()) {
    return;
  }
  if (function_exists('zp_suite_is_frontend_request') && !zp_suite_is_frontend_request()) {
    return;
  }

  // Legacy header-only override <style> blocks now superseded by the clean header.css.
  $ids = array(
    'zp-suite-1852-final-front-css',
    'zp-suite-1875-logo-mobile-glass-final',
    'zp-suite-1876-header-logo-hard-css',
    'zp-suite-2121-single-post-header-css',
    'zp-suite-2123-hero-nav-separator-css',
    'zp-suite-2129-final-header-state-css',
    'zp-suite-2130-one-header-line-final',
    'zp-suite-2131-no-header-divider-css',
    'zp-suite-2132-early-no-divider-css',
    'zp-suite-2147-header-line-and-mobile-fix',
    'zp-suite-2148-header-line-final-visibility',
    'zp-suite-2300-header-shadow-reset-css',
    'zp-suite-2300b-header-no-shadow-hard-css',
    'zp-suite-2301-header-clean-final-css',
  );

  ob_start(function ($html) use ($ids) {
    if (!is_string($html) || stripos($html, 'zp-suite-') === false) {
      return $html;
    }
    foreach ($ids as $id) {
      $html = preg_replace('#<style[^>]*\bid="' . preg_quote($id, '#') . '"[^>]*>.*?</style>#is', '', $html, 1);
    }
    return $html;
  });
}, -9998);
