<?php
namespace ZPTE;

if (!defined('ABSPATH')) { exit; }

/**
 * 2.1.0: the language Tłumacz AI works on. English is as it always was (options zpte2_*, tables {wp}zpen_*);
 * German and Spanish have their own copies of everything (options zpte2_de_*, tables {wp}zpde_*, and so on),
 * made from the same Polish pages. Every class asks here for the names of the current language; Lang::with()
 * switches it for one piece of work (a worker step, an admin screen, a page the language module renders).
 * The OpenAI key, the schedule and the lock are shared by all languages.
 */
final class Lang {
    const ALL = ['en', 'de', 'es'];
    const AI = ['de', 'es'];
    /** Polish names for the panel. */
    const NAME = ['en' => 'angielski', 'de' => 'niemiecki', 'es' => 'hiszpański'];
    const CODE = ['en' => 'EN', 'de' => 'DE', 'es' => 'ES'];

    private static $code = 'en';

    public static function code(): string { return self::$code; }

    public static function is_en(): bool { return self::$code === 'en'; }

    public static function valid(string $lang): bool { return in_array($lang, self::ALL, true); }

    /** @return mixed what $fn returns, with $lang as the current language */
    public static function with(string $lang, callable $fn) {
        if (!self::valid($lang)) { $lang = 'en'; }
        $prev = self::$code;
        self::$code = $lang;
        try {
            return $fn();
        } finally {
            self::$code = $prev;
        }
    }

    /** Option of the current language: zpte2_routes (English, unchanged) or zpte2_de_routes. */
    public static function opt(string $name): string {
        if (self::$code === 'en' || strpos($name, 'zpte2_') !== 0) { return $name; }
        return 'zpte2_' . self::$code . '_' . substr($name, 6);
    }

    /** Table prefix after the WordPress one: zpen_, zpde_, zpes_. */
    public static function table_prefix(): string { return 'zp' . self::$code . '_'; }

    /** Address prefix of the current language: /en/, /de/, /es/. */
    public static function prefix(): string { return '/' . self::$code . '/'; }

    /** Languages the worker runs: English always (as before), German and Spanish when switched on in their settings. */
    public static function active(): array {
        $out = ['en'];
        foreach (self::AI as $l) {
            if (self::with($l, static function () { return (int) Settings::get('on') === 1; })) { $out[] = $l; }
        }
        return $out;
    }

    /** "Tłumacz EN", "Tłumacz DE"… */
    public static function title(): string { return 'Tłumacz ' . self::CODE[self::$code]; }
}
