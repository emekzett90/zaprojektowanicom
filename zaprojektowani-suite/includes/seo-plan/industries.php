<?php
if (!defined('ABSPATH')) { exit; }

/**
 * ZP Suite 2.9.7 — industry photos (Mat 8.10, three ZIPs; originals in /mnt/project-files/zdjecia-branze/).
 *
 * Covers (assets/img/branze/<key>-cover-*.webp) appear on every home tile of "SEO • usługi • branże"
 * (templates/seo-industries.php), as small circles in the desktop mega menu's "Strony dla branż"
 * (templates/header.php; the phone menu keeps its icons), in the first "Co projektujemy" card of each
 * industry page and on the linked cards of the "Strony dla branż" section (the template's #branze) of
 * the website pages. On an industry page that section lists the other seven industries.
 *
 * Industry pages also get:
 * - the transparent industry photo instead of the laptop in the hero, faded from the bottom and a little
 *   darker on the side the heading reaches (it is preloaded instead of the laptop);
 * - the package price list (#pakiety, "od 3 999 zł", domain and hosting "w cenie") right after the hero;
 * - three transparent photos of people from the industry where the template shows team photos: beside the
 *   process heading, above the FAQ heading and over the band under the packages, faded from the bottom.
 *
 * The section order comes from data/pages.php through zp_seo_industry_pages_layout(), so the copy the
 * content thread writes there stays its own. Every new image has width and height and only the hero photo
 * loads eagerly. No "[class...] *" or bare ":focus" rules (see speed-293.php).
 */

