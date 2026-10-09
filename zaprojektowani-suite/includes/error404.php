<?php
if (!defined('ABSPATH')) { exit; }

/**
 * Zaprojektowani Suite — styled 404 page.
 * Renders a full, branded 404 document so broken URLs never show a raw theme page.
 */

add_filter('pre_get_document_title', function($title){
  if (is_404()) {
    return 'Nie znaleziono strony | Zaprojektowani.com';
  }
  return $title;
}, 20);

function zp_suite_render_404_page() {
  // 2.9.13: an address the SEO plan marked as gone (seo-plan/redirects.php) keeps its 410 under the styled page;
  // before, this 404 replaced it, so Google kept retrying those addresses.
  status_header(http_response_code() === 410 ? 410 : 404);
  nocache_headers();

  if (function_exists('zp_suite_enqueue_global_assets')) {
    zp_suite_enqueue_global_assets();
  }
  if (function_exists('zp_suite_enqueue_block_assets')) {
    zp_suite_enqueue_block_assets('zp_header');
    zp_suite_enqueue_block_assets('zp_footer');
  }

  wp_enqueue_style(
    'zp-suite-404',
    ZP_SUITE_URL . 'assets/css/zp-404.css',
    ['zp-suite-global'],
    function_exists('zp_suite_asset_version') ? zp_suite_asset_version('assets/css/zp-404.css') : ZP_SUITE_VERSION
  );

  add_action('wp_head', function(){
    $header = ZP_SUITE_PATH . 'assets/css/blocks/header.css';
    if (file_exists($header) && empty($GLOBALS['zp_suite_header_css_printed_in_head'])) {
      $GLOBALS['zp_suite_header_css_printed_in_head'] = true;
      // v2.2.505: critical inline + cached async sheet (no 570 KB inline). Same position => 1:1.
      $href = function_exists('zp_suite_asset_version')
        ? add_query_arg('ver', zp_suite_asset_version('assets/css/blocks/header.css'), ZP_SUITE_URL . 'assets/css/blocks/header.css')
        : ZP_SUITE_URL . 'assets/css/blocks/header.css';
      if (function_exists('zp_suite_header_critical_css')) {
        echo "\n<style id=\"zp-suite-header-head-critical-css\">\n" . zp_suite_header_critical_css() . "\n</style>\n";
      }
      printf("<link rel=\"preload\" as=\"style\" href=\"%s\">\n", esc_url($href));
      printf("<link rel=\"stylesheet\" id=\"zp-suite-header-full-css\" href=\"%s\" media=\"print\" onload=\"this.media='all';this.onload=null\">\n", esc_url($href));
      printf("<noscript><link rel=\"stylesheet\" href=\"%s\"></noscript>\n", esc_url($href));
    }
  }, 0);

  $home = home_url('/');
  $contact = home_url('/kontakt/');
  $www = home_url('/strony-internetowe-katowice/');
  $shop = home_url('/sklepy-internetowe-katowice/');
  $brand = home_url('/logo-branding-katowice/');
  $ads = home_url('/kampanie-reklamowe/');

  ?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php wp_head(); ?>
</head>
<body <?php body_class('zpSuite404Body'); ?>>
<?php wp_body_open(); ?>
<?php echo do_shortcode('[zp_header]'); ?>
<main class="zp404" id="zp404" aria-labelledby="zp404Title">
  <div class="zp404__bg" aria-hidden="true"></div>
  <div class="zp404__mark" aria-hidden="true">404</div>

  <section class="zp404__inner">
    <div class="zp404__copy">
      <span class="zp404__eyebrow">Błąd 404 / tej strony nie ma</span>
      <h1 id="zp404Title">Ta podstrona zniknęła, ale możemy szybko wrócić na właściwą ścieżkę.</h1>
      <p>
        Wygląda na to, że adres jest nieaktualny albo został przeniesiony podczas przebudowy serwisu. Wybierz jedną z głównych usług Zaprojektowani.com albo napisz do nas — pomożemy dobrać stronę, sklep, branding lub kampanię.
      </p>
      <div class="zp404__actions" aria-label="Najważniejsze akcje">
        <a class="zp404__btn zp404__btn--primary" href="<?php echo esc_url($home); ?>">
          <span>Wróć na stronę główną</span>
          <i data-lucide="arrow-up-right"></i>
        </a>
        <a class="zp404__btn zp404__btn--ghost" href="<?php echo esc_url($contact); ?>">
          <span>Poproś o wycenę</span>
          <i data-lucide="message-circle"></i>
        </a>
      </div>
    </div>

    <div class="zp404__panel" aria-label="Szybkie przejścia">
      <div class="zp404__panelTop">
        <span>Najczęściej wybierane</span>
        <strong>Przejdź do oferty</strong>
      </div>
      <nav class="zp404__links" aria-label="Główne sekcje oferty">
        <a href="<?php echo esc_url($www); ?>"><i data-lucide="monitor"></i><span>Strony internetowe Katowice</span></a>
        <a href="<?php echo esc_url($shop); ?>"><i data-lucide="shopping-cart"></i><span>Sklepy WooCommerce</span></a>
        <a href="<?php echo esc_url($brand); ?>"><i data-lucide="pen-tool"></i><span>Logo i branding</span></a>
        <a href="<?php echo esc_url($ads); ?>"><i data-lucide="megaphone"></i><span>Kampanie Meta Ads</span></a>
      </nav>
      <div class="zp404__note">
        <i data-lucide="search-check"></i>
        <p>Jeżeli szukasz konkretnego artykułu lub realizacji, wróć na stronę główną albo skorzystaj z kontaktu — przekierujemy Cię we właściwe miejsce.</p>
      </div>
    </div>
  </section>
</main>
<?php echo do_shortcode('[zp_footer]'); ?>
<?php wp_footer(); ?>
</body>
</html><?php
  exit;
}

add_action('template_redirect', function(){
  if (is_404()) {
    zp_suite_render_404_page();
  }
}, 99);
