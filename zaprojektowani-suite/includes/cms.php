<?php
if (!defined('ABSPATH')) { exit; }

function zp_suite_cms_defaults(){
  return [
    'portfolio' => [
      'web' => [
    [
      'brand'=>'Apartament Piękna',
      'sub'=>'nowa strona, rezerwacje online, branding i SEO',
      'type'=>'Strona + Rezerwacje + SEO',
      'year'=>'2026',
      'img'=>'https://zaprojektowani.com/wp-content/uploads/2026/05/apartamentpiekna_strona-scaled.webp',
      'tag'=>'Beauty premium / medycyna estetyczna',
      'desc'=>'Dla marki Apartament Piękna z Tarnowa przygotowaliśmy kompleksowy redesign wizerunku online — od odświeżenia identyfikacji wizualnej, przez pełny projekt strony internetowej, aż po autorski system rezerwacji wizyt. Projekt połączył jasną, elegancką estetykę beauty premium z funkcjonalnością, lokalnym SEO, blogiem, analityką i optymalizacją konwersji.',
      'client'=>'Apartament Piękna — Tarnów',
      'services'=>'Redesign strony, branding, WordPress, rezerwacje online, blog SEO, analityka',
      'live'=>'https://www.apartamentpiekna.pl/',
      'meta'=>'rezerwacje online|SEO lokalne|premium beauty',
      'scope'=>'Całkowity redesign strony apartamentpiekna.pl|Odświeżenie logo i oprawy wizualnej marki|Projekt graficzny premium beauty|Autorski system rezerwacji wizyt online|Powiadomienia SMS dla klientów|Responsywna wersja mobilna|Autorski system bloga branżowego|Struktura SEO pod frazy lokalne|Konfiguracja Google Search Console, GA4 i Meta Pixel',
      'tools'=>'',
      'mode'=>'web'
    ],
    [
      'brand'=>'Gravia',
      'sub'=>'strona dla biura projektów infrastrukturalnych',
      'type'=>'Strona firmowa + Kariera',
      'year'=>'2026',
      'img'=>'https://zaprojektowani.com/wp-content/uploads/2026/05/gravia-scaled.webp',
      'tag'=>'Infrastruktura / branża drogowa',
      'desc'=>'Dla firmy Gravia — Biuro Projektów Infrastrukturalnych przygotowaliśmy nowoczesną, techniczną i w pełni funkcjonalną stronę internetową dopasowaną do branży drogowej oraz infrastrukturalnej. Szczególnie ważnym elementem był autorski system rekrutacyjny z możliwością dodawania stanowisk pracy.',
      'client'=>'Gravia — Biuro Projektów Infrastrukturalnych',
      'services'=>'Web Design, WordPress Dev, UX, System kariery',
      'live'=>'https://gravia.pl/',
      'meta'=>'techniczny UI|kariera|realizacje',
      'scope'=>'Indywidualny projekt graficzny strony|Nowoczesny układ pod branżę infrastrukturalną|Wersja desktop i mobile|Podstrony ofertowe i informacyjne|Formularze kontaktowe|Rozbudowana sekcja realizacji|Autorski system kariery z obsługą CV i plików',
      'tools'=>'',
      'mode'=>'web'
    ],
    [
      'brand'=>'ShotHome',
      'sub'=>'strona usługowa + SEO + darmowa wycena online',
      'type'=>'Strona + SEO + darmowa wycena',
      'year'=>'2026',
      'img'=>'https://zaprojektowani.com/wp-content/uploads/2026/05/shothome_realizacja-2_11zon.webp',
      'tag'=>'Drzwi, podłogi i wykończenia / Warszawa',
      'desc'=>'Dla ShotHome stworzyliśmy od podstaw stronę usługową, która skraca drogę od przeglądania oferty do wysłania zapytania. Projekt objął indywidualny UI, wdrożenie WordPress, strukturę SEO pod Warszawę, blog, customową darmową wycenę online, system realizacji oraz katalog produktów podłogowych z możliwością dodawania i edycji w panelu.',
      'client'=>'ShotHome',
      'services'=>'Projekt graficzny, WordPress, SEO, blog, custom wycena online, system realizacji, katalog produktów',
      'live'=>'https://www.shothome.pl/',
      'meta'=>'darmowa wycena|SEO Warszawa|panel realizacji|katalog podłóg',
      'scope'=>'Projekt strony od podstaw|Indywidualny projekt graficzny w kolorystyce marki|Wdrożenie WordPress|Customowy formularz darmowej wyceny online|Panel realizacji: dodawanie, edytowanie i usuwanie projektów|Katalog produktów podłogowych z obsługą w panelu|Blog i struktura treści pod SEO|Optymalizacja lokalna pod Warszawę|Responsywny widok mobile',
      'tools'=>'WordPress|PHP|SEO|UX/UI|Blog|Custom CMS',
      'mode'=>'web'
    ],
    [
      'brand'=>'Krawiec z dojazdem',
      'sub'=>'rebranding, nowa strona i system wizyt',
      'type'=>'System + Rebranding',
      'year'=>'2026',
      'img'=>'https://zaprojektowani.com/wp-content/uploads/2026/01/krawiec_strona.webp',
      'tag'=>'Usługi premium / lokalnie',
      'desc'=>'Kompleksowy projekt obejmujący rebranding marki, zaprojektowanie nowej strony internetowej oraz wdrożenie autorskiego systemu umawiania wizyt z dojazdem do klienta. Elegancka typografia, złote akcenty i ciemna kolorystyka oddają premium charakter usługi.',
      'client'=>'Krawiec z dojazdem',
      'services'=>'Branding, Web Design, WordPress Dev, SEO',
      'live'=>'https://krawieczdojazdem.pl/',
      'meta'=>'premium UI|mobile-first|SEO-ready',
      'scope'=>'Rebranding identyfikacji wizualnej|Strona WordPress od zera|System umawiania wizyt online|Optymalizacja SEO lokalne|Integracja Google Analytics|Design mobile-first',
      'tools'=>'',
      'mode'=>'web'
    ],
    [
      'brand'=>'Papeterio',
      'sub'=>'subtelny sklep online z papeterią i personalizacją',
      'type'=>'Sklep WooCommerce',
      'year'=>'2026',
      'img'=>'https://zaprojektowani.com/wp-content/uploads/2026/05/papeterio-scaled.webp',
      'tag'=>'Papeteria / e-commerce',
      'desc'=>'Dla marki Papeterio stworzyliśmy subtelny, elegancki sklep online z papeterią i dodatkami na wyjątkowe okazje. Projekt miał oddać delikatny charakter marki, ale jednocześnie działać jak wygodny, nowoczesny e-commerce.',
      'client'=>'Papeterio',
      'services'=>'Web Design, WooCommerce, UX, Payments',
      'live'=>'https://papeterio.pl/',
      'meta'=>'WooCommerce|personalizacja|mobile UX',
      'scope'=>'Sklep internetowy z kategoriami produktów|Dopracowane karty produktów|Opcje personalizacji zamówienia|Warianty i dodatki|Koszyk i płatności online|Formularze kontaktowe|Wersja mobilna dopasowana do zakupów z telefonu',
      'tools'=>'',
      'mode'=>'web'
    ],
    [
      'brand'=>'Bransoletka24',
      'sub'=>'sklep online z hurtem i detalem',
      'type'=>'Sklep WooCommerce',
      'year'=>'2026',
      'img'=>'https://zaprojektowani.com/wp-content/uploads/2026/01/bransoletka24_strona.webp',
      'tag'=>'E-commerce / detal + hurt',
      'desc'=>'Sklep WooCommerce zaprojektowany od zera z myślą o konwersji zarówno dla klientów detalicznych, jak i hurtowych. Prosty checkout, przejrzyste kategorie, szybki filtr produktów, wishlist i responsywny design.',
      'client'=>'Bransoletka24',
      'services'=>'Web Design, WooCommerce, Payments, UX',
      'live'=>'https://bransoletka24.pl/',
      'meta'=>'WooCommerce|checkout|B2B + B2C',
      'scope'=>'Sklep WooCommerce od zera|System hurtowy z rejestracją B2B|Integracja płatności online|Wishlist i warianty produktów|Animacje GSAP|Optymalizacja konwersji',
      'tools'=>'',
      'mode'=>'web'
    ],
    [
      'brand'=>'RUTPOŻ',
      'sub'=>'strona, system zgłoszeń i asystent AI',
      'type'=>'System + AI',
      'year'=>'2025',
      'img'=>'https://zaprojektowani.com/wp-content/uploads/2026/01/rutpoz_strona.jpg',
      'tag'=>'PPOŻ / system zgłoszeń + AI',
      'desc'=>'Projekt łączący nowoczesną stronę z autorskim systemem zgłoszeń serwisowych, kalkulatorami wydajności hydrantów i asystentem AI AIPKIT, który odpowiada na pytania klientów 24/7. System automatyzuje obsługę i generuje raporty PDF.',
      'client'=>'RUTPOŻ',
      'services'=>'Web, System zgłoszeń, AI Chatbot, Kalkulatory',
      'live'=>'https://rutpoz.pl/',
      'meta'=>'AI chatbot|kalkulatory|B2B',
      'scope'=>'Strona firmowa WordPress|Asystent AI 24/7|Kalkulator wydajności hydrantów|Kalkulator gęstości ogniowej|Generowanie raportów PDF|System zgłoszeń serwisowych',
      'tools'=>'',
      'mode'=>'web'
    ],
    [
      'brand'=>'Druga Szansa',
      'sub'=>'strona sieci sklepów z sekcją franczyzy',
      'type'=>'Sieć / Franczyza',
      'year'=>'2025',
      'img'=>'https://zaprojektowani.com/wp-content/uploads/2025/09/drugaszansa_web-scaled.webp',
      'tag'=>'Sieć sklepów / franczyza',
      'desc'=>'Strona dla sieci sklepów z odzieżą Druga Szansa. Mapa punktów sprzedaży, sekcja franczyzy z formularzem dla potencjalnych partnerów, system kodów rabatowych i przejrzysta prezentacja oferty.',
      'client'=>'Druga Szansa',
      'services'=>'Web Design, Lead Gen, Mapa lokalizacji',
      'live'=>'https://drugaszansa.net/',
      'meta'=>'lead gen|franczyza|mapa punktów',
      'scope'=>'Strona sieci sklepów|Sekcja franczyzy z formularzem|Mapa punktów sprzedaży|System kodów rabatowych|Integracja WooCommerce|Kampanie leadowe',
      'tools'=>'',
      'mode'=>'web'
    ],
    [
      'brand'=>'Inusti',
      'sub'=>'strona B2B nastawiona na szybkie zapytania',
      'type'=>'Strona B2B',
      'year'=>'2025',
      'img'=>'https://zaprojektowani.com/wp-content/uploads/2025/09/inusti-1.webp',
      'tag'=>'Firma budowlana / B2B',
      'desc'=>'Strona B2B dla firmy budowlanej — czytelna struktura, szybki formularz kontaktowy, sekcja realizacji i optymalizacja pod Google Ads. Projekt skupiony na generowaniu zapytań ofertowych.',
      'client'=>'Inusti',
      'services'=>'Web Design, WordPress Dev, SEO',
      'live'=>'',
      'meta'=>'B2B|lead flow|SEO-ready',
      'scope'=>'Strona firmowa B2B|Formularz szybkiego kontaktu|Sekcja realizacji z galeriami|Optymalizacja pod Google Ads|Struktura SEO on-site|Design mobile-first',
      'tools'=>'',
      'mode'=>'web'
    ],
    [
      'brand'=>'Piekarnia Marysia',
      'sub'=>'strona lokalna z ofertą i kierunkiem pod rozwój',
      'type'=>'Strona lokalna',
      'year'=>'2025',
      'img'=>'https://zaprojektowani.com/wp-content/uploads/2025/09/marysia-.webp',
      'tag'=>'Piekarnia / gastronomia',
      'desc'=>'Minimalistyczna strona dla piekarni — prezentacja menu, galeria produktów, dane kontaktowe z integracją Google Maps. Zoptymalizowana pod SEO lokalne.',
      'client'=>'Piekarnia Marysia',
      'services'=>'Web Design, SEO lokalne, Fotografia',
      'live'=>'',
      'meta'=>'local SEO|oferta|szybki UX',
      'scope'=>'Strona wizytówka piekarni|Prezentacja menu i produktów|Galeria z fotografią produktową|Integracja Google Maps|Optymalizacja SEO lokalne|Responsywny design',
      'tools'=>'',
      'mode'=>'web'
    ]
  ],
      'logo' => [
    [
      'brand'=>'Siemianowski',
      'sub'=>'logo i branding kancelarii premium',
      'type'=>'Logo + branding premium',
      'year'=>'2026',
      'img'=>'https://zaprojektowani.com/wp-content/uploads/2026/05/siemianowski.webp',
      'tag'=>'Kancelaria / Restrukturyzacja',
      'desc'=>'Dla Kancelarii Adwokata i Doradcy Restrukturyzacyjnego Arkadiusza Siemianowskiego przygotowaliśmy spójny kierunek logo i brandingu premium. Projekt został oparty na eleganckiej typografii, stonowanej kolorystyce oraz znaku, który dobrze działa w kontekście kancelarii, dokumentów, wizytówek, strony internetowej i materiałów firmowych.',
      'client'=>'Siemianowski',
      'services'=>'Logo, Branding, Mockupy, Materiały firmowe',
      'live'=>'',
      'meta'=>'Premium|Kancelaria|Elegancja',
      'scope'=>'Projekt logo z wariantami|Kolorystyka i typografia|Mockupy i wizualizacje|Materiały do druku|Identyfikacja kancelarii|Wytyczne do strony WWW',
      'tools'=>'Illustrator|Photoshop|Figma|Mockupy|Druk',
      'mode'=>'logo'
    ],
    [
      'brand'=>'POLERSTONE',
      'sub'=>'nowoczesne logo dla usług kamieniarskich',
      'type'=>'Logo + identyfikacja',
      'year'=>'2026',
      'img'=>'https://zaprojektowani.com/wp-content/uploads/2026/05/polerstone.webp',
      'tag'=>'Kamieniarstwo premium',
      'desc'=>'Dla marki POLERSTONE — Usługi Kamieniarskie przygotowaliśmy elegancki i nowoczesny projekt logo, który podkreśla solidność, precyzję i charakter branży premium. Projekt opiera się na minimalistycznym monogramie „PS” z dynamicznym przecięciem.',
      'client'=>'POLERSTONE',
      'services'=>'Logo, Sygnet, Wersje kolorystyczne, Mockupy',
      'live'=>'',
      'meta'=>'Monogram|Premium|Kamień',
      'scope'=>'Logo marki POLERSTONE|Sygnet / monogram PS|Wersje kolorystyczne|Mockupy prezentacyjne|Wizualizacje na kamieniu|Materiały firmowe',
      'tools'=>'Illustrator|Photoshop|Figma|Mockupy|Druk',
      'mode'=>'logo'
    ],
    [
      'brand'=>'Pracownia Urody',
      'sub'=>'delikatny branding beauty dla Joanny Samborskiej',
      'type'=>'Logo + identyfikacja',
      'year'=>'2026',
      'img'=>'https://zaprojektowani.com/wp-content/uploads/2026/05/pracownia_urody.webp',
      'tag'=>'Beauty / Kosmetologia',
      'desc'=>'Dla Pracowni Urody Joanna Samborska przygotowaliśmy delikatny, kobiecy i elegancki projekt logo, który podkreśla spokojny, profesjonalny i estetyczny charakter gabinetu. Projekt opiera się na lekkiej, minimalistycznej linii, która łączy kobiecy profil, miękkie kształty i roślinny detal.',
      'client'=>'Pracownia Urody',
      'services'=>'Logo, Sygnet, Wersje kolorystyczne, Mockupy',
      'live'=>'',
      'meta'=>'Beauty|Subtelnie|Kobieco',
      'scope'=>'Logo marki|Subtelny sygnet z profilem|Wersje kolorystyczne|Mockupy prezentacyjne|Wizualizacje na opakowaniach|Materiały firmowe i social media',
      'tools'=>'Illustrator|Photoshop|Figma|Mockupy|Druk',
      'mode'=>'logo'
    ],
    [
      'brand'=>'Arcycięcie',
      'sub'=>'charakterystyczny branding studia fryzur',
      'type'=>'Logo + identyfikacja',
      'year'=>'2025',
      'img'=>'https://zaprojektowani.com/wp-content/uploads/2025/11/arcyciecie.webp',
      'tag'=>'Barber / Fryzjerstwo',
      'desc'=>'Kompleksowy projekt brandingowy dla studia fryzur Arcycięcie. Logo z wyraźnym detalem brzytwy i nożyczek oddaje charakter barbershopu, a elegancka typografia dodaje autorskiego stylu.',
      'client'=>'Arcycięcie',
      'services'=>'Logo, Branding, Materiały salonu, Social media',
      'live'=>'',
      'meta'=>'Logo premium|Materiały salonu|Branding',
      'scope'=>'Projekt logo z wariantami|Kolorystyka i typografia|Mockupy i wizualizacje|Materiały do druku|Elementy oznakowania salonu|Szablony social media',
      'tools'=>'Illustrator|Photoshop|Figma|Mockupy|Druk',
      'mode'=>'logo'
    ],
    [
      'brand'=>'Vista Architekci',
      'sub'=>'minimalistyczny monogram dla biura projektowego',
      'type'=>'Logo + identyfikacja',
      'year'=>'2025',
      'img'=>'https://zaprojektowani.com/wp-content/uploads/2025/11/vista.webp',
      'tag'=>'Architekci',
      'desc'=>'Monogram VA zaprojektowany z myślą o biurze architektonicznym. Geometryczna forma nawiązuje do przekrojów budowlanych i gry światła. System identyfikacji obejmuje brandbook, papeterię firmową i szablony ofertowe.',
      'client'=>'Vista Architekci',
      'services'=>'Logo, Monogram, Brandbook, Papeteria',
      'live'=>'',
      'meta'=>'Minimalizm|Architektura|Brandbook',
      'scope'=>'Sygnet monogramowy VA|Brandbook z wytycznymi|Papeteria firmowa|Szablony ofertowe|Wizytówki i teczki|Materiały do prezentacji',
      'tools'=>'Illustrator|Figma|InDesign|Mockupy|Brandbook',
      'mode'=>'logo'
    ],
    [
      'brand'=>'Akoya',
      'sub'=>'gabinet kosmetyczny premium',
      'type'=>'Logo + identyfikacja',
      'year'=>'2025',
      'img'=>'https://zaprojektowani.com/wp-content/uploads/2025/11/Screenshot-2025-11-12-at-19.55.10.webp',
      'tag'=>'Beauty & Wellness',
      'desc'=>'Identyfikacja wizualna gabinetu kosmetycznego Akoya. Beżowo-złota paleta i miękka typografia tworzą poczucie luksusu i spokoju. Projekt obejmuje logo, mini brandbook, wizytówki, karty zabiegowe i szablony do social mediów.',
      'client'=>'Akoya',
      'services'=>'Logo, Mini brandbook, Karty zabiegowe, Social media',
      'live'=>'',
      'meta'=>'Beauty|Mini brandbook|Premium',
      'scope'=>'Logo z wariantami kolorowymi|Mini brandbook|Wizytówki i karty zabiegowe|Szablony social media|Paleta kolorów i fonty|Mockupy i wizualizacje',
      'tools'=>'Illustrator|Photoshop|Figma|Canva|Druk',
      'mode'=>'logo'
    ],
    [
      'brand'=>'Eat Tasty',
      'sub'=>'apetyczny branding dowozu jedzenia',
      'type'=>'Logo + identyfikacja',
      'year'=>'2025',
      'img'=>'https://zaprojektowani.com/wp-content/uploads/2025/11/eattasty.webp',
      'tag'=>'Gastro / Delivery',
      'desc'=>'Branding marki delivery Eat Tasty z dynamicznym symbolem łączącym talerz i strzałkę dostawy. System identyfikacji zaprojektowany pod boksy, opakowania, naklejki i materiały do social mediów.',
      'client'=>'Eat Tasty',
      'services'=>'Logo, Opakowania, Naklejki, Social media',
      'live'=>'',
      'meta'=>'Gastro|Opakowania|Social media',
      'scope'=>'Logo z symbolem delivery|Projekty opakowań i boksów|Naklejki i etykiety|Materiały social media|System kolorystyczny|Mockupy na opakowaniach',
      'tools'=>'Illustrator|Photoshop|Mockupy|Druk',
      'mode'=>'logo'
    ],
    [
      'brand'=>'Fryz Dobry Barber',
      'sub'=>'męski branding barbershopu',
      'type'=>'Logo + identyfikacja',
      'year'=>'2025',
      'img'=>'https://zaprojektowani.com/wp-content/uploads/2025/11/Screenshot-2025-11-12-at-19.56.35.webp',
      'tag'=>'Barber / Men’s',
      'desc'=>'Kompletny branding barbershopu z brodatym sygnetem w stylu vintage. Szeryfowa typografia, detale brzytwy i nożyczek tworzą spójny system marki.',
      'client'=>'Fryz Dobry Barber',
      'services'=>'Logo, Sygnet, Branding wnętrza, Komunikacja',
      'live'=>'',
      'meta'=>'Barbershop|Vintage|Materiały wnętrza',
      'scope'=>'Sygnet z brodatym motywem|Typografia vintage|Materiały do wnętrza|Wizytówki i karty|Szyld i oznakowanie|System identyfikacji',
      'tools'=>'Illustrator|Photoshop|InDesign|Mockupy|Druk',
      'mode'=>'logo'
    ],
    [
      'brand'=>'Piotr Mazur',
      'sub'=>'monogram PM dla kancelarii adwokackiej',
      'type'=>'Logo + identyfikacja',
      'year'=>'2025',
      'img'=>'https://zaprojektowani.com/wp-content/uploads/2025/11/mazur.webp',
      'tag'=>'Kancelaria',
      'desc'=>'Elegancki monogram PM dla kancelarii adwokackiej w formie stylizowanej tarczy. Ciemna paleta z granatami i złotem buduje powagę i profesjonalizm.',
      'client'=>'Piotr Mazur',
      'services'=>'Logo, Monogram, Papeteria, Tablice',
      'live'=>'',
      'meta'=>'Kancelaria|Monogram|Premium',
      'scope'=>'Monogram PM w formie tarczy|Papeteria firmowa|Tablice kancelarii|Wizytówki premium|Wytyczne do strony WWW|Brandbook',
      'tools'=>'Illustrator|Figma|InDesign|Mockupy|Druk|Brandbook',
      'mode'=>'logo'
    ],
    [
      'brand'=>'Okruszek',
      'sub'=>'pastelowe logo pracowni cukierniczej',
      'type'=>'Logo + identyfikacja',
      'year'=>'2025',
      'img'=>'https://zaprojektowani.com/wp-content/uploads/2025/11/Screenshot-2025-11-12-at-20.06.17.webp',
      'tag'=>'Cukiernia / Słodkości',
      'desc'=>'Pastelowa identyfikacja pracowni cukierniczej Okruszek z symbolem tortu. Lekka, ciepła kolorystyka pasuje do charakteru lokalnej cukierni.',
      'client'=>'Okruszek',
      'services'=>'Logo, Opakowania, Etykiety, Witryna',
      'live'=>'',
      'meta'=>'Cukiernia|Pastel|Opakowania',
      'scope'=>'Logo z symbolem tortu|Projekty pudełek i opakowań|Etykiety na produkty|Oznakowanie witryny|Paleta pastelowa|Mockupy na pudełkach',
      'tools'=>'Illustrator|Photoshop|Mockupy|Druk',
      'mode'=>'logo'
    ],
    [
      'brand'=>'Mona Cake',
      'sub'=>'branding pracowni tortów artystycznych',
      'type'=>'Logo + identyfikacja',
      'year'=>'2025',
      'img'=>'https://zaprojektowani.com/wp-content/uploads/2025/11/mona.webp',
      'tag'=>'Cukiernia / Torty',
      'desc'=>'Bajkowy branding pracowni tortów artystycznych Mona Cake z symbolem babeczki. Pastelowe kolory i elegancki logotyp tworzą kobiecy, lekki charakter.',
      'client'=>'Mona Cake',
      'services'=>'Logo, Sygnet, Opakowania, Social media',
      'live'=>'',
      'meta'=>'Torty|Pastel|Social media',
      'scope'=>'Logo z babeczką i sygnet|Projekty pudełek na torty|Wizytówki i etykiety|Szablony Instagram|System kolorystyczny|Mockupy',
      'tools'=>'Illustrator|Photoshop|Canva|Mockupy|Druk',
      'mode'=>'logo'
    ],
    [
      'brand'=>'Wonder Handmade',
      'sub'=>'eleganckie logo dla marki handmade',
      'type'=>'Logo + identyfikacja',
      'year'=>'2025',
      'img'=>'https://zaprojektowani.com/wp-content/uploads/2025/11/Screenshot-2025-11-12-at-19.55.56.webp',
      'tag'=>'Handmade / Rękodzieło',
      'desc'=>'Minimalistyczny branding marki handmade z biżuteryjnym sygnetem. Delikatna forma i neutralna paleta podkreślają butikowy charakter.',
      'client'=>'Wonder Handmade',
      'services'=>'Logo, Brandbook, Etykiety, Opakowania',
      'live'=>'',
      'meta'=>'Handmade|Etykiety|Brandbook',
      'scope'=>'Sygnet biżuteryjny|Brandbook z wytycznymi|Etykiety na produkty|Projekty opakowań|Wizytówki i papeteria|Szablony social media',
      'tools'=>'Illustrator|Figma|Photoshop|Mockupy|Brandbook',
      'mode'=>'logo'
    ],
    [
      'brand'=>'Sfera',
      'sub'=>'naturalny branding studia well-being',
      'type'=>'Logo + identyfikacja',
      'year'=>'2025',
      'img'=>'https://zaprojektowani.com/wp-content/uploads/2025/11/Screenshot-2025-11-12-at-22.35.41.webp',
      'tag'=>'Well-being / Holistycznie',
      'desc'=>'Branding studia well-being Sfera z organicznym symbolem i stonowaną paletą beży i zieleni. Projekt oddaje spokój i naturę, tworząc atmosferę zaufania.',
      'client'=>'Sfera',
      'services'=>'Logo, Wizytówki, Karty klienta, Komunikacja',
      'live'=>'',
      'meta'=>'Well-being|Naturalne kolory|Minimalizm',
      'scope'=>'Logo z organicznym symbolem|Paleta beży i zieleni|Wizytówki i karty klienta|Szablony komunikacji|Mockupy i wizualizacje|Materiały gabinetowe',
      'tools'=>'Illustrator|Figma|Canva|Mockupy',
      'mode'=>'logo'
    ],
    [
      'brand'=>'Looksus',
      'sub'=>'monogramowe logo studia beauty',
      'type'=>'Logo + identyfikacja',
      'year'=>'2025',
      'img'=>'https://zaprojektowani.com/wp-content/uploads/2025/11/Screenshot-2025-11-12-at-22.36.00.webp',
      'tag'=>'Beauty studio',
      'desc'=>'Monogramowy branding studia beauty Looksus w ciepłej palecie nude. Delikatne formy otaczające monogram budują premium odbiór.',
      'client'=>'Looksus',
      'services'=>'Logo, Monogram, Karty, Wystrój studia',
      'live'=>'',
      'meta'=>'Beauty|Monogram|Nude palette',
      'scope'=>'Monogram w delikatnych formach|Paleta nude i typografia|Wizytówki premium|Karty klienta|Elementy wystroju studia|Szablony social media',
      'tools'=>'Illustrator|Photoshop|Figma|Mockupy|Druk',
      'mode'=>'logo'
    ],
    [
      'brand'=>'Show Party',
      'sub'=>'selfie mirror — wariant 1',
      'type'=>'Logo + identyfikacja',
      'year'=>'2025',
      'img'=>'https://zaprojektowani.com/wp-content/uploads/2025/11/Screenshot-2025-11-12-at-19.57.04.webp',
      'tag'=>'Event / Selfie mirror',
      'desc'=>'Nowoczesny branding firmy eventowej Show Party. Logo łączy elegancję z klimatem imprezy — światło, blask i rozrywka.',
      'client'=>'Show Party',
      'services'=>'Logo, Branding eventowy, Social media, Mockupy',
      'live'=>'',
      'meta'=>'Event|Selfie mirror|Mockupy',
      'scope'=>'Logo z efektem światła|Branding pod eventy|Materiały promocyjne|Szablony social media|Mockupy selfie mirror|Materiały drukowane',
      'tools'=>'Illustrator|Photoshop|After Effects|Mockupy',
      'mode'=>'logo'
    ],
    [
      'brand'=>'Impulse Tanning',
      'sub'=>'energetyczny branding studia opalania',
      'type'=>'Logo + identyfikacja',
      'year'=>'2025',
      'img'=>'https://zaprojektowani.com/wp-content/uploads/2025/11/Screenshot-2025-11-12-at-22.38.00.webp',
      'tag'=>'Studio opalania',
      'desc'=>'Energetyczny branding studia opalania Impulse Tanning z gradientem glow. Nowoczesna typografia i wakacyjna estetyka wyróżniają markę na tle konkurencji.',
      'client'=>'Impulse Tanning',
      'services'=>'Logo, Gradient branding, Karty, Witryna',
      'live'=>'',
      'meta'=>'Glow|Gradient|Studio',
      'scope'=>'Logo z efektem glow|Gradient i typografia|Karty klienta|Oznakowanie witryny|Materiały promocyjne|Mockupy',
      'tools'=>'Illustrator|Photoshop|Figma|Mockupy|Druk',
      'mode'=>'logo'
    ],
    [
      'brand'=>'Medical Friend',
      'sub'=>'przyjazny brand medyczny',
      'type'=>'Logo + identyfikacja',
      'year'=>'2025',
      'img'=>'https://zaprojektowani.com/wp-content/uploads/2025/11/Screenshot-2025-11-13-at-01.40.38.webp',
      'tag'=>'Medycyna',
      'desc'=>'Branding medyczny Medical Friend z symbolem serca i uśmiechu. Chłodna, profesjonalna paleta bieli, szarości i czerwieni buduje zaufanie bez dystansu.',
      'client'=>'Medical Friend',
      'services'=>'Logo, Oznakowanie, Materiały gabinetu, Papeteria',
      'live'=>'',
      'meta'=>'Medycyna|Zaufanie|System',
      'scope'=>'Logo z symbolem serca|Paleta medyczna|Oznakowanie gabinetów|Papeteria firmowa|Materiały informacyjne|Szablony do strony',
      'tools'=>'Illustrator|Figma|InDesign|Mockupy|Druk',
      'mode'=>'logo'
    ],
    [
      'brand'=>'Janda',
      'sub'=>'subtelny luksus w stylistyce spa',
      'type'=>'Logo + identyfikacja',
      'year'=>'2025',
      'img'=>'https://zaprojektowani.com/wp-content/uploads/2025/11/Screenshot-2025-11-13-at-01.36.41.webp',
      'tag'=>'Health & Beauty',
      'desc'=>'Luksusowy branding spa Janda z delikatną ilustracją kobiety otoczonej kwiatami. Paleta beży, złamanej bieli i złota tworzy poczucie premium i relaksu.',
      'client'=>'Janda',
      'services'=>'Logo, Ilustracja, Menu zabiegów, Social media',
      'live'=>'',
      'meta'=>'Spa|Luxury|Branding',
      'scope'=>'Logo z ilustracją kwiatową|Paleta luxury|Menu zabiegów|Wizytówki premium|Szablony social media|Mockupy i wizualizacje',
      'tools'=>'Illustrator|Photoshop|Procreate|Mockupy|Druk',
      'mode'=>'logo'
    ],
    [
      'brand'=>'Ralf Furniture',
      'sub'=>'branding marki meblarskiej',
      'type'=>'Logo + identyfikacja',
      'year'=>'2025',
      'img'=>'https://zaprojektowani.com/wp-content/uploads/2025/11/Screenshot-2025-11-12-at-22.37.24.webp',
      'tag'=>'Meble na wymiar',
      'desc'=>'Minimalistyczny branding marki meblarskiej Ralf Furniture z sygnetem w palecie czerni, złota i drewna. Identyfikacja premium dla mebli na wymiar.',
      'client'=>'Ralf Furniture',
      'services'=>'Logo, Sygnet, Katalog, B2B materiały',
      'live'=>'',
      'meta'=>'Meble|B2B|Premium',
      'scope'=>'Sygnet minimalistyczny|Paleta czerni i drewna|Katalog produktowy|Papeteria firmowa|Oznakowanie showroomu|Materiały B2B',
      'tools'=>'Illustrator|Figma|InDesign|Photoshop|Mockupy|Druk',
      'mode'=>'logo'
    ],
    [
      'brand'=>'EVA Aesthetics',
      'sub'=>'luksusowy branding kliniki',
      'type'=>'Logo + identyfikacja',
      'year'=>'2025',
      'img'=>'https://zaprojektowani.com/wp-content/uploads/2025/11/Screenshot-2025-11-16-at-20.57.05.webp',
      'tag'=>'Medycyna estetyczna',
      'desc'=>'Luksusowy branding kliniki medycyny estetycznej EVA Aesthetics. Minimalistyczny logotyp w beżach i złocie podkreśla spokój i profesjonalizm.',
      'client'=>'EVA Aesthetics',
      'services'=>'Logo, Oznakowanie kliniki, Karty, Komunikacja',
      'live'=>'',
      'meta'=>'Klinika|Nude & gold|Luxury',
      'scope'=>'Logotyp minimalistyczny|Paleta nude & gold|Oznakowanie kliniki|Karty zabiegowe|Wizytówki premium|Brandbook z wytycznymi',
      'tools'=>'Illustrator|Figma|Photoshop|InDesign|Mockupy|Brandbook',
      'mode'=>'logo'
    ]
  ]
    ],
    'reviews' => [
    [
      'name'=>'Magdalena Kasińska',
      'source'=>'Google',
      'rating'=>'5.0',
      'date'=>'',
      'text'=>'Najbardziej doceniam komunikację. Zawsze wiadomo, na jakim etapie jest projekt, co jest po naszej stronie, a co po ich. Żadnego „znikania” na tygodnie.',
      'avatar'=>'MK',
      'featured'=>'1'
    ],
    [
      'name'=>'Aneta Waszkiewicz',
      'source'=>'Google',
      'rating'=>'5.0',
      'date'=>'2 miesiące temu',
      'text'=>'Profesjonalne podejście, świetny kontakt i nowoczesny projekt strony www. Wszystko wykonane sprawnie i zgodnie z ustaleniami. Efekt końcowy naprawdę robi wrażenie!',
      'avatar'=>'AW',
      'featured'=>'0'
    ],
    [
      'name'=>'Dana Studniarek',
      'source'=>'Google',
      'rating'=>'5.0',
      'date'=>'4 miesiące temu',
      'text'=>'Największy plus: proces. Od ustaleń po finalne wdrożenie wszystko idzie krok po kroku i wiadomo, na jakim etapie jesteśmy. Zero chaosu.',
      'avatar'=>'DS',
      'featured'=>'0'
    ],
    [
      'name'=>'Krzysztof Motylski',
      'source'=>'Google',
      'rating'=>'5.0',
      'date'=>'4 miesiące temu',
      'text'=>'Polecam z całego serca! Zrobili nam nową stronę gabinetu i odświeżyli logo. Wszystko spójne, eleganckie i w końcu na poziomie tego, co robimy na co dzień.',
      'avatar'=>'KM',
      'featured'=>'0'
    ],
    [
      'name'=>'Sylwia Gasentzer',
      'source'=>'Google',
      'rating'=>'5.0',
      'date'=>'',
      'text'=>'Jestem bardzo zadowolona ze współpracy. Potrzebowałam prostej, ale ładnej strony pod mój salon kosmetyczny.',
      'avatar'=>'SG',
      'featured'=>'0'
    ],
    [
      'name'=>'Violetta Majcher',
      'source'=>'Google',
      'rating'=>'5.0',
      'date'=>'',
      'text'=>'Zaprojektowani uratowali mój projekt logo po niemiłych doświadczeniach z inną firmą. Ogromne zrozumienie i elastyczność.',
      'avatar'=>'VM',
      'featured'=>'0'
    ],
    [
      'name'=>'Marcelina Wachowicz',
      'source'=>'Google',
      'rating'=>'5.0',
      'date'=>'',
      'text'=>'Przygotowali dla nas stronę firmową w dwóch językach oraz zestaw grafik do kampanii. Regularne podsumowania prac.',
      'avatar'=>'MW',
      'featured'=>'0'
    ],
    [
      'name'=>'Jarosław Pawlikowski',
      'source'=>'Google',
      'rating'=>'5.0',
      'date'=>'',
      'text'=>'Szybkie poprawki na stronie i dopięcie SEO technicznego. Widać, że dbają o detale: wydajność, mobile i strukturę.',
      'avatar'=>'JP',
      'featured'=>'0'
    ],
    [
      'name'=>'Aleksandra Siwik',
      'source'=>'Google',
      'rating'=>'5.0',
      'date'=>'',
      'text'=>'Kampanie Facebook Ads przyniosły wymierne efekty. Cenimy transparentność działań oraz jasną i konkretną komunikację.',
      'avatar'=>'AS',
      'featured'=>'0'
    ],
    [
      'name'=>'Niewczas Karol',
      'source'=>'Google',
      'rating'=>'5.0',
      'date'=>'',
      'text'=>'Świetnie zrealizowany projekt — strona szybka, dobrze się pozycjonujemy. Polecam tę ekipę!',
      'avatar'=>'NK',
      'featured'=>'0'
    ],
    [
      'name'=>'Adrianna Malinowska',
      'source'=>'Google',
      'rating'=>'5.0',
      'date'=>'',
      'text'=>'Projekt strony www przerósł moje oczekiwania. Kontakt też super, jestem zadowolona.',
      'avatar'=>'AM',
      'featured'=>'0'
    ],
    [
      'name'=>'Ania Forma',
      'source'=>'Facebook',
      'rating'=>'5.0',
      'date'=>'',
      'text'=>'Świetny kontakt, ładny design i szybkie poprawki. Strona wyszła lepiej niż zakładałam.',
      'avatar'=>'AF',
      'featured'=>'0'
    ],
    [
      'name'=>'Anna Lubańska',
      'source'=>'Facebook',
      'rating'=>'5.0',
      'date'=>'',
      'text'=>'Logo i strona zrobione w jednej estetyce. W końcu marka wygląda spójnie, a nie jak z trzech różnych miejsc.',
      'avatar'=>'AL',
      'featured'=>'0'
    ],
    [
      'name'=>'Michał Żołnierowicz',
      'source'=>'Facebook',
      'rating'=>'5.0',
      'date'=>'',
      'text'=>'Sklep wygląda świetnie, a panel edycji jest prosty. Po wdrożeniu dostałam krótkie szkolenie i instrukcję.',
      'avatar'=>'MŻ',
      'featured'=>'0'
    ],
    [
      'name'=>'Krzysztof Domaradzki',
      'source'=>'Facebook',
      'rating'=>'5.0',
      'date'=>'',
      'text'=>'SEO techniczne dopięte od startu: struktura, szybkość i podstawy pod widoczność. Konkretne rekomendacje co dalej.',
      'avatar'=>'KD',
      'featured'=>'0'
    ],
    [
      'name'=>'Dajana Anna',
      'source'=>'Facebook',
      'rating'=>'5.0',
      'date'=>'',
      'text'=>'Ogarnęli Pixel + CAPI, zdarzenia i raportowanie. Dane w końcu mają sens i można optymalizować reklamy.',
      'avatar'=>'DA',
      'featured'=>'0'
    ],
    [
      'name'=>'Bartek Lubera',
      'source'=>'Facebook',
      'rating'=>'5.0',
      'date'=>'',
      'text'=>'Największy plus: estetyka i spójność. Strona wygląda jak marka, a nie jak gotowiec.',
      'avatar'=>'BL',
      'featured'=>'0'
    ]
  ],
    'logos' => [
    [
      'name'=>'Apartament Piękna',
      'image'=>'https://zaprojektowani.com/wp-content/uploads/2026/05/ap.webp',
      'url'=>'',
      'visible'=>'1'
    ],
    [
      'name'=>'Eat Tasty',
      'image'=>'https://zaprojektowani.com/wp-content/uploads/2026/05/eat_tasty.webp',
      'url'=>'',
      'visible'=>'1'
    ],
    [
      'name'=>'Gravia',
      'image'=>'https://zaprojektowani.com/wp-content/uploads/2026/05/gravia.webp',
      'url'=>'',
      'visible'=>'1'
    ],
    [
      'name'=>'Kamiński',
      'image'=>'https://zaprojektowani.com/wp-content/uploads/2026/05/kaminski.webp',
      'url'=>'',
      'visible'=>'1'
    ],
    [
      'name'=>'Piotr Mazur',
      'image'=>'https://zaprojektowani.com/wp-content/uploads/2026/05/piotr_mazur.webp',
      'url'=>'',
      'visible'=>'1'
    ],
    [
      'name'=>'Polerstone',
      'image'=>'https://zaprojektowani.com/wp-content/uploads/2026/05/polerstone.webp',
      'url'=>'',
      'visible'=>'1'
    ],
    [
      'name'=>'Prisma Dent',
      'image'=>'https://zaprojektowani.com/wp-content/uploads/2026/05/prisma_dent.webp',
      'url'=>'',
      'visible'=>'1'
    ],
    [
      'name'=>'ProScarves',
      'image'=>'https://zaprojektowani.com/wp-content/uploads/2026/05/proscarves.webp',
      'url'=>'',
      'visible'=>'1'
    ],
    [
      'name'=>'Raxo',
      'image'=>'https://zaprojektowani.com/wp-content/uploads/2026/05/raxo.webp',
      'url'=>'',
      'visible'=>'1'
    ],
    [
      'name'=>'Sfera',
      'image'=>'https://zaprojektowani.com/wp-content/uploads/2026/05/sfera-1.webp',
      'url'=>'',
      'visible'=>'1'
    ],
    [
      'name'=>'Siemianowski',
      'image'=>'https://zaprojektowani.com/wp-content/uploads/2026/05/siemianowski-1.webp',
      'url'=>'',
      'visible'=>'1'
    ],
    [
      'name'=>'Vista',
      'image'=>'https://zaprojektowani.com/wp-content/uploads/2026/05/vista.webp',
      'url'=>'',
      'visible'=>'1'
    ]
  ],
    'faq' => [
    [
      'q'=>'Ile kosztuje strona internetowa dla firmy?',
      'a'=>'Koszt strony internetowej zależy od zakresu: liczby podstron, projektu graficznego, treści, funkcji, SEO, formularzy i integracji. Po krótkim briefie dobieramy zakres do celu biznesowego.'
    ],
    [
      'q'=>'Czy projektujecie strony internetowe w Katowicach?',
      'a'=>'Tak. Projektujemy strony internetowe dla firm z Katowic, Śląska i całej Polski. Możemy spotkać się lokalnie lub pracować zdalnie.'
    ],
    [
      'q'=>'Czy tworzycie sklepy WooCommerce?',
      'a'=>'Tak. Budujemy sklepy internetowe WooCommerce z produktami, koszykiem, płatnościami, dostawami, analityką i układem gotowym pod sprzedaż oraz kampanie reklamowe.'
    ],
    [
      'q'=>'Czy można zamówić logo, branding i stronę w jednym pakiecie?',
      'a'=>'Tak. Najlepsze efekty daje połączenie logo, identyfikacji wizualnej, strony internetowej, SEO i kampanii w jeden spójny system marki.'
    ],
    [
      'q'=>'Czy prowadzicie kampanie Meta Ads?',
      'a'=>'Tak. Przygotowujemy kampanie na Facebooku i Instagramie, kreacje, Pixel/CAPI, analitykę i testy.'
    ],
    [
      'q'=>'Dla jakich branż tworzycie strony internetowe?',
      'a'=>'Najczęściej pracujemy dla firm usługowych, kancelarii, deweloperów, salonów beauty, lekarzy, producentów, sklepów internetowych i marek premium.'
    ]
  ],
    'industries' => [
    [
      'title'=>'Strony internetowe dla kancelarii',
      'url'=>'/strony-internetowe-dla-kancelarii/',
      'icon'=>'scale',
      'num'=>'01',
      'text'=>'Specjalizacje opisane językiem klienta, szybka ścieżka kontaktu, sekcje zaufania i treści, które pomagają podjąć decyzję przed pierwszą rozmową.',
      'image'=>'https://zaprojektowani.com/wp-content/uploads/2026/05/laptop_widget-scaled.webp'
    ],
    [
      'title'=>'Strony internetowe dla deweloperów',
      'url'=>'/strony-internetowe-dla-deweloperow/',
      'icon'=>'building-2',
      'num'=>'02',
      'text'=>'Układ pod inwestycje, lokalizacje, standard wykończenia, galerie, karty lokali i formularze zapytań, które nie gubią użytkownika na mobile.',
      'image'=>'https://zaprojektowani.com/wp-content/uploads/2026/05/laptop_zaprojektowani.webp'
    ],
    [
      'title'=>'Strony internetowe dla lekarzy',
      'url'=>'/strony-internetowe-dla-lekarzy/',
      'icon'=>'stethoscope',
      'num'=>'03',
      'text'=>'Rzeczowa informacja o usługach i zespole, umawianie wizyt, dane gabinetu i lokalne SEO — bez cech reklamy, zgodnie z zasadami informowania o świadczeniach.',
      'image'=>'https://zaprojektowani.com/wp-content/uploads/2026/05/laptop_widget-scaled.webp'
    ],
    [
      'title'=>'Sklep internetowy dla producenta',
      'url'=>'/sklep-internetowy-dla-producenta/',
      'icon'=>'factory',
      'num'=>'04',
      'text'=>'WooCommerce, katalog produktów, koszyk, płatności, formularze B2B i struktura gotowa pod reklamy oraz dalszy rozwój sprzedaży.',
      'image'=>'https://zaprojektowani.com/wp-content/uploads/2026/05/e-commerce-scaled.webp'
    ]
  ]
  ];
}