/** Industry pages: path, card copy (the home tiles' texts), cover focus, hero and people photo sizes. */
function zp_seo_industries(): array {
  return [
    'kancelarie' => [
      'path' => '/strony-internetowe-dla-kancelarii/', 'title' => 'Strony internetowe dla kancelarii',
      'text' => 'Układ pod zaufanie, eksperckość i szybki kontakt z klientem.', 'focus' => '42% 50%',
      'hero' => [1400, 1120], 'hero_alt' => 'Prawnik i prawniczka przed budynkiem kancelarii',
      'people' => [[900, 891], [900, 895], [900, 910]], 'slots' => ['process' => 1, 'band' => 2, 'faq' => 3],
      'pakiety' => ['Pakiety stron dla kancelarii. <strong>Od 3 999 zł.</strong>', 'specjalizacji i sposobu pracy kancelarii.'],
    ],
    'lekarze' => [
      'path' => '/strony-internetowe-dla-lekarzy/', 'title' => 'Strony dla lekarzy i gabinetów',
      'text' => 'Rzeczowa informacja o usługach, umawianie wizyt i lokalne SEO.', 'focus' => '50% 50%',
      'hero' => [1400, 1031], 'hero_alt' => 'Lekarka i lekarz przed wejściem do kliniki',
      'people' => [[900, 898], [900, 916], [900, 971]], 'slots' => ['band' => 1, 'process' => 2, 'faq' => 3],
      'pakiety' => ['Pakiety stron dla lekarzy i gabinetów. <strong>Od 3 999 zł.</strong>', 'usług gabinetu i sposobu zapisów pacjentów.'],
    ],
    'deweloperzy' => [
      'path' => '/strony-internetowe-dla-deweloperow/', 'title' => 'Strony dla deweloperów',
      'text' => 'Prezentacja inwestycji, lokalizacji, standardu i formularzy zapytań.', 'focus' => '30% 50%',
      'hero' => [1400, 867], 'hero_alt' => 'Deweloper i architektka przed nowym budynkiem mieszkalnym',
      'people' => [[900, 883], [900, 858], [900, 910]], 'slots' => ['process' => 1, 'faq' => 2, 'band' => 3],
      'pakiety' => ['Pakiety stron dla deweloperów. <strong>Od 3 999 zł.</strong>', 'liczby inwestycji i lokali.'],
    ],
    'beauty' => [
      'path' => '/strony-internetowe-dla-salonow-beauty/', 'title' => 'Strony dla salonów beauty',
      'text' => 'Opisy zabiegów, czytelny cennik i rezerwacja wizyt online.', 'focus' => '60% 50%',
      'hero' => [1243, 1182], 'hero_alt' => 'Kosmetyczki w salonie beauty',
      'people' => [[900, 984], [900, 924], [900, 958]], 'slots' => ['band' => 1, 'faq' => 2, 'process' => 3],
      'pakiety' => ['Pakiety stron dla salonów beauty. <strong>Od 3 999 zł.</strong>', 'zabiegów i rezerwacji w salonie.'],
    ],
    'trenerzy' => [
      'path' => '/strony-internetowe-dla-trenerow-personalnych/', 'title' => 'Strony dla trenerów personalnych',
      'text' => 'Oferta, pakiety i zapis na pierwszy trening bez wymiany wiadomości.', 'focus' => '58% 50%',
      'hero' => [1254, 1195], 'hero_alt' => 'Trenerzy personalni na siłowni',
      'people' => [[900, 588], [900, 607], [900, 622]], 'slots' => ['band' => 1, 'faq' => 2, 'process' => 3],
      'pakiety' => ['Pakiety stron dla trenerów personalnych. <strong>Od 3 999 zł.</strong>', 'Twojej oferty treningów i zapisów.'],
    ],
    'fotografowie' => [
      'path' => '/strony-internetowe-dla-fotografow/', 'title' => 'Strony dla fotografów',
      'text' => 'Portfolio w kategoriach, szybkie galerie i zapytania o termin.', 'focus' => '74% 50%',
      'hero' => [1254, 1203], 'hero_alt' => 'Fotografowie w studiu podczas sesji',
      'people' => [[900, 1030], [900, 939], [900, 910]], 'slots' => ['process' => 1, 'faq' => 2, 'band' => 3],
      'pakiety' => ['Pakiety stron dla fotografów. <strong>Od 3 999 zł.</strong>', 'Twojego portfolio i rodzajów sesji.'],
    ],
    'restauracje' => [
      'path' => '/strony-internetowe-dla-restauracji/', 'title' => 'Strony dla restauracji i kawiarni',
      'text' => 'Menu online, rezerwacja stolika i zamówienia na wynos.', 'focus' => '66% 50%',
      'hero' => [1254, 1043], 'hero_alt' => 'Obsługa kawiarni przy ladzie z deserami',
      'people' => [[900, 955], [900, 883], [900, 893]], 'slots' => ['faq' => 1, 'process' => 2, 'band' => 3],
      'pakiety' => ['Pakiety stron dla restauracji i kawiarni. <strong>Od 3 999 zł.</strong>', 'menu, rezerwacji i zamówień w lokalu.'],
    ],
    'hotele' => [
      'path' => '/strony-internetowe-dla-hoteli/', 'title' => 'Strony dla hoteli i pensjonatów',
      'text' => 'Pokoje, ceny na wybrany termin i rezerwacje bezpośrednie.', 'focus' => '70% 50%',
      'hero' => [1254, 1236], 'hero_alt' => 'Zespół hotelu przy recepcji',
      'people' => [[900, 944], [900, 912], [900, 936]], 'slots' => ['band' => 1, 'faq' => 2, 'process' => 3],
      'pakiety' => ['Pakiety stron dla hoteli i pensjonatów. <strong>Od 3 999 zł.</strong>', 'pokoi, cennika i rezerwacji obiektu.'],
    ],
  ];
}

/** Website pages whose "Strony dla branż" section lists all eight industries. */
function zp_seo_industry_block_pages(): array {
  return ['/strony-internetowe-katowice/', '/tworzenie-stron-internetowych/', '/strona-wizytowka/', '/strony-wordpress/', '/tworzenie-landing-page/'];
}

/** Key of the industry page at $path ('' for other pages). */
function zp_seo_industry_key(string $path): string {
  foreach (zp_seo_industries() as $key => $industry) {
    if ($industry['path'] === $path) { return $key; }
  }
  return '';
}

function zp_seo_industry_img(string $file): string {
  return ZP_SUITE_URL . 'assets/img/branze/' . $file;
}

/** Polish path of the page being shown; the copied pages keep it on /en/ (as zp_seo_service_variant()). */
function zp_seo_industry_page_path(): string {
  if (is_admin() || !did_action('wp')) { return ''; }
  if (is_singular()) {
    $id = get_queried_object_id();
    if ($id) { return zp_seo_plan_path((string) get_permalink($id)); }
  }
  return zp_seo_plan_path();
}

