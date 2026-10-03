<?php
/**
 * ZP Suite v2.2.603 — Studio Wyceny / sekcja „Zaufali nam”:
 * statyczna, kompletna siatka logotypów bez animacji; korekty skali wybranych znaków.
 */
if (!defined('ABSPATH')) { exit; }

function zp_suite_297_studio_trust_polish(){
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
<style id="zp-suite-front-fixes-297-studio-trust-polish">
html body #zpbsUltimate,
html body #zpbsUltimate .zpbsTrustSlot,
html body #zpbsUltimate .zpbsTrustDark,
html body #zpbsUltimate .zpbsTrustDark__in{
  overflow:visible!important;
}
html body #zpbsUltimate .zpbsTrustDark{
  position:relative!important;
  z-index:2!important;
}
html body #zpbsUltimate .zpbsTrustDark__grid{
  position:relative!important;
  display:grid!important;
  grid-template-columns:repeat(6,minmax(0,1fr))!important;
  gap:0!important;
  overflow:hidden!important;
  border-top:1px solid rgba(255,255,255,.11)!important;
  border-bottom:1px solid rgba(255,255,255,.11)!important;
}
html body #zpbsUltimate .zpbsTrustDark__logo{
  position:relative!important;
  display:grid!important;
  place-items:center!important;
  min-width:0!important;
  width:auto!important;
  height:clamp(92px,7.4vw,112px)!important;
  border:0!important;
  border-radius:0!important;
  background:transparent!important;
  box-shadow:none!important;
  transform:none!important;
}
html body #zpbsUltimate .zpbsTrustDark__logo::after{
  content:""!important;
  position:absolute!important;
  top:18%!important;
  right:0!important;
  width:1px!important;
  height:64%!important;
  background:linear-gradient(180deg,transparent,rgba(255,255,255,.12) 24%,rgba(142,200,247,.16) 50%,rgba(255,255,255,.10) 76%,transparent)!important;
}
html body #zpbsUltimate .zpbsTrustDark__logo:nth-child(6n)::after{display:none!important}
html body #zpbsUltimate .zpbsTrustDark__logo:nth-child(n+7){border-top:1px solid rgba(255,255,255,.055)!important}
html body #zpbsUltimate .zpbsTrustDark__logo:hover{
  background:rgba(255,255,255,.022)!important;
  transform:none!important;
}
html body #zpbsUltimate .zpbsTrustDark__logo img{
  max-width:66%!important;
  max-height:46px!important;
  opacity:.78!important;
  transform:translateZ(0) scale(1)!important;
  transition:filter .28s ease,opacity .28s ease,transform .28s ease!important;
}
html body #zpbsUltimate .zpbsTrustDark__logo::before{
  content:""!important;
  position:absolute!important;
  inset:18px 14%!important;
  z-index:2!important;
  pointer-events:none!important;
  opacity:0!important;
  background:linear-gradient(115deg,#ffffff 4%,#dff4ff 34%,#8ed5ff 68%,#ffffff 96%)!important;
  -webkit-mask-image:var(--zpbs-logo-mask)!important;
  mask-image:var(--zpbs-logo-mask)!important;
  -webkit-mask-repeat:no-repeat!important;
  mask-repeat:no-repeat!important;
  -webkit-mask-position:center!important;
  mask-position:center!important;
  -webkit-mask-size:contain!important;
  mask-size:contain!important;
  transform:translateZ(0) scale(1)!important;
  transition:opacity .25s ease,transform .28s ease!important;
}
html body #zpbsUltimate .zpbsTrustDark__logo:hover img{
  opacity:0!important;
  transform:translateZ(0) scale(1.15)!important;
}
html body #zpbsUltimate .zpbsTrustDark__logo:hover::before{
  opacity:1!important;
  transform:translateZ(0) scale(1.15)!important;
}
html body #zpbsUltimate .zpbsTrustDark__logo--wide img{max-width:80%!important}
html body #zpbsUltimate .zpbsTrustDark__logo--tall img{max-height:64px!important}
/* Indywidualne korekty względem obecnego wyglądu. */
html body #zpbsUltimate .zpbsTrustDark__logo--siemianowski img{transform:translateZ(0) scale(.90)!important}
html body #zpbsUltimate .zpbsTrustDark__logo--raxo img{transform:translateZ(0) scale(.85)!important}
html body #zpbsUltimate .zpbsTrustDark__logo--gravia img{transform:translateZ(0) scale(.85)!important}
html body #zpbsUltimate .zpbsTrustDark__logo--siemianowski:hover img{transform:translateZ(0) scale(1.035)!important}
html body #zpbsUltimate .zpbsTrustDark__logo--raxo:hover img,
html body #zpbsUltimate .zpbsTrustDark__logo--gravia:hover img{transform:translateZ(0) scale(.9775)!important}

