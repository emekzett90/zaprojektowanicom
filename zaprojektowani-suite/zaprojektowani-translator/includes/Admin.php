<?php
namespace ZPTE;

if (!defined('ABSPATH')) { exit; }

/** ZP Suite → Tłumacz EN */
final class Admin {
    const SLUG = 'zp-tlumacz-en';
    const PER_PAGE = 50;
    /** Daily limit choices: characters of Polish text sent to OpenAI a day (0 = no limit). */
    const LIMITS = [100000 => '100 000 znaków', 200000 => '200 000 znaków', 400000 => '400 000 znaków (zalecane)', 1000000 => '1 000 000 znaków', 0 => 'bez limitu'];
    /** Models offered in the settings; any other model name can still be typed in. */
    const MODELS = ['gpt-5.4-mini' => 'Zalecany: gpt-5.4-mini (szybki i tani)', 'gpt-5.4' => 'Dokładniejszy: gpt-5.4 (droższy)'];

    public static function boot(): void {
        // After the Suite trims its submenu (priority 9999), so this entry stays visible.
        add_action('admin_menu', [self::class, 'menu'], 10000);
        add_action('admin_post_zpte', [self::class, 'handle']);
        add_action('admin_notices', [self::class, 'notices']);
        add_action('wp_ajax_zpte_status', [self::class, 'ajax_status']);
        add_action('wp_ajax_zpte_step', [self::class, 'ajax_step']);
        add_action('admin_enqueue_scripts', [self::class, 'assets']);
    }

    public static function assets(): void {
        if (($_GET['page'] ?? '') !== self::SLUG || !current_user_can('manage_options')) { return; }
        wp_enqueue_style('zpte-admin', plugins_url('assets/admin.css', ZPTE_DIR . 'translator.php'), [], ZPTE_VERSION);
        if (($_GET['tab'] ?? 'status') !== 'status') { return; }
        wp_enqueue_script('zpte-admin', plugins_url('assets/admin.js', ZPTE_DIR . 'translator.php'), [], ZPTE_VERSION, true);
        wp_localize_script('zpte-admin', 'zpteAdmin', ['url' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce('zpte_progress')]);
    }

    private static function ajax_authorize(): void {
        if (!current_user_can('manage_options')) { wp_send_json_error(['message' => 'Brak uprawnień.'], 403); }
        check_ajax_referer('zpte_progress', 'nonce');
        if (!Bridge::hooks_ready()) { wp_send_json_error(['message' => 'Moduł językowy wymaga aktualizacji.'], 409); }
        Store::install();
    }

    public static function ajax_status(): void {
        self::ajax_authorize();
        wp_send_json_success(['progress' => Worker::progress(), 'nonce' => wp_create_nonce('zpte_progress')]);
    }

    /** Browser fallback: works even when hosting blocks wp-cron loopback requests. */
    public static function ajax_step(): void {
        self::ajax_authorize();
        Worker::work();
        wp_send_json_success(['progress' => Worker::progress(), 'nonce' => wp_create_nonce('zpte_progress')]);
    }

    /** Live progress of the current run (assets/admin.js fills the data-zpte fields and drives the queue). */
    private static function progress_panel(array $s): void {
        echo '<section class="zpxCard zpte-progress" id="zpte-progress" data-state="' . esc_attr($s['state']) . '" aria-labelledby="zpte-progress-title">'
            . '<div class="zpte-progress-head"><h2 id="zpte-progress-title">Postęp tłumaczenia</h2><strong data-zpte="percent">' . (int) $s['percent'] . '%</strong></div>'
            . '<div class="zpte-progress-track" role="progressbar" aria-label="Postęp tłumaczenia stron" aria-valuemin="0" aria-valuemax="100" aria-valuenow="' . (int) $s['percent'] . '"><span style="width:' . (int) $s['percent'] . '%"></span></div>'
            . '<p class="zpte-progress-message" data-zpte="message" role="status" aria-live="polite">' . esc_html($s['message']) . '</p>'
            . '<div class="zpte-progress-counts"><span><strong data-zpte="done">' . (int) $s['done'] . '</strong> z <strong data-zpte="total">' . (int) $s['total'] . '</strong> stron gotowych</span>'
            . '<span><strong data-zpte="queued">' . (int) $s['queued'] . '</strong> w kolejce</span><span><strong data-zpte="errors">' . (int) $s['errors'] . '</strong> wymaga uwagi</span></div>'
            . '<p class="zpte-progress-current" data-zpte="current"></p><p class="description" data-zpte="retry"></p>'
            . '<p class="description" id="zpte-poll-status">Postęp odświeża się sam. Dopóki ten ekran jest otwarty, tłumaczenie idzie dalej także wtedy, gdy serwer nie uruchamia zadań w tle.</p>'
            . '<noscript><p>Włącz JavaScript, aby widzieć postęp na żywo. Codzienne tłumaczenie działa niezależnie od tego ekranu.</p></noscript></section>';
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
        echo '<div class="notice notice-error"><p><strong>Tłumacz EN wstrzymany:</strong> ' . esc_html((string) $a['m'])
            . ' <a href="' . esc_url(self::url()) . '">Otwórz Tłumacza EN</a></p></div>';
    }

    private static function when(?string $gmt): string {
        if ($gmt === null || $gmt === '' || strpos($gmt, '0000') === 0) { return '—'; }
        $ts = strtotime($gmt . ' UTC');
        return $ts ? self::day_time($ts) : '—';
    }

    /** "dziś 03:00", "jutro 03:00", "wczoraj 14:10" or "5.10.2026 03:00" (site time). */
    public static function day_time(int $ts): string {
        $d = wp_date('Y-m-d', $ts);
        $t = wp_date('H:i', $ts);
        if ($d === wp_date('Y-m-d')) { return 'dziś ' . $t; }
        if ($d === wp_date('Y-m-d', time() + DAY_IN_SECONDS)) { return 'jutro ' . $t; }
        if ($d === wp_date('Y-m-d', time() - DAY_IN_SECONDS)) { return 'wczoraj ' . $t; }
        return wp_date('j.m.Y', $ts) . ' ' . $t;
    }

