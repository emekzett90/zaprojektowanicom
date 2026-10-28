<?php
/**
 * v2.2.781 — Home portfolio branding popup.
 * Keep square branding artwork square and give the case-study copy enough width.
 */
if (!defined('ABSPATH')) { exit; }

if (!function_exists('zp_suite_306_home_branding_popup_square_css')) {
  function zp_suite_306_home_branding_popup_square_css(){
    if (is_admin() || (!is_front_page() && !is_home())) { return; }
    ?>
    <style id="zp-suite-306-home-branding-popup-square-v2781">
      @media (min-width:761px){
        html body .zpShowcasePop__panel{
          width:min(1480px,calc(100vw - 48px))!important;
          height:auto!important;
          max-height:calc(100dvh - 48px)!important;
          border-radius:30px!important;
          overflow-y:auto!important;
          overflow-x:hidden!important;
        }
        html body .zpShowcasePop__body{
          min-height:0!important;
          height:auto!important;
          max-height:none!important;
          overflow:visible!important;
        }
        html body .zpShowcasePop__layout--logo{
          display:grid!important;
          grid-template-columns:minmax(480px,.92fr) minmax(600px,1.08fr)!important;
          align-items:start!important;
          min-height:0!important;
        }
        html body .zpShowcasePop__layout--logo .zpShowcasePop__visual{
          min-height:0!important;
          height:auto!important;
          padding:28px!important;
          display:flex!important;
          align-items:flex-start!important;
          justify-content:center!important;
          overflow:visible!important;
          background:#071426!important;
        }
        html body .zpShowcasePop__layout--logo .zpShowcasePop__visualInner{
          position:relative!important;
          inset:auto!important;
          width:min(100%,760px)!important;
          height:auto!important;
          aspect-ratio:1 / 1!important;
          padding:0!important;
          display:block!important;
          overflow:hidden!important;
          border-radius:24px!important;
          background:#0a1728!important;
          box-shadow:0 24px 72px rgba(7,20,38,.24)!important;
        }
        html body .zpShowcasePop__layout--logo .zpShowcasePop__visualInner img,
        html body .zpShowcasePop__layout--logo .zpShowcasePop__img{
          display:block!important;
          width:100%!important;
          height:100%!important;
          max-width:none!important;
          max-height:none!important;
          aspect-ratio:1 / 1!important;
          object-fit:contain!important;
          object-position:center center!important;
          border-radius:24px!important;
          transform:none!important;
        }
        html body .zpShowcasePop__layout--logo .zpShowcasePop__info{
          min-width:0!important;
          padding:44px clamp(34px,3.1vw,54px) 48px!important;
        }
        html body .zpShowcasePop__layout--logo .zpShowcasePop__title{
          font-size:clamp(42px,3.6vw,62px)!important;
          line-height:.98!important;
          letter-spacing:-.052em!important;
          overflow-wrap:normal!important;
          word-break:normal!important;
          hyphens:none!important;
        }
        html body .zpShowcasePop__layout--logo .zpShowcasePop__subtitle,
        html body .zpShowcasePop__layout--logo .zpShowcasePop__text,
        html body .zpShowcasePop__layout--logo .zpShowcasePop__value,
        html body .zpShowcasePop__layout--logo .zpShowcasePop__scope li,
        html body .zpShowcasePop__layout--logo .zpShowcasePop__storyCard p,
        html body .zpShowcasePop__layout--logo .zpShowcasePop__result span{
          overflow-wrap:normal!important;
          word-break:normal!important;
          hyphens:none!important;
        }
        html body .zpShowcasePop__layout--logo .zpShowcasePop__text{
          max-width:760px!important;
          font-size:15px!important;
          line-height:1.68!important;
        }
        html body .zpShowcasePop__layout--logo .zpShowcasePop__story{
          grid-template-columns:1fr!important;
          gap:10px!important;
        }
        html body .zpShowcasePop__layout--logo .zpShowcasePop__storyCard{
          grid-template-columns:42px minmax(0,1fr)!important;
          padding:16px 18px!important;
        }
        html body .zpShowcasePop__layout--logo .zpShowcasePop__grid{
          grid-template-columns:repeat(2,minmax(0,1fr))!important;
        }
        html body .zpShowcasePop__layout--logo .zpShowcasePop__results{
          grid-template-columns:repeat(auto-fit,minmax(180px,1fr))!important;
        }
      }
      @media (min-width:761px) and (max-width:1240px){
        html body .zpShowcasePop__panel{
          width:min(980px,calc(100vw - 36px))!important;
        }
        html body .zpShowcasePop__layout--logo{
          grid-template-columns:1fr!important;
        }
        html body .zpShowcasePop__layout--logo .zpShowcasePop__visual{
          padding:24px!important;
        }
        html body .zpShowcasePop__layout--logo .zpShowcasePop__visualInner{
          width:min(720px,100%)!important;
        }
        html body .zpShowcasePop__layout--logo .zpShowcasePop__info{
          padding:38px 36px 46px!important;
        }
      }
      @media (max-width:760px){
        html body .zpShowcasePop__layout--logo .zpShowcasePop__visualInner,
        html body .zpShowcasePop__layout--logo .zpShowcasePop__visualInner img,
        html body .zpShowcasePop__layout--logo .zpShowcasePop__img{
          aspect-ratio:1 / 1!important;
          object-fit:contain!important;
          object-position:center!important;
        }
      }
    </style>
    <?php
  }
  add_action('wp_head','zp_suite_306_home_branding_popup_square_css',PHP_INT_MAX);
  add_action('wp_footer','zp_suite_306_home_branding_popup_square_css',PHP_INT_MAX);
}
