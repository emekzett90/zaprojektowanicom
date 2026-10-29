<?php
/**
 * „Nowe zdjęcia wpisów” (Mat, 21.10.2026): every post from the last two weeks and from the schedule gets three
 * concrete photos with Polish text in the article and a separate, fourth photo as its cover.
 *
 * The cloud prepares the photos and a reviewed package on the content branch (tresci-wpisy): zdjecia-wpisow.json
 * lists the posts, and the photos sit in obrazy/ with their sha256, like the feed's own photos. ZP Suite → Treści →
 * Nowe zdjęcia wpisów shows each listed post with its current and new photos. „Zastosuj” changes only the photos of
 * one post (or of the next five): the article's photo figures give way to three new ones at the end of the named
 * sections, placed and styled the way that article's photos stand today (inside its sections or between them, with
 * the same figure class); the cover becomes the featured image (and the Rank Math social image where that showed
 * the old cover); Codex's batch posts get their fingerprint rebased, as his photo refresh does, so BlogPosting and
 * FAQPage stay. Before saving, the article without its photo figures must read exactly as before; after saving,
 * status, date, address, title, categories and the Rank Math fields must match the snapshot, or the post is restored.
 * Each post keeps a backup, and „Cofnij” restores it while nobody has edited the post since. Nothing runs on page
 * views or by cron.
 */
if (!defined('ABSPATH')) { exit; }

final class ZP_Article_Photo_Package_20261022 {
    public const PAGE = 'zp-suite-nowe-zdjecia-wpisow';
    public const ACTION = 'zp_article_photo_package_20261022';
    private const MANIFEST = 'zdjecia-wpisow.json';
    private const MAX_MANIFEST = 524288;
    private const PACKAGE = 'zp_app_20261022_package';
    private const LOG = 'zp_app_20261022_log';
    private const LOCK = 'zp_app_20261022_lock';
    private const META = '_zp_app_20261022';
    private const STEP = 5;
    private const CB = '_zp_cb_20261009_';
    private const CB_BATCH = 'senuto-20261009-v1';
    private const ELEMENTOR_CACHE = ['_elementor_css', '_elementor_element_cache', '_elementor_page_assets'];
    /** Saved before and compared after: the photos may change, these may not. */
    private const KEEP_META = ['rank_math_title', 'rank_math_description', 'rank_math_focus_keyword', 'rank_math_canonical_url', 'rank_math_robots', 'rank_math_primary_category', 'rank_math_rich_snippet', 'rank_math_snippet_article_type', 'rank_math_schema_Article', '_wp_page_template', '_elementor_edit_mode', '_elementor_template_type', '_elementor_page_settings'];
    /** The photo fields a backup holds besides the content, the Elementor data and the featured image. */
    private const PHOTO_META = ['rank_math_facebook_image', 'rank_math_facebook_image_id', 'rank_math_twitter_image', 'rank_math_twitter_image_id', self::CB . 'featured_alt', self::CB . 'ready_hash', self::CB . 'content_ready_hash'];
    /** Post fields a content save keeps as they are, as in Codex's photo refresh. */
    private const PINNED = ['post_date', 'post_date_gmt', 'post_status', 'post_title', 'post_name', 'post_excerpt', 'post_author', 'post_password'];

    public static function register(): void {
        add_action('admin_menu', [self::class, 'menu'], 10020);
        add_action('admin_post_' . self::ACTION, [self::class, 'handle']);
        add_filter('zp_suite3_sections', static function ($sections) {
            if (is_array($sections) && isset($sections['content'][2]) && is_array($sections['content'][2])) { $sections['content'][2][self::PAGE] = 'Nowe zdjęcia wpisów'; }
            return $sections;
        });
        // The package, the log and the backups are on no page, so they never empty the HTML cache by themselves.
        add_filter('zp_html_cache_ignore_option', static fn($skip, $option = '') => $skip || in_array($option, [self::PACKAGE, self::LOG, self::LOCK], true), 10, 2);
        add_filter('zp_html_cache_ignore_meta', static fn($skip, $key = '') => $skip || $key === self::META, 10, 2);
    }

    public static function allowed(): bool {
        return current_user_can('manage_options') && current_user_can('edit_posts') && current_user_can('upload_files') && current_user_can('unfiltered_html');
    }

    public static function menu(): void {
        if (self::allowed()) { add_submenu_page('zp-suite', 'Nowe zdjęcia wpisów', 'Nowe zdjęcia wpisów', 'manage_options', self::PAGE, [self::class, 'page']); }
    }

    /* ------------------------------------------------------------------ package */

    private static function base_url(): string {
        return function_exists('zp_feed_base_url') ? zp_feed_base_url() : 'https://raw.githubusercontent.com/emekzett90/zaprojektowanicom/tresci-wpisy/';
    }

