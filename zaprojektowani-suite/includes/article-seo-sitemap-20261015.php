<?php
/** Adapt Rank Math's sampled post objects to the reviewed, durable article URL pins. */
if (!defined('ABSPATH')) { exit; }

final class ZP_Article_SEO_Sitemap_20261015 {
    public const CACHE_VERSION = 1;
    public const CACHE_MARKER = 'zps_article_seo_sitemap_20261015_cache';

    public static function register(): void {
        // Rank Math uses a partial stdClass with filter=sample. Core deliberately skips get_post()
        // for that object, so the strict WP_Post post_link guard cannot recognize it.
        add_filter('rank_math/sitemap/xml_post_url', [self::class, 'post_url'], PHP_INT_MAX, 2);
        add_filter('rank_math/sitemap/post_object', [self::class, 'post_object'], PHP_INT_MAX);
        // English maps render at wp_loaded:0; both plugins have finished init before this callback.
        add_action('wp_loaded', [self::class, 'refresh_cache'], -10);
        add_action('update_option_'.ZP_Article_SEO_Maintenance_20261015::PINS, static function (): void {
            delete_option(self::CACHE_MARKER);
        });
    }

    /** Only completed, reviewed records whose durable backup still owns the exact pin. */
    private static function record($object): ?array {
        if (!is_object($object) || !isset($object->ID) || !is_numeric($object->ID)) { return null; }
        $id = (int)$object->ID;
        if (!isset(ZP_Article_SEO_Maintenance_20261015::manifest()[$id])) { return null; }
        $pin = ZP_Article_SEO_Maintenance_20261015::pins()[$id] ?? null;
        $row = get_option(ZP_Article_SEO_Maintenance_20261015::ROW.$id, []);
        $post = get_post($id);
        if (!$post instanceof WP_Post || $post->post_type !== 'post' || !is_array($pin)
            || !is_array($row) || ($row['state'] ?? '') !== 'applied' || ($row['pin'] ?? null) !== $pin) { return null; }
        return ['post'=>$post, 'pin'=>$pin];
    }

    /** Defensive exclusion if a stale/custom provider supplies a now non-public reviewed post. */
    public static function post_object($object) {
        $record = self::record($object);
        if (!$record) { return $object; }
        $post = $record['post'];
        return $post->post_status === 'publish' && $post->post_password === '' && is_post_publicly_viewable($post)
            ? $object : false;
    }

    /** Runs before Rank Math's manual-canonical comparison and before the EN exact-path lookup. */
    public static function post_url($url, $object) {
        $record = self::record($object);
        if (!$record || !is_string($url)) { return $url; }
        $post = $record['post']; $pin = $record['pin'];
        if ($post->post_status !== 'publish' || $post->post_password !== '' || !is_post_publicly_viewable($post)
            || !is_string($pin['slug'] ?? null) || $pin['slug'] !== $post->post_name
            || !ZP_Article_SEO_Maintenance_20261015::safe_path($pin['path'] ?? null)
            || basename($pin['path']) !== $post->post_name) { return $url; }
        $parts = wp_parse_url($url); $home = wp_parse_url(home_url('/'));
        if (!is_array($parts) || !is_array($home) || ($parts['scheme'] ?? '') !== ($home['scheme'] ?? '')
            || ($parts['host'] ?? '') !== ($home['host'] ?? '') || ($parts['port'] ?? null) !== ($home['port'] ?? null)
            || isset($parts['user']) || isset($parts['pass']) || isset($parts['query']) || isset($parts['fragment'])
            || !ZP_Article_SEO_Maintenance_20261015::safe_path($parts['path'] ?? null)
            || basename($parts['path']) !== $post->post_name) { return $url; }
        return home_url($pin['path']);
    }

    /** One scoped upgrade invalidation; mark completion only after both native cache operations finish. */
    public static function refresh_cache(): void {
        if ((string)get_option(self::CACHE_MARKER, '') === (string)self::CACHE_VERSION
            || !is_callable(['RankMath\\Sitemap\\Cache', 'invalidate_storage'])
            || !is_callable(['ZPL\\Sitemap', 'forget'])) { return; }
        $invalidated = false;
        $confirmed = static function ($type) use (&$invalidated): void { if ($type === 'post') { $invalidated = true; } };
        add_action('rank_math/sitemap/invalidated_storage', $confirmed);
        try {
            // Rank Math returns void even when invalidation is blocked. Its success action is required.
            \RankMath\Sitemap\Cache::invalidate_storage('post');
            if (!$invalidated) { return; }
            \ZPL\Sitemap::forget();
            foreach (array_keys(\ZPL\Sitemap::MAPS) as $type) {
                if (get_transient(\ZPL\Sitemap::CACHE.'_'.$type) !== false) { return; }
            }
            update_option(self::CACHE_MARKER, (string)self::CACHE_VERSION, false);
        } catch (Throwable $error) {
            // Leave the marker absent so a later request can retry; never purge HTML or alter articles.
            return;
        } finally {
            remove_action('rank_math/sitemap/invalidated_storage', $confirmed);
        }
    }
}

ZP_Article_SEO_Sitemap_20261015::register();
