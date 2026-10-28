<?php
/**
 * 3.2.64: release-built deletion plans for absent static Suite sections.
 * No stylesheet is moved, deferred, requested or rewritten. Surviving CSS bytes,
 * declarations, selector lists and cascade positions stay exact. The old parser
 * runs first; edited/unknown CSS, unknown scripts or any present/dynamic root
 * fail open to that old result. Plans only remove proven whole qualified rules;
 * all font faces, keyframes, unknown at-rules and functional selectors survive.
 */
if (!defined('ABSPATH')) { exit; }

function zp_css_prune_manifest_3264(): array {
    static $plans = null;
    if ($plans === null) {
        $path = __DIR__ . '/css-prune-plans-3264.json';
        $value = is_readable($path) ? json_decode((string) file_get_contents($path), true) : [];
        $plans = is_array($value) ? $value : [];
    }
    return $plans;
}

/** Hash-guarded subsequence only; no request-time CSS parser or minifier. */
function zp_css_prune_apply_3264(string $css, array $absent): string {
    $entries = zp_css_prune_manifest_3264()[hash('sha256', $css)] ?? null;
    if (!is_array($entries)) { return $css; }
    $known = array_fill_keys($absent, true);
    foreach ($entries as $entry) {
        if (!is_array($entry) || !is_array($entry['absent'] ?? null) || !is_array($entry['delete'] ?? null)) { continue; }
        foreach ($entry['absent'] as $root) { if (!is_string($root) || !isset($known[$root])) { continue 2; } }
        $out = ''; $last = 0; $length = strlen($css);
        foreach ($entry['delete'] as $range) {
            if (!is_array($range) || count($range) !== 2 || !is_int($range[0]) || !is_int($range[1])
                || $range[0] < $last || $range[1] <= $range[0] || $range[1] > $length) { continue 2; }
            $out .= substr($css, $last, $range[0] - $last); $last = $range[1];
        }
        $out .= substr($css, $last);
        if (strlen($out) === ($entry['result_bytes'] ?? -1) && is_string($entry['result_sha256'] ?? null)
            && hash_equals($entry['result_sha256'], hash('sha256', $out))) { return $out; }
    }
    return $css;
}

function zp_css_prune_script_guard_3264(string $html, array $scan): ?string {
    $text = ''; $seen = []; $bytes = 0;
    foreach ($scan['skip'] as $range) {
        if (strncasecmp(substr($html, $range[0], 7), '<script', 7) !== 0) { continue; }
        $gt = zp_speed_tag_end($html, $range[0]);
        if ($gt < 0) { return null; }
        $text .= substr($html, $gt + 1, $range[1] - $gt - 1) . "\n";
        $attrs = zp_css_delivery_attributes(substr($html, $range[0], $gt + 1 - $range[0]));
        $src = $attrs['src'] ?? ($attrs['data-zp-src'] ?? ($attrs['data-src'] ?? ''));
        if (!is_string($src) || $src === '') { continue; }
        $path = parse_url($src, PHP_URL_PATH);
        $base = defined('ZP_SUITE_URL') ? parse_url(ZP_SUITE_URL, PHP_URL_PATH) : null;
        if (!is_string($path) || !is_string($base) || strpos($path, $base) !== 0) { continue; }
        $actual = parse_url($src); $expected = parse_url(ZP_SUITE_URL);
        if ((isset($actual['scheme']) && strcasecmp($actual['scheme'], (string) ($expected['scheme'] ?? '')) !== 0)
            || (isset($actual['host']) && strcasecmp($actual['host'], (string) ($expected['host'] ?? '')) !== 0)
            || (isset($actual['port']) && $actual['port'] !== ($expected['port'] ?? null))) { return null; }
        $relative = substr($path, strlen($base));
        if ($relative === '' || strpos($relative, '..') !== false || strpos($relative, '\\') !== false
            || strpos($relative, '%') !== false || substr($relative, -3) !== '.js') { return null; }
        $file = ZP_SUITE_PATH . $relative;
        if (isset($seen[$file])) { continue; }
        $seen[$file] = true;
        if (is_link($file) || !is_file($file) || !is_readable($file)
            || ($size = @filesize($file)) === false || $size > 1048576 || ($bytes += $size) > 4194304) { return null; }
        $contents = @file_get_contents($file);
        if (!is_string($contents)) { return null; }
        $text .= $contents . "\n";
    }
    return $text;
}

