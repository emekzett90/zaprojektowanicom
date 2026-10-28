<?php
/**
 * 3.2.34 — one declarative mobile header/hero layout, independent of JS readiness.
 *
 * 3.2.33 made the bar visible but not the gated call/brand/burger inside it.
 * A delayed readiness script could therefore reveal the header after the hero.
 * Elementor's header ancestors also need to stay out of normal document flow:
 * an arriving builder stylesheet must not create a second header-sized gap.
 *
 * No loader, content hiding, network calls, UA variants, DOM polling or consent changes.
 * Desktop styles and the actual menu controller are preserved.
 */
if (!defined('ABSPATH')) { exit; }

function zp_mobile_shell_enabled_3234(): bool {
    return !is_admin() && (!function_exists('is_preview') || !is_preview())
        && (!function_exists('is_customize_preview') || !is_customize_preview())
        && !isset($_GET['elementor-preview']) && !isset($_GET['fl_builder']) && !isset($_GET['et_fb'])
        && function_exists('zp_suite_308_is_home_like') && zp_suite_308_is_home_like()
        && !(isset($_GET['zp_mobile_shell']) && (string) $_GET['zp_mobile_shell'] === '0')
        && (bool) apply_filters('zp_mobile_shell_enabled_3234', true);
}

function zp_mobile_shell_css_3234(): string {
    $file = ZP_SUITE_PATH . 'assets/css/mobile-header-shell-3234.css';
    return is_readable($file) ? (string) file_get_contents($file) : '';
}

add_filter('body_class', static function (array $classes): array {
    if (zp_mobile_shell_enabled_3234() && !in_array('zp-mobile-shell-3234', $classes, true)) {
        $classes[] = 'zp-mobile-shell-3234';
    }
    return $classes;
});

add_action('wp_head', static function (): void {
    if (!zp_mobile_shell_enabled_3234()) { return; }
    // Also available if the optional response optimizer is disabled or fails closed.
    $font = '';
    if (function_exists('zp_suite_header_first_font_b64')) {
        $font = '@font-face{font-family:"ZP Header Jakarta";font-weight:800;font-style:normal;font-display:swap;src:url("data:font/woff2;base64,' . zp_suite_header_first_font_b64() . '") format("woff2");}';
    }
    echo '<style id="zp-mobile-shell-3234-css" data-zp-critical="3234">' . $font . zp_mobile_shell_css_3234() . '</style>';
}, -999980);

/** Same default mobile logo pixels, available with the initial document.
 * Custom logo URLs keep their existing loading path. Never fetch a remote URL
 * while rendering; prefer the installed original thumbnail and fall back to
 * the packaged copy. The desktop and drawer images are not rewritten.
 */
function zp_mobile_shell_logo_src_3234(string $url): string {
    if (!zp_mobile_shell_enabled_3234()) { return $url; }
    $map = [
        'https://zaprojektowani.com/wp-content/uploads/2026/06/zp_sygnet-300x235.webp' => ['zp_sygnet-300x235.webp', 'assets/logo-branding/zp-sygnet-jasny.webp'],
        'https://zaprojektowani.com/wp-content/uploads/2026/06/zp_sygnet_ciemny-300x235.webp' => ['zp_sygnet_ciemny-300x235.webp', 'assets/img/header/zp-sygnet-ciemny-300x235.webp'],
    ];
    if (!isset($map[$url])) { return $url; }
    static $cache = [];
    if (isset($cache[$url])) { return $cache[$url]; }
    $uploads = wp_upload_dir();
    $local = isset($uploads['basedir']) ? $uploads['basedir'] . '/2026/06/' . $map[$url][0] : '';
    if (!$local || !is_readable($local)) { $local = ZP_SUITE_PATH . $map[$url][1]; }
    if (!is_readable($local)) { return $url; }
    $size = @filesize($local);
    if ($size === false || $size > 16384) { return $url; }
    $bytes = (string) @file_get_contents($local);
    // Require a small actual WebP, never inline arbitrary file contents.
    if (strlen($bytes) > 16384 || substr($bytes, 0, 4) !== 'RIFF' || substr($bytes, 8, 4) !== 'WEBP') { return $url; }
    return $cache[$url] = 'data:image/webp;base64,' . base64_encode($bytes);
}

/** Escape only our generated WebP data URI as an attribute; all other URLs
 * still pass through WordPress's usual protocol allowlist. */
function zp_mobile_shell_logo_attr_3234(string $url): string {
    $src = zp_mobile_shell_logo_src_3234($url);
    return strncmp($src, 'data:image/webp;base64,', 23) === 0 ? esc_attr($src) : esc_url($src);
}

/** Add a token to a tag's class list, without reserializing the document. */
function zp_mobile_shell_class_3234(string $tag, string $class): string {
    $attrs = zp_css_delivery_attributes($tag);
    $tokens = preg_split('~\s+~', trim((string) ($attrs['class'] ?? '')), -1, PREG_SPLIT_NO_EMPTY);
    if (!in_array($class, $tokens, true)) { $tokens[] = $class; }
    return zp_home_first_paint_attribute($tag, 'class', implode(' ', $tokens));
}

/**
 * Mark only the real header's ancestor path. Raw text in scripts/styles/comments
 * is ignored; unknown/malformed nesting returns the original response.
 */