function zp_suite_cms(){
  $saved = get_option('zp_suite_cms', []);
  if(!is_array($saved)) $saved = [];
  return zp_suite_deep_merge(zp_suite_cms_defaults(), $saved);
}
function zp_suite_cms_get($key, $default=[]){
  $data=zp_suite_cms();
  foreach(explode('.', $key) as $p){
    if(!is_array($data) || !array_key_exists($p,$data)) return $default;
    $data=$data[$p];
  }
  return $data;
}
function zp_suite_split_pipes($s){ return array_values(array_filter(array_map('trim', explode('|', (string)$s)), 'strlen')); }
/**
 * v1.2.0 content restore:
 * Przy zmianie wersji przywraca komplet projektów, opinii i logotypów z wersji legacy,
 * żeby front i CMS startowały z pełnym, poprawnym zestawem danych.
 */
function zp_suite_maybe_upgrade_content_120(){
  $stored = get_option('zp_suite_content_version', '');
  if ($stored === '1.2.0') {
    return;
  }
  $defaults = zp_suite_cms_defaults();
  $current = zp_suite_cms();

  // Przywracamy pełne listy contentowe, ale zostawiamy FAQ/branże jeśli były edytowane i nie są puste.
  $current['portfolio'] = $defaults['portfolio'];
  $current['reviews'] = $defaults['reviews'];
  $current['logos'] = $defaults['logos'];

  if (empty($current['faq'])) {
    $current['faq'] = $defaults['faq'];
  }
  if (empty($current['industries'])) {
    $current['industries'] = $defaults['industries'];
  }

  update_option('zp_suite_cms', $current, false);
  update_option('zp_suite_content_version', '1.2.0', false);
}
add_action('plugins_loaded', 'zp_suite_maybe_upgrade_content_120', 20);

