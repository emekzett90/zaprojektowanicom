<?php
if (!defined('ABSPATH')) {
  exit;
}

function zp_suite_render_template($template) {
  $file = ZP_SUITE_PATH . 'templates/' . $template . '.php';

  if (!file_exists($file)) {
    return current_user_can('manage_options') ? '<!-- Zaprojektowani Suite: missing template ' . esc_html($template) . ' -->' : '';
  }

  ob_start();
  include $file;
  return ob_get_clean();
}

function zp_suite_register_template_shortcode($tag, $template) {
  add_shortcode($tag, function() use ($tag, $template) {
    /**
     * Safety fallback: when shortcode is rendered outside normal post content,
     * enqueue its assets anyway. On regular pages assets are already detected
     * before wp_head by zp_suite_enqueue_detected_shortcode_assets().
     */
    if (function_exists('zp_suite_enqueue_block_assets')) {
      zp_suite_enqueue_block_assets($tag);
    }

    return zp_suite_render_template($template);
  });
}


/**
 * v2.2.42 — Realizacje page body class available before header render.
 * This prevents late :has()/JS-based header corrections and stops the page from
 * jumping down after header spacer JS runs.
 */
add_filter('body_class', function($classes){
  if (is_admin()) {
    return $classes;
  }

  $is_realizacje = false;
  if (function_exists('is_page') && is_page('realizacje')) {
    $is_realizacje = true;
  }

  $is_faq = false;
  if (function_exists('is_page') && (is_page('najczesciej-zadawane-pytania') || is_page('faq'))) {
    $is_faq = true;
  }

  if ((!$is_realizacje || !$is_faq) && function_exists('is_singular') && is_singular()) {
    $post_id = get_queried_object_id();
    $content = $post_id ? (string) get_post_field('post_content', $post_id) : '';

    if (!$is_realizacje && $content && (has_shortcode($content, 'zp_realizacje') || has_shortcode($content, 'zp_page_realizacje'))) {
      $is_realizacje = true;
    }

    if (!$is_faq && $content && (has_shortcode($content, 'zp_faq_page') || has_shortcode($content, 'zp_page_faq'))) {
      $is_faq = true;
    }
  }

  if ($is_realizacje) {
    $classes[] = 'zp-realizacje-page';
    $classes[] = 'zp-realizacje-shortcode';
  }

  if ($is_faq) {
    $classes[] = 'zp-faq-page';
    $classes[] = 'zp-faq-shortcode';
  }

  return array_values(array_unique($classes));
}, 20);



/**
 * Logo & Branding Katowice page body classes for stable hero/header behavior.
 */
add_filter('body_class', function($classes){
  if (is_admin()) return $classes;

  $is_logo_branding = false;

  if (function_exists('is_page') && (zp_suite_is_service_page('logo') || is_page('logo-branding'))) {
    $is_logo_branding = true;
  }

  if (!$is_logo_branding && function_exists('is_singular') && is_singular()) {
    $post_id = get_queried_object_id();
    $content = $post_id ? (string) get_post_field('post_content', $post_id) : '';
    if ($content && (has_shortcode($content, 'zp_logo_branding_katowice') || has_shortcode($content, 'zp_page_logo_branding_katowice'))) {
      $is_logo_branding = true;
    }
  }

  if ($is_logo_branding) {
    $classes[] = 'zp-logo-branding-page';
    $classes[] = 'zp-logo-branding-shortcode';
  }

  return array_values(array_unique($classes));
}, 21);



/**
 * v2.2.523 — service page body classes available before header render.
 * This lets critical header CSS paint the correct transparent/dark hero state
 * immediately on /strony-internetowe-katowice/, /sklepy-internetowe-katowice/
 * and /logo-branding-katowice/ without waiting for JS or the full header.css.
 */
