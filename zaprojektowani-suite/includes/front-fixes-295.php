<?php
/**
 * ZP Suite v2.2.582 — Studio Wyceny: nowy ekran wyboru usługi (chooser) — premium glass redesign.
 * Zasady (ui-ux-pro-max): jedna główna akcja ekranu (wybór usługi), karty ≥44px targetu,
 * mikro-interakcje 150–300ms ease-out, stagger 60ms, prefers-reduced-motion, kontrast AA.
 * Zmiany wyłącznie w CSS — cała logika JS (.zpbsService, .is-on, data-preview, mockupy) bez zmian:
 *  - biały panel boczny → przezroczysty; usługi jako duże glassowe karty na ciemnym hero,
 *  - copy + karty w lewej kolumnie, żywe mockupy po prawej (reagują na hover jak dotąd),
 *  - odchudzenie ekranu: ukryty dymek-podpowiedź i pigułki duplikujące karty,
 *  - hover/aktywna karta: gradientowa poświata ice, kafelek ikony w gradiencie navy→ice,
 *    strzałka w kółku wypełnia się na biało,
 *  - mobile: jedna kolumna, karty pełnej szerokości, mockupy ukryte.
 */
if (!defined('ABSPATH')) { exit; }

function zp_suite_295_studio_chooser_redesign_css(){
  if (is_admin()) { return; }
  $uri = isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '';
  if (strpos($uri, '/studio-wyceny') === false) { return; }
  ?>
<style id="zp-suite-front-fixes-295-studio-chooser">
/* ============================================================
   v2.2.589 — ETAP 1 (wybór usługi): JASNY design 1:1 z zaakceptowanym
   konceptem (studio-wyceny-wybor-uslugi-koncept-jasny.html).
   Biel, wielki ciemny heading, tag 01/05, karty 2x2 ze zdjęciami realizacji,
   hover = granatowe wypełnienie jak pakiety w briefie. Cała logika JS bez zmian
   (.zpbsService + data-service/preview, mockupy ukryte — każda karta ma shot).
   ============================================================ */

/* ===== Sekcja: biel zamiast ciemnego hero ===== */
html body #zpbsUltimate .zpbsChoose{
  background:#fff!important;
  min-height:0!important;
  padding:clamp(118px,9vw,168px) 0 clamp(56px,5vw,84px)!important;
}
html body #zpbsUltimate .zpbsChoose::before,
html body #zpbsUltimate .zpbsChoose::after,
html body #zpbsUltimate .zpbsChoose__copy::before,
html body #zpbsUltimate .zpbsChoose__copy::after,
html body #zpbsUltimate .zpbsVideo,
html body #zpbsUltimate .zpbsFloatMockups{
  display:none!important;
}
html body #zpbsUltimate .zpbsWater{
  color:#05070b!important;
  opacity:.028!important;
  -webkit-mask-image:linear-gradient(90deg,transparent 0%,#000 12%,#000 88%,transparent 100%),linear-gradient(180deg,#000 0%,#000 44%,rgba(0,0,0,.55) 68%,transparent 100%)!important;
  mask-image:linear-gradient(90deg,transparent 0%,#000 12%,#000 88%,transparent 100%),linear-gradient(180deg,#000 0%,#000 44%,rgba(0,0,0,.55) 68%,transparent 100%)!important;
  -webkit-mask-composite:source-in!important;
  mask-composite:intersect!important;
}

/* ===== Layout: jedna kolumna (copy + grid + trust) ===== */
html body #zpbsUltimate .zpbsChoose__in{
  display:block!important;
  position:relative!important;
  width:min(1740px,calc(100% - clamp(28px,6vw,112px)))!important;
  max-width:1740px!important;
  margin-left:auto!important;
  margin-right:auto!important;
}
html body #zpbsUltimate .zpbsChoose__copy{
  position:static!important;
  max-width:1060px!important;
  margin-bottom:clamp(34px,4vw,54px)!important;
}
html body #zpbsUltimate .zpbsEyebrow{
  color:#667386!important;
  font-size:10px!important;
  font-weight:850!important;
  letter-spacing:.14em!important;
  display:inline-flex!important;
  align-items:center!important;
  gap:10px!important;
}
html body #zpbsUltimate .zpbsEyebrow::before{
  content:""!important;
  width:24px!important;
  height:1px!important;
  background:#07111f!important;
}
html body #zpbsUltimate #zpbsUTitle .zpbsH1Lite{
  font-weight:660!important;
  font-variation-settings:"wght" 660!important;
}
html body #zpbsUltimate #zpbsUTitle .zpbsH1Bold{
  font-weight:760!important;
  font-variation-settings:"wght" 760!important;
}
html body #zpbsUltimate .zpbsChoose h1,
html body #zpbsUltimate #zpbsUTitle{
  color:#05070b!important;
  font-size:clamp(40px,5.4vw,80px)!important;
  line-height:.98!important;
  letter-spacing:-.05em!important;
  font-weight:760!important;
  max-width:1020px!important;
  text-wrap:balance!important;
}
html body #zpbsUltimate .zpbsChooseLead{
  color:#5c6675!important;
  max-width:620px!important;
  font-size:clamp(14.5px,1.1vw,17px)!important;
  line-height:1.66!important;
}
html body #zpbsUltimate .zpbsChooseLead strong{
  color:#102a4f!important;
  font-weight:700!important;
}
/* Progress kroków (Krok 1 z 5) — zamiast pigułki 01/05 */
html body #zpbsUltimate .zpbsChooseProgress{
  position:absolute!important;
  right:0!important;
  top:10px!important;
  display:flex!important;
  flex-direction:column!important;
  align-items:flex-end!important;
  gap:9px!important;
}
html body #zpbsUltimate .zpbsChooseProgress em{
  font-style:normal!important;
  color:#7a8493!important;
  font-size:10px!important;
  font-weight:850!important;
  letter-spacing:.16em!important;
  text-transform:uppercase!important;
  line-height:1!important;
}
html body #zpbsUltimate .zpbsChooseProgress__bar{
  display:inline-flex!important;
  gap:5px!important;
}
html body #zpbsUltimate .zpbsChooseProgress__bar i{
  display:block!important;
  width:30px!important;
  height:4px!important;
  border-radius:99px!important;
  background:rgba(7,17,31,.1)!important;
}
html body #zpbsUltimate .zpbsChooseProgress__bar i.is-on{
  background:linear-gradient(90deg,#1c477a,#102a4f 60%,#071426)!important;
}
html body #zpbsUltimate .zpbsChoose .zpbsPills{display:none!important}
html body #zpbsUltimate .zpbsChooseHint{display:none!important}

