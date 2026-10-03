<?php
/**
 * ZP Suite v2.2.582 — /logo-branding-katowice "Wybrane realizacje": karty projektów 2x większe
 * + nowoczesny, czytelny redesign.
 *  - szerokość karty ~2x (desktop: clamp(560px,42vw,700px) zamiast max 334px),
 *  - media 4:3 zamiast 1:1 (kontrola łącznej wysokości przy podwójnej szerokości),
 *  - wysokość auto — flex stretch wyrównuje karty w szynie, przycisk dopinany na dole
 *    (koniec z łańcuchem sztywnych height:820px z poprzednich wersji),
 *  - większa, czytelniejsza typografia (tytuł do 40px, opis 15px), badge glass na zdjęciu,
 *  - hover: delikatny lift + navy ramka + miękki cień,
 *  - tryb wyszukiwania: 2 kolumny desktop / 1 kolumna tablet i mobile.
 */
if (!defined('ABSPATH')) { exit; }

function zp_suite_292_logo_portfolio_cards_css(){
  if (is_admin()) { return; }
  $uri = isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '';
  if (!zp_suite_is_service_page('logo', $uri)) { return; }
  ?>
<style id="zp-suite-front-fixes-292-logo-cards-xl">
/* ===== Rozmiar karty: 2x + auto wysokość (stretch w szynie) ===== */
html body #zpLogoPortfolio .zpLogoCard,
html body #zpLogoPortfolio.is-searching .zpLogoCard{
  height:auto!important;
  min-height:0!important;
  max-height:none!important;
  overflow:hidden!important;
  border-radius:30px!important;
  background:#ffffff!important;
  border:1px solid rgba(7,17,31,.09)!important;
  transition:transform .34s cubic-bezier(.16,1,.3,1),border-color .3s ease,box-shadow .3s ease!important;
}
html body #zpLogoPortfolio .zpLogoCard:hover,
html body #zpLogoPortfolio .zpLogoCard:focus-visible{
  transform:translateY(-6px)!important;
  border-color:rgba(28,71,122,.28)!important;
  box-shadow:0 26px 58px rgba(7,20,38,.09)!important;
}
@media (min-width:1101px){
  html body #zpLogoPortfolio .zpLogoCard{
    width:clamp(560px,42vw,700px)!important;
    min-width:clamp(560px,42vw,700px)!important;
    max-width:700px!important;
  }
}
@media (min-width:761px) and (max-width:1100px){
  html body #zpLogoPortfolio .zpLogoCard{
    width:min(78vw,640px)!important;
    min-width:min(78vw,640px)!important;
    max-width:640px!important;
  }
}
@media (max-width:760px){
  html body #zpLogoPortfolio .zpLogoCard{
    width:88vw!important;
    min-width:88vw!important;
    max-width:88vw!important;
  }
}

/* ===== Media 4:3 + badge glass ===== */
html body #zpLogoPortfolio .zpLogoCard__media{
  aspect-ratio:4/3!important;
  min-height:0!important;
  max-height:none!important;
  border-bottom:1px solid rgba(7,17,31,.07)!important;
  background:#f4f6f9!important;
}
html body #zpLogoPortfolio .zpLogoCard__media::after{
  content:""!important;
  position:absolute!important;
  left:0!important;right:0!important;bottom:0!important;
  height:34%!important;
  background:linear-gradient(to top,rgba(5,10,18,.16),transparent)!important;
  opacity:0!important;
  transition:opacity .3s ease!important;
  pointer-events:none!important;
}
html body #zpLogoPortfolio .zpLogoCard:hover .zpLogoCard__media::after{
  opacity:1!important;
}
html body #zpLogoPortfolio .zpLogoCard__cat{
  position:absolute!important;
  left:14px!important;
  top:14px!important;
  display:inline-flex!important;
  align-items:center!important;
  gap:7px!important;
  min-height:32px!important;
  padding:0 13px!important;
  border-radius:999px!important;
  background:rgba(255,255,255,.86)!important;
  -webkit-backdrop-filter:blur(10px) saturate(1.3)!important;
  backdrop-filter:blur(10px) saturate(1.3)!important;
  border:1px solid rgba(7,20,38,.09)!important;
  color:#0b1a2e!important;
  font-size:11.5px!important;
  font-weight:720!important;
  letter-spacing:.015em!important;
  line-height:1!important;
  box-shadow:none!important;
}
html body #zpLogoPortfolio .zpLogoCard__cat svg{
  width:13px!important;
  height:13px!important;
  color:#1c477a!important;
  stroke:currentColor!important;
}

