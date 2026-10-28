<?php
/**
 * 3.2.37 — one-shot, owner-requested publication of the bundled Altiora case study.
 *
 * Runs on activation or the first normal, authorized wp-admin request after an
 * update. Never does database/media migration in a visitor's page render.
 * Reuses 3.2.36 drafts, preserves editorial changes, and never republishes a post
 * that was subsequently withdrawn or deleted after this migration completed.
 */
if (!defined('ABSPATH')) { exit; }

final class ZP_Altiora_Autopublish_3237 {
    public const OPTION = 'zp_altiora_autopublish_3237';
    private const LOCK = 'zp_altiora_autopublish_3237_lock';
    private const LEGACY_MARKER = '_zp_altiora_case_3236';
    private const LEGACY_OPTION = 'zp_altiora_case_draft_3236';
    private const MAX_ATTEMPTS = 3;

    public static function activate($network_wide = false): void {
        // Do not publish to every site in a multisite network without authorization.
        self::run(true);
    }

    private static function owned($post): bool {
        if (!$post || $post->post_type !== 'post') { return false; }
        return get_post_meta((int) $post->ID, ZP_Altiora_Article_3236::MARKER, true) === '1'
            || get_post_meta((int) $post->ID, self::LEGACY_MARKER, true) === '1'
            || strpos((string) $post->post_content, 'data-zp-altiora-case="3236"') !== false
            || strpos((string) $post->post_content, 'data-zp-case-study="altiora-linea"') !== false;
    }

    /** Prefer a published, marked post; never turn a slug collision into a -2 copy. */
    private static function find_post(array $data, array &$warnings) {
        $matches = [];
        foreach ([ZP_Altiora_Article_3236::OPTION, self::LEGACY_OPTION, self::OPTION] as $key) {
            $state = get_option($key, []);
            $id = is_array($state) ? (int) ($state['id'] ?? $state['post_id'] ?? 0) : 0;
            $post = $id ? get_post($id) : null;
            if (self::owned($post)) { $matches[(int) $post->ID] = $post; }
        }
        $posts = get_posts([
            'post_type' => 'post',
            'post_status' => ['publish', 'draft', 'pending', 'private', 'future', 'trash', 'auto-draft'],
            'numberposts' => -1, 'orderby' => 'ID', 'order' => 'ASC', 'suppress_filters' => true,
            'meta_query' => ['relation' => 'OR',
                ['key' => ZP_Altiora_Article_3236::MARKER, 'value' => '1'],
                ['key' => self::LEGACY_MARKER, 'value' => '1'],
            ],
        ]);
        foreach ($posts as $post) { if (self::owned($post)) { $matches[(int) $post->ID] = $post; } }
        $collisions = [];
        foreach (array_unique([$data['slug'], 'altiora-linea-strona-internetowa-branding']) as $slug) {
            $by_slug = get_posts(['name' => $slug, 'post_type' => ['post', 'page'],
                'post_status' => ['publish', 'draft', 'pending', 'private', 'future', 'trash'],
                'numberposts' => -1, 'suppress_filters' => true]);
            foreach ($by_slug as $post) {
                if (self::owned($post)) { $matches[(int) $post->ID] = $post; }
                else { $collisions[] = $post; }
            }
        }
        if (!$matches && $collisions) {
            throw new RuntimeException('Adres artykułu jest zajęty przez inny wpis. Nie nadpisano go ani nie utworzono duplikatu.');
        }
        if (!$matches) { return null; }
        usort($matches, static function ($a, $b) use ($data): int {
            $score = static fn($p): int => ($p->post_status === 'publish' ? 100 : 0)
                + ($p->post_name === $data['slug'] ? 10 : 0);
            return ($score($b) <=> $score($a)) ?: ($a->ID <=> $b->ID);
        });
        if (count($matches) > 1) {
            $warnings[] = 'Znaleziono więcej niż jeden wcześniejszy wpis tej realizacji. Użyto istniejącego wpisu; pozostałych nie usuwano.';
        }
        foreach ($collisions as $other) {
            if ($matches[0]->post_status !== 'publish' && $other->post_name === $matches[0]->post_name
                && $other->post_status !== 'trash') {
                throw new RuntimeException('Adres szkicu koliduje z innym wpisem. Nie publikowano kopii z dopiskiem -2.');
            }
        }
        return $matches[0];
    }

