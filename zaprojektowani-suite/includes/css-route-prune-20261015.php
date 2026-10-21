<?php
/**
 * Omit provably irrelevant rules from Suite-owned inline patches.
 *
 * This module has no hooks, option writes, asset extraction or visual changes.
 * The caller passes the final translated HTML from the outer Suite delivery
 * stage. Surviving rules/declarations and all style nodes keep their order.
 * A whole rule is removable only when EVERY branch requires a positive, literal
 * ID outside a functional/attribute selector that names an absent allowed root.
 * Shared classes, modals, contact forms and arbitrary dynamic IDs are NOT in
 * that allowlist. The one dynamic mobile-brand root is retained whenever either
 * parent hero variant exists: the legacy logo-hero controller clones it at runtime.
 *
 * The CSS walker is deliberately not a general CSS optimizer: strings, comments,
 * functions, attributes, declaration bodies, keyframes and unknown at-rules are
 * opaque. It recurses only into known grouping rule lists. Uncertain syntax
 * returns null and the complete original style block remains inline.
 */
if (!defined('ABSPATH')) { exit; }

/** Final-document adapter. Missing scanner, malformed markup or errors fail open. */
function zp_css_route_prune_html(string $html): string {
    if (!function_exists('zp_speed_scan') || !function_exists('zp_speed_tag_end')) { return $html; }
    if (function_exists('is_preview') && is_preview()) { return $html; }
    try {
        $scan = zp_speed_scan($html);
        if ($scan === null) { return $html; }
        $markup = ''; $last = 0;
        foreach ($scan['skip'] as $range) {
            $tag = strtolower(substr($html, $range[0], 10));
            // Templates/noscript are retained: their markup may become active.
            if (!str_starts_with($tag, '<script') && !str_starts_with($tag, '<style') && !str_starts_with($tag, '<!--')) { continue; }
            $markup .= substr($html, $last, $range[0] - $last);
            $last = $range[1];
        }
        $markup .= substr($html, $last);
        $absent = zp_css_delivery_absent_sections($markup);
        if (!$absent) { return $html; }
        $ops = [];
        foreach ($scan['styles'] as $style) {
            $gt = zp_speed_tag_end($html, $style[0]);
            if ($gt < 0) { continue; }
            $attrs = zp_css_delivery_attributes(substr($html, $style[0], $gt + 1 - $style[0]));
            $id = $attrs['id'] ?? '';
            // Anonymous, theme, Elementor and third-party blocks are untouched.
            if (!str_starts_with($id, 'zp-') && !str_starts_with($id, 'zpbs-')) { continue; }
            if (isset($attrs['type']) && !in_array(strtolower(trim($attrs['type'])), ['', 'text/css'], true)) { continue; }
            $contents = substr($html, $gt + 1, $style[1] - $gt - 1);
            $close = strripos($contents, '</style');
            if ($close === false) { continue; }
            $css = substr($contents, 0, $close);
            $relevant = array_values(array_filter($absent, static fn($root) => strpos($css, '#' . $root) !== false));
            if (!$relevant) { continue; }
            $new = zp_css_delivery_prune_css($css, $relevant);
            if ($new !== null && $new !== $css) { $ops[] = [$gt + 1, $gt + 1 + $close, $new]; }
        }
        $out = ''; $last = 0;
        foreach ($ops as [$start, $end, $replacement]) {
            if ($start < $last) { return $html; }
            $out .= substr($html, $last, $start - $last) . $replacement;
            $last = $end;
        }
        return $ops ? $out . substr($html, $last) : $html;
    } catch (\Throwable $error) {
        return $html;
    }
}

