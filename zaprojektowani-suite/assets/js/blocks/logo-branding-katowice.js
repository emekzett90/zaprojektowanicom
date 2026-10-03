/* ZAPROJEKTOWANI — Logo & Branding Katowice v2 (1:1 z prototypu).
   #siteHeader / .menu-toggle nie istnieją (globalny header wtyczki) — kod jest na to odporny. */

/* Bazowy URL assetów strony. page.php ustawia window.ZP_LB_ASSETS na ZP_SUITE_URL.
   Fallbacki: URL własnego <script>, a na końcu konwencjonalna ścieżka wtyczki —
   dzięki temu działa też, gdy plugin cache'ujący pozmienia kolejność skryptów. */
var ZP_LB_ASSETS = (function () {
  try {
    if (window.ZP_LB_ASSETS) return window.ZP_LB_ASSETS;
    var s = document.querySelector('script[src*="logo-branding-katowice.js"]');
    if (s && s.src) return s.src.split('?')[0].replace(/assets\/js\/blocks\/[^/]*$/, 'assets/logo-branding/');
  } catch (e) {}
  return '/wp-content/plugins/zaprojektowani-suite/assets/logo-branding/';
})();

      (() => {
        const header = document.getElementById("siteHeader");
        const toggle = document.querySelector(".menu-toggle");
        const progress = document.querySelector(".scroll-progress");
        const navLinks = [...document.querySelectorAll(".nav a")];
        const revealItems = document.querySelectorAll(
          ".reveal,.process-track",
        );
        const sections = [...document.querySelectorAll("main section[id]")];
        const processSection = document.querySelector(".process");
        const processHead = processSection?.querySelector(".section-head");
        const processTrack = processSection?.querySelector(".process-track");
        const processSteps = processSection
          ? [...processSection.querySelectorAll(".process-step")]
          : [];
        const heroCallbackForm = document.getElementById("heroCallbackForm");
        const heroCallbackPhone = document.getElementById("heroCallbackPhone");
        const heroCallbackStatus = document.getElementById("heroCallbackStatus");
        if (heroCallbackForm && heroCallbackPhone && heroCallbackStatus) {
          heroCallbackPhone.addEventListener("input", () => {
            heroCallbackForm.classList.remove("is-error", "is-ready");
            heroCallbackStatus.textContent = "30 sekund — bez długiego briefu.";
          });
          heroCallbackForm.addEventListener("submit", (event) => {
            event.preventDefault();
            const phone = heroCallbackPhone.value.trim();
            const digits = phone.replace(/\D/g, "");
            if (digits.length < 9 || digits.length > 15) {
              heroCallbackForm.classList.remove("is-ready");
              heroCallbackForm.classList.add("is-error");
              heroCallbackStatus.textContent = "Wpisz poprawny numer telefonu.";
              heroCallbackPhone.focus();
              return;
            }

            const contactForm = document.getElementById("zpContactFormMain");
            const contactPhone = contactForm?.querySelector('[name="phone"]');
            const callbackMode = contactForm?.querySelector('[data-mode-value="call"]');
            const callbackTopic = contactForm?.querySelector('[name="callback_topic"]');
            if (callbackMode) {
              callbackMode.checked = true;
              callbackMode.dispatchEvent(new Event("change", { bubbles: true }));
            }
            if (contactPhone) {
              contactPhone.value = phone;
              contactPhone.dispatchEvent(new Event("input", { bubbles: true }));
              contactPhone.dispatchEvent(new Event("change", { bubbles: true }));
            }
            if (callbackTopic) {
              callbackTopic.value = "Logo / branding";
              callbackTopic.dispatchEvent(new Event("change", { bubbles: true }));
            }

            heroCallbackForm.classList.remove("is-error");
            heroCallbackForm.classList.add("is-ready");
            heroCallbackStatus.textContent = "Numer wpisany — potwierdź zgłoszenie poniżej.";
            window.setTimeout(() => {
              document.getElementById("kontakt")?.scrollIntoView({
                behavior: window.matchMedia("(prefers-reduced-motion: reduce)").matches
                  ? "auto"
                  : "smooth",
                block: "start",
              });
              window.setTimeout(() => contactPhone?.focus({ preventScroll: true }), 650);
            }, 420);
          });
        }
        const mapCard = document.querySelector(".poland-map-card");
        const mapStatus = mapCard?.querySelector(".map-status");
        const mapMarkers = mapCard
          ? [...mapCard.querySelectorAll(".map-marker")]
          : [];
        const activateMapMarker = (marker) => {
          mapMarkers.forEach((item) =>
            item.classList.toggle("is-active", item === marker),
          );
          if (!mapStatus) return;
          mapStatus.querySelector("strong").textContent = marker.dataset.city;
          mapStatus.querySelector("small").textContent = marker.dataset.mode;
          mapStatus.querySelector("p").textContent = marker.dataset.copy;
          if (
            !window.matchMedia("(prefers-reduced-motion: reduce)").matches &&
            typeof mapStatus.animate === "function"
          ) {
            mapStatus.animate(
              [
                { opacity: 0.45, transform: "translateY(8px) scale(.985)" },
                { opacity: 1, transform: "translateY(0) scale(1)" },
              ],
              {
                duration: 360,
                easing: "cubic-bezier(.22,.8,.32,1)",
              },
            );
          }
        };
        mapMarkers.forEach((marker) =>
          marker.addEventListener("click", () => activateMapMarker(marker)),
        );
        const polandOutline = document.getElementById("polandOutline");
        const polandSvg = document.querySelector(".poland-shape-svg");
        if (
          polandOutline &&
          polandSvg &&
          typeof polandOutline.getTotalLength === "function"
        ) {
          polandSvg.style.setProperty(
            "--poland-path-length",
            String(Math.ceil(polandOutline.getTotalLength())),
          );
        }

        /* v2.2.684: mapa pozostaje płaska. Usunięto przechylenie 3D na hover,
           które na desktopie powodowało cięcie cienia i migotanie warstw. */
        const updateScroll = () => {
          const y = window.scrollY;
          const max = Math.max(
            1,
            document.documentElement.scrollHeight - window.innerHeight,
          );
          if (header) header.classList.toggle("is-scrolled", y > 24);
          if (progress) progress.style.transform = "scaleX(" + Math.min(1, y / max) + ")";
          let current = "start";
          sections.forEach((section) => {
            if (y >= section.offsetTop - 180) current = section.id;
          });
          navLinks.forEach((link) =>
            link.classList.toggle(
              "is-active",
              link.getAttribute("href") === "#" + current,
            ),
          );
          if (processTrack) {
            const rect = processTrack.getBoundingClientRect();
            const start = window.innerHeight * 0.78;
            const end = window.innerHeight * 0.38 - rect.height;
            const processProgress = Math.max(
              0,
              Math.min(1, (start - rect.top) / Math.max(1, start - end)),
            );
            processTrack.style.setProperty(
              "--process-progress",
              processProgress.toFixed(4),
            );
            let currentProcessStep = -1;
            processSteps.forEach((step, index) => {
              const threshold = index / Math.max(1, processSteps.length);
              const reached = processProgress > 0.015 && processProgress + 0.012 >= threshold;
              step.classList.toggle("is-reached", reached);
              if (reached) currentProcessStep = index;
            });
            processSteps.forEach((step, index) => {
              step.classList.toggle("is-current", index === currentProcessStep);
            });
          }
          if (processSection && processHead && window.innerWidth > 680) {
            const rect = processHead.getBoundingClientRect();
            const travel = Math.max(
              0,
              Math.min(
                1,
                (window.innerHeight * 0.15 - rect.top) /
                  Math.max(1, rect.height * 0.88),
              ),
            );
            const fade = Math.max(0, Math.min(1, (travel - 0.7) / 0.3));
            processSection.style.setProperty(
              "--process-person-shift",
              `${Math.round(-travel * 115)}px`,
            );
            processSection.style.setProperty(
              "--process-person-opacity",
              (1 - fade).toFixed(3),
            );
          }
        };
        /* ZP Suite: header i menu pochodza z globalnego headera wtyczki,
           wiec #siteHeader/.menu-toggle nie istnieja na tej stronie. */
        const closeMenu = () => {
          if (toggle) toggle.setAttribute("aria-expanded", "false");
          if (header) header.classList.remove("is-open");
          document.body.classList.remove("menu-open");
        };
        if (toggle) {
          toggle.addEventListener("click", () => {
            const open = toggle.getAttribute("aria-expanded") !== "true";
            toggle.setAttribute("aria-expanded", String(open));
            if (header) header.classList.toggle("is-open", open);
            document.body.classList.toggle("menu-open", open);
          });
        }
        navLinks.forEach((link) => link.addEventListener("click", closeMenu));
        const observer = new IntersectionObserver(
          (entries) => {
            entries.forEach((entry) => {
              if (entry.isIntersecting) {
                entry.target.classList.add("is-visible");
                observer.unobserve(entry.target);
              }
            });
          },
          { threshold: 0.12, rootMargin: "0px 0px -7% 0px" },
        );
        revealItems.forEach((item) => observer.observe(item));
        const processObserver = new IntersectionObserver(
          (entries) => {
            entries.forEach((entry) => {
              if (!entry.isIntersecting) return;
              entry.target.classList.add("is-visible");
              processObserver.unobserve(entry.target);
            });
          },
          window.innerWidth <= 680
            ? { threshold: 0.38, rootMargin: "0px 0px -18% 0px" }
            : { threshold: 0.12, rootMargin: "0px 0px -7% 0px" },
        );
        processSteps.forEach((step) => processObserver.observe(step));
        document.querySelectorAll(".faq-item").forEach((item) =>
          item.addEventListener("toggle", () => {
            if (!item.open) return;
            document.querySelectorAll(".faq-item[open]").forEach((other) => {
              if (other !== item) other.removeAttribute("open");
            });
          }),
        );
        const projectDetails = {
          Siemianowski: {
            year: "2026",
            type: "Logo + branding premium",
            category: "Kancelaria / Restrukturyzacja",
            subtitle: "Logo i branding kancelarii premium",
            image: ZP_LB_ASSETS + "portfolio-siemianowski.webp",
            lead: "Prestiż, zaufanie i czytelność przełożone na znak, dokumenty, materiały firmowe i cyfrowe punkty styku.",
            tags: ["Premium", "Kancelaria", "Elegancja"],
            challenge: "Stworzenie rozpoznawalnego znaku dla branży prawnej bez powielania motywów wagi, kolumn i paragrafów.",
            solution: "Postawiliśmy na autorski układ typograficzny, dopracowane proporcje oraz system zastosowań formalnych i cyfrowych.",
            effect: "Ponadczasowa identyfikacja wzmacniająca ekspercki i prestiżowy charakter kancelarii.",
            scope: ["Projekt logo z wariantami", "Kolorystyka i typografia", "Mockupy i wizualizacje", "Materiały do druku", "Identyfikacja kancelarii", "Wytyczne do strony WWW"],
            results: [
              ["Autorski znak", "Logo wyróżniające kancelarię bez branżowych klisz."],
              ["Pełny system", "Kolory, typografia i warianty do zastosowań online i offline."],
              ["Wizerunek premium", "Spójne materiały wzmacniające wiarygodność marki."],
            ],
            gallery: [
              [ZP_LB_ASSETS + "siemianowski-stationery-overview.jpg", "System identyfikacji Kancelarii Siemianowski na papeterii i materiałach cyfrowych", "landscape"],
              [ZP_LB_ASSETS + "siemianowski-client.jpg", "Arkadiusz Siemianowski przy biurku", "portrait"],
              [ZP_LB_ASSETS + "siemianowski-letterhead.jpg", "Projekt papieru firmowego Kancelarii Siemianowski", "landscape"],
              [ZP_LB_ASSETS + "siemianowski-business-cards-alt.jpg", "Awers i rewers wizytówki Kancelarii Siemianowski", "landscape"],
            ],
            variants: [
              [ZP_LB_ASSETS + "siemianowski-logo-white-gold.jpg", "Logo Siemianowski w wersji podstawowej"],
              [ZP_LB_ASSETS + "siemianowski-logo-navy.jpg", "Logo Siemianowski na granatowym tle"],
              [ZP_LB_ASSETS + "siemianowski-logo-black.jpg", "Czarna wersja logo Siemianowski"],
              [ZP_LB_ASSETS + "siemianowski-logo-white-on-black.jpg", "Biała wersja logo Siemianowski"],
            ],
          },
          "Pracownia Urody": {
            year: "2026", type: "Logo + identyfikacja", category: "Beauty / Kosmetologia", subtitle: "Delikatny branding beauty dla Joanny Samborskiej", image: ZP_LB_ASSETS + "portfolio-pracownia-urody.webp",
            lead: "Delikatna identyfikacja, która łączy profesjonalny charakter gabinetu z lekkością i spokojem.", tags: ["Beauty", "Subtelnie", "Kobieco"],
            challenge: "Połączenie delikatnej estetyki beauty z wiarygodnym, profesjonalnym charakterem usług.", solution: "Stworzyliśmy lekki znak, spokojną paletę i zestaw materiałów łatwych do konsekwentnego stosowania.", effect: "Spójny i rozpoznawalny wizerunek, który wzmacnia atmosferę gabinetu jeszcze przed wizytą.",
            scope: ["Logo marki", "Subtelny sygnet z profilem", "Wersje kolorystyczne", "Mockupy prezentacyjne", "Wizualizacje na opakowaniach", "Materiały firmowe i social media"],
            results: [["Subtelny sygnet", "Charakterystyczny profil twarzy bez nadmiernej dosłowności."], ["Paleta beauty", "Kolory wspierające poczucie spokoju i jakości."], ["Gotowa komunikacja", "Materiały do druku, opakowań i social mediów."]],
          },
          "Eat Tasty": {
            year: "2025", type: "Logo + identyfikacja", category: "Gastro / Delivery", subtitle: "Apetyczny branding dowozu jedzenia", image: ZP_LB_ASSETS + "portfolio-eat-tasty.webp",
            lead: "Dynamiczny branding marki delivery, przygotowany do opakowań, etykiet, digitalu i materiałów promocyjnych.", tags: ["Gastro", "Opakowania", "Social media"],
            challenge: "Połączenie skojarzenia z jedzeniem i szybką dostawą w jednym prostym symbolu.", solution: "Opracowaliśmy dynamiczny znak, wyrazistą paletę i zestaw zastosowań na opakowaniach oraz w komunikacji online.", effect: "Rozpoznawalna identyfikacja gotowa do skalowania wraz z ofertą i obszarem dostaw.",
            scope: ["Logo z symbolem delivery", "Projekty opakowań i boksów", "Naklejki i etykiety", "Materiały social media", "System kolorystyczny", "Mockupy na opakowaniach"],
            results: [["Symbol delivery", "Talerz i ruch dostawy zamknięte w jednym znaku."], ["System opakowań", "Spójne boksy, naklejki i etykiety."], ["Widoczność marki", "Kolory i układ przygotowane pod szybkie rozpoznanie."]],
          },
          "Medical Friend": {
            year: "2025", type: "Logo + identyfikacja", category: "Medycyna", subtitle: "Przyjazny brand medyczny", image: ZP_LB_ASSETS + "portfolio-medical-friend.webp",
            lead: "Przyjazna identyfikacja medyczna, która buduje zaufanie bez chłodnego, korporacyjnego dystansu.", tags: ["Medycyna", "Zaufanie", "System"],
            challenge: "Połączenie medycznej wiarygodności z ciepłym, empatycznym charakterem komunikacji.", solution: "Zastosowaliśmy prosty symbol, jasną paletę i czytelną typografię dopasowaną do informacji zdrowotnych.", effect: "Przyjazna marka medyczna, która ułatwia kontakt i wzmacnia poczucie bezpieczeństwa pacjenta.",
            scope: ["Logo z symbolem serca", "Paleta medyczna", "Oznakowanie gabinetów", "Papeteria firmowa", "Materiały informacyjne", "Szablony do strony"],
            results: [["Symbol zaufania", "Serce i uśmiech budujące pozytywne pierwsze wrażenie."], ["Czytelny system", "Kolory i typografia odpowiednie do komunikacji medycznej."], ["Pełne oznakowanie", "Gabinet, dokumenty i materiały informacyjne w jednym stylu."]],
          },
          "Vista Architekci": {
            year: "2025", type: "Logo + identyfikacja", category: "Architektura", subtitle: "Minimalistyczny monogram dla biura projektowego", image: ZP_LB_ASSETS + "portfolio-vista.webp",
            lead: "Geometryczny monogram i oszczędny system graficzny oparte na precyzji, rytmie i eleganckim minimalizmie.", tags: ["Minimalizm", "Architektura", "Brandbook"],
            challenge: "Oddanie charakteru pracowni architektonicznej w prostym znaku bez dosłownych ikon budynku.", solution: "Oparliśmy monogram na proporcjach i negatywnej przestrzeni, tworząc system o technicznym, ale eleganckim charakterze.", effect: "Ponadczasowa identyfikacja wspierająca profesjonalne prezentacje, oferty i dokumentację projektową.",
            scope: ["Sygnet monogramowy VA", "Brandbook z wytycznymi", "Papeteria firmowa", "Szablony ofertowe", "Wizytówki i teczki", "Materiały do prezentacji"],
            results: [["Monogram VA", "Geometryczny znak inspirowany językiem architektury."], ["Brandbook", "Reguły stosowania logo, kolorów i typografii."], ["Materiały ofertowe", "Spójna papeteria i szablony prezentacji projektów."]],
          },
          "Wonder Handmade": {
            year: "2025", type: "Logo + identyfikacja", category: "Handmade / Rękodzieło", subtitle: "Eleganckie logo dla marki handmade", image: ZP_LB_ASSETS + "portfolio-wonder.webp",
            lead: "Minimalistyczna identyfikacja z biżuteryjnym charakterem, przygotowana do etykiet, opakowań i sprzedaży online.", tags: ["Handmade", "Etykiety", "Brandbook"],
            challenge: "Zaprojektowanie delikatnego znaku, który pozostaje czytelny na bardzo małych elementach produktowych.", solution: "Opracowaliśmy prosty sygnet, oszczędną typografię i skalowalne warianty do druku oraz znakowania.", effect: "Elegancka marka handmade, która zwiększa wartość postrzeganą produktu i porządkuje komunikację.",
            scope: ["Sygnet biżuteryjny", "Brandbook z wytycznymi", "Etykiety na produkty", "Projekty opakowań", "Wizytówki i papeteria", "Szablony social media"],
            results: [["Biżuteryjny sygnet", "Delikatny znak działający na małych etykietach."], ["Brandbook", "Spójne zasady dla kolorów, typografii i logo."], ["Materiały produktowe", "Opakowania, zawieszki i komunikacja social media."]],
          },
        
          /* --- projekty dociągnięte 1:1 z bazy wtyczki (assets/js/blocks/logo-branding-katowice.js v2.2.684) --- */
          "Polerstone": {
            year: "2026", type: "Logo + identyfikacja wizualna", category: "Kamieniarstwo premium", subtitle: "Nowoczesne logo dla usług kamieniarskich", image: "https://zaprojektowani.com/wp-content/uploads/2026/05/polerstone_logo_compressed.webp",
            lead: "Nowoczesny projekt logo dla marki usługowej z segmentu premium. Minimalistyczny znak podkreśla solidność, dokładność i wysoką jakość realizacji.", tags: ["Premium", "Monogram", "Usługi"],
            detail: "Projekt został przygotowany tak, żeby znak działał zarówno w małym rozmiarze, jak i w bardziej reprezentacyjnych zastosowaniach. Zadbaliśmy o czytelność, proporcje, warianty użytkowe oraz estetykę dopasowaną do branży. Całość daje marce bardziej uporządkowany i pewny wizualnie charakter.",
            scope: ["Projekt znaku i logotypu", "Warianty kolorystyczne", "Mockupy prezentacyjne", "Materiały firmowe", "System użycia logo w komunikacji", "Przygotowanie kierunku wizualnego pod stronę www i social media", "Uporządkowanie zastosowań znaku w praktycznych materiałach", "Dopasowanie stylu do odbiorców, branży i pozycjonowania marki"],
          },
          "Arcycięcie": {
            year: "2025", type: "Logo + identyfikacja wizualna", category: "Fryzjerstwo / Barber", subtitle: "Charakterystyczny branding studia fryzur", image: "https://zaprojektowani.com/wp-content/uploads/2026/05/arcy_ciecie_logo_compressed.webp",
            lead: "Wyraziste logo dla studia fryzur, które buduje rozpoznawalność salonu i dobrze działa zarówno na szyldzie, jak i w materiałach promocyjnych.", tags: ["Fryzjerstwo", "Salon", "Branding"],
            detail: "W tym projekcie najważniejsze było zbudowanie wizerunku, który wygląda profesjonalnie już od pierwszego kontaktu z marką. Opracowaliśmy kierunek, który można wygodnie przenieść na stronę internetową, ofertę, social media, dokumenty, wizytówki oraz materiały sprzedażowe. Dzięki temu logo nie jest tylko pojedynczym znakiem, ale początkiem spójnego systemu komunikacji.",
            scope: ["Logo z charakterystycznym detalem", "Identyfikacja salonu", "Materiały do druku", "Grafiki do social media", "Wizualizacje zastosowania logo", "Przygotowanie kierunku wizualnego pod stronę www i social media", "Uporządkowanie zastosowań znaku w praktycznych materiałach", "Dopasowanie stylu do odbiorców, branży i pozycjonowania marki"],
          },
          "Akoya": {
            year: "2025", type: "Logo + mini branding", category: "Beauty & Wellness", subtitle: "Gabinet kosmetyczny premium", image: "https://zaprojektowani.com/wp-content/uploads/2026/05/akoya_logo_compressed.webp",
            lead: "Identyfikacja wizualna dla gabinetu beauty oparta na elegancji, spokoju i ciepłej palecie kolorów. Branding wspiera premium odbiór marki już od pierwszego kontaktu.", tags: ["Beauty", "Premium", "Wellness"],
            detail: "Przy tej realizacji skupiliśmy się na połączeniu estetyki premium z praktycznym użyciem w codziennej komunikacji. Logo, kolorystyka i materiały mają wspierać zaufanie, zapamiętywalność i spójność marki — nie tylko na mockupach, ale przede wszystkim w realnym kontakcie z klientem.",
            scope: ["Logo i warianty znaku", "Paleta kolorów", "Typografia marki", "Wizytówki i karty zabiegowe", "Szablony do social media", "Przygotowanie kierunku wizualnego pod stronę www i social media", "Uporządkowanie zastosowań znaku w praktycznych materiałach", "Dopasowanie stylu do odbiorców, branży i pozycjonowania marki"],
          },
          "Fryz Dobry Barber": {
            year: "2025", type: "Logo + identyfikacja wizualna", category: "Barber / Men’s style", subtitle: "Męski branding barbershopu", image: "https://zaprojektowani.com/wp-content/uploads/2026/05/fryz_dobry_logo_compressed.webp",
            lead: "Męski, mocny branding dla barbershopu. Logo w stylistyce vintage nadaje marce charakter i pomaga budować rozpoznawalność lokalnego salonu.", tags: ["Barber", "Vintage", "Salon"],
            detail: "Projekt został przygotowany tak, żeby znak działał zarówno w małym rozmiarze, jak i w bardziej reprezentacyjnych zastosowaniach. Zadbaliśmy o czytelność, proporcje, warianty użytkowe oraz estetykę dopasowaną do branży. Całość daje marce bardziej uporządkowany i pewny wizualnie charakter.",
            scope: ["Logo z sygnetem", "Typografia w stylu vintage", "Materiały salonu", "Szyld i oznakowanie", "Wizytówki i karty klienta", "Przygotowanie kierunku wizualnego pod stronę www i social media", "Uporządkowanie zastosowań znaku w praktycznych materiałach", "Dopasowanie stylu do odbiorców, branży i pozycjonowania marki"],
          },
          "Piotr Mazur": {
            year: "2025", type: "Logo + identyfikacja wizualna", category: "Kancelaria adwokacka", subtitle: "Monogram PM dla kancelarii adwokackiej", image: "https://zaprojektowani.com/wp-content/uploads/2026/05/piotr_mazur_logo_compressed.webp",
            lead: "Elegancki monogram dla kancelarii, zaprojektowany tak, by wzmacniać profesjonalny wizerunek i działać w komunikacji drukowanej oraz cyfrowej.", tags: ["Kancelaria", "Monogram", "Premium"],
            detail: "Przy tej realizacji skupiliśmy się na połączeniu estetyki premium z praktycznym użyciem w codziennej komunikacji. Logo, kolorystyka i materiały mają wspierać zaufanie, zapamiętywalność i spójność marki — nie tylko na mockupach, ale przede wszystkim w realnym kontakcie z klientem.",
            scope: ["Monogram PM", "Logo w kilku wariantach", "Papeteria firmowa", "Wizytówki premium", "Kierunek identyfikacji wizualnej", "Przygotowanie kierunku wizualnego pod stronę www i social media", "Uporządkowanie zastosowań znaku w praktycznych materiałach", "Dopasowanie stylu do odbiorców, branży i pozycjonowania marki"],
          },
          "Okruszek": {
            year: "2025", type: "Logo + identyfikacja wizualna", category: "Cukiernia / Słodkości", subtitle: "Pastelowe logo pracowni cukierniczej", image: "https://zaprojektowani.com/wp-content/uploads/2026/05/okruszek_logo_compressed.webp",
            lead: "Ciepły, pastelowy branding dla pracowni cukierniczej. Logo jest lekkie, przyjazne i dobrze sprawdza się na pudełkach, etykietach oraz w social media.", tags: ["Cukiernia", "Pastel", "Opakowania"],
            detail: "W tym projekcie najważniejsze było zbudowanie wizerunku, który wygląda profesjonalnie już od pierwszego kontaktu z marką. Opracowaliśmy kierunek, który można wygodnie przenieść na stronę internetową, ofertę, social media, dokumenty, wizytówki oraz materiały sprzedażowe. Dzięki temu logo nie jest tylko pojedynczym znakiem, ale początkiem spójnego systemu komunikacji.",
            scope: ["Logo z motywem tortu", "Paleta pastelowa", "Etykiety produktowe", "Projekty pudełek", "Materiały do social media", "Przygotowanie kierunku wizualnego pod stronę www i social media", "Uporządkowanie zastosowań znaku w praktycznych materiałach", "Dopasowanie stylu do odbiorców, branży i pozycjonowania marki"],
          },
          "Mona Cake": {
            year: "2025", type: "Logo + identyfikacja wizualna", category: "Torty artystyczne", subtitle: "Branding pracowni tortów artystycznych", image: "https://zaprojektowani.com/wp-content/uploads/2026/05/mona_logo_compressed.webp",
            lead: "Słodka, zapamiętywalna identyfikacja wizualna dla pracowni tortów. Projekt łączy lekkość, rzemieślniczy charakter i gotowość do użycia na opakowaniach.", tags: ["Torty", "Handmade", "Social media"],
            detail: "Projekt został przygotowany tak, żeby znak działał zarówno w małym rozmiarze, jak i w bardziej reprezentacyjnych zastosowaniach. Zadbaliśmy o czytelność, proporcje, warianty użytkowe oraz estetykę dopasowaną do branży. Całość daje marce bardziej uporządkowany i pewny wizualnie charakter.",
            scope: ["Logo i sygnet", "Opakowania na torty", "Etykiety i naklejki", "Kolory marki", "Szablony komunikacji", "Przygotowanie kierunku wizualnego pod stronę www i social media", "Uporządkowanie zastosowań znaku w praktycznych materiałach", "Dopasowanie stylu do odbiorców, branży i pozycjonowania marki"],
          },
          "Sfera": {
            year: "2025", type: "Logo + identyfikacja wizualna", category: "Well-being / Holistycznie", subtitle: "Naturalny branding studia well-being", image: "https://zaprojektowani.com/wp-content/uploads/2026/05/sfera_logo_compressed.webp",
            lead: "Naturalna identyfikacja wizualna dla studia well-being. Organiczna forma logo i spokojna paleta kolorów budują zaufanie oraz poczucie harmonii.", tags: ["Well-being", "Naturalnie", "Minimalizm"],
            detail: "W tym projekcie najważniejsze było zbudowanie wizerunku, który wygląda profesjonalnie już od pierwszego kontaktu z marką. Opracowaliśmy kierunek, który można wygodnie przenieść na stronę internetową, ofertę, social media, dokumenty, wizytówki oraz materiały sprzedażowe. Dzięki temu logo nie jest tylko pojedynczym znakiem, ale początkiem spójnego systemu komunikacji.",
            scope: ["Logo z organicznym symbolem", "Naturalna paleta kolorów", "Materiały gabinetowe", "Karty klienta", "Szablony komunikacji", "Przygotowanie kierunku wizualnego pod stronę www i social media", "Uporządkowanie zastosowań znaku w praktycznych materiałach", "Dopasowanie stylu do odbiorców, branży i pozycjonowania marki"],
          },
          "Looksus": {
            year: "2025", type: "Logo + identyfikacja wizualna", category: "Beauty studio", subtitle: "Monogramowe logo studia beauty", image: "https://zaprojektowani.com/wp-content/uploads/2026/05/looksus_logo_compressed.webp",
            lead: "Monogramowy branding dla studia beauty. Projekt został oparty na delikatnych formach, ciepłej kolorystyce i eleganckim stylu dopasowanym do usług premium.", tags: ["Beauty", "Monogram", "Premium"],
            detail: "Projekt został przygotowany tak, żeby znak działał zarówno w małym rozmiarze, jak i w bardziej reprezentacyjnych zastosowaniach. Zadbaliśmy o czytelność, proporcje, warianty użytkowe oraz estetykę dopasowaną do branży. Całość daje marce bardziej uporządkowany i pewny wizualnie charakter.",
            scope: ["Monogram marki", "Paleta nude", "Typografia premium", "Wizytówki i karty klienta", "Grafiki do social media", "Przygotowanie kierunku wizualnego pod stronę www i social media", "Uporządkowanie zastosowań znaku w praktycznych materiałach", "Dopasowanie stylu do odbiorców, branży i pozycjonowania marki"],
          },
          "Show Party": {
            year: "2025", type: "Logo + identyfikacja wizualna", category: "Event / Selfie mirror", subtitle: "Logo dla marki eventowej", image: "https://zaprojektowani.com/wp-content/uploads/2026/05/show_party_logo_compressed.webp",
            lead: "Nowoczesne logo dla firmy eventowej. Branding łączy elegancki charakter imprez z energią, światłem i dobrym pierwszym wrażeniem.", tags: ["Eventy", "Imprezy", "Social media"],
            detail: "Przy tej realizacji skupiliśmy się na połączeniu estetyki premium z praktycznym użyciem w codziennej komunikacji. Logo, kolorystyka i materiały mają wspierać zaufanie, zapamiętywalność i spójność marki — nie tylko na mockupach, ale przede wszystkim w realnym kontakcie z klientem.",
            scope: ["Logo eventowe", "Materiały promocyjne", "Szablony social media", "Mockupy na sprzęcie", "Spójny styl komunikacji", "Przygotowanie kierunku wizualnego pod stronę www i social media", "Uporządkowanie zastosowań znaku w praktycznych materiałach", "Dopasowanie stylu do odbiorców, branży i pozycjonowania marki"],
          },
          "Impulse Tanning": {
            year: "2025", type: "Logo + identyfikacja wizualna", category: "Studio opalania", subtitle: "Energetyczny branding studia opalania", image: "https://zaprojektowani.com/wp-content/uploads/2026/05/impulse_training_logo_compressed.webp",
            lead: "Energetyczny branding dla studia opalania. Gradientowy charakter logo wspiera wyrazisty, nowoczesny i łatwo rozpoznawalny styl marki.", tags: ["Glow", "Beauty", "Studio"],
            detail: "W tym projekcie najważniejsze było zbudowanie wizerunku, który wygląda profesjonalnie już od pierwszego kontaktu z marką. Opracowaliśmy kierunek, który można wygodnie przenieść na stronę internetową, ofertę, social media, dokumenty, wizytówki oraz materiały sprzedażowe. Dzięki temu logo nie jest tylko pojedynczym znakiem, ale początkiem spójnego systemu komunikacji.",
            scope: ["Logo z efektem glow", "Gradient marki", "Oznaczenia studia", "Karty klienta", "Materiały promocyjne", "Przygotowanie kierunku wizualnego pod stronę www i social media", "Uporządkowanie zastosowań znaku w praktycznych materiałach", "Dopasowanie stylu do odbiorców, branży i pozycjonowania marki"],
          },
          "Janda": {
            year: "2025", type: "Logo + identyfikacja wizualna", category: "Health & Beauty", subtitle: "Subtelny luksus w stylistyce spa", image: "https://zaprojektowani.com/wp-content/uploads/2026/05/janda_logo_compressed.webp",
            lead: "Luksusowy branding w stylistyce spa. Delikatna ilustracja i miękka typografia pomagają budować spokojny, kobiecy i premium odbiór marki.", tags: ["Spa", "Luxury", "Beauty"],
            detail: "Przy tej realizacji skupiliśmy się na połączeniu estetyki premium z praktycznym użyciem w codziennej komunikacji. Logo, kolorystyka i materiały mają wspierać zaufanie, zapamiętywalność i spójność marki — nie tylko na mockupach, ale przede wszystkim w realnym kontakcie z klientem.",
            scope: ["Logo z ilustracją", "Paleta premium", "Menu zabiegów", "Wizytówki", "Szablony social media", "Przygotowanie kierunku wizualnego pod stronę www i social media", "Uporządkowanie zastosowań znaku w praktycznych materiałach", "Dopasowanie stylu do odbiorców, branży i pozycjonowania marki"],
          },
          "Ralf Furniture": {
            year: "2025", type: "Logo + identyfikacja wizualna", category: "Meble na wymiar", subtitle: "Branding marki meblarskiej", image: "https://zaprojektowani.com/wp-content/uploads/2026/05/ralf_logo_compressed.webp",
            lead: "Minimalistyczne logo dla marki meblarskiej, zaprojektowane pod katalogi, ofertowanie, showroom i komunikację premium dla klientów indywidualnych oraz B2B.", tags: ["Furniture", "Premium", "B2B"],
            detail: "W tym projekcie najważniejsze było zbudowanie wizerunku, który wygląda profesjonalnie już od pierwszego kontaktu z marką. Opracowaliśmy kierunek, który można wygodnie przenieść na stronę internetową, ofertę, social media, dokumenty, wizytówki oraz materiały sprzedażowe. Dzięki temu logo nie jest tylko pojedynczym znakiem, ale początkiem spójnego systemu komunikacji.",
            scope: ["Logo marki meblarskiej", "Paleta czerni i drewna", "Materiały B2B", "Katalog produktowy", "Oznaczenie showroomu", "Przygotowanie kierunku wizualnego pod stronę www i social media", "Uporządkowanie zastosowań znaku w praktycznych materiałach", "Dopasowanie stylu do odbiorców, branży i pozycjonowania marki"],
          },
          "EVA Aesthetics": {
            year: "2025", type: "Logo + identyfikacja wizualna", category: "Medycyna estetyczna", subtitle: "Luksusowy branding kliniki estetycznej", image: "https://zaprojektowani.com/wp-content/uploads/2026/05/eva_logo_compressed.webp",
            lead: "Elegancka identyfikacja wizualna dla kliniki estetycznej. Minimalistyczne logo, jasna paleta i premium charakter wzmacniają profesjonalny odbiór marki.", tags: ["Klinika", "Premium", "Beauty"],
            detail: "Projekt został przygotowany tak, żeby znak działał zarówno w małym rozmiarze, jak i w bardziej reprezentacyjnych zastosowaniach. Zadbaliśmy o czytelność, proporcje, warianty użytkowe oraz estetykę dopasowaną do branży. Całość daje marce bardziej uporządkowany i pewny wizualnie charakter.",
            scope: ["Logo kliniki", "Paleta nude & gold", "Karty zabiegowe", "Wizytówki premium", "Brandbook z wytycznymi", "Przygotowanie kierunku wizualnego pod stronę www i social media", "Uporządkowanie zastosowań znaku w praktycznych materiałach", "Dopasowanie stylu do odbiorców, branży i pozycjonowania marki"],
          },
};
        const dialog = document.getElementById("projectDialog");
        const dialogContent = document.getElementById("dialogContent");
        const imageZoom = document.getElementById("imageZoom");
        const zoomImage = document.getElementById("zoomImage");
        let lastProjectTrigger = null;
        const openZoom = (src, alt) => {
          zoomImage.src = src;
          zoomImage.alt = alt || "Powiększony podgląd projektu";
          imageZoom.showModal();
        };
        const wireZoomButtons = () => {
          dialogContent.querySelectorAll("[data-zoom]").forEach((button) => {
            button.addEventListener("click", () => {
              const img = button.querySelector("img");
              openZoom(button.dataset.zoom, img ? img.alt : "Podgląd projektu");
            });
          });
        };
        const openProject = (card) => {
          const brand = card.dataset.project;
          const p = projectDetails[brand];
          if (!p) return;
          lastProjectTrigger = card;
          const gallery = (p.gallery || []).length
            ? `<section class="dialog-section"><div class="dialog-section-head"><div><span class="dialog-section-label">Projekt w użyciu</span><h4>Nie tylko na jednej planszy.</h4></div><p>Pokazujemy system w realnych materiałach — od dokumentów i wizytówek po cyfrowe punkty styku.</p></div><div class="dialog-gallery">${p.gallery.map(([src, alt, orient]) => `<button type="button" data-zoom="${src}" data-orient="${orient}" aria-label="Powiększ: ${alt}"><img src="${src}" alt="${alt}" loading="lazy" decoding="async"></button>`).join("")}</div>${(p.variants || []).length ? `<div class="dialog-variants">${p.variants.map(([src, alt]) => `<button type="button" data-zoom="${src}" aria-label="Powiększ: ${alt}"><img src="${src}" alt="${alt}" loading="lazy" decoding="async"></button>`).join("")}</div>` : ""}</section>`
            : "";
          /* Projekty dociągnięte z bazy wtyczki nie mają rozbicia na wyzwanie/rozwiązanie/efekt —
             pokazujemy wtedy ich oryginalny opis (p.detail) zamiast wymyślać treść. */
          const story = (p.challenge && p.solution && p.effect)
            ? `<div class="dialog-story">
                <article><em>01 / wyzwanie</em><h4>Punkt wyjścia</h4><p>${p.challenge}</p></article>
                <article><em>02 / rozwiązanie</em><h4>Kierunek marki</h4><p>${p.solution}</p></article>
                <article><em>03 / efekt</em><h4>System gotowy do pracy</h4><p>${p.effect}</p></article>
              </div>`
            : (p.detail
                ? `<div class="dialog-story dialog-story--single"><article><em>o projekcie</em><h4>Jak podeszliśmy do marki</h4><p>${p.detail}</p></article></div>`
                : "");
          const results = (p.results || []).length
            ? `<section class="dialog-section">
                <div class="dialog-section-head"><div><span class="dialog-section-label">Najważniejsze elementy</span><h4>Co daje ten system?</h4></div><p>Każdy element odpowiada na konkretną potrzebę marki i razem tworzy spójną całość.</p></div>
                <div class="dialog-results">${p.results.map(([title, copy]) => `<article class="dialog-result"><strong>${title}</strong><p>${copy}</p></article>`).join("")}</div>
              </section>`
            : "";
          dialogContent.innerHTML = `
            <div class="dialog-shell">
              <div class="dialog-hero">
                <button class="dialog-media" type="button" data-zoom="${p.image}" aria-label="Powiększ projekt ${brand}"><img src="${p.image}" alt="${brand} — ${p.subtitle}"></button>
                <div class="dialog-intro">
                  <span class="dialog-badge">${p.category} / ${p.year}</span>
                  <h3 id="dialogTitle">${brand}</h3>
                  <span class="dialog-subtitle">${p.subtitle}</span>
                  <p class="dialog-lead">${p.lead}</p>
                  <div class="dialog-tags">${(p.tags || []).map((tag) => `<span>${tag}</span>`).join("")}</div>
                </div>
              </div>
              <div class="dialog-facts">
                <div class="dialog-fact"><span>Marka</span><strong>${brand}</strong></div>
                <div class="dialog-fact"><span>Branża</span><strong>${p.category}</strong></div>
                <div class="dialog-fact"><span>Zakres</span><strong>${p.type}</strong></div>
                <div class="dialog-fact"><span>Rok</span><strong>${p.year}</strong></div>
              </div>
              ${story}
              ${results}
              ${gallery}
              <section class="dialog-section">
                <div class="dialog-section-head"><div><span class="dialog-section-label">Zakres realizacji</span><h4>Od znaku do wdrożenia.</h4></div><p>Projekt kończy się uporządkowanym zestawem elementów przygotowanych do codziennego użycia.</p></div>
                <div class="dialog-scope">${(p.scope || []).map((item) => `<span>${item}</span>`).join("")}</div>
              </section>
              <div class="dialog-cta">
                <div><h4>Chcesz podobnie dopracować własną markę?</h4><p>Wybierz zakres i opowiedz nam krótko o swoim projekcie.</p></div>
                <a class="btn btn-primary" href="https://zaprojektowani.com/studio-wyceny/?zpbs_service=brand&amp;zpbs_package=Branding%20Premium&amp;zpbs_source=logo-branding-katowice#zpbsUltimate">Przejdź do Studia Wyceny <svg viewBox="0 0 24 24"><path d="M7 17 17 7M7 7h10v10"></path></svg></a>
              </div>
            </div>`;
          dialog.showModal();
          document.documentElement.classList.add("zp-project-dialog-open");
          dialog.scrollTop = 0;
          window.requestAnimationFrame(() => dialog.scrollTo({ top: 0, behavior: "auto" }));
          wireZoomButtons();
        };

        const portfolioRail = document.querySelector(".portfolio-grid");
        if (portfolioRail) {
          const portfolioCards = Array.from(portfolioRail.querySelectorAll(".project-card"));
          const portfolioCounter = document.querySelector(".portfolio-rail-count");
          const portfolioProgress = document.querySelector(".portfolio-rail-progress");
          const portfolioProgressFill = portfolioProgress?.querySelector("i");
          const portfolioPrev = document.querySelector("[data-portfolio-prev]");
          const portfolioNext = document.querySelector("[data-portfolio-next]");
          const reducePortfolioMotion = window.matchMedia("(prefers-reduced-motion: reduce)");
          let portfolioIndex = 0;
          let portfolioFrame = 0;
          let portfolioPointerStart = 0;
          let portfolioDidDrag = false;

          const setPortfolioIndex = (nextIndex) => {
            portfolioIndex = Math.max(0, Math.min(portfolioCards.length - 1, nextIndex));
            portfolioCards.forEach((card, index) => {
              card.classList.toggle("is-rail-active", index === portfolioIndex);
              if (index === portfolioIndex) card.setAttribute("aria-current", "true");
              else card.removeAttribute("aria-current");
            });
            if (portfolioCounter) {
              portfolioCounter.textContent = `${String(portfolioIndex + 1).padStart(2, "0")} / ${String(portfolioCards.length).padStart(2, "0")}`;
            }
            if (portfolioProgressFill) {
              portfolioProgressFill.style.width = `${((portfolioIndex + 1) / portfolioCards.length) * 100}%`;
            }
            if (portfolioProgress) portfolioProgress.setAttribute("aria-valuenow", String(portfolioIndex + 1));
            if (portfolioPrev) portfolioPrev.disabled = portfolioIndex === 0;
            if (portfolioNext) portfolioNext.disabled = portfolioIndex === portfolioCards.length - 1;
          };

          const detectPortfolioIndex = () => {
            portfolioFrame = 0;
            const railRect = portfolioRail.getBoundingClientRect();
            const maxScroll = Math.max(0, portfolioRail.scrollWidth - portfolioRail.clientWidth);
            if (portfolioRail.scrollLeft <= 3) return setPortfolioIndex(0);
            if (maxScroll - portfolioRail.scrollLeft <= 3) return setPortfolioIndex(portfolioCards.length - 1);
            const paddingLeft = parseFloat(getComputedStyle(portfolioRail).paddingLeft) || 0;
            const targetLeft = railRect.left + paddingLeft;
            let closestIndex = 0;
            let closestDistance = Number.POSITIVE_INFINITY;
            portfolioCards.forEach((card, index) => {
              const distance = Math.abs(card.getBoundingClientRect().left - targetLeft);
              if (distance < closestDistance) {
                closestDistance = distance;
                closestIndex = index;
              }
            });
            setPortfolioIndex(closestIndex);
          };

          const queuePortfolioUpdate = () => {
            if (!portfolioFrame) portfolioFrame = requestAnimationFrame(detectPortfolioIndex);
          };

          const scrollPortfolioTo = (nextIndex) => {
            const targetIndex = Math.max(0, Math.min(portfolioCards.length - 1, nextIndex));
            const target = portfolioCards[targetIndex];
            if (!target) return;
            const railRect = portfolioRail.getBoundingClientRect();
            const paddingLeft = parseFloat(getComputedStyle(portfolioRail).paddingLeft) || 0;
            const delta = target.getBoundingClientRect().left - railRect.left - paddingLeft;
            portfolioRail.scrollTo({
              left: portfolioRail.scrollLeft + delta,
              behavior: reducePortfolioMotion.matches ? "auto" : "smooth",
            });
            setPortfolioIndex(targetIndex);
          };

          portfolioPrev?.addEventListener("click", () => scrollPortfolioTo(portfolioIndex - 1));
          portfolioNext?.addEventListener("click", () => scrollPortfolioTo(portfolioIndex + 1));
          portfolioRail.addEventListener("scroll", queuePortfolioUpdate, { passive: true });
          portfolioRail.addEventListener("keydown", (event) => {
            if (event.key === "ArrowLeft") {
              event.preventDefault();
              scrollPortfolioTo(portfolioIndex - 1);
            }
            if (event.key === "ArrowRight") {
              event.preventDefault();
              scrollPortfolioTo(portfolioIndex + 1);
            }
          });
          portfolioRail.addEventListener("pointerdown", (event) => {
            portfolioPointerStart = event.clientX;
            portfolioDidDrag = false;
          }, { passive: true });
          portfolioRail.addEventListener("pointermove", (event) => {
            if (Math.abs(event.clientX - portfolioPointerStart) > 8) portfolioDidDrag = true;
          }, { passive: true });
          portfolioRail.addEventListener("pointerup", () => {
            window.setTimeout(() => { portfolioDidDrag = false; }, 0);
          }, { passive: true });
          portfolioRail.addEventListener("click", (event) => {
            if (!portfolioDidDrag) return;
            event.preventDefault();
            event.stopPropagation();
          }, true);
          window.addEventListener("resize", queuePortfolioUpdate, { passive: true });
          setPortfolioIndex(0);
        }

        document.querySelectorAll(".project-card").forEach((card) => {
          card.addEventListener("click", () => openProject(card));
          card.addEventListener("keydown", (event) => {
            if (event.key === "Enter" || event.key === " ") {
              event.preventDefault();
              openProject(card);
            }
          });
        });
        document.querySelectorAll("[data-open-project]").forEach((button) => {
          button.addEventListener("click", () => {
            button.dataset.project = button.dataset.openProject;
            openProject(button);
          });
        });
        dialog
          .querySelector(".dialog-close")
          .addEventListener("click", () => dialog.close());
        dialog.addEventListener("click", (event) => {
          /* Na mobile dialog zajmuje cały viewport — zamykamy go wyłącznie
             przyciskiem X, żeby kliknięcie pustej przestrzeni nie wyrzucało
             użytkownika ze szczegółów projektu. */
          if (event.target === dialog && window.innerWidth > 680) dialog.close();
        });
        dialog.addEventListener("close", () => {
          document.documentElement.classList.remove("zp-project-dialog-open");
          if (lastProjectTrigger) lastProjectTrigger.focus({ preventScroll: true });
        });
        imageZoom
          .querySelector(".dialog-close")
          .addEventListener("click", () => imageZoom.close());
        imageZoom.addEventListener("click", (event) => {
          if (event.target === imageZoom) imageZoom.close();
        });
        window.addEventListener("scroll", updateScroll, { passive: true });
        window.addEventListener("resize", () => {
          if (window.innerWidth > 1100) closeMenu();
        });
        /* #year był w stopce prototypu (usuniętej). Globalna stopka wtyczki
           ma własny licznik roku ([data-zp-year] w footer.js) — stąd zabezpieczenie. */
        const yearEl = document.getElementById("year");
        if (yearEl) yearEl.textContent = new Date().getFullYear();
        updateScroll();
      })();
    
