<?php
/** Native ordered defer for audited standalone homepage widgets. */
if (!defined('ABSPATH')) { exit; }

function zp_home_owned_defer_3290_html(string $html): string {
    $path = wp_parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH);
    if (!in_array($path, ['/', '/en/'], true) || !function_exists('zp_home_first_paint_enabled')
        || !zp_home_first_paint_enabled() || strpos($html, 'id="zp-home-first-paint-3233"') === false
        || strpos($html, 'id="zhHero"') === false) { return $html; }
    try {
        foreach (headers_list() as $header) {
            if (stripos($header, 'Content-Security-Policy:') === 0) { return $html; }
        }
        if (stripos($html, 'content-security-policy') !== false) { return $html; }
        $scan = zp_speed_scan($html);
        if (!$scan) { return $html; }
        // These IIFEs have no synchronous consumers. Three initialize at DCL;
        // case links read their earlier inline configuration and existing DOM.
        $assets = [
            'zp-suite-home-featured-packages-js' => ['assets/js/blocks/home-featured-packages.js', '493ada51300516dbe6b1926673ddbfd03f4187aa4251ef0990e8edb317c93ef4'],
            'zp-case-studies-20261015-js' => ['assets/js/case-studies-20261015.js', 'dd8b47b652b30c43eb967f863a8c829fb55dcbd4ef176411a2bc872880acbe0e'],
            'zp-suite-meta-verified-js' => ['assets/js/blocks/meta-verified.js', 'f655e414042a5d8c60c5987c3a912186b36fbb294d8c568c207e120b61f674c1'],
            'zp-home-service-mobile-3236-js' => ['assets/js/home-service-mobile-3236.js', 'bc94e0624101707af5d7d605dd89ff87522780de84ad33ac3af2c11ab65fd8cc'],
        ];
        $seen = []; $edits = []; $root = realpath(ZP_SUITE_PATH);
        foreach ($scan['skip'] as [$start, $end]) {
            if (strncasecmp(substr($html, $start, 7), '<script', 7) !== 0) { continue; }
            $gt = zp_speed_tag_end($html, $start);
            if ($gt < 0) { return $html; }
            $open = substr($html, $start, $gt + 1 - $start);
            $attrs = [];
            if (preg_match_all('~\s+([^\s=/>"\']+)(?:\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|([^\s>"\']+)))?~', $open, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $m) {
                    $key = strtolower($m[1]);
                    if (array_key_exists($key, $attrs)) { return $html; }
                    $attrs[$key] = html_entity_decode(($m[2] ?? '') . ($m[3] ?? '') . ($m[4] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                }
            }
            $id = $attrs['id'] ?? '';
            if (!isset($assets[$id])) { continue; }
            if (isset($seen[$id])) { return $html; }
            $seen[$id] = true;
            [$asset, $hash] = $assets[$id];
            $file = ZP_SUITE_PATH . $asset; $real = realpath($file);
            if (!$root || !$real || is_link($file) || strpos($real, $root . DIRECTORY_SEPARATOR) !== 0
                || !is_file($real) || !hash_equals($hash, (string) hash_file('sha256', $real))) { return $html; }
            if (array_diff(array_keys($attrs), ['id', 'src', 'type', 'defer'])
                || !in_array(strtolower(trim($attrs['type'] ?? '')), ['', 'text/javascript', 'application/javascript'], true)
                || !preg_match('~^' . preg_quote(ZP_SUITE_URL . $asset, '~') . '\?ver=[0-9.]+$~', $attrs['src'] ?? '')
                || substr(rtrim($open), -2) === '/>') { return $html; }
            $tail = substr($html, $gt + 1, $end - $gt - 1); $close = strripos($tail, '</script');
            if ($close === false || trim(substr($tail, 0, $close)) !== '') { return $html; }
            if (!array_key_exists('defer', $attrs)) { $edits[] = [$gt, ' defer']; }
        }
        foreach (array_reverse($edits) as [$at, $value]) { $html = substr($html, 0, $at) . $value . substr($html, $at); }
        return $html;
    } catch (\Throwable $error) { return $html; }
}