    /** 1 strona, 2 strony, 5 stron. */
    private static function pages_word(int $n): string {
        if ($n === 1) { return 'strona'; }
        $d = $n % 10;
        $h = $n % 100;
        return $d >= 2 && $d <= 4 && ($h < 12 || $h > 14) ? 'strony' : 'stron';
    }

    private static function form_open(string $do, array $hidden = [], string $confirm = ''): string {
        $h = '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '" class="zpte-inline"' . ($confirm !== '' ? ' onsubmit="return confirm(' . esc_attr((string) wp_json_encode($confirm)) . ')"' : '') . '>'
            . '<input type="hidden" name="action" value="zpte"><input type="hidden" name="do" value="' . esc_attr($do) . '">'
            . wp_nonce_field('zpte', '_wpnonce', true, false);
        foreach ($hidden as $k => $v) { $h .= '<input type="hidden" name="' . esc_attr($k) . '" value="' . esc_attr((string) $v) . '">'; }
        return $h;
    }

    private static function button(string $do, string $label, array $hidden = [], string $class = 'button', string $confirm = ''): string {
        return self::form_open($do, $hidden, $confirm) . '<button type="submit" class="' . esc_attr($class) . '">' . esc_html($label) . '</button></form>';
    }

    // ---------------------------------------------------------------- summary

    /**
     * Published Polish posts and pages (of the types chosen in Ustawienia) that have no English version, so a
     * visitor from abroad reads them in Polish. Newest first. Pages the translator skipped on purpose (hidden or
     * noindex) and pages switched off by hand are left out.
     * @return array<int,array{post_id:int,path:string,title:string,type:string,date:string,row:?object}>
     */
    public static function missing_pages(): array {
        static $cache = null;
        if ($cache !== null) { return $cache; }
        $cache = [];
        $types = Bridge::ready() ? Settings::types() : [];
        if (!$types) { return $cache; }
        $rows = [];
        if (get_option('zpte_db') === Store::DB_VERSION) {
            foreach (Store::paths(['kind' => ['new', 'older']]) as $r) { $rows[(string) $r->path] = $r; }
        }
        $ids = get_posts(['post_type' => $types, 'post_status' => 'publish', 'has_password' => false, 'posts_per_page' => -1,
            'orderby' => 'date', 'order' => 'DESC', 'fields' => 'ids', 'no_found_rows' => true, 'suppress_filters' => true, 'ignore_sticky_posts' => true]);
        if ($ids) { _prime_post_caches(array_map('intval', $ids), true, true); } // permalinks and robots meta without a query per post
        $routes = \ZPL\Router::routes();
        foreach ((array) $ids as $pid) {
            $pid = (int) $pid;
            $path = Bridge::path_of_post($pid);
            if ($path === '' || Bridge::ineligible($path) !== '') { continue; }
            if (isset($routes[$path]) && \ZPL\Dict::has_page($path)) { continue; }
            $row = $rows[$path] ?? null;
            if ($row && in_array($row->status, ['skipped', 'off'], true)) { continue; }
            $robots = get_post_meta($pid, 'rank_math_robots', true);
            if (is_array($robots) && in_array('noindex', $robots, true)) { continue; }
            $cache[] = ['post_id' => $pid, 'path' => $path, 'title' => html_entity_decode(get_the_title($pid), ENT_QUOTES, 'UTF-8'),
                'type' => (string) get_post_type($pid), 'date' => (string) get_the_date('j.m.Y', $pid), 'row' => $row];
        }
        return $cache;
    }

    /**
     * One sentence for the top of the Status tab: is it working, and if not, what to do about it.
     * @return array{0:string,1:string,2:string} level (ok|warn|bad), text, action buttons (HTML)
     */
    public static function headline(): array {
        $settings = self::url(['tab' => 'settings']);
        if (!Bridge::hooks_ready()) {
            return ['bad', 'Moduł językowy w tej wersji wtyczki nie współpracuje z tłumaczem, więc nic nie tłumaczę. Wgraj aktualną wersję ZP Suite.', ''];
        }
        if (Settings::api_key() === '') {
            $text = Settings::key_unreadable()
                ? 'Zapisanego klucza OpenAI nie da się odczytać (zmieniły się klucze bezpieczeństwa WordPressa). Wklej klucz ponownie.'
                : 'Brak klucza OpenAI, więc nowe wpisy nie dostają wersji angielskiej.';
            return ['bad', $text, '<a class="button button-primary" href="' . esc_url($settings . '#zpte-key') . '">Wklej klucz</a>'];
        }
        $alert = Log::current_alert();
        if ($alert) {
            return ['bad', 'Wstrzymane: ' . (string) $alert['m'], self::button('retry', 'Spróbuj ponownie', [], 'button button-primary') . '<a class="button" href="' . esc_url($settings) . '">Ustawienia</a>'];
        }
        if (!Settings::get('enabled')) {
            return ['warn', 'Codzienne tłumaczenie jest wyłączone, więc nowe wpisy nie dostaną wersji angielskiej same.', '<a class="button button-primary" href="' . esc_url($settings . '#zpte-enabled') . '">Włącz w ustawieniach</a>'];
        }
        $p = Worker::progress();
        if ($p['state'] === 'limit') {
            return ['warn', 'Wstrzymane do jutra: wykorzystano dzienny limit znaków. Dokończę sam po północy.', '<a class="button" href="' . esc_url($settings . '#zpte-limit') . '">Zmień limit</a>'];
        }
        if ($p['state'] === 'retry') {
            $at = (int) ($p['retry_at'] ?? 0);
            return ['warn', 'Chwilowy błąd połączenia z OpenAI. Ponowię sam' . ($at > time() ? ' o ' . wp_date('H:i', $at) : '') . '.', ''];
        }
        if ((int) $p['queued'] > 0) {
            $n = (int) $p['queued'];
            return ['ok', 'Tłumaczę: w kolejce ' . $n . ' ' . self::pages_word($n) . '. Postęp widać poniżej.', ''];
        }
        $last = (int) get_option('zpte_last_plan');
        $next = wp_next_scheduled(Worker::DAILY);
        $text = 'Działa. ' . ($last ? 'Ostatnie sprawdzenie: ' . self::day_time($last) . '.' : 'Pierwsze sprawdzenie jeszcze przed nami.')
            . ($next ? ' Następne: ' . self::day_time((int) $next) . '.' : '');
        return ['ok', $text, self::button('run', 'Sprawdź teraz')];
    }

