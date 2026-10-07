<!-- =========================================================
ZAPROJEKTOWANI — HEADER / CLEAN INLINE HTML + CSS + JS v9.0 LIGHT MENU
Zaprojektowani Suite template / v0.2 assets moved to /assets/css/blocks and /assets/js/blocks

- Jeden czysty plik do wklejenia w Zaprojektowani Suite template / v0.2 assets moved to /assets/css/blocks and /assets/js/blocks
- Lucide zakładamy jako globalnie załadowane na stronie
- Desktop mega menu: portal do <body>, full bleed 100vw, bez ucinania
- Sticky/fixed safe mode: header trzyma się góry + JS spacer zapobiega przeskokowi
- Naprawa białej szczeliny między headerem a mega menu
- Jasna wersja mega menu + dropdownów, sticky/fixed safe mode
========================================================= -->


<?php
/*
 * ZP Suite v0.2.2 FOUC fix:
 * Header CSS is printed directly before header markup so the browser never paints
 * the raw logo/menu while external CSS is still loading or being optimized by cache plugins.
 */
$zp_header_css_file = defined('ZP_SUITE_PATH') ? ZP_SUITE_PATH . 'assets/css/blocks/header.css' : '';

/*
 * v2.2.552: service desktop header no-FOUC hard fix.
 * The service pages must not wait for the async media=print header.css swap, because
 * the full CSS arrival changes final header metrics/icons/divider after first paint.
 * Use a normal stylesheet link on the three service landing pages so the first paint
 * already uses the final header cascade. Other pages keep the async path.
 */
$zp_header_req_uri_552 = isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '';
$zp_header_is_service_page_552 = (bool) preg_match('#/(strony-internetowe-katowice|sklepy-internetowe-katowice|logo-branding-katowice|kampanie-reklamowe)(/|\?|$)#', $zp_header_req_uri_552);
$zp_header_is_clean_sync_559 = !empty($zp_header_is_service_page_552) || $zp_header_req_uri_552 === '/' || (bool) preg_match('#/(kontakt|wiedza)(/|\?|$)#', $zp_header_req_uri_552) || is_category();

if (empty($GLOBALS['zp_suite_header_css_printed_in_head']) && $zp_header_css_file && file_exists($zp_header_css_file)) :
  $GLOBALS['zp_suite_header_css_printed_in_head'] = true;
  /*
   * v2.2.505: instead of inlining the full ~570 KB header.css here (uncached, in every
   * request's HTML), emit the small critical header CSS inline for instant first paint,
   * then load the full sheet as a cached external <link> at this exact DOM position.
   * Same position => identical cascade order vs later header-override styles => 1:1 look.
   */
  $zp_header_css_href = function_exists('zp_suite_asset_version')
    ? add_query_arg('ver', zp_suite_asset_version('assets/css/blocks/header.css'), ZP_SUITE_URL . 'assets/css/blocks/header.css')
    : ZP_SUITE_URL . 'assets/css/blocks/header.css';
?>
<?php if (function_exists('zp_suite_header_critical_css')) : ?>
<style id="zp-suite-header-inline-critical"><?php echo zp_suite_header_critical_css(); ?>
/* v2.2.560 — critical center lock for tablet/mobile logo, before header.css finishes loading. */
@media(max-width:1100px){
  html body #zpNewNav .zpNewNav__mobileBar{position:relative!important;width:100%!important;max-width:none!important;margin:0!important;padding-left:clamp(22px,4.6vw,52px)!important;padding-right:clamp(22px,4.6vw,52px)!important;display:flex!important;align-items:center!important;justify-content:space-between!important;}
  html body #zpNewNav .zpNewNav__mobileBrand,html body #zpNewNav.zpNewNav--heroOverlay .zpNewNav__mobileBrand,html body #zpNewNav.is-scrolled .zpNewNav__mobileBrand,html body.zp-nav-scrolled #zpNewNav .zpNewNav__mobileBrand{position:absolute!important;left:50%!important;top:50%!important;right:auto!important;bottom:auto!important;width:76px!important;height:58px!important;min-width:76px!important;min-height:58px!important;max-width:76px!important;max-height:58px!important;margin:0!important;padding:0!important;display:flex!important;align-items:center!important;justify-content:center!important;transform:translate3d(-50%,-50%,0)!important;translate:none!important;}
  html body #zpNewNav .zpNewNav__mobileLogo,html body #zpNewNav .zpNewNav__mobileLogo--light,html body #zpNewNav .zpNewNav__mobileLogo--dark{position:absolute!important;left:50%!important;top:50%!important;right:auto!important;bottom:auto!important;width:auto!important;height:44px!important;min-height:44px!important;max-height:44px!important;max-width:62px!important;margin:0!important;padding:0!important;object-fit:contain!important;object-position:center!important;transform:translate3d(-50%,-50%,0)!important;translate:none!important;scale:1!important;filter:none!important;box-shadow:none!important;}
  html body #zpNewNav .zpNewNav__mobileActions{margin-left:auto!important;}
}

</style>
<?php endif; ?>
<link rel="preload" as="style" id="zp-suite-header-full-preload" href="<?php echo esc_url($zp_header_css_href); ?>">
<?php if (!empty($zp_header_is_clean_sync_559)) : ?>
<link rel="stylesheet" id="zp-suite-header-full-css" href="<?php echo esc_url($zp_header_css_href); ?>" media="all">
<?php else : ?>
<link rel="stylesheet" id="zp-suite-header-full-css" href="<?php echo esc_url($zp_header_css_href); ?>" media="print" onload="this.media='all';this.onload=null">
<noscript><link rel="stylesheet" href="<?php echo esc_url($zp_header_css_href); ?>"></noscript>
<?php endif; ?>
<?php endif; ?>

<?php
$zp_logo_light_default = 'https://zaprojektowani.com/wp-content/uploads/2026/06/zp_sygnet-150x150.webp';
$zp_logo_dark_default = 'https://zaprojektowani.com/wp-content/uploads/2026/06/zp_sygnet_ciemny-150x150.webp';
$zp_logo_light = $zp_logo_light_default; // static / ciemny header na górze strony
$zp_logo_dark = $zp_logo_dark_default; // sticky / jasny header po scrollu

/* v1.8.22: jeśli w bazie zostały stare / odwrócone URL-e logo, naprawiamy je na froncie bez ręcznego klikania w panelu. */
if (empty($zp_logo_light)) {
  $zp_logo_light = $zp_logo_light_default;
}
if (strpos((string)$zp_logo_dark, 'logo_transparent') !== false || strpos((string)$zp_logo_dark, 'transparent') !== false) {
  $zp_logo_dark = $zp_logo_dark_default;
}
$zp_header_overlay_class = (
  is_front_page()
  || is_home()
  || is_singular('post')
  || zp_suite_service_kind() !== ''
  || is_page('kontakt')
  || is_page('wiedza')
  || (isset($_SERVER['REQUEST_URI']) && strpos((string) $_SERVER['REQUEST_URI'], '/wiedza') !== false)
  || is_page('logo-branding')
  || is_page('studio-wyceny')
  || (isset($_SERVER['REQUEST_URI']) && strpos((string) $_SERVER['REQUEST_URI'], '/kampanie-reklamowe') !== false)
  || (is_singular() && has_shortcode((string) get_post_field('post_content', get_queried_object_id()), 'zp_studio_wyceny'))
  || (is_singular() && has_shortcode((string) get_post_field('post_content', get_queried_object_id()), 'zp_studio_wyceny_cms'))
  || (is_singular() && has_shortcode((string) get_post_field('post_content', get_queried_object_id()), 'zp_logo_branding_katowice'))
  || (is_singular() && has_shortcode((string) get_post_field('post_content', get_queried_object_id()), 'zp_page_logo_branding_katowice'))
  || (is_singular() && has_shortcode((string) get_post_field('post_content', get_queried_object_id()), 'zp_sklepy_internetowe_katowice'))
  || (is_singular() && has_shortcode((string) get_post_field('post_content', get_queried_object_id()), 'zp_page_sklepy_katowice'))
  // v2.2.526: strony-katowice was only matched by its exact page slug above; without
  // these shortcode fallbacks a strony page on a different slug rendered the light
  // (default) header server-side, then flipped dark once header.js ran = visible delay.
  || (is_singular() && has_shortcode((string) get_post_field('post_content', get_queried_object_id()), 'zp_strony_internetowe_katowice'))
  || (is_singular() && has_shortcode((string) get_post_field('post_content', get_queried_object_id()), 'zp_page_strony_katowice'))
  || (is_singular() && has_shortcode((string) get_post_field('post_content', get_queried_object_id()), 'zp_katowice_page'))
) ? ' zpNewNav--heroOverlay' : '';
$zp_header_service_class = !empty($zp_header_is_service_page_552) ? ' zpNewNav--serviceHero' : '';
/* v2.2.558: one-divider / transparent hero header mode reused from service pages.
 * Apply also to /kontakt and the Wiedza index, matching the final service divider behavior.
 * Do not apply to single posts, because article reading pages have their own content background. */
