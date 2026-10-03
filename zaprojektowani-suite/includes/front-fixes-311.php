<?php
/**
 * ZP Suite v2.2.791–793 — Home hero: wejście (mobile + desktop), sygnet ZP za robotem, zespół nad kickerem,
 * gradient zmieniający kierunek, natychmiastowe chowanie bannera zgód.
 *
 * v2.2.795
 * - Cookiebot znów ładowany oryginalnym tagiem (async w <head>, tryb manualny, data-culture z URL — moduł językowy).
 *   Odroczony loader z 2.2.793 przejmował surowy tag wtyczki Cookiebot ZANIM moduł językowy przełączył go w tryb
 *   manualny, więc na produkcji Cookiebot działał w trybie auto-blocking, bez języka z URL, i startował dopiero po
 *   zdarzeniu load: jego ramki (i dla osób ze zgodą GTM/piksele) ładowały się po load — pasek ładowania wracał
 *   i główny wątek był zajęty akurat wtedy, gdy ktoś stukał w hamburger.
 * - Gradient zmieniający kierunek: skrypt reaguje tylko na zmiany klas wejścia hero (nie na każdą zmianę klasy <html>,
 *   np. zpNewNav-lock przy otwieraniu menu — wcześniej wymuszało to przeliczenie układu w trakcie animacji szuflady)
 *   i tylko na zmianę szerokości okna (nie na chowanie paska adresu przy przewijaniu). Na mobile kopia gradientu
 *   powstaje dopiero po zdarzeniu load.
 * - Desktop: lewa kolumna (zespół, kicker, H1, cała treść) tuż pod nagłówkiem — przesunięta w górę o nadmiar odstępu;
 *   robot, opinie i wysokość sekcji bez zmian. Naprawiony nadmiarowy nawias w CSS z 2.2.794 (kasował kolejny blok
 *   @media z opóźnieniami kaskady wejścia na desktopie).
 * - Mobile: robot (i sygnet za nim) 40 px wyżej. Kicker i H1 bez animacji wejścia (widoczne w pierwszej klatce), wejście
 *   0,45 s tylko dla zespołu, „Projektujemy…”, opisu, CTA i opinii.
 * - Hamburger (i „zamknij” w szufladzie) widoczne od razu — zapasowa ikona w CSS do czasu, aż JS narysuje ikony lucide.
 * v2.2.794
 * - Mobile: wejście hero w czystym CSS (animacje startują przy parsowaniu, bez JS i bez czekania na główny wątek), więc
 *   zajęty wątek przy parsowaniu 1,5 MB HTML nie zatrzymuje już hero w półprzezroczystym stanie. Robot: samo opacity.
 * - Zespół: sylwetki niżej, wychodzą zza kickera (maska u dołu); desktop +20 % (187 px), mobile 122 px.
 * v2.2.793
 * - Obrazy poniżej hero: loading="lazy" (ok. 400 KB mniej przed load na telefonie); loader Cookiebota (uc.js + 390 KB cc.js)
 *   startował po zdarzeniu load (maks. 3,5 s) (wycofane w 2.2.795, patrz wyżej).
 * - Mobile ma ładować się „od kopa”: hero odsłania się zaraz po sparsowaniu własnego HTML (skrypt na końcu hero),
 *   czeka już tylko na font tytułu (maks. 0,5 s; font jest preloadowany), nie czeka na arkusze CSS.
 * - Wejście na mobile: tylko opacity + translate (bez scale, bez filtrów na animowanych warstwach), ~0,9 s.
 * - Sygnet: własne, małe pliki (720 px mobile ≈ 18 KB, 1200 px desktop ≈ 31 KB) zamiast 2560 px; bez filter:drop-shadow
 *   (poświata to statyczny radial-gradient w ::after); unoszenie tylko na desktopie.
 * - Zespół: trzy wycięte sylwetki (PNG → WebP ≈ 24 KB każda) obok siebie, nałożone na siebie, nad kickerem i H1;
 *   dół wtopiony maską. Wysokość 140 px (desktop) / 100 px (mobile), więc hero rośnie tylko o ok. 120 / 70 px.
 * - Odblask CTA, drganie strzałek i „skan” chipów tylko na desktopie z myszą ((hover:hover) and (pointer:fine)).
 *
 * Skrypty inline: bez znaku „mniejsze niż” i bez dosłownej nazwy atrybutu przełącznika języka.
 */
if (!defined('ABSPATH')) { exit; }

