<?php
if (!defined('ABSPATH')) { exit; }

/**
 * ZP Suite 2.4.0 — one project count everywhere (audit point 17).
 *
 * The hero sections had "120+ projektów" hard-coded while the trust widget and the about
 * section used "Liczba realizacji" from the plugin settings (114). The service heroes and
 * the logo section now read the same setting, so the number is changed in one place.
 */

function zp_seo_plan_projects_count(): int {
  $count = function_exists('zp_suite_opt') ? (int) zp_suite_opt('stats.projects_count', zp_suite_opt('hero.projects_count', 114)) : 114;
  return $count > 0 ? $count : 114;
}

/** Replaces the hard-coded counts in service page HTML with the setting. */
function zp_seo_plan_apply_projects_count(string $html): string {
  $n = (string) zp_seo_plan_projects_count();
  $html = preg_replace_callback('~<b data-count="120" data-suffix="\+">(0|120\+)</b><span>projektów</span>~', static function ($m) use ($n) {
    return '<b data-count="' . $n . '" data-suffix="+">' . ($m[1] === '0' ? '0' : $n . '+') . '</b><span>projektów</span>';
  }, $html);
  $html = str_replace(['ponad <em>114 marek</em>', '<strong>114+</strong>'], ['ponad <em>' . $n . ' marek</em>', '<strong>' . $n . '+</strong>'], $html);
  return $html;
}

add_filter('do_shortcode_tag', function ($output, $tag) {
  static $tags = ['zp_strony_internetowe_katowice', 'zp_page_strony_katowice', 'zp_sklepy_internetowe_katowice', 'zp_page_sklepy_katowice', 'zp_logo_branding_katowice', 'zp_page_logo_branding_katowice'];
  return is_string($output) && in_array($tag, $tags, true) ? zp_seo_plan_apply_projects_count($output) : $output;
}, 20, 2);
