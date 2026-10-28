<?php
/**
 * ZP Suite v2.2.582 — Mega menu "Usługi" premium polish.
 * Desktop: obie wersje (ciemna .is-dark-menu i jasna .is-light-menu) portalu mega menu:
 *  - miękkie radialne poświaty w tle panelu + gradientowa linia górna,
 *  - stagger reveal kolumn przy otwarciu (z poszanowaniem prefers-reduced-motion),
 *  - dopracowane hovery linków (przesunięcie, kafelek ikony, akcent tytułu),
 *  - karta promo z liftem, poświatą i ceną w akcencie ice,
 *  - dolna belka z gradientowym hairline.
 * Mobile: pod-menu "Usługi" w drawerze jako miękkie karty (spójnie na telefonie i tablecie,
 * wygrywa z front-fixes-243 przez późniejszy output + tę samą specyficzność).
 */
if (!defined('ABSPATH')) { exit; }

function zp_suite_290_mega_menu_polish_css(){
  if (is_admin()) { return; }
  ?>
<link rel="stylesheet" id="zp-suite-front-fixes-290-mega-polish" href="<?php echo esc_url(ZP_SUITE_URL . 'assets/css/front-fixes-290.css'); ?>?ver=<?php echo esc_attr(zp_suite_asset_version('assets/css/front-fixes-290.css')); ?>">
  <?php
}
add_action('wp_footer','zp_suite_290_mega_menu_polish_css',PHP_INT_MAX);
