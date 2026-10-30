<?php
namespace ZPTE;

if (!defined('ABSPATH')) { exit; }

/**
 * ZP Suite → Tłumacz EN: status and progress, pages, translations, settings and log.
 * 2.1.0: one panel for every language: the language tabs (EN, DE, ES) on top switch all of it (parameter zpte_lang).
 */
final class Admin {
    const SLUG = 'zp-tlumacz-en';
    const PER_PAGE = 50;
    const TABS = ['overview' => 'Przegląd', 'pages' => 'Strony', 'texts' => 'Teksty', 'settings' => 'Ustawienia', 'log' => 'Dziennik'];
    /** Models offered in the settings (any other name can be typed in). */
    const MODELS = ['gpt-6-luna' => 'gpt-6-luna (zalecany: szybki i tani)'];
    /** 2.1.0: models offered for German and Spanish (copy written for people there, not a quick translation). */
    const AI_MODELS = [
        'gpt-6.1-sol' => 'gpt-6.1-sol (zalecany: najlepszy model GPT-6.1)',
        'gpt-6-astra' => 'gpt-6-astra (najmocniejszy GPT-6, ok. 5× droższy)',
        'gpt-6.1-terra' => 'gpt-6.1-terra (tańszy, słabszy)',
    ];
    const MODELS_OPTION = 'zpte2_models';

    /** 2.1.0: the language the panel shows (zpte_lang in the address, the form or the AJAX request). */
    private static function lang(): string {
        $v = $_REQUEST['zpte_lang'] ?? 'en';
        $v = is_string($v) ? sanitize_key(wp_unslash($v)) : 'en';
        return Lang::valid($v) ? $v : 'en';
    }

    /** 2.1.0: "EN", "DE", "ES" of the current language. */
    private static function code(): string { return Lang::CODE[Lang::code()]; }

    public static function boot(): void {
        // After the Suite trims its submenu (priority 9999), so this entry stays visible.
        add_action('admin_menu', [self::class, 'menu'], 10000);
        add_action('admin_post_zpte2', [self::class, 'handle']);
        add_action('admin_notices', [self::class, 'notices']);
        add_action('wp_ajax_zpte2_status', [self::class, 'ajax_status']);
        add_action('wp_ajax_zpte2_step', [self::class, 'ajax_step']);
        add_action('admin_enqueue_scripts', [self::class, 'assets']);
    }

    public static function menu(): void {
        global $admin_page_hooks;
        $parent = isset($admin_page_hooks['zp-suite']) ? 'zp-suite' : 'options-general.php';
        add_submenu_page($parent, 'Tłumacz EN', 'Tłumacz EN', 'manage_options', self::SLUG, [self::class, 'page']);
    }

    public static function url(array $args = []): string {
        $base = ['page' => self::SLUG];
        if (!Lang::is_en()) { $base['zpte_lang'] = Lang::code(); }
        return add_query_arg(array_merge($base, $args), admin_url('admin.php'));
    }

    private static function tab(): string {
        $t = isset($_GET['tab']) && is_string($_GET['tab']) ? sanitize_key(wp_unslash($_GET['tab'])) : 'overview';
        if ($t === 'status') { $t = 'overview'; } // older links
        return isset(self::TABS[$t]) ? $t : 'overview';
    }

    public static function assets(): void {
        if (($_GET['page'] ?? '') !== self::SLUG || !current_user_can('manage_options')) { return; }
        wp_enqueue_style('zpte2-admin', plugins_url('assets/admin.css', ZPTE_DIR . 'translator.php'), [], ZPTE_VERSION);
        if (self::tab() !== 'overview') { return; }
        wp_enqueue_script('zpte2-admin', plugins_url('assets/admin.js', ZPTE_DIR . 'translator.php'), [], ZPTE_VERSION, true);
        wp_localize_script('zpte2-admin', 'zpte2Admin', ['url' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce('zpte2_progress'), 'lang' => self::lang()]);
    }

    // ------------------------------------------------------------------ AJAX

    private static function ajax_ok(): bool {
        if (!current_user_can('manage_options') || !check_ajax_referer('zpte2_progress', 'nonce', false)) {
            // 200 with an error flag: the panel stops asking, without noise in the browser console.
            wp_send_json(['success' => false, 'data' => ['reload' => true]]);
        }
        return true;
    }

    public static function ajax_status(): void {
        self::ajax_ok();
        wp_send_json_success(['progress' => Lang::with(self::lang(), [Worker::class, 'progress']), 'nonce' => wp_create_nonce('zpte2_progress')]);
    }

    /** The open panel moves the work on when the background steps do not run (hosting without loopback or cron). */
    public static function ajax_step(): void {
        self::ajax_ok();
        if (function_exists('session_write_close')) { @session_write_close(); }
        $p = Lang::with(self::lang(), [Worker::class, 'progress']);
        if ($p['stalled']) { Worker::work('panel'); } // a step works on every language
        wp_send_json_success(['progress' => Lang::with(self::lang(), [Worker::class, 'progress']), 'nonce' => wp_create_nonce('zpte2_progress')]);
    }

    // --------------------------------------------------------------- helpers

    /** "dziś 03:00", "jutro 03:00", "wczoraj 14:10" or "5.10.2026 03:00" (site time). */
    public static function day_time(int $ts): string {
        if ($ts <= 0) { return '—'; }
        $d = wp_date('Y-m-d', $ts);
        $t = wp_date('H:i', $ts);
        if ($d === wp_date('Y-m-d')) { return 'dziś ' . $t; }
        if ($d === wp_date('Y-m-d', time() + DAY_IN_SECONDS)) { return 'jutro ' . $t; }
        if ($d === wp_date('Y-m-d', time() - DAY_IN_SECONDS)) { return 'wczoraj ' . $t; }
        return wp_date('j.m.Y', $ts) . ' ' . $t;
    }

    private static function when(?string $gmt): string {
        if ($gmt === null || $gmt === '' || strpos($gmt, '0000') === 0) { return '—'; }
        $ts = strtotime($gmt . ' UTC');
        return $ts ? self::day_time($ts) : '—';
    }

    /** 1 strona, 2 strony, 5 stron. */
    public static function pages_word(int $n): string { return self::word($n, 'strona', 'strony', 'stron'); }

    /** Polish form for a number: one (1), few (2–4, 22–24…, not 12–14) or many (the rest). */
    public static function word(int $n, string $one, string $few, string $many): string {
        if ($n === 1) { return $one; }
        $d = $n % 10;
        $h = $n % 100;
        return $d >= 2 && $d <= 4 && ($h < 12 || $h > 14) ? $few : $many;
    }

    /** "1 fragment", "3 fragmenty", "5 fragmentów" (3.1.1: messages said "1 fragmentów"). */
    public static function n(int $n, string $one, string $few, string $many): string {
        return $n . ' ' . self::word($n, $one, $few, $many);
    }

    private static function usd(float $v): string {
        return '$' . number_format($v, $v < 1 ? 4 : 2, '.', ',');
    }

