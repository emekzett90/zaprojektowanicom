<?php
if (!defined('ABSPATH')) { exit; }

/**
 * ZP Suite v2.2.117 — home hero/trust visual patch.
 * - trust logos: dark navy/black hover, rounded like cards, white logos on hover
 * - hero: remove square/box-like overlay shadow around team image
 * - hero eyebrow: shorter label without "Katowice i cała Polska"
 */
add_action('init', function () {
  $done_key = 'zp_suite_home_visual_patch_2_2_117';
  if (get_option($done_key) === '1') { return; }

  $opts = get_option('zp_suite_options', []);
  if (!is_array($opts)) { $opts = []; }
  if (empty($opts['hero']) || !is_array($opts['hero'])) { $opts['hero'] = []; }

  $current = isset($opts['hero']['eyebrow']) ? (string) $opts['hero']['eyebrow'] : '';
  if ($current === '' || stripos($current, 'Katowice') !== false || stripos($current, 'cała Polska') !== false || stripos($current, 'cala Polska') !== false) {
    $opts['hero']['eyebrow'] = 'Strony internetowe • sklepy • branding';
    update_option('zp_suite_options', $opts, false);
  }

  update_option($done_key, '1', false);
}, 30);

add_action('wp_head', function () {
  if (is_admin()) { return; }
  ?>
  <style id="zp-suite-22117-home-visual-fix">html body .zpNewHero .zpNewHero__personBox::before,html body .zpNewHero .zpNewHero__personBox::after{content:none!important;display:none!important;opacity:0!important;background:none!important;box-shadow:none!important;filter:none!important}html body .zpNewHero .zpNewHero__personBox,html body .zpNewHero .zpNewHero__person{box-shadow:none!important;background:transparent!important}html body .zpNewHero .zpNewHero__eyebrow{max-width:min(100%,560px)!important;white-space:normal!important;text-wrap:balance!important}@media(max-width:760px){html body .zpNewHero .zpNewHero__eyebrow{max-width:310px!important;font-size:8.5px!important;letter-spacing:.105em!important;line-height:1.35!important}}html body .zpTrustPinned .zpTrustPinned__logo{border-radius:24px!important;overflow:hidden!important;background:rgba(255,255,255,.72)!important;border:1px solid rgba(7,20,38,.105)!important;box-shadow:0 18px 44px rgba(7,20,38,.045)!important;isolation:isolate!important}html body .zpTrustPinned .zpTrustPinned__logo::before{content:""!important;position:absolute!important;inset:0!important;z-index:0!important;border-radius:inherit!important;background: radial-gradient(circle at 18% 0%,rgba(28,71,122,.36),transparent 42%),linear-gradient(135deg,#020407 0%,#05070b 32%,#071426 62%,#102a4f 100%)!important;opacity:0!important;transform:scale(.985)!important;transition:opacity .26s ease,transform .32s cubic-bezier(.16,1,.3,1)!important;pointer-events:none!important}html body .zpTrustPinned .zpTrustPinned__logo::after{content:""!important;position:absolute!important;inset:1px!important;z-index:1!important;border-radius:calc(24px - 1px)!important;border:1px solid rgba(255,255,255,.08)!important;opacity:0!important;pointer-events:none!important;transition:opacity .26s ease!important}html body .zpTrustPinned .zpTrustPinned__logo img,html body .zpTrustPinned .zpTrustPinned__logoImg{position:relative!important;z-index:2!important;filter:grayscale(1) brightness(0) contrast(1.22)!important;mix-blend-mode:multiply!important;opacity:.96!important;transition:filter .26s ease,opacity .26s ease,transform .34s cubic-bezier(.16,1,.3,1),mix-blend-mode .26s ease!important}html body .zpTrustPinned .zpTrustPinned__logo:hover,html body .zpTrustPinned .zpTrustPinned__logo:focus-within{border-color:rgba(255,255,255,.16)!important;box-shadow:0 24px 68px rgba(5,12,24,.20)!important;transform:translate3d(0,-5px,0) scale(1.025)!important;background:#05070b!important}html body .zpTrustPinned .zpTrustPinned__logo:hover::before,html body .zpTrustPinned .zpTrustPinned__logo:focus-within::before{opacity:1!important;transform:scale(1)!important}html body .zpTrustPinned .zpTrustPinned__logo:hover::after,html body .zpTrustPinned .zpTrustPinned__logo:focus-within::after{opacity:1!important}html body .zpTrustPinned .zpTrustPinned__logo:hover img,html body .zpTrustPinned .zpTrustPinned__logo:focus-within img,html body .zpTrustPinned .zpTrustPinned__logo:hover .zpTrustPinned__logoImg,html body .zpTrustPinned .zpTrustPinned__logo:focus-within .zpTrustPinned__logoImg{filter:brightness(0) invert(1) grayscale(1) contrast(1.22)!important;mix-blend-mode:screen!important;opacity:1!important;transform:translate3d(0,0,0) scale(calc(var(--zp-logo-scale,1) * 1.045))!important}@media(max-width:760px){html body .zpTrustPinned .zpTrustPinned__logo,html body .zpTrustPinned .zpTrustPinned__logo.is-mobile-active,html body .zpTrustPinned .zpTrustPinned__logo.is-mobile-prev,html body .zpTrustPinned .zpTrustPinned__logo.is-mobile-next{border-radius:24px!important}html body .zpTrustPinned .zpTrustPinned__logo::after{border-radius:23px!important}}</style>
  <?php
}, PHP_INT_MAX);
