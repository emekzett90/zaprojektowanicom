<?php
namespace ZPTE;

if (!defined('ABSPATH')) { exit; }

/** ZP Suite → Tłumacz EN */
final class Admin {
    const SLUG = 'zp-tlumacz-en';
    const PER_PAGE = 50;

    public static function boot(): void {
        // After the Suite trims its submenu (priority 9999), so this entry stays visible.
        add_action('admin_menu', [self::class, 'menu'], 10000);
        add_action('admin_post_zpte', [self::class, 'handle']);
        add_action('admin_notices', [self::class, 'notices']);
    }

    public static function menu(): void {
        global $admin_page_hooks;
        $parent = isset($admin_page_hooks['zp-suite']) ? 'zp-suite' : 'options-general.php';
        add_submenu_page($parent, 'Tłumacz EN (OpenAI)', 'Tłumacz EN', 'manage_options', self::SLUG, [self::class, 'page']);
    }

    public static function url(array $args = []): string {
        return add_query_arg(array_merge(['page' => self::SLUG], $args), admin_url('admin.php'));
    }

    public static function notices(): void {
        if (!current_user_can('manage_options')) { return; }
        $a = Log::current_alert();
        if (!$a) { return; }
        echo '<div class="notice notice-error"><p><strong>Tłumacz EN:</strong> ' . esc_html((string) $a['m'])
            . ' <a href="' . esc_url(self::url(['tab' => 'settings'])) . '">Ustawienia tłumacza</a></p></div>';
    }

    private static function when(?string $gmt): string {
        if ($gmt === null || $gmt === '' || strpos($gmt, '0000') === 0) { return '—'; }
        $ts = strtotime($gmt . ' UTC');
        return $ts ? wp_date('Y-m-d H:i', $ts) : '—';
    }

    private static function form_open(string $do, array $hidden = [], string $confirm = ''): string {
        $h = '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '" style="display:inline"' . ($confirm !== '' ? ' onsubmit="return confirm(' . esc_attr((string) wp_json_encode($confirm)) . ')"' : '') . '>'
            . '<input type="hidden" name="action" value="zpte"><input type="hidden" name="do" value="' . esc_attr($do) . '">'
            . wp_nonce_field('zpte', '_wpnonce', true, false);
        foreach ($hidden as $k => $v) { $h .= '<input type="hidden" name="' . esc_attr($k) . '" value="' . esc_attr((string) $v) . '">'; }
        return $h;
    }

    private static function button(string $do, string $label, array $hidden = [], string $class = 'button', string $confirm = ''): string {
        return self::form_open($do, $hidden, $confirm) . '<button type="submit" class="' . esc_attr($class) . '">' . esc_html($label) . '</button></form>';
    }

    // ------------------------------------------------------------------- page

    public static function page(): void {
        if (!current_user_can('manage_options')) { return; }
        $tabs = ['status' => 'Status', 'pages' => 'Strony', 'settings' => 'Ustawienia', 'log' => 'Dziennik'];
        $tab = sanitize_key($_GET['tab'] ?? 'status');
        if ($tab === 'edit') { $current = 'pages'; } else { $current = isset($tabs[$tab]) ? $tab : 'status'; }
        echo '<div class="wrap zpte"><h1>Tłumacz EN <span style="font-size:13px;color:#646970;font-weight:400">OpenAI · moduł ' . esc_html(ZPTE_VERSION) . '</span></h1>';
        echo '<style>.zpte .zpte-box{background:#fff;border:1px solid #dcdcde;border-radius:6px;padding:14px 18px;margin:14px 0;max-width:980px}.zpte .zpte-scroll{overflow-x:auto;max-width:100%}.zpte table.widefat td,.zpte table.widefat th{vertical-align:top}.zpte .zpte-ok{color:#00a32a}.zpte .zpte-bad{color:#d63638}.zpte .zpte-warn{color:#996800}.zpte code{word-break:break-all}.zpte .zpte-actions form{margin:0 4px 4px 0}.zpte textarea{width:100%}</style>';
        if (!empty($_GET['msg'])) { echo '<div class="notice notice-success is-dismissible"><p>' . esc_html(wp_unslash((string) $_GET['msg'])) . '</p></div>'; }
        if (!empty($_GET['err'])) { echo '<div class="notice notice-error is-dismissible"><p>' . esc_html(wp_unslash((string) $_GET['err'])) . '</p></div>'; }
        echo '<nav class="nav-tab-wrapper">';
        foreach ($tabs as $k => $label) { echo '<a class="nav-tab' . ($k === $current ? ' nav-tab-active' : '') . '" href="' . esc_url(self::url(['tab' => $k])) . '">' . esc_html($label) . '</a>'; }
        echo '</nav>';
        if (!Bridge::ready()) {
            echo '<div class="zpte-box"><p class="zpte-bad"><strong>Brak modułu językowego (zaprojektowani-languages).</strong> Tłumacz zapisuje wersje angielskie w tym module, więc bez niego nic nie robi.</p></div></div>';
            return;
        }
        Store::install();
        if ($tab === 'edit') { self::tab_edit((int) ($_GET['id'] ?? 0)); }
        else { $fn = 'tab_' . $current; self::$fn(); }
        echo '</div>';
    }

