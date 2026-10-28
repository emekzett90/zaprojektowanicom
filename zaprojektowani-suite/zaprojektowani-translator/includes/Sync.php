<?php
namespace ZPTE;

if (!defined('ABSPATH')) { exit; }

/**
 * One Polish page on its way to English:
 *   read()    fetch the Polish page as visitors get it, cut it into fragments with the language module itself,
 *             find what has no English text yet and create OpenAI jobs for it;
 *   advance() once the jobs are answered: compile the page's map, give the page an English address if it has
 *             none (or only one made of Polish words), and publish it.
 * Translations are kept per Polish text, not per page version: editing a page never throws its English away;
 * only new or changed sentences are translated again.
 */
final class Sync {
    const MAX_TRIES = 3;
    /** Pages to translate again from scratch at their next read ("Przetłumacz od nowa" in the panel). */
    const FORCE = 'zpte2_force';
    /** 3.1.1: Polish texts (hash => time) whose machine translations are asked for again although they pass the checks (Migrate::knowledge). */
    const AGAIN = 'zpte2_retranslate';
    /** Names the site uses for its own sections, fixed in every English text (older translations used others). */
    const TERM_FIXES = ['Pricing Studio' => 'Quote Studio', 'Valuation studio' => 'Quote Studio', 'Valuation Studio' => 'Quote Studio', 'Studio Wyceny' => 'Quote Studio'];
    /** Parts of every page (menu, footer): their texts are shared by all English pages. */
    const SHARED_IDS = ['zpNewNav'];
    const SHARED_CLASSES = ['zpMegaFooter'];

    // ------------------------------------------------------------------ paths

    /** Site-relative path of a published post, normalized like the language module does it ('' if none). */
    public static function path_of_post(int $post_id): string {
        if ((int) get_option('page_on_front') === $post_id && get_option('show_on_front') === 'page') { return '/'; }
        $url = get_permalink($post_id);
        if (!is_string($url) || $url === '') { return ''; }
        $p = (string) wp_parse_url($url, PHP_URL_PATH);
        if ($p === '' || wp_parse_url($url, PHP_URL_QUERY)) { return ''; } // plain permalinks (?p=1) have no path
        return \ZPL\Router::norm_path(\ZPL\Router::rel($p));
    }

    public static function path_of_term(int $term_id): string {
        $url = get_term_link($term_id);
        if (!is_string($url) || $url === '') { return ''; }
        $p = (string) wp_parse_url($url, PHP_URL_PATH);
        if ($p === '' || wp_parse_url($url, PHP_URL_QUERY)) { return ''; }
        return \ZPL\Router::norm_path(\ZPL\Router::rel($p));
    }

    public static function post_id_for(string $path): int {
        if ($path === '/') { return get_option('show_on_front') === 'page' ? (int) get_option('page_on_front') : 0; }
        $id = (int) url_to_postid(home_url($path));
        return $id > 0 && self::path_of_post($id) === $path ? $id : 0;
    }

    public static function term_id_for(string $path): int {
        if (!preg_match('~^/category/(?:[^/]+/)*([^/]+)/$~', $path, $m)) { return 0; }
        $t = get_term_by('slug', $m[1], 'category');
        return $t instanceof \WP_Term ? (int) $t->term_id : 0;
    }

    /** Why a Polish path gets no English version of its own ('' = it can have one). */
    public static function ineligible(string $path): string {
        if ($path === '' || $path === '/en/' || strpos($path, '/en/') === 0) { return 'adres angielski'; }
        if (\ZPL\Router::never($path)) { return 'adres techniczny'; }
        if (\ZPL\Router::is_excluded($path)) { return 'ścieżka wyłączona w Języki PL/EN'; }
        if (strpos($path, '/dziekujemy') === 0 || strpos($path, '/page/') !== false) { return 'strona pomocnicza'; }
        // Pages made for one client (questionnaires, offers) stay out of Google, so no English version.
        if (function_exists('zp_seo_plan_is_private_path') && zp_seo_plan_is_private_path($path)) { return 'strona dla klienta (noindex)'; }
        return '';
    }

    // ------------------------------------------------------------------ queue

    /** Row for a Polish path (created when missing). */
    public static function ensure(string $path, string $kind, int $post_id = 0, int $term_id = 0): int {
        $row = Store::page_by_path($path);
        if ($row) {
            $data = [];
            if ($post_id > 0 && (int) $row->post_id !== $post_id) { $data['post_id'] = $post_id; }
            if ($term_id > 0 && (int) $row->term_id !== $term_id) { $data['term_id'] = $term_id; }
            if ($row->status === 'off') { $data['status'] = (int) $row->keys_done > 0 ? 'done' : 'new'; $data['message'] = ''; $data['tries'] = 0; }
            if ($data) { Store::page_update((int) $row->id, $data); }
            return (int) $row->id;
        }
        return Store::page_add($path, ['kind' => $kind, 'post_id' => $post_id, 'term_id' => $term_id, 'origin' => 'new']);
    }