/* v2.2.685 — ochrona obrazów przed miniaturami (odpowiednik PHP-owego
   zp_suite_logo_branding_katowice_protect_images dla treści budowanej z JS).
   Dialog projektu i zoom wstawiają <img> po stronie klienta, a wtyczki
   lazy-load/optymalizujące potrafią podmienić src na wariant -150x150 JUŻ PO
   naszym przebiegu. Dlatego harden() jest idempotentne i zapisuje atrybut tylko
   wtedy, gdy faktycznie się różni — obserwator nie wpada więc w pętlę od
   własnych zapisów, ale nadal cofa każdą późniejszą podmianę. */
(function () {
  'use strict';
  var ROOT_SEL = '#zp-logo-branding-katowice, #projectDialog, #imageZoom';
  var SIZED = /-\d{2,5}x\d{2,5}(\.(?:webp|png|jpe?g|gif|avif))(\?|$)/i;
  var LAZY = ['skip-lazy', 'no-lazy', 'no-litespeed-lazyload'];

  function harden(img) {
    if (!img || img.tagName !== 'IMG') return;

    var src = img.getAttribute('src') || '';
    var full = src.replace(SIZED, '$1$2');
    if (full && full !== src) img.setAttribute('src', full);
    if (!full) return;

    var want = full + ' 4096w';
    if (img.getAttribute('srcset') !== want) img.setAttribute('srcset', want);
    if (img.getAttribute('sizes') !== '100vw') img.setAttribute('sizes', '100vw');
    if (img.hasAttribute('data-srcset')) img.removeAttribute('data-srcset');
    if (img.hasAttribute('data-sizes')) img.removeAttribute('data-sizes');

    ['data-zp-no-thumb', 'data-no-lazy', 'data-skip-lazy', 'data-nitro-no-lazy'].forEach(function (a) {
      if (img.getAttribute(a) !== '1') img.setAttribute(a, '1');
    });
    LAZY.forEach(function (c) { if (!img.classList.contains(c)) img.classList.add(c); });
  }

  function sweep(scope) {
    if (!scope || !scope.querySelectorAll) return;
    if (scope.tagName === 'IMG') { harden(scope); return; }
    Array.prototype.forEach.call(scope.querySelectorAll('img'), harden);
  }

  function boot() {
    Array.prototype.forEach.call(document.querySelectorAll(ROOT_SEL), function (root) {
      sweep(root);
      new MutationObserver(function (muts) {
        for (var i = 0; i < muts.length; i++) {
          var mu = muts[i];
          if (mu.type === 'attributes') { harden(mu.target); continue; }
          Array.prototype.forEach.call(mu.addedNodes, function (n) {
            if (n.nodeType === 1) sweep(n);
          });
        }
      }).observe(root, {
        childList: true, subtree: true,
        attributes: true, attributeFilter: ['src', 'srcset', 'sizes']
      });
    });
  }

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', boot, { once: true });
  else boot();
})();