    private static function tab_status(): void {
        $key = Settings::api_key() !== '';
        $hooks = Bridge::hooks_ready();
        $enabled = (bool) Settings::get('enabled');
        $next = wp_next_scheduled(Worker::DAILY);
        $usage = Log::usage();
        $month = Log::month();
        $limit = (int) Settings::get('daily_chars');
        $queued = Store::queued_count();

        $problems = [];
        if (!$hooks) { $problems[] = 'Moduł językowy w tej wersji Suite nie ma haków dla tłumacza (potrzebny zaprojektowani-languages 1.0.13 lub nowszy).'; }
        if (!$key) { $problems[] = Settings::key_unreadable() ? 'Zapisanego klucza nie da się odczytać (zmieniły się klucze bezpieczeństwa WordPressa) — wklej klucz ponownie.' : 'Brak klucza API OpenAI — wklej go w zakładce Ustawienia.'; }
        if (!$enabled) { $problems[] = 'Codzienne sprawdzanie jest wyłączone w ustawieniach.'; }
        if (defined('DISABLE_WP_CRON') && DISABLE_WP_CRON) { $problems[] = 'WP-Cron jest wyłączony w wp-config.php (DISABLE_WP_CRON) — tłumacz ruszy tylko, jeśli serwer sam wywołuje wp-cron.php.'; }
        $alert = Log::current_alert();
        if ($alert) { $problems[] = (string) $alert['m']; }

        echo '<div class="zpte-box">';
        echo $problems
            ? '<p class="zpte-bad"><strong>Wymaga uwagi:</strong></p><ul style="list-style:disc;padding-left:20px">' . implode('', array_map(static function ($p) { return '<li>' . esc_html($p) . '</li>'; }, $problems)) . '</ul>'
            : '<p class="zpte-ok"><strong>Działa.</strong> Codziennie o 3:00 sprawdzam nowe treści i braki w wersji angielskiej.</p>';
        echo '<table class="widefat striped" style="max-width:760px"><tbody>';
        $rows = [
            'Klucz API OpenAI' => $key ? 'zapisany (' . esc_html(Settings::key_hint()) . ')' . (Settings::key_source() === 'constant' ? ' — z wp-config.php' : '') : '<span class="zpte-bad">brak</span>',
            'Model' => esc_html(Settings::model()),
            'Nowe treści' => Settings::get('mode') === 'draft' ? 'zapisuję jako szkic do akceptacji' : 'publikuję automatycznie',
            'Nowe = opublikowane od' => esc_html((string) Settings::get('since')) . (Settings::get('older') ? ' (starsze też)' : ' (starsze tylko po włączeniu w ustawieniach)'),
            'Następne sprawdzenie' => $next ? esc_html(wp_date('Y-m-d H:i', $next)) : '—',
            'Ostatnie sprawdzenie' => ($t = (int) get_option('zpte_last_plan')) ? esc_html(wp_date('Y-m-d H:i', $t)) : '—',
            'W kolejce' => number_format_i18n($queued) . (Worker::running() ? ' — <em>tłumaczę teraz</em>' : ''),
            'Dziś wysłano do OpenAI' => number_format_i18n((int) $usage['chars']) . ' znaków' . ($limit > 0 ? ' z ' . number_format_i18n($limit) . ' (limit dzienny)' : '') . ', zapytań: ' . number_format_i18n((int) $usage['requests']),
            'W tym miesiącu' => number_format_i18n((int) $month['chars']) . ' znaków, tokeny: ' . number_format_i18n((int) $month['in']) . ' wejście / ' . number_format_i18n((int) $month['out']) . ' wyjście',
        ];
        foreach ($rows as $k => $v) { echo '<tr><th style="width:260px">' . esc_html($k) . '</th><td>' . $v . '</td></tr>'; }
        echo '</tbody></table>';
        echo '<p class="zpte-actions" style="margin-top:14px">'
            . self::button('run', 'Sprawdź teraz', [], 'button button-primary')
            . self::button('test', 'Sprawdź połączenie z OpenAI')
            . ($alert ? self::button('clear_alert', 'Ukryj komunikat o błędzie') : '')
            . '</p>';
        echo '</div>';

        $counts = [
            'Opublikowane wersje EN nowych treści' => Store::count_paths(['kind' => ['new', 'older'], 'status' => 'published']),
            'Szkice do akceptacji' => Store::count_paths(['status' => 'draft']),
            'Z błędem (ponowię)' => Store::count_paths(['status' => 'error']),
            'Istniejące strony EN z uzupełnionymi brakami' => Store::count_paths(['kind' => 'existing', 'ai' => true]),
        ];
        echo '<div class="zpte-box"><h2 style="margin-top:0">Wersje angielskie</h2><table class="widefat striped" style="max-width:760px"><tbody>';
        foreach ($counts as $k => $v) { echo '<tr><th style="width:360px">' . esc_html($k) . '</th><td>' . number_format_i18n($v) . '</td></tr>'; }
        echo '</tbody></table><p><a href="' . esc_url(self::url(['tab' => 'pages'])) . '">Lista stron →</a></p>';
        echo '<p class="description">Jak to działa: nowa polska treść dostaje angielski adres (np. /en/websites/…), przetłumaczony tytuł, opis, treść i dane dla Google, a wersje PL i EN wskazują się nawzajem (hreflang). Słownik dostarczony z wtyczką i ręczne poprawki z Ustawienia → Języki PL/EN zawsze mają pierwszeństwo przed tłumaczeniem maszynowym.</p></div>';
    }