    private static function form(string $do, array $hidden = [], string $confirm = '', string $class = 'zpte2-inline'): string {
        $h = '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '" class="' . esc_attr($class) . '"'
            . ($confirm !== '' ? ' onsubmit="return confirm(' . esc_attr((string) wp_json_encode($confirm)) . ')"' : '') . '>'
            . '<input type="hidden" name="action" value="zpte2"><input type="hidden" name="do" value="' . esc_attr($do) . '">'
            . (Lang::is_en() ? '' : '<input type="hidden" name="zpte_lang" value="' . esc_attr(Lang::code()) . '">')
            . wp_nonce_field('zpte2', '_wpnonce', true, false);
        foreach ($hidden as $k => $v) { $h .= '<input type="hidden" name="' . esc_attr((string) $k) . '" value="' . esc_attr((string) $v) . '">'; }
        return $h;
    }

    private static function button(string $do, string $label, array $hidden = [], string $class = 'button', string $confirm = ''): string {
        return self::form($do, $hidden, $confirm) . '<button type="submit" class="' . esc_attr($class) . '">' . esc_html($label) . '</button></form>';
    }

    // ---------------------------------------------------------------- summary

    /**
     * Published Polish posts and pages (types chosen in Ustawienia) with no English version yet: visitors from
     * abroad read them in Polish. Newest first; noindex and client-only pages are left out.
     * @return array<int,array{post_id:int,path:string,title:string,type:string,date:string}>
     */
    public static function missing_pages(): array {
        static $memo = []; // 2.1.0: per language
        $l = Lang::code();
        if (isset($memo[$l])) { return $memo[$l]; }
        $key = Index::count() . ':' . Routes::version() . ':' . implode(',', Settings::types());
        $cached = get_transient(Lang::opt('zpte2_missing'));
        if (is_array($cached) && ($cached['k'] ?? '') === $key && is_array($cached['v'] ?? null)) { return $memo[$l] = $cached['v']; }
        $cache = [];
        $types = Bridge::ready() ? Settings::types() : [];
        if ($types) {
            $ids = get_posts(['post_type' => $types, 'post_status' => 'publish', 'has_password' => false, 'posts_per_page' => -1,
                'orderby' => 'date', 'order' => 'DESC', 'fields' => 'ids', 'no_found_rows' => true, 'suppress_filters' => true, 'ignore_sticky_posts' => true]);
            if ($ids) { _prime_post_caches(array_map('intval', $ids), false, true); }
            foreach ((array) $ids as $pid) {
                $pid = (int) $pid;
                $path = Sync::path_of_post($pid);
                if ($path === '' || Sync::ineligible($path) !== '') { continue; }
                if (\ZPL\Router::has_route($path, Lang::code()) && \ZPL\Dict::has_page($path, Lang::code())) { continue; }
                $robots = get_post_meta($pid, 'rank_math_robots', true);
                if (is_array($robots) && in_array('noindex', $robots, true)) { continue; }
                $cache[] = ['post_id' => $pid, 'path' => $path, 'title' => html_entity_decode(get_the_title($pid), ENT_QUOTES, 'UTF-8'),
                    'type' => (string) get_post_type($pid), 'date' => (string) get_the_date('j.m.Y', $pid)];
            }
        }
        set_transient(Lang::opt('zpte2_missing'), ['k' => $key, 'v' => $cache], 10 * MINUTE_IN_SECONDS);
        return $memo[$l] = $cache;
    }

    /**
     * One sentence: is it working, and if not, what to do (ZP Suite command center reads [0] and [1]).
     * @return array{0:string,1:string,2:string} level (ok|warn|bad), text, buttons (HTML)
     */
    public static function headline(): array {
        $settings = self::url(['tab' => 'settings']);
        if (!Bridge::hooks_ready()) {
            return ['bad', 'Moduł Języki PL/EN w tej wersji nie współpracuje z tłumaczem. Wgraj aktualną wersję ZP Suite.', ''];
        }
        if (!Lang::is_en() && (int) Settings::get('on') !== 1) {
            return ['warn', 'Wersja ' . self::code() . ' jest wyłączona: strona nie ma jej adresów i nie jest tłumaczona.', '<a class="button" href="' . esc_url($settings) . '">Włącz w ustawieniach</a>'];
        }
        if (Settings::api_key() === '') {
            $text = Settings::key_unreadable()
                ? 'Zapisanego klucza OpenAI nie da się odczytać (zmieniły się klucze bezpieczeństwa WordPressa). Wklej klucz ponownie.'
                : 'Brak klucza OpenAI: obecne strony ' . self::code() . ' działają, ale nowe i zmienione teksty czekają na tłumaczenie.';
            return ['bad', $text, '<a class="button button-primary" href="' . esc_url($settings . '#zpte2-key') . '">Wklej klucz</a>'];
        }
        if ($a = Log::current_alert()) {
            return ['bad', 'Wstrzymany: ' . (string) $a['m'], self::button('resume', 'Spróbuj ponownie', [], 'button button-primary') . ' <a class="button" href="' . esc_url($settings) . '">Ustawienia</a>'];
        }
        $p = Worker::progress();
        if ($p['paused'] !== '' && $p['retry_at'] > time()) {
            return ['warn', $p['paused'], self::button('resume', 'Wznów teraz')];
        }
        if (!Settings::get('enabled')) {
            return ['warn', 'Codzienne sprawdzenie jest wyłączone: nowe wpisy dostaną wersję ' . self::code() . ' tylko po publikacji lub ręcznym sprawdzeniu.', '<a class="button" href="' . esc_url($settings) . '">Włącz w ustawieniach</a>'];
        }
        if (!Import::done()) {
            return ['ok', 'Przenoszę obecne tłumaczenia i adresy EN do nowego tłumacza (bez kosztów OpenAI). Strony EN działają w tym czasie jak dotąd.', ''];
        }
        if ($p['work']) {
            return ['ok', 'Tłumaczę: ' . $p['message'], ''];
        }
        $s = Worker::state();
        $next = (int) (wp_next_scheduled(Worker::DAILY) ?: Worker::next_daily());
        $text = 'Działa. Wersji ' . self::code() . ': ' . Index::count() . '. ' . ((int) $s['daily_at'] ? 'Ostatnie sprawdzenie: ' . self::day_time((int) $s['daily_at']) . '. ' : '') . 'Następne: ' . self::day_time($next) . '.';
        return ['ok', $text, self::button('run', 'Sprawdź wszystko teraz')];
    }

    public static function notices(): void {
        if (!current_user_can('manage_options') || (($_GET['page'] ?? '') === self::SLUG)) { return; }
        // 2.1.0: an alert of any language (a German or Spanish one opens its own tab).
        foreach (Lang::ALL as $l) {
            Lang::with($l, static function () {
                if (!Lang::is_en() && (int) Settings::get('on') !== 1) { return; }
                $a = Log::current_alert();
                if (!$a) { return; }
                echo '<div class="notice notice-error"><p><strong>Tłumacz ' . esc_html(self::code()) . ' wstrzymany:</strong> ' . esc_html((string) $a['m'])
                    . ' <a href="' . esc_url(self::url()) . '">Otwórz Tłumacza ' . esc_html(self::code()) . '</a></p></div>';
            });
        }
    }

    // ------------------------------------------------------------------- page

    public static function page(): void {
        if (!current_user_can('manage_options')) { return; }
        Lang::with(self::lang(), [self::class, 'page_lang']);
    }

