<?php
/**
 * 3.2.95 — szybkość strony głównej (Mat 29.10.2026 20:19 UTC: „postaraj się osiągnąć 80+ stabilnie
 * w Lighthouse na mobile i 90+ stabilnie na desktop strony głównej”), bez żadnej widocznej zmiany.
 *
 * Wspólny mechanizm: jeden bufor wyjścia, który startuje PRZED buforami 3235 (mobile-header-ssr)
 * i speed-293, więc jest od nich zewnętrzny i dostaje gotowy HTML po wszystkich wcześniejszych
 * przebiegach (3233, 3236, 3280…). Moduły z tego katalogu dopinają się filtrem `zp_s95_html`
 * (priorytety: js 10, img 20, desk 30, font 40, css 50). Kopia strony w cache HTML (drop-in
 * advanced-cache.php) startuje jeszcze wcześniej, więc zapisuje już zmieniony HTML.
 *
 * Wyłączniki: opcja zp_s95_off = '1' (cała 3.2.95), ?zp_s95=0 (jedna odsłona, omija też cache,
 * bo każdy parametr w URL go omija).
 */
if (!defined('ABSPATH')) { exit; }

if (!function_exists('zp_s95_enabled')) {
  function zp_s95_enabled(): bool {
    if (isset($_GET['zp_s95']) && (string) $_GET['zp_s95'] === '0') { return false; }
    return (string) get_option('zp_s95_off', '0') !== '1';
  }
}

if (!function_exists('zp_s95_buffer')) {
  function zp_s95_buffer($html, $phase = 0) {
    if (!is_string($html) || $html === '' || stripos($html, '<html') === false || stripos($html, '</html>') === false) { return $html; }
    if (!function_exists('apply_filters')) { return $html; }
    // Tylko strona główna PL/EN (te same ścieżki co 3235/3236); is_front_page po zapytaniu WP.
    $is_home = function_exists('zp_suite_308_is_home_like') ? (bool) zp_suite_308_is_home_like() : (function_exists('is_front_page') && is_front_page());
    if (!$is_home) { return $html; }
    try {
      $out = apply_filters('zp_s95_html', $html);
      return (is_string($out) && $out !== '' && stripos($out, '</html>') !== false) ? $out : $html;
    } catch (\Throwable $e) {
      return $html;
    }
  }
}

$zp_s95_path = parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH);
if (zp_s95_enabled() && !is_admin() && !wp_doing_ajax() && !wp_doing_cron()
    && !(defined('WP_CLI') && WP_CLI) && !(defined('XMLRPC_REQUEST') && XMLRPC_REQUEST)
    && in_array(strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET')), ['GET', 'HEAD'], true)
    && in_array($zp_s95_path, ['/', '/en', '/en/', '/de', '/de/', '/es', '/es/'], true)) {
  ob_start('zp_s95_buffer');
}
unset($zp_s95_path);

foreach (glob(__DIR__ . '/*.php') ?: [] as $zp_s95_file) {
  if (basename($zp_s95_file) !== 'loader.php') { require_once $zp_s95_file; }
}
unset($zp_s95_file);
