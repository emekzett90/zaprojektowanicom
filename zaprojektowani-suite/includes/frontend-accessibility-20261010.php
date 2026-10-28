<?php
/** Small text contrast and label fixes evidenced by the saved Google Lighthouse reports. */
if (!defined('ABSPATH')) { exit; }

final class ZP_Frontend_Accessibility_20261010 {
    public static function eligible(): bool {
        if (is_admin() || wp_doing_ajax() || wp_doing_cron() || is_feed() || is_preview()
            || (defined('REST_REQUEST') && REST_REQUEST) || (defined('WP_CLI') && WP_CLI)
            || !in_array(strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET')), ['GET', 'HEAD'], true)) { return false; }
        foreach (['elementor-preview','elementor_library','customize_changeset_uuid','customize_theme','preview','preview_id','preview_nonce','rest_route','zpl_frame','zpl_scan'] as $key) {
            if (isset($_GET[$key])) { return false; }
        }
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        if (!is_string($uri) || $uri === '' || $uri[0] !== '/' || str_starts_with($uri, '//')) { return false; }
        $path = wp_parse_url($uri, PHP_URL_PATH);
        return is_string($path) && !preg_match('~^/(?:wp-admin|wp-json|wp-login\.php|wp-cron\.php|wp-content|wp-includes|xmlrpc\.php|zp-suite)(?:/|$)~i', $path);
    }

    public static function head(): void {
        if (!self::eligible()) { return; }
        // Only foreground colors change. Existing backgrounds, sizes, layout and animations remain.
        echo '<style id="zp-suite-frontend-a11y-20261010">'
            . 'html body #zp-cookiebot-banner .zp-cookiebot__eyebrow{color:#67758a!important}'
            . 'html body #zp-cookiebot-banner #zp-cookiebot-toggle-settings{color:#3b6db2!important}'
            . 'html body #zp-cookiebot-banner .zp-cookiebot__links #zp-privacy-link,html body #zp-cookiebot-banner .zp-cookiebot__links #zp-cookies-link{color:#6b7586!important}'
            . 'html body #zpContactFormLight#zpContactFormLight .zpContactFormLight__field .zpContactFormLight__opt{color:#6d7581!important}'
            . 'html body #zpContactFormLight#zpContactFormLight #zpContactFormMain .zpContactFormLight__submitRow > p[data-submit-note]{color:#6d7581!important}'
            . 'html body #zpMegaFooter .zpMegaFooter__contactCards > a.zpMegaFooter__contactCard > span > em{color:#747474!important}'
            . '</style>';
    }

}

// 3.0.0: the sticky bar's aria-labels now match their visible labels in includes/mobile-sticky-cta.php, so the
// whole-page output buffer that rewrote them on every page view is gone.
add_action('wp_head', [ZP_Frontend_Accessibility_20261010::class, 'head'], PHP_INT_MAX);
