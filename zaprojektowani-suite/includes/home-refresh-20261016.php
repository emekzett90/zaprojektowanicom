<?php
/** Homepage FAQ and review presentation. Verified review snapshot: 16 October 2026. */
if (!defined('ABSPATH')) { exit; }

function zp_home_refresh_english(): bool {
    return function_exists('zpl_language') && zpl_language() === 'en';
}

function zp_reviews_snapshot(): array {
    static $snapshot;
    if ($snapshot === null) {
        $decoded = json_decode((string) file_get_contents(ZP_SUITE_PATH . 'assets/data/reviews-20261016.json'), true);
        $snapshot = is_array($decoded) ? $decoded : [];
    }
    return $snapshot;
}

/** Mat 21.10.2026, point 6: the verified aggregate every badge and counter shows (rating as a number, review count). */
function zp_reviews_aggregate(): array {
    $a = zp_reviews_snapshot()['aggregate'] ?? [];
    $rating = isset($a['rating']) ? (float) $a['rating'] : 0.0;
    $count = isset($a['reviewCount']) ? (int) $a['reviewCount'] : 0;
    return ['rating' => $rating > 0 ? $rating : 4.9, 'count' => $count > 0 ? $count : 150];
}

/** "4,9" on Polish pages, "4.9" on English ones. */
function zp_reviews_rating_text(bool $english = false): string {
    return number_format(zp_reviews_aggregate()['rating'], 1, $english ? '.' : ',', '');
}

/** Change aggregate badges only. Individual five-star reviews retain their own rating. */
function zp_reviews_truth_fragment(string $html, bool $facebook, bool $english): string {
    $s = zp_reviews_snapshot();
    $count = (string)($facebook ? ($s['facebookVerified']['reviewCount'] ?? 62) : zp_reviews_aggregate()['count']);
    $numericRating = zp_reviews_rating_text(true);
    $percentage = (string)($s['facebookVerified']['recommendPercent'] ?? 100);
    $rating = $facebook ? $percentage.'%' : ($english ? $numericRating : str_replace('.',',',$numericRating));
    $html = preg_replace('~(?<=>)\s*(?:5[.,]0(?:\s*/\s*5)?|5\s*/\s*5)\s*(?:★)?\s*(?=<)~u', $rating, $html) ?? $html;
    $html = preg_replace('~\b(data-(?:count|score))=(["\'])5[.,]0\2~', '$1="' . ($facebook ? $percentage : $numericRating) . '"', $html) ?? $html;
    $html = preg_replace('~(data-count="' . preg_quote($numericRating, '~') . '"[^>]*>)0(?=<)~', '${1}' . ($english ? $numericRating : str_replace('.', ',', $numericRating)), $html) ?? $html;
    $html = preg_replace('~(aria-label=["\'][^"\']*(?:ocena|rating)[^"\']*)5[.,]0~iu', '${1}' . $rating, $html) ?? $html;
    $html = preg_replace('~\b(?:120|60|61|50|22)\s+(?=(?:zweryfikowanych\s+)?(?:opini[ie]|reviews|customer reviews|client reviews))~u', $count . ' ', $html) ?? $html;
    if ($facebook) {
        $html = str_replace(['★★★★★', 'rekomendacje i kontakt na Facebooku', 'rekomendacje i kontakt', 'recommendations and contact'], ['', '100% poleca na Facebooku', '100% poleca', '100% recommend'], $html);
        $html = preg_replace('~(?:62)\s+opinii\b~u', '62 opinie', $html) ?? $html;
    }
    return $html;
}

