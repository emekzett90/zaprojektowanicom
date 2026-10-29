
(function(){
  const root = document.querySelector('[data-zp-showcase-white]');
  if (!root || root.dataset.zpReady === '1') return;
  const zpInitialMobile = (window.innerWidth || document.documentElement.clientWidth || 0) <= 760;
  if (zpInitialMobile) {
    root.dataset.zpReady = 'mobile-static';
    root.classList.add('is-visible','is-mobile-static');
    return;
  }
  root.dataset.zpReady = '1';

  const zpPortfolioCreateIcons = (scope) => {
    const target = scope || root || document;

    /* On the homepage Lucide is intentionally guarded against expensive global
       DOM scans. Dynamic modal markup therefore has to use the scoped icon
       hydrator owned by the global header controller. */
    if (typeof window.zpSuiteHydrateIcons === 'function') {
      try {
        const hydrated = window.zpSuiteHydrateIcons(target);
        if (hydrated !== false) return true;
      } catch(e) {}
    }

    if (window.lucide && typeof window.lucide.createIcons === 'function') {
      try {
        window.__zpAllowLucideScan = true;
        window.lucide.createIcons({
          attrs: {
            'stroke-width': 2,
            'stroke-linecap': 'round',
            'stroke-linejoin': 'round',
            'aria-hidden': 'true'
          }
        });
        return true;
      } catch(e) {
        return false;
      } finally {
        window.__zpAllowLucideScan = false;
      }
    }

    return false;
  };
  zpPortfolioCreateIcons(root);
  window.addEventListener('load', () => zpPortfolioCreateIcons(root), { once: true, passive: true });

  const spacer = root.querySelector('[data-zp-showcase-spacer]');
  const stage = root.querySelector('[data-zp-showcase-stage]');
  const rail = root.querySelector('[data-zp-showcase-rail]');
  const titleEl = root.querySelector('[data-zp-showcase-title]');
  const leadEl = root.querySelector('[data-zp-showcase-lead]');
  const kickerEl = root.querySelector('[data-zp-showcase-kicker]');
  const tipEl = root.querySelector('[data-zp-showcase-tip]');
  const bottomTipEl = root.querySelector('[data-zp-showcase-bottom-tip]');
  const modeBtns = root.querySelectorAll('[data-zp-mode-btn]');
  const currentEls = root.querySelectorAll('[data-zp-showcase-current]');
  const totalEls = root.querySelectorAll('[data-zp-showcase-total]');
  const barEls = root.querySelectorAll('[data-zp-showcase-bar]');
  const prevBtn = root.querySelector('[data-zp-showcase-prev]');
  const nextBtn = root.querySelector('[data-zp-showcase-next]');
  const scrollHint = root.querySelector('[data-zp-scroll-hint]');

  const pop = document.getElementById('zpShowcasePop');
  const popBody = pop ? pop.querySelector('[data-zp-pop-body]') : null;
  const popCloseEls = pop ? pop.querySelectorAll('[data-zp-pop-close]') : [];

  const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const clamp = (n, min, max) => Math.min(Math.max(n, min), max);

  let maxTranslate = 0;
  let scrollDistance = 0;
  let startDelay = 0;
  let ticking = false;
  let savedScroll = 0;
  let measured = false;
  let lastWidth = window.innerWidth || document.documentElement.clientWidth || 0;
  let lastStableVH = 0;
  let resizeTimer = null;
  let activeMode = 'web';
  let currentX = 0;
  let targetX = 0;
  let xAnimRaf = 0;
  let vhLock = 0;
  let isInsideTrack = false;
  let lastScrollY = window.scrollY || window.pageYOffset || 0;
  let railPointerDown = false;
  let railPointerStartX = 0;
  let railPointerStartY = 0;
  let railDragged = false;
  let suppressClickUntil = 0;
  let mobileTouchStartX = 0;
  let mobileTouchStartY = 0;
  let mobileTouchMoved = false;
  let mobileSwitchLock = false;
  let mobileActiveIndex = 0;
  let mobileSnapTimer = null;
  let mobileProgrammaticScroll = false;
  let cardMetrics = [];
  let lastActiveIndex = -1;
  let lastProgressPct = -1;
  let lastRenderedX = null;
  let portfolioInView = true;
  let lastStateX = null;
  let hintDone = false;
  let sideAxis = '';
  let sideAxisUntil = 0;
  let sideTargetY = null;
  let sideLastY = 0;
  let sideLastT = 0;
  let sideRaf = 0;
  let sideWheelOn = false;

  if (!rail || !spacer || !stage) return;

  const arrowIcon = () => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 17 17 7"/><path d="M7 7h10v10"/></svg>';
  const isMobileView = () => (window.innerWidth || document.documentElement.clientWidth || 0) <= 1500;

  const copy = {
    web: {
      kicker: 'Portfolio / strony i sklepy',
      title: '<strong>Strony, sklepy i systemy,</strong> <span>które realnie pracują</span> na wizerunek i sprzedaż.',
      lead: 'Zobacz wybrane projekty, które łączą projekt graficzny, WordPress, WooCommerce, SEO, analitykę, rezerwacje, automatyzacje i kampanie w jeden dopracowany system.',
      tip: 'Przewiń w dół — realizacje stron przejdą poziomo. Możesz też przełączyć widok na logo i branding.',
      bottom: 'Scrolluj dalej lub przełącz na logo'
    },
    logo: {
      kicker: 'Portfolio / logo i branding',
      title: '<strong>Logo, systemy wizualne i brandingi,</strong> <span>które nadają marce</span> profesjonalny charakter.',
      lead: 'Zobacz wybrane projekty logo, sygnety, monogramy, systemy identyfikacji, mockupy i materiały firmowe przygotowane dla różnych branż.',
      tip: 'Przewiń w dół — realizacje logo przejdą poziomo. Karty są kwadratowe, a szczegóły pokażą zakres brandingu.',
      bottom: 'Scrolluj dalej lub wróć do stron'
    }
  };

  const webItems = [{"brand":"Altiora Linea","year":"2026","mode":"web","isNew":true,"img":"https://zaprojektowani.com/wp-content/uploads/2026/10/altiora_linea_realizacja-scaled.webp","live":"https://altioralinea.com/","meta":["WordPress","PL / EN / IT","Katalogi marek"],"tools":["WordPress","UX/UI","RWD","PL / EN / IT"],"case_url":"https://zaprojektowani.com/strony-internetowe/strona-internetowa-altiora-linea/","caseStudyUrl":"https://zaprojektowani.com/strony-internetowe/strona-internetowa-altiora-linea/","sub":"strona premium · 3 języki · katalogi producentów","type":"Strona firmowa / design i wnętrza","tag":"Marki wnętrzarskie premium / architekci i inwestorzy","client":"Altiora Linea — projekt dla Pana Dariusza","services":"Projekt UX/UI, wdrożenie WordPress, wersje PL/EN/IT, prezentacja marek i kolekcji, katalogi, formularze kontaktowe","desc":"Design potrzebuje przestrzeni — również w internecie. Dla Pana Dariusza przygotowaliśmy stronę Altiora Linea, marki łączącej architektów, projektantów wnętrz i inwestorów z wyselekcjonowanymi producentami wyposażenia premium. Zamiast przypadkowej listy produktów powstała spokojna, uporządkowana prezentacja marek, kolekcji i możliwości współpracy. Duże fotografie, ciepłe odcienie i wyrazista typografia pozwalają skupić uwagę na detalach oraz charakterze wnętrz.","scope":["Indywidualny projekt wizualny nawiązujący do świata architektury i wnętrz premium","Wdrożenie w WordPressie oraz dopasowanie do telefonu, tabletu i komputera","Trzy wersje językowe: polska, angielska i włoska","Prezentacja reprezentowanych marek, kolekcji i katalogów producentów","Formularze kontaktowe prowadzące do rozmowy o konkretnym projekcie","Osobne informacje dla architektów, projektantów i inwestorów","Czytelne przedstawienie procesu: selekcja, specyfikacja, koordynacja i dostawa"],"challenge":"Pokazać szeroki świat marek wnętrzarskich bez wrażenia zatłoczonego katalogu i jednocześnie wyjaśnić rolę Altiora Linea jako partnera w projekcie. Serwis miał łączyć elegancki wizerunek z praktycznym dostępem do informacji dla odbiorców z różnych rynków.","solution":"Ułożyliśmy nawigację wokół marek, kolekcji i współpracy z profesjonalistami. W detalach zaplanowaliśmy dostęp do katalogów oraz wyraźne przejścia do kontaktu. Sekcja procesu wyjaśnia, co dzieje się od doboru produktów po koordynację dostawy. Całość wdrożyliśmy w WordPressie w językach polskim, angielskim i włoskim.","effect":"Gotowa strona jest wizytówką marki i punktem startowym do rozmowy o wyposażeniu wnętrza. Odbiorca może poznać ofertę, przejrzeć katalogi, zrozumieć model współpracy i przejść do zapytania — w wybranym języku, również na telefonie.","results":[["Trzy języki","Jedna marka przedstawiona po polsku, angielsku i włosku."],["Marki i katalogi","Uporządkowane miejsce do poznawania kolekcji oraz materiałów producentów."],["Od inspiracji do rozmowy","Prezentacja oferty połączona z formularzami kontaktowymi."]],"cat":"web","featured":true,"project_slug":"altiora-linea"},{"brand":"OutTech","sub":"nowa strona firmowa + oferta B2B/B2C + formularze serwisowe","type":"Strona firmowa / technika zabezpieczeń","year":"2026","img":"https://zaprojektowani.com/wp-content/uploads/2026/09/outtech_realizacja_compressed-scaled.webp","tag":"Systemy bezpieczeństwa / B2B + smart home","desc":"Dla OutTech Technika Zabezpieczeń zaprojektowaliśmy nową stronę, która porządkuje rozbudowaną ofertę systemów bezpieczeństwa i pokazuje firmę jako partnera inżynieryjnego, a nie tylko instalatora. Serwis rozdziela rozwiązania dla biznesu i klientów indywidualnych, prowadzi przez alarmy, CCTV, kontrolę dostępu, automatykę i smart home oraz obsługuje zapytania ofertowe i zgłoszenia serwisowe.","meta":["WordPress","systemy bezpieczeństwa","formularz serwisowy","SEO"],"client":"OutTech Technika Zabezpieczeń","services":"UX/UI, WordPress, architektura informacji, formularz ofertowy, formularz serwisowy, SEO, RWD","scope":["Nowa architektura informacji i uporządkowanie rozbudowanej oferty","Strona główna, O firmie, Realizacje, Blog / aktualności, FAQ i Kontakt","Sekcja Dla biznesu z osobnymi obszarami bezpieczeństwa elektronicznego, nadzoru, ruchu osobowego, automatyzacji i ciągłości działania","Sekcja Inteligentna i bezpieczna przestrzeń dla klientów indywidualnych","Zakres usług: projektowanie, dobór rozwiązań, know-how, integracje, serwis i rozbudowa","Sekcja Jak pracujemy prowadząca od rozpoznania potrzeb do opieki nad instalacją","Rozbudowany formularz ofertowy","Formularz serwisowy z kategorią awarii, typem zgłoszenia, priorytetem i załącznikami","Responsywny projekt desktop, tablet i mobile","Struktura treści przygotowana pod widoczność lokalną w Google na Śląsku i Opolszczyźnie"],"tools":["WordPress","UX/UI","SEO","Custom Forms","RWD"],"results":[["Czytelna architektura","Oferta B2B, B2C i zakres usług są rozdzielone w logiczne ścieżki."],["Obsługa serwisu","Dedykowany formularz porządkuje zgłoszenia, priorytety i załączniki."],["Ekspercki wizerunek","Serwis podkreśla doświadczenie, know-how i kompleksowe podejście do bezpieczeństwa."]],"challenge":"Dotychczasowa strona była zbyt ogólna i słabo porządkowała szeroką ofertę OutTech. Wyzwaniem było czytelne rozdzielenie usług dla biznesu i domu, pokazanie zakresu obsługi od projektu po serwis oraz skrócenie drogi do zgłoszenia usterki lub zapytania ofertowego.","solution":"Zaprojektowaliśmy nową architekturę informacji z rozbudowanym menu, osobnymi ścieżkami dla biznesu i klientów indywidualnych oraz sekcją zakresu usług. Całość uzupełniliśmy ekspercką, ale zrozumiałą komunikacją, formularzem ofertowym i formularzem serwisowym z obsługą priorytetów oraz załączników.","effect":"Powstał nowoczesny serwis inżynieryjny, który lepiej komunikuje kompetencje OutTech, porządkuje ofertę systemów bezpieczeństwa i ułatwia klientom zarówno wybór rozwiązania, jak i kontakt z działem serwisu. Struktura stanowi też bazę pod dalszy rozwój treści i lokalnego SEO.","live":"https://www.outtech.pl/","mode":"web"},{"brand":"Polerstone","sub":"branding + nowa strona firmowa premium","type":"Branding + strona firmowa","year":"2026","img":"https://zaprojektowani.com/wp-content/uploads/2026/08/polerstone_realizacja-scaled.webp","tag":"Kamieniarstwo premium / wnętrza i architektura","desc":"Dla Polerstone przygotowaliśmy kompletny kierunek marki i nową stronę internetową, która pokazuje kamień jako element nowoczesnej architektury, a nie tylko materiał wykończeniowy. Projekt połączył branding, identyfikację wizualną, materiały firmowe i rozbudowany serwis prezentujący ofertę, materiały, realizacje, showroom oraz cały proces współpracy — od wyboru płyty po profesjonalny montaż.","meta":["branding","WordPress","premium UI","kamień naturalny"],"client":"Polerstone — Usługi Kamieniarskie","services":"Branding, logo, identyfikacja wizualna, UX/UI, WordPress, RWD, struktura SEO","scope":["Logo i spójny system identyfikacji wizualnej","Papier firmowy, wizytówki, teczki i stopki mailowe","Indywidualny projekt UX/UI nowej strony","Prezentacja blatów kuchennych, łazienek, schodów, parapetów, kominków, tarasów i elewacji","Rozbudowana sekcja materiałów: granit, marmur, kwarcyt, onyks, konglomeraty i spieki","Galeria realizacji i prezentacja showroomu","Sekcja procesu od konsultacji i pomiaru po obróbkę oraz montaż","Formularze kontaktowe i ścieżka umówienia wyceny","Responsywny widok desktop, tablet i mobile","Przygotowanie struktury pod dalszy rozwój SEO"],"tools":["Figma","WordPress","Elementor","Adobe Illustrator","Adobe Photoshop","SEO"],"results":[["Spójna marka","Logo, materiały firmowe i strona tworzą jeden konsekwentny system wizualny."],["Oferta premium","Kamień, realizacje i proces są prezentowane w architektoniczny, czytelny sposób."],["Gotowe do rozwoju","Serwis ma fundament pod kolejne realizacje, treści SEO i działania reklamowe."]],"challenge":"Połączyć szeroki zakres usług kamieniarskich i bogactwo naturalnych materiałów w jeden premium wizerunek, który będzie czytelny zarówno dla klienta indywidualnego, jak i architekta czy inwestora.","solution":"Zbudowaliśmy spokojny, architektoniczny system oparty na dużych fotografiach kamienia, czarno-grafitowej typografii, mocnym detalu i przejrzystej strukturze. Branding został przeniesiony na stronę, materiały firmowe i wszystkie najważniejsze punkty kontaktu z marką.","effect":"Polerstone otrzymało spójny wizerunek premium i nowoczesny serwis, który porządkuje ofertę, podkreśla jakość wykonania oraz prowadzi użytkownika od inspiracji i wyboru materiału do kontaktu i wyceny.","live":"","mode":"web","project_slug":"polerstone-branding-strona-firmowa"},{"brand":"Świat Grilli","sub":"branding + sklep WooCommerce od podstaw","type":"Sklep internetowy + branding","year":"2026","img":"https://zaprojektowani.com/wp-content/uploads/2026/08/swiatgrili_realizacja.webp","tag":"Grille / e-commerce multibrand","desc":"Dla ŚwiatGrilli.pl stworzyliśmy od podstaw kompletną markę i sklep internetowy dla branży grillowej. Od logo i brandingu, przez projekt UX/UI i strukturę kategorii, po WooCommerce, karty produktów, koszyk, checkout oraz płatności iMoje / ING — całość została zaprojektowana jako spójny system sprzedaży dla wielu marek i szerokiego katalogu produktów.","meta":["WooCommerce","iMoje / ING","multi-brand","RWD"],"client":"Świat Grilli","services":"Logo, branding, UX/UI, WooCommerce, płatności iMoje / ING, checkout, RWD, SEO","scope":["Logo i branding marki Świat Grilli","Indywidualny projekt sklepu internetowego","Struktura kategorii dla grilli, wędzarni, akcesoriów, kominków i outletu","Prezentacja marek El Fuego, Landmann i Barbecook","Karty produktów i czytelna prezentacja parametrów","Konfiguracja WooCommerce, koszyka i pełnego checkoutu","Integracja bramki płatniczej iMoje / ING","Sekcje budujące zaufanie oraz prezentacja sklepu stacjonarnego","Regulaminy, polityka prywatności i mechanizm cookies","Responsywny widok desktop, tablet i mobile","Przygotowanie struktury sklepu pod SEO i kampanie reklamowe"],"tools":["WordPress","WooCommerce","Elementor","iMoje / ING","UX/UI","SEO"],"results":[["E-commerce od zera","Branding, UX/UI i WooCommerce zostały połączone w jednym wdrożeniu."],["Pełna sprzedaż online","Koszyk, checkout i płatności iMoje / ING tworzą kompletną ścieżkę zakupową."],["Sklep multi-brand","Oferta wielu producentów jest uporządkowana w czytelnej strukturze kategorii i produktów."]],"challenge":"Stworzyć od podstaw nową markę e-commerce i uporządkować szeroką ofertę grilli, wędzarni, akcesoriów oraz różnych producentów tak, aby zakup był prosty zarówno dla początkującego, jak i bardziej świadomego klienta.","solution":"Zaprojektowaliśmy nowoczesny, produktowy sklep z wyraźnym podziałem kategorii, osobną ekspozycją kluczowych marek, rozbudowanymi kartami produktów i prostą drogą do finalizacji zamówienia. Całość uzupełniliśmy brandingiem, WooCommerce oraz płatnościami online.","effect":"Powstał kompletny sklep internetowy gotowy do skalowania oferty, prowadzenia kampanii i rozwijania sprzedaży wielu marek w jednym spójnym środowisku.","live":"https://www.swiatgrili.pl/","mode":"web"},{"brand":"ProScarves","sub":"E-commerce + zamówienia hurtowe","type":"B2B custom / sklep sportowy","year":"2026","img":"https://zaprojektowani.com/wp-content/uploads/2026/05/proscarves_realizacja_nowa_compressed.webp","tag":"B2B custom / sklep sportowy","desc":"Dla ProScarves zaprojektowaliśmy rozbudowany sklep sportowy łączący sprzedaż detaliczną z obsługą zamówień klubowych i hurtowych. Klient indywidualny może szybko kupić gotowy produkt, a klub lub organizacja przechodzi przez dedykowaną ścieżkę zapytania i wyceny produkcji customowej. Całość otrzymała dynamiczny, międzynarodowy charakter dopasowany do rynku kibicowskiego.","meta":["WooCommerce","B2B custom","fan clubs","system wyceny"],"client":"ProScarves","services":"UX/UI, WooCommerce, sklep detaliczny, system wyceny B2B, formularze custom orders, wdrożenie WordPress","scope":["Indywidualny projekt graficzny sklepu","Sklep detaliczny z gotowymi produktami dla fanów","Customowy system wyceny dla klubów i fan clubów","Ścieżka zapytań B2B dla klubów, firm i organizacji","Prezentacja produkcji customowej","Struktura pod rynki międzynarodowe","Responsywna wersja mobile","Podstawy SEO, analityka i przygotowanie pod kampanie"],"tools":[],"results":[["2 ścieżki sprzedaży","Detaliczny sklep i dedykowane zapytania klubowe w jednym serwisie."],["Custom B2B","System zbierający komplet danych potrzebnych do przygotowania wyceny."],["Gotowość na rozwój","Struktura przygotowana pod kolejne rynki, kategorie i warianty produktów."]],"challenge":"Połączenie dwóch różnych modeli sprzedaży w jednym serwisie bez komplikowania ścieżki zakupowej.","solution":"Rozdzieliliśmy e-commerce detaliczny i zapytania B2B, projektując osobne komunikaty, formularze oraz prezentację produkcji customowej.","effect":"Spójna platforma, która obsługuje klientów indywidualnych, kluby i partnerów biznesowych w jednym ekosystemie.","live":"https://www.proscarves.com/","mode":"web"},{"brand":"Siemianowski","sub":"Strona firmowa + branding + wizyty","type":"Kancelaria premium / prawo i restrukturyzacja","year":"2026","img":"https://zaprojektowani.com/wp-content/uploads/2026/05/siemianowski_realizacja_nowa_compressed.webp","tag":"Kancelaria premium / prawo i restrukturyzacja","desc":"Dla kancelarii Arkadiusza Siemianowskiego stworzyliśmy kompletny wizerunek premium oraz rozbudowaną stronę ekspercką. Projekt porządkuje specjalizacje prawne i restrukturyzacyjne, eksponuje doświadczenie oraz prowadzi użytkownika do kontaktu lub umówienia wizyty. Stonowany design buduje poczucie bezpieczeństwa, profesjonalizmu i wysokiej jakości obsługi.","meta":["branding","kancelaria","premium UI","wizyty"],"client":"Kancelaria Adwokata i Doradcy Restrukturyzacyjnego Arkadiusz Siemianowski","services":"Branding, projekt strony, WordPress, UX, copywriting, formularze kontaktowe, umawianie wizyt","scope":["Kierunek wizualny marki premium","Logo i system identyfikacji","Projekt rozbudowanej strony kancelarii","Struktura specjalizacji prawnych i restrukturyzacyjnych","Sekcje zaufania, doświadczenia i kontaktu","Elementy umawiania wizyt","Responsywny widok mobile","Przygotowanie pod treści eksperckie i SEO"],"tools":[],"results":[["Marka premium","Logo, typografia, kolorystyka i strona tworzą jeden konsekwentny system."],["Prostszy kontakt","Czytelne CTA i możliwość przejścia bezpośrednio do umawiania wizyty."],["Baza pod SEO","Struktura przygotowana pod rozwój specjalizacji i treści eksperckich."]],"challenge":"Zbudowanie zaufania jeszcze przed pierwszą rozmową oraz czytelne przedstawienie szerokiego zakresu specjalizacji.","solution":"Połączyliśmy elegancki branding z uporządkowaną architekturą informacji, treściami eksperckimi i prostą ścieżką umawiania spotkań.","effect":"Spójna marka kancelarii i serwis, który wspiera pozyskiwanie klientów oraz rozwój widoczności eksperckiej.","live":"https://siemianowski.pl/","mode":"web","project_slug":"siemianowski-kancelaria-premium-prawo-i-restrukturyzacja","case_url":"https://zaprojektowani.com/strony-internetowe/strona-internetowa-siemianowski/","caseStudyUrl":"https://zaprojektowani.com/strony-internetowe/strona-internetowa-siemianowski/"},{"brand":"Apartament Piękna","sub":"Nowa strona, rezerwacje online, branding i SEO","type":"Strona + Rezerwacje + SEO","year":"2026","img":"https://zaprojektowani.com/wp-content/uploads/2026/05/apartament_realizacja_compressed.webp","tag":"Beauty premium / medycyna estetyczna","desc":"Dla Apartamentu Piękna przeprowadziliśmy pełny redesign obecności marki w internecie. Odświeżyliśmy identyfikację, zaprojektowaliśmy elegancką stronę beauty i wdrożyliśmy autorski system rezerwacji z przypomnieniami SMS. Projekt łączy estetykę premium z wygodną obsługą wizyt i lokalnym SEO.","meta":["rezerwacje online","SEO lokalne","premium beauty"],"client":"Apartament Piękna — Tarnów","services":"Redesign strony, branding, WordPress, rezerwacje online, blog SEO, analityka","scope":["Całkowity redesign strony apartamentpiekna.pl","Odświeżenie logo i oprawy wizualnej marki","Projekt graficzny premium beauty","Autorski system rezerwacji wizyt online","Powiadomienia SMS dla klientów","Responsywna wersja mobilna","Autorski system bloga branżowego","Struktura SEO pod frazy lokalne","Konfiguracja Google Search Console, GA4 i Meta Pixel"],"tools":[],"results":[["Rezerwacje 24/7","Klientki mogą samodzielnie wybrać usługę i termin wizyty."],["Automatyzacja SMS","Przypomnienia ograniczają liczbę zapomnianych wizyt."],["Widoczność lokalna","Struktura i blog przygotowane pod frazy z Tarnowa i okolic."]],"challenge":"Uproszczenie rezerwacji zabiegów oraz uporządkowanie rozbudowanej oferty bez utraty luksusowego charakteru marki.","solution":"Zaprojektowaliśmy przejrzyste kategorie zabiegów, system rezerwacji 24/7, przypomnienia SMS i strukturę treści pod lokalne wyszukiwania.","effect":"Nowoczesny serwis, który odciąża recepcję, wspiera sprzedaż usług i konsekwentnie buduje wizerunek salonu premium.","live":"https://www.apartamentpiekna.pl/","mode":"web","project_slug":"apartament-piekna-strona-rezerwacje-seo","case_url":"https://zaprojektowani.com/strony-internetowe/strona-internetowa-apartament-piekna/","caseStudyUrl":"https://zaprojektowani.com/strony-internetowe/strona-internetowa-apartament-piekna/"},{"brand":"Gravia","sub":"Strona dla biura projektów infrastrukturalnych","type":"Strona firmowa + Kariera","year":"2026","img":"https://zaprojektowani.com/wp-content/uploads/2026/05/gravia_real_compressed.webp","tag":"Infrastruktura / branża drogowa","desc":"Dla biura projektów infrastrukturalnych Gravia stworzyliśmy techniczną, nowoczesną stronę pokazującą skalę kompetencji i realizacji. Projekt otrzymał uporządkowaną ofertę, rozbudowane portfolio oraz autorski moduł kariery z obsługą CV i załączników. Wizualnie połączyliśmy precyzję branży inżynieryjnej z lekkim, współczesnym UI.","meta":["techniczny UI","kariera","realizacje"],"client":"Gravia — Biuro Projektów Infrastrukturalnych","services":"Web Design, WordPress Dev, UX, System kariery","scope":["Indywidualny projekt graficzny strony","Nowoczesny układ pod branżę infrastrukturalną","Wersja desktop i mobile","Podstrony ofertowe i informacyjne","Formularze kontaktowe","Rozbudowana sekcja realizacji","Autorski system kariery z obsługą CV i plików"],"tools":["WordPress","PHP","SEO","UX/UI","Blog","Custom CMS"],"results":[["System kariery","Dedykowane oferty pracy, formularze i bezpieczna obsługa plików CV."],["Portfolio realizacji","Czytelna prezentacja projektów infrastrukturalnych i kompetencji zespołu."],["Techniczny wizerunek","Design dopasowany do branży inżynieryjnej i klientów instytucjonalnych."]],"challenge":"Przedstawienie złożonych usług technicznych w sposób zrozumiały dla inwestorów i atrakcyjny dla kandydatów.","solution":"Zbudowaliśmy klarowną strukturę usług, katalog realizacji i dedykowany system rekrutacyjny dostępny z poziomu panelu.","effect":"Profesjonalna platforma firmowa wspierająca sprzedaż B2B, prezentację doświadczenia i pozyskiwanie pracowników.","live":"https://gravia.pl/","mode":"web","project_slug":"gravia-strona-firmowa-kariera"},{"brand":"ShotHome","sub":"Strona usługowa + SEO + darmowa wycena online","type":"Strona + SEO + darmowa wycena","year":"2026","img":"https://zaprojektowani.com/wp-content/uploads/2026/05/shothome_realizacja-2_11zon.webp","tag":"Drzwi, podłogi i wykończenia / Warszawa","desc":"Dla ShotHome zaprojektowaliśmy stronę usługową nastawioną na szybkie pozyskiwanie zapytań z Warszawy. Serwis łączy ofertę drzwi, podłóg i wykończeń z customową darmową wyceną, katalogiem produktów oraz panelem realizacji. Całość przygotowaliśmy pod kampanie reklamowe, lokalne SEO i wygodną samodzielną edycję.","meta":["darmowa wycena","SEO Warszawa","panel realizacji","katalog podłóg"],"client":"ShotHome","services":"Projekt graficzny, WordPress, SEO, blog, custom wycena online, system realizacji, katalog produktów","scope":["Projekt strony od podstaw","Indywidualny projekt graficzny w kolorystyce marki","Wdrożenie WordPress","Customowy formularz darmowej wyceny online","Panel realizacji: dodawanie, edytowanie i usuwanie projektów","Katalog produktów podłogowych z obsługą w panelu","Blog i struktura treści pod SEO","Optymalizacja lokalna pod Warszawę","Responsywny widok mobile"],"tools":[],"results":[["Wycena online","Klient przekazuje zakres prac i oczekiwania bez długiej wymiany wiadomości."],["Własny panel","Zespół samodzielnie aktualizuje produkty, realizacje i treści."],["SEO Warszawa","Struktura dopasowana do lokalnych usług i kampanii reklamowych."]],"challenge":"Skrócenie drogi od zainteresowania ofertą do konkretnego zapytania oraz uporządkowanie wielu grup produktowych.","solution":"Wdrożyliśmy konfigurator wyceny, moduł realizacji, katalog produktów i rozbudowaną strukturę landingów usługowych.","effect":"Serwis pełniący jednocześnie funkcję prezentacji oferty, generatora leadów i bazy treści pod marketing lokalny.","live":"https://www.shothome.pl/","mode":"web"},{"brand":"Krawiec z dojazdem","sub":"Rebranding, nowa strona i system wizyt","type":"System + Rebranding","year":"2026","img":"https://zaprojektowani.com/wp-content/uploads/2026/05/krawiec_real_compressed.webp","tag":"Usługi premium / lokalnie","desc":"Dla usługi krawieckiej premium przygotowaliśmy rebranding, nową stronę oraz system umawiania wizyt z dojazdem do klienta. Projekt podkreśla indywidualne podejście, wygodę i wysoką jakość obsługi, jednocześnie upraszczając rezerwację spotkania. Mobile-first pozwala sprawnie umówić wizytę bezpośrednio z telefonu.","meta":["premium UI","mobile-first","SEO-ready"],"client":"Krawiec z dojazdem","services":"Branding, Web Design, WordPress Dev, SEO","scope":["Rebranding identyfikacji wizualnej","Strona WordPress od zera","System umawiania wizyt online","Optymalizacja SEO lokalne","Integracja Google Analytics","Design mobile-first"],"tools":[],"results":[["Nowy wizerunek","Rebranding porządkujący komunikację usługi premium."],["Wizyty online","Prosty proces umawiania spotkania z dojazdem."],["Mobile-first","Najważniejsze działania dostępne wygodnie z telefonu."]],"challenge":"Wyjaśnienie nietypowego modelu usługi i zamiana zainteresowania w konkretną rezerwację wizyty.","solution":"Połączyliśmy elegancki wizerunek z prostą ścieżką usług, lokalizacją dojazdu i kalendarzem spotkań.","effect":"Spójna marka premium i narzędzie, które wspiera pozyskiwanie lokalnych klientów bez ręcznego ustalania każdego terminu.","live":"https://krawieczdojazdem.pl/","mode":"web","project_slug":"krawiec-z-dojazdem-system-rebranding"},{"brand":"Papeterio","sub":"Subtelny sklep online z papeterią i personalizacją","type":"Sklep WooCommerce","year":"2026","img":"https://zaprojektowani.com/wp-content/uploads/2026/05/papeterio_real_compressed.webp","tag":"Papeteria / e-commerce","desc":"Dla Papeterio stworzyliśmy subtelny sklep internetowy z papeterią i dodatkami na wyjątkowe okazje. Kluczowe było połączenie delikatnej estetyki marki z czytelną personalizacją produktów, wariantami i dodatkami. Projekt prowadzi użytkownika spokojnie od inspiracji do finalizacji zamówienia.","meta":["WooCommerce","personalizacja","mobile UX"],"client":"Papeterio","services":"Web Design, WooCommerce, UX, Payments","scope":["Sklep internetowy z kategoriami produktów","Dopracowane karty produktów","Opcje personalizacji zamówienia","Warianty i dodatki","Koszyk i płatności online","Formularze kontaktowe","Wersja mobilna dopasowana do zakupów z telefonu"],"tools":[],"results":[["Czytelna personalizacja","Warianty, teksty i dodatki są wybierane bezpośrednio przy produkcie."],["Zakupy mobilne","Interfejs dopracowany pod klientów zamawiających z telefonu."],["Spójna estetyka","Sklep, opakowania i komunikacja tworzą jednolite doświadczenie marki."]],"challenge":"Pokazanie wielu wariantów personalizacji w sposób prosty, estetyczny i niewymagający dodatkowego kontaktu.","solution":"Uporządkowaliśmy opcje produktu, dodatki i pola personalizacji, projektując lekkie karty produktowe oraz wygodny koszyk.","effect":"Elegancki e-commerce, który zachowuje emocjonalny charakter marki, a jednocześnie ułatwia składanie złożonych zamówień.","live":"https://papeterio.pl/","mode":"web"},{"brand":"Bransoletka24","sub":"Sklep online z hurtem i detalem","type":"Sklep WooCommerce","year":"2026","img":"https://zaprojektowani.com/wp-content/uploads/2026/05/bransoletka24_real_compressed.webp","tag":"E-commerce / detal + hurt","desc":"Bransoletka24 otrzymała nowy sklep WooCommerce obsługujący równolegle sprzedaż detaliczną i hurtową. Zaprojektowaliśmy przejrzyste kategorie, szybkie filtrowanie, warianty produktów, wishlistę i uproszczony checkout. Całość powstała z naciskiem na konwersję, szybkość oraz wygodę klientów B2C i B2B.","meta":["WooCommerce","checkout","B2B + B2C"],"client":"Bransoletka24","services":"Web Design, WooCommerce, Payments, UX","scope":["Sklep WooCommerce od zera","System hurtowy z rejestracją B2B","Integracja płatności online","Wishlist i warianty produktów","Animacje GSAP","Optymalizacja konwersji"],"tools":[],"results":[["B2C + B2B","Jeden sklep z osobnymi zasadami obsługi klientów detalicznych i hurtowych."],["Szybszy wybór","Filtry, warianty i wishlista ułatwiają pracę z szerokim katalogiem."],["Lepszy checkout","Ograniczona liczba kroków i czytelne podsumowanie zamówienia."]],"challenge":"Obsługa dwóch grup klientów i rozbudowanego katalogu bez przeciążania interfejsu.","solution":"Wdrożyliśmy osobne mechanizmy cenowe dla hurtu, intuicyjne filtry, listę ulubionych i uproszczoną ścieżkę zakupu.","effect":"Skalowalny sklep, który porządkuje katalog i ułatwia zakupy zarówno klientom indywidualnym, jak i partnerom hurtowym.","live":"https://bransoletka24.pl/","mode":"web"},{"brand":"RUTPOŻ","sub":"Strona, system zgłoszeń i asystent AI","type":"System + AI","year":"2025","img":"https://zaprojektowani.com/wp-content/uploads/2026/05/rutpoz_rea_compressed.jpg","tag":"PPOŻ / system zgłoszeń + AI","desc":"Dla RUTPOŻ stworzyliśmy rozbudowaną platformę firmową łączącą ofertę PPOŻ z narzędziami użytkowymi. Serwis zawiera system zgłoszeń serwisowych, kalkulatory branżowe, generowanie raportów PDF oraz asystenta AI odpowiadającego na pytania klientów. Projekt zamienia klasyczną stronę firmową w praktyczne centrum obsługi.","meta":["AI chatbot","kalkulatory","B2B"],"client":"RUTPOŻ","services":"Web, System zgłoszeń, AI Chatbot, Kalkulatory","scope":["Strona firmowa WordPress","Asystent AI 24/7","Kalkulator wydajności hydrantów","Kalkulator gęstości ogniowej","Generowanie raportów PDF","System zgłoszeń serwisowych"],"tools":[],"results":[["Asystent AI 24/7","Odpowiedzi na podstawowe pytania bez oczekiwania na kontakt z działem obsługi."],["Kalkulatory PPOŻ","Praktyczne narzędzia branżowe dostępne bezpośrednio na stronie."],["Cyfrowe zgłoszenia","Uporządkowana obsługa serwisu i generowanie dokumentacji PDF."]],"challenge":"Przeniesienie technicznych procesów i powtarzalnych zapytań klientów do jednego, łatwego w obsłudze systemu.","solution":"Połączyliśmy stronę ofertową z kalkulatorami, formularzami serwisowymi, raportami i bazą wiedzy obsługiwaną przez AI.","effect":"Platforma ograniczająca ręczną pracę zespołu i dostarczająca klientom odpowiedzi oraz narzędzia przez całą dobę.","live":"https://rutpoz.pl/","mode":"web"},{"brand":"Druga Szansa","sub":"Strona sieci sklepów z sekcją franczyzy","type":"Sieć / Franczyza","year":"2025","img":"https://zaprojektowani.com/wp-content/uploads/2026/05/druga_szansa_real_compressed.webp","tag":"Sieć sklepów / franczyza","desc":"Dla sieci sklepów Druga Szansa przygotowaliśmy stronę łączącą prezentację marki, lokalizacje punktów oraz rozwój franczyzy. Użytkownik może szybko znaleźć najbliższy sklep, poznać model współpracy i wysłać zgłoszenie franczyzowe. Projekt wspiera również kampanie leadowe i komunikację kodów promocyjnych.","meta":["lead gen","franczyza","mapa punktów"],"client":"Druga Szansa","services":"Web Design, Lead Gen, Mapa lokalizacji","scope":["Strona sieci sklepów","Sekcja franczyzy z formularzem","Mapa punktów sprzedaży","System kodów rabatowych","Integracja WooCommerce","Kampanie leadowe"],"tools":[],"results":[["Mapa punktów","Szybkie wyszukiwanie najbliższego sklepu i danych kontaktowych."],["Leady franczyzowe","Dedykowana prezentacja modelu współpracy i formularz zgłoszeniowy."],["Wsparcie kampanii","Landingowe sekcje i mechanizmy kodów rabatowych."]],"challenge":"Połączenie potrzeb klientów detalicznych, obecnych placówek i potencjalnych franczyzobiorców w jednym serwisie.","solution":"Zaprojektowaliśmy mapę punktów, rozbudowaną sekcję franczyzy, formularze leadowe i moduły promocyjne.","effect":"Centralna platforma marki wspierająca ruch w sklepach oraz pozyskiwanie nowych partnerów biznesowych.","live":"","mode":"web","liveGone":true},{"brand":"Inusti","sub":"Strona B2B nastawiona na szybkie zapytania","type":"Strona B2B","year":"2025","img":"https://zaprojektowani.com/wp-content/uploads/2026/05/inusti_real_compressed.webp","tag":"Firma budowlana / B2B","desc":"Dla firmy Inusti stworzyliśmy stronę B2B, która szybko komunikuje zakres usług budowlanych i prowadzi do zapytania ofertowego. Projekt wykorzystuje realizacje jako główny dowód kompetencji, a strukturę przygotowaliśmy pod kampanie Google Ads i rozwój SEO. Całość jest prosta, techniczna i wygodna na urządzeniach mobilnych.","meta":["B2B","lead flow","SEO-ready"],"client":"Inusti","services":"Web Design, WordPress Dev, SEO","scope":["Strona firmowa B2B","Formularz szybkiego kontaktu","Sekcja realizacji z galeriami","Optymalizacja pod Google Ads","Struktura SEO on-site","Design mobile-first"],"tools":[],"results":[["Lead flow","Kontakt dostępny bez szukania i zbędnych kroków."],["Portfolio B2B","Realizacje pokazujące skalę prac i doświadczenie firmy."],["Gotowość reklamowa","Struktura przygotowana pod kampanie Google Ads i SEO."]],"challenge":"Przedstawienie usług wykonawczych w sposób konkretny oraz maksymalne skrócenie drogi do kontaktu.","solution":"Zbudowaliśmy klarowne landing pages, wyeksponowane realizacje i szybki formularz dostępny w kluczowych punktach strony.","effect":"Lekki serwis sprzedażowy, który wspiera kampanie, buduje wiarygodność i generuje zapytania B2B.","live":"","mode":"web"},{"brand":"Piekarnia Marysia","sub":"Strona lokalna z ofertą i kierunkiem pod rozwój","type":"Strona lokalna","year":"2025","img":"https://zaprojektowani.com/wp-content/uploads/2026/05/piekarnia_marysia_real_compressed.webp","tag":"Piekarnia / gastronomia","desc":"Dla lokalnej Piekarni Marysia przygotowaliśmy ciepłą, minimalistyczną stronę prezentującą ofertę, produkty i charakter marki. Projekt ułatwia sprawdzenie menu, lokalizacji i godzin otwarcia, a galeria buduje apetyt jeszcze przed wizytą. Serwis został przygotowany pod lokalne wyniki Google i wygodne korzystanie z telefonu.","meta":["local SEO","oferta","szybki UX"],"client":"Piekarnia Marysia","services":"Web Design, SEO lokalne, Fotografia","scope":["Strona wizytówka piekarni","Prezentacja menu i produktów","Galeria z fotografią produktową","Integracja Google Maps","Optymalizacja SEO lokalne","Responsywny design"],"tools":[],"results":[["Oferta pod ręką","Menu i najważniejsze produkty dostępne w czytelnej formie."],["Lokalne SEO","Dane, mapa i struktura wspierające wyszukiwania w okolicy."],["Szybki mobile","Najważniejsze informacje widoczne od razu na telefonie."]],"challenge":"Przeniesienie rodzinnego charakteru piekarni do internetu i szybkie dostarczenie najważniejszych informacji lokalnym klientom.","solution":"Połączyliśmy prostą ofertę, fotografie produktów, mapę i dane kontaktowe w lekkiej strukturze mobile-first.","effect":"Czytelna wizytówka marki, która wspiera ruch w lokalu i zwiększa widoczność w lokalnych wyszukiwaniach.","live":"","mode":"web","project_slug":"piekarnia-marysia-strona-lokalna"},{"isCta":true,"mode":"web","brand":"Tu może być Twoja strona","sub":"otrzymaj darmową wycenę","type":"Darmowa wycena","year":"2026","tag":"Twój projekt","live":"/studio-wyceny/"}];

  const logoItems = [{"brand":"Natalia Arciszewska","sub":"logo i identyfikacja wizualna radcy prawnego","type":"Logo + mini branding","year":"2026","img":"https://zaprojektowani.com/wp-content/uploads/2026/09/arciszewska.webp","tag":"Kancelaria / Radca prawny","desc":"Dla Natalii Arciszewskiej przygotowaliśmy elegancką, nowoczesną identyfikację dla kancelarii radcy prawnego. Punktem wyjścia było 10 kierunków logo; finalny znak dopracowaliśmy w granatowo-beżowej palecie ze złotym akcentem, tak aby marka budowała zaufanie i wyglądała premium zarówno online, jak i w druku.","meta":["Prawo","Granat + beż","Premium"],"client":"Natalia Arciszewska — Radca prawny","services":"Logo, mini branding, papeteria, wizytówki, stopka e-mail, mockupy","scope":["10 propozycji logo","Dopracowanie wybranego znaku i typografii","Paleta granatowo-beżowa ze złotym akcentem","Wizytówki z kodem QR i LinkedIn","Papier firmowy i teczka z kieszonką","Stopka e-mail i materiały firmowe","Mockupy i wizualizacje zastosowań"],"tools":["Illustrator","Photoshop","Figma","InDesign","Mockupy","Druk"],"results":[["Spójny system","Logo, kolorystyka i typografia tworzą konsekwentny, profesjonalny wizerunek kancelarii."],["Gotowe materiały","Identyfikację przełożyliśmy na wizytówki, papier firmowy, teczkę i stopkę e-mail."],["Premium bez przesady","Granat, beż i subtelne złoto budują zaufanie bez typowej, ciężkiej estetyki prawniczej."]],"challenge":"Stworzyć markę prawniczą, która jest profesjonalna i premium, ale nie wygląda ciężko ani schematycznie.","solution":"Przygotowaliśmy 10 kierunków logo, a następnie dopracowaliśmy wybraną koncepcję, typografię i paletę granatowo-beżową ze złotym akcentem. System rozszerzyliśmy na najważniejsze materiały firmowe.","effect":"Powstała spójna identyfikacja, która dobrze działa na dokumentach, wizytówkach, materiałach cyfrowych i jako baza pod stronę internetową.","live":"","mode":"logo","project_slug":"natalia-arciszewska-logo-mini-branding"},{"brand":"Zgórecki Nieruchomości","sub":"logo i mini branding marki nieruchomości","type":"Logo + mini branding","year":"2026","img":"https://zaprojektowani.com/wp-content/uploads/2026/09/zgorecki.webp","tag":"Nieruchomości / Marka osobista","desc":"Dla marki Wojciech Zgórecki Nieruchomości stworzyliśmy elegancki system logo i mini brandingu łączący osobisty charakter marki z estetyką segmentu premium. Projekt powstał na bazie 10 kierunków, a wybrany znak dopracowaliśmy pod kątem proporcji, grubości formy, złotych wariantów kolorystycznych i praktycznego użycia w materiałach sprzedażowych.","meta":["Nieruchomości","Złoto","Premium"],"client":"Wojciech Zgórecki Nieruchomości","services":"Logo, mini branding, warianty kolorystyczne, mockupy, materiały marki","scope":["10 propozycji logo","Dopracowanie proporcji i grubości znaku","Złote warianty kolorystyczne","Typografia marki","Zestaw wersji logo do druku i internetu","Mockupy nieruchomościowe i materiały prezentacyjne"],"tools":["Illustrator","Photoshop","Figma","Mockupy","Druk"],"results":[["Rozpoznawalny znak","Dopracowany symbol i typografia tworzą wyrazisty podpis marki osobistej."],["Elastyczne warianty","Przygotowane wersje kolorystyczne działają w druku, online i na materiałach ofertowych."],["Wizerunek premium","Złote akcenty i spokojna kompozycja wspierają eleganckie pozycjonowanie marki nieruchomości."]],"challenge":"Połączyć osobisty charakter marki pośrednika z estetyką premium i zachować czytelność znaku także w małej skali.","solution":"Na bazie 10 kierunków wybraliśmy i dopracowaliśmy znak, korygując proporcje, grubość formy, odcień złota i typografię. Następnie przygotowaliśmy zestaw wariantów oraz mockupy pokazujące markę w praktycznych zastosowaniach.","effect":"Marka otrzymała elegancki, elastyczny system logo gotowy do ofert nieruchomości, social mediów, materiałów drukowanych i przyszłej komunikacji online.","live":"","mode":"logo","project_slug":"zgorecki-nieruchomosci-logo-mini-branding"},{"brand":"Siemianowski","sub":"Logo i branding kancelarii premium","type":"Logo + branding premium","year":"2026","img":"https://zaprojektowani.com/wp-content/uploads/2026/05/siemianowski_real_logo_compressed.webp","tag":"Kancelaria / Restrukturyzacja","desc":"Dla kancelarii Siemianowski opracowaliśmy elegancki system identyfikacji oparty na stonowanej typografii, szlachetnej kolorystyce i znaku o profesjonalnym charakterze. Logo zostało zaprojektowane tak, aby równie dobrze działało na dokumentach, tablicach, stronie internetowej i materiałach firmowych. Całość buduje wizerunek kancelarii premium bez korzystania z oczywistych symboli prawniczych.","meta":["Premium","Kancelaria","Elegancja"],"client":"Siemianowski","services":"Logo, branding, mockupy, materiały firmowe","scope":["Projekt logo z wariantami","Kolorystyka i typografia","Mockupy i wizualizacje","Materiały do druku","Identyfikacja kancelarii","Wytyczne do strony WWW"],"tools":["Illustrator","Photoshop","Figma","Mockupy","Druk"],"results":[["Autorski znak","Logo wyróżniające kancelarię bez branżowych klisz."],["Pełny system","Kolory, typografia i warianty gotowe do zastosowań online i offline."],["Wizerunek premium","Spójne materiały podnoszące wiarygodność marki."]],"challenge":"Stworzenie rozpoznawalnego znaku dla branży prawnej bez powielania motywów wagi, kolumn i paragrafów.","solution":"Postawiliśmy na autorski układ typograficzny, dopracowane proporcje oraz system zastosowań formalnych i cyfrowych.","effect":"Ponadczasowa identyfikacja wzmacniająca ekspercki i prestiżowy charakter kancelarii.","live":"","mode":"logo","project_slug":"siemianowski-logo-branding-premium","case_url":"https://zaprojektowani.com/strony-internetowe/strona-internetowa-siemianowski/","caseStudyUrl":"https://zaprojektowani.com/strony-internetowe/strona-internetowa-siemianowski/"},{"brand":"POLERSTONE","sub":"Nowoczesne logo dla usług kamieniarskich","type":"Logo + identyfikacja","year":"2026","img":"https://zaprojektowani.com/wp-content/uploads/2026/05/polerstone_logo_compressed.webp","tag":"Kamieniarstwo premium","desc":"Dla marki POLERSTONE stworzyliśmy nowoczesne logo podkreślające precyzję obróbki kamienia, solidność i wysoką jakość realizacji. Geometryczny monogram PS został przygotowany tak, aby dobrze prezentował się zarówno na jasnych materiałach firmowych, jak i bezpośrednio na kamieniu, odzieży czy oznakowaniu. Charakter identyfikacji łączy techniczność z segmentem premium.","meta":["Monogram","Premium","Kamień"],"client":"POLERSTONE","services":"Logo, branding, mockupy, materiały firmowe","scope":["Logo marki POLERSTONE","Sygnet / monogram PS","Wersje kolorystyczne","Mockupy prezentacyjne","Wizualizacje na kamieniu","Materiały firmowe"],"tools":["Illustrator","Photoshop","Figma","Mockupy","Druk"],"results":[["Monogram PS","Zwarty znak dobrze działający w małych i dużych formatach."],["Zastosowania techniczne","Warianty przygotowane pod grawer, druk i oznakowanie."],["Charakter premium","Minimalistyczny system wspierający pozycjonowanie jakościowe."]],"challenge":"Zbudowanie nowoczesnego wizerunku dla tradycyjnej branży i zapewnienie czytelności znaku na wymagających powierzchniach.","solution":"Opracowaliśmy zwarty monogram, mocną typografię i ograniczoną paletę łatwą do wdrożenia w produkcji.","effect":"Rozpoznawalny system wizualny gotowy do oznakowania produktów, pojazdów i materiałów sprzedażowych.","live":"","mode":"logo","project_slug":"polerstone-logo-identyfikacja"},{"brand":"Pracownia Urody","sub":"Delikatny branding beauty dla Joanny Samborskiej","type":"Logo + identyfikacja","year":"2026","img":"https://zaprojektowani.com/wp-content/uploads/2026/05/pracownia_urody_logo_compressed.webp","tag":"Beauty / Kosmetologia","desc":"Dla Pracowni Urody Joanny Samborskiej zaprojektowaliśmy subtelną identyfikację podkreślającą kobiecość, spokój i profesjonalizm gabinetu. Delikatny sygnet z profilem twarzy współpracuje z elegancką typografią oraz jasną paletą kolorów. System został przygotowany do komunikacji w social mediach, materiałach drukowanych i opakowaniach.","meta":["Beauty","Subtelnie","Kobieco"],"client":"Pracownia Urody","services":"Logo, branding, mockupy, materiały firmowe","scope":["Logo marki","Subtelny sygnet z profilem","Wersje kolorystyczne","Mockupy prezentacyjne","Wizualizacje na opakowaniach","Materiały firmowe i social media"],"tools":["Illustrator","Photoshop","Figma","Mockupy","Druk"],"results":[["Subtelny sygnet","Charakterystyczny profil twarzy bez nadmiernej dosłowności."],["Paleta beauty","Kolory wspierające poczucie spokoju i jakości."],["Gotowa komunikacja","Materiały do druku, opakowań i social mediów."]],"challenge":"Połączenie delikatnej estetyki beauty z wiarygodnym, profesjonalnym charakterem usług.","solution":"Stworzyliśmy lekki znak, spokojną paletę i zestaw materiałów łatwych do konsekwentnego stosowania.","effect":"Spójny i rozpoznawalny wizerunek, który wzmacnia atmosferę gabinetu jeszcze przed wizytą.","live":"","mode":"logo","project_slug":"pracownia-urody-logo-identyfikacja"},{"brand":"Arcycięcie","sub":"Charakterystyczny branding studia fryzur","type":"Logo + identyfikacja","year":"2025","img":"https://zaprojektowani.com/wp-content/uploads/2026/05/arcy_ciecie_logo_compressed.webp","tag":"Barber / Fryzjerstwo","desc":"Dla studia fryzur Arcycięcie przygotowaliśmy charakterystyczny branding łączący energię nowoczesnego salonu z detalami kojarzonymi z rzemiosłem fryzjerskim. Znak wykorzystuje motyw brzytwy i nożyczek, ale został uproszczony tak, aby zachować czytelność i współczesny charakter. Identyfikację rozwinięliśmy na oznakowanie salonu, materiały drukowane i social media.","meta":["Logo premium","Materiały salonu","Branding"],"client":"Arcycięcie","services":"Logo, branding, mockupy, materiały firmowe","scope":["Projekt logo z wariantami","Kolorystyka i typografia","Mockupy i wizualizacje","Materiały do druku","Elementy oznakowania salonu","Szablony social media"],"tools":["Illustrator","Photoshop","Figma","Mockupy","Druk"],"results":[["Wyrazisty znak","Motyw fryzjerski zamieniony w nowoczesny symbol marki."],["Oznakowanie salonu","Spójne zastosowania na szyldach, witrynie i materiałach wnętrza."],["Social ready","System przygotowany do codziennej komunikacji w mediach społecznościowych."]],"challenge":"Stworzenie wyrazistej marki fryzjerskiej bez efektu ciężkiego, typowego logo barberskiego.","solution":"Połączyliśmy dynamiczny znak z nowoczesną typografią i elastycznym systemem komunikacji.","effect":"Branding, który jest łatwo rozpoznawalny i dobrze pracuje zarówno w przestrzeni salonu, jak i online.","live":"","mode":"logo","project_slug":"arcyciecie-logo-identyfikacja"},{"brand":"Vista Architekci","sub":"Minimalistyczny monogram dla biura projektowego","type":"Logo + identyfikacja","year":"2025","img":"https://zaprojektowani.com/wp-content/uploads/2026/05/vista_logo_compressed.webp","tag":"Architekci","desc":"Dla Vista Architekci zaprojektowaliśmy minimalistyczny monogram VA inspirowany geometrią, przekrojami budowlanymi i światłem. Znak jest precyzyjny, oszczędny i dopasowany do profesjonalnej prezentacji projektów architektonicznych. Identyfikację rozszerzyliśmy o brandbook, papeterię i szablony ofertowe.","meta":["Minimalizm","Architektura","Brandbook"],"client":"Vista Architekci","services":"Logo, branding, mockupy, materiały firmowe","scope":["Sygnet monogramowy VA","Brandbook z wytycznymi","Papeteria firmowa","Szablony ofertowe","Wizytówki i teczki","Materiały do prezentacji"],"tools":["Illustrator","Figma","InDesign","Mockupy","Brandbook"],"results":[["Monogram VA","Geometryczny znak inspirowany językiem architektury."],["Brandbook","Czytelne reguły stosowania logo, kolorów i typografii."],["Materiały ofertowe","Spójna papeteria i szablony prezentacji projektów."]],"challenge":"Oddanie charakteru pracowni architektonicznej w prostym znaku bez dosłownych ikon budynku.","solution":"Oparliśmy monogram na proporcjach i negatywnej przestrzeni, tworząc system o technicznym, ale eleganckim charakterze.","effect":"Ponadczasowa identyfikacja wspierająca profesjonalne prezentacje, oferty i dokumentację projektową.","live":"","mode":"logo"},{"brand":"Akoya","sub":"Gabinet kosmetyczny premium","type":"Logo + identyfikacja","year":"2025","img":"https://zaprojektowani.com/wp-content/uploads/2026/05/akoya_logo_compressed.webp","tag":"Beauty & Wellness","desc":"Dla gabinetu kosmetycznego Akoya przygotowaliśmy spokojną identyfikację premium inspirowaną naturalnym pięknem i perłowym charakterem nazwy. Beżowo-złota paleta, miękka typografia i delikatne formy budują poczucie luksusu bez przesadnego przepychu. System obejmuje logo, materiały zabiegowe, komunikację social media i podstawowe wytyczne marki.","meta":["Beauty","Mini brandbook","Premium"],"client":"Akoya","services":"Logo, branding, mockupy, materiały firmowe","scope":["Logo z wariantami kolorowymi","Mini brandbook","Wizytówki i karty zabiegowe","Szablony social media","Paleta kolorów i fonty","Mockupy i wizualizacje"],"tools":["Illustrator","Photoshop","Figma","Canva","Druk"],"results":[["Spokojny luksus","Estetyka premium bez chłodnego, klinicznego charakteru."],["Mini brandbook","Zasady kolorów, fontów i wariantów logo."],["Materiały gabinetu","Karty zabiegowe, wizytówki i szablony komunikacji."]],"challenge":"Zbudowanie luksusowego wizerunku, który pozostaje ciepły, dostępny i kobiecy.","solution":"Zastosowaliśmy miękką typografię, subtelny detal i elegancką paletę dobrze działającą w gabinecie oraz online.","effect":"Spójna marka beauty wzmacniająca poczucie jakości i komfortu na każdym etapie kontaktu.","live":"","mode":"logo"},{"brand":"Eat Tasty","sub":"Apetyczny branding dowozu jedzenia","type":"Logo + identyfikacja","year":"2025","img":"https://zaprojektowani.com/wp-content/uploads/2026/05/eat_tasty_logo_compressed.webp","tag":"Gastro / Delivery","desc":"Dla Eat Tasty stworzyliśmy energetyczny branding marki delivery, którego znak łączy formę talerza z ruchem i kierunkiem dostawy. Kolorystyka została dobrana tak, aby dobrze działać na opakowaniach, aplikacjach i materiałach promocyjnych. System jest prosty, apetyczny i łatwy do zauważenia w dynamicznym otoczeniu gastronomii.","meta":["Gastro","Opakowania","Social media"],"client":"Eat Tasty","services":"Logo, branding, mockupy, materiały firmowe","scope":["Logo z symbolem delivery","Projekty opakowań i boksów","Naklejki i etykiety","Materiały social media","System kolorystyczny","Mockupy na opakowaniach"],"tools":["Illustrator","Photoshop","Mockupy","Druk"],"results":[["Symbol delivery","Talerz i ruch dostawy zamknięte w jednym znaku."],["System opakowań","Spójne boksy, naklejki i etykiety produktowe."],["Widoczność marki","Kolory i układ zaprojektowane pod szybkie rozpoznanie."]],"challenge":"Połączenie skojarzenia z jedzeniem i szybką dostawą w jednym prostym symbolu.","solution":"Opracowaliśmy dynamiczny znak, wyrazistą paletę i zestaw zastosowań na opakowaniach oraz komunikacji online.","effect":"Rozpoznawalna identyfikacja gotowa do skalowania wraz z ofertą i obszarem dostaw.","live":"","mode":"logo","project_slug":"eat-tasty-logo-identyfikacja"},{"brand":"Fryz Dobry Barber","sub":"Męski branding barbershopu","type":"Logo + identyfikacja","year":"2025","img":"https://zaprojektowani.com/wp-content/uploads/2026/05/fryz_dobry_logo_compressed.webp","tag":"Barber / Men’s","desc":"Dla Fryz Dobry Barber przygotowaliśmy pełną identyfikację inspirowaną klasycznym rzemiosłem barberskim. Brodaty sygnet i typografia vintage budują męski, charakterystyczny klimat, a system materiałów pozwala konsekwentnie oznaczyć wnętrze, szyld i komunikację. Projekt zachowuje styl retro, ale pozostaje czytelny i funkcjonalny.","meta":["Barbershop","Vintage","Materiały wnętrza"],"client":"Fryz Dobry Barber","services":"Logo, branding, mockupy, materiały firmowe","scope":["Sygnet z brodatym motywem","Typografia vintage","Materiały do wnętrza","Wizytówki i karty","Szyld i oznakowanie","System identyfikacji"],"tools":["Illustrator","Photoshop","InDesign","Mockupy","Druk"],"results":[["Brodaty sygnet","Charakterystyczny symbol dopasowany do męskiego salonu."],["Styl vintage","Typografia i detale budujące rzemieślniczy klimat."],["Wnętrze i druk","Materiały gotowe do oznakowania salonu i obsługi klientów."]],"challenge":"Uzyskanie klasycznego charakteru bez nadmiernej ilości detali i problemów z czytelnością znaku.","solution":"Uprościliśmy ilustracyjny motyw i połączyliśmy go z mocną typografią oraz ograniczoną paletą.","effect":"Spójny klimat barbershopu widoczny od szyldu po wizytówkę i media społecznościowe.","live":"","mode":"logo"},{"brand":"Piotr Mazur","sub":"Monogram PM dla kancelarii adwokackiej","type":"Logo + identyfikacja","year":"2025","img":"https://zaprojektowani.com/wp-content/uploads/2026/05/piotr_mazur_logo_compressed.webp","tag":"Kancelaria","desc":"Dla kancelarii adwokackiej Piotra Mazura stworzyliśmy elegancki monogram PM osadzony w formie subtelnej tarczy. Znak nawiązuje do ochrony i pewności, ale unika oczywistych symboli prawniczych. Identyfikacja została przygotowana do dokumentów, tablic kancelarii, wizytówek i komunikacji cyfrowej.","meta":["Kancelaria","Monogram","Premium"],"client":"Piotr Mazur","services":"Logo, branding, mockupy, materiały firmowe","scope":["Monogram PM w formie tarczy","Papeteria firmowa","Tablice kancelarii","Wizytówki premium","Wytyczne do strony WWW","Brandbook"],"tools":["Illustrator","Figma","InDesign","Mockupy","Druk","Brandbook"],"results":[["Monogram PM","Autorsko połączone inicjały w stabilnej formie."],["Symbol ochrony","Tarcza sugerująca bezpieczeństwo bez dosłowności."],["Formalne zastosowania","Papeteria, tablice i materiały cyfrowe w jednym systemie."]],"challenge":"Zaprojektowanie prestiżowego znaku prawnego, który wyróżnia się na tle standardowych herbów i wag.","solution":"Połączyliśmy inicjały w zwartą formę tarczy i zbudowaliśmy wokół niej oszczędny system typograficzny.","effect":"Profesjonalna identyfikacja budująca zaufanie i dobrze działająca w formalnych zastosowaniach.","live":"","mode":"logo","project_slug":"piotr-mazur-logo-identyfikacja"},{"brand":"Okruszek","sub":"Pastelowe logo pracowni cukierniczej","type":"Logo + identyfikacja","year":"2025","img":"https://zaprojektowani.com/wp-content/uploads/2026/05/okruszek_logo_compressed.webp","tag":"Cukiernia / Słodkości","desc":"Dla pracowni cukierniczej Okruszek stworzyliśmy pastelową, pogodną identyfikację z subtelnym motywem tortu. Projekt miał zachować rękodzielniczy i rodzinny charakter marki, a jednocześnie dobrze prezentować się na pudełkach, etykietach i witrynie. Lekka paleta i przyjazna typografia budują apetyczny, łatwo rozpoznawalny styl.","meta":["Cukiernia","Pastel","Opakowania"],"client":"Okruszek","services":"Logo, branding, mockupy, materiały firmowe","scope":["Logo z symbolem tortu","Projekty pudełek i opakowań","Etykiety na produkty","Oznakowanie witryny","Paleta pastelowa","Mockupy na pudełkach"],"tools":["Illustrator","Photoshop","Mockupy","Druk"],"results":[["Symbol tortu","Czytelny motyw pracowni cukierniczej."],["Pastelowy system","Kolory budujące lekki, apetyczny charakter."],["Opakowania marki","Pudełka, etykiety i oznakowanie witryny."]],"challenge":"Stworzenie słodkiej estetyki bez przesadnej infantylności i z zachowaniem dobrej czytelności.","solution":"Zaprojektowaliśmy prosty symbol, pastelowy system kolorów i praktyczne materiały opakowaniowe.","effect":"Ciepła identyfikacja, która wyróżnia produkty i wzmacnia doświadczenie ich wręczania.","live":"","mode":"logo"},{"brand":"Mona Cake","sub":"Branding pracowni tortów artystycznych","type":"Logo + identyfikacja","year":"2025","img":"https://zaprojektowani.com/wp-content/uploads/2026/05/mona_logo_compressed.webp","tag":"Cukiernia / Torty","desc":"Dla Mona Cake zaprojektowaliśmy bajkowy, ale uporządkowany branding pracowni tortów artystycznych. Sygnet z babeczką, pastelowa paleta i miękka typografia podkreślają kreatywność wypieków oraz indywidualny charakter realizacji. System został rozwinięty na pudełka, etykiety i komunikację na Instagramie.","meta":["Torty","Pastel","Social media"],"client":"Mona Cake","services":"Logo, branding, mockupy, materiały firmowe","scope":["Logo z babeczką i sygnet","Projekty pudełek na torty","Wizytówki i etykiety","Szablony Instagram","System kolorystyczny","Mockupy"],"tools":["Illustrator","Photoshop","Canva","Mockupy","Druk"],"results":[["Sygnet babeczki","Przyjazny znak związany bezpośrednio z ofertą."],["Opakowania tortów","Pudełka i etykiety wzmacniające efekt produktu premium."],["Instagram ready","Szablony ułatwiające regularną prezentację realizacji."]],"challenge":"Oddanie artystycznego charakteru tortów bez utraty profesjonalnego wyglądu marki.","solution":"Połączyliśmy ilustracyjny sygnet z prostą typografią i spójną paletą do zastosowań produktowych.","effect":"Rozpoznawalna marka, która dobrze prezentuje się na wypiekach, opakowaniach i w social mediach.","live":"","mode":"logo","project_slug":"mona-cake-logo-identyfikacja"},{"brand":"Wonder Handmade","sub":"Eleganckie logo dla marki handmade","type":"Logo + identyfikacja","year":"2025","img":"https://zaprojektowani.com/wp-content/uploads/2026/05/wonder_logo_compressed.webp","tag":"Handmade / Rękodzieło","desc":"Dla Wonder Handmade stworzyliśmy minimalistyczną identyfikację, która podkreśla ręczne wykonanie i biżuteryjny charakter produktów. Subtelny sygnet dobrze pracuje w małym formacie na etykietach, zawieszkach i opakowaniach. Całość uzupełnia brandbook oraz system materiałów wspierających sprzedaż online i offline.","meta":["Handmade","Etykiety","Brandbook"],"client":"Wonder Handmade","services":"Logo, branding, mockupy, materiały firmowe","scope":["Sygnet biżuteryjny","Brandbook z wytycznymi","Etykiety na produkty","Projekty opakowań","Wizytówki i papeteria","Szablony social media"],"tools":["Illustrator","Figma","Photoshop","Mockupy","Brandbook"],"results":[["Biżuteryjny sygnet","Delikatny znak działający na małych etykietach."],["Brandbook","Spójne zasady dla kolorów, typografii i logo."],["Materiały produktowe","Opakowania, zawieszki i komunikacja social media."]],"challenge":"Zaprojektowanie delikatnego znaku, który pozostaje czytelny na bardzo małych elementach produktowych.","solution":"Opracowaliśmy prosty sygnet, oszczędną typografię i skalowalne warianty do druku oraz znakowania.","effect":"Elegancka marka handmade, która zwiększa wartość postrzeganą produktu i porządkuje komunikację.","live":"","mode":"logo"},{"brand":"Sfera","sub":"Naturalny branding studia well-being","type":"Logo + identyfikacja","year":"2025","img":"https://zaprojektowani.com/wp-content/uploads/2026/05/sfera_logo_compressed.webp","tag":"Well-being / Holistycznie","desc":"Dla studia well-being Sfera stworzyliśmy naturalną identyfikację opartą na organicznym symbolu i spokojnej palecie beży oraz zieleni. Projekt komunikuje równowagę, uważność i holistyczne podejście bez korzystania z nadmiernie ezoterycznych motywów. System został przygotowany do materiałów gabinetowych i codziennej komunikacji.","meta":["Well-being","Naturalne kolory","Minimalizm"],"client":"Sfera","services":"Logo, branding, mockupy, materiały firmowe","scope":["Logo z organicznym symbolem","Paleta beży i zieleni","Wizytówki i karty klienta","Szablony komunikacji","Mockupy i wizualizacje","Materiały gabinetowe"],"tools":["Illustrator","Figma","Canva","Mockupy"],"results":[["Organiczny symbol","Prosty znak odnoszący się do równowagi i całości."],["Naturalna paleta","Beże i zielenie tworzące spokojne doświadczenie marki."],["Materiały gabinetowe","Karty klienta, wizytówki i szablony komunikacji."]],"challenge":"Oddanie idei równowagi i naturalności w nowoczesny, wiarygodny sposób.","solution":"Zastosowaliśmy organiczne formy, miękką typografię i stonowane kolory inspirowane naturą.","effect":"Spokojna, profesjonalna marka budująca poczucie bezpieczeństwa i harmonii.","live":"","mode":"logo"},{"brand":"Looksus","sub":"Monogramowe logo studia beauty","type":"Logo + identyfikacja","year":"2025","img":"https://zaprojektowani.com/wp-content/uploads/2026/05/looksus_logo_compressed.webp","tag":"Beauty studio","desc":"Dla studia beauty Looksus zaprojektowaliśmy elegancki monogram w ciepłej palecie nude. Delikatne linie i dopracowane proporcje nadają marce kobiecy, luksusowy charakter, a jednocześnie zachowują czytelność na wizytówkach, kartach klienta i oznakowaniu. System wizualny został przygotowany także do komunikacji social media.","meta":["Beauty","Monogram","Nude palette"],"client":"Looksus","services":"Logo, branding, mockupy, materiały firmowe","scope":["Monogram w delikatnych formach","Paleta nude i typografia","Wizytówki premium","Karty klienta","Elementy wystroju studia","Szablony social media"],"tools":["Illustrator","Photoshop","Figma","Mockupy","Druk"],"results":[["Autorski monogram","Rozpoznawalny znak zbudowany z delikatnych form."],["Nude palette","Ciepła kolorystyka podkreślająca segment premium."],["System studia","Wizytówki, karty klienta, wnętrze i social media."]],"challenge":"Stworzenie luksusowego monogramu, który nie będzie ciężki ani przesadnie ozdobny.","solution":"Oparliśmy znak na lekkich liniach, negatywnej przestrzeni i spokojnym zestawie kolorystycznym.","effect":"Spójny wizerunek beauty premium działający w przestrzeni studia i kanałach cyfrowych.","live":"","mode":"logo"},{"brand":"Show Party","sub":"Selfie mirror — wariant 1","type":"Logo + identyfikacja","year":"2025","img":"https://zaprojektowani.com/wp-content/uploads/2026/05/show_party_logo_compressed.webp","tag":"Event / Selfie mirror","desc":"Dla Show Party stworzyliśmy nowoczesny branding usługi selfie mirror i oprawy eventowej. Znak wykorzystuje motyw światła i odbicia, dzięki czemu łączy elegancję z imprezową energią. Identyfikację przygotowaliśmy do urządzeń, materiałów promocyjnych, social mediów i wydruków eventowych.","meta":["Event","Selfie mirror","Mockupy"],"client":"Show Party","services":"Logo, branding, mockupy, materiały firmowe","scope":["Logo z efektem światła","Branding pod eventy","Materiały promocyjne","Szablony social media","Mockupy selfie mirror","Materiały drukowane"],"tools":["Illustrator","Photoshop","After Effects","Mockupy"],"results":[["Efekt światła","Znak inspirowany lustrem, błyskiem i fotografią."],["Event ready","Materiały dopasowane do przestrzeni wydarzeń i urządzeń."],["Spójna promocja","Szablony social media i druków sprzedażowych."]],"challenge":"Połączenie premium eventu z dynamicznym, rozrywkowym charakterem usługi.","solution":"Zaprojektowaliśmy świetlny znak, kontrastową typografię i system dobrze działający na ciemnym tle.","effect":"Wyrazista marka eventowa przyciągająca uwagę na realizacjach, sprzęcie i materiałach reklamowych.","live":"","mode":"logo"},{"brand":"Impulse Tanning","sub":"Energetyczny branding studia opalania","type":"Logo + identyfikacja","year":"2025","img":"https://zaprojektowani.com/wp-content/uploads/2026/05/impulse_training_logo_compressed.webp","tag":"Studio opalania","desc":"Dla Impulse Tanning opracowaliśmy energetyczny branding studia opalania oparty na efekcie glow i intensywnym gradiencie. Znak ma nowoczesny, dynamiczny charakter i pozostaje rozpoznawalny na witrynie, kartach klienta oraz materiałach promocyjnych. Identyfikacja podkreśla efekt, energię i pewność siebie.","meta":["Glow","Gradient","Studio"],"client":"Impulse Tanning","services":"Logo, branding, mockupy, materiały firmowe","scope":["Logo z efektem glow","Gradient i typografia","Karty klienta","Oznakowanie witryny","Materiały promocyjne","Mockupy"],"tools":["Illustrator","Photoshop","Figma","Mockupy","Druk"],"results":[["Glow system","Kontrolowany efekt świetlny jako główny motyw marki."],["Widoczna witryna","Znak i kolory przygotowane do oznakowania lokalu."],["Materiały sprzedażowe","Karty klienta, promocje i wizualizacje usług."]],"challenge":"Stworzenie mocnej marki wizualnej bez wrażenia przypadkowego, neonowego efektu.","solution":"Uporządkowaliśmy gradient, typografię i znak w system możliwy do konsekwentnego stosowania.","effect":"Energetyczna identyfikacja wyróżniająca studio i dobrze pracująca w reklamie oraz przestrzeni lokalu.","live":"","mode":"logo"},{"brand":"Medical Friend","sub":"Przyjazny brand medyczny","type":"Logo + identyfikacja","year":"2025","img":"https://zaprojektowani.com/wp-content/uploads/2026/05/medical_friend_logo_compressed.webp","tag":"Medycyna","desc":"Dla Medical Friend stworzyliśmy przyjazną identyfikację medyczną łączącą symbol serca z subtelnym motywem uśmiechu. Projekt miał zmniejszać dystans i budować zaufanie, pozostając jednocześnie profesjonalnym i czytelnym. System obejmuje oznakowanie gabinetów, papeterię i materiały informacyjne dla pacjentów.","meta":["Medycyna","Zaufanie","System"],"client":"Medical Friend","services":"Logo, branding, mockupy, materiały firmowe","scope":["Logo z symbolem serca","Paleta medyczna","Oznakowanie gabinetów","Papeteria firmowa","Materiały informacyjne","Szablony do strony"],"tools":["Illustrator","Figma","InDesign","Mockupy","Druk"],"results":[["Symbol zaufania","Serce i uśmiech budujące pozytywne pierwsze wrażenie."],["Czytelny system","Kolory i typografia odpowiednie do komunikacji medycznej."],["Pełne oznakowanie","Gabinet, dokumenty i materiały informacyjne w jednym stylu."]],"challenge":"Połączenie medycznej wiarygodności z ciepłym, empatycznym charakterem komunikacji.","solution":"Zastosowaliśmy prosty symbol, jasną paletę i czytelną typografię dopasowaną do informacji zdrowotnych.","effect":"Przyjazna marka medyczna, która ułatwia kontakt i wzmacnia poczucie bezpieczeństwa pacjenta.","live":"","mode":"logo","project_slug":"medical-friend-logo-identyfikacja"},{"brand":"Janda","sub":"Subtelny luksus w stylistyce spa","type":"Logo + identyfikacja","year":"2025","img":"https://zaprojektowani.com/wp-content/uploads/2026/05/janda_logo_compressed.webp","tag":"Health & Beauty","desc":"Dla marki Janda zaprojektowaliśmy luksusową, ilustracyjną identyfikację inspirowaną spokojem spa i kobiecym pięknem. Delikatny profil otoczony kwiatami tworzy charakterystyczny znak, który dobrze prezentuje się na menu zabiegów, wizytówkach i materiałach cyfrowych. Paleta została utrzymana w subtelnym, eleganckim tonie.","meta":["Spa","Luxury","Branding"],"client":"Janda","services":"Logo, branding, mockupy, materiały firmowe","scope":["Logo z ilustracją kwiatową","Paleta luxury","Menu zabiegów","Wizytówki premium","Szablony social media","Mockupy i wizualizacje"],"tools":["Illustrator","Photoshop","Procreate","Mockupy","Druk"],"results":[["Ilustracyjny znak","Kobiecy profil i kwiaty tworzące unikalny motyw marki."],["Paleta luxury","Stonowane kolory wzmacniające wrażenie jakości."],["Materiały zabiegowe","Menu, wizytówki i komunikacja social media."]],"challenge":"Stworzenie bogatego, kobiecego znaku bez utraty elegancji i czytelności.","solution":"Uprościliśmy ilustrację do kontrolowanego detalu i zestawiliśmy ją z lekką typografią premium.","effect":"Rozpoznawalny branding spa budujący atmosferę relaksu jeszcze przed wizytą.","live":"","mode":"logo"},{"brand":"Ralf Furniture","sub":"Branding marki meblarskiej","type":"Logo + identyfikacja","year":"2025","img":"https://zaprojektowani.com/wp-content/uploads/2026/05/ralf_logo_compressed.webp","tag":"Meble na wymiar","desc":"Dla Ralf Furniture stworzyliśmy minimalistyczny branding marki mebli na wymiar. Geometryczny sygnet, paleta czerni i naturalnych tonów drewna podkreślają precyzję wykonania oraz jakość materiałów. System został przygotowany do katalogów, ofert B2B, oznakowania showroomu i materiałów firmowych.","meta":["Meble","B2B","Premium"],"client":"Ralf Furniture","services":"Logo, branding, mockupy, materiały firmowe","scope":["Sygnet minimalistyczny","Paleta czerni i drewna","Katalog produktowy","Papeteria firmowa","Oznakowanie showroomu","Materiały B2B"],"tools":["Illustrator","Figma","InDesign","Photoshop","Mockupy","Druk"],"results":[["Minimalistyczny sygnet","Znak dobrze działający na meblach, dokumentach i online."],["Naturalna paleta","Czerń i drewno podkreślające jakość materiałów."],["Sprzedaż B2B","Katalogi, oferty i materiały showroomu w jednym stylu."]],"challenge":"Zbudowanie premium wizerunku, który pasuje zarówno do nowoczesnych wnętrz, jak i technicznej komunikacji produkcyjnej.","solution":"Połączyliśmy prosty sygnet z naturalną paletą i uporządkowanym systemem prezentacji produktów.","effect":"Profesjonalna identyfikacja wspierająca sprzedaż, prezentacje projektów i kontakt z partnerami B2B.","live":"","mode":"logo"},{"brand":"EVA Aesthetics","sub":"Luksusowy branding kliniki","type":"Logo + identyfikacja","year":"2025","img":"https://zaprojektowani.com/wp-content/uploads/2026/05/eva_logo_compressed.webp","tag":"Medycyna estetyczna","desc":"Dla EVA Aesthetics przygotowaliśmy luksusową identyfikację kliniki medycyny estetycznej opartą na minimalistycznym logotypie i palecie nude & gold. Projekt łączy kliniczną wiarygodność z ciepłym, kobiecym charakterem marki. System obejmuje oznakowanie kliniki, karty zabiegowe, wizytówki i szczegółowe wytyczne stosowania.","meta":["Klinika","Nude &amp; gold","Luxury"],"client":"EVA Aesthetics","services":"Logo, branding, mockupy, materiały firmowe","scope":["Logotyp minimalistyczny","Paleta nude & gold","Oznakowanie kliniki","Karty zabiegowe","Wizytówki premium","Brandbook z wytycznymi"],"tools":["Illustrator","Figma","Photoshop","InDesign","Mockupy","Brandbook"],"results":[["Minimalistyczne logo","Elegancki znak odpowiedni dla komunikacji medycznej i beauty."],["Nude & gold","Paleta tworząca ciepły, luksusowy charakter."],["System kliniki","Oznakowanie, karty zabiegowe i materiały firmowe."]],"challenge":"Zachowanie równowagi pomiędzy medycznym profesjonalizmem a estetyką premium beauty.","solution":"Oparliśmy identyfikację na czystej typografii, subtelnym detalu i eleganckiej, kontrolowanej palecie.","effect":"Spójny wizerunek kliniki, który wzmacnia zaufanie i podnosi wartość postrzeganą usług.","live":"","mode":"logo"},{"isCta":true,"mode":"logo","brand":"Tu może być Twoje logo","sub":"otrzymaj darmową wycenę","type":"Darmowa wycena","year":"2026","tag":"Twój projekt","live":"/studio-wyceny/"}];

  function esc(str){
    return String(str || '').replace(/[&<>"']/g, function(m){
      return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'})[m];
    });
  }

  /* liveGone: the client site no longer opens, its buttons stay without the link (zp_suite_portfolio_live_gone()). */
  const hasLive = item => !!(item.live || item.liveGone);
  const liveAttrs = item => item.live ? `href="${esc(item.live)}" target="_blank" rel="noopener"` : 'tabindex="-1"';

  function currentItems(){ return activeMode === 'logo' ? logoItems : webItems; }
  function setText(nodeList, value){ nodeList.forEach(el => el.textContent = value); }
  function setBar(nodeList, value){ nodeList.forEach(el => el.style.width = value); }

  function cacheCardMetrics(){
    const cards = Array.from(rail.querySelectorAll('[data-zp-card]'));
    cardMetrics = cards.map((card) => ({
      el: card,
      center: card.offsetLeft + card.offsetWidth * .5
    }));
    lastActiveIndex = -1;
    lastProgressPct = -1;
    lastRenderedX = null;
    lastStateX = null;
    cards.forEach(card => card.style.setProperty('--shift', '0px'));
  }

  function updateCardStateFromX(x, force){
    const items = currentItems();
    if (!cardMetrics.length || !items.length) cacheCardMetrics();
    if (!cardMetrics.length || !items.length) return;

    const roundedForState = Math.round(x / 24) * 24;
    if (!force && lastStateX !== null && Math.abs(roundedForState - lastStateX) < 24) return;
    lastStateX = roundedForState;

    const center = Math.abs(x) + stage.clientWidth * .50;
    let bestIndex = 0;
    let bestDistance = Infinity;

    for (let i = 0; i < cardMetrics.length; i++) {
      const dist = Math.abs(cardMetrics[i].center - center);
      if (dist < bestDistance) { bestDistance = dist; bestIndex = i; }
    }

    if (force || bestIndex !== lastActiveIndex) {
      for (let i = 0; i < cardMetrics.length; i++) {
        const card = cardMetrics[i].el;
        card.classList.toggle('is-active', i === bestIndex);
        card.classList.toggle('is-past', i < bestIndex);
      }
      setText(currentEls, String(bestIndex + 1).padStart(2, '0'));
      lastActiveIndex = bestIndex;
    }

    const progressPct = Math.round(((bestIndex + 1) / items.length) * 1000) / 10;
    if (force || Math.abs(progressPct - lastProgressPct) >= .5) {
      setBar(barEls, `${progressPct}%`);
      lastProgressPct = progressPct;
    }
  }

  function writeRailTransform(x, force){
    const roundedX = Math.round(x * 10) / 10;
    if (!force && lastRenderedX !== null && Math.abs(roundedX - lastRenderedX) < .15) return;
    lastRenderedX = roundedX;
    rail.style.transform = `translate3d(${roundedX}px,0,0)`;
  }

  function setRailX(nextX, immediate){
    targetX = clamp(nextX, -maxTranslate, 0);
    const mobile = isMobileView();

    if (xAnimRaf) { cancelAnimationFrame(xAnimRaf); xAnimRaf = 0; }

    currentX = targetX;

    if (mobile) {
      rail.style.transform = 'none';
      lastRenderedX = null;
    } else {
      writeRailTransform(currentX, true);
    }

    updateCardStateFromX(currentX, true);
  }

  function getStableViewportHeight(){
    const raw = window.innerHeight || document.documentElement.clientHeight || 720;
    const mobile = isMobileView();
    if (!mobile) { lastStableVH = raw; return raw; }
    if (vhLock) return vhLock;
    if (!lastStableVH) lastStableVH = raw;
    if (Math.abs(raw - lastStableVH) > 160) lastStableVH = raw;
    return lastStableVH;
  }

  function setViewportVar(){
    const vh = getStableViewportHeight();
    root.style.setProperty('--zpShowcaseVH', (vh * 0.01) + 'px');
  }

  function updateCopy(){
    const c = copy[activeMode];
    if (kickerEl) kickerEl.textContent = c.kicker;
    if (titleEl) titleEl.innerHTML = c.title;
    if (leadEl) leadEl.textContent = c.lead;
    if (tipEl) tipEl.textContent = c.tip;
    if (bottomTipEl) bottomTipEl.textContent = c.bottom;

    modeBtns.forEach(btn => {
      const isActive = btn.getAttribute('data-zp-mode-btn') === activeMode;
      btn.classList.toggle('is-active', isActive);
      btn.setAttribute('aria-selected', isActive ? 'true' : 'false');
    });

    root.setAttribute('data-mode', activeMode);
  }



  function fullUploadUrl(src){
    src = String(src || '');
    if (!src || src.indexOf('/wp-content/uploads/') === -1) return src;
    return src.replace(/-\d+x\d+(\.(?:webp|avif|png|jpe?g|gif))(\?.*)?$/i, '$1$2');
  }

  function portfolioImgAttrs(item, index){
    const src = esc(fullUploadUrl(item && item.img ? item.img : ''));
    const eager = false; // 3.2.36: portfolio is below the fold, including its first card.
    const base = 'class="zpShowcaseWhite__img" decoding="async" draggable="false" sizes="(max-width: 720px) 92vw, (max-width: 1180px) 58vw, 620px"';

    if (isMobileView()) {
      const blank = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 900 1280"%3E%3Crect width="900" height="1280" fill="%2305070b"/%3E%3C/svg%3E';
      if (index <= 1) {
        return `src="${src}" loading="${eager ? 'eager' : 'lazy'}" fetchpriority="${eager ? 'high' : 'low'}" ${base}`;
      }
      return `src="${blank}" data-zp-lazy-src="${src}" loading="lazy" fetchpriority="low" ${base}`;
    }

    return `src="${src}" loading="${eager ? 'eager' : 'lazy'}" fetchpriority="${eager ? 'high' : 'low'}" ${base}`;
  }

  let mobileImgObserver = null;
  function setupMobileImageLazy(){
    if (!isMobileView()) return;
    const imgs = Array.from(rail.querySelectorAll('img[data-zp-lazy-src]'));
    if (!imgs.length) return;

    const loadImg = (img) => {
      const src = img.getAttribute('data-zp-lazy-src');
      if (!src) return;
      img.src = src;
      img.removeAttribute('data-zp-lazy-src');
      img.loading = 'lazy';
      img.decoding = 'async';
    };

    if ('IntersectionObserver' in window) {
      if (mobileImgObserver && typeof mobileImgObserver.disconnect === 'function') {
        try { mobileImgObserver.disconnect(); } catch(e) {}
      }
      mobileImgObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
          if (!entry.isIntersecting) return;
          loadImg(entry.target);
          try { mobileImgObserver.unobserve(entry.target); } catch(e) {}
        });
      }, { root: stage, rootMargin: '220px 420px', threshold: 0.01 });
      imgs.forEach(img => mobileImgObserver.observe(img));
    } else {
      imgs.slice(0, 3).forEach(loadImg);
    }
  }

  function render(){
    const items = currentItems();

    cardMetrics = [];
    lastActiveIndex = -1;
    lastProgressPct = -1;
    lastRenderedX = null;
    lastStateX = null;

    rail.innerHTML = items.map((item, index) => {
      if (item.isCta) {
        const title = activeMode === 'logo' ? 'Tu może być Twoje logo' : 'Tu może być Twoja strona';
        const text = activeMode === 'logo'
          ? 'Opisz nam markę — przygotujemy kierunek, logo, kolory, typografię, mockupy i materiały firmowe.'
          : 'Opisz nam projekt — dobierzemy zakres, styl, funkcje, SEO i plan wdrożenia.';
        const href = '/studio-wyceny/';

        return `
          <article class="zpShowcaseWhite__card zpShowcaseWhite__card--cta" data-zp-card data-index="${index}" aria-label="${esc(item.brand)}">
            <div class="zpShowcaseWhite__ctaFrame">
              <div class="zpShowcaseWhite__ctaContent">
                <span class="zpShowcaseWhite__ctaEyebrow">Twój projekt może być następny</span>
                <h3 class="zpShowcaseWhite__ctaTitle">${esc(title)}</h3>
                <p class="zpShowcaseWhite__ctaText">${esc(text)}</p>
                <div class="zpShowcaseWhite__ctaActions"><a class="zpShowcaseWhite__ctaBtn" href="${esc(href)}">Otrzymaj darmową wycenę ${arrowIcon()}</a></div>
              </div>
            </div>
          </article>`;
      }

      const meta = (item.meta || []).map(m => `<span>${esc(m)}</span>`).join('');
      const live = hasLive(item) ? `<a class="zpShowcaseWhite__btn zpShowcaseWhite__btn--live" ${liveAttrs(item)}>Live ${arrowIcon()}</a>` : '';

      return `
        <article class="zpShowcaseWhite__card" data-zp-card data-index="${index}" tabindex="0" aria-label="Otwórz szczegóły projektu ${esc(item.brand)}">
          <div class="zpShowcaseWhite__media"><img ${portfolioImgAttrs(item, index)} alt="${esc(item.brand)} — ${esc(item.sub)}" loading="lazy" decoding="async"></div>
          <div class="zpShowcaseWhite__veil"></div>
          <div class="zpShowcaseWhite__top"><span class="zpShowcaseWhite__pill" data-zp-project-type>${esc(item.type)}</span>${item.isNew ? '<span class="zpPortfolioNew">NEW</span>' : '<span class="zpShowcaseWhite__index">' + String(index + 1).padStart(2, '0') + '</span>'}</div>
          <div class="zpShowcaseWhite__content">
            <h3 class="zpShowcaseWhite__name">${esc(item.brand)}<span class="zpShowcaseWhite__sub">${esc(item.sub)}</span></h3>
            <div class="zpShowcaseWhite__meta">${meta}</div>
            <div class="zpShowcaseWhite__actions">
              <button type="button" class="zpShowcaseWhite__btn zpShowcaseWhite__btn--primary" data-zp-detail="${index}">Szczegóły ${arrowIcon()}</button>
              ${live}
            </div>
          </div>
        </article>`;
    }).join('');

    setText(totalEls, String(items.length).padStart(2, '0'));
    currentX = 0;
    targetX = 0;
    vhLock = 0;
    isInsideTrack = false;
    setRailX(0, true);

    if (isMobileView()) {
      stage.scrollLeft = 0;
      root.classList.add('is-visible');
      root.classList.remove('is-flowing');
      updateMobileRailState();
      setupMobileImageLazy();
    }

    rail.querySelectorAll('[data-zp-detail]').forEach(btn => {
      btn.addEventListener('click', function(e){
        e.preventDefault();
        e.stopPropagation();
        if (Date.now() < suppressClickUntil || railDragged) return;
        const index = Number(btn.getAttribute('data-zp-detail') || 0);
        const item = currentItems()[index];
        if (item && !item.isCta) openPopup(item);
      });
    });

    rail.querySelectorAll('a').forEach(link => {
      link.addEventListener('click', function(e){
        if (Date.now() < suppressClickUntil || railDragged) { e.preventDefault(); e.stopPropagation(); return; }
        e.stopPropagation();
      });
    });

    rail.querySelectorAll('[data-zp-card]').forEach(card => {
      card.addEventListener('click', function(e){
        if (Date.now() < suppressClickUntil || railDragged) { e.preventDefault(); e.stopPropagation(); return; }
        if (e.target.closest('a, button')) return;
        if (isMobileView()) return;
        const index = Number(card.getAttribute('data-index') || 0);
        const item = currentItems()[index];
        if (!item) return;
        if (item.isCta) { window.location.href = '/studio-wyceny/'; return; }
        openPopup(item);
      });

      card.addEventListener('keydown', function(e){
        if (e.key !== 'Enter' && e.key !== ' ') return;
        if (e.target.closest('a, button')) return;
        e.preventDefault();
        const index = Number(card.getAttribute('data-index') || 0);
        const item = currentItems()[index];
        if (!item) return;
        if (item.isCta) { window.location.href = '/studio-wyceny/'; return; }
        openPopup(item);
      });
    });
  }

  function renderPopup(item){
    const isLogo = activeMode === 'logo';
    const live = hasLive(item) ? `<a ${liveAttrs(item)}>Zobacz live ${arrowIcon()}</a>` : '';
    const scope = (item.scope || []).map(s => `<li><i data-lucide="check"></i><span>${esc(s)}</span></li>`).join('');
    const tools = (item.tools || []).map(t => `<span class="zpShowcasePop__tool"><i data-lucide="check-circle-2"></i>${esc(t)}</span>`).join('');
    const storyRows = [
      ['target','Cel projektu',item.challenge],
      ['wand-sparkles','Kierunek rozwiązania',item.solution],
      ['trending-up','Efekt dla marki',item.effect]
    ].filter(row => row[2]);
    const story = storyRows.length ? `<div class="zpShowcasePop__story">${storyRows.map((row,index) => `<article class="zpShowcasePop__storyCard${index === 1 ? ' zpShowcasePop__storyCard--navy' : ''}"><span class="zpShowcasePop__storyIcon"><i data-lucide="${row[0]}"></i></span><div><small>${esc(row[1])}</small><p>${esc(row[2])}</p></div></article>`).join('')}</div>` : '';
    const resultRows = Array.isArray(item.results) ? item.results.filter(row => Array.isArray(row) && (row[0] || row[1])) : [];
    const results = resultRows.length ? `<div class="zpShowcasePop__resultsTitle"><i data-lucide="badge-check"></i>Najważniejsze efekty</div><div class="zpShowcasePop__results">${resultRows.map(row => `<article class="zpShowcasePop__result"><strong>${esc(row[0])}</strong><span>${esc(row[1])}</span></article>`).join('')}</div>` : '';
    const caseLink = item.case_url ? `<a href="${esc(item.case_url)}">Pełny opis realizacji ${arrowIcon()}</a>` : '';
    const primaryHref = isLogo ? '/logo-branding-katowice/' : '/zamowienie-strony-sklepu-internetowego/';
    const primaryText = isLogo ? 'Zamów logo w tym stylu' : 'Zamów podobny projekt';
    const secondaryHref = isLogo ? '/kontakt/' : '/strony-internetowe-katowice/';
    const secondaryText = isLogo ? 'Porozmawiajmy o brandingu' : 'Zobacz ofertę stron';

    return `
      <div class="zpShowcasePop__layout ${isLogo ? 'zpShowcasePop__layout--logo' : 'zpShowcasePop__layout--web'}">
        <div class="zpShowcasePop__visual" style="--pop-img:url('${esc(fullUploadUrl(item.img))}')"><div class="zpShowcasePop__visualInner"><img class="zpShowcasePop__img" src="${esc(fullUploadUrl(item.img))}" alt="${esc(item.brand)} — ${esc(item.sub)}" loading="eager" decoding="async"></div></div>
        <div class="zpShowcasePop__info">
          <div class="zpShowcasePop__top"><span class="zpShowcasePop__pill"><i data-lucide="tag"></i>${esc(item.tag)}</span><span class="zpShowcasePop__pill"><i data-lucide="calendar-days"></i>${esc(item.year || '2026')}</span>${hasLive(item) ? '<span class="zpShowcasePop__pill"><i data-lucide="radio"></i>live</span>' : ''}</div>
          <h3 class="zpShowcasePop__title" id="zpShowcasePopTitle">${esc(item.brand)}</h3>
          <p class="zpShowcasePop__subtitle">${esc(item.sub)}</p>
          <p class="zpShowcasePop__text">${esc(item.desc)}</p>
          ${story}
          <div class="zpShowcasePop__grid">
            <div class="zpShowcasePop__gridItem"><div class="zpShowcasePop__label"><i data-lucide="building-2"></i>Klient</div><div class="zpShowcasePop__value">${esc(item.client || item.brand)}</div></div>
            <div class="zpShowcasePop__gridItem"><div class="zpShowcasePop__label"><i data-lucide="calendar"></i>Rok</div><div class="zpShowcasePop__value">${esc(item.year || '2026')}</div></div>
            <div class="zpShowcasePop__gridItem"><div class="zpShowcasePop__label"><i data-lucide="layers-3"></i>Usługi</div><div class="zpShowcasePop__value">${esc(item.services || item.type)}</div></div>
            <div class="zpShowcasePop__gridItem"><div class="zpShowcasePop__label"><i data-lucide="sparkles"></i>${isLogo ? 'Styl' : 'Typ'}</div><div class="zpShowcasePop__value">${isLogo ? esc((item.meta || []).join(', ')) : esc(item.type)}</div></div>
          </div>
          ${tools ? `<div class="zpShowcasePop__tools">${tools}</div>` : ''}
          <div><div class="zpShowcasePop__scopeTitle"><i data-lucide="list-checks"></i>Zakres prac</div><ul class="zpShowcasePop__scope">${scope}</ul></div>
          ${results}
          <div class="zpShowcasePop__cta"><a href="${esc(primaryHref)}">${esc(primaryText)} ${arrowIcon()}</a>${live || `<a href="${esc(secondaryHref)}">${esc(secondaryText)} ${arrowIcon()}</a>`}${caseLink}</div>
        </div>
      </div>`;
  }

  function openPopup(item){
    if (!pop || !popBody) return;
    savedScroll = window.scrollY || window.pageYOffset || 0;
    popBody.innerHTML = renderPopup(item);
    pop.hidden = false;
    pop.classList.add('is-open');
    pop.setAttribute('aria-hidden', 'false');
    document.documentElement.classList.add('zpShowcaseNoScroll');
    document.body.classList.add('zpShowcaseNoScroll');

    /* Hydrate only the freshly injected modal icons. The second pass covers
       slower cached/deferred Lucide loading without rescanning the whole page. */
    requestAnimationFrame(() => {
      zpPortfolioCreateIcons(popBody);
      window.setTimeout(() => zpPortfolioCreateIcons(popBody), 90);
    });
  }

  function closePopup(){
    if (!pop) return;
    pop.classList.remove('is-open');
    pop.setAttribute('aria-hidden', 'true');
    pop.hidden = true;
    document.documentElement.classList.remove('zpShowcaseNoScroll');
    document.body.classList.remove('zpShowcaseNoScroll');
    if (!isMobileView()) requestAnimationFrame(() => window.scrollTo(0, savedScroll));
  }

  popCloseEls.forEach(el => el.addEventListener('click', closePopup));
  document.addEventListener('keydown', e => { if (e.key === 'Escape' && pop && pop.classList.contains('is-open')) closePopup(); });

  function bindRailPointerGuard(){
    if (rail.dataset.zpPointerGuard === '1') return;
    rail.dataset.zpPointerGuard = '1';

    function armSuppress(ms){
      suppressClickUntil = Date.now() + (ms || 650);
    }

    rail.addEventListener('touchstart', (e) => {
      const t = e.touches && e.touches[0];
      if (!t) return;
      mobileTouchStartX = t.clientX || 0;
      mobileTouchStartY = t.clientY || 0;
      mobileTouchMoved = false;
      railDragged = false;
    }, { passive: true });

    rail.addEventListener('touchmove', (e) => {
      const t = e.touches && e.touches[0];
      if (!t) return;

      const dx = Math.abs((t.clientX || 0) - mobileTouchStartX);
      const dy = Math.abs((t.clientY || 0) - mobileTouchStartY);

      if (dx > 5 || dy > 7) {
        mobileTouchMoved = true;
        railDragged = true;
        armSuppress(820);
      }
    }, { passive: true });

    rail.addEventListener('touchend', () => {
      if (mobileTouchMoved) armSuppress(900);
      setTimeout(() => {
        mobileTouchMoved = false;
        railDragged = false;
      }, 140);
    }, { passive: true });

    rail.addEventListener('click', (e) => {
      if (!isMobileView()) return;
      if (Date.now() < suppressClickUntil || mobileTouchMoved || railDragged) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
      }
    }, true);

    rail.addEventListener('pointerdown', (e) => {
      railPointerDown = true;
      railDragged = false;
      railPointerStartX = e.clientX || 0;
      railPointerStartY = e.clientY || 0;
    }, { passive: true });

    rail.addEventListener('pointermove', (e) => {
      if (!railPointerDown) return;
      const dx = Math.abs((e.clientX || 0) - railPointerStartX);
      const dy = Math.abs((e.clientY || 0) - railPointerStartY);
      if (dx > 6 || dy > 10) {
        railDragged = true;
        armSuppress(820);
      }
    }, { passive: true });

    const end = () => {
      if (railDragged) armSuppress(900);
      railPointerDown = false;
      /* v2.2.14: mobile crash guard — żadnego snapowania ani liczenia pozycji po geście. */
      if (isMobileView() && railDragged) {
        window.clearTimeout(mobileSnapTimer);
      }
      window.setTimeout(() => { railDragged = false; }, 180);
    };

    rail.addEventListener('pointerup', end, { passive: true });
    rail.addEventListener('pointercancel', end, { passive: true });
    rail.addEventListener('mouseleave', end, { passive: true });
  }

  function getMobileCards(){
    return Array.from(rail.querySelectorAll('[data-zp-card]'));
  }

  function getMobileNearestIndex(){
    const cards = getMobileCards();
    if (!cards.length) return 0;

    const center = stage.scrollLeft + stage.clientWidth * .5;
    let bestIndex = 0;
    let bestDistance = Infinity;

    cards.forEach((card, i) => {
      const cardCenter = card.offsetLeft + card.offsetWidth * .5;
      const dist = Math.abs(cardCenter - center);
      if (dist < bestDistance) {
        bestDistance = dist;
        bestIndex = i;
      }
    });

    return bestIndex;
  }

  function scrollToMobileIndex(index, behavior = 'smooth'){
    if (!isMobileView()) return;

    const cards = getMobileCards();
    if (!cards.length) return;

    const nextIndex = clamp(index, 0, cards.length - 1);
    const card = cards[nextIndex];
    const left = Math.max(0, card.offsetLeft - ((stage.clientWidth - card.offsetWidth) / 2));

    mobileProgrammaticScroll = true;
    mobileActiveIndex = nextIndex;
    stage.scrollTo({ left: left, top: 0, behavior: behavior });
    updateMobileRailState(nextIndex);

    window.clearTimeout(mobileSnapTimer);
    mobileSnapTimer = window.setTimeout(() => {
      mobileProgrammaticScroll = false;
      updateMobileRailState();
    }, behavior === 'auto' ? 80 : 460);
  }

  function snapMobileToNearest(){
    /* v8.4 mobile no-jump: bez programatycznego dosnapowania po geście.
       To właśnie potrafiło odpalać pionowy skok viewportu w Safari/Chrome mobile. */
    if (!isMobileView()) return;
    updateMobileRailState();
  }

  function updateMobileRailState(forcedIndex){
    if (!isMobileView()) return;
    const items = currentItems();
    const cards = getMobileCards();
    if (!cards.length || !items.length) return;

    const bestIndex = typeof forcedIndex === 'number' ? clamp(forcedIndex, 0, cards.length - 1) : getMobileNearestIndex();
    mobileActiveIndex = bestIndex;

    /* v2.2.09: mobile ultra-light — nie przepinamy klas na wszystkich kartach podczas scrolla. */
    if (!isMobileView()) {
      cards.forEach((card, i) => {
        card.classList.toggle('is-active', i === bestIndex);
        card.classList.toggle('is-past', i < bestIndex);
        card.style.setProperty('--shift', '0px');
      });
    }

    setText(currentEls, String(bestIndex + 1).padStart(2, '0'));
    setBar(barEls, `${((bestIndex + 1) / items.length) * 100}%`);
  }

  /* =========================================================
     v8.10 / HOME DESKTOP PORTFOLIO REWRITE
     Logika przeniesiona z sekcji process ze strony /strony-internetowe-katowice/:
     - bez wheel hijacku,
     - bez snapowania desktopowego,
     - sticky + normalny pionowy scroll mapowany 1:1 na poziomy track,
     - scroll w dół = ruch w prawo, scroll w górę = powrót w lewo,
     - brak opóźnionych przeliczeń wyrzucających do środka galerii.
  ========================================================= */

  function measure(options = {}){
    if (prefersReduced) return;

    setViewportVar();

    const viewportW = window.innerWidth || document.documentElement.clientWidth || 1200;
    const viewportH = getStableViewportHeight();
    const mobile = viewportW <= 760;

    if (mobile) {
      maxTranslate = 0;
      scrollDistance = 0;
      startDelay = 0;
      spacer.style.minHeight = 'auto';
      root.classList.add('is-visible');
      root.classList.remove('is-flowing', 'is-ending', 'is-desktop-pin');
      rail.style.transform = 'none';
      lastRenderedX = null;
      updateMobileRailState();
      measured = true;
      ticking = false;
      return;
    }

    root.classList.add('is-desktop-pin');

    const stageW = Math.max(1, stage.clientWidth || viewportW);
    maxTranslate = Math.max(0, rail.scrollWidth - stageW);

    /* v1.9.03: wolniejszy, bardziej premium desktop horizontal flow — dłuższy dystans pinowania. */
    scrollDistance = clamp(maxTranslate * 1.62, 1850, 5200);
    startDelay = clamp(viewportH * 0.08, 56, 110);

    const minH = Math.ceil(viewportH + startDelay + scrollDistance);
    spacer.style.minHeight = minH + 'px';

    cacheCardMetrics();
    measured = true;
    requestUpdate();
  }

  function update(){
    ticking = false;
    if (prefersReduced) return;

    const viewportW = window.innerWidth || document.documentElement.clientWidth || 1200;
    const viewportH = getStableViewportHeight();
    const mobile = viewportW <= 760;

    if (mobile) {
      root.classList.add('is-visible');
      root.classList.remove('is-flowing', 'is-ending', 'is-desktop-pin');
      rail.style.transform = 'none';
      updateMobileRailState();
      return;
    }

    if (!measured) measure({ force: true });

    const rect = spacer.getBoundingClientRect();
    const scrollable = Math.max(1, rect.height - viewportH);
    const p = clamp((-rect.top - startDelay) / scrollDistance, 0, 1);
    const x = -maxTranslate * p;

    root.classList.add('is-visible');
    root.classList.toggle('is-flowing', p > .075 && p < .965);
    root.classList.toggle('is-ending', p >= .94);

    /* 3.1.1: the "scroll down" hint shows while the pinned rail waits at its start and goes
       for good once the visitor scrolls into the projects (with the header, at is-flowing). */
    if (scrollHint && !hintDone) {
      if (p > .075) {
        hintDone = true;
        scrollHint.classList.remove('is-on');
      } else {
        scrollHint.classList.toggle('is-on', rect.top < viewportH * .15 && rect.bottom > viewportH);
      }
    }
    /* 3.1.1: the sideways-scroll listener is attached only while the section is pinned, so wheel
       events elsewhere on the page, and on the way in and out, never wait for this script. */
    setSideWheel(viewportW > 1500 && rect.top <= 1 && rect.bottom >= viewportH - 1);

    writeRailTransform(x, false);
    currentX = x;
    targetX = x;
    updateCardStateFromX(x, false);

    if (rect.bottom < 0) {
      writeRailTransform(-maxTranslate, true);
      updateCardStateFromX(-maxTranslate, true);
    } else if (rect.top > viewportH) {
      writeRailTransform(0, true);
      updateCardStateFromX(0, true);
      root.classList.remove('is-flowing', 'is-ending');
    }
  }

  function requestUpdate(){
    if (ticking || prefersReduced) return;
    if (!portfolioInView && measured) return;
    ticking = true;
    window.requestAnimationFrame(update);
  }

  /* 3.1.1: on desktop the projects also follow a sideways scroll (a trackpad swipe, a tilting
     wheel, Shift + wheel). The rail keeps following the page's vertical scroll, so a sideways
     step becomes the same step of page scroll inside the pinned section and the counter,
     progress bar and active card stay in step. The page eases towards the target, because a
     mouse wheel moves in big steps; a vertical scroll meanwhile stops the easing. Each step is
     an instant scroll: html has scroll-behavior: smooth, which would turn every step into a
     smooth scroll of its own. */
  function sideScrollTo(y){
    try { window.scrollTo({ top: y, behavior: 'instant' }); } catch (err) { window.scrollTo(0, y); }
  }
  function sideFrame(now){
    sideRaf = 0;
    if (sideTargetY === null) return;
    const y = window.scrollY || window.pageYOffset || 0;
    if (Math.abs(y - sideLastY) > 2) { sideTargetY = null; return; }
    const left = sideTargetY - sideLastY;
    if (Math.abs(left) < 1) { sideScrollTo(sideTargetY); sideTargetY = null; return; }
    const dt = sideLastT ? Math.min(50, now - sideLastT) : 16.7;
    sideLastT = now;
    sideLastY += left * (1 - Math.pow(.7, dt / 16.7));
    sideScrollTo(sideLastY);
    sideRaf = window.requestAnimationFrame(sideFrame);
  }

  function setSideWheel(on){
    if (on === sideWheelOn) return;
    sideWheelOn = on;
    if (on) root.addEventListener('wheel', onSideWheel, { passive: false });
    else root.removeEventListener('wheel', onSideWheel);
  }
  function onSideWheel(e){
    if (e.ctrlKey || e.metaKey || !measured || scrollDistance <= 0) return;
    let dx = e.deltaX;
    let dy = e.deltaY;
    if (e.shiftKey && !dx) { dx = dy; dy = 0; }
    /* One axis per gesture: a sideways swipe keeps scrolling sideways when it drifts. */
    if (e.timeStamp > sideAxisUntil) sideAxis = Math.abs(dx) > Math.abs(dy) ? 'x' : 'y';
    sideAxisUntil = e.timeStamp + 160;
    if (sideAxis !== 'x') { sideTargetY = null; return; }
    if (!dx || document.documentElement.classList.contains('zpShowcaseNoScroll')) return;
    if ((window.innerWidth || document.documentElement.clientWidth || 0) <= 1500) return;
    const viewportH = getStableViewportHeight();
    const rect = spacer.getBoundingClientRect();
    if (rect.top > 1 || rect.bottom < viewportH - 1 || !e.cancelable) return;
    e.preventDefault();
    const y = window.scrollY || window.pageYOffset || 0;
    const top = y + rect.top;
    const step = dx * (e.deltaMode === 1 ? 40 : e.deltaMode === 2 ? viewportH : 1);
    if (sideTargetY === null) { sideLastY = y; sideLastT = 0; }
    sideTargetY = clamp((sideTargetY === null ? y : sideTargetY) + step, top, top + startDelay + scrollDistance);
    if (!sideRaf) sideRaf = window.requestAnimationFrame(sideFrame);
  }

  function scrollToDesktopProgress(progress, behavior = 'smooth'){
    if (isMobileView() || prefersReduced) return;
    const p = clamp(progress, 0, 1);
    const baseY = spacer.getBoundingClientRect().top + (window.scrollY || window.pageYOffset || 0);
    const y = baseY + startDelay + p * scrollDistance;
    window.scrollTo({ top: y, behavior: behavior });
  }

  function scrollToDesktopIndex(index, behavior = 'smooth'){
    if (isMobileView() || prefersReduced) return;
    const maxIndex = Math.max(0, currentItems().length - 1);
    const idx = clamp(index, 0, maxIndex);
    scrollToDesktopProgress(maxIndex ? idx / maxIndex : 0, behavior);
  }

  function moveToProgress(delta){
    if (prefersReduced) return;

    if (isMobileView()) {
      const base = typeof mobileActiveIndex === 'number' ? mobileActiveIndex : getMobileNearestIndex();
      scrollToMobileIndex(base + delta, 'smooth');
      return;
    }

    const baseIndex = lastActiveIndex > -1 ? lastActiveIndex : 0;
    scrollToDesktopIndex(baseIndex + delta, 'smooth');
  }

  function switchMode(mode){
    if (mode === activeMode || (mode !== 'web' && mode !== 'logo') || mobileSwitchLock) return;

    const mobile = isMobileView();
    mobileSwitchLock = true;
    activeMode = mode;

    if (!mobile) root.classList.add('is-switching');

    const doSwitch = () => {
      updateCopy();
      render();

      currentX = 0;
      targetX = 0;
      lastRenderedX = null;
      vhLock = 0;
      isInsideTrack = false;

      if (mobile) {
        spacer.style.minHeight = 'auto';
        rail.style.transform = 'none';
        root.classList.add('is-visible');
        root.classList.remove('is-flowing', 'is-ending', 'is-switching', 'is-desktop-pin');

        mobileActiveIndex = 0;
        stage.scrollLeft = 0;
        setText(currentEls, '01');
        setBar(barEls, `${(1 / currentItems().length) * 100}%`);
        updateMobileRailState(0);
        mobileSwitchLock = false;
        return;
      }

      measured = false;
      measure({ force: true });
      scrollToDesktopProgress(0, 'auto');
      setText(currentEls, '01');
      setBar(barEls, `${(1 / currentItems().length) * 100}%`);

      window.setTimeout(() => {
        root.classList.remove('is-switching');
        mobileSwitchLock = false;
        measure({ force: true });
      }, 80);
    };

    if (mobile) {
      doSwitch();
    } else {
      window.setTimeout(doSwitch, 120);
    }
  }


  /* ZP v1.8.99 — homepage smooth wheel disabled.
     Poprzedni globalny wheel smoother przechwytywał scroll strony i potrafił ją zablokować.
     Zostawiamy natywny scroll strony + smooth silnik wyłącznie w portfolio. */
  function initHomeSmoothWheel(){
    if (prefersReduced || window.__zpHomeSmoothWheelReady) return;
    if ((window.innerWidth || document.documentElement.clientWidth || 0) <= 760) return;
    window.__zpHomeSmoothWheelReady = true;

    let targetY = window.scrollY || window.pageYOffset || 0;
    let currentY = targetY;
    let raf = 0;
    let userSync = false;

    const maxScroll = () => Math.max(0, document.documentElement.scrollHeight - window.innerHeight);
    const stop = () => { if (raf) cancelAnimationFrame(raf); raf = 0; };
    const sync = () => {
      if (userSync) return;
      targetY = window.scrollY || window.pageYOffset || 0;
      currentY = targetY;
    };
    const canSmooth = (e) => {
      if (!e || e.defaultPrevented || e.ctrlKey || e.metaKey || e.shiftKey) return false;
      if (document.documentElement.classList.contains('zpShowcaseNoScroll') || document.body.classList.contains('zpShowcaseNoScroll')) return false;
      const t = e.target;
      if (t && t.closest && t.closest('input,textarea,select,[contenteditable="true"],.zpShowcasePop,.zpShowcaseWhite__stage')) return false;
      const scroller = t && t.closest ? t.closest('[data-native-scroll], .elementor-popup-modal, .dialog-widget-content') : null;
      if (scroller) return false;
      return true;
    };
    const animate = () => {
      const diff = targetY - currentY;
      if (Math.abs(diff) < 0.45) {
        currentY = targetY;
        userSync = true;
        window.scrollTo(0, currentY);
        userSync = false;
        raf = 0;
        return;
      }
      currentY += diff * 0.13;
      userSync = true;
      window.scrollTo(0, currentY);
      userSync = false;
      raf = requestAnimationFrame(animate);
    };

    window.addEventListener('wheel', (e) => {
      if (!canSmooth(e)) return;
      e.preventDefault();
      const multiplier = Math.abs(e.deltaY) < 80 ? 0.92 : 0.78;
      targetY = clamp(targetY + (e.deltaY * multiplier), 0, maxScroll());
      if (!raf) {
        currentY = window.scrollY || window.pageYOffset || 0;
        raf = requestAnimationFrame(animate);
      }
    }, { passive:false });

    window.addEventListener('keydown', (e) => {
      if (['ArrowDown','ArrowUp','PageDown','PageUp','Home','End',' '].includes(e.key)) {
        stop();
        setTimeout(sync, 0);
      }
    }, { passive:true });
    window.addEventListener('resize', () => { targetY = clamp(targetY, 0, maxScroll()); currentY = window.scrollY || window.pageYOffset || 0; }, { passive:true });
    window.addEventListener('scroll', sync, { passive:true });
  }

  modeBtns.forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation();
      switchMode(btn.getAttribute('data-zp-mode-btn'));
    });
  });

  if (prevBtn) prevBtn.addEventListener('click', () => moveToProgress(-1));
  if (nextBtn) nextBtn.addEventListener('click', () => moveToProgress(1));

  updateCopy();
  render();
  bindRailPointerGuard();
  setViewportVar();

  if (prefersReduced) {
    root.classList.add('is-visible');
    spacer.style.minHeight = 'auto';
    stage.style.overflowX = 'auto';
    rail.style.transform = 'none';
  } else if (isMobileView()) {
    root.classList.add('is-visible');
    root.classList.remove('is-flowing', 'is-ending', 'is-desktop-pin');
    spacer.style.minHeight = 'auto';
    rail.style.transform = 'none';
    stage.scrollLeft = 0;
    mobileActiveIndex = 0;
    updateMobileRailState(0);

    let mobileLastWidth = window.innerWidth || document.documentElement.clientWidth || 0;
    let mobileRailTicking = false;

    /* v2.2.14 MOBILE STABILITY: bez listenera scroll na railu.
       Na iOS/Chrome mobile ciągłe getBounding/offset + class toggles podczas pionowego wejścia w portfolio
       potrafiły powodować Aw Snap/przeładowanie. Zostaje natywne poziome przewijanie,
       a licznik aktualizują tylko strzałki/przełączenie trybu. */
    if (false) stage.addEventListener('scroll', () => {
      clearTimeout(mobileSnapTimer);
    }, { passive: true });

    window.addEventListener('resize', () => {
      const w = window.innerWidth || document.documentElement.clientWidth || 0;
      if (Math.abs(w - mobileLastWidth) < 24) return;
      mobileLastWidth = w;

      clearTimeout(resizeTimer);
      resizeTimer = setTimeout(() => {
        spacer.style.minHeight = 'auto';
        rail.style.transform = 'none';
        stage.scrollLeft = 0;
        updateMobileRailState(0);
      }, 220);
    }, { passive: true });

    window.addEventListener('orientationchange', () => {
      setTimeout(() => {
        mobileLastWidth = window.innerWidth || document.documentElement.clientWidth || 0;
        lastStableVH = 0;
        spacer.style.minHeight = 'auto';
        rail.style.transform = 'none';
        stage.scrollLeft = 0;
        updateMobileRailState(0);
      }, 320);
    }, { passive: true });
  } else {
    /* v2.2.347: scroll work only while portfolio is near the viewport. */
    if ('IntersectionObserver' in window) {
      const portfolioIo = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
          portfolioInView = !!entry.isIntersecting;
          if (portfolioInView) requestUpdate();
          if (!portfolioInView) setSideWheel(false);
        });
      }, { rootMargin: '48% 0px 48% 0px', threshold: 0 });
      portfolioIo.observe(spacer);
    }

    /* v1.8.99: NIE uruchamiamy globalnego smooth-wheel, bo blokował scroll strony. */
    window.addEventListener('scroll', requestUpdate, { passive: true });
    window.addEventListener('resize', () => {
      clearTimeout(resizeTimer);
      resizeTimer = setTimeout(() => {
        measured = false;
        measure({ force: true });
      }, 120);
    }, { passive: true });
    window.addEventListener('orientationchange', () => {
      setTimeout(() => {
        lastStableVH = 0;
        vhLock = 0;
        measured = false;
        measure({ force: true });
      }, 220);
    }, { passive: true });

    if (document.readyState === 'complete') {
      measure({ force: true });
    } else {
      window.addEventListener('load', () => measure({ force: true }), { once: true });
      measure({ force: true });
    }
  }
})();
