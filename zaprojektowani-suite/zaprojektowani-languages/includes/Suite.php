<?php
namespace ZPL;

if (!defined('ABSPATH') && !defined('ZPL_CLI')) { exit; }

/**
 * Compatibility with Zaprojektowani Suite. Suite scripts gate some behaviour on Polish
 * paths (location.pathname); on /en/ URLs those reads are routed through window.ZPLpl(),
 * which returns the Polish source path of the current page.
 */
final class Suite {
    const SERVICE = ['/strony-internetowe-katowice/', '/sklepy-internetowe-katowice/', '/logo-branding-katowice/', '/kampanie-reklamowe/'];

    /** Tiny early script: must run before any Suite inline script. */
    public static function early_script(string $source): string {
        $flags = JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE;
        return '<script id="zpl-early">window.ZPLSourcePath=' . json_encode($source, $flags)
            . ';window.ZPLpl=function(p){try{return p===location.pathname?window.ZPLSourcePath:p}catch(e){return p}};</script>';
    }

    /** Wrap pathname reads used for feature gates; skip URL-building / analytics / href comparisons. */
    public static function patch_js(string $code): string {
        if (strpos($code, 'location.pathname') === false) { return $code; }
        return preg_replace_callback('~(?<![\w.$])((?:w|window)\.location\.pathname|location\.pathname)(?![\w$])~', static function ($m) use ($code) {
            $at = $m[0][1];
            $before = substr($code, max(0, $at - 90), min(90, $at));
            $after = substr($code, $at + strlen($m[0][0]), 40);
            if (preg_match('~(?:replaceState|pushState|normalizePath|append|page_path|path\s*:|URL)\s*\(?\s*[^;]*$~', $before)) { return $m[0][0]; }
            if (preg_match('~^\s*\+\s*(?:w\.|window\.)?location\.(?:search|hash)~', $after)) { return $m[0][0]; }
            if (preg_match('~^\s*=[^=]~', $after)) { return $m[0][0]; } // assignment
            return 'window.ZPLpl(' . $m[0][0] . ')';
        }, $code, -1, $n, PREG_OFFSET_CAPTURE) ?? $code;
    }

    /** Inline scripts on English pages. Scripts comparing link hrefs keep the real pathname. */
    public static function patch_inline(string $html): string {
        return preg_replace_callback('~(<script\b(?![^>]*\bsrc=)([^>]*)>)(.*?)(</script\s*>)~is', static function ($m) {
            $type = '';
            if (preg_match('~\btype\s*=\s*["\']?([^"\'\s>]+)~i', $m[2], $t)) { $type = strtolower($t[1]); }
            if ($type !== '' && $type !== 'text/javascript' && $type !== 'module' && $type !== 'application/javascript') { return $m[0]; }
            if (strpos($m[3], 'location.pathname') === false) { return $m[0]; }
            if (preg_match('~getAttribute\(\s*[\'"]href~', $m[3])) { return $m[0]; }
            return $m[1] . self::patch_js($m[3]) . $m[4];
        }, $html) ?? $html;
    }

