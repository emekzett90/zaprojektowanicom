<?php
/**
 * ZP Suite v2.2.667 — Realizacje + Studio Wyceny: editorial flat polish.
 *
 * Kierunek (feedback usera): "mega super", ale BEZ dodawania cieni — kontynuacja
 * języka z front-fixes-300 (mega/drawer): separacja przez hairline'y, światło
 * i typografię, nie przez box-shadow.
 *
 * REALIZACJE (/realizacje/):
 * - Karty portfolio: zero box-shadow (karta, hover, pigułki, przyciski) — hover
 *   pracuje krawędzią (hairline → ink) i zoomem zdjęcia.
 * - Edytorska numeracja "01…32" (czyste CSS counters — karty ukryte filtrem
 *   mają display:none, więc numeracja przelicza się sama po filtrowaniu).
 * - Topline karty: micro-label uppercase z większym trackingiem, rok jako
 *   przezroczysta pigułka hairline z cyframi tabelarycznymi.
 * - Hairline między opisem a chipami; chipy bez niebieskawych wypełnień
 *   (biel + hairline — preferencja: neutralnie zamiast tintów).
 * - Pasek narzędzi (szukajka/filtry/licznik), featured, hero-panel, CTA strip
 *   i modal: flat — cienie zdjęte, separacja hairline'ami.
 *
 * STUDIO WYCENY (/studio-wyceny/, chooser):
 * - Karty usług: widmowa numeracja edytorska 01–04 w rogu karty (CSS counter,
 *   pod treścią, nad tłem), na hover rozjaśnia się na granatowym wypełnieniu.
 * - Chip ceny = wypełniona granatowa pigułka (hierarchia: cena to pierwsza
 *   informacja); reguły hover z 295 są bardziej specyficzne, więc inwersja
 *   na ciemnym hoverze karty zostaje bez zmian.
 * - Sekcja formularza na stronie studia: flat pass — cienie zdjęte,
 *   niebieskawe tinty zneutralizowane (scoped do body.zpbs-studio-chooser,
 *   /kontakt bez zmian).
 *
 * Warstwa wygrywa z 267/268 (wp_head) i 295/298 (wp_footer) późniejszym
 * miejscem w dokumencie przy tej samej lub wyższej specyficzności.
 */
if (!defined('ABSPATH')) { exit; }

function zp_suite_301_is_realizacje(){
  $uri = isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '';
  if (strpos($uri, '/realizacje') !== false) { return true; }
  if (is_singular()) {
    global $post;
    $content = $post && !empty($post->post_content) ? (string) $post->post_content : '';
    if (strpos($content, 'zpRealizacjePage') !== false) { return true; }
    if (has_shortcode($content, 'zp_realizacje_page')) { return true; }
  }
  return false;
}

function zp_suite_301_is_studio(){
  $uri = isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '';
  if (strpos($uri, '/studio-wyceny') !== false) { return true; }
  if (is_singular()) {
    global $post;
    $content = $post && !empty($post->post_content) ? (string) $post->post_content : '';
    if (has_shortcode($content, 'zp_studio_wyceny') || has_shortcode($content, 'zp_studio_wyceny_cms')) { return true; }
  }
  return false;
}

