<?php
/**
 * ZP Suite v2.2.417 — mobile/header/text polish.
 * - Shop H1 emphasis normalized to one-step +100 weight (no double bold).
 * - Logo & Branding mobile mockup can bleed edge-to-edge and above its visual box without clipping.
 * - /strony-internetowe-katowice/ desktop: right mockup enlarged and nudged right, no size flash.
 */
if (!defined('ABSPATH')) { exit; }

function zp_suite_239_final_visual_hardfix_css(){ ?>
<style id="zp-suite-239-final-visual-hardfix">html body #zpShopHeroKat h1.zpServiceHeroH1 > .zpH1Emphasis,html body #zpShopHeroKat h1.zpServiceHeroH1 .zpH1Emphasis,html body #zpSklepyKatowice #zpShopHeroKat h1.zpServiceHeroH1 > .zpH1Emphasis,html body #zpSklepyKatowice #zpShopHeroKat h1.zpServiceHeroH1 .zpH1Emphasis,html body #zpShopHeroKatTitle > .zpH1Emphasis,html body #zpShopHeroKatTitle .zpH1Emphasis{font-family:"Plus Jakarta Sans Local","Plus Jakarta Sans","Outfit",system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif!important;font-weight:560!important;font-variation-settings:"wght" 560!important;letter-spacing:inherit!important;color:inherit!important;text-rendering:geometricPrecision!important;font-synthesis:none!important;-webkit-font-smoothing:antialiased!important}@media (max-width:760px){html body #zpBrandHeroKatSafe,html body #zpBrandHeroKatSafe.zpBrandHeroSafe{--mockWidth:118vw!important;--mockX:0px!important;--mockScale:1.04!important}html body #zpBrandHeroKatSafe .zpBrandHeroSafe__visual{width:100vw!important;max-width:100vw!important;min-height:560px!important;margin-left:calc(50% - 50vw)!important;margin-right:calc(50% - 50vw)!important;padding-top:0!important}html body #zpBrandHeroKatSafe .zpBrandHeroSafe__stage{position:absolute!important;inset:-190px 0 -70px 0!important;width:100vw!important;max-width:100vw!important}html body #zpBrandHeroKatSafe .zpBrandHeroSafe__mockFrame,html body #zpBrandHeroKatSafe[data-ready="0"] .zpBrandHeroSafe__mockFrame,html body #zpBrandHeroKatSafe[data-ready="1"] .zpBrandHeroSafe__mockFrame{width:var(--mockWidth)!important;max-width:none!important;left:50%!important;top:50%!important;transform:translate(calc(-50% + var(--mockX)),calc(-50% + var(--mockY))) rotate(var(--mockRotate)) scale(var(--mockScale))!important}html body #zpBrandHeroKatSafe .zpBrandHeroSafe__mockImg{display:block!important;width:100%!important;max-width:none!important;height:auto!important;object-fit:contain!important}}@media (min-width:981px){html body #zpStronyKatowice #zpWebHeroKat .zpWebHeroKat__inner,html body #zpWebHeroKat.zpWebHeroKat .zpWebHeroKat__inner{grid-template-columns:minmax(520px,.84fr) minmax(610px,1.16fr)!important;gap:clamp(18px,2.15vw,42px)!important}html body #zpStronyKatowice #zpWebHeroKat .zpWebHeroKat__content,html body #zpWebHeroKat.zpWebHeroKat .zpWebHeroKat__content{max-width:768px!important;width:100%!important}html body #zpStronyKatowice #zpWebHeroKat h1.zpServiceHeroH1,html body #zpWebHeroKat h1.zpServiceHeroH1{max-width:748px!important}html body #zpStronyKatowice #zpWebHeroKat .zpWebHeroKat__lead,html body #zpWebHeroKat .zpWebHeroKat__lead{max-width:720px!important}html body #zpStronyKatowice #zpWebHeroKat .zpWebHeroKat__visual,html body #zpWebHeroKat .zpWebHeroKat__visual{overflow:visible!important;contain:none!important}html body #zpStronyKatowice #zpWebHeroKat .zpWebHeroKat__visualStage,html body #zpWebHeroKat .zpWebHeroKat__visualStage,html body #zpWebHeroKat[data-ready="0"] .zpWebHeroKat__visualStage,html body #zpWebHeroKat:not([data-ready="1"]) .zpWebHeroKat__visualStage,html body #zpWebHeroKat[data-ready="1"] .zpWebHeroKat__visualStage{transform:translateX(130px) scale(1.58)!important;transform-origin:center right!important;transition:none!important;animation:none!important;opacity:1!important;visibility:visible!important}html body #zpStronyKatowice #zpWebHeroKat .zpWebHeroKat__mock,html body #zpWebHeroKat .zpWebHeroKat__mock,html body #zpWebHeroKat[data-ready="0"] .zpWebHeroKat__mock,html body #zpWebHeroKat:not([data-ready="1"]) .zpWebHeroKat__mock,html body #zpWebHeroKat[data-ready="1"] .zpWebHeroKat__mock{transform:none!important;transition:none!important;animation:none!important;width:112%!important;max-width:none!important;opacity:1!important;visibility:visible!important}html body #zpStronyKatowice #zpWebHeroKat .zpWebHeroKat__miniCard,html body #zpWebHeroKat .zpWebHeroKat__miniCard,html body #zpWebHeroKat[data-ready="0"] .zpWebHeroKat__miniCard,html body #zpWebHeroKat:not([data-ready="1"]) .zpWebHeroKat__miniCard,html body #zpWebHeroKat[data-ready="1"] .zpWebHeroKat__miniCard{transform:translate3d(0,40px,0) scale(.7)!important;transform-origin:top right!important;transition:none!important;animation:none!important;opacity:1!important;visibility:visible!important}}@media (min-width:1280px){html body #zpStronyKatowice #zpWebHeroKat .zpWebHeroKat__visualStage,html body #zpWebHeroKat .zpWebHeroKat__visualStage,html body #zpWebHeroKat[data-ready="0"] .zpWebHeroKat__visualStage,html body #zpWebHeroKat:not([data-ready="1"]) .zpWebHeroKat__visualStage,html body #zpWebHeroKat[data-ready="1"] .zpWebHeroKat__visualStage{transform:translateX(calc(10% + 130px)) scale(1.65)!important;transform-origin:center right!important}}@media (max-width:760px){html body #zpShopHeroKat h1.zpServiceHeroH1 .zpH1Emphasis,html body #zpShopHeroKatTitle .zpH1Emphasis{font-weight:660!important;font-variation-settings:"wght" 660!important}html body #zpBrandHeroKatSafe,html body #zpBrandHeroKatSafe.zpBrandHeroSafe{--mockY:-62px!important;overflow:visible!important;clip-path:none!important;contain:none!important}html body #zpBrandHeroKatSafe .zpBrandHeroSafe__inner,html body #zpBrandHeroKatSafe .zpBrandHeroSafe__visual,html body #zpBrandHeroKatSafe .zpBrandHeroSafe__stage,html body #zpBrandHeroKatSafe .zpBrandHeroSafe__mockFrame{overflow:visible!important;clip-path:none!important;contain:none!important}}@media (max-width:980px){html body{--zpNavH:82px!important}html body .zpNewNav,html body .zpNewNav.zpNewNav--heroOverlay,html body .zpNewNav.is-scrolled{top:var(--zpAdminTop,0px)!important;transform:none!important}html body .zpNewNav__shell,html body .zpNewNav.is-scrolled .zpNewNav__shell,html body .zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled) .zpNewNav__shell{height:82px!important;min-height:82px!important;padding:0!important;display:flex!important;align-items:center!important}html body .zpNewNav__mobileBar,html body .zpNewNav.is-scrolled .zpNewNav__mobileBar,html body .zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled) .zpNewNav__mobileBar{height:82px!important;min-height:82px!important;padding-top:0!important;padding-bottom:0!important;align-items:center!important;transform:none!important}html body .zpNewNav__mobileBrand,html body .zpNewNav__mobileActions{height:82px!important;min-height:82px!important;display:flex!important;align-items:center!important;transform:none!important}html body .zpNewNav__mobileLogo{height:38px!important;max-height:38px!important;display:block!important;object-fit:contain!important}html body .zpNewNav__mobileLogo--light{transform:translateX(var(--zp-admin-logo-mobile-x,0px)) translateY(0)!important}html body .zpNewNav__mobileLogo--dark{top:50%!important;transform:translateY(-50%) translateX(var(--zp-admin-logo-mobile-x,0px))!important}html body .zpNewNav__mobileSearch,html body .zpNewNav__burger{width:46px!important;height:46px!important;display:grid!important;place-items:center!important;transform:none!important}}</style>
<?php }
add_action('wp_head', 'zp_suite_239_final_visual_hardfix_css', PHP_INT_MAX);
// v2.2.420: header-critical CSS prints in wp_head only; footer duplicate caused late reflow.
// add_action('wp_footer', 'zp_suite_239_final_visual_hardfix_css', PHP_INT_MAX);

