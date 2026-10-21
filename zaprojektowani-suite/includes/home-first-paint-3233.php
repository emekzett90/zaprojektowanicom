<?php
/**
 * 3.2.33 — CSS needed by the mobile first screen is delivered in the initial HTML.
 *
 * This runs after all of Suite's CSS extraction passes, not after DOMContentLoaded.
 * Only Suite-owned local CSS is eligible. Unknown/unreadable/unsupported sheets
 * keep their original delivery. No UA sniffing, consent interception, loader,
 * hiding the page, timeout, window-load dependency or database content changes.
 *
 * For an eligible linked sheet its complete first-screen subset is inserted
 * immediately AFTER that link, at the same cascade position. The full sheet
 * loads non-blockingly on mobile and normally on desktop. A full-sheet arrival
 * therefore cannot replace a temporary header/hero layout with another layout.
 * Body style nodes are promoted to head in their original relative order before
 * HTML parsing reaches header/hero, avoiding successive corrections while parsing.
 */
if (!defined('ABSPATH')) { exit; }

function zp_home_first_paint_enabled(): bool {
    return !is_admin() && (!function_exists('is_preview') || !is_preview())
        && (!function_exists('is_customize_preview') || !is_customize_preview())
        && function_exists('zp_suite_308_is_home_like') && zp_suite_308_is_home_like()
        && !(isset($_GET['zp_first_paint']) && (string) $_GET['zp_first_paint'] === '0')
        && (bool) apply_filters('zp_home_first_paint_enabled', true);
}