/* ===== Panel usług: przezroczysty, pełna szerokość ===== */
html body #zpbsUltimate .zpbsServicePanel{
  width:100%!important;
  max-width:none!important;
  margin:0!important;
  padding:0!important;
  border-radius:0!important;
  background:transparent!important;
  border:0!important;
  box-shadow:none!important;
  overflow:visible!important;
}
html body #zpbsUltimate .zpbsServiceTop{display:none!important}
html body #zpbsUltimate .zpbsServiceList,
html body #zpbsUltimate .zpbsServicePanel,
html body #zpbsUltimate .zpbsChoose__in{
  box-shadow:none!important;
  filter:none!important;
}
/* warstwa "wow" dawala ogromny cien calej tabeli przy hoverze karty (:has) — OFF */
html body #zpbsUltimate .zpbsServicePanel:has(.zpbsService:hover),
html body #zpbsUltimate .zpbsServicePanel:has(.zpbsService:focus-visible),
html body #zpbsUltimate .zpbsServicePanel.is-zp-pulse{
  box-shadow:none!important;
  border-color:transparent!important;
  transform:none!important;
  filter:none!important;
}
html body #zpbsUltimate .zpbsServicePanel{
  transition:none!important;
}

/* Tip nad kartami: animowana lapka "kliknij karte" */
html body #zpbsUltimate .zpbsChoosePick{
  display:inline-flex!important;
  align-items:center!important;
  gap:11px!important;
  margin:0 0 16px!important;
  color:#7a8493!important;
}
html body #zpbsUltimate .zpbsChoosePick__hand{
  width:38px!important;
  height:38px!important;
  min-width:38px!important;
  display:grid!important;
  place-items:center!important;
  border-radius:999px!important;
  background:#fff!important;
  border:1px solid rgba(7,17,31,.12)!important;
  color:#102a4f!important;
  animation:zpbsPickTap 1.7s ease-in-out infinite!important;
}
html body #zpbsUltimate .zpbsChoosePick__hand svg{
  width:18px!important;
  height:18px!important;
}
html body #zpbsUltimate .zpbsChoosePick em{
  font-style:normal!important;
  font-size:11px!important;
  font-weight:800!important;
  letter-spacing:.14em!important;
  text-transform:uppercase!important;
  line-height:1.3!important;
}
@keyframes zpbsPickTap{
  0%,100%{transform:translateY(0) scale(1)}
  45%{transform:translateY(4px) scale(.94)}
  60%{transform:translateY(0) scale(1.02)}
}
@media (prefers-reduced-motion:reduce){
  html body #zpbsUltimate .zpbsChoosePick__hand{animation:none!important}
}

/* ===== Karty 2x2 ===== */
html body #zpbsUltimate .zpbsServiceList{
  display:grid!important;
  grid-template-columns:repeat(2,minmax(0,1fr))!important;
  gap:clamp(14px,1.6vw,22px)!important;
}
html body #zpbsUltimate .zpbsServiceList .zpbsService{
  position:relative!important;
  isolation:isolate!important;
  overflow:hidden!important;
  display:flex!important;
  flex-direction:column!important;
  justify-content:flex-end!important;
  align-items:stretch!important;
  min-height:clamp(240px,24vw,320px)!important;
  padding:clamp(22px,2.4vw,34px)!important;
  border-radius:30px!important;
  border:1px solid rgba(7,17,31,.1)!important;
  background:#fff!important;
  -webkit-backdrop-filter:none!important;
  backdrop-filter:none!important;
  color:#05070b!important;
  text-align:left!important;
  box-shadow:none!important;
  contain:paint!important;
  cursor:pointer!important;
  transition:transform .32s cubic-bezier(.16,1,.3,1),border-color .26s ease,box-shadow .3s ease,color .26s ease!important;
}
/* hover/wybrana: granatowe wypełnienie wprost na tle karty (jak pakiety w briefie) */
html body #zpbsUltimate .zpbsServiceList .zpbsService::before{
  content:none!important;
  display:none!important;
}
html body #zpbsUltimate .zpbsServiceList .zpbsService:hover,
html body #zpbsUltimate .zpbsServiceList .zpbsService:focus-visible,
html body #zpbsUltimate .zpbsServiceList .zpbsService.is-on{
  transform:translateY(-5px)!important;
  border-color:#071426!important;
  color:#fff!important;
  background:radial-gradient(620px 240px at 92% 0%,rgba(28,71,122,.34),transparent 58%),linear-gradient(135deg,#04070d 0%,#071426 46%,#102a4f 78%,#1c477a 100%)!important;
  box-shadow:none!important;
  outline:none!important;
}

/* zdjęcie realizacji w prawym górnym rogu */
html body #zpbsUltimate .zpbsServiceList .zpbsServiceShot{
  position:absolute!important;
  right:-5%!important;
  top:-14%!important;
  width:58%!important;
  max-width:420px!important;
  z-index:-1!important;
  border-radius:18px!important;
  overflow:hidden!important;
  transform:rotate(4deg) translateY(12px)!important;
  opacity:.5!important;
  filter:saturate(.96)!important;
  transition:transform .5s cubic-bezier(.16,1,.3,1),opacity .4s ease!important;
  pointer-events:none!important;
  -webkit-mask-image:linear-gradient(210deg,#000 40%,transparent 84%)!important;
  mask-image:linear-gradient(210deg,#000 40%,transparent 84%)!important;
}
html body #zpbsUltimate .zpbsServiceList .zpbsServiceShot img{
  display:block!important;
  width:100%!important;
  height:auto!important;
}
html body #zpbsUltimate .zpbsServiceList .zpbsService:hover .zpbsServiceShot,
html body #zpbsUltimate .zpbsServiceList .zpbsService.is-on .zpbsServiceShot{
  transform:rotate(2deg) translateY(0) scale(1.05)!important;
  opacity:.34!important;
}