    private static function status_label(string $status): string {
        $map = ['queued' => 'w kolejce', 'working' => 'w trakcie', 'draft' => 'szkic', 'published' => 'opublikowana', 'error' => 'błąd', 'skipped' => 'pominięta', 'off' => 'wyłączona'];
        return $map[$status] ?? $status;
    }

    private static function tab_pages(): void {
        $filters = [
            'new' => ['Nowe i starsze treści', ['kind' => ['new', 'older']]],
            'draft' => ['Szkice', ['status' => 'draft']],
            'error' => ['Błędy', ['status' => 'error']],
            'existing' => ['Istniejące strony EN', ['kind' => 'existing']],
            'all' => ['Wszystkie', []],
        ];
        $f = sanitize_key($_GET['f'] ?? 'new');
        if (!isset($filters[$f])) { $f = 'new'; }
        $where = $filters[$f][1];
        $paged = max(1, (int) ($_GET['paged'] ?? 1));
        $total = Store::count_paths($where);
        $rows = Store::paths($where, self::PER_PAGE, ($paged - 1) * self::PER_PAGE, 'queued DESC, id DESC');

        echo '<ul class="subsubsub" style="float:none">';
        $links = [];
        foreach ($filters as $k => [$label, $w]) {
            $links[] = '<li><a href="' . esc_url(self::url(['tab' => 'pages', 'f' => $k])) . '"' . ($k === $f ? ' class="current"' : '') . '>' . esc_html($label) . ' <span class="count">(' . number_format_i18n(Store::count_paths($w)) . ')</span></a></li>';
        }
        echo implode(' | ', $links) . '</ul>';

        echo '<div class="zpte-box"><form method="post" action="' . esc_url(admin_url('admin-post.php')) . '"><input type="hidden" name="action" value="zpte"><input type="hidden" name="do" value="add">';
        wp_nonce_field('zpte');
        echo '<label for="zpte-add"><strong>Przetłumacz konkretną stronę</strong> (polski adres, np. <code>/strony-www/nazwa-wpisu/</code>)</label><br><input id="zpte-add" name="path" class="regular-text code" placeholder="/polski-adres/"> <button class="button">Dodaj do kolejki</button></form></div>';

        if (!$rows) { echo '<p><em>Brak stron w tym widoku.</em></p>'; return; }
        echo '<div class="zpte-scroll"><table class="widefat striped"><thead><tr><th>Strona PL</th><th>Wersja EN</th><th>Status</th><th>Fragmenty</th><th>Sprawdzono</th><th>Akcje</th></tr></thead><tbody>';
        foreach ($rows as $r) {
            $en = (string) $r->en_path !== '' ? (string) $r->en_path : \ZPL\Router::en_path((string) $r->path);
            $kind = ['new' => 'nowa', 'older' => 'starsza', 'existing' => 'istniejąca EN'][$r->kind] ?? $r->kind;
            echo '<tr><td><a href="' . esc_url(home_url((string) $r->path)) . '" target="_blank" rel="noopener">' . esc_html(rawurldecode((string) $r->path)) . '</a><br><small>' . esc_html($kind) . ((int) $r->post_id ? ' · <a href="' . esc_url((string) get_edit_post_link((int) $r->post_id)) . '">edytuj wpis</a>' : '') . '</small></td>';
            echo '<td><a href="' . esc_url(home_url($en) . ($r->status === 'draft' ? '?zpte_preview=1' : '')) . '" target="_blank" rel="noopener">' . esc_html($en) . '</a>' . ($r->status === 'draft' ? '<br><small>podgląd dla administratora</small>' : '') . '</td>';
            echo '<td>' . esc_html(self::status_label((string) $r->status)) . ((int) $r->queued ? '<br><small>w kolejce</small>' : '') . ($r->message ? '<br><small>' . esc_html((string) $r->message) . '</small>' : '') . '</td>';
            echo '<td>' . number_format_i18n((int) $r->keys_ai) . ' maszynowo z ' . number_format_i18n((int) $r->keys_total) . ((int) $r->keys_failed ? '<br><small class="zpte-bad">' . number_format_i18n((int) $r->keys_failed) . ' do ponowienia</small>' : '') . '</td>';
            echo '<td>' . esc_html(self::when($r->checked_at)) . '</td><td class="zpte-actions">';
            $hid = ['id' => (int) $r->id];
            if ($r->status === 'draft') { echo self::button('publish', 'Opublikuj', $hid, 'button button-primary'); }
            if ($r->status !== 'off') { echo self::button('queue', 'Przetłumacz ponownie', $hid); }
            if ((int) $r->keys_ai > 0) { echo '<a class="button" href="' . esc_url(self::url(['tab' => 'edit', 'id' => (int) $r->id])) . '">Popraw tłumaczenie</a> '; }
            echo $r->status === 'off'
                ? self::button('on', 'Włącz', $hid)
                : self::button('off', 'Wyłącz', $hid, 'button', 'Wyłączyć tłumaczenie maszynowe tej strony? Angielski adres dodany przez tłumacza zostanie usunięty.');
            echo '</td></tr>';
        }
        echo '</tbody></table></div>';
        $pages = (int) ceil($total / self::PER_PAGE);
        if ($pages > 1) {
            echo '<p>';
            for ($i = 1; $i <= $pages; $i++) { echo $i === $paged ? '<strong>' . $i . '</strong> ' : '<a href="' . esc_url(self::url(['tab' => 'pages', 'f' => $f, 'paged' => $i])) . '">' . $i . '</a> '; }
            echo '</p>';
        }
    }

