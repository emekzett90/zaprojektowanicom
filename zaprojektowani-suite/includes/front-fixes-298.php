<?php
/**
 * ZP Suite v2.2.631 — Studio Wyceny:
 * - pewne przywrócenie zdjęcia Mateusza przy logotypach,
 * - twarde przycięcie fotografii na dolnej krawędzi ciemnej sekcji,
 * - sekwencyjne animacje wejścia nagłówka, kart, mockupów i sekcji zaufania.
 */
if (!defined('ABSPATH')) { exit; }

function zp_suite_298_studio_entrance_and_person(){
  if (is_admin()) { return; }
  $uri = isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '';
  $is_studio = strpos($uri, '/studio-wyceny') !== false;
  if (!$is_studio && is_singular()) {
    global $post;
    $content = $post && !empty($post->post_content) ? (string) $post->post_content : '';
    $is_studio = has_shortcode($content, 'zp_studio_wyceny') || has_shortcode($content, 'zp_studio_wyceny_cms');
  }
  if (!$is_studio) { return; }
  ?>
<style id="zp-suite-front-fixes-298-studio-entrance-person">
/* === Mateusz w sekcji logotypów: jawny rozmiar kontenera zapobiega znikaniu
   po absolutnym pozycjonowaniu obrazu. Góra może wychodzić ponad sekcję,
   dół jest przycinany dokładnie na końcu ciemnego tła. === */
@media (min-width:1200px){
  html body #zpbsUltimate .zpbsTrustDark,
  html body #zpbsUltimate .zpbsTrustDark__in{
    overflow:visible!important;
  }
  html body #zpbsUltimate .zpbsTrustDark__in{
    padding-right:clamp(410px,29vw,570px)!important;
  }
  html body #zpbsUltimate .zpbsTrustDark__head,
  html body #zpbsUltimate .zpbsTrustDark__grid{
    position:relative!important;
    z-index:4!important;
  }
  html body #zpbsUltimate .zpbsTrustDark__person{
    display:block!important;
    position:absolute!important;
    top:clamp(-160px,-8vw,-112px)!important;
    right:clamp(-34px,-1.4vw,-10px)!important;
    bottom:0!important;
    width:clamp(500px,34vw,660px)!important;
    height:auto!important;
    min-height:0!important;
    margin:0!important;
    overflow:hidden!important;
    z-index:3!important;
    opacity:1!important;
    visibility:visible!important;
    transform:none!important;
    clip-path:none!important;
    -webkit-clip-path:none!important;
    -webkit-mask-image:none!important;
    mask-image:none!important;
    pointer-events:none!important;
  }
  html body #zpbsUltimate .zpbsTrustDark__person::before{
    display:none!important;
    content:none!important;
  }
  html body #zpbsUltimate .zpbsTrustDark__person img{
    display:block!important;
    position:absolute!important;
    right:0!important;
    bottom:-1px!important;
    width:auto!important;
    height:100%!important;
    min-width:0!important;
    max-width:none!important;
    max-height:none!important;
    object-fit:contain!important;
    object-position:right bottom!important;
    opacity:1!important;
    visibility:visible!important;
    filter:none!important;
    transform:none!important;
  }
  html body #zpbsUltimate .zpbsTrustSlot{
    overflow:visible!important;
    clip-path:inset(-360px 0 0 0)!important;
    -webkit-clip-path:inset(-360px 0 0 0)!important;
  }
  html body #zpbsUltimate .zpbsTrustSlot::after{
    top:100%!important;
    height:34px!important;
    background:#fff!important;
    z-index:50!important;
  }
}

/* === Mocne, ale eleganckie wejście pierwszego ekranu.
   W kartach używamy translate/scale jako osobnych właściwości, dzięki czemu
   nie kolidują z istniejącymi regułami transform!important i hoverami. === */
#zpbsUltimate.zp630Entrance[data-mode="chooser"] .zpbsEyebrow{
  animation:zp630Eyebrow .72s cubic-bezier(.16,1,.3,1) .04s both!important;
}
#zpbsUltimate.zp630Entrance[data-mode="chooser"] .zpbsChoose h1{
  animation:zp630Heading 1.02s cubic-bezier(.16,1,.3,1) .10s both!important;
}
#zpbsUltimate.zp630Entrance[data-mode="chooser"] .zpbsChooseLead{
  animation:zp630Lead .82s cubic-bezier(.16,1,.3,1) .24s both!important;
}
#zpbsUltimate.zp630Entrance[data-mode="chooser"] .zpbsChooseProgress{
  animation:zp630Progress .72s cubic-bezier(.16,1,.3,1) .32s both!important;
}
#zpbsUltimate.zp630Entrance[data-mode="chooser"] .zpbsChoosePick{
  animation:zp630SmallUp .72s cubic-bezier(.16,1,.3,1) .42s both!important;
}
#zpbsUltimate.zp630Entrance[data-mode="chooser"] .zpbsServiceList .zpbsService{
  animation:zp630Card 1s cubic-bezier(.16,1,.3,1) both!important;
  will-change:translate,scale,filter,clip-path!important;
}
#zpbsUltimate.zp630Entrance[data-mode="chooser"] .zpbsServiceList .zpbsService:nth-child(1){animation-delay:.34s!important}
#zpbsUltimate.zp630Entrance[data-mode="chooser"] .zpbsServiceList .zpbsService:nth-child(2){animation-delay:.46s!important}
#zpbsUltimate.zp630Entrance[data-mode="chooser"] .zpbsServiceList .zpbsService:nth-child(3){animation-delay:.58s!important}
#zpbsUltimate.zp630Entrance[data-mode="chooser"] .zpbsServiceList .zpbsService:nth-child(4){animation-delay:.70s!important}
#zpbsUltimate.zp630Entrance[data-mode="chooser"] .zpbsServiceList .zpbsServiceIcon{
  animation:zp630Icon .78s cubic-bezier(.16,1,.3,1) both!important;
}
#zpbsUltimate.zp630Entrance[data-mode="chooser"] .zpbsServiceList .zpbsService:nth-child(1) .zpbsServiceIcon{animation-delay:.52s!important}
#zpbsUltimate.zp630Entrance[data-mode="chooser"] .zpbsServiceList .zpbsService:nth-child(2) .zpbsServiceIcon{animation-delay:.64s!important}
#zpbsUltimate.zp630Entrance[data-mode="chooser"] .zpbsServiceList .zpbsService:nth-child(3) .zpbsServiceIcon{animation-delay:.76s!important}
#zpbsUltimate.zp630Entrance[data-mode="chooser"] .zpbsServiceList .zpbsService:nth-child(4) .zpbsServiceIcon{animation-delay:.88s!important}
#zpbsUltimate.zp630Entrance[data-mode="chooser"] .zpbsServiceList .zpbsServiceShot{
  animation:zp630Shot 1.18s cubic-bezier(.16,1,.3,1) both!important;
}
#zpbsUltimate.zp630Entrance[data-mode="chooser"] .zpbsFloatMockups{
  animation:zp630Mockups 1.18s cubic-bezier(.16,1,.3,1) .30s both!important;
  will-change:translate,scale,filter,clip-path!important;
}
#zpbsUltimate.zp630Entrance[data-mode="chooser"] .zpbsChooseTrust{
  animation:zp630SmallUp .78s cubic-bezier(.16,1,.3,1) .82s both!important;
}

