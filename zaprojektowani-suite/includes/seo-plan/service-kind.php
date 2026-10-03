<?php
if (!defined('ABSPATH')) { exit; }

/**
 * ZP Suite 2.3.0 — which service template a request uses.
 *
 * The nationwide service pages reuse the Katowice templates, so every place that
 * used to check one Katowice slug (CSS/JS gating, performance tweaks, header
 * overlay) asks these helpers instead. Katowice pages behave exactly as before.
 */
function zp_suite_service_slugs(): array {
  return [
    'strony' => ['strony-internetowe-katowice', 'tworzenie-stron-internetowych'],
    'sklepy' => ['sklepy-internetowe-katowice', 'tworzenie-sklepow-internetowych'],
    'logo'   => ['logo-branding-katowice', 'projektowanie-logo', 'identyfikacja-wizualna'],
  ];
}

/** 'strony' | 'sklepy' | 'logo' | '' for a request URI (default: the current one). */
function zp_suite_service_kind($uri = null): string {
  if ($uri === null) { $uri = isset($_SERVER['REQUEST_URI']) ? (string) wp_unslash($_SERVER['REQUEST_URI']) : ''; }
  $path = trim((string) wp_parse_url((string) $uri, PHP_URL_PATH), '/');
  $path = strtolower(rawurldecode($path));
  foreach (zp_suite_service_slugs() as $kind => $slugs) {
    if (in_array($path, $slugs, true)) { return $kind; }
  }
  return '';
}

/** True on a page that renders the given service template (Katowice or nationwide). */
function zp_suite_is_service_page(string $kind, $uri = null): bool {
  if ($uri === null && function_exists('is_page') && did_action('wp')) {
    foreach (zp_suite_service_slugs()[$kind] ?? [] as $slug) {
      if (is_page($slug)) { return true; }
    }
  }
  return zp_suite_service_kind($uri) === $kind;
}

/** Regex alternation of all service slugs of a kind (for inline JS path checks). */
function zp_suite_service_slug_regex(string $kind = ''): string {
  $all = [];
  foreach (zp_suite_service_slugs() as $k => $slugs) {
    if ($kind === '' || $kind === $k) { $all = array_merge($all, $slugs); }
  }
  return implode('|', array_map('preg_quote', $all));
}