/* ikona (lewy górny róg) i strzałka (prawy górny róg) */
html body #zpbsUltimate .zpbsServiceList .zpbsServiceIcon{
  position:absolute!important;
  left:clamp(22px,2.4vw,34px)!important;
  top:clamp(22px,2.4vw,34px)!important;
  width:clamp(58px,4.6vw,72px)!important;
  height:clamp(58px,4.6vw,72px)!important;
  border-radius:20px!important;
  display:grid!important;
  place-items:center!important;
  background:#f4f6f8!important;
  border:1px solid rgba(7,17,31,.08)!important;
  color:#071426!important;
  box-shadow:none!important;
  transition:transform .34s cubic-bezier(.16,1,.3,1),background .26s ease,border-color .26s ease,color .26s ease!important;
}
html body #zpbsUltimate .zpbsServiceList .zpbsServiceIcon svg{
  width:clamp(26px,2vw,32px)!important;
  height:clamp(26px,2vw,32px)!important;
  stroke:currentColor!important;
  stroke-width:1.7!important;
}
html body #zpbsUltimate .zpbsServiceList .zpbsService:hover .zpbsServiceIcon,
html body #zpbsUltimate .zpbsServiceList .zpbsService.is-on .zpbsServiceIcon{
  transform:scale(1.08) rotate(-4deg)!important;
  background:rgba(255,255,255,.12)!important;
  border-color:rgba(255,255,255,.22)!important;
  color:#fff!important;
}
html body #zpbsUltimate .zpbsServiceList .zpbsServiceArrow{
  position:absolute!important;
  right:clamp(22px,2.4vw,34px)!important;
  top:clamp(22px,2.4vw,34px)!important;
  width:46px!important;
  height:46px!important;
  min-width:46px!important;
  border-radius:999px!important;
  display:grid!important;
  place-items:center!important;
  border:1px solid rgba(7,17,31,.14)!important;
  background:#fff!important;
  color:#071426!important;
  transition:background .24s ease,color .24s ease,border-color .24s ease,transform .3s cubic-bezier(.16,1,.3,1)!important;
}
html body #zpbsUltimate .zpbsServiceList .zpbsServiceArrow svg{
  width:18px!important;
  height:18px!important;
  stroke:currentColor!important;
  stroke-width:2!important;
}
html body #zpbsUltimate .zpbsServiceList .zpbsService:hover .zpbsServiceArrow,
html body #zpbsUltimate .zpbsServiceList .zpbsService.is-on .zpbsServiceArrow{
  background:#fff!important;
  color:#071426!important;
  border-color:#fff!important;
  transform:translate(3px,-3px)!important;
}

/* stare pseudo-pigułki przy tekście karty — OFF */
html body #zpbsUltimate .zpbsServiceList .zpbsServiceText::before,
html body #zpbsUltimate .zpbsServiceList .zpbsServiceText::after,
html body #zpbsUltimate .zpbsServiceList .zpbsServiceText span::before,
html body #zpbsUltimate .zpbsServiceList .zpbsServiceText span::after,
html body #zpbsUltimate .zpbsServiceList .zpbsServiceText strong::before,
html body #zpbsUltimate .zpbsServiceList .zpbsServiceText strong::after{
  content:none!important;
  display:none!important;
}

/* treść karty */
html body #zpbsUltimate .zpbsServiceList .zpbsServiceText{
  max-width:540px!important;
  position:relative!important;
  z-index:2!important;
}
html body #zpbsUltimate .zpbsServiceList .zpbsServiceText strong{
  display:block!important;
  color:inherit!important;
  font-size:clamp(24px,2.2vw,33px)!important;
  line-height:1.05!important;
  letter-spacing:-.04em!important;
  font-weight:750!important;
  margin:0!important;
}
html body #zpbsUltimate .zpbsServiceList .zpbsServiceText span{
  display:block!important;
  margin-top:10px!important;
  color:#5c6675!important;
  font-size:clamp(13.5px,1vw,15px)!important;
  line-height:1.6!important;
  letter-spacing:-.008em!important;
  font-weight:460!important;
  white-space:normal!important;
  transition:color .26s ease!important;
}
html body #zpbsUltimate .zpbsServiceList .zpbsService:hover .zpbsServiceText span,
html body #zpbsUltimate .zpbsServiceList .zpbsService.is-on .zpbsServiceText span{
  color:rgba(255,255,255,.76)!important;
}

/* chipy: cena / czas / technologia */
html body #zpbsUltimate .zpbsServiceList .zpbsServiceChips{
  display:flex!important;
  flex-wrap:wrap!important;
  gap:8px!important;
  margin-top:16px!important;
  position:relative!important;
  z-index:2!important;
}
html body #zpbsUltimate .zpbsServiceList .zpbsChip{
  display:inline-flex!important;
  align-items:center!important;
  gap:7px!important;
  min-height:32px!important;
  padding:0 13px!important;
  border-radius:999px!important;
  background:#fff!important;
  border:1px solid rgba(7,17,31,.12)!important;
  color:rgba(7,17,31,.72)!important;
  font-size:11.5px!important;
  font-weight:700!important;
  line-height:1!important;
  transition:background .26s ease,border-color .26s ease,color .26s ease!important;
}
html body #zpbsUltimate .zpbsServiceList .zpbsChip--price{
  color:#102a4f!important;
  border-color:rgba(16,42,79,.3)!important;
  font-weight:800!important;
}
html body #zpbsUltimate .zpbsServiceList .zpbsService:hover .zpbsChip,
html body #zpbsUltimate .zpbsServiceList .zpbsService.is-on .zpbsChip{
  background:rgba(255,255,255,.1)!important;
  border-color:rgba(255,255,255,.24)!important;
  color:rgba(255,255,255,.9)!important;
}
html body #zpbsUltimate .zpbsServiceList .zpbsService:hover .zpbsChip--price,
html body #zpbsUltimate .zpbsServiceList .zpbsService.is-on .zpbsChip--price{
  background:#fff!important;
  color:#071426!important;
  border-color:#fff!important;
}

