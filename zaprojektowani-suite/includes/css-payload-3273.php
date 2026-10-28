<?php
/**
 * 3.2.73: final-response, content-addressed CSS for reviewed below-fold components.
 *
 * The release manifest recognizes exact final CSS bytes only. It names a complete
 * style or one contiguous sequence of complete top-level CSS rules. Font faces,
 * global rules, first-screen geometry, navigation and consent remain inline.
 * Only the two reviewed Polish website-service routes are eligible. Three
 * verified files are preloaded in head and linked at their original positions.
 * Adjacent complete styles may share a file only across whitespace/comments.
 * Nothing is delayed with JavaScript; edited CSS and missing assets stay inline.
 */
if (!defined('ABSPATH')) { exit; }

function zp_css_payload_manifest_3273(): array {
    static $manifest = null;
    if ($manifest === null) {
        $file = __DIR__ . '/css-payload-manifest-3273.php';
        $data = is_file($file) ? require $file : [];
        $manifest = is_array($data) ? $data : [];
    }
    return $manifest;
}

/** Fail open on incomplete packages, changed bytes, symlinks or invalid paths. */
function zp_css_payload_asset_3273(array $entry): ?string {
    static $verified = [];
    $name = $entry['file'] ?? '';
    if (!is_string($name) || !preg_match('~^assets/css/payload-3273/[a-f0-9]{24}\.css$~D', $name)) { return null; }
    $key = $name . '|' . ($entry['sha256'] ?? '') . '|' . ($entry['bytes'] ?? '');
    if (!array_key_exists($key, $verified)) {
        $file = ZP_SUITE_PATH . $name;
        $verified[$key] = !is_link(dirname($file)) && !is_link($file) && is_file($file)
            && is_int($entry['bytes'] ?? null) && @filesize($file) === $entry['bytes']
            && is_string($entry['sha256'] ?? null)
            && hash_equals($entry['sha256'], (string) @hash_file('sha256', $file));
    }
    return $verified[$key] ? ZP_SUITE_URL . $name : null;
}

