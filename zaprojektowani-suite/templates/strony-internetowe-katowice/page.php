<?php
if (!defined('ABSPATH')) { exit; }

/**
 * ZAPROJEKTOWANI SUITE — Strony internetowe Katowice v2
 *
 * Strona to jeden blok 1:1 z zatwierdzonego prototypu
 * (templates/strony-internetowe-katowice/body.html), analogicznie do
 * logo-branding-katowice. Stary renderer sekcyjny z CMS-em
 * (includes/katowice-page.php) zostaje na dysku dla rollbacku, ale nie
 * bierze udziału w renderze — patrz komentarz w
 * includes/strony-internetowe-katowice-page.php.
 *
 * - header i stopka: globalne z wtyczki (motyw drukuje je wokół shortcode'a),
 * - font Plus Jakarta Sans: /wp-content/web-font/jakarta/ (deklaracje w CSS bloku),
 * - Lucide: globalny skrypt wtyczki (zp-suite-lucide),
 * - formularz kontaktowy: żywy [zp_contact_system] (nonce/AJAX) — wspólny
 *   z logo-branding-katowice i kampanie-reklamowe.
 */

$zp_si_body_file = ZP_SUITE_PATH . 'templates/strony-internetowe-katowice/body.html';
$zp_si_html = file_exists($zp_si_body_file) ? file_get_contents($zp_si_body_file) : '';

/* Assety strony leżą we wtyczce — token zamieniamy na realny URL instalacji. */
/* Część grafik to te same pliki, co w assets/logo-branding i assets/campaigns —
   nie duplikujemy ich w paczce, tylko wskazujemy istniejące katalogi.
   Kolejność ma znaczenie: najpierw token z '../', potem zwykły. */
$zp_si_html = str_replace('%%ZPSI%%../', ZP_SUITE_URL . 'assets/', $zp_si_html);
$zp_si_html = str_replace('%%ZPSI%%', ZP_SUITE_URL . 'assets/strony-internetowe/', $zp_si_html);
if (function_exists('zp_suite_meta_verified_tokens')) { $zp_si_html = zp_suite_meta_verified_tokens($zp_si_html); }
?>
<script id="zp-si-assets-base">window.ZP_SI_ASSETS=<?php echo wp_json_encode(ZP_SUITE_URL . 'assets/strony-internetowe/'); ?>;</script>
<main id="zp-strony-internetowe-katowice" class="zpStronyKatowicePage" data-zp-strony-internetowe-katowice>
<?php
echo zp_suite_strony_internetowe_katowice_clean_html($zp_si_html);
echo '<!-- ZP Suite / globalna sekcja kontakt pod FAQ -->' . do_shortcode('[zp_contact_system]');
if (function_exists('zp_suite_katowice_schema')) {
  echo zp_suite_katowice_schema();
}
?>
</main>
