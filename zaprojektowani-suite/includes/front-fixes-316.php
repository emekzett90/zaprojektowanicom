<?php
/**
 * ZP Suite v2.2.801 — home hero motion/glass polish and smoother mobile header divider entrance.
 */
if (!defined('ABSPATH')) { exit; }

if (!function_exists('zp_suite_316_home_hero_polish_css')) {
  function zp_suite_316_home_hero_polish_css() {
    if (is_admin()) { return; }
    ?>
<style id="zp-suite-316-home-hero-polish">
/* ===== Home hero motion / readability ===== */
@media (prefers-reduced-motion:no-preference){
  @keyframes zpHomeRobotMobileFloat{
    0%{transform:translate3d(0,0,0) rotate(0deg) scale(1)}
    35%{transform:translate3d(-4px,-8px,0) rotate(.55deg) scale(1.008)}
    68%{transform:translate3d(3px,-3px,0) rotate(-.35deg) scale(1.004)}
    100%{transform:translate3d(1px,4px,0) rotate(.2deg) scale(.998)}
  }
  @keyframes zpCrewFloatA{
    0%{transform:translate3d(0,0,0) scale(.99)}
    50%{transform:translate3d(0,-8px,0) scale(1)}
    100%{transform:translate3d(0,4px,0) scale(.992)}
  }
  @keyframes zpCrewFloatB{
    0%{transform:translate3d(0,3px,0) scale(.94)}
    50%{transform:translate3d(0,-6px,0) scale(.948)}
    100%{transform:translate3d(0,5px,0) scale(.942)}
  }
  @keyframes zpCrewFloatC{
    0%{transform:translate3d(0,1px,0) scale(.99)}
    50%{transform:translate3d(0,-7px,0) scale(1)}
    100%{transform:translate3d(0,4px,0) scale(.992)}
  }
}

@media (max-width:1100px){
  html body.home #zhHero .zh__poster,
  html body.front-page #zhHero .zh__poster,
  html body.home #zhHero .zh__spline,
  html body.front-page #zhHero .zh__spline{
    transform-origin:72% 54%!important;
    will-change:transform!important;
  }
  @media (prefers-reduced-motion:no-preference){
    html body.home #zhHero .zh__poster,
    html body.front-page #zhHero .zh__poster,
    html body.home #zhHero .zh__spline,
    html body.front-page #zhHero .zh__spline{
      animation:zpHomeRobotMobileFloat 9.5s ease-in-out 1.1s infinite alternate;
    }
  }

  /* Softer, glassier mobile opinion cards — less color, more translucent glass. */
  html body.home #zhHero .zh__rev,
  html body.front-page #zhHero .zh__rev{
    background:linear-gradient(180deg,rgba(255,255,255,.12),rgba(255,255,255,.03)) border-box, rgba(6,11,20,.30)!important;
    background-color:rgba(6,11,20,.30)!important;
    border-color:rgba(255,255,255,.12)!important;
    box-shadow:0 18px 44px rgba(0,0,0,.18), inset 0 1px 0 rgba(255,255,255,.12)!important;
    -webkit-backdrop-filter:blur(18px) saturate(118%)!important;
    backdrop-filter:blur(18px) saturate(118%)!important;
  }
  html body.home #zhHero .zh__rev::before,
  html body.front-page #zhHero .zh__rev::before{
    background:linear-gradient(145deg,rgba(255,255,255,.20),rgba(255,255,255,.05) 24%,rgba(255,255,255,.03) 58%,rgba(255,255,255,.12) 100%)!important;
    opacity:.68!important;
  }
  html body.home #zhHero .zh__rl,
  html body.front-page #zhHero .zh__rl{color:rgba(255,255,255,.64)!important}
  html body.home #zhHero .zh__rt span,
  html body.front-page #zhHero .zh__rt span{color:rgba(255,255,255,.58)!important}
}