html body #zpbsUltimate .zpbsTrustDark__logo--siemianowski::before{transform:translateZ(0) scale(.90)!important}
html body #zpbsUltimate .zpbsTrustDark__logo--raxo::before,
html body #zpbsUltimate .zpbsTrustDark__logo--gravia::before{transform:translateZ(0) scale(.85)!important}
html body #zpbsUltimate .zpbsTrustDark__logo--siemianowski:hover::before{transform:translateZ(0) scale(1.035)!important}
html body #zpbsUltimate .zpbsTrustDark__logo--raxo:hover::before,
html body #zpbsUltimate .zpbsTrustDark__logo--gravia:hover::before{transform:translateZ(0) scale(.9775)!important}

@media (min-width:1200px){
  html body #zpbsUltimate .zpbsTrustDark__in{
    padding-right:clamp(385px,27vw,540px)!important;
  }
  html body #zpbsUltimate .zpbsTrustDark__person{
    display:block!important;
    top:clamp(-118px,-7vw,-84px)!important;
    right:clamp(-50px,-2vw,-14px)!important;
    bottom:-2px!important;
    height:calc(100% + clamp(92px,7vw,126px))!important;
    transform:scale(1.15)!important;
    transform-origin:right bottom!important;
    z-index:0!important;
    overflow:visible!important;
  }
  html body #zpbsUltimate .zpbsTrustDark__person img{
    height:100%!important;
    max-height:none!important;
    object-fit:contain!important;
    object-position:right bottom!important;
  }
}
@media (max-width:1100px){
  html body #zpbsUltimate .zpbsTrustDark__grid{grid-template-columns:repeat(4,minmax(0,1fr))!important}
  html body #zpbsUltimate .zpbsTrustDark__logo:nth-child(6n)::after{display:block!important}
  html body #zpbsUltimate .zpbsTrustDark__logo:nth-child(4n)::after{display:none!important}
  html body #zpbsUltimate .zpbsTrustDark__logo:nth-child(n+5){border-top:1px solid rgba(255,255,255,.055)!important}
}
@media (max-width:720px){
  html body #zpbsUltimate .zpbsTrustDark__grid{grid-template-columns:repeat(3,minmax(0,1fr))!important}
  html body #zpbsUltimate .zpbsTrustDark__logo{height:78px!important}
  html body #zpbsUltimate .zpbsTrustDark__logo img{max-width:74%!important;max-height:36px!important}
  html body #zpbsUltimate .zpbsTrustDark__logo--tall img{max-height:50px!important}
  html body #zpbsUltimate .zpbsTrustDark__logo:nth-child(4n)::after{display:block!important}
  html body #zpbsUltimate .zpbsTrustDark__logo:nth-child(3n)::after{display:none!important}
  html body #zpbsUltimate .zpbsTrustDark__logo:nth-child(n+4){border-top:1px solid rgba(255,255,255,.055)!important}
}
@media (max-width:420px){
  html body #zpbsUltimate .zpbsTrustDark__grid{grid-template-columns:repeat(2,minmax(0,1fr))!important}
  html body #zpbsUltimate .zpbsTrustDark__logo:nth-child(3n)::after{display:block!important}
  html body #zpbsUltimate .zpbsTrustDark__logo:nth-child(2n)::after{display:none!important}
  html body #zpbsUltimate .zpbsTrustDark__logo:nth-child(n+3){border-top:1px solid rgba(255,255,255,.055)!important}
}

