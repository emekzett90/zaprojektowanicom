<?php
if (!defined('ABSPATH')) { exit; }

/**
 * ZAPROJEKTOWANI SUITE v1.8.2
 * Final front fixes: about team controls, hero z-index, contact cards/tabs, mobile header/search/drawer, sticky CTA.
 */

function zp_suite_upgrade_182_front_fixes(){
  $opts = get_option('zp_suite_options', []);
  if (!is_array($opts)) { $opts = []; }
  if (!isset($opts['header']) || !is_array($opts['header'])) { $opts['header'] = []; }
  if (!isset($opts['about']) || !is_array($opts['about'])) { $opts['about'] = []; }
  // v1.8.74: logo sizing is controlled only by Header panel fields; old mobile logo migration disabled.
  // v1.8.74: logo X offset is controlled only by Header panel field; old migration disabled.

  $opts['about'] = array_merge([
    'team_image' => 'https://zaprojektowani.com/wp-content/uploads/2026/05/team_box.webp',
    'team_width' => '650',
    'team_top' => '-150',
    'team_right' => '38',
    'team_scale' => '1.25',
    'team_grayscale' => '0',
    'team_opacity' => '1',
  ], $opts['about']);

  update_option('zp_suite_options', $opts, false);
}

add_action('plugins_loaded', function(){
  if (get_option('zp_suite_front_fixes_182_done') !== '1') {
    zp_suite_upgrade_182_front_fixes();
    update_option('zp_suite_front_fixes_182_done', '1', false);
  }
}, 22);