/* ===== Trust bar pod kartami ===== */
html body #zpbsUltimate .zpbsChooseTrust{
  display:flex!important;
  flex-wrap:wrap!important;
  align-items:center!important;
  gap:clamp(20px,3vw,44px)!important;
  margin-top:clamp(32px,3.4vw,50px)!important;
  padding-top:clamp(22px,2.4vw,30px)!important;
  border-top:1px solid rgba(7,17,31,.1)!important;
  color:#7a8493!important;
}
html body #zpbsUltimate .zpbsChooseTrust__t{
  display:flex!important;
  align-items:baseline!important;
  gap:9px!important;
  font-size:13px!important;
  font-weight:600!important;
}
html body #zpbsUltimate .zpbsChooseTrust__t b{
  color:#05070b!important;
  font-size:21px!important;
  font-weight:800!important;
  letter-spacing:-.03em!important;
  font-variant-numeric:tabular-nums!important;
}
html body #zpbsUltimate .zpbsChooseTrust__t i{
  font-style:normal!important;
  color:#eab308!important;
  letter-spacing:1.5px!important;
  font-size:12px!important;
}
html body #zpbsUltimate .zpbsChooseTrust__hint{
  margin-left:auto!important;
  font-size:11px!important;
  font-weight:800!important;
  letter-spacing:.14em!important;
  text-transform:uppercase!important;
  color:#7a8493!important;
}

/* stagger wejścia kart */
@media (prefers-reduced-motion:no-preference){
  html body #zpbsUltimate[data-mode="chooser"] .zpbsService{
    opacity:0;
    transform:translateY(22px);
    animation:zpbsCardIn295 .6s cubic-bezier(.16,1,.3,1) forwards;
  }
  html body #zpbsUltimate[data-mode="chooser"] .zpbsService:nth-child(1){animation-delay:.06s}
  html body #zpbsUltimate[data-mode="chooser"] .zpbsService:nth-child(2){animation-delay:.14s}
  html body #zpbsUltimate[data-mode="chooser"] .zpbsService:nth-child(3){animation-delay:.22s}
  html body #zpbsUltimate[data-mode="chooser"] .zpbsService:nth-child(4){animation-delay:.30s}
  @keyframes zpbsCardIn295{to{opacity:1;transform:none}}
}
@media (prefers-reduced-motion:reduce){
  html body #zpbsUltimate .zpbsServiceList .zpbsService{animation:none!important;transition:none!important;opacity:1!important;transform:none!important;}
}

/* ===== JASNY HEADER na /studio-wyceny (etap 1 jest teraz biały) ===== */
html body.zpbs-studio-page #zpNewNav:not(.is-mega-open) .zpNewNav__shell{
  background:rgba(255,255,255,.96)!important;
  background-image:none!important;
  box-shadow:none!important;
  border-bottom:1px solid rgba(7,17,31,.07)!important;
}
html body.zpbs-studio-page #zpNewNav:not(.is-mega-open) .zpNewNav__link,
html body.zpbs-studio-page #zpNewNav:not(.is-mega-open) .zpNewNav__caret,
html body.zpbs-studio-page #zpNewNav:not(.is-mega-open) .zpNewNav__contactIcon,
html body.zpbs-studio-page #zpNewNav:not(.is-mega-open) .zpNewNav__burger{
  color:#071426!important;
}
html body.zpbs-studio-page #zpNewNav:not(.is-mega-open) .zpNewNav__divider{
  background:rgba(16,24,40,.09)!important;
}
html body.zpbs-studio-page #zpNewNav:not(.is-mega-open) .zpNewNav__logo--dark,
html body.zpbs-studio-page #zpNewNav:not(.is-mega-open) .zpNewNav__mobileLogo--dark{
  display:block!important;opacity:1!important;visibility:visible!important;
}
html body.zpbs-studio-page #zpNewNav:not(.is-mega-open) .zpNewNav__logo--light,
html body.zpbs-studio-page #zpNewNav:not(.is-mega-open) .zpNewNav__mobileLogo--light{
  display:none!important;opacity:0!important;visibility:hidden!important;
}
html body.zpbs-studio-page #zpNewNav:not(.is-mega-open) .zpNewNav__cta{
  background:linear-gradient(100deg,#020407 0%,#071426 34%,#102a4f 70%,#1c477a 100%)!important;
  color:#fff!important;
  border:0!important;
}
html body.zpbs-studio-page #zpNewNav .zpHeaderStaticDivider{display:none!important}

