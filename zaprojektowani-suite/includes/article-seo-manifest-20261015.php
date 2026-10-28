<?php
/** Exact reviewed 162-post scope, from the authenticated 2026-10-15 baseline.
 * No score is stored here. Slugs, status and publication dates are guards, not updates.
 * Existing 27 explicit social images are preserved; three shared covers use their own article's existing inline asset.
 * The copywriting cover uses an existing editorial asset visually approved by the site owner.
 */
if (!defined('ABSPATH')) { exit; }

function zp_article_seo_manifest_20261015(): array {
    static $manifest = null;
    if ($manifest === null) {
        $manifest = json_decode(<<<'JSON'
{
  "1446": {
    "category_slug": "seo-i-konwersja",
    "expected_title": "Teksty na stronę – jak pisać, żeby nie brzmiały jak katalog usług?",
    "expected_status": "publish",
    "expected_slug": "jak-pisac-teksty-na-strone-zeby-nie-brzmialy-jak-katalog-uslug",
    "expected_date": "2026-05-20 22:11:37",
    "expected_featured_id": 1487,
    "featured_id": 4939,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/09/wordpress-tresci-redakcja.webp",
    "facebook_image_id": 4939,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/09/wordpress-tresci-redakcja.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "Zespół redakcyjny planuje teksty, nagłówki i treści na stronę internetową",
    "featured_alt_source": "Existing editorial asset visually reviewed and approved by the site owner on 2026-10-15",
    "classification_action": "consolidate_duplicate_labels_or_choose_primary",
    "rationale": "Tytuł i nagłówki opisują temat „SEO i konwersja”; zachować tę główną intencję."
  },
  "1671": {
    "category_slug": "sklepy-internetowe",
    "expected_title": "Kiedy warto założyć sklep WooCommerce?",
    "expected_status": "publish",
    "expected_slug": "wiedza-kiedy-warto-zalozyc-sklep-woocommerce",
    "expected_date": "2026-05-21 18:01:10",
    "expected_featured_id": 1672,
    "featured_id": 1672,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/kiedy-warto-zalozyc-sklep-1.webp",
    "facebook_image_id": 1672,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/kiedy-warto-zalozyc-sklep-1.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Sklepy internetowe”; zachować tę główną intencję.",
    "seo_meta": {
      "rank_math_focus_keyword": "sklep WooCommerce, kiedy warto założyć sklep WooCommerce, sklep WooCommerce dla małej firmy, WooCommerce dla małej firmy, sklep internetowy WordPress, wdrożenie WooCommerce",
      "rank_math_title": "Sklep WooCommerce dla małej firmy: kiedy warto go założyć?",
      "rank_math_description": "Sprawdź, kiedy sklep WooCommerce ma sens dla małej firmy. Porównaj koszty, funkcje, integracje i ryzyka oraz zaplanuj testy przed startem sprzedaży."
    },
    "expected_seo_meta": {
      "rank_math_focus_keyword": [
        "kiedy warto założyć sklep WooCommerce,sklep WooCommerce dla małej fimry,WooCommerce dla małej firmy,sklep internetowy WordPress,wdrożenie WooCommerce"
      ],
      "rank_math_title": [
        "Kiedy warto założyć sklep WooCommerce?"
      ],
      "rank_math_description": [
        "Sprawdź, kiedy WooCommerce ma sens dla małej firmy: koszty, funkcje, integracje, skalowanie, ryzyka wdrożenia i testy przed startem."
      ]
    },
    "seo_meta_en": {
      "rank_math_title": "A WooCommerce store for a small business: when is it worth launching?",
      "rank_math_description": "Find out when a WooCommerce store makes sense for a small business. Compare costs, features, integrations and risks, and plan your pre-launch tests."
    }
  },
  "1684": {
    "category_slug": "logo-branding",
    "expected_title": "Ile kosztuje logo dla firmy? Ceny logo i identyfikacji wizualnej",
    "expected_status": "publish",
    "expected_slug": "ile-kosztuje-logo-dla-firmy-i-co-obejmuje-cena",
    "expected_date": "2026-05-21 21:23:21",
    "expected_featured_id": 1792,
    "featured_id": 1792,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/ile-kosztuje-logo-dla-firmy-1.webp",
    "facebook_image_id": 1792,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/ile-kosztuje-logo-dla-firmy-1.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Logo i branding”; zachować tę główną intencję.",
    "seo_meta": {
      "rank_math_focus_keyword": "projekt logo, ile kosztuje logo, projekt logo ceny, logo ceny, projektowanie logo cena, projekt logo cena",
      "rank_math_title": "Projekt logo dla firmy: ceny, zakres i pliki do odbioru",
      "rank_math_description": "Sprawdź, co obejmuje projekt logo i od czego zależy jego cena. Porównaj zakres koncepcji, poprawek, praw autorskich i plików potrzebnych firmie."
    },
    "expected_seo_meta": {
      "rank_math_focus_keyword": [
        "ile kosztuje logo, projekt logo ceny, logo ceny, projektowanie logo cena, projekt logo cena"
      ],
      "rank_math_title": [
        "Ile kosztuje logo? Ceny projektu logo w 2026"
      ],
      "rank_math_description": [
        "Ile kosztuje projekt logo w 2026? Widełki cen logo, mini brandingu i pełnej identyfikacji wizualnej oraz to, co obejmuje cena. Zobacz przykłady."
      ]
    },
    "seo_meta_en": {
      "rank_math_title": "Business logo design: prices, scope and deliverable files",
      "rank_math_description": "Find out what a logo design project includes and what affects its price. Compare concepts, revisions, usage rights and the files your business needs."
    }
  },
  "1800": {
    "category_slug": "logo-branding",
    "expected_title": "Logo a branding — czym różnią się w praktyce?",
    "expected_status": "publish",
    "expected_slug": "czym-rozni-sie-logo-od-brandingu",
    "expected_date": "2026-05-21 21:48:53",
    "expected_featured_id": 3335,
    "featured_id": 3335,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/logo-a-branding-1-1.webp",
    "facebook_image_id": 3335,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/logo-a-branding-1-1.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Logo i branding”; zachować tę główną intencję."
  },
  "1809": {
    "category_slug": "sklepy-internetowe",
    "expected_title": "Co powinien zawierać sklep internetowy przed startem reklamy?",
    "expected_status": "publish",
    "expected_slug": "co-powinien-zawierac-sklep-internetowy-przed-startem-reklamy",
    "expected_date": "2026-05-21 22:01:46",
    "expected_featured_id": 1812,
    "featured_id": 1812,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/co-powinien-zawierac-sklep-1.webp",
    "facebook_image_id": 1812,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/co-powinien-zawierac-sklep-1.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Sklepy internetowe”; zachować tę główną intencję."
  },
  "1818": {
    "category_slug": "logo-branding",
    "expected_title": "Brandbook — co powinien zawierać i jak go czytać?",
    "expected_status": "publish",
    "expected_slug": "brandbook-co-powinien-zawierac-i-jak-go-czytac",
    "expected_date": "2026-05-21 22:11:13",
    "expected_featured_id": 1819,
    "featured_id": 1819,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/brandbook-co-powinien-zawierac-1.webp",
    "facebook_image_id": 1819,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/brandbook-co-powinien-zawierac-1.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Logo i branding”; zachować tę główną intencję."
  },
  "1828": {
    "category_slug": "seo-i-konwersja",
    "expected_title": "Jak mierzyć konwersje z formularza kontaktowego?",
    "expected_status": "publish",
    "expected_slug": "jak-mierzyc-konwersje-z-formularza-kontaktowego",
    "expected_date": "2026-05-21 22:17:12",
    "expected_featured_id": 1831,
    "featured_id": 1831,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/jak-mierzyc-konwersje-z-formularza-kontaktowego-1.webp",
    "facebook_image_id": 1831,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/jak-mierzyc-konwersje-z-formularza-kontaktowego-1.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "primary_refinement",
    "rationale": "Konwersja formularza, zdarzenie sukcesu, GA4 i GTM; pomiar dotyczy także ruchu spoza reklam Meta."
  },
  "1845": {
    "category_slug": "kampanie-reklamowe",
    "expected_title": "Jak przygotować stronę pod reklamy Facebook i Instagram?",
    "expected_status": "publish",
    "expected_slug": "jak-przygotowac-strone-pod-reklamy-facebook-i-instagram",
    "expected_date": "2026-05-21 22:28:47",
    "expected_featured_id": 1849,
    "featured_id": 1849,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/jak-przygotowac-strone-pod-reklany-1.webp",
    "facebook_image_id": 1849,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/jak-przygotowac-strone-pod-reklany-1.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "consolidate_duplicate_labels_or_choose_primary",
    "rationale": "Tytuł i nagłówki opisują temat „Kampanie reklamowe”; zachować tę główną intencję."
  },
  "2049": {
    "category_slug": "kampanie-reklamowe",
    "expected_title": "Landing page czy strona internetowa — co wybrać pod kampanię?",
    "expected_status": "publish",
    "expected_slug": "landing-page-czy-strona-internetowa-co-wybrac-pod-kampanie",
    "expected_date": "2026-05-22 17:25:13",
    "expected_featured_id": 2051,
    "featured_id": 2051,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/czy-warto-robic-audyt-strony-3-1.webp",
    "facebook_image_id": 2051,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/czy-warto-robic-audyt-strony-3-1.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "consolidate_duplicate_labels_or_choose_primary",
    "rationale": "Tytuł i nagłówki opisują temat „Kampanie reklamowe”; zachować tę główną intencję."
  },
  "2086": {
    "category_slug": "seo-i-konwersja",
    "expected_title": "Strona ma ruch, ale nie generuje zapytań – dlaczego?",
    "expected_status": "publish",
    "expected_slug": "dlaczego-strona-ma-ruch-ale-nie-generuje-zapytan",
    "expected_date": "2026-05-22 18:52:49",
    "expected_featured_id": 2089,
    "featured_id": 2089,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/dlaczego-strona-ma-ruch-1.webp",
    "facebook_image_id": 2089,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/dlaczego-strona-ma-ruch-1.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "consolidate_duplicate_labels_or_choose_primary",
    "rationale": "Diagnoza ruchu bez zapytań, CTA, formularzy i źródeł; główną intencją jest poprawa konwersji."
  },
  "2104": {
    "category_slug": "strony-internetowe",
    "expected_title": "Jak wybrać agencję do stworzenia strony WWW?",
    "expected_status": "publish",
    "expected_slug": "jak-wybrac-agencje-do-stworzenia-strony-www",
    "expected_date": "2026-05-22 19:10:23",
    "expected_featured_id": 2107,
    "featured_id": 2107,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/jak-wybrac-agencje-do-storzenia-strony-www-1.webp",
    "facebook_image_id": 2107,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/jak-wybrac-agencje-do-storzenia-strony-www-1.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "primary_refinement",
    "rationale": "Wybór agencji do zaprojektowania i wdrożenia strony; SEO pozostaje kryterium wyboru, nie główną usługą."
  },
  "2133": {
    "category_slug": "seo-i-konwersja",
    "expected_title": "Case studies na stronie firmowej — jak pokazać realizacje, efekty współpracy i zwiększyć liczbę zapytań",
    "expected_status": "publish",
    "expected_slug": "case-studies-na-stronie-firmowej-jak-pokazac-realizacje",
    "expected_date": "2026-05-22 19:46:09",
    "expected_featured_id": 1672,
    "featured_id": 2134,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/case-studies-na-stronie-firmowej-2.webp",
    "facebook_image_id": 2134,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/case-studies-na-stronie-firmowej-2.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "Case studies na stronie firmowej — jak pokazać realizacje, problem, proces i efekt końcowy",
    "featured_alt_source": "Existing approved inline image ALT in this exact article",
    "classification_action": "consolidate_duplicate_labels_or_choose_primary",
    "rationale": "Tytuł i nagłówki opisują temat „SEO i konwersja”; zachować tę główną intencję."
  },
  "2150": {
    "category_slug": "kampanie-reklamowe",
    "expected_title": "Strona usługowa pod Google Ads — jak przygotować ofertę i landingi pod kliknięcia",
    "expected_status": "publish",
    "expected_slug": "strona-uslugowa-pod-google-ads-jak-przygotowac-oferte-i-landingi",
    "expected_date": "2026-05-22 19:54:55",
    "expected_featured_id": 2151,
    "featured_id": 2151,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/strona-uslugowa-goofle-ads-1.webp",
    "facebook_image_id": 2151,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/strona-uslugowa-goofle-ads-1.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "primary_refinement",
    "rationale": "Dopasowanie strony docelowej do intencji Google Ads; landing page jest usługą wspierającą kampanię."
  },
  "2156": {
    "category_slug": "sklepy-internetowe",
    "expected_title": "Sklep internetowy dla marki premium — jak połączyć estetykę, UX i sprzedaż",
    "expected_status": "publish",
    "expected_slug": "sklep-internetowy-dla-marki-premium-jak-polaczyc-estetyke-ux-i-sprzedaz",
    "expected_date": "2026-05-22 19:59:55",
    "expected_featured_id": 2160,
    "featured_id": 2160,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/skle-internetowy-dla-marki-premium-1.webp",
    "facebook_image_id": 2160,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/skle-internetowy-dla-marki-premium-1.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Sklepy internetowe”; zachować tę główną intencję."
  },
  "2416": {
    "category_slug": "seo-i-konwersja",
    "expected_title": "Formularz kontaktowy na stronie firmowej — jak zwiększyć liczbę zapytań bez dokładania ruchu",
    "expected_status": "publish",
    "expected_slug": "formularz-kontaktowy-na-stronie-firmowej-jak-zwiekszyc-liczbe-zapytan",
    "expected_date": "2026-05-24 18:26:15",
    "expected_featured_id": 2419,
    "featured_id": 2419,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/formularz-kontaktowy-na-stronie-firmowej-1.webp",
    "facebook_image_id": 2419,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/formularz-kontaktowy-na-stronie-firmowej-1.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "consolidate_duplicate_labels_or_choose_primary",
    "rationale": "Tytuł i nagłówki opisują temat „SEO i konwersja”; zachować tę główną intencję."
  },
  "2424": {
    "category_slug": "strony-internetowe",
    "expected_title": "Oferta usług na stronie WWW — jak opisać pakiety, żeby klient szybciej rozumiał różnice i częściej pytał o wycenę",
    "expected_status": "publish",
    "expected_slug": "oferta-uslug-na-stronie-www-jak-opisac-pakiety",
    "expected_date": "2026-05-24 18:33:13",
    "expected_featured_id": 2427,
    "featured_id": 2427,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/oferta-uslug-na-stronie-www-1.webp",
    "facebook_image_id": 2427,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/oferta-uslug-na-stronie-www-1.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "consolidate_duplicate_labels_or_choose_primary",
    "rationale": "Tytuł i nagłówki opisują temat „Strony internetowe”; zachować tę główną intencję."
  },
  "2436": {
    "category_slug": "kampanie-reklamowe",
    "expected_title": "Remarketing dla małej firmy — kiedy ma sens i jak przygotować stronę, żeby odzyskiwać zainteresowanych klientów",
    "expected_status": "publish",
    "expected_slug": "remarketing-dla-malej-firmy-kiedy-ma-sens",
    "expected_date": "2026-05-24 18:40:42",
    "expected_featured_id": 2437,
    "featured_id": 2437,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/remarketing-dla-malej-firmy-1.webp",
    "facebook_image_id": 2437,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/remarketing-dla-malej-firmy-1.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "reassign_wrong_topic",
    "rationale": "Remarketing i ponowne dotarcie do odbiorców; kategoria logo nie odpowiada głównej intencji."
  },
  "2454": {
    "category_slug": "strony-internetowe",
    "expected_title": "Strona internetowa dla usług premium — jak projektować witrynę, która uzasadnia wyższą cenę",
    "expected_status": "publish",
    "expected_slug": "strona-internetowa-dla-uslug-premium-jak-uzasadnic-wyzsza-cene",
    "expected_date": "2026-05-24 18:52:23",
    "expected_featured_id": 2457,
    "featured_id": 2457,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/strona-internetowa-dla-uslug-premium-1-1.webp",
    "facebook_image_id": 2457,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/strona-internetowa-dla-uslug-premium-1-1.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "consolidate_duplicate_labels_or_choose_primary",
    "rationale": "Tytuł i nagłówki opisują temat „Strony internetowe”; zachować tę główną intencję."
  },
  "2464": {
    "category_slug": "strony-internetowe",
    "expected_title": "Audyt strony internetowej firmy — co sprawdzić przed reklamami i SEO, żeby nie przepalać budżetu",
    "expected_status": "publish",
    "expected_slug": "audyt-strony-internetowej-firmy-przed-reklamami-i-seo",
    "expected_date": "2026-05-24 18:55:53",
    "expected_featured_id": 2465,
    "featured_id": 2465,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/audyt-strony-internetowej-firmy-1.webp",
    "facebook_image_id": 2465,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/audyt-strony-internetowej-firmy-1.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "consolidate_duplicate_labels_or_choose_primary",
    "rationale": "Tytuł i nagłówki opisują temat „Strony internetowe”; zachować tę główną intencję."
  },
  "2484": {
    "category_slug": "strony-internetowe",
    "expected_title": "Strona internetowa dla lokalnej firmy – co powinna zawierać, żeby zdobywać klientów?",
    "expected_status": "publish",
    "expected_slug": "strona-internetowa-dla-firmy-z-katowic-co-powinna-zawierac-zeby-zdobywac-lokalnych-klientow",
    "expected_date": "2026-05-24 19:10:48",
    "expected_featured_id": 2487,
    "featured_id": 2487,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/strona-internetowa-dla-firmy-z-katowic-1.webp",
    "facebook_image_id": 2487,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/strona-internetowa-dla-firmy-z-katowic-1.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "reassign_wrong_topic",
    "rationale": "Strona lokalnej firmy, oferta i kontakt; obecnie tylko sklepy internetowe.",
    "seo_meta": {
      "rank_math_focus_keyword": "strona internetowa dla firmy z Katowic, strona internetowa dla lokalnej firmy",
      "rank_math_title": "Strona internetowa dla firmy z Katowic: oferta i kontakt",
      "rank_math_description": "Strona internetowa dla firmy z Katowic powinna pokazywać ofertę, obszar działania i opinie. Sprawdź, jakie sekcje pomagają zdobywać lokalne zapytania."
    },
    "expected_seo_meta": {
      "rank_math_focus_keyword": [
        "strona internetowa dla lokalnej firmy"
      ],
      "rank_math_title": [
        "Strona internetowa dla lokalnej firmy – co powinna zawierać?"
      ],
      "rank_math_description": [
        "Zobacz, co powinna zawierać strona internetowa firmy z Katowic: czytelną ofertę, lokalny kontekst, opinie, mocne CTA i łatwy kontakt z firmą."
      ]
    },
    "seo_meta_en": {
      "rank_math_title": "A website for a Katowice business: services and contact",
      "rank_math_description": "A website for a Katowice business should present its services, coverage area and reviews. Find out which sections help generate enquiries from local customers."
    }
  },
  "2492": {
    "category_slug": "seo-i-konwersja",
    "expected_title": "SEO lokalne Katowice – jak pozyskiwać klientów z miasta i okolic",
    "expected_status": "publish",
    "expected_slug": "seo-lokalne-w-katowicach-jak-pozyskiwac-klientow-z-miasta-i-okolic",
    "expected_date": "2026-05-24 19:14:20",
    "expected_featured_id": 2495,
    "featured_id": 2495,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/seo-lokalne-w-katowicach-jak-pozyskiwac-klientow-z-miasta-i-okolic-1-2.webp",
    "facebook_image_id": 2495,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/seo-lokalne-w-katowicach-jak-pozyskiwac-klientow-z-miasta-i-okolic-1-2.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "reassign_wrong_topic",
    "rationale": "Widoczność lokalna w Katowicach i pozyskiwanie klientów z miasta; nie budowa sklepu.",
    "seo_meta": {
      "rank_math_focus_keyword": "SEO lokalne w Katowicach, SEO lokalne Katowice",
      "rank_math_title": "SEO lokalne w Katowicach: jak pozyskiwać klientów z okolicy",
      "rank_math_description": "Sprawdź, jak SEO lokalne w Katowicach łączy stronę, Profil Firmy w Google, opinie i treści usługowe. Zaplanuj widoczność dla klientów z miasta i okolic."
    },
    "expected_seo_meta": {
      "rank_math_focus_keyword": [
        "SEO lokalne Katowice"
      ],
      "rank_math_title": [
        "SEO lokalne Katowice – jak zdobywać klientów z miasta"
      ],
      "rank_math_description": [
        "Sprawdź, jak SEO lokalne w Katowicach pomaga zdobywać klientów. Poznaj rolę strony, Profilu Firmy w Google, opinii i treści dopasowanych do usług."
      ]
    },
    "seo_meta_en": {
      "rank_math_title": "Local SEO in Katowice: how to reach customers nearby",
      "rank_math_description": "Find out how local SEO in Katowice connects your website, Google Business Profile, reviews and service content. Plan your visibility in the city and surrounding area."
    }
  },
  "2500": {
    "category_slug": "strony-internetowe",
    "expected_title": "Strona internetowa – czy lokalna agencja ma znaczenie?",
    "expected_status": "publish",
    "expected_slug": "lokalna-agencja-strony-internetowe",
    "expected_date": "2026-05-24 19:21:18",
    "expected_featured_id": 2503,
    "featured_id": 2503,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/strona-internetowa-czy-lokalna-agencja-ma-znaczenie-1.webp",
    "facebook_image_id": 2503,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/strona-internetowa-czy-lokalna-agencja-ma-znaczenie-1.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "consolidate_duplicate_labels_or_choose_primary",
    "rationale": "Tytuł i nagłówki opisują temat „Strony internetowe”; zachować tę główną intencję."
  },
  "2530": {
    "category_slug": "logo-branding",
    "expected_title": "Dlaczego profesjonalne logo ma znaczenie dla firmy: efekty",
    "expected_status": "publish",
    "expected_slug": "dlaczego-profesjonalne-logo-ma-znaczenie-dla-firmy",
    "expected_date": "2026-05-24 21:43:14",
    "expected_featured_id": 2534,
    "featured_id": 2534,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/dlaczego-profesjonalne-logo-ma-znaczenie-dla-firmy-1-2048x1536-1.webp",
    "facebook_image_id": 2534,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/dlaczego-profesjonalne-logo-ma-znaczenie-dla-firmy-1-2048x1536-1.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Logo i branding”; zachować tę główną intencję."
  },
  "2539": {
    "category_slug": "seo-i-konwersja",
    "expected_title": "Optymalizacja konwersji – co naprawdę ma znaczenie na stronie",
    "expected_status": "publish",
    "expected_slug": "strona-internetowa-a-konwersja",
    "expected_date": "2026-05-24 21:51:09",
    "expected_featured_id": 2545,
    "featured_id": 2545,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/strona-internetowa-a-konwersja-co-naprawde-ma-znaczenie-1.webp",
    "facebook_image_id": 2545,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/strona-internetowa-a-konwersja-co-naprawde-ma-znaczenie-1.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "consolidate_duplicate_labels_or_choose_primary",
    "rationale": "Tytuł i nagłówki opisują temat „SEO i konwersja”; zachować tę główną intencję.",
    "seo_meta": {
      "rank_math_focus_keyword": "strona internetowa a konwersja, optymalizacja konwersji, konwersja na stronie internetowej, jak zwiększyć konwersję na stronie",
      "rank_math_title": "Strona internetowa a konwersja: co poprawia wyniki?",
      "rank_math_description": "Strona internetowa a konwersja: sprawdź wpływ oferty, CTA, formularzy, UX i szybkości na zapytania. Zobacz, jak mierzyć zmiany w GA4 i planować poprawki."
    },
    "expected_seo_meta": {
      "rank_math_focus_keyword": [
        "optymalizacja konwersji, konwersja na stronie internetowej, jak zwiększyć konwersję na stronie"
      ],
      "rank_math_title": [
        "Optymalizacja konwersji strony – od czego zacząć"
      ],
      "rank_math_description": [
        "Strona internetowa a konwersja – sprawdź, co naprawdę wpływa na sprzedaż, zapytania i skuteczność strony: CTA, formularze, UX, mobile, szybkość i zaufanie."
      ]
    },
    "seo_meta_en": {
      "rank_math_title": "Website conversion: what improves business results?",
      "rank_math_description": "Explore how your offer, calls to action, forms, UX and page speed affect website enquiries. Learn how to measure changes in GA4 and plan improvements."
    }
  },
  "2550": {
    "category_slug": "strony-internetowe",
    "expected_title": "Brief strony internetowej – jak go przygotować do projektu",
    "expected_status": "publish",
    "expected_slug": "jak-przygotowac-brief-do-projektowania-strony-internetowej",
    "expected_date": "2026-05-24 21:55:09",
    "expected_featured_id": 2557,
    "featured_id": 2557,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/jak-przygotowac-brief-do-projektowania-strony-internetowej-1-2.webp",
    "facebook_image_id": 2557,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/jak-przygotowac-brief-do-projektowania-strony-internetowej-1-2.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "consolidate_duplicate_labels_or_choose_primary",
    "rationale": "Tytuł i nagłówki opisują temat „Strony internetowe”; zachować tę główną intencję.",
    "seo_meta": {
      "rank_math_focus_keyword": "brief do projektowania strony internetowej, brief strony internetowej, brief do strony internetowej",
      "rank_math_title": "Brief do projektowania strony internetowej: co przygotować",
      "rank_math_description": "Dobry brief do projektowania strony internetowej porządkuje cele, odbiorców, funkcje, SEO, budżet i terminy. Sprawdź, co opisać przed wyceną projektu."
    },
    "expected_seo_meta": {
      "rank_math_focus_keyword": [
        "brief strony internetowej, brief do strony internetowej"
      ],
      "rank_math_title": [
        "Brief strony internetowej – co przygotować przed projektem"
      ],
      "rank_math_description": [
        "Dowiedz się, jak przygotować brief do projektowania strony internetowej. Zobacz, co powinien zawierać brief, jak opisać cele, funkcje, SEO, budżet i terminy."
      ]
    },
    "seo_meta_en": {
      "rank_math_title": "A website design brief: what to prepare",
      "rank_math_description": "A good website design brief defines your goals, audience, features, SEO, budget and deadlines. Find out what to describe before requesting a project quote."
    }
  },
  "2564": {
    "category_slug": "logo-branding",
    "expected_title": "Jakie cechy powinno mieć dobre logo firmowe – kryteria",
    "expected_status": "publish",
    "expected_slug": "jakie-cechy-powinno-miec-dobre-logo-firmowe",
    "expected_date": "2026-05-24 21:58:24",
    "expected_featured_id": 2565,
    "featured_id": 2565,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/jakie-cechy-powinno-miec-dobre-logo-firmowe-kryteria-1-2048x1536-1.webp",
    "facebook_image_id": 2565,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/jakie-cechy-powinno-miec-dobre-logo-firmowe-kryteria-1-2048x1536-1.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Logo i branding”; zachować tę główną intencję."
  },
  "2573": {
    "category_slug": "kampanie-reklamowe",
    "expected_title": "Reklama na Facebooku dla firm lokalnych – co działa w Facebook Ads?",
    "expected_status": "publish",
    "expected_slug": "facebook-ads-dla-firm-lokalnych",
    "expected_date": "2026-05-24 22:01:55",
    "expected_featured_id": 2574,
    "featured_id": 2574,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/facebook-ads-dla-firm-lokalnych-co-dziala-najlepiej-1-2048x1536-1.webp",
    "facebook_image_id": 2574,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/facebook-ads-dla-firm-lokalnych-co-dziala-najlepiej-1-2048x1536-1.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "consolidate_duplicate_labels_or_choose_primary",
    "rationale": "Tytuł i nagłówki opisują temat „Kampanie reklamowe”; zachować tę główną intencję.",
    "seo_meta": {
      "rank_math_focus_keyword": "Facebook Ads dla firm lokalnych, reklama na Facebooku dla firm lokalnych, lokalne reklamy facebook, reklamy facebook lokalne, facebook reklamy lokalne, reklama lokalna na facebooku",
      "rank_math_title": "Facebook Ads dla firm lokalnych: cele, zasięg i pomiar",
      "rank_math_description": "Facebook Ads dla firm lokalnych: sprawdź, jak dobrać cel, obszar i budżet kampanii. Zaplanuj pomiar telefonów i wiadomości, aby ocenić jakość zapytań."
    },
    "expected_seo_meta": {
      "rank_math_focus_keyword": [
        "reklama na Facebooku dla firm lokalnych, lokalne reklamy facebook, reklamy facebook lokalne, facebook reklamy lokalne, reklama lokalna na facebooku"
      ],
      "rank_math_title": [
        "Reklama na Facebooku dla firm lokalnych – co działa"
      ],
      "rank_math_description": [
        "Lokalne reklamy na Facebooku dla małych firm: jaki cel kampanii wybrać, jak ustawić zasięg i budżet oraz jak liczyć telefony i wiadomości. Sprawdź, co działa."
      ]
    },
    "seo_meta_en": {
      "rank_math_title": "Facebook Ads for local businesses: goals, reach and measurement",
      "rank_math_description": "Facebook Ads for local businesses: learn how to choose a campaign goal, coverage area and budget. Measure calls and messages to assess the quality of enquiries."
    }
  },
  "2580": {
    "category_slug": "kampanie-reklamowe",
    "expected_title": "Jak zwiększyć sprzedaż dzięki Facebook Ads: cele i ROAS",
    "expected_status": "publish",
    "expected_slug": "jak-zwiekszyc-sprzedaz-dzieki-facebook-ads",
    "expected_date": "2026-05-24 22:06:25",
    "expected_featured_id": 2583,
    "featured_id": 2583,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/jak-zwiekszyc-sprzedaz-dzieki-facebook-ads-1-2048x1536-1.webp",
    "facebook_image_id": 2583,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/jak-zwiekszyc-sprzedaz-dzieki-facebook-ads-1-2048x1536-1.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "consolidate_duplicate_labels_or_choose_primary",
    "rationale": "Tytuł i nagłówki opisują temat „Kampanie reklamowe”; zachować tę główną intencję.",
    "seo_meta": {
      "rank_math_focus_keyword": "jak zwiększyć sprzedaż dzięki Facebook Ads, Facebook Ads dla sklepu online, kampanie sprzedażowe Facebook Ads, remarketing Facebook Ads sklep internetowy, ROAS w Facebook Ads",
      "rank_math_title": "Jak zwiększyć sprzedaż dzięki Facebook Ads w sklepie?",
      "rank_math_description": "Sprawdź, jak zwiększyć sprzedaż dzięki Facebook Ads w sklepie online. Zaplanuj pomiar, remarketing i odbiorców oraz oceniaj sprzedaż i ROAS."
    },
    "expected_seo_meta": {
      "rank_math_focus_keyword": [
        "jak zwiększyć sprzedaż dzięki Facebook Ads,Facebook Ads dla sklepu online,kampanie sprzedażowe Facebook Ads,remarketing Facebook Ads sklep internetowy,ROAS w Facebook Ads"
      ],
      "rank_math_title": [
        "Jak zwiększyć sprzedaż sklepu dzięki Facebook Ads?"
      ],
      "rank_math_description": [
        "Sprawdź, jak zwiększyć sprzedaż dzięki Facebook Ads w sklepie online. Zobacz, jak ustawić kampanie sprzedażowe, piksel, remarketing, grupy odbiorców i ROAS."
      ]
    },
    "seo_meta_en": {
      "rank_math_title": "How to increase online store sales with Facebook Ads",
      "rank_math_description": "Learn how to increase online store sales with Facebook Ads. Plan tracking, remarketing and audiences, then assess sales and return on ad spend."
    }
  },
  "2589": {
    "category_slug": "strony-internetowe",
    "expected_title": "Projektowanie stron www a obsługa klientów z całej Polski",
    "expected_status": "publish",
    "expected_slug": "projektowanie-stron-www-a-obsluga-klientow-z-calej-polski",
    "expected_date": "2026-05-24 22:10:39",
    "expected_featured_id": 2592,
    "featured_id": 2592,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/projektowanie-stron-www-a-obsluga-klienow-z-calej-polski-1-2048x1536-1.jpeg",
    "facebook_image_id": 2592,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/projektowanie-stron-www-a-obsluga-klienow-z-calej-polski-1-2048x1536-1.jpeg",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "consolidate_duplicate_labels_or_choose_primary",
    "rationale": "Tytuł i nagłówki opisują temat „Strony internetowe”; zachować tę główną intencję.",
    "seo_meta": {
      "rank_math_focus_keyword": "projektowanie stron WWW a obsługa klientów z całej Polski, współpraca online przy projektowaniu stron www, zdalne projektowanie stron internetowych, tworzenie stron internetowych dla klientów z całej Polski, obsługa klientów zdalnie przy tworzeniu strony",
      "rank_math_title": "Projektowanie stron WWW a obsługa klientów z całej Polski",
      "rank_math_description": "Projektowanie stron WWW a obsługa klientów z całej Polski: sprawdź zdalny proces, akceptacje, umowę i płatności bez spotkań na miejscu."
    },
    "expected_seo_meta": {
      "rank_math_focus_keyword": [
        "projektowanie stron www a obsługa klientów z całej Polski,współpraca online przy projektowaniu stron www,zdalne projektowanie stron internetowych,tworzenie stron internetowych dla klientów z całej Polski,obsługa klientów zdalnie przy tworzeniu strony"
      ],
      "rank_math_title": [
        "Projektowanie stron www zdalnie – współpraca online"
      ],
      "rank_math_description": [
        "Projektowanie stron www dla klientów z całej Polski: jak działa współpraca online, akceptacje, umowa, płatności i zdalna realizacja."
      ]
    },
    "seo_meta_en": {
      "rank_math_title": "Remote website design for clients across Poland",
      "rank_math_description": "Find out how remote website design works for clients across Poland. Review the project stages, approvals, contract and payments without in-person meetings."
    }
  },
  "2684": {
    "category_slug": "strony-internetowe",
    "expected_title": "Strona internetowa dla małej firmy: elementy konieczne",
    "expected_status": "publish",
    "expected_slug": "strona-internetowa-dla-malej-firmy",
    "expected_date": "2026-05-26 02:20:12",
    "expected_featured_id": 2685,
    "featured_id": 2685,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/strona-internetowa-dla-malej-fimry-elemnty-konieczne-1.webp",
    "facebook_image_id": 2685,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/strona-internetowa-dla-malej-fimry-elemnty-konieczne-1.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "consolidate_duplicate_labels_or_choose_primary",
    "rationale": "Tytuł i nagłówki opisują temat „Strony internetowe”; zachować tę główną intencję."
  },
  "2698": {
    "category_slug": "strony-internetowe",
    "expected_title": "Ile kosztuje strona internetowa dla firmy w 2026 roku?",
    "expected_status": "publish",
    "expected_slug": "ile-kosztuje-stworzenie-strony-internetowej-dla-firmy",
    "expected_date": "2026-05-26 02:31:52",
    "expected_featured_id": 2701,
    "featured_id": 2701,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/ile-kosztuje-stworzenie-strony-internetowej-dla-firmy-1.webp",
    "facebook_image_id": 2701,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/ile-kosztuje-stworzenie-strony-internetowej-dla-firmy-1.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "consolidate_duplicate_labels_or_choose_primary",
    "rationale": "Tytuł i nagłówki opisują temat „Strony internetowe”; zachować tę główną intencję.",
    "seo_meta": {
      "rank_math_focus_keyword": "ile kosztuje stworzenie strony internetowej, ile kosztuje strona internetowa, strony internetowe cena, strona internetowa cena, ile kosztuje zrobienie strony internetowej",
      "rank_math_title": "Ile kosztuje stworzenie strony internetowej dla firmy?",
      "rank_math_description": "Sprawdź, ile kosztuje stworzenie strony internetowej dla firmy. Porównaj projekt, treści, funkcje, integracje i utrzymanie przed wyborem oferty."
    },
    "expected_seo_meta": {
      "rank_math_focus_keyword": [
        "ile kosztuje strona internetowa, strony internetowe cena, strona internetowa cena, ile kosztuje zrobienie strony internetowej, ile kosztuje stworzenie strony internetowej"
      ],
      "rank_math_title": [
        "Ile kosztuje strona internetowa w 2026? Ceny i przykłady"
      ],
      "rank_math_description": [
        "Ile kosztuje strona internetowa w 2026? Strony dla firm od 3 999 zł: od czego zależy koszt projektu i ile kosztuje utrzymanie strony."
      ]
    },
    "seo_meta_en": {
      "rank_math_title": "How much does it cost to build a business website?",
      "rank_math_description": "Find out how much it costs to build a business website. Compare design, content, features, integrations and maintenance before choosing a proposal."
    }
  },
  "2706": {
    "category_slug": "seo-i-konwersja",
    "expected_title": "Jak sprawdzić czy strona internetowa działa skutecznie",
    "expected_status": "publish",
    "expected_slug": "jak-sprawdzic-czy-strona-internetowa-dziala-skutecznie",
    "expected_date": "2026-05-26 02:38:35",
    "expected_featured_id": 2709,
    "featured_id": 2709,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/jak-sprawdzic-czy-strona-internetowa-dziala-sktecznie-1.webp",
    "facebook_image_id": 2709,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/jak-sprawdzic-czy-strona-internetowa-dziala-sktecznie-1.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "primary_refinement",
    "rationale": "Ocena celów, konwersji, GA4, Search Console i skuteczności; główny temat to pomiar i optymalizacja."
  },
  "2714": {
    "category_slug": "logo-branding",
    "expected_title": "Jak zrobić logo firmy? Od czego zacząć i czego unikać",
    "expected_status": "publish",
    "expected_slug": "projekt-logo-firmy-od-czego-zaczac-i-czego-unikac",
    "expected_date": "2026-05-26 02:41:49",
    "expected_featured_id": 2717,
    "featured_id": 2717,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/projekt-logo-firmy-od-czego-zaczac-i-czego-unikac-1-2048x1536-1.jpeg",
    "facebook_image_id": 2717,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/projekt-logo-firmy-od-czego-zaczac-i-czego-unikac-1-2048x1536-1.jpeg",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Logo i branding”; zachować tę główną intencję.",
    "seo_meta": {
      "rank_math_focus_keyword": "projekt logo firmy, jak zrobić logo firmy, jak stworzyć logo firmy, jak zrobić logo, jak stworzyć logo",
      "rank_math_title": "Projekt logo firmy: od briefu do gotowych plików",
      "rank_math_description": "Projekt logo firmy zacznij od marki, odbiorców i zastosowań znaku. Sprawdź, jak przygotować brief, ocenić koncepcje i odebrać pliki do internetu oraz druku."
    },
    "expected_seo_meta": {
      "rank_math_focus_keyword": [
        "jak zrobić logo firmy, jak stworzyć logo firmy, jak zrobić logo, jak stworzyć logo"
      ],
      "rank_math_title": [
        "Jak zrobić logo firmy? Od czego zacząć i czego unikać"
      ],
      "rank_math_description": [
        "Sprawdź, jak zacząć projekt logo firmy. Zobacz, jak przygotować brief, przeanalizować markę, dobrać kolory i typografię oraz uniknąć błędów przy tworzeniu logo."
      ]
    },
    "seo_meta_en": {
      "rank_math_title": "Business logo design: from the brief to final files",
      "rank_math_description": "Start your business logo design with your brand, audience and use cases. Learn how to prepare a brief, assess concepts and receive files for web and print."
    }
  },
  "2722": {
    "category_slug": "sklepy-internetowe",
    "expected_title": "Projektowanie sklepu internetowego a doświadczenie klienta (UX)",
    "expected_status": "publish",
    "expected_slug": "projektowanie-sklepu-internetowego-a-doswiadczenie-klienta",
    "expected_date": "2026-05-26 02:45:11",
    "expected_featured_id": 2725,
    "featured_id": 2725,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/projekt-sklepu-internetowego-a-doswiadczenie-klienta-UX-1-2048x1536-1.webp",
    "facebook_image_id": 2725,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/projekt-sklepu-internetowego-a-doswiadczenie-klienta-UX-1-2048x1536-1.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "primary_refinement",
    "rationale": "UX procesu zakupowego sklepu; kategorie, produkty, koszyk i doświadczenie klienta e-commerce.",
    "seo_meta": {
      "rank_math_focus_keyword": "doświadczenie klienta, projektowanie sklepu internetowego, projektowanie sklepu internetowego a doświadczenie klienta, UX sklepu internetowego, doświadczenie klienta w sklepie internetowym, projekt interfejsu sklepu online",
      "rank_math_title": "Doświadczenie klienta w sklepie: jak projekt UX ułatwia zakup",
      "rank_math_description": "Doświadczenie klienta w sklepie zależy od nawigacji, kart produktów, koszyka i płatności. Sprawdź, jak projekt UX pomaga wybrać produkt i sfinalizować zakup."
    },
    "expected_seo_meta": {
      "rank_math_focus_keyword": [
        "projektowanie sklepu internetowego, projektowanie sklepu internetowego a doświadczenie klienta, UX sklepu internetowego, doświadczenie klienta w sklepie internetowym, projekt interfejsu sklepu online"
      ],
      "rank_math_title": [
        "Projektowanie sklepu internetowego a doświadczenie klienta"
      ],
      "rank_math_description": [
        "Projektowanie sklepu internetowego a doświadczenie klienta: jak UX, checkout, mobile i karty produktów wpływają na konwersję."
      ]
    },
    "seo_meta_en": {
      "rank_math_title": "The store customer experience: how UX design makes buying easier",
      "rank_math_description": "The store customer experience depends on navigation, product pages, the cart and payments. Find out how UX design helps customers choose products and complete purchases."
    }
  },
  "2794": {
    "category_slug": "seo-i-konwersja",
    "expected_title": "Jakie błędy obniżają skuteczność strony firmowej – lista",
    "expected_status": "publish",
    "expected_slug": "jakie-bledy-obnizaja-skutecznosc-strony-firmowej",
    "expected_date": "2026-05-27 02:38:44",
    "expected_featured_id": 2798,
    "featured_id": 2798,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/jakie-bledy-obnizaja-skutecznosc-strony-internetowej-lista-1.webp",
    "facebook_image_id": 2798,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/jakie-bledy-obnizaja-skutecznosc-strony-internetowej-lista-1.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "consolidate_duplicate_labels_or_choose_primary",
    "rationale": "Tytuł i nagłówki opisują temat „SEO i konwersja”; zachować tę główną intencję.",
    "seo_meta": {
      "rank_math_focus_keyword": "skuteczność strony, jakie błędy obniżają skuteczność strony firmowej, błędy na stronie firmowej, problemy z konwersją na stronie internetowej",
      "rank_math_title": "Skuteczność strony firmowej: błędy, które warto sprawdzić",
      "rank_math_description": "Sprawdź, co obniża skuteczność strony firmowej: niejasna oferta, UX, szybkość i formularze. Zobacz, jak rozpoznać problemy i ustalić kolejność poprawek."
    },
    "expected_seo_meta": {
      "rank_math_focus_keyword": [
        "jakie błędy obniżają skuteczność strony firmowej,błędy na stronie firmowej,problemy z konwersją na stronie internetowej"
      ],
      "rank_math_title": [
        "Błędy, które obniżają skuteczność strony firmowej"
      ],
      "rank_math_description": [
        "Błędy, które obniżają skuteczność strony firmowej: UX, oferta, SEO, szybkość, formularz i analityka. Zobacz, jak je znaleźć."
      ]
    },
    "seo_meta_en": {
      "rank_math_title": "Business website effectiveness: mistakes worth checking",
      "rank_math_description": "Find out what reduces business website effectiveness: an unclear offer, UX issues, slow pages and forms. Learn how to identify problems and prioritise improvements."
    }
  },
  "2803": {
    "category_slug": "logo-branding",
    "expected_title": "Branding firmy od podstaw – co to jest i kiedy jest potrzebny",
    "expected_status": "publish",
    "expected_slug": "branding-firmy-od-podstaw",
    "expected_date": "2026-05-27 08:59:48",
    "expected_featured_id": 2725,
    "featured_id": 2804,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/branding-firmy-od-podstaw-kiedy-jest-potrzebny-sygnaly-2.webp",
    "facebook_image_id": 2804,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/branding-firmy-od-podstaw-kiedy-jest-potrzebny-sygnaly-2.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "Branding firmy od podstaw — sygnały, że obecny wizerunek nie nadąża za rozwojem biznesu",
    "featured_alt_source": "Existing approved inline image ALT in this exact article",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Logo i branding”; zachować tę główną intencję."
  },
  "2812": {
    "category_slug": "logo-branding",
    "expected_title": "Rebranding — co to jest, kiedy ma sens i jak zaplanować zmianę?",
    "expected_status": "publish",
    "expected_slug": "rebranding-firmy",
    "expected_date": "2026-05-27 09:04:52",
    "expected_featured_id": 2813,
    "featured_id": 2813,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/rebranding-firmy-sygnaly-ze-logo-wymaga-zmiany-1.webp",
    "facebook_image_id": 2813,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/rebranding-firmy-sygnaly-ze-logo-wymaga-zmiany-1.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Logo i branding”; zachować tę główną intencję."
  },
  "2818": {
    "category_slug": "strony-internetowe",
    "expected_title": "Przebudowa strony internetowej firmy – kiedy warto?",
    "expected_status": "publish",
    "expected_slug": "kiedy-warto-przebudowac-strone-internetowa-firmy",
    "expected_date": "2026-05-27 09:09:20",
    "expected_featured_id": 2823,
    "featured_id": 2823,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/kiedy-warto-przebudowac-strone-inernetowa-firmy-1.webp",
    "facebook_image_id": 2823,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/kiedy-warto-przebudowac-strone-inernetowa-firmy-1.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "consolidate_duplicate_labels_or_choose_primary",
    "rationale": "Tytuł i nagłówki opisują temat „Strony internetowe”; zachować tę główną intencję.",
    "seo_meta": {
      "rank_math_focus_keyword": "przebudowa strony, przebudowa strony internetowej, modernizacja strony internetowej, redesign strony internetowej, odświeżenie strony internetowej, przebudowa strony www",
      "rank_math_title": "Przebudowa strony firmy: kiedy warto i jak podjąć decyzję",
      "rank_math_description": "Przebudowa strony wymaga oceny oferty, UX, technologii i wyników. Sprawdź sygnały do zmiany oraz sposób planowania zakresu i priorytetów projektu."
    },
    "expected_seo_meta": {
      "rank_math_focus_keyword": [
        "przebudowa strony internetowej, modernizacja strony internetowej, redesign strony internetowej, odświeżenie strony internetowej, przebudowa strony www"
      ],
      "rank_math_title": [
        "Przebudowa strony internetowej – kiedy warto ją zrobić"
      ],
      "rank_math_description": [
        "Kiedy warto przebudować stronę internetową firmy? Sygnały, że wystarczy lifting, i takie, które wymagają nowej struktury, CMS i SEO."
      ]
    },
    "seo_meta_en": {
      "rank_math_title": "Redesigning a business website: when and how to decide",
      "rank_math_description": "A website redesign requires an assessment of your offer, UX, technology and results. Recognise the signs that change is needed and plan the scope and priorities."
    }
  },
  "2828": {
    "category_slug": "sklepy-internetowe",
    "expected_title": "Sklep internetowy na zamówienie: kiedy lepszy niż szablon",
    "expected_status": "publish",
    "expected_slug": "sklep-internetowy-na-zamowienie",
    "expected_date": "2026-05-27 09:13:25",
    "expected_featured_id": 2831,
    "featured_id": 2831,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/sklep-internetowy-na-zamowienie-kiedy-lepszy-niz-szablon-1-2048x1536-1.webp",
    "facebook_image_id": 2831,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/sklep-internetowy-na-zamowienie-kiedy-lepszy-niz-szablon-1-2048x1536-1.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Sklepy internetowe”; zachować tę główną intencję."
  },
  "2838": {
    "category_slug": "kampanie-reklamowe",
    "expected_title": "Ile kosztuje reklama na Facebooku? Budżet, obsługa i kreacje",
    "expected_status": "publish",
    "expected_slug": "ile-kosztuje-reklama-na-facebooku",
    "expected_date": "2026-05-27 09:17:30",
    "expected_featured_id": 2839,
    "featured_id": 2839,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/ile-kosztuje-reklama-na-facebooku-dla-firmy-widelki-1.webp",
    "facebook_image_id": 2839,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/ile-kosztuje-reklama-na-facebooku-dla-firmy-widelki-1.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "consolidate_duplicate_labels_or_choose_primary",
    "rationale": "Tytuł i nagłówki opisują temat „Kampanie reklamowe”; zachować tę główną intencję."
  },
  "2844": {
    "category_slug": "kampanie-reklamowe",
    "expected_title": "Reklama na Instagramie a Facebook Ads – kiedy kampanie Instagram Ads działają lepiej?",
    "expected_status": "publish",
    "expected_slug": "reklama-na-instagramie-a-facebook-ads",
    "expected_date": "2026-05-27 09:24:41",
    "expected_featured_id": 2847,
    "featured_id": 2847,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/reklama-na-instagramie-a-facebook-ads-roznice-1.webp",
    "facebook_image_id": 2847,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/reklama-na-instagramie-a-facebook-ads-roznice-1.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "consolidate_duplicate_labels_or_choose_primary",
    "rationale": "Tytuł i nagłówki opisują temat „Kampanie reklamowe”; zachować tę główną intencję.",
    "seo_meta": {
      "rank_math_focus_keyword": "reklama na Instagramie, kampanie instagram ads, kampania instagram ads, reklama instagram ads, kampanie reklamowe instagram, reklama na instagramie czy warto",
      "rank_math_title": "Reklama na Instagramie a Facebook Ads: różnice i wyniki",
      "rank_math_description": "Reklama na Instagramie i Facebook Ads różnią się kontekstem odbioru, formatami i kreacjami. Sprawdź, jak dobrać kanał do celu oraz porównywać wyniki."
    },
    "expected_seo_meta": {
      "rank_math_focus_keyword": [
        "kampanie instagram ads, kampania instagram ads, reklama instagram ads, kampanie reklamowe instagram, reklama na instagramie czy warto"
      ],
      "rank_math_title": [
        "Kampanie Instagram Ads a Facebook Ads – różnice i wyniki"
      ],
      "rank_math_description": [
        "Kampanie Instagram Ads i Facebook Ads działają w systemie Meta, ale różnią się formatami, odbiorcami i kosztem. Sprawdź, czy reklama na Instagramie się opłaca."
      ]
    },
    "seo_meta_en": {
      "rank_math_title": "Instagram advertising and Facebook Ads: differences and results",
      "rank_math_description": "Instagram advertising and Facebook Ads differ in audience context, formats and creative. Learn how to choose a channel for your goal and compare results."
    }
  },
  "2860": {
    "category_slug": "sklepy-internetowe",
    "expected_title": "Jakie funkcje sklepu internetowego zwiększają sprzedaż",
    "expected_status": "publish",
    "expected_slug": "jakie-funkcje-sklepu-internetowego-zwiekszaja-sprzedaz",
    "expected_date": "2026-05-27 09:32:53",
    "expected_featured_id": 2863,
    "featured_id": 2863,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/jakie-funkcje-sklepu-internetowego-zwiekszaja-sprzedaz-1.webp",
    "facebook_image_id": 2863,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/jakie-funkcje-sklepu-internetowego-zwiekszaja-sprzedaz-1.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Sklepy internetowe”; zachować tę główną intencję.",
    "seo_meta": {
      "rank_math_focus_keyword": "funkcje sklepu internetowego, jakie funkcje sklepu internetowego zwiększają sprzedaż, współczynnik konwersji sklepu, marketing automation w e-commerce, karta produktu a sprzedaż",
      "rank_math_title": "Funkcje sklepu internetowego, które pomagają sprzedawać",
      "rank_math_description": "Sprawdź, jakie funkcje sklepu internetowego wspierają zakup: wyszukiwarka, karty produktów, płatności i dostawy. Ustal priorytety wdrożenia oraz testów."
    },
    "expected_seo_meta": {
      "rank_math_focus_keyword": [
        "jakie funkcje sklepu internetowego zwiększają sprzedaż,funkcje sklepu internetowego,współczynnik konwersji sklepu,marketing automation w e-commerce,karta produktu a sprzedaż"
      ],
      "rank_math_title": [
        "Funkcje sklepu internetowego, które zwiększają sprzedaż"
      ],
      "rank_math_description": [
        "Funkcje sklepu internetowego, które zwiększają sprzedaż: checkout, płatności, dostawy, karta produktu, wyszukiwarka i opinie."
      ]
    },
    "seo_meta_en": {
      "rank_math_title": "Online store features that help customers buy",
      "rank_math_description": "Explore online store features that support purchases: search, product pages, payments and delivery. Set priorities for implementation and testing."
    }
  },
  "2870": {
    "category_slug": "strony-internetowe",
    "expected_title": "Projektowanie stron www z myślą o użytkowniku – podstawy UX",
    "expected_status": "publish",
    "expected_slug": "projektowanie-stron-www-z-mysla-o-uzytkowniku",
    "expected_date": "2026-05-27 09:36:45",
    "expected_featured_id": 2871,
    "featured_id": 2871,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/podstawy-ux-strony-www-z-mysla-o-uzytkowaniu-1.webp",
    "facebook_image_id": 2871,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/podstawy-ux-strony-www-z-mysla-o-uzytkowaniu-1.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "primary_refinement",
    "rationale": "Projektowanie nawigacji, hierarchii, mobile i interakcji strony WWW; UX zachować jako temat szczegółowy."
  },
  "2878": {
    "category_slug": "kampanie-reklamowe",
    "expected_title": "Najczęstsze błędy w kampaniach Facebook i Instagram",
    "expected_status": "publish",
    "expected_slug": "kampanie-reklamowe-facebook-i-instagram-najczestsze-bledy",
    "expected_date": "2026-05-27 09:41:19",
    "expected_featured_id": 2879,
    "featured_id": 2879,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/kampanie-reklamowe-fb-i-instagram-najczestsze-bledy-1-2048x1536-1.webp",
    "facebook_image_id": 2879,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/kampanie-reklamowe-fb-i-instagram-najczestsze-bledy-1-2048x1536-1.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "consolidate_duplicate_labels_or_choose_primary",
    "rationale": "Tytuł i nagłówki opisują temat „Kampanie reklamowe”; zachować tę główną intencję.",
    "seo_meta": {
      "rank_math_focus_keyword": "kampanie reklamowe Facebook i Instagram, błędy w kampaniach Facebook i Instagram, kampanie reklamowe Facebook i Instagram najczęstsze błędy, błędy w Meta Ads, optymalizacja kampanii Facebook Ads, błędy targetowania reklam",
      "rank_math_title": "Kampanie reklamowe Facebook i Instagram: błędy i ich skutki",
      "rank_math_description": "Kampanie reklamowe Facebook i Instagram wymagają spójnego celu, pomiaru i oferty. Sprawdź błędy w kreacjach, ustawieniach oraz stronie docelowej."
    },
    "expected_seo_meta": {
      "rank_math_focus_keyword": [
        "błędy w kampaniach Facebook i Instagram, kampanie reklamowe Facebook i Instagram najczęstsze błędy, błędy w Meta Ads, optymalizacja kampanii Facebook Ads, błędy targetowania reklam"
      ],
      "rank_math_title": [
        "Błędy w kampaniach Facebook i Instagram – jak ich unikać"
      ],
      "rank_math_description": [
        "Najczęstsze błędy w kampaniach Facebook i Instagram: cel kampanii, piksel, kreacje, budżet i targetowanie. Zobacz, jak ich uniknąć."
      ]
    },
    "seo_meta_en": {
      "rank_math_title": "Facebook and Instagram campaigns: mistakes and their effects",
      "rank_math_description": "Facebook and Instagram ad campaigns need a consistent goal, measurement and offer. Check for mistakes in creative, settings and the landing page."
    }
  },
  "2884": {
    "category_slug": "logo-branding",
    "expected_title": "Identyfikacja wizualna firmy – co to jest i co zawiera",
    "expected_status": "publish",
    "expected_slug": "identyfikacja-wizualna-firmy",
    "expected_date": "2026-05-27 09:47:17",
    "expected_featured_id": 2887,
    "featured_id": 2887,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/identyfikacja-wizualna-firmy-co-powinna-zawierac-1-2048x1536-1.webp",
    "facebook_image_id": 2887,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/identyfikacja-wizualna-firmy-co-powinna-zawierac-1-2048x1536-1.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Logo i branding”; zachować tę główną intencję."
  },
  "2892": {
    "category_slug": "kampanie-reklamowe",
    "expected_title": "Cele kampanii Facebook Ads – jakie wybierać na początku",
    "expected_status": "publish",
    "expected_slug": "kampanie-facebook-ads-jakie-cele-wybierac-na-poczatku",
    "expected_date": "2026-05-27 09:52:21",
    "expected_featured_id": 2895,
    "featured_id": 2895,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/kampanie-facebook-ads-jakie-cele-wybrac-na-poczatku-1-2048x1536-1.webp",
    "facebook_image_id": 2895,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/kampanie-facebook-ads-jakie-cele-wybrac-na-poczatku-1-2048x1536-1.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "consolidate_duplicate_labels_or_choose_primary",
    "rationale": "Tytuł i nagłówki opisują temat „Kampanie reklamowe”; zachować tę główną intencję.",
    "seo_meta": {
      "rank_math_focus_keyword": "kampanie Facebook Ads, cele kampanii Facebook Ads, kampanie Facebook Ads – jakie cele wybierać na początku, konfiguracja kampanii Facebook Ads, kampanie Meta Ads na start, jaki cel kampanii Facebook Ads wybrać",
      "rank_math_title": "Kampanie Facebook Ads: jakie cele wybierać na początku?",
      "rank_math_description": "Kampanie Facebook Ads zacznij od celu biznesowego i pomiaru. Porównaj ruch, kontakty oraz sprzedaż i sprawdź, kiedy poszczególne cele mają sens dla firmy."
    },
    "expected_seo_meta": {
      "rank_math_focus_keyword": [
        "cele kampanii Facebook Ads, kampanie Facebook Ads – jakie cele wybierać na początku, konfiguracja kampanii Facebook Ads, kampanie Meta Ads na start, jaki cel kampanii Facebook Ads wybrać"
      ],
      "rank_math_title": [
        "Cele kampanii Facebook Ads – co wybrać na początku"
      ],
      "rank_math_description": [
        "Kampanie Facebook Ads – jakie cele wybierać na początku? Sprawdź, kiedy postawić na ruch, wiadomości, kontakty lub sprzedaż i jak dopasować cel do biznesu."
      ]
    },
    "seo_meta_en": {
      "rank_math_title": "Facebook Ads campaigns: which goals should you start with?",
      "rank_math_description": "Start Facebook Ads campaigns with a business goal and measurement. Compare traffic, leads and sales, and find out when each objective makes sense."
    }
  },
  "2902": {
    "category_slug": "logo-branding",
    "expected_title": "Projektowanie logo a współpraca zdalna – proces i zasady",
    "expected_status": "publish",
    "expected_slug": "projektowanie-logo-a-wspolpraca-zdalna",
    "expected_date": "2026-05-27 09:58:00",
    "expected_featured_id": 2903,
    "featured_id": 2903,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/projektowanie-logo-a-wspolpraca-zdalna-proces-i-zasady-1.webp",
    "facebook_image_id": 2903,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/05/projektowanie-logo-a-wspolpraca-zdalna-proces-i-zasady-1.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Logo i branding”; zachować tę główną intencję.",
    "seo_meta": {
      "rank_math_focus_keyword": "projektowanie logo a współpraca zdalna, projekt logo online, współpraca zdalna z grafikiem, brief do logo, zdalne projektowanie logo",
      "rank_math_title": "Projektowanie logo a współpraca zdalna: proces i zasady",
      "rank_math_description": "Projektowanie logo a współpraca zdalna: sprawdź etapy, brief, feedback i akceptacje. Ustal poprawki, prawa autorskie oraz odbiór gotowych plików."
    },
    "expected_seo_meta": {
      "rank_math_focus_keyword": [
        "projektowanie logo a współpraca zdalna,projekt logo online,współpraca zdalna z grafikiem,brief do logo,zdalne projektowanie logo"
      ],
      "rank_math_title": [
        "Projektowanie logo zdalnie – jak wygląda współpraca"
      ],
      "rank_math_description": [
        "Projektowanie logo zdalnie: brief, feedback, poprawki, umowa, prawa autorskie i pliki końcowe. Cały proces bez spotkań na żywo."
      ]
    },
    "seo_meta_en": {
      "rank_math_title": "Remote logo design: process and collaboration rules",
      "rank_math_description": "Explore the stages, brief, feedback and approvals involved in remote logo design. Agree on revisions, usage rights and the handover of final files."
    }
  },
  "3154": {
    "category_slug": "logo-branding",
    "expected_title": "Logo, branding i strona – jaka kolejność wdrożenia ma sens?",
    "expected_status": "publish",
    "expected_slug": "logo-branding-i-strona-jaka-kolejnosc-wdrozenia-ma-sens",
    "expected_date": "2026-06-01 20:33:02",
    "expected_featured_id": 3175,
    "featured_id": 3175,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/06/logo-branding-i-strona-1.webp",
    "facebook_image_id": 3175,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/06/logo-branding-i-strona-1.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Logo i branding”; zachować tę główną intencję."
  },
  "3193": {
    "category_slug": "strony-internetowe",
    "expected_title": "Struktura podstrony usługowej premium – jak opisać ofertę?",
    "expected_status": "publish",
    "expected_slug": "struktura-podstrony-uslugowej-premium",
    "expected_date": "2026-06-01 21:29:43",
    "expected_featured_id": 3197,
    "featured_id": 3197,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/06/struktura-podstrony-uslugi-premium-1.webp",
    "facebook_image_id": 3197,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/06/struktura-podstrony-uslugi-premium-1.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "consolidate_duplicate_labels_or_choose_primary",
    "rationale": "Struktura oferty premium, treść, zakres i CTA na podstronie usługowej; konwersja jako temat wspierający.",
    "seo_meta": {
      "rank_math_title": "Struktura podstrony usługowej premium: 6 kroków do lepszej oferty",
      "rank_math_description": "Struktura podstrony usługowej powinna wyjaśniać ofertę, proces i kolejne kroki. Sprawdź korzyści, dowody, zakres oraz kontakt dla usług premium."
    },
    "expected_seo_meta": {
      "rank_math_title": [
        "Struktura podstrony usługowej premium – jak opisać ofertę"
      ],
      "rank_math_description": [
        "Sprawdź, jak powinna wyglądać struktura podstrony usługi premium: sekcje, dowody zaufania, proces, zakres, CTA, formularz i treści wspierające zapytania."
      ]
    },
    "seo_meta_en": {
      "rank_math_title": "A premium service page structure: 6 steps to a clearer offer",
      "rank_math_description": "A service page structure should explain the offer, process and next steps. Review benefits, evidence, scope and contact options for premium services."
    }
  },
  "3336": {
    "category_slug": "seo-i-konwersja",
    "expected_title": "Jak połączyć branding i SEO, stronę i reklamy w jeden system?",
    "expected_status": "publish",
    "expected_slug": "jak-polaczyc-branding-strone-reklamy-i-seo-w-jeden-system",
    "expected_date": "2026-06-02 21:20:41",
    "expected_featured_id": 3337,
    "featured_id": 3337,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/06/jak-polaczyc-branding-strone-reklamy-i-seo-1.webp",
    "facebook_image_id": 3337,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/06/jak-polaczyc-branding-strone-reklamy-i-seo-1.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "consolidate_duplicate_labels_or_choose_primary",
    "rationale": "Przekrojowy system marki, strony, reklam, SEO i pomiaru; nie ograniczać wyłącznie do jednej usługi.",
    "seo_meta": {
      "rank_math_focus_keyword": "jak połączyć branding, branding i SEO, branding strona reklamy SEO, system sprzedażowy online, jak połączyć stronę i reklamy, spójny marketing firmy",
      "rank_math_title": "Jak połączyć branding, stronę, reklamy i SEO w jeden system?",
      "rank_math_description": "Sprawdź, jak połączyć branding, stronę, reklamy i SEO. Uporządkuj markę, ofertę, ruch i pomiar, aby działania wspierały pozyskiwanie klientów."
    },
    "expected_seo_meta": {
      "rank_math_focus_keyword": [
        "branding i SEO, branding strona reklamy SEO, system sprzedażowy online, jak połączyć stronę i reklamy, spójny marketing firmy"
      ],
      "rank_math_title": [
        "Branding i SEO, strona i reklamy w jednym systemie"
      ],
      "rank_math_description": [
        "Branding, strona, reklamy i SEO działają najlepiej wtedy, gdy są projektowane jako jeden system. Marka buduje zaufanie, strona porządkuje ofertę i kon."
      ]
    },
    "seo_meta_en": {
      "rank_math_title": "How to connect branding, your website, advertising and SEO",
      "rank_math_description": "Learn how to connect branding, your website, advertising and SEO. Align your brand, offer, traffic and measurement to support customer acquisition."
    }
  },
  "3352": {
    "category_slug": "sklepy-internetowe",
    "expected_title": "Porzucone koszyki – jak je ograniczyć w sklepie internetowym?",
    "expected_status": "publish",
    "expected_slug": "koszyk-w-sklepie-internetowym-jak-ograniczyc-porzucone-zakupy",
    "expected_date": "2026-06-02 22:14:15",
    "expected_featured_id": 3353,
    "featured_id": 3353,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/06/koszyk-w-sklepie-internetowym-1.webp",
    "facebook_image_id": 3353,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/06/koszyk-w-sklepie-internetowym-1.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Sklepy internetowe”; zachować tę główną intencję.",
    "seo_meta": {
      "rank_math_focus_keyword": "koszyk w sklepie internetowym, porzucone koszyki, porzucony koszyk, porzucone koszyki woocommerce",
      "rank_math_title": "Koszyk w sklepie internetowym: jak ograniczyć porzucone zakupy",
      "rank_math_description": "Koszyk w sklepie internetowym powinien jasno pokazywać koszt i warunki zakupu. Sprawdź dostawy, płatności, formularze oraz pomiar porzuceń."
    },
    "expected_seo_meta": {
      "rank_math_focus_keyword": [
        "porzucone koszyki, porzucony koszyk, porzucone koszyki woocommerce"
      ],
      "rank_math_title": [
        "Porzucone koszyki – jak je ograniczyć w sklepie internetowym"
      ],
      "rank_math_description": [
        "Porzucone koszyki pojawiają się wtedy, gdy klient dodaje produkt, ale rezygnuje przed finalizacją zakupu. Najczęstsze przyczyny to nieoczekiwane koszt."
      ]
    },
    "seo_meta_en": {
      "rank_math_title": "The online store cart: how to reduce abandoned purchases",
      "rank_math_description": "An online store cart should clearly show costs and purchase terms. Check delivery, payments, forms and abandonment tracking."
    }
  },
  "3360": {
    "category_slug": "strony-internetowe",
    "expected_title": "Jakie sekcje strony salonu beauty zwiększają liczbę zapytań?",
    "expected_status": "publish",
    "expected_slug": "strona-internetowa-dla-branzy-beauty-jakie-sekcje-zwiekszaja-zapytania",
    "expected_date": "2026-06-02 22:31:59",
    "expected_featured_id": 3361,
    "featured_id": 3361,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/06/strona-internetowa-dla-branzy-beauty-1.webp",
    "facebook_image_id": 3361,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/06/strona-internetowa-dla-branzy-beauty-1.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "reassign_wrong_topic",
    "rationale": "Sekcje strony salonu beauty i pozyskiwanie zapytań; nie katalog, koszyk i checkout.",
    "seo_meta": {
      "rank_math_focus_keyword": "strona internetowa dla branży beauty, sekcje strony salonu beauty",
      "rank_math_title": "Strona internetowa dla branży beauty: sekcje i zaufanie",
      "rank_math_description": "Strona internetowa dla branży beauty łączy ofertę, efekty, kwalifikacje i opinie. Sprawdź, jak ułatwić kontakt oraz rezerwację bez agresywnej sprzedaży."
    },
    "expected_seo_meta": {
      "rank_math_focus_keyword": [
        "sekcje strony salonu beauty"
      ],
      "rank_math_title": [
        "Sekcje strony salonu beauty – co zwiększa liczbę zapytań?"
      ],
      "rank_math_description": [
        "Strona internetowa dla branży beauty powinna łączyć estetykę z zaufaniem: jasno pokazywać zabiegi, efekty, kwalifikacje, przeciwwskazania, opinie i pr."
      ]
    },
    "seo_meta_en": {
      "rank_math_title": "A beauty business website: sections and trust",
      "rank_math_description": "A beauty business website brings together services, results, qualifications and reviews. Learn how to make contact and booking easier without aggressive selling."
    }
  },
  "3368": {
    "category_slug": "strony-internetowe",
    "expected_title": "Strona internetowa dla firmy budowlanej — jak pokazać realizacje i zdobywać wyceny?",
    "expected_status": "publish",
    "expected_slug": "strona-internetowa-dla-firmy-budowlanej-jak-pokazac-realizacje-i-zdobywac-wyceny",
    "expected_date": "2026-06-02 22:48:52",
    "expected_featured_id": 3371,
    "featured_id": 3371,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/06/strona-internetowa-dla-fimry-budowlanej-1.webp",
    "facebook_image_id": 3371,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/06/strona-internetowa-dla-fimry-budowlanej-1.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "reassign_wrong_topic",
    "rationale": "Strona firmy budowlanej z realizacjami i zapytaniem o wycenę; nie proces sprzedaży sklepu."
  },
  "3376": {
    "category_slug": "sklepy-internetowe",
    "expected_title": "Ile kosztuje sklep internetowy na WooCommerce w 2026 roku?",
    "expected_status": "publish",
    "expected_slug": "ile-kosztuje-sklep-internetowy-woocommerce-w-2026-roku",
    "expected_date": "2026-06-03 21:51:43",
    "expected_featured_id": 4797,
    "featured_id": 4797,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/06/hero-ile-kosztuje-sklep-internetowy-scaled.webp",
    "facebook_image_id": 4797,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/06/hero-ile-kosztuje-sklep-internetowy-scaled.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "reassign_wrong_topic",
    "rationale": "Wycena wdrożenia sklepu WooCommerce; obecne SEO/content/strony nie zawierają kategorii e-commerce."
  },
  "3384": {
    "category_slug": "kampanie-reklamowe",
    "expected_title": "Reklama na Facebooku — jak przygotować pierwszą kampanię dla firmy?",
    "expected_status": "publish",
    "expected_slug": "kampanie-facebook-ads-kiedy-warto-uruchomic-jak-przygotowac-strone",
    "expected_date": "2026-06-03 22:24:22",
    "expected_featured_id": 3385,
    "featured_id": 3385,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/06/kampanie-facebook-ads-1.webp",
    "facebook_image_id": 3385,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/06/kampanie-facebook-ads-1.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "consolidate_duplicate_labels_or_choose_primary",
    "rationale": "Tytuł i nagłówki opisują temat „Kampanie reklamowe”; zachować tę główną intencję.",
    "seo_meta": {
      "rank_math_focus_keyword": "kampanie Facebook Ads, reklama na facebooku",
      "rank_math_title": "Kampanie Facebook Ads: jak przygotować pierwszą kampanię",
      "rank_math_description": "Kampanie Facebook Ads wymagają jasnej oferty, pomiaru i strony docelowej. Sprawdź, co przygotować przed startem pierwszego budżetu reklamowego firmy."
    },
    "expected_seo_meta": {
      "rank_math_focus_keyword": [
        "reklama na facebooku"
      ],
      "rank_math_title": [
        "Reklama na Facebooku — plan pierwszej kampanii dla firmy"
      ],
      "rank_math_description": [
        "Zaplanuj reklamę na Facebooku: cel kampanii, ofertę, kreacje, stronę docelową i pomiar. Sprawdź, co przygotować, zanim uruchomisz pierwszy budżet."
      ]
    },
    "seo_meta_en": {
      "rank_math_title": "Facebook Ads campaigns: how to prepare your first campaign",
      "rank_math_description": "Facebook Ads campaigns need a clear offer, measurement and a landing page. Find out what your business should prepare before spending its first advertising budget."
    }
  },
  "3392": {
    "category_slug": "logo-branding",
    "expected_title": "Mini księga znaku, brandbook czy pełna identyfikacja – co wybrać?",
    "expected_status": "publish",
    "expected_slug": "ksiega-znaku-mini-brandbook-pelna-identyfikacja-wizualna-co-wybrac",
    "expected_date": "2026-06-03 22:43:27",
    "expected_featured_id": 3393,
    "featured_id": 3393,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/06/ksiega-znaku-mini-brandbook-1.webp",
    "facebook_image_id": 3393,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/06/ksiega-znaku-mini-brandbook-1.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Logo i branding”; zachować tę główną intencję.",
    "seo_meta": {
      "rank_math_focus_keyword": "księga znaku, mini księga znaku, mini brandbook",
      "rank_math_title": "Księga znaku czy mini brandbook: co wybrać dla firmy?",
      "rank_math_description": "Sprawdź, kiedy wystarczy księga znaku, a kiedy potrzebny jest mini brandbook lub pełna identyfikacja wizualna. Porównaj zakres i zastosowania dokumentów."
    },
    "expected_seo_meta": {
      "rank_math_focus_keyword": [
        "mini księga znaku, mini brandbook"
      ],
      "rank_math_title": [
        "Mini księga znaku czy pełny brandbook – co wybrać?"
      ],
      "rank_math_description": [
        "Sprawdź, czym różni się księga znaku, mini brandbook i pełna identyfikacja wizualna. Porównanie zakresu, kosztów, zastosowań i ryzyk dla firmy."
      ]
    },
    "seo_meta_en": {
      "rank_math_title": "Logo guidelines or a mini brand book: what does your business need?",
      "rank_math_description": "Find out when logo guidelines are enough and when you need a mini brand book or full visual identity. Compare the scope and use of each document."
    }
  },
  "3408": {
    "category_slug": "strony-internetowe",
    "expected_title": "Co powinna zawierać strona internetowa firmy? Lista elementów",
    "expected_status": "publish",
    "expected_slug": "strona-internetowa-dla-firmy-co-powinna-zawierac",
    "expected_date": "2026-06-03 23:31:16",
    "expected_featured_id": 3410,
    "featured_id": 3410,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/06/strona-internetowa-dla-firmy-1.webp",
    "facebook_image_id": 3410,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/06/strona-internetowa-dla-firmy-1.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Strony internetowe”; zachować tę główną intencję.",
    "seo_meta": {
      "rank_math_focus_keyword": "strona internetowa dla firmy, co powinna zawierać strona internetowa, co powinna zawierać strona internetowa firmy, elementy strony internetowej, elementy strony firmowej",
      "rank_math_title": "Strona internetowa dla firmy: oferta, zaufanie i kontakt",
      "rank_math_description": "Strona internetowa dla firmy powinna jasno pokazywać ofertę, dowody i sposób kontaktu. Sprawdź, co umieścić na stronie, aby wspierała zapytania klientów."
    },
    "expected_seo_meta": {
      "rank_math_focus_keyword": [
        "co powinna zawierać strona internetowa, co powinna zawierać strona internetowa firmy, elementy strony internetowej, elementy strony firmowej"
      ],
      "rank_math_title": [
        "Co powinna zawierać strona internetowa firmy?"
      ],
      "rank_math_description": [
        "Sprawdź, co powinna zawierać skuteczna strona internetowa dla firmy: oferta, zaufanie, UX, SEO, CTA, formularze, mobile, szybkość i analityka."
      ]
    },
    "seo_meta_en": {
      "rank_math_title": "A business website: services, trust and contact",
      "rank_math_description": "A business website should clearly present its offer, evidence and contact options. Find out what to include to support customer enquiries."
    }
  },
  "3774": {
    "category_slug": "strony-internetowe",
    "expected_title": "Etapy tworzenia strony internetowej krok po kroku",
    "expected_status": "publish",
    "expected_slug": "tworzenie-stron-internetowych-profesjonalny-proces-od-strategii-do-wdrozenia",
    "expected_date": "2026-06-15 21:58:39",
    "expected_featured_id": 3410,
    "featured_id": 3899,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/06/tworzenie-stron-internetowych-jak-wyglada-2.webp",
    "facebook_image_id": 3899,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/06/tworzenie-stron-internetowych-jak-wyglada-2.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "Tworzenie stron internetowych — strategia, struktura, UX, treści i wdrożenie",
    "featured_alt_source": "Existing approved inline image ALT in this exact article",
    "classification_action": "consolidate_duplicate_labels_or_choose_primary",
    "rationale": "Proces projektowania i wdrożenia strony krok po kroku; SEO jest jednym z etapów wykonania.",
    "seo_meta": {
      "rank_math_focus_keyword": "tworzenie stron internetowych, etapy tworzenia strony internetowej, etapy projektowania strony internetowej, jak tworzyć strony internetowe, tworzenie strony internetowej krok po kroku, proces tworzenia strony internetowej",
      "rank_math_title": "Tworzenie stron internetowych: etapy od strategii do wdrożenia",
      "rank_math_description": "Tworzenie stron internetowych obejmuje strategię, strukturę, treści, projekt i testy. Sprawdź profesjonalny proces oraz zakres odbioru przed publikacją."
    },
    "expected_seo_meta": {
      "rank_math_focus_keyword": [
        "etapy tworzenia strony internetowej, etapy projektowania strony internetowej, jak tworzyć strony internetowe, tworzenie strony internetowej krok po kroku, proces tworzenia strony internetowej"
      ],
      "rank_math_title": [
        "Etapy tworzenia strony internetowej krok po kroku"
      ],
      "rank_math_description": [
        "Poznaj proces tworzenia stron internetowych: brief, strukturę, projekt, wdrożenie i testy. Sprawdź zadania klienta oraz kryteria odbioru gotowej strony."
      ]
    },
    "seo_meta_en": {
      "rank_math_title": "Website development: stages from strategy to launch",
      "rank_math_description": "Website development includes strategy, structure, content, design and testing. Explore a professional process and the acceptance checks needed before launch."
    }
  },
  "3904": {
    "category_slug": "logo-branding",
    "expected_title": "Jak zaprojektować logo? Proces projektowania krok po kroku",
    "expected_status": "publish",
    "expected_slug": "projektowanie-logo-jak-powstaje-znak-ktory-dziala-w-internecie-druku-i-sprzedazy",
    "expected_date": "2026-06-15 22:11:23",
    "expected_featured_id": 3905,
    "featured_id": 3905,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/06/projektowanie-logo-jak-powstaje-znak-1.webp",
    "facebook_image_id": 3905,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/06/projektowanie-logo-jak-powstaje-znak-1.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Logo i branding”; zachować tę główną intencję.",
    "seo_meta": {
      "rank_math_focus_keyword": "projektowanie logo, jak zaprojektować logo, proces projektowania logo, jak zaprojektować logo firmy, zasady projektowania logo, techniki projektowania logo",
      "rank_math_title": "Projektowanie logo: od analizy marki do wdrożenia znaku",
      "rank_math_description": "Projektowanie logo wymaga briefu, analizy, koncepcji i testów. Sprawdź proces tworzenia znaku do internetu, druku oraz sprzedaży i zakres końcowych plików."
    },
    "expected_seo_meta": {
      "rank_math_focus_keyword": [
        "jak zaprojektować logo, proces projektowania logo, jak zaprojektować logo firmy, zasady projektowania logo, techniki projektowania logo"
      ],
      "rank_math_title": [
        "Jak zaprojektować logo? Proces projektowania krok po kroku"
      ],
      "rank_math_description": [
        "Projektowanie logo krok po kroku: brief, analiza, koncepcje, typografia, kolorystyka, testy, pliki i wdrożenie znaku w internecie, druku i sprzedaży."
      ]
    },
    "seo_meta_en": {
      "rank_math_title": "Logo design: from brand analysis to implementation",
      "rank_math_description": "Logo design requires a brief, analysis, concepts and testing. Explore the process of creating a mark for web, print and sales, and the final files to expect."
    }
  },
  "3920": {
    "category_slug": "logo-branding",
    "expected_title": "Księga znaku — co zawiera? Przykłady i checklista dla firmy",
    "expected_status": "publish",
    "expected_slug": "ksiega-znaku-co-to-jest-co-zawiera-i-kiedy-firma-jej-potrzebuje",
    "expected_date": "2026-06-15 22:24:31",
    "expected_featured_id": 3921,
    "featured_id": 3921,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/06/ksiega-znaku-co-to-jest-1.webp",
    "facebook_image_id": 3921,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/06/ksiega-znaku-co-to-jest-1.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Logo i branding”; zachować tę główną intencję."
  },
  "3928": {
    "category_slug": "strony-internetowe",
    "expected_title": "Zasady tworzenia stron internetowych, które dają zapytania",
    "expected_status": "publish",
    "expected_slug": "projektowanie-stron-internetowych-co-decyduje-o-zapytaniach",
    "expected_date": "2026-06-15 22:33:52",
    "expected_featured_id": 3929,
    "featured_id": 3929,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/06/projektowanie-stron-internetowych-1.webp",
    "facebook_image_id": 3929,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/06/projektowanie-stron-internetowych-1.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Strony internetowe”; zachować tę główną intencję.",
    "seo_meta": {
      "rank_math_focus_keyword": "projektowanie stron internetowych, zasady tworzenia stron internetowych, jak zaprojektować stronę internetową, projekt graficzny strony internetowej",
      "rank_math_title": "Projektowanie stron internetowych, które wspierają zapytania",
      "rank_math_description": "Projektowanie stron internetowych łączy ofertę, strukturę, treści i UX. Sprawdź, jak zaplanować sekcje, kontakt oraz pomiar działań klientów na stronie."
    },
    "expected_seo_meta": {
      "rank_math_focus_keyword": [
        "zasady tworzenia stron internetowych, jak zaprojektować stronę internetową, projekt graficzny strony internetowej"
      ],
      "rank_math_title": [
        "Zasady tworzenia stron internetowych, które dają zapytania"
      ],
      "rank_math_description": [
        "Zobacz, jak projektowanie stron internetowych łączy układ, treści i UX. Poznaj sekcje, które pomagają zrozumieć ofertę i przejść do wysłania zapytania."
      ]
    },
    "seo_meta_en": {
      "rank_math_title": "Website design that supports customer enquiries",
      "rank_math_description": "Website design connects your offer, structure, content and UX. Learn how to plan sections, contact options and measurement of customer actions."
    }
  },
  "4541": {
    "category_slug": "case-study",
    "expected_title": "Strona internetowa Polerstone i branding marki kamieniarskiej",
    "expected_status": "publish",
    "expected_slug": "strona-internetowa-polerstone",
    "expected_date": "2026-09-06 21:11:45",
    "expected_featured_id": 4507,
    "featured_id": 4507,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/09/polerstone_realizacja-scaled.png",
    "facebook_image_id": 4507,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/09/polerstone_realizacja-scaled.png",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "format_refinement",
    "rationale": "Rzeczywista realizacja Polerstone: marka i strona; format prezentacji wykonanej pracy dla klienta."
  },
  "4551": {
    "category_slug": "case-study",
    "expected_title": "Sklep internetowy Świat Grilli – od brandingu do WooCommerce",
    "expected_status": "publish",
    "expected_slug": "sklep-internetowy-swiat-grilli",
    "expected_date": "2026-09-06 22:00:50",
    "expected_featured_id": 4449,
    "featured_id": 4449,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/08/swiatgrili_realizacja.webp",
    "facebook_image_id": 4449,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/08/swiatgrili_realizacja.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "format_refinement",
    "rationale": "Rzeczywista realizacja Świat Grilli, branding i WooCommerce; odróżnić od poradników o case studies."
  },
  "4630": {
    "category_slug": "strony-internetowe",
    "expected_title": "Landing page — co to jest i jak zaplanować stronę pod kampanię?",
    "expected_status": "publish",
    "expected_slug": "landing-page-co-to",
    "expected_date": "2026-09-13 00:25:43",
    "expected_featured_id": 4631,
    "featured_id": 4631,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/09/hero-landing-page-scaled.webp",
    "facebook_image_id": 4631,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/09/hero-landing-page-scaled.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Strony internetowe”; zachować tę główną intencję."
  },
  "4766": {
    "category_slug": "sklepy-internetowe",
    "expected_title": "Jak założyć sklep internetowy? Plan uruchomienia krok po kroku",
    "expected_status": "publish",
    "expected_slug": "jak-zalozyc-sklep-internetowy",
    "expected_date": "2026-09-15 21:46:42",
    "expected_featured_id": 4767,
    "featured_id": 4767,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/09/hero-jak-zalozyc-sklep-internetowy-scaled.webp",
    "facebook_image_id": 4767,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/09/hero-jak-zalozyc-sklep-internetowy-scaled.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "reassign_wrong_topic",
    "rationale": "Plan uruchomienia sprzedaży: platforma, katalog, płatności, dostawy i zamówienia; brakuje kategorii sklepu."
  },
  "4778": {
    "category_slug": "strony-internetowe",
    "expected_title": "Przykłady landing page – omówienie stron pod zapytania",
    "expected_status": "publish",
    "expected_slug": "landing-page-przyklady",
    "expected_date": "2026-09-15 22:11:34",
    "expected_featured_id": 4782,
    "featured_id": 4782,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/09/hero-landing-page-przyklady-scaled.webp",
    "facebook_image_id": 4782,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/09/hero-landing-page-przyklady-scaled.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "consolidate_duplicate_labels_or_choose_primary",
    "rationale": "Modelowe landing pages, układ sekcji, CTA i formularz; format poradnika UX, nie zrealizowane case study klienta."
  },
  "4787": {
    "category_slug": "strony-internetowe",
    "expected_title": "Ile kosztuje utrzymanie strony internetowej miesięcznie?",
    "expected_status": "publish",
    "expected_slug": "ile-kosztuje-utrzymanie-strony-internetowej",
    "expected_date": "2026-09-15 22:51:01",
    "expected_featured_id": 4788,
    "featured_id": 4788,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/09/hero-koszt-utrzymania-strony-scaled.webp",
    "facebook_image_id": 4788,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/09/hero-koszt-utrzymania-strony-scaled.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "consolidate_duplicate_labels_or_choose_primary",
    "rationale": "Koszty utrzymania witryny: domena, hosting i obsługa; strony WWW jako temat główny, SEO jako kontekst."
  },
  "4808": {
    "category_slug": "logo-branding",
    "expected_title": "Agencja brandingowa czy freelancer? Komu zlecić logo",
    "expected_status": "publish",
    "expected_slug": "agencja-brandingowa-czy-freelancer",
    "expected_date": "2026-09-16 15:34:41",
    "expected_featured_id": 4810,
    "featured_id": 4810,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/09/hf_20260916_132503_ef4943c3-1096-43d5-83b0-f7c56ace0f6d-scaled.webp",
    "facebook_image_id": 4810,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/09/hf_20260916_132503_ef4943c3-1096-43d5-83b0-f7c56ace0f6d-scaled.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Logo i branding”; zachować tę główną intencję."
  },
  "4820": {
    "category_slug": "strony-internetowe",
    "expected_title": "Agencja czy freelancer do stworzenia strony?",
    "expected_status": "publish",
    "expected_slug": "agencja-czy-freelancer-do-stworzenia-strony",
    "expected_date": "2026-09-16 16:08:48",
    "expected_featured_id": 4821,
    "featured_id": 4821,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/09/hf_20260916_140350_cefac276-8d41-4a41-9efe-aad6756a3bb9_compressed-scaled.webp",
    "facebook_image_id": 4821,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/09/hf_20260916_140350_cefac276-8d41-4a41-9efe-aad6756a3bb9_compressed-scaled.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Strony internetowe”; zachować tę główną intencję."
  },
  "4831": {
    "category_slug": "kampanie-reklamowe",
    "expected_title": "Dlaczego Google Ads nie generuje zapytań? Najczęstsze przyczyny",
    "expected_status": "publish",
    "expected_slug": "google-ads-nie-generuje-zapytan",
    "expected_date": "2026-09-16 16:21:37",
    "expected_featured_id": 4836,
    "featured_id": 4836,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/09/hf_20260916_142049_39ecb8cf-53c0-4c2b-a95f-c95536e78d35_compressed.webp",
    "facebook_image_id": 4836,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/09/hf_20260916_142049_39ecb8cf-53c0-4c2b-a95f-c95536e78d35_compressed.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "reassign_wrong_topic",
    "rationale": "Diagnoza emisji Google Ads, budżetu, stawek, kierowania i konwersji; obecnie tylko strony WWW."
  },
  "4843": {
    "category_slug": "kampanie-reklamowe",
    "expected_title": "Google Ads lokalnie czy na całą Polskę? Jak dobrać obszar kampanii",
    "expected_status": "publish",
    "expected_slug": "google-ads-cala-polska-czy-lokalnie",
    "expected_date": "2026-09-16 17:58:14",
    "expected_featured_id": 4849,
    "featured_id": 4849,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/09/hf_20260916_155704_025526d2-1048-4860-a340-0477bcc1964a_compressed-1-scaled.webp",
    "facebook_image_id": 4849,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/09/hf_20260916_155704_025526d2-1048-4860-a340-0477bcc1964a_compressed-1-scaled.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Kampanie reklamowe”; zachować tę główną intencję.",
    "seo_meta": {
      "rank_math_focus_keyword": "Google Ads cała Polska czy lokalnie, Google Ads lokalnie",
      "rank_math_title": "Google Ads: cała Polska czy lokalnie? Jak dobrać zasięg",
      "rank_math_description": "Google Ads: cała Polska czy lokalnie? Sprawdź kierowanie na miasta, regiony i promień, opcje obecności oraz wykluczenia i wyniki lokalizacji kampanii."
    },
    "expected_seo_meta": {
      "rank_math_focus_keyword": [
        "Google Ads lokalnie, Google Ads cała Polska czy lokalnie"
      ],
      "rank_math_title": [
        "Google Ads lokalnie czy na całą Polskę? Jak ustawić zasięg"
      ],
      "rank_math_description": [
        "Google Ads cała Polska czy lokalnie? Sprawdź kierowanie na miasta, regiony, promień, obecność użytkownika, wykluczenia i wyniki lokalizacji."
      ]
    },
    "seo_meta_en": {
      "rank_math_title": "Google Ads nationwide or locally? How to choose your reach",
      "rank_math_description": "Google Ads nationwide or locally? Check city, region and radius targeting, presence options, exclusions and campaign location results."
    }
  },
  "4871": {
    "category_slug": "logo-branding",
    "expected_title": "Brief do logo i identyfikacji wizualnej – jak go przygotować",
    "expected_status": "publish",
    "expected_slug": "brief-do-logo-i-identyfikacji-wizualnej",
    "expected_date": "2026-09-16 19:59:03",
    "expected_featured_id": 4868,
    "featured_id": 4868,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/09/hf_20260916_173633_dc368f1f-aca5-4cda-9332-2c04bc014149-scaled.webp",
    "facebook_image_id": 4868,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/09/hf_20260916_173633_dc368f1f-aca5-4cda-9332-2c04bc014149-scaled.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Logo i branding”; zachować tę główną intencję."
  },
  "4876": {
    "category_slug": "sklepy-internetowe",
    "expected_title": "Karta produktu w sklepie internetowym – co musi zawierać, żeby klient kupił",
    "expected_status": "publish",
    "expected_slug": "karta-produktu-w-sklepie-internetowym",
    "expected_date": "2026-09-16 20:08:45",
    "expected_featured_id": 5625,
    "featured_id": 5625,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/karta-produktu-okladka.webp",
    "facebook_image_id": 5625,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/karta-produktu-okladka.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "Karta produktu w sklepie internetowym – co musi zawierać, żeby klient kupił",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Sklepy internetowe”; zachować tę główną intencję."
  },
  "4884": {
    "category_slug": "sklepy-internetowe",
    "expected_title": "Kategorie produktów w sklepie internetowym – UX, SEO i sprzedaż",
    "expected_status": "publish",
    "expected_slug": "kategorie-produktow-w-sklepie-internetowym",
    "expected_date": "2026-09-16 20:20:02",
    "expected_featured_id": 4886,
    "featured_id": 4886,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/09/hf_20260916_181615_da27328e-4ea4-4c77-b680-0b4d330ce1bf.webp",
    "facebook_image_id": 4886,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/09/hf_20260916_181615_da27328e-4ea4-4c77-b680-0b4d330ce1bf.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Sklepy internetowe”; zachować tę główną intencję."
  },
  "4892": {
    "category_slug": "logo-branding",
    "expected_title": "Logo AI czy projektant? Różnice, ryzyko i wybór",
    "expected_status": "publish",
    "expected_slug": "logo-ai-czy-projektant",
    "expected_date": "2026-09-17 13:06:16",
    "expected_featured_id": 4895,
    "featured_id": 4895,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/09/hf_20260917_105856_c9d09b32-58fd-4230-a622-d6d92bedfe39-scaled.webp",
    "facebook_image_id": 4895,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/09/hf_20260917_105856_c9d09b32-58fd-4230-a622-d6d92bedfe39-scaled.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Logo i branding”; zachować tę główną intencję."
  },
  "4919": {
    "category_slug": "strony-internetowe",
    "expected_title": "Ile trwa stworzenie strony internetowej?",
    "expected_status": "publish",
    "expected_slug": "ile-trwa-stworzenie-strony-internetowej",
    "expected_date": "2026-09-17 22:27:25",
    "expected_featured_id": 4928,
    "featured_id": 4928,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/09/ile-trwa-strona-harmonogram-1.webp",
    "facebook_image_id": 4928,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/09/ile-trwa-strona-harmonogram-1.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "reassign_wrong_topic",
    "rationale": "Harmonogram tworzenia strony internetowej; wpis jest obecnie w sklepach internetowych."
  },
  "4936": {
    "category_slug": "strony-internetowe",
    "expected_title": "WordPress czy strona dedykowana dla firmy?",
    "expected_status": "publish",
    "expected_slug": "wordpress-czy-strona-dedykowana",
    "expected_date": "2026-09-18 23:53:30",
    "expected_featured_id": 4937,
    "featured_id": 4937,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/09/wordpress-czy-dedykowana-proces.webp",
    "facebook_image_id": 4937,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/09/wordpress-czy-dedykowana-proces.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Strony internetowe”; zachować tę główną intencję."
  },
  "4955": {
    "category_slug": "strony-internetowe",
    "expected_title": "WordPress, Wix czy Webflow? Jak wybrać platformę",
    "expected_status": "publish",
    "expected_slug": "wordpress-wix-czy-webflow",
    "expected_date": "2026-09-19 09:12:33",
    "expected_featured_id": 4956,
    "featured_id": 4956,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/09/wordpress-wix-webflow-praca.webp",
    "facebook_image_id": 4956,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/09/wordpress-wix-webflow-praca.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Strony internetowe”; zachować tę główną intencję.",
    "seo_meta": {
      "rank_math_title": "WordPress, Wix czy Webflow? Porównanie 3 platform dla firmy",
      "rank_math_description": "WordPress, Wix czy Webflow? Porównaj projekt, obsługę treści, SEO, koszty i utrzymanie strony firmowej. Sprawdź ograniczenia platform przed wyborem narzędzia."
    },
    "expected_seo_meta": {
      "rank_math_title": [
        "WordPress, Wix czy Webflow? Jak wybrać platformę"
      ],
      "rank_math_description": [
        "WordPress, Wix czy Webflow? Porównaj kontrolę, obsługę, SEO, hosting, rozwój oraz możliwości migracji przed wyborem platformy strony."
      ]
    },
    "seo_meta_en": {
      "rank_math_title": "WordPress, Wix or Webflow? Comparing 3 business website platforms",
      "rank_math_description": "WordPress, Wix or Webflow? Compare design, content management, SEO, costs and website maintenance. Check platform limitations before choosing a tool."
    }
  },
  "4965": {
    "category_slug": "strony-internetowe",
    "expected_title": "Strona na abonament czy na własność? Porównanie",
    "expected_status": "publish",
    "expected_slug": "strona-na-abonament-czy-na-wlasnosc",
    "expected_date": "2026-09-19 09:24:03",
    "expected_featured_id": 4966,
    "featured_id": 4966,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/09/strona-abonament-czy-wlasnosc.webp",
    "facebook_image_id": 4966,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/09/strona-abonament-czy-wlasnosc.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Strony internetowe”; zachować tę główną intencję.",
    "seo_meta": {
      "rank_math_focus_keyword": "strona na własność, strona na abonament, strona internetowa w abonamencie, strona www w abonamencie, strona internetowa na abonament",
      "rank_math_title": "Strona na własność czy abonament? Koszty i zasady",
      "rank_math_description": "Strona na własność czy abonament? Porównaj koszty, zakres opieki, prawa do strony i warunki zakończenia współpracy przed podpisaniem umowy."
    },
    "expected_seo_meta": {
      "rank_math_focus_keyword": [
        "strona na abonament, strona internetowa w abonamencie, strona www w abonamencie, strona internetowa na abonament"
      ],
      "rank_math_title": [
        "Strona na abonament czy na własność? Porównanie"
      ],
      "rank_math_description": [
        "Strona na abonament czy na własność? Porównaj pełny koszt, prawa, domenę, hosting, dostęp do plików oraz warunki zakończenia współpracy."
      ]
    },
    "seo_meta_en": {
      "rank_math_title": "Website ownership or a subscription? Costs and terms",
      "rank_math_description": "Website ownership or a subscription? Compare costs, support, website rights and the terms of ending the relationship before signing a contract."
    }
  },
  "4973": {
    "category_slug": "strony-internetowe",
    "expected_title": "Szablon czy projekt indywidualny strony",
    "expected_status": "publish",
    "expected_slug": "szablon-czy-projekt-indywidualny-strony",
    "expected_date": "2026-09-19 09:56:56",
    "expected_featured_id": 4974,
    "featured_id": 4974,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/09/szablon-vs-projekt-indywidualny-metafora.webp",
    "facebook_image_id": 4974,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/09/szablon-vs-projekt-indywidualny-metafora.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Strony internetowe”; zachować tę główną intencję.",
    "seo_meta": {
      "rank_math_focus_keyword": "projekt indywidualny, szablon czy projekt indywidualny, strona internetowa szablon, gotowy szablon strony internetowej, szablon czy indywidualny projekt strony",
      "rank_math_title": "Projekt indywidualny strony czy szablon? Zakres i różnice",
      "rank_math_description": "Projekt indywidualny strony czy szablon? Sprawdź różnice w układzie, treściach, funkcjach i rozwoju. Dopasuj zakres do potrzeb oraz budżetu firmy."
    },
    "expected_seo_meta": {
      "rank_math_focus_keyword": [
        "szablon czy projekt indywidualny, strona internetowa szablon, gotowy szablon strony internetowej, szablon czy indywidualny projekt strony"
      ],
      "rank_math_title": [
        "Szablon czy projekt indywidualny strony? Porównanie"
      ],
      "rank_math_description": [
        "Szablon czy projekt indywidualny strony? Porównaj koszt, czas, możliwości rozbudowy, utrzymanie i poziom dopasowania do potrzeb firmy."
      ]
    },
    "seo_meta_en": {
      "rank_math_title": "A custom website design or a template? Scope and differences",
      "rank_math_description": "A custom website design or a template? Compare layout, content, features and future development. Match the scope to your business needs and budget."
    }
  },
  "4998": {
    "category_slug": "seo-i-konwersja",
    "expected_title": "Przebudowa strony bez utraty widoczności w Google",
    "expected_status": "publish",
    "expected_slug": "przebudowa-strony-bez-utraty-widocznosci-w-google",
    "expected_date": "2026-09-20 19:49:09",
    "expected_featured_id": 4999,
    "featured_id": 4999,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/09/redesign-strony-mapa-przekierowan.webp",
    "facebook_image_id": 4999,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/09/redesign-strony-mapa-przekierowan.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "primary_refinement",
    "rationale": "Audyt URL, przekierowania, indeksowanie i GSC przy redesignie; główny problem to zachowanie widoczności.",
    "seo_meta": {
      "rank_math_focus_keyword": "przebudowa strony, przebudowa strony bez utraty widoczności, migracja strony internetowej, przebudowa strony a seo",
      "rank_math_title": "Przebudowa strony bez utraty widoczności: plan migracji SEO",
      "rank_math_description": "Przebudowa strony wymaga planu SEO: inwentaryzacji adresów, przekierowań, treści i pomiaru. Sprawdź, jak ograniczyć ryzyko utraty widoczności w Google."
    },
    "expected_seo_meta": {
      "rank_math_focus_keyword": [
        "przebudowa strony bez utraty widoczności, migracja strony internetowej, przebudowa strony a seo"
      ],
      "rank_math_title": [
        "Przebudowa strony bez utraty widoczności w Google"
      ],
      "rank_math_description": [
        "Przebudowa strony bez utraty widoczności w Google: zaplanuj adresy, treści, przekierowania, testy, indeksowanie i monitoring po migracji."
      ]
    },
    "seo_meta_en": {
      "rank_math_title": "Website redesign without losing search visibility: an SEO migration plan",
      "rank_math_description": "A website redesign needs an SEO plan covering URLs, redirects, content and measurement. Learn how to reduce the risk of losing visibility in Google."
    }
  },
  "5006": {
    "category_slug": "sklepy-internetowe",
    "expected_title": "WooCommerce czy Shopify? Wybór platformy sklepu",
    "expected_status": "publish",
    "expected_slug": "woocommerce-czy-shopify",
    "expected_date": "2026-09-20 20:17:58",
    "expected_featured_id": 5008,
    "featured_id": 5008,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/09/woocommerce-shopify-start-sklepu.webp",
    "facebook_image_id": 5008,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/09/woocommerce-shopify-start-sklepu.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "reassign_wrong_topic",
    "rationale": "Porównanie platform sklepu WooCommerce i Shopify oraz płatności i integracji.",
    "seo_meta": {
      "rank_math_focus_keyword": "kiedy wybrać WooCommerce a kiedy Shopify, WooCommerce czy Shopify, shopify czy woocommerce, shopify vs woocommerce",
      "rank_math_title": "Kiedy wybrać WooCommerce, a kiedy Shopify? Porównanie",
      "rank_math_description": "Sprawdź, kiedy wybrać WooCommerce, a kiedy Shopify. Porównaj koszty, płatności, integracje, kontrolę nad sklepem oraz odpowiedzialność za utrzymanie."
    },
    "expected_seo_meta": {
      "rank_math_focus_keyword": [
        "WooCommerce czy Shopify, shopify czy woocommerce, shopify vs woocommerce"
      ],
      "rank_math_title": [
        "WooCommerce czy Shopify? Co wybrać do sklepu"
      ],
      "rank_math_description": [
        "WooCommerce czy Shopify? Porównaj koszty, hosting, kontrolę, płatności, integracje i utrzymanie, aby dobrać platformę do procesu sprzedaży."
      ]
    },
    "seo_meta_en": {
      "rank_math_title": "When should you choose WooCommerce, and when Shopify? A comparison",
      "rank_math_description": "Learn when to choose WooCommerce and when Shopify. Compare costs, payments, integrations, control over the store and maintenance responsibilities."
    }
  },
  "5015": {
    "category_slug": "sklepy-internetowe",
    "expected_title": "WooCommerce czy PrestaShop? Wybór dla firmy",
    "expected_status": "publish",
    "expected_slug": "woocommerce-czy-prestashop",
    "expected_date": "2026-09-20 20:40:54",
    "expected_featured_id": 5016,
    "featured_id": 5016,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/09/prestashop-multistore.webp",
    "facebook_image_id": 5016,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/09/prestashop-multistore.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "reassign_wrong_topic",
    "rationale": "Porównanie platform e-commerce WooCommerce i PrestaShop, nie ogólnych stron firmowych."
  },
  "5024": {
    "category_slug": "sklepy-internetowe",
    "expected_title": "Migracja sklepu na WooCommerce: kiedy ma sens?",
    "expected_status": "publish",
    "expected_slug": "migracja-sklepu-na-woocommerce",
    "expected_date": "2026-09-20 21:31:27",
    "expected_featured_id": 5025,
    "featured_id": 5025,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/09/migracja-woocommerce-cutover_.webp",
    "facebook_image_id": 5025,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/09/migracja-woocommerce-cutover_.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "reassign_wrong_topic",
    "rationale": "Migracja produktów, danych i procesu sprzedaży na WooCommerce; obecnie tylko kategoria stron WWW.",
    "seo_meta": {
      "rank_math_focus_keyword": "migracja sklepu, migracja sklepu na WooCommerce, migracja sklepu internetowego, przeniesienie sklepu na woocommerce",
      "rank_math_title": "Migracja sklepu na WooCommerce: kiedy ma sens i co sprawdzić",
      "rank_math_description": "Migracja sklepu na WooCommerce wymaga planu danych, integracji i adresów. Sprawdź, kiedy warto zmienić platformę i jak przygotować testy przed startem."
    },
    "expected_seo_meta": {
      "rank_math_focus_keyword": [
        "migracja sklepu na WooCommerce, migracja sklepu internetowego, przeniesienie sklepu na woocommerce"
      ],
      "rank_math_title": [
        "Migracja sklepu na WooCommerce – kiedy ma sens?"
      ],
      "rank_math_description": [
        "Migracja sklepu na WooCommerce: sprawdź zakres danych, staging, próbny import, przekierowania, SEO, testy i plan bezpiecznego przełączenia."
      ]
    },
    "seo_meta_en": {
      "rank_math_title": "Migrating a store to WooCommerce: when it makes sense",
      "rank_math_description": "Migrating a store to WooCommerce requires a plan for data, integrations and URLs. Find out when to change platforms and how to prepare pre-launch tests."
    }
  },
  "5043": {
    "category_slug": "logo-branding",
    "expected_title": "Ile trwa projekt logo i brandingu? Etapy oraz poprawki",
    "expected_status": "publish",
    "expected_slug": "ile-trwa-projekt-logo-i-brandingu",
    "expected_date": "2026-09-20 22:22:03",
    "expected_featured_id": 5044,
    "featured_id": 5044,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/09/harmonogram-projektu-logo.webp",
    "facebook_image_id": 5044,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/09/harmonogram-projektu-logo.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Logo i branding”; zachować tę główną intencję.",
    "seo_meta": {
      "rank_math_focus_keyword": "ile trwa projekt logo, ile trwa projekt logo i brandingu, czas projektowania logo, termin brandingu, etapy projektowania logo",
      "rank_math_title": "Ile trwa projekt logo i brandingu? Etapy i poprawki",
      "rank_math_description": "Sprawdź, ile trwa projekt logo i brandingu: brief, koncepcje, akceptacje oraz wdrożenie. Zobacz, które decyzje i materiały wpływają na harmonogram."
    },
    "expected_seo_meta": {
      "rank_math_focus_keyword": [
        "ile trwa projekt logo i brandingu,ile trwa projekt logo`,`czas projektowania logo`,`termin brandingu`,`etapy projektowania logo`"
      ],
      "rank_math_title": [
        "Ile trwa projekt logo i brandingu? Etapy i terminy"
      ],
      "rank_math_description": [
        "Ile trwa projekt logo i brandingu? Poznaj realistyczne terminy, etapy pracy, wpływ poprawek oraz czynniki, które przesuwają harmonogram."
      ]
    },
    "seo_meta_en": {
      "rank_math_title": "How long does logo and branding design take? Stages and revisions",
      "rank_math_description": "Find out how long logo and branding design takes: the brief, concepts, approvals and implementation. See which decisions and materials affect the schedule."
    }
  },
  "5051": {
    "category_slug": "logo-branding",
    "expected_title": "Rebranding bez utraty rozpoznawalności marki",
    "expected_status": "publish",
    "expected_slug": "rebranding-bez-utraty-rozpoznawalnosci-marki",
    "expected_date": "2026-09-20 22:35:12",
    "expected_featured_id": 5053,
    "featured_id": 5053,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/09/rebranding-wymiana-oznakowania.webp",
    "facebook_image_id": 5053,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/09/rebranding-wymiana-oznakowania.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Logo i branding”; zachować tę główną intencję.",
    "seo_meta": {
      "rank_math_focus_keyword": "rebranding, rebranding bez utraty rozpoznawalności marki, jak przeprowadzić rebranding, zmiana identyfikacji wizualnej, rebranding marki krok po kroku, migracja marki",
      "rank_math_title": "Rebranding bez utraty rozpoznawalności marki: plan wdrożenia",
      "rank_math_description": "Rebranding bez utraty rozpoznawalności wymaga zachowania kluczowych elementów i planu wdrożenia. Sprawdź zmianę logo, komunikacji oraz materiałów firmy."
    },
    "expected_seo_meta": {
      "rank_math_focus_keyword": [
        "rebranding bez utraty rozpoznawalności marki,jak przeprowadzić rebranding`,`zmiana identyfikacji wizualnej`,`rebranding marki krok po kroku`,`migracja marki`"
      ],
      "rank_math_title": [
        "Rebranding bez utraty rozpoznawalności marki"
      ],
      "rank_math_description": [
        "Rebranding bez utraty rozpoznawalności marki: sprawdź, co zachować, jak zaplanować migrację identyfikacji oraz jak mierzyć reakcje odbiorców."
      ]
    },
    "seo_meta_en": {
      "rank_math_title": "Rebranding without losing brand recognition: an implementation plan",
      "rank_math_description": "Rebranding without losing recognition requires preserving key elements and planning implementation. Review changes to your logo, communications and business materials."
    }
  },
  "5064": {
    "category_slug": "logo-branding",
    "expected_title": "Kolory i typografia marki: jak zbudować spójny system",
    "expected_status": "publish",
    "expected_slug": "kolory-i-typografia-marki",
    "expected_date": "2026-09-20 22:55:22",
    "expected_featured_id": 5065,
    "featured_id": 5065,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/09/kolory-marki-materialy.webp",
    "facebook_image_id": 5065,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/09/kolory-marki-materialy.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Logo i branding”; zachować tę główną intencję.",
    "seo_meta": {
      "rank_math_focus_keyword": "kolory i typografia, kolory i typografia marki, czcionka do logo, kolory logo, kolory w logo, psychologia kolorów logo",
      "rank_math_title": "Kolory i typografia marki: jak zbudować spójny system",
      "rank_math_description": "Kolory i typografia tworzą system wizualny marki. Sprawdź, jak dobierać paletę, kroje i hierarchię oraz utrzymać spójność w internecie i materiałach do druku."
    },
    "expected_seo_meta": {
      "rank_math_focus_keyword": [
        "kolory i typografia marki, czcionka do logo, kolory logo, kolory w logo, psychologia kolorów logo"
      ],
      "rank_math_title": [
        "Kolory i typografia marki – czcionka i kolory logo"
      ],
      "rank_math_description": [
        "Kolory i typografia marki powinny mieć jasno określone role. Sprawdź paletę, hierarchię fontów, kontrast, brand book i testy na realnych materiałach."
      ]
    },
    "seo_meta_en": {
      "rank_math_title": "Brand colours and typography: building a consistent system",
      "rank_math_description": "Colours and typography form your brand's visual system. Learn how to choose a palette, typefaces and hierarchy, and stay consistent across web and print."
    }
  },
  "5075": {
    "category_slug": "kampanie-reklamowe",
    "expected_title": "Google Ads czy Meta Ads dla firmy usługowej",
    "expected_status": "publish",
    "expected_slug": "google-ads-czy-meta-ads-firma-uslugowa",
    "expected_date": "2026-09-20 23:14:31",
    "expected_featured_id": 5077,
    "featured_id": 5077,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/09/google-ads-meta-ads-intencja-klienta.webp",
    "facebook_image_id": 5077,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/09/google-ads-meta-ads-intencja-klienta.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Kampanie reklamowe”; zachować tę główną intencję.",
    "seo_meta": {
      "rank_math_focus_keyword": "Google Ads czy Meta Ads dla firmy usługowej, Google Ads czy Meta Ads, Meta Ads dla firmy usługowej, Google Ads dla firmy usługowej, reklama firmy usługowej"
    },
    "expected_seo_meta": {
      "rank_math_focus_keyword": [
        "Google Ads czy Meta Ads dla firmy usługowej,Google Ads czy Meta Ads`,`Meta Ads dla firmy usługowej`,`Google Ads dla firmy usługowej`,`reklama firmy usługowej`"
      ]
    },
    "seo_meta_en": {}
  },
  "5089": {
    "category_slug": "kampanie-reklamowe",
    "expected_title": "Google Ads: Search czy Performance Max dla usług?",
    "expected_status": "publish",
    "expected_slug": "google-ads-search-czy-performance-max",
    "expected_date": "2026-09-20 23:50:03",
    "expected_featured_id": 5090,
    "featured_id": 5090,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/09/search-vs-pmax-intencja.webp",
    "facebook_image_id": 5090,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/09/search-vs-pmax-intencja.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Kampanie reklamowe”; zachować tę główną intencję.",
    "seo_meta": {
      "rank_math_focus_keyword": "Search czy Performance Max, Google Ads Search dla usług, Performance Max dla usług, PMax czy Search, kampania Search Google Ads"
    },
    "expected_seo_meta": {
      "rank_math_focus_keyword": [
        "Search czy Performance Max,Google Ads Search dla usług`,`Performance Max dla usług`,`PMax czy Search`,`kampania Search Google Ads`"
      ]
    },
    "seo_meta_en": {}
  },
  "5097": {
    "category_slug": "kampanie-reklamowe",
    "expected_title": "Ile kosztuje Google Ads? Koszt kliknięcia, budżet testu i obsługa kampanii",
    "expected_status": "publish",
    "expected_slug": "ile-kosztuje-google-ads",
    "expected_date": "2026-09-21 00:37:08",
    "expected_featured_id": 5623,
    "featured_id": 5623,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/ile-kosztuje-google-ads-okladka.webp",
    "facebook_image_id": 5623,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/ile-kosztuje-google-ads-okladka.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "Ile kosztuje Google Ads? Koszt kliknięcia, budżet testu i obsługa kampanii",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Kampanie reklamowe”; zachować tę główną intencję."
  },
  "5396": {
    "category_slug": "strony-internetowe",
    "expected_title": "Jak stworzyć stronę internetową dla firmy? Kreator, WordPress czy agencja",
    "expected_status": "publish",
    "expected_slug": "jak-stworzyc-strone-internetowa",
    "expected_date": "2026-10-03 20:30:34",
    "expected_featured_id": 6122,
    "featured_id": 6122,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-0447ad18-036-cover-webp37.webp",
    "facebook_image_id": 6122,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-0447ad18-036-cover-webp37.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "Ustalanie struktury firmowej strony na papierowej tablicy",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Strony internetowe”; zachować tę główną intencję."
  },
  "5399": {
    "category_slug": "logo-branding",
    "expected_title": "Zastrzeżenie logo – jak zastrzec logo w Polsce i w Unii Europejskiej?",
    "expected_status": "publish",
    "expected_slug": "jak-zastrzec-logo",
    "expected_date": "2026-10-03 20:30:35",
    "expected_featured_id": 6115,
    "featured_id": 6115,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-9615966c-cc4-cover-webp37.webp",
    "facebook_image_id": 6115,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-9615966c-cc4-cover-webp37.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "Porównywanie prostych znaków graficznych pod lupą",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Logo i branding”; zachować tę główną intencję."
  },
  "5402": {
    "category_slug": "logo-branding",
    "expected_title": "Key visual – co to jest i jak zaprojektować główny motyw kampanii?",
    "expected_status": "publish",
    "expected_slug": "key-visual-co-to-jest",
    "expected_date": "2026-10-03 20:30:36",
    "expected_featured_id": 6112,
    "featured_id": 6112,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-dfe41c11-85d-cover-webp37.webp",
    "facebook_image_id": 6112,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-dfe41c11-85d-cover-webp37.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "Budowanie motywu kampanii z papierowych form i tkanin w studiu",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Logo i branding”; zachować tę główną intencję."
  },
  "5405": {
    "category_slug": "logo-branding",
    "expected_title": "Logo a logotyp i sygnet – czym się różnią i co wybrać dla firmy?",
    "expected_status": "publish",
    "expected_slug": "logo-logotyp-sygnet-roznice",
    "expected_date": "2026-10-03 20:30:37",
    "expected_featured_id": 6109,
    "featured_id": 6109,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-01828542-1b4-cover-webp37.webp",
    "facebook_image_id": 6109,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-01828542-1b4-cover-webp37.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "Oddzielne litery i geometryczny sygnet na próbniku oznakowania",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Logo i branding”; zachować tę główną intencję."
  },
  "5409": {
    "category_slug": "logo-branding",
    "expected_title": "Logo wektorowe – jakie pliki logo powinna mieć firma i do czego służą?",
    "expected_status": "publish",
    "expected_slug": "logo-wektorowe-pliki-logo",
    "expected_date": "2026-10-03 20:30:39",
    "expected_featured_id": 6105,
    "featured_id": 6105,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-6c844271-d4b-cover-webp37.webp",
    "facebook_image_id": 6105,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-6c844271-d4b-cover-webp37.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "Porównanie małego i dużego znaku wyciętego ploterem",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Logo i branding”; zachować tę główną intencję."
  },
  "5413": {
    "category_slug": "logo-branding",
    "expected_title": "Archetypy marki – 12 archetypów z przykładami i jak wybrać swój",
    "expected_status": "publish",
    "expected_slug": "archetypy-marki",
    "expected_date": "2026-10-03 20:30:40",
    "expected_featured_id": 6102,
    "featured_id": 6102,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-ba96d47f-6de-cover-webp37.webp",
    "facebook_image_id": 6102,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-ba96d47f-6de-cover-webp37.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "Przedmioty symbolizujące różne osobowości marki podczas warsztatu",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Logo i branding”; zachować tę główną intencję."
  },
  "5417": {
    "category_slug": "logo-branding",
    "expected_title": "Jak wymyślić nazwę firmy? Metody, przykłady i co sprawdzić przed wyborem",
    "expected_status": "publish",
    "expected_slug": "jak-wymyslic-nazwe-firmy",
    "expected_date": "2026-10-03 20:30:41",
    "expected_featured_id": 6096,
    "featured_id": 6096,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-153e92c1-2eb-cover-webp37.webp",
    "facebook_image_id": 6096,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-153e92c1-2eb-cover-webp37.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "Próba wymowy kandydatów na nazwę firmy",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Logo i branding”; zachować tę główną intencję."
  },
  "5421": {
    "category_slug": "logo-branding",
    "expected_title": "Strategia marki – jak ją zbudować? Elementy, przykład i plan",
    "expected_status": "publish",
    "expected_slug": "strategia-marki",
    "expected_date": "2026-10-03 20:30:42",
    "expected_featured_id": 6035,
    "featured_id": 6035,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-fc093b11-a5b-cover-webp37.webp",
    "facebook_image_id": 6035,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-fc093b11-a5b-cover-webp37.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "Warsztat strategii marki przy mapie odbiorców i potrzeb",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Logo i branding”; zachować tę główną intencję."
  },
  "5425": {
    "category_slug": "strony-internetowe",
    "expected_title": "Rodzaje stron internetowych – który wybrać dla firmy?",
    "expected_status": "publish",
    "expected_slug": "rodzaje-stron-internetowych",
    "expected_date": "2026-10-03 20:30:43",
    "expected_featured_id": 6032,
    "featured_id": 6032,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-b2c4ffd6-d79-cover-webp37.webp",
    "facebook_image_id": 6032,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-b2c4ffd6-d79-cover-webp37.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "Porównanie układu strony usługowej, katalogu i rozbudowanego serwisu",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Strony internetowe”; zachować tę główną intencję."
  },
  "5428": {
    "category_slug": "strony-internetowe",
    "expected_title": "Makieta strony internetowej – wireframe, mockup i prototyp",
    "expected_status": "publish",
    "expected_slug": "makieta-strony-internetowej",
    "expected_date": "2026-10-03 20:30:45",
    "expected_featured_id": 5929,
    "featured_id": 5929,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-1f1bb106-b09-cover-webp37.webp",
    "facebook_image_id": 5929,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-1f1bb106-b09-cover-webp37.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "Układanie papierowej makiety strony na pionowej tablicy",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Strony internetowe”; zachować tę główną intencję."
  },
  "5564": {
    "category_slug": "sklepy-internetowe",
    "expected_title": "Bramka płatnicza w sklepie internetowym – jak wybrać i podłączyć do WooCommerce",
    "expected_status": "publish",
    "expected_slug": "bramka-platnicza",
    "expected_date": "2026-10-07 19:11:46",
    "expected_featured_id": 5926,
    "featured_id": 5926,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-b98c92c3-606-cover-webp37.webp",
    "facebook_image_id": 5926,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-b98c92c3-606-cover-webp37.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "Wybór sposobu płatności podczas zakupu na telefonie",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Sklepy internetowe”; zachować tę główną intencję."
  },
  "5568": {
    "category_slug": "logo-branding",
    "expected_title": "Logo salonu kosmetycznego – style, kolory i przykłady z naszych realizacji",
    "expected_status": "publish",
    "expected_slug": "logo-salonu-kosmetycznego",
    "expected_date": "2026-10-07 19:11:47",
    "expected_featured_id": 5924,
    "featured_id": 5924,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-4ae5ab60-2ee-cover-webp37.webp",
    "facebook_image_id": 5924,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-4ae5ab60-2ee-cover-webp37.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "Przykładowe oznakowanie wejścia do salonu kosmetycznego",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Logo i branding”; zachować tę główną intencję."
  },
  "5572": {
    "category_slug": "logo-branding",
    "expected_title": "Projekt wizytówki firmowej – wymiary, treść i przygotowanie do druku",
    "expected_status": "publish",
    "expected_slug": "projekt-wizytowki",
    "expected_date": "2026-10-07 19:11:49",
    "expected_featured_id": 5922,
    "featured_id": 5922,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-73af7970-1a0-cover-webp37.webp",
    "facebook_image_id": 5922,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-73af7970-1a0-cover-webp37.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "Kontrola wymiarów wizytówek przy gilotynie drukarskiej",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Logo i branding”; zachować tę główną intencję."
  },
  "5576": {
    "category_slug": "sklepy-internetowe",
    "expected_title": "Sklep B2B i hurtownia internetowa – funkcje, platformy i wdrożenie",
    "expected_status": "publish",
    "expected_slug": "sklep-b2b",
    "expected_date": "2026-10-07 19:11:50",
    "expected_featured_id": 5920,
    "featured_id": 5920,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-48c0136c-d91-cover-webp37.webp",
    "facebook_image_id": 5920,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-48c0136c-d91-cover-webp37.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "Kompletowanie zbiorczego zamówienia na regałach hurtowni",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Sklepy internetowe”; zachować tę główną intencję."
  },
  "5579": {
    "category_slug": "strony-internetowe",
    "expected_title": "System rezerwacji online na stronie internetowej – jak wybrać i wdrożyć",
    "expected_status": "publish",
    "expected_slug": "system-rezerwacji-online",
    "expected_date": "2026-10-07 19:11:51",
    "expected_featured_id": 5918,
    "featured_id": 5918,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-4ccc410c-bdb-cover-webp37.webp",
    "facebook_image_id": 5918,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-4ccc410c-bdb-cover-webp37.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "Potwierdzanie terminu wizyty przy recepcji punktu usługowego",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Strony internetowe”; zachować tę główną intencję."
  },
  "5597": {
    "category_slug": "logo-branding",
    "expected_title": "Papier firmowy – co powinien zawierać, jakie ma wymiary i jak go zaprojektować",
    "expected_status": "publish",
    "expected_slug": "papier-firmowy",
    "expected_date": "2026-10-15 08:00:00",
    "expected_featured_id": 5916,
    "featured_id": 5916,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-a79fc03b-93f-cover-webp37.webp",
    "facebook_image_id": 5916,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-a79fc03b-93f-cover-webp37.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "Arkusze papieru firmowego wychodzące z maszyny drukarskiej",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Logo i branding”; zachować tę główną intencję."
  },
  "5704": {
    "category_slug": "seo-i-konwersja",
    "expected_title": "Pozycjonowanie w AI: jak przygotować firmową stronę do odpowiedzi AI",
    "expected_status": "publish",
    "expected_slug": "pozycjonowanie-w-ai",
    "expected_date": "2026-10-09 18:44:46",
    "expected_featured_id": 5656,
    "featured_id": 5656,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/ile-kosztuje-domena-okladka.webp",
    "facebook_image_id": 5656,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/ile-kosztuje-domena-okladka.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "Strona internetowa Kancelarii Siemianowski na laptopie, realizacja Zaprojektowani.",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „SEO i konwersja”; zachować tę główną intencję."
  },
  "5788": {
    "category_slug": "logo-branding",
    "expected_title": "Logo na koszulce: kiedy uprościć znak do haftu lub nadruku?",
    "expected_status": "publish",
    "expected_slug": "logo-na-koszulce-kiedy-uproscic-do-haftu-lub-nadruku",
    "expected_date": "2026-10-10 21:07:59",
    "expected_featured_id": 6006,
    "featured_id": 6006,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-published-5788-c88c3412-ac8-cover.webp",
    "facebook_image_id": 6006,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-published-5788-c88c3412-ac8-cover.webp",
    "preserve_existing_facebook": true,
    "featured_alt": "Dwie koszulki z próbkami drobnego i uproszczonego geometrycznego nadruku.",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Logo i branding”; zachować tę główną intencję."
  },
  "5792": {
    "category_slug": "logo-branding",
    "expected_title": "Teczki z logo firmy: jak zamówić projekt dopasowany do dokumentów, kieszeni i wykrojnika drukarni",
    "expected_status": "publish",
    "expected_slug": "teczki-z-logo-firmy-projekt-dokumenty-kieszenie-wykrojnik-drukarni",
    "expected_date": "2026-10-10 20:30:48",
    "expected_featured_id": 6099,
    "featured_id": 6099,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-published-compressed-5792-369e6c46d9de-cover.webp",
    "facebook_image_id": 6099,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-published-compressed-5792-369e6c46d9de-cover.webp",
    "preserve_existing_facebook": true,
    "featured_alt": "Teczka z geometrycznym nadrukiem i rozłożonym wykrojnikiem obok dokumentów.",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Logo i branding”; zachować tę główną intencję."
  },
  "5831": {
    "category_slug": "strony-internetowe",
    "expected_title": "Strona firmowa na Facebooku czy własna witryna: kiedy potrzebujesz obu i jakie zadania im przypisać?",
    "expected_status": "publish",
    "expected_slug": "strona-firmowa-na-facebooku-czy-wlasna-witryna-podzial-zadan",
    "expected_date": "2026-10-11 18:00:00",
    "expected_featured_id": 6036,
    "featured_id": 6036,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-compressed-5831-b8e39338ba09-cover.webp",
    "facebook_image_id": 6036,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-compressed-5831-b8e39338ba09-cover.webp",
    "preserve_existing_facebook": true,
    "featured_alt": "Karty ze zdjęciami ceramiki porządkowane w segregatorze z przekładkami podczas planowania struktury oferty.",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Strony internetowe”; zachować tę główną intencję."
  },
  "5833": {
    "category_slug": "logo-branding",
    "expected_title": "Logo firmy odzieżowej: jak rozdzielić znak marki i oznaczenia kolekcji na metkach oraz w sklepie?",
    "expected_status": "publish",
    "expected_slug": "logo-firmy-odziezowej-marka-i-kolekcje-na-metkach-oraz-w-sklepie",
    "expected_date": "2026-10-12 18:00:00",
    "expected_featured_id": 6039,
    "featured_id": 6039,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-compressed-5833-baa43657da49-cover.webp",
    "facebook_image_id": 6039,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-compressed-5833-baa43657da49-cover.webp",
    "preserve_existing_facebook": true,
    "featured_alt": "Dwie wszyte metki z geometrycznym znakiem marki i oznaczeniem kolekcji o różnej wielkości i położeniu.",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Logo i branding”; zachować tę główną intencję."
  },
  "5835": {
    "category_slug": "logo-branding",
    "expected_title": "Gadżety z logo firmy mają różne kolory: jak porównać próbki i ustalić, czy poprawić pliki czy wykonanie?",
    "expected_status": "publish",
    "expected_slug": "gadzety-z-logo-firmy-rozne-kolory-porownanie-probek",
    "expected_date": "2026-10-13 18:00:00",
    "expected_featured_id": 6043,
    "featured_id": 6043,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-compressed-5835-72ba1695d858-cover.webp",
    "facebook_image_id": 6043,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-compressed-5835-72ba1695d858-cover.webp",
    "preserve_existing_facebook": true,
    "featured_alt": "Dwa kremowe notesy z próbnym nadrukiem geometrycznego znaku w różnych odcieniach niebieskiego, porównywane obok siebie.",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Logo i branding”; zachować tę główną intencję."
  },
  "5837": {
    "category_slug": "logo-branding",
    "expected_title": "Kalendarze z logo firmy: jak zaplanować miejsce na notatki, czytelne daty i kontrolę treści przed drukiem?",
    "expected_status": "publish",
    "expected_slug": "kalendarze-z-logo-firmy-notatki-daty-kontrola-przed-drukiem",
    "expected_date": "2026-10-10 20:40:27",
    "expected_featured_id": 6119,
    "featured_id": 6119,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-published-compressed-5837-a01e1f424ab7-cover.webp",
    "facebook_image_id": 6119,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-published-compressed-5837-a01e1f424ab7-cover.webp",
    "preserve_existing_facebook": true,
    "featured_alt": "Lupa na próbnym wydruku siatki kalendarza z geometrycznym znakiem i miejscem na odręczne notatki.",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Logo i branding”; zachować tę główną intencję."
  },
  "5965": {
    "category_slug": "kampanie-reklamowe",
    "expected_title": "Pierwsza kampania Google Ads: jakie dane o ofercie, budżecie i obsłudze leadów przekazać przed uruchomieniem?",
    "expected_status": "publish",
    "expected_slug": "pierwsza-kampania-google-ads-dane-do-briefu-przed-uruchomieniem",
    "expected_date": "2026-10-14 18:00:00",
    "expected_featured_id": 6080,
    "featured_id": 6080,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-compressed-5965-81803aa379b7-cover.webp",
    "facebook_image_id": 6080,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-compressed-5965-81803aa379b7-cover.webp",
    "preserve_existing_facebook": true,
    "featured_alt": "Papierowy planer z drewnianymi żetonami pokazującymi podział budżetu między dni i osobną pulą miesięczną.",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Kampanie reklamowe”; zachować tę główną intencję.",
    "seo_meta": {
      "rank_math_focus_keyword": "pierwsza kampania Google Ads, google ads jak zacząć, brief do kampanii Google Ads",
      "rank_math_title": "Pierwsza kampania Google Ads: dane do briefu i pomiaru",
      "rank_math_description": "Pierwsza kampania Google Ads wymaga danych o ofercie, budżecie i obsłudze leadów. Sprawdź, co przekazać w briefie przed uruchomieniem reklam."
    },
    "expected_seo_meta": {
      "rank_math_focus_keyword": [
        "google ads jak zacząć, brief do kampanii Google Ads, pierwsza kampania Google Ads"
      ],
      "rank_math_title": [
        "Pierwsza kampania Google Ads: dane do briefu przed uruchomieniem"
      ],
      "rank_math_description": [
        "Sprawdź, jakie dane o ofercie, budżecie, pomiarze i obsłudze leadów przygotować w briefie przed zleceniem i uruchomieniem pierwszej kampanii Google Ads."
      ]
    },
    "seo_meta_en": {
      "rank_math_title": "Your first Google Ads campaign: data for the brief and measurement",
      "rank_math_description": "Your first Google Ads campaign needs information about your offer, budget and lead handling. Find out what to include in the brief before launching ads."
    }
  },
  "5601": {
    "category_slug": "strony-internetowe",
    "expected_title": "Kreatory stron internetowych i AI – kiedy wystarczą, a kiedy zamówić projekt strony",
    "expected_status": "future",
    "expected_slug": "kreatory-stron-internetowych",
    "expected_date": "2026-10-19 08:00:00",
    "expected_featured_id": 5912,
    "featured_id": 5912,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-5be659f9-9cf-cover-webp37.webp",
    "facebook_image_id": 5912,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-5be659f9-9cf-cover-webp37.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "Porównanie modułowego układu strony z projektem przygotowanym indywidualnie",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Strony internetowe”; zachować tę główną intencję."
  },
  "5605": {
    "category_slug": "logo-branding",
    "expected_title": "Mockup – co to jest i jak pokazać logo w prawdziwym otoczeniu",
    "expected_status": "future",
    "expected_slug": "mockup-co-to-jest",
    "expected_date": "2026-10-21 08:00:00",
    "expected_featured_id": 5908,
    "featured_id": 5908,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-d1a39932-350-cover-webp37.webp",
    "facebook_image_id": 5908,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-d1a39932-350-cover-webp37.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "Ten sam prosty znak na pudełku, torbie i tkaninie",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Logo i branding”; zachować tę główną intencję."
  },
  "5609": {
    "category_slug": "sklepy-internetowe",
    "expected_title": "SEO sklepu internetowego – jak przygotować WooCommerce pod Google już przy budowie",
    "expected_status": "future",
    "expected_slug": "seo-sklepu-internetowego",
    "expected_date": "2026-10-23 08:00:00",
    "expected_featured_id": 5904,
    "featured_id": 5904,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-3afd8524-e63-cover-webp37.webp",
    "facebook_image_id": 5904,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-3afd8524-e63-cover-webp37.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "Porządkowanie produktów w czytelne grupy asortymentowe",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Sklepy internetowe”; zachować tę główną intencję."
  },
  "5613": {
    "category_slug": "logo-branding",
    "expected_title": "Logo firmy budowlanej – jak zaprojektować znak widoczny na budowie i w ofercie",
    "expected_status": "future",
    "expected_slug": "logo-firmy-budowlanej",
    "expected_date": "2026-10-27 08:00:00",
    "expected_featured_id": 5899,
    "featured_id": 5899,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-46e2a79d-db0-cover-webp37.webp",
    "facebook_image_id": 5899,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-46e2a79d-db0-cover-webp37.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "Oznakowanie przy wejściu na budowę i kaski robocze",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Logo i branding”; zachować tę główną intencję."
  },
  "5657": {
    "category_slug": "strony-internetowe",
    "expected_title": "Ile kosztuje domena i hosting dla firmy i jak je dobrze wybrać",
    "expected_status": "future",
    "expected_slug": "ile-kosztuje-domena-i-hosting",
    "expected_date": "2026-10-30 08:00:00",
    "expected_featured_id": 5895,
    "featured_id": 5895,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-68719d34-e92-cover-webp37.webp",
    "facebook_image_id": 5895,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-68719d34-e92-cover-webp37.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "Kontrola serwerów i połączeń w pomieszczeniu technicznym",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Strony internetowe”; zachować tę główną intencję."
  },
  "5659": {
    "category_slug": "sklepy-internetowe",
    "expected_title": "InPost w WooCommerce i inne integracje sklepu – kurierzy, Allegro, BaseLinker i faktury",
    "expected_status": "future",
    "expected_slug": "inpost-woocommerce-integracje-sklepu",
    "expected_date": "2026-11-03 08:00:00",
    "expected_featured_id": 5891,
    "featured_id": 5891,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-bfee8f3e-01f-cover-webp37.webp",
    "facebook_image_id": 5891,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-bfee8f3e-01f-cover-webp37.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "Przygotowanie paczek do przekazania przewoźnikowi",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Sklepy internetowe”; zachować tę główną intencję."
  },
  "5661": {
    "category_slug": "strony-internetowe",
    "expected_title": "Nowoczesne strony internetowe – cechy, przykłady i jak sprawdzić swoją stronę",
    "expected_status": "future",
    "expected_slug": "nowoczesne-strony-internetowe",
    "expected_date": "2026-11-06 08:00:00",
    "expected_featured_id": 5887,
    "featured_id": 5887,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-43a92182-751-cover-webp37.webp",
    "facebook_image_id": 5887,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-43a92182-751-cover-webp37.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "Sprawdzanie czytelności mobilnego interfejsu poza biurem",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Strony internetowe”; zachować tę główną intencję."
  },
  "5663": {
    "category_slug": "logo-branding",
    "expected_title": "Logo restauracji i kawiarni – jak zaprojektować znak na szyld, menu i kubek na wynos",
    "expected_status": "future",
    "expected_slug": "logo-restauracji-i-kawiarni",
    "expected_date": "2026-11-10 08:00:00",
    "expected_featured_id": 5883,
    "featured_id": 5883,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-4812e091-298-cover-webp37.webp",
    "facebook_image_id": 5883,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-4812e091-298-cover-webp37.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "Szyld z prostym motywem filiżanki przy wejściu do kawiarni",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Logo i branding”; zachować tę główną intencję."
  },
  "5665": {
    "category_slug": "seo-i-konwersja",
    "expected_title": "Core Web Vitals i PageSpeed Insights: jak sprawdzić i przyspieszyć stronę firmy",
    "expected_status": "future",
    "expected_slug": "core-web-vitals",
    "expected_date": "2026-11-13 08:00:00",
    "expected_featured_id": 5878,
    "featured_id": 5878,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-5a0bc5de-757-cover-webp37.webp",
    "facebook_image_id": 5878,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-5a0bc5de-757-cover-webp37.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "Sprawdzanie ładowania strony na komputerze i telefonie",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „SEO i konwersja”; zachować tę główną intencję."
  },
  "5706": {
    "category_slug": "sklepy-internetowe",
    "expected_title": "Google Merchant Center i WooCommerce: jak naprawić feed produktów",
    "expected_status": "future",
    "expected_slug": "google-merchant-center-woocommerce-feed-produktow",
    "expected_date": "2026-10-17 08:00:00",
    "expected_featured_id": 5914,
    "featured_id": 5914,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-87ae91f7-44f-cover-webp37.webp",
    "facebook_image_id": 5914,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-87ae91f7-44f-cover-webp37.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "Sprawdzanie zgodności opakowania i rzeczywistego produktu",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Sklepy internetowe”; zachować tę główną intencję."
  },
  "5708": {
    "category_slug": "logo-branding",
    "expected_title": "Logo gabinetu stomatologicznego: od pomysłu do szyldu i dokumentów",
    "expected_status": "future",
    "expected_slug": "logo-gabinetu-stomatologicznego",
    "expected_date": "2026-10-20 08:00:00",
    "expected_featured_id": 5910,
    "featured_id": 5910,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-1513ee6f-cfa-cover-webp37.webp",
    "facebook_image_id": 5910,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-1513ee6f-cfa-cover-webp37.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "Szklane drzwi gabinetu z prostym znakiem zęba",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Logo i branding”; zachować tę główną intencję."
  },
  "5710": {
    "category_slug": "strony-internetowe",
    "expected_title": "WordPress nie wysyła maili: diagnoza formularza i dostarczania",
    "expected_status": "future",
    "expected_slug": "wordpress-nie-wysyla-maili",
    "expected_date": "2026-10-22 08:00:00",
    "expected_featured_id": 5906,
    "featured_id": 5906,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-a30ab940-060-cover-webp37.webp",
    "facebook_image_id": 5906,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-a30ab940-060-cover-webp37.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "Próba wysłania wiadomości z formularza kontaktowego",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Strony internetowe”; zachować tę główną intencję."
  },
  "5712": {
    "category_slug": "sklepy-internetowe",
    "expected_title": "Sprzedaż produktów cyfrowych w WooCommerce: pliki, płatności i dostęp",
    "expected_status": "future",
    "expected_slug": "sprzedaz-produktow-cyfrowych-woocommerce",
    "expected_date": "2026-10-25 08:00:00",
    "expected_featured_id": 5902,
    "featured_id": 5902,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-eae2a628-8d2-cover-webp37.webp",
    "facebook_image_id": 5902,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-eae2a628-8d2-cover-webp37.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "Korzystanie z materiału audio podczas spaceru",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Sklepy internetowe”; zachować tę główną intencję."
  },
  "5714": {
    "category_slug": "logo-branding",
    "expected_title": "Logo salonu fryzjerskiego: znak na szyld, lustro i kartę wizyty",
    "expected_status": "future",
    "expected_slug": "logo-salonu-fryzjerskiego",
    "expected_date": "2026-10-28 08:00:00",
    "expected_featured_id": 5897,
    "featured_id": 5897,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-b2e2c500-83b-cover-webp37.webp",
    "facebook_image_id": 5897,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-b2e2c500-83b-cover-webp37.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "Oznakowanie witryny salonu fryzjerskiego przy wejściu",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Logo i branding”; zachować tę główną intencję.",
    "seo_meta": {
      "rank_math_title": "Logo salonu fryzjerskiego: jak sprawdzić znak przed wdrożeniem",
      "rank_math_description": "Logo salonu fryzjerskiego powinno działać na szyldzie, lustrze i karcie wizyty. Sprawdź czytelność, skalowanie i pliki przed akceptacją projektu."
    },
    "expected_seo_meta": {
      "rank_math_title": [
        "Logo salonu fryzjerskiego: jak sprawdzić projekt"
      ],
      "rank_math_description": [
        "Logo fryzjera powinno działać na szyldzie, w rezerwacjach i na karcie wizyty. Sprawdź brief, wersje znaku oraz testy przed drukiem i montażem."
      ]
    },
    "seo_meta_en": {
      "rank_math_title": "A hair salon logo: checking the design before implementation",
      "rank_math_description": "A hair salon logo needs to work on signage, mirrors and business cards. Check readability, scaling and the required files before approving the design."
    }
  },
  "5716": {
    "category_slug": "strony-internetowe",
    "expected_title": "Strona wielojęzyczna w WordPressie: języki, adresy i obsługa",
    "expected_status": "future",
    "expected_slug": "strona-wielojezyczna-wordpress",
    "expected_date": "2026-11-01 08:00:00",
    "expected_featured_id": 5893,
    "featured_id": 5893,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-f78c3144-ff8-cover-webp37.webp",
    "facebook_image_id": 5893,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-f78c3144-ff8-cover-webp37.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "Porównywanie równoległych wersji językowych tego samego układu",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Strony internetowe”; zachować tę główną intencję.",
    "seo_meta": {
      "rank_math_focus_keyword": "strona wielojęzyczna, wordpress wielojęzyczna strona, strona wielojęzyczna wordpress, tłumaczenie strony wordpress, wersje językowe strony, hreflang wordpress",
      "rank_math_title": "Strona wielojęzyczna w WordPressie: języki, adresy i SEO",
      "rank_math_description": "Strona wielojęzyczna w WordPressie wymaga planu adresów, tłumaczeń i aktualizacji. Sprawdź przełącznik języka, hreflang oraz obsługę treści po wdrożeniu."
    },
    "expected_seo_meta": {
      "rank_math_focus_keyword": [
        "wordpress wielojęzyczna strona, strona wielojęzyczna wordpress, tłumaczenie strony wordpress, wersje językowe strony, hreflang wordpress"
      ],
      "rank_math_title": [
        "Strona wielojęzyczna WordPress: jak ją zaplanować"
      ],
      "rank_math_description": [
        "Jak zaplanować wielojęzyczny WordPress? Sprawdź strukturę adresów, tłumaczenia, przełącznik języka, SEO i aktualizowanie treści po wdrożeniu."
      ]
    },
    "seo_meta_en": {
      "rank_math_title": "A multilingual WordPress website: languages, URLs and SEO",
      "rank_math_description": "A multilingual WordPress website needs a plan for URLs, translations and updates. Check the language switcher, hreflang and ongoing content management."
    }
  },
  "5718": {
    "category_slug": "sklepy-internetowe",
    "expected_title": "Konfigurator produktu w WooCommerce: warianty, dopłaty i zamówienia",
    "expected_status": "future",
    "expected_slug": "konfigurator-produktu-woocommerce",
    "expected_date": "2026-11-04 08:00:00",
    "expected_featured_id": 5889,
    "featured_id": 5889,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-53d6be48-7fc-cover-webp37.webp",
    "facebook_image_id": 5889,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-53d6be48-7fc-cover-webp37.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "Dobór tkaniny i drewnianych nóg przy konfigurowanym fotelu",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Sklepy internetowe”; zachować tę główną intencję."
  },
  "5720": {
    "category_slug": "logo-branding",
    "expected_title": "Projekt ulotki firmowej: treść, format i przygotowanie do druku",
    "expected_status": "future",
    "expected_slug": "projekt-ulotki-firmowej",
    "expected_date": "2026-11-08 08:00:00",
    "expected_featured_id": 5885,
    "featured_id": 5885,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-d9221258-8b8-cover-webp37.webp",
    "facebook_image_id": 5885,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-d9221258-8b8-cover-webp37.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "Składanie ulotki i kontrola jej formatu przy maszynie drukarskiej",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Logo i branding”; zachować tę główną intencję."
  },
  "5722": {
    "category_slug": "strony-internetowe",
    "expected_title": "Strona O nas: co napisać i jak pokazać firmę wiarygodnie",
    "expected_status": "future",
    "expected_slug": "strona-o-nas",
    "expected_date": "2026-11-11 08:00:00",
    "expected_featured_id": 5880,
    "featured_id": 5880,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-a5ba4796-3fc-cover-webp37.webp",
    "facebook_image_id": 5880,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-a5ba4796-3fc-cover-webp37.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "Przykładowa scena pracy zespołu w przestrzeni warsztatowej",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Strony internetowe”; zachować tę główną intencję."
  },
  "5955": {
    "category_slug": "kampanie-reklamowe",
    "expected_title": "Facebook Lead Ads: jak zbierać zapytania od klientów z reklam",
    "expected_status": "future",
    "expected_slug": "facebook-lead-ads-nietrafione-kontakty-pytania-kwalifikujace",
    "expected_date": "2026-11-12 18:00:00",
    "expected_featured_id": 6065,
    "featured_id": 6065,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-compressed-5955-364293f32ed8-cover.webp",
    "facebook_image_id": 6065,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-compressed-5955-364293f32ed8-cover.webp",
    "preserve_existing_facebook": true,
    "featured_alt": "Tablica z kartami przedstawiającymi potrzebę, wielkość grupy i termin oraz połączeniami wskazującymi kolejne decyzje kwalifikacji.",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "consolidate_duplicate_labels_or_choose_primary",
    "rationale": "Tytuł i nagłówki opisują temat „Kampanie reklamowe”; zachować tę główną intencję.",
    "seo_meta": {
      "rank_math_focus_keyword": "Facebook Lead Ads, leads facebook ads, pytania kwalifikujące Lead Ads, jakość zgłoszeń Facebook",
      "rank_math_title": "Facebook Lead Ads: jak zbierać i kwalifikować zapytania",
      "rank_math_description": "Facebook Lead Ads pomaga zbierać zapytania z reklam na Facebooku i Instagramie. Sprawdź formularz, pytania kwalifikujące oraz obsługę i jakość kontaktów."
    },
    "expected_seo_meta": {
      "rank_math_focus_keyword": [
        "leads facebook ads, pytania kwalifikujące Lead Ads, jakość zgłoszeń Facebook"
      ],
      "rank_math_title": [
        "Facebook Lead Ads: jak zbierać zapytania od klientów"
      ],
      "rank_math_description": [
        "Facebook Lead Ads w praktyce: jak zbierać zapytania od klientów z reklam na Facebooku i Instagramie. Poradnik dla firm, które chcą więcej kontaktów z kampanii."
      ]
    },
    "seo_meta_en": {
      "rank_math_title": "Facebook Lead Ads: how to collect and qualify enquiries",
      "rank_math_description": "Facebook Lead Ads collects enquiries from Facebook and Instagram advertising. Review the form, qualifying questions, lead handling and contact quality."
    }
  },
  "5967": {
    "category_slug": "kampanie-reklamowe",
    "expected_title": "Audyt Google Ads: co sprawdzić na koncie i w kampaniach, zanim dołożysz budżet",
    "expected_status": "future",
    "expected_slug": "audyt-google-ads-po-zmianie-oferty",
    "expected_date": "2026-10-18 18:00:00",
    "expected_featured_id": 6083,
    "featured_id": 6083,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-compressed-5967-7300b17a73f3-cover.webp",
    "facebook_image_id": 6083,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-compressed-5967-7300b17a73f3-cover.webp",
    "preserve_existing_facebook": true,
    "featured_alt": "Wydruki tekstów reklam porównywane z aktualną ofertą, oznaczone zakładkami i śladami korekty ołówkiem.",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Kampanie reklamowe”; zachować tę główną intencję."
  },
  "5724": {
    "category_slug": "sklepy-internetowe",
    "expected_title": "WooCommerce Subskrypcje: jak zaplanować płatności cykliczne",
    "expected_status": "future",
    "expected_slug": "woocommerce-subskrypcje-platnosci-cykliczne",
    "expected_date": "2026-11-17 08:00:00",
    "expected_featured_id": 5876,
    "featured_id": 5876,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-f40d917c-472-cover-webp37.webp",
    "facebook_image_id": 5876,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-f40d917c-472-cover-webp37.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "Kompletowanie pudełka regularnej dostawy produktów",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Sklepy internetowe”; zachować tę główną intencję."
  },
  "5726": {
    "category_slug": "strony-internetowe",
    "expected_title": "Dostępność stron internetowych: jak sprawdzić projekt i odbiór",
    "expected_status": "future",
    "expected_slug": "dostepnosc-stron-internetowych",
    "expected_date": "2026-11-20 08:00:00",
    "expected_featured_id": 5874,
    "featured_id": 5874,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-1ac3dc4d-f46-cover-webp37.webp",
    "facebook_image_id": 5874,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-1ac3dc4d-f46-cover-webp37.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "Testowanie obsługi strony za pomocą klawiatury",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Strony internetowe”; zachować tę główną intencję."
  },
  "5728": {
    "category_slug": "logo-branding",
    "expected_title": "Prezentacja firmowa: jak ułożyć slajdy, które pomagają w rozmowie",
    "expected_status": "future",
    "expected_slug": "prezentacja-firmowa",
    "expected_date": "2026-11-24 08:00:00",
    "expected_featured_id": 5872,
    "featured_id": 5872,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-d0a9c32f-b5d-cover-webp37.webp",
    "facebook_image_id": 5872,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-d0a9c32f-b5d-cover-webp37.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "Rozmowa przy prezentacji firmowej w sali spotkań",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Logo i branding”; zachować tę główną intencję."
  },
  "5730": {
    "category_slug": "sklepy-internetowe",
    "expected_title": "Zdjęcia produktowe do sklepu: brief, galeria i przygotowanie plików",
    "expected_status": "future",
    "expected_slug": "zdjecia-produktowe-do-sklepu-brief-galeria",
    "expected_date": "2026-11-27 08:00:00",
    "expected_featured_id": 5870,
    "featured_id": 5870,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-63db322c-b0f-cover-webp37.webp",
    "facebook_image_id": 5870,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-63db322c-b0f-cover-webp37.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "Przygotowanie ceramicznego produktu do zdjęcia z dwoma softboksami",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Sklepy internetowe”; zachować tę główną intencję."
  },
  "5732": {
    "category_slug": "strony-internetowe",
    "expected_title": "Bezpieczeństwo WordPressa: dostępy, aktualizacje i reakcja",
    "expected_status": "future",
    "expected_slug": "bezpieczenstwo-wordpress-firma",
    "expected_date": "2026-12-01 08:00:00",
    "expected_featured_id": 5868,
    "featured_id": 5868,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-a402f92b-14b-cover-webp37.webp",
    "facebook_image_id": 5868,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-a402f92b-14b-cover-webp37.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "Klucz sprzętowy używany przy zabezpieczonej szafie serwerowej",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Strony internetowe”; zachować tę główną intencję."
  },
  "5734": {
    "category_slug": "sklepy-internetowe",
    "expected_title": "WooCommerce kupony: jak ustawić rabaty, limity i testy promocji",
    "expected_status": "future",
    "expected_slug": "woocommerce-kupony-rabaty-limity-testy",
    "expected_date": "2026-12-04 08:00:00",
    "expected_featured_id": 5866,
    "featured_id": 5866,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-c588d5c3-f55-cover-webp37.webp",
    "facebook_image_id": 5866,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-c588d5c3-f55-cover-webp37.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "Testowanie pola kuponu w koszyku sklepu internetowego",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Sklepy internetowe”; zachować tę główną intencję."
  },
  "5957": {
    "category_slug": "kampanie-reklamowe",
    "expected_title": "Piksel Meta (Facebook Pixel): do czego służy w reklamach na Facebooku i Instagramie",
    "expected_status": "future",
    "expected_slug": "meta-pixel-wiecej-zakupow-niz-sklep-duplikaty-conversions-api",
    "expected_date": "2026-11-14 18:00:00",
    "expected_featured_id": 6068,
    "featured_id": 6068,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-compressed-5957-06732292c0d8-cover.webp",
    "facebook_image_id": 6068,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-compressed-5957-06732292c0d8-cover.webp",
    "preserve_existing_facebook": true,
    "featured_alt": "Jedna paczka i dwa identyczne znaczniki w neutralnym modelu porównywania dwóch zapisów tego samego zamówienia.",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "consolidate_duplicate_labels_or_choose_primary",
    "rationale": "Tytuł i nagłówki opisują temat „Kampanie reklamowe”; zachować tę główną intencję.",
    "seo_meta": {
      "rank_math_focus_keyword": "Meta Pixel, do czego służy pixel systemu facebook ads, podwójne zdarzenia zakupu, deduplikacja Meta Pixel, Conversions API",
      "rank_math_title": "Meta Pixel (Piksel Meta): pomiar i deduplikacja zdarzeń",
      "rank_math_description": "Meta Pixel łączy zdarzenia na stronie z reklamami na Facebooku i Instagramie. Sprawdź pomiar, deduplikację oraz współpracę z Conversions API."
    },
    "expected_seo_meta": {
      "rank_math_focus_keyword": [
        "do czego służy pixel systemu facebook ads, podwójne zdarzenia zakupu, deduplikacja Meta Pixel, Conversions API"
      ],
      "rank_math_title": [
        "Piksel Meta (Facebook Pixel): do czego służy w reklamach"
      ],
      "rank_math_description": [
        "Piksel Meta (Facebook Pixel): do czego służy w reklamach na Facebooku i Instagramie i dlaczego bez niego kampania gorzej trafia do klientów. Poradnik dla firm."
      ]
    },
    "seo_meta_en": {
      "rank_math_title": "Meta Pixel: measurement and event deduplication",
      "rank_math_description": "Meta Pixel connects website events with Facebook and Instagram advertising. Learn about measurement, deduplication and working with the Conversions API."
    }
  },
  "5959": {
    "category_slug": "kampanie-reklamowe",
    "expected_title": "Remarketing Google Ads nie wyświetla reklam: jak odróżnić problem zbierania odbiorców od braku gotowości listy?",
    "expected_status": "future",
    "expected_slug": "remarketing-google-ads-brak-reklam-zbieranie-odbiorcow-gotowosc-listy",
    "expected_date": "2026-11-15 18:00:00",
    "expected_featured_id": 6071,
    "featured_id": 6071,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-compressed-5959-dd8356913563-cover.webp",
    "facebook_image_id": 6071,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-compressed-5959-dd8356913563-cover.webp",
    "preserve_existing_facebook": true,
    "featured_alt": "Model przepływu odbiorców: znaczniki w pierwszym pojemniku i puste kolejne pola przed potwierdzeniem zbierania.",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Kampanie reklamowe”; zachować tę główną intencję.",
    "seo_meta": {
      "rank_math_focus_keyword": "remarketing Google Ads, google ads remarketing, remarketing nie wyświetla reklam, diagnostyka remarketingu",
      "rank_math_title": "Remarketing Google Ads nie działa: odbiorcy i gotowość listy",
      "rank_math_description": "Remarketing Google Ads nie wyświetla reklam? Sprawdź zbieranie odbiorców, gotowość listy i ustawienia kampanii przed wprowadzaniem zmian."
    },
    "expected_seo_meta": {
      "rank_math_focus_keyword": [
        "google ads remarketing, remarketing nie wyświetla reklam, diagnostyka remarketingu"
      ],
      "rank_math_title": [
        "Remarketing Google Ads nie działa: zbieranie czy gotowość listy?"
      ],
      "rank_math_description": [
        "Remarketing Google Ads nie wyświetla reklam? Sprawdź, jak rozdzielić problem zbierania odbiorców, gotowości listy i ustawień kampanii przed zmianami."
      ]
    },
    "seo_meta_en": {
      "rank_math_title": "Google Ads remarketing not working: audiences and list readiness",
      "rank_math_description": "Google Ads remarketing not showing ads? Check audience collection, list readiness and campaign settings before making changes."
    }
  },
  "6135": {
    "category_slug": "strony-internetowe",
    "expected_title": "WordPress Multisite czy osobne instalacje: kto odpowiada za utrzymanie kilku witryn?",
    "expected_status": "future",
    "expected_slug": "wordpress-multisite-czy-osobne-instalacje-odpowiedzialnosc-za-utrzymanie",
    "expected_date": "2026-11-19 18:00:00",
    "expected_featured_id": 6126,
    "featured_id": 6126,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-poradnik-57320c26-bb3-cover.webp",
    "facebook_image_id": 6126,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-poradnik-57320c26-bb3-cover.webp",
    "preserve_existing_facebook": true,
    "featured_alt": "Dłoń technika przekręcająca klucz w zamku szafy serwerowej z widocznymi urządzeniami i przewodami.",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Strony internetowe”; zachować tę główną intencję."
  },
  "6137": {
    "category_slug": "strony-internetowe",
    "expected_title": "SSL w WordPressie po migracji: certyfikat, mieszana zawartość i odpowiedzialność za naprawę",
    "expected_status": "future",
    "expected_slug": "ssl-wordpress-po-migracji-certyfikat-mieszana-zawartosc",
    "expected_date": "2026-11-21 18:00:00",
    "expected_featured_id": 6127,
    "featured_id": 6127,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-poradnik-ff462932-ca6-cover.webp",
    "facebook_image_id": 6127,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-poradnik-ff462932-ca6-cover.webp",
    "preserve_existing_facebook": true,
    "featured_alt": "Dłoń wskazująca przewód między dwoma urządzeniami w szafie serwerowej podczas kontroli infrastruktury.",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Strony internetowe”; zachować tę główną intencję.",
    "seo_meta": {
      "rank_math_focus_keyword": "mieszana zawartość, ssl wordpress, HTTPS po migracji, mieszana zawartość WordPress, certyfikat WordPress",
      "rank_math_title": "SSL w WordPressie po migracji: mieszana zawartość i certyfikat",
      "rank_math_description": "Mieszana zawartość może wywoływać ostrzeżenia HTTPS po migracji WordPressa. Sprawdź certyfikat i adresy zasobów oraz ustal odpowiedzialność za naprawę."
    },
    "expected_seo_meta": {
      "rank_math_focus_keyword": [
        "ssl wordpress, HTTPS po migracji, mieszana zawartość WordPress, certyfikat WordPress"
      ],
      "rank_math_title": [
        "SSL WordPress po migracji: kto naprawia ostrzeżenia HTTPS?"
      ],
      "rank_math_description": [
        "Ostrzeżenia HTTPS po migracji WordPressa? Sprawdź, jak odróżnić problem certyfikatu od mieszanej zawartości i komu zlecić diagnozę oraz naprawę."
      ]
    },
    "seo_meta_en": {
      "rank_math_title": "WordPress SSL after migration: mixed content and certificates",
      "rank_math_description": "Mixed content can cause HTTPS warnings after a WordPress migration. Check the certificate and resource URLs, and agree who is responsible for repairs."
    }
  },
  "6139": {
    "category_slug": "seo-i-konwersja",
    "expected_title": "Sitemap XML po zmianach strony: jak sprawdzić adresy i zgłosić aktualną mapę",
    "expected_status": "future",
    "expected_slug": "sitemap-xml-po-zmianach-strony-sprawdzanie-adresow-zgloszenie-mapy",
    "expected_date": "2026-11-22 18:00:00",
    "expected_featured_id": 6128,
    "featured_id": 6128,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-poradnik-c7e9382b-f0a-cover.webp",
    "facebook_image_id": 6128,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-poradnik-c7e9382b-f0a-cover.webp",
    "preserve_existing_facebook": true,
    "featured_alt": "Dwa wydruki wykazów adresów z zaznaczoną zmianą wiersza, lupą i ołówkiem podczas porównania.",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "consolidate_duplicate_labels_or_choose_primary",
    "rationale": "Tytuł i nagłówki opisują temat „SEO i konwersja”; zachować tę główną intencję."
  },
  "6141": {
    "category_slug": "seo-i-konwersja",
    "expected_title": "Robots.txt a noindex: jak ustalić dostęp do strony i uniknąć sprzecznych ustawień",
    "expected_status": "future",
    "expected_slug": "robots-txt-a-noindex-dostep-do-strony-bez-sprzecznych-ustawien",
    "expected_date": "2026-11-23 18:00:00",
    "expected_featured_id": 6129,
    "featured_id": 6129,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-poradnik-71ed46ce-c39-cover.webp",
    "facebook_image_id": 6129,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-poradnik-71ed46ce-c39-cover.webp",
    "preserve_existing_facebook": true,
    "featured_alt": "Zamknięta metalowa bramka w drewnianym kanale zasłania czerwony znacznik przed stalowym żetonem.",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "consolidate_duplicate_labels_or_choose_primary",
    "rationale": "Tytuł i nagłówki opisują temat „SEO i konwersja”; zachować tę główną intencję.",
    "seo_meta": {
      "rank_math_focus_keyword": "robots.txt a noindex, robots txt, blokowanie indeksowania",
      "rank_math_title": "Robots.txt a noindex: dostęp robota i sprzeczne ustawienia",
      "rank_math_description": "Robots.txt a noindex to różne mechanizmy. Sprawdź dostęp robota, wykryj sprzeczne ustawienia i przygotuj korektę indeksowania wybranych stron oraz plików."
    },
    "expected_seo_meta": {
      "rank_math_focus_keyword": [
        "robots txt, robots.txt a noindex, blokowanie indeksowania"
      ],
      "rank_math_title": [
        "Robots.txt a noindex: dostęp robota bez sprzecznych ustawień"
      ],
      "rank_math_description": [
        "Rozróżnij robots.txt i noindex, wykryj sprzeczne ustawienia oraz przygotuj zlecenie korekty dostępu robota i indeksowania wybranych stron oraz plików."
      ]
    },
    "seo_meta_en": {
      "rank_math_title": "Robots.txt and noindex: crawler access and conflicting settings",
      "rank_math_description": "Robots.txt and noindex serve different purposes. Check crawler access, identify conflicting settings and plan indexing changes for selected pages and files."
    }
  },
  "6150": {
    "category_slug": "sklepy-internetowe",
    "expected_title": "WooCommerce API przed integracją: jakie dane, uprawnienia i właściciela dostępu ustalić",
    "expected_status": "future",
    "expected_slug": "woocommerce-api-dane-uprawnienia-wlasciciel-dostepu",
    "expected_date": "2026-11-26 18:00:00",
    "expected_featured_id": 6148,
    "featured_id": 6148,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-poradnik-3408787e-a2a-cover.webp",
    "facebook_image_id": 6148,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-poradnik-3408787e-a2a-cover.webp",
    "preserve_existing_facebook": true,
    "featured_alt": "Plansza z kartami danych i żetonami używanymi do rozdzielenia zakresu odczytu oraz zapisu integracji.",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Sklepy internetowe”; zachować tę główną intencję."
  },
  "6153": {
    "category_slug": "seo-i-konwersja",
    "expected_title": "Dane strukturalne na stronie firmy: jak porównać znaczniki z widoczną ofertą",
    "expected_status": "future",
    "expected_slug": "dane-strukturalne-firmy-porownanie-znacznikow-z-oferta",
    "expected_date": "2026-11-25 18:00:00",
    "expected_featured_id": 6130,
    "featured_id": 6130,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-poradnik-640fa5a0-432-cover.webp",
    "facebook_image_id": 6130,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-poradnik-640fa5a0-432-cover.webp",
    "preserve_existing_facebook": true,
    "featured_alt": "Papierowe karty i przezroczyste nakładki z dopasowanymi znacznikami podczas porównania informacji.",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "consolidate_duplicate_labels_or_choose_primary",
    "rationale": "Tytuł i nagłówki opisują temat „SEO i konwersja”; zachować tę główną intencję."
  },
  "6157": {
    "category_slug": "strony-internetowe",
    "expected_title": "Favicon ICO i ikona witryny: jakie pliki przygotować i jak sprawdzić wdrożenie",
    "expected_status": "future",
    "expected_slug": "favicon-ico-ikona-witryny-pliki-wdrozenie",
    "expected_date": "2026-11-28 18:00:00",
    "expected_featured_id": 6155,
    "featured_id": 6155,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-poradnik-af4d824a-da7-cover.webp",
    "facebook_image_id": 6155,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-poradnik-af4d824a-da7-cover.webp",
    "preserve_existing_facebook": true,
    "featured_alt": "Pasek wydrukowanych próbek geometrycznej ikony w różnych rozmiarach i wariantach szczegółowości, oglądany przez lupę.",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Strony internetowe”; zachować tę główną intencję."
  },
  "6164": {
    "category_slug": "strony-internetowe",
    "expected_title": "Open Graph na stronie firmy: jak zaplanować miniaturę linku i sprawdzić metadane",
    "expected_status": "future",
    "expected_slug": "open-graph-na-stronie-firmy-miniatura-linku-i-metadane",
    "expected_date": "2026-11-29 18:00:00",
    "expected_featured_id": 6159,
    "featured_id": 6159,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-poradnik-a554be0a-bff-cover.webp",
    "facebook_image_id": 6159,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-poradnik-a554be0a-bff-cover.webp",
    "preserve_existing_facebook": true,
    "featured_alt": "Dwie odbitki zdjęcia mosiężnego elementu z rowkami i przesuwane ramki do porównania kadru miniatury.",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Strony internetowe”; zachować tę główną intencję."
  },
  "6168": {
    "category_slug": "sklepy-internetowe",
    "expected_title": "Cross-sell i up-sell w WooCommerce: gdzie pokazać uzupełnienie, a gdzie alternatywę",
    "expected_status": "future",
    "expected_slug": "cross-sell-up-sell-woocommerce-uzupelnienie-alternatywa",
    "expected_date": "2026-11-30 18:00:00",
    "expected_featured_id": 6166,
    "featured_id": 6166,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-poradnik-43c58684-253-cover.webp",
    "facebook_image_id": 6166,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-poradnik-43c58684-253-cover.webp",
    "preserve_existing_facebook": true,
    "featured_alt": "Dwa ręczne młynki do kawy: prosty model z tworzywa i stalowa alternatywa z widocznym mechanizmem regulacji.",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Sklepy internetowe”; zachować tę główną intencję.",
    "seo_meta": {
      "rank_math_focus_keyword": "cross-sell i up-sell, cross selling co to, cross sell WooCommerce, up sell WooCommerce, produkty powiązane WooCommerce",
      "rank_math_title": "Cross-sell i up-sell w WooCommerce: 2 rodzaje rekomendacji",
      "rank_math_description": "Cross-sell i up-sell w WooCommerce wymagają doboru uzupełnień i alternatyw. Sprawdź miejsca rekomendacji oraz scenariusze testów przed odbiorem konfiguracji."
    },
    "expected_seo_meta": {
      "rank_math_focus_keyword": [
        "cross selling co to, cross sell WooCommerce, up sell WooCommerce, produkty powiązane WooCommerce"
      ],
      "rank_math_title": [
        "Cross-sell i up-sell w WooCommerce: miejsca i dobór rekomendacji"
      ],
      "rank_math_description": [
        "Sprawdź, gdzie pokazać cross-sell i up-sell w WooCommerce, jak dobrać uzupełnienia oraz alternatywy i co przetestować przed odbiorem konfiguracji."
      ]
    },
    "seo_meta_en": {
      "rank_math_title": "Cross-sell and up-sell in WooCommerce: 2 types of recommendations",
      "rank_math_description": "Cross-sell and up-sell in WooCommerce require suitable complements and alternatives. Check recommendation placement and test scenarios before accepting the setup."
    }
  },
  "6173": {
    "category_slug": "sklepy-internetowe",
    "expected_title": "Potwierdzenie zamówienia w WooCommerce: co pokazać po zakupie i w wiadomości klienta",
    "expected_status": "future",
    "expected_slug": "potwierdzenie-zamowienia-woocommerce-strona-po-zakupie-wiadomosc-klienta",
    "expected_date": "2026-12-02 18:00:00",
    "expected_featured_id": 6171,
    "featured_id": 6171,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-poradnik-dbaf7676-bcf-cover.webp",
    "facebook_image_id": 6171,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-poradnik-dbaf7676-bcf-cover.webp",
    "preserve_existing_facebook": true,
    "featured_alt": "Trzy osobne karty z paskiem podsumowania, znacznikiem płatności i małą paczką obok kubka oraz pudełka.",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Sklepy internetowe”; zachować tę główną intencję."
  },
  "6191": {
    "category_slug": "sklepy-internetowe",
    "expected_title": "PIM a sklep internetowy: kto utrzymuje dane produktów i co ustalić przed integracją",
    "expected_status": "future",
    "expected_slug": "pim-a-sklep-internetowy-odpowiedzialnosc-za-dane-przed-integracja",
    "expected_date": "2026-12-05 18:00:00",
    "expected_featured_id": 6179,
    "featured_id": 6179,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-poradnik-2b24ed0a-e67-cover.webp",
    "facebook_image_id": 6179,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-poradnik-2b24ed0a-e67-cover.webp",
    "preserve_existing_facebook": true,
    "featured_alt": "Wieszak z jednokolorowymi koszulkami w różnych rozmiarach i kolorach w pomieszczeniu z próbkami produktów.",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Sklepy internetowe”; zachować tę główną intencję."
  },
  "6196": {
    "category_slug": "sklepy-internetowe",
    "expected_title": "Testy automatyczne sklepu WooCommerce: co sprawdzać po zmianie checkoutu lub płatności",
    "expected_status": "future",
    "expected_slug": "testy-automatyczne-woocommerce-checkout-platnosci",
    "expected_date": "2026-12-03 18:00:00",
    "expected_featured_id": 6180,
    "featured_id": 6180,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-poradnik-fca7cf43-1e3-cover.webp",
    "facebook_image_id": 6180,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-poradnik-fca7cf43-1e3-cover.webp",
    "preserve_existing_facebook": true,
    "featured_alt": "Szeroki kadr koszyka z notesem, butelką i saszetką na stole w pomieszczeniu do przygotowania towaru.",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Sklepy internetowe”; zachować tę główną intencję."
  },
  "5736": {
    "category_slug": "strony-internetowe",
    "expected_title": "Integracja CRM z formularzem strony: wdrożenie i obsługa zapytań",
    "expected_status": "future",
    "expected_slug": "integracja-crm-z-formularzem-strony",
    "expected_date": "2026-12-08 08:00:00",
    "expected_featured_id": 5864,
    "featured_id": 5864,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-9060fb1b-33c-cover-webp37.webp",
    "facebook_image_id": 5864,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-9060fb1b-33c-cover-webp37.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "Przekazywanie zgłoszenia między osobami obsługującymi klienta",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "primary_refinement",
    "rationale": "Integracja formularza WWW z CRM i obsługa zgłoszeń; techniczna funkcja strony, z kontekstem procesu leadów."
  },
  "5738": {
    "category_slug": "sklepy-internetowe",
    "expected_title": "Import produktów WooCommerce: jak przygotować CSV i uniknąć duplikatów",
    "expected_status": "future",
    "expected_slug": "import-produktow-woocommerce-csv",
    "expected_date": "2026-12-11 08:00:00",
    "expected_featured_id": 5862,
    "featured_id": 5862,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-91e63c9a-984-cover-webp37.webp",
    "facebook_image_id": 5862,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-91e63c9a-984-cover-webp37.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "Porządkowanie rozmiarów i kolorów produktów na regale katalogowym",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Sklepy internetowe”; zachować tę główną intencję."
  },
  "5740": {
    "category_slug": "logo-branding",
    "expected_title": "Folder reklamowy B2B: jak zaplanować 8–12 stron oferty",
    "expected_status": "future",
    "expected_slug": "folder-reklamowy-b2b",
    "expected_date": "2026-12-15 08:00:00",
    "expected_featured_id": 5860,
    "featured_id": 5860,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-61270435-5ad-cover-webp37.webp",
    "facebook_image_id": 5860,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-61270435-5ad-cover-webp37.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "Składanie wielostronicowego folderu przy urządzeniu introligatorskim",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Logo i branding”; zachować tę główną intencję."
  },
  "5742": {
    "category_slug": "strony-internetowe",
    "expected_title": "Kopia zapasowa WordPressa: backup, przechowywanie i odtwarzanie",
    "expected_status": "future",
    "expected_slug": "kopia-zapasowa-wordpress",
    "expected_date": "2026-12-18 08:00:00",
    "expected_featured_id": 5858,
    "featured_id": 5858,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-7b00dedb-f6a-cover-webp37.webp",
    "facebook_image_id": 5858,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-photo-7b00dedb-f6a-cover-webp37.webp",
    "preserve_existing_facebook": false,
    "featured_alt": "Podłączanie zewnętrznego nośnika do wykonania kopii zapasowej",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Strony internetowe”; zachować tę główną intencję.",
    "seo_meta": {
      "rank_math_focus_keyword": "kopia zapasowa WordPressa, wordpress kopia zapasowa, kopia zapasowa strony wordpress, backup wordpress, przywracanie strony wordpress, automatyczne kopie zapasowe wordpress",
      "rank_math_title": "Kopia zapasowa WordPressa: pliki, baza i test odtwarzania",
      "rank_math_description": "Kopia zapasowa WordPressa powinna obejmować pliki i bazę danych. Zaplanuj częstotliwość, przechowywanie oraz test przywrócenia działającej strony."
    },
    "expected_seo_meta": {
      "rank_math_focus_keyword": [
        "wordpress kopia zapasowa, kopia zapasowa strony wordpress, backup wordpress, przywracanie strony wordpress, automatyczne kopie zapasowe wordpress"
      ],
      "rank_math_title": [
        "Kopia zapasowa WordPressa: jak zaplanować backup"
      ],
      "rank_math_description": [
        "Jak zrobić użyteczną kopię WordPressa? Sprawdź pliki i bazę danych, częstotliwość backupu, przechowywanie oraz test przywrócenia strony."
      ]
    },
    "seo_meta_en": {
      "rank_math_title": "A WordPress backup: files, database and a restore test",
      "rank_math_description": "A WordPress backup should include files and the database. Plan backup frequency, storage and a test that restores a working website."
    }
  },
  "6198": {
    "category_slug": "seo-i-konwersja",
    "expected_title": "KPI e-commerce w GA4: jak ustalić plan pomiaru od produktu do zakupu przed wdrożeniem",
    "expected_status": "future",
    "expected_slug": "kpi-e-commerce-ga4-plan-pomiaru-przed-wdrozeniem",
    "expected_date": "2026-12-06 18:00:00",
    "expected_featured_id": 6182,
    "featured_id": 6182,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-poradnik-8f74e43e-384-cover.webp",
    "facebook_image_id": 6182,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-poradnik-8f74e43e-384-cover.webp",
    "preserve_existing_facebook": true,
    "featured_alt": "Regał z ceramicznymi kubkami w dwóch kolorach, uporządkowanymi według wariantów produktu.",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "consolidate_duplicate_labels_or_choose_primary",
    "rationale": "Tytuł i nagłówki opisują temat „SEO i konwersja”; zachować tę główną intencję.",
    "seo_meta": {
      "rank_math_focus_keyword": "KPI e-commerce w GA4, e commerce kpi, plan pomiaru e-commerce, zdarzenia e-commerce GA4",
      "rank_math_description": "Ustal KPI e-commerce w GA4 przed wdrożeniem sklepu. Rozpisz zdarzenia od produktu do zakupu, parametry, jednostki pomiaru oraz scenariusze odbioru."
    },
    "expected_seo_meta": {
      "rank_math_focus_keyword": [
        "e commerce kpi, KPI e-commerce w GA4, plan pomiaru e-commerce, zdarzenia e-commerce GA4"
      ],
      "rank_math_description": [
        "Ustal KPI e-commerce w GA4 przed wdrożeniem sklepu. Rozpisz zdarzenia od produktu do zakupu, jednostki pomiaru, parametry i scenariusze odbioru."
      ]
    },
    "seo_meta_en": {
      "rank_math_description": "Define your e-commerce KPIs in GA4 before implementing the store. Map events from product view to purchase, parameters, units and acceptance scenarios."
    }
  },
  "6200": {
    "category_slug": "sklepy-internetowe",
    "expected_title": "Omnichannel w sklepie: jak ustalić obsługę zamówień, odbioru i zwrotów między kanałami",
    "expected_status": "future",
    "expected_slug": "omnichannel-obsluga-zamowien-odbioru-i-zwrotow-miedzy-kanalami",
    "expected_date": "2026-12-07 18:00:00",
    "expected_featured_id": 6183,
    "featured_id": 6183,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-poradnik-223f4473-375-cover.webp",
    "facebook_image_id": 6183,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-poradnik-223f4473-375-cover.webp",
    "preserve_existing_facebook": true,
    "featured_alt": "Pracownicy przekazują zamkniętą paczkę przy stole w strefie przygotowania zamówień.",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Sklepy internetowe”; zachować tę główną intencję."
  },
  "6202": {
    "category_slug": "strony-internetowe",
    "expected_title": "Powiadomienia push na własnej stronie: zgoda, przypadki użycia i plan testu",
    "expected_status": "future",
    "expected_slug": "powiadomienia-push-na-stronie-zgoda-przypadki-uzycia-plan-testu",
    "expected_date": "2026-12-09 18:00:00",
    "expected_featured_id": 6184,
    "featured_id": 6184,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-poradnik-64341549-545-cover.webp",
    "facebook_image_id": 6184,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-poradnik-64341549-545-cover.webp",
    "preserve_existing_facebook": true,
    "featured_alt": "Kącik do czytania z telefonem odłożonym ekranem do dołu na stoliku obok książki i kubka.",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Strony internetowe”; zachować tę główną intencję."
  },
  "6213": {
    "category_slug": "sklepy-internetowe",
    "expected_title": "Headless commerce a WooCommerce: kiedy oddzielny frontend uzasadnia dodatkowe utrzymanie",
    "expected_status": "future",
    "expected_slug": "headless-commerce-woocommerce-oddzielny-frontend-utrzymanie",
    "expected_date": "2026-12-10 18:00:00",
    "expected_featured_id": 6210,
    "featured_id": 6210,
    "featured_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-poradnik-09b81a4a-45f-cover.webp",
    "facebook_image_id": 6210,
    "facebook_image_url": "https://zaprojektowani.com/wp-content/uploads/2026/10/zp-poradnik-09b81a4a-45f-cover.webp",
    "preserve_existing_facebook": true,
    "featured_alt": "Druciany koszyk z ceramicznymi kubkami i dodatkowy kubek na stole w pomieszczeniu do pakowania.",
    "featured_alt_source": "Existing Media Library ALT",
    "classification_action": "keep",
    "rationale": "Tytuł i nagłówki opisują temat „Sklepy internetowe”; zachować tę główną intencję."
  }
}
JSON
, true);
    }
    return $manifest;
}
