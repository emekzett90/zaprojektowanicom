<?php
namespace ZPL;

if (!defined('ABSPATH') && !defined('ZPL_CLI')) { exit; }

/** Extraction of translatable units and rendering of a translated document. */
final class Page {
    const SCHEMA_KEYS = ['name'=>1,'headline'=>1,'alternativeHeadline'=>1,'description'=>1,'text'=>1,'caption'=>1,'articleSection'=>1,'slogan'=>1,'disambiguatingDescription'=>1,'abstract'=>1,'keywords'=>1,'serviceType'=>1,'category'=>1,'jobTitle'=>1,'award'=>1,'knowsAbout'=>1,'areaServed'=>1,'addressRegion'=>1,'priceSpecification'=>0];

    /** @var string[] extra JSON-LD fields to translate (option zpl_extra_schema, set by Skaner EN) */
    public static $extraSchema = [];

    private static function schema_key(string $key): bool { return !empty(self::SCHEMA_KEYS[$key]) || ($key !== '' && in_array($key, self::$extraSchema, true)); }

    /** @var array<string,int> */
    public static $stats = ['found' => 0, 'hit' => 0, 'miss' => 0];
    /** @var array<string,string> missing keys => kind */
    public static $missing = [];

    /** Items of an attribute-bearing element: [attrName, normalizedValue, kind]. */
    public static function attr_items(array $el, array $a): array {
        $out = [];
        $name = $el['n'];
        if ($name === 'meta') {
            $key = strtolower($a['name'] ?? ($a['property'] ?? ''));
            if (isset(Html::META[$key]) && isset($a['content'])) {
                $v = Html::norm($a['content']);
                if (Html::human($v)) { $out[] = ['content', $v, 'meta']; }
            }
            return $out;
        }
        foreach (Html::attr_names() as $at) {
            if (!isset($a[$at])) { continue; }
            $v = Html::norm($a[$at]);
            if (Html::human($v)) { $out[] = [$at, $v, 'attr']; }
        }
        if ($name === 'input' && isset($a['value'])) {
            $type = strtolower($a['type'] ?? 'text');
            if ($type === 'submit' || $type === 'button' || $type === 'reset') {
                $v = Html::norm($a['value']);
                if (Html::human($v)) { $out[] = ['value', $v, 'attr']; }
            }
        }
        return $out;
    }

    public static function schema_strings($v, array &$acc, string $key = ''): void {
        if (is_array($v)) {
            foreach ($v as $k => $x) { self::schema_strings($x, $acc, is_int($k) ? $key : (string) $k); }
            return;
        }
        if (is_string($v) && self::schema_key($key)) {
            $n = Html::norm(wp_strip_all_tags_safe($v));
            if (Html::human($n)) { $acc[] = $n; }
        }
    }

    /** List of translatable units of a document, in document order. */
    public static function extract(string $html): array {
        $items = [];
        $root = Html::parse($html);
        Html::walk($root, static function (array $seg) use (&$items) {
            $items[] = ['k' => $seg['k'], 'kind' => $seg['type'] === 'title' ? 'title' : 'text'];
        }, static function (array $el, ?array $a) use (&$items, $html) {
            if ($a === null) { $a = Html::attrs(substr($html, $el['s'], $el['te'] - $el['s'])); }
            if ($el['n'] === 'script') {
                if (strtolower($a['type'] ?? '') === 'application/ld+json' && !empty($el['c'])) {
                    $json = json_decode(substr($html, $el['c'][0]['s'], $el['c'][0]['e'] - $el['c'][0]['s']), true);
                    $acc = [];
                    if (is_array($json)) { self::schema_strings($json, $acc); }
                    foreach ($acc as $k) { $items[] = ['k' => $k, 'kind' => 'schema']; }
                }
                return;
            }
            foreach (self::attr_items($el, $a) as $it) { $items[] = ['k' => $it[1], 'kind' => $it[2]]; }
        });
        return $items;
    }

