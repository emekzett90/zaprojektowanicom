<?php
if (!defined('ABSPATH')) { exit; }

/**
 * ZP Suite v2.2.214 — Performance Lite dla /strony-internetowe-katowice/.
 * Bez zmiany układu: mniej blur/backdrop-filter, lżejsze cienie/dekoracje,
 * uproszczone reveal’e i wyłączenie ciężkich animacji mobile.
 */

if (!function_exists('zp_suite_perf_bool_214')) {
  function zp_suite_perf_bool_214($path, $default = '1') {
    return (string) zp_suite_opt($path, $default) === '1';
  }
}

function zp_suite_is_katowice_perf_page_214() {
  if (is_admin()) { return false; }
  return zp_suite_is_service_page('strony') || (isset($_SERVER['REQUEST_URI']) && strpos((string)$_SERVER['REQUEST_URI'], '/strony-internetowe-katowice') !== false);
}

function zp_suite_is_katowice_perf_lite_214() {
  return zp_suite_perf_bool_214('performance.katowice_lite_enabled', '1');
}

add_filter('body_class', function($classes){
  if (zp_suite_is_katowice_perf_page_214() && zp_suite_is_katowice_perf_lite_214()) {
    $classes[] = 'zpKatPerfLite';
    if (zp_suite_perf_bool_214('performance.katowice_lite_mobile_enabled', '1')) $classes[] = 'zpKatPerfLiteMobile';
    if (zp_suite_perf_bool_214('performance.katowice_disable_blur', '1')) $classes[] = 'zpKatPerfNoBlur';
    if (zp_suite_perf_bool_214('performance.katowice_reduce_reveals', '1')) $classes[] = 'zpKatPerfSimpleReveal';
    if (zp_suite_perf_bool_214('performance.katowice_reduce_shadows_watermarks', '1')) $classes[] = 'zpKatPerfLightDecor';
    if (zp_suite_perf_bool_214('performance.katowice_disable_mobile_heavy_motion', '1')) $classes[] = 'zpKatPerfMobileStatic';
  }
  return $classes;
}, 31);