function zp_css_payload_html_3273(string $html): string {
    $path = parse_url((string) ($_SERVER['REQUEST_URI'] ?? ''), PHP_URL_PATH);
    if (!in_array($path, ['/tworzenie-stron-internetowych/', '/strony-internetowe-katowice/'], true)
        || is_admin() || (defined('REST_REQUEST') && REST_REQUEST)
        || (function_exists('wp_is_json_request') && wp_is_json_request())
        || isset($_GET['elementor-preview']) || isset($_GET['fl_builder']) || isset($_GET['et_fb'])
        || !function_exists('zp_css_delivery_enabled') || !zp_css_delivery_enabled()
        || (isset($_GET['zp_payload']) && (string) $_GET['zp_payload'] === '0')
        || strpos($html, 'data-zp-payload="3273"') !== false) { return $html; }
    try {
        $manifest = zp_css_payload_manifest_3273();
        $scan = !empty($manifest['styles']) ? zp_speed_scan($html) : null;
        if (!$scan) { return $html; }
        $head_open = null; $offset = 0;
        while (preg_match('~<head(?=[\s>])~i', $html, $match, PREG_OFFSET_CAPTURE, $offset)) {
            $at = $match[0][1]; $offset = $at + strlen($match[0][0]);
            if ($at >= $scan['head_end']) { break; }
            if (zp_speed_in_skip($scan['skip'], $at)) { continue; }
            $gt = zp_speed_tag_end($html, $at);
            if ($gt >= 0 && $gt < $scan['head_end']) { $head_open = $gt + 1; }
            break;
        }
        if ($head_open === null) { return $html; }
        $preloaded = [];
        if (preg_match_all('~<link\b[^>]*>~i', $html, $links, PREG_OFFSET_CAPTURE)) {
            foreach ($links[0] as [$tag, $at]) {
                if (zp_speed_in_skip($scan['skip'], $at)) { continue; }
                $attrs = zp_css_delivery_attributes($tag);
                if (strtolower($attrs['rel'] ?? '') === 'preload' && strtolower($attrs['as'] ?? '') === 'style') {
                    $preloaded[$attrs['href'] ?? ''] = true;
                }
            }
        }
        $scripts = '';
        foreach ($scan['skip'] as $range) {
            if (strncasecmp(substr($html, $range[0], 7), '<script', 7) !== 0) { continue; }
            $gt = zp_speed_tag_end($html, $range[0]);
            if ($gt < 0) { return $html; }
            // A script's own id="component-css-js" is not a reference to a CSS node.
            $end = strripos(substr($html, $gt + 1, $range[1] - $gt - 1), '</script');
            $scripts .= substr($html, $gt + 1, $end === false ? $range[1] - $gt - 1 : $end) . "\n";
        }
        $runs = [];
        foreach ($scan['styles'] as [$start, $end]) {
            $gt = zp_speed_tag_end($html, $start);
            if ($gt < 0) { continue; }
            $open = substr($html, $start, $gt + 1 - $start);
            $attrs = zp_css_delivery_attributes($open);
            if (array_diff(array_keys($attrs), ['id', 'type'])
                || (isset($attrs['type']) && strtolower(trim($attrs['type'])) !== 'text/css')) { continue; }
            $id = $attrs['id'] ?? '';
            if ($id !== '' && (!preg_match('~^(?:zp-|zpbs-|zh-)~', $id) || strpos($scripts, $id) !== false)) { continue; }
            $contents = substr($html, $gt + 1, $end - $gt - 1);
            $close = strripos($contents, '</style');
            if ($close === false) { continue; }
            $css = substr($contents, 0, $close);
            $hash = hash('sha256', $css);
            $entry = $manifest['styles'][$hash] ?? null;
            if (!is_array($entry) || ($entry['source_bytes'] ?? -1) !== strlen($css)) { continue; }
            $offset = $entry['offset'] ?? -1; $length = $entry['length'] ?? 0;
            if (!is_int($offset) || !is_int($length) || $offset < 0 || $length < 2500 || $offset + $length > strlen($css)) { continue; }
            $node = ['start' => $start, 'end' => $end, 'open' => $open, 'close' => substr($contents, $close),
                'css' => $css, 'hash' => $hash, 'offset' => $offset, 'length' => $length,
                'full' => $offset === 0 && $length === strlen($css)];
            $last = count($runs) - 1;
            $previous = $last >= 0 ? $runs[$last][count($runs[$last]) - 1] : null;
            $gap = $previous ? substr($html, $previous['end'], $start - $previous['end']) : '';
            if ($previous && $previous['full'] && $node['full']
                && preg_match('~\A(?:\s|<!--(?:(?!-->).)*-->)*\z~sD', $gap)) { $runs[$last][] = $node; }
            else { $runs[] = [$node]; }
        }
        $plans = [];
        foreach ($runs as $run) {
            $hashes = array_column($run, 'hash');
            $key = hash('sha256', implode("\n", $hashes));
            $entry = $manifest['runs'][$key] ?? null;
            if (!is_array($entry) || ($entry['hashes'] ?? []) !== $hashes) { continue; }
            $url = zp_css_payload_asset_3273($entry);
            if ($url === null) { continue; }
            $plans[] = ['run' => $run, 'url' => $url, 'bytes' => array_sum(array_column($run, 'length'))];
        }
        usort($plans, static fn($a, $b) => $b['bytes'] <=> $a['bytes'] ?: $a['run'][0]['start'] <=> $b['run'][0]['start']);
        $plans = array_slice($plans, 0, 3);
        usort($plans, static fn($a, $b) => $a['run'][0]['start'] <=> $b['run'][0]['start']);
        $ops = []; $preloads = '';
        foreach ($plans as $plan) {
            if (!isset($preloaded[$plan['url']])) {
                $preloads .= '<link rel="preload" as="style" href="' . esc_url($plan['url']) . '">';
                $preloaded[$plan['url']] = true;
            }
            foreach ($plan['run'] as $i => $node) {
                $prefix = substr($node['css'], 0, $node['offset']);
                $suffix = substr($node['css'], $node['offset'] + $node['length']);
                // Keep the original style node and id, even when its contents move.
                // This also preserves empty-node checks and stylesheet placeholders.
                $replacement = $node['open'] . $prefix . $node['close'];
                if ($i === 0) {
                    $replacement .= '<link rel="stylesheet" href="' . esc_url($plan['url']) . '" data-zp-payload="3273">';
                }
                if ($suffix !== '') { $replacement .= '<style>' . $suffix . '</style>'; }
                $ops[] = [$node['start'], $node['end'], $replacement];
            }
        }
        if (!$ops) { return $html; }
        if ($preloads !== '') { $ops[] = [$head_open, $head_open, $preloads]; }
        usort($ops, static fn($a, $b) => $a[0] <=> $b[0]);
        $out = ''; $last = 0;
        foreach ($ops as [$start, $end, $replacement]) {
            if ($start < $last) { return $html; }
            $out .= substr($html, $last, $start - $last) . $replacement;
            $last = $end;
        }
        return $out . substr($html, $last);
    } catch (\Throwable $error) { return $html; }
}
