<?php
if (!defined('ABSPATH')) { exit; }

/* v2.2.687: force refresh page assets — subtelniejszy orb hero, pływające kuleczki i korekta mobile następny krok. */
add_action('init', function(){
  $ver_key = 'zp_suite_logo_branding_katowice_hero_live_version';
  if (get_option($ver_key) !== '2.2.701') {
    if (function_exists('wp_cache_flush')) { wp_cache_flush(); }
    update_option($ver_key, '2.2.701', false);
  }
}, 7);


/**
 * ZAPROJEKTOWANI SUITE — Logo & Branding Katowice page v1.2
 * Shortcodes:
 * [zp_logo_branding_katowice]
 * [zp_page_logo_branding_katowice]
 *
 * v2.2.93: proces i FAQ przywrócone 1:1 z plików źródłowych; fix uciętych ogonków w headingu logotypów.
 * v2.2.154: dodany blok SEO boost pod projektowanie logo Katowice, identyfikację wizualną i CTA.
 */

function zp_suite_logo_branding_katowice_sections_map(){
  return [
    'hero'      => ['label'=>'Hero', 'file'=>'hero.html'],
    'logos'     => ['label'=>'Logotypy klientów', 'file'=>'logos.html'],
    'trust'     => ['label'=>'Zaufanie / opinie', 'file'=>'trust.html'],
    'portfolio' => ['label'=>'Portfolio logo', 'file'=>'portfolio.html'],
    'seo_boost' => ['label'=>'SEO boost / Logo Katowice', 'file'=>'seo-boost.html'],
    'packages'  => ['label'=>'Pakiety logo i branding', 'file'=>'packages.html'],
    'process'   => ['label'=>'Proces', 'file'=>'process.html'],
    'faq'       => ['label'=>'FAQ', 'file'=>'faq.html'],
  ];
}

function zp_suite_logo_branding_katowice_default_section($key){
  $m = zp_suite_logo_branding_katowice_sections_map();
  $file = ZP_SUITE_PATH.'templates/logo-branding-katowice/sections/'.($m[$key]['file'] ?? '');
  return file_exists($file) ? file_get_contents($file) : '';
}

function zp_suite_logo_branding_katowice_get_section($key){
  /**
   * Sections are file-based for performance and 1:1 consistency with the supplied HTML widgets.
   * If a CMS editor is added later, this function is ready to be extended.
   */
  return zp_suite_logo_branding_katowice_default_section($key);
}

/**
 * Keep the approved landing-page cards and add missing logo projects from the
 * homepage CMS. Both logo service pages use this template; new projects must
 * not require a second, manually maintained copy of their case-study content.
 */
function zp_suite_logo_branding_katowice_portfolio($html){
  $result = ['html' => $html, 'items' => []];
  if (!function_exists('zp_suite_portfolio_items') || !preg_match('#<div\b[^>]*class="portfolio-grid"[^>]*>#s', $html, $grid)) {
    return $result;
  }

  preg_match_all('#\bdata-project="([^"]+)"#', $html, $existing);
  $seen = [];
  foreach ($existing[1] as $brand) {
    $seen[zp_suite_portfolio_content_key(html_entity_decode($brand, ENT_QUOTES, 'UTF-8'))] = true;
  }

  $cards = '';
  $is_en = function_exists('zpl_language') && zpl_language() === 'en';
  $tool_icons = [
    'Illustrator' => 'adobe-illustrator.svg',
    'Photoshop' => 'adobe-photoshop.svg',
    'InDesign' => 'adobe-indesign.svg',
  ];
  foreach (zp_suite_portfolio_items('logo') as $item) {
    $brand = trim((string)($item['brand'] ?? ''));
    $key = zp_suite_portfolio_content_key($brand);
    if (!empty($item['isCta']) || $brand === '' || empty($item['img']) || isset($seen[$key])) {
      continue;
    }
    $seen[$key] = true;
    $result['items'][] = $item;
    $image = esc_url($item['img']);
    $category = esc_html($item['tag'] ?? '');
    $subtitle = esc_html($item['sub'] ?? '');
    $cards .= '<article class="project-card" tabindex="0" role="button" data-project="' . esc_attr($brand) . '"'
      . ' data-category="' . esc_attr($item['tag'] ?? '') . '" data-copy="' . esc_attr($item['desc'] ?? '') . '" data-image="' . $image . '">'
      . '<div class="project-image"><img src="' . $image . '" alt="' . esc_attr($brand . ' — ' . ($item['type'] ?? '')) . '" loading="lazy" decoding="async"></div>'
      . '<div class="project-copy"><div class="project-meta"><span>' . $category . '</span><i>↗</i></div>'
      . '<h3>' . esc_html($brand) . '</h3><p>' . $subtitle . '</p><div class="project-card__footer"><div class="project-scope">';
    foreach (array_slice((array)($item['meta'] ?? []), 0, 3) as $tag) {
      $cards .= '<span>' . esc_html($tag) . '</span>';
    }
    $cards .= '</div><div class="project-tools">';
    foreach ((array)($item['tools'] ?? []) as $tool) {
      if (isset($tool_icons[$tool])) {
        $cards .= '<img src="' . esc_url(ZP_SUITE_URL . 'assets/logo-branding/' . $tool_icons[$tool]) . '" alt="' . esc_attr($tool) . '" title="' . esc_attr('Adobe ' . $tool) . '" loading="lazy" decoding="async">';
      }
    }
    $cards .= '</div></div></div></article>';
  }

  $html = str_replace($grid[0], $grid[0] . $cards, $html);
  $count = 0;
  $html = preg_replace('#\s+data-rail-index="\d+"#', '', $html);
  $html = preg_replace_callback('#<article\b(?=[^>]*class="project-card")#', static function($m) use (&$count){
    return $m[0] . ' data-rail-index="' . sprintf('%02d', ++$count) . '"';
  }, $html);
  $label = $is_en ? 'Portfolio / ' . $count . ' brand stories' : 'Portfolio / ' . $count . ' historii marek';
  $html = preg_replace('#Portfolio / \d+ historii marek#', esc_html($label), $html, 1);
  $html = preg_replace('#(class="portfolio-rail-progress"[^>]*aria-valuemax=")\d+#s', '${1}' . $count, $html, 1);
  $html = preg_replace('#(class="portfolio-rail-count"[^>]*>)01 / \d+#', '${1}01 / ' . sprintf('%02d', $count), $html, 1);
  $result['html'] = $html;
  return $result;
}

