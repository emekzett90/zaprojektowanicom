<?php
if (!defined('ABSPATH')) { exit; }

/**
 * ZAPROJEKTOWANI SUITE v1.8.3
 * Front correction pack:
 * - fixed Elementor global header mobile drawer/search viewport escape
 * - smaller mobile search/hamburger icons
 * - hard contact service/tabs reconstruction: ticks top-right, navy active state, chevron dot restored
 * - about image controls/defaults corrected
 * - one clean stats page
 */

function zp_suite_upgrade_183_front_fixes(){
  $opts = get_option('zp_suite_options', []);
  if (!is_array($opts)) { $opts = []; }
  if (!isset($opts['about']) || !is_array($opts['about'])) { $opts['about'] = []; }
  if (!isset($opts['header']) || !is_array($opts['header'])) { $opts['header'] = []; }

  $opts['about'] = array_merge([
    'team_image' => 'https://zaprojektowani.com/wp-content/uploads/2026/05/team_box.webp',
    'team_width' => '820',
    'team_top' => '-260',
    'team_right' => '22',
    'team_scale' => '1.18',
    'team_grayscale' => '0',
    'team_opacity' => '1',
    'cards_top' => '210',
  ], $opts['about']);

  // Jeżeli poprzedni update zapisał niskie / cz-b ustawienia, podnieś sensowne wartości startowe.
  if ((float)($opts['about']['team_width'] ?? 0) < 760) { $opts['about']['team_width'] = '820'; }
  if ((float)($opts['about']['team_top'] ?? 0) > -210) { $opts['about']['team_top'] = '-260'; }
  if ((string)($opts['about']['team_grayscale'] ?? '') !== '0') { $opts['about']['team_grayscale'] = '0'; }
  if ((float)($opts['about']['team_scale'] ?? 0) < 1.12) { $opts['about']['team_scale'] = '1.18'; }
  // v1.8.74: logo sizing is controlled only by Header panel fields; old mobile logo migration disabled.

  update_option('zp_suite_options', $opts, false);
}

add_action('plugins_loaded', function(){
  if (get_option('zp_suite_front_fixes_183_done') !== '1') {
    zp_suite_upgrade_183_front_fixes();
    update_option('zp_suite_front_fixes_183_done', '1', false);
  }
}, 30);

