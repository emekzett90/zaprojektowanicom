<?php
if (!defined('ABSPATH')) { exit; }

/** 2.7.7 — requested service visuals, scoped to the three published service paths. */
function zp_seo_service_hero_path(): string {
  if (is_admin()) { return ''; }
  $path = zp_seo_plan_path();
  return (string) preg_replace('~^/en/~', '/', $path);
}

/** Shared by the hero, its preload and the related-service cards. */
function zp_seo_service_hero_image(string $path): string {
  $path = (string) preg_replace('~^/en/~', '/', $path);
  $images = [
    '/tworzenie-stron-internetowych/' => 'https://zaprojektowani.com/wp-content/uploads/2026/10/Polerstone-na-skalnej-podstawie.webp',
    '/tworzenie-sklepow-internetowych/' => 'https://zaprojektowani.com/wp-content/uploads/2026/10/Swiat-Grili-na-smartfonie-i-laptopie.webp',
    '/strony-wordpress/' => ZP_SUITE_URL . 'assets/strony-internetowe/hero-laptop.webp',
  ];
  return $images[$path] ?? '';
}

/** Update source candidates together: an older srcset must not win over the new src. */
function zp_seo_service_hero_image_tag(string $tag, string $src, string $alt, int $width, int $height): string {
  $tag = (string) preg_replace('~\s(?:src|srcset|sizes|data-src|data-srcset|data-lazy-src|data-lazy-srcset|width|height|alt|loading|fetchpriority)\s*=\s*(["\']).*?\1~is', '', $tag);
  return rtrim(substr($tag, 0, -1), '/ ') . ' src="' . esc_url($src) . '" srcset="' . esc_attr($src) . ' ' . $width . 'w" sizes="(max-width:1100px) 100vw, 64vw" width="' . $width . '" height="' . $height . '" alt="' . esc_attr($alt) . '" loading="eager" fetchpriority="high">';
}

function zp_seo_wordpress_hero_technologies(): string {
  $assets = ZP_SUITE_URL . 'assets/strony-internetowe/';
  $logos = [
    ['WordPress', 'https://zaprojektowani.com/wp-content/uploads/2026/10/WordPress_blue_logo.svg-1.webp', 'wordpress'],
    ['WooCommerce', $assets . 'tech-woocommerce.svg', 'woocommerce'],
    ['PHP', $assets . 'tech-php.svg', 'php'],
    ['Elementor', $assets . 'tool-elementor.png', 'elementor'],
  ];
  $group = '';
  foreach ($logos as $logo) {
    $group .= '<span class="zpWpTech__logo zpWpTech__logo--' . esc_attr($logo[2]) . '" role="listitem"><span class="zpWpTech__mark"><img src="' . esc_url($logo[1]) . '" width="48" height="48" alt="" decoding="async"></span><span>' . esc_html($logo[0]) . '</span></span>';
  }
  return '<div class="zpWpTech" data-zp-wp-tech>'
    . '<div class="zpWpTech__head"><p>Technologie, na których budujemy</p><button class="zpWpTech__pause" type="button" aria-pressed="false" aria-label="Zatrzymaj przewijanie logotypów"><span data-zp-wp-tech-pause>Zatrzymaj</span><span data-zp-wp-tech-resume>Wznów</span></button></div>'
    . '<div class="zpWpTech__window"><div class="zpWpTech__track"><div class="zpWpTech__group" role="list" aria-label="Technologie WordPress">' . $group . '</div><div class="zpWpTech__group zpWpTech__group--duplicate" aria-hidden="true">' . $group . '</div></div></div></div>';
}

/** Runs after the existing service copy transform; no edits to the shared Katowice templates. */
function zp_seo_service_hero_transform(string $html, string $path): string {
  $path = (string) preg_replace('~^/en/~', '/', $path);
  if (zp_seo_service_hero_image($path) === '') { return $html; }
  $shop = $path === '/tworzenie-sklepow-internetowych/';
  $marker = $shop ? 'id="zhHero"' : 'id="start"';
  $html = zp_seo_content_in_section($html, $marker, static function ($section) use ($path, $shop) {
    if ($path === '/strony-wordpress/') {
      if (strpos($section, 'data-zp-wp-tech') !== false) { return $section; }
      $section = (string) preg_replace('~(<section\b[^>]*)(>)~i', '$1 data-zp-wordpress-hero="1"$2', $section, 1);
      return (string) preg_replace_callback('~<div\b[^>]*class=(["\'])hero-proof\1[^>]*>.*?</div>~s', static function ($match) {
        return $match[0] . zp_seo_wordpress_hero_technologies();
      }, $section, 1);
    }
    if (strpos($section, 'data-zp-service-visual=') === false) {
      $section = (string) preg_replace('~(<section\b[^>]*)(>)~i', '$1 data-zp-service-visual="' . ($shop ? 'shops' : 'websites') . '"$2', $section, 1);
    }
    return (string) preg_replace_callback('~<img\b[^>]*>~i', static function ($m) use ($path, $shop) {
      $tag = $m[0];
      $target = $shop ? strpos($tag, 'laptop_sklepy_internetowe_katowice') !== false : (bool) preg_match('~\bclass=(["\'])[^"\']*\bhero-shot\b~', $tag);
      if (!$target) { return $tag; }
      $alt = $shop ? 'Świat Grilli — sklep internetowy na smartfonie i laptopie' : 'Polerstone — projekt strony internetowej na laptopie i skalnej podstawie';
      // The mobile shop mockup is already an aria-hidden duplicate of the desktop image.
      if ($shop && preg_match('~\balt=(["\'])\1~', $tag)) { $alt = ''; }
      return zp_seo_service_hero_image_tag($tag, zp_seo_service_hero_image($path), $alt, $shop ? 1717 : 1590, $shop ? 916 : 989);
    }, $section);
  });
  if ($shop) {
    // The shop template prints its preload before the hero section.
    $html = (string) preg_replace_callback('~<link\b[^>]*>~i', static function ($m) use ($path) {
      return str_replace('https://zaprojektowani.com/wp-content/uploads/2026/05/laptop_sklepy_internetowe_katowice-scaled.webp', esc_url(zp_seo_service_hero_image($path)), $m[0]);
    }, $html);
  }
  return $html;
}

add_filter('do_shortcode_tag', static function ($output, $tag) {
  if (!is_string($output) || !isset(zp_seo_service_shortcodes()[$tag])) { return $output; }
  return zp_seo_service_hero_transform($output, zp_seo_service_hero_path());
}, 25, 2);

add_action('wp_enqueue_scripts', static function () {
  $path = zp_seo_service_hero_path();
  if (!in_array($path, ['/strony-wordpress/', '/tworzenie-sklepow-internetowych/'], true)) { return; }
  wp_enqueue_style('zp-service-heroes-277', ZP_SUITE_URL . 'assets/css/blocks/service-heroes.css', [], '2.7.7');
  if ($path === '/strony-wordpress/') {
    wp_enqueue_script('zp-wordpress-hero-277', ZP_SUITE_URL . 'assets/js/blocks/wordpress-hero.js', [], '2.7.7', true);
  }
}, 1001);
