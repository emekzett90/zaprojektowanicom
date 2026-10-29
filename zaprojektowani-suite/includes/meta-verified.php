<?php
if (!defined('ABSPATH')) { exit; }

/**
 * Zaprojektowani Suite 2.7.8 — Meta Verified badge.
 *
 * One reusable, accessible trust widget for hero sections, desktop mega menu
 * and the mobile drawer. Desktop opens on hover/focus, touch opens on click.
 */
function zp_suite_meta_verified_markup(string $theme = 'dark', string $context = 'hero'): string {
  $theme = in_array($theme, ['dark', 'light', 'auto'], true) ? $theme : 'dark';
  $context = sanitize_html_class($context ?: 'hero');

  $facebook = 'https://www.facebook.com/zaprojektowanicom';
  $instagram = 'https://www.instagram.com/zaprojektowanicom';

  ob_start();
  ?>
  <div class="zpMetaVerified zpMetaVerified--theme-<?php echo esc_attr($theme); ?> zpMetaVerified--<?php echo esc_attr($context); ?>" data-zp-meta-verified>
    <button class="zpMetaVerified__trigger" type="button" aria-expanded="false" aria-haspopup="true" aria-label="Meta Verified — pokaż zweryfikowane profile Zaprojektowani">
      <span class="zpMetaVerified__brand" aria-hidden="true"></span>
      <span class="zpMetaVerified__chev" aria-hidden="true">
        <svg viewBox="0 0 24 24"><path d="m7 10 5 5 5-5"/></svg>
      </span>
    </button>
    <div class="zpMetaVerified__popover" role="group" aria-label="Zweryfikowane profile Zaprojektowani">
      <div class="zpMetaVerified__head">
        <span class="zpMetaVerified__check" aria-hidden="true">
          <svg viewBox="0 0 24 24" focusable="false"><path class="zpMetaVerified__seal" d="m12 1.5 2.1 2.2 2.9-.8.7 2.9 2.9.7-.8 2.9 2.2 2.1-2.2 2.1.8 2.9-2.9.7-.7 2.9-2.9-.8-2.1 2.2-2.1-2.2-2.9.8-.7-2.9-2.9-.7.8-2.9L1.5 12l2.2-2.1-.8-2.9 2.9-.7.7-2.9 2.9.8Z"/><path class="zpMetaVerified__sealTick" d="m7.9 12.1 2.7 2.7 5.5-5.6"/></svg>
        </span>
        <span><span class="zpMetaVerified__name">Zweryfikowane profile</span><small>Zaprojektowani.com w Meta</small></span>
      </div>
      <div class="zpMetaVerified__links">
        <a href="<?php echo esc_url($facebook); ?>" target="_blank" rel="noopener" aria-label="Otwórz Facebook Zaprojektowani">
          <span class="zpMetaVerified__social zpMetaVerified__social--fb" aria-hidden="true">f</span>
          <span><span class="zpMetaVerified__name">Facebook</span><small>@zaprojektowanicom</small></span>
          <svg class="zpMetaVerified__arrow" viewBox="0 0 24 24" aria-hidden="true"><path d="M7 17 17 7M8 7h9v9"/></svg>
        </a>
        <a href="<?php echo esc_url($instagram); ?>" target="_blank" rel="noopener" aria-label="Otwórz Instagram Zaprojektowani">
          <span class="zpMetaVerified__social zpMetaVerified__social--ig" aria-hidden="true"><svg viewBox="0 0 24 24"><rect x="4" y="4" width="16" height="16" rx="5"/><circle cx="12" cy="12" r="3.5"/><circle cx="17.4" cy="6.7" r=".8" class="fill"/></svg></span>
          <span><span class="zpMetaVerified__name">Instagram</span><small>@zaprojektowanicom</small></span>
          <svg class="zpMetaVerified__arrow" viewBox="0 0 24 24" aria-hidden="true"><path d="M7 17 17 7M8 7h9v9"/></svg>
        </a>
      </div>
    </div>
  </div>
  <?php
  return (string) ob_get_clean();
}

/** Replace lightweight template tokens in file-based HTML sections. */
function zp_suite_meta_verified_tokens(string $html): string {
  if (strpos($html, '%%ZPMV_') === false) { return $html; }
  return strtr($html, [
    '%%ZPMV_DARK%%'  => zp_suite_meta_verified_markup('dark', 'service'),
    '%%ZPMV_LIGHT%%' => zp_suite_meta_verified_markup('light', 'service'),
  ]);
}

add_action('wp_enqueue_scripts', static function () {
  if (is_admin()) { return; }

  $css_rel = 'assets/css/blocks/meta-verified.css';
  $js_rel  = 'assets/js/blocks/meta-verified.js';
  $css = ZP_SUITE_PATH . $css_rel;
  $js  = ZP_SUITE_PATH . $js_rel;

  if (is_file($css)) {
    wp_enqueue_style('zp-suite-meta-verified', ZP_SUITE_URL . $css_rel, [], (string) filemtime($css));
  }
  if (is_file($js)) {
    wp_enqueue_script('zp-suite-meta-verified', ZP_SUITE_URL . $js_rel, [], (string) filemtime($js), true);
  }
}, 80);