    /** 2.1.0: the language tabs, each with where its version stands. */
    private static function langs_nav(): void {
        echo '<nav class="zpte2-tabs zpte2-langs" aria-label="Język tłumaczenia">';
        foreach (Lang::ALL as $l) {
            [$label, $note] = Lang::with($l, static function () {
                $name = ['en' => 'Angielski', 'de' => 'Niemiecki', 'es' => 'Hiszpański'][Lang::code()];
                if (!Bridge::ready() || !Store::ready()) { return [$name, '']; }
                if (!Lang::is_en() && (int) Settings::get('on') !== 1) { return [$name, 'wyłączony']; }
                $p = Worker::progress();
                if ($p['work']) { return [$name, $p['percent'] . '%']; }
                return [$name, (string) Index::count()];
            });
            $url = add_query_arg(array_merge(['page' => self::SLUG, 'tab' => self::tab()], $l === 'en' ? [] : ['zpte_lang' => $l]), admin_url('admin.php'));
            echo '<a href="' . esc_url($url) . '"' . ($l === Lang::code() ? ' class="is-active" aria-current="page"' : '') . '>' . esc_html(Lang::CODE[$l] . ' · ' . $label) . ($note !== '' ? ' <small>' . esc_html($note) . '</small>' : '') . '</a>';
        }
        echo '</nav>';
    }

    /** The panel in the current language. */
    public static function page_lang(): void {
        if (Bridge::ready()) { Store::install(); }
        $tab = self::tab();
        $code = self::code();
        echo '<div class="wrap zpte2"><h1>' . esc_html(Lang::is_en() ? 'Tłumacz EN' : 'Tłumacz ' . $code) . '</h1>';
        self::langs_nav();
        if (Lang::is_en()) {
            echo '<p class="zpte2-lead">Jeden tłumacz całej strony: nowe i zmienione teksty po angielsku, angielskie adresy z przekierowaniami, mapy witryny EN i hreflang. Sprawdza wszystko codziennie o 03:00 i od razu po publikacji wpisu.</p>';
        } else {
            $who = Lang::code() === 'de' ? 'niemiecki copywriter dla klientów z Niemiec' : 'hiszpański copywriter dla klientów z Hiszpanii';
            echo '<p class="zpte2-lead">' . esc_html('Wersja ' . $code . ' całej strony i wszystkich wpisów, pisana tak, jak napisałby ją ' . $who . ': naturalny język i frazy, których tam szukają w Google, ceny w euro. Własne adresy /' . Lang::code() . '/… z przekierowaniami, mapy witryny ' . $code . ' i hreflang. Sprawdza wszystko codziennie o 03:00 i od razu po publikacji wpisu.') . '</p>';
        }
        self::flash();
        echo '<nav class="zpte2-tabs" aria-label="Tłumacz ' . esc_attr($code) . '">';
        foreach (self::TABS as $k => $label) {
            echo '<a href="' . esc_url(self::url(['tab' => $k])) . '"' . ($k === $tab ? ' class="is-active" aria-current="page"' : '') . '>' . esc_html($label) . '</a>';
        }
        echo '</nav>';
        if (!Bridge::ready()) {
            echo '<div class="zpte2-box is-bad"><p>Brak modułu Języki PL/EN, więc tłumacz nie działa.</p></div></div>';
            return;
        }
        switch ($tab) {
            case 'pages': self::tab_pages(); break;
            case 'texts': self::tab_texts(); break;
            case 'settings': self::tab_settings(); break;
            case 'log': self::tab_log(); break;
            default: self::tab_overview();
        }
        echo '</div>';
    }

    private static function flash(): void {
        $msg = isset($_GET['zpte2_msg']) && is_string($_GET['zpte2_msg']) ? sanitize_text_field(wp_unslash($_GET['zpte2_msg'])) : '';
        if ($msg === '') { return; }
        $bad = !empty($_GET['zpte2_err']);
        echo '<div class="zpte2-flash' . ($bad ? ' is-bad' : '') . '" role="status"><p>' . esc_html($msg) . '</p></div>';
    }

    private static function tab_overview(): void {
        [$level, $text, $actions] = self::headline();
        echo '<div class="zpte2-box is-' . esc_attr($level) . '"><p>' . esc_html($text) . '</p>' . ($actions !== '' ? '<div class="zpte2-actions">' . $actions . '</div>' : '') . '</div>';

        $p = Worker::progress();
        echo '<section class="zpte2-card" id="zpte2-progress" data-work="' . ($p['work'] ? '1' : '0') . '" aria-labelledby="zpte2-progress-title">'
            . '<div class="zpte2-head"><h2 id="zpte2-progress-title">Bieżący przebieg</h2><strong data-zpte2="percent">' . (int) $p['percent'] . '%</strong></div>'
            . '<div class="zpte2-bar" role="progressbar" aria-label="Postęp przebiegu" aria-valuemin="0" aria-valuemax="100" aria-valuenow="' . (int) $p['percent'] . '"><span style="width:' . (int) $p['percent'] . '%"></span></div>'
            . '<p class="zpte2-message" data-zpte2="message" role="status" aria-live="polite">' . esc_html($p['message'] !== '' ? $p['message'] : 'Czekam na następne sprawdzenie.') . '</p>'
            . '<ul class="zpte2-counts">'
            . '<li><strong data-zpte2="done">' . (int) $p['done'] . '</strong> z <strong data-zpte2="total">' . (int) $p['total'] . '</strong> stron sprawdzonych</li>'
            . '<li><strong data-zpte2="queued">' . (int) $p['queued'] . '</strong> w kolejce</li>'
            . '<li><strong data-zpte2="en">' . (int) $p['en'] . '</strong> wersji ' . esc_html(self::code()) . '</li>'
            . '<li><strong data-zpte2="attention">' . (int) $p['attention'] . '</strong> do poprawy</li></ul>'
            . '<p class="zpte2-current" data-zpte2="current">' . ($p['current'] !== '' && $p['work'] ? esc_html('Teraz: ' . $p['current']) : '') . '</p>'
            . '<p class="description">Postęp odświeża się sam. Tłumaczenie działa w tle także przy zamkniętym panelu; gdy serwer wstrzyma zadania w tle, otwarty panel prowadzi je dalej.</p>'
            . '<div class="zpte2-actions">' . self::button('run', 'Sprawdź wszystko teraz', [], 'button button-primary')
            . ($p['paused'] !== '' || Log::current_alert() ? ' ' . self::button('resume', 'Wznów') : '') . '</div></section>';

        $today = Log::usage();
        $month = Log::month();
        $cap = (float) Settings::get('monthly_usd');
        $daily = (int) Settings::get('daily_chars');
        $s = Worker::state();
        $next = (int) (wp_next_scheduled(Worker::DAILY) ?: 0);
        echo '<div class="zpte2-grid">';
        echo '<section class="zpte2-card"><h2>Koszt OpenAI</h2><dl class="zpte2-dl">'
            . '<dt>Dziś</dt><dd>' . esc_html(self::usd((float) $today['usd']) . ' · ' . number_format_i18n((int) $today['chars']) . ' ' . self::word((int) $today['chars'], 'znak', 'znaki', 'znaków') . ($daily > 0 ? ' z ' . number_format_i18n($daily) : '') . ' · ' . self::n((int) $today['requests'], 'zapytanie', 'zapytania', 'zapytań')) . '</dd>'
            . '<dt>Ten miesiąc</dt><dd>' . esc_html(self::usd((float) $month['usd']) . ($cap > 0 ? ' z limitu ' . self::usd($cap) : '')) . '</dd>'
            . '<dt>Model</dt><dd>' . esc_html(Settings::model() . ' · rozumowanie ' . (Settings::EFFORTS[Settings::effort()] ?? Settings::effort())) . '</dd>'
            . (Lang::is_en() ? '' : '<dt>Na stronie</dt><dd>' . esc_html(self::public_state()) . '</dd>') . '</dl></section>';
        echo '<section class="zpte2-card"><h2>Harmonogram</h2><dl class="zpte2-dl">'
            . '<dt>Codziennie</dt><dd>' . esc_html(Settings::get('enabled') ? '03:00 (czas strony) — nowe, zmienione i wszystkie pozostałe strony' : 'wyłączone') . '</dd>'
            . '<dt>Ostatnie</dt><dd>' . esc_html(self::day_time((int) $s['daily_at'])) . '</dd>'
            . '<dt>Następne</dt><dd>' . esc_html($next ? self::day_time($next) : '—') . '</dd>'
            . '<dt>Po publikacji</dt><dd>' . esc_html(Settings::get('auto') ? 'od razu (tylko nowe i zmienione teksty)' : 'wyłączone') . '</dd></dl></section>';
        echo '</div>';

        $missing = self::missing_pages();
        echo '<section class="zpte2-card"><h2>' . esc_html('Wpisy i strony bez wersji ' . self::code()) . '</h2>';
        if (!$missing) {
            echo '<p>' . esc_html(Lang::is_en() ? 'Każdy opublikowany wpis i strona ma wersję angielską.' : 'Każdy opublikowany wpis i strona ma wersję ' . self::code() . '.') . '</p></section>';
            return;
        }
        echo '<p>' . esc_html(count($missing) . ' ' . self::pages_word(count($missing)) . ' — goście z zagranicy widzą je po polsku. Tłumacz zajmie się nimi sam przy najbliższym przebiegu; możesz też uruchomić to od razu.') . '</p><ul class="zpte2-list">';
        foreach (array_slice($missing, 0, 15) as $m) {
            echo '<li><span><a href="' . esc_url(home_url($m['path'])) . '" target="_blank" rel="noopener">' . esc_html($m['title'] !== '' ? $m['title'] : $m['path']) . '</a><small>' . esc_html($m['path'] . ' · ' . $m['date']) . '</small></span>'
                . self::button('translate_post', 'Przetłumacz teraz', ['post_id' => $m['post_id']]) . '</li>';
        }
        echo '</ul></section>';
    }

