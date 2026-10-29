<?php
/**
 * ZP Suite 3.2.93 — nawigacja (Mat, 28.10.2026: „usuń breadcrumbs i niech podstrony zaczynają się od kickera”).
 *
 * The visible trails were removed from the markup wherever this plugin renders the page from a template
 * (templates/faq-page.php, templates/wiedza-category.php, templates/thank-you/*.html), and the Realizacje
 * mega-menu heading override left includes/front-fixes-223.php. Two trails cannot be removed from markup and are
 * hidden here instead (BreadcrumbList JSON-LD in includes/seo.php and includes/seo-plan/schema.php stays untouched):
 * - the legal documents (regulamin, polityka prywatności, RODO, cookies): includes/legal-pages.php stores their HTML
 *   in post_content and in the Elementor data and rewrites the four pages whenever its stylesheet mtime changes, so
 *   neither that file nor legal-pages-20261016.css is edited — .zpLegal__breadcrumbs is hidden on those pages
 *   (the /en/ routes resolve to the same page objects);
 * - „Strona główna / Wiedza” at the bottom of the Wiedza SEO hub (templates/wiedza-blog.php, rebuilt by the wiedza
 *   module of this release) — .zpKBSeoHub__crumbs, on every page or post whose content runs [zp_wiedza_blog].
 */
if (!defined('ABSPATH')) { exit; }

function zp_w93_nawigacja_css(): string {
  $css = '';
  if (is_page(['regulamin', 'polityka-prywatnosci', 'rodo', 'cookies'])) {
    $css .= 'html body .zpLegal .zpLegal__breadcrumbs{display:none!important}';
  }
  $post = is_singular() ? get_post() : null;
  if ($post && is_string($post->post_content) && $post->post_content !== '' && has_shortcode($post->post_content, 'zp_wiedza_blog')) {
    $css .= 'html body .zpKBSeoHub__crumbs{display:none!important}';
  }
  return $css;
}

add_action('wp_head', function () {
  if (is_admin()) { return; }
  $css = zp_w93_nawigacja_css();
  if ($css !== '') { echo '<style id="zp-w93-nawigacja">' . $css . "</style>\n"; }
}, 30);
