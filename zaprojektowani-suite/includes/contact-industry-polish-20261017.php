<?php
/**
 * Small contact-heading correction and useful examples on the eight industry pages.
 * All changes are render-time only. Existing copy, forms, heroes and routes stay intact.
 * The examples run after translation and portfolio selection, without another buffer.
 */
if (!defined('ABSPATH')) { exit; }

function zp_ci_source_20261017(): string {
    if (is_admin() || !did_action('wp') || !is_singular('page') || is_preview() || is_feed()) { return ''; }
    $id = (int) get_queried_object_id();
    if (!$id || get_post_status($id) !== 'publish' || (string) get_post_field('post_password', $id) !== '') { return ''; }
    if (class_exists('\\ZPL\\Router') && \ZPL\Router::$source !== '') { return (string) \ZPL\Router::$source; }
    return function_exists('zp_seo_plan_path') ? zp_seo_plan_path((string) get_permalink($id)) : '';
}

/** Preserve the exact SEO H1 wording and add emphasis to its existing final phrase. */
function zp_ci_contact_heading_20261017(string $html): string {
    if (strpos($html, 'zh--contact') === false) { return $html; }
    $headings = [
        'Kontakt z Zaprojektowani — opowiedz o swoim projekcie' => ['Kontakt z Zaprojektowani — ', 'opowiedz o swoim projekcie'],
        'Contact Zaprojektowani — tell us about your project' => ['Contact Zaprojektowani — ', 'tell us about your project'],
    ];
    $result = preg_replace_callback('~(<h1\b[^>]*\bclass=(["\'])[^"\']*\bzh__title\b[^"\']*\2[^>]*>)(.*?)</h1>~is', static function ($match) use ($headings) {
        if (strpos($match[3], 'zpContactHeroAccent') !== false) { return $match[0]; }
        $plain = trim(html_entity_decode(wp_strip_all_tags($match[3]), ENT_QUOTES, 'UTF-8'));
        $plain = (string) preg_replace('~\s+~u', ' ', $plain);
        if (!isset($headings[$plain])) { return $match[0]; }
        [$prefix, $accent] = $headings[$plain];
        return $match[1] . esc_html($prefix) . '<strong class="zh__grad zpContactHeroAccent">' . esc_html($accent) . '</strong></h1>';
    }, $html, 1);
    return is_string($result) ? $result : $html;
}

add_filter('do_shortcode_tag', static function ($html, $tag) {
    if (!is_string($html) || !in_array($tag, ['zp_contact_page', 'zp_kontakt'], true)) { return $html; }
    return zp_ci_contact_heading_20261017($html);
}, 100, 2);

// Also covers an existing contact-page HTML override instead of its shortcode.
add_filter('the_content', static function ($html) {
    if (!is_string($html) || zp_ci_source_20261017() !== '/kontakt/') { return $html; }
    return zp_ci_contact_heading_20261017($html);
}, 99);

add_filter('zpl_dictionary', static function ($map, $lang = 'en') {
    $dictionary = [
        'Kontakt z Zaprojektowani — opowiedz o swoim projekcie' => 'Contact Zaprojektowani — tell us about your project',
        'Kontakt z Zaprojektowani — <1>opowiedz o swoim projekcie</1>' => 'Contact Zaprojektowani — <1>tell us about your project</1>',
        'Kontakt z Zaprojektowani —' => 'Contact Zaprojektowani —',
        'opowiedz o swoim projekcie' => 'tell us about your project',
    ];
    if (function_exists('zp_suite_contact_280_merge_dictionary')) {
        return zp_suite_contact_280_merge_dictionary($map, (string) $lang, $dictionary);
    }
    return $map;
}, 11, 2);