add_action('wp_head', function(){
  if (is_admin()) { return; }
  $about_img = esc_url(zp_suite_opt('about.team_image', 'https://zaprojektowani.com/wp-content/uploads/2026/05/team_box.webp'));
  $about_w = (float) zp_suite_opt('about.team_width', 650);
  $about_top = (float) zp_suite_opt('about.team_top', -150);
  $about_right = (float) zp_suite_opt('about.team_right', 38);
  $about_scale = (float) zp_suite_opt('about.team_scale', 1.25);
  $about_gray = (float) zp_suite_opt('about.team_grayscale', 0);
  $about_opacity = (float) zp_suite_opt('about.team_opacity', 1);
  $about_cards_top = (float) zp_suite_opt('about.cards_top', 210);
  $about_cards_wrap_min = max(438, min(980, $about_cards_top + 438));
  $logo_mobile_h = (int) zp_suite_opt('header.logo_mobile_h', 64);
  ?>
  <style id="zp-suite-front-fixes-182-css">
    :root{
      --zp182-about-team-img:url('<?php echo $about_img; ?>');
      --zp182-about-team-width:<?php echo max(260, min(980, $about_w)); ?>px;
      --zp182-about-team-top:<?php echo max(-360, min(120, $about_top)); ?>px;
      --zp182-about-team-right:<?php echo max(-220, min(260, $about_right)); ?>px;
      --zp182-about-team-scale:<?php echo max(.55, min(2.2, $about_scale)); ?>;
      --zp182-about-team-gray:<?php echo max(0, min(1, $about_gray)); ?>;
      --zp182-about-team-opacity:<?php echo max(0, min(1, $about_opacity)); ?>;
      --zp182-about-cards-top:<?php echo max(0, min(520, $about_cards_top)); ?>px;
      --zp182-about-cards-wrap-min:<?php echo $about_cards_wrap_min; ?>px;
      --zp182-mobile-logo-h:<?php echo max(44, min(110, $logo_mobile_h)); ?>px;
    }

    /* ABOUT: zdjęcie ma być kolorowe, większe i zawsze nad kartami, z kontrolą w panelu */
    html body .zpAboutExperience,
    html body .zpAboutExperience__inner,
    html body .zpAboutExperience__layout,
    html body .zpAboutExperience__cardsWrap{
      overflow:visible!important;
      contain:none!important;
    }

    @media(min-width:981px){
      html body .zpAboutExperience__cardsWrap{
        padding-top:var(--zp182-about-cards-top)!important;
        min-height:var(--zp182-about-cards-wrap-min)!important;
        position:relative!important;
        z-index:4!important;
      }

      html body .zpAboutExperience__team{
        display:block!important;
        position:absolute!important;
        width:var(--zp182-about-team-width)!important;
        max-width:none!important;
        right:var(--zp182-about-team-right)!important;
        top:var(--zp182-about-team-top)!important;
        z-index:6!important;
        transform:scale(var(--zp182-about-team-scale)) translateZ(0)!important;
        transform-origin:center bottom!important;
        opacity:var(--zp182-about-team-opacity)!important;
        filter:grayscale(var(--zp182-about-team-gray)) contrast(1.02) brightness(1)!important;
        mix-blend-mode:normal!important;
        pointer-events:none!important;
      }

      html body .zpAboutExperience__team img{
        display:block!important;
        width:100%!important;
        height:auto!important;
        max-width:none!important;
        object-fit:contain!important;
        filter:none!important;
      }

      html body .zpAboutExperience__cards{
        position:relative!important;
        z-index:10!important;
      }
    }

    /* HERO: osoby mogą wychodzić, ale karty/quote zawsze są na wierzchu */
    html body .zpNewHero,
    html body .zpNewHero__inner,
    html body .zpNewHero__visual,
    html body .zpNewHero__personBox{
      overflow:visible!important;
      contain:none!important;
    }

    html body .zpNewHero__personBox{z-index:35!important;}
    html body .zpNewHero__personBox::after{z-index:1!important;}
    html body .zpNewHero__person{position:relative!important;z-index:2!important;}
    html body .zpNewHero__cards,
    html body .zpNewHero__card,
    html body .zpNewHero__signatureQuote,
    html body .zpNewHero__tech{
      position:relative!important;
      z-index:12080!important;
    }
    html body .zpNewHero__signatureQuote{z-index:12090!important;}
    html body .zpNewHero__card{isolation:isolate!important;}

    /* HEADER MOBILE: większe logo, równe ikony, działające warstwy kliknięć */
    @media(max-width:980px){
      html body .zpNewNav{z-index:2147482000!important;}
      html body .zpNewNav__shell{overflow:visible!important;}
      html body .zpNewNav__mobileBar{
        width:calc(100% - 28px)!important;
        min-height:max(var(--zp182-mobile-logo-h),86px)!important;
        display:flex!important;
        align-items:center!important;
        justify-content:space-between!important;
      }
      html body .zpNewNav .zpLegacyDisabled__mobileLogo{
        height:var(--zp182-mobile-logo-h)!important;
        max-width:min(52vw,260px)!important;
        width:auto!important;
        object-fit:contain!important;
      }
      html body .zpNewNav__mobileActions{
        display:flex!important;
        align-items:center!important;
        gap:10px!important;
      }
      html body .zpNewNav__mobileSearch,
      html body .zpNewNav__burger{
        width:54px!important;
        height:54px!important;
        min-width:54px!important;
        min-height:54px!important;
        display:grid!important;
        place-items:center!important;
        padding:0!important;
        margin:0!important;
        border-radius:999px!important;
        border:1px solid rgba(7,20,38,.09)!important;
        background:#fff!important;
        color:#071426!important;
        box-shadow:0 10px 26px rgba(7,20,38,.055)!important;
        line-height:1!important;
        position:relative!important;
        z-index:5!important;
        pointer-events:auto!important;
      }
      html body .zpNewNav__mobileSearch svg,
      html body .zpNewNav__burger svg{
        width:26px!important;
        height:26px!important;
        display:block!important;
        stroke-width:1.9!important;
      }
      html body .zpNewNav__drawer,
      html body .zpNewNav__search{
        z-index:2147482600!important;
      }
      html body .zpNewNav__drawer.is-open,
      html body .zpNewNav__search.is-open{
        display:block!important;
        visibility:visible!important;
        opacity:1!important;
        pointer-events:auto!important;
      }
    }
    @media(max-width:520px){
      html body .zpNewNav__mobileBar{width:calc(100% - 24px)!important;}
      html body .zpNewNav__mobileSearch,
      html body .zpNewNav__burger{width:52px!important;height:52px!important;min-width:52px!important;min-height:52px!important;}
    }

    /* CONTACT: tick w rogu, brak różu, chevron aktywnej zakładki */
    html body .zpContactSystemLight__service{
      position:relative!important;
      overflow:hidden!important;
      border-color:rgba(7,20,38,.09)!important;
      box-shadow:none!important;
    }
    html body .zpContactSystemLight__serviceCheck{
      position:absolute!important;
      top:16px!important;
      right:16px!important;
      left:auto!important;
      bottom:auto!important;
      z-index:40!important;
      width:28px!important;
      height:28px!important;
      min-width:28px!important;
      min-height:28px!important;
      margin:0!important;
      padding:0!important;
      display:grid!important;
      place-items:center!important;
      border-radius:999px!important;
      transform:none!important;
      translate:none!important;
      background:rgba(255,255,255,.72)!important;
      border:1px solid rgba(7,20,38,.12)!important;
      color:rgba(7,20,38,.42)!important;
      box-shadow:0 8px 18px rgba(7,20,38,.07)!important;
      pointer-events:none!important;
    }
    html body .zpContactSystemLight__service.is-active .zpContactSystemLight__serviceCheck{
      background:#071426!important;
      color:#fff!important;
      border-color:rgba(255,255,255,.30)!important;
      box-shadow:0 10px 24px rgba(7,20,38,.20)!important;
    }
    html body .zpContactSystemLight__serviceCheck svg{
      width:15px!important;
      height:15px!important;
      display:block!important;
      opacity:1!important;
      transform:none!important;
      stroke:currentColor!important;
      stroke-width:2.1!important;
    }
    html body .zpContactSystemLight__service:not(.is-active) .zpContactSystemLight__serviceCheck svg{opacity:.34!important;}

    html body .zpContactSystemLight__service,
    html body .zpContactSystemLight__service:hover,
    html body .zpContactSystemLight__service:focus,
    html body .zpContactSystemLight__service:focus-visible,
    html body .zpContactSystemLight__service:active{
      outline:0!important;
      --pink:#102a4f!important;
      --rose:#102a4f!important;
      --accent:#102a4f!important;
      -webkit-tap-highlight-color:transparent!important;
    }
    html body .zpContactSystemLight__service:not(.is-active):hover,
    html body .zpContactSystemLight__service:not(.is-active):focus,
    html body .zpContactSystemLight__service:not(.is-active):active{
      background:#fff!important;
      color:#071426!important;
      border-color:rgba(16,42,79,.18)!important;
      box-shadow:none!important;
    }
    html body .zpContactSystemLight__service.is-active,
    html body .zpContactSystemLight__service.is-active:hover,
    html body .zpContactSystemLight__service.is-active:focus,
    html body .zpContactSystemLight__service.is-active:active{
      background:radial-gradient(circle at 92% 8%,rgba(28,71,122,.36),transparent 42%),linear-gradient(135deg,#05070b 0%,#071426 45%,#102a4f 100%)!important;
      color:#fff!important;
      border-color:rgba(16,42,79,.26)!important;
      box-shadow:none!important;
    }

    html body .zpContactSystemLight__tabs{
      overflow:visible!important;
      border-radius:26px!important;
      isolation:isolate!important;
    }
    html body .zpContactSystemLight__tab{
      position:relative!important;
      overflow:visible!important;
      color:rgba(7,20,38,.54)!important;
      background:transparent!important;
      box-shadow:none!important;
      border-color:rgba(7,20,38,.08)!important;
      transform:none!important;
    }
    html body .zpContactSystemLight__tab.is-active,
    html body .zpContactSystemLight__tab.is-active:hover,
    html body .zpContactSystemLight__tab.is-active:active,
    html body .zpContactSystemLight__tab.is-active:focus-visible{
      background:#fff!important;
      color:#071426!important;
      box-shadow:0 14px 34px rgba(7,20,38,.055)!important;
    }
    html body .zpContactSystemLight__tab span{
      position:absolute!important;
      left:50%!important;
      bottom:-11px!important;
      top:auto!important;
      right:auto!important;
      width:24px!important;
      height:24px!important;
      min-width:24px!important;
      min-height:24px!important;
      display:grid!important;
      place-items:center!important;
      border-radius:999px!important;
      background:#e8edf4!important;
      border:4px solid #fff!important;
      transform:translateX(-50%) scale(.84)!important;
      opacity:1!important;
      box-shadow:0 10px 22px rgba(7,20,38,.11)!important;
      pointer-events:none!important;
    }
    html body .zpContactSystemLight__tab.is-active span{background:#071426!important;transform:translateX(-50%) scale(1)!important;}
    html body .zpContactSystemLight__tab span::before{
      content:""!important;
      width:7px!important;
      height:7px!important;
      border-right:2px solid #fff!important;
      border-bottom:2px solid #fff!important;
      transform:translateY(-1px) rotate(45deg)!important;
      opacity:0!important;
    }
    html body .zpContactSystemLight__tab.is-active span::before{opacity:1!important;}

    @media(max-width:760px){
      html body .zpContactSystemLight__serviceCheck{top:14px!important;right:14px!important;}
      html body .zpContactSystemLight__tab{
        display:grid!important;
        grid-template-columns:28px minmax(0,1fr) 26px!important;
        align-items:center!important;
        gap:10px!important;
      }
      html body .zpContactSystemLight__tab span{
        position:relative!important;
        left:auto!important;
        bottom:auto!important;
        justify-self:end!important;
        transform:scale(.86)!important;
      }
      html body .zpContactSystemLight__tab.is-active span{transform:scale(1)!important;}
    }
  </style>
  <?php
}, 1000010);

add_action('wp_footer', function(){
  if (is_admin()) { return; }
  ?>
  <script id="zp-suite-front-fixes-182-js">
    (function(){
      'use strict';
      function qs(s,c){return (c||document).querySelector(s)}
      function qsa(s,c){return Array.prototype.slice.call((c||document).querySelectorAll(s))}
      function renderIcons(){
        if(window.lucide && typeof window.lucide.createIcons === 'function'){
          try{ window.lucide.createIcons({attrs:{'stroke-width':1.9,'stroke-linecap':'round','stroke-linejoin':'round'}}); }catch(e){}
        }
      }
      function unlockBody(){document.documentElement.classList.remove('zpNewNav-lock');document.body.classList.remove('zpNewNav-lock')}
      function lockBody(){document.documentElement.classList.add('zpNewNav-lock');document.body.classList.add('zpNewNav-lock')}
      function openDrawer(){
        var nav=qs('#zpNewNav'); if(!nav) return;
        var drawer=qs('#zpNewNavDrawer',nav); var burger=qs('.zpNewNav__burger',nav);
        if(!drawer) return;
        drawer.hidden=false; drawer.removeAttribute('hidden'); drawer.classList.add('is-open'); drawer.setAttribute('aria-hidden','false');
        if(burger) burger.setAttribute('aria-expanded','true'); lockBody(); renderIcons();
      }
      function closeDrawer(){
        var nav=qs('#zpNewNav'); if(!nav) return;
        var drawer=qs('#zpNewNavDrawer',nav); var burger=qs('.zpNewNav__burger',nav);
        if(!drawer) return;
        drawer.classList.remove('is-open'); drawer.setAttribute('aria-hidden','true'); drawer.hidden=true;
        if(burger) burger.setAttribute('aria-expanded','false');
        if(!qs('#zpNewNavSearch.is-open',nav)) unlockBody();
      }
      function openSearch(){
        var nav=qs('#zpNewNav'); if(!nav) return;
        var modal=qs('#zpNewNavSearch',nav); if(!modal) return;
        modal.hidden=false; modal.removeAttribute('hidden'); modal.classList.add('is-open'); modal.setAttribute('aria-hidden','false');
        lockBody(); renderIcons(); setTimeout(function(){var input=qs('.zpNewNav__searchInput',modal); if(input) input.focus();},80);
      }
      function closeSearch(){
        var nav=qs('#zpNewNav'); if(!nav) return;
        var modal=qs('#zpNewNavSearch',nav); if(!modal) return;
        modal.classList.remove('is-open'); modal.setAttribute('aria-hidden','true'); modal.hidden=true;
        if(!qs('#zpNewNavDrawer.is-open',nav)) unlockBody();
      }
      document.addEventListener('click',function(e){
        if(e.target && e.target.closest && e.target.closest('#zpNewNav')){ return; }
        var burger=e.target.closest && e.target.closest('.zpNewNav__burger');
        var search=e.target.closest && e.target.closest('.zpNewNav__mobileSearch');
        var close=e.target.closest && e.target.closest('[data-zpnn-close]');
        var sclose=e.target.closest && e.target.closest('[data-zpnn-search-close]');
        if(burger){e.preventDefault();e.stopPropagation();openDrawer();return;}
        if(search){e.preventDefault();e.stopPropagation();openSearch();return;}
        if(close){e.preventDefault();closeDrawer();return;}
        if(sclose){e.preventDefault();closeSearch();return;}
      },true);
      document.addEventListener('keydown',function(e){if(e.key==='Escape'){closeDrawer();closeSearch();}});
      if(document.readyState==='loading') document.addEventListener('DOMContentLoaded',renderIcons); else renderIcons();
      window.addEventListener('load',renderIcons,{once:true,passive:true});
    })();
  </script>
  <?php
}, 1000010);