    /** add_option has a unique key; expired locks are removed with compare-and-delete. */
    private static function lock(): string {
        $value = ['token' => wp_generate_uuid4(), 'time' => time()];
        if (add_option(self::LOCK, $value, '', false)) { return $value['token']; }
        $old = get_option(self::LOCK, []);
        $time = is_array($old) ? (int) ($old['time'] ?? 0) : (int) $old;
        if ($time > time() - 600) { return ''; }
        global $wpdb;
        $removed = $wpdb->delete($wpdb->options, ['option_name' => self::LOCK, 'option_value' => maybe_serialize($old)]);
        if (!$removed) { return ''; }
        wp_cache_delete(self::LOCK, 'options');
        return add_option(self::LOCK, $value, '', false) ? $value['token'] : '';
    }

    private static function categories(int $id, bool $fresh): array {
        $ids = [];
        foreach (['strony-internetowe' => 'Strony internetowe', 'case-study' => 'Case study'] as $slug => $name) {
            $term = get_term_by('slug', $slug, 'category');
            if (!$term) {
                $created = wp_insert_term($name, 'category', ['slug' => $slug]);
                if (is_wp_error($created)) {
                    $term = get_term_by('slug', $slug, 'category');
                    if (!$term) { throw new RuntimeException('Nie udało się przygotować kategorii: ' . $name . '.'); }
                } else { $term = get_term((int) $created['term_id'], 'category'); }
            }
            if (!$term || is_wp_error($term)) { throw new RuntimeException('Nie można odczytać kategorii artykułu.'); }
            $ids[] = (int) $term->term_id;
        }
        $assigned = wp_set_post_terms($id, $ids, 'category', !$fresh);
        if (is_wp_error($assigned)) { throw new RuntimeException('Nie udało się przypisać artykułu do kategorii Wiedzy.'); }
        return $ids;
    }

    private static function put_if_empty(int $id, string $key, $value): void {
        $current = get_post_meta($id, $key, true);
        if ($current !== '' && $current !== []) { return; }
        update_post_meta($id, $key, wp_slash($value));
        if (get_post_meta($id, $key, true) != $value) {
            throw new RuntimeException('Nie udało się zapisać ustawienia artykułu: ' . $key . '.');
        }
    }

    private static function attach_file(string $file, int $post_id, array $data): int {
        if (!is_readable($file)) { return 0; }
        $check = wp_check_filetype(basename($file));
        if (empty($check['type']) || strpos($check['type'], 'image/') !== 0) { return 0; }
        $id = wp_insert_attachment([
            'post_title' => 'Altiora Linea — realizacja strony internetowej',
            'post_mime_type' => $check['type'], 'post_status' => 'inherit',
            'post_excerpt' => $data['pl']['image_caption'],
        ], $file, $post_id, true);
        if (is_wp_error($id) || !$id) { return 0; }
        update_post_meta((int) $id, '_zp_altiora_cover_source_3237', $data['image']);
        update_post_meta((int) $id, '_wp_attachment_image_alt', $data['pl']['image_alt']);
        if (!function_exists('wp_generate_attachment_metadata')) { require_once ABSPATH . 'wp-admin/includes/image.php'; }
        $meta = wp_generate_attachment_metadata((int) $id, $file);
        if (is_array($meta)) { wp_update_attachment_metadata((int) $id, $meta); }
        return (int) $id;
    }