/* v2.2.602 — finalne ustawienie sekcji względem kart oraz miękkie wtopienie fotografii. */
@media (min-width:1200px){
  html body #zpbsUltimate .zpbsTrustSlot{
    margin-top:140px!important;
  }
  html body #zpbsUltimate .zpbsTrustDark__person{
    top:clamp(-96px,-5.7vw,-68px)!important;
  }
  html body #zpbsUltimate .zpbsTrustDark__person::before{
    display:none!important;
    content:none!important;
  }
  html body #zpbsUltimate .zpbsTrustDark__person{
    /* Bez wtapiania po bokach — pełna szerokość sylwetki.
       Jedynie subtelne wygaszenie u dołu, aby fotografia naturalnie łączyła się z tłem. */
    -webkit-mask-image:linear-gradient(to bottom,#000 0%,#000 82%,rgba(0,0,0,.82) 89%,rgba(0,0,0,.26) 97%,transparent 100%)!important;
    mask-image:linear-gradient(to bottom,#000 0%,#000 82%,rgba(0,0,0,.82) 89%,rgba(0,0,0,.26) 97%,transparent 100%)!important;
    -webkit-mask-composite:initial!important;
    mask-composite:initial!important;
  }
}

/* v2.2.621 — twarde zakończenie fotografii równo z ciemnym widgetem,
   bez wysuwania nóg na białe tło; góra sylwetki nadal może wystawać. */
@media (min-width:1200px){
  html body #zpbsUltimate .zpbsTrustDark__person{
    bottom:0!important;
    clip-path:inset(-260px -140px 0 -140px)!important;
    -webkit-clip-path:inset(-260px -140px 0 -140px)!important;
    -webkit-mask-image:none!important;
    mask-image:none!important;
  }
}

/* v2.2.622 — ostateczne przycięcie dołu zdjęcia bez blokowania wyjścia górą.
   Nie skalujemy już całego figure, bo transform powiększał także jego dół poza widget.
   Powiększana jest wyłącznie fotografia wewnątrz ramki kończącej się równo z ciemnym tłem. */
@media (min-width:1200px){
  html body #zpbsUltimate .zpbsTrustDark__person{
    top:clamp(-96px,-5.7vw,-68px)!important;
    right:clamp(-50px,-2vw,-14px)!important;
    bottom:0!important;
    height:auto!important;
    transform:none!important;
    overflow:hidden!important;
    clip-path:none!important;
    -webkit-clip-path:none!important;
    -webkit-mask-image:none!important;
    mask-image:none!important;
  }
  html body #zpbsUltimate .zpbsTrustDark__person img{
    position:absolute!important;
    right:0!important;
    bottom:0!important;
    width:auto!important;
    height:115%!important;
    max-width:none!important;
    max-height:none!important;
    object-fit:contain!important;
    object-position:right bottom!important;
    transform:none!important;
  }
}


/* v2.2.623 — przycięcie całej warstwy trustu na dolnej krawędzi ciemnego tła.
   Slot może nadal przepuszczać sylwetkę górą, ale nic nie wyjdzie na białą sekcję poniżej. */
@media (min-width:1200px){
  html body #zpbsUltimate .zpbsTrustSlot{
    overflow:visible!important;
    clip-path:inset(-320px 0 0 0)!important;
    -webkit-clip-path:inset(-320px 0 0 0)!important;
  }
  html body #zpbsUltimate .zpbsTrustDark__person{
    bottom:0!important;
  }
}

/* v2.2.629 — stabilizacja po porównaniu z działającą 2.2.623.
   Nie zmieniamy szerokości choosera ani kolejności kolumn.
   Biała osłona znajduje się WYŁĄCZNIE pod dolną granicą ciemnego widgetu,
   więc nie przycina głowy ani lewej ręki Mateusza. */
@media (min-width:1200px){
  html body #zpbsUltimate .zpbsTrustSlot{
    isolation:isolate!important;
  }
  html body #zpbsUltimate .zpbsTrustSlot::after{
    content:""!important;
    position:absolute!important;
    left:0!important;
    right:0!important;
    top:100%!important;
    height:28px!important;
    background:#fff!important;
    z-index:40!important;
    pointer-events:none!important;
  }
}
</style>
  <?php
}
add_action('wp_footer','zp_suite_297_studio_trust_polish',PHP_INT_MAX);