    private static function tab_edit(int $id): void {
        $row = Store::path($id);
        if (!$row) { echo '<p>Nie ma takiej strony.</p>'; return; }
        $paged = max(1, (int) ($_GET['paged'] ?? 1));
        $per = 100;
        $total = Store::count_strings_of($id);
        $strings = Store::strings_of($id, $per, ($paged - 1) * $per);
        echo '<p><a href="' . esc_url(self::url(['tab' => 'pages'])) . '">← Lista stron</a></p>';
        echo '<h2>' . esc_html(rawurldecode((string) $row->path)) . '</h2>';
        echo '<p class="description">Poprawione fragmenty są zapamiętywane jako ręczne i ponowne tłumaczenie ich nie nadpisze. Znaczniki <code>&lt;1&gt;…&lt;/1&gt;</code> i <code>&lt;2/&gt;</code> oznaczają linki i pogrubienia — muszą zostać.</p>';
        if (!$strings) { echo '<p><em>Ta strona nie ma tłumaczeń maszynowych.</em></p>'; return; }
        echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '"><input type="hidden" name="action" value="zpte"><input type="hidden" name="do" value="edit"><input type="hidden" name="id" value="' . (int) $id . '"><input type="hidden" name="paged" value="' . (int) $paged . '">';
        wp_nonce_field('zpte');
        echo '<div class="zpte-scroll"><table class="widefat striped"><thead><tr><th style="width:45%">Polski</th><th>Angielski</th></tr></thead><tbody>';
        foreach ($strings as $s) {
            echo '<tr><td><code style="white-space:pre-wrap">' . esc_html((string) $s->pl) . '</code><br><small>' . esc_html((string) $s->kind) . ((int) $s->manual ? ' · poprawione ręcznie' : '') . '</small></td>'
                . '<td><textarea name="t[' . (int) $s->id . ']" rows="' . max(2, min(10, (int) ceil(strlen((string) $s->en) / 90))) . '">' . esc_textarea((string) $s->en) . '</textarea>'
                . '<input type="hidden" name="o[' . (int) $s->id . ']" value="' . esc_attr(md5((string) $s->en)) . '"></td></tr>';
        }
        echo '</tbody></table></div>';
        submit_button('Zapisz poprawki');
        echo '</form>';
        $pages = (int) ceil($total / $per);
        if ($pages > 1) {
            echo '<p>';
            for ($i = 1; $i <= $pages; $i++) { echo $i === $paged ? '<strong>' . $i . '</strong> ' : '<a href="' . esc_url(self::url(['tab' => 'edit', 'id' => $id, 'paged' => $i])) . '">' . $i . '</a> '; }
            echo '</p>';
        }
    }

