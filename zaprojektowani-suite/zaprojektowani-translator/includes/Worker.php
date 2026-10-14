<?php
namespace ZPTE;

if (!defined('ABSPATH')) { exit; }

/**
 * Runs the translator in short steps (≤ 22 s, below common hosting limits), one at a time (lock), until the
 * queue is empty:
 *  - first the copy of the earlier translations (Import), with no API calls;
 *  - then pages: read → OpenAI jobs in the background (up to 6 at once) → answers → English page.
 * Steps chain themselves through a request to this site's own REST address, with WP-Cron as a fallback, so
 * the work continues at night without visitors. Open panel = another fallback. Every 03:00 (site time) the
 * daily check queues new, edited and all other pages; publishing or updating a post queues it at once.
 */
final class Worker {
    const DAILY = 'zpte2_daily';
    const WORK = 'zpte2_work';
    const LOCK = 'zpte2_lock';
    const STATE = 'zpte2_state';
    const TICK = 'zpte2_tick_key';
    const BUILD = 'zpte2_build';
    const STEP = 22;
    const LOCK_TTL = 90;
    const IN_FLIGHT = 6;
    const SENT_MAX_AGE = 1800;  // no answer after 30 minutes: ask again
    const UNKNOWN_WAIT = 600;   // sending ended without an answer: maybe accepted, so wait before sending again

    private static $token = '';

    public static function boot(): void {
        add_action(self::DAILY, [self::class, 'daily']);
        add_action(self::WORK, [self::class, 'cron']);
        add_action('init', [self::class, 'maintain'], 20);
        add_action('rest_api_init', [self::class, 'rest']);
        add_action('wp_after_insert_post', [self::class, 'saved'], 20, 4);
        add_action('transition_post_status', [self::class, 'status_changed'], 10, 3);
        add_action('before_delete_post', [self::class, 'deleted']);
        add_action('created_category', [self::class, 'term_changed']);
        add_action('edited_category', [self::class, 'term_changed']);
    }

    // ------------------------------------------------------------------ state

    public static function state(): array {
        $s = get_option(self::STATE, []);
        $s = is_array($s) ? $s : [];
        $run = array_merge(['started' => 0, 'total' => 0, 'done' => 0, 'errors' => 0, 'who' => '', 'finished' => 0], is_array($s['run'] ?? null) ? $s['run'] : []);
        $s = array_merge(['work' => 0, 'phase' => 'idle', 'message' => '', 'step_at' => 0, 'daily_at' => 0, 'retry_at' => 0, 'wait_until' => 0, 'current' => ''], $s);
        $s['run'] = $run;
        // Names the older status screen reads.
        $s['state'] = $s['phase'];
        $s['total'] = $run['total'];
        $s['done'] = $run['done'];
        $s['errors'] = $run['errors'];
        $s['updated_at'] = $s['step_at'];
        $s['finished_at'] = $run['finished'];
        return $s;
    }

    private static function update(array $data): void {
        $s = get_option(self::STATE, []);
        $s = is_array($s) ? $s : [];
        if (isset($data['run']) && is_array($data['run'])) { $data['run'] = array_merge(is_array($s['run'] ?? null) ? $s['run'] : [], $data['run']); }
        update_option(self::STATE, array_merge($s, $data), true);
    }

    /** One page of the current run finished (translated, unchanged, skipped). */
    public static function page_finished(bool $error = false): void {
        $s = self::state();
        self::update(['run' => ['done' => (int) $s['run']['done'] + 1, 'errors' => (int) $s['run']['errors'] + ($error ? 1 : 0)], 'current' => '']);
    }

    public static function running(): bool {
        $t = (int) get_option(self::LOCK);
        return $t > 0 && $t >= time() - self::LOCK_TTL;
    }

    /** Waiting for the owner (key, credit, model) or for a limit to renew. */
    public static function paused(): string {
        if (Settings::api_key() === '') { return 'Brak klucza API OpenAI. Wklej go w Ustawieniach.'; }
        if ($a = Log::current_alert()) { return (string) $a['m']; }
        $s = self::state();
        if ((int) $s['retry_at'] > time()) { return (string) $s['message']; }
        return '';
    }

