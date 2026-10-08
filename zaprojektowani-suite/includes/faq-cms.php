<?php
if (!defined('ABSPATH')) { exit; }

/**
 * ZAPROJEKTOWANI SUITE — FAQ CMS v2.2.51
 * Edycja kategorii oraz pytań dla shortcode [zp_faq_page].
 */

function zp_suite_faq_default_categories() {
  return [
  [
    'visible' => '1',
    'slug' => 'strony',
    'name' => 'Strony internetowe',
    'title' => 'Strony internetowe Katowice i projekty WWW',
    'desc' => 'Pytania o projektowanie stron internetowych, zakres wdrożenia, treści, responsywność i przygotowanie strony pod rozwój biznesu.',
    'icon' => 'monitor'
  ],
  [
    'visible' => '1',
    'slug' => 'sklepy',
    'name' => 'Sklepy internetowe Katowice i WooCommerce',
    'title' => 'Sklepy internetowe Katowice i WooCommerce',
    'desc' => 'Pytania o sklepy internetowe, WooCommerce, płatności, dostawy, produkty, koszyk, UX i przygotowanie sklepu pod sprzedaż.',
    'icon' => 'shopping-cart'
  ],
  [
    'visible' => '1',
    'slug' => 'branding',
    'name' => 'Logo, branding i identyfikacja wizualna',
    'title' => 'Logo, branding i identyfikacja wizualna',
    'desc' => 'Pytania o projektowanie logo, pełny branding, materiały graficzne, style marki i różnicę między samym znakiem a identyfikacją.',
    'icon' => 'pen-tool'
  ],
  [
    'visible' => '1',
    'slug' => 'seo',
    'name' => 'SEO, treści i widoczność w Google',
    'title' => 'SEO, treści i widoczność w Google',
    'desc' => 'Pytania o pozycjonowanie, strukturę podstron, artykuły, Rank Math, frazy lokalne i przygotowanie strony pod długofalową widoczność.',
    'icon' => 'search'
  ],
  [
    'visible' => '1',
    'slug' => 'reklamy',
    'name' => 'Kampanie reklamowe Meta Ads i Google Ads',
    'title' => 'Kampanie reklamowe Meta Ads i Google Ads',
    'desc' => 'Pytania o reklamy, przygotowanie strony pod kampanie, kreacje, formularze, analitykę i ścieżki kontaktu.',
    'icon' => 'megaphone'
  ],
  [
    'visible' => '1',
    'slug' => 'wspolpraca',
    'name' => 'Współpraca, wycena i płatności',
    'title' => 'Współpraca, wycena i płatności',
    'desc' => 'Pytania o kontakt, proces, Studio Wyceny, terminy, materiały od klienta, zaliczki i dalszą obsługę po wdrożeniu.',
    'icon' => 'handshake'
  ]
];
}