    /** Downloads and checks the package. Throws RuntimeException with a message for the panel. */
    private static function download(): array {
        $body = apply_filters('zp_app_20261022_pre_get', null);
        if ($body === null) {
            $res = wp_remote_get(self::base_url() . self::MANIFEST . '?v=' . time(), ['timeout' => 20, 'redirection' => 2, 'limit_response_size' => self::MAX_MANIFEST + 1]);
            if (is_wp_error($res)) { throw new RuntimeException('nie udało się pobrać pakietu: ' . $res->get_error_message()); }
            $code = (int) wp_remote_retrieve_response_code($res);
            if ($code !== 200) { throw new RuntimeException('pakietu nie ma na kanale (HTTP ' . $code . ')'); }
            $body = (string) wp_remote_retrieve_body($res);
        }
        if (!is_string($body) || $body === '' || strlen($body) > self::MAX_MANIFEST) { throw new RuntimeException('pakiet jest pusty albo za duży'); }
        $data = json_decode($body, true);
        if (!is_array($data) || ($data['format'] ?? null) !== 1 || !is_string($data['pakiet'] ?? null) || !preg_match('~^[a-z0-9-]{3,80}$~D', $data['pakiet'])
            || !is_array($data['wpisy'] ?? null) || !array_is_list($data['wpisy']) || count($data['wpisy']) < 1 || count($data['wpisy']) > 100) {
            throw new RuntimeException('pakiet ma zły format');
        }
        $seen = [];
        foreach ($data['wpisy'] as $n => $entry) {
            $error = self::entry_error($entry);
            if ($error !== '') { throw new RuntimeException('pozycja ' . ($n + 1) . ': ' . $error); }
            if (isset($seen[$entry['id']])) { throw new RuntimeException('wpis ' . $entry['id'] . ' jest w pakiecie dwa razy'); }
            $seen[$entry['id']] = true;
        }
        return ['pakiet' => $data['pakiet'], 'wpisy' => $data['wpisy'], 'sha256' => hash('sha256', $body), 'at' => time()];
    }

    private static function entry_error($e): string {
        if (!is_array($e) || !is_int($e['id'] ?? null) || $e['id'] < 1 || !is_string($e['slug'] ?? null) || !preg_match('~^[a-z0-9-]{1,200}$~D', $e['slug'])) { return 'brak ID albo adresu wpisu'; }
        if (!self::image_ok($e['okladka'] ?? null, false)) { return 'zły opis okładki'; }
        if (!is_array($e['obrazy'] ?? null) || !array_is_list($e['obrazy']) || count($e['obrazy']) !== 3) { return 'wpis musi mieć dokładnie trzy zdjęcia w treści'; }
        foreach ($e['obrazy'] as $img) { if (!self::image_ok($img, true)) { return 'zły opis zdjęcia w treści'; } }
        if (count(array_unique(array_column($e['obrazy'], 'sekcja'))) !== 3) { return 'każde zdjęcie w treści musi stać w innej sekcji'; }
        if (count(array_unique(array_merge([$e['okladka']['sha256']], array_column($e['obrazy'], 'sha256')))) !== 4) { return 'każde z czterech zdjęć musi być inne'; }
        return '';
    }

    private static function image_ok($img, bool $inline): bool {
        if (!is_array($img) || !is_string($img['plik'] ?? null) || !preg_match('~^obrazy/[a-z0-9-]+\.webp$~D', $img['plik'])
            || !is_string($img['sha256'] ?? null) || !preg_match('~^[a-f0-9]{64}$~D', $img['sha256'])
            || !is_string($img['alt'] ?? null) || mb_strlen(trim($img['alt'])) < 10 || mb_strlen($img['alt']) > 250) { return false; }
        if (!$inline) { return true; }
        return is_string($img['podpis'] ?? null) && mb_strlen(trim($img['podpis'])) >= 10 && mb_strlen($img['podpis']) <= 300
            && is_string($img['sekcja'] ?? null) && (bool) preg_match('~^[a-z0-9-]{1,80}$~D', $img['sekcja']);
    }

    private static function package(): ?array {
        $p = get_option(self::PACKAGE);
        return is_array($p) && is_array($p['wpisy'] ?? null) ? $p : null;
    }

    private static function entry_hash(array $entry): string { return hash('sha256', (string) wp_json_encode($entry)); }

    /* ------------------------------------------------------------------ article */

    /** The Elementor data and the one HTML widget that holds the article. */
    private static function article(int $id): array {
        $raw = get_post_meta($id, '_elementor_data', true);
        $tree = is_array($raw) ? $raw : json_decode((string) $raw, true);
        if (!is_array($tree)) { throw new RuntimeException('wpis nie ma danych Elementora'); }
        $found = [];
        $walk = static function (array $items) use (&$walk, &$found): void {
            foreach ($items as $item) {
                if (!is_array($item)) { continue; }
                $html = $item['settings']['html'] ?? null;
                if (($item['widgetType'] ?? '') === 'html' && is_string($html) && preg_match('~<article\b~i', $html) && preg_match('~<section\b~i', $html)) { $found[] = $html; }
                if (is_array($item['elements'] ?? null)) { $walk($item['elements']); }
            }
        };
        $walk($tree);
        if (count($found) !== 1) { throw new RuntimeException('artykuł nie stoi w jednym widżecie HTML Elementora'); }
        return ['raw' => $raw, 'tree' => $tree, 'html' => $found[0]];
    }

