<?php
/**
 * SEO audit 13.10.2026 — a lighter HTML document for the same page.
 *
 * Seobility measured 1.6 MB of HTML on the home page, and most service pages are over 1 MB.
 * About 60% of it is CSS that the plugin prints into the page (some 190 <style> blocks); much of
 * the rest is the home page's portfolio controller and the cards of the mobile portfolio, which
 * the page holds a second time for phones. Every page view downloads all of it again, and
 * Seobility counts the hidden phone cards as 39 more headings, 37 images and 51 links.
 *
 * At the very end (zp_speed_buffer in speed-293.php, after every other change) this file moves
 * them into static files in uploads/zp-suite-cache/v1/. A file is named by a hash of its content,
 * so pages printing the same content share one file, browsers keep it, and changed content gets a
 * new name:
 * - inline styles: each run of styles printed after the page's first stylesheet link, with no
 *   stylesheet link, outside script or style-adding script between them, becomes one CSS file
 *   loaded by a <link> at the place of the run's first style. The order of all CSS stays the same,
 *   so the same rules win and the page looks the same. Styles before the first stylesheet link
 *   (the header's critical CSS), styles in <body> above the page's first heading (the hero) and
 *   the home hero's own styles in <body> (#zhHero, .zh__, printed after its H1) stay in the page,
 *   so the top of the page is drawn as before. Also kept in the page: runs under
 *   8 kB, styles with other attributes than id and type, with @import, @charset, relative or #
 *   addresses, unbalanced braces or an open comment, and styles whose id a script of the page names.
 * - the home page's portfolio controller, which the page keeps as text until it is needed
 *   (optimizer.php), becomes a JS file; the idle loader there already loads data-zp-src.
 * - the two <template>s with the cards of the mobile portfolio become JSON files; the mobile rail
 *   (templates/legacy/legacy-portfolio.php) fetches them after the page has loaded, or when the
 *   portfolio nears the screen.
 * Before the first use, and again every week, a test file is fetched from the site's address
 * (zp_speed_files_check): if the server does not hand it back unchanged with the right type,
 * nothing moves. When a file cannot be written (permissions, full disk, more than 3000 files or
 * 150 MB) that part stays in the page. Pages look the same either way. Files no page has used for
 * 60 days are removed once a day (a used file's date is refreshed at most once a day; page caches
 * here keep pages for 30 minutes to a week).
 *
 * Off switch: ZP Suite → Przyspieszenie. One page view without it: ?zp_files=0.
 */
if (!defined('ABSPATH')) { exit; }

define('ZP_SPEED_FILES_DIR', 'zp-suite-cache/v1');
define('ZP_SPEED_FILES_MAX', 3000);
define('ZP_SPEED_FILES_MAX_BYTES', 150 * 1024 * 1024);
define('ZP_SPEED_FILES_KEEP_DAYS', 60);
define('ZP_SPEED_FILES_MIN_RUN', 8000);
/* A script between two styles that may add CSS to the page while it is read, or that reads styles. */
define('ZP_SPEED_FILES_BREAK_RE', '~<style|<link|createElement\(\s*["\'](?:style|link)["\']|document\.write|insertAdjacentHTML|styleSheets|\.sheet\b|insertRule|getComputedStyle~i');

function zp_speed_files_enabled(): bool {
  // 3.1.0: off unless switched on with the option zp_speed_files_on = '1'. On a cold slow-4G load the style files made
  // the first paint of /sklepy-internetowe/ 1.4 s later than styles in the page (measured 14.10); 3.1.1 tunes the order.
  if (get_option('zp_speed_files_on') !== '1') { return false; }
  if (get_option('zp_speed_files_off') === '1') { return false; }
  return !(isset($_GET['zp_files']) && (string) $_GET['zp_files'] === '0');
}