add_filter('body_class', function($classes){
  if (is_admin()) return $classes;

  $is_strony = false;
  $is_sklepy = false;
  $is_logo = false;

  if (function_exists('is_page')) {
    if (zp_suite_is_service_page('strony')) $is_strony = true;
    if (zp_suite_is_service_page('sklepy')) $is_sklepy = true;
    if (zp_suite_is_service_page('logo') || is_page('logo-branding')) $is_logo = true;
  }

  if (function_exists('is_singular') && is_singular()) {
    $post_id = get_queried_object_id();
    $content = $post_id ? (string) get_post_field('post_content', $post_id) : '';
    if ($content) {
      if (!$is_strony && (has_shortcode($content, 'zp_strony_internetowe_katowice') || has_shortcode($content, 'zp_page_strony_katowice') || has_shortcode($content, 'zp_katowice_page'))) {
        $is_strony = true;
      }
      if (!$is_sklepy && (has_shortcode($content, 'zp_sklepy_internetowe_katowice') || has_shortcode($content, 'zp_page_sklepy_katowice'))) {
        $is_sklepy = true;
      }
      if (!$is_logo && (has_shortcode($content, 'zp_logo_branding_katowice') || has_shortcode($content, 'zp_page_logo_branding_katowice'))) {
        $is_logo = true;
      }
    }
  }

  if ($is_strony) {
    $classes[] = 'zp-service-hero-page';
    $classes[] = 'zp-strony-katowice-page';
  }
  if ($is_sklepy) {
    $classes[] = 'zp-service-hero-page';
    $classes[] = 'zp-sklepy-katowice-page';
    $classes[] = 'zp-page-shop-katowice';
  }
  if ($is_logo) {
    $classes[] = 'zp-service-hero-page';
    $classes[] = 'zp-logo-branding-page';
    $classes[] = 'zp-logo-branding-shortcode';
  }

  return array_values(array_unique($classes));
}, 12);

// Realizacje / portfolio page moved from Elementor HTML widget into ZP Suite assets.
zp_suite_register_template_shortcode('zp_realizacje', 'realizacje-page');
zp_suite_register_template_shortcode('zp_page_realizacje', 'realizacje-page');

// FAQ page moved from Elementor HTML widget into ZP Suite assets.
zp_suite_register_template_shortcode('zp_faq_page', 'faq-page');
zp_suite_register_template_shortcode('zp_page_faq', 'faq-page');





function zp_suite_section_enabled($key) {
  return (string) zp_suite_opt('visibility.' . $key, '1') !== '0';
}

function zp_suite_home_sections_map(){
  return [
    'home_hero' => ['label'=>'Hero', 'shortcode'=>'[zp_home_hero]', 'visibility'=>null],
    'seo_intro' => ['label'=>'SEO intro', 'shortcode'=>'[zp_home_seo_intro]', 'visibility'=>null],
    'trust_logos' => ['label'=>'Logotypy', 'shortcode'=>'[zp_trust_logos]', 'visibility'=>'trust_logos'],
    'services_path' => ['label'=>'Zakres usług', 'shortcode'=>'[zp_services_path]', 'visibility'=>'services_path'],
    'featured_packages' => ['label'=>'Polecane pakiety', 'shortcode'=>'[zp_home_featured_packages]', 'visibility'=>null],
    'showcase_portfolio' => ['label'=>'Portfolio', 'shortcode'=>'[zp_showcase_portfolio]', 'visibility'=>'showcase_portfolio'],
    'laptop_showcase' => ['label'=>'Laptop showcase', 'shortcode'=>'[zp_laptop_showcase]', 'visibility'=>'laptop_showcase'],
    'about_experience' => ['label'=>'O Zaprojektowani', 'shortcode'=>'[zp_about_experience]', 'visibility'=>'about_experience'],
    'showcase_services' => ['label'=>'Usługi i branże', 'shortcode'=>'[zp_showcase_services]', 'visibility'=>'showcase_services'],
    'reviews_section' => ['label'=>'Opinie', 'shortcode'=>'[zp_reviews_section]', 'visibility'=>'reviews_section'],
    'seo_faq' => ['label'=>'FAQ', 'shortcode'=>'[zp_seo_faq]', 'visibility'=>'seo_faq'],
    'seo_industries' => ['label'=>'Branże SEO', 'shortcode'=>'[zp_seo_industries]', 'visibility'=>'seo_industries'],
    'home_audit_cta' => ['label'=>'Mini-audyt CTA', 'shortcode'=>'[zp_home_audit_cta]', 'visibility'=>'home_audit_cta'],
    'contact_system' => ['label'=>'Kontakt', 'shortcode'=>'[zp_home_contact_form]', 'visibility'=>'contact_system'],
  ];
}