add_action('wp_head', function(){
  if (!zp_suite_is_katowice_perf_page_214()) return;
  if (!zp_suite_is_katowice_perf_lite_214()) return;

  $no_blur = zp_suite_perf_bool_214('performance.katowice_disable_blur', '1');
  $simple_reveal = zp_suite_perf_bool_214('performance.katowice_reduce_reveals', '1');
  $light_decor = zp_suite_perf_bool_214('performance.katowice_reduce_shadows_watermarks', '1');
  $mobile_static = zp_suite_perf_bool_214('performance.katowice_disable_mobile_heavy_motion', '1');
  $respect_rm = zp_suite_perf_bool_214('performance.katowice_respect_reduced_motion', '1');
  ?>
  <style id="zp-suite-katowice-performance-lite-214">
    /* ZP /strony-internetowe-katowice/ Performance Lite — układ zostaje, mniej pracy dla GPU. */
    html body.zpKatPerfLite #zpStronyKatowice{
      scroll-behavior:auto;
    }

    <?php if ($no_blur): ?>
    html body.zpKatPerfNoBlur #zpStronyKatowice :is(
      .zpWebHeroKat__btn--ghost,
      .zpWebHeroKat__chip,
      .zpWebHeroKat__metric,
      .zpWebHeroKat__mini,
      .zpSSCard,
      .zpSSBadge,
      .zpSSBtn,
      .zpTrustCert__score,
      .zpTrustCert__quote,
      .zpTrustCert__bubble,
      .zpProcessFlow__card,
      .zpProcessFlow__topBar,
      .zpKatSeoBoost__panel,
      .zpKatSeoBoost__card,
      .zpPkgCard,
      .zpFaqItem,
      .zpIndustryCard
    ){
      -webkit-backdrop-filter:none!important;
      backdrop-filter:none!important;
    }

    html body.zpKatPerfNoBlur #zpStronyKatowice :is(
      .zpWebHeroKat__glow,
      .zpWebHeroKat__orbital,
      .zpSSMockAura,
      .zpTrustCert__scoreGlow,
      .zpTrustCert__bleed,
      .zpProcessFlow__bleed,
      .zpProcessFlow__person::before,
      .zpKatSeoBoost__glow,
      .zpIndustryCard__glow
    ){
      filter:none!important;
      -webkit-filter:none!important;
    }
    <?php endif; ?>

    <?php if ($light_decor): ?>
    html body.zpKatPerfLightDecor #zpStronyKatowice :is(
      .zpWebHeroKat__metric,
      .zpWebHeroKat__mini,
      .zpSSCard,
      .zpTrustCert__score,
      .zpTrustCert__quote,
      .zpProcessFlow__card,
      .zpKatSeoBoost__panel,
      .zpKatSeoBoost__card,
      .zpPkgCard,
      .zpIndustryCard
    ){
      box-shadow:0 16px 44px rgba(5,12,24,.12)!important;
    }

    html body.zpKatPerfLightDecor #zpStronyKatowice :is(
      .zpWebHeroKat__watermark,
      .zpTrustCert__watermark,
      .zpProcessFlow__watermark,
      .zpKatSeoBoost__watermark
    ){
      opacity:.018!important;
      animation:none!important;
    }

    html body.zpKatPerfLightDecor #zpStronyKatowice :is(
      .zpWebHeroKat__noise,
      .zpTrustCert__bleed,
      .zpProcessFlow__bleed
    ){
      opacity:.035!important;
    }
    <?php endif; ?>

    <?php if ($simple_reveal): ?>
    html body.zpKatPerfSimpleReveal #zpStronyKatowice :is(
      [data-zp-reveal],
      [data-zp-ss-card],
      .zpWebHeroKat__copy,
      .zpWebHeroKat__visual,
      .zpSSCard,
      .zpTrustCert__copy,
      .zpTrustCert__score,
      .zpTrustCert__quote,
      .zpProcessFlow__head,
      .zpProcessFlow__card,
      .zpKatSeoBoost__panel,
      .zpKatSeoBoost__card,
      .zpIndustryCard,
      .zpPkgCard,
      .zpFaqItem
    ){
      transition-duration:.24s!important;
      transition-delay:0s!important;
      filter:none!important;
      will-change:auto!important;
    }
    <?php endif; ?>

    html body.zpKatPerfLite #zpStronyKatowice :is(
      .zpWebHeroKat__video,
      .zpWebHeroKat__metric,
      .zpWebHeroKat__mini,
      .zpWebHeroKat__mockup,
      .zpSSCard,
      .zpSSCard__mock,
      .zpTrustCert__score,
      .zpTrustCert__quote,
      .zpProcessFlow__track,
      .zpProcessFlow__card
    ){
      will-change:auto!important;
    }

    <?php if ($mobile_static): ?>
    @media (max-width: 900px){
      html body.zpKatPerfMobileStatic #zpStronyKatowice :is(
        .zpWebHeroKat__watermark,
        .zpWebHeroKat__orbital,
        .zpWebHeroKat__glow,
        .zpWebHeroKat__metric,
        .zpWebHeroKat__mini,
        .zpWebHeroKat__mockup,
        .zpSSMockAura,
        .zpSSBadge,
        .zpTrustCert__watermark,
        .zpTrustCert__scoreGlow,
        .zpTrustCert__bubble,
        .zpProcessFlow__watermark,
        .zpProcessFlow__person,
        .zpProcessFlow__mobileHint i,
        .zpKatSeoBoost__watermark
      ),
      html body.zpKatPerfMobileStatic #zpStronyKatowice :is(
        .zpWebHeroKat__watermark,
        .zpWebHeroKat__orbital,
        .zpWebHeroKat__glow,
        .zpWebHeroKat__metric,
        .zpWebHeroKat__mini,
        .zpWebHeroKat__mockup,
        .zpSSMockAura,
        .zpSSBadge,
        .zpTrustCert__watermark,
        .zpTrustCert__scoreGlow,
        .zpTrustCert__bubble,
        .zpProcessFlow__watermark,
        .zpProcessFlow__person,
        .zpProcessFlow__mobileHint i,
        .zpKatSeoBoost__watermark
      )::before,
      html body.zpKatPerfMobileStatic #zpStronyKatowice :is(
        .zpWebHeroKat__watermark,
        .zpWebHeroKat__orbital,
        .zpWebHeroKat__glow,
        .zpWebHeroKat__metric,
        .zpWebHeroKat__mini,
        .zpWebHeroKat__mockup,
        .zpSSMockAura,
        .zpSSBadge,
        .zpTrustCert__watermark,
        .zpTrustCert__scoreGlow,
        .zpTrustCert__bubble,
        .zpProcessFlow__watermark,
        .zpProcessFlow__person,
        .zpProcessFlow__mobileHint i,
        .zpKatSeoBoost__watermark
      )::after{
        animation:none!important;
      }

      html body.zpKatPerfMobileStatic #zpStronyKatowice :is(
        [data-zp-reveal],
        .zpWebHeroKat__copy,
        .zpWebHeroKat__visual,
        .zpSSCard,
        .zpTrustCert__copy,
        .zpTrustCert__score,
        .zpTrustCert__quote,
        .zpProcessFlow__head,
        .zpKatSeoBoost__panel,
        .zpKatSeoBoost__card,
        .zpIndustryCard,
        .zpPkgCard,
        .zpFaqItem
      ){
        opacity:1!important;
        transform:none!important;
        filter:none!important;
        transition:none!important;
      }

      html body.zpKatPerfMobileStatic #zpStronyKatowice :is(
        a,button,.zpWebHeroKat__btn,.zpSSBtn,.zpKatSeoBoost__link,.zpPkgCard__btn,.zpFaqItem__q
      ){
        transition-duration:.14s!important;
      }
    }
    <?php endif; ?>

    <?php if ($respect_rm): ?>
    @media (prefers-reduced-motion: reduce){
      html body.zpKatPerfLite #zpStronyKatowice *,
      html body.zpKatPerfLite #zpStronyKatowice *::before,
      html body.zpKatPerfLite #zpStronyKatowice *::after{
        animation-duration:.001ms!important;
        animation-iteration-count:1!important;
        transition-duration:.001ms!important;
        scroll-behavior:auto!important;
      }
    }
    <?php endif; ?>
  </style>
  <?php
}, 2147483647);
