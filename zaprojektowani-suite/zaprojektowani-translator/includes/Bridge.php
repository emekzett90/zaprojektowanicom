<?php
namespace ZPTE;

if (!defined('ABSPATH')) { exit; }

/**
 * Link with the language module (zaprojektowani-languages, namespace ZPL). Serving itself is in Serve.php;
 * this class keeps the names other parts of ZP Suite call (command center, Języki PL/EN, workspace screens).
 */
final class Bridge {
    public static function ready(): bool {
        return defined('ZPL_VERSION') && class_exists('ZPL\\Dict') && class_exists('ZPL\\Page') && class_exists('ZPL\\Router') && class_exists('ZPL\\Html');
    }

    /** The language module asks for this module's translations above its shipped dictionary (ZPL API 2+). */
    public static function hooks_ready(): bool {
        return self::ready() && defined('ZPL_API') && (int) ZPL_API >= 2;
    }

    /** Kept for older callers: serving starts when the module file loads (Serve::hooks). */
    public static function boot(): void {}

    public static function path_of_post(int $post_id): string { return Sync::path_of_post($post_id); }

    public static function ineligible(string $path): string { return Sync::ineligible($path); }

    /** English URL of a Polish path ('' when the page has no English version). */
    public static function en_url(string $pl): string {
        if (!self::ready()) { return ''; }
        $pl = \ZPL\Router::norm_path($pl);
        if (!\ZPL\Router::has_route($pl) || !\ZPL\Dict::has_page($pl)) { return ''; }
        return home_url(\ZPL\Router::en_path($pl));
    }
}
