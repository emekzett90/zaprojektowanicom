Zaprojektowani Suite v2.2.636 — optymalizacja strony głównej, menu mobilnego i Studio Wyceny.

# Zaprojektowani Suite v2.2.110 — Strony Internetowe Katowice SEO

- Dostosowano podstronę /strony-internetowe-katowice/ pod strategię SEO z maja 2026.
- Poprawiono H1, H2, leady, FAQ, proces, treści branżowe i alty obrazków bez zmian wizualnych layoutu.
- Dodano mocniejsze JSON-LD: WebPage, Service, BreadcrumbList, FAQPage.
- Odświeżono seed CMS do v1.8.14, żeby nowe treści weszły także przy istniejących opcjach.

# Zaprojektowani Suite v2.2.109 — Home FAQ AI SEO + Schema

- Rozbudowano FAQ strony głównej do 10 treściwych pytań i odpowiedzi pod użytkowników, Google i asystentów AI.
- Dodano microdata FAQPage/Question/Answer w sekcji FAQ.
- Rozszerzono JSON-LD strony głównej: knowsAbout, hasOfferCatalog, BreadcrumbList, speakable, mentions i FAQPage na podstawie widocznych pytań.
- FAQ wzmacnia strategię SEO: strony internetowe Katowice, sklepy WooCommerce, logo i branding, SEO, kampanie Meta Ads, branże i praca zdalna.

- v2.1.62: Mobile header divider exact align — linia wyrównana do dolnej krawędzi sticky/static headera, bez skoku i bez opóźnionego doganiania.
- v2.1.60: Wiedza hero video — navy maska, cień pod tekstem/CTA i ustawienia 0–100 w ZP Suite CMS (widoczność, jasność, maska navy, cień).
- v2.1.59: Wiedza video bootstrap jak w działającym hero sklepów + bezpieczny desktop speed pass dla strony głównej.
Zaprojektowani Suite v1.8.77 — Hard Desktop Logo + Mobile Glass Fix

- Dodano płynniejsze floatowanie i hover kart opinii w hero.
- Dodano pola admina do regulacji typografii i skali kart opinii.

Zaprojektowani Suite v1.8.77 — Hard Desktop Logo + Mobile Glass Fix

- Przywrócona sekcja Usługi i branże 1:1 z dostarczonego widgetu HTML.
- CSS/JS sekcji przeniesione do assets, żeby shortcode renderował właściwy wygląd i animacje.

# Zaprojektowani Suite v1.8.77 — Hard Desktop Logo + Mobile Glass Fix

Front Polish & Performance Pack dla zaprojektowani.com.

## Shortcode’y strony głównej

```txt
[zp_header]
[zp_home_full]
[zp_footer]
```

## Nowości v1.8.57

- Smooth loading system (`zp-loading` / `zp-ready`).
- Hero poster + video fallback i strategia ładowania video w CMS.
- Premium skeleton/fallback hero, żeby nie było pustego bloku.
- Smart image loading: `alt`, `decoding`, `loading`, `fetchpriority`.
- Home image audit w panelu Front Polish.
- Lekki smooth scroll dla anchorów.
- Globalny reveal manager.
- Safari/iOS safe mode bez ograniczania efektów mobile.
- Header load lock i CLS guard.
- Font preload dla lokalnej Jakarty.
- Asset / Front Health / speed score.

Po aktualizacji wyczyść LiteSpeed Cache oraz CSS/JS Cache.


## v1.8.57 — Mobile + PageSpeed hotfix
- Ustawiony czytelniejszy dimmer video na mobile (`--videoDimMobile:42`; skala 1–100 w CSS).
- Hero mobile: wydłużone, mockup przesunięty w prawo i opuszczony o 20px.
- Branże: poprawione nazwy obrazów w seed/CMS + JS fallback, żeby brakujące grafiki podmieniały się na działające warianty.
- Marquee logotypów: przyspieszone na mobile i wyrównane do marginesu sekcji/kart.
- PageSpeed: video w hero nie wymusza natychmiastowego `load()` na mobile; preload zmieniony na `none`, start w idle, bez zmiany wyglądu.


## 1.9.04
- Dodano ustawienie w panelu: Hero → Media i wygląd → „Lewa kolumna hero desktop: góra/dół px”. Wartość ujemna podnosi lewą sekcję H1/lead/CTA/chipy bez ruszania obrazu po prawej.


## v2.2.101
- FIX: rejestracja shortcode `[zp_logo_branding_katowice]` i `[zp_page_logo_branding_katowice]`.


## v2.2.133
- Poprawka hero /strony-internetowe-katowice/ na mobile: usunięty twardy prostokątny edge/cień przy mockupie, bez zmiany layoutu.

## v2.2.132
Performance polish dla home i /strony-internetowe-katowice/ bez zmian wizualnych: preloads, hero critical paint, lazy render sekcji below-the-fold i opóźnienie video na mobile/PSI.
