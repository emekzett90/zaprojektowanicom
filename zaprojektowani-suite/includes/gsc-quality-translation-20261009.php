<?php
/** Exact inline-segment addendum. The immutable version19 backup and article HTML are never written. */
if (!defined('ABSPATH')) { exit; }

final class ZP_GSC_Quality_Translation_20261009 {
    public const PAGE = 'zp-suite-gsc-quality-translation';
    public const BACKUP = 'zp_gsc_quality_translation_20261009_backup';
    public const LOCK = 'zp_gsc_quality_translation_20261009_lock';
    public const PL = 'Przy większych działaniach reklamowych warto połączyć optymalizację strony z osobnymi landing page’ami, np. pod <1>landing page pod kampanię Meta Ads</1>.';
    public const EN = "With bigger advertising activities, it's worth combining website optimization with separate landing pages, such as a <1>landing page for a Meta Ads campaign</1>.";

    public static function allowed(): bool { return current_user_can('manage_options') && current_user_can('edit_post', 2539); }
    public static function preflight(): array {
        if (!self::allowed() || !class_exists('ZP_GSC_Quality_20261009') || !class_exists('ZPL\\Html') || !is_callable(['ZPL\\Admin', 'store_overrides'])) { throw new RuntimeException('Brak uprawnień lub modułu jakości/tłumaczeń.'); }
        $original = get_option(ZP_GSC_Quality_20261009::BACKUP, null);
        if (!is_array($original) || ($original['schema'] ?? null) !== 1 || !is_string($original['hash'] ?? null)) { throw new RuntimeException('Brak oryginalnej prywatnej kopii korekty z wersji19.'); }
        $verified = $original; unset($verified['hash'], $verified['created_utc']);
        if (!hash_equals($original['hash'], hash('sha256', wp_json_encode($verified)))) { throw new RuntimeException('Oryginalna kopia wersji19 nie przeszła kontroli hash.'); }
        // Existing version19 guards retain exact post identity/status/path and protect manual changes.
        $review19 = ZP_GSC_Quality_20261009::preflight();
        foreach ($review19['fields'] as $field) { if ($field['old'] !== $field['new']) { throw new RuntimeException('Najpierw dokończ pierwotne cztery korekty.'); } }
        foreach (ZP_GSC_Quality_20261009::translations() as $pl => $en) {
            $before = $review19['translations_before'][$pl] ?? [];
            if (empty($before['exists']) || ($before['value'] ?? null) !== $en) { throw new RuntimeException('Pierwotne tłumaczenia wersji19 są niezgodne.'); }
        }
        $raw = get_post_meta(2539, '_elementor_data', true);
        $expected = $original['fields']['2539:_elementor_data']['new'] ?? null;
        if (!is_string($raw) || !is_string($expected) || $raw !== $expected) { throw new RuntimeException('Artykuł2539 zmieniono po korekcie19; zachowano późniejszą treść.'); }
        $nodes = json_decode($raw, true); if (!is_array($nodes)) { throw new RuntimeException('Nieprawidłowy zapis Elementor.'); }
        $widgets = 0; $segments = 0;
        $walk = static function (array $nodes) use (&$walk, &$widgets, &$segments): void {
            foreach ($nodes as $node) {
                if (!is_array($node)) { continue; }
                $html = $node['settings']['html'] ?? null;
                if (($node['widgetType'] ?? '') === 'html' && is_string($html) && preg_match('/<article\b[^>]*\bzpArticleNew\b/i', $html)) {
                    $widgets++;
                    \ZPL\Html::walk(\ZPL\Html::parse('<body>' . $html . '</body>'), static function (array $segment) use (&$segments): void { if (($segment['k'] ?? null) === self::PL) { $segments++; } }, static function (): void {});
                }
                if (is_array($node['elements'] ?? null)) { $walk($node['elements']); }
            }
        };
        $walk($nodes);
        if ($widgets !== 1 || $segments !== 1 || !\ZPL\Html::markers_match(self::PL, self::EN)) { throw new RuntimeException('Oczekiwany akapit tłumaczenia nie występuje dokładnie raz.'); }
        $o = get_option('zpl_overrides', []); if (!is_array($o)) { throw new RuntimeException('Niestandardowy format korekt języka.'); }
        $exists = array_key_exists(self::PL, $o); $value = $o[self::PL] ?? null;
        if ($exists && $value !== self::EN) { throw new RuntimeException('Istnieje późniejsza ręczna korekta tego akapitu; zachowano ją.'); }
        $r = ['schema' => 1, 'post_id' => 2539, 'elementor_sha256' => hash('sha256', $raw), 'original_backup_sha256' => hash('sha256', wp_json_encode($original)), 'key' => self::PL, 'before' => ['exists' => $exists, 'value' => $value], 'after' => self::EN];
        $r['hash'] = hash('sha256', wp_json_encode($r)); return $r;
    }
    public static function apply(string $hash): void {
        $r = self::preflight();
        if (!preg_match('/^[a-f0-9]{64}$/D', $hash) || !hash_equals($r['hash'], $hash)) { throw new RuntimeException('Dane zmieniono po przeglądzie; odśwież panel.'); }
        if ($r['before']['exists'] && $r['before']['value'] === self::EN) { return; }
        if (get_option(self::BACKUP, null) !== null) { throw new RuntimeException('Istnieje kopia tego uzupełnienia; sprawdź ją przed kolejnym zapisem.'); }
        $r['created_utc'] = gmdate('c');
        if (!add_option(self::BACKUP, $r, '', false) || get_option(self::BACKUP) !== $r) { throw new RuntimeException('Nie zapisano oddzielnej prywatnej kopii przed zmianą.'); }
        $o = get_option('zpl_overrides', []);
        if (!is_array($o) || array_key_exists(self::PL, $o) || hash('sha256', (string) get_post_meta(2539, '_elementor_data', true)) !== $r['elementor_sha256']) { throw new RuntimeException('Równoległa zmiana akapitu lub korekt języka; nic nie nadpisano.'); }
        $o[self::PL] = self::EN; self::store($o);
    }
    private static function store(array $o): void {
        \ZPL\Admin::store_overrides($o); ksort($o);
        if (get_option('zpl_overrides', []) !== $o || (string) get_option('zpl_overrides_version', '') === '') { throw new RuntimeException('Korekta języka nie przeszła odczytu kontrolnego.'); }
        clean_post_cache(2539); do_action('litespeed_purge_post', 2539);
    }
    public static function restore(): void {
        if (!self::allowed()) { throw new RuntimeException('Brak uprawnień.'); }
        $b = get_option(self::BACKUP, null);
        if (!is_array($b) || ($b['schema'] ?? null) !== 1 || ($b['key'] ?? '') !== self::PL || ($b['after'] ?? '') !== self::EN || !is_string($b['hash'] ?? null)) { throw new RuntimeException('Brak prawidłowej kopii uzupełnienia.'); }
        $verified = $b; unset($verified['hash'], $verified['created_utc']);
        if (!hash_equals($b['hash'], hash('sha256', wp_json_encode($verified)))) { throw new RuntimeException('Kopia uzupełnienia nie przeszła kontroli hash.'); }
        $o = get_option('zpl_overrides', []); if (!is_array($o)) { throw new RuntimeException('Niestandardowy format korekt języka.'); }
        $exists = array_key_exists(self::PL, $o);
        if ($exists && $o[self::PL] !== self::EN && (!($b['before']['exists'] ?? false) || $o[self::PL] !== ($b['before']['value'] ?? null))) { throw new RuntimeException('Późniejszą ręczną korektę zachowano; przywracanie zablokowane.'); }
        if ($exists) {
            if (!empty($b['before']['exists'])) { $o[self::PL] = $b['before']['value']; } else { unset($o[self::PL]); }
            self::store($o);
        }
        delete_option(self::BACKUP);
    }
    public static function handle(): void {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST' || !self::allowed() || !wp_verify_nonce(is_string($_POST['_wpnonce'] ?? null) ? wp_unslash($_POST['_wpnonce']) : '', 'zp_gsc_quality_translation_20261009')) { wp_die('Brak uprawnień lub poprawnego nonce.', '', ['response' => 403]); }
        if (!add_option(self::LOCK, time(), '', false)) { wp_die('Uzupełnienie jest już wykonywane.', '', ['response' => 409]); }
        try {
            if (($_POST['command'] ?? '') === 'apply') { self::apply(is_string($_POST['review_hash'] ?? null) ? wp_unslash($_POST['review_hash']) : ''); }
            elseif (($_POST['command'] ?? '') === 'restore') { self::restore(); }
            else { throw new RuntimeException('Niedozwolone działanie.'); }
            set_transient('zp_gsc_quality_translation_notice_' . get_current_user_id(), 'Uzupełnienie zakończone; odczyt kontrolny zgodny.', 600);
        } catch (Throwable $e) { set_transient('zp_gsc_quality_translation_notice_' . get_current_user_id(), $e->getMessage(), 600); }
        finally { delete_option(self::LOCK); }
        wp_safe_redirect(admin_url('admin.php?page=' . self::PAGE)); exit;
    }
    public static function page(): void {
        if (!self::allowed()) { wp_die('Brak uprawnień.', '', ['response' => 403]); }
        echo '<div class="wrap"><h1>Korekta jednego akapitu EN</h1><p>Uzupełnienie dla artykułu2539: tłumacz odczytuje cały akapit z markerem linku jako jeden fragment. Dodajemy tylko ten dokładny wpis językowy. Oryginalna kopia wersji19, treść artykułu i poprzednie trzy korekty pozostają zachowane.</p>';
        $notice_key = 'zp_gsc_quality_translation_notice_' . get_current_user_id();
        $notice = get_transient($notice_key); if ($notice) { echo '<div class="notice notice-info"><p>' . esc_html($notice) . '</p></div>'; delete_transient($notice_key); }
        try {
            $r = self::preflight();
            echo '<p><strong>PL:</strong> ' . esc_html(self::PL) . '</p><p><strong>EN:</strong> ' . esc_html(self::EN) . '</p><p>SHA-256 aktualnego Elementor: <code>' . esc_html($r['elementor_sha256']) . '</code>.</p>';
            if ($r['before']['exists']) { echo '<p><strong>Tłumaczenie jest gotowe.</strong></p>'; }
            else { self::form('apply', 'Zastosuj dokładne tłumaczenie akapitu', $r['hash']); }
        } catch (Throwable $e) { echo '<div class="notice notice-error"><p>' . esc_html($e->getMessage()) . '</p></div>'; }
        if (get_option(self::BACKUP, null) !== null) { self::form('restore', 'Przywróć tylko to uzupełnienie'); }
        echo '</div>';
    }
    private static function form(string $command, string $label, string $hash = ''): void {
        echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '">'; wp_nonce_field('zp_gsc_quality_translation_20261009');
        echo '<input type="hidden" name="action" value="zp_gsc_quality_translation_20261009"><input type="hidden" name="command" value="' . esc_attr($command) . '"><input type="hidden" name="review_hash" value="' . esc_attr($hash) . '">'; submit_button($label); echo '</form>';
    }
}
add_action('admin_menu', static function (): void { add_submenu_page('zp-suite', 'Korekta akapitu EN', 'Korekta akapitu EN', 'manage_options', ZP_GSC_Quality_Translation_20261009::PAGE, [ZP_GSC_Quality_Translation_20261009::class, 'page']); }, 10006);
add_action('admin_post_zp_gsc_quality_translation_20261009', [ZP_GSC_Quality_Translation_20261009::class, 'handle']);