    /**
     * Share buttons (2.9.8): the address and the e-mail subject sit in the link's query string, where
     * the URL mapper does not look, so English posts were shared with the Polish address and title.
     * Only known share endpoints and mailto: links with a query are touched.
     */
    private static function share_href(string $href, callable $url, callable $t): string {
        $q = strpos($href, '?');
        if ($q === false) { return $href; }
        $base = substr($href, 0, $q);
        $mail = stripos($base, 'mailto:') === 0;
        if (!$mail && !preg_match('~^https?://(?:www\.)?(?:facebook\.com/sharer|linkedin\.com/(?:sharing|shareArticle)|(?:twitter|x)\.com/(?:intent|share)|wa\.me/|api\.whatsapp\.com/send|t\.me/share|pinterest\.com/pin/create)~i', $base)) { return $href; }
        $query = substr($href, $q + 1);
        $frag = '';
        if (($hp = strpos($query, '#')) !== false) { $frag = substr($query, $hp); $query = substr($query, 0, $hp); }
        $map = static function (string $v) use ($url): string {
            return (string) preg_replace_callback('~https?://[^\s<>"]+~i', static function ($m) use ($url) { return $url($m[0]); }, $v);
        };
        $changed = false;
        $pairs = explode('&', $query);
        foreach ($pairs as $i => $pair) {
            $eq = strpos($pair, '=');
            if ($eq === false) { continue; }
            $k = strtolower(substr($pair, 0, $eq));
            $raw = substr($pair, $eq + 1);
            $v = $mail ? rawurldecode($raw) : urldecode($raw);
            if (in_array($k, ['subject', 'title', 'text'], true)) {
                $n = Html::norm($v);
                $tr = Html::human($n) ? $t($n, 'text') : null;
                $new = is_string($tr) && $tr !== '' ? $tr : $map($v);
            } elseif (in_array($k, ['u', 'url', 'body', 'link'], true)) {
                $new = $map($v);
            } else {
                continue;
            }
            if ($new !== $v) { $pairs[$i] = substr($pair, 0, $eq + 1) . rawurlencode($new); $changed = true; }
        }
        return $changed ? $base . '?' . implode('&', $pairs) . $frag : $href;
    }

