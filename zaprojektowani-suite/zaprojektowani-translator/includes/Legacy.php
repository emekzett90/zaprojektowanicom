<?php
namespace ZPTE;

if (!defined('ABSPATH')) { exit; }

/**
 * Read-only access to the translations of the earlier translators, so they keep working after the switch:
 *  - Tłumacz EN 1 (tables {wp}zpte_paths, zpte_strings, zpte_links);
 *  - the Luna rewrite from 11.10 (options zps_en_rewrite_20261011_*: index + one row per page and version);
 *  - ZP Translator v2 (options zpt2_*: inventory, active pointer, published map).
 * Nothing here writes to those options or tables: reinstalling the previous plugin version brings them back as
 * they were. The import (Import.php) copies them into the zpen_* tables once; until it has, Serve asks here.
 */
final class Legacy {
    const CODEX_INDEX = 'zps_en_rewrite_20261011_index';
    const CODEX_PREFIX = 'zps_en_rewrite_20261011_';
    const NIGHTLY_REGISTRY = 'zps_en_nightly_20261011_registry';
    const V2_PREFIX = 'zpt2_';
    /** Page states of the rewrite whose map was public (the same list its own serving code accepted). */
    const CODEX_SERVED = ['staged', 'activation_prepared', 'checking_serving', 'verified'];
    /** Five fixed corrections the rewrite applied on top of its maps (semantic repair of 12.10). */
    const CODEX_FIXES = [
        ['/strony-internetowe-dla-restauracji/', 'Jak chcesz się z nami skontaktować?', 'How would you like us to contact you?', 'How would you like to contact us?'],
        ['/strony-internetowe/woocommerce-czy-prestashop/', 'Pozycjonowanie w AI: jak przygotować firmową stronę do odpowiedzi AI', 'AI SEO: how to prepare your business website to answer AI queries', 'AI SEO: how to prepare your business website for AI-generated answers'],
        ['/strony-wordpress/', 'Rano', 'Rano', 'Morning'],
        ['/realizacje/', 'Studio wyceny', 'Valuation studio', 'Quote Studio'],
    ];

    private static $cache = [];

    private static function opt(string $name) {
        // These options are large and never autoloaded: read once per request.
        if (!array_key_exists($name, self::$cache)) { self::$cache[$name] = get_option($name, null); }
        return self::$cache[$name];
    }

    public static function forget(): void { self::$cache = []; }

    // ------------------------------------------------------------- rewrite (11.10)

    /** Polish path => English path of every page the rewrite served (its fixed list + pages added nightly). */
    public static function codex_routes(): array {
        $out = [];
        $reg = self::opt(self::NIGHTLY_REGISTRY);
        foreach (is_array($reg) ? $reg : [] as $source => $row) {
            if (is_string($source) && is_array($row) && is_string($row['en'] ?? null) && strpos($row['en'], '/en/') === 0) { $out[$source] = (string) $row['en']; }
        }
        return $out;
    }

    /** Sources with an active version in the rewrite. @return array<string,string> source => revision */
    public static function codex_active(): array {
        $idx = self::opt(self::CODEX_INDEX);
        $act = is_array($idx) && is_array($idx['active'] ?? null) ? $idx['active'] : [];
        $out = [];
        foreach ($act as $source => $rev) {
            if (is_string($source) && is_string($rev) && preg_match('~^[a-f0-9]{32}$~', $rev)) { $out[$source] = $rev; }
        }
        return $out;
    }

    /** The map the rewrite served for a page (Polish key => English), with its fixed corrections. */
    public static function codex_map(string $source): array {
        $rev = self::codex_active()[$source] ?? '';
        if ($rev === '') { return []; }
        return self::codex_page_map($source, $rev);
    }

    private static function codex_page_map(string $source, string $rev): array {
        $row = self::opt(self::CODEX_PREFIX . 'p_' . hash('sha256', $rev . '|' . $source));
        if (!is_array($row) || ($row['source'] ?? '') !== $source || !in_array($row['state'] ?? '', self::CODEX_SERVED, true) || !is_array($row['map'] ?? null)) { return []; }
        $map = self::strings_only($row['map']);
        foreach (self::CODEX_FIXES as [$src, $pl, $old, $new]) {
            if ($src === $source && ($map[$pl] ?? null) === $old) { $map[$pl] = $new; }
        }
        return $map;
    }

    // ---------------------------------------------------------------------- v2

    /** Pages v2 published: Polish path => English path. */
    public static function v2_routes(): array {
        $out = [];
        $inv = self::opt(self::V2_PREFIX . 'inventory');
        foreach (is_array($inv) ? $inv : [] as $source => $row) {
            if (!is_string($source) || !is_array($row)) { continue; }
            $p = self::v2_published($source);
            $en = is_array($p['snapshot'] ?? null) ? (string) ($p['snapshot']['en'] ?? '') : '';
            if ($en !== '' && strpos($en, '/en/') === 0) { $out[$source] = $en; }
        }
        return $out;
    }

