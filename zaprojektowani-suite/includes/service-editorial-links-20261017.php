<?php
/**
 * Short planning notes that add missing contextual links on four service routes.
 * Existing links inside the service content count; global navigation/footer links do not.
 * The identity service already links to both identity and rebranding guides, so it is untouched.
 */
if (!defined('ABSPATH')) { exit; }

function zp_el_plan_20261017(): array {
    $identity = [
        'path' => '/logo-branding/identyfikacja-wizualna-firmy/',
        'pl' => ['Sam znak czy cała identyfikacja?', 'Logo jest jednym z elementów marki. Jeśli potrzebujesz także kolorów, typografii i wzorów materiałów, sprawdź, co obejmuje ', 'identyfikacja wizualna firmy', '. Zakres projektu dopasujemy do miejsc, w których Twoja marka jest używana na co dzień.'],
        'en' => ['A logo or a complete identity?', 'A logo is one part of a brand. If you also need colours, typography and material templates, explore what a ', 'brand identity includes', '. We can plan the scope around the places where your brand is used every day.'],
    ];
    $rebranding = [
        'path' => '/logo-branding/rebranding-firmy/',
        'pl' => ['Co zrobić z dotychczasową marką?', 'Gdy firma ma już rozpoznawalny znak i materiały, warto najpierw ustalić, co zachować. Poradnik ', 'rebranding firmy', ' wyjaśnia, jak zaplanować zmianę wizerunku. Podczas rozmowy ocenimy, czy potrzebna jest korekta logo, czy szersza zmiana identyfikacji.'],
        'en' => ['What should happen to your existing brand?', 'If your business already has a recognisable logo and materials, first consider what to keep. Our ', 'company rebranding guide', ' explains how to plan a change of identity. We can discuss whether the project needs a logo adjustment or a wider identity update.'],
    ];
    return [
        '/projektowanie-logo/' => ['after' => 'pliki', 'items' => [$identity, $rebranding]],
        '/logo-branding-katowice/' => ['after' => 'pliki', 'items' => [$identity, $rebranding]],
        '/tworzenie-stron-internetowych/' => [
            'after' => 'zakres',
            'items' => [
                [
                    'path' => '/strony-internetowe/strona-internetowa-dla-firmy-co-powinna-zawierac/',
                    'pl' => ['Co uwzględnić w zakresie strony?', 'Przed wyceną warto rozpisać usługi, przykłady realizacji, informacje o firmie i sposób kontaktu. Zobacz, ', 'co powinna zawierać strona firmowa', ', aby przygotować listę podstron oraz materiałów do projektu. Na tej podstawie łatwiej wspólnie ustalić właściwy zakres.'],
                    'en' => ['What belongs in the website scope?', 'Before requesting a quote, list your services, project examples, company information and contact options. Explore ', 'what a company website should include', ' to prepare the pages and materials for your project. This gives us a clearer basis for agreeing the scope together.'],
                ],
                [
                    'path' => '/strony-internetowe/jak-stworzyc-strone-internetowa/',
                    'pl' => ['Jak przygotować się do realizacji?', 'Porównujesz samodzielne wykonanie z projektem agencji? Poradnik ', 'jak stworzyć stronę internetową', ' prowadzi przez wybór rozwiązania, treści, projekt i uruchomienie. Wspólnie ustalimy, które elementy przygotujesz samodzielnie, a którymi zajmie się nasz zespół.'],
                    'en' => ['How should you prepare for the project?', 'Comparing a website you build yourself with an agency project? Our guide to ', 'creating a website', ' covers the platform, content, design and launch. We can agree which elements you will prepare and which our team will handle.'],
                ],
            ],
        ],
        '/strony-internetowe-katowice/' => [
            'after' => 'zakres',
            'items' => [[
                'path' => '/sklepy-internetowe-katowice/', 'local' => true,
                'pl' => ['Klient ma kupować bezpośrednio na stronie?', 'Jeśli klient ma wybierać produkty, dodawać je do koszyka i płacić online, zakres obejmuje sklep internetowy. Zobacz nasze ', 'projektowanie sklepów internetowych w Katowicach', '. Katalog produktów, płatności i dostawy dopasujemy do sposobu sprzedaży Twojej firmy.'],
                'en' => ['Should customers buy directly on the website?', 'If customers need to select products, add them to a basket and pay online, the project includes an online store. Explore our ', 'online store design service in Katowice', '. We can plan the product catalogue, payments and delivery around how your business sells.'],
            ]],
        ],
    ];
}

function zp_el_source_20261017(): string {
    if (!function_exists('zp_seo_service_faq_context') || !zp_seo_service_faq_context()
        || !function_exists('zp_coverage_source_20261015')) { return ''; }
    return zp_coverage_source_20261015();
}

