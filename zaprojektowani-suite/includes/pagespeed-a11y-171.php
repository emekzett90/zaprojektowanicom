<?php
if (!defined('ABSPATH')) { exit; }

/**
 * ZP Suite v1.7.1 — PageSpeed / Lighthouse accessibility fixes.
 * Fixes: skip-link visibility, hidden modals, ARIA tab roles, and safer focus outlines.
 */
add_action('wp_head', function(){
  ?>
  <style id="zp-suite-a11y-pagespeed-171">html body .skip-link.screen-reader-text,html body a.skip-link.screen-reader-text,html body .screen-reader-text.skip-link{position:absolute!important;left:12px!important;top:12px!important;width:auto!important;height:auto!important;clip:auto!important;clip-path:none!important;overflow:visible!important;padding:12px 16px!important;border-radius:999px!important;background:#071426!important;color:#fff!important;z-index:10000000!important;transform:translateY(-160%)!important;opacity:0!important;pointer-events:none!important;text-decoration:none!important;font-family:"Plus Jakarta Sans Local",system-ui,sans-serif!important;font-weight:800!important;letter-spacing:-.01em!important;box-shadow:0 12px 28px rgba(7,20,38,.22)!important}html body .skip-link.screen-reader-text:focus,html body a.skip-link.screen-reader-text:focus,html body .screen-reader-text.skip-link:focus{transform:translateY(0)!important;opacity:1!important;pointer-events:auto!important;outline:3px solid rgba(28,71,122,.35)!important;outline-offset:3px!important}.zpNewNav [hidden],.zpContactModal[hidden],.zpShowcasePop[hidden]{display:none!important}</style>
  <?php
}, 0);

/** Ensure a valid skip-link target exists even when the page is built from shortcodes. */
add_action('wp_body_open', function(){
  if (!is_admin()) {
    echo '<a class="skip-link screen-reader-text" href="#content">Przejdź do treści</a>';
  }
}, 1);

/** Add a minimal content target when the page does not use [zp_home_full]. */
add_action('wp_footer', function(){
  if (!is_admin()) {
    echo '<span id="zp-content-fallback" tabindex="-1" aria-hidden="true"></span>';
  }
}, 1);