    // ------------------------------------------------------------------- page

    public static function page(): void {
        if (!current_user_can('manage_options')) { return; }
        $tabs = ['status' => 'Status', 'pages' => 'Strony', 'settings' => 'Ustawienia', 'log' => 'Dziennik'];
        $tab = sanitize_key($_GET['tab'] ?? 'status');
        if ($tab === 'edit') { $current = 'pages'; } else { $current = isset($tabs[$tab]) ? $tab : 'status'; }
        $ready = Bridge::ready();
        if ($ready) { Store::install(); }
        echo '<div class="wrap zpx zpte"><header class="zpxHead"><span class="zpxKicker">ZP Suite · Tłumacz EN ' . esc_html(ZPTE_VERSION) . '</span><h1>Tłumacz EN</h1>'
            . '<p>Każdej nocy o 3:00 tłumaczy nowe polskie wpisy na angielski (OpenAI) i uzupełnia braki na stronach angielskich. Słownik z wtyczki i ręczne poprawki z Języki PL/EN mają pierwszeństwo.</p>';
        if ($ready && $current === 'status') {
            $missing = count(self::missing_pages());
            $attention = Store::count_paths(['attention' => true]);
            $stats = [
                [$missing, 'bez wersji EN', self::url(['tab' => 'pages', 'f' => 'missing']), $missing > 0],
                [Store::queued_count(), 'w kolejce', '', false],
                [$attention, 'wymaga uwagi', self::url(['tab' => 'pages', 'f' => 'error']), $attention > 0],
                [Store::count_paths(['kind' => ['new', 'older'], 'status' => 'published']), 'przetłumaczone przez AI', self::url(['tab' => 'pages', 'f' => 'new']), false],
            ];
            echo '<div class="zpxStats">';
            foreach ($stats as [$n, $label, $href, $hot]) {
                $inner = '<strong>' . esc_html(number_format_i18n((int) $n)) . '</strong><span>' . esc_html($label) . '</span>';
                echo $href !== '' ? '<a class="zpxStat' . ($hot ? ' is-hot' : '') . '" href="' . esc_url($href) . '">' . $inner . '</a>' : '<div class="zpxStat">' . $inner . '</div>';
            }
            echo '</div>';
        }
        echo '</header><hr class="wp-header-end">';
        if (!empty($_GET['msg'])) { echo '<div class="notice notice-success is-dismissible"><p>' . esc_html(wp_unslash((string) $_GET['msg'])) . '</p></div>'; }
        if (!empty($_GET['err'])) { echo '<div class="notice notice-error is-dismissible"><p>' . esc_html(wp_unslash((string) $_GET['err'])) . '</p></div>'; }
        echo '<nav class="zpxTabs" aria-label="Zakładki Tłumacza EN">';
        foreach ($tabs as $k => $label) { echo '<a class="' . ($k === $current ? 'is-active' : '') . '" href="' . esc_url(self::url(['tab' => $k])) . '"' . ($k === $current ? ' aria-current="page"' : '') . '>' . esc_html($label) . '</a>'; }
        echo '</nav>';
        if (!$ready) {
            echo '<section class="zpxCard zpte-gap"><p class="zpte-bad"><strong>Brak modułu językowego (zaprojektowani-languages).</strong> Tłumacz zapisuje wersje angielskie w tym module, więc bez niego nic nie robi.</p></section></div>';
            return;
        }
        if ($tab === 'edit') { self::tab_edit((int) ($_GET['id'] ?? 0)); }
        else { $fn = 'tab_' . $current; self::$fn(); }
        echo '</div>';
    }

    private static function tab_status(): void {
        [$level, $text, $actions] = self::headline();
        echo '<section class="zpteState is-' . esc_attr($level) . '" role="status"><p><span class="zpteDot" aria-hidden="true"></span>' . esc_html($text) . '</p>'
            . ($actions !== '' ? '<div class="zpteActions">' . $actions . '</div>' : '') . '</section>';

        $p = Worker::progress();
        $active = Settings::api_key() !== '' && Bridge::hooks_ready()
            && ((int) $p['queued'] > 0 || !empty($p['running']) || ((int) $p['finished_at'] > time() - 6 * HOUR_IN_SECONDS));
        if ($active) { self::progress_panel($p); }

        $missing = self::missing_pages();
        echo '<div class="zpxGrid">';
        echo '<section class="zpxCard"><h2>Strony bez wersji angielskiej</h2>';
        if ($missing) {
            echo '<p>Goście z zagranicy widzą te strony po polsku: przekierowanie na wersję angielską działa tylko dla stron, które ją mają.</p>';
        }
        self::missing_list(array_slice($missing, 0, 6), 'status');
        if (count($missing) > 6) { echo '<a class="zpxMore" href="' . esc_url(self::url(['tab' => 'pages', 'f' => 'missing'])) . '">Wszystkie (' . count($missing) . ') →</a>'; }
        echo '</section>';

        $usage = Log::usage();
        $month = Log::month();
        $limit = (int) Settings::get('daily_chars');
        $next = wp_next_scheduled(Worker::DAILY);
        $last = (int) get_option('zpte_last_plan');
        $cron = defined('DISABLE_WP_CRON') && DISABLE_WP_CRON;
        $key = Settings::api_key() !== '';
        $rows = [
            'Klucz OpenAI' => $key ? 'zapisany (' . Settings::key_hint() . ')' . (Settings::key_source() === 'constant' ? ', z wp-config.php' : '') : 'brak',
            'Model' => Settings::model(),
            'Następne sprawdzenie' => $next ? self::day_time((int) $next) : '—',
            'Ostatnie sprawdzenie' => $last ? self::day_time($last) : 'jeszcze nie było',
            'Dziś wysłano do OpenAI' => number_format_i18n((int) $usage['chars']) . ' znaków' . ($limit > 0 ? ' z ' . number_format_i18n($limit) . ' (limit dzienny)' : ' (bez limitu)'),
            'W tym miesiącu' => number_format_i18n((int) $month['chars']) . ' znaków, zapytań: ' . number_format_i18n((int) $month['requests']),
            'Uruchamianie w tle' => $cron ? 'wyłączone na serwerze (DISABLE_WP_CRON): tłumaczenie idzie przy otwartym panelu albo z zadaniem cron na hostingu' : 'włączone: start przy pierwszym wejściu na stronę po 3:00',
        ];
        echo '<section class="zpxCard"><h2>Szczegóły</h2><dl class="zpteDl">';
        foreach ($rows as $k => $v) { echo '<div><dt>' . esc_html($k) . '</dt><dd' . ($k === 'Klucz OpenAI' && !$key ? ' class="zpte-bad"' : '') . '>' . esc_html($v) . '</dd></div>'; }
        echo '</dl>';
        if ($key) { echo '<div class="zpteActions">' . self::button('run', 'Sprawdź teraz', [], 'button button-primary') . self::button('test', 'Sprawdź połączenie z OpenAI') . '</div>'; }
        echo '<p class="description">Nowy polski wpis dostaje angielski adres (np. /en/websites/…), przetłumaczony tytuł, opis, treść i dane dla Google, a wersje PL i EN wskazują się nawzajem (hreflang).</p></section>';
        echo '</div>';
    }

