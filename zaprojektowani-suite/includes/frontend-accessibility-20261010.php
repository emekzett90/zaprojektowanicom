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

    public static function html(string $html): string {
        if (!self::eligible() || strlen($html) > 4194304 || strpos($html, 'zpMobileStickyCta') === false
            || stripos($html, '<html') === false || stripos($html, '</html>') === false
            || !class_exists('WP_HTML_Tag_Processor') || !method_exists('WP_HTML_Tag_Processor', 'next_token')) { return $html; }
        foreach (headers_list() as $header) {
            if (stripos($header, 'content-type:') === 0 && stripos($header, 'text/html') === false) { return $html; }
            if (stripos($header, 'content-encoding:') === 0) { return $html; }
        }
        $tags = new WP_HTML_Tag_Processor($html);
        $nav_depth = 0; $pending = null;
        while ($tags->next_token()) {
            $tag = $tags->get_tag();
            $closer = $tags->is_tag_closer();
            if ($tag === 'NAV') {
                if (!$closer && ($nav_depth > 0 || ($tags->get_attribute('id') === 'zpMobileStickyCta' && $tags->has_class('zpMobileStickyCta')))) { $nav_depth++; }
                elseif ($closer && $nav_depth > 0 && --$nav_depth === 0) { break; }
                continue;
            }
            if ($nav_depth === 0) { continue; }
            if ($tag === 'A' && !$closer) {
                $pending = null;
                $tags->release_bookmark('zp_a11y_link');
                if ($tags->has_class('zpMobileStickyCta__item') && is_string($tags->get_attribute('aria-label')) && $tags->set_bookmark('zp_a11y_link')) {
                    $pending = ['name' => $tags->get_attribute('aria-label'), 'label' => '', 'depth' => 0];
                }
                continue;
            }
            if ($pending === null) { continue; }
            if ($tag === 'SPAN') {
                if (!$closer && ($pending['depth'] > 0 || $tags->has_class('zpMobileStickyCta__label'))) { $pending['depth']++; }
                elseif ($closer && $pending['depth'] > 0) { $pending['depth']--; }
            } elseif ($tags->get_token_type() === '#text' && $pending['depth'] > 0) {
                $pending['label'] .= $tags->get_modifiable_text();
            }
            if ($tag === 'A' && $closer) {
                $label = trim(html_entity_decode($pending['label'], ENT_QUOTES | ENT_HTML5, 'UTF-8'));
                // Existing dictionary already translates these exact visible labels in both directions.
                if (in_array($label, ['Start','Zamów','Telefon','Home','Order','Phone'], true)
                    && stripos($pending['name'], $label) === false && $tags->set_bookmark('zp_a11y_end')) {
                    if (!$tags->seek('zp_a11y_link')) { return $html; }
                    $tags->set_attribute('aria-label', $label);
                    if (!$tags->seek('zp_a11y_end')) { return $html; }
                    $tags->release_bookmark('zp_a11y_end');
                }
                $tags->release_bookmark('zp_a11y_link'); $pending = null;
            }
        }
        return $tags->get_updated_html();
    }

    public static function buffer($chunk, int $phase = 0) {
        static $buffer = '';
        if (!is_string($chunk)) { return $chunk; }
        if ($phase & PHP_OUTPUT_HANDLER_CLEAN) { $buffer = ''; return ''; }
        $buffer .= $chunk;
        if (!($phase & PHP_OUTPUT_HANDLER_FINAL)) { return ''; }
        $html = $buffer; $buffer = '';
        try { return self::html($html); } catch (Throwable $error) { return $html; }
    }

    public static function boot(): void {
        if (self::eligible()) { ob_start([self::class, 'buffer']); }
    }
}

add_action('wp_head', [ZP_Frontend_Accessibility_20261010::class, 'head'], PHP_INT_MAX);
add_action('template_redirect', [ZP_Frontend_Accessibility_20261010::class, 'boot'], -500);
