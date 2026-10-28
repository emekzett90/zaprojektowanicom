<?php
/** Altiora Linea: a display-only, bilingual featured project. Existing CMS entries stay intact. */
if (!defined('ABSPATH')) { exit; }
function zp_portfolio_altiora_3236(bool $en = false): array {
    $common = [
        'brand'=>'Altiora Linea', 'year'=>'2026', 'mode'=>'web', 'isNew'=>true,
        'img'=>'https://zaprojektowani.com/wp-content/uploads/2026/10/altiora_linea_realizacja-scaled.webp',
        'live'=>'https://altioralinea.com/',
        'meta'=>['WordPress','PL / EN / IT',$en ? 'Brand catalogues' : 'Katalogi marek'],
        'tools'=>['WordPress','UX/UI','RWD','PL / EN / IT'],
    ];
    $pl = [
        'sub'=>'strona premium · 3 języki · katalogi producentów',
        'type'=>'Strona firmowa / design i wnętrza',
        'tag'=>'Marki wnętrzarskie premium / architekci i inwestorzy',
        'client'=>'Altiora Linea — projekt dla Pana Dariusza',
        'services'=>'Projekt UX/UI, wdrożenie WordPress, wersje PL/EN/IT, prezentacja marek i kolekcji, katalogi, formularze kontaktowe',
        'desc'=>'Design potrzebuje przestrzeni — również w internecie. Dla Pana Dariusza przygotowaliśmy stronę Altiora Linea, marki łączącej architektów, projektantów wnętrz i inwestorów z wyselekcjonowanymi producentami wyposażenia premium. Zamiast przypadkowej listy produktów powstała spokojna, uporządkowana prezentacja marek, kolekcji i możliwości współpracy. Duże fotografie, ciepłe odcienie i wyrazista typografia pozwalają skupić uwagę na detalach oraz charakterze wnętrz.',
        'scope'=>[
            'Indywidualny projekt wizualny nawiązujący do świata architektury i wnętrz premium',
            'Wdrożenie w WordPressie oraz dopasowanie do telefonu, tabletu i komputera',
            'Trzy wersje językowe: polska, angielska i włoska',
            'Prezentacja reprezentowanych marek, kolekcji i katalogów producentów',
            'Formularze kontaktowe prowadzące do rozmowy o konkretnym projekcie',
            'Osobne informacje dla architektów, projektantów i inwestorów',
            'Czytelne przedstawienie procesu: selekcja, specyfikacja, koordynacja i dostawa',
        ],
        'challenge'=>'Pokazać szeroki świat marek wnętrzarskich bez wrażenia zatłoczonego katalogu i jednocześnie wyjaśnić rolę Altiora Linea jako partnera w projekcie. Serwis miał łączyć elegancki wizerunek z praktycznym dostępem do informacji dla odbiorców z różnych rynków.',
        'solution'=>'Ułożyliśmy nawigację wokół marek, kolekcji i współpracy z profesjonalistami. W detalach zaplanowaliśmy dostęp do katalogów oraz wyraźne przejścia do kontaktu. Sekcja procesu wyjaśnia, co dzieje się od doboru produktów po koordynację dostawy. Całość wdrożyliśmy w WordPressie w językach polskim, angielskim i włoskim.',
        'effect'=>'Gotowa strona jest wizytówką marki i punktem startowym do rozmowy o wyposażeniu wnętrza. Odbiorca może poznać ofertę, przejrzeć katalogi, zrozumieć model współpracy i przejść do zapytania — w wybranym języku, również na telefonie.',
        'results'=>[
            ['Trzy języki','Jedna marka przedstawiona po polsku, angielsku i włosku.'],
            ['Marki i katalogi','Uporządkowane miejsce do poznawania kolekcji oraz materiałów producentów.'],
            ['Od inspiracji do rozmowy','Prezentacja oferty połączona z formularzami kontaktowymi.'],
        ],
    ];
    $eng = [
        'sub'=>'premium website · 3 languages · manufacturer catalogues',
        'type'=>'Business website / interiors and design',
        'tag'=>'Premium interior brands / architects and investors',
        'client'=>'Altiora Linea — a project for Dariusz',
        'services'=>'UX/UI design, WordPress development, PL/EN/IT, brands and collections, catalogues, contact forms',
        'desc'=>'Good design needs room to breathe, online as well as in an interior. For Dariusz we created the Altiora Linea website, introducing a brand that connects architects, interior designers and investors with a carefully selected portfolio of premium manufacturers. Generous imagery, warm colours and distinctive typography turn the collection of brands into a considered presentation, with clear routes from inspiration to an enquiry.',
        'scope'=>[
            'A bespoke visual direction inspired by premium interiors and architecture',
            'WordPress development with layouts for mobile, tablet and desktop',
            'Three language versions: Polish, English and Italian',
            'Presentation of brands, collections and manufacturer catalogues',
            'Contact forms for enquiries about a specific project',
            'Dedicated information for architects, designers and investors',
            'A clear process: curation, specification, coordination and delivery',
        ],
        'challenge'=>'Present a broad portfolio of interior brands without making the website feel like a crowded product directory. Explain Altiora Linea’s role as a project partner while making useful information accessible to visitors from different markets.',
        'solution'=>'We organised the website around brands, collections and professional cooperation, with access to catalogues and straightforward contact routes. The process section explains the journey from product selection to coordinated delivery. The website runs on WordPress in Polish, English and Italian.',
        'effect'=>'The website gives the brand a considered online presence and provides a starting point for an interior project: explore the offer, browse catalogues, understand the working process and send an enquiry in the preferred language, including on a phone.',
        'results'=>[
            ['Three languages','The same brand presented in Polish, English and Italian.'],
            ['Brands and catalogues','A structured place to discover collections and manufacturer materials.'],
            ['From inspiration to enquiry','A visual presentation connected to useful contact forms.'],
        ],
    ];
    if (class_exists('ZP_Altiora_Article_3236') && ($case = ZP_Altiora_Article_3236::public_url())) { $common['case_url'] = $case; $common['caseStudyUrl'] = $case; }
    return array_merge($common, $en ? $eng : $pl);
}
add_filter('zp_portfolio_projects_20261015', static function ($items, $mode) {
    if (!in_array($mode, ['web', 'realizacje'], true) || !is_array($items)) { return $items; }
    if ($mode === 'realizacje') {
        $cms = get_option('zp_suite_realizacje_cms', []);
        foreach (is_array($cms) ? (array)($cms['projects'] ?? []) : [] as $saved) {
            if (is_array($saved) && strtolower(trim((string)($saved['brand'] ?? ''))) === 'altiora linea'
                && isset($saved['visible']) && (string)$saved['visible'] === '0') { return $items; }
        }
    }
    $en = function_exists('zpl_language') && zpl_language() === 'en';
    $project = zp_portfolio_altiora_3236($en);
    $project['cat'] = 'web';
    $project['featured'] = true;
    $project['project_slug'] = 'altiora-linea';
    $found = false; $other = [];
    foreach ($items as $item) {
        $brand = is_array($item) ? strtolower(trim((string) ($item['brand'] ?? ''))) : '';
        if ($brand !== 'altiora linea') { $other[] = $item; continue; }
        if (!$found) {
            // Published CMS copy wins. Add only missing defaults; never overwrite editorial text.
            foreach ($item as $field => $value) {
                if ($value !== '' && $value !== null && $value !== []) { $project[$field] = $value; }
            }
            $found = true;
        }
    }
    $project['isNew'] = true;
    // A public case-study route is preferred; retain the existing valid article link otherwise.
    if (function_exists('zp_cs18_case_public') && zp_cs18_case_public('altiora-linea')) {
        $data = zp_cs18_data();
        $project['case_url'] = zp_cs18_url($data['altiora-linea'][$en ? 'en_path' : 'path'], $en ? 'en' : 'pl');
        $project['caseStudyUrl'] = $project['case_url'];
    }
    array_unshift($other, $project);
    return $other;
}, 100, 2);

add_action('wp_enqueue_scripts', static function (): void {
    if (!is_front_page() && !(function_exists('zp_suite_308_is_home_like') && zp_suite_308_is_home_like())) { return; }
    wp_enqueue_style('zp-home-service-mobile-3236', ZP_SUITE_URL . 'assets/css/home-service-mobile-3236.css', [], ZP_SUITE_VERSION);
    wp_enqueue_script('zp-home-service-mobile-3236', ZP_SUITE_URL . 'assets/js/home-service-mobile-3236.js', [], ZP_SUITE_VERSION, true);
}, 80);