@keyframes zp630Eyebrow{
  0%{opacity:0;filter:blur(8px);translate:-24px 0;clip-path:inset(0 100% 0 0)}
  100%{opacity:1;filter:blur(0);translate:0 0;clip-path:inset(0)}
}
@keyframes zp630Heading{
  0%{opacity:0;filter:blur(16px);translate:0 52px;scale:.965;clip-path:inset(0 0 100% 0)}
  58%{opacity:1;filter:blur(0);translate:0 -5px;scale:1.006;clip-path:inset(0)}
  100%{opacity:1;filter:blur(0);translate:0 0;scale:1;clip-path:inset(0)}
}
@keyframes zp630Lead{
  0%{opacity:0;filter:blur(9px);translate:0 26px}
  100%{opacity:1;filter:blur(0);translate:0 0}
}
@keyframes zp630Progress{
  0%{opacity:0;filter:blur(6px);translate:34px 0;scale:.94}
  100%{opacity:1;filter:blur(0);translate:0 0;scale:1}
}
@keyframes zp630SmallUp{
  0%{opacity:0;filter:blur(7px);translate:0 24px}
  100%{opacity:1;filter:blur(0);translate:0 0}
}
@keyframes zp630Card{
  0%{filter:blur(15px) saturate(.72);translate:0 76px;scale:.925;clip-path:inset(10% 5% 16% 5% round 38px)}
  62%{filter:blur(0) saturate(1);translate:0 -8px;scale:1.012;clip-path:inset(0 round 30px)}
  100%{filter:blur(0) saturate(1);translate:0 0;scale:1;clip-path:inset(0 round 30px)}
}
@keyframes zp630Icon{
  0%{filter:blur(8px);translate:0 18px;scale:.5;rotate:-14deg}
  68%{filter:blur(0);translate:0 -3px;scale:1.08;rotate:3deg}
  100%{filter:blur(0);translate:0 0;scale:1;rotate:0deg}
}
@keyframes zp630Shot{
  0%{filter:blur(13px) saturate(.55);translate:48px -30px;scale:.88}
  100%{filter:blur(0) saturate(.96);translate:0 0;scale:1}
}
@keyframes zp630Mockups{
  0%{filter:blur(18px) saturate(.7);translate:110px 28px;scale:.88;clip-path:inset(8% 4% 8% 8% round 42px)}
  65%{filter:blur(0) saturate(1);translate:-7px -4px;scale:1.012;clip-path:inset(0 round 28px)}
  100%{filter:blur(0) saturate(1);translate:0 0;scale:1;clip-path:inset(0 round 28px)}
}

/* Sekcja z logotypami animuje się dopiero, gdy dojedzie do viewportu. */
#zpbsUltimate .zpbsTrustDark.zp630TrustEntrance .zpbsTrustDark__head{
  animation:zp630TrustHead .9s cubic-bezier(.16,1,.3,1) both!important;
}
#zpbsUltimate .zpbsTrustDark.zp630TrustEntrance .zpbsTrustDark__grid{
  animation:zp630TrustGrid .94s cubic-bezier(.16,1,.3,1) .12s both!important;
}
#zpbsUltimate .zpbsTrustDark.zp630TrustEntrance .zpbsTrustDark__logo{
  animation:zp630TrustLogo .68s cubic-bezier(.16,1,.3,1) both!important;
}
#zpbsUltimate .zpbsTrustDark.zp630TrustEntrance .zpbsTrustDark__logo:nth-child(1){animation-delay:.20s!important}
#zpbsUltimate .zpbsTrustDark.zp630TrustEntrance .zpbsTrustDark__logo:nth-child(2){animation-delay:.25s!important}
#zpbsUltimate .zpbsTrustDark.zp630TrustEntrance .zpbsTrustDark__logo:nth-child(3){animation-delay:.30s!important}
#zpbsUltimate .zpbsTrustDark.zp630TrustEntrance .zpbsTrustDark__logo:nth-child(4){animation-delay:.35s!important}
#zpbsUltimate .zpbsTrustDark.zp630TrustEntrance .zpbsTrustDark__logo:nth-child(5){animation-delay:.40s!important}
#zpbsUltimate .zpbsTrustDark.zp630TrustEntrance .zpbsTrustDark__logo:nth-child(6){animation-delay:.45s!important}
#zpbsUltimate .zpbsTrustDark.zp630TrustEntrance .zpbsTrustDark__logo:nth-child(n+7){animation-delay:.50s!important}
#zpbsUltimate .zpbsTrustDark.zp630TrustEntrance .zpbsTrustDark__person{
  animation:zp630Person 1.15s cubic-bezier(.16,1,.3,1) .12s both!important;
}
@keyframes zp630TrustHead{
  0%{opacity:0;filter:blur(12px);translate:-36px 28px;clip-path:inset(0 100% 0 0)}
  100%{opacity:1;filter:blur(0);translate:0 0;clip-path:inset(0)}
}
@keyframes zp630TrustGrid{
  0%{opacity:0;filter:blur(11px);translate:0 42px;scale:.97}
  100%{opacity:1;filter:blur(0);translate:0 0;scale:1}
}
@keyframes zp630TrustLogo{
  0%{filter:blur(8px);translate:0 22px;scale:.78}
  72%{filter:blur(0);translate:0 -3px;scale:1.035}
  100%{filter:blur(0);translate:0 0;scale:1}
}
@keyframes zp630Person{
  0%{opacity:0;filter:blur(15px);translate:110px 38px;scale:.93}
  68%{opacity:1;filter:blur(0);translate:-6px 0;scale:1.008}
  100%{opacity:1;filter:blur(0);translate:0 0;scale:1}
}

@media (max-width:1145px){
  #zpbsUltimate .zpbsTrustDark__person{display:none!important}
}

/* === v2.2.641 MOBILE: mockup jest elementem karty, ale swobodnie wychodzi ponad jej górę.
   Kluczowe: kasujemy contain:paint, overflow:hidden oraz clip-path z wcześniejszego redesignu/animacji. === */