/** Cover photo (640 and 1100 px wide). $sizes: the width it is drawn at, which object-fit can make wider than its box. */
function zp_seo_industry_cover(string $key, string $class, string $sizes): string {
  $industry = zp_seo_industries()[$key] ?? null;
  if (!$industry) { return ''; }
  $small = zp_seo_industry_img($key . '-cover-640.webp');
  return '<img' . ($class !== '' ? ' class="' . esc_attr($class) . '"' : '') . ' src="' . esc_url($small) . '" srcset="' . esc_url($small) . ' 640w, ' . esc_url(zp_seo_industry_img($key . '-cover-1100.webp')) . ' 1100w"'
    . ' sizes="' . esc_attr($sizes) . '" width="640" height="512" alt="" loading="lazy" decoding="async" style="--zp-ind-pos:' . esc_attr($industry['focus']) . '" data-zp-no-thumb="1">';
}

/** One of the three transparent photos of people from the industry (600 and 900 px wide), decorative. */
function zp_seo_industry_people(string $key, string $slot, string $class, string $sizes): string {
  $industry = zp_seo_industries()[$key] ?? null;
  $n = (int) ($industry['slots'][$slot] ?? 0);
  if (!$n) { return ''; }
  [$w, $h] = $industry['people'][$n - 1];
  $small = zp_seo_industry_img($key . '-ludzie-' . $n . '-600.webp');
  return '<img class="' . esc_attr($class) . '" src="' . esc_url($small) . '" srcset="' . esc_url($small) . ' 600w, ' . esc_url(zp_seo_industry_img($key . '-ludzie-' . $n . '.webp')) . ' ' . $w . 'w"'
    . ' sizes="' . esc_attr($sizes) . '" width="' . $w . '" height="' . $h . '" alt="" loading="lazy" decoding="async" data-zp-no-thumb="1">';
}

/** The hero photo's candidates, shared by the <img> and its preload. */
function zp_seo_industry_hero_sources(string $key): array {
  $industry = zp_seo_industries()[$key] ?? null;
  if (!$industry) { return []; }
  [$w, $h] = $industry['hero'];
  return [
    'src' => zp_seo_industry_img($key . '-hero-800.webp'),
    'srcset' => zp_seo_industry_img($key . '-hero-800.webp') . ' 800w, ' . zp_seo_industry_img($key . '-hero.webp') . ' ' . $w . 'w',
    'sizes' => '(max-width:680px) 100vw, (max-width:1100px) 82vw, 52vw',
    'width' => $w, 'height' => $h,
  ];
}

/** Preload for the industry hero photo (printed instead of the laptop's in strony-internetowe-katowice-page.php). */
function zp_seo_industry_hero_preload(): string {
  if (!function_exists('zp_seo_plan_active') || !zp_seo_plan_active()) { return ''; }
  $sources = zp_seo_industry_hero_sources(zp_seo_industry_key(zp_seo_industry_page_path()));
  if (!$sources) { return ''; }
  return '<link rel="preload" as="image" href="' . esc_url($sources['src']) . '" imagesrcset="' . esc_attr($sources['srcset']) . '" imagesizes="' . esc_attr($sources['sizes']) . '" type="image/webp" fetchpriority="high">' . "\n";
}

/**
 * Section order of the content pages (zp_seo_content_pages()). Industry pages: the price list right
 * after the hero and the other industries before the guide. Business card, WordPress and landing page:
 * the industries right after the packages. A head the content thread wrote itself wins.
 */