    /**
     * Daily check (and "Sprawdź wszystko"): every public post, page and category, the home page and every page
     * the language module has an English address for. New pages first, then edited ones, then the rest.
     * @return array{new:int,changed:int,recheck:int}
     */
    public static function plan_all(): array {
        $out = ['new' => 0, 'changed' => 0, 'recheck' => 0];
        $seen = [];
        $types = Settings::types();
        if ($types) {
            $page = 1;
            do {
                $ids = (array) get_posts(['post_type' => $types, 'post_status' => 'publish', 'has_password' => false, 'posts_per_page' => 300,
                    'paged' => $page++, 'orderby' => 'ID', 'order' => 'ASC', 'fields' => 'ids', 'no_found_rows' => true, 'suppress_filters' => true, 'ignore_sticky_posts' => true]);
                foreach ($ids as $pid) {
                    $pid = (int) $pid;
                    $path = self::path_of_post($pid);
                    if ($path === '' || isset($seen[$path]) || self::ineligible($path) !== '') { continue; }
                    $seen[$path] = true;
                    $id = self::ensure($path, $path === '/' ? 'home' : 'post', $pid);
                    $out[self::plan_one($id, (string) get_post_field('post_modified_gmt', $pid))]++;
                }
            } while (count($ids) === 300);
        }
        if (Settings::get('terms')) {
            $terms = get_terms(['taxonomy' => 'category', 'hide_empty' => true]);
            foreach (is_array($terms) ? $terms : [] as $t) {
                if (!$t instanceof \WP_Term) { continue; }
                $path = self::path_of_term((int) $t->term_id);
                if ($path === '' || isset($seen[$path]) || self::ineligible($path) !== '' || self::alias_of($path) !== '') { continue; }
                $seen[$path] = true;
                $out[self::plan_one(self::ensure($path, 'term', 0, (int) $t->term_id), '')]++;
            }
        }
        // Pages the language module already serves in English (home, services, archives): their new texts too.
        foreach (array_keys(\ZPL\Router::routes()) as $path) {
            $path = (string) $path;
            if (isset($seen[$path]) || self::ineligible($path) !== '') { continue; }
            $seen[$path] = true;
            $kind = $path === '/' ? 'home' : (strpos($path, '/category/') === 0 ? 'term' : 'post');
            $id = self::ensure($path, $kind, $kind === 'post' || $kind === 'home' ? self::post_id_for($path) : 0, $kind === 'term' ? self::term_id_for($path) : 0);
            $out[self::plan_one($id, '')]++;
        }
        // Rows of posts that are no longer public: their English version goes.
        foreach (Store::pages(['kind' => ['post', 'term', 'home']]) as $row) {
            if (isset($seen[(string) $row->path]) || $row->status === 'off') { continue; }
            if ($row->kind === 'term' && self::alias_of((string) $row->path) !== '') { self::retire($row, 'Kategoria jest odpowiednikiem innej kategorii (wspólny adres EN).'); continue; }
            Store::queue((int) $row->id, 40); // read() decides: gone, moved or still there
        }
        return $out;
    }

    /** Queue one row for the daily check with the right urgency; returns new|changed|recheck. */
    private static function plan_one(int $id, string $modified_gmt): string {
        $row = $id ? Store::page($id) : null;
        if (!$row) { return 'recheck'; }
        if ((int) $row->keys_done === 0 && $row->status !== 'done') { Store::queue($id, 10); return 'new'; }
        if ($modified_gmt !== '' && ($row->checked_at === null || $modified_gmt > (string) $row->checked_at)) { Store::queue($id, 20); return 'changed'; }
        if (Settings::get('recheck') || (int) $row->keys_failed > 0 || $row->status === 'error') { Store::queue($id, 60); }
        return 'recheck';
    }

    /** A post was published or updated: translate it now, and refresh the pages that list it. */
    public static function plan_post(int $post_id): bool {
        $path = self::path_of_post($post_id);
        if ($path === '' || self::ineligible($path) !== '') { return false; }
        Store::install();
        $id = self::ensure($path, $path === '/' ? 'home' : 'post', $post_id);
        if (!$id) { return false; }
        Store::queue($id, 5);
        // Lists and the footer show the post's title and lead: the home page, the blog and its category.
        foreach (['/', (string) self::path_of_post((int) get_option('page_for_posts'))] as $p) {
            if ($p !== '' && $p !== $path && ($r = Store::page_by_path($p))) { Store::queue((int) $r->id, 7); }
        }
        foreach ((array) get_the_category($post_id) as $cat) {
            if ($cat instanceof \WP_Term && ($r = Store::page_by_term((int) $cat->term_id))) { Store::queue((int) $r->id, 8); }
        }
        return true;
    }

    /** Translate this page again from scratch at its next read (all fragments, also those already translated). */
    public static function force(int $page_id): void {
        $f = get_option(self::FORCE, []);
        $f = is_array($f) ? $f : [];
        $f[$page_id] = time();
        update_option(self::FORCE, $f, false);
        Store::queue($page_id, 3);
    }

    /** Was a full translation asked for this page? (Asked once: the flag is cleared.) */
    public static function take_force(int $page_id): bool {
        $f = get_option(self::FORCE, []);
        if (!is_array($f) || !isset($f[$page_id])) { return false; }
        unset($f[$page_id]);
        update_option(self::FORCE, $f, false);
        return true;
    }

    /** Texts listed for translating again (hash => time). */
    public static function again(): array {
        $a = get_option(self::AGAIN, []);
        return is_array($a) ? $a : [];
    }

    /**
     * Listed texts got their new translations (hash => [pl, en, meta]): every copy that is not a manual correction takes
     * it, also the copies kept for single pages (they outrank the memory there), and the texts leave the list. The pages
     * that wait for them or read them again compile the new text.
     */
    private static function again_done(array $done): void {
        foreach ($done as [$pl, $en, $meta]) { Store::strings_replace((string) $pl, (string) $en, (array) $meta); }
        $list = self::again();
        foreach (array_keys($done) as $h) { unset($list[$h]); }
        update_option(self::AGAIN, $list, false);
    }

    /** Category paths that share another category's English page (see ZPL\Router::ALIASES). */
    public static function alias_of(string $path): string {
        $a = \ZPL\Router::ALIASES;
        return strpos($path, '/category/') === 0 && isset($a[$path]) ? (string) $a[$path] : '';
    }

    // ------------------------------------------------------------------- read