$zp_header_is_clean_hero_558 = !empty($zp_header_is_service_page_552)
  || is_front_page()
  || is_home()
  || is_page('kontakt')
  || is_page('wiedza')
  || (isset($_SERVER['REQUEST_URI']) && preg_match('#/(kontakt|wiedza)(/|\?|$)#', (string) $_SERVER['REQUEST_URI']));
$zp_header_clean_hero_class = !empty($zp_header_is_clean_hero_558) ? ' zpNewNav--cleanHero' : '';
$zp_header_archive_class = is_category() ? ' zpNewNav--knowledgeArchive' : '';
$zp_header_cta_text = zp_suite_opt('header.cta_text', 'Szybka wycena');
$zp_header_cta_url = zp_suite_opt('header.cta_url', '/studio-wyceny/');
$zp_header_contact_url = zp_suite_opt('header.contact_url', '/kontakt/');
$zp_logo_desktop_h = (int) zp_suite_opt('header.logo_desktop_h', 36);
$zp_logo_mobile_h = (int) zp_suite_opt('header.logo_mobile_h', 32);
$zp_logo_mobile_x = (int) zp_suite_opt('header.logo_mobile_x', -8);
$zp_mobile_glass_raw = zp_suite_opt('header.mobile_glass_opacity', '0.72');
$zp_mobile_glass = is_numeric($zp_mobile_glass_raw) ? max(.82, min(.98, (float)$zp_mobile_glass_raw)) : .88;
$zp_header_line_width = (int) zp_suite_opt('header.header_line_width', 1850);
$zp_header_line_opacity_raw = zp_suite_opt('header.header_line_opacity', '0.38');
$zp_header_line_opacity = is_numeric($zp_header_line_opacity_raw) ? max(0, min(1, (float)$zp_header_line_opacity_raw)) : .38;
$zp_header_line_scrolled_opacity_raw = zp_suite_opt('header.header_line_scrolled_opacity', '0.24');

$zp_header_line_scrolled_opacity = is_numeric($zp_header_line_scrolled_opacity_raw) ? max(0, min(1, (float)$zp_header_line_scrolled_opacity_raw)) : .24;

if (!function_exists('zp_suite_header_inline_icon')) {
  function zp_suite_header_inline_icon($name, $class = '') {
    $class_attr = $class ? ' class="' . esc_attr($class) . '"' : '';
    $attrs = ' xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"' . $class_attr;
    $paths = array(
      'chevron-down' => '<path d="m6 9 6 6 6-6"/>',
      'message-circle' => '<path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/>',
      'messages-square' => '<path d="M14 9a2 2 0 0 1-2 2H6l-4 4V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2Z"/><path d="M18 9h2a2 2 0 0 1 2 2v10l-4-4h-6a2 2 0 0 1-2-2v-1"/>',
      'phone' => '<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.12 4.18 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.12.9.32 1.77.59 2.61a2 2 0 0 1-.45 2.11L8 9.69a16 16 0 0 0 6.31 6.31l1.25-1.25a2 2 0 0 1 2.11-.45c.84.27 1.71.47 2.61.59A2 2 0 0 1 22 16.92Z"/>',
      'arrow-up-right' => '<path d="M7 17 17 7"/><path d="M7 7h10v10"/>',
      'search' => '<path d="m21 21-4.34-4.34"/><circle cx="11" cy="11" r="8"/>'
    );
    $body = isset($paths[$name]) ? $paths[$name] : '';
    return '<svg' . $attrs . '>' . $body . '</svg>';
  }
}
?>



















<style id="zp-suite-header-admin-vars">
/* v2.2.775 — safe contact-icons patch.
   IMPORTANT: DOM/JS navigation markup is kept 1:1 with the known-good 2.2.773 header.
   Messenger is hidden only with CSS and WhatsApp is restyled with a pseudo-icon,
   so no menu link handlers or hit areas are changed. */