/** Collect literal IDs/classes from the actual SSR hero, header and their ancestors. */
function zp_home_first_paint_known(string $html, array $scan): ?array {
    $markup = ''; $prefix_end = 0;
    foreach (['zhHero','zpNewNav'] as $id) {
        $offset = 0; $found = false;
        while (preg_match('~<([a-z][a-z0-9:-]*)\b[^>]*\bid\s*=\s*(["\'])' . $id . '\2[^>]*>~i', $html, $m, PREG_OFFSET_CAPTURE, $offset)) {
            $offset = $m[0][1] + strlen($m[0][0]);
            if (!zp_speed_in_skip($scan['skip'], $m[0][1])) { $found = true; break; }
        }
        if (!$found) { return null; }
        $start = $m[0][1]; $prefix_end = max($prefix_end, $start);
        $end = zp_speed_nested_end($html, strtolower($m[1][0]), $start + strlen($m[0][0]));
        if ($end < 0) { return null; }
        $markup .= substr($html, $start, $end - $start);
    }
    // Include the actual ancestors supplied by WordPress/the page builder, not a hardcoded page ID.
    $markup .= substr($html, 0, $prefix_end);
    // Strip raw styles/scripts before collecting class and ID attributes.
    $markup = preg_replace('~<(script|style)\b[^>]*>.*?</\1\s*>|<!--.*?-->~is', '', $markup);
    $known = ['#' => [], '.' => []];
    if (preg_match_all('~\s(id|class)\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|([^\s>]+))~i', $markup, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $m) {
            $value = html_entity_decode(($m[2] ?? '') . ($m[3] ?? '') . ($m[4] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $kind = strtolower($m[1]) === 'id' ? '#' : '.';
            foreach ($kind === '#' ? [$value] : preg_split('~\s+~', trim($value)) as $name) { $known[$kind][$name] = true; }
        }
    }
    // Ancestors and state classes can come from WP/Elementor or JS, not just this shortcode.
    foreach (['zpHomeContent','entry-content','page-content','site-main','site-content','elementor','elementor-element','elementor-widget','elementor-widget-container','e-con','e-con-inner','home','page','front-page','page-template-default','active','open','loaded','ready','scrolled','locked','js','no-js','admin-bar','wp-embed-responsive'] as $name) { $known['.'][$name] = true; }
    foreach (['content','page','main','masthead','primary','wpadminbar'] as $id) { $known['#'][$id] = true; }
    return $known;
}

/** A positive top-level ID/class absent from the first screen cannot match it.
 * Functions/attributes remain opaque, including :not(#zpx) specificity guards.
 * Dynamic navigation, consent and root states are retained conservatively.
 */
function zp_home_first_paint_absent(string $selector, array $known): bool {
    $quote = ''; $paren = 0; $bracket = 0; $len = strlen($selector);
    for ($i = 0; $i < $len; $i++) {
        $c = $selector[$i];
        if ($c === '\\') { $i++; continue; }
        if ($quote !== '') { if ($c === $quote) { $quote = ''; } continue; }
        if ($c === '"' || $c === "'") { $quote = $c; continue; }
        if ($c === '/' && ($selector[$i + 1] ?? '') === '*') { $end = strpos($selector, '*/', $i + 2); if ($end === false) { return false; } $i = $end + 1; continue; }
        if ($c === '(') { $paren++; } elseif ($c === ')') { $paren--; }
        if ($c === '[') { $bracket++; } elseif ($c === ']') { $bracket--; }
        if (($c !== '#' && $c !== '.') || $paren || $bracket) { continue; }
        if (!preg_match('~\G[.#]([A-Za-z_][A-Za-z0-9_-]*)(?![A-Za-z0-9_\x80-\xff\\\\-])~A', $selector, $m, 0, $i)) { continue; }
        $name = $m[1];
        if (isset($known[$c][$name]) || preg_match('~^(?:zp-|zpNewNav-|is-|has-|Cybot|Cookiebot|cookiebot|zpCookie|wp-|elementor-|e-con|page-id-|postid-)~', $name)) { continue; }
        return true;
    }
    return false;
}

/** Accept only our plugin files or our generated CSS store, never arbitrary uploads/URLs. */
function zp_home_first_paint_file(string $url): ?string {
    $path = zp_speed_local_file($url);
    if (!$path || !is_file($path) || strtolower(pathinfo($path, PATHINFO_EXTENSION)) !== 'css') { return null; }
    $real = realpath($path); $root = realpath(ZP_SUITE_PATH);
    $store = function_exists('zp_speed_files_store') ? zp_speed_files_store() : null;
    $generated = $store ? realpath($store[0]) : false;
    if (!$real || ((!$root || strpos($real, $root . DIRECTORY_SEPARATOR) !== 0) && (!$generated || strpos($real, $generated . DIRECTORY_SEPARATOR) !== 0))) { return null; }
    if (!is_readable($real) || filesize($real) > 2097152) { return null; }
    return $real;
}

/** CSS relative URL rebasing; uncertain forms/imports leave a sheet blocking. */
function zp_home_first_paint_rebase(string $css, string $url): ?string {
    if (preg_match('~@import\b|</style~i', $css)) { return null; }
    $parts = wp_parse_url($url);
    $path = (string) ($parts['path'] ?? '');
    if ($path === '' || $path[0] !== '/') { return null; }
    $base = substr($path, 0, strrpos($path, '/') + 1);
    $ok = true;
    $out = preg_replace_callback('~url\(\s*(?:"([^"]*)"|\'([^\']*)\'|([^\s()]+))\s*\)~i', static function ($m) use ($base, &$ok) {
        $value = ($m[1] ?? '') . ($m[2] ?? '') . ($m[3] ?? '');
        if ($value === '' || preg_match('~^(?:/|\#|data:|[a-z][a-z0-9+.-]*:)~i', $value)) { return $m[0]; }
        if (strpos($value, '\\') !== false || preg_match('~[\x00-\x20"\'<>]~', $value)) { $ok = false; return $m[0]; }
        $segments = [];
        foreach (explode('/', $base . $value) as $part) {
            if ($part === '' || $part === '.') { continue; }
            if ($part === '..') { array_pop($segments); } else { $segments[] = $part; }
        }
        return 'url("/' . implode('/', $segments) . '")';
    }, $css);
    return $ok && is_string($out) ? $out : null;
}

/** Only edits attributes on an already scanned tag, never scans CSS/JS with tag regexes. */
function zp_home_first_paint_attribute(string $tag, string $name, string $value): string {
    $pattern = '~\s' . preg_quote($name, '~') . '(?:\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s>]+))?(?=\s|/?>)~i';
    $tag = preg_replace($pattern, '', $tag);
    $end = strrpos($tag, '>');
    $pos = $end > 0 && $tag[$end - 1] === '/' ? $end - 1 : $end;
    return substr($tag, 0, $pos) . ' ' . $name . '="' . esc_attr($value) . '"' . substr($tag, $pos);
}

/** Final response adapter. Failure returns the original document, never unstyled HTML. */
function zp_home_first_paint_html(string $html): string {
    if (!zp_home_first_paint_enabled() || strpos($html, 'id="zp-home-first-paint-3233"') !== false) { return $html; }
    try {
        $scan = zp_speed_scan($html);
        if (!$scan || !($known = zp_home_first_paint_known($html, $scan))) { return $html; }
        $ops = []; $promoted = []; $deferred = 0; $critical_bytes = 0; $subsets = []; $deferred_urls = [];
        // Finish all Suite-owned body CSS before the browser reaches the header/hero.
        foreach ($scan['styles'] as $s) {
            if ($s[0] < $scan['body_open']) { continue; }
            $node = substr($html, $s[0], $s[1] - $s[0]);
            $gt = zp_speed_tag_end($node, 0); if ($gt < 0) { continue; }
            $attrs = zp_css_delivery_attributes(substr($node, 0, $gt + 1));
            $id = $attrs['id'] ?? '';
            $own = preg_match('~^(?:zp-|zpbs-|zh-)~', $id) || ($id === '' && preg_match('~#zhHero\b|\.zh__|#zpNewNav\b|\.zpNewNav__~', $node));
            if (!$own) { continue; }
            $promoted[] = [$s[0], $node]; $ops[] = [$s[0], $s[1], ''];
        }
        foreach ($scan['sheet_links'] as $s) {
            $node = substr($html, $s[0], $s[1] - $s[0]);
            $attrs = zp_css_delivery_attributes($node);
            $url = $attrs['href'] ?? '';
            $file = zp_home_first_paint_file($url);
            if (!$file || isset($attrs['disabled']) || isset($attrs['integrity']) || stripos($attrs['rel'] ?? '', 'alternate') !== false) { continue; }
            if (!array_key_exists($file, $subsets)) {
                $raw = file_get_contents($file);
                $css = is_string($raw) ? zp_home_first_paint_rebase($raw, $url) : null;
                $subsets[$file] = is_string($css) ? zp_home_first_paint_css(ltrim($css, "\xEF\xBB\xBF"), $known) : null;
            }
            $subset = $subsets[$file];
            if ($subset === null) { continue; }
            // Every matching first-screen rule keeps its exact declarations and order.
            $media = trim($attrs['media'] ?? 'all');
            $critical = $subset;
            if ($media !== '' && $media !== 'all' && $media !== 'screen' && $media !== 'print') { $critical = '@media ' . $media . '{' . $critical . '}'; }
            $replacement = $node;
            if (!isset($attrs['onload'])) {
                // Non-matching media is downloaded without holding mobile's first paint.
                // Full CSS is still fetched immediately (no load/idle timeout) for early scroll/menu.
                $desktop = ($media === '' || $media === 'all' || $media === 'screen') ? '(min-width:1101px)' : '(min-width:1101px) and (' . $media . ')';
                // Avoid invalid nested media syntax; screen conjunction is safe for a single query.
                if ($media !== '' && $media !== 'all' && $media !== 'screen') {
                    if (strpos($media, ',') !== false || preg_match('~^(not|only|print)\b~i', $media)) { continue; }
                    $desktop = '(min-width:1101px) and ' . $media;
                }
                $replacement = zp_home_first_paint_attribute($replacement, 'media', $desktop);
                $handler = 'this.onload=null;this.media=' . json_encode($media ?: 'all', JSON_HEX_APOS | JSON_HEX_QUOT) . ';';
                $replacement = zp_home_first_paint_attribute($replacement, 'onload', $handler);
                $replacement = zp_home_first_paint_attribute($replacement, 'data-zp-home-deferred', '3233');
                $replacement = zp_home_first_paint_attribute($replacement, 'data-zp-home-media', $media ?: 'all');
                // With JS disabled the full original stylesheet applies normally below our critical subset.
                $fallback = '<noscript>' . $node . '</noscript>';
                $deferred++;
                $deferred_urls[$url] = true;
            } else {
                // Keep the existing async callback (including UI guards) and add its missing first-screen subset.
                $fallback = '';
                $replacement = zp_home_first_paint_attribute($replacement, 'data-zp-home-deferred', '3233');
                $replacement = zp_home_first_paint_attribute($replacement, 'data-zp-home-media', $media ?: 'all');
            }
            if (trim($critical) !== '') {
                $replacement .= '<style data-zp-critical="3233" media="(max-width:1100px)">' . $critical . '</style>';
                $critical_bytes += strlen($critical);
            }
            $replacement .= $fallback;
            if ($s[0] >= $scan['body_open']) { $promoted[] = [$s[0], $replacement]; $ops[] = [$s[0], $s[1], '']; }
            else { $ops[] = [$s[0], $s[1], $replacement]; }
        }
        // Avoid preload competition: mobile needs 400/500 plus its image, not every
        // desktop weight/italic face or a full stylesheet that already has inline critical CSS.
        if (preg_match_all('~<link\b[^>]*>~i', $html, $links, PREG_OFFSET_CAPTURE)) {
            foreach ($links[0] as [$tag, $at]) {
                if (zp_speed_in_skip($scan['skip'], $at)) { continue; }
                $a = zp_css_delivery_attributes($tag);
                if (strtolower($a['rel'] ?? '') !== 'preload') { continue; }
                $href = $a['href'] ?? ''; $as = strtolower($a['as'] ?? '');
                $unneeded_font = $as === 'font' && strpos($href, '/web-font/jakarta/') !== false
                    ;
                if (!$unneeded_font && !($as === 'style' && isset($deferred_urls[$href]))) { continue; }
                $media = trim($a['media'] ?? '');
                if ($media === '' || $media === 'all' || $media === 'screen') { $media = '(min-width:1101px)'; }
                elseif (strpos($media, ',') === false && $media[0] === '(') { $media = '(min-width:1101px) and ' . $media; }
                else { continue; }
                $ops[] = [$at, $at + strlen($tag), zp_home_first_paint_attribute($tag, 'media', $media)];
            }
        }
        // Conservatively cap duplicated critical CSS. Future template changes fail back instead of ballooning HTML.
        if ($critical_bytes > 650000) { return $html; }
        usort($promoted, static fn($a, $b) => $a[0] <=> $b[0]);
        $last_css = ''; foreach ($promoted as $s) { $last_css .= $s[1] . "\n"; }
        $last_css .= '<meta id="zp-home-first-paint-3233" name="zp-home-first-paint" content="3.2.33; nonblocking=' . $deferred . '">';
        $last_css .= zp_home_first_paint_geometry();
        $ops[] = [$scan['head_end'], $scan['head_end'], $last_css];
        usort($ops, static fn($a, $b) => $a[0] <=> $b[0]);
        $out = ''; $last = 0;
        foreach ($ops as [$start, $end, $new]) {
            if ($start < $last) { return $html; }
            $out .= substr($html, $last, $start - $last) . $new; $last = $end;
        }
        return $out . substr($html, $last);
    } catch (\Throwable $e) { return $html; }
}

function zp_home_first_paint_geometry(bool $with_fonts = false): string {
    $file = ZP_SUITE_PATH . 'assets/css/home-mobile-stable-3233.css';
    if (!is_readable($file)) { return ''; }
    $css = file_get_contents($file);
    $fonts = ZP_SUITE_PATH . 'assets/css/home-mobile-fonts-3233.css';
    $faces = $with_fonts && is_readable($fonts) ? file_get_contents($fonts) : '';
    return '<style data-zp-geometry="3233" data-zp-critical="3233">' . $faces . $css . '</style>';
}

// If optimization is explicitly disabled, retain old delivery but still reserve mobile image geometry.
add_action('wp_head', static function (): void {
    if (!zp_home_first_paint_enabled()) { return; }
    echo zp_home_first_paint_geometry(true);
}, PHP_INT_MAX);

add_action('wp_loaded', static function (): void {
    if (get_option('zp_suite_first_paint_3233', '') !== 'pending') { return; }
    do_action('zp_html_cache_purge');
    update_option('zp_suite_first_paint_3233', 'queued', true);
}, 111);

function zp_home_first_paint_css(string $css, array $known, int $level = 0): ?string {
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
            if (preg_match('~^@media\b~i', $clean) && (preg_match('~^@media\s+print\s*$~i', $clean) || preg_match('~min-width\s*:\s*(\d+(?:\.\d+)?)px~i', $clean, $mw) && (float) $mw[1] > 1100 && strpos($clean, ',') === false)) { $start = $i + 1; continue; }
            if (preg_match('~^@(media|supports|container|layer|starting-style)\b~i', $clean)) {
                $pruned = zp_home_first_paint_css($inner, $known, $level + 1);
                if ($pruned === null) { return null; }
                $inner = $pruned;
            }
            $out .= $prelude . '{' . $inner . '}';
        } else {
            $selectors = zp_css_delivery_css_parts($prelude, ',');
            if ($selectors === null || in_array('', array_map('trim', $selectors), true)) { return null; }
            $all_absent = true;
            foreach ($selectors as $selector) {
                if (!zp_home_first_paint_absent($selector, $known)) { $all_absent = false; break; }
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

/** Single image decode on mobile; responsive sources keep the exact desktop portraits. */
add_filter('do_shortcode_tag', static function ($output, $tag) {
    if ($tag !== 'zp_home_hero' || !is_string($output) || !zp_home_first_paint_enabled()
        || strpos($output, 'zh__crewMobile') !== false) { return $output; }
    $url = ZP_SUITE_URL . 'assets/img/hero-team/crew-mobile-676.webp';
    if (!is_readable(ZP_SUITE_PATH . 'assets/img/hero-team/crew-mobile-676.webp')) { return $output; }
    return preg_replace_callback('~<div\b([^>]*\bclass="zh__crew"[^>]*)>(.*?)</div>~s', static function ($m) use ($url) {
        $blank = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';
        $desktop = preg_replace_callback('~<img\b[^>]*>~', static function ($im) use ($blank) {
            return '<picture class="zh__crewDesktop"><source media="(max-width:1100px)" srcset="' . $blank . '">' . $im[0] . '</picture>';
        }, $m[2]);
        $mobile = '<picture class="zh__crewMobilePicture"><source media="(min-width:1101px)" srcset="' . $blank . '"><img class="zh__crewMobile" src="' . esc_url($url) . '" width="676" height="366" alt="" loading="eager" decoding="async" fetchpriority="high" data-zp-first-paint="3233"></picture>';
        return '<div' . $m[1] . '>' . $mobile . $desktop . '</div>';
    }, $output, 1) ?? $output;
}, 40, 2);

add_action('wp_head', static function (): void {
    if (!zp_home_first_paint_enabled()) { return; }
    echo '<link rel="preload" as="image" type="image/webp" media="(max-width:1100px)" fetchpriority="high" href="' . esc_url(ZP_SUITE_URL . 'assets/img/hero-team/crew-mobile-676.webp') . '">';

}, -999990);