@media (min-width:1101px){
  /* Hand/arm darkening under the title for stronger text contrast on desktop. */
  html body.home #zhHero .zh__fade::before,
  html body.front-page #zhHero .zh__fade::before{
    content:"";
    position:absolute;
    inset:0;
    z-index:1;
    pointer-events:none;
    background:
      radial-gradient(30% 24% at 49% 40%, rgba(1,3,9,.56) 0%, rgba(1,3,9,.48) 34%, rgba(1,3,9,.22) 58%, rgba(1,3,9,0) 82%),
      radial-gradient(20% 18% at 56% 56%, rgba(1,3,9,.36) 0%, rgba(1,3,9,.26) 44%, rgba(1,3,9,0) 78%);
  }

  /* Delicate floating of the people on desktop. */
  html body.home #zhHero .zh__crew,
  html body.front-page #zhHero .zh__crew{will-change:transform}
  @media (prefers-reduced-motion:no-preference){
    html body.home #zhHero .zh__crewImg--1,
    html body.front-page #zhHero .zh__crewImg--1{animation:zpCrewFloatA 6.8s ease-in-out .15s infinite alternate!important}
    html body.home #zhHero .zh__crewImg--2,
    html body.front-page #zhHero .zh__crewImg--2{animation:zpCrewFloatB 7.4s ease-in-out .35s infinite alternate!important}
    html body.home #zhHero .zh__crewImg--3,
    html body.front-page #zhHero .zh__crewImg--3{animation:zpCrewFloatC 7s ease-in-out .55s infinite alternate!important}
  }
}

/* ===== Mobile header: divider enters with the rest of the header ===== */
@media (max-width:1100px){
  html body #zpNewNav .zpHeaderStaticDivider,
  html body #zpNewNav .zpNewNav__mobileCall,
  html body #zpNewNav .zpl-switch--chip,
  html body #zpNewNav .zpNewNav__mobileBrand,
  html body #zpNewNav .zpNewNav__burger{
    transition:opacity .42s cubic-bezier(.16,1,.3,1),transform .52s cubic-bezier(.16,1,.3,1),visibility 0s linear!important;
    will-change:opacity,transform!important;
  }

  html.zp-mobile-ui-pending body #zpNewNav .zpHeaderStaticDivider,
  html:not(.zp-mobile-header-ready) body #zpNewNav .zpHeaderStaticDivider{
    opacity:0!important;
    visibility:hidden!important;
    transform:translate3d(0,-6px,0) scaleX(.86)!important;
  }
  html.zp-mobile-header-ready body #zpNewNav .zpHeaderStaticDivider{
    opacity:.52!important;
    visibility:visible!important;
    transform:translate3d(0,0,0) scaleX(1)!important;
    transition-delay:.12s!important;
  }

  html.zp-mobile-ui-pending body #zpNewNav .zpNewNav__mobileCall,
  html:not(.zp-mobile-header-ready) body #zpNewNav .zpNewNav__mobileCall{
    opacity:0!important;transform:translate3d(-14px,-8px,0)!important;
  }
  html.zp-mobile-ui-pending body #zpNewNav .zpl-switch--chip,
  html:not(.zp-mobile-header-ready) body #zpNewNav .zpl-switch--chip{
    opacity:0!important;transform:translate3d(0,-10px,0)!important;
  }
  html.zp-mobile-ui-pending body #zpNewNav .zpNewNav__mobileBrand,
  html:not(.zp-mobile-header-ready) body #zpNewNav .zpNewNav__mobileBrand{
    opacity:0!important;transform:translate3d(-50%,-58%,0)!important;
  }
  html.zp-mobile-ui-pending body #zpNewNav .zpNewNav__burger,
  html:not(.zp-mobile-header-ready) body #zpNewNav .zpNewNav__burger{
    opacity:0!important;transform:translate3d(14px,-8px,0)!important;
  }

  html.zp-mobile-header-ready body #zpNewNav .zpNewNav__mobileCall,
  html.zp-mobile-header-ready body #zpNewNav .zpl-switch--chip,
  html.zp-mobile-header-ready body #zpNewNav .zpNewNav__burger{
    opacity:1!important;visibility:visible!important;
  }
  html.zp-mobile-header-ready body #zpNewNav .zpNewNav__mobileCall{transform:translate3d(0,0,0)!important;transition-delay:.03s!important}
  html.zp-mobile-header-ready body #zpNewNav .zpl-switch--chip{transform:translate3d(0,0,0)!important;transition-delay:.10s!important}
  html.zp-mobile-header-ready body #zpNewNav .zpNewNav__mobileBrand{opacity:1!important;visibility:visible!important;transform:translate3d(-50%,-50%,0)!important;transition-delay:.16s!important}
  html.zp-mobile-header-ready body #zpNewNav .zpNewNav__burger{transform:translate3d(0,0,0)!important;transition-delay:.22s!important}
}

@media (prefers-reduced-motion:reduce){
  html body.home #zhHero .zh__poster,
  html body.front-page #zhHero .zh__poster,
  html body.home #zhHero .zh__spline,
  html body.front-page #zhHero .zh__spline,
  html body.home #zhHero .zh__crewImg,
  html body.front-page #zhHero .zh__crewImg{animation:none!important}
}
</style>
    <?php
  }
}
add_action('wp_head', 'zp_suite_316_home_hero_polish_css', PHP_INT_MAX);
