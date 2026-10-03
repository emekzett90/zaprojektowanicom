<?php
/**
 * Plugin Name: Zaprojektowani Suite
 * Description: Zaprojektowani Suite z wersją angielską strony (PL/EN, adresy /en/, przełącznik języka), automatycznymi naprawami SEO, nagłówków, zasobów i paginacji na podstawie audytu z 13.09.2026.
 * Version: 2.6.2
 * Requires at least: 6.5
 * Requires PHP: 8.0
 * Author: Zaprojektowani.com
 */

if (!defined('ABSPATH')) {
  exit;
}

if (defined('ZP_SUITE_VERSION')) {
  return;
}

define('ZP_SUITE_VERSION', '2.6.2');
define('ZP_SUITE_PATH', plugin_dir_path(__FILE__));
define('ZP_SUITE_URL', plugin_dir_url(__FILE__));

// v2.3.0 — nationwide service pages reuse the Katowice templates; gating helpers load first.
require_once ZP_SUITE_PATH . 'includes/seo-plan/service-kind.php';

// Wersja angielska (PL/EN, adresy /en/) — moduł Zaprojektowani Languages wbudowany w Suite.
if (is_file(ZP_SUITE_PATH . 'zaprojektowani-languages/zaprojektowani-languages.php')) {
  define('ZPL_EMBEDDED', __FILE__);
  require_once ZP_SUITE_PATH . 'zaprojektowani-languages/zaprojektowani-languages.php';
}

require_once ZP_SUITE_PATH . 'includes/settings.php';
require_once ZP_SUITE_PATH . 'includes/cms.php';
require_once ZP_SUITE_PATH . 'includes/assets.php';
require_once ZP_SUITE_PATH . 'includes/initial-bg-2275.php';
require_once ZP_SUITE_PATH . 'includes/shortcodes.php';
require_once ZP_SUITE_PATH . 'includes/optimizer.php';
require_once ZP_SUITE_PATH . 'includes/mobile-sticky-cta.php';
require_once ZP_SUITE_PATH . 'includes/admin.php';
require_once ZP_SUITE_PATH . 'includes/seo.php';
require_once ZP_SUITE_PATH . 'includes/seobility-fixes-753.php';
require_once ZP_SUITE_PATH . 'includes/forms.php';
require_once ZP_SUITE_PATH . 'includes/analytics.php';
require_once ZP_SUITE_PATH . 'includes/command-center.php';
require_once ZP_SUITE_PATH . 'includes/error404.php';
require_once ZP_SUITE_PATH . 'includes/tools.php';
require_once ZP_SUITE_PATH . 'includes/experience-upgrades.php';
require_once ZP_SUITE_PATH . 'includes/floating-widgets.php';
require_once ZP_SUITE_PATH . 'includes/polish-fixes-166.php';
require_once ZP_SUITE_PATH . 'includes/stability-design-170.php';
require_once ZP_SUITE_PATH . 'includes/pagespeed-a11y-171.php';
require_once ZP_SUITE_PATH . 'includes/contact-card-restore-172.php';
require_once ZP_SUITE_PATH . 'includes/admin-cleanup-173.php';
require_once ZP_SUITE_PATH . 'includes/front-polish-180.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-181.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-182.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-183.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-184.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-185.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-186.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-187.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-188.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-191.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-192.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-193.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-195.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-196.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-198.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-199.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-200.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-201.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-203.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-204.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-205.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-206.php';
require_once ZP_SUITE_PATH . 'includes/katowice-page.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-207.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-208.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-213.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-214.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-215.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-216.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-217.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-218.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-219.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-220.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-223.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-224.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-225.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-226.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-228.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-229.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-230.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-231.php';
require_once ZP_SUITE_PATH . 'includes/about-page.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-232.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-233.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-234.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-235.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-236.php';
require_once ZP_SUITE_PATH . 'includes/studio-wyceny.php';
require_once ZP_SUITE_PATH . 'includes/thank-you-pages.php';
require_once ZP_SUITE_PATH . 'includes/meta-events.php';
require_once ZP_SUITE_PATH . 'includes/legal-pages.php';
require_once ZP_SUITE_PATH . 'includes/blog-knowledge.php';
require_once ZP_SUITE_PATH . 'includes/shop-katowice-page.php';
require_once ZP_SUITE_PATH . 'includes/logo-branding-katowice-page.php';
/* v2.2.688: nowa Strony internetowe Katowice — ładowana PO katowice-page.php,
   żeby jej rejestracja shortcode'ów wygrała ze starym rendererem sekcyjnym. */
require_once ZP_SUITE_PATH . 'includes/strony-internetowe-katowice-page.php';
/* v2.2.688: wspólne hero podstron usługowych (gradient, orbity, odstęp mobile). */
require_once ZP_SUITE_PATH . 'includes/service-hero-global.php';
require_once ZP_SUITE_PATH . 'includes/realizacje-v19-content.php';
require_once ZP_SUITE_PATH . 'includes/realizacje-cms.php';
require_once ZP_SUITE_PATH . 'includes/realizacje-route-v20.php';
require_once ZP_SUITE_PATH . 'includes/campaigns-page.php';
require_once ZP_SUITE_PATH . 'includes/faq-cms.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-237.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-238.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-239.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-240.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-241.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-242.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-243.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-244.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-245.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-246.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-249.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-250.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-251.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-252.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-253.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-254.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-255.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-256.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-257.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-258.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-259.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-260.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-261.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-262.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-263.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-264.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-265.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-266.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-267.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-268.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-269.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-270.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-271.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-272.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-273.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-274.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-277.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-278.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-279.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-280.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-281.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-282.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-283.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-284.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-285.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-286.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-287.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-288.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-289.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-290.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-291.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-292.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-293.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-294.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-295.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-296.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-297.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-298.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-299.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-300.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-301.php';

/**
 * Legacy snippets already contain their own shortcode registration.
 */
require_once ZP_SUITE_PATH . 'templates/legacy/legacy-portfolio.php';
require_once ZP_SUITE_PATH . 'templates/legacy/legacy-reviews.php';
require_once ZP_SUITE_PATH . 'includes/admin-menu-consolidation-186.php';
require_once ZP_SUITE_PATH . 'includes/header-rebuild-2506.php';


function zp_suite_upgrade_140(){
  $opts = get_option('zp_suite_options', []);
  if (is_array($opts)) {
    $long = 'Z Katowic projektujemy strony internetowe, sklepy WooCommerce i identyfikacje wizualne dla firm z całej Polski. Pomagamy markom usługowym, kancelariom, deweloperom, salonom beauty, lekarzom i producentom poukładać wizerunek, ofertę i ścieżkę zapytania tak, żeby strona nie tylko wyglądała dobrze, ale realnie pracowała na sprzedaż.';
    if (!empty($opts['hero']['lead']) && trim($opts['hero']['lead']) === $long) {
      $opts['hero']['lead'] = 'Z Katowic tworzymy strony, sklepy WooCommerce i branding dla firm z całej Polski. Łączymy design, SEO i kampanie tak, żeby marka wyglądała premium i realnie zdobywała zapytania.';
      update_option('zp_suite_options', $opts, false);
    }
  }
}

function zp_suite_upgrade_108_home_seo_rankmath(){
  $opts = get_option('zp_suite_options', []);
  if (!is_array($opts)) { $opts = []; }

  if (empty($opts['hero']) || !is_array($opts['hero'])) { $opts['hero'] = []; }
  $opts['hero']['eyebrow'] = 'Strony internetowe • sklepy • branding';
  $opts['hero']['h1_html'] = '<strong>Strony internetowe Katowice</strong> — sklepy internetowe, branding i kampanie dla firm z całej Polski.';
  $opts['hero']['lead'] = 'Strony internetowe Katowice to główny obszar naszej pracy: projektujemy strony WordPress, sklepy WooCommerce, logo, identyfikację wizualną i kampanie Meta Ads. Łączymy UX, SEO, treści i konwersję, żeby strona nie tylko wyglądała dobrze, ale też pomagała zdobywać zapytania.';
  $opts['hero']['primary_text'] = 'Wyceń projekt';
  $opts['hero']['primary_url'] = '/studio-wyceny/';
  $opts['hero']['secondary_text'] = 'Zobacz realizacje';
  $opts['hero']['secondary_url'] = '/realizacje/';
  $opts['hero']['chips'] = "01|strony internetowe Katowice\n02|projektowanie stron internetowych Katowice\n03|sklepy internetowe Katowice\n04|logo i branding Katowice\n05|kampanie Meta Ads";

  if (empty($opts['seo']) || !is_array($opts['seo'])) { $opts['seo'] = []; }
  $opts['seo']['home_title'] = 'Strony internetowe Katowice, sklepy i branding | Zaprojektowani.com';
  $opts['seo']['home_description'] = 'Strony internetowe Katowice, sklepy WooCommerce, logo i branding, SEO oraz kampanie Meta Ads dla firm ze Śląska i całej Polski.';
  $opts['seo']['focus_keywords'] = 'strony internetowe Katowice, projektowanie stron internetowych Katowice, sklepy internetowe Katowice, logo i branding Katowice, kampanie Meta Ads';

  if (empty($opts['trust_logos']) || !is_array($opts['trust_logos'])) { $opts['trust_logos'] = []; }
  $opts['trust_logos']['heading'] = 'Firmy, dla których projektowaliśmy strony, sklepy i branding';
  $opts['trust_logos']['lead'] = 'Projektujemy strony internetowe Katowice, sklepy WooCommerce, logo, branding i kampanie dla firm z Katowic, Śląska oraz całej Polski — od pierwszego wrażenia po zapytanie.';

  update_option('zp_suite_options', $opts, false);
}

function zp_suite_activate_home_defaults_once(){
  /* Re-activating an existing installation must not reset edited homepage copy. */
  if ((string) get_option('zp_suite_version_done', '') === '') {
    zp_suite_upgrade_108_home_seo_rankmath();
  }
}

register_activation_hook(__FILE__, 'zp_suite_upgrade_140');
register_activation_hook(__FILE__, 'zp_suite_activate_home_defaults_once');
register_activation_hook(__FILE__, 'zp_suite_legal_create_or_update_pages');
register_activation_hook(__FILE__, 'zp_suite_blog_activate');
register_activation_hook(__FILE__, 'zp_suite_campaigns_activate');
add_action('plugins_loaded', function(){
  $previous = (string) get_option('zp_suite_version_done', '');
  if ($previous === ZP_SUITE_VERSION) { return; }

  zp_suite_upgrade_140();

  /* Seed defaults only for a fresh install; version bumps must preserve CMS edits. */
  if ($previous === '') {
    zp_suite_upgrade_108_home_seo_rankmath();
  }

  update_option('zp_suite_version_done', ZP_SUITE_VERSION, false);
}, 5);


/**
 * ZP Suite v1.8.57 — finalny CSS naprawiający mobile logo, CTA i kolor initial paint.
 * Bez JS do hoverów. Drukowane późno w <head>, aby przebić stare legacy patche.
 */
