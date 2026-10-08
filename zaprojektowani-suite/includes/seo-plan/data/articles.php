<?php
if (!defined('ABSPATH')) { exit; }
/**
 * New articles (content batch 2, tresci/paczka-2; 2.6.0: content batch 3, tresci/paczka-3). The article body is data/articles/<slug>.html;
 * {{IMG:name}} is a photo from assets/img/wpisy/, imported into the media library on publishing.
 * 'source' is the post whose Elementor layout and article styles are copied.
 * 'swap' (2.9.6): the post exists with another article's text; see zp_seo_articles_swap().
*/
return [
  '/strony-internetowe/jak-stworzyc-strone-internetowa/' => [
    'slug' => 'jak-stworzyc-strone-internetowa',
    'category' => 'strony-internetowe',
    'title' => 'Jak stworzyć stronę internetową dla firmy? Kreator, WordPress czy agencja',
    'source' => '/strony-internetowe/strona-internetowa-dla-firmy-co-powinna-zawierac/',
    'featured' => 'strona-salonu-urody-mockup-okladka',
  ],
  '/logo-branding/jak-zastrzec-logo/' => [
    'slug' => 'jak-zastrzec-logo',
    'category' => 'logo-branding',
    'title' => 'Zastrzeżenie logo – jak zastrzec logo w Polsce i w Unii Europejskiej?',
    'source' => '/logo-branding/projektowanie-logo-jak-powstaje-znak-ktory-dziala-w-internecie-druku-i-sprzedazy/',
    'featured' => 'znak-marki-medycznej-okladka-kadr',
  ],
  '/logo-branding/key-visual-co-to-jest/' => [
    'slug' => 'key-visual-co-to-jest',
    'category' => 'logo-branding',
    'title' => 'Key visual – co to jest i jak zaprojektować główny motyw kampanii?',
    'source' => '/logo-branding/projektowanie-logo-jak-powstaje-znak-ktory-dziala-w-internecie-druku-i-sprzedazy/',
    'featured' => 'key-visual-formaty-okladka',
  ],
  '/logo-branding/logo-logotyp-sygnet-roznice/' => [
    'slug' => 'logo-logotyp-sygnet-roznice',
    'category' => 'logo-branding',
    'title' => 'Logo a logotyp i sygnet – czym się różnią i co wybrać dla firmy?',
    'source' => '/logo-branding/projektowanie-logo-jak-powstaje-znak-ktory-dziala-w-internecie-druku-i-sprzedazy/',
    'featured' => 'znak-pracowni-architektury-okladka-kadr',
  ],
  '/logo-branding/logo-wektorowe-pliki-logo/' => [
    'slug' => 'logo-wektorowe-pliki-logo',
    'category' => 'logo-branding',
    'title' => 'Logo wektorowe – jakie pliki logo powinna mieć firma i do czego służą?',
    'source' => '/logo-branding/projektowanie-logo-jak-powstaje-znak-ktory-dziala-w-internecie-druku-i-sprzedazy/',
    'featured' => 'wersje-znaku-restauracji-okladka-kadr',
  ],
  // 2.6.0: content batch 3 (tresci/paczka-3), published together.
  '/logo-branding/archetypy-marki/' => [
    'slug' => 'archetypy-marki',
    'category' => 'logo-branding',
    'title' => 'Archetypy marki – 12 archetypów z przykładami i jak wybrać swój',
    'source' => '/logo-branding/projektowanie-logo-jak-powstaje-znak-ktory-dziala-w-internecie-druku-i-sprzedazy/',
    'featured' => 'charakter-marki-salonu-okladka-kadr',
  ],
  '/logo-branding/jak-wymyslic-nazwe-firmy/' => [
    'slug' => 'jak-wymyslic-nazwe-firmy',
    'category' => 'logo-branding',
    'title' => 'Jak wymyślić nazwę firmy? Metody, przykłady i co sprawdzić przed wyborem',
    'source' => '/logo-branding/projektowanie-logo-jak-powstaje-znak-ktory-dziala-w-internecie-druku-i-sprzedazy/',
    'featured' => 'wizytowki-kancelarii-okladka',
  ],
  '/logo-branding/strategia-marki/' => [
    'slug' => 'strategia-marki',
    'category' => 'logo-branding',
    'title' => 'Jak zbudować strategię marki? Elementy, przykład i plan krok po kroku',
    'source' => '/logo-branding/projektowanie-logo-jak-powstaje-znak-ktory-dziala-w-internecie-druku-i-sprzedazy/',
    'featured' => 'marka-premium-okladka-kadr',
  ],
  '/strony-internetowe/rodzaje-stron-internetowych/' => [
    'slug' => 'rodzaje-stron-internetowych',
    'category' => 'strony-internetowe',
    'title' => 'Typy stron internetowych – który rodzaj strony wybrać dla firmy?',
    'source' => '/strony-internetowe/strona-internetowa-dla-firmy-co-powinna-zawierac/',
    'featured' => 'podstrony-strony-firmowej-okladka',
  ],
  '/strony-internetowe/makieta-strony-internetowej/' => [
    'slug' => 'makieta-strony-internetowej',
    'category' => 'strony-internetowe',
    'title' => 'Makieta strony www – wireframe, mockup i prototyp krok po kroku',
    'source' => '/strony-internetowe/strona-internetowa-dla-firmy-co-powinna-zawierac/',
    'featured' => 'strona-pracowni-krawieckiej-okladka',
  ],
  // 2.8.0: tresci/paczka-4.
  '/sklepy-internetowe/bramka-platnicza/' => [
    'slug' => 'bramka-platnicza',
    'category' => 'sklepy-internetowe',
    'title' => 'Bramka płatnicza w sklepie internetowym – jak wybrać i podłączyć do WooCommerce',
    'source' => '/strony-internetowe/strona-internetowa-dla-firmy-co-powinna-zawierac/',
    'featured' => 'platnosci-w-sklepie-okladka-kadr',
  ],
  '/logo-branding/logo-salonu-kosmetycznego/' => [
    'slug' => 'logo-salonu-kosmetycznego',
    'category' => 'logo-branding',
    'title' => 'Logo salonu kosmetycznego – style, kolory i przykłady z naszych realizacji',
    'source' => '/logo-branding/projektowanie-logo-jak-powstaje-znak-ktory-dziala-w-internecie-druku-i-sprzedazy/',
    'featured' => 'logo-salonow-beauty-okladka-kadr',
  ],
  '/logo-branding/projekt-wizytowki/' => [
    'slug' => 'projekt-wizytowki',
    'category' => 'logo-branding',
    'title' => 'Projekt wizytówki firmowej – wymiary, treść i przygotowanie do druku',
    'source' => '/logo-branding/projektowanie-logo-jak-powstaje-znak-ktory-dziala-w-internecie-druku-i-sprzedazy/',
    'featured' => 'wizytowki-z-realizacji-okladka-kadr',
  ],
  '/sklepy-internetowe/sklep-b2b/' => [
    'slug' => 'sklep-b2b',
    'category' => 'sklepy-internetowe',
    'title' => 'Sklep B2B i hurtownia internetowa – funkcje, platformy i wdrożenie',
    'source' => '/strony-internetowe/strona-internetowa-dla-firmy-co-powinna-zawierac/',
    'featured' => 'sklep-b2b-okladka-kadr',
  ],
  '/strony-internetowe/system-rezerwacji-online/' => [
    'slug' => 'system-rezerwacji-online',
    'category' => 'strony-internetowe',
    'title' => 'System rezerwacji online na stronie internetowej – jak wybrać i wdrożyć',
    'source' => '/strony-internetowe/strona-internetowa-dla-firmy-co-powinna-zawierac/',
    'featured' => 'system-rezerwacji-okladka-kadr',
  ],
  // 2.9.6: posts that already exist but showed another article's text (thread 13's review, 8.10).
  // 'swap' is a fragment of that other text: the body is replaced once, only while the post still has it.
  '/kampanie-reklamowe/ile-kosztuje-google-ads/' => [
    'slug' => 'ile-kosztuje-google-ads',
    'category' => 'kampanie-reklamowe',
    'title' => 'Ile kosztuje Google Ads? Koszt kliknięcia, budżet testu i obsługa kampanii',
    'source' => '/strony-internetowe/strona-internetowa-dla-firmy-co-powinna-zawierac/',
    'featured' => 'ile-kosztuje-google-ads-okladka',
    'swap' => 'Search zwykle jest bezpieczniejszym wyborem',
  ],
  '/sklepy-internetowe/karta-produktu-w-sklepie-internetowym/' => [
    'slug' => 'karta-produktu-w-sklepie-internetowym',
    'category' => 'sklepy-internetowe',
    'title' => 'Karta produktu w sklepie internetowym – co musi zawierać, żeby klient kupił',
    'source' => '/strony-internetowe/strona-internetowa-dla-firmy-co-powinna-zawierac/',
    'featured' => 'karta-produktu-okladka',
    'swap' => 'Brief do logo i identyfikacji wizualnej to dokument',
  ],
];