if (!function_exists('zp_suite_311_signet_url')) {
  function zp_suite_311_signet_url() {
    return apply_filters('zp_suite_311_signet_url', ZP_SUITE_URL . 'assets/img/hero-signet/zp-signet-1200.webp');
  }
}
if (!function_exists('zp_suite_311_signet_url_mobile')) {
  function zp_suite_311_signet_url_mobile() {
    return apply_filters('zp_suite_311_signet_url_mobile', ZP_SUITE_URL . 'assets/img/hero-signet/zp-signet-720.webp');
  }
}

if (!function_exists('zp_suite_311_head_boot')) {
  /** Jak najwcześniej w <head>: stan początkowy wejścia (przed pierwszym renderem) + szybkie chowanie bannera zgód. */
  function zp_suite_311_head_boot() {
    if (!function_exists('zp_suite_308_is_home_like') || !zp_suite_308_is_home_like()) { return; }
    ?>
<script id="zp-suite-311-hero-boot-js">(function(d,w){'use strict';
var h=d.documentElement,done=false,armed=false,MOB=false;
function release(){try{if(w.ZPHeroIntroGate)w.ZPHeroIntroGate.release('hero-complete');}catch(e){}}
function fontReady(cb){var n=0,fin=function(){if(!n){n=1;cb();}};
  try{var t=d.querySelector('#zhHero .zh__title');if(!t||!d.fonts||!d.fonts.load){fin();return;}
    var cs=w.getComputedStyle(t);d.fonts.load(cs.fontWeight+' '+cs.fontSize+' '+cs.fontFamily,t.textContent||'a').then(fin,fin);w.setTimeout(fin,500);}catch(e){fin();}}
function go(){if(done)return;done=true;
  if(MOB){w.setTimeout(release,900);return;}
  h.classList.add('zp-hero-early');h.classList.add('zp-hero-in');h.classList.remove('zp-hero-pre');
  w.setTimeout(release,1200);
  w.setTimeout(function(){h.classList.remove('zp-hero-in');},2400);}
function arm(){if(armed||done)return;armed=true;fontReady(function(){w.requestAnimationFrame(function(){w.requestAnimationFrame(go);});});}
w.__zpHeroEnd=function(){if(MOB)go();};
try{
  if(w.top!==w.self||!w.matchMedia)return;
  if(w.matchMedia('(prefers-reduced-motion:reduce)').matches)return;
  MOB=w.matchMedia('(max-width:1100px)').matches;
  if(!MOB)h.classList.add('zp-hero-pre');
  d.addEventListener('DOMContentLoaded',arm);
  w.setTimeout(go,10000);
  w.addEventListener('pageshow',function(e){if(e&&e.persisted){h.classList.remove('zp-hero-pre');h.classList.remove('zp-hero-in');}});
}catch(e){h.classList.remove('zp-hero-pre');}
})(document,window);</script>
<script id="zp-suite-311-consent-fast-js">(function(d,w){'use strict';
var h=d.documentElement,bypass=false,safety=0,IDS=['CybotCookiebotDialogBodyLevelButtonLevelOptinAllowAll','CybotCookiebotDialogBodyLevelButtonLevelOptinDeclineAll','CybotCookiebotDialogBodyLevelButtonLevelOptinAllowallSelection'];
function shownAgain(){var b=d.getElementById('zp-cookiebot-banner')||d.getElementById('CybotCookiebotDialog');if(!b)return false;return w.getComputedStyle(b).display!=='none';}
function reopen(){h.classList.remove('zp-cb-closing');w.clearTimeout(safety);}
d.addEventListener('click',function(e){
  if(bypass)return;
  var btn=(e.target&&e.target.closest)?e.target.closest('button'):null;
  if(!btn||IDS.indexOf(btn.id)===-1)return;
  e.preventDefault();e.stopImmediatePropagation();
  h.classList.add('zp-cb-closing');
  w.requestAnimationFrame(function(){w.requestAnimationFrame(function(){w.setTimeout(function(){bypass=true;try{btn.click();}catch(x){}bypass=false;},0);});});
  w.clearTimeout(safety);safety=w.setTimeout(function(){if(shownAgain())reopen();},9000);
},true);
w.addEventListener('CookiebotOnDialogDisplay',function(){if(!h.classList.contains('zp-cb-closing'))return;w.setTimeout(function(){if(shownAgain())reopen();},50);});
d.addEventListener('click',function(e){var s=(e.target&&e.target.closest)?e.target.closest('[data-zp-cookie-settings]'):null;if(s)reopen();},true);
})(document,window);</script>
    <?php
  }
}
add_action('wp_head', 'zp_suite_311_head_boot', -90);

