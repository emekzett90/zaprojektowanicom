<?php
if (!defined('ABSPATH')) { exit; }

/**
 * ZP Suite v2.2.215 — Performance Lite dla /sklepy-internetowe-katowice/.
 * Bez zmiany layoutu: mniej pracy GPU na najcięższej podstronie e-commerce.
 */

if (!function_exists('zp_suite_perf_bool_215')) {
  function zp_suite_perf_bool_215($path, $default = '1') {
    return (string) zp_suite_opt($path, $default) === '1';
  }
}

function zp_suite_is_shop_perf_page_215() {
  if (is_admin()) { return false; }
  return is_page('sklepy-internetowe-katowice') || (isset($_SERVER['REQUEST_URI']) && strpos((string)$_SERVER['REQUEST_URI'], '/sklepy-internetowe-katowice') !== false);
}

function zp_suite_is_shop_perf_lite_215() {
  return zp_suite_perf_bool_215('performance.shop_katowice_lite_enabled', '1');
}

add_filter('body_class', function($classes){
  if (zp_suite_is_shop_perf_page_215() && zp_suite_is_shop_perf_lite_215()) {
    $classes[] = 'zpShopPerfLite';
    if (zp_suite_perf_bool_215('performance.shop_katowice_lite_mobile_enabled', '1')) $classes[] = 'zpShopPerfLiteMobile';
    if (zp_suite_perf_bool_215('performance.shop_katowice_disable_blur', '1')) $classes[] = 'zpShopPerfNoBlur';
    if (zp_suite_perf_bool_215('performance.shop_katowice_reduce_reveals', '1')) $classes[] = 'zpShopPerfSimpleReveal';
    if (zp_suite_perf_bool_215('performance.shop_katowice_reduce_shadows_watermarks', '1')) $classes[] = 'zpShopPerfLightDecor';
    if (zp_suite_perf_bool_215('performance.shop_katowice_disable_mobile_heavy_motion', '1')) $classes[] = 'zpShopPerfMobileStatic';
  }
  return $classes;
}, 32);

