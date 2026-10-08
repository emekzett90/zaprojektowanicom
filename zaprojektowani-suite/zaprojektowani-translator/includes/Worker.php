<?php
namespace ZPTE;

if (!defined('ABSPATH')) { exit; }

/**
 * WP-Cron: once a day (03:00 site time) the planner queues pages, then a worker translates them in short
 * bounded steps (24 s each, with an admin-browser fallback) until the queue is empty or the daily budget is used.
 */
final class Worker {
    const DAILY = 'zpte_daily';
    const WORK = 'zpte_work';
    const SCAN = 'zpte_scan_retry';
    const LOCK = 'zpte_lock';
    const STEP = 24;          // stay below the common 30-second hosting limit
    const STATE = 'zpte_progress';
    const LOCK_TTL = 180;
    private static $token = '';

    public static function boot(): void {
        add_action(self::DAILY, [self::class, 'daily']);
        add_action(self::WORK, [self::class, 'work']);
        add_action(self::SCAN, [self::class, 'plan'], 10, 1);
        add_action('init', [self::class, 'maintain'], 20);
        add_action('transition_post_status', [self::class, 'status_changed'], 10, 3);
        add_action('before_delete_post', [self::class, 'deleted']);
    }

    /** Tables, first-run settings and the daily event (self-healing after updates or a cron reset). */
    public static function maintain(): void {
        if (!Bridge::ready()) { return; }
        Store::install();
        Settings::migrate();
        Settings::ensure_since();
        $next = wp_next_scheduled(self::DAILY);
        if (!Settings::get('enabled')) {
            if ($next) { wp_clear_scheduled_hook(self::DAILY); }
            return;
        }
        // Preserve overdue events: WordPress must execute them, not postpone them by another day.
        // A single event is recalculated after every scan so DST never turns 03:00 into 02:00/04:00.
        if ($next && $next > time() && (int) wp_date('G', $next) !== 3) {
            wp_clear_scheduled_hook(self::DAILY);
            $candidate = self::next_daily();
            if ($candidate > $next) { $candidate = time() + 5; }
            wp_schedule_single_event($candidate, self::DAILY);
            $next = $candidate;
        }
        if (!$next) { wp_schedule_single_event(self::next_daily(), self::DAILY); }
        // Reconcile the newly included backlog once after this upgrade. Keep the
        // flag while no API key is configured; the ordinary daily scan also remains.
        if (get_option('zpte_coverage_rescan') === '1' && Settings::api_key() !== '') {
            if (wp_next_scheduled(self::SCAN, ['cron']) || wp_schedule_single_event(time() + 60, self::SCAN, ['cron'])) {
                delete_option('zpte_coverage_rescan');
            }
        }
        // A killed request must not strand the persisted queue. The work hook is also a watchdog.
        if (Store::queued_count() && !wp_next_scheduled(self::WORK) && !self::running() && !Log::current_alert()) {
            $state = self::state();
            self::kick(max(5, (int) $state['retry_at'] - time()));
        }
    }

    public static function unschedule(): void {
        wp_clear_scheduled_hook(self::DAILY);
        wp_clear_scheduled_hook(self::WORK);
        wp_unschedule_hook(self::SCAN);
        delete_option(self::LOCK);
    }

    /** Next 03:00 in the site's time zone. */
    public static function next_daily(): int {
        $tz = wp_timezone();
        $at = new \DateTimeImmutable('today 03:00', $tz);
        if ($at->getTimestamp() <= time() + 300) { $at = $at->modify('+1 day'); }
        return $at->getTimestamp();
    }

    public static function daily(): void {
        if (!Settings::get('enabled')) { return; }
        wp_clear_scheduled_hook(self::DAILY);
        wp_schedule_single_event(self::next_daily(), self::DAILY);
        self::plan('cron');
    }

    // ---------------------------------------------------------------- planner

