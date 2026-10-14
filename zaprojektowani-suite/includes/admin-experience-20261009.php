<?php
/** Admin-only ZP Suite workspace. Uses existing data and handlers; no frontend changes. */
if (!defined('ABSPATH')) { exit; }
require_once __DIR__ . '/admin-astra-workspace-20261013.php';

function zp_admin20_nav(): array {
    return [
        'dashboard' => ['Pulpit', 'zp-suite'],
        'stats' => ['Statystyki SEO', 'zp-suite-stats'],
        'positions' => ['Pozycje i zmiany', 'zp-suite-positions'],
        'plan' => ['Plan SEO', 'zp-suite-seo-plan'],
        'calendar' => ['Harmonogram', 'zp-suite-calendar'],
        'prepare' => ['Przygotuj nowe treści', 'zp-suite-prepare'],
        'articlephotos' => ['Zdjęcia artykułów', 'zp-suite-article-photos'],
        'translations' => ['Tłumaczenia', zp_astra_translator_page()],
        'inquiries' => ['Zapytania', 'zp-suite-zapytania'],
        'performance' => ['Wydajność', 'zp-suite-performance'],
        'ai' => ['Widoczność AI', 'zp-suite-ai'],
        'settings' => ['Ustawienia', 'zp-suite-settings'],
    ];
}

function zp_admin20_url(string $page, array $extra = []): string {
    if (function_exists('zp_suite_workspace_active') && zp_suite_workspace_active()) { $extra['zp_workspace'] = '1'; }
    return add_query_arg(array_merge(['page' => $page], $extra), admin_url('admin.php'));
}

function zp_admin20_open(string $title, string $active = 'dashboard', string $subtitle = ''): void {
    zp_astra_open($title, $active, $subtitle);
}
function zp_admin20_close(): void { zp_astra_close(); }

function zp_admin20_number($value, int $precision = 0): string {
    return is_numeric($value) && is_finite((float) $value) && (float) $value >= 0 ? number_format_i18n((float) $value, $precision) : '—';
}

function zp_admin20_date($value): string {
    if (is_numeric($value) && (int) $value > 0) { return wp_date('d.m.Y H:i', (int) $value); }
    if (is_string($value) && $value !== '' && ($stamp = strtotime($value)) !== false) { return wp_date('d.m.Y H:i', $stamp); }
    return 'brak pomiaru';
}

function zp_admin20_stats(): array {
    $data = function_exists('zp_admin_stats_data') ? zp_admin_stats_data() : [];
    return is_array($data) ? $data : [];
}

function zp_admin20_period(): string {
    $period = isset($_GET['zp_period']) && is_string($_GET['zp_period']) ? sanitize_key(wp_unslash($_GET['zp_period'])) : 'today';
    return in_array($period, ['today', '7d', '28d'], true) ? $period : 'today';
}

function zp_admin20_sources(array $data, string $period): ?array {
    if (!in_array(($data['ga4']['sourceReports'][$period]['status'] ?? ''), ['ready', 'limited'], true)) { return null; }
    $rows = $data['ga4']['sourcesByPeriod'][$period] ?? null;
    if (!is_array($rows) && $period === 'today') { $rows = $data['ga4']['sources'] ?? null; }
    return is_array($rows) ? $rows : null;
}

function zp_admin20_source_counts(?array $rows): array {
    if ($rows === null) { return ['google' => null, 'ai' => null]; }
    $google = $ai = 0;
    foreach ($rows as $row) {
        if (!is_array($row) || !isset($row['sessions']) || !is_numeric($row['sessions'])) { continue; }
        $source = strtolower((string) ($row['source'] ?? ''));
        $medium = strtolower((string) ($row['medium'] ?? ''));
        $channel = strtolower((string) ($row['channel'] ?? $row['channelGroup'] ?? ''));
        // Google administration/preview referrers are visible in sources, but are not organic search visits.
        $google_admin = in_array($source, ['search.google.com', 'ads.google.com', 'analytics.google.com', 'tagassistant.google.com'], true);
        if (!$google_admin && preg_match('/(^|[. ])google([.]|$)/', $source) && ($medium === 'organic' || $channel === 'organic search')) { $google += max(0, (int) $row['sessions']); }
        $host = strtolower((string) (parse_url('https://' . preg_replace('#^https?://#', '', $source), PHP_URL_HOST) ?? ''));
        foreach (['chatgpt.com', 'chat.openai.com', 'claude.ai', 'gemini.google.com', 'perplexity.ai', 'copilot.microsoft.com'] as $known) {
            if ($host === $known || str_ends_with($host, '.' . $known)) { $ai += max(0, (int) $row['sessions']); break; }
        }
    }
    return ['google' => $google, 'ai' => $ai];
}

function zp_admin20_metric(string $label, $value, string $source, string $hint): void {
    echo '<section class="zpAdmin20__metric"><div><h2>' . esc_html($label) . '</h2><span>' . esc_html($source) . '</span></div><strong>' . esc_html(zp_admin20_number($value)) . '</strong><p>' . esc_html($hint) . '</p></section>';
}

function zp_admin20_period_form(string $page, string $period): void {
    echo '<div class="zpAdmin20__toolbar"><form method="get" action="' . esc_url(admin_url('admin.php')) . '"><input type="hidden" name="page" value="' . esc_attr($page) . '"><label for="zp20period" class="screen-reader-text">Okres statystyk GA4</label><select id="zp20period" name="zp_period">';
    foreach (['today' => 'Dzisiaj', '7d' => 'Ostatnie 7 dni', '28d' => 'Ostatnie 28 dni'] as $key => $label) {
        echo '<option value="' . esc_attr($key) . '"' . selected($period, $key, false) . '>' . esc_html($label) . '</option>';
    }
    echo '</select><button class="button">Pokaż</button></form>';
    if (function_exists('zp_admin_stats_refresh_form')) { zp_admin_stats_refresh_form(); }
    echo '</div>';
}