function zp_suite_301_realizacje_studio_polish(){
  if (is_admin()) { return; }
  $realizacje = zp_suite_301_is_realizacje();
  $studio = zp_suite_301_is_studio();
  if (!$realizacje && !$studio) { return; }

  if ($realizacje) {
  ?>
<style id="zp-suite-front-fixes-301-realizacje-editorial">
/* ============================================================
   REALIZACJE — editorial flat gallery
   ============================================================ */

/* ===== 1. Flat pass: zero box-shadow na całej stronie ===== */
html body #zpRealizacjePage .zpRealizacjeCard,
html body #zpRealizacjePage .zpRealizacjeCard:hover,
html body #zpRealizacjePage .zpRealizacjeCard__cat,
html body #zpRealizacjePage .zpRealizacjeCard__year,
html body #zpRealizacjePage .zpRealizacjePage__search,
html body #zpRealizacjePage .zpRealizacjePage__search:focus-within,
html body #zpRealizacjePage .zpRealizacjePage__search input,
html body #zpRealizacjePage .zpRealizacjePage__search input:focus,
html body #zpRealizacjePage .zpRealizacjePage__filter,
html body #zpRealizacjePage .zpRealizacjePage__filter:hover,
html body #zpRealizacjePage .zpRealizacjePage__filter.is-active,
html body #zpRealizacjePage .zpRealizacjePage__count,
html body #zpRealizacjePage .zpRealizacjePage__heroPanel,
html body #zpRealizacjePage .zpFeaturedRealizacja__copy,
html body #zpRealizacjePage .zpFeaturedRealizacja__media,
html body #zpRealizacjePage .zpFeaturedRealizacja__floating,
html body #zpRealizacjePage .zpRealizacjeBtn,
html body #zpRealizacjePage .zpRealizacjeBtn:hover,
html body #zpRealizacjePage .zpRealizacjeCard__actions .zpRealizacjeBtn,
html body .zpRealizacjeCtaStrip .zpRealizacjeBtn,
html body .zpRealizacjeCtaStrip .zpRealizacjeBtn:hover,
html body .zpRealizacjeModal__dialog,
html body .zpRealizacjeModal__close,
html body .zpRealizacjeModal__factIcon,
html body .zpRealizacjeModal .zpRealizacjeBtn,
html body .zpRealizacjeModal .zpRealizacjeBtn:hover{
  box-shadow:none!important;
}
html body .zpRealizacjeCtaStrip__frame span{
  text-shadow:none!important;
}

/* ===== 2. Karta: hairline pracuje zamiast cienia ===== */
html body #zpRealizacjePage .zpRealizacjeCard{
  border-radius:24px!important;
  border-color:rgba(7,17,31,.12)!important;
}
html body #zpRealizacjePage .zpRealizacjeCard:hover{
  transform:translateY(-3px)!important;
  border-color:rgba(7,17,31,.34)!important;
}

/* ===== 3. Edytorska numeracja kart (przelicza się po filtrach) ===== */
html body #zpRealizacjePage .zpRealizacjePage__grid{
  counter-reset:zpRealIdx;
}
html body #zpRealizacjePage .zpRealizacjeCard{
  counter-increment:zpRealIdx;
}
html body #zpRealizacjePage .zpRealizacjeCard::after{
  content:counter(zpRealIdx,decimal-leading-zero);
  position:absolute;
  left:14px;
  top:14px;
  z-index:3;
  display:inline-flex;
  align-items:center;
  min-height:34px;
  padding:0 13px;
  border-radius:999px;
  background:rgba(255,255,255,.94);
  border:1px solid rgba(7,17,31,.10);
  color:#05070b;
  font-size:11px;
  line-height:1;
  font-weight:800;
  letter-spacing:.10em;
  font-variant-numeric:tabular-nums;
  pointer-events:none;
}

/* ===== 4. Topline: micro-label + rok hairline/tabular ===== */
html body #zpRealizacjePage .zpRealizacjeCard__type{
  font-size:10.5px!important;
  letter-spacing:.16em!important;
  color:#69758a!important;
}
html body #zpRealizacjePage .zpRealizacjeCard__year{
  background:transparent!important;
  border:1px solid rgba(7,17,31,.14)!important;
  color:#111827!important;
  font-variant-numeric:tabular-nums!important;
  letter-spacing:.04em!important;
}

/* ===== 5. Chipy: neutralna biel + hairline, nad nimi hairline sekcji ===== */
html body #zpRealizacjePage .zpRealizacjeCard__chips{
  margin-top:16px!important;
  padding-top:14px!important;
  border-top:1px solid rgba(7,17,31,.07)!important;
}
html body #zpRealizacjePage .zpRealizacjeCard__chips span{
  background:#fff!important;
  border-color:rgba(7,17,31,.12)!important;
  color:#46536a!important;
}

