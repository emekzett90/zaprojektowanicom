<?php
/**
 * ZP Suite 3.2.93 — watermarki (Mat 28.10.2026, pkt 3 wątku „Popraw wygląd podstron i Wiedzy”).
 *
 * 1. Duże słowa-watermarki („zaprojektowani”, „faq”, „realizacje”, „wiedza”, „kontakt” — na Kontakcie zamiast rotora
 *    KONTAKT / WYCENA / BRIEF…) schodzą spod zdjęć na dół swojego jasnego/ciemnego bloku hero: od dołu wtopione (maska),
 *    przyklejone do dolnej linii, w osi kickera.
 * 2. Ten sam odstęp header→kicker na każdej podstronie: 72 px na desktopie i tablecie, 48 px na telefonie.
 * 3. Na ośmiu stronach branżowych słowo-watermark pod zdjęciem w hero (lekarze, hotele, kancelarie, fotografowie,
 *    deweloperzy, restauracje, beauty, trenerzy; po angielsku na /en/).
 *
 * Cały wygląd siedzi w assets/css/wyglad-3293/watermarki.css, ładowanym tylko na tych podstronach (PL i EN trafiają
 * w ten sam obiekt strony). Ten plik tylko dobiera strony, podaje CSS-owi zmienne zależne od języka (słowo i jego
 * szerokość w em, policzoną z metryk Plus Jakarta Sans 800 przy -.08em) i drukuje znacznik słowa branżowego,
 * o który prosi includes/seo-plan/industries.php w callbacku sekcji #start.
 */
if (!defined('ABSPATH')) { exit; }

/** Which of the heroes in scope the current request shows ('' = none, so nothing is enqueued). */
function zp_w93_watermarki_page(): string {
  if (is_admin() || !did_action('wp') || is_front_page() || !is_singular('page')) { return ''; }
  $id = (int) get_queried_object_id();
  $slug = $id ? (string) get_post_field('post_name', $id) : '';
  $pages = [
    'o-nas' => 'onas', 'najczesciej-zadawane-pytania' => 'faq', 'faq' => 'faq', 'realizacje' => 'realizacje',
    'wiedza' => 'wiedza', 'case-studies' => 'cs', 'kontakt' => 'kontakt',
    'kampanie-reklamowe' => 'service', 'agencja-reklamowa-katowice' => 'service',
  ];
  if (isset($pages[$slug])) { return $pages[$slug]; }
  if (function_exists('zp_seo_industry_page_path') && function_exists('zp_seo_industry_key')) {
    $path = zp_seo_industry_page_path();
    if ($path !== '' && zp_seo_industry_key($path) !== '') { return 'industry'; }
    if ($path !== '' && function_exists('zp_seo_service_registry') && isset(zp_seo_service_registry()[$path])) { return 'service'; }
  }
  return '';
}

/** Industry key => [PL word, EN word]; the words Mat listed for the hero of each industry page. */
function zp_w93_watermarki_industry_words(): array {
  return [
    'lekarze' => ['lekarze', 'doctors'], 'hotele' => ['hotele', 'hotels'], 'kancelarie' => ['kancelarie', 'law firms'],
    'fotografowie' => ['fotografowie', 'photographers'], 'deweloperzy' => ['deweloperzy', 'developers'],
    'restauracje' => ['restauracje', 'restaurants'], 'beauty' => ['beauty', 'beauty'], 'trenerzy' => ['trenerzy', 'trainers'],
  ];
}

/**
 * Width of a word in em at Plus Jakarta Sans 800 with letter-spacing -.08em (advance widths measured with canvas
 * on the site's own font; the trailing tracking of the last letter is part of the box). The CSS divides the column
 * width by it, so a word never runs past its column (long words shrink, short ones keep the full size).
 */
function zp_w93_watermarki_word_em(string $word): float {
  $em = [
    'zaprojektowani' => 6.518, 'faq' => 1.426, 'realizacje' => 3.857, 'projects' => 3.488, 'wiedza' => 3.051, 'insights' => 3.177,
    'kontakt' => 3.277, 'contact' => 3.337,
    'knowledge' => 4.821, 'lekarze' => 2.969, 'doctors' => 3.348, 'hotele' => 2.663, 'hotels' => 2.567, 'kancelarie' => 4.29,
    'law firms' => 3.683, 'fotografowie' => 5.584, 'photographers' => 6.346, 'deweloperzy' => 5.592, 'developers' => 4.753,
    'restauracje' => 4.686, 'restaurants' => 4.739, 'beauty' => 3.009, 'trenerzy' => 3.468, 'trainers' => 3.121,
  ];
  // Unknown word: .52em per character is the average advance of this face at 800, minus the tracking.
  return $em[$word] ?? round(mb_strlen($word) * .52 - mb_strlen($word) * .08, 3);
}

/** Markup of the industry hero word, appended inside the #start section by includes/seo-plan/industries.php. */
function zp_w93_watermarki_industry_word(string $section, string $key): string {
  $words = zp_w93_watermarki_industry_words()[$key] ?? null;
  if (!$words || strpos($section, 'zpIndWord') !== false) { return $section; }
  $word = (function_exists('zp_seo_plan_is_en') && zp_seo_plan_is_en()) ? $words[1] : $words[0];
  $span = '<span class="zpIndWord" aria-hidden="true" style="--zp-w93-em:' . esc_attr((string) zp_w93_watermarki_word_em($word)) . '">' . esc_html($word) . '</span>';
  $close = strripos($section, '</section>');
  return $close === false ? $section . $span : substr($section, 0, $close) . $span . substr($section, $close);
}

add_action('wp_enqueue_scripts', static function () {
  $page = zp_w93_watermarki_page();
  if ($page === '') { return; }
  wp_enqueue_style('zp-w93-watermarki', ZP_SUITE_URL . 'assets/css/wyglad-3293/watermarki.css', [], ZP_SUITE_VERSION);
  // EN: the words the languages module translates (Realizacje „realizacje” → „projects”, Wiedza „wiedza” → „insights”,
  // Kontakt rotor „KONTAKT” → „CONTACT”) get their width in em for the size cap; the Realizacje word is drawn by ::after,
  // so its EN text is printed after the stylesheet in the same two forms (plain, then with an empty alt text).
  if (!(function_exists('zp_seo_plan_is_en') && zp_seo_plan_is_en())) { return; }
  if ($page === 'realizacje') {
    wp_add_inline_style('zp-w93-watermarki', '#zpRealizacjePage{--zp-w93-em:' . zp_w93_watermarki_word_em('projects') . '}html body #zpRealizacjePage .hero.zpRealizacjePage__hero::after{content:"projects"!important;content:"projects" / ""!important}');
  } elseif ($page === 'wiedza') {
    wp_add_inline_style('zp-w93-watermarki', 'section#zhHero.zh--knowledge{--zp-w93-em:' . zp_w93_watermarki_word_em('insights') . '}');
  } elseif ($page === 'kontakt') {
    wp_add_inline_style('zp-w93-watermarki', 'section#zhHero.zh--contact .zh__kin .zh__kinWord{--zp-w93-em:' . zp_w93_watermarki_word_em('contact') . '}');
  }
}, 1002);