    /**
     * Read one queued page. Returns 'jobs' (waiting for OpenAI), 'done', 'skip' or 'retry'.
     * @param object $row zpen_pages row
     */
    public static function read($row, int $deadline, bool $force = false): string {
        $id = (int) $row->id;
        $path = (string) $row->path;
        $now = Store::now();

        // 1. The post or category still exists and is public; a post's Polish address may have changed.
        if ($row->kind !== 'term' && (int) $row->post_id > 0) {
            $post = get_post((int) $row->post_id);
            if (!$post || $post->post_status !== 'publish' || $post->post_password !== '') {
                self::retire($row, 'Wpis nie jest już publiczny — wersję angielską wyłączono.');
                return 'skip';
            }
            $current = self::path_of_post((int) $post->ID);
            if ($current !== '' && $current !== $path) {
                $other = Store::page_by_path($current);
                if ($other && (int) $other->id !== $id) { Store::page_delete((int) $other->id); }
                Store::page_update($id, ['path' => $current]);
                Log::add('Zmienił się polski adres: ' . $path . ' → ' . $current . '. Angielski adres zostaje.');
                $path = $current;
                Routes::rebuild();
                Index::rebuild();
            }
        }
        if ($row->kind === 'term' && (int) $row->term_id > 0) {
            $t = get_term((int) $row->term_id, 'category');
            if (!$t instanceof \WP_Term || (int) $t->count === 0) {
                self::retire($row, 'Kategoria jest pusta lub usunięta — wersję angielską wyłączono.');
                return 'skip';
            }
        }

        // 2. Pages that get no English version of their own.
        $why = self::ineligible($path);
        if ($why === '' && $row->kind === 'term' && self::alias_of($path) !== '') { $why = 'kategoria z tym samym adresem EN co inna'; }
        if ($why !== '') {
            self::retire($row, 'Pominięto: ' . $why . '.');
            return 'skip';
        }

        // 3. The Polish page as visitors get it.
        $src = Source::fetch($path, (int) $row->post_id, max(3, min(12, $deadline - time() - 3)));
        if (!$src['ok']) {
            // A post or category confirmed public above that answers 404 is a hiccup of this one request (security rule,
            // cache, maintenance), not a removed page: its English version stays and the read is repeated.
            if ($src['gone'] && in_array((int) $src['code'], [404, 410], true) && ((int) $row->post_id > 0 || (int) $row->term_id > 0)) {
                $src['gone'] = false;
            }
            if ($src['gone']) {
                self::retire($row, $src['error'] . '.');
                return 'skip';
            }
            $tries = (int) $row->tries + 1;
            Store::page_update($id, ['tries' => $tries, 'queued' => $tries < self::MAX_TRIES ? 1 : 0, 'priority' => max((int) $row->priority, 70),
                'checked_at' => $now, 'status' => $row->status === 'new' ? 'error' : $row->status, 'message' => OpenAI::short($src['error']) . '.']);
            Log::add($path . ': ' . $src['error'] . '.', 'warning');
            return 'retry';
        }
        if ($src['via'] === 'post') { Log::add($path . ': ' . $src['error'], 'warning'); }
        $html = (string) $src['html'];
        if ($row->kind === 'post' && Source::noindex($html) && !\ZPL\Router::has_route($path)) {
            self::retire($row, 'Polska strona ma noindex — nie tłumaczę stron wyłączonych z Google.');
            return 'skip';
        }

        // 4. Fragments of the page, plus the texts other pages show about it (titles and leads in lists).
        $units = Source::units($html);
        $shared = self::region_units($html);
        foreach (self::post_units($row) as $k => $kind) {
            if (!isset($units[$k])) { $units[$k] = $kind; }
            $shared[$k] = true;
        }
        $hash = md5(implode("\n", array_keys($units)));
        if (!$force && $hash === (string) $row->source_hash && (int) $row->keys_failed === 0 && $row->status === 'done' && !self::needs_route($row)) {
            Store::page_update($id, ['queued' => 0, 'tries' => 0, 'checked_at' => $now, 'message' => '']);
            Worker::page_finished();
            return 'done';
        }

        // 5. What already has English text: this page's own translations, the translation memory, the shipped
        //    dictionary, manual corrections and the shared texts. Broken earlier translations are done again.
        $hashes = [];
        foreach ($units as $k => $kind) { $hashes[Store::hash((string) $k)] = (string) $kind; }
        $own = Store::strings(array_keys($hashes), $id);
        $mem = Store::strings(array_keys($hashes), Store::SHARED);
        $sharedMap = Serve::shared_map();
        $overrides = \ZPL\Dict::overrides('en');
        $again = self::again();
        $todo = [];
        $known = 0;
        $markShared = [];
        foreach ($units as $k => $kind) {
            $k = (string) $k;
            $h = Store::hash($k);
            $r = self::pick($own[$h] ?? null, $mem[$h] ?? null);
            if ($r) {
                // 3.1.1: a text listed for translating again (Migrate::knowledge) goes to OpenAI although its translation
                // passes the checks, unless the page shows another one for it: the owner's overrides, the site terms, or the
                // shipped dictionaries above a copy in the memory. The old text is served until the new one arrives.
                $redo = isset($again[$h]) && !(int) $r->manual && !isset($overrides[$k]) && !isset(Serve::SITE_TERMS[$k])
                    && ($r === ($own[$h] ?? null) || (int) $r->shared || \ZPL\Dict::lower($k, 'en', $path) === null);
                // A manual correction stays as the owner wrote it, also when the page is translated again on request.
                if ((int) $r->manual || (!$force && !$redo && Translator::broken($k, (string) $r->en) === '')) {
                    $known++;
                    // Shared texts outrank the plugin's own dictionaries on every page: a text those already
                    // translate (shipped, or curated by another module) keeps that wording unless corrected by hand.
                    if (isset($shared[$k]) && ((int) $r->manual || \ZPL\Dict::lower($k, 'en', $path) === null)) {
                        if (!isset($mem[$h])) { $markShared[$k] = (string) $r->en; }
                        elseif (!(int) $mem[$h]->shared) { $markShared[$k] = (string) $mem[$h]->en; }
                    }
                    continue;
                }
                // 3.1.1: a broken earlier translation of a text the plugin's own dictionaries translate (shipped, site terms,
                // manual overrides, a good shared translation) is not asked for again: those serve it (compile() and
                // compile_shared() leave the broken copy out). The earlier translators kept "<szybka_strona />" unchanged, and
                // OpenAI kept it too, three times a day (14.10). The shared map counts only with a good translation of its own:
                // a broken shared text is itself in it until compiled again, and was then never translated again.
                if (!(int) $r->manual && (isset($overrides[$k]) || self::good_shared($sharedMap, $k) || \ZPL\Dict::lower($k, 'en', $path) !== null)
                    && Translator::broken($k, (string) $r->en) !== '') { $known++; continue; }
                $todo[$k] = (string) $kind; // translated again; the old text is served until the new one arrives
                continue;
            }
            // Texts the plugin already translates (manual overrides, shared texts, shipped dictionaries and other
            // modules' curated texts such as the contact forms) never go to OpenAI.
            if (isset($overrides[$k]) || self::good_shared($sharedMap, $k) || \ZPL\Dict::lower($k, 'en', $path) !== null) { $known++; continue; }
            if (strlen($k) > Translator::MAX_KEY) { continue; }
            $todo[$k] = (string) $kind;
        }
        if ($markShared) {
            // Menu and footer texts already translated for one page now serve every English page.
            Store::strings_insert(Store::SHARED, $markShared, 'shared', true);
            Store::mark_shared(array_map([Store::class, 'hash'], array_keys($markShared)));
            self::compile_shared();
        }
        Store::keys_save($id, Store::hash($path), $hashes);

        // 6. OpenAI jobs, in document order, a few fragments each. A text already on its way to OpenAI for another
        //    page (a title or lead shown in several lists) is not sent again: this page waits for that answer.
        $ctx = ['title' => Source::title($html) ?: Source::h1($html), 'path' => $path];
        $await = [];
        if ($todo) {
            $pending = self::pending();
            foreach ($todo as $k => $kind) {
                if (isset($pending[Store::hash((string) $k)])) { $await[$k] = $kind; unset($todo[$k]); }
            }
        }
        $jobs = 0;
        foreach (Translator::batches($todo) as $batch) {
            $chars = 0;
            foreach (array_keys($batch) as $k) { $chars += Translator::len((string) $k); self::$pending[Store::hash((string) $k)] = $id; }
            $sh = array_values(array_intersect(array_keys($batch), array_keys($shared)));
            Store::job_add($id, 'units', ['items' => $batch, 'shared' => $sh, 'ctx' => $ctx], $chars);
            $jobs++;
        }
        if ($await) {
            Store::job_add($id, 'wait', ['items' => $await, 'shared' => array_values(array_intersect(array_keys($await), array_keys($shared))), 'ctx' => $ctx], 0, 'wait');
        }
        $message = $jobs ? 'Tłumaczę ' . Admin::n(count($todo), 'fragment', 'fragmenty', 'fragmentów') . ' (' . Admin::n($jobs, 'zapytanie', 'zapytania', 'zapytań') . ').' : '';
        if ($await) { $message = trim($message . ' Czekam na ' . Admin::n(count($await), 'wspólny fragment tłumaczony', 'wspólne fragmenty tłumaczone', 'wspólnych fragmentów tłumaczonych') . ' dla innej strony.'); }
        Store::page_update($id, [
            'source_hash' => $hash, 'source_modified' => $row->kind === 'post' && (int) $row->post_id > 0 ? (string) get_post_field('post_modified_gmt', (int) $row->post_id) : null,
            'keys_total' => count($hashes), 'keys_done' => $known, 'keys_failed' => 0, 'queued' => 0, 'tries' => 0,
            'stage' => $jobs || $await ? 'translating' : '', 'checked_at' => $now,
            'status' => $row->status === 'error' ? ((int) $row->keys_done > 0 ? 'done' : 'new') : $row->status,
            'message' => $message,
        ]);
        if (!$jobs && !$await) {
            self::advance(Store::page($id));
            return 'done';
        }
        return 'jobs';
    }

