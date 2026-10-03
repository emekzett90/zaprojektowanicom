<?php
/**
 * ZP Suite v2.2.666 — mega menu (ciemne + jasne) i drawer mobilny: modern editorial polish.
 *
 * Kierunek (feedback usera): nowocześnie, BEZ drop shadowów — separacja przez hairline'y,
 * światło i typografię, nie przez cienie.
 * - Panel mega: zero box-shadow (dot. też karty promo na hover), dolna krawędź = 1px hairline.
 * - Nagłówki kolumn: editorial micro-labels (uppercase, letter-spacing) zamiast dużych bold.
 * - Kolumny rozdzielone pionowymi hairline'ami (grid ma gap:0 + padding-right:30px,
 *   więc border-left + padding-left:30px daje symetryczne światło).
 * - Karta promo: plakietki "Start marki"/"Realizacja: Siemianowski" usunięte z markupu
 *   (templates/header.php) — tu twardy display:none jako siatka bezpieczeństwa dla cache;
 *   zdjęcie realizacji dostaje lżejszy gradient u góry, bo nic go już nie zasłania.
 * - Drawer: panel bez cienia (hairline na krawędzi), stagger wejścia pozycji,
 *   dopracowane stany :active/:focus-visible.
 *
 * Warstwa wygrywa z front-fixes-290 późniejszym miejscem w dokumencie (ta sama specyficzność).
 */
if (!defined('ABSPATH')) { exit; }

