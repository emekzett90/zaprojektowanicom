<?php
/**
 * 3.2.35: put the ONE existing homepage header before the page, on the server.
 *
 * Visibility/height rules cannot make an absent header paint, nor override a
 * hidden/transformed ancestor above Elementor's header document. 3.2.34 kept
 * that ancestry. This adapter removes that dependency before any HTML is sent.
 * It does not clone the menu, wait for JS, hide the hero, sniff devices, or change
 * consent. IDs, links, event targets, text and both language versions are kept.
 * The mobile row is first in its shell; the original desktop row still uses CSS.
 *
 * This outer buffer is registered BEFORE the optional speed/language buffers,
 * so it also works when CSS optimization is disabled and sees their final HTML.
 */
if (!defined('ABSPATH')) { exit; }

function zp_header_ssr_enabled_3235(): bool {
    return !is_admin() && !wp_doing_ajax() && !wp_doing_cron()
        && !(defined('REST_REQUEST') && REST_REQUEST)
        && (!function_exists('is_feed') || !is_feed())
        && (!function_exists('is_preview') || !is_preview())
        && (!function_exists('is_customize_preview') || !is_customize_preview())
        && !isset($_GET['elementor-preview']) && !isset($_GET['fl_builder']) && !isset($_GET['et_fb'])
        && function_exists('zp_suite_308_is_home_like') && zp_suite_308_is_home_like()
        && !(isset($_GET['zp_header_ssr']) && (string) $_GET['zp_header_ssr'] === '0')
        && (bool) apply_filters('zp_header_ssr_enabled_3235', true);
}

/** Scan actual tags; ignore raw text, SVG and template/noscript contents. */
function zp_header_ssr_nodes_3235(string $html, array $scan): ?array {
    $nodes = []; $stack = []; $skip_i = 0; $offset = $scan['body_tag'];
    $void = ['area','base','br','col','embed','hr','img','input','link','meta','param','source','track','wbr'];
    while (preg_match('~<\s*(/?)\s*([a-z][a-z0-9:-]*)\b~i', $html, $m, PREG_OFFSET_CAPTURE, $offset)) {
        $at = $m[0][1];
        while (isset($scan['skip'][$skip_i]) && $scan['skip'][$skip_i][1] <= $at) { $skip_i++; }
        if (isset($scan['skip'][$skip_i]) && $at >= $scan['skip'][$skip_i][0]) {
            $offset = $scan['skip'][$skip_i][1]; continue;
        }
        $gt = zp_speed_tag_end($html, $at); if ($gt < 0) { return null; }
        $offset = $gt + 1; $name = strtolower($m[2][0]);
        if ($m[1][0] === '/') {
            // Keep browser-style recovery for optional end tags, but only move a
            // header that itself has an explicit matching closing tag.
            for ($i = count($stack) - 1; $i >= 0; $i--) {
                $key = $stack[$i];
                if ($nodes[$key]['name'] === $name) {
                    $nodes[$key]['end'] = $offset;
                    $nodes[$key]['close'] = $at;
                    $stack = array_slice($stack, 0, $i); break;
                }
            }
            continue;
        }
        $tag = substr($html, $at, $offset - $at);
        $attrs = zp_css_delivery_attributes($tag);
        $key = count($nodes);
        $nodes[] = ['name'=>$name, 'start'=>$at, 'open'=>$offset, 'end'=>null, 'close'=>null,
                    'attrs'=>$attrs, 'parent'=>$stack ? end($stack) : null];
        if (($name === 'svg' || $name === 'math') && !preg_match('~/\s*>$~', $tag)) {
            $end = zp_speed_nested_end($html, $name, $offset);
            if ($end < 0) { return null; }
            $nodes[$key]['end'] = $end; $offset = $end; continue;
        }
        if (!in_array($name, $void, true) && !preg_match('~/\s*>$~', $tag)) { $stack[] = $key; }
        else { $nodes[$key]['end'] = $offset; }
    }
    return $nodes;
}

