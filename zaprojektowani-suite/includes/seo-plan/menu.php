<?php
if (!defined('ABSPATH')) { exit; }

/**
 * Styles for the SEO-plan subpages in the header menu (templates/header.php, suite 2.5.0).
 *
 * The new links reuse the mega menu's own items. Only two things are new: the
 * "Strony dla branż" group under the promo card, whose items carry no description so the
 * column keeps the height of the others, and its label in the mobile menu, set like the
 * menu's other small caps labels. On short laptop screens the mega menu gets slightly
 * tighter spacing, and it scrolls inside when it is still taller than the window, so its
 * bottom bar is never cut off.
 */
add_action('wp_head', function () {
  if (!zp_seo_plan_active() || zp_seo_plan_is_en() || is_admin()) { return; }
  echo '<style id="zp-seo-plan-menu-250">'
    . '.zpNewNav__megaPromoCol .zpNewNav__megaHead.zpNewNav__megaHead--branze{margin-top:30px!important;margin-bottom:14px!important}'
    . '.zpNewNav__megaList.zpNewNav__megaList--branze{gap:2px!important}'
    . '.zpNewNav__megaList--branze .zpNewNav__megaLink{align-items:center!important;padding-top:6px!important;padding-bottom:6px!important}'
    . '.zpNewNav__megaList--branze .zpNewNav__megaTitle{margin:0!important;font-size:17px!important}'
    . '.zpNewNav__mSub .zpNewNav__mSubHead{margin:16px 0 0;padding:0 0 0 6px;font-size:11px;font-weight:800;line-height:1.4;letter-spacing:.14em;text-transform:uppercase;color:rgba(7,20,38,.5)}'
    . '@media (min-width:1101px){html body .zpNewNav__mega.zpNewNav__mega.zpNewNav__mega{max-height:calc(100vh - var(--zp-mega-top,88px));overflow-y:auto!important;overscroll-behavior:contain}}'
    . '@media (min-width:1101px) and (max-height:860px){'
    . 'html body .zpNewNav__mega.zpNewNav__mega.zpNewNav__mega .zpNewNav__megaInner.zpNewNav__megaInner{padding-top:34px!important}'
    . 'html body .zpNewNav__mega.zpNewNav__mega.zpNewNav__mega .zpNewNav__megaList.zpNewNav__megaList{gap:10px!important}'
    . 'html body .zpNewNav__mega.zpNewNav__mega.zpNewNav__mega .zpNewNav__megaHead.zpNewNav__megaHead{margin-bottom:16px!important}'
    . 'html body .zpNewNav__mega.zpNewNav__mega.zpNewNav__mega .zpNewNav__promoCard.zpNewNav__promoCard{min-height:270px!important}'
    . 'html body .zpNewNav__mega.zpNewNav__mega.zpNewNav__mega .zpNewNav__megaPromoCol .zpNewNav__megaHead.zpNewNav__megaHead--branze{margin-top:20px!important;margin-bottom:10px!important}'
    . 'html body .zpNewNav__mega.zpNewNav__mega.zpNewNav__mega .zpNewNav__megaList--branze.zpNewNav__megaList{gap:0!important}'
    . '}'
    . '</style>' . "\n";
}, 40);
