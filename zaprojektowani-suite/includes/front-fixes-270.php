<?php
if (!defined('ABSPATH')) exit;
/**
 * ZP Suite v2.2.453 — home portfolio grid/full-bleed correction.
 * Keep the portfolio heading/copy on the 1750px grid, but let the horizontal
 * card sequence use a full-viewport bleed so cards are not clipped by the
 * 1750px content wrapper during the scroll sequence.
 */
add_action('wp_head', function(){
  if (is_admin()) return;
  // v2.2.492: every selector is prefixed body.home/.front-page — print only there.
  if (!is_front_page() && !is_home()) return;
  ?>
<style id="zp-suite-front-fixes-270-home-portfolio-fullbleed-v453">@media (min-width:761px){html body.home #zpShowcaseWhite,html body.front-page #zpShowcaseWhite{overflow-x:clip!important;overflow-y:visible!important}html body.home #zpShowcaseWhite .zpShowcaseWhite__inner,html body.front-page #zpShowcaseWhite .zpShowcaseWhite__inner{width:100%!important;max-width:none!important;margin-left:0!important;margin-right:0!important;padding-left:0!important;padding-right:0!important;overflow:visible!important;box-sizing:border-box!important}html body.home #zpShowcaseWhite .zpShowcaseWhite__head,html body.front-page #zpShowcaseWhite .zpShowcaseWhite__head,html body.home #zpShowcaseWhite .zpShowcaseWhite__bottom,html body.front-page #zpShowcaseWhite .zpShowcaseWhite__bottom{width:min(1750px,calc(100% - clamp(36px,6vw,112px)))!important;max-width:1750px!important;margin-left:auto!important;margin-right:auto!important;padding-left:0!important;padding-right:0!important;box-sizing:border-box!important}html body.home #zpShowcaseWhite .zpShowcaseWhite__stage,html body.front-page #zpShowcaseWhite .zpShowcaseWhite__stage{width:100vw!important;max-width:100vw!important;min-width:100vw!important;margin-left:calc(50% - 50vw)!important;margin-right:calc(50% - 50vw)!important;padding-left:0!important;padding-right:0!important;box-sizing:border-box!important;overflow:visible!important;clip-path:none!important;contain:none!important}html body.home #zpShowcaseWhite .zpShowcaseWhite__rail,html body.front-page #zpShowcaseWhite .zpShowcaseWhite__rail{overflow:visible!important;contain:none!important;clip-path:none!important;will-change:transform!important}html body.home #zpShowcaseWhite .zpShowcaseWhite__card,html body.front-page #zpShowcaseWhite .zpShowcaseWhite__card{overflow:visible!important;clip-path:none!important}}@media (min-width:761px) and (max-width:1500px){html body.home #zpShowcaseWhite .zpShowcaseWhite__stage,html body.front-page #zpShowcaseWhite .zpShowcaseWhite__stage{overflow-x:auto!important;overflow-y:visible!important;scroll-padding-left:clamp(28px,5vw,72px)!important;padding:2px 0 clamp(18px,2vw,28px)!important}html body.home #zpShowcaseWhite .zpShowcaseWhite__rail,html body.front-page #zpShowcaseWhite .zpShowcaseWhite__rail{padding-left:clamp(28px,5vw,72px)!important;padding-right:clamp(28px,5vw,72px)!important}}</style>
  <?php
}, PHP_INT_MAX);
