<?php
/**
 * ZP Suite v2.2.563 — /strony-internetowe-katowice portfolio mockups refresh.
 * Replaces the old right-side portfolio images with transparent industry mockups
 * and adds a subtle premium floating motion, scoped only to the portfolio cards.
 */
if (!defined('ABSPATH')) { exit; }

function zp_suite_288_strony_portfolio_floating_mockups_css(){
  if (is_admin()) { return; }
  if (function_exists('zp_suite_2232_is_strony_katowice_request') && !zp_suite_2232_is_strony_katowice_request()) { return; }
  ?>
  <style id="zp-suite-288-strony-portfolio-floating-mockups-v563">
    html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--portfolio:not(.zpSSCard--portfolioCta),
    html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--portfolio:not(.zpSSCard--portfolioCta) .zpSSCard__content,
    html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--portfolio:not(.zpSSCard--portfolioCta) .zpSSCard__visual{
      overflow:visible!important;
      contain:none!important;
    }
    html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--portfolio:not(.zpSSCard--portfolioCta) .zpSSCard__visual{
      min-height:clamp(430px,36vw,650px)!important;
      isolation:isolate!important;
    }
    html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--portfolio:not(.zpSSCard--portfolioCta) .zpSSMockAura,
    html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--portfolio:not(.zpSSCard--portfolioCta) .zpSSMockIcon{
      display:none!important;
      opacity:0!important;
      visibility:hidden!important;
    }
    html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--portfolio:not(.zpSSCard--portfolioCta) .zpSSCard__mock--portfolio{
      inset:-10% -20% -16% -4%!important;
      z-index:8!important;
      overflow:visible!important;
      background:transparent!important;
      border:0!important;
      box-shadow:none!important;
      opacity:1!important;
      transform-origin:center center!important;
      transform:translate3d(var(--zpPortX,0px),var(--zpPortY,0px),0) rotate(var(--zpPortRot,-1deg)) scale(var(--zpPortScale,1.20))!important;
      animation:zpPortfolioFloat563 var(--zpPortFloatDur,7.4s) ease-in-out infinite!important;
      animation-delay:var(--zpPortDelay,0s)!important;
      transition:transform .72s cubic-bezier(.16,1,.3,1),filter .72s cubic-bezier(.16,1,.3,1)!important;
      will-change:transform!important;
    }
    html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--portfolio:not(.zpSSCard--portfolioCta):hover .zpSSCard__mock--portfolio,
    html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--portfolio:not(.zpSSCard--portfolioCta).isActive .zpSSCard__mock--portfolio,
    html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--portfolio:not(.zpSSCard--portfolioCta).isVisible:hover .zpSSCard__mock--portfolio{
      transform:translate3d(calc(var(--zpPortX,0px) + 10px),calc(var(--zpPortY,0px) - 14px),0) rotate(var(--zpPortRot,-1deg)) scale(var(--zpPortHoverScale,1.235))!important;
    }
    html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--portfolio:not(.zpSSCard--portfolioCta) .zpSSCard__mock--portfolio img,
    html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--portfolio:not(.zpSSCard--portfolioCta):hover .zpSSCard__mock--portfolio img,
    html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--portfolio:not(.zpSSCard--portfolioCta).isActive .zpSSCard__mock--portfolio img,
    html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--portfolio:not(.zpSSCard--portfolioCta).isVisible .zpSSCard__mock--portfolio img{
      width:100%!important;
      height:100%!important;
      object-fit:contain!important;
      object-position:center!important;
      background:transparent!important;
      border:0!important;
      box-shadow:none!important;
      transform:none!important;
      animation:none!important;
      filter:drop-shadow(0 34px 34px rgba(0,0,0,.24)) drop-shadow(0 10px 14px rgba(7,17,31,.14))!important;
    }
    html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--portfolio:not(.zpSSCard--portfolioCta) .zpSSCard__mock--portfolio::after{
      content:""!important;
      position:absolute!important;
      z-index:0!important;
      left:50%!important;
      top:82%!important;
      width:min(540px,62%)!important;
      height:52px!important;
      border-radius:999px!important;
      transform:translate(-50%,-50%) rotate(-3deg)!important;
      background:radial-gradient(ellipse at center,rgba(0,0,0,.18) 0%,rgba(0,0,0,.08) 45%,transparent 76%)!important;
      opacity:.48!important;
      pointer-events:none!important;
      filter:none!important;
    }
    html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--ap{--zpPortScale:1.34;--zpPortHoverScale:1.37;--zpPortX:8px;--zpPortY:0px;--zpPortRot:-1.1deg;--zpPortDelay:-.4s;--zpPortFloatDur:7.2s;}
    html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--siemianowski{--zpPortScale:1.27;--zpPortHoverScale:1.30;--zpPortX:2px;--zpPortY:8px;--zpPortRot:1deg;--zpPortDelay:-1.2s;--zpPortFloatDur:8s;}
    html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--gravia{--zpPortScale:1.24;--zpPortHoverScale:1.27;--zpPortX:4px;--zpPortY:6px;--zpPortRot:-.8deg;--zpPortDelay:-2s;--zpPortFloatDur:7.6s;}
    html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--shothome{--zpPortScale:1.25;--zpPortHoverScale:1.28;--zpPortX:4px;--zpPortY:4px;--zpPortRot:.7deg;--zpPortDelay:-1.6s;--zpPortFloatDur:7.8s;}
    html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--proscarves{--zpPortScale:1.25;--zpPortHoverScale:1.28;--zpPortX:6px;--zpPortY:8px;--zpPortRot:-1deg;--zpPortDelay:-2.4s;--zpPortFloatDur:8.2s;}
    html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--krawiec{--zpPortScale:1.30;--zpPortHoverScale:1.33;--zpPortX:8px;--zpPortY:2px;--zpPortRot:.9deg;--zpPortDelay:-.9s;--zpPortFloatDur:7.4s;}
    html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--papeterio{--zpPortScale:1.26;--zpPortHoverScale:1.29;--zpPortX:4px;--zpPortY:4px;--zpPortRot:-.7deg;--zpPortDelay:-1.9s;--zpPortFloatDur:8.1s;}
    html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--bransoletka{--zpPortScale:1.27;--zpPortHoverScale:1.30;--zpPortX:6px;--zpPortY:6px;--zpPortRot:.8deg;--zpPortDelay:-2.8s;--zpPortFloatDur:7.7s;}
    html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--rutpoz{--zpPortScale:1.29;--zpPortHoverScale:1.32;--zpPortX:8px;--zpPortY:2px;--zpPortRot:-.8deg;--zpPortDelay:-1.3s;--zpPortFloatDur:7.5s;}
    @keyframes zpPortfolioFloat563{
      0%,100%{translate:0 0;}
      50%{translate:0 -12px;}
    }
    @media (max-width:980px){
      html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--portfolio:not(.zpSSCard--portfolioCta) .zpSSCard__visual{
        min-height:clamp(250px,72vw,410px)!important;
        margin-top:8px!important;
      }
      html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--portfolio:not(.zpSSCard--portfolioCta) .zpSSCard__mock--portfolio{
        inset:-6% -13% -12% -13%!important;
        transform:translate3d(var(--zpPortMobX,0px),var(--zpPortMobY,0px),0) rotate(var(--zpPortRot,-1deg)) scale(var(--zpPortMobScale,.96))!important;
        animation-duration:8s!important;
      }
      html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--portfolio:not(.zpSSCard--portfolioCta):hover .zpSSCard__mock--portfolio,
      html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--portfolio:not(.zpSSCard--portfolioCta).isActive .zpSSCard__mock--portfolio,
      html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--portfolio:not(.zpSSCard--portfolioCta).isVisible:hover .zpSSCard__mock--portfolio{
        transform:translate3d(var(--zpPortMobX,0px),calc(var(--zpPortMobY,0px) - 8px),0) rotate(var(--zpPortRot,-1deg)) scale(var(--zpPortMobHoverScale,1.00))!important;
      }
      html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--ap{--zpPortMobScale:1.02;--zpPortMobHoverScale:1.05;--zpPortMobY:0px;}
      html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--siemianowski{--zpPortMobScale:.98;--zpPortMobHoverScale:1.01;--zpPortMobY:4px;}
      html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--gravia{--zpPortMobScale:.98;--zpPortMobHoverScale:1.01;}
      html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--shothome{--zpPortMobScale:.99;--zpPortMobHoverScale:1.02;}
      html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--proscarves{--zpPortMobScale:1.00;--zpPortMobHoverScale:1.03;}
      html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--krawiec{--zpPortMobScale:1.02;--zpPortMobHoverScale:1.05;}
      html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--papeterio{--zpPortMobScale:.99;--zpPortMobHoverScale:1.02;}
      html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--bransoletka{--zpPortMobScale:1.00;--zpPortMobHoverScale:1.03;}
      html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--rutpoz{--zpPortMobScale:1.01;--zpPortMobHoverScale:1.04;}
    }
    @media (prefers-reduced-motion:reduce){
      html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--portfolio:not(.zpSSCard--portfolioCta) .zpSSCard__mock--portfolio{
        animation:none!important;
      }
    }
  </style>
  <?php
}
add_action('wp_head','zp_suite_288_strony_portfolio_floating_mockups_css',PHP_INT_MAX);
add_action('wp_footer','zp_suite_288_strony_portfolio_floating_mockups_css',PHP_INT_MAX);