@keyframes zp641MobileCardIn{
  0%{opacity:0;filter:blur(10px);translate:0 34px;scale:.97}
  100%{opacity:1;filter:blur(0);translate:0 0;scale:1}
}
@keyframes zp641MobileHeadingIn{
  0%{opacity:0;filter:blur(12px);translate:0 30px}
  100%{opacity:1;filter:blur(0);translate:0 0}
}
@keyframes zp641ServiceShotFloat{
  0%,100%{transform:translate3d(0,-7px,0) rotate(-.7deg)}
  50%{transform:translate3d(0,8px,0) rotate(.7deg)}
}
@media (max-width:1180px){
  html body #zpbsUltimate,
  html body #zpbsUltimate .zpbsChoose,
  html body #zpbsUltimate .zpbsChoose__in,
  html body #zpbsUltimate .zpbsServicePanel,
  html body #zpbsUltimate .zpbsServiceList,
  html body #zpbsUltimate .zpbsServiceShopWrap{
    overflow:visible!important;
    contain:none!important;
    clip-path:none!important;
    -webkit-clip-path:none!important;
  }
  html body #zpbsUltimate{
    overflow-x:clip!important;
    overflow-y:visible!important;
  }
  html body #zpbsUltimate .zpbsChoose{
    overflow-x:clip!important;
    overflow-y:visible!important;
  }

  /* Nagłówek bez obcinania dolnych części liter. */
  html body #zpbsUltimate .zpbsChoose h1,
  #zpbsUltimate.zp630Entrance[data-mode="chooser"] .zpbsChoose h1{
    display:block!important;
    height:auto!important;
    min-height:0!important;
    line-height:1.14!important;
    padding:0 0 .20em!important;
    margin:0!important;
    overflow:visible!important;
    contain:none!important;
    clip-path:none!important;
    -webkit-clip-path:none!important;
    animation:zp641MobileHeadingIn .78s cubic-bezier(.16,1,.3,1) .08s both!important;
  }
  html body #zpbsUltimate .zpbsChoose h1 .zpbsH1Lite,
  html body #zpbsUltimate .zpbsChoose h1 .zpbsH1Bold{
    display:inline!important;
    line-height:inherit!important;
    padding-bottom:.08em!important;
    overflow:visible!important;
    clip-path:none!important;
    -webkit-clip-path:none!important;
  }

  /* Rezerwujemy fizyczne miejsce nad każdą kartą, więc mockup nie nachodzi na kartę wyżej. */
  html body #zpbsUltimate .zpbsServiceList{
    grid-template-columns:1fr!important;
    gap:82px!important;
    padding-top:74px!important;
    isolation:isolate!important;
  }
  html body #zpbsUltimate .zpbsServiceShopWrap{
    overflow:visible!important;
    contain:none!important;
    clip-path:none!important;
    -webkit-clip-path:none!important;
  }

  /* Sama karta może mieć border-radius, ale NIE może klipować zdjęcia. */
  html body #zpbsUltimate .zpbsServiceList .zpbsService,
  #zpbsUltimate.zp630Entrance[data-mode="chooser"] .zpbsServiceList .zpbsService{
    position:relative!important;
    isolation:isolate!important;
    overflow:visible!important;
    contain:none!important;
    clip-path:none!important;
    -webkit-clip-path:none!important;
    min-height:0!important;
    padding:0 18px 20px!important;
    border-radius:24px!important;
    animation:zp641MobileCardIn .82s cubic-bezier(.16,1,.3,1) both!important;
    will-change:opacity,translate,scale,filter!important;
  }

  /* Duży, pełny mockup: szerzej niż karta i wysoko ponad jej górną krawędzią. */
  html body #zpbsUltimate .zpbsServiceList .zpbsServiceShot,
  #zpbsUltimate.zp630Entrance[data-mode="chooser"] .zpbsServiceList .zpbsServiceShot{
    position:absolute!important;
    left:-6%!important;
    right:auto!important;
    top:-72px!important;
    bottom:auto!important;
    width:112%!important;
    max-width:none!important;
    height:214px!important;
    margin:0!important;
    padding:0!important;
    z-index:2!important;
    border-radius:0!important;
    overflow:visible!important;
    contain:none!important;
    clip-path:none!important;
    -webkit-clip-path:none!important;
    opacity:1!important;
    transform:none!important;
    filter:none!important;
    -webkit-mask-image:none!important;
    mask-image:none!important;
    animation:none!important;
    pointer-events:none!important;
  }
  html body #zpbsUltimate .zpbsServiceList .zpbsServiceShot::before{
    content:""!important;
    position:absolute!important;
    left:10%!important;
    right:10%!important;
    bottom:8%!important;
    height:34%!important;
    z-index:0!important;
    border-radius:999px!important;
    background:radial-gradient(ellipse,rgba(7,20,38,.22),rgba(7,20,38,0) 70%)!important;
    filter:blur(14px)!important;
    opacity:.7!important;
  }
  html body #zpbsUltimate .zpbsServiceList .zpbsServiceShot img{
    position:relative!important;
    z-index:1!important;
    display:block!important;
    width:100%!important;
    height:100%!important;
    max-width:none!important;
    max-height:none!important;
    object-fit:contain!important;
    object-position:center bottom!important;
    overflow:visible!important;
    contain:none!important;
    clip-path:none!important;
    -webkit-clip-path:none!important;
    -webkit-mask-image:none!important;
    mask-image:none!important;
    animation:zp641ServiceShotFloat 4.4s ease-in-out infinite!important;
    filter:drop-shadow(0 22px 25px rgba(5,7,11,.22)) drop-shadow(0 5px 8px rgba(5,7,11,.13))!important;
    transform-origin:center bottom!important;
    will-change:transform!important;
  }

  /* Treść pozostaje nad tłem i dolną częścią grafiki. */
  html body #zpbsUltimate .zpbsServiceList .zpbsServiceIcon{
    position:relative!important;
    z-index:5!important;
    margin:126px 0 12px!important;
  }
  html body #zpbsUltimate .zpbsServiceList .zpbsServiceText,
  html body #zpbsUltimate .zpbsServiceList .zpbsServiceChips{
    position:relative!important;
    z-index:5!important;
  }
  html body #zpbsUltimate .zpbsServiceList .zpbsServiceArrow{
    z-index:6!important;
  }

  html body #zpbsUltimate .zpbsServiceList .zpbsService:hover .zpbsServiceShot,
  html body #zpbsUltimate .zpbsServiceList .zpbsService.is-on .zpbsServiceShot{
    opacity:1!important;
    transform:none!important;
  }
  html body #zpbsUltimate .zpbsServiceList .zpbsService:hover .zpbsServiceShot img,
  html body #zpbsUltimate .zpbsServiceList .zpbsService.is-on .zpbsServiceShot img{
    filter:drop-shadow(0 26px 30px rgba(0,0,0,.28)) brightness(1.025)!important;
  }
}
@media (max-width:390px){
  html body #zpbsUltimate .zpbsServiceList{
    gap:74px!important;
    padding-top:68px!important;
  }
  html body #zpbsUltimate .zpbsServiceList .zpbsServiceShot,
  #zpbsUltimate.zp630Entrance[data-mode="chooser"] .zpbsServiceList .zpbsServiceShot{
    top:-64px!important;
    left:-5%!important;
    width:110%!important;
    height:194px!important;
  }
  html body #zpbsUltimate .zpbsServiceList .zpbsServiceIcon{
    margin-top:114px!important;
  }
}


/* === v2.2.642: ten sam swobodny overflow na desktopie + branding większy o 20%. === */
/* Powiększenie tylko grafiki „Logo i branding”; osobna właściwość scale nie koliduje
   z animowanym transformem floatowania. */
html body #zpbsUltimate .zpbsService[data-service="brand"] .zpbsServiceShot img{
  scale:1.2!important;
  transform-origin:center bottom!important;
}

/* Desktop zachowuje kompaktowe karty obok dużego podglądu, ale mockupy kart również
   wystają ponad ich górną krawędź i nie są przycinane. */
