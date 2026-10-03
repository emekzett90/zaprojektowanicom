/* ZAPROJEKTOWANI — Strony internetowe Katowice v2 (1:1 z prototypu v6).
   #siteHeader / .menu-toggle / #callbackPanel nie istnieją na tej stronie
   (globalny header wtyczki + globalny widget zpFloatUx) — kod jest na to odporny. */

/* Bazowy URL assetów strony. page.php ustawia window.ZP_SI_ASSETS na ZP_SUITE_URL.
   Fallbacki: URL własnego <script>, a na końcu konwencjonalna ścieżka wtyczki. */
var ZP_SI_ASSETS = (function () {
  try {
    if (window.ZP_SI_ASSETS) return window.ZP_SI_ASSETS;
    var s = document.querySelector('script[src*="strony-internetowe-katowice.js"]');
    if (s && s.src) return s.src.split('?')[0].replace(/assets\/js\/blocks\/[^/]*$/, 'assets/strony-internetowe/');
  } catch (e) {}
  return '/wp-content/plugins/zaprojektowani-suite/assets/strony-internetowe/';
})();


    (() => {
      const header = document.getElementById('siteHeader');
      const toggle = document.querySelector('.menu-toggle');
      const progress = document.querySelector('.scroll-progress');
      const navLinks = [...document.querySelectorAll('.nav a')];
      const revealItems = document.querySelectorAll('.reveal, .campaign-cockpit, .process-track, .process-step');
      const sections = [...document.querySelectorAll('main section[id]')];
      const reduced = matchMedia('(prefers-reduced-motion: reduce)').matches;
      const portfolioStack = document.getElementById('portfolioStack');
      const portfolioCards = portfolioStack ? [...portfolioStack.querySelectorAll('[data-portfolio-card]')] : [];
      const portfolioTotalLabels = [...document.querySelectorAll('.pf-toolbar__count em, .pf-side-progress em')];
      const portfolioTotalText = '/' + String(portfolioCards.length).padStart(2, '0');
      portfolioTotalLabels.forEach(label => { label.textContent = portfolioTotalText; });
      const portfolioCurrent = document.getElementById('portfolioCurrent');
      const portfolioProgress = document.getElementById('portfolioProgress');
      const portfolioPrev = document.getElementById('portfolioPrev');
      const portfolioNext = document.getElementById('portfolioNext');
      let portfolioIndex = 0;

      portfolioCards.forEach(card => {
        const liveChip = card.querySelector('.pf-card__index b');
        if (!liveChip) return;
        liveChip.classList.add('pf-live-chip');
        liveChip.setAttribute('aria-label', 'Projekt dostępny na żywo');
        card.append(liveChip);
      });

      const setPortfolioIndex = index => {
        if (!portfolioCards.length) return;
        portfolioIndex = Math.max(0, Math.min(portfolioCards.length - 1, index));
        portfolioCards.forEach((card, cardIndex) => card.classList.toggle('is-current', cardIndex === portfolioIndex));
        portfolioCurrent.textContent = String(portfolioIndex + 1).padStart(2, '0');
        portfolioProgress.style.transform = `scaleX(${(portfolioIndex + 1) / portfolioCards.length})`;
        portfolioPrev.disabled = portfolioIndex === 0;
        portfolioNext.disabled = portfolioIndex === portfolioCards.length - 1;
      };

      const updateDesktopPortfolio = () => {
        if (!portfolioCards.length || window.innerWidth <= 820) return;
        const center = window.innerHeight * .52;
        let best = Infinity;
        let nextIndex = portfolioIndex;
        portfolioCards.forEach((card, index) => {
          const rect = card.getBoundingClientRect();
          if (rect.bottom < 0 || rect.top > window.innerHeight) return;
          const distance = Math.abs(rect.top + rect.height * .5 - center);
          if (distance < best) { best = distance; nextIndex = index; }
        });
        setPortfolioIndex(nextIndex);
      };

      const fab = document.getElementById('callbackFab');
      const processSection = document.querySelector('.process');
      const processHead = document.querySelector('.process .process-headline');

      const updateScroll = () => {
        const y = window.scrollY;
        const max = Math.max(1, document.documentElement.scrollHeight - window.innerHeight);
        /* ZP Suite: #siteHeader, .scroll-progress i #callbackFab pochodzą z prototypu.
           Header jest globalny (wtyczka), a pływające CTA obsługuje zpFloatUx. */
        if (header) header.classList.toggle('is-scrolled', y > 24);
        if (progress) progress.style.transform = `scaleX(${Math.min(1, y / max)})`;
        if (fab) fab.classList.toggle('is-shown', y > 480);

        /* Siedzący Mateusz w sekcji proces chowa się przy scrollu (jak na logo-branding). */
        if (processSection && processHead && window.innerWidth > 680) {
          const rect = processHead.getBoundingClientRect();
          const travel = Math.max(0, Math.min(1, (window.innerHeight * 0.15 - rect.top) / Math.max(1, rect.height * 0.88)));
          const fade = Math.max(0, Math.min(1, (travel - 0.7) / 0.3));
          processSection.style.setProperty('--process-person-shift', `${Math.round(-travel * 115)}px`);
          processSection.style.setProperty('--process-person-opacity', (1 - fade).toFixed(3));
        }

        let current = 'start';
        sections.forEach(section => {
          if (y >= section.offsetTop - 180) current = section.id;
        });
        navLinks.forEach(link => link.classList.toggle('is-active', link.getAttribute('href') === `#${current}`));
        updateDesktopPortfolio();
      };

      const closeMenu = () => {
        if (toggle) toggle.setAttribute('aria-expanded', 'false');
        if (header) header.classList.remove('is-open');
        document.body.classList.remove('menu-open');
      };
      if (toggle) {
        toggle.addEventListener('click', () => {
          const open = toggle.getAttribute('aria-expanded') !== 'true';
          toggle.setAttribute('aria-expanded', String(open));
          if (header) header.classList.toggle('is-open', open);
          document.body.classList.toggle('menu-open', open);
        });
      }
      navLinks.forEach(link => link.addEventListener('click', closeMenu));

      const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.classList.add('is-visible');
            observer.unobserve(entry.target);
          }
        });
      }, { threshold: 0.12, rootMargin: '0px 0px -7% 0px' });
      revealItems.forEach(item => observer.observe(item));

      document.querySelectorAll('.faq-item').forEach(item => {
        item.addEventListener('toggle', () => {
          if (!item.open) return;
          document.querySelectorAll('.faq-item[open]').forEach(other => {
            if (other !== item) other.removeAttribute('open');
          });
        });
      });

      if (portfolioStack) {
        let portfolioRaf = 0;
        portfolioStack.addEventListener('scroll', () => {
          if (window.innerWidth > 820 || portfolioRaf) return;
          portfolioRaf = requestAnimationFrame(() => {
            portfolioRaf = 0;
            const padding = parseFloat(getComputedStyle(portfolioStack).paddingLeft) || 0;
            let best = Infinity;
            let nextIndex = 0;
            portfolioCards.forEach((card, index) => {
              const distance = Math.abs(card.offsetLeft - portfolioStack.offsetLeft - padding - portfolioStack.scrollLeft);
              if (distance < best) { best = distance; nextIndex = index; }
            });
            setPortfolioIndex(nextIndex);
          });
        }, { passive: true });
        const goToPortfolioCard = index => {
          const card = portfolioCards[Math.max(0, Math.min(portfolioCards.length - 1, index))];
          if (!card) return;
          const padding = parseFloat(getComputedStyle(portfolioStack).paddingLeft) || 0;
          portfolioStack.scrollTo({ left: card.offsetLeft - portfolioStack.offsetLeft - padding, behavior: reduced ? 'auto' : 'smooth' });
        };
        portfolioPrev.addEventListener('click', () => goToPortfolioCard(portfolioIndex - 1));
        portfolioNext.addEventListener('click', () => goToPortfolioCard(portfolioIndex + 1));
        setPortfolioIndex(0);
      }

      const trustedLogos = document.querySelector('.client-trust__logos');
      if (trustedLogos && !reduced) {
        const mobileLogos = matchMedia('(max-width: 820px)');
        let logosTimer = 0;
        let logosDirection = 1;
        let logosVisible = false;
        let logosPausedUntil = 0;

        const stopLogoMotion = () => {
          if (!logosTimer) return;
          clearInterval(logosTimer);
          logosTimer = 0;
        };
        const stepLogos = () => {
          if (!mobileLogos.matches || !logosVisible || document.hidden || Date.now() < logosPausedUntil) return;
          const maxScroll = trustedLogos.scrollWidth - trustedLogos.clientWidth;
          if (maxScroll < 4) return;
          if (trustedLogos.scrollLeft >= maxScroll - 2) logosDirection = -1;
          else if (trustedLogos.scrollLeft <= 2) logosDirection = 1;
          const firstLogo = trustedLogos.querySelector('.client-logo');
          const distance = Math.max(130, (firstLogo?.getBoundingClientRect().width || 160) * .9);
          trustedLogos.scrollBy({ left: logosDirection * distance, behavior: 'smooth' });
        };
        const syncLogoMotion = () => {
          stopLogoMotion();
          if (!mobileLogos.matches || !logosVisible || document.hidden) return;
          logosTimer = window.setInterval(stepLogos, 2300);
        };
        const pauseLogoMotion = () => { logosPausedUntil = Date.now() + 5000; };
        const logosObserver = new IntersectionObserver(entries => {
          logosVisible = entries.some(entry => entry.isIntersecting);
          syncLogoMotion();
        }, { threshold: .15 });

        logosObserver.observe(trustedLogos);
        trustedLogos.addEventListener('pointerdown', pauseLogoMotion, { passive: true });
        trustedLogos.addEventListener('touchstart', pauseLogoMotion, { passive: true });
        trustedLogos.addEventListener('wheel', pauseLogoMotion, { passive: true });
        trustedLogos.addEventListener('focusin', pauseLogoMotion);
        document.addEventListener('visibilitychange', syncLogoMotion);
        if (mobileLogos.addEventListener) mobileLogos.addEventListener('change', syncLogoMotion);
        else mobileLogos.addListener(syncLogoMotion);
      }

      const projectDetails = {
        apartament: {
          title: 'Apartament Piękna', client: 'Apartament Piękna', type: 'Strona usługowa + rezerwacje', industry: 'Beauty premium / medycyna estetyczna', year: '2026',
          image: ZP_SI_ASSETS + 'project-detail-apartament.webp', alt: 'Pełny widok strony Apartament Piękna', caption: 'Pełny widok realizacji • desktop', live: 'https://www.apartamentpiekna.pl/', colors: ['#191013', '#4b2430', '#e7a9b8'],
          description: 'Dla Apartamentu Piękna przeprowadziliśmy pełny redesign obecności marki w internecie. Odświeżyliśmy identyfikację, zaprojektowaliśmy elegancką stronę beauty i wdrożyliśmy autorski system rezerwacji z przypomnieniami SMS. Projekt łączy estetykę premium z wygodną obsługą wizyt i lokalnym SEO.',
          challenge: 'Uproszczenie rezerwacji zabiegów oraz uporządkowanie rozbudowanej oferty bez utraty luksusowego charakteru marki.',
          solution: 'Zaprojektowaliśmy przejrzyste kategorie zabiegów, system rezerwacji 24/7, przypomnienia SMS i strukturę treści pod lokalne wyszukiwania.',
          effect: 'Nowoczesny serwis, który odciąża recepcję, wspiera sprzedaż usług i konsekwentnie buduje wizerunek salonu premium.',
          scope: ['Całkowity redesign strony i identyfikacji', 'Autorski system rezerwacji wizyt online', 'Powiadomienia SMS dla klientów', 'Blog i struktura SEO pod frazy lokalne', 'Konfiguracja GA4, Search Console i Meta Pixel'],
          services: ['UX/UI', 'branding', 'copywriting', 'SEO lokalne', 'analityka'],
          technologies: [{ name: 'WordPress', logo: ZP_SI_ASSETS + 'tech-wordpress.svg' }, { name: 'Search Console', logo: ZP_SI_ASSETS + 'tech-search-console.svg' }, { name: 'Google Analytics 4', short: 'GA4', logo: ZP_SI_ASSETS + 'tech-google-analytics.svg' }, { name: 'Meta Pixel', short: 'Meta', logo: ZP_SI_ASSETS + 'tech-meta.svg' }],
          capabilities: ['rezerwacje 24/7', 'powiadomienia SMS', 'autorski blog', 'SEO lokalne', 'pełny responsive'],
          results: [['Rezerwacje 24/7', 'Klientki samodzielnie wybierają usługę i termin wizyty.'], ['Automatyzacja SMS', 'Przypomnienia ograniczają liczbę zapomnianych wizyt.'], ['Widoczność lokalna', 'Struktura i blog są przygotowane pod Tarnów i okolice.']]
        },
        siemianowski: {
          title: 'Siemianowski', client: 'Kancelaria Siemianowski', type: 'Branding + strona ekspercka', industry: 'Kancelaria premium / prawo i restrukturyzacja', year: '2026',
          image: ZP_SI_ASSETS + 'project-detail-siemianowski.webp', alt: 'Pełny widok strony kancelarii Siemianowski', caption: 'Pełny widok realizacji • desktop', live: 'https://siemianowski.pl/', colors: ['#06101c', '#102f4e', '#d8b579'],
          description: 'Dla kancelarii Arkadiusza Siemianowskiego stworzyliśmy kompletny wizerunek premium oraz rozbudowaną stronę ekspercką. Projekt porządkuje specjalizacje prawne i restrukturyzacyjne, eksponuje doświadczenie oraz prowadzi użytkownika do kontaktu lub umówienia wizyty.',
          challenge: 'Zbudowanie zaufania jeszcze przed pierwszą rozmową oraz czytelne przedstawienie szerokiego zakresu specjalizacji.',
          solution: 'Połączyliśmy elegancki branding z uporządkowaną architekturą informacji, treściami eksperckimi i prostą ścieżką umawiania spotkań.',
          effect: 'Spójna marka kancelarii i serwis, który wspiera pozyskiwanie klientów oraz rozwój widoczności eksperckiej.',
          scope: ['Logo i system identyfikacji premium', 'Projekt rozbudowanej strony kancelarii', 'Struktura specjalizacji prawnych', 'Sekcje zaufania, doświadczenia i kontaktu', 'Umawianie wizyt i baza pod SEO'],
          services: ['branding', 'UX/UI', 'copywriting', 'SEO', 'treści eksperckie'],
          technologies: [{ name: 'WordPress', logo: ZP_SI_ASSETS + 'tech-wordpress.svg' }, { name: 'Figma', logo: ZP_SI_ASSETS + 'tech-figma.svg' }],
          capabilities: ['umawianie wizyt', 'formularze konsultacji', 'struktura specjalizacji', 'sekcje zaufania', 'pełny responsive'],
          results: [['Marka premium', 'Logo, typografia, kolorystyka i strona tworzą jeden system.'], ['Prostszy kontakt', 'Czytelne CTA prowadzą bezpośrednio do umówienia wizyty.'], ['Baza pod SEO', 'Struktura wspiera rozwój specjalizacji i treści eksperckich.']]
        },
        gravia: {
          title: 'Gravia', client: 'Gravia', type: 'Strona B2B + system kariery', industry: 'Infrastruktura / branża drogowa', year: '2026',
          image: ZP_SI_ASSETS + 'project-detail-gravia.webp', alt: 'Pełny widok strony Gravia', caption: 'Pełny widok realizacji • desktop', live: 'https://gravia.pl/', colors: ['#08090b', '#3a0806', '#ff5d56'],
          description: 'Dla biura projektów infrastrukturalnych Gravia stworzyliśmy techniczną, nowoczesną stronę pokazującą skalę kompetencji i realizacji. Projekt otrzymał uporządkowaną ofertę, rozbudowane portfolio oraz autorski moduł kariery z obsługą CV i załączników.',
          challenge: 'Przedstawienie złożonych usług technicznych w sposób zrozumiały dla inwestorów i atrakcyjny dla kandydatów.',
          solution: 'Zbudowaliśmy klarowną strukturę usług, katalog realizacji i dedykowany system rekrutacyjny dostępny z poziomu panelu.',
          effect: 'Profesjonalna platforma firmowa wspierająca sprzedaż B2B, prezentację doświadczenia i pozyskiwanie pracowników.',
          scope: ['Indywidualny projekt graficzny', 'Podstrony usługowe i informacyjne', 'Rozbudowana sekcja realizacji', 'System kariery z obsługą CV i plików', 'Responsywne wdrożenie WordPress'],
          services: ['Web Design', 'UX', 'architektura informacji', 'wdrożenie', 'formularze'],
          technologies: [{ name: 'WordPress', logo: ZP_SI_ASSETS + 'tech-wordpress.svg' }],
          capabilities: ['system kariery', 'obsługa CV i załączników', 'portfolio realizacji', 'podstrony ofertowe', 'custom CMS'],
          results: [['System kariery', 'Oferty pracy, formularze i bezpieczna obsługa CV.'], ['Portfolio realizacji', 'Czytelna prezentacja projektów i kompetencji zespołu.'], ['Techniczny wizerunek', 'Design dopasowany do inżynierii i klientów instytucjonalnych.']]
        },
        shothome: {
          title: 'ShotHome', client: 'ShotHome', type: 'Strona usługowa + wycena', industry: 'Drzwi, podłogi i wykończenia / Warszawa', year: '2026',
          image: ZP_SI_ASSETS + 'project-detail-shothome.webp', alt: 'Pełny widok strony ShotHome', caption: 'Pełny widok realizacji • desktop', live: 'https://www.shothome.pl/', colors: ['#0d0f0d', '#687268', '#d4ddd2'],
          description: 'Dla ShotHome zaprojektowaliśmy stronę usługową nastawioną na szybkie pozyskiwanie zapytań z Warszawy. Serwis łączy ofertę drzwi, podłóg i wykończeń z customową darmową wyceną, katalogiem produktów oraz panelem realizacji.',
          challenge: 'Skrócenie drogi od zainteresowania ofertą do konkretnego zapytania oraz uporządkowanie wielu grup produktowych.',
          solution: 'Wdrożyliśmy konfigurator wyceny, moduł realizacji, katalog produktów i rozbudowaną strukturę landingów usługowych.',
          effect: 'Serwis pełniący jednocześnie funkcję prezentacji oferty, generatora leadów i bazy treści pod marketing lokalny.',
          scope: ['Indywidualny projekt graficzny', 'Customowy formularz darmowej wyceny', 'Panel realizacji i katalog produktów', 'Blog i struktura treści pod SEO', 'Optymalizacja lokalna pod Warszawę'],
          services: ['UX/UI', 'SEO', 'copywriting', 'wdrożenie', 'optymalizacja lokalna'],
          technologies: [{ name: 'WordPress', logo: ZP_SI_ASSETS + 'tech-wordpress.svg' }, { name: 'PHP', logo: ZP_SI_ASSETS + 'tech-php.svg' }],
          capabilities: ['wycena online', 'panel CRUD realizacji', 'katalog podłóg', 'blog', 'custom CMS'],
          results: [['Wycena online', 'Klient przekazuje zakres prac bez długiej wymiany wiadomości.'], ['Własny panel', 'Zespół aktualizuje produkty, realizacje i treści.'], ['SEO Warszawa', 'Struktura wspiera lokalne usługi i kampanie.']]
        },
        proscarves: {
          title: 'ProScarves', client: 'ProScarves', type: 'E-commerce + ścieżka B2B', industry: 'B2B custom / sklep sportowy', year: '2026',
          image: ZP_SI_ASSETS + 'project-detail-proscarves.webp', alt: 'Pełny widok sklepu ProScarves', caption: 'Pełny widok realizacji • desktop', live: 'https://www.proscarves.com/', colors: ['#061011', '#0b6571', '#55cfe3'],
          description: 'Dla ProScarves zaprojektowaliśmy rozbudowany sklep sportowy łączący sprzedaż detaliczną z obsługą zamówień klubowych i hurtowych. Klient indywidualny kupuje gotowy produkt, a klub lub organizacja przechodzi przez dedykowaną ścieżkę zapytania i wyceny produkcji customowej.',
          challenge: 'Połączenie dwóch różnych modeli sprzedaży w jednym serwisie bez komplikowania ścieżki zakupowej.',
          solution: 'Rozdzieliliśmy e-commerce detaliczny i zapytania B2B, projektując osobne komunikaty, formularze oraz prezentację produkcji customowej.',
          effect: 'Spójna platforma, która obsługuje klientów indywidualnych, kluby i partnerów biznesowych w jednym ekosystemie.',
          scope: ['Indywidualny projekt sklepu', 'Sklep detaliczny z gotowymi produktami', 'System wyceny zamówień customowych', 'Ścieżka zapytań B2B', 'Struktura pod rynki międzynarodowe'],
          services: ['UX/UI', 'e-commerce', 'B2B custom', 'analityka', 'architektura sprzedaży'],
          technologies: [{ name: 'WordPress', logo: ZP_SI_ASSETS + 'tech-wordpress.svg' }, { name: 'WooCommerce', logo: ZP_SI_ASSETS + 'tech-woocommerce.svg' }],
          capabilities: ['detal + hurt', 'system wyceny', 'custom orders', 'formularze klubowe', 'rynki międzynarodowe'],
          results: [['Dwie ścieżki sprzedaży', 'Detaliczny sklep i zapytania klubowe w jednym serwisie.'], ['Custom B2B', 'System zbiera dane potrzebne do przygotowania wyceny.'], ['Gotowość na rozwój', 'Struktura wspiera kolejne rynki i kategorie.']]
        },
        krawiec: {
          title: 'Krawiec z dojazdem', client: 'Krawiec z dojazdem', type: 'Rebranding + system wizyt', industry: 'Usługi premium / lokalnie', year: '2026',
          image: ZP_SI_ASSETS + 'project-detail-krawiec.webp', alt: 'Pełny widok strony Krawiec z dojazdem', caption: 'Pełny widok realizacji • desktop', live: 'https://krawieczdojazdem.pl/', colors: ['#070707', '#3a2b13', '#d6b06b'],
          description: 'Dla usługi krawieckiej premium przygotowaliśmy rebranding, nową stronę oraz system umawiania wizyt z dojazdem do klienta. Projekt podkreśla indywidualne podejście i upraszcza rezerwację spotkania bezpośrednio z telefonu.',
          challenge: 'Wyjaśnienie nietypowego modelu usługi i zamiana zainteresowania w konkretną rezerwację wizyty.',
          solution: 'Połączyliśmy elegancki wizerunek z prostą ścieżką usług, lokalizacją dojazdu i kalendarzem spotkań.',
          effect: 'Spójna marka premium i narzędzie wspierające pozyskiwanie lokalnych klientów bez ręcznego ustalania każdego terminu.',
          scope: ['Rebranding identyfikacji wizualnej', 'Strona WordPress od zera', 'System umawiania wizyt online', 'Optymalizacja SEO lokalnego', 'Projekt mobile-first i analityka'],
          services: ['branding', 'Web Design', 'UX mobile', 'SEO lokalne', 'analityka'],
          technologies: [{ name: 'WordPress', logo: ZP_SI_ASSETS + 'tech-wordpress.svg' }, { name: 'Google Analytics', short: 'Analytics', logo: ZP_SI_ASSETS + 'tech-google-analytics.svg' }],
          capabilities: ['system wizyt', 'mobile-first', 'obszar dojazdu', 'formularze kontaktowe', 'SEO lokalne'],
          results: [['Nowy wizerunek', 'Rebranding porządkujący komunikację usługi premium.'], ['Wizyty online', 'Prosty proces umawiania spotkania z dojazdem.'], ['Mobile-first', 'Najważniejsze działania są wygodne na telefonie.']]
        },
        papeterio: {
          title: 'Papeterio', client: 'Papeterio', type: 'Sklep z personalizacją', industry: 'Papeteria / e-commerce', year: '2026',
          image: ZP_SI_ASSETS + 'project-detail-papeterio.webp', alt: 'Pełny widok sklepu Papeterio', caption: 'Pełny widok realizacji • desktop', live: 'https://papeterio.pl/', colors: ['#140d0d', '#58352f', '#e3b7a8'],
          description: 'Dla Papeterio stworzyliśmy subtelny sklep internetowy z papeterią i dodatkami na wyjątkowe okazje. Kluczowe było połączenie delikatnej estetyki marki z czytelną personalizacją produktów, wariantami i dodatkami.',
          challenge: 'Pokazanie wielu wariantów personalizacji w sposób prosty, estetyczny i niewymagający dodatkowego kontaktu.',
          solution: 'Uporządkowaliśmy opcje produktu, dodatki i pola personalizacji, projektując lekkie karty produktowe oraz wygodny koszyk.',
          effect: 'Elegancki e-commerce, który zachowuje emocjonalny charakter marki, a jednocześnie ułatwia składanie złożonych zamówień.',
          scope: ['Sklep z kategoriami produktów', 'Dopracowane karty produktów', 'Opcje personalizacji i warianty', 'Koszyk oraz płatności online', 'Mobilna ścieżka zakupowa'],
          services: ['Web Design', 'e-commerce', 'UX', 'copywriting', 'wdrożenie'],
          technologies: [{ name: 'WordPress', logo: ZP_SI_ASSETS + 'tech-wordpress.svg' }, { name: 'WooCommerce', logo: ZP_SI_ASSETS + 'tech-woocommerce.svg' }],
          capabilities: ['personalizacja', 'warianty i dodatki', 'płatności online', 'formularze kontaktowe', 'mobile commerce'],
          results: [['Czytelna personalizacja', 'Warianty, teksty i dodatki wybiera się przy produkcie.'], ['Zakupy mobilne', 'Interfejs jest dopracowany pod zamówienia z telefonu.'], ['Spójna estetyka', 'Sklep i komunikacja tworzą jedno doświadczenie marki.']]
        },
        bransoletka: {
          title: 'Bransoletka24', client: 'Bransoletka24', type: 'WooCommerce B2C + B2B', industry: 'E-commerce / detal + hurt', year: '2026',
          image: ZP_SI_ASSETS + 'project-detail-bransoletka.webp', alt: 'Pełny widok sklepu Bransoletka24', caption: 'Pełny widok realizacji • desktop', live: 'https://bransoletka24.pl/', colors: ['#070713', '#35204a', '#c69be0'],
          description: 'Bransoletka24 otrzymała nowy sklep WooCommerce obsługujący równolegle sprzedaż detaliczną i hurtową. Zaprojektowaliśmy przejrzyste kategorie, szybkie filtrowanie, warianty produktów, wishlistę i uproszczony checkout.',
          challenge: 'Obsługa dwóch grup klientów i rozbudowanego katalogu bez przeciążania interfejsu.',
          solution: 'Wdrożyliśmy osobne mechanizmy cenowe dla hurtu, intuicyjne filtry, listę ulubionych i uproszczoną ścieżkę zakupu.',
          effect: 'Skalowalny sklep, który porządkuje katalog i ułatwia zakupy zarówno klientom indywidualnym, jak i partnerom hurtowym.',
          scope: ['Sklep WooCommerce od zera', 'System hurtowy z rejestracją B2B', 'Integracja płatności online', 'Wishlista, filtry i warianty', 'Optymalizacja konwersji i checkoutu'],
          services: ['e-commerce', 'B2C + B2B', 'UX', 'checkout', 'optymalizacja konwersji'],
          technologies: [{ name: 'WordPress', logo: ZP_SI_ASSETS + 'tech-wordpress.svg' }, { name: 'WooCommerce', logo: ZP_SI_ASSETS + 'tech-woocommerce.svg' }, { name: 'GSAP', logo: ZP_SI_ASSETS + 'tech-gsap.svg' }],
          capabilities: ['ceny hurtowe', 'płatności online', 'wishlista', 'filtry i warianty', 'animacje interfejsu'],
          results: [['B2C + B2B', 'Jeden sklep z osobną obsługą detalu i hurtu.'], ['Szybszy wybór', 'Filtry, warianty i wishlista porządkują katalog.'], ['Lepszy checkout', 'Mniej kroków i czytelne podsumowanie zamówienia.']]
        },
        rutpoz: {
          title: 'RUTPOŻ', client: 'RUTPOŻ', type: 'Platforma firmowa + AI', industry: 'PPOŻ / system zgłoszeń + AI', year: '2025',
          image: ZP_SI_ASSETS + 'project-detail-rutpoz.jpg', alt: 'Pełny widok platformy RUTPOŻ', caption: 'Pełny widok realizacji • desktop', live: 'https://rutpoz.pl/', colors: ['#100706', '#5b1510', '#ff8a42'],
          description: 'Dla RUTPOŻ stworzyliśmy rozbudowaną platformę firmową łączącą ofertę PPOŻ z narzędziami użytkowymi. Serwis zawiera system zgłoszeń, kalkulatory branżowe, generowanie raportów PDF oraz asystenta AI odpowiadającego na pytania klientów.',
          challenge: 'Przeniesienie technicznych procesów i powtarzalnych zapytań klientów do jednego, łatwego w obsłudze systemu.',
          solution: 'Połączyliśmy stronę ofertową z kalkulatorami, formularzami serwisowymi, raportami i bazą wiedzy obsługiwaną przez AI.',
          effect: 'Platforma ograniczająca ręczną pracę zespołu i dostarczająca klientom odpowiedzi oraz narzędzia przez całą dobę.',
          scope: ['Strona firmowa WordPress', 'Asystent AI 24/7', 'Kalkulatory wydajności i gęstości ogniowej', 'Generowanie raportów PDF', 'System zgłoszeń serwisowych'],
          services: ['UX/UI', 'wdrożenie', 'automatyzacja', 'system zgłoszeń', 'narzędzia branżowe'],
          technologies: [{ name: 'WordPress', logo: ZP_SI_ASSETS + 'tech-wordpress.svg' }, { name: 'AIPKIT', short: 'AI', logo: ZP_SI_ASSETS + 'tech-aipkit.svg' }],
          capabilities: ['asystent AI 24/7', 'kalkulator wydajności hydrantów', 'kalkulator gęstości ogniowej', 'raporty PDF', 'cyfrowe zgłoszenia'],
          results: [['Asystent AI 24/7', 'Odpowiada na podstawowe pytania bez oczekiwania.'], ['Kalkulatory PPOŻ', 'Narzędzia branżowe są dostępne na stronie.'], ['Cyfrowe zgłoszenia', 'Obsługa serwisu i dokumentacji jest uporządkowana.']]
        },
        swiatgrili: {
          title: 'Świat Grilli', client: 'Świat Grilli', type: 'Sklep WooCommerce', industry: 'Grille, wędzarnie i akcesoria / e-commerce', year: '2026',
          image: 'https://zaprojektowani.com/wp-content/uploads/2026/09/swiatgrili_vis.webp', alt: 'Świat Grilli — sklep internetowy z grillami na laptopie i telefonie', caption: 'Prezentacja realizacji • desktop + mobile', live: 'https://swiatgrili.pl/', colors: ['#080808', '#3a0808', '#f3312f'],
          description: 'Dla Świata Grilli przygotowaliśmy nowoczesny sklep internetowy porządkujący ofertę grilli, wędzarni, palenisk, akcesoriów i części wielu producentów. Interfejs stawia na szybkie wyszukiwanie, czytelne kategorie i marki, mocne karty produktowe oraz wygodny zakup na telefonie.',
          challenge: 'Uporządkowanie szerokiego katalogu i wielu marek tak, aby klient szybko dotarł do właściwego typu grilla, akcesoriów lub produktu promocyjnego.',
          solution: 'Zaprojektowaliśmy wyraźną hierarchię kategorii, wyszukiwarkę, filtrowanie produktów, moduły marek i promocji oraz responsywną ścieżkę zakupową WooCommerce.',
          effect: 'Sklep jest prostszy w przeglądaniu, lepiej eksponuje ofertę i skraca drogę od inspiracji do dodania produktu do koszyka.',
          scope: ['Indywidualny projekt UX/UI sklepu', 'WooCommerce i rozbudowany katalog produktów', 'Kategorie, marki, wyszukiwarka i filtry', 'Koszyk, płatności i proces zakupowy', 'Dopracowana wersja mobilna'],
          services: ['UX/UI', 'e-commerce', 'architektura katalogu', 'mobile commerce', 'wdrożenie'],
          technologies: [{ name: 'WordPress', logo: ZP_SI_ASSETS + 'tech-wordpress.svg' }, { name: 'WooCommerce', logo: ZP_SI_ASSETS + 'tech-woocommerce.svg' }],
          capabilities: ['katalog wielu marek', 'filtry produktów', 'wyszukiwarka sklepu', 'promocje i outlet', 'mobile commerce'],
          results: [['Lepsza nawigacja', 'Kategorie i marki pomagają szybciej zawęzić wybór.'], ['Sprzedaż mobilna', 'Kluczowe elementy zakupowe są dopracowane pod telefon.'], ['Spójna marka', 'Czerń, biel i mocna czerwień budują rozpoznawalny charakter sklepu.']]
        },
        polerstone: {
          title: 'Polerstone', client: 'Polerstone', type: 'Strona firmowa premium', industry: 'Kamieniarstwo / blaty i kamień naturalny', year: '2026',
          image: 'https://zaprojektowani.com/wp-content/uploads/2026/09/polerstone_vis_front.webp', alt: 'Polerstone — strona firmy kamieniarskiej na laptopie i telefonie', caption: 'Prezentacja realizacji • desktop + mobile', live: 'https://polerstone.pl/', colors: ['#0d0d0d', '#4a433d', '#d6c1a7'],
          description: 'Dla Polerstone stworzyliśmy minimalistyczny kierunek strony premium, w którym to materiał gra główną rolę. Serwis prezentuje kamieniarstwo budowlane, blaty, realizacje oraz szeroką bibliotekę materiałów — od granitu i marmuru po kwarcyty, konglomeraty i spieki.',
          challenge: 'Pokazanie szerokiej oferty kamienia w sposób nowoczesny i uporządkowany, bez przytłaczania użytkownika technicznymi informacjami.',
          solution: 'Postawiliśmy na dużą fotografię, mocną typografię, spokojną paletę inspirowaną kamieniem oraz czytelne przejścia między materiałami, realizacjami i zapytaniem o wycenę.',
          effect: 'Serwis pozycjonuje markę wyżej wizualnie i ułatwia klientowi przejście od inspiracji materiałem do kontaktu w sprawie konkretnej realizacji.',
          scope: ['Indywidualny projekt strony premium', 'Prezentacja kamieniarstwa budowlanego', 'Sekcja materiałów i realizacji', 'Ścieżka kontaktu i zapytania o wycenę', 'Responsywne wdrożenie WordPress'],
          services: ['strategia', 'UX/UI', 'Web Design', 'wdrożenie', 'prezentacja portfolio'],
          technologies: [{ name: 'WordPress', logo: ZP_SI_ASSETS + 'tech-wordpress.svg' }, { name: 'Figma', logo: ZP_SI_ASSETS + 'tech-figma.svg' }],
          capabilities: ['oferta materiałów', 'portfolio realizacji', 'wycena', 'mobile-first', 'czytelna architektura usług'],
          results: [['Wizerunek premium', 'Minimalizm i materiały budują jakość bez zbędnych ozdobników.'], ['Czytelna oferta', 'Blaty, kamień i materiały są uporządkowane w logiczne ścieżki.'], ['Szybsza wycena', 'CTA prowadzą użytkownika od realizacji i materiału prosto do kontaktu.']]
        }
      };

      const createTechnologyBadge = (technology, compact = false) => {
        const badge = document.createElement(compact ? 'span' : 'div');
        if (!compact) badge.className = 'project-tech';
        const logo = document.createElement('img');
        logo.src = technology.logo;
        logo.alt = '';
        logo.setAttribute('aria-hidden', 'true');
        logo.loading = 'lazy';
        logo.decoding = 'async';
        badge.append(logo, document.createTextNode(compact ? (technology.short || technology.name) : technology.name));
        return badge;
      };

      document.querySelectorAll('[data-portfolio-card][data-project]').forEach(card => {
        const project = projectDetails[card.dataset.project];
        const copy = card.querySelector('.pf-card__copy');
        const tagRow = copy?.querySelector('.pf-tags');
        if (!project?.technologies?.length || !copy || !tagRow) return;
        const row = document.createElement('div');
        row.className = 'pf-techs';
        row.setAttribute('aria-label', 'Technologie projektu');
        row.replaceChildren(...project.technologies.map(technology => createTechnologyBadge(technology, true)));
        const technologyNames = new Set(project.technologies.flatMap(technology => [technology.name, technology.short]).filter(Boolean).map(name => name.toLocaleLowerCase('pl-PL')));
        tagRow.querySelectorAll('span').forEach(tag => { if (technologyNames.has(tag.textContent.trim().toLocaleLowerCase('pl-PL'))) tag.remove(); });
        copy.insertBefore(row, tagRow);
      });

      const projectDialog = document.getElementById('projectDialog');
      if (projectDialog) {
        const dialogHero = document.getElementById('projectDialogHero');
        const dialogTitle = document.getElementById('projectDialogTitle');
        const dialogEyebrow = document.getElementById('projectDialogEyebrow');
        const dialogMeta = document.getElementById('projectDialogMeta');
        const dialogClient = document.getElementById('projectDialogClient');
        const dialogYear = document.getElementById('projectDialogYear');
        const dialogType = document.getElementById('projectDialogType');
        const dialogImage = document.getElementById('projectDialogImage');
        const dialogCaption = document.getElementById('projectDialogCaption');
        const dialogLead = document.getElementById('projectDialogLead');
        const dialogTechnologies = document.getElementById('projectDialogTechnologies');
        const dialogChallenge = document.getElementById('projectDialogChallenge');
        const dialogSolution = document.getElementById('projectDialogSolution');
        const dialogEffect = document.getElementById('projectDialogEffect');
        const dialogScope = document.getElementById('projectDialogScope');
        const dialogCapabilities = document.getElementById('projectDialogCapabilities');
        const dialogServices = document.getElementById('projectDialogServices');
        const dialogResults = document.getElementById('projectDialogResults');
        const dialogLive = document.getElementById('projectDialogLive');
        let lastProjectTrigger = null;

        const fillProjectDialog = project => {
          dialogHero.style.setProperty('--modal-a', project.colors[0]);
          dialogHero.style.setProperty('--modal-b', project.colors[1]);
          dialogHero.style.setProperty('--modal-accent', project.colors[2]);
          projectDialog.style.setProperty('--modal-accent', project.colors[2]);
          dialogTitle.textContent = project.title;
          dialogEyebrow.textContent = project.industry;
          dialogMeta.textContent = 'Case study • projekt, wdrożenie i rozwój';
          dialogClient.textContent = project.client;
          dialogYear.textContent = project.year;
          dialogType.textContent = project.type;
          dialogImage.src = project.image;
          dialogImage.alt = project.alt;
          dialogCaption.textContent = project.caption;
          dialogLead.textContent = project.description;
          dialogTechnologies.replaceChildren(...project.technologies.map(technology => createTechnologyBadge(technology)));
          dialogChallenge.textContent = project.challenge;
          dialogSolution.textContent = project.solution;
          dialogEffect.textContent = project.effect;
          dialogScope.replaceChildren(...project.scope.map(item => { const li = document.createElement('li'); li.textContent = item; return li; }));
          dialogCapabilities.replaceChildren(...project.capabilities.map(item => { const span = document.createElement('span'); span.textContent = item; return span; }));
          dialogServices.replaceChildren(...project.services.map(item => { const span = document.createElement('span'); span.textContent = item; return span; }));
          dialogResults.replaceChildren(...project.results.map(([title, copy]) => { const article = document.createElement('article'); article.className = 'project-result'; const strong = document.createElement('strong'); const span = document.createElement('span'); strong.textContent = title; span.textContent = copy; article.append(strong, span); return article; }));
          dialogLive.href = project.live;
          dialogLive.setAttribute('aria-label', `Zobacz stronę ${project.title} na żywo`);
          projectDialog.querySelector('.project-dialog__shell').scrollTop = 0;
        };

        document.querySelectorAll('[data-project-details]').forEach(button => button.addEventListener('click', () => {
          const project = projectDetails[button.dataset.projectDetails];
          if (!project) return;
          lastProjectTrigger = button;
          fillProjectDialog(project);
          projectDialog.showModal();
          document.body.classList.add('project-open');
          projectDialog.querySelector('[data-project-close]').focus();
        }));
        projectDialog.querySelector('[data-project-close]').addEventListener('click', () => projectDialog.close());
        projectDialog.addEventListener('click', event => { if (event.target === projectDialog) projectDialog.close(); });
        projectDialog.addEventListener('close', () => {
          document.body.classList.remove('project-open');
          if (lastProjectTrigger) lastProjectTrigger.focus({ preventScroll: true });
        });
      }

      const nf = (v, d) => v.toLocaleString('pl-PL', { minimumFractionDigits: d, maximumFractionDigits: d });
      const runCount = el => {
        const to = parseFloat(el.dataset.count);
        const d = parseInt(el.dataset.decimals || '0', 10);
        const suffix = el.dataset.suffix || '';
        if (reduced || !isFinite(to)) { el.textContent = nf(to, d) + suffix; return; }
        const t0 = performance.now(), dur = 1500;
        const tick = now => {
          const p = Math.min(1, (now - t0) / dur);
          const e = 1 - Math.pow(1 - p, 4);
          el.textContent = nf(to * e, d) + suffix;
          if (p < 1) requestAnimationFrame(tick);
        };
        requestAnimationFrame(tick);
        setTimeout(() => { el.textContent = nf(to, d) + suffix; }, dur + 200);
      };
      const countObserver = new IntersectionObserver(entries => {
        entries.forEach(entry => {
          if (!entry.isIntersecting) return;
          runCount(entry.target);
          countObserver.unobserve(entry.target);
        });
      }, { threshold: .6 });
      document.querySelectorAll('[data-count]').forEach(el => countObserver.observe(el));

      const donut = document.getElementById('mixDonut');
      if (donut) {
        const target = parseFloat(donut.dataset.a);
        const c1 = donut.dataset.c1 || '#8ec8f7';
        const c2 = donut.dataset.c2 || '#7c82ff';
        const paint = v => { donut.style.background = `conic-gradient(${c1} 0 ${v}%, ${c2} ${v}% 100%)`; };
        const donutObserver = new IntersectionObserver(entries => {
          entries.forEach(entry => {
            if (!entry.isIntersecting) return;
            donutObserver.disconnect();
            if (reduced) { paint(target); return; }
            const t0 = performance.now();
            const tick = now => {
              const p = Math.min(1, (now - t0) / 1300);
              paint(target * (1 - Math.pow(1 - p, 3)));
              if (p < 1) requestAnimationFrame(tick);
            };
            requestAnimationFrame(tick);
            setTimeout(() => paint(target), 1500);
          });
        }, { threshold: .5 });
        donutObserver.observe(donut);
      }

      document.querySelectorAll('.channel-card,.placement-card,.package-card,.case-card').forEach(card => {
        const spot = document.createElement('i');
        spot.className = 'card-spot';
        spot.setAttribute('aria-hidden', 'true');
        card.appendChild(spot);
        card.addEventListener('pointermove', e => {
          const r = card.getBoundingClientRect();
          card.style.setProperty('--mx', `${e.clientX - r.left}px`);
          card.style.setProperty('--my', `${e.clientY - r.top}px`);
        });
      });

      /* ZP Suite: pływające CTA „Zostaw numer" z prototypu jest wyłączone —
         wtyczka renderuje globalnie własny widget zpFloatUx (wp_footer) w tym
         samym rogu ekranu, więc oba nachodziłyby na siebie. Markup callbacka
         nie trafia do body.html, dlatego cała obsługa jest tu warunkowa. */
      const panel = document.getElementById('callbackPanel');
      if (panel) {
        const cbForm = panel.querySelector('.callback-form');
        const cbInput = document.getElementById('cbPhone');
        const setOpen = open => {
          panel.classList.toggle('is-open', open);
          panel.setAttribute('aria-hidden', String(!open));
          fab.setAttribute('aria-expanded', String(open));
          if (open) setTimeout(() => cbInput.focus(), 260);
        };
        fab.addEventListener('click', () => setOpen(!panel.classList.contains('is-open')));
        panel.querySelector('.callback-close').addEventListener('click', () => setOpen(false));
        document.addEventListener('keydown', e => { if (e.key === 'Escape') setOpen(false); });
        document.addEventListener('click', e => {
          if (panel.classList.contains('is-open') && !panel.contains(e.target) && !fab.contains(e.target)) setOpen(false);
        });
        document.querySelectorAll('[data-open-callback]').forEach(el => el.addEventListener('click', e => {
          e.preventDefault();
          fab.classList.add('is-shown');
          setOpen(true);
        }));
        cbInput.addEventListener('input', () => {
          const digits = cbInput.value.replace(/\D/g, '').slice(0, 9);
          cbInput.value = digits.replace(/(\d{3})(?=\d)/g, '$1 ');
          panel.classList.remove('is-error');
        });
        cbForm.addEventListener('submit', e => {
          e.preventDefault();
          const digits = cbInput.value.replace(/\D/g, '');
          if (digits.length !== 9) { panel.classList.add('is-error'); cbInput.focus(); return; }
          panel.querySelector('[data-cb-num]').textContent = `+48 ${cbInput.value}`;
          cbForm.hidden = true;
          panel.querySelector('.callback-note').hidden = true;
          panel.querySelector('.callback-legal').hidden = true;
          panel.querySelector('.callback-done').hidden = false;
        });
      }

      window.addEventListener('scroll', updateScroll, { passive: true });
      window.addEventListener('resize', () => { if (window.innerWidth > 1100) closeMenu(); });
      const yearEl = document.getElementById('year');
      if (yearEl) yearEl.textContent = new Date().getFullYear();
      updateScroll();
    })();
  (()=>{/* MAPA / ZASIĘG — logika 1:1 z logo-branding-katowice.js.
   Prototyp celował w '.web-local .poland-map-card' (klasa usunięta) i nie ustawiał
   --poland-path-length, przez co brakowało animacji rysowania konturu. */
const mapCard = document.querySelector('.poland-map-card');
const mapStatus = mapCard ? mapCard.querySelector('.map-status') : null;
const mapMarkers = mapCard ? [...mapCard.querySelectorAll('.map-marker')] : [];
const activateMapMarker = (marker) => {
  mapMarkers.forEach((item) => item.classList.toggle('is-active', item === marker));
  if (!mapStatus) return;
  const s = mapStatus.querySelector('strong'), sm = mapStatus.querySelector('small'), p = mapStatus.querySelector('p');
  if (s) s.textContent = marker.dataset.city || '';
  if (sm) sm.textContent = marker.dataset.mode || '';
  if (p) p.textContent = marker.dataset.copy || '';
  if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches && typeof mapStatus.animate === 'function') {
    mapStatus.animate(
      [{ opacity: 0.45, transform: 'translateY(8px) scale(.985)' }, { opacity: 1, transform: 'translateY(0) scale(1)' }],
      { duration: 360, easing: 'cubic-bezier(.22,.8,.32,1)' }
    );
  }
};
mapMarkers.forEach((marker) => marker.addEventListener('click', () => activateMapMarker(marker)));
const polandOutline = document.getElementById('polandOutline');
const polandSvg = document.querySelector('.poland-shape-svg');
if (polandOutline && polandSvg && typeof polandOutline.getTotalLength === 'function') {
  polandSvg.style.setProperty('--poland-path-length', String(Math.ceil(polandOutline.getTotalLength())));
}
const portfolio=document.getElementById('portfolio'),side=document.querySelector('.pf-side-progress'),cards=[...document.querySelectorAll('[data-portfolio-card]')];if(portfolio&&side&&cards.length){const fill=side.querySelector('.pf-side-progress__track i'),current=side.querySelector('b'),update=()=>{const rect=portfolio.getBoundingClientRect(),active=rect.top<innerHeight*.35&&rect.bottom>innerHeight*.45;side.classList.toggle('is-visible',active&&innerWidth>1100);let best=0,dist=Infinity;cards.forEach((card,i)=>{const d=Math.abs(card.getBoundingClientRect().top-innerHeight*.28);if(d<dist){dist=d;best=i}});if(fill)fill.style.height=`${((best+1)/cards.length)*100}%`;if(current)current.textContent=String(best+1).padStart(2,'0')};addEventListener('scroll',update,{passive:true});addEventListener('resize',update);update()}const contact=document.querySelector('.contact-system');if(contact){const services=[...contact.querySelectorAll('.contact-service')],selected=contact.querySelector('[data-contact-selected]'),sync=()=>{const list=services.filter(x=>x.classList.contains('is-active')).map(x=>x.dataset.service);if(selected)selected.textContent=list.join(', ')||'Wybierz zakres'};services.forEach(btn=>btn.addEventListener('click',()=>{btn.classList.toggle('is-active');sync()}));const tabs=[...contact.querySelectorAll('button[data-contact-tab]')],panels=[...contact.querySelectorAll('[data-contact-panel]')];tabs.forEach(tab=>tab.addEventListener('click',()=>{tabs.forEach(x=>x.classList.toggle('is-active',x===tab));panels.forEach(panel=>{const on=panel.dataset.contactPanel===tab.dataset.contactTab;panel.classList.toggle('is-active',on);panel.hidden=!on})}));const form=contact.querySelector('.contact-form');form?.addEventListener('submit',e=>{e.preventDefault();const note=form.querySelector('.contact-notice');if(note)note.textContent='Prototyp formularza — po wdrożeniu wiadomość będzie wysyłana przez system wtyczki.'});sync()}})();
/* v2.2.694 — niezależny trigger animacji rysowania mapy + CTA pakietów. */
(() => {
  const root = document.getElementById('zp-strony-internetowe-katowice');
  if (!root) return;

  /* Karty mają kilka warstw dekoracyjnych. Zatrzymujemy bubbling kliknięcia,
     żeby żaden globalny handler motywu/karty nie blokował normalnej nawigacji. */
  root.querySelectorAll('.package-cta, .packages-bottom a.btn').forEach((link) => {
    link.addEventListener('click', (event) => event.stopPropagation());
  });

  const card = root.querySelector('.poland-map-card');
  const outline = root.querySelector('#polandOutline');
  const svg = root.querySelector('.poland-shape-svg');
  const draw = root.querySelector('.poland-shape-draw');
  const fill = root.querySelector('.poland-shape-fill');
  if (!card || !outline || !svg || !draw) return;

  let length = 2800;
  if (typeof outline.getTotalLength === 'function') {
    length = Math.ceil(outline.getTotalLength());
  }
  svg.style.setProperty('--poland-path-length', String(length));

  const startDrawing = () => {
    card.classList.remove('is-map-drawing');
    draw.style.setProperty('animation', 'none', 'important');
    draw.style.setProperty('transition', 'none', 'important');
    draw.style.setProperty('stroke-dasharray', `${length}px`, 'important');
    draw.style.setProperty('stroke-dashoffset', `${length}px`, 'important');
    if (fill) {
      fill.style.setProperty('animation', 'none', 'important');
      fill.style.setProperty('transition', 'none', 'important');
      fill.style.setProperty('opacity', '0', 'important');
    }
    void draw.getBoundingClientRect();
    card.classList.add('is-map-drawing');
    requestAnimationFrame(() => {
      requestAnimationFrame(() => {
        draw.style.setProperty('transition', 'stroke-dashoffset 2.15s cubic-bezier(.22,.8,.32,1)', 'important');
        draw.style.setProperty('stroke-dashoffset', '0px', 'important');
        if (fill) {
          fill.style.setProperty('transition', 'opacity .82s ease 1.05s', 'important');
          fill.style.setProperty('opacity', '1', 'important');
        }
      });
    });
  };

  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    draw.style.setProperty('stroke-dashoffset', '0px', 'important');
    if (fill) fill.style.setProperty('opacity', '1', 'important');
    card.classList.add('is-map-drawing');
    return;
  }

  if ('IntersectionObserver' in window) {
    const observer = new IntersectionObserver((entries) => {
      for (const entry of entries) {
        if (!entry.isIntersecting) continue;
        startDrawing();
        observer.disconnect();
        break;
      }
    }, { threshold: 0.22, rootMargin: '0px 0px -8% 0px' });
    observer.observe(card);
  } else {
    startDrawing();
  }
})();

