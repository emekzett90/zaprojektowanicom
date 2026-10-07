<?php
if (!defined('ABSPATH')) { exit; }

/**
 * ZAPROJEKTOWANI SUITE — Realizacje CMS v2.2.47
 */
function zp_suite_realizacje_default_categories(){
  return [
    ['visible'=>'1','name'=>'Wszystkie','slug'=>'all','icon'=>'layout-grid'],
    ['visible'=>'1','name'=>'Strony internetowe','slug'=>'web','icon'=>'monitor'],
    ['visible'=>'1','name'=>'Sklepy internetowe','slug'=>'shop','icon'=>'shopping-bag'],
    ['visible'=>'1','name'=>'Logo & branding','slug'=>'branding','icon'=>'pen-tool'],
  ];
}
function zp_suite_realizacje_default_projects(){
  $json = <<<'JSON'
[
  {
    "cat": "shop",
    "brand": "ProScarves",
    "sub": "E-commerce + zamówienia hurtowe",
    "type": "B2B custom / sklep sportowy",
    "year": "2026",
    "img": "https://zaprojektowani.com/wp-content/uploads/2026/05/proscarves_realizacja_nowa_compressed.webp",
    "tag": "B2B custom / sklep sportowy",
    "desc": "Dla ProScarves projektujemy sklep pod dwa scenariusze sprzedaży: gotowe produkty dla fanów oraz zapytania B2B dla klubów, fan clubów i organizacji. Struktura łączy detaliczny e-commerce, customowy system wyceny zamówień hurtowych, konfigurację zapytania, prezentację produkcji customowej i własny design dopasowany do rynku sportowego.",
    "client": "ProScarves",
    "services": "UX/UI, WooCommerce, sklep detaliczny, system wyceny B2B, formularze custom orders, wdrożenie WordPress",
    "live": "https://www.proscarves.com/",
    "meta": [
      "WooCommerce",
      "B2B custom",
      "fan clubs",
      "system wyceny"
    ],
    "scope": [
      "Indywidualny projekt graficzny sklepu",
      "Sklep detaliczny z gotowymi produktami dla fanów",
      "Customowy system wyceny dla klubów i fan clubów",
      "Ścieżka zapytań B2B dla klubów, firm i organizacji",
      "Prezentacja produkcji customowej",
      "Struktura pod rynki międzynarodowe",
      "Responsywna wersja mobile",
      "Podstawy SEO, analityka i przygotowanie pod kampanie"
    ],
    "featured": true
  },
  {
    "cat": "web branding",
    "brand": "Siemianowski",
    "sub": "Strona firmowa + branding + wizyty",
    "type": "Kancelaria premium / prawo i restrukturyzacja",
    "year": "2026",
    "img": "https://zaprojektowani.com/wp-content/uploads/2026/05/siemianowski_realizacja_nowa_compressed.webp",
    "tag": "Kancelaria premium / prawo i restrukturyzacja",
    "desc": "Dla kancelarii przygotowaliśmy elegancki system wizerunkowy i rozbudowaną stronę, która ma budować zaufanie jeszcze przed pierwszym telefonem. Struktura prowadzi użytkownika przez specjalizacje, doświadczenie, zakres pomocy, kontakt i umawianie wizyt — w spokojnym, eksperckim stylu premium.",
    "client": "Kancelaria Adwokata i Doradcy Restrukturyzacyjnego Arkadiusz Siemianowski",
    "services": "Branding, projekt strony, WordPress, UX, copywriting, formularze kontaktowe, umawianie wizyt",
    "live": "https://siemianowski.pl/",
    "meta": [
      "branding",
      "kancelaria",
      "premium UI",
      "wizyty"
    ],
    "scope": [
      "Kierunek wizualny marki premium",
      "Logo i system identyfikacji",
      "Projekt rozbudowanej strony kancelarii",
      "Struktura specjalizacji prawnych i restrukturyzacyjnych",
      "Sekcje zaufania, doświadczenia i kontaktu",
      "Elementy umawiania wizyt",
      "Responsywny widok mobile",
      "Przygotowanie pod treści eksperckie i SEO"
    ],
    "featured": true
  },
  {
    "cat": "web branding",
    "brand": "Apartament Piękna",
    "sub": "nowa strona, rezerwacje online, branding i SEO",
    "type": "Strona + Rezerwacje + SEO",
    "year": "2026",
    "img": "https://zaprojektowani.com/wp-content/uploads/2026/05/apartament_realizacja_compressed.webp",
    "tag": "Beauty premium / medycyna estetyczna",
    "desc": "Dla marki Apartament Piękna z Tarnowa przygotowaliśmy kompleksowy redesign wizerunku online — od odświeżenia identyfikacji wizualnej, przez pełny projekt strony internetowej, aż po autorski system rezerwacji wizyt.",
    "client": "Apartament Piękna — Tarnów",
    "services": "Redesign strony, branding, WordPress, rezerwacje online, blog SEO, analityka",
    "live": "https://www.apartamentpiekna.pl/",
    "meta": [
      "rezerwacje online",
      "SEO lokalne",
      "premium beauty"
    ],
    "scope": [
      "Całkowity redesign strony apartamentpiekna.pl",
      "Odświeżenie logo i oprawy wizualnej marki",
      "Projekt graficzny premium beauty",
      "Autorski system rezerwacji wizyt online",
      "Powiadomienia SMS dla klientów",
      "Responsywna wersja mobilna",
      "Autorski system bloga branżowego",
      "Struktura SEO pod frazy lokalne",
      "Konfiguracja Google Search Console, GA4 i Meta Pixel"
    ],
    "featured": false
  },
  {
    "cat": "web",
    "brand": "Gravia",
    "sub": "strona dla biura projektów infrastrukturalnych",
    "type": "Strona firmowa + Kariera",
    "year": "2026",
    "img": "https://zaprojektowani.com/wp-content/uploads/2026/05/gravia_real_compressed.webp",
    "tag": "Infrastruktura / branża drogowa",
    "desc": "Dla firmy Gravia przygotowaliśmy nowoczesną, techniczną i funkcjonalną stronę dopasowaną do branży drogowej oraz infrastrukturalnej. Ważnym elementem był autorski system rekrutacyjny.",
    "client": "Gravia — Biuro Projektów Infrastrukturalnych",
    "services": "Web Design, WordPress Dev, UX, System kariery",
    "live": "https://gravia.pl/",
    "meta": [
      "techniczny UI",
      "kariera",
      "realizacje"
    ],
    "scope": [
      "Indywidualny projekt graficzny strony",
      "Nowoczesny układ pod branżę infrastrukturalną",
      "Wersja desktop i mobile",
      "Podstrony ofertowe i informacyjne",
      "Formularze kontaktowe",
      "Rozbudowana sekcja realizacji",
      "Autorski system kariery z obsługą CV i plików"
    ],
    "featured": false
  },
  {
    "cat": "web",
    "brand": "ShotHome",
    "sub": "strona usługowa + SEO + darmowa wycena online",
    "type": "Strona + SEO + darmowa wycena",
    "year": "2026",
    "img": "https://zaprojektowani.com/wp-content/uploads/2026/05/shothome_realizacja-2_11zon.webp",
    "tag": "Drzwi, podłogi i wykończenia / Warszawa",
    "desc": "Dla ShotHome stworzyliśmy od podstaw stronę usługową, która skraca drogę od przeglądania oferty do wysłania zapytania. Projekt objął indywidualny UI, wdrożenie WordPress, strukturę SEO pod Warszawę, blog, customową darmową wycenę online, system realizacji oraz katalog produktów podłogowych z możliwością dodawania i edycji w panelu.",
    "client": "ShotHome",
    "services": "Projekt graficzny, WordPress, SEO, blog, custom wycena online, system realizacji, katalog produktów",
    "live": "https://www.shothome.pl/",
    "meta": [
      "darmowa wycena",
      "SEO Warszawa",
      "panel realizacji",
      "katalog podłóg"
    ],
    "scope": [
      "Projekt strony od podstaw",
      "Indywidualny projekt graficzny w kolorystyce marki",
      "Wdrożenie WordPress",
      "Customowy formularz darmowej wyceny online",
      "Panel realizacji: dodawanie, edytowanie i usuwanie projektów",
      "Katalog produktów podłogowych z obsługą w panelu",
      "Blog i struktura treści pod SEO",
      "Optymalizacja lokalna pod Warszawę",
      "Responsywny widok mobile"
    ],
    "featured": false
  },
  {
    "cat": "web branding",
    "brand": "Krawiec z dojazdem",
    "sub": "rebranding, nowa strona i system wizyt",
    "type": "System + Rebranding",
    "year": "2026",
    "img": "https://zaprojektowani.com/wp-content/uploads/2026/05/krawiec_real_compressed.webp",
    "tag": "Usługi premium / lokalnie",
    "desc": "Kompleksowy projekt obejmujący rebranding marki, zaprojektowanie nowej strony internetowej oraz wdrożenie autorskiego systemu umawiania wizyt z dojazdem do klienta.",
    "client": "Krawiec z dojazdem",
    "services": "Branding, Web Design, WordPress Dev, SEO",
    "live": "https://krawieczdojazdem.pl/",
    "meta": [
      "premium UI",
      "mobile-first",
      "SEO-ready"
    ],
    "scope": [
      "Rebranding identyfikacji wizualnej",
      "Strona WordPress od zera",
      "System umawiania wizyt online",
      "Optymalizacja SEO lokalne",
      "Integracja Google Analytics",
      "Design mobile-first"
    ],
    "featured": false
  },
  {
    "cat": "shop",
    "brand": "Papeterio",
    "sub": "subtelny sklep online z papeterią i personalizacją",
    "type": "Sklep WooCommerce",
    "year": "2026",
    "img": "https://zaprojektowani.com/wp-content/uploads/2026/05/papeterio_real_compressed.webp",
    "tag": "Papeteria / e-commerce",
    "desc": "Dla marki Papeterio stworzyliśmy subtelny, elegancki sklep online z papeterią i dodatkami na wyjątkowe okazje. Projekt oddaje delikatny charakter marki i działa jak wygodny e-commerce.",
    "client": "Papeterio",
    "services": "Web Design, WooCommerce, UX, Payments",
    "live": "https://papeterio.pl/",
    "meta": [
      "WooCommerce",
      "personalizacja",
      "mobile UX"
    ],
    "scope": [
      "Sklep internetowy z kategoriami produktów",
      "Dopracowane karty produktów",
      "Opcje personalizacji zamówienia",
      "Warianty i dodatki",
      "Koszyk i płatności online",
      "Formularze kontaktowe",
      "Wersja mobilna dopasowana do zakupów z telefonu"
    ],
    "featured": false
  },
  {
    "cat": "shop",
    "brand": "Bransoletka24",
    "sub": "sklep online z hurtem i detalem",
    "type": "Sklep WooCommerce",
    "year": "2026",
    "img": "https://zaprojektowani.com/wp-content/uploads/2026/05/bransoletka24_real_compressed.webp",
    "tag": "E-commerce / detal + hurt",
    "desc": "Sklep WooCommerce zaprojektowany od zera z myślą o konwersji dla klientów detalicznych i hurtowych. Prosty checkout, przejrzyste kategorie, szybki filtr, wishlist i responsywny design.",
    "client": "Bransoletka24",
    "services": "Web Design, WooCommerce, Payments, UX",
    "live": "https://bransoletka24.pl/",
    "meta": [
      "WooCommerce",
      "checkout",
      "B2B + B2C"
    ],
    "scope": [
      "Sklep WooCommerce od zera",
      "System hurtowy z rejestracją B2B",
      "Integracja płatności online",
      "Wishlist i warianty produktów",
      "Animacje GSAP",
      "Optymalizacja konwersji"
    ],
    "featured": false
  },
  {
    "cat": "web",
    "brand": "RUTPOŻ",
    "sub": "strona, system zgłoszeń i asystent AI",
    "type": "System + AI",
    "year": "2025",
    "img": "https://zaprojektowani.com/wp-content/uploads/2026/05/rutpoz_rea_compressed.jpg",
    "tag": "PPOŻ / system zgłoszeń + AI",
    "desc": "Projekt łączący nowoczesną stronę z autorskim systemem zgłoszeń serwisowych, kalkulatorami wydajności hydrantów i asystentem AI odpowiadającym na pytania klientów 24/7.",
    "client": "RUTPOŻ",
    "services": "Web, System zgłoszeń, AI Chatbot, Kalkulatory",
    "live": "https://rutpoz.pl/",
    "meta": [
      "AI chatbot",
      "kalkulatory",
      "B2B"
    ],
    "scope": [
      "Strona firmowa WordPress",
      "Asystent AI 24/7",
      "Kalkulator wydajności hydrantów",
      "Kalkulator gęstości ogniowej",
      "Generowanie raportów PDF",
      "System zgłoszeń serwisowych"
    ],
    "featured": false
  },
  {
    "cat": "web shop",
    "brand": "Druga Szansa",
    "sub": "strona sieci sklepów z sekcją franczyzy",
    "type": "Sieć / Franczyza",
    "year": "2025",
    "img": "https://zaprojektowani.com/wp-content/uploads/2026/05/druga_szansa_real_compressed.webp",
    "tag": "Sieć sklepów / franczyza",
    "desc": "Strona dla sieci sklepów z odzieżą Druga Szansa. Mapa punktów sprzedaży, sekcja franczyzy z formularzem, system kodów rabatowych i prezentacja oferty.",
    "client": "Druga Szansa",
    "services": "Web Design, Lead Gen, Mapa lokalizacji",
    "live": "https://drugaszansa.net/",
    "meta": [
      "lead gen",
      "franczyza",
      "mapa punktów"
    ],
    "scope": [
      "Strona sieci sklepów",
      "Sekcja franczyzy z formularzem",
      "Mapa punktów sprzedaży",
      "System kodów rabatowych",
      "Integracja WooCommerce",
      "Kampanie leadowe"
    ],
    "featured": false
  },
  {
    "cat": "web",
    "brand": "Inusti",
    "sub": "strona B2B nastawiona na szybkie zapytania",
    "type": "Strona B2B",
    "year": "2025",
    "img": "https://zaprojektowani.com/wp-content/uploads/2026/05/inusti_real_compressed.webp",
    "tag": "Firma budowlana / B2B",
    "desc": "Strona B2B dla firmy budowlanej — czytelna struktura, szybki formularz kontaktowy, sekcja realizacji i optymalizacja pod Google Ads.",
    "client": "Inusti",
    "services": "Web Design, WordPress Dev, SEO",
    "live": "",
    "meta": [
      "B2B",
      "lead flow",
      "SEO-ready"
    ],
    "scope": [
      "Strona firmowa B2B",
      "Formularz szybkiego kontaktu",
      "Sekcja realizacji z galeriami",
      "Optymalizacja pod Google Ads",
      "Struktura SEO on-site",
      "Design mobile-first"
    ],
    "featured": false
  },
  {
    "cat": "web",
    "brand": "Piekarnia Marysia",
    "sub": "strona lokalna z ofertą i kierunkiem pod rozwój",
    "type": "Strona lokalna",
    "year": "2025",
    "img": "https://zaprojektowani.com/wp-content/uploads/2026/05/piekarnia_marysia_real_compressed.webp",
    "tag": "Piekarnia / gastronomia",
    "desc": "Minimalistyczna strona dla piekarni — prezentacja menu, galeria produktów, dane kontaktowe z integracją Google Maps i SEO lokalnym.",
    "client": "Piekarnia Marysia",
    "services": "Web Design, SEO lokalne, Fotografia",
    "live": "",
    "meta": [
      "local SEO",
      "oferta",
      "szybki UX"
    ],
    "scope": [
      "Strona wizytówka piekarni",
      "Prezentacja menu i produktów",
      "Galeria z fotografią produktową",
      "Integracja Google Maps",
      "Optymalizacja SEO lokalne",
      "Responsywny design"
    ],
    "featured": false
  },
  {
    "cat": "branding",
    "brand": "Siemianowski",
    "sub": "logo i branding kancelarii premium",
    "type": "Logo + branding premium",
    "year": "2026",
    "img": "https://zaprojektowani.com/wp-content/uploads/2026/05/siemianowski_real_logo_compressed.webp",
    "tag": "Kancelaria / Restrukturyzacja",
    "desc": "Dla kancelarii przygotowaliśmy spójny kierunek logo i brandingu premium oparty na eleganckiej typografii, stonowanej kolorystyce i znaku działającym na dokumentach, stronie oraz materiałach firmowych.",
    "client": "Siemianowski",
    "services": "Logo, branding, mockupy, materiały firmowe",
    "live": "",
    "meta": [
      "Premium",
      "Kancelaria",
      "Elegancja"
    ],
    "scope": [
      "Projekt logo z wariantami",
      "Kolorystyka i typografia",
      "Mockupy i wizualizacje",
      "Materiały do druku",
      "Identyfikacja kancelarii",
      "Wytyczne do strony WWW"
    ],
    "featured": false
  },
  {
    "cat": "branding",
    "brand": "POLERSTONE",
    "sub": "nowoczesne logo dla usług kamieniarskich",
    "type": "Logo + identyfikacja",
    "year": "2026",
    "img": "https://zaprojektowani.com/wp-content/uploads/2026/05/polerstone_logo_compressed.webp",
    "tag": "Kamieniarstwo premium",
    "desc": "Elegancki i nowoczesny projekt logo podkreślający solidność, precyzję i premium charakter usług kamieniarskich.",
    "client": "POLERSTONE",
    "services": "Logo, branding, mockupy, materiały firmowe",
    "live": "",
    "meta": [
      "Monogram",
      "Premium",
      "Kamień"
    ],
    "scope": [
      "Logo marki POLERSTONE",
      "Sygnet / monogram PS",
      "Wersje kolorystyczne",
      "Mockupy prezentacyjne",
      "Wizualizacje na kamieniu",
      "Materiały firmowe"
    ],
    "featured": false
  },
  {
    "cat": "branding",
    "brand": "Pracownia Urody",
    "sub": "delikatny branding beauty dla Joanny Samborskiej",
    "type": "Logo + identyfikacja",
    "year": "2026",
    "img": "https://zaprojektowani.com/wp-content/uploads/2026/05/pracownia_urody_logo_compressed.webp",
    "tag": "Beauty / Kosmetologia",
    "desc": "Delikatny, kobiecy i elegancki projekt logo, który podkreśla spokojny, profesjonalny i estetyczny charakter gabinetu.",
    "client": "Pracownia Urody",
    "services": "Logo, branding, mockupy, materiały firmowe",
    "live": "",
    "meta": [
      "Beauty",
      "Subtelnie",
      "Kobieco"
    ],
    "scope": [
      "Logo marki",
      "Subtelny sygnet z profilem",
      "Wersje kolorystyczne",
      "Mockupy prezentacyjne",
      "Wizualizacje na opakowaniach",
      "Materiały firmowe i social media"
    ],
    "featured": false
  },
  {
    "cat": "branding",
    "brand": "Arcycięcie",
    "sub": "charakterystyczny branding studia fryzur",
    "type": "Logo + identyfikacja",
    "year": "2025",
    "img": "https://zaprojektowani.com/wp-content/uploads/2026/05/arcy_ciecie_logo_compressed.webp",
    "tag": "Barber / Fryzjerstwo",
    "desc": "Kompleksowy projekt brandingowy dla studia fryzur Arcycięcie. Logo z detalem brzytwy i nożyczek oddaje charakter barbershopu.",
    "client": "Arcycięcie",
    "services": "Logo, branding, mockupy, materiały firmowe",
    "live": "",
    "meta": [
      "Logo premium",
      "Materiały salonu",
      "Branding"
    ],
    "scope": [
      "Projekt logo z wariantami",
      "Kolorystyka i typografia",
      "Mockupy i wizualizacje",
      "Materiały do druku",
      "Elementy oznakowania salonu",
      "Szablony social media"
    ],
    "featured": false
  },
  {
    "cat": "branding",
    "brand": "Vista Architekci",
    "sub": "minimalistyczny monogram dla biura projektowego",
    "type": "Logo + identyfikacja",
    "year": "2025",
    "img": "https://zaprojektowani.com/wp-content/uploads/2026/05/vista_logo_compressed.webp",
    "tag": "Architekci",
    "desc": "Monogram VA zaprojektowany z myślą o biurze architektonicznym. Geometryczna forma nawiązuje do przekrojów budowlanych i gry światła.",
    "client": "Vista Architekci",
    "services": "Logo, branding, mockupy, materiały firmowe",
    "live": "",
    "meta": [
      "Minimalizm",
      "Architektura",
      "Brandbook"
    ],
    "scope": [
      "Sygnet monogramowy VA",
      "Brandbook z wytycznymi",
      "Papeteria firmowa",
      "Szablony ofertowe",
      "Wizytówki i teczki",
      "Materiały do prezentacji"
    ],
    "featured": false
  },
  {
    "cat": "branding",
    "brand": "Akoya",
    "sub": "gabinet kosmetyczny premium",
    "type": "Logo + identyfikacja",
    "year": "2025",
    "img": "https://zaprojektowani.com/wp-content/uploads/2026/05/akoya_logo_compressed.webp",
    "tag": "Beauty & Wellness",
    "desc": "Identyfikacja wizualna gabinetu kosmetycznego Akoya. Beżowo-złota paleta i miękka typografia tworzą poczucie luksusu i spokoju.",
    "client": "Akoya",
    "services": "Logo, branding, mockupy, materiały firmowe",
    "live": "",
    "meta": [
      "Beauty",
      "Mini brandbook",
      "Premium"
    ],
    "scope": [
      "Logo z wariantami kolorowymi",
      "Mini brandbook",
      "Wizytówki i karty zabiegowe",
      "Szablony social media",
      "Paleta kolorów i fonty",
      "Mockupy i wizualizacje"
    ],
    "featured": false
  },
  {
    "cat": "branding",
    "brand": "Eat Tasty",
    "sub": "apetyczny branding dowozu jedzenia",
    "type": "Logo + identyfikacja",
    "year": "2025",
    "img": "https://zaprojektowani.com/wp-content/uploads/2026/05/eat_tasty_logo_compressed.webp",
    "tag": "Gastro / Delivery",
    "desc": "Branding marki delivery Eat Tasty z dynamicznym symbolem łączącym talerz i strzałkę dostawy.",
    "client": "Eat Tasty",
    "services": "Logo, branding, mockupy, materiały firmowe",
    "live": "",
    "meta": [
      "Gastro",
      "Opakowania",
      "Social media"
    ],
    "scope": [
      "Logo z symbolem delivery",
      "Projekty opakowań i boksów",
      "Naklejki i etykiety",
      "Materiały social media",
      "System kolorystyczny",
      "Mockupy na opakowaniach"
    ],
    "featured": false
  },
  {
    "cat": "branding",
    "brand": "Fryz Dobry Barber",
    "sub": "męski branding barbershopu",
    "type": "Logo + identyfikacja",
    "year": "2025",
    "img": "https://zaprojektowani.com/wp-content/uploads/2026/05/fryz_dobry_logo_compressed.webp",
    "tag": "Barber / Men’s",
    "desc": "Kompletny branding barbershopu z brodatym sygnetem w stylu vintage.",
    "client": "Fryz Dobry Barber",
    "services": "Logo, branding, mockupy, materiały firmowe",
    "live": "",
    "meta": [
      "Barbershop",
      "Vintage",
      "Materiały wnętrza"
    ],
    "scope": [
      "Sygnet z brodatym motywem",
      "Typografia vintage",
      "Materiały do wnętrza",
      "Wizytówki i karty",
      "Szyld i oznakowanie",
      "System identyfikacji"
    ],
    "featured": false
  },
  {
    "cat": "branding",
    "brand": "Piotr Mazur",
    "sub": "monogram PM dla kancelarii adwokackiej",
    "type": "Logo + identyfikacja",
    "year": "2025",
    "img": "https://zaprojektowani.com/wp-content/uploads/2026/05/piotr_mazur_logo_compressed.webp",
    "tag": "Kancelaria",
    "desc": "Elegancki monogram PM dla kancelarii adwokackiej w formie stylizowanej tarczy.",
    "client": "Piotr Mazur",
    "services": "Logo, branding, mockupy, materiały firmowe",
    "live": "",
    "meta": [
      "Kancelaria",
      "Monogram",
      "Premium"
    ],
    "scope": [
      "Monogram PM w formie tarczy",
      "Papeteria firmowa",
      "Tablice kancelarii",
      "Wizytówki premium",
      "Wytyczne do strony WWW",
      "Brandbook"
    ],
    "featured": false
  },
  {
    "cat": "branding",
    "brand": "Okruszek",
    "sub": "pastelowe logo pracowni cukierniczej",
    "type": "Logo + identyfikacja",
    "year": "2025",
    "img": "https://zaprojektowani.com/wp-content/uploads/2026/05/okruszek_logo_compressed.webp",
    "tag": "Cukiernia / Słodkości",
    "desc": "Pastelowa identyfikacja pracowni cukierniczej Okruszek z symbolem tortu.",
    "client": "Okruszek",
    "services": "Logo, branding, mockupy, materiały firmowe",
    "live": "",
    "meta": [
      "Cukiernia",
      "Pastel",
      "Opakowania"
    ],
    "scope": [
      "Logo z symbolem tortu",
      "Projekty pudełek i opakowań",
      "Etykiety na produkty",
      "Oznakowanie witryny",
      "Paleta pastelowa",
      "Mockupy na pudełkach"
    ],
    "featured": false
  },
  {
    "cat": "branding",
    "brand": "Mona Cake",
    "sub": "branding pracowni tortów artystycznych",
    "type": "Logo + identyfikacja",
    "year": "2025",
    "img": "https://zaprojektowani.com/wp-content/uploads/2026/05/mona_logo_compressed.webp",
    "tag": "Cukiernia / Torty",
    "desc": "Bajkowy branding pracowni tortów artystycznych Mona Cake z symbolem babeczki.",
    "client": "Mona Cake",
    "services": "Logo, branding, mockupy, materiały firmowe",
    "live": "",
    "meta": [
      "Torty",
      "Pastel",
      "Social media"
    ],
    "scope": [
      "Logo z babeczką i sygnet",
      "Projekty pudełek na torty",
      "Wizytówki i etykiety",
      "Szablony Instagram",
      "System kolorystyczny",
      "Mockupy"
    ],
    "featured": false
  },
  {
    "cat": "branding",
    "brand": "Wonder Handmade",
    "sub": "eleganckie logo dla marki handmade",
    "type": "Logo + identyfikacja",
    "year": "2025",
    "img": "https://zaprojektowani.com/wp-content/uploads/2026/05/wonder_logo_compressed.webp",
    "tag": "Handmade / Rękodzieło",
    "desc": "Minimalistyczny branding marki handmade z biżuteryjnym sygnetem.",
    "client": "Wonder Handmade",
    "services": "Logo, branding, mockupy, materiały firmowe",
    "live": "",
    "meta": [
      "Handmade",
      "Etykiety",
      "Brandbook"
    ],
    "scope": [
      "Sygnet biżuteryjny",
      "Brandbook z wytycznymi",
      "Etykiety na produkty",
      "Projekty opakowań",
      "Wizytówki i papeteria",
      "Szablony social media"
    ],
    "featured": false
  },
  {
    "cat": "branding",
    "brand": "Sfera",
    "sub": "naturalny branding studia well-being",
    "type": "Logo + identyfikacja",
    "year": "2025",
    "img": "https://zaprojektowani.com/wp-content/uploads/2026/05/sfera_logo_compressed.webp",
    "tag": "Well-being / Holistycznie",
    "desc": "Branding studia well-being Sfera z organicznym symbolem i stonowaną paletą beży i zieleni.",
    "client": "Sfera",
    "services": "Logo, branding, mockupy, materiały firmowe",
    "live": "",
    "meta": [
      "Well-being",
      "Naturalne kolory",
      "Minimalizm"
    ],
    "scope": [
      "Logo z organicznym symbolem",
      "Paleta beży i zieleni",
      "Wizytówki i karty klienta",
      "Szablony komunikacji",
      "Mockupy i wizualizacje",
      "Materiały gabinetowe"
    ],
    "featured": false
  },
  {
    "cat": "branding",
    "brand": "Looksus",
    "sub": "monogramowe logo studia beauty",
    "type": "Logo + identyfikacja",
    "year": "2025",
    "img": "https://zaprojektowani.com/wp-content/uploads/2026/05/looksus_logo_compressed.webp",
    "tag": "Beauty studio",
    "desc": "Monogramowy branding studia beauty Looksus w ciepłej palecie nude.",
    "client": "Looksus",
    "services": "Logo, branding, mockupy, materiały firmowe",
    "live": "",
    "meta": [
      "Beauty",
      "Monogram",
      "Nude palette"
    ],
    "scope": [
      "Monogram w delikatnych formach",
      "Paleta nude i typografia",
      "Wizytówki premium",
      "Karty klienta",
      "Elementy wystroju studia",
      "Szablony social media"
    ],
    "featured": false
  },
  {
    "cat": "branding",
    "brand": "Show Party",
    "sub": "selfie mirror — wariant 1",
    "type": "Logo + identyfikacja",
    "year": "2025",
    "img": "https://zaprojektowani.com/wp-content/uploads/2026/05/show_party_logo_compressed.webp",
    "tag": "Event / Selfie mirror",
    "desc": "Nowoczesny branding firmy eventowej Show Party. Logo łączy elegancję z klimatem imprezy.",
    "client": "Show Party",
    "services": "Logo, branding, mockupy, materiały firmowe",
    "live": "",
    "meta": [
      "Event",
      "Selfie mirror",
      "Mockupy"
    ],
    "scope": [
      "Logo z efektem światła",
      "Branding pod eventy",
      "Materiały promocyjne",
      "Szablony social media",
      "Mockupy selfie mirror",
      "Materiały drukowane"
    ],
    "featured": false
  },
  {
    "cat": "branding",
    "brand": "Impulse Tanning",
    "sub": "energetyczny branding studia opalania",
    "type": "Logo + identyfikacja",
    "year": "2025",
    "img": "https://zaprojektowani.com/wp-content/uploads/2026/05/impulse_training_logo_compressed.webp",
    "tag": "Studio opalania",
    "desc": "Energetyczny branding studia opalania Impulse Tanning z gradientem glow.",
    "client": "Impulse Tanning",
    "services": "Logo, branding, mockupy, materiały firmowe",
    "live": "",
    "meta": [
      "Glow",
      "Gradient",
      "Studio"
    ],
    "scope": [
      "Logo z efektem glow",
      "Gradient i typografia",
      "Karty klienta",
      "Oznakowanie witryny",
      "Materiały promocyjne",
      "Mockupy"
    ],
    "featured": false
  },
  {
    "cat": "branding",
    "brand": "Medical Friend",
    "sub": "przyjazny brand medyczny",
    "type": "Logo + identyfikacja",
    "year": "2025",
    "img": "https://zaprojektowani.com/wp-content/uploads/2026/05/medical_friend_logo_compressed.webp",
    "tag": "Medycyna",
    "desc": "Branding medyczny Medical Friend z symbolem serca i uśmiechu.",
    "client": "Medical Friend",
    "services": "Logo, branding, mockupy, materiały firmowe",
    "live": "",
    "meta": [
      "Medycyna",
      "Zaufanie",
      "System"
    ],
    "scope": [
      "Logo z symbolem serca",
      "Paleta medyczna",
      "Oznakowanie gabinetów",
      "Papeteria firmowa",
      "Materiały informacyjne",
      "Szablony do strony"
    ],
    "featured": false
  },
  {
    "cat": "branding",
    "brand": "Janda",
    "sub": "subtelny luksus w stylistyce spa",
    "type": "Logo + identyfikacja",
    "year": "2025",
    "img": "https://zaprojektowani.com/wp-content/uploads/2026/05/janda_logo_compressed.webp",
    "tag": "Health & Beauty",
    "desc": "Luksusowy branding spa Janda z delikatną ilustracją kobiety otoczonej kwiatami.",
    "client": "Janda",
    "services": "Logo, branding, mockupy, materiały firmowe",
    "live": "",
    "meta": [
      "Spa",
      "Luxury",
      "Branding"
    ],
    "scope": [
      "Logo z ilustracją kwiatową",
      "Paleta luxury",
      "Menu zabiegów",
      "Wizytówki premium",
      "Szablony social media",
      "Mockupy i wizualizacje"
    ],
    "featured": false
  },
  {
    "cat": "branding",
    "brand": "Ralf Furniture",
    "sub": "branding marki meblarskiej",
    "type": "Logo + identyfikacja",
    "year": "2025",
    "img": "https://zaprojektowani.com/wp-content/uploads/2026/05/ralf_logo_compressed.webp",
    "tag": "Meble na wymiar",
    "desc": "Minimalistyczny branding marki meblarskiej Ralf Furniture z sygnetem w palecie czerni, złota i drewna.",
    "client": "Ralf Furniture",
    "services": "Logo, branding, mockupy, materiały firmowe",
    "live": "",
    "meta": [
      "Meble",
      "B2B",
      "Premium"
    ],
    "scope": [
      "Sygnet minimalistyczny",
      "Paleta czerni i drewna",
      "Katalog produktowy",
      "Papeteria firmowa",
      "Oznakowanie showroomu",
      "Materiały B2B"
    ],
    "featured": false
  },
  {
    "cat": "branding",
    "brand": "EVA Aesthetics",
    "sub": "luksusowy branding kliniki",
    "type": "Logo + identyfikacja",
    "year": "2025",
    "img": "https://zaprojektowani.com/wp-content/uploads/2026/05/eva_logo_compressed.webp",
    "tag": "Medycyna estetyczna",
    "desc": "Luksusowy branding kliniki medycyny estetycznej EVA Aesthetics.",
    "client": "EVA Aesthetics",
    "services": "Logo, branding, mockupy, materiały firmowe",
    "live": "",
    "meta": [
      "Klinika",
      "Nude & gold",
      "Luxury"
    ],
    "scope": [
      "Logotyp minimalistyczny",
      "Paleta nude & gold",
      "Oznakowanie kliniki",
      "Karty zabiegowe",
      "Wizytówki premium",
      "Brandbook z wytycznymi"
    ],
    "featured": false
  }
]
JSON;
  $rows = json_decode($json, true);
  return is_array($rows) ? $rows : [];
}
function zp_suite_realizacje_cms(){
  $saved = get_option('zp_suite_realizacje_cms', []);
  if (!is_array($saved)) { $saved = []; }
  $data = $saved;
  if (empty($data['projects']) || !is_array($data['projects'])) { $data['projects'] = zp_suite_realizacje_default_projects(); }
  if (empty($data['categories']) || !is_array($data['categories'])) { $data['categories'] = zp_suite_realizacje_default_categories(); }
  return $data;
}
function zp_suite_realizacje_projects(){
  $cms = zp_suite_realizacje_cms();
  $rows = $cms['projects'] ?? [];
  $out = [];
  foreach ((array)$rows as $row) {
    if (!is_array($row)) { continue; }
    $visible = isset($row['visible']) ? (string)$row['visible'] : '1';
    if ($visible === '0') { continue; }
    $meta = $row['meta'] ?? [];
    $scope = $row['scope'] ?? [];
    if (!is_array($meta)) { $meta = function_exists('zp_suite_split_pipes') ? zp_suite_split_pipes($meta) : array_values(array_filter(array_map('trim', explode('|', (string)$meta)))); }
    if (!is_array($scope)) { $scope = function_exists('zp_suite_split_pipes') ? zp_suite_split_pipes($scope) : array_values(array_filter(array_map('trim', explode('|', (string)$scope)))); }
    $cat = trim((string)($row['cat'] ?? $row['mode'] ?? 'web'));
    if ($cat === '') { $cat = 'web'; }
    $out[] = [
      'cat'=>$cat,'brand'=>(string)($row['brand']??''),'sub'=>(string)($row['sub']??''),'type'=>(string)($row['type']??''),'year'=>(string)($row['year']??''),'img'=>(string)($row['img']??''),'tag'=>(string)($row['tag']??''),'desc'=>(string)($row['desc']??''),'client'=>(string)($row['client']??''),'services'=>(string)($row['services']??''),'live'=>(string)($row['live']??''),'meta'=>array_values($meta),'scope'=>array_values($scope),'featured'=>!empty($row['featured']) && (string)$row['featured'] !== '0'
    ];
  }
  $out = !empty($out) ? $out : zp_suite_realizacje_default_projects();
  $out = function_exists('zp_suite_realizacje_v19_enrich_projects') ? zp_suite_realizacje_v19_enrich_projects($out) : $out;

  /* Polerstone + Świat Grilli — najnowsze case studies na podstronie /realizacje/.
     Wpisy są dokładane na froncie bez naruszania zapisanych danych CMS i bez duplikowania marek. */
  $priority = [
    [
      'cat'=>'web',
      'brand'=>'OutTech',
      'sub'=>'Nowa strona firmowa + oferta B2B/B2C + formularze serwisowe',
      'type'=>'Strona firmowa / technika zabezpieczeń',
      'year'=>'2026',
      'img'=>'https://zaprojektowani.com/wp-content/uploads/2026/09/outtech_realizacja_compressed-scaled.webp',
      'tag'=>'Systemy bezpieczeństwa / B2B + smart home',
      'desc'=>'Dla OutTech Technika Zabezpieczeń zaprojektowaliśmy nową stronę, która porządkuje rozbudowaną ofertę systemów bezpieczeństwa i pokazuje firmę jako partnera inżynieryjnego, a nie tylko instalatora. Serwis rozdziela rozwiązania dla biznesu i klientów indywidualnych, prowadzi przez alarmy, CCTV, kontrolę dostępu, automatykę i smart home oraz obsługuje zapytania ofertowe i zgłoszenia serwisowe.',
      'client'=>'OutTech Technika Zabezpieczeń',
      'services'=>['UX/UI','WordPress','Architektura informacji','Formularz ofertowy','Formularz serwisowy','SEO','RWD'],
      'live'=>'https://www.outtech.pl/',
      'meta'=>['WordPress','systemy bezpieczeństwa','formularz serwisowy','SEO'],
      'scope'=>[
        'Nowa architektura informacji i uporządkowanie rozbudowanej oferty',
        'Strona główna, O firmie, Realizacje, Blog / aktualności, FAQ i Kontakt',
        'Sekcja Dla biznesu z osobnymi obszarami bezpieczeństwa elektronicznego, nadzoru, ruchu osobowego, automatyzacji i ciągłości działania',
        'Sekcja Inteligentna i bezpieczna przestrzeń dla klientów indywidualnych',
        'Zakres usług: projektowanie, dobór rozwiązań, know-how, integracje, serwis i rozbudowa',
        'Sekcja Jak pracujemy prowadząca od rozpoznania potrzeb do opieki nad instalacją',
        'Rozbudowany formularz ofertowy',
        'Formularz serwisowy z kategorią awarii, typem zgłoszenia, priorytetem i załącznikami',
        'Responsywny projekt desktop, tablet i mobile',
        'Struktura treści przygotowana pod widoczność lokalną w Google na Śląsku i Opolszczyźnie'
      ],
      'tools'=>['WordPress','UX/UI','SEO','Custom Forms','RWD'],
      'results'=>[
        ['Czytelna architektura','Oferta B2B, B2C i zakres usług są rozdzielone w logiczne ścieżki.'],
        ['Obsługa serwisu','Dedykowany formularz porządkuje zgłoszenia, priorytety i załączniki.'],
        ['Ekspercki wizerunek','Serwis podkreśla doświadczenie, know-how i kompleksowe podejście do bezpieczeństwa.']
      ],
      'challenge'=>'Dotychczasowa strona była zbyt ogólna i słabo porządkowała szeroką ofertę OutTech. Wyzwaniem było czytelne rozdzielenie usług dla biznesu i domu, pokazanie zakresu obsługi od projektu po serwis oraz skrócenie drogi do zgłoszenia usterki lub zapytania ofertowego.',
      'solution'=>'Zaprojektowaliśmy nową architekturę informacji z rozbudowanym menu, osobnymi ścieżkami dla biznesu i klientów indywidualnych oraz sekcją zakresu usług. Całość uzupełniliśmy ekspercką, ale zrozumiałą komunikacją, formularzem ofertowym i formularzem serwisowym z obsługą priorytetów oraz załączników.',
      'effect'=>'Powstał nowoczesny serwis inżynieryjny, który lepiej komunikuje kompetencje OutTech, porządkuje ofertę systemów bezpieczeństwa i ułatwia klientom zarówno wybór rozwiązania, jak i kontakt z działem serwisu. Struktura stanowi też bazę pod dalszy rozwój treści i lokalnego SEO.',
      'featured'=>true
    ],
    [
      'cat'=>'web branding',
      'brand'=>'Polerstone',
      'sub'=>'Branding + nowa strona firmowa premium',
      'type'=>'Branding + strona firmowa',
      'year'=>'2026',
      'img'=>'https://zaprojektowani.com/wp-content/uploads/2026/08/polerstone_realizacja-scaled.webp',
      'tag'=>'Kamieniarstwo premium / wnętrza i architektura',
      'desc'=>'Dla Polerstone przygotowaliśmy kompletny kierunek marki i nową stronę internetową, która pokazuje kamień jako element nowoczesnej architektury, a nie tylko materiał wykończeniowy. Projekt połączył branding, identyfikację wizualną, materiały firmowe i rozbudowany serwis prezentujący ofertę, materiały, realizacje, showroom oraz cały proces współpracy — od wyboru płyty po profesjonalny montaż.',
      'client'=>'Polerstone — Usługi Kamieniarskie',
      'services'=>['Branding','Logo i identyfikacja wizualna','UX/UI','WordPress','RWD','Struktura SEO'],
      'live'=>'',
      'meta'=>['branding','WordPress','premium UI','kamień naturalny'],
      'scope'=>[
        'Logo i spójny system identyfikacji wizualnej',
        'Papier firmowy, wizytówki, teczki i stopki mailowe',
        'Indywidualny projekt UX/UI nowej strony internetowej',
        'Prezentacja blatów kuchennych, łazienek, schodów, parapetów, kominków, tarasów i elewacji',
        'Rozbudowana sekcja materiałów: granit, marmur, kwarcyt, onyks, konglomeraty i spieki',
        'Galeria realizacji i prezentacja showroomu kamienia',
        'Sekcja procesu od konsultacji i wyboru materiału po pomiar, obróbkę i montaż',
        'Formularze kontaktowe i czytelna ścieżka umówienia wyceny',
        'Responsywny widok desktop, tablet i mobile',
        'Przygotowanie struktury serwisu pod dalszy rozwój SEO i kampanie'
      ],
      'results'=>[
        ['Spójna marka','Logo, materiały firmowe i strona tworzą jeden konsekwentny system wizualny.'],
        ['Oferta premium','Kamień, realizacje i proces są prezentowane w architektoniczny, czytelny sposób.'],
        ['Gotowe do rozwoju','Serwis ma fundament pod kolejne realizacje, treści SEO i działania reklamowe.']
      ],
      'challenge'=>'Połączyć szeroki zakres usług kamieniarskich i bogactwo naturalnych materiałów w jeden premium wizerunek, który będzie czytelny zarówno dla klienta indywidualnego, jak i architekta czy inwestora.',
      'solution'=>'Zbudowaliśmy spokojny, architektoniczny system oparty na dużych fotografiach kamienia, czarno-grafitowej typografii, mocnym detalu i przejrzystej strukturze. Branding został przeniesiony na stronę, materiały firmowe i wszystkie najważniejsze punkty kontaktu z marką.',
      'effect'=>'Polerstone otrzymało spójny wizerunek premium i nowoczesny serwis, który porządkuje ofertę, podkreśla jakość wykonania oraz prowadzi użytkownika od inspiracji i wyboru materiału do kontaktu i wyceny.',
      'featured'=>true
    ],
    [
      'cat'=>'shop branding',
      'brand'=>'Świat Grilli',
      'sub'=>'Branding + sklep WooCommerce od podstaw',
      'type'=>'Sklep internetowy + branding',
      'year'=>'2026',
      'img'=>'https://zaprojektowani.com/wp-content/uploads/2026/08/swiatgrili_realizacja.webp',
      'tag'=>'Grille / e-commerce multibrand',
      'desc'=>'Dla ŚwiatGrilli.pl stworzyliśmy od podstaw kompletną markę i sklep internetowy dla branży grillowej. Od logo i brandingu, przez projekt UX/UI i strukturę kategorii, po WooCommerce, karty produktów, koszyk, checkout oraz płatności iMoje / ING — całość została zaprojektowana jako spójny system sprzedaży dla wielu marek i szerokiego katalogu produktów.',
      'client'=>'Świat Grilli',
      'services'=>['Logo i branding','UX/UI','WooCommerce','iMoje / ING','Checkout','RWD','SEO'],
      'live'=>'https://www.swiatgrili.pl/',
      'meta'=>['WooCommerce','iMoje / ING','multi-brand','RWD'],
      'scope'=>[
        'Logo i branding marki Świat Grilli',
        'Indywidualny projekt sklepu internetowego od podstaw',
        'Struktura kategorii dla grilli, wędzarni, akcesoriów, kominków i outletu',
        'Prezentacja marek El Fuego, Landmann i Barbecook',
        'Karty produktów i czytelna prezentacja parametrów oraz oferty',
        'Konfiguracja WooCommerce, koszyka i pełnego procesu zakupowego',
        'Integracja bramki płatniczej iMoje / ING',
        'Sekcje budujące zaufanie oraz prezentacja sklepu stacjonarnego',
        'Regulaminy, polityka prywatności i mechanizm cookies',
        'Responsywny widok desktop, tablet i mobile',
        'Przygotowanie struktury sklepu pod SEO i kampanie reklamowe'
      ],
      'results'=>[
        ['E-commerce od zera','Branding, UX/UI i WooCommerce zostały połączone w jednym wdrożeniu.'],
        ['Pełna sprzedaż online','Koszyk, checkout i płatności iMoje / ING tworzą kompletną ścieżkę zakupową.'],
        ['Sklep multi-brand','Oferta wielu producentów jest uporządkowana w czytelnej strukturze kategorii i produktów.']
      ],
      'challenge'=>'Stworzyć od podstaw nową markę e-commerce i uporządkować szeroką ofertę grilli, wędzarni, akcesoriów oraz różnych producentów tak, aby zakup był prosty zarówno dla początkującego, jak i bardziej świadomego klienta.',
      'solution'=>'Zaprojektowaliśmy nowoczesny, produktowy sklep z wyraźnym podziałem kategorii, osobną ekspozycją kluczowych marek, rozbudowanymi kartami produktów i prostą drogą do finalizacji zamówienia. Całość uzupełniliśmy brandingiem, WooCommerce oraz płatnościami online.',
      'effect'=>'Powstał kompletny sklep internetowy gotowy do skalowania oferty, prowadzenia kampanii i rozwijania sprzedaży wielu marek w jednym spójnym środowisku.',
      'featured'=>true
    ]
  ];

  /* v2.2.782 — Natalia Arciszewska + Zgórecki Nieruchomości również na /realizacje/.
     Dokładamy je przed pozostałymi case studies i podajemy komplet danych do kart/modalu.
     Wersja treści dobierana jest bezpośrednio do aktywnego języka strony. */
  $zp_real_is_en = function_exists('zpl_language') && zpl_language() === 'en';
  $zp_real_branding_pl = [
    [
      'cat'=>'branding',
      'brand'=>'Natalia Arciszewska',
      'sub'=>'Logo i identyfikacja wizualna radcy prawnego',
      'type'=>'Logo + mini branding',
      'year'=>'2026',
      'img'=>'https://zaprojektowani.com/wp-content/uploads/2026/09/arciszewska.webp',
      'tag'=>'Prawo / marka osobista',
      'desc'=>'Dla Natalii Arciszewskiej stworzyliśmy spokojną, nowoczesną identyfikację wizualną kancelarii radcy prawnego. Projekt rozpoczął się od 10 autorskich kierunków logo, a wybrana koncepcja została dopracowana w granatowo-beżowej palecie z subtelnym złotym akcentem i przełożona na najważniejsze materiały marki.',
      'client'=>'Natalia Arciszewska — Radca prawny',
      'services'=>['Logo','Mini branding','Papeteria','Wizytówki','Stopka e-mail','Mockupy'],
      'live'=>'',
      'meta'=>['Prawo','Granat + beż','Premium'],
      'scope'=>[
        '10 autorskich propozycji logo i wybór kierunku marki',
        'Dopracowanie znaku, proporcji oraz typografii',
        'Paleta granatowo-beżowa z subtelnym złotym akcentem',
        'Wizytówki z kodem QR i odnośnikiem do LinkedIn',
        'Papier firmowy oraz teczka ofertowa z kieszonką',
        'Stopka e-mail i materiały do codziennej komunikacji',
        'Mockupy i wizualizacje pokazujące identyfikację w praktyce'
      ],
      'tools'=>['Illustrator','Photoshop','Figma','InDesign','Mockupy','Druk'],
      'results'=>[
        ['Spójny system','Logo, typografia i kolorystyka tworzą profesjonalną, łatwo rozpoznawalną identyfikację kancelarii.'],
        ['Materiały gotowe do użycia','Branding został przełożony na wizytówki, papier firmowy, teczkę i stopkę e-mail.'],
        ['Premium bez ciężkości','Granat, beż i subtelne złoto budują zaufanie, ale unikają stereotypowej estetyki kancelarii.']
      ],
      'challenge'=>'Zbudować markę prawniczą, która będzie profesjonalna i premium, ale jednocześnie nowoczesna, lekka i daleka od schematycznych symboli prawniczych.',
      'solution'=>'Przygotowaliśmy 10 kierunków logo, następnie dopracowaliśmy wybraną koncepcję, typografię i paletę granatowo-beżową ze złotym akcentem. System rozszerzyliśmy na najważniejsze materiały firmowe, zachowując spójność online i w druku.',
      'effect'=>'Powstała elegancka i elastyczna identyfikacja, która dobrze działa na dokumentach, wizytówkach, materiałach cyfrowych i stanowi mocną bazę pod dalszą komunikację marki.',
      'featured'=>false
    ],
    [
      'cat'=>'branding',
      'brand'=>'Zgórecki Nieruchomości',
      'sub'=>'Logo i mini branding marki nieruchomości premium',
      'type'=>'Logo + mini branding',
      'year'=>'2026',
      'img'=>'https://zaprojektowani.com/wp-content/uploads/2026/09/zgorecki.webp',
      'tag'=>'Nieruchomości / marka osobista',
      'desc'=>'Dla marki Wojciech Zgórecki Nieruchomości zaprojektowaliśmy elegancki system logo i mini brandingu łączący charakter marki osobistej z estetyką rynku nieruchomości premium. Z 10 przygotowanych kierunków wybraliśmy znak, który następnie dopracowaliśmy pod kątem proporcji, grubości formy, typografii i złotych wariantów kolorystycznych.',
      'client'=>'Wojciech Zgórecki Nieruchomości',
      'services'=>['Logo','Mini branding','Warianty kolorystyczne','Typografia','Mockupy','Materiały marki'],
      'live'=>'',
      'meta'=>['Nieruchomości','Złoto','Premium'],
      'scope'=>[
        '10 propozycji logo i selekcja finalnego kierunku',
        'Dopracowanie proporcji oraz grubości znaku',
        'Złote i neutralne warianty kolorystyczne',
        'Dobór typografii wspierającej charakter marki osobistej',
        'Komplet wersji logo do internetu i druku',
        'Mockupy nieruchomościowe i materiały prezentacyjne',
        'System przygotowany do ofert, social mediów i materiałów sprzedażowych'
      ],
      'tools'=>['Illustrator','Photoshop','Figma','Mockupy','Druk'],
      'results'=>[
        ['Rozpoznawalny podpis marki','Dopracowany symbol i typografia tworzą charakterystyczny znak marki osobistej.'],
        ['Elastyczne warianty','Logo działa w złocie, wersjach neutralnych, online i na materiałach drukowanych.'],
        ['Pozycjonowanie premium','Spokojna kompozycja i eleganckie detale wzmacniają wizerunek marki na rynku nieruchomości.']
      ],
      'challenge'=>'Połączyć osobisty charakter marki pośrednika z estetyką premium, a przy tym zachować wysoką czytelność znaku na małych formatach i materiałach sprzedażowych.',
      'solution'=>'Na bazie 10 kierunków wybraliśmy i dopracowaliśmy finalny znak, korygując jego proporcje, grubość, typografię i odcień złota. Następnie przygotowaliśmy elastyczny zestaw wariantów oraz wizualizacje realnych zastosowań.',
      'effect'=>'Marka otrzymała spójny, elegancki system logo gotowy do wykorzystania w ofertach nieruchomości, social mediach, druku i przyszłej komunikacji online.',
      'featured'=>false
    ]
  ];

  $zp_real_branding_en = [
    [
      'cat'=>'branding',
      'brand'=>'Natalia Arciszewska',
      'sub'=>'Logo and visual identity for a legal counsel',
      'type'=>'Logo + mini branding',
      'year'=>'2026',
      'img'=>'https://zaprojektowani.com/wp-content/uploads/2026/09/arciszewska.webp',
      'tag'=>'Legal services / personal brand',
      'desc'=>'For Natalia Arciszewska we created a calm, modern visual identity for a legal practice. The project started with 10 original logo directions; the selected concept was refined into a navy-and-beige system with a subtle gold accent and then extended across the key brand materials.',
      'client'=>'Natalia Arciszewska — Legal counsel',
      'services'=>['Logo','Mini branding','Stationery','Business cards','Email signature','Mockups'],
      'live'=>'',
      'meta'=>['Legal','Navy + beige','Premium'],
      'scope'=>[
        '10 original logo concepts and selection of the brand direction',
        'Refinement of the mark, proportions and typography',
        'Navy-and-beige palette with a subtle gold accent',
        'Business cards with QR code and LinkedIn link',
        'Letterhead and presentation folder with a pocket',
        'Email signature and everyday corporate materials',
        'Mockups and previews showing the identity in real applications'
      ],
      'tools'=>['Illustrator','Photoshop','Figma','InDesign','Mockups','Print'],
      'results'=>[
        ['Consistent system','Logo, typography and color palette create a professional and recognizable identity for the legal practice.'],
        ['Ready-to-use materials','The branding was extended to business cards, letterhead, a presentation folder and an email signature.'],
        ['Premium without heaviness','Navy, beige and subtle gold build trust while avoiding a stereotypical legal aesthetic.']
      ],
      'challenge'=>'Create a legal brand that feels professional and premium while remaining modern, light and free from predictable legal symbols.',
      'solution'=>'We developed 10 logo directions and then refined the selected concept, typography and navy-and-beige palette with a gold accent. The system was extended across the key corporate materials for consistent use online and in print.',
      'effect'=>'The result is an elegant, flexible identity that works across documents, business cards and digital materials and provides a strong foundation for further brand communication.',
      'featured'=>false
    ],
    [
      'cat'=>'branding',
      'brand'=>'Zgórecki Nieruchomości',
      'sub'=>'Logo and mini branding for a premium real-estate brand',
      'type'=>'Logo + mini branding',
      'year'=>'2026',
      'img'=>'https://zaprojektowani.com/wp-content/uploads/2026/09/zgorecki.webp',
      'tag'=>'Real estate / personal brand',
      'desc'=>'For Wojciech Zgórecki Nieruchomości we designed an elegant logo system and mini branding that combines the personality of the founder with a premium real-estate aesthetic. From 10 initial directions we selected a mark and refined its proportions, line weight, typography and gold color variants.',
      'client'=>'Wojciech Zgórecki Nieruchomości',
      'services'=>['Logo','Mini branding','Color variants','Typography','Mockups','Brand materials'],
      'live'=>'',
      'meta'=>['Real estate','Gold','Premium'],
      'scope'=>[
        '10 logo concepts and selection of the final direction',
        'Refinement of the proportions and mark weight',
        'Gold and neutral color variants',
        'Typography supporting the personal-brand character',
        'Complete logo versions for digital and print use',
        'Real-estate mockups and presentation materials',
        'A system prepared for listings, social media and sales materials'
      ],
      'tools'=>['Illustrator','Photoshop','Figma','Mockups','Print'],
      'results'=>[
        ['Distinctive brand signature','A refined symbol and typography create a recognizable signature for the personal brand.'],
        ['Flexible variants','The logo works in gold and neutral versions across digital channels and printed materials.'],
        ['Premium positioning','A calm composition and elegant details strengthen the brand image in the real-estate market.']
      ],
      'challenge'=>'Combine the personal character of a real-estate advisor brand with a premium aesthetic while keeping the mark highly readable at small sizes and across sales materials.',
      'solution'=>'From 10 initial directions we selected and refined the final mark, adjusting its proportions, weight, typography and gold tone. We then prepared a flexible set of variants and realistic application mockups.',
      'effect'=>'The brand received a consistent, elegant logo system ready for property listings, social media, print and future online communication.',
      'featured'=>false
    ]
  ];

  $priority = array_merge($zp_real_is_en ? $zp_real_branding_en : $zp_real_branding_pl, $priority);

  $priority_slugs = [];
  foreach ($priority as $item) { $priority_slugs[] = sanitize_title((string)($item['brand'] ?? '')); }
  $out = array_values(array_filter($out, function($row) use ($priority_slugs){
    $brand_slug = sanitize_title((string)($row['brand'] ?? ''));
    return !in_array($brand_slug, $priority_slugs, true);
  }));

  return array_merge($priority, $out);
}
function zp_suite_realizacje_categories(){
  $cms = zp_suite_realizacje_cms();
  $cats = $cms['categories'] ?? [];
  $out = [];
  foreach ((array)$cats as $cat) {
    if (!is_array($cat)) { continue; }
    $visible = isset($cat['visible']) ? (string)$cat['visible'] : '1';
    if ($visible === '0') { continue; }
    $slug = sanitize_title((string)($cat['slug'] ?? ''));
    if ($slug === '') { continue; }
    $out[] = ['visible'=>'1','name'=>(string)($cat['name'] ?? $slug),'slug'=>$slug,'icon'=>sanitize_text_field((string)($cat['icon'] ?? 'layout-grid'))];
  }
  return !empty($out) ? $out : zp_suite_realizacje_default_categories();
}
function zp_suite_realizacje_sanitize_rows($rows){ return function_exists('zp_suite_sanitize_repeater') ? zp_suite_sanitize_repeater($rows) : []; }
add_action('admin_menu', function(){ add_submenu_page('zp-suite','Realizacje CMS','Realizacje','manage_options','zp-suite-realizacje','zp_suite_render_realizacje_cms_page'); }, 30);
function zp_realizacje_admin_input($name,$label,$value='',$type='text',$wide=false){
  if (function_exists('zp_admin_input')) { zp_admin_input($name,$label,$value,$type,$wide); return; }
}
function zp_suite_realizacje_project_row($i,$item=[]){ ob_start();
  $title=$item['brand']??'Nowa realizacja'; $sub=$item['sub']??($item['type']??''); $img=$item['img']??''; $cat=$item['cat']??'web';
  $meta=$item['meta']??''; $scope=$item['scope']??''; if(is_array($meta)){$meta=implode('|',$meta);} if(is_array($scope)){$scope=implode('|',$scope);} ?>
  <div class="zpRepeatItem zpRepeatItem--portfolio zpRepeatItem--realizacje"><details><summary><span class="zpRepeatSummaryTitle"><?php if($img): ?><img class="zpRepeatThumb" src="<?php echo esc_url($img); ?>" alt=""><?php endif; ?><span class="zpRepeatSummaryText"><strong><?php echo esc_html($title); ?></strong><span>Realizacja • <?php echo esc_html($cat); ?><?php echo $sub ? ' • '.esc_html($sub) : ''; ?></span></span></span><span class="zpRepeatChevron">›</span></summary><div class="zpRepeatTop"><strong>Projekt do podstrony Realizacje</strong><button class="button zpRemoveRow">Usuń</button></div><div class="zpRepeatGrid">
    <?php zp_realizacje_admin_input("zp_realizacje[projects][$i][visible]",'Pokaż? 1/0',$item['visible']??'1'); ?>
    <?php zp_realizacje_admin_input("zp_realizacje[projects][$i][featured]",'Wyróżniona? 1/0',$item['featured']??'0'); ?>
    <?php zp_realizacje_admin_input("zp_realizacje[projects][$i][cat]",'Kategorie/filtry, np. web shop branding',$cat); ?>
    <?php zp_realizacje_admin_input("zp_realizacje[projects][$i][brand]",'Nazwa projektu',$item['brand']??''); ?>
    <?php zp_realizacje_admin_input("zp_realizacje[projects][$i][sub]",'Krótki opis do karty',$item['sub']??''); ?>
    <?php zp_realizacje_admin_input("zp_realizacje[projects][$i][type]",'Typ / podpis',$item['type']??''); ?>
    <?php zp_realizacje_admin_input("zp_realizacje[projects][$i][year]",'Rok',$item['year']??''); ?>
    <?php zp_realizacje_admin_input("zp_realizacje[projects][$i][tag]",'Branża / tag główny',$item['tag']??''); ?>
    <?php zp_realizacje_admin_input("zp_realizacje[projects][$i][live]",'Link live',$item['live']??''); ?>
    <?php zp_realizacje_admin_input("zp_realizacje[projects][$i][img]",'Zdjęcie / mockup',$item['img']??'','media',true); ?>
    <?php zp_realizacje_admin_input("zp_realizacje[projects][$i][client]",'Klient',$item['client']??''); ?>
    <?php zp_realizacje_admin_input("zp_realizacje[projects][$i][services]",'Usługi / zakres główny',$item['services']??''); ?>
    <?php zp_realizacje_admin_input("zp_realizacje[projects][$i][meta]",'Tagi na karcie, oddziel |',$meta,'text',true); ?>
    <?php zp_realizacje_admin_input("zp_realizacje[projects][$i][desc]",'Opis szczegółowy do modalu',$item['desc']??'','textarea',true); ?>
    <?php zp_realizacje_admin_input("zp_realizacje[projects][$i][scope]",'Zakres prac / punkty w modalu, oddziel |',$scope,'textarea',true); ?>
  </div></details></div><?php return ob_get_clean(); }