    /** 2.1.0: is the German (Spanish) version offered to visitors, and why not yet. */
    private static function public_state(): string {
        $mode = (string) Settings::get('public');
        if ($mode === 'off') { return 'ukryta (wyłączona w ustawieniach)'; }
        if (\ZPL\Lang::is_public(Lang::code())) { return 'widoczna: przełącznik języka, hreflang, mapy witryny'; }
        if (!Index::has('/')) { return 'jeszcze niewidoczna: czeka na przetłumaczenie strony głównej'; }
        return 'jeszcze niewidoczna: pokaże się po pierwszym pełnym tłumaczeniu całej strony';
    }

    // ---------------------------------------------------------------- pages

    private static function filters(): array {
        return [
            'all' => ['Wszystkie', ['status' => ['new', 'done', 'partial', 'error']]],
            'en' => ['Z wersją ' . self::code(), ['status' => ['done', 'partial']]],
            'queue' => ['W kolejce', ['queued' => 1, 'status' => ['new', 'done', 'partial', 'error']]],
            'attention' => ['Do poprawy', ['attention' => true, 'status' => ['new', 'done', 'partial', 'error']]],
            'off' => ['Wyłączone', ['status' => ['off']]],
        ];
    }

    private static function tab_pages(): void {
        $filters = self::filters();
        $f = isset($_GET['f']) && is_string($_GET['f']) ? sanitize_key(wp_unslash($_GET['f'])) : 'all';
        if ($f === 'error') { $f = 'attention'; }
        if (!isset($filters[$f])) { $f = 'all'; }
        $q = isset($_GET['q']) && is_string($_GET['q']) ? trim(sanitize_text_field(wp_unslash($_GET['q']))) : '';
        $paged = max(1, (int) ($_GET['paged'] ?? 1));
        $where = $filters[$f][1];
        if ($q !== '') { $where['search'] = $q; }

        echo '<div class="zpte2-toolbar"><ul class="zpte2-filters">';
        foreach ($filters as $k => [$label, $w]) {
            echo '<li><a href="' . esc_url(self::url(['tab' => 'pages', 'f' => $k])) . '"' . ($k === $f ? ' class="is-active"' : '') . '>' . esc_html($label) . ' <span>' . (int) Store::count_pages($w) . '</span></a></li>';
        }
        echo '</ul><form method="get" class="zpte2-search"><input type="hidden" name="page" value="' . esc_attr(self::SLUG) . '"><input type="hidden" name="tab" value="pages"><input type="hidden" name="f" value="' . esc_attr($f) . '">'
            . (Lang::is_en() ? '' : '<input type="hidden" name="zpte_lang" value="' . esc_attr(Lang::code()) . '">')
            . '<label class="screen-reader-text" for="zpte2-q">Szukaj adresu</label><input type="search" id="zpte2-q" name="q" value="' . esc_attr($q) . '" placeholder="' . esc_attr('Szukaj adresu PL lub ' . self::code()) . '"><button class="button">Szukaj</button></form></div>';

        $total = Store::count_pages($where);
        $rows = Store::pages($where, self::PER_PAGE, ($paged - 1) * self::PER_PAGE, 'queued DESC, priority ASC, id DESC');
        if (!$rows) { echo '<div class="zpte2-card"><p>Brak stron w tym widoku.</p></div>'; return; }
        $routes = \ZPL\Router::routes(Lang::code());
        $code = self::code();
        $status = ['new' => 'Nowa', 'done' => 'Gotowa', 'partial' => 'Częściowo', 'error' => 'Błąd', 'off' => 'Wyłączona'];
        $stage = ['translating' => 'tłumaczy', 'route' => 'nadaje adres ' . $code];
        // 2.1.0: the two kinds of German and Spanish pages made from files.
        $virtual = ['dyn' => 'teksty skryptów strony (Studio wyceny, formularze, okna realizacji)', 'frame' => 'Studio wyceny w ramce (iframe)'];
        echo '<div class="zpte2-card zpte2-table-wrap"><table class="zpte2-table"><thead><tr><th scope="col">Strona</th><th scope="col">Adres ' . esc_html($code) . '</th><th scope="col">Stan</th><th scope="col">Teksty</th><th scope="col">Sprawdzono</th><th scope="col"><span class="screen-reader-text">Akcje</span></th></tr></thead><tbody>';
        foreach ($rows as $r) {
            $path = (string) $r->path;
            $en = (string) ($routes[$path] ?? '');
            $live = $en !== '' && strpos($en, Lang::prefix()) === 0 && Index::has($path);
            $state = $status[$r->status] ?? $r->status;
            if ($r->stage !== '' && isset($stage[$r->stage])) { $state .= ', ' . $stage[$r->stage]; }
            elseif ((int) $r->queued) { $state .= ', w kolejce'; }
            $cls = $r->status === 'error' || (int) $r->keys_failed > 0 ? 'bad' : ($r->status === 'done' ? 'ok' : ($r->status === 'off' ? 'off' : 'warn'));
            if (isset($virtual[(string) $r->kind])) {
                echo '<tr><td class="zpte2-break">' . esc_html($path) . '</td><td>' . esc_html($virtual[(string) $r->kind]) . '</td>';
            } else {
                echo '<tr><td class="zpte2-break"><a href="' . esc_url(home_url($path)) . '" target="_blank" rel="noopener">' . esc_html($path) . '</a></td>';
                echo '<td class="zpte2-break">' . ($en !== '' ? '<a href="' . esc_url(home_url($en)) . '" target="_blank" rel="noopener">' . esc_html($en) . '</a>' . ($live ? '' : ' <small>(jeszcze niewidoczna)</small>') . (Routes::polish($en) ? ' <small class="zpte2-warn">polskie słowa</small>' : '') : '—') . '</td>';
            }
            echo '<td><span class="zpte2-pill is-' . esc_attr($cls) . '">' . esc_html($state) . '</span>' . ((string) $r->message !== '' ? '<small>' . esc_html((string) $r->message) . '</small>' : '') . '</td>';
            echo '<td>' . (int) $r->keys_done . ' / ' . (int) $r->keys_total . ((int) $r->keys_failed ? '<small class="zpte2-warn">' . (int) $r->keys_failed . ' bez tłumaczenia</small>' : '') . '</td>';
            echo '<td class="zpte2-when">' . esc_html(self::when($r->checked_at)) . '</td><td class="zpte2-row-actions">';
            if ($r->status !== 'off') {
                echo self::button('page_check', 'Sprawdź teraz', ['id' => (int) $r->id]);
                echo self::button('page_force', 'Przetłumacz od nowa', ['id' => (int) $r->id], 'button-link', 'Przetłumaczyć całą stronę od nowa? Ręczne poprawki zostaną.');
                if ($r->kind !== 'home' && !isset($virtual[(string) $r->kind]) && !Routes::external($path)) {
                    echo '<details><summary>Zmień adres ' . esc_html($code) . '</summary>' . self::form('page_route', ['id' => (int) $r->id], '', 'zpte2-route')
                        . '<label class="screen-reader-text" for="zpte2-en-' . (int) $r->id . '">Nowy adres ' . esc_html($code) . '</label><input type="text" id="zpte2-en-' . (int) $r->id . '" name="en" value="' . esc_attr($en) . '" pattern="' . esc_attr(Lang::prefix()) . '.*" required>'
                        . '<button class="button">Zapisz</button><small>Stary adres przekieruje na nowy (301).</small></form></details>';
                }
            }
            echo '</td></tr>';
        }
        echo '</tbody></table></div>';
        $pages = (int) ceil($total / self::PER_PAGE);
        if ($pages > 1) {
            echo '<p class="zpte2-pager">';
            for ($i = 1; $i <= $pages; $i++) {
                $args = ['tab' => 'pages', 'f' => $f, 'paged' => $i]; // url() adds the language
                if ($q !== '') { $args['q'] = $q; }
                echo $i === $paged ? '<strong>' . $i . '</strong> ' : '<a href="' . esc_url(self::url($args)) . '">' . $i . '</a> ';
            }
            echo '</p>';
        }
    }

