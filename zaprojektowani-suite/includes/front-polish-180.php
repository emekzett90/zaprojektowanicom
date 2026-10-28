<?php
if (!defined('ABSPATH')) { exit; }

/**
 * ZAPROJEKTOWANI SUITE v1.8.0
 * Front Polish & Performance Pack
 * - smooth loading without mobile performance degradation
 * - hero poster / video fallback
 * - smart image attributes + home image audit
 * - smooth anchors, reveal manager, Safari/iOS safe mode
 * - header lock, CLS guard, font polish, asset audit, Front Health
 */

function zp_suite_180_enabled($key, $default = '1'){
  return (string) zp_suite_opt('front_polish.' . $key, $default) !== '0';
}

function zp_suite_180_setting($key, $default = ''){
  return zp_suite_opt('front_polish.' . $key, $default);
}

add_action('wp_enqueue_scripts', function(){
  if (is_admin()) return;
  if (!zp_suite_180_enabled('enabled')) return;

  if (function_exists('zp_suite_enqueue_global_assets')) { zp_suite_enqueue_global_assets(); }

  wp_enqueue_style(
    'zp-suite-front-polish-180',
    ZP_SUITE_URL . 'assets/css/zp-front-polish-180.css',
    ['zp-suite-global'],
    zp_suite_asset_version('assets/css/zp-front-polish-180.css')
  );

  wp_enqueue_script(
    'zp-suite-front-polish-180',
    ZP_SUITE_URL . 'assets/js/zp-front-polish-180.js',
    ['zp-suite-global'],
    zp_suite_asset_version('assets/js/zp-front-polish-180.js'),
    true
  );

  wp_localize_script('zp-suite-front-polish-180', 'zpSuiteFrontPolish', [
    'smoothScroll' => zp_suite_180_enabled('smooth_scroll'),
    'reveal' => zp_suite_180_enabled('reveal_manager'),
    'safariSafe' => zp_suite_180_enabled('safari_safe'),
    'loader' => zp_suite_180_enabled('loader'),
    'videoStrategy' => sanitize_key(zp_suite_180_setting('video_strategy', 'idle')),
    'videoDelay' => max(0, (int) zp_suite_180_setting('video_delay', '900')),
    'headerOffset' => 92,
  ]);
}, 55);

