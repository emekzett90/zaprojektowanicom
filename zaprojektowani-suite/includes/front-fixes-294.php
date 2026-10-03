<?php
/**
 * ZP Suite v2.2.582 — Formularz kontaktowy nad stopką: spójny wygląd na każdej podstronie.
 *  - checkbox zgody "Wyrażam zgodę…": jeden spójny, JASNY custom checkbox wszędzie
 *    (zamiast natywnego z accent-color, który na części stron wyglądał ciemno/rozjeżdżał się
 *    między przeglądarkami),
 *  - uproszczenie wizualne: lżejsze cienie na zaznaczonych kafelkach trybu i chipach zakresu,
 *    delikatniejsze dividery, czytelniejsza zgoda i notka pod przyciskiem,
 *  - dotyczy .zpContactFormLight, czyli tego samego formularza na home, /kontakt/
 *    i wszystkich podstronach usługowych (shortcode [zp_contact_system]).
 */
if (!defined('ABSPATH')) { exit; }

function zp_suite_294_contact_form_unify_css(){
  if (is_admin()) { return; }
  ?>
<style id="zp-suite-front-fixes-294-contact-form-unify">
/* ===== Zgoda: spójny jasny checkbox na każdej stronie ===== */
html body .zpContactFormLight__consent{
  display:grid!important;
  grid-template-columns:22px minmax(0,1fr)!important;
  gap:12px!important;
  align-items:start!important;
  margin:20px 0 0!important;
  color:rgba(7,17,31,.62)!important;
  font-size:12.5px!important;
  line-height:1.5!important;
  cursor:pointer!important;
}
html body .zpContactFormLight__consent input[type="checkbox"]{
  appearance:none!important;
  -webkit-appearance:none!important;
  width:22px!important;
  height:22px!important;
  min-width:22px!important;
  margin:0!important;
  margin-top:1px!important;
  border-radius:7px!important;
  border:1px solid rgba(7,17,31,.2)!important;
  background-color:#ffffff!important;
  background-repeat:no-repeat!important;
  background-position:center!important;
  background-size:13px 13px!important;
  cursor:pointer!important;
  transition:background-color .2s ease,border-color .2s ease,box-shadow .2s ease!important;
  accent-color:initial!important;
  display:inline-block!important;
  position:static!important;
  opacity:1!important;
  pointer-events:auto!important;
}
html body .zpContactFormLight__consent input[type="checkbox"]:hover{
  border-color:rgba(28,71,122,.4)!important;
}
html body .zpContactFormLight__consent input[type="checkbox"]:focus-visible{
  outline:none!important;
  box-shadow:0 0 0 4px rgba(28,71,122,.16)!important;
  border-color:rgba(28,71,122,.5)!important;
}
html body .zpContactFormLight__consent input[type="checkbox"]:checked{
  border-color:rgba(16,42,79,.55)!important;
  background-color:#0b1f3c!important;
  background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23ffffff' stroke-width='3.4' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M20 6 9 17l-5-5'/%3E%3C/svg%3E"),linear-gradient(135deg,#071426 0%,#102a4f 60%,#1c477a 100%)!important;
  background-size:13px 13px,100% 100%!important;
  background-position:center,center!important;
}
html body .zpContactFormLight__consent > span{
  color:rgba(7,17,31,.62)!important;
  font-size:12.5px!important;
  line-height:1.5!important;
}

/* ===== Spokojniejszy, prostszy wygląd ===== */
/* Kafelki trybu (Szybki kontakt / Pełny brief / Oddzwońcie): mniej cienia, subtelny lift */
html body .zpContactFormLight__mode input:checked + span{
  box-shadow:0 14px 34px rgba(7,17,31,.10),inset 0 1px 0 rgba(255,255,255,.12)!important;
  transform:translateY(-2px)!important;
}
/* Chipy zakresu projektu: bez ciężkiego cienia po zaznaczeniu */
html body .zpContactFormLight__checks input:checked + span{
  box-shadow:none!important;
  transform:translateY(-1px)!important;
}
/* Delikatniejsze dividery między krokami */
html body .zpContactFormLight__divider{
  opacity:.7!important;
}
/* Notka pod przyciskiem — czytelna, bez konkurowania z CTA */
html body .zpContactFormLight [data-submit-note]{
  color:rgba(7,17,31,.5)!important;
  font-size:12px!important;
  line-height:1.5!important;
}
</style>
  <?php
}
add_action('wp_footer','zp_suite_294_contact_form_unify_css',PHP_INT_MAX);
