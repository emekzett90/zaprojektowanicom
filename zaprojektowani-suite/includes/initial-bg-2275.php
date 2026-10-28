<?php
if (!defined('ABSPATH')) {
  exit;
}

/**
 * ZP Suite v2.2.75 — ultra-early dark initial paint for Elementor shortcode home.
 *
 * Cel: usunąć biały flash zanim Elementor wyrenderuje kontener z [zp_home_full].
 * Nie zmienia układu ani wyglądu sekcji — ustawia tylko kolor tła warstw nadrzędnych
 * na stronie głównej, zanim doładują się pełne style widgetów.
 */

function zp_suite_2275_is_home_like_request() {
  if (is_admin() || wp_doing_ajax() || wp_is_json_request()) {
    return false;
  }

  if (is_front_page() || is_home()) {
    return true;
  }

  // SEO landing pages with dark hero paint. Wiedza has its own lighter boot logic below.
  if (function_exists('is_page') && zp_suite_is_service_page('strony')) {
    return true;
  }

  if (is_singular()) {
    global $post;
    $content = ($post && !empty($post->post_content)) ? (string) $post->post_content : '';
    $slug = ($post && !empty($post->post_name)) ? (string) $post->post_name : '';

    if (in_array($slug, ['strony-internetowe-katowice'], true)) {
      return true;
    }

    return $content && (
      has_shortcode($content, 'zp_home_full') ||
      has_shortcode($content, 'zp_home') ||
      has_shortcode($content, 'zp_zaprojektowani_home') ||
      has_shortcode($content, 'zp_strony_internetowe_katowice') ||
      has_shortcode($content, 'zp_page_strony_katowice')
    );
  }

  return false;
}


function zp_suite_2275_is_wiedza_like_request() {
  if (is_admin() || wp_doing_ajax() || wp_is_json_request()) {
    return false;
  }

  if (function_exists('is_page') && (
    is_page('wiedza') ||
    is_page('blog') ||
    is_page('baza-wiedzy')
  )) {
    return true;
  }

  if (is_singular()) {
    global $post;
    $content = ($post && !empty($post->post_content)) ? (string) $post->post_content : '';
    $slug = ($post && !empty($post->post_name)) ? (string) $post->post_name : '';

    if (in_array($slug, ['wiedza', 'blog', 'baza-wiedzy'], true)) {
      return true;
    }

    return $content && has_shortcode($content, 'zp_wiedza_blog');
  }

  return false;
}

