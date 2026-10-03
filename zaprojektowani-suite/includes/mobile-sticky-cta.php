<?php
if (!defined('ABSPATH')) {
  exit;
}

/**
 * ZAPROJEKTOWANI — RESPONSIVE STICKY CTA DOCK v2.6 DESKTOP + MOBILE
 * Bottom mobile navigation inspired by ProScarves dock.
 * Items: Start / Zamów / WhatsApp / Telefon.
 *
 * v2.6: render as early as wp_body_open so the fixed dock no longer appears only
 * at the very end of document parsing. wp_footer remains a safe fallback for themes
 * that do not call wp_body_open(). Static guard prevents duplicate markup.
 */
if (!function_exists('zp_suite_render_mobile_sticky_cta')) {
  function zp_suite_render_mobile_sticky_cta() {
    static $rendered = false;
    if ($rendered || is_admin()) {
      return;
    }
    $rendered = true;
    ?>
    <nav class="zpMobileStickyCta" id="zpMobileStickyCta" aria-label="Szybka nawigacja Zaprojektowani">
      <div class="zpMobileStickyCta__inner">
        <a class="zpMobileStickyCta__item" href="/" aria-label="Przejdź na stronę główną">
          <span class="zpMobileStickyCta__icon" aria-hidden="true"><i data-lucide="home"></i></span>
          <span class="zpMobileStickyCta__label">Start</span>
          <span class="zpMobileStickyCta__sub">Strona główna</span>
        </a>

        <a class="zpMobileStickyCta__item zpMobileStickyCta__item--primary" href="/studio-wyceny/" aria-label="Przejdź do Studio Wyceny">
          <span class="zpMobileStickyCta__icon" aria-hidden="true"><i data-lucide="file-check-2"></i></span>
          <span class="zpMobileStickyCta__label">Zamów</span>
          <span class="zpMobileStickyCta__sub">Studio wyceny</span>
          <span class="zpMobileStickyCta__shine" aria-hidden="true"></span>
        </a>

        <a class="zpMobileStickyCta__item" href="https://wa.me/48501054253" target="_blank" rel="noopener" aria-label="Napisz do Zaprojektowani na WhatsApp">
          <span class="zpMobileStickyCta__icon" aria-hidden="true"><i data-lucide="message-circle"></i></span>
          <span class="zpMobileStickyCta__label">WhatsApp</span>
          <span class="zpMobileStickyCta__sub">Napisz teraz</span>
        </a>

        <a class="zpMobileStickyCta__item" href="tel:+48501054253" aria-label="Zadzwoń do Zaprojektowani">
          <span class="zpMobileStickyCta__icon" aria-hidden="true"><i data-lucide="phone-call"></i></span>
          <span class="zpMobileStickyCta__label">Telefon</span>
          <span class="zpMobileStickyCta__sub">501 054 253</span>
        </a>
      </div>
    </nav>
    <?php
  }
}

add_action('wp_body_open', 'zp_suite_render_mobile_sticky_cta', 1);
add_action('wp_footer', 'zp_suite_render_mobile_sticky_cta', 1);
