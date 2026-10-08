<?php
if (!defined('ABSPATH')) { exit; }

/**
 * ZP Suite 2.9.4 — two home page sections (Mat 8.10).
 *
 * "SEO • usługi • branże" (templates/seo-industries.php) has a tile for every industry page.
 * The four industries with a photo of a real project keep the photo tiles. The others get the
 * same tile without a photo (.zpHomeSeo__card): on computers a shorter row under the photo tiles,
 * with the same borders, icon circle, number and hover; on phones and tablets a list under the
 * swipeable photo tiles.
 *
 * "Strony internetowe dla firm z całej Polski" (templates/home-seo-intro.php, above the footer)
 * was four long paragraphs beside a list of four links. It keeps the same texts, now laid out as
 * a lead paragraph with the links beside it and three short points with headings below, so it
 * reads less like a block of text. The links get an arrow (drawn in CSS).
 *
 * Colours, fonts, borders and hover effects are taken from the existing tiles and lists.
 * No "[class...] *" or bare ":focus" rules (see speed-293.php).
 */
add_action('wp_head', function () {
  if (is_admin() || !is_front_page()) { return; }
  $line = 'rgba(255,255,255,.13)';
  // Lucide "arrow-up-right" as a mask, painted in the link's text colour.
  $arrow = 'url("data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 24 24%27 fill=%27none%27 stroke=%27%23000%27 stroke-width=%272%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27%3E%3Cpath d=%27M7 7h10v10%27/%3E%3Cpath d=%27M7 17 17 7%27/%3E%3C/svg%3E") center / contain no-repeat';
  echo '<style id="zp-home-294">'
    // Tiles without a photo, computers: one row of up to four under the photo tiles.
    . 'html body .zpHomeSeo .zpHomeSeo__more{position:relative;z-index:2;display:grid;grid-template-columns:repeat(4,minmax(0,1fr));border-left:1px solid ' . $line . ';background:rgba(255,255,255,.016)}'
    . 'html body .zpHomeSeo .zpHomeSeo__card{position:relative;display:flex;flex-direction:column;justify-content:flex-end;min-height:250px;padding:clamp(24px,2.1vw,34px);border-right:1px solid ' . $line . ';border-bottom:1px solid ' . $line . ';background:#05070b radial-gradient(circle at 18% 0%,rgba(59,110,168,.13),transparent 46%);color:#fff;text-decoration:none;overflow:hidden;transition:background .24s ease,border-color .24s ease,transform .24s cubic-bezier(.16,1,.3,1)}'
    . 'html body .zpHomeSeo .zpHomeSeo__card:hover,html body .zpHomeSeo .zpHomeSeo__card:focus-visible{transform:translateY(-3px);background:linear-gradient(rgba(255,255,255,.067),rgba(255,255,255,.024)),#05070b;border-color:rgba(59,110,168,.36)}'
    . 'html body .zpHomeSeo .zpHomeSeo__cardIcon{position:absolute;top:clamp(22px,2vw,30px);left:clamp(22px,2vw,30px);display:flex;align-items:center;justify-content:center;width:58px;height:58px;border:1px solid rgba(255,255,255,.14);border-radius:999px;background:rgba(255,255,255,.08);color:#fff}'
    . 'html body .zpHomeSeo .zpHomeSeo__cardIcon svg{width:22px;height:22px;stroke:currentColor}'
    . 'html body .zpHomeSeo .zpHomeSeo__cardNum{position:absolute;top:clamp(18px,1.7vw,28px);right:clamp(18px,1.7vw,28px);font-size:12px;line-height:1.2;font-weight:850;letter-spacing:.12em;color:rgba(255,255,255,.28)}'
    . 'html body .zpHomeSeo .zpHomeSeo__card strong{display:block;margin:0;font-size:clamp(21px,1.6vw,26px);line-height:1.05;font-weight:560;letter-spacing:-.045em;color:#fff}'
    . 'html body .zpHomeSeo .zpHomeSeo__cardText{display:block;margin-top:10px;max-width:420px;font-size:13px;line-height:1.58;color:rgba(255,255,255,.74)}'
    . '@media (max-width:1180px){html body .zpHomeSeo .zpHomeSeo__more{grid-template-columns:repeat(2,minmax(0,1fr))}}'
    // Phones and tablets: a list under the swipeable photo tiles.
    . '@media (max-width:900px){'
    . 'html body .zpHomeSeo .zpHomeSeo__more{grid-template-columns:1fr;gap:10px;margin-top:4px;border-left:0;background:none}'
    . 'html body .zpHomeSeo .zpHomeSeo__card{display:grid;grid-template-columns:46px minmax(0,1fr);column-gap:14px;align-content:center;align-items:center;min-height:0;padding:16px 46px 16px 16px;border:1px solid ' . $line . ';border-radius:22px}'
    . 'html body .zpHomeSeo .zpHomeSeo__card:hover,html body .zpHomeSeo .zpHomeSeo__card:focus-visible{transform:none}'
    . 'html body .zpHomeSeo .zpHomeSeo__cardIcon{position:static;grid-row:1 / span 2;width:46px;height:46px}'
    . 'html body .zpHomeSeo .zpHomeSeo__cardIcon svg{width:20px;height:20px}'
    . 'html body .zpHomeSeo .zpHomeSeo__cardNum{top:16px;right:18px}'
    . 'html body .zpHomeSeo .zpHomeSeo__card strong{grid-column:2;font-size:18px;line-height:1.15;letter-spacing:-.035em}'
    . 'html body .zpHomeSeo .zpHomeSeo__cardText{grid-column:2;margin-top:4px;font-size:12.5px;line-height:1.5}'
    . '}'
    // "Strony internetowe dla firm z całej Polski": lead and links, then three points.
    . 'html body .zpHomeSeoIntro--294 .zpHomeSeoIntro__inner{align-items:end;row-gap:0}'
    . 'html body .zpHomeSeoIntro--294 .zpHomeSeoIntro__copy p{max-width:780px;font-size:clamp(16px,1.3vw,19px);line-height:1.7}'
    // The arrow is drawn in CSS, so the links' text and its English version stay as they were.
    . 'html body .zpHomeSeoIntro--294 .zpHomeSeoIntro__links a::after{content:"";flex:0 0 18px;width:18px;height:18px;margin-left:auto;background:currentColor;opacity:.42;-webkit-mask:' . $arrow . ';mask:' . $arrow . ';transition:transform .22s ease,opacity .22s ease}'
    . 'html body .zpHomeSeoIntro--294 .zpHomeSeoIntro__links a:hover::after,html body .zpHomeSeoIntro--294 .zpHomeSeoIntro__links a:focus-visible::after{opacity:1;transform:translate(2px,-2px)}'
    . 'html body .zpHomeSeoIntro--294 .zpHomeSeoIntro__points{grid-column:1 / -1;display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:clamp(28px,3.4vw,64px);margin-top:clamp(52px,5vw,84px)}'
    . 'html body .zpHomeSeoIntro--294 .zpHomeSeoIntro__point{position:relative;padding-top:26px;border-top:1px solid rgba(6,16,31,.12)}'
    . 'html body .zpHomeSeoIntro--294 .zpHomeSeoIntro__point::before{content:"";position:absolute;top:-1px;left:0;width:44px;height:2px;background:#071426}'
    . 'html body .zpHomeSeoIntro--294 .zpHomeSeoIntro__point h3{margin:0 0 12px;font-size:clamp(19px,1.45vw,22px);line-height:1.22;font-weight:680;letter-spacing:-.03em;color:#05101f}'
    . 'html body .zpHomeSeoIntro--294 .zpHomeSeoIntro__point p{margin:0;max-width:none;font-size:clamp(15px,1.08vw,16px);line-height:1.72;color:#445165}'
    . '@media (max-width:900px){'
    . 'html body .zpHomeSeoIntro--294 .zpHomeSeoIntro__inner{row-gap:20px}'
    . 'html body .zpHomeSeoIntro--294 .zpHomeSeoIntro__points{grid-template-columns:1fr;gap:26px;margin-top:20px}'
    . 'html body .zpHomeSeoIntro--294 .zpHomeSeoIntro__point{padding-top:20px}'
    . 'html body .zpHomeSeoIntro--294 .zpHomeSeoIntro__point h3{margin-bottom:8px;font-size:19px}'
    . 'html body .zpHomeSeoIntro--294 .zpHomeSeoIntro__point p{font-size:15px;line-height:1.65}'
    . '}'
    . '@media (prefers-reduced-motion:reduce){html body .zpHomeSeo .zpHomeSeo__card,html body .zpHomeSeoIntro--294 .zpHomeSeoIntro__links a::after{transition:none}}'
    . '</style>' . "\n";
}, 60);