function zp_seo_industry_pages_layout(array $pages): array {
  if (!function_exists('zp_seo_plan_active') || !zp_seo_plan_active()) { return $pages; }
  $insert = static function (array $order, string $key, array $before): array {
    foreach ($before as $next) {
      $at = array_search($next, $order, true);
      if ($at !== false) { array_splice($order, (int) $at, 0, [$key]); return $order; }
    }
    $order[] = $key;
    return $order;
  };
  foreach ($pages as $path => $page) {
    $industry = zp_seo_industries()[zp_seo_industry_key((string) $path)] ?? null;
    $block = in_array($path, zp_seo_industry_block_pages(), true);
    if ((!$industry && !$block) || empty($page['order'])) { continue; }
    $page['drop'] = array_values(array_diff($page['drop'] ?? [], $industry ? ['pakiety', 'branze'] : ['branze']));
    $order = array_values(array_diff($page['order'], $industry ? ['pakiety', 'branze'] : ['branze']));
    if ($industry) {
      array_splice($order, (int) array_search('hero', $order, true) + 1, 0, ['pakiety']);
      $order = $insert($order, 'branze', ['guide', 'faq', 'cta']);
      $page['reuse'] = ($page['reuse'] ?? []) + [
        'pakiety' => [
          'kicker' => 'Cennik pakietów',
          'title' => $industry['pakiety'][0],
          'lead' => 'Strony realizujemy od 3 999 zł, a domena i hosting są w cenie. Pakiet to punkt startu: w Studiu Wyceny dopasujemy zakres, liczbę podstron i funkcje do ' . $industry['pakiety'][1],
        ],
        'branze' => [
          'kicker' => 'Inne branże',
          'title' => 'Projektujemy też strony <strong>dla innych branż.</strong>',
          'lead' => 'Każda branża ma własną ścieżkę klienta, dlatego każdej poświęciliśmy osobną stronę z zakresem, cennikiem i przykładami realizacji.',
        ],
      ];
    } else {
      $order = in_array('pakiety', $order, true) ? $insert($order, 'branze', [$order[(int) array_search('pakiety', $order, true) + 1] ?? 'cta']) : $insert($order, 'branze', ['guide', 'faq', 'cta']);
      $page['reuse'] = ($page['reuse'] ?? []) + [
        'branze' => [
          'kicker' => 'Strony dla branż',
          'title' => 'Dla jakiej branży <strong>potrzebujesz strony?</strong>',
          'lead' => 'Mamy osobne strony dla ośmiu branż, z zakresem, cennikiem i przykładami realizacji. Wybierz swoją i zobacz, jak projektujemy strony dla firm takich jak Twoja.',
        ],
      ];
    }
    $page['order'] = $order;
    $pages[$path] = $page;
  }
  return $pages;
}

/** Linked cover cards of the "Strony dla branż" section; $skip is the industry of the page itself. */
function zp_seo_industry_cards(string $skip = ''): string {
  $cards = '';
  $n = 0;
  $arrow = '<span class="zpIndCard__go" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M7 17 17 7M7 7h10v10"/></svg></span>';
  foreach (zp_seo_industries() as $key => $industry) {
    if ($key === $skip || !zp_seo_plan_link_is_live($industry['path'])) { continue; }
    $n++;
    $cards .= '<a class="ind-card zpIndCard reveal"' . (($n - 1) % 4 ? ' data-delay="' . (($n - 1) % 4) . '"' : '') . ' href="' . esc_url(home_url($industry['path'])) . '">'
      . zp_seo_industry_cover($key, '', '(max-width:680px) 270px, (max-width:1100px) 50vw, 440px')
      . $arrow . '<span class="ind-num">' . sprintf('%02d', $n) . '</span>'
      . '<div class="ind-body"><strong>' . esc_html($industry['title']) . '</strong><p>' . esc_html($industry['text']) . '</p></div></a>';
  }
  return $n ? '<div class="ind-grid zpIndGrid zpIndGrid--' . $n . '">' . $cards . '</div>' : '';
}

/** Replaces the template's six industry cards (not links) with the linked cover cards. */
function zp_seo_industry_block(string $section, string $skip): string {
  $start = strpos($section, '<div class="ind-grid">');
  if ($start === false) { return $section; }
  $cards = zp_seo_industry_cards($skip);
  if ($cards === '') { return $section; }
  $depth = 0;
  $pos = $start;
  while (preg_match('~<(/?)div\b[^>]*>~i', $section, $t, PREG_OFFSET_CAPTURE, $pos)) {
    $depth += $t[1][0] === '/' ? -1 : 1;
    $pos = (int) $t[0][1] + strlen($t[0][0]);
    if ($depth === 0) {
      $section = substr($section, 0, $start) . $cards . substr($section, $pos);
      return (string) preg_replace('~(<section\b[^>]*\bclass="[^"]*\bindustries)\b~', '$1 zpIndSection', $section, 1);
    }
  }
  return $section;
}