function zp_suite_realizacje_category_row($i,$item=[]){ ob_start(); ?>
  <div class="zpRepeatItem"><div class="zpRepeatTop"><strong>Kategoria / filtr</strong><button class="button zpRemoveRow">Usuń</button></div><div class="zpRepeatGrid">
    <?php zp_realizacje_admin_input("zp_realizacje[categories][$i][visible]",'Pokaż? 1/0',$item['visible']??'1'); ?>
    <?php zp_realizacje_admin_input("zp_realizacje[categories][$i][name]",'Nazwa',$item['name']??''); ?>
    <?php zp_realizacje_admin_input("zp_realizacje[categories][$i][slug]",'Slug filtra, np. web / shop / branding',$item['slug']??''); ?>
    <?php zp_realizacje_admin_input("zp_realizacje[categories][$i][icon]",'Ikona Lucide',$item['icon']??'layout-grid'); ?>
  </div></div><?php return ob_get_clean(); }
function zp_suite_render_realizacje_cms_page(){
  if (!current_user_can('manage_options')) { return; }
  $home = function_exists('zp_suite_cms') ? zp_suite_cms() : [];
  $real = zp_suite_realizacje_cms();
  if (!empty($_POST['zp_realizacje_save']) && check_admin_referer('zp_suite_realizacje_save')) {
    if (isset($_POST['zp_cms']) && is_array($_POST['zp_cms']) && function_exists('zp_suite_sanitize_repeater')) {
      $raw_home = wp_unslash($_POST['zp_cms']); $current = function_exists('zp_suite_cms') ? zp_suite_cms() : [];
      if (!isset($current['portfolio']) || !is_array($current['portfolio'])) { $current['portfolio']=['web'=>[],'logo'=>[]]; }
      $current['portfolio']['web']=zp_suite_sanitize_repeater($raw_home['portfolio']['web'] ?? []);
      $current['portfolio']['logo']=zp_suite_sanitize_repeater($raw_home['portfolio']['logo'] ?? []);
      update_option('zp_suite_cms', $current, false);
    }
    $raw = isset($_POST['zp_realizacje']) && is_array($_POST['zp_realizacje']) ? wp_unslash($_POST['zp_realizacje']) : [];
    $save = ['projects'=>zp_suite_realizacje_sanitize_rows($raw['projects'] ?? []),'categories'=>zp_suite_realizacje_sanitize_rows($raw['categories'] ?? [])];
    if (empty($save['projects'])) { $save['projects']=zp_suite_realizacje_default_projects(); }
    if (empty($save['categories'])) { $save['categories']=zp_suite_realizacje_default_categories(); }
    update_option('zp_suite_realizacje_cms',$save,false); $home=function_exists('zp_suite_cms')?zp_suite_cms():[]; $real=zp_suite_realizacje_cms();
    echo '<div class="notice notice-success is-dismissible"><p>Zapisano Realizacje CMS.</p></div>';
  }
  $home_web=$home['portfolio']['web']??[]; $home_logo=$home['portfolio']['logo']??[]; $projects=$real['projects']??[]; $categories=$real['categories']??[]; ?>
  <div class="wrap zpSuiteAdmin zpSuiteRealizacjeAdmin"><div class="zpSuiteHero"><span class="zpSuiteBadge">ZAPROJEKTOWANI SUITE • REALIZACJE CMS</span><h1>Realizacje i portfolio w jednym miejscu.</h1><p>Zarządzasz osobno portfolio na stronie głównej i pełną podstroną Realizacje. Projekty z podstrony mają pełne dane do kart, filtrów i modalu szczegółów.</p><div class="zpStatus"><div class="zpStat"><strong><?php echo (int)count($home_web)+(int)count($home_logo); ?></strong><span>home portfolio</span></div><div class="zpStat"><strong><?php echo (int)count($projects); ?></strong><span>realizacje</span></div><div class="zpStat"><strong>[zp_realizacje]</strong><span>shortcode</span></div></div></div>
  <form method="post"><?php wp_nonce_field('zp_suite_realizacje_save'); ?><input type="hidden" name="zp_realizacje_save" value="1"><div class="zpTabs"><button type="button" class="is-active" data-tab="zpRealTabHome">Portfolio strona główna</button><button type="button" data-tab="zpRealTabProjects">Portfolio realizacje</button><button type="button" data-tab="zpRealTabCats">Kategorie</button><button type="button" data-tab="zpRealTabShortcode">Shortcode</button></div>
  <section id="zpRealTabHome" class="zpPanel is-active"><div class="zpGrid"><div class="zpCard"><h2>Strony / sklepy / systemy — strona główna</h2><p>To jest obecne portfolio z home.</p><div class="zpToolsBar"><button class="button zpExpandAll" data-target="#zpRealHomeWeb" type="button">Rozwiń wszystko</button><button class="button zpCollapseAll" data-target="#zpRealHomeWeb" type="button">Zwiń wszystko</button></div><div id="zpRealHomeWeb" class="zpRepeater"><?php foreach($home_web as $i=>$row) echo function_exists('zp_portfolio_row') ? zp_portfolio_row('web',$i,$row) : ''; ?></div><button class="button zpAddRow" data-repeater="#zpRealHomeWeb" data-template="#zpTplRealHomeWeb" type="button">Dodaj projekt WEB</button></div><div class="zpCard"><h2>Logo / branding — strona główna</h2><div class="zpToolsBar"><button class="button zpExpandAll" data-target="#zpRealHomeLogo" type="button">Rozwiń wszystko</button><button class="button zpCollapseAll" data-target="#zpRealHomeLogo" type="button">Zwiń wszystko</button></div><div id="zpRealHomeLogo" class="zpRepeater"><?php foreach($home_logo as $i=>$row) echo function_exists('zp_portfolio_row') ? zp_portfolio_row('logo',$i,$row) : ''; ?></div><button class="button zpAddRow" data-repeater="#zpRealHomeLogo" data-template="#zpTplRealHomeLogo" type="button">Dodaj projekt logo</button></div></div></section>
  <section id="zpRealTabProjects" class="zpPanel"><div class="zpGrid zpGrid1"><div class="zpCard"><h2>Portfolio realizacje — podstrona</h2><p>Te pozycje zasilają shortcode <code>[zp_realizacje]</code>.</p><div class="zpToolsBar"><button class="button zpExpandAll" data-target="#zpRealProjects" type="button">Rozwiń wszystko</button><button class="button zpCollapseAll" data-target="#zpRealProjects" type="button">Zwiń wszystko</button></div><div id="zpRealProjects" class="zpRepeater"><?php foreach($projects as $i=>$row) echo zp_suite_realizacje_project_row($i,$row); ?></div><button class="button zpAddRow" data-repeater="#zpRealProjects" data-template="#zpTplRealProject" type="button">Dodaj realizację</button></div></div></section>
  <section id="zpRealTabCats" class="zpPanel"><div class="zpGrid zpGrid1"><div class="zpCard"><h2>Kategorie i filtry</h2><p>Slug kategorii musi odpowiadać wartości wpisanej w polu „Kategorie/filtry” projektu.</p><div id="zpRealCats" class="zpRepeater"><?php foreach($categories as $i=>$row) echo zp_suite_realizacje_category_row($i,$row); ?></div><button class="button zpAddRow" data-repeater="#zpRealCats" data-template="#zpTplRealCat" type="button">Dodaj kategorię</button></div></div></section>
  <section id="zpRealTabShortcode" class="zpPanel"><div class="zpGrid zpGrid1"><div class="zpCard"><h2>Shortcode</h2><pre>[zp_realizacje]</pre><p>Alias: <code>[zp_page_realizacje]</code></p></div></div></section><div class="zpSave"><button class="button button-primary button-large">Zapisz wszystkie realizacje</button></div></form>
  <template id="zpTplRealHomeWeb"><?php echo function_exists('zp_portfolio_row') ? zp_portfolio_row('web','__i__', []) : ''; ?></template><template id="zpTplRealHomeLogo"><?php echo function_exists('zp_portfolio_row') ? zp_portfolio_row('logo','__i__', []) : ''; ?></template><template id="zpTplRealProject"><?php echo zp_suite_realizacje_project_row('__i__', []); ?></template><template id="zpTplRealCat"><?php echo zp_suite_realizacje_category_row('__i__', []); ?></template></div><?php
}
add_action('plugins_loaded', function(){
  if (get_option('zp_suite_realizacje_content_version') !== '2.2.48') {
    $saved=get_option('zp_suite_realizacje_cms',[]);
    if (empty($saved) || !is_array($saved) || empty($saved['projects'])) { update_option('zp_suite_realizacje_cms',['projects'=>zp_suite_realizacje_default_projects(),'categories'=>zp_suite_realizacje_default_categories()],false); }
    update_option('zp_suite_realizacje_content_version','2.2.48',false);
  }
},25);


