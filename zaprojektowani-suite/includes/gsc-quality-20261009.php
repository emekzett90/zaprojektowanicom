<?php
/** Four precise editorial corrections; no automatic migration or frontend filter. */
if (!defined('ABSPATH')) { exit; }

final class ZP_GSC_Quality_20261009 {
    public const PAGE = 'zp-suite-gsc-quality';
    public const BACKUP = 'zp_gsc_quality_20261009_backup';
    public const LOCK = 'zp_gsc_quality_20261009_lock';
    public const BUILD_DESC_OLD = 'Strona internetowa dla firmy budowlanej powinna jasno pokazywać zakres prac, obszar działania, realizacje, proces wyceny i szybki kontakt. Najważniejs.';
    public const BUILD_DESC_NEW = 'Zobacz, jak strona firmy budowlanej może pokazywać realizacje, zakres prac i obszar działania. Sprawdź portfolio, formularz wyceny i elementy budujące zaufanie.';
    public const STRATEGY_PL = 'Strategia marki mówi, dla kogo jest firma, czym się wyróżnia i jak komunikuje. Zobacz 9 elementów strategii, plan w 7 krokach i przykład dla małej firmy.';
    public const STRATEGY_EN_OLD = 'A brand strategy defines who your company is for, what sets it apart, and how it communicates. Discover the 9 elements of a brand strategy, a 7-step plan, and a real example for a small business.';
    public const STRATEGY_EN_NEW = 'A brand strategy defines who your company is for, what sets it apart, and how it communicates. Discover the 9 elements of a brand strategy, a 7-step plan, and an example for a small business.';

