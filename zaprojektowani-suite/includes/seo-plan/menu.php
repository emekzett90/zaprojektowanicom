<?php
if (!defined('ABSPATH')) { exit; }

/**
 * Styles for the SEO-plan subpages in the header menu (templates/header.php, suite 2.5.0).
 *
 * The new links reuse the mega menu's own items. Only two things are new: the
 * "Strony dla branż" group, whose items carry no description, and its label in the mobile
 * menu, set like the menu's other small caps labels. Since 2.8.0 (eight industries, Mat
 * 7.10: "sekcja w mega menu z fajnymi ikonami lucide") the group is its own section: a soft
 * panel under "Strony & sklepy", "Sprzedaż" and the promo card, its items lined up with those
 * three columns, with tinted icon tiles (the lucide icons the menu already loads). The space
 * under "Start marki" stays free for Mateusz. In the mobile menu the same group gets the
 * tinted tiles too. On short laptop screens the mega menu gets slightly tighter spacing, and
 * it scrolls inside when it is still taller than the window, so its bottom bar is never cut off.
 */
add_action('wp_head', function () {
  if (!zp_seo_plan_active() || zp_seo_plan_is_en() || is_admin()) { return; }
  echo '<style id="zp-seo-plan-menu-250">'
    . '.zpNewNav__megaCol--branze .zpNewNav__megaHead.zpNewNav__megaHead--branze{margin-top:0!important;margin-bottom:14px!important}'
    . '.zpNewNav__megaList.zpNewNav__megaList--branze{gap:2px 61px!important}'
    // Row 2 of the grid, columns 2-4; its own columns match the content width of the three
    // columns above (each has 30px padding on both sides and a 1px divider, the last is 340px).
    // The panel's content box starts where the content of "Strony & sklepy" starts (6px margin,
    // 1px border, 24px padding = the column's 1px divider and 30px padding; .zpNewNav__megaGrid in
    // the selector outweighs the columns' own "+ column" padding and divider rule) and ends with the
    // promo card's content, so the 3 inner columns line up with the 3 columns above.
    . '@media (min-width:1101px){'
    . 'html body .zpNewNav__mega .zpNewNav__megaGrid .zpNewNav__megaCol.zpNewNav__megaCol--branze{grid-column:2 / -1;margin:26px 5px 0 6px;padding:20px 24px 12px!important;border:1px solid rgba(143,184,234,.18)!important;border-radius:24px;background:linear-gradient(135deg,rgba(143,184,234,.085) 0%,rgba(255,255,255,.018) 100%)}'
    . 'html body .zpNewNav__mega.is-light-menu .zpNewNav__megaGrid .zpNewNav__megaCol.zpNewNav__megaCol--branze{border-color:rgba(28,71,122,.1)!important;background:linear-gradient(135deg,#f2f7fe 0%,#fafcff 100%)}'
    . 'html body .zpNewNav__mega .zpNewNav__megaList.zpNewNav__megaList--branze{grid-template-columns:repeat(2,calc((100% - 279px) / 2 - 61px)) 279px}'
    . 'html body .zpNewNav__mega .zpNewNav__megaGrid .zpNewNav__megaCol.zpNewNav__megaCol--branze .zpNewNav__megaLink .zpNewNav__megaIco.zpNewNav__megaIco{background:linear-gradient(150deg,rgba(143,184,234,.36) 0%,rgba(59,110,168,.24) 100%)!important;border-color:rgba(143,184,234,.5)!important;color:#e6f1ff!important}'
    . 'html body .zpNewNav__mega .zpNewNav__megaGrid .zpNewNav__megaCol.zpNewNav__megaCol--branze .zpNewNav__megaLink .zpNewNav__megaIco.zpNewNav__megaIco svg{stroke:#e6f1ff!important;color:#e6f1ff!important}'
    . 'html body .zpNewNav__mega .zpNewNav__megaGrid .zpNewNav__megaCol.zpNewNav__megaCol--branze .zpNewNav__megaLink:hover .zpNewNav__megaIco.zpNewNav__megaIco,html body .zpNewNav__mega .zpNewNav__megaGrid .zpNewNav__megaCol.zpNewNav__megaCol--branze .zpNewNav__megaLink:focus-visible .zpNewNav__megaIco.zpNewNav__megaIco{background:#8fb8ea!important;border-color:#8fb8ea!important}'
    . 'html body .zpNewNav__mega .zpNewNav__megaGrid .zpNewNav__megaCol.zpNewNav__megaCol--branze .zpNewNav__megaLink:hover .zpNewNav__megaIco.zpNewNav__megaIco svg,html body .zpNewNav__mega .zpNewNav__megaGrid .zpNewNav__megaCol.zpNewNav__megaCol--branze .zpNewNav__megaLink:focus-visible .zpNewNav__megaIco.zpNewNav__megaIco svg{stroke:#071426!important;color:#071426!important}'
    . 'html body .zpNewNav__mega.is-light-menu .zpNewNav__megaGrid .zpNewNav__megaCol.zpNewNav__megaCol--branze .zpNewNav__megaLink .zpNewNav__megaIco.zpNewNav__megaIco{background:linear-gradient(150deg,#e6f0fe 0%,#d8e7fa 100%)!important;border-color:rgba(28,71,122,.16)!important;color:#1c477a!important;box-shadow:none!important}'
    . 'html body .zpNewNav__mega.is-light-menu .zpNewNav__megaGrid .zpNewNav__megaCol.zpNewNav__megaCol--branze .zpNewNav__megaLink .zpNewNav__megaIco.zpNewNav__megaIco svg{stroke:#1c477a!important;color:#1c477a!important}'
    . 'html body .zpNewNav__mega.is-light-menu .zpNewNav__megaGrid .zpNewNav__megaCol.zpNewNav__megaCol--branze .zpNewNav__megaLink:hover .zpNewNav__megaIco.zpNewNav__megaIco,html body .zpNewNav__mega.is-light-menu .zpNewNav__megaGrid .zpNewNav__megaCol.zpNewNav__megaCol--branze .zpNewNav__megaLink:focus-visible .zpNewNav__megaIco.zpNewNav__megaIco{background:#1c477a!important;border-color:#1c477a!important}'
    . 'html body .zpNewNav__mega.is-light-menu .zpNewNav__megaGrid .zpNewNav__megaCol.zpNewNav__megaCol--branze .zpNewNav__megaLink:hover .zpNewNav__megaIco.zpNewNav__megaIco svg,html body .zpNewNav__mega.is-light-menu .zpNewNav__megaGrid .zpNewNav__megaCol.zpNewNav__megaCol--branze .zpNewNav__megaLink:focus-visible .zpNewNav__megaIco.zpNewNav__megaIco svg{stroke:#fff!important;color:#fff!important}'
    . 'html body .zpNewNav__mega--portal.is-portal-open .zpNewNav__megaCol--branze{transition-delay:.29s}'
    . '}'
    // Mobile menu: the industry group's icons get the same tinted tiles.
    . 'html body header#zpNewNav .zpNewNav__drawer .zpNewNav__mItem--branze .zpNewNav__mSub--cards .zpNewNav__mSubIcon.zpNewNav__mSubIcon,html body .zpNewNav__drawer.is-open .zpNewNav__mItem--branze .zpNewNav__mSub--cards .zpNewNav__mSubIcon.zpNewNav__mSubIcon{width:34px!important;height:34px!important;min-width:34px!important;flex:0 0 34px!important;border-radius:11px!important;background:linear-gradient(150deg,#e6f0fe 0%,#d8e7fa 100%)!important;border:1px solid rgba(28,71,122,.14)!important;color:#1c477a!important}'
    . 'html body header#zpNewNav .zpNewNav__drawer .zpNewNav__mItem--branze .zpNewNav__mSub--cards .zpNewNav__mSubIcon.zpNewNav__mSubIcon svg,html body .zpNewNav__drawer.is-open .zpNewNav__mItem--branze .zpNewNav__mSub--cards .zpNewNav__mSubIcon.zpNewNav__mSubIcon svg{width:18px!important;height:18px!important;stroke:#1c477a!important;color:#1c477a!important}'
    . '.zpNewNav__megaList--branze .zpNewNav__megaLink{align-items:center!important;padding-top:6px!important;padding-bottom:6px!important}'
    . '.zpNewNav__megaList--branze .zpNewNav__megaTitle{margin:0!important;font-size:17px!important}'
    . '.zpNewNav__mSub .zpNewNav__mSubHead{margin:16px 0 0;padding:0 0 0 6px;font-size:11px;font-weight:800;line-height:1.4;letter-spacing:.14em;text-transform:uppercase;color:rgba(7,20,38,.5)}'
    . '@media (min-width:1101px){html body .zpNewNav__mega.zpNewNav__mega.zpNewNav__mega{max-height:calc(100vh - var(--zp-mega-top,88px));overflow-y:auto!important;overscroll-behavior:contain}}'
    . '@media (min-width:1101px) and (max-height:860px){'
    . 'html body .zpNewNav__mega.zpNewNav__mega.zpNewNav__mega .zpNewNav__megaInner.zpNewNav__megaInner{padding-top:34px!important}'
    . 'html body .zpNewNav__mega.zpNewNav__mega.zpNewNav__mega .zpNewNav__megaList.zpNewNav__megaList{gap:10px!important}'
    . 'html body .zpNewNav__mega.zpNewNav__mega.zpNewNav__mega .zpNewNav__megaHead.zpNewNav__megaHead{margin-bottom:16px!important}'
    . 'html body .zpNewNav__mega.zpNewNav__mega.zpNewNav__mega .zpNewNav__promoCard.zpNewNav__promoCard{min-height:270px!important}'
    . 'html body .zpNewNav__mega.zpNewNav__mega.zpNewNav__mega .zpNewNav__megaCol.zpNewNav__megaCol--branze{margin-top:20px!important;padding-top:16px!important;padding-bottom:10px!important}'
    . 'html body .zpNewNav__mega.zpNewNav__mega.zpNewNav__mega .zpNewNav__megaCol--branze .zpNewNav__megaHead.zpNewNav__megaHead--branze{margin-bottom:10px!important}'
    . 'html body .zpNewNav__mega.zpNewNav__mega.zpNewNav__mega .zpNewNav__megaList--branze.zpNewNav__megaList{gap:0 61px!important}'
    . '}'
    . '</style>' . "\n";
}, 40);