    /** Rows of Polish pages without English, each with one action. $from = tab the actions return to. */
    private static function missing_list(array $items, string $from): void {
        if (!$items) { echo '<div class="zpxEmpty">Każda opublikowana strona ma wersję angielską.</div>'; return; }
        $key = Settings::api_key() !== '';
        if (!$key) {
            echo '<p class="zpteNote">Najpierw <a href="' . esc_url(self::url(['tab' => 'settings'])) . '#zpte-key">wklej klucz OpenAI</a>, potem przetłumaczysz te strony jednym przyciskiem.</p>';
        }
        $back = ['back_tab' => $from, 'f' => 'missing'];
        echo '<div class="zpteList">';
        foreach ($items as $it) {
            $row = $it['row'];
            $type = get_post_type_object($it['type']);
            $meta = rawurldecode($it['path']) . ' · ' . ($type ? (string) $type->labels->singular_name : $it['type']) . ' · ' . $it['date'];
            $chip = '';
            $action = '';
            if ($row && ((int) $row->queued || $row->status === 'working')) {
                $chip = '<span class="zpxPill">w kolejce</span>';
            } elseif ($row && $row->status === 'draft') {
                $chip = '<span class="zpxPill is-quote">szkic do akceptacji</span>';
                $en = (string) $row->en_path !== '' ? (string) $row->en_path : \ZPL\Router::en_path((string) $row->path);
                $action = '<a class="button" href="' . esc_url(home_url($en) . '?zpte_preview=1') . '" target="_blank" rel="noopener">Podgląd</a>'
                    . self::button('publish', 'Opublikuj', array_merge($back, ['id' => (int) $row->id]), 'button button-primary');
            } else {
                if ($row && $row->status === 'error') { $chip = '<span class="zpxPill is-warn" title="' . esc_attr((string) $row->message) . '">nie udało się</span>'; }
                $action = $key
                    ? self::button('add', 'Przetłumacz', array_merge($back, ['path' => $it['path']]), 'button button-primary')
                    : '<button type="button" class="button" disabled>Przetłumacz</button>';
            }
            echo '<div class="zpteItem"><div class="zpteItemMain"><a href="' . esc_url(home_url($it['path'])) . '" target="_blank" rel="noopener"><b>' . esc_html($it['title'] !== '' ? $it['title'] : rawurldecode($it['path'])) . '</b></a><small>' . esc_html($meta) . '</small>'
                . ($row && $row->status === 'error' && (string) $row->message !== '' ? '<small class="zpte-warn">' . esc_html((string) $row->message) . '</small>' : '') . '</div>'
                . '<div class="zpteItemEnd">' . $chip . $action . '</div></div>';
        }
        echo '</div>';
    }

    private static function status_label(string $status): string {
        $map = ['queued' => 'czeka w kolejce', 'working' => 'w trakcie', 'draft' => 'szkic do akceptacji', 'published' => 'gotowa', 'error' => 'problem', 'skipped' => 'pominięta', 'off' => 'wyłączona'];
        return $map[$status] ?? $status;
    }

    private static function status_class(string $status): string {
        $map = ['published' => 'is-ok', 'draft' => 'is-quote', 'error' => 'is-warn', 'skipped' => 'is-off', 'off' => 'is-off'];
        return $map[$status] ?? '';
    }