add_action('wp_head', function(){
  if (is_admin()) { return; }
  $about_img = esc_url(zp_suite_opt('about.team_image', 'https://zaprojektowani.com/wp-content/uploads/2026/05/team_box.webp'));
  $about_w = (float) zp_suite_opt('about.team_width', 820);
  $about_top = (float) zp_suite_opt('about.team_top', -260);
  $about_right = (float) zp_suite_opt('about.team_right', 22);
  $about_scale = (float) zp_suite_opt('about.team_scale', 1.18);
  $about_gray = (float) zp_suite_opt('about.team_grayscale', 0);
  $about_opacity = (float) zp_suite_opt('about.team_opacity', 1);
  $about_cards_top = (float) zp_suite_opt('about.cards_top', 210);
  $about_cards_wrap_min = max(438, min(980, $about_cards_top + 438));
  $logo_mobile_h = (int) zp_suite_opt('header.logo_mobile_h', 64);
  ?>
  <style id="zp-suite-front-fixes-183-css">
    :root{
      --zp183-about-team-img:url('<?php echo $about_img; ?>');
      --zp183-about-team-width:<?php echo max(320, min(1100, $about_w)); ?>px;
      --zp183-about-team-top:<?php echo max(-460, min(80, $about_top)); ?>px;
      --zp183-about-team-right:<?php echo max(-260, min(360, $about_right)); ?>px;
      --zp183-about-team-scale:<?php echo max(.65, min(2.4, $about_scale)); ?>;
      --zp183-about-team-gray:<?php echo max(0, min(1, $about_gray)); ?>;
      --zp183-about-team-opacity:<?php echo max(0, min(1, $about_opacity)); ?>;
      --zp183-about-cards-top:<?php echo max(0, min(520, $about_cards_top)); ?>px;
      --zp183-about-cards-wrap-min:<?php echo $about_cards_wrap_min; ?>px;
      --zp183-mobile-logo-h:<?php echo max(24, min(42, $logo_mobile_h)); ?>px;
      --zp-navy:#071426;
      --zp-navy2:#102a4f;
      --zp-navy3:#1c477a;
    }

    /* Header globalny Elementora: drawer/search musi wyjść poza kontener headera */
    html body .elementor-location-header,
    html body .elementor-location-header .elementor-section,
    html body .elementor-location-header .elementor-container,
    html body .elementor-location-header .elementor-widget,
    html body .elementor-location-header .elementor-widget-container,
    html body .elementor-location-header .e-con,
    html body .elementor-location-header .e-con-inner,
    html body .elementor-location-header .elementor-element:has(#zpNewNav),
    html body .elementor-location-header .elementor-widget-container:has(#zpNewNav){
      overflow:visible!important;
      contain:none!important;
      transform:none!important;
    }
    html body .zpNewNav{overflow:visible!important;contain:none!important;isolation:isolate!important;z-index:2147482000!important;}
    html body .zpNewNav__shell,
    html body .zpNewNav__mobileBar{overflow:visible!important;contain:none!important;}

    @media(max-width:980px){
      html body .zpLegacyDisabled__mobileLogo{height:var(--zp183-mobile-logo-h)!important;width:auto!important;max-width:min(56vw,190px)!important;object-fit:contain!important;}
      html body .zpNewNav__mobileSearch,
      html body .zpNewNav__burger{
        width:46px!important;height:46px!important;min-width:46px!important;min-height:46px!important;
        display:grid!important;place-items:center!important;padding:0!important;margin:0!important;
        border-radius:0!important;background:transparent!important;color:#071426!important;
        border:0!important;box-shadow:none!important;
        position:relative!important;z-index:2147482100!important;line-height:1!important;pointer-events:auto!important;
      }
      html body .zpNewNav__mobileSearch svg,
      html body .zpNewNav__burger svg{width:21px!important;height:21px!important;stroke-width:1.9!important;display:block!important;}

      html body .zpNewNav__drawer,
      html body .zpNewNav__searchModal{
        position:fixed!important;inset:0!important;width:100vw!important;height:100dvh!important;max-width:none!important;
        z-index:2147483000!important;overflow:hidden!important;contain:none!important;transform:none!important;
      }
      html body .zpNewNav__drawer.is-open,
      html body .zpNewNav__searchModal.is-open{display:block!important;visibility:visible!important;opacity:1!important;pointer-events:auto!important;}
      html body .zpNewNav__drawerPanel{position:absolute!important;right:0!important;top:0!important;bottom:0!important;max-height:100dvh!important;overflow:auto!important;}
      html body .zpNewNav__searchBox{position:relative!important;z-index:2!important;max-height:calc(100dvh - 40px)!important;overflow:auto!important;}
    }
    @media(max-width:520px){
      html body .zpNewNav__mobileSearch,
      html body .zpNewNav__burger{width:44px!important;height:44px!important;min-width:44px!important;min-height:44px!important;}
      html body .zpNewNav__mobileSearch svg,
      html body .zpNewNav__burger svg{width:20px!important;height:20px!important;}
    }

    /* O Zaprojektowani: pełna kontrola zdjęcia z panelu i wyższe ułożenie */
    html body .zpAboutExperience,
    html body .zpAboutExperience__inner,
    html body .zpAboutExperience__layout,
    html body .zpAboutExperience__cardsWrap{overflow:visible!important;contain:none!important;}
    @media(min-width:981px){
      html body .zpAboutExperience__cardsWrap{padding-top:var(--zp183-about-cards-top)!important;min-height:var(--zp183-about-cards-wrap-min)!important;position:relative!important;z-index:4!important;}
      html body .zpAboutExperience__team{
        display:block!important;position:absolute!important;width:var(--zp183-about-team-width)!important;max-width:none!important;
        right:var(--zp183-about-team-right)!important;top:var(--zp183-about-team-top)!important;
        z-index:8!important;margin:0!important;pointer-events:none!important;
        transform:scale(var(--zp183-about-team-scale)) translateZ(0)!important;transform-origin:center bottom!important;
        opacity:var(--zp183-about-team-opacity)!important;
        filter:grayscale(var(--zp183-about-team-gray)) contrast(1.03) brightness(1)!important;
        mix-blend-mode:normal!important;
      }
      html body .zpAboutExperience__team img{display:block!important;width:100%!important;max-width:none!important;height:auto!important;object-fit:contain!important;filter:none!important;}
      html body .zpAboutExperience__cards{position:relative!important;z-index:12!important;}
      html body .zpAboutExperience__copy{position:relative!important;z-index:14!important;}
    }

    /* Hero: zdjęcie może wychodzić, ale content i oceny nie mogą być przykryte */
    html body .zpNewHero,
    html body .zpNewHero__inner,
    html body .zpNewHero__visual,
    html body .zpNewHero__personBox{overflow:visible!important;contain:none!important;}
    html body .zpNewHero__personBox{z-index:25!important;}
    html body .zpNewHero__content,
    html body .zpNewHero__cards,
    html body .zpNewHero__card,
    html body .zpNewHero__signatureQuote,
    html body .zpNewHero__tech{position:relative!important;z-index:160!important;}
    html body .zpNewHero__signatureQuote{z-index:170!important;}

    /* Kontakt: pełne przepisanie położeń ticków i tab-chevronów, bez różu */
    html body #zpContactSystemLight .zpContactSystemLight__grid .zpContactSystemLight__service{
      position:relative!important;overflow:hidden!important;border-color:rgba(7,20,38,.10)!important;box-shadow:none!important;
      -webkit-tap-highlight-color:transparent!important;outline:0!important;transform:none!important;
    }
    html body #zpContactSystemLight .zpContactSystemLight__grid .zpContactSystemLight__service > .zpContactSystemLight__serviceCheck{
      position:absolute!important;top:14px!important;right:14px!important;left:auto!important;bottom:auto!important;
      width:28px!important;height:28px!important;min-width:28px!important;min-height:28px!important;
      margin:0!important;padding:0!important;display:grid!important;place-items:center!important;
      transform:none!important;translate:none!important;border-radius:999px!important;z-index:50!important;
      background:#eef3f8!important;border:1px solid rgba(7,20,38,.10)!important;color:rgba(7,20,38,.38)!important;
      box-shadow:none!important;pointer-events:none!important;opacity:1!important;
    }
    html body #zpContactSystemLight .zpContactSystemLight__grid .zpContactSystemLight__service > .zpContactSystemLight__serviceCheck svg,
    html body #zpContactSystemLight .zpContactSystemLight__grid .zpContactSystemLight__service > .zpContactSystemLight__serviceCheck [data-lucide]{
      width:14px!important;height:14px!important;display:block!important;stroke:currentColor!important;stroke-width:2.15!important;opacity:.45!important;transform:none!important;
    }
    html body #zpContactSystemLight .zpContactSystemLight__grid .zpContactSystemLight__service.is-active > .zpContactSystemLight__serviceCheck{
      background:#071426!important;border-color:rgba(255,255,255,.26)!important;color:#fff!important;box-shadow:0 10px 24px rgba(7,20,38,.18)!important;
    }
    html body #zpContactSystemLight .zpContactSystemLight__grid .zpContactSystemLight__service.is-active > .zpContactSystemLight__serviceCheck svg,
    html body #zpContactSystemLight .zpContactSystemLight__grid .zpContactSystemLight__service.is-active > .zpContactSystemLight__serviceCheck [data-lucide]{opacity:1!important;}

    html body #zpContactSystemLight .zpContactSystemLight__service,
    html body #zpContactSystemLight .zpContactSystemLight__service:hover,
    html body #zpContactSystemLight .zpContactSystemLight__service:focus,
    html body #zpContactSystemLight .zpContactSystemLight__service:focus-visible,
    html body #zpContactSystemLight .zpContactSystemLight__service:active,
    html body #zpContactSystemLight .zpContactSystemLight__btn,
    html body #zpContactSystemLight .zpContactSystemLight__btn:hover,
    html body #zpContactSystemLight .zpContactSystemLight__btn:focus,
    html body #zpContactSystemLight .zpContactSystemLight__btn:active{
      --pink:#071426!important;--rose:#071426!important;--accent:#102a4f!important;accent-color:#071426!important;outline:0!important;
    }
    html body #zpContactSystemLight .zpContactSystemLight__service.is-active,
    html body #zpContactSystemLight .zpContactSystemLight__service.is-active:hover,
    html body #zpContactSystemLight .zpContactSystemLight__service.is-active:focus,
    html body #zpContactSystemLight .zpContactSystemLight__service.is-active:active{
      background:radial-gradient(circle at 92% 8%,rgba(28,71,122,.34),transparent 42%),linear-gradient(135deg,#05070b 0%,#071426 46%,#102a4f 100%)!important;
      color:#fff!important;border-color:rgba(16,42,79,.25)!important;box-shadow:none!important;
    }
    html body #zpContactSystemLight .zpContactSystemLight__service:not(.is-active),
    html body #zpContactSystemLight .zpContactSystemLight__service:not(.is-active):hover,
    html body #zpContactSystemLight .zpContactSystemLight__service:not(.is-active):focus,
    html body #zpContactSystemLight .zpContactSystemLight__service:not(.is-active):active{
      background:#fff!important;color:#071426!important;border-color:rgba(7,20,38,.10)!important;box-shadow:none!important;
    }

    html body #zpContactSystemLight .zpContactSystemLight__tabs{overflow:visible!important;border-radius:28px!important;isolation:isolate!important;}
    html body #zpContactSystemLight .zpContactSystemLight__tab{
      position:relative!important;overflow:visible!important;transform:none!important;background:transparent!important;box-shadow:none!important;
      color:rgba(7,20,38,.52)!important;border-color:rgba(7,20,38,.08)!important;outline:0!important;
    }
    html body #zpContactSystemLight .zpContactSystemLight__tab.is-active,
    html body #zpContactSystemLight .zpContactSystemLight__tab.is-active:hover,
    html body #zpContactSystemLight .zpContactSystemLight__tab.is-active:focus,
    html body #zpContactSystemLight .zpContactSystemLight__tab.is-active:active{
      background:#fff!important;color:#071426!important;box-shadow:0 14px 34px rgba(7,20,38,.055)!important;
    }
    html body #zpContactSystemLight .zpContactSystemLight__tab > span{
      position:absolute!important;left:50%!important;bottom:-13px!important;top:auto!important;right:auto!important;
      width:26px!important;height:26px!important;min-width:26px!important;min-height:26px!important;
      display:grid!important;place-items:center!important;border-radius:999px!important;
      background:#e8edf4!important;border:5px solid #fff!important;box-shadow:0 10px 20px rgba(7,20,38,.10)!important;
      transform:translateX(-50%) scale(.86)!important;opacity:1!important;pointer-events:none!important;z-index:25!important;
    }
    html body #zpContactSystemLight .zpContactSystemLight__tab.is-active > span{background:#071426!important;transform:translateX(-50%) scale(1)!important;}
    html body #zpContactSystemLight .zpContactSystemLight__tab > span::before{
      content:""!important;width:7px!important;height:7px!important;border-right:2px solid #fff!important;border-bottom:2px solid #fff!important;
      transform:translateY(-1px) rotate(45deg)!important;display:block!important;opacity:0!important;
    }
    html body #zpContactSystemLight .zpContactSystemLight__tab.is-active > span::before{opacity:1!important;}

    @media(max-width:760px){
      html body #zpContactSystemLight .zpContactSystemLight__grid .zpContactSystemLight__service > .zpContactSystemLight__serviceCheck{top:12px!important;right:12px!important;width:26px!important;height:26px!important;min-width:26px!important;min-height:26px!important;}
      html body #zpContactSystemLight .zpContactSystemLight__tab{display:grid!important;grid-template-columns:24px minmax(0,1fr) 28px!important;gap:10px!important;align-items:center!important;text-align:left!important;}
      html body #zpContactSystemLight .zpContactSystemLight__tab > span{position:relative!important;left:auto!important;right:auto!important;top:auto!important;bottom:auto!important;justify-self:end!important;transform:scale(.86)!important;border-width:4px!important;}
      html body #zpContactSystemLight .zpContactSystemLight__tab.is-active > span{transform:scale(1)!important;}
    }

    /* Mobile sticky CTA — v2.2.253: visibility + one-line balanced guard, visual styles live in assets/css/blocks/mobile-sticky-cta.css */
    @media(max-width:760px){
      html body{padding-bottom:calc(88px + env(safe-area-inset-bottom,0px))!important;}
      html body .zpMobileStickyCta{display:block!important;visibility:visible!important;opacity:1!important;pointer-events:none!important;position:fixed!important;left:0!important;right:0!important;bottom:0!important;z-index:2147481500!important;font-family:"Plus Jakarta Sans Local",system-ui,sans-serif!important;padding:0 14px calc(6px + env(safe-area-inset-bottom,0px))!important;}
      html body .zpMobileStickyCta::before,html body .zpMobileStickyCta::after{content:none!important;display:none!important;background:none!important;height:0!important;}
      html body .zpMobileStickyCta__inner{display:grid!important;grid-template-columns:minmax(0,1fr) minmax(92px,1.28fr) minmax(0,1fr) minmax(0,1fr)!important;grid-template-rows:1fr!important;grid-auto-flow:column!important;gap:4px!important;align-items:center!important;min-height:74px!important;padding:7px 9px!important;border-radius:32px!important;}
      html body .zpMobileStickyCta__item{display:flex!important;flex-direction:column!important;align-items:center!important;justify-content:center!important;gap:4px!important;grid-row:1!important;min-height:60px!important;width:100%!important;min-width:0!important;background:transparent!important;border:0!important;box-shadow:none!important;padding:6px 3px 7px!important;}
      html body .zpMobileStickyCta__item:nth-child(1){grid-column:1!important} html body .zpMobileStickyCta__item:nth-child(2){grid-column:2!important} html body .zpMobileStickyCta__item:nth-child(3){grid-column:3!important} html body .zpMobileStickyCta__item:nth-child(4){grid-column:4!important}
      html body .zpMobileStickyCta__item--primary{min-height:64px!important;margin:0!important;color:#fff!important;border-radius:23px!important;padding:7px 4px 8px!important;background:radial-gradient(circle at 30% 0%,rgba(255,255,255,.28),transparent 35%),radial-gradient(circle at 96% 100%,rgba(166,124,255,.14),transparent 40%),linear-gradient(135deg,#05070b 0%,#071426 38%,#102a4f 72%,#1c477a 100%)!important;}
      html body .zpMobileStickyCta__icon svg,html body .zpMobileStickyCta__icon i svg{width:20px!important;height:20px!important;}
      html body .zpMobileStickyCta__item--primary .zpMobileStickyCta__icon svg,html body .zpMobileStickyCta__item--primary .zpMobileStickyCta__icon i svg{width:19px!important;height:19px!important;}
      html body .zpMobileStickyCta__item--primary .zpMobileStickyCta__label{padding-bottom:2px!important;}
      html body .zpMobileStickyCta__btn,html body .zpMobileStickyCta__btn--primary,html body .zpMobileStickyCta__btn--ghost{display:none!important;}
    }
    @media(max-width:390px){html body .zpMobileStickyCta__inner{grid-template-columns:minmax(0,1fr) minmax(86px,1.22fr) minmax(0,1fr) minmax(0,1fr)!important;min-height:71px!important;}html body .zpMobileStickyCta__item{min-height:57px!important;}html body .zpMobileStickyCta__item--primary{min-height:61px!important}}
    @media(min-width:761px){html body .zpMobileStickyCta{display:none!important;}}
  </style>
  <?php
}, 2147483000);