/** Planning examples, clearly separate from actual portfolio projects. */
function zp_ci_industry_examples_20261017(): array {
    return [
        'kancelarie' => [
            'pl' => [
                'title' => 'Przykładowa struktura', 'accent' => 'strony kancelarii.',
                'lead' => 'Zacznijmy od pytań osoby, która szuka pomocy. Taki układ pomaga jej rozpoznać właściwą specjalizację, poznać prawnika i przygotować się do pierwszej rozmowy.',
                'items' => [
                    ['Specjalizacje opisane osobno', 'Prawo rodzinne, spadki czy obsługa firm otrzymują własne podstrony: sytuacje klientów, zakres pomocy i sposób rozpoczęcia współpracy.'],
                    ['Prawnicy i doświadczenie', 'Profile zespołu pokazują kwalifikacje, obszary praktyki, języki oraz publikacje. Z podstrony usługi można przejść do osoby, która się nią zajmuje.'],
                    ['Pierwsza konsultacja', 'Wyjaśniamy, jak umówić rozmowę, czy może odbyć się online i jakie dokumenty warto przygotować po kontakcie z kancelarią.'],
                    ['Odpowiedzi na konkretne pytania', 'Poradniki rozwijają najczęstsze pytania klientów i prowadzą do powiązanej specjalizacji, dzięki czemu wiedza i oferta są ze sobą połączone.'],
                    ['Kontakt bez zbędnych kroków', 'Widoczny telefon, adres i krótki formularz z wyborem tematu. Szczegóły sprawy kancelaria ustala już w bezpośredniej rozmowie.'],
                ],
                'scenario_title' => 'Przykład: zapytanie o sprawę spadkową',
                'scenario' => ['Klient otwiera podstronę dotyczącą spadków.', 'Sprawdza zakres pomocy i profil prawnika.', 'Wybiera kontakt i dowiaduje się, jak wygląda konsultacja.'],
                'materials' => 'Na start przydadzą się: lista specjalizacji, opisy zespołu, zdjęcia biura i informacje o przebiegu konsultacji.',
            ],
            'en' => [
                'title' => 'An example structure for', 'accent' => 'a law firm website.',
                'lead' => 'Start with the questions of someone seeking legal help. This structure lets them find a relevant practice area, get to know the lawyer and prepare for a first conversation.',
                'items' => [
                    ['Separate practice areas', 'Family law, inheritance or business services each receive a page explaining client situations, the scope of help and how to get started.'],
                    ['Lawyers and experience', 'Team profiles present qualifications, practice areas, languages and publications. Each service connects with the lawyer responsible for it.'],
                    ['The first consultation', 'Explain how to arrange a conversation, whether it can take place online and which documents to prepare after contacting the firm.'],
                    ['Answers to specific questions', 'Guides address common client questions and link to the relevant practice area, connecting useful information with the service offer.'],
                    ['A straightforward way to contact you', 'A visible telephone number, address and short form with a topic selector. The firm can discuss case details directly with the client.'],
                ],
                'scenario_title' => 'Example: an inheritance enquiry',
                'scenario' => ['A visitor opens the inheritance practice page.', 'They review the scope of help and the lawyer’s profile.', 'They choose a contact method and learn what to expect from the consultation.'],
                'materials' => 'Useful starting materials: practice areas, team profiles, office photographs and information about the consultation process.',
            ],
        ],
        'lekarze' => [
            'pl' => [
                'title' => 'Przykładowa struktura', 'accent' => 'strony gabinetu.',
                'lead' => 'Pacjent powinien łatwo sprawdzić, czy gabinet wykonuje potrzebne świadczenie, kto przyjmuje i jak się zapisać. Z tych informacji budujemy czytelny plan strony.',
                'items' => [
                    ['Świadczenia i badania', 'Osobna podstrona opisuje zakres świadczenia, czas wizyty i przygotowanie do badania według materiałów zatwierdzonych przez gabinet.'],
                    ['Lekarze i godziny przyjęć', 'Profile specjalistów łączą kwalifikacje, zakres przyjęć i dostępny sposób rejestracji. Pacjent widzi, do kogo może się zgłosić.'],
                    ['Cennik i pierwsza wizyta', 'Zebrane w jednym miejscu ceny, informacje organizacyjne oraz lista dokumentów potrzebnych podczas pierwszej wizyty.'],
                    ['Dojazd i informacje o placówce', 'Adres, wejście do budynku, parking, dostępność dla osób z ograniczoną mobilnością i telefon do rejestracji.'],
                    ['Rejestracja w używanym systemie', 'Odnośnik lub integracja z obecnym systemem gabinetu. Wyraźnie rozróżniamy prośbę o kontakt od potwierdzonego terminu wizyty.'],
                ],
                'scenario_title' => 'Przykład: zapis na pierwszą wizytę',
                'scenario' => ['Pacjent wybiera specjalistę albo rodzaj świadczenia.', 'Sprawdza cenę, adres i przygotowanie do wizyty.', 'Przechodzi do rejestracji lub dzwoni do gabinetu.'],
                'materials' => 'Na start przydadzą się: zakres świadczeń, aktualny cennik, profile lekarzy, zdjęcia placówki i zasady rejestracji.',
            ],
            'en' => [
                'title' => 'An example structure for', 'accent' => 'a medical practice website.',
                'lead' => 'Patients need to find out whether the practice offers the service they need, who they can see and how to arrange an appointment. Those details form the website plan.',
                'items' => [
                    ['Services and examinations', 'A dedicated page explains the service, appointment duration and preparation using information approved by the practice.'],
                    ['Doctors and appointment hours', 'Specialist profiles connect qualifications, areas of practice and registration options, helping patients find the right person.'],
                    ['Prices and the first visit', 'Prices, practical information and the documents needed for a first visit are gathered in one place.'],
                    ['Directions and practice information', 'Show the address, building entrance, parking, accessibility information and reception telephone number.'],
                    ['Registration through your current system', 'Link to or integrate the practice’s existing system. Make the difference between a contact request and a confirmed appointment clear.'],
                ],
                'scenario_title' => 'Example: arranging a first visit',
                'scenario' => ['A patient chooses a specialist or a service.', 'They check the price, location and preparation instructions.', 'They proceed to registration or call the practice.'],
                'materials' => 'Useful starting materials: services, current prices, doctor profiles, practice photographs and registration rules.',
            ],
        ],
        'deweloperzy' => [
            'pl' => [
                'title' => 'Przykładowa struktura', 'accent' => 'strony inwestycji.',
                'lead' => 'Kupujący porównuje lokalizację, układ i cenę mieszkania. Strona powinna zebrać te informacje przy konkretnym lokalu oraz ułatwić kontakt z biurem sprzedaży.',
                'items' => [
                    ['Prezentacja inwestycji', 'Położenie, standard, harmonogram i wizualizacje pokazane w jednym miejscu, z odnośnikiem do listy mieszkań.'],
                    ['Lista mieszkań i karty lokali', 'Piętro, powierzchnia, liczba pokoi, cena i status dostępności. Karta lokalu zawiera rzut oraz plik do pobrania.'],
                    ['Lokalizacja i otoczenie', 'Mapa, dojazd i wybrane punkty w okolicy uzupełnione zdjęciami. Treść pomaga zrozumieć, jak wygląda codzienne życie w tej lokalizacji.'],
                    ['Postęp prac', 'Uporządkowana chronologicznie galeria z datami i podpisami, którą zespół inwestycji może aktualizować.'],
                    ['Zapytanie o wybrane mieszkanie', 'Formularz przenosi numer lokalu do zgłoszenia. Biuro sprzedaży od razu wie, którą ofertą zainteresował się klient.'],
                ],
                'scenario_title' => 'Przykład: porównanie dwóch mieszkań',
                'scenario' => ['Kupujący zawęża listę do powierzchni i liczby pokoi.', 'Otwiera rzut, cenę i szczegóły wybranego lokalu.', 'Wysyła zapytanie z numerem mieszkania.'],
                'materials' => 'Na start przydadzą się: lista lokali, rzuty, ceny, statusy dostępności, standard wykończenia i wizualizacje.',
            ],
            'en' => [
                'title' => 'An example structure for', 'accent' => 'a property development website.',
                'lead' => 'Buyers compare location, layout and price. The website should bring those details together for each home and make it easy to contact the sales team.',
                'items' => [
                    ['The development overview', 'Present the location, specification, schedule and visualisations together, with a link to the available homes.'],
                    ['Home listings and individual details', 'Floor, area, room count, price and availability status. Each home has a floor plan and downloadable details.'],
                    ['Location and surroundings', 'A map, travel information and nearby places supported by photographs help buyers understand everyday life in the area.'],
                    ['Construction progress', 'A chronological gallery with dates and captions that the development team can update.'],
                    ['An enquiry about a particular home', 'The form includes the home reference in the enquiry so the sales team knows which listing interests the buyer.'],
                ],
                'scenario_title' => 'Example: comparing two homes',
                'scenario' => ['A buyer narrows the list by area and room count.', 'They open the floor plan, price and details of a home.', 'They send an enquiry containing the property reference.'],
                'materials' => 'Useful starting materials: home listings, floor plans, prices, availability, specifications and visualisations.',
            ],
        ],
        'beauty' => [
            'pl' => [
                'title' => 'Przykładowa struktura', 'accent' => 'strony salonu beauty.',
                'lead' => 'Po obejrzeniu zdjęć klientka chce poznać usługę, cenę i dostępny sposób zapisu. Łączymy estetykę marki z informacjami potrzebnymi przed wizytą.',
                'items' => [
                    ['Zabiegi w czytelnych kategoriach', 'Zamiast jednej długiej listy: osobne grupy usług i podstrony z opisem przebiegu oraz informacjami przygotowanymi przez salon.'],
                    ['Cennik z zakresem usługi', 'Przy usłudze pokazujemy czas trwania, cenę i to, co obejmuje wizyta. Warianty długości lub zakresu mają własne wyjaśnienia.'],
                    ['Zespół i miejsce', 'Zdjęcia wnętrza i osób wykonujących usługi pomagają poznać salon jeszcze przed pierwszą wizytą.'],
                    ['Galeria prac i styl marki', 'Wybrane, opisane realizacje porządkujemy według usług. Kolorystyka i typografia pozostają spójne z logo oraz materiałami salonu.'],
                    ['Wygodny zapis', 'Po wyborze usługi klientka przechodzi do systemu rezerwacji albo wysyła prośbę o termin — zgodnie ze sposobem pracy salonu.'],
                ],
                'scenario_title' => 'Przykład: przejście z Instagrama do zapisu',
                'scenario' => ['Klientka otwiera link prowadzący do konkretnej usługi.', 'Sprawdza zakres, czas trwania i cenę.', 'Wybiera termin lub kontakt z recepcją.'],
                'materials' => 'Na start przydadzą się: lista usług, cennik, zdjęcia salonu i prac, opisy zespołu oraz używany system zapisów.',
            ],
            'en' => [
                'title' => 'An example structure for', 'accent' => 'a beauty salon website.',
                'lead' => 'After looking at the photographs, a client wants to understand the service, price and booking options. Connect the brand’s appearance with practical information before a visit.',
                'items' => [
                    ['Treatments in clear categories', 'Use service groups and individual pages describing the visit, with information prepared by the salon.'],
                    ['Prices with a clear scope', 'Show the duration, price and what each service includes. Explain any options that differ in length or scope.'],
                    ['The team and the salon', 'Photographs of the space and the people providing the services help visitors get to know the salon beforehand.'],
                    ['Work gallery and visual identity', 'Organise selected, captioned work by service. Keep colours and typography consistent with the salon’s logo and materials.'],
                    ['A convenient way to book', 'After choosing a service, clients enter the booking system or submit a preferred date, depending on how the salon works.'],
                ],
                'scenario_title' => 'Example: from Instagram to an appointment',
                'scenario' => ['A client opens a link to a specific service.', 'They check its scope, duration and price.', 'They select a date or contact reception.'],
                'materials' => 'Useful starting materials: services, prices, salon and work photographs, team profiles and your current booking system.',
            ],
        ],
        'trenerzy' => [
            'pl' => [
                'title' => 'Przykładowa struktura', 'accent' => 'strony trenera.',
                'lead' => 'Osoba zainteresowana współpracą chce sprawdzić, z kim pracujesz, gdzie odbywają się treningi i jak wygląda początek. Pokażmy to na konkretnym przykładzie.',
                'items' => [
                    ['Oferta dla różnych potrzeb', 'Trening indywidualny, w parze i współpraca online mają osobne opisy: dla kogo są, co obejmują i jak odbywa się kontakt.'],
                    ['Pierwsze spotkanie krok po kroku', 'Wyjaśniamy, gdzie się spotykacie, ile trwa konsultacja i co zabrać. Przy zapisie nie trzeba od razu kupować rozbudowanego pakietu.'],
                    ['Pakiety i zasady współpracy', 'Liczba treningów, czas trwania, cena i zasady umawiania. Klient może porównać zakres, zanim zada pytanie.'],
                    ['Trener i miejsce treningów', 'Kwalifikacje, sposób prowadzenia zajęć oraz zdjęcia studia, siłowni albo miejsca spotkań w plenerze.'],
                    ['Zapisy dopasowane do grafiku', 'Link do kalendarza lub formularz z wyborem formy współpracy i miejsca. Dalsze kroki po wysłaniu zgłoszenia są opisane przy zapisie.'],
                ],
                'scenario_title' => 'Przykład: zapytanie o trening po pracy',
                'scenario' => ['Klient sprawdza, czy trenujesz w jego okolicy.', 'Porównuje trening indywidualny z pakietem i czyta o pierwszym spotkaniu.', 'Wybiera dogodny sposób zapisu i przekazuje preferowane godziny.'],
                'materials' => 'Na start przydadzą się: oferta, miejsca treningów, ceny, zdjęcia, kwalifikacje i zasady umawiania zajęć.',
            ],
            'en' => [
                'title' => 'An example structure for', 'accent' => 'a personal trainer website.',
                'lead' => 'A prospective client wants to know who you work with, where sessions take place and how to begin. Make those details concrete.',
                'items' => [
                    ['Services for different needs', 'Individual training, sessions for pairs and online coaching each explain who they suit, what is included and how communication works.'],
                    ['The first meeting, step by step', 'Explain where to meet, how long the consultation takes and what to bring. A first enquiry does not need to require a large package purchase.'],
                    ['Packages and working arrangements', 'Number and duration of sessions, prices and scheduling rules help clients compare the scope before making an enquiry.'],
                    ['The trainer and training locations', 'Present qualifications, the approach to sessions and photographs of the studio, gym or outdoor meeting place.'],
                    ['Bookings around your schedule', 'Link to a calendar or provide a form to choose a service and location. Explain what happens after the request is submitted.'],
                ],
                'scenario_title' => 'Example: an enquiry about training after work',
                'scenario' => ['A visitor checks whether you train clients in their area.', 'They compare individual sessions with a package and read about the first meeting.', 'They choose a booking method and share their preferred times.'],
                'materials' => 'Useful starting materials: services, training locations, prices, photographs, qualifications and booking arrangements.',
            ],
        ],
        'fotografowie' => [
            'pl' => [
                'title' => 'Przykładowa struktura', 'accent' => 'strony fotografa.',
                'lead' => 'Najpierw styl zdjęć, później szczegóły współpracy i pytanie o termin. Tę kolejność zachowujemy zarówno w portfolio, jak i w ofercie poszczególnych sesji.',
                'items' => [
                    ['Portfolio według rodzaju sesji', 'Śluby, portrety, sesje rodzinne czy fotografia dla firm otrzymują osobne galerie, dzięki czemu klient szybko ogląda właściwy rodzaj pracy.'],
                    ['Pełne historie zdjęciowe', 'Oprócz wybranych kadrów pokazujemy spójny zestaw z jednej sesji lub reportażu, z krótkim opisem miejsca i zakresu.'],
                    ['Oferta i pakiety', 'Czas sesji, liczba przekazywanych zdjęć, sposób wyboru i termin oddania materiałów. Klient wie, co obejmuje współpraca.'],
                    ['Przygotowanie do sesji', 'Osobna sekcja odpowiada na pytania o lokalizację, strój i przebieg zdjęć. Jej treść można też przesłać przed spotkaniem.'],
                    ['Zapytanie o dostępność', 'Rodzaj sesji, termin, miejsce i dane kontaktowe wystarczą do rozpoczęcia rozmowy o zakresie oraz wycenie.'],
                ],
                'scenario_title' => 'Przykład: zapytanie o sesję wizerunkową',
                'scenario' => ['Klient ogląda galerię portretów biznesowych.', 'Sprawdza pakiet i sposób przygotowania do sesji.', 'Podaje termin, miejsce i orientacyjną liczbę fotografowanych osób.'],
                'materials' => 'Na start przydadzą się: selekcja zdjęć, pełne reportaże, pakiety, terminy przekazania materiałów i opis przebiegu sesji.',
            ],
            'en' => [
                'title' => 'An example structure for', 'accent' => 'a photographer website.',
                'lead' => 'First the photographic style, then the working arrangements and an availability enquiry. Follow that sequence in the portfolio and individual session offers.',
                'items' => [
                    ['A portfolio organised by session type', 'Weddings, portraits, family sessions and business photography receive separate galleries so visitors quickly find relevant work.'],
                    ['Complete photographic stories', 'Alongside selected images, show a consistent set from one session or event with a short description of the location and scope.'],
                    ['Services and packages', 'Explain session duration, delivered image count, selection process and delivery schedule so clients understand what is included.'],
                    ['Preparing for a session', 'A dedicated section answers questions about location, clothing and the session itself. It can also be shared before the appointment.'],
                    ['An availability enquiry', 'Session type, preferred date, location and contact details provide a starting point for discussing scope and price.'],
                ],
                'scenario_title' => 'Example: an enquiry about business portraits',
                'scenario' => ['A client explores the business portrait gallery.', 'They review a package and the preparation guidance.', 'They provide a date, location and approximate number of people.'],
                'materials' => 'Useful starting materials: selected images, complete stories, packages, delivery schedules and a description of the session process.',
            ],
        ],
        'restauracje' => [
            'pl' => [
                'title' => 'Przykładowa struktura', 'accent' => 'strony restauracji.',
                'lead' => 'Gość często zagląda na stronę z telefonu tuż przed wizytą. Menu, godziny otwarcia, dojazd i kontakt powinny być łatwe do znalezienia.',
                'items' => [
                    ['Menu wygodne na telefonie', 'Dania i ceny zapisane na stronie, z czytelnymi kategoriami. Plik PDF może być dodatkiem do pobrania, a nie jedynym sposobem poznania menu.'],
                    ['Zdjęcia potraw i wnętrza', 'Aktualne fotografie pokazują charakter kuchni i miejsca. Galerię dobieramy do oferty, która rzeczywiście jest dostępna.'],
                    ['Rezerwacja stolika', 'Liczba osób, data i godzina w krótkim formularzu albo przejście do systemu rezerwacji. Jasna informacja, kiedy stolik zostaje potwierdzony.'],
                    ['Przyjęcia i oferta dla grup', 'Osobna podstrona na imprezy okolicznościowe, spotkania firmowe lub catering z zakresem i pytaniami do wyceny.'],
                    ['Godziny, dojazd i zamówienia', 'Dane kontaktowe, mapa oraz odnośnik do zamówień na wynos, jeżeli lokal je prowadzi. Aktualizację godzin można wykonać w jednym miejscu.'],
                ],
                'scenario_title' => 'Przykład: planowanie kolacji ze znajomymi',
                'scenario' => ['Gość sprawdza menu i ceny na telefonie.', 'Ogląda wnętrze oraz godziny otwarcia.', 'Wybiera rezerwację dla określonej liczby osób.'],
                'materials' => 'Na start przydadzą się: aktualne menu, ceny, godziny, zdjęcia potraw i wnętrza oraz zasady rezerwacji.',
            ],
            'en' => [
                'title' => 'An example structure for', 'accent' => 'a restaurant website.',
                'lead' => 'Guests often visit the site on their phones shortly before coming in. Make the menu, opening hours, directions and contact information easy to find.',
                'items' => [
                    ['A menu that works on phones', 'Publish dishes and prices as website content with clear categories. A PDF can be an additional download rather than the only menu format.'],
                    ['Food and interior photographs', 'Current photographs introduce the food and atmosphere. Select images that reflect what the restaurant actually offers.'],
                    ['Table reservations', 'Use a short form for party size, date and time, or connect to a reservation system. Explain when a table is confirmed.'],
                    ['Events and group enquiries', 'A dedicated page for celebrations, corporate events or catering explains the available scope and information needed for a quote.'],
                    ['Opening hours, directions and ordering', 'Contact details, a map and a takeaway ordering link where relevant. Keep opening hours easy to update in one place.'],
                ],
                'scenario_title' => 'Example: arranging dinner with friends',
                'scenario' => ['A guest checks the menu and prices on their phone.', 'They look at the interior and opening hours.', 'They choose a reservation for their group size.'],
                'materials' => 'Useful starting materials: current menu, prices, opening hours, food and interior photographs, and reservation arrangements.',
            ],
        ],
        'hotele' => [
            'pl' => [
                'title' => 'Przykładowa struktura', 'accent' => 'strony hotelu.',
                'lead' => 'Gość potrzebuje porównać pokoje, sprawdzić warunki pobytu i znaleźć cenę na wybrany termin. Struktura strony powinna prowadzić go przez te decyzje.',
                'items' => [
                    ['Pokoje pokazane osobno', 'Galeria, metraż, rodzaj łóżek, wyposażenie i liczba gości. Każdy pokój ma własną kartę z przejściem do sprawdzenia terminu.'],
                    ['Cena i warunki pobytu', 'Informacje o posiłkach, parkingu, zameldowaniu oraz warunkach zmiany rezerwacji pokazujemy przed przejściem do zapisu.'],
                    ['Lokalizacja i pomysły na pobyt', 'Dojazd, odległości i wybrane miejsca w okolicy ułatwiają ocenę, czy obiekt pasuje do planu podróży.'],
                    ['Oferty na konkretny wyjazd', 'Pobyt rodzinny, weekend we dwoje czy wyjazd służbowy można przedstawić na oddzielnych podstronach z własnym zakresem.'],
                    ['Rezerwacja albo zapytanie', 'Połączenie ze stosowanym systemem obiektu lub formularz z datami i liczbą gości. Potwierdzenie dostępności wynika z wybranego sposobu obsługi.'],
                ],
                'scenario_title' => 'Przykład: rezerwacja weekendu dla rodziny',
                'scenario' => ['Gość porównuje pokoje mieszczące całą rodzinę.', 'Sprawdza wyposażenie, posiłki i warunki pobytu.', 'Wybiera daty i przechodzi do rezerwacji lub zapytania.'],
                'materials' => 'Na start przydadzą się: typy pokoi, zdjęcia, wyposażenie, warunki pobytu, cennik i informacje o systemie rezerwacji.',
            ],
            'en' => [
                'title' => 'An example structure for', 'accent' => 'a hotel website.',
                'lead' => 'Guests need to compare rooms, review stay conditions and find a price for their chosen dates. Organise the website around those decisions.',
                'items' => [
                    ['Individual room presentations', 'Photographs, floor area, bed types, facilities and occupancy. Each room has its own page leading to a date enquiry.'],
                    ['Price and stay conditions', 'Present meals, parking, check-in and booking-change conditions before the guest proceeds to reserve.'],
                    ['Location and ideas for a stay', 'Directions, distances and selected nearby places help guests decide whether the property suits their travel plans.'],
                    ['Offers for different trips', 'Family stays, weekends for two and business visits can each receive a dedicated page with a clearly defined scope.'],
                    ['A booking or an enquiry', 'Connect the property’s existing system or use a form with dates and guest numbers. Availability confirmation follows the chosen booking process.'],
                ],
                'scenario_title' => 'Example: a family weekend booking',
                'scenario' => ['A guest compares rooms suitable for the family.', 'They review facilities, meals and stay conditions.', 'They select dates and proceed to book or send an enquiry.'],
                'materials' => 'Useful starting materials: room types, photographs, facilities, stay conditions, prices and your booking system details.',
            ],
        ],
    ];
}