/** An old header-only layout slot can collapse on mobile; never hide siblings. */
function zp_header_ssr_empty_slot_3235(string $html, array $node, array $nav): bool {
    if (!in_array($node['name'], ['div','header','section'], true) || $node['end'] === null
        || $node['start'] >= $nav['start'] || $node['end'] <= $nav['end']) { return false; }
    $rest = substr($html, $node['open'], $nav['start'] - $node['open'])
        . substr($html, $nav['end'], $node['close'] - $nav['end']);
    $rest = preg_replace('~<(script|style|template)\b[^>]*>.*?</\1\s*>|<!--.*?-->~is', '', $rest);
    // Only empty transparent wrappers and non-rendering metadata may remain.
    // In particular an img, anchor, form, text, iframe or noscript fails closed.
    $rest = preg_replace('~</?(?:div|header|section)\b[^>]*>|<(?:link|meta)\b[^>]*>~i', '', (string) $rest);
    return trim($rest) === '';
}

/** Apply offset edits without reserializing HTML, URLs, SVG or JSON-LD. */
function zp_header_ssr_edits_3235(string $html, array $ops): string {
    usort($ops, static fn($a, $b) => $a[0] <=> $b[0] ?: $a[1] <=> $b[1]);
    $out = ''; $last = 0;
    foreach ($ops as [$start,$end,$new]) {
        if ($start < $last || $end < $start) { return $html; }
        $out .= substr($html, $last, $start - $last) . $new; $last = $end;
    }
    return $out . substr($html, $last);
}

function zp_header_ssr_html_3235(string $html): string {
    if (!zp_header_ssr_enabled_3235() || strlen($html) < 500 || strlen($html) > 16777216
        || !function_exists('zp_speed_scan') || !function_exists('zp_home_first_paint_attribute')) { return $html; }
    try {
        $scan = zp_speed_scan($html);
        if (!$scan || $scan['head_end'] < 0 || stripos($html, '</body') === false) { return $html; }
        $nodes = zp_header_ssr_nodes_3235($html, $scan);
        if (!$nodes) { return $html; }
        $navs = []; $heroes = []; $body = null; $bar = null; $shell = null;
        foreach ($nodes as $key => $node) {
            if (($node['attrs']['id'] ?? '') === 'zpNewNav') { $navs[] = $key; }
            if (($node['attrs']['id'] ?? '') === 'zhHero') { $heroes[] = $key; }
            if ($node['name'] === 'body') { if ($body !== null) { return $html; } $body = $node; }
        }
        if (count($navs) !== 1 || count($heroes) !== 1 || !$body) { return $html; }
        $nav = $nodes[$navs[0]]; $hero = $nodes[$heroes[0]];
        if (($nav['attrs']['data-zp-header-ssr'] ?? '') === '3235'
            && $nav['parent'] !== null && $nodes[$nav['parent']]['name'] === 'body') { return $html; }
        if ($nav['name'] !== 'header' || !$nav['end'] || !$hero['end']
            || ($nav['start'] < $hero['start'] && $nav['end'] > $hero['start'])
            || ($hero['start'] < $nav['start'] && $hero['end'] > $nav['start'])) { return $html; }
        foreach ($nodes as $node) {
            if ($node['start'] <= $nav['start'] || $node['start'] >= $nav['end']) { continue; }
            $classes = preg_split('~\s+~', trim((string) ($node['attrs']['class'] ?? '')));
            if (in_array('zpNewNav__shell', $classes, true)) { if ($shell !== null) { return $html; } $shell = $node; }
            if (in_array('zpNewNav__mobileBar', $classes, true)) { if ($bar !== null) { return $html; } $bar = $node; }
        }
        if (!$bar || !$shell || !$bar['end'] || !$shell['end'] || $bar['start'] < $shell['open'] || $bar['end'] > $shell['end']) { return $html; }
        $markup = substr($html, $nav['start'], $nav['end'] - $nav['start']);
        // First useful mobile pixels before the large, desktop-only mega-menu.
        $mobile = substr($html, $bar['start'], $bar['end'] - $bar['start']);
        $markup = zp_header_ssr_edits_3235($markup, [
            [$shell['open']-$nav['start'], $shell['open']-$nav['start'], $mobile],
            [$bar['start']-$nav['start'], $bar['end']-$nav['start'], ''],
        ]);
        $open_end = zp_speed_tag_end($markup, 0);
        if ($open_end < 0) { return $html; }
        $open_tag = zp_home_first_paint_attribute(substr($markup, 0, $open_end + 1), 'data-zp-header-ssr', '3235');
        $markup = $open_tag . substr($markup, $open_end + 1);

        $body_tag = substr($html, $body['start'], $body['open']-$body['start']);
        $classes = preg_split('~\s+~', trim((string) ($body['attrs']['class'] ?? '')), -1, PREG_SPLIT_NO_EMPTY);
        $classes = array_values(array_unique(array_merge($classes, ['zp-mobile-shell-3234','zp-header-ssr-3235'])));
        $body_tag = zp_home_first_paint_attribute($body_tag, 'class', implode(' ', $classes));
        $ops = [[$body['start'], $body['open'], $body_tag . "\n" . $markup . "\n"],
                [$nav['start'], $nav['end'], '<!-- ZP 3.2.35: existing header emitted at body start -->']];
        $parent = $nav['parent'];
        while ($parent !== null && isset($nodes[$parent])) {
            $node = $nodes[$parent];
            if ($node['name'] === 'body') { break; }
            if (zp_header_ssr_empty_slot_3235($html, $node, $nav)) {
                $tag = substr($html, $node['start'], $node['open'] - $node['start']);
                $ops[] = [$node['start'], $node['open'], zp_home_first_paint_attribute($tag, 'data-zp-header-slot', '3235')];
            }
            $parent = $node['parent'];
        }
        // Last in head, not downloaded later or dependent on an optimization toggle.
        $css = function_exists('zp_mobile_shell_css_3234') ? zp_mobile_shell_css_3234() : '';
        if ($css === '') { return $html; }
        foreach ($scan['styles'] as $range) {
            if ($range[0] >= $scan['head_end']) { continue; }
            $tag_end = zp_speed_tag_end($html, $range[0]);
            $attrs = zp_css_delivery_attributes(substr($html, $range[0], $tag_end-$range[0]+1));
            if (($attrs['id'] ?? '') === 'zp-mobile-shell-3234-final') {
                $ops[] = [$range[0], $range[1], ''];
            }
        }
        $css .= '@media(max-width:1100px){html body.zp-header-ssr-3235 [data-zp-header-slot="3235"]:not(#zpx){display:none!important;height:0!important;min-height:0!important;max-height:0!important;margin:0!important;padding:0!important;border:0!important;}html body.zp-header-ssr-3235>header#zpNewNav[data-zp-header-ssr="3235"]{isolation:isolate;overflow-anchor:none!important;}html body.zp-header-ssr-3235>header#zpNewNav[data-zp-header-ssr="3235"] .zpNewNav__mobileCall,html body.zp-header-ssr-3235>header#zpNewNav[data-zp-header-ssr="3235"] .zpNewNav__burger{content-visibility:visible!important;}}';
        $ops[] = [$scan['head_end'], $scan['head_end'], '<meta name="zp-header-layout" content="3.2.35; server-first"><style id="zp-header-ssr-3235-critical" data-zp-critical="3235">' . $css . '</style>'];
        return zp_header_ssr_edits_3235($html, $ops);
    } catch (\Throwable $error) { return $html; }
}