add_action('wp_head', function(){
  if (is_admin()) return;
  if (!zp_suite_180_enabled('enabled')) return;

  $poster = esc_url(zp_suite_180_setting('hero_poster', ''));
  $font400 = esc_url('/wp-content/web-font/jakarta/plus-jakarta-sans-v12-latin_latin-ext-regular.woff2');
  $font600 = esc_url('/wp-content/web-font/jakarta/plus-jakarta-sans-v12-latin_latin-ext-600.woff2');
  $font700 = esc_url('/wp-content/web-font/jakarta/plus-jakarta-sans-v12-latin_latin-ext-700.woff2');

  echo "\n".'<script id="zp-front-polish-early">document.documentElement.classList.add("zp-loading");document.documentElement.classList.remove("zp-ready");</script>' . "\n";
  echo '<link rel="preload" href="'.$font600.'" as="font" type="font/woff2" crossorigin>' . "\n";
  echo '<link rel="preload" href="'.$font700.'" as="font" type="font/woff2" crossorigin>' . "\n";
  if ($poster && !(function_exists('is_front_page') && is_front_page())) {
    echo '<link rel="preload" href="'.$poster.'" as="image" fetchpriority="high">' . "\n";
  }

  $poster_css = $poster ? "url('" . esc_url($poster) . "')" : 'none';
  ?>
<style id="zp-front-polish-critical">
  :root{--zp-hero-poster:<?php echo $poster_css; ?>;--zp-safe-header-h:88px;scroll-padding-top:104px}
  html.zp-loading body{overflow-x:clip}
  .zpNewNav{min-height:88px;contain:layout style paint;}
  .zpNewNav__shell{min-height:88px;}
  .zpNewHero{min-height:440px;contain:layout paint style;}
  .zpNewHero__bg{background:#030509;background-image:var(--zp-hero-poster),linear-gradient(135deg,#020407 0%,#071426 48%,#102a4f 100%);background-size:cover;background-position:center;background-repeat:no-repeat;}
  .zpNewHero__inner{min-height:440px;}
  .zpNewHero__video:not([src]){opacity:0!important;}
  html.zp-loading .zpNewHero__copy,html.zp-loading .zpNewHero__visual{opacity:.001;transform:translate3d(0,10px,0)}
  html.zp-ready .zpNewHero__copy,html.zp-ready .zpNewHero__visual{opacity:1;transition:opacity .38s cubic-bezier(.16,1,.3,1),transform .38s cubic-bezier(.16,1,.3,1)}
  .zpFrontSkeleton{position:absolute;inset:0;pointer-events:none;z-index:3;opacity:0;transition:opacity .32s ease;background:radial-gradient(circle at 22% 18%,rgba(59,110,168,.18),transparent 34%),linear-gradient(90deg,rgba(3,5,9,.55),rgba(3,5,9,.1));}
  html.zp-loading .zpNewHero .zpFrontSkeleton{opacity:1}
  .zpSuitePreventCls img{height:auto;}
  @media(max-width:980px){.zpNewHero{min-height:540px}.zpNewHero__inner{min-height:540px}.zpNewNav,.zpNewNav__shell{min-height:74px}:root{scroll-padding-top:82px}}
</style>
  <?php
}, 0);

/**
 * HTML polish buffer: add image attrs, ALT fallbacks, hero poster and skeleton without editing each legacy template.
 */
add_action('template_redirect', function(){
  if (is_admin() || wp_doing_ajax() || is_feed()) return;
  if (!zp_suite_180_enabled('enabled')) return;
  ob_start('zp_suite_180_html_polish');
}, 1);

function zp_suite_180_html_polish($html){
  if (!is_string($html) || $html === '') return $html;

  $poster = esc_url(zp_suite_180_setting('hero_poster', ''));
  if ($poster && strpos($html, 'class="zpNewHero__video"') !== false && strpos($html, 'class="zpNewHero__video" poster=') === false) {
    $html = preg_replace('/(<video\b[^>]*class="[^"]*zpNewHero__video[^"]*"[^>]*)(>)/i', '$1 poster="'.$poster.'"$2', $html, 1);
  }

  if (strpos($html, 'class="zpNewHero__bg"') !== false && strpos($html, 'zpFrontSkeleton') === false) {
    $html = preg_replace('/(<div\s+class="zpNewHero__bg"[^>]*>)/i', '$1<span class="zpFrontSkeleton" aria-hidden="true"></span>', $html, 1);
  }

  if (zp_suite_180_enabled('smart_images')) {
    $img_index = 0;
    $html = preg_replace_callback('/<img\b[^>]*>/i', function($m) use (&$img_index){
      $tag = $m[0];
      $img_index++;
      if (stripos($tag, ' alt=') === false && function_exists('zp_seo_plan_active') && zp_seo_plan_active()) {
        // 2.3.0: media library alt or decorative alt="", never a file name.
        $src = preg_match('/src=["\']([^"\']+)["\']/i', $tag, $srcm) ? $srcm[1] : '';
        $tag = preg_replace('/<img\b/i', '<img alt="'.esc_attr(function_exists('zp_suite_seo_alt_library') ? zp_suite_seo_alt_library($src) : '').'"', $tag, 1);
      }
      if (stripos($tag, ' alt=') === false) {
        $alt = 'Zaprojektowani.com — projektowanie stron, sklepów i brandingu';
        if (preg_match('/src=["\']([^"\']+)["\']/i', $tag, $srcm)) {
          $name = basename(parse_url($srcm[1], PHP_URL_PATH) ?: '');
          $name = preg_replace('/\.(webp|png|jpe?g|gif|svg)$/i','',$name);
          $name = trim(str_replace(['-','_'], ' ', $name));
          if ($name) $alt = 'Zaprojektowani.com — ' . $name;
        }
        $tag = preg_replace('/<img\b/i', '<img alt="'.esc_attr($alt).'"', $tag, 1);
      }
      if (stripos($tag, ' decoding=') === false) {
        $tag = preg_replace('/<img\b/i', '<img decoding="async"', $tag, 1);
      }
      $above = (stripos($tag, 'zpNewHero__person') !== false || stripos($tag, 'zpNewNav__logo') !== false || $img_index <= 2);
      if (stripos($tag, ' loading=') === false) {
        $tag = preg_replace('/<img\b/i', '<img loading="'.($above?'eager':'lazy').'"', $tag, 1);
      }
      if ($above && stripos($tag, ' fetchpriority=') === false) {
        $tag = preg_replace('/<img\b/i', '<img fetchpriority="high"', $tag, 1);
      }
      return $tag;
    }, $html);
  }

  if (zp_suite_180_enabled('cls_guard') && stripos($html, '<body') !== false && stripos($html, 'zpSuitePreventCls') === false) {
    $html = preg_replace('/<body([^>]*)>/i', '<body$1 class="zpSuitePreventCls">', $html, 1);
  }

  return $html;
}