/**
 * v2.2.55 — trust logos: Polerstone + Siemianowski jako pierwsze logo w sekwencji.
 * Nie resetuje całego CMS, tylko aktualizuje listę logo klientów.
 */
function zp_suite_maybe_upgrade_trust_logos_2280(){
  $stored = get_option('zp_suite_trust_logos_version', '');
  if ($stored === '2.2.81') {
    return;
  }

  $current = get_option('zp_suite_cms', []);
  if (!is_array($current)) {
    $current = [];
  }

  // v2.2.81 — nowe logotypy klientów pod hero: pełne pliki WEBP 2026/05, bez starych miniaturek 300x300.
  $current['logos'] = [
    ['name'=>'Apartament Piękna','image'=>'https://zaprojektowani.com/wp-content/uploads/2026/05/ap.webp','url'=>'','visible'=>'1'],
    ['name'=>'Eat Tasty','image'=>'https://zaprojektowani.com/wp-content/uploads/2026/05/eat_tasty.webp','url'=>'','visible'=>'1'],
    ['name'=>'Gravia','image'=>'https://zaprojektowani.com/wp-content/uploads/2026/05/gravia.webp','url'=>'','visible'=>'1'],
    ['name'=>'Kamiński','image'=>'https://zaprojektowani.com/wp-content/uploads/2026/05/kaminski.webp','url'=>'','visible'=>'1'],
    ['name'=>'Piotr Mazur','image'=>'https://zaprojektowani.com/wp-content/uploads/2026/05/piotr_mazur.webp','url'=>'','visible'=>'1'],
    ['name'=>'Polerstone','image'=>'https://zaprojektowani.com/wp-content/uploads/2026/05/polerstone.webp','url'=>'','visible'=>'1'],
    ['name'=>'Prisma Dent','image'=>'https://zaprojektowani.com/wp-content/uploads/2026/05/prisma_dent.webp','url'=>'','visible'=>'1'],
    ['name'=>'ProScarves','image'=>'https://zaprojektowani.com/wp-content/uploads/2026/05/proscarves.webp','url'=>'','visible'=>'1'],
    ['name'=>'Raxo','image'=>'https://zaprojektowani.com/wp-content/uploads/2026/05/raxo.webp','url'=>'','visible'=>'1'],
    ['name'=>'Sfera','image'=>'https://zaprojektowani.com/wp-content/uploads/2026/05/sfera-1.webp','url'=>'','visible'=>'1'],
    ['name'=>'Siemianowski','image'=>'https://zaprojektowani.com/wp-content/uploads/2026/05/siemianowski-1.webp','url'=>'','visible'=>'1'],
    ['name'=>'Vista','image'=>'https://zaprojektowani.com/wp-content/uploads/2026/05/vista.webp','url'=>'','visible'=>'1'],
  ];

  update_option('zp_suite_cms', $current, false);
  update_option('zp_suite_trust_logos_version', '2.2.81', false);
}
add_action('plugins_loaded', 'zp_suite_maybe_upgrade_trust_logos_2280', 24);