@media (min-width:1181px){
  html body #zpbsUltimate,
  html body #zpbsUltimate .zpbsChoose,
  html body #zpbsUltimate .zpbsChoose__in,
  html body #zpbsUltimate .zpbsServicePanel,
  html body #zpbsUltimate .zpbsServiceList,
  html body #zpbsUltimate .zpbsServiceShopWrap{
    overflow:visible!important;
    contain:none!important;
    clip-path:none!important;
    -webkit-clip-path:none!important;
  }
  html body #zpbsUltimate{
    overflow-x:clip!important;
    overflow-y:visible!important;
  }
  html body #zpbsUltimate .zpbsChoose{
    overflow-x:clip!important;
    overflow-y:visible!important;
  }
  html body #zpbsUltimate .zpbsServicePanel{
    isolation:isolate!important;
  }
  html body #zpbsUltimate .zpbsServiceList{
    gap:44px!important;
    padding-top:44px!important;
    isolation:isolate!important;
  }
  html body #zpbsUltimate .zpbsServiceShopWrap{
    overflow:visible!important;
    contain:none!important;
    clip-path:none!important;
    -webkit-clip-path:none!important;
  }
  html body #zpbsUltimate .zpbsServiceList .zpbsService,
  #zpbsUltimate.zp630Entrance[data-mode="chooser"] .zpbsServiceList .zpbsService{
    position:relative!important;
    min-height:142px!important;
    padding:24px 190px 20px 18px!important;
    overflow:visible!important;
    contain:none!important;
    clip-path:none!important;
    -webkit-clip-path:none!important;
    isolation:isolate!important;
    animation:zp641MobileCardIn .82s cubic-bezier(.16,1,.3,1) both!important;
    will-change:opacity,translate,scale,filter!important;
  }
  html body #zpbsUltimate .zpbsServiceList .zpbsServiceShot,
  #zpbsUltimate.zp630Entrance[data-mode="chooser"] .zpbsServiceList .zpbsServiceShot{
    position:absolute!important;
    top:-50px!important;
    right:28px!important;
    left:auto!important;
    bottom:auto!important;
    width:172px!important;
    max-width:none!important;
    height:184px!important;
    margin:0!important;
    padding:0!important;
    z-index:4!important;
    overflow:visible!important;
    contain:none!important;
    clip-path:none!important;
    -webkit-clip-path:none!important;
    -webkit-mask-image:none!important;
    mask-image:none!important;
    opacity:1!important;
    animation:none!important;
    pointer-events:none!important;
  }
  html body #zpbsUltimate .zpbsServiceList .zpbsServiceShot::before{
    left:4%!important;
    right:4%!important;
    bottom:4%!important;
    height:38%!important;
    opacity:.78!important;
  }
  html body #zpbsUltimate .zpbsServiceList .zpbsServiceShot img{
    position:relative!important;
    z-index:2!important;
    display:block!important;
    width:100%!important;
    height:100%!important;
    max-width:none!important;
    max-height:none!important;
    object-fit:contain!important;
    object-position:center bottom!important;
    overflow:visible!important;
    contain:none!important;
    clip-path:none!important;
    -webkit-clip-path:none!important;
    -webkit-mask-image:none!important;
    mask-image:none!important;
    animation:zp641ServiceShotFloat 4.4s ease-in-out infinite!important;
    filter:drop-shadow(0 20px 24px rgba(5,7,11,.22)) drop-shadow(0 5px 8px rgba(5,7,11,.13))!important;
    transform-origin:center bottom!important;
    will-change:transform!important;
  }
  html body #zpbsUltimate .zpbsServiceList .zpbsServiceIcon,
  html body #zpbsUltimate .zpbsServiceList .zpbsServiceText,
  html body #zpbsUltimate .zpbsServiceList .zpbsServiceChips,
  html body #zpbsUltimate .zpbsServiceList .zpbsServiceArrow{
    position:relative!important;
    z-index:6!important;
  }
  html body #zpbsUltimate .zpbsServiceList .zpbsServiceIcon{
    margin:0!important;
  }
  html body #zpbsUltimate .zpbsServiceList .zpbsServiceArrow{
    position:absolute!important;
    top:14px!important;
    right:12px!important;
    z-index:8!important;
  }
  html body #zpbsUltimate .zpbsServiceList .zpbsService:hover,
  html body #zpbsUltimate .zpbsServiceList .zpbsService.is-on{
    transform:translateY(-2px)!important;
    clip-path:none!important;
    -webkit-clip-path:none!important;
  }
  html body #zpbsUltimate .zpbsServiceList .zpbsService:hover .zpbsServiceShot img,
  html body #zpbsUltimate .zpbsServiceList .zpbsService.is-on .zpbsServiceShot img{
    filter:drop-shadow(0 26px 30px rgba(0,0,0,.30)) brightness(1.025)!important;
  }
}

/* Na tabletach grafiki są pełnoszerokie jak na mobile, tylko nieco większe. */
@media (min-width:761px) and (max-width:1180px){
  html body #zpbsUltimate .zpbsServiceList{
    gap:92px!important;
    padding-top:82px!important;
  }
  html body #zpbsUltimate .zpbsServiceList .zpbsService,
  #zpbsUltimate.zp630Entrance[data-mode="chooser"] .zpbsServiceList .zpbsService{
    padding:0 24px 24px!important;
    border-radius:28px!important;
  }
  html body #zpbsUltimate .zpbsServiceList .zpbsServiceShot,
  #zpbsUltimate.zp630Entrance[data-mode="chooser"] .zpbsServiceList .zpbsServiceShot{
    top:-84px!important;
    left:-4%!important;
    width:108%!important;
    height:258px!important;
  }
  html body #zpbsUltimate .zpbsServiceList .zpbsServiceIcon{
    margin-top:158px!important;
  }
}

@media (prefers-reduced-motion:reduce){
  #zpbsUltimate.zp630Entrance *,
  #zpbsUltimate .zpbsTrustDark.zp630TrustEntrance *{
    animation-duration:.001ms!important;
    animation-delay:0s!important;
  }
}
</style>
<style id="zp-suite-front-fixes-298-v2666-studio-images-no-shadow">
/* v2.2.666 — Studio Wyceny: całkowite usunięcie cieni spod zdjęć w kartach usług */
html body #zpbsUltimate .zpbsServiceList .zpbsServiceShot,
html body #zpbsUltimate .zpbsServiceList .zpbsServiceShot img,
html body #zpbsUltimate .zpbsServiceList .zpbsService:hover .zpbsServiceShot,
html body #zpbsUltimate .zpbsServiceList .zpbsService:hover .zpbsServiceShot img,
html body #zpbsUltimate .zpbsServiceList .zpbsService.is-on .zpbsServiceShot,
html body #zpbsUltimate .zpbsServiceList .zpbsService.is-on .zpbsServiceShot img{
  filter:none!important;
  -webkit-filter:none!important;
  box-shadow:none!important;
  text-shadow:none!important;
}
html body #zpbsUltimate .zpbsServiceList .zpbsServiceShot::before,
html body #zpbsUltimate .zpbsServiceList .zpbsServiceShot::after{
  display:none!important;
  content:none!important;
  opacity:0!important;
  visibility:hidden!important;
  background:none!important;
  filter:none!important;
  -webkit-filter:none!important;
  box-shadow:none!important;
}
</style>


