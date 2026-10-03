<?php
if (!defined('ABSPATH')) {
  exit;
}

/**
 * ZAPROJEKTOWANI SUITE — SEO foundation v0.2.7
 * Home meta helpers + structured data for the new SEO strategy.
 */

function zp_suite_seo_is_front_page() {
  return !is_admin() && (is_front_page() || is_home());
}

function zp_suite_home_title() {
  return 'Strony internetowe Katowice, sklepy WooCommerce i branding';
}

function zp_suite_home_description() {
  return zp_suite_opt('seo.home_description', 'Zaprojektowani.com: strony internetowe Katowice, sklepy WooCommerce, logo, branding, SEO i kampanie Meta Ads dla firm ze Śląska i całej Polski.');
}

/** Rank Math support — can still be overridden manually in Rank Math if needed. */
add_filter('rank_math/frontend/title', function ($title) {
  return zp_suite_seo_is_front_page() ? zp_suite_home_title() : $title;
}, 20);

add_filter('rank_math/frontend/description', function ($description) {
  return zp_suite_seo_is_front_page() ? zp_suite_home_description() : $description;
}, 20);

add_filter('rank_math/frontend/canonical', function ($canonical) {
  return zp_suite_seo_is_front_page() ? home_url('/') : $canonical;
}, 20);


/** v2.2.113 — Rank Math fallback meta dla głównych podstron usługowych. */
function zp_suite_seo_current_slug(){
  if (is_admin()) return '';
  $path = trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/');
  return sanitize_title($path);
}
function zp_suite_seo_page_meta_map(){
  return [
    'strony-internetowe-katowice' => [
      'title' => 'Strony Internetowe Katowice | Projektowanie i tworzenie stron WWW',
      'description' => 'Projektujemy i tworzymy strony internetowe w Katowicach: WordPress, UX/UI, treści SEO, landing page, strony firmowe, formularze, analityka i przygotowanie pod pozycjonowanie.',
      'canonical' => home_url('/strony-internetowe-katowice/'),
    ],
    'sklepy-internetowe-katowice' => [
      'title' => 'Sklepy Internetowe Katowice | WooCommerce i sklepy online — Zaprojektowani',
      'description' => 'Projektujemy sklepy internetowe WooCommerce dla firm z Katowic, Śląska i całej Polski. UX, płatności online, SEO e-commerce, koszyk, dostawy i wdrożenie gotowe do sprzedaży.',
      'canonical' => home_url('/sklepy-internetowe-katowice/'),
    ],
    'logo-branding-katowice' => [
      'title' => 'Projektowanie Logo Katowice | Logo i Branding — Zaprojektowani',
      'description' => 'Projektowanie logo Katowice: tworzymy logo, branding, identyfikację wizualną, brandbook i system marki dla firm ze Śląska i całej Polski.',
      'canonical' => home_url('/logo-branding-katowice/'),
    ],
    'wiedza' => [
      'title' => 'Wiedza: strony WWW, sklepy, SEO i branding | Zaprojektowani',
      'description' => 'Praktyczne poradniki o stronach internetowych, sklepach WooCommerce, SEO, logo, brandingu i Meta Ads. Sprawdź koszty, procesy, checklisty i przykłady.',
      'canonical' => home_url('/wiedza/'),
    ],
  ];
}
add_filter('rank_math/frontend/title', function($title){
  $map = zp_suite_seo_page_meta_map();
  $slug = zp_suite_seo_current_slug();
  return isset($map[$slug]['title']) ? $map[$slug]['title'] : $title;
}, 25);
add_filter('rank_math/frontend/description', function($description){
  $map = zp_suite_seo_page_meta_map();
  $slug = zp_suite_seo_current_slug();
  return isset($map[$slug]['description']) ? $map[$slug]['description'] : $description;
}, 25);
add_filter('rank_math/frontend/canonical', function($canonical){
  $map = zp_suite_seo_page_meta_map();
  $slug = zp_suite_seo_current_slug();
  return isset($map[$slug]['canonical']) ? $map[$slug]['canonical'] : $canonical;
}, 25);

