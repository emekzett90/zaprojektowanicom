<?php
if (!defined('ABSPATH')) {
  exit;
}

function zp_suite_asset_version($relative_path) {
  $file = ZP_SUITE_PATH . ltrim($relative_path, '/');
  return file_exists($file) ? (string) filemtime($file) : ZP_SUITE_VERSION;
}

/**
 * v2.2.524 — ultra-early service first-paint marker.
 * Body classes are too late for the very first CSS match on some Elementor/service pages,
 * so this tiny synchronous head script marks <html> by URL before critical header CSS paints.
 */
add_action('wp_head', function(){
  if (is_admin()) {
    return;
  }
  ?>
<script id="zp-service-firstpaint-marker">(function(d,p,h){try{h=d.documentElement;p=(location.pathname||'').replace(/\/+$/,'/');if(p==='/'||p===''){h.classList.add('zp-clean-hero-fp');h.classList.add('zp-home-hero-fp');h.setAttribute('data-zp-path','home');}if(/\/(strony-internetowe-katowice|tworzenie-stron-internetowych|sklepy-internetowe-katowice|tworzenie-sklepow-internetowych|logo-branding-katowice|projektowanie-logo|identyfikacja-wizualna|kampanie-reklamowe)\//.test(p)){h.classList.add('zp-service-hero-fp');h.classList.add('zp-clean-hero-fp');if(/\/(strony-internetowe-katowice|tworzenie-stron-internetowych)\//.test(p)){h.classList.add('zp-strony-katowice-fp');h.setAttribute('data-zp-path','strony-internetowe-katowice');}if(/\/(sklepy-internetowe-katowice|tworzenie-sklepow-internetowych)\//.test(p)){h.classList.add('zp-sklepy-katowice-fp');h.setAttribute('data-zp-path','sklepy-internetowe-katowice');}if(/\/(logo-branding-katowice|projektowanie-logo|identyfikacja-wizualna)\//.test(p)){h.classList.add('zp-logo-branding-fp');h.setAttribute('data-zp-path','logo-branding-katowice');}if(p.indexOf('/kampanie-reklamowe/')>-1){h.classList.add('zp-kampanie-fp');h.setAttribute('data-zp-path','kampanie-reklamowe');}}if(/\/(kontakt|wiedza)\//.test(p)){h.classList.add('zp-clean-hero-fp');}}catch(e){}})(document);</script>
  <?php
}, 0);

function zp_suite_header_critical_css() {
  return <<<'CSS'
/* ZP Suite Critical Header CSS — prevents FOUC before full header.css is loaded */
html,body{background:#05070b!important;font-family:var(--zp-font)!important;}body.home,body.front-page{background:#05070b!important;}:root{--zp-font:"Plus Jakarta Sans Local",system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;--zp-ink:#0f1116;--zp-line:rgba(10,18,32,.075)}
.zpNewNav,.zpNewNav *,.zpNewNav *:before,.zpNewNav *:after{box-sizing:border-box}
.zpNewNav{position:fixed!important;top:0!important;left:0!important;right:auto!important;z-index:999900!important;width:100vw!important;max-width:100vw!important;margin:0!important;font-family:var(--zp-font)!important;color:var(--zp-ink);background:transparent;isolation:isolate;overflow:visible!important;-webkit-font-smoothing:antialiased;text-rendering:geometricPrecision}
.admin-bar .zpNewNav{top:32px!important}@media(max-width:782px){.admin-bar .zpNewNav{top:46px!important}}
.zpNewNav a{text-decoration:none;color:inherit}.zpNewNav svg{display:block;width:1em;height:1em;stroke-width:1.85}.zpNewNav [hidden]{display:none!important}
/* v2.2.552: reserve Lucide icon boxes before JS replaces <i> with <svg>; prevents desktop header micro-shift */
.zpNewNav i[data-lucide]{display:inline-block!important;width:1em!important;height:1em!important;min-width:1em!important;line-height:1!important;vertical-align:middle!important;overflow:hidden!important;color:currentColor!important;stroke:currentColor!important}
.zpNewNav__caret[data-lucide],.zpNewNav__link i[data-lucide].zpNewNav__caret{display:inline-block!important;width:16px!important;height:16px!important;min-width:16px!important;vertical-align:-3px!important}
.zpNewNav__contactIcon i[data-lucide]{display:block!important;width:22px!important;height:22px!important;min-width:22px!important}
.zpNewNav__cta i[data-lucide]{display:block!important;width:16px!important;height:16px!important;min-width:16px!important}
.zpNewNav__shell{position:relative;z-index:999901;width:100%;background:rgba(255,255,255,.985);border-bottom:0!important;overflow:visible;box-shadow:none}
.zpNewNav__inner{width:auto;max-width:none;min-height:88px;margin:0 50px;display:grid;grid-template-columns:auto minmax(0,1fr) auto;align-items:center;gap:82px}
.zpNewNav__brand{display:flex;align-items:center;min-width:0}.zpNewNav__logo{display:block;height:var(--zp-admin-logo-desktop-h,36px)!important;width:auto!important;max-width:244px!important;object-fit:contain}.zpNewNav__logo--dark{display:none!important}
.zpNewNav__desktop{display:flex;align-items:center;justify-content:center;min-width:0}.zpNewNav__menu{display:flex;align-items:center;justify-content:center;gap:8px;list-style:none;margin:0;padding:0}.zpNewNav__item{position:relative;display:flex;align-items:center}.zpNewNav__item[data-mega]{position:static}.zpNewNav__link{position:relative;display:inline-flex;align-items:center;gap:8px;min-height:48px;padding:0 18px;color:var(--zp-ink);font-size:16px;line-height:1;font-weight:700;letter-spacing:-.022em;white-space:nowrap;background:transparent!important;border:0!important;box-shadow:none!important}.zpNewNav__caret{width:14px;height:14px;opacity:.74}
.zpNewNav__actions{display:flex;align-items:center;justify-content:flex-end;gap:10px}.zpNewNav__round{width:44px;height:44px;border-radius:999px;display:grid;place-items:center;border:1px solid rgba(10,18,32,.13);background:#fff;color:var(--zp-ink);padding:0}.zpNewNav__round svg{width:18px;height:18px}.zpNewNav__divider{width:1px;height:32px;background:rgba(10,18,32,.04);margin:0 7px}.zpNewNav__cta{display:inline-flex;align-items:center;justify-content:center;gap:9px;min-height:46px;padding:0 24px;border-radius:999px;border:1px solid rgba(16,42,79,.72);color:#10223d;background:#fff;font-size:15px;font-weight:700;letter-spacing:-.01em;white-space:nowrap}.zpNewNav__cta svg{width:16px;height:16px}
.zpNewNav__mega,.zpNewNav__drop{visibility:hidden;opacity:0;pointer-events:none}.zpNewNav__item.is-open>.zpNewNav__mega,.zpNewNav__item.is-open>.zpNewNav__drop{visibility:visible;opacity:1;pointer-events:auto}
.zpNewNav__mobileBar,.zpNewNav__mobileOverlay,.zpNewNav__mobilePanel{display:none}
.zpNewNav__searchTrigger,.zpNewNav__divider--preContact,.zpNewNav__mobileSearch{display:none!important}
.zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__shell,html body .zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__shell{background:transparent!important;border-bottom-color:transparent!important;box-shadow:none!important;backdrop-filter:none!important;-webkit-backdrop-filter:none!important}
.zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__inner{min-height:104px!important}
.zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__link,.zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__round,.zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__mobileSearch,.zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__burger{color:#fff!important}
.zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__logo--light{display:block!important;opacity:1!important}.zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__logo--dark{display:none!important;opacity:0!important}
.zpNewNav__cta,.zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__cta{background:#fff!important;color:#07101e!important;border:1px solid rgba(255,255,255,.88)!important;box-shadow:none!important}


/* v2.2.509 critical: no white first-paint bar on hero pages + no static/sticky height jump */
html body.home .zpNewNav:not(.is-scrolled):not(.is-mega-open) .zpNewNav__shell,html body.front-page .zpNewNav:not(.is-scrolled):not(.is-mega-open) .zpNewNav__shell{background:transparent!important;background-image:none!important;box-shadow:none!important;backdrop-filter:none!important;-webkit-backdrop-filter:none!important}
@media(min-width:1101px){.zpNewNav__inner,.zpNewNav__brand,.zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__inner,.zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__brand{min-height:88px!important;height:88px!important}.zpNewNav__link,.zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__link{min-height:88px!important}}
.zpNewNav__cta,.zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__cta{background:#fff!important;color:#071426!important;border:1px solid rgba(255,255,255,.32)!important;box-shadow:none!important}

/* LOGO BRANDING HEADER NO-FLASH CRITICAL */
html body.zp-logo-branding-page:not(.zp-nav-scrolled) .zpNewNav__shell,html body.zp-logo-branding-shortcode:not(.zp-nav-scrolled) .zpNewNav__shell{background:transparent!important;box-shadow:none!important;border-bottom-color:transparent!important;backdrop-filter:none!important;-webkit-backdrop-filter:none!important}
html body.zp-logo-branding-page:not(.zp-nav-scrolled) .zpNewNav__link,html body.zp-logo-branding-shortcode:not(.zp-nav-scrolled) .zpNewNav__link,html body.zp-logo-branding-page:not(.zp-nav-scrolled) .zpNewNav__round,html body.zp-logo-branding-shortcode:not(.zp-nav-scrolled) .zpNewNav__round,html body.zp-logo-branding-page:not(.zp-nav-scrolled) .zpNewNav__mobileSearch,html body.zp-logo-branding-shortcode:not(.zp-nav-scrolled) .zpNewNav__mobileSearch,html body.zp-logo-branding-page:not(.zp-nav-scrolled) .zpNewNav__burger,html body.zp-logo-branding-shortcode:not(.zp-nav-scrolled) .zpNewNav__burger{color:#fff!important}
@media(max-width:1080px){.zpNewNav__inner{display:none}.zpNewNav__mobileBar{display:flex;align-items:center;justify-content:space-between;width:min(1450px,calc(100% - 32px));min-height:78px;margin:0 auto}.zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__mobileBar{min-height:82px}.zpNewNav__mobileLogo{height:var(--zp-admin-logo-mobile-h,32px)!important;width:auto!important;max-width:176px!important;object-fit:contain}.zpNewNav__mobileLogo--dark{position:absolute;left:0;top:50%;transform:translateY(-50%) translateX(var(--zp-admin-logo-mobile-x,0px));opacity:1}.zpNewNav__mobileLogo--light{transform:translateX(var(--zp-admin-logo-mobile-x,0px));opacity:0}.zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__mobileLogo--light{opacity:1!important}.zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__mobileLogo--dark{opacity:0!important}.zpNewNav__mobileActions{display:flex;align-items:center;gap:18px}.zpNewNav__mobileSearch,.zpNewNav__burger{width:46px;height:46px;border:0!important;background:transparent!important;color:#101217!important;display:grid;place-items:center;cursor:pointer;box-shadow:none!important;outline:none!important;padding:0;border-radius:0!important}.zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__mobileSearch,.zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__burger{color:#fff!important}.zpNewNav__mobileSearch svg,.zpNewNav__burger svg{width:31px;height:31px;stroke-width:1.8}}
/* v2.2.510 critical: stable mobile logo + persistent divider before full CSS */
.zpHeaderStaticDivider{display:block!important;position:absolute!important;left:50%!important;bottom:0!important;width:min(1850px,calc(100vw - 32px))!important;height:1px!important;transform:translateX(-50%)!important;pointer-events:none!important;z-index:5!important;opacity:.32!important;background:linear-gradient(90deg,transparent 0%,rgba(7,20,38,.12) 18%,rgba(7,20,38,.14) 50%,rgba(7,20,38,.12) 82%,transparent 100%)!important}
.zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled) .zpHeaderStaticDivider,body.home .zpNewNav:not(.is-scrolled) .zpHeaderStaticDivider,body.front-page .zpNewNav:not(.is-scrolled) .zpHeaderStaticDivider{opacity:.52!important;background:linear-gradient(90deg,transparent 0%,rgba(255,255,255,.10) 18%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.10) 82%,transparent 100%)!important;transition:none!important}
@media(min-width:1101px){
html body.zp-service-hero-page #zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__shell,
html body.zp-strony-katowice-page #zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__shell,
html body.zp-sklepy-katowice-page #zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__shell,
html body.zp-logo-branding-page #zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__shell{background:transparent!important;background-image:none!important;border-bottom:0!important;box-shadow:none!important;filter:none!important;backdrop-filter:none!important;-webkit-backdrop-filter:none!important}
html body.zp-service-hero-page #zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpHeaderStaticDivider,
html body.zp-strony-katowice-page #zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpHeaderStaticDivider,
html body.zp-sklepy-katowice-page #zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpHeaderStaticDivider,
html body.zp-logo-branding-page #zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpHeaderStaticDivider{display:block!important;opacity:.52!important;background:linear-gradient(90deg,transparent 0%,rgba(255,255,255,.10) 18%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.10) 82%,transparent 100%)!important;transition:none!important}
html.zp-service-hero-fp #zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpHeaderStaticDivider,html.zp-strony-katowice-fp #zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpHeaderStaticDivider,html.zp-sklepy-katowice-fp #zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpHeaderStaticDivider,html.zp-logo-branding-fp #zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpHeaderStaticDivider{display:block!important;opacity:.52!important;background:linear-gradient(90deg,transparent 0%,rgba(255,255,255,.10) 18%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.10) 82%,transparent 100%)!important;transition:none!important}
}
@media(max-width:1080px){.zpNewNav__mobileBar,.zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__mobileBar{height:74px!important;min-height:74px!important;max-height:74px!important;position:relative!important}.zpNewNav__mobileBrand{position:absolute!important;left:50%!important;top:50%!important;width:76px!important;height:58px!important;transform:translate3d(-50%,-50%,0)!important;display:flex!important;align-items:center!important;justify-content:center!important}.zpNewNav__mobileLogo,.zpNewNav__mobileLogo--dark,.zpNewNav__mobileLogo--light{position:absolute!important;left:50%!important;top:50%!important;height:44px!important;max-height:44px!important;max-width:62px!important;width:auto!important;object-fit:contain!important;transform:translate3d(-50%,-50%,0)!important}}

/* v2.2.523 critical: service pages get final dark/transparent header on first paint */
html.zp-service-hero-fp body:not(.zp-nav-scrolled) .zpNewNav:not(.is-scrolled):not(.is-mega-open) .zpNewNav__shell,
html.zp-strony-katowice-fp body:not(.zp-nav-scrolled) .zpNewNav:not(.is-scrolled):not(.is-mega-open) .zpNewNav__shell,
html.zp-sklepy-katowice-fp body:not(.zp-nav-scrolled) .zpNewNav:not(.is-scrolled):not(.is-mega-open) .zpNewNav__shell,
html.zp-logo-branding-fp body:not(.zp-nav-scrolled) .zpNewNav:not(.is-scrolled):not(.is-mega-open) .zpNewNav__shell,
html body.zp-service-hero-page:not(.zp-nav-scrolled) .zpNewNav:not(.is-scrolled):not(.is-mega-open) .zpNewNav__shell,
html body.zp-strony-katowice-page:not(.zp-nav-scrolled) .zpNewNav:not(.is-scrolled):not(.is-mega-open) .zpNewNav__shell,
html body.zp-sklepy-katowice-page:not(.zp-nav-scrolled) .zpNewNav:not(.is-scrolled):not(.is-mega-open) .zpNewNav__shell,
html body.zp-logo-branding-page:not(.zp-nav-scrolled) .zpNewNav:not(.is-scrolled):not(.is-mega-open) .zpNewNav__shell{
  background:transparent!important;background-image:none!important;box-shadow:none!important;border-bottom-color:transparent!important;backdrop-filter:none!important;-webkit-backdrop-filter:none!important;
}
html.zp-service-hero-fp body:not(.zp-nav-scrolled) .zpNewNav:not(.is-scrolled):not(.is-mega-open) .zpNewNav__link,
html.zp-service-hero-fp body:not(.zp-nav-scrolled) .zpNewNav:not(.is-scrolled):not(.is-mega-open) .zpNewNav__caret,
html.zp-service-hero-fp body:not(.zp-nav-scrolled) .zpNewNav:not(.is-scrolled):not(.is-mega-open) .zpNewNav__contactIcon,
html.zp-service-hero-fp body:not(.zp-nav-scrolled) .zpNewNav:not(.is-scrolled):not(.is-mega-open) .zpNewNav__round,
html.zp-service-hero-fp body:not(.zp-nav-scrolled) .zpNewNav:not(.is-scrolled):not(.is-mega-open) .zpNewNav__burger,
html.zp-service-hero-fp body:not(.zp-nav-scrolled) .zpNewNav:not(.is-scrolled):not(.is-mega-open) .zpNewNav__mobileSearch,
html body.zp-service-hero-page:not(.zp-nav-scrolled) .zpNewNav:not(.is-scrolled):not(.is-mega-open) .zpNewNav__link,
html body.zp-service-hero-page:not(.zp-nav-scrolled) .zpNewNav:not(.is-scrolled):not(.is-mega-open) .zpNewNav__caret,
html body.zp-service-hero-page:not(.zp-nav-scrolled) .zpNewNav:not(.is-scrolled):not(.is-mega-open) .zpNewNav__contactIcon,
html body.zp-service-hero-page:not(.zp-nav-scrolled) .zpNewNav:not(.is-scrolled):not(.is-mega-open) .zpNewNav__round,
html body.zp-service-hero-page:not(.zp-nav-scrolled) .zpNewNav:not(.is-scrolled):not(.is-mega-open) .zpNewNav__burger,
html body.zp-service-hero-page:not(.zp-nav-scrolled) .zpNewNav:not(.is-scrolled):not(.is-mega-open) .zpNewNav__mobileSearch{
  color:#fff!important;
}
html.zp-service-hero-fp body:not(.zp-nav-scrolled) .zpNewNav:not(.is-scrolled):not(.is-mega-open) .zpNewNav__logo--light,
html.zp-service-hero-fp body:not(.zp-nav-scrolled) .zpNewNav:not(.is-scrolled):not(.is-mega-open) .zpNewNav__mobileLogo--light,
html body.zp-service-hero-page:not(.zp-nav-scrolled) .zpNewNav:not(.is-scrolled):not(.is-mega-open) .zpNewNav__logo--light,
html body.zp-service-hero-page:not(.zp-nav-scrolled) .zpNewNav:not(.is-scrolled):not(.is-mega-open) .zpNewNav__mobileLogo--light{display:block!important;opacity:1!important;visibility:visible!important}
html.zp-service-hero-fp body:not(.zp-nav-scrolled) .zpNewNav:not(.is-scrolled):not(.is-mega-open) .zpNewNav__logo--dark,
html.zp-service-hero-fp body:not(.zp-nav-scrolled) .zpNewNav:not(.is-scrolled):not(.is-mega-open) .zpNewNav__mobileLogo--dark,
html body.zp-service-hero-page:not(.zp-nav-scrolled) .zpNewNav:not(.is-scrolled):not(.is-mega-open) .zpNewNav__logo--dark,
html body.zp-service-hero-page:not(.zp-nav-scrolled) .zpNewNav:not(.is-scrolled):not(.is-mega-open) .zpNewNav__mobileLogo--dark{display:none!important;opacity:0!important;visibility:hidden!important}
/* v2.2.556: removed desktop /strony first-paint header hard-lock.
   The generic service first-paint marker now supplies the transparent hero header state;
   this keeps the contact-like header entrance and avoids a colored bar / second divider. */
html.zp-strony-katowice-fp #zpWebHeroKat h1,
html.zp-strony-katowice-fp #zpWebHeroKat h1 *,
html.zp-strony-katowice-fp #zpWebHeroKat .zpH1Emphasis{filter:none!important;animation:none!important;transition:none!important;opacity:1!important;transform:none!important;will-change:auto!important}
@media(max-width:1100px){
  html.zp-strony-katowice-fp body.zp-nav-scrolled #zpNewNav:not(.is-mega-open) .zpNewNav__shell,
  html.zp-strony-katowice-fp #zpNewNav.is-scrolled:not(.is-mega-open) .zpNewNav__shell{background:rgba(255,255,255,.94)!important;background-image:none!important;color:#071426!important;box-shadow:none!important;backdrop-filter:saturate(145%) blur(12px)!important;-webkit-backdrop-filter:saturate(145%) blur(12px)!important;}
  html.zp-strony-katowice-fp body.zp-nav-scrolled #zpNewNav:not(.is-mega-open) .zpNewNav__mobileLogo--light,
  html.zp-strony-katowice-fp #zpNewNav.is-scrolled:not(.is-mega-open) .zpNewNav__mobileLogo--light{display:block!important;opacity:0!important;visibility:hidden!important;}
  html.zp-strony-katowice-fp body.zp-nav-scrolled #zpNewNav:not(.is-mega-open) .zpNewNav__mobileLogo--dark,
  html.zp-strony-katowice-fp #zpNewNav.is-scrolled:not(.is-mega-open) .zpNewNav__mobileLogo--dark{display:block!important;opacity:1!important;visibility:visible!important;filter:none!important;}
  html.zp-strony-katowice-fp body.zp-nav-scrolled #zpNewNav:not(.is-mega-open) .zpNewNav__burger,
  html.zp-strony-katowice-fp body.zp-nav-scrolled #zpNewNav:not(.is-mega-open) .zpNewNav__burger svg,
  html.zp-strony-katowice-fp #zpNewNav.is-scrolled:not(.is-mega-open) .zpNewNav__burger,
  html.zp-strony-katowice-fp #zpNewNav.is-scrolled:not(.is-mega-open) .zpNewNav__burger svg{color:#071426!important;stroke:#071426!important;}
}


/* v2.2.556 — one service divider + transparent service header from first paint on desktop. */
@media(min-width:1101px){
html.zp-service-hero-fp #zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open),
html.zp-service-hero-fp #zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__shell,
html body.zp-service-hero-page #zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open),
html body.zp-service-hero-page #zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__shell,
html body.zp-strony-katowice-page #zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open),
html body.zp-strony-katowice-page #zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__shell,
html body.zp-sklepy-katowice-page #zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open),
html body.zp-sklepy-katowice-page #zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__shell,
html body.zp-logo-branding-page #zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open),
html body.zp-logo-branding-page #zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__shell{background:transparent!important;background-color:transparent!important;background-image:none!important;border-bottom:0!important;box-shadow:none!important;filter:none!important;backdrop-filter:none!important;-webkit-backdrop-filter:none!important}
html.zp-service-hero-fp #zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpHeaderStaticDivider,
html body.zp-service-hero-page #zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpHeaderStaticDivider,
html body.zp-strony-katowice-page #zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpHeaderStaticDivider,
html body.zp-sklepy-katowice-page #zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpHeaderStaticDivider,
html body.zp-logo-branding-page #zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpHeaderStaticDivider{display:block!important;opacity:.52!important;background:linear-gradient(90deg,transparent 0%,rgba(255,255,255,.10) 18%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.10) 82%,transparent 100%)!important;transition:none!important}
}


/* v2.2.559 — home uses the same single clean hero divider from first paint. */
@media(min-width:1101px){
  html.zp-home-hero-fp #zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open),
  html.zp-home-hero-fp #zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__shell,
  body.home #zpNewNav.zpNewNav--cleanHero.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open),
  body.home #zpNewNav.zpNewNav--cleanHero.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__shell,
  body.front-page #zpNewNav.zpNewNav--cleanHero.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open),
  body.front-page #zpNewNav.zpNewNav--cleanHero.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__shell{background:transparent!important;background-color:transparent!important;background-image:none!important;border-bottom:0!important;box-shadow:none!important;filter:none!important;backdrop-filter:none!important;-webkit-backdrop-filter:none!important;}
  html.zp-home-hero-fp #zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpHeaderStaticDivider,
  body.home #zpNewNav.zpNewNav--cleanHero.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpHeaderStaticDivider,
  body.front-page #zpNewNav.zpNewNav--cleanHero.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpHeaderStaticDivider{display:block!important;position:absolute!important;left:50%!important;bottom:0!important;width:min(1850px,calc(100vw - 32px))!important;height:1px!important;transform:translateX(-50%)!important;pointer-events:none!important;z-index:5!important;opacity:.52!important;background:linear-gradient(90deg,transparent 0%,rgba(255,255,255,.10) 18%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.10) 82%,transparent 100%)!important;transition:none!important;box-shadow:none!important;border:0!important;}
  html.zp-home-hero-fp #zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open)::before,
  html.zp-home-hero-fp #zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open)::after,
  html.zp-home-hero-fp #zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__shell::before,
  html.zp-home-hero-fp #zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__shell::after,
  body.home #zpNewNav.zpNewNav--cleanHero.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open)::before,
  body.home #zpNewNav.zpNewNav--cleanHero.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open)::after,
  body.home #zpNewNav.zpNewNav--cleanHero.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__shell::before,
  body.home #zpNewNav.zpNewNav--cleanHero.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__shell::after{content:none!important;display:none!important;opacity:0!important;background:none!important;border:0!important;box-shadow:none!important;}
}

CSS;
}

function zp_suite_block_assets_map() {
  return [
    'zp_header' => [
      'slug' => 'header',
      // Header CSS is intentionally inlined in templates/header.php to prevent FOUC.
      'css' => '',
      'js' => 'assets/js/blocks/header.js',
    ],
    'zp_footer' => [
      'slug' => 'footer',
      'css' => 'assets/css/blocks/footer.css',
      'js' => 'assets/js/blocks/footer.js',
    ],
    'zp_home_hero' => [
      'slug' => 'home-hero',
      // Robot hero v2.2.361 ships scoped inline CSS/JS in templates/home-hero.php.
      // The old .zpNewHero assets are intentionally not enqueued on home.
      'css' => '',
      'js' => '',
    ],
    'zp_trust_logos' => [
      'slug' => 'trust-logos',
      'css' => 'assets/css/blocks/trust-logos.css',
      'js' => 'assets/js/blocks/trust-logos.js',
    ],
    'zp_services_path' => [
      'slug' => 'services-path',
      'css' => 'assets/css/blocks/services-path.css',
      'js' => 'assets/js/blocks/services-path.js',
    ],
    'zp_home_featured_packages' => [
      'slug' => 'home-featured-packages',
      'css' => 'assets/css/blocks/home-featured-packages.css',
      'js' => 'assets/js/blocks/home-featured-packages.js',
    ],
    'zp_mobile_dark_cta' => [
      'slug' => 'mobile-dark-cta',
      'css' => 'assets/css/blocks/mobile-dark-cta.css',
      'js' => 'assets/js/blocks/mobile-dark-cta.js',
    ],
    'zp_laptop_showcase' => [
      'slug' => 'laptop-showcase',
      'css' => 'assets/css/blocks/laptop-showcase.css',
      'js' => 'assets/js/blocks/laptop-showcase.js',
    ],
    'zp_about_experience' => [
      'slug' => 'about-experience',
      'css' => '',
      'js' => '',
    ],
    'zp_showcase_services' => [
      'slug' => 'showcase-services',
      // v1.8.19: wygląd home chroniony przez nieładowanie CSS podstrony Katowice na [zp_home_full].
      'css' => 'assets/css/blocks/showcase-services.css',
      'js' => 'assets/js/blocks/showcase-services.js',
    ],
    'zp_seo_industries' => [
      'slug' => 'footer',
      'css' => 'assets/css/blocks/footer.css',
      'js' => 'assets/js/blocks/footer.js',
    ],
    'zp_seo_faq' => [
      'slug' => 'footer',
      'css' => 'assets/css/blocks/footer.css',
      'js' => 'assets/js/blocks/footer.js',
    ],
    'zp_contact_system' => [
      'slug' => 'contact-page-form',
      // Formularz /kontakt/ ma kompletne style i JS inline z templates/contact-page/form.html.
      'css' => '',
      'js' => '',
    ],
    'zp_home_contact_form' => [
      'slug' => 'contact-page-form',
      // Ten sam formularz co /kontakt/, bez ładowania starego contact-system.
      'css' => '',
      'js' => '',
    ],
    'zp_strony_internetowe_katowice' => [
      'slug' => 'strony-katowice',
      'css' => 'assets/css/blocks/strony-katowice.css',
      'js' => 'assets/js/blocks/strony-katowice.js',
    ],
    'zp_page_strony_katowice' => [
      'slug' => 'strony-katowice',
      'css' => 'assets/css/blocks/strony-katowice.css',
      'js' => 'assets/js/blocks/strony-katowice.js',
    ],

    'zp_sklepy_internetowe_katowice' => [
      'slug' => 'sklepy-katowice',
      'css' => 'assets/css/blocks/sklepy-katowice.css',
      'js' => 'assets/js/blocks/sklepy-katowice.js',
    ],
    'zp_page_sklepy_katowice' => [
      'slug' => 'sklepy-katowice',
      'css' => 'assets/css/blocks/sklepy-katowice.css',
      'js' => 'assets/js/blocks/sklepy-katowice.js',
    ],


    /* v2.2.688 — nowa podstrona Strony internetowe Katowice (body.html 1:1 z prototypu).
       Osobny tag niż stary 'zp_strony_internetowe_katowice', żeby nie ruszać
       rejestracji starego renderera sekcyjnego. */
    'zp_strony_internetowe_katowice_v2' => [
      'slug' => 'strony-internetowe-katowice',
      'css' => 'assets/css/blocks/strony-internetowe-katowice.css',
      'js' => 'assets/js/blocks/strony-internetowe-katowice.js',
    ],

    'zp_logo_branding_katowice' => [
      'slug' => 'logo-branding-katowice',
      'css' => 'assets/css/blocks/logo-branding-katowice.css',
      'js' => 'assets/js/blocks/logo-branding-katowice.js',
    ],
    'zp_page_logo_branding_katowice' => [
      'slug' => 'logo-branding-katowice',
      'css' => 'assets/css/blocks/logo-branding-katowice.css',
      'js' => 'assets/js/blocks/logo-branding-katowice.js',
    ],

    'zp_realizacje' => [
      'slug' => 'realizacje-page',
      'css' => 'assets/css/blocks/realizacje-page.css',
      'js' => 'assets/js/blocks/realizacje-page.js',
    ],
    'zp_page_realizacje' => [
      'slug' => 'realizacje-page',
      'css' => 'assets/css/blocks/realizacje-page.css',
      'js' => 'assets/js/blocks/realizacje-page.js',
    ],

    'zp_faq_page' => [
      'slug' => 'faq-page',
      'css' => 'assets/css/blocks/faq-page.css',
      'js' => 'assets/js/blocks/faq-page.js',
    ],
    'zp_page_faq' => [
      'slug' => 'faq-page',
      'css' => 'assets/css/blocks/faq-page.css',
      'js' => 'assets/js/blocks/faq-page.js',
    ],

  ];
}

function zp_suite_enqueue_global_assets() {
  wp_enqueue_style(
    'zp-suite-global',
    ZP_SUITE_URL . 'assets/css/zp-global.css',
    [],
    zp_suite_asset_version('assets/css/zp-global.css')
  );

  // v2.2.538: mobile sticky CTA CSS is irrelevant for desktop Lighthouse and was render-blocking.
  // Keep it for mobile/tablet only.
  // A shared page cache must deliver the same stylesheet links to every device.
  if (file_exists(ZP_SUITE_PATH . 'assets/css/blocks/mobile-sticky-cta.css')) {
    wp_enqueue_style(
      'zp-suite-mobile-sticky-cta',
      ZP_SUITE_URL . 'assets/css/blocks/mobile-sticky-cta.css',
      ['zp-suite-global'],
      zp_suite_asset_version('assets/css/blocks/mobile-sticky-cta.css'),
      '(max-width: 760px)'
    );
  }

  $zp_lucide_rel = 'assets/js/vendor/lucide.min.js';
  $zp_lucide_src = file_exists(ZP_SUITE_PATH . $zp_lucide_rel)
    ? ZP_SUITE_URL . $zp_lucide_rel
    : content_url('/web-font/lucide.min.js');
  $zp_lucide_ver = file_exists(ZP_SUITE_PATH . $zp_lucide_rel)
    ? zp_suite_asset_version($zp_lucide_rel)
    : ZP_SUITE_VERSION;

  wp_enqueue_script(
    'zp-suite-lucide',
    $zp_lucide_src,
    [],
    $zp_lucide_ver,
    true
  );

  wp_enqueue_script(
    'zp-suite-global',
    ZP_SUITE_URL . 'assets/js/zp-global.js',
    ['zp-suite-lucide'],
    zp_suite_asset_version('assets/js/zp-global.js'),
    true
  );

  if (file_exists(ZP_SUITE_PATH . 'assets/css/zp-motion-lite.css')) {
    wp_enqueue_style(
      'zp-suite-motion-lite',
      ZP_SUITE_URL . 'assets/css/zp-motion-lite.css',
      ['zp-suite-global'],
      zp_suite_asset_version('assets/css/zp-motion-lite.css')
    );
    // Tiny layer-budget overrides stay inline, avoiding another blocking request.
    $motion_budget = ZP_SUITE_PATH . 'assets/css/motion-budget.css';
    if (is_readable($motion_budget)) {
      wp_add_inline_style('zp-suite-motion-lite', file_get_contents($motion_budget));
    }
  }

  if (file_exists(ZP_SUITE_PATH . 'assets/js/zp-motion-lite.js')) {
    wp_enqueue_script(
      'zp-suite-motion-lite',
      ZP_SUITE_URL . 'assets/js/zp-motion-lite.js',
      ['zp-suite-global'],
      zp_suite_asset_version('assets/js/zp-motion-lite.js'),
      true
    );
  }
}

function zp_suite_enqueue_block_assets($shortcode_tag) {
  $map = zp_suite_block_assets_map();
  if (empty($map[$shortcode_tag])) {
    return;
  }

  zp_suite_enqueue_global_assets();

  $asset = $map[$shortcode_tag];
  $slug = $asset['slug'];

  if (!empty($asset['css']) && file_exists(ZP_SUITE_PATH . $asset['css'])) {
    wp_enqueue_style(
      'zp-suite-' . $slug,
      ZP_SUITE_URL . $asset['css'],
      ['zp-suite-global'],
      zp_suite_asset_version($asset['css'])
    );

    if ($shortcode_tag === 'zp_header') {
      wp_add_inline_style('zp-suite-' . $slug, zp_suite_header_critical_css());
    }
  }

  if (!empty($asset['js']) && file_exists(ZP_SUITE_PATH . $asset['js'])) {
    wp_enqueue_script(
      'zp-suite-' . $slug,
      ZP_SUITE_URL . $asset['js'],
      ['zp-suite-global'],
      zp_suite_asset_version($asset['js']),
      true
    );

    if ($shortcode_tag === 'zp_contact_system') {
      wp_localize_script('zp-suite-' . $slug, 'zpSuiteContact', [
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('zp_suite_contact'),
        'whatsapp' => zp_suite_opt('brand.whatsapp', '48501054253'),
      ]);
    }
  }
}

function zp_suite_enqueue_detected_shortcode_assets() {
  if (is_admin()) {
    return;
  }

  if (!is_singular()) {
    return;
  }

  global $post;
  if (!$post || empty($post->post_content)) {
    return;
  }

  if (has_shortcode($post->post_content, 'zp_home_full') || has_shortcode($post->post_content, 'zp_home')) {
    /*
     * The header must be registered before the large home bundle.  Several legacy
     * home blocks attach DOMContentLoaded handlers which otherwise run before the
     * header can protect the first mobile interaction from non-critical work.
     */
    zp_suite_enqueue_block_assets('zp_header');
    zp_suite_enqueue_home_full_assets();
    return;
  }

  foreach (zp_suite_block_assets_map() as $tag => $asset) {
    if (has_shortcode($post->post_content, $tag)) {
      zp_suite_enqueue_block_assets($tag);
      if ($tag === 'zp_sklepy_internetowe_katowice' || $tag === 'zp_page_sklepy_katowice' || $tag === 'zp_logo_branding_katowice' || $tag === 'zp_page_logo_branding_katowice') {
        zp_suite_enqueue_block_assets('zp_contact_system');
      }
    }
  }

  /**
   * Legacy portfolio/reviews still contain scoped inline CSS/JS in v0.2.
   * They use the same global Lucide/font assets, so load globals when detected.
   */
  if (has_shortcode($post->post_content, 'zp_showcase_portfolio') || has_shortcode($post->post_content, 'zp_reviews_section')) {
    zp_suite_enqueue_global_assets();
  }
}
add_action('wp_enqueue_scripts', 'zp_suite_enqueue_detected_shortcode_assets', 20);


/**
 * v2.2.505 — Header CSS delivery is now identical on ALL header pages:
 * print the small critical header CSS inline (instant first paint, no FOUC), then
 * load the full header.css as a cached external sheet asynchronously.
 *
 * Previously only the home page used this path; every other page inlined the full
 * ~570 KB header.css into the HTML on every request (assets.php + templates/header.php
 * fallback), uncached. The async <link> is emitted here at wp_head priority 1 — the
 * exact same DOM position the old inline <style> occupied — so the cascade order vs the
 * late header-override <style> blocks (priorities 999 / 100000 / PHP_INT_MAX) is
 * unchanged. Visual output is therefore 1:1; only the 570 KB leaves the document.
 */
add_action('wp_head', function(){
  if (is_admin()) {
    return;
  }

  $should_print = is_front_page() || is_home();

  if (!$should_print && is_singular()) {
    global $post;
    $content = $post && !empty($post->post_content) ? $post->post_content : '';
    $should_print = has_shortcode($content, 'zp_header') || has_shortcode($content, 'zp_home_full') || has_shortcode($content, 'zp_home');
  }

  if (!$should_print) {
    return;
  }

  $file = ZP_SUITE_PATH . 'assets/css/blocks/header.css';
  if (!file_exists($file)) {
    return;
  }

  $GLOBALS['zp_suite_header_css_printed_in_head'] = true;

  $href = add_query_arg(
    'ver',
    zp_suite_asset_version('assets/css/blocks/header.css'),
    ZP_SUITE_URL . 'assets/css/blocks/header.css'
  );

  echo "\n<style id=\"zp-suite-header-head-critical-css\">\n" . zp_suite_header_critical_css() . "\n
/* v2.2.560 — early mobile/tablet logo centering around 1030–1065px. */
@media(max-width:1100px){
  html body #zpNewNav .zpNewNav__mobileBar{position:relative!important;width:100%!important;max-width:none!important;margin:0!important;padding-left:clamp(22px,4.6vw,52px)!important;padding-right:clamp(22px,4.6vw,52px)!important;display:flex!important;align-items:center!important;justify-content:space-between!important;}
  html body #zpNewNav .zpNewNav__mobileBrand{position:absolute!important;left:50%!important;top:50%!important;right:auto!important;bottom:auto!important;width:76px!important;height:58px!important;min-width:76px!important;min-height:58px!important;max-width:76px!important;max-height:58px!important;margin:0!important;padding:0!important;display:flex!important;align-items:center!important;justify-content:center!important;transform:translate3d(-50%,-50%,0)!important;translate:none!important;}
  html body #zpNewNav .zpNewNav__mobileLogo,html body #zpNewNav .zpNewNav__mobileLogo--light,html body #zpNewNav .zpNewNav__mobileLogo--dark{position:absolute!important;left:50%!important;top:50%!important;right:auto!important;bottom:auto!important;width:auto!important;height:44px!important;min-height:44px!important;max-height:44px!important;max-width:62px!important;margin:0!important;padding:0!important;object-fit:contain!important;object-position:center!important;transform:translate3d(-50%,-50%,0)!important;translate:none!important;scale:1!important;filter:none!important;box-shadow:none!important;}
  html body #zpNewNav .zpNewNav__mobileActions{margin-left:auto!important;}
}

</style>\n";
  printf(
    "<link rel=\"preload\" as=\"style\" id=\"zp-suite-header-full-preload\" href=\"%s\">\n",
    esc_url($href)
  );

  /* v2.2.526 — header.css loading strategy by page.
     The async print-swap (media="print" onload media='all') downloads at the LOWEST
     priority. On the service landing pages — /strony-internetowe-katowice/ above all,
     which also ships a hero <video> + heavy mockups — the full header.css therefore
     applied late, so the header visibly "snapped" from the inline critical subset to
     its final styling a beat after paint ("oryginalna wersja z opóźnieniem"). A pure-CSS
     first-paint lock (v2.2.525) could not fix a *timing* problem.
     header.css is only ~9 KB gzipped and is preloaded right above, so loading it as a
     normal blocking sheet on these pages costs ~0 and removes the swap/delay entirely.
     Home uses the same stable path: its mobile drawer can be tapped before an async
     print-swap finishes, which otherwise causes a costly restyle during interaction. */
  $service_landing =
    zp_suite_service_kind() !== ''
    || is_page('logo-branding');
  if (!$service_landing && is_singular()) {
    global $post;
    $sc = $post && !empty($post->post_content) ? $post->post_content : '';
    $service_landing =
      has_shortcode($sc, 'zp_strony_internetowe_katowice')
      || has_shortcode($sc, 'zp_page_strony_katowice')
      || has_shortcode($sc, 'zp_katowice_page')
      || has_shortcode($sc, 'zp_sklepy_internetowe_katowice')
      || has_shortcode($sc, 'zp_page_sklepy_katowice')
      || has_shortcode($sc, 'zp_logo_branding_katowice')
      || has_shortcode($sc, 'zp_page_logo_branding_katowice');
  }

  /*
   * On home the first tap can happen while the print-swap stylesheet is still
   * applying.  Loading this small cached sheet normally keeps the drawer styles
   * stable from the first paint and avoids a large style recalculation mid-tap.
   */
  $sync_header = is_front_page() || is_home() || $service_landing;

  if ($sync_header) {
    printf(
      "<link rel=\"stylesheet\" id=\"zp-suite-header-full-css\" href=\"%s\" media=\"all\">\n",
      esc_url($href)
    );
  } else {
    printf(
      "<link rel=\"stylesheet\" id=\"zp-suite-header-full-css\" href=\"%s\" media=\"print\" onload=\"this.media='all';this.onload=null\">\n",
      esc_url($href)
    );
    printf(
      "<noscript><link rel=\"stylesheet\" href=\"%s\"></noscript>\n",
      esc_url($href)
    );
  }
}, 1);

/**
 * v2.2.551 — desktop service header no-FOUC lock.
 *
 * The service pages were painting the critical/fallback header first, then the full
 * header.css / footer micro-fixes changed desktop-only metrics a moment later
 * (menu gap, link padding, contact icon boxes, header divider opacity). On slower
 * pages like /strony-internetowe-katowice/ this looked like a second header load and
 * a ~5px left shift. This tiny head CSS uses the ultra-early <html> URL marker from
 * zp-service-firstpaint-marker and makes the first paint match the final service
 * header state. It is intentionally desktop-only and does not touch the mobile drawer.
 */
add_action('wp_head', function () {
  if (is_admin()) {
    return;
  }
  /* v2.2.553: disabled on desktop/service pages. The contact page header path is now the reference: no extra service-only hard lock. */
  return;
  ?>
<style id="zp-suite-2551-service-header-desktop-nofouc">
@media (min-width:1101px){
  html.zp-service-hero-fp #zpNewNav,
  html.zp-service-hero-fp body #zpNewNav,
  html body.zp-service-hero-page #zpNewNav,
  html body.zp-strony-katowice-page #zpNewNav,
  html body.zp-sklepy-katowice-page #zpNewNav,
  html body.zp-logo-branding-page #zpNewNav{
    position:fixed!important;top:var(--zp-admin-top,0px)!important;left:0!important;right:auto!important;width:100vw!important;max-width:100vw!important;margin:0!important;background:transparent!important;transform:none!important;translate:none!important;z-index:2147483000!important;
  }
  html.zp-service-hero-fp #zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__shell,
  html body.zp-service-hero-page #zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__shell,
  html body.zp-strony-katowice-page #zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__shell,
  html body.zp-sklepy-katowice-page #zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__shell,
  html body.zp-logo-branding-page #zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__shell{
    background:transparent!important;background-color:transparent!important;background-image:none!important;border-bottom:0!important;border-bottom-color:transparent!important;box-shadow:none!important;filter:none!important;backdrop-filter:none!important;-webkit-backdrop-filter:none!important;transition:none!important;
  }
  html.zp-service-hero-fp #zpNewNav .zpNewNav__inner,
  html body.zp-service-hero-page #zpNewNav .zpNewNav__inner,
  html body.zp-strony-katowice-page #zpNewNav .zpNewNav__inner,
  html body.zp-sklepy-katowice-page #zpNewNav .zpNewNav__inner,
  html body.zp-logo-branding-page #zpNewNav .zpNewNav__inner{
    display:grid!important;grid-template-columns:auto minmax(0,1fr) auto!important;align-items:center!important;width:auto!important;max-width:none!important;min-height:88px!important;height:88px!important;margin-left:50px!important;margin-right:50px!important;gap:82px!important;transform:none!important;translate:none!important;transition:none!important;
  }
  html.zp-service-hero-fp #zpNewNav .zpNewNav__brand,
  html body.zp-service-hero-page #zpNewNav .zpNewNav__brand,
  html body.zp-strony-katowice-page #zpNewNav .zpNewNav__brand,
  html body.zp-sklepy-katowice-page #zpNewNav .zpNewNav__brand,
  html body.zp-logo-branding-page #zpNewNav .zpNewNav__brand{
    display:flex!important;align-items:center!important;min-height:88px!important;height:88px!important;min-width:0!important;opacity:1!important;transform:none!important;translate:none!important;animation:none!important;transition:none!important;will-change:auto!important;
  }
  html.zp-service-hero-fp #zpNewNav .zpNewNav__desktop,
  html body.zp-service-hero-page #zpNewNav .zpNewNav__desktop,
  html body.zp-strony-katowice-page #zpNewNav .zpNewNav__desktop,
  html body.zp-sklepy-katowice-page #zpNewNav .zpNewNav__desktop,
  html body.zp-logo-branding-page #zpNewNav .zpNewNav__desktop{
    display:flex!important;align-items:center!important;justify-content:center!important;min-width:0!important;
  }
  html.zp-service-hero-fp #zpNewNav .zpNewNav__menu,
  html body.zp-service-hero-page #zpNewNav .zpNewNav__menu,
  html body.zp-strony-katowice-page #zpNewNav .zpNewNav__menu,
  html body.zp-sklepy-katowice-page #zpNewNav .zpNewNav__menu,
  html body.zp-logo-branding-page #zpNewNav .zpNewNav__menu{
    display:flex!important;align-items:center!important;justify-content:center!important;gap:1.25vw!important;list-style:none!important;margin:0!important;padding:0!important;
  }
  html.zp-service-hero-fp #zpNewNav .zpNewNav__desktop .zpNewNav__item,
  html body.zp-service-hero-page #zpNewNav .zpNewNav__desktop .zpNewNav__item,
  html body.zp-strony-katowice-page #zpNewNav .zpNewNav__desktop .zpNewNav__item,
  html body.zp-sklepy-katowice-page #zpNewNav .zpNewNav__desktop .zpNewNav__item,
  html body.zp-logo-branding-page #zpNewNav .zpNewNav__desktop .zpNewNav__item{
    position:relative!important;display:flex!important;align-items:center!important;opacity:1!important;transform:none!important;translate:none!important;animation:none!important;transition:none!important;will-change:auto!important;
  }
  html.zp-service-hero-fp #zpNewNav .zpNewNav__desktop .zpNewNav__item[data-mega],
  html body.zp-service-hero-page #zpNewNav .zpNewNav__desktop .zpNewNav__item[data-mega],
  html body.zp-strony-katowice-page #zpNewNav .zpNewNav__desktop .zpNewNav__item[data-mega],
  html body.zp-sklepy-katowice-page #zpNewNav .zpNewNav__desktop .zpNewNav__item[data-mega],
  html body.zp-logo-branding-page #zpNewNav .zpNewNav__desktop .zpNewNav__item[data-mega]{position:static!important;}
  html.zp-service-hero-fp #zpNewNav .zpNewNav__link,
  html body.zp-service-hero-page #zpNewNav .zpNewNav__link,
  html body.zp-strony-katowice-page #zpNewNav .zpNewNav__link,
  html body.zp-sklepy-katowice-page #zpNewNav .zpNewNav__link,
  html body.zp-logo-branding-page #zpNewNav .zpNewNav__link{
    position:relative!important;display:inline-flex!important;align-items:center!important;gap:8px!important;min-height:88px!important;height:88px!important;padding:0 1.35vw!important;font-size:16px!important;line-height:16px!important;font-weight:700!important;letter-spacing:-.022em!important;white-space:nowrap!important;background:transparent!important;border:0!important;box-shadow:none!important;transform:none!important;translate:none!important;transition:none!important;
  }
  html.zp-service-hero-fp body:not(.zp-nav-scrolled) #zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__link,
  html.zp-service-hero-fp body:not(.zp-nav-scrolled) #zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__caret,
  html.zp-service-hero-fp body:not(.zp-nav-scrolled) #zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__contactIcon,
  html body.zp-service-hero-page:not(.zp-nav-scrolled) #zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__link,
  html body.zp-service-hero-page:not(.zp-nav-scrolled) #zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__caret,
  html body.zp-service-hero-page:not(.zp-nav-scrolled) #zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__contactIcon{
    color:#fff!important;stroke:#fff!important;
  }
  html.zp-service-hero-fp #zpNewNav .zpNewNav__caret,
  html body.zp-service-hero-page #zpNewNav .zpNewNav__caret,
  html body.zp-strony-katowice-page #zpNewNav .zpNewNav__caret,
  html body.zp-sklepy-katowice-page #zpNewNav .zpNewNav__caret,
  html body.zp-logo-branding-page #zpNewNav .zpNewNav__caret{
    display:inline-block!important;position:relative!important;width:16px!important;height:16px!important;min-width:16px!important;opacity:.74!important;vertical-align:-3px!important;transform:none!important;transition:none!important;
  }
  html.zp-service-hero-fp #zpNewNav .zpNewNav__actions,
  html body.zp-service-hero-page #zpNewNav .zpNewNav__actions,
  html body.zp-strony-katowice-page #zpNewNav .zpNewNav__actions,
  html body.zp-sklepy-katowice-page #zpNewNav .zpNewNav__actions,
  html body.zp-logo-branding-page #zpNewNav .zpNewNav__actions{
    display:flex!important;align-items:center!important;justify-content:flex-end!important;gap:8px!important;opacity:1!important;transform:none!important;translate:none!important;animation:none!important;transition:none!important;will-change:auto!important;
  }
  html.zp-service-hero-fp #zpNewNav .zpNewNav__actions>*,
  html body.zp-service-hero-page #zpNewNav .zpNewNav__actions>*,
  html body.zp-strony-katowice-page #zpNewNav .zpNewNav__actions>*,
  html body.zp-sklepy-katowice-page #zpNewNav .zpNewNav__actions>*,
  html body.zp-logo-branding-page #zpNewNav .zpNewNav__actions>*{
    opacity:1!important;transform:none!important;translate:none!important;animation:none!important;transition:none!important;will-change:auto!important;
  }
  html.zp-service-hero-fp #zpNewNav .zpNewNav__contactIcon,
  html body.zp-service-hero-page #zpNewNav .zpNewNav__contactIcon,
  html body.zp-strony-katowice-page #zpNewNav .zpNewNav__contactIcon,
  html body.zp-sklepy-katowice-page #zpNewNav .zpNewNav__contactIcon,
  html body.zp-logo-branding-page #zpNewNav .zpNewNav__contactIcon{
    display:inline-flex!important;align-items:center!important;justify-content:center!important;width:34px!important;min-width:34px!important;height:44px!important;min-height:44px!important;padding:0!important;border:0!important;background:transparent!important;box-shadow:none!important;cursor:pointer!important;transform:none!important;translate:none!important;transition:none!important;
  }
  html.zp-service-hero-fp #zpNewNav .zpNewNav__contactIcon svg,
  html body.zp-service-hero-page #zpNewNav .zpNewNav__contactIcon svg,
  html body.zp-strony-katowice-page #zpNewNav .zpNewNav__contactIcon svg,
  html body.zp-sklepy-katowice-page #zpNewNav .zpNewNav__contactIcon svg,
  html body.zp-logo-branding-page #zpNewNav .zpNewNav__contactIcon svg{
    width:22px!important;height:22px!important;stroke:currentColor!important;color:currentColor!important;fill:none!important;stroke-width:1.9!important;transform:none!important;transition:none!important;
  }
  html.zp-service-hero-fp #zpNewNav .zpNewNav__divider,
  html body.zp-service-hero-page #zpNewNav .zpNewNav__divider,
  html body.zp-strony-katowice-page #zpNewNav .zpNewNav__divider,
  html body.zp-sklepy-katowice-page #zpNewNav .zpNewNav__divider,
  html body.zp-logo-branding-page #zpNewNav .zpNewNav__divider{
    width:1px!important;height:32px!important;margin:0 7px!important;background:rgba(255,255,255,.086)!important;opacity:1!important;
  }
  html.zp-service-hero-fp #zpNewNav .zpHeaderStaticDivider,
  html body.zp-service-hero-page #zpNewNav .zpHeaderStaticDivider,
  html body.zp-strony-katowice-page #zpNewNav .zpHeaderStaticDivider,
  html body.zp-sklepy-katowice-page #zpNewNav .zpHeaderStaticDivider,
  html body.zp-logo-branding-page #zpNewNav .zpHeaderStaticDivider{
    display:block!important;position:absolute!important;left:50%!important;right:auto!important;bottom:0!important;width:min(var(--zp-header-line-width,1850px),calc(100vw - 32px))!important;height:1px!important;max-width:calc(100vw - 32px)!important;transform:translateX(-50%)!important;pointer-events:none!important;z-index:5!important;opacity:.26!important;background:linear-gradient(90deg,transparent 0%,rgba(255,255,255,.08) 18%,rgba(255,255,255,.12) 50%,rgba(255,255,255,.08) 82%,transparent 100%)!important;transition:none!important;
  }
  html.zp-service-hero-fp body.zp-nav-scrolled #zpNewNav .zpHeaderStaticDivider,
  html.zp-service-hero-fp #zpNewNav.is-scrolled .zpHeaderStaticDivider,
  html body.zp-service-hero-page.zp-nav-scrolled #zpNewNav .zpHeaderStaticDivider,
  html body.zp-service-hero-page #zpNewNav.is-scrolled .zpHeaderStaticDivider{
    opacity:.22!important;background:linear-gradient(90deg,transparent 0%,rgba(7,20,38,.08) 18%,rgba(7,20,38,.11) 50%,rgba(7,20,38,.08) 82%,transparent 100%)!important;
  }
}
</style>
  <?php
}, 2);