function zp_suite_1852_final_front_css(){
  if (is_admin()) { return; }
  $zp_logo_desktop_h = (int) zp_suite_opt('header.logo_desktop_h', 36);
  $zp_logo_mobile_h = (int) zp_suite_opt('header.logo_mobile_h', 32);
  $zp_logo_mobile_x = (int) zp_suite_opt('header.logo_mobile_x', -8);
  $zp_header_mobile_h = (int) zp_suite_opt('header.mobile_height', 74);
  $zp_mobile_glass_raw = zp_suite_opt('header.mobile_glass_opacity', '0.78');
  $zp_mobile_glass = is_numeric($zp_mobile_glass_raw) ? max(.50, min(.92, (float)$zp_mobile_glass_raw)) : .78;
  $zp_logo_desktop_h = max(18, min(96, $zp_logo_desktop_h));
  $zp_logo_mobile_h = max(22, min(90, $zp_logo_mobile_h));
  $zp_header_mobile_h = max(66, min(96, $zp_header_mobile_h));
  ?>
<style id="zp-suite-1852-final-front-css">
  :root{--zp-final-logo-desktop-h:<?php echo (int)$zp_logo_desktop_h; ?>px;--zp-final-logo-mobile-h:<?php echo (int)$zp_logo_mobile_h; ?>px;--zp-final-logo-mobile-x:<?php echo (int)$zp_logo_mobile_x; ?>px;--zp-final-mobile-header-h:<?php echo (int)$zp_header_mobile_h; ?>px;--zp-final-mobile-glass:<?php echo esc_attr($zp_mobile_glass); ?>;}
  html,body,#page,.site{background-color:#05070b;}
  body.home,body.front-page,body.home #page,body.front-page #page,body.home .site,body.front-page .site,body.home .elementor,body.front-page .elementor{background-color:#05070b!important;}
  body.home .zpNewHero,body.front-page .zpNewHero,body.home .zpNewHero__bg,body.front-page .zpNewHero__bg{background-color:#05070b!important;}

  /* MOBILE LOGO — jedna wysokość i identyczne centrowanie static/sticky dla obu logo */
  @media(max-width:1080px){
    html body .zpNewNav .zpNewNav__mobileBar,
    html body .zpNewNav.is-scrolled .zpNewNav__mobileBar,
    html body .zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled) .zpNewNav__mobileBar{
      height:var(--zp-final-mobile-header-h)!important;min-height:var(--zp-final-mobile-header-h)!important;display:flex!important;align-items:center!important;
      padding-top:0!important;padding-bottom:0!important;overflow:visible!important;
    }
    html body .zpNewNav .zpNewNav__mobileBrand{
      position:relative!important;display:flex!important;align-items:center!important;justify-content:flex-start!important;
      width:220px!important;max-width:58vw!important;height:var(--zp-final-mobile-header-h)!important;min-height:var(--zp-final-mobile-header-h)!important;line-height:0!important;
      padding:0!important;margin:0!important;overflow:visible!important;
    }
    html body .zpNewNav .zpNewNav__mobileLogo,
    html body .zpNewNav.is-scrolled .zpNewNav__mobileLogo,
    html body .zpNewNav.is-mega-open .zpNewNav__mobileLogo{
      position:absolute!important;left:0!important;top:50%!important;bottom:auto!important;
      width:auto!important;height:var(--zp-final-logo-mobile-h)!important;min-height:var(--zp-final-logo-mobile-h)!important;max-height:var(--zp-final-logo-mobile-h)!important;max-width:min(58vw,220px)!important;
      object-fit:contain!important;margin:0!important;padding:0!important;
      transform:translate3d(var(--zp-final-logo-mobile-x),-50%,0)!important;
      transform-origin:left center!important;display:block!important;line-height:0!important;
      transition:opacity .22s ease,filter .22s ease!important;
    }
    html body .zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__mobileLogo--light{opacity:1!important;visibility:visible!important;}
    html body .zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__mobileLogo--dark{opacity:0!important;visibility:hidden!important;}
    html body .zpNewNav.is-scrolled:not(.is-mega-open) .zpNewNav__mobileLogo--light,
    html body .zpNewNav:not(.zpNewNav--heroOverlay):not(.is-mega-open) .zpNewNav__mobileLogo--light{opacity:0!important;visibility:hidden!important;}
    html body .zpNewNav.is-scrolled:not(.is-mega-open) .zpNewNav__mobileLogo--dark,
    html body .zpNewNav:not(.zpNewNav--heroOverlay):not(.is-mega-open) .zpNewNav__mobileLogo--dark{opacity:1!important;visibility:visible!important;}
    html body .zpNewNav.is-scrolled:not(.is-mega-open) .zpNewNav__shell{
      background:rgba(255,255,255,var(--zp-final-mobile-glass))!important;border-bottom:0!important;
      box-shadow:0 16px 42px rgba(5,10,18,.12)!important;backdrop-filter: none;-webkit-backdrop-filter: none;
    }
  }

  /* Ikony kontaktowe przy CTA — bez kółek/kwadratów, tylko luźna ikona + mikroanimacja */
  html body .zpNewNav .zpNewNav__round,
  html body .zpNewNav .zpNewNav__round:hover,
  html body .zpNewNav .zpNewNav__round:focus-visible,
  html body .zpNewNav.is-scrolled .zpNewNav__round,
  html body .zpNewNav.is-mega-open .zpNewNav__round{
    width:34px!important;height:40px!important;min-width:34px!important;min-height:40px!important;padding:0!important;margin:0!important;
    display:inline-flex!important;align-items:center!important;justify-content:center!important;border:0!important;outline:0!important;border-radius:0!important;
    background:transparent!important;box-shadow:none!important;overflow:visible!important;color:rgba(255,255,255,.88)!important;transform:translateZ(0)!important;
  }
  html body .zpNewNav.is-scrolled:not(.is-mega-open) .zpNewNav__round{color:#071426!important;}
  html body .zpNewNav .zpNewNav__round::before,html body .zpNewNav .zpNewNav__round::after{display:none!important;content:none!important;}
  html body .zpNewNav .zpNewNav__round svg{width:21px!important;height:21px!important;display:block!important;color:currentColor!important;stroke:currentColor!important;background:transparent!important;border:0!important;box-shadow:none!important;transition:transform .22s cubic-bezier(.16,1,.3,1),color .22s ease,filter .22s ease!important;}
  html body .zpNewNav .zpNewNav__round:hover,html body .zpNewNav .zpNewNav__round:focus-visible{color:#3b6ea8!important;transform:translateY(-2px)!important;}
  html body .zpNewNav .zpNewNav__round:hover svg,html body .zpNewNav .zpNewNav__round:focus-visible svg{transform:translate(2px,-2px) rotate(5deg)!important;filter:none!important)!important;}

  /* CTA PRIMARY — naprawione na CSS-only: tło gradientowe realnie wjeżdża, tekst jest biały */
  html body .zpNewHero .zpNewHero__btn--primary,
  html body .zpNewHero a.zpNewHero__btn--primary,
  html body .zpNewNav .zpNewNav__cta{
    position:relative!important;isolation:isolate!important;overflow:hidden!important;border-radius:999px!important;
    background:#fff!important;background-image:none!important;border:0!important;box-shadow:none!important;outline:0!important;
    color:#071426!important;transform:translateZ(0)!important;-webkit-mask-image:-webkit-radial-gradient(white,black)!important;
    transition:color .22s cubic-bezier(.16,1,.3,1),transform .22s cubic-bezier(.16,1,.3,1),box-shadow .22s ease!important;
  }
  html body .zpNewHero .zpNewHero__btn--primary::before,
  html body .zpNewHero a.zpNewHero__btn--primary::before,
  html body .zpNewNav .zpNewNav__cta::before{
    content:""!important;display:block!important;position:absolute!important;inset:-2px!important;border-radius:inherit!important;
    z-index:1!important;background:linear-gradient(100deg,#020407 0%,#071426 34%,#102a4f 70%,#1c477a 100%)!important;
    transform:scaleX(0)!important;transform-origin:left center!important;transition:transform .38s cubic-bezier(.16,1,.3,1)!important;pointer-events:none!important;
  }
  html body .zpNewHero .zpNewHero__btn--primary::after,
  html body .zpNewHero a.zpNewHero__btn--primary::after,
  html body .zpNewNav .zpNewNav__cta::after{display:none!important;content:none!important;}
  html body .zpNewHero .zpNewHero__btn--primary span,
  html body .zpNewHero .zpNewHero__btn--primary svg,
  html body .zpNewNav .zpNewNav__cta span,
  html body .zpNewNav .zpNewNav__cta svg{position:relative!important;z-index:3!important;color:inherit!important;stroke:currentColor!important;}
  html body .zpNewHero .zpNewHero__btn--primary:hover,
  html body .zpNewHero .zpNewHero__btn--primary:focus-visible,
  html body .zpNewNav .zpNewNav__cta:hover,
  html body .zpNewNav .zpNewNav__cta:focus-visible{color:#fff!important;transform:translateY(-1px)!important;box-shadow:none!important;}
  html body .zpNewHero .zpNewHero__btn--primary:hover::before,
  html body .zpNewHero .zpNewHero__btn--primary:focus-visible::before,
  html body .zpNewNav .zpNewNav__cta:hover::before,
  html body .zpNewNav .zpNewNav__cta:focus-visible::before{transform:scaleX(1)!important;}

  /* Sticky CTA — bazowo navy gradient, hover odwrócony gradient, bez zmiany rozmiaru */
  html body .zpNewNav.is-scrolled:not(.is-mega-open) .zpNewNav__cta{background:linear-gradient(100deg,#020407 0%,#071426 34%,#102a4f 70%,#1c477a 100%)!important;color:#fff!important;border:0!important;}
  html body .zpNewNav.is-scrolled:not(.is-mega-open) .zpNewNav__cta::before{display:block!important;transform:scaleX(0)!important;background:linear-gradient(100deg,#1c477a 0%,#102a4f 34%,#071426 68%,#020407 100%)!important;}
  html body .zpNewNav.is-scrolled:not(.is-mega-open) .zpNewNav__cta:hover::before{transform:scaleX(1)!important;}

  /* HERO GHOST — transparent glass, hover biały */
  html body .zpNewHero .zpNewHero__btn--ghost,
  html body .zpNewHero a.zpNewHero__btn--ghost{background:rgba(255,255,255,.045)!important;background-image:none!important;color:#fff!important;border:1px solid rgba(255,255,255,.22)!important;box-shadow:none!important;backdrop-filter: none;-webkit-backdrop-filter: none;}
  html body .zpNewHero .zpNewHero__btn--ghost::before,html body .zpNewHero .zpNewHero__btn--ghost::after{display:none!important;content:none!important;}
  html body .zpNewHero .zpNewHero__btn--ghost:hover,html body .zpNewHero .zpNewHero__btn--ghost:focus-visible{background:#fff!important;color:#071426!important;border-color:#fff!important;transform:translateY(-1px)!important;}


  /* =========================================================
     ZP v1.8.57 HARD FINAL — icons / CTA / mega headings
     ========================================================= */

  /* Kontakt icon buttons — kill every possible square/circle layer */
  html body .zpNewNav .zpNewNav__actions .zpNewNav__round,
  html body .zpNewNav .zpNewNav__actions .zpNewNav__round:link,
  html body .zpNewNav .zpNewNav__actions .zpNewNav__round:visited,
  html body .zpNewNav .zpNewNav__actions .zpNewNav__round:hover,
  html body .zpNewNav .zpNewNav__actions .zpNewNav__round:focus,
  html body .zpNewNav .zpNewNav__actions .zpNewNav__round:focus-visible,
  html body .zpNewNav .zpNewNav__actions .zpNewNav__round:active{
    width:34px!important;min-width:34px!important;height:42px!important;min-height:42px!important;
    padding:0!important;margin:0 2px!important;border:0!important;outline:0!important;border-radius:0!important;
    background:transparent!important;background-color:transparent!important;background-image:none!important;
    box-shadow:none!important;text-shadow:none!important;filter: none;backdrop-filter: none;-webkit-backdrop-filter: none;
    display:inline-flex!important;align-items:center!important;justify-content:center!important;overflow:visible!important;
    color:rgba(255,255,255,.9)!important;opacity:1!important;
    transform:translate3d(0,0,0)!important;transition:color .22s ease,transform .22s cubic-bezier(.16,1,.3,1),filter .22s ease!important;
  }
  html body .zpNewNav.is-scrolled:not(.is-mega-open) .zpNewNav__actions .zpNewNav__round{color:#071426!important;}
  html body .zpNewNav .zpNewNav__actions .zpNewNav__round::before,
  html body .zpNewNav .zpNewNav__actions .zpNewNav__round::after,
  html body .zpNewNav .zpNewNav__actions .zpNewNav__round:hover::before,
  html body .zpNewNav .zpNewNav__actions .zpNewNav__round:hover::after{
    display:none!important;content:none!important;background:transparent!important;background-image:none!important;box-shadow:none!important;border:0!important;opacity:0!important;
  }
  html body .zpNewNav .zpNewNav__actions .zpNewNav__round svg,
  html body .zpNewNav .zpNewNav__actions .zpNewNav__round i,
  html body .zpNewNav .zpNewNav__actions .zpNewNav__round i svg{
    width:22px!important;height:22px!important;min-width:22px!important;min-height:22px!important;
    color:currentColor!important;stroke:currentColor!important;background:transparent!important;background-color:transparent!important;background-image:none!important;
    border:0!important;border-radius:0!important;box-shadow:none!important;filter: none;display:block!important;
  }
  html body .zpNewNav .zpNewNav__actions .zpNewNav__round:hover,
  html body .zpNewNav .zpNewNav__actions .zpNewNav__round:focus-visible{
    color:#8fb8ea!important;transform:translate3d(0,-2px,0)!important;filter:drop-shadow(0 8px 14px rgba(28,71,122,.24))!important;
  }

  /* Primary white CTA — no pseudo fill, no white frayed edges, CSS-only animated gradient */
  html body .zpNewHero .zpNewHero__btn--primary,
  html body .zpNewHero a.zpNewHero__btn--primary,
  html body .zpNewNav:not(.is-scrolled) .zpNewNav__cta,
  html body .zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled) .zpNewNav__cta,
  html body .zpNewNav.is-mega-open:not(.is-scrolled) .zpNewNav__cta{
    position:relative!important;isolation:isolate!important;overflow:hidden!important;border-radius:999px!important;
    background-color:#fff!important;
    background-image:linear-gradient(100deg,#020407 0%,#071426 34%,#102a4f 70%,#1c477a 100%)!important;
    background-repeat:no-repeat!important;background-position:left center!important;background-size:0% 100%!important;
    border:0!important;outline:0!important;box-shadow:none!important;
    color:#071426!important;text-shadow:none!important;filter: none;
    transition:background-size .42s cubic-bezier(.16,1,.3,1),color .18s ease,box-shadow .18s ease,transform .18s ease!important;
    -webkit-mask-image:none!important;mask-image:none!important;
  }
  html body .zpNewHero .zpNewHero__btn--primary::before,
  html body .zpNewHero .zpNewHero__btn--primary::after,
  html body .zpNewNav .zpNewNav__cta::before,
  html body .zpNewNav .zpNewNav__cta::after{
    display:none!important;content:none!important;opacity:0!important;visibility:hidden!important;background:none!important;box-shadow:none!important;border:0!important;
  }
  html body .zpNewHero .zpNewHero__btn--primary span,
  html body .zpNewHero .zpNewHero__btn--primary svg,
  html body .zpNewNav .zpNewNav__cta span,
  html body .zpNewNav .zpNewNav__cta svg{
    position:relative!important;z-index:2!important;color:inherit!important;stroke:currentColor!important;fill:none!important;
  }
  html body .zpNewHero .zpNewHero__btn--primary:hover,
  html body .zpNewHero .zpNewHero__btn--primary:focus-visible,
  html body .zpNewNav:not(.is-scrolled) .zpNewNav__cta:hover,
  html body .zpNewNav:not(.is-scrolled) .zpNewNav__cta:focus-visible,
  html body .zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled) .zpNewNav__cta:hover,
  html body .zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled) .zpNewNav__cta:focus-visible{
    background-size:100% 100%!important;color:#fff!important;box-shadow:inset 0 0 0 1px rgba(59,110,168,.60)!important;transform:translateY(-1px)!important;
  }

  /* Sticky quick quote — gradient base + reverse gradient hover */
  html body .zpNewNav.is-scrolled:not(.is-mega-open) .zpNewNav__cta{
    color:#fff!important;border:0!important;outline:0!important;box-shadow:none!important;
    background-color:#071426!important;
    background-image:linear-gradient(100deg,#020407 0%,#071426 34%,#102a4f 70%,#1c477a 100%)!important;
    background-size:100% 100%!important;background-position:left center!important;background-repeat:no-repeat!important;
  }
  html body .zpNewNav.is-scrolled:not(.is-mega-open) .zpNewNav__cta:hover,
  html body .zpNewNav.is-scrolled:not(.is-mega-open) .zpNewNav__cta:focus-visible{
    color:#fff!important;background-image:linear-gradient(100deg,#1c477a 0%,#102a4f 34%,#071426 68%,#020407 100%)!important;box-shadow:inset 0 0 0 1px rgba(59,110,168,.52)!important;transform:translateY(-1px)!important;
  }

  /* Mega menu headings — bigger, underline extends on hover, dark dividers and brand color */
  html body > .zpNewNav__mega.zpNewNav__mega--portal,
  html body:not(.zp-nav-scrolled) > .zpNewNav__mega.zpNewNav__mega--portal{
    background:linear-gradient(135deg,#020407 0%,#050b15 42%,#071426 70%,#0b1830 100%)!important;
  }
  html body > .zpNewNav__mega.zpNewNav__mega--portal .zpNewNav__megaCol{
    position:relative!important;
  }
  html body > .zpNewNav__mega.zpNewNav__mega--portal .zpNewNav__megaCol:not(:first-child)::before{
    content:""!important;position:absolute!important;left:0!important;top:4px!important;bottom:8px!important;width:1px!important;
    background:linear-gradient(180deg,transparent,rgba(143,184,234,.18),rgba(255,255,255,.08),transparent)!important;
  }
  html body > .zpNewNav__mega.zpNewNav__mega--portal .zpNewNav__megaHead{
    font-size:24px!important;line-height:1.02!important;font-weight:720!important;letter-spacing:-.04em!important;
  }
  html body > .zpNewNav__mega.zpNewNav__mega--portal .zpNewNav__megaHead::after{
    width:42px!important;height:2px!important;margin-top:13px!important;border-radius:999px!important;
    background:linear-gradient(90deg,#8fb8ea 0%,#3b6ea8 44%,rgba(255,255,255,.12) 100%)!important;
    transition:width .32s cubic-bezier(.16,1,.3,1),background .32s ease!important;
  }
  html body > .zpNewNav__mega.zpNewNav__mega--portal .zpNewNav__megaCol:hover .zpNewNav__megaHead::after{width:76px!important;}
  html body:not(.zp-nav-scrolled) > .zpNewNav__mega.zpNewNav__mega--portal .zpNewNav__promoPrice,
  html body:not(.zp-nav-scrolled) > .zpNewNav__mega.zpNewNav__mega--portal .zpNewNav__promoTitle,
  html body:not(.zp-nav-scrolled) > .zpNewNav__mega.zpNewNav__mega--portal .zpNewNav__promoText,
  html body:not(.zp-nav-scrolled) > .zpNewNav__mega.zpNewNav__mega--portal .zpNewNav__megaIco,
  html body:not(.zp-nav-scrolled) > .zpNewNav__mega.zpNewNav__mega--portal .zpNewNav__megaLink:hover .zpNewNav__megaIco{
    color:#fff!important;stroke:#fff!important;
  }
  html body.zp-nav-scrolled > .zpNewNav__mega.zpNewNav__mega--portal .zpNewNav__promoCard,
  html body.zp-nav-scrolled > .zpNewNav__mega.zpNewNav__mega--portal .zpNewNav__promoCard *{
    color:#fff!important;
  }
</style>
  <?php
}
add_action('wp_head','zp_suite_1852_final_front_css',999);

function zp_suite_1852_initial_dark_bg(){
  if (is_admin()) { return; }
  ?>
<style id="zp-suite-1852-initial-bg">html,body{background:#04080f;}body:not(.home):not(.front-page){background:#fff;}</style>
  <?php
}
add_action('wp_head','zp_suite_1852_initial_dark_bg',0);


/**
 * ZP Suite v1.8.75 — single source of truth for header logo sizing + mobile glass.
 * This is intentionally printed very late in <head> so legacy CSS cannot resize the logo after load.
 */
function zp_suite_1875_header_logo_and_mobile_glass_css(){
  if (is_admin()) { return; }
  $desk = max(18, min(96, (int) zp_suite_opt('header.logo_desktop_h', 36)));
  $mob  = max(22, min(90, (int) zp_suite_opt('header.logo_mobile_h', 32)));
  $mx   = (int) zp_suite_opt('header.logo_mobile_x', -8);
  $mh   = max(62, min(120, (int) zp_suite_opt('header.mobile_height', 78)));
  $dh   = max(62, min(120, (int) zp_suite_opt('header.desktop_height', 88)));
  $glass = zp_suite_opt('header.mobile_glass_opacity', '0.72');
  $glass = is_numeric($glass) ? max(.48, min(.92, (float)$glass)) : .72;
  ?>
<style id="zp-suite-1875-logo-mobile-glass-final">
  html,body,#page,.site{background:#05070b!important;}
  body:not(.home):not(.front-page){background:#fff!important;}
  body.home,body.front-page,body.home #page,body.front-page #page,body.home .site,body.front-page .site{background:#05070b!important;}
  #zpNewNav{--zp1875-logo-desk:<?php echo $desk; ?>px;--zp1875-logo-mob:<?php echo $mob; ?>px;--zp1875-logo-mx:<?php echo $mx; ?>px;--zp1875-nav-desk:<?php echo $dh; ?>px;--zp1875-nav-mob:<?php echo $mh; ?>px;--zp1875-mobile-glass:<?php echo $glass; ?>;}
  #zpNewNav,
  #zpNewNav .zpNewNav__shell,
  #zpNewNav .zpNewNav__inner{--zpNavH:var(--zp1875-nav-desk)!important;--zp-header-h:var(--zp1875-nav-desk)!important;--zp-header-logo-h:var(--zp1875-logo-desk)!important;}
  #zpNewNav .zpNewNav__shell,
  #zpNewNav.is-scrolled .zpNewNav__shell,
  #zpNewNav.is-mega-open .zpNewNav__shell{min-height:var(--zp1875-nav-desk)!important;height:var(--zp1875-nav-desk)!important;padding-top:0!important;padding-bottom:0!important;}
  #zpNewNav .zpNewNav__inner,
  #zpNewNav.is-scrolled .zpNewNav__inner,
  #zpNewNav.is-mega-open .zpNewNav__inner{min-height:var(--zp1875-nav-desk)!important;height:var(--zp1875-nav-desk)!important;align-items:center!important;}
  #zpNewNav .zpNewNav__brand,
  #zpNewNav.is-scrolled .zpNewNav__brand,
  #zpNewNav.is-mega-open .zpNewNav__brand{height:var(--zp1875-nav-desk)!important;min-height:var(--zp1875-nav-desk)!important;display:flex!important;align-items:center!important;line-height:0!important;}
  #zpNewNav .zpNewNav__logo,
  #zpNewNav.is-scrolled .zpNewNav__logo,
  #zpNewNav.is-mega-open .zpNewNav__logo,
  #zpNewNav.zpNewNav--heroOverlay .zpNewNav__logo{height:var(--zp1875-logo-desk)!important;min-height:var(--zp1875-logo-desk)!important;max-height:var(--zp1875-logo-desk)!important;width:auto!important;max-width:260px!important;object-fit:contain!important;margin:0!important;padding:0!important;transform:none!important;scale:1!important;transition:opacity .22s ease,filter .22s ease!important;}
  #zpNewNav .zpNewNav__brand:hover .zpNewNav__logo{transform:none!important;scale:1!important;}
  @media(max-width:1080px){
    #zpNewNav,#zpNewNav .zpNewNav__shell,#zpNewNav .zpNewNav__mobileBar{--zpNavH:var(--zp1875-nav-mob)!important;}
    #zpNewNav .zpNewNav__shell,
    #zpNewNav.is-scrolled .zpNewNav__shell,
    #zpNewNav.is-mega-open .zpNewNav__shell{min-height:var(--zp1875-nav-mob)!important;height:var(--zp1875-nav-mob)!important;padding:0!important;display:flex!important;align-items:center!important;}
    #zpNewNav.is-scrolled:not(.is-mega-open) .zpNewNav__shell{background:rgba(255,255,255,var(--zp1875-mobile-glass))!important;border-bottom:0!important;box-shadow:none!important;backdrop-filter: none;-webkit-backdrop-filter: none;}
    #zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__shell{background:linear-gradient(180deg,rgba(2,4,7,.36),rgba(2,4,7,.08) 72%,rgba(2,4,7,0))!important;box-shadow:none!important;backdrop-filter: none;-webkit-backdrop-filter: none;}
    #zpNewNav .zpNewNav__mobileBar,
    #zpNewNav.is-scrolled .zpNewNav__mobileBar{height:var(--zp1875-nav-mob)!important;min-height:var(--zp1875-nav-mob)!important;display:flex!important;align-items:center!important;padding-top:0!important;padding-bottom:0!important;}
    #zpNewNav .zpNewNav__mobileBrand,
    #zpNewNav.is-scrolled .zpNewNav__mobileBrand,
    #zpNewNav.is-mega-open .zpNewNav__mobileBrand{position:relative!important;width:220px!important;max-width:58vw!important;height:var(--zp1875-nav-mob)!important;min-height:var(--zp1875-nav-mob)!important;display:flex!important;align-items:center!important;line-height:0!important;overflow:visible!important;transform:none!important;padding:0!important;margin:0!important;}
    #zpNewNav .zpNewNav__mobileLogo,
    #zpNewNav.is-scrolled .zpNewNav__mobileLogo,
    #zpNewNav.is-mega-open .zpNewNav__mobileLogo,
    #zpNewNav.zpNewNav--heroOverlay .zpNewNav__mobileLogo{position:absolute!important;left:0!important;top:50%!important;bottom:auto!important;width:auto!important;height:var(--zp1875-logo-mob)!important;min-height:var(--zp1875-logo-mob)!important;max-height:var(--zp1875-logo-mob)!important;max-width:min(58vw,210px)!important;object-fit:contain!important;margin:0!important;padding:0!important;transform:translate3d(var(--zp1875-logo-mx),-50%,0)!important;transform-origin:left center!important;scale:1!important;transition:opacity .22s ease,filter .22s ease!important;}
    #zpNewNav .zpNewNav__mobileBrand:hover .zpNewNav__mobileLogo{transform:translate3d(var(--zp1875-logo-mx),-50%,0)!important;scale:1!important;}
    #zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__mobileLogo--light,#zpNewNav.is-mega-open .zpNewNav__mobileLogo--light{opacity:1!important;visibility:visible!important;}
    #zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__mobileLogo--dark,#zpNewNav.is-mega-open .zpNewNav__mobileLogo--dark{opacity:0!important;visibility:hidden!important;}
    #zpNewNav.is-scrolled:not(.is-mega-open) .zpNewNav__mobileLogo--light,#zpNewNav:not(.zpNewNav--heroOverlay):not(.is-mega-open) .zpNewNav__mobileLogo--light{opacity:0!important;visibility:hidden!important;}
    #zpNewNav.is-scrolled:not(.is-mega-open) .zpNewNav__mobileLogo--dark,#zpNewNav:not(.zpNewNav--heroOverlay):not(.is-mega-open) .zpNewNav__mobileLogo--dark{opacity:1!important;visibility:visible!important;}
  }
</style>
  <?php
}
add_action('wp_head','zp_suite_1875_header_logo_and_mobile_glass_css',100000);


/**
 * ZP Suite v1.8.76 — single hard header logo/mobile glass override.
 * This is deliberately printed last in wp_head and uses ID selectors so CMS values win over legacy fixes.
 */
function zp_suite_1876_header_logo_hard_css(){
  if (is_admin()) { return; }
  $d = max(18, min(96, (int) zp_suite_opt('header.logo_desktop_h', 36)));
  $m = max(22, min(90, (int) zp_suite_opt('header.logo_mobile_h', 32)));
  $x = (int) zp_suite_opt('header.logo_mobile_x', -8);
  $mh = max(66, min(96, (int) zp_suite_opt('header.mobile_height', 74)));
  $glass_raw = zp_suite_opt('header.mobile_glass_opacity', '0.78');
  $glass = is_numeric($glass_raw) ? max(.50, min(.92, (float)$glass_raw)) : .78;
?>
<style id="zp-suite-1876-header-logo-hard-css">
#zpNewNav{--zp-1876-logo-d:<?php echo (int)$d; ?>px!important;--zp-1876-logo-m:<?php echo (int)$m; ?>px!important;--zp-1876-logo-x:<?php echo (int)$x; ?>px!important;--zp-1876-mobile-h:<?php echo (int)$mh; ?>px!important;--zp-1876-glass:<?php echo esc_attr($glass); ?>!important;}
html body #zpNewNav .zpNewNav__brand,html body #zpNewNav.is-scrolled .zpNewNav__brand,html body #zpNewNav.is-mega-open .zpNewNav__brand{height:var(--zpNavH,88px)!important;min-height:var(--zpNavH,88px)!important;display:flex!important;align-items:center!important;line-height:0!important;overflow:visible!important;}
html body #zpNewNav .zpNewNav__logo,html body #zpNewNav.is-scrolled .zpNewNav__logo,html body #zpNewNav.is-mega-open .zpNewNav__logo,html body #zpNewNav.zpNewNav--heroOverlay .zpNewNav__logo{height:var(--zp-1876-logo-d)!important;min-height:var(--zp-1876-logo-d)!important;max-height:var(--zp-1876-logo-d)!important;width:auto!important;max-width:260px!important;object-fit:contain!important;transform:none!important;scale:1!important;transition:opacity .22s ease,filter .22s ease!important;}
@media(max-width:1080px){
  html body #zpNewNav .zpNewNav__shell{min-height:var(--zp-1876-mobile-h)!important;}
  html body #zpNewNav.is-scrolled:not(.is-mega-open) .zpNewNav__shell{background:rgba(255,255,255,var(--zp-1876-glass))!important;border-bottom:0!important;box-shadow:none!important;backdrop-filter: none;-webkit-backdrop-filter: none;}
  html body #zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__shell{background:linear-gradient(180deg,rgba(2,4,7,.34),rgba(2,4,7,.12) 68%,rgba(2,4,7,0))!important;box-shadow:none!important;backdrop-filter: none;-webkit-backdrop-filter: none;}
  html body #zpNewNav .zpNewNav__mobileBar,html body #zpNewNav.is-scrolled .zpNewNav__mobileBar,html body #zpNewNav.is-mega-open .zpNewNav__mobileBar{height:var(--zp-1876-mobile-h)!important;min-height:var(--zp-1876-mobile-h)!important;display:flex!important;align-items:center!important;padding-top:0!important;padding-bottom:0!important;overflow:visible!important;}
  html body #zpNewNav .zpNewNav__mobileBrand,html body #zpNewNav.is-scrolled .zpNewNav__mobileBrand,html body #zpNewNav.is-mega-open .zpNewNav__mobileBrand{position:relative!important;display:flex!important;align-items:center!important;justify-content:flex-start!important;width:220px!important;max-width:58vw!important;height:var(--zp-1876-mobile-h)!important;min-height:var(--zp-1876-mobile-h)!important;line-height:0!important;padding:0!important;margin:0!important;overflow:visible!important;transform:none!important;}
  html body #zpNewNav .zpNewNav__mobileLogo,html body #zpNewNav.is-scrolled .zpNewNav__mobileLogo,html body #zpNewNav.is-mega-open .zpNewNav__mobileLogo,html body #zpNewNav.zpNewNav--heroOverlay .zpNewNav__mobileLogo{position:absolute!important;left:0!important;top:50%!important;bottom:auto!important;width:auto!important;height:var(--zp-1876-logo-m)!important;min-height:var(--zp-1876-logo-m)!important;max-height:var(--zp-1876-logo-m)!important;max-width:min(58vw,220px)!important;object-fit:contain!important;margin:0!important;padding:0!important;transform:translate3d(var(--zp-1876-logo-x),-50%,0)!important;transform-origin:left center!important;display:block!important;line-height:0!important;transition:opacity .22s ease,filter .22s ease!important;scale:1!important;}
  html body #zpNewNav .zpNewNav__mobileBrand:hover .zpNewNav__mobileLogo,html body #zpNewNav .zpNewNav__mobileBrand:focus-visible .zpNewNav__mobileLogo{transform:translate3d(var(--zp-1876-logo-x),-50%,0)!important;scale:1!important;}
  html body #zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__mobileLogo--light{opacity:1!important;visibility:visible!important;}
  html body #zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__mobileLogo--dark{opacity:0!important;visibility:hidden!important;}
  html body #zpNewNav.is-scrolled:not(.is-mega-open) .zpNewNav__mobileLogo--light,html body #zpNewNav:not(.zpNewNav--heroOverlay):not(.is-mega-open) .zpNewNav__mobileLogo--light{opacity:0!important;visibility:hidden!important;}
  html body #zpNewNav.is-scrolled:not(.is-mega-open) .zpNewNav__mobileLogo--dark,html body #zpNewNav:not(.zpNewNav--heroOverlay):not(.is-mega-open) .zpNewNav__mobileLogo--dark{opacity:1!important;visibility:visible!important;}
}
</style>
<?php
}
add_action('wp_head','zp_suite_1876_header_logo_hard_css',PHP_INT_MAX);


/**
 * ZP Suite v2.1.87 — single post header parity.
 * Wpisy blogowe dostają ten sam tryb headera co hero: static dark/transparent,
 * po scrollu biały glass. Naprawia też logo mobile: jasne na static dark,
 * ciemne po scrollu.
 */
function zp_suite_2121_single_post_header_css(){
  if (is_admin()) { return; }
  // v2.2.492: every selector below is scoped to body.single-post — print only there.
  if (!is_singular('post')) { return; }
  ?>
<style id="zp-suite-2121-single-post-header-css">html body.single-post header#zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) > .zpNewNav__shell,html body.single-post header#zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__shell{background:transparent!important;background-color:transparent!important;background-image:none!important;border-bottom:0!important;box-shadow:none!important;backdrop-filter: none;-webkit-backdrop-filter: none}html body.single-post header#zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__link,html body.single-post header#zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__round,html body.single-post header#zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__mobileSearch,html body.single-post header#zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__burger{color:#fff!important}html body.single-post header#zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__logo--light{display:block!important;opacity:1!important;visibility:visible!important}html body.single-post header#zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__logo--dark{display:none!important;opacity:0!important;visibility:hidden!important}html body.single-post header#zpNewNav.is-scrolled:not(.is-mega-open) > .zpNewNav__shell,html body.single-post header#zpNewNav.is-scrolled:not(.is-mega-open) .zpNewNav__shell,html body.single-post.zp-nav-scrolled header#zpNewNav:not(.is-mega-open) > .zpNewNav__shell,html body.single-post.zp-nav-scrolled header#zpNewNav:not(.is-mega-open) .zpNewNav__shell{background:rgba(255,255,255,.76)!important;background-color:rgba(255,255,255,.76)!important;background-image:linear-gradient(180deg,rgba(255,255,255,.86),rgba(255,255,255,.64))!important;border-bottom:0!important;box-shadow:0 14px 42px rgba(5,10,18,.10)!important;backdrop-filter: none;-webkit-backdrop-filter: none}html body.single-post header#zpNewNav.is-scrolled:not(.is-mega-open) .zpNewNav__link,html body.single-post header#zpNewNav.is-scrolled:not(.is-mega-open) .zpNewNav__round,html body.single-post header#zpNewNav.is-scrolled:not(.is-mega-open) .zpNewNav__mobileSearch,html body.single-post header#zpNewNav.is-scrolled:not(.is-mega-open) .zpNewNav__burger{color:#071426!important}html body.single-post header#zpNewNav.is-scrolled:not(.is-mega-open) .zpNewNav__logo--light{display:none!important;opacity:0!important;visibility:hidden!important}html body.single-post header#zpNewNav.is-scrolled:not(.is-mega-open) .zpNewNav__logo--dark{display:block!important;opacity:1!important;visibility:visible!important}@media(max-width:1080px){html body.single-post header#zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__mobileLogo--light{opacity:1!important;visibility:visible!important;filter: none}html body.single-post header#zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__mobileLogo--dark{opacity:0!important;visibility:hidden!important;filter: none}html body.single-post header#zpNewNav.is-scrolled:not(.is-mega-open) .zpNewNav__mobileLogo--light,html body.single-post.zp-nav-scrolled header#zpNewNav:not(.is-mega-open) .zpNewNav__mobileLogo--light{opacity:0!important;visibility:hidden!important;filter: none}html body.single-post header#zpNewNav.is-scrolled:not(.is-mega-open) .zpNewNav__mobileLogo--dark,html body.single-post.zp-nav-scrolled header#zpNewNav:not(.is-mega-open) .zpNewNav__mobileLogo--dark{opacity:1!important;visibility:visible!important;filter: none}}</style>
  <?php
}
add_action('wp_head','zp_suite_2121_single_post_header_css',PHP_INT_MAX);


/** ZP Suite v2.1.87 — final transparent hero nav separator for all hero overlay pages. */
function zp_suite_2123_hero_nav_separator_css(){
  if (is_admin()) { return; }
  ?>
<style id="zp-suite-2123-hero-nav-separator-css">html body header#zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) > .zpNewNav__shell,html body header#zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__shell{background:transparent!important;background-color:transparent!important;background-image:none!important;border-bottom:0!important;box-shadow:none!important;backdrop-filter: none;-webkit-backdrop-filter: none}@media(max-width:1080px){html body header#zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__mobileBar{background:transparent!important;background-color:transparent!important;background-image:none!important;border-bottom:0!important;box-shadow:none!important;backdrop-filter: none;-webkit-backdrop-filter: none}}</style>
  <?php
}
// disabled in v2.1.87: header divider removed completely.


/**
 * ZP Suite v2.1.87 — FINAL header state fix.
 * Static hero: transparent, part of hero, light logo.
 * Scrolled: white glass, dark logo.
 * Divider: one very subtle centered line only, no full bleed / no duplicate mobile line.
 */
function zp_suite_2129_final_header_state_css(){
  if (is_admin()) { return; }
  ?>
<style id="zp-suite-2129-final-header-state-css">html body header#zpNewNav{--zp-final-header-line-w:min(1280px,calc(100vw - 72px));--zp-final-header-line-light:rgba(255,255,255,.13);--zp-final-header-line-dark:rgba(7,20,38,.095)}html body header#zpNewNav .zpNewNav__shell,html body header#zpNewNav .zpNewNav__mobileBar{border-bottom:0!important}html body header#zpNewNav .zpNewNav__mobileBar::before,html body header#zpNewNav .zpNewNav__mobileBar::after,html body header#zpNewNav .zpNewNav__inner::before,html body header#zpNewNav .zpNewNav__inner::after{display:none!important;content:none!important;opacity:0!important}html body header#zpNewNav .zpNewNav__shell{position:relative!important}html body header#zpNewNav .zpNewNav__shell::after{content:""!important;display:block!important;position:absolute!important;left:50%!important;right:auto!important;bottom:0!important;width:var(--zp-final-header-line-w)!important;max-width:calc(100vw - 72px)!important;height:1px!important;transform:translateX(-50%)!important;pointer-events:none!important;opacity:1!important;background:linear-gradient(90deg,transparent 0%,rgba(255,255,255,.035) 11%,var(--zp-final-header-line-light) 50%,rgba(255,255,255,.035) 89%,transparent 100%)!important;box-shadow:none!important}html body header#zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open),html body:not(.zp-nav-scrolled) header#zpNewNav.zpNewNav--heroOverlay:not(.is-mega-open){background:transparent!important;background-color:transparent!important;background-image:none!important;box-shadow:none!important;backdrop-filter: none;-webkit-backdrop-filter: none}html body header#zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) > .zpNewNav__shell,html body header#zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__shell,html body:not(.zp-nav-scrolled) header#zpNewNav.zpNewNav--heroOverlay:not(.is-mega-open) > .zpNewNav__shell,html body:not(.zp-nav-scrolled) header#zpNewNav.zpNewNav--heroOverlay:not(.is-mega-open) .zpNewNav__shell,html body header#zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__mobileBar,html body:not(.zp-nav-scrolled) header#zpNewNav.zpNewNav--heroOverlay:not(.is-mega-open) .zpNewNav__mobileBar,html body header#zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__mobileBrand,html body:not(.zp-nav-scrolled) header#zpNewNav.zpNewNav--heroOverlay:not(.is-mega-open) .zpNewNav__mobileBrand{background:transparent!important;background-color:transparent!important;background-image:none!important;border-bottom:0!important;box-shadow:none!important;backdrop-filter: none;-webkit-backdrop-filter: none}html body header#zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__link,html body header#zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__round,html body header#zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__mobileSearch,html body header#zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__burger{color:#fff!important}html body header#zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__logo--light{display:block!important;opacity:1!important;visibility:visible!important;filter: none}html body header#zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__logo--dark{display:none!important;opacity:0!important;visibility:hidden!important;filter: none}html body header#zpNewNav.is-scrolled:not(.is-mega-open),html body.zp-nav-scrolled header#zpNewNav:not(.is-mega-open){background:transparent!important;background-color:transparent!important;background-image:none!important;box-shadow:none!important}html body header#zpNewNav.is-scrolled:not(.is-mega-open) > .zpNewNav__shell,html body header#zpNewNav.is-scrolled:not(.is-mega-open) .zpNewNav__shell,html body.zp-nav-scrolled header#zpNewNav:not(.is-mega-open) > .zpNewNav__shell,html body.zp-nav-scrolled header#zpNewNav:not(.is-mega-open) .zpNewNav__shell{background:rgba(255,255,255,.78)!important;background-color:rgba(255,255,255,.78)!important;background-image:linear-gradient(180deg,rgba(255,255,255,.86),rgba(255,255,255,.70))!important;border-bottom:0!important;box-shadow:0 14px 42px rgba(5,10,18,.08)!important;backdrop-filter: none;-webkit-backdrop-filter: none}html body header#zpNewNav.is-scrolled:not(.is-mega-open) .zpNewNav__shell::after,html body.zp-nav-scrolled header#zpNewNav:not(.is-mega-open) .zpNewNav__shell::after{background:linear-gradient(90deg,transparent 0%,rgba(7,20,38,.025) 11%,var(--zp-final-header-line-dark) 50%,rgba(7,20,38,.025) 89%,transparent 100%)!important}html body header#zpNewNav.is-scrolled:not(.is-mega-open) .zpNewNav__mobileBar,html body.zp-nav-scrolled header#zpNewNav:not(.is-mega-open) .zpNewNav__mobileBar,html body header#zpNewNav.is-scrolled:not(.is-mega-open) .zpNewNav__mobileBrand,html body.zp-nav-scrolled header#zpNewNav:not(.is-mega-open) .zpNewNav__mobileBrand{background:transparent!important;background-color:transparent!important;background-image:none!important;border-bottom:0!important;box-shadow:none!important;backdrop-filter: none;-webkit-backdrop-filter: none}html body header#zpNewNav.is-scrolled:not(.is-mega-open) .zpNewNav__link,html body header#zpNewNav.is-scrolled:not(.is-mega-open) .zpNewNav__round,html body header#zpNewNav.is-scrolled:not(.is-mega-open) .zpNewNav__mobileSearch,html body header#zpNewNav.is-scrolled:not(.is-mega-open) .zpNewNav__burger{color:#071426!important}html body header#zpNewNav.is-scrolled:not(.is-mega-open) .zpNewNav__logo--light,html body.zp-nav-scrolled header#zpNewNav:not(.is-mega-open) .zpNewNav__logo--light{display:none!important;opacity:0!important;visibility:hidden!important;filter: none}html body header#zpNewNav.is-scrolled:not(.is-mega-open) .zpNewNav__logo--dark,html body.zp-nav-scrolled header#zpNewNav:not(.is-mega-open) .zpNewNav__logo--dark{display:block!important;opacity:1!important;visibility:visible!important;filter: none}@media(max-width:1080px){html body header#zpNewNav{--zp-final-header-line-w:calc(100vw - 36px)}html body header#zpNewNav .zpNewNav__shell::after{max-width:calc(100vw - 36px)!important}html body header#zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__mobileLogo--light{opacity:1!important;visibility:visible!important;filter: none}html body header#zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__mobileLogo--dark{opacity:0!important;visibility:hidden!important;filter: none}html body header#zpNewNav.is-scrolled:not(.is-mega-open) .zpNewNav__mobileLogo--light,html body.zp-nav-scrolled header#zpNewNav:not(.is-mega-open) .zpNewNav__mobileLogo--light{opacity:0!important;visibility:hidden!important;filter: none}html body header#zpNewNav.is-scrolled:not(.is-mega-open) .zpNewNav__mobileLogo--dark,html body.zp-nav-scrolled header#zpNewNav:not(.is-mega-open) .zpNewNav__mobileLogo--dark{opacity:1!important;visibility:visible!important;filter: none}}</style>
  <?php
}
// disabled in v2.1.87: header divider removed completely.


/**
 * ZP Suite v2.1.87 — ONE HEADER LINE FINAL.
 * Jedna linia pod headerem: desktop max 1650px, mobile full bleed i subtelniej.
 * Bez zdublowanych ::after na mobileBar/inner, bez flashowania białej linii.
 */
function zp_suite_2130_one_header_line_css(){
  if (is_admin()) { return; }
  ?>
<style id="zp-suite-2130-one-header-line-final">html body header#zpNewNav{--zpHeaderLineW:min(1650px,calc(100vw - 72px));--zpHeaderLineAlpha:.44;--zpHeaderLineStatic:linear-gradient(90deg,transparent 0%,rgba(170,215,255,0) 7%,rgba(170,215,255,.13) 18%,rgba(220,240,255,.38) 50%,rgba(170,215,255,.13) 82%,rgba(170,215,255,0) 93%,transparent 100%);--zpHeaderLineScrolled:linear-gradient(90deg,transparent 0%,rgba(7,20,38,0) 7%,rgba(7,20,38,.035) 18%,rgba(7,20,38,.105) 50%,rgba(7,20,38,.035) 82%,rgba(7,20,38,0) 93%,transparent 100%)}html body header#zpNewNav .zpNewNav__shell,html body header#zpNewNav .zpNewNav__mobileBar,html body header#zpNewNav .zpNewNav__inner{border-bottom:0!important;box-shadow:none!important}html body header#zpNewNav .zpNewNav__mobileBar::before,html body header#zpNewNav .zpNewNav__mobileBar::after,html body header#zpNewNav .zpNewNav__inner::before,html body header#zpNewNav .zpNewNav__inner::after{display:none!important;content:none!important;opacity:0!important;background:none!important;box-shadow:none!important}html body header#zpNewNav .zpNewNav__shell{position:relative!important;overflow:visible!important}html body header#zpNewNav .zpNewNav__shell::before{display:none!important;content:none!important}html body header#zpNewNav .zpNewNav__shell::after{content:""!important;display:block!important;position:absolute!important;left:50%!important;right:auto!important;bottom:0!important;width:var(--zpHeaderLineW)!important;max-width:calc(100vw - 72px)!important;height:1px!important;transform:translate3d(-50%,0,0) scaleX(0)!important;transform-origin:center!important;pointer-events:none!important;opacity:0!important;background:var(--zpHeaderLineStatic)!important;box-shadow:0 0 12px rgba(170,215,255,.10)!important;animation:zpHeaderOneLineLoad .82s cubic-bezier(.16,1,.3,1) .34s forwards!important;transition:background .28s ease,opacity .28s ease,box-shadow .28s ease!important}html body header#zpNewNav.is-scrolled:not(.is-mega-open) .zpNewNav__shell::after,html body.zp-nav-scrolled header#zpNewNav:not(.is-mega-open) .zpNewNav__shell::after{background:var(--zpHeaderLineScrolled)!important;box-shadow:none!important;opacity:.50!important}@keyframes zpHeaderOneLineLoad{0%{opacity:0;transform:translate3d(-50%,0,0) scaleX(.08);filter:none}56%{opacity:.78;transform:translate3d(-50%,0,0) scaleX(1.015);filter:none}100%{opacity:var(--zpHeaderLineAlpha);transform:translate3d(-50%,0,0) scaleX(1);filter:none}}@media(max-width:1080px){html body header#zpNewNav{--zpHeaderLineW:100vw;--zpHeaderLineAlpha:.28;--zpHeaderLineStatic:linear-gradient(90deg,transparent 0%,rgba(170,215,255,.06) 15%,rgba(220,240,255,.22) 50%,rgba(170,215,255,.06) 85%,transparent 100%);--zpHeaderLineScrolled:linear-gradient(90deg,transparent 0%,rgba(7,20,38,.025) 14%,rgba(7,20,38,.075) 50%,rgba(7,20,38,.025) 86%,transparent 100%)}html body header#zpNewNav .zpNewNav__shell::after{width:100vw!important;max-width:100vw!important}}@media(prefers-reduced-motion:reduce){html body header#zpNewNav .zpNewNav__shell::after{animation:none!important;opacity:var(--zpHeaderLineAlpha)!important;transform:translate3d(-50%,0,0) scaleX(1)!important}}</style>
  <?php
}
// disabled in v2.1.87: header divider removed completely.


/**
 * ZP Suite v2.1.87 — HARD REMOVE header divider + mobile clearance.
 * Removes every header separator/border/pseudo-line created by legacy CSS, inline CSS and DB options.
 */
function zp_suite_2131_no_header_divider_css(){
  if (is_admin()) { return; }
  ?>
<style id="zp-suite-2131-no-header-divider-css">html body header#zpNewNav,html body header#zpNewNav .zpNewNav__shell,html body header#zpNewNav .zpNewNav__inner,html body header#zpNewNav .zpNewNav__mobileBar,html body header#zpNewNav .zpNewNav__mobileBrand{border-top:0!important;border-bottom:0!important}html body header#zpNewNav::before,html body header#zpNewNav::after,html body header#zpNewNav .zpNewNav__shell::before,html body header#zpNewNav .zpNewNav__shell::after,html body header#zpNewNav .zpNewNav__inner::before,html body header#zpNewNav .zpNewNav__inner::after,html body header#zpNewNav .zpNewNav__mobileBar::before,html body header#zpNewNav .zpNewNav__mobileBar::after,html body header#zpNewNav .zpNewNav__mobileBrand::before,html body header#zpNewNav .zpNewNav__mobileBrand::after{content:none!important;display:none!important;width:0!important;height:0!important;max-width:0!important;min-width:0!important;opacity:0!important;visibility:hidden!important;background:none!important;background-image:none!important;border:0!important;box-shadow:none!important;filter: none;animation:none!important;transform:none!important}@media(max-width:1080px){html body{--zp-mobile-after-header-space:45px}html body #zpKnowledgePro{margin-top:45px!important}html body .zpNewNav + main,html body .zpNewNav + #content,html body main.site-main,html body #primary,html body #content.site-content{padding-top:45px!important}}</style>
  <?php
}
// disabled in v2.1.87: replaced by one controlled animated line in header.php


/**
 * ZP Suite v2.1.87 — EARLY no-divider critical CSS.
 * This removes the white flash from critical header CSS before full CSS loads.
 */
function zp_suite_2132_early_no_divider_css(){
  if (is_admin()) { return; }
  ?>
<style id="zp-suite-2132-early-no-divider-css">html body header#zpNewNav,html body header#zpNewNav .zpNewNav__shell,html body header#zpNewNav .zpNewNav__inner,html body header#zpNewNav .zpNewNav__mobileBar,html body header#zpNewNav .zpNewNav__mobileBrand{border-top:0!important;border-bottom:0!important}html body header#zpNewNav::before,html body header#zpNewNav::after,html body header#zpNewNav .zpNewNav__shell::before,html body header#zpNewNav .zpNewNav__shell::after,html body header#zpNewNav .zpNewNav__inner::before,html body header#zpNewNav .zpNewNav__inner::after,html body header#zpNewNav .zpNewNav__mobileBar::before,html body header#zpNewNav .zpNewNav__mobileBar::after{content:none!important;display:none!important;opacity:0!important;border:0!important;background:none!important;box-shadow:none!important}@media(max-width:1080px){html body #zpKnowledgePro{margin-top:45px!important}}</style>
  <?php
}
// disabled in v2.1.87: replaced by one controlled animated line in header.php



/* ZP Suite v2.1.87: electric line moved into header template as a real child of #zpNewNav. */




/* v2.1.87: previous footer electric-line experiment removed; line is rendered directly inside header shell. */


/**
 * ZP Suite v2.1.87 — header line fallback + mobile performance/hero anti-flash.
 * - Keeps header glued to dark hero (no white strip above header).
 * - Adds 45px breathing room inside hero only.
 * - Prevents mobile hero mockup flash before CSS/JS is ready.
 * - Stops mobile video sources from blocking the page load bar.
 */
add_action('wp_head', function(){
  if (is_admin()) { return; }
  ?>
<style id="zp-suite-2147-header-line-and-mobile-fix">html body header#zpNewNav,html body header#zpNewNav .zpNewNav__shell,html body header#zpNewNav .zpNewNav__inner,html body header#zpNewNav .zpNewNav__mobileBar,html body header#zpNewNav .zpNewNav__mobileBrand{border-top:0!important;border-bottom:0!important}html body header#zpNewNav::before,html body header#zpNewNav::after,html body header#zpNewNav .zpNewNav__shell::before,html body header#zpNewNav .zpNewNav__shell::after,html body header#zpNewNav .zpNewNav__inner::before,html body header#zpNewNav .zpNewNav__inner::after,html body header#zpNewNav .zpNewNav__mobileBar::before,html body header#zpNewNav .zpNewNav__mobileBar::after,html body header#zpNewNav .zpNewNav__mobileBrand::before,html body header#zpNewNav .zpNewNav__mobileBrand::after{content:none!important;display:none!important;opacity:0!important;visibility:hidden!important;background:none!important;box-shadow:none!important;border:0!important}html body header#zpNewNav .zpNewNav__shell{position:relative!important;overflow:visible!important}html body header#zpNewNav #zpHeaderLine2147{position:absolute!important;left:50%!important;bottom:0!important;width:min(1850px,calc(100vw - 24px))!important;height:9px!important;display:block!important;visibility:visible!important;opacity:1!important;overflow:visible!important;pointer-events:none!important;z-index:2147483000!important;transform:translate3d(-50%,0,0)!important;contain:layout paint!important}html body header#zpNewNav #zpHeaderLine2147 .zpHeaderLine2147__base,html body header#zpNewNav #zpHeaderLine2147 .zpHeaderLine2147__draw,html body header#zpNewNav #zpHeaderLine2147 .zpHeaderLine2147__blink{position:absolute!important;left:0!important;right:0!important;top:4px!important;height:1px!important;border-radius:999px!important;display:block!important;visibility:visible!important;pointer-events:none!important}html body header#zpNewNav #zpHeaderLine2147 .zpHeaderLine2147__base{opacity:.16!important;background:linear-gradient(90deg,transparent 0%,rgba(209,235,250,0) 5%,rgba(209,235,250,.12) 17%,rgba(121,188,228,.22) 50%,rgba(209,235,250,.12) 83%,rgba(209,235,250,0) 95%,transparent 100%)!important}html body header#zpNewNav.is-scrolled:not(.is-mega-open) #zpHeaderLine2147 .zpHeaderLine2147__base,html body.zp-nav-scrolled header#zpNewNav:not(.is-mega-open) #zpHeaderLine2147 .zpHeaderLine2147__base{opacity:.10!important;background:linear-gradient(90deg,transparent 0%,rgba(7,20,38,0) 5%,rgba(7,20,38,.035) 17%,rgba(7,20,38,.11) 50%,rgba(7,20,38,.035) 83%,rgba(7,20,38,0) 95%,transparent 100%)!important}html body header#zpNewNav #zpHeaderLine2147 .zpHeaderLine2147__draw{height:2px!important;top:3.5px!important;opacity:0!important;transform:scaleX(0)!important;transform-origin:left center!important;background:linear-gradient(90deg,rgba(209,235,250,0) 0%,rgba(209,235,250,.18) 12%,rgba(209,235,250,.95) 45%,rgba(121,188,228,.96) 56%,rgba(209,235,250,.24) 88%,rgba(121,188,228,0) 100%)!important;box-shadow:none!important;animation:zpHeaderLine2147Draw 1.1s cubic-bezier(.16,1,.3,1) .38s forwards!important}html body header#zpNewNav #zpHeaderLine2147 .zpHeaderLine2147__blink{height:5px!important;top:2px!important;opacity:0!important;background:linear-gradient(90deg,transparent 0%,rgba(209,235,250,.16) 22%,rgba(121,188,228,.55) 50%,rgba(209,235,250,.16) 78%,transparent 100%)!important;filter: none;animation:zpHeaderLine2147Blink .52s ease-out 1.34s forwards!important}@keyframes zpHeaderLine2147Draw{0%{opacity:0;transform:scaleX(0);filter:none}18%{opacity:.95;transform:scaleX(.18);filter:none}72%{opacity:.92;transform:scaleX(1);filter:none}100%{opacity:0;transform:scaleX(1);filter:none}}@keyframes zpHeaderLine2147Blink{0%{opacity:0;transform:scaleX(.96)}38%{opacity:.78;transform:scaleX(1)}100%{opacity:0;transform:scaleX(1)}}@media(max-width:1080px){html body header#zpNewNav #zpHeaderLine2147{width:calc(100vw + 40px)!important;max-width:calc(100vw + 40px)!important;left:50%!important;bottom:0!important;transform:translate3d(-50%,0,0)!important}html body header#zpNewNav #zpHeaderLine2147 .zpHeaderLine2147__base{opacity:.18!important}}@media(prefers-reduced-motion:reduce){html body header#zpNewNav #zpHeaderLine2147 .zpHeaderLine2147__draw,html body header#zpNewNav #zpHeaderLine2147 .zpHeaderLine2147__blink{display:none!important;animation:none!important}html body header#zpNewNav #zpHeaderLine2147 .zpHeaderLine2147__base{opacity:.16!important}}@media(max-width:1080px){html body{padding-top:0!important;margin-top:0!important}html body #zpStronyKatowice,html body #zpKnowledgePro,html body .zpNewNav + main,html body .zpNewNav + #content,html body main.site-main,html body #primary,html body #content.site-content{margin-top:0!important;padding-top:0!important}html body #zpStronyKatowice .zpWebHeroKat{padding-top:calc(clamp(78px,8vw,124px) + 45px)!important}html body #zpKnowledgePro .zpKBHero{padding-top:calc(116px + 45px)!important}}@media(max-width:760px){html body #zpStronyKatowice .zpWebHeroKat__mock,html body #zpStronyKatowice .zpWebHeroKat__screenAura,html body #zpStronyKatowice .zpWebHeroKat__miniCard{opacity:0!important;visibility:hidden!important}html body #zpStronyKatowice .zpWebHeroKat[data-ready="1"] .zpWebHeroKat__mock,html body #zpStronyKatowice .zpWebHeroKat[data-ready="1"] .zpWebHeroKat__miniCard{visibility:visible!important;opacity:1!important}html body #zpStronyKatowice .zpWebHeroKat[data-ready="1"] .zpWebHeroKat__screenAura{visibility:visible!important}}</style>
  <?php
}, 1000);

add_action('wp_footer', function(){
  if (is_admin()) { return; }
  ?>
<script id="zp-suite-2148-mobile-video-and-line-guard">
(function(){
  /* v2.2.301: header separator line removed permanently. */
var isMobile = window.matchMedia && window.matchMedia('(max-width:1080px)').matches;
  if(isMobile){
    var vids = Array.prototype.slice.call(document.querySelectorAll('#zpStronyKatowice video,#zpKnowledgePro video'));
    vids.forEach(function(v){
      try{
        /* v2.1.87: keep mobile video sources alive; only make them safe for autoplay/performance. */
        v.muted = true;
        v.defaultMuted = true;
        v.setAttribute('muted','');
        v.setAttribute('playsinline','');
        v.setAttribute('webkit-playsinline','');
        v.setAttribute('autoplay','');
        v.autoplay = true;
        v.loop = true;
        v.preload = 'metadata';
        v.setAttribute('preload','metadata');
        Array.prototype.slice.call(v.querySelectorAll('source[data-src]')).forEach(function(s){
          if(!s.getAttribute('src')){ s.setAttribute('src', s.getAttribute('data-src')); }
        });
        var start=function(){
          try{
            if(v.readyState < 2){ v.load(); }
            var p=v.play();
            if(p && p.catch){ p.catch(function(){}); }
          }catch(e){}
        };
        if('requestIdleCallback' in window){ requestIdleCallback(start,{timeout:1400}); }
        else { setTimeout(start,450); }
      }catch(e){}
    });
  }
})();
</script>
  <?php
}, 1000);


/**
 * ZP Suite v2.1.87 — final header line visibility + mobile video restore.
 * - Header line is 20% more visible.
 * - Draw animation is visible while the base line already sits in place.
 * - Mobile videos keep src/autoplay instead of being stripped.
 */
add_action('wp_head', function(){
  if (is_admin()) { return; }
  ?>
<style id="zp-suite-2148-header-line-final-visibility">html body header#zpNewNav .zpNewNav__shell{position:relative!important;overflow:visible!important}html body header#zpNewNav #zpHeaderLine2147{position:absolute!important;left:50%!important;bottom:0!important;width:min(1850px,calc(100vw - 24px))!important;height:10px!important;display:block!important;visibility:visible!important;opacity:1!important;overflow:visible!important;pointer-events:none!important;z-index:2147483000!important;transform:translate3d(-50%,0,0)!important}html body header#zpNewNav #zpHeaderLine2147 .zpHeaderLine2147__base,html body header#zpNewNav #zpHeaderLine2147 .zpHeaderLine2147__draw,html body header#zpNewNav #zpHeaderLine2147 .zpHeaderLine2147__blink{position:absolute!important;left:0!important;right:0!important;top:4px!important;height:1px!important;border-radius:999px!important;display:block!important;visibility:visible!important;pointer-events:none!important}html body header#zpNewNav #zpHeaderLine2147 .zpHeaderLine2147__base{opacity:.20!important;background:linear-gradient(90deg,transparent 0%,rgba(209,235,250,0) 4%,rgba(209,235,250,.18) 17%,rgba(121,188,228,.30) 50%,rgba(209,235,250,.18) 83%,rgba(209,235,250,0) 96%,transparent 100%)!important;box-shadow:0 0 10px rgba(121,188,228,.07)!important}html body header#zpNewNav.is-scrolled:not(.is-mega-open) #zpHeaderLine2147 .zpHeaderLine2147__base,html body.zp-nav-scrolled header#zpNewNav:not(.is-mega-open) #zpHeaderLine2147 .zpHeaderLine2147__base{opacity:.12!important;background:linear-gradient(90deg,transparent 0%,rgba(7,20,38,0) 4%,rgba(7,20,38,.055) 17%,rgba(7,20,38,.135) 50%,rgba(7,20,38,.055) 83%,rgba(7,20,38,0) 96%,transparent 100%)!important;box-shadow:none!important}html body header#zpNewNav #zpHeaderLine2147 .zpHeaderLine2147__draw{height:2px!important;top:3.5px!important;opacity:0!important;transform:scaleX(0)!important;transform-origin:left center!important;background:linear-gradient(90deg,rgba(209,235,250,0) 0%,rgba(209,235,250,.32) 13%,rgba(209,235,250,1) 42%,rgba(121,188,228,1) 58%,rgba(209,235,250,.34) 88%,rgba(121,188,228,0) 100%)!important;box-shadow:0 0 14px rgba(209,235,250,.52),0 0 28px rgba(121,188,228,.30)!important;animation:zpHeaderLine2148Draw 1.12s cubic-bezier(.16,1,.3,1) .28s forwards!important}html body header#zpNewNav #zpHeaderLine2147 .zpHeaderLine2147__blink{height:5px!important;top:2px!important;opacity:0!important;transform:scaleX(1)!important;background:linear-gradient(90deg,transparent 0%,rgba(209,235,250,.13) 18%,rgba(121,188,228,.36) 50%,rgba(209,235,250,.13) 82%,transparent 100%)!important;filter: none;animation:zpHeaderLine2148Blink .36s ease-out 1.18s forwards!important}@keyframes zpHeaderLine2148Draw{0%{opacity:0;transform:scaleX(0);filter:none}15%{opacity:1;transform:scaleX(.18);filter:none}75%{opacity:.96;transform:scaleX(1);filter:none}100%{opacity:0;transform:scaleX(1);filter:none}}@keyframes zpHeaderLine2148Blink{0%{opacity:0}38%{opacity:.58}100%{opacity:0}}@media(max-width:1080px){html body header#zpNewNav #zpHeaderLine2147{width:calc(100vw + 40px)!important;max-width:calc(100vw + 40px)!important}html body header#zpNewNav #zpHeaderLine2147 .zpHeaderLine2147__base{opacity:.22!important}}@media(prefers-reduced-motion:reduce){html body header#zpNewNav #zpHeaderLine2147 .zpHeaderLine2147__draw,html body header#zpNewNav #zpHeaderLine2147 .zpHeaderLine2147__blink{display:none!important;animation:none!important}}</style>
  <?php
}, 2148);

add_action('wp_footer', function(){
  if (is_admin()) { return; }
  ?>
<script id="zp-suite-2148-video-autoplay-restore">
(function(){
  var run=function(){
    var vids=Array.prototype.slice.call(document.querySelectorAll('#zpStronyKatowice video,#zpKnowledgePro video'));
    vids.forEach(function(v){
      try{
        v.muted=true; v.defaultMuted=true;
        v.setAttribute('muted','');
        v.setAttribute('playsinline','');
        v.setAttribute('webkit-playsinline','');
        v.setAttribute('autoplay','');
        v.autoplay=true;
        v.loop=true;
        v.preload='metadata';
        v.setAttribute('preload','metadata');
        Array.prototype.slice.call(v.querySelectorAll('source[data-src]')).forEach(function(s){
          if(!s.getAttribute('src')) s.setAttribute('src',s.getAttribute('data-src'));
        });
        if(v.readyState < 2){ try{v.load();}catch(e){} }
        var p=v.play();
        if(p && p.catch){ p.catch(function(){}); }
      }catch(e){}
    });
  };
  if(document.readyState==='loading'){document.addEventListener('DOMContentLoaded',run,{once:true});}
  else{run();}
  setTimeout(run,900);
})();
</script>
  <?php
}, 2148);


/**
 * ZP Suite v2.1.87 — final header line visibility controls + Katowice mobile video restore.
 */
add_action('wp_footer', function(){
  if (is_admin()) { return; }
  ?>
<script id="zp-suite-2150-katowice-mobile-video-restore">
(function(){
  function bootKatowiceVideo(){
    try{
      var video=document.querySelector('#zpStronyKatowice .zpWebHeroKat__video');
      if(!video) return;
      video.muted=true; video.defaultMuted=true; video.autoplay=true; video.loop=true; video.playsInline=true;
      video.setAttribute('muted',''); video.setAttribute('autoplay',''); video.setAttribute('loop',''); video.setAttribute('playsinline',''); video.setAttribute('webkit-playsinline',''); video.setAttribute('preload','metadata');
      Array.prototype.slice.call(video.querySelectorAll('source[data-src]')).forEach(function(s){ if(!s.getAttribute('src')) s.setAttribute('src',s.getAttribute('data-src')); });
      try{ if(video.readyState < 2) video.load(); }catch(e){}
      var play=function(){try{var p=video.play(); if(p&&p.catch) p.catch(function(){});}catch(e){}};
      play(); setTimeout(play,350); setTimeout(play,1200);
    }catch(e){}
  }
  if(document.readyState==='loading') document.addEventListener('DOMContentLoaded',bootKatowiceVideo,{once:true}); else bootKatowiceVideo();
  window.addEventListener('pageshow',bootKatowiceVideo,{passive:true});
})();
</script>
  <?php
}, 1200);


/**
 * ZP Suite v2.1.87 — final mobile menu/header line + Katowice video bootstrap.
 */
add_action('wp_footer', function(){
  if (is_admin()) { return; }
  ?>
<script id="zp-suite-2151-katowice-video-bootstrap">
(function(){
  function boot(){
    try{
      var root=document.querySelector('#zpStronyKatowice');
      if(!root) return;
      var video=root.querySelector('.zpWebHeroKat__video');
      if(!video) return;
      video.muted=true; video.defaultMuted=true; video.playsInline=true; video.autoplay=true; video.loop=true;
      video.setAttribute('muted',''); video.setAttribute('playsinline',''); video.setAttribute('webkit-playsinline',''); video.setAttribute('autoplay',''); video.setAttribute('loop',''); video.setAttribute('preload','none');
      var src=video.getAttribute('data-src')||video.getAttribute('src')||'';
      Array.prototype.slice.call(video.querySelectorAll('source')).forEach(function(s){
        var v=s.getAttribute('data-src')||s.getAttribute('src')||'';
        if(!src && v) src=v;
      });
      if(src && video.getAttribute('src')!==src){ video.setAttribute('src',src); }
      try{ if(video.dataset.zpKatBooted!=='1'){video.dataset.zpKatBooted='1';video.load();} }catch(e){}
      var play=function(){try{var p=video.play(); if(p&&p.catch)p.catch(function(){});}catch(e){}};
      play(); setTimeout(play,300); setTimeout(play,1000);
    }catch(e){}
  }
  if(document.readyState==='loading') document.addEventListener('DOMContentLoaded',boot,{once:true}); else boot();
  window.addEventListener('pageshow',boot,{passive:true});
  document.addEventListener('visibilitychange',function(){if(!document.hidden) boot();});
})();
</script>
  <?php
}, 2151);


/**
 * ZP Suite v2.1.87 — final contact tabs center + why mockup lower lock.
 */
add_action('wp_head', function(){
  if (is_admin()) { return; }
  ?>
<style id="zp-suite-2176-final-contact-why-lock">@media (max-width:760px){html body .zpContactSystemLight .zpContactSystemLight__tabs,html body .zpContactSystemLight__tabs{position:relative!important;width:calc(100% - 28px)!important;max-width:calc(100% - 28px)!important;margin:20px auto 34px!important;display:grid!important;grid-template-columns:repeat(3,minmax(0,1fr))!important;align-items:stretch!important;gap:0!important;overflow:visible!important;border-radius:30px!important;background:#f7f9fc!important;border:1px solid rgba(7,20,38,.105)!important;box-shadow:0 18px 54px rgba(7,20,38,.055)!important}html body .zpContactSystemLight .zpContactSystemLight__tab,html body .zpContactSystemLight .zpContactSystemLight__tab:hover,html body .zpContactSystemLight .zpContactSystemLight__tab:focus,html body .zpContactSystemLight .zpContactSystemLight__tab:focus-visible,html body .zpContactSystemLight__tab,html body .zpContactSystemLight__tab:hover,html body .zpContactSystemLight__tab:focus,html body .zpContactSystemLight__tab:focus-visible{position:relative!important;z-index:1!important;min-width:0!important;width:100%!important;min-height:148px!important;height:148px!important;padding:18px 6px 26px!important;display:flex!important;flex-direction:column!important;align-items:center!important;justify-content:center!important;gap:10px!important;text-align:center!important;overflow:visible!important;color:#8793a5!important;background:rgba(248,250,253,.96)!important;border:0!important;border-right:1px solid rgba(7,20,38,.095)!important;border-radius:0!important;box-shadow:none!important;transform:none!important}html body .zpContactSystemLight .zpContactSystemLight__tab:first-child,html body .zpContactSystemLight__tab:first-child{border-radius:30px 0 0 30px!important}html body .zpContactSystemLight .zpContactSystemLight__tab:last-child,html body .zpContactSystemLight__tab:last-child{border-right:0!important;border-radius:0 30px 30px 0!important}html body .zpContactSystemLight .zpContactSystemLight__tab.is-active,html body .zpContactSystemLight .zpContactSystemLight__tab[aria-selected="true"],html body .zpContactSystemLight__tab.is-active,html body .zpContactSystemLight__tab[aria-selected="true"]{z-index:3!important;color:#071426!important;background:#fff!important;box-shadow:0 16px 48px rgba(7,20,38,.06)!important}html body .zpContactSystemLight .zpContactSystemLight__tab:first-child.is-active,html body .zpContactSystemLight .zpContactSystemLight__tab:first-child[aria-selected="true"],html body .zpContactSystemLight__tab:first-child.is-active,html body .zpContactSystemLight__tab:first-child[aria-selected="true"]{border-radius:30px 0 0 30px!important}html body .zpContactSystemLight .zpContactSystemLight__tab:last-child.is-active,html body .zpContactSystemLight .zpContactSystemLight__tab:last-child[aria-selected="true"],html body .zpContactSystemLight__tab:last-child.is-active,html body .zpContactSystemLight__tab:last-child[aria-selected="true"]{border-radius:0 30px 30px 0!important}html body .zpContactSystemLight .zpContactSystemLight__tab::before,html body .zpContactSystemLight .zpContactSystemLight__tab::after,html body .zpContactSystemLight__tab::before,html body .zpContactSystemLight__tab::after{content:none!important;display:none!important}html body .zpContactSystemLight .zpContactSystemLight__tab > i,html body .zpContactSystemLight .zpContactSystemLight__tab > svg,html body .zpContactSystemLight .zpContactSystemLight__tab > i svg,html body .zpContactSystemLight__tab > i,html body .zpContactSystemLight__tab > svg,html body .zpContactSystemLight__tab > i svg{order:1!important;position:relative!important;left:auto!important;right:auto!important;top:auto!important;bottom:auto!important;inset:auto!important;display:block!important;width:34px!important;height:34px!important;min-width:34px!important;min-height:34px!important;flex:0 0 34px!important;margin:0 auto!important;transform:none!important;color:currentColor!important;stroke:currentColor!important}html body .zpContactSystemLight .zpContactSystemLight__tab > strong,html body .zpContactSystemLight__tab > strong{order:2!important;position:relative!important;left:auto!important;right:auto!important;top:auto!important;bottom:auto!important;inset:auto!important;display:block!important;width:100%!important;max-width:116px!important;min-width:0!important;margin:0 auto!important;padding:0!important;color:currentColor!important;font-size:0!important;line-height:0!important;font-weight:820!important;text-align:center!important;white-space:normal!important;overflow:visible!important;text-overflow:clip!important;transform:none!important}html body .zpContactSystemLight .zpContactSystemLight__tab[data-contact-tab="form"] > strong::after,html body .zpContactSystemLight__tab[data-contact-tab="form"] > strong::after{content:"Formularz\A kontaktowy"!important}html body .zpContactSystemLight .zpContactSystemLight__tab[data-contact-tab="phone"] > strong::after,html body .zpContactSystemLight__tab[data-contact-tab="phone"] > strong::after{content:"Zadzwońcie\A do mnie"!important}html body .zpContactSystemLight .zpContactSystemLight__tab[data-contact-tab="whatsapp"] > strong::after,html body .zpContactSystemLight__tab[data-contact-tab="whatsapp"] > strong::after{content:"Rozmowa\A WhatsApp"!important}html body .zpContactSystemLight .zpContactSystemLight__tab > strong::after,html body .zpContactSystemLight__tab > strong::after{display:block!important;white-space:pre-line!important;font-size:13.5px!important;line-height:1.12!important;letter-spacing:-.018em!important;font-weight:820!important;text-align:center!important}html body .zpContactSystemLight .zpContactSystemLight__tab > span,html body .zpContactSystemLight__tab > span{order:3!important;position:absolute!important;left:50%!important;right:auto!important;top:auto!important;bottom:-17px!important;display:block!important;width:38px!important;height:38px!important;min-width:38px!important;min-height:38px!important;margin:0!important;padding:0!important;flex:none!important;transform:translateX(-50%)!important;border-radius:999px!important;background:#e8eef7!important;border:7px solid #fff!important;box-shadow:0 16px 32px rgba(7,20,38,.10)!important;opacity:1!important;pointer-events:none!important}html body .zpContactSystemLight .zpContactSystemLight__tab > span::before,html body .zpContactSystemLight__tab > span::before{content:""!important;position:absolute!important;left:50%!important;top:46%!important;width:9px!important;height:9px!important;background:transparent!important;border:0!important;border-right:2px solid transparent!important;border-bottom:2px solid transparent!important;transform:translate(-50%,-50%) rotate(45deg)!important;box-shadow:none!important}html body .zpContactSystemLight .zpContactSystemLight__tab.is-active > span,html body .zpContactSystemLight .zpContactSystemLight__tab[aria-selected="true"] > span,html body .zpContactSystemLight__tab.is-active > span,html body .zpContactSystemLight__tab[aria-selected="true"] > span{background:#071426!important;transform:translateX(-50%)!important}html body .zpContactSystemLight .zpContactSystemLight__tab.is-active > span::before,html body .zpContactSystemLight .zpContactSystemLight__tab[aria-selected="true"] > span::before,html body .zpContactSystemLight__tab.is-active > span::before,html body .zpContactSystemLight__tab[aria-selected="true"] > span::before{border-right-color:#fff!important;border-bottom-color:#fff!important}html body .zpContactSystemLight .zpContactSystemLight__panelWrap,html body .zpContactSystemLight__panelWrap{margin-top:42px!important}}@media (max-width:390px){html body .zpContactSystemLight .zpContactSystemLight__tab,html body .zpContactSystemLight__tab{min-height:142px!important;height:142px!important;padding-left:3px!important;padding-right:3px!important}html body .zpContactSystemLight .zpContactSystemLight__tab > strong::after,html body .zpContactSystemLight__tab > strong::after{font-size:12.5px!important}}</style>
  <?php
}, PHP_INT_MAX);



/* ZP Suite v2.1.90: removed old ultra-late shop why mock hard-lock; controls now live in includes/shop-katowice-page.php. */



/* ZP Suite v2.2.06 — hard final fixes: focus ring, footer CTA mobile overflow/spacing, AP Studio label */
add_action('wp_head', function () {
  ?>
  <style id="zp-suite-hard-final-2206">html body *{-webkit-tap-highlight-color: transparent!important}html body a:focus,html body button:focus,html body [role="button"]:focus,html body input:focus,html body select:focus,html body textarea:focus,html body summary:focus,html body a:focus-visible,html body button:focus-visible,html body [role="button"]:focus-visible,html body input:focus-visible,html body select:focus-visible,html body textarea:focus-visible,html body summary:focus-visible,html body .zpNewNav *:focus,html body .zpNewNav *:focus-visible,html body [class^="zp"] *:focus,html body [class*=" zp"] *:focus,html body [class^="zp"] *:focus-visible,html body [class*=" zp"] *:focus-visible{outline:0!important;outline-width:0!important;outline-style:none!important;outline-color:transparent!important;outline-offset:0!important;box-shadow:none!important;-webkit-box-shadow:none!important}html body .zpNewNav__link:focus,html body .zpNewNav__link:focus-visible,html body .zpNewNav__item:focus,html body .zpNewNav__item:focus-visible,html body .zpNewNav__item.is-open > .zpNewNav__link,html body .zpNewNav__item.is-active > .zpNewNav__link,html body .zpNewNav button:focus,html body .zpNewNav button:focus-visible{outline:0!important;box-shadow:none!important;border-color:transparent!important}@media (max-width:760px){html body .zpMegaFooter,html body .zpMegaFooter *{box-sizing:border-box!important}html body .zpMegaFooter{width:100%!important;max-width:100vw!important;overflow:hidden!important}html body .zpMegaFooter__darkBand--cta{width:100vw!important;max-width:100vw!important;overflow:hidden!important;min-height:0!important;margin-top:0!important;padding-left:0!important;padding-right:0!important;padding-bottom:36px!important}html body.home .zpMegaFooter__darkBand--cta,html body.front-page .zpMegaFooter__darkBand--cta,html body.page-template-front-page .zpMegaFooter__darkBand--cta{padding-top:0!important}html body:not(.home):not(.front-page):not(.page-template-front-page) .zpMegaFooter__darkBand--cta{padding-top:30px!important}html body.home .zpMegaFooter__inner--cta,html body.front-page .zpMegaFooter__inner--cta,html body.page-template-front-page .zpMegaFooter__inner--cta{transform:translateY(-40px)!important;margin-bottom:-40px!important}html body .zpMegaFooter__inner--cta{width:calc(100vw - 32px)!important;max-width:calc(100vw - 32px)!important;min-width:0!important;margin-left:auto!important;margin-right:auto!important;padding-left:0!important;padding-right:0!important;overflow:hidden!important}html body .zpMegaFooter__cta{width:100%!important;max-width:100%!important;min-width:0!important;overflow:hidden!important;display:grid!important;grid-template-columns:minmax(0,1fr)!important;grid-template-areas:"copy" "photo" "actions"!important;gap:8px!important;margin:0!important;padding:0!important}html body .zpMegaFooter__ctaCopy{width:100%!important;max-width:100%!important;min-width:0!important;overflow:visible!important;margin:0!important;padding:0!important;transform:none!important}html body .zpMegaFooter__eyebrow--dark{margin-top:0!important;margin-bottom:12px!important;padding-top:0!important}html body .zpMegaFooter__cta h2,html body .zpMegaFooter__cta p{max-width:100%!important}html body .zpMegaFooter__teamPhoto{max-width:none!important;justify-self:center!important;overflow:visible!important}html body .zpMegaFooter__ctaActions{grid-area:actions!important;display:grid!important;grid-template-columns:minmax(0,1fr)!important;width:100%!important;max-width:100%!important;min-width:0!important;gap:12px!important;margin:0!important;padding:0!important;overflow:hidden!important;justify-items:stretch!important;align-items:stretch!important;left:auto!important;right:auto!important;transform:none!important}html body .zpMegaFooter__ctaActions .zpMegaFooter__btn,html body .zpMegaFooter__btn{width:100%!important;max-width:100%!important;min-width:0!important;flex:0 1 auto!important;display:flex!important;box-sizing:border-box!important;justify-content:center!important;align-items:center!important;padding-left:14px!important;padding-right:14px!important;margin-left:0!important;margin-right:0!important;transform:none!important;left:auto!important;right:auto!important;overflow:hidden!important;white-space:nowrap!important}html body .zpMegaFooter__btn span{min-width:0!important;max-width:100%!important;overflow:hidden!important;text-overflow:ellipsis!important;white-space:nowrap!important}}</style>
  <?php
}, PHP_INT_MAX);



/* ZP Suite v2.2.08 — strict home map removal spacing + final click focus reset */
add_action('wp_footer', function () {
  ?>
  <style id="zp-suite-final-2207">html body *{-webkit-tap-highlight-color:rgba(0,0,0,0)!important;-webkit-focus-ring-color:rgba(0,0,0,0)!important}html body *:focus,html body *:focus-visible,html body *:active,html body a:focus,html body a:focus-visible,html body button:focus,html body button:focus-visible,html body [role="button"]:focus,html body [role="button"]:focus-visible,html body input:focus,html body textarea:focus,html body select:focus,html body summary:focus,html body .zpNewNav *:focus,html body .zpNewNav *:focus-visible,html body .zpNewNav__item:focus,html body .zpNewNav__item:focus-within,html body .zpNewNav__link:focus,html body .zpNewNav__link:focus-visible,html body .zpNewNav__link:active,html body .zpNewNav__mSummary:focus,html body .zpNewNav__mSummary:focus-visible,html body .zpNewNav__mLink:focus,html body .zpNewNav__mLink:focus-visible,html body .zpNewNav__megaLink:focus,html body .zpNewNav__megaLink:focus-visible{outline:none!important;outline-width:0!important;outline-style:none!important;outline-color:transparent!important;outline-offset:0!important;box-shadow:none!important;-webkit-box-shadow:none!important}html body .zpNewNav__item.is-open,html body .zpNewNav__item.is-open > .zpNewNav__link,html body .zpNewNav__item:focus-within > .zpNewNav__link,html body .zpNewNav__link:focus,html body .zpNewNav__link:active,html body .zpNewNav__link:focus-visible{border:0!important;outline:0!important;box-shadow:none!important;background-color:transparent!important}html body button::-moz-focus-inner,html body input::-moz-focus-inner{border:0!important;padding:0!important}html body.home .zpContactPageShortcode[data-zp-home-contact-form] .zpContactFormLight__mapWrap,html body.front-page .zpContactPageShortcode[data-zp-home-contact-form] .zpContactFormLight__mapWrap,html body.page-template-front-page .zpContactPageShortcode[data-zp-home-contact-form] .zpContactFormLight__mapWrap{display:none!important;height:0!important;min-height:0!important;max-height:0!important;margin:0!important;padding:0!important;overflow:hidden!important}@media (max-width:760px){html body.home .zpMegaFooter__darkBand--cta,html body.front-page .zpMegaFooter__darkBand--cta,html body.page-template-front-page .zpMegaFooter__darkBand--cta{min-height:0!important;height:auto!important;padding-top:0!important;padding-bottom:26px!important;margin-top:0!important;overflow:hidden!important}html body.home .zpMegaFooter__inner--cta,html body.front-page .zpMegaFooter__inner--cta,html body.page-template-front-page .zpMegaFooter__inner--cta{transform:translateY(-118px)!important;margin-top:0!important;margin-bottom:-118px!important;padding-top:0!important}html body.home .zpMegaFooter__cta,html body.front-page .zpMegaFooter__cta,html body.page-template-front-page .zpMegaFooter__cta{padding-top:0!important;margin-top:0!important;gap:0!important}html body.home .zpMegaFooter__ctaCopy,html body.front-page .zpMegaFooter__ctaCopy,html body.page-template-front-page .zpMegaFooter__ctaCopy{padding-top:0!important;margin-top:0!important;transform:none!important}html body.home .zpMegaFooter__eyebrow--dark,html body.front-page .zpMegaFooter__eyebrow--dark,html body.page-template-front-page .zpMegaFooter__eyebrow--dark{margin-top:0!important;padding-top:0!important}html body.home .zpMegaFooter__darkBand--cta::before,html body.home .zpMegaFooter__darkBand--cta::after,html body.front-page .zpMegaFooter__darkBand--cta::before,html body.front-page .zpMegaFooter__darkBand--cta::after{display:none!important;content:none!important}}</style>
  <script id="zp-suite-final-2207-js">
    (function(){
      function killFocus(e){
        var el=e.target && e.target.closest ? e.target.closest('a,button,[role="button"],summary,.zpNewNav__link,.zpNewNav__mSummary,.zpNewNav__megaLink') : null;
        if(!el) return;
        setTimeout(function(){ try{ el.none; }catch(_e){} },0);
      }
      document.addEventListener('pointerup', killFocus, true);
      document.addEventListener('touchend', killFocus, true);
      document.addEventListener('mouseup', killFocus, true);
      document.addEventListener('click', killFocus, true);
    })();
  </script>
  <?php
}, PHP_INT_MAX);


/* ZP Suite v2.2.08 — real footer mobile reset, legal links, contact hero lift, hard nav active/focus cleanup */
add_action('wp_footer', function () {
  ?>
  <style id="zp-suite-ultra-final-2208">html body #zpNewNav,html body #zpNewNav *,html body .zpNewNav,html body .zpNewNav *{-webkit-tap-highlight-color: transparent !important;-webkit-focus-ring-color: transparent !important}html body #zpNewNav :where(a,button,[role="button"],summary,li,span,div):focus,html body #zpNewNav :where(a,button,[role="button"],summary,li,span,div):focus-visible,html body #zpNewNav :where(a,button,[role="button"],summary,li,span,div):active,html body .zpNewNav :where(a,button,[role="button"],summary,li,span,div):focus,html body .zpNewNav :where(a,button,[role="button"],summary,li,span,div):focus-visible,html body .zpNewNav :where(a,button,[role="button"],summary,li,span,div):active{outline:0 !important;outline-width:0 !important;outline-style:none !important;outline-color:transparent !important;outline-offset:0 !important;box-shadow:none !important;-webkit-box-shadow:none !important}html body #zpNewNav .zpNewNav__link,html body #zpNewNav .zpNewNav__link:hover,html body #zpNewNav .zpNewNav__link:focus,html body #zpNewNav .zpNewNav__link:focus-visible,html body #zpNewNav .zpNewNav__link:active,html body #zpNewNav .zpNewNav__item.is-open > .zpNewNav__link,html body #zpNewNav .zpNewNav__item:focus-within > .zpNewNav__link,html body .zpNewNav .zpNewNav__link,html body .zpNewNav .zpNewNav__link:hover,html body .zpNewNav .zpNewNav__link:focus,html body .zpNewNav .zpNewNav__link:focus-visible,html body .zpNewNav .zpNewNav__link:active,html body .zpNewNav .zpNewNav__item.is-open > .zpNewNav__link,html body .zpNewNav .zpNewNav__item:focus-within > .zpNewNav__link{outline:0 !important;outline-offset:0 !important;box-shadow:none !important;-webkit-box-shadow:none !important;border:0 !important;background:transparent !important;background-color:transparent !important;border-radius:0 !important}html body #zpNewNav .zpNewNav__item.is-open,html body #zpNewNav .zpNewNav__item:focus,html body #zpNewNav .zpNewNav__item:focus-within,html body .zpNewNav .zpNewNav__item.is-open,html body .zpNewNav .zpNewNav__item:focus,html body .zpNewNav .zpNewNav__item:focus-within{outline:0 !important;box-shadow:none !important;border:0 !important;background:transparent !important}html body .zpContactPageShortcode[data-zp-home-contact-form] .zpContactFormLight__mapWrap,html body .zpContactPageShortcode[data-zp-home-contact-form] iframe[src*="google"],html body .zpContactPageShortcode[data-zp-home-contact-form] [class*="map"],html body .zpContactPageShortcode[data-zp-home-contact-form] [class*="Map"]{display:none !important;height:0 !important;min-height:0 !important;max-height:0 !important;margin:0 !important;padding:0 !important;overflow:hidden !important}@media (max-width:760px){html body .zpMegaFooter__darkBand--cta{min-height:auto !important;height:auto !important;padding-top:28px !important;padding-bottom:34px !important;margin-top:0 !important;overflow:hidden !important}html body .zpMegaFooter__inner--cta{transform:none !important;margin-top:0 !important;margin-bottom:0 !important;padding-top:0 !important;width:calc(100vw - 32px) !important;max-width:calc(100vw - 32px) !important}html body .zpMegaFooter__cta{grid-template-columns:minmax(0,1fr) !important;grid-template-areas:"copy" "photo" "actions" !important;align-items:start !important;gap:10px !important;padding:0 !important;margin:0 !important;min-height:0 !important}html body .zpMegaFooter__ctaCopy{padding-top:0 !important;margin-top:0 !important;transform:none !important}html body .zpMegaFooter__eyebrow--dark{margin-top:0 !important;margin-bottom:12px !important}html body.home .zpMegaFooter__darkBand--cta,html body.front-page .zpMegaFooter__darkBand--cta,html body.page-template-front-page .zpMegaFooter__darkBand--cta{padding-top:18px !important}html body .zpMegaFooter__teamPhoto{transform:translate3d(-30px,92px,0) scale(1.25) !important;margin:0 !important}html body .zpMegaFooter__ctaActions{width:100% !important;max-width:100% !important;overflow:hidden !important;display:grid !important;grid-template-columns:minmax(0,1fr) !important;gap:12px !important;margin:0 !important;padding:0 !important}html body .zpMegaFooter__ctaActions .zpMegaFooter__btn{width:100% !important;max-width:100% !important;min-width:0 !important;margin:0 !important;left:auto !important;right:auto !important;transform:none !important}html body .zpContactHeroKat{--teamMY:40px !important}html body .zpContactHeroKat .zpContactHeroKat__team{transform:translateX(calc(-50% + var(--teamMX))) translateY(40px) scale(var(--teamMS)) !important}}</style>
  <script id="zp-suite-ultra-final-2208-js">
    (function(){
      function clean(){
        try{
          document.querySelectorAll('#zpNewNav a,#zpNewNav button,#zpNewNav [role="button"],.zpNewNav a,.zpNewNav button,.zpNewNav [role="button"]').forEach(function(el){
            el.style.outline='0';
            el.style.boxShadow='none';
            el.style.webkitBoxShadow='none';
          });
        }catch(e){}
      }
      ['pointerdown','pointerup','mousedown','mouseup','touchstart','touchend','click'].forEach(function(ev){
        document.addEventListener(ev,function(e){
          if(e.target && e.target.closest && e.target.closest('#zpNewNav')) return;
          var el=e.target && e.target.closest ? e.target.closest('#zpNewNav a,#zpNewNav button,#zpNewNav [role="button"],.zpNewNav a,.zpNewNav button,.zpNewNav [role="button"]') : null;
          if(el){ setTimeout(function(){ try{ el.none; if(document.activeElement && document.activeElement.blur) document.activeElement.none; }catch(e){} clean(); },0); }
        },true);
      });
      clean();
      window.addEventListener('load', clean, {once:true});
    })();
  </script>
  <?php
}, PHP_INT_MAX);


/* ZP Suite v2.2.12 — footer-only mobile home rebuild guard */
add_action('wp_footer', function () {
  ?>
  <style id="zp-suite-footer-only-2212">.zpHomeFooterMobileFinal{display:none!important}@media (max-width:760px){html body .zpMegaFooter.zpMegaFooter--home > .zpMegaFooter__darkBand--cta{display:none!important;visibility:hidden!important;height:0!important;min-height:0!important;max-height:0!important;margin:0!important;padding:0!important;overflow:hidden!important}html body .zpMegaFooter.zpMegaFooter--home .zpHomeFooterMobileFinal{display:block!important;position:relative!important;width:100vw!important;max-width:100vw!important;margin:0!important;padding:24px 0 0!important;overflow:hidden!important;color:#fff!important;isolation:isolate!important;background:#030407!important}html body .zpMegaFooter.zpMegaFooter--home .zpHomeFooterMobileFinal__bg{position:absolute!important;inset:0!important;z-index:-2!important;pointer-events:none!important;background:radial-gradient(circle at 15% 0%,rgba(28,71,122,.16),transparent 34%),linear-gradient(135deg,#030407 0%,#05070b 50%,#07101d 100%)!important}html body .zpMegaFooter.zpMegaFooter--home .zpHomeFooterMobileFinal__bg:after{content:""!important;position:absolute!important;inset:0!important;background:linear-gradient(180deg,rgba(2,3,6,.62) 0%,rgba(2,3,6,.12) 42%,rgba(2,3,6,.96) 100%)!important}html body .zpMegaFooter.zpMegaFooter--home .zpHomeFooterMobileFinal__inner{width:calc(100vw - 32px)!important;max-width:calc(100vw - 32px)!important;margin:0 auto!important;padding:0!important;position:relative!important;z-index:2!important;box-sizing:border-box!important}html body .zpMegaFooter.zpMegaFooter--home .zpHomeFooterMobileFinal__eyebrow{display:inline-flex!important;align-items:center!important;gap:10px!important;margin:0 0 12px!important;padding:0!important;color:rgba(255,255,255,.70)!important;text-transform:uppercase!important;letter-spacing:.13em!important;font-size:10px!important;line-height:1!important;font-weight:800!important}html body .zpMegaFooter.zpMegaFooter--home .zpHomeFooterMobileFinal__eyebrow:before{content:""!important;width:24px!important;height:1px!important;background:linear-gradient(90deg,#1c477a,#376fa8,#7ea7d9)!important}html body .zpMegaFooter.zpMegaFooter--home .zpHomeFooterMobileFinal h2{margin:0!important;padding:0!important;color:#fff!important;font-size:clamp(35px,10.2vw,47px)!important;line-height:1.01!important;letter-spacing:-.056em!important;font-weight:560!important;font-variation-settings:"wght" 560!important;text-wrap:balance!important;overflow:visible!important}html body .zpMegaFooter.zpMegaFooter--home .zpHomeFooterMobileFinal h2 span{display:block!important;margin-top:.04em!important;font-size:.94em!important;line-height:1.04!important;letter-spacing:-.05em!important;font-weight:650!important;font-variation-settings:"wght" 650!important}html body .zpMegaFooter.zpMegaFooter--home .zpHomeFooterMobileFinal p{margin:18px 0 0!important;padding:0!important;color:rgba(255,255,255,.70)!important;font-size:14.5px!important;line-height:1.58!important;letter-spacing:-.006em!important}html body .zpMegaFooter.zpMegaFooter--home .zpHomeFooterMobileFinal__photo{position:relative!important;z-index:1!important;width:300vw!important;max-width:none!important;height:520px!important;margin:-86px 0 -238px -104vw!important;padding:0!important;pointer-events:none!important;user-select:none!important;overflow:visible!important}html body .zpMegaFooter.zpMegaFooter--home .zpHomeFooterMobileFinal__photo img{display:block!important;width:100%!important;height:100%!important;object-fit:contain!important;object-position:center bottom!important;filter:saturate(1.03) contrast(1.02)!important;-webkit-mask-image:linear-gradient(180deg,#000 0%,#000 64%,rgba(0,0,0,.72) 82%,transparent 100%)!important;mask-image:linear-gradient(180deg,#000 0%,#000 64%,rgba(0,0,0,.72) 82%,transparent 100%)!important}html body .zpMegaFooter.zpMegaFooter--home .zpHomeFooterMobileFinal__actions{position:relative!important;z-index:5!important;display:grid!important;grid-template-columns:1fr!important;gap:12px!important;width:100%!important;max-width:100%!important;margin:0!important;padding:0 0 30px!important;box-sizing:border-box!important}html body .zpMegaFooter.zpMegaFooter--home .zpHomeFooterMobileFinal__btn{min-height:54px!important;width:100%!important;max-width:100%!important;min-width:0!important;display:flex!important;align-items:center!important;justify-content:center!important;gap:10px!important;padding:0 18px!important;border-radius:999px!important;text-decoration:none!important;box-sizing:border-box!important;overflow:hidden!important;font-size:15px!important;font-weight:800!important;letter-spacing:-.02em!important}html body .zpMegaFooter.zpMegaFooter--home .zpHomeFooterMobileFinal__btn span{min-width:0!important;overflow:hidden!important;text-overflow:ellipsis!important;white-space:nowrap!important}html body .zpMegaFooter.zpMegaFooter--home .zpHomeFooterMobileFinal__btn svg{width:17px!important;height:17px!important;flex:0 0 17px!important}html body .zpMegaFooter.zpMegaFooter--home .zpHomeFooterMobileFinal__btn--light{background:#fff!important;color:#071426!important;border:1px solid #fff!important}html body .zpMegaFooter.zpMegaFooter--home .zpHomeFooterMobileFinal__btn--glass{background:rgba(255,255,255,.045)!important;color:#fff!important;border:1px solid rgba(255,255,255,.16)!important}}</style>
  <?php
}, PHP_INT_MAX);


/**
 * ZP Suite v2.2.35 — AJAX live search for header modal.
 */
add_action('wp_ajax_zp_suite_live_search', 'zp_suite_live_search_ajax');
add_action('wp_ajax_nopriv_zp_suite_live_search', 'zp_suite_live_search_ajax');
function zp_suite_live_search_ajax(){
  // Public live search for published pages/posts. Do not hard-block on nonce,
  // because cached headers can keep an older nonce and then the modal appears
  // to work while returning no results. Query is read-only and limited below.
  $nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';

  $term = isset($_POST['term']) ? sanitize_text_field(wp_unslash($_POST['term'])) : '';
  $term = trim($term);
  if (mb_strlen($term) < 2) {
    wp_send_json_success(['items' => [], 'term' => $term]);
  }

  $query = new WP_Query([
    's' => $term,
    'post_type' => ['page', 'post'],
    'post_status' => 'publish',
    'posts_per_page' => 8,
    'no_found_rows' => true,
    'ignore_sticky_posts' => true,
  ]);

  $items = [];
  foreach ($query->posts as $post) {
    $type = get_post_type($post);
    $label = $type === 'post' ? 'Wpis' : 'Strona';
    $excerpt = has_excerpt($post) ? get_the_excerpt($post) : wp_trim_words(wp_strip_all_tags($post->post_content), 18, '…');
    $thumb = get_the_post_thumbnail_url($post, 'full');
    $items[] = [
      'title' => html_entity_decode(get_the_title($post), ENT_QUOTES, get_bloginfo('charset')),
      'url' => get_permalink($post),
      'type' => $label,
      'excerpt' => html_entity_decode($excerpt, ENT_QUOTES, get_bloginfo('charset')),
      'thumb' => $thumb ? esc_url_raw(function_exists('zp_suite_2260_full_upload_image_url') ? zp_suite_2260_full_upload_image_url($thumb) : $thumb) : '',
    ];
  }
  wp_reset_postdata();

  wp_send_json_success(['items' => $items, 'term' => $term]);
}


/**
 * ZP Suite v2.2.102 — globalny patch kontaktu, toastów, z-index i CTA logo.
 * Poprawki: naturalny napis Wyślij, widoczne komunikaty po wysłaniu, mini chat nad headerem.
 */
add_action('wp_head', function(){
  if (is_admin()) { return; }
  ?>
  <style id="zp-suite-22102-contact-global-fix">html body .zpContactFormLight__submit,html body .zpContactSystemLight__btn[type="submit"],html body .zpContactSystemLight__btn,html body .zpMiniChat__submit{text-transform:none!important;letter-spacing:-.01em!important}html body .zpContactFormLight__submit [data-submit-text]::before,html body .zpContactSystemLight__btn[type="submit"]::before{content:none!important}html body .zpFloatUx{z-index:2147483000!important}html body .zpMiniChatOverlay{z-index:2147483600!important}html body .zpContactModal,html body [data-zp-contact-modal]{z-index:2147483500!important}html.zpContactModalOpen body{overflow:hidden!important}html body .zpContactToast22102{position:fixed!important;right:22px!important;bottom:104px!important;z-index:2147483640!important;width:min(390px,calc(100vw - 32px))!important;padding:16px 17px!important;border-radius:20px!important;background:#071426!important;color:#fff!important;border:1px solid rgba(255,255,255,.14)!important;box-shadow:0 28px 80px rgba(0,0,0,.28)!important;font-family:"Plus Jakarta Sans Local",system-ui,sans-serif!important;opacity:0!important;transform:translate3d(0,14px,0) scale(.98)!important;pointer-events:none!important;transition:opacity .24s ease,transform .24s cubic-bezier(.16,1,.3,1)!important}html body .zpContactToast22102.is-on{opacity:1!important;transform:translate3d(0,0,0) scale(1)!important}html body .zpContactToast22102 strong{display:block!important;margin:0 0 5px!important;color:#fff!important;font-size:15px!important;line-height:1.15!important;letter-spacing:-.03em!important;font-weight:760!important}html body .zpContactToast22102 span{display:block!important;color:rgba(255,255,255,.74)!important;font-size:13px!important;line-height:1.45!important;font-weight:430!important}@media(max-width:760px){html body .zpContactToast22102{left:12px!important;right:12px!important;bottom:calc(154px + env(safe-area-inset-bottom))!important;width:auto!important}}</style>
  <?php
}, PHP_INT_MAX);

add_action('wp_footer', function(){
  if (is_admin()) { return; }
  ?>
  <script id="zp-suite-22102-contact-global-fix-js">
    (function(){
      'use strict';
      function toast(title,msg){
        var el=document.querySelector('.zpContactToast22102');
        if(!el){ el=document.createElement('div'); el.className='zpContactToast22102'; el.setAttribute('role','status'); el.setAttribute('aria-live','polite'); el.innerHTML='<strong></strong><span></span>'; document.body.appendChild(el); }
        el.querySelector('strong').textContent=title||'Wiadomość wysłana';
        el.querySelector('span').textContent=msg||'Dziękujemy — zgłoszenie dotarło. Odezwemy się z konkretną odpowiedzią.';
        el.classList.add('is-on');
        clearTimeout(window.zpContactToast22102Timer);
        window.zpContactToast22102Timer=setTimeout(function(){el.classList.remove('is-on');},5200);
      }
      window.zpContactToast22102=toast;

      function normalizeButtons(){
        document.querySelectorAll('.zpContactFormLight__submit [data-submit-text], .zpContactSystemLight__btn[type="submit"]').forEach(function(el){
          if(el.matches('.zpContactSystemLight__btn')){
            var icon=el.querySelector('svg,i');
            Array.prototype.slice.call(el.childNodes).forEach(function(n){ if(n.nodeType===3 && /Wyślij|zapytanie|brief/i.test(n.nodeValue||'')){ n.nodeValue='Wyślij '; } });
            if(!icon && window.lucide && window.lucide.createIcons){ try{window.lucide.createIcons();}catch(e){} }
          }else{ el.textContent='Wyślij'; }
        });
      }
      normalizeButtons();
      document.addEventListener('DOMContentLoaded',normalizeButtons,{once:true});
      window.addEventListener('load',normalizeButtons,{once:true,passive:true});

      var originalFetch=window.fetch;
      if(typeof originalFetch==='function' && !window.zpFetchContactToast22102){
        window.zpFetchContactToast22102=1;
        window.fetch=function(input,init){
          var isContact=false;
          try{
            var body=init&&init.body;
            if(body && typeof FormData!=='undefined' && body instanceof FormData){ isContact=body.get('action')==='zp_suite_contact'; }
          }catch(e){}
          var p=originalFetch.apply(this,arguments);
          if(isContact){
            p.then(function(resp){
              try{ resp.clone().json().then(function(res){ if(res&&res.success){ var d=res.data||{}; toast(d.title||'Wiadomość wysłana',d.message||'Dziękujemy — zgłoszenie dotarło. Odezwemy się z konkretną odpowiedzią.'); } }); }catch(e){}
            });
          }
          return p;
        };
      }
    })();
  </script>
  <?php
}, PHP_INT_MAX);


/**
 * ZP Suite v2.2.106 — mobile mini chat, trust logos, studio spacing and lead delete hard fix.
 */
add_action('wp_head', function(){
  if (is_admin()) { return; }
  ?>
  <style id="zp-suite-22103-global-fixes">html body .zpMiniChat__visual h3,html body .zpMiniChat__formTitle{letter-spacing:-.018em!important;word-spacing:.03em!important;text-wrap:balance!important}@media(max-width:760px){html body .zpMiniChat__person{display:none!important;visibility:hidden!important;opacity:0!important}html body .zpMiniChat__visual{min-height:auto!important;padding:24px 22px 18px!important}html body .zpMiniChat__visual h3{font-size:clamp(32px,10vw,40px)!important;line-height:1.02!important;letter-spacing:-.012em!important;max-width:100%!important}html body .zpMiniChat__formTitle{font-size:clamp(27px,8.7vw,32px)!important;line-height:1.04!important;letter-spacing:-.012em!important}html body .zpMiniChat__chips{margin-top:18px!important;max-width:100%!important}}html body .zpTrustedLogos__logoCard,html body .zpbsHomeTrust__logo{overflow:visible!important}html body .zpTrustedLogos__logoCard img,html body .zpbsHomeTrust__logo img{display:block!important;width:auto!important;height:auto!important;max-width:92%!important;max-height:82px!important;object-fit:contain!important;filter:brightness(0) invert(1) grayscale(1) contrast(1.08)!important;opacity:.94!important;mix-blend-mode:normal!important;background:transparent!important}html body .zpTrustPinned__logo{overflow:visible!important}html body .zpTrustPinned__logo img,html body .zpTrustPinned__logoImg{object-fit:contain!important;filter:grayscale(1) brightness(0) contrast(1.18)!important;mix-blend-mode:multiply!important}@media(max-width:760px){html body .zpTrustPinned__logo img,html body .zpTrustPinned__logoImg{max-width:96%!important;max-height:150px!important}html body .zpTrustedLogos__logoCard img,html body .zpbsHomeTrust__logo img{max-width:94%!important;max-height:72px!important}html body.zpbs-studio-page #zpbsUltimate,html body #zpbsUltimate{padding-bottom:0!important;margin-bottom:0!important}html body #zpbsUltimate .zpbsChoose{padding-bottom:34px!important}html body #zpbsUltimate .zpbsBrief{padding-bottom:34px!important}html body #zpbsUltimate + *,html body .elementor-widget-shortcode:has(#zpbsUltimate){margin-bottom:0!important;padding-bottom:0!important}html body .zpTrustPinned{padding-bottom:0!important;margin-bottom:0!important}html body .zpTrustPinned__inner{padding-bottom:clamp(28px,7vw,42px)!important}html body .zpTrustPinned__stats{margin-bottom:14px!important}html body .zpTrustPinned__progress{margin-bottom:8px!important}html body .zpTrustPinned__scene{min-height:240px!important;height:240px!important;margin-top:0!important;margin-bottom:0!important;align-items:center!important}html body .zpTrustPinned__logos{height:228px!important;min-height:228px!important;margin-top:0!important;margin-bottom:0!important}html body .zpTrustPinned__logo,html body .zpTrustPinned__logo.is-mobile-prev,html body .zpTrustPinned__logo.is-mobile-next,html body .zpTrustPinned__logo.is-mobile-active{width:min(90vw,330px)!important;height:188px!important;padding:18px!important;top:50%!important;overflow:visible!important}html body .zpTrustPinned__logo img,html body .zpTrustPinned__logoImg,html body .zpTrustPinned__logo--wide img,html body .zpTrustPinned__logo--compact img,html body .zpTrustPinned__logo--scale115 img,html body .zpTrustPinned__logo--scale090 img{width:100%!important;height:96px!important;max-width:224px!important;max-height:96px!important;object-fit:contain!important;transform:none!important;--zp-logo-scale:1!important}html body .zpTrustPinned__logo--wide img{max-width:276px!important;height:78px!important;max-height:78px!important}html body .zpTrustPinned__logo--compact img{max-width:196px!important;height:104px!important;max-height:104px!important}}@media(max-width:420px){html body .zpTrustPinned__scene{height:220px!important;min-height:220px!important}html body .zpTrustPinned__logos{height:210px!important;min-height:210px!important}html body .zpTrustPinned__logo,html body .zpTrustPinned__logo.is-mobile-active,html body .zpTrustPinned__logo.is-mobile-prev,html body .zpTrustPinned__logo.is-mobile-next{height:176px!important;width:min(92vw,318px)!important;padding:16px!important}html body .zpTrustPinned__logo img,html body .zpTrustPinned__logoImg,html body .zpTrustPinned__logo--wide img,html body .zpTrustPinned__logo--compact img{max-width:210px!important;height:90px!important;max-height:90px!important}html body .zpTrustPinned__logo--wide img{max-width:258px!important;height:74px!important;max-height:74px!important}html body .zpTrustPinned__logo--compact img{max-width:184px!important;height:98px!important;max-height:98px!important}}</style>
  <?php
}, PHP_INT_MAX);

add_action('admin_init', function(){
  if (!is_admin() || !current_user_can('manage_options')) { return; }
  if (empty($_GET['page']) || $_GET['page'] !== 'zp-suite-leads') { return; }

  $leads = function_exists('zp_suite_leads_all') ? zp_suite_leads_all() : get_option('zp_suite_leads', []);
  if (!is_array($leads)) { $leads = []; }

  // Twarde usuwanie pojedynczego leada przez POST — omija problemy z linkami GET/cache/URL encoding.
  if (!empty($_POST['zp_lead_delete_post'])) {
    check_admin_referer('zp_suite_lead_delete_post');
    $lead_id = sanitize_text_field(wp_unslash($_POST['lead_id'] ?? ''));
    $before = count($leads);
    $leads = array_values(array_filter($leads, function($lead) use ($lead_id){
      return (string)($lead['id'] ?? '') !== $lead_id;
    }));
    update_option('zp_suite_leads', $leads, false);
    wp_cache_delete('zp_suite_leads', 'options');
    $removed = max(0, $before - count($leads));
    wp_safe_redirect(admin_url('admin.php?page=zp-suite-leads&deleted='.(int)$removed));
    exit;
  }

  // Twarde czyszczenie masowe — dodatkowy handler, niezależny od renderowania listy.
  if (!empty($_POST['zp_leads_cleanup_hard'])) {
    check_admin_referer('zp_suite_leads_cleanup_hard');
    $cleanup = sanitize_key(wp_unslash($_POST['zp_leads_cleanup_hard'] ?? ''));
    $before = count($leads);
    if ($cleanup === 'done') {
      $leads = array_values(array_filter($leads, function($lead){ return !in_array(($lead['status'] ?? 'new'), ['closed','spam'], true); }));
    } elseif ($cleanup === 'old30') {
      $limit = strtotime('-30 days', current_time('timestamp'));
      $leads = array_values(array_filter($leads, function($lead) use ($limit){
        $ts = !empty($lead['created_at']) ? strtotime($lead['created_at']) : current_time('timestamp');
        return $ts >= $limit;
      }));
    } elseif ($cleanup === 'all') {
      $leads = [];
    }
    update_option('zp_suite_leads', $leads, false);
    wp_cache_delete('zp_suite_leads', 'options');
    $removed = max(0, $before - count($leads));
    wp_safe_redirect(admin_url('admin.php?page=zp-suite-leads&cleaned='.(int)$removed));
    exit;
  }
}, 1);

function zp_suite_22103_logo_set(){
  return [
    ['name'=>'Vista','image'=>'https://zaprojektowani.com/wp-content/uploads/2026/05/vista.webp','url'=>'','visible'=>'1'],
    ['name'=>'Siemianowski','image'=>'https://zaprojektowani.com/wp-content/uploads/2026/05/siemianowski-1.webp','url'=>'','visible'=>'1'],
    ['name'=>'Sfera','image'=>'https://zaprojektowani.com/wp-content/uploads/2026/05/sfera-1.webp','url'=>'','visible'=>'1'],
    ['name'=>'Raxo','image'=>'https://zaprojektowani.com/wp-content/uploads/2026/05/raxo.webp','url'=>'','visible'=>'1'],
    ['name'=>'ProScarves','image'=>'https://zaprojektowani.com/wp-content/uploads/2026/05/proscarves.webp','url'=>'','visible'=>'1'],
    ['name'=>'Prisma Dent','image'=>'https://zaprojektowani.com/wp-content/uploads/2026/05/prisma_dent.webp','url'=>'','visible'=>'1'],
    ['name'=>'Polerstone','image'=>'https://zaprojektowani.com/wp-content/uploads/2026/05/polerstone.webp','url'=>'','visible'=>'1'],
    ['name'=>'Piotr Mazur','image'=>'https://zaprojektowani.com/wp-content/uploads/2026/05/piotr_mazur.webp','url'=>'','visible'=>'1'],
    ['name'=>'Kamiński','image'=>'https://zaprojektowani.com/wp-content/uploads/2026/05/kaminski.webp','url'=>'','visible'=>'1'],
    ['name'=>'Gravia','image'=>'https://zaprojektowani.com/wp-content/uploads/2026/05/gravia.webp','url'=>'','visible'=>'1'],
    ['name'=>'Apartament Piękna','image'=>'https://zaprojektowani.com/wp-content/uploads/2026/05/ap.webp','url'=>'','visible'=>'1'],
  ];
}

add_action('plugins_loaded', function(){
  $stored = get_option('zp_suite_trust_logos_version_22103', '');
  if ($stored === '2.2.105') { return; }
  $current = get_option('zp_suite_cms', []);
  if (!is_array($current)) { $current = []; }
  $current['logos'] = zp_suite_22103_logo_set();
  update_option('zp_suite_cms', $current, false);
  update_option('zp_suite_trust_logos_version_22103', '2.2.105', false);
}, 30);

/* ZP Suite v2.2.184 — reverted broken footer spacer/layering override. */


/* ZP Suite v2.2.272 — removed obsolete mobile header tap/opacity patch; mobile header is now controlled cleanly in templates/header.php and assets/js/blocks/header.js. */


/**
 * ZP Suite v2.2.300 — finalny reset cieni headera.
 * Usuwa dropshadow / box-shadow z warstw static/sticky/force-mobile bez ruszania logiki logo.
 */
function zp_suite_2300_header_shadow_reset_css(){
  ?>
  <style id="zp-suite-2300-header-shadow-reset-css">html body header#zpNewNav,html body header#zpNewNav.zpNewNav,html body header#zpNewNav.is-scrolled,html body.zp-nav-scrolled header#zpNewNav,html body header#zpNewNav.zpNewNav--heroOverlay,html body header#zpNewNav.zpNewNav--forceMobile,html body.zpNewNav-force-mobile header#zpNewNav,html body header#zpNewNav > .zpNewNav__shell,html body header#zpNewNav .zpNewNav__shell,html body header#zpNewNav .zpNewNav__inner,html body header#zpNewNav .zpNewNav__mobileBar,html body header#zpNewNav .zpNewNav__mobileBrand,html body header#zpNewNav .zpNewNav__mobileBg,html body header#zpNewNav .zpNewNav__fixedBg,html body header#zpNewNav .zpNewNav__backdrop,html body header#zpNewNav .zpNewNav__glass,html body header#zpNewNav .zpHeaderLine2147,html body header#zpNewNav [id*="zpHeaderLine"],html body header#zpNewNav [class*="HeaderLine"]{box-shadow:none!important;-webkit-box-shadow:none!important;text-shadow:none!important;filter:none!important}html body header#zpNewNav::before,html body header#zpNewNav::after,html body header#zpNewNav > .zpNewNav__shell::before,html body header#zpNewNav > .zpNewNav__shell::after,html body header#zpNewNav .zpNewNav__shell::before,html body header#zpNewNav .zpNewNav__shell::after,html body header#zpNewNav .zpNewNav__inner::before,html body header#zpNewNav .zpNewNav__inner::after,html body header#zpNewNav .zpNewNav__mobileBar::before,html body header#zpNewNav .zpNewNav__mobileBar::after,html body header#zpNewNav .zpHeaderLine2147::before,html body header#zpNewNav .zpHeaderLine2147::after,html body header#zpNewNav [id*="zpHeaderLine"]::before,html body header#zpNewNav [id*="zpHeaderLine"]::after,html body header#zpNewNav [class*="HeaderLine"]::before,html body header#zpNewNav [class*="HeaderLine"]::after{box-shadow:none!important;-webkit-box-shadow:none!important;text-shadow:none!important;filter:none!important}html body header#zpNewNav.is-scrolled:not(.is-mega-open) > .zpNewNav__shell,html body header#zpNewNav.is-scrolled:not(.is-mega-open) .zpNewNav__shell,html body.zp-nav-scrolled header#zpNewNav:not(.is-mega-open) > .zpNewNav__shell,html body.zp-nav-scrolled header#zpNewNav:not(.is-mega-open) .zpNewNav__shell,html body header#zpNewNav:not(.zpNewNav--heroOverlay):not(.is-mega-open) > .zpNewNav__shell,html body header#zpNewNav:not(.zpNewNav--heroOverlay):not(.is-mega-open) .zpNewNav__shell{box-shadow:none!important;-webkit-box-shadow:none!important}html body header#zpNewNav .zpHeaderLine2147__draw,html body header#zpNewNav .zpHeaderLine2147__blink,html body header#zpNewNav [class*="HeaderLine"] *,html body header#zpNewNav [id*="zpHeaderLine"] *{box-shadow:none!important;-webkit-box-shadow:none!important;filter:none!important}</style>
  <?php
}
add_action('wp_head','zp_suite_2300_header_shadow_reset_css',PHP_INT_MAX);


/**
 * ZP Suite v2.2.300b — hard reset cieni/fade headera.
 * Drukowane w head i footer, żeby wygrało ze starymi inline patchami.
 */
function zp_suite_2300b_header_no_shadow_hard_css(){
  ?>
  <style id="zp-suite-2300b-header-no-shadow-hard-css">html body header#zpNewNav,html body header#zpNewNav.zpNewNav,html body header#zpNewNav.is-scrolled,html body.zp-nav-scrolled header#zpNewNav,html body header#zpNewNav > .zpNewNav__shell,html body header#zpNewNav .zpNewNav__shell,html body header#zpNewNav .zpNewNav__inner,html body header#zpNewNav .zpNewNav__mobileBar,html body header#zpNewNav .zpNewNav__mobileBg,html body header#zpNewNav .zpNewNav__fixedBg,html body header#zpNewNav .zpNewNav__backdrop,html body header#zpNewNav .zpNewNav__glass,html body header#zpNewNav.is-scrolled:not(.is-mega-open) > .zpNewNav__shell,html body header#zpNewNav.is-scrolled:not(.is-mega-open) .zpNewNav__shell,html body.zp-nav-scrolled header#zpNewNav:not(.is-mega-open) > .zpNewNav__shell,html body.zp-nav-scrolled header#zpNewNav:not(.is-mega-open) .zpNewNav__shell{box-shadow:none!important;-webkit-box-shadow:none!important;text-shadow:none!important;filter:none!important}html body header#zpNewNav.is-scrolled:not(.is-mega-open) > .zpNewNav__shell,html body header#zpNewNav.is-scrolled:not(.is-mega-open) .zpNewNav__shell,html body.zp-nav-scrolled header#zpNewNav:not(.is-mega-open) > .zpNewNav__shell,html body.zp-nav-scrolled header#zpNewNav:not(.is-mega-open) .zpNewNav__shell,html body header#zpNewNav:not(.zpNewNav--heroOverlay):not(.is-mega-open) > .zpNewNav__shell,html body header#zpNewNav:not(.zpNewNav--heroOverlay):not(.is-mega-open) .zpNewNav__shell{background-image:none!important;box-shadow:none!important;-webkit-box-shadow:none!important;filter:none!important}html body header#zpNewNav::before,html body header#zpNewNav::after,html body header#zpNewNav > .zpNewNav__shell::before,html body header#zpNewNav > .zpNewNav__shell::after,html body header#zpNewNav .zpNewNav__shell::before,html body header#zpNewNav .zpNewNav__shell::after,html body header#zpNewNav .zpNewNav__inner::before,html body header#zpNewNav .zpNewNav__inner::after,html body header#zpNewNav .zpNewNav__mobileBar::before,html body header#zpNewNav .zpNewNav__mobileBar::after,html body header#zpNewNav .zpNewNav__mobileBg::before,html body header#zpNewNav .zpNewNav__mobileBg::after,html body header#zpNewNav .zpHeaderLine2147,html body header#zpNewNav .zpHeaderLine2147::before,html body header#zpNewNav .zpHeaderLine2147::after,html body header#zpNewNav [id*="zpHeaderLine"],html body header#zpNewNav [id*="zpHeaderLine"]::before,html body header#zpNewNav [id*="zpHeaderLine"]::after,html body header#zpNewNav [class*="HeaderLine"],html body header#zpNewNav [class*="HeaderLine"]::before,html body header#zpNewNav [class*="HeaderLine"]::after{content:none!important;display:none!important;opacity:0!important;visibility:hidden!important;width:0!important;height:0!important;background:none!important;background-image:none!important;border:0!important;box-shadow:none!important;-webkit-box-shadow:none!important;filter:none!important;animation:none!important}html body header#zpNewNav .zpNewNav__round,html body header#zpNewNav .zpNewNav__round:hover,html body header#zpNewNav .zpNewNav__round:focus,html body header#zpNewNav .zpNewNav__round:focus-visible,html body header#zpNewNav .zpNewNav__round:active,html body header#zpNewNav .zpNewNav__round svg,html body header#zpNewNav .zpNewNav__round:hover svg,html body header#zpNewNav .zpNewNav__round:focus-visible svg{filter:none!important;box-shadow:none!important;-webkit-box-shadow:none!important}</style>
  <?php
}
add_action('wp_head','zp_suite_2300b_header_no_shadow_hard_css',PHP_INT_MAX);
add_action('wp_footer','zp_suite_2300b_header_no_shadow_hard_css',PHP_INT_MAX);


/**
 * ZP Suite v2.2.301 — final cleanup of old duplicate header line/shadows.
 * Removes the old animated separator DOM node and kills header shadows globally.
 */
function zp_suite_2301_header_clean_final_css(){
  ?>
  <style id="zp-suite-2301-header-clean-final-css">html body header#zpNewNav,html body header#zpNewNav.zpNewNav,html body header#zpNewNav.is-scrolled,html body.zp-nav-scrolled header#zpNewNav,html body header#zpNewNav.zpNewNav--heroOverlay,html body header#zpNewNav.zpNewNav--forceMobile,html body.zpNewNav-force-mobile header#zpNewNav,html body header#zpNewNav .zpNewNav__shell,html body header#zpNewNav > .zpNewNav__shell,html body header#zpNewNav .zpNewNav__inner,html body header#zpNewNav .zpNewNav__mobileBar,html body header#zpNewNav .zpNewNav__mobileBg,html body header#zpNewNav .zpNewNav__fixedBg,html body header#zpNewNav .zpNewNav__backdrop,html body header#zpNewNav .zpNewNav__glass,html body header#zpNewNav .zpNewNav__mobileSearch,html body header#zpNewNav .zpNewNav__burger,html body header#zpNewNav .zpNewNav__round,html body header#zpNewNav .zpNewNav__consult,html body header#zpNewNav .zpNewNav__call{box-shadow:none!important;-webkit-box-shadow:none!important;text-shadow:none!important;filter:none!important}html body header#zpNewNav::before,html body header#zpNewNav::after,html body header#zpNewNav .zpNewNav__shell::before,html body header#zpNewNav .zpNewNav__shell::after,html body header#zpNewNav > .zpNewNav__shell::before,html body header#zpNewNav > .zpNewNav__shell::after,html body header#zpNewNav .zpNewNav__inner::before,html body header#zpNewNav .zpNewNav__inner::after,html body header#zpNewNav .zpNewNav__mobileBar::before,html body header#zpNewNav .zpNewNav__mobileBar::after,html body header#zpNewNav .zpNewNav__mobileBg::before,html body header#zpNewNav .zpNewNav__mobileBg::after,html body #zpHeaderLine2147,html body header#zpNewNav #zpHeaderLine2147,html body header#zpNewNav [id*="zpHeaderLine"],html body header#zpNewNav [class*="HeaderLine"]{content:none!important;display:none!important;visibility:hidden!important;opacity:0!important;width:0!important;height:0!important;max-width:0!important;max-height:0!important;background:none!important;background-image:none!important;border:0!important;box-shadow:none!important;-webkit-box-shadow:none!important;filter:none!important;animation:none!important;overflow:hidden!important}html body #zpHeaderLine2147 *,html body header#zpNewNav #zpHeaderLine2147 *,html body header#zpNewNav [id*="zpHeaderLine"] *,html body header#zpNewNav [class*="HeaderLine"] *{display:none!important;visibility:hidden!important;opacity:0!important;background:none!important;background-image:none!important;box-shadow:none!important;-webkit-box-shadow:none!important;filter:none!important;animation:none!important}</style>
  <?php
}
add_action('wp_head','zp_suite_2301_header_clean_final_css',PHP_INT_MAX);
add_action('wp_footer','zp_suite_2301_header_clean_final_css',PHP_INT_MAX);

add_action('wp_footer', function(){
  if (is_admin()) { return; }
  ?>
  <script id="zp-suite-2301-remove-old-header-line">
  (function(){
    function kill(){
      try{document.querySelectorAll('#zpHeaderLine2147,[id*="zpHeaderLine"],[class*="zpHeaderLine"],[class*="HeaderLine"]').forEach(function(el){el.remove();});}catch(e){}
    }
    if(document.readyState==='loading'){document.addEventListener('DOMContentLoaded', kill, {once:true});}else{kill();}
    window.addEventListener('load', kill, {once:true});
    setTimeout(kill,60);setTimeout(kill,300);setTimeout(kill,1000);
  })();
  </script>
  <?php
}, PHP_INT_MAX);


/**
 * ZP Suite v2.2.324 — hero video remove light band.
 * Removes bright rectangular glow/light band from subpage video heroes and leaves a dark smooth mask.
 */
function zp_suite_2324_hero_video_remove_light_band_css(){
  if (is_admin()) { return; }
  ?>
  <style id="zp-suite-2324-hero-video-remove-light-band">html body #zpWebHeroKat .zpWebHeroKat__glow,html body #zpContactHeroKat .zpWebHeroKat__glow,html body .zpWebHeroKat .zpWebHeroKat__glow{display:none!important;opacity:0!important;visibility:hidden!important;background:none!important;filter:none!important;box-shadow:none!important}html body #zpShopHeroKat .zpShopCockpit__ambient,html body #zpShopHeroKat .zpShopCockpit__halo,html body .zpShopCockpit .zpShopCockpit__ambient,html body .zpShopCockpit .zpShopCockpit__halo,html body #zpBrandHeroKatSafe .zpBrandHeroSafe__ambient,html body #zpBrandHeroKatSafe .zpBrandHeroSafe__halo,html body .zpBrandHeroSafe .zpBrandHeroSafe__ambient,html body .zpBrandHeroSafe .zpBrandHeroSafe__halo{display:none!important;opacity:0!important;visibility:hidden!important;background:none!important;filter:none!important;box-shadow:none!important}html body #zpWebHeroKat .zpWebHeroKat__videoWrap::after,html body #zpContactHeroKat .zpWebHeroKat__videoWrap::after,html body .zpWebHeroKat .zpWebHeroKat__videoWrap::after{content:""!important;position:absolute!important;inset:-1px!important;z-index:3!important;pointer-events:none!important;opacity:.92!important;mix-blend-mode:normal!important;background: radial-gradient(ellipse at 20% 44%,rgba(2,4,7,.72) 0%,rgba(3,7,13,.48) 42%,rgba(3,7,13,.18) 68%,transparent 84%),linear-gradient(90deg,rgba(2,4,7,.86) 0%,rgba(5,13,24,.62) 42%,rgba(5,13,24,.38) 72%,rgba(2,4,7,.64) 100%),linear-gradient(180deg,rgba(2,4,7,.30) 0%,rgba(2,4,7,.48) 56%,rgba(2,4,7,.90) 100%)!important}html body #zpShopHeroKat .zpShopCockpit__videoWrap::after,html body .zpShopCockpit .zpShopCockpit__videoWrap::after,html body #zpBrandHeroKatSafe .zpBrandHeroSafe__videoWrap::after,html body .zpBrandHeroSafe .zpBrandHeroSafe__videoWrap::after{content:""!important;position:absolute!important;inset:-1px!important;z-index:3!important;pointer-events:none!important;opacity:.94!important;mix-blend-mode:normal!important;background: radial-gradient(ellipse at 20% 42%,rgba(2,4,7,.76) 0%,rgba(3,7,13,.50) 44%,rgba(3,7,13,.16) 72%,transparent 86%),linear-gradient(90deg,rgba(2,4,7,.86) 0%,rgba(5,13,24,.58) 46%,rgba(5,13,24,.34) 76%,rgba(2,4,7,.68) 100%),linear-gradient(180deg,rgba(2,4,7,.24) 0%,rgba(2,4,7,.42) 55%,rgba(2,4,7,.88) 100%)!important}html body #zpContactHeroKat .zpWebHeroKat__videoWrap::before,html body #zpWebHeroKat .zpWebHeroKat__videoWrap::before{opacity:.52!important;mix-blend-mode:color!important;background:linear-gradient(135deg,#020407 0%,#06101e 48%,#0b1830 100%)!important}@media(max-width:980px){html body #zpWebHeroKat .zpWebHeroKat__videoWrap::after,html body #zpContactHeroKat .zpWebHeroKat__videoWrap::after,html body .zpWebHeroKat .zpWebHeroKat__videoWrap::after,html body #zpShopHeroKat .zpShopCockpit__videoWrap::after,html body .zpShopCockpit .zpShopCockpit__videoWrap::after,html body #zpBrandHeroKatSafe .zpBrandHeroSafe__videoWrap::after,html body .zpBrandHeroSafe .zpBrandHeroSafe__videoWrap::after{opacity:.95!important;background: linear-gradient(180deg,rgba(2,4,7,.56) 0%,rgba(5,13,24,.60) 48%,rgba(2,4,7,.90) 100%),radial-gradient(ellipse at 50% 36%,rgba(5,13,24,.28) 0%,transparent 74%)!important}}</style>
  <?php
}
add_action('wp_head','zp_suite_2324_hero_video_remove_light_band_css',PHP_INT_MAX);
add_action('wp_footer','zp_suite_2324_hero_video_remove_light_band_css',PHP_INT_MAX);


/**
 * ZP Suite v2.2.326 — STRONY HERO: parity with shop/logo video mask.
 * Aligns /strony-internetowe-katowice/ hero background/video overlays with
 * /sklepy-internetowe-katowice/ and /logo-branding-katowice/ to remove the right-side cut.
 */
function zp_suite_2326_strony_hero_video_parity_css(){
  if (is_admin()) { return; }
  ?>
  <style id="zp-suite-2326-strony-hero-video-parity">html body:has(#zpWebHeroKat),html body:has(#zpStronyKatowice #zpWebHeroKat){background:#05070b!important}html body #zpWebHeroKat.zpWebHeroKat,html body #zpStronyKatowice #zpWebHeroKat.zpWebHeroKat{background: radial-gradient(circle at 12% 4%,rgba(28,71,122,.32),transparent 34%),radial-gradient(circle at 88% 18%,rgba(73,120,190,.18),transparent 35%),linear-gradient(135deg,#030407 0%,#06101e 46%,#05070b 100%)!important;overflow:hidden!important;isolation:isolate!important}html body #zpWebHeroKat .zpWebHeroKat__videoWrap,html body #zpStronyKatowice #zpWebHeroKat .zpWebHeroKat__videoWrap{position:absolute!important;inset:0!important;overflow:hidden!important;pointer-events:none!important;background:#020407!important;border:0!important;box-shadow:none!important;filter:none!important}html body #zpWebHeroKat .zpWebHeroKat__video,html body #zpStronyKatowice #zpWebHeroKat .zpWebHeroKat__video{display:block!important;width:100%!important;height:100%!important;object-fit:cover!important;object-position:center center!important;opacity:1!important;transform:scale(1.035)!important;filter:grayscale(.28) saturate(.76) sepia(.015) hue-rotate(190deg) brightness(1.02) contrast(1.12)!important;mix-blend-mode:luminosity!important}html body #zpWebHeroKat .zpWebHeroKat__videoWrap::before,html body #zpStronyKatowice #zpWebHeroKat .zpWebHeroKat__videoWrap::before{content:""!important;position:absolute!important;inset:-1px!important;z-index:2!important;pointer-events:none!important;background:linear-gradient(135deg,#020407 0%,#071426 44%,#102a4f 76%,#1c477a 100%)!important;mix-blend-mode:color!important;opacity:.76!important;border:0!important;box-shadow:none!important;filter:none!important}html body #zpWebHeroKat .zpWebHeroKat__videoWrap::after,html body #zpStronyKatowice #zpWebHeroKat .zpWebHeroKat__videoWrap::after{content:""!important;position:absolute!important;inset:-1px!important;z-index:3!important;pointer-events:none!important;background: radial-gradient(circle at 72% 20%,rgba(59,110,168,.28),transparent 34%),radial-gradient(circle at 18% 78%,rgba(16,42,79,.34),transparent 36%),linear-gradient(90deg,rgba(2,4,7,.80) 0%,rgba(7,20,38,.58) 38%,rgba(7,20,38,.28) 62%,rgba(2,4,7,.61) 100%),linear-gradient(180deg,rgba(3,7,13,.18) 0%,rgba(3,7,13,.34) 48%,rgba(3,5,9,.86) 100%)!important;mix-blend-mode:multiply!important;opacity:.88!important;border:0!important;box-shadow:none!important;filter:none!important}html body #zpWebHeroKat .zpWebHeroKat__screenAura,html body #zpStronyKatowice #zpWebHeroKat .zpWebHeroKat__screenAura,html body #zpWebHeroKat .zpWebHeroKat__visual::before,html body #zpWebHeroKat .zpWebHeroKat__visual::after,html body #zpWebHeroKat .zpWebHeroKat__visualStage::before,html body #zpWebHeroKat .zpWebHeroKat__visualStage::after,html body #zpStronyKatowice #zpWebHeroKat .zpWebHeroKat__visual::before,html body #zpStronyKatowice #zpWebHeroKat .zpWebHeroKat__visual::after,html body #zpStronyKatowice #zpWebHeroKat .zpWebHeroKat__visualStage::before,html body #zpStronyKatowice #zpWebHeroKat .zpWebHeroKat__visualStage::after{display:none!important;content:none!important;opacity:0!important;visibility:hidden!important;background:none!important;background-image:none!important;border:0!important;box-shadow:none!important;filter:none!important;-webkit-mask:none!important;mask:none!important}html body #zpWebHeroKat .zpWebHeroKat__visual,html body #zpWebHeroKat .zpWebHeroKat__visualStage,html body #zpStronyKatowice #zpWebHeroKat .zpWebHeroKat__visual,html body #zpStronyKatowice #zpWebHeroKat .zpWebHeroKat__visualStage{background:transparent!important;box-shadow:none!important;filter:none!important;-webkit-mask:none!important;mask:none!important;overflow:visible!important;isolation:auto!important}@media(max-width:760px){html body #zpWebHeroKat .zpWebHeroKat__video,html body #zpStronyKatowice #zpWebHeroKat .zpWebHeroKat__video{object-position:center 18%!important;opacity:1!important;transform:scale(1.02)!important;filter:grayscale(.30) saturate(.72) sepia(.015) hue-rotate(190deg) brightness(1.10) contrast(1.10)!important;mix-blend-mode:luminosity!important}html body #zpWebHeroKat .zpWebHeroKat__videoWrap::after,html body #zpStronyKatowice #zpWebHeroKat .zpWebHeroKat__videoWrap::after{background: radial-gradient(circle at 72% 20%,rgba(59,110,168,.28),transparent 34%),radial-gradient(circle at 18% 78%,rgba(16,42,79,.34),transparent 36%),linear-gradient(180deg,rgba(3,7,13,.58) 0%,rgba(7,20,38,.64) 52%,rgba(3,5,9,.86) 100%)!important;opacity:.76!important}}</style>
  <?php
}
add_action('wp_head','zp_suite_2326_strony_hero_video_parity_css',PHP_INT_MAX);
add_action('wp_footer','zp_suite_2326_strony_hero_video_parity_css',PHP_INT_MAX);

/**
 * ZP Suite v2.2.328 — STRONY HERO: full-height brighter video mask.
 * Fixes the horizontal split/cut on /strony-internetowe-katowice/ by making the
 * dark video mask cover the full hero height instead of starting halfway down the mockup.
 */
function zp_suite_2327_strony_hero_full_height_mask_css(){
  if (is_admin()) { return; }
  // v2.2.492: selectors are all scoped to #zpStronyKatowice — print only on that page.
  if (function_exists('zp_suite_2232_is_strony_katowice_request') && !zp_suite_2232_is_strony_katowice_request()) { return; }
  ?>
  <style id="zp-suite-2327-strony-hero-full-height-mask">html body #zpStronyKatowice #zpWebHeroKat .zpWebHeroKat__videoWrap::after,html body #zpStronyKatowice .zpWebHeroKat .zpWebHeroKat__videoWrap::after{content:""!important;position:absolute!important;left:0!important;right:0!important;top:0!important;bottom:0!important;width:auto!important;height:auto!important;inset:0!important;z-index:3!important;pointer-events:none!important;opacity:1!important;mix-blend-mode:normal!important;border:0!important;box-shadow:none!important;filter:none!important;background: linear-gradient(180deg,rgba(3,5,9,.59) 0%,rgba(4,7,12,.51) 30%,rgba(4,7,12,.58) 58%,rgba(2,4,7,.75) 100% ),linear-gradient(90deg,rgba(2,4,7,.70) 0%,rgba(5,13,24,.46) 37%,rgba(5,13,24,.29) 62%,rgba(2,4,7,.61) 100% ),radial-gradient(ellipse at 74% 20%,rgba(59,110,168,.13) 0%,rgba(59,110,168,.04) 34%,transparent 62%),radial-gradient(ellipse at 22% 74%,rgba(16,42,79,.19) 0%,transparent 58%)!important}html body #zpStronyKatowice #zpWebHeroKat .zpWebHeroKat__videoWrap::before,html body #zpStronyKatowice .zpWebHeroKat .zpWebHeroKat__videoWrap::before{content:""!important;position:absolute!important;inset:0!important;z-index:2!important;pointer-events:none!important;opacity:.50!important;mix-blend-mode:color!important;background:linear-gradient(135deg,#020407 0%,#06101e 48%,#102a4f 100%)!important;border:0!important;box-shadow:none!important;filter:none!important}html body #zpStronyKatowice #zpWebHeroKat.zpWebHeroKat::after,html body #zpStronyKatowice .zpWebHeroKat::after{content:""!important;position:absolute!important;inset:0!important;z-index:-3!important;pointer-events:none!important;opacity:1!important;background: linear-gradient(180deg,rgba(3,5,9,.50) 0%,rgba(5,7,11,.45) 38%,rgba(3,5,9,.72) 100% ),linear-gradient(90deg,rgba(3,4,7,.70) 0%,rgba(5,7,11,.42) 48%,rgba(3,4,7,.62) 100% )!important}@media(max-width:760px){html body #zpStronyKatowice #zpWebHeroKat .zpWebHeroKat__videoWrap::after,html body #zpStronyKatowice .zpWebHeroKat .zpWebHeroKat__videoWrap::after{background: linear-gradient(180deg,rgba(3,5,9,.62) 0%,rgba(5,13,24,.58) 48%,rgba(2,4,7,.75) 100%),radial-gradient(ellipse at 50% 30%,rgba(16,42,79,.18) 0%,transparent 68%)!important}}</style>
  <?php
}
add_action('wp_head','zp_suite_2327_strony_hero_full_height_mask_css',PHP_INT_MAX);
add_action('wp_footer','zp_suite_2327_strony_hero_full_height_mask_css',PHP_INT_MAX);


/**
 * ZP Suite v2.2.564 — Portfolio cards: 16:9 transparent floating mockups.
 * Applies only to /strony-internetowe-katowice/ portfolio cards.
 */
function zp_suite_2564_portfolio_floating_mockups_css(){
  if (is_admin()) { return; }
  if (function_exists('zp_suite_2232_is_strony_katowice_request') && !zp_suite_2232_is_strony_katowice_request()) { return; }
  ?>
  <style id="zp-suite-2564-portfolio-floating-mockups">
    html body #zpStronyKatowice .zpSSCard--portfolio .zpSSCard__visual{
      overflow:visible!important;
      align-self:stretch!important;
      display:flex!important;
      align-items:center!important;
      justify-content:center!important;
      min-width:0!important;
      isolation:isolate!important;
    }
    html body #zpStronyKatowice .zpSSCard--portfolio .zpSSCard__mock--portfolio{
      width:min(58vw,980px)!important;
      max-width:980px!important;
      aspect-ratio:16/9!important;
      min-height:0!important;
      height:auto!important;
      margin:0!important;
      padding:0!important;
      background:transparent!important;
      border:0!important;
      box-shadow:none!important;
      overflow:visible!important;
      transform:translate3d(7%,0,0) scale(1.08)!important;
      filter:drop-shadow(0 36px 42px rgba(0,0,0,.28)) drop-shadow(0 12px 18px rgba(0,0,0,.18))!important;
      animation:zpPortfolioMockFloat564 7.2s ease-in-out infinite!important;
      will-change:transform!important;
    }
    html body #zpStronyKatowice .zpSSCard--portfolio:nth-of-type(2n) .zpSSCard__mock--portfolio{animation-duration:8.4s!important;animation-delay:-1.6s!important;transform:translate3d(5%,2%,0) scale(1.05)!important}
    html body #zpStronyKatowice .zpSSCard--portfolio:nth-of-type(3n) .zpSSCard__mock--portfolio{animation-duration:7.8s!important;animation-delay:-2.4s!important;transform:translate3d(8%,-1%,0) scale(1.07)!important}
    html body #zpStronyKatowice .zpSSCard--portfolio .zpSSCard__mock--portfolio img{
      display:block!important;
      width:100%!important;
      height:100%!important;
      max-width:none!important;
      object-fit:contain!important;
      object-position:center center!important;
      background:transparent!important;
      border:0!important;
      border-radius:0!important;
      box-shadow:none!important;
      transform:none!important;
      filter:none!important;
    }
    html body #zpStronyKatowice .zpSSCard--portfolio:hover .zpSSCard__mock--portfolio{
      animation-play-state:paused!important;
      transform:translate3d(4%,-2%,0) scale(1.115)!important;
      filter:drop-shadow(0 44px 52px rgba(0,0,0,.32)) drop-shadow(0 16px 24px rgba(0,0,0,.20))!important;
    }
    @keyframes zpPortfolioMockFloat564{
      0%,100%{translate:0 0;rotate:-.15deg}
      50%{translate:0 -14px;rotate:.22deg}
    }
    @media(max-width:1180px){
      html body #zpStronyKatowice .zpSSCard--portfolio .zpSSCard__mock--portfolio{width:min(64vw,820px)!important;transform:translate3d(4%,0,0) scale(1.02)!important}
    }
    @media(max-width:880px){
      html body #zpStronyKatowice .zpSSCard--portfolio .zpSSCard__visual{margin-top:18px!important}
      html body #zpStronyKatowice .zpSSCard--portfolio .zpSSCard__mock--portfolio{
        width:min(112vw,760px)!important;
        max-width:none!important;
        transform:translate3d(0,0,0) scale(1)!important;
        filter:drop-shadow(0 26px 30px rgba(0,0,0,.26))!important;
        animation-duration:6.8s!important;
      }
      html body #zpStronyKatowice .zpSSCard--portfolio:hover .zpSSCard__mock--portfolio{transform:translate3d(0,-1%,0) scale(1.025)!important}
    }
    @media(prefers-reduced-motion:reduce){
      html body #zpStronyKatowice .zpSSCard--portfolio .zpSSCard__mock--portfolio{animation:none!important;will-change:auto!important}
    }
  </style>
  <?php
}
add_action('wp_head','zp_suite_2564_portfolio_floating_mockups_css',PHP_INT_MAX);
add_action('wp_footer','zp_suite_2564_portfolio_floating_mockups_css',PHP_INT_MAX);

/**
 * ZP Suite v2.2.570 — Portfolio mockups: keep v2.2.566 sizes, force only vertical lowering.
 * No scaling/alpha-trim changes here: this patch only moves the right-side mockups 40px down.
 */
function zp_suite_2570_portfolio_only_lower_mockups_css(){
  if (is_admin()) { return; }
  if (function_exists('zp_suite_2232_is_strony_katowice_request') && !zp_suite_2232_is_strony_katowice_request()) { return; }
  ?>
  <style id="zp-suite-2570-portfolio-only-lower-mockups">
    html body #zpStronyKatowice .zpSSCard--portfolio .zpSSCard__visual{
      position:relative!important;
      top:40px!important;
      transform:translate3d(0,40px,0)!important;
      padding-top:0!important;
      margin-top:0!important;
      margin-bottom:40px!important;
    }
    html body #zpStronyKatowice .zpSSCard--portfolio .zpSSCard__mock--portfolio{
      margin-top:40px!important;
    }
    html body #zpStronyKatowice .zpSSCard--portfolio:nth-of-type(2n) .zpSSCard__mock--portfolio,
    html body #zpStronyKatowice .zpSSCard--portfolio:nth-of-type(3n) .zpSSCard__mock--portfolio{
      margin-top:40px!important;
    }
    @media(max-width:880px){
      html body #zpStronyKatowice .zpSSCard--portfolio .zpSSCard__visual{
        top:26px!important;
        transform:translate3d(0,26px,0)!important;
        margin-bottom:26px!important;
      }
      html body #zpStronyKatowice .zpSSCard--portfolio .zpSSCard__mock--portfolio{
        margin-top:26px!important;
      }
    }
  </style>
  <script id="zp-suite-2570-portfolio-only-lower-mockups-js">
  (function(){
    if(!document.body || !document.getElementById('zpStronyKatowice')) return;
    function lower(){
      document.querySelectorAll('#zpStronyKatowice .zpSSCard--portfolio .zpSSCard__visual').forEach(function(el){
        el.style.setProperty('position','relative','important');
        el.style.setProperty('top','40px','important');
        el.style.setProperty('transform','translate3d(0,40px,0)','important');
        el.style.setProperty('margin-bottom','40px','important');
      });
      document.querySelectorAll('#zpStronyKatowice .zpSSCard--portfolio .zpSSCard__mock--portfolio').forEach(function(el){
        el.style.setProperty('margin-top','40px','important');
      });
    }
    lower();
    window.addEventListener('load', lower, {once:true});
    setTimeout(lower, 250);
    setTimeout(lower, 900);
  })();
  </script>
  <?php
}
add_action('wp_head','zp_suite_2570_portfolio_only_lower_mockups_css',PHP_INT_MAX);
add_action('wp_footer','zp_suite_2570_portfolio_only_lower_mockups_css',PHP_INT_MAX);



/**
 * ZP Suite v2.2.571 — Portfolio mockups: 10% smaller + smoother alpha edges.
 * Base: v2.2.570. Keeps the existing lower position, only trims size by 10% and softens rough image edges.
 */
function zp_suite_2571_portfolio_10_smaller_smooth_edges_css(){
  if (is_admin()) { return; }
  if (function_exists('zp_suite_2232_is_strony_katowice_request') && !zp_suite_2232_is_strony_katowice_request()) { return; }
  ?>
  <style id="zp-suite-2571-portfolio-10-smaller-smooth-edges">
    @media (min-width:981px){
      html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--ap{--zpPortfolioMockScale:.91125!important;--zpPortfolioMockHoverScale:.92745!important}
      html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--siemianowski{--zpPortfolioMockScale:.9477!important;--zpPortfolioMockHoverScale:.9639!important}
      html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--gravia{--zpPortfolioMockScale:.91125!important;--zpPortfolioMockHoverScale:.92745!important}
      html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--shothome{--zpPortfolioMockScale:.91125!important;--zpPortfolioMockHoverScale:.92745!important}
      html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--proscarves{--zpPortfolioMockScale:.83835!important;--zpPortfolioMockHoverScale:.85455!important}
      html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--krawiec{--zpPortfolioMockScale:.83835!important;--zpPortfolioMockHoverScale:.85455!important}
      html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--papeterio{--zpPortfolioMockScale:.729!important;--zpPortfolioMockHoverScale:.7452!important}
      html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--bransoletka{--zpPortfolioMockScale:.729!important;--zpPortfolioMockHoverScale:.7452!important}
      html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--rutpoz{--zpPortfolioMockScale:.729!important;--zpPortfolioMockHoverScale:.7452!important}
    }

    html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--portfolio:not(.zpSSCard--portfolioCta) .zpSSCard__mock--portfolio,
    html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--portfolio:not(.zpSSCard--portfolioCta):hover .zpSSCard__mock--portfolio,
    html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--portfolio:not(.zpSSCard--portfolioCta).isActive .zpSSCard__mock--portfolio,
    html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--portfolio:not(.zpSSCard--portfolioCta).isVisible .zpSSCard__mock--portfolio{
      background:transparent!important;
      border:0!important;
      box-shadow:none!important;
      overflow:visible!important;
    }

    html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--portfolio:not(.zpSSCard--portfolioCta) .zpSSCard__mock--portfolio::before,
    html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--portfolio:not(.zpSSCard--portfolioCta) .zpSSCard__mock--portfolio::after{
      display:none!important;
      content:none!important;
    }

    html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--portfolio:not(.zpSSCard--portfolioCta) .zpSSCard__mock--portfolio img{
      filter:drop-shadow(0 28px 26px rgba(4,10,20,.20)) drop-shadow(0 9px 12px rgba(4,10,20,.14)) blur(.12px) contrast(1.006) saturate(1.006)!important;
      -webkit-filter:drop-shadow(0 28px 26px rgba(4,10,20,.20)) drop-shadow(0 9px 12px rgba(4,10,20,.14)) blur(.12px) contrast(1.006) saturate(1.006)!important;
      image-rendering:auto!important;
      backface-visibility:hidden!important;
      -webkit-backface-visibility:hidden!important;
      transform:translateZ(0)!important;
      -webkit-mask-image:linear-gradient(90deg,transparent 0%,rgba(0,0,0,.42) 1.15%,#000 3.6%,#000 96.4%,rgba(0,0,0,.42) 98.85%,transparent 100%),linear-gradient(180deg,transparent 0%,rgba(0,0,0,.42) 1.15%,#000 3.6%,#000 96.4%,rgba(0,0,0,.42) 98.85%,transparent 100%)!important;
      mask-image:linear-gradient(90deg,transparent 0%,rgba(0,0,0,.42) 1.15%,#000 3.6%,#000 96.4%,rgba(0,0,0,.42) 98.85%,transparent 100%),linear-gradient(180deg,transparent 0%,rgba(0,0,0,.42) 1.15%,#000 3.6%,#000 96.4%,rgba(0,0,0,.42) 98.85%,transparent 100%)!important;
      -webkit-mask-composite:source-in!important;
      mask-composite:intersect!important;
      -webkit-mask-repeat:no-repeat!important;
      mask-repeat:no-repeat!important;
      -webkit-mask-size:100% 100%!important;
      mask-size:100% 100%!important;
    }

    @media (max-width:980px){
      html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--portfolio:not(.zpSSCard--portfolioCta) .zpSSCard__mock--portfolio{
        transform:translate3d(0,calc(var(--move,0px) + 40px),0) rotate(var(--rot,0deg)) scale(.729)!important;
      }
      html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--portfolio:not(.zpSSCard--portfolioCta):hover .zpSSCard__mock--portfolio,
      html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--portfolio:not(.zpSSCard--portfolioCta).isActive .zpSSCard__mock--portfolio,
      html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--portfolio:not(.zpSSCard--portfolioCta).isVisible .zpSSCard__mock--portfolio{
        transform:translate3d(0,calc(var(--move,0px) + 35px),0) rotate(var(--rot,0deg)) scale(.747)!important;
      }
      html body #zpStronyKatowice #zpPortfolioReveal .zpSSCard--portfolio:not(.zpSSCard--portfolioCta) .zpSSCard__mock--portfolio img{
        filter:drop-shadow(0 22px 22px rgba(4,10,20,.18)) drop-shadow(0 8px 10px rgba(4,10,20,.12)) blur(.10px) contrast(1.004) saturate(1.004)!important;
        -webkit-filter:drop-shadow(0 22px 22px rgba(4,10,20,.18)) drop-shadow(0 8px 10px rgba(4,10,20,.12)) blur(.10px) contrast(1.004) saturate(1.004)!important;
      }
    }
  </style>
  <?php
}
add_action('wp_head','zp_suite_2571_portfolio_10_smaller_smooth_edges_css',PHP_INT_MAX);
add_action('wp_footer','zp_suite_2571_portfolio_10_smaller_smooth_edges_css',PHP_INT_MAX);

/**
 * ZP Suite v2.2.573 — Home showcase: Meta Ads mockup + contact copy space.
 */
function zp_suite_2573_home_showcase_meta_contact_css(){
  if (is_admin() || (!is_front_page() && !is_home())) { return; }
  ?>
  <style id="zp-suite-2573-home-showcase-meta-contact">
    html body #zpShowcaseServices .zpSSCard--ads .zpSSCard__mock--ads{
      inset:-4% -7% -7% -3%!important;
      border-radius:0!important;
      background:transparent!important;
      border:0!important;
      box-shadow:none!important;
      overflow:visible!important;
      transform:translate3d(0,calc(var(--move,0px) * .72),0) rotate(.35deg)!important;
      animation:zpSSMetaAdsFloat573 6.8s ease-in-out infinite!important;
    }
    html body #zpShowcaseServices .zpSSCard--ads .zpSSCard__mock--ads:before,
    html body #zpShowcaseServices .zpSSCard--ads .zpSSCard__mock--ads:after{
      display:none!important;
      content:none!important;
    }
    html body #zpShowcaseServices .zpSSCard--ads .zpSSCard__mock--ads img{
      width:100%!important;
      height:100%!important;
      object-fit:contain!important;
      object-position:center!important;
      border-radius:0!important;
      background:transparent!important;
      transform:translateZ(22px) scale(1.04)!important;
      filter:drop-shadow(0 34px 40px rgba(2,8,20,.26)) drop-shadow(0 12px 18px rgba(2,8,20,.16))!important;
      -webkit-filter:drop-shadow(0 34px 40px rgba(2,8,20,.26)) drop-shadow(0 12px 18px rgba(2,8,20,.16))!important;
      image-rendering:auto!important;
    }
    html body #zpShowcaseServices .zpSSCard--ads:hover .zpSSCard__mock--ads{
      transform:translate3d(0,calc((var(--move,0px) * .72) - 8px),0) rotate(.05deg) scale(1.01)!important;
    }
    html body #zpShowcaseServices .zpSSCard--ads:hover .zpSSCard__mock--ads img{
      transform:translateZ(30px) scale(1.065)!important;
    }

    html body #zpShowcaseServices .zpSSCard--contact .zpSSCard__content{
      grid-template-columns:minmax(430px,.56fr) minmax(340px,.44fr)!important;
      gap:clamp(18px,2.4vw,46px)!important;
    }
    html body #zpShowcaseServices .zpSSCard--contact .zpSSCard__copy{
      max-width:660px!important;
      width:100%!important;
    }
    html body #zpShowcaseServices .zpSSCard--contact .zpSSCard__desc{
      max-width:620px!important;
    }
    html body #zpShowcaseServices .zpSSCard--contact .zpSSCard__h3{
      max-width:680px!important;
    }
    html body #zpShowcaseServices .zpSSCard--contact .zpSSCard__visual{
      justify-content:flex-end!important;
      min-width:0!important;
    }
    html body #zpShowcaseServices .zpSSCard--contact .zpSSCard__mock--contact{
      left:-8%!important;
      right:-7%!important;
    }

    @keyframes zpSSMetaAdsFloat573{
      0%,100%{transform:translate3d(0,calc(var(--move,0px) * .72),0) rotate(.35deg)}
      50%{transform:translate3d(0,calc((var(--move,0px) * .72) - 14px),0) rotate(-.15deg)}
    }
    @media(max-width:1100px){
      html body #zpShowcaseServices .zpSSCard--contact .zpSSCard__content{
        grid-template-columns:minmax(0,1fr)!important;
      }
      html body #zpShowcaseServices .zpSSCard--contact .zpSSCard__copy,
      html body #zpShowcaseServices .zpSSCard--contact .zpSSCard__desc,
      html body #zpShowcaseServices .zpSSCard--contact .zpSSCard__h3{
        max-width:100%!important;
      }
    }
    @media(max-width:880px){
      html body #zpShowcaseServices .zpSSCard--ads .zpSSCard__mock--ads{
        inset:0 -8% -4% -8%!important;
        transform:translate3d(0,calc(var(--move,0px) * .5),0) rotate(.2deg)!important;
      }
      html body #zpShowcaseServices .zpSSCard--ads .zpSSCard__mock--ads img{
        transform:translateZ(0) scale(1.02)!important;
        filter:drop-shadow(0 22px 28px rgba(2,8,20,.22))!important;
        -webkit-filter:drop-shadow(0 22px 28px rgba(2,8,20,.22))!important;
      }
    }
  </style>
  <?php
}
add_action('wp_head','zp_suite_2573_home_showcase_meta_contact_css',PHP_INT_MAX);