    // ------------------------------------------------------------ scheduling

    /** Tables, the daily event and a watchdog. Front-end requests only check one autoloaded option. */
    public static function maintain(): void {
        if (!Bridge::ready()) { return; }
        $admin = is_admin() || wp_doing_cron();
        if ($admin) {
            Store::install();
            self::schedule_daily(false);
            self::after_update();
        }
        $s = self::state();
        if (!empty($s['work']) && !self::running() && !wp_next_scheduled(self::WORK)) {
            wp_schedule_single_event(time() + 5, self::WORK);
        }
        if ($admin && !Import::done() && !wp_next_scheduled(self::WORK) && !self::running()) {
            self::update(['work' => 1]);
            wp_schedule_single_event(time() + 2, self::WORK);
        }
    }

    /**
     * A new version of ZP Suite can change texts in templates (headings, buttons, footer): every English page is
     * read again right away instead of at the next 03:00, and only its new texts go to OpenAI.
     */
    private static function after_update(): void {
        $build = (defined('ZP_SUITE_VERSION') ? ZP_SUITE_VERSION : '') . '|' . ZPTE_VERSION . '|' . (defined('ZPL_VERSION') ? ZPL_VERSION : '');
        $was = (string) get_option(self::BUILD, '');
        if ($was === $build) { return; }
        update_option(self::BUILD, $build, false);
        // The first start copies the earlier translations and checks everything afterwards anyway.
        if ($was === '' || !Import::done() || !Settings::get('enabled') || !Settings::get('recheck')) { return; }
        self::plan('update');
    }

    /** Next 03:00 in the site's time zone (Europe/Warsaw on the live site). */
    public static function next_daily(): int {
        $at = new \DateTimeImmutable('today 03:00', wp_timezone());
        if ($at->getTimestamp() <= time() + 60) { $at = $at->modify('+1 day'); }
        return $at->getTimestamp();
    }

    private static function schedule_daily(bool $reset): void {
        $next = wp_next_scheduled(self::DAILY);
        if (!Settings::get('enabled')) {
            if ($next) { wp_clear_scheduled_hook(self::DAILY); }
            return;
        }
        // A single event recalculated every day keeps 03:00 right across summer and winter time.
        if ($next && ($reset || ($next > time() && (int) wp_date('G', $next) !== 3))) {
            wp_clear_scheduled_hook(self::DAILY);
            $next = 0;
        }
        if (!$next) { wp_schedule_single_event(self::next_daily(), self::DAILY); }
    }

    /** Was today's 03:00 check missed (no traffic at night, WP-Cron off)? */
    private static function daily_due(): bool {
        if (!Settings::get('enabled') || !Import::done()) { return false; }
        $today = (new \DateTimeImmutable('today 03:00', wp_timezone()))->getTimestamp();
        return time() >= $today && (int) self::state()['daily_at'] < $today;
    }

    public static function daily(): void {
        self::schedule_daily(true);
        if (!Settings::get('enabled')) { return; }
        self::plan('cron');
    }

    /** Queue everything that needs a look; returns the counts for the log and the panel. */
    public static function plan(string $who): array {
        if (!Bridge::ready()) { return []; }
        Store::install();
        if (!Import::done()) {
            self::update(['work' => 1]);
            self::chain(1);
            return ['import' => 1];
        }
        $c = Sync::plan_all();
        $q = Store::queued_count();
        $s = self::state();
        $fresh = !(int) $s['run']['started'] || (int) $s['run']['finished'] || !$s['work'];
        self::update([
            'work' => 1, 'phase' => 'translating', 'wait_until' => 0, 'daily_at' => in_array($who, ['cron', 'tick', 'import'], true) ? time() : (int) $s['daily_at'],
            'run' => $fresh ? ['started' => time(), 'total' => $q, 'done' => 0, 'errors' => 0, 'who' => $who, 'finished' => 0] : ['total' => max((int) $s['run']['total'], (int) $s['run']['done'] + $q)],
            'message' => 'Sprawdzam strony i tłumaczę nowe teksty.',
        ]);
        $label = ['cron' => 'codzienne', 'tick' => 'codzienne', 'import' => 'pierwsze po przeniesieniu', 'manual' => 'ręczne', 'update' => 'po aktualizacji wtyczki'][$who] ?? $who;
        Log::add(sprintf('Sprawdzenie (%s): nowe %d, zmienione %d, pozostałe %d — w kolejce %d.', $label, $c['new'] ?? 0, $c['changed'] ?? 0, $c['recheck'] ?? 0, $q));
        self::chain(1);
        return $c;
    }