#zpNewNav a[aria-label="Messenger"]{display:none!important;}
#zpNewNav a[aria-label="WhatsApp"]{position:relative;}
#zpNewNav a[aria-label="WhatsApp"]>svg,
#zpNewNav a[aria-label="WhatsApp"]>i{display:none!important;}
#zpNewNav a[aria-label="WhatsApp"]::before{
  content:"";display:block;width:22px;height:22px;flex:0 0 22px;background:currentColor;pointer-events:none;
  -webkit-mask:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.198-.347.223-.644.074-.297-.148-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.009-.371-.011-.57-.011-.198 0-.52.074-.792.371-.272.297-1.04 1.016-1.04 2.479s1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.262.489 1.693.625.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.981.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.887-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.9 6.99c-.003 5.45-4.437 9.884-9.884 9.884m8.413-18.297A11.815 11.815 0 0 0 12.055 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.14 1.588 5.945L.056 24l6.3-1.654a11.882 11.882 0 0 0 5.695 1.45h.005c6.559 0 11.894-5.335 11.897-11.893a11.821 11.821 0 0 0-3.489-8.413Z'/%3E%3C/svg%3E") center/contain no-repeat;
  mask:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.198-.347.223-.644.074-.297-.148-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.009-.371-.011-.57-.011-.198 0-.52.074-.792.371-.272.297-1.04 1.016-1.04 2.479s1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.262.489 1.693.625.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.981.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.887-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.9 6.99c-.003 5.45-4.437 9.884-9.884 9.884m8.413-18.297A11.815 11.815 0 0 0 12.055 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.14 1.588 5.945L.056 24l6.3-1.654a11.882 11.882 0 0 0 5.695 1.45h.005c6.559 0 11.894-5.335 11.897-11.893a11.821 11.821 0 0 0-3.489-8.413Z'/%3E%3C/svg%3E") center/contain no-repeat;
}
#zpNewNav .zpNewNav__drawerFoot a[aria-label="WhatsApp"]::before{width:24px;height:24px;flex-basis:24px;}
html body.home,html body.front-page{background:#05070b!important;}
#zpNewNav{--zp-admin-logo-desktop-h:<?php echo max(18,min(96,$zp_logo_desktop_h)); ?>px;--zp-admin-logo-mobile-h:<?php echo max(22,min(90,$zp_logo_mobile_h)); ?>px;--zp-admin-logo-mobile-x:<?php echo $zp_logo_mobile_x; ?>px;--zp-admin-mobile-glass:<?php echo $zp_mobile_glass; ?>;--zp-header-line-width:<?php echo max(320,min(2400,$zp_header_line_width)); ?>px;--zp-header-line-opacity:0;--zp-header-line-scrolled-opacity:0;}
#zpNewNav .zpNewNav__logo,#zpNewNav.is-scrolled .zpNewNav__logo,#zpNewNav.is-mega-open .zpNewNav__logo{height:var(--zp-admin-logo-desktop-h)!important;min-height:var(--zp-admin-logo-desktop-h)!important;max-height:var(--zp-admin-logo-desktop-h)!important;box-shadow:none!important;filter:none!important;text-shadow:none!important;}
@media(max-width:1080px){#zpNewNav .zpNewNav__mobileLogo{height:var(--zp-admin-logo-mobile-h)!important;min-height:var(--zp-admin-logo-mobile-h)!important;max-height:var(--zp-admin-logo-mobile-h)!important;box-shadow:none!important;filter:none!important;text-shadow:none!important;}#zpNewNav.is-scrolled:not(.is-mega-open) .zpNewNav__shell{box-shadow:none!important;filter:none!important;}}
@media(max-width:1080px){html body #zpNewNav .zpNewNav__mobileBrand,html body #zpNewNav.zpNewNav--heroOverlay .zpNewNav__mobileBrand,html body #zpNewNav.is-scrolled .zpNewNav__mobileBrand,html body.zp-nav-scrolled #zpNewNav .zpNewNav__mobileBrand{position:absolute!important;left:50%!important;top:50%!important;width:76px!important;height:58px!important;min-width:76px!important;min-height:58px!important;max-width:76px!important;max-height:58px!important;margin:0!important;padding:0!important;display:flex!important;align-items:center!important;justify-content:center!important;transform:translate3d(-50%,-50%,0)!important}html body #zpNewNav .zpNewNav__mobileLogo,html body #zpNewNav .zpNewNav__mobileLogo--light,html body #zpNewNav .zpNewNav__mobileLogo--dark,html body #zpNewNav.zpNewNav--heroOverlay .zpNewNav__mobileLogo,html body #zpNewNav.is-scrolled .zpNewNav__mobileLogo,html body.zp-nav-scrolled #zpNewNav .zpNewNav__mobileLogo{position:absolute!important;left:50%!important;top:50%!important;right:auto!important;bottom:auto!important;width:auto!important;height:44px!important;min-height:44px!important;max-height:44px!important;max-width:62px!important;margin:0!important;padding:0!important;object-fit:contain!important;object-position:center center!important;transform:translate3d(-50%,-50%,0)!important;filter:none!important;box-shadow:none!important;scale:1!important;transition:opacity .16s ease!important}}
@media(min-width:1101px){#zpNewNav.zpNewNav--serviceHero,#zpNewNav.zpNewNav--serviceHero .zpNewNav__shell{background:transparent!important;background-color:transparent!important;background-image:none!important;border-bottom:0!important;box-shadow:none!important;filter:none!important;backdrop-filter:none!important;-webkit-backdrop-filter:none!important}#zpNewNav.zpNewNav--serviceHero .zpHeaderStaticDivider{display:block!important;position:absolute!important;left:50%!important;bottom:0!important;width:min(1850px,calc(100vw - 32px))!important;height:1px!important;transform:translateX(-50%)!important;opacity:.46!important;background:linear-gradient(90deg,transparent 0%,rgba(255,255,255,.08) 18%,rgba(255,255,255,.13) 50%,rgba(255,255,255,.08) 82%,transparent 100%)!important;transition:none!important;box-shadow:none!important}}

@media(min-width:1101px){#zpNewNav.zpNewNav--cleanHero.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open),#zpNewNav.zpNewNav--cleanHero.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__shell{background:transparent!important;background-color:transparent!important;background-image:none!important;border-bottom:0!important;box-shadow:none!important;filter:none!important;backdrop-filter:none!important;-webkit-backdrop-filter:none!important}#zpNewNav.zpNewNav--cleanHero.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpHeaderStaticDivider{display:block!important;position:absolute!important;left:50%!important;bottom:0!important;width:min(1850px,calc(100vw - 32px))!important;height:1px!important;transform:translateX(-50%)!important;pointer-events:none!important;z-index:5!important;opacity:.52!important;background:linear-gradient(90deg,transparent 0%,rgba(255,255,255,.10) 18%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.10) 82%,transparent 100%)!important;transition:none!important;box-shadow:none!important;border:0!important}#zpNewNav.zpNewNav--cleanHero.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open)::before,#zpNewNav.zpNewNav--cleanHero.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open)::after,#zpNewNav.zpNewNav--cleanHero.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__shell::before,#zpNewNav.zpNewNav--cleanHero.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__shell::after{content:none!important;display:none!important;opacity:0!important;background:none!important;border:0!important;box-shadow:none!important}}
@media(max-width:1100px){#zpNewNav .zpNewNav__mobileBar{position:relative!important;display:flex!important;align-items:center!important;justify-content:space-between!important}#zpNewNav .zpNewNav__mobileBrand{position:absolute!important;left:50%!important;top:50%!important;right:auto!important;bottom:auto!important;margin:0!important;transform:translate3d(-50%,-50%,0)!important}#zpNewNav .zpNewNav__mobileLogo,#zpNewNav .zpNewNav__mobileLogo--light,#zpNewNav .zpNewNav__mobileLogo--dark{position:absolute!important;left:50%!important;top:50%!important;margin:0!important;transform:translate3d(-50%,-50%,0)!important}#zpNewNav .zpNewNav__mobileActions{margin-left:auto!important}}
</style>
<style id="zp-suite-666-mobile-header-until-breakpoint">
/* v2.2.666 — one consistent tablet/crossover header for 981–1200px (replaces v2.2.562).
   First-paint metrics are IDENTICAL to the final wp_footer layer (front-fixes-299):
   88px bar, centered absolute brand, styled "Konsultacja" pill. Before this fix the
   1101–1200 band showed a completely unstyled call link and 981–1100 let a footer
   layer re-center the brand into a flex slot (logo drifting right of true center). */
@media (min-width:981px) and (max-width:1200px){
  html body #zpNewNav .zpNewNav__inner{display:none!important;visibility:hidden!important;height:0!important;min-height:0!important;max-height:0!important;overflow:hidden!important;}
  html body #zpNewNav .zpNewNav__mobileBar,
  html body #zpNewNav.zpNewNav--heroOverlay .zpNewNav__mobileBar,
  html body #zpNewNav.is-scrolled .zpNewNav__mobileBar,
  html body.zp-nav-scrolled #zpNewNav .zpNewNav__mobileBar{
    position:relative!important;display:flex!important;visibility:visible!important;opacity:1!important;
    width:min(1450px,calc(100vw - 2 * clamp(22px,4vw,42px)))!important;max-width:none!important;
    height:88px!important;min-height:88px!important;max-height:88px!important;
    margin:0 auto!important;padding:0!important;align-items:center!important;justify-content:space-between!important;gap:18px!important;
    transform:none!important;translate:none!important;
  }
  html body #zpNewNav .zpNewNav__mobileCall{
    display:inline-flex!important;align-items:center!important;justify-content:center!important;gap:8px!important;
    height:46px!important;min-height:46px!important;padding:0 18px!important;margin-left:0!important;
    border-radius:999px!important;border:1px solid rgba(7,20,38,.12)!important;
    background:rgba(255,255,255,.56)!important;color:#071426!important;
    font-size:13px!important;font-weight:720!important;letter-spacing:-.018em!important;line-height:1!important;white-space:nowrap!important;
    -webkit-backdrop-filter:blur(16px) saturate(130%)!important;backdrop-filter:blur(16px) saturate(130%)!important;
    transform:none!important;translate:none!important;flex:0 0 auto!important;
  }
  html body #zpNewNav .zpNewNav__mobileCall i,
  html body #zpNewNav .zpNewNav__mobileCall svg{display:block!important;width:15px!important;height:15px!important;stroke:currentColor!important;fill:none!important;}
  html body #zpNewNav .zpNewNav__mobileCall span{display:inline-block!important;}
  html body #zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled) .zpNewNav__mobileCall{
    color:#fff!important;background:rgba(255,255,255,.05)!important;border-color:rgba(255,255,255,.22)!important;
  }
  /* Studio Wyceny chooser: heroOverlay class on a WHITE header — pill must stay dark. */
  html body.zpbs-studio-page #zpNewNav .zpNewNav__mobileCall,
  html body #zpNewNav.zpNewNav--studioChooser .zpNewNav__mobileCall{
    color:#071426!important;background:rgba(255,255,255,.62)!important;border-color:rgba(7,17,31,.12)!important;
  }
  html body #zpNewNav .zpNewNav__mobileActions{margin-left:0!important;display:flex!important;align-items:center!important;justify-content:flex-end!important;flex:0 0 auto!important;}
  html body #zpNewNav .zpNewNav__burger{width:44px!important;min-width:44px!important;height:44px!important;min-height:44px!important;margin-right:0!important;display:grid!important;place-items:center!important;transform:none!important;translate:none!important;}
  html body #zpNewNav .zpNewNav__mobileBrand,
  html body #zpNewNav.zpNewNav--heroOverlay .zpNewNav__mobileBrand,
  html body #zpNewNav.is-scrolled .zpNewNav__mobileBrand,
  html body.zp-nav-scrolled #zpNewNav .zpNewNav__mobileBrand{
    position:absolute!important;left:50%!important;top:50%!important;right:auto!important;bottom:auto!important;
    width:76px!important;height:58px!important;min-width:76px!important;min-height:58px!important;max-width:76px!important;max-height:58px!important;
    margin:0!important;padding:0!important;display:flex!important;align-items:center!important;justify-content:center!important;
    flex:0 0 auto!important;
    transform:translate3d(-50%,-50%,0)!important;translate:none!important;transition:none!important;
  }
  html body #zpNewNav .zpNewNav__mobileLogo,
  html body #zpNewNav .zpNewNav__mobileLogo--light,
  html body #zpNewNav .zpNewNav__mobileLogo--dark,
  html body #zpNewNav.zpNewNav--heroOverlay .zpNewNav__mobileLogo,
  html body #zpNewNav.is-scrolled .zpNewNav__mobileLogo,
  html body.zp-nav-scrolled #zpNewNav .zpNewNav__mobileLogo{
    position:absolute!important;left:50%!important;top:50%!important;right:auto!important;bottom:auto!important;
    width:auto!important;height:44px!important;min-height:44px!important;max-height:44px!important;max-width:62px!important;
    margin:0!important;padding:0!important;object-fit:contain!important;object-position:center!important;
    transform:translate3d(-50%,-50%,0)!important;translate:none!important;scale:1!important;filter:none!important;box-shadow:none!important;transition:none!important;
  }
}
</style>
<?php if (is_category()) : ?>
<style id="zp-knowledge-archive-header-fix">
html body #zpNewNav.zpNewNav--knowledgeArchive:not(.is-mega-open),
html body #zpNewNav.zpNewNav--knowledgeArchive:not(.is-mega-open) .zpNewNav__shell{background:#fff!important;background-color:#fff!important;background-image:none!important;box-shadow:none!important;backdrop-filter:none!important;-webkit-backdrop-filter:none!important;}
html body #zpNewNav.zpNewNav--knowledgeArchive:not(.is-mega-open){border-bottom:1px solid rgba(7,17,31,.075)!important;}
</style>
<?php endif; ?>
<header class="zpNewNav<?php echo esc_attr($zp_header_overlay_class . $zp_header_service_class . $zp_header_clean_hero_class . $zp_header_archive_class); ?>" id="zpNewNav">
  <div class="zpNewNav__shell">
    <div class="zpNewNav__inner">
      <a class="zpNewNav__brand" href="/" aria-label="Zaprojektowani.com">
        <img class="zpNewNav__logo zpNewNav__logo--light" src="<?php echo esc_url($zp_logo_light); ?>" alt="Zaprojektowani.com" loading="eager" decoding="async" width="150" height="150" style="height:<?php echo max(18,min(96,$zp_logo_desktop_h)); ?>px!important;min-height:<?php echo max(18,min(96,$zp_logo_desktop_h)); ?>px!important;max-height:<?php echo max(18,min(96,$zp_logo_desktop_h)); ?>px!important;width:auto!important;">
        <img class="zpNewNav__logo zpNewNav__logo--dark" src="<?php echo esc_url($zp_logo_dark); ?>" alt="Zaprojektowani.com" loading="eager" decoding="async" width="150" height="150" style="height:<?php echo max(18,min(96,$zp_logo_desktop_h)); ?>px!important;min-height:<?php echo max(18,min(96,$zp_logo_desktop_h)); ?>px!important;max-height:<?php echo max(18,min(96,$zp_logo_desktop_h)); ?>px!important;width:auto!important;">
      </a>
      

