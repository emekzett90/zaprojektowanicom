<?php
/**
 * Realizacje 21.10.2026 (Mat, points 4 and 5).
 *
 * 4. On desktop and tablet every portfolio card gets the 4:3 thumbnail the logo cards already use, so a website
 *    card (Altiora Linea) is no longer shorter than the logo cards next to it. Phones keep their ratios.
 * 5. Small technology marks under the service chips of each card and a "Technologie projektu" block in the
 *    project sheet (mark, name and its role in the project).
 *
 * Marks are the brand assets the plugin already ships (no drawn substitutes). Adobe tools have no supplied
 * mark and are shown as text. Projects without a confirmed stack get no row. The technology keys are attached
 * to each project through the existing zp_portfolio_projects_20261015 filter (mode "realizacje"), so the server
 * cards, the JS grid and the sheet read the same data; the registry and labels are printed once as
 * window.zpRealizacjeTech. The CSS (assets/css/realizacje-tech-3250.css) is inlined after the page stylesheet,
 * so the page makes no extra request.
 */
if (!defined('ABSPATH')) { exit; }

/** Most marks a card shows; the rest is a "+N" note (the sheet lists all of them). */
const ZP_RT50_CARD_LIMIT = 6;

/**
 * key => [name, logo (plugin-relative, '' for text-only), kind (icon|mark|text), intrinsic width, height,
 *         role PL, role EN, lucide icon for text-only tiles, short card label]
 * The order of this list is the card priority (most recognisable first).
 */
function zp_rt50_registry(): array {
    return [
        'wordpress'   => ['WordPress', 'assets/strony-internetowe/tech-wordpress.svg', 'icon', 24, 24, 'CMS i zarządzanie treścią', 'CMS and content management'],
        'woocommerce' => ['WooCommerce', 'assets/img/case-studies-3242/tech-woocommerce.svg', 'mark', 2400, 484, 'Sklep, koszyk i zamówienia', 'Store, cart and orders'],
        'elementor'   => ['Elementor', 'assets/strony-internetowe/tool-elementor.png', 'mark', 300, 80, 'Budowa układów i sekcji strony', 'Page layouts and sections'],
        'figma'       => ['Figma', 'assets/strony-internetowe/tech-figma.svg', 'icon', 24, 24, 'Projekt UX/UI i makiety', 'UX/UI design and mockups'],
        'ga4'         => ['Google Analytics 4', 'assets/strony-internetowe/tech-google-analytics.svg', 'icon', 24, 24, 'Analityka ruchu i konwersji', 'Traffic and conversion analytics'],
        'gsc'         => ['Google Search Console', 'assets/strony-internetowe/tech-search-console.svg', 'icon', 24, 24, 'Indeksowanie i widoczność w Google', 'Indexing and visibility in Google'],
        'gtm'         => ['Google Tag Manager', 'assets/img/case-studies-3248/tech-google-tag-manager.svg', 'icon', 192, 192, 'Tagi i zdarzenia marketingowe', 'Marketing tags and events'],
        'googleads'   => ['Google Ads', 'assets/campaigns/google-ads-logo.png', 'mark', 300, 80, 'Kampanie reklamowe w Google', 'Google advertising campaigns'],
        'meta'        => ['Meta Ads', 'assets/strony-internetowe/tech-meta.svg', 'icon', 24, 24, 'Piksel, remarketing i kampanie Meta', 'Pixel, remarketing and Meta campaigns'],
        'php'         => ['PHP', 'assets/strony-internetowe/tech-php.svg', 'icon', 24, 24, 'Funkcje i integracje na zamówienie', 'Custom functions and integrations'],
        'imoje'       => ['imoje / ING', 'assets/img/case-studies-3242/imoje.png', 'crop', 256, 256, 'Płatności online', 'Online payments'],
        'gsap'        => ['GSAP', 'assets/strony-internetowe/tech-gsap.svg', 'icon', 24, 24, 'Animacje interfejsu', 'Interface animations'],
        'aipkit'      => ['AIPKIT', 'assets/strony-internetowe/tech-aipkit.svg', 'icon', 48, 48, 'Asystent AI na stronie', 'On-site AI assistant'],
        'cookiebot'   => ['Cookiebot', 'assets/img/case-studies-3249/cookiebot.svg', 'mark', 570, 169, 'Zgody cookies zgodne z RODO', 'GDPR cookie consent'],
        'recaptcha'   => ['reCAPTCHA', 'assets/img/case-studies-3249/recaptcha.svg', 'icon', 150, 150, 'Ochrona formularzy przed spamem', 'Spam protection for forms'],
        'illustrator' => ['Adobe Illustrator', '', 'text', 0, 0, 'Projekt znaku i grafika wektorowa', 'Logo design and vector graphics', 'pen-tool', 'Illustrator'],
        'photoshop'   => ['Adobe Photoshop', '', 'text', 0, 0, 'Grafika rastrowa i mockupy', 'Raster graphics and mockups', 'image', 'Photoshop'],
        'indesign'    => ['Adobe InDesign', '', 'text', 0, 0, 'Materiały do druku', 'Print materials', 'book-open', 'InDesign'],
    ];
}