/** Fallback meta tags when Rank Math is not active or does not print them. */
add_action('wp_head', function () {
  if (!zp_suite_seo_is_front_page()) {
    return;
  }

  if (!defined('RANK_MATH_VERSION')) {
    echo '<meta name="description" content="' . esc_attr(zp_suite_home_description()) . '">' . "\n";
  }
}, 4);

/** Structured data for homepage: Organization + WebSite + Service catalog + FAQPage. */
add_action('wp_head', function () {
  if (!zp_suite_seo_is_front_page()) {
    return;
  }

  $site = home_url('/');
  $logo = 'https://zaprojektowani.com/wp-content/uploads/2026/05/zaproktowani_znak_wodny_czarny.png';

  $schema = [
    '@context' => 'https://schema.org',
    '@graph' => [
      [
        '@type' => 'Organization',
        '@id' => $site . '#organization',
        'name' => 'Zaprojektowani.com',
        'url' => $site,
        'logo' => [
          '@type' => 'ImageObject',
          'url' => $logo,
        ],
        'email' => zp_suite_opt('brand.admin_email','kontakt@zaprojektowani.com'),
        'telephone' => zp_suite_opt('brand.phone','+48501054253'),
        'sameAs' => [
          'https://www.facebook.com/zaprojektowanicom',
          'https://www.instagram.com/zaprojektowani.com/',
        ],
        'knowsAbout' => [
          'strony internetowe Katowice',
          'projektowanie stron internetowych',
          'tworzenie sklepów internetowych WooCommerce',
          'logo i branding',
          'identyfikacja wizualna',
          'kampanie Meta Ads',
          'SEO dla stron firmowych',
          'UX i konwersja',
        ],
      ],
      [
        '@type' => 'LocalBusiness',
        '@id' => $site . '#localbusiness',
        'name' => 'Zaprojektowani.com',
        'url' => $site,
        'image' => $logo,
        'telephone' => zp_suite_opt('brand.phone','+48501054253'),
        'email' => zp_suite_opt('brand.admin_email','kontakt@zaprojektowani.com'),
        'priceRange' => '$$',
        'address' => [
          '@type' => 'PostalAddress',
          'addressLocality' => 'Katowice',
          'addressRegion' => 'śląskie',
          'addressCountry' => 'PL',
        ],
        'areaServed' => [
          ['@type' => 'City', 'name' => 'Katowice'],
          ['@type' => 'AdministrativeArea', 'name' => 'Śląsk'],
          ['@type' => 'Country', 'name' => 'Polska'],
        ],
        'parentOrganization' => ['@id' => $site . '#organization'],
        'hasOfferCatalog' => [
          '@type' => 'OfferCatalog',
          'name' => 'Usługi Zaprojektowani.com',
          'itemListElement' => [
            ['@type'=>'Offer','itemOffered'=>['@type'=>'Service','name'=>'Strony internetowe Katowice','url'=>home_url('/strony-internetowe-katowice/')]],
            ['@type'=>'Offer','itemOffered'=>['@type'=>'Service','name'=>'Sklepy internetowe Katowice','url'=>home_url('/sklepy-internetowe-katowice/')]],
            ['@type'=>'Offer','itemOffered'=>['@type'=>'Service','name'=>'Logo i branding Katowice','url'=>home_url('/logo-branding-katowice/')]],
            ['@type'=>'Offer','itemOffered'=>['@type'=>'Service','name'=>'Kampanie Meta Ads','url'=>home_url('/kampanie-reklamowe/')]],
          ],
        ],
      ],
      [
        '@type' => 'WebSite',
        '@id' => $site . '#website',
        'url' => $site,
        'name' => 'Zaprojektowani.com',
        'publisher' => ['@id' => $site . '#organization'],
        'potentialAction' => [
          '@type' => 'SearchAction',
          'target' => $site . '?s={search_term_string}',
          'query-input' => 'required name=search_term_string',
        ],
      ],
      [
        '@type' => 'WebPage',
        '@id' => $site . '#webpage',
        'url' => $site,
        'name' => zp_suite_home_title(),
        'description' => zp_suite_home_description(),
        'isPartOf' => ['@id' => $site . '#website'],
        'about' => ['@id' => $site . '#organization'],
        'primaryImageOfPage' => [
          '@type' => 'ImageObject',
          'url' => 'https://zaprojektowani.com/wp-content/uploads/2026/05/team_zaprojektowani.webp',
        ],
        'speakable' => [
          '@type' => 'SpeakableSpecification',
          'cssSelector' => ['h1', '#zpHomeFaqTitle', '.zpHomeFaq__faqList summary'],
        ],
        'mentions' => [
          ['@type'=>'Thing','name'=>'strony internetowe Katowice'],
          ['@type'=>'Thing','name'=>'sklepy internetowe WooCommerce'],
          ['@type'=>'Thing','name'=>'logo i branding Katowice'],
          ['@type'=>'Thing','name'=>'kampanie Meta Ads'],
        ],
      ],
      [
        '@type' => 'ItemList',
        '@id' => $site . '#services',
        'name' => 'Usługi Zaprojektowani.com',
        'itemListElement' => [
          [
            '@type' => 'ListItem',
            'position' => 1,
            'item' => [
              '@type' => 'Service',
              'name' => 'Strony internetowe Katowice',
              'url' => home_url('/strony-internetowe-katowice/'),
              'provider' => ['@id' => $site . '#organization'],
              'areaServed' => 'Katowice, Śląsk, Polska',
            ],
          ],
          [
            '@type' => 'ListItem',
            'position' => 2,
            'item' => [
              '@type' => 'Service',
              'name' => 'Sklepy internetowe Katowice',
              'url' => home_url('/sklepy-internetowe-katowice/'),
              'provider' => ['@id' => $site . '#organization'],
              'areaServed' => 'Katowice, Śląsk, Polska',
            ],
          ],
          [
            '@type' => 'ListItem',
            'position' => 3,
            'item' => [
              '@type' => 'Service',
              'name' => 'Logo i branding Katowice',
              'url' => home_url('/logo-branding-katowice/'),
              'provider' => ['@id' => $site . '#organization'],
              'areaServed' => 'Katowice, Śląsk, Polska',
            ],
          ],
          [
            '@type' => 'ListItem',
            'position' => 4,
            'item' => [
              '@type' => 'Service',
              'name' => 'Kampanie Meta Ads',
              'url' => home_url('/kampanie-reklamowe/'),
              'provider' => ['@id' => $site . '#organization'],
              'areaServed' => 'Polska',
            ],
          ],
        ],
      ],
      [
        '@type' => 'BreadcrumbList',
        '@id' => $site . '#breadcrumb',
        'itemListElement' => [
          [
            '@type' => 'ListItem',
            'position' => 1,
            'name' => 'Strona główna',
            'item' => $site,
          ],
        ],
      ],
      [
        '@type' => 'FAQPage',
        '@id' => $site . '#faq',
        'mainEntity' => array_map(function($faq){ return ['@type'=>'Question','name'=>wp_strip_all_tags($faq['q'] ?? ''),'acceptedAnswer'=>['@type'=>'Answer','text'=>wp_strip_all_tags($faq['a'] ?? '')]]; }, array_values(array_filter(zp_suite_cms_get('faq', []), function($r){ return !isset($r['visible']) || (string)$r['visible'] !== '0'; }))),
      ],
    ],
  ];

  echo '<script type="application/ld+json" id="zp-suite-home-schema">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
}, 25);