    // ---------------------------------------------------------------- texts

    private static function tab_texts(): void {
        $q = isset($_GET['q']) && is_string($_GET['q']) ? trim(sanitize_text_field(wp_unslash($_GET['q']))) : '';
        $in = ['en' => ['angielskie', 'angielsku'], 'de' => ['niemieckie', 'niemiecku'], 'es' => ['hiszpańskie', 'hiszpańsku']][Lang::code()];
        echo '<div class="zpte2-card"><p>' . esc_html('Pamięć tłumaczeń: każdy polski tekst ma jedno ' . $in[0] . ' tłumaczenie, używane na wszystkich stronach, na których występuje. Poprawka zapisana tutaj zostaje na stałe (tłumacz jej nie nadpisze) i od razu trafia na strony ' . self::code() . '.') . '</p>'
            . '<form method="get" class="zpte2-search"><input type="hidden" name="page" value="' . esc_attr(self::SLUG) . '"><input type="hidden" name="tab" value="texts">'
            . (Lang::is_en() ? '' : '<input type="hidden" name="zpte_lang" value="' . esc_attr(Lang::code()) . '">')
            . '<label class="screen-reader-text" for="zpte2-tq">Szukaj tekstu</label><input type="search" id="zpte2-tq" name="q" value="' . esc_attr($q) . '" placeholder="' . esc_attr('Szukaj po polsku lub po ' . $in[1]) . '"><button class="button">Szukaj</button></form></div>';
        $rows = Store::find_strings($q, 50);
        echo '<h2 class="zpte2-h">' . esc_html($q === '' ? 'Ostatnio przetłumaczone' : 'Wyniki: ' . count($rows) . (count($rows) === 50 ? '+' : '')) . '</h2>';
        if (!$rows) { echo '<div class="zpte2-card"><p>Nic nie znaleziono.</p></div>'; return; }
        $origins = ['ai' => 'AI', 'manual' => 'poprawka', 'zpte' => 'import', 'codex' => 'import', 'v2' => 'import', 'shared' => 'wspólny', 'import' => 'import'];
        echo '<div class="zpte2-texts">';
        foreach ($rows as $r) {
            $label = $origins[(string) $r->origin] ?? (string) $r->origin;
            if ((int) $r->manual) { $label = 'poprawka'; }
            $scope = (int) $r->scope === 0 ? ((int) $r->shared ? 'cała strona (menu, stopka, tytuły)' : 'wszystkie strony z tym tekstem') : 'tylko strona ' . (string) (Store::page((int) $r->scope)->path ?? '#' . (int) $r->scope);
            echo '<div class="zpte2-text">' . self::form('string_save', ['id' => (int) $r->id, 'q' => $q], '', 'zpte2-text-form')
                . '<p class="zpte2-pl" lang="pl">' . esc_html((string) $r->pl) . '</p>'
                . '<label class="screen-reader-text" for="zpte2-s' . (int) $r->id . '">Tłumaczenie</label><textarea id="zpte2-s' . (int) $r->id . '" name="en" rows="' . (strlen((string) $r->en) > 180 ? 4 : 2) . '" lang="' . esc_attr(Lang::code()) . '">' . esc_textarea((string) $r->en) . '</textarea>'
                . '<p class="zpte2-meta"><span class="zpte2-pill">' . esc_html($label) . '</span> ' . esc_html($scope . ' · ' . self::when((string) $r->updated_at)) . ' <button class="button button-small">Zapisz poprawkę</button></p></form></div>';
        }
        echo '</div>';
    }

