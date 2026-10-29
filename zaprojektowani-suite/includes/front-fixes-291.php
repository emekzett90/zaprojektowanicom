<?php
/**
 * ZP Suite v2.2.582 — Jasna stopka (whiteBand + bottomBand) premium polish, desktop + mobile.
 *  - tło z miękkimi radialnymi akcentami navy/ice + gradientowy hairline nad bandem,
 *  - kafelki ikon w kartach kontaktu spójne z mega menu (gradient + navy),
 *  - nagłówki kolumn z gradientową linią akcentu (jak w mega menu),
 *  - socale i chipy z gradientem navy na hover,
 *  - dolna belka z hairline + dopracowany przycisk "do góry".
 * Czysty CSS-override (wp_footer, PHP_INT_MAX) — zero zmian w markupie.
 */
if (!defined('ABSPATH')) { exit; }

function zp_suite_291_footer_polish_css(){
  if (is_admin()) { return; }
  ?>
<style id="zp-suite-front-fixes-291-footer-polish">
/* ===== Hairline nad jasnym bandem (tło zostaje czysto białe z bazy — v2.2.583) ===== */
html body .zpMegaFooter__whiteBand::before{
  content:""!important;
  position:absolute!important;
  left:50%!important;top:0!important;
  width:min(1740px,calc(100% - clamp(28px,6vw,112px)))!important;
  height:1px!important;
  transform:translateX(-50%)!important;
  background:linear-gradient(90deg,transparent 2%,rgba(28,71,122,.14) 30%,rgba(59,110,168,.2) 50%,rgba(28,71,122,.14) 70%,transparent 98%)!important;
  pointer-events:none!important;
  z-index:2!important;
}

/* ===== Opis marki ===== */
html body .zpMegaFooter__about{
  color:rgba(5,10,18,.64)!important;
}

/* ===== Karty kontaktu: czysta biel, neutralne kafelki ikon (v2.2.583) ===== */
html body .zpMegaFooter__contactCard{
  background:#ffffff!important;
  border:1px solid rgba(7,20,38,.085)!important;
}
html body .zpMegaFooter__contactCard:hover{
  border-color:rgba(28,71,122,.26)!important;
  background:#ffffff!important;
}
html body .zpMegaFooter__contactCard > svg,
html body .zpMegaFooter__contactCard > i svg{
  border-radius:13px!important;
  background:#f6f7f9!important;
  color:#1c477a!important;
  border:1px solid rgba(7,20,38,.08)!important;
  box-shadow:none!important;
  transition:transform .3s cubic-bezier(.16,1,.3,1)!important;
}
html body .zpMegaFooter__contactCard:hover > svg,
html body .zpMegaFooter__contactCard:hover > i svg{
  transform:scale(1.05) rotate(-3deg)!important;
}

/* ===== Socale ===== */
html body .zpMegaFooter__socials a{
  border:1px solid rgba(7,20,38,.11)!important;
  background:#ffffff!important;
  color:#1c477a!important;
}
html body .zpMegaFooter__socials a:hover{
  color:#071426!important;
  background:#ffffff!important;
  border-color:#ffffff!important;
}

/* ===== Nagłówki kolumn linków: gradientowa linia akcentu ===== */
html body .zpMegaFooter__group h3{
  position:relative!important;
  padding-bottom:13px!important;
  margin-bottom:16px!important;
  color:#071426!important;
}
html body .zpMegaFooter__group h3::after{
  content:""!important;
  position:absolute!important;
  left:0!important;bottom:0!important;
  width:42px!important;height:2px!important;
  border-radius:2px!important;
  background:linear-gradient(90deg,#1c477a 0%,#3b6ea8 70%,transparent 100%)!important;
}

/* ===== Linki: navy akcent na ikonach + hover ===== */
html body .zpMegaFooter__group a > i,
html body .zpMegaFooter__group a > i svg,
html body .zpMegaFooter__group a > svg:first-child{
  color:rgba(28,71,122,.55)!important;
}
html body .zpMegaFooter__group a:hover{
  color:#0b1a2e!important;
}
html body .zpMegaFooter__group a:hover > i,
html body .zpMegaFooter__group a:hover > i svg,
html body .zpMegaFooter__group a:hover > svg:first-child{
  color:#1c477a!important;
}
html body .zpMegaFooter__group a:hover > b,
html body .zpMegaFooter__group a:hover > b svg{
  color:#1c477a!important;
}

/* ===== Chipy "Popularne tematy" ===== */
html body .zpMegaFooter__chips{
  position:relative!important;
}
html body .zpMegaFooter__chips a{
  background:#ffffff!important;
  border:1px solid rgba(7,20,38,.1)!important;
  transition:color .22s ease,background .24s ease,border-color .24s ease,transform .22s ease!important;
}
html body .zpMegaFooter__chips a:hover{
  color:#ffffff!important;
  background:linear-gradient(120deg,#071426 0%,#102a4f 58%,#1c477a 100%)!important;
  border-color:rgba(28,71,122,.5)!important;
  transform:translateY(-2px)!important;
}

/* ===== Dolna belka ===== */
html body .zpMegaFooter__bottomBand{
  position:relative!important;
  background:linear-gradient(180deg,#03060b 0%,#050912 100%)!important;
  border-top:1px solid rgba(255,255,255,.075)!important;
}
html body .zpMegaFooter__bottomBand::before{
  content:""!important;
  position:absolute!important;
  left:50%!important;top:0!important;
  width:min(1740px,calc(100% - clamp(28px,6vw,112px)))!important;
  height:1px!important;
  transform:translateX(-50%)!important;
  background:linear-gradient(90deg,transparent 2%,rgba(142,200,247,.06) 30%,rgba(142,200,247,.18) 50%,rgba(142,200,247,.06) 70%,transparent 98%)!important;
  pointer-events:none!important;
}
html body .zpMegaFooter__bottomLinks a{
  transition:color .2s ease!important;
}
html body .zpMegaFooter__bottomLinks a{
  color:rgba(255,255,255,.46)!important;
}
html body .zpMegaFooter__bottomLinks a:hover{
  color:#ffffff!important;
}
html body .zpMegaFooter__toTop{
  color:#ffffff!important;
  background:rgba(255,255,255,.06)!important;
  border-color:rgba(255,255,255,.14)!important;
  transition:color .22s ease,background .24s ease,border-color .24s ease,transform .22s ease!important;
}
html body .zpMegaFooter__toTop:hover{
  color:#071426!important;
  background:#ffffff!important;
  border-color:#ffffff!important;
  transform:translateY(-3px)!important;
}

/* ===== Mobile ===== */
@media (max-width:760px){
  html body .zpMegaFooter__contactCard{
    border-radius:16px!important;
  }
  html body .zpMegaFooter__group{
    border-bottom:1px solid rgba(7,20,38,.07)!important;
  }
  html body .zpMegaFooter__group:last-child{
    border-bottom:0!important;
  }
  html body .zpMegaFooter__group a:active{
    color:#0b1a2e!important;
  }
  html body .zpMegaFooter__chips a{
    min-height:36px!important;
  }
}
</style>
  <?php
}
add_action('wp_footer','zp_suite_291_footer_polish_css',PHP_INT_MAX);