/**
 * ZP Suite v2.2.557 — final service divider, mobile hero float restore, and white post reading background.
 * - service desktop header: one divider tone from first paint via .zpNewNav--serviceHero
 * - mobile service heroes: restore gentle floating of mockups/chips after previous performance locks
 * - single blog posts: white reading surface under the dark hero only
 */
add_action('wp_head', function () {
  if (is_admin()) { return; }
  ?>
<style id="zp-suite-557-service-divider-mobile-floats-post-bg">
@media (min-width:1101px){
  #zpNewNav.zpNewNav--serviceHero,
  #zpNewNav.zpNewNav--serviceHero .zpNewNav__shell,
  html.zp-service-hero-fp #zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open),
  html.zp-service-hero-fp #zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__shell{
    background:transparent!important;background-color:transparent!important;background-image:none!important;
    border-bottom:0!important;box-shadow:none!important;filter:none!important;
    backdrop-filter:none!important;-webkit-backdrop-filter:none!important;
  }
  #zpNewNav.zpNewNav--serviceHero .zpHeaderStaticDivider,
  html.zp-service-hero-fp #zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpHeaderStaticDivider{
    display:block!important;position:absolute!important;left:50%!important;bottom:0!important;
    width:min(1850px,calc(100vw - 32px))!important;height:1px!important;
    transform:translateX(-50%)!important;opacity:.46!important;
    background:linear-gradient(90deg,transparent 0%,rgba(255,255,255,.08) 18%,rgba(255,255,255,.13) 50%,rgba(255,255,255,.08) 82%,transparent 100%)!important;
    transition:none!important;box-shadow:none!important;filter:none!important;
  }
  #zpNewNav.zpNewNav--serviceHero .zpNewNav__shell::before,
  #zpNewNav.zpNewNav--serviceHero .zpNewNav__shell::after,
  #zpNewNav.zpNewNav--serviceHero::before,
  #zpNewNav.zpNewNav--serviceHero::after{border:0!important;box-shadow:none!important;background:none!important;opacity:0!important;content:none!important;display:none!important;}
}
@media (max-width:760px) and (prefers-reduced-motion:no-preference){
  @keyframes zpSvcFloatSoft557{0%,100%{translate:0 0}50%{translate:0 -8px}}
  @keyframes zpSvcFloatTiny557{0%,100%{translate:0 0}50%{translate:0 -5px}}
  #zpWebHeroKat .zpWebHeroKat__mock,
  #zpShopHeroKat .zpShopCockpit__mockFrame,
  #zpBrandHeroKatSafe .zpBrandHeroSafe__mockFrame,
  #zpBrandHeroMobileKat .zpBrandHeroMobileKat__mock{
    animation:zpSvcFloatSoft557 6.8s ease-in-out infinite!important;
    will-change:translate!important;
  }
  #zpWebHeroKat .zpWebHeroKat__metric,
  #zpWebHeroKat .zpWebHeroKat__miniCard,
  #zpShopHeroKat .zpShopCockpit__pill,
  #zpShopHeroKat .zpShopCockpit__offerCard,
  #zpShopHeroKat .zpShopCockpit__wooLogo,
  #zpBrandHeroKatSafe .zpBrandHeroSafe__pill,
  #zpBrandHeroKatSafe .zpBrandHeroSafe__miniCard,
  #zpBrandHeroKatSafe .zpBrandHeroSafe__price,
  #zpBrandHeroMobileKat .zpBrandHeroMobileKat__floatPill,
  #zpBrandHeroMobileKat .zpBrandHeroMobileKat__miniCard{
    animation:zpSvcFloatTiny557 5.8s ease-in-out infinite!important;
    will-change:translate!important;
  }
  #zpWebHeroKat .zpWebHeroKat__miniCard,
  #zpShopHeroKat .zpShopCockpit__offerCard,
  #zpBrandHeroKatSafe .zpBrandHeroSafe__miniCard,
  #zpBrandHeroMobileKat .zpBrandHeroMobileKat__miniCard{animation-delay:.65s!important;}
}
body.single-post{background:#fff!important;}
body.single-post #page,
body.single-post .site,
body.single-post main,
body.single-post #content,
body.single-post .site-main,
body.single-post .entry-content,
body.single-post #zpKnowledgePro{background:#fff!important;}
body.single-post #zpKnowledgePro .zpKBHero,
body.single-post #zpKnowledgePro .zpKBHero *,
body.single-post .zpKBHero{color:inherit;}
body.single-post #zpKnowledgePro .zpKBHero{background-color:#05070b!important;}
body.single-post article,
body.single-post .elementor,
body.single-post .elementor-section,
body.single-post .elementor-widget-container{background-color:transparent;}
</style>
  <?php
}, 999);