/* ===== 6. Pasek narzędzi: flat ===== */
html body #zpRealizacjePage .zpRealizacjePage__search input:focus{
  border-color:rgba(7,17,31,.34)!important;
}
html body #zpRealizacjePage .zpRealizacjePage__count{
  background:transparent!important;
  border:1px solid rgba(7,17,31,.12)!important;
  font-variant-numeric:tabular-nums!important;
}

/* ===== 7. Featured + hero panel: hairline zamiast poświaty ===== */
html body #zpRealizacjePage .zpRealizacjePage__heroPanel{
  border-color:rgba(255,255,255,.14)!important;
}
html body #zpRealizacjePage .zpFeaturedRealizacja__media{
  border-color:rgba(7,17,31,.12)!important;
}
html body #zpRealizacjePage .zpFeaturedRealizacja__floating{
  background:#fff!important;
  border:1px solid rgba(7,17,31,.10)!important;
}

/* ===== 8. Neutralizacja niebieskawych tintów (preferencja: biel/neutral) ===== */
html body #zpRealizacjePage .zpRealizacjePage__empty{
  background:#f6f7f9!important;
  border-color:rgba(7,17,31,.09)!important;
}
html body .zpRealizacjeModal__fact,
html body .zpRealizacjeModal__detail{
  background:#f6f7f9!important;
  border-color:rgba(7,17,31,.08)!important;
}
html body .zpRealizacjeModal__label{
  background:#fff!important;
  border-color:rgba(7,17,31,.12)!important;
}

/* ===== 9. Modal: flat, krawędź hairline ===== */
html body .zpRealizacjeModal__dialog{
  border:1px solid rgba(7,17,31,.14)!important;
}
html body .zpRealizacjeModal__close{
  border:1px solid rgba(7,17,31,.14)!important;
}

@media (max-width:640px){
  html body #zpRealizacjePage .zpRealizacjeCard::after{
    left:12px;
    top:12px;
    min-height:30px;
    padding:0 11px;
    font-size:10px;
  }
}
@media (prefers-reduced-motion:reduce){
  html body #zpRealizacjePage .zpRealizacjeCard:hover{
    transform:none!important;
  }
}
</style>
  <?php
  }

  if ($studio) {
  ?>
<style id="zp-suite-front-fixes-301-studio-polish">
/* ============================================================
   STUDIO WYCENY — chooser polish + flat formularz
   ============================================================ */

/* ===== 1. Widmowa numeracja edytorska 01–04 na kartach usług =====
   Górny lewy róg, przycięta do karty (karta ma overflow:hidden),
   wtopiona od dołu maską — house style watermarków (por. .zpbsWater). */
html body #zpbsUltimate .zpbsServiceList{
  counter-reset:zpbsSvcIdx;
}
html body #zpbsUltimate .zpbsServiceList .zpbsService{
  counter-increment:zpbsSvcIdx;
}
@media (min-width:761px){
  html body #zpbsUltimate .zpbsServiceList .zpbsService::after{
    content:counter(zpbsSvcIdx,decimal-leading-zero)!important;
    position:absolute!important;
    left:clamp(16px,1.8vw,30px)!important;
    top:-.12em!important;
    right:auto!important;
    bottom:auto!important;
    z-index:1!important;
    color:#05070b!important;
    opacity:.06!important;
    font-size:clamp(80px,7.8vw,132px)!important;
    line-height:1!important;
    letter-spacing:-.06em!important;
    font-weight:800!important;
    font-variant-numeric:tabular-nums!important;
    pointer-events:none!important;
    -webkit-mask-image:linear-gradient(180deg,#000 0%,#000 32%,rgba(0,0,0,.45) 64%,transparent 96%)!important;
    mask-image:linear-gradient(180deg,#000 0%,#000 32%,rgba(0,0,0,.45) 64%,transparent 96%)!important;
    transition:color .26s ease,opacity .26s ease!important;
  }
  html body #zpbsUltimate .zpbsServiceList .zpbsService:hover::after,
  html body #zpbsUltimate .zpbsServiceList .zpbsService.is-on::after{
    color:#fff!important;
    opacity:.14!important;
  }
}