    /** The published v2 version of a page (map, snapshot), or []. */
    public static function v2_published(string $source): array {
        $ptr = self::opt(self::V2_PREFIX . 'active_' . hash('sha256', $source));
        if (!is_array($ptr) || !is_string($ptr['id'] ?? null) || $ptr['id'] === '') { return []; }
        $pub = self::opt(self::V2_PREFIX . 'published_' . $ptr['id']);
        if (!is_array($pub) || ($pub['source'] ?? null) !== $source || !is_array($pub['map'] ?? null)) { return []; }
        return $pub;
    }

    public static function v2_map(string $source): array {
        $p = self::v2_published($source);
        $map = $p ? self::strings_only($p['map']) : [];
        if ($map) { return $map; }
        // v2 also served an older rewrite version it kept a reference to.
        $ref = self::opt(self::V2_PREFIX . 'legacy_' . hash('sha256', $source));
        $rev = is_array($ref) ? (string) ($ref['revision'] ?? '') : '';
        return preg_match('~^[a-f0-9]{32}$~', $rev) ? self::codex_page_map($source, $rev) : [];
    }

    // -------------------------------------------------------- Tłumacz EN 1 (tables)

    public static function zpte_ready(): bool {
        global $wpdb;
        $t = $wpdb->prefix . 'zpte_strings';
        return (string) $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $t)) === $t;
    }

    /** Pages of Tłumacz EN 1 with their translations. @return array<int,array{path:string,post_id:int,en_path:string}> */
    public static function zpte_paths(): array {
        global $wpdb;
        if (!self::zpte_ready()) { return []; }
        $rows = $wpdb->get_results('SELECT id, path, post_id, en_path, status FROM ' . $wpdb->prefix . 'zpte_paths', ARRAY_A);
        $out = [];
        foreach ((array) $rows as $r) { $out[(int) $r['id']] = ['path' => (string) $r['path'], 'post_id' => (int) $r['post_id'], 'en_path' => (string) $r['en_path'], 'status' => (string) $r['status']]; }
        return $out;
    }

    /** Translations used by one page of Tłumacz EN 1: Polish key => English. */
    public static function zpte_page_map(int $path_id): array {
        global $wpdb;
        if (!self::zpte_ready()) { return []; }
        $rows = $wpdb->get_results($wpdb->prepare('SELECT s.pl, s.en FROM ' . $wpdb->prefix . 'zpte_links l JOIN ' . $wpdb->prefix . "zpte_strings s ON s.id = l.string_id WHERE l.path_id = %d AND s.en <> ''", $path_id), ARRAY_N);
        $out = [];
        foreach ((array) $rows as $r) { $out[(string) $r[0]] = (string) $r[1]; }
        return $out;
    }

    /** Translations Tłumacz EN 1 showed on every page (post titles, excerpts). */
    public static function zpte_global(): array {
        global $wpdb;
        if (!self::zpte_ready()) { return []; }
        $rows = $wpdb->get_results('SELECT pl, en, manual FROM ' . $wpdb->prefix . "zpte_strings WHERE is_global = 1 AND en <> ''", ARRAY_N);
        $out = [];
        foreach ((array) $rows as $r) { $out[(string) $r[0]] = (string) $r[1]; }
        return $out;
    }

    /** Every translation of Tłumacz EN 1 in chunks (import): [[pl, en, kind, manual, is_global], ...]. */
    public static function zpte_strings(int $after_id, int $limit): array {
        global $wpdb;
        if (!self::zpte_ready()) { return []; }
        $rows = $wpdb->get_results($wpdb->prepare('SELECT id, pl, en, kind, manual, is_global FROM ' . $wpdb->prefix . "zpte_strings WHERE id > %d AND en <> '' ORDER BY id ASC LIMIT %d", $after_id, $limit), ARRAY_A);
        return is_array($rows) ? $rows : [];
    }

    // ------------------------------------------------------------------- shared

    /** Every page one of the earlier translators served in English: Polish path => English path. */
    public static function all_routes(): array {
        $out = [];
        foreach (self::zpte_paths() as $p) {
            if ($p['en_path'] !== '' && strpos($p['en_path'], '/en/') === 0 && $p['status'] === 'published') { $out[$p['path']] = $p['en_path']; }
        }
        foreach (self::codex_routes() as $pl => $en) { $out[$pl] = $en; }
        foreach (self::v2_routes() as $pl => $en) { $out[$pl] = $en; }
        return $out;
    }

    /** What the earlier translators served for one page, merged in their own order (v2 over the rewrite). */
    public static function page_map(string $source): array {
        $map = self::codex_map($source);
        foreach (self::v2_map($source) as $pl => $en) { $map[$pl] = $en; }
        return $map;
    }

    private static function strings_only(array $map): array {
        $out = [];
        foreach ($map as $pl => $en) {
            if (is_string($pl) && $pl !== '' && is_string($en) && $en !== '') { $out[$pl] = $en; }
        }
        return $out;
    }
}