if (!function_exists('zp_suite_full_upload_image_url')) {
  /**
   * ZP Suite v2.2.60 — portfolio image guard.
   * WordPress Media Library sometimes stores a generated thumbnail URL in CMS fields
   * (e.g. image-150x150.webp). For portfolio cards we always want the original file,
   * especially logo/branding cards on mobile.
   */
  function zp_suite_full_upload_image_url($url) {
    $url = (string) $url;
    if ($url === '' || strpos($url, '/wp-content/uploads/') === false) {
      return $url;
    }
    return preg_replace('/-\d+x\d+(\.(?:webp|avif|png|jpe?g|gif))(\?.*)?$/i', '$1$2', $url);
  }
}

function zp_suite_portfolio_content_key($value){
  $value = trim((string)$value);
  if (function_exists('mb_strtolower')) {
    return mb_strtolower($value, 'UTF-8');
  }
  // Safe fallback for hosting environments without mbstring.
  $value = strtr($value, [
    'Ą'=>'ą','Ć'=>'ć','Ę'=>'ę','Ł'=>'ł','Ń'=>'ń','Ó'=>'ó','Ś'=>'ś','Ź'=>'ź','Ż'=>'ż'
  ]);
  return strtolower($value);
}

/**
 * v2.2.699 — homepage portfolio uses the approved descriptions and case-study
 * fields from the /realizacje/ section. Image, live URL and year stay controlled
 * by the homepage portfolio CMS; editorial fields come from Realizacje.
 */
