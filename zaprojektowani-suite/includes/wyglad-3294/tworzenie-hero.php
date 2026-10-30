<?php
/**
 * ZP Suite 3.2.94 — hero „Tworzenie stron internetowych” (Mat 29.10.2026): laptop wyżej, tuż pod headerem
 * (ale nie na headerze), i niższe hero na /tworzenie-stron-internetowych/ oraz /en/website-development/.
 *
 * Cały wygląd siedzi w assets/css/wyglad-3294/tworzenie-hero.css (sam CSS, bez JS i bez nowych obrazów). Ten plik
 * tylko ładuje go na tej jednej stronie: obie wersje językowe to ten sam obiekt strony 5372 (router EN podmienia
 * adres na polską stronę, więc is_page() jest prawdziwe w obu językach), a reguły w CSS są dodatkowo zawężone do
 * body.page-id-5372 i #start.hero[data-zp-service-visual="websites"] — żadna inna strona z tym szablonem
 * (Katowice, WordPress, landing page, wizytówka, opieka, strony branżowe) się nie zmienia.
 */
if (!defined('ABSPATH')) { exit; }

/** Page id of „Tworzenie stron internetowych” (PL and EN share it). */
function zp_w94_tworzenie_hero_page_id(): int {
  return 5372;
}

/** True only on the front-end request for that page. */
function zp_w94_tworzenie_hero_applies(): bool {
  if (is_admin() || !did_action('wp') || !function_exists('is_page')) { return false; }
  return is_page(zp_w94_tworzenie_hero_page_id());
}

add_action('wp_enqueue_scripts', static function () {
  if (!zp_w94_tworzenie_hero_applies()) { return; }
  wp_enqueue_style('zp-w94-tworzenie-hero', ZP_SUITE_URL . 'assets/css/wyglad-3294/tworzenie-hero.css', [], ZP_SUITE_VERSION);
}, 1003);
