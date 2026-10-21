<?php
/**
 * 3.2.40: presentation-only refinements. No post rewriting, route changes or remote requests.
 * The original service map and SEO copy remain the single sources of their content.
 */
if (!defined('ABSPATH')) { exit; }

function zp_refine_home_context_3240(): bool {
    return !is_admin() && (is_front_page() || (function_exists('zp_suite_308_is_home_like') && zp_suite_308_is_home_like()));
}

/** Embed only the existing map, city controls and explanatory status in the home SEO grid. */
function zp_refine_home_map_3240(): string {
    if (!function_exists('zp_coverage_markup_20261015') || !function_exists('zp_speed_nested_end')) { return ''; }
    $en = function_exists('zpl_language') && zpl_language() === 'en';
    $html = zp_coverage_markup_20261015('strony', $en);
    $open = '<div class="zpCoverage__visual">';
    $start = strpos($html, $open);
    if ($start === false) { return ''; }
    $end = zp_speed_nested_end($html, 'div', $start + strlen($open));
    if ($end < 0) { return ''; }
    return '<div id="zasieg" class="zpCoverage zpCoverage--home" data-zp-coverage="20261015" aria-label="' . esc_attr($en ? 'Our collaboration across Poland' : 'Współpraca z firmami z całej Polski') . '">' . substr($html, $start, $end - $start) . '</div>';
}

/** Small inline flags: no library, icon font or extra network request. */
function zp_refine_flag_3240(string $language): string {
    $start = '<svg class="zpLangDemo__flag" viewBox="0 0 30 20" aria-hidden="true" focusable="false">';
    if ($language === 'pl') { return $start . '<path fill="#fff" d="M0 0h30v10H0z"/><path fill="#dc143c" d="M0 10h30v10H0z"/></svg>'; }
    if ($language === 'it') { return $start . '<path fill="#009246" d="M0 0h10v20H0z"/><path fill="#fff" d="M10 0h10v20H10z"/><path fill="#ce2b37" d="M20 0h10v20H20z"/></svg>'; }
    return $start . '<path fill="#012169" d="M0 0h30v20H0z"/><path stroke="#fff" stroke-width="4" d="m0 0 30 20M30 0 0 20"/><path stroke="#c8102e" stroke-width="1.7" d="m0 0 30 20M30 0 0 20"/><path stroke="#fff" stroke-width="7" d="M15 0v20M0 10h30"/><path stroke="#c8102e" stroke-width="4" d="M15 0v20M0 10h30"/></svg>';
}