    private static function tab_settings(): void {
        $s = Settings::all();
        $types = Settings::public_types();
        echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '" autocomplete="off"><input type="hidden" name="action" value="zpte"><input type="hidden" name="do" value="save">';
        wp_nonce_field('zpte');
        echo '<div class="zpte-box"><h2 style="margin-top:0">Klucz API OpenAI</h2>';
        if (Settings::key_source() === 'constant') {
            echo '<p>Klucz jest ustawiony w wp-config.php (stała <code>ZPTE_OPENAI_API_KEY</code>) — ' . esc_html(Settings::key_hint()) . '.</p>';
        } else {
            $hint = Settings::key_hint();
            echo '<p><input type="password" name="api_key" class="regular-text code" autocomplete="new-password" spellcheck="false" placeholder="' . esc_attr($hint !== '' ? 'zapisany: ' . $hint . ' — wklej nowy, aby zmienić' : 'sk-…') . '" style="max-width:100%"></p>';
            echo '<p class="description">Klucz utworzysz na platform.openai.com → API keys. Zapisuję go zaszyfrowany w bazie i nigdy nie pokazuję w całości. Puste pole zostawia obecny klucz.</p>';
            if ($hint !== '') { echo '<p><label><input type="checkbox" name="delete_key" value="1"> Usuń zapisany klucz</label></p>'; }
        }
        echo '</div>';

        echo '<div class="zpte-box"><h2 style="margin-top:0">Tłumaczenie</h2><table class="form-table" role="presentation"><tbody>';
        echo '<tr><th>Codzienne sprawdzanie</th><td><label><input type="checkbox" name="enabled" value="1"' . checked(!empty($s['enabled']), true, false) . '> Codziennie o 3:00 szukaj nowych treści i braków w wersji angielskiej</label></td></tr>';
        echo '<tr><th><label for="zpte-model">Model OpenAI</label></th><td><input id="zpte-model" name="model" list="zpte-models" class="regular-text code" value="' . esc_attr((string) $s['model']) . '"><datalist id="zpte-models"><option value="gpt-4.1"><option value="gpt-4.1-mini"><option value="gpt-4o"><option value="gpt-4o-mini"><option value="gpt-5"><option value="gpt-5-mini"></datalist><p class="description">Domyślnie gpt-4.1: bardzo dobra jakość tekstów marketingowych przy niskim koszcie (zwykle 10–20 groszy za wpis). „Sprawdź połączenie” na zakładce Status pokaże, czy model jest dostępny dla Twojego klucza.</p></td></tr>';
        echo '<tr><th>Nowe treści</th><td><label><input type="radio" name="mode" value="publish"' . checked($s['mode'] !== 'draft', true, false) . '> publikuj wersję angielską automatycznie</label><br><label><input type="radio" name="mode" value="draft"' . checked($s['mode'] === 'draft', true, false) . '> zapisz jako szkic — opublikuję ręcznie (Strony → Opublikuj)</label></td></tr>';
        echo '<tr><th>Rodzaje treści</th><td>';
        foreach ($types as $name => $label) { echo '<label style="margin-right:14px"><input type="checkbox" name="types[]" value="' . esc_attr($name) . '"' . checked(in_array($name, (array) $s['types'], true), true, false) . '> ' . esc_html($label) . '</label>'; }
        echo '</td></tr>';
        echo '<tr><th><label for="zpte-since">Nowe treści = opublikowane od</label></th><td><input id="zpte-since" type="date" name="since" value="' . esc_attr((string) $s['since']) . '"></td></tr>';
        echo '<tr><th>Starsze treści</th><td><label><input type="checkbox" name="older" value="1"' . checked(!empty($s['older']), true, false) . '> Tłumacz także starsze wpisy i strony, które nie mają jeszcze wersji angielskiej</label><p class="description">Dziś takie strony są pod adresem /en/… z polską treścią i noindex. Po tłumaczeniu dostają angielski adres, a stary /en/… przekierowuje na nowy.</p></td></tr>';
        echo '<tr><th>Braki na stronach EN</th><td><label><input type="checkbox" name="gaps" value="1"' . checked(!empty($s['gaps']), true, false) . '> Uzupełniaj nieprzetłumaczone fragmenty istniejących stron angielskich (nowe sekcje, stopka, menu)</label><p class="description">Codziennie sprawdzam ' . (int) Worker::ROTATION . ' stron EN, a po każdej aktualizacji ZP Suite — wszystkie.</p></td></tr>';
        echo '<tr><th><label for="zpte-limit">Dzienny limit</label></th><td><input id="zpte-limit" type="number" min="0" step="10000" name="daily_chars" value="' . (int) $s['daily_chars'] . '" class="small-text" style="width:120px"> znaków polskiego tekstu dziennie <p class="description">Bezpiecznik kosztów: 400 000 znaków to ok. 50 długich wpisów (przy gpt-4.1 ok. 1,5 USD). 0 = bez limitu.</p></td></tr>';
        echo '<tr><th><label for="zpte-instr">Dodatkowe instrukcje</label></th><td><textarea id="zpte-instr" name="instructions" rows="4" placeholder="np. Zwracaj się do klienta per you. „Wycena” tłumacz jako „quote”.">' . esc_textarea((string) $s['instructions']) . '</textarea></td></tr>';
        echo '</tbody></table></div>';
        submit_button('Zapisz ustawienia');
        echo '</form>';

        echo '<div class="zpte-box"><h2 style="margin-top:0">Dane tłumacza</h2><p>Usuwa wszystkie tłumaczenia maszynowe, listę stron i angielskie adresy dodane przez tłumacza. Ustawienia i klucz zostają.</p>'
            . self::button('purge', 'Usuń tłumaczenia maszynowe', [], 'button button-link-delete', 'Usunąć wszystkie tłumaczenia maszynowe? Strony EN tłumaczone przez OpenAI wrócą do stanu sprzed tłumaczenia.') . '</div>';
    }