/* ===== MOBILE ===== */
@media (max-width:900px){
  html body #zpbsUltimate .zpbsServiceList{
    grid-template-columns:1fr!important;
    gap:14px!important;
  }
  /* Karta mobile: baner zdjęciowy u góry, ikona nachodząca, treść pod spodem */
  html body #zpbsUltimate .zpbsServiceList .zpbsService{
    min-height:0!important;
    padding:0 18px 18px!important;
    border-radius:24px!important;
    justify-content:flex-start!important;
  }
  html body #zpbsUltimate .zpbsServiceList .zpbsServiceShot{
    left:0!important;
    right:0!important;
    top:0!important;
    width:100%!important;
    max-width:none!important;
    height:128px!important;
    border-radius:0!important;
    transform:none!important;
    opacity:1!important;
    -webkit-mask-image:linear-gradient(180deg,#000 55%,transparent 100%)!important;
    mask-image:linear-gradient(180deg,#000 55%,transparent 100%)!important;
  }
  html body #zpbsUltimate .zpbsServiceList .zpbsServiceShot img{
    width:100%!important;
    height:100%!important;
    object-fit:cover!important;
    object-position:center 30%!important;
  }
  html body #zpbsUltimate .zpbsServiceList .zpbsService:hover .zpbsServiceShot,
  html body #zpbsUltimate .zpbsServiceList .zpbsService.is-on .zpbsServiceShot{
    transform:none!important;
    opacity:.5!important;
  }
  html body #zpbsUltimate .zpbsServiceList .zpbsServiceIcon{
    position:static!important;
    margin:76px 0 12px!important;
    width:54px!important;height:54px!important;min-width:54px!important;
    border-radius:16px!important;
    background:#fff!important;
    border:1px solid rgba(7,17,31,.1)!important;
    box-shadow:0 6px 18px rgba(7,20,38,.08)!important;
  }
  html body #zpbsUltimate .zpbsServiceList .zpbsService:hover .zpbsServiceIcon,
  html body #zpbsUltimate .zpbsServiceList .zpbsService.is-on .zpbsServiceIcon{
    background:rgba(255,255,255,.12)!important;
    border-color:rgba(255,255,255,.24)!important;
    box-shadow:none!important;
  }
  html body #zpbsUltimate .zpbsServiceList .zpbsServiceArrow{
    right:14px!important;top:14px!important;
    width:42px!important;height:42px!important;min-width:42px!important;
    background:rgba(255,255,255,.92)!important;
  }
  html body #zpbsUltimate .zpbsServiceList .zpbsServiceText strong{font-size:21px!important}
  html body #zpbsUltimate .zpbsServiceList .zpbsServiceText span{font-size:13px!important;margin-top:7px!important}
  html body #zpbsUltimate .zpbsServiceList .zpbsServiceChips{margin-top:13px!important}
  html body #zpbsUltimate .zpbsServiceList .zpbsChip{min-height:30px!important;font-size:11px!important;padding:0 11px!important}
  html body #zpbsUltimate .zpbsServiceList .zpbsService:active{transform:scale(.985)!important}
  /* Progress: pełna szerokość pod leadem */
  html body #zpbsUltimate .zpbsChooseProgress{
    position:static!important;
    align-items:stretch!important;
    margin:18px 0 6px!important;
  }
  html body #zpbsUltimate .zpbsChooseProgress em{
    align-self:flex-start!important;
  }
  html body #zpbsUltimate .zpbsChooseProgress__bar{
    width:100%!important;
  }
  html body #zpbsUltimate .zpbsChooseProgress__bar i{
    flex:1 1 0!important;
    width:auto!important;
    height:5px!important;
  }
  /* Tip z łapką: wyśrodkowany nad kartami */
  html body #zpbsUltimate .zpbsChoosePick{
    width:100%!important;
    justify-content:center!important;
    margin:2px 0 14px!important;
  }
  html body #zpbsUltimate .zpbsChooseTrust{
    gap:14px 22px!important;
  }
  html body #zpbsUltimate .zpbsChooseTrust__hint{display:none!important}
}

/* ============================================================
   v2.2.593 — "ZAUFALI NAM" w studio: tylko na kroku wyboru usługi
   + pełny dark full-bleed w stylu sekcji "Zakres usług" (WIDGET-2).
   ============================================================ */
html body #zpbsUltimate[data-mode="brief"] .zpbsTrustSlot{
  display:none!important;
}
/* v2.2.594: full-bleed przez margines (100vw) z ciemnym tłem BEZPOŚREDNIO na slocie —
   pewne malowanie niezależnie od warstw wstrzykniętego widgetu (poprzedni pseudo z
   z-index:-2 przegrywał ze stackingiem i tło zostawało białe). */
html body #zpbsUltimate .zpbsTrustSlot{
  position:relative!important;
  width:100vw!important;
  max-width:100vw!important;
  margin-left:calc(50% - 50vw)!important;
  margin-right:calc(50% - 50vw)!important;
  overflow:hidden!important;
  background:
    radial-gradient(circle at 12% 0%,rgba(28,71,122,.20),transparent 34%),
    radial-gradient(circle at 86% 14%,rgba(59,110,168,.12),transparent 32%),
    radial-gradient(circle at 50% 110%,rgba(16,42,79,.10),transparent 38%),
    linear-gradient(135deg,#030407 0%,#05070b 42%,#07101d 100%)!important;
}
/* subtelna siatka jak w WIDGET-2 — overlay, nie blokuje treści */
html body #zpbsUltimate .zpbsTrustSlot::before{
  content:""!important;
  position:absolute!important;
  inset:0!important;
  z-index:0!important;
  pointer-events:none!important;
  opacity:.055!important;
  background-image:
    linear-gradient(rgba(255,255,255,.12) 1px,transparent 1px),
    linear-gradient(90deg,rgba(255,255,255,.12) 1px,transparent 1px)!important;
  background-size:54px 54px!important;
  -webkit-mask-image:linear-gradient(180deg,transparent 0%,#000 18%,#000 78%,transparent 100%)!important;
  mask-image:linear-gradient(180deg,transparent 0%,#000 18%,#000 78%,transparent 100%)!important;
}
/* treść trustu ponad siatką i tłem */
html body #zpbsUltimate .zpbsTrustSlot > *{
  position:relative!important;
  z-index:1!important;
}
/* ------------------------------------------------------------
   v2.2.595 — kompaktowa treść trustu (własny markup .zpbsTrustDark).
   Wcześniej wstrzykiwaliśmy pinowany widget z home (spacer 330vh +
   sticky 100svh z białym tłem) — w slocie z overflow:hidden sticky
   przestaje działać: biała "rozwalona" sekcja + ~2 ekrany pustego
   ciemnego tła. Teraz zwykła statyczna siatka logotypów.
   ------------------------------------------------------------ */
