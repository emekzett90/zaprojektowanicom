<?php
/**
 * ZP Suite v2.2.670 — Formularze kontaktowe: globalny flat pass.
 *
 * Usuwa wszystkie efekty box-shadow / text-shadow z formularza kontaktowego
 * wyświetlanego nad stopką — niezależnie od podstrony i wariantu formularza.
 * Plik jest ładowany na końcu wtyczki, aby nadpisać wcześniejsze warstwy CSS.
 */
if (!defined('ABSPATH')) { exit; }

function zp_suite_302_contact_forms_no_shadow_css(){
  if (is_admin()) { return; }
  ?>
  <style id="zp-suite-front-fixes-302-contact-no-shadow">
    html body #zpContactSystemLight,
    html body #zpContactSystemLight *,
    html body #zpContactSystemLight *::before,
    html body #zpContactSystemLight *::after,
    html body .zpContactSystemLight,
    html body .zpContactSystemLight *,
    html body .zpContactSystemLight *::before,
    html body .zpContactSystemLight *::after,
    html body #zpContactFormLight,
    html body #zpContactFormLight *,
    html body #zpContactFormLight *::before,
    html body #zpContactFormLight *::after,
    html body .zpContactFormLight,
    html body .zpContactFormLight *,
    html body .zpContactFormLight *::before,
    html body .zpContactFormLight *::after{
      box-shadow:none!important;
      -webkit-box-shadow:none!important;
      text-shadow:none!important;
    }
  </style>
  <?php
}
add_action('wp_head', 'zp_suite_302_contact_forms_no_shadow_css', PHP_INT_MAX);
add_action('wp_footer', 'zp_suite_302_contact_forms_no_shadow_css', PHP_INT_MAX);