    /** Run the next step soon: a request to this site's REST address now, WP-Cron as the fallback. */
    public static function chain(int $delay): void {
        $at = time() + max(1, $delay) + 25;
        $next = wp_next_scheduled(self::WORK);
        if ($next && $next > $at) { wp_unschedule_event($next, self::WORK); $next = false; }
        if (!$next) { wp_schedule_single_event($at, self::WORK); }
        if ($delay <= 3) { self::tick_async(); }
    }

    private static function tick_async(): void {
        wp_remote_post(rest_url('zpte/v1/tick'), [
            'timeout' => 0.01,
            'blocking' => false,
            'sslverify' => (bool) apply_filters('https_local_ssl_verify', false),
            'body' => ['key' => self::tick_key()],
            'headers' => ['Cache-Control' => 'no-cache'],
        ]);
    }

    public static function tick_key(): string {
        $k = (string) get_option(self::TICK, '');
        if (strlen($k) < 24) {
            $k = wp_generate_password(32, false, false);
            update_option(self::TICK, $k, false);
        }
        return $k;
    }

    /** Address for an outside scheduler (hosting cron or an uptime monitor): runs a step, starts a missed daily check. */
    public static function tick_url(): string {
        return add_query_arg('key', self::tick_key(), rest_url('zpte/v1/tick'));
    }

    public static function rest(): void {
        register_rest_route('zpte/v1', '/tick', [
            'methods' => ['GET', 'POST'],
            'permission_callback' => '__return_true',
            'callback' => [self::class, 'tick'],
        ]);
    }

    /** @param \WP_REST_Request $r */
    public static function tick($r) {
        $key = $r->get_param('key');
        if (!is_string($key) || !hash_equals(self::tick_key(), $key)) {
            return new \WP_Error('zpte_key', 'Nieprawidłowy klucz.', ['status' => 403]);
        }
        do_action('litespeed_control_set_nocache', 'zpte tick');
        ignore_user_abort(true);
        if (self::daily_due()) { self::plan('tick'); }
        $s = self::state();
        $out = ['ok' => true, 'busy' => false, 'work' => !empty($s['work']) || !Import::done()];
        if ($out['work']) { $out = array_merge($out, self::work('tick')); }
        $res = new \WP_REST_Response($out);
        $res->header('Cache-Control', 'no-store, max-age=0');
        $res->header('X-LiteSpeed-Cache-Control', 'no-cache');
        $res->header('X-Robots-Tag', 'noindex, nofollow');
        return $res;
    }

    public static function cron(): void {
        if (self::daily_due()) { self::plan('tick'); }
        self::work('cron');
    }

    // ------------------------------------------------------------------- step