function zp_suite_home_portfolio_realizacje_overlay($item, $mode='web'){
  if (!is_array($item) || empty($item['brand']) || !empty($item['skip_realizacje_overlay']) || !function_exists('zp_suite_realizacje_v19_content')) {
    return $item;
  }

  $brand = zp_suite_portfolio_content_key($item['brand']);
  $mode = $mode === 'logo' ? 'logo' : 'web';
  $content = zp_suite_realizacje_v19_content();
  $selected = null;
  $fallback = null;

  foreach ((array)$content as $candidate_key => $candidate) {
    $parts = explode('|', (string)$candidate_key, 3);
    if (count($parts) < 2 || zp_suite_portfolio_content_key($parts[0]) !== $brand) {
      continue;
    }
    if ($fallback === null && is_array($candidate)) {
      $fallback = $candidate;
    }
    $category = zp_suite_portfolio_content_key($parts[1]);
    if (($mode === 'logo' && $category === 'branding') || ($mode === 'web' && $category !== 'branding')) {
      $selected = $candidate;
      break;
    }
  }

  $overlay = is_array($selected) ? $selected : $fallback;
  if (!is_array($overlay)) {
    return $item;
  }

  $fields = ['sub','type','tag','desc','client','services','scope','results','challenge','solution','effect'];
  foreach ($fields as $field) {
    if (array_key_exists($field, $overlay) && $overlay[$field] !== '' && $overlay[$field] !== []) {
      $item[$field] = $overlay[$field];
    }
  }
  return $item;
}

function zp_suite_portfolio_list_value($value){
  if (is_array($value)) {
    return array_values(array_filter(array_map(function($entry){
      return trim((string)$entry);
    }, $value), 'strlen'));
  }
  return zp_suite_split_pipes($value);
}

function zp_suite_portfolio_services_value($value){
  if (is_array($value)) {
    return implode(', ', zp_suite_portfolio_list_value($value));
  }
  return trim((string)$value);
}

function zp_suite_portfolio_results_value($value){
  if (!is_array($value)) return [];
  $out = [];
  foreach ($value as $row) {
    if (!is_array($row)) continue;
    $title = trim((string)($row[0] ?? ''));
    $text = trim((string)($row[1] ?? ''));
    if ($title === '' && $text === '') continue;
    $out[] = [$title, $text];
  }
  return $out;
}