    private static function tab_log(): void {
        $log = Log::all();
        if (!$log) { echo '<p><em>Dziennik jest pusty.</em></p>'; return; }
        echo '<div class="zpte-scroll"><table class="widefat striped" style="max-width:1100px"><thead><tr><th style="width:150px">Kiedy</th><th>Zdarzenie</th></tr></thead><tbody>';
        foreach ($log as $e) {
            $cls = ($e['l'] ?? '') === 'error' ? 'zpte-bad' : (($e['l'] ?? '') === 'warning' ? 'zpte-warn' : '');
            echo '<tr><td>' . esc_html(wp_date('Y-m-d H:i', (int) ($e['t'] ?? 0))) . '</td><td class="' . $cls . '">' . esc_html((string) ($e['m'] ?? '')) . '</td></tr>';
        }
        echo '</tbody></table></div>';
    }

    // ---------------------------------------------------------------- actions

    public static function handle(): void {
        if (!current_user_can('manage_options')) { wp_die('Brak uprawnień.'); }
        check_admin_referer('zpte');
        $do = sanitize_key($_POST['do'] ?? '');
        $id = (int) ($_POST['id'] ?? 0);
        $back = ['tab' => 'pages'];
        $msg = '';
        $err = '';
        Store::install();
        switch ($do) {
            case 'save':
                $back = ['tab' => 'settings'];
                $types = array_values(array_intersect(array_map('sanitize_key', (array) ($_POST['types'] ?? [])), array_keys(Settings::public_types())));
                $since = sanitize_text_field(wp_unslash((string) ($_POST['since'] ?? '')));
                $model = trim(sanitize_text_field(wp_unslash((string) ($_POST['model'] ?? ''))));
                Settings::update([
                    'enabled' => empty($_POST['enabled']) ? 0 : 1,
                    'model' => preg_match('~^[A-Za-z0-9._:-]{2,80}$~', $model) ? $model : Settings::DEFAULT_MODEL,
                    'mode' => ($_POST['mode'] ?? '') === 'draft' ? 'draft' : 'publish',
                    'types' => $types,
                    'since' => preg_match('~^\d{4}-\d{2}-\d{2}$~', $since) ? $since : (string) Settings::get('since'),
                    'older' => empty($_POST['older']) ? 0 : 1,
                    'gaps' => empty($_POST['gaps']) ? 0 : 1,
                    'daily_chars' => max(0, (int) ($_POST['daily_chars'] ?? 0)),
                    'instructions' => sanitize_textarea_field(wp_unslash((string) ($_POST['instructions'] ?? ''))),
                ]);
                $msg = 'Zapisano ustawienia.';
                if (!empty($_POST['delete_key'])) {
                    Settings::delete_key();
                    $msg .= ' Klucz usunięty.';
                }
                $key = trim((string) wp_unslash($_POST['api_key'] ?? ''));
                $saved = false;
                if ($key !== '') {
                    if (!Settings::looks_like_key($key)) {
                        $err = 'To nie wygląda na klucz API OpenAI (oczekiwany ciąg zaczynający się zwykle od „sk-”, bez spacji). Klucza nie zapisano.';
                    } else {
                        Settings::save_key($key);
                        Log::clear_alert();
                        $msg .= ' Klucz zapisany (' . Settings::key_hint() . ').';
                        Log::add('Zapisano nowy klucz API (' . Settings::key_hint() . ').');
                        $saved = true;
                    }
                }
                unset($key);
                Worker::maintain();
                if ($saved) {
                    // Check the key at once, and start the very first check so new content is translated today.
                    $r = OpenAI::models();
                    if (!$r['ok']) {
                        $err = 'Klucz zapisany, ale OpenAI go nie przyjmuje: ' . $r['error'];
                    } elseif (Settings::get('enabled') && !get_option('zpte_last_plan')) {
                        $msg .= ' ' . Worker::plan('admin');
                    }
                }
                break;
            case 'test':
                $back = ['tab' => 'status'];
                $r = OpenAI::models();
                if (!$r['ok']) { $err = 'Połączenie z OpenAI nie działa: ' . $r['error']; break; }
                $model = Settings::model();
                $gpt = array_values(array_filter($r['ids'], static function ($m) { return (bool) preg_match('~^(?:gpt-|o\d|chatgpt)~', $m); }));
                if (in_array($model, $r['ids'], true)) {
                    Log::clear_alert();
                    $msg = 'Połączenie działa, klucz jest poprawny, model ' . $model . ' jest dostępny.';
                } else {
                    $err = 'Klucz działa, ale model ' . $model . ' nie jest dostępny. Dostępne modele GPT: ' . implode(', ', array_slice($gpt, 0, 25)) . '.';
                }
                break;
            case 'run':
                $back = ['tab' => 'status'];
                $summary = Worker::plan('admin');
                $msg = $summary . (Store::queued_count() > 0 ? ' Tłumaczę w tle — odśwież tę stronę za kilka minut.' : '');
                break;
            case 'clear_alert':
                $back = ['tab' => 'status'];
                Log::clear_alert();
                break;
            case 'add':
                $raw = trim((string) wp_unslash($_POST['path'] ?? ''));
                $p = (string) wp_parse_url($raw, PHP_URL_PATH);
                $path = \ZPL\Router::norm_path(\ZPL\Router::rel($p !== '' ? $p : $raw));
                $why = Bridge::ineligible($path);
                if ($raw === '' || $why !== '') { $err = 'Tego adresu nie tłumaczę' . ($why !== '' ? ': ' . $why : '') . '.'; break; }
                $pid = url_to_postid(home_url($path));
                $row = Store::path_by_path($path);
                if (!$row) {
                    $kind = Bridge::shipped($path) ? 'existing' : 'older';
                    $nid = Store::path_add($path, ['post_id' => max(0, (int) $pid), 'kind' => $kind, 'status' => $kind === 'existing' ? 'published' : 'queued']);
                    $row = Store::path($nid);
                }
                if ($row) {
                    if ($row->status === 'off' || $row->status === 'skipped') { Store::path_update((int) $row->id, ['status' => $row->kind === 'existing' ? 'published' : 'queued', 'tries' => 0]); }
                    Store::queue((int) $row->id, 5);
                    Worker::kick(0);
                    $msg = 'Dodano do kolejki: ' . $path . '. Tłumaczę w tle — odśwież za kilka minut.';
                }
                break;
            case 'queue':
                $row = Store::path($id);
                if ($row) {
                    if (in_array($row->status, ['skipped', 'error'], true)) { Store::path_update($id, ['status' => $row->kind === 'existing' ? 'published' : 'queued', 'tries' => 0]); }
                    Store::path_update($id, ['source_modified' => null]);
                    Store::queue($id, 5);
                    Worker::kick(0);
                    $msg = 'Dodano do kolejki. Tłumaczę w tle — odśwież za kilka minut.';
                }
                break;
            case 'publish':
                $row = Store::path($id);
                if ($row && $row->status === 'draft') {
                    // The worker gives it an English address and publishes it (mode is ignored for an approved draft).
                    Store::path_update($id, ['status' => 'error', 'tries' => 0, 'message' => 'Zaakceptowano — publikuję.']);
                    self::approve($id);
                    $msg = 'Opublikowano wersję angielską.';
                }
                break;
            case 'off':
                $row = Store::path($id);
                if ($row) {
                    Sync::retire($row, 'Wyłączono ręcznie.', 'off');
                    $msg = 'Wyłączono tłumaczenie maszynowe tej strony.';
                }
                break;
            case 'on':
                $row = Store::path($id);
                if ($row) {
                    Store::path_update($id, ['status' => $row->kind === 'existing' ? 'published' : 'queued', 'tries' => 0, 'source_modified' => null, 'message' => '']);
                    Store::queue($id, 5);
                    Worker::kick(0);
                    $msg = 'Włączono — tłumaczę w tle.';
                }
                break;
            case 'edit':
                $back = ['tab' => 'edit', 'id' => $id, 'paged' => max(1, (int) ($_POST['paged'] ?? 1))];
                $row = Store::path($id);
                if (!$row) { break; }
                $n = 0;
                $bad = 0;
                $current = [];
                foreach (Store::strings_of($id, 100000) as $s) { $current[(int) $s->id] = $s; }
                foreach ((array) ($_POST['t'] ?? []) as $sid => $text) {
                    $sid = (int) $sid;
                    if (!isset($current[$sid])) { continue; }
                    $en = \ZPL\Html::norm((string) wp_unslash($text));
                    $orig = (string) ($_POST['o'][$sid] ?? '');
                    if ($en === '' || md5((string) $current[$sid]->en) !== $orig || $en === (string) $current[$sid]->en) { continue; }
                    $pl = (string) $current[$sid]->pl;
                    if ((preg_match('~</?\d+/?>~', $pl) || preg_match('~</?\d+/?>~', $en)) && !\ZPL\Html::markers_match($pl, $en)) { $bad++; continue; }
                    Store::string_edit($sid, $en);
                    $n++;
                }
                if ($n) {
                    Bridge::rebuild_index();
                    Bridge::purge([(string) $row->path, (string) $row->en_path !== '' ? (string) $row->en_path : \ZPL\Router::en_path((string) $row->path)], (int) $row->post_id);
                }
                $msg = 'Zapisano poprawki: ' . $n . '.';
                if ($bad) { $err = 'Nie zapisano poprawek: ' . $bad . ' — znaczniki <1>…</1> muszą się zgadzać z tekstem polskim.'; }
                break;
            case 'purge':
                $back = ['tab' => 'settings'];
                foreach (Store::paths(['kind' => ['new', 'older']]) as $row) {
                    if ((int) $row->route_added && (string) $row->en_path !== '') { Bridge::remove_route((string) $row->path, (string) $row->en_path); }
                }
                Store::drop_all();
                Store::install();
                delete_option(Bridge::INDEX);
                delete_option('zpte_suite_seen');
                Log::add('Usunięto wszystkie tłumaczenia maszynowe.');
                do_action('litespeed_purge_all');
                $msg = 'Usunięto tłumaczenia maszynowe.';
                break;
        }
        $args = $back;
        if ($msg !== '') { $args['msg'] = rawurlencode($msg); }
        if ($err !== '') { $args['err'] = rawurlencode($err); }
        wp_safe_redirect(self::url($args));
        exit;
    }

    /** Publish an approved draft: English address + status, without another translation pass. */
    private static function approve(int $id): void {
        $row = Store::path($id);
        if (!$row) { return; }
        $post = (int) $row->post_id > 0 ? get_post((int) $row->post_id) : null;
        $html = '';
        if (!$post && Bridge::route((string) $row->path) === '') {
            $src = Source::fetch((string) $row->path);
            $html = $src['ok'] ? $src['html'] : '';
        }
        $data = Sync::ensure_route($row, (string) $row->path, $html, $post);
        Store::path_update($id, array_merge($data, ['status' => 'published', 'queued' => 0, 'tries' => 0, 'message' => 'Opublikowano po akceptacji.']));
        Store::recompute_globals();
        Bridge::rebuild_index();
        $fresh = Store::path($id);
        Bridge::purge([(string) $row->path, $fresh ? (string) $fresh->en_path : ''], (int) $row->post_id);
        Log::add('Zaakceptowano szkic: ' . $row->path . ' → ' . ($fresh ? $fresh->en_path : '') . '.');
    }
}