function zp_ci_url_20261017(string $path, bool $english): string {
    $url = home_url($path);
    return function_exists('zpl_url') ? zpl_url($url, $english ? 'en' : 'pl') : $url;
}

function zp_ci_industry_markup_20261017(string $key, bool $english, bool $has_work = false): string {
    $spec = zp_ci_industry_examples_20261017()[$key][$english ? 'en' : 'pl'] ?? null;
    if (!$spec) { return ''; }
    $cta = [
        'kancelarie' => ['Wyceń stronę kancelarii', 'Get a quote for your law firm website'],
        'lekarze' => ['Wyceń stronę gabinetu', 'Get a quote for your practice website'],
        'deweloperzy' => ['Wyceń stronę inwestycji', 'Get a quote for your development website'],
        'beauty' => ['Wyceń stronę salonu', 'Get a quote for your salon website'],
        'trenerzy' => ['Wyceń stronę trenera', 'Get a quote for your trainer website'],
        'fotografowie' => ['Wyceń stronę z portfolio', 'Get a quote for your portfolio website'],
        'restauracje' => ['Wyceń stronę restauracji', 'Get a quote for your restaurant website'],
        'hotele' => ['Wyceń stronę swojego obiektu', 'Get a quote for your hotel website'],
    ];
    $work = [
        'kancelarie' => ['Zobacz stronę Siemianowski i identyfikacje kancelarii', 'Explore the Siemianowski website and law firm identities'],
        'lekarze' => ['Zobacz branding Medical Friend i rezerwacje w projekcie beauty', 'Explore Medical Friend branding and a beauty appointment project'],
        'deweloperzy' => ['Zobacz realizacje z obszaru architektury i nieruchomości', 'Explore our architecture and property-related work'],
        'beauty' => ['Zobacz stronę Apartamentu Piękna i branding salonów', 'Explore the Apartament Piękna website and salon branding'],
        'trenerzy' => ['Zobacz przykłady zapisów z innych branż', 'Explore appointment examples from other industries'],
        'fotografowie' => ['Zobacz przykłady prezentacji zdjęć i wizyt z innych branż', 'Explore image presentation and appointment examples from other industries'],
        'restauracje' => ['Zobacz Piekarnię Marysia i branding marek gastronomicznych', 'Explore Piekarnia Marysia and food brand identities'],
        'hotele' => ['Zobacz przykład systemu wizyt z branży beauty', 'Explore an appointment-system example from the beauty industry'],
    ];
    $html = '<section class="zpIndustryPlan" id="zpIndustryPlan" data-zp-industry-example="' . esc_attr($key) . '" aria-labelledby="zpIndustryPlanTitle"><div class="zpIndustryPlan__inner">';
    $html .= '<header class="zpIndustryPlan__head"><div><p class="zpIndustryPlan__kicker">' . ($english ? 'Before we design' : 'Zanim zaczniemy projekt') . '</p><h2 id="zpIndustryPlanTitle">' . esc_html($spec['title']) . ' <strong>' . esc_html($spec['accent']) . '</strong></h2></div><p class="zpIndustryPlan__lead">' . esc_html($spec['lead']) . '</p></header>';
    $html .= '<div class="zpIndustryPlan__layout"><ol class="zpIndustryPlan__list" role="list">';
    foreach ($spec['items'] as $index => [$title, $body]) {
        $html .= '<li><span class="zpIndustryPlan__number" aria-hidden="true">' . sprintf('%02d', $index + 1) . '</span><div><h3>' . esc_html($title) . '</h3><p>' . esc_html($body) . '</p></div></li>';
    }
    $html .= '</ol><aside class="zpIndustryPlan__example" aria-labelledby="zpIndustryPlanExampleTitle"><p class="zpIndustryPlan__label">' . ($english ? 'A visitor’s journey' : 'Ścieżka użytkownika') . '</p><h3 id="zpIndustryPlanExampleTitle">' . esc_html($spec['scenario_title']) . '</h3><ol class="zpIndustryPlan__steps" role="list">';
    foreach ($spec['scenario'] as $step) { $html .= '<li>' . esc_html($step) . '</li>'; }
    $html .= '</ol><p class="zpIndustryPlan__materials">' . esc_html($spec['materials']) . '</p><p class="zpIndustryPlan__note">' . ($english ? 'This is an example plan. The final structure and features depend on your services and the agreed scope.' : 'To przykładowy plan. Ostateczną strukturę i funkcje dopasujemy do Twojej oferty oraz ustalonego zakresu.') . '</p>';
    if ($has_work) { $html .= '<a class="zpIndustryPlan__work" href="#zpIndustryWork">' . esc_html($work[$key][$english ? 1 : 0]) . '</a>'; }
    $html .= '<a class="zpIndustryPlan__cta" href="' . esc_url(zp_ci_url_20261017('/studio-wyceny/?zpbs_service=web#zpbsUltimate', $english)) . '">' . esc_html($cta[$key][$english ? 1 : 0]) . '<span aria-hidden="true">↗</span></a></aside></div>';
    $html .= '<footer class="zpIndustryPlan__reading"><p>' . ($english ? 'Explore the website plan in more detail:' : 'Chcesz lepiej przygotować zakres strony?') . '</p><div><a href="' . esc_url(zp_ci_url_20261017('/strony-internetowe/strona-internetowa-dla-firmy-co-powinna-zawierac/', $english)) . '">' . ($english ? 'What a company website should include' : 'Co powinna zawierać strona firmowa') . '<span aria-hidden="true">↗</span></a><a href="' . esc_url(zp_ci_url_20261017('/strony-internetowe/jak-stworzyc-strone-internetowa/', $english)) . '">' . ($english ? 'How to create a website, step by step' : 'Jak stworzyć stronę internetową krok po kroku') . '<span aria-hidden="true">↗</span></a></div></footer></div></section>';
    return $html;
}