    /**
     * Texts inside the menu and the footer: the same on every page, so their translations are shared.
     * @return array<string,bool>
     */
    private static function region_units(string $html): array {
        $out = [];
        $root = \ZPL\Html::parse($html);
        $found = [];
        $walk = static function ($n) use (&$walk, &$found, $html) {
            if (($n['t'] ?? 0) !== 1) { return; }
            $a = \ZPL\Html::attrs(substr($html, $n['s'], $n['te'] - $n['s']));
            $classes = explode(' ', (string) ($a['class'] ?? ''));
            if (in_array((string) ($a['id'] ?? ''), self::SHARED_IDS, true) || array_intersect(self::SHARED_CLASSES, $classes)) { $found[] = $n; return; }
            foreach ($n['c'] ?? [] as $c) { $walk($c); }
        };
        $walk($root);
        foreach ($found as $n) {
            $frag = '<html><head></head><body>' . substr($html, $n['s'], $n['e'] - $n['s']) . '</body></html>';
            foreach (\ZPL\Page::extract($frag) as $it) {
                $k = (string) ($it['k'] ?? '');
                if ($k !== '') { $out[$k] = true; }
            }
        }
        return $out;
    }

    /**
     * Title and leads of a post the way lists, cards and the footer print them (they are on other pages, so
     * they are translated with the post and shared by every English page). @return array<string,string>
     */
    private static function post_units($row): array {
        $pid = (int) $row->post_id;
        if ($row->kind !== 'post' || $pid <= 0 || get_post_type($pid) !== 'post') { return []; }
        $norm = static function (string $s): string { return \ZPL\Html::norm(\ZPL\Html::decode(wp_strip_all_tags($s))); };
        $out = [];
        $title = $norm((string) get_the_title($pid));
        if ($title !== '') { $out[$title] = 'text'; }
        $excerpt = (string) get_post_field('post_excerpt', $pid, 'raw');
        $variants = [];
        if ($excerpt !== '') {
            $variants[] = $excerpt;
            $variants[] = wp_trim_words(wp_strip_all_tags($excerpt), 22, '…');
        } else {
            $variants[] = wp_trim_words(wp_strip_all_tags((string) get_post_field('post_content', $pid, 'raw')), 22, '…');
        }
        if (function_exists('zp_suite_wiedza_fast_excerpt')) {
            foreach ([20, 34, 42] as $w) { $variants[] = (string) zp_suite_wiedza_fast_excerpt($pid, $w); }
            $variants[] = wp_trim_words((string) zp_suite_wiedza_fast_excerpt($pid, 42), 32, '…');
        }
        foreach ($variants as $v) {
            $v = $norm((string) $v);
            if ($v !== '' && strlen($v) <= Translator::MAX_KEY) { $out[$v] = 'text'; }
        }
        return $out;
    }