/** [directory, URL path] of the store, or null without uploads. A path, not a full URL: same host as the page. */
function zp_speed_files_store(): ?array {
  static $store = false;
  if ($store !== false) { return $store; }
  $store = null;
  $up = wp_upload_dir(null, false);
  if (!empty($up['error']) || empty($up['basedir']) || empty($up['baseurl'])) { return null; }
  $path = (string) wp_parse_url((string) $up['baseurl'], PHP_URL_PATH);
  $store = [rtrim((string) $up['basedir'], '/\\') . '/' . ZP_SPEED_FILES_DIR, rtrim($path, '/') . '/' . ZP_SPEED_FILES_DIR];
  return $store;
}

/** Whether the weekly test found the store served correctly; schedules the test when it is due. */
function zp_speed_files_verified(): bool {
  $state = get_option('zp_speed_files_ok');
  $valid = is_array($state) && ($state['dir'] ?? '') === ZP_SPEED_FILES_DIR;
  if ((!$valid || (int) ($state['t'] ?? 0) < time() - WEEK_IN_SECONDS) && function_exists('wp_next_scheduled') && !wp_next_scheduled('zp_speed_files_check')) {
    wp_schedule_single_event(time(), 'zp_speed_files_check');
  }
  return $valid && !empty($state['ok']); // the last result counts until the next test is done
}

/** Creates the store on first use; false when it is full or cannot be written. */
function zp_speed_files_writable(string $dir): bool {
  static $ok = null;
  if ($ok !== null) { return $ok; }
  $ok = false;
  if (!is_dir($dir) && !wp_mkdir_p($dir)) { return false; }
  if (!is_writable($dir)) { return false; }
  if (!file_exists($dir . '/index.html')) { @file_put_contents($dir . '/index.html', ''); }
  $count = 0;
  $bytes = 0;
  foreach ((array) glob($dir . '/*') as $file) {
    $count++;
    $bytes += (int) @filesize($file);
  }
  if ($count >= ZP_SPEED_FILES_MAX || $bytes >= ZP_SPEED_FILES_MAX_BYTES) {
    if (!wp_next_scheduled('zp_speed_files_cleanup')) { wp_schedule_single_event(time(), 'zp_speed_files_cleanup'); }
    return false;
  }
  $ok = true;
  return true;
}

/** The URL path of a file with this content (written when missing), or null. */
function zp_speed_files_put(string $ext, string $content): ?string {
  static $algo = null;
  $store = zp_speed_files_store();
  if ($store === null) { return null; }
  if ($algo === null) { $algo = in_array('xxh128', hash_algos(), true) ? 'xxh128' : 'md5'; } // xxh128: PHP 8.1+, ten times faster
  $name = substr(hash($algo, $content), 0, 20) . '.' . $ext;
  $file = $store[0] . '/' . $name;
  clearstatcache(true, $file);
  if (@filesize($file) === strlen($content)) {
    if ((int) @filemtime($file) < time() - DAY_IN_SECONDS) { @touch($file); }
    return $store[1] . '/' . $name;
  }
  if (!zp_speed_files_writable($store[0])) { return null; }
  $tmp = $store[0] . '/.' . $name . '.' . getmypid() . '-' . mt_rand() . '.tmp';
  if (@file_put_contents($tmp, $content) !== strlen($content) || !@rename($tmp, $file)) {
    @unlink($tmp);
    return null;
  }
  $stat = @stat($store[0]);
  if (is_array($stat)) { @chmod($file, $stat['mode'] & 0000666); }
  return $store[1] . '/' . $name;
}

