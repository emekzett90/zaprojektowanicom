<?php
if (!defined('ABSPATH')) exit;
/**
 * ZP Suite v2.2.454 — home portfolio first card alignment.
 * Keep the horizontal rail full-bleed, but align the first card to the same
 * left grid line as the section heading (1750px content grid).
 */
add_action('wp_head', function(){
  if (is_admin()) return;
  // v2.2.492: every selector is prefixed body.home/.front-page — print only there.
  if (!is_front_page() && !is_home()) return;
  ?>
<style id="zp-suite-front-fixes-271-home-portfolio-card-align-v454">@media (min-width:761px){html body.home #zpShowcaseWhite,html body.front-page #zpShowcaseWhite{--zpHomePortfolioGridPad: clamp(36px,6vw,112px);--zpHomePortfolioGutter: max(calc((100vw - 1750px) / 2),calc(var(--zpHomePortfolioGridPad) / 2))}html body.home #zpShowcaseWhite .zpShowcaseWhite__head,html body.front-page #zpShowcaseWhite .zpShowcaseWhite__head,html body.home #zpShowcaseWhite .zpShowcaseWhite__bottom,html body.front-page #zpShowcaseWhite .zpShowcaseWhite__bottom{width:min(1750px,calc(100% - var(--zpHomePortfolioGridPad)))!important;max-width:1750px!important;margin-left:auto!important;margin-right:auto!important;padding-left:0!important;padding-right:0!important;box-sizing:border-box!important}html body.home #zpShowcaseWhite .zpShowcaseWhite__stage,html body.front-page #zpShowcaseWhite .zpShowcaseWhite__stage{width:100vw!important;max-width:100vw!important;min-width:100vw!important;margin-left:calc(50% - 50vw)!important;margin-right:calc(50% - 50vw)!important;padding-left:0!important;padding-right:0!important;overflow:visible!important;clip-path:none!important;contain:none!important}html body.home #zpShowcaseWhite .zpShowcaseWhite__rail,html body.front-page #zpShowcaseWhite .zpShowcaseWhite__rail{padding-left:var(--zpHomePortfolioGutter)!important;padding-right:var(--zpHomePortfolioGutter)!important;margin-left:0!important;overflow:visible!important;clip-path:none!important;contain:none!important;box-sizing:border-box!important}html body.home #zpShowcaseWhite .zpShowcaseWhite__card:first-child,html body.front-page #zpShowcaseWhite .zpShowcaseWhite__card:first-child{margin-left:0!important}}@media (min-width:761px) and (max-width:1500px){html body.home #zpShowcaseWhite .zpShowcaseWhite__stage,html body.front-page #zpShowcaseWhite .zpShowcaseWhite__stage{scroll-padding-left:var(--zpHomePortfolioGutter)!important}}</style>
  <?php
}, PHP_INT_MAX);