/** Final translated HTML hook: legacy Elementor/CMS fragments and templates share the verified figures. */
function zp_reviews_truth_document(string $html, $language = 'pl', $source = ''): string {
    if (is_admin() || strlen($html) > 8000000 || strpos($html, '</head>') === false || !function_exists('zp_speed_scan')) { return $html; }
    $scan = zp_speed_scan($html); if (!$scan) { return $html; }
    $en = $language === 'en'; $edits = [];
    if (preg_match_all('~<(a|div|span|article)(?=[\s>])[^>]*>~i', $html, $matches, PREG_OFFSET_CAPTURE)) {
        foreach ($matches[0] as $i => $m) {
            $at = $m[1]; if (zp_speed_in_skip($scan['skip'], $at)) { continue; }
            $attrs = zp_services_faq_attributes($m[0]); $tag = strtolower($matches[1][$i][0]);
            $href = $attrs['href'] ?? ''; $class = $attrs['class'] ?? '';
            $fb = strpos($href, 'facebook.com/zaprojektowanicom') !== false;
            $trust = strpos($href, 'trustindex.io') !== false;
            $known = preg_match('~(?:^|\s)(?:reviews-score|zpAboutX__trustindexScore|zpAboutX__award|zp-proof__ratingText|zh__stat|zhMobileBrandStat|rail-stat)(?:\s|$)~', $class);
            if (!$fb && !$trust && !$known) { continue; }
            $end = zp_speed_nested_end($html, $tag, $at + strlen($m[0])); if ($end < 0) { continue; }
            $fragment = substr($html, $at, $end-$at);
            if ($known && strpos($fragment,'5.0') === false && strpos($fragment,'5,0') === false && strpos($fragment,'120') === false) { continue; }
            if (preg_match('~(?:^|\s)(?:zh__stat|zhMobileBrandStat)(?:\s|$)~', $class) && !preg_match('~średnia ocena|average rating~iu', $fragment)) { continue; }
            $new = zp_reviews_truth_fragment($fragment, $fb, $en);
            if ($fb && $new !== $fragment) { $new = preg_replace('~href=(["\'])https://www.facebook.com/zaprojektowanicom/?\1~', 'href="https://www.facebook.com/zaprojektowanicom/reviews"', $new) ?? $new; }
            if ($trust) { $new = str_replace(['href="https://www.trustindex.io/"','href="https://trustindex.io/"'], 'href="https://www.trustindex.io/reviews/zaprojektowani.com"', $new); }
            if ($known) { $new = str_replace(['Google i Facebook','Google and Facebook'], zp_reviews_aggregate()['count'] . ($en ? ' reviews on Trustindex' : ' opinii w Trustindex'), $new); }
            if ($new !== $fragment) { $edits[] = [$at,$end,$new]; }
        }
    }
    // Use outermost matched badge only; avoid overlapping edits in nested markup.
    usort($edits, static function($a,$b){return $a[0]<=>$b[0];}); $safe=[]; $last=-1;
    foreach($edits as $edit){if($edit[0]>=$last){$safe[]=$edit;$last=$edit[1];}}
    foreach(array_reverse($safe) as [$at,$end,$new]){$html=substr($html,0,$at).$new.substr($html,$end);}
    // Standalone captions adjacent to a Trustindex badge (About/portfolio). Do not alter scripts or styles.
    $scan = zp_speed_scan($html);
    if ($scan && preg_match_all('~(?<=>)120 (zweryfikowanych opinii klientów|opinii klientów|verified client reviews|verified customer reviews|verified reviews|client reviews|customer reviews)(?=<| ·| •)~u', $html, $captions, PREG_OFFSET_CAPTURE)) {
        foreach (array_reverse($captions[0]) as [$text,$at]) {
            if (!zp_speed_in_skip($scan['skip'],$at)) { $html=substr_replace($html,zp_reviews_aggregate()['count'].substr($text,3),$at,strlen($text)); }
        }
    }
    // The advertising-agency landing also states the old aggregate in a prose paragraph.
    $rating = zp_reviews_rating_text(true);
    $count = zp_reviews_aggregate()['count'];
    $prose = [
        'Średnia 5,0 ze 120 opinii.' => 'Średnia '.zp_reviews_rating_text(false).'/5 w Trustindex na podstawie '.$count.' opinii.',
        'An average of 5.0 from 120 reviews.' => 'A '.$rating.'/5 rating on Trustindex, based on '.$count.' reviews.',
    ];
    $scan = zp_speed_scan($html);
    foreach ($prose as $old => $new) {
        $at = 0;
        while (($at = strpos($html,$old,$at)) !== false) {
            if ($scan && !zp_speed_in_skip($scan['skip'],$at)) {
                $html=substr_replace($html,$new,$at,strlen($old));
                $scan=zp_speed_scan($html);
                $at+=strlen($new);
            } else { $at+=strlen($old); }
        }
    }
    return $html;
}
add_filter('zp_en_public_price_html','zp_reviews_truth_document',100,3);