function zp_suite_2275_initial_bg_css() {
  return 'html{background:#04080f!important;color-scheme:dark;}html body{background:#04080f!important;}body.zp-home-bg-boot,body.zp-home-bg-boot #page,body.zp-home-bg-boot .site,body.zp-home-bg-boot .site-content,body.zp-home-bg-boot .content-area,body.zp-home-bg-boot main,body.zp-home-bg-boot .entry-content,body.zp-home-bg-boot .page-content,body.home,body.front-page,body.home #page,body.front-page #page{background:#04080f!important;background-color:#04080f!important;}body.zp-home-bg-boot .elementor,body.zp-home-bg-boot .elementor-section-wrap,body.zp-home-bg-boot .elementor-widget-container,body.zp-home-bg-boot .elementor-shortcode,body.home .elementor,body.front-page .elementor,body.home .elementor-section-wrap,body.front-page .elementor-section-wrap,body.home .elementor-widget-container,body.front-page .elementor-widget-container,body.home .elementor-shortcode,body.front-page .elementor-shortcode{background:#04080f!important;background-color:#04080f!important;}body.zp-home-bg-boot .zpNewNav:not(.is-scrolled):not(.is-mega-open),body.zp-home-bg-boot .zpNewNav:not(.is-scrolled):not(.is-mega-open) .zpNewNav__shell,body.zp-home-bg-boot .zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__shell{background:#04080f!important;background-color:#04080f!important;box-shadow:none!important;border-bottom-color:transparent!important;}body.zp-home-bg-boot .zpNewNav:not(.is-scrolled):not(.is-mega-open) .zpNewNav__link,body.zp-home-bg-boot .zpNewNav:not(.is-scrolled):not(.is-mega-open) .zpNewNav__round,body.zp-home-bg-boot .zpNewNav:not(.is-scrolled):not(.is-mega-open) .zpNewNav__burger{color:#fff!important;}body.zp-home-bg-boot .zpNewHero,body.zp-home-bg-boot .zpNewHero__bg,body.zp-home-bg-boot .zpKatHero,body.zp-home-bg-boot .zpKatHero__bg,body.zp-home-bg-boot .zpWebHero,body.zp-home-bg-boot .zpWebHero__bg,body.home .zpNewHero,body.front-page .zpNewHero,body.home .zpNewHero__bg,body.front-page .zpNewHero__bg{background-color:#04080f!important;}body.zp-wiedza-bg-boot,body.zp-wiedza-bg-boot #page,body.zp-wiedza-bg-boot .site,body.zp-wiedza-bg-boot .site-content,body.zp-wiedza-bg-boot .content-area,body.zp-wiedza-bg-boot main,body.zp-wiedza-bg-boot .entry-content,body.zp-wiedza-bg-boot .page-content{background:#04080f!important;background-color:#04080f!important;}body.zp-wiedza-bg-boot .elementor,body.zp-wiedza-bg-boot .elementor-section-wrap,body.zp-wiedza-bg-boot .elementor-widget-container,body.zp-wiedza-bg-boot .elementor-shortcode{background:#04080f!important;background-color:#04080f!important;}body.zp-wiedza-bg-boot .zpNewNav:not(.is-scrolled):not(.is-mega-open),body.zp-wiedza-bg-boot .zpNewNav:not(.is-scrolled):not(.is-mega-open) .zpNewNav__shell,body.zp-wiedza-bg-boot .zpNewNav.zpNewNav--heroOverlay:not(.is-scrolled):not(.is-mega-open) .zpNewNav__shell,body.zp-wiedza-bg-boot #zpKnowledgePro .zpKBHero,body.zp-wiedza-bg-boot #zpKnowledgePro .zpKBHero__video{background:#04080f!important;background-color:#04080f!important;}body.zp-wiedza-bg-boot #zpKnowledgePro,body.zp-wiedza-bg-boot #zpKnowledgePro .zpKBFeatured,body.zp-wiedza-bg-boot #zpKnowledgePro .zpKBIndex,body.zp-wiedza-bg-boot #zpKnowledgePro .zpKBFooterCTA,body.zp-wiedza-bg-boot #zpKnowledgePro .zpKBInner,body.zp-wiedza-bg-boot #zpKnowledgePro .zpKBPostsWrap,body.zp-wiedza-bg-boot #zpKnowledgePro .zpKBIndex__main,body.zp-wiedza-bg-boot #zpKnowledgePro .zpKBIndex__layout,body.zp-wiedza-bg-boot #zpKnowledgePro .zpKBIndex__side{background:#fff!important;background-color:#fff!important;}body.zp-wiedza-bg-boot #zpKnowledgePro .zpKBHero,body.zp-wiedza-bg-boot #zpKnowledgePro .zpKBHero .zpKBInner,body.zp-wiedza-bg-boot #zpKnowledgePro .zpKBHero__inner,body.zp-wiedza-bg-boot #zpKnowledgePro .zpKBHero__grid,body.zp-wiedza-bg-boot #zpKnowledgePro .zpKBHero__visual{background:transparent!important;background-color:transparent!important;}';
}

add_filter('body_class', function ($classes) {
  if (zp_suite_2275_is_home_like_request()) {
    $classes[] = 'zp-home-bg-boot';
  }
  if (zp_suite_2275_is_wiedza_like_request()) {
    $classes[] = 'zp-wiedza-bg-boot';
  }
  return $classes;
}, 20);

// Bardzo wcześnie w <head>, zanim większość CSS Elementora/motywu zacznie malować białe tło.
add_action('wp_head', function () {
  if (!zp_suite_2275_is_home_like_request() && !zp_suite_2275_is_wiedza_like_request()) {
    return;
  }
  echo "\n<style id=\"zp-suite-2275-initial-dark-paint\">" . zp_suite_2275_initial_bg_css() . "</style>\n";
  echo "<meta name=\"theme-color\" content=\"#04080f\">\n";
}, -999999);

// Drugi raz możliwie szybko po otwarciu body — pomaga przy motywach/Elementorze,
// które nadpisują tło między <head> a widgetem shortcode.
add_action('wp_body_open', function () {
  if (!zp_suite_2275_is_home_like_request() && !zp_suite_2275_is_wiedza_like_request()) {
    return;
  }
  echo "\n<style id=\"zp-suite-2275-body-open-dark-paint\">" . zp_suite_2275_initial_bg_css() . "</style>\n";
}, -999999);
