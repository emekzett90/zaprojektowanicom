<?php
if (!defined('ABSPATH')) { exit; }

/**
 * ZAPROJEKTOWANI SUITE — Logo & Branding Katowice v2
 *
 * Strona jest teraz jednym blokiem 1:1 z zatwierdzonego prototypu
 * (templates/logo-branding-katowice/body.html) zamiast ośmiu plików sekcji.
 * Stare pliki w sections/ zostają na dysku dla historii/rollbacku, ale
 * nie biorą udziału w renderze.
 *
 * - header i stopka: globalne z wtyczki (motyw drukuje je wokół shortcode'a),
 * - font Plus Jakarta Sans: /wp-content/web-font/jakarta/ (deklaracje w CSS bloku),
 * - Lucide: globalny skrypt wtyczki (zp-suite-lucide); ewentualny <script src=...lucide...>
 *   z HTML-a wycina zp_suite_logo_branding_katowice_clean_html(),
 * - formularz kontaktowy: żywy [zp_contact_system] (nonce/AJAX), a nie statyczna kopia.
 */

$zp_lb_body_file = ZP_SUITE_PATH . 'templates/logo-branding-katowice/body.html';
$zp_lb_html = file_exists($zp_lb_body_file) ? file_get_contents($zp_lb_body_file) : '';

/* Assety strony leżą we wtyczce — token zamieniamy na realny URL instalacji. */
$zp_lb_html = str_replace('%%ZPLB%%', ZP_SUITE_URL . 'assets/logo-branding/', $zp_lb_html);
?>
<script id="zp-lb-assets-base">window.ZP_LB_ASSETS=<?php echo wp_json_encode(ZP_SUITE_URL . 'assets/logo-branding/'); ?>;</script>
<main id="zp-logo-branding-katowice" class="zpLogoBrandingKatPage" data-zp-logo-branding-katowice>
<?php
echo zp_suite_logo_branding_katowice_clean_html($zp_lb_html);
echo '<!-- ZP Suite / globalna sekcja kontakt pod FAQ -->' . do_shortcode('[zp_contact_system]');
?>
</main>