/** Hero, price band, scope card, process and FAQ photos of one industry page. */
function zp_seo_industry_page(string $html, string $key): string {
  $industry = zp_seo_industries()[$key];
  $html = zp_seo_content_in_section($html, 'id="start"', static function ($s) use ($key, $industry) {
    if (strpos($s, 'data-zp-industry=') !== false) { return $s; }
    // The photo's aspect ratio sizes it in industries.css (each industry's photo has its own).
    $ratio = round($industry['hero'][0] / $industry['hero'][1], 4);
    $s = (string) preg_replace('~(<section\b[^>]*)(>)~i', '$1 data-zp-industry="' . esc_attr($key) . '" style="--zp-hero-ar:' . $ratio . '"$2', $s, 1);
    // The laptop's description no longer fits; the photo carries its own alt text.
    $s = (string) preg_replace('~(<div class="hero-build")\s+aria-label="[^"]*"~', '$1', $s, 1);
    return (string) preg_replace_callback('~<img\b[^>]*\bclass=(["\'])[^"\']*\bhero-shot\b[^>]*>~i', static function ($m) use ($key, $industry) {
      $src = zp_seo_industry_hero_sources($key);
      $tag = (string) preg_replace('~\s(?:src|srcset|sizes|data-src|data-srcset|data-lazy-src|data-lazy-srcset|width|height|alt|loading|fetchpriority|decoding)\s*=\s*(["\']).*?\1~is', '', $m[0]);
      $tag = (string) preg_replace('~\bclass=(["\'])~', 'class=$1zpIndHero ', $tag, 1);
      return rtrim(substr($tag, 0, -1), '/ ') . ' src="' . esc_url($src['src']) . '" srcset="' . esc_attr($src['srcset']) . '" sizes="' . esc_attr($src['sizes']) . '" width="' . (int) $src['width'] . '" height="' . (int) $src['height'] . '" alt="' . esc_attr($industry['hero_alt']) . '" loading="eager" fetchpriority="high" decoding="async">';
    }, $s, 1);
  });

  // People over the band under the packages ("Nie wiesz, który zakres będzie właściwy?").
  $html = zp_seo_content_in_section($html, 'id="pakiety"', static function ($s) use ($key, $industry) {
    if (strpos($s, 'zpIndBand') !== false) { return $s; }
    $img = zp_seo_industry_people($key, 'band', 'zpIndBand__img', '(max-width:680px) 250px, 300px');
    // Height to width of the photo: the band leaves room above itself for the heads (industries.css).
    [$w, $h] = $industry['people'][$industry['slots']['band'] - 1];
    return (string) preg_replace('~<div class="packages-bottom reveal">~', '<div class="packages-bottom packages-bottom--people reveal" style="--zp-band-r:' . round($h / $w, 3) . '"><span class="zpIndBand" aria-hidden="true">' . $img . '</span>', $s, 1);
  });

  // The cover photo fills the empty lower part of the first "Co projektujemy" card.
  $html = zp_seo_content_in_section($html, 'id="zakres"', static function ($s) use ($key) {
    if (strpos($s, 'zpIndScope') !== false) { return $s; }
    $out = preg_replace_callback('~<article class="scope-card reveal">(.*?)</article>~s', static function ($m) use ($key) {
      return '<article class="scope-card scope-card--photo reveal">' . $m[1] . '<span class="zpIndScope" aria-hidden="true">' . zp_seo_industry_cover($key, '', '(max-width:680px) 100vw, (max-width:1100px) 100vw, 560px') . '</span></article>';
    }, $s, 1);
    return is_string($out) ? $out : $s;
  });

  // Process: the industry pair stands where Mateusz sits on the other website pages.
  $html = zp_seo_content_in_section($html, 'id="proces"', static function ($s) use ($key) {
    $out = preg_replace_callback('~<figure class="process-person-stage([^"]*)"([^>]*)>\s*<img\b[^>]*>~', static function ($m) use ($key) {
      return '<figure class="process-person-stage process-person-stage--industry' . $m[1] . '"' . $m[2] . '>' . zp_seo_industry_people($key, 'process', 'zpIndProcess', '(max-width:1100px) 56vw, 520px');
    }, $s, 1);
    return is_string($out) ? $out : $s;
  });

  // FAQ: the pair above the heading, turned towards the questions.
  $html = zp_seo_content_in_section($html, 'id="faq"', static function ($s) use ($key) {
    $out = preg_replace_callback('~<div class="faq-figure"([^>]*)>\s*<img\b[^>]*>~', static function ($m) use ($key) {
      return '<div class="faq-figure faq-figure--industry"' . $m[1] . '>' . zp_seo_industry_people($key, 'faq', 'zpIndFaq', '(max-width:680px) 300px, 420px');
    }, $s, 1);
    return is_string($out) ? $out : $s;
  });

  return zp_seo_content_in_section($html, 'id="branze"', static function ($s) use ($key) { return zp_seo_industry_block($s, $key); });
}

