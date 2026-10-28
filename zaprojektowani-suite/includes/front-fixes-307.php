<?php
/**
 * v2.2.782 — Home portfolio / branding case study polish.
 * Cleaner proportions, aligned content blocks and a safe square artwork stage.
 */
if (!defined('ABSPATH')) { exit; }

if (!function_exists('zp_suite_307_home_branding_popup_polish')) {
  function zp_suite_307_home_branding_popup_polish(){
    if (is_admin() || (!is_front_page() && !is_home())) { return; }
    ?>
    <style id="zp-suite-307-home-branding-popup-polish-v2782">
      /* Desktop: one calm two-column case-study sheet. */
      @media (min-width:1101px){
        html body .zpShowcasePop__panel{
          width:min(1380px,calc(100vw - 44px))!important;
          max-height:calc(100dvh - 40px)!important;
          height:auto!important;
          overflow:hidden!important;
          border-radius:30px!important;
        }
        html body .zpShowcasePop__body{
          max-height:calc(100dvh - 40px)!important;
          height:auto!important;
          overflow-y:auto!important;
          overflow-x:hidden!important;
          scrollbar-gutter:stable;
        }
        html body .zpShowcasePop__layout--logo{
          display:grid!important;
          grid-template-columns:minmax(500px,.92fr) minmax(0,1.08fr)!important;
          align-items:start!important;
          min-height:0!important;
          background:#fff!important;
        }
        html body .zpShowcasePop__layout--logo .zpShowcasePop__visual{
          position:sticky!important;
          top:0!important;
          align-self:start!important;
          min-height:0!important;
          height:min(calc(100dvh - 40px),790px)!important;
          padding:34px!important;
          display:flex!important;
          align-items:center!important;
          justify-content:center!important;
          background:linear-gradient(145deg,#06101e 0%,#0a1b31 100%)!important;
          overflow:hidden!important;
        }
        html body .zpShowcasePop__layout--logo .zpShowcasePop__visualInner{
          position:relative!important;
          inset:auto!important;
          width:min(100%,680px)!important;
          height:auto!important;
          aspect-ratio:1/1!important;
          display:block!important;
          padding:0!important;
          border-radius:24px!important;
          overflow:hidden!important;
          background:#fff!important;
          box-shadow:0 28px 70px rgba(0,0,0,.24)!important;
        }
        html body .zpShowcasePop__layout--logo .zpShowcasePop__visualInner img,
        html body .zpShowcasePop__layout--logo .zpShowcasePop__img{
          display:block!important;
          width:100%!important;
          height:100%!important;
          max-width:none!important;
          max-height:none!important;
          aspect-ratio:1/1!important;
          object-fit:contain!important;
          object-position:center!important;
          border-radius:24px!important;
          transform:none!important;
        }
        html body .zpShowcasePop__layout--logo .zpShowcasePop__info{
          min-width:0!important;
          padding:42px 42px 46px!important;
          background:#fff!important;
        }
        html body .zpShowcasePop__layout--logo .zpShowcasePop__top{
          display:flex!important;
          flex-wrap:wrap!important;
          gap:8px!important;
          padding-right:54px!important;
          margin-bottom:18px!important;
        }
        html body .zpShowcasePop__layout--logo .zpShowcasePop__title{
          max-width:760px!important;
          margin:0!important;
          font-size:clamp(40px,3.1vw,54px)!important;
          line-height:1.02!important;
          letter-spacing:-.048em!important;
          text-wrap:balance!important;
          overflow-wrap:normal!important;
          word-break:normal!important;
          hyphens:none!important;
        }
        html body .zpShowcasePop__layout--logo .zpShowcasePop__subtitle{
          margin-top:9px!important;
          font-size:15px!important;
          line-height:1.45!important;
        }
        html body .zpShowcasePop__layout--logo .zpShowcasePop__text{
          max-width:760px!important;
          margin-top:18px!important;
          font-size:14px!important;
          line-height:1.72!important;
        }

        /* Story: the key direction gets a full row, goal/effect sit evenly below. */
        html body .zpShowcasePop__layout--logo .zpShowcasePop__story{
          display:grid!important;
          grid-template-columns:repeat(2,minmax(0,1fr))!important;
          gap:10px!important;
          margin:24px 0!important;
        }
        html body .zpShowcasePop__layout--logo .zpShowcasePop__storyCard{
          min-width:0!important;
          min-height:0!important;
          grid-template-columns:40px minmax(0,1fr)!important;
          gap:12px!important;
          padding:17px 18px!important;
          border-radius:18px!important;
          align-content:start!important;
        }
        html body .zpShowcasePop__layout--logo .zpShowcasePop__storyCard:nth-child(2){
          grid-column:1 / -1!important;
          grid-row:1!important;
        }
        html body .zpShowcasePop__layout--logo .zpShowcasePop__storyCard:nth-child(1),
        html body .zpShowcasePop__layout--logo .zpShowcasePop__storyCard:nth-child(3){
          grid-row:2!important;
        }
        html body .zpShowcasePop__layout--logo .zpShowcasePop__storyCard p{
          font-size:12.5px!important;
          line-height:1.58!important;
          overflow-wrap:normal!important;
          word-break:normal!important;
          hyphens:none!important;
        }

        /* 2x2 facts, aligned and equal instead of a squeezed strip. */
        html body .zpShowcasePop__layout--logo .zpShowcasePop__grid{
          display:grid!important;
          grid-template-columns:repeat(2,minmax(0,1fr))!important;
          grid-auto-rows:1fr!important;
          gap:1px!important;
          margin:0 0 24px!important;
          border-radius:18px!important;
          overflow:hidden!important;
        }
        html body .zpShowcasePop__layout--logo .zpShowcasePop__gridItem{
          min-width:0!important;
          padding:16px 17px!important;
          background:rgba(255,255,255,.72)!important;
        }
        html body .zpShowcasePop__layout--logo .zpShowcasePop__value{
          margin-top:6px!important;
          line-height:1.45!important;
          overflow-wrap:normal!important;
          word-break:normal!important;
          hyphens:none!important;
        }
        html body .zpShowcasePop__layout--logo .zpShowcasePop__tools{
          margin:-2px 0 24px!important;
        }
        html body .zpShowcasePop__layout--logo .zpShowcasePop__scope{
          grid-template-columns:repeat(2,minmax(0,1fr))!important;
          gap:9px 20px!important;
          margin-bottom:26px!important;
        }
        html body .zpShowcasePop__layout--logo .zpShowcasePop__scope li{
          min-width:0!important;
          align-items:flex-start!important;
          line-height:1.5!important;
          overflow-wrap:normal!important;
          word-break:normal!important;
          hyphens:none!important;
        }
        html body .zpShowcasePop__layout--logo .zpShowcasePop__results{
          display:grid!important;
          grid-template-columns:repeat(3,minmax(0,1fr))!important;
          gap:10px!important;
          margin-bottom:26px!important;
        }
        html body .zpShowcasePop__layout--logo .zpShowcasePop__result{
          min-width:0!important;
          min-height:116px!important;
          padding:16px!important;
          display:flex!important;
          flex-direction:column!important;
        }
        html body .zpShowcasePop__layout--logo .zpShowcasePop__result span{
          overflow-wrap:normal!important;
          word-break:normal!important;
          hyphens:none!important;
        }
        html body .zpShowcasePop__layout--logo .zpShowcasePop__cta{
          align-items:stretch!important;
          gap:10px!important;
        }
        html body .zpShowcasePop__layout--logo .zpShowcasePop__cta a{
          min-height:48px!important;
          justify-content:center!important;
        }
      }

      /* Tablet / smaller notebook: artwork first, copy below, all at one clean width. */
      @media (min-width:761px) and (max-width:1100px){
        html body .zpShowcasePop__panel{
          width:min(920px,calc(100vw - 34px))!important;
          max-height:calc(100dvh - 34px)!important;
          overflow:hidden!important;
        }
        html body .zpShowcasePop__body{
          max-height:calc(100dvh - 34px)!important;
          overflow-y:auto!important;
          overflow-x:hidden!important;
        }
        html body .zpShowcasePop__layout--logo{
          grid-template-columns:1fr!important;
          background:#fff!important;
        }
        html body .zpShowcasePop__layout--logo .zpShowcasePop__visual{
          min-height:0!important;
          height:auto!important;
          padding:26px!important;
          background:linear-gradient(145deg,#06101e,#0a1b31)!important;
        }
        html body .zpShowcasePop__layout--logo .zpShowcasePop__visualInner{
          width:min(100%,660px)!important;
          aspect-ratio:1/1!important;
          margin:0 auto!important;
          border-radius:22px!important;
          background:#fff!important;
        }
        html body .zpShowcasePop__layout--logo .zpShowcasePop__info{
          padding:34px 32px 42px!important;
          margin:0!important;
          border-radius:0!important;
        }
        html body .zpShowcasePop__layout--logo .zpShowcasePop__story{
          grid-template-columns:repeat(2,minmax(0,1fr))!important;
          gap:10px!important;
        }
        html body .zpShowcasePop__layout--logo .zpShowcasePop__storyCard:nth-child(2){grid-column:1/-1!important;grid-row:1!important}
        html body .zpShowcasePop__layout--logo .zpShowcasePop__storyCard:nth-child(1),
        html body .zpShowcasePop__layout--logo .zpShowcasePop__storyCard:nth-child(3){grid-row:2!important}
        html body .zpShowcasePop__layout--logo .zpShowcasePop__results{grid-template-columns:repeat(3,minmax(0,1fr))!important}
      }

      @media (max-width:760px){
        html body .zpShowcasePop__layout--logo .zpShowcasePop__visual{
          min-height:0!important;
          height:auto!important;
          padding:76px 12px 12px!important;
          background:linear-gradient(145deg,#06101e,#0a1b31)!important;
        }
        html body .zpShowcasePop__layout--logo .zpShowcasePop__visualInner{
          position:relative!important;
          inset:auto!important;
          width:100%!important;
          height:auto!important;
          aspect-ratio:1/1!important;
          border-radius:18px!important;
          overflow:hidden!important;
          background:#fff!important;
        }
        html body .zpShowcasePop__layout--logo .zpShowcasePop__visualInner img,
        html body .zpShowcasePop__layout--logo .zpShowcasePop__img{
          width:100%!important;
          height:100%!important;
          aspect-ratio:1/1!important;
          object-fit:contain!important;
          object-position:center!important;
          border-radius:18px!important;
          transform:none!important;
        }
        html body .zpShowcasePop__layout--logo .zpShowcasePop__info{
          margin-top:-1px!important;
          padding:26px 17px 34px!important;
          border-radius:0!important;
        }
        html body .zpShowcasePop__layout--logo .zpShowcasePop__title{
          font-size:clamp(34px,10vw,44px)!important;
          line-height:1.02!important;
          letter-spacing:-.045em!important;
        }
        html body .zpShowcasePop__layout--logo .zpShowcasePop__text{
          font-size:13px!important;
          line-height:1.66!important;
        }
        html body .zpShowcasePop__layout--logo .zpShowcasePop__story,
        html body .zpShowcasePop__layout--logo .zpShowcasePop__grid,
        html body .zpShowcasePop__layout--logo .zpShowcasePop__results{
          grid-template-columns:1fr!important;
        }
        html body .zpShowcasePop__layout--logo .zpShowcasePop__storyCard:nth-child(n){grid-column:auto!important;grid-row:auto!important}
        html body .zpShowcasePop__layout--logo .zpShowcasePop__scope{grid-template-columns:1fr!important}
        html body .zpShowcasePop__layout--logo .zpShowcasePop__result{min-height:0!important}
      }
    </style>
    <?php
  }
  add_action('wp_head','zp_suite_307_home_branding_popup_polish',PHP_INT_MAX);
  add_action('wp_footer','zp_suite_307_home_branding_popup_polish',PHP_INT_MAX);
}