function zp_el_url_20261017(string $path, bool $english): string {
    $url = home_url($path);
    return function_exists('zpl_url') ? zpl_url($url, $english ? 'en' : 'pl') : $url;
}

/** Identify links within the service itself, excluding raw script/style/template contents. */
function zp_el_context_links_20261017(string $html, int $from, int $to): array {
    $scan = zp_speed_scan($html);
    if (!$scan) { return []; }
    $home_host = preg_replace('~^www\.~', '', strtolower((string) parse_url(home_url('/'), PHP_URL_HOST)));
    $paths = [];
    if (!preg_match_all('~<a(?=[\s>])~i', $html, $matches, PREG_OFFSET_CAPTURE, $from)) { return []; }
    foreach ($matches[0] as $match) {
        $at = $match[1];
        if ($at >= $to) { break; }
        if (zp_speed_in_skip($scan['skip'], $at)) { continue; }
        $end = zp_speed_tag_end($html, $at);
        if ($end < 0 || $end >= $to) { continue; }
        $attrs = zp_services_faq_attributes(substr($html, $at, $end + 1 - $at));
        $href = (string) ($attrs['href'] ?? '');
        if ($href === '' || $href[0] === '#') { continue; }
        $parts = parse_url($href);
        if (!is_array($parts) || (isset($parts['scheme']) && !in_array(strtolower($parts['scheme']), ['http', 'https'], true))) { continue; }
        if (isset($parts['host']) && preg_replace('~^www\.~', '', strtolower($parts['host'])) !== $home_host) { continue; }
        $path = $parts['path'] ?? '';
        if ($path !== '' && $path[0] === '/') { $paths[rtrim($path, '/') . '/'] = true; }
    }
    return $paths;
}

function zp_el_document_20261017(string $html, string $source, bool $english): string {
    $plan = zp_el_plan_20261017()[$source] ?? null;
    if (!$plan || !function_exists('zp_coverage_section_20261015') || !function_exists('zp_speed_scan')
        || !function_exists('zp_services_faq_attributes') || zp_coverage_section_20261015($html, 'zpServiceReading')) { return $html; }
    $start = zp_coverage_section_20261015($html, 'start');
    $faq = zp_coverage_section_20261015($html, 'faq');
    if (!$start || !$faq || $start[0] >= $faq[1]) { return $html; }
    $links = zp_el_context_links_20261017($html, $start[0], $faq[1]);
    $entries = '';
    $count = 0;
    foreach ($plan['items'] as $item) {
        $url = zp_el_url_20261017($item['path'], $english);
        $path = rtrim((string) parse_url($url, PHP_URL_PATH), '/') . '/';
        if (isset($links[$path]) || isset($links[$item['path']])) { continue; }
        [$heading, $prefix, $anchor, $suffix] = $item[$english ? 'en' : 'pl'];
        $count++;
        $entries .= '<div class="zpServiceReading__entry"><h3>' . esc_html($heading) . '</h3><p>' . esc_html($prefix) . '<a class="zpSeoLink"' . (!empty($item['local']) ? ' data-zp-local="1"' : '') . ' href="' . esc_url($url) . '">' . esc_html($anchor) . '</a>' . esc_html($suffix) . '</p></div>';
    }
    if ($entries === '') { return $html; }
    $markup = '<section class="zpServiceReading" id="zpServiceReading" data-zp-service-reading="20261017" aria-labelledby="zpServiceReadingTitle"><div class="zpServiceReading__inner"><h2 id="zpServiceReadingTitle">' . ($english ? 'Plan the right scope for your business' : 'Dobierz zakres do swojej firmy') . '</h2><div class="zpServiceReading__grid" data-zp-reading-count="' . $count . '">' . $entries . '</div></div></section>';
    $after = zp_coverage_section_20261015($html, $plan['after']);
    $at = $after ? $after[1] : $faq[0];
    return substr($html, 0, $at) . $markup . substr($html, $at);
}

add_filter('zp_service_widgets_document', static function (string $html): string {
    $source = zp_el_source_20261017();
    if ($source === '') { return $html; }
    return zp_el_document_20261017($html, $source, function_exists('zp_seo_plan_is_en') && zp_seo_plan_is_en());
}, 14);

add_action('wp_enqueue_scripts', static function (): void {
    if (!isset(zp_el_plan_20261017()[zp_el_source_20261017()])) { return; }
    wp_enqueue_style('zp-service-editorial-links-20261017', ZP_SUITE_URL . 'assets/css/service-editorial-links-20261017.css', [], ZP_SUITE_VERSION);
}, 10006);