    // ---------------------------------------------------------------- answers

    /**
     * Save the translations of one answered batch. Returns the number saved; fragments that failed the checks
     * get one more job, after that they are counted as failed on the page.
     * @param object $job zpen_jobs row
     */
    public static function apply_units($job, array $data): int {
        $payload = json_decode((string) $job->items, true);
        $items = is_array($payload['items'] ?? null) ? $payload['items'] : [];
        $shared = array_flip(is_array($payload['shared'] ?? null) ? $payload['shared'] : []);
        $ids = self::ids($items);
        $res = Translator::accept($data, $ids, $items);
        $pageId = (int) $job->page_id;
        $okHashes = array_map([Store::class, 'hash'], array_map('strval', array_keys($res['ok'])));
        $own = $pageId > 0 ? Store::strings($okHashes, $pageId) : [];
        $mem = Store::strings($okHashes, Store::SHARED);
        $again = self::again();
        $redone = [];
        $sharedHit = false;
        foreach ($res['ok'] as $pl => $en) {
            $pl = (string) $pl;
            $h = Store::hash($pl);
            $meta = ['kind' => (string) ($items[$pl] ?? 'text'), 'origin' => 'ai', 'model' => (string) $job->model, 'shared' => isset($shared[$pl]) ? 1 : 0];
            Store::string_save(Store::SHARED, $pl, (string) $en, $meta, true);
            // An earlier translation kept for this page only would hide the new one: replace it too.
            if (isset($own[$h])) { Store::string_save($pageId, $pl, (string) $en, $meta, true); }
            // 3.1.1: a text the memory already shows on every page (a title in lists, asked for by a page that only lists
            // it) is served from the shared map too: compiled again, or the new translation waited for the next change there.
            if (isset($shared[$pl]) || (isset($mem[$h]) && (int) $mem[$h]->shared)) { $sharedHit = true; }
            if (isset($again[$h])) { $redone[$h] = [$pl, (string) $en, $meta]; }
        }
        if ($redone) { self::again_done($redone); }
        if ($sharedHit) { self::compile_shared(); }
        if ($res['failed']) {
            $retry = [];
            foreach ($res['failed'] as $pl => $why) { $retry[(string) $pl] = (string) ($items[$pl] ?? 'text'); }
            if ((int) $job->tries + 1 < 2) {
                $nid = Store::job_add($pageId, 'units', ['items' => $retry, 'shared' => array_values(array_intersect(array_keys($retry), array_keys($shared))), 'ctx' => $payload['ctx'] ?? []], 0);
                Store::job_update($nid, ['tries' => (int) $job->tries + 1]);
            } else {
                $row = Store::page($pageId);
                // 3.1.1: the panel names the text too, so a fragment that keeps failing can be found (and corrected by hand).
                $first = (string) array_key_first($res['failed']);
                $quote = ' („' . (Translator::len($first) > 70 ? mb_substr($first, 0, 69, 'UTF-8') . '…' : $first) . '”)';
                if ($row) { Store::page_update($pageId, ['keys_failed' => (int) $row->keys_failed + count($retry), 'message' => Admin::n(count($retry), 'fragment', 'fragmenty', 'fragmentów') . ' bez tłumaczenia: ' . reset($res['failed']) . $quote . '.']); }
                Log::add(($row ? $row->path : '#' . $pageId) . ': ' . Admin::n(count($retry), 'fragment', 'fragmenty', 'fragmentów') . ' bez tłumaczenia (' . reset($res['failed']) . ')' . $quote . '.', 'warning');
            }
        }
        if ($res['ok'] && ($row = Store::page($pageId))) {
            Store::page_update($pageId, ['keys_done' => min((int) $row->keys_total, (int) $row->keys_done + count($res['ok']))]);
        }
        return count($res['ok']);
    }

    /** "a1" => Polish key, in the order the request listed them (Translator::units_request). */
    public static function ids(array $items): array {
        $ids = [];
        $n = 0;
        foreach (array_keys($items) as $k) { $ids['a' . (++$n)] = (string) $k; }
        return $ids;
    }

