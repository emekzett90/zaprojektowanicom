<?php
/**
 * 3.2.83: sample the mobile exit gesture on scroll, without a parser-time layout.
 * Only the exact published PL/EN script is changed. Its first scroll establishes
 * a baseline; depth, later samples, arming, desktop exit and tracking stay intact.
 * Unknown source/attributes/order or enforced CSP retain the original bytes.
 */
if (!defined('ABSPATH')) { exit; }

function zp_home_exit_scroll_3283_html(string $html): string {
    $path = wp_parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH);
    if (!in_array($path, ['/', '/en/'], true) || !function_exists('zp_home_first_paint_enabled')
        || !zp_home_first_paint_enabled() || strpos($html, 'id="zp-home-first-paint-3233"') === false) {
        return $html;
    }
    try {
        foreach (headers_list() as $header) {
            if (stripos($header, 'Content-Security-Policy:') === 0) { return $html; }
        }
        $scan = zp_speed_scan($html);
        if (!$scan || $scan['head_end'] < 0) { return $html; }
        // Use the existing quote-aware tag boundary, including a quoted ">".
        if (preg_match_all('~<meta(?=[\s/>])~i', $html, $metas, PREG_OFFSET_CAPTURE)) {
            foreach ($metas[0] as [$token, $at]) {
                if ($at >= $scan['head_end'] || zp_speed_in_skip($scan['skip'], $at)) { continue; }
                $gt = zp_speed_tag_end($html, $at);
                if ($gt < 0) { return $html; }
                $node = substr($html, $at, $gt + 1 - $at);
                $attrs = zp_css_delivery_attributes($node);
                if (strtolower(trim($attrs['http-equiv'] ?? '')) === 'content-security-policy') { return $html; }
                // Duplicate http-equiv values are ambiguous: retain original delivery.
                if (preg_match_all('~\s+([^\s=/>"\']+)(?:\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s>"\']+))?~', $node, $am)) {
                    $names = array_map('strtolower', $am[1]);
                    if (count($names) !== count(array_unique($names))) { return $html; }
                }
            }
        }
        $found = [];
        foreach ($scan['skip'] as [$start, $end]) {
            if (strncasecmp(substr($html, $start, 7), '<script', 7) !== 0) { continue; }
            $gt = zp_speed_tag_end($html, $start);
            if ($gt < 0) { return $html; }
            $open = substr($html, $start, $gt + 1 - $start);
            $attrs = zp_css_delivery_attributes($open);
            if (($attrs['id'] ?? '') !== 'zp-exit-v42-js') {
                if (strpos($open, 'zp-exit-v42-js') !== false) { return $html; }
                continue;
            }
            // Exactly one id attribute and no type/src/nonce/handlers/new attributes.
            if (!preg_match('~^<script\s+id=("|\')zp-exit-v42-js\1\s*>$~iD', $open)) { return $html; }
            $tail = substr($html, $gt + 1, $end - $gt - 1);
            if (!preg_match('~</script\s*>$~iD', $tail, $closing, PREG_OFFSET_CAPTURE)) { return $html; }
            $body = substr($tail, 0, $closing[0][1]);
            // Both freshly fetched public languages share this exact script.
            if (strlen($body) !== 22481 || !hash_equals(
                '2f9dfcb75abf3a1557cacfa78e477ea0efe03c2fc8e6b7fc34f4b4262a6953f6',
                hash('sha256', $body)
            )) { return $html; }
            $found[] = [$start, $gt + 1, $body];
        }
        if (count($found) !== 1) { return $html; }
        [$start, $body_at, $body] = $found[0];
        // The original inline initializer must follow its real SSR roots.
        $previous = $scan['body_open'];
        foreach (['#zhHero', '#zp-exit-v42', '#zp-exit-v42-form'] as $selector) {
            $roots = zp_speed_matching_tags($html, $scan, $selector, $scan['body_open'], strlen($html));
            if (count($roots) !== 1 || $roots[0][0] < $previous || $roots[0][1] >= $start) { return $html; }
            $previous = $roots[0][0];
        }
        $before = [
            "let lastScrollY =\n        window.scrollY || 0;",
            "const delta =\n                    currentY -\n                    lastScrollY;",
        ];
        $after = [
            'let lastScrollY = null;',
            'const delta = lastScrollY === null ? 0 : currentY - lastScrollY;',
        ];
        foreach ($before as $fragment) {
            if (substr_count($body, $fragment) !== 1) { return $html; }
        }
        $changed = str_replace($before, $after, $body);
        return substr($html, 0, $body_at) . $changed . substr($html, $body_at + strlen($body));
    } catch (\Throwable $error) {
        return $html;
    }
}