/**
 * Project slug (as the realizacje template derives it) => technology keys in the order the sheet lists them.
 * Sources: content/case-studies-20261018.json (Altiora Linea, Siemianowski, Apartament Piękna, Świat Grilli),
 * the portfolio of assets/js/blocks/strony-internetowe-katowice.js, the portfolio "tools" fields (OutTech,
 * Natalia Arciszewska, Zgórecki Nieruchomości) and the Inusti card ("WordPress").
 * Not listed (stack not confirmed): Druga Szansa, Piekarnia Marysia and the logo-only projects below Siemianowski logo.
 */
function zp_rt50_project_map(): array {
    return [
        'altiora-linea'                                            => ['wordpress', 'elementor', 'php', 'ga4', 'gsc', 'cookiebot', 'recaptcha'],
        'siemianowski-kancelaria-premium-prawo-i-restrukturyzacja' => ['wordpress', 'elementor', 'figma', 'php', 'ga4', 'gsc', 'cookiebot', 'recaptcha'],
        'apartament-piekna-strona-rezerwacje-seo'                  => ['wordpress', 'elementor', 'php', 'ga4', 'gsc', 'gtm', 'googleads', 'meta', 'cookiebot', 'recaptcha'],
        'swiat-grilli-sklep-internetowy-branding'                  => ['wordpress', 'woocommerce', 'imoje', 'elementor', 'php', 'ga4', 'gsc', 'gtm', 'googleads', 'meta', 'cookiebot', 'recaptcha'],
        'polerstone-branding-strona-firmowa'                       => ['wordpress', 'figma'],
        'proscarves-b2b-custom-sklep-sportowy'                     => ['wordpress', 'woocommerce'],
        'gravia-strona-firmowa-kariera'                            => ['wordpress'],
        'shothome-strona-seo-darmowa-wycena'                       => ['wordpress', 'php'],
        'krawiec-z-dojazdem-system-rebranding'                     => ['wordpress', 'ga4'],
        'papeterio-sklep-woocommerce'                              => ['wordpress', 'woocommerce'],
        'bransoletka24-sklep-woocommerce'                          => ['wordpress', 'woocommerce', 'gsap'],
        'rutpoz-system-ai'                                         => ['wordpress', 'aipkit'],
        'outtech-strona-firmowa-technika-zabezpieczen'             => ['wordpress'],
        'inusti-strona-b2b'                                        => ['wordpress'],
        'natalia-arciszewska-logo-mini-branding'                   => ['figma', 'illustrator', 'photoshop', 'indesign'],
        'zgorecki-nieruchomosci-logo-mini-branding'                => ['figma', 'illustrator', 'photoshop'],
    ];
}

function zp_rt50_is_en(): bool {
    return function_exists('zpl_language') && zpl_language() === 'en';
}

/** The slug the realizacje template and the page script derive for a project. */
function zp_rt50_project_slug(array $project): string {
    $stable = (string) ($project['project_slug'] ?? '');
    if (preg_match('~^[a-z0-9]+(?:-[a-z0-9]+)*$~D', $stable)) { return $stable; }
    return sanitize_title(($project['brand'] ?? '') . '-' . ($project['type'] ?? 'projekt'));
}

/**
 * Registry for the page: key => {n name, s short label, l logo URL, k kind, w/h intrinsic size, r role, i icon, p card priority}.
 * Entries whose file is missing are left out.
 */
function zp_rt50_registry_public(): array {
    static $cache = [];
    $lang = zp_rt50_is_en() ? 'en' : 'pl';
    if (isset($cache[$lang])) { return $cache[$lang]; }
    $out = []; $priority = 0;
    foreach (zp_rt50_registry() as $key => $row) {
        $priority++;
        if ($row[1] !== '' && !file_exists(ZP_SUITE_PATH . $row[1])) { continue; }
        $out[$key] = [
            'n' => $row[0],
            's' => $row[8] ?? $row[0],
            'l' => $row[1] !== '' ? ZP_SUITE_URL . $row[1] : '',
            'k' => $row[2],
            'w' => (int) $row[3],
            'h' => (int) $row[4],
            'r' => $lang === 'en' ? $row[6] : $row[5],
            'i' => $row[7] ?? '',
            'p' => $priority,
        ];
    }
    return $cache[$lang] = $out;
}