function zp_suite_faq_default_items() {
  return [
  [
    'visible' => '1',
    'category' => 'strony',
    'q' => 'Czym zajmuje się Zaprojektowani.com?',
    'a' => '<p>Zaprojektowani.com to studio kreatywne z Katowic, które projektuje strony internetowe, sklepy internetowe, logo, branding, SEO oraz kampanie reklamowe. Pomagamy firmom wyglądać profesjonalnie w internecie i budować ścieżkę od pierwszego kontaktu do sprzedaży.</p>',
    'open' => '1'
  ],
  [
    'visible' => '1',
    'category' => 'strony',
    'q' => 'Czy tworzycie strony internetowe w Katowicach?',
    'a' => '<p>Tak. Jednym z głównych kierunków SEO i oferty są strony internetowe Katowice oraz projektowanie stron internetowych Katowice. Obsługujemy firmy lokalne ze Śląska, ale pracujemy też z klientami z całej Polski.</p>',
    'open' => '0'
  ],
  [
    'visible' => '1',
    'category' => 'strony',
    'q' => 'Ile kosztuje strona internetowa dla firmy?',
    'a' => '<p>Cena zależy od zakresu: liczby sekcji i podstron, indywidualnego projektu, treści, formularzy, animacji, SEO, integracji oraz poziomu dopracowania. Najlepszym pierwszym krokiem jest <a href="/studio-wyceny/">Studio Wyceny</a>, gdzie można określić potrzeby i uzyskać konkretniejszy zakres.</p>',
    'open' => '0'
  ],
  [
    'visible' => '1',
    'category' => 'strony',
    'q' => 'Czy strona będzie dobrze działać na telefonie?',
    'a' => '<p>Tak. Projektujemy responsywne strony internetowe, czyli takie, które dobrze wyglądają i działają na desktopie, tablecie oraz telefonie. Mobile traktujemy jako kluczowy widok, bo bardzo często to właśnie tam użytkownik pierwszy raz styka się z marką.</p>',
    'open' => '0'
  ],
  [
    'visible' => '1',
    'category' => 'strony',
    'q' => 'Czy przygotowujecie teksty na stronę?',
    'a' => '<p>Tak. Możemy przygotować strukturę, nagłówki, opisy usług, sekcje sprzedażowe, CTA, FAQ i treści SEO. Dobre teksty pomagają nie tylko w pozycjonowaniu, ale też w lepszym zrozumieniu oferty przez klienta.</p>',
    'open' => '0'
  ],
  [
    'visible' => '1',
    'category' => 'strony',
    'q' => 'Czy możecie przebudować obecną stronę?',
    'a' => '<p>Tak. Możemy zaprojektować nową wersję obecnej strony, uporządkować strukturę, poprawić wygląd, wersję mobilną, szybkość, SEO i konwersję. Często zaczynamy od analizy tego, co już działa, a co blokuje pozyskiwanie klientów.</p>',
    'open' => '0'
  ],
  [
    'visible' => '1',
    'category' => 'strony',
    'q' => 'Jakie podstrony warto mieć na stronie firmowej?',
    'a' => '<p>Najczęściej warto zaplanować stronę główną, ofertę, realizacje, kontakt, FAQ oraz osobne podstrony usługowe. W praktyce często warto dodać landing pages pod konkretne usługi, np. <a href="/strony-internetowe-katowice/">strony internetowe Katowice</a>, <a href="/sklepy-internetowe-katowice/">sklepy internetowe Katowice</a> i podstrony branżowe.</p>',
    'open' => '0'
  ],
  [
    'visible' => '1',
    'category' => 'strony',
    'q' => 'Czy warto tworzyć osobne landing page pod konkretne usługi?',
    'a' => '<p>Tak. Osobne landing page pomagają lepiej dopasować treść do intencji klienta i fraz SEO. Przykładowo innej struktury potrzebuje strona pod „projektowanie stron internetowych Katowice”, a innej landing dla kancelarii, salonu beauty, lekarza, dewelopera albo producenta.</p>',
    'open' => '0'
  ],
  [
    'visible' => '1',
    'category' => 'strony',
    'q' => 'Czy tworzycie strony internetowe dla konkretnych branż?',
    'a' => '<p>Tak. Możemy przygotować strony internetowe dla kancelarii, salonów beauty, lekarzy, deweloperów, producentów, firm lokalnych, usług B2B i sklepów. Branżowe podstrony są ważnym elementem strategii SEO, bo odpowiadają na bardziej zakupowe zapytania niż ogólne frazy.</p>',
    'open' => '0'
  ],
  [
    'visible' => '1',
    'category' => 'strony',
    'q' => 'Czy strona internetowa może być jednocześnie ładna i szybka?',
    'a' => '<p>Tak, ale wymaga to rozsądnego projektu. Stawiamy na efekt premium, ale pilnujemy wydajności: lekkie sekcje, przemyślane animacje, dobre formaty zdjęć, ograniczanie zbędnych skryptów i układ, który nie przeciąża telefonu.</p>',
    'open' => '0'
  ],
  [
    'visible' => '1',
    'category' => 'strony',
    'q' => 'Czy pomagacie dobrać strukturę menu i nawigację?',
    'a' => '<p>Tak. Menu powinno prowadzić użytkownika do najważniejszych decyzji: sprawdzenia usług, realizacji, wyceny i kontaktu. Preferujemy układ mniej sztampowy niż klasyczne „O nas / Usługi / Kontakt”, ale nadal czytelny i wspierający konwersję.</p>',
    'open' => '0'
  ],
  [
    'visible' => '1',
    'category' => 'strony',
    'q' => 'Czy projektujecie stronę pod późniejsze pozycjonowanie?',
    'a' => '<p>Tak. Już na etapie projektu warto zaplanować H1, H2, strukturę adresów URL, sekcje FAQ, linkowanie wewnętrzne, podstrony lokalne i treści pod intencje użytkownika. Dzięki temu strona nie jest tylko wizytówką, ale bazą pod dalszy rozwój SEO.</p>',
    'open' => '0'
  ],
  [
    'visible' => '1',
    'category' => 'sklepy',
    'q' => 'Czy tworzycie sklepy internetowe Katowice?',
    'a' => '<p>Tak. Tworzymy sklepy internetowe Katowice dla firm, które chcą sprzedawać online skuteczniej. Wdrażamy sklepy WooCommerce, konfigurujemy strukturę, produkty, koszyk, płatności, dostawy i elementy wspierające sprzedaż.</p>',
    'open' => '0'
  ],
  [
    'visible' => '1',
    'category' => 'sklepy',
    'q' => 'Dlaczego WooCommerce?',
    'a' => '<p>WooCommerce jest elastyczny, dobrze łączy się z WordPressem i pozwala budować zarówno proste sklepy, jak i bardziej rozbudowane systemy sprzedaży. Daje dużą kontrolę nad wyglądem, SEO, produktami, płatnościami i rozbudową w przyszłości.</p>',
    'open' => '0'
  ],
  [
    'visible' => '1',
    'category' => 'sklepy',
    'q' => 'Czy sklep będzie przygotowany pod reklamy?',
    'a' => '<p>Możemy przygotować sklep pod kampanie reklamowe: czytelne karty produktów, logiczny koszyk, szybkie CTA, podstawową analitykę, ścieżki konwersji i treści, które pomagają klientowi podjąć decyzję zakupową.</p>',
    'open' => '0'
  ],
  [
    'visible' => '1',
    'category' => 'sklepy',
    'q' => 'Czy wdrażacie płatności online?',
    'a' => '<p>Tak. W zależności od projektu można wdrożyć płatności online, przelew tradycyjny, Stripe, PayPal, Przelewy24 lub inne rozwiązania potrzebne w sklepie. Zakres dobieramy do kraju sprzedaży, modelu biznesowego i potrzeb klienta.</p>',
    'open' => '0'
  ],
  [
    'visible' => '1',
    'category' => 'sklepy',
    'q' => 'Czy pomagacie z regulaminem i podstawami sklepu?',
    'a' => '<p>Możemy przygotować miejsce na regulamin, politykę prywatności, zwroty, dostawę, checkboxy i podstawowe elementy informacyjne. Treści prawne powinny być jednak finalnie sprawdzone przez prawnika lub dostarczone przez klienta.</p>',
    'open' => '0'
  ],
  [
    'visible' => '1',
    'category' => 'sklepy',
    'q' => 'Czym różni się sklep internetowy od strony z katalogiem produktów?',
    'a' => '<p>Sklep internetowy pozwala kupić produkt online, dodać go do koszyka i opłacić zamówienie. Katalog produktów może tylko prezentować ofertę i prowadzić do zapytania. Dobór rozwiązania zależy od modelu sprzedaży, liczby produktów i sposobu obsługi klienta.</p>',
    'open' => '0'
  ],
  [
    'visible' => '1',
    'category' => 'sklepy',
    'q' => 'Czy tworzycie sklepy WooCommerce dla producentów i firm B2B?',
    'a' => '<p>Tak. WooCommerce można dopasować zarówno do sprzedaży detalicznej, jak i B2B: progi ilościowe, zapytania ofertowe, formularze, płatność po wycenie, ukryte ceny, konta klientów i niestandardowe procesy zamówień.</p>',
    'open' => '0'
  ],
  [
    'visible' => '1',
    'category' => 'sklepy',
    'q' => 'Co powinien zawierać sklep przed startem reklamy?',
    'a' => '<p>Przed startem reklamy sklep powinien mieć czytelne karty produktów, jasne ceny, dostawy, płatności, regulaminy, szybki checkout, dobre zdjęcia, sekcje z zaufaniem, remarketing/analitykę i testową ścieżkę zakupu. Reklama nie naprawi chaotycznego sklepu.</p>',
    'open' => '0'
  ],
  [
    'visible' => '1',
    'category' => 'sklepy',
    'q' => 'Czy sklep internetowy trzeba od razu robić bardzo rozbudowany?',
    'a' => '<p>Nie zawsze. Często lepiej zacząć od stabilnego sklepu z dobrą strukturą, a później rozwijać go o kolejne funkcje: promocje, cross-sell, warianty, automatyzacje, blog, SEO, integracje i kampanie reklamowe.</p>',
    'open' => '0'
  ],
  [
    'visible' => '1',
    'category' => 'sklepy',
    'q' => 'Czy pomagacie uporządkować kategorie produktów?',
    'a' => '<p>Tak. Kategorie produktów są ważne dla UX i SEO. Pomagamy ułożyć strukturę sklepu tak, żeby klient szybko rozumiał ofertę, a Google lepiej widział tematyczne obszary sprzedaży.</p>',
    'open' => '0'
  ],
  [
    'visible' => '1',
    'category' => 'branding',
    'q' => 'Czym różni się logo od brandingu?',
    'a' => '<p>Logo to znak marki. Branding to szerszy system: kolorystyka, typografia, styl grafik, kompozycja, sposób komunikacji, materiały firmowe i zasady użycia marki. Dobre logo jest ważne, ale dopiero branding sprawia, że firma wygląda spójnie w każdym miejscu.</p>',
    'open' => '0'
  ],
  [
    'visible' => '1',
    'category' => 'branding',
    'q' => 'Czy projektujecie logo dla nowych firm?',
    'a' => '<p>Tak. Projektujemy logo dla nowych marek, firm usługowych, sklepów, kancelarii, salonów, producentów i projektów lokalnych. Możemy przygotować sam znak albo pełniejszy pakiet z identyfikacją wizualną.</p>',
    'open' => '0'
  ],
  [
    'visible' => '1',
    'category' => 'branding',
    'q' => 'Co może obejmować projekt identyfikacji wizualnej?',
    'a' => '<p>Zakres może obejmować logo, warianty znaku, paletę kolorów, typografię, key visual, styl social media, wizytówki, materiały drukowane, mini brandbook i podstawowe zasady użycia. Zakres dobieramy do realnych potrzeb marki.</p>',
    'open' => '0'
  ],
  [
    'visible' => '1',
    'category' => 'branding',
    'q' => 'Czy możecie odświeżyć istniejące logo?',
    'a' => '<p>Tak. Możemy wykonać redesign lub lifting logo, jeśli marka ma już rozpoznawalność, ale znak wygląda przestarzale albo nie działa dobrze na stronie, w social mediach, druku lub reklamach.</p>',
    'open' => '0'
  ],
  [
    'visible' => '1',
    'category' => 'branding',
    'q' => 'Czy projektowanie logo Katowice to osobna usługa?',
    'a' => '<p>Tak. Projektowanie logo może być osobną usługą albo częścią większego pakietu brandingowego. W SEO planujemy rozwijać również obszar „projektowanie logo Katowice”, „agencja brandingowa Katowice” i „identyfikacja wizualna Katowice”.</p>',
    'open' => '0'
  ],
  [
    'visible' => '1',
    'category' => 'branding',
    'q' => 'Czy dobre logo wystarczy do profesjonalnego wizerunku?',
    'a' => '<p>Dobre logo to fundament, ale pełny efekt daje dopiero spójny system: kolory, fonty, grafiki, zdjęcia, styl social media, strona internetowa i materiały ofertowe. Dlatego przy wielu projektach rekomendujemy minimum podstawową identyfikację wizualną.</p>',
    'open' => '0'
  ],
  [
    'visible' => '1',
    'category' => 'branding',
    'q' => 'Czy przygotowujecie brandbook lub mini brandbook?',
    'a' => '<p>Tak. Możemy przygotować mini brandbook lub szerszą księgę identyfikacji, zależnie od potrzeb. Dokument porządkuje warianty logo, kolorystykę, typografię, marginesy ochronne, przykłady użycia i zasady komunikacji wizualnej.</p>',
    'open' => '0'
  ],
  [
    'visible' => '1',
    'category' => 'branding',
    'q' => 'Czy logo powinno być projektowane od razu pod stronę internetową?',
    'a' => '<p>Najlepiej tak. Logo powinno działać w headerze strony, faviconie, social mediach, stopce mailowej, reklamach, druku i na ciemnym oraz jasnym tle. Dzięki temu marka od początku jest praktyczna, a nie tylko efektowna na mockupie.</p>',
    'open' => '0'
  ],
  [
    'visible' => '1',
    'category' => 'branding',
    'q' => 'Czy można połączyć logo, stronę i branding w jednym projekcie?',
    'a' => '<p>Tak, to często najlepszy wariant. Kiedy logo, identyfikacja i strona powstają razem, łatwiej zachować spójność, przygotować mocniejsze CTA, lepsze sekcje sprzedażowe i bardziej premium odbiór marki.</p>',
    'open' => '0'
  ],
  [
    'visible' => '1',
    'category' => 'seo',
    'q' => 'Czy projekt strony od razu uwzględnia SEO?',
    'a' => '<p>Tak, możemy projektować stronę od początku z myślą o SEO: struktura nagłówków, podstrony usługowe, logiczne adresy URL, treści, linkowanie wewnętrzne, FAQ, metadane i podstawowa optymalizacja techniczna. To nie zastępuje wielomiesięcznego pozycjonowania, ale daje lepszy start.</p>',
    'open' => '0'
  ],
  [
    'visible' => '1',
    'category' => 'seo',
    'q' => 'Jakie frazy są ważne dla Zaprojektowani.com?',
    'a' => '<p>W majowym kierunku SEO dla nowej strony najważniejsze klastry to między innymi: strony internetowe Katowice, sklepy internetowe Katowice, projektowanie stron internetowych Katowice, agencja kreatywna Katowice, logo i branding, realizacje oraz wycena strony internetowej.</p>',
    'open' => '0'
  ],
  [
    'visible' => '1',
    'category' => 'seo',
    'q' => 'Czy piszecie artykuły blogowe?',
    'a' => '<p>Tak. Przygotowujemy artykuły poradnikowe i SEO, które wspierają usługi strony, sklepu, logo, brandingu, reklam i pozycjonowania. Artykuły mogą linkować do podstron usługowych, realizacji, kontaktu i Studio Wyceny.</p>',
    'open' => '0'
  ],
  [
    'visible' => '1',
    'category' => 'seo',
    'q' => 'Czy pomagacie z Rank Math?',
    'a' => '<p>Tak. Możemy przygotować focus keyword, dodatkowe frazy, meta title, meta description, slug, H1, strukturę H2/H3 i sekcje tekstowe, które pomagają stronie osiągać lepszą ocenę w Rank Math bez sztucznego upychania fraz.</p>',
    'open' => '0'
  ],
  [
    'visible' => '1',
    'category' => 'seo',
    'q' => 'Dlaczego nie warto walczyć tylko o bardzo ogólne frazy?',
    'a' => '<p>Ogólne frazy typu „strony internetowe” są bardzo konkurencyjne i często mniej precyzyjne. W majowym planie SEO lepszym kierunkiem są klastry lokalne, usługowe i branżowe, np. „strony internetowe Katowice”, „tworzenie sklepów internetowych”, „strona internetowa dla kancelarii”.</p>',
    'open' => '0'
  ],
  [
    'visible' => '1',
    'category' => 'seo',
    'q' => 'Jakie podstrony SEO są priorytetowe dla Zaprojektowani.com?',
    'a' => '<p>Priorytetowe kierunki to: <a href="/strony-internetowe-katowice/">strony internetowe Katowice</a>, <a href="/sklepy-internetowe-katowice/">sklepy internetowe Katowice</a>, logo i branding Katowice, kampanie Meta Ads oraz branżowe landingi pod firmy, które realnie szukają wykonawcy.</p>',
    'open' => '0'
  ],
  [
    'visible' => '1',
    'category' => 'seo',
    'q' => 'Czy FAQ pomaga w SEO?',
    'a' => '<p>Tak, jeśli odpowiada na realne pytania klientów. FAQ wzmacnia semantykę strony, pozwala naturalnie użyć fraz long-tail, poprawia użyteczność i może wspierać schema FAQPage. Najważniejsze, żeby odpowiedzi były konkretne, a nie napisane tylko pod roboty.</p>',
    'open' => '0'
  ],
  [
    'visible' => '1',
    'category' => 'seo',
    'q' => 'Czy linkowanie wewnętrzne ma znaczenie?',
    'a' => '<p>Tak. Linki wewnętrzne pomagają użytkownikowi przejść do właściwej usługi, a Google lepiej zrozumieć strukturę strony. Dlatego FAQ, artykuły i realizacje powinny prowadzić do usług, Studio Wyceny i kontaktu.</p>',
    'open' => '0'
  ],
  [
    'visible' => '1',
    'category' => 'seo',
    'q' => 'Czy artykuły blogowe powinny prowadzić do usług?',
    'a' => '<p>Tak. Artykuł nie powinien być ślepą uliczką. W treści warto dodawać naturalne linki do usług, np. strony internetowe, sklepy, logo, branding, kampanie Meta Ads, realizacje i Studio Wyceny.</p>',
    'open' => '0'
  ],
  [
    'visible' => '1',
    'category' => 'seo',
    'q' => 'Czy lokalne SEO dla Katowic wyklucza klientów z całej Polski?',
    'a' => '<p>Nie. Lokalny kierunek pomaga zdobywać ruch z Katowic i Śląska, ale komunikacja strony może nadal pokazywać, że pracujemy z klientami z całej Polski. Ważne jest, żeby nie upychać lokalizacji sztucznie, tylko używać jej tam, gdzie ma sens.</p>',
    'open' => '0'
  ],
  [
    'visible' => '1',
    'category' => 'reklamy',
    'q' => 'Czy prowadzicie kampanie reklamowe?',
    'a' => '<p>Tak. Możemy przygotować i prowadzić kampanie reklamowe Meta Ads oraz Google Ads, a także zadbać o kreacje, komunikaty, landing page, formularze i podstawy analityki. Reklama działa najlepiej wtedy, gdy strona jest gotowa na ruch i konwersję.</p>',
    'open' => '0'
  ],
  [
    'visible' => '1',
    'category' => 'reklamy',
    'q' => 'Czy sama reklama wystarczy do sprzedaży?',
    'a' => '<p>Nie zawsze. Reklama może przyprowadzić użytkownika, ale sprzedaż zależy też od strony, oferty, formularza, szybkości ładowania, zaufania i jasnego CTA. Dlatego często rekomendujemy najpierw uporządkowanie strony lub landing page.</p>',
    'open' => '0'
  ],
  [
    'visible' => '1',
    'category' => 'reklamy',
    'q' => 'Czy przygotowujecie grafiki do reklam?',
    'a' => '<p>Tak. Jako studio projektowe możemy przygotować kreacje reklamowe dopasowane do strony, brandingu i celu kampanii. Dzięki temu reklamy są spójne z marką, a nie przypadkowe wizualnie.</p>',
    'open' => '0'
  ],
  [
    'visible' => '1',
    'category' => 'reklamy',
    'q' => 'Czy możecie przygotować stronę pod kampanię?',
    'a' => '<p>Tak. Możemy przygotować landing page, skrócić formularz, poprawić CTA, dopracować sekcje z ofertą, dodać FAQ, social proof, realizacje i elementy, które pomagają użytkownikowi podjąć decyzję po kliknięciu reklamy.</p>',
    'open' => '0'
  ],
  [
    'visible' => '1',
    'category' => 'reklamy',
    'q' => 'Czy prowadzicie kampanie Meta Ads dla stron i sklepów?',
    'a' => '<p>Tak. Możemy prowadzić kampanie Meta Ads dla usług, stron internetowych, sklepów, brandingu i ofert lokalnych. Dbamy nie tylko o ustawienia kampanii, ale też o kreacje, komunikaty i stronę docelową.</p>',
    'open' => '0'
  ],
  [
    'visible' => '1',
    'category' => 'reklamy',
    'q' => 'Kiedy warto uruchomić Google Ads, a kiedy Meta Ads?',
    'a' => '<p>Google Ads dobrze działa, gdy użytkownik aktywnie szuka konkretnej usługi lub produktu. Meta Ads częściej buduje popyt, dociera do grup odbiorców i wspiera remarketing. W praktyce dobór kanału zależy od oferty, budżetu i gotowości strony do konwersji.</p>',
    'open' => '0'
  ],
  [
    'visible' => '1',
    'category' => 'reklamy',
    'q' => 'Jak przygotować landing page pod reklamę?',
    'a' => '<p>Landing page powinien mieć jasny nagłówek, konkretną ofertę, krótką ścieżkę kontaktu, mocne CTA, realizacje, argumenty zaufania, FAQ i szybki formularz. Dobrze, gdy tekst reklamy i treść strony mówią jednym językiem.</p>',
    'open' => '0'
  ],
  [
    'visible' => '1',
    'category' => 'reklamy',
    'q' => 'Czy pomagacie z pikselem, konwersjami i analityką?',
    'a' => '<p>Możemy pomóc przygotować podstawy analityki i śledzenia konwersji, tak aby kampanie miały dane do optymalizacji. Zakres zależy od używanych narzędzi, zgód cookies, formularzy, sklepu i celu kampanii.</p>',
    'open' => '0'
  ],
  [
    'visible' => '1',
    'category' => 'reklamy',
    'q' => 'Czy reklamy mogą promować Studio Wyceny?',
    'a' => '<p>Tak. Studio Wyceny może być dobrym miejscem docelowym dla kampanii, jeśli użytkownik jest już zainteresowany usługą i chce szybko określić zakres. W reklamach można kierować ruch także do podstron usługowych albo konkretnych landing page.</p>',
    'open' => '0'
  ],
  [
    'visible' => '1',
    'category' => 'wspolpraca',
    'q' => 'Jak rozpocząć współpracę z Zaprojektowani?',
    'a' => '<p>Najprościej przejść do <a href="/studio-wyceny/">Studio Wyceny</a> albo wysłać wiadomość przez <a href="/kontakt/">formularz kontaktowy</a>. Wystarczy opisać, czy potrzebujesz strony, sklepu, logo, brandingu, SEO, reklam albo kompleksowego pakietu.</p>',
    'open' => '0'
  ],
  [
    'visible' => '1',
    'category' => 'wspolpraca',
    'q' => 'Czym jest Studio Wyceny?',
    'a' => '<p>Studio Wyceny to miejsce, w którym można określić zakres projektu i szybciej przygotować rozmowę o kosztach. Pomaga uporządkować potrzeby: typ strony, sklep, logo, branding, reklamy, treści, SEO i integracje.</p>',
    'open' => '0'
  ],
  [
    'visible' => '1',
    'category' => 'wspolpraca',
    'q' => 'Jakie materiały trzeba przygotować przed startem?',
    'a' => '<p>Najlepiej przygotować logo, obecne materiały marki, opis oferty, zdjęcia, przykładowe strony, które się podobają, dostęp do hostingu lub WordPressa oraz informacje o celach biznesowych. Jeśli czegoś brakuje, możemy pomóc to uporządkować.</p>',
    'open' => '0'
  ],
  [
    'visible' => '1',
    'category' => 'wspolpraca',
    'q' => 'Czy po wdrożeniu można dalej rozwijać stronę?',
    'a' => '<p>Tak. Po publikacji można rozwijać stronę o kolejne podstrony, artykuły, sekcje, realizacje, formularze, integracje, SEO i kampanie reklamowe. Strona internetowa powinna rosnąć razem z firmą, a nie kończyć się na samej publikacji.</p>',
    'open' => '0'
  ],
  [
    'visible' => '1',
    'category' => 'wspolpraca',
    'q' => 'Czy pracujecie tylko z firmami z Katowic?',
    'a' => '<p>Nie. Katowice są ważne lokalnie i SEO, ale współpracujemy z klientami z całej Polski. Wiele etapów projektu można przeprowadzić zdalnie: brief, konsultacje, projekt, wdrożenie, poprawki i dalszą obsługę.</p>',
    'open' => '0'
  ],
  [
    'visible' => '1',
    'category' => 'wspolpraca',
    'q' => 'Czy można zamówić kompleksowy pakiet: logo, stronę i reklamy?',
    'a' => '<p>Tak. Możemy przygotować kompleksowy zakres: logo, branding, stronę internetową, sklep, SEO, treści, realizacje, formularze oraz kampanie reklamowe. Przy większych projektach dzielimy pracę na logiczne etapy.</p>',
    'open' => '0'
  ],
  [
    'visible' => '1',
    'category' => 'wspolpraca',
    'q' => 'Czy można zacząć od mniejszego zakresu i rozbudować projekt później?',
    'a' => '<p>Tak. Można zacząć od najważniejszej strony, landing page, logo albo podstawowego sklepu, a później rozwijać kolejne podstrony, artykuły, SEO, reklamy i automatyzacje. Ważne, żeby już na starcie nie zablokować przyszłej rozbudowy.</p>',
    'open' => '0'
  ],
  [
    'visible' => '1',
    'category' => 'wspolpraca',
    'q' => 'Jak szybko można otrzymać wycenę?',
    'a' => '<p>Im dokładniejszy opis w Studio Wyceny, tym szybciej można przygotować sensowny zakres. Najbardziej pomaga informacja o typie projektu, liczbie podstron, funkcjach, materiałach, terminie, budżecie i przykładach stylu.</p>',
    'open' => '0'
  ],
  [
    'visible' => '1',
    'category' => 'wspolpraca',
    'q' => 'Czy mogę zadzwonić zamiast wypełniać formularz?',
    'a' => '<p>Tak. Możesz skorzystać z formularza, Studio Wyceny albo kontaktu telefonicznego. Przy bardziej rozbudowanym projekcie i tak warto później zebrać zakres na piśmie, żeby uniknąć niejasności.</p>',
    'open' => '0'
  ],
  [
    'visible' => '1',
    'category' => 'wspolpraca',
    'q' => 'Czy pomagacie po publikacji strony?',
    'a' => '<p>Tak. Po wdrożeniu można kontynuować współpracę przy treściach, SEO, reklamach, rozwoju strony, nowych sekcjach, aktualizacjach i optymalizacji konwersji. Najlepsze strony rozwijają się razem z firmą.</p>',
    'open' => '0'
  ]
];
}