    /** Photo figures: <figure> without a class or with zpArticleNew__imageBlock, holding one image. */
    public static function photo_figures(string $html): array {
        preg_match_all('~<figure\b([^>]*)>(.*?)</figure>~is', $html, $matches, PREG_SET_ORDER);
        $out = [];
        foreach ($matches as $m) {
            $classes = preg_match('~\bclass\s*=\s*["\']([^"\']*)["\']~i', $m[1], $c) ? preg_split('~\s+~', trim($c[1])) : [];
            if ($classes && $classes !== [''] && !in_array('zpArticleNew__imageBlock', $classes, true)) { continue; }
            if (preg_match_all('~<img\b~i', $m[2]) !== 1) { continue; }
            $out[] = $m[0];
        }
        return $out;
    }

    /** The article without its photo figures, whitespace between tags ignored: what a photo change must keep. */
    public static function without_photos(string $html): string {
        foreach (self::photo_figures($html) as $figure) { $html = str_replace($figure, '', $html); }
        return (string) preg_replace(['~\s+~u', '~>\s+<~'], [' ', '><'], trim($html));
    }

    /** Offsets of the section with this id: [start of its tag, end of its tag, start of its </section>, end of it]. */
    private static function section_bounds(string $html, string $id): array {
        $open = '~<section\b[^>]*\sid\s*=\s*["\']' . preg_quote($id, '~') . '["\'][^>]*>~i';
        if (preg_match_all($open, $html, $m, PREG_OFFSET_CAPTURE) !== 1) { throw new RuntimeException('w artykule nie ma sekcji „' . $id . '”'); }
        $start = $m[0][0][1];
        $inner = $start + strlen($m[0][0][0]);
        // Sections may hold sections (the steps blocks), so the closing tag is found by depth.
        preg_match_all('~<(/?)section\b[^>]*>~i', $html, $tags, PREG_SET_ORDER | PREG_OFFSET_CAPTURE, $inner);
        $depth = 1;
        foreach ($tags as $tag) {
            $depth += $tag[1][0] === '/' ? -1 : 1;
            if ($depth === 0) { return [$start, $inner, $tag[0][1], $tag[0][1] + strlen($tag[0][0])]; }
        }
        throw new RuntimeException('sekcja „' . $id . '” nie ma zamknięcia');
    }

    /** How this article shows its photos today: the figure's class and whether figures stand inside sections. */
    public static function layout(string $html): array {
        $class = 'zpArticleNew__imageBlock';
        $inside = 0;
        $between = 0;
        foreach (self::photo_figures($html) as $n => $figure) {
            if ($n === 0) { $class = preg_match('~^<figure\b[^>]*\bclass\s*=\s*["\']([^"\']*)["\']~i', $figure, $c) ? trim($c[1]) : ''; }
            $before = substr($html, 0, (int) strpos($html, $figure));
            (preg_match_all('~<section\b~i', $before) - preg_match_all('~</section\s*>~i', $before)) > 0 ? $inside++ : $between++;
        }
        return ['class' => $class, 'inside' => $inside > $between];
    }

    /**
     * The article with its photo figures replaced by new ones ($photos: section id => photo), each at the end of its
     * section: before its </section> where this article's photos stand inside sections, else right after it.
     */
    public static function place(string $html, array $photos): string {
        $layout = self::layout($html);
        $out = $html;
        foreach (self::photo_figures($html) as $figure) {
            if (substr_count($out, $figure) !== 1) { throw new RuntimeException('to samo zdjęcie stoi w artykule dwa razy'); }
            // The figure leaves with the white space before it, so no empty lines stay behind.
            $at = (int) strpos($out, $figure);
            $from = $at;
            while ($from > 0 && ctype_space($out[$from - 1])) { $from--; }
            $out = substr($out, 0, $from) . substr($out, $at + strlen($figure));
        }
        foreach ($photos as $section => $photo) {
            [, , $close, $after] = self::section_bounds($out, (string) $section);
            $at = $layout['inside'] ? $close : $after;
            $out = substr($out, 0, $at) . "\n" . self::figure_html($photo, $layout['class']) . substr($out, $at);
        }
        if (count(self::photo_figures($out)) !== count($photos) || self::without_photos($out) !== self::without_photos($html)) {
            throw new RuntimeException('zmiana dotknęłaby czegoś poza zdjęciami');
        }
        return $out;
    }