html body #zpbsUltimate .zpbsTrustDark{
  background:transparent!important;
  color:#fff!important;
}
html body #zpbsUltimate .zpbsTrustDark__in{
  position:relative!important;
  box-sizing:border-box!important;
  /* v2.2.596: szerokość 1:1 z panelem choosera powyżej (1740px) */
  width:min(1740px,calc(100% - clamp(28px,6vw,112px)))!important;
  margin:0 auto!important;
  padding:clamp(52px,6.4vw,80px) 0 clamp(44px,5.6vw,68px)!important;
}
html body #zpbsUltimate .zpbsTrustDark__head{
  max-width:820px!important;
  margin:0 0 clamp(26px,3.4vw,40px)!important;
}
html body #zpbsUltimate .zpbsTrustDark__eyebrow{
  display:inline-flex!important;
  align-items:center!important;
  gap:12px!important;
  margin:0 0 14px!important;
  font-size:11px!important;
  font-weight:600!important;
  letter-spacing:.32em!important;
  text-transform:uppercase!important;
  color:rgba(255,255,255,.66)!important;
}
html body #zpbsUltimate .zpbsTrustDark__eyebrow i{
  width:26px!important;
  height:1px!important;
  flex:none!important;
  background:linear-gradient(90deg,#8ec8f7,rgba(142,200,247,0))!important;
}
html body #zpbsUltimate .zpbsTrustDark__title{
  margin:0!important;
  font-size:clamp(28px,3.3vw,44px)!important;
  line-height:1.08!important;
  font-weight:600!important;
  letter-spacing:-.03em!important;
  color:#fff!important;
}
html body #zpbsUltimate .zpbsTrustDark__title span{
  background:linear-gradient(100deg,#ffffff 8%,#8ec8f7 55%,#5f93c9 100%)!important;
  -webkit-background-clip:text!important;
  background-clip:text!important;
  color:transparent!important;
  -webkit-text-fill-color:transparent!important;
}
html body #zpbsUltimate .zpbsTrustDark__lead{
  margin:14px 0 0!important;
  max-width:640px!important;
  font-size:15.5px!important;
  line-height:1.65!important;
  color:rgba(255,255,255,.62)!important;
}
html body #zpbsUltimate .zpbsTrustDark__grid{
  display:grid!important;
  grid-template-columns:repeat(6,minmax(0,1fr))!important;
  gap:10px!important;
}
html body #zpbsUltimate .zpbsTrustDark__logo{
  display:grid!important;
  place-items:center!important;
  height:clamp(86px,7vw,104px)!important;
  border-radius:16px!important;
  background:rgba(255,255,255,.045)!important;
  border:1px solid rgba(255,255,255,.10)!important;
  box-shadow:inset 0 1px 0 rgba(255,255,255,.06)!important;
  transition:border-color .28s ease,background-color .28s ease,transform .28s ease!important;
}
html body #zpbsUltimate .zpbsTrustDark__logo:hover{
  border-color:rgba(142,200,247,.38)!important;
  background:rgba(255,255,255,.075)!important;
  transform:translateY(-2px)!important;
}
/* loga: białe; hover = błękitno-biały (ice) */
html body #zpbsUltimate .zpbsTrustDark__logo img{
  display:block!important;
  max-width:64%!important;
  max-height:42px!important;
  width:auto!important;
  height:auto!important;
  object-fit:contain!important;
  filter:brightness(0) invert(1)!important;
  opacity:.82!important;
  transition:filter .28s ease,opacity .28s ease!important;
}
html body #zpbsUltimate .zpbsTrustDark__logo--wide img{
  max-width:78%!important;
}
/* loga "portretowe"/kwadratowe (Vista, Sfera, Gravia, AP) — wyższy limit, żeby nie zniknęły */
html body #zpbsUltimate .zpbsTrustDark__logo--tall img{
  max-height:60px!important;
}
html body #zpbsUltimate .zpbsTrustDark__logo:hover img{
  filter:brightness(0) invert(.93) sepia(.9) saturate(2.2) hue-rotate(175deg) brightness(1.55)!important;
  opacity:1!important;
}
html body #zpbsUltimate .zpbsTrustDark__stats{
  display:flex!important;
  flex-wrap:wrap!important;
  align-items:center!important;
  gap:12px 16px!important;
  margin:clamp(24px,3vw,34px) 0 0!important;
  font-size:13.5px!important;
  color:rgba(255,255,255,.6)!important;
}
html body #zpbsUltimate .zpbsTrustDark__stats strong{
  color:#fff!important;
  font-weight:650!important;
  font-size:15px!important;
  letter-spacing:-.01em!important;
  margin-right:2px!important;
}
html body #zpbsUltimate .zpbsTrustDark__stats i{
  width:4px!important;
  height:4px!important;
  border-radius:50%!important;
  background:rgba(255,255,255,.28)!important;
}
/* v2.2.596: Mateusz po prawej (jak na stronie głównej) — tylko desktop.
   Wycinanka kotwiczona do prawego-dolnego rogu, dół wtopiony maską w tło. */