/** Explicit illustrative demo, not an iframe and not a switch for the entire agency website. */
function zp_refine_altiora_languages_3240(string $lang = 'pl'): string {
    $en = $lang === 'en';
    $variants = [
        'pl' => ['Polski', 'Marki dobrane do charakteru wnętrza.', 'Poznaj kolekcje, przejrzyj katalogi producentów i opowiedz o swoim projekcie.', 'Porozmawiajmy o wnętrzu'],
        'en' => ['English', 'Brands selected for the character of your interior.', 'Explore collections, browse manufacturer catalogues and tell us about your project.', 'Let’s discuss your interior'],
        'it' => ['Italiano', 'Marchi selezionati per il carattere dei tuoi interni.', 'Scopri le collezioni, consulta i cataloghi dei produttori e raccontaci il tuo progetto.', 'Parliamo dei tuoi interni'],
    ];
    $panel = static function(array $v, string $code): string {
        return '<div class="zpLangDemo__previewCopy" lang="' . esc_attr($code) . '"><p class="zpLangDemo__brand">Altiora Linea</p><h3>' . esc_html($v[1]) . '</h3><p>' . esc_html($v[2]) . '</p><span class="zpLangDemo__sampleCta">' . esc_html($v[3]) . ' <span aria-hidden="true">↗</span></span></div>';
    };
    $initial = $en ? 'en' : 'pl';
    $out = '<section class="zpCS18__section zpCS18__languages" aria-labelledby="zpAltioraLanguageTitle"><div class="zpCS18__inner zpCS18__languageGrid"><div>';
    $out .= zp_cs18_eyebrow($en ? 'PL / EN / IT · language versions' : 'PL / EN / IT · wersje językowe');
    $out .= '<h2 id="zpAltioraLanguageTitle">' . ($en ? 'One website.<br><strong>Three languages.</strong>' : 'Jedna strona.<br><strong>Trzy języki.</strong>') . '</h2>';
    $out .= '<p class="zpCS18__lead">' . esc_html($en ? 'Altiora Linea presents its offer in Polish, English and Italian. Visitors can switch the language without reloading the page — while staying in the same visual environment.' : 'Altiora Linea prezentuje ofertę po polsku, angielsku i włosku. Zmiana języka odbywa się bez przeładowania strony — odbiorca pozostaje w tym samym, spójnym wizualnie serwisie.') . '</p>';
    $out .= '<ul class="zpCS18__languageBenefits"><li>' . esc_html($en ? 'A consistent presentation of brands and collections' : 'Spójna prezentacja marek i kolekcji') . '</li><li>' . esc_html($en ? 'Content in the visitor’s chosen language' : 'Treści w języku wybranym przez odbiorcę') . '</li><li>' . esc_html($en ? 'No full-page reload during language selection' : 'Bez ponownego wczytywania całej strony') . '</li></ul>';
    $out .= '<a class="zpCS18__textLink" href="https://altioralinea.com/" target="_blank" rel="noopener">' . esc_html($en ? 'See it on the live website' : 'Sprawdź na stronie live') . zp_cs18_arrow() . '</a></div>';
    $out .= '<div class="zpLangDemo notranslate" translate="no" data-zp-language-demo data-zp-language-initial="' . $initial . '"><div class="zpLangDemo__toolbar"><span class="zpLangDemo__label">' . esc_html($en ? 'Language switch preview' : 'Podgląd zmiany języka') . '</span><span class="zpLangDemo__dot" aria-hidden="true"></span></div><div class="zpLangDemo__switch" role="group" aria-label="' . esc_attr($en ? 'Choose the preview language' : 'Wybierz język podglądu') . '">';
    foreach ($variants as $code => $variant) {
        $out .= '<button type="button" data-zp-demo-language="' . $code . '" aria-controls="zpAltioraLanguagePreview" aria-pressed="' . ($code === $initial ? 'true' : 'false') . '" disabled>' . zp_refine_flag_3240($code) . '<span>' . strtoupper($code) . '</span><span class="zpLangDemo__languageName">' . $variant[0] . '</span></button>';
    }
    $out .= '</div><div id="zpAltioraLanguagePreview" class="zpLangDemo__preview" aria-live="polite" aria-atomic="true">' . $panel($variants[$initial], $initial) . '</div>';
    foreach ($variants as $code => $variant) { $out .= '<template data-zp-demo-template="' . $code . '">' . $panel($variant, $code) . '</template>'; }
    $out .= '<p class="zpLangDemo__note">' . esc_html($en ? 'An illustrative interaction preview — the buttons change only this card, not the entire case study.' : 'Demonstracja działania — przyciski zmieniają treść tej karty, a nie język całego case study.') . '</p><noscript><p class="zpLangDemo__note">' . esc_html($en ? 'The interactive preview requires JavaScript. You can also visit the live website.' : 'Interaktywny podgląd wymaga JavaScript. Możesz też przejść do strony live.') . '</p></noscript></div></div></section>';
    return $out;
}