// v2.2.670 — globalny flat pass formularzy kontaktowych.
require_once ZP_SUITE_PATH . 'includes/front-fixes-302.php';

// v2.2.672 — FAQ: kontener 1750 px i navy gradient na hoverach.
require_once ZP_SUITE_PATH . 'includes/front-fixes-303.php';

// v2.2.776 — karta kontaktu: logo pod cytatem zespołu, niżej zdjęcie zespołu na mobile.
require_once ZP_SUITE_PATH . 'includes/front-fixes-304.php';

// v2.2.777 — nowe zdjęcie brandingu (Zgórecki) na home i w case study Logo i branding Katowice.
require_once ZP_SUITE_PATH . 'includes/front-fixes-305.php';

// v2.2.781 — popup portfolio logo: kwadratowa realizacja + czytelniejszy układ oraz nowe projekty brandingowe.
require_once ZP_SUITE_PATH . 'includes/front-fixes-306.php';

// v2.2.782 — dopracowany, równy modal brandingu + kwadratowe case studies i nowe projekty na /realizacje/.
require_once ZP_SUITE_PATH . 'includes/front-fixes-307.php';

// v2.2.790 — home hero: desktop letters before Cookiebot, native mobile gradient and staged entrance.
require_once ZP_SUITE_PATH . 'includes/front-fixes-308.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-309.php';
require_once ZP_SUITE_PATH . 'includes/front-fixes-310.php';
// v2.2.791–795 — home hero: szybkie wejście mobile (bez czekania na CSS, bez filtrów), sygnet ZP (małe pliki), sylwetki zespołu nad kickerem, CTA/chipy desktop, gradient, szybki banner zgód.
require_once ZP_SUITE_PATH . 'includes/front-fixes-311.php';
// v2.2.798 — mobile sticky first-paint + first-paint home hero positioning (no desktop robot jump).
require_once ZP_SUITE_PATH . 'includes/front-fixes-312.php';
// v2.2.797 — smoother mobile header entrance, deferred sticky dock reveal and darker robot for mobile readability.
require_once ZP_SUITE_PATH . 'includes/front-fixes-313.php';
// v2.2.799 — robot higher on desktop/mobile and smaller ZP signet in the background.
require_once ZP_SUITE_PATH . 'includes/front-fixes-314.php';
// v2.2.800 — darker readability fades on mobile and full desktop stage lift with wider H1 area.
require_once ZP_SUITE_PATH . 'includes/front-fixes-315.php';
// v2.2.801 — home hero motion/glass polish and smoother mobile header divider entrance.
require_once ZP_SUITE_PATH . 'includes/front-fixes-316.php';
// v2.2.802 — mobile header-first sequencing, desktop robot/crew refinement and transparent mobile review glass.
require_once ZP_SUITE_PATH . 'includes/front-fixes-317.php';
// v2.2.803 — hard mobile header-first gate and mobile crew another 30px higher.
require_once ZP_SUITE_PATH . 'includes/front-fixes-318.php';
// v2.2.804 — final crew positioning: mobile 20 px lower, desktop 30 px higher.
require_once ZP_SUITE_PATH . 'includes/front-fixes-319.php';
// v2.2.805 — authoritative crew positioning + desktop copy 50 px lower.
require_once ZP_SUITE_PATH . 'includes/front-fixes-320.php';
// v2.2.806 — crew visually aligned to the kicker on desktop/mobile.
require_once ZP_SUITE_PATH . 'includes/front-fixes-321.php';
// v2.2.808 — immediate-after-first-paint Spline start + fast poster handoff.
require_once ZP_SUITE_PATH . 'includes/front-fixes-322.php';
// v2.2.809 — desktop Lighthouse pass: zero-layout header/theme sync + lazy below-fold paint.
require_once ZP_SUITE_PATH . 'includes/front-fixes-323.php';

// Integrated SEO repair module, based on the supplied Suite templates.
require_once ZP_SUITE_PATH . 'includes/ultimate-seo/bootstrap.php';

// v2.3.0 — keyword plan: Rank Math values, nationwide service pages, redirects, schema and indexation.
require_once ZP_SUITE_PATH . 'includes/seo-plan/bootstrap.php';

// v2.4.0 — minified stylesheets (assets/*.min.css, tools/minify_css.py).
require_once ZP_SUITE_PATH . 'includes/css-min.php';