function zp_suite_home_default_order(){
  return array_keys(zp_suite_home_sections_map());
}

function zp_suite_home_order(){
  $raw = zp_suite_opt('home_order.order', '');
  $order = [];
  if ($raw) {
    foreach (preg_split('/\r\n|\r|\n/', str_replace(['\\n','\\r'], "\n", (string)$raw)) as $line) {
      $key = sanitize_key(trim($line));
      if ($key) $order[] = $key;
    }
  }
  $map = zp_suite_home_sections_map();
  $out = [];
  foreach ($order as $key) { if (isset($map[$key]) && !in_array($key, $out, true)) $out[] = $key; }
  $featured_was_saved = in_array('featured_packages', $order, true);
  foreach (array_keys($map) as $key) { if (!in_array($key, $out, true)) $out[] = $key; }

  // Nowa sekcja ma wejść po "Zakres usług" także na instalacjach,
  // które mają już zapisaną własną kolejność sekcji strony głównej.
  if (!$featured_was_saved && in_array('featured_packages', $out, true)) {
    $out = array_values(array_diff($out, ['featured_packages']));
    $services_index = array_search('services_path', $out, true);
    if ($services_index === false) {
      array_unshift($out, 'featured_packages');
    } else {
      array_splice($out, $services_index + 1, 0, ['featured_packages']);
    }
  }

  return $out ?: zp_suite_home_default_order();
}

function zp_suite_home_part($shortcode, $visibility_key) {
  return (!$visibility_key || zp_suite_section_enabled($visibility_key)) ? do_shortcode($shortcode) : '';
}