<style id="zp-suite-front-fixes-298-v2645-desktop-cards">
/* v2.2.645 — rzeczywisty desktopowy override ładowany po wszystkich redesignach. */
@media (min-width:1181px){
  html body #zpbsUltimate .zpbsServicePanel,
  html body #zpbsUltimate .zpbsServiceList,
  html body #zpbsUltimate .zpbsServiceShopWrap,
  html body #zpbsUltimate .zpbsServiceList .zpbsService{
    overflow:visible!important;
    contain:none!important;
    clip-path:none!important;
    -webkit-clip-path:none!important;
  }

  /* więcej przestrzeni na dwukrotnie większe mockupy */
  html body #zpbsUltimate .zpbsServiceList{
    grid-template-columns:repeat(2,minmax(0,1fr))!important;
    gap:clamp(42px,3vw,62px) clamp(22px,2vw,34px)!important;
    padding-top:118px!important;
  }
  html body #zpbsUltimate .zpbsServiceList .zpbsService,
  #zpbsUltimate.zp630Entrance[data-mode="chooser"] .zpbsServiceList .zpbsService{
    display:flex!important;
    flex-direction:column!important;
    justify-content:flex-end!important;
    align-items:stretch!important;
    min-height:clamp(292px,18vw,336px)!important;
    padding:clamp(32px,2.2vw,42px) clamp(350px,24vw,410px) clamp(24px,1.8vw,30px) clamp(26px,2vw,34px)!important;
    border-radius:30px!important;
    overflow:visible!important;
    contain:none!important;
    clip-path:none!important;
    -webkit-clip-path:none!important;
    isolation:isolate!important;
  }

  /* mockupy około 2x większe niż w v2.2.642 */
  html body #zpbsUltimate .zpbsServiceList .zpbsServiceShot,
  #zpbsUltimate.zp630Entrance[data-mode="chooser"] .zpbsServiceList .zpbsServiceShot{
    position:absolute!important;
    top:-118px!important;
    right:38px!important;
    left:auto!important;
    bottom:auto!important;
    width:344px!important;
    max-width:none!important;
    height:368px!important;
    z-index:4!important;
    overflow:visible!important;
    contain:none!important;
    clip-path:none!important;
    -webkit-clip-path:none!important;
    -webkit-mask-image:none!important;
    mask-image:none!important;
    opacity:1!important;
    transform:none!important;
    animation:none!important;
    pointer-events:none!important;
  }
  html body #zpbsUltimate .zpbsServiceList .zpbsServiceShot::before{
    left:-4%!important;
    right:-4%!important;
    bottom:2%!important;
    height:42%!important;
    filter:blur(18px)!important;
    opacity:.8!important;
  }
  html body #zpbsUltimate .zpbsServiceList .zpbsServiceShot img{
    display:block!important;
    width:100%!important;
    height:100%!important;
    max-width:none!important;
    max-height:none!important;
    object-fit:contain!important;
    object-position:center bottom!important;
    overflow:visible!important;
    contain:none!important;
    clip-path:none!important;
    -webkit-clip-path:none!important;
    -webkit-mask-image:none!important;
    mask-image:none!important;
    animation:zp641ServiceShotFloat 4.4s ease-in-out infinite!important;
    filter:drop-shadow(0 28px 34px rgba(5,7,11,.24)) drop-shadow(0 7px 12px rgba(5,7,11,.14))!important;
    transform-origin:center bottom!important;
  }

  /* branding pozostaje dodatkowo większy */
  html body #zpbsUltimate .zpbsServiceList .zpbsService[data-service="brand"] .zpbsServiceShot{
    width:392px!important;
    height:420px!important;
    top:-142px!important;
    right:12px!important;
  }
  html body #zpbsUltimate .zpbsServiceList .zpbsService[data-service="brand"] .zpbsServiceShot img{
    scale:1.08!important;
  }

  /* ikonka w całości po prawej stronie, pod strzałką i nad mockupem */
  html body #zpbsUltimate .zpbsServiceList .zpbsServiceIcon{
    position:absolute!important;
    left:auto!important;
    right:26px!important;
    top:auto!important;
    bottom:24px!important;
    width:64px!important;
    height:64px!important;
    margin:0!important;
    border-radius:20px!important;
    z-index:8!important;
    box-shadow:0 14px 34px rgba(7,20,38,.12)!important;
  }
  html body #zpbsUltimate .zpbsServiceList .zpbsServiceIcon svg{
    width:28px!important;
    height:28px!important;
  }
  html body #zpbsUltimate .zpbsServiceList .zpbsServiceArrow{
    position:absolute!important;
    top:18px!important;
    right:18px!important;
    z-index:10!important;
  }

  html body #zpbsUltimate .zpbsServiceList .zpbsServiceText,
  html body #zpbsUltimate .zpbsServiceList .zpbsServiceChips{
    position:relative!important;
    z-index:7!important;
    max-width:100%!important;
  }
  html body #zpbsUltimate .zpbsServiceList .zpbsServiceText strong{
    font-size:clamp(27px,1.8vw,35px)!important;
  }
  html body #zpbsUltimate .zpbsServiceList .zpbsServiceText span{
    font-size:clamp(13px,.9vw,15px)!important;
    line-height:1.52!important;
  }
}

/* łagodniejsze skalowanie na mniejszych desktopach, żeby nic nie nachodziło na tekst */
@media (min-width:1181px) and (max-width:1450px){
  html body #zpbsUltimate .zpbsServiceList .zpbsService,
  #zpbsUltimate.zp630Entrance[data-mode="chooser"] .zpbsServiceList .zpbsService{
    padding-right:310px!important;
  }
  html body #zpbsUltimate .zpbsServiceList .zpbsServiceShot,
  #zpbsUltimate.zp630Entrance[data-mode="chooser"] .zpbsServiceList .zpbsServiceShot{
    width:292px!important;
    height:326px!important;
    top:-102px!important;
    right:30px!important;
  }
  html body #zpbsUltimate .zpbsServiceList .zpbsService[data-service="brand"] .zpbsServiceShot{
    width:336px!important;
    height:372px!important;
    top:-122px!important;
  }
}
</style>
<style id="zp-suite-front-fixes-298-v2649-desktop-only-tuning">
/* v2.2.649 — WYŁĄCZNIE desktop (od 1181 px). Mobile i tablet bez zmian. */
@media (min-width:1181px){
  /* Cienie zdjęć na desktopie o około 50% jaśniejsze. */
  html body #zpbsUltimate .zpbsServiceList .zpbsServiceShot::before{
    opacity:.40!important;
  }
  html body #zpbsUltimate .zpbsServiceList .zpbsServiceShot img{
    filter:drop-shadow(0 28px 34px rgba(5,7,11,.12)) drop-shadow(0 7px 12px rgba(5,7,11,.07))!important;
  }
  html body #zpbsUltimate .zpbsServiceList .zpbsService:hover .zpbsServiceShot img,
  html body #zpbsUltimate .zpbsServiceList .zpbsService.is-on .zpbsServiceShot img{
    filter:drop-shadow(0 26px 30px rgba(0,0,0,.15)) brightness(1.025)!important;
  }

  /* Górny rząd: strona i sklep — łącznie +30% względem v2.2.645 i 20% wyżej. */
  html body #zpbsUltimate .zpbsServiceList .zpbsService[data-service="web"] .zpbsServiceShot,
  html body #zpbsUltimate .zpbsServiceList .zpbsService[data-service="shop"] .zpbsServiceShot{
    width:447px!important;
    height:478px!important;
    top:-192px!important;
    right:16px!important;
  }

  /* Dolny rząd: branding i Meta przesunięte niżej. */
  html body #zpbsUltimate .zpbsServiceList .zpbsService[data-service="brand"] .zpbsServiceShot{
    top:-79px!important;
  }
  /* Meta dodatkowo -20% rozmiaru względem v2.2.645. */
  html body #zpbsUltimate .zpbsServiceList .zpbsService[data-service="ads"] .zpbsServiceShot{
    width:275px!important;
    height:294px!important;
    top:-74px!important;
    right:50px!important;
  }
}

/* Te same proporcje na mniejszych desktopach 1181–1450 px. */
@media (min-width:1181px) and (max-width:1450px){
  html body #zpbsUltimate .zpbsServiceList .zpbsService[data-service="web"] .zpbsServiceShot,
  html body #zpbsUltimate .zpbsServiceList .zpbsService[data-service="shop"] .zpbsServiceShot{
    width:380px!important;
    height:424px!important;
    top:-167px!important;
    right:10px!important;
  }
  html body #zpbsUltimate .zpbsServiceList .zpbsService[data-service="brand"] .zpbsServiceShot{
    top:-66px!important;
  }
  html body #zpbsUltimate .zpbsServiceList .zpbsService[data-service="ads"] .zpbsServiceShot{
    width:234px!important;
    height:261px!important;
    top:-63px!important;
    right:42px!important;
  }
}
</style>
<style id="zp-suite-front-fixes-298-v2653-mobile-keep-desktop-651-responsive">
/* v2.2.653 — mobile from 2.2.649 preserved, only icon card moved right; desktop like 2.2.651 */

/* Mobile i mniejsze rozdzielczości: tylko kafelek ikony po prawej, bez innych zmian układu */
@media (max-width:1180px){
  html body #zpbsUltimate .zpbsServiceList .zpbsServiceIcon{
    position:absolute!important;
    left:auto!important;
    right:18px!important;
    top:auto!important;
    bottom:18px!important;
    margin:0!important;
    z-index:8!important;
  }
}
@media (max-width:760px){
  html body #zpbsUltimate .zpbsServiceList .zpbsServiceIcon{
    right:16px!important;
    bottom:16px!important;
  }
}
@media (max-width:390px){
  html body #zpbsUltimate .zpbsServiceList .zpbsServiceIcon{
    right:14px!important;
    bottom:14px!important;
  }
}