    /**
     * Render $html with $t(key, kind): ?string and $url(href): string.
     * $opts: lang (html lang), locale (og:locale).
     */
    public static function translate(string $html, callable $t, callable $url, array $opts = []): string {
        self::$stats = ['found' => 0, 'hit' => 0, 'miss' => 0];
        self::$missing = [];
        $root = Html::parse($html);
        $edits = [];      // start => [end, replacement]
        $tags = [];       // tagStart => [tagEnd, newTag]
        $lang = $opts['lang'] ?? 'en';
        $locale = $opts['locale'] ?? 'en_US';

        $lookup = static function (string $k, string $kind) use ($t) {
            // Script data holds names/codes too: never count it towards coverage or the missing list.
            if ($kind === 'data') { return $t($k, $kind); }
            self::$stats['found']++;
            $tr = $t($k, $kind);
            if ($tr === null) { self::$stats['miss']++; self::$missing[$k] = $kind; return null; }
            self::$stats['hit']++;
            return $tr;
        };

        $onAttr = static function (array $el, ?array $a) use (&$tags, &$edits, $html, $lookup, $t, $url, $lang, $locale) {
            $tag = substr($html, $el['s'], $el['te'] - $el['s']);
            if ($a === null) { $a = Html::attrs($tag); }
            $name = $el['n'];
            if ($name === 'script') {
                if (strtolower($a['type'] ?? '') === 'application/ld+json' && !empty($el['c'])) {
                    $s = $el['c'][0]['s']; $e = $el['c'][0]['e'];
                    $json = json_decode(substr($html, $s, $e - $s), true);
                    if (is_array($json)) {
                        $new = self::schema_translate($json, $lookup, $url, '');
                        if ($new !== $json) { $edits[$s] = [$e, wp_json_encode_safe($new)]; }
                    }
                } elseif ($lang === 'en' && !empty($el['c']) && self::is_js_type($a['type'] ?? '')) {
                    // Suite CMS data consumed by page scripts (e.g. window.zpRealizacjeProjects = [...]).
                    $s = $el['c'][0]['s']; $e = $el['c'][0]['e'];
                    $code = substr($html, $s, $e - $s);
                    if (preg_match('~^(\s*window\.(zp\w+)\s*=\s*)([\[{].*[\]}])(\s*;?\s*)$~s', $code, $m)) {
                        $data = json_decode($m[3], true);
                        if (is_array($data)) {
                            $new = self::data_translate($data, $lookup);
                            if ($new !== $data) {
                                $edits[$s] = [$e, $m[1] . wp_json_encode_safe($new) . ';(window.zplData=window.zplData||{}).' . $m[2] . '=' . wp_json_encode_safe($data) . ';' . "\n"];
                            }
                        }
                    }
                }
                return;
            }
            $set = [];
            foreach (self::attr_items($el, $a) as [$at, $v, $kind]) {
                $tr = $lookup($v, $kind);
                if ($tr !== null && $tr !== $v) { $set[$at] = $tr; }
            }
            if (($name === 'a' || $name === 'area') && isset($a['href'])) {
                $h = $url($a['href']);
                if ($h === $a['href'] && $lang === 'en') { $h = self::share_href($a['href'], $url, $t); }
                if ($h !== $a['href']) { $set['href'] = $h; }
            } elseif ($name === 'form' && isset($a['action']) && strtolower($a['method'] ?? 'get') === 'get') {
                $h = $url($a['action']);
                if ($h !== $a['action']) { $set['action'] = $h; }
            } elseif ($name === 'iframe' && $lang === 'en' && isset($a['src']) && class_exists(__NAMESPACE__ . '\\Frame', false) && function_exists('home_url')) {
                $f = Frame::src($a['src']);
                if ($f !== $a['src']) { $set['src'] = $f; $set['data-zpl-src-pl'] = $a['src']; }
            } elseif ($name === 'meta') {
                $prop = strtolower($a['property'] ?? '');
                if ($prop === 'og:locale' && isset($a['content'])) { $set['content'] = $locale; }
                elseif ($prop === 'og:url' && isset($a['content'])) { $set['content'] = $url($a['content']); }
                elseif ($lang === 'en' && isset($a['content'])) {
                    // Link previews (2.9.5): Rank Math's site name and reading-time labels stay Polish on /en/.
                    // Looked up without $lookup, so the bare brand name never lands on the missing list.
                    $key = $prop !== '' ? $prop : strtolower($a['name'] ?? '');
                    $v = Html::norm($a['content']);
                    if ($key === 'og:site_name' || $key === 'twitter:label1' || $key === 'twitter:label2') {
                        $tr = $t($v, 'meta');
                        if (is_string($tr) && $tr !== '' && $tr !== $v) { $set['content'] = $tr; }
                    } elseif ($key === 'twitter:data1' || $key === 'twitter:data2') {
                        // data2 on posts (data1 is the author there); data1 on pages, which have no author line (2.9.8).
                        // A name never matches the patterns below.
                        if (preg_match('~^(\d+)\s*minut(?:/y|y|a)?$~u', $v, $m)) {
                            $set['content'] = $m[1] . ((int) $m[1] === 1 ? ' minute' : ' minutes');
                        } elseif (preg_match('~^mniej niż minut[aę]$~iu', $v)) {
                            $set['content'] = 'Less than a minute';
                        }
                    }
                }
            }
            if (isset($a['lang']) && preg_match('~^pl\b~i', $a['lang'])) { $set['lang'] = $lang; }
            if ($set) { $tags[$el['s']] = [$el['te'], Html::set_attrs($tag, $set)]; }
        };

        Html::walk($root, static function (array $seg) use (&$edits, &$tags, $html, $lookup) {
            if ($seg['type'] === 'multi') {
                $tr = $lookup($seg['k'], 'text');
                if ($tr === null || $tr === $seg['k']) { return; }
                $tokens = Html::tokens($tr);
                if ($tokens === null || !Html::markers_match($seg['k'], $tr)) { self::$stats['miss']++; return; }
                $root = $seg['root'];
                $inner = substr($html, $root['te'], $root['es'] - $root['te']);
                preg_match('~^\s*~', $inner, $l); preg_match('~\s*$~', $inner, $r);
                $pos = 0;
                $body = self::rebuild($tokens, $pos, 0, $seg['els'], $html, $tags);
                $edits[$root['te']] = [$root['es'], ($l[0] ?? '') . $body . ($r[0] ?? '')];
                return;
            }
            $tr = $lookup($seg['k'], $seg['type'] === 'title' ? 'title' : 'text');
            if ($tr === null || $tr === $seg['k']) { return; }
            $node = $seg['node'];
            $raw = substr($html, $node['s'], $node['e'] - $node['s']);
            preg_match('~^\s*~', $raw, $l); preg_match('~\s*$~', $raw, $r);
            $edits[$node['s']] = [$node['e'], ($l[0] ?? '') . Html::esc($tr) . ($r[0] ?? '')];
        }, $onAttr);

        foreach ($tags as $s => $pair) { if (!isset($edits[$s]) && empty($pair[2])) { $edits[$s] = [$pair[0], $pair[1]]; } }
        if (!$edits) { return $html; }
        ksort($edits);
        $out = '';
        $pos = 0;
        foreach ($edits as $s => [$e, $rep]) {
            if ($s < $pos) { continue; } // nested edit already consumed by a rebuilt segment
            $out .= substr($html, $pos, $s - $pos) . $rep;
            $pos = $e;
        }
        return $out . substr($html, $pos);
    }