    /** One bounded step. @return array{busy:bool,left:int} */
    public static function work(string $who = 'cron'): array {
        if (!Bridge::ready()) { return ['busy' => false, 'left' => 0]; }
        if (!self::lock()) { return ['busy' => true, 'left' => -1]; }
        $left = 0;
        $next = null; // the next step is asked for after the lock is free, so that it is not turned away as busy
        try {
            if (function_exists('set_time_limit')) { @set_time_limit(self::STEP + 20); }
            Store::install();
            Retire::maybe();
            $deadline = time() + self::STEP;

            // 1. The earlier translations first: no API, a few seconds.
            if (!Import::done()) {
                self::update(['work' => 1, 'phase' => 'import', 'message' => 'Przenoszę obecne tłumaczenia EN i adresy.']);
                if (Import::step($deadline - 3)) { $next = 1; return ['busy' => false, 'left' => 1]; }
                self::plan('import');
            }

            // 2. Pages and OpenAI answers.
            $pause = self::paused();
            self::update(['phase' => $pause !== '' ? 'paused' : 'translating']);
            while (time() < $deadline - 2) {
                $did = self::poll($deadline);
                if ($pause === '') {
                    $did += self::submit($deadline);
                    $pause = self::paused();
                }
                $did += self::advance_pages();
                if (Store::count_jobs(['new']) < self::IN_FLIGHT * 3) { $did += self::read_next($deadline); }
                if ($did > 0) { continue; }
                if (Store::count_jobs(['sent']) > 0 && time() < $deadline - 5) { sleep(2); continue; }
                break;
            }
            $left = self::left();
            if ($left === 0) {
                self::complete();
            } elseif ($pause !== '') {
                // Nothing moves until the owner fixes the key or the limit renews; check again later.
                $s = self::state();
                $retry = (int) $s['retry_at'] > time() ? (int) $s['retry_at'] - time() : 15 * MINUTE_IN_SECONDS;
                self::update(['phase' => 'paused', 'message' => $pause]);
                $next = $retry;
            } else {
                // Only pages or requests that wait for their next try: no step until then (no requests in a loop).
                $wait = self::next_due();
                self::update(['wait_until' => $wait > 3 ? time() + $wait : 0, 'message' => $wait > 3
                    ? 'Czekam na ponowną próbę (' . $left . ' w toku) — następna o ' . wp_date('H:i', time() + $wait) . '.'
                    : 'Tłumaczę: ' . $left . ' w toku.']);
                $next = $wait;
            }
        } catch (\Throwable $e) {
            $m = 'Błąd modułu: ' . $e->getMessage() . ' (' . basename($e->getFile()) . ':' . $e->getLine() . '). Ponowię za 5 minut.';
            Log::add($m, 'error');
            self::update(['phase' => 'error', 'message' => OpenAI::short($m), 'retry_at' => time() + 300]);
            $next = 300;
            $left = 1;
        } finally {
            self::update(['step_at' => time()]);
            self::unlock();
            if ($next !== null) { self::chain($next); }
        }
        return ['busy' => false, 'left' => $left];
    }

    /** Seconds until something in the queue can move: a page to read, a request to send, an answer to collect. */
    private static function next_due(): int {
        if (Store::next_queued()) { return 1; }
        if (Store::count_jobs(['sent']) > 0) { return 2; }
        $at = Store::next_retry_at();
        foreach (Store::jobs(['new', 'unknown'], 100) as $job) {
            $t = (int) strtotime((string) $job->updated_at . ' UTC');
            if ($job->state === 'unknown') { $t += self::UNKNOWN_WAIT; }
            elseif ((string) $job->error !== '') { $t += 45 * max(1, (int) $job->tries); }
            else { return 1; }
            $at = $at > 0 ? min($at, $t) : $t;
        }
        return $at > 0 ? max(1, $at - time()) : 60;
    }

    /** Pages in the queue or waiting for answers, plus jobs not finished. */
    public static function left(): int {
        if (!Store::ready()) { return 0; }
        return Store::queued_count() + Store::count_jobs(['new', 'sent', 'unknown', 'wait']) + (Import::done() ? 0 : 1);
    }

    private static function complete(): void {
        $s = self::state();
        $run = $s['run'];
        $wasRunning = !empty($s['work']) && !(int) $run['finished'];
        self::update(['work' => 0, 'phase' => 'idle', 'current' => '', 'retry_at' => 0, 'wait_until' => 0,
            'run' => ['finished' => time(), 'total' => max((int) $run['total'], (int) $run['done'])],
            'message' => 'Wszystko sprawdzone. Następne sprawdzenie: ' . wp_date('j.m H:i', (int) (wp_next_scheduled(self::DAILY) ?: self::next_daily())) . '.']);
        if ($wasRunning) {
            $errors = (int) $run['errors'];
            Log::add('Koniec przebiegu: sprawdzono ' . (int) $run['done'] . ' stron' . ($errors ? ', ' . $errors . ' z problemem (zakładka Strony)' : '') . '. Wersji EN: ' . Index::count() . '.', $errors ? 'warning' : 'info');
        }
        Store::prune_jobs();
    }