    // ------------------------------------------------------------- settings

    private static function tab_settings(): void {
        $all = Settings::all();
        $src = Settings::key_source();
        echo self::form('save_settings', [], '', 'zpte2-settings');
        if (!Lang::is_en()) {
            $mode = (string) ($all['public'] ?? 'auto');
            echo '<section class="zpte2-card"><h2>' . esc_html('Wersja ' . self::code() . ' na stronie') . '</h2>'
                . '<p><label><input type="checkbox" name="on" value="1"' . checked((int) $all['on'] === 1, true, false) . '> ' . esc_html('Twórz i aktualizuj wersję ' . self::code() . ' całej strony (adresy /' . Lang::code() . '/…)') . '</label></p>'
                . '<p><label for="zpte2-public">Pokazuj gościom (przełącznik języka, hreflang, mapy witryny, przekierowanie gości z ' . (Lang::code() === 'de' ? 'Niemiec' : 'Hiszpanii') . ')</label><br><select id="zpte2-public" name="public">';
            foreach (['auto' => 'automatycznie, po pierwszym pełnym tłumaczeniu całej strony (zalecane)', 'on' => 'od razu (strony jeszcze nieprzetłumaczone zostają po polsku)', 'off' => 'nie pokazuj (strony ' . self::code() . ' tylko z noindex, do podglądu)'] as $k => $label) {
                echo '<option value="' . esc_attr($k) . '"' . selected($mode, $k, false) . '>' . esc_html($label) . '</option>';
            }
            echo '</select></p><p class="description">' . esc_html('Teraz: ' . self::public_state() . '.') . '</p></section>';
        }
        echo '<section class="zpte2-card" id="zpte2-key"><h2>Klucz API OpenAI</h2>';
        if ($src === 'constant') {
            echo '<p>Klucz pochodzi z pliku wp-config.php (stała ZPTE_OPENAI_API_KEY): ' . esc_html(Settings::key_hint()) . '.</p>';
        } else {
            if ($src === 'option') { echo '<p>Zapisany klucz: <code>' . esc_html(Settings::key_hint()) . '</code>. Zostaw pole puste, żeby go nie zmieniać.</p>'; }
            elseif (Settings::key_unreadable()) { echo '<p class="zpte2-warn">Zapisanego klucza nie da się odczytać (zmieniły się klucze bezpieczeństwa WordPressa). Wklej go ponownie.</p>'; }
            else { echo '<p>Brak klucza: nowe teksty nie będą tłumaczone.</p>'; }
            echo '<p><label for="zpte2-api-key">Nowy klucz</label><br><input type="password" id="zpte2-api-key" name="api_key" value="" autocomplete="new-password" spellcheck="false" class="regular-text" placeholder="sk-…"></p>';
            if ($src === 'option') { echo '<p><label><input type="checkbox" name="delete_key" value="1"> Usuń zapisany klucz</label></p>'; }
            echo '<p class="description">Klucz jest zapisany w bazie w postaci zaszyfrowanej i nigdy nie jest wyświetlany w całości.</p>';
        }
        echo '</section>';

        $models = Lang::is_en() ? self::MODELS : self::AI_MODELS;
        foreach ((array) get_option(self::MODELS_OPTION, []) as $m) {
            if (is_string($m) && !isset($models[$m]) && preg_match('~^(gpt|o\d)~', $m)) { $models[$m] = $m; }
        }
        $current = Settings::model();
        if (!isset($models[$current])) { $models[$current] = $current; }
        echo '<section class="zpte2-card"><h2>Model i jakość</h2><p><label for="zpte2-model">Model</label><br><select id="zpte2-model" name="model">';
        foreach ($models as $m => $label) { echo '<option value="' . esc_attr($m) . '"' . selected($current, $m, false) . '>' . esc_html($label) . '</option>'; }
        echo '</select> <label class="screen-reader-text" for="zpte2-model-other">Inny model</label><input type="text" id="zpte2-model-other" name="model_other" value="" placeholder="inny model (opcjonalnie)" spellcheck="false"></p>';
        echo '<p><label for="zpte2-effort">Rozumowanie</label><br><select id="zpte2-effort" name="effort">';
        foreach (Settings::EFFORTS as $e => $label) { echo '<option value="' . esc_attr($e) . '"' . selected(Settings::effort(), $e, false) . '>' . esc_html($label) . '</option>'; }
        echo '</select></p><p><label for="zpte2-instr">Dodatkowe wskazówki dla tłumacza</label><br><textarea id="zpte2-instr" name="instructions" rows="3" class="large-text" maxlength="2000">' . esc_textarea((string) $all['instructions']) . '</textarea></p>'
            . '<p class="description">' . esc_html(Lang::is_en()
                ? 'Np. nazwy, których nie tłumaczyć. Tłumacz zawsze pisze naturalnym amerykańskim angielskim, oddaje sens, a nie słowa, i zostawia ceny w złotych.'
                : 'Np. nazwy, których nie tłumaczyć. Tłumacz pisze jak ' . (Lang::code() === 'de' ? 'niemiecki copywriter agencji (forma „Sie”, frazy wpisywane w Google w Niemczech)' : 'hiszpański copywriter agencji (forma „tú”, hiszpański z Hiszpanii, frazy wpisywane w Google w Hiszpanii)') . ', z tymi samymi faktami co polska strona. Kwoty zostawia w złotych, a strona pokazuje je gościom w euro.') . '</p></section>';

        echo '<section class="zpte2-card"><h2>Kiedy tłumaczyć</h2>'
            . '<p><label><input type="checkbox" name="enabled" value="1"' . checked(!empty($all['enabled']), true, false) . '> Codziennie o 03:00 sprawdzaj wszystkie strony i tłumacz nowe oraz zmienione teksty</label></p>'
            . '<p><label><input type="checkbox" name="auto" value="1"' . checked(!empty($all['auto']), true, false) . '> Tłumacz wpis od razu po publikacji lub aktualizacji</label></p>'
            . '<p><label><input type="checkbox" name="terms" value="1"' . checked(!empty($all['terms']), true, false) . '> ' . esc_html('Strony kategorii też mają wersje ' . self::code()) . '</label></p>'
            . '<p><label><input type="checkbox" name="recheck" value="1"' . checked(!empty($all['recheck']), true, false) . '> ' . esc_html('Codziennie czytaj ponownie wszystkie strony ' . self::code() . ' (menu, stopka, listy wpisów)') . '</label></p><fieldset><legend>Rodzaje treści</legend>';
        foreach (Settings::public_types() as $type => $label) {
            echo '<label class="zpte2-check"><input type="checkbox" name="types[]" value="' . esc_attr($type) . '"' . checked(in_array($type, Settings::types(), true), true, false) . '> ' . esc_html($label) . '</label>';
        }
        echo '</fieldset></section>';

        echo '<section class="zpte2-card"><h2>Limity</h2>'
            . '<p><label for="zpte2-daily">Polskich znaków dziennie (0 = bez limitu)</label><br><input type="number" id="zpte2-daily" name="daily_chars" min="0" step="10000" value="' . (int) $all['daily_chars'] . '"></p>'
            . '<p><label for="zpte2-monthly">Koszt miesięcznie, USD (0 = bez limitu)</label><br><input type="number" id="zpte2-monthly" name="monthly_usd" min="0" step="1" value="' . esc_attr((string) (float) $all['monthly_usd']) . '"></p>'
            . '<p class="description">' . esc_html('Po przekroczeniu limitu tłumacz czeka (do północy albo do nowego miesiąca) i sam wznawia pracę. Obecne strony ' . self::code() . ' działają dalej.' . (Lang::is_en() ? '' : ' Każdy język ma własne limity; pierwsze tłumaczenie całej strony kosztuje około 20–30 USD.')) . '</p></section>';
        echo '<p><button type="submit" class="button button-primary">Zapisz ustawienia</button></p></form>';

        echo '<section class="zpte2-card"><h2>Połączenie z OpenAI</h2><p>Sprawdza klucz i pobiera listę dostępnych modeli.</p>' . self::button('test', 'Sprawdź połączenie') . '</section>';
        echo '<section class="zpte2-card"><h2>Zadania w tle bez ruchu na stronie</h2><p>Tłumacz uruchamia kolejne kroki sam. Jeśli hosting ma cron, dodaj wywołanie tego adresu co 5 minut — wtedy codzienne sprawdzenie ruszy o czasie nawet bez odwiedzin:</p>'
            . '<p><label class="screen-reader-text" for="zpte2-tick">Adres zadania</label><input type="text" id="zpte2-tick" readonly class="large-text code" value="' . esc_attr(Worker::tick_url()) . '" onfocus="this.select()"></p>'
            . '<p class="description">Adres zawiera tajny klucz tej strony; nie publikuj go.</p></section>';
        echo '<section class="zpte2-card"><h2>Języki PL/EN</h2><p>Ręczne korekty (mają pierwszeństwo przed tłumaczem), adresy i język odwiedzających są w module Języki PL/EN:</p><p class="zpte2-actions">';
        foreach (['overrides' => 'Ręczne korekty', 'routes' => 'Adresy PL / EN', 'geo' => 'Język odwiedzających'] as $t => $label) {
            echo '<a class="button" href="' . esc_url(add_query_arg(['page' => 'zpl-languages', 'tab' => $t], admin_url('admin.php'))) . '">' . esc_html($label) . '</a> ';
        }
        echo '</p></section>';
        // 2.1.0: the dollar card belongs to English; German and Spanish show their own cards (prices in euro).
        if (Lang::is_en()) { do_action('zpte2_settings_cards'); } else { do_action('zpte2_lang_settings_cards', Lang::code()); }
    }

