<?php
namespace ZPL;

if (!defined('ABSPATH') && !defined('ZPL_CLI')) { exit; }

/**
 * HTML segmentation shared (rule-for-rule) with assets/zpl.js.
 *
 * A "segment" is either a single text node, or an inline-only element that has
 * direct text; the latter is keyed as text with numbered inline markers:
 *   "Tworzymy <1>strony</1> dla firm<2/>"   (<N>..</N> paired, <N/> atom)
 * Everything outside segments and translated attributes is emitted byte-for-byte.
 */
final class Html {
    const VOID = ['area'=>1,'base'=>1,'br'=>1,'col'=>1,'embed'=>1,'hr'=>1,'img'=>1,'input'=>1,'link'=>1,'meta'=>1,'param'=>1,'source'=>1,'track'=>1,'wbr'=>1,'keygen'=>1];
    const RAW = ['script'=>1,'style'=>1,'textarea'=>1,'title'=>1,'xmp'=>1,'iframe'=>1,'noembed'=>1,'noframes'=>1,'noscript'=>1];
    /** Subtrees never translated (as atoms when inside a segment). */
    const SKIP = ['script'=>1,'style'=>1,'textarea'=>1,'noscript'=>1,'code'=>1,'pre'=>1,'svg'=>1,'math'=>1,'iframe'=>1,'object'=>1,'canvas'=>1,'video'=>1,'audio'=>1,'select'=>0,'kbd'=>1,'samp'=>1,'head'=>1];
    /** Phrasing elements that may live inside a segment. */
    const INLINE = ['a'=>1,'abbr'=>1,'b'=>1,'bdi'=>1,'bdo'=>1,'br'=>1,'cite'=>1,'code'=>1,'data'=>1,'del'=>1,'dfn'=>1,'em'=>1,'font'=>1,'i'=>1,'img'=>1,'ins'=>1,'kbd'=>1,'mark'=>1,'q'=>1,'s'=>1,'samp'=>1,'small'=>1,'span'=>1,'strong'=>1,'sub'=>1,'sup'=>1,'time'=>1,'u'=>1,'var'=>1,'wbr'=>1,'svg'=>1,'picture'=>1,'input'=>1];
    /** Inline elements whose content is opaque (always an atom marker). */
    const ATOM = ['br'=>1,'wbr'=>1,'img'=>1,'svg'=>1,'picture'=>1,'input'=>1,'code'=>1,'kbd'=>1,'samp'=>1,'math'=>1];
    const P_CLOSERS = ['address'=>1,'article'=>1,'aside'=>1,'blockquote'=>1,'details'=>1,'dialog'=>1,'div'=>1,'dl'=>1,'fieldset'=>1,'figcaption'=>1,'figure'=>1,'footer'=>1,'form'=>1,'h1'=>1,'h2'=>1,'h3'=>1,'h4'=>1,'h5'=>1,'h6'=>1,'header'=>1,'hgroup'=>1,'hr'=>1,'main'=>1,'menu'=>1,'nav'=>1,'ol'=>1,'p'=>1,'pre'=>1,'section'=>1,'summary'=>1,'table'=>1,'ul'=>1,'li'=>1,'dd'=>1,'dt'=>1,'search'=>1];
    const SCOPE = ['html'=>1,'table'=>1,'td'=>1,'th'=>1,'caption'=>1,'template'=>1,'object'=>1,'marquee'=>1,'applet'=>1,'button'=>1,'svg'=>1,'math'=>1];
    const HEADINGS = ['h1'=>1,'h2'=>1,'h3'=>1,'h4'=>1,'h5'=>1,'h6'=>1];
    /** Translatable attributes (any element). */
    const ATTRS = ['alt','title','placeholder','aria-label','aria-placeholder','aria-description','aria-roledescription','aria-valuetext','data-zpl-text','label'];
    /** @var string[] data-* attributes shown on the page by scripts/CSS (option zpl_extra_attrs, set by Skaner EN) */
    public static $extra = [];

    /** @return string[] */
    public static function attr_names(): array { return self::$extra ? array_merge(self::ATTRS, self::$extra) : self::ATTRS; }
    const META = ['description'=>1,'og:title'=>1,'og:description'=>1,'og:image:alt'=>1,'twitter:title'=>1,'twitter:description'=>1,'twitter:image:alt'=>1];

    /** @var string */
    private static $html = '';

    // ------------------------------------------------------------------ text