    // ------------------------------------------------------------------- jobs

    /** Collect finished background answers. */
    private static function poll(int $deadline): int {
        $n = 0;
        foreach (Store::jobs(['sent'], 40) as $job) {
            if (time() >= $deadline - 3) { break; }
            $touched = strtotime((string) $job->updated_at . ' UTC');
            if ($touched && $touched > time() - 2) { continue; }
            $age = time() - (int) strtotime((string) $job->sent_at . ' UTC');
            $r = OpenAI::fetch((string) $job->response_id);
            if (!empty($r['lost'])) {
                Store::job_update((int) $job->id, ['state' => 'new', 'response_id' => '', 'error' => (string) $r['error']]);
                $n++;
                continue;
            }
            if (!$r['ok']) {
                if ($r['fatal']) { Log::alert($r['error']); break; }
                Store::job_update((int) $job->id, ['error' => OpenAI::short((string) $r['error'])]);
                if ($age > self::SENT_MAX_AGE) { self::resend($job, 'Brak odpowiedzi OpenAI przez 30 minut — wysyłam ponownie.'); $n++; }
                continue;
            }
            if (empty($r['done'])) {
                if ($age > self::SENT_MAX_AGE) { OpenAI::cancel((string) $job->response_id); self::resend($job, 'Odpowiedź OpenAI trwała ponad 30 minut — wysyłam ponownie.'); $n++; }
                else { Store::job_update((int) $job->id, []); }
                continue;
            }
            self::finish_job($job, (array) $r['answer']);
            $n++;
        }
        return $n;
    }

    /** @param object $job */
    private static function resend($job, string $why): void {
        $tries = (int) $job->tries + 1;
        if ($tries >= 3) { self::job_failed($job, $why); return; }
        Store::job_update((int) $job->id, ['state' => 'new', 'response_id' => '', 'tries' => $tries, 'error' => OpenAI::short($why)]);
    }

    /** @param object $job */
    private static function finish_job($job, array $answer): void {
        Log::count((int) $job->chars, (int) ($answer['in'] ?? 0), (int) ($answer['out'] ?? 0), (string) ($job->model ?: Settings::model()));
        $data = $answer['data'] ?? null;
        if (is_array($data) && (string) ($answer['error'] ?? '') === '') {
            if ($job->kind === 'slug') {
                if (!Sync::apply_slug($job, $data)) { self::resend($job, 'Model nie podał angielskiego adresu.'); return; }
            } else {
                Sync::apply_units($job, $data);
            }
            Store::job_update((int) $job->id, ['state' => 'done', 'error' => '']);
            if (Log::current_alert()) { Log::clear_alert(); }
            return;
        }
        if (!empty($answer['truncated']) && $job->kind === 'units') {
            $payload = json_decode((string) $job->items, true);
            $items = is_array($payload['items'] ?? null) ? $payload['items'] : [];
            if (count($items) > 1) {
                // Too long for one answer: the fragments are sent again in two halves.
                $half = (int) ceil(count($items) / 2);
                foreach ([array_slice($items, 0, $half, true), array_slice($items, $half, null, true)] as $part) {
                    $chars = 0;
                    foreach (array_keys($part) as $k) { $chars += Translator::len((string) $k); }
                    $sh = array_values(array_intersect(array_keys($part), (array) ($payload['shared'] ?? [])));
                    Store::job_add((int) $job->page_id, 'units', ['items' => $part, 'shared' => $sh, 'ctx' => $payload['ctx'] ?? []], $chars);
                }
                Store::job_update((int) $job->id, ['state' => 'dropped', 'error' => 'Podzielono na mniejsze zapytania.']);
                return;
            }
        }
        $why = (string) ($answer['error'] ?? '') ?: 'Nieczytelna odpowiedź OpenAI.';
        if ($job->kind === 'slug' && (int) $job->tries >= 1) {
            // Second failure of an address request: an address from the English title.
            Store::job_update((int) $job->id, ['tries' => (int) $job->tries + 1]);
            if (Sync::apply_slug((object) array_merge((array) $job, ['tries' => 2]), [])) { Store::job_update((int) $job->id, ['state' => 'done', 'error' => OpenAI::short($why)]); return; }
        }
        self::resend($job, $why);
    }

