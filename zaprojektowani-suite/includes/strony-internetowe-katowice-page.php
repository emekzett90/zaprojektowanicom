<?php
if (!defined('ABSPATH')) { exit; }


/**
 * v2.2.739 — podmiana wizualizacji sklepu w sekcji oferta na Świat Grilli; wcześniejsze poprawki zachowane.
 * Used before the shortcode is rendered, so its CSS is present in <head> and
 * the browser never paints the raw watermark/content first.
 */
function zp_suite_strony_internetowe_katowice_v2_request(){
  if (is_admin() || wp_doing_ajax() || wp_is_json_request()) { return false; }
  if (function_exists('is_page') && is_page('strony-internetowe-katowice')) { return true; }
  if (!is_singular()) { return false; }
  $post = get_post();
  if (!$post) { return false; }
  $content = (string) $post->post_content;
  return has_shortcode($content, 'zp_strony_internetowe_katowice')
    || has_shortcode($content, 'zp_page_strony_katowice')
    || has_shortcode($content, 'zp_page_strony_internetowe_katowice');
}

/* Enqueue the v2 bundle late in wp_enqueue_scripts, but still before
   wp_head prints styles. This preserves the FOUC fix while restoring the
   exact cascade order of v2.2.689, where the landing stylesheet won over
   theme/global button and image rules. */
add_action('wp_enqueue_scripts', function(){
  if (!zp_suite_strony_internetowe_katowice_v2_request()) { return; }
  if (function_exists('zp_suite_enqueue_block_assets')) {
    zp_suite_enqueue_block_assets('zp_strony_internetowe_katowice_v2');
  }
}, 999);

/* Tiny boot lock: if cache/optimization ever delays the full stylesheet, show
   a stable dark canvas instead of a giant natural-size logo and raw text. The
   real stylesheet overrides visibility as soon as it is applied. */
add_action('wp_head', function(){
  if (!zp_suite_strony_internetowe_katowice_v2_request()) { return; }
  $hero = ZP_SUITE_URL . 'assets/strony-internetowe/hero-laptop.webp';
  echo "\n<style id=\"zp-si-v2-first-paint-lock\">"
    . "#zp-strony-internetowe-katowice{visibility:hidden;min-height:100vh;background:#030407;color:#fff}"
    . "body.zp-home-bg-boot .elementor-shortcode:has(#zp-strony-internetowe-katowice){min-height:100vh;background:#030407}"
    . "</style>\n";
  printf(
    "<link rel=\"preload\" as=\"image\" href=\"%s\" type=\"image/webp\" fetchpriority=\"high\">\n",
    esc_url($hero)
  );
}, -999998);

/* Cache bust only once after update. */
add_action('init', function(){
  $key = 'zp_suite_strony_internetowe_katowice_v2_live_version';
  if (get_option($key) !== '2.2.818') {
    if (function_exists('wp_cache_flush')) { wp_cache_flush(); }
    update_option($key, '2.2.818', false);
  }
}, 7);

/**
 * ZAPROJEKTOWANI SUITE — Strony internetowe Katowice v2 (2.2.694)
 *
 * Nowa wersja podstrony, zbudowana tak samo jak logo-branding-katowice:
 * jeden plik body.html 1:1 z zatwierdzonego prototypu + globalny header,
 * globalna stopka i wspólny formularz [zp_contact_system].
 *
 * WAŻNE — relacja do starego renderera (includes/katowice-page.php):
 * Tamten moduł ma własny CMS w panelu (Podstrony → Strony internetowe
 * Katowice) i skład sekcji z templates/katowice/sections/*.html. Ten plik
 * ładowany jest PO nim, więc jego add_shortcode() nadpisuje rejestracje
 * `zp_strony_internetowe_katowice` i `zp_page_strony_katowice`. Stary kod,
 * opcje w bazie i ekran w panelu zostają nietknięte — nic nie kasujemy,
 * żeby dało się wrócić — ale front renderuje już nową wersję, więc edycja
 * treści przez tamten ekran nie ma wpływu na wygląd strony.
 */