    public static function allowed(): bool { return current_user_can('manage_options') && current_user_can('edit_others_posts'); }
    public static function paths(): array {
        return [3368 => '/sklepy-internetowe/strona-internetowa-dla-firmy-budowlanej-jak-pokazac-realizacje-i-zdobywac-wyceny/', 5421 => '/logo-branding/strategia-marki/', 4630 => '/strony-internetowe/landing-page-co-to/', 2539 => '/ux-cro-analityka/strona-internetowa-a-konwersja/'];
    }
    public static function translations(): array {
        return [self::STRATEGY_PL => self::STRATEGY_EN_NEW, self::BUILD_DESC_NEW => 'See how a construction company website can showcase projects, services and its service area. Check portfolio, quote forms and elements that build trust.', 'landing page pod kampanię Meta Ads' => 'landing page for a Meta Ads campaign'];
    }
    public static function path(string $url): string { return (string) (wp_parse_url($url, PHP_URL_PATH) ?: '/'); }
    private static function article(string $html): bool {
        return (bool) preg_match('~<article\b[^>]*\bclass\s*=\s*(["\'])[^"\']*\bzpArticleNew\b[^"\']*\1~i', $html);
    }
    /** Change one known anchor, retaining every other byte including CSS and markup. */
    public static function html(string $html, int $id): string {
        if (!self::article($html)) { throw new RuntimeException('Brak oczekiwanego artykułu Elementor.'); }
        // Native Elementor post4630 was verified on 9 Oct; public HTML had already remapped this legacy href.
        if ($id === 4630) { $old_text = $new_text = 'projekt landing page'; $old_paths = ['/tworzenie-stron-internetowych/', '/strony-internetowe-katowice/']; $new_path = '/tworzenie-landing-page/'; }
        elseif ($id === 2539) { $old_text = 'kampanię Meta Ads'; $new_text = 'landing page pod kampanię Meta Ads'; $new_path = '/tworzenie-landing-page/'; $old_paths = [$new_path, '/wiedza/landing-page-pod-kampanie-meta-ads/']; }
        else { throw new RuntimeException('Niedozwolony cel HTML.'); }
        // Existing premium cards themselves use <article>; find the balanced outer wrapper.
        preg_match_all('~</?article\b(?:[^>"\']|"[^"]*"|\'[^\']*\')*>~i', $html, $tokens, PREG_OFFSET_CAPTURE);
        $at = null; $end = null; $depth = 0; $wrappers = 0;
        foreach ($tokens[0] as $token) {
            if (preg_match('~^</~', $token[0])) { if ($at !== null && $end === null && --$depth === 0) { $end = $token[1] + strlen($token[0]); } continue; }
            $tag = new WP_HTML_Tag_Processor($token[0]); $tag->next_tag('ARTICLE');
            $is_wrapper = in_array('zpArticleNew', preg_split('/\s+/', (string) $tag->get_attribute('class')) ?: [], true);
            if ($is_wrapper) { $wrappers++; }
            if ($at === null && $is_wrapper) { $at = $token[1]; $depth = 1; }
            elseif ($at !== null && $end === null) { $depth++; }
        }
        if ($wrappers !== 1 || $at === null || $end === null || $depth !== 0) { throw new RuntimeException('Nieoczekiwane granice bloku artykułu.'); }
        $article = substr($html, $at, $end - $at);
        $old_count = $new_count = 0;
        // Keep embedded schema, CSS, JS and comments byte-identical, even if they contain anchor-like text.
        $out = preg_replace_callback('~(?P<opaque><!--.*?-->|<(?P<raw>script|style)\b[^>]*>.*?</(?P=raw)\s*>)|(?P<open><a\b[^>]*>)(?P<text>[^<]*)(?P<close></a\s*>)~ius', static function ($m) use ($old_text, $new_text, $old_paths, $new_path, &$old_count, &$new_count) {
            if (!empty($m['opaque'])) { return $m[0]; }
            $tag = new WP_HTML_Tag_Processor($m['open']); if (!$tag->next_tag('A')) { return $m[0]; }
            $href = $tag->get_attribute('href'); if (!is_string($href)) { return $m[0]; }
            $parts = wp_parse_url($href); if (!is_array($parts) || isset($parts['query']) || isset($parts['fragment'])) { return $m[0]; }
            if (isset($parts['host']) && strtolower($parts['host']) !== strtolower((string) wp_parse_url(home_url('/'), PHP_URL_HOST))) { return $m[0]; }
            $text = trim(html_entity_decode($m['text'], ENT_QUOTES | ENT_HTML5, 'UTF-8')); $path = (string) ($parts['path'] ?? '');
            if ($text === $new_text && $path === $new_path) { $new_count++; return $m[0]; }
            if ($text !== $old_text || !in_array($path, $old_paths, true)) { return $m[0]; }
            // The exact visible text must be present literally, not reconstructed from unknown markup.
            if (substr_count($m['text'], $old_text) !== 1) { throw new RuntimeException('Niestandardowe kodowanie znanego anchora.'); }
            $old_count++; if ($path !== $new_path) { $tag->set_attribute('href', isset($parts['host']) ? home_url($new_path) : $new_path); }
            return $tag->get_updated_html() . str_replace($old_text, $new_text, $m['text']) . $m['close'];
        }, $article);
        if (!is_string($out) || $old_count + $new_count !== 1 || ($old_count && $new_count)) { throw new RuntimeException('Znany link nie występuje dokładnie raz; nic nie zmieniono.'); }
        return substr_replace($html, $out, $at, strlen($article));
    }
    /** Only one HTML widget is changed; unrelated widgets and settings remain structurally identical. */
    public static function elementor(string $raw, int $id): string {
        $data = json_decode($raw, true); if (!is_array($data) || json_last_error() !== JSON_ERROR_NONE) { throw new RuntimeException('Nieprawidłowe dane Elementor.'); }
        $count = 0; $changed = false;
        $walk = static function (array &$nodes) use (&$walk, &$count, &$changed, $id): void {
            foreach ($nodes as &$node) {
                if (!is_array($node)) { continue; }
                if (($node['widgetType'] ?? '') === 'html' && is_string($node['settings']['html'] ?? null) && self::article($node['settings']['html'])) {
                    $count++; $new = self::html($node['settings']['html'], $id); $changed = $changed || $new !== $node['settings']['html']; $node['settings']['html'] = $new;
                }
                if (is_array($node['elements'] ?? null)) { $walk($node['elements']); }
            }
            unset($node);
        };
        $walk($data); if ($count !== 1) { throw new RuntimeException('Wymagany dokładnie jeden widget artykułu.'); }
        if (!$changed) { return $raw; }
        $new = wp_json_encode($data); if (!is_string($new)) { throw new RuntimeException('Nie zakodowano danych Elementor.'); } return $new;
    }
    public static function preflight(): array {
        if (!self::allowed() || !class_exists('WP_HTML_Tag_Processor') || !class_exists('\ZPL\Dict') || !is_callable(['\ZPL\Admin', 'store_overrides'])) { throw new RuntimeException('Brak uprawnień lub wymaganego parsera/tłumacza.'); }
        $fields = []; $posts = [];
        foreach (self::paths() as $id => $path) {
            $post = get_post($id); if (!$post || $post->post_type !== 'post' || $post->post_status !== 'publish' || $post->post_password !== '' || self::path((string) get_permalink($id)) !== $path || !current_user_can('edit_post', $id)) { throw new RuntimeException('Tożsamość lub stan jednego z czterech wpisów zmieniono.'); }
            $posts[$id] = ['id' => $id, 'path' => $path];
        }
        if (get_post_meta(5421, 'rank_math_description', true) !== self::STRATEGY_PL) { throw new RuntimeException('Opis PL strategii zmieniono ręcznie.'); }
        $old = get_post_meta(3368, 'rank_math_description', true);
        if (!in_array($old, [self::BUILD_DESC_OLD, self::BUILD_DESC_NEW], true)) { throw new RuntimeException('Opis firmy budowlanej zmieniono ręcznie.'); }
        $fields['3368:rank_math_description'] = ['id' => 3368, 'key' => 'rank_math_description', 'old' => $old, 'new' => self::BUILD_DESC_NEW];
        foreach ([4630, 2539] as $id) {
            $raw = get_post_meta($id, '_elementor_data', true); if (!is_string($raw)) { throw new RuntimeException('Elementor nie zawiera oczekiwanego JSON.'); }
            $fields[$id . ':_elementor_data'] = ['id' => $id, 'key' => '_elementor_data', 'old' => $raw, 'new' => self::elementor($raw, $id)];
        }
        $overrides = get_option('zpl_overrides', []); if (!is_array($overrides)) { throw new RuntimeException('Niestandardowy format słownika.'); }
        $before = []; $after = self::translations();
        foreach ($after as $pl => $en) {
            $before[$pl] = ['exists' => array_key_exists($pl, $overrides), 'value' => $overrides[$pl] ?? null];
            $known_bad = $pl === self::STRATEGY_PL && $before[$pl]['value'] === self::STRATEGY_EN_OLD;
            if ($before[$pl]['exists'] && $before[$pl]['value'] !== $en && !$known_bad) { throw new RuntimeException('Istnieje ręczna korekta jednego opisu EN; zachowano ją.'); }
        }
        $resolved = \ZPL\Dict::get(self::STRATEGY_PL, 'en', self::paths()[5421]);
        if (!in_array($resolved, [self::STRATEGY_EN_OLD, self::STRATEGY_EN_NEW], true)) { throw new RuntimeException('Obecne tłumaczenie strategii nie odpowiada audytowi.'); }
        $review = ['schema' => 1, 'posts' => $posts, 'fields' => $fields, 'translations_before' => $before, 'translations_after' => $after];
        $review['hash'] = hash('sha256', wp_json_encode($review)); return $review;
    }
    public static function apply(string $hash): void {
        $r = self::preflight(); if (!preg_match('/^[a-f0-9]{64}$/D', $hash) || !hash_equals($r['hash'], $hash)) { throw new RuntimeException('Dane zmieniono po przeglądzie; odśwież panel.'); }
        $changed = false; foreach ($r['fields'] as $f) { $changed = $changed || $f['old'] !== $f['new']; }
        foreach ($r['translations_before'] as $pl => $b) { $changed = $changed || !$b['exists'] || $b['value'] !== $r['translations_after'][$pl]; }
        if (!$changed) { return; }
        if (get_option(self::BACKUP, null) !== null) { throw new RuntimeException('Istnieje wcześniejsza kopia; najpierw sprawdź lub przywróć poprzednią operację.'); }
        $r['created_utc'] = gmdate('c');
        if (!add_option(self::BACKUP, $r, '', false) || get_option(self::BACKUP) !== $r) { throw new RuntimeException('Nie zapisano prywatnej kopii przed zmianami.'); }
        foreach ($r['fields'] as $f) {
            if ($f['old'] === $f['new']) { continue; }
            if (get_post_meta($f['id'], $f['key'], true) !== $f['old']) { throw new RuntimeException('Równoległa zmiana wpisu; kopia pozostaje do przywrócenia.'); }
            update_post_meta($f['id'], $f['key'], wp_slash($f['new']));
            if (get_post_meta($f['id'], $f['key'], true) !== $f['new']) { throw new RuntimeException('Zapis wpisu nie przeszedł odczytu kontrolnego.'); }
            self::invalidate($f['id'], $f['key'] === '_elementor_data');
        }
        $o = get_option('zpl_overrides', []); if (!is_array($o)) { throw new RuntimeException('Słownik zmieniono równolegle.'); }
        foreach ($r['translations_after'] as $pl => $en) {
            $b = $r['translations_before'][$pl]; if (array_key_exists($pl, $o) !== $b['exists'] || ($b['exists'] && $o[$pl] !== $b['value'])) { throw new RuntimeException('Ręczna zmiana słownika podczas operacji; zachowano ją.'); }
            $o[$pl] = $en;
        }
        self::write_translations($o);
    }
    private static function invalidate(int $id, bool $elementor): void {
        if ($elementor) { foreach (['_elementor_css', '_elementor_element_cache', '_elementor_page_assets'] as $key) { delete_post_meta($id, $key); } }
        clean_post_cache($id); do_action('litespeed_purge_post', $id);
    }
    private static function write_translations(array $o): void {
        // Same authoritative write as Languages UI: bump browser dictionary version and flush runtime/object cache.
        \ZPL\Admin::store_overrides($o); ksort($o);
        if (get_option('zpl_overrides') !== $o || (string) get_option('zpl_overrides_version', '') === '') { throw new RuntimeException('Słownik lub wersja przełącznika nie przeszły odczytu kontrolnego.'); }
    }
    /** An interrupted apply can restore only our old/new values; a later manual edit always blocks restore. */
    public static function restore(): void {
        if (!self::allowed()) { throw new RuntimeException('Brak uprawnień.'); }
        $b = get_option(self::BACKUP, null); if (!is_array($b) || ($b['schema'] ?? null) !== 1 || !isset($b['hash'])) { throw new RuntimeException('Brak prawidłowej kopii.'); }
        $check = $b; unset($check['hash'], $check['created_utc']); if (!hash_equals($b['hash'], hash('sha256', wp_json_encode($check)))) { throw new RuntimeException('Kopia nie przeszła kontroli hash.'); }
        $o = get_option('zpl_overrides', []); if (!is_array($o)) { throw new RuntimeException('Słownik ma nieoczekiwany format.'); }
        foreach ($b['fields'] as $f) { if (!in_array(get_post_meta($f['id'], $f['key'], true), [$f['old'], $f['new']], true)) { throw new RuntimeException('Późniejsza ręczna zmiana wpisu — przywracanie zablokowane.'); } }
        foreach ($b['translations_after'] as $pl => $en) {
            $before = $b['translations_before'][$pl]; $exists = array_key_exists($pl, $o);
            if ($exists && $o[$pl] !== $en && (!$before['exists'] || $o[$pl] !== $before['value'])) { throw new RuntimeException('Późniejsza ręczna korekta słownika — przywracanie zablokowane.'); }
            if (!$exists && $before['exists']) { throw new RuntimeException('Późniejsza ręczna zmiana słownika — przywracanie zablokowane.'); }
        }
        foreach ($b['fields'] as $f) {
            if (get_post_meta($f['id'], $f['key'], true) === $f['old']) { continue; }
            update_post_meta($f['id'], $f['key'], wp_slash($f['old'])); if (get_post_meta($f['id'], $f['key'], true) !== $f['old']) { throw new RuntimeException('Odczyt przywracanego wpisu niezgodny.'); } self::invalidate($f['id'], $f['key'] === '_elementor_data');
        }
        foreach ($b['translations_before'] as $pl => $before) { if ($before['exists']) { $o[$pl] = $before['value']; } else { unset($o[$pl]); } }
        self::write_translations($o); delete_option(self::BACKUP);
    }
    public static function handle(): void {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST' || !self::allowed() || !wp_verify_nonce(is_string($_POST['_wpnonce'] ?? null) ? wp_unslash($_POST['_wpnonce']) : '', 'zp_gsc_quality_20261009')) { wp_die('Brak uprawnień lub poprawnego nonce.', '', ['response' => 403]); }
        $lock = (int) get_option(self::LOCK, 0); if ($lock && $lock < time() - 300) { delete_option(self::LOCK); }
        if (!add_option(self::LOCK, time(), '', false)) { wp_die('Operacja jest już wykonywana.', '', ['response' => 409]); }
        try {
            $command = $_POST['command'] ?? '';
            if ($command === 'apply') { self::apply(is_string($_POST['review_hash'] ?? null) ? wp_unslash($_POST['review_hash']) : ''); }
            elseif ($command === 'restore') { self::restore(); }
            else { throw new RuntimeException('Niedozwolone działanie.'); }
            set_transient('zp_gsc_quality_notice_' . get_current_user_id(), 'Operacja zakończona; odczyt kontrolny zgodny.', 600);
        } catch (Throwable $error) { set_transient('zp_gsc_quality_notice_' . get_current_user_id(), $error->getMessage(), 600); }
        finally { delete_option(self::LOCK); }
        wp_safe_redirect(admin_url('admin.php?page=' . self::PAGE)); exit;
    }
    public static function page(): void {
        if (!self::allowed()) { wp_die('Brak uprawnień.'); }
        echo '<div class="wrap"><h1>ZP Suite — Korekty jakości artykułów</h1><p>Cztery wpisy z audytu: pełny opis firmy budowlanej, zgodny z treścią opis EN strategii, dedykowany link do landing page i czytelniejszy anchor Meta Ads. Operacja zachowuje układ, CSS, schematy i harmonogram publikacji.</p>';
        $notice = get_transient('zp_gsc_quality_notice_' . get_current_user_id()); if ($notice) { echo '<div class="notice notice-info"><p>' . esc_html($notice) . '</p></div>'; delete_transient('zp_gsc_quality_notice_' . get_current_user_id()); }
        try {
            $r = self::preflight(); $changes = 0;
            echo '<table class="widefat"><tr><th>Wpis i pole</th><th>Stan</th><th>SHA-256 przed → po</th></tr>';
            foreach ($r['fields'] as $f) { $same = $f['old'] === $f['new']; if (!$same) { $changes++; } echo '<tr><td>' . (int) $f['id'] . ' / ' . esc_html($f['key']) . '</td><td>' . ($same ? 'gotowe' : 'do korekty') . '</td><td><code>' . esc_html(hash('sha256', $f['old'])) . ' → ' . esc_html(hash('sha256', $f['new'])) . '</code></td></tr>'; }
            foreach ($r['translations_before'] as $pl => $before) { if (!$before['exists'] || $before['value'] !== $r['translations_after'][$pl]) { $changes++; } }
            echo '</table><p>Zmiany słownika dotyczą tylko dwóch dokładnych opisów i jednego anchora, w istniejącej opcji Języki PL/EN. Kopia oryginalnego Elementor JSON i opisów jest prywatną opcją bazy (autoload=false).</p>';
            if ($changes) { self::form('apply', 'Zastosuj sprawdzone korekty', $r['hash']); } else { echo '<p><strong>Wszystkie korekty są już zastosowane.</strong></p>'; }
        } catch (Throwable $error) { echo '<div class="notice notice-error"><p>' . esc_html($error->getMessage()) . '</p></div>'; }
        if (get_option(self::BACKUP, null) !== null) { self::form('restore', 'Przywróć oryginały przy zgodności hashów'); } echo '</div>';
    }
    private static function form(string $command, string $label, string $hash = ''): void {
        echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '">'; wp_nonce_field('zp_gsc_quality_20261009'); echo '<input type="hidden" name="action" value="zp_gsc_quality_20261009"><input type="hidden" name="command" value="' . esc_attr($command) . '"><input type="hidden" name="review_hash" value="' . esc_attr($hash) . '">'; submit_button($label); echo '</form>';
    }
}
add_action('admin_menu', static function () { add_submenu_page('zp-suite', 'Korekty jakości artykułów', 'Korekty artykułów', 'manage_options', ZP_GSC_Quality_20261009::PAGE, [ZP_GSC_Quality_20261009::class, 'page']); }, 10004);
add_action('admin_post_zp_gsc_quality_20261009', [ZP_GSC_Quality_20261009::class, 'handle']);