/**
 * Suite 2.6.0: the promo card shows logo, website or shop at random, and Mateusz stands on the
 * mega menu's bottom bar with a short "choose" bubble.
 *
 * All three promo cards are in the page (the page cache keeps one copy of the HTML), the other
 * two carry `hidden`. A small script picks one when the page loads and a different one each time
 * the menu is opened again. Without JavaScript the logo card stays, as before.
 *
 * Mateusz is a cut-out photo whose lower edge fades into the bar. He stands in the empty space
 * under the "Start marki" column, the shortest one, and gestures towards the bubble. Below 1240px
 * width that space is too narrow, so he is not shown there; on short laptop screens he is smaller.
 */
add_action('wp_head', function () {
  if (!zp_seo_plan_active() || zp_seo_plan_is_en() || is_admin()) { return; }
  echo '<style id="zp-seo-plan-menu-260">'
    . 'html body .zpNewNav__mega .zpNewNav__promoCard.zpNewNav__promoCard[hidden]{display:none!important}'
    . 'html body .zpNewNav__mega .zpNewNav__promoCard--strona .zpNewNav__promoTitle,html body .zpNewNav__mega .zpNewNav__promoCard--sklep .zpNewNav__promoTitle{font-size:30px!important;line-height:1.04!important}'
    . 'html body .zpNewNav__mega .zpNewNav__promoCard--strona .zpNewNav__promoMedia::after,html body .zpNewNav__mega .zpNewNav__promoCard--sklep .zpNewNav__promoMedia::after{background:linear-gradient(180deg,rgba(4,10,20,.12) 0%,rgba(4,10,20,.55) 30%,rgba(2,6,13,.93) 56%,rgba(2,6,13,.97) 100%)!important}'
    . 'html body .zpNewNav__mega .zpNewNav__promoCard--strona .zpNewNav__promoMedia img,html body .zpNewNav__mega .zpNewNav__promoCard--sklep .zpNewNav__promoMedia img{object-position:center top!important}'
    . '.zpNewNav__megaGuide{display:none}'
    . '@media (min-width:1240px){'
    . 'html body .zpNewNav__mega .zpNewNav__megaBottomInner{position:relative!important}'
    . 'html body .zpNewNav__mega .zpNewNav__megaGuide{display:block;position:absolute;right:calc(75% + 16px);bottom:calc(100% - 14px);height:170px;z-index:1;pointer-events:none;transition:opacity .45s ease .22s,transform .55s cubic-bezier(.16,1,.3,1) .22s}'
    . 'html body .zpNewNav__mega--portal:not(.is-portal-open) .zpNewNav__megaGuide{opacity:0;transform:translateY(14px)}'
    . 'html body .zpNewNav__mega .zpNewNav__megaGuideImg{display:block!important;height:100%!important;width:auto!important;max-width:none!important;margin:0!important;border-radius:0!important;box-shadow:none!important}'
    . 'html body .zpNewNav__mega .zpNewNav__megaGuideBubble{position:absolute;right:66%;bottom:54%;width:max-content;max-width:150px;padding:9px 13px;border-radius:16px 16px 4px 16px;background:#102a4f;border:1px solid rgba(142,200,247,.28);color:#fff;font-size:13px;font-weight:700;line-height:1.35;letter-spacing:0;text-align:left;box-shadow:0 14px 34px rgba(2,6,12,.35)}'
    . 'html body .zpNewNav__mega.is-light-menu .zpNewNav__megaGuideBubble{background:#f4f7fb;border-color:rgba(7,20,38,.1);color:#071426;box-shadow:0 12px 28px rgba(7,20,38,.1)}'
    . '}'
    . '@media (min-width:1240px) and (max-width:1399px){html body .zpNewNav__mega .zpNewNav__megaGuide{height:140px}}'
    . '@media (min-width:1240px) and (max-height:860px){html body .zpNewNav__mega .zpNewNav__megaGuide{height:128px}html body .zpNewNav__mega .zpNewNav__megaGuideBubble{font-size:12.5px;padding:8px 12px}}'
    . '@media (prefers-reduced-motion:reduce){html body .zpNewNav__mega .zpNewNav__megaGuide{transition:none;opacity:1;transform:none}}'
    . '</style>' . "\n";
}, 41);