function zp_suite_strony_internetowe_katowice_clean_html($html){
  $html = str_replace('http://zaprojektowani.com/', 'https://zaprojektowani.com/', (string) $html);
  /* Lucide ładuje wtyczka globalnie — ewentualny tag z HTML-a wycinamy. */
  $html = preg_replace('#<script\s+src=["\'][^"\']*lucide\.min\.js[^"\']*["\']\s*></script>#i', '', $html);
  if (function_exists('zp_suite_mobile_hero_strip_video')) {
    $html = zp_suite_mobile_hero_strip_video($html);
  }
  if (function_exists('zp_suite_defer_hero_video_sources')) {
    $html = zp_suite_defer_hero_video_sources($html);
  }
  $html = zp_suite_strony_internetowe_katowice_protect_images($html);
  return $html;
}

/**
 * Ochrona obrazów przed podmianą na miniatury — identyczna jak w
 * logo-branding-katowice. WordPress i wtyczki optymalizujące potrafią
 * podmienić src na wariant ...-150x150.webp albo dołożyć wielokandydatowy
 * srcset; przy wyciętych zdjęciach postaci re-enkodowany plik gubi kanał
 * alfa i dookoła sylwetki pojawia się prostokątny cień.
 */
function zp_suite_strony_internetowe_katowice_protect_images($html){
  if (strpos((string) $html, '<img') === false) {
    return $html;
  }

  return preg_replace_callback('#<img\b[^>]*>#i', function($m){
    $tag = $m[0];

    if (strpos($tag, 'data-zp-no-thumb') !== false) {
      return $tag;
    }
    if (!preg_match('#\ssrc=(["\'])(.*?)\1#i', $tag, $src)) {
      return $tag;
    }
    $url = $src[2];

    $orig = preg_replace('#-\d{2,5}x\d{2,5}(\.(?:webp|png|jpe?g|gif|avif))$#i', '$1', $url);
    if ($orig !== $url) {
      $tag = str_replace($src[0], ' src="' . $orig . '"', $tag);
      $url = $orig;
    }

    $tag = preg_replace('#\s(?:srcset|sizes|data-srcset|data-sizes)=(["\']).*?\1#i', '', $tag);

    $add = ' srcset="' . esc_attr($url) . ' 4096w" sizes="100vw"'
         . ' data-zp-no-thumb="1" data-no-lazy="1" data-skip-lazy="1" data-nitro-no-lazy="1"';

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

function zp_suite_strony_internetowe_katowice_render(){
  if (function_exists('zp_suite_enqueue_block_assets')) {
    zp_suite_enqueue_block_assets('zp_strony_internetowe_katowice_v2');
    zp_suite_enqueue_block_assets('zp_contact_system');
  }
  return zp_suite_render_template('strony-internetowe-katowice/page');
}

/**
 * Rejestracja na init z późnym priorytetem, żeby na pewno wygrać
 * z rejestracją ze starego katowice-page.php.
 */
add_action('init', function(){
  remove_shortcode('zp_strony_internetowe_katowice');
  remove_shortcode('zp_page_strony_katowice');
  add_shortcode('zp_strony_internetowe_katowice', 'zp_suite_strony_internetowe_katowice_render');
  add_shortcode('zp_page_strony_katowice', 'zp_suite_strony_internetowe_katowice_render');
  add_shortcode('zp_page_strony_internetowe_katowice', 'zp_suite_strony_internetowe_katowice_render');
}, 30);

/**
 * Stary moduł (includes/katowice-page.php) ma własny hook wp_enqueue_scripts,
 * który na tej podstronie doładowuje assets/css/blocks/strony-katowice.css
 * (~246 KB) i strony-katowice.js starego układu sekcyjnego. Nowa wersja ich
 * nie używa, więc zdejmujemy je z kolejki — inaczej strona ciągnęłaby dwa
 * komplety stylów i skryptów naraz.
 */
add_action('wp_enqueue_scripts', function(){
  if (is_admin() || !is_singular()) { return; }
  $post = get_post();
  if (!$post) { return; }
  $sc = (string) $post->post_content;
  $uses_new = has_shortcode($sc, 'zp_strony_internetowe_katowice')
    || has_shortcode($sc, 'zp_page_strony_katowice')
    || has_shortcode($sc, 'zp_page_strony_internetowe_katowice');
  if (!$uses_new) { return; }
  wp_dequeue_style('zp-suite-strony-katowice');
  wp_dequeue_script('zp-suite-strony-katowice');
}, 100);