    /** @param object $job */
    private static function job_failed($job, string $why): void {
        Store::job_update((int) $job->id, ['state' => 'failed', 'error' => OpenAI::short($why)]);
        $row = Store::page((int) $job->page_id);
        if (!$row) { return; }
        if ($job->kind === 'units') {
            $payload = json_decode((string) $job->items, true);
            $n = is_array($payload['items'] ?? null) ? count($payload['items']) : 0;
            Store::page_update((int) $row->id, ['keys_failed' => (int) $row->keys_failed + $n, 'message' => 'Nie przetłumaczono ' . $n . ' fragmentów: ' . OpenAI::short($why)]);
        }
        Log::add($row->path . ': ' . $why, 'warning');
    }

    /** Send new requests while fewer than IN_FLIGHT wait for answers. */
    private static function submit(int $deadline): int {
        $limit = Log::limit_reached();
        if ($limit !== '') { self::limit_pause($limit); return 0; }
        foreach (Store::jobs(['unknown'], 30) as $j) {
            if ((int) strtotime((string) $j->updated_at . ' UTC') < time() - self::UNKNOWN_WAIT) { Store::job_update((int) $j->id, ['state' => 'new', 'response_id' => '']); }
        }
        $inflight = Store::count_jobs(['sent']);
        $n = 0;
        foreach (Store::jobs(['new'], self::IN_FLIGHT * 2) as $job) {
            if ($inflight >= self::IN_FLIGHT || time() >= $deadline - 4) { break; }
            if ((string) $job->error !== '' && (int) strtotime((string) $job->updated_at . ' UTC') > time() - 45 * max(1, (int) $job->tries)) { continue; }
            if ((int) $job->chars > 0 && ($l = Log::limit_reached((int) $job->chars)) !== '') { self::limit_pause($l); break; }
            $body = self::body($job);
            if ($body === null) { Store::job_update((int) $job->id, ['state' => 'dropped', 'error' => 'Puste zapytanie.']); continue; }
            // Marked before sending: if this request dies half-way, the job is not sent twice at once.
            Store::job_update((int) $job->id, ['state' => 'unknown', 'model' => Settings::model(), 'effort' => Settings::effort(), 'sent_at' => Store::now()]);
            $r = OpenAI::submit($body);
            if ($r['ok']) {
                Store::job_update((int) $job->id, ['state' => 'sent', 'response_id' => (string) $r['id'], 'error' => '']);
                $inflight++;
                $n++;
                if (isset($r['answer'])) {
                    $fresh = Store::jobs(['sent'], 1, (int) $job->page_id);
                    foreach ($fresh as $f) { if ((int) $f->id === (int) $job->id) { self::finish_job($f, (array) $r['answer']); } }
                }
                continue;
            }
            if ($r['fatal']) {
                Store::job_update((int) $job->id, ['state' => 'new', 'error' => OpenAI::short((string) $r['error'])]);
                Log::alert((string) $r['error']);
                self::update(['phase' => 'paused', 'message' => OpenAI::short((string) $r['error'])]);
                break;
            }
            if ($r['retry']) {
                // A connection error may come after OpenAI accepted the request: such a job waits before it is sent again.
                Store::job_update((int) $job->id, ['state' => (int) $r['http'] === 0 ? 'unknown' : 'new', 'error' => OpenAI::short((string) $r['error'])]);
                self::update(['retry_at' => time() + 60, 'message' => OpenAI::short((string) $r['error'])]);
                Log::add((string) $r['error'], 'warning');
                break;
            }
            $tries = (int) $job->tries + 1;
            if ($tries >= 3) { self::job_failed($job, (string) $r['error']); continue; }
            Store::job_update((int) $job->id, ['state' => 'new', 'tries' => $tries, 'error' => OpenAI::short((string) $r['error'])]);
        }
        return $n;
    }

