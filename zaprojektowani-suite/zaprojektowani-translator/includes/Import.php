<?php
namespace ZPTE;

if (!defined('ABSPATH')) { exit; }

/**
 * One-time copy of the earlier English translations into the zpen_* tables, in small resumable steps run by
 * the worker. Nothing is deleted or changed in the earlier translators' data. Until the copy is done, Serve
 * keeps reading the earlier data directly, so English pages look the same during and after the switch.
 */
final class Import {
    const OPTION = 'zpte2_import';
    const STEP_PAGES = 25;
    const STEP_STRINGS = 800;

    private static $done = null;

    public static function state(): array {
        $s = get_option(self::OPTION, []);
        return array_merge(['step' => 'start', 'cursor' => 0, 'done' => 0, 'pages' => 0, 'strings' => 0, 'list' => []], is_array($s) ? $s : []);
    }

    public static function done(): bool {
        // 2.1.0: German and Spanish have nothing to copy (no earlier translator made them).
        if (!Lang::is_en()) { return true; }
        if (self::$done === null) {
            $s = get_option(self::OPTION, []);
            self::$done = is_array($s) && !empty($s['done']);
        }
        return self::$done;
    }

    private static function save(array $s): void {
        update_option(self::OPTION, $s, true);
        self::$done = !empty($s['done']);
    }

    /** One step; returns true while there is more to do. Safe to repeat: every write is idempotent. */
    public static function step(int $deadline): bool {
        if (self::done()) { return false; }
        Store::install();
        $s = self::state();
        while (time() < $deadline) {
            switch ($s['step']) {
                case 'start':
                    $s['list'] = self::sources();
                    $s['cursor'] = 0;
                    $s['step'] = 'pages';
                    Log::add('Przenoszę obecne tłumaczenia EN: ' . Admin::n(count($s['list']), 'strona', 'strony', 'stron') . '.', 'info');
                    break;
                case 'pages':
                    $chunk = array_slice($s['list'], (int) $s['cursor'], self::STEP_PAGES, true);
                    if (!$chunk) { $s['step'] = 'strings'; $s['cursor'] = 0; break; }
                    foreach ($chunk as $source => $info) {
                        self::page((string) $source, (array) $info);
                        $s['cursor']++;
                        $s['pages']++;
                    }
                    break;
                case 'strings':
                    $rows = Legacy::zpte_strings((int) $s['cursor'], self::STEP_STRINGS);
                    if (!$rows) { $s['step'] = 'finish'; break; }
                    $plain = [];
                    $shared = [];
                    foreach ($rows as $r) {
                        $s['cursor'] = (int) $r['id'];
                        $pl = (string) $r['pl'];
                        $en = (string) $r['en'];
                        if ($pl === '' || $en === '') { continue; }
                        if ((int) $r['manual']) {
                            Store::string_save(Store::SHARED, $pl, $en, ['kind' => (string) $r['kind'], 'origin' => 'zpte', 'manual' => 1, 'shared' => (int) $r['is_global']], false);
                        } elseif ((int) $r['is_global']) {
                            $shared[$pl] = $en;
                        } else {
                            $plain[$pl] = $en;
                        }
                    }
                    $s['strings'] += Store::strings_insert(Store::SHARED, $plain, 'zpte', false);
                    $s['strings'] += Store::strings_insert(Store::SHARED, $shared, 'zpte', true);
                    // A title already copied from a page's translations is still shown on every English page.
                    if ($shared) { Store::mark_shared(array_map([Store::class, 'hash'], array_map('strval', array_keys($shared)))); }
                    break;
                case 'finish':
                    Sync::compile_shared();
                    Routes::rebuild();
                    Index::rebuild();
                    Retire::old_translators();
                    $s['done'] = time();
                    $s['list'] = [];
                    self::save($s);
                    Serve::flush();
                    Log::add('Obecne tłumaczenia EN przeniesione: ' . Admin::n((int) $s['pages'], 'strona', 'strony', 'stron') . ', ' . Admin::n((int) $s['strings'], 'tekst', 'teksty', 'tekstów') . ' z pamięci tłumaczeń. Poprzednie tłumacze wyłączone, ich dane zostają bez zmian.', 'info');
                    return false;
            }
            self::save($s);
        }
        self::save($s);
        return true;
    }