function zp_suite_faq_defaults() {
  return [
    'categories' => zp_suite_faq_default_categories(),
    'items' => zp_suite_faq_default_items(),
  ];
}

function zp_suite_faq_get() {
  $data = get_option('zp_suite_faq_cms', null);
  if (!is_array($data)) {
    $data = zp_suite_faq_defaults();
    update_option('zp_suite_faq_cms', $data, false);
  }

  if (empty($data['categories']) || !is_array($data['categories'])) {
    $data['categories'] = zp_suite_faq_default_categories();
  }
  if (empty($data['items']) || !is_array($data['items'])) {
    $data['items'] = zp_suite_faq_default_items();
  }

  return $data;
}

/**
 * =========================================================
 * FAQ FRONT SEO — v2.2.719
 * =========================================================
 * - one canonical FAQ URL,
 * - dedicated Rank Math title/description,
 * - one FAQPage node in Rank Math graph,
 * - fallback JSON-LD only when Rank Math is unavailable.
 */
if (!function_exists('zp_suite_faq_is_front')) {
  function zp_suite_faq_is_front() {
    if (is_admin()) return false;

    if (function_exists('is_page') && (is_page('najczesciej-zadawane-pytania') || is_page('faq'))) {
      return true;
    }

    if (function_exists('is_singular') && is_singular()) {
      $post_id = get_queried_object_id();
      $content = $post_id ? (string) get_post_field('post_content', $post_id) : '';

      if ($content && (has_shortcode($content, 'zp_faq_page') || has_shortcode($content, 'zp_page_faq'))) {
        return true;
      }
    }

    return false;
  }
}

