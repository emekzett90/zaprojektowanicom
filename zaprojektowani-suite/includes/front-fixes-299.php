<?php
/**
 * ZP Suite v2.2.666 — header responsywny w paśmie 981–1200px (finalna warstwa kaskady).
 *
 * Naprawiane błędy (zgłoszone screenami):
 * 1. 1101–1200px: pasek mobilny był włączany przez blok v562, ale bazowe style pigułki
 *    "Konsultacja" żyły tylko w @media(max-width:1100px) w header.css — przycisk
 *    renderował się jako goły link (ikona + tekst bez tła/obrysu).
 * 2. 981–1100px: front-fixes-237 (wp_footer) zamieniał .zpNewNav__mobileBrand z powrotem
 *    na relatywny slot flexa — przy asymetrycznych bokach (pigułka szersza niż burger)
 *    logo lądowało ~150px na prawo od środka.
 * 3. Studio Wyceny (chooser): biały header z klasą heroOverlay dostawał białą pigułkę
 *    na białym tle (heredoc v308 stylował nieistniejącą klasę .zpNewNav__consultBtn
 *    i tylko do 1080px) — przycisk był niewidoczny.
 *
 * Ta warstwa drukuje się na wp_footer z PHP_INT_MAX — wygrywa z 237/241 późniejszą
 * pozycją w dokumencie przy tej samej specyficzności. Metryki są 1:1 z blokiem
 * first-paint v666 w templates/header.php, więc nie ma przeskoku po załadowaniu.
 */
if (!defined('ABSPATH')) { exit; }