function zp_rt50_labels(): array {
    $en = zp_rt50_is_en();
    return [
        'row'      => $en ? 'Project technologies' : 'Technologie projektu',
        'more'     => $en ? 'More technologies' : 'Więcej technologii',
        'title'    => $en ? 'Project technologies' : 'Technologie projektu',
        'titleSub' => $en ? 'tools and systems' : 'narzędzia i systemy',
        'services' => $en ? 'Services in the project' : 'Usługi w projekcie',
        'caseLink' => $en ? 'Full project description' : 'Pełny opis realizacji',
        'limit'    => ZP_RT50_CARD_LIMIT,
    ];
}

/** Technology keys of a project that the registry can render, in sheet order. */
function zp_rt50_project_tech(array $project): array {
    $map = zp_rt50_project_map();
    $slug = zp_rt50_project_slug($project);
    if (empty($map[$slug])) { return []; }
    $registry = zp_rt50_registry_public();
    return array_values(array_filter($map[$slug], static function ($key) use ($registry) { return isset($registry[$key]); }));
}

add_filter('zp_portfolio_projects_20261015', static function ($items, $mode) {
    if ($mode !== 'realizacje' || !is_array($items)) { return $items; }
    foreach ($items as &$item) {
        if (is_array($item)) { $item['tech'] = zp_rt50_project_tech($item); }
    }
    unset($item);
    return $items;
}, 120, 2);

/** One mark of a card row. The JS twin is techMark() in assets/js/blocks/realizacje-page.js. */
function zp_rt50_mark_html(string $key): string {
    $registry = zp_rt50_registry_public();
    if (empty($registry[$key])) { return ''; }
    $t = $registry[$key];
    if ($t['l'] === '') {
        return '<span class="techMark techMark--text" title="' . esc_attr($t['n']) . '">' . esc_html($t['s']) . '</span>';
    }
    $h = $t['k'] === 'mark' ? 15 : 18;
    $w = $t['k'] === 'mark' && $t['h'] > 0 ? (int) round($h * $t['w'] / $t['h']) : ($t['k'] === 'crop' ? 36 : 18);
    return '<span class="techMark techMark--' . esc_attr($t['k']) . ' techMark--' . esc_attr($key) . '" role="img" title="' . esc_attr($t['n']) . '" aria-label="' . esc_attr($t['n']) . '">'
        . '<img src="' . esc_url($t['l']) . '" alt="" width="' . $w . '" height="' . $h . '" loading="lazy" decoding="async"></span>';
}

/** Card row: the most recognisable marks (up to ZP_RT50_CARD_LIMIT) and "+N" for the rest. */
function zp_rt50_row_html(array $project): string {
    $keys = is_array($project['tech'] ?? null) ? $project['tech'] : zp_rt50_project_tech($project);
    $registry = zp_rt50_registry_public();
    $keys = array_values(array_filter(array_map('strval', $keys), static function ($key) use ($registry) { return isset($registry[$key]); }));
    if (!$keys) { return ''; }
    usort($keys, static function ($a, $b) use ($registry) { return $registry[$a]['p'] <=> $registry[$b]['p']; });
    $shown = array_slice($keys, 0, ZP_RT50_CARD_LIMIT);
    $rest = array_slice($keys, ZP_RT50_CARD_LIMIT);
    $labels = zp_rt50_labels();
    $html = '';
    foreach ($shown as $key) { $html .= zp_rt50_mark_html($key); }
    if ($rest) {
        $names = implode(', ', array_map(static function ($key) use ($registry) { return $registry[$key]['n']; }, $rest));
        $html .= '<span class="techMore" role="img" title="' . esc_attr($names) . '" aria-label="' . esc_attr($labels['more'] . ': ' . $names) . '">+' . count($rest) . '</span>';
    }
    return '<div class="techRow" role="group" aria-label="' . esc_attr($labels['row']) . '">' . $html . '</div>';
}

/** Registry and labels for the page script, printed once next to window.zpRealizacjeProjects. */
function zp_rt50_data_script(): string {
    zp_rt50_attach_css();
    $data = ['labels' => zp_rt50_labels(), 'items' => zp_rt50_registry_public()];
    return '<script id="zp-realizacje-tech-data">' . "\n" . 'window.zpRealizacjeTech = ' . wp_json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . ";\n</script>\n";
}

/** Inline the stylesheet after the realizacje page stylesheet (once; also works when that is enqueued late). */
function zp_rt50_attach_css(): void {
    static $done = false;
    if ($done || !wp_style_is('zp-suite-realizacje-page', 'enqueued')) { return; }
    $file = ZP_SUITE_PATH . 'assets/css/realizacje-tech-3250.css';
    if (!is_readable($file)) { return; }
    $css = (string) file_get_contents($file);
    $css = preg_replace('~/\*.*?\*/~s', '', $css);
    $css = trim(preg_replace('~\s*\n\s*~', '', $css));
    if ($css === '') { return; }
    $done = true;
    wp_add_inline_style('zp-suite-realizacje-page', $css);
}
add_action('wp_enqueue_scripts', 'zp_rt50_attach_css', 20);
