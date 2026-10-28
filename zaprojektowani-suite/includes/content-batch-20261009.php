<?php
/**
 * Reviewed, static October 2026 article batch. Loading this file never creates posts.
 * The package is read only; its sole write entry point is a capability/nonce checked POST.
 */
if (!defined('ABSPATH')) { exit; }

final class ZP_Content_Batch_20261009 {
    public const BATCH = 'senuto-20261009-v1';
    public const PREFIX = '_zp_cb_20261009_';
    public const PAGE = 'zp-suite-content-20261009';
    private const ACTION = 'zp_cb_20261009_schedule';
    private const STATE = 'zp_cb_20261009_results';
    private const LOCK = 'zp_cb_20261009_lock';
    // Exact STYLE supplied by the user and independently matched to published Elementor sources.
    private const TEMPLATE_STYLE_SHA256 = '5cc264a2aec609b2185b443924653a1a3c2afe04af7b61b079f0b374b72a5d03';
    private const COPY_META = ['_elementor_edit_mode', '_elementor_template_type', '_elementor_version', '_elementor_pro_version', '_elementor_page_settings', '_wp_page_template'];

    public static function allowed(): bool {
        return current_user_can('manage_options') && current_user_can('edit_posts') && current_user_can('unfiltered_html');
    }

    public static function manifest_path(): string {
        return __DIR__ . '/content-batch-20261009-manifest.php';
    }

    /** Extract only the fixed JSON literal; do not execute even the packaged manifest PHP. */
    public static function read_manifest(): string {
        $file = self::manifest_path();
        if (!is_file($file) || !is_readable($file)) { throw new RuntimeException('Brak gotowego prywatnego manifestu 20 artykułów w pakiecie.'); }
        $wrapped = (string) file_get_contents($file);
        $pattern = "~\\A<\\?php\\R//[^\\r\\n]*\\Rif \\(!defined\\('ABSPATH'\\)\\) \\{ exit; \\}\\Rreturn <<<'ZP_REVIEWED_CONTENT_20261009_JSON'\\R(.*)\\RZP_REVIEWED_CONTENT_20261009_JSON;\\R?\\z~s";
        if (strlen($wrapped) > 5001000 || !preg_match($pattern, $wrapped, $match)) { throw new RuntimeException('Nieprawidłowy stały wrapper prywatnego manifestu.'); }
        return $match[1];
    }

    private static function exact_keys(array $value, array $keys, string $label): void {
        $actual = array_keys($value); sort($actual); sort($keys);
        if ($actual !== $keys) { throw new RuntimeException($label . ': nieprawidłowe pola.'); }
    }