/** The weekly test: each kind of file is fetched from the site's own address and must come back unchanged, with its type. */
function zp_speed_files_check(): array {
  $state = ['dir' => ZP_SPEED_FILES_DIR, 't' => time(), 'ok' => false, 'why' => ''];
  $store = zp_speed_files_store();
  $probes = [
    'css' => ['/* zp-suite probe */.zp-probe-css{color:#010203}', 'text/css'],
    'js' => ['/* zp-suite probe */window.zpProbeJs=1;', 'javascript'],
    'json' => ['{"zp":"probe"}', ''],
  ];
  if ($store === null) {
    $state['why'] = 'brak katalogu uploads';
  } else {
    $origin = (string) preg_replace('~^(https?://[^/]+).*$~i', '$1', home_url('/'));
    foreach ($probes as $ext => $probe) {
      $path = zp_speed_files_put($ext, $probe[0]);
      if ($path === null) { $state['why'] = 'nie można zapisać pliku w ' . $store[0]; break; }
      $res = wp_remote_get($origin . $path . '?zp_probe=' . time(), ['timeout' => 15, 'redirection' => 2, 'sslverify' => apply_filters('https_local_ssl_verify', false), 'headers' => ['Cache-Control' => 'no-cache']]);
      $type = is_wp_error($res) ? '' : strtolower((string) wp_remote_retrieve_header($res, 'content-type'));
      if (is_wp_error($res)) {
        $state['why'] = 'test .' . $ext . ': ' . $res->get_error_message();
        break;
      }
      if ((int) wp_remote_retrieve_response_code($res) !== 200 || wp_remote_retrieve_body($res) !== $probe[0] || ($probe[1] !== '' && strpos($type, $probe[1]) === false)) {
        $state['why'] = 'test .' . $ext . ': odpowiedź ' . (int) wp_remote_retrieve_response_code($res) . ', typ ' . ($type !== '' ? $type : 'brak');
        break;
      }
    }
    if ($state['why'] === '') { $state['ok'] = true; }
  }
  update_option('zp_speed_files_ok', $state, true);
  if (!wp_next_scheduled('zp_speed_files_cleanup')) { wp_schedule_event(time() + HOUR_IN_SECONDS, 'daily', 'zp_speed_files_cleanup'); }
  return $state;
}
add_action('zp_speed_files_check', 'zp_speed_files_check');

/** Removes files no page has used for ZP_SPEED_FILES_KEEP_DAYS days, and leftovers of interrupted writes. */
function zp_speed_files_cleanup(): void {
  $store = zp_speed_files_store();
  if ($store === null || !is_dir($store[0])) { return; }
  $old = time() - ZP_SPEED_FILES_KEEP_DAYS * DAY_IN_SECONDS;
  foreach ((array) glob($store[0] . '/*.{css,js,json}', defined('GLOB_BRACE') ? GLOB_BRACE : 0) as $file) {
    if ((int) @filemtime($file) < $old) { @unlink($file); }
  }
  foreach ((array) glob($store[0] . '/.*.tmp') as $file) {
    if ((int) @filemtime($file) < time() - HOUR_IN_SECONDS) { @unlink($file); }
  }
}
add_action('zp_speed_files_cleanup', 'zp_speed_files_cleanup');

/** Called by zp_speed_buffer with the finished page; returns it unchanged when anything is off. */
function zp_speed_weight_html(string $html): string {
  if (!zp_speed_files_enabled() || !zp_speed_files_verified()) { return $html; }
  try {
    $out = zp_speed_weight_process($html);
  } catch (\Throwable $e) {
    $out = null;
  }
  return is_string($out) ? $out : $html;
}

function zp_speed_weight_process(string $html): ?string {
  $scan = zp_speed_scan($html);
  if ($scan === null) { return null; }
  $ops = array_merge(zp_speed_weight_style_ops($html, $scan), zp_speed_weight_block_ops($html, $scan));
  if (!$ops) { return null; }
  usort($ops, function ($a, $b) { return $a[0] <=> $b[0] ?: $a[1] <=> $b[1]; });
  $out = '';
  $last = 0;
  foreach ($ops as $op) {
    if ($op[0] < $last) { return null; } // overlapping changes: never expected, keep the page as it is
    $out .= substr($html, $last, $op[0] - $last) . $op[2];
    $last = $op[1];
  }
  return $out . substr($html, $last);
}

