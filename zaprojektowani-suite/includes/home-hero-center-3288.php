<?php
/**
 * Keep decorative hero lights centered without percentage-top layout shifts.
 * The full-size wrapper translates by half the stage height. Child percentages,
 * masks, animations and final visual positions are unchanged. No new JS/work.
 */
if (!defined('ABSPATH')) { exit; }

function zp_home_hero_center_3288_html(string $html): string {
    $path = wp_parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH);
    if (!in_array($path, ['/', '/en/'], true) || !function_exists('zp_home_first_paint_enabled')
        || !zp_home_first_paint_enabled() || strpos($html, 'id="zp-home-first-paint-3233"') === false
        || strpos($html, 'zp-hero-center-3288') !== false) { return $html; }
    try {
        foreach (headers_list() as $header) {
            if (stripos($header, 'Content-Security-Policy:') === 0) { return $html; }
        }
        // An unexpected template keeps its original markup, including CSP pages.
        if (stripos($html, 'content-security-policy') !== false) { return $html; }
        $scan = zp_speed_scan($html);
        if (!$scan || substr_count($html, 'id="zhHero"') !== 1 || substr_count($html, 'id="zhStage"') !== 1) { return $html; }
        if (!preg_match('~<div\b[^>]*\bid="zhStage"[^>]*>~', $html, $m, PREG_OFFSET_CAPTURE)) { return $html; }
        $start = $m[0][1];
        if (zp_speed_in_skip($scan['skip'], $start)) { return $html; }
        $end = zp_speed_nested_end($html, 'div', $start + strlen($m[0][0]));
        if ($end < 0) { return $html; }
        $stage = substr($html, $start, $end - $start);
        $pattern = '~<div class="zh__conic"></div>\s*<div class="zh__halo"></div>~';
        if (preg_match_all($pattern, $stage, $pairs, PREG_OFFSET_CAPTURE) !== 1) { return $html; }
        [$pair, $offset] = $pairs[0][0];
        if (zp_speed_in_skip($scan['skip'], $start + $offset)) { return $html; }
        $wrapper = '<div class="zh__centerFx" aria-hidden="true">' . $pair . '</div>';
        $css = '<style id="zp-hero-center-3288" data-zp-critical="3233">'
            . '#zhHero #zhStage>.zh__centerFx{position:absolute;inset:0;transform:translateY(50%);pointer-events:none}'
            . '#zhHero #zhStage>.zh__centerFx>.zh__conic,#zhHero #zhStage>.zh__centerFx>.zh__halo{top:0}'
            // Mobile artwork stays at the same coordinates; only its CSS box fills the stage.
            . '@media(max-width:1100px){html body #zhHero #zhStage::before,html body #zhHero #zhStage::after{inset:0!important;width:100%!important;height:100%!important;aspect-ratio:auto!important;transform:none!important;background-repeat:no-repeat!important;-webkit-mask-repeat:no-repeat!important;mask-repeat:no-repeat!important}html body #zhHero #zhStage::before{background-size:420px 328.3px!important;background-position:calc(50% + 8.4px) calc(56% - 100.302px)!important;-webkit-mask-size:420px 328.3px!important;mask-size:420px 328.3px!important;-webkit-mask-position:calc(50% + 8.4px) calc(56% - 100.302px)!important;mask-position:calc(50% + 8.4px) calc(56% - 100.302px)!important}html body #zhHero #zhStage::after{background-size:400px 400px!important;background-position:calc(50% + 8px) calc(56% - 96px)!important}}'
            . '</style>';
        $at = $start + $offset;
        if ($scan['head_end'] >= $at) { return $html; }
        $out = substr($html, 0, $at) . $wrapper . substr($html, $at + strlen($pair));
        return substr($out, 0, $scan['head_end']) . $css . substr($out, $scan['head_end']);
    } catch (\Throwable $error) { return $html; }
}