add_action('wp_footer', function(){
  if (is_admin()) { return; }
  ?>
  <script id="zp-suite-front-fixes-183-js">
    (function(){
      'use strict';
      function qs(s,c){return (c||document).querySelector(s)}
      function qsa(s,c){return Array.prototype.slice.call((c||document).querySelectorAll(s))}
      function icons(){ if(window.lucide && window.lucide.createIcons){ try{window.lucide.createIcons({attrs:{'stroke-width':1.9,'stroke-linecap':'round','stroke-linejoin':'round'}})}catch(e){} } }
      function lock(v){document.documentElement.classList.toggle('zpNewNav-lock',!!v);document.body.classList.toggle('zpNewNav-lock',!!v)}
      function getNav(btn){ return btn ? (btn.closest('#zpNewNav') || qs('#zpNewNav')) : qs('#zpNewNav'); }
      function openLayer(layer){ if(!layer) return; layer.hidden=false; layer.removeAttribute('hidden'); layer.classList.add('is-open'); layer.setAttribute('aria-hidden','false'); lock(true); icons(); }
      function closeLayer(layer){ if(!layer) return; layer.classList.remove('is-open'); layer.setAttribute('aria-hidden','true'); layer.hidden=true; if(!qs('#zpNewNavDrawer.is-open') && !qs('#zpNewNavSearch.is-open')) lock(false); }
      document.addEventListener('click',function(e){
        if(e.target && e.target.closest && e.target.closest('#zpNewNav')){ return; }
        var b=e.target.closest && e.target.closest('.zpNewNav__burger');
        var s=e.target.closest && e.target.closest('.zpNewNav__mobileSearch');
        var c=e.target.closest && e.target.closest('[data-zpnn-close]');
        var sc=e.target.closest && e.target.closest('[data-zpnn-search-close]');
        if(b){ e.preventDefault(); e.stopPropagation(); var nav=getNav(b); openLayer(qs('#zpNewNavDrawer',nav)); b.setAttribute('aria-expanded','true'); return; }
        if(s){ e.preventDefault(); e.stopPropagation(); var nav=getNav(s); openLayer(qs('#zpNewNavSearch',nav)); setTimeout(function(){var input=qs('.zpNewNav__searchInput',nav); if(input) input.focus();},80); return; }
        if(c){ e.preventDefault(); var nav=getNav(c); closeLayer(qs('#zpNewNavDrawer',nav)); var bb=qs('.zpNewNav__burger',nav); if(bb) bb.setAttribute('aria-expanded','false'); return; }
        if(sc){ e.preventDefault(); var nav=getNav(sc); closeLayer(qs('#zpNewNavSearch',nav)); return; }
      },true);
      document.addEventListener('keydown',function(e){ if(e.key==='Escape'){closeLayer(qs('#zpNewNavDrawer'));closeLayer(qs('#zpNewNavSearch'));} });
      function normalizeContact(){
        qsa('#zpContactSystemLight .zpContactSystemLight__service').forEach(function(card){
          var check=card.querySelector(':scope > .zpContactSystemLight__serviceCheck');
          if(check){ card.appendChild(check); }
        });
        qsa('#zpContactSystemLight .zpContactSystemLight__tab').forEach(function(tab){
          var dot=tab.querySelector(':scope > span');
          if(!dot){ dot=document.createElement('span'); tab.appendChild(dot); }
          tab.appendChild(dot);
        });
        icons();
      }
      if(document.readyState==='loading') document.addEventListener('DOMContentLoaded',normalizeContact); else normalizeContact();
      window.addEventListener('load',normalizeContact,{once:true,passive:true});
      setTimeout(normalizeContact,600);
      setTimeout(normalizeContact,1600);
    })();
  </script>
  <?php
}, 2147483000);


