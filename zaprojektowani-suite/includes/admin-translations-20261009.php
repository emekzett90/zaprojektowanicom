<?php
/** Single translation workspace. Existing editors, handlers and dictionaries remain authoritative. */
if (!defined('ABSPATH')) { exit; }

function zp_admin_translations_20261009_tabs(): array {
    return ['overview' => 'Przegląd', 'queue' => 'Kolejka', 'attention' => 'Wymagają uwagi', 'corrections' => 'Korekty', 'scanner' => 'Skaner', 'language' => 'Ustawienia języka'];
}

function zp_admin_translations_20261009_url(string $tab = 'overview'): string {
    $tabs = zp_admin_translations_20261009_tabs();
    return add_query_arg(['page' => 'zp-suite-translations', 'tab' => isset($tabs[$tab]) ? $tab : 'overview'], admin_url('admin.php'));
}

/** Hiding a menu entry does not unregister its page callback or any existing POST/AJAX handler. */
function zp_admin_translations_20261009_menu(): void {
    add_submenu_page('zp-suite', 'Tłumaczenia', 'Tłumaczenia', 'manage_options', 'zp-suite-translations', 'zp_admin_translations_20261009_page');
}

function zp_admin_translations_20261009_consolidate_menu(): void {
    foreach (['zp-suite', 'options-general.php'] as $parent) {
        foreach (['zp-tlumacz-en', 'zpl-languages', 'zpl-skaner'] as $slug) { remove_submenu_page($parent, $slug); }
    }
    remove_menu_page('zpl-skaner');
    // WP resolves a removed submenu as admin_page_* instead of its old Suite hook.
    // Register the same native callback as a hidden screen on that exact request.
    // A parent with an empty slug has no visible sidebar entry. All old hooks remain.
    $page = isset($_GET['page']) && is_string($_GET['page']) ? sanitize_key(wp_unslash($_GET['page'])) : '';
    if ($page === 'zp-tlumacz-en' && class_exists('ZPTE\\Admin')) {
        add_submenu_page('', 'Tłumacz EN', 'Tłumacz EN', 'manage_options', $page, ['ZPTE\\Admin', 'page']);
    } elseif ($page === 'zpl-languages' && class_exists('ZPL\\Admin')) {
        add_submenu_page('', 'Języki PL/EN', 'Języki PL/EN', 'manage_options', $page, ['ZPL\\Admin', 'page']);
    }
}
add_action('admin_menu', 'zp_admin_translations_20261009_menu', 10010);
add_action('admin_menu', 'zp_admin_translations_20261009_consolidate_menu', PHP_INT_MAX - 5);

/** Exact scanner registration, rather than guessing that its separately installed plugin is active. */
function zp_admin_translations_20261009_scanner_available(): bool {
    global $_registered_pages;
    foreach (array_keys(is_array($_registered_pages) ? $_registered_pages : []) as $hook) {
        if (preg_match('/_page_zpl-skaner$/D', (string) $hook)) { return true; }
    }
    return false;
}

/** Effective lookup order mirrors ZPL\Dict::exact(); no duplicate data is deleted or merged. */
function zp_admin_translations_20261009_dictionary_sources(): array {
    return [
        ['key' => 'overrides', 'label' => 'Ręczne korekty języka', 'detail' => 'Dokładne wpisy z „Korekt” mają pierwszeństwo.'],
        ['key' => 'page', 'label' => 'Słownik konkretnej strony', 'detail' => 'Gotowe tłumaczenia dostarczone z wtyczką.'],
        ['key' => 'common', 'label' => 'Wspólny słownik', 'detail' => 'Stałe elementy wspólne dla wielu stron.'],
        ['key' => 'seo', 'label' => 'Słowniki treści SEO', 'detail' => 'Uzupełnienia seo-230 i seo-271, w tej kolejności.'],
        ['key' => 'index', 'label' => 'Pozostałe gotowe słowniki', 'detail' => 'Dokładny fragment odnaleziony w indeksie słowników.'],
        ['key' => 'memory', 'label' => 'Pamięć tłumacza', 'detail' => 'Wersje AI i korekty fragmentów z edytora kolejki uzupełniają braki po gotowych słownikach.'],
    ];
}