    private static function tab_pages(): void {
        $filters = [
            'missing' => ['Bez wersji EN', null],
            'new' => ['Tłumaczone przez AI', ['kind' => ['new', 'older']]],
            'draft' => ['Szkice do akceptacji', ['status' => 'draft']],
            'error' => ['Wymaga uwagi', ['attention' => true]],
            'existing' => ['Gotowe strony EN', ['kind' => 'existing']],
            'all' => ['Wszystkie', []],
        ];
        $f = sanitize_key($_GET['f'] ?? 'missing');
        if (!isset($filters[$f])) { $f = 'missing'; }

        echo '<nav class="zpxTabs zpte-filters" aria-label="Filtr stron">';
        foreach ($filters as $k => [$label, $w]) {
            $n = $w === null ? count(self::missing_pages()) : Store::count_paths($w);
            echo '<a class="' . ($k === $f ? 'is-active' : '') . '" href="' . esc_url(self::url(['tab' => 'pages', 'f' => $k])) . '">' . esc_html($label) . ' <em>' . esc_html(number_format_i18n($n)) . '</em></a>';
        }
        echo '</nav>';

        if ($f === 'missing') {
            $missing = self::missing_pages();
            echo '<section class="zpxCard zpte-gap"><h2>Polskie strony bez wersji angielskiej</h2><p>Goście z zagranicy widzą je po polsku. Tłumaczenie trwa kilka minut na stronę i od razu publikuje wersję angielską'
                . (Settings::get('mode') === 'draft' ? ' jako szkic do akceptacji' : '') . '.</p>';
            $todo = array_filter($missing, static function ($it) { return !$it['row'] || (!(int) $it['row']->queued && in_array($it['row']->status, ['queued', 'error'], true)); });
            if (count($todo) > 1 && Settings::api_key() !== '') {
                $limit = (int) Settings::get('daily_chars');
                $confirm = 'Przetłumaczyć ' . count($todo) . ' ' . self::pages_word(count($todo)) . '? Koszt w OpenAI zależy od długości stron.' . ($limit > 0 ? ' Dzienny limit to ' . number_format_i18n($limit) . ' znaków: większa partia dokończy się następnego dnia.' : '');
                echo '<div class="zpteActions">' . self::button('add_all', 'Przetłumacz wszystkie (' . count($todo) . ')', ['back_tab' => 'pages', 'f' => 'missing'], 'button button-primary', $confirm) . '</div>';
            }
            self::missing_list($missing, 'pages');
            echo '</section>';
        } else {
            self::pages_table($filters[$f][1], $f);
        }

        echo '<details class="zpxCard zpte-gap"><summary>Przetłumacz inny adres</summary>';
        echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '" class="zpte-add"><input type="hidden" name="action" value="zpte"><input type="hidden" name="do" value="add"><input type="hidden" name="back_tab" value="pages"><input type="hidden" name="f" value="' . esc_attr($f) . '">';
        wp_nonce_field('zpte');
        echo '<label for="zpte-add">Polski adres strony, np. <code>/strony-www/nazwa-wpisu/</code></label><span><input id="zpte-add" name="path" class="regular-text code" placeholder="/polski-adres/"> <button class="button">Dodaj do kolejki</button></span></form></details>';
    }

    private static function pages_table(array $where, string $f): void {
        $paged = max(1, (int) ($_GET['paged'] ?? 1));
        $total = Store::count_paths($where);
        $rows = Store::paths($where, self::PER_PAGE, ($paged - 1) * self::PER_PAGE, 'queued DESC, id DESC');
        if (!$rows) { echo '<div class="zpxEmpty zpte-gap">Brak stron w tym widoku.</div>'; return; }
        echo '<div class="zpte-gap"><table class="widefat striped zpte-table"><thead><tr><th>Strona PL</th><th>Wersja angielska</th><th>Stan</th><th>Teksty</th><th>Sprawdzono</th><th>Akcje</th></tr></thead><tbody>';
        foreach ($rows as $r) {
            $en = (string) $r->en_path !== '' ? (string) $r->en_path : \ZPL\Router::en_path((string) $r->path);
            $kind = ['new' => 'nowy wpis', 'older' => 'starszy wpis', 'existing' => 'gotowa strona EN, uzupełniam braki'][$r->kind] ?? $r->kind;
            echo '<tr><td data-label="Strona PL"><a href="' . esc_url(home_url((string) $r->path)) . '" target="_blank" rel="noopener">' . esc_html(rawurldecode((string) $r->path)) . '</a><br><small>' . esc_html($kind) . ((int) $r->post_id ? ' · <a href="' . esc_url((string) get_edit_post_link((int) $r->post_id)) . '">edytuj wpis</a>' : '') . '</small></td>';
            echo '<td data-label="Wersja angielska"><a href="' . esc_url(home_url($en) . ($r->status === 'draft' ? '?zpte_preview=1' : '')) . '" target="_blank" rel="noopener">' . esc_html($en) . '</a>' . ($r->status === 'draft' ? '<br><small>podgląd dla administratora</small>' : '') . '</td>';
            echo '<td data-label="Stan"><span class="zpxPill ' . esc_attr(self::status_class((string) $r->status)) . '">' . esc_html(self::status_label((string) $r->status)) . '</span>' . ((int) $r->queued ? '<br><small>w kolejce</small>' : '') . ($r->message ? '<br><small>' . esc_html((string) $r->message) . '</small>' : '') . '</td>';
            echo '<td data-label="Teksty">' . number_format_i18n((int) $r->keys_ai) . ' z ' . number_format_i18n((int) $r->keys_total) . ' przetłumaczone automatycznie' . ((int) $r->keys_failed ? '<br><small class="zpte-bad">' . number_format_i18n((int) $r->keys_failed) . ' do ponowienia</small>' : '') . '</td>';
            echo '<td data-label="Sprawdzono">' . esc_html(self::when($r->checked_at)) . '</td><td data-label="Akcje" class="zpte-actions">';
            $hid = ['id' => (int) $r->id, 'back_tab' => 'pages', 'f' => $f];
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
            echo '<p class="zpxPager">';
            for ($i = 1; $i <= $pages; $i++) { echo $i === $paged ? '<strong>' . $i . '</strong> ' : '<a href="' . esc_url(self::url(['tab' => 'pages', 'f' => $f, 'paged' => $i])) . '">' . $i . '</a> '; }
            echo '</p>';
        }
    }

