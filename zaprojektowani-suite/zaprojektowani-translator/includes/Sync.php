<?php
namespace ZPTE;

if (!defined('ABSPATH')) { exit; }

/** One page: fetch the Polish version, translate what the English version is missing, publish. */
final class Sync {
    const DONE = 'done';
    const PARTIAL = 'partial';     // ran out of time or daily budget: continues on the next run
    const RETRY = 'retry';         // OpenAI temporarily unavailable: stop this run
    const LIMIT = 'limit';
    const FATAL = 'fatal';         // key/credit/model problem: stop until the administrator fixes it

    /** @param object $row zpte_paths row */
    public static function run($row, int $deadline): string {
        $id = (int) $row->id;
        $path = (string) $row->path;
        $kind = (string) $row->kind;
        $post = null;
        $now = Store::now();

        // 1. The post behind the page still exists and is public; its address may have changed.
        if ((int) $row->post_id > 0) {
            $post = get_post((int) $row->post_id);
            if (!$post || $post->post_status !== 'publish' || $post->post_password !== '') {
                self::retire($row, 'Wpis nie jest już publiczny — wersję angielską wyłączono.');
                return self::DONE;
            }
            $current = Bridge::path_of_post((int) $post->ID);
            if ($current !== '' && $current !== $path) {
                $other = Store::path_by_path($current);
                if ($other && (int) $other->id !== $id) { Store::path_delete((int) $other->id); }
                if ((int) $row->route_added && (string) $row->en_path !== '') {
                    Bridge::remove_route($path, (string) $row->en_path);
                    Bridge::add_route($current, (string) $row->en_path);
                }
                Log::add('Zmienił się polski adres: ' . $path . ' → ' . $current . '.');
                Store::path_update($id, ['path' => $current]);
                $path = $current;
            }
        }

        // 2. Pages that get no English version of their own.
        $why = Bridge::ineligible($path);
        if ($why !== '') {
            Store::path_update($id, ['status' => $kind === 'existing' ? 'off' : 'skipped', 'queued' => 0, 'checked_at' => $now, 'message' => 'Pominięto: ' . $why . '.']);
            return self::DONE;
        }

        // 3. The Polish page as visitors see it.
        $src = Source::fetch($path, (int) $row->post_id);
        if (!$src['ok']) {
            if ($src['gone']) {
                if ($kind === 'existing') {
                    Store::path_update($id, ['queued' => 0, 'checked_at' => $now, 'message' => $src['error'] . '.']);
                } else {
                    self::retire($row, $src['error'] . ' — pominięto.');
                }
                return self::DONE;
            }
            $tries = (int) $row->tries + 1;
            Store::path_update($id, ['queued' => 0, 'tries' => $tries, 'checked_at' => $now, 'status' => in_array($row->status, ['published', 'draft'], true) ? $row->status : 'error', 'message' => $src['error'] . '.']);
            Log::add($path . ': ' . $src['error'] . '.', 'warning');
            return self::DONE;
        }
        if ($src['via'] === 'post') { Log::add($path . ': ' . $src['error'], 'warning'); }
        if ($kind !== 'existing' && Source::noindex($src['html'])) {
            self::retire($row, 'Polska strona ma noindex — nie tłumaczę stron wyłączonych z Google.', 'skipped');
            return self::DONE;
        }

        // 4. Fragments the English page has no translation for (shipped dictionary and manual fixes count).
        $units = Source::units($src['html']);
        $need = [];
        Bridge::$off = true;
        \ZPL\Dict::flush();
        try {
            foreach ($units as $k => $unitKind) {
                if (\ZPL\Dict::get($k, 'en', $path) === null) { $need[$k] = $unitKind; }
            }
        } finally {
            Bridge::$off = false;
            \ZPL\Dict::flush();
        }

        // 5. Translation memory first, OpenAI for the rest.
        $known = Store::strings_by_hash(array_map([Store::class, 'hash'], array_keys($need)));
        $ids = [];
        $todo = [];
        foreach ($need as $k => $unitKind) {
            $r = $known[Store::hash($k)] ?? null;
            if ($r && (string) $r->en !== '') { $ids[$k] = (int) $r->id; } else { $todo[$k] = $unitKind; }
        }
        $context = Source::title($src['html']) ?: Source::h1($src['html']) ?: $path;
        $failed = [];
        Store::path_update($id, ['keys_total' => count($units), 'keys_ai' => count($ids)]);
        Worker::page_progress($path, count($units), count($units) - count($todo));
        if ($todo) {
            Store::path_update($id, ['status' => in_array($row->status, ['published', 'draft'], true) ? $row->status : 'working']);
            foreach (Translator::batches($todo) as $batch) {
                if (time() >= $deadline - 5 || Log::over_limit()) {
                    // Progress is kept: the next run finds these fragments in the translation memory.
                    Store::links_add($id, array_values($ids));
                    Store::path_update($id, ['message' => Log::over_limit() ? 'Dzienny limit znaków wyczerpany — dokończę jutro.' : 'W trakcie tłumaczenia — dokończę przy następnym uruchomieniu.']);
                    return Log::over_limit() ? self::LIMIT : self::PARTIAL;
                }
                $res = Translator::translate($batch, $context);
                foreach ($res['ok'] as $pl => $en) {
                    $sid = Store::string_save((string) $pl, (string) $en, (string) ($batch[$pl] ?? 'text'), Settings::model());
                    if ($sid) { $ids[(string) $pl] = $sid; }
                }
                foreach ($res['failed'] as $pl) { $failed[(string) $pl] = true; }
                Store::links_add($id, array_values($ids));
                Store::path_update($id, ['keys_ai' => count($ids), 'keys_failed' => count($failed), 'message' => 'Zapisano ' . count($ids) . ' tłumaczeń; kontynuuję stronę.']);
                Worker::page_progress($path, count($units), count($units) - count($need) + count($ids));
                if (!empty($res['deferred']) || !empty($res['limit'])) { return !empty($res['limit']) ? self::LIMIT : self::PARTIAL; }
                if ($res['fatal']) {
                    Store::links_add($id, array_values($ids));
                    Store::path_update($id, ['message' => $res['error']]);
                    Log::alert($res['error']);
                    return self::FATAL;
                }
                if ($res['retry']) {
                    Store::links_add($id, array_values($ids));
                    Store::path_update($id, ['message' => $res['error']]);
                    Log::add($res['error'], 'warning');
                    return self::RETRY;
                }
                if ($res['error'] !== '') { Log::add($path . ': ' . $res['error'], 'warning'); }
            }
            // Validation failures get a later bounded retry; do not hide fatal errors in a second pass.

        }

        // 6. The page uses exactly these fragments now (fragments that disappeared from the page are unlinked).
        Store::links_replace($id, array_values($ids));
        self::pin_titles($post, $units, $ids);

        $stats = [
            'queued' => 0,
            'keys_total' => count($units),
            'keys_ai' => count($ids),
            'keys_failed' => count($failed),
            'checked_at' => $now,
            'source_modified' => $post ? (string) $post->post_modified_gmt : null,
        ];
        if ($todo) { $stats['translated_at'] = $now; }
        $was = (string) $row->status;

        // 7. Status and English address.
        if ($kind === 'existing') {
            $stats['status'] = 'published';
            $stats['message'] = $failed ? 'Nie przetłumaczono ' . count($failed) . ' fragm. — ponowię przy kolejnym sprawdzeniu.' : ($todo ? 'Uzupełniono ' . count($todo) . ' fragm.' : 'Bez braków.');
            $stats['tries'] = $failed ? (int) $row->tries + 1 : 0;
        } elseif ($failed && $was !== 'published' && !self::good_enough(count($failed), count($need), (int) $row->tries + 1)) {
            $stats['status'] = 'error';
            $stats['tries'] = (int) $row->tries + 1;
            $stats['message'] = 'Nie przetłumaczono ' . count($failed) . ' z ' . count($need) . ' fragm. — ponowię przy następnym uruchomieniu.';
        } elseif ($was !== 'published' && Settings::get('mode') === 'draft') {
            $stats['status'] = 'draft';
            $stats['message'] = 'Szkic czeka na akceptację.';
            $stats['tries'] = 0;
        } else {
            if (time() >= $deadline - 5 && Bridge::route($path) === '') {
                Store::links_add($id, array_values($ids));
                Store::path_update($id, ['message' => 'Treść zapisana — kończę publikację w następnym kroku.']);
                return self::PARTIAL;
            }
            $route = self::ensure_route($row, $path, $src['html'], $post);
            $stats = array_merge($stats, $route);
            $stats['status'] = 'published';
            // Leftover fragments are retried by the daily check (up to 4 times).
            $stats['tries'] = $failed ? (int) $row->tries + 1 : 0;
            $stats['message'] = $failed ? 'Opublikowano; ' . count($failed) . ' fragm. czeka na ponowne tłumaczenie.' : ($todo ? 'Przetłumaczono ' . count($todo) . ' fragm.' : 'Bez zmian.');
        }
        if (!empty($stats['keys_failed']) && (int) ($stats['tries'] ?? 0) >= 4) {
            $stats['message'] = 'Pozostało ' . (int) $stats['keys_failed'] . ' fragm. po 4 próbach. Sprawdź Dziennik i wybierz „Przetłumacz ponownie”, aby wznowić.';
        }
        Store::path_update($id, $stats);

        // 8. Shared fragments, cache, lists that show the new page.
        Store::recompute_globals();
        Bridge::rebuild_index();
        $fresh = Store::path($id);
        $en = $fresh ? (string) $fresh->en_path : '';
        Bridge::purge([$path, $en !== '' ? $en : \ZPL\Router::en_path($path)], $post ? (int) $post->ID : 0);
        if ($stats['status'] === 'published' && $was !== 'published' && $kind !== 'existing') {
            Log::add('Opublikowano wersję angielską: ' . $path . ' → ' . ($en !== '' ? $en : \ZPL\Router::en_path($path)) . ' (' . count($todo) . ' fragm.).');
            self::queue_hubs($post);
        } elseif ($todo || $failed) {
            Log::add($path . ': ' . (string) $stats['message']);
        }
        return self::DONE;
    }