/** Read-only status. Deliberately avoids progress()/headline(): they decrypt the API key. */
function zp_admin_translations_20261009_data(): array {
    if (!current_user_can('manage_options')) { return ['authorized' => false]; }
    $data = [
        'authorized' => true, 'language_available' => class_exists('ZPL\\Router'),
        'worker_available' => class_exists('ZPTE\\Worker'), 'database_ready' => false,
        'bridge_ready' => class_exists('ZPTE\\Bridge') && \ZPTE\Bridge::hooks_ready(),
        'scanner_available' => zp_admin_translations_20261009_scanner_available(),
        'queued' => null, 'attention' => null, 'published_ai' => null, 'drafts' => null,
        'missing_pages' => null, 'manual_corrections' => null, 'missing_fragments' => null,
        'mapped_routes' => null, 'queue_rows' => [], 'attention_rows' => [], 'state' => 'unknown', 'running' => false,
        'last_run' => ['total' => 0, 'done' => 0, 'errors' => 0, 'updated_at' => 0, 'finished_at' => 0, 'retry_at' => 0],
        'next_daily' => 0, 'last_plan' => max(0, (int) get_option('zpte_last_plan', 0)),
        'daily_enabled' => null, 'daily_chars' => null, 'model' => '', 'mode' => '',
        'usage' => null, 'geo_enabled' => null, 'polish_stays' => null,
    ];
    if (class_exists('ZPL\\Dict')) { $data['manual_corrections'] = count(\ZPL\Dict::overrides('en')); }
    if (class_exists('ZPL\\Missing')) { $data['missing_fragments'] = count(\ZPL\Missing::all()); }
    if ($data['language_available']) { $data['mapped_routes'] = count(\ZPL\Router::routes()); }
    if (class_exists('ZPL\\Geo')) {
        $geo = \ZPL\Geo::settings();
        $data['geo_enabled'] = (bool) $geo['enabled']; $data['polish_stays'] = (bool) $geo['polish_stays'];
    }
    if (class_exists('ZPTE\\Settings')) {
        $settings = \ZPTE\Settings::all();
        $data['daily_enabled'] = !empty($settings['enabled']);
        $data['daily_chars'] = max(0, (int) $settings['daily_chars']);
        $data['model'] = substr((string) $settings['model'], 0, 100);
        $data['mode'] = (string) $settings['mode'] === 'draft' ? 'draft' : 'publish';
    }
    if ($data['worker_available']) {
        $state = \ZPTE\Worker::state();
        $allowed_states = ['idle', 'queued', 'running', 'completed', 'completed_errors', 'blocked', 'retry', 'limit'];
        $data['state'] = in_array($state['state'] ?? '', $allowed_states, true) ? $state['state'] : 'unknown';
        $data['running'] = \ZPTE\Worker::running();
        foreach ($data['last_run'] as $key => $value) { $data['last_run'][$key] = max(0, (int) ($state[$key] ?? 0)); }
        $data['next_daily'] = (int) wp_next_scheduled(\ZPTE\Worker::DAILY);
    }
    if (class_exists('ZPTE\\Log')) {
        $usage = \ZPTE\Log::usage(); $data['usage'] = [];
        foreach (['chars', 'requests', 'in', 'out'] as $key) { $data['usage'][$key] = max(0, (int) ($usage[$key] ?? 0)); }
    }
    // The existing worker owns installation/migrations. A status page must not install tables.
    if (class_exists('ZPTE\\Store') && get_option('zpte_db') === \ZPTE\Store::DB_VERSION) {
        global $wpdb;
        $data['database_ready'] = true;
        $counts = [
            'queued' => null, 'attention' => ['attention' => true],
            'published_ai' => ['kind' => ['new', 'older'], 'status' => 'published'],
            'drafts' => ['status' => 'draft'],
        ];
        foreach ($counts as $key => $where) {
            $count = $where === null ? \ZPTE\Store::queued_count() : \ZPTE\Store::count_paths($where);
            if (!empty($wpdb->last_error)) { $data['database_ready'] = false; break; }
            $data[$key] = max(0, (int) $count);
        }
        if ($data['database_ready']) {
            $data['queue_rows'] = \ZPTE\Store::paths(['queued' => true, 'status' => ['queued', 'working', 'published', 'draft', 'error', 'skipped']], 20, 0, 'priority ASC, id ASC');
            if (!empty($wpdb->last_error)) { $data['database_ready'] = false; }
            if ($data['database_ready']) {
                $data['attention_rows'] = \ZPTE\Store::paths(['attention' => true], 20, 0, 'checked_at DESC, id DESC');
                if (!empty($wpdb->last_error)) { $data['database_ready'] = false; }
            }
        }
        if (!$data['database_ready']) { foreach (array_keys($counts) as $key) { $data[$key] = null; } $data['queue_rows'] = []; $data['attention_rows'] = []; }
    }
    if ($data['database_ready'] && $data['bridge_ready'] && class_exists('ZPTE\\Admin')) {
        $data['missing_pages'] = count(\ZPTE\Admin::missing_pages());
    }
    return $data;
}