    /** Reuse WP media; register the existing local upload before attempting HTTP. */
    private static function cover(int $id, array $data, array &$warnings): int {
        $thumb = (int) get_post_thumbnail_id($id);
        if ($thumb && wp_attachment_is_image($thumb)) { return $thumb; }
        $urls = array_unique([$data['image'], str_replace('-scaled.webp', '.webp', $data['image'])]);
        foreach ($urls as $url) {
            $thumb = (int) attachment_url_to_postid($url);
            if ($thumb && wp_attachment_is_image($thumb)) { break; }
            $thumb = 0;
        }
        if (!$thumb) {
            $found = get_posts(['post_type' => 'attachment', 'post_status' => 'inherit', 'fields' => 'ids',
                'numberposts' => 1, 'suppress_filters' => true,
                'meta_key' => '_zp_altiora_cover_source_3237', 'meta_value' => $data['image']]);
            if ($found && wp_attachment_is_image((int) $found[0])) { $thumb = (int) $found[0]; }
        }
        $uploads = wp_upload_dir();
        if (!$thumb && empty($uploads['error'])) {
            $base = realpath((string) $uploads['basedir']);
            foreach ($urls as $url) {
                $parts = explode('/uploads/', (string) wp_parse_url($url, PHP_URL_PATH), 2);
                if (!$base || count($parts) !== 2) { continue; }
                $relative = rawurldecode($parts[1]);
                if (strpos($relative, '..') !== false || strpos($relative, "\0") !== false) { continue; }
                $file = realpath($base . DIRECTORY_SEPARATOR . $relative);
                if (!$file || strpos($file, $base . DIRECTORY_SEPARATOR) !== 0) { continue; }
                $existing = get_posts(['post_type' => 'attachment', 'post_status' => 'inherit', 'fields' => 'ids',
                    'numberposts' => 1, 'suppress_filters' => true, 'meta_key' => '_wp_attached_file', 'meta_value' => $relative]);
                $thumb = $existing ? (int) $existing[0] : self::attach_file($file, $id, $data);
                if ($thumb) { break; }
            }
        }
        if (!$thumb && empty($uploads['error'])) {
            // Only the fixed, trusted project image from the bundled data; never request input.
            if (!function_exists('download_url')) { require_once ABSPATH . 'wp-admin/includes/file.php'; }
            if (!function_exists('media_handle_sideload')) { require_once ABSPATH . 'wp-admin/includes/media.php'; }
            if (!function_exists('wp_generate_attachment_metadata')) { require_once ABSPATH . 'wp-admin/includes/image.php'; }
            $tmp = download_url($data['image'], 12);
            if (!is_wp_error($tmp)) {
                try {
                    $result = media_handle_sideload(['name' => 'altiora-linea-realizacja.webp', 'tmp_name' => $tmp], $id,
                        'Altiora Linea — realizacja strony internetowej');
                    if (!is_wp_error($result) && $result) {
                        $thumb = (int) $result;
                        update_post_meta($thumb, '_zp_altiora_cover_source_3237', $data['image']);
                        update_post_meta($thumb, '_wp_attachment_image_alt', $data['pl']['image_alt']);
                    }
                } finally { if (is_file($tmp)) { wp_delete_file($tmp); } }
            }
        }
        if ($thumb) {
            set_post_thumbnail($id, $thumb);
            if ((int) get_post_thumbnail_id($id) === $thumb) { return $thumb; }
        }
        // The article and the Wiedza card still use the reviewed original image URL.
        self::put_if_empty($id, '_zp_blog_img', $data['image']);
        $warnings[] = 'Nie udało się zarejestrować obrazu wyróżniającego w Mediach. Artykuł i karta Wiedzy używają adresu podanej grafiki; sprawdź bibliotekę mediów.';
        return 0;
    }

