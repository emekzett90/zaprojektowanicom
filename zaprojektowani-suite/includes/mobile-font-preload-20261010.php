<?php
/** Earlier discovery of existing mobile italic fonts; no CSS or animation changes. */
if (!defined('ABSPATH')) { exit; }

final class ZP_Mobile_Font_Preload_20261010 {
    public const FILE = 'plus-jakarta-sans-v12-latin_latin-ext-italic.woff2';
    public const FILE_700_ITALIC = 'plus-jakarta-sans-v12-latin_latin-ext-700italic.woff2';
    public const MEDIA = '(max-width: 767px)';

    public static function eligible(): bool {
        if (!function_exists('wp_preload_resources') || is_admin() || !is_front_page()
            || is_user_logged_in() || is_preview() || is_feed() || wp_doing_ajax() || wp_doing_cron()
            || (defined('REST_REQUEST') && REST_REQUEST) || (defined('WP_CLI') && WP_CLI)
            || !in_array(strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET')), ['GET', 'HEAD'], true)) {
            return false;
        }
        if (class_exists('\\ZPL\\Router') && \ZPL\Router::$lang !== 'pl') { return false; }
        foreach (['elementor-preview', 'elementor_library', 'customize_changeset_uuid', 'customize_theme', 'preview', 'preview_id', 'preview_nonce', 'rest_route', 'zpl_frame', 'zpl_scan'] as $key) {
            if (isset($_GET[$key])) { return false; }
        }
        // English routing can reuse WordPress's front-page query: require the exact PL home path.
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        if (!is_string($uri) || $uri === '' || $uri[0] !== '/' || str_starts_with($uri, '//')) { return false; }
        $path = wp_parse_url($uri, PHP_URL_PATH);
        $home_path = wp_parse_url(home_url('/'), PHP_URL_PATH);
        return is_string($path) && $path === (is_string($home_path) && $home_path !== '' ? $home_path : '/');
    }

    private static function same_resource(string $href, string $target): bool {
        if ($href === $target) { return true; }
        $expected = wp_parse_url($target);
        $given = wp_parse_url($href);
        if (!is_array($expected) || !is_array($given) || isset($given['query']) || isset($given['fragment'])
            || isset($given['user']) || isset($given['pass']) || isset($given['port'])) { return false; }
        if (($given['path'] ?? '') !== ($expected['path'] ?? '')) { return false; }
        if (!isset($given['host'])) { return str_starts_with($href, '/') && !str_starts_with($href, '//'); }
        return strtolower($given['host']) === strtolower($expected['host'] ?? '')
            && (!isset($given['scheme']) || $given['scheme'] === ($expected['scheme'] ?? ''));
    }

    public static function resources($resources) {
        if (!is_array($resources) || !self::eligible()) { return $resources; }
        $targets = [content_url('/web-font/jakarta/' . self::FILE), content_url('/web-font/jakarta/' . self::FILE_700_ITALIC)];
        foreach ($targets as $href) {
            $parts = wp_parse_url($href);
            if (!is_array($parts) || ($parts['scheme'] ?? '') !== 'https' || empty($parts['host'])
                || isset($parts['query']) || isset($parts['fragment']) || isset($parts['user']) || isset($parts['pass'])) { return $resources; }
        }
        foreach ($targets as $href) {
            $present = false;
            foreach ($resources as $resource) {
                if (is_array($resource) && is_string($resource['href'] ?? null) && self::same_resource($resource['href'], $href)) {
                    $present = true;
                    break;
                }
            }
            if (!$present) {
                $resources[] = ['href' => $href, 'as' => 'font', 'type' => 'font/woff2',
                    'crossorigin' => 'anonymous', 'media' => self::MEDIA];
            }
        }
        return $resources;
    }
}

// Core prints this in the HEAD, supports media, and deduplicates the final resource list.
add_filter('wp_preload_resources', [ZP_Mobile_Font_Preload_20261010::class, 'resources'], PHP_INT_MAX);