<nav class="zpNewNav__desktop" aria-label="Menu główne">
        <ul class="zpNewNav__menu">
<?php
/* Suite 2.5.0: SEO-plan subpages in the existing mega menu and mobile menu. Shown only on the
   Polish site while the SEO plan is on and the page is published; the English site keeps its menu. */
$zp_menu_250 = static function (string $path): bool {
  return function_exists('zp_seo_plan_active') && zp_seo_plan_active() && function_exists('zp_seo_plan_is_en') && !zp_seo_plan_is_en()
    && function_exists('zp_seo_plan_link_is_live') && zp_seo_plan_link_is_live($path);
};
$zp_menu_250_item = static function (string $path, string $icon, string $title, string $desc) use ($zp_menu_250): string {
  if (!$zp_menu_250($path)) { return ''; }
  return '<a class="zpNewNav__megaLink" href="' . esc_url($path) . '"><span class="zpNewNav__megaIco"><i data-lucide="' . esc_attr($icon) . '"></i></span><span><span class="zpNewNav__megaTitle">' . esc_html($title) . '</span>'
    . ($desc !== '' ? '<span class="zpNewNav__megaDesc">' . esc_html($desc) . '</span>' : '') . '</span></a>';
};
$zp_menu_250_m = static function (string $path, string $icon, string $title, string $desc) use ($zp_menu_250): string {
  if (!$zp_menu_250($path)) { return ''; }
  return '<a href="' . esc_url($path) . '"><span class="zpNewNav__mSubIcon"><i data-lucide="' . esc_attr($icon) . '"></i></span><span><strong>' . esc_html($title) . '</strong><em>' . esc_html($desc) . '</em></span><span class="zpNewNav__mGo"><i data-lucide="arrow-up-right"></i></span></a>';
};
$zp_menu_250_industry = [
  ['/strony-internetowe-dla-kancelarii/', 'scale', 'Kancelarie i prawnicy', 'Specjalizacje, zespół i kontakt'],
  ['/strony-internetowe-dla-lekarzy/', 'stethoscope', 'Lekarze i gabinety', 'Usługi, cennik i rejestracja'],
  ['/strony-internetowe-dla-deweloperow/', 'building-2', 'Deweloperzy i inwestycje', 'Inwestycje, mieszkania i zapytania'],
];
$zp_menu_250_ind_desk = '';
$zp_menu_250_ind_mob = '';
foreach ($zp_menu_250_industry as $zp_ind) {
  $zp_menu_250_ind_desk .= $zp_menu_250_item($zp_ind[0], $zp_ind[1], $zp_ind[2], '');
  $zp_menu_250_ind_mob .= $zp_menu_250_m($zp_ind[0], $zp_ind[1], $zp_ind[2], $zp_ind[3]);
}
/* Suite 2.6.0: items that pointed to the same address as another item now lead to their own
   SEO-plan pages (only once that page is published; until then they keep their old link), the
   promo card shows logo, website or shop at random, and Mateusz stands above the bottom bar.
   In the mobile menu the industry pages get their own group right under "Usługi". */