    private static function tab_edit(int $id): void {
        $row = Store::path($id);
        echo '<p class="zpxBackLink"><a href="' . esc_url(self::url(['tab' => 'pages', 'f' => 'new'])) . '">← Lista stron</a></p>';
        if (!$row) { echo '<div class="zpxEmpty zpte-gap">Nie ma takiej strony.</div>'; return; }
        $paged = max(1, (int) ($_GET['paged'] ?? 1));
        $per = 100;
        $total = Store::count_strings_of($id);
        $strings = Store::strings_of($id, $per, ($paged - 1) * $per);
        $kinds = ['text' => 'tekst na stronie', 'title' => 'tytuł', 'meta' => 'opis w Google i podgląd linku', 'attr' => 'opis obrazka lub przycisku', 'schema' => 'dane dla Google'];
        echo '<section class="zpxCard zpte-gap"><h2>' . esc_html(rawurldecode((string) $row->path)) . '</h2>';
        echo '<p>Poprawione teksty są zapamiętywane jako ręczne i ponowne tłumaczenie ich nie nadpisze. Znaczniki <code>&lt;1&gt;…&lt;/1&gt;</code> i <code>&lt;2/&gt;</code> oznaczają linki i pogrubienia: muszą zostać.</p>';
        if (!$strings) { echo '<div class="zpxEmpty">Ta strona nie ma tłumaczeń maszynowych.</div></section>'; return; }
        echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '"><input type="hidden" name="action" value="zpte"><input type="hidden" name="do" value="edit"><input type="hidden" name="id" value="' . (int) $id . '"><input type="hidden" name="paged" value="' . (int) $paged . '">';
        wp_nonce_field('zpte');
        echo '<table class="widefat striped zpte-table zpte-edit"><thead><tr><th style="width:45%">Polski</th><th>Angielski</th></tr></thead><tbody>';
        foreach ($strings as $s) {
            echo '<tr><td data-label="Polski"><code style="white-space:pre-wrap">' . esc_html((string) $s->pl) . '</code><br><small>' . esc_html($kinds[(string) $s->kind] ?? (string) $s->kind) . ((int) $s->manual ? ' · poprawione ręcznie' : '') . '</small></td>'
                . '<td data-label="Angielski"><textarea name="t[' . (int) $s->id . ']" rows="' . max(2, min(10, (int) ceil(strlen((string) $s->en) / 90))) . '">' . esc_textarea((string) $s->en) . '</textarea>'
                . '<input type="hidden" name="o[' . (int) $s->id . ']" value="' . esc_attr(md5((string) $s->en)) . '"></td></tr>';
        }
        echo '</tbody></table>';
        submit_button('Zapisz poprawki');
        echo '</form>';
        $pages = (int) ceil($total / $per);
        if ($pages > 1) {
            echo '<p class="zpxPager">';
            for ($i = 1; $i <= $pages; $i++) { echo $i === $paged ? '<strong>' . $i . '</strong> ' : '<a href="' . esc_url(self::url(['tab' => 'edit', 'id' => $id, 'paged' => $i])) . '">' . $i . '</a> '; }
            echo '</p>';
        }
        echo '</section>';
    }

    private static function tab_settings(): void {
        $s = Settings::all();
        $types = Settings::public_types();
        echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '" autocomplete="off"><input type="hidden" name="action" value="zpte"><input type="hidden" name="do" value="save">';
        wp_nonce_field('zpte');
        echo '<section class="zpxCard zpte-gap" id="zpte-key"><h2>Klucz OpenAI</h2>';
        if (Settings::key_source() === 'constant') {
            echo '<p>Klucz jest ustawiony w wp-config.php (stała <code>ZPTE_OPENAI_API_KEY</code>): ' . esc_html(Settings::key_hint()) . '.</p>';
        } else {
            $hint = Settings::key_hint();
            echo '<p><label for="zpte-key-input" class="screen-reader-text">Klucz OpenAI</label><input id="zpte-key-input" type="password" name="api_key" class="regular-text code zpte-wide" autocomplete="new-password" spellcheck="false" placeholder="' . esc_attr($hint !== '' ? 'zapisany: ' . $hint . ' (wklej nowy, aby zmienić)' : 'sk-…') . '"></p>';
            echo '<p class="description">Klucz utworzysz na platform.openai.com → API keys. Zapisuję go zaszyfrowany i nigdy nie pokazuję w całości. Puste pole zostawia obecny klucz.</p>';
            if ($hint !== '') { echo '<p><label><input type="checkbox" name="delete_key" value="1"> Usuń zapisany klucz</label></p>'; }
        }
        echo '</section>';

        $model = Settings::model();
        $custom = !isset(self::MODELS[$model]);
        $limit = (int) $s['daily_chars'];
        $limits = self::LIMITS;
        if (!isset($limits[$limit])) { $limits = [$limit => number_format_i18n($limit) . ' znaków (obecny)'] + $limits; }
        echo '<section class="zpxCard zpte-gap"><h2>Tłumaczenie</h2><table class="form-table zpte-form" role="presentation"><tbody>';
        echo '<tr id="zpte-enabled"><th>Codzienne tłumaczenie</th><td><label><input type="checkbox" name="enabled" value="1"' . checked(!empty($s['enabled']), true, false) . '> Codziennie o 3:00 tłumacz nowe wpisy i uzupełniaj braki na stronach angielskich</label></td></tr>';
        echo '<tr><th><label for="zpte-model">Model OpenAI</label></th><td><select id="zpte-model" name="model_choice">';
        if ($custom) { echo '<option value="' . esc_attr($model) . '" selected>' . esc_html($model . ' (obecny)') . '</option>'; }
        foreach (self::MODELS as $m => $label) { echo '<option value="' . esc_attr($m) . '"' . selected($model, $m, false) . '>' . esc_html($label) . '</option>'; }
        echo '</select><p class="description"><label for="zpte-model-custom">Inny model (zostaw puste, jeśli nie wiesz):</label><br><input id="zpte-model-custom" name="model_custom" class="regular-text code" placeholder="np. gpt-5-mini"></p>'
            . '<p class="description">Koszt zależy od długości tekstów i cennika OpenAI. „Sprawdź połączenie” na zakładce Status pokaże, czy model jest dostępny dla Twojego klucza.</p></td></tr>';
        echo '<tr><th>Nowe wpisy</th><td><label><input type="radio" name="mode" value="publish"' . checked($s['mode'] !== 'draft', true, false) . '> publikuj wersję angielską od razu</label><br><label><input type="radio" name="mode" value="draft"' . checked($s['mode'] === 'draft', true, false) . '> zapisz jako szkic, opublikuję ręcznie (Strony → Szkice do akceptacji)</label></td></tr>';
        echo '<tr><th>Co tłumaczyć</th><td>';
        foreach ($types as $name => $label) { echo '<label class="zpte-check"><input type="checkbox" name="types[]" value="' . esc_attr($name) . '"' . checked(in_array($name, (array) $s['types'], true), true, false) . '> ' . esc_html($label) . '</label>'; }
        echo '</td></tr>';
        echo '<tr><th><label for="zpte-since">Nowe wpisy = opublikowane od</label></th><td><input id="zpte-since" type="date" name="since" value="' . esc_attr((string) $s['since']) . '"></td></tr>';
        echo '<tr><th>Starsze wpisy</th><td><label><input type="checkbox" name="older" value="1"' . checked(!empty($s['older']), true, false) . '> Co noc tłumacz też starsze wpisy i strony bez wersji angielskiej</label><p class="description">Można to zrobić też ręcznie: Strony → Bez wersji EN → Przetłumacz. Do czasu tłumaczenia taka strona ma pod /en/… polską treść i nie trafia do Google.</p></td></tr>';
        echo '<tr><th>Braki na stronach EN</th><td><label><input type="checkbox" name="gaps" value="1"' . checked(!empty($s['gaps']), true, false) . '> Uzupełniaj nieprzetłumaczone teksty na gotowych stronach angielskich (nowe sekcje, stopka, menu)</label><p class="description">Gotowe teksty biorę ze słownika i z pamięci tłumaczeń; do OpenAI wysyłam tylko brakujące.</p></td></tr>';
        echo '<tr id="zpte-limit"><th><label for="zpte-limit-select">Dzienny limit</label></th><td><select id="zpte-limit-select" name="daily_chars">';
        foreach ($limits as $n => $label) { echo '<option value="' . (int) $n . '"' . selected($limit, (int) $n, false) . '>' . esc_html($label) . '</option>'; }
        echo '</select><p class="description">Ile polskiego tekstu dziennie wysyłam do OpenAI. Gdy limit się skończy, dokończę następnego dnia.</p></td></tr>';
        echo '<tr><th><label for="zpte-instr">Dodatkowe instrukcje</label></th><td><textarea id="zpte-instr" name="instructions" rows="4" placeholder="np. Zwracaj się do klienta per you. „Wycena” tłumacz jako „quote”.">' . esc_textarea((string) $s['instructions']) . '</textarea></td></tr>';
        echo '</tbody></table>';
        submit_button('Zapisz ustawienia');
        echo '</section></form>';

        echo '<details class="zpxCard zpte-gap zpte-danger"><summary>Usuń dane tłumacza</summary><p>Usuwa wszystkie tłumaczenia maszynowe, listę stron i angielskie adresy dodane przez tłumacza. Ustawienia i klucz zostają.</p>'
            . self::button('purge', 'Usuń tłumaczenia maszynowe', [], 'button button-link-delete', 'Usunąć wszystkie tłumaczenia maszynowe? Strony EN tłumaczone przez OpenAI wrócą do stanu sprzed tłumaczenia.') . '</details>';
    }