/* v2.2.817 — pełny zakres pakietów WWW zbudowany od nowa.
 * Panel jest przenoszony bezpośrednio do karty, więc nie może zostać
 * przykryty przez starsze warstwy/z-index sekcji. Desktop: hover + klik.
 * Mobile/tablet: klik/tap. X i Escape zawsze zamykają panel. */
(() => {
  function bootWebPackageScopesV217(){
    const root = document.getElementById('zp-strony-internetowe-katowice');
    if (!root || root.dataset.scopeV217 === '1') return;
    root.dataset.scopeV217 = '1';

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

    const canHover = () => window.matchMedia('(hover: hover) and (pointer: fine)').matches;
    const entries = [];

    root.querySelectorAll('.package-scope').forEach((scope) => {
      const card = scope.closest('.package-card');
      const trigger = scope.querySelector('.package-scope__trigger');
      const panel = scope.querySelector('.package-scope__panel');
      if (!card || !trigger || !panel) return;

      /* Najważniejsze: panel staje się bezpośrednim dzieckiem karty.
         Dzięki temu jego z-index nie jest ograniczany przez .package-scope. */
      card.appendChild(panel);
      panel.classList.add('package-scope__panel--card');

      const close = panel.querySelector('.package-scope__close');
      const entry = { scope, card, trigger, panel, close, pinned: false, dismissed: false };
      entries.push(entry);

      const sync = (open) => {
        card.classList.toggle('scope-open', !!open);
        card.classList.toggle('scope-pinned', !!open && entry.pinned);
        trigger.setAttribute('aria-expanded', open ? 'true' : 'false');
        panel.setAttribute('aria-hidden', open ? 'false' : 'true');
      };

      const closeSelf = ({ focus = false, dismiss = false } = {}) => {
        entry.pinned = false;
        entry.dismissed = !!dismiss;
        sync(false);
        if (focus) {
          try { trigger.focus({ preventScroll: true }); }
          catch (e) { trigger.focus(); }
        }
      };

      entry.sync = sync;
      entry.closeSelf = closeSelf;

      trigger.addEventListener('mouseenter', () => {
        if (!canHover() || entry.dismissed || entry.pinned) return;
        entries.forEach((other) => {
          if (other === entry) return;
          other.pinned = false;
          other.dismissed = false;
          other.sync(false);
        });
        sync(true);
      });

      trigger.addEventListener('click', (event) => {
        event.preventDefault();
        event.stopPropagation();
        const shouldOpen = !card.classList.contains('scope-open') || !entry.pinned;
        entries.forEach((other) => {
          if (other === entry) return;
          other.pinned = false;
          other.dismissed = false;
          other.sync(false);
        });
        entry.dismissed = false;
        entry.pinned = shouldOpen;
        sync(shouldOpen);
      });

      card.addEventListener('mouseleave', () => {
        entry.dismissed = false;
        if (canHover() && !entry.pinned) sync(false);
      });

      if (close) {
        close.addEventListener('click', (event) => {
          event.preventDefault();
          event.stopPropagation();
          closeSelf({ focus: true, dismiss: canHover() });
        });
      }

      panel.addEventListener('click', (event) => event.stopPropagation());
      panel.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', (event) => event.stopPropagation());
      });

      card.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape' || !card.classList.contains('scope-open')) return;
        event.preventDefault();
        closeSelf({ focus: true, dismiss: canHover() });
      });
    });

    document.addEventListener('click', (event) => {
      entries.forEach((entry) => {
        if (!entry.pinned || entry.card.contains(event.target)) return;
        entry.closeSelf();
      });
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bootWebPackageScopesV217, { once: true });
  } else {
    bootWebPackageScopesV217();
  }
})();