add_filter('do_shortcode_tag', static function ($output, $tag) {
  if (!is_string($output) || $output === '' || (zp_seo_service_shortcodes()[$tag] ?? '') !== 'strony' || !zp_seo_plan_active()) { return $output; }
  $path = zp_seo_industry_page_path();
  $key = zp_seo_industry_key($path);
  if ($key !== '') { return zp_seo_industry_page($output, $key); }
  if (in_array($path, zp_seo_industry_block_pages(), true)) {
    return zp_seo_content_in_section($output, 'id="branze"', static function ($s) { return zp_seo_industry_block($s, ''); });
  }
  return $output;
}, 22, 2);

add_action('wp_enqueue_scripts', static function () {
  if (!zp_seo_plan_active()) { return; }
  $path = zp_seo_industry_page_path();
  if (zp_seo_industry_key($path) === '' && !in_array($path, zp_seo_industry_block_pages(), true)) { return; }
  wp_enqueue_style('zp-industries-297', ZP_SUITE_URL . 'assets/css/blocks/industries.css', [], ZP_SEO_PLAN_VERSION);
}, 1001);

/**
 * Home tiles (templates/seo-industries.php). The covers are bright interiors, so they show a little more than the
 * project mockups did, framed on their people (--zp-ind-pos), with a light shade at the top behind the number. The
 * #zpHomeSeo selectors win over footer.css, which loads later (with the shortcode) and centres every tile photo.
 * Eight photo tiles: two rows on computers, the same swipeable row on phones, and lower tiles where there are two
 * columns.
 */
add_action('wp_head', static function () {
  if (is_admin() || !is_front_page() || !zp_seo_plan_active()) { return; }
  $tile = 'html body #zpHomeSeo .zpHomeSeo__tile';
  echo '<style id="zp-home-industries-297">'
    . $tile . ' .zpHomeSeo__mock{object-position:var(--zp-ind-pos,50% 50%)!important;opacity:.86!important;filter:saturate(.9) contrast(1.04) brightness(.8)!important}'
    . $tile . ':hover .zpHomeSeo__mock,' . $tile . ':focus-visible .zpHomeSeo__mock{opacity:1!important;filter:saturate(.96) contrast(1.06) brightness(.9)!important;transform:scale(1.03)!important}'
    . $tile . '::before{background:linear-gradient(180deg,rgba(5,7,11,.3) 0%,rgba(5,7,11,.06) 20%,rgba(5,7,11,.14) 42%,rgba(5,7,11,.74) 74%,rgba(5,7,11,.96) 100%)!important}'
    . $tile . ':hover::before,' . $tile . ':focus-visible::before{background:linear-gradient(180deg,rgba(5,7,11,.24) 0%,rgba(5,7,11,.02) 20%,rgba(5,7,11,.08) 38%,rgba(5,7,11,.64) 72%,rgba(5,7,11,.94) 100%)!important}'
    . $tile . ' .zpHomeSeo__num{opacity:.9!important;color:rgba(255,255,255,.62)!important}'
    . '@media (min-width:981px) and (max-width:1180px){' . $tile . '{min-height:340px!important}}'
    . '@media (prefers-reduced-motion:reduce){' . $tile . ':hover .zpHomeSeo__mock,' . $tile . ':focus-visible .zpHomeSeo__mock{transform:none!important}}'
    . '</style>' . "\n";
}, 61);