$zp_menu_260 = function_exists('zp_seo_plan_active') && zp_seo_plan_active() && function_exists('zp_seo_plan_is_en') && !zp_seo_plan_is_en();
$zp_menu_260_swap = static function (string $path, string $icon, string $title, string $desc, string $old_href, string $old_icon, string $old_title, string $old_desc) use ($zp_menu_250): string {
  $live = $zp_menu_250($path);
  return '<a class="zpNewNav__megaLink" href="' . esc_url($live ? $path : $old_href) . '"><span class="zpNewNav__megaIco"><i data-lucide="' . esc_attr($live ? $icon : $old_icon) . '"></i></span><span><span class="zpNewNav__megaTitle">' . esc_html($live ? $title : $old_title) . '</span><span class="zpNewNav__megaDesc">' . esc_html($live ? $desc : $old_desc) . '</span></span></a>';
};
$zp_menu_260_promo = static function (string $key, string $href, string $img, string $title, string $price, string $text, string $btn, bool $hidden): string {
  return '<a class="zpNewNav__promoCard zpNewNav__promoCard--' . esc_attr($key) . '" href="' . esc_url($href) . '" data-zp-promo="' . esc_attr($key) . '"' . ($hidden ? ' hidden' : '') . '><span class="zpNewNav__promoMedia" aria-hidden="true"><img src="' . esc_url($img) . '" alt="" loading="lazy" decoding="async"></span><span class="zpNewNav__promoBody"><span class="zpNewNav__promoTitle">' . esc_html($title) . ' <span class="zpNewNav__promoPrice">' . esc_html($price) . '</span></span><span class="zpNewNav__promoText">' . esc_html($text) . '</span></span><span class="zpNewNav__promoBtn">' . esc_html($btn) . ' <i data-lucide="arrow-up-right"></i></span></a>';
};
?>
          <li class="zpNewNav__item"><a class="zpNewNav__link" href="/">Start</a></li>
          <li class="zpNewNav__item" data-mega>
            <a class="zpNewNav__link" href="/strony-internetowe-katowice/" aria-haspopup="true" aria-expanded="false">Usługi <?php echo zp_suite_header_inline_icon('chevron-down', 'zpNewNav__caret'); ?></a>
            <div class="zpNewNav__mega" role="group" aria-label="Usługi" hidden>
              <div class="zpNewNav__megaInner">
                <div class="zpNewNav__megaGrid">
                  <div class="zpNewNav__megaCol"><p class="zpNewNav__megaHead">Start marki</p><div class="zpNewNav__megaList">
                    <a class="zpNewNav__megaLink" href="/logo-branding-katowice/"><span class="zpNewNav__megaIco"><i data-lucide="sparkles"></i></span><span><span class="zpNewNav__megaTitle">Projekt logo</span><span class="zpNewNav__megaDesc">Projekt znaku, warianty, pliki do druku i internetu.</span></span></a>
                    <?php if ($zp_menu_250('/identyfikacja-wizualna/')) : ?><a class="zpNewNav__megaLink" href="/identyfikacja-wizualna/"><span class="zpNewNav__megaIco"><i data-lucide="book-open-text"></i></span><span><span class="zpNewNav__megaTitle">Identyfikacja wizualna</span><span class="zpNewNav__megaDesc">Brandbook, kolory, typografia i zasady użycia.</span></span></a>
                    <?php else : ?><a class="zpNewNav__megaLink" href="<?php echo esc_url(zp_seo_plan_url('/identyfikacja-wizualna/', '/logo-branding-katowice/')); ?>"><span class="zpNewNav__megaIco"><i data-lucide="book-open-text"></i></span><span><span class="zpNewNav__megaTitle">Brandbook</span><span class="zpNewNav__megaDesc">Kolory, typografia, zasady użycia i system wizualny.</span></span></a>
                    <?php endif; ?>
                    <?php echo $zp_menu_260_swap('/logo-branding/rebranding-firmy/', 'badge-check', 'Rebranding', 'Odświeżenie marki i uporządkowanie komunikacji wizualnej.', zp_seo_plan_url('/identyfikacja-wizualna/', '/logo-branding-katowice/'), 'badge-check', 'Rebranding', 'Odświeżenie marki i uporządkowanie komunikacji wizualnej.'); ?>
                    <?php echo $zp_menu_250_item('/strona-wizytowka/', 'id-card', 'Strona wizytówka', 'Strona one page z ofertą, opiniami i szybkim kontaktem.'); ?>
                  </div></div>
                  <div class="zpNewNav__megaCol"><p class="zpNewNav__megaHead">Strony &amp; sklepy</p><div class="zpNewNav__megaList">
                    <a class="zpNewNav__megaLink" href="/strony-internetowe-katowice/"><span class="zpNewNav__megaIco"><i data-lucide="panel-top"></i></span><span><span class="zpNewNav__megaTitle">Strona premium</span><span class="zpNewNav__megaDesc">Projekt, wdrożenie, wersja mobilna, SEO i analityka.</span></span></a>
                    <?php echo $zp_menu_250_item('/strony-wordpress/', 'layout-template', 'Strona WordPress', 'Projekt bez gotowego motywu i prosta edycja treści.'); ?>
                    <a class="zpNewNav__megaLink" href="/sklepy-internetowe-katowice/"><span class="zpNewNav__megaIco"><i data-lucide="package-check"></i></span><span><span class="zpNewNav__megaTitle">Sklep WooCommerce</span><span class="zpNewNav__megaDesc">Produkty, koszyk, płatności, dostawy i gotowość do reklam.</span></span></a>
                    <?php echo $zp_menu_260_swap('/strony-www/kiedy-warto-przebudowac-strone-internetowa-firmy/', 'rocket', 'Przebudowa strony', 'Nowy projekt i szybsza strona bez utraty pozycji w Google.', '/kontakt/', 'rocket', 'Rozbudowa strony', 'Nowe sekcje, funkcje, formularze, kalkulatory i optymalizacja.'); ?>
                  </div></div>
                  <div class="zpNewNav__megaCol"><p class="zpNewNav__megaHead">Sprzedaż</p><div class="zpNewNav__megaList">
                    <a class="zpNewNav__megaLink" href="/kampanie-reklamowe/"><span class="zpNewNav__megaIco"><i data-lucide="badge-percent"></i></span><span><span class="zpNewNav__megaTitle">Kampanie reklamowe</span><span class="zpNewNav__megaDesc">Meta Ads i Google Ads: strategia, kreacja, analityka i optymalizacja.</span></span></a>
                    <?php echo $zp_menu_250_item('/tworzenie-landing-page/', 'mouse-pointer-click', 'Landing page', 'Strona pod kampanię, która zamienia kliknięcia w zapytania.'); ?>
                    <?php echo $zp_menu_260_swap('/strony-www/audyt-strony-internetowej-firmy-przed-reklamami-i-seo/', 'scan-search', 'Audyt strony', 'Co poprawić, zanim ruszą reklamy i SEO.', '/kontakt/', 'goal', 'Lejki sprzedażowe', 'Struktura strony i reklam ułożona pod konkretne zapytania.'); ?>
                    <a class="zpNewNav__megaLink" href="<?php echo esc_url(function_exists('zp_seo_plan_url') ? zp_seo_plan_url('/opieka-wordpress/', '/kontakt/') : '/kontakt/'); ?>"><span class="zpNewNav__megaIco"><i data-lucide="life-buoy"></i></span><span><span class="zpNewNav__megaTitle">Wsparcie techniczne</span><span class="zpNewNav__megaDesc">Pomoc po wdrożeniu i stała opieka nad stroną.</span></span></a>
                  </div></div>
                  <div class="zpNewNav__megaCol zpNewNav__megaPromoCol"><p class="zpNewNav__megaHead">Najczęściej wybierane</p><a class="zpNewNav__promoCard zpNewNav__promoCard--logo" href="/logo-branding-katowice/"<?php echo $zp_menu_260 ? ' data-zp-promo="logo"' : ''; ?>><span class="zpNewNav__promoMedia" aria-hidden="true"><img src="https://zaprojektowani.com/wp-content/uploads/2026/09/zgorecki_oferta.webp" alt="" loading="lazy" decoding="async"></span><span class="zpNewNav__promoBody"><span class="zpNewNav__promoTitle">Logo <span class="zpNewNav__promoPrice">już od 999 zł</span></span><span class="zpNewNav__promoText">Profesjonalny znak, pliki do użycia i spójny kierunek wizualny.</span></span><span class="zpNewNav__promoBtn">Zamów logo <i data-lucide="arrow-up-right"></i></span></a><?php if ($zp_menu_260) {
                    echo $zp_menu_260_promo('strona', '/strony-internetowe-katowice/', ZP_SUITE_URL . 'assets/img/menu/promo-strona.webp', 'Strona internetowa', 'już od 3 999 zł', 'Projekt na miarę, wersja mobilna, SEO i szybkie działanie.', 'Zamów stronę', true);
                    echo $zp_menu_260_promo('sklep', '/sklepy-internetowe-katowice/', ZP_SUITE_URL . 'assets/img/menu/promo-sklep.webp', 'Sklep internetowy', 'już od 6 499 zł', 'Produkty, płatności i dostawy gotowe do sprzedaży i reklam.', 'Zamów sklep', true);
                  } ?><?php if (function_exists('zp_suite_meta_verified_markup')) { echo zp_suite_meta_verified_markup('auto', 'mega'); } ?><?php if ($zp_menu_250_ind_desk !== '') : ?><p class="zpNewNav__megaHead zpNewNav__megaHead--branze">Strony dla branż</p><div class="zpNewNav__megaList zpNewNav__megaList--branze"><?php echo $zp_menu_250_ind_desk; ?></div><?php endif; ?></div>
                </div>
                <div class="zpNewNav__megaBottom"><div class="zpNewNav__megaBottomInner"><?php if ($zp_menu_260) : ?><span class="zpNewNav__megaGuide" aria-hidden="true"><span class="zpNewNav__megaGuideBubble">Wybierz, od czego zaczynamy.</span><img class="zpNewNav__megaGuideImg" src="<?php echo esc_url(ZP_SUITE_URL . 'assets/img/menu/mateusz-wybierz.webp'); ?>" alt="" width="354" height="360" loading="lazy" decoding="async"></span><?php endif; ?><p class="zpNewNav__megaClaim">Od pomysłu do gotowego systemu sprzedaży.</p><p class="zpNewNav__megaText">Możemy zacząć od logo, strony, sklepu albo kampanii — ważne, żeby całość pracowała na jeden cel.</p><a class="zpNewNav__megaBtn" href="/studio-wyceny/"><span>Omów projekt</span><i data-lucide="arrow-up-right"></i></a></div></div>
              </div>
            </div>
          </li>
          <li class="zpNewNav__item"><a class="zpNewNav__link" href="/realizacje/">Realizacje</a></li>
          <li class="zpNewNav__item"><a class="zpNewNav__link" href="/wiedza/">Wiedza</a></li>
          <li class="zpNewNav__item"><a class="zpNewNav__link" href="/najczesciej-zadawane-pytania/">FAQ</a></li>
          <li class="zpNewNav__item"><a class="zpNewNav__link" href="/o-nas/">O nas</a></li>
          <li class="zpNewNav__item"><a class="zpNewNav__link" href="/kontakt/">Kontakt</a></li>
        </ul>
      </nav>
      <div class="zpNewNav__actions" role="group" aria-label="Szybki kontakt"><button class="zpNewNav__contactIcon zpNewNav__searchTrigger" type="button" aria-label="Szukaj"><?php echo zp_suite_header_inline_icon('search'); ?></button><span class="zpNewNav__divider zpNewNav__divider--preContact" aria-hidden="true"></span><a class="zpNewNav__contactIcon" href="https://wa.me/48501054253" target="_blank" rel="noopener" aria-label="WhatsApp"><?php echo zp_suite_header_inline_icon('message-circle'); ?></a><a class="zpNewNav__contactIcon" href="https://m.me/zaprojektowanicom" target="_blank" rel="noopener" aria-label="Messenger"><?php echo zp_suite_header_inline_icon('messages-square'); ?></a><a class="zpNewNav__contactIcon" href="tel:+48501054253" aria-label="Telefon"><?php echo zp_suite_header_inline_icon('phone'); ?></a><span class="zpNewNav__divider" aria-hidden="true"></span><a class="zpNewNav__cta" data-zp-header-cta href="<?php echo esc_url($zp_header_cta_url); ?>"><span><?php echo esc_html($zp_header_cta_text); ?></span><?php echo zp_suite_header_inline_icon('arrow-up-right'); ?></a></div>
    </div>
    
