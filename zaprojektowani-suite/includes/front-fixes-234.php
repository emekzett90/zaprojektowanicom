<?php
/**
 * ZAPROJEKTOWANI Suite — front fixes v2.2.234
 * O nas: stabilne ładowanie fontu Jakarta + regulowany odstęp header → kicker.
 */
if (!defined('ABSPATH')) exit;

add_action('wp_head', function(){
  if (function_exists('zp_suite_233_is_about_page') && !zp_suite_233_is_about_page()) return;
  $gap_d = (float) (function_exists('zp_suite_opt') ? zp_suite_opt('about_page.top_gap_desktop', '174') : 174);
  $gap_m = (float) (function_exists('zp_suite_opt') ? zp_suite_opt('about_page.top_gap_mobile', '138') : 138);
  $gap_d = max(90, min(360, $gap_d));
  $gap_m = max(72, min(300, $gap_m));
  ?>
  <style id="zp-suite-front-fixes-234-about-gap-font">
    @font-face{font-family:"Plus Jakarta Sans Local";src:url("/wp-content/web-font/jakarta/plus-jakarta-sans-v12-latin_latin-ext-regular.woff2") format("woff2"),url("https://zaprojektowani.com/wp-content/web-font/jakarta/plus-jakarta-sans-v12-latin_latin-ext-regular.woff2") format("woff2");font-weight:400;font-style:normal;font-display:swap}
    @font-face{font-family:"Plus Jakarta Sans Local";src:url("/wp-content/web-font/jakarta/plus-jakarta-sans-v12-latin_latin-ext-500.woff2") format("woff2"),url("https://zaprojektowani.com/wp-content/web-font/jakarta/plus-jakarta-sans-v12-latin_latin-ext-500.woff2") format("woff2");font-weight:500;font-style:normal;font-display:swap}
    @font-face{font-family:"Plus Jakarta Sans Local";src:url("/wp-content/web-font/jakarta/plus-jakarta-sans-v12-latin_latin-ext-600.woff2") format("woff2"),url("https://zaprojektowani.com/wp-content/web-font/jakarta/plus-jakarta-sans-v12-latin_latin-ext-600.woff2") format("woff2");font-weight:600;font-style:normal;font-display:swap}
    @font-face{font-family:"Plus Jakarta Sans Local";src:url("/wp-content/web-font/jakarta/plus-jakarta-sans-v12-latin_latin-ext-700.woff2") format("woff2"),url("https://zaprojektowani.com/wp-content/web-font/jakarta/plus-jakarta-sans-v12-latin_latin-ext-700.woff2") format("woff2");font-weight:700;font-style:normal;font-display:swap}
    @font-face{font-family:"Plus Jakarta Sans Local";src:url("/wp-content/web-font/jakarta/plus-jakarta-sans-v12-latin_latin-ext-800.woff2") format("woff2"),url("https://zaprojektowani.com/wp-content/web-font/jakarta/plus-jakarta-sans-v12-latin_latin-ext-800.woff2") format("woff2");font-weight:800;font-style:normal;font-display:swap}

    html body.zp-about-nav-final #zpAboutPage,
    html body:has(#zpAboutPage) #zpAboutPage{
      padding-top:<?php echo esc_html($gap_d); ?>px!important;
      font-family:"Plus Jakarta Sans Local","Plus Jakarta Sans",system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif!important;
    }
    html body.zp-about-nav-final #zpAboutPage *,
    html body:has(#zpAboutPage) #zpAboutPage *{
      font-family:"Plus Jakarta Sans Local","Plus Jakarta Sans",system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif!important;
    }
    @media(max-width:980px){
      html body.zp-about-nav-final #zpAboutPage,
      html body:has(#zpAboutPage) #zpAboutPage{padding-top:<?php echo esc_html($gap_m); ?>px!important;}
    }
  </style>
  <?php
}, 2147483647);