/** A consistent 48px icon field for custom functions without an official supplied logo. */
function zp_refine_tech_icon_3240(string $name): string {
    $paths = [
        'Custom booking' => '<rect x="4" y="5" width="16" height="16" rx="3"/><path d="M8 3v4m8-4v4M4 10h16m-12 5 3 3 5-5"/>',
        'Administrator listings' => '<rect x="4" y="3" width="16" height="18" rx="3"/><path d="M8 8h8M8 12h8m-8 4h4"/>',
        'Responsive layouts' => '<rect x="2" y="4" width="14" height="12" rx="2"/><path d="M6 20h6m-3-4v4"/><rect x="16" y="9" width="6" height="12" rx="1.5"/>',
        'SEO' => '<circle cx="10" cy="10" r="6"/><path d="m15 15 6 6M7 11l2-2 2 1 2-3"/>',
        'PL / EN / IT' => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c-5 5-5 13 0 18 5-5 5-13 0-18"/>',
        'Catalogues and contact' => '<path d="M12 5C8 2 4 3 2 4v15c4-2 7-1 10 1 3-2 6-3 10-1V4c-3-1-7-2-10 1Zm0 0v15"/>',
        'imoje / ING' => '<rect x="2" y="4" width="20" height="15" rx="3"/><path d="M2 9h20m-8 5 2 2 4-4M6 14h3"/>',
    ];
    $path = $paths[$name] ?? '<rect x="2" y="3" width="20" height="18" rx="3"/><path d="M2 8h20m-14 4-3 3 3 3m8-6 3 3-3 3m-3-8-2 10"/>';
    return '<svg class="zpCS18__customIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.65" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $path . '</svg>';
}

add_action('wp_enqueue_scripts', static function(): void {
    $home = zp_refine_home_context_3240();
    $case = !$home && class_exists('ZP_Case_Pages_20261018') ? ZP_Case_Pages_20261018::current_key() : '';
    if (!$home && $case === '') { return; }
    // Separate lower-page CSS. First-screen case CSS stays in the existing stylesheet.
    if ($home) {
        wp_enqueue_style('zp-service-coverage', ZP_SUITE_URL . 'assets/css/service-coverage-20261015.css', [], ZP_SUITE_VERSION);
        wp_enqueue_script('zp-service-coverage', ZP_SUITE_URL . 'assets/js/service-coverage-20261015.js', [], ZP_SUITE_VERSION, true);
        wp_script_add_data('zp-service-coverage', 'strategy', 'defer');
        wp_enqueue_style('zp-home-refine-3240', ZP_SUITE_URL . 'assets/css/home-refine-3240.css', [], ZP_SUITE_VERSION);
        wp_enqueue_script('zp-portfolio-contrast-3240', ZP_SUITE_URL . 'assets/js/portfolio-contrast-3240.js', [], ZP_SUITE_VERSION, true);
        wp_script_add_data('zp-portfolio-contrast-3240', 'strategy', 'defer');
    }
    if ($case === 'altiora-linea') {
        wp_enqueue_script('zp-case-language-demo-3240', ZP_SUITE_URL . 'assets/js/case-language-demo-3240.js', [], ZP_SUITE_VERSION, true);
        wp_script_add_data('zp-case-language-demo-3240', 'strategy', 'defer');
    }
}, 98);

// These additions sit below the hero. Avoid adding blocking CSS requests to first paint.
// A normal stylesheet stays available without JavaScript; other handles are untouched.
add_filter('style_loader_tag', static function(string $html, string $handle): string {
    if (!in_array($handle, ['zp-service-coverage', 'zp-home-refine-3240'], true)
        || !zp_refine_home_context_3240()
        || strpos($html, 'data-zp-lower-page-css') !== false
        || !preg_match('~<link\b[^>]*>~i', $html, $m)) { return $html; }
    $original = $m[0];
    if (preg_match('~\sonload\s*=~i', $original)) { return $html; }
    $tag = preg_replace('~\smedia\s*=\s*(["\']).*?\1~is', '', $original);
    if (!is_string($tag)) { return $html; }
    $tag = preg_replace('~<link\b~i', "<link data-zp-lower-page-css=\"3240\" media=\"print\" onload=\"this.media='all';this.onload=null\"", $tag, 1);
    if (!is_string($tag)) { return $html; }
    $fallback = preg_replace('~\sid\s*=\s*(["\']).*?\1~is', '', $original);
    return str_replace($original, $tag . '<noscript>' . $fallback . '</noscript>', $html);
}, 999, 2);

// Old HTML must not mask new templates/styles. Only the plugin-owned cache is purged.
add_action('admin_init', static function(): void {
    if (!current_user_can('manage_options') || get_option('zp_refine_3240_cache', '') === ZP_SUITE_VERSION) { return; }
    do_action('zp_html_cache_purge');
    update_option('zp_refine_3240_cache', ZP_SUITE_VERSION, false);
}, 99);
