<?php
namespace ZPL;

if (!defined('ABSPATH') && !defined('ZPL_CLI')) { exit; }

/**
 * Translation dictionaries. Shipped shards live in data/php/{lang}/ (opcache friendly):
 *   common.php, p/{pageId}.php, index.php (key hash => shard). Manual overrides live in the
 *   zpl_overrides option and always win.
 * Direction "en" maps Polish keys to English; direction "pl" maps English keys back to Polish.
 */
final class Dict {
    private static $shards = [];
    private static $overrides = null;
    private static $page = [];
    /** @var array<string,array<string,string>> extra dictionaries from the zpl_dictionary filter, per direction+page */
    private static $external = [];
    /** @var array<string,array<int,array<string,string>>> manual overrides + priority layers, per direction+page */
    private static $priority = [];

    public static function page_id(string $source): string { return substr(md5($source), 0, 12); }

    /** External/AI dictionaries exist in WordPress, not in shipped JSON files.
     * Both directions must be present because the same page switches PL <-> EN.
     * Missing static shards use the existing REST lookup without a doomed 404.
     */
    public static function client_page_id(string $source): string {
        $id = self::page_id($source);
        return is_file(ZPL_DIR . 'data/json/en/' . $id . '.json')
            && is_file(ZPL_DIR . 'data/json/pl/' . $id . '.json') ? $id : '';
    }

    public static function dir(string $lang): string { return ZPL_DIR . 'data/php/' . ($lang === 'pl' ? 'pl' : 'en') . '/'; }

    private static function shard(string $lang, string $name): array {
        $k = $lang . '/' . $name;
        if (!array_key_exists($k, self::$shards)) {
            $f = self::dir($lang) . $name . '.php';
            if (is_file($f)) {
                self::$shards[$k] = (array) include $f;
            } elseif ($lang === 'pl' && is_file(self::dir('en') . $name . '.php')) {
                // Suite 2.9.12: the SEO plan's texts (seo-230, seo-271: page titles, H1s, sections) ship in the
                // PL => EN direction only, so on a page opened in English the switch to Polish left them in
                // English (the home H1 among them). Polish for those is the English shard read backwards.
                $rev = [];
                foreach ((array) include self::dir('en') . $name . '.php' as $pl => $en) {
                    $rev[Html::norm((string) $en)] = (string) $pl;
                }
                self::$shards[$k] = $rev;
            } else {
                self::$shards[$k] = [];
            }
        }
        return self::$shards[$k];
    }

    public static function overrides(string $lang = 'en'): array {
        if (self::$overrides === null) {
            $o = get_option('zpl_overrides', []);
            self::$overrides = is_array($o) ? $o : [];
        }
        $en = self::$overrides;
        if ($lang === 'en') { return $en; }
        $rev = [];
        foreach ($en as $pl => $tr) { $rev[Html::norm((string) $tr)] = $pl; }
        return $rev;
    }

    public static function flush(): void { self::$overrides = null; self::$shards = []; self::$page = []; self::$external = []; self::$priority = []; }

    /**
     * Translations that outrank the shipped dictionaries (filter zpl_priority_dictionary, Tłumacz EN):
     * [page map, shared map] for this page and direction. Loaded once per page and direction.
     */
    private static function priority(string $lang, string $source): array {
        $k = $lang . $source;
        if (!isset(self::$priority[$k])) {
            $v = function_exists('apply_filters') ? apply_filters('zpl_priority_dictionary', [], $lang, $source) : [];
            $layers = [self::overrides($lang)];
            foreach (is_array($v) ? $v : [] as $layer) { if (is_array($layer) && $layer) { $layers[] = $layer; } }
            self::$priority[$k] = $layers;
        }
        return self::$priority[$k];
    }

    /** Shipped page dictionary, or a page translated by another module (filter zpl_has_page, e.g. Tłumacz EN). */
    public static function has_page(string $source): bool {
        if (is_file(self::dir('en') . 'p/' . self::page_id($source) . '.php')) { return true; }
        return function_exists('apply_filters') && (bool) apply_filters('zpl_has_page', false, $source);
    }

    /**
     * Last-resort translations for one page (filter zpl_dictionary: key => translation, $lang is the direction).
     * Shipped dictionaries and manual overrides always win; loaded once per page and direction.
     */
    private static function external(string $lang, string $source): array {
        $k = $lang . $source;
        if (!isset(self::$external[$k])) {
            $v = function_exists('apply_filters') ? apply_filters('zpl_dictionary', [], $lang, $source) : [];
            self::$external[$k] = is_array($v) ? $v : [];
        }
        return self::$external[$k];
    }

    /** Exact lookup (no templates): manual overrides, Tłumacz EN, shipped dictionaries, other modules. */
    public static function exact(string $key, string $lang, string $source): ?string {
        foreach (self::priority($lang, $source) as $layer) {
            if (isset($layer[$key])) { return (string) $layer[$key]; }
        }
        return self::shipped($key, $lang, $source);
    }

