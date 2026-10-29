<?php
/**
 * ZP Suite 3.2.93 — O nas (wygląd podstron, agent onas).
 *
 * 1. Kicker. The template prints only „Studio kreatywne z Katowic” now. On live something outside the
 *    plugin used to put „O nas — Zaprojektowani” in front of the H1 text (the EN dictionary stores that
 *    H1 as "<1>O nas — Zaprojektowani</1>Projektujemy…"). The H1 is therefore guarded: a leading inline
 *    element or bare text „O nas — Zaprojektowani” / "About us — Zaprojektowani" is removed from
 *    #zpAboutXTitle in the shortcode output, in a page buffer that runs before the languages module
 *    translates the page, and once more on the final document (zp_en_public_price_html, which the
 *    languages module applies after translation). The SEO text of the H1 is untouched.
 * 2. Visual polish: assets/css/wyglad-3293/onas.css, enqueued on /o-nas/ and /en/about-us/ only (the EN
 *    router rewrites REQUEST_URI to the Polish page, so is_page('o-nas') is true for both languages).
 * 3. Service cards: the four cards that includes/seo-plan/about.php turns into links get a visible
 *    „Zobacz usługę” label; the English page gets the same links (the languages module localises the
 *    hrefs) and the translated label.
 */
if (!defined('ABSPATH')) { exit; }

function zp_w93_onas_is_about(): bool {
  return function_exists('is_page') && is_page('o-nas');
}

/** Remove a leading „O nas — Zaprojektowani” / "About us — Zaprojektowani" from the about page H1. */
function zp_w93_onas_clean_h1(string $html): string {
  if ($html === '' || strpos($html, 'zpAboutXTitle') === false) { return $html; }
  $label = '(?:O\s+nas|About\s+us)\s*(?:—|–|-|&mdash;|&ndash;|&#8212;|&#8211;)\s*Zaprojektowani(?:\.com)?';
  $out = preg_replace_callback('~(<h1\b[^>]*\bid=["\']zpAboutXTitle["\'][^>]*>)(.*?)(</h1>)~is', static function ($m) use ($label) {
    $inner = $m[2];
    // a) wrapped in an element (span, small, em, …), optionally followed by a <br>
    $inner = preg_replace('~^\s*<(span|small|em|i|b|strong|div|p|a)\b[^>]*>\s*' . $label . '\s*</\1>\s*(?:<br\s*/?>\s*)?~iu', '', $inner, 1) ?? $inner;
    // b) bare text, optionally followed by a separator or a <br>
    $inner = preg_replace('~^\s*' . $label . '\s*(?:[:|·]\s*)?(?:<br\s*/?>\s*)?~iu', '', $inner, 1) ?? $inner;
    return $m[1] . $inner . $m[3];
  }, $html, 1);
  return is_string($out) ? $out : $html;
}

/** Shortcode output: the H1 guard first, the service-card labels after seo-plan/about.php (priority 20). */
add_filter('do_shortcode_tag', static function ($output, $tag) {
  if (is_string($output) && in_array($tag, ['zp_o_nas', 'zp_page_o_nas', 'zp_about_page'], true)) {
    $output = zp_w93_onas_clean_h1($output);
  }
  return $output;
}, 5, 2);

add_filter('do_shortcode_tag', static function ($output, $tag) {
  if (!is_string($output) || !in_array($tag, ['zp_o_nas', 'zp_page_o_nas', 'zp_about_page'], true) || is_admin()) { return $output; }
  // The template's own CSS mentions zpAboutX__cap--link, so test for a linked article, not the bare class name.
  $linked = 'class="zpAboutX__cap zpAboutX__cap--link"';
  if (strpos($output, $linked) === false
    && function_exists('zp_seo_plan_is_en') && zp_seo_plan_is_en()
    && function_exists('zp_seo_plan_active') && zp_seo_plan_active()
    && function_exists('zp_seo_about_apply')) {
    $output = zp_seo_about_apply($output);
  }
  if (strpos($output, $linked) === false) { return $output; }
  $more = '<span class="zpAboutX__capMore">Zobacz usługę<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span>';
  $out = preg_replace('~(<article class="zpAboutX__cap zpAboutX__cap--link"[^>]*>.*?)(<span class="zpAboutX__capArrow")~s', '$1' . $more . '$2', $output);
  return is_string($out) ? $out : $output;
}, 21, 2);

/** Page buffer (inside the languages module's buffer, so it runs before translation). */
add_action('template_redirect', static function () {
  if (is_admin() || !zp_w93_onas_is_about()) { return; }
  $buf = '';
  ob_start(static function ($chunk, $phase = 0) use (&$buf) {
    if (!is_string($chunk)) { return $chunk; }
    if ($phase & PHP_OUTPUT_HANDLER_CLEAN) { $buf = ''; return ''; }
    $buf .= $chunk;
    if (!($phase & PHP_OUTPUT_HANDLER_FINAL)) { return ''; }
    $html = $buf;
    $buf = '';
    return zp_w93_onas_clean_h1($html);
  });
}, 50);

/** Final document (after translation): the English label is caught here. */
add_filter('zp_en_public_price_html', static function ($html) {
  if (!is_string($html) || !zp_w93_onas_is_about()) { return $html; }
  return zp_w93_onas_clean_h1($html);
}, 1);

add_action('wp_enqueue_scripts', static function () {
  if (!zp_w93_onas_is_about()) { return; }
  wp_enqueue_style('zp-w93-onas', ZP_SUITE_URL . 'assets/css/wyglad-3293/onas.css', [], ZP_SUITE_VERSION);
}, 30);