    /** @param object $job */
    private static function body($job): ?array {
        $payload = json_decode((string) $job->items, true);
        if (!is_array($payload)) { return null; }
        if ($job->kind === 'slug') {
            return Translator::slug_request((array) ($payload['page'] ?? []), (array) ($payload['segments'] ?? []), (int) $job->id);
        }
        $items = is_array($payload['items'] ?? null) ? $payload['items'] : [];
        if (!$items) { return null; }
        [$body] = Translator::units_request($items, (array) ($payload['ctx'] ?? []), (int) $job->id);
        return $body;
    }

    private static function limit_pause(string $which): void {
        if ($which === 'day') {
            $at = (new \DateTimeImmutable('tomorrow 00:05', wp_timezone()))->getTimestamp();
            $m = 'Dzienny limit znaków wyczerpany. Dokończę po północy.';
        } else {
            $at = (new \DateTimeImmutable('first day of next month 00:05', wp_timezone()))->getTimestamp();
            $m = 'Miesięczny limit kosztów OpenAI wyczerpany. Podnieś go w Ustawieniach albo dokończę w nowym miesiącu.';
        }
        $s = self::state();
        if ((int) $s['retry_at'] < $at - 60) { Log::add($m, 'warning'); }
        self::update(['retry_at' => $at, 'phase' => 'paused', 'message' => $m]);
    }

    private static function advance_pages(): int {
        $n = Sync::resolve_waits();
        foreach (['translating', 'route'] as $stage) {
            foreach (Store::ready_in_stage($stage, 20) as $row) {
                Sync::advance($row);
                $n++;
            }
        }
        return $n;
    }

    private static function read_next(int $deadline): int {
        if (time() >= $deadline - 8) { return 0; }
        $row = Store::next_queued();
        if (!$row) { return 0; }
        self::update(['current' => (string) $row->path]);
        $r = Sync::read($row, $deadline, Sync::take_force((int) $row->id));
        if ($r === 'retry') {
            $fresh = Store::page((int) $row->id);
            if ($fresh && !(int) $fresh->queued) { self::page_finished(true); }
        }
        return 1;
    }

    /** Re-check after a settings change or a "try again" in the panel. */
    public static function resume(): void {
        Log::clear_alert();
        self::update(['retry_at' => 0, 'wait_until' => 0, 'work' => 1, 'phase' => 'translating', 'message' => 'Wznowiono.']);
        self::chain(1);
    }

    /** Something was queued from the panel: count it in the current run (or start one) and run soon. */
    public static function kick(int $pages = 1): void {
        $s = self::state();
        $open = !empty($s['work']) && !(int) $s['run']['finished'] && (int) $s['run']['started'];
        self::update(['work' => 1, 'wait_until' => 0, 'run' => $open ? ['total' => (int) $s['run']['total'] + $pages]
            : ['started' => time(), 'total' => max($pages, Store::queued_count()), 'done' => 0, 'errors' => 0, 'who' => 'manual', 'finished' => 0]]);
        self::chain(1);
    }

    /** Progress of the current run for the panel (and the ZP Suite summary). */
    public static function progress(): array {
        $s = self::state();
        $run = $s['run'];
        $ready = Store::ready();
        $queued = $ready ? Store::queued_count() : 0;
        $done = (int) $run['done'];
        $total = max((int) $run['total'], $done);
        $work = !empty($s['work']) || !Import::done();
        if ($work && $queued > 0) { $total = max($total, $done + $queued); }
        $percent = $total > 0 ? (int) floor(min($done, $total) * 100 / $total) : ($work ? 0 : 100);
        if ($work && $percent >= 100) { $percent = 99; }
        if (!$work) { $percent = 100; }
        $import = Import::state();
        $running = self::running();
        return [
            'work' => $work, 'phase' => (string) $s['phase'], 'message' => (string) $s['message'],
            'percent' => $percent, 'done' => min($done, $total), 'total' => $total, 'queued' => $queued,
            'errors' => (int) $run['errors'], 'en' => Index::count(),
            'attention' => $ready ? Store::count_pages(['attention' => true, 'status' => ['new', 'done', 'partial', 'error']]) : 0,
            'current' => (string) $s['current'], 'running' => $running,
            'paused' => self::paused(), 'retry_at' => (int) $s['retry_at'],
            // Background steps stopped (no loopback, no WP-Cron): the open panel runs them.
            'stalled' => $work && !$running && (int) $s['step_at'] < time() - 45 && (int) $s['retry_at'] <= time() && (int) $s['wait_until'] <= time(),
            'step_at' => (int) $s['step_at'],
            'import' => Import::done() ? null : ['pages' => (int) $import['pages'], 'of' => count((array) $import['list'])],
        ];
    }