<div class="zpNewNav__mobileBar">
  <a class="zpNewNav__mobileCall" href="tel:+48501054253" aria-label="Konsultacja telefoniczna z Zaprojektowani">
    <i data-lucide="phone"></i><span>Konsultacja</span>
  </a>
  <a class="zpNewNav__mobileBrand" href="/" aria-label="Zaprojektowani.com">
    <img class="zpNewNav__mobileLogo zpNewNav__mobileLogo--light" src="<?php echo esc_url($zp_logo_light); ?>" alt="Zaprojektowani.com" loading="eager" decoding="async" width="150" height="150">
    <img class="zpNewNav__mobileLogo zpNewNav__mobileLogo--dark" src="<?php echo esc_url($zp_logo_dark); ?>" alt="Zaprojektowani.com" loading="eager" decoding="async" width="150" height="150">
  </a>
  <div class="zpNewNav__mobileActions">
    <button class="zpNewNav__burger" type="button" aria-label="Otwórz menu" aria-expanded="false" aria-controls="zpNewNavDrawer"><i data-lucide="menu"></i></button>
  </div>
</div>
    <div class="zpHeaderStaticDivider" aria-hidden="true"></div>
  </div>

  <div class="zpNewNav__drawer" id="zpNewNavDrawer" aria-hidden="true" hidden>
    <div class="zpNewNav__drawerBg" data-zpnn-close></div>
    <aside class="zpNewNav__drawerPanel zpNewNav__drawerPanel--pro" role="dialog" aria-modal="true" aria-label="Menu mobilne">
      <div class="zpNewNav__drawerTop">
        <a class="zpNewNav__drawerBrand" href="/" aria-label="Zaprojektowani.com">
          <img class="zpNewNav__drawerLogo zpNewNav__drawerLogo--dark" src="https://zaprojektowani.com/wp-content/uploads/2026/06/zp_sygnet_ciemny-150x150.webp" alt="Zaprojektowani.com" loading="eager" decoding="async" width="150" height="150">
          <img class="zpNewNav__drawerLogo zpNewNav__drawerLogo--light" src="https://zaprojektowani.com/wp-content/uploads/2026/06/zp_sygnet-150x150.webp" alt="Zaprojektowani.com" loading="eager" decoding="async" width="150" height="150">
        </a>
        <button class="zpNewNav__close" type="button" data-zpnn-close aria-label="Zamknij menu"><i data-lucide="x" aria-hidden="true"></i></button>
      </div>

      <div class="zpNewNav__drawerBody">
        <div class="zpNewNav__drawerSearch" role="search">
          <i data-lucide="search"></i>
          <input type="search" placeholder="Szukaj usługi, np. strona, sklep, logo..." aria-label="Szukaj w menu">
        </div>

        