    /** A photo from the media library, ready for the article. */
    private static function photo(int $attachment, string $alt, string $caption): array {
        $src = (string) wp_get_attachment_url($attachment);
        $meta = wp_get_attachment_metadata($attachment);
        $w = (int) ($meta['width'] ?? 0);
        $h = (int) ($meta['height'] ?? 0);
        if ($src === '' || $w < 1 || $h < 1) { throw new RuntimeException('zdjęcie nie trafiło do biblioteki mediów'); }
        // The article template shows photos at 16:9, so a different shape would be cut.
        if ($w < 1200 || abs($w / $h - 16 / 9) > 0.02) { throw new RuntimeException('zdjęcie w treści ma ' . $w . '×' . $h . ' zamiast proporcji 16:9'); }
        return ['src' => $src, 'w' => $w, 'h' => $h, 'alt' => $alt, 'caption' => $caption];
    }

    /** The image class tells the speed optimizer this is an article photo (not a small logo); it carries no styles. */
    public static function figure_html(array $p, string $class): string {
        return '<figure' . ($class !== '' ? ' class="' . esc_attr($class) . '"' : '') . '><img class="zpArticleNew__img" src="' . esc_url($p['src']) . '" alt="' . esc_attr($p['alt']) . '" width="' . (int) $p['w'] . '" height="' . (int) $p['h'] . '" loading="lazy" decoding="async"><figcaption>' . esc_html($p['caption']) . '</figcaption></figure>';
    }

    /** The Elementor data with the article widget's HTML replaced, keeping the stored JSON encoding where possible. */
    private static function elementor_data($raw, array $tree, string $old, string $new) {
        $count = 0;
        $walk = static function (array &$items) use (&$walk, &$count, $old, $new): void {
            foreach ($items as &$item) {
                if (!is_array($item)) { continue; }
                if (($item['widgetType'] ?? '') === 'html' && ($item['settings']['html'] ?? null) === $old) { $item['settings']['html'] = $new; $count++; }
                if (is_array($item['elements'] ?? null)) { $walk($item['elements']); }
            }
            unset($item);
        };
        $walk($tree);
        if ($count !== 1) { throw new RuntimeException('nie znaleziono widżetu artykułu'); }
        if (is_array($raw)) { return $tree; }
        foreach ([0, JSON_UNESCAPED_UNICODE, JSON_UNESCAPED_SLASHES, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES] as $flags) {
            $from = json_encode($old, $flags);
            if (!is_string($from) || substr_count((string) $raw, $from) !== 1) { continue; }
            $result = str_replace($from, (string) json_encode($new, $flags), (string) $raw);
            if (json_decode($result, true) === $tree) { return $result; }
        }
        $result = wp_json_encode($tree);
        if (!is_string($result) || json_decode($result, true) !== $tree) { throw new RuntimeException('nie udało się przygotować danych Elementora'); }
        return $result;
    }

    /* ------------------------------------------------------------------ state */

    /** What must not change: status, date, address, title, categories, author and the SEO fields. */
    private static function snapshot(int $id): array {
        clean_post_cache($id);
        $p = get_post($id);
        $meta = [];
        foreach (self::KEEP_META as $key) { $meta[$key] = get_post_meta($id, $key, true); }
        $cats = wp_get_post_categories($id);
        sort($cats);
        return ['title' => $p->post_title, 'name' => $p->post_name, 'status' => $p->post_status, 'date' => $p->post_date, 'date_gmt' => $p->post_date_gmt,
            'excerpt' => $p->post_excerpt, 'author' => (int) $p->post_author, 'password' => $p->post_password, 'cats' => $cats,
            'cron' => $p->post_status === 'future' ? (int) wp_next_scheduled('publish_future_post', [$id]) : 0, 'meta' => $meta];
    }

    /** Everything a photo change touches; the backup. */
    private static function photo_state(int $id): array {
        clean_post_cache($id);
        $state = ['post_content' => (string) get_post_field('post_content', $id, 'raw'), '_elementor_data' => get_post_meta($id, '_elementor_data', true), '_thumbnail_id' => (int) get_post_thumbnail_id($id)];
        foreach (self::PHOTO_META as $key) { $state[$key] = metadata_exists('post', $id, $key) ? get_post_meta($id, $key, true) : null; }
        return $state;
    }

    /** Content, Elementor data and featured image: any later edit changes this. */
    private static function content_hash(int $id): string {
        $s = self::photo_state($id);
        return hash('sha256', (string) wp_json_encode([$s['post_content'], $s['_elementor_data'], $s['_thumbnail_id']]));
    }

    /**
     * Saves only the content: date, status, address, title, excerpt, author and password stay as they are, and the
     * empty page_template keeps WordPress from checking (or resetting) the post's template.
     */
    private static function save_content(int $id, string $content): void {
        $old = get_post($id);
        $pin = static function ($data, $postarr) use ($id, $old) {
            if ((int) ($postarr['ID'] ?? 0) === $id) { foreach (self::PINNED as $key) { $data[$key] = $old->$key; } }
            return $data;
        };
        add_filter('wp_insert_post_data', $pin, PHP_INT_MAX, 2);
        try {
            $r = wp_update_post(wp_slash(['ID' => $id, 'post_content' => $content, 'page_template' => '']), true);
            if (is_wp_error($r) || !$r) { throw new RuntimeException('WordPress nie zapisał treści'); }
        } finally {
            remove_filter('wp_insert_post_data', $pin, PHP_INT_MAX);
        }
    }