/**
 * v2.2.561 — tablet crossover logo center + remove rating hover jitter early.
 */
add_action('wp_head', function(){
  if (is_admin()) { return; }
  echo "\n<style id=\"zp-suite-561-crossover-logo-rating-fix\">\n";
  echo '@media (min-width:981px) and (max-width:1100px){html body #zpNewNav .zpNewNav__mobileBar,html body #zpNewNav.zpNewNav--heroOverlay .zpNewNav__mobileBar,html body #zpNewNav.is-scrolled .zpNewNav__mobileBar,html body.zp-nav-scrolled #zpNewNav .zpNewNav__mobileBar{position:relative!important;width:100vw!important;max-width:100vw!important;margin-left:calc(50% - 50vw)!important;margin-right:calc(50% - 50vw)!important;padding-left:clamp(22px,4.6vw,52px)!important;padding-right:clamp(22px,4.6vw,52px)!important}html body #zpNewNav .zpNewNav__mobileBrand,html body #zpNewNav.zpNewNav--heroOverlay .zpNewNav__mobileBrand,html body #zpNewNav.is-scrolled .zpNewNav__mobileBrand,html body.zp-nav-scrolled #zpNewNav .zpNewNav__mobileBrand{position:fixed!important;left:50vw!important;top:37px!important;right:auto!important;bottom:auto!important;width:76px!important;min-width:76px!important;max-width:76px!important;height:58px!important;min-height:58px!important;max-height:58px!important;margin:0!important;padding:0!important;transform:translate3d(-50%,-50%,0)!important;translate:none!important;display:flex!important;align-items:center!important;justify-content:center!important}html body #zpNewNav .zpNewNav__mobileLogo,html body #zpNewNav .zpNewNav__mobileLogo--light,html body #zpNewNav .zpNewNav__mobileLogo--dark,html body #zpNewNav.zpNewNav--heroOverlay .zpNewNav__mobileLogo,html body #zpNewNav.is-scrolled .zpNewNav__mobileLogo,html body.zp-nav-scrolled #zpNewNav .zpNewNav__mobileLogo{position:absolute!important;left:50%!important;top:50%!important;transform:translate3d(-50%,-50%,0)!important;translate:none!important}}';
  /* v2.2.582: usunięto regułę hover dla .zh__sc strong (letter-spacing:inherit powodował skok
     kerningu na 5.0 przy hoverze). Ocena 5.0 jest teraz stylowana w jednym miejscu: home-hero.php. */
  echo "\n</style>\n";
}, 1000001);