function zp_home_faq_20261016(bool $en = false): array {
    $rows = $en ? [
        ['Where should I start: a website, an online store or branding?', 'Start with your business goal. A service business usually needs a website with a clear offer and an enquiry form. To sell products online, choose an online store. If your brand has no consistent identity, we can start with the logo and visual identity, then apply them to the website and advertising. Describe your situation in our quote studio and we will suggest a suitable scope.'],
        ['How is a website quote calculated?', 'The scope determines the price: page count, content, languages, animations and integrations. Our proposal sets out the design and development work, what you need to provide, the timeline and any recurring costs such as hosting, licences or maintenance. Extra work is agreed before it starts. You can compare our website packages or request an individual quote.'],
        ['How long does a project take?', 'A business website usually takes 2–4 weeks, a WooCommerce store 3–6 weeks and a logo 7–14 days. We confirm the schedule for your scope before starting. The availability of content, feedback and external integrations can affect the delivery date; we agree on milestones and review stages in advance.'],
        ['Will I be able to update the website myself?', 'Yes. We build websites on WordPress and online stores on WooCommerce. You receive the agreed access and guidance for editing text, images, posts or products. Custom features may need developer support; we explain what you can manage yourself and what is covered by any maintenance arrangement.'],
        ['What does an online store project include?', 'The agreed scope can include product and category pages, the basket and checkout, payments, delivery methods and transactional emails. We test the purchase journey on mobile and desktop before launch. Product imports, subscriptions, B2B pricing or ERP integrations are scoped separately, so the proposal reflects how you actually sell.'],
        ['What is the difference between a logo and a branding project?', 'A logo project focuses on the mark and its agreed variants and export files. Branding also defines how the company communicates visually: colours, typography, usage rules and selected materials. The exact deliverables and revision rounds depend on the package. We can carry the same visual identity through to your website, store and campaigns.'],
        ['Can you deliver the branding and website together?', 'Yes. One team can plan the identity, website or online store and launch materials. We usually approve the visual direction before applying it across the website. The proposal separates deliverables and approval stages, so you know what is included and when each part will be ready.'],
        ['Is SEO included, and do you guarantee Google positions?', 'The agreed website scope includes technical foundations such as readable content, headings, metadata, internal links and indexability checks. Ongoing SEO is a separate activity involving content, analysis and development. We do not guarantee a particular position, indexing date, number of enquiries or AI citations; search engines make those decisions.'],
        ['Can you migrate or rebuild an existing website?', 'Yes. We first review its content, URLs, integrations and available analytics. Where needed, we prepare a redirect map, move the agreed content and check important pages after launch. Migration can involve changes in search visibility, so we cannot promise unchanged rankings; the aim is to reduce avoidable errors and monitor the transition.'],
        ['What happens after the website goes live?', 'We hand over the agreed access and explain how to use the website. Ongoing updates, backups, monitoring, content changes and development can be covered by a separate WordPress maintenance agreement. We define what is included, how to report an issue and the response arrangements before you choose that service.'],
        ['Can you run Google Ads or Meta Ads after launch?', 'Yes. We can prepare campaigns and the agreed conversion measurement for Google, Facebook and Instagram. Advertising spend paid to the platform is separate from our campaign management fee. Access, scope, creative work and reporting are agreed in the proposal. Results depend on the offer, market, budget and landing page.'],
        ['What materials should I prepare before starting?', 'Useful materials include your goals, an outline of the offer, existing brand files, photos, texts and examples of websites you like. For an existing website, we also agree which access is needed. If something is missing, we can include preparation or sourcing in the proposal. Please do not send passwords in a public brief.'],
        ['Is the quote free, and can we work remotely?', 'The initial project quote is free and non-binding. Choose a service in the quote studio and describe your needs. We can clarify the scope online and show relevant projects before you decide. Briefing, presentations, feedback and handover can all take place remotely; local meetings are arranged individually.'],
    ] : [
        ['Od czego zacząć: od strony, sklepu czy brandingu?', 'Zacznij od celu firmy. Do prezentacji usług i pozyskiwania zapytań zwykle wystarczy strona z czytelną ofertą i formularzem. Do sprzedaży produktów potrzebujesz sklepu. Jeśli marka nie ma spójnego wizerunku, możemy zacząć od logo i identyfikacji, a potem przenieść je na stronę oraz reklamy. W Studio Wyceny opisz swoją sytuację — zaproponujemy zakres dopasowany do tego, co chcesz osiągnąć.'],
        ['Ile kosztuje strona internetowa i co wpływa na wycenę?', 'Strony firmowe zaczynają się u nas od 3 999 zł. Cenę ustalamy według liczby podstron, treści, języków, animacji i integracji. W ofercie określamy zakres projektu i wdrożenia, materiały po Twojej stronie, harmonogram oraz ewentualne koszty cykliczne: domenę, hosting, licencje i opiekę. Dodatkowe prace uzgadniamy przed ich rozpoczęciem. Szczegóły znajdziesz w ofercie tworzenia stron internetowych.'],
        ['Ile trwa realizacja strony, sklepu albo logo?', 'Strona firmowa powstaje zwykle w 2–4 tygodnie, sklep WooCommerce w 3–6 tygodni, a logo w 7–14 dni. Dokładny termin potwierdzamy dla wybranego zakresu przed startem. Wpływają na niego także materiały, czas na akceptacje i integracje z zewnętrznymi usługami. Wcześniej ustalamy etapy oraz momenty, w których potrzebujemy Twojej opinii.'],
        ['Czy po wdrożeniu mogę samodzielnie zmieniać treści?', 'Tak. Strony wdrażamy na WordPressie, a sklepy na WooCommerce. Dostajesz ustalone dostępy i wskazówki do zmiany tekstów, zdjęć, wpisów albo produktów. Funkcje tworzone na zamówienie mogą wymagać pomocy programisty. Wyjaśniamy, co zmienisz samodzielnie, a co można objąć osobną opieką nad stroną.'],
        ['Co obejmuje tworzenie sklepu internetowego?', 'Sklepy WooCommerce zaczynają się od 6 499 zł. Ustalony zakres może obejmować produkty i kategorie, koszyk, zamówienia, płatności, dostawy oraz wiadomości transakcyjne. Przed publikacją sprawdzamy ścieżkę zakupu na telefonie i komputerze. Import produktów, subskrypcje, ceny B2B czy połączenie z ERP wyceniamy zgodnie z potrzebami, żeby oferta odpowiadała Twojemu modelowi sprzedaży.'],
        ['Czym różni się projekt logo od pełnego brandingu?', 'Projekt logo obejmuje znak oraz uzgodnione warianty i pliki do wykorzystania. Branding porządkuje szerszy system: kolory, typografię, zasady użycia i wybrane materiały firmowe. Logo Startowe kosztuje 999 zł, Mini Branding 1 499 zł, a Branding Premium 2 999 zł. Dokładny zakres plików i poprawek opisujemy przy pakietach projektowania logo. Tę samą identyfikację możemy zastosować na stronie, w sklepie i reklamach.'],
        ['Czy można zamówić logo, stronę i reklamy w jednym projekcie?', 'Tak. Jeden zespół może przygotować identyfikację, stronę lub sklep oraz materiały do startu kampanii. Zwykle najpierw zatwierdzamy kierunek wizualny, a potem przenosimy go na kolejne elementy. W ofercie rozdzielamy zakres i etapy akceptacji, żeby było jasne, co powstaje i w jakiej kolejności.'],
        ['Czy strona będzie przygotowana pod SEO? Gwarantujecie pozycje?', 'W ustalonym zakresie wdrożenia przygotowujemy podstawy techniczne: czytelne treści, nagłówki, metadane, linki wewnętrzne i sprawdzenie możliwości indeksowania. Stałe pozycjonowanie to osobna praca nad treściami, analizą i rozwojem serwisu. Nie gwarantujemy konkretnej pozycji, terminu indeksacji, liczby zapytań ani cytowań w AI — o widoczności decydują wyszukiwarki.'],
        ['Czy możecie przebudować lub przenieść istniejącą stronę?', 'Tak. Najpierw sprawdzamy treści, adresy URL, integracje i dostępną analitykę. W razie potrzeby przygotowujemy mapę przekierowań, przenosimy uzgodnione materiały i kontrolujemy ważne podstrony po publikacji. Migracja może wiązać się ze zmianami widoczności w Google, dlatego nie obiecujemy niezmiennych pozycji. Celem jest ograniczenie błędów i monitorowanie przejścia na nową stronę.'],
        ['Co dzieje się po publikacji? Czy oferujecie opiekę WordPress?', 'Przekazujemy ustalone dostępy i wskazówki do obsługi. Aktualizacje, kopie zapasowe, monitoring, zmiany treści i dalszy rozwój można objąć osobną opieką WordPress. Przed wyborem tej usługi określamy zakres, sposób zgłaszania problemów i zasady reakcji. Dzięki temu wiesz, jakie działania są w pakiecie, a które wymagają dodatkowej wyceny.'],
        ['Czy uruchamiacie Google Ads i Meta Ads? Co z budżetem?', 'Tak. Możemy przygotować kampanie i uzgodniony pomiar konwersji w Google, na Facebooku i Instagramie. Obsługa zaczyna się od 1 200 zł miesięcznie. Budżet reklamowy płacony platformie jest osobnym kosztem. Dostępy, zakres kreacji i raportowania ustalamy w ofercie. Wyniki zależą również od oferty firmy, rynku, budżetu i strony docelowej.'],
        ['Jakie materiały i informacje przygotować przed startem?', 'Przydadzą się cel projektu, opis oferty, obecne pliki marki, zdjęcia, teksty i przykłady stron, które Ci się podobają. Przy istniejącym serwisie ustalamy także potrzebne dostępy. Jeśli materiałów brakuje, możemy uwzględnić ich przygotowanie lub pozyskanie w ofercie. Haseł nie przesyłaj w publicznym briefie — sposób przekazania dostępów uzgodnimy osobno.'],
        ['Czy wycena jest bezpłatna i czy możemy pracować zdalnie?', 'Wstępna wycena projektu jest bezpłatna i niezobowiązująca. Wybierz usługę w Studio Wyceny i opisz potrzeby. Możemy doprecyzować zakres online i pokazać podobne realizacje, zanim podejmiesz decyzję. Brief, prezentacje, poprawki i przekazanie projektu prowadzimy również zdalnie. Spotkania lokalne umawiamy indywidualnie.'],
    ];
    return array_map(static function ($row) { return ['q' => $row[0], 'a' => $row[1]]; }, $rows);
}