/* ZAPROJEKTOWANI — v2.2.252 mobile dock final: one-line, bottom pinned, no chat bubble, styled back-to-top */
add_action('wp_head', function(){
  if (is_admin()) { return; }
  ?>
  <style id="zp-mobile-dock-v252-final">@media(max-width:760px){html body{padding-bottom:calc(82px + env(safe-area-inset-bottom,0px))!important}html body .zpFloatUx .zpFloatUx__chat,html body .zpFloatUx [data-zp-mini-chat-open]{display:none!important;visibility:hidden!important;opacity:0!important;pointer-events:none!important}html body .zpMiniChatOverlay{display:none!important;visibility:hidden!important;opacity:0!important;pointer-events:none!important}html body .zpFloatUx{position:fixed!important;right:16px!important;bottom:calc(92px + env(safe-area-inset-bottom,0px))!important;z-index:2147481499!important;display:flex!important;flex-direction:column!important;gap:0!important;pointer-events:none!important}html body .zpFloatUx__top{pointer-events:auto!important;width:48px!important;height:48px!important;min-width:48px!important;min-height:48px!important;border-radius:999px!important;display:flex!important;align-items:center!important;justify-content:center!important;color:#fff!important;background:radial-gradient(circle at 30% 0%,rgba(255,255,255,.22),transparent 34%),linear-gradient(135deg,#05070b 0%,#071426 42%,#102a4f 78%,#1c477a 100%)!important;border:1px solid rgba(255,255,255,.20)!important;box-shadow:0 14px 34px rgba(7,20,38,.24),0 4px 10px rgba(7,20,38,.12),inset 0 1px 0 rgba(255,255,255,.22)!important;backdrop-filter:blur(16px) saturate(140%)!important;-webkit-backdrop-filter:blur(16px) saturate(140%)!important;opacity:0!important;transform:translate3d(0,8px,0) scale(.94)!important;transition:opacity .22s ease,transform .22s cubic-bezier(.16,1,.3,1)!important}html body .zpFloatUx__top.is-visible{opacity:1!important;transform:translate3d(0,0,0) scale(1)!important}html body .zpFloatUx__top svg{width:21px!important;height:21px!important;stroke-width:2.15!important;color:#fff!important;stroke:currentColor!important}html body .zpFloatUx__tooltip{display:none!important}html body .zpMobileStickyCta{display:block!important;visibility:visible!important;opacity:1!important;pointer-events:none!important;position:fixed!important;left:0!important;right:0!important;bottom:0!important;z-index:2147481500!important;padding:0 12px max(4px,env(safe-area-inset-bottom,0px))!important;margin:0!important;font-family:var(--zp-font,"Plus Jakarta Sans Local","Plus Jakarta Sans","Outfit",system-ui,sans-serif)!important}html body .zpMobileStickyCta::before,html body .zpMobileStickyCta::after{content:none!important;display:none!important;background:none!important;box-shadow:none!important;height:0!important;opacity:0!important}html body .zpMobileStickyCta__inner{position:relative!important;width:min(100%,620px)!important;margin:0 auto!important;min-height:68px!important;height:68px!important;display:grid!important;grid-template-columns:minmax(0,1fr) minmax(88px,1.22fr) minmax(0,1fr) minmax(0,1fr)!important;grid-template-rows:1fr!important;grid-auto-flow:column!important;align-items:center!important;justify-items:stretch!important;gap:3px!important;padding:6px 7px!important;pointer-events:auto!important;border-radius:30px!important;background:linear-gradient(180deg,rgba(255,255,255,.98),rgba(247,250,252,.95))!important;border:1px solid rgba(255,255,255,.96)!important;box-shadow:0 10px 25px rgba(7,20,38,.12),0 2px 8px rgba(7,20,38,.045),inset 0 1px 0 rgba(255,255,255,1)!important;overflow:hidden!important;backdrop-filter:blur(18px) saturate(145%)!important;-webkit-backdrop-filter:blur(18px) saturate(145%)!important}html body .zpMobileStickyCta__inner::before{content:""!important;position:absolute!important;inset:0!important;pointer-events:none!important;border-radius:inherit!important;background:radial-gradient(circle at 50% -30%,rgba(255,255,255,.76),transparent 40%)!important;opacity:.38!important}html body .zpMobileStickyCta__item{position:relative!important;z-index:2!important;grid-row:1!important;min-width:0!important;width:100%!important;height:56px!important;min-height:56px!important;display:flex!important;flex-direction:column!important;align-items:center!important;justify-content:center!important;gap:4px!important;padding:4px 2px!important;border-radius:21px!important;color:#05070b!important;background:transparent!important;border:0!important;box-shadow:none!important;text-decoration:none!important;-webkit-tap-highlight-color:transparent!important;overflow:hidden!important;isolation:isolate!important}html body .zpMobileStickyCta__item:nth-child(1){grid-column:1!important}html body .zpMobileStickyCta__item:nth-child(2){grid-column:2!important}html body .zpMobileStickyCta__item:nth-child(3){grid-column:3!important}html body .zpMobileStickyCta__item:nth-child(4){grid-column:4!important}html body .zpMobileStickyCta__item:focus,html body .zpMobileStickyCta__item:active,html body .zpMobileStickyCta__item:hover{outline:none!important;text-decoration:none!important}html body .zpMobileStickyCta__item:active{transform:scale(.97)!important}html body .zpMobileStickyCta__icon{width:23px!important;height:23px!important;flex:0 0 23px!important;display:grid!important;place-items:center!important;color:currentColor!important;background:transparent!important;border:0!important;box-shadow:none!important}html body .zpMobileStickyCta__icon svg,html body .zpMobileStickyCta__icon i svg{display:block!important;width:20px!important;height:20px!important;stroke:currentColor!important;stroke-width:2.05!important}html body .zpMobileStickyCta__label{display:block!important;color:currentColor!important;font-size:10.8px!important;line-height:1!important;font-weight:850!important;letter-spacing:-.025em!important;white-space:nowrap!important}html body .zpMobileStickyCta__item--primary{height:60px!important;min-height:60px!important;margin:0!important;color:#fff!important;border-radius:22px!important;background:radial-gradient(circle at 28% 0%,rgba(255,255,255,.26),transparent 34%),radial-gradient(circle at 96% 100%,rgba(166,124,255,.13),transparent 42%),linear-gradient(135deg,#05070b 0%,#071426 40%,#102a4f 72%,#1c477a 100%)!important;box-shadow:0 12px 26px rgba(16,42,79,.23),0 5px 12px rgba(7,20,38,.12),inset 0 1px 0 rgba(255,255,255,.20)!important}html body .zpMobileStickyCta__item--primary::before{content:""!important;position:absolute!important;left:22%!important;right:22%!important;bottom:7px!important;height:2px!important;border-radius:999px!important;background:rgba(255,255,255,.70)!important;pointer-events:none!important}html body .zpMobileStickyCta__item--primary::after{content:""!important;position:absolute!important;top:-55%!important;bottom:-55%!important;left:-72%!important;width:54%!important;z-index:1!important;transform:rotate(18deg)!important;background:linear-gradient(90deg,transparent,rgba(255,255,255,.88),transparent)!important;opacity:.78!important;pointer-events:none!important;animation:zpDockShine252 3.2s cubic-bezier(.22,1,.36,1) infinite!important}html body .zpMobileStickyCta__item--primary .zpMobileStickyCta__icon{width:23px!important;height:23px!important;flex-basis:23px!important;z-index:2!important}html body .zpMobileStickyCta__item--primary .zpMobileStickyCta__icon svg,html body .zpMobileStickyCta__item--primary .zpMobileStickyCta__icon i svg{width:18px!important;height:18px!important;stroke-width:2!important}html body .zpMobileStickyCta__item--primary .zpMobileStickyCta__label{font-size:11.4px!important;font-weight:900!important;padding-bottom:4px!important;z-index:2!important}html body .zpMobileStickyCta__shine{display:none!important}html body .zpMobileStickyCta__btn,html body .zpMobileStickyCta__btn--primary,html body .zpMobileStickyCta__btn--ghost{display:none!important}}@keyframes zpDockShine252{0%,54%{left:-72%!important;opacity:0!important}62%{opacity:.72!important}100%{left:118%!important;opacity:0!important}}@media(max-width:390px){html body{padding-bottom:calc(78px + env(safe-area-inset-bottom,0px))!important}html body .zpMobileStickyCta{padding-left:9px!important;padding-right:9px!important}html body .zpMobileStickyCta__inner{height:64px!important;min-height:64px!important;padding:5px 6px!important;border-radius:28px!important;grid-template-columns:minmax(0,1fr) minmax(82px,1.18fr) minmax(0,1fr) minmax(0,1fr)!important}html body .zpMobileStickyCta__item{height:53px!important;min-height:53px!important;gap:3px!important}html body .zpMobileStickyCta__item--primary{height:57px!important;min-height:57px!important;border-radius:20px!important}html body .zpMobileStickyCta__icon svg,html body .zpMobileStickyCta__icon i svg{width:18px!important;height:18px!important}html body .zpMobileStickyCta__item--primary .zpMobileStickyCta__icon svg,html body .zpMobileStickyCta__item--primary .zpMobileStickyCta__icon i svg{width:17px!important;height:17px!important}html body .zpMobileStickyCta__label{font-size:10px!important}html body .zpMobileStickyCta__item--primary .zpMobileStickyCta__label{font-size:10.7px!important}html body .zpFloatUx{right:14px!important;bottom:calc(84px + env(safe-area-inset-bottom,0px))!important}html body .zpFloatUx__top{width:45px!important;height:45px!important;min-width:45px!important;min-height:45px!important}}@media(min-width:761px){html body .zpMobileStickyCta{display:none!important}}@media(prefers-reduced-motion:reduce){html body .zpMobileStickyCta__item--primary::after{animation:none!important;display:none!important}}</style>
  <?php
}, 2147483600);


/* ZAPROJEKTOWANI — v2.2.258 floating cleanup, mobile dock only */
add_action('wp_head', function(){
  if (is_admin()) { return; }
  ?>
  <style id="zp-floating-cleanup-v258">html body .zpFloatUx .zpFloatUx__chat,html body .zpFloatUx [data-zp-mini-chat-open],html body .zpMiniChatOverlay{display:none!important;visibility:hidden!important;opacity:0!important;pointer-events:none!important}@media(min-width:761px){html body{padding-bottom:0!important}html body .zpMobileStickyCta{display:none!important;visibility:hidden!important;opacity:0!important;pointer-events:none!important}html body .zpFloatUx{position:fixed!important;right:22px!important;bottom:22px!important;z-index:2147481499!important;display:flex!important;flex-direction:column!important;gap:0!important;align-items:flex-end!important;pointer-events:none!important}}@media(max-width:760px){html body .zpFloatUx{position:fixed!important;right:16px!important;bottom:calc(92px + env(safe-area-inset-bottom,0px))!important;z-index:2147481499!important;display:flex!important;flex-direction:column!important;gap:0!important;align-items:flex-end!important;pointer-events:none!important}}html body .zpFloatUx__top{pointer-events:auto!important;width:50px!important;height:50px!important;min-width:50px!important;min-height:50px!important;border-radius:999px!important;display:flex!important;align-items:center!important;justify-content:center!important;color:#fff!important;background:radial-gradient(circle at 30% 0%,rgba(255,255,255,.22),transparent 34%),linear-gradient(135deg,#05070b 0%,#071426 42%,#102a4f 78%,#1c477a 100%)!important;border:1px solid rgba(255,255,255,.20)!important;box-shadow:0 14px 34px rgba(7,20,38,.24),0 4px 10px rgba(7,20,38,.12),inset 0 1px 0 rgba(255,255,255,.22)!important;backdrop-filter:blur(16px) saturate(140%)!important;-webkit-backdrop-filter:blur(16px) saturate(140%)!important;opacity:0!important;transform:translate3d(0,8px,0) scale(.94)!important;transition:opacity .22s ease,transform .22s cubic-bezier(.16,1,.3,1)!important}html body .zpFloatUx__top.is-visible{opacity:1!important;transform:translate3d(0,0,0) scale(1)!important}html body .zpFloatUx__top svg{width:21px!important;height:21px!important;stroke-width:2.15!important;color:#fff!important;stroke:currentColor!important}html body .zpFloatUx__tooltip{display:none!important}</style>
  <?php
}, 2147483641);


/* ZAPROJEKTOWANI — v2.2.710 mobile hero touch / compositing stability fix.
 * The primary CTA used a full-size translucent gradient plus
 * -webkit-mask-composite:xor to fake a one-pixel border. WebKit can temporarily
 * lose that mask while recompositing after a fast scroll, painting the entire
 * pseudo-element over the white button. That is the grey/blue state visible in
 * the bug report; it is not the intended navy pressed state.
 *
 * On touch layouts the risky mask is removed completely and replaced by a
 * regular border. The navy effect is driven by a short-lived JS class which is
 * added only when the CTA itself receives a pointer/touch down. Any pan, scroll,
 * cancellation, viewport change or page restore clears it deterministically.
 */
add_action('wp_head', function(){
  if (is_admin()) { return; }
  ?>
  <style id="zp-suite-mobile-touch-stability-v2710">
  @media (max-width:1400px) and (hover:none), (max-width:1400px) and (pointer:coarse), (max-width:1400px) and (any-pointer:coarse){
    /* Home hero — touch must never latch the desktop hover/focus skin. */
    html body #zhHero .zh__btn,
    html body #zhHero .zh__btn:hover,
    html body #zhHero .zh__btn:focus,
    html body #zhHero .zh__btn:active{
      -webkit-tap-highlight-color:transparent!important;
      touch-action:manipulation!important;
      outline:0!important;
      box-shadow:none!important;
      transform:none!important;
      transition:none!important;
    }
    html body #zhHero .zh__btn svg,
    html body #zhHero .zh__btn:hover svg,
    html body #zhHero .zh__btn:focus svg,
    html body #zhHero .zh__btn:active svg{
      transform:none!important;
      transition:none!important;
    }
    html body #zhHero .zh__btn--p,
    html body #zhHero .zh__btn--p:hover,
    html body #zhHero .zh__btn--p:focus,
    html body #zhHero .zh__btn--p:active{
      color:#071426!important;
      background:#fff!important;
      background-image:none!important;
      border:1px solid rgba(28,71,122,.56)!important;
    }
    /* Never use a composited mask for this border on WebKit/touch. */
    html body #zhHero .zh__btn--p::after{
      content:none!important;
      display:none!important;
      background:none!important;
      -webkit-mask:none!important;
      mask:none!important;
      -webkit-mask-composite:initial!important;
      mask-composite:initial!important;
    }
    /* Intentional touch-down feedback. :hover/:focus/:active stay neutral so a
       browser-generated sticky state can never impersonate a real press. */
    html body #zhHero .zh__btn--p.is-touch-pressed{
      color:#fff!important;
      background-color:#071426!important;
      background-image:linear-gradient(90deg,#05070b 0%,#0b1830 42%,#102a4f 72%,#1c477a 100%)!important;
      background-repeat:no-repeat!important;
      background-position:center!important;
      background-size:100% 100%!important;
      border-color:rgba(143,184,234,.42)!important;
      box-shadow:inset 0 0 0 1px rgba(143,184,234,.16)!important;
    }
    /* Preserve a visible keyboard focus indicator on touch-capable hybrids. */
    html body #zhHero .zh__btn:focus-visible{
      outline:2px solid rgba(143,200,247,.96)!important;
      outline-offset:3px!important;
    }
    html body #zhHero .zh__btn--g,
    html body #zhHero .zh__btn--g:hover,
    html body #zhHero .zh__btn--g:focus,
    html body #zhHero .zh__btn--g:active{
      color:#fff!important;
      background:rgba(255,255,255,.05)!important;
      border:1px solid rgba(255,255,255,.22)!important;
      -webkit-backdrop-filter:none!important;
      backdrop-filter:none!important;
    }
    html body #zhHero .zh__chip,
    html body #zhHero .zh__chip:hover,
    html body #zhHero .zh__chip:focus,
    html body #zhHero .zh__chip:active{
      transform:none!important;
      transition:none!important;
      box-shadow:none!important;
      outline:0!important;
      -webkit-tap-highlight-color:transparent!important;
    }
    /* Animated text is attractive on desktop, but an endless gradient repaint is
       needless on phones and makes history snapshots heavier. */
    html body #zhHero .zh__title .zh__grad{
      animation:none!important;
      will-change:auto!important;
      background-position:52% 50%!important;
    }

    /* Bottom dock — keep one deterministic visual state after every tap. */
    html body .zpMobileStickyCta,
    html body .zpMobileStickyCta__inner,
    html body .zpMobileStickyCta__item,
    html body .zpMobileStickyCta__item::before,
    html body .zpMobileStickyCta__item::after{
      will-change:auto!important;
    }
    html body .zpMobileStickyCta__inner,
    html body .zpMobileStickyCta__inner:hover,
    html body .zpMobileStickyCta__inner:focus-within{
      background:#fff!important;
      border:1px solid rgba(7,20,38,.10)!important;
      box-shadow:0 10px 25px rgba(7,20,38,.12),0 2px 8px rgba(7,20,38,.045),inset 0 1px 0 #fff!important;
      -webkit-backdrop-filter:none!important;
      backdrop-filter:none!important;
      filter:none!important;
      transition:none!important;
    }
    html body .zpMobileStickyCta__inner::before{display:none!important;content:none!important;}
    html body .zpMobileStickyCta__item,
    html body .zpMobileStickyCta__item:hover,
    html body .zpMobileStickyCta__item:focus,
    html body .zpMobileStickyCta__item:active{
      color:#05070b!important;
      background:transparent!important;
      border:0!important;
      box-shadow:none!important;
      outline:0!important;
      transform:none!important;
      transition:none!important;
      -webkit-tap-highlight-color:transparent!important;
      touch-action:manipulation!important;
    }
    html body .zpMobileStickyCta__item--primary,
    html body .zpMobileStickyCta__item--primary:hover,
    html body .zpMobileStickyCta__item--primary:focus,
    html body .zpMobileStickyCta__item--primary:active{
      color:#fff!important;
      background:radial-gradient(circle at 28% 0%,rgba(255,255,255,.26),transparent 34%),radial-gradient(circle at 96% 100%,rgba(166,124,255,.13),transparent 42%),linear-gradient(135deg,#05070b 0%,#071426 40%,#102a4f 72%,#1c477a 100%)!important;
      border:0!important;
      box-shadow:0 12px 26px rgba(16,42,79,.23),0 5px 12px rgba(7,20,38,.12),inset 0 1px 0 rgba(255,255,255,.20)!important;
      transform:none!important;
    }
    html body .zpMobileStickyCta__item--primary::after{display:none!important;content:none!important;animation:none!important;}
    html body .zpMobileStickyCta__shine{display:none!important;animation:none!important;}
    html body .zpFloatUx__top{-webkit-backdrop-filter:none!important;backdrop-filter:none!important;will-change:auto!important;}
  }
  /* Five sections were 1.5–5.1x taller than the shared 980px placeholder.
     Measured fallbacks at 320/360/390/430/600/760px keep the document height
     stable during a fling while retaining content-visibility's layout savings. */
  @media (max-width:760px) and (hover:none), (max-width:760px) and (pointer:coarse), (max-width:760px) and (any-pointer:coarse){
    html body #content.zpHomeContent{
      --zp-ci-laptop:calc(2507px - 137.5vw);
      --zp-ci-about:calc(1774px - 38.125vw);
      --zp-ci-services:calc(5265px - 95vw);
      --zp-ci-reviews:calc(4693px - 168.125vw);
      --zp-ci-faq:calc(1799px - 20vw);
    }
    html body #content.zpHomeContent > #zpLaptopShowcase{
      content-visibility:auto!important;
      contain-intrinsic-size:1px var(--zp-ci-laptop)!important;
    }
    html body #content.zpHomeContent > #zpAboutExperience{
      content-visibility:auto!important;
      contain-intrinsic-size:1px var(--zp-ci-about)!important;
    }
    html body #content.zpHomeContent > #zpShowcaseServices{
      content-visibility:auto!important;
      contain-intrinsic-size:1px var(--zp-ci-services)!important;
    }
    html body #content.zpHomeContent > #zpRevEd{
      content-visibility:auto!important;
      contain-intrinsic-size:1px var(--zp-ci-reviews)!important;
    }
    html body #content.zpHomeContent > #zpHomeFaqKatNavy{
      content-visibility:auto!important;
      contain-intrinsic-size:1px var(--zp-ci-faq)!important;
    }
  }
  @media (max-width:600px){
    html body #content.zpHomeContent{--zp-ci-laptop:calc(2003px - 53.53vw);--zp-ci-about:calc(1750px - 34.12vw);--zp-ci-services:calc(7010px - 385.88vw);--zp-ci-reviews:calc(5050px - 227.65vw);--zp-ci-faq:calc(2452px - 128.82vw);}
  }
  @media (max-width:430px){
    html body #content.zpHomeContent{--zp-ci-laptop:1810px;--zp-ci-about:1610px;--zp-ci-services:5360px;--zp-ci-reviews:4040px;--zp-ci-faq:1910px;}
  }
  @media (max-width:375px){
    html body #content.zpHomeContent{--zp-ci-laptop:1850px;--zp-ci-about:1640px;--zp-ci-services:5620px;--zp-ci-reviews:3945px;--zp-ci-faq:2020px;}
  }
  @media (max-width:340px){
    html body #content.zpHomeContent{--zp-ci-laptop:1995px;--zp-ci-about:1755px;--zp-ci-services:5815px;--zp-ci-reviews:3880px;--zp-ci-faq:2220px;}
  }
  html.zp-page-leaving-mobile body .zpMobileStickyCta,
  html.zp-page-leaving-mobile body .zpFloatUx{
    visibility:hidden!important;
    opacity:0!important;
    pointer-events:none!important;
  }
  html.zp-page-leaving-mobile body #zhHero *,
  html.zp-page-leaving-mobile body #zpShowcaseWhite *{
    animation-play-state:paused!important;
  }
  </style>
  <?php
}, PHP_INT_MAX);

