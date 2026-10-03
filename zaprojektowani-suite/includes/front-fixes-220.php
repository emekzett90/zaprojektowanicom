<?php
/**
 * ZAPROJEKTOWANI Suite — front fixes v2.2.221
 * TYLKO header/nawigacja na /realizacje/ jak na FAQ.
 * Nie dotyka hero, treści, filtrów, kart, grida ani JS widgetu Realizacje.
 */
if (!defined('ABSPATH')) exit;

add_action('wp_footer', function () {
  if (is_admin() || !is_page('realizacje')) return;
  ?>
  <style id="zp-suite-front-fixes-221-realizacje-nav-only">html body.page #zpNewNav,html body.page header#zpNewNav{color:#071426!important}html body.page header#zpNewNav:not(.is-mega-open) .zpNewNav__shell{background:#fff!important;border-bottom:0!important;box-shadow:0 16px 46px rgba(5,10,18,.055)!important;-webkit-backdrop-filter:none!important;backdrop-filter:none!important}html body.page header#zpNewNav:not(.is-mega-open) .zpNewNav__inner,html body.page header#zpNewNav:not(.is-mega-open) .zpNewNav__desktop,html body.page header#zpNewNav:not(.is-mega-open) .zpNewNav__actions{color:#071426!important}html body.page header#zpNewNav:not(.is-mega-open) .zpNewNav__logo--light{opacity:0!important;visibility:hidden!important}html body.page header#zpNewNav:not(.is-mega-open) .zpNewNav__logo--dark{opacity:1!important;visibility:visible!important}html body.page header#zpNewNav:not(.is-mega-open) .zpNewNav__link,html body.page header#zpNewNav:not(.is-mega-open) .zpNewNav__round,html body.page header#zpNewNav:not(.is-mega-open) .zpNewNav__contactIcon{color:#071426!important;opacity:1!important}html body.page header#zpNewNav:not(.is-mega-open) .zpNewNav__link svg,html body.page header#zpNewNav:not(.is-mega-open) .zpNewNav__round svg,html body.page header#zpNewNav:not(.is-mega-open) .zpNewNav__contactIcon svg{stroke:#071426!important}html body.page header#zpNewNav:not(.is-mega-open) .zpNewNav__link:hover,html body.page header#zpNewNav:not(.is-mega-open) .zpNewNav__link:focus-visible,html body.page header#zpNewNav:not(.is-mega-open) .zpNewNav__link.is-active,html body.page header#zpNewNav:not(.is-mega-open) .zpNewNav__item.current-menu-item > .zpNewNav__link{color:#020407!important}html body.page header#zpNewNav:not(.is-mega-open) .zpNewNav__link::after{background:#071426!important}html body.page header#zpNewNav .zpNewNav__mega,html body.page .zpNewNav__mega,html body.page .zpNewNav__megaInner{background:rgba(255,255,255,.96)!important;color:#071426!important;border-color:rgba(7,20,38,.09)!important;box-shadow:0 26px 80px rgba(5,10,18,.13)!important;-webkit-backdrop-filter:none!important;backdrop-filter:none!important}html body.page header#zpNewNav .zpNewNav__megaHead,html body.page header#zpNewNav .zpNewNav__megaTitle,html body.page header#zpNewNav .zpNewNav__megaDesc,html body.page header#zpNewNav .zpNewNav__megaLink,html body.page .zpNewNav__megaHead,html body.page .zpNewNav__megaTitle,html body.page .zpNewNav__megaDesc,html body.page .zpNewNav__megaLink{color:#071426!important}html body.page header#zpNewNav .zpNewNav__megaDesc,html body.page .zpNewNav__megaDesc{color:#667085!important}html body.page header#zpNewNav .zpNewNav__megaLink:hover,html body.page .zpNewNav__megaLink:hover{background:rgba(7,20,38,.045)!important;color:#020407!important}html body.page header#zpNewNav,html body.page #zpNewNav,html body.page header#zpNewNav:not(.zpNewNav--heroOverlay),html body.page header#zpNewNav:not(.is-mega-open):not(.zpNewNav--heroOverlay){background:#fff!important;background-image:none!important;-webkit-backdrop-filter:none!important;backdrop-filter:none!important}html body.page header#zpNewNav:not(.is-mega-open) > .zpNewNav__shell,html body.page header#zpNewNav:not(.is-mega-open) .zpNewNav__shell,html body.page #zpNewNav:not(.is-mega-open) > .zpNewNav__shell,html body.page #zpNewNav:not(.is-mega-open) .zpNewNav__shell{background:#fff!important;background-color:#fff!important;background-image:none!important;border-bottom:0!important;box-shadow:0 12px 34px rgba(5,10,18,.045)!important;opacity:1!important;-webkit-backdrop-filter:none!important;backdrop-filter:none!important}html body.page header#zpNewNav:not(.is-mega-open) .zpNewNav__shell::before,html body.page header#zpNewNav:not(.is-mega-open) .zpNewNav__shell::after{display:none!important;content:none!important;background:none!important;box-shadow:none!important}@media(max-width:1080px){html body.page header#zpNewNav:not(.is-mega-open) .zpNewNav__shell{background:#fff!important;box-shadow:0 12px 34px rgba(5,10,18,.08)!important}}</style>
  <?php
}, 2147483647);