function zp_header_ssr_buffer_3235($chunk, $phase = 0) {
    static $buffer = '';
    if (!is_string($chunk)) { return $chunk; }
    if ($phase & PHP_OUTPUT_HANDLER_CLEAN) { $buffer = ''; return ''; }
    $buffer .= $chunk;
    if (!($phase & PHP_OUTPUT_HANDLER_FINAL)) { return ''; }
    $html = $buffer; $buffer = '';
    $status = (int) http_response_code();
    if ($status >= 300 && $status < 400) { return $html; }
    foreach (headers_list() as $header) {
        if (stripos($header, 'content-type:') === 0 && stripos($header, 'text/html') === false) { return $html; }
        // Content-Encoding can already be set by an OUTER compression buffer.
        // Parse actual HTML here; already-compressed bytes fail the scanner safely.
    }
    try { $html = zp_header_ssr_html_3235($html); return function_exists('zp_first_screen_delivery_3236') ? zp_first_screen_delivery_3236($html) : $html; } catch (\Throwable $error) { return $html; }
}

// URI check only limits buffer allocation before WP's main query exists. There
// is no device-dependent HTML and no new caching/consent behavior.
$zp_header_ssr_path_3235 = parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH);
if (!is_admin() && !wp_doing_ajax() && !wp_doing_cron()
    && !(defined('WP_CLI') && WP_CLI) && !(defined('XMLRPC_REQUEST') && XMLRPC_REQUEST)
    && in_array(strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET')), ['GET','HEAD'], true)
    && in_array($zp_header_ssr_path_3235, ['/','/en','/en/'], true)) {
    ob_start('zp_header_ssr_buffer_3235');
}
unset($zp_header_ssr_path_3235);
