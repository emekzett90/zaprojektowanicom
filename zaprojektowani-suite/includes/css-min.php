<?php
if (!defined('ABSPATH')) { exit; }

/**
 * ZP Suite 2.4.0 — minified stylesheets (audit point 6).
 *
 * Each large stylesheet in assets/ has a .min.css twin made by tools/minify_css.py
 * (comments and whitespace removed, rules unchanged). assets/css-min.json holds the size of
 * each source at that moment; the front end loads the twin only while the source still has
 * that size, so a source edited without re-running the tool is served as is. Pausing the
 * SEO plan loads the sources again.
 */

function zp_suite_min_css_rel(string $rel): string {
  if (substr($rel, -4) !== '.css' || substr($rel, -8) === '.min.css') { return $rel; }
  if (function_exists('zp_seo_plan_active') && !zp_seo_plan_active()) { return $rel; }
  static $manifest = null;
  if ($manifest === null) {
    $json = @file_get_contents(ZP_SUITE_PATH . 'assets/css-min.json');
    $manifest = is_string($json) ? (array) json_decode($json, true) : [];
  }
  if (!isset($manifest[$rel])) { return $rel; }
  $min = substr($rel, 0, -4) . '.min.css';
  return (is_file(ZP_SUITE_PATH . $min) && (int) @filesize(ZP_SUITE_PATH . $rel) === (int) $manifest[$rel]) ? $min : $rel;
}

add_filter('style_loader_src', function ($src) {
  if (is_admin() || !is_string($src) || strpos($src, ZP_SUITE_URL . 'assets/') !== 0) { return $src; }
  $parts = explode('?', $src, 2);
  $rel = substr($parts[0], strlen(ZP_SUITE_URL));
  $min = zp_suite_min_css_rel($rel);
  if ($min === $rel) { return $src; }
  return ZP_SUITE_URL . $min . (isset($parts[1]) ? '?' . $parts[1] : '');
}, 20);
