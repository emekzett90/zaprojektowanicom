<?php
namespace ZPTE;

if (!defined('ABSPATH')) { exit; }

/**
 * The Polish page as visitors get it, cut into fragments by the language module itself (ZPL\Page::extract),
 * so the keys saved here are exactly the ones the English page will look up.
 * 2.1.0: German and Spanish also get what is not a page of the site (virtual()): the texts page scripts write after
 * load and the iframe documents of the Suite (the Quote Studio chooser).
 */
final class Source {
    /** 2.1.0: the page of texts page scripts write after load (German and Spanish only; English ships them). */
    const DYN = '/__dyn/';

    /** 2.1.0: iframe documents of the Suite served by the language module (\ZPL\Frame): source path => file. */
    public static function frames(): array {
        $out = [];
        if (!defined('ZP_SUITE_PATH') || !class_exists('ZPL\\Frame')) { return $out; }
        foreach ((array) glob(ZP_SUITE_PATH . 'assets/*/*.html') as $f) {
            $rel = substr((string) $f, strlen(ZP_SUITE_PATH . 'assets/'));
            if (preg_match(\ZPL\Frame::RX_REL, $rel)) { $out['/__frame/' . $rel] = (string) $f; }
        }
        return $out;
    }

    /**
     * 2.1.0: fragments of a page made from a file. '/__dyn/': the texts page scripts write after load, by the Polish keys
     * of the language module's list of them (data/json/en/dyn.json, the English version of the same texts);
     * '/__frame/<file>': an iframe document, read from disk as the language module serves it.
     * @return array{ok:bool,units:array,title:string,error:string}
     */
    public static function virtual(string $kind, string $path): array {
        $out = ['ok' => false, 'units' => [], 'title' => '', 'error' => ''];
        if ($kind === 'dyn') {
            $file = defined('ZPL_DIR') ? ZPL_DIR . 'data/json/en/dyn.json' : '';
            $raw = $file !== '' && is_file($file) ? file_get_contents($file) : false;
            $map = is_string($raw) ? json_decode($raw, true) : null;
            if (!is_array($map)) { $out['error'] = 'Brak listy tekstów skryptów (data/json/en/dyn.json)'; return $out; }
            foreach (array_keys($map) as $k) {
                $k = (string) $k;
                if ($k !== '' && strlen($k) <= Translator::MAX_KEY) { $out['units'][$k] = 'text'; }
            }
            $out['title'] = 'Teksty, które skrypty strony pokazują po jej załadowaniu: Studio wyceny, formularze, okna realizacji';
        } else {
            $file = self::frames()[$path] ?? '';
            $html = $file !== '' ? file_get_contents($file) : false;
            if (!is_string($html) || $html === '') { $out['error'] = 'Brak pliku ' . $path; return $out; }
            $out['units'] = self::units($html);
            $out['title'] = self::title($html) ?: 'Studio wyceny';
        }
        $out['ok'] = (bool) $out['units'];
        if (!$out['ok'] && $out['error'] === '') { $out['error'] = 'Brak tekstów w ' . $path; }
        return $out;
    }

    /**
     * Fetch the rendered Polish page from this server (loopback request).
     * @return array{ok:bool,html:string,code:int,error:string,gone:bool,redirect:string,via:string}
     */
    public static function fetch(string $path, int $post_id = 0, int $timeout = 10): array {
        $out = ['ok' => false, 'html' => '', 'code' => 0, 'error' => '', 'gone' => false, 'redirect' => '', 'via' => 'http'];
        $res = wp_remote_get(home_url($path), [
            'timeout' => max(2, min(15, $timeout)),
            'redirection' => 0,
            'user-agent' => 'Zaprojektowani-Tlumacz-EN/' . ZPTE_VERSION . '; ' . home_url('/'),
            // zpl_lang_pref=pl: a manual language choice, so the GEO redirect never sends this request to /en/.
            'cookies' => ['zpl_lang_pref' => 'pl'],
            'headers' => ['Accept' => 'text/html', 'Accept-Language' => 'pl-PL,pl;q=0.9'],
            'sslverify' => (bool) apply_filters('https_local_ssl_verify', false),
            'limit_response_size' => 8 * MB_IN_BYTES,
        ]);
        if (is_wp_error($res)) {
            $out['error'] = $res->get_error_message();
            return self::fallback($path, $post_id, $out);
        }
        $code = (int) wp_remote_retrieve_response_code($res);
        $out['code'] = $code;
        if ($code >= 300 && $code < 400) {
            $out['redirect'] = (string) wp_remote_retrieve_header($res, 'location');
            $out['error'] = 'Polski adres przekierowuje' . ($out['redirect'] !== '' ? ' na ' . $out['redirect'] : '');
            // Only a move to another page of this site counts (e.g. a redirect set in Rank Math). A redirect to the same
            // address, to logging in or to another site (security rules, maintenance) is a hiccup: the next check retries.
            $out['gone'] = self::moved($path, $out['redirect']);
            return $out;
        }
        if ($code === 404 || $code === 410) {
            $out['gone'] = true;
            $out['error'] = 'Polska strona nie istnieje (' . $code . ')';
            return $out;
        }
        $html = (string) wp_remote_retrieve_body($res);
        $type = (string) wp_remote_retrieve_header($res, 'content-type');
        if ($code !== 200 || ($type !== '' && stripos($type, 'html') === false) || stripos($html, '</head>') === false) {
            $out['error'] = 'Strona zwróciła kod ' . $code;
            return self::fallback($path, $post_id, $out);
        }
        $out['ok'] = true;
        $out['html'] = $html;
        return $out;
    }

