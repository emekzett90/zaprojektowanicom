<?php
/** Frontend fixes confirmed by the October 2026 console audit. */
if (!defined('ABSPATH')) { exit; }

/**
 * Suite's early standalone routes bypass Elementor's template_redirect init.
 * A theme/add-on can still enqueue its frontend script. Let Elementor supply its
 * own real configuration before WordPress prints that script, exactly once.
 */
add_action('wp_footer', function () {
  if (is_admin() || !class_exists('Elementor\\Plugin')) { return; }
  if (!wp_script_is('elementor-frontend', 'enqueued') || wp_script_is('elementor-frontend', 'done')) { return; }
  $scripts = wp_scripts();
  $inline = implode("\n", (array) $scripts->get_data('elementor-frontend', 'before'))
    . "\n" . (string) $scripts->get_data('elementor-frontend', 'data');
  if (strpos($inline, 'elementorFrontendConfig') !== false) { return; }
  $frontend = \Elementor\Plugin::$instance->frontend ?? null;
  if (is_object($frontend) && is_callable([$frontend, 'enqueue_scripts'])) {
    $frontend->enqueue_scripts();
  }
}, 19);

/** Read only local media headers, never make a network request during rendering. */
function zp_suite_quality_image_dimensions(string $src): array {
  static $roots = null, $cache = [];
  if (isset($cache[$src])) { return $cache[$src]; }
  $cache[$src] = [];
  if ($roots === null) {
    $uploads = wp_upload_dir(null, false);
    $roots = [
      [(string) ($uploads['baseurl'] ?? ''), (string) ($uploads['basedir'] ?? '')],
      [ZP_SUITE_URL, ZP_SUITE_PATH],
    ];
  }
  $url = wp_parse_url(html_entity_decode($src, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
  if (!is_array($url) || empty($url['path']) || (isset($url['scheme']) && !in_array($url['scheme'], ['http', 'https'], true))) { return []; }
  foreach ($roots as [$base_url, $base_dir]) {
    if ($base_url === '' || $base_dir === '') { continue; }
    $base = wp_parse_url($base_url);
    if (!is_array($base) || (isset($url['host']) && strcasecmp($url['host'], (string) ($base['host'] ?? '')) !== 0)) { continue; }
    $prefix = rtrim((string) ($base['path'] ?? ''), '/') . '/';
    if (strpos($url['path'], $prefix) !== 0) { continue; }
    $relative = rawurldecode(substr($url['path'], strlen($prefix)));
    if ($relative === '' || strpos($relative, "\0") !== false || preg_match('~(?:^|[\\\\/])\.\.(?:[\\\\/]|$)~', $relative)) { continue; }
    if (!preg_match('/\.(?:avif|webp|png|jpe?g|gif)$/i', $relative)) { continue; }
    $root = realpath($base_dir);
    $path = realpath(rtrim($base_dir, '/\\') . '/' . $relative);
    if (!$root || !$path || strpos($path, rtrim($root, '/\\') . DIRECTORY_SEPARATOR) !== 0 || !is_file($path) || !is_readable($path)) { continue; }
    $size = @getimagesize($path);
    if (is_array($size) && !empty($size[0]) && !empty($size[1])) {
      return $cache[$src] = [(int) $size[0], (int) $size[1]];
    }
  }
  return [];
}

/**
 * 2.9.13: an article's image slot note ("<!-- ZP_IMAGE_SLOT_1 | file.webp | ALT: ... -->", where a draft said a picture
 * goes) never reaches the page. On /sklepy-internetowe/ile-trwa-stworzenie-strony-internetowej/ readers saw
 * "ZP_IMAGE_SLOT_1" in PL and EN; where the note is a plain comment it still carried a Polish ALT into the English
 * page. That post already shows the named picture as its cover, so the note is removed, not turned into an image.
 * Covers the note as a comment and as text escaped by an editor (with the dashes WordPress may have typeset).
 */
function zp_suite_strip_image_slots($html) {
  if (!is_string($html) || strpos($html, 'ZP_IMAGE_SLOT') === false) { return $html; }
  $dash = '(?:--|&#8211;|&#x2013;|&ndash;|\x{2013}|&#8212;|&mdash;|\x{2014})';
  $note = '(?:<!--|(?:&lt;|&#60;)!' . $dash . ')\s*ZP_IMAGE_SLOT_\d+\b.*?(?:-->|' . $dash . '\s*(?:&gt;|&#62;))';
  // A paragraph holding nothing but the note goes with it, so no empty line is left.
  $html = preg_replace('~<p\b[^>]*>\s*' . $note . '\s*</p>~isu', '', $html) ?? $html;
  return preg_replace('~' . $note . '~isu', '', $html) ?? $html;
}
add_filter('the_content', 'zp_suite_strip_image_slots', 999);

/** Run on the final HTML, including early routes and late footer images. */
function zp_suite_quality_html(string $html): string {
  if (is_admin() || !class_exists('WP_HTML_Tag_Processor') || stripos($html, '<html') === false || stripos($html, '<body') === false) { return $html; }
  if ((defined('REST_REQUEST') && REST_REQUEST) || is_feed() || isset($_GET['elementor-preview'])) { return $html; }
  foreach (headers_list() as $header) {
    if (stripos($header, 'content-type:') === 0 && stripos($header, 'text/html') === false) { return $html; }
    if (stripos($header, 'content-encoding:') === 0) { return $html; }
  }
  $html = zp_suite_strip_image_slots($html);
  $tags = new WP_HTML_Tag_Processor($html);
  $marked = 0;
  while ($tags->next_tag('IMG')) {
    $width = $tags->get_attribute('width');
    $height = $tags->get_attribute('height');
    // Preserve author sizing. Supplying the natural ratio prevents a layout jump.
    if ($width !== null && $height !== null) { continue; }
    $src = $tags->get_attribute('src');
    if (!is_string($src)) { continue; }
    $size = zp_suite_quality_image_dimensions($src);
    if (!$size) { continue; }
    // data-zp-wh names the attributes added here. The rule printed below sets those
    // dimensions back to auto, so the image keeps the size it had without them (a
    // stylesheet that sets only the width no longer gets the file's height in pixels)
    // and the attributes give only the ratio, which reserves the box before loading.
    if ($width === null && $height === null) {
      $tags->set_attribute('width', (string) $size[0]);
      $tags->set_attribute('height', (string) $size[1]);
      $tags->set_attribute('data-zp-wh', 'w h');
      $marked++;
    } elseif ($width !== null && ctype_digit((string) $width) && (int) $width > 0) {
      $tags->set_attribute('height', (string) max(1, (int) round((int) $width * $size[1] / $size[0])));
      $tags->set_attribute('data-zp-wh', 'h');
      $marked++;
    } elseif ($height !== null && ctype_digit((string) $height) && (int) $height > 0) {
      $tags->set_attribute('width', (string) max(1, (int) round((int) $height * $size[0] / $size[1])));
      $tags->set_attribute('data-zp-wh', 'w');
      $marked++;
    }
  }
  $html = $tags->get_updated_html();
  if ($marked > 0) {
    // First in <head> with zero specificity: it outranks only the attributes themselves.
    $html = preg_replace('#<head\b[^>]*>#i', '$0<style id="zp-quality-img-wh">:where(img[data-zp-wh~="w"]){width:auto}:where(img[data-zp-wh~="h"]){height:auto}</style>', $html, 1);
  }
  return $html;
}

// The speed buffer calls quality_html even with ?zp_speed=0. Keep the fixes when
// the administrator turns that separate optimization module off entirely.
if (!is_admin() && get_option('zp_speed_off') === '1' && !wp_doing_ajax() && !wp_doing_cron()
    && !(defined('WP_CLI') && WP_CLI) && in_array(strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET')), ['GET', 'HEAD'], true)) {
  ob_start(function ($chunk, $phase = 0) {
    static $buffer = '';
    if ($phase & PHP_OUTPUT_HANDLER_CLEAN) { $buffer = ''; return ''; }
    $buffer .= $chunk;
    if (!($phase & PHP_OUTPUT_HANDLER_FINAL)) { return ''; }
    $html = $buffer; $buffer = '';
    try { return zp_suite_quality_html($html); } catch (\Throwable $e) { return $html; }
  });
}

// Cookiebot's account-hosted custom banner points Polish visitors to /privacy-policy/
// (404). Its public dialog events also cover opening preferences again. Consent
// choices and vendor scripts are unchanged.
add_action('wp_head', function () {
  if (is_admin()) { return; }
  ?>
<script id="zp-suite-cookiebot-links">(function(w,d){
  function fix(){
    var link=d.getElementById('zp-privacy-link');
    if(!link)return;
    var path=link.getAttribute('href');
    if(path==='/privacy-policy/'||path==='/privacy-policy')link.setAttribute('href',<?php echo wp_json_encode(home_url('/polityka-prywatnosci/')); ?>);
  }
  function afterDialog(){fix();w.setTimeout(fix,0);}
  w.addEventListener('CookiebotOnDialogDisplay',afterDialog);
  w.addEventListener('CookiebotOnLoad',afterDialog);
  if(d.readyState==='loading')d.addEventListener('DOMContentLoaded',fix,{once:true});else fix();
})(window,document);</script>
  <?php
}, 1);