add_action('wp_footer', function () {
  if (!zp_seo_plan_active() || zp_seo_plan_is_en() || is_admin()) { return; }
  echo '<script id="zp-seo-plan-menu-260-js">(function(){'
    . 'var cards=[].slice.call(document.querySelectorAll(".zpNewNav__promoCard[data-zp-promo]"));if(cards.length<2)return;'
    . 'var cur=-1;function show(i){cards.forEach(function(c,k){if(k===i){c.removeAttribute("hidden")}else{c.setAttribute("hidden","")}});cur=i}'
    . 'function pick(){var i=Math.floor(Math.random()*cards.length);if(i===cur){i=(i+1)%cards.length}show(i)}'
    . 'pick();var mega=cards[0].closest(".zpNewNav__mega");if(!mega||!window.MutationObserver)return;'
    . 'function isOpen(){return !mega.hasAttribute("hidden")&&(mega.classList.contains("is-portal-open")||!mega.classList.contains("zpNewNav__mega--portal"))}'
    . 'var open=isOpen(),seen=open;new MutationObserver(function(){var o=isOpen();if(o&&!open){if(seen){pick()}seen=true}open=o}).observe(mega,{attributes:true,attributeFilter:["class","hidden"]});'
    . '})();</script>' . "\n";
}, 40);
