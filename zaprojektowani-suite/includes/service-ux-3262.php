<?php
if (!defined('ABSPATH')) { exit; }

/** Service-page presentation only. Keeps the 3.2.61 copy, packages and tracking intact. */
function zp_service_ux3262_wordpress_scope(string $html): string {
  if (strpos($html, 'data-zp-wordpress-scope') !== false || !function_exists('zp_seo_content_in_section')) { return $html; }
  return zp_seo_content_in_section($html, 'id="zakres"', static function ($section) {
    $section = (string) preg_replace('~(<section\b[^>]*)(>)~i', '$1 data-zp-wordpress-scope="1"$2', $section, 1);
    return (string) preg_replace_callback('~(<div class="section-head">)(.*?)(</div>\s*)(<div class="scope-grid">)~s', static function ($m) {
      $copy = $m[2];
      $lead = '';
      $copy = (string) preg_replace_callback('~<p class="section-head__aside\b[^>]*>(.*?)</p>~s', static function ($p) use (&$lead) {
        $lead = '<p class="section-head__aside zpServiceScope__lead">' . $p[1] . '</p>';
        return '';
      }, $copy, 1);
      // The original explanatory paragraph sits under the heading, leaving the right column for the team.
      $copy = (string) preg_replace('~(</h2>)(\s*</div>)~s', '$1' . $lead . '$2', $copy, 1);
      $assets = ZP_SUITE_URL . 'assets/img/contact-team/';
      $alt = function_exists('zp_seo_plan_is_en') && zp_seo_plan_is_en()
        ? 'Zaprojektowani team working on a website' : 'Zespół Zaprojektowani pracujący nad stroną internetową';
      $photo = '<figure class="zpServiceScope__photo"><img src="' . esc_url($assets . 'zespol-laptop-1x.webp') . '" srcset="'
        . esc_attr($assets . 'zespol-laptop-1x.webp') . ' 491w, ' . esc_attr($assets . 'zespol-laptop-2x.webp') . ' 955w" sizes="(max-width:760px) 78vw, (max-width:1100px) 360px, 520px" width="955" height="934" alt="' . esc_attr($alt) . '" loading="lazy" decoding="async"></figure>';
      return $m[1] . $copy . $photo . $m[3] . $m[4];
    }, $section, 1);
  });
}

add_filter('do_shortcode_tag', static function ($output, $tag) {
  if (!is_string($output) || !function_exists('zp_seo_service_shortcodes') || !isset(zp_seo_service_shortcodes()[$tag])) { return $output; }
  if (!function_exists('zp_seo_service_variant') || zp_seo_service_variant() !== 'content-strony-wordpress') { return $output; }
  return zp_service_ux3262_wordpress_scope($output);
}, 88, 2);

add_action('wp_enqueue_scripts', static function () {
  if (!function_exists('zp_seo_service_variant') || zp_seo_service_variant() !== 'content-strony-wordpress') { return; }
  wp_enqueue_style('zp-service-ux-3262', ZP_SUITE_URL . 'assets/css/service-ux-3262.css', [], ZP_SUITE_VERSION);
}, 1002);