add_action('wp_footer', function(){
  if (is_admin()) { return; }
  ?>
  <script id="zp-suite-mobile-touch-stability-v2710-js">
  (function(w,d){
    'use strict';
    var mqCoarse=w.matchMedia ? w.matchMedia('(max-width:1400px) and (pointer:coarse)') : null;
    var mqNoHover=w.matchMedia ? w.matchMedia('(max-width:1400px) and (hover:none)') : null;
    var mqAnyCoarse=w.matchMedia ? w.matchMedia('(max-width:1400px) and (any-pointer:coarse)') : null;
    var pressed=null, startX=0, startY=0;

    function touchLayout(){
      if(mqCoarse && mqNoHover && mqAnyCoarse) return mqCoarse.matches || mqNoHover.matches || mqAnyCoarse.matches;
      return w.innerWidth<=1400;
    }
    function heroButton(target){
      return target && target.closest ? target.closest('#zhHero .zh__btn--p') : null;
    }
    function clearPressed(){
      if(!pressed) return;
      var button=pressed;
      pressed=null;
      try{ button.classList.remove('is-touch-pressed'); }catch(e){}
    }
    function clearAllPressed(){
      clearPressed();
      try{
        d.querySelectorAll('#zhHero .zh__btn--p.is-touch-pressed').forEach(function(button){
          button.classList.remove('is-touch-pressed');
        });
      }catch(e){}
    }
    function blurTransientLink(){
      if(!touchLayout()) return;
      try{
        var a=d.activeElement;
        if(a && a.matches && a.matches('#zhHero .zh__btn,.zpMobileStickyCta__item') && typeof a.blur==='function') a.blur();
      }catch(e){}
    }
    function restore(){
      clearAllPressed();
      try{ d.documentElement.classList.remove('zp-page-leaving-mobile'); }catch(e){}
      if(!touchLayout()) return;
      blurTransientLink();
    }
    function leaving(){
      clearPressed();
      if(!touchLayout()) return;
      blurTransientLink();
      try{ d.documentElement.classList.add('zp-page-leaving-mobile'); }catch(e){}
    }

    function begin(target,x,y){
      if(!touchLayout()) return;
      var button=heroButton(target);
      if(!button) return;
      clearPressed();
      pressed=button;
      startX=typeof x==='number' ? x : 0;
      startY=typeof y==='number' ? y : 0;
      button.classList.add('is-touch-pressed');
    }
    function move(x,y){
      if(!pressed || typeof x!=='number' || typeof y!=='number') return;
      var dx=x-startX, dy=y-startY;
      if((dx*dx)+(dy*dy)>64) clearPressed();
    }
    function finish(){
      if(!pressed) return;
      clearPressed();
      w.setTimeout(blurTransientLink,0);
    }

    var hero=d.getElementById ? d.getElementById('zhHero') : null;
    if(hero){
      if('PointerEvent' in w){
        hero.addEventListener('pointerdown',function(e){
          if(e.isPrimary===false || (typeof e.button==='number' && e.button!==0)) return;
          begin(e.target,e.clientX,e.clientY);
        },{passive:true,capture:true});
        d.addEventListener('pointermove',function(e){ move(e.clientX,e.clientY); },{passive:true,capture:true});
        d.addEventListener('pointerup',finish,{passive:true,capture:true});
        d.addEventListener('pointercancel',clearPressed,{passive:true,capture:true});
      }else{
        hero.addEventListener('touchstart',function(e){
          var t=e.touches && e.touches[0];
          if(t) begin(e.target,t.clientX,t.clientY);
        },{passive:true,capture:true});
        d.addEventListener('touchmove',function(e){
          var t=e.touches && e.touches[0];
          if(t) move(t.clientX,t.clientY);
        },{passive:true,capture:true});
        d.addEventListener('touchend',finish,{passive:true,capture:true});
        d.addEventListener('touchcancel',clearPressed,{passive:true,capture:true});
      }

      /* A vertical pan must cancel feedback even if a browser omits pointercancel. */
      w.addEventListener('scroll',clearPressed,{passive:true});
      w.addEventListener('blur',clearPressed,{passive:true});
      w.addEventListener('resize',clearPressed,{passive:true});
      w.addEventListener('orientationchange',clearPressed,{passive:true});
      if(w.visualViewport){
        w.visualViewport.addEventListener('scroll',clearPressed,{passive:true});
        w.visualViewport.addEventListener('resize',clearPressed,{passive:true});
      }
    }

    /* pagehide means navigation actually began. Doing this in click-capture
       would strand the leaving class after same-document or cancelled links. */
    w.addEventListener('pagehide',leaving,{passive:true});
    w.addEventListener('pageshow',function(){ restore(); w.setTimeout(restore,50); },{passive:true});
    d.addEventListener('visibilitychange',function(){ if(d.hidden) clearPressed(); else restore(); },{passive:true});
    restore();
  })(window,document);
  </script>
  <?php
}, PHP_INT_MAX);
