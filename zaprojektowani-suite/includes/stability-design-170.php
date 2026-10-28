<?php
if (!defined('ABSPATH')) { exit; }

/* =========================================================
 * ZAPROJEKTOWANI SUITE v1.7.0
 * CMS Stabilization + Design System + Analytics Pro
 * ========================================================= */

function zp_suite_170_design_defaults(){
  return [
    'navy' => '#071426',
    'navy_dark' => '#05070b',
    'navy_mid' => '#102a4f',
    'blue' => '#1c477a',
    'light_bg' => '#f6f8fb',
    'text' => '#071426',
    'radius_button' => '999px',
    'radius_card' => '24px',
    'button_style' => 'unified',
    'animations' => '1',
    'safe_mode' => '0',
    'editor_mode' => 'production',
    'no_pink_guard' => '1',
  ];
}

function zp_suite_170_design(){
  $saved = get_option('zp_suite_design_system', []);
  if (!is_array($saved)) $saved = [];
  return array_merge(zp_suite_170_design_defaults(), $saved);
}

/* ---------- Front global design system ---------- */
add_action('wp_head', function(){
  if (is_admin()) return;
  $d = zp_suite_170_design();
  $navy = esc_html($d['navy']); $dark = esc_html($d['navy_dark']); $mid = esc_html($d['navy_mid']); $blue = esc_html($d['blue']); $radius = esc_html($d['radius_button']);
  ?>
  <script id="zp-safe-loader-head">document.documentElement.classList.add('zp-loading');</script>
  <style id="zp-suite-design-system-front">
    :root{--zpDSNavy:<?php echo $navy; ?>;--zpDSDark:<?php echo $dark; ?>;--zpDSMid:<?php echo $mid; ?>;--zpDSBlue:<?php echo $blue; ?>;--zpDSGrad:linear-gradient(90deg,<?php echo $dark; ?> 0%,#0b1830 42%,<?php echo $mid; ?> 72%,<?php echo $blue; ?> 100%);--zpDSRadius:<?php echo $radius; ?>;}
    html.zp-loading body{background:#fff;}
    body [class^="zp"] a[class*="btn"],body [class*=" zp"] a[class*="btn"],body [class^="zp"] button,body [class*=" zp"] button,.zp404__btn,.zpNewNav__cta,.zpNewHero__btn,.zpServicesPath__btn,.zpContactSystemLight__submit{border-radius:var(--zpDSRadius)!important;transition:color .22s ease,border-color .22s ease,background .22s ease,filter .22s ease!important;transform:none!important;box-shadow:none!important;}
    body [class^="zp"] a[class*="btn"]:hover,body [class*=" zp"] a[class*="btn"]:hover,body [class^="zp"] button:hover,body [class*=" zp"] button:hover,.zp404__btn:hover,.zpNewNav__cta:hover,.zpNewHero__btn:hover,.zpServicesPath__btn:hover,.zpContactSystemLight__submit:hover{transform:none!important;box-shadow:none!important;}
    .zpNewNav__cta,.zpNewHero__btn--primary,.zpServicesPath__btn,.zp404__btn--primary,.zpHomeAuditCta__btn,.zpContactSystemLight__submit{background:#fff!important;color:var(--zpDSNavy)!important;border-color:rgba(7,20,38,.18)!important;position:relative!important;overflow:hidden!important;}
    .zpNewNav__cta:before,.zpNewHero__btn--primary:before,.zpServicesPath__btn:before,.zp404__btn--primary:before,.zpHomeAuditCta__btn:before,.zpContactSystemLight__submit:before{content:"";position:absolute;inset:0;z-index:-1;background:var(--zpDSGrad);transform:scaleX(0);transform-origin:left center;transition:transform .28s cubic-bezier(.16,1,.3,1);}
    .zpNewNav__cta:hover:before,.zpNewHero__btn--primary:hover:before,.zpServicesPath__btn:hover:before,.zp404__btn--primary:hover:before,.zpHomeAuditCta__btn:hover:before,.zpContactSystemLight__submit:hover:before{transform:scaleX(1);}
    .zpNewNav__cta:hover,.zpNewHero__btn--primary:hover,.zpServicesPath__btn:hover,.zp404__btn--primary:hover,.zpHomeAuditCta__btn:hover,.zpContactSystemLight__submit:hover{color:#fff!important;border-color:var(--zpDSMid)!important;background:var(--zpDSDark)!important;}
    .zpNewHero__btn--ghost,.zp404__btn--ghost{background:rgba(255,255,255,.07)!important;color:#fff!important;border-color:rgba(255,255,255,.20)!important;}
    .zpNewHero__btn--ghost:hover,.zp404__btn--ghost:hover{background:var(--zpDSGrad)!important;color:#fff!important;border-color:var(--zpDSMid)!important;}
    <?php if (($d['no_pink_guard'] ?? '1') === '1'): ?>
    body [class^="zp"] *:where(a,area,button,input,select,textarea,summary,option,iframe,object,embed,audio,video,dialog,[tabindex],[contenteditable]):focus,body [class*=" zp"] *:where(a,area,button,input,select,textarea,summary,option,iframe,object,embed,audio,video,dialog,[tabindex],[contenteditable]):focus,body [class^="zp"] *:where(a,area,button,input,select,textarea,summary,option,iframe,object,embed,audio,video,dialog,[tabindex],[contenteditable]):focus-visible,body [class*=" zp"] *:where(a,area,button,input,select,textarea,summary,option,iframe,object,embed,audio,video,dialog,[tabindex],[contenteditable]):focus-visible,body *:not(a,area,button,input,select,textarea,summary,option,iframe,object,embed,audio,video,dialog,[tabindex],[contenteditable]):focus,body *:not(a,area,button,input,select,textarea,summary,option,iframe,object,embed,audio,video,dialog,[tabindex],[contenteditable]):focus-visible{outline-color:var(--zpDSMid)!important;box-shadow:0 0 0 3px rgba(16,42,79,.16)!important;border-color:rgba(16,42,79,.56)!important;}
    body [class^="zp"] .is-active,body [class*=" zp"] .is-active,body [class^="zp"] [aria-selected="true"],body [class*=" zp"] [aria-selected="true"]{--pink:var(--zpDSMid)!important;--accent:var(--zpDSMid)!important;}
    <?php endif; ?>
    <?php if (($d['animations'] ?? '1') !== '1' || ($d['safe_mode'] ?? '0') === '1'): ?>
    body [class^="zp"] *,body [class*=" zp"] *,body [class^="zp"] *:before,body [class*=" zp"] *:before,body [class^="zp"] *:after,body [class*=" zp"] *:after{animation:none!important;transition-duration:.001ms!important;scroll-behavior:auto!important;}
    <?php endif; ?>
  </style>
  <?php
}, 0);

add_action('wp_footer', function(){ if (is_admin()) return; ?><script id="zp-safe-loader-ready">(function(){function r(){document.documentElement.classList.remove('zp-loading');document.documentElement.classList.add('zp-ready')}if(document.readyState==='loading'){document.addEventListener('DOMContentLoaded',r,{once:true})}else{r()}window.addEventListener('load',r,{once:true})})();</script><?php }, 999);