/** Inline styles → CSS files (see the top of this file): [start, end, replacement] operations. */
function zp_speed_weight_style_ops(string $html, array $scan): array {
  if (!$scan['styles'] || !$scan['sheet_links']) { return []; }
  $first_link = $scan['sheet_links'][0][0];
  // The page's first heading in <body>: styles between <body> and it stay in the page.
  $h1 = -1;
  $pos = $scan['body_open'];
  while (preg_match('~<h1(?=[\s>])~i', $html, $hm, PREG_OFFSET_CAPTURE, $pos)) {
    if (!zp_speed_in_skip($scan['skip'], $hm[0][1])) { $h1 = $hm[0][1]; break; }
    $pos = $hm[0][1] + 3;
  }
  // What ends a run: stylesheet links, outside scripts, scripts that may add or read CSS, the end of <head>, <body>.
  $breaks = array_column($scan['sheet_links'], 0);
  $breaks[] = $scan['head_end'];
  $breaks[] = $scan['body_tag'];
  $codes = [];
  foreach ($scan['skip'] as $range) {
    if ($html[$range[0] + 1] !== 's' && $html[$range[0] + 1] !== 'S') { continue; }
    if (strncasecmp(substr($html, $range[0], 7), '<script', 7) !== 0) { continue; }
    $gt = zp_speed_tag_end($html, $range[0]);
    if ($gt < 0) { return []; }
    $open = substr($html, $range[0], $gt + 1 - $range[0]);
    $code = substr($html, $gt + 1, max(0, $range[1] - $gt - 1));
    $codes[] = $code;
    if (preg_match('~\ssrc\s*=~i', $open)) { $breaks[] = $range[0]; continue; }
    if (preg_match('~\stype\s*=\s*["\']?(?:application/(?:ld\+)?json|text/plain)\b~i', $open)) { continue; } // data, or code run after the page has loaded
    if (preg_match(ZP_SPEED_FILES_BREAK_RE, $code)) { $breaks[] = $range[0]; }
  }
  // Every word of the page's scripts, to keep styles whose id a script names (built when the first style with an id is met).
  $words = null;
  $text = null;
  $named = function (string $id) use (&$words, &$text, &$codes): bool {
    if ($words === null) {
      $text = implode(' ', $codes);
      $codes = [];
      $words = preg_match_all('~\w+(?:-\w+)+~', $text, $wm) ? array_flip($wm[0]) : [];
    }
    return strpos($id, '-') !== false ? isset($words[$id]) : strpos($text, $id) !== false;
  };
  sort($breaks);
  $runs = [];
  $run = -1;
  $b = 0;
  $nb = count($breaks);
  foreach ($scan['styles'] as $style) {
    $broken = false;
    while ($b < $nb && $breaks[$b] < $style[0]) { $b++; $broken = true; }
    $hero = $style[0] >= $scan['body_open'] && ($h1 < 0 || $style[0] < $h1);
    if ($style[0] < $first_link || $hero) { $run = -1; continue; }
    $css = zp_speed_weight_style_css($html, $style, $named);
    // The home hero's own styles come after its H1 (templates/home-hero.php). From a file in <body> they can arrive
    // after the first paint and move the heading on phones (3.1.0 review), so there they stay in the page. In <head>
    // a stylesheet holds back the first paint, so nothing moves.
    if ($css !== null && $style[0] >= $scan['body_open'] && preg_match('~#zhHero\b|\.zh__~', $css)) { $css = null; }
    if ($css === null) { $run = -1; continue; } // a style that stays in the page also ends the run
    if ($broken || $run < 0) { $runs[] = []; $run = count($runs) - 1; }
    $runs[$run][] = [$style[0], $style[1], $css];
  }
  $ops = [];
  foreach ($runs as $list) {
    $css = '';
    foreach ($list as $item) { $css .= $item[2] . "\n"; }
    if (strlen($css) < ZP_SPEED_FILES_MIN_RUN) { continue; }
    // A UTF-8 mark first: the file is read as UTF-8 whatever character set the server names.
    $url = zp_speed_files_put('css', (preg_match('~[\x80-\xff]~', $css) ? "\xEF\xBB\xBF" : '') . $css);
    if ($url === null) { continue; }
    foreach ($list as $i => $item) {
      $ops[] = [$item[0], $item[1], $i === 0 ? '<link rel="stylesheet" href="' . esc_attr($url) . '" data-zp-inline="' . count($list) . '">' : ''];
    }
  }
  return $ops;
}

