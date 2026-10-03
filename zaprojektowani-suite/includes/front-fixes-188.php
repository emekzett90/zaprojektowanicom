<?php
/**
 * ZP Suite v2.2.188 — hard header opacity + contact dark cards.
 * - One CMS setting controls desktop and mobile sticky header whiteness.
 * - Contact widget/contact form active cards use black → navy gradient matching shop process dark cards.
 */
if (!defined('ABSPATH')) { exit; }

function zp_suite_188_sticky_white_strength(){
  $raw = function_exists('zp_suite_opt') ? zp_suite_opt('header.sticky_white_strength', '85') : '85';
  $val = is_numeric($raw) ? (float)$raw : 85;
  $val = max(1, min(100, $val));
  return $val / 100;
}

add_action('wp_head', function(){
  if (is_admin()) return;
  $a = zp_suite_188_sticky_white_strength();
  $top = min(1, $a + .035);
  $bot = max(0, $a - .045);
  ?>
  <style id="zp-suite-2-2-188-vars">
    :root{
      --zp-header-sticky-white-a:<?php echo esc_html(number_format($a,3,'.','')); ?>;
      --zp-header-sticky-white-top:<?php echo esc_html(number_format($top,3,'.','')); ?>;
      --zp-header-sticky-white-bottom:<?php echo esc_html(number_format($bot,3,'.','')); ?>;
      --zp-contact-dark-card:linear-gradient(135deg,#020407 0%,#05070b 42%,#071426 74%,#102a4f 100%);
      --zp-contact-dark-card-soft:radial-gradient(circle at 90% 8%,rgba(59,110,168,.14),transparent 42%),linear-gradient(135deg,#020407 0%,#05070b 42%,#071426 74%,#102a4f 100%);
    }
  </style>
  <?php
}, 2147483600);

add_action('wp_footer', function(){
  if (is_admin()) return;
  $a = zp_suite_188_sticky_white_strength();
  $top = min(1, $a + .035);
  $bot = max(0, $a - .045);
  ?>
  <style id="zp-suite-2-2-188-final">html body header#zpNewNav.is-scrolled:not(.is-mega-open) > .zpNewNav__shell,html body header#zpNewNav.is-scrolled:not(.is-mega-open) .zpNewNav__shell,html body.zp-nav-scrolled header#zpNewNav:not(.is-mega-open) > .zpNewNav__shell,html body.zp-nav-scrolled header#zpNewNav:not(.is-mega-open) .zpNewNav__shell,html body header#zpNewNav.is-scrolled:not(.is-mega-open) .zpNewNav__mobileBar,html body.zp-nav-scrolled header#zpNewNav:not(.is-mega-open) .zpNewNav__mobileBar,html body #zpNewNav.is-scrolled:not(.is-mega-open) > .zpNewNav__shell,html body #zpNewNav.is-scrolled:not(.is-mega-open) .zpNewNav__shell,html body #zpNewNav.is-scrolled:not(.is-mega-open) .zpNewNav__mobileBar{background:rgba(255,255,255,var(--zp-header-sticky-white-a))!important;background-color:rgba(255,255,255,var(--zp-header-sticky-white-a))!important;background-image:linear-gradient(180deg,rgba(255,255,255,var(--zp-header-sticky-white-top)),rgba(255,255,255,var(--zp-header-sticky-white-bottom)))!important;-webkit-backdrop-filter:none!important;backdrop-filter:none!important;box-shadow:0 16px 42px rgba(5,10,18,.11)!important}html body #zpContactFormLight .zpContactFormLight__mode input:checked + span,html body #zpContactFormLight .zpContactFormLight__mode label:hover span,html body .zpContactFormLight .zpContactFormLight__mode input:checked + span,html body .zpContactFormLight .zpContactFormLight__mode label:hover span{background:#020407!important;background-color:#020407!important;background-image:var(--zp-contact-dark-card-soft)!important;border-color:rgba(255,255,255,.14)!important;color:#fff!important;box-shadow:inset 0 1px 0 rgba(255,255,255,.10),0 22px 56px rgba(2,4,10,.20)!important}html body #zpContactFormLight .zpContactFormLight__mode input:checked + span::before,html body #zpContactFormLight .zpContactFormLight__mode label:hover span::before,html body .zpContactFormLight .zpContactFormLight__mode input:checked + span::before,html body .zpContactFormLight .zpContactFormLight__mode label:hover span::before{background:var(--zp-contact-dark-card-soft)!important;opacity:1!important;transform:scale(1)!important}html body #zpContactFormLight .zpContactFormLight__mode input:checked + span::after,html body #zpContactFormLight .zpContactFormLight__mode label:hover span::after,html body .zpContactFormLight .zpContactFormLight__mode input:checked + span::after,html body .zpContactFormLight .zpContactFormLight__mode label:hover span::after{opacity:.24!important;background:radial-gradient(circle at 50% 0%,rgba(255,255,255,.24),transparent 58%)!important}html body #zpContactSystemLight .zpContactSystemLight__service.is-active,html body #zpContactSystemLight .zpContactSystemLight__service.is-active:hover,html body #zpContactSystemLight .zpContactSystemLight__service.is-active:focus,html body #zpContactSystemLight .zpContactSystemLight__service.is-active:active,html body .zpContactSystemLight .zpContactSystemLight__service.is-active,html body .zpContactSystemLight .zpContactSystemLight__service.is-active:hover,html body .zpContactSystemLight .zpContactSystemLight__service.is-active:focus,html body .zpContactSystemLight .zpContactSystemLight__service.is-active:active{--active:1!important;background:#020407!important;background-color:#020407!important;background-image:var(--zp-contact-dark-card-soft)!important;border-color:rgba(255,255,255,.14)!important;color:#fff!important;box-shadow:inset 0 1px 0 rgba(255,255,255,.10),0 22px 56px rgba(2,4,10,.18)!important;outline:0!important}html body #zpContactSystemLight .zpContactSystemLight__service.is-active::before,html body .zpContactSystemLight .zpContactSystemLight__service.is-active::before{background:var(--zp-contact-dark-card-soft)!important;opacity:1!important;z-index:0!important}html body #zpContactSystemLight .zpContactSystemLight__service.is-active::after,html body .zpContactSystemLight .zpContactSystemLight__service.is-active::after{background:linear-gradient(90deg,rgba(255,255,255,.28),rgba(143,184,234,.32),rgba(255,255,255,.18))!important;opacity:.72!important}html body #zpContactSystemLight .zpContactSystemLight__service.is-active > svg,html body #zpContactSystemLight .zpContactSystemLight__service.is-active strong,html body #zpContactSystemLight .zpContactSystemLight__service.is-active p,html body #zpContactSystemLight .zpContactSystemLight__service.is-active em,html body .zpContactSystemLight .zpContactSystemLight__service.is-active > svg,html body .zpContactSystemLight .zpContactSystemLight__service.is-active strong,html body .zpContactSystemLight .zpContactSystemLight__service.is-active p,html body .zpContactSystemLight .zpContactSystemLight__service.is-active em{color:#fff!important;stroke:#fff!important}html body #zpContactSystemLight .zpContactSystemLight__service.is-active p,html body .zpContactSystemLight .zpContactSystemLight__service.is-active p{color:rgba(255,255,255,.70)!important}html body #zpContactSystemLight .zpContactSystemLight__service.is-active em,html body .zpContactSystemLight .zpContactSystemLight__service.is-active em{color:rgba(255,255,255,.58)!important}html body #zpContactSystemLight .zpContactSystemLight__tab.is-active,html body #zpContactSystemLight .zpContactSystemLight__tab[aria-selected="true"],html body .zpContactSystemLight .zpContactSystemLight__tab.is-active,html body .zpContactSystemLight .zpContactSystemLight__tab[aria-selected="true"]{background:#020407!important;background-color:#020407!important;background-image:var(--zp-contact-dark-card-soft)!important;border-color:rgba(255,255,255,.14)!important;color:#fff!important;box-shadow:inset 0 1px 0 rgba(255,255,255,.10),0 18px 46px rgba(2,4,10,.18)!important}html body #zpContactSystemLight .zpContactSystemLight__tab.is-active strong,html body #zpContactSystemLight .zpContactSystemLight__tab[aria-selected="true"] strong,html body #zpContactSystemLight .zpContactSystemLight__tab.is-active em,html body #zpContactSystemLight .zpContactSystemLight__tab[aria-selected="true"] em,html body .zpContactSystemLight .zpContactSystemLight__tab.is-active strong,html body .zpContactSystemLight .zpContactSystemLight__tab[aria-selected="true"] strong,html body .zpContactSystemLight .zpContactSystemLight__tab.is-active em,html body .zpContactSystemLight .zpContactSystemLight__tab[aria-selected="true"] em{color:#fff!important}html body #zpContactSystemLight .zpContactSystemLight__tab.is-active > span,html body #zpContactSystemLight .zpContactSystemLight__tab[aria-selected="true"] > span,html body .zpContactSystemLight .zpContactSystemLight__tab.is-active > span,html body .zpContactSystemLight .zpContactSystemLight__tab[aria-selected="true"] > span{background:rgba(255,255,255,.36)!important}html body #zpContactSystemLight .zpContactSystemLight__selected,html body .zpContactSystemLight .zpContactSystemLight__selected,html body #zpContactSystemLight .zpContactSystemLight__selected.zpContactSystemLight__selected--wa,html body .zpContactSystemLight .zpContactSystemLight__selected.zpContactSystemLight__selected--wa{background:#020407!important;background-color:#020407!important;background-image:var(--zp-contact-dark-card-soft)!important;border-color:rgba(255,255,255,.12)!important;color:#fff!important;box-shadow:inset 0 1px 0 rgba(255,255,255,.08),0 18px 46px rgba(2,4,10,.18)!important}html body #zpContactSystemLight .zpContactSystemLight__selected span,html body .zpContactSystemLight .zpContactSystemLight__selected span{color:rgba(255,255,255,.58)!important}html body #zpContactSystemLight .zpContactSystemLight__selected strong,html body .zpContactSystemLight .zpContactSystemLight__selected strong{color:#fff!important}</style>
  <script id="zp-suite-2-2-188-header-js">
  (function(){
    var a=<?php echo json_encode(number_format($a,3,'.','')); ?>;
    var top=<?php echo json_encode(number_format($top,3,'.','')); ?>;
    var bot=<?php echo json_encode(number_format($bot,3,'.','')); ?>;
    function applyHeader(){
      var nav=document.getElementById('zpNewNav');
      if(!nav || nav.classList.contains('is-mega-open')) return;
      var scrolled=nav.classList.contains('is-scrolled') || document.body.classList.contains('zp-nav-scrolled') || window.scrollY>8;
      if(!scrolled) return;
      var bg='rgba(255,255,255,'+a+')';
      var grad='linear-gradient(180deg,rgba(255,255,255,'+top+'),rgba(255,255,255,'+bot+'))';
      nav.querySelectorAll('.zpNewNav__shell,.zpNewNav__mobileBar').forEach(function(el){
        el.style.setProperty('background',bg,'important');
        el.style.setProperty('background-color',bg,'important');
        el.style.setProperty('background-image',grad,'important');
        el.style.setProperty('-webkit-backdrop-filter','none','important');
        el.style.setProperty('backdrop-filter','none','important');
      });
    }
    ['scroll','resize','load'].forEach(function(evt){window.addEventListener(evt,applyHeader,{passive:true});});
    document.addEventListener('DOMContentLoaded',function(){applyHeader(); setTimeout(applyHeader,120); setTimeout(applyHeader,700); setTimeout(applyHeader,1600);});
  })();
  </script>
  <?php
}, 2147483647);
