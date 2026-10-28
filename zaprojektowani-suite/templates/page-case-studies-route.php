<?php
/** Shared native page/post template: the theme remains the single site-shell owner. */
if (!defined('ABSPATH')) { exit; }
$zp_case_key = ZP_Case_Pages_20261018::current_key();
if ($zp_case_key === '' || !function_exists('zp_cs18_render')) {
    wp_die(esc_html__('Nie znaleziono strony.', 'zaprojektowani-suite'), '', ['response'=>404]);
}
// Dictionary preparation is pure and precedes Rank Math/wp_head and the native form's once guard.
ZP_Case_Pages_20261018::prepare($zp_case_key);
$zp_case_language = function_exists('zpl_language') && zpl_language() === 'en' ? 'en' : 'pl';
get_header();
echo '<main id="content" class="zpCS18Main">';
echo zp_cs18_render($zp_case_key, $zp_case_language, true);
echo '</main>';
get_footer();