    // ------------------------------------------------------------------ lock

    private static function lock(): bool {
        $token = time() . ':' . wp_generate_uuid4();
        if (add_option(self::LOCK, $token, '', false)) { self::$token = $token; return true; }
        $old = (string) get_option(self::LOCK);
        if ((int) $old > 0 && (int) $old < time() - self::LOCK_TTL) {
            // Compare-and-delete: never remove a lock another request has just taken.
            global $wpdb;
            $deleted = $wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->options} WHERE option_name = %s AND option_value = %s", self::LOCK, $old));
            wp_cache_delete(self::LOCK, 'options');
            wp_cache_delete('notoptions', 'options');
            if ($deleted && add_option(self::LOCK, $token, '', false)) { self::$token = $token; return true; }
        }
        return false;
    }

    private static function unlock(): void {
        if (self::$token === '') { return; }
        global $wpdb;
        $wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->options} WHERE option_name = %s AND option_value = %s", self::LOCK, self::$token));
        wp_cache_delete(self::LOCK, 'options');
        wp_cache_delete('notoptions', 'options');
        self::$token = '';
    }

    public static function unschedule(): void {
        wp_clear_scheduled_hook(self::DAILY);
        wp_clear_scheduled_hook(self::WORK);
        delete_option(self::LOCK);
    }

    // ------------------------------------------------------- content changes

    /** Published or updated: translate at once (only what changed), and refresh the lists that show it. */
    public static function saved($post_id, $post = null, $update = false, $before = null): void {
        if (!Settings::get('auto') || !Store::ready() || !Bridge::ready()) { return; }
        if (!$post instanceof \WP_Post || wp_is_post_revision((int) $post_id) || wp_is_post_autosave((int) $post_id)) { return; }
        if ($post->post_status !== 'publish' || $post->post_password !== '' || !in_array($post->post_type, Settings::types(), true)) { return; }
        if (Sync::plan_post((int) $post_id)) { self::kick(1); }
    }

    /** A post that stops being public loses its English version at once (no 404s in the English sitemap). */
    public static function status_changed($new, $old, $post): void {
        if ($old !== 'publish' || $new === 'publish' || !$post instanceof \WP_Post || !Store::ready() || !Bridge::ready()) { return; }
        $row = Store::page_by_post((int) $post->ID);
        if ($row && $row->status !== 'off') { Sync::retire($row, 'Wpis nie jest już publiczny — wersję angielską wyłączono.'); }
    }

    public static function deleted($post_id): void {
        if (!Store::ready() || !Bridge::ready()) { return; }
        $row = Store::page_by_post((int) $post_id);
        if ($row && $row->status !== 'off') { Sync::retire($row, 'Wpis usunięty.'); }
    }

    public static function term_changed($term_id): void {
        if (!Settings::get('auto') || !Settings::get('terms') || !Store::ready() || !Bridge::ready()) { return; }
        $path = Sync::path_of_term((int) $term_id);
        if ($path === '' || Sync::ineligible($path) !== '' || Sync::alias_of($path) !== '') { return; }
        $id = Sync::ensure($path, 'term', 0, (int) $term_id);
        if ($id) { Store::queue($id, 30); self::update(['work' => 1, 'wait_until' => 0]); self::chain(5); }
    }
}