    public static function normalize(string $text): string {
        // Adjacent block tags must not concatenate the end of a heading with the next FAQ.
        $text = html_entity_decode(wp_strip_all_tags((string) preg_replace('~<[^>]*>~s', ' ', $text)), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = remove_accents($text);
        $text = function_exists('mb_strtolower') ? mb_strtolower($text, 'UTF-8') : strtolower($text);
        return trim((string) preg_replace('/[^\p{L}\p{N}]+/u', ' ', $text));
    }

    private static function plain(array $value, string $key, int $minimum, int $maximum): string {
        if (!isset($value[$key]) || !is_string($value[$key])) { throw new RuntimeException($key . ': wymagany tekst.'); }
        $text = $value[$key];
        $length = function_exists('mb_strlen') ? mb_strlen($text, 'UTF-8') : strlen($text);
        if ($length < $minimum || $length > $maximum || trim($text) !== $text || preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', $text) || strpos($text, '<') !== false || strpos($text, '>') !== false || preg_match('/%[a-z_]+%/i', $text)) {
            throw new RuntimeException($key . ': nieprawidłowy lub niegotowy tekst.');
        }
        return $text;
    }

    /** Parse Warsaw wall time strictly, including DST offset, then convert to the real WP zone. */
    public static function dates(string $input, ?int $now = null, bool $require_future = true): array {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}[+-]\d{2}:\d{2}$/D', $input)) { throw new RuntimeException('publish_at: wymagany pełny ISO 8601 z offsetem.'); }
        $date = DateTimeImmutable::createFromFormat('!Y-m-d\TH:i:sP', $input);
        $errors = DateTimeImmutable::getLastErrors();
        if (!$date || ($errors && ($errors['warning_count'] || $errors['error_count']))) { throw new RuntimeException('publish_at: nieprawidłowa data.'); }
        $warsaw = $date->setTimezone(new DateTimeZone('Europe/Warsaw'));
        if ($warsaw->format('Y-m-d\TH:i:sP') !== $input) { throw new RuntimeException('publish_at: offset nie odpowiada strefie Europe/Warsaw.'); }
        if ($require_future && $date->getTimestamp() <= ($now ?? time()) + 300) { throw new RuntimeException('publish_at: termin musi być co najmniej 5 minut w przyszłości.'); }
        return ['timestamp' => $date->getTimestamp(), 'day' => $warsaw->format('Y-m-d'), 'warsaw' => $warsaw->format('Y-m-d H:i:s T'), 'local' => $date->setTimezone(wp_timezone())->format('Y-m-d H:i:s'), 'gmt' => $date->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d H:i:s')];
    }

    /** Every CSS rule is confined to the new article wrapper; nested media/support rules are allowed. */
    public static function scoped_css(string $css): string {
        if (strlen($css) > 100000 || preg_match('~</?style|<\?|@import|@font-face|@(?:-[a-z]+-)?keyframes|expression\s*\(|javascript\s*:|url\s*\(~i', $css)) { throw new RuntimeException('scoped_css: niedozwolony kod lub zasób.'); }
        $clean = preg_replace('~/\*.*?\*/~s', '', $css);
        if ($clean === null) { throw new RuntimeException('scoped_css: błąd składni.'); }
        $scan = static function (string $rules) use (&$scan): void {
            $length = strlen($rules); $start = 0; $level = 0; $quote = ''; $escaped = false; $open = null;
            for ($i = 0; $i < $length; $i++) {
                $char = $rules[$i];
                if ($escaped) { $escaped = false; continue; }
                if ($char === '\\') { $escaped = true; continue; }
                if ($quote !== '') { if ($char === $quote) { $quote = ''; } continue; }
                if ($char === '"' || $char === "'") { $quote = $char; continue; }
                if ($char === '{') { if ($level++ === 0) { $open = $i; } }
                if ($char === '}') {
                    if (--$level < 0 || $open === null) { throw new RuntimeException('scoped_css: niezrównoważone nawiasy.'); }
                    if ($level === 0) {
                        $selector = trim(substr($rules, $start, $open - $start));
                        $body = substr($rules, $open + 1, $i - $open - 1);
                        if (preg_match('/^@(media|supports|container)\s+/i', $selector)) { $scan($body); }
                        else {
                            if ($selector === '' || $selector[0] === '@' || strpos($body, '{') !== false || strpos($body, '}') !== false) { throw new RuntimeException('scoped_css: niedozwolona reguła.'); }
                            // Commas inside :is/:not are rejected rather than interpreting a global selector.
                            foreach (explode(',', $selector) as $part) { $part = trim($part); if (!preg_match('/^\.zpCb20261009(?=[\s.#:\[>]|$)/', $part) || preg_match('/^\.zpCb20261009[^\s>]*\s*[+~]/', $part)) { throw new RuntimeException('scoped_css: każdy selektor musi pozostać wewnątrz .zpCb20261009.'); } }
                        }
                        $start = $i + 1; $open = null;
                    }
                }
            }
            if ($level !== 0 || $quote !== '' || trim(substr($rules, $start)) !== '') { throw new RuntimeException('scoped_css: niekompletna reguła.'); }
        };
        $scan($clean);
        return $css;
    }

    public static function parse_manifest(string $raw): array {
        if ($raw === '' || strlen($raw) > 5000000) { throw new RuntimeException('Manifest jest pusty lub za duży.'); }
        $pack = json_decode($raw, true);
        if (!is_array($pack) || json_last_error() !== JSON_ERROR_NONE) { throw new RuntimeException('Manifest: nieprawidłowy JSON.'); }
        self::exact_keys($pack, ['schema_version', 'batch_id', 'timezone', 'research_note', 'scoped_css', 'articles'], 'Manifest');
        if ($pack['schema_version'] !== 1 || $pack['batch_id'] !== self::BATCH || $pack['timezone'] !== 'Europe/Warsaw' || !is_string($pack['scoped_css']) || !is_array($pack['articles']) || array_keys($pack['articles']) !== range(0, 19)) { throw new RuntimeException('Manifest: wymagany pakiet senuto-20261009-v1 i dokładnie 20 artykułów.'); }
        self::plain($pack, 'research_note', 10, 2000);
        if ($pack['scoped_css'] !== '') { throw new RuntimeException('scoped_css: nowy wygląd został odrzucony; używamy dokładnego dotychczasowego szablonu.'); }
        $seen = [];
        foreach ($pack['articles'] as $index => &$row) {
            if (!is_array($row)) { throw new RuntimeException('Artykuł ' . ($index + 1) . ': wymagany obiekt.'); }
            self::exact_keys($row, ['key', 'slug', 'title', 'seo_title', 'description', 'excerpt', 'focus_keywords', 'keyword_volume', 'category_slug', 'source_post_id', 'featured_attachment_id', 'featured_alt', 'inline_image_ids', 'publish_at', 'article_html', 'faq'], 'Artykuł ' . ($index + 1));
            foreach (['key', 'slug', 'category_slug'] as $field) { $value = self::plain($row, $field, 3, 180); if (!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/D', $value)) { throw new RuntimeException($field . ': wymagany slug.'); } }
            foreach (['title' => [10, 200], 'seo_title' => [10, 160], 'description' => [50, 320], 'excerpt' => [50, 800], 'featured_alt' => [5, 300], 'publish_at' => [25, 25]] as $field => $limits) { self::plain($row, $field, $limits[0], $limits[1]); }
            foreach (['source_post_id', 'featured_attachment_id'] as $field) { if (!is_int($row[$field]) || $row[$field] < 1) { throw new RuntimeException($field . ': wymagany dodatni attachment/post ID.'); } }
            if (!is_int($row['keyword_volume']) || $row['keyword_volume'] < 0 || $row['keyword_volume'] > 100000000) { throw new RuntimeException('keyword_volume: wymagana nieujemna liczba całkowita.'); }
            if (!is_array($row['inline_image_ids']) || count($row['inline_image_ids']) > 6 || ($row['inline_image_ids'] && array_keys($row['inline_image_ids']) !== range(0, count($row['inline_image_ids']) - 1))) { throw new RuntimeException('inline_image_ids: wymagana lista, maksymalnie 6.'); }
            foreach ($row['inline_image_ids'] as $id) { if (!is_int($id) || $id < 1) { throw new RuntimeException('inline_image_ids: dodatnie istniejące ID.'); } }
            if (count(array_unique($row['inline_image_ids'])) !== count($row['inline_image_ids'])) { throw new RuntimeException('inline_image_ids: powtórzony ID.'); }
            if (!is_array($row['focus_keywords']) || count($row['focus_keywords']) < 1 || count($row['focus_keywords']) > 5 || array_keys($row['focus_keywords']) !== range(0, count($row['focus_keywords']) - 1)) { throw new RuntimeException('focus_keywords: wymagane od 1 do 5 fraz.'); }
            $keywords = [];
            foreach ($row['focus_keywords'] as $keyword) { self::plain(['keyword' => $keyword], 'keyword', 2, 120); $normalized = self::normalize($keyword); if ($normalized === '' || strpos($keyword, ',') !== false || isset($keywords[$normalized])) { throw new RuntimeException('focus_keywords: pusta/powtórzona fraza lub przecinek.'); } $keywords[$normalized] = true; }
            if (!is_string($row['article_html']) || strlen($row['article_html']) > 200000 || strlen($row['article_html']) < 2000 || preg_match('~<\?|\{\{(?!FEATURED_(?:URL|ALT|WIDTH|HEIGHT)\}\}|IMG:[1-9]\d*\}\})~', $row['article_html'])) { throw new RuntimeException('article_html: wymagany gotowy statyczny artykuł.'); }
            if (!is_array($row['faq']) || count($row['faq']) > 12 || ($row['faq'] && array_keys($row['faq']) !== range(0, count($row['faq']) - 1))) { throw new RuntimeException('faq: wymagana lista do 12 pozycji.'); }
            $questions = [];
            foreach ($row['faq'] as $faq) { if (!is_array($faq)) { throw new RuntimeException('faq: wymagany obiekt.'); } self::exact_keys($faq, ['question', 'answer'], 'faq'); self::plain($faq, 'question', 5, 300); self::plain($faq, 'answer', 15, 1500); $q = self::normalize($faq['question']); if (isset($questions[$q])) { throw new RuntimeException('faq: powtórzone pytanie.'); } $questions[$q] = true; }
            $dates = self::dates($row['publish_at'], null, false);
            foreach (['key' => $row['key'], 'slug' => $row['slug'], 'title' => self::normalize($row['title']), 'focus' => self::normalize($row['focus_keywords'][0]), 'day' => $dates['day']] as $kind => $value) { if (isset($seen[$kind][$value])) { throw new RuntimeException('Manifest: kolizja ' . $kind . ' dla ' . $row['title']); } $seen[$kind][$value] = true; }
        }
        unset($row);
        return $pack;
    }

    private static function media(int $id): array {
        $post = get_post($id); $meta = wp_get_attachment_metadata($id); $url = wp_get_attachment_url($id);
        if (!$post || $post->post_type !== 'attachment' || $post->post_status === 'trash' || !in_array(get_post_mime_type($id), ['image/jpeg', 'image/png', 'image/webp', 'image/avif'], true) || !wp_attachment_is_image($id) || !is_array($meta) || empty($meta['width']) || empty($meta['height']) || !$url || !preg_match('~^https?://~i', $url)) { throw new RuntimeException('Nieprawidłowy obraz biblioteki #' . $id); }
        return ['id' => $id, 'url' => $url, 'width' => (int) $meta['width'], 'height' => (int) $meta['height']];
    }

    public static function image_dimensions(string $html, array $media): string {
        $by_url = []; foreach ($media as $image) { $by_url[$image['url']] = $image; }
        $parser = new WP_HTML_Tag_Processor($html);
        while ($parser->next_tag(['tag_name' => 'IMG'])) {
            $url = $parser->get_attribute('src');
            if (is_string($url) && isset($by_url[$url])) { $parser->set_attribute('width', $by_url[$url]['width']); $parser->set_attribute('height', $by_url[$url]['height']); }
        }
        return $parser->get_updated_html();
    }

    /** An optional full-block STYLE must be the exact existing user-approved template. */
    public static function article_without_style(string $html): string {
        preg_match_all('~<style\b[^>]*>.*?</style>~is', $html, $styles);
        if (!$styles[0]) { return $html; }
        if (count($styles[0]) !== 1 || !hash_equals(self::TEMPLATE_STYLE_SHA256, hash('sha256', str_replace("\r\n", "\n", $styles[0][0])))) { throw new RuntimeException('article_html: STYLE musi być dokładnym wklejonym szablonem.'); }
        return str_replace($styles[0][0], '', $html);
    }

    /** Validate actual tags rather than accepting marker text in a comment or a script. */
    public static function validate_html(string $html, string $title, array $allowed_images, array $faq): void {
        if (!class_exists('WP_HTML_Tag_Processor')) { throw new RuntimeException('Brak natywnego parsera HTML WordPress.'); }
        $parser = new WP_HTML_Tag_Processor($html); $articles = 0; $h1 = 0; $images = 0; $image_urls = [];
        $tags = ['ARTICLE', 'HEADER', 'FOOTER', 'MAIN', 'SECTION', 'ASIDE', 'NAV', 'DIV', 'SPAN', 'P', 'H1', 'H2', 'H3', 'H4', 'H5', 'H6', 'A', 'UL', 'OL', 'LI', 'STRONG', 'B', 'EM', 'I', 'SMALL', 'BR', 'HR', 'FIGURE', 'FIGCAPTION', 'IMG', 'BLOCKQUOTE', 'TABLE', 'THEAD', 'TBODY', 'TFOOT', 'TR', 'TH', 'TD', 'CAPTION', 'CODE', 'PRE', 'DETAILS', 'SUMMARY', 'TIME', 'MARK', 'SUP', 'SUB', 'DL', 'DT', 'DD', 'ABBR'];
        while ($parser->next_tag()) {
            $tag = $parser->get_tag();
            if (!in_array($tag, $tags, true)) { throw new RuntimeException('article_html: niedozwolony element ' . $tag); }
            if ($tag === 'ARTICLE') { $articles++; if (!$parser->has_class('zpArticleNew') || !$parser->has_class('zpArticlePremium')) { throw new RuntimeException('article_html: brak klas dokładnego istniejącego szablonu.'); } }
            if ($tag === 'H1') { $h1++; }
            foreach ($parser->get_attribute_names_with_prefix('') ?? [] as $name) {
                $value = $parser->get_attribute($name);
                if (stripos($name, 'on') === 0 || in_array(strtolower($name), ['srcdoc', 'hidden'], true) || (strtolower($name) === 'aria-hidden' && $value === 'true') || (is_string($value) && preg_match('/(?:javascript|vbscript|data)\s*:/i', html_entity_decode($value, ENT_QUOTES, 'UTF-8')))) { throw new RuntimeException('article_html: niedozwolony atrybut ' . $name); }
                if (strtolower($name) === 'style' && is_string($value)) {
                    if (preg_match('/expression\s*\(|@import|position\s*:\s*fixed/i', $value)) { throw new RuntimeException('article_html: niedozwolony inline CSS.'); }
                    preg_match_all('~url\(\s*[\'\"]?([^\)\'\"]+)[\'\"]?\s*\)~i', $value, $urls);
                    foreach ($urls[1] as $url) { if (!in_array(trim(html_entity_decode($url, ENT_QUOTES, 'UTF-8')), $allowed_images, true)) { throw new RuntimeException('article_html: background obraz spoza zadeklarowanej biblioteki.'); } }
                }
            }
            if ($tag === 'IMG') {
                $images++; $src = $parser->get_attribute('src');
                if (!is_string($src) || !in_array($src, $allowed_images, true) || $parser->get_attribute('srcset') !== null || (int) $parser->get_attribute('width') < 1 || (int) $parser->get_attribute('height') < 1 || !is_string($parser->get_attribute('alt')) || trim($parser->get_attribute('alt')) === '') { throw new RuntimeException('article_html: obraz spoza zadeklarowanej biblioteki lub brak wymiarów/alt.'); }
                $image_urls[$src] = true;
            }
        }
        $h1_text = preg_match('~<h1\b[^>]*>(.*?)</h1\s*>~is', $html, $match) ? trim((string) preg_replace('/\s+/u', ' ', html_entity_decode(wp_strip_all_tags($match[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8'))) : '';
        if ($articles !== 1 || $h1 !== 1 || $images < 2 || $images > 3 || count($image_urls) < 2 || $h1_text !== $title) { throw new RuntimeException('article_html: wymagany jeden article, jeden H1 dokładnie zgodny z tytułem i 2–3 rzeczywiste wewnętrzne zdjęcia.'); }
        $text = self::normalize($html);
        $words = count(explode(' ', $text));
        if ($words < 2000 || $words > 3000) { throw new RuntimeException('article_html: wymagane 2000–3000 słów pełnej treści (jest ' . $words . ').'); }
        foreach ($faq as $item) { if (strpos($text, self::normalize($item['question'])) === false || strpos($text, self::normalize($item['answer'])) === false) { throw new RuntimeException('faq: pytanie/odpowiedź nie występuje w treści artykułu.'); } }
    }

    private static function source(int $id, string $article, string $css, bool $owned_future = false): array {
        $post = get_post($id);
        $valid_status = $post && ($owned_future ? ($post->post_status === 'future' && get_post_meta($id, self::PREFIX . 'batch', true) === self::BATCH && self::unchanged($id)) : $post->post_status === 'publish');
        if (!$post || $post->post_type !== 'post' || !$valid_status || $post->post_password !== '' || get_post_meta($id, '_elementor_edit_mode', true) !== 'builder' || !function_exists('zp_seo_articles_elementor')) { throw new RuntimeException('Nieprawidłowy szablon Elementora #' . $id); }
        $raw = get_post_meta($id, '_elementor_data', true); $tree = is_array($raw) ? $raw : json_decode((string) $raw, true);
        if (!is_array($tree) || !$tree) { throw new RuntimeException('Brak danych Elementora #' . $id); }
        $hits = []; $false_markers = 0;
        $walk = static function (array $elements, array $path = []) use (&$walk, &$hits, &$false_markers): void {
            foreach ($elements as $i => $element) {
                if (!is_array($element)) { continue; }
                $current = array_merge($path, [$i]);
                if (($element['elType'] ?? '') === 'widget' && ($element['widgetType'] ?? '') === 'html') {
                    $html = (string) ($element['settings']['html'] ?? ''); $count = 0; $parser = new WP_HTML_Tag_Processor($html);
                    while ($parser->next_tag(['tag_name' => 'ARTICLE'])) { if ($parser->has_class('zpArticleNew')) { $count++; } }
                    if ($count > 0) { $hits[] = ['path' => $current, 'html' => $html, 'count' => $count]; }
                    elseif (strpos($html, 'zpArticleNew') !== false && stripos($html, '<article') !== false) { $false_markers++; }
                    if (preg_match('~<script\b[^>]*application/ld\+json~i', $html) && $count === 0) { throw new RuntimeException('Źródło ma dodatkowy inline JSON-LD poza widgetem artykułu.'); }
                }
                if (!empty($element['elements']) && is_array($element['elements'])) { $walk($element['elements'], array_merge($current, ['elements'])); }
            }
        };
        $walk($tree);
        if (count($hits) !== 1 || $hits[0]['count'] !== 1 || $false_markers) { throw new RuntimeException('Źródło #' . $id . ': wymagany dokładnie jeden jednoznaczny widget artykułu.'); }
        $old = $hits[0]['html']; preg_match_all('~<style\b[^>]*>.*?</style>~is', $old, $styles); preg_match_all('~<script\b(?![^>]*application/ld\+json)[^>]*>.*?</script>~is', $old, $scripts);
        if (count($styles[0]) !== 1 || !hash_equals(self::TEMPLATE_STYLE_SHA256, hash('sha256', str_replace("\r\n", "\n", $styles[0][0])))) { throw new RuntimeException('Źródło #' . $id . ': CSS różni się od dokładnego szablonu wklejonego przez użytkownika.'); }
        $supplied = ($css !== '' ? '<style id="zp-cb-20261009">' . $css . '</style>' . "\n" : '') . $article; $notes = [];
        $json = zp_seo_articles_elementor($id, $supplied, $notes); $changed = $json ? json_decode($json, true) : null;
        if (!is_array($changed)) { throw new RuntimeException('Nie udało się sklonować Elementora #' . $id); }
        $ref = &$changed;
        foreach ($hits[0]['path'] as $part) { if (!array_key_exists($part, $ref)) { throw new RuntimeException('Zmieniła się struktura źródła.'); } $ref = &$ref[$part]; }
        $expected = implode("\n", $styles[0]) . "\n" . $supplied . ($scripts[0] ? "\n" . implode("\n", $scripts[0]) : '');
        if (($ref['settings']['html'] ?? null) !== $expected) { throw new RuntimeException('Nie zachowano CSS/skryptów szablonu.'); }
        $ref['settings']['html'] = $old; unset($ref);
        if ($changed !== $tree) { throw new RuntimeException('Klon zmienił inne elementy szablonu.'); }
        $meta = [];
        foreach (self::COPY_META as $name) { if (metadata_exists('post', $id, $name)) { $meta[$name] = get_post_meta($id, $name, true); } }
        return ['json' => $json, 'meta' => $meta, 'source_hash' => hash('sha256', wp_json_encode([$tree, $meta, $post->post_status, $post->post_author])) , 'author' => (int) $post->post_author];
    }

    /** Read all native posts/pages, including drafts, private entries, scheduled posts and trash. */
    public static function inventory(): array {
        global $wpdb;
        $rows = $wpdb->get_results("SELECT ID, post_title, post_name, post_type, post_status, post_date_gmt, post_date FROM {$wpdb->posts} WHERE post_type IN ('post','page')", ARRAY_A);
        if (!is_array($rows)) { throw new RuntimeException('Nie udało się odczytać istniejących wpisów.'); }
        if ($rows) { update_meta_cache('post', array_column($rows, 'ID')); }
        foreach ($rows as &$row) {
            $id = (int) $row['ID'];
            $row['focus'] = self::normalize(explode(',', (string) get_post_meta($id, 'rank_math_focus_keyword', true))[0]);
            $row['batch'] = get_post_meta($id, self::PREFIX . 'batch', true);
            $row['key'] = get_post_meta($id, self::PREFIX . 'key', true);
            $row['record_hash'] = get_post_meta($id, self::PREFIX . 'record_hash', true);
            $row['day'] = '';
            if ($row['post_status'] === 'future') {
                $gmt = $row['post_date_gmt'];
                $date = $gmt !== '0000-00-00 00:00:00' ? new DateTimeImmutable($gmt, new DateTimeZone('UTC')) : new DateTimeImmutable($row['post_date'], wp_timezone());
                $row['day'] = $date->setTimezone(new DateTimeZone('Europe/Warsaw'))->format('Y-m-d');
            }
        }
        unset($row); return $rows;
    }

    public static function collisions(array $record, array $inventory, array $ignore = []): array {
        $errors = []; $hash = hash('sha256', wp_json_encode($record)); $dates = self::dates($record['publish_at'], null, false);
        foreach ($inventory as $existing) {
            $id = (int) $existing['ID']; if (in_array($id, $ignore, true)) { continue; }
            if (($existing['batch'] ?? '') === self::BATCH && ($existing['key'] ?? '') === $record['key'] && ($existing['record_hash'] ?? '') === $hash) { continue; }
            $slug = preg_replace('/__trashed(?:-\d+)?$/', '', (string) $existing['post_name']);
            $reason = [];
            if ($slug === $record['slug']) { $reason[] = 'slug'; }
            if (self::normalize((string) $existing['post_title']) === self::normalize($record['title'])) { $reason[] = 'tytuł'; }
            if (($existing['focus'] ?? '') !== '' && $existing['focus'] === self::normalize($record['focus_keywords'][0])) { $reason[] = 'główna fraza'; }
            if (($existing['day'] ?? '') === $dates['day']) { $reason[] = 'dzień publikacji'; }
            if ($reason) { $errors[] = implode(', ', $reason) . ' koliduje z #' . $id . ' (' . $existing['post_status'] . ').'; }
        }
        return $errors;
    }

    public static function preflight(?string $raw = null): array {
        $out = ['errors' => [], 'rows' => [], 'sha' => '', 'review_hash' => '', 'pack' => null, 'complete' => false];
        if (!self::allowed()) { $out['errors'][] = 'Wymagane uprawnienia administratora, edit_posts i unfiltered_html.'; return $out; }
        try {
            if (!defined('RANK_MATH_VERSION') && !class_exists('RankMath')) { throw new RuntimeException('Rank Math musi być aktywny przed zaplanowaniem.'); }
            if ($raw === null) { $raw = self::read_manifest(); }
            $pack = self::parse_manifest($raw); $out['pack'] = $pack; $out['sha'] = hash('sha256', $raw); $inventory = self::inventory(); $existing_count = 0; $fingerprints = [];
            foreach ($pack['articles'] as $record) {
                $row = ['record' => $record, 'errors' => [], 'existing_id' => 0];
                try {
                    $dates = self::dates($record['publish_at'], null, false); $term = get_term_by('slug', $record['category_slug'], 'category');
                    if (!$term || is_wp_error($term)) { throw new RuntimeException('Brak istniejącej kategorii ' . $record['category_slug']); }
                    $media = []; foreach (array_unique(array_merge([$record['featured_attachment_id']], $record['inline_image_ids'])) as $id) { $media[$id] = self::media($id); }
                    $featured = $media[$record['featured_attachment_id']];
                    $html = strtr(self::article_without_style($record['article_html']), ['{{FEATURED_URL}}' => esc_url($featured['url']), '{{FEATURED_ALT}}' => esc_attr($record['featured_alt']), '{{FEATURED_WIDTH}}' => (string) $featured['width'], '{{FEATURED_HEIGHT}}' => (string) $featured['height']]);
                    $html = preg_replace_callback('/\{\{IMG:([1-9]\d*)\}\}/', static function ($match) use ($record, $media) { $id = (int) $match[1]; if (!in_array($id, $record['inline_image_ids'], true)) { throw new RuntimeException('HTML używa niezadeklarowanego IMG ID #' . $id); } return esc_url($media[$id]['url']); }, $html);
                    if (strpos($html, '{{') !== false) { throw new RuntimeException('HTML zawiera nierozwiązany token.'); }
                    $html = self::image_dimensions($html, $media);
                    self::validate_html($html, $record['title'], array_column($media, 'url'), $record['faq']);
                    $source = self::source($record['source_post_id'], $html, $pack['scoped_css']);
                    $record_hash = hash('sha256', wp_json_encode($record)); $matches = [];
                    foreach ($inventory as $existing) { if (($existing['batch'] ?? '') === self::BATCH && ($existing['key'] ?? '') === $record['key']) { $matches[] = $existing; } }
                    if ($matches) {
                        if (count($matches) !== 1 || $matches[0]['record_hash'] !== $record_hash || !in_array($matches[0]['post_status'], ['future', 'publish'], true) || !self::unchanged((int) $matches[0]['ID'])) { throw new RuntimeException('Ten rekord już istnieje lub był zmieniony; użyj natywnego edytora #' . $matches[0]['ID'] . '.'); }
                        $row['existing_id'] = (int) $matches[0]['ID']; $existing_count++;
                    } else { self::dates($record['publish_at']); }
                    $collision_errors = self::collisions($record, $inventory); if ($collision_errors) { throw new RuntimeException(implode(' ', $collision_errors)); }
                    $row += ['dates' => $dates, 'category_id' => (int) $term->term_id, 'media' => $media, 'html' => $html, 'source' => $source, 'record_hash' => $record_hash];
                    $fingerprints[] = [$record_hash, $dates, (int) $term->term_id, $media, $source['source_hash']];
                } catch (Throwable $error) { $row['errors'][] = $error->getMessage(); $out['errors'][] = $record['title'] . ': ' . $error->getMessage(); }
                $out['rows'][] = $row;
            }
            if ($existing_count && $existing_count !== 20) { $out['errors'][] = 'Część pakietu już istnieje. Nie tworzymy pozostałych automatycznie; sprawdź zapisane wpisy.'; }
            $out['complete'] = $existing_count === 20 && !$out['errors'];
            $out['review_hash'] = hash('sha256', wp_json_encode([$out['sha'], $fingerprints]));
        } catch (Throwable $error) { $out['errors'][] = $error->getMessage(); }
        return $out;
    }

    private static function metadata(array $row): array {
        $record = $row['record'];
        // Rank Math's native fields are editable in its normal post metabox.
        $meta = array_merge($row['source']['meta'], [
            '_elementor_data' => $row['source']['json'],
            'rank_math_title' => $record['seo_title'], 'rank_math_description' => $record['description'],
            'rank_math_focus_keyword' => implode(', ', $record['focus_keywords']), 'rank_math_robots' => ['index'],
            'rank_math_primary_category' => $row['category_id'],
            'rank_math_rich_snippet' => 'article', 'rank_math_snippet_article_type' => 'BlogPosting',
            'rank_math_schema_Article' => ['@type' => 'Article', 'articleType' => 'BlogPosting', 'headline' => '%seo_title%', 'description' => '%seo_description%', 'image' => '%post_thumbnail%', 'datePublished' => '%date(Y-m-dTH:i:sP)%', 'dateModified' => '%modified(Y-m-dTH:i:sP)%', 'author' => ['@type' => 'Person', 'name' => '%name%'], 'metadata' => ['title' => 'Article', 'type' => 'template', 'isPrimary' => true, 'shortcode' => 'zp-cb-20261009']],
            self::PREFIX . 'batch' => self::BATCH, self::PREFIX . 'key' => $record['key'], self::PREFIX . 'record_hash' => $row['record_hash'],
            self::PREFIX . 'research_volume' => $record['keyword_volume'], self::PREFIX . 'source_id' => $record['source_post_id'],
            self::PREFIX . 'featured_alt' => $record['featured_alt'], self::PREFIX . 'faq' => $record['faq'],
        ]);
        return $meta;
    }

    /** Relevant content fingerprint deliberately excludes status/modified time and generated Elementor CSS. */
    public static function fingerprint(int $id, bool $with_dates = true): string {
        $post = get_post($id); if (!$post) { return ''; }
        $meta = [];
        foreach (array_merge(self::COPY_META, ['_elementor_data', '_thumbnail_id', 'rank_math_title', 'rank_math_description', 'rank_math_focus_keyword', 'rank_math_primary_category', 'rank_math_robots', 'rank_math_rich_snippet', 'rank_math_snippet_article_type', 'rank_math_schema_Article', self::PREFIX . 'batch', self::PREFIX . 'key', self::PREFIX . 'record_hash', self::PREFIX . 'featured_alt', self::PREFIX . 'faq']) as $key) { $meta[$key] = get_post_meta($id, $key, true); }
        $categories = wp_get_post_categories($id); sort($categories);
        return hash('sha256', wp_json_encode([$post->post_title, $post->post_name, $post->post_content, $post->post_excerpt, $with_dates ? $post->post_date : null, $with_dates ? $post->post_date_gmt : null, $post->post_author, $post->post_password, $categories, $meta]));
    }

    public static function unchanged(int $id): bool {
        // Native publishing can intentionally move the date earlier. Content/meta remain protected.
        $published = get_post_status($id) === 'publish';
        $stored = get_post_meta($id, self::PREFIX . ($published ? 'content_ready_hash' : 'ready_hash'), true);
        return is_string($stored) && $stored !== '' && hash_equals($stored, self::fingerprint($id, !$published));
    }

    private static function state(array $created, array $errors, string $status, string $sha): void {
        update_option(self::STATE, ['batch' => self::BATCH, 'status' => $status, 'created_ids' => $created, 'errors' => $errors, 'manifest_sha256' => $sha, 'at_utc' => gmdate('c')], false);
    }

    /** Caller must supply the exact two hashes shown by the review page; no user HTML is accepted. */
    public static function schedule(string $manifest_hash, string $review_hash): array {
        if (!self::allowed()) { throw new RuntimeException('Brak wymaganych uprawnień.'); }
        $review = self::preflight();
        if ($review['errors']) { throw new RuntimeException(implode("\n", $review['errors'])); }
        if (!preg_match('/^[a-f0-9]{64}$/D', $manifest_hash) || !preg_match('/^[a-f0-9]{64}$/D', $review_hash) || !hash_equals($review['sha'], $manifest_hash) || !hash_equals($review['review_hash'], $review_hash)) { throw new RuntimeException('Pakiet lub źródłowy szablon zmienił się od podglądu. Odśwież przegląd.'); }
        if ($review['complete']) { return array_column($review['rows'], 'existing_id'); }
        $lock = ['token' => wp_generate_uuid4(), 'at' => time()];
        if (!add_option(self::LOCK, $lock, '', false)) { throw new RuntimeException('Inne uruchomienie pakietu trwa lub wymaga sprawdzenia.'); }
        $created = []; $errors = [];
        try {
            // Recheck collisions inside the lock before the first write.
            $inventory = self::inventory(); foreach ($review['rows'] as $row) { $check = self::collisions($row['record'], $inventory); if ($check) { throw new RuntimeException(implode(' ', $check)); } }
            foreach ($review['rows'] as $row) {
                $record = $row['record']; $dates = self::dates($record['publish_at']);
                $id = wp_insert_post(wp_slash(['post_type' => 'post', 'post_status' => 'draft', 'post_title' => $record['title'], 'post_name' => $record['slug'], 'post_content' => '', 'post_excerpt' => '', 'post_author' => $row['source']['author'], 'post_date' => $dates['local'], 'post_date_gmt' => $dates['gmt'], 'post_category' => [$row['category_id']]]), true);
                if (is_wp_error($id) || !$id) { throw new RuntimeException('Tworzenie szkicu: ' . (is_wp_error($id) ? $id->get_error_message() : 'brak ID')); }
                $id = (int) $id; $created[] = $id;
                // Ownership is stored immediately so an interrupted run is discoverable and cannot duplicate.
                foreach ([self::PREFIX . 'batch' => self::BATCH, self::PREFIX . 'key' => $record['key'], self::PREFIX . 'record_hash' => $row['record_hash']] as $key => $value) { update_post_meta($id, $key, $value); }
                update_post_meta($id, self::PREFIX . 'initial_draft', ['post_content' => '', 'post_excerpt' => '', 'status' => 'draft']);
                self::state($created, [], 'preparing', $review['sha']);
                $updated = wp_update_post(wp_slash(['ID' => $id, 'post_content' => $row['html'], 'post_excerpt' => $record['excerpt'], 'post_status' => 'draft']), true);
                if (is_wp_error($updated) || !$updated) { throw new RuntimeException('Nie zapisano treści szkicu #' . $id); }
                foreach (self::metadata($row) as $key => $value) { update_post_meta($id, $key, wp_slash($value)); }
                set_post_thumbnail($id, $record['featured_attachment_id']);
                foreach (['_elementor_css', '_elementor_element_cache', '_elementor_page_assets'] as $cache) { delete_post_meta($id, $cache); }
                clean_post_cache($id); $saved = get_post($id);
                if (!$saved || $saved->post_status !== 'draft' || $saved->post_name !== $record['slug'] || $saved->post_title !== $record['title'] || $saved->post_content !== $row['html'] || $saved->post_excerpt !== $record['excerpt'] || $saved->post_date !== $dates['local'] || $saved->post_date_gmt !== $dates['gmt'] || (int) get_post_thumbnail_id($id) !== $record['featured_attachment_id'] || wp_get_post_categories($id) !== [$row['category_id']]) { throw new RuntimeException('Weryfikacja treści/terminu/zdjęcia szkicu #' . $id . ' nie powiodła się.'); }
                foreach (self::metadata($row) as $key => $value) { $expected = is_scalar($value) ? (string) $value : $value; if (get_post_meta($id, $key, true) !== $expected) { throw new RuntimeException('Weryfikacja metadanych ' . $key . ' #' . $id . ' nie powiodła się.'); } }
                update_post_meta($id, self::PREFIX . 'ready_hash', self::fingerprint($id));
                update_post_meta($id, self::PREFIX . 'content_ready_hash', self::fingerprint($id, false));
                if (!self::unchanged($id)) { throw new RuntimeException('Weryfikacja gotowego szkicu #' . $id . ' nie powiodła się.'); }
            }
            $inventory = self::inventory();
            foreach ($review['rows'] as $row) { self::dates($row['record']['publish_at']); $check = self::collisions($row['record'], $inventory, $created); if ($check) { throw new RuntimeException(implode(' ', $check)); } }
            foreach ($created as $index => $id) {
                $dates = self::dates($review['rows'][$index]['record']['publish_at']);
                if (!self::unchanged($id)) { throw new RuntimeException('Szkic #' . $id . ' zmieniono podczas przygotowania.'); }
                $result = wp_update_post(wp_slash(['ID' => $id, 'post_status' => 'future', 'post_date' => $dates['local'], 'post_date_gmt' => $dates['gmt'], 'edit_date' => true]), true);
                clean_post_cache($id);
                if (is_wp_error($result) || get_post_status($id) !== 'future' || !self::unchanged($id) || (int) wp_next_scheduled('publish_future_post', [$id]) !== $dates['timestamp']) { throw new RuntimeException('Weryfikacja harmonogramu #' . $id . ' nie powiodła się.'); }
            }
            self::state($created, [], 'scheduled', $review['sha']); return $created;
        } catch (Throwable $error) {
            $errors[] = $error->getMessage();
            // Failure recovery changes only unchanged future posts created by this invocation.
            foreach ($created as $id) { if (get_post_meta($id, self::PREFIX . 'batch', true) === self::BATCH && get_post_status($id) === 'future' && self::unchanged($id)) { $result = wp_update_post(['ID' => $id, 'post_status' => 'draft'], true); if (is_wp_error($result) || get_post_status($id) !== 'draft') { $errors[] = 'Sprawdź ręcznie utworzony wpis #' . $id; } } }
            self::state($created, $errors, 'drafts_need_review', $review['sha']); throw new RuntimeException(implode("\n", $errors));
        } finally { if (get_option(self::LOCK) === $lock) { delete_option(self::LOCK); } }
    }

    public static function handle(): void {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST' || !self::allowed()) { wp_die('Brak uprawnień lub nieprawidłowa metoda.', '', ['response' => 403]); }
        $nonce = isset($_POST['_wpnonce']) && is_string($_POST['_wpnonce']) ? wp_unslash($_POST['_wpnonce']) : '';
        if (!wp_verify_nonce($nonce, self::ACTION)) { wp_die('Nieprawidłowy nonce.', '', ['response' => 403]); }
        try {
            $sha = isset($_POST['manifest_sha256']) && is_string($_POST['manifest_sha256']) ? wp_unslash($_POST['manifest_sha256']) : '';
            $review = isset($_POST['review_hash']) && is_string($_POST['review_hash']) ? wp_unslash($_POST['review_hash']) : '';
            self::schedule($sha, $review);
        } catch (Throwable $error) { set_transient('zp_cb_20261009_error_' . get_current_user_id(), $error->getMessage(), 600); }
        wp_safe_redirect(admin_url('admin.php?page=' . self::PAGE)); exit;
    }

    /** Stable rendering structure/text with only image attributes and caption text masked. */
    public static function photo_signature(string $html, bool $fix_legacy_woo_link = false): string {
        if ($fix_legacy_woo_link) { $html = str_replace('href="https://woocommerce.com/document/adding-and-managing-products/"', 'href="https://woocommerce.com/document/managing-products/"', $html); }
        $parser = new WP_HTML_Tag_Processor($html); $tokens = []; $caption = 0;
        while ($parser->next_token()) {
            $type = $parser->get_token_type();
            if ($type === '#tag') {
                $tag = $parser->get_tag(); $closing = $parser->is_tag_closer();
                if ($tag === 'FIGCAPTION') { $caption += $closing ? -1 : 1; }
                $attrs = [];
                foreach ($parser->get_attribute_names_with_prefix('') ?? [] as $name) {
                    if ($tag === 'IMG' && in_array(strtolower($name), ['src', 'alt', 'width', 'height'], true)) { continue; }
                    $value = $parser->get_attribute($name);
                    if ($tag === 'IMG' && strtolower($name) === 'style' && is_string($value)) { $value = trim((string) preg_replace('/(?:^|;)\s*object-fit\s*:\s*(?:contain|cover)\s*(?=;|$)/i', '', $value), " ;\t\r\n"); if ($value === '') { continue; } }
                    $attrs[strtolower($name)] = $value;
                }
                ksort($attrs); $tokens[] = [$tag, $closing, $attrs];
            } elseif (!$caption) {
                $text = $parser->get_modifiable_text();
                $text = trim((string) preg_replace('/\s+/u', ' ', $text));
                if ($text !== '') { $tokens[] = [$type, $text]; }
            }
        }
        if ($caption !== 0) { throw new RuntimeException('Nieprawidłowa struktura podpisu zdjęcia.'); }
        return hash('sha256', wp_json_encode($tokens));
    }

    private static function photo_unchanged_fields(int $id): string {
        $post = get_post($id); $meta = [];
        foreach (array_merge(self::COPY_META, ['rank_math_title', 'rank_math_description', 'rank_math_focus_keyword', 'rank_math_primary_category', 'rank_math_robots', 'rank_math_rich_snippet', 'rank_math_snippet_article_type', 'rank_math_schema_Article', self::PREFIX . 'batch', self::PREFIX . 'key', self::PREFIX . 'source_id', self::PREFIX . 'research_volume', self::PREFIX . 'faq']) as $key) { $meta[$key] = get_post_meta($id, $key, true); }
        return hash('sha256', wp_json_encode([$post->post_title, $post->post_name, $post->post_excerpt, $post->post_author, $post->post_password, $post->post_status, $post->post_date, $post->post_date_gmt, wp_get_post_categories($id), wp_next_scheduled('publish_future_post', [$id]), $meta]));
    }

    public static function photo_preflight(): array {
        $out = ['errors' => [], 'rows' => [], 'review_hash' => '', 'complete' => false];
        try {
            if (!self::allowed()) { throw new RuntimeException('Brak wymaganych uprawnień.'); }
            $file = __DIR__ . '/content-batch-20261009-photos.php';
            if (!is_file($file)) { throw new RuntimeException('Brak gotowej prywatnej mapy zmian zdjęć.'); }
            $wrapped = (string) file_get_contents($file);
            $pattern = "~\\A<\\?php\\R//[^\\r\\n]*\\Rif \\(!defined\\('ABSPATH'\\)\\) \\{ exit; \\}\\Rreturn <<<'ZP_REVIEWED_PHOTOS_20261009_JSON'\\R(.*)\\RZP_REVIEWED_PHOTOS_20261009_JSON;\\R?\\z~s";
            if (strlen($wrapped) > 30000 || !preg_match($pattern, $wrapped, $match)) { throw new RuntimeException('Nieprawidłowy prywatny wrapper zdjęć.'); }
            $map = json_decode($match[1], true); if (!is_array($map)) { throw new RuntimeException('Nieprawidłowa mapa zdjęć.'); }
            self::exact_keys($map, ['schema_version', 'refresh_id', 'original_batch_sha256', 'entries'], 'Mapa zdjęć');
            $state = get_option(self::STATE, []);
            if ($map['schema_version'] !== 1 || $map['refresh_id'] !== 'media-20261009-v1' || !is_array($map['entries']) || count($map['entries']) !== 19 || ($state['batch'] ?? '') !== self::BATCH || ($state['manifest_sha256'] ?? '') !== $map['original_batch_sha256']) { throw new RuntimeException('Mapa musi dotyczyć dokładnie 19 przyszłych wpisów oryginalnego importu.'); }
            $raw = self::read_manifest(); $pack = self::parse_manifest($raw); $records = []; foreach ($pack['articles'] as $record) { $records[$record['key']] = $record; }
            $seen = []; $proof = []; $done = 0;
            foreach ($map['entries'] as $entry) {
                self::exact_keys($entry, ['post_id', 'key', 'original_record_sha256', 'new_record_sha256'], 'Zmiana zdjęć');
                $id = $entry['post_id'];
                if (!is_int($id) || !in_array($id, range(5706, 5742, 2), true) || isset($seen[$id]) || !in_array($id, $state['created_ids'] ?? [], true) || !isset($records[$entry['key']]) || $entry['key'] === 'pozycjonowanie-w-ai' || !preg_match('/^[a-f0-9]{64}$/D', (string) $entry['original_record_sha256']) || !preg_match('/^[a-f0-9]{64}$/D', (string) $entry['new_record_sha256'])) { throw new RuntimeException('Nieprawidłowy lub niedozwolony docelowy wpis zdjęć.'); }
                $seen[$id] = true; $record = $records[$entry['key']]; $hash = hash('sha256', wp_json_encode($record)); $post = get_post($id);
                if (!$post || $post->post_type !== 'post' || $post->post_status !== 'future' || get_post_meta($id, self::PREFIX . 'batch', true) !== self::BATCH || get_post_meta($id, self::PREFIX . 'key', true) !== $entry['key'] || !self::unchanged($id) || !hash_equals($hash, $entry['new_record_sha256'])) { throw new RuntimeException('Zmienił się wpis lub dane nowego pakietu #' . $id); }
                $stored = (string) get_post_meta($id, self::PREFIX . 'record_hash', true); $already = hash_equals($stored, $hash) && get_post_meta($id, self::PREFIX . 'photo_refresh', true) === 'media-20261009-v1';
                if (!$already && !hash_equals($stored, $entry['original_record_sha256'])) { throw new RuntimeException('Oryginalny hash wpisu nie zgadza się #' . $id); }
                $media = []; foreach (array_unique(array_merge([$record['featured_attachment_id']], $record['inline_image_ids'])) as $media_id) { $media[$media_id] = self::media($media_id); }
                $feature = $media[$record['featured_attachment_id']];
                $html = strtr(self::article_without_style($record['article_html']), ['{{FEATURED_URL}}' => esc_url($feature['url']), '{{FEATURED_ALT}}' => esc_attr($record['featured_alt']), '{{FEATURED_WIDTH}}' => (string) $feature['width'], '{{FEATURED_HEIGHT}}' => (string) $feature['height']]);
                $html = preg_replace_callback('/\{\{IMG:([1-9]\d*)\}\}/', static function ($m) use ($record, $media) { $image_id = (int) $m[1]; if (!in_array($image_id, $record['inline_image_ids'], true)) { throw new RuntimeException('Niezadeklarowany obraz.'); } return esc_url($media[$image_id]['url']); }, $html);
                $html = self::image_dimensions($html, $media); self::validate_html($html, $record['title'], array_column($media, 'url'), $record['faq']);
                if (self::photo_signature($post->post_content, $id === 5712 && $entry['key'] === 'e01-sprzedaz-produktow-cyfrowych') !== self::photo_signature($html)) { throw new RuntimeException('Propozycja zmienia coś poza zdjęciami/podpisami #' . $id); }
                $dates = self::dates($record['publish_at']); $category = get_term_by('slug', $record['category_slug'], 'category');
                if (!$category || is_wp_error($category) || $post->post_title !== $record['title'] || $post->post_name !== $record['slug'] || $post->post_excerpt !== $record['excerpt'] || $post->post_date !== $dates['local'] || $post->post_date_gmt !== $dates['gmt'] || wp_get_post_categories($id) !== [(int) $category->term_id] || (int) wp_next_scheduled('publish_future_post', [$id]) !== $dates['timestamp']) { throw new RuntimeException('Zmieniła się treść lub harmonogram #' . $id); }
                foreach (['rank_math_title' => $record['seo_title'], 'rank_math_description' => $record['description'], 'rank_math_focus_keyword' => implode(', ', $record['focus_keywords']), self::PREFIX . 'faq' => $record['faq'], self::PREFIX . 'source_id' => (string) $record['source_post_id'], self::PREFIX . 'research_volume' => (string) $record['keyword_volume']] as $key => $value) { if (get_post_meta($id, $key, true) !== $value) { throw new RuntimeException('Nowy pakiet zmienia SEO/dane poza zdjęciem #' . $id); } }
                $layout = self::source($id, $html, '', true);
                $row = ['id' => $id, 'record' => $record, 'entry' => $entry, 'already' => $already, 'html' => $html, 'json' => $layout['json'], 'before_hash' => self::fingerprint($id), 'unchanged_fields' => self::photo_unchanged_fields($id), 'old_thumbnail' => (int) get_post_thumbnail_id($id)];
                $out['rows'][] = $row; $proof[] = [$id, $entry, $row['before_hash'], $row['unchanged_fields'], $media]; if ($already) { $done++; }
            }
            $out['complete'] = $done === 19; $out['review_hash'] = hash('sha256', wp_json_encode([hash('sha256', $raw), hash('sha256', $wrapped), $proof]));
        } catch (Throwable $error) { $out['errors'][] = $error->getMessage(); }
        return $out;
    }

    public static function photo_apply(string $review_hash): array {
        if (!self::allowed()) { throw new RuntimeException('Brak uprawnień.'); }
        $review = self::photo_preflight();
        if ($review['errors'] || count($review['rows']) !== 19 || !preg_match('/^[a-f0-9]{64}$/D', $review_hash) || !hash_equals($review['review_hash'], $review_hash)) { throw new RuntimeException(implode(' ', $review['errors']) ?: 'Przegląd zdjęć zmienił się; odśwież stronę.'); }
        if ($review['complete']) { return array_column($review['rows'], 'id'); }
        $lock = ['token' => wp_generate_uuid4(), 'at' => time()]; if (!add_option(self::LOCK, $lock, '', false)) { throw new RuntimeException('Inne działanie pakietu trwa.'); }
        $changed = []; $backups = [];
        try {
            foreach ($review['rows'] as $row) {
                if ($row['already']) { continue; } $id = $row['id']; $record = $row['record'];
                if (get_post_status($id) !== 'future' || !hash_equals($row['before_hash'], self::fingerprint($id))) { throw new RuntimeException('Wpis zmieniono po przeglądzie #' . $id); }
                $post = get_post($id); $backup = ['post_content' => $post->post_content, '_elementor_data' => get_post_meta($id, '_elementor_data', true), '_thumbnail_id' => get_post_thumbnail_id($id)];
                foreach (['record_hash', 'featured_alt', 'ready_hash', 'content_ready_hash', 'photo_refresh'] as $name) { $backup[self::PREFIX . $name] = get_post_meta($id, self::PREFIX . $name, true); }
                $backups[$id] = $backup; update_post_meta($id, self::PREFIX . 'photo_backup', wp_slash($backup));
                if (get_post_meta($id, self::PREFIX . 'photo_backup', true) !== $backup) { throw new RuntimeException('Nie zapisano kopii zdjęć przed zmianą #' . $id); }
                $changed[] = $id;
                $result = wp_update_post(wp_slash(['ID' => $id, 'post_content' => $row['html']]), true);
                if (is_wp_error($result) || !$result) { throw new RuntimeException('Nie zapisano zdjęć #' . $id); }
                update_post_meta($id, '_elementor_data', wp_slash($row['json'])); set_post_thumbnail($id, $record['featured_attachment_id']);
                update_post_meta($id, self::PREFIX . 'featured_alt', $record['featured_alt']); update_post_meta($id, self::PREFIX . 'record_hash', $row['entry']['new_record_sha256']); update_post_meta($id, self::PREFIX . 'photo_refresh', 'media-20261009-v1');
                foreach (['_elementor_css', '_elementor_element_cache', '_elementor_page_assets'] as $cache) { delete_post_meta($id, $cache); } clean_post_cache($id);
                if (get_post($id)->post_content !== $row['html'] || get_post_meta($id, '_elementor_data', true) !== $row['json'] || (int) get_post_thumbnail_id($id) !== $record['featured_attachment_id'] || get_post_meta($id, self::PREFIX . 'record_hash', true) !== $row['entry']['new_record_sha256'] || get_post_meta($id, self::PREFIX . 'featured_alt', true) !== $record['featured_alt'] || !hash_equals($row['unchanged_fields'], self::photo_unchanged_fields($id))) { throw new RuntimeException('Kontrola zdjęć/statusu/daty/cron/SEO nie powiodła się #' . $id); }
                update_post_meta($id, self::PREFIX . 'ready_hash', self::fingerprint($id)); update_post_meta($id, self::PREFIX . 'content_ready_hash', self::fingerprint($id, false));
                if (!self::unchanged($id)) { throw new RuntimeException('Kontrola fingerprintu zdjęć nie powiodła się #' . $id); }
            }
            update_option('zp_cb_20261009_photo_results', ['refresh' => 'media-20261009-v1', 'changed_ids' => $changed, 'checked_ids' => array_column($review['rows'], 'id'), 'status_dates_cron_rankmath_unchanged' => true, 'at_utc' => gmdate('c')], false);
            return array_column($review['rows'], 'id');
        } catch (Throwable $error) {
            foreach ($review['rows'] as $row) {
                $id = $row['id']; if (!in_array($id, $changed, true) || !isset($backups[$id]) || get_post_status($id) !== 'future' || !hash_equals($row['unchanged_fields'], self::photo_unchanged_fields($id))) { continue; }
                $backup = $backups[$id]; $current = get_post($id)->post_content; $json = get_post_meta($id, '_elementor_data', true);
                if (!in_array($current, [$backup['post_content'], $row['html']], true) || !in_array($json, [$backup['_elementor_data'], $row['json']], true)) { continue; }
                $our_values = ['_thumbnail_id' => (string) $row['record']['featured_attachment_id'], self::PREFIX . 'featured_alt' => $row['record']['featured_alt'], self::PREFIX . 'record_hash' => $row['entry']['new_record_sha256'], self::PREFIX . 'photo_refresh' => 'media-20261009-v1']; $safe = true;
                foreach ($our_values as $key => $value) { if (!in_array(get_post_meta($id, $key, true), [(string) $backup[$key], $value], true)) { $safe = false; break; } }
                if (!$safe) { continue; }
                wp_update_post(wp_slash(['ID' => $id, 'post_content' => $backup['post_content']])); foreach ($backup as $key => $value) { if ($key !== 'post_content') { update_post_meta($id, $key, wp_slash($value)); } } clean_post_cache($id);
            }
            update_option('zp_cb_20261009_photo_results', ['refresh' => 'media-20261009-v1', 'changed_ids' => $changed, 'error' => $error->getMessage(), 'recoverable_backups' => true, 'at_utc' => gmdate('c')], false); throw $error;
        } finally { if (get_option(self::LOCK) === $lock) { delete_option(self::LOCK); } }
    }

    public static function photo_handle(): void {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST' || !self::allowed() || !wp_verify_nonce(isset($_POST['_wpnonce']) && is_string($_POST['_wpnonce']) ? wp_unslash($_POST['_wpnonce']) : '', 'zp_cb_20261009_photos')) { wp_die('Brak uprawnień lub nieprawidłowy nonce.', '', ['response' => 403]); }
        try { self::photo_apply(isset($_POST['review_hash']) && is_string($_POST['review_hash']) ? wp_unslash($_POST['review_hash']) : ''); } catch (Throwable $error) { set_transient('zp_cb_20261009_error_' . get_current_user_id(), $error->getMessage(), 600); }
        wp_safe_redirect(admin_url('admin.php?page=zp-suite-photos-20261009')); exit;
    }

    public static function photo_page(): void {
        if (!self::allowed()) { wp_die('Brak wymaganych uprawnień.', '', ['response' => 403]); }
        $review = self::photo_preflight(); echo '<div class="wrap"><h1>Zdjęcia 19 przyszłych artykułów</h1><p>Zmiana obejmuje tylko zdjęcia, alt i podpisy. Tytuły, teksty, FAQ, Rank Math, daty i cron pozostają. Opublikowany artykuł AI #5704 jest wyłączony. Jedyny dodatkowy link poprawiamy w #5712 na aktualną dokumentację WooCommerce.</p>';
        $error = get_transient('zp_cb_20261009_error_' . get_current_user_id()); if ($error) { echo '<div class="notice notice-error"><p>' . esc_html($error) . '</p></div>'; delete_transient('zp_cb_20261009_error_' . get_current_user_id()); }
        foreach ($review['errors'] as $message) { echo '<div class="notice notice-error"><p>' . esc_html($message) . '</p></div>'; }
        echo '<table class="widefat striped"><tr><th>Wpis / podgląd</th><th>Dotychczasowa okładka</th><th>Nowa okładka / zdjęcia</th><th>Kontrola</th></tr>';
        foreach ($review['rows'] as $row) { $record = $row['record']; echo '<tr><td>#' . (int) $row['id'] . ' ' . esc_html($record['title']) . '<br><a target="_blank" rel="noopener" href="' . esc_url(get_preview_post_link($row['id'])) . '">Podgląd WordPress</a></td><td>' . wp_get_attachment_image($row['old_thumbnail'], [180,120]) . '<br>#' . (int) $row['old_thumbnail'] . '</td><td>' . wp_get_attachment_image($record['featured_attachment_id'], [180,120], false, ['alt' => $record['featured_alt']]) . '<br>#' . (int) $record['featured_attachment_id'] . ' — ' . esc_html($record['featured_alt']) . '<br>Wewnątrz: ' . esc_html(implode(', ', $record['inline_image_ids'])) . '<details><summary>Nowy HTML i podpisy</summary><pre style="white-space:pre-wrap;max-height:250px;overflow:auto">' . esc_html($row['html']) . '</pre></details></td><td>' . ($row['already'] ? 'Już zastosowane' : 'Gotowy: treść/SEO/daty/cron sprawdzone') . '</td></tr>'; }
        echo '</table>'; if (!$review['errors'] && count($review['rows']) === 19 && !$review['complete']) { echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '">'; wp_nonce_field('zp_cb_20261009_photos'); echo '<input type="hidden" name="action" value="zp_cb_20261009_photos"><input type="hidden" name="review_hash" value="' . esc_attr($review['review_hash']) . '">'; submit_button('Zastosuj zdjęcia wyłącznie do tych 19 przyszłych wpisów'); echo '</form>'; } elseif ($review['complete']) { echo '<p>Wszystkie 19 zmian już zastosowano; nie tworzymy ani nie publikujemy wpisów.</p>'; } echo '</div>';
    }

    public static function page(): void {
        if (!self::allowed()) { wp_die('Wymagane uprawnienia administratora, edit_posts i unfiltered_html.', '', ['response' => 403]); }
        $review = self::preflight(); $state = get_option(self::STATE, []); $error = get_transient('zp_cb_20261009_error_' . get_current_user_id());
        echo '<div class="wrap"><h1>20 artykułów — przegląd i harmonogram</h1><p>Nowy pakiet Senuto. Wpisy powstaną po użyciu przycisku poniżej. Istniejące wpisy i terminy publikacji są tylko odczytywane.</p>';
        if ($error) { echo '<div class="notice notice-error"><p>' . nl2br(esc_html($error)) . '</p></div>'; delete_transient('zp_cb_20261009_error_' . get_current_user_id()); }
        if ($review['pack']) { echo '<p>' . esc_html($review['pack']['research_note']) . '</p>'; }
        if ($review['errors']) { echo '<div class="notice notice-error"><p><strong>Pakiet zablokowany — nic nie zostanie utworzone.</strong></p><ul>'; foreach ($review['errors'] as $message) { echo '<li>' . esc_html($message) . '</li>'; } echo '</ul></div>'; }
        if ($review['complete']) { echo '<div class="notice notice-success"><p>Wszystkie 20 wpisów tego pakietu już istnieje. Ponowne uruchomienie nie tworzy duplikatów.</p></div>'; }
        if (!empty($state['created_ids'])) { echo '<h2>Utworzone wpisy</h2><p>Status pakietu: ' . esc_html($state['status'] ?? '') . '</p><ul>'; foreach ($state['created_ids'] as $id) { if (get_post_meta((int) $id, self::PREFIX . 'batch', true) !== self::BATCH) { continue; } echo '<li>#' . (int) $id . ' ' . esc_html(get_the_title($id)) . ' (' . esc_html(get_post_status($id)) . ') — <a href="' . esc_url(get_edit_post_link($id, 'raw')) . '">Edytuj w WordPress</a> · <a target="_blank" rel="noopener" href="' . esc_url(get_preview_post_link($id)) . '">Podgląd WordPress</a></li>'; } echo '</ul>'; }
        echo '<table class="widefat striped"><thead><tr><th>Artykuł / Rank Math</th><th>Warszawa / strefa WordPress</th><th>Frazy i wolumen</th><th>Zdjęcie / źródło</th><th>Kontrola</th></tr></thead><tbody>';
        foreach ($review['rows'] as $index => $row) {
            $record = $row['record']; echo '<tr><td><strong>' . ($index + 1) . '. ' . esc_html($record['title']) . '</strong><br>' . esc_html($record['slug']) . '<br>Tytuł SEO: ' . esc_html($record['seo_title']) . '<br>Opis: ' . esc_html($record['description']) . '<details><summary>Zobacz pełną treść HTML</summary><pre style="white-space:pre-wrap;max-height:360px;overflow:auto">' . esc_html($row['html'] ?? $record['article_html']) . '</pre></details></td>';
            echo '<td>' . esc_html($row['dates']['warsaw'] ?? $record['publish_at']) . '<br>WP: ' . esc_html($row['dates']['local'] ?? '') . ' (' . esc_html(wp_timezone_string()) . ')</td><td>' . esc_html(implode(', ', $record['focus_keywords'])) . '<br>Senuto: ' . (int) $record['keyword_volume'] . '/mies.</td>';
            $existing_label = $row['existing_id'] ? 'Istnieje #' . $row['existing_id'] . ' (' . get_post_status($row['existing_id']) . ')' : 'Gotowy';
            if ($row['existing_id'] && get_post_status($row['existing_id']) === 'publish') { $actual = get_post($row['existing_id']); $actual_date = new DateTimeImmutable($actual->post_date_gmt, new DateTimeZone('UTC')); $existing_label .= ' — opublikowany ' . $actual_date->setTimezone(new DateTimeZone('Europe/Warsaw'))->format('Y-m-d H:i:s T'); }
            echo '<td>' . wp_get_attachment_image($record['featured_attachment_id'], [180, 120], false, ['alt' => $record['featured_alt'], 'style' => 'max-width:180px;height:auto']) . '<br>Obraz #' . (int) $record['featured_attachment_id'] . '<br>Źródło #' . (int) $record['source_post_id'] . '</td><td>' . esc_html($row['errors'] ? implode(' ', $row['errors']) : $existing_label) . '</td></tr>';
        }
        echo '</tbody></table>';
        if (!$review['errors'] && !$review['complete'] && count($review['rows']) === 20) { echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '">'; wp_nonce_field(self::ACTION); echo '<input type="hidden" name="action" value="' . esc_attr(self::ACTION) . '"><input type="hidden" name="manifest_sha256" value="' . esc_attr($review['sha']) . '"><input type="hidden" name="review_hash" value="' . esc_attr($review['review_hash']) . '">'; submit_button('Utwórz 20 kompletnych wpisów i zaplanuj publikację'); echo '</form>'; }
        echo '</div>';
    }

    /** One BlogPosting in Rank Math's head graph for this batch only; remove duplicate article nodes. */
    public static function schema($data) {
        if (!is_array($data) || is_admin() || !is_singular('post')) { return $data; }
        $id = get_queried_object_id();
        if (get_post_meta($id, self::PREFIX . 'batch', true) !== self::BATCH || !self::unchanged($id)) { return $data; }
        $url = get_permalink($id); $author = null; $publisher = null; $webpage_id = $url . '#webpage';
        foreach ($data as $key => $node) {
            if (!is_array($node)) { continue; } $types = (array) ($node['@type'] ?? []);
            if (array_intersect($types, ['Article', 'BlogPosting', 'NewsArticle'])) { $author = $node['author'] ?? $author; $publisher = $node['publisher'] ?? $publisher; unset($data[$key]); }
            if (in_array('FAQPage', $types, true)) { unset($data[$key]); }
            elseif (in_array('WebPage', $types, true) && isset($node['@id'])) { $webpage_id = $node['@id']; }
        }
        $post = get_post($id); if (!$post) { return $data; }
        $node = ['@type' => 'BlogPosting', '@id' => $url . '#blogposting', 'url' => $url, 'headline' => $post->post_title, 'description' => (string) get_post_meta($id, 'rank_math_description', true), 'datePublished' => get_post_time(DATE_W3C, false, $post), 'dateModified' => get_post_modified_time(DATE_W3C, false, $post), 'mainEntityOfPage' => ['@id' => $webpage_id], 'inLanguage' => 'pl-PL'];
        if ($author) { $node['author'] = $author; } else { $node['author'] = ['@type' => 'Person', 'name' => get_the_author_meta('display_name', $post->post_author), 'url' => get_author_posts_url($post->post_author)]; }
        if ($publisher) { $node['publisher'] = $publisher; }
        $image = wp_get_attachment_image_src(get_post_thumbnail_id($id), 'full');
        if ($image) { $node['image'] = ['@type' => 'ImageObject', 'url' => $image[0], 'width' => (int) $image[1], 'height' => (int) $image[2]]; }
        $data['zp_cb_20261009_blogposting'] = $node;
        $faq = get_post_meta($id, self::PREFIX . 'faq', true); $text = self::normalize($post->post_content); $questions = [];
        if (is_array($faq)) { foreach ($faq as $item) { if (!is_array($item) || empty($item['question']) || empty($item['answer']) || strpos($text, self::normalize($item['question'])) === false || strpos($text, self::normalize($item['answer'])) === false) { $questions = []; break; } $questions[] = ['@type' => 'Question', 'name' => $item['question'], 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $item['answer']]]; } }
        if ($questions) { $data['zp_cb_20261009_faq'] = ['@type' => 'FAQPage', '@id' => $url . '#faq', 'url' => $url, 'mainEntity' => $questions]; }
        return $data;
    }
}

add_action('admin_menu', static function () {
    if (ZP_Content_Batch_20261009::allowed()) { add_submenu_page('zp-suite', '20 artykułów — Senuto', '20 artykułów — Senuto', 'manage_options', ZP_Content_Batch_20261009::PAGE, ['ZP_Content_Batch_20261009', 'page']); }
}, 10001);
add_action('admin_post_zp_cb_20261009_schedule', ['ZP_Content_Batch_20261009', 'handle']);
add_action('admin_menu', static function () { if (ZP_Content_Batch_20261009::allowed()) { add_submenu_page('zp-suite', 'Zdjęcia 19 artykułów', 'Zdjęcia 19 artykułów', 'manage_options', 'zp-suite-photos-20261009', ['ZP_Content_Batch_20261009', 'photo_page']); } }, 10002);
add_action('admin_post_zp_cb_20261009_photos', ['ZP_Content_Batch_20261009', 'photo_handle']);
add_filter('rank_math/json_ld', ['ZP_Content_Batch_20261009', 'schema'], 120);