function zp_suite_portfolio_items($mode='web'){
  $items = zp_suite_cms_get('portfolio.'.$mode, []);

  /* v2.1.16 — home portfolio: Polerstone, Świat Grilli, ProScarves i Siemianowski jako pozycje priorytetowe, bez duplikowania wpisów z opcji CMS. */
  if ($mode === 'web') {
    $priority = [
      [
        'brand'=>'OutTech',
        'sub'=>'nowa strona firmowa + oferta B2B/B2C + formularze serwisowe',
        'type'=>'Strona firmowa / technika zabezpieczeń',
        'year'=>'2026',
        'img'=>'https://zaprojektowani.com/wp-content/uploads/2026/09/outtech_realizacja_compressed-scaled.webp',
        'tag'=>'Systemy bezpieczeństwa / B2B + smart home',
        'desc'=>'Dla OutTech Technika Zabezpieczeń zaprojektowaliśmy nową stronę, która porządkuje rozbudowaną ofertę systemów bezpieczeństwa i pokazuje firmę jako partnera inżynieryjnego, a nie tylko instalatora. Serwis rozdziela rozwiązania dla biznesu i klientów indywidualnych, prowadzi przez alarmy, CCTV, kontrolę dostępu, automatykę i smart home oraz obsługuje zapytania ofertowe i zgłoszenia serwisowe.',
        'client'=>'OutTech Technika Zabezpieczeń',
        'services'=>'UX/UI, WordPress, architektura informacji, formularz ofertowy, formularz serwisowy, SEO, RWD',
        'live'=>'https://www.outtech.pl/',
        'meta'=>'WordPress|systemy bezpieczeństwa|formularz serwisowy|SEO',
        'scope'=>'Nowa architektura informacji i uporządkowanie rozbudowanej oferty|Strona główna, O firmie, Realizacje, Blog / aktualności, FAQ i Kontakt|Sekcja Dla biznesu z osobnymi obszarami bezpieczeństwa elektronicznego, nadzoru, ruchu osobowego, automatyzacji i ciągłości działania|Sekcja Inteligentna i bezpieczna przestrzeń dla klientów indywidualnych|Zakres usług: projektowanie, dobór rozwiązań, know-how, integracje, serwis i rozbudowa|Sekcja Jak pracujemy prowadząca od rozpoznania potrzeb do opieki nad instalacją|Rozbudowany formularz ofertowy|Formularz serwisowy z kategorią awarii, typem zgłoszenia, priorytetem i załącznikami|Responsywny projekt desktop, tablet i mobile|Struktura treści przygotowana pod widoczność lokalną w Google na Śląsku i Opolszczyźnie',
        'tools'=>'WordPress|UX/UI|SEO|Custom Forms|RWD',
        'results'=>[
          ['Czytelna architektura','Oferta B2B, B2C i zakres usług są rozdzielone w logiczne ścieżki.'],
          ['Obsługa serwisu','Dedykowany formularz porządkuje zgłoszenia, priorytety i załączniki.'],
          ['Ekspercki wizerunek','Serwis podkreśla doświadczenie, know-how i kompleksowe podejście do bezpieczeństwa.']
        ],
        'challenge'=>'Dotychczasowa strona była zbyt ogólna i słabo porządkowała szeroką ofertę OutTech. Wyzwaniem było czytelne rozdzielenie usług dla biznesu i domu, pokazanie zakresu obsługi od projektu po serwis oraz skrócenie drogi do zgłoszenia usterki lub zapytania ofertowego.',
        'solution'=>'Zaprojektowaliśmy nową architekturę informacji z rozbudowanym menu, osobnymi ścieżkami dla biznesu i klientów indywidualnych oraz sekcją zakresu usług. Całość uzupełniliśmy ekspercką, ale zrozumiałą komunikacją, formularzem ofertowym i formularzem serwisowym z obsługą priorytetów oraz załączników.',
        'effect'=>'Powstał nowoczesny serwis inżynieryjny, który lepiej komunikuje kompetencje OutTech, porządkuje ofertę systemów bezpieczeństwa i ułatwia klientom zarówno wybór rozwiązania, jak i kontakt z działem serwisu. Struktura stanowi też bazę pod dalszy rozwój treści i lokalnego SEO.',
        'mode'=>'web',
        'skip_realizacje_overlay'=>'1'
      ],
      [
        'brand'=>'Polerstone',
        'sub'=>'branding + nowa strona firmowa premium',
        'type'=>'Branding + strona firmowa',
        'year'=>'2026',
        'img'=>'https://zaprojektowani.com/wp-content/uploads/2026/08/polerstone_realizacja-scaled.webp',
        'tag'=>'Kamieniarstwo premium / wnętrza i architektura',
        'desc'=>'Dla Polerstone przygotowaliśmy kompletny kierunek marki i nową stronę internetową, która pokazuje kamień jako element nowoczesnej architektury, a nie tylko materiał wykończeniowy. Projekt połączył branding, identyfikację wizualną, materiały firmowe i rozbudowany serwis prezentujący ofertę, materiały, realizacje, showroom oraz cały proces współpracy — od wyboru płyty po profesjonalny montaż.',
        'client'=>'Polerstone — Usługi Kamieniarskie',
        'services'=>'Branding, logo, identyfikacja wizualna, UX/UI, WordPress, RWD, struktura SEO',
        'live'=>'',
        'meta'=>'branding|WordPress|premium UI|kamień naturalny',
        'scope'=>'Logo i spójny system identyfikacji wizualnej|Papier firmowy, wizytówki, teczki i stopki mailowe|Indywidualny projekt UX/UI nowej strony|Prezentacja blatów kuchennych, łazienek, schodów, parapetów, kominków, tarasów i elewacji|Rozbudowana sekcja materiałów: granit, marmur, kwarcyt, onyks, konglomeraty i spieki|Galeria realizacji i prezentacja showroomu|Sekcja procesu od konsultacji i pomiaru po obróbkę oraz montaż|Formularze kontaktowe i ścieżka umówienia wyceny|Responsywny widok desktop, tablet i mobile|Przygotowanie struktury pod dalszy rozwój SEO',
        'tools'=>'Figma|WordPress|Elementor|Adobe Illustrator|Adobe Photoshop|SEO',
        'results'=>[
          ['Spójna marka','Logo, materiały firmowe i strona tworzą jeden konsekwentny system wizualny.'],
          ['Oferta premium','Kamień, realizacje i proces są prezentowane w architektoniczny, czytelny sposób.'],
          ['Gotowe do rozwoju','Serwis ma fundament pod kolejne realizacje, treści SEO i działania reklamowe.']
        ],
        'challenge'=>'Połączyć szeroki zakres usług kamieniarskich i bogactwo naturalnych materiałów w jeden premium wizerunek, który będzie czytelny zarówno dla klienta indywidualnego, jak i architekta czy inwestora.',
        'solution'=>'Zbudowaliśmy spokojny, architektoniczny system oparty na dużych fotografiach kamienia, czarno-grafitowej typografii, mocnym detalu i przejrzystej strukturze. Branding został przeniesiony na stronę, materiały firmowe i wszystkie najważniejsze punkty kontaktu z marką.',
        'effect'=>'Polerstone otrzymało spójny wizerunek premium i nowoczesny serwis, który porządkuje ofertę, podkreśla jakość wykonania oraz prowadzi użytkownika od inspiracji i wyboru materiału do kontaktu i wyceny.',
        'mode'=>'web',
        'skip_realizacje_overlay'=>'1'
      ],
      [
        'brand'=>'Świat Grilli',
        'sub'=>'branding + sklep WooCommerce od podstaw',
        'type'=>'Sklep internetowy + branding',
        'year'=>'2026',
        'img'=>'https://zaprojektowani.com/wp-content/uploads/2026/08/swiatgrili_realizacja.webp',
        'tag'=>'Grille / e-commerce multibrand',
        'desc'=>'Dla ŚwiatGrilli.pl stworzyliśmy od podstaw kompletną markę i sklep internetowy dla branży grillowej. Od logo i brandingu, przez projekt UX/UI i strukturę kategorii, po WooCommerce, karty produktów, koszyk, checkout oraz płatności iMoje / ING — całość została zaprojektowana jako spójny system sprzedaży dla wielu marek i szerokiego katalogu produktów.',
        'client'=>'Świat Grilli',
        'services'=>'Logo, branding, UX/UI, WooCommerce, płatności iMoje / ING, checkout, RWD, SEO',
        'live'=>'https://www.swiatgrili.pl/',
        'meta'=>'WooCommerce|iMoje / ING|multi-brand|RWD',
        'scope'=>'Logo i branding marki Świat Grilli|Indywidualny projekt sklepu internetowego|Struktura kategorii dla grilli, wędzarni, akcesoriów, kominków i outletu|Prezentacja marek El Fuego, Landmann i Barbecook|Karty produktów i czytelna prezentacja parametrów|Konfiguracja WooCommerce, koszyka i pełnego checkoutu|Integracja bramki płatniczej iMoje / ING|Sekcje budujące zaufanie oraz prezentacja sklepu stacjonarnego|Regulaminy, polityka prywatności i mechanizm cookies|Responsywny widok desktop, tablet i mobile|Przygotowanie struktury sklepu pod SEO i kampanie reklamowe',
        'tools'=>'WordPress|WooCommerce|Elementor|iMoje / ING|UX/UI|SEO',
        'results'=>[
          ['E-commerce od zera','Branding, UX/UI i WooCommerce zostały połączone w jednym wdrożeniu.'],
          ['Pełna sprzedaż online','Koszyk, checkout i płatności iMoje / ING tworzą kompletną ścieżkę zakupową.'],
          ['Sklep multi-brand','Oferta wielu producentów jest uporządkowana w czytelnej strukturze kategorii i produktów.']
        ],
        'challenge'=>'Stworzyć od podstaw nową markę e-commerce i uporządkować szeroką ofertę grilli, wędzarni, akcesoriów oraz różnych producentów tak, aby zakup był prosty zarówno dla początkującego, jak i bardziej świadomego klienta.',
        'solution'=>'Zaprojektowaliśmy nowoczesny, produktowy sklep z wyraźnym podziałem kategorii, osobną ekspozycją kluczowych marek, rozbudowanymi kartami produktów i prostą drogą do finalizacji zamówienia. Całość uzupełniliśmy brandingiem, WooCommerce oraz płatnościami online.',
        'effect'=>'Powstał kompletny sklep internetowy gotowy do skalowania oferty, prowadzenia kampanii i rozwijania sprzedaży wielu marek w jednym spójnym środowisku.',
        'mode'=>'web',
        'skip_realizacje_overlay'=>'1'
      ],
      [
        'brand'=>'ProScarves',
        'sub'=>'E-commerce + zamówienia hurtowe',
        'type'=>'B2B custom / sklep sportowy',
        'year'=>'2026',
        'img'=>'https://zaprojektowani.com/wp-content/uploads/2026/05/proscarves_realizacja_nowa_compressed.webp',
        'tag'=>'B2B custom / sklep sportowy',
        'desc'=>'Dla ProScarves projektujemy sklep pod dwa scenariusze sprzedaży: gotowe produkty dla fanów oraz zapytania B2B dla klubów, fan clubów i organizacji. Struktura łączy detaliczny e-commerce, customowy system wyceny zamówień hurtowych, konfigurację zapytania, prezentację produkcji customowej i własny design dopasowany do rynku sportowego.',
        'client'=>'ProScarves',
        'services'=>'UX/UI, WooCommerce, sklep detaliczny, system wyceny B2B, formularze custom orders, wdrożenie WordPress',
        'live'=>'https://www.proscarves.com/',
        'meta'=>'WooCommerce|B2B custom|fan clubs|system wyceny',
        'scope'=>'Indywidualny projekt graficzny sklepu|Sklep detaliczny z gotowymi produktami dla fanów|Customowy system wyceny dla klubów i fan clubów|Ścieżka zapytań B2B dla klubów, firm i organizacji|Prezentacja produkcji customowej|Struktura pod rynki międzynarodowe|Responsywna wersja mobile|Podstawy SEO, analityka i przygotowanie pod kampanie',
        'tools'=>'',
        'mode'=>'web'
      ],
      [
        'brand'=>'Siemianowski',
        'sub'=>'Strona firmowa + branding + wizyty',
        'type'=>'Kancelaria premium / prawo i restrukturyzacja',
        'year'=>'2026',
        'img'=>'https://zaprojektowani.com/wp-content/uploads/2026/05/siemianowski_realizacja_nowa_compressed.webp',
        'tag'=>'Kancelaria premium / prawo i restrukturyzacja',
        'desc'=>'Dla kancelarii przygotowaliśmy elegancki system wizerunkowy i rozbudowaną stronę, która ma budować zaufanie jeszcze przed pierwszym telefonem. Struktura prowadzi użytkownika przez specjalizacje, doświadczenie, zakres pomocy, kontakt i umawianie wizyt — w spokojnym, eksperckim stylu premium.',
        'client'=>'Kancelaria Adwokata i Doradcy Restrukturyzacyjnego Arkadiusz Siemianowski',
        'services'=>'Branding, projekt strony, WordPress, UX, copywriting, formularze kontaktowe, umawianie wizyt',
        'live'=>'https://siemianowski.pl/',
        'meta'=>'branding|kancelaria|premium UI|wizyty',
        'scope'=>'Kierunek wizualny marki premium|Logo i system identyfikacji|Projekt rozbudowanej strony kancelarii|Struktura specjalizacji prawnych i restrukturyzacyjnych|Sekcje zaufania, doświadczenia i kontaktu|Elementy umawiania wizyt|Responsywny widok mobile|Przygotowanie pod treści eksperckie i SEO',
        'tools'=>'',
        'mode'=>'web'
      ],
    ];
    $items = array_values(array_filter((array)$items, function($it){
      $brand = strtolower(trim((string)($it['brand'] ?? '')));
      return !in_array($brand, ['outtech','proscarves','siemianowski','polerstone','Świat grilli','świat grilli','swiat grilli'], true);
    }));
    $items = array_merge($priority, $items);
  }


  /* v2.2.781 — nowe realizacje brandingowe na stronie głównej: Natalia Arciszewska i Zgórecki Nieruchomości.
     Są dokładane priorytetowo także wtedy, gdy istniejąca instalacja ma starszą zawartość zapisaną w opcji CMS.
     Treść jest generowana od razu w PL albo EN, dzięki czemu działa także w dynamicznym popupie portfolio. */
  if ($mode === 'logo') {
    $is_en = function_exists('zpl_language') && zpl_language() === 'en';

    $priority_logo_pl = [
      [
        'brand'=>'Natalia Arciszewska',
        'sub'=>'logo i identyfikacja wizualna radcy prawnego',
        'type'=>'Logo + mini branding',
        'year'=>'2026',
        'img'=>'https://zaprojektowani.com/wp-content/uploads/2026/09/arciszewska.webp',
        'tag'=>'Kancelaria / Radca prawny',
        'desc'=>'Dla Natalii Arciszewskiej przygotowaliśmy elegancką, nowoczesną identyfikację dla kancelarii radcy prawnego. Punktem wyjścia było 10 kierunków logo; finalny znak dopracowaliśmy w granatowo-beżowej palecie ze złotym akcentem, tak aby marka budowała zaufanie i wyglądała premium zarówno online, jak i w druku.',
        'client'=>'Natalia Arciszewska — Radca prawny',
        'services'=>'Logo, mini branding, papeteria, wizytówki, stopka e-mail, mockupy',
        'live'=>'',
        'meta'=>'Prawo|Granat + beż|Premium',
        'scope'=>'10 propozycji logo|Dopracowanie wybranego znaku i typografii|Paleta granatowo-beżowa ze złotym akcentem|Wizytówki z kodem QR i LinkedIn|Papier firmowy i teczka z kieszonką|Stopka e-mail i materiały firmowe|Mockupy i wizualizacje zastosowań',
        'tools'=>'Illustrator|Photoshop|Figma|InDesign|Mockupy|Druk',
        'results'=>[
          ['Spójny system','Logo, kolorystyka i typografia tworzą konsekwentny, profesjonalny wizerunek kancelarii.'],
          ['Gotowe materiały','Identyfikację przełożyliśmy na wizytówki, papier firmowy, teczkę i stopkę e-mail.'],
          ['Premium bez przesady','Granat, beż i subtelne złoto budują zaufanie bez typowej, ciężkiej estetyki prawniczej.']
        ],
        'challenge'=>'Stworzyć markę prawniczą, która jest profesjonalna i premium, ale nie wygląda ciężko ani schematycznie.',
        'solution'=>'Przygotowaliśmy 10 kierunków logo, a następnie dopracowaliśmy wybraną koncepcję, typografię i paletę granatowo-beżową ze złotym akcentem. System rozszerzyliśmy na najważniejsze materiały firmowe.',
        'effect'=>'Powstała spójna identyfikacja, która dobrze działa na dokumentach, wizytówkach, materiałach cyfrowych i jako baza pod stronę internetową.',
        'mode'=>'logo',
        'skip_realizacje_overlay'=>'1'
      ],
      [
        'brand'=>'Zgórecki Nieruchomości',
        'sub'=>'logo i mini branding marki nieruchomości',
        'type'=>'Logo + mini branding',
        'year'=>'2026',
        'img'=>'https://zaprojektowani.com/wp-content/uploads/2026/09/zgorecki.webp',
        'tag'=>'Nieruchomości / Marka osobista',
        'desc'=>'Dla marki Wojciech Zgórecki Nieruchomości stworzyliśmy elegancki system logo i mini brandingu łączący osobisty charakter marki z estetyką segmentu premium. Projekt powstał na bazie 10 kierunków, a wybrany znak dopracowaliśmy pod kątem proporcji, grubości formy, złotych wariantów kolorystycznych i praktycznego użycia w materiałach sprzedażowych.',
        'client'=>'Wojciech Zgórecki Nieruchomości',
        'services'=>'Logo, mini branding, warianty kolorystyczne, mockupy, materiały marki',
        'live'=>'',
        'meta'=>'Nieruchomości|Złoto|Premium',
        'scope'=>'10 propozycji logo|Dopracowanie proporcji i grubości znaku|Złote warianty kolorystyczne|Typografia marki|Zestaw wersji logo do druku i internetu|Mockupy nieruchomościowe i materiały prezentacyjne',
        'tools'=>'Illustrator|Photoshop|Figma|Mockupy|Druk',
        'results'=>[
          ['Rozpoznawalny znak','Dopracowany symbol i typografia tworzą wyrazisty podpis marki osobistej.'],
          ['Elastyczne warianty','Przygotowane wersje kolorystyczne działają w druku, online i na materiałach ofertowych.'],
          ['Wizerunek premium','Złote akcenty i spokojna kompozycja wspierają eleganckie pozycjonowanie marki nieruchomości.']
        ],
        'challenge'=>'Połączyć osobisty charakter marki pośrednika z estetyką premium i zachować czytelność znaku także w małej skali.',
        'solution'=>'Na bazie 10 kierunków wybraliśmy i dopracowaliśmy znak, korygując proporcje, grubość formy, odcień złota i typografię. Następnie przygotowaliśmy zestaw wariantów oraz mockupy pokazujące markę w praktycznych zastosowaniach.',
        'effect'=>'Marka otrzymała elegancki, elastyczny system logo gotowy do ofert nieruchomości, social mediów, materiałów drukowanych i przyszłej komunikacji online.',
        'mode'=>'logo',
        'skip_realizacje_overlay'=>'1'
      ]
    ];

    $priority_logo_en = [
      [
        'brand'=>'Natalia Arciszewska',
        'sub'=>'logo and visual identity for a legal counsel',
        'type'=>'Logo + mini branding',
        'year'=>'2026',
        'img'=>'https://zaprojektowani.com/wp-content/uploads/2026/09/arciszewska.webp',
        'tag'=>'Legal services / Legal counsel',
        'desc'=>'For Natalia Arciszewska we created an elegant, modern visual identity for a legal practice. We started with 10 logo directions and refined the selected concept into a navy-and-beige system with a subtle gold accent, designed to build trust and feel premium both online and in print.',
        'client'=>'Natalia Arciszewska — Legal counsel',
        'services'=>'Logo, mini branding, stationery, business cards, email signature, mockups',
        'live'=>'',
        'meta'=>'Legal|Navy + beige|Premium',
        'scope'=>'10 logo concepts|Refinement of the selected mark and typography|Navy-and-beige palette with a gold accent|Business cards with QR code and LinkedIn|Letterhead and presentation folder with pocket|Email signature and corporate materials|Mockups and application previews',
        'tools'=>'Illustrator|Photoshop|Figma|InDesign|Mockups|Print',
        'results'=>[
          ['Consistent system','Logo, color palette and typography create a coherent, professional identity for the legal practice.'],
          ['Ready-to-use materials','We extended the identity to business cards, letterhead, a presentation folder and an email signature.'],
          ['Premium without excess','Navy, beige and subtle gold build trust without the heavy visual language often used by legal brands.']
        ],
        'challenge'=>'Create a legal brand that feels professional and premium without becoming heavy, generic or overly formal.',
        'solution'=>'We prepared 10 logo directions, then refined the selected concept, typography and navy-and-beige palette with a gold accent. The system was extended across the key corporate materials.',
        'effect'=>'The result is a consistent identity that works across documents, business cards, digital materials and as a strong foundation for the future website.',
        'mode'=>'logo',
        'skip_realizacje_overlay'=>'1'
      ],
      [
        'brand'=>'Zgórecki Nieruchomości',
        'sub'=>'logo and mini branding for a real estate brand',
        'type'=>'Logo + mini branding',
        'year'=>'2026',
        'img'=>'https://zaprojektowani.com/wp-content/uploads/2026/09/zgorecki.webp',
        'tag'=>'Real estate / Personal brand',
        'desc'=>'For Wojciech Zgórecki Nieruchomości we created an elegant logo system and mini branding that combines the personal character of the brand with a premium real estate aesthetic. The project began with 10 creative directions, and the selected mark was refined in terms of proportions, line weight, gold color variants and practical use in sales materials.',
        'client'=>'Wojciech Zgórecki Nieruchomości',
        'services'=>'Logo, mini branding, color variants, mockups, brand materials',
        'live'=>'',
        'meta'=>'Real estate|Gold|Premium',
        'scope'=>'10 logo concepts|Refinement of proportions and mark weight|Gold color variants|Brand typography|Logo versions for print and digital use|Real-estate mockups and presentation materials',
        'tools'=>'Illustrator|Photoshop|Figma|Mockups|Print',
        'results'=>[
          ['Distinctive mark','A refined symbol and typography create a recognizable signature for the personal brand.'],
          ['Flexible variants','Prepared color versions work across print, digital channels and property presentation materials.'],
          ['Premium positioning','Gold accents and a calm composition support an elegant real-estate brand image.']
        ],
        'challenge'=>'Combine the personal character of a real-estate advisor brand with a premium aesthetic while keeping the mark readable at small sizes.',
        'solution'=>'From 10 initial directions we selected and refined the final mark, adjusting its proportions, weight, gold tone and typography. We then prepared a flexible set of variants and realistic application mockups.',
        'effect'=>'The brand received an elegant, flexible logo system ready for property offers, social media, printed materials and future online communication.',
        'mode'=>'logo',
        'skip_realizacje_overlay'=>'1'
      ]
    ];

    $priority_logo = $is_en ? $priority_logo_en : $priority_logo_pl;
    $items = array_values(array_filter((array)$items, function($it){
      $brand = trim((string)($it['brand'] ?? ''));
      $brand_key = function_exists('sanitize_title') ? sanitize_title($brand) : strtolower($brand);
      return !in_array($brand_key, ['natalia-arciszewska','zgorecki','zgorecki-nieruchomosci','wojciech-zgorecki-nieruchomosci'], true);
    }));
    $items = array_merge($priority_logo, $items);
  }

  // v2.1.17 — twarde zabezpieczenie przed duplikatami marek w portfolio
  // (np. gdy po wcześniejszych aktualizacjach w opcji CMS zostały zdublowane Inusti / Piekarnia Marysia).
  $deduped = [];
  $seen = [];
  foreach ((array) $items as $it) {
    $brand_key = strtolower(trim((string)($it['brand'] ?? $it['name'] ?? '')));
    if ($brand_key !== '' && isset($seen[$brand_key])) {
      continue;
    }
    if ($brand_key !== '') {
      $seen[$brand_key] = true;
    }
    $deduped[] = $it;
  }
  $items = $deduped;

  $out=[];
  foreach($items as $it){
    if(empty($it['brand'])) continue;
    if(isset($it['visible']) && (string)$it['visible'] === '0') continue;
    $it = zp_suite_home_portfolio_realizacje_overlay($it, $mode);
    $out[] = [
      'brand'=>$it['brand'] ?? '',
      'sub'=>$it['sub'] ?? '',
      'type'=>$it['type'] ?? '',
      'year'=>$it['year'] ?? '',
      'img'=>function_exists('zp_suite_full_upload_image_url') ? zp_suite_full_upload_image_url($it['img'] ?? '') : ($it['img'] ?? ''),
      'tag'=>$it['tag'] ?? '',
      'desc'=>$it['desc'] ?? '',
      'meta'=>zp_suite_portfolio_list_value($it['meta'] ?? ''),
      'client'=>$it['client'] ?? '',
      'services'=>zp_suite_portfolio_services_value($it['services'] ?? ''),
      'scope'=>zp_suite_portfolio_list_value($it['scope'] ?? ''),
      'tools'=>zp_suite_portfolio_list_value($it['tools'] ?? ''),
      'results'=>zp_suite_portfolio_results_value($it['results'] ?? []),
      'challenge'=>$it['challenge'] ?? '',
      'solution'=>$it['solution'] ?? '',
      'effect'=>$it['effect'] ?? '',
      'live'=>$it['live'] ?? '',
      'mode'=>$mode
    ];
  }
  $out[] = ['isCta'=>true,'mode'=>$mode,'brand'=> $mode==='logo' ? 'Tu może być Twoje logo' : 'Tu może być Twoja strona','sub'=>'otrzymaj darmową wycenę','type'=>'Darmowa wycena','year'=>'2026','tag'=>'Twój projekt','live'=>'/studio-wyceny/'];
  return $out;
}
function zp_suite_json($data){ return wp_json_encode($data, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); }
function zp_suite_source_icon($source){ return (stripos((string)$source,'facebook')!==false) ? 'https://zaprojektowani.com/wp-content/uploads/2026/05/fb_logo-scaled.webp' : 'https://zaprojektowani.com/wp-content/uploads/2026/05/google_logo-scaled.webp'; }

