<?php
/**
 * ZAPROJEKTOWANI Suite — front fixes v2.2.196
 * Home reviews source-width rail + Home SEO intro kicker/spacing cleanup.
 */
if (!defined('ABSPATH')) exit;

add_action('wp_head', function () {
  if (is_admin()) return;
  ?>
  <style id="zp-suite-front-fixes-196">html body .zpRevEd .zpRevEd__inner{width:min(1740px,calc(100% - clamp(28px,6vw,112px)))!important;max-width:1740px!important;margin-left:auto!important;margin-right:auto!important}html body .zpRevEd .zpRevEd__more .zpRevEd__railWrap{width:100vw!important;max-width:100vw!important;margin-left:calc(50% - 50vw)!important;margin-right:0!important;overflow-x:clip!important;overflow-y:visible!important}html body .zpRevEd .zpRevEd__more .zpRevEd__rail{padding-left:max(calc((100vw - 1740px) / 2),calc(clamp(28px,6vw,112px) / 2))!important;padding-right:max(28px,calc((100vw - 1740px) / 2 + 28px))!important}html body .zpHomeSeoIntro,html body section#seo-strony-internetowe-katowice.zpHomeSeoIntro{margin-top:0!important;background:#fff!important;padding-top:calc(clamp(28px,4.5vw,72px) + 40px)!important}html body .zpHomeSeoIntro::before,html body section#seo-strony-internetowe-katowice.zpHomeSeoIntro::before{background:radial-gradient(circle at 14% 0%,rgba(7,20,38,.035),transparent 36%)!important}html body .zpHomeSeoIntro__kicker{display:inline-flex!important;align-items:center!important;gap:10px!important;margin:0 0 12px!important;color:#667386!important;text-transform:uppercase!important;letter-spacing:.13em!important;font-size:10px!important;line-height:1!important;font-weight:850!important}html body .zpHomeSeoIntro__kicker::before{content:""!important;width:24px!important;height:1px!important;background:#07111f!important;flex:0 0 24px!important}@media(max-width:900px){html body .zpHomeSeoIntro,html body section#seo-strony-internetowe-katowice.zpHomeSeoIntro{margin-top:0!important;padding-top:66px!important;background:#fff!important}html body .zpHomeSeoIntro__kicker{font-size:10px!important;letter-spacing:.13em!important}}</style>
  <?php
}, 2147483647);