html body #zpbsUltimate .zpbsTrustDark__person{
  display:none!important;
  margin:0!important;
  pointer-events:none!important;
}
@media (min-width:1200px){
  html body #zpbsUltimate .zpbsTrustDark__in{
    padding-right:clamp(340px,24vw,470px)!important;
  }
  html body #zpbsUltimate .zpbsTrustDark__person{
    display:block!important;
    position:absolute!important;
    right:clamp(-40px,-1.6vw,-8px)!important;
    bottom:0!important;
    top:clamp(34px,4.6vw,64px)!important;
    width:auto!important;
    z-index:0!important;
    -webkit-mask-image:linear-gradient(to bottom,#000 0%,#000 72%,rgba(0,0,0,.7) 86%,rgba(0,0,0,.16) 97%,transparent 100%)!important;
    mask-image:linear-gradient(to bottom,#000 0%,#000 72%,rgba(0,0,0,.7) 86%,rgba(0,0,0,.16) 97%,transparent 100%)!important;
  }
  html body #zpbsUltimate .zpbsTrustDark__person img{
    display:block!important;
    height:100%!important;
    width:auto!important;
    object-fit:contain!important;
    object-position:right bottom!important;
    filter:none!important;
    opacity:1!important;
  }
  /* poświata za sylwetką, żeby nie ginęła na czarnym */
  html body #zpbsUltimate .zpbsTrustDark__person::before{
    content:""!important;
    position:absolute!important;
    inset:6% -12% -4% -12%!important;
    z-index:-1!important;
    background:radial-gradient(ellipse at 52% 62%,rgba(59,110,168,.20),rgba(28,71,122,.08) 48%,transparent 72%)!important;
    filter:blur(2px)!important;
  }
}
@media (max-width:1100px){
  html body #zpbsUltimate .zpbsTrustDark__grid{
    grid-template-columns:repeat(4,minmax(0,1fr))!important;
  }
}
@media (max-width:720px){
  html body #zpbsUltimate .zpbsTrustDark__in{
    width:calc(100% - 32px)!important;
  }
  html body #zpbsUltimate .zpbsTrustDark__grid{
    grid-template-columns:repeat(3,minmax(0,1fr))!important;
    gap:8px!important;
  }
  html body #zpbsUltimate .zpbsTrustDark__logo{
    height:74px!important;
    border-radius:14px!important;
  }
  html body #zpbsUltimate .zpbsTrustDark__logo img{
    max-width:72%!important;
    max-height:34px!important;
  }
  html body #zpbsUltimate .zpbsTrustDark__logo--tall img{
    max-height:48px!important;
  }
  html body #zpbsUltimate .zpbsTrustDark__lead{
    font-size:14.5px!important;
  }
}
@media (max-width:360px){
  html body #zpbsUltimate .zpbsTrustDark__grid{
    grid-template-columns:repeat(2,minmax(0,1fr))!important;
  }
  html body #zpbsUltimate .zpbsTrustDark__logo{
    height:82px!important;
  }
}

/* ============================================================
   v2.2.584 — STICKY BAR konfiguratora: FULL-BLEED przy dolnej krawędzi.
   Ciemny navy gradient na całą szerokość, hairline ice u góry.
   Desktop: krok + podsumowanie (pakiet, "Krok x z 5 · Budżet/Suma")
            + przyciski Usługa / Wstecz / Dalej.
   Mobile:  TYLKO Wstecz + Dalej (duplikat głównych przycisków).
   Mechanika pokazywania bez zmian (data-mode="brief" + transformy z bazy).
   ============================================================ */
/* Pancerz na stary cache: dawna reguła [class*="zpbsSticky"] robiła position:fixed
   KAŻDEMU dziecku paska (pasek zapadał się do 1px, a zawartość lądowała poza ekranem).
   Usunięta u źródła (front-fixes-185.php + footer.php), a tu neutralizacja gdyby
   zminifikowana kopia wisiała jeszcze w cache: */
html body #zpbsUltimate .zpbsSticky [class*="zpbsSticky"]{
  position:static!important;
  transform:none!important;
  z-index:auto!important;
  isolation:auto!important;
}
html body #zpbsUltimate .zpbsSticky{
  left:50%!important;
  bottom:0!important;
  width:100vw!important;
  max-width:100vw!important;
  border-radius:0!important;
  border:0!important;
  border-top:1px solid rgba(255,255,255,.1)!important;
  background:
    radial-gradient(900px 140px at 88% 0%,rgba(28,71,122,.35),transparent 60%),
    linear-gradient(90deg,#04070d 0%,#071426 52%,#0b1f3c 100%)!important;
  -webkit-backdrop-filter:blur(18px) saturate(1.3)!important;
  backdrop-filter:blur(18px) saturate(1.3)!important;
  box-shadow:0 -18px 48px rgba(0,0,0,.2)!important;
  overflow:visible!important;
}
html body #zpbsUltimate .zpbsSticky::before{
  content:""!important;
  position:absolute!important;
  left:0!important;right:0!important;top:-1px!important;
  height:1px!important;
  background:linear-gradient(90deg,transparent 3%,rgba(142,200,247,.42) 50%,transparent 97%)!important;
  pointer-events:none!important;
}
html body #zpbsUltimate .zpbsStickyIn{
  display:grid!important;
  grid-template-columns:auto minmax(0,1fr) auto!important;
  align-items:center!important;
  gap:18px!important;
  width:min(1500px,calc(100% - 48px))!important;
  margin:0 auto!important;
  min-height:78px!important;
  padding:12px 0!important;
}
html body #zpbsUltimate .zpbsStickyStep{
  width:46px!important;
  height:46px!important;
  display:grid!important;
  place-items:center!important;
  border-radius:999px!important;
  background:linear-gradient(150deg,#102a4f,#1c477a)!important;
  border:1px solid rgba(142,200,247,.34)!important;
  color:#fff!important;
  font-variant-numeric:tabular-nums!important;
}
html body #zpbsUltimate .zpbsStickyText{
  min-width:0!important;
  display:grid!important;
  gap:5px!important;
}
html body #zpbsUltimate .zpbsStickyText strong{
  color:#fff!important;
  font-size:14.5px!important;
  font-weight:800!important;
  letter-spacing:-.018em!important;
  white-space:nowrap!important;
  overflow:hidden!important;
  text-overflow:ellipsis!important;
}
html body #zpbsUltimate .zpbsStickyText span{
  color:rgba(255,255,255,.62)!important;
  font-size:11.5px!important;
  font-weight:600!important;
  letter-spacing:0!important;
  white-space:nowrap!important;
  overflow:hidden!important;
  text-overflow:ellipsis!important;
}
html body #zpbsUltimate .zpbsStickyActions{
  display:flex!important;
  align-items:center!important;
  gap:9px!important;
}
html body #zpbsUltimate .zpbsStickyBtn{
  min-height:50px!important;
  padding:0 18px!important;
  border-radius:999px!important;
  border:1px solid rgba(255,255,255,.16)!important;
  background:transparent!important;
  color:#fff!important;
  cursor:pointer!important;
  transition:background .22s ease,color .22s ease,border-color .22s ease,transform .24s cubic-bezier(.16,1,.3,1)!important;
}
html body #zpbsUltimate .zpbsStickyBtn:hover{
  background:rgba(255,255,255,.09)!important;
  border-color:rgba(255,255,255,.3)!important;
  color:#ffffff!important;
}
html body #zpbsUltimate .zpbsStickyBtn:hover svg{
  stroke:#ffffff!important;
  color:#ffffff!important;
}
html body #zpbsUltimate .zpbsStickyBtnPrimary:hover svg{
  stroke:#071426!important;
  color:#071426!important;
}
html body #zpbsUltimate .zpbsStickyBtn svg{
  transition:transform .24s cubic-bezier(.16,1,.3,1)!important;
}
html body #zpbsUltimate .zpbsStickyBtnPrimary{
  background:#ffffff!important;
  color:#071426!important;
  border-color:#ffffff!important;
  box-shadow:0 8px 26px rgba(255,255,255,.07)!important;
}
html body #zpbsUltimate .zpbsStickyBtnPrimary:hover{
  background:#ffffff!important;
  color:#071426!important;
  transform:translateY(-1px)!important;
  box-shadow:0 12px 32px rgba(255,255,255,.1)!important;
}
html body #zpbsUltimate .zpbsStickyBtnPrimary:hover svg{
  transform:translateX(3px)!important;
}
/* Pasek nie zasłania treści na samym dole briefu */
@media (min-width:761px){
  html body #zpbsUltimate[data-mode="brief"]{
    padding-bottom:96px!important;
  }
}