function zp_suite_sanitize_repeater($rows){
  $clean=[];
  if(!is_array($rows)) return $clean;
  foreach($rows as $row){
    if(!is_array($row)) continue;
    $c=[];
    foreach($row as $k=>$v){ $c[sanitize_key($k)] = is_array($v) ? '' : wp_kses_post(wp_unslash($v)); }
    if(array_filter($c, fn($x)=>trim((string)$x)!=='')) $clean[]=$c;
  }
  return $clean;
}


/* ZP Suite v2.2.76 — add/update ShotHome in home portfolio CMS after Gravia. */
if (!function_exists('zp_suite_shothome_home_portfolio_item')) {
  function zp_suite_shothome_home_portfolio_item(){
    return [
      'brand'=>'ShotHome',
      'sub'=>'strona usługowa + SEO + darmowa wycena online',
      'type'=>'Strona + SEO + darmowa wycena',
      'year'=>'2026',
      'img'=>'https://zaprojektowani.com/wp-content/uploads/2026/05/shothome_realizacja-2_11zon.webp',
      'tag'=>'Drzwi, podłogi i wykończenia / Warszawa',
      'desc'=>'Dla ShotHome stworzyliśmy od podstaw stronę usługową, która skraca drogę od przeglądania oferty do wysłania zapytania. Projekt objął indywidualny UI, wdrożenie WordPress, strukturę SEO pod Warszawę, blog, customową darmową wycenę online, system realizacji oraz katalog produktów podłogowych z możliwością dodawania i edycji w panelu.',
      'client'=>'ShotHome',
      'services'=>'Projekt graficzny, WordPress, SEO, blog, custom wycena online, system realizacji, katalog produktów',
      'live'=>'https://www.shothome.pl/',
      'meta'=>'darmowa wycena|SEO Warszawa|panel realizacji|katalog podłóg',
      'scope'=>'Projekt strony od podstaw|Indywidualny projekt graficzny w kolorystyce marki|Wdrożenie WordPress|Customowy formularz darmowej wyceny online|Panel realizacji: dodawanie, edytowanie i usuwanie projektów|Katalog produktów podłogowych z obsługą w panelu|Blog i struktura treści pod SEO|Optymalizacja lokalna pod Warszawę|Responsywny widok mobile',
      'tools'=>'WordPress|PHP|SEO|UX/UI|Blog|Custom CMS',
      'mode'=>'web',
      'visible'=>'1'
    ];
  }
}
if (!function_exists('zp_suite_upsert_shothome_home_portfolio')) {
  function zp_suite_upsert_shothome_home_portfolio(){
    $data = get_option('zp_suite_cms', []);
    if (!is_array($data) || empty($data)) {
      $data = function_exists('zp_suite_cms_defaults') ? zp_suite_cms_defaults() : [];
    }
    if (empty($data['portfolio']) || !is_array($data['portfolio'])) { $data['portfolio'] = ['web'=>[], 'logo'=>[]]; }
    if (empty($data['portfolio']['web']) || !is_array($data['portfolio']['web'])) { $data['portfolio']['web'] = []; }
    $shot = zp_suite_shothome_home_portfolio_item();
    $rows = [];
    foreach ((array)$data['portfolio']['web'] as $row) {
      if (!is_array($row)) { continue; }
      $brand = strtolower(trim((string)($row['brand'] ?? '')));
      if ($brand === 'shothome' || $brand === 'shot home') { continue; }
      $rows[] = $row;
    }
    $inserted = false;
    $out = [];
    foreach ($rows as $row) {
      $out[] = $row;
      $brand = strtolower(trim((string)($row['brand'] ?? '')));
      if (!$inserted && $brand === 'gravia') {
        $out[] = $shot;
        $inserted = true;
      }
    }
    if (!$inserted) {
      $pos = min(2, count($out));
      array_splice($out, $pos, 0, [$shot]);
    }
    $data['portfolio']['web'] = $out;
    update_option('zp_suite_cms', $data, false);
  }
}
add_action('plugins_loaded', function(){
  if (get_option('zp_suite_shothome_home_migration') !== '2.2.76') {
    zp_suite_upsert_shothome_home_portfolio();
    update_option('zp_suite_shothome_home_migration', '2.2.76', false);
  }
}, 31);