    /**
     * A new page goes live when only a few fragments failed (they are retried later), or, after the third
     * attempt, when at least 80% is translated (the language module keeps a page below 90% out of Google).
     */
    private static function good_enough(int $failed, int $need, int $attempt): bool {
        if ($failed <= max(2, (int) ceil($need * 0.05))) { return true; }
        return $attempt >= 3 && $failed <= (int) floor($need * 0.2);
    }

    /** Titles and excerpts appear in post lists and cards on other pages: share them site-wide. */
    private static function pin_titles($post, array $units, array $ids): void {
        if (!$post) { return; }
        $want = [];
        foreach ([get_the_title($post), (string) $post->post_excerpt] as $t) {
            $k = \ZPL\Html::norm(wp_strip_all_tags(html_entity_decode((string) $t, ENT_QUOTES | ENT_HTML5, 'UTF-8')));
            if ($k !== '' && isset($ids[$k])) { $want[] = $ids[$k]; }
        }
        if ($want) { Store::pin($want); }
    }

    /**
     * English address for a newly translated page: an existing route if there is one, otherwise
     * the English parent learned from similar pages + an English slug from the model.
     */
    public static function ensure_route($row, string $path, string $html, $post): array {
        $existing = Bridge::route($path);
        if ($existing !== '') { return ['en_path' => $existing]; }

        $segs = array_values(array_filter(explode('/', trim($path, '/')), 'strlen'));
        $last = (string) array_pop($segs);
        $parent = $segs ? '/' . implode('/', $segs) . '/' : '/';
        $prefix = $parent === '/' ? '/en/' : Bridge::learned_parent($parent);
        $unknown = [];
        if ($prefix === '') {
            // Parent path never seen in English: translate its segments (deepest known prefix first).
            $prefix = '/en/';
            $acc = '/';
            foreach ($segs as $s) {
                $acc .= $s . '/';
                $known = Bridge::learned_parent($acc) ?: Bridge::route($acc);
                if ($known !== '') { $prefix = trailingslashit($known); continue; }
                $unknown[] = $s;
            }
        }

        $title_pl = $post ? \ZPL\Html::norm(wp_strip_all_tags(get_the_title($post))) : (Source::h1($html) ?: Source::title($html));
        $title_en = '';
        if ($title_pl !== '') {
            $t = Store::strings_by_hash([Store::hash($title_pl)]);
            $title_en = $t ? (string) reset($t)->en : '';
        }
        $ai = Translator::slug($title_pl, $title_en, rawurldecode($last), $unknown);
        $slug = $ai['slug'] !== '' ? $ai['slug'] : Translator::slug_from_title($title_en !== '' ? $title_en : $title_pl);
        if ($slug === '') { $slug = Translator::sanitize_slug(rawurldecode($last)); }
        foreach ($unknown as $s) {
            $prefix .= ($ai['segments'][$s] ?? Translator::sanitize_slug(rawurldecode($s))) . '/';
        }
        $en = Bridge::free_en_path($prefix . $slug . '/', $path);
        Bridge::add_route($path, $en);
        return ['en_path' => $en, 'route_added' => 1];
    }