/* ===== 1b. Zdjęcia w kartach: bez drop-shadow (flat) =====
   298 kładzie filter:drop-shadow na .zpbsServiceShot img (baza + hover,
   kilka breakpointów) — zdejmujemy wszędzie; animacja floatu zostaje. */
html body #zpbsUltimate[data-mode] .zpbsServiceList .zpbsServiceShot img,
html body #zpbsUltimate[data-mode] .zpbsServiceList .zpbsService:hover .zpbsServiceShot img,
html body #zpbsUltimate[data-mode] .zpbsServiceList .zpbsService:focus-visible .zpbsServiceShot img,
html body #zpbsUltimate[data-mode] .zpbsServiceList .zpbsService.is-on .zpbsServiceShot img{
  filter:none!important;
}

/* ===== 2. Cena jako pierwsza informacja: wypełniona pigułka =====
   Specyficzność podbita atrybutem [data-mode] + podwójną klasą — bazowe
   (1,2,2) przegrywało z warstwą (1,3,2); wzorzec jak przy headerze v666.
   Inwersja na ciemnym hoverze karty odtworzona niżej jeszcze wyżej. */
html body #zpbsUltimate[data-mode] .zpbsServiceList .zpbsChip.zpbsChip--price{
  background:#071426!important;
  color:#fff!important;
  border-color:#071426!important;
}
html body #zpbsUltimate[data-mode] .zpbsServiceList .zpbsService:hover .zpbsChip.zpbsChip--price,
html body #zpbsUltimate[data-mode] .zpbsServiceList .zpbsService:focus-visible .zpbsChip.zpbsChip--price,
html body #zpbsUltimate[data-mode] .zpbsServiceList .zpbsService.is-on .zpbsChip.zpbsChip--price{
  background:#fff!important;
  color:#071426!important;
  border-color:#fff!important;
}

/* ===== 2b. Stopka na stronie studia: wpisy bloga wracaja do zaokraglen =====
   Root cause: studiowy blok "zpbs-v22184-shortcode-final-overrides" wymusza
   overflow:visible!important na CALEJ stopce przez selektor ".zpMegaFooter *"
   (fix pod sticky/teamPhoto) — absolutne zdjecie wpisu (inset:0) przestaje byc
   przycinane przez border-radius karty i posty wygladaja "kwadratowo".
   Przywracamy clipping wylacznie na karcie wpisu; wyzsza specyficznosc
   (body.zpbs-studio-page + 3 klasy) bije (0,1,3) wildcardu. */
html body.zpbs-studio-page .zpMegaFooter .zpMegaFooter__post a{
  overflow:hidden!important;
}

/* ===== 3. Sekcja formularza na stronie studia: flat pass =====
   Scoped do body.zpbs-studio-chooser — /kontakt zostaje jak jest. */
html body.zpbs-studio-chooser .zpContactFormLight *,
html body.zpbs-studio-chooser .zpContactFormLight *::before,
html body.zpbs-studio-chooser .zpContactFormLight *::after{
  box-shadow:none!important;
}
html body.zpbs-studio-chooser .zpContactFormLight input:focus,
html body.zpbs-studio-chooser .zpContactFormLight textarea:focus,
html body.zpbs-studio-chooser .zpContactFormLight select:focus{
  border-color:rgba(16,42,79,.46)!important;
}
</style>
  <?php
  }
}
add_action('wp_footer','zp_suite_301_realizacje_studio_polish',PHP_INT_MAX);