function zp_mobile_shell_html_3234(string $html): string {
    if (!zp_mobile_shell_enabled_3234() || strpos($html, 'data-zp-mobile-shell="3234"') !== false) { return $html; }
    if (!function_exists('zp_speed_scan') || !function_exists('zp_home_first_paint_attribute')) { return $html; }
    try {
        $scan = zp_speed_scan($html);
        if (!$scan || $scan['head_end'] < 0) { return $html; }
        $nav = null; $hero_found = false; $body = null; $stack = []; $path = [];
        $void = ['area','base','br','col','embed','hr','img','input','link','meta','param','source','track','wbr'];
        $offset = $scan['body_tag']; $skip_index = 0; $skip_count = count($scan['skip']);
        while (preg_match('~<\s*(/?)\s*([a-z][a-z0-9:-]*)\b~i', $html, $match, PREG_OFFSET_CAPTURE, $offset)) {
            $start = $match[0][1];
            while ($skip_index < $skip_count && $scan['skip'][$skip_index][1] <= $start) { $skip_index++; }
            if ($skip_index < $skip_count && $start >= $scan['skip'][$skip_index][0]) {
                $offset = $scan['skip'][$skip_index][1];
                continue;
            }
            $end = zp_speed_tag_end($html, $start);
            if ($end < 0) { return $html; }
            $offset = $end + 1;
            $name = strtolower($match[2][0]);
            $closing = $match[1][0] === '/';
            if ($closing) {
                for ($i = count($stack) - 1; $i >= 0; $i--) {
                    if ($stack[$i]['name'] === $name) { $stack = array_slice($stack, 0, $i); break; }
                }
                continue;
            }
            $tag = substr($html, $start, $end - $start + 1);
            $attrs = zp_css_delivery_attributes($tag);
            $node = ['name' => $name, 'start' => $start, 'end' => $end + 1, 'tag' => $tag, 'attrs' => $attrs];
            if ($name === 'body') { $body = $node; }
            if (($attrs['id'] ?? '') === 'zhHero') { $hero_found = true; }
            if (($attrs['id'] ?? '') === 'zpNewNav') {
                if ($nav !== null) { return $html; } // Never normalize a duplicate/ambiguous header.
                $nav = $node; $path = $stack;
            }
            if (!in_array($name, $void, true) && !preg_match('~/\s*>$~', $tag)) { $stack[] = $node; }
        }
        if (!$nav || !$body || !$hero_found) { return $html; }
        $ops = [];
        $body_tag = zp_mobile_shell_class_3234($body['tag'], 'zp-mobile-shell-3234');
        $ops[] = [$body['start'], $body['end'], $body_tag];
        $ops[] = [$nav['start'], $nav['end'], zp_home_first_paint_attribute($nav['tag'], 'data-zp-mobile-shell', '3234')];
        $inside_header = false;
        foreach ($path as $ancestor) {
            $attrs = $ancestor['attrs'];
            $classes = preg_split('~\s+~', trim((string) ($attrs['class'] ?? '')));
            $host = ($attrs['data-elementor-type'] ?? '') === 'header' || in_array('elementor-location-header', $classes, true);
            if ($host) { $inside_header = true; }
            if (!$inside_header || !in_array($ancestor['name'], ['header','section','div'], true)) { continue; }
            $key = $host ? 'data-zp-header-host' : 'data-zp-header-path';
            $ops[] = [$ancestor['start'], $ancestor['end'], zp_home_first_paint_attribute($ancestor['tag'], $key, '3234')];
        }
        // These three scripts only start/finish obsolete header entrance gates.
        // Their removal is limited to this home response. The header font and menu stay.
        foreach ($scan['skip'] as $range) {
            $start = $range[0]; $end = $range[1];
            $node = substr($html, $start, $end - $start);
            if (!preg_match('~^<script\b~i', $node)) { continue; }
            $tag_end = zp_speed_tag_end($node, 0);
            if ($tag_end < 0) { continue; }
            $attrs = zp_css_delivery_attributes(substr($node, 0, $tag_end + 1));
            if (in_array($attrs['id'] ?? '', ['zp-suite-313-mobile-ui-pre','zp-suite-317-mobile-header-early','zp-suite-318-header-first-gate-js'], true)) {
                $ops[] = [$start, $end, ''];
            }
        }
        // Last in head, after all promoted CSS, before either header or hero can paint.
        $css = zp_mobile_shell_css_3234();
        if ($css === '') { return $html; }
        $ops[] = [$scan['head_end'], $scan['head_end'], '<style id="zp-mobile-shell-3234-final" data-zp-critical="3234">' . $css . '</style>'];
        usort($ops, static fn($a, $b) => $a[0] <=> $b[0]);
        $out = ''; $last = 0;
        foreach ($ops as [$start,$end,$new]) {
            if ($start < $last) { return $html; }
            $out .= substr($html, $last, $start - $last) . $new; $last = $end;
        }
        return $out . substr($html, $last);
    } catch (\Throwable $e) { return $html; }
}

add_action('wp_loaded', static function (): void {
    if (get_option('zp_suite_mobile_shell_3234', '') !== 'pending') { return; }
    do_action('zp_html_cache_purge');
    update_option('zp_suite_mobile_shell_3234', 'queued', true);
}, 112);