function zp_suite_home_gradient_motion_css_702() {
  return <<<'CSS'
/* v2.2.702 — płynne przechodzenie gradientu przez wyróżnione słowa na stronie głównej. */
@keyframes zpHomeGradientFlow702 {
  0% { background-position: 0% 50%; }
  100% { background-position: 100% 50%; }
}

/* Hero oraz rotator usług — jasny biało-błękitny gradient na ciemnym tle. */
html body .zpHomeContent .zh .zh__title .zh__grad,
html body .zpHomeContent .zh .zh__svcRotMask i {
  background-image: linear-gradient(105deg,
    #79baf0 0%,
    #bfe5ff 18%,
    #ffffff 38%,
    #dff3ff 52%,
    #9fd3fb 70%,
    #ffffff 86%,
    #79baf0 100%) !important;
  background-size: 320% 100% !important;
  background-position: 0% 50% !important;
  background-repeat: no-repeat !important;
  -webkit-background-clip: text !important;
  background-clip: text !important;
  -webkit-text-fill-color: transparent !important;
  color: transparent !important;
  animation: zpHomeGradientFlow702 5.8s cubic-bezier(.45,0,.55,1) infinite alternate !important;
  will-change: background-position;
}

/* Wyróżnione frazy w jasnych sekcjach — ciemna baza zachowuje czytelność. */
html body .zpHomeContent .zpTrustPinned__title span,
html body .zpHomeContent #zpShowcaseWhite .zpShowcaseWhite__title span,
html body .zpHomeContent .zpLaptopShowcase h2 span,
html body .zpHomeContent #zpShowcaseServices .zpSS__title em {
  display: inline !important;
  overflow: visible !important;
  line-height: inherit !important;
  padding: .015em .035em .105em !important;
  margin: -.015em -.035em -.075em !important;
  background-image: linear-gradient(105deg,
    #071426 0%,
    #102a4f 18%,
    #3b6ea8 34%,
    #9fd3fb 45%,
    #f5fbff 51%,
    #79baf0 59%,
    #1c477a 76%,
    #071426 100%) !important;
  background-size: 320% 100% !important;
  background-position: 0% 50% !important;
  background-repeat: no-repeat !important;
  -webkit-background-clip: text !important;
  background-clip: text !important;
  -webkit-text-fill-color: transparent !important;
  color: transparent !important;
  -webkit-box-decoration-break: clone !important;
  box-decoration-break: clone !important;
  animation: zpHomeGradientFlow702 6.4s cubic-bezier(.45,0,.55,1) infinite alternate !important;
  will-change: background-position;
}

@media (max-width: 760px) {
  html body .zpHomeContent .zh .zh__title .zh__grad,
  html body .zpHomeContent .zh .zh__svcRotMask i {
    background-image: linear-gradient(105deg,
      #a9d9fb 0%,
      #e7f7ff 20%,
      #ffffff 41%,
      #eefaff 57%,
      #bce5ff 76%,
      #ffffff 100%) !important;
    background-size: 340% 100% !important;
    animation-duration: 6.2s !important;
  }

  html body .zpHomeContent .zpTrustPinned__title span,
  html body .zpHomeContent #zpShowcaseWhite .zpShowcaseWhite__title span,
  html body .zpHomeContent .zpLaptopShowcase h2 span,
  html body .zpHomeContent #zpShowcaseServices .zpSS__title em {
    background-size: 340% 100% !important;
    animation-duration: 6.8s !important;
  }
}

@media (prefers-reduced-motion: reduce) {
  html body .zpHomeContent .zh .zh__title .zh__grad,
  html body .zpHomeContent .zh .zh__svcRotMask i,
  html body .zpHomeContent .zpTrustPinned__title span,
  html body .zpHomeContent #zpShowcaseWhite .zpShowcaseWhite__title span,
  html body .zpHomeContent .zpLaptopShowcase h2 span,
  html body .zpHomeContent #zpShowcaseServices .zpSS__title em {
    animation: none !important;
    background-position: 52% 50% !important;
    will-change: auto !important;
  }
}
CSS;
}

function zp_suite_home_full_markup() {
  $map = zp_suite_home_sections_map();
  $html = '';
  foreach (zp_suite_home_order() as $key) {
    if (empty($map[$key])) continue;
    $html .= zp_suite_home_part($map[$key]['shortcode'], $map[$key]['visibility']);
  }
  $boot_bg = function_exists('zp_suite_2275_initial_bg_css') ? '<style id="zp-suite-2275-shortcode-dark-paint">' . zp_suite_2275_initial_bg_css() . '</style>' : '';
  $gradient_motion = '<style id="zp-suite-home-gradient-motion-702">' . zp_suite_home_gradient_motion_css_702() . '</style>';
  return $boot_bg . '<main id="content" class="zpHomeContent">' . $html . $gradient_motion . '</main>';
}