    private static function tab_log(): void {
        $log = Log::all();
        if (!$log) { echo '<div class="zpxEmpty zpte-gap">Dziennik jest pusty.</div>'; return; }
        echo '<div class="zpte-gap"><table class="widefat striped zpte-table zpte-log"><thead><tr><th style="width:150px">Kiedy</th><th>Zdarzenie</th></tr></thead><tbody>';
        foreach ($log as $e) {
            $cls = ($e['l'] ?? '') === 'error' ? 'zpte-bad' : (($e['l'] ?? '') === 'warning' ? 'zpte-warn' : '');
            echo '<tr><td data-label="Kiedy">' . esc_html(self::day_time((int) ($e['t'] ?? 0))) . '</td><td data-label="Zdarzenie" class="' . $cls . '">' . esc_html((string) ($e['m'] ?? '')) . '</td></tr>';
        }
        echo '</tbody></table></div>';
    }

    // ---------------------------------------------------------------- actions

    /** Queue one Polish address. Returns [path that was queued, error]. */
    private static function enqueue(string $raw): array {
        $raw = trim($raw);
        $p = (string) wp_parse_url($raw, PHP_URL_PATH);
        $path = \ZPL\Router::norm_path(\ZPL\Router::rel($p !== '' ? $p : $raw));
        $why = Bridge::ineligible($path);
        if ($raw === '' || $why !== '') { return ['', 'Tego adresu nie tłumaczę' . ($why !== '' ? ': ' . $why : '') . '.']; }
        $pid = url_to_postid(home_url($path));
        $row = Store::path_by_path($path);
        if (!$row) {
            $kind = Bridge::shipped($path) ? 'existing' : 'older';
            $nid = Store::path_add($path, ['post_id' => max(0, (int) $pid), 'kind' => $kind, 'status' => $kind === 'existing' ? 'published' : 'queued']);
            $row = Store::path($nid);
        }
        if (!$row) { return ['', 'Nie udało się dodać ' . $path . ' do kolejki.']; }
        if ($row->status === 'off' || $row->status === 'skipped') { Store::path_update((int) $row->id, ['status' => $row->kind === 'existing' ? 'published' : 'queued', 'tries' => 0]); }
        Store::queue((int) $row->id, 5);
        return [$path, ''];
    }