/* ===== Body: większa, czytelna typografia ===== */
html body #zpLogoPortfolio .zpLogoCard__body{
  padding:clamp(24px,2.2vw,32px) clamp(24px,2.2vw,32px) clamp(22px,2vw,28px)!important;
  display:flex!important;
  flex-direction:column!important;
  min-height:0!important;
  justify-content:flex-start!important;
}
html body #zpLogoPortfolio .zpLogoCard__topline{
  display:flex!important;
  align-items:center!important;
  justify-content:space-between!important;
  gap:12px!important;
  margin:0 0 14px!important;
}
html body #zpLogoPortfolio .zpLogoCard__type{
  color:#1c477a!important;
  font-size:11px!important;
  font-weight:800!important;
  letter-spacing:.14em!important;
  text-transform:uppercase!important;
  line-height:1.3!important;
}
html body #zpLogoPortfolio .zpLogoCard__year{
  display:inline-flex!important;
  align-items:center!important;
  min-height:26px!important;
  padding:0 10px!important;
  border-radius:999px!important;
  background:#f6f7f9!important;
  border:1px solid rgba(7,20,38,.08)!important;
  color:rgba(7,20,38,.66)!important;
  font-size:12px!important;
  font-weight:700!important;
  font-variant-numeric:tabular-nums!important;
  line-height:1!important;
}
html body #zpLogoPortfolio .zpLogoCard__title{
  margin:0!important;
  color:#05070b!important;
  font-size:clamp(28px,2.5vw,40px)!important;
  line-height:1.04!important;
  letter-spacing:-.045em!important;
  font-weight:680!important;
  min-height:0!important;
  max-height:none!important;
  display:-webkit-box!important;
  -webkit-line-clamp:2!important;
  -webkit-box-orient:vertical!important;
  overflow:hidden!important;
}
html body #zpLogoPortfolio .zpLogoCard__sub{
  margin:9px 0 0!important;
  color:rgba(28,71,122,.78)!important;
  font-size:14.5px!important;
  line-height:1.4!important;
  font-weight:600!important;
  letter-spacing:-.012em!important;
}
html body #zpLogoPortfolio .zpLogoCard__desc{
  height:auto!important;
  min-height:0!important;
  max-height:none!important;
  margin:14px 0 0!important;
  color:rgba(5,7,11,.62)!important;
  font-size:15px!important;
  line-height:1.66!important;
  letter-spacing:-.006em!important;
  display:-webkit-box!important;
  -webkit-line-clamp:4!important;
  -webkit-box-orient:vertical!important;
  overflow:hidden!important;
}
html body #zpLogoPortfolio .zpLogoCard__chips{
  display:flex!important;
  flex-wrap:wrap!important;
  gap:7px!important;
  margin:18px 0 0!important;
}
html body #zpLogoPortfolio .zpLogoCard__chips span{
  display:inline-flex!important;
  align-items:center!important;
  min-height:30px!important;
  padding:0 12px!important;
  border-radius:999px!important;
  background:#f6f7f9!important;
  border:1px solid rgba(7,20,38,.08)!important;
  color:rgba(7,20,38,.7)!important;
  font-size:12.5px!important;
  font-weight:600!important;
  line-height:1!important;
  letter-spacing:-.006em!important;
}

/* ===== Przycisk dopinany do dołu karty ===== */
html body #zpLogoPortfolio .zpLogoCard__actions{
  margin-top:auto!important;
  padding-top:22px!important;
}
html body #zpLogoPortfolio .zpLogoCard .zpLogoBtn{
  width:100%!important;
  min-height:52px!important;
  justify-content:center!important;
  font-size:15px!important;
  border-radius:999px!important;
}

/* ===== Tryb wyszukiwania: 2 kolumny desktop / 1 tablet i mobile ===== */
@media (min-width:1101px){
  html body #zpLogoPortfolio.is-searching .zpLogoPortfolio__grid{
    grid-template-columns:repeat(2,minmax(0,1fr))!important;
    gap:clamp(18px,1.8vw,28px)!important;
  }
  html body #zpLogoPortfolio.is-searching .zpLogoCard{
    width:auto!important;
    min-width:0!important;
    max-width:none!important;
  }
}
@media (min-width:761px) and (max-width:1100px){
  html body #zpLogoPortfolio.is-searching .zpLogoPortfolio__grid{
    grid-template-columns:1fr!important;
  }
  html body #zpLogoPortfolio.is-searching .zpLogoCard{
    width:auto!important;
    min-width:0!important;
    max-width:none!important;
  }
}
@media (max-width:760px){
  html body #zpLogoPortfolio.is-searching .zpLogoCard{
    width:auto!important;
    min-width:0!important;
    max-width:none!important;
  }
}

/* Szyna: trochę więcej oddechu pod większe karty */
html body #zpLogoPortfolio .zpLogoRail{
  padding-top:26px!important;
}
</style>
  <?php
}
add_action('wp_footer','zp_suite_292_logo_portfolio_cards_css',PHP_INT_MAX);