    /** Home, blog list and the post's categories list the new post: fill their gaps soon. */
    private static function queue_hubs($post): void {
        if (!Settings::get('gaps')) { return; }
        $paths = ['/'];
        $blog = (int) get_option('page_for_posts');
        if ($blog > 0) { $paths[] = Bridge::path_of_post($blog); }
        if ($post && $post->post_type === 'post') {
            foreach ((array) get_the_category((int) $post->ID) as $cat) {
                $link = get_category_link($cat);
                if (is_string($link) && $link !== '') { $paths[] = \ZPL\Router::norm_path(\ZPL\Router::rel((string) wp_parse_url($link, PHP_URL_PATH))); }
            }
        }
        $routes = \ZPL\Router::routes();
        foreach (array_unique(array_filter($paths)) as $p) {
            if (!isset($routes[$p]) || Bridge::ineligible($p) !== '') { continue; }
            $pid = Store::path_add($p, ['kind' => 'existing', 'status' => 'published']);
            if ($pid) { Store::queue($pid, 35); }
        }
    }

    /** Turn off the English version of a page that is gone (route removed only if this module made it). */
    public static function retire($row, string $message, string $status = 'skipped'): void {
        $id = (int) $row->id;
        if ((int) $row->route_added && (string) $row->en_path !== '') { Bridge::remove_route((string) $row->path, (string) $row->en_path); }
        $was = (string) $row->status;
        Store::path_update($id, ['status' => $status, 'queued' => 0, 'route_added' => 0, 'checked_at' => Store::now(), 'message' => $message]);
        Store::links_replace($id, []);
        if ($was === 'published') {
            Store::recompute_globals();
            Bridge::rebuild_index();
            Bridge::purge([(string) $row->path, (string) $row->en_path], (int) $row->post_id);
            Log::add((string) $row->path . ': ' . $message);
        }
    }
}