/* Desktop i większe rozdzielczości: zachowanie jak w 2.2.651 */
@media (min-width:1181px){
  /* pierwsze 2 karty wyżej */
  html body #zpbsUltimate .zpbsServiceList .zpbsService[data-service="web"] .zpbsServiceShot,
  html body #zpbsUltimate .zpbsServiceList .zpbsService[data-service="shop"] .zpbsServiceShot{
    top:-281px!important;
  }

  /* logo i branding: +15% i wyżej */
  html body #zpbsUltimate .zpbsServiceList .zpbsService[data-service="brand"] .zpbsServiceShot{
    width:451px!important;
    height:483px!important;
    top:-152px!important;
    right:10px!important;
  }
}

/* responsywne dopasowanie dla mniejszych desktopów */
@media (min-width:1181px) and (max-width:1450px){
  html body #zpbsUltimate .zpbsServiceList .zpbsService[data-service="web"] .zpbsServiceShot,
  html body #zpbsUltimate .zpbsServiceList .zpbsService[data-service="shop"] .zpbsServiceShot{
    top:-252px!important;
  }
  html body #zpbsUltimate .zpbsServiceList .zpbsService[data-service="brand"] .zpbsServiceShot{
    width:386px!important;
    height:428px!important;
    top:-132px!important;
    right:10px!important;
  }
}
</style>
<style id="zp-suite-front-fixes-298-v2654-mobile-icon-layout-stability">
/* v2.2.654 — keep the right-side mobile icon without collapsing the card */
@media (max-width:760px){
  html body #zpbsUltimate .zpbsServiceList .zpbsService,
  #zpbsUltimate.zp630Entrance[data-mode="chooser"] .zpbsServiceList .zpbsService{
    min-height:260px!important;
    padding-top:126px!important;
    padding-right:86px!important;
  }
  html body #zpbsUltimate .zpbsServiceList .zpbsServiceIcon{
    position:absolute!important;
    left:auto!important;
    right:16px!important;
    top:auto!important;
    bottom:18px!important;
    margin:0!important;
    z-index:8!important;
  }
  html body #zpbsUltimate .zpbsServiceList .zpbsServiceText,
  html body #zpbsUltimate .zpbsServiceList .zpbsServiceChips{
    position:relative!important;
    z-index:5!important;
    max-width:100%!important;
  }
}
@media (max-width:390px){
  html body #zpbsUltimate .zpbsServiceList .zpbsService,
  #zpbsUltimate.zp630Entrance[data-mode="chooser"] .zpbsServiceList .zpbsService{
    min-height:246px!important;
    padding-top:114px!important;
    padding-right:80px!important;
  }
  html body #zpbsUltimate .zpbsServiceList .zpbsServiceIcon{
    right:14px!important;
    bottom:16px!important;
  }
}
</style>
<style id="zp-suite-front-fixes-298-v2655-mobile-tablet-cards-taller-images-higher">
/* v2.2.655 — mobile/tablet: cards taller, images 50px higher, icon stays right */

@media (min-width:761px) and (max-width:1180px){
  html body #zpbsUltimate .zpbsServiceList .zpbsService,
  #zpbsUltimate.zp630Entrance[data-mode="chooser"] .zpbsServiceList .zpbsService{
    min-height:360px!important;
    padding-top:208px!important;
    padding-right:104px!important;
    padding-bottom:26px!important;
  }
  html body #zpbsUltimate .zpbsServiceList .zpbsServiceShot,
  #zpbsUltimate.zp630Entrance[data-mode="chooser"] .zpbsServiceList .zpbsServiceShot{
    top:-134px!important;
  }
  html body #zpbsUltimate .zpbsServiceList .zpbsServiceIcon{
    position:absolute!important;
    left:auto!important;
    right:20px!important;
    top:auto!important;
    bottom:20px!important;
    margin:0!important;
    z-index:8!important;
  }
  html body #zpbsUltimate .zpbsServiceList .zpbsServiceText,
  html body #zpbsUltimate .zpbsServiceList .zpbsServiceChips{
    position:relative!important;
    z-index:5!important;
    max-width:100%!important;
  }
}

@media (max-width:760px){
  html body #zpbsUltimate .zpbsServiceList .zpbsService,
  #zpbsUltimate.zp630Entrance[data-mode="chooser"] .zpbsServiceList .zpbsService{
    min-height:312px!important;
    padding-top:176px!important;
    padding-right:88px!important;
    padding-bottom:22px!important;
  }
  html body #zpbsUltimate .zpbsServiceList .zpbsServiceShot,
  #zpbsUltimate.zp630Entrance[data-mode="chooser"] .zpbsServiceList .zpbsServiceShot{
    top:-122px!important;
  }
  html body #zpbsUltimate .zpbsServiceList .zpbsServiceIcon{
    position:absolute!important;
    left:auto!important;
    right:16px!important;
    top:auto!important;
    bottom:18px!important;
    margin:0!important;
    z-index:8!important;
  }
  html body #zpbsUltimate .zpbsServiceList .zpbsServiceText,
  html body #zpbsUltimate .zpbsServiceList .zpbsServiceChips{
    position:relative!important;
    z-index:5!important;
    max-width:100%!important;
  }
}

@media (max-width:390px){
  html body #zpbsUltimate .zpbsServiceList .zpbsService,
  #zpbsUltimate.zp630Entrance[data-mode="chooser"] .zpbsServiceList .zpbsService{
    min-height:292px!important;
    padding-top:160px!important;
    padding-right:82px!important;
    padding-bottom:20px!important;
  }
  html body #zpbsUltimate .zpbsServiceList .zpbsServiceShot,
  #zpbsUltimate.zp630Entrance[data-mode="chooser"] .zpbsServiceList .zpbsServiceShot{
    top:-114px!important;
  }
  html body #zpbsUltimate .zpbsServiceList .zpbsServiceIcon{
    right:14px!important;
    bottom:16px!important;
  }
}
</style>
<style id="zp-suite-front-fixes-298-v2656-desktop-tablet-image-tuning">
/* v2.2.656 — desktop/tablet image tuning */

/* Około 1105 px i podobne: zdjęcia trochę niżej, bo były za wysoko */
@media (min-width:981px) and (max-width:1180px){
  html body #zpbsUltimate .zpbsServiceList .zpbsServiceShot,
  #zpbsUltimate.zp630Entrance[data-mode="chooser"] .zpbsServiceList .zpbsServiceShot{
    top:-104px!important;
  }
}

/* Około 1335 px i podobne: dopasowanie wysokości, żeby tekst nie wchodził na obraz */
@media (min-width:1181px) and (max-width:1450px){
  html body #zpbsUltimate .zpbsServiceList .zpbsService[data-service="brand"]{
    padding-right:332px!important;
  }
  html body #zpbsUltimate .zpbsServiceList .zpbsService[data-service="brand"] .zpbsServiceShot{
    width:386px!important;
    height:428px!important;
    top:-122px!important;
    right:10px!important;
  }
}