    /** English address from an answered slug job. @param object $job */
    public static function apply_slug($job, array $data): bool {
        $payload = json_decode((string) $job->items, true);
        $segments = is_array($payload['segments'] ?? null) ? $payload['segments'] : [];
        $acc = Translator::accept_slug($data, $segments);
        if ($acc['segments']) { Routes::learn_segments($acc['segments']); }
        $row = Store::page((int) $job->page_id);
        if (!$row) { return true; }
        $slug = $acc['slug'];
        if ($slug === '' && (int) $job->tries >= 1) { $slug = Translator::slug_from_title((string) ($payload['page']['title_en'] ?? '')); }
        if ($slug === '') { return false; }
        $en = Routes::build((string) $row->path, $slug);
        if ($en === '') {
            self::park($row, 'Brak angielskiej nazwy dla części adresu ' . Routes::parent_of((string) $row->path) . ' — dodaj ją w Adresach EN.');
            Log::add($row->path . ': brak angielskiej nazwy części adresu, strona czeka.', 'warning');
            return true;
        }
        Routes::assign((int) $row->id, $en);
        self::finish(Store::page((int) $row->id));
        return true;
    }

    // ---------------------------------------------------------------- advance

    /** The page's English address is missing or made of Polish words (and this module may set it). */
    public static function needs_route($row): bool {
        $path = (string) $row->path;
        if ($row->kind === 'home' || self::alias_of($path) !== '') { return false; }
        if (Routes::external($path)) { return false; }
        $route = (string) (\ZPL\Router::routes()[$path] ?? '');
        return $route === '' || Routes::polish($route);
    }

    /** All jobs of a page are answered: compile, give it an English address, publish. @param object|null $row */
    public static function advance($row): void {
        if (!$row) { return; }
        $id = (int) $row->id;
        if (Store::count_jobs(['new', 'sent', 'unknown', 'wait'], $id) > 0) { return; }
        self::compile($id);
        $row = Store::page($id);
        if (!$row) { return; }
        $failed = (int) $row->keys_failed;
        $total = max(1, (int) $row->keys_total);
        if (self::needs_route($row)) {
            if ($failed > max(2, (int) floor($total * 0.05))) {
                // Too much is missing to open a new English page: next daily check tries again.
                self::park($row, 'Za dużo nieprzetłumaczonych fragmentów (' . $failed . ') — strona EN poczeka na kolejne sprawdzenie.');
                return;
            }
            $direct = self::direct_route($row);
            if ($direct !== '') {
                Routes::assign($id, $direct);
                self::finish(Store::page($id));
                return;
            }
            if ($row->stage === 'route') {
                // The address request failed three times (Worker::finish_job): an address from the English
                // title, otherwise the page keeps its current address until the next check.
                $payload = self::slug_payload($row);
                $slug = Translator::slug_from_title((string) ($payload['page']['title_en'] ?? ''));
                $en = $slug !== '' ? Routes::build((string) $row->path, $slug) : '';
                if ($en !== '') {
                    Routes::assign($id, $en);
                    self::finish(Store::page($id));
                    return;
                }
                self::park($row, 'Nie udało się utworzyć angielskiego adresu — spróbuję przy następnym sprawdzeniu.');
                return;
            }
            Store::job_add($id, 'slug', self::slug_payload($row), 0);
            Store::page_update($id, ['stage' => 'route', 'message' => 'Tworzę angielski adres strony.']);
            return;
        }
        self::finish($row);
    }

    /** The page waits for the next check (its current English version, if any, stays). @param object $row */
    private static function park($row, string $message): void {
        Store::page_update((int) $row->id, ['stage' => '', 'queued' => 0, 'status' => (int) $row->keys_done > 0 ? 'partial' : 'error', 'message' => OpenAI::short($message)]);
        Index::rebuild();
        Worker::page_finished(true);
    }

    /** Categories with a known English name get their address without asking the model. @param object $row */
    private static function direct_route($row): string {
        $path = (string) $row->path;
        if ($row->kind !== 'term') { return ''; }
        $seg = Routes::last($path);
        $en = Routes::SEGMENTS[$seg] ?? (string) (Routes::learned_segments()[$seg] ?? '');
        if ($en === '') { return ''; }
        $parent = Routes::parent(Routes::parent_of($path));
        return $parent === '' ? '' : Routes::free($parent . $en . '/', $path);
    }

    /** What the model needs to name the page's English address. @param object $row */
    private static function slug_payload($row): array {
        $path = (string) $row->path;
        $title = '';
        if ($row->kind === 'term' && (int) $row->term_id > 0) {
            $t = get_term((int) $row->term_id, 'category');
            $title = $t instanceof \WP_Term ? (string) $t->name : '';
        } elseif ((int) $row->post_id > 0) {
            $title = (string) get_the_title((int) $row->post_id);
        }
        $title = \ZPL\Html::norm(\ZPL\Html::decode(wp_strip_all_tags($title)));
        $titleEn = $title !== '' ? (string) (\ZPL\Dict::get($title, 'en', $path) ?? '') : '';
        if ($titleEn === '' && $title !== '') {
            $r = Store::strings([Store::hash($title)], Store::SHARED)[Store::hash($title)] ?? null;
            $titleEn = $r ? (string) $r->en : '';
        }
        $parent = Routes::parent_of($path);
        return [
            'page' => ['kind' => (string) $row->kind, 'title_pl' => $title, 'title_en' => $titleEn, 'slug_pl' => Routes::last($path)],
            'segments' => Routes::unknown_segments($parent),
        ];
    }