if (!function_exists('zp_suite_faq_front_url')) {
  function zp_suite_faq_front_url() {
    $post_id = get_queried_object_id();
    $url = $post_id ? get_permalink($post_id) : '';
    return $url ?: home_url('/najczesciej-zadawane-pytania/');
  }
}

if (!function_exists('zp_suite_faq_schema_node')) {
  function zp_suite_faq_schema_node() {
    $data = zp_suite_faq_get();
    $items_all = is_array($data['items'] ?? null) ? $data['items'] : [];
    $main = [];

    foreach ($items_all as $item) {
      if (!is_array($item) || (isset($item['visible']) && (string) $item['visible'] === '0')) continue;

      $q = trim(wp_strip_all_tags((string) ($item['q'] ?? '')));
      $a = trim(preg_replace('/\s+/u', ' ', wp_strip_all_tags((string) ($item['a'] ?? ''))));

      if ($q === '' || $a === '') continue;

      $main[] = [
        '@type' => 'Question',
        'name' => $q,
        'acceptedAnswer' => [
          '@type' => 'Answer',
          'text' => $a,
        ],
      ];
    }

    if (!$main) return [];

    $url = zp_suite_faq_front_url();

    return [
      '@type' => 'FAQPage',
      '@id' => trailingslashit($url) . '#faq',
      'url' => $url,
      'name' => 'Najczęściej zadawane pytania | Zaprojektowani.com',
      'inLanguage' => 'pl-PL',
      'mainEntity' => $main,
    ];
  }
}