    private static function metadata(int $id, array $data, array $categories, int $thumb): void {
        $c = $data['pl'];
        $image = $thumb ? (string) wp_get_attachment_url($thumb) : $data['image'];
        $meta = [
            'rank_math_title' => $c['seo_title'], 'rank_math_description' => $c['description'],
            'rank_math_focus_keyword' => $c['keywords'], 'rank_math_primary_category' => $categories[0],
            'rank_math_robots' => ['index'], 'rank_math_rich_snippet' => 'article',
            'rank_math_snippet_article_type' => 'BlogPosting',
            'rank_math_schema_Article' => [
                '@type' => 'Article', 'articleType' => 'BlogPosting', 'headline' => '%seo_title%',
                'description' => '%seo_description%', 'image' => '%post_thumbnail%',
                'datePublished' => '%date(Y-m-dTH:i:sP)%', 'dateModified' => '%modified(Y-m-dTH:i:sP)%',
                'metadata' => ['title' => 'Article', 'type' => 'template', 'isPrimary' => true],
            ],
            'rank_math_facebook_title' => $c['seo_title'], 'rank_math_facebook_description' => $c['description'],
            'rank_math_facebook_image' => $image, 'rank_math_twitter_image' => $image,
            'rank_math_twitter_card_type' => 'summary_large_image', 'rank_math_twitter_use_facebook' => 'on',
            '_zp_blog_img' => $image,
        ];
        if ($thumb) { $meta['rank_math_facebook_image_id'] = $thumb; $meta['rank_math_twitter_image_id'] = $thumb; }
        foreach ($meta as $key => $value) { self::put_if_empty($id, $key, $value); }
        $tag_ids = wp_set_post_tags($id, ['WordPress', 'Strony wielojęzyczne', 'Branża wnętrzarska', 'Realizacje'], true);
        if (is_wp_error($tag_ids)) { throw new RuntimeException('Nie udało się zapisać tagów artykułu.'); }
    }

    /** Keep only the source's article widget and its enclosing layout, not its other content. */
    private static function layout(int $id, string $html): string {
        if (get_post_meta($id, '_elementor_data', true) !== '') { return 'existing'; }
        if (!function_exists('zp_seo_articles_elementor')) { return 'native'; }
        $source = get_page_by_path('sklep-internetowy-swiat-grilli', OBJECT, 'post');
        if (!$source || $source->post_status !== 'publish') { return 'native'; }
        $notes = [];
        $raw = zp_seo_articles_elementor((int) $source->ID, $html, $notes);
        $tree = is_string($raw) ? json_decode($raw, true) : null;
        if (!is_array($tree)) { return 'native'; }
        $found = 0;
        $walk = static function (array $items) use (&$walk, &$found): array {
            $out = [];
            foreach ($items as $item) {
                if (!is_array($item)) { continue; }
                if (($item['elType'] ?? '') === 'widget') {
                    $type = $item['widgetType'] ?? '';
                    $widget_html = (string) ($item['settings']['html'] ?? '');
                    if ($type === 'html' && strpos($widget_html, 'data-zp-altiora-case="3236"') !== false) {
                        // No template-specific JS, analytics, review or duplicate JSON-LD.
                        $item['settings']['html'] = preg_replace('~<script\b[^>]*>.*?</script>~is', '', $widget_html);
                        ++$found; $out[] = $item;
                    } elseif (in_array($type, ['spacer', 'divider'], true)) { $out[] = $item; }
                } elseif (!empty($item['elements']) && is_array($item['elements'])) {
                    $item['elements'] = $walk($item['elements']);
                    if ($item['elements']) { $out[] = $item; }
                }
            }
            return $out;
        };
        $tree = $walk($tree);
        if ($found !== 1) { return 'native'; }
        foreach (['_elementor_template_type', '_elementor_version', '_elementor_pro_version', '_wp_page_template'] as $key) {
            $value = get_post_meta($source->ID, $key, true);
            if ($value !== '') { self::put_if_empty($id, $key, $value); }
        }
        $settings = get_post_meta($source->ID, '_elementor_page_settings', true);
        $settings = is_array($settings) ? $settings : [];
        $settings['hide_title'] = 'yes';
        self::put_if_empty($id, '_elementor_page_settings', $settings);
        self::put_if_empty($id, '_elementor_data', (string) wp_json_encode($tree));
        self::put_if_empty($id, '_elementor_edit_mode', 'builder');
        return 'elementor';
    }