function zp_suite_logo_branding_katowice_clean_html($html){
  $html = str_replace('http://zaprojektowani.com/', 'https://zaprojektowani.com/', (string)$html);
  $html = preg_replace('#<script\s+src=["\'][^"\']*lucide\.min\.js[^"\']*["\']\s*></script>#i', '', $html);
  if (function_exists('zp_suite_mobile_hero_strip_video')) {
    $html = zp_suite_mobile_hero_strip_video($html);
  }
  if (function_exists('zp_suite_defer_hero_video_sources')) {
    $html = zp_suite_defer_hero_video_sources($html);
  }
  $html = zp_suite_logo_branding_katowice_protect_images($html);
  return $html;
}

/**
 * v2.2.685 — ochrona obrazów podstrony przed podmianą na miniatury.
 *
 * Problem: WordPress (wp_filter_content_tags) i wtyczki optymalizujące/lazy-load
 * dokładają srcset/sizes albo podmieniają src na wariant typu
 * ...akoya_logo_compressed-150x150.webp. W portfolio dawało to rozmyte miniatury,
 * a przy wyciętych zdjęciach postaci re-enkodowany plik potrafi zgubić kanał alfa,
 * przez co dookoła sylwetki widać prostokątny cień.
 *
 * Rozwiązanie: każdemu <img> na tej podstronie nadajemy jednokandydatowy srcset
 * (sam oryginał) oraz znaczniki pomijania lazy-loadu używane już przez tę wtyczkę
 * (skip-lazy / no-litespeed-lazyload / data-nitro-no-lazy). Jednokandydatowy srcset
 * sprawia, że WordPress nie dokłada własnego, a przeglądarka nie ma czego wybrać.
 */
function zp_suite_logo_branding_katowice_protect_images($html){
  if (strpos((string) $html, '<img') === false) {
    return $html;
  }

  return preg_replace_callback('#<img\b[^>]*>#i', function($m){
    $tag = $m[0];

    /* Nie dotykamy tagów, które ktoś już zabezpieczył. */
    if (strpos($tag, 'data-zp-no-thumb') !== false) {
      return $tag;
    }

    if (!preg_match('#\ssrc=(["\'])(.*?)\1#i', $tag, $src)) {
      return $tag;
    }
    $url = $src[2];

    /* Gdyby w źródle został wariant -800x600 — wracamy do oryginału. */
    $orig = preg_replace('#-\d{2,5}x\d{2,5}(\.(?:webp|png|jpe?g|gif|avif))$#i', '$1', $url);
    if ($orig !== $url) {
      $tag = str_replace($src[0], ' src="' . $orig . '"', $tag);
      $url = $orig;
    }

    /* Istniejące srcset/sizes zastępujemy jednym kandydatem = oryginał. */
    $tag = preg_replace('#\s(?:srcset|sizes|data-srcset|data-sizes)=(["\']).*?\1#i', '', $tag);

    $add = ' srcset="' . esc_attr($url) . ' 4096w" sizes="100vw"'
         . ' data-zp-no-thumb="1" data-no-lazy="1" data-skip-lazy="1" data-nitro-no-lazy="1"';

    /* Klasy pomijania lazy-loadu dokładamy do istniejącego atrybutu class. */
    if (preg_match('#\sclass=(["\'])(.*?)\1#i', $tag, $cls)) {
      $tag = str_replace(
        $cls[0],
        ' class="' . $cls[2] . ' skip-lazy no-lazy no-litespeed-lazyload"',
        $tag
      );
    } else {
      $add .= ' class="skip-lazy no-lazy no-litespeed-lazyload"';
    }

    return rtrim(substr($tag, 0, -1), '/ ') . $add . '>';
  }, $html);
}

function zp_suite_logo_branding_katowice_render(){
  if (function_exists('zp_suite_enqueue_block_assets')) {
    zp_suite_enqueue_block_assets('zp_logo_branding_katowice');
  }
  return zp_suite_render_template('logo-branding-katowice/page');
}


/**
 * Register shortcode directly. v2.2.92 fix:
 * In v2.2.91 the renderer existed, but the shortcode was not registered,
 * so Elementor/WordPress printed nothing for [zp_logo_branding_katowice].
 */
add_action('init', function(){
  if (!shortcode_exists('zp_logo_branding_katowice')) {
    add_shortcode('zp_logo_branding_katowice', 'zp_suite_logo_branding_katowice_render');
  }

  if (!shortcode_exists('zp_page_logo_branding_katowice')) {
    add_shortcode('zp_page_logo_branding_katowice', 'zp_suite_logo_branding_katowice_render');
  }
}, 20);