function zp_suite_enqueue_home_full_assets() {
  /* Header logic must be queued first so its deferred script runs before the
     home blocks' DOMContentLoaded callbacks. */
  zp_suite_enqueue_block_assets('zp_header');

  /**
   * v1.8.19:
   * Nie ładujemy tutaj wszystkich assetów z mapy globalnej, bo asset
   * podstrony /strony-internetowe-katowice/ używa tych samych klas .zpSSCard
   * i nadpisywał wygląd sekcji "Usługi i branże" na stronie głównej.
   * Home dostaje tylko assety sekcji, które faktycznie występują w [zp_home_full].
   */
  $home_asset_map = [
    'home_hero' => ['zp_home_hero'],
    'seo_intro' => ['zp_home_seo_intro'],
    'trust_logos' => ['zp_trust_logos'],
    'services_path' => ['zp_services_path', 'zp_mobile_dark_cta'],
    'featured_packages' => ['zp_home_featured_packages'],
    'laptop_showcase' => ['zp_laptop_showcase'],
    'about_experience' => ['zp_about_experience'],
    'showcase_services' => ['zp_showcase_services'],
    'seo_industries' => ['zp_seo_industries'],
    'seo_faq' => ['zp_seo_faq'],
    'contact_system' => ['zp_home_contact_form'],
  ];

  $home_asset_tags = [];
  $sections = zp_suite_home_sections_map();
  foreach (zp_suite_home_order() as $section_key) {
    if (empty($sections[$section_key]) || empty($home_asset_map[$section_key])) {
      continue;
    }
    $visibility = isset($sections[$section_key]['visibility']) ? $sections[$section_key]['visibility'] : null;
    if ($visibility && !zp_suite_section_enabled($visibility)) {
      continue;
    }
    foreach ($home_asset_map[$section_key] as $tag) {
      $home_asset_tags[] = $tag;
    }
  }

  $home_asset_tags = array_values(array_unique($home_asset_tags));

  foreach ($home_asset_tags as $tag) {
    zp_suite_enqueue_block_assets($tag);
  }

  zp_suite_enqueue_global_assets();
}

add_shortcode('zp_home_full', function() {
  zp_suite_enqueue_home_full_assets();
  return zp_suite_home_full_markup();
});

add_shortcode('zp_home', function() {
  zp_suite_enqueue_home_full_assets();
  return zp_suite_home_full_markup();
});

add_shortcode('zp_zaprojektowani_home', function() {
  zp_suite_enqueue_home_full_assets();
  return do_shortcode('[zp_home_full]');
});

add_action('init', function() {
  zp_suite_register_template_shortcode('zp_header', 'header');
  zp_suite_register_template_shortcode('zp_footer', 'footer');

  zp_suite_register_template_shortcode('zp_home_hero', 'home-hero');
  zp_suite_register_template_shortcode('zp_home_seo_intro', 'home-seo-intro');
  zp_suite_register_template_shortcode('zp_trust_logos', 'trust-logos');
  zp_suite_register_template_shortcode('zp_services_path', 'services-path');
  zp_suite_register_template_shortcode('zp_home_featured_packages', 'home-featured-packages');
  zp_suite_register_template_shortcode('zp_mobile_dark_cta', 'mobile-dark-cta');
  zp_suite_register_template_shortcode('zp_laptop_showcase', 'laptop-showcase');
  zp_suite_register_template_shortcode('zp_showcase_services', 'showcase-services');
  zp_suite_register_template_shortcode('zp_seo_industries', 'seo-industries');
  zp_suite_register_template_shortcode('zp_seo_faq', 'seo-faq');
  zp_suite_register_template_shortcode('zp_home_audit_cta', 'home-audit-cta');
  zp_suite_register_template_shortcode('zp_about_experience', 'about-experience');
  // v2.2.18: właściwy formularz 1:1 z podstrony /kontakt/ (bez hero/mapy)
  // dla home, strony-internetowe-katowice i sklepy-internetowe-katowice.
  zp_suite_register_template_shortcode('zp_contact_system', 'home-contact-form');
  zp_suite_register_template_shortcode('zp_home_contact_form', 'home-contact-form');
  zp_suite_register_template_shortcode('zp_contact_page', 'contact-page');
  zp_suite_register_template_shortcode('zp_kontakt', 'contact-page');
});


add_action('init', function() {
  if (!shortcode_exists('zp_showcase_portfolio')) {
    add_shortcode('zp_showcase_portfolio', function() {
      if (function_exists('zp_suite_enqueue_global_assets')) zp_suite_enqueue_global_assets();
      return zp_suite_render_template('legacy/legacy-portfolio');
    });
  }

  if (!shortcode_exists('zp_reviews_section')) {
    add_shortcode('zp_reviews_section', function() {
      if (function_exists('zp_suite_enqueue_global_assets')) zp_suite_enqueue_global_assets();
      return zp_suite_render_template('legacy/legacy-reviews');
    });
  }
}, 30);