    // --------------------------------------------------------------------- log

    private static function tab_log(): void {
        $log = Log::all();
        if (!$log) { echo '<div class="zpte2-card"><p>Dziennik jest pusty.</p></div>'; return; }
        $labels = ['info' => 'info', 'warning' => 'uwaga', 'error' => 'błąd'];
        echo '<div class="zpte2-card zpte2-table-wrap"><table class="zpte2-table"><thead><tr><th scope="col">Kiedy</th><th scope="col">Rodzaj</th><th scope="col">Co się stało</th></tr></thead><tbody>';
        foreach ($log as $e) {
            $l = (string) ($e['l'] ?? 'info');
            echo '<tr class="is-' . esc_attr($l) . '"><td class="zpte2-when">' . esc_html(self::day_time((int) ($e['t'] ?? 0))) . '</td><td>' . esc_html($labels[$l] ?? $l) . '</td><td class="zpte2-break">' . esc_html((string) ($e['m'] ?? '')) . '</td></tr>';
        }
        echo '</tbody></table></div>';
    }

    // ----------------------------------------------------------------- actions

    private static function back(string $tab, string $msg, bool $error = false, array $extra = []): void {
        $args = array_merge(['tab' => $tab, 'zpte2_msg' => $msg], $extra);
        if ($error) { $args['zpte2_err'] = 1; }
        wp_safe_redirect(self::url($args));
        exit;
    }

    public static function handle(): void {
        if (!current_user_can('manage_options')) { wp_die('Brak uprawnień.', '', ['response' => 403]); }
        check_admin_referer('zpte2');
        Lang::with(self::lang(), [self::class, 'handle_lang']);
    }

    /** 2.1.0: the action of a form, in the language of the tab it came from. */
    public static function handle_lang(): void {
        $do = isset($_POST['do']) && is_string($_POST['do']) ? sanitize_key(wp_unslash($_POST['do'])) : '';
        if (!Bridge::ready()) { self::back('overview', 'Brak modułu Języki PL/EN.', true); }
        Store::install();
        $id = (int) ($_POST['id'] ?? 0);
        switch ($do) {
            case 'run':
                $c = Worker::plan('manual');
                self::back('overview', isset($c['import']) ? 'Najpierw przenoszę obecne tłumaczenia, potem sprawdzę wszystkie strony.' : 'Sprawdzam wszystkie strony: nowe ' . (int) ($c['new'] ?? 0) . ', zmienione ' . (int) ($c['changed'] ?? 0) . '.');
                break;
            case 'resume':
                Worker::resume();
                self::back('overview', 'Wznowiono.');
                break;
            case 'translate_post':
                $pid = (int) ($_POST['post_id'] ?? 0);
                if ($pid > 0 && Sync::plan_post($pid)) {
                    Worker::kick(1);
                    delete_transient(Lang::opt('zpte2_missing'));
                    self::back('overview', 'Tłumaczę: ' . Sync::path_of_post($pid));
                }
                self::back('overview', 'Tej strony nie da się przetłumaczyć (adres techniczny, wyłączony lub niepubliczny).', true);
                break;
            case 'page_check':
            case 'page_force':
                $row = $id ? Store::page($id) : null;
                if (!$row || $row->status === 'off') { self::back('pages', 'Nie znaleziono strony.', true); }
                if ($do === 'page_force') { Sync::force($id); } else { Store::queue($id, 3); }
                Worker::kick(1);
                self::back('pages', ($do === 'page_force' ? 'Tłumaczę od nowa: ' : 'Sprawdzam: ') . $row->path, false, ['f' => 'queue']);
                break;
            case 'page_route':
                self::change_route($id);
                break;
            case 'string_save':
                self::save_string();
                break;
            case 'save_settings':
                self::save_settings();
                break;
            case 'test':
                $r = OpenAI::models();
                if (empty($r['ok'])) { self::back('settings', 'Połączenie nie działa: ' . OpenAI::short((string) ($r['error'] ?? 'nieznany błąd')), true); }
                $list = array_values(array_filter(array_map('strval', (array) ($r['ids'] ?? [])), static function ($m) { return (bool) preg_match('~^[A-Za-z0-9._:-]{2,80}$~', $m); }));
                update_option(self::MODELS_OPTION, array_slice($list, 0, 200), false);
                Log::clear_alert();
                $has = in_array(Settings::model(), $list, true);
                self::back('settings', 'Połączenie działa. Dostępnych modeli: ' . count($list) . '.' . ($list && !$has ? ' Uwaga: wybranego modelu ' . Settings::model() . ' nie ma na liście tego klucza.' : ''), $list && !$has);
                break;
        }
        self::back('overview', 'Nieznana akcja.', true);
    }