/**
 * ZP Suite v2.2.111 — schema dla kluczowych podstron SEO.
 */
add_action('wp_head', function(){
  if (is_admin()) return;
  $uri = trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/');
  $site = home_url('/');
  $base = [
    'sklepy-internetowe-katowice' => [
      'type' => 'Service',
      'name' => 'Sklepy internetowe Katowice — WooCommerce i e-commerce',
      'description' => 'Zaprojektowani.com projektuje sklepy internetowe WooCommerce dla firm z Katowic, Śląska i całej Polski: strategia sprzedaży, architektura kategorii, produkty i warianty, UX/UI, koszyk, checkout, płatności, dostawy, SEO e-commerce, analityka, remarketing oraz przygotowanie pod Google Ads i Meta Ads.',
      'serviceType' => 'Tworzenie sklepów internetowych, sklepy WooCommerce, UX e-commerce, SEO sklepu, płatności online, dostawy i konfiguracja sprzedaży online',
      'keywords' => ['sklepy internetowe Katowice','tworzenie sklepów internetowych','sklep WooCommerce','projektowanie sklepów internetowych','sklepy WooCommerce Katowice'],
      'breadcrumb' => 'Sklepy internetowe Katowice',
    ],
    'logo-branding-katowice' => [
      'type' => 'Service',
      'name' => 'Projektowanie logo Katowice — logo, branding i identyfikacja wizualna',
      'description' => 'Zaprojektowani.com projektuje logo, branding, identyfikację wizualną, brandbooki i systemy marki dla firm z Katowic, Śląska i całej Polski. Projekty obejmują znak, warianty logo, kolorystykę, typografię, pliki do druku i internetu oraz spójność ze stroną internetową, ofertą i social mediami.',
      'serviceType' => 'Projektowanie logo, branding, identyfikacja wizualna, brandbook, pliki logo i system marki',
      'keywords' => ['projektowanie logo Katowice','logo dla firmy Katowice','agencja brandingowa Katowice','identyfikacja wizualna Katowice','branding Katowice'],
      'breadcrumb' => 'Logo i branding Katowice',
    ],
    'realizacje' => [
      'type' => 'CollectionPage',
      'name' => 'Realizacje stron internetowych, sklepów i brandingu — Zaprojektowani.com',
      'description' => 'Portfolio projektów Zaprojektowani.com: strony internetowe WordPress, sklepy WooCommerce, logo, branding, identyfikacja wizualna i projekty nastawione na SEO, UX oraz konwersję.',
      'keywords' => ['realizacje stron internetowych','portfolio stron internetowych','realizacje sklepów internetowych','portfolio logo i branding','Zaprojektowani realizacje'],
      'breadcrumb' => 'Realizacje',
    ],
    'faq' => [
      'type' => 'FAQPage',
      'name' => 'FAQ — strony internetowe, sklepy, branding i kampanie Meta Ads',
      'description' => 'Najczęstsze pytania o projektowanie stron internetowych, sklepów WooCommerce, logo, brandingu, SEO i kampanii Meta Ads w Zaprojektowani.com.',
      'keywords' => ['FAQ strony internetowe','FAQ sklepy internetowe','FAQ branding','pytania o projektowanie stron','pytania o WooCommerce'],
      'breadcrumb' => 'FAQ',
    ],
    'kontakt' => [
      'type' => 'ContactPage',
      'name' => 'Kontakt Zaprojektowani.com — wycena strony, sklepu, logo lub kampanii',
      'description' => 'Skontaktuj się z Zaprojektowani.com, aby wycenić stronę internetową, sklep WooCommerce, logo, branding, SEO albo kampanię Meta Ads.',
      'keywords' => ['kontakt Zaprojektowani','wycena strony internetowej','wycena sklepu internetowego','wycena logo','kontakt agencja Katowice'],
      'breadcrumb' => 'Kontakt',
    ],
    'wiedza' => [
      'type' => 'CollectionPage',
      'name' => 'Wiedza — poradniki o stronach internetowych, sklepach, SEO i brandingu',
      'description' => 'Baza wiedzy Zaprojektowani.com: praktyczne poradniki o stronach internetowych, sklepach WooCommerce, SEO, logo, brandingu, UX, konwersji i kampaniach reklamowych.',
      'keywords' => ['wiedza o stronach internetowych','poradniki SEO','blog o WooCommerce','blog branding','poradniki Meta Ads'],
      'breadcrumb' => 'Wiedza',
    ],
    'studio-wyceny' => [
      'type' => 'WebPage',
      'name' => 'Studio wyceny — strony internetowe, sklepy, logo i kampanie',
      'description' => 'Studio wyceny Zaprojektowani.com pomaga określić zakres projektu strony internetowej, sklepu WooCommerce, logo, brandingu, SEO lub kampanii Meta Ads.',
      'keywords' => ['studio wyceny','wycena strony internetowej','wycena sklepu WooCommerce','wycena logo','wycena kampanii Meta Ads'],
      'breadcrumb' => 'Studio wyceny',
    ],
  ];
  if (!isset($base[$uri])) return;
  $d = $base[$uri];
  $url = home_url('/'.$uri.'/');
  $graph = [
    [
      '@type' => $d['type'] === 'Service' ? 'WebPage' : $d['type'],
      '@id' => $url.'#webpage',
      'url' => $url,
      'name' => $d['name'],
      'description' => $d['description'],
      'inLanguage' => 'pl-PL',
      'isPartOf' => ['@id' => $site.'#website'],
      'publisher' => ['@id' => $site.'#organization'],
      'keywords' => implode(', ', $d['keywords']),
      'about' => array_map(function($k){ return ['@type'=>'Thing','name'=>$k]; }, $d['keywords']),
    ],
    [
      '@type' => 'BreadcrumbList',
      '@id' => $url.'#breadcrumb',
      'itemListElement' => [
        ['@type'=>'ListItem','position'=>1,'name'=>'Strona główna','item'=>$site],
        ['@type'=>'ListItem','position'=>2,'name'=>$d['breadcrumb'],'item'=>$url],
      ],
    ],
  ];
  if ($uri === 'wiedza') {
    $knowledge_posts = get_posts([
      'post_type' => 'post',
      'post_status' => 'publish',
      'posts_per_page' => 60,
      'orderby' => 'date',
      'order' => 'DESC',
      'ignore_sticky_posts' => true,
      'no_found_rows' => true,
      'suppress_filters' => false,
    ]);
    $list_items = [];
    foreach ($knowledge_posts as $idx => $knowledge_post) {
      $post_url = get_permalink($knowledge_post);
      if (!$post_url) { continue; }
      $list_items[] = [
        '@type' => 'ListItem',
        'position' => $idx + 1,
        'url' => $post_url,
        'name' => get_the_title($knowledge_post),
        'item' => [
          '@type' => 'Article',
          '@id' => $post_url . '#article',
          'url' => $post_url,
          'headline' => get_the_title($knowledge_post),
          'datePublished' => get_the_date('c', $knowledge_post),
          'dateModified' => get_the_modified_date('c', $knowledge_post),
          'inLanguage' => 'pl-PL',
        ],
      ];
    }
    $graph[0]['mainEntity'] = ['@id' => $url . '#articles'];
    $graph[] = [
      '@type' => 'ItemList',
      '@id' => $url . '#articles',
      'name' => 'Artykuły i poradniki Zaprojektowani.com',
      'itemListOrder' => 'https://schema.org/ItemListOrderDescending',
      'numberOfItems' => count($list_items),
      'itemListElement' => $list_items,
    ];
  }
  if ($d['type'] === 'Service') {
    $graph[0]['mainEntity'] = ['@id' => $url.'#service'];
    $graph[] = [
      '@type' => 'Service',
      '@id' => $url.'#service',
      'name' => $d['name'],
      'description' => $d['description'],
      'serviceType' => $d['serviceType'],
      'provider' => ['@id' => $site.'#organization'],
      'areaServed' => [
        ['@type'=>'City','name'=>'Katowice'],
        ['@type'=>'AdministrativeArea','name'=>'Śląsk'],
        ['@type'=>'Country','name'=>'Polska'],
      ],
      'url' => $url,
    ];
  }
  $schema = ['@context'=>'https://schema.org','@graph'=>$graph];
  echo '<script type="application/ld+json" id="zp-suite-page-schema-'.esc_attr($uri).'">'.wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE).'</script>' . "\n";
}, 28);