    /** Emit tokens up to the closing marker $until. Marks consumed tag edits. */
    private static function rebuild(array $tokens, int &$i, int $until, array $els, string $html, array &$tags): string {
        $out = '';
        $count = count($tokens);
        while ($i < $count) {
            [$type, $v] = $tokens[$i++];
            if ($type === 'x') { $out .= Html::esc($v); continue; }
            if ($type === 'c') { if ($v === $until) { return $out; } continue; }
            $el = $els[$v] ?? null;
            if (!$el) { continue; }
            if ($type === 'a') { $out .= self::emit_range($el['s'], $el['e'], $html, $tags); continue; }
            $start = isset($tags[$el['s']]) ? $tags[$el['s']][1] : substr($html, $el['s'], $el['te'] - $el['s']);
            if (isset($tags[$el['s']])) { $tags[$el['s']][2] = 1; }
            $end = $el['e'] > $el['es'] ? substr($html, $el['es'], $el['e'] - $el['es']) : '</' . $el['n'] . '>';
            $out .= $start . self::rebuild($tokens, $i, $v, $els, $html, $tags) . $end;
        }
        return $out;
    }

    private static function emit_range(int $s, int $e, string $html, array &$tags): string {
        $out = '';
        $pos = $s;
        foreach ($tags as $ts => $pair) {
            if ($ts < $s || $ts >= $e) { continue; }
            $out .= substr($html, $pos, $ts - $pos) . $pair[1];
            $tags[$ts][2] = 1;
            $pos = $pair[0];
        }
        return $out . substr($html, $pos, $e - $pos);
    }

    private static function is_js_type(string $type): bool {
        $type = strtolower(trim($type));
        return $type === '' || $type === 'text/javascript' || $type === 'application/javascript' || $type === 'module';
    }

    /** Human-readable strings in script data (URLs, ids and codes stay as they are). */
    private static function data_translate($v, callable $lookup) {
        if (is_array($v)) {
            foreach ($v as $k => $x) { $v[$k] = self::data_translate($x, $lookup); }
            return $v;
        }
        if (!is_string($v) || $v === '' || preg_match('~^(?:https?:|/|#|data:|mailto:|tel:)~i', $v)) { return $v; }
        $n = Html::norm($v);
        if ($n === '' || !Html::human($n)) { return $v; }
        $tr = $lookup($n, 'data');
        return $tr !== null ? $tr : $v;
    }

    private static function schema_translate($v, callable $lookup, callable $url, string $key) {
        if (is_array($v)) {
            foreach ($v as $k => $x) { $v[$k] = self::schema_translate($x, $lookup, $url, is_int($k) ? $key : (string) $k); }
            return $v;
        }
        if (!is_string($v)) { return $v; }
        if ($key === 'inLanguage') { return preg_match('~^pl~i', $v) ? $GLOBALS['zpl_schema_lang'] ?? 'en' : $v; }
        if (in_array($key, ['url', 'item', 'mainEntityOfPage', '@id'], true)) { return $url($v); }
        if (self::schema_key($key)) {
            $n = Html::norm(wp_strip_all_tags_safe($v));
            if (Html::human($n)) { $tr = $lookup($n, 'schema'); if ($tr !== null) { return $tr; } }
        }
        return $v;
    }
}

function wp_strip_all_tags_safe(string $s): string {
    if (strpos($s, '<') === false) { return $s; }
    return trim(strip_tags(preg_replace('~<(script|style)[^>]*?>.*?</\1>~si', '', $s) ?? $s));
}

function wp_json_encode_safe($v): string {
    $flags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG;
    $j = json_encode($v, $flags);
    return $j === false ? '{}' : $j;
}

namespace ZPL;

final class Fragment {
    /** Translate an HTML fragment (AJAX payloads) for the current request's language. */
    public static function translate(string $html): string {
        $wrapped = '<html><head></head><body>' . $html . '</body></html>';
        $source = Router::$source;
        $out = Page::translate($wrapped, static function ($k, $kind) use ($source) { return Dict::get($k, 'en', $source); }, static function ($u) { return Router::url($u, 'en'); });
        $a = strpos($out, '<body>');
        $b = strrpos($out, '</body>');
        return ($a !== false && $b !== false) ? substr($out, $a + 6, $b - $a - 6) : $html;
    }
}