    private static function finish(int $id, array $state): void {
        $state = array_merge($state, ['done' => true, 'id' => $id, 'time' => time()]);
        update_option(self::OPTION, $state, false);
        update_option(ZP_Altiora_Article_3236::OPTION, ['id' => $id, 'state' => 'publish', 'autopublish' => '3.2.37'], false);
        clean_post_cache($id);
        if (function_exists('zp_seo_articles_elementor_flush')) { zp_seo_articles_elementor_flush([$id]); }
        if (function_exists('zp_suite_wiedza_flush_index_cache')) { zp_suite_wiedza_flush_index_cache(); }
        do_action('zp_html_cache_purge');
    }

    public static function run(bool $activation = false): void {
        if (!is_admin() || wp_doing_cron() || (!$activation && wp_doing_ajax())) { return; }
        if (!current_user_can('manage_options') || !current_user_can('edit_posts')
            || !current_user_can('publish_posts') || !current_user_can('unfiltered_html')) { return; }
        $state = get_option(self::OPTION, []);
        $state = is_array($state) ? $state : [];
        if (!empty($state['done'])) { return; }
        if ((int) ($state['attempts'] ?? 0) >= self::MAX_ATTEMPTS) { return; }
        if (!empty($state['last_attempt']) && (int) $state['last_attempt'] > time() - 60) { return; }
        $token = self::lock();
        if ($token === '') { return; }
        $id = 0; $warnings = [];
        try {
            $state['attempts'] = (int) ($state['attempts'] ?? 0) + 1;
            $state['last_attempt'] = time();
            update_option(self::OPTION, $state, false);
            $data = ZP_Altiora_Article_3236::data();
            if (empty($data['pl']['title']) || empty($data['slug'])) { throw new RuntimeException('Brak poprawnego pliku z treścią artykułu.'); }
            $post = self::find_post($data, $warnings);
            $fresh = !$post;
            if ($post) {
                $id = (int) $post->ID;
                if (!current_user_can('edit_post', $id)) { throw new RuntimeException('Brak uprawnień do istniejącego wpisu.'); }
                if (in_array($post->post_status, ['trash', 'private', 'future'], true)) {
                    $state = array_merge($state, ['done' => true, 'id' => $id, 'state' => 'protected',
                        'message' => 'Wpis jest w koszu, prywatny lub zaplanowany. Nie zmieniono jego statusu i nie utworzono duplikatu.']);
                    update_option(self::OPTION, $state, false); return;
                }
                update_post_meta($id, ZP_Altiora_Article_3236::MARKER, '1');
                if ($post->post_status === 'publish') {
                    self::finish($id, ['state' => 'existing', 'warnings' => $warnings]); return;
                }
                $html = (string) $post->post_content;
                if (trim($html) === '') { throw new RuntimeException('Istniejący szkic ma pustą treść. Nie zastąpiono ręcznych zmian.'); }
            } else {
                $html = ZP_Altiora_Article_3236::render('pl');
                if (substr_count($html, '<h1>') !== 1 || strpos($html, 'data-zp-altiora-case="3236"') === false) {
                    throw new RuntimeException('Kontrola kompletności artykułu nie powiodła się.');
                }
                $result = wp_insert_post(wp_slash([
                    'post_type' => 'post', 'post_status' => 'draft', 'post_name' => $data['slug'],
                    'post_title' => $data['pl']['title'], 'post_content' => $html,
                    'post_excerpt' => $data['pl']['excerpt'], 'post_author' => get_current_user_id(),
                    'comment_status' => 'closed', 'ping_status' => 'closed',
                    'meta_input' => [ZP_Altiora_Article_3236::MARKER => '1', '_zp_altiora_autopublish_3237' => '1'],
                ]), true);
                if (is_wp_error($result) || !$result) { throw new RuntimeException('Nie udało się zapisać artykułu w bazie WordPressa.'); }
                $id = (int) $result;
            }
            // Persist the ID before media preparation: a retry always resumes this post.
            $state['id'] = $id;
            update_option(self::OPTION, $state, false);
            update_option(ZP_Altiora_Article_3236::OPTION, ['id' => $id, 'state' => 'preparing'], false);
            $categories = self::categories($id, $fresh);
            $layout = $fresh ? self::layout($id, $html) : 'preserved';
            $thumb = self::cover($id, $data, $warnings);
            self::metadata($id, $data, $categories, $thumb);
            $update = ['ID' => $id, 'post_status' => 'publish'];
            if (trim((string) get_post_field('post_excerpt', $id)) === '') { $update['post_excerpt'] = $data['pl']['excerpt']; }
            $published = wp_update_post(wp_slash($update), true);
            if (is_wp_error($published) || !$published || get_post_status($id) !== 'publish') {
                throw new RuntimeException('WordPress nie potwierdził publikacji. Przygotowany wpis pozostaje do ponowienia; nie powstanie drugi.');
            }
            update_post_meta($id, '_zp_altiora_published_by_3237', current_time('mysql'));
            self::finish($id, ['state' => $fresh ? 'published' : 'published_draft', 'layout' => $layout,
                'thumbnail_id' => $thumb, 'warnings' => $warnings]);
        } catch (Throwable $error) {
            $state = array_merge($state, ['done' => false, 'state' => 'error', 'id' => $id,
                'message' => $error->getMessage(), 'warnings' => $warnings]);
            update_option(self::OPTION, $state, false);
        } finally {
            $lock = get_option(self::LOCK, []);
            if (is_array($lock) && ($lock['token'] ?? '') === $token) { delete_option(self::LOCK); }
        }
    }