<nav class="zpNewNav__mNav" aria-label="Menu mobilne">
          <div class="zpNewNav__mItem">
            <button class="zpNewNav__mSummary" type="button" aria-expanded="false">
              <span class="zpNewNav__mIcon"><i data-lucide="layers-3"></i></span>
              <span class="zpNewNav__mMain">Usługi</span>
              <span class="zpNewNav__mArrow"><i data-lucide="chevron-down"></i></span>
            </button>
            <div class="zpNewNav__mSubWrap">
              <div class="zpNewNav__mSub zpNewNav__mSub--cards">
                <a href="/strony-internetowe-katowice/"><span class="zpNewNav__mSubIcon"><i data-lucide="monitor"></i></span><span><strong>Strony internetowe</strong><em>WWW, WordPress, SEO</em></span><span class="zpNewNav__mGo"><i data-lucide="arrow-up-right"></i></span></a>
                <?php echo $zp_menu_250_m('/strony-wordpress/', 'layout-template', 'Strona WordPress', 'Projekt bez gotowego motywu'); ?>
                <?php echo $zp_menu_250_m('/strona-wizytowka/', 'id-card', 'Strona wizytówka', 'One page dla firmy'); ?>
                <?php echo $zp_menu_250_m('/tworzenie-landing-page/', 'mouse-pointer-click', 'Landing page', 'Strona pod kampanię'); ?>
                <a href="/sklepy-internetowe-katowice/"><span class="zpNewNav__mSubIcon"><i data-lucide="shopping-cart"></i></span><span><strong>Sklepy internetowe</strong><em>WooCommerce i sprzedaż</em></span><span class="zpNewNav__mGo"><i data-lucide="arrow-up-right"></i></span></a>
                <a href="/logo-branding-katowice/"><span class="zpNewNav__mSubIcon"><i data-lucide="pen-tool"></i></span><span><strong>Logo & branding</strong><em><?php echo $zp_menu_250('/identyfikacja-wizualna/') ? 'Projekt logo i znaku' : 'Identyfikacja wizualna'; ?></em></span><span class="zpNewNav__mGo"><i data-lucide="arrow-up-right"></i></span></a>
                <?php echo $zp_menu_250_m('/identyfikacja-wizualna/', 'book-open-text', 'Identyfikacja wizualna', 'Brandbook, kolory i typografia'); ?>
                <a href="/kampanie-reklamowe/"><span class="zpNewNav__mSubIcon"><i data-lucide="megaphone"></i></span><span><strong>Kampanie reklamowe</strong><em>Meta Ads + Google Ads</em></span><span class="zpNewNav__mGo"><i data-lucide="arrow-up-right"></i></span></a>
                <a href="/kontakt/"><span class="zpNewNav__mSubIcon"><i data-lucide="search-check"></i></span><span><strong>SEO i treści</strong><em>Widoczność w Google</em></span><span class="zpNewNav__mGo"><i data-lucide="arrow-up-right"></i></span></a>
              </div>
            </div>
          </div>
          <?php if ($zp_menu_250_ind_mob !== '') : ?>
          <div class="zpNewNav__mItem zpNewNav__mItem--branze">
            <button class="zpNewNav__mSummary" type="button" aria-expanded="false">
              <span class="zpNewNav__mIcon"><i data-lucide="briefcase-business"></i></span>
              <span class="zpNewNav__mMain">Strony dla branż</span>
              <span class="zpNewNav__mArrow"><i data-lucide="chevron-down"></i></span>
            </button>
            <div class="zpNewNav__mSubWrap">
              <div class="zpNewNav__mSub zpNewNav__mSub--cards">
                <?php echo $zp_menu_250_ind_mob; ?>
              </div>
            </div>
          </div>
          <?php endif; ?>

          <a class="zpNewNav__mLink" href="/realizacje/"><span class="zpNewNav__mIcon"><i data-lucide="gallery-horizontal-end"></i></span><span class="zpNewNav__mMain">Realizacje</span><span class="zpNewNav__mArrow"><i data-lucide="arrow-up-right"></i></span></a>
          <a class="zpNewNav__mLink" href="/wiedza/"><span class="zpNewNav__mIcon"><i data-lucide="book-open-text"></i></span><span class="zpNewNav__mMain">Wiedza</span><span class="zpNewNav__mArrow"><i data-lucide="arrow-up-right"></i></span></a>
          <a class="zpNewNav__mLink" href="/najczesciej-zadawane-pytania/"><span class="zpNewNav__mIcon"><i data-lucide="circle-help"></i></span><span class="zpNewNav__mMain">FAQ</span><span class="zpNewNav__mArrow"><i data-lucide="arrow-up-right"></i></span></a>
          <a class="zpNewNav__mLink" href="/o-nas/"><span class="zpNewNav__mIcon"><i data-lucide="sparkles"></i></span><span class="zpNewNav__mMain">O nas</span><span class="zpNewNav__mArrow"><i data-lucide="arrow-up-right"></i></span></a>
          <a class="zpNewNav__mLink" href="/kontakt/"><span class="zpNewNav__mIcon"><i data-lucide="messages-square"></i></span><span class="zpNewNav__mMain">Kontakt</span><span class="zpNewNav__mArrow"><i data-lucide="arrow-up-right"></i></span></a>
        </nav>

        <?php if (function_exists('zp_suite_meta_verified_markup')) { echo zp_suite_meta_verified_markup('light', 'mobile'); } ?>

        <a class="zpNewNav__drawerContact" href="/studio-wyceny/" aria-label="Przejdź do Studio Wyceny">
          <small>Szybka wycena</small>
          <strong>Potrzebujesz wyceny?</strong>
          <p>Opisz krótko, czego potrzebujesz. Przejdź do Studio Wyceny i ułóżmy konkretny zakres projektu.</p>
          <span class="zpNewNav__drawerContactCta"><span>Przejdź do Studio Wyceny</span><i data-lucide="arrow-up-right"></i></span>
        </a>
      </div>

      <div class="zpNewNav__drawerFoot">
        <a href="https://wa.me/48501054253" target="_blank" rel="noopener" aria-label="WhatsApp"><i data-lucide="message-circle"></i></a>
        <a href="https://m.me/zaprojektowanicom" target="_blank" rel="noopener" aria-label="Messenger"><i data-lucide="messages-square"></i></a>
        <a href="tel:+48501054253" aria-label="Telefon"><i data-lucide="phone"></i></a>
      </div>
    </aside>
  </div>
  <div class="zpNewNav__searchModal" id="zpNewNavSearch" aria-hidden="true" hidden data-ajax-url="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" data-nonce="<?php echo esc_attr(wp_create_nonce('zp_suite_live_search')); ?>"><div class="zpNewNav__searchBg" data-zpnn-search-close></div><div class="zpNewNav__searchBox" role="dialog" aria-modal="true" aria-label="Wyszukiwarka"><div class="zpNewNav__searchTop"><span><small>Wyszukiwarka</small><strong>Szukaj w Zaprojektowani</strong></span><button class="zpNewNav__searchClose" type="button" data-zpnn-search-close aria-label="Zamknij wyszukiwarkę"><i data-lucide="x"></i></button></div><form class="zpNewNav__searchForm" action="/" method="get"><span class="zpNewNav__searchIcon"><i data-lucide="search"></i></span><input class="zpNewNav__searchInput" type="search" name="s" placeholder="Wpisz: strona, sklep, logo, SEO..." autocomplete="off"><button class="zpNewNav__searchSubmit" type="submit"><span>Szukaj</span><i data-lucide="arrow-up-right"></i></button></form><div class="zpNewNav__searchState" data-zp-search-state>Wpisz minimum 2 znaki — wyniki stron i wpisów pokażą się tutaj na żywo.</div><div class="zpNewNav__searchResults" data-zp-search-results hidden></div><div class="zpNewNav__searchQuick"><a href="/strony-internetowe-katowice/"><i data-lucide="monitor"></i><span>Strony internetowe</span></a><a href="/sklepy-internetowe-katowice/"><i data-lucide="shopping-cart"></i><span>Sklepy internetowe</span></a><a href="/logo-branding-katowice/"><i data-lucide="pen-tool"></i><span>Logo & branding</span></a><a href="/studio-wyceny/"><i data-lucide="send"></i><span>Studio wyceny</span></a></div></div></div>

<!-- removed zp-suite-v1884-mobile-true-glass-only in v2.2.270 clean mobile header -->



