<?php
/**
 * v2.2.456 — Studio Wyceny 1750 grid only.
 * The previous logo mobile hero unclip script was removed because it pushed the header/chips/cards outside the viewport.
 */
if (!defined('ABSPATH')) { exit; }

add_action('wp_head', function () {
  if (is_admin()) { return; }
  ?>
  <style id="zp-suite-273-studio-1750-only">html body #zpbsUltimate{--zpbsGridMax:1750px!important}html body #zpbsUltimate .zpbsChoose__in,html body #zpbsUltimate .zpbsInner,html body #zpbsUltimate .zpbsBrief .zpbsInner,html body #zpbsUltimate .zpbsTop,html body #zpbsUltimate .zpbsGrid,html body #zpbsUltimate .zpbsSlide,html body #zpbsUltimate .zpbsSlideHead,html body #zpbsUltimate .zpbsMiniProgress,html body #zpbsUltimate .zpbsTimeline,html body #zpbsUltimate .zpbsPackages,html body #zpbsUltimate .zpbsFieldGrid,html body #zpbsUltimate .zpbsBudget,html body #zpbsUltimate .zpbsFinal,html body #zpbsUltimate .zpbsNav{max-width:var(--zpbsGridMax)!important;box-sizing:border-box!important}html body #zpbsUltimate .zpbsChoose__in,html body #zpbsUltimate .zpbsInner,html body #zpbsUltimate .zpbsBrief .zpbsInner{width:min(var(--zpbsGridMax),calc(100% - 56px))!important;margin-left:auto!important;margin-right:auto!important}html body #zpbsUltimate .zpbsChoose__in{grid-template-columns:minmax(430px,690px) minmax(640px,1fr)!important;column-gap:clamp(46px,5.2vw,104px)!important}html body #zpbsUltimate .zpbsServicePanel{max-width:720px!important}html body #zpbsUltimate .zpbsChoose__copy{max-width:1240px!important}html body #zpbsUltimate .zpbsChoose h1{max-width:1060px!important}html body #zpbsUltimate .zpbsChooseLead{max-width:900px!important}@media(max-width:1180px){html body #zpbsUltimate .zpbsChoose__in{width:min(var(--zpbsGridMax),calc(100% - 44px))!important;grid-template-columns:1fr!important}html body #zpbsUltimate .zpbsServicePanel{max-width:100%!important}}@media(max-width:760px){html body #zpbsUltimate .zpbsChoose__in,html body #zpbsUltimate .zpbsInner,html body #zpbsUltimate .zpbsBrief .zpbsInner{width:calc(100% - 28px)!important}}</style>
  <?php
}, 1020);