function zp_suite_299_header_responsive_band(){
  if (is_admin()) { return; }
  ?>
<style id="zp-suite-front-fixes-299-header-band">
@media (min-width:981px) and (max-width:1200px){
  html body header#zpNewNav .zpNewNav__shell .zpNewNav__inner,
  html body .zpNewNav#zpNewNav .zpNewNav__shell .zpNewNav__inner{display:none!important;visibility:hidden!important;height:0!important;min-height:0!important;max-height:0!important;overflow:hidden!important;}
  html body header#zpNewNav,
  html body header#zpNewNav .zpNewNav__shell{min-height:88px!important;}
  html body header#zpNewNav .zpNewNav__shell .zpNewNav__mobileBar,
  html body .zpNewNav#zpNewNav .zpNewNav__shell .zpNewNav__mobileBar,
  html body header#zpNewNav.zpNewNav--heroOverlay .zpNewNav__shell .zpNewNav__mobileBar,
  html body header#zpNewNav.is-scrolled .zpNewNav__shell .zpNewNav__mobileBar,
  html body.zp-nav-scrolled header#zpNewNav .zpNewNav__shell .zpNewNav__mobileBar{
    position:relative!important;display:flex!important;visibility:visible!important;opacity:1!important;
    width:min(1450px,calc(100vw - 2 * clamp(22px,4vw,42px)))!important;max-width:none!important;
    height:88px!important;min-height:88px!important;max-height:88px!important;
    margin:0 auto!important;padding:0!important;align-items:center!important;justify-content:space-between!important;gap:18px!important;
    transform:none!important;translate:none!important;
  }

  /* Pigułka "Konsultacja" — pełny styl w CAŁYM paśmie, nie tylko <=1100. */
  html body header#zpNewNav .zpNewNav__mobileBar .zpNewNav__mobileCall,
  html body .zpNewNav#zpNewNav .zpNewNav__mobileBar .zpNewNav__mobileCall{
    display:inline-flex!important;align-items:center!important;justify-content:center!important;gap:8px!important;
    height:46px!important;min-height:46px!important;max-height:46px!important;padding:0 18px!important;margin-left:0!important;
    border-radius:999px!important;border:1px solid rgba(7,20,38,.12)!important;
    background:rgba(255,255,255,.56)!important;color:#071426!important;
    font-size:13px!important;font-weight:720!important;letter-spacing:-.018em!important;line-height:1!important;white-space:nowrap!important;
    -webkit-backdrop-filter:blur(16px) saturate(130%)!important;backdrop-filter:blur(16px) saturate(130%)!important;
    box-shadow:none!important;transform:none!important;translate:none!important;flex:0 0 auto!important;
    opacity:1!important;visibility:visible!important;pointer-events:auto!important;
  }
  html body header#zpNewNav .zpNewNav__mobileBar .zpNewNav__mobileCall i,
  html body header#zpNewNav .zpNewNav__mobileBar .zpNewNav__mobileCall svg,
  html body .zpNewNav#zpNewNav .zpNewNav__mobileBar .zpNewNav__mobileCall i,
  html body .zpNewNav#zpNewNav .zpNewNav__mobileBar .zpNewNav__mobileCall svg{display:block!important;width:15px!important;min-width:15px!important;height:15px!important;min-height:15px!important;stroke:currentColor!important;fill:none!important;opacity:1!important;visibility:visible!important;transform:none!important;flex:0 0 15px!important;}
  html body header#zpNewNav .zpNewNav__mobileBar .zpNewNav__mobileCall span,
  html body .zpNewNav#zpNewNav .zpNewNav__mobileBar .zpNewNav__mobileCall span{display:inline-block!important;opacity:1!important;visibility:visible!important;transform:none!important;}

  /* Ciemne hero (home / wiedza / kontakt / strony usługowe): jasna pigułka na granacie. */
  html body header#zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled) .zpNewNav__mobileBar .zpNewNav__mobileCall,
  html body .zpNewNav#zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled) .zpNewNav__mobileBar .zpNewNav__mobileCall{
    color:#fff!important;background:rgba(255,255,255,.05)!important;border-color:rgba(255,255,255,.22)!important;
  }

  /* Akcje po prawej + burger. */
  html body header#zpNewNav .zpNewNav__mobileBar .zpNewNav__mobileActions,
  html body .zpNewNav#zpNewNav .zpNewNav__mobileBar .zpNewNav__mobileActions{margin-left:0!important;display:flex!important;align-items:center!important;justify-content:flex-end!important;gap:16px!important;flex:0 0 auto!important;opacity:1!important;visibility:visible!important;}
  html body header#zpNewNav .zpNewNav__mobileBar .zpNewNav__burger,
  html body .zpNewNav#zpNewNav .zpNewNav__mobileBar .zpNewNav__burger{width:44px!important;min-width:44px!important;height:44px!important;min-height:44px!important;margin-right:0!important;display:grid!important;place-items:center!important;border:0!important;background:transparent!important;box-shadow:none!important;padding:0!important;color:currentColor!important;flex:0 0 44px!important;transform:none!important;translate:none!important;opacity:1!important;visibility:visible!important;}

  /* Brand: ZAWSZE absolutnie wycentrowany względem paska — wygrywa z front-fixes-237,
     który robił z niego relatywny slot flexa (logo uciekało w prawo). */
  html body header#zpNewNav .zpNewNav__mobileBar .zpNewNav__mobileBrand,
  html body .zpNewNav#zpNewNav .zpNewNav__mobileBar .zpNewNav__mobileBrand,
  html body header#zpNewNav.zpNewNav--heroOverlay .zpNewNav__mobileBar .zpNewNav__mobileBrand,
  html body header#zpNewNav.is-scrolled .zpNewNav__mobileBar .zpNewNav__mobileBrand,
  html body.zp-nav-scrolled header#zpNewNav .zpNewNav__mobileBar .zpNewNav__mobileBrand{
    position:absolute!important;left:50%!important;top:50%!important;right:auto!important;bottom:auto!important;
    width:76px!important;height:58px!important;min-width:76px!important;min-height:58px!important;max-width:76px!important;max-height:58px!important;
    margin:0!important;padding:0!important;display:flex!important;align-items:center!important;justify-content:center!important;
    flex:0 0 auto!important;overflow:visible!important;line-height:0!important;
    transform:translate3d(-50%,-50%,0)!important;translate:none!important;transition:none!important;
    opacity:1!important;visibility:visible!important;
  }
  html body header#zpNewNav .zpNewNav__mobileBar .zpNewNav__mobileLogo,
  html body header#zpNewNav .zpNewNav__mobileBar .zpNewNav__mobileLogo--light,
  html body header#zpNewNav .zpNewNav__mobileBar .zpNewNav__mobileLogo--dark,
  html body .zpNewNav#zpNewNav .zpNewNav__mobileBar .zpNewNav__mobileLogo,
  html body header#zpNewNav.zpNewNav--heroOverlay .zpNewNav__mobileBar .zpNewNav__mobileLogo,
  html body header#zpNewNav.is-scrolled .zpNewNav__mobileBar .zpNewNav__mobileLogo,
  html body.zp-nav-scrolled header#zpNewNav .zpNewNav__mobileBar .zpNewNav__mobileLogo{
    position:absolute!important;left:50%!important;top:50%!important;right:auto!important;bottom:auto!important;
    width:auto!important;height:44px!important;min-height:44px!important;max-height:44px!important;max-width:62px!important;
    margin:0!important;padding:0!important;object-fit:contain!important;object-position:center!important;
    transform:translate3d(-50%,-50%,0)!important;translate:none!important;scale:1!important;filter:none!important;box-shadow:none!important;transition:opacity .16s ease!important;
  }
}

/* Studio Wyceny (chooser/brief): heroOverlay na białym tle — pigułka zawsze ciemna,
   niezależnie od szerokości paska mobilnego (obejmuje też <=980px). */
@media (max-width:1200px){
  html body.zpbs-studio-page header#zpNewNav .zpNewNav__mobileBar .zpNewNav__mobileCall,
  html body.zpbs-studio-page .zpNewNav#zpNewNav .zpNewNav__mobileBar .zpNewNav__mobileCall,
  html body header#zpNewNav.zpNewNav--studioChooser .zpNewNav__mobileBar .zpNewNav__mobileCall,
  html body .zpNewNav#zpNewNav.zpNewNav--studioChooser .zpNewNav__mobileBar .zpNewNav__mobileCall{
    color:#071426!important;background:rgba(255,255,255,.62)!important;border-color:rgba(7,17,31,.12)!important;box-shadow:none!important;
  }
  html body.zpbs-studio-page header#zpNewNav .zpNewNav__mobileBar .zpNewNav__mobileCall svg,
  html body header#zpNewNav.zpNewNav--studioChooser .zpNewNav__mobileBar .zpNewNav__mobileCall svg{stroke:#071426!important;}
}
</style>
  <?php
}
add_action('wp_footer','zp_suite_299_header_responsive_band',PHP_INT_MAX);