/* ZP Suite v2.2.76 — add/update ShotHome in full Realizacje CMS. */
if (!function_exists('zp_suite_realizacje_shothome_project')) {
  function zp_suite_realizacje_shothome_project(){
    return [
      'visible'=>'1','cat'=>'web','brand'=>'ShotHome','sub'=>'strona usługowa + SEO + darmowa wycena online','type'=>'Strona + SEO + darmowa wycena','year'=>'2026',
      'img'=>'https://zaprojektowani.com/wp-content/uploads/2026/05/shothome_realizacja-2_11zon.webp','tag'=>'Drzwi, podłogi i wykończenia / Warszawa',
      'desc'=>'Dla ShotHome stworzyliśmy od podstaw stronę usługową, która skraca drogę od przeglądania oferty do wysłania zapytania. Projekt objął indywidualny UI, wdrożenie WordPress, strukturę SEO pod Warszawę, blog, customową darmową wycenę online, system realizacji oraz katalog produktów podłogowych z możliwością dodawania i edycji w panelu.',
      'client'=>'ShotHome','services'=>'Projekt graficzny, WordPress, SEO, blog, custom wycena online, system realizacji, katalog produktów','live'=>'https://www.shothome.pl/',
      'meta'=>['darmowa wycena','SEO Warszawa','panel realizacji','katalog podłóg'],
      'scope'=>['Projekt strony od podstaw','Indywidualny projekt graficzny w kolorystyce marki','Wdrożenie WordPress','Customowy formularz darmowej wyceny online','Panel realizacji: dodawanie, edytowanie i usuwanie projektów','Katalog produktów podłogowych z obsługą w panelu','Blog i struktura treści pod SEO','Optymalizacja lokalna pod Warszawę','Responsywny widok mobile'],
      'featured'=>'0'
    ];
  }
}
if (!function_exists('zp_suite_realizacje_upsert_shothome')) {
  function zp_suite_realizacje_upsert_shothome(){
    $data = get_option('zp_suite_realizacje_cms', []);
    if (!is_array($data) || empty($data)) { $data = ['projects'=>zp_suite_realizacje_default_projects(), 'categories'=>zp_suite_realizacje_default_categories()]; }
    if (empty($data['projects']) || !is_array($data['projects'])) { $data['projects'] = zp_suite_realizacje_default_projects(); }
    if (empty($data['categories']) || !is_array($data['categories'])) { $data['categories'] = zp_suite_realizacje_default_categories(); }
    $shot = zp_suite_realizacje_shothome_project();
    $rows = [];
    foreach ((array)$data['projects'] as $row) {
      if (!is_array($row)) { continue; }
      $brand = strtolower(trim((string)($row['brand'] ?? '')));
      if ($brand === 'shothome' || $brand === 'shot home') { continue; }
      $rows[] = $row;
    }
    $inserted = false; $out = [];
    foreach ($rows as $row) {
      $out[] = $row;
      $brand = strtolower(trim((string)($row['brand'] ?? '')));
      if (!$inserted && $brand === 'gravia') { $out[] = $shot; $inserted = true; }
    }
    if (!$inserted) { $pos = min(4, count($out)); array_splice($out, $pos, 0, [$shot]); }
    $data['projects'] = $out;
    update_option('zp_suite_realizacje_cms', $data, false);
  }
}
add_action('plugins_loaded', function(){
  if (get_option('zp_suite_realizacje_shothome_migration') !== '2.2.76') {
    zp_suite_realizacje_upsert_shothome();
    update_option('zp_suite_realizacje_shothome_migration', '2.2.76', false);
  }
}, 32);