function zp_admin_translations_20261009_number($number): string {
    return $number === null ? '—' : number_format_i18n((int) $number);
}

function zp_admin_translations_20261009_when(int $timestamp): string {
    return $timestamp > 0 ? wp_date('j.m.Y H:i', $timestamp) : 'Brak zapisanego terminu';
}

function zp_admin_translations_20261009_checked($gmt): string {
    if (!is_string($gmt) || $gmt === '') { return '—'; }
    $date = DateTimeImmutable::createFromFormat('!Y-m-d H:i:s', $gmt, new DateTimeZone('UTC'));
    $errors = DateTimeImmutable::getLastErrors();
    return $date && (!$errors || (!$errors['warning_count'] && !$errors['error_count'])) ? wp_date('j.m.Y H:i', $date->getTimestamp()) : '—';
}

function zp_admin_translations_20261009_legacy_url(string $page, string $tab = '', array $extra = []): string {
    $allowed = ['zp-tlumacz-en', 'zpl-languages', 'zpl-skaner'];
    if (!in_array($page, $allowed, true)) { return zp_admin_translations_20261009_url(); }
    $args = ['page' => $page];
    if ($tab !== '') { $args['tab'] = $tab; }
    foreach (['f', 'id'] as $key) { if (isset($extra[$key])) { $args[$key] = $extra[$key]; } }
    return add_query_arg($args, admin_url('admin.php'));
}

function zp_admin_translations_20261009_link(string $page, string $tab, string $label, array $extra = []): void {
    $available = ($page === 'zp-tlumacz-en' && class_exists('ZPTE\\Admin'))
        || ($page === 'zpl-languages' && class_exists('ZPL\\Admin'))
        || ($page === 'zpl-skaner' && zp_admin_translations_20261009_scanner_available());
    if (!$available) { echo '<span class="zpAdmin20__muted">' . esc_html($label . ' — moduł niedostępny') . '</span>'; return; }
    echo '<a class="button" href="' . esc_url(zp_admin_translations_20261009_legacy_url($page, $tab, $extra)) . '">' . esc_html($label) . '</a>';
}

function zp_admin_translations_20261009_metrics(array $data): void {
    echo '<div class="zpAdmin20__metrics">';
    foreach (['queued' => 'Strony w kolejce', 'attention' => 'Strony wymagające uwagi', 'missing_pages' => 'Opublikowane strony bez EN', 'manual_corrections' => 'Ręczne korekty języka'] as $key => $label) {
        echo '<div class="zpAdmin20__metric"><strong>' . esc_html(zp_admin_translations_20261009_number($data[$key])) . '</strong><span>' . esc_html($label) . '</span></div>';
    }
    echo '</div>';
}

