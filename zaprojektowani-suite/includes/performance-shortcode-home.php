<?php
/**
 * Skip Elementor's widget runtime on the public, shortcode-only homepage.
 * All styles, jQuery, theme code, Suite code and the Spline loader are retained.
 * Unknown layouts/settings fail closed and keep the original document.
 */
if (!defined('ABSPATH')) { exit; }

/** Exact WordPress script IDs and expected sources, as observed on the homepage. */
function zp_suite_shortcode_home_runtime_scripts(): array {
  return [
    'elementor-webpack-runtime-js' => '/elementor/assets/js/webpack.runtime.min.js',
    'elementor-frontend-modules-js' => '/elementor/assets/js/frontend-modules.min.js',
    'elementor-frontend-js' => '/elementor/assets/js/frontend.min.js',
    'elementor-pro-webpack-runtime-js' => '/elementor-pro/assets/js/webpack-pro.runtime.min.js',
    'elementor-pro-frontend-js' => '/elementor-pro/assets/js/frontend.min.js',
    'pro-elements-handlers-js' => '/elementor-pro/assets/js/elements-handlers.min.js',
  ];
}

/** Verify actual HTML tags, excluding strings inside scripts, styles and comments. */
function zp_suite_shortcode_home_runtime_safe(string $html): bool {
  if (!class_exists('WP_HTML_Tag_Processor') || stripos($html, '</html>') === false) { return false; }
  if (preg_match('~["\']hasPopUps["\']\s*:\s*true\b~i', $html)) { return false; }
  $tags = new WP_HTML_Tag_Processor($html);
  $scripts = zp_suite_shortcode_home_runtime_scripts();
  $seen = [];
  $roots = [];
  $widgets = 0;
  $containers = 0;
  $markers = [];
  while ($tags->next_tag()) {
    $element_type = $tags->get_attribute('data-element_type');
    $e_type = $tags->get_attribute('data-e-type');
    foreach ([$element_type, $e_type] as $type) {
      // Elementor 4 atomic elements may have no traditional data-widget_type.
      if ($type !== null && !in_array($type, ['container', 'widget'], true)) { return false; }
    }
    if ($element_type !== null && $e_type !== null && $element_type !== $e_type) { return false; }
    $type = $element_type ?? $e_type;
    if ($type === 'container') { $containers++; }
    $root = $tags->get_attribute('data-elementor-type');
    if ($root !== null) {
      if (!in_array($root, ['header', 'wp-page', 'footer'], true)) { return false; }
      $roots[$root] = ($roots[$root] ?? 0) + 1;
    }
    $widget = $tags->get_attribute('data-widget_type');
    if ($type === 'widget' && $widget !== 'shortcode.default') { return false; }
    if ($widget !== null) {
      if ($widget !== 'shortcode.default' || $type !== 'widget') { return false; }
      $widgets++;
    }
    $settings = $tags->get_attribute('data-settings');
    if ($settings !== null) {
      if (!is_string($settings)) { return false; }
      $settings = json_decode($settings, true);
      if (!is_array($settings)) { return false; }
      // These are the only settings in the three observed shortcode containers.
      // Any animation, motion effect, video, slideshow, sticky or new setting keeps Elementor.
      foreach ($settings as $key => $value) {
        if ($key !== 'background_background' || $value !== 'classic') { return false; }
      }
    }
    $class = $tags->get_attribute('class');
    if (is_string($class) && preg_match('~(?:^|\s)(?:elementor-invisible|elementor-sticky|elementor-motion-effects-element)(?:\s|$)~', $class)) { return false; }
    foreach (['data-e-action-hash', 'data-elementor-lightbox-id', 'data-elementor-lightbox-type'] as $attribute) {
      if ($tags->get_attribute($attribute) !== null) { return false; }
    }
    $lightbox = $tags->get_attribute('data-elementor-open-lightbox');
    if ($lightbox !== null && $lightbox !== 'no') { return false; }
    $id = $tags->get_attribute('id');
    if (in_array($id, ['zpNewNav', 'zhHero', 'zpMegaFooter'], true)) { $markers[$id] = true; }
    if ($tags->get_tag() === 'SCRIPT') {
      if (!method_exists($tags, 'get_modifiable_text')) { return false; }
      // A later custom snippet can depend on Elementor even when every widget is a shortcode.
      if (preg_match('~\belementor(?:Pro)?Frontend\b~', $tags->get_modifiable_text())) { return false; }
    }
    if ($tags->get_tag() === 'SCRIPT' && isset($scripts[$id ?? ''])) {
      $src = $tags->get_attribute('src');
      if (!is_string($src)) { return false; }
      $path = parse_url($src, PHP_URL_PATH);
      $expected = $scripts[$id];
      if (!is_string($path) || substr($path, -strlen($expected)) !== $expected) { return false; }
      $seen[$id] = ($seen[$id] ?? 0) + 1;
    }
  }
  if ($widgets !== 3 || $containers !== 3 || count($markers) !== 3 || count($roots) !== 3 || count($seen) !== 6) { return false; }
  foreach (['header', 'wp-page', 'footer'] as $root) {
    if (($roots[$root] ?? 0) !== 1) { return false; }
  }
  foreach ($scripts as $id => $src) {
    if (($seen[$id] ?? 0) !== 1) { return false; }
  }
  return true;
}

function zp_suite_shortcode_home_runtime_html(string $html): string {
  if (!zp_suite_shortcode_home_runtime_safe($html)) { return $html; }
  $ids = zp_suite_shortcode_home_runtime_scripts();
  $removed = [];
  $out = preg_replace_callback('~<script\b[^>]*>.*?</script\s*>~is', function ($match) use ($ids, &$removed) {
    $open_end = strpos($match[0], '>');
    $open = substr($match[0], 0, $open_end + 1);
    if (!preg_match('~\s+id\s*=\s*(["\'])([^"\']+)\1~i', $open, $id) || !isset($ids[$id[2]])) { return $match[0]; }
    $removed[$id[2]] = ($removed[$id[2]] ?? 0) + 1;
    return '';
  }, $html);
  if (!is_string($out) || count($removed) !== 6 || array_sum($removed) !== 6) { return $html; }
  return $out;
}

add_action('template_redirect', function () {
  if (is_admin() || !is_front_page() || is_user_logged_in() || is_preview() || is_feed()
      || wp_doing_ajax() || wp_doing_cron() || (defined('REST_REQUEST') && REST_REQUEST)
      || (defined('WP_CLI') && WP_CLI)
      || !in_array(strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET')), ['GET', 'HEAD'], true)
      || isset($_GET['elementor-preview']) || isset($_GET['elementor_library']) || isset($_GET['customize_changeset_uuid'])
      || isset($_GET['zp_keep_elementor'])) { return; }
  ob_start(function ($chunk, $phase = 0) {
    static $buffer = '';
    if (!is_string($chunk)) { return $chunk; }
    if ($phase & PHP_OUTPUT_HANDLER_CLEAN) { $buffer = ''; return ''; }
    $buffer .= $chunk;
    if (!($phase & PHP_OUTPUT_HANDLER_FINAL)) { return ''; }
    $html = $buffer;
    $buffer = '';
    foreach (headers_list() as $header) {
      if (stripos($header, 'content-type:') === 0 && stripos($header, 'text/html') === false) { return $html; }
      if (stripos($header, 'content-encoding:') === 0) { return $html; }
    }
    try { return zp_suite_shortcode_home_runtime_html($html); } catch (\Throwable $e) { return $html; }
  });
}, PHP_INT_MAX);
