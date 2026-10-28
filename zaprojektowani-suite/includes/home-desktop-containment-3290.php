<?php
/**
 * Restore the eight previously selected desktop sections' containment after
 * the legacy minifier removes a meaningful descendant space before :is().
 * Plain, scoped selectors survive that minifier. All declarations, dimensions,
 * markup and assets remain unchanged; mobile never matches this existing rule.
 */
if (!defined('ABSPATH')) { exit; }

function zp_home_desktop_containment_3290_html(string $html): string {
    $path = wp_parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH);
    if (!in_array($path, ['/', '/en/'], true) || !function_exists('zp_home_first_paint_enabled')
        || !zp_home_first_paint_enabled() || strpos($html, 'id="zp-home-first-paint-3233"') === false
        || strpos($html, 'data-zp-desktop-containment="3290"') !== false) { return $html; }
    try {
        foreach (headers_list() as $header) {
            if (stripos($header, 'Content-Security-Policy:') === 0) { return $html; }
        }
        if (stripos($html, 'content-security-policy') !== false) { return $html; }
        $root = realpath(ZP_SUITE_PATH);
        $file = ZP_SUITE_PATH . 'includes/front-fixes-323.php';
        $real = realpath($file);
        if (!$root || !$real || is_link($file) || !is_file($real) || !is_readable($real)
            || strpos($real, $root . DIRECTORY_SEPARATOR) !== 0
            || !hash_equals('da12f99e0fedee99ee93a9e1a6191dab99ee77d48ce9f00d5c508f3ab5a4653f',
                (string) hash_file('sha256', $real))) { return $html; }
        $scan = zp_speed_scan($html);
        if (!$scan || substr_count($html, 'id="zp-suite-323-desktop-perf"') !== 1) { return $html; }
        foreach ($scan['styles'] as [$start, $end]) {
            $node = substr($html, $start, $end - $start);
            $open = '<style id="zp-suite-323-desktop-perf">';
            if (strpos($node, $open) !== 0 || substr($node, -8) !== '</style>') { continue; }
            $css = substr($node, strlen($open), -8);
            // Guard the exact deployed stylesheet, including its desktop media.
            if (!hash_equals('73752caf5ad505add5424c89ae1503219a870fae2dc7216566053d3e285afa6c', hash('sha256', $css))) { return $html; }
            $targets = ['#zpHomeFeaturedPackages', '#zpLaptopShowcase', '#zpShowcaseServices', '.zpRevEd',
                '#zpHomeFaqKatNavy', '#zpHomeSeo', '#zpHomeAuditCta', '#zpContactFormLight'];
            foreach (['home', 'front-page'] as $body) {
                // Keep the three later height overrides at the same specificity
                // as their expanded generic selectors, so original sizes win.
                foreach (['#zpLaptopShowcase', '#zpShowcaseServices', '.zpRevEd'] as $target) {
                    $before = 'html body.' . $body . ' ' . $target;
                    if (substr_count($css, $before) !== 1) { return $html; }
                    $css = str_replace($before, 'html body.' . $body . ' #content ' . $target, $css);
                }
                $before = 'html body.' . $body . ':is(' . implode(',', $targets) . ')';
                $after = implode(',', array_map(static function ($target) use ($body) {
                    // #content also beats the old generic "main#content>section"
                    // visible!important reset; only these reviewed roots match.
                    return 'html body.' . $body . ' #content ' . $target;
                }, $targets));
                if (substr_count($css, $before) !== 1) { return $html; }
                $css = str_replace($before, $after, $css);
            }
            $replacement = '<style id="zp-suite-323-desktop-perf" data-zp-desktop-containment="3290">' . $css . '</style>';
            return substr($html, 0, $start) . $replacement . substr($html, $end);
        }
    } catch (\Throwable $error) { return $html; }
    return $html;
}