    private static function restore(int $id, array $state): void {
        if ((string) get_post_field('post_content', $id, 'raw') !== $state['post_content']) { self::save_content($id, $state['post_content']); }
        update_post_meta($id, '_elementor_data', wp_slash($state['_elementor_data']));
        if ($state['_thumbnail_id'] > 0) { set_post_thumbnail($id, $state['_thumbnail_id']); } else { delete_post_thumbnail($id); }
        foreach (self::PHOTO_META as $key) {
            if ($state[$key] === null) { delete_post_meta($id, $key); } else { update_post_meta($id, $key, wp_slash($state[$key])); }
        }
        foreach (self::ELEMENTOR_CACHE as $cache) { delete_post_meta($id, $cache); }
        clean_post_cache($id);
    }

    private static function codex_batch(int $id): bool {
        if (get_post_meta($id, self::CB . 'batch', true) !== self::CB_BATCH) { return false; }
        if (!class_exists('ZP_Content_Batch_20261009')) { require_once __DIR__ . '/content-batch-20261009.php'; }
        return true;
    }

    /* ------------------------------------------------------------------ apply and roll back */

    private static function check_post(array $entry): WP_Post {
        $post = get_post($entry['id']);
        if (!$post || $post->post_type !== 'post') { throw new RuntimeException('nie ma takiego wpisu'); }
        if (!in_array($post->post_status, ['publish', 'future'], true)) { throw new RuntimeException('wpis nie jest opublikowany ani zaplanowany'); }
        if ($post->post_name !== $entry['slug']) { throw new RuntimeException('wpis ma inny adres niż w pakiecie'); }
        if (!current_user_can('edit_post', $post->ID)) { throw new RuntimeException('brak uprawnień do tego wpisu'); }
        return $post;
    }

    /** Changes the photos of one post; returns a short note for the log. */
    private static function apply(array $package, array $entry): string {
        $post = self::check_post($entry);
        $id = $post->ID;
        $done = get_post_meta($id, self::META, true);
        if (is_array($done)) {
            if (empty($done['after'])) { throw new RuntimeException('poprzednia zmiana tego wpisu została przerwana; najpierw ją cofnij'); }
            if (($done['entry'] ?? '') === self::entry_hash($entry) && hash_equals((string) $done['after'], self::content_hash($id))) { return 'nowe zdjęcia już są'; }
            throw new RuntimeException('wpis ma już zdjęcia z pakietu; najpierw je cofnij');
        }
        $codex = self::codex_batch($id);
        $codex_ok = $codex && ZP_Content_Batch_20261009::unchanged($id);
        $article = self::article($id);
        $content = (string) get_post_field('post_content', $id, 'raw');

        // Photos into the media library first; each once by its sha256, so a retry reuses them.
        if (!function_exists('zp_feed_media')) { throw new RuntimeException('brak modułu Publikacja wpisów'); }
        $cover = zp_feed_media($entry['okladka'], true);
        if (is_wp_error($cover)) { throw new RuntimeException($cover->get_error_message()); }
        $photos = [];
        foreach ($entry['obrazy'] as $img) {
            $attachment = zp_feed_media($img, false);
            if (is_wp_error($attachment)) { throw new RuntimeException($attachment->get_error_message()); }
            $photos[$img['sekcja']] = self::photo((int) $attachment, trim($img['alt']), trim($img['podpis']));
        }
        $html = self::place($article['html'], $photos);
        // The post content carries the same article; posts saved in Elementor may keep it in another form, or none.
        $new_content = $content === $article['html'] ? $html : (trim($content) === '' ? $content : self::place($content, $photos));
        $data = self::elementor_data($article['raw'], $article['tree'], $article['html'], $html);

        $before = self::snapshot($id);
        $backup = self::photo_state($id);
        $record = ['package' => $package['pakiet'], 'entry' => self::entry_hash($entry), 'backup' => $backup, 'after' => '', 'at' => time(), 'user' => get_current_user_id()];
        update_post_meta($id, self::META, wp_slash($record));
        if (get_post_meta($id, self::META, true) !== $record) { delete_post_meta($id, self::META); throw new RuntimeException('nie udało się zapisać kopii zapasowej'); }
        try {
            if ($new_content !== $content) { self::save_content($id, $new_content); }
            update_post_meta($id, '_elementor_data', wp_slash($data));
            set_post_thumbnail($id, (int) $cover);
            // The social images follow the cover only where they showed the old one.
            $old_id = $backup['_thumbnail_id'];
            $old_url = $old_id > 0 ? (string) wp_get_attachment_url($old_id) : '';
            foreach (['rank_math_facebook_image', 'rank_math_twitter_image'] as $key) {
                if (($old_id > 0 && (int) $backup[$key . '_id'] === $old_id) || ($old_url !== '' && (string) $backup[$key] === $old_url)) {
                    update_post_meta($id, $key . '_id', (string) (int) $cover);
                    update_post_meta($id, $key, (string) wp_get_attachment_url((int) $cover));
                }
            }
            if ($codex) { update_post_meta($id, self::CB . 'featured_alt', trim($entry['okladka']['alt'])); }
            foreach (self::ELEMENTOR_CACHE as $cache) { delete_post_meta($id, $cache); }
            clean_post_cache($id);
            if ($codex_ok) {
                update_post_meta($id, self::CB . 'ready_hash', ZP_Content_Batch_20261009::fingerprint($id));
                update_post_meta($id, self::CB . 'content_ready_hash', ZP_Content_Batch_20261009::fingerprint($id, false));
            }
            $now = self::photo_state($id);
            if ($now['post_content'] !== $new_content || $now['_elementor_data'] !== $data || $now['_thumbnail_id'] !== (int) $cover) { throw new RuntimeException('odczyt po zapisie nie zgadza się'); }
            if (self::snapshot($id) !== $before) { throw new RuntimeException('zmieniłoby się coś poza zdjęciami (status, data, adres albo pola SEO)'); }
            if ($codex_ok && !ZP_Content_Batch_20261009::unchanged($id)) { throw new RuntimeException('odcisk wpisu z pakietu Codexa się nie zgadza'); }
        } catch (Throwable $e) {
            try {
                self::restore($id, $backup);
            } catch (Throwable $r) {
                // The backup stays with the post, so „Cofnij” can try again.
                throw new RuntimeException($e->getMessage() . '; przywracanie też się nie udało, kliknij Cofnij');
            }
            delete_post_meta($id, self::META);
            throw new RuntimeException($e->getMessage() . '; wpis przywrócony');
        }
        $record['after'] = self::content_hash($id);
        update_post_meta($id, self::META, wp_slash($record));
        return 'nowe zdjęcia' . ($codex ? ($codex_ok ? ', dane strukturalne zostają' : ', dane strukturalne Codexa były już wyłączone') : '');
    }