/** No embedded legacy renderer: its forms/assets/polling keep their original screen and nonce context. */
function zp_admin_translations_20261009_content(string $tab = 'overview'): void {
    if (!current_user_can('manage_options')) { return; }
    $tabs = zp_admin_translations_20261009_tabs();
    if (!isset($tabs[$tab])) { $tab = 'overview'; }
    $data = zp_admin_translations_20261009_data();
    echo '<nav class="zpAdmin20__tabs" aria-label="Narzędzia tłumaczeń">';
    foreach ($tabs as $key => $label) {
        echo '<a href="' . esc_url(zp_admin_translations_20261009_url($key)) . '"' . ($tab === $key ? ' class="is-active" aria-current="page"' : '') . '>' . esc_html($label) . '</a>';
    }
    echo '</nav>';
    if (in_array($tab, ['overview', 'queue', 'attention'], true)) {
        zp_admin_translations_20261009_metrics($data);
        $states = ['idle' => 'Jeszcze nie uruchomiono', 'queued' => 'Oczekuje', 'running' => 'Praca w toku', 'completed' => 'Ostatni przebieg zakończony', 'completed_errors' => 'Ostatni przebieg zakończony z brakami', 'blocked' => 'Wstrzymany', 'retry' => 'Oczekuje na ponowienie', 'limit' => 'Osiągnięto limit', 'unknown' => 'Brak danych'];
        echo '<section class="zpAdmin20__card"><h2>Stan tłumacza</h2><p class="zpAdmin20__status">' . esc_html($states[$data['state']]) . '</p>';
        echo '<p>Aktywny krok: <strong>' . esc_html($data['running'] ? 'tak' : 'nie') . '</strong>. Codzienne sprawdzanie: <strong>' . esc_html($data['daily_enabled'] === null ? 'brak danych' : ($data['daily_enabled'] ? 'włączone' : 'wyłączone')) . '</strong>.</p>';
        echo '<p>Ostatnie sprawdzenie stron: ' . esc_html(zp_admin_translations_20261009_when($data['last_plan'])) . '. Następny termin: ' . esc_html(zp_admin_translations_20261009_when($data['next_daily'])) . '.</p>';
        $run = $data['last_run'];
        echo '<p class="zpAdmin20__muted">Zapisany przebieg: ' . esc_html(zp_admin_translations_20261009_number($run['done']) . ' z ' . zp_admin_translations_20261009_number($run['total']) . ' stron; błędy: ' . zp_admin_translations_20261009_number($run['errors'])) . '. To wynik przebiegu, a liczniki powyżej opisują bieżące dane.</p>';
        if ($run['retry_at']) { echo '<p>Ponowienie od: ' . esc_html(zp_admin_translations_20261009_when($run['retry_at'])) . '.</p>'; }
        if (!$data['database_ready']) { echo '<p>Stan bazy tłumacza jest niedostępny. Otwórz jego natywny ekran, aby sprawdzić moduł. Znak „—” oznacza brak danych.</p>'; }
        if (!$data['bridge_ready']) { echo '<p>Połączenie tłumacza z modułem języka jest niedostępne. Sprawdź natywne ustawienia modułu.</p>'; }
        echo '<div class="zpAdmin20__actions">';
        zp_admin_translations_20261009_link('zp-tlumacz-en', 'status', 'Otwórz sterowanie i postęp kolejki');
        zp_admin_translations_20261009_link('zp-tlumacz-en', 'pages', 'Strony i błędy', ['f' => 'error']);
        zp_admin_translations_20261009_link('zp-tlumacz-en', 'log', 'Dziennik tłumaczeń');
        echo '</div><p class="zpAdmin20__muted">Stan z chwili otwarcia tego panelu. Postęp na żywo i automatyczne ponawianie w przeglądarce działają na ekranie sterowania kolejką.</p></section>';
        if ($tab === 'attention') {
            echo '<section class="zpAdmin20__card"><h2>Strony wymagające sprawdzenia</h2><p class="zpAdmin20__muted">Błąd strony, brakujące fragmenty lub zapisane nieudane próby. Otwarcie tej listy niczego nie tłumaczy i nie ponawia pracy API. Licznik może wymagać ponownego sprawdzenia konkretnej strony po ręcznej korekcie.</p><div class="zpAdmin20__actions">';
            zp_admin_translations_20261009_link('zp-tlumacz-en', 'pages', 'Pełna natywna lista błędów', ['f' => 'error']);
            zp_admin_translations_20261009_link('zpl-languages', 'overrides', 'Ręczne korekty języka');
            echo '</div>';
            if (!$data['database_ready']) { echo '<p>Brak dostępnych danych; nie przyjęto zerowego wyniku.</p>'; }
            elseif (!$data['attention_rows']) { echo '<p>Nie ma stron oznaczonych jako wymagające uwagi. To stan zapisów tłumacza, a nie potwierdzenie pełnego audytu każdej strony.</p>'; }
            else {
                $labels = ['queued' => 'oczekuje', 'working' => 'w trakcie', 'published' => 'opublikowana', 'draft' => 'szkic', 'error' => 'błąd', 'skipped' => 'pominięta', 'off' => 'wyłączona'];
                echo '<div class="zpAdmin20__tableWrap"><table class="widefat striped zpAdmin20__table"><thead><tr><th>Polski adres</th><th>Stan strony</th><th>Brakujące fragmenty</th><th>Nieudane próby</th><th>Ostatnio sprawdzono</th><th>Szczegóły</th></tr></thead><tbody>';
                foreach ($data['attention_rows'] as $row) {
                    $status = (string) ($row->status ?? '');
                    echo '<tr><td><code>' . esc_html((string) ($row->path ?? '')) . '</code></td><td>' . esc_html($labels[$status] ?? $status) . '</td><td>' . esc_html(zp_admin_translations_20261009_number(max(0, (int) ($row->keys_failed ?? 0)))) . '</td><td>' . esc_html(zp_admin_translations_20261009_number(max(0, (int) ($row->tries ?? 0)))) . '</td><td>' . esc_html(zp_admin_translations_20261009_checked($row->checked_at ?? null)) . '</td><td>';
                    zp_admin_translations_20261009_link('zp-tlumacz-en', 'edit', 'Sprawdź fragmenty', ['id' => max(0, (int) ($row->id ?? 0))]);
                    echo '</td></tr>';
                }
                echo '</tbody></table></div><p class="zpAdmin20__muted">Pierwsze 20 zapisów, od ostatnio sprawdzanych. Wszystkie strony znajdziesz w natywnej liście błędów. Edytor fragmentów pokazuje zapisane tłumaczenia; nie przechowuje treści nieudanych odpowiedzi API.</p>';
            }
            echo '</section>';
        } elseif ($tab === 'queue') {
            echo '<section class="zpAdmin20__card"><h2>Najbliższe strony w kolejce</h2>';
            if (!$data['database_ready']) { echo '<p>Brak dostępnych danych kolejki.</p>'; }
            elseif (!$data['queue_rows']) { echo '<p>Nie ma stron oczekujących w kolejce.</p>'; }
            else {
                echo '<div class="zpAdmin20__tableWrap"><table class="widefat striped zpAdmin20__table"><thead><tr><th>Polski adres</th><th>Stan zapisu</th><th>Fragmenty AI / wszystkie</th><th>Korekta</th></tr></thead><tbody>';
                foreach ($data['queue_rows'] as $row) {
                    echo '<tr><td><code>' . esc_html((string) $row->path) . '</code></td><td>' . esc_html((string) $row->status) . '</td><td>' . esc_html(zp_admin_translations_20261009_number($row->keys_ai ?? 0) . ' / ' . zp_admin_translations_20261009_number($row->keys_total ?? 0)) . '</td><td>';
                    zp_admin_translations_20261009_link('zp-tlumacz-en', 'edit', 'Otwórz fragmenty', ['id' => max(0, (int) $row->id)]);
                    echo '</td></tr>';
                }
                echo '</tbody></table></div><p class="zpAdmin20__muted">Pierwsze 20 pozycji według priorytetu kolejki. Liczba fragmentów AI nie oznacza całego słownika strony.</p>';
            }
            echo '</section>';
        } else {
            echo '<div class="zpAdmin20__grid"><section class="zpAdmin20__card"><h2>Pokrycie językowe</h2><p>Zmapowane adresy PL → EN: <strong>' . esc_html(zp_admin_translations_20261009_number($data['mapped_routes'])) . '</strong>. To mapa adresów, obejmująca także archiwa; nie liczba zaindeksowanych stron.</p><p>Nowe / starsze strony opublikowane przez tłumacz: <strong>' . esc_html(zp_admin_translations_20261009_number($data['published_ai'])) . '</strong>. Wersje robocze w pamięci tłumacza: <strong>' . esc_html(zp_admin_translations_20261009_number($data['drafts'])) . '</strong>.</p><div class="zpAdmin20__actions">';
            zp_admin_translations_20261009_link('zp-tlumacz-en', 'pages', 'Sprawdź strony bez EN', ['f' => 'missing']);
            zp_admin_translations_20261009_link('zpl-languages', 'routes', 'Adresy PL / EN');
            echo '</div></section><section class="zpAdmin20__card"><h2>Zużycie tłumacza dzisiaj</h2>';
            if ($data['usage'] === null) { echo '<p>Brak danych zużycia.</p>'; }
            else { echo '<p><strong>' . esc_html(zp_admin_translations_20261009_number($data['usage']['chars'])) . '</strong> znaków polskiego tekstu, <strong>' . esc_html(zp_admin_translations_20261009_number($data['usage']['requests'])) . '</strong> żądań API.</p><p>Tokeny wejściowe: ' . esc_html(zp_admin_translations_20261009_number($data['usage']['in'])) . '; wyjściowe: ' . esc_html(zp_admin_translations_20261009_number($data['usage']['out'])) . '.</p>'; }
            echo '<p class="zpAdmin20__muted">Zużycie API w strefie czasowej WordPressa. To nie są statystyki ruchu ani koszt w złotych.</p><div class="zpAdmin20__actions">';
            zp_admin_translations_20261009_link('zp-tlumacz-en', 'settings', 'Ustawienia limitu i modelu');
            echo '</div></section></div>';
        }
    } elseif ($tab === 'corrections') {
        echo '<section class="zpAdmin20__card"><h2>Korekty i pierwszeństwo słowników</h2><p>Ręczne korekty języka: <strong>' . esc_html(zp_admin_translations_20261009_number($data['manual_corrections'])) . '</strong>. Zebrane brakujące fragmenty: <strong>' . esc_html(zp_admin_translations_20261009_number($data['missing_fragments'])) . '</strong>.</p><ol>';
        foreach (zp_admin_translations_20261009_dictionary_sources() as $source) { echo '<li><strong>' . esc_html($source['label']) . '</strong> — ' . esc_html($source['detail']) . '</li>'; }
        echo '</ol><p>Poprawka fragmentu w edytorze kolejki jest chroniona przed nadpisaniem przez AI. Jeśli istnieje już taki sam klucz w gotowym słowniku, pierwszeństwo zachowuje ten słownik. Aby poprawić wynik widoczny na stronie, użyj „Ręcznych korekt języka”.</p><div class="zpAdmin20__actions">';
        zp_admin_translations_20261009_link('zpl-languages', 'overrides', 'Ręczne korekty języka');
        zp_admin_translations_20261009_link('zpl-languages', 'missing', 'Zebrane brakujące fragmenty');
        zp_admin_translations_20261009_link('zp-tlumacz-en', 'pages', 'Edytor fragmentów stron');
        echo '</div></section>';
    } elseif ($tab === 'scanner') {
        echo '<section class="zpAdmin20__card"><h2>Kontrola wersji angielskiej</h2><p>Zebrane fragmenty do przejrzenia: <strong>' . esc_html(zp_admin_translations_20261009_number($data['missing_fragments'])) . '</strong>. Lista jest wynikiem dotychczasowych kontroli; pusta lista nie potwierdza, że cała strona została sprawdzona.</p><div class="zpAdmin20__actions">';
        zp_admin_translations_20261009_link('zpl-languages', 'missing', 'Przejrzyj zebrane fragmenty');
        if ($data['scanner_available']) { zp_admin_translations_20261009_link('zpl-skaner', '', 'Otwórz Skaner EN'); }
        echo '</div>';
        if (!$data['scanner_available']) { echo '<p class="zpAdmin20__muted">Osobny ekran Skanera EN nie jest zarejestrowany. Nadal dostępna jest lista brakujących tłumaczeń modułu językowego.</p>'; }
        echo '</section>';
    } else {
        echo '<div class="zpAdmin20__grid"><section class="zpAdmin20__card"><h2>Tłumaczenie nowych treści</h2><p>Codzienne sprawdzanie: <strong>' . esc_html($data['daily_enabled'] === null ? 'brak danych' : ($data['daily_enabled'] ? 'włączone' : 'wyłączone')) . '</strong>.</p><p>Model: ' . esc_html($data['model'] !== '' ? $data['model'] : 'brak danych') . '. Nowe wersje: ' . esc_html($data['mode'] === '' ? 'brak danych' : ($data['mode'] === 'draft' ? 'szkic' : 'publikacja')) . '.</p><p>Dzienny limit znaków: ' . esc_html($data['daily_chars'] === null ? 'brak danych' : ($data['daily_chars'] === 0 ? 'bez limitu' : zp_admin_translations_20261009_number($data['daily_chars']))) . '.</p><div class="zpAdmin20__actions">';
        zp_admin_translations_20261009_link('zp-tlumacz-en', 'settings', 'Otwórz ustawienia tłumacza');
        echo '</div></section><section class="zpAdmin20__card"><h2>Język odwiedzających i adresy</h2><p>Automatyczny wybór EN dla gości z zagranicy: <strong>' . esc_html($data['geo_enabled'] === null ? 'brak danych' : ($data['geo_enabled'] ? 'włączony' : 'wyłączony')) . '</strong>.</p><p>Przeglądarka znająca polski pozostaje przy PL: <strong>' . esc_html($data['polish_stays'] === null ? 'brak danych' : ($data['polish_stays'] ? 'tak' : 'nie')) . '</strong>.</p><div class="zpAdmin20__actions">';
        zp_admin_translations_20261009_link('zpl-languages', 'geo', 'Ustawienia języka gości');
        zp_admin_translations_20261009_link('zpl-languages', 'routes', 'Mapa adresów PL / EN');
        zp_admin_translations_20261009_link('zpl-languages', 'status', 'Wykluczenia i ustawienia języka');
        echo '</div></section></div>';
    }
}

function zp_admin_translations_20261009_page(): void {
    if (!current_user_can('manage_options')) { wp_die('Brak uprawnień.', '', ['response' => 403]); return; }
    $tab = isset($_GET['tab']) && is_string($_GET['tab']) ? sanitize_key(wp_unslash($_GET['tab'])) : 'overview';
    if (!isset(zp_admin_translations_20261009_tabs()[$tab])) { $tab = 'overview'; }
    $shared = function_exists('zp_admin20_open') && function_exists('zp_admin20_close');
    if ($shared) { zp_admin20_open('Tłumaczenia', 'translations', 'Kolejka, korekty i ustawienia wersji angielskiej w jednym miejscu.'); }
    else { echo '<div class="wrap zpAdmin20"><h1>Tłumaczenia</h1>'; }
    zp_admin_translations_20261009_content($tab);
    if ($shared) { zp_admin20_close(); } else { echo '</div>'; }
}