add_action('wp_head', function(){
  if (!zp_suite_is_shop_perf_page_215()) return;
  if (!zp_suite_is_shop_perf_lite_215()) return;

  $no_blur = zp_suite_perf_bool_215('performance.shop_katowice_disable_blur', '1');
  $simple_reveal = zp_suite_perf_bool_215('performance.shop_katowice_reduce_reveals', '1');
  $light_decor = zp_suite_perf_bool_215('performance.shop_katowice_reduce_shadows_watermarks', '1');
  $mobile_static = zp_suite_perf_bool_215('performance.shop_katowice_disable_mobile_heavy_motion', '1');
  $respect_rm = zp_suite_perf_bool_215('performance.shop_katowice_respect_reduced_motion', '1');
  ?>
  <style id="zp-suite-shop-katowice-performance-lite-215">
    /* ZP /sklepy-internetowe-katowice/ Performance Lite — etap 1. */
    html body.zpShopPerfLite #zp-sklepy-internetowe-katowice{
      scroll-behavior:auto;
    }

    <?php if ($no_blur): ?>
    html body.zpShopPerfNoBlur #zp-sklepy-internetowe-katowice :is(
      .zpShopCockpit__btn,
      .zpShopCockpit__proofPill,
      .zpShopCockpit__offerCard,
      .zpShopCockpit__pill,
      .zpShopWhyLight__introCard,
      .zpShopWhyLight__floatCard,
      .zpShopWhyLight__chip,
      .zpEcomTrust__score,
      .zpEcomTrust__proofPanel,
      .zpEcomTrust__review,
      .zpShopCard,
      .zpShopPortfolio__cta,
      .zpShopSeoBoost__card,
      .zpShopSeoBoost__cta,
      .zpShopSeoBoost__row,
      .zpShopProcessFlow__card,
      .zpShopProcessFlow__topBar,
      .zpShopProcessFlow__satisfaction,
      .zpEcomIndustries__card,
      .zpShopPackage,
      .zpFaqShopKatNavy__item,
      .zpFaqShopKatNavy__sideCard,
      .zpContactFormLight__card,
      .zpContactFormLight__contactType,
      .zpContactFormLight__serviceChip
    ){
      -webkit-backdrop-filter:none!important;
      backdrop-filter:none!important;
    }

    html body.zpShopPerfNoBlur #zp-sklepy-internetowe-katowice :is(
      .zpShopCockpit__halo,
      .zpShopCockpit__ring,
      .zpShopCockpit__ambient,
      .zpShopWhyLight__glow,
      .zpShopWhyLight__mockWrap::before,
      .zpEcomTrust__orb,
      .zpEcomTrust__scoreGlow,
      .zpShopProcessFlow__bleed,
      .zpShopProcessFlow__rocketGlow,
      .zpShopSeoBoost__cta::before,
      .zpEcomIndustries__orbit,
      .zpEcomIndustries__ring,
      .zpFaqShopKatNavy__bleed
    ){
      filter:none!important;
      -webkit-filter:none!important;
    }
    <?php endif; ?>

    <?php if ($light_decor): ?>
    html body.zpShopPerfLightDecor #zp-sklepy-internetowe-katowice :is(
      .zpShopCockpit__offerCard,
      .zpShopWhyLight__introCard,
      .zpShopWhyLight__floatCard,
      .zpEcomTrust__score,
      .zpEcomTrust__proofPanel,
      .zpEcomTrust__review,
      .zpShopCard,
      .zpShopSeoBoost__card,
      .zpShopSeoBoost__cta,
      .zpShopProcessFlow__card,
      .zpEcomIndustries__card,
      .zpShopPackage,
      .zpFaqShopKatNavy__item,
      .zpContactFormLight__card
    ){
      box-shadow:0 16px 44px rgba(5,12,24,.12)!important;
    }

    html body.zpShopPerfLightDecor #zp-sklepy-internetowe-katowice :is(
      .zpShopCockpit__watermark,
      .zpShopPortfolio__bigWord,
      .zpEcomTrust__watermark,
      .zpEcomTrust__watermark--top,
      .zpEcomTrust__watermark--bottom,
      .zpShopWhyLight__watermark,
      .zpShopProcessFlow__bleed,
      .zpShopSeoBoost__watermark,
      .zpEcomIndustries__watermark,
      .zpShopPackages__watermark,
      .zpFaqShopKatNavy__watermark
    ){
      opacity:.018!important;
      animation:none!important;
    }

    html body.zpShopPerfLightDecor #zp-sklepy-internetowe-katowice :is(
      .zpShopCockpit__video,
      .zpShopCockpit__videoWrap,
      .zpShopCockpit__ambient,
      .zpEcomTrust__orb,
      .zpFaqShopKatNavy__bleed
    ){
      opacity:.72!important;
    }
    <?php endif; ?>

    <?php if ($simple_reveal): ?>
    html body.zpShopPerfSimpleReveal #zp-sklepy-internetowe-katowice :is(
      [data-zp-reveal],
      .zpShopCockpit__eyebrow,
      .zpShopCockpit h1,
      .zpShopCockpit__lead,
      .zpShopCockpit__actions,
      .zpShopCockpit__proof,
      .zpShopCockpit__mockFrame,
      .zpShopCockpit__wooLogo,
      .zpShopCockpit__offerCard,
      .zpShopCockpit__pill,
      .zpShopWhyLight__copy,
      .zpShopWhyLight__visual,
      .zpEcomTrust__copy,
      .zpEcomTrust__score,
      .zpEcomTrust__review,
      .zpShopPortfolio__head,
      .zpShopCard,
      .zpShopSeoBoost__head,
      .zpShopSeoBoost__card,
      .zpShopProcessFlow__head,
      .zpShopProcessFlow__card,
      .zpEcomIndustries__card,
      .zpShopPackage,
      .zpFaqShopKatNavy__item
    ){
      transition-duration:.24s!important;
      transition-delay:0s!important;
      filter:none!important;
      will-change:auto!important;
    }
    <?php endif; ?>

    html body.zpShopPerfLite #zp-sklepy-internetowe-katowice :is(
      .zpShopCockpit__mockFrame,
      .zpShopCockpit__mockImg,
      .zpShopCockpit__offerCard,
      .zpShopCockpit__pill,
      .zpShopWhyLight__mock,
      .zpShopWhyLight__floatCard,
      .zpShopPortfolio__stack,
      .zpShopCard,
      .zpShopProcessFlow__track,
      .zpShopProcessFlow__card,
      .zpEcomTrust__reviewsBoard,
      .zpEcomTrust__review
    ){
      will-change:auto!important;
    }

    <?php if ($mobile_static): ?>
    @media (max-width: 900px){
      html body.zpShopPerfMobileStatic #zp-sklepy-internetowe-katowice :is(
        .zpShopCockpit__halo,
        .zpShopCockpit__ring,
        .zpShopCockpit__ambient,
        .zpShopCockpit__watermark,
        .zpShopCockpit__wooLogo,
        .zpShopCockpit__offerCard,
        .zpShopCockpit__pill,
        .zpShopWhyLight__watermark,
        .zpShopWhyLight__glow,
        .zpShopWhyLight__floatCard,
        .zpEcomTrust__orb,
        .zpEcomTrust__watermark,
        .zpEcomTrust__scoreGlow,
        .zpEcomTrust__review,
        .zpShopPortfolio__bigWord,
        .zpShopPortfolio__swipeHint i,
        .zpShopSeoBoost__watermark,
        .zpShopProcessFlow__bleed,
        .zpShopProcessFlow__person,
        .zpShopProcessFlow__rocketGlow,
        .zpShopProcessFlow__rocketIcon,
        .zpShopProcessFlow__mobileHint i,
        .zpEcomIndustries__orbit,
        .zpEcomIndustries__ring,
        .zpEcomIndustries__watermark,
        .zpShopPackages__watermark,
        .zpFaqShopKatNavy__watermark
      ),
      html body.zpShopPerfMobileStatic #zp-sklepy-internetowe-katowice :is(
        .zpShopCockpit__halo,
        .zpShopCockpit__ring,
        .zpShopCockpit__ambient,
        .zpShopCockpit__watermark,
        .zpShopCockpit__wooLogo,
        .zpShopCockpit__offerCard,
        .zpShopCockpit__pill,
        .zpShopWhyLight__watermark,
        .zpShopWhyLight__glow,
        .zpShopWhyLight__floatCard,
        .zpEcomTrust__orb,
        .zpEcomTrust__watermark,
        .zpEcomTrust__scoreGlow,
        .zpEcomTrust__review,
        .zpShopPortfolio__bigWord,
        .zpShopPortfolio__swipeHint i,
        .zpShopSeoBoost__watermark,
        .zpShopProcessFlow__bleed,
        .zpShopProcessFlow__person,
        .zpShopProcessFlow__rocketGlow,
        .zpShopProcessFlow__rocketIcon,
        .zpShopProcessFlow__mobileHint i,
        .zpEcomIndustries__orbit,
        .zpEcomIndustries__ring,
        .zpEcomIndustries__watermark,
        .zpShopPackages__watermark,
        .zpFaqShopKatNavy__watermark
      )::before,
      html body.zpShopPerfMobileStatic #zp-sklepy-internetowe-katowice :is(
        .zpShopCockpit__halo,
        .zpShopCockpit__ring,
        .zpShopCockpit__ambient,
        .zpShopCockpit__watermark,
        .zpShopCockpit__wooLogo,
        .zpShopCockpit__offerCard,
        .zpShopCockpit__pill,
        .zpShopWhyLight__watermark,
        .zpShopWhyLight__glow,
        .zpShopWhyLight__floatCard,
        .zpEcomTrust__orb,
        .zpEcomTrust__watermark,
        .zpEcomTrust__scoreGlow,
        .zpEcomTrust__review,
        .zpShopPortfolio__bigWord,
        .zpShopPortfolio__swipeHint i,
        .zpShopSeoBoost__watermark,
        .zpShopProcessFlow__bleed,
        .zpShopProcessFlow__person,
        .zpShopProcessFlow__rocketGlow,
        .zpShopProcessFlow__rocketIcon,
        .zpShopProcessFlow__mobileHint i,
        .zpEcomIndustries__orbit,
        .zpEcomIndustries__ring,
        .zpEcomIndustries__watermark,
        .zpShopPackages__watermark,
        .zpFaqShopKatNavy__watermark
      )::after{
        animation:none!important;
      }

      html body.zpShopPerfMobileStatic #zp-sklepy-internetowe-katowice :is(
        [data-zp-reveal],
        .zpShopCockpit__eyebrow,
        .zpShopCockpit h1,
        .zpShopCockpit__lead,
        .zpShopCockpit__actions,
        .zpShopCockpit__proof,
        .zpShopCockpit__content,
        .zpShopCockpit__visual,
        .zpShopWhyLight__copy,
        .zpShopWhyLight__visual,
        .zpEcomTrust__copy,
        .zpEcomTrust__score,
        .zpEcomTrust__review,
        .zpShopPortfolio__head,
        .zpShopSeoBoost__head,
        .zpShopSeoBoost__card,
        .zpShopProcessFlow__head,
        .zpEcomIndustries__card,
        .zpShopPackage,
        .zpFaqShopKatNavy__item
      ){
        opacity:1!important;
        transform:none!important;
        filter:none!important;
        transition:none!important;
      }

      html body.zpShopPerfMobileStatic #zp-sklepy-internetowe-katowice :is(
        a,button,.zpShopCockpit__btn,.zpShopWhyLight__btn,.zpShopPortfolio__btn,.zpShopSeoBoost__btn,.zpShopProcessFlow__cta,.zpShopPackages__btn,.zpFaqShopKatNavy__question
      ){
        transition-duration:.14s!important;
      }
    }
    <?php endif; ?>

    <?php if ($respect_rm): ?>
    @media (prefers-reduced-motion: reduce){
      html body.zpShopPerfLite #zp-sklepy-internetowe-katowice *,
      html body.zpShopPerfLite #zp-sklepy-internetowe-katowice *::before,
      html body.zpShopPerfLite #zp-sklepy-internetowe-katowice *::after{
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