    /** Queue what needs translating and start the worker. Returns a short summary. */
    public static function plan(string $who = 'cron'): string {
        if ($who === 'cron' && !Settings::get('enabled')) { return 'Codzienny harmonogram jest wyłączony.'; }
        if (!Bridge::hooks_ready()) {
            $m = 'Moduł językowy nie ma haków dla Tłumacza EN (potrzebny zaprojektowani-languages 1.0.13+) — nic nie tłumaczę.';
            Log::add($m, 'error');
            return $m;
        }
        if (Settings::api_key() === '') {
            $m = 'Brak klucza API OpenAI — nic nie tłumaczę. Wklej klucz w ZP Suite → Tłumacz EN → Ustawienia.';
            Log::add($m, 'warning');
            return $m;
        }
        Store::install();
        if (!self::lock()) {
            if (!wp_next_scheduled(self::SCAN, [$who])) { wp_schedule_single_event(time() + 60, self::SCAN, [$who]); }
            return 'Trwa przetwarzanie kolejki — postęp aktualizuje się poniżej. Sprawdzenie nowych treści zostało zaplanowane ponownie za minutę.';
        }
        try {
        wp_clear_scheduled_hook(self::SCAN, [$who]);
        if ($who !== 'cron') { Log::clear_alert(); self::update_state(['retry_at' => 0, 'state' => 'queued']); }
        $n = self::plan_posts();
        $m = self::plan_changed();
        $r = self::plan_retry();
        $g = Settings::get('gaps') ? self::plan_existing() : 0;
        update_option('zpte_last_plan', time(), false);
        $q = Store::queued_count();
        $summary = sprintf('Sprawdzenie (%s): nowe %d, zmienione %d, ponowne próby %d, strony EN do uzupełnienia %d — w kolejce %d.', $who === 'cron' ? 'codzienne' : 'ręczne', $n, $m, $r, $g, $q);
        Log::add($summary);
        self::begin($q);
        if ($q > 0) { self::kick(5); }
        else { self::complete(); }
        return $summary;
        } finally { self::unlock(); }
    }

    /** Published posts/pages without an English version: new ones always, older ones when switched on. */
    private static function plan_posts(): int {
        $types = Settings::types();
        if (!$types) { return 0; }
        $since = Settings::since_gmt();
        $older = (bool) Settings::get('older');
        $args = [
            'post_type' => $types,
            'post_status' => 'publish',
            'has_password' => false,
            'posts_per_page' => 500,
            'orderby' => 'date',
            'order' => 'DESC',
            'fields' => 'ids',
            'no_found_rows' => true,
            'suppress_filters' => true,
            'ignore_sticky_posts' => true,
        ];
        if (!$older) { $args['date_query'] = [['column' => 'post_date_gmt', 'after' => $since, 'inclusive' => true]]; }
        $added = 0;
        $page = 1;
        do {
        $args['paged'] = $page++;
        $posts = (array) get_posts($args);
        foreach ($posts as $pid) {
            $pid = (int) $pid;
            if ($row = Store::path_by_post($pid)) {
                // Switched off because the post was hidden or noindexed: back in when it is edited or published again.
                if ($row->status === 'skipped' && $row->kind !== 'existing' && !(int) $row->queued
                    && (string) get_post_field('post_modified_gmt', $pid) > (string) $row->checked_at) {
                    Store::queue((int) $row->id, $row->kind === 'new' ? 10 : 40);
                    $added++;
                }
                continue;
            }
            $path = Bridge::path_of_post($pid);
            if ($path === '' || Bridge::ineligible($path) !== '') { continue; }
            if (Store::path_by_path($path)) { continue; }
            if (Bridge::shipped($path)) {
                // Already has an English version: only its gaps are filled (with the other English pages).
                if (Settings::get('gaps')) { Store::path_add($path, ['post_id' => $pid, 'kind' => 'existing', 'status' => 'published', 'source_modified' => (string) get_post_field('post_modified_gmt', $pid)]); }
                continue;
            }
            $isNew = (string) get_post_field('post_date_gmt', $pid) >= $since;
            if (!$isNew && !$older) { continue; }
            $id = Store::path_add($path, ['post_id' => $pid, 'kind' => $isNew ? 'new' : 'older', 'status' => 'queued']);
            if ($id) { Store::queue($id, $isNew ? 10 : 40); $added++; }
        }
        } while (count($posts) === 500);
        return $added;
    }

    /** Pages whose post was edited since the last check (re-sync only translates the changed fragments). */
    private static function plan_changed(): int {
        $n = 0;
        foreach (Store::paths(['post' => true, 'status' => ['published', 'draft', 'error', 'working', 'queued']]) as $row) {
            if ((int) $row->queued) { continue; }
            $post = get_post((int) $row->post_id);
            if (!$post || $post->post_status !== 'publish' || $post->post_password !== '') {
                if ($row->kind !== 'existing') { Sync::retire($row, 'Wpis nie jest już publiczny — wersję angielską wyłączono.'); }
                continue;
            }
            if ((int) $row->tries >= 4 && $row->source_modified === null && (string) $post->post_modified_gmt <= (string) $row->checked_at) { continue; }
            if ($row->source_modified === null || (string) $post->post_modified_gmt > (string) $row->source_modified) {
                if ((string) $post->post_modified_gmt > (string) $row->checked_at) { Store::path_update((int) $row->id, ['tries' => 0]); }
                Store::queue((int) $row->id, $row->kind === 'existing' ? 30 : 20);
                $n++;
            }
        }
        return $n;
    }

