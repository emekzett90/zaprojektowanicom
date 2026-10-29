<?php
/** Native future-post calendar. Read-only: no cron, schedule, content or metadata writes. */
if (!defined('ABSPATH')) { exit; }

final class ZP_Admin_Calendar_20261009 {
    private const PREFIX = '_zp_cb_20261009_';
    private const BATCH = 'senuto-20261009-v1';

    public static function date(string $gmt, string $local = ''): ?DateTimeImmutable {
        foreach ([[$gmt, new DateTimeZone('UTC')], [$local, function_exists('wp_timezone') ? wp_timezone() : new DateTimeZone('UTC')]] as $candidate) {
            if ($candidate[0] === '' || $candidate[0] === '0000-00-00 00:00:00') { continue; }
            $value = DateTimeImmutable::createFromFormat('!Y-m-d H:i:s', $candidate[0], $candidate[1]);
            $errors = DateTimeImmutable::getLastErrors();
            if ($value && (!$errors || (!$errors['warning_count'] && !$errors['error_count']))) {
                return $value->setTimezone(new DateTimeZone('Europe/Warsaw'));
            }
        }
        return null;
    }

    /** Inspect saved HTML widgets without rendering Elementor or the_content filters. */
    public static function content($elementor, string $post_content): array {
        $tree = is_array($elementor) ? $elementor : (is_string($elementor) && strlen($elementor) <= 8000000 ? json_decode($elementor, true) : null);
        $articles = []; $visited = 0;
        $walk = static function (array $elements, int $depth = 0) use (&$walk, &$articles, &$visited): void {
            if ($depth > 25) { return; }
            foreach ($elements as $element) {
                if (++$visited > 5000 || !is_array($element)) { continue; }
                if (($element['widgetType'] ?? '') === 'html' && is_string($element['settings']['html'] ?? null)) {
                    $html = $element['settings']['html'];
                    if (preg_match('~<article\b[^>]*\bclass\s*=\s*[\"\'][^\"\']*\bzpArticleNew\b~i', $html)) { $articles[] = $html; }
                }
                if (is_array($element['elements'] ?? null)) { $walk($element['elements'], $depth + 1); }
            }
        };
        if (is_array($tree)) { $walk($tree); }
        $html = count($articles) === 1 ? $articles[0] : (count($articles) === 0 ? $post_content : '');
        $clean = preg_replace('~<(script|style)\b[^>]*>.*?</\1\s*>~is', ' ', $html);
        $plain = html_entity_decode((string) preg_replace('~<[^>]*>~s', ' ', (string) $clean), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $plain = trim((string) preg_replace('/[^\p{L}\p{N}]+/u', ' ', $plain));
        $words = $plain === '' ? null : count(explode(' ', $plain));
        preg_match_all('~<h2\b[^>]*>(.*?)</h2\s*>~is', (string) $clean, $heading_matches);
        $headings = array_map(static function ($v) { return trim(html_entity_decode(strip_tags($v), ENT_QUOTES | ENT_HTML5, 'UTF-8')); }, $heading_matches[1]);
        preg_match_all('~<details\b[^>]*\bclass\s*=\s*[\"\'][^\"\']*\bzpArticleNewFAQ\b~i', (string) $clean, $faq_matches);
        $images = [];
        // Source is native saved content; only plain attributes are inspected, never emitted as HTML.
        if (class_exists('DOMDocument') && $clean !== '') {
            $doc = new DOMDocument(); $previous = libxml_use_internal_errors(true);
            $loaded = $doc->loadHTML('<?xml encoding="UTF-8">' . $clean, LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING);
            libxml_clear_errors(); libxml_use_internal_errors($previous);
            if ($loaded) {
                foreach ($doc->getElementsByTagName('img') as $image) {
                    $src = $image->getAttribute('src');
                    if ($src !== '' && !isset($images[$src])) { $images[$src] = ['url' => $src, 'alt' => $image->getAttribute('alt')]; }
                }
            }
        }
        return ['words' => $words, 'headings' => $headings, 'faq_count' => count($faq_matches[0]), 'html_widgets' => count($articles), 'ambiguous' => count($articles) > 1, 'images' => array_slice(array_values($images), 0, 3)];
    }

    private static function phrase(string $value): string {
        $value = trim((string) preg_replace('/\s+/u', ' ', $value));
        return function_exists('mb_strtolower') ? mb_strtolower($value, 'UTF-8') : strtolower($value);
    }

    /** Whitelisted read-only GET filters; malformed values never alter the native schedule. */
    public static function filters(array $query): array {
        $string = static function (string $key) use ($query): string { return isset($query[$key]) && is_string($query[$key]) ? trim($query[$key]) : ''; };
        $search = trim((string) preg_replace('/\s+/u', ' ', strip_tags($string('zp_cal_q'))));
        $search = function_exists('mb_substr') ? mb_substr($search, 0, 160, 'UTF-8') : substr($search, 0, 160);
        $month = $string('zp_cal_month');
        if (!preg_match('/^(\d{4})-(\d{2})$/D', $month, $parts) || !checkdate((int) ($parts[2] ?? 0), 1, (int) ($parts[1] ?? 0))) { $month = ''; }
        $source = $string('zp_cal_source');
        if (!in_array($source, ['batch', 'feed', 'prepare', 'native'], true)) { $source = ''; }
        $category = $string('zp_cal_category');
        return ['q' => $search, 'month' => $month, 'source' => $source, 'category' => preg_match('/^[1-9]\d{0,8}$/D', $category) ? (int) $category : 0, 'attention' => $string('zp_cal_attention') === '1'];
    }

    public static function filter_records(array $rows, array $filters): array {
        $needle = self::phrase((string) ($filters['q'] ?? ''));
        return array_values(array_filter($rows, static function (array $row) use ($filters, $needle): bool {
            if (($filters['source'] ?? '') !== '' && ($row['source'] ?? '') !== $filters['source']) { return false; }
            if (($filters['month'] ?? '') !== '' && (!(($row['date'] ?? null) instanceof DateTimeInterface) || $row['date']->format('Y-m') !== $filters['month'])) { return false; }
            if (!empty($filters['category']) && !in_array((int) $filters['category'], (array) ($row['category_ids'] ?? []), true)) { return false; }
            if (!empty($filters['attention']) && empty($row['readiness']['attention'])) { return false; }
            $text = self::phrase((string) ($row['title'] ?? '') . ' ' . implode(' ', (array) ($row['keywords'] ?? [])));
            return $needle === '' || strpos($text, $needle) !== false;
        }));
    }

    /** Exact matching is a review signal, not evidence of cannibalization or a reason to rewrite content. */
    public static function duplicate_groups(array $inventory): array {
        $groups = [];
        foreach ($inventory as $item) {
            if (!is_array($item) || !in_array($item['post_status'] ?? '', ['publish', 'future', 'draft'], true) || !in_array($item['post_type'] ?? '', ['post', 'page'], true)) { continue; }
            $id = (int) ($item['ID'] ?? 0); $phrase = self::phrase(explode(',', (string) ($item['meta_value'] ?? ''))[0]);
            if ($id <= 0 || $phrase === '') { continue; }
            $groups[$phrase][$id] = ['id' => $id, 'title' => (string) ($item['post_title'] ?? ''), 'type' => $item['post_type'], 'status' => $item['post_status']];
        }
        return $groups;
    }

    private static function duplicate_inventory(): array {
        global $wpdb;
        if (!isset($wpdb) || !is_callable([$wpdb, 'get_results'])) { return ['available' => false, 'limited' => false, 'groups' => []]; }
        // Read only focus fields, avoiding hundreds of large Elementor metadata trees.
        $items = $wpdb->get_results("SELECT p.ID, p.post_title, p.post_type, p.post_status, pm.meta_value FROM {$wpdb->posts} p INNER JOIN {$wpdb->postmeta} pm ON pm.post_id = p.ID AND pm.meta_key = 'rank_math_focus_keyword' WHERE p.post_type IN ('post','page') AND p.post_status IN ('publish','future','draft') AND p.post_password = '' AND NOT EXISTS (SELECT 1 FROM {$wpdb->postmeta} r WHERE r.post_id = p.ID AND r.meta_key = 'rank_math_robots' AND r.meta_value LIKE '%noindex%') ORDER BY p.ID ASC LIMIT 501", ARRAY_A);
        if (!is_array($items) || !empty($wpdb->last_error)) { return ['available' => false, 'limited' => false, 'groups' => []]; }
        return ['available' => true, 'limited' => count($items) > 500, 'groups' => self::duplicate_groups(array_slice($items, 0, 500))];
    }

    /** Information and warnings only: no arbitrary SEO score, word-count target or publication block. */
    public static function readiness(array $row, ?int $now = null): array {
        $items = []; $add = static function (string $code, string $label, string $level = 'info') use (&$items): void { $items[] = ['code' => $code, 'label' => $label, 'level' => $level]; };
        if (($row['seo_title'] ?? '') === '') { $add('title_fallback', 'Brak własnego title — tytuł wpisu / ustawienia Rank Math.'); }
        if (($row['seo_description'] ?? '') === '') { $add('description_fallback', 'Brak własnego opisu — zajawka / ustawienia Rank Math.'); }
        if (($row['primary'] ?? '') === '') { $add('missing_focus', 'Nie ustawiono głównej frazy w Rank Math.', 'warn'); }
        if (in_array('noindex', (array) ($row['robots'] ?? []), true)) { $add('noindex', 'Zapisano noindex — sprawdź zamierzony zakres indeksowania.', 'warn'); }
        if (empty($row['thumbnail_id']) || empty($row['thumbnail_url'])) { $add('missing_cover', 'Okładka wpisu nie jest dostępna w bibliotece.', 'warn'); }
        elseif (($row['thumbnail_alt'] ?? '') === '') { $add('cover_alt', 'Nie zapisano własnego opisu alt okładki — sprawdź zdjęcie.'); }
        if (!empty($row['content']['ambiguous'])) { $add('ambiguous_content', 'Kilka widgetów artykułu — odczyt treści jest niejednoznaczny.', 'warn'); }
        elseif (($row['content']['words'] ?? null) === null) { $add('unreadable_content', 'Brak jednoznacznego odczytu zapisanej treści.', 'warn'); }
        if (!(($row['date'] ?? null) instanceof DateTimeInterface)) { $add('invalid_date', 'Brak poprawnego terminu publikacji.', 'warn'); }
        elseif ($row['date']->getTimestamp() < ($now ?? time()) - 300) { $add('overdue', 'Termin minął ponad 5 minut temu; wpis nadal jest zaplanowany.', 'warn'); }
        if (!empty($row['publish_job_checked']) && empty($row['publish_job_at'])) { $add('missing_publish_job', 'Nie znaleziono zadania publikacji WordPressa dla tego wpisu.', 'warn'); }
        if (!empty($row['duplicates'])) { $add('shared_focus', 'Ta sama główna fraza jest zapisana także przy innych treściach — sprawdź intencję.', 'warn'); }
        return ['items' => $items, 'attention' => (bool) array_filter($items, static fn($item) => $item['level'] === 'warn')];
    }

    /** Getter contract: cached GSC only. A focus phrase is not evidence that this future URL ranks. */
    public static function position(string $phrase): array {
        $empty = ['matched' => false, 'position' => null, 'query' => null, 'as_of' => null, 'period' => null, 'status' => 'unavailable'];
        if ($phrase === '' || !current_user_can('manage_options')) { return $empty; }
        try {
            if (!function_exists('zp_admin_stats_keyword_position')) { return $empty; }
            $metric = zp_admin_stats_keyword_position($phrase);
        } catch (Throwable $error) { return $empty; }
        if (!is_array($metric) || empty($metric['matched']) || !is_numeric($metric['position'] ?? null) || (float) $metric['position'] <= 0 || !is_finite((float) $metric['position'])) { return $empty; }
        if (!is_string($metric['query'] ?? null) || self::phrase($metric['query']) !== self::phrase($phrase)) { return $empty; }
        $date = $metric['date'] ?? $metric['as_of'] ?? null;
        if (!is_string($date) || !preg_match('/^\d{4}-\d{2}-\d{2}$/D', $date) || !checkdate((int) substr($date, 5, 2), (int) substr($date, 8, 2), (int) substr($date, 0, 4))) { return $empty; }
        $period = $metric['period'] ?? null;
        if (!in_array($period, ['day', '28_days'], true)) {
            $period = isset($metric['period_start']) && $metric['period_start'] === $date ? 'day' : '28_days';
        }
        return array_merge($metric, ['matched' => true, 'position' => (float) $metric['position'], 'as_of' => $date, 'period' => $period]);
    }

    public static function records(): array {
        if (!current_user_can('manage_options')) { return []; }
        static $cache = null;
        if ($cache !== null) { return $cache; }
        $query = new WP_Query(['post_type' => 'post', 'post_status' => 'future', 'posts_per_page' => -1, 'orderby' => ['date' => 'ASC', 'ID' => 'ASC'], 'no_found_rows' => true, 'suppress_filters' => true, 'update_post_meta_cache' => true, 'update_post_term_cache' => true]);
        $cache = [];
        foreach ($query->posts as $post) {
            if (!$post instanceof WP_Post || $post->post_status !== 'future') { continue; }
            $id = (int) $post->ID; $focus = get_post_meta($id, 'rank_math_focus_keyword', true); $focus = is_string($focus) ? $focus : '';
            $keywords = array_values(array_filter(array_map('trim', explode(',', $focus)), static function ($v) { return $v !== ''; }));
            $primary = $keywords[0] ?? ''; $batch = get_post_meta($id, self::PREFIX . 'batch', true);
            $feed_slug = get_post_meta($id, '_zp_feed_slug', true);
            $prepared_job = get_post_meta($id, '_zp_prepared_20261010_job', true);
            $source = is_string($prepared_job) && $prepared_job !== '' ? 'prepare' : ($batch === self::BATCH ? 'batch' : (is_string($feed_slug) && $feed_slug !== '' ? 'feed' : 'native'));
            $content = self::content(get_post_meta($id, '_elementor_data', true), (string) $post->post_content);
            $date = self::date((string) $post->post_date_gmt, (string) $post->post_date);
            $modified = self::date((string) $post->post_modified_gmt, (string) $post->post_modified);
            $terms = get_the_category($id); $categories = []; $category_ids = []; $category_labels = [];
            foreach (is_array($terms) ? $terms : [] as $term) {
                if (is_string($term->name ?? null)) {
                    $categories[] = $term->name;
                    if ((int) ($term->term_id ?? 0) > 0) { $category_ids[] = (int) $term->term_id; $category_labels[(int) $term->term_id] = $term->name; }
                }
            }
            $url = (string) get_permalink($id);
            if (function_exists('get_sample_permalink')) {
                $sample = get_sample_permalink($id, $post->post_title, $post->post_name);
                if (is_array($sample) && is_string($sample[0] ?? null) && is_string($sample[1] ?? null)) { $url = str_replace(['%postname%', '%pagename%'], $sample[1], $sample[0]); }
            }
            $thumbnail = (int) get_post_thumbnail_id($id);
            $volume = get_post_meta($id, $source === 'prepare' ? '_zp_prepared_20261010_volume' : self::PREFIX . 'research_volume', true);
            $research_date = $source === 'prepare' ? get_post_meta($id, '_zp_prepared_20261010_research_date', true) : '2026-10-09';
            if (!is_string($research_date) || !preg_match('/^\d{4}-\d{2}-\d{2}$/D', $research_date) || !checkdate((int) substr($research_date, 5, 2), (int) substr($research_date, 8, 2), (int) substr($research_date, 0, 4))) { $research_date = ''; }
            $alt = get_post_meta($id, self::PREFIX . 'featured_alt', true);
            if (!is_string($alt) || trim($alt) === '') { $alt = $thumbnail ? get_post_meta($thumbnail, '_wp_attachment_image_alt', true) : ''; }
            $job = function_exists('wp_next_scheduled') ? wp_next_scheduled('publish_future_post', [$id]) : false;
            $cache[] = ['id' => $id, 'title' => (string) $post->post_title, 'status' => 'future', 'date' => $date, 'modified' => $modified, 'source' => $source, 'keywords' => $keywords, 'primary' => $primary, 'categories' => $categories, 'category_ids' => $category_ids, 'category_labels' => $category_labels, 'url' => $url,
                'preview' => current_user_can('edit_post', $id) ? (string) get_preview_post_link($id) : '', 'edit' => current_user_can('edit_post', $id) ? (string) get_edit_post_link($id, 'raw') : '',
                'thumbnail_id' => $thumbnail, 'thumbnail_url' => $thumbnail ? (string) wp_get_attachment_image_url($thumbnail, 'medium') : '', 'thumbnail_alt' => is_string($alt) ? trim($alt) : '',
                'seo_title' => is_string(get_post_meta($id, 'rank_math_title', true)) ? trim(get_post_meta($id, 'rank_math_title', true)) : '', 'seo_description' => is_string(get_post_meta($id, 'rank_math_description', true)) ? trim(get_post_meta($id, 'rank_math_description', true)) : '',
                'robots' => get_post_meta($id, 'rank_math_robots', true), 'publish_job_checked' => function_exists('wp_next_scheduled'), 'publish_job_at' => $job && (int) $job > 0 ? (int) $job : null,
                'volume' => in_array($source, ['batch', 'prepare'], true) && is_numeric($volume) && is_finite((float) $volume) && (float) $volume >= 0 && floor((float) $volume) === (float) $volume ? (int) $volume : null, 'research_date' => $research_date, 'photo_refresh' => get_post_meta($id, self::PREFIX . 'photo_refresh', true) === 'media-20261009-v1', 'content' => $content, 'position' => self::position($primary)];
        }
        $duplicates = $cache ? self::duplicate_inventory() : ['available' => true, 'limited' => false, 'groups' => []];
        foreach ($cache as &$row) {
            $matches = $duplicates['groups'][self::phrase($row['primary'])] ?? []; unset($matches[$row['id']]);
            $row['duplicates'] = array_slice(array_values($matches), 0, 10);
            $row['duplicate_check'] = ['available' => $duplicates['available'], 'limited' => $duplicates['limited']];
            $row['readiness'] = self::readiness($row);
        }
        unset($row);
        usort($cache, static function ($a, $b) { return (($a['date'] ? $a['date']->getTimestamp() : PHP_INT_MAX) <=> ($b['date'] ? $b['date']->getTimestamp() : PHP_INT_MAX)) ?: ($a['id'] <=> $b['id']); });
        return $cache;
    }

    private static function source_label(string $source): string {
        return ['batch' => 'Pakiet Senuto', 'feed' => 'Plan z kanału', 'prepare' => 'Przygotuj nowe treści · OpenAI', 'native' => 'WordPress'][$source] ?? 'WordPress';
    }

    private static function safe_image(string $url): bool {
        $parts = wp_parse_url($url); $site = wp_parse_url(home_url('/'));
        return is_array($parts) && is_array($site) && in_array($parts['scheme'] ?? '', ['http', 'https'], true) && strtolower($parts['host'] ?? '') === strtolower($site['host'] ?? '') && strpos($parts['path'] ?? '', '/wp-content/uploads/') !== false;
    }

    private static function metric_html(array $metric): string {
        if (empty($metric['matched'])) { return '<span class="zpAdmin20__muted">—<br>Brak danych dla frazy</span>'; }
        $period = $metric['period'] === 'day' ? 'średnia 1 dnia' : 'średnia 28 dni';
        return '<strong>' . esc_html(number_format_i18n($metric['position'], 1)) . '</strong><br><span class="zpAdmin20__muted">' . esc_html($period . ' · ' . $metric['as_of']) . '</span>';
    }

    private static function filter_form(array $rows, array $filters): void {
        $months = []; $categories = [];
        foreach ($rows as $row) {
            if ($row['date'] instanceof DateTimeInterface) { $months[$row['date']->format('Y-m')] = wp_date('F Y', $row['date']->getTimestamp(), new DateTimeZone('Europe/Warsaw')); }
            foreach ((array) ($row['category_labels'] ?? []) as $id => $label) { $categories[$id] = $label; }
        }
        ksort($months); asort($categories);
        echo '<section class="zpAdmin20__card"><h2>Znajdź publikację</h2><form method="get" class="zpAdmin20__filters" action="' . esc_url(admin_url('admin.php')) . '"><input type="hidden" name="page" value="zp-suite-calendar"><label for="zp-cal-search">Tytuł lub fraza</label><input id="zp-cal-search" type="search" name="zp_cal_q" maxlength="160" value="' . esc_attr($filters['q']) . '" placeholder="np. logo, WordPress"><label for="zp-cal-month">Miesiąc</label><select name="zp_cal_month" id="zp-cal-month"><option value="">Wszystkie miesiące</option>';
        foreach ($months as $value => $label) { echo '<option value="' . esc_attr($value) . '"' . ($value === $filters['month'] ? ' selected' : '') . '>' . esc_html($label) . '</option>'; }
        echo '</select><label for="zp-cal-category">Kategoria</label><select name="zp_cal_category" id="zp-cal-category"><option value="">Wszystkie kategorie</option>';
        foreach ($categories as $id => $label) { echo '<option value="' . (int) $id . '"' . ((int) $id === $filters['category'] ? ' selected' : '') . '>' . esc_html($label) . '</option>'; }
        echo '</select><label for="zp-cal-source">Źródło</label><select name="zp_cal_source" id="zp-cal-source"><option value="">Wszystkie źródła</option>';
        foreach (['batch' => 'Pakiet Senuto', 'feed' => 'Plan z kanału', 'prepare' => 'Przygotuj nowe treści · OpenAI', 'native' => 'WordPress'] as $value => $label) { echo '<option value="' . esc_attr($value) . '"' . ($value === $filters['source'] ? ' selected' : '') . '>' . esc_html($label) . '</option>'; }
        echo '</select><label class="zpAdmin20__check"><input type="checkbox" name="zp_cal_attention" value="1"' . ($filters['attention'] ? ' checked' : '') . '> Tylko wymagające sprawdzenia</label><button class="button button-primary">Filtruj</button><a class="button" href="' . esc_url(admin_url('admin.php?page=zp-suite-calendar')) . '">Wyczyść filtry</a></form></section>';
    }

    private static function readiness_html(array $row, bool $detail = false): string {
        $check = $row['readiness'];
        $warnings = count(array_filter($check['items'], static fn($item) => $item['level'] === 'warn'));
        $out = '<span class="zpAdmin20__status' . ($warnings ? ' is-warn' : ' is-ok') . '">' . esc_html($warnings ? 'Do sprawdzenia: ' . $warnings : 'Brak alertów') . '</span>';
        if (!$detail) { return $out; }
        $out .= '<ul class="zpAdmin20__muted">';
        foreach ($check['items'] as $item) { $out .= '<li>' . esc_html($item['label']) . '</li>'; }
        if (!$check['items']) { $out .= '<li>Nie znaleziono braków w sprawdzanych polach.</li>'; }
        $out .= '</ul>';
        if ($row['publish_job_checked']) { $out .= '<p class="zpAdmin20__muted">Zadanie publikacji WordPressa: ' . esc_html($row['publish_job_at'] ? wp_date('d.m.Y H:i T', $row['publish_job_at'], new DateTimeZone('Europe/Warsaw')) : 'nie znaleziono') . '.</p>'; }
        if ($row['duplicates']) {
            $out .= '<p class="zpAdmin20__muted">Wspólna główna fraza (dokładne dopasowanie):</p><ul>';
            foreach ($row['duplicates'] as $match) {
                $label = '#' . $match['id'] . ' — ' . $match['title'] . ' (' . (['publish' => 'opublikowany', 'future' => 'zaplanowany', 'draft' => 'szkic'][$match['status']] ?? $match['status']) . ')';
                $edit = current_user_can('edit_post', $match['id']) ? get_edit_post_link($match['id'], 'raw') : '';
                $out .= '<li>' . ($edit ? '<a href="' . esc_url($edit) . '">' . esc_html($label) . '</a>' : esc_html($label)) . '</li>';
            }
            $out .= '</ul><p class="zpAdmin20__muted">Wspólna fraza nie dowodzi konfliktu SEO. Sprawdź intencję treści przed ewentualną zmianą.</p>';
        }
        return $out;
    }

    private static function timeline(): void {
        $events = [];
        $import = get_option('zp_cb_20261009_results', []);
        if (is_array($import) && ($import['batch'] ?? '') === self::BATCH && isset($import['at_utc'])) { $events[] = ['at' => $import['at_utc'], 'text' => 'Pakiet Senuto: ' . count(array_unique(array_map('intval', (array) ($import['created_ids'] ?? [])))) . ' zapisanych wpisów · ' . (['complete' => 'gotowy', 'partial' => 'częściowo przygotowany', 'error' => 'wymaga sprawdzenia'][$import['status'] ?? ''] ?? 'zapisany')]; }
        $photos = get_option('zp_cb_20261009_photo_results', []);
        if (is_array($photos) && isset($photos['at_utc'])) { $events[] = ['at' => $photos['at_utc'], 'text' => 'Zdjęcia z biblioteki: ' . count(array_unique(array_map('intval', (array) ($photos['changed_ids'] ?? [])))) . ' zmienionych wpisów' . (!empty($photos['error']) ? ' · kontrola wymaga sprawdzenia' : '')]; }
        echo '<section class="zpAdmin20__card"><h2>Historia pakietu</h2>';
        if (!$events) { echo '<p class="zpAdmin20__muted">Brak zapisanych zdarzeń pakietu.</p>'; }
        foreach ($events as $event) {
            $date = DateTimeImmutable::createFromFormat('!Y-m-d\TH:i:sP', (string) $event['at']); $errors = DateTimeImmutable::getLastErrors();
            $label = $date && (!$errors || (!$errors['warning_count'] && !$errors['error_count'])) ? $date->setTimezone(new DateTimeZone('Europe/Warsaw'))->format('d.m.Y H:i T') : 'Brak daty';
            echo '<p><strong>' . esc_html($label) . '</strong><br>' . esc_html($event['text']) . '</p>';
        }
        $log = get_option('zp_feed_log', []);
        if (is_array($log) && $log) {
            echo '<details><summary>Ostatnie zdarzenia kanału publikacji</summary><ul>';
            foreach (array_slice(array_reverse($log), 0, 12) as $line) { if (is_string($line)) { echo '<li>' . esc_html($line) . '</li>'; } }
            echo '</ul></details>';
        }
        echo '</section>';
    }

    public static function render(): void {
        if (!current_user_can('manage_options')) { wp_die('Brak uprawnień.'); return; }
        $all_rows = self::records(); $filters = self::filters($_GET);
        $rows = self::filter_records($all_rows, $filters);
        $shared = function_exists('zp_admin20_open') && function_exists('zp_admin20_close');
        if ($shared) { zp_admin20_open('Kalendarz publikacji', 'calendar', 'Terminy, podglądy i frazy zaplanowanych artykułów.'); }
        else { echo '<div class="wrap zpAdmin20"><h1>Kalendarz publikacji</h1>'; }
        echo '<div data-zp-calendar-root>';
        echo '<style>.zpAdmin20 .zpCalGrid{display:grid;grid-template-columns:repeat(7,minmax(0,1fr));gap:8px}.zpAdmin20 .zpCalDay{min-height:82px;border:1px solid #e4e6ef;border-radius:12px;padding:10px;background:#fff}.zpAdmin20 .zpCalDay--empty{background:#f8f9fc}.zpAdmin20 .zpCalDay--scheduled{border-color:#c4c6f4;background:#f6f5ff}.zpAdmin20 .zpCalWeekday{font-weight:600;color:#626a7a;text-align:center;font-size:12px}.zpAdmin20 .zpCalNumber{font-weight:700;color:#4c5570;margin-bottom:6px}.zpAdmin20 .zpCalItem{margin-top:8px;padding-top:8px;border-top:1px solid #e3e2f5}.zpAdmin20 .zpCalItem a{font-size:12px;line-height:1.45;display:block;text-decoration:none}.zpAdmin20 .zpCalItem small{display:block;font-size:11px;line-height:1.4;color:#626a7a;margin-top:5px}.zpAdmin20 .zpCalDetails{scroll-margin-top:45px;margin:12px 0}.zpAdmin20 .zpCalDetails summary{cursor:pointer;font-weight:600;line-height:1.6}.zpAdmin20 .zpCalMeta{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px;margin:18px 0}.zpAdmin20 .zpCalMeta dt{font-size:12px;color:#626a7a}.zpAdmin20 .zpCalMeta dd{margin:5px 0 16px;overflow-wrap:anywhere}.zpAdmin20 .zpCalPhotos{display:flex;flex-wrap:wrap;gap:12px}.zpAdmin20 .zpCalPhotos figure{margin:0;width:180px}.zpAdmin20 .zpCalPhotos img{width:180px;height:125px;object-fit:contain;background:#f5f6fb;border-radius:10px}.zpAdmin20 .zpCalPhotos figcaption{font-size:12px;color:#626a7a;margin-top:5px}.zpAdmin20 .zpCalActions{display:flex;flex-wrap:wrap;gap:8px;margin:16px 0}.zpAdmin20 .zpCalOverflow{overflow:auto}.zpAdmin20 .zpCalSummary{font-size:28px;font-weight:700;display:block}.zpAdmin20 .zpCalTable th,.zpAdmin20 .zpCalTable td{vertical-align:top}.zpAdmin20 .zpCalTable td:first-child{white-space:nowrap}@media(max-width:900px){.zpAdmin20 .zpCalGrid{grid-template-columns:1fr}.zpAdmin20 .zpCalWeekday,.zpAdmin20 .zpCalDay--empty{display:none}.zpAdmin20 .zpCalDay{min-height:0}.zpAdmin20 .zpCalMeta{grid-template-columns:1fr}}</style>';
        $counts = array_count_values(array_column($all_rows, 'source'));
        echo '<div class="zpAdmin20__grid">';
        foreach (['Wszystkie zaplanowane' => count($all_rows), 'Pakiet Senuto' => $counts['batch'] ?? 0, 'Plan z kanału' => $counts['feed'] ?? 0] as $label => $count) { echo '<div class="zpAdmin20__card"><span class="zpCalSummary">' . (int) $count . '</span>' . esc_html($label) . '</div>'; }
        echo '</div><p class="zpAdmin20__muted">Terminy pochodzą z WordPressa i są pokazane w strefie Europe/Warsaw. Otwórz podgląd lub edycję, aby przejrzeć konkretny wpis.</p>';
        self::filter_form($all_rows, $filters);
        echo '<p class="zpAdmin20__muted">Widoczne: ' . (int) count($rows) . ' z ' . (int) count($all_rows) . ' zaplanowanych. Kontrola przygotowania jest informacyjna; nie zmienia terminów, statusu ani treści.</p>';
        if ($all_rows && empty($all_rows[0]['duplicate_check']['available'])) { echo '<p class="zpAdmin20__muted">Kontrola wspólnych fraz jest obecnie niedostępna; nie przyjęto zerowego wyniku.</p>'; }
        elseif ($all_rows && !empty($all_rows[0]['duplicate_check']['limited'])) { echo '<p class="zpAdmin20__muted">Kontrola wspólnych fraz obejmuje pierwsze 500 rekordów z zapisaną frazą. Część treści może być poza tym zakresem.</p>'; }
        if (!$rows) { echo '<section class="zpAdmin20__card"><h2>' . ($all_rows ? 'Brak wyników dla filtrów' : 'Brak zaplanowanych wpisów') . '</h2><p>' . ($all_rows ? 'Zmień lub wyczyść filtry, aby zobaczyć inne artykuły.' : 'Kalendarz pokaże wpisy ze statusem „Zaplanowany”.') . '</p></section>'; }
        $months = [];
        foreach ($rows as $row) { if ($row['date']) { $months[$row['date']->format('Y-m')][$row['date']->format('j')][] = $row; } }
        foreach ($months as $month => $days) {
            $start = new DateTimeImmutable($month . '-01', new DateTimeZone('Europe/Warsaw'));
            echo '<section class="zpAdmin20__card"><h2>' . esc_html(wp_date('F Y', $start->getTimestamp(), new DateTimeZone('Europe/Warsaw'))) . '</h2><div class="zpCalGrid" role="list" aria-label="' . esc_attr('Publikacje ' . $month) . '">';
            foreach (['Pon', 'Wt', 'Śr', 'Czw', 'Pt', 'Sob', 'Nd'] as $weekday) { echo '<div class="zpCalWeekday" aria-hidden="true">' . esc_html($weekday) . '</div>'; }
            for ($i = 1; $i < (int) $start->format('N'); $i++) { echo '<div class="zpCalDay zpCalDay--empty" aria-hidden="true"></div>'; }
            for ($day = 1; $day <= (int) $start->format('t'); $day++) {
                $entries = $days[$day] ?? [];
                echo '<div class="zpCalDay' . ($entries ? ' zpCalDay--scheduled' : ' zpCalDay--empty') . '" role="listitem"><div class="zpCalNumber">' . (int) $day . '</div>';
                foreach ($entries as $row) { echo '<div class="zpCalItem"><a data-zp-calendar-jump href="#zp-calendar-post-' . (int) $row['id'] . '">' . esc_html($row['title']) . '</a><small>' . esc_html($row['date']->format('H:i') . ' · ' . self::source_label($row['source'])) . '</small></div>'; }
                echo '</div>';
            }
            echo '</div></section>';
        }
        if ($rows) {
            echo '<section class="zpAdmin20__card"><h2>Wpisy i docelowe frazy</h2><p class="zpAdmin20__muted">Pozycja GSC jest średnią wyświetleń dla frazy w całej witrynie, z widoczną datą i okresem pomiaru. Nie jest pozycją przyszłego artykułu ani lokalnym rankingiem SERP. Kliknij frazę, aby przejść do statystyk.</p><div class="zpAdmin20__tableWrap zpCalOverflow"><table class="zpAdmin20__table zpCalTable widefat"><thead><tr><th>Publikacja / Warszawa</th><th>Artykuł / źródło</th><th>Główna fraza</th><th>Średnia pozycja GSC</th><th>Słowa</th><th>Przygotowanie</th></tr></thead><tbody>';
            foreach ($rows as $row) {
                $stats_url = add_query_arg(['page' => 'zp-suite-stats', 'zp_query' => $row['primary']], admin_url('admin.php'));
                echo '<tr><td>' . esc_html($row['date'] ? $row['date']->format('d.m.Y H:i T') : 'Brak poprawnej daty') . '</td><td><a data-zp-calendar-jump href="#zp-calendar-post-' . (int) $row['id'] . '">' . esc_html($row['title']) . '</a><br><span class="zpAdmin20__pill">' . esc_html(self::source_label($row['source'])) . '</span></td><td>' . ($row['primary'] !== '' ? '<a href="' . esc_url($stats_url) . '">' . esc_html($row['primary']) . '</a>' : 'Brak frazy w Rank Math') . '</td><td>' . self::metric_html($row['position']) . '</td><td>' . ($row['content']['words'] !== null ? '≈ ' . (int) $row['content']['words'] : '—') . '</td><td>' . self::readiness_html($row) . '</td></tr>';
            }
            echo '</tbody></table></div></section><section class="zpAdmin20__card"><h2>Szczegóły artykułów</h2>';
            foreach ($rows as $row) {
                echo '<details class="zpCalDetails" id="zp-calendar-post-' . (int) $row['id'] . '"><summary>' . esc_html(($row['date'] ? $row['date']->format('d.m.Y') . ' · ' : '') . $row['title']) . '</summary><dl class="zpCalMeta"><div><dt>Termin / status</dt><dd>' . esc_html($row['date'] ? $row['date']->format('d.m.Y H:i T') : 'Brak poprawnej daty') . ' · Zaplanowany</dd><dt>Kategoria / źródło planu</dt><dd>' . esc_html(implode(', ', $row['categories']) . ' · ' . self::source_label($row['source'])) . '</dd><dt>Frazy zapisane w Rank Math</dt><dd>' . esc_html($row['keywords'] ? implode(', ', $row['keywords']) : 'Brak frazy') . '</dd><dt>Adres docelowy artykułu</dt><dd><code>' . esc_html($row['url']) . '</code></dd></div><div><dt>Treść zapisana w WordPressie</dt><dd>' . ($row['content']['words'] !== null ? '≈ ' . (int) $row['content']['words'] . ' słów' : 'Brak jednoznacznego odczytu treści') . ' · ' . (int) count($row['content']['headings']) . ' nagłówków H2 · ' . (int) $row['content']['faq_count'] . ' pytań FAQ</dd><dt>Pomiar frazy w całej witrynie</dt><dd>' . self::metric_html($row['position']) . '</dd><dt>Modyfikacja wpisu w WordPressie</dt><dd>' . esc_html($row['modified'] ? $row['modified']->format('d.m.Y H:i T') : 'Brak daty') . '</dd>';
                if ($row['volume'] !== null) { echo '<dt>Wolumen frazy · badanie ' . esc_html($row['research_date'] !== '' ? $row['research_date'] : 'bez zapisanej daty') . '</dt><dd>' . (int) $row['volume'] . ' wyszukiwań / mies. · Polska</dd>'; }
                echo '</div></dl>';
                echo '<h3>Przygotowanie wpisu</h3>' . self::readiness_html($row, true) . '<dl class="zpCalMeta"><div><dt>Title zapisany w Rank Math</dt><dd>' . esc_html($row['seo_title'] !== '' ? $row['seo_title'] : 'Własne pole puste — tytuł wpisu / ustawienia Rank Math.') . '</dd></div><div><dt>Opis zapisany w Rank Math</dt><dd>' . esc_html($row['seo_description'] !== '' ? $row['seo_description'] : 'Własne pole puste — zajawka / ustawienia Rank Math.') . '</dd></div></dl><p class="zpAdmin20__muted">To odczyt zapisanych pól, nie symulacja wyniku Google. Podgląd przyszłego wpisu może różnić się od strony po publikacji.</p>';
                if ($row['content']['ambiguous']) { echo '<p class="zpAdmin20__muted">Kilka widgetów artykułu — sprawdź treść w edytorze.</p>'; }
                if ($row['photo_refresh']) { echo '<p><span class="zpAdmin20__pill">Zdjęcia z biblioteki odświeżone</span></p>'; }
                echo '<div class="zpCalPhotos">';
                if (self::safe_image($row['thumbnail_url'])) { echo '<figure><img loading="lazy" decoding="async" src="' . esc_url($row['thumbnail_url']) . '" alt="' . esc_attr($row['thumbnail_alt']) . '"><figcaption>Okładka wpisu · #' . (int) $row['thumbnail_id'] . '</figcaption></figure>'; }
                foreach (array_slice($row['content']['images'], 0, 2) as $image) { if (self::safe_image($image['url'])) { echo '<figure><img loading="lazy" decoding="async" src="' . esc_url($image['url']) . '" alt="' . esc_attr($image['alt']) . '"><figcaption>' . esc_html($image['alt'] !== '' ? $image['alt'] : 'Obraz wewnątrz artykułu') . '</figcaption></figure>'; } }
                echo '</div><div class="zpCalActions">';
                if ($row['preview']) { echo '<a class="button button-primary" target="_blank" rel="noopener" href="' . esc_url($row['preview']) . '">Podgląd WordPress</a>'; }
                if ($row['edit']) { echo '<a class="button" href="' . esc_url($row['edit']) . '">Edytuj wpis</a>'; }
                if(class_exists('ZP_Content_Preparation_20261010'))ZP_Content_Preparation_20261010::controls_by_post((int)$row['id']);
                if(class_exists('ZP_Article_Google_20261010'))ZP_Article_Google_20261010::controls((int)$row['id']);
                echo '</div></details>';
            }
            echo '</section>';
        }
        self::timeline();
        echo '<script>(function(){var root=document.querySelector("[data-zp-calendar-root]");if(!root)return;root.addEventListener("click",function(e){var link=e.target.closest("a[data-zp-calendar-jump]");if(!link||!root.contains(link))return;var id=link.getAttribute("href").slice(1),target=document.getElementById(id);if(!target||!root.contains(target))return;e.preventDefault();target.open=true;target.scrollIntoView({block:"start"});target.querySelector("summary").focus();});})();</script></div>';
        if ($shared) { zp_admin20_close(); } else { echo '</div>'; }
    }
}

function zp_suite_admin20_calendar_rows(): array { return ZP_Admin_Calendar_20261009::records(); }
function zp_suite_admin20_render_calendar(): void { ZP_Admin_Calendar_20261009::render(); }
