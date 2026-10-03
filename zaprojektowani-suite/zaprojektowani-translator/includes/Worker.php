<?php
namespace ZPTE;

if (!defined('ABSPATH')) { exit; }

/**
 * WP-Cron: once a day (03:00 site time) the planner queues pages, then a worker translates them in short
 * steps (about 90 s each, the next step a minute later) until the queue is empty or the daily budget is used.
 */
final class Worker {
    const DAILY = 'zpte_daily';
    const WORK = 'zpte_work';
    const LOCK = 'zpte_lock';
    const STEP = 90;          // seconds of work per cron request
    const ROTATION = 12;      // existing English pages re-checked per day

    public static function boot(): void {
        add_action(self::DAILY, [self::class, 'daily']);
        add_action(self::WORK, [self::class, 'work']);
        add_action('init', [self::class, 'maintain'], 20);
        add_action('transition_post_status', [self::class, 'status_changed'], 10, 3);
        add_action('before_delete_post', [self::class, 'deleted']);
    }

    /** Tables, first-run settings and the daily event (self-healing after updates or a cron reset). */
    public static function maintain(): void {
        if (!Bridge::ready()) { return; }
        Store::install();
        Settings::ensure_since();
        $next = wp_next_scheduled(self::DAILY);
        if (!Settings::get('enabled')) {
            if ($next) { wp_clear_scheduled_hook(self::DAILY); }
            return;
        }
        // Time zone changed in Settings → General: keep the check at 03:00 local time.
        if ($next && (int) wp_date('G', $next) !== 3) { wp_clear_scheduled_hook(self::DAILY); $next = false; }
        if (!$next) { wp_schedule_event(self::next_daily(), 'daily', self::DAILY); }
    }

    public static function unschedule(): void {
        wp_clear_scheduled_hook(self::DAILY);
        wp_clear_scheduled_hook(self::WORK);
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
        self::plan('cron');
    }

    // ---------------------------------------------------------------- planner

    /** Queue what needs translating and start the worker. Returns a short summary. */
    public static function plan(string $who = 'cron'): string {
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
        $n = self::plan_posts();
        $m = self::plan_changed();
        $r = self::plan_retry();
        $g = Settings::get('gaps') ? self::plan_existing() : 0;
        update_option('zpte_last_plan', time(), false);
        $q = Store::queued_count();
        $summary = sprintf('Sprawdzenie (%s): nowe %d, zmienione %d, ponowne próby %d, strony EN do uzupełnienia %d — w kolejce %d.', $who === 'cron' ? 'codzienne' : 'ręczne', $n, $m, $r, $g, $q);
        Log::add($summary);
        if ($q > 0) { self::kick(0); }
        return $summary;
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
        foreach ((array) get_posts($args) as $pid) {
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
            if ($row->source_modified === null || (string) $post->post_modified_gmt > (string) $row->source_modified) {
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
     * Existing English pages (routes of the language module): the least recently checked ones every day,
     * all of them after a Suite update (new sections, footer, menu).
     */
    private static function plan_existing(): int {
        $routes = \ZPL\Router::routes();
        foreach ($routes as $pl => $en) {
            $pl = (string) $pl;
            if (Bridge::ineligible($pl) !== '' || Store::path_by_path($pl)) { continue; }
            $pid = url_to_postid(home_url($pl));
            Store::path_add($pl, ['kind' => 'existing', 'status' => 'published', 'post_id' => max(0, (int) $pid)]);
        }
        $suite = defined('ZP_SUITE_VERSION') ? (string) ZP_SUITE_VERSION : '';
        $all = $suite !== '' && get_option('zpte_suite_seen') !== $suite;
        $rows = Store::paths(['kind' => 'existing', 'status' => ['published'], 'queued' => 0], $all ? 0 : self::ROTATION, 0, 'checked_at IS NULL DESC, checked_at ASC, id ASC');
        foreach ($rows as $row) { Store::queue((int) $row->id, $all ? 60 : 50); }
        if ($all) { update_option('zpte_suite_seen', $suite, false); }
        return count($rows);
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

    public static function work(): void {
        if (!Bridge::hooks_ready() || Settings::api_key() === '') { return; }
        if (!self::lock()) { return; }
        if (function_exists('set_time_limit')) { @set_time_limit(self::STEP + 120); }
        $deadline = time() + self::STEP;
        $stop = '';
        try {
            while (time() < $deadline) {
                if (Log::over_limit()) { $stop = 'limit'; break; }
                $row = Store::next_queued();
                if (!$row) { break; }
                $res = Sync::run($row, $deadline);
                if ($res === Sync::FATAL || $res === Sync::RETRY) { $stop = $res; break; }
                if ($res === Sync::PARTIAL) { break; }
            }
        } catch (\Throwable $e) {
            Log::add('Błąd modułu: ' . $e->getMessage() . ' (' . basename($e->getFile()) . ':' . $e->getLine() . ')', 'error');
            $stop = 'error';
        } finally {
            self::unlock();
        }
        if ($stop === 'limit') {
            Log::add('Dzienny limit znaków (' . number_format_i18n((int) Settings::get('daily_chars')) . ') wyczerpany — reszta kolejki jutro.', 'warning');
            return;
        }
        if ($stop === Sync::FATAL || $stop === 'error') { return; }
        if (Store::queued_count() > 0) { self::kick($stop === Sync::RETRY ? 15 * MINUTE_IN_SECONDS : 60); }
    }

    private static function lock(): bool {
        if (add_option(self::LOCK, (string) time(), '', false)) { return true; }
        $t = (int) get_option(self::LOCK);
        if ($t > 0 && $t < time() - 15 * MINUTE_IN_SECONDS) {
            delete_option(self::LOCK);
            return add_option(self::LOCK, (string) time(), '', false);
        }
        return false;
    }

    private static function unlock(): void { delete_option(self::LOCK); }

    public static function running(): bool {
        $t = (int) get_option(self::LOCK);
        return $t > 0 && $t >= time() - 15 * MINUTE_IN_SECONDS;
    }

    // ------------------------------------------------------- post status hooks

    /** A translated post that stops being public loses its English version at once (no 404s left in sitemap-en). */
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
