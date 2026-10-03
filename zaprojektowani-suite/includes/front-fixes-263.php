<?php
/**
 * v2.2.446 — Portfolio CTA image replacement + position polish.
 * - Uses team_kontakt.webp on home showcase CTA and /strony-internetowe-katowice portfolio CTA.
 * - Moves the /strony-internetowe-katowice CTA image slightly left without changing card layout.
 */
if (!defined('ABSPATH')) { exit; }

function zp_suite_263_portfolio_cta_team_kontakt_css_js() {
  // v2.2.492: needed only where the portfolio/services CTA card renders:
  // home, /strony-internetowe-katowice/ or pages with the showcase shortcodes.
  $zp263_show = is_front_page() || is_home();
  if (!$zp263_show && function_exists('zp_suite_2232_is_strony_katowice_request')) {
    $zp263_show = zp_suite_2232_is_strony_katowice_request();
  }
  if (!$zp263_show && is_singular()) {
    global $post;
    $zp263_content = $post && !empty($post->post_content) ? (string) $post->post_content : '';
    $zp263_show = $zp263_content !== '' && (has_shortcode($zp263_content, 'zp_showcase_portfolio') || has_shortcode($zp263_content, 'zp_showcase_services') || has_shortcode($zp263_content, 'zp_home_full') || has_shortcode($zp263_content, 'zp_home'));
  }
  if (!$zp263_show) { return; }
  ?>
<style id="zp-suite-front-fixes-263-portfolio-team-kontakt-v446">@media (min-width:981px){html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--portfolioCta{--zpCtaPhotoXDesktop:-12px!important;--zpCtaPhotoYDesktop:-96px!important;--zpCtaPhotoScaleDesktop:1.20!important}html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--portfolioCta .zpSSCard__mock--contact,html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--portfolioCta:hover .zpSSCard__mock--contact,html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--portfolioCta.isActive .zpSSCard__mock--contact,html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--portfolioCta.isVisible .zpSSCard__mock--contact{transform:translate3d(var(--zpCtaPhotoXDesktop),calc(var(--zpContactPhotoUpDesktop,0px) + var(--zpCtaPhotoYDesktop)),0)!important}html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--portfolioCta .zpSSCard__mock--contact img,html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--portfolioCta:hover .zpSSCard__mock--contact img,html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--portfolioCta.isActive .zpSSCard__mock--contact img,html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--portfolioCta.isVisible .zpSSCard__mock--contact img{object-position:center bottom!important}}</style>
<script id="zp-suite-front-fixes-263-portfolio-team-kontakt-v446-js">
(function(){
  var src='https://zaprojektowani.com/wp-content/uploads/2026/06/team_kontakt.webp';
  function swap(){
    document.querySelectorAll('.zpSSCard--portfolioCta .zpSSCard__mock--contact img, .zpShowcaseServices .zpSSCard__mock--contact img, .zpShowcaseWhite .zpSSCard__mock--contact img').forEach(function(img){
      if(!img || img.getAttribute('src')===src) return;
      img.setAttribute('src',src);
      img.removeAttribute('srcset');
      img.removeAttribute('sizes');
    });
  }
  if(document.readyState==='loading') document.addEventListener('DOMContentLoaded',swap,{once:true}); else swap();
  window.addEventListener('load',swap,{once:true});
})();
</script>
  <?php
}
add_action('wp_head', 'zp_suite_263_portfolio_cta_team_kontakt_css_js', PHP_INT_MAX);
add_action('wp_footer', 'zp_suite_263_portfolio_cta_team_kontakt_css_js', PHP_INT_MAX);
