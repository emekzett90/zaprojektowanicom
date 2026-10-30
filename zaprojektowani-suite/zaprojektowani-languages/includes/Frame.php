<?php
namespace ZPL;

if (!defined('ABSPATH')) { exit; }

/**
 * Static HTML documents of the Suite loaded in iframes (e.g. the Quote Studio chooser) are served
 * straight from disk and never pass through WordPress. For English they are served through
 * /?zpl_frame=<path under zaprojektowani-suite/assets/> and translated like a page.
 * 1.2.0: German and Spanish too (&zpl_lang=de|es; the translations are those of the source /__frame/<path>).
 */
final class Frame {
    const RX_REL = '~^[A-Za-z0-9_-]+(?:/[A-Za-z0-9._-]+)*\.html$~';

    /** Asset path relative to zaprojektowani-suite/assets/ when $src points to a Suite HTML document. */
    public static function rel(string $src): ?string {
        $src = html_entity_decode($src, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        if (!preg_match('~/wp-content/plugins/zaprojektowani-suite/assets/([^?#"\']+\.html)(?:\?([^#]*))?~i', $src, $m)) { return null; }
        return (strpos($m[1], '..') === false && preg_match(self::RX_REL, $m[1])) ? $m[1] : null;
    }

    public static function src(string $src, string $lang = 'en'): string {
        $rel = self::rel($src);
        if ($rel === null) { return $src; }
        $q = [];
        parse_str((string) wp_parse_url(html_entity_decode($src, ENT_QUOTES | ENT_HTML5, 'UTF-8'), PHP_URL_QUERY), $q);
        $args = ['zpl_frame' => $rel];
        if (Lang::ai($lang)) { $args['zpl_lang'] = $lang; }
        if (isset($q['v']) && is_scalar($q['v'])) { $args['v'] = preg_replace('~[^A-Za-z0-9._-]~', '', (string) $q['v']); }
        return add_query_arg($args, home_url('/'));
    }

    public static function serve(): void {
        $rel = is_string($_GET['zpl_frame'] ?? null) ? wp_unslash($_GET['zpl_frame']) : '';
        $file = WP_PLUGIN_DIR . '/zaprojektowani-suite/assets/' . $rel;
        if ($rel === '' || strpos($rel, '..') !== false || !preg_match(self::RX_REL, $rel) || !is_file($file)) {
            status_header(404);
            header('Content-Type: text/plain; charset=UTF-8');
            echo 'Not found';
            exit;
        }
        $html = (string) file_get_contents($file);
        $dir = dirname($rel);
        $base = plugins_url('zaprojektowani-suite/assets/' . ($dir === '.' ? '' : $dir . '/'));
        $html = preg_replace('~(<head\b[^>]*>)~i', '$1<base href="' . esc_url($base) . '">', $html, 1) ?? $html;
        $lang = is_string($_GET['zpl_lang'] ?? null) && Lang::ai((string) $_GET['zpl_lang']) ? (string) $_GET['zpl_lang'] : 'en';
        $html = preg_replace('~(<html\b[^>]*\blang=)(["\'])pl(?:-PL)?\2~i', '$1$2' . Lang::HTML[$lang] . '$2', $html, 1) ?? $html;
        Router::$lang = $lang;
        Router::$source = '/';
        $source = '/__frame/' . $rel;
        $out = Page::translate($html, static function ($k, $kind) use ($source, $lang) { return Dict::get($k, $lang, $source); }, static function ($u) use ($lang) { return Router::url($u, $lang); }, ['lang' => $lang, 'locale' => Lang::LOCALE[$lang], 'html' => Lang::HTML[$lang]]);
        // German/Spanish frames: prices in euro (the English frame keeps what it had).
        if (Lang::ai($lang)) { $out = (string) apply_filters('zp_en_public_price_html', $out, $lang, $source); }
        if ($lang === 'en' && Page::$missing) { Missing::remember(Page::$missing, '/studio-wyceny/'); }
        status_header(200);
        header('Content-Type: text/html; charset=UTF-8');
        header('Cache-Control: public, max-age=600');
        header('X-Robots-Tag: noindex, nofollow');
        header('Content-Language: ' . $lang);
        echo $out;
        exit;
    }
}