<script id="zp-suite-532-early-mobile-drawer">
(function(w,d){
  'use strict';
  if(w.__zpEarlyMobileDrawer532) return;
  w.__zpEarlyMobileDrawer532=1;
  var suppressClickUntil=0;
  var fastHome=false;
  try{fastHome=((w.location&&w.location.pathname||'/').replace(/\/+$/,'')||'/')==='/';}catch(e){}
  function qs(s,c){return (c||d).querySelector(s)}
  function navOf(el){return el && el.closest ? el.closest('#zpNewNav') : qs('#zpNewNav')}
  function lock(v){
    d.documentElement.classList.toggle('zpNewNav-lock',!!v);
    if(d.body) d.body.classList.toggle('zpNewNav-lock',!!v);
  }
  function isOpen(nav){
    var dr=qs('#zpNewNavDrawer',nav);
    return !!(dr && !dr.classList.contains('is-closing') && (dr.classList.contains('is-open') || dr.getAttribute('aria-hidden')==='false'));
  }
  function setBusy(ms){try{w.__zpNavUiBusyUntil=Date.now()+(ms||1600);}catch(e){}}
  function open(nav){
    if(!nav) return;
    setBusy(fastHome?480:1800);
    var dr=qs('#zpNewNavDrawer',nav), b=qs('.zpNewNav__burger',nav);
    if(!dr) return;
    if(dr.__zpCloseTimer){clearTimeout(dr.__zpCloseTimer);dr.__zpCloseTimer=0;}
    nav.classList.remove('zpNewNav--drawer-closing');
    dr.classList.remove('is-closing');
    dr.hidden=false; dr.removeAttribute('hidden');
    dr.setAttribute('aria-hidden','false');
    dr.classList.add('is-open');
    if(b) b.setAttribute('aria-expanded','true');
    lock(true);
    nav.classList.add('zpNewNav--drawer-open');
    if(typeof w.zpSuiteHydrateIcons==='function'){
      try{w.zpSuiteHydrateIcons(dr);}catch(e){}
    }
  }
  function close(nav){
    if(!nav) return;
    setBusy(fastHome?320:1800);
    suppressClickUntil=Date.now()+(fastHome?260:1200);
    var dr=qs('#zpNewNavDrawer',nav), b=qs('.zpNewNav__burger',nav);
    if(dr){
      if(dr.__zpCloseTimer){clearTimeout(dr.__zpCloseTimer);}
      dr.classList.remove('is-open');
      dr.classList.add('is-closing');
      dr.setAttribute('aria-hidden','true');
      if(fastHome){
        dr.classList.remove('is-closing');
        dr.hidden=true; dr.setAttribute('hidden','hidden');
      }else{
        dr.hidden=false; dr.removeAttribute('hidden');
        dr.__zpCloseTimer=setTimeout(function(){
          if(!dr.classList.contains('is-open')){
            dr.classList.remove('is-closing');
            dr.hidden=true; dr.setAttribute('hidden','hidden');
          }
        },420);
      }
    }
    if(b) b.setAttribute('aria-expanded','false');
    lock(false);
    nav.classList.remove('zpNewNav--drawer-open');
    nav.classList.add('zpNewNav--drawer-closing');
    setTimeout(function(){nav.classList.remove('zpNewNav--drawer-closing');},fastHome?280:1250);
  }
  function consume(e){
    if(!e) return;
    e.preventDefault();
    e.stopPropagation();
    if(e.stopImmediatePropagation) e.stopImmediatePropagation();
  }
  function handle(e){
    var t=e.target;
    if(!t || !t.closest) return;
    var closeBtn=t.closest('[data-zpnn-close]');
    var burger=t.closest('.zpNewNav__burger');
    if(!closeBtn && !burger) return;
    var nav=navOf(closeBtn||burger);
    if(!nav) return;
    if(e.type==='click' && burger && Date.now()<suppressClickUntil){consume(e);return;}
    if(e.type==='click' && (Date.now()-(w.__zpEarlyDrawerLastTap||0))<760){consume(e);return;}
    consume(e);
    w.__zpEarlyDrawerLastTap=Date.now();
    if(closeBtn){close(nav);return;}
    if(burger){isOpen(nav)?close(nav):open(nav);}
  }
  d.addEventListener('pointerdown',handle,{capture:true,passive:false});
  if(!w.PointerEvent){d.addEventListener('touchstart',handle,{capture:true,passive:false});}
  d.addEventListener('click',handle,true);
  w.__zpEarlyDrawerApi={
    open:function(){open(qs('#zpNewNav'));},
    close:function(){close(qs('#zpNewNav'));},
    isOpen:function(){return isOpen(qs('#zpNewNav'));}
  };
})(window,document);
</script>

</header>

<script id="zp-suite-2550-header-active-early">
/* v2.2.550: paint the active-link underline with the header, not seconds later.
   header.js is a DEFER script, so its setActive() only runs after the whole (~1.1MB)
   page has parsed (domInteractive); on heavy pages like /strony-internetowe-katowice/
   the header is visible long before the underline "catches up". This tiny inline pass
   runs during parse, right after the nav exists, and sets the same .is-active classes
   header.js would — so the underline is correct from first paint. header.js still runs
   later and is idempotent (it only adds .is-active). */
(function(d){try{
  var nav=d.getElementById('zpNewNav'); if(!nav) return;
  var path=(location.pathname||'/').replace(/\/+$/,''); if(!path) path='/';
  function norm(h){ if(!h) return ''; try{h=new URL(h,location.origin).pathname;}catch(e){}
    h=String(h||'').split('#')[0].split('?')[0]; if(h.length>1) h=h.replace(/\/+$/,''); return h||'/'; }
  var links=nav.querySelectorAll('.zpNewNav__link,.zpNewNav__mLink,#zpNewNavDrawer .zpNewNav__mSub a,.zpNewNav__megaLink,.zpNewNav__promoCard');
  for(var i=0;i<links.length;i++){ var h=norm(links[i].getAttribute('href')); if(!h) continue;
    if((h==='/'&&path==='/')||(h!=='/'&&(path===h||path.indexOf(h+'/')===0))){
      links[i].classList.add('is-active');
      var it=links[i].closest&&links[i].closest('.zpNewNav__item'); if(it) it.classList.add('is-active'); } }
  var svc=['/strony-internetowe-katowice','/sklepy-internetowe-katowice','/logo-branding-katowice','/kampanie-reklamowe'];
  if(svc.indexOf(path)!==-1){
    var items=nav.querySelectorAll('.zpNewNav__item[data-mega],.zpNewNav__item[data-drop]');
    for(var j=0;j<items.length;j++){
      if(items[j].querySelector('a[href*="strony-internetowe-katowice"],a[href*="sklepy-internetowe-katowice"],a[href*="logo-branding-katowice"],a[href*="kampanie-reklamowe"]')){
        items[j].classList.add('is-active');
        var lk=items[j].querySelector(':scope > .zpNewNav__link'); if(lk) lk.classList.add('is-active'); } }
  }
}catch(e){}})(document);
</script>






<!-- =========================================================
ZAPROJEKTOWANI — HEADER FINAL CLEAN LIGHT/NAVY v9.2
Zmiany względem v9:
- większe headingi +2px
- czystsze jasne mega menu bez kratki/gridu
- ciemniejszy navy bez zielonych przejść
- ciemne promo karty jak hero, białe ceny
- buttony w nowym stylu: biały/ciemny tekst + navy sweep hover
========================================================= -->


<!-- /ZAPROJEKTOWANI — HEADER / CLEAN INLINE HTML + CSS + JS v9.2 FINAL CLEAN LIGHT/NAVY -->

<!-- removed zp-suite-v1877-hard-logo-mobile-glass-final in v2.2.270 clean mobile header -->





































<!-- =========================================================
ZP Suite v2.1.61 — MOBILE HEADER DIVIDER FIX
Problem: na mobile divider pod headerem był synchronizowany z getBoundingClientRect()
i przy powrocie na górę potrafił „doganiać” header z opóźnieniem.
Fix: na mobile linia jest zawsze fixed pod paskiem headera, bez transition na top/transform.
========================================================= -->





<!-- =========================================================
ZP Suite v2.1.62 — MOBILE HEADER DIVIDER EXACT ALIGN
Fix: divider na mobile jest wyrównany do dolnej krawędzi realnego mobile headera.
- bez transform/transition na pozycji
- linia nie siedzi 4px niżej przez wewnętrzne span top:4px
- JS ustawia wysokość z .zpNewNav__mobileBar, a nie z pozycji scrolla
========================================================= -->







<!-- =========================================================
ZP Suite v2.1.92 — HEADER DIVIDER TRUE BOTTOM ALIGN
Fix: divider liczony z realnej dolnej krawędzi shell/mobileBar, bez twardego 76/78px.
Dzięki temu statyczny header i sticky mają tę samą wysokość optyczną, bez przeskoku linii.
========================================================= -->




<!-- =========================================================
ZP Suite v2.1.93 — HEADER HEIGHT + DIVIDER RESTORE
Fix po v2.1.92: divider nie jest już liczony z wysokiego recta logo/hero, tylko z realnej wysokości sticky headera.
========================================================= -->



















































































<!-- removed v2.2.561 crossover logo lock; v2.2.562 uses the real mobile header layout up to 1200px. -->