    /** Pages that failed, stopped half-way, or went live with a few untranslated fragments (up to 4 attempts). */
    private static function plan_retry(): int {
        $n = 0;
        foreach (Store::paths(['status' => ['error', 'working', 'published']]) as $row) {
            if ((int) $row->queued) { continue; }
            if ($row->status === 'published' && (int) $row->keys_failed === 0) { continue; }
            if ((int) $row->tries >= 4) { continue; }
            Store::queue((int) $row->id, 30);
            $n++;
        }
        return $n;
    }

    /**
     * Existing English pages: all eligible routes daily, with the least recently checked first.
     */
    private static function plan_existing(): int {
        $routes = \ZPL\Router::routes();
        foreach ($routes as $pl => $en) {
            $pl = (string) $pl;
            if (Bridge::ineligible($pl) !== '' || Store::path_by_path($pl)) { continue; }
            $pid = url_to_postid(home_url($pl));
            Store::path_add($pl, ['kind' => 'existing', 'status' => 'published', 'post_id' => max(0, (int) $pid)]);
        }
        // Every existing EN route is inspected daily. Already translated fragments come from memory.
        $rows = Store::paths(['kind' => 'existing', 'status' => ['published'], 'queued' => 0], 0, 0, 'checked_at IS NULL DESC, checked_at ASC, id ASC');
        $added = 0;
        foreach ($rows as $row) {
            if ((int) $row->tries >= 4) { continue; }
            Store::queue((int) $row->id, 50);
            $added++;
        }
        return $added;
    }

    // ----------------------------------------------------------------- worker

    /** Run the worker now (0) or in $delay seconds. */
    public static function kick(int $delay): void {
        $next = wp_next_scheduled(self::WORK);
        if ($next && $next <= time() + max(0, $delay)) {
            if ($delay === 0) { spawn_cron(); }
            return;
        }
        if ($next) { wp_unschedule_event($next, self::WORK); }
        wp_schedule_single_event(time() + max(0, $delay), self::WORK);
        if ($delay === 0) { spawn_cron(); }
    }

    /** Persisted state contains counts and public paths only, never credentials or API bodies. */
    public static function state(): array {
        $s = get_option(self::STATE, []);
        return array_merge(['state' => 'idle', 'total' => 0, 'done' => 0, 'errors' => 0, 'started_at' => 0,
            'updated_at' => 0, 'finished_at' => 0, 'retry_at' => 0, 'current' => '', 'page_total' => 0,
            'page_done' => 0, 'message' => 'Kolejka nie została jeszcze uruchomiona.'], is_array($s) ? $s : []);
    }

    private static function update_state(array $data): void {
        update_option(self::STATE, array_merge(self::state(), $data, ['updated_at' => time()]), false);
    }

    private static function begin(int $queued): void {
        $s = self::state();
        if (!$s['started_at'] || $s['finished_at']) {
            self::update_state(['state' => 'queued', 'total' => $queued, 'done' => 0, 'errors' => 0,
                'started_at' => time(), 'finished_at' => 0, 'current' => '', 'page_done' => 0, 'page_total' => 0]);
        } else {
            self::update_state(['total' => max((int) $s['total'], (int) $s['done'] + $queued)]);
        }
    }

    public static function page_progress(string $path, int $total, int $done): void {
        self::update_state(['state' => 'running', 'current' => $path, 'page_total' => $total,
            'page_done' => min($total, $done), 'message' => 'Tłumaczę i zapisuję fragmenty strony.']);
    }

    private static function complete(): void {
        $s = self::state();
        $message = (int) $s['errors'] > 0
            ? 'Zakończono kolejkę z brakami. Strony wymagające uwagi: ' . (int) $s['errors'] . '. Szczegóły w zakładce Strony i Dziennik.'
            : 'Zakończono tłumaczenie. Wszystkie strony z tej kolejki zostały sprawdzone i zapisane.';
        if (!(int) $s['total']) { $message = 'Sprawdzenie zakończone — brak stron do przetłumaczenia.'; }
        self::update_state(['state' => (int) $s['errors'] ? 'completed_errors' : 'completed', 'finished_at' => time(),
            'current' => '', 'page_total' => 0, 'page_done' => 0, 'retry_at' => 0, 'message' => $message]);
        Log::add($message, (int) $s['errors'] ? 'warning' : 'info');
        wp_clear_scheduled_hook(self::WORK);
    }