    /** The redirect leads to a different page of this site. */
    private static function moved(string $path, string $location): bool {
        if ($location === '') { return false; }
        $to = wp_parse_url($location);
        $home = wp_parse_url(home_url('/'));
        if (!is_array($to) || !is_array($home)) { return false; }
        $host = strtolower((string) ($to['host'] ?? ''));
        $own = strtolower((string) ($home['host'] ?? ''));
        if ($host !== '' && $host !== $own && $host !== 'www.' . $own && 'www.' . $host !== $own) { return false; }
        $target = \ZPL\Router::norm_path(\ZPL\Router::rel((string) ($to['path'] ?? '/')));
        // Logging in, or the English version (a language redirect, which this request is never meant to get).
        if (preg_match('~^/(?:wp-login\.php|wp-admin|en)(?:/|$)~i', $target)) { return false; }
        return $target !== \ZPL\Router::norm_path($path);
    }

    /**
     * Server can't call itself (loopback blocked): rebuild the page text from the post. Template texts
     * (header, footer) are already translated by the shipped dictionary, so the post is what matters.
     */
    private static function fallback(string $path, int $post_id, array $out): array {
        $post = $post_id > 0 ? get_post($post_id) : null;
        if (!$post || $post->post_status !== 'publish') {
            $out['error'] = 'Nie udało się pobrać strony: ' . $out['error'];
            return $out;
        }
        $content = (string) $post->post_content;
        // Shortcodes and builders depend on the frontend route/query. Rendering them inside admin/cron
        // can silently select another service variant; never certify that as the source page.
        if (preg_match('~\[[a-z][a-z0-9_-]*(?:\s|\])~i', $content) || get_post_meta($post->ID, '_elementor_data', true)) {
            $out['error'] = 'Nie udało się pobrać pełnej strony (loopback): ' . $out['error'] . '. Ta strona używa szablonu lub kreatora — wymagane jest prawidłowe pobranie jej publicznego adresu';
            return $out;
        }
        $previous = $GLOBALS['post'] ?? null;
        try {
            $GLOBALS['post'] = $post;
            setup_postdata($post);
            $content = (string) apply_filters('the_content', $content);
        } catch (\Throwable $e) {
            $content = wpautop((string) $post->post_content);
        } finally {
            wp_reset_postdata();
            $GLOBALS['post'] = $previous;
        }
        $title = get_the_title($post);
        $desc = (string) get_post_meta($post->ID, 'rank_math_description', true);
        if ($desc === '' || strpos($desc, '%') !== false) { $desc = (string) $post->post_excerpt; }
        $html = '<!doctype html><html lang="pl-PL"><head><title>' . esc_html(wp_strip_all_tags($title)) . '</title>'
            . ($desc !== '' ? '<meta name="description" content="' . esc_attr(wp_strip_all_tags($desc)) . '">' : '')
            . '</head><body><main><h1>' . esc_html(wp_strip_all_tags($title)) . '</h1>' . $content
            . ($post->post_excerpt !== '' ? '<p>' . esc_html($post->post_excerpt) . '</p>' : '')
            . '</main></body></html>';
        $out['ok'] = true;
        $out['html'] = $html;
        $out['via'] = 'post';
        $out['error'] = 'Serwer nie może pobrać własnej strony (' . $out['error'] . ') — tłumaczę z treści wpisu.';
        return $out;
    }

    /** The Polish page asks search engines not to index it. */
    public static function noindex(string $html): bool {
        if (!preg_match_all('~<meta\b[^>]*>~i', substr($html, 0, (int) (stripos($html, '</head>') ?: strlen($html))), $m)) { return false; }
        foreach ($m[0] as $tag) {
            $a = \ZPL\Html::attrs($tag);
            if (strtolower($a['name'] ?? '') === 'robots' && stripos($a['content'] ?? '', 'noindex') !== false) { return true; }
        }
        return false;
    }

    /** Page title from <title> (for context and the English slug). */
    public static function title(string $html): string {
        if (preg_match('~<title\b[^>]*>(.*?)</title>~is', $html, $m)) { return \ZPL\Html::norm(\ZPL\Html::decode($m[1])); }
        return '';
    }

    /** Text of the first <h1>. */
    public static function h1(string $html): string {
        if (preg_match('~<h1\b[^>]*>(.*?)</h1>~is', $html, $m)) { return \ZPL\Html::norm(\ZPL\Html::decode(wp_strip_all_tags($m[1]))); }
        return '';
    }

    /**
     * Translatable fragments in document order: [key => kind]. Uses the same extra attributes and
     * structured-data fields as the live English pages (options set by Skaner EN).
     */
    public static function units(string $html): array {
        \ZPL\Html::$extra = self::opt_list('zpl_extra_attrs', '~^data-[a-z0-9-]{1,40}$~');
        \ZPL\Page::$extraSchema = self::opt_list('zpl_extra_schema', '~^[A-Za-z]{2,40}$~');
        $out = [];
        foreach (\ZPL\Page::extract($html) as $it) {
            $k = (string) ($it['k'] ?? '');
            if ($k === '' || isset($out[$k]) || strlen($k) > Translator::MAX_KEY) { continue; }
            $out[$k] = (string) ($it['kind'] ?? 'text');
        }
        return $out;
    }

    private static function opt_list(string $name, string $rx): array {
        $v = get_option($name, []);
        if (!is_array($v)) { return []; }
        return array_values(array_unique(array_filter(array_map('strval', $v), static function ($x) use ($rx) { return (bool) preg_match($rx, $x); })));
    }
}