    public static function handle(): void {
        if (!current_user_can('manage_options')) { wp_die('Brak uprawnień.'); }
        check_admin_referer('zpte');
        $do = sanitize_key($_POST['do'] ?? '');
        $id = (int) ($_POST['id'] ?? 0);
        $back = ['tab' => 'pages'];
        $backTab = sanitize_key($_POST['back_tab'] ?? '');
        $backF = sanitize_key($_POST['f'] ?? '');
        if ($backTab === 'status') { $back = ['tab' => 'status']; }
        elseif ($backF !== '') { $back = ['tab' => 'pages', 'f' => $backF]; }
        $msg = '';
        $err = '';
        $nokey = 'Najpierw wklej klucz OpenAI w zakładce Ustawienia. Bez niego nic nie tłumaczę.';
        Store::install();
        switch ($do) {
            case 'save':
                $back = ['tab' => 'settings'];
                $before = Settings::all();
                $types = array_values(array_intersect(array_map('sanitize_key', (array) ($_POST['types'] ?? [])), array_keys(Settings::public_types())));
                $since = sanitize_text_field(wp_unslash((string) ($_POST['since'] ?? '')));
                $model = trim(sanitize_text_field(wp_unslash((string) ($_POST['model_custom'] ?? ''))));
                if ($model === '') { $model = trim(sanitize_text_field(wp_unslash((string) ($_POST['model_choice'] ?? ($_POST['model'] ?? ''))))); }
                $new = [
                    'enabled' => empty($_POST['enabled']) ? 0 : 1,
                    'model' => preg_match('~^[A-Za-z0-9._:-]{2,80}$~', $model) ? $model : Settings::DEFAULT_MODEL,
                    'mode' => ($_POST['mode'] ?? '') === 'draft' ? 'draft' : 'publish',
                    'types' => $types,
                    'since' => preg_match('~^\d{4}-\d{2}-\d{2}$~', $since) ? $since : (string) Settings::get('since'),
                    'older' => empty($_POST['older']) ? 0 : 1,
                    'gaps' => empty($_POST['gaps']) ? 0 : 1,
                    'daily_chars' => max(0, (int) ($_POST['daily_chars'] ?? 0)),
                    'instructions' => sanitize_textarea_field(wp_unslash((string) ($_POST['instructions'] ?? ''))),
                ];
                Settings::update($new);
                $msg = 'Zapisano ustawienia.';
                // A paused run (no credit, unavailable model, daily limit) only resumes when something that can fix it changed.
                $fixes = $new['model'] !== (string) $before['model'] || $new['daily_chars'] !== (int) $before['daily_chars'] || $new['enabled'] !== (int) $before['enabled'];
                if (!empty($_POST['delete_key'])) {
                    Settings::delete_key();
                    $msg .= ' Klucz usunięty.';
                    $fixes = true;
                }
                $key = trim((string) wp_unslash($_POST['api_key'] ?? ''));
                $saved = false;
                if ($key !== '') {
                    if (!Settings::looks_like_key($key)) {
                        $err = 'To nie wygląda na klucz OpenAI (oczekiwany ciąg zaczynający się zwykle od „sk-”, bez spacji). Klucza nie zapisano.';
                    } else {
                        Settings::save_key($key);
                        $msg .= ' Klucz zapisany (' . Settings::key_hint() . ').';
                        Log::add('Zapisano nowy klucz API (' . Settings::key_hint() . ').');
                        $saved = true;
                        $fixes = true;
                    }
                }
                unset($key);
                if ($fixes) {
                    Log::clear_alert();
                    Worker::resume();
                }
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
                if (Settings::api_key() === '') { $err = $nokey; break; }
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
                if (!Bridge::hooks_ready()) { $err = 'Moduł językowy w tej wersji wtyczki nie współpracuje z tłumaczem. Wgraj aktualną wersję ZP Suite.'; break; }
                if (Settings::api_key() === '') { $err = $nokey; break; }
                $summary = Worker::plan('admin');
                $msg = $summary . (Store::queued_count() > 0 ? ' Postęp widać poniżej.' : '');
                break;
            case 'retry':
            case 'clear_alert':
                $back = ['tab' => 'status'];
                Log::clear_alert();
                if ($do === 'retry') {
                    Worker::resume();
                    $msg = 'Wznowiono. Jeśli problem wróci, komunikat pojawi się znowu.';
                }
                break;
            case 'add':
                if (Settings::api_key() === '') { $err = $nokey; break; }
                [$path, $err] = self::enqueue((string) wp_unslash($_POST['path'] ?? ''));
                if ($path !== '') {
                    Worker::kick(0);
                    $msg = 'Dodano do kolejki: ' . rawurldecode($path) . '. Postęp zobaczysz na zakładce Status.';
                }
                break;
            case 'add_all':
                if (Settings::api_key() === '') { $err = $nokey; break; }
                $n = 0;
                foreach (self::missing_pages() as $it) {
                    $row = $it['row'];
                    if ($row && ((int) $row->queued || !in_array($row->status, ['queued', 'error'], true))) { continue; }
                    [$path] = self::enqueue($it['path']);
                    if ($path !== '') { $n++; }
                }
                if ($n) { Worker::kick(0); }
                $msg = $n ? 'Dodano do kolejki: ' . $n . ' ' . self::pages_word($n) . '. Postęp zobaczysz na zakładce Status.' : 'Nie ma stron do dodania.';
                break;
            case 'queue':
                $row = Store::path($id);
                if ($row) {
                    if (in_array($row->status, ['skipped', 'error'], true)) { Store::path_update($id, ['status' => $row->kind === 'existing' ? 'published' : 'queued', 'tries' => 0]); }
                    Store::path_update($id, ['source_modified' => null]);
                    Store::queue($id, 5);
                    Worker::kick(0);
                    $msg = 'Dodano do kolejki. Postęp zobaczysz na zakładce Status.';
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
                    $msg = 'Włączono: tłumaczę w tle.';
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
                if ($bad) { $err = 'Nie zapisano poprawek: ' . $bad . ' (znaczniki <1>…</1> muszą się zgadzać z tekstem polskim).'; }
                break;
            case 'purge':
                $back = ['tab' => 'settings'];
                foreach (Store::paths(['kind' => ['new', 'older']]) as $row) {
                    if ((int) $row->route_added && (string) $row->en_path !== '') { Bridge::remove_route((string) $row->path, (string) $row->en_path); }
                }
                Worker::unschedule();
                delete_option(Worker::STATE);
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