/* Największe laptopy / duże rozdzielczości */
@media (min-width:1451px){
  /* Strona internetowa +15% */
  html body #zpbsUltimate .zpbsServiceList .zpbsService[data-service="web"]{
    padding-right:440px!important;
  }
  html body #zpbsUltimate .zpbsServiceList .zpbsService[data-service="web"] .zpbsServiceShot{
    width:591px!important;
    height:633px!important;
    top:-281px!important;
    right:-22px!important;
  }

  /* Sklep internetowy +10% */
  html body #zpbsUltimate .zpbsServiceList .zpbsService[data-service="shop"]{
    padding-right:420px!important;
  }
  html body #zpbsUltimate .zpbsServiceList .zpbsService[data-service="shop"] .zpbsServiceShot{
    width:565px!important;
    height:605px!important;
    top:-281px!important;
    right:-16px!important;
  }

  /* Logo i branding wyżej o 30 px */
  html body #zpbsUltimate .zpbsServiceList .zpbsService[data-service="brand"]{
    padding-right:360px!important;
  }
  html body #zpbsUltimate .zpbsServiceList .zpbsService[data-service="brand"] .zpbsServiceShot{
    width:451px!important;
    height:483px!important;
    top:-182px!important;
    right:10px!important;
  }
}
</style>
<style id="zp-suite-front-fixes-298-v2657-resolution-position-tuning">
/* v2.2.657 — requested per-resolution image positioning */

/* Mobile: wszystkie zdjęcia 30 px niżej */
@media (max-width:760px){
  html body #zpbsUltimate .zpbsServiceList .zpbsServiceShot,
  #zpbsUltimate.zp630Entrance[data-mode="chooser"] .zpbsServiceList .zpbsServiceShot{
    top:-92px!important;
  }
}
@media (max-width:390px){
  html body #zpbsUltimate .zpbsServiceList .zpbsServiceShot,
  #zpbsUltimate.zp630Entrance[data-mode="chooser"] .zpbsServiceList .zpbsServiceShot{
    top:-84px!important;
  }
}

/* Około 1335 px: pierwsze 2 karty i ostatnia ~40 px niżej */
@media (min-width:1181px) and (max-width:1450px){
  html body #zpbsUltimate .zpbsServiceList .zpbsService[data-service="web"] .zpbsServiceShot,
  html body #zpbsUltimate .zpbsServiceList .zpbsService[data-service="shop"] .zpbsServiceShot{
    top:-127px!important;
  }
  html body #zpbsUltimate .zpbsServiceList .zpbsService[data-service="ads"] .zpbsServiceShot{
    top:-23px!important;
  }
}

/* Około 1725 px: pierwsze 2 karty 60 px wyżej */
@media (min-width:1600px) and (max-width:1799px){
  html body #zpbsUltimate .zpbsServiceList .zpbsService[data-service="web"] .zpbsServiceShot,
  html body #zpbsUltimate .zpbsServiceList .zpbsService[data-service="shop"] .zpbsServiceShot{
    top:-341px!important;
  }
}

/* Pełna / bardzo szeroka rozdzielczość: pierwsze 2 karty 50 px wyżej */
@media (min-width:1800px){
  html body #zpbsUltimate .zpbsServiceList .zpbsService[data-service="web"] .zpbsServiceShot,
  html body #zpbsUltimate .zpbsServiceList .zpbsService[data-service="shop"] .zpbsServiceShot{
    top:-331px!important;
  }
}
</style>
<style id="zp-suite-front-fixes-298-v2658-resolution-fine-tuning">
/* v2.2.658 — dodatkowe dopasowanie pozycji zdjęć per rozdzielczość */

/* Około 933 px: wszystkie zdjęcia ok. 30 px niżej */
@media (min-width:861px) and (max-width:980px){
  html body #zpbsUltimate .zpbsServiceList .zpbsServiceShot,
  #zpbsUltimate.zp630Entrance[data-mode="chooser"] .zpbsServiceList .zpbsServiceShot{
    top:-104px!important;
  }
}

/* Około 1518 px: pierwsze 2 zdjęcia w pierwszych 2 kartach ok. 100 px wyżej */
@media (min-width:1451px) and (max-width:1699px){
  html body #zpbsUltimate .zpbsServiceList .zpbsService[data-service="web"] .zpbsServiceShot,
  html body #zpbsUltimate .zpbsServiceList .zpbsService[data-service="shop"] .zpbsServiceShot{
    top:-381px!important;
  }
}

/* Pełna / bardzo szeroka rozdzielczość: pierwsze 2 zdjęcia ok. 40 px wyżej */
@media (min-width:1700px){
  html body #zpbsUltimate .zpbsServiceList .zpbsService[data-service="web"] .zpbsServiceShot,
  html body #zpbsUltimate .zpbsServiceList .zpbsService[data-service="shop"] .zpbsServiceShot{
    top:-321px!important;
  }
}
</style>
<style id="zp-suite-front-fixes-298-v2659-final-requested-resolution-rules">
/* v2.2.659 — final requested resolution rules only */

/* Około 933 px: wszystkie zdjęcia 30 px niżej */
@media (min-width:861px) and (max-width:980px){
  html body #zpbsUltimate .zpbsServiceList .zpbsServiceShot,
  #zpbsUltimate.zp630Entrance[data-mode="chooser"] .zpbsServiceList .zpbsServiceShot{
    top:-104px!important;
  }
}

/* Około 1518 px: pierwsze 2 zdjęcia o 100 px wyżej */
@media (min-width:1451px) and (max-width:1699px){
  html body #zpbsUltimate .zpbsServiceList .zpbsService[data-service="web"] .zpbsServiceShot,
  html body #zpbsUltimate .zpbsServiceList .zpbsService[data-service="shop"] .zpbsServiceShot{
    top:-381px!important;
  }
}

/* Pełna / bardzo szeroka rozdzielczość: pierwsze 2 zdjęcia o 40 px wyżej */
@media (min-width:1700px){
  html body #zpbsUltimate .zpbsServiceList .zpbsService[data-service="web"] .zpbsServiceShot,
  html body #zpbsUltimate .zpbsServiceList .zpbsService[data-service="shop"] .zpbsServiceShot{
    top:-321px!important;
  }
}
</style>
<style id="zp-suite-front-fixes-298-v2660-mobile-lower-fullres-raise-fix">
/* v2.2.660 — final fix: mobile/lower resolutions 30px lower, full-res first 2 cards 100px higher */

/* Mobile i mniejsze rozdzielczości: wszystkie zdjęcia 30 px niżej */
@media (min-width:761px) and (max-width:1180px){
  html body #zpbsUltimate .zpbsServiceList .zpbsServiceShot,
  #zpbsUltimate.zp630Entrance[data-mode="chooser"] .zpbsServiceList .zpbsServiceShot{
    top:-104px!important;
  }
}
@media (max-width:760px){
  html body #zpbsUltimate .zpbsServiceList .zpbsServiceShot,
  #zpbsUltimate.zp630Entrance[data-mode="chooser"] .zpbsServiceList .zpbsServiceShot{
    top:-92px!important;
  }
}
@media (max-width:390px){
  html body #zpbsUltimate .zpbsServiceList .zpbsServiceShot,
  #zpbsUltimate.zp630Entrance[data-mode="chooser"] .zpbsServiceList .zpbsServiceShot{
    top:-84px!important;
  }
}

/* Pełna / bardzo szeroka rozdzielczość: pierwsze 2 zdjęcia 100 px wyżej */
@media (min-width:1700px){
  html body #zpbsUltimate .zpbsServiceList .zpbsService[data-service="web"] .zpbsServiceShot,
  html body #zpbsUltimate .zpbsServiceList .zpbsService[data-service="shop"] .zpbsServiceShot{
    top:-381px!important;
  }
}
</style>
<style id="zp-suite-front-fixes-298-v2661-mobile-lower-fullres-individual-raise">
/* v2.2.661 — mobile lower 30px, full resolution individual raise for first 2 cards */