function zp_ci_industry_document_20261017(string $html, string $source, bool $english): string {
    if (!function_exists('zp_seo_industry_key') || !function_exists('zp_coverage_section_20261015')) { return $html; }
    $key = zp_seo_industry_key($source);
    if ($key === '' || zp_coverage_section_20261015($html, 'zpIndustryPlan')) { return $html; }
    // Place the planning example beside real project work, before the existing long guide.
    $work = zp_coverage_section_20261015($html, 'zpIndustryWork');
    $anchor = $work ?: zp_coverage_section_20261015($html, 'faq');
    if (!$anchor) { return $html; }
    $markup = zp_ci_industry_markup_20261017($key, $english, (bool) $work);
    if ($markup === '') { return $html; }
    $at = $work ? $anchor[1] : $anchor[0];
    return substr($html, 0, $at) . $markup . substr($html, $at);
}

add_filter('zp_service_widgets_document', static function (string $html): string {
    if (!function_exists('zp_seo_plan_active') || !zp_seo_plan_active()) { return $html; }
    return zp_ci_industry_document_20261017($html, zp_ci_source_20261017(), function_exists('zp_seo_plan_is_en') && zp_seo_plan_is_en());
}, 8);

add_filter('body_class', static function ($classes) {
    $classes = is_array($classes) ? $classes : [];
    if (function_exists('zp_seo_plan_active') && zp_seo_plan_active() && function_exists('zp_seo_industry_key') && zp_seo_industry_key(zp_ci_source_20261017()) !== '') { $classes[] = 'zp-industry-polish'; }
    return array_values(array_unique($classes));
});

add_action('wp_enqueue_scripts', static function (): void {
    $source = zp_ci_source_20261017();
    $industry = function_exists('zp_seo_plan_active') && zp_seo_plan_active() && function_exists('zp_seo_industry_key') && zp_seo_industry_key($source) !== '';
    if ($source !== '/kontakt/' && !$industry) { return; }
    wp_enqueue_style('zp-contact-industry-polish-20261017', ZP_SUITE_URL . 'assets/css/contact-industry-polish-20261017.css', [], ZP_SUITE_VERSION);
}, 10005);