    private static function rollback(array $entry): string {
        $post = self::check_post($entry);
        $id = $post->ID;
        $record = get_post_meta($id, self::META, true);
        if (!is_array($record) || !is_array($record['backup'] ?? null)) { throw new RuntimeException('ten wpis nie ma zdjęć z pakietu'); }
        // An interrupted change has no "after" state; its backup is the post as it was before.
        if (!empty($record['after']) && !hash_equals((string) $record['after'], self::content_hash($id))) { throw new RuntimeException('wpis zmieniono po wymianie zdjęć, więc nie cofam go automatycznie'); }
        $before = self::snapshot($id);
        self::restore($id, $record['backup']);
        if (self::photo_state($id) !== $record['backup'] || self::snapshot($id) !== $before) { throw new RuntimeException('przywrócenie nie zgadza się z kopią; sprawdź wpis ręcznie'); }
        delete_post_meta($id, self::META);
        return 'przywrócono poprzednie zdjęcia';
    }

    /** One entry's state for the panel: [code, text]; code is gotowy, zrobione, przerwany, zmieniony or blad. */
    private static function status(array $entry): array {
        try {
            $post = self::check_post($entry);
        } catch (RuntimeException $e) {
            return ['blad', self::sentence($e->getMessage())];
        }
        $record = get_post_meta($post->ID, self::META, true);
        if (is_array($record)) {
            if (empty($record['after'])) { return ['przerwany', 'Zmiana została przerwana. „Cofnij” przywróci wpis sprzed niej.']; }
            if (!hash_equals((string) $record['after'], self::content_hash($post->ID))) { return ['zmieniony', 'Nowe zdjęcia są, ale wpis zmieniono później.']; }
            return ['zrobione', 'Nowe zdjęcia od ' . wp_date('j.m.Y H:i', (int) $record['at']) . (($record['entry'] ?? '') === self::entry_hash($entry) ? '.' : ' (z wcześniejszej wersji pakietu).')];
        }
        try {
            $html = self::article($post->ID)['html'];
            foreach ($entry['obrazy'] as $img) { self::section_bounds($html, $img['sekcja']); }
        } catch (RuntimeException $e) {
            return ['blad', self::sentence($e->getMessage())];
        }
        return ['gotowy', 'Gotowy do zmiany.'];
    }

    private static function sentence(string $text): string {
        return mb_strtoupper(mb_substr($text, 0, 1)) . mb_substr($text, 1) . '.';
    }

    private static function label(array $entry): string {
        $title = get_the_title($entry['id']);
        return '#' . $entry['id'] . ' ' . ($title !== '' ? $title : $entry['slug']);
    }