/* ===== Mobile: tylko Wstecz + Dalej ===== */
@media (max-width:780px){
  /* v2.2.593: jasny glass jak sticky na stronie głównej */
  html body #zpbsUltimate .zpbsSticky{
    bottom:calc(8px + env(safe-area-inset-bottom,0px))!important;
    width:calc(100vw - 16px)!important;
    max-width:calc(100vw - 16px)!important;
    border-radius:22px!important;
    border:1px solid rgba(255,255,255,.96)!important;
    background:linear-gradient(180deg,rgba(255,255,255,.97),rgba(247,250,252,.94))!important;
    -webkit-backdrop-filter:blur(18px) saturate(1.4)!important;
    backdrop-filter:blur(18px) saturate(1.4)!important;
    box-shadow:0 15px 38px rgba(7,20,38,.13),0 3px 10px rgba(7,20,38,.045),inset 0 1px 0 rgba(255,255,255,1)!important;
  }
  html body #zpbsUltimate .zpbsSticky::before{
    display:none!important;
  }
  html body #zpbsUltimate .zpbsStickyStep,
  html body #zpbsUltimate .zpbsStickyText,
  html body #zpbsUltimate .zpbsStickySummaryBtn,
  html body #zpbsUltimate .zpbsStickyBtn[data-back-to-chooser]{
    display:none!important;
  }
  html body #zpbsUltimate .zpbsStickyIn{
    display:block!important;
    width:100%!important;
    min-height:0!important;
    padding:10px!important;
  }
  html body #zpbsUltimate .zpbsStickyActions{
    display:grid!important;
    grid-template-columns:1fr 1.5fr!important;
    gap:10px!important;
    width:100%!important;
  }
  /* v2.2.587: typografia przycisków odporna na warstwy v209/v214/v2259
     (małe 10-11px + letter-spacing .05-.08em + uppercase) — selektor (1,2,2)
     i każdy property jawnie, na buttonie i na wszystkich dzieciach. */
  html body #zpbsUltimate .zpbsSticky .zpbsStickyBtn,
  html body #zpbsUltimate .zpbsSticky .zpbsStickyActions .zpbsStickyBtn{
    min-height:56px!important;
    height:56px!important;
    font-size:15px!important;
    font-weight:800!important;
    font-variation-settings:"wght" 800!important;
    letter-spacing:-.012em!important;
    word-spacing:normal!important;
    text-transform:none!important;
    line-height:1!important;
    border-radius:999px!important;
    width:100%!important;
    padding:0 16px!important;
    gap:9px!important;
  }
  html body #zpbsUltimate .zpbsSticky .zpbsStickyBtn *{
    font-size:15px!important;
    font-weight:800!important;
    font-variation-settings:"wght" 800!important;
    letter-spacing:-.012em!important;
    word-spacing:normal!important;
    text-transform:none!important;
    line-height:1!important;
  }
  html body #zpbsUltimate .zpbsSticky .zpbsStickyBtn svg{
    width:19px!important;
    height:19px!important;
    min-width:19px!important;
  }
  html body #zpbsUltimate .zpbsSticky .zpbsStickyBtn:not(.zpbsStickyBtnPrimary){
    background:#ffffff!important;
    border:1px solid rgba(7,17,31,.16)!important;
    color:#071426!important;
  }
  html body #zpbsUltimate .zpbsSticky .zpbsStickyBtn:not(.zpbsStickyBtnPrimary) svg{
    stroke:#071426!important;
    color:#071426!important;
  }
  html body #zpbsUltimate .zpbsSticky .zpbsStickyBtnPrimary{
    background:linear-gradient(100deg,#020407 0%,#071426 34%,#102a4f 70%,#1c477a 100%)!important;
    color:#ffffff!important;
    border:0!important;
  }
  html body #zpbsUltimate .zpbsSticky .zpbsStickyBtnPrimary svg{
    stroke:#ffffff!important;
    color:#ffffff!important;
  }
  html body #zpbsUltimate .zpbsSticky .zpbsStickyBtn:active{
    transform:scale(.975)!important;
  }
  html body #zpbsUltimate .zpbsStickyBtnPrimary{
    box-shadow:0 10px 26px rgba(7,20,38,.18)!important;
  }
}
@media (prefers-reduced-motion:reduce){
  html body #zpbsUltimate .zpbsSticky,
  html body #zpbsUltimate .zpbsStickyBtn,
  html body #zpbsUltimate .zpbsStickyBtn svg{
    transition:none!important;
  }
}
</style>
  <?php
}
add_action('wp_footer','zp_suite_295_studio_chooser_redesign_css',PHP_INT_MAX);