    /** Shipped dictionaries, then the zpl_dictionary filter (last resort). */
    private static function shipped(string $key, string $lang, string $source): ?string {
        $pid = self::$page[$lang . $source] ?? (self::$page[$lang . $source] = 'p/' . self::page_id($source));
        $s = self::shard($lang, $pid);
        if (isset($s[$key])) { return $s[$key]; }
        $c = self::shard($lang, 'common');
        if (isset($c[$key])) { return $c[$key]; }
        // Texts added by the Suite's SEO plan (2.3.0 titles and headings, 2.7.1 page sections).
        foreach (['seo-230', 'seo-271'] as $extra) {
            $c = self::shard($lang, $extra);
            if (isset($c[$key])) { return $c[$key]; }
        }
        $idx = self::shard($lang, 'index');
        $h = substr(md5($key), 0, 10);
        if (isset($idx[$h])) {
            $s = self::shard($lang, $idx[$h]);
            if (isset($s[$key])) { return $s[$key]; }
        }
        $x = self::external($lang, $source);
        if (isset($x[$key])) { return (string) $x[$key]; }
        return null;
    }

    /** Lookup with number templates ("{1} czytań" => "{1} reads"). */
    public static function get(string $key, string $lang, string $source): ?string {
        // Resolve numeric templates inside each priority layer before any shipped raw key.
        $t = self::template($key);
        foreach (self::priority($lang, $source) as $layer) {
            if (isset($layer[$key])) { return (string) $layer[$key]; }
            if ($t !== null && isset($layer[$t[0]])) { return self::fill((string) $layer[$t[0]], $t[1], $lang); }
        }
        $v = self::shipped($key, $lang, $source);
        if ($v !== null) { return $v; }
        if ($t !== null) {
            $v = self::shipped($t[0], $lang, $source);
            if ($v !== null) { return self::fill($v, $t[1], $lang); }
        }
        return null;
    }

    /**
     * Shipped dictionaries only (with number templates), without manual overrides, Tłumacz EN or other modules.
     * Tłumacz EN asks this to know which texts the plugin already translates.
     */
    public static function base(string $key, string $lang, string $source): ?string {
        $t = self::template($key);
        foreach ([$key, $t[0] ?? null] as $i => $k) {
            if ($k === null) { continue; }
            $pid = 'p/' . self::page_id($source);
            $v = self::shard($lang, $pid)[$k] ?? self::shard($lang, 'common')[$k] ?? self::shard($lang, 'seo-230')[$k] ?? self::shard($lang, 'seo-271')[$k] ?? null;
            if ($v === null) {
                $idx = self::shard($lang, 'index');
                $h = substr(md5($k), 0, 10);
                if (isset($idx[$h])) { $v = self::shard($lang, $idx[$h])[$k] ?? null; }
            }
            if ($v !== null) { return $i === 0 ? (string) $v : self::fill((string) $v, $t[1], $lang); }
        }
        return null;
    }

    /**
     * Shipped dictionaries and other modules' curated texts (filter zpl_dictionary), with number templates; without
     * manual overrides or Tłumacz EN. Tłumacz EN asks this to know which texts the plugin already translates, so
     * those are never sent to OpenAI and keep their wording.
     */
    public static function lower(string $key, string $lang, string $source): ?string {
        $v = self::shipped($key, $lang, $source);
        if ($v !== null) { return $v; }
        $t = self::template($key);
        if ($t !== null && ($v = self::shipped($t[0], $lang, $source)) !== null) { return self::fill($v, $t[1], $lang); }
        return null;
    }

    public static function template(string $key): ?array {
        $vals = [];
        $parts = preg_split('~(</?\d+/?>)~', $key, -1, PREG_SPLIT_DELIM_CAPTURE) ?: [$key];
        for ($i = 0; $i < count($parts); $i += 2) {
            $parts[$i] = preg_replace_callback('/\d+(?:[.,:\x{00A0}\x{202F} ]\d+)*/u', static function ($m) use (&$vals) { $vals[] = $m[0]; return '{' . count($vals) . '}'; }, $parts[$i]) ?? $parts[$i];
        }
        return $vals ? [implode('', $parts), $vals] : null;
    }

    public static function fill(string $tr, array $vals, string $lang = 'en'): string {
        return preg_replace_callback('~\{(\d+)\}~', static function ($m) use ($vals, $lang) {
            $v = $vals[(int) $m[1] - 1] ?? null;
            return $v === null ? $m[0] : self::num($v, $lang);
        }, $tr) ?? $tr;
    }

    /** "1 500" <-> "1,500" and "4,9" <-> "4.9"; dates, times and plain numbers are untouched. */
    public static function num(string $v, string $lang): string {
        // Phone numbers ("501 054 253", "+48 501 054 253") keep their spaces: no price on the site has 9 digits.
        if (strlen((string) preg_replace('/\D/', '', $v)) >= 9) { return $v; }
        if ($lang === 'en') {
            if (preg_match('/^\d{1,3}(?:[ \x{00A0}\x{202F}]\d{3})+$/u', $v)) { return preg_replace('/[ \x{00A0}\x{202F}]/u', ',', $v) ?? $v; }
            if (preg_match('/^\d+,\d{1,2}$/', $v)) { return str_replace(',', '.', $v); }
        } else {
            if (preg_match('/^\d{1,3}(?:,\d{3})+$/', $v)) { return str_replace(',', "\u{00A0}", $v); }
            if (preg_match('/^\d+\.\d{1,2}$/', $v)) { return str_replace('.', ',', $v); }
        }
        return $v;
    }

    public static function meta(): array {
        static $m = null;
        if ($m === null) { $f = ZPL_DIR . 'data/meta.php'; $m = is_file($f) ? (array) include $f : []; }
        return $m;
    }
}