/** The CSS of a <style> element that can move to a file, or null when it stays in the page. */
function zp_speed_weight_style_css(string $html, array $style, callable $named): ?string {
  $gt = zp_speed_tag_end($html, $style[0]);
  $close = strrpos($html, '<', $style[1] - strlen($html) - 1); // the "<" of </style>
  if ($gt < 0 || $close === false || $close <= $gt) { return null; }
  $close -= $style[0];
  $open = substr($html, $style[0] + 6, $gt - $style[0] - 6);
  if (substr($open, -1) === '/') { return null; }
  $id = '';
  if (trim($open) !== '') {
    if (!preg_match_all('~([^\s=/>"\']+)\s*(?:=\s*(?:"([^"]*)"|\'([^\']*)\'|([^\s>"\']+)))?~', $open, $am, PREG_SET_ORDER)) { return null; }
    foreach ($am as $a) {
      $name = strtolower($a[1]);
      $value = ($a[2] ?? '') . ($a[3] ?? '') . ($a[4] ?? '');
      if ($name === 'id') { $id = $value; continue; }
      if ($name === 'type' && in_array(strtolower(trim($value)), ['', 'text/css'], true)) { continue; }
      return null; // media, nonce, data-…: stays as it is
    }
  }
  if ($id !== '' && $named($id)) { return null; }
  $css = substr($html, $gt + 1, $style[0] + $close - $gt - 1);
  if (preg_match('~@import|@charset|@namespace|image-set\(\s*["\']~i', $css)) { return null; }
  // In a file, an address without a scheme or a leading slash (and #id) would point next to the file, not the page.
  $plain = (string) preg_replace('~url\(\s*(["\']?)\s*data:.*?\1\s*\)~is', '', $css);
  if (preg_match_all('~url\(\s*["\']?\s*([^"\')\s]*)~i', $plain, $um)) {
    foreach ($um[1] as $target) {
      if (!preg_match('~^(?:[a-z][a-z0-9+.-]*:|/)~i', $target)) { return null; }
    }
  }
  if (substr_count($css, '{') !== substr_count($css, '}')) { return null; }
  $comment = strrpos($css, '/*');
  if ($comment !== false && strpos($css, '*/', $comment + 2) === false) { return null; }
  return $css;
}

/** The delayed portfolio controller → JS file, the mobile portfolio templates → JSON files. */
function zp_speed_weight_block_ops(string $html, array $scan): array {
  $ops = [];
  foreach ($scan['skip'] as $range) {
    $script = strncasecmp(substr($html, $range[0], 7), '<script', 7) === 0;
    if (!$script && strncasecmp(substr($html, $range[0], 9), '<template', 9) !== 0) { continue; }
    $gt = zp_speed_tag_end($html, $range[0]);
    if ($gt < 0) { continue; }
    $open = substr($html, $range[0], $gt + 1 - $range[0]);
    if (stripos($open, 'data-zp-src') !== false) { continue; }
    if ($script) {
      if (!preg_match('~\sdata-zp-delay-home-(?:portfolio|js)\b~i', $open) || !preg_match('~\stype\s*=\s*["\']?text/plain\b~i', $open)) { continue; }
      $tag_close = '</script>';
    } else {
      if (!preg_match('~\sdata-zp-mobile-template\s*=~i', $open)) { continue; }
      $tag_close = '</template>';
    }
    $close = strripos(substr($html, $range[0], $range[1] - $range[0]), substr($tag_close, 0, -1));
    if ($close === false) { continue; }
    $inner = substr($html, $gt + 1, $range[0] + $close - $gt - 1);
    if (strlen($inner) < 4096) { continue; }
    if ($script) {
      $url = zp_speed_files_put('js', (preg_match('~[\x80-\xff]~', $inner) ? "\xEF\xBB\xBF" : '') . $inner);
    } else {
      $json = wp_json_encode(['html' => $inner], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
      $url = is_string($json) && $json !== '' ? zp_speed_files_put('json', $json) : null;
    }
    if ($url === null) { continue; }
    $ops[] = [$range[0], $range[1], substr($open, 0, -1) . ' data-zp-src="' . esc_attr($url) . '">' . $tag_close];
  }
  return $ops;
}