function zp_suite_300_mega_drawer_polish(){
  if (is_admin()) { return; }
  ?>
<style id="zp-suite-front-fixes-300-mega-drawer-polish">
@media (min-width:1101px){

  /* ===== Panel: flat, bez cienia, hairline na dole (wersja ciemna) ===== */
  html body .zpNewNav__mega--portal{
    box-shadow:none!important;
    border-radius:0 0 28px 28px!important;
    border-bottom:1px solid rgba(142,200,247,.16)!important;
  }

  /* ===== Pionowe hairline'y między kolumnami ===== */
  html body .zpNewNav__mega--portal .zpNewNav__megaCol + .zpNewNav__megaCol{
    border-left:1px solid rgba(255,255,255,.065)!important;
    padding-left:30px!important;
  }

  /* ===== Nagłówki kolumn: editorial micro-labels ===== */
  html body .zpNewNav__mega--portal .zpNewNav__megaHead{
    margin:0 0 22px!important;
    font-size:12.5px!important;
    font-weight:760!important;
    letter-spacing:.16em!important;
    text-transform:uppercase!important;
    line-height:1.3!important;
    color:rgba(255,255,255,.55)!important;
  }
  html body .zpNewNav__mega--portal .zpNewNav__megaHead::after{
    width:26px!important;
    height:2px!important;
    margin-top:10px!important;
    background:linear-gradient(90deg,#8ec8f7 0%,rgba(59,110,168,.0) 100%)!important;
    box-shadow:none!important;
  }

  /* ===== Linki: spokojniejszy hover, kafelek ikony bez rotacji ===== */
  html body .zpNewNav__mega--portal .zpNewNav__megaLink{
    opacity:1!important;
  }
  html body .zpNewNav__mega--portal .zpNewNav__megaLink:hover,
  html body .zpNewNav__mega--portal .zpNewNav__megaLink:focus-visible{
    transform:translateX(4px)!important;
    background:linear-gradient(100deg,rgba(142,200,247,.09),rgba(255,255,255,.035) 62%,transparent)!important;
  }
  html body .zpNewNav__mega--portal .zpNewNav__megaIco{
    width:40px!important;height:40px!important;border-radius:12px!important;
    background:transparent!important;
    border:1px solid rgba(255,255,255,.16)!important;
    box-shadow:none!important;
    transition:border-color .22s ease,color .22s ease!important;
  }
  html body .zpNewNav__mega--portal .zpNewNav__megaLink:hover .zpNewNav__megaIco{
    transform:none!important;
    border-color:rgba(142,200,247,.55)!important;
    color:#8ec8f7!important;
  }
  html body .zpNewNav__mega--portal .zpNewNav__megaTitle{
    font-size:17.5px!important;letter-spacing:-.032em!important;
  }
  html body .zpNewNav__mega--portal .zpNewNav__megaDesc{
    font-size:13.5px!important;
  }

  /* ===== Karta promo: plakietki out, zdjęcie oddycha, zero cieni ===== */
  html body .zpNewNav__mega--portal .zpNewNav__promoTop,
  html body .zpNewNav__mega--portal .zpNewNav__promoKicker,
  html body .zpNewNav__mega--portal .zpNewNav__promoBadge,
  html body #zpNewNav .zpNewNav__promoTop,
  html body #zpNewNav .zpNewNav__promoKicker,
  html body #zpNewNav .zpNewNav__promoBadge{
    display:none!important;visibility:hidden!important;
  }
  html body .zpNewNav__mega--portal .zpNewNav__promoCard{
    border-radius:22px!important;
    border:1px solid rgba(255,255,255,.15)!important;
    box-shadow:none!important;
  }
  html body .zpNewNav__mega--portal .zpNewNav__promoMedia::after{
    background:
      linear-gradient(180deg,rgba(4,10,20,.02) 0%,rgba(4,10,20,.30) 42%,rgba(2,6,13,.93) 100%),
      linear-gradient(115deg,rgba(4,10,20,.30) 0%,transparent 55%)!important;
  }
  html body .zpNewNav__mega--portal .zpNewNav__promoCard:hover{
    transform:translateY(-3px)!important;
    border-color:rgba(142,200,247,.5)!important;
    box-shadow:none!important;
  }
  html body .zpNewNav__mega--portal .zpNewNav__promoTitle{
    text-shadow:none!important;
  }
  html body .zpNewNav__mega--portal .zpNewNav__promoText{
    text-shadow:none!important;
  }
  html body .zpNewNav__mega--portal .zpNewNav__promoBtn{
    box-shadow:none!important;
  }

  /* ===== Dolna belka: przezroczysta, sam hairline ===== */
  html body .zpNewNav__mega--portal .zpNewNav__megaBottom{
    background:transparent!important;
    border-top:1px solid rgba(255,255,255,.08)!important;
  }
  html body .zpNewNav__mega--portal .zpNewNav__megaClaim{
    font-size:26px!important;letter-spacing:-.045em!important;
  }

  /* ============================================================
     WERSJA JASNA (.is-light-menu)
     ============================================================ */
  html body .zpNewNav__mega--portal.is-light-menu{
    background:#ffffff!important;
    box-shadow:none!important;
    border-bottom:1px solid rgba(7,20,38,.12)!important;
  }
  html body .zpNewNav__mega--portal.is-light-menu .zpNewNav__megaCol + .zpNewNav__megaCol{
    border-left:1px solid rgba(7,20,38,.08)!important;
  }
  html body .zpNewNav__mega--portal.is-light-menu .zpNewNav__megaHead{
    color:rgba(7,20,38,.55)!important;
  }
  html body .zpNewNav__mega--portal.is-light-menu .zpNewNav__megaHead::after{
    background:linear-gradient(90deg,#1c477a 0%,rgba(59,110,168,0) 100%)!important;
    box-shadow:none!important;
  }
  html body .zpNewNav__mega--portal.is-light-menu .zpNewNav__megaLink:hover,
  html body .zpNewNav__mega--portal.is-light-menu .zpNewNav__megaLink:focus-visible{
    background:rgba(7,17,31,.045)!important;
  }
  html body .zpNewNav__mega--portal.is-light-menu .zpNewNav__megaIco{
    background:#f6f8fb!important;
    border:1px solid rgba(7,20,38,.10)!important;
    box-shadow:none!important;
    color:#1c477a!important;
  }
  html body .zpNewNav__mega--portal.is-light-menu .zpNewNav__megaLink:hover .zpNewNav__megaIco{
    border-color:rgba(28,71,122,.45)!important;
    color:#1c477a!important;
  }
  html body .zpNewNav__mega--portal.is-light-menu .zpNewNav__promoCard{
    border:1px solid rgba(7,20,38,.14)!important;
    box-shadow:none!important;
  }
  html body .zpNewNav__mega--portal.is-light-menu .zpNewNav__promoCard:hover{
    border-color:rgba(28,71,122,.5)!important;
    box-shadow:none!important;
  }
  html body .zpNewNav__mega--portal.is-light-menu .zpNewNav__megaBottom{
    background:transparent!important;
    border-top:1px solid rgba(7,20,38,.08)!important;
  }
}

/* ============================================================
   DRAWER MOBILNY — flat panel + stagger wejścia
   ============================================================ */
@media (max-width:1440px){
  html body #zpNewNav .zpNewNav__drawerPanel{
    box-shadow:none!important;
    border-left:1px solid rgba(7,20,38,.08)!important;
  }
  html body #zpNewNav .zpNewNav__drawerTop{
    padding:20px 24px 14px!important;
  }
  html body #zpNewNav .zpNewNav__close{
    width:44px!important;height:44px!important;
    border:1px solid rgba(7,20,38,.10)!important;
    box-shadow:none!important;
    transition:border-color .18s ease,background .18s ease!important;
  }
  html body #zpNewNav .zpNewNav__close:active{
    background:#f4f7fb!important;border-color:rgba(28,71,122,.35)!important;
  }

  /* Pozycje menu: spokojna typografia + wyraźne stany dotyku. */
  html body #zpNewNav .zpNewNav__mSummary,
  html body #zpNewNav .zpNewNav__mLink{
    gap:15px!important;padding:19px 0!important;
    font-size:19px!important;letter-spacing:-.024em!important;
    transition:opacity .16s ease!important;
  }
  html body #zpNewNav .zpNewNav__mSummary:active,
  html body #zpNewNav .zpNewNav__mLink:active{opacity:.62!important;}
  html body #zpNewNav .zpNewNav__mSummary:focus-visible,
  html body #zpNewNav .zpNewNav__mLink:focus-visible{
    outline:2px solid rgba(28,71,122,.5)!important;outline-offset:3px!important;border-radius:10px!important;
  }
  html body #zpNewNav .zpNewNav__mIcon svg{width:22px!important;height:22px!important;stroke-width:1.6!important;}
  html body #zpNewNav .zpNewNav__mArrow{color:rgba(7,20,38,.34)!important;}
  html body #zpNewNav .zpNewNav__mArrow svg{width:18px!important;height:18px!important;}

  /* Karta CTA: hairline zamiast poświaty, bez cieni. */
  html body #zpNewNav .zpNewNav__drawerContact{
    border-radius:24px!important;
    border:1px solid rgba(255,255,255,.10)!important;
    box-shadow:none!important;
  }
  html body #zpNewNav .zpNewNav__drawerContact small{
    letter-spacing:.18em!important;
  }
  html body #zpNewNav .zpNewNav__drawerContactCta{
    box-shadow:none!important;
    transition:transform .18s ease!important;
  }
  html body #zpNewNav .zpNewNav__drawerContact:active .zpNewNav__drawerContactCta{
    transform:scale(.97)!important;
  }

  /* Stopka: pigułki kontaktu z hairline. */
  html body #zpNewNav .zpNewNav__drawerFoot{
    border-top:1px solid rgba(7,20,38,.07)!important;
    padding:16px 24px calc(20px + env(safe-area-inset-bottom,0px))!important;
  }
  html body #zpNewNav .zpNewNav__drawerFoot a{
    height:54px!important;
    border:1px solid rgba(7,20,38,.10)!important;
    box-shadow:none!important;
    transition:background .18s ease,border-color .18s ease!important;
  }
  html body #zpNewNav .zpNewNav__drawerFoot a:active{
    background:#f4f7fb!important;border-color:rgba(28,71,122,.35)!important;
  }

  /* Stagger wejścia pozycji przy otwarciu — lekki (opacity + 10px), tylko transform/opacity. */
  html body #zpNewNav .zpNewNav__drawer.is-open .zpNewNav__mNav > *,
  html body #zpNewNav .zpNewNav__drawer.is-open .zpNewNav__drawerContact,
  html body #zpNewNav .zpNewNav__drawer.is-open .zpNewNav__drawerFoot{
    animation:zpDrawerItemIn300 .34s cubic-bezier(.16,1,.3,1) both;
  }
  html body #zpNewNav .zpNewNav__drawer.is-open .zpNewNav__mNav > *:nth-child(1){animation-delay:.05s}
  html body #zpNewNav .zpNewNav__drawer.is-open .zpNewNav__mNav > *:nth-child(2){animation-delay:.08s}
  html body #zpNewNav .zpNewNav__drawer.is-open .zpNewNav__mNav > *:nth-child(3){animation-delay:.11s}
  html body #zpNewNav .zpNewNav__drawer.is-open .zpNewNav__mNav > *:nth-child(4){animation-delay:.14s}
  html body #zpNewNav .zpNewNav__drawer.is-open .zpNewNav__mNav > *:nth-child(5){animation-delay:.17s}
  html body #zpNewNav .zpNewNav__drawer.is-open .zpNewNav__mNav > *:nth-child(6){animation-delay:.20s}
  html body #zpNewNav .zpNewNav__drawer.is-open .zpNewNav__drawerContact{animation-delay:.24s}
  html body #zpNewNav .zpNewNav__drawer.is-open .zpNewNav__drawerFoot{animation-delay:.28s}
}

@keyframes zpDrawerItemIn300{
  from{opacity:0;transform:translate3d(0,10px,0)}
  to{opacity:1;transform:translate3d(0,0,0)}
}

@media (prefers-reduced-motion:reduce){
  html body #zpNewNav .zpNewNav__drawer.is-open .zpNewNav__mNav > *,
  html body #zpNewNav .zpNewNav__drawer.is-open .zpNewNav__drawerContact,
  html body #zpNewNav .zpNewNav__drawer.is-open .zpNewNav__drawerFoot{
    animation:none!important;opacity:1!important;transform:none!important;
  }
}
</style>
  <?php
}
add_action('wp_footer','zp_suite_300_mega_drawer_polish',PHP_INT_MAX);