    /* ------------------------------------------------------------------ panel */

    private static function log(array $lines): void {
        if (!$lines) { return; }
        $log = get_option(self::LOG, []);
        $log = is_array($log) ? $log : [];
        foreach ($lines as $line) { array_unshift($log, ['at' => time(), 'text' => (string) $line]); }
        update_option(self::LOG, array_slice($log, 0, 60), false);
    }

    public static function handle(): void {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST' || !self::allowed() || !wp_verify_nonce(is_string($_POST['_wpnonce'] ?? null) ? wp_unslash($_POST['_wpnonce']) : '', self::ACTION)) {
            wp_die('Brak uprawnień lub nieprawidłowy nonce.', '', ['response' => 403]);
        }
        $do = is_string($_POST['do'] ?? null) ? sanitize_key(wp_unslash($_POST['do'])) : '';
        $id = is_string($_POST['id'] ?? null) && ctype_digit($_POST['id']) ? (int) $_POST['id'] : 0;
        $lines = [];
        if (!add_option(self::LOCK, time(), '', false)) {
            if ((int) get_option(self::LOCK) > time() - 600) { self::log(['Inna zmiana zdjęć jeszcze trwa; spróbuj za chwilę.']); self::back(); }
            update_option(self::LOCK, time(), false);
        }
        if (function_exists('set_time_limit')) { @set_time_limit(180); }
        try {
            if ($do === 'pobierz') {
                $package = self::download();
                update_option(self::PACKAGE, $package, false);
                $lines[] = 'Pobrano pakiet ' . $package['pakiet'] . ': ' . count($package['wpisy']) . ' wpisów.';
            } elseif (in_array($do, ['zastosuj', 'cofnij', 'nastepne'], true)) {
                $package = self::package();
                if (!$package) { throw new RuntimeException('najpierw pobierz pakiet'); }
                if ($do === 'nastepne') {
                    $deadline = microtime(true) + 25;
                    $n = 0;
                    foreach ($package['wpisy'] as $entry) {
                        if ($n >= self::STEP || microtime(true) > $deadline) { break; }
                        if (self::status($entry)[0] !== 'gotowy') { continue; }
                        $n++;
                        try {
                            $lines[] = self::label($entry) . ': ' . self::apply($package, $entry) . '.';
                        } catch (Throwable $e) {
                            $lines[] = self::label($entry) . ': BŁĄD, ' . $e->getMessage() . '. Dalsze wpisy czekają.';
                            break;
                        }
                    }
                    if ($n === 0) { $lines[] = 'Nie ma więcej wpisów gotowych do zmiany.'; }
                } else {
                    $entry = null;
                    foreach ($package['wpisy'] as $candidate) { if ($candidate['id'] === $id) { $entry = $candidate; } }
                    if (!$entry) { throw new RuntimeException('tego wpisu nie ma w pakiecie'); }
                    try {
                        $lines[] = self::label($entry) . ': ' . ($do === 'zastosuj' ? self::apply($package, $entry) : self::rollback($entry)) . '.';
                    } catch (Throwable $e) {
                        $lines[] = self::label($entry) . ': BŁĄD, ' . $e->getMessage() . '.';
                    }
                }
            }
        } catch (Throwable $e) {
            $lines[] = 'BŁĄD: ' . $e->getMessage() . '.';
        } finally {
            delete_option(self::LOCK);
        }
        self::log($lines);
        self::back();
    }

    private static function back(): void {
        wp_safe_redirect(admin_url('admin.php?page=' . self::PAGE));
        exit;
    }

    private static function button(string $do, string $label, int $id = 0, bool $primary = false, string $confirm = ''): string {
        return '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '" style="display:inline"' . ($confirm !== '' ? ' onsubmit="return confirm(' . esc_attr(wp_json_encode($confirm)) . ')"' : '') . '>'
            . wp_nonce_field(self::ACTION, '_wpnonce', true, false)
            . '<input type="hidden" name="action" value="' . esc_attr(self::ACTION) . '"><input type="hidden" name="do" value="' . esc_attr($do) . '">'
            . ($id ? '<input type="hidden" name="id" value="' . (int) $id . '">' : '')
            . '<button type="submit" class="button' . ($primary ? ' button-primary' : '') . '">' . esc_html($label) . '</button></form>';
    }

    private static function thumb(string $src, string $alt): string {
        return '<img src="' . esc_url($src) . '" alt="' . esc_attr($alt) . '" width="96" height="54" loading="lazy" decoding="async" style="width:96px;height:54px;object-fit:cover;border-radius:6px;margin:0 4px 4px 0;background:#eef0f3">';
    }

    public static function page(): void {
        if (!self::allowed()) { wp_die('Brak uprawnień.', '', ['response' => 403]); }
        $open = function_exists('zp_suite3_open');
        if ($open) { zp_suite3_open('Nowe zdjęcia wpisów', 'Trzy zdjęcia z polskimi napisami w treści i osobna okładka. Zmienia tylko zdjęcia, z kopią do cofnięcia.'); } else { echo '<div class="wrap"><h1>Nowe zdjęcia wpisów</h1>'; }
        $package = self::package();
        echo '<section class="card"><header class="card-head"><div><h2>Pakiet z kanału treści</h2><p>';
        echo $package ? esc_html('Pakiet ' . $package['pakiet'] . ', ' . count($package['wpisy']) . ' wpisów, pobrany ' . wp_date('j.m.Y H:i', (int) $package['at']) . '.') : 'Pakiet nie jest jeszcze pobrany.';
        echo '</p></div></header><p>' . self::button('pobierz', $package ? 'Pobierz pakiet ponownie' : 'Pobierz pakiet', 0, !$package);
        if ($package) { echo ' ' . self::button('nastepne', 'Zastosuj następne ' . self::STEP, 0, true, 'Zmienić zdjęcia w następnych ' . self::STEP . ' wpisach?'); }
        echo '</p></section>';

        if ($package) {
            $rows = '';
            $counts = ['gotowy' => 0, 'zrobione' => 0, 'przerwany' => 0, 'zmieniony' => 0, 'blad' => 0];
            foreach ($package['wpisy'] as $entry) {
                [$code, $text] = self::status($entry);
                $counts[$code]++;
                $post = get_post($entry['id']);
                $now = '';
                if ($post) {
                    $thumb = (int) get_post_thumbnail_id($post->ID);
                    if ($thumb > 0) { $now .= self::thumb((string) wp_get_attachment_image_url($thumb, 'medium'), 'Okładka teraz'); }
                    try { $shown = self::article($post->ID)['html']; } catch (RuntimeException $e) { $shown = (string) $post->post_content; }
                    foreach (self::photo_figures($shown) as $figure) {
                        if (preg_match('~<img\b[^>]*\bsrc\s*=\s*["\']([^"\']+)["\']~i', $figure, $m)) { $now .= self::thumb(html_entity_decode($m[1]), 'Zdjęcie w treści teraz'); }
                    }
                }
                $new = self::thumb(self::base_url() . $entry['okladka']['plik'] . '?v=' . substr($entry['okladka']['sha256'], 0, 12), $entry['okladka']['alt']);
                foreach ($entry['obrazy'] as $img) { $new .= self::thumb(self::base_url() . $img['plik'] . '?v=' . substr($img['sha256'], 0, 12), $img['alt']); }
                $link = $post ? ($post->post_status === 'publish' ? (string) get_permalink($post) : (string) get_preview_post_link($post)) : '';
                $actions = $code === 'gotowy' ? self::button('zastosuj', 'Zastosuj', $entry['id'], true)
                    : (in_array($code, ['zrobione', 'przerwany'], true) ? self::button('cofnij', 'Cofnij', $entry['id'], false, 'Przywrócić poprzednie zdjęcia tego wpisu?') : '');
                $rows .= '<tr><td><strong>' . ($link !== '' ? '<a href="' . esc_url($link) . '" target="_blank" rel="noopener">' . esc_html(self::label($entry)) . '</a>' : esc_html(self::label($entry))) . '</strong><br><small>'
                    . esc_html($post ? ($post->post_status === 'future' ? 'zaplanowany na ' : 'opublikowany ') . wp_date('j.m.Y H:i', (int) get_post_timestamp($post)) : '') . '</small></td>'
                    . '<td>' . $now . '</td><td>' . $new . '</td><td>' . esc_html($text) . '</td><td>' . $actions . '</td></tr>';
            }
            echo '<section class="card"><header class="card-head"><div><h2>Wpisy</h2><p>' . esc_html(sprintf('Gotowe do zmiany: %d, z nowymi zdjęciami: %d, przerwane: %d, zmienione później: %d, do sprawdzenia: %d.', $counts['gotowy'], $counts['zrobione'], $counts['przerwany'], $counts['zmieniony'], $counts['blad'])) . '</p></div></header>';
            echo '<div class="table-scroller"><table class="content-table widefat striped"><thead><tr><th>Wpis</th><th>Teraz</th><th>Nowe: okładka i 3 w treści</th><th>Stan</th><th></th></tr></thead><tbody>' . $rows . '</tbody></table></div></section>';
        }

        $log = get_option(self::LOG, []);
        if (is_array($log) && $log) {
            echo '<section class="card"><header class="card-head"><div><h2>Ostatnie działania</h2></div></header><ul>';
            foreach (array_slice($log, 0, 30) as $line) { echo '<li><small>' . esc_html(wp_date('j.m H:i', (int) ($line['at'] ?? 0))) . '</small> ' . esc_html((string) ($line['text'] ?? '')) . '</li>'; }
            echo '</ul></section>';
        }
        if ($open) { zp_suite3_close(); } else { echo '</div>'; }
    }
}
