<?php
/** Approved Realizacje v19 copy/content overlay. */
if (!defined('ABSPATH')) { exit; }

function zp_suite_realizacje_v19_key($row){
  $brand = strtolower(trim((string)($row['brand'] ?? '')));
  $cat = strtolower(trim((string)($row['cat'] ?? '')));
  $type = strtolower(trim((string)($row['type'] ?? '')));
  return $brand . '|' . $cat . '|' . $type;
}

function zp_suite_realizacje_v19_content(){
  static $data = null;
  if ($data !== null) { return $data; }
  $data = [
    "proscarves|shop|b2b custom / sklep sportowy" => [
      "sub" => "E-commerce + zamówienia hurtowe",
      "type" => "B2B custom / sklep sportowy",
      "tag" => "B2B custom / sklep sportowy",
      "desc" => "Dla ProScarves zaprojektowaliśmy rozbudowany sklep sportowy łączący sprzedaż detaliczną z obsługą zamówień klubowych i hurtowych. Klient indywidualny może szybko kupić gotowy produkt, a klub lub organizacja przechodzi przez dedykowaną ścieżkę zapytania i wyceny produkcji customowej. Całość otrzymała dynamiczny, międzynarodowy charakter dopasowany do rynku kibicowskiego.",
      "client" => "ProScarves",
      "services" => [
        "UX/UI",
        "WooCommerce",
        "sklep detaliczny",
        "system wyceny B2B",
        "formularze custom orders",
        "wdrożenie WordPress"
      ],
      "scope" => [
        "Indywidualny projekt graficzny sklepu",
        "Sklep detaliczny z gotowymi produktami dla fanów",
        "Customowy system wyceny dla klubów i fan clubów",
        "Ścieżka zapytań B2B dla klubów, firm i organizacji",
        "Prezentacja produkcji customowej",
        "Struktura pod rynki międzynarodowe",
        "Responsywna wersja mobile",
        "Podstawy SEO, analityka i przygotowanie pod kampanie"
      ],
      "results" => [
        [
          "2 ścieżki sprzedaży",
          "Detaliczny sklep i dedykowane zapytania klubowe w jednym serwisie."
        ],
        [
          "Custom B2B",
          "System zbierający komplet danych potrzebnych do przygotowania wyceny."
        ],
        [
          "Gotowość na rozwój",
          "Struktura przygotowana pod kolejne rynki, kategorie i warianty produktów."
        ]
      ],
      "challenge" => "Połączenie dwóch różnych modeli sprzedaży w jednym serwisie bez komplikowania ścieżki zakupowej.",
      "solution" => "Rozdzieliliśmy e-commerce detaliczny i zapytania B2B, projektując osobne komunikaty, formularze oraz prezentację produkcji customowej.",
      "effect" => "Spójna platforma, która obsługuje klientów indywidualnych, kluby i partnerów biznesowych w jednym ekosystemie.",
      "featured" => true
    ],
    "siemianowski|web branding|kancelaria premium / prawo i restrukturyzacja" => [
      "sub" => "Strona firmowa + branding + wizyty",
      "type" => "Kancelaria premium / prawo i restrukturyzacja",
      "tag" => "Kancelaria premium / prawo i restrukturyzacja",
      "desc" => "Dla kancelarii Arkadiusza Siemianowskiego stworzyliśmy kompletny wizerunek premium oraz rozbudowaną stronę ekspercką. Projekt porządkuje specjalizacje prawne i restrukturyzacyjne, eksponuje doświadczenie oraz prowadzi użytkownika do kontaktu lub umówienia wizyty. Stonowany design buduje poczucie bezpieczeństwa, profesjonalizmu i wysokiej jakości obsługi.",
      "client" => "Kancelaria Adwokata i Doradcy Restrukturyzacyjnego Arkadiusz Siemianowski",
      "services" => [
        "Branding",
        "projekt strony",
        "WordPress",
        "UX",
        "copywriting",
        "formularze kontaktowe",
        "umawianie wizyt"
      ],
      "scope" => [
        "Kierunek wizualny marki premium",
        "Logo i system identyfikacji",
        "Projekt rozbudowanej strony kancelarii",
        "Struktura specjalizacji prawnych i restrukturyzacyjnych",
        "Sekcje zaufania, doświadczenia i kontaktu",
        "Elementy umawiania wizyt",
        "Responsywny widok mobile",
        "Przygotowanie pod treści eksperckie i SEO"
      ],
      "results" => [
        [
          "Marka premium",
          "Logo, typografia, kolorystyka i strona tworzą jeden konsekwentny system."
        ],
        [
          "Prostszy kontakt",
          "Czytelne CTA i możliwość przejścia bezpośrednio do umawiania wizyty."
        ],
        [
          "Baza pod SEO",
          "Struktura przygotowana pod rozwój specjalizacji i treści eksperckich."
        ]
      ],
      "challenge" => "Zbudowanie zaufania jeszcze przed pierwszą rozmową oraz czytelne przedstawienie szerokiego zakresu specjalizacji.",
      "solution" => "Połączyliśmy elegancki branding z uporządkowaną architekturą informacji, treściami eksperckimi i prostą ścieżką umawiania spotkań.",
      "effect" => "Spójna marka kancelarii i serwis, który wspiera pozyskiwanie klientów oraz rozwój widoczności eksperckiej.",
      "featured" => true
    ],
    "apartament piękna|web branding|strona + rezerwacje + seo" => [
      "sub" => "Nowa strona, rezerwacje online, branding i SEO",
      "type" => "Strona + Rezerwacje + SEO",
      "tag" => "Beauty premium / medycyna estetyczna",
      "desc" => "Dla Apartamentu Piękna przeprowadziliśmy pełny redesign obecności marki w internecie. Odświeżyliśmy identyfikację, zaprojektowaliśmy elegancką stronę beauty i wdrożyliśmy autorski system rezerwacji z przypomnieniami SMS. Projekt łączy estetykę premium z wygodną obsługą wizyt i lokalnym SEO.",
      "client" => "Apartament Piękna — Tarnów",
      "services" => [
        "Redesign strony",
        "branding",
        "WordPress",
        "rezerwacje online",
        "blog SEO",
        "analityka"
      ],
      "scope" => [
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
      "results" => [
        [
          "Rezerwacje 24/7",
          "Klientki mogą samodzielnie wybrać usługę i termin wizyty."
        ],
        [
          "Automatyzacja SMS",
          "Przypomnienia ograniczają liczbę zapomnianych wizyt."
        ],
        [
          "Widoczność lokalna",
          "Struktura i blog przygotowane pod frazy z Tarnowa i okolic."
        ]
      ],
      "challenge" => "Uproszczenie rezerwacji zabiegów oraz uporządkowanie rozbudowanej oferty bez utraty luksusowego charakteru marki.",
      "solution" => "Zaprojektowaliśmy przejrzyste kategorie zabiegów, system rezerwacji 24/7, przypomnienia SMS i strukturę treści pod lokalne wyszukiwania.",
      "effect" => "Nowoczesny serwis, który odciąża recepcję, wspiera sprzedaż usług i konsekwentnie buduje wizerunek salonu premium.",
      "featured" => false
    ],
    "gravia|web|strona firmowa + kariera" => [
      "sub" => "Strona dla biura projektów infrastrukturalnych",
      "type" => "Strona firmowa + Kariera",
      "tag" => "Infrastruktura / branża drogowa",
      "desc" => "Dla biura projektów infrastrukturalnych Gravia stworzyliśmy techniczną, nowoczesną stronę pokazującą skalę kompetencji i realizacji. Projekt otrzymał uporządkowaną ofertę, rozbudowane portfolio oraz autorski moduł kariery z obsługą CV i załączników. Wizualnie połączyliśmy precyzję branży inżynieryjnej z lekkim, współczesnym UI.",
      "client" => "Gravia — Biuro Projektów Infrastrukturalnych",
      "services" => [
        "Web Design",
        "WordPress Dev",
        "UX",
        "System kariery"
      ],
      "scope" => [
        "Indywidualny projekt graficzny strony",
        "Nowoczesny układ pod branżę infrastrukturalną",
        "Wersja desktop i mobile",
        "Podstrony ofertowe i informacyjne",
        "Formularze kontaktowe",
        "Rozbudowana sekcja realizacji",
        "Autorski system kariery z obsługą CV i plików"
      ],
      "results" => [
        [
          "System kariery",
          "Dedykowane oferty pracy, formularze i bezpieczna obsługa plików CV."
        ],
        [
          "Portfolio realizacji",
          "Czytelna prezentacja projektów infrastrukturalnych i kompetencji zespołu."
        ],
        [
          "Techniczny wizerunek",
          "Design dopasowany do branży inżynieryjnej i klientów instytucjonalnych."
        ]
      ],
      "challenge" => "Przedstawienie złożonych usług technicznych w sposób zrozumiały dla inwestorów i atrakcyjny dla kandydatów.",
      "solution" => "Zbudowaliśmy klarowną strukturę usług, katalog realizacji i dedykowany system rekrutacyjny dostępny z poziomu panelu.",
      "effect" => "Profesjonalna platforma firmowa wspierająca sprzedaż B2B, prezentację doświadczenia i pozyskiwanie pracowników.",
      "featured" => false
    ],
    "shothome|web|strona + seo + darmowa wycena" => [
      "sub" => "Strona usługowa + SEO + darmowa wycena online",
      "type" => "Strona + SEO + darmowa wycena",
      "tag" => "Drzwi, podłogi i wykończenia / Warszawa",
      "desc" => "Dla ShotHome zaprojektowaliśmy stronę usługową nastawioną na szybkie pozyskiwanie zapytań z Warszawy. Serwis łączy ofertę drzwi, podłóg i wykończeń z customową darmową wyceną, katalogiem produktów oraz panelem realizacji. Całość przygotowaliśmy pod kampanie reklamowe, lokalne SEO i wygodną samodzielną edycję.",
      "client" => "ShotHome",
      "services" => [
        "Projekt graficzny",
        "WordPress",
        "SEO",
        "blog",
        "custom wycena online",
        "system realizacji",
        "katalog produktów"
      ],
      "scope" => [
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
      "results" => [
        [
          "Wycena online",
          "Klient przekazuje zakres prac i oczekiwania bez długiej wymiany wiadomości."
        ],
        [
          "Własny panel",
          "Zespół samodzielnie aktualizuje produkty, realizacje i treści."
        ],
        [
          "SEO Warszawa",
          "Struktura dopasowana do lokalnych usług i kampanii reklamowych."
        ]
      ],
      "challenge" => "Skrócenie drogi od zainteresowania ofertą do konkretnego zapytania oraz uporządkowanie wielu grup produktowych.",
      "solution" => "Wdrożyliśmy konfigurator wyceny, moduł realizacji, katalog produktów i rozbudowaną strukturę landingów usługowych.",
      "effect" => "Serwis pełniący jednocześnie funkcję prezentacji oferty, generatora leadów i bazy treści pod marketing lokalny.",
      "featured" => false
    ],
    "krawiec z dojazdem|web branding|system + rebranding" => [
      "sub" => "Rebranding, nowa strona i system wizyt",
      "type" => "System + Rebranding",
      "tag" => "Usługi premium / lokalnie",
      "desc" => "Dla usługi krawieckiej premium przygotowaliśmy rebranding, nową stronę oraz system umawiania wizyt z dojazdem do klienta. Projekt podkreśla indywidualne podejście, wygodę i wysoką jakość obsługi, jednocześnie upraszczając rezerwację spotkania. Mobile-first pozwala sprawnie umówić wizytę bezpośrednio z telefonu.",
      "client" => "Krawiec z dojazdem",
      "services" => [
        "Branding",
        "Web Design",
        "WordPress Dev",
        "SEO"
      ],
      "meta" => [
        "premium UI",
        "mobile-first",
        "SEO-ready"
      ],
      "scope" => [
        "Rebranding identyfikacji wizualnej",
        "Strona WordPress od zera",
        "System umawiania wizyt online",
        "Optymalizacja SEO lokalne",
        "Integracja Google Analytics",
        "Design mobile-first"
      ],
      "results" => [
        [
          "Nowy wizerunek",
          "Rebranding porządkujący komunikację usługi premium."
        ],
        [
          "Wizyty online",
          "Prosty proces umawiania spotkania z dojazdem."
        ],
        [
          "Mobile-first",
          "Najważniejsze działania dostępne wygodnie z telefonu."
        ]
      ],
      "challenge" => "Wyjaśnienie nietypowego modelu usługi i zamiana zainteresowania w konkretną rezerwację wizyty.",
      "solution" => "Połączyliśmy elegancki wizerunek z prostą ścieżką usług, lokalizacją dojazdu i kalendarzem spotkań.",
      "effect" => "Spójna marka premium i narzędzie, które wspiera pozyskiwanie lokalnych klientów bez ręcznego ustalania każdego terminu.",
      "featured" => false
    ],
    "papeterio|shop|sklep woocommerce" => [
      "sub" => "Subtelny sklep online z papeterią i personalizacją",
      "type" => "Sklep WooCommerce",
      "tag" => "Papeteria / e-commerce",
      "desc" => "Dla Papeterio stworzyliśmy subtelny sklep internetowy z papeterią i dodatkami na wyjątkowe okazje. Kluczowe było połączenie delikatnej estetyki marki z czytelną personalizacją produktów, wariantami i dodatkami. Projekt prowadzi użytkownika spokojnie od inspiracji do finalizacji zamówienia.",
      "client" => "Papeterio",
      "services" => [
        "Web Design",
        "WooCommerce",
        "UX",
        "Payments"
      ],
      "scope" => [
        "Sklep internetowy z kategoriami produktów",
        "Dopracowane karty produktów",
        "Opcje personalizacji zamówienia",
        "Warianty i dodatki",
        "Koszyk i płatności online",
        "Formularze kontaktowe",
        "Wersja mobilna dopasowana do zakupów z telefonu"
      ],
      "results" => [
        [
          "Czytelna personalizacja",
          "Warianty, teksty i dodatki są wybierane bezpośrednio przy produkcie."
        ],
        [
          "Zakupy mobilne",
          "Interfejs dopracowany pod klientów zamawiających z telefonu."
        ],
        [
          "Spójna estetyka",
          "Sklep, opakowania i komunikacja tworzą jednolite doświadczenie marki."
        ]
      ],
      "challenge" => "Pokazanie wielu wariantów personalizacji w sposób prosty, estetyczny i niewymagający dodatkowego kontaktu.",
      "solution" => "Uporządkowaliśmy opcje produktu, dodatki i pola personalizacji, projektując lekkie karty produktowe oraz wygodny koszyk.",
      "effect" => "Elegancki e-commerce, który zachowuje emocjonalny charakter marki, a jednocześnie ułatwia składanie złożonych zamówień.",
      "featured" => false
    ],
    "bransoletka24|shop|sklep woocommerce" => [
      "sub" => "Sklep online z hurtem i detalem",
      "type" => "Sklep WooCommerce",
      "tag" => "E-commerce / detal + hurt",
      "desc" => "Bransoletka24 otrzymała nowy sklep WooCommerce obsługujący równolegle sprzedaż detaliczną i hurtową. Zaprojektowaliśmy przejrzyste kategorie, szybkie filtrowanie, warianty produktów, wishlistę i uproszczony checkout. Całość powstała z naciskiem na konwersję, szybkość oraz wygodę klientów B2C i B2B.",
      "client" => "Bransoletka24",
      "services" => [
        "Web Design",
        "WooCommerce",
        "Payments",
        "UX"
      ],
      "meta" => [
        "WooCommerce",
        "checkout",
        "B2B + B2C"
      ],
      "scope" => [
        "Sklep WooCommerce od zera",
        "System hurtowy z rejestracją B2B",
        "Integracja płatności online",
        "Wishlist i warianty produktów",
        "Animacje GSAP",
        "Optymalizacja konwersji"
      ],
      "results" => [
        [
          "B2C + B2B",
          "Jeden sklep z osobnymi zasadami obsługi klientów detalicznych i hurtowych."
        ],
        [
          "Szybszy wybór",
          "Filtry, warianty i wishlista ułatwiają pracę z szerokim katalogiem."
        ],
        [
          "Lepszy checkout",
          "Ograniczona liczba kroków i czytelne podsumowanie zamówienia."
        ]
      ],
      "challenge" => "Obsługa dwóch grup klientów i rozbudowanego katalogu bez przeciążania interfejsu.",
      "solution" => "Wdrożyliśmy osobne mechanizmy cenowe dla hurtu, intuicyjne filtry, listę ulubionych i uproszczoną ścieżkę zakupu.",
      "effect" => "Skalowalny sklep, który porządkuje katalog i ułatwia zakupy zarówno klientom indywidualnym, jak i partnerom hurtowym.",
      "featured" => false
    ],
    "rutpoż|web|system + ai" => [
      "sub" => "Strona, system zgłoszeń i asystent AI",
      "type" => "System + AI",
      "tag" => "PPOŻ / system zgłoszeń + AI",
      "desc" => "Dla RUTPOŻ stworzyliśmy rozbudowaną platformę firmową łączącą ofertę PPOŻ z narzędziami użytkowymi. Serwis zawiera system zgłoszeń serwisowych, kalkulatory branżowe, generowanie raportów PDF oraz asystenta AI odpowiadającego na pytania klientów. Projekt zamienia klasyczną stronę firmową w praktyczne centrum obsługi.",
      "client" => "RUTPOŻ",
      "services" => [
        "Web",
        "System zgłoszeń",
        "AI Chatbot",
        "Kalkulatory"
      ],
      "meta" => [
        "AI chatbot",
        "kalkulatory",
        "B2B"
      ],
      "scope" => [
        "Strona firmowa WordPress",
        "Asystent AI 24/7",
        "Kalkulator wydajności hydrantów",
        "Kalkulator gęstości ogniowej",
        "Generowanie raportów PDF",
        "System zgłoszeń serwisowych"
      ],
      "results" => [
        [
          "Asystent AI 24/7",
          "Odpowiedzi na podstawowe pytania bez oczekiwania na kontakt z działem obsługi."
        ],
        [
          "Kalkulatory PPOŻ",
          "Praktyczne narzędzia branżowe dostępne bezpośrednio na stronie."
        ],
        [
          "Cyfrowe zgłoszenia",
          "Uporządkowana obsługa serwisu i generowanie dokumentacji PDF."
        ]
      ],
      "challenge" => "Przeniesienie technicznych procesów i powtarzalnych zapytań klientów do jednego, łatwego w obsłudze systemu.",
      "solution" => "Połączyliśmy stronę ofertową z kalkulatorami, formularzami serwisowymi, raportami i bazą wiedzy obsługiwaną przez AI.",
      "effect" => "Platforma ograniczająca ręczną pracę zespołu i dostarczająca klientom odpowiedzi oraz narzędzia przez całą dobę.",
      "featured" => false
    ],
    "druga szansa|web shop|sieć / franczyza" => [
      "sub" => "Strona sieci sklepów z sekcją franczyzy",
      "type" => "Sieć / Franczyza",
      "tag" => "Sieć sklepów / franczyza",
      "desc" => "Dla sieci sklepów Druga Szansa przygotowaliśmy stronę łączącą prezentację marki, lokalizacje punktów oraz rozwój franczyzy. Użytkownik może szybko znaleźć najbliższy sklep, poznać model współpracy i wysłać zgłoszenie franczyzowe. Projekt wspiera również kampanie leadowe i komunikację kodów promocyjnych.",
      "client" => "Druga Szansa",
      "services" => [
        "Web Design",
        "Lead Gen",
        "Mapa lokalizacji"
      ],
      "meta" => [
        "lead gen",
        "franczyza",
        "mapa punktów"
      ],
      "scope" => [
        "Strona sieci sklepów",
        "Sekcja franczyzy z formularzem",
        "Mapa punktów sprzedaży",
        "System kodów rabatowych",
        "Integracja WooCommerce",
        "Kampanie leadowe"
      ],
      "results" => [
        [
          "Mapa punktów",
          "Szybkie wyszukiwanie najbliższego sklepu i danych kontaktowych."
        ],
        [
          "Leady franczyzowe",
          "Dedykowana prezentacja modelu współpracy i formularz zgłoszeniowy."
        ],
        [
          "Wsparcie kampanii",
          "Landingowe sekcje i mechanizmy kodów rabatowych."
        ]
      ],
      "challenge" => "Połączenie potrzeb klientów detalicznych, obecnych placówek i potencjalnych franczyzobiorców w jednym serwisie.",
      "solution" => "Zaprojektowaliśmy mapę punktów, rozbudowaną sekcję franczyzy, formularze leadowe i moduły promocyjne.",
      "effect" => "Centralna platforma marki wspierająca ruch w sklepach oraz pozyskiwanie nowych partnerów biznesowych.",
      "featured" => false
    ],
    "inusti|web|strona b2b" => [
      "sub" => "Strona B2B nastawiona na szybkie zapytania",
      "type" => "Strona B2B",
      "tag" => "Firma budowlana / B2B",
      "desc" => "Dla firmy Inusti stworzyliśmy stronę B2B, która szybko komunikuje zakres usług budowlanych i prowadzi do zapytania ofertowego. Projekt wykorzystuje realizacje jako główny dowód kompetencji, a strukturę przygotowaliśmy pod kampanie Google Ads i rozwój SEO. Całość jest prosta, techniczna i wygodna na urządzeniach mobilnych.",
      "client" => "Inusti",
      "services" => [
        "Web Design",
        "WordPress Dev",
        "SEO"
      ],
      "meta" => [
        "B2B",
        "lead flow",
        "SEO-ready"
      ],
      "scope" => [
        "Strona firmowa B2B",
        "Formularz szybkiego kontaktu",
        "Sekcja realizacji z galeriami",
        "Optymalizacja pod Google Ads",
        "Struktura SEO on-site",
        "Design mobile-first"
      ],
      "results" => [
        [
          "Lead flow",
          "Kontakt dostępny bez szukania i zbędnych kroków."
        ],
        [
          "Portfolio B2B",
          "Realizacje pokazujące skalę prac i doświadczenie firmy."
        ],
        [
          "Gotowość reklamowa",
          "Struktura przygotowana pod kampanie Google Ads i SEO."
        ]
      ],
      "challenge" => "Przedstawienie usług wykonawczych w sposób konkretny oraz maksymalne skrócenie drogi do kontaktu.",
      "solution" => "Zbudowaliśmy klarowne landing pages, wyeksponowane realizacje i szybki formularz dostępny w kluczowych punktach strony.",
      "effect" => "Lekki serwis sprzedażowy, który wspiera kampanie, buduje wiarygodność i generuje zapytania B2B.",
      "featured" => false
    ],
    "piekarnia marysia|web|strona lokalna" => [
      "sub" => "Strona lokalna z ofertą i kierunkiem pod rozwój",
      "type" => "Strona lokalna",
      "tag" => "Piekarnia / gastronomia",
      "desc" => "Dla lokalnej Piekarni Marysia przygotowaliśmy ciepłą, minimalistyczną stronę prezentującą ofertę, produkty i charakter marki. Projekt ułatwia sprawdzenie menu, lokalizacji i godzin otwarcia, a galeria buduje apetyt jeszcze przed wizytą. Serwis został przygotowany pod lokalne wyniki Google i wygodne korzystanie z telefonu.",
      "client" => "Piekarnia Marysia",
      "services" => [
        "Web Design",
        "SEO lokalne",
        "Fotografia"
      ],
      "meta" => [
        "local SEO",
        "oferta",
        "szybki UX"
      ],
      "scope" => [
        "Strona wizytówka piekarni",
        "Prezentacja menu i produktów",
        "Galeria z fotografią produktową",
        "Integracja Google Maps",
        "Optymalizacja SEO lokalne",
        "Responsywny design"
      ],
      "results" => [
        [
          "Oferta pod ręką",
          "Menu i najważniejsze produkty dostępne w czytelnej formie."
        ],
        [
          "Lokalne SEO",
          "Dane, mapa i struktura wspierające wyszukiwania w okolicy."
        ],
        [
          "Szybki mobile",
          "Najważniejsze informacje widoczne od razu na telefonie."
        ]
      ],
      "challenge" => "Przeniesienie rodzinnego charakteru piekarni do internetu i szybkie dostarczenie najważniejszych informacji lokalnym klientom.",
      "solution" => "Połączyliśmy prostą ofertę, fotografie produktów, mapę i dane kontaktowe w lekkiej strukturze mobile-first.",
      "effect" => "Czytelna wizytówka marki, która wspiera ruch w lokalu i zwiększa widoczność w lokalnych wyszukiwaniach.",
      "featured" => false
    ],
    "siemianowski|branding|logo + branding premium" => [
      "sub" => "Logo i branding kancelarii premium",
      "type" => "Logo + branding premium",
      "tag" => "Kancelaria / Restrukturyzacja",
      "desc" => "Dla kancelarii Siemianowski opracowaliśmy elegancki system identyfikacji oparty na stonowanej typografii, szlachetnej kolorystyce i znaku o profesjonalnym charakterze. Logo zostało zaprojektowane tak, aby równie dobrze działało na dokumentach, tablicach, stronie internetowej i materiałach firmowych. Całość buduje wizerunek kancelarii premium bez korzystania z oczywistych symboli prawniczych.",
      "client" => "Siemianowski",
      "services" => [
        "Logo",
        "branding",
        "mockupy",
        "materiały firmowe"
      ],
      "meta" => [
        "Premium",
        "Kancelaria",
        "Elegancja"
      ],
      "scope" => [
        "Projekt logo z wariantami",
        "Kolorystyka i typografia",
        "Mockupy i wizualizacje",
        "Materiały do druku",
        "Identyfikacja kancelarii",
        "Wytyczne do strony WWW"
      ],
      "results" => [
        [
          "Autorski znak",
          "Logo wyróżniające kancelarię bez branżowych klisz."
        ],
        [
          "Pełny system",
          "Kolory, typografia i warianty gotowe do zastosowań online i offline."
        ],
        [
          "Wizerunek premium",
          "Spójne materiały podnoszące wiarygodność marki."
        ]
      ],
      "challenge" => "Stworzenie rozpoznawalnego znaku dla branży prawnej bez powielania motywów wagi, kolumn i paragrafów.",
      "solution" => "Postawiliśmy na autorski układ typograficzny, dopracowane proporcje oraz system zastosowań formalnych i cyfrowych.",
      "effect" => "Ponadczasowa identyfikacja wzmacniająca ekspercki i prestiżowy charakter kancelarii.",
      "featured" => false
    ],
    "polerstone|branding|logo + identyfikacja" => [
      "sub" => "Nowoczesne logo dla usług kamieniarskich",
      "type" => "Logo + identyfikacja",
      "tag" => "Kamieniarstwo premium",
      "desc" => "Dla marki POLERSTONE stworzyliśmy nowoczesne logo podkreślające precyzję obróbki kamienia, solidność i wysoką jakość realizacji. Geometryczny monogram PS został przygotowany tak, aby dobrze prezentował się zarówno na jasnych materiałach firmowych, jak i bezpośrednio na kamieniu, odzieży czy oznakowaniu. Charakter identyfikacji łączy techniczność z segmentem premium.",
      "client" => "POLERSTONE",
      "services" => [
        "Logo",
        "branding",
        "mockupy",
        "materiały firmowe"
      ],
      "meta" => [
        "Monogram",
        "Premium",
        "Kamień"
      ],
      "scope" => [
        "Logo marki POLERSTONE",
        "Sygnet / monogram PS",
        "Wersje kolorystyczne",
        "Mockupy prezentacyjne",
        "Wizualizacje na kamieniu",
        "Materiały firmowe"
      ],
      "results" => [
        [
          "Monogram PS",
          "Zwarty znak dobrze działający w małych i dużych formatach."
        ],
        [
          "Zastosowania techniczne",
          "Warianty przygotowane pod grawer, druk i oznakowanie."
        ],
        [
          "Charakter premium",
          "Minimalistyczny system wspierający pozycjonowanie jakościowe."
        ]
      ],
      "challenge" => "Zbudowanie nowoczesnego wizerunku dla tradycyjnej branży i zapewnienie czytelności znaku na wymagających powierzchniach.",
      "solution" => "Opracowaliśmy zwarty monogram, mocną typografię i ograniczoną paletę łatwą do wdrożenia w produkcji.",
      "effect" => "Rozpoznawalny system wizualny gotowy do oznakowania produktów, pojazdów i materiałów sprzedażowych.",
      "featured" => false
    ],
    "pracownia urody|branding|logo + identyfikacja" => [
      "sub" => "Delikatny branding beauty dla Joanny Samborskiej",
      "type" => "Logo + identyfikacja",
      "tag" => "Beauty / Kosmetologia",
      "desc" => "Dla Pracowni Urody Joanny Samborskiej zaprojektowaliśmy subtelną identyfikację podkreślającą kobiecość, spokój i profesjonalizm gabinetu. Delikatny sygnet z profilem twarzy współpracuje z elegancką typografią oraz jasną paletą kolorów. System został przygotowany do komunikacji w social mediach, materiałach drukowanych i opakowaniach.",
      "client" => "Pracownia Urody",
      "services" => [
        "Logo",
        "branding",
        "mockupy",
        "materiały firmowe"
      ],
      "meta" => [
        "Beauty",
        "Subtelnie",
        "Kobieco"
      ],
      "scope" => [
        "Logo marki",
        "Subtelny sygnet z profilem",
        "Wersje kolorystyczne",
        "Mockupy prezentacyjne",
        "Wizualizacje na opakowaniach",
        "Materiały firmowe i social media"
      ],
      "results" => [
        [
          "Subtelny sygnet",
          "Charakterystyczny profil twarzy bez nadmiernej dosłowności."
        ],
        [
          "Paleta beauty",
          "Kolory wspierające poczucie spokoju i jakości."
        ],
        [
          "Gotowa komunikacja",
          "Materiały do druku, opakowań i social mediów."
        ]
      ],
      "challenge" => "Połączenie delikatnej estetyki beauty z wiarygodnym, profesjonalnym charakterem usług.",
      "solution" => "Stworzyliśmy lekki znak, spokojną paletę i zestaw materiałów łatwych do konsekwentnego stosowania.",
      "effect" => "Spójny i rozpoznawalny wizerunek, który wzmacnia atmosferę gabinetu jeszcze przed wizytą.",
      "featured" => false
    ],
    "arcycięcie|branding|logo + identyfikacja" => [
      "sub" => "Charakterystyczny branding studia fryzur",
      "type" => "Logo + identyfikacja",
      "tag" => "Barber / Fryzjerstwo",
      "desc" => "Dla studia fryzur Arcycięcie przygotowaliśmy charakterystyczny branding łączący energię nowoczesnego salonu z detalami kojarzonymi z rzemiosłem fryzjerskim. Znak wykorzystuje motyw brzytwy i nożyczek, ale został uproszczony tak, aby zachować czytelność i współczesny charakter. Identyfikację rozwinięliśmy na oznakowanie salonu, materiały drukowane i social media.",
      "client" => "Arcycięcie",
      "services" => [
        "Logo",
        "branding",
        "mockupy",
        "materiały firmowe"
      ],
      "meta" => [
        "Logo premium",
        "Materiały salonu",
        "Branding"
      ],
      "scope" => [
        "Projekt logo z wariantami",
        "Kolorystyka i typografia",
        "Mockupy i wizualizacje",
        "Materiały do druku",
        "Elementy oznakowania salonu",
        "Szablony social media"
      ],
      "results" => [
        [
          "Wyrazisty znak",
          "Motyw fryzjerski zamieniony w nowoczesny symbol marki."
        ],
        [
          "Oznakowanie salonu",
          "Spójne zastosowania na szyldach, witrynie i materiałach wnętrza."
        ],
        [
          "Social ready",
          "System przygotowany do codziennej komunikacji w mediach społecznościowych."
        ]
      ],
      "challenge" => "Stworzenie wyrazistej marki fryzjerskiej bez efektu ciężkiego, typowego logo barberskiego.",
      "solution" => "Połączyliśmy dynamiczny znak z nowoczesną typografią i elastycznym systemem komunikacji.",
      "effect" => "Branding, który jest łatwo rozpoznawalny i dobrze pracuje zarówno w przestrzeni salonu, jak i online.",
      "featured" => false
    ],
    "vista architekci|branding|logo + identyfikacja" => [
      "sub" => "Minimalistyczny monogram dla biura projektowego",
      "type" => "Logo + identyfikacja",
      "tag" => "Architekci",
      "desc" => "Dla Vista Architekci zaprojektowaliśmy minimalistyczny monogram VA inspirowany geometrią, przekrojami budowlanymi i światłem. Znak jest precyzyjny, oszczędny i dopasowany do profesjonalnej prezentacji projektów architektonicznych. Identyfikację rozszerzyliśmy o brandbook, papeterię i szablony ofertowe.",
      "client" => "Vista Architekci",
      "services" => [
        "Logo",
        "branding",
        "mockupy",
        "materiały firmowe"
      ],
      "meta" => [
        "Minimalizm",
        "Architektura",
        "Brandbook"
      ],
      "scope" => [
        "Sygnet monogramowy VA",
        "Brandbook z wytycznymi",
        "Papeteria firmowa",
        "Szablony ofertowe",
        "Wizytówki i teczki",
        "Materiały do prezentacji"
      ],
      "results" => [
        [
          "Monogram VA",
          "Geometryczny znak inspirowany językiem architektury."
        ],
        [
          "Brandbook",
          "Czytelne reguły stosowania logo, kolorów i typografii."
        ],
        [
          "Materiały ofertowe",
          "Spójna papeteria i szablony prezentacji projektów."
        ]
      ],
      "challenge" => "Oddanie charakteru pracowni architektonicznej w prostym znaku bez dosłownych ikon budynku.",
      "solution" => "Oparliśmy monogram na proporcjach i negatywnej przestrzeni, tworząc system o technicznym, ale eleganckim charakterze.",
      "effect" => "Ponadczasowa identyfikacja wspierająca profesjonalne prezentacje, oferty i dokumentację projektową.",
      "featured" => false
    ],
    "akoya|branding|logo + identyfikacja" => [
      "sub" => "Gabinet kosmetyczny premium",
      "type" => "Logo + identyfikacja",
      "tag" => "Beauty & Wellness",
      "desc" => "Dla gabinetu kosmetycznego Akoya przygotowaliśmy spokojną identyfikację premium inspirowaną naturalnym pięknem i perłowym charakterem nazwy. Beżowo-złota paleta, miękka typografia i delikatne formy budują poczucie luksusu bez przesadnego przepychu. System obejmuje logo, materiały zabiegowe, komunikację social media i podstawowe wytyczne marki.",
      "client" => "Akoya",
      "services" => [
        "Logo",
        "branding",
        "mockupy",
        "materiały firmowe"
      ],
      "meta" => [
        "Beauty",
        "Mini brandbook",
        "Premium"
      ],
      "scope" => [
        "Logo z wariantami kolorowymi",
        "Mini brandbook",
        "Wizytówki i karty zabiegowe",
        "Szablony social media",
        "Paleta kolorów i fonty",
        "Mockupy i wizualizacje"
      ],
      "results" => [
        [
          "Spokojny luksus",
          "Estetyka premium bez chłodnego, klinicznego charakteru."
        ],
        [
          "Mini brandbook",
          "Zasady kolorów, fontów i wariantów logo."
        ],
        [
          "Materiały gabinetu",
          "Karty zabiegowe, wizytówki i szablony komunikacji."
        ]
      ],
      "challenge" => "Zbudowanie luksusowego wizerunku, który pozostaje ciepły, dostępny i kobiecy.",
      "solution" => "Zastosowaliśmy miękką typografię, subtelny detal i elegancką paletę dobrze działającą w gabinecie oraz online.",
      "effect" => "Spójna marka beauty wzmacniająca poczucie jakości i komfortu na każdym etapie kontaktu.",
      "featured" => false
    ],
    "eat tasty|branding|logo + identyfikacja" => [
      "sub" => "Apetyczny branding dowozu jedzenia",
      "type" => "Logo + identyfikacja",
      "tag" => "Gastro / Delivery",
      "desc" => "Dla Eat Tasty stworzyliśmy energetyczny branding marki delivery, którego znak łączy formę talerza z ruchem i kierunkiem dostawy. Kolorystyka została dobrana tak, aby dobrze działać na opakowaniach, aplikacjach i materiałach promocyjnych. System jest prosty, apetyczny i łatwy do zauważenia w dynamicznym otoczeniu gastronomii.",
      "client" => "Eat Tasty",
      "services" => [
        "Logo",
        "branding",
        "mockupy",
        "materiały firmowe"
      ],
      "meta" => [
        "Gastro",
        "Opakowania",
        "Social media"
      ],
      "scope" => [
        "Logo z symbolem delivery",
        "Projekty opakowań i boksów",
        "Naklejki i etykiety",
        "Materiały social media",
        "System kolorystyczny",
        "Mockupy na opakowaniach"
      ],
      "results" => [
        [
          "Symbol delivery",
          "Talerz i ruch dostawy zamknięte w jednym znaku."
        ],
        [
          "System opakowań",
          "Spójne boksy, naklejki i etykiety produktowe."
        ],
        [
          "Widoczność marki",
          "Kolory i układ zaprojektowane pod szybkie rozpoznanie."
        ]
      ],
      "challenge" => "Połączenie skojarzenia z jedzeniem i szybką dostawą w jednym prostym symbolu.",
      "solution" => "Opracowaliśmy dynamiczny znak, wyrazistą paletę i zestaw zastosowań na opakowaniach oraz komunikacji online.",
      "effect" => "Rozpoznawalna identyfikacja gotowa do skalowania wraz z ofertą i obszarem dostaw.",
      "featured" => false
    ],
    "fryz dobry barber|branding|logo + identyfikacja" => [
      "sub" => "Męski branding barbershopu",
      "type" => "Logo + identyfikacja",
      "tag" => "Barber / Men’s",
      "desc" => "Dla Fryz Dobry Barber przygotowaliśmy pełną identyfikację inspirowaną klasycznym rzemiosłem barberskim. Brodaty sygnet i typografia vintage budują męski, charakterystyczny klimat, a system materiałów pozwala konsekwentnie oznaczyć wnętrze, szyld i komunikację. Projekt zachowuje styl retro, ale pozostaje czytelny i funkcjonalny.",
      "client" => "Fryz Dobry Barber",
      "services" => [
        "Logo",
        "branding",
        "mockupy",
        "materiały firmowe"
      ],
      "meta" => [
        "Barbershop",
        "Vintage",
        "Materiały wnętrza"
      ],
      "scope" => [
        "Sygnet z brodatym motywem",
        "Typografia vintage",
        "Materiały do wnętrza",
        "Wizytówki i karty",
        "Szyld i oznakowanie",
        "System identyfikacji"
      ],
      "results" => [
        [
          "Brodaty sygnet",
          "Charakterystyczny symbol dopasowany do męskiego salonu."
        ],
        [
          "Styl vintage",
          "Typografia i detale budujące rzemieślniczy klimat."
        ],
        [
          "Wnętrze i druk",
          "Materiały gotowe do oznakowania salonu i obsługi klientów."
        ]
      ],
      "challenge" => "Uzyskanie klasycznego charakteru bez nadmiernej ilości detali i problemów z czytelnością znaku.",
      "solution" => "Uprościliśmy ilustracyjny motyw i połączyliśmy go z mocną typografią oraz ograniczoną paletą.",
      "effect" => "Spójny klimat barbershopu widoczny od szyldu po wizytówkę i media społecznościowe.",
      "featured" => false
    ],
    "piotr mazur|branding|logo + identyfikacja" => [
      "sub" => "Monogram PM dla kancelarii adwokackiej",
      "type" => "Logo + identyfikacja",
      "tag" => "Kancelaria",
      "desc" => "Dla kancelarii adwokackiej Piotra Mazura stworzyliśmy elegancki monogram PM osadzony w formie subtelnej tarczy. Znak nawiązuje do ochrony i pewności, ale unika oczywistych symboli prawniczych. Identyfikacja została przygotowana do dokumentów, tablic kancelarii, wizytówek i komunikacji cyfrowej.",
      "client" => "Piotr Mazur",
      "services" => [
        "Logo",
        "branding",
        "mockupy",
        "materiały firmowe"
      ],
      "meta" => [
        "Kancelaria",
        "Monogram",
        "Premium"
      ],
      "scope" => [
        "Monogram PM w formie tarczy",
        "Papeteria firmowa",
        "Tablice kancelarii",
        "Wizytówki premium",
        "Wytyczne do strony WWW",
        "Brandbook"
      ],
      "results" => [
        [
          "Monogram PM",
          "Autorsko połączone inicjały w stabilnej formie."
        ],
        [
          "Symbol ochrony",
          "Tarcza sugerująca bezpieczeństwo bez dosłowności."
        ],
        [
          "Formalne zastosowania",
          "Papeteria, tablice i materiały cyfrowe w jednym systemie."
        ]
      ],
      "challenge" => "Zaprojektowanie prestiżowego znaku prawnego, który wyróżnia się na tle standardowych herbów i wag.",
      "solution" => "Połączyliśmy inicjały w zwartą formę tarczy i zbudowaliśmy wokół niej oszczędny system typograficzny.",
      "effect" => "Profesjonalna identyfikacja budująca zaufanie i dobrze działająca w formalnych zastosowaniach.",
      "featured" => false
    ],
    "okruszek|branding|logo + identyfikacja" => [
      "sub" => "Pastelowe logo pracowni cukierniczej",
      "type" => "Logo + identyfikacja",
      "tag" => "Cukiernia / Słodkości",
      "desc" => "Dla pracowni cukierniczej Okruszek stworzyliśmy pastelową, pogodną identyfikację z subtelnym motywem tortu. Projekt miał zachować rękodzielniczy i rodzinny charakter marki, a jednocześnie dobrze prezentować się na pudełkach, etykietach i witrynie. Lekka paleta i przyjazna typografia budują apetyczny, łatwo rozpoznawalny styl.",
      "client" => "Okruszek",
      "services" => [
        "Logo",
        "branding",
        "mockupy",
        "materiały firmowe"
      ],
      "meta" => [
        "Cukiernia",
        "Pastel",
        "Opakowania"
      ],
      "scope" => [
        "Logo z symbolem tortu",
        "Projekty pudełek i opakowań",
        "Etykiety na produkty",
        "Oznakowanie witryny",
        "Paleta pastelowa",
        "Mockupy na pudełkach"
      ],
      "results" => [
        [
          "Symbol tortu",
          "Czytelny motyw pracowni cukierniczej."
        ],
        [
          "Pastelowy system",
          "Kolory budujące lekki, apetyczny charakter."
        ],
        [
          "Opakowania marki",
          "Pudełka, etykiety i oznakowanie witryny."
        ]
      ],
      "challenge" => "Stworzenie słodkiej estetyki bez przesadnej infantylności i z zachowaniem dobrej czytelności.",
      "solution" => "Zaprojektowaliśmy prosty symbol, pastelowy system kolorów i praktyczne materiały opakowaniowe.",
      "effect" => "Ciepła identyfikacja, która wyróżnia produkty i wzmacnia doświadczenie ich wręczania.",
      "featured" => false
    ],
    "mona cake|branding|logo + identyfikacja" => [
      "sub" => "Branding pracowni tortów artystycznych",
      "type" => "Logo + identyfikacja",
      "tag" => "Cukiernia / Torty",
      "desc" => "Dla Mona Cake zaprojektowaliśmy bajkowy, ale uporządkowany branding pracowni tortów artystycznych. Sygnet z babeczką, pastelowa paleta i miękka typografia podkreślają kreatywność wypieków oraz indywidualny charakter realizacji. System został rozwinięty na pudełka, etykiety i komunikację na Instagramie.",
      "client" => "Mona Cake",
      "services" => [
        "Logo",
        "branding",
        "mockupy",
        "materiały firmowe"
      ],
      "meta" => [
        "Torty",
        "Pastel",
        "Social media"
      ],
      "scope" => [
        "Logo z babeczką i sygnet",
        "Projekty pudełek na torty",
        "Wizytówki i etykiety",
        "Szablony Instagram",
        "System kolorystyczny",
        "Mockupy"
      ],
      "results" => [
        [
          "Sygnet babeczki",
          "Przyjazny znak związany bezpośrednio z ofertą."
        ],
        [
          "Opakowania tortów",
          "Pudełka i etykiety wzmacniające efekt produktu premium."
        ],
        [
          "Instagram ready",
          "Szablony ułatwiające regularną prezentację realizacji."
        ]
      ],
      "challenge" => "Oddanie artystycznego charakteru tortów bez utraty profesjonalnego wyglądu marki.",
      "solution" => "Połączyliśmy ilustracyjny sygnet z prostą typografią i spójną paletą do zastosowań produktowych.",
      "effect" => "Rozpoznawalna marka, która dobrze prezentuje się na wypiekach, opakowaniach i w social mediach.",
      "featured" => false
    ],
    "wonder handmade|branding|logo + identyfikacja" => [
      "sub" => "Eleganckie logo dla marki handmade",
      "type" => "Logo + identyfikacja",
      "tag" => "Handmade / Rękodzieło",
      "desc" => "Dla Wonder Handmade stworzyliśmy minimalistyczną identyfikację, która podkreśla ręczne wykonanie i biżuteryjny charakter produktów. Subtelny sygnet dobrze pracuje w małym formacie na etykietach, zawieszkach i opakowaniach. Całość uzupełnia brandbook oraz system materiałów wspierających sprzedaż online i offline.",
      "client" => "Wonder Handmade",
      "services" => [
        "Logo",
        "branding",
        "mockupy",
        "materiały firmowe"
      ],
      "meta" => [
        "Handmade",
        "Etykiety",
        "Brandbook"
      ],
      "scope" => [
        "Sygnet biżuteryjny",
        "Brandbook z wytycznymi",
        "Etykiety na produkty",
        "Projekty opakowań",
        "Wizytówki i papeteria",
        "Szablony social media"
      ],
      "results" => [
        [
          "Biżuteryjny sygnet",
          "Delikatny znak działający na małych etykietach."
        ],
        [
          "Brandbook",
          "Spójne zasady dla kolorów, typografii i logo."
        ],
        [
          "Materiały produktowe",
          "Opakowania, zawieszki i komunikacja social media."
        ]
      ],
      "challenge" => "Zaprojektowanie delikatnego znaku, który pozostaje czytelny na bardzo małych elementach produktowych.",
      "solution" => "Opracowaliśmy prosty sygnet, oszczędną typografię i skalowalne warianty do druku oraz znakowania.",
      "effect" => "Elegancka marka handmade, która zwiększa wartość postrzeganą produktu i porządkuje komunikację.",
      "featured" => false
    ],
    "sfera|branding|logo + identyfikacja" => [
      "sub" => "Naturalny branding studia well-being",
      "type" => "Logo + identyfikacja",
      "tag" => "Well-being / Holistycznie",
      "desc" => "Dla studia well-being Sfera stworzyliśmy naturalną identyfikację opartą na organicznym symbolu i spokojnej palecie beży oraz zieleni. Projekt komunikuje równowagę, uważność i holistyczne podejście bez korzystania z nadmiernie ezoterycznych motywów. System został przygotowany do materiałów gabinetowych i codziennej komunikacji.",
      "client" => "Sfera",
      "services" => [
        "Logo",
        "branding",
        "mockupy",
        "materiały firmowe"
      ],
      "meta" => [
        "Well-being",
        "Naturalne kolory",
        "Minimalizm"
      ],
      "scope" => [
        "Logo z organicznym symbolem",
        "Paleta beży i zieleni",
        "Wizytówki i karty klienta",
        "Szablony komunikacji",
        "Mockupy i wizualizacje",
        "Materiały gabinetowe"
      ],
      "results" => [
        [
          "Organiczny symbol",
          "Prosty znak odnoszący się do równowagi i całości."
        ],
        [
          "Naturalna paleta",
          "Beże i zielenie tworzące spokojne doświadczenie marki."
        ],
        [
          "Materiały gabinetowe",
          "Karty klienta, wizytówki i szablony komunikacji."
        ]
      ],
      "challenge" => "Oddanie idei równowagi i naturalności w nowoczesny, wiarygodny sposób.",
      "solution" => "Zastosowaliśmy organiczne formy, miękką typografię i stonowane kolory inspirowane naturą.",
      "effect" => "Spokojna, profesjonalna marka budująca poczucie bezpieczeństwa i harmonii.",
      "featured" => false
    ],
    "looksus|branding|logo + identyfikacja" => [
      "sub" => "Monogramowe logo studia beauty",
      "type" => "Logo + identyfikacja",
      "tag" => "Beauty studio",
      "desc" => "Dla studia beauty Looksus zaprojektowaliśmy elegancki monogram w ciepłej palecie nude. Delikatne linie i dopracowane proporcje nadają marce kobiecy, luksusowy charakter, a jednocześnie zachowują czytelność na wizytówkach, kartach klienta i oznakowaniu. System wizualny został przygotowany także do komunikacji social media.",
      "client" => "Looksus",
      "services" => [
        "Logo",
        "branding",
        "mockupy",
        "materiały firmowe"
      ],
      "meta" => [
        "Beauty",
        "Monogram",
        "Nude palette"
      ],
      "scope" => [
        "Monogram w delikatnych formach",
        "Paleta nude i typografia",
        "Wizytówki premium",
        "Karty klienta",
        "Elementy wystroju studia",
        "Szablony social media"
      ],
      "results" => [
        [
          "Autorski monogram",
          "Rozpoznawalny znak zbudowany z delikatnych form."
        ],
        [
          "Nude palette",
          "Ciepła kolorystyka podkreślająca segment premium."
        ],
        [
          "System studia",
          "Wizytówki, karty klienta, wnętrze i social media."
        ]
      ],
      "challenge" => "Stworzenie luksusowego monogramu, który nie będzie ciężki ani przesadnie ozdobny.",
      "solution" => "Oparliśmy znak na lekkich liniach, negatywnej przestrzeni i spokojnym zestawie kolorystycznym.",
      "effect" => "Spójny wizerunek beauty premium działający w przestrzeni studia i kanałach cyfrowych.",
      "featured" => false
    ],
    "show party|branding|logo + identyfikacja" => [
      "sub" => "Selfie mirror — wariant 1",
      "type" => "Logo + identyfikacja",
      "tag" => "Event / Selfie mirror",
      "desc" => "Dla Show Party stworzyliśmy nowoczesny branding usługi selfie mirror i oprawy eventowej. Znak wykorzystuje motyw światła i odbicia, dzięki czemu łączy elegancję z imprezową energią. Identyfikację przygotowaliśmy do urządzeń, materiałów promocyjnych, social mediów i wydruków eventowych.",
      "client" => "Show Party",
      "services" => [
        "Logo",
        "branding",
        "mockupy",
        "materiały firmowe"
      ],
      "meta" => [
        "Event",
        "Selfie mirror",
        "Mockupy"
      ],
      "scope" => [
        "Logo z efektem światła",
        "Branding pod eventy",
        "Materiały promocyjne",
        "Szablony social media",
        "Mockupy selfie mirror",
        "Materiały drukowane"
      ],
      "results" => [
        [
          "Efekt światła",
          "Znak inspirowany lustrem, błyskiem i fotografią."
        ],
        [
          "Event ready",
          "Materiały dopasowane do przestrzeni wydarzeń i urządzeń."
        ],
        [
          "Spójna promocja",
          "Szablony social media i druków sprzedażowych."
        ]
      ],
      "challenge" => "Połączenie premium eventu z dynamicznym, rozrywkowym charakterem usługi.",
      "solution" => "Zaprojektowaliśmy świetlny znak, kontrastową typografię i system dobrze działający na ciemnym tle.",
      "effect" => "Wyrazista marka eventowa przyciągająca uwagę na realizacjach, sprzęcie i materiałach reklamowych.",
      "featured" => false
    ],
    "impulse tanning|branding|logo + identyfikacja" => [
      "sub" => "Energetyczny branding studia opalania",
      "type" => "Logo + identyfikacja",
      "tag" => "Studio opalania",
      "desc" => "Dla Impulse Tanning opracowaliśmy energetyczny branding studia opalania oparty na efekcie glow i intensywnym gradiencie. Znak ma nowoczesny, dynamiczny charakter i pozostaje rozpoznawalny na witrynie, kartach klienta oraz materiałach promocyjnych. Identyfikacja podkreśla efekt, energię i pewność siebie.",
      "client" => "Impulse Tanning",
      "services" => [
        "Logo",
        "branding",
        "mockupy",
        "materiały firmowe"
      ],
      "meta" => [
        "Glow",
        "Gradient",
        "Studio"
      ],
      "scope" => [
        "Logo z efektem glow",
        "Gradient i typografia",
        "Karty klienta",
        "Oznakowanie witryny",
        "Materiały promocyjne",
        "Mockupy"
      ],
      "results" => [
        [
          "Glow system",
          "Kontrolowany efekt świetlny jako główny motyw marki."
        ],
        [
          "Widoczna witryna",
          "Znak i kolory przygotowane do oznakowania lokalu."
        ],
        [
          "Materiały sprzedażowe",
          "Karty klienta, promocje i wizualizacje usług."
        ]
      ],
      "challenge" => "Stworzenie mocnej marki wizualnej bez wrażenia przypadkowego, neonowego efektu.",
      "solution" => "Uporządkowaliśmy gradient, typografię i znak w system możliwy do konsekwentnego stosowania.",
      "effect" => "Energetyczna identyfikacja wyróżniająca studio i dobrze pracująca w reklamie oraz przestrzeni lokalu.",
      "featured" => false
    ],
    "medical friend|branding|logo + identyfikacja" => [
      "sub" => "Przyjazny brand medyczny",
      "type" => "Logo + identyfikacja",
      "tag" => "Medycyna",
      "desc" => "Dla Medical Friend stworzyliśmy przyjazną identyfikację medyczną łączącą symbol serca z subtelnym motywem uśmiechu. Projekt miał zmniejszać dystans i budować zaufanie, pozostając jednocześnie profesjonalnym i czytelnym. System obejmuje oznakowanie gabinetów, papeterię i materiały informacyjne dla pacjentów.",
      "client" => "Medical Friend",
      "services" => [
        "Logo",
        "branding",
        "mockupy",
        "materiały firmowe"
      ],
      "meta" => [
        "Medycyna",
        "Zaufanie",
        "System"
      ],
      "scope" => [
        "Logo z symbolem serca",
        "Paleta medyczna",
        "Oznakowanie gabinetów",
        "Papeteria firmowa",
        "Materiały informacyjne",
        "Szablony do strony"
      ],
      "results" => [
        [
          "Symbol zaufania",
          "Serce i uśmiech budujące pozytywne pierwsze wrażenie."
        ],
        [
          "Czytelny system",
          "Kolory i typografia odpowiednie do komunikacji medycznej."
        ],
        [
          "Pełne oznakowanie",
          "Gabinet, dokumenty i materiały informacyjne w jednym stylu."
        ]
      ],
      "challenge" => "Połączenie medycznej wiarygodności z ciepłym, empatycznym charakterem komunikacji.",
      "solution" => "Zastosowaliśmy prosty symbol, jasną paletę i czytelną typografię dopasowaną do informacji zdrowotnych.",
      "effect" => "Przyjazna marka medyczna, która ułatwia kontakt i wzmacnia poczucie bezpieczeństwa pacjenta.",
      "featured" => false
    ],
    "janda|branding|logo + identyfikacja" => [
      "sub" => "Subtelny luksus w stylistyce spa",
      "type" => "Logo + identyfikacja",
      "tag" => "Health & Beauty",
      "desc" => "Dla marki Janda zaprojektowaliśmy luksusową, ilustracyjną identyfikację inspirowaną spokojem spa i kobiecym pięknem. Delikatny profil otoczony kwiatami tworzy charakterystyczny znak, który dobrze prezentuje się na menu zabiegów, wizytówkach i materiałach cyfrowych. Paleta została utrzymana w subtelnym, eleganckim tonie.",
      "client" => "Janda",
      "services" => [
        "Logo",
        "branding",
        "mockupy",
        "materiały firmowe"
      ],
      "meta" => [
        "Spa",
        "Luxury",
        "Branding"
      ],
      "scope" => [
        "Logo z ilustracją kwiatową",
        "Paleta luxury",
        "Menu zabiegów",
        "Wizytówki premium",
        "Szablony social media",
        "Mockupy i wizualizacje"
      ],
      "results" => [
        [
          "Ilustracyjny znak",
          "Kobiecy profil i kwiaty tworzące unikalny motyw marki."
        ],
        [
          "Paleta luxury",
          "Stonowane kolory wzmacniające wrażenie jakości."
        ],
        [
          "Materiały zabiegowe",
          "Menu, wizytówki i komunikacja social media."
        ]
      ],
      "challenge" => "Stworzenie bogatego, kobiecego znaku bez utraty elegancji i czytelności.",
      "solution" => "Uprościliśmy ilustrację do kontrolowanego detalu i zestawiliśmy ją z lekką typografią premium.",
      "effect" => "Rozpoznawalny branding spa budujący atmosferę relaksu jeszcze przed wizytą.",
      "featured" => false
    ],
    "ralf furniture|branding|logo + identyfikacja" => [
      "sub" => "Branding marki meblarskiej",
      "type" => "Logo + identyfikacja",
      "tag" => "Meble na wymiar",
      "desc" => "Dla Ralf Furniture stworzyliśmy minimalistyczny branding marki mebli na wymiar. Geometryczny sygnet, paleta czerni i naturalnych tonów drewna podkreślają precyzję wykonania oraz jakość materiałów. System został przygotowany do katalogów, ofert B2B, oznakowania showroomu i materiałów firmowych.",
      "client" => "Ralf Furniture",
      "services" => [
        "Logo",
        "branding",
        "mockupy",
        "materiały firmowe"
      ],
      "meta" => [
        "Meble",
        "B2B",
        "Premium"
      ],
      "scope" => [
        "Sygnet minimalistyczny",
        "Paleta czerni i drewna",
        "Katalog produktowy",
        "Papeteria firmowa",
        "Oznakowanie showroomu",
        "Materiały B2B"
      ],
      "results" => [
        [
          "Minimalistyczny sygnet",
          "Znak dobrze działający na meblach, dokumentach i online."
        ],
        [
          "Naturalna paleta",
          "Czerń i drewno podkreślające jakość materiałów."
        ],
        [
          "Sprzedaż B2B",
          "Katalogi, oferty i materiały showroomu w jednym stylu."
        ]
      ],
      "challenge" => "Zbudowanie premium wizerunku, który pasuje zarówno do nowoczesnych wnętrz, jak i technicznej komunikacji produkcyjnej.",
      "solution" => "Połączyliśmy prosty sygnet z naturalną paletą i uporządkowanym systemem prezentacji produktów.",
      "effect" => "Profesjonalna identyfikacja wspierająca sprzedaż, prezentacje projektów i kontakt z partnerami B2B.",
      "featured" => false
    ],
    "eva aesthetics|branding|logo + identyfikacja" => [
      "sub" => "Luksusowy branding kliniki",
      "type" => "Logo + identyfikacja",
      "tag" => "Medycyna estetyczna",
      "desc" => "Dla EVA Aesthetics przygotowaliśmy luksusową identyfikację kliniki medycyny estetycznej opartą na minimalistycznym logotypie i palecie nude & gold. Projekt łączy kliniczną wiarygodność z ciepłym, kobiecym charakterem marki. System obejmuje oznakowanie kliniki, karty zabiegowe, wizytówki i szczegółowe wytyczne stosowania.",
      "client" => "EVA Aesthetics",
      "services" => [
        "Logo",
        "branding",
        "mockupy",
        "materiały firmowe"
      ],
      "meta" => [
        "Klinika",
        "Nude & gold",
        "Luxury"
      ],
      "scope" => [
        "Logotyp minimalistyczny",
        "Paleta nude & gold",
        "Oznakowanie kliniki",
        "Karty zabiegowe",
        "Wizytówki premium",
        "Brandbook z wytycznymi"
      ],
      "results" => [
        [
          "Minimalistyczne logo",
          "Elegancki znak odpowiedni dla komunikacji medycznej i beauty."
        ],
        [
          "Nude & gold",
          "Paleta tworząca ciepły, luksusowy charakter."
        ],
        [
          "System kliniki",
          "Oznakowanie, karty zabiegowe i materiały firmowe."
        ]
      ],
      "challenge" => "Zachowanie równowagi pomiędzy medycznym profesjonalizmem a estetyką premium beauty.",
      "solution" => "Oparliśmy identyfikację na czystej typografii, subtelnym detalu i eleganckiej, kontrolowanej palecie.",
      "effect" => "Spójny wizerunek kliniki, który wzmacnia zaufanie i podnosi wartość postrzeganą usług.",
      "featured" => false
    ]
  ];
  return $data;
}

function zp_suite_realizacje_v19_enrich_projects($projects){
  $content = zp_suite_realizacje_v19_content();
  $out = [];
  foreach ((array)$projects as $row) {
    if (!is_array($row)) { continue; }
    $key = zp_suite_realizacje_v19_key($row);
    $overlay = $content[$key] ?? null;
    if (!$overlay) {
      // Fallback by brand for legacy CMS rows where type wording changed.
      $brand = strtolower(trim((string)($row['brand'] ?? '')));
      foreach ($content as $candidate_key => $candidate) {
        if (strpos($candidate_key, $brand . '|') === 0) { $overlay = $candidate; break; }
      }
    }
    if (is_array($overlay)) {
      foreach ($overlay as $field => $value) { $row[$field] = $value; }
    }
    $out[] = $row;
  }
  return $out;
}
