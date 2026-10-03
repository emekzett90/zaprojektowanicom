<?php
if (!defined('ABSPATH')) { exit; }

/**
 * ZP Suite v2.2.213 — Home Performance Lite, etap 1.
 * Bezpieczne odciążenie home: blur/backdrop-filter, ciężkie animacje mobile,
 * uproszczenie reveal’i i lżejsze cienie bez zmiany układu strony.
 */

function zp_suite_perf_bool_213($path, $default = '1') {
  return (string) zp_suite_opt($path, $default) === '1';
}

function zp_suite_is_home_perf_lite_213() {
  return zp_suite_perf_bool_213('performance.home_lite_enabled', '1');
}

add_filter('body_class', function($classes){
  if (is_admin()) return $classes;
  if ((is_front_page() || is_home()) && zp_suite_is_home_perf_lite_213()) {
    $classes[] = 'zpHomePerfLite';
    if (zp_suite_perf_bool_213('performance.home_lite_mobile_enabled', '1')) $classes[] = 'zpHomePerfLiteMobile';
    if (zp_suite_perf_bool_213('performance.disable_blur', '1')) $classes[] = 'zpHomePerfNoBlur';
    if (zp_suite_perf_bool_213('performance.reduce_reveals', '1')) $classes[] = 'zpHomePerfSimpleReveal';
    if (zp_suite_perf_bool_213('performance.reduce_shadows_watermarks', '1')) $classes[] = 'zpHomePerfLightDecor';
    if (zp_suite_perf_bool_213('performance.disable_mobile_heavy_motion', '1')) $classes[] = 'zpHomePerfMobileStatic';
  }
  return $classes;
}, 30);