    private static function change_route(int $id): void {
        $row = $id ? Store::page($id) : null;
        if (!$row) { self::back('pages', 'Nie znaleziono strony.', true); }
        $en = strtolower(trim(sanitize_text_field(wp_unslash((string) ($_POST['en'] ?? '')))));
        $en = '/' . trim((string) preg_replace('~^https?://[^/]+~', '', $en), '/') . '/';
        $pre = Lang::prefix();
        $code = self::code();
        if (strpos($en, $pre) !== 0) { $en = rtrim($pre, '/') . $en; }
        $example = ['en' => '/en/websites/my-page/', 'de' => '/de/webdesign/meine-seite/', 'es' => '/es/diseno-web/mi-pagina/'][Lang::code()];
        if (!Routes::valid_en($en) || $en === $pre) { self::back('pages', 'Adres ' . $code . ' może mieć tylko małe litery bez polskich znaków, cyfry i myślniki, np. ' . $example . '.', true, ['q' => $row->path]); }
        if (Routes::external((string) $row->path)) { self::back('pages', 'Adres tej strony ustawia moduł Języki PL/EN (Adresy URL) — zmień go tam.', true, ['q' => $row->path]); }
        $owner = \ZPL\Router::reverse(Lang::code())[$en] ?? '';
        if ($owner !== '' && $owner !== (string) $row->path) { self::back('pages', 'Ten adres ma już strona ' . $owner . '.', true, ['q' => $row->path]); }
        $old = (string) (\ZPL\Router::routes(Lang::code())[(string) $row->path] ?? '');
        if (!Routes::assign($id, $en)) { self::back('pages', 'Nie udało się zapisać adresu.', true, ['q' => $row->path]); }
        Index::rebuild();
        Log::add('Ręczna zmiana adresu ' . $code . ': ' . $row->path . ' → ' . $en . ($old !== '' && $old !== $en ? ' (stary ' . $old . ' przekierowuje 301)' : '') . '.');
        self::back('pages', 'Zapisano adres ' . $code . ' ' . $en . '.', false, ['q' => $row->path]);
    }

    private static function save_string(): void {
        $id = (int) ($_POST['id'] ?? 0);
        $q = isset($_POST['q']) && is_string($_POST['q']) ? sanitize_text_field(wp_unslash($_POST['q'])) : '';
        $row = $id ? Store::string_row($id) : null;
        if (!$row) { self::back('texts', 'Nie znaleziono tekstu.', true, ['q' => $q]); }
        $en = trim((string) preg_replace('~\s+~u', ' ', wp_unslash((string) ($_POST['en'] ?? ''))));
        if ($en === '') { self::back('texts', 'Tłumaczenie nie może być puste.', true, ['q' => $q]); }
        // Only the numbered markers of the Polish text (<1>…</1>, <2/>) may be there, never real HTML.
        if (preg_match('~<\s*/?\s*[a-z!?]~i', $en)) { self::back('texts', 'Nie zapisano: tłumaczenie nie może zawierać znaczników HTML.', true, ['q' => $q]); }
        $why = Translator::broken((string) $row->pl, $en);
        if ($why !== '' && strpos($why, 'polsku') === false) { self::back('texts', 'Nie zapisano: ' . $why . '. Zachowaj znaczniki takie jak <1>…</1> i pola {1} z polskiego tekstu.', true, ['q' => $q]); }
        Store::string_edit($id, $en);
        $n = (int) $row->scope > 0 ? (Sync::compile((int) $row->scope) ? 1 : 0) : Sync::recompile_using([(string) $row->hash]);
        if ((int) $row->scope === 0 && (int) $row->shared) { Sync::compile_shared(); }
        Log::add('Ręczna poprawka tłumaczenia: „' . mb_substr((string) $row->pl, 0, 80) . '” (stron: ' . $n . ').');
        self::back('texts', 'Zapisano poprawkę' . ($n ? ' (stron: ' . $n . ')' : '') . '.', false, ['q' => $q]);
    }

    private static function save_settings(): void {
        $p = wp_unslash($_POST);
        $vals = [];
        foreach (['enabled', 'auto', 'terms', 'recheck'] as $k) { $vals[$k] = !empty($p[$k]) ? 1 : 0; }
        if (!Lang::is_en()) {
            $vals['on'] = !empty($p['on']) ? 1 : 0;
            $mode = (string) ($p['public'] ?? 'auto');
            $vals['public'] = in_array($mode, ['auto', 'on', 'off'], true) ? $mode : 'auto';
        }
        $model = trim((string) ($p['model_other'] ?? '')) !== '' ? trim((string) $p['model_other']) : (string) ($p['model'] ?? '');
        if (preg_match('~^[A-Za-z0-9._:-]{2,80}$~', $model)) { $vals['model'] = $model; }
        $effort = (string) ($p['effort'] ?? '');
        if (isset(Settings::EFFORTS[$effort])) { $vals['effort'] = $effort; }
        $types = array_values(array_intersect(array_map('strval', (array) ($p['types'] ?? [])), array_keys(Settings::public_types())));
        $vals['types'] = $types;
        $vals['daily_chars'] = max(0, min(50000000, (int) ($p['daily_chars'] ?? 0)));
        $vals['monthly_usd'] = max(0, min(10000, round((float) ($p['monthly_usd'] ?? 0), 2)));
        $vals['instructions'] = mb_substr(sanitize_textarea_field((string) ($p['instructions'] ?? '')), 0, 2000);
        $before = Settings::all();
        Settings::update($vals);
        $msg = 'Zapisano ustawienia.';
        if (Settings::key_source() !== 'constant') {
            if (!empty($p['delete_key'])) { Settings::delete_key(); $msg .= ' Klucz usunięty.'; }
            $key = trim((string) ($p['api_key'] ?? ''));
            if ($key !== '') {
                if (!Settings::looks_like_key($key)) { self::back('settings', 'To nie wygląda na klucz API OpenAI (sk-…). Nic nie zmieniono w kluczu.', true); }
                Settings::save_key($key);
                $msg .= ' Klucz zapisany.';
                Log::clear_alert();
            }
        }
        // The daily event follows the switch; a model or key change lets paused work continue.
        if ((int) $before['enabled'] !== (int) $vals['enabled']) { wp_clear_scheduled_hook(Worker::DAILY); }
        if ($vals['enabled']) { Worker::maintain(); }
        if (Settings::api_key() !== '' && ($before['model'] !== Settings::model() || strpos($msg, 'Klucz zapisany') !== false || Log::current_alert())) { Worker::resume(); }
        if (!Lang::is_en()) {
            // 2.1.0: the language's addresses, switch and sitemaps follow the switches at once.
            if ((int) ($before['on'] ?? 1) !== (int) $vals['on'] || (string) ($before['public'] ?? 'auto') !== $vals['public']) {
                Routes::flush();
                \ZPL\Lang::reset();
                do_action('zp_html_cache_refresh');
            }
            if ((int) $vals['on'] === 1 && (int) ($before['on'] ?? 1) !== 1) { Worker::plan('manual'); }
        }
        Log::add('Zmieniono ustawienia tłumacza.');
        self::back('settings', $msg);
    }
}
