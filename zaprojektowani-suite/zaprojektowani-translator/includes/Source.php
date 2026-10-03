<?php
namespace ZPTE;

if (!defined('ABSPATH')) { exit; }

/**
 * The Polish page as visitors get it, cut into fragments by the language module itself (ZPL\Page::extract),
 * so the keys saved here are exactly the ones the English page will look up.
 */
final class Source {
    /**
     * Fetch the rendered Polish page from this server (loopback request).
     * @return array{ok:bool,html:string,code:int,error:string,gone:bool,redirect:string,via:string}
     */
    public static function fetch(string $path, int $post_id = 0): array {
        $out = ['ok' => false, 'html' => '', 'code' => 0, 'error' => '', 'gone' => false, 'redirect' => '', 'via' => 'http'];
        $res = wp_remote_get(home_url($path), [
            'timeout' => 45,
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
            $out['gone'] = true;
            $out['error'] = 'Polski adres przekierowuje' . ($out['redirect'] !== '' ? ' na ' . $out['redirect'] : '');
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
        try {
            $GLOBALS['post'] = $post;
            setup_postdata($post);
            $content = (string) apply_filters('the_content', $content);
            wp_reset_postdata();
        } catch (\Throwable $e) {
            $content = wpautop((string) $post->post_content);
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