    /** Patched copies of Suite asset scripts that read location.pathname (both languages). */
    public static function script_url(string $src): string {
        if (strpos($src, '/zaprojektowani-suite/assets/js/') === false) { return $src; }
        $parts = wp_parse_url($src);
        $path = $parts['path'] ?? '';
        $pos = strpos($path, '/zaprojektowani-suite/assets/js/');
        if ($pos === false) { return $src; }
        $relFile = substr($path, $pos + strlen('/zaprojektowani-suite/'));
        $file = WP_PLUGIN_DIR . '/zaprojektowani-suite/' . $relFile;
        if (!is_file($file) || filesize($file) > 3000000) { return $src; }
        static $cache = [];
        $sig = $relFile . '|' . filemtime($file) . '|' . filesize($file) . '|' . ZPL_VERSION;
        if (!isset($cache[$sig])) {
            $cache[$sig] = '';
            $up = wp_upload_dir(null, false);
            if (empty($up['error'])) {
                $name = preg_replace('~[^a-z0-9]+~i', '-', $relFile) . '-' . substr(md5($sig), 0, 10) . '.js';
                $dir = trailingslashit($up['basedir']) . 'zpl-cache/js/';
                $target = $dir . $name;
                if (!is_file($target)) {
                    $code = (string) file_get_contents($file);
                    if (strpos($code, 'location.pathname') !== false) {
                        wp_mkdir_p($dir);
                        $patched = self::patch_js($code);
                        if ($patched !== $code) { @file_put_contents($target, $patched, LOCK_EX); }
                    }
                }
                if (is_file($target)) { $cache[$sig] = trailingslashit($up['baseurl']) . 'zpl-cache/js/' . $name; }
            }
        }
        if ($cache[$sig] === '') { return $src; }
        return set_url_scheme($cache[$sig]) . (isset($parts['query']) ? '?' . $parts['query'] : '');
    }

    public static function rewrite_script_urls(string $html): string {
        return preg_replace_callback('~(<script\b[^>]*?\b(?:src|data-zp-src)\s*=\s*)(["\'])([^"\']*/zaprojektowani-suite/assets/js/[^"\']+)\2~i', static function ($m) {
            $new = self::script_url(html_entity_decode($m[3], ENT_QUOTES));
            return $m[1] . $m[2] . esc_attr($new) . $m[2];
        }, $html) ?? $html;
    }

    /** Header: the service parent item is highlighted by Polish href selectors in Suite. */
    public static function nav_active(string $html, string $source): string {
        $src = rtrim($source, '/') . '/';
        if (!in_array($src, self::SERVICE, true)) { return $html; }
        return preg_replace_callback('~<(div|li)\b([^>]*\bclass=(["\'])([^"\']*\bzpNewNav__item\b[^"\']*)\3[^>]*\bdata-(?:mega|drop)\b[^>]*)>~i', static function ($m) {
            if (preg_match('~\bis-active\b~', $m[4])) { return $m[0]; }
            return str_replace($m[3] . $m[4] . $m[3], $m[3] . $m[4] . ' is-active' . $m[3], $m[0]);
        }, $html, 1) ?? $html;
    }

    /** admin-ajax JSON for requests coming from English pages: localize URLs and known strings. */
    public static function ajax(): void {
        $ref = (string) ($_SERVER['HTTP_REFERER'] ?? '');
        if ($ref === '') { return; }
        $rp = (string) wp_parse_url($ref, PHP_URL_PATH);
        $rel = Router::rel($rp);
        if ($rel !== '/en' && strpos($rel, '/en/') !== 0) { return; }
        $r = Router::resolve($rel);
        Router::$lang = 'en';
        Router::$source = $r[1] ?: '/';
        ob_start(static function ($out, $phase = 0) {
            if (!is_string($out) || $out === '' || ($out[0] !== '{' && $out[0] !== '[')) { return $out; }
            $j = json_decode($out, true);
            if (!is_array($j)) { return $out; }
            $j = self::ajax_value($j, '');
            $enc = wp_json_encode($j);
            return is_string($enc) ? $enc : $out;
        });
    }

    private static function ajax_value($v, string $key) {
        if (is_array($v)) { foreach ($v as $k => $x) { $v[$k] = self::ajax_value($x, (string) $k); } return $v; }
        if (!is_string($v) || $v === '') { return $v; }
        if (preg_match('~^(?:https?://|/)[^\s<>"]*$~i', $v) && (stripos($key, 'redirect') !== false || stripos($key, 'url') !== false || stripos($key, 'link') !== false || $key === 'href')) {
            return Router::url($v, 'en');
        }
        if (strpos($v, '<') !== false && preg_match('~<[a-z][^>]*>~i', $v)) { return Fragment::translate($v); }
        $n = Html::norm($v);
        if (!Html::human($n)) { return $v; }
        $t = Dict::get($n, 'en', Router::$source);
        return $t ?? $v;
    }
}