function zp_suite_239_final_visual_hardfix_js(){ ?>
<script id="zp-suite-239-final-visual-hardfix-js">
(function(){
  function ready(fn){
    if(document.readyState==='loading') document.addEventListener('DOMContentLoaded',fn,{once:true});
    else fn();
  }
  function wrapShopH1(){
    try{
      var h=document.getElementById('zpShopHeroKatTitle') || document.querySelector('#zpShopHeroKat h1');
      if(!h) return;
      var phrase='Sklepy internetowe Katowice';
      if(!h.querySelector('.zpH1Emphasis') && h.textContent.indexOf(phrase)===0){
        var rest=h.textContent.slice(phrase.length);
        h.innerHTML='<span class="zpH1Emphasis">'+phrase+'</span>'+rest;
      }
    }catch(e){}
  }

  function polishWebHeroCopy(){
    try{
      var h=document.getElementById('zpWebHeroKatTitle');
      if(h){
        h.innerHTML='<span class="zpH1Emphasis">Strony internetowe Katowice</span> — strony firmowe gotowe na SEO i zapytania';
      }
      var lead=document.querySelector('#zpWebHeroKat .zpWebHeroKat__lead');
      if(lead){
        lead.innerHTML='Projektujemy <strong>strony internetowe dla firm z Katowic, Śląska i całej Polski</strong>: strategię, UX/UI, treści, WordPress, SEO techniczne, analitykę i formularze przygotowane pod Google Ads oraz kampanie Meta Ads.';
      }
    }catch(e){}
  }
  function lockWebHeroImage(){
    try{
      var root=document.getElementById('zpWebHeroKat');
      if(!root) return;
      root.setAttribute('data-ready','1');
      var nativeSet=root.setAttribute;
      root.setAttribute=function(name,value){
        if(String(name).toLowerCase()==='data-ready' && String(value)==='0') value='1';
        return nativeSet.call(this,name,value);
      };
    }catch(e){}
  }
  wrapShopH1();
  polishWebHeroCopy();
  lockWebHeroImage();
  ready(function(){ wrapShopH1(); polishWebHeroCopy(); lockWebHeroImage(); });
  window.addEventListener('load',function(){ wrapShopH1(); polishWebHeroCopy(); lockWebHeroImage(); },{once:true});
})();
</script>
<?php }
add_action('wp_head', 'zp_suite_239_final_visual_hardfix_js', PHP_INT_MAX);
// v2.2.420: avoid duplicate script in footer.
// add_action('wp_footer', 'zp_suite_239_final_visual_hardfix_js', PHP_INT_MAX);


add_action('plugins_loaded', function(){
  $target_h1 = 'Strony internetowe Katowice — strony firmowe gotowe na SEO i zapytania';
  $target_lead = 'Projektujemy <strong>strony internetowe dla firm z Katowic, Śląska i całej Polski</strong>: strategię, UX/UI, treści, WordPress, SEO techniczne, analitykę i formularze przygotowane pod Google Ads oraz kampanie Meta Ads.';
  $rows = get_option('zp_suite_katowice_page', []);
  if (is_array($rows) && isset($rows[0]) && is_array($rows[0])) {
    $plain = trim(wp_strip_all_tags((string)($rows[0]['hero_h1'] ?? '')));
    if ($plain === 'Strony internetowe Katowice — strony, które pracują' || $plain === 'Strony internetowe Katowice — projektowanie stron WWW' || strpos($plain, 'Strony internetowe Katowice') === 0) {
      $rows[0]['hero_h1'] = $target_h1;
      $rows[0]['hero_lead'] = $target_lead;
      update_option('zp_suite_katowice_page', $rows, false);
    }
  }
}, 99);
