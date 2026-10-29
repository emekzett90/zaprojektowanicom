<?php
/** Project-specific experience modules. Existing assets, native details, no tracking or synthetic metrics. */
if (!defined('ABSPATH')) { exit; }

function zp_cs67_icon(string $name): string {
    $paths = [
        'catalogue' => '<rect x="3" y="3" width="7" height="7" rx="2"/><rect x="14" y="3" width="7" height="7" rx="2"/><rect x="3" y="14" width="7" height="7" rx="2"/><rect x="14" y="14" width="7" height="7" rx="2"/>',
        'book' => '<path d="M12 5c-3-2-6-2-9-1v15c3-1 6-1 9 1 3-2 6-2 9-1V4c-3-1-6-1-9 1Zm0 0v15"/>',
        'message' => '<path d="M20 11a8 8 0 0 1-8 8H7l-4 3V11a8 8 0 1 1 17 0Z"/><path d="M8 9h7M8 13h5"/>',
        'scales' => '<path d="M12 3v18M7 21h10M4 6h16M6 6l-4 8h8L6 6Zm12 0-4 8h8l-4-8Z"/><path d="M2 14a4 4 0 0 0 8 0m4 0a4 4 0 0 0 8 0"/>',
        'listing' => '<rect x="4" y="3" width="16" height="18" rx="2"/><path d="M8 8h8M8 12h8M8 16h4"/>',
        'cart' => '<path d="M3 3h2l2 12h12l2-9H6M9 20h.01M18 20h.01"/><circle cx="9" cy="20" r="1"/><circle cx="18" cy="20" r="1"/>',
        'check' => '<path d="m5 12 4 4L19 6"/>',
    ];
    return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . ($paths[$name] ?? $paths['catalogue']) . '</svg>';
}

function zp_cs67_screen(array $case, string $lang): string {
    $asset = $case['screenshots'][1] ?? [];
    if (!$asset) { return ''; }
    $domain = preg_replace('~^https?://|/$~', '', (string) ($case['live'] ?? ''));
    return '<div class="zpCS67__browser"><div class="zpCS67__browserBar"><span class="zpCS67__browserDots" aria-hidden="true"><i></i><i></i><i></i></span><span>' . esc_html($domain) . '</span><span class="zpCS67__browserStatus">' . zp_cs67_icon('check') . '<span>' . esc_html($lang === 'en' ? 'Delivered website' : 'Wdrożona strona') . '</span></span></div>' . zp_cs18_figure($asset, $lang, 'zpCS67__screen') . '</div>';
}

/** A native disclosure retains all content in server-rendered HTML and works without JavaScript. */
function zp_cs67_disclosure(string $number, string $title, string $intro, string $text, bool $open = false): string {
    return '<details class="zpCS67__disclosure"' . ($open ? ' open' : '') . '><summary><span class="zpCS67__stepNumber" aria-hidden="true">' . esc_html($number) . '</span><span><strong>' . esc_html($title) . '</strong><small>' . esc_html($intro) . '</small></span><span class="zpCS67__plus" aria-hidden="true"></span></summary><div class="zpCS67__disclosureText"><p>' . esc_html($text) . '</p></div></details>';
}

