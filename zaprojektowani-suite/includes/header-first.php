<?php
/**
 * 2.9.11: on phones the header paints first, finished, with nothing moving.
 *
 * Before, the mobile bar was held invisible until fonts loaded (front-fixes-313/316/318) and then slid in, the
 * phone and menu icons were <i> tags drawn later by Lucide, the logo was lazy on service pages, and the late
 * header stylesheet restarted the entrance animation, so the header appeared after the hero photo and title.
 * Now the header starts in its final state: the classes are set before the body is parsed, the entrance
 * animations are off on phones and tablets (up to 1100 px), and the two icons are printed as the same SVG
 * Lucide makes. The home hero still waits for the header (front-fixes-318). Desktop is unchanged.
 *
 * The bar's labels (weight 720 and 800) are drawn with Jakarta's 800 file, which no page preloads, so they first
 * showed in the browser's default font and widened when the file arrived. The page now carries those glyphs
 * (K o n s u l t a c j C i P L E N, 1.6 KB cut from the same 800 file) and registers them from binary data
 * in <head>, where FontFace has them ready at once, so the labels are final in the first frame (a data: URL
 * in @font-face still loads later). Any other letter, or a browser without FontFace, uses the full file.
 */
if (!defined('ABSPATH')) { exit; }

/** The SVG Lucide 0.574 makes for these two icons with the header's stroke width (1.9). */
function zp_suite_header_first_icon($name) {
  $paths = [
    'phone' => '<path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"></path>',
    'menu' => '<path d="M4 5h16"></path><path d="M4 12h16"></path><path d="M4 19h16"></path>',
  ];
  if (!isset($paths[$name])) {
    return '<i data-lucide="' . esc_attr($name) . '"></i>';
  }
  return '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" data-lucide="' . $name . '" aria-hidden="true" class="lucide lucide-' . $name . '">' . $paths[$name] . '</svg>';
}

/** Glyphs of the phone header labels, cut from plus-jakarta-sans-v12-latin_latin-ext-800.woff2 (fontTools subset). */
function zp_suite_header_first_font_b64() {
  return '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';
}

add_action('wp_head', function () {
  if (is_admin()) { return; }
  ?>
<script id="zp-suite-header-first">(function(w,d,h){try{if(w.matchMedia&&w.matchMedia('(max-width:1100px)').matches){h.classList.add('zp-mobile-header-ready');h.classList.remove('zp-mobile-ui-pending');}}catch(e){}try{if(w.FontFace&&d.fonts&&d.fonts.add){var s=atob('<?php echo zp_suite_header_first_font_b64(); ?>'),b=new Uint8Array(s.length);for(var i=0;i<s.length;i++)b[i]=s.charCodeAt(i);d.fonts.add(new FontFace('ZP Header Jakarta',b.buffer,{weight:'800',style:'normal'}));}}catch(e){}})(window,document,document.documentElement);</script>
<style id="zp-suite-header-first-css">@media (max-width:1100px){html body #zpNewNav:not(#zpx) .zpNewNav__mobileBar .zpNewNav__mobileCall span,html body #zpNewNav:not(#zpx) .zpNewNav__mobileBar .zpl-switch__code{font-family:"ZP Header Jakarta","Plus Jakarta Sans Local","Plus Jakarta Sans",system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif!important}html body #zpNewNav:not(#zpx) .zpNewNav__mobileBar,html body #zpNewNav:not(#zpx) .zpNewNav__mobileCall,html body #zpNewNav:not(#zpx) .zpNewNav__mobileBrand,html body #zpNewNav:not(#zpx) .zpNewNav__mobileLogo,html body #zpNewNav:not(#zpx) .zpNewNav__mobileActions>*,html body #zpNewNav:not(#zpx) .zpl-switch--chip,html body #zpNewNav:not(#zpx) .zpHeaderStaticDivider{animation:none!important}}</style>
<?php
}, PHP_INT_MAX);