/**
 * v2.2.107 — SEO home FAQ migration based on May 2026 strategy.
 * Updates homepage FAQ defaults once to strengthen Rank Math and internal SEO.
 */
add_action('plugins_loaded', function(){
  if (get_option('zp_suite_home_faq_seo_migration_2_2_107') === '1') {
    return;
  }
  $data = get_option('zp_suite_cms', []);
  if (!is_array($data) || empty($data)) {
    $data = function_exists('zp_suite_cms_defaults') ? zp_suite_cms_defaults() : [];
  }
  $data['faq'] = [
    [
      'q' => 'Czy projektujecie strony internetowe w Katowicach?',
      'a' => 'Tak. Projektujemy strony internetowe dla firm z Katowic, Śląska i całej Polski. Pracujemy na WordPressie, dbamy o UX, responsywność, szybkość działania, SEO i jasną ścieżkę kontaktu.'
    ],
    [
      'q' => 'Czy tworzycie sklepy internetowe WooCommerce?',
      'a' => 'Tak. Tworzymy sklepy internetowe WooCommerce z produktami, koszykiem, płatnościami, dostawami, analityką i strukturą gotową pod SEO oraz kampanie reklamowe.'
    ],
    [
      'q' => 'Czy można zamówić logo, branding i stronę w jednym projekcie?',
      'a' => 'Tak. Połączenie logo, identyfikacji wizualnej, strony internetowej i treści daje najlepszy efekt, bo marka wygląda spójnie od pierwszego kontaktu aż po formularz zapytania.'
    ],
    [
      'q' => 'Czy pomagacie w SEO i widoczności strony?',
      'a' => 'Tak. Już na etapie projektu planujemy strukturę nagłówków, sekcje, treści, linkowanie wewnętrzne, FAQ i podstawy techniczne, które pomagają stronie lepiej pracować w Google.'
    ],
    [
      'q' => 'Czy prowadzicie kampanie Meta Ads?',
      'a' => 'Tak. Przygotowujemy kampanie na Facebooku i Instagramie, konfigurujemy Pixel/CAPI, remarketing, kreacje, zdarzenia i analitykę, żeby reklama prowadziła do zapytań lub sprzedaży.'
    ],
    [
      'q' => 'Dla jakich branż tworzycie strony i sklepy?',
      'a' => 'Pracujemy między innymi dla kancelarii, deweloperów, salonów beauty, lekarzy, producentów, e-commerce, marek premium i firm usługowych, które chcą wyglądać profesjonalnie online.'
    ],
  ];
  update_option('zp_suite_cms', $data, false);
  update_option('zp_suite_home_faq_seo_migration_2_2_107', '1', false);
}, 33);

/**
 * v2.2.109 — rozszerzone FAQ homepage pod SEO, AI Overviews i dane strukturalne.
 */
add_action('plugins_loaded', function(){
  if (get_option('zp_suite_home_faq_ai_seo_migration_2_2_109') === '1') {
    return;
  }
  $data = get_option('zp_suite_cms', []);
  if (!is_array($data) || empty($data)) {
    $data = function_exists('zp_suite_cms_defaults') ? zp_suite_cms_defaults() : [];
  }
  $data['faq'] = [
    [
      'q' => 'Czym zajmuje się Zaprojektowani.com?',
      'a' => 'Zaprojektowani.com to studio projektujące strony internetowe, sklepy internetowe WooCommerce, logo, branding, identyfikację wizualną, treści SEO i kampanie Meta Ads. Pomagamy firmom z Katowic, Śląska i całej Polski stworzyć spójny wizerunek online oraz stronę, która prowadzi użytkownika do zapytania, zakupu lub kontaktu.'
    ],
    [
      'q' => 'Czy projektujecie strony internetowe w Katowicach?',
      'a' => 'Tak. Projektujemy strony internetowe dla firm z Katowic, Śląska i całej Polski. Tworzymy strony firmowe, landing page, rozbudowane serwisy WordPress i podstrony usługowe. W projekcie uwzględniamy UX, responsywność, szybkość działania, SEO, strukturę nagłówków, formularze kontaktowe i jasną ścieżkę do pozyskania zapytania.'
    ],
    [
      'q' => 'Czy tworzycie sklepy internetowe WooCommerce?',
      'a' => 'Tak. Tworzymy sklepy internetowe WooCommerce dla firm, marek premium, producentów i sprzedawców, którzy chcą sprzedawać online. Projekt obejmuje architekturę kategorii, karty produktów, koszyk, checkout, płatności, dostawy, podstawy SEO produktowego, analitykę i układ przygotowany pod kampanie reklamowe.'
    ],
    [
      'q' => 'Czy można zamówić stronę internetową, logo i branding w jednym projekcie?',
      'a' => 'Tak. To często najlepsze rozwiązanie, ponieważ strona internetowa, logo, kolorystyka, typografia, komunikacja i materiały marki powstają jako jeden spójny system. Dzięki temu firma wygląda profesjonalnie na stronie, w social media, reklamach, ofertach, wizytówkach i materiałach sprzedażowych.'
    ],
    [
      'q' => 'Czy pomagacie w SEO już podczas projektowania strony?',
      'a' => 'Tak. SEO planujemy już na etapie struktury strony. Ustalamy hierarchię H1, H2 i H3, logiczne sekcje, treści usługowe, linkowanie wewnętrzne, FAQ, meta title, meta description, alt teksty obrazów oraz dane strukturalne. Dzięki temu strona nie jest tylko ładnym projektem, ale ma lepsze podstawy pod widoczność w Google i w odpowiedziach generowanych przez AI.'
    ],
    [
      'q' => 'Czy prowadzicie kampanie Meta Ads na Facebooku i Instagramie?',
      'a' => 'Tak. Przygotowujemy i prowadzimy kampanie Meta Ads dla firm usługowych, e-commerce i marek lokalnych. Konfigurujemy Pixel, zdarzenia, remarketing, kreacje reklamowe, grupy odbiorców, formularze leadowe i analitykę, aby reklama kierowała użytkownika do właściwej strony, sklepu lub formularza kontaktowego.'
    ],
    [
      'q' => 'Dla jakich branż tworzycie strony internetowe i sklepy?',
      'a' => 'Najczęściej pracujemy dla kancelarii prawnych, deweloperów, salonów beauty, gabinetów lekarskich, producentów, sklepów internetowych, firm lokalnych, marek premium i usług B2B. Każdy projekt dopasowujemy do branży: inna struktura sprawdzi się dla kancelarii, inna dla sklepu WooCommerce, a inna dla marki usługowej zbierającej leady.'
    ],
    [
      'q' => 'Czym różni się zwykła strona od strony zaprojektowanej pod SEO i konwersję?',
      'a' => 'Zwykła strona często skupia się wyłącznie na wyglądzie. Strona projektowana pod SEO i konwersję ma przemyślaną strukturę treści, nagłówki, sekcje usługowe, linkowanie wewnętrzne, szybkie formularze, dobre CTA, responsywny układ, zoptymalizowane obrazy i jasną odpowiedź na pytania użytkownika. Dzięki temu lepiej wspiera widoczność i sprzedaż.'
    ],
    [
      'q' => 'Czy realizujecie projekty tylko lokalnie w Katowicach?',
      'a' => 'Nie. Katowice i Śląsk są ważnym obszarem lokalnym, ale obsługujemy klientów z całej Polski. Brief, projekt, konsultacje, wdrożenie WordPress lub WooCommerce oraz dalszą opiekę możemy prowadzić zdalnie, bez utraty jakości komunikacji i procesu.'
    ],
    [
      'q' => 'Jak zacząć współpracę przy stronie, sklepie lub brandingu?',
      'a' => 'Najprościej przejść do Studia Wyceny i opisać, czego potrzebujesz: strony internetowej, sklepu WooCommerce, logo, brandingu, SEO albo kampanii Meta Ads. Na podstawie odpowiedzi przygotujemy konkretny zakres, rekomendowaną strukturę projektu i wycenę dopasowaną do celu biznesowego.'
    ],
  ];
  update_option('zp_suite_cms', $data, false);
  update_option('zp_suite_home_faq_ai_seo_migration_2_2_109', '1', false);
}, 34);

/* ZP Suite v2.2.770 — add OUTTECH to homepage portfolio CMS. */
if (!function_exists('zp_suite_outtech_home_portfolio_item')) {
  function zp_suite_outtech_home_portfolio_item(){
    return [
      'brand'=>'OutTech','sub'=>'nowa strona firmowa + oferta B2B/B2C + formularze serwisowe','type'=>'Strona firmowa / technika zabezpieczeń','year'=>'2026',
      'img'=>'https://zaprojektowani.com/wp-content/uploads/2026/09/outtech_realizacja_compressed-scaled.webp','tag'=>'Systemy bezpieczeństwa / B2B + smart home',
      'desc'=>'Dla OutTech Technika Zabezpieczeń zaprojektowaliśmy nową stronę, która porządkuje rozbudowaną ofertę systemów bezpieczeństwa i pokazuje firmę jako partnera inżynieryjnego, a nie tylko instalatora. Serwis rozdziela rozwiązania dla biznesu i klientów indywidualnych, prowadzi przez alarmy, CCTV, kontrolę dostępu, automatykę i smart home oraz obsługuje zapytania ofertowe i zgłoszenia serwisowe.',
      'client'=>'OutTech Technika Zabezpieczeń','services'=>'UX/UI, WordPress, architektura informacji, formularz ofertowy, formularz serwisowy, SEO, RWD','live'=>'https://www.outtech.pl/',
      'meta'=>'WordPress|systemy bezpieczeństwa|formularz serwisowy|SEO',
      'scope'=>'Nowa architektura informacji i uporządkowanie rozbudowanej oferty|Strona główna, O firmie, Realizacje, Blog / aktualności, FAQ i Kontakt|Sekcja Dla biznesu z osobnymi obszarami bezpieczeństwa elektronicznego, nadzoru, ruchu osobowego, automatyzacji i ciągłości działania|Sekcja Inteligentna i bezpieczna przestrzeń dla klientów indywidualnych|Zakres usług: projektowanie, dobór rozwiązań, know-how, integracje, serwis i rozbudowa|Sekcja Jak pracujemy prowadząca od rozpoznania potrzeb do opieki nad instalacją|Rozbudowany formularz ofertowy|Formularz serwisowy z kategorią awarii, typem zgłoszenia, priorytetem i załącznikami|Responsywny projekt desktop, tablet i mobile|Struktura treści przygotowana pod widoczność lokalną w Google na Śląsku i Opolszczyźnie',
      'tools'=>'WordPress|UX/UI|SEO|Custom Forms|RWD','mode'=>'web','visible'=>'1'
    ];
  }
}
if (!function_exists('zp_suite_upsert_outtech_home_portfolio')) {
  function zp_suite_upsert_outtech_home_portfolio(){
    $data = get_option('zp_suite_cms', []);
    if (!is_array($data) || empty($data)) { $data = function_exists('zp_suite_cms_defaults') ? zp_suite_cms_defaults() : []; }
    if (empty($data['portfolio']) || !is_array($data['portfolio'])) { $data['portfolio'] = ['web'=>[], 'logo'=>[]]; }
    if (empty($data['portfolio']['web']) || !is_array($data['portfolio']['web'])) { $data['portfolio']['web'] = []; }
    $outtech = zp_suite_outtech_home_portfolio_item();
    $rows = [];
    foreach ((array)$data['portfolio']['web'] as $row) {
      if (!is_array($row)) { continue; }
      $brand = strtolower(trim((string)($row['brand'] ?? '')));
      if ($brand === 'outtech' || $brand === 'out tech') { continue; }
      $rows[] = $row;
    }
    array_unshift($rows, $outtech);
    $data['portfolio']['web'] = $rows;
    update_option('zp_suite_cms', $data, false);
  }
}
add_action('plugins_loaded', function(){
  if (get_option('zp_suite_outtech_home_migration') !== '2.2.770') {
    zp_suite_upsert_outtech_home_portfolio();
    update_option('zp_suite_outtech_home_migration', '2.2.770', false);
  }
}, 34);