function zp_admin20_sync_notice(array $data): void {
    $sync = is_array($data['sync'] ?? null) ? $data['sync'] : [];
    $status = (string) ($sync['status'] ?? 'not_connected');
    $queued = !empty($sync['queued']);
    echo '<div class="zpAdmin20__notice"><span><b>Źródło: Google Analytics 4 i Search Console.</b> ';
    if ($queued) { echo 'Trwa aktualizacja danych w tle. '; }
    elseif (empty($sync['updated_at'])) { echo 'Brak pobranych raportów. Użyj „Odśwież dane”. '; }
    else { echo 'Ostatnia aktualizacja: ' . esc_html(zp_admin20_date($sync['updated_at'])) . '. '; }
    echo 'Dziś jest raportem częściowym.</span><a href="' . esc_url(admin_url('admin.php?page=rank-math-options-general&view=analytics')) . '">Połączenie Google →</a></div>';
    if (!empty($sync['last_safe_error'])) {
        echo '<p class="zpAdmin20__muted">Nie udało się odświeżyć jednego z raportów Google. Poprzednie poprawne pomiary pozostają dostępne; brak nowego pomiaru oznaczamy kreską.</p>';
        echo '<details class="zpAdmin20__details"><summary>Diagnostyka raportu Google</summary><p class="zpAdmin20__muted">Kod: ' . esc_html((string) $sync['last_safe_error']) . '. Raport: ' . esc_html((string) ($sync['last_error_report_id'] ?? 'nieznany')) . '.</p>';
        if (is_array($sync['last_response_shape'] ?? null)) { echo '<pre class="zpAdmin20__muted">' . esc_html(wp_json_encode($sync['last_response_shape'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) . '</pre>'; }
        echo '</details>';
    }
    $zone = $data['ga4']['timeZone'] ?? $data['ga4']['timezone'] ?? null;
    if (is_string($zone) && $zone !== '') { echo '<p class="zpAdmin20__muted">Strefa czasu danych GA4: ' . esc_html($zone) . '.</p>'; }
    echo '<details class="zpAdmin20__details"><summary>Automatyczne aktualizacje</summary><p class="zpAdmin20__muted">Dzisiejszy ruch: co godzinę. Raporty Google i pozycje: codziennie o ' . esc_html((string) ($sync['daily_at'] ?? '03:05')) . '. Dzienne pozycje pobieramy dla zakończonego dnia sprzed trzech dni według czasu raportów Search Console. ' . esc_html((string) ($sync['cron_note'] ?? '')) . '</p>' . (!empty($sync['cron_disabled']) ? '<p class="zpAdmin20__muted">Standardowy WP-Cron jest wyłączony. Terminy wymagają zewnętrznego uruchamiania crona.</p>' : '') . '</details>';
    echo '<p class="zpAdmin20__muted">Najbliższe zaplanowane zadanie: ruch dziś — ' . esc_html(zp_admin20_date($sync['next_hourly_at'] ?? null)) . '; pełne raporty Google — ' . esc_html(zp_admin20_date($sync['next_daily_at'] ?? null)) . '. Terminy z harmonogramu WordPressa.</p>';
    $health = is_array($sync['health'] ?? null) ? $sync['health'] : [];
    $labels = ['daily_missing'=>'Brakuje codziennego zadania raportów.', 'hourly_missing'=>'Brakuje godzinowego zadania ruchu.', 'daily_overdue'=>'Codzienny raport czeka na uruchomienie WP-Cron.', 'hourly_overdue'=>'Godzinowa aktualizacja czeka na uruchomienie WP-Cron.', 'worker_missing'=>'Kolejka raportów nie ma zadania wykonawczego.', 'worker_overdue'=>'Kolejka raportów czeka na uruchomienie.', 'queue_stalled'=>'Kolejka raportów nie postępuje od co najmniej 15 minut.'];
    $issues = [];
    foreach ((array) ($health['issues'] ?? []) as $issue) { if (is_string($issue) && isset($labels[$issue])) { $issues[] = $labels[$issue]; } }
    if ($issues) { echo '<div class="zpAdmin20__notice" role="status"><span><b>Sprawdź automatyczne aktualizacje.</b> ' . esc_html(implode(' ', $issues)) . ' Możesz użyć „Odśwież dane”.</span></div>'; }
}

function zp_admin23_freshness(array $report): string {
    if (empty($report['stale'])) { return ''; }
    $labels = ['age_exceeded'=>'pomiar wymaga odświeżenia', 'range_mismatch'=>'zakres dat różni się od bieżącego okresu', 'connection_unavailable'=>'połączenie Google wymaga sprawdzenia', 'refresh_error'=>'ostatnia aktualizacja nie powiodła się', 'missing_fetch_time'=>'brakuje daty pobrania', 'clock_mismatch'=>'data pobrania wymaga sprawdzenia'];
    $reasons = [];
    foreach ((array) ($report['stale_reasons'] ?? []) as $reason) { if (is_string($reason) && isset($labels[$reason])) { $reasons[] = $labels[$reason]; } }
    return 'Dane wymagają uwagi: ' . ($reasons ? implode('; ', array_unique($reasons)) : 'pokazano wcześniejszy pomiar') . '.';
}

function zp_admin20_chart(array $daily, array $range, array $report): void {
    $daily = ($report['status'] ?? '') === 'ready' ? array_values(array_filter($daily, static fn($row) => is_array($row) && !empty($row['date']) && isset($row['screenPageViews']) && is_numeric($row['screenPageViews']) && !empty($range['start']) && !empty($range['end']) && $row['date'] >= $range['start'] && $row['date'] <= $range['end'])) : [];
    usort($daily, static fn($a, $b) => strcmp((string) $a['date'], (string) $b['date']));
    echo '<section class="zpAdmin20__card"><div class="zpAdmin20__sectionTitle"><div><h2>Odsłony dzień po dniu</h2><p class="zpAdmin20__muted">GA4 · odsłony strony i użytkownicy w danym dniu</p></div></div>';
    if (!$daily) { echo '<div class="zpAdmin20__empty">Wykres pojawi się po pobraniu dziennych danych GA4.</div></section>'; return; }
    $max = max(1, max(array_map(static fn($r) => max(0, (float) $r['screenPageViews']), $daily)));
    $points = [];
    foreach ($daily as $i => $row) { $points[] = (count($daily) === 1 ? 450 : 25 + 850 * $i / (count($daily) - 1)) . ',' . (215 - 185 * max(0, (float) $row['screenPageViews']) / $max); }
    echo '<svg class="zpAdmin20__chart" viewBox="0 0 900 255" role="img" aria-label="Dzienne odsłony strony z GA4"><line x1="25" y1="215" x2="875" y2="215" stroke="#dfe4ec"/><line x1="25" y1="122" x2="875" y2="122" stroke="#eef1f5"/><line x1="25" y1="30" x2="875" y2="30" stroke="#eef1f5"/><text x="26" y="16" fill="#6b7280" font-size="12">' . esc_html(zp_admin20_number($max)) . '</text><polyline fill="none" stroke="#1c477a" stroke-width="3" points="' . esc_attr(implode(' ', $points)) . '"/>';
    foreach ($daily as $i => $row) {
        [$x, $y] = explode(',', $points[$i]);
        echo '<circle cx="' . esc_attr($x) . '" cy="' . esc_attr($y) . '" r="4" fill="#1c477a"><title>' . esc_html($row['date'] . ': ' . zp_admin20_number($row['screenPageViews']) . ' odsłon, ' . zp_admin20_number($row['totalUsers'] ?? null) . ' użytkowników') . '</title></circle>';
    }
    echo '<text x="25" y="244" fill="#6b7280" font-size="12">' . esc_html((string) $daily[0]['date']) . '</text><text x="875" y="244" text-anchor="end" fill="#6b7280" font-size="12">' . esc_html((string) $daily[count($daily) - 1]['date']) . '</text></svg><details class="zpAdmin20__details"><summary>Dane dzienne w tabeli</summary><div class="zpAdmin20__tableWrap"><table class="zpAdmin20__table"><thead><tr><th>Data</th><th>Użytkownicy</th><th>Sesje</th><th>Odsłony</th></tr></thead><tbody>';
    foreach (array_reverse($daily) as $row) { echo '<tr><td>' . esc_html($row['date']) . '</td><td>' . esc_html(zp_admin20_number($row['totalUsers'] ?? null)) . '</td><td>' . esc_html(zp_admin20_number($row['sessions'] ?? null)) . '</td><td>' . esc_html(zp_admin20_number($row['screenPageViews'])) . '</td></tr>'; }
    echo '</tbody></table></div></details><p class="zpAdmin20__muted">Pobrano: ' . esc_html(zp_admin20_date($report['fetched_at'] ?? null)) . '. Google może pomijać dni bez zarejestrowanych zdarzeń.' . (!empty($report['stale']) ? ' Pokazano poprzedni poprawny pomiar.' : '') . '</p></section>';
}

function zp_admin20_source_table(?array $rows, array $report = []): void {
    echo '<section class="zpAdmin20__card"><h2>Skąd przyszli użytkownicy?</h2><p class="zpAdmin20__muted">Źródła wizyt w wybranym okresie · liczba sesji, nie suma unikalnych osób.</p>';
    if ($rows === null) { echo '<div class="zpAdmin20__empty">Brak raportu źródeł GA4.</div></section>'; return; }
    if (isset($report['_health']['totals_usable']) && !$report['_health']['totals_usable'] && empty($report['limited'])) { echo '<p class="zpAdmin20__muted">Wiersze źródeł pozostają widoczne, ale liczniki Google i AI czekają na kompletny, aktualny raport zgodny z okresem GA4.</p>'; }
    echo '<p class="zpAdmin20__muted">' . esc_html((string) ($report['start'] ?? '')) . ' — ' . esc_html((string) ($report['end'] ?? '')) . ' · pobrano ' . esc_html(zp_admin20_date($report['fetched_at'] ?? null)) . (!empty($report['limited']) ? ' · Raport ma ograniczony zakres; sumy Google i AI pozostają nieokreślone.' : '') . (!empty($report['stale']) ? ' · Pokazano poprzedni poprawny pomiar.' : '') . '</p>';
    usort($rows, static fn($a, $b) => (int) ($b['sessions'] ?? 0) <=> (int) ($a['sessions'] ?? 0));
    echo '<div class="zpAdmin20__tableWrap"><table class="zpAdmin20__table"><thead><tr><th>Źródło</th><th>Medium</th><th>Kanał</th><th>Sesje</th></tr></thead><tbody>';
    foreach (array_slice($rows, 0, 50) as $row) {
        if (!is_array($row)) { continue; }
        echo '<tr><td>' . esc_html((string) ($row['source'] ?? '')) . '</td><td>' . esc_html((string) ($row['medium'] ?? '')) . '</td><td>' . esc_html((string) ($row['channel'] ?? $row['channelGroup'] ?? '')) . '</td><td>' . esc_html(zp_admin20_number($row['sessions'] ?? null)) . '</td></tr>';
    }
    if (!$rows) { echo '<tr><td colspan="4">Google nie zwrócił sesji dla tego okresu.</td></tr>'; }
    echo '</tbody></table></div><p class="zpAdmin20__muted">Wejścia z AI obejmują rozpoznane odesłania asystentów, m.in. ChatGPT, Claude, Gemini, Perplexity i Copilot. Aplikacja może ukrywać źródło i wtedy sesja trafi do ruchu bezpośredniego. GA4 nie udostępnia treści pytań zadanych asystentom AI. Zapytania Google są w raporcie Search Console poniżej. Odesłania z paneli Google, takich jak Search Console, pokazujemy w tabeli źródeł; nie zaliczamy ich do karty ruchu organicznego.</p></section>';
}

function zp_admin23_opportunities(array $gsc): void {
    $report = is_array($gsc['opportunities'] ?? null) ? $gsc['opportunities'] : [];
    echo '<section class="zpAdmin20__card"><h2>Podstrony warte poprawy</h2><p class="zpAdmin20__muted">Search Console · co najmniej 50 wyświetleń, średnia pozycja 4–20 i CTR do 2%. Lista pomaga wybrać podstrony do przeglądu tytułu, opisu i treści.</p>';
    if (!in_array(($report['status'] ?? ''), ['ready', 'limited'], true)) { echo '<div class="zpAdmin20__empty">Lista pojawi się po pobraniu raportu podstron Search Console.</div></section>'; return; }
    echo '<p class="zpAdmin20__muted">' . esc_html((string) ($report['period_start'] ?? '')) . ' — ' . esc_html((string) ($report['as_of'] ?? '')) . ' · pobrano ' . esc_html(zp_admin20_date($report['fetched_at'] ?? null)) . (!empty($report['limited']) ? ' · Ograniczony zakres odpowiedzi Google.' : '') . ' ' . esc_html(zp_admin23_freshness($report)) . '</p>';
    $rows = is_array($report['rows'] ?? null) ? $report['rows'] : [];
    echo '<div class="zpAdmin20__tableWrap"><table class="zpAdmin20__table"><thead><tr><th>Podstrona</th><th>Wyświetlenia</th><th>CTR</th><th>Śr. pozycja</th><th>Otwórz</th></tr></thead><tbody>';
    foreach (array_slice($rows, 0, 10) as $row) {
        if (!is_array($row)) { continue; }
        $url = (string) ($row['page'] ?? '');
        $local = in_array(strtolower((string) parse_url($url, PHP_URL_SCHEME)), ['http', 'https'], true) && strtolower((string) parse_url($url, PHP_URL_HOST)) === strtolower((string) parse_url(home_url('/'), PHP_URL_HOST));
        $edit = '';
        if ($local && function_exists('url_to_postid') && function_exists('get_edit_post_link')) { $id = url_to_postid($url); if ($id) { $edit = (string) get_edit_post_link($id, 'raw'); } }
        echo '<tr><td>' . esc_html($local ? (string) (parse_url($url, PHP_URL_PATH) ?: '/') : $url) . '</td><td>' . esc_html(zp_admin20_number($row['impressions'] ?? null)) . '</td><td>' . esc_html(isset($row['ctr']) && is_numeric($row['ctr']) ? zp_admin20_number((float) $row['ctr'] * 100, 2) . '%' : '—') . '</td><td>' . esc_html(zp_admin20_number($row['position'] ?? null, 1)) . '</td><td>' . ($local ? '<a href="' . esc_url($url) . '" target="_blank" rel="noopener">Podgląd ↗</a>' : '—') . ($edit !== '' ? '<br><a href="' . esc_url($edit) . '">Edytuj →</a>' : '') . '</td></tr>';
    }
    if (!$rows) { echo '<tr><td colspan="5">Brak podstron spełniających te kryteria w pobranym raporcie.</td></tr>'; }
    echo '</tbody></table></div><p class="zpAdmin20__muted">To wskazówki na podstawie widocznych wyników Google. Ocena wymaga sprawdzenia intencji zapytań i treści; nie prognozuje wzrostu pozycji.</p></section>';
}

function zp_admin20_position_table(array $gsc, string $query): void {
    $report = is_array($gsc['positionsReport'] ?? null) ? $gsc['positionsReport'] : [];
    echo '<section class="zpAdmin20__card"><h2>Codzienne wyniki fraz</h2><p class="zpAdmin20__muted">Średnia pozycja w Google dla całej witryny · ' . esc_html((string) ($report['end'] ?? 'brak pomiaru')) . '. Pełny dzień z Search Console, z opóźnieniem; nie jest lokalnym pomiarem SERP z Katowic.</p>';
    if (($report['status'] ?? '') !== 'ready') { echo '<div class="zpAdmin20__empty">Pierwsze dzienne pozycje pojawią się po aktualizacji Google.</div></section>'; return; }
    $rows = is_array($gsc['positions'] ?? null) ? $gsc['positions'] : [];
    if ($query !== '') { $rows = array_values(array_filter($rows, static fn($r) => is_array($r) && mb_stripos((string) ($r['query'] ?? ''), $query) !== false)); }
    usort($rows, static fn($a, $b) => (int) ($b['impressions'] ?? 0) <=> (int) ($a['impressions'] ?? 0));
    $history = is_array($gsc['positionHistory'] ?? null) ? $gsc['positionHistory'] : [];
    $dates = array_filter(array_keys($history), static fn($day) => is_string($day) && $day < ($report['end'] ?? ''));
    rsort($dates, SORT_STRING); $previous_day = $dates[0] ?? null; $previous = [];
    if ($previous_day !== null) { foreach ($history[$previous_day] as $old) { if (is_array($old) && isset($old['query'], $old['position'])) { $previous[(string) $old['query']] = $old['position']; } } }
    echo '<div class="zpAdmin20__tableWrap"><table class="zpAdmin20__table"><thead><tr><th>Fraza</th><th>Śr. pozycja</th><th>Zmiana' . ($previous_day !== null ? ' od ' . esc_html($previous_day) : '') . '</th><th>Kliknięcia</th><th>Wyświetlenia</th></tr></thead><tbody>';
    foreach (array_slice($rows, 0, 250) as $row) {
        if (!is_array($row)) { continue; }
        $phrase = (string) ($row['query'] ?? ''); $change = '—';
        if (isset($previous[$phrase]) && is_numeric($previous[$phrase]) && is_numeric($row['position'] ?? null)) {
            $delta = (float) $previous[$phrase] - (float) $row['position'];
            $change = abs($delta) < .05 ? 'bez zmiany' : ($delta > 0 ? '↑ ' : '↓ ') . zp_admin20_number(abs($delta), 1);
        }
        echo '<tr><td>' . esc_html($phrase) . '</td><td>' . esc_html(zp_admin20_number($row['position'] ?? null, 1)) . '</td><td>' . esc_html($change) . '</td><td>' . esc_html(zp_admin20_number($row['clicks'] ?? null)) . '</td><td>' . esc_html(zp_admin20_number($row['impressions'] ?? null)) . '</td></tr>';
    }
    if (!$rows) { echo '<tr><td colspan="5">Google nie zwrócił widocznych zapytań dla tego dnia lub filtra.</td></tr>'; }
    echo '</tbody></table></div><p class="zpAdmin20__muted">Pobrano: ' . esc_html(zp_admin20_date($report['fetched_at'] ?? null)) . '. Historia zbierana jest od uruchomienia panelu; brak porównania oznacza brak wcześniejszego pomiaru.' . (!empty($report['limited']) ? ' Lista jest ograniczona przez zakres odpowiedzi Google.' : '') . (!empty($report['stale']) ? ' Pokazano poprzedni poprawny pomiar.' : '') . '</p></section>';
}

function zp_admin20_gsc_tables(array $gsc, bool $full): void {
    $rows = ($gsc['queriesReport']['status'] ?? '') === 'ready' && is_array($gsc['queries'] ?? null) ? $gsc['queries'] : null;
    $query = isset($_GET['zp_query']) && is_string($_GET['zp_query']) ? mb_substr(sanitize_text_field(wp_unslash($_GET['zp_query'])), 0, 200) : '';
    echo '<section class="zpAdmin20__card"><div class="zpAdmin20__sectionTitle"><div><h2>Z jakich zapytań trafiają klienci?</h2><p class="zpAdmin20__muted">Search Console · ' . esc_html((string) ($gsc['start'] ?? '')) . ' — ' . esc_html((string) ($gsc['end'] ?? $gsc['data_through'] ?? '')) . '</p></div>';
    if (!$full) { echo '<a href="' . esc_url(zp_admin20_url('zp-suite-stats')) . '">Pełny raport →</a>'; }
    echo '</div>';
    if ($full) { echo '<form class="zpAdmin20__filters" method="get" action="' . esc_url(admin_url('admin.php')) . '"><input type="hidden" name="page" value="zp-suite-stats"><input type="hidden" name="zp_period" value="' . esc_attr(zp_admin20_period()) . '"><label for="zp20query">Znajdź frazę</label><input type="search" name="zp_query" id="zp20query" value="' . esc_attr($query) . '" placeholder="np. projektowanie logo katowice"><button class="button">Filtruj</button></form>'; }
    if ($rows === null) { echo '<div class="zpAdmin20__empty">Raport zapytań pojawi się po pobraniu danych Search Console.</div></section>'; return; }
    echo '<p class="zpAdmin20__muted">Pobrano: ' . esc_html(zp_admin20_date($gsc['queriesReport']['fetched_at'] ?? null)) . (!empty($gsc['queriesReport']['limited']) ? ' · Lista jest ograniczona do wyników zwróconych przez Google.' : '') . (!empty($gsc['queriesReport']['stale']) ? ' · Pokazano poprzedni poprawny pomiar.' : '') . '</p>';
    if ($query !== '') { $rows = array_values(array_filter($rows, static fn($r) => is_array($r) && mb_stripos((string) ($r['query'] ?? ''), $query) !== false)); }
    usort($rows, static fn($a, $b) => (int) ($b['clicks'] ?? 0) <=> (int) ($a['clicks'] ?? 0) ?: ((int) ($b['impressions'] ?? 0) <=> (int) ($a['impressions'] ?? 0)));
    echo '<div class="zpAdmin20__tableWrap"><table class="zpAdmin20__table"><thead><tr><th>Zapytanie</th><th>Kliknięcia</th><th>Wyświetlenia</th><th>CTR</th><th>Śr. pozycja</th></tr></thead><tbody>';
    foreach (array_slice($rows, 0, $full ? 250 : 10) as $row) {
        if (!is_array($row)) { continue; }
        echo '<tr><td>' . esc_html((string) ($row['query'] ?? '')) . '</td><td>' . esc_html(zp_admin20_number($row['clicks'] ?? null)) . '</td><td>' . esc_html(zp_admin20_number($row['impressions'] ?? null)) . '</td><td>' . esc_html(isset($row['ctr']) && is_numeric($row['ctr']) ? zp_admin20_number((float) $row['ctr'] * 100, 2) . '%' : '—') . '</td><td>' . esc_html(zp_admin20_number($row['position'] ?? null, 1)) . '</td></tr>';
    }
    if (!$rows) { echo '<tr><td colspan="5">Brak widocznych zapytań w tym raporcie lub dla wybranego filtra.</td></tr>'; }
    echo '</tbody></table></div><p class="zpAdmin20__muted">To zagregowane zapytania Google, a nie przypisanie frazy do konkretnej osoby. Google ukrywa część zapytań. Pozycja jest średnią z raportowanego okresu.</p>';
    if (!empty($gsc['property'])) { echo '<p class="zpAdmin20__muted">Zakres właściwości: ' . esc_html((string) $gsc['property']) . '.</p>'; }
    echo '</section><section class="zpAdmin20__card"><h2>Najważniejsze podstrony w Google</h2>';
    $pages = ($gsc['pagesReport']['status'] ?? '') === 'ready' && is_array($gsc['pages'] ?? null) ? $gsc['pages'] : null;
    if ($pages === null) { echo '<div class="zpAdmin20__empty">Brak raportu podstron Search Console.</div></section>'; return; }
    usort($pages, static fn($a, $b) => (int) ($b['clicks'] ?? 0) <=> (int) ($a['clicks'] ?? 0));
    echo '<div class="zpAdmin20__tableWrap"><table class="zpAdmin20__table"><thead><tr><th>Podstrona</th><th>Kliknięcia</th><th>Wyświetlenia</th><th>Śr. pozycja</th></tr></thead><tbody>';
    foreach (array_slice($pages, 0, $full ? 100 : 5) as $row) {
        if (!is_array($row)) { continue; }
        $url = (string) ($row['page'] ?? '');
        $allowed = parse_url($url, PHP_URL_HOST) === parse_url(home_url('/'), PHP_URL_HOST);
        echo '<tr><td>' . ($allowed ? '<a href="' . esc_url($url) . '" target="_blank" rel="noopener">' . esc_html((string) (parse_url($url, PHP_URL_PATH) ?: '/')) . '</a>' : esc_html($url)) . '</td><td>' . esc_html(zp_admin20_number($row['clicks'] ?? null)) . '</td><td>' . esc_html(zp_admin20_number($row['impressions'] ?? null)) . '</td><td>' . esc_html(zp_admin20_number($row['position'] ?? null, 1)) . '</td></tr>';
    }
    if (!$pages) { echo '<tr><td colspan="4">Brak podstron w pobranym raporcie.</td></tr>'; }
    echo '</tbody></table></div></section>';
    if ($full) { zp_admin20_position_table($gsc, $query); }
}

function zp_admin20_side(): void {
    $posts = get_posts(['post_type' => 'post', 'post_status' => 'future', 'numberposts' => 3, 'orderby' => 'date', 'order' => 'ASC']);
    echo '<aside class="zpAdmin20__side"><section class="zpAdmin20__card zpAdmin20__card--dark"><p class="zpAdmin20__eyebrow">Publikacje</p><h2>Najbliższe artykuły</h2>';
    foreach ($posts as $post) {
        $stamp = strtotime($post->post_date_gmt . ' UTC');
        echo '<div class="zpAdmin20__scheduleItem"><time>' . esc_html(wp_date('d.m.Y · H:i', $stamp)) . '</time><b>' . esc_html($post->post_title) . '</b><span class="zpAdmin20__pill">Zaplanowany</span></div>';
    }
    if (!$posts) { echo '<p>Brak zaplanowanych artykułów.</p>'; }
    echo '<a href="' . esc_url(zp_admin20_url('zp-suite-calendar')) . '">Cały harmonogram →</a></section><section class="zpAdmin20__card"><h2>Stan strony</h2><div class="zpAdmin20__rows">';
    $cache = get_option('zp_html_cache_20261009_state', []);
    $enabled = is_array($cache) && !empty($cache['enabled']);
    $modules = [
        ['Cache HTML', $enabled ? 'włączony' : 'sprawdź panel', 'zp-suite-html-cache'],
        ['Tłumaczenia PL / EN', class_exists('ZPL\\Runtime') || class_exists('ZPL\\Plugin') ? 'dostępne' : 'sprawdź panel', 'zp-suite-translations'],
        ['Automatyczna publikacja', (string) count($posts) . ' najbliższe wpisy', 'zp-suite-calendar'],
    ];
    foreach ($modules as $m) { echo '<a href="' . esc_url(zp_admin20_url($m[2])) . '"><span>' . esc_html($m[0]) . '</span><b>' . esc_html($m[1]) . '</b></a>'; }
    echo '</div></section><section class="zpAdmin20__card"><h2>Zapytania od klientów</h2><p class="zpAdmin20__muted">Formularze, prośby o kontakt i Studio Wyceny w jednej skrzynce.</p><a class="button" href="' . esc_url(zp_admin20_url('zp-suite-zapytania')) . '">Otwórz zapytania →</a></section>';
    if (function_exists('zp_admin_lighthouse_compact')) { zp_admin_lighthouse_compact(); }
    echo '</aside>';
}

function zp_admin20_render_report(bool $full = false): void {
    if (!current_user_can('manage_options')) { return; }
    $data = zp_admin20_stats(); $period = zp_admin20_period();
    $labels = ['today' => 'Dziś na stronie', '7d' => 'Ostatnie 7 dni na stronie', '28d' => 'Ostatnie 28 dni na stronie'];
    zp_admin20_open($full ? 'Statystyki SEO i źródła ruchu' : $labels[$period], $full ? 'stats' : 'dashboard', 'Użytkownicy, odsłony, Google i AI — rzeczywiste dane z połączonych usług.');
    zp_admin20_period_form($full ? 'zp-suite-stats' : 'zp-suite', $period);
    if ($full) { do_action('zp_astra_stats_overview', $period); }
    zp_admin20_sync_notice($data);
    $total = is_array($data['ga4']['periods'][$period] ?? null) ? $data['ga4']['periods'][$period] : [];
    $source_report = is_array($data['ga4']['sourceReports'][$period] ?? null) ? $data['ga4']['sourceReports'][$period] : [];
    $sources = zp_admin20_sources($data, $period);
    $source_health = is_array($data['ga4']['sourceHealth'][$period] ?? null) ? $data['ga4']['sourceHealth'][$period] : [];
    $usable = !empty($source_health['totals_usable']);
    $counts = zp_admin20_source_counts($usable ? $sources : null);
    if ($usable && $sources !== null && is_array($data['ga4']['aiByPeriod'][$period] ?? null)) { $counts['ai'] = array_sum(array_column($data['ga4']['aiByPeriod'][$period], 'sessions')); }
    echo '<p class="zpAdmin20__muted">Zakres GA4: ' . esc_html((string) ($total['start'] ?? '')) . ' — ' . esc_html((string) ($total['end'] ?? '')) . ' · pobrano ' . esc_html(zp_admin20_date($total['fetched_at'] ?? null)) . (!empty($total['stale']) ? ' · Pokazano poprzedni poprawny pomiar.' : '') . '</p>';
    if (($total['status'] ?? '') === 'awaiting_data') { $counts = ['google'=>null,'ai'=>null]; echo '<p class="zpAdmin20__muted">Google nie udostępnił jeszcze pomiaru dla tego okresu. Liczniki pojawią się po odświeżeniu danych; kreska nie oznacza zera odwiedzin. Dane GA4 mogą być opóźnione.</p>'; }
    if (!empty($total['stale'])) { echo '<p class="zpAdmin20__muted">' . esc_html(zp_admin23_freshness($total)) . '</p>'; }
    echo '<div class="zpAdmin20__metrics">';
    zp_admin20_metric('Użytkownicy', $total['totalUsers'] ?? null, 'GA4', 'Użytkownicy w wybranym okresie według pomiaru GA4.');
    zp_admin20_metric('Odsłony strony', $total['screenPageViews'] ?? null, 'GA4', 'Każde otwarcie strony; jedna osoba może wygenerować kilka odsłon.');
    zp_admin20_metric('Z Google — organiczne', $counts['google'], 'Sesje GA4', 'Wizyty z bezpłatnych wyników Google w tym samym okresie.');
    zp_admin20_metric('Z rozpoznanych źródeł AI', $counts['ai'], 'Sesje GA4', 'Wizyty z rozpoznanych odesłań asystentów AI.');
    echo '</div><p class="zpAdmin20__muted">Sesje w wybranym okresie: <b>' . esc_html(zp_admin20_number($total['sessions'] ?? null)) . '</b>. Dane obejmują ruch widoczny w GA4; zgody na analitykę i blokowanie skryptów wpływają na zakres pomiaru. Unikalnych użytkowników tygodnia lub miesiąca nie sumujemy z dni.</p><div class="zpAdmin20__layout"><div class="zpAdmin20__stack">';
    if ($full && function_exists('zp_admin_stats_trends_render')) {
        zp_admin_stats_trends_render($data, $period);
    } else {
    zp_admin20_chart(is_array($data['ga4']['daily'] ?? null) ? $data['ga4']['daily'] : [], $total, is_array($data['ga4']['dailyReport'] ?? null) ? $data['ga4']['dailyReport'] : []);
    }
    zp_admin20_source_table($sources, $source_report + ['_health'=>$source_health]);
    zp_admin20_gsc_tables(is_array($data['gsc'] ?? null) ? $data['gsc'] : [], $full);
    zp_admin23_opportunities(is_array($data['gsc'] ?? null) ? $data['gsc'] : []);
    echo '</div>'; zp_admin20_side(); echo '</div>';
    zp_admin20_close();
}

function zp_admin20_dashboard(): void { zp_astra_dashboard(); }
function zp_admin20_stats_page(): void { zp_admin20_render_report(true); }

function zp_admin20_settings_page(): void { zp_astra_settings(); }

function zp_admin23_modules(): void {
    $cache = get_option('zp_html_cache_20261009_state', []);
    $connection = class_exists('ZP_Admin_Stats_20261009') ? ZP_Admin_Stats_20261009::connection() : [];
    $items = [
        ['Cache HTML', !empty($cache['enabled']) ? 'Włączony' : 'Wyłączony', 'zp-suite-html-cache'],
        ['Raporty Google', !empty($connection['authorized']) && (!empty($connection['ga4_configured']) || !empty($connection['gsc_configured'])) ? 'Połączone przez Rank Math' : 'Sprawdź połączenie', 'zp-suite-stats'],
        ['Pozycje i zmiany w Senuto', class_exists('ZP_Senuto_Positions_20261010') ? 'Polska i Katowice' : 'Sprawdź moduł', 'zp-suite-positions'],
        ['Języki PL / EN', defined('ZPL_VERSION') ? 'Wersja ' . ZPL_VERSION : 'Sprawdź moduł', 'zp-suite-translations'],
        ['Tłumacz treści', defined('ZPTE_VERSION') ? 'Wersja ' . ZPTE_VERSION : 'Sprawdź moduł', 'zp-suite-translations'],
        ['Harmonogram artykułów', class_exists('ZP_Admin_Calendar_20261009') ? 'Dostępny' : 'Sprawdź moduł', 'zp-suite-calendar'],
        ['Formularze i Studio Wyceny', function_exists('zp_inbox_render_page') ? 'Wspólna skrzynka' : 'Otwórz skrzynkę', 'zp-suite-zapytania'],
        ['Widoczność AI', function_exists('zp_ai_on') && zp_ai_on() ? 'Moduł włączony' : 'Sprawdź ustawienia', 'zp-suite-ai'],
        ['Codzienny Lighthouse', function_exists('zp_admin_lighthouse_render') ? 'Harmonogram i wyniki' : 'Sprawdź moduł', 'zp-suite-performance'],
        ['Treści strony głównej', function_exists('zp_suite_render_admin_page') ? 'CMS dostępny' : 'Sprawdź moduł', 'zp-suite-home-cms'],
    ];
    echo '<section class="zpAdmin20__card" style="margin-top:20px"><h2>Moduły i narzędzia</h2><p class="zpAdmin20__muted">Szybki dostęp do wszystkich obszarów Suite. Dostępność modułu nie oznacza zaliczenia audytu strony ani kompletności danych.</p><div class="zpAdmin20__rows">';
    foreach ($items as [$label, $state, $page]) { echo '<a href="' . esc_url(zp_admin20_url($page)) . '"><span>' . esc_html($label) . '</span><b>' . esc_html($state) . ' →</b></a>'; }
    echo '</div></section>';
}

function zp_admin20_performance_page(): void {
    if (!current_user_can('manage_options')) { return; }
    zp_admin20_open('Wydajność strony', 'performance', 'Cache HTML, optymalizacja zasobów i wyniki audytu.');
    if (function_exists('zp_admin_lighthouse_render')) { zp_admin_lighthouse_render(); }
    echo '<div class="zpAdmin20__grid" style="margin-top:20px"><section class="zpAdmin20__card"><h2>Cache HTML</h2><p class="zpAdmin20__muted">Zapisany HTML dla publicznych polskich stron. Panel, podglądy i formularze POST pozostają poza cache.</p><a class="button button-primary" href="' . esc_url(zp_admin20_url('zp-suite-html-cache')) . '">Otwórz ustawienia cache</a></section><section class="zpAdmin20__card"><h2>Optymalizacja zasobów</h2><p class="zpAdmin20__muted">Istniejące ustawienia obrazów i skryptów. Wygląd i animacje pozostają zachowane.</p><a class="button" href="' . esc_url(zp_admin20_url('zp-suite-speed')) . '">Otwórz optymalizację</a></section><section class="zpAdmin20__card"><h2>Pomiar odpowiedzi HTML z 9.10.2026</h2><p>Mediana odpowiedzi HTML: <b>1,23 s → 0,58 s</b> w czterech porównywalnych próbach 9.10.2026. Dwie rozgrzane odpowiedzi HIT: mediana <b>0,16 s</b>.</p><p class="zpAdmin20__muted">To historyczny pomiar odpowiedzi serwera. Dzisiejszy Lighthouse i daty kolejnych pomiarów są widoczne powyżej.</p></section></div>'; zp_admin20_close();
}

add_action('admin_menu', static function (): void {
    add_submenu_page('zp-suite', 'Statystyki SEO', 'Statystyki SEO', 'manage_options', 'zp-suite-stats', 'zp_admin20_stats_page');
    add_submenu_page('zp-suite', 'Pozycje i zmiany w Senuto', 'Pozycje i zmiany', 'manage_options', 'zp-suite-positions', 'zp_admin_senuto_positions_render');
    add_submenu_page('zp-suite', 'Harmonogram artykułów', 'Harmonogram', 'manage_options', 'zp-suite-calendar', 'zp_suite_admin20_render_calendar');
    add_submenu_page('zp-suite', 'Przygotuj nowe treści', 'Przygotuj nowe treści', 'manage_options', 'zp-suite-prepare', 'zp_admin_content_preparation_render');
    add_submenu_page('zp-suite', 'Wydajność strony', 'Wydajność', 'manage_options', 'zp-suite-performance', 'zp_admin20_performance_page');
    add_submenu_page('zp-suite', 'Ustawienia ZP Suite', 'Ustawienia', 'manage_options', 'zp-suite-settings', 'zp_admin20_settings_page');
}, 11000);

// 3.0.0: the menu order, section labels and hidden pages are set in includes/suite3/admin-ui.php.
// The hidden pages stay registered under ZP Suite, so WordPress knows their titles (no strip_tags(null) notice).

add_filter('admin_body_class', static function (string $classes): string {
    $page = isset($_GET['page']) ? sanitize_key(wp_unslash($_GET['page'])) : '';
    if (str_starts_with($page, 'zp-suite') || in_array($page, ['zp-translator-v2', 'zp-tlumacz-en', 'zpl-languages', 'zpl-skaner'], true)) { $classes .= ' zpAdmin20-route'; }
    return $classes;
});

add_action('admin_head', static function (): void {
    $page = isset($_GET['page']) ? sanitize_key(wp_unslash($_GET['page'])) : '';
    if (!str_starts_with($page, 'zp-suite') && !in_array($page, ['zp-translator-v2', 'zp-tlumacz-en', 'zpl-languages', 'zpl-skaner'], true)) { return; }
    // 3.0.0: assets/admin/zp-suite-3.css replaces these inline styles when the new panel is loaded.
    if (function_exists('zp_suite3_open')) { return; }
    echo '<style id="zp-admin20-css">' . zp_admin20_css() . '</style>';
}, 99);

function zp_admin20_css(): string {
    $fonts = '';
    foreach ([400, 500, 600, 700, 800] as $weight) {
        $fonts .= '@font-face{font-family:"Plus Jakarta Sans";src:url("' . esc_url(ZP_SUITE_URL . 'assets/campaigns/plus-jakarta-sans-' . $weight . '.woff2') . '") format("woff2");font-weight:' . $weight . ';font-style:normal;font-display:swap}';
    }
    return $fonts . <<<'CSS'
body.zpAdmin20-route{background:#f4f6f9}
body.zpAdmin20-route #wpbody-content,body.zpAdmin20-route #wpbody-content .wrap{font-family:"Plus Jakarta Sans",sans-serif!important}
body.zpAdmin20-route #wpbody-content :where(h1,h2,h3,h4,p,label,a:not(.dashicons),button,input,select,textarea,th,td,summary){font-family:"Plus Jakarta Sans",sans-serif!important}
.zpAdmin20 .zpxHead .zpxStat,.zpAdmin20 .zpxHead .zpxStat:hover,.zpAdmin20 .zpxHead .zpxStat:focus{color:#fff}
.zpAdmin20{--ink:#071426;--muted:#667385;--line:#e1e6ee;max-width:1450px;width:calc(100% - 32px);margin:24px auto 0;color:var(--ink);font-family:"Plus Jakarta Sans",sans-serif;font-size:14px;line-height:1.6}
.zpAdmin20 *{box-sizing:border-box}.zpAdmin20 a{text-decoration:none;color:#1c477a}.zpAdmin20 a:hover{text-decoration:underline}.zpAdmin20 a:focus-visible,.zpAdmin20 button:focus-visible,.zpAdmin20 summary:focus-visible{outline:2px solid #1c477a;outline-offset:4px}.zpAdmin20 h1,.zpAdmin20 h2,.zpAdmin20 h3{color:var(--ink);font-family:inherit}.zpAdmin20 p{font-size:inherit;line-height:1.65}.zpAdmin20 .button{border-radius:8px;min-height:36px;padding:3px 13px;border-color:var(--line);font-family:inherit}.zpAdmin20 .button-primary{background:var(--ink);border-color:var(--ink);color:#fff}.zpAdmin20 input,.zpAdmin20 select{border-radius:8px;border-color:#ccd4e0}.zpAdmin20__header{display:flex;align-items:center;justify-content:space-between;gap:20px}.zpAdmin20__identity{display:flex;align-items:center;gap:15px}.zpAdmin20__identity b{display:block;font-size:25px;line-height:1.25;letter-spacing:-.04em}.zpAdmin20__identity small{display:block;font-size:12px;margin-top:4px;color:var(--muted)}.zpAdmin20__logo{display:flex;align-items:center;justify-content:center;flex-shrink:0;width:112px;height:88px;padding:0;background:transparent}.zpAdmin20__logo img{max-width:100%;width:auto;height:auto;max-height:88px;object-fit:contain}.zpAdmin20__actions{display:flex;align-items:center;flex-wrap:wrap;gap:10px}.zpAdmin20__pill,.zpAdmin20__status{display:inline-flex;align-items:center;gap:5px;line-height:1.5;border-radius:6px;background:#eef2f7;padding:4px 9px;font-size:11px;color:#536075}.zpAdmin20__status.is-ok{background:#eef8f0;color:#166534}.zpAdmin20__status.is-warn{background:#fff6e8;color:#9a3412}.zpAdmin20__nav{display:flex;flex-wrap:wrap;gap:4px;margin-top:25px;padding:8px;border:1px solid var(--line);border-radius:13px;background:#fff}.zpAdmin20__nav a{display:inline-flex;align-items:center;padding:8px 14px;border-radius:8px;font-size:13px;font-weight:650;color:#647084;white-space:nowrap}.zpAdmin20__nav a:hover{background:#f1f4f8;text-decoration:none}.zpAdmin20 .zpAdmin20__nav a.is-active,.zpAdmin20 .zpAdmin20__nav a[aria-current=page]{background:var(--ink,#071426);color:#fff}.zpAdmin20 .zpAdmin20__nav a.is-active:hover{background:var(--ink,#071426);color:#fff}.zpAdmin20__title{display:flex;justify-content:space-between;gap:16px;margin:29px 0 15px}.zpAdmin20__title p{font-size:10px;text-transform:uppercase;letter-spacing:.14em;font-weight:650;color:#8490a1;margin:0 0 8px}.zpAdmin20__title h1{font-size:30px;font-weight:650;letter-spacing:-.04em;line-height:1.2;margin:0 0 8px;padding:0}.zpAdmin20__title span{display:block;color:var(--muted);font-size:13px}.zpAdmin20 .wp-header-end{height:0;border:0;margin:0}.zpAdmin20__toolbar{display:flex;flex-wrap:wrap;justify-content:flex-end;align-items:center;gap:10px;margin:-47px 0 24px}.zpAdmin20__toolbar form{display:flex;gap:7px;align-items:center;margin:0}.zpAdmin20__notice{display:flex;align-items:center;justify-content:space-between;gap:20px;padding:13px 16px;border:1px solid #dfE8f4;border-radius:11px;background:#edf3fc77;color:#5a6778;font-size:12px;margin:18px 0}.zpAdmin20__notice a{font-weight:650;white-space:nowrap}.zpAdmin20__metrics{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:16px;margin:18px 0}.zpAdmin20__metric{background:#fff;border:1px solid var(--line);border-radius:16px;padding:20px;min-width:0}.zpAdmin20__metric>div{display:flex;align-items:flex-start;justify-content:space-between;gap:9px}.zpAdmin20__metric h2{font-size:13px;font-weight:650;margin:0;line-height:1.5;color:#5a6677}.zpAdmin20__metric span{display:inline-block;white-space:nowrap;font-size:9px;line-height:1.5;border-radius:4px;background:#f6f8fa;padding:3px 5px;color:#8490a1}.zpAdmin20__metric strong{display:block;margin:20px 0 12px;font-size:38px;font-weight:650;line-height:1;letter-spacing:-.04em;font-variant-numeric:tabular-nums}.zpAdmin20__metric p{margin:0;color:#8190a3;font-size:11px;line-height:1.6}.zpAdmin20__muted{color:var(--muted);font-size:12px!important;line-height:1.65}.zpAdmin20__layout{display:grid;grid-template-columns:minmax(0,1fr) 320px;gap:20px;align-items:start;margin-top:20px}.zpAdmin20__stack,.zpAdmin20__side{display:grid;gap:20px;min-width:0}.zpAdmin20__grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:20px;align-items:start}.zpAdmin20__card{border:1px solid var(--line);border-radius:16px;background:#fff;padding:22px;min-width:0}.zpAdmin20__card h2{font-size:16px;font-weight:650;line-height:1.4;margin:0 0 12px;letter-spacing:-.02em}.zpAdmin20__card h3{font-size:14px;margin:20px 0 8px}.zpAdmin20__card p{margin:0 0 12px}.zpAdmin20__card--dark{background:var(--ink);border-color:var(--ink);color:#fff}.zpAdmin20__card--dark h2,.zpAdmin20__card--dark a{color:#fff}.zpAdmin20__card--dark p{color:#a4b1c3}.zpAdmin20__card--dark .zpAdmin20__pill{background:#ffffff15;color:#c5cfdf}.zpAdmin20__eyebrow{font-size:10px!important;text-transform:uppercase;letter-spacing:.15em}.zpAdmin20__scheduleItem{display:grid;gap:8px;padding:15px 0;border-top:1px solid #ffffff15;margin:10px 0}.zpAdmin20__scheduleItem time{font-size:11px;color:#9caec5}.zpAdmin20__scheduleItem b{font-size:13px;font-weight:500;line-height:1.55}.zpAdmin20__scheduleItem .zpAdmin20__pill{justify-self:start}.zpAdmin20__rows{display:grid;gap:0}.zpAdmin20__rows a{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:14px 0;border-bottom:1px solid #eef1f5;color:#526176;font-size:12px}.zpAdmin20__rows a:last-child{border-bottom:0}.zpAdmin20__rows b{font-size:11px;font-weight:500;border-radius:5px;background:#f3f6fa;padding:3px 6px;white-space:nowrap}.zpAdmin20__empty{display:flex;min-height:130px;align-items:center;justify-content:center;padding:26px;background:#fafbfd;border:1px dashed var(--line);border-radius:11px;color:#8390a2;text-align:center;font-size:13px;line-height:1.7;margin-top:18px}.zpAdmin20__chart{display:block;width:100%;height:auto;max-height:285px;margin:20px 0 10px}.zpAdmin20__sectionTitle{display:flex;align-items:start;justify-content:space-between;gap:15px}.zpAdmin20__sectionTitle h2{margin:0 0 4px}.zpAdmin20__sectionTitle a{font-size:11px;font-weight:650;white-space:nowrap}.zpAdmin20__details{margin-top:15px}.zpAdmin20__details summary{cursor:pointer;color:#1c477a;font-size:12px;font-weight:650}.zpAdmin20__tableWrap{overflow-x:auto;-webkit-overflow-scrolling:touch;max-width:100%;margin-top:15px}.zpAdmin20__table{width:100%;border-collapse:collapse;font-size:12px;text-align:left}.zpAdmin20__table th{padding:12px 14px;background:#f8fafc;color:#788599;font-size:10px;font-weight:650;text-transform:uppercase;letter-spacing:.04em;white-space:nowrap}.zpAdmin20__table td{padding:13px 14px;border-bottom:1px solid #edf1f5;vertical-align:top;line-height:1.6;overflow-wrap:anywhere}.zpAdmin20__table td:first-child{min-width:180px}.zpAdmin20__table td:nth-child(n+2){font-variant-numeric:tabular-nums}.zpAdmin20__filters{display:flex;align-items:center;flex-wrap:wrap;gap:8px;margin:15px 0}.zpAdmin20__filters label{font-size:12px;font-weight:650}.zpAdmin20__filters input:not([type=checkbox]):not([type=radio]){min-width:220px}.zpAdmin20__filters input[type=checkbox],.zpAdmin20__filters input[type=radio]{width:16px;min-width:16px;height:16px;flex:0 0 16px}.zpAdmin20__tabs{display:flex;align-items:center;flex-wrap:wrap;gap:7px;margin:16px 0}.zpAdmin20__tabs a{border:1px solid var(--line);border-radius:8px;background:#fff;padding:7px 13px;color:#667385;font-size:12px}.zpAdmin20__tabs a.is-active,.zpAdmin20__tabs a[aria-current=page]{background:var(--ink);border-color:var(--ink);color:#fff}.zpAdmin20__check{display:flex;align-items:flex-start;gap:9px;margin:15px 0;line-height:1.6;font-size:13px}.zpAdmin20__check input{margin-top:3px}.zpAdmin20__field{display:grid;gap:7px;font-size:12px;color:#667385;margin:14px 0}.zpAdmin20__field input{max-width:130px}.zpAdmin20__footer{border-top:1px solid var(--line);margin:28px 0 16px;padding-top:14px;color:#8a96a7;font-size:10px}.zpAdmin20__thumb{width:86px;height:54px;object-fit:cover;border-radius:7px}.zpAdmin20__calendarMeta{display:grid;gap:5px}.zpAdmin20__calendarMeta strong{font-size:13px}.zpAdmin20-route .zpx{max-width:1450px;font-family:"Plus Jakarta Sans",sans-serif}.zpAdmin20-route .zpxHead{border-radius:16px;padding:22px 24px;background:#071426}.zpAdmin20-route .zpxCard,.zpAdmin20-route .zpxItem{border-radius:16px;border-color:#e1e6ee}.zpAdmin20-route .zpxRows{gap:0}.zpAdmin20-route .zpxRow{border:0;border-bottom:1px solid #edf1f5;border-radius:0;background:#fff;padding:14px 0}.zpAdmin20-route .zpxRow:last-child{border-bottom:0}.zpAdmin20-route .wrap:not(.zpAdmin20):not(.zpx)>h1{font-family:"Plus Jakarta Sans",sans-serif;font-weight:650;color:#071426;letter-spacing:-.025em}
@media(max-width:1280px){.zpAdmin20__metrics{grid-template-columns:repeat(2,minmax(0,1fr))}.zpAdmin20__layout{grid-template-columns:minmax(0,1fr) 285px}.zpAdmin20__toolbar{margin:0 0 20px;justify-content:flex-start}}
@media(max-width:960px){.zpAdmin20__layout,.zpAdmin20__grid{grid-template-columns:1fr}.zpAdmin20__side{grid-template-columns:repeat(2,minmax(0,1fr))}.zpAdmin20__header{align-items:flex-start}}
@media(max-width:782px){.zpAdmin20{width:calc(100% - 24px);margin:16px auto 0}.zpAdmin20__logo{width:90px;height:70px}.zpAdmin20__logo img{max-height:70px}.zpAdmin20__header{flex-wrap:wrap}.zpAdmin20__nav{flex-wrap:nowrap;overflow-x:auto}.zpAdmin20__nav a{padding:8px 11px}.zpAdmin20__title h1{font-size:25px}.zpAdmin20__metrics{gap:10px}.zpAdmin20__metric{padding:16px}.zpAdmin20__metric>div{display:block}.zpAdmin20__metric span{margin-top:5px}.zpAdmin20__metric strong{font-size:31px}.zpAdmin20__notice{align-items:start;flex-direction:column;gap:9px}.zpAdmin20__side{grid-template-columns:1fr}.zpAdmin20__card{padding:18px}.zpAdmin20__table td,.zpAdmin20__table th{padding:10px}.zpAdmin20__filters input:not([type=checkbox]):not([type=radio]){min-width:0;width:100%}}
CSS;
}

add_action('admin_footer', static function (): void {
    $page = isset($_GET['page']) ? sanitize_key(wp_unslash($_GET['page'])) : '';
    if (!in_array($page, ['zp-suite', 'zp-suite-stats'], true) || !current_user_can('manage_options') || !function_exists('zp_admin_stats_data')) { return; }
    $data = zp_admin_stats_data();
    if (empty($data['sync']['queued'])) { return; }
    $endpoint = admin_url('admin-ajax.php'); $nonce = wp_create_nonce('zp_admin_stats_status');
    echo '<script>(function(){let attempts=0;const initial=' . wp_json_encode((string) ($data['sync']['updated_at'] ?? '')) . ';const url=' . wp_json_encode($endpoint) . ';const nonce=' . wp_json_encode($nonce) . ';async function poll(){if(++attempts>36)return;if(document.visibilityState!=="visible"){setTimeout(poll,5000);return;}try{const response=await fetch(url+"?action=zp_admin_stats_status&_ajax_nonce="+encodeURIComponent(nonce),{credentials:"same-origin",cache:"no-store"});const json=await response.json();if(json.success&&json.data){const focused=document.activeElement;if(String(json.data.updated_at||"")!==initial&&!(focused&&/^(INPUT|TEXTAREA|SELECT)$/.test(focused.tagName))){location.reload();return;}if(!json.data.queued){if(!(focused&&/^(INPUT|TEXTAREA|SELECT)$/.test(focused.tagName))){location.reload();}return;}}}catch(e){}setTimeout(poll,5000);}setTimeout(poll,5000);})();</script>';
});
