<?php
/**
 * 3.2.8: reviewed, content-addressed CSS snapshots. Critical/mixed styles stay inline.
 * Only exact source hashes from the release manifest are transformed. Unknown or edited
 * CSS, unsupported attributes, missing/corrupt assets and previews keep the original style.
 * External styles occupy their original position: no deferred loading or cascade reordering.
 */
if (!defined('ABSPATH')) { exit; }
require_once __DIR__ . '/css-payload-3273.php';

function zp_css_delivery_enabled(): bool {
    return get_option('zp_css_delivery_off') !== '1'
        && !(isset($_GET['zp_css']) && (string) $_GET['zp_css'] === '0')
        && !(function_exists('is_preview') && is_preview())
        && !(function_exists('is_customize_preview') && is_customize_preview());
}

function zp_css_delivery_snapshot_map(): array {
    static $map = null;
    if ($map === null) {
        $path = __DIR__ . '/css-snapshot-map.php';
        $data = is_file($path) ? require $path : [];
        $map = is_array($data) ? $data : [];
    }
    return $map;
}

/** Check the shipped bytes once per request; never link to a broken or substituted file. */
function zp_css_delivery_asset(array $entry): ?string {
    static $verified = [];
    $name = $entry['file'] ?? '';
    if (!is_string($name) || !preg_match('~^assets/css/snapshots-20261015/[A-Za-z0-9][A-Za-z0-9.-]+\.css$~D', $name)) { return null; }
    $key = $name . '|' . (string) ($entry['bytes'] ?? '') . '|' . (string) ($entry['sha256'] ?? '');
    if (!array_key_exists($key, $verified)) {
        $verified[$key] = false;
        $dir = ZP_SUITE_PATH . 'assets/css/snapshots-20261015';
        $file = ZP_SUITE_PATH . $name;
        if (!is_link($dir) && !is_link($file) && is_file($file)
            && @filesize($file) === ($entry['bytes'] ?? -1)
            && is_string($entry['sha256'] ?? null)
            && hash_equals($entry['sha256'], (string) @hash_file('sha256', $file))) {
            $verified[$key] = true;
        }
    }
    return $verified[$key] ? ZP_SUITE_URL . $name : null;
}

function zp_css_delivery_html(string $html): string {
    if (!zp_css_delivery_enabled()) { return $html; }
    try {
        $map = zp_css_delivery_snapshot_map();
        $scan = $map ? zp_speed_scan($html) : null;
        if (!$scan) { return $html; }
        $ops = []; $scripts = null;
        foreach ($scan['styles'] as $style) {
            $gt = zp_speed_tag_end($html, $style[0]);
            if ($gt < 0) { continue; }
            $open = substr($html, $style[0], $gt + 1 - $style[0]);
            $attrs = zp_css_delivery_attributes($open);
            if (isset($attrs['id']) && !preg_match('~^(?:zp-|zpbs-|zh-)~', $attrs['id'])) { continue; }
            if (array_diff(array_keys($attrs), ['id', 'type'])) { continue; }
            if (isset($attrs['type']) && strtolower(trim($attrs['type'])) !== 'text/css') { continue; }
            $close = strripos(substr($html, $gt + 1, $style[1] - $gt - 1), '</style');
            if ($close === false) { continue; }
            $end = $gt + 1 + $close;
            $css = substr($html, $gt + 1, $end - $gt - 1);
            $entry = $map[hash('sha256', $css)] ?? null;
            if (!is_array($entry)) { continue; }
            if (isset($entry['file'])) {
                // A later page/controller can start naming an unchanged style: retain STYLE then.
                if (isset($attrs['id'])) {
                    if ($scripts === null) {
                        $scripts = '';
                        foreach ($scan['skip'] as $range) {
                            if (strncasecmp(substr($html, $range[0], 7), '<script', 7) === 0) {
                                $scripts .= substr($html, $range[0], $range[1] - $range[0]);
                            }
                        }
                    }
                    if (strpos($scripts, $attrs['id']) !== false) { continue; }
                }
                $url = zp_css_delivery_asset($entry);
                if ($url === null) { continue; }
                $id = isset($attrs['id']) ? ' id="' . esc_attr($attrs['id']) . '"' : '';
                $critical = is_string($entry['critical'] ?? null) && $entry['critical'] !== '' ? '<style>' . $entry['critical'] . '</style>' : '';
                $ops[] = [$style[0], $style[1], $critical . '<link rel="stylesheet"' . $id . ' href="' . esc_url($url) . '">'];
            } elseif (is_string($entry['inline'] ?? null) && strlen($entry['inline']) < strlen($css)) {
                $ops[] = [$gt + 1, $end, $entry['inline']];
            }
        }
        if (!$ops) { return $html; }
        $out = ''; $last = 0;
        foreach ($ops as [$start, $end, $replacement]) {
            if ($start < $last) { return $html; }
            $out .= substr($html, $last, $start - $last) . $replacement;
            $last = $end;
        }
        return $out . substr($html, $last);
    } catch (\Throwable $e) {
        return $html;
    }
}