    /** Safe for authenticated polling; status reads never perform API calls. */
    public static function progress(): array {
        $s = self::state();
        $q = Store::queued_count();
        $active = self::running();
        $s['queued'] = $q;
        $s['running'] = $active;
        $s['total'] = max((int) $s['total'], (int) $s['done'] + $q);
        $fraction = $s['current'] && $s['page_total'] ? min(1, (int) $s['page_done'] / (int) $s['page_total']) : 0;
        $s['percent'] = $s['total'] ? min($q ? 99 : 100, (int) floor(100 * ((int) $s['done'] + $fraction) / $s['total'])) : ($s['finished_at'] ? 100 : 0);
        if (!$q && $s['finished_at']) { $s['percent'] = 100; }
        if ($q && !$active && !in_array($s['state'], ['blocked', 'retry', 'limit'], true)) {
            $s['state'] = 'queued';
            $s['message'] = 'Kolejka czeka na kolejny krok. Otwarty panel uruchamia ją automatycznie.';
        }
        if ($q && !$active && (int) $s['updated_at'] < time() - self::LOCK_TTL) {
            $s['message'] .= ' Ostatni krok nie odpowiedział; kolejka zostanie wznowiona z zapisanych fragmentów.';
        }
        $s['can_step'] = $q > 0 && !$active && !Log::current_alert() && (int) $s['retry_at'] <= time()
            && Bridge::hooks_ready() && Settings::api_key() !== '';
        $s['counts'] = [
            'published_new' => Store::count_paths(['kind' => ['new', 'older'], 'status' => 'published']),
            'drafts' => Store::count_paths(['status' => 'draft']),
            'error_pages' => Store::count_paths(['attention' => true]),
            'filled_existing' => Store::count_paths(['kind' => 'existing', 'ai' => true]),
        ];
        $s['usage'] = Log::usage();
        $s['next_daily'] = ($next = wp_next_scheduled(self::DAILY)) ? wp_date('Y-m-d H:i', $next) : '—';
        $s['retry_label'] = $s['retry_at'] ? Admin::day_time((int) $s['retry_at']) : '';
        if (!Bridge::hooks_ready() || Settings::api_key() === '') {
            $s['state'] = 'blocked'; $s['message'] = 'Uzupełnij klucz API i sprawdź moduł językowy w ustawieniach.';
        } elseif ($alert = Log::current_alert()) {
            $s['state'] = 'blocked'; $s['message'] = (string) $alert['m'];
        }
        return $s;
    }

    /** Explicit settings change or administrator retry releases an API/limit pause. */
    public static function resume(): void {
        self::update_state(['retry_at' => 0, 'state' => 'queued']);
        if (Store::queued_count()) { self::kick(5); }
    }