if (!function_exists('zp_suite_311_css')) {
  function zp_suite_311_css() {
    if (!function_exists('zp_suite_308_is_home_like') || !zp_suite_308_is_home_like()) { return; }
    $sig_d = esc_url(zp_suite_311_signet_url());
    $sig_m = esc_url(zp_suite_311_signet_url_mobile());
    ?>
<style id="zp-suite-311-hero">
/* ── Case study brandingu w sekcji usług: obraz z atrybutu srcset (lazy) zamiast content:url() z CSS (180 KB pobierane od razu) ── */
html body #zpShowcaseServices .zpSSCard--brand .zpSSCard__mock--brandStationery img:not(#zpx){content:normal!important}

/* ── Hamburger i „zamknij” widoczne od razu: ikony lucide rysuje JS dopiero przy DOMContentLoaded (biblioteka z defer na 82 % HTML),
   do tego czasu przycisk był pusty. Zapasowa ikona w CSS znika sama, gdy JS podmieni pusty <i> na <svg>. ── */
html body #zpNewNav .zpNewNav__burger>i[data-lucide="menu"]:empty{display:block;width:20px!important;height:20px!important;background:linear-gradient(currentColor,currentColor) 50% 17%/72% 2px no-repeat,linear-gradient(currentColor,currentColor) 50% 50%/72% 2px no-repeat,linear-gradient(currentColor,currentColor) 50% 83%/72% 2px no-repeat}
html body #zpNewNav .zpNewNav__close>i[data-lucide="x"]:empty{display:block;width:20px!important;height:20px!important;background:linear-gradient(45deg,transparent calc(50% - 1px),currentColor 0,currentColor calc(50% + 1px),transparent 0) 50% 50%/64% 64% no-repeat,linear-gradient(-45deg,transparent calc(50% - 1px),currentColor 0,currentColor calc(50% + 1px),transparent 0) 50% 50%/64% 64% no-repeat}

/* ── Banner zgód: chowany natychmiast po kliknięciu (Cookiebot kończy w tle) ── */
html.zp-cb-closing #zp-cookiebot-banner,html.zp-cb-closing #CybotCookiebotDialog,html.zp-cb-closing #CybotCookiebotDialogBodyUnderlay{opacity:0!important;visibility:hidden!important;pointer-events:none!important;transition:opacity .16s ease,visibility 0s linear .16s!important}

/* ── Hero widoczne od razu po wczytaniu samego hero (mobile) — nadpisuje ukrywanie do DOMContentLoaded ── */
html.zp-hero-early body .zpHomeContent>#zhHero:not(#zpx){opacity:1!important;transform:none!important;visibility:visible!important;transition:none!important}
@media (max-width:1100px){html body .zpHomeContent>#zhHero:not(#zpx){opacity:1!important;transform:none!important;visibility:visible!important;transition:none!important}}

/* ── Sygnet ZP za robotem: obraz w ::before, statyczna poświata w ::after (bez filtrów) ── */
html body #zhHero .zh__stage::before{content:"";position:absolute;z-index:0;pointer-events:none;left:50%;top:50%;width:min(106%,1120px);aspect-ratio:1200/938;transform:translate(-56%,-40%);background:url("<?php echo $sig_d; ?>") center/contain no-repeat;opacity:0;-webkit-mask-image:radial-gradient(ellipse 66% 64% at 50% 46%,#000 42%,rgba(0,0,0,.6) 66%,transparent 100%);mask-image:radial-gradient(ellipse 66% 64% at 50% 46%,#000 42%,rgba(0,0,0,.6) 66%,transparent 100%);transition:opacity 1.4s cubic-bezier(.22,1,.36,1) .1s}
html body #zhHero .zh__stage::after{content:"";position:absolute;z-index:0;pointer-events:none;left:50%;top:50%;width:min(84%,900px);aspect-ratio:1;transform:translate(-56%,-44%);background:radial-gradient(closest-side,rgba(142,200,247,.17),rgba(142,200,247,.06) 46%,rgba(142,200,247,0) 72%);opacity:0;transition:opacity 1.6s ease .1s}
html.zp-ready body #zhHero .zh__stage::before,html.zp-hero-early body #zhHero .zh__stage::before{opacity:.24}
html.zp-ready body #zhHero .zh__stage::after,html.zp-hero-early body #zhHero .zh__stage::after{opacity:1}
@media (min-width:1101px) and (prefers-reduced-motion:no-preference){
  html.zp-ready body #zhHero .zh__stage::before{animation:zpSigFloat 9s ease-in-out 2.2s infinite alternate}
}
@keyframes zpSigFloat{from{translate:0 0}to{translate:0 -16px}}
@media (max-width:1100px){
  html body #zhHero .zh__stage::before{width:540px;top:calc(56% - 40px);transform:translate(-48%,-50%);background-image:url("<?php echo $sig_m; ?>");transition-duration:.9s}
  html body #zhHero .zh__stage::after{width:520px;top:calc(56% - 40px);transform:translate(-48%,-50%)}
  html.zp-ready body #zhHero .zh__stage::before,html.zp-hero-early body #zhHero .zh__stage::before{opacity:.5}
  /* robot 40 px wyżej (cały plakat; obraz nie dotyka krawędzi pola, więc nic się nie przycina) */
  html body #zhHero .zh__poster:not(#zpx){translate:0 -40px}
}
/* ── Desktop: lewa kolumna (zespół, kicker, H1, treść) tuż pod nagłówkiem (88 px + 14 px). Przesunięcie wizualne,
   więc robot i karta opinii (liczone od połowy wysokości hero) oraz wysokość sekcji zostają bez zmian. ── */
@media (min-width:1101px){
  html body #zhHero .zh__copy:not(#zpx){translate:0 min(0px,calc(102px - var(--zpHeaderH,89px) - var(--zhHeaderGap,80px)))}
}
@media (prefers-reduced-motion:reduce){html body #zhHero .zh__stage::before,html body #zhHero .zh__stage::after{transition:none}}

/* ── Zespół nad kickerem: trzy sylwetki obok siebie, nałożone, dół wtopiony ── */
html body #zhHero .zh__crew:not(#zpx){position:relative;z-index:8;display:flex;align-items:flex-end;height:187px;margin:0 0 -36px -8px;padding:0;pointer-events:none;-webkit-mask-image:linear-gradient(#000 56%,rgba(0,0,0,.55) 80%,rgba(0,0,0,0) 97%);mask-image:linear-gradient(#000 56%,rgba(0,0,0,.55) 80%,rgba(0,0,0,0) 97%)}
html body #zhHero .zh__eb:not(#zpx){position:relative;z-index:9}
html body #zhHero .zh__crewImg:not(#zpx){display:block;position:relative;height:100%;width:auto;max-width:none;flex:0 0 auto;margin:0;padding:0;border:0;background:transparent;object-fit:contain;transform-origin:50% 100%}
html body #zhHero .zh__crewImg--2:not(#zpx){z-index:2;margin:0 -30px;transform:scale(.94)}
html body #zhHero .zh__crewImg--3:not(#zpx){z-index:1}
@media (max-width:1100px){
  html body #zhHero .zh__crew:not(#zpx){height:122px;margin:-4px 0 -30px -4px}
  html body #zhHero .zh__crewImg--2:not(#zpx){margin:0 -24px}
}

/* ── Stan końcowy na mobile (bez starych animacji wejścia z szablonu) ── */
@media (max-width:1100px){
  /* kicker i H1 bez żadnego wejścia: widoczne w pierwszej klatce, w której przeglądarka maluje hero */
  html body #zhHero :is(.zh__eb,.zh__title):not(#zpx){animation:none!important;opacity:1!important;transform:none!important;filter:none!important;visibility:visible!important}
  html body #zhHero :is(.zh__svcRot,.zh__lead):not(#zpx){filter:none!important;visibility:visible!important}
  html body #zhHero :is(.zh__actions,.zh__chips,.zh__side):not(#zpx){animation:none!important;opacity:1!important;transform:none!important;filter:none!important;visibility:visible!important}
  html body #zhHero :is(.zh__crew,.zh__crewImg,.zh__actions>*,.zh__side>*):not(#zpx){filter:none!important}
}
/* ── Mobile: wejście w czystym CSS — animacje startują w chwili sparsowania elementu (bez JS, bez czekania na główny wątek),
   tylko opacity + transform (GPU). Robot: samo opacity. ── */
@media (max-width:1100px) and (prefers-reduced-motion:no-preference){
  html body #zhHero .zh__stage:not(#zpx){animation:zpMStage .6s ease-out both!important}
  html body #zhHero :is(.zh__svcRot,.zh__lead):not(#zpx){animation:zpMIn .45s cubic-bezier(.22,1,.36,1) var(--zd,0s) both!important}
  html body #zhHero :is(.zh__actions,.zh__side)>*:not(#zpx),html body #zhHero .zh__crewImg:not(#zpx){animation:zpMIn .45s cubic-bezier(.22,1,.36,1) var(--zd,0s) both}
  html body #zhHero .zh__crewImg--1:not(#zpx){--zd:0s}
  html body #zhHero .zh__crewImg--2:not(#zpx){--zd:.05s}
  html body #zhHero .zh__crewImg--3:not(#zpx){--zd:.1s}
  html body #zhHero .zh__svcRot:not(#zpx){--zd:.06s}
  html body #zhHero .zh__lead:not(#zpx){--zd:.12s}
  html body #zhHero .zh__actions>:nth-child(1):not(#zpx){--zd:.18s}
  html body #zhHero .zh__actions>:nth-child(2):not(#zpx){--zd:.22s}
  html body #zhHero .zh__side>*:not(#zpx){--zd:.36s}
  html body #zhHero .zh__side>:nth-child(1):not(#zpx){--zd:.3s}
}
@media (max-width:1100px) and (prefers-reduced-motion:reduce){
  html body #zhHero :is(.zh__svcRot,.zh__lead,.zh__stage,.zh__crewImg):not(#zpx),html body #zhHero :is(.zh__actions,.zh__side)>*:not(#zpx){animation:none!important;opacity:1!important;transform:none!important}
  html body #zhHero .zh__stage:not(#zpx){opacity:.66!important}
}
@keyframes zpMIn{from{opacity:0;transform:translate3d(0,10px,0)}to{opacity:1;transform:none}}
@keyframes zpMStage{from{opacity:0}}

/* ── Stan początkowy (html.zp-hero-pre, z <head> przed pierwszym renderem) + przejścia wejścia (html.zp-hero-in) ── */
@media (min-width:1101px) and (prefers-reduced-motion:no-preference){
  html.zp-hero-pre body #zhHero .zh__crewImg:not(#zpx){opacity:0!important;translate:0 16px!important}
  html.zp-hero-pre body #zhHero .zh__lead:not(#zpx){opacity:0!important;translate:0 14px!important}
  html.zp-hero-pre body #zhHero :is(.zh__actions,.zh__chips,.zh__side)>*:not(#zpx){opacity:0!important;translate:0 14px!important;scale:.96!important}
  html.zp-hero-in body #zhHero .zh__crewImg:not(#zpx){transition:opacity .55s cubic-bezier(.22,1,.36,1) var(--zd,0s),translate .75s cubic-bezier(.22,1,.36,1) var(--zd,0s)!important}
  html.zp-hero-in body #zhHero .zh__lead:not(#zpx){transition:opacity .6s cubic-bezier(.22,1,.36,1) var(--zd,0s),translate .7s cubic-bezier(.22,1,.36,1) var(--zd,0s)!important}
  html.zp-hero-in body #zhHero :is(.zh__actions,.zh__chips,.zh__side)>*:not(#zpx){transition:opacity .5s cubic-bezier(.22,1,.36,1) var(--zd,0s),translate .8s cubic-bezier(.34,1.3,.64,1) var(--zd,0s),scale .8s cubic-bezier(.34,1.3,.64,1) var(--zd,0s)!important}
  html body #zhHero .zh__crewImg--1:not(#zpx){--zd:.02s}
  html body #zhHero .zh__crewImg--2:not(#zpx){--zd:.09s}
  html body #zhHero .zh__crewImg--3:not(#zpx){--zd:.16s}
}
@media (min-width:1101px) and (prefers-reduced-motion:no-preference){
  html body #zhHero .zh__lead:not(#zpx){--zd:.3s}
  html body #zhHero .zh__actions>:nth-child(1):not(#zpx){--zd:.5s}
  html body #zhHero .zh__actions>:nth-child(2):not(#zpx){--zd:.58s}
  html body #zhHero .zh__chips>*:not(#zpx){--zd:.94s}
  html body #zhHero .zh__chips>:nth-child(1):not(#zpx){--zd:.7s}
  html body #zhHero .zh__chips>:nth-child(2):not(#zpx){--zd:.76s}
  html body #zhHero .zh__chips>:nth-child(3):not(#zpx){--zd:.82s}
  html body #zhHero .zh__chips>:nth-child(4):not(#zpx){--zd:.88s}
  html body #zhHero .zh__side>*:not(#zpx){--zd:1.1s}
  html body #zhHero .zh__side>:nth-child(1):not(#zpx){--zd:.9s}
  html body #zhHero .zh__side>:nth-child(2):not(#zpx){--zd:1s}
}
/* ── Desktop z myszą: CTA przyciąga uwagę — odblask co kilka sekund + ruch strzałki; chipy: kolejny „skan” światła ── */
@media (min-width:1101px) and (hover:hover) and (pointer:fine) and (prefers-reduced-motion:no-preference){
  html body #zhHero .zh__actions .zh__btn--p{overflow:hidden;isolation:isolate}
  html body #zhHero .zh__actions .zh__btn--p::before{content:"";position:absolute;z-index:1;top:-30%;bottom:-30%;left:0;width:42%;pointer-events:none;background:linear-gradient(100deg,rgba(142,200,247,0) 0%,rgba(142,200,247,.6) 50%,rgba(142,200,247,0) 100%);translate:-170% 0;rotate:14deg;animation:zpCtaShine 5.6s cubic-bezier(.45,0,.2,1) 2.4s infinite}
  html body #zhHero .zh__actions .zh__btn svg{animation:zpCtaArrow 5.6s cubic-bezier(.34,1.56,.64,1) 2.4s infinite}
  html body #zhHero .zh__actions .zh__btn--g svg{animation-delay:3.1s}
  html body #zhHero .zh__actions .zh__btn:active{scale:.97}
  html body #zhHero .zh__chip::after{content:"";position:absolute;inset:0;pointer-events:none;background:linear-gradient(100deg,rgba(142,200,247,0) 15%,rgba(142,200,247,.26) 50%,rgba(142,200,247,0) 85%);translate:-120% 0;animation:zpChipScan 9s cubic-bezier(.45,0,.2,1) infinite;animation-delay:calc(3s + var(--ci,0) * .16s)}
  html body #zhHero .zh__chip:nth-child(2){--ci:1}
  html body #zhHero .zh__chip:nth-child(3){--ci:2}
  html body #zhHero .zh__chip:nth-child(4){--ci:3}
  html body #zhHero .zh__chip b{transition:color .24s ease,text-shadow .24s ease}
  html body #zhHero .zh__chip:hover b{color:#fff;text-shadow:0 0 12px rgba(142,200,247,.8)}
}
@keyframes zpCtaShine{0%{translate:-170% 0}20%,100%{translate:340% 0}}
@keyframes zpCtaArrow{0%,6%,20%,100%{translate:0 0}12%{translate:3px -3px}}
@keyframes zpChipScan{0%{translate:-120% 0}11%,100%{translate:120% 0}}
html body #zhHero.zp-hero-off .zh__stage::before,html body #zhHero.zp-hero-off .zh__actions .zh__btn--p::before,html body #zhHero.zp-hero-off .zh__actions .zh__btn svg,html body #zhHero.zp-hero-off .zh__chip::after,html body #zhHero.zp-hero-off .zh__gflip{animation-play-state:paused!important}

/* ── Gradient zmieniający kierunek: kopia z odwróconym położeniem gradientu, animowana tylko opacity ── */
html body #zhHero .zh__gflip:not(#zpx){position:absolute!important;left:0;top:0;margin:0!important;padding:0!important;border:0!important;pointer-events:none!important;opacity:0;animation:zpGFlip 6.5s cubic-bezier(.45,0,.55,1) 1.2s infinite alternate both}
html body #zhHero .zh__gflip.is-paused:not(#zpx){animation-play-state:paused}
html body #zhHero .zh__gflip>.zh__title:not(#zpx){margin:0!important;opacity:1!important;transform:none!important;translate:none!important;scale:none!important;filter:none!important;animation:none!important;transition:none!important;visibility:hidden!important;text-shadow:none!important}
html body #zhHero .zh__gflip .zh__title .zh__grad:not(#zpx){visibility:visible!important;background-position:100% 50%!important;animation:none!important;transition:none!important}
@media (max-width:1100px){html body #zhHero .zh__gflip .zh__title .zh__grad:not(#zpx){background-position:0% 50%!important}}
@keyframes zpGFlip{0%,14%{opacity:0}86%,100%{opacity:1}}
@media (prefers-reduced-motion:reduce){
  html body #zhHero .zh__gflip:not(#zpx){display:none!important}
}
</style>
<script id="zp-suite-311-hero-gflip-js">(function(d,w){'use strict';
var h=d.documentElement,t=null,hero=null,gh=null,inner=null,tries=0,rt=0,io=null,io2=null,mo=null,mh=null,pending=0,lw=0,MOB=false,loaded=false;
function hasC(s,c){return (' '+(s||'')+' ').indexOf(' '+c+' ')>-1;}
function busy(){
  if(h.classList.contains('zp-hero-pre')||h.classList.contains('zp-hero-in'))return true;
  if(t.classList.contains('zh__title--wait')||t.classList.contains('zh__title--letters'))return true;
  try{var a=t.getAnimations?t.getAnimations():[],i,ct;for(i=0;a.length>i;i++){if(a[i].playState!=='running'||!a[i].effect||!a[i].effect.getComputedTiming)continue;ct=a[i].effect.getComputedTiming();if(isFinite(ct.endTime))return true;}}catch(e){}
  return false;}
function place(){if(!gh)return;var zi=w.getComputedStyle(t).zIndex;gh.style.zIndex=(zi==='auto'||!zi)?'1':String((parseInt(zi,10)||0)+1);
  var a=t.getBoundingClientRect();gh.style.width=a.width.toFixed(2)+'px';gh.style.left='0px';gh.style.top='0px';
  var b=inner.getBoundingClientRect();gh.style.left=(a.left-b.left).toFixed(2)+'px';gh.style.top=(a.top-b.top).toFixed(2)+'px';}
function sync(){if(!gh)return;if(busy()){gh.style.display='none';return;}gh.style.display='';if(inner.innerHTML!==t.innerHTML)inner.innerHTML=t.innerHTML;place();}
function build(){if(gh||!t.querySelector('.zh__grad'))return;
  gh=d.createElement('div');gh.className='zh__gflip';gh.setAttribute('aria-hidden','true');gh.setAttribute('data-zpl-skip','');
  inner=d.createElement('div');inner.className='zh__title zh__title--ghost';inner.innerHTML=t.innerHTML;gh.appendChild(inner);
  t.parentNode.insertBefore(gh,t.nextSibling);place();
  if(w.IntersectionObserver){io=new IntersectionObserver(function(es){gh.classList.toggle('is-paused',!es[es.length-1].isIntersecting);});io.observe(t);}}
/* Tylko zmiany klas wejścia hero (desktop) — nie każda zmiana klasy html (np. zpNewNav-lock przy otwieraniu menu). */
function onHtml(rs){var i,o,n=h.className;for(i=0;rs.length>i;i++){o=rs[i].oldValue;if(hasC(o,'zp-hero-pre')!==hasC(n,'zp-hero-pre')||hasC(o,'zp-hero-in')!==hasC(n,'zp-hero-in')){later();return;}}}
function tick(){pending=0;
  if(!t){t=d.querySelector('#zhHero .zh__title');hero=d.getElementById('zhHero');if(!t)return;
    if(w.MutationObserver){mo=new MutationObserver(later);mo.observe(t,{attributes:true,attributeFilter:['class'],childList:true,characterData:true,subtree:true});
      if(!MOB){mh=new MutationObserver(onHtml);mh.observe(h,{attributes:true,attributeFilter:['class'],attributeOldValue:true});}}
    if(hero&&w.IntersectionObserver){io2=new IntersectionObserver(function(es){hero.classList.toggle('zp-hero-off',!es[es.length-1].isIntersecting);});io2.observe(hero);}}
  if(gh){sync();return;}
  if(MOB&&!loaded)return;
  if(d.readyState==='loading'||busy()){if(160>tries){tries++;pending=w.setTimeout(tick,250);}return;}
  build();}
function later(){if(!pending)pending=w.setTimeout(tick,60);}
try{
  if(w.top!==w.self)return;
  if(w.matchMedia&&w.matchMedia('(prefers-reduced-motion:reduce)').matches)return;
  MOB=!!(w.matchMedia&&w.matchMedia('(max-width:1100px)').matches);lw=w.innerWidth;
  d.addEventListener('DOMContentLoaded',later);d.addEventListener('zpl:change',later);
  w.addEventListener('resize',function(){if(w.innerWidth===lw)return;lw=w.innerWidth;w.clearTimeout(rt);rt=w.setTimeout(later,150);});
  if(d.fonts&&d.fonts.addEventListener)d.fonts.addEventListener('loadingdone',later);
  w.addEventListener('load',function(){if(MOB){w.setTimeout(function(){loaded=true;later();},900);}else{later();}});
}catch(e){}
})(document,window);</script>
    <?php
  }
}
add_action('wp_head', 'zp_suite_311_css', PHP_INT_MAX);

if (!function_exists('zp_suite_311_hero_markup')) {
  /**
   * Na wyjściu shortcode'u hero: sylwetki zespołu nad kickerem oraz skrypt na końcu HTML hero,
   * który uruchamia wejście mobile od razu po wczytaniu hero (bez czekania na resztę strony).
   */
  function zp_suite_311_hero_markup($output, $tag) {
    if ($tag !== 'zp_home_hero' || !is_string($output) || strpos($output, 'id="zhHero"') === false) { return $output; }
    if (strpos($output, 'class="zh__crew"') === false) {
      $base = ZP_SUITE_URL . 'assets/img/hero-team/';
      $people = [
        ['crew-mateusz.webp', 322, 440],
        ['crew-marta.webp', 342, 440],
        ['crew-stanislaw.webp', 321, 440],
      ];
      $imgs = '';
      foreach ($people as $i => $p) {
        $imgs .= '<img class="zh__crewImg zh__crewImg--' . ($i + 1) . '" src="' . esc_url($base . $p[0]) . '" alt="" width="' . $p[1] . '" height="' . $p[2] . '" decoding="async">';
      }
      $crew = '<div class="zh__crew" aria-hidden="true" data-zpl-skip>' . $imgs . '</div>';
      $output = preg_replace('~<p class="zh__eb">~', $crew . '<p class="zh__eb">', $output, 1);
    }
    if (strpos($output, 'zp-suite-311-hero-end-js') === false) {
      $output .= '<script id="zp-suite-311-hero-end-js">(function(w){try{if(w.__zpHeroEnd)w.__zpHeroEnd();}catch(e){}})(window);</script>';
    }
    return $output;
  }
}
add_filter('do_shortcode_tag', 'zp_suite_311_hero_markup', 30, 2);

if (!function_exists('zp_suite_311_final_html')) {
  /**
   * Ostatnie przejście po gotowym HTML strony głównej (czysta obróbka tekstu, bez funkcji WP):
   * obrazy poniżej hero (i ukryte warianty logo w nagłówku) dostają loading="lazy" decoding="async" — na telefonie
   * ok. 400 KB obrazów spod hero nie konkuruje już z hero i robotem o łącze.
   *
   * v2.2.795: Cookiebota NIE ruszamy. Ten bufor działa przed buforem modułu językowego (który przełącza tag
   * w tryb manualny async i ustawia data-culture), więc każda zamiana tagu tutaj odcina tę obróbkę.
   */
  function zp_suite_311_final_html($html) {
    if (!is_string($html) || $html === '' || stripos($html, '<html') === false) { return $html; }
    // 1) lazy images below the hero
    $end = strpos($html, 'zp-suite-311-hero-end-js');
    if ($end === false) { $h = strpos($html, 'id="zhHero"'); $end = $h === false ? 0 : (strpos($html, '</section>', $h) ?: 0); }
    if ($end > 0) {
      $head = substr($html, 0, $end);
      $tail = substr($html, $end);
      $tail = preg_replace_callback('~<img\b[^>]*>~i', static function ($m) {
        $tag = $m[0];
        if (preg_match('~\sloading\s*=~i', $tag)) { return $tag; }
        if (preg_match('~\sfetchpriority\s*=\s*["\']?high~i', $tag)) { return $tag; }
        if (preg_match('~\sclass\s*=\s*["\'][^"\']*zpNewNav__~i', $tag)) { return $tag; }
        $tag = preg_replace('~\s*/?>$~', '', $tag, 1) . ' loading="lazy"' . (preg_match('~\sdecoding\s*=~i', $tag) ? '' : ' decoding="async"') . '>';
        return $tag;
      }, $tail) ?? $tail;
      $head = preg_replace_callback('~<img\b[^>]*\bclass\s*=\s*["\'][^"\']*zpNewNav__(?:logo--dark|drawerLogo)[^"\']*["\'][^>]*>~i', static function ($m) {
        $tag = $m[0];
        if (preg_match('~\sloading\s*=~i', $tag) || preg_match('~\sfetchpriority\s*=\s*["\']?high~i', $tag)) { return $tag; }
        return preg_replace('~\s*/?>$~', '', $tag, 1) . ' loading="lazy">';
      }, $head) ?? $head;
      $html = $head . $tail;
    }
    // Cookiebot: bez zmian — oryginalny tag (async w <head>, tryb manualny i język z URL ustawia moduł językowy).
    return $html;
  }
}
if (!function_exists('zp_suite_311_start_buffer')) {
  function zp_suite_311_start_buffer() {
    if (is_admin() || !function_exists('zp_suite_308_is_home_like') || !zp_suite_308_is_home_like()) { return; }
    ob_start('zp_suite_311_final_html');
  }
}
add_action('template_redirect', 'zp_suite_311_start_buffer', 3);