add_filter('rank_math/frontend/title', function($title){
  if (!zp_suite_faq_is_front()) return $title;
  // 2.7.1: a title saved in Rank Math (the SEO plan writes one) wins over this default.
  if (function_exists('zp_seo_faq_rank_math_owns') && zp_seo_faq_rank_math_owns('rank_math_title')) return $title;
  return 'FAQ — strony WWW, sklepy, branding i reklamy | Zaprojektowani';
}, 80);

add_filter('rank_math/frontend/description', function($description){
  if (!zp_suite_faq_is_front()) return $description;
  if (function_exists('zp_seo_faq_rank_math_owns') && zp_seo_faq_rank_math_owns('rank_math_description')) return $description;
  return 'Odpowiedzi na najczęstsze pytania o strony internetowe, WooCommerce, logo i branding, SEO, kampanie reklamowe, wycenę i współpracę z Zaprojektowani.';
}, 80);

add_filter('rank_math/frontend/canonical', function($canonical){
  if (!zp_suite_faq_is_front()) return $canonical;
  return zp_suite_faq_front_url();
}, 80);

add_filter('rank_math/json_ld', function($data, $jsonld = null){
  if (!zp_suite_faq_is_front() || !is_array($data)) return $data;

  foreach ($data as $key => $node) {
    $type = is_array($node) ? ($node['@type'] ?? '') : '';
    $types = is_array($type) ? $type : [$type];

    if (in_array('FAQPage', $types, true)) {
      unset($data[$key]);
    }
  }

  $faq = zp_suite_faq_schema_node();
  if ($faq) $data['zp-faq-page'] = $faq;

  return $data;
}, 99, 2);

add_filter('document_title_parts', function($parts){
  if (!zp_suite_faq_is_front()) return $parts;

  if (!defined('RANK_MATH_VERSION')) {
    $parts['title'] = 'FAQ — strony WWW, sklepy, branding i reklamy';
    $parts['site'] = 'Zaprojektowani.com';
  }

  return $parts;
}, 80);

add_action('wp_head', function(){
  if (!zp_suite_faq_is_front() || defined('RANK_MATH_VERSION')) return;

  $description = 'Odpowiedzi na najczęstsze pytania o strony internetowe, WooCommerce, logo i branding, SEO, kampanie reklamowe, wycenę i współpracę z Zaprojektowani.';
  $schema = zp_suite_faq_schema_node();

  echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";

  if ($schema) {
    echo '<script type="application/ld+json">' . wp_json_encode([
      '@context' => 'https://schema.org',
    ] + $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
  }
}, 35);
