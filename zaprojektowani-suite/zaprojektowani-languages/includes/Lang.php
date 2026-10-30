<?php
namespace ZPL;

if (!defined('ABSPATH') && !defined('ZPL_CLI')) { exit; }

/**
 * 1.2.0 (Suite 3.2.97): the languages of the site. Polish is the source; English, German and Spanish are served at
 * /en/, /de/ and /es/. English keeps everything it had (shipped dictionaries, its own filters); German and Spanish
 * come only from Tłumacz AI (filters zpl_lang_*), so English text written for /en/ can never show on /de/ or /es/.
 * German and Spanish join the switch, hreflang, sitemaps and the visitor redirect only once Tłumacz AI says the
 * language is ready (filter zpl_lang_public), so an empty /de/ never appears.
 */
final class Lang {
    const ALL = ['pl', 'en', 'de', 'es'];
    const FOREIGN = ['en', 'de', 'es'];
    /** Languages served from Tłumacz AI only (no shipped dictionaries, no English-only filters). */
    const AI = ['de', 'es'];
    const LOCALE = ['pl' => 'pl_PL', 'en' => 'en_US', 'de' => 'de_DE', 'es' => 'es_ES'];
    /** Value of <html lang> (English keeps "en", as before). */
    const HTML = ['pl' => 'pl-PL', 'en' => 'en', 'de' => 'de-DE', 'es' => 'es-ES'];
    const NAME = ['pl' => 'Polski', 'en' => 'English', 'de' => 'Deutsch', 'es' => 'Español'];
    const CODE = ['pl' => 'PL', 'en' => 'EN', 'de' => 'DE', 'es' => 'ES'];
    /** "Language" in each language (switch caption and aria-label). */
    const LABEL = ['pl' => 'Język', 'en' => 'Language', 'de' => 'Sprache', 'es' => 'Idioma'];
    /** Cookiebot culture of each language. */
    const COOKIEBOT = ['pl' => 'pl', 'en' => 'en', 'de' => 'de', 'es' => 'es'];

    private static $public = null;

    public static function valid(string $l): bool { return in_array($l, self::ALL, true); }
    public static function foreign(string $l): bool { return in_array($l, self::FOREIGN, true); }
    public static function ai(string $l): bool { return in_array($l, self::AI, true); }

    /** Language of a site-relative path by its prefix ("/de/…" => de); Polish otherwise. */
    public static function of_path(string $path): string {
        return preg_match('~^/(en|de|es)(?:/|$)~', $path, $m) ? $m[1] : 'pl';
    }

    /** "/de/" for German ("/" for Polish). */
    public static function prefix(string $l): string { return $l === 'pl' ? '/' : '/' . $l . '/'; }

    /** Languages visitors are offered (switch, hreflang, sitemaps, redirect): Polish, English, and German/Spanish once ready. */
    public static function public(): array {
        if (self::$public === null) {
            $out = ['pl', 'en'];
            foreach (self::AI as $l) {
                if (function_exists('apply_filters') && apply_filters('zpl_lang_public', false, $l)) { $out[] = $l; }
            }
            self::$public = $out;
        }
        return self::$public;
    }

    public static function is_public(string $l): bool { return in_array($l, self::public(), true); }

    public static function reset(): void { self::$public = null; }

    /** Best served language for an Accept-Language header among $allowed (null when none of them is asked for). */
    public static function from_accept(string $header, array $allowed): ?string {
        $best = null; $q = -1.0;
        foreach (explode(',', strtolower($header)) as $i => $part) {
            $bits = explode(';', trim($part));
            $code = substr(trim($bits[0]), 0, 2);
            $w = 1.0;
            foreach (array_slice($bits, 1) as $b) { if (preg_match('~^\s*q=([0-9.]+)~', $b, $m)) { $w = (float) $m[1]; } }
            $w -= $i * 0.0001; // earlier entries win ties
            if ($w > $q && in_array($code, $allowed, true)) { $best = $code; $q = $w; }
        }
        return $best;
    }
}