/* ZP Suite v2.2.770 — add OUTTECH to Realizacje CMS. */
if (!function_exists('zp_suite_realizacje_outtech_project')) {
  function zp_suite_realizacje_outtech_project(){
    return [
      'visible'=>'1','featured'=>'1','cat'=>'web','brand'=>'OutTech','sub'=>'Nowa strona firmowa + oferta B2B/B2C + formularze serwisowe','type'=>'Strona firmowa / technika zabezpieczeń','year'=>'2026',
      'img'=>'https://zaprojektowani.com/wp-content/uploads/2026/09/outtech_realizacja_compressed-scaled.webp','tag'=>'Systemy bezpieczeństwa / B2B + smart home',
      'desc'=>'Dla OutTech Technika Zabezpieczeń zaprojektowaliśmy nową stronę, która porządkuje rozbudowaną ofertę systemów bezpieczeństwa i pokazuje firmę jako partnera inżynieryjnego, a nie tylko instalatora. Serwis rozdziela rozwiązania dla biznesu i klientów indywidualnych, prowadzi przez alarmy, CCTV, kontrolę dostępu, automatykę i smart home oraz obsługuje zapytania ofertowe i zgłoszenia serwisowe.',
      'client'=>'OutTech Technika Zabezpieczeń','services'=>'UX/UI, WordPress, architektura informacji, formularz ofertowy, formularz serwisowy, SEO, RWD','live'=>'https://www.outtech.pl/',
      'meta'=>['WordPress','systemy bezpieczeństwa','formularz serwisowy','SEO'],
      'scope'=>['Nowa architektura informacji i uporządkowanie rozbudowanej oferty','Strona główna, O firmie, Realizacje, Blog / aktualności, FAQ i Kontakt','Sekcja Dla biznesu z osobnymi obszarami bezpieczeństwa elektronicznego, nadzoru, ruchu osobowego, automatyzacji i ciągłości działania','Sekcja Inteligentna i bezpieczna przestrzeń dla klientów indywidualnych','Zakres usług: projektowanie, dobór rozwiązań, know-how, integracje, serwis i rozbudowa','Sekcja Jak pracujemy prowadząca od rozpoznania potrzeb do opieki nad instalacją','Rozbudowany formularz ofertowy','Formularz serwisowy z kategorią awarii, typem zgłoszenia, priorytetem i załącznikami','Responsywny projekt desktop, tablet i mobile','Struktura treści przygotowana pod widoczność lokalną w Google na Śląsku i Opolszczyźnie']
    ];
  }
}
if (!function_exists('zp_suite_realizacje_upsert_outtech')) {
  function zp_suite_realizacje_upsert_outtech(){
    $data = get_option('zp_suite_realizacje_cms', []);
    if (!is_array($data) || empty($data)) { $data = ['projects'=>zp_suite_realizacje_default_projects(), 'categories'=>zp_suite_realizacje_default_categories()]; }
    if (empty($data['projects']) || !is_array($data['projects'])) { $data['projects'] = zp_suite_realizacje_default_projects(); }
    if (empty($data['categories']) || !is_array($data['categories'])) { $data['categories'] = zp_suite_realizacje_default_categories(); }
    $outtech = zp_suite_realizacje_outtech_project();
    $rows = [];
    foreach ((array)$data['projects'] as $row) {
      if (!is_array($row)) { continue; }
      $brand = strtolower(trim((string)($row['brand'] ?? '')));
      if ($brand === 'outtech' || $brand === 'out tech') { continue; }
      $rows[] = $row;
    }
    array_unshift($rows, $outtech);
    $data['projects'] = $rows;
    update_option('zp_suite_realizacje_cms', $data, false);
  }
}
add_action('plugins_loaded', function(){
  if (get_option('zp_suite_realizacje_outtech_migration') !== '2.2.770') {
    zp_suite_realizacje_upsert_outtech();
    update_option('zp_suite_realizacje_outtech_migration', '2.2.770', false);
  }
}, 35);

