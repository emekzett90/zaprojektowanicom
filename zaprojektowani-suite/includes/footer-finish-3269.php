<?php
/** 3.2.69: footer owns the mobile dock clearance so there is no white tail. */
defined('ABSPATH') || exit;
add_action('wp_head', static function (): void {
  if (is_admin()) return;
  echo '<style id="zp-footer-finish-3269">
html body:has(#zpMegaFooter){padding-bottom:0!important;margin-bottom:0!important;}
html body .elementor-location-footer:has(#zpMegaFooter){padding-bottom:0!important;margin-bottom:0!important;}
html body #zpMegaFooter{margin-bottom:0!important;}
@media(max-width:760px){
  html body #zpMegaFooter#zpMegaFooter .zpMegaFooter__bottomBand{padding-bottom:calc(108px + env(safe-area-inset-bottom,0px))!important;}
}
</style>';
}, PHP_INT_MAX);
