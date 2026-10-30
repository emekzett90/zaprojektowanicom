<?php
/**
 * 3.2.94 — Elementor nie wciąga hero wtyczki pod stały nagłówek (Mat 29.10.2026: „xzobacz jak wyszla wiedza na mobile
 * wchodzi na header”).
 *
 * Przyczyna (live): /wiedza/ to strona Elementora (1431); jej CSS (uploads/elementor/css/post-1431.css) daje kontenerowi
 * 26476e42 --margin-top:-4px, a na telefonie (max-width:767px) -50px. Kontener Elementora ustawia z tego margines,
 * więc całe hero było podciągnięte o 50 px: napis nad tytułem kończył się 2 px pod nagłówkiem zamiast 48 px.
 * Lokalna kopia nie ma Elementora, dlatego 3.2.93 tego nie pokazywało.
 *
 * Poprawka: assets/css/wyglad-3294/elementor-guard.css (dwie reguły, ~0,5 kB) wklejone inline w <head> każdej strony
 * front-endu — bez dodatkowego pliku do pobrania, przed malowaniem, więc nic nie przeskakuje:
 *  - kontener 26476e42 strony 1431: margines górny 0 (komputer i telefon);
 *  - ogólnie: kontener Elementora (.e-con), w którym jest hero wtyczki (#zhHero, #zpAboutX, section.zpFaqPage,
 *    #zpRealizacjePage, .zpCS18, #start.hero), ma margines górny max(0, wartość z Elementora) — ujemny staje się 0,
 *    dodatni zostaje. Widżety (np. celowe ujemne marginesy w Studiu wyceny) i kontenery bez hero bez zmian.
 */
if (!defined('ABSPATH')) { exit; }

/** The guard CSS, comments stripped (read once per request). */
function zp_w94_elementor_guard_css(): string {
  static $css = null;
  if ($css !== null) { return $css; }
  $file = ZP_SUITE_PATH . 'assets/css/wyglad-3294/elementor-guard.css';
  $raw = is_readable($file) ? (string) file_get_contents($file) : '';
  $raw = (string) preg_replace('~/\*.*?\*/~s', '', $raw);
  return $css = trim((string) preg_replace('~\s*\n\s*~', '', $raw));
}

function zp_w94_elementor_guard_enqueue(): void {
  if (is_admin()) { return; }
  $css = zp_w94_elementor_guard_css();
  if ($css === '') { return; }
  wp_register_style('zp-w94-elementor-guard', false, [], ZP_SUITE_VERSION);
  wp_enqueue_style('zp-w94-elementor-guard');
  wp_add_inline_style('zp-w94-elementor-guard', $css);
}
add_action('wp_enqueue_scripts', 'zp_w94_elementor_guard_enqueue', 1);