    /** @param object|null $row */
    public static function finish($row): void {
        if (!$row) { return; }
        $failed = (int) $row->keys_failed;
        $route = (string) (\ZPL\Router::routes()[(string) $row->path] ?? '');
        $was = (string) $row->status;
        Store::page_update((int) $row->id, [
            'stage' => '', 'queued' => 0, 'tries' => 0,
            'status' => $failed > 0 ? 'partial' : 'done',
            'translated_at' => Store::now(),
            'message' => $failed > 0 ? (string) $row->message : '',
        ]);
        Index::rebuild();
        if ($was !== 'done' && $route !== '') {
            Log::add('Wersja EN gotowa: ' . $row->path . ' → ' . $route . ($failed ? ' (bez ' . Admin::n($failed, 'fragmentu', 'fragmentów', 'fragmentów') . ')' : '') . '.');
        }
        Worker::page_finished();
    }

    /** Remove a page's English version (post unpublished, deleted, noindex…). @param object $row */
    public static function retire($row, string $why): void {
        foreach (Store::jobs(['new', 'unknown', 'wait'], 200, (int) $row->id) as $j) { Store::job_update((int) $j->id, ['state' => 'dropped']); }
        foreach (Store::jobs(['sent'], 200, (int) $row->id) as $j) { OpenAI::cancel((string) $j->response_id); Store::job_update((int) $j->id, ['state' => 'dropped']); }
        $had = $row->status !== 'off' && (string) $row->en_path !== '';
        Store::page_update((int) $row->id, ['status' => 'off', 'queued' => 0, 'stage' => '', 'checked_at' => Store::now(), 'message' => OpenAI::short($why)]);
        if ($had) { Routes::rebuild(); }
        Index::rebuild();
        Worker::page_finished();
    }

    // ------------------------------------------------------------- shared waits

    /** Texts in requests not answered yet (this request's own new jobs included): hash => page id. */
    private static $pending = null;

    private static function pending(): array {
        if (self::$pending === null) { self::$pending = Store::pending_texts(); }
        return self::$pending;
    }

    /**
     * Pages waiting for texts another page sent: the wait ends when those answers are in. A text whose request
     * ended without an answer (failed, dropped with its page) is sent by the waiting page itself.
     */
    public static function resolve_waits(): int {
        $waits = Store::jobs(['wait'], 50);
        if (!$waits) { return 0; }
        $pending = Store::pending_texts();
        self::$pending = $pending;
        $again = self::again();
        $n = 0;
        foreach ($waits as $job) {
            $payload = json_decode((string) $job->items, true);
            $items = is_array($payload['items'] ?? null) ? $payload['items'] : [];
            $sharedKeys = array_flip(is_array($payload['shared'] ?? null) ? $payload['shared'] : []);
            $pageId = (int) $job->page_id;
            $mem = $items ? Store::strings(array_map(static function ($k) { return Store::hash((string) $k); }, array_keys($items)), Store::SHARED) : [];
            $done = [];
            $orphans = [];
            $still = [];
            foreach ($items as $k => $kind) {
                $h = Store::hash((string) $k);
                // A text listed for translating again has its old translation in the memory until the new one arrives.
                if (isset($mem[$h]) && (string) $mem[$h]->en !== '' && !isset($again[$h])) { $done[$h] = (string) $k; }
                elseif (isset($pending[$h])) { $still[$k] = $kind; }
                else { $orphans[$k] = $kind; }
            }
            if (!$done && !$orphans) { continue; }
            $n++;
            // A title or lead of this page translated for a list elsewhere is shown on every English page.
            $mark = [];
            foreach ($done as $h => $k) { if (isset($sharedKeys[$k]) && !(int) $mem[$h]->shared) { $mark[] = $h; } }
            if ($mark) { Store::mark_shared($mark); self::compile_shared(); }
            if ($done && ($row = Store::page($pageId))) {
                Store::page_update($pageId, ['keys_done' => min((int) $row->keys_total, (int) $row->keys_done + count($done))]);
            }
            foreach (Translator::batches($orphans) as $batch) {
                $chars = 0;
                foreach (array_keys($batch) as $k) { $chars += Translator::len((string) $k); $pending[Store::hash((string) $k)] = $pageId; }
                $sh = array_values(array_intersect(array_keys($batch), array_keys($sharedKeys)));
                Store::job_add($pageId, 'units', ['items' => $batch, 'shared' => $sh, 'ctx' => $payload['ctx'] ?? []], $chars);
            }
            if ($still) {
                $sh = array_values(array_intersect(array_keys($still), array_keys($sharedKeys)));
                Store::job_update((int) $job->id, ['items' => (string) wp_json_encode(['items' => $still, 'shared' => $sh, 'ctx' => $payload['ctx'] ?? []], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)]);
            } else {
                Store::job_update((int) $job->id, ['state' => 'done']);
            }
        }
        self::$pending = $pending;
        return $n;
    }

    // ---------------------------------------------------------------- compile

    /** Build the served map of one page from its key list. Returns the number of entries. */
    public static function compile(int $page_id): int {
        $row = Store::page($page_id);
        if (!$row) { return 0; }
        $path = (string) $row->path;
        $keys = Store::keys_of($page_id);
        if (!$keys) { return 0; }
        $own = Store::strings(array_keys($keys), $page_id);
        $mem = Store::strings(array_keys($keys), Store::SHARED);
        $map = [];
        foreach ($keys as $h => $kind) {
            $r = self::pick($own[$h] ?? null, $mem[$h] ?? null);
            if (!$r) { continue; }
            $pl = (string) $r->pl;
            if (\ZPL\Page::plain($pl) !== $pl) { continue; } // short versions are made below, never kept as translations
            $en = null;
            if ($r === ($own[$h] ?? null)) {
                // 3.1.1: a broken copy kept from the earlier translators gives way to the plugin's own dictionaries when
                // they translate the text (it was served above them: "<szybka_strona />" on the English law firm page).
                if (!(int) $r->manual && self::dictionary_knows($pl, $path) && Translator::broken($pl, (string) $r->en) !== '') { continue; }
                $en = self::fix_terms((string) $r->en, $pl);
            } elseif (!(int) $r->shared && ((int) $r->manual || \ZPL\Dict::lower($pl, 'en', $path) === null)) {
                // Shared texts come from the shared map. The plugin's own dictionaries (shipped, and other modules' curated
                // texts) keep their wording unless the memory has a manual correction.
                $en = self::fix_terms((string) $r->en, $pl);
            }
            if ($en !== null) { $map[$pl] = $en; }
            $short = self::short_version($pl, (string) $kind, $r, $en, $mem[$h] ?? null);
            if ($short !== '') { $map[\ZPL\Page::variant((string) $kind, $pl)] = $short; }
        }
        $rev = Store::map_save($page_id, Store::hash($path), $map, null);
        Serve::bump($page_id, $rev);
        return count($map);
    }