function zp_css_prune_absent_3264(string $markup, ?string $script_guard = null, ?array $root_state = null): array {
    $ids = $root_state['ids'] ?? []; $classes = $root_state['classes'] ?? []; $brand_parent = isset($classes['zpBrandHeroSafe']);
    if ($root_state === null && preg_match_all('~\s(id|class)\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|([^\s>]+))~i', $markup, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $match) {
            $value = html_entity_decode(($match[2] ?? '') . ($match[3] ?? '') . ($match[4] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            if (strtolower($match[1]) === 'id') { $ids[$value] = true; }
            else {
                foreach (preg_split('~\s+~', trim($value)) ?: [] as $class) { $classes[$class] = true; }
                if (isset($classes['zpBrandHeroSafe'])) { $brand_parent = true; }
            }
        }
    }
    $roots = ['zpAboutPage', 'zpbsUltimate', 'zpBrandHeroKatSafe', 'zpStronyKatowice', 'zpShopKatowice', 'zpShopHeroKat', 'zp-sklepy-internetowe-katowice'];
    if (!isset($ids['zpBrandHeroKatSafe']) && !$brand_parent) { $roots[] = 'zpBrandHeroMobileKat'; }
    // 3.2.64: static section roots verified in shipped server templates. These
    // extra roots are opt-in only when the document's script guard is available.
    // Any mention in inline or Suite-local external JS retains the rules, even
    // a read-only query: future/dynamic insertion cannot lose its stylesheet.
    if ($script_guard !== null) {
        foreach ([
            'zpWebHeroKat', 'zpContactSystemLight', 'zpContactFormLight',
            'zpShowcaseWhite', 'zpShowcaseServices', 'zpProcessFlow',
            'zpContactHeroKat', 'zpLogoBrandingKatowice', 'zpSklepyKatowice',
            'zpShopWhyLight', 'zpShopProcessFlow', 'zpHomeFaqKatNavy',
            'zpLogoPortfolio', 'zpRealizacjePage', 'zpLaptopShowcase',
            'zpHomeSeo', 'zpHomeAuditCta', 'zpBrandTrust114', 'zpTrustCert',
            'zpHomeFeaturedPackages', 'zpRevEd', 'zpAboutExperience',
        ] as $root) {
            if (strpos($script_guard, $root) === false) { $roots[] = $root; }
        }
    }

    $absent = array_values(array_filter($roots, static fn($root) => !isset($ids[$root])));
    if ($script_guard !== null) {
        // Same registered server components, including their BEM descendants.
        // A family is retained if ANY member occurs in markup or any script.
        // No generic shared class, modal, navigation, typography or state class.
        foreach ([
            'zpTrustPinned', 'zpRobotHero', 'zpNewHero', 'zpHomeSeoIntro',
            'zpServicesPath', 'zpShopCockpit', 'zpBrandHeroSafe',
            'zpRevEd', 'zpShowcaseWhite', 'zpAboutExperience',
            'zpShopWhyLight', 'zpLogoPortfolio', 'zpHomeAuditCta',
            'zpBrandTrust', 'zpRealizacjePage', 'zpHomeSeo',
        ] as $family) {
            if (strpos($script_guard, $family) !== false) { continue; }
            $present = false;
            foreach ($classes as $class => $_) {
                if ($class === $family || str_starts_with($class, $family . '__') || str_starts_with($class, $family . '--')) { $present = true; break; }
            }
            if (!$present) { $absent[] = '.' . $family; }
        }
    }
    // Hero variants are static server-rendered classes. The shipped controller
    // only READS them using classList.contains(); it does not switch variants.
    // Any other mention (including HTML, assignment or future insertion code)
    // keeps that variant's rules. :not/:is/:has arguments remain opaque below.
    if ($script_guard !== null) {
        $reads = preg_replace('~\.classList\.contains\(\s*(["\'])(?:zh--logoBranding|zh--knowledge|zh--websites|zh--shops|zh--contact)\1\s*\)~', '', $script_guard);
        foreach (['zh--logoBranding', 'zh--knowledge', 'zh--websites', 'zh--shops', 'zh--contact'] as $variant) {
            if (!isset($classes[$variant]) && is_string($reads) && strpos($reads, $variant) === false) { $absent[] = '.' . $variant; }
        }
    }
    return $absent;
}