    /**
     * Pages with English data in the earlier translators: source => [en, post_id, origin].
     */
    private static function sources(): array {
        $out = [];
        foreach (Legacy::zpte_paths() as $id => $p) {
            if ($p['path'] === '' || $p['status'] !== 'published') { continue; }
            $out[$p['path']] = ['en' => $p['en_path'], 'post_id' => $p['post_id'], 'origin' => 'zpte', 'zpte' => $id];
        }
        foreach (Legacy::codex_active() as $source => $rev) {
            $out[$source] = array_merge($out[$source] ?? [], ['origin' => 'codex']);
        }
        foreach (Legacy::codex_routes() as $source => $en) {
            $out[$source] = array_merge($out[$source] ?? [], ['en' => $en, 'origin' => $out[$source]['origin'] ?? 'codex']);
        }
        foreach (Legacy::v2_routes() as $source => $en) {
            $out[$source] = array_merge($out[$source] ?? [], ['en' => $en, 'origin' => 'v2', 'post_id' => (int) (Legacy::v2_snapshot($source)['post_id'] ?? 0)]);
        }
        foreach (Routes::FIXED as $source => $en) {
            $out[$source] = array_merge($out[$source] ?? ['origin' => 'v2'], ['en' => $en, 'fixed' => 1]);
        }
        ksort($out);
        return $out;
    }

    /** Copy one page: its row, its English address and its translations. */
    private static function page(string $source, array $info): void {
        $source = \ZPL\Router::norm_path($source);
        if ($source === '' || strpos($source, '/en/') === 0 || \ZPL\Router::never($source)) { return; }
        $post_id = (int) ($info['post_id'] ?? 0);
        if ($post_id <= 0) { $post_id = Sync::post_id_for($source); }
        $kind = strpos($source, '/category/') === 0 ? 'term' : ($source === '/' ? 'home' : 'post');
        $term_id = $kind === 'term' ? Sync::term_id_for($source) : 0;
        $origin = substr((string) ($info['origin'] ?? 'import'), 0, 12);
        $id = Store::page_add($source, ['post_id' => $post_id, 'term_id' => $term_id, 'kind' => $kind, 'origin' => $origin]);
        if (!$id) { return; }

        // English address: the fixed one, or the earlier translator's one. An address made of Polish words keeps
        // working until the page has an English one (the worker asks for it right after the copy).
        $en = (string) ($info['en'] ?? '');
        $polish = $en !== '' && Routes::polish($en);
        $data = [];
        // A page edited between the update and the copy (e.g. a title changed by another module) was queued as new.
        $row = Store::page($id);
        if ($row && (string) $row->origin === 'new') { $data['origin'] = $origin; }
        if (!Routes::external($source) && $en !== '' && Routes::valid_en($en)) { $data['en_path'] = $en; }

        // Translations: the rewrite and v2 maps (what is served today), plus Tłumacz EN 1's own page strings.
        $map = Legacy::page_map($source);
        if (!empty($info['zpte'])) {
            foreach (Legacy::zpte_page_map((int) $info['zpte']) as $pl => $tr) { if (!isset($map[$pl])) { $map[$pl] = $tr; } }
        }
        // Site terms are shared by every page now: a page keeps no copy of its own.
        $map = array_diff_key($map, Serve::SITE_TERMS);
        foreach ($map as $pl => $tr) { $map[$pl] = Sync::fix_terms((string) $tr, (string) $pl); }
        if ($map) {
            Store::strings_insert($id, $map, $origin);
            // The same text on another page (a lead shown in a list, a heading used twice) reuses this translation
            // instead of going to OpenAI again. Translations already in the memory stay as they are.
            Store::strings_insert(Store::SHARED, $map, $origin);
            $keys = [];
            foreach ($map as $pl => $tr) { $keys[Store::hash((string) $pl)] = 'text'; }
            $served = [];
            foreach ($map as $pl => $tr) { $served[(string) $pl] = (string) $tr; }
            $rev = Store::map_save($id, Store::hash($source), $served, $keys);
            Serve::bump($id, $rev);
            $data['status'] = 'done';
            $data['keys_total'] = count($map);
            $data['keys_done'] = count($map);
            $data['translated_at'] = Store::now();
        }
        if ($data) { Store::page_update($id, $data); }
        // An address made of Polish words gets an English one in the next worker steps (the old one then redirects).
        if ($polish && empty($info['fixed'])) { Store::page_update($id, ['message' => 'Angielski adres do poprawy (polskie słowa).']); Store::queue($id, 5); }
        if (!empty($info['fixed']) && $en !== '' && '/en' . $source !== $en) { Routes::moved('/en' . $source, $en); }
    }
}