function zp_cs67_experience(array $case, string $lang): string {
    $key = (string) ($case['key'] ?? '');
    if (!in_array($key, ['siemianowski', 'altiora-linea', 'swiat-grilli'], true)) { return ''; }
    $en = $lang === 'en';
    $lang = $en ? 'en' : 'pl';
    $copy = [
        'swiat-grilli' => $en ? [
            'From browsing to ordering', 'A store that connects every step.', 'every step.',
            'The visual identity is the starting point. Categories, product information, the basket and payment form one purchase journey — with a clear role for each screen.',
            'Plan your online store', 'Products, checkout and payments in one project scope.',
        ] : [
            'Od wyboru do zamówienia', 'Sklep, który łączy wszystkie kroki.', 'wszystkie kroki.',
            'Identyfikacja jest punktem wyjścia. Kategorie, informacje o produkcie, koszyk i płatność tworzą jedną drogę zakupową — a każdy ekran ma w niej konkretne zadanie.',
            'Zaplanuj swój sklep', 'Produkty, zamówienie i płatności w jednym zakresie prac.',
        ],
        'altiora-linea' => $en ? [
            'A catalogue that starts a conversation', 'From inspiration to a specific project.', 'a specific project.',
            'An architect may arrive with a chosen brand. An investor may be looking for a direction. The structure gives both a useful next step: explore the offer, find materials and discuss the interior.',
            'Plan a catalogue website', 'A presentation built around the way your customers choose.',
        ] : [
            'Katalog, który otwiera rozmowę', 'Od inspiracji do konkretnego projektu.', 'konkretnego projektu.',
            'Architekt może wejść z wybraną marką, a inwestor dopiero szukać kierunku. Struktura daje obu osobom następny krok: poznanie oferty, dostęp do materiałów i rozmowę o wnętrzu.',
            'Zaplanuj stronę z katalogiem', 'Prezentacja dopasowana do sposobu, w jaki wybierają Twoi klienci.',
        ],
        'siemianowski' => $en ? [
            'A website shaped around the firm', 'One law firm. Two clear paths.', 'Two clear paths.',
            'A visitor seeking legal advice and a visitor viewing an asset for sale need different information. We gave these tasks their own place within one recognisable website.',
            'Discuss your website and system', 'We connect your service offer with the functions your team needs.',
        ] : [
            'Strona dopasowana do kancelarii', 'Jedna kancelaria. Dwie jasne ścieżki.', 'Dwie jasne ścieżki.',
            'Osoba szukająca pomocy prawnej i osoba zainteresowana przedmiotem na sprzedaż potrzebują innych informacji. Oba zadania otrzymały własne miejsce w jednym, rozpoznawalnym serwisie.',
            'Omów stronę i własny system', 'Łączymy prezentację usług z funkcjami potrzebnymi Twojemu zespołowi.',
        ],
    ];
    $c = $copy[$key];
    $out = '<section id="projekt-w-praktyce" class="zpCS18__section zpCS67 zpCS67--' . esc_attr($key) . '" aria-labelledby="zpCS67ExperienceTitle"><div class="zpCS18__inner"><header class="zpCS67__header"><div>' . zp_cs18_eyebrow($c[0]) . '<h2 id="zpCS67ExperienceTitle">' . zp_cs18_heading($c[1], $c[2]) . '</h2></div><p class="zpCS67__lead">' . esc_html($c[3]) . '</p></header>';

    if ($key === 'swiat-grilli') {
        $steps = $en ? [
            ['01', 'Find the right product', 'Categories and manufacturers', 'Categories cover grills, smokers, accessories, fireplaces and outlet products. El Fuego, Landmann and Barbecook provide another way into the range for customers who already know their preferred manufacturer.'],
            ['02', 'Make an informed choice', 'Product pages and specifications', 'A product page brings the item, price and parameters together. This is where browsing becomes a specific choice, so the information needs to remain legible on a phone as well as a desktop.'],
            ['03', 'Review the order', 'Basket and checkout', 'The basket connects the selected products with the order form. WooCommerce provides the structure for collecting the information needed to place and handle an order.'],
            ['04', 'Move on to payment', 'imoje / ING integration', 'The payment integration follows the checkout stage. Contact information and details of the physical store remain available for customers who need support before buying.'],
        ] : [
            ['01', 'Trafić do właściwego produktu', 'Kategorie i producenci', 'Grille, wędzarnie, akcesoria, kominki i outlet porządkują asortyment. El Fuego, Landmann i Barbecook dają drugie wejście do oferty — dla osób, które mają już wybranego producenta.'],
            ['02', 'Sprawdzić przed decyzją', 'Karta produktu i parametry', 'Karta łączy prezentację produktu, cenę i parametry. To tutaj przeglądanie zamienia się w wybór konkretnego modelu, dlatego informacje muszą pozostać czytelne także na telefonie.'],
            ['03', 'Przejść do zamówienia', 'Koszyk i checkout', 'Koszyk łączy wybrane produkty z formularzem zamówienia. WooCommerce porządkuje dane potrzebne do złożenia i dalszej obsługi zakupu.'],
            ['04', 'Dokończyć płatność', 'Integracja imoje / ING', 'Płatność jest kolejnym etapem po złożeniu zamówienia. Kontakt i informacje o sklepie stacjonarnym uzupełniają proces dla osób, które przed zakupem potrzebują pomocy.'],
        ];
        $out .= '<div class="zpCS67__commerce"><div class="zpCS67__commerceVisual">' . zp_cs67_screen($case, $lang) . '<div class="zpCS67__commerceFoundation"><span class="zpCS67__foundationIcon">' . zp_cs67_icon('cart') . '</span><div><span class="zpCS67__micro">' . esc_html($en ? 'The store’s foundation' : 'Podstawa sklepu') . '</span><strong>WooCommerce</strong><p>' . esc_html($en ? 'Products → basket → order' : 'Produkty → koszyk → zamówienie') . '</p></div><div class="zpCS67__payment"><span>' . esc_html($en ? 'Payments' : 'Płatności') . '</span><strong>imoje <small>/ ING</small></strong></div></div></div><div class="zpCS67__journey"><p class="zpCS67__hint">' . esc_html($en ? 'Open a step to see its role in the project' : 'Rozwiń etap i zobacz jego rolę w projekcie') . '</p>';
        foreach ($steps as $i => $step) { $out .= zp_cs67_disclosure($step[0], $step[1], $step[2], $step[3], $i === 0); }
        $out .= '</div></div>';
    } elseif ($key === 'altiora-linea') {
        $nodes = $en ? [
            ['catalogue', '01', 'Brands and collections', 'Find a direction', 'A distinct space for manufacturers and their offer gives visitors a useful starting point.'],
            ['book', '02', 'Manufacturer catalogues', 'Explore the details', 'Materials help an architect or investor move from a photograph to a more specific selection.'],
            ['message', '03', 'A conversation about the interior', 'Connect the offer with the project', 'The contact form provides a next step for discussing needs and the scope of cooperation.'],
        ] : [
            ['catalogue', '01', 'Marki i kolekcje', 'Znajdź kierunek', 'Osobne miejsce dla producentów i ich oferty daje odbiorcy czytelny punkt startu.'],
            ['book', '02', 'Katalogi producentów', 'Poznaj szczegóły', 'Materiały pomagają architektowi lub inwestorowi przejść od fotografii do bardziej konkretnego wyboru.'],
            ['message', '03', 'Rozmowa o wnętrzu', 'Połącz ofertę z projektem', 'Formularz kontaktowy otwiera kolejny krok: ustalenie potrzeb i zakresu współpracy.'],
        ];
        $out .= '<div class="zpCS67__catalogue"><div class="zpCS67__catalogueVisual">' . zp_cs67_screen($case, $lang) . '<div class="zpCS67__audiences"><span>' . esc_html($en ? 'Designed for' : 'Z myślą o odbiorcach') . '</span><ul><li>' . esc_html($en ? 'Architects' : 'Architekci') . '</li><li>' . esc_html($en ? 'Interior designers' : 'Projektanci wnętrz') . '</li><li>' . esc_html($en ? 'Investors' : 'Inwestorzy') . '</li></ul></div></div><div class="zpCS67__map"><div class="zpCS67__mapTop"><span>' . esc_html($en ? 'One structure for the offer' : 'Jedna struktura oferty') . '</span><span class="zpCS67__languages" aria-label="' . esc_attr($en ? 'Polish, English and Italian' : 'Polski, angielski i włoski') . '"><b>PL</b><b>EN</b><b>IT</b></span></div><ol class="zpCS67__mapNodes">';
        foreach ($nodes as $i => $node) {
            $out .= '<li' . ($i === 2 ? ' class="zpCS67__mapNode--contact"' : '') . '><span class="zpCS67__nodeIcon">' . zp_cs67_icon($node[0]) . '</span><div><span class="zpCS67__micro">' . esc_html($node[1] . ' / ' . $node[3]) . '</span><h3>' . esc_html($node[2]) . '</h3><p>' . esc_html($node[4]) . '</p></div></li>';
        }
        $out .= '</ol></div></div>';
    } else {
        $routes = $en ? [
            ['scales', 'Legal assistance', 'I need help with a matter', ['Area of practice', 'Information about the firm', 'Contact'], 'Service content helps the visitor understand the firm’s scope and find the right route to a conversation.'],
            ['listing', 'Administrator listings', 'I am interested in an asset', ['Listings', 'Asset information', 'Enquiry'], 'Sale listings have their own area. New assets can be published without mixing them with permanent descriptions of legal services.'],
        ] : [
            ['scales', 'Pomoc prawna', 'Szukam pomocy w swojej sprawie', ['Zakres usług', 'Informacje o kancelarii', 'Kontakt'], 'Treść usługowa pomaga rozpoznać zakres działalności kancelarii i znaleźć właściwą drogę do rozmowy.'],
            ['listing', 'Oferty syndyka', 'Interesuje mnie przedmiot na sprzedaż', ['Lista ofert', 'Informacje o przedmiocie', 'Zapytanie'], 'Sprzedaż ma własny obszar. Kolejny przedmiot można opublikować bez mieszania go ze stałymi opisami usług prawnych.'],
        ];
        $out .= '<div class="zpCS67__legal"><div class="zpCS67__legalVisual">' . zp_cs67_screen($case, $lang) . '<div class="zpCS67__publication"><span class="zpCS67__micro">' . esc_html($en ? 'The team’s workflow' : 'Po stronie zespołu kancelarii') . '</span><ol><li>' . esc_html($en ? 'Add an asset' : 'Dodanie przedmiotu') . '</li><li>' . esc_html($en ? 'Publish the listing' : 'Publikacja oferty') . '</li><li>' . esc_html($en ? 'Present it on the website' : 'Prezentacja na stronie') . '</li></ol></div></div><div class="zpCS67__routes">';
        foreach ($routes as $i => $route) {
            $out .= '<article class="zpCS67__route"><div class="zpCS67__routeTop"><span class="zpCS67__routeIcon">' . zp_cs67_icon($route[0]) . '</span><span class="zpCS67__micro">' . esc_html($route[1]) . '</span><b aria-hidden="true">0' . ($i + 1) . '</b></div><h3>' . esc_html($route[2]) . '</h3><ol>';
            foreach ($route[3] as $item) { $out .= '<li>' . esc_html($item) . '</li>'; }
            $out .= '</ol><p>' . esc_html($route[4]) . '</p></article>';
        }
        $out .= '</div></div>';
        $out .= zp_cs67_disclosure('↳', $en ? 'What makes the custom module useful?' : 'Co daje własny moduł ofert?', $en ? 'Separate publishing, a consistent brand' : 'Osobna publikacja, wspólny wizerunek', $en ? 'The firm can add sale items within its own website. The visitor sees a consistent presentation, while permanent service content and changing listings remain separate. A similar approach can support a business that needs to publish properties, equipment or other regularly updated offers.' : 'Kancelaria może dodawać przedmioty sprzedaży we własnym serwisie. Odbiorca widzi spójną prezentację, a stałe informacje o usługach i zmienne oferty pozostają rozdzielone. Podobne podejście można zastosować w firmie, która potrzebuje publikować nieruchomości, sprzęt lub inne regularnie aktualizowane oferty.');
    }
    $out .= '<footer class="zpCS67__footer"><div><span class="zpCS67__micro">' . esc_html($en ? 'A similar challenge in your business?' : 'Podobne wyzwanie w Twojej firmie?') . '</span><p>' . esc_html($c[5]) . '</p></div>' . zp_cs18_button($c[4], '#szybki-kontakt', $lang, 'zpCS67__cta') . '</footer></div></section>';
    return $out;
}