/** HTML attributes; first occurrence wins, as in the browser. */
function zp_css_delivery_attributes(string $tag): array {
    $attrs = [];
    if (preg_match_all('~\s([^\s=/>"\']+)\s*(?:=\s*(?:"([^"]*)"|\'([^\']*)\'|([^\s>"\']+)))?~', $tag, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $match) {
            $name = strtolower($match[1]);
            if (!array_key_exists($name, $attrs)) {
                $attrs[$name] = html_entity_decode(($match[2] ?? '') . ($match[3] ?? '') . ($match[4] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }
        }
    }
    return $attrs;
}

/**
 * Seven static page roots plus a guarded runtime clone, not a generic namespace.
 * Input excludes raw script/style/comment text, but keeps templates and noscript.
 * Attribute-like text can conservatively cause a root to be retained, never a
 * root actually present in markup to be removed.
 */
function zp_css_delivery_absent_sections(string $markup): array {
    $ids = []; $brand_parent = false;
    if (preg_match_all('~\s(id|class)\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|([^\s>]+))~i', $markup, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $match) {
            $value = html_entity_decode(($match[2] ?? '') . ($match[3] ?? '') . ($match[4] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            if (strtolower($match[1]) === 'id') { $ids[$value] = true; }
            elseif (in_array('zpBrandHeroSafe', preg_split('~\s+~', trim($value)) ?: [], true)) { $brand_parent = true; }
        }
    }
    $roots = ['zpAboutPage', 'zpbsUltimate', 'zpBrandHeroKatSafe', 'zpStronyKatowice', 'zpShopKatowice', 'zpShopHeroKat', 'zp-sklepy-internetowe-katowice'];
    if (!isset($ids['zpBrandHeroKatSafe']) && !$brand_parent) { $roots[] = 'zpBrandHeroMobileKat'; }
    return array_values(array_filter($roots, static fn($root) => !isset($ids[$root])));
}

/** Split at a top-level delimiter; escaped characters/comments/strings are opaque. */
function zp_css_delivery_css_parts(string $text, string $delimiter): ?array {
    if (strlen($delimiter) !== 1) { return null; }
    $parts = []; $start = 0; $quote = ''; $paren = 0; $bracket = 0; $len = strlen($text);
    for ($i = 0; $i < $len; $i++) {
        $char = $text[$i];
        if ($char === '\\') { if ($i + 1 >= $len) { return null; } $i++; continue; }
        if ($quote !== '') { if ($char === $quote) { $quote = ''; } continue; }
        if ($char === '"' || $char === "'") { $quote = $char; continue; }
        if ($char === '/' && ($text[$i + 1] ?? '') === '*') {
            $end = strpos($text, '*/', $i + 2); if ($end === false) { return null; }
            $i = $end + 1; continue;
        }
        if ($char === '(') { $paren++; } elseif ($char === ')' && --$paren < 0) { return null; }
        if ($char === '[') { $bracket++; } elseif ($char === ']' && --$bracket < 0) { return null; }
        if ($char === $delimiter && $paren === 0 && $bracket === 0) { $parts[] = substr($text, $start, $i - $start); $start = $i + 1; }
    }
    if ($quote !== '' || $paren !== 0 || $bracket !== 0) { return null; }
    $parts[] = substr($text, $start);
    return $parts;
}

/** Positive literal ID outside functions/attribute selectors is necessary to match. */
function zp_css_delivery_selector_needs_absent(string $selector, array $absent): bool {
    $quote = ''; $paren = 0; $bracket = 0; $len = strlen($selector);
    for ($i = 0; $i < $len; $i++) {
        $char = $selector[$i];
        if ($char === '\\') { $i++; continue; }
        if ($quote !== '') { if ($char === $quote) { $quote = ''; } continue; }
        if ($char === '"' || $char === "'") { $quote = $char; continue; }
        if ($char === '/' && ($selector[$i + 1] ?? '') === '*') {
            $end = strpos($selector, '*/', $i + 2); if ($end === false) { return false; }
            $i = $end + 1; continue;
        }
        if ($char === '(') { $paren++; } elseif ($char === ')') { $paren--; }
        if ($char === '[') { $bracket++; } elseif ($char === ']') { $bracket--; }
        if ($char !== '#' || $paren !== 0 || $bracket !== 0) { continue; }
        // A non-ASCII suffix or CSS escape is part of a different identifier.
        if (preg_match('~\G#([A-Za-z_][A-Za-z0-9_-]*)(?![A-Za-z0-9_\x80-\xff\\\\-])~A', $selector, $match, 0, $i)
            && in_array($match[1], $absent, true)) { return true; }
    }
    return false;
}

/**
 * Balanced rule-list walker. Only WHOLE qualified rules can disappear; a mixed
 * selector list remains byte-identical. Removing one invalid/unsupported branch
 * from a mixed list could activate the surviving branch in some browsers.
 * Rule bodies/unknown at-rules are opaque; nested CSS is retained safely.
 */
function zp_css_delivery_prune_css(string $css, array $absent, int $level = 0): ?string {
    if ($level > 48) { return null; }
    $out = ''; $start = 0; $quote = ''; $paren = 0; $bracket = 0; $len = strlen($css);
    for ($i = 0; $i < $len; $i++) {
        $char = $css[$i];
        if ($char === '\\') { if ($i + 1 >= $len) { return null; } $i++; continue; }
        if ($quote !== '') { if ($char === $quote) { $quote = ''; } continue; }
        if ($char === '"' || $char === "'") { $quote = $char; continue; }
        if ($char === '/' && ($css[$i + 1] ?? '') === '*') {
            $end = strpos($css, '*/', $i + 2); if ($end === false) { return null; }
            $i = $end + 1; continue;
        }
        if ($char === '(') { $paren++; } elseif ($char === ')' && --$paren < 0) { return null; }
        if ($char === '[') { $bracket++; } elseif ($char === ']' && --$bracket < 0) { return null; }
        if ($paren || $bracket) { continue; }
        if ($char === ';') {
            $statement = substr($css, $start, $i + 1 - $start);
            $clean = ltrim((string) preg_replace('~/\*.*?\*/~s', '', $statement));
            if (!preg_match('~^@[A-Za-z-]+\b~', $clean)) { return null; }
            $out .= $statement; $start = $i + 1; continue;
        }
        if ($char === '}') { return null; }
        if ($char !== '{') { continue; }
        $prelude = substr($css, $start, $i - $start);
        $open = $i; $depth = 1; $inner_quote = ''; $inner_paren = 0; $inner_bracket = 0;
        for ($i++; $i < $len; $i++) {
            $value = $css[$i];
            if ($value === '\\') { if ($i + 1 >= $len) { return null; } $i++; continue; }
            if ($inner_quote !== '') { if ($value === $inner_quote) { $inner_quote = ''; } continue; }
            if ($value === '"' || $value === "'") { $inner_quote = $value; continue; }
            if ($value === '/' && ($css[$i + 1] ?? '') === '*') {
                $end = strpos($css, '*/', $i + 2); if ($end === false) { return null; }
                $i = $end + 1; continue;
            }
            if ($value === '(') { $inner_paren++; } elseif ($value === ')' && --$inner_paren < 0) { return null; }
            if ($value === '[') { $inner_bracket++; } elseif ($value === ']' && --$inner_bracket < 0) { return null; }
            if ($inner_paren || $inner_bracket) { continue; }
            if ($value === '{') { $depth++; } elseif ($value === '}' && --$depth === 0) { break; }
        }
        if ($depth !== 0 || $inner_quote !== '' || $inner_paren !== 0 || $inner_bracket !== 0) { return null; }
        $inner = substr($css, $open + 1, $i - $open - 1);
        $clean = ltrim((string) preg_replace('~/\*.*?\*/~s', '', $prelude));
        if ($clean === '') { return null; }
        if (str_starts_with($clean, '@')) {
            if (preg_match('~^@(media|supports|container|layer|starting-style)\b~i', $clean)) {
                $pruned = zp_css_delivery_prune_css($inner, $absent, $level + 1);
                if ($pruned === null) { return null; }
                $inner = $pruned;
            }
            $out .= $prelude . '{' . $inner . '}';
        } else {
            $selectors = zp_css_delivery_css_parts($prelude, ',');
            if ($selectors === null || in_array('', array_map('trim', $selectors), true)) { return null; }
            $all_absent = true;
            foreach ($selectors as $selector) {
                if (!zp_css_delivery_selector_needs_absent($selector, $absent)) { $all_absent = false; break; }
            }
            if (!$all_absent) { $out .= $prelude . '{' . $inner . '}'; }
        }
        $start = $i + 1;
    }
    if ($quote !== '' || $paren !== 0 || $bracket !== 0) { return null; }
    $tail = substr($css, $start);
    if (trim((string) preg_replace('~/\*.*?\*/~s', '', $tail)) !== '') { return null; }
    return $out . $tail;
}