add_action('wp_head', function(){
  if (is_admin()) return;
  if (!(is_front_page() || is_home())) return;
  if (!zp_suite_is_home_perf_lite_213()) return;

  $no_blur = zp_suite_perf_bool_213('performance.disable_blur', '1');
  $simple_reveal = zp_suite_perf_bool_213('performance.reduce_reveals', '1');
  $light_decor = zp_suite_perf_bool_213('performance.reduce_shadows_watermarks', '1');
  $mobile_static = zp_suite_perf_bool_213('performance.disable_mobile_heavy_motion', '1');
  $respect_rm = zp_suite_perf_bool_213('performance.respect_reduced_motion', '1');
  ?>
  <style id="zp-suite-home-performance-lite-213">
    /* ZP Home Performance Lite — etap 1: układ zostaje, GPU ma mniej pracy. */
    html body.zpHomePerfLite.home,
    html body.zpHomePerfLite.front-page{
      scroll-behavior:auto;
    }

    <?php if ($no_blur): ?>
    html body.zpHomePerfNoBlur.home :is(
      .zpNewHero__reviewCard,
      .zpNewHero__btn,
      .zpNewHero__chip,
      .zpHomeAuditCta__panel,
      .zpContactFormLight__card,
      .zpContactFormLight__contactType,
      .zpContactFormLight__serviceChip,
      .zpHomeFaqKatNavy__sideCard,
      .zpShowcaseWhite__tab,
      .zpShowcaseWhite__card,
      .zpRevEd__featured,
      .zpRevEd__quote,
      .zpRevEd__platform,
      .zpServicesPath__card,
      .zpLaptopShowcase__card,
      .zpSeoIndustries__card
    ),
    html body.zpHomePerfNoBlur.front-page :is(
      .zpNewHero__reviewCard,
      .zpNewHero__btn,
      .zpNewHero__chip,
      .zpHomeAuditCta__panel,
      .zpContactFormLight__card,
      .zpContactFormLight__contactType,
      .zpContactFormLight__serviceChip,
      .zpHomeFaqKatNavy__sideCard,
      .zpShowcaseWhite__tab,
      .zpShowcaseWhite__card,
      .zpRevEd__featured,
      .zpRevEd__quote,
      .zpRevEd__platform,
      .zpServicesPath__card,
      .zpLaptopShowcase__card,
      .zpSeoIndustries__card
    ){
      -webkit-backdrop-filter:none!important;
      backdrop-filter:none!important;
    }

    html body.zpHomePerfNoBlur.home :is(.zpNewHero__glow,.zpNewHero__orb,.zpNewHero__videoGlow,.zpRevEd__aura,.zpShowcaseWhite__glow,.zpLaptopShowcase__glow,.zpServicesPath__glow),
    html body.zpHomePerfNoBlur.front-page :is(.zpNewHero__glow,.zpNewHero__orb,.zpNewHero__videoGlow,.zpRevEd__aura,.zpShowcaseWhite__glow,.zpLaptopShowcase__glow,.zpServicesPath__glow){
      filter:none!important;
    }
    <?php endif; ?>

    <?php if ($light_decor): ?>
    /* v2.2.582: karty sekcji opinii (.zpRevEd__featured/.zpRevEd__quote) wyjęte z tej reguły —
       sekcja opinii ma być całkowicie bez drop-shadow. */
    html body.zpHomePerfLightDecor.home :is(.zpNewHero__reviewCard,.zpShowcaseWhite__card,.zpHomeAuditCta__panel,.zpContactFormLight__card),
    html body.zpHomePerfLightDecor.front-page :is(.zpNewHero__reviewCard,.zpShowcaseWhite__card,.zpHomeAuditCta__panel,.zpContactFormLight__card){
      box-shadow:0 16px 44px rgba(7,20,38,.08)!important;
    }
    html body.zpHomePerfLightDecor.home :is(.zpRevEd__featured,.zpRevEd__quote),
    html body.zpHomePerfLightDecor.front-page :is(.zpRevEd__featured,.zpRevEd__quote){
      box-shadow:none!important;
    }

    html body.zpHomePerfLightDecor.home :is(.zpHomeFaqKatNavy__watermark,.zpRevEd__watermark,.zpShowcaseWhite__watermark,.zpLaptopShowcase__watermark),
    html body.zpHomePerfLightDecor.front-page :is(.zpHomeFaqKatNavy__watermark,.zpRevEd__watermark,.zpShowcaseWhite__watermark,.zpLaptopShowcase__watermark){
      opacity:.018!important;
      animation:none!important;
    }
    <?php endif; ?>

    <?php if ($simple_reveal): ?>
    html body.zpHomePerfSimpleReveal.home :is(
      .zpHomeFaqKatNavy__copy,.zpHomeFaqKatNavy__lead,.zpHomeFaqKatNavy__side,.zpHomeFaqKatNavy__items,
      .zpServicesPath__head,.zpServicesPath__card,.zpShowcaseWhite__head,.zpShowcaseWhite__item,
      .zpLaptopShowcase__copy,.zpLaptopShowcase__visual,.zpRevEd__head,.zpRevEd__featured,.zpRevEd__quote,
      .zpSeoIndustries__head,.zpSeoIndustries__card,.zpHomeSeoIntro__copy,.zpHomeSeoIntro__links
    ),
    html body.zpHomePerfSimpleReveal.front-page :is(
      .zpHomeFaqKatNavy__copy,.zpHomeFaqKatNavy__lead,.zpHomeFaqKatNavy__side,.zpHomeFaqKatNavy__items,
      .zpServicesPath__head,.zpServicesPath__card,.zpShowcaseWhite__head,.zpShowcaseWhite__item,
      .zpLaptopShowcase__copy,.zpLaptopShowcase__visual,.zpRevEd__head,.zpRevEd__featured,.zpRevEd__quote,
      .zpSeoIndustries__head,.zpSeoIndustries__card,.zpHomeSeoIntro__copy,.zpHomeSeoIntro__links
    ){
      transition-duration:.28s!important;
      transition-delay:0s!important;
      filter:none!important;
      will-change:auto!important;
    }
    <?php endif; ?>

    html body.zpHomePerfLite.home :is(.zpNewHero__reviewCard,.zpShowcaseWhite__card,.zpRevEd__featured,.zpRevEd__quote,.zpLaptopShowcase__card,.zpServicesPath__card),
    html body.zpHomePerfLite.front-page :is(.zpNewHero__reviewCard,.zpShowcaseWhite__card,.zpRevEd__featured,.zpRevEd__quote,.zpLaptopShowcase__card,.zpServicesPath__card){
      will-change:auto!important;
    }

    <?php if ($mobile_static): ?>
    @media (max-width: 900px){
      html body.zpHomePerfMobileStatic.home :is(
        .zpNewHero__reviewCard,.zpNewHero__person,.zpNewHero__signature,
        .zpRevEd__line,.zpRevEd__scoreAura,.zpRevEd__digit,.zpRevEd__hint,.zpRevEd__featured::before,.zpRevEd__quote::before,
        .zpShowcaseWhite__hint i::after,.zpShowcaseWhite__tap,.zpShowcaseWhite__glow,
        .zpLaptopShowcase__chip,.zpLaptopShowcase__mockup,.zpHomeFaqKatNavy__watermark
      ),
      html body.zpHomePerfMobileStatic.front-page :is(
        .zpNewHero__reviewCard,.zpNewHero__person,.zpNewHero__signature,
        .zpRevEd__line,.zpRevEd__scoreAura,.zpRevEd__digit,.zpRevEd__hint,.zpRevEd__featured::before,.zpRevEd__quote::before,
        .zpShowcaseWhite__hint i::after,.zpShowcaseWhite__tap,.zpShowcaseWhite__glow,
        .zpLaptopShowcase__chip,.zpLaptopShowcase__mockup,.zpHomeFaqKatNavy__watermark
      ){
        animation:none!important;
      }

      html body.zpHomePerfMobileStatic.home :is(
        .zpHomeFaqKatNavy__copy,.zpHomeFaqKatNavy__lead,.zpHomeFaqKatNavy__side,.zpHomeFaqKatNavy__items,
        .zpServicesPath__head,.zpServicesPath__card,.zpShowcaseWhite__head,.zpShowcaseWhite__item,
        .zpLaptopShowcase__copy,.zpLaptopShowcase__visual,.zpRevEd__head,.zpRevEd__featured,.zpRevEd__quote,
        .zpSeoIndustries__head,.zpSeoIndustries__card,.zpHomeSeoIntro__copy,.zpHomeSeoIntro__links
      ),
      html body.zpHomePerfMobileStatic.front-page :is(
        .zpHomeFaqKatNavy__copy,.zpHomeFaqKatNavy__lead,.zpHomeFaqKatNavy__side,.zpHomeFaqKatNavy__items,
        .zpServicesPath__head,.zpServicesPath__card,.zpShowcaseWhite__head,.zpShowcaseWhite__item,
        .zpLaptopShowcase__copy,.zpLaptopShowcase__visual,.zpRevEd__head,.zpRevEd__featured,.zpRevEd__quote,
        .zpSeoIndustries__head,.zpSeoIndustries__card,.zpHomeSeoIntro__copy,.zpHomeSeoIntro__links
      ){
        opacity:1!important;
        transform:none!important;
        filter:none!important;
        transition:none!important;
      }

      html body.zpHomePerfMobileStatic.home :is(a,button,.zpNewHero__btn,.zpShowcaseWhite__card,.zpRevEd__featured,.zpRevEd__quote,.zpServicesPath__card,.zpContactFormLight__serviceChip),
      html body.zpHomePerfMobileStatic.front-page :is(a,button,.zpNewHero__btn,.zpShowcaseWhite__card,.zpRevEd__featured,.zpRevEd__quote,.zpServicesPath__card,.zpContactFormLight__serviceChip){
        transition-duration:.14s!important;
      }
    }
    <?php endif; ?>

    <?php if ($respect_rm): ?>
    @media (prefers-reduced-motion: reduce){
      html body.zpHomePerfLite.home *,
      html body.zpHomePerfLite.home *::before,
      html body.zpHomePerfLite.home *::after,
      html body.zpHomePerfLite.front-page *,
      html body.zpHomePerfLite.front-page *::before,
      html body.zpHomePerfLite.front-page *::after{
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