    public static function norm(string $s): string {
        $s = preg_replace('/[\x{00AD}\x{200B}\x{200C}\x{200D}\x{2060}\x{FEFF}]/u', '', $s) ?? $s;
        $s = preg_replace('/[\t\n\x0B\f\r \x{00A0}\x{1680}\x{2000}-\x{200A}\x{2028}\x{2029}\x{202F}\x{205F}\x{3000}]+/u', ' ', $s) ?? $s;
        return trim($s, ' ');
    }

    public static function decode(string $raw): string {
        return strpos($raw, '&') === false ? $raw : html_entity_decode($raw, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /** Worth translating: has a letter and is not a URL, e-mail or code-like token. */
    public static function human(string $n): bool {
        if ($n === '' || strlen($n) > 20000 || !preg_match('/\p{L}/u', $n)) { return false; }
        if (preg_match('~^(?:https?:)?//|^(?:mailto|tel):|^[\w.+-]+@[\w-]+\.[\w.-]+$|^www\.\S+$|^[#.]?[a-z][\w-]*\{~i', $n)) { return false; }
        if (preg_match('/^[\d\s.,:;%+\-–—\/()×x*#€$zł]+$/u', $n) && !preg_match('/zł/u', $n)) { return false; }
        return true;
    }

    public static function esc(string $s): string {
        return htmlspecialchars($s, ENT_NOQUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8');
    }

    // ------------------------------------------------------------- tree build

    /**
     * Parse into a light tree. Nodes are arrays:
     *  element: ['t'=>1,'n'=>name,'s'=>tagStart,'te'=>tagEnd,'es'=>innerEnd,'e'=>outerEnd,'c'=>[children],'raw'=>rawTextStart?]
     *  text:    ['t'=>3,'s'=>start,'e'=>end]
     */
    public static function parse(string $html): array {
        self::$html = $html;
        $len = strlen($html);
        $root = ['t' => 1, 'n' => '#root', 's' => 0, 'te' => 0, 'es' => $len, 'e' => $len, 'c' => []];
        $stack = [&$root];
        $foreign = 0;
        $pos = 0;
        while ($pos < $len) {
            $lt = strpos($html, '<', $pos);
            if ($lt === false) { self::text($stack, $pos, $len); break; }
            if ($lt > $pos) { self::text($stack, $pos, $lt); }
            $next = $html[$lt + 1] ?? '';
            if ($next === '!') {
                if (substr_compare($html, '<!--', $lt, 4) === 0) {
                    $end = strpos($html, '-->', $lt + 4);
                    $pos = $end === false ? $len : $end + 3;
                } else {
                    $end = strpos($html, '>', $lt);
                    $pos = $end === false ? $len : $end + 1;
                }
                continue;
            }
            if ($next === '?') { $end = strpos($html, '>', $lt); $pos = $end === false ? $len : $end + 1; continue; }
            if ($next === '/') {
                if (preg_match('~\G</([a-zA-Z][^\s/>]*)[^>]*>~A', $html, $m, 0, $lt)) {
                    $name = strtolower($m[1]);
                    $pos = $lt + strlen($m[0]);
                    self::close($stack, $name, $lt, $pos, $foreign);
                } else {
                    $end = strpos($html, '>', $lt);
                    $pos = $end === false ? $len : $end + 1; // bogus comment
                }
                continue;
            }
            if (!preg_match('~\G<([a-zA-Z][^\s/>]*)((?:[^>"\']|"[^"]*"|\'[^\']*\')*)>~A', $html, $m, 0, $lt)) {
                self::text($stack, $lt, $lt + 1);
                $pos = $lt + 1;
                continue;
            }
            $name = strtolower($m[1]);
            $tagEnd = $lt + strlen($m[0]);
            $selfClosing = substr(rtrim($m[2]), -1) === '/';
            if ($foreign === 0) { self::implied($stack, $name, $lt); }
            $node = ['t' => 1, 'n' => $name, 's' => $lt, 'te' => $tagEnd, 'es' => $tagEnd, 'e' => $tagEnd, 'c' => []];
            $parent = &$stack[count($stack) - 1];
            $parent['c'][] = $node;
            $idx = count($parent['c']) - 1;
            $pos = $tagEnd;
            if ($foreign === 0 && isset(self::RAW[$name])) {
                $close = self::find_close($html, $name, $tagEnd);
                $parent['c'][$idx]['es'] = $close[0];
                $parent['c'][$idx]['e'] = $close[1];
                $parent['c'][$idx]['raw'] = 1;
                if ($close[0] > $tagEnd) { $parent['c'][$idx]['c'][] = ['t' => 3, 's' => $tagEnd, 'e' => $close[0]]; }
                $pos = $close[1];
                unset($parent);
                continue;
            }
            if (($foreign === 0 && isset(self::VOID[$name])) || ($foreign > 0 && $selfClosing)) { unset($parent); continue; }
            $stack[] = &$parent['c'][$idx];
            unset($parent);
            if ($name === 'svg' || $name === 'math') { $foreign++; }
        }
        // Close everything left open at EOF.
        for ($i = count($stack) - 1; $i >= 1; $i--) { $stack[$i]['es'] = $len; $stack[$i]['e'] = $len; }
        return $root;
    }

    private static function find_close(string $html, string $name, int $from): array {
        $len = strlen($html);
        $p = $from;
        while (($p = stripos($html, '</' . $name, $p)) !== false) {
            $c = $html[$p + 2 + strlen($name)] ?? '>';
            if ($c === '>' || $c === '/' || ctype_space($c)) {
                $gt = strpos($html, '>', $p);
                return [$p, $gt === false ? $len : $gt + 1];
            }
            $p += 2;
        }
        return [$len, $len];
    }

    private static function text(array &$stack, int $s, int $e): void {
        $top = &$stack[count($stack) - 1];
        $n = count($top['c']);
        if ($n && $top['c'][$n - 1]['t'] === 3 && $top['c'][$n - 1]['e'] === $s) { $top['c'][$n - 1]['e'] = $e; }
        else { $top['c'][] = ['t' => 3, 's' => $s, 'e' => $e]; }
    }

    private static function pop_to(array &$stack, int $i, int $at, int $outer): void {
        for ($j = count($stack) - 1; $j >= $i; $j--) {
            if ($j === $i) { $stack[$j]['es'] = $at; $stack[$j]['e'] = $outer; }
            else { $stack[$j]['es'] = $at; $stack[$j]['e'] = $at; }
            array_pop($stack);
        }
    }

    /** Index of the nearest open $names element, not crossing a scope boundary (or $stop). */
    private static function in_scope(array &$stack, array $names, array $stop = []): int {
        for ($i = count($stack) - 1; $i >= 1; $i--) {
            $n = $stack[$i]['n'];
            if (isset($names[$n])) { return $i; }
            if (isset(self::SCOPE[$n]) || isset($stop[$n])) { return -1; }
        }
        return -1;
    }

    private static function implied(array &$stack, string $name, int $at): void {
        if (isset(self::P_CLOSERS[$name])) {
            $i = self::in_scope($stack, ['p' => 1]);
            if ($i > 0) { self::pop_to($stack, $i, $at, $at); }
        }
        if ($name === 'li') {
            $i = self::in_scope($stack, ['li' => 1], ['ul' => 1, 'ol' => 1, 'menu' => 1]);
            if ($i > 0) { self::pop_to($stack, $i, $at, $at); }
        } elseif ($name === 'dt' || $name === 'dd') {
            $i = self::in_scope($stack, ['dt' => 1, 'dd' => 1], ['dl' => 1]);
            if ($i > 0) { self::pop_to($stack, $i, $at, $at); }
        } elseif (isset(self::HEADINGS[$name])) {
            $top = $stack[count($stack) - 1]['n'];
            if (isset(self::HEADINGS[$top])) { self::pop_to($stack, count($stack) - 1, $at, $at); }
        } elseif ($name === 'option' || $name === 'optgroup') {
            $top = $stack[count($stack) - 1]['n'];
            if ($top === 'option') { self::pop_to($stack, count($stack) - 1, $at, $at); }
            if ($name === 'optgroup' && $stack[count($stack) - 1]['n'] === 'optgroup') { self::pop_to($stack, count($stack) - 1, $at, $at); }
        } elseif ($name === 'a' || $name === 'button' || $name === 'form') {
            $i = self::in_scope($stack, [$name => 1]);
            if ($i > 0 && $name !== 'form') { self::pop_to($stack, $i, $at, $at); }
        } elseif ($name === 'td' || $name === 'th') {
            $i = self::in_scope($stack, ['td' => 1, 'th' => 1], ['tr' => 1, 'table' => 1]);
            if ($i > 0) { self::pop_to($stack, $i, $at, $at); }
        } elseif ($name === 'tr') {
            $i = self::in_scope($stack, ['tr' => 1, 'td' => 1, 'th' => 1], ['table' => 1, 'tbody' => 1, 'thead' => 1, 'tfoot' => 1]);
            if ($i > 0) { self::pop_to($stack, $i, $at, $at); }
        } elseif ($name === 'tbody' || $name === 'thead' || $name === 'tfoot') {
            $i = self::in_scope($stack, ['tbody' => 1, 'thead' => 1, 'tfoot' => 1, 'tr' => 1, 'td' => 1, 'th' => 1], ['table' => 1]);
            if ($i > 0) { self::pop_to($stack, $i, $at, $at); }
        }
    }

    private static function close(array &$stack, string $name, int $at, int $after, int &$foreign): void {
        if ($foreign === 0 && $name === 'br') { // </br> behaves like <br>
            $top = &$stack[count($stack) - 1];
            $top['c'][] = ['t' => 1, 'n' => 'br', 's' => $at, 'te' => $after, 'es' => $after, 'e' => $after, 'c' => []];
            return;
        }
        for ($i = count($stack) - 1; $i >= 1; $i--) {
            if ($stack[$i]['n'] === $name) {
                if ($name === 'svg' || $name === 'math') { $foreign = max(0, $foreign - 1); }
                self::pop_to($stack, $i, $at, $after);
                return;
            }
            if ($foreign === 0 && isset(self::SCOPE[$stack[$i]['n']]) && $name !== 'body' && $name !== 'html') { return; }
        }
    }

    // ------------------------------------------------------------ attributes

    public static function attrs(string $tag): array {
        $out = [];
        if (!preg_match('~^<[^\s/>]+~', $tag, $m)) { return $out; }
        $rest = substr($tag, strlen($m[0]));
        if (preg_match_all('~([^\s"\'<>/=]+)(?:\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|([^\s"\'=<>`]+)))?~', $rest, $mm, PREG_SET_ORDER)) {
            foreach ($mm as $a) {
                $k = strtolower($a[1]);
                if (isset($out[$k])) { continue; }
                $v = $a[2] ?? '';
                if (($a[3] ?? '') !== '') { $v = $a[3]; }
                if (($a[4] ?? '') !== '') { $v = $a[4]; }
                $out[$k] = self::decode($v);
            }
        }
        return $out;
    }

    /** Replace/add attribute values in a raw start tag, keeping everything else. */
    public static function set_attrs(string $tag, array $set): string {
        foreach ($set as $name => $value) {
            $q = '"' . htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8', false) . '"';
            $re = '~(\s' . preg_quote($name, '~') . ')(\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s"\'=<>`]+))?(?=[\s/>])~i';
            $count = 0;
            $tag = preg_replace_callback($re, static function ($m) use ($q) { return $m[1] . '=' . $q; }, $tag, 1, $count) ?? $tag;
            if (!$count) { $tag = preg_replace('~\s*(/?>)$~', ' ' . $name . '=' . $q . '$1', $tag, 1) ?? $tag; }
        }
        return $tag;
    }

    public static function skipped(array $el, ?array $attrs = null): bool {
        if (isset(self::SKIP[$el['n']]) && self::SKIP[$el['n']]) { return true; }
        $tag = substr(self::$html, $el['s'], $el['te'] - $el['s']);
        if (strpos($tag, '=') === false) { return false; }
        if (!preg_match('~\b(?:translate|class|data-zpl-skip|contenteditable|id|data-no-translation)\b~i', $tag)) { return false; }
        $a = $attrs ?? self::attrs($tag);
        if (isset($a['data-zpl-skip']) || isset($a['data-no-translation'])) { return true; }
        if (isset($a['translate']) && strtolower($a['translate']) === 'no') { return true; }
        if (isset($a['contenteditable']) && strtolower($a['contenteditable']) !== 'false') { return true; }
        if (isset($a['id']) && $a['id'] === 'wpadminbar') { return true; }
        if (isset($a['class']) && preg_match('~(?:^|\s)(?:notranslate|zpl-skip|zpl-switch)(?:\s|$)~', $a['class'])) { return true; }
        return false;
    }

    // ------------------------------------------------------------ segmenting

    /**
     * Walk the tree; $visit is called with segments:
     *  ['k'=>key, 'type'=>'text', 'node'=>textNode]  or  ['k'=>key,'type'=>'multi','root'=>el,'els'=>[n=>el]]
     * and $attr with ['el'=>el,'attrs'=>[name=>value]] for elements carrying translatable attributes.
     */
    public static function walk(array $node, callable $visit, callable $attr, bool $inBody = false): void {
        foreach ($node['c'] as $child) {
            if ($child['t'] === 3) {
                if ($inBody) { self::bare_text($child, $visit); }
                continue;
            }
            $name = $child['n'];
            if ($name === 'head') { self::walk_head($child, $visit, $attr); continue; }
            $body = $inBody || $name === 'body';
            if ($name === 'html' || $name === 'body' || $name === '#root') {
                $attr($child, null);
                self::walk($child, $visit, $attr, $body);
                continue;
            }
            if (!$body) { continue; }
            self::element($child, $visit, $attr);
        }
    }

    private static function walk_head(array $head, callable $visit, callable $attr): void {
        foreach ($head['c'] as $child) {
            if ($child['t'] !== 1) { continue; }
            if ($child['n'] === 'title' && !empty($child['c'])) {
                $raw = substr(self::$html, $child['c'][0]['s'], $child['c'][0]['e'] - $child['c'][0]['s']);
                $k = self::norm(self::decode($raw));
                if (self::human($k)) { $visit(['k' => $k, 'type' => 'title', 'node' => $child['c'][0]]); }
            } elseif ($child['n'] === 'meta' || $child['n'] === 'script') {
                $attr($child, null);
            }
        }
    }

    private static function bare_text(array $text, callable $visit): void {
        $raw = substr(self::$html, $text['s'], $text['e'] - $text['s']);
        if (trim($raw) === '') { return; }
        $k = self::norm(self::decode($raw));
        if (self::human($k)) { $visit(['k' => $k, 'type' => 'text', 'node' => $text]); }
    }

    private static function element(array $el, callable $visit, callable $attr): void {
        $tag = substr(self::$html, $el['s'], $el['te'] - $el['s']);
        $a = strpos($tag, '=') !== false ? self::attrs($tag) : [];
        if ($el['n'] === 'script') { $attr($el, $a); return; }
        // Textarea/iframe content is not page text, but their placeholder/title/aria-label are UI text.
        if ($el['n'] === 'textarea' || $el['n'] === 'iframe') { if (!self::skipped(['n' => '#'] + $el, $a)) { $attr($el, $a); } return; }
        if (self::skipped($el, $a)) { return; }
        $attr($el, $a);
        if (!empty($el['raw'])) { return; }
        if ($el['n'] === 'template') { self::walk_children($el, $visit, $attr); return; }
        if (self::inline_only($el) && self::direct_text($el)) {
            self::segment($el, $visit, $attr);
            return;
        }
        self::walk_children($el, $visit, $attr);
    }

    private static function walk_children(array $el, callable $visit, callable $attr): void {
        foreach ($el['c'] as $child) {
            if ($child['t'] === 3) { self::bare_text($child, $visit); }
            else { self::element($child, $visit, $attr); }
        }
    }

    private static function direct_text(array $el): bool {
        foreach ($el['c'] as $c) {
            if ($c['t'] === 3 && self::norm(self::decode(substr(self::$html, $c['s'], $c['e'] - $c['s']))) !== '') { return true; }
        }
        return false;
    }

    /** All descendants are text or INLINE elements (atoms are opaque). */
    private static function inline_only(array $el): bool {
        foreach ($el['c'] as $c) {
            if ($c['t'] === 3) { continue; }
            if (!isset(self::INLINE[$c['n']])) { return false; }
            if (isset(self::ATOM[$c['n']]) || self::skipped($c)) { continue; }
            if (!self::inline_only($c)) { return false; }
        }
        return true;
    }

    private static function segment(array $root, callable $visit, callable $attr): void {
        $texts = [];
        self::collect_texts($root, $texts, $attr);
        if (!$texts) { return; }
        if (count($texts) === 1) {
            $raw = substr(self::$html, $texts[0]['s'], $texts[0]['e'] - $texts[0]['s']);
            $k = self::norm(self::decode($raw));
            if (self::human($k)) { $visit(['k' => $k, 'type' => 'text', 'node' => $texts[0]]); }
            return;
        }
        $els = [];
        $counter = 0;
        $key = self::norm(self::key_of($root, $els, $counter));
        $plain = self::norm(preg_replace('~</?\d+/?>~', ' ', $key) ?? $key);
        if (!self::human(html_entity_decode($plain, ENT_QUOTES | ENT_HTML5, 'UTF-8'))) { return; }
        $visit(['k' => $key, 'type' => 'multi', 'root' => $root, 'els' => $els]);
    }

    private static function collect_texts(array $el, array &$texts, callable $attr): void {
        foreach ($el['c'] as $c) {
            if ($c['t'] === 3) {
                if (self::norm(self::decode(substr(self::$html, $c['s'], $c['e'] - $c['s']))) !== '') { $texts[] = $c; }
                continue;
            }
            $tag = substr(self::$html, $c['s'], $c['te'] - $c['s']);
            $a = strpos($tag, '=') !== false ? self::attrs($tag) : [];
            if (self::skipped($c, $a)) { if (($c['n'] === 'textarea' || $c['n'] === 'iframe') && !self::skipped(['n' => '#'] + $c, $a)) { $attr($c, $a); } continue; }
            $attr($c, $a);
            if (isset(self::ATOM[$c['n']])) { self::atom_attrs($c, $attr); continue; }
            self::collect_texts($c, $texts, $attr);
        }
    }

    /** Attributes inside atoms (e.g. <picture><img alt>) still get translated. */
    private static function atom_attrs(array $el, callable $attr): void {
        foreach ($el['c'] as $c) {
            if ($c['t'] !== 1 || isset(self::SKIP[$c['n']]) && self::SKIP[$c['n']]) { continue; }
            $attr($c, null);
            self::atom_attrs($c, $attr);
        }
    }

    /** Numbered-marker key of an inline subtree (pre-order numbering). */
    private static function key_of(array $el, array &$els, int &$counter): string {
        $out = '';
        foreach ($el['c'] as $c) {
            if ($c['t'] === 3) {
                $t = self::decode(substr(self::$html, $c['s'], $c['e'] - $c['s']));
                $out .= str_replace(['&', '<'], ['&amp;', '&lt;'], $t);
                continue;
            }
            $n = ++$counter;
            $els[$n] = $c;
            $opaque = isset(self::ATOM[$c['n']]) || self::skipped($c) || !self::has_text($c);
            if ($opaque) { $out .= '<' . $n . '/>'; continue; }
            $out .= '<' . $n . '>' . self::key_of($c, $els, $counter) . '</' . $n . '>';
        }
        return $out;
    }

    private static function has_text(array $el): bool {
        foreach ($el['c'] as $c) {
            if ($c['t'] === 3) {
                if (self::norm(self::decode(substr(self::$html, $c['s'], $c['e'] - $c['s']))) !== '') { return true; }
            } elseif (!isset(self::ATOM[$c['n']]) && !self::skipped($c) && self::has_text($c)) {
                return true;
            }
        }
        return false;
    }

    // ------------------------------------------------------------- markers

    /** Parse "<1>a</1><2/>b" into tokens. Returns null if malformed. */
    public static function tokens(string $s): ?array {
        $out = [];
        $stack = [];
        $pos = 0;
        $len = strlen($s);
        while ($pos < $len) {
            if (!preg_match('~<(/?)(\d+)(/?)>~', $s, $m, PREG_OFFSET_CAPTURE, $pos)) {
                $out[] = ['x', html_entity_decode(substr($s, $pos), ENT_QUOTES | ENT_HTML5, 'UTF-8')];
                break;
            }
            $at = $m[0][1];
            if ($at > $pos) { $out[] = ['x', html_entity_decode(substr($s, $pos, $at - $pos), ENT_QUOTES | ENT_HTML5, 'UTF-8')]; }
            $n = (int) $m[2][0];
            if ($m[1][0] === '/') {
                if (!$stack || array_pop($stack) !== $n) { return null; }
                $out[] = ['c', $n];
            } elseif ($m[3][0] === '/') {
                $out[] = ['a', $n];
            } else {
                $stack[] = $n;
                $out[] = ['o', $n];
            }
            $pos = $at + strlen($m[0][0]);
        }
        return $stack ? null : $out;
    }

    /** Same marker multiset in both strings. */
    public static function markers_match(string $a, string $b): bool {
        preg_match_all('~<(/?\d+/?)>~', $a, $x);
        preg_match_all('~<(/?\d+/?)>~', $b, $y);
        $x = $x[1]; $y = $y[1];
        sort($x); sort($y);
        return $x === $y && self::tokens($b) !== null;
    }
}