    public static function work(): void {
        if (!Bridge::hooks_ready() || Settings::api_key() === '' || Log::current_alert()) { return; }
        $state = self::state();
        if ((int) $state['retry_at'] > time()) { self::kick((int) $state['retry_at'] - time()); return; }
        if (!self::lock()) { return; }
        try {
        if (function_exists('set_time_limit')) { @set_time_limit(self::STEP + 4); }
        $deadline = time() + self::STEP;
        OpenAI::deadline($deadline);
        $stop = '';
        $row = null;
        // Replace a near-due event: it might otherwise fire while this lock is held and disappear.
        wp_clear_scheduled_hook(self::WORK);
        self::kick(self::LOCK_TTL + 10);
        self::begin(Store::queued_count());
        self::update_state(['state' => 'running', 'retry_at' => 0, 'message' => 'Przetwarzam kolejkę.']);
        try {
            while (time() < $deadline - 4) {
                if (Log::over_limit()) { $stop = 'limit'; break; }
                $row = Store::next_queued();
                if (!$row) { break; }
                self::page_progress((string) $row->path, (int) $row->keys_total, (int) $row->keys_ai);
                $res = Sync::run($row, $deadline);
                if (in_array($res, [Sync::FATAL, Sync::RETRY, Sync::LIMIT], true)) { $stop = $res; break; }
                if ($res === Sync::PARTIAL) { break; }
                $fresh = Store::path((int) $row->id);
                $s = self::state();
                self::update_state(['done' => (int) $s['done'] + 1,
                    'errors' => (int) $s['errors'] + ($fresh && ($fresh->status === 'error' || (int) $fresh->tries > 0 || (int) $fresh->keys_failed > 0) ? 1 : 0),
                    'current' => '', 'page_done' => 0, 'page_total' => 0]);
            }
        } catch (\Throwable $e) {
            $message = 'Błąd modułu: ' . $e->getMessage() . ' (' . basename($e->getFile()) . ':' . $e->getLine() . ')';
            Log::alert($message);
            self::update_state(['message' => $message]);
            $stop = 'error';
        } finally {
            OpenAI::deadline(0);
        }
        if ($stop === 'limit') {
            $tz = wp_timezone();
            $tomorrow = (new \DateTimeImmutable('tomorrow 00:05', $tz))->getTimestamp();
            self::update_state(['state' => 'limit', 'retry_at' => $tomorrow, 'message' => 'Osiągnięto dzienny limit znaków. Dokończę automatycznie po jego odnowieniu.']);
            wp_clear_scheduled_hook(self::WORK); self::kick($tomorrow - time());
            return;
        }
        if ($stop === Sync::FATAL || $stop === 'error') {
            self::update_state(['state' => 'blocked', 'message' => (string) (Log::current_alert()['m'] ?? self::state()['message'])]);
            wp_clear_scheduled_hook(self::WORK);
            return;
        }
        if (Store::queued_count() > 0) {
            $delay = $stop === Sync::RETRY ? 5 * MINUTE_IN_SECONDS : 5;
            self::update_state(['state' => $stop === Sync::RETRY ? 'retry' : 'queued', 'retry_at' => time() + $delay,
                'message' => $stop === Sync::RETRY ? 'Chwilowy błąd połączenia lub API. Ponowię automatycznie za 5 minut; zapisany postęp pozostaje.' : 'Zapisano krok. Kontynuuję pozostałe fragmenty.']);
            wp_clear_scheduled_hook(self::WORK); self::kick($delay);
        } else { self::complete(); }
        } finally { self::unlock(); }
    }

    private static function lock(): bool {
        $token = time() . ':' . wp_generate_uuid4();
        if (add_option(self::LOCK, $token, '', false)) { self::$token = $token; return true; }
        $old = (string) get_option(self::LOCK);
        if ((int) $old > 0 && (int) $old < time() - self::LOCK_TTL) {
            // Compare-and-delete: never remove another request's newly acquired lock.
            global $wpdb;
            $deleted = $wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->options} WHERE option_name = %s AND option_value = %s", self::LOCK, $old));
            if ($deleted) { wp_cache_delete(self::LOCK, 'options'); }
            if ($deleted && add_option(self::LOCK, $token, '', false)) { self::$token = $token; return true; }
        }
        return false;
    }

    private static function unlock(): void {
        if (self::$token === '') { return; }
        global $wpdb;
        $wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->options} WHERE option_name = %s AND option_value = %s", self::LOCK, self::$token));
        wp_cache_delete(self::LOCK, 'options');
        self::$token = '';
    }

    public static function running(): bool {
        $t = (int) get_option(self::LOCK);
        return $t > 0 && $t >= time() - self::LOCK_TTL;
    }

    // ------------------------------------------------------- post status hooks

    /** A translated post that stops being public loses its English version at once (no 404s left in the English sitemap). */
    public static function status_changed($new, $old, $post): void {
        if ($old === $new || ($old !== 'publish' && $new !== 'publish') || !($post instanceof \WP_Post) || !Bridge::ready() || get_option('zpte_db') !== Store::DB_VERSION) { return; }
        $row = Store::path_by_post((int) $post->ID);
        if (!$row || $row->kind === 'existing') { return; }
        if ($old === 'publish') {
            Sync::retire($row, 'Wpis nie jest już publiczny — wersję angielską wyłączono.');
        } elseif ($row->status === 'skipped' && Settings::get('enabled') && Settings::api_key() !== '') {
            // Published again: its English version comes back with the next worker run.
            Store::queue((int) $row->id, $row->kind === 'new' ? 10 : 40);
            self::kick(60);
        }
    }

    public static function deleted($post_id): void {
        if (!Bridge::ready() || get_option('zpte_db') !== Store::DB_VERSION) { return; }
        $row = Store::path_by_post((int) $post_id);
        if (!$row) { return; }
        if ($row->kind !== 'existing') { Sync::retire($row, 'Wpis usunięty.'); }
        Store::path_delete((int) $row->id);
    }
}
