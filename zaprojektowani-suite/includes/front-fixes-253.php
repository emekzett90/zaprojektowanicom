<?php
/**
 * v2.2.436 — Logo Branding selected section widths only.
 * Scope: /logo-branding-katowice/ section containers listed by client.
 * No global 1750px override, no hero changes.
 */
if (!defined('ABSPATH')) { exit; }

function zp_suite_253_logo_branding_widths_css() {
  ?>
<style id="zp-suite-front-fixes-253-logo-branding-widths-v436">@media (min-width:1025px){html body #zpBrandTrust114 .zpBrandTrust__inner,html body #zpTrustCert .zpTrustCert__inner,html body #zpLogoPortfolio .zpLogoPortfolio__wrap,html body #zpLogoPortfolio .zpLogoPortfolio__toolsInner,html body #oferta-logo-branding .zpBrandPackages__inner,html body #zpShopProcessFlow .zpShopProcessFlow__inner,html body #zpShopProcessFlow .zpShopProcessFlow__pinInner,html body #zpShopProcessFlow .zpShopProcessFlow__inner--bottom,html body .zpLbSeoBoost .zpLbSeoBoost__inner{width:min(1750px,calc(100% - clamp(28px,6vw,112px)))!important;max-width:1750px!important;margin-left:auto!important;margin-right:auto!important}html body #zpBrandTrust114 .zpBrandTrust__inner{padding-left:0!important;padding-right:0!important}html body #zpLogoPortfolio{--wrap:min(1750px,calc(100vw - clamp(28px,6vw,112px)))!important;--wrapLeft:calc((100vw - var(--wrap)) / 2)!important}}@media (min-width:761px) and (max-width:1024px){html body #zpBrandTrust114 .zpBrandTrust__inner,html body #zpTrustCert .zpTrustCert__inner,html body #zpLogoPortfolio .zpLogoPortfolio__wrap,html body #zpLogoPortfolio .zpLogoPortfolio__toolsInner,html body #oferta-logo-branding .zpBrandPackages__inner,html body #zpShopProcessFlow .zpShopProcessFlow__inner,html body #zpShopProcessFlow .zpShopProcessFlow__pinInner,html body #zpShopProcessFlow .zpShopProcessFlow__inner--bottom,html body .zpLbSeoBoost .zpLbSeoBoost__inner{width:calc(100% - 32px)!important;max-width:none!important;margin-left:auto!important;margin-right:auto!important}html body #zpBrandTrust114 .zpBrandTrust__inner{padding-left:0!important;padding-right:0!important}}</style>
  <?php
}
add_action('wp_head', 'zp_suite_253_logo_branding_widths_css', 120);
