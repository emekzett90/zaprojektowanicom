<?php
if (!defined('ABSPATH')) exit;

/**
 * Zaprojektowani Suite 2.2.753 — Seobility cleanup.
 * Front page only for content/markup transformations; Apple icon is global.
 */
function zp_suite_753_is_front(): bool {
  return !is_admin() && (is_front_page() || is_home());
}

// Apple Touch Icon — use native site icon if available, otherwise bundled brand mark.
add_action('wp_head', function(){
  $icon = get_site_icon_url(180);
  if (!$icon) {
    $icon = ZP_SUITE_URL . 'assets/apple-touch-icon.png';
  }
  echo "\n<link rel=\"apple-touch-icon\" sizes=\"180x180\" href=\"" . esc_url($icon) . "\">\n";
}, 2);

// Lightweight styles used only if semantic strong tags are reduced in source HTML.
add_action('wp_head', function(){
  if (!zp_suite_753_is_front()) return;
  echo '<style id="zp-seobility-753-css">.zpSeoRuntimeStrong{font-weight:700}.zpHomeSeoIntro__copy p+p{margin-top:14px}</style>';
}, 998);

// Restore source-optimized spans to real STRONG/B tags in the browser, preserving visual CSS selectors.
add_action('wp_footer', function(){
  if (!zp_suite_753_is_front()) return;
  ?>
<script id="zp-seobility-753-runtime">
(function(){
  function restore(){
    document.querySelectorAll('[data-zp-semantic-bold]').forEach(function(el){
      var tag=(el.getAttribute('data-zp-semantic-bold')||'strong').toLowerCase()==='b'?'b':'strong';
      var n=document.createElement(tag);
      Array.prototype.slice.call(el.attributes).forEach(function(a){
        if(a.name!=='data-zp-semantic-bold' && a.name!=='class') n.setAttribute(a.name,a.value);
      });
      var cls=(el.getAttribute('class')||'').replace(/\bzpSeoRuntimeStrong\b/g,'').trim();
      if(cls)n.setAttribute('class',cls);
      while(el.firstChild)n.appendChild(el.firstChild);
      el.replaceWith(n);
    });
  }
  if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',restore,{once:true});else restore();
})();
</script>
  <?php
}, 999);

add_action('template_redirect', function(){
  if (!zp_suite_753_is_front() || is_feed() || wp_doing_ajax()) return;
  ob_start('zp_suite_753_cleanup_html');
}, 0);

function zp_suite_753_cleanup_html(string $html): string {
  if ($html === '' || stripos($html, '<html') === false) return $html;

  // Remove truly empty semantic bold tags.
  $html = preg_replace('~<(strong|b)\b[^>]*>\s*(?:&nbsp;|&#160;)?\s*</\\1>~is', '', $html);

  // Seobility flags repeats, very long bold text and >22 semantic bold tags.
  // Keep the first 22 unique, short tags in raw HTML; the rest are restored client-side for identical appearance.
  // 2.3.0: Google reads the same HTML as visitors, so bold tags are no longer swapped for spans.
  $plan_on = function_exists('zp_seo_plan_active') && zp_seo_plan_active();
  $kept = 0;
  $seen = [];
  if (!$plan_on) $html = preg_replace_callback('~<(strong|b)\b([^>]*)>(.*?)</\\1>~is', function($m) use (&$kept, &$seen){
    $plain = trim(wp_strip_all_tags(html_entity_decode($m[3], ENT_QUOTES | ENT_HTML5, 'UTF-8')));
    if ($plain === '') return '';
    $normalized = preg_replace('/\s+/u', ' ', $plain);
    $key = function_exists('mb_strtolower') ? mb_strtolower($normalized, 'UTF-8') : strtolower($normalized);
    $len = function_exists('mb_strlen') ? mb_strlen($plain, 'UTF-8') : strlen($plain);
    $repeat = isset($seen[$key]);
    $seen[$key] = true;
    if ($repeat || $len > 70 || $kept >= 22) {
      $attrs = trim($m[2]);
      $class = 'zpSeoRuntimeStrong';
      if (preg_match('/\bclass\s*=\s*(["\'])(.*?)\\1/is', $attrs, $cm)) {
        $class .= ' ' . trim($cm[2]);
        $attrs = preg_replace('/\s*\bclass\s*=\s*(["\'])(.*?)\\1/is', '', $attrs, 1);
      }
      return '<span class="' . esc_attr(trim($class)) . '" data-zp-semantic-bold="' . esc_attr(strtolower($m[1])) . '"' . ($attrs ? ' ' . trim($attrs) : '') . '>' . $m[3] . '</span>';
    }
    $kept++;
    return $m[0];
  }, $html);

  // Remove ordinary HTML comments while preserving IE conditionals.
  $html = preg_replace('~<!--(?!\[if).*?-->~s', '', $html);

  // Minify inline CSS blocks — safe and useful on a very large document.
  $html = preg_replace_callback('~<style\b([^>]*)>(.*?)</style>~is', function($m){
    $css = preg_replace('~/\*.*?\*/~s', '', $m[2]);
    $css = preg_replace('/\s+/', ' ', $css);
    $css = preg_replace('/\s*([{}:;,])\s*/', '$1', $css);
    return '<style' . $m[1] . '>' . trim($css) . '</style>';
  }, $html);

  // Protect script/pre/textarea before conservative whitespace compaction.
  $protected = [];
  $html = preg_replace_callback('~<(script|pre|textarea)\b[^>]*>.*?</\\1>~is', function($m) use (&$protected){
    $token = '___ZP753_' . count($protected) . '___';
    $protected[$token] = $m[0];
    return $token;
  }, $html);
  $html = preg_replace('/[\t\r\n ]{2,}/', ' ', $html);
  foreach($protected as $token=>$value) $html = str_replace($token, $value, $html);
  return trim($html);
}