    public static function notice(): void {
        if (!current_user_can('manage_options')) { return; }
        $screen = function_exists('get_current_screen') ? get_current_screen() : null;
        if ($screen && !in_array($screen->base, ['plugins', 'dashboard', 'edit', 'update', 'plugin-install'], true)) { return; }
        $state = get_option(self::OPTION, []);
        if (!is_array($state) || !$state) { return; }
        $id = (int) ($state['id'] ?? 0);
        $status = $id ? get_post_status($id) : false;
        $ok = !empty($state['done']) && $status === 'publish';
        $warning = !$ok || !empty($state['warnings']);
        $message = $ok ? 'Artykuł Altiora Linea jest opublikowany w Wiedzy. Import zakończony; nie utworzono dodatkowej kopii.'
            : (string) ($state['message'] ?? 'Artykuł Altiora Linea nie jest obecnie opublikowany. Wtyczka nie zmienia ponownie jego statusu.');
        echo '<div class="notice notice-' . ($warning ? 'warning' : 'success') . ' is-dismissible"><p><strong>Zaprojektowani Suite 3.2.37:</strong> ' . esc_html($message);
        if ($ok) { echo ' <a href="' . esc_url(get_permalink($id)) . '">Zobacz artykuł</a>'; }
        if ($id && current_user_can('edit_post', $id)) { echo ' · <a href="' . esc_url(get_edit_post_link($id, '')) . '">Edytuj wpis</a>'; }
        foreach ((array) ($state['warnings'] ?? []) as $text) { echo '<br>' . esc_html((string) $text); }
        if (($state['state'] ?? '') === 'error' && (int) ($state['attempts'] ?? 0) < self::MAX_ATTEMPTS) {
            echo '<br>Próba zostanie automatycznie ponowiona przy kolejnym wejściu do panelu po upływie minuty.';
        }
        echo '</p></div>';
    }
}
register_activation_hook(ZP_SUITE_PATH . 'zaprojektowani-suite.php', [ZP_Altiora_Autopublish_3237::class, 'activate']);