/* Mobile i mniejsze rozdzielczości: wszystkie zdjęcia 30 px niżej */
@media (min-width:761px) and (max-width:1180px){
  html body #zpbsUltimate .zpbsServiceList .zpbsServiceShot,
  #zpbsUltimate.zp630Entrance[data-mode="chooser"] .zpbsServiceList .zpbsServiceShot{
    top:-104px!important;
  }
}
@media (max-width:760px){
  html body #zpbsUltimate .zpbsServiceList .zpbsServiceShot,
  #zpbsUltimate.zp630Entrance[data-mode="chooser"] .zpbsServiceList .zpbsServiceShot{
    top:-92px!important;
  }
}
@media (max-width:390px){
  html body #zpbsUltimate .zpbsServiceList .zpbsServiceShot,
  #zpbsUltimate.zp630Entrance[data-mode="chooser"] .zpbsServiceList .zpbsServiceShot{
    top:-84px!important;
  }
}

/* Pełna / bardzo szeroka rozdzielczość: 1. zdjęcie +50 px wyżej, 2. zdjęcie +20 px wyżej */
@media (min-width:1700px){
  html body #zpbsUltimate .zpbsServiceList .zpbsService[data-service="web"] .zpbsServiceShot{
    top:-431px!important;
  }
  html body #zpbsUltimate .zpbsServiceList .zpbsService[data-service="shop"] .zpbsServiceShot{
    top:-401px!important;
  }
}
</style>
<style id="zp-suite-front-fixes-298-v2662-mobile40-fullres-brand-fix">
/* v2.2.662 — mobile images 40px lower, full-res web +20 higher, brand +10% and +20 higher */

/* Mobile i mniejsze rozdzielczości: wszystkie zdjęcia 40 px niżej */
@media (min-width:761px) and (max-width:1180px){
  html body #zpbsUltimate .zpbsServiceList .zpbsServiceShot,
  #zpbsUltimate.zp630Entrance[data-mode="chooser"] .zpbsServiceList .zpbsServiceShot{
    top:-94px!important;
  }
}
@media (max-width:760px){
  html body #zpbsUltimate .zpbsServiceList .zpbsServiceShot,
  #zpbsUltimate.zp630Entrance[data-mode="chooser"] .zpbsServiceList .zpbsServiceShot{
    top:-82px!important;
  }
}
@media (max-width:390px){
  html body #zpbsUltimate .zpbsServiceList .zpbsServiceShot,
  #zpbsUltimate.zp630Entrance[data-mode="chooser"] .zpbsServiceList .zpbsServiceShot{
    top:-74px!important;
  }
}

/* Pełna / bardzo szeroka rozdzielczość */
@media (min-width:1700px){
  /* 1. zdjęcie jeszcze 20 px wyżej */
  html body #zpbsUltimate .zpbsServiceList .zpbsService[data-service="web"] .zpbsServiceShot{
    top:-451px!important;
  }
  /* 2. zdjęcie bez zmian względem poprzedniej wersji */
  html body #zpbsUltimate .zpbsServiceList .zpbsService[data-service="shop"] .zpbsServiceShot{
    top:-401px!important;
  }
  /* 3. zdjęcie (Logo i branding) +10% i 20 px wyżej */
  html body #zpbsUltimate .zpbsServiceList .zpbsService[data-service="brand"]{
    padding-right:390px!important;
  }
  html body #zpbsUltimate .zpbsServiceList .zpbsService[data-service="brand"] .zpbsServiceShot{
    width:496px!important;
    height:531px!important;
    top:-202px!important;
    right:6px!important;
  }
}
</style>
<style id="zp-suite-front-fixes-298-v2663-width1620-raise-first-two-cards">
/* v2.2.663 — around 1620 px raise first two card images by ~90 px */
@media (min-width:1560px) and (max-width:1699px){
  html body #zpbsUltimate .zpbsServiceList .zpbsService[data-service="web"] .zpbsServiceShot,
  html body #zpbsUltimate .zpbsServiceList .zpbsService[data-service="shop"] .zpbsServiceShot{
    top:-471px!important;
  }
}
</style>
<style id="zp-suite-front-fixes-298-v2664-heading-descender-fix">
/* v2.2.664 — nagłówek „Twój projekt?” bez obcinania dolnych części liter */
html body #zpbsUltimate .zpbsChoose__copy,
html body #zpbsUltimate .zpbsChoose h1,
html body #zpbsUltimate #zpbsUTitle,
#zpbsUltimate.zp630Entrance[data-mode="chooser"] .zpbsChoose h1{
  height:auto!important;
  min-height:0!important;
  line-height:1.10!important;
  padding-bottom:.20em!important;
  overflow:visible!important;
  contain:none!important;
  clip-path:none!important;
  -webkit-clip-path:none!important;
  mask-image:none!important;
  -webkit-mask-image:none!important;
}
html body #zpbsUltimate #zpbsUTitle .zpbsH1Lite,
html body #zpbsUltimate #zpbsUTitle .zpbsH1Bold{
  line-height:inherit!important;
  padding-bottom:.10em!important;
  overflow:visible!important;
  clip-path:none!important;
  -webkit-clip-path:none!important;
  mask-image:none!important;
  -webkit-mask-image:none!important;
  -webkit-box-decoration-break:clone!important;
  box-decoration-break:clone!important;
}
</style>
<style id="zp-suite-front-fixes-298-v2665-mobile-images-lower-brand-larger">
/* v2.2.665 — telefon: wszystkie zdjęcia 20 px niżej, Logo i branding +10% */
@media (max-width:760px){
  html body #zpbsUltimate .zpbsServiceList .zpbsServiceShot,
  #zpbsUltimate.zp630Entrance[data-mode="chooser"] .zpbsServiceList .zpbsServiceShot{
    top:-62px!important;
  }

  html body #zpbsUltimate .zpbsServiceList .zpbsService[data-service="brand"] .zpbsServiceShot img{
    scale:1.32!important;
    transform-origin:center bottom!important;
  }
}

@media (max-width:390px){
  html body #zpbsUltimate .zpbsServiceList .zpbsServiceShot,
  #zpbsUltimate.zp630Entrance[data-mode="chooser"] .zpbsServiceList .zpbsServiceShot{
    top:-54px!important;
  }
}
</style>
<script id="zp-suite-front-fixes-298-studio-entrance-person-js">
(function(){
  function init(){
    var root=document.getElementById('zpbsUltimate');
    if(!root || root.dataset.zp630Ready==='1') return;
    root.dataset.zp630Ready='1';

    function runChooser(){
      if(root.getAttribute('data-mode')!=='chooser') return;
      root.classList.remove('zp630Entrance');
      requestAnimationFrame(function(){
        requestAnimationFrame(function(){ root.classList.add('zp630Entrance'); });
      });
    }

    var trust=root.querySelector('.zpbsTrustDark');
    if(trust){
      var showTrust=function(){ trust.classList.add('zp630TrustEntrance'); };
      if('IntersectionObserver' in window){
        var io=new IntersectionObserver(function(entries){
          entries.forEach(function(entry){
            if(entry.isIntersecting){ showTrust(); io.disconnect(); }
          });
        },{threshold:.16,rootMargin:'0px 0px -7% 0px'});
        io.observe(trust);
      }else{ showTrust(); }
    }

    runChooser();
    try{
      new MutationObserver(function(mutations){
        for(var i=0;i<mutations.length;i++){
          if(mutations[i].attributeName==='data-mode' && root.getAttribute('data-mode')==='chooser'){
            runChooser();
            break;
          }
        }
      }).observe(root,{attributes:true,attributeFilter:['data-mode']});
    }catch(e){}
  }
  if(document.readyState==='loading') document.addEventListener('DOMContentLoaded',init,{once:true});
  else init();
})();
</script>
  <?php
}
add_action('wp_footer','zp_suite_298_studio_entrance_and_person',PHP_INT_MAX);