    /**
     * 3.1.1 (SearchFit): Google shows about 60 characters of a page title and 155 of a description. A longer machine
     * translation gets a short version, made here without OpenAI (Translator::fit: a title loses " | Zaprojektowani" at its
     * end, a description its last sentences); the <title> and description tags use it (\ZPL\Page::variant) and the same
     * text elsewhere on the page keeps the full translation. The owner's wording (overrides in Języki PL/EN, corrections
     * made by hand), the site terms and the shipped and curated texts stay as written, and so does a text that does not fit.
     * @param object $r the row pick() chose  @param string|null $en what this page's map serves for $pl  @param object|null $mem the memory row
     */
    private static function short_version(string $pl, string $kind, $r, ?string $en, $mem): string {
        $max = Translator::limit($kind);
        if (!$max || isset(\ZPL\Dict::overrides('en')[$pl]) || isset(Serve::SITE_TERMS[$pl])) { return ''; }
        if (isset(Serve::shared_map()[$pl])) {
            // The shared map serves the text (above this page's map): a machine translation unless corrected by hand.
            if (!$mem || !(int) $mem->shared || (int) $mem->manual || (string) $mem->en === '') { return ''; }
            $en = self::fix_terms((string) $mem->en, $pl);
        } elseif ($en === null || (int) $r->manual) {
            return '';
        }
        if (Translator::len($en) <= $max) { return ''; }
        $fit = Translator::fit($en, $kind);
        return $fit !== $en && Translator::len($fit) <= $max ? $fit : '';
    }

    /** The site terms, the shared texts, the owner's corrections or the shipped dictionary translate this text. */
    private static function dictionary_knows(string $pl, string $path): bool {
        return isset(Serve::shared_map()[$pl]) || isset(\ZPL\Dict::overrides('en')[$pl]) || \ZPL\Dict::lower($pl, 'en', $path) !== null;
    }

    /** The shared map translates this text, and its translation passes the checks. */
    private static function good_shared(array $sharedMap, string $k): bool {
        return isset($sharedMap[$k]) && Translator::broken($k, (string) $sharedMap[$k]) === '';
    }

    /**
     * The translation a page uses: its own copy (kept from the earlier translators), unless the memory holds a
     * manual correction and the copy is not one, so a correction saved in the panel shows on every page.
     * @param object|null $own zpen_strings row of the page
     * @param object|null $mem zpen_strings row of the memory
     * @return object|null
     */
    private static function pick($own, $mem) {
        $o = $own && (string) $own->en !== '' ? $own : null;
        $m = $mem && (string) $mem->en !== '' ? $mem : null;
        if ($o && $m && (int) $m->manual && !(int) $o->manual) { return $m; }
        return $o ?: $m;
    }

    /** The shared map (titles, leads, menu and footer texts) from the translation memory. */
    public static function compile_shared(): void {
        $map = [];
        foreach (Store::shared_strings(true) as $pl => [$en, $manual]) {
            // 3.1.1: the site terms (Serve::SITE_TERMS: Wiedza = Knowledge…) outrank machine translations of the same
            // text, which used to override them in this map; corrections made by hand stay.
            if (!$manual && isset(Serve::SITE_TERMS[(string) $pl])) { continue; }
            // 3.1.1: a broken machine translation (Polish left in English, lost link markers) is left out: the site terms or
            // the shipped dictionaries serve the text until read() gets a new translation (it counted as translated before).
            if (!$manual && Translator::broken((string) $pl, (string) $en) !== '') { continue; }
            $map[(string) $pl] = self::fix_terms((string) $en, (string) $pl);
        }
        $rev = Store::map_save(Store::SHARED, '', $map, null);
        Serve::bump(Store::SHARED, $rev);
    }

    /** Recompile every page that uses one of these Polish texts (after a manual correction). */
    public static function recompile_using(array $hashes): int {
        // 3.1.1: the shared map first: the pages' short titles and descriptions (short_version()) depend on it.
        self::compile_shared();
        $n = 0;
        foreach (Store::pages_using($hashes) as $pid) { self::compile($pid); $n++; }
        return $n;
    }

    /** Fixed names, and phone numbers written as in Polish ("501 054 253", never "501,054,253" as old translations had it). */
    public static function fix_terms(string $en, string $pl = ''): string {
        $en = strtr($en, self::TERM_FIXES);
        if ($pl === '' || strpos($en, ',') === false || !preg_match_all('~(?<![\d,.])\d{2,3}(?:[ \x{00A0}\x{202F}]\d{3}){2,}(?!\d)~u', $pl, $m)) { return $en; }
        foreach ($m[0] as $tel) { $en = str_replace((string) preg_replace('~[ \x{00A0}\x{202F}]~u', ',', $tel), $tel, $en); }
        return $en;
    }
}