/**
 * ZAPROJEKTOWANI Suite — front fixes v2.2.222
 * Realizacje: tylko NAV/header sticky jak FAQ. Nie dotyka hero ani widgetu realizacji.
 */
if (!defined('ABSPATH')) exit;

add_action('wp_footer', function () {
  if (is_admin() || !is_page('realizacje')) return;
  ?>
  <style id="zp-suite-front-fixes-222-realizacje-sticky-nav-like-faq">html body.page header#zpNewNav,html body.page #zpNewNav{position:fixed!important;top:0!important;left:0!important;right:auto!important;width:100vw!important;max-width:100vw!important;z-index:2147482000!important;transform:none!important;margin:0!important;background:#fff!important;background-image:none!important;-webkit-backdrop-filter:none!important;backdrop-filter:none!important;will-change:auto!important}html body.admin-bar.page header#zpNewNav,html body.admin-bar.page #zpNewNav{top:32px!important}@media(max-width:782px){html body.admin-bar.page header#zpNewNav,html body.admin-bar.page #zpNewNav{top:46px!important}}html body.page header#zpNewNav > .zpNewNav__shell,html body.page #zpNewNav > .zpNewNav__shell,html body.page header#zpNewNav .zpNewNav__shell,html body.page #zpNewNav .zpNewNav__shell{position:relative!important;top:auto!important;background:#fff!important;background-color:#fff!important;background-image:none!important;border-bottom:0!important;box-shadow:0 12px 34px rgba(5,10,18,.045)!important;-webkit-backdrop-filter:none!important;backdrop-filter:none!important;transform:none!important;opacity:1!important}html body.page header#zpNewNav.is-scrolled:not(.is-mega-open) > .zpNewNav__shell,html body.page header#zpNewNav.is-scrolled:not(.is-mega-open) .zpNewNav__shell,html body.page.zp-nav-scrolled header#zpNewNav:not(.is-mega-open) > .zpNewNav__shell,html body.page.zp-nav-scrolled header#zpNewNav:not(.is-mega-open) .zpNewNav__shell{background:rgba(255,255,255,.94)!important;background-image:none!important;box-shadow:0 14px 38px rgba(5,10,18,.075)!important;-webkit-backdrop-filter:none!important;backdrop-filter:none!important}html body.page header#zpNewNav .zpNewNav__fixedSpacer,html body.page .zpNewNav__fixedSpacer{height:0!important;display:block!important}</style>
  <script id="zp-suite-front-fixes-222-realizacje-sticky-nav-js">
    (function(){
      if(!document.body || !document.body.classList || !document.body.classList.contains('page')) return;
      var nav=document.getElementById('zpNewNav');
      if(!nav) return;
      var lastKey='';
      var raf=0;
      function sync(){
        var y=window.scrollY || document.documentElement.scrollTop || 0;
        var scrolled=y > 18;
        var top=(document.body.classList.contains('admin-bar') ? (window.matchMedia('(max-width:782px)').matches ? '46px' : '32px') : '0px');
        var key=(scrolled?'1':'0')+'|'+top;
        if(key===lastKey) return;
        lastKey=key;
        nav.classList.toggle('is-scrolled', scrolled);
        document.body.classList.toggle('zp-nav-scrolled', scrolled);
        nav.style.setProperty('position','fixed','important');
        nav.style.setProperty('top',top,'important');
        nav.style.setProperty('left','0','important');
        nav.style.setProperty('width','100vw','important');
        nav.style.setProperty('z-index','2147482000','important');
      }
      function scheduleSync(){
        if(raf) return;
        raf=requestAnimationFrame(function(){raf=0;sync();});
      }
      sync();
      window.addEventListener('scroll', scheduleSync, {passive:true});
      window.addEventListener('resize', scheduleSync, {passive:true});
    })();
  </script>
  <?php
}, 2147483647);
