<?php
/**
 * v2.2.445 — Strony Internetowe portfolio CTA people image +40% safer scaling.
 * Scope: desktop only, final portfolio CTA card. No contact/form changes.
 */
if (!defined('ABSPATH')) { exit; }

function zp_suite_262_portfolio_cta_people_image_plus_40_css() {
  // v2.2.492: selectors are all scoped to #zpStronyKatowice — print only on that page.
  if (function_exists('zp_suite_2232_is_strony_katowice_request') && !zp_suite_2232_is_strony_katowice_request()) { return; }
  ?>
<style id="zp-suite-front-fixes-262-portfolio-cta-people-plus40-v445">@media (min-width:981px){html body #zpStronyKatowice #zpPortfolioReveal,html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard,html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard__inner,html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard__visual,html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--portfolioCta{overflow:visible!important}html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--portfolioCta{--zpCtaPhotoScaleDesktop:1.20!important;--zpCtaPhotoXDesktop:34px!important;--zpCtaPhotoYDesktop:-96px!important}html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--portfolioCta .zpSSCard__mock--contact,html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--portfolioCta.isVisible .zpSSCard__mock--contact,html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--portfolioCta.isActive .zpSSCard__mock--contact,html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--portfolioCta:hover .zpSSCard__mock--contact{position:absolute!important;inset:-78px -8% -25% auto!important;width:min(940px,74%)!important;height:calc(100% + 190px)!important;transform:translate3d(var(--zpCtaPhotoXDesktop),calc(var(--zpContactPhotoUpDesktop,0px) + var(--zpCtaPhotoYDesktop)),0)!important;transform-origin:right bottom!important;overflow:visible!important;z-index:9!important;background:transparent!important;border:0!important;outline:0!important;box-shadow:none!important;-webkit-box-shadow:none!important;filter:none!important}html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--portfolioCta .zpSSCard__mock--contact img,html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--portfolioCta.isVisible .zpSSCard__mock--contact img,html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--portfolioCta.isActive .zpSSCard__mock--contact img,html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--portfolioCta:hover .zpSSCard__mock--contact img{width:100%!important;height:100%!important;object-fit:contain!important;object-position:right bottom!important;transform:scale(var(--zpCtaPhotoScaleDesktop))!important;transform-origin:right bottom!important;max-width:none!important;max-height:none!important;border:0!important;outline:0!important;box-shadow:none!important;-webkit-box-shadow:none!important;filter:none!important}}</style>
  <?php
}
add_action('wp_head', 'zp_suite_262_portfolio_cta_people_image_plus_40_css', PHP_INT_MAX);
add_action('wp_footer', 'zp_suite_262_portfolio_cta_people_image_plus_40_css', PHP_INT_MAX);
