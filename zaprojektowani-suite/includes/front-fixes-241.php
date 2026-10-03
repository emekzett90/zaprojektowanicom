<?php
if (!defined('ABSPATH')) { exit; }

/**
 * ZP Suite v2.2.420 — clean header stabilization layer.
 * Purpose: keep the existing visual design 1:1, but remove mobile first-paint
 * gating, static/sticky micro-jumps and logo-branding mobile init issues.
 */
function zp_suite_241_clean_header_stabilizer(){
  if (is_admin()) { return; }
  ?>
<style id="zp-suite-241-clean-header-stabilizer">html body header#zpNewNav,html body .zpNewNav#zpNewNav{display:block!important;visibility:visible!important;opacity:1!important;pointer-events:auto!important;transform:none!important;contain:none!important;z-index:2147483000!important}html body header#zpNewNav .zpNewNav__shell,html body .zpNewNav#zpNewNav .zpNewNav__shell{display:block!important;visibility:visible!important;opacity:1!important;pointer-events:auto!important;transform:none!important;contain:none!important;box-shadow:none!important;filter:none!important}@media(max-width:1180px){html body header#zpNewNav .zpNewNav__mobileCall,html body header#zpNewNav.zp-icons-ready .zpNewNav__mobileCall,html body header#zpNewNav .zpNewNav__mobileBrand,html body header#zpNewNav.zp-icons-ready .zpNewNav__mobileBrand{opacity:1!important;visibility:visible!important;animation:none!important;filter:none!important;pointer-events:auto!important}html body header#zpNewNav .zpNewNav__mobileCall i,html body header#zpNewNav .zpNewNav__mobileCall svg{display:block!important;width:13px!important;min-width:13px!important;height:13px!important;min-height:13px!important;opacity:1!important;visibility:visible!important;flex:0 0 13px!important;transform:none!important}html body header#zpNewNav .zpNewNav__mobileCall span{display:inline-block!important;opacity:1!important;visibility:visible!important;transform:none!important}}@media(max-width:980px){html body{--zpNavH:82px!important}html body header#zpNewNav,html body header#zpNewNav.zpNewNav--heroOverlay,html body header#zpNewNav.is-scrolled{min-height:82px!important;height:auto!important;transform:none!important}html body header#zpNewNav .zpNewNav__shell,html body header#zpNewNav.is-scrolled .zpNewNav__shell,html body header#zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled) .zpNewNav__shell{height:82px!important;min-height:82px!important;padding:0!important;display:flex!important;align-items:center!important;transform:none!important}html body header#zpNewNav .zpNewNav__mobileBar,html body header#zpNewNav.is-scrolled .zpNewNav__mobileBar,html body header#zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled) .zpNewNav__mobileBar{position:relative!important;display:flex!important;align-items:center!important;justify-content:space-between!important;height:82px!important;min-height:82px!important;padding-top:0!important;padding-bottom:0!important;margin-top:0!important;margin-bottom:0!important;transform:none!important;overflow:visible!important}html body header#zpNewNav .zpNewNav__mobileActions,html body header#zpNewNav.is-scrolled .zpNewNav__mobileActions,html body header#zpNewNav .zpNewNav__mobileSearch,html body header#zpNewNav.is-scrolled .zpNewNav__mobileSearch,html body header#zpNewNav .zpNewNav__burger,html body header#zpNewNav.is-scrolled .zpNewNav__burger{transform:none!important;margin-top:0!important;margin-bottom:0!important;align-items:center!important}html body header#zpNewNav .zpNewNav__mobileSearch,html body header#zpNewNav .zpNewNav__burger{display:grid!important;place-items:center!important;width:48px!important;height:48px!important;min-width:48px!important;min-height:48px!important;flex:0 0 48px!important}html body header#zpNewNav .zpNewNav__mobileBrand,html body header#zpNewNav.is-scrolled .zpNewNav__mobileBrand{top:50%!important;transform:translate(-50%,-50%)!important;height:82px!important;min-height:82px!important;margin:0!important}}@media(min-width:981px) and (max-width:1100px){html body header#zpNewNav .zpNewNav__mobileCall,html body header#zpNewNav .zpNewNav__mobileBrand,html body header#zpNewNav .zpNewNav__mobileActions,html body header#zpNewNav .zpNewNav__mobileSearch,html body header#zpNewNav .zpNewNav__burger{opacity:1!important;visibility:visible!important;pointer-events:auto!important;animation:none!important;filter:none!important}}@media(max-width:980px){html body.zp-logo-branding-page header#zpNewNav,html body.zp-logo-branding-shortcode header#zpNewNav,html body.zp-logo-branding-page header#zpNewNav .zpNewNav__shell,html body.zp-logo-branding-shortcode header#zpNewNav .zpNewNav__shell,html body.zp-logo-branding-page header#zpNewNav .zpNewNav__mobileBar,html body.zp-logo-branding-shortcode header#zpNewNav .zpNewNav__mobileBar{display:flex!important;visibility:visible!important;opacity:1!important;pointer-events:auto!important;transform:none!important;contain:none!important}html body.zp-logo-branding-page header#zpNewNav .zpNewNav__mobileCall,html body.zp-logo-branding-page header#zpNewNav .zpNewNav__mobileBrand,html body.zp-logo-branding-page header#zpNewNav .zpNewNav__mobileActions,html body.zp-logo-branding-page header#zpNewNav .zpNewNav__mobileSearch,html body.zp-logo-branding-page header#zpNewNav .zpNewNav__burger,html body.zp-logo-branding-shortcode header#zpNewNav .zpNewNav__mobileCall,html body.zp-logo-branding-shortcode header#zpNewNav .zpNewNav__mobileBrand,html body.zp-logo-branding-shortcode header#zpNewNav .zpNewNav__mobileActions,html body.zp-logo-branding-shortcode header#zpNewNav .zpNewNav__mobileSearch,html body.zp-logo-branding-shortcode header#zpNewNav .zpNewNav__burger{display:flex!important;visibility:visible!important;opacity:1!important;pointer-events:auto!important;animation:none!important;filter:none!important}html body.zp-logo-branding-page header#zpNewNav .zpNewNav__mobileSearch,html body.zp-logo-branding-page header#zpNewNav .zpNewNav__burger,html body.zp-logo-branding-shortcode header#zpNewNav .zpNewNav__mobileSearch,html body.zp-logo-branding-shortcode header#zpNewNav .zpNewNav__burger{display:grid!important;place-items:center!important}}</style>
<script id="zp-suite-241-clean-header-stabilizer-js">
(function(w,d){
  'use strict';
  function ready(fn){ if(d.readyState==='loading') d.addEventListener('DOMContentLoaded',fn,{once:true}); else fn(); }
  function stabilize(){
    var nav=d.getElementById('zpNewNav');
    if(!nav) return;
    nav.classList.add('zp-icons-ready');
    if(d.body && d.body.classList && (d.body.classList.contains('zp-logo-branding-page') || d.body.classList.contains('zp-logo-branding-shortcode'))){
      nav.classList.add('zpNewNav--heroOverlay');
    }
    var y=w.scrollY || d.documentElement.scrollTop || 0;
    nav.classList.toggle('is-scrolled', y>18);
    if(d.body) d.body.classList.toggle('zp-nav-scrolled', y>18);
  }
  stabilize();
  ready(stabilize);
  w.addEventListener('pageshow',stabilize,{passive:true});
  w.addEventListener('load',stabilize,{once:true,passive:true});
})(window,document);
</script>
<?php }
add_action('wp_head', 'zp_suite_241_clean_header_stabilizer', 1);