/* v2.2.811 — pełny zakres pakietów: hover desktop + tap/click mobile + pewne zamykanie X. */
(function () {
  'use strict';

  function bootPackageScopes() {
    var root = document.getElementById('zp-logo-branding-katowice');
    if (!root) return;

    /* v2.2.812 — Lucide tylko jako delikatne, semantyczne ikonki na froncie kart. */
    if (window.lucide && typeof window.lucide.createIcons === 'function') {
      try {
        window.lucide.createIcons({
          attrs: {
            'stroke-width': 1.7,
            'stroke-linecap': 'round',
            'stroke-linejoin': 'round',
            'aria-hidden': 'true'
          }
        });
      } catch (e) {}
    }

    var scopes = Array.prototype.slice.call(root.querySelectorAll('.package-scope'));
    if (!scopes.length) return;

    function sync(scope, open) {
      var trigger = scope.querySelector('.package-scope__trigger');
      var panel = scope.querySelector('.package-scope__panel');
      if (trigger) trigger.setAttribute('aria-expanded', open ? 'true' : 'false');
      if (panel) panel.setAttribute('aria-hidden', open ? 'false' : 'true');
    }

    function closeOthers(except) {
      scopes.forEach(function (scope) {
        if (scope === except) return;
        scope.classList.remove('is-open');
        sync(scope, false);
      });
    }

    scopes.forEach(function (scope) {
      var trigger = scope.querySelector('.package-scope__trigger');
      var close = scope.querySelector('.package-scope__close');
      if (!trigger) return;

      trigger.addEventListener('click', function () {
        scope.classList.remove('is-dismissed');
        var next = !scope.classList.contains('is-open');
        closeOthers(scope);
        scope.classList.toggle('is-open', next);
        sync(scope, next);
      });

      if (close) {
        close.addEventListener('click', function (event) {
          event.preventDefault();
          event.stopPropagation();
          scope.classList.remove('is-open');
          if (window.matchMedia('(hover: hover)').matches) scope.classList.add('is-dismissed');
          sync(scope, false);
          trigger.focus({ preventScroll: true });
        });
      }

      scope.addEventListener('mouseenter', function () {
        if (window.matchMedia('(hover: hover)').matches && !scope.classList.contains('is-open') && !scope.classList.contains('is-dismissed')) {
          sync(scope, true);
        }
      });
      scope.addEventListener('mouseleave', function () {
        scope.classList.remove('is-dismissed');
        if (window.matchMedia('(hover: hover)').matches && !scope.classList.contains('is-open')) {
          sync(scope, false);
        }
      });
      scope.addEventListener('keydown', function (event) {
        if (event.key !== 'Escape') return;
        scope.classList.remove('is-open');
        if (window.matchMedia('(hover: hover)').matches) scope.classList.add('is-dismissed');
        sync(scope, false);
        trigger.focus({ preventScroll: true });
      });
    });

    document.addEventListener('click', function (event) {
      scopes.forEach(function (scope) {
        if (!scope.classList.contains('is-open') || scope.contains(event.target)) return;
        scope.classList.remove('is-open');
        sync(scope, false);
      });
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bootPackageScopes, { once: true });
  } else {
    bootPackageScopes();
  }
})();
