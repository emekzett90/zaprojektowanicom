<?php
namespace ZPTE;

if (!defined('ABSPATH')) { exit; }

/**
 * Tables of Tłumacz EN 2 (prefix {wp}zpen_). The earlier translators keep their own data untouched
 * (zpte_* tables, zps_en_rewrite_* and zpt2_* options): everything here is a copy or new work, so
 * reinstalling an older plugin version brings the old state back.
 *
 *  zpen_pages   — one row per Polish page (post, page, category, home): English address, status, queue;
 *  zpen_strings — translations: scope 0 = translation memory for the whole site, scope = page id for
 *                 translations that belong to one page only (copied from the previous translators);
 *  zpen_maps    — what the English page is served from: the compiled map of one page and the key list of
 *                 its Polish version (page_id 0 = shared map: titles, excerpts, menu and footer texts);
 *  zpen_jobs    — OpenAI requests (one batch of fragments or one English address), resumable.
 */
final class Store {
    const DB_VERSION = '2.0.0';
    const DB_OPTION = 'zpte2_db';
    const SHARED = 0;
    const RETRY_AFTER = 600; // a page that failed to load is read again after 10 minutes

    public static function table(string $name): string {
        global $wpdb;
        return $wpdb->prefix . 'zpen_' . $name;
    }

    public static function now(): string { return current_time('mysql', true); }

    public static function ready(): bool { return get_option(self::DB_OPTION) === self::DB_VERSION; }

    public static function install(): void {
        if (self::ready()) { return; }
        global $wpdb;
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        $c = $wpdb->get_charset_collate();
        $pages = self::table('pages');
        $strings = self::table('strings');
        $maps = self::table('maps');
        $jobs = self::table('jobs');
        dbDelta("CREATE TABLE {$pages} (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  path_hash char(32) NOT NULL,
  path varchar(1000) NOT NULL DEFAULT '',
  post_id bigint(20) unsigned NOT NULL DEFAULT 0,
  term_id bigint(20) unsigned NOT NULL DEFAULT 0,
  kind varchar(12) NOT NULL DEFAULT 'post',
  status varchar(12) NOT NULL DEFAULT 'new',
  queued tinyint(1) NOT NULL DEFAULT 0,
  priority smallint(5) NOT NULL DEFAULT 50,
  stage varchar(12) NOT NULL DEFAULT '',
  en_path varchar(1000) NOT NULL DEFAULT '',
  origin varchar(12) NOT NULL DEFAULT '',
  keys_total int(11) NOT NULL DEFAULT 0,
  keys_done int(11) NOT NULL DEFAULT 0,
  keys_failed int(11) NOT NULL DEFAULT 0,
  tries smallint(5) NOT NULL DEFAULT 0,
  source_hash char(32) NOT NULL DEFAULT '',
  source_modified datetime NULL DEFAULT NULL,
  checked_at datetime NULL DEFAULT NULL,
  translated_at datetime NULL DEFAULT NULL,
  message text NULL,
  created_at datetime NOT NULL,
  PRIMARY KEY  (id),
  UNIQUE KEY path_hash (path_hash),
  KEY post_id (post_id),
  KEY queued (queued,priority),
  KEY status (status)
) {$c};");
        dbDelta("CREATE TABLE {$strings} (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  scope bigint(20) unsigned NOT NULL DEFAULT 0,
  hash char(32) NOT NULL,
  pl longtext NOT NULL,
  en longtext NOT NULL,
  kind varchar(10) NOT NULL DEFAULT 'text',
  origin varchar(12) NOT NULL DEFAULT '',
  shared tinyint(1) NOT NULL DEFAULT 0,
  manual tinyint(1) NOT NULL DEFAULT 0,
  model varchar(60) NOT NULL DEFAULT '',
  created_at datetime NOT NULL,
  updated_at datetime NOT NULL,
  PRIMARY KEY  (id),
  UNIQUE KEY scope_hash (scope,hash),
  KEY hash (hash),
  KEY shared (shared)
) {$c};");
        dbDelta("CREATE TABLE {$maps} (
  page_id bigint(20) unsigned NOT NULL,
  path_hash char(32) NOT NULL DEFAULT '',
  rev int(10) unsigned NOT NULL DEFAULT 0,
  map longtext NOT NULL,
  keys_list longtext NOT NULL,
  updated_at datetime NOT NULL,
  PRIMARY KEY  (page_id),
  KEY path_hash (path_hash)
) {$c};");
        dbDelta("CREATE TABLE {$jobs} (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  page_id bigint(20) unsigned NOT NULL DEFAULT 0,
  kind varchar(10) NOT NULL DEFAULT 'units',
  state varchar(10) NOT NULL DEFAULT 'new',
  response_id varchar(200) NOT NULL DEFAULT '',
  items longtext NOT NULL,
  result longtext NULL,
  chars int(11) NOT NULL DEFAULT 0,
  tries smallint(5) NOT NULL DEFAULT 0,
  error varchar(255) NOT NULL DEFAULT '',
  model varchar(60) NOT NULL DEFAULT '',
  effort varchar(10) NOT NULL DEFAULT '',
  sent_at datetime NULL DEFAULT NULL,
  created_at datetime NOT NULL,
  updated_at datetime NOT NULL,
  PRIMARY KEY  (id),
  KEY page_id (page_id),
  KEY state (state)
) {$c};");
        update_option(self::DB_OPTION, self::DB_VERSION, true);
    }

    public static function hash(string $s): string { return md5($s); }

    // ------------------------------------------------------------------ pages

    /** @return object|null */
    public static function page(int $id) {
        global $wpdb;
        $row = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . self::table('pages') . ' WHERE id = %d', $id));
        return $row ?: null;
    }

    /** @return object|null */
    public static function page_by_path(string $path) {
        global $wpdb;
        $row = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . self::table('pages') . ' WHERE path_hash = %s', self::hash($path)));
        return $row ?: null;
    }

    /** @return object|null */
    public static function page_by_post(int $post_id) {
        global $wpdb;
        if ($post_id <= 0) { return null; }
        $row = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . self::table('pages') . ' WHERE post_id = %d ORDER BY id ASC LIMIT 1', $post_id));
        return $row ?: null;
    }

    /** @return object|null */
    public static function page_by_term(int $term_id) {
        global $wpdb;
        if ($term_id <= 0) { return null; }
        $row = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . self::table('pages') . ' WHERE term_id = %d ORDER BY id ASC LIMIT 1', $term_id));
        return $row ?: null;
    }

    /** Insert a page (or return the existing row id). */
    public static function page_add(string $path, array $data = []): int {
        global $wpdb;
        $row = self::page_by_path($path);
        if ($row) { return (int) $row->id; }
        $data = array_merge(['kind' => 'post', 'status' => 'new', 'queued' => 0, 'priority' => 50, 'post_id' => 0, 'term_id' => 0], $data);
        $data['path'] = $path;
        $data['path_hash'] = self::hash($path);
        $data['created_at'] = self::now();
        if (!$wpdb->insert(self::table('pages'), $data)) {
            $row = self::page_by_path($path);
            return $row ? (int) $row->id : 0;
        }
        return (int) $wpdb->insert_id;
    }

    public static function page_update(int $id, array $data): void {
        global $wpdb;
        if (isset($data['path'])) { $data['path_hash'] = self::hash((string) $data['path']); }
        $wpdb->update(self::table('pages'), $data, ['id' => $id]);
    }

    public static function page_delete(int $id): void {
        global $wpdb;
        $wpdb->delete(self::table('maps'), ['page_id' => $id]);
        $wpdb->delete(self::table('strings'), ['scope' => $id]);
        $wpdb->delete(self::table('jobs'), ['page_id' => $id]);
        $wpdb->delete(self::table('pages'), ['id' => $id]);
    }

    /** Put a page in the queue; a page already waiting keeps the more urgent priority. */
    public static function queue(int $id, int $priority): void {
        $row = self::page($id);
        if (!$row || in_array($row->status, ['off'], true)) { return; }
        $prio = (int) $row->queued ? min((int) $row->priority, $priority) : $priority;
        self::page_update($id, ['queued' => 1, 'priority' => $prio]);
    }

    /** Next page to read (queued, not waiting for OpenAI; a page that just failed to load waits 10 minutes). @return object|null */
    public static function next_queued() {
        global $wpdb;
        $row = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . self::table('pages') . " WHERE queued = 1 AND stage = '' AND status <> 'off' AND (tries = 0 OR checked_at IS NULL OR checked_at < %s) ORDER BY priority ASC, id ASC LIMIT 1", gmdate('Y-m-d H:i:s', time() - self::RETRY_AFTER)));
        return $row ?: null;
    }

    /** When the first page that failed to load may be read again (0 = no page waits for that). */
    public static function next_retry_at(): int {
        global $wpdb;
        $t = $wpdb->get_var('SELECT MIN(checked_at) FROM ' . self::table('pages') . " WHERE queued = 1 AND stage = '' AND status <> 'off' AND tries > 0 AND checked_at IS NOT NULL");
        return $t ? (int) strtotime((string) $t . ' UTC') + self::RETRY_AFTER : 0;
    }

    public static function queued_count(): int {
        global $wpdb;
        return (int) $wpdb->get_var('SELECT COUNT(*) FROM ' . self::table('pages') . " WHERE (queued = 1 OR stage <> '') AND status <> 'off'");
    }

    /** Pages waiting for OpenAI answers or for their English address. @return object[] */
    public static function in_stage(string $stage, int $limit = 20): array {
        global $wpdb;
        $rows = $wpdb->get_results($wpdb->prepare('SELECT * FROM ' . self::table('pages') . ' WHERE stage = %s ORDER BY priority ASC, id ASC LIMIT %d', $stage, $limit));
        return is_array($rows) ? $rows : [];
    }

    /** Pages of a stage with no request left (not answered, or waiting for another page's answer). @return object[] */
    public static function ready_in_stage(string $stage, int $limit = 20): array {
        global $wpdb;
        $rows = $wpdb->get_results($wpdb->prepare('SELECT p.* FROM ' . self::table('pages') . ' p WHERE p.stage = %s AND NOT EXISTS (SELECT 1 FROM ' . self::table('jobs') . " j WHERE j.page_id = p.id AND j.state IN ('new','sent','unknown','wait')) ORDER BY p.priority ASC, p.id ASC LIMIT %d", $stage, $limit));
        return is_array($rows) ? $rows : [];
    }

    /** @return object[] */
    public static function pages(array $where = [], int $limit = 0, int $offset = 0, string $order = 'id DESC'): array {
        global $wpdb;
        [$sql, $args] = self::where($where);
        $q = 'SELECT * FROM ' . self::table('pages') . $sql . ' ORDER BY ' . $order . ($limit > 0 ? ' LIMIT ' . (int) $limit . ' OFFSET ' . (int) $offset : '');
        $rows = $args ? $wpdb->get_results($wpdb->prepare($q, $args)) : $wpdb->get_results($q);
        return is_array($rows) ? $rows : [];
    }

    public static function count_pages(array $where = []): int {
        global $wpdb;
        [$sql, $args] = self::where($where);
        $q = 'SELECT COUNT(*) FROM ' . self::table('pages') . $sql;
        return (int) ($args ? $wpdb->get_var($wpdb->prepare($q, $args)) : $wpdb->get_var($q));
    }

    /** ['kind' => 'post', 'status' => ['error'], 'attention' => true, 'queued' => 1, 'search' => 'logo'] */
    private static function where(array $where): array {
        global $wpdb;
        $parts = [];
        $args = [];
        foreach (['kind', 'status'] as $col) {
            if (!isset($where[$col])) { continue; }
            $vals = (array) $where[$col];
            if (!$vals) { continue; }
            $parts[] = $col . ' IN (' . implode(',', array_fill(0, count($vals), '%s')) . ')';
            foreach ($vals as $v) { $args[] = (string) $v; }
        }
        if (!empty($where['attention'])) { $parts[] = "(status = 'error' OR keys_failed > 0)"; }
        if (isset($where['queued'])) { $parts[] = (int) $where['queued'] ? "(queued = 1 OR stage <> '')" : "(queued = 0 AND stage = '')"; }
        if (!empty($where['search'])) { $parts[] = '(path LIKE %s OR en_path LIKE %s)'; $like = '%' . $wpdb->esc_like((string) $where['search']) . '%'; $args[] = $like; $args[] = $like; }
        return [$parts ? ' WHERE ' . implode(' AND ', $parts) : '', $args];
    }

    // ---------------------------------------------------------------- strings

    /**
     * Translations by Polish key for one scope. @return array<string,object> hash => row (id, hash, pl, en, kind, origin, shared, manual)
     */
    public static function strings(array $hashes, int $scope = 0): array {
        global $wpdb;
        $out = [];
        foreach (array_chunk(array_values(array_unique($hashes)), 300) as $chunk) {
            $q = 'SELECT id, hash, pl, en, kind, origin, shared, manual FROM ' . self::table('strings') . ' WHERE scope = %d AND hash IN (' . implode(',', array_fill(0, count($chunk), '%s')) . ')';
            foreach ((array) $wpdb->get_results($wpdb->prepare($q, array_merge([$scope], $chunk))) as $r) { $out[(string) $r->hash] = $r; }
        }
        return $out;
    }

    /** All translations of one scope: Polish key => English. */
    public static function scope_map(int $scope): array {
        global $wpdb;
        $out = [];
        $rows = $wpdb->get_results($wpdb->prepare('SELECT pl, en FROM ' . self::table('strings') . " WHERE scope = %d AND en <> ''", $scope), ARRAY_N);
        foreach ((array) $rows as $r) { $out[(string) $r[0]] = (string) $r[1]; }
        return $out;
    }

    /** Translations shown on every English page (titles, excerpts, menu, footer). Polish key => English. */
    /** Shared texts: [pl => en], or with $manual [pl => [en, manual]]. */
    public static function shared_strings(bool $manual = false): array {
        global $wpdb;
        $out = [];
        $rows = $wpdb->get_results('SELECT pl, en, manual FROM ' . self::table('strings') . " WHERE scope = 0 AND shared = 1 AND en <> ''", ARRAY_N);
        foreach ((array) $rows as $r) { $out[(string) $r[0]] = $manual ? [(string) $r[1], (int) $r[2]] : (string) $r[1]; }
        return $out;
    }

    /**
     * Save a translation. A manual correction is never replaced by a machine translation, and an
     * existing translation is replaced only when $replace is true. Returns the row id.
     */
    public static function string_save(int $scope, string $pl, string $en, array $meta = [], bool $replace = true): int {
        global $wpdb;
        $t = self::table('strings');
        $h = self::hash($pl);
        $now = self::now();
        $row = $wpdb->get_row($wpdb->prepare("SELECT id, manual, shared FROM {$t} WHERE scope = %d AND hash = %s", $scope, $h));
        $manual = !empty($meta['manual']) ? 1 : 0;
        if ($row) {
            $data = [];
            if ($manual || ($replace && !(int) $row->manual)) {
                $data = ['en' => $en, 'kind' => (string) ($meta['kind'] ?? 'text'), 'origin' => substr((string) ($meta['origin'] ?? ''), 0, 12), 'model' => substr((string) ($meta['model'] ?? ''), 0, 60), 'updated_at' => $now];
                if ($manual) { $data['manual'] = 1; }
            }
            if (!empty($meta['shared']) && !(int) $row->shared) { $data['shared'] = 1; $data['updated_at'] = $now; }
            if ($data) { $wpdb->update($t, $data, ['id' => (int) $row->id]); }
            return (int) $row->id;
        }
        $ok = $wpdb->insert($t, [
            'scope' => $scope, 'hash' => $h, 'pl' => $pl, 'en' => $en,
            'kind' => (string) ($meta['kind'] ?? 'text'), 'origin' => substr((string) ($meta['origin'] ?? ''), 0, 12),
            'shared' => !empty($meta['shared']) ? 1 : 0, 'manual' => $manual, 'model' => substr((string) ($meta['model'] ?? ''), 0, 60),
            'created_at' => $now, 'updated_at' => $now,
        ]);
        if ($ok) { return (int) $wpdb->insert_id; }
        return (int) $wpdb->get_var($wpdb->prepare("SELECT id FROM {$t} WHERE scope = %d AND hash = %s", $scope, $h));
    }

    /**
     * Fast copy of many translations into one scope (imports). Rows that already exist are left alone.
     * @param array<string,string> $map Polish key => English
     */
    public static function strings_insert(int $scope, array $map, string $origin, bool $shared = false, string $kind = 'text'): int {
        global $wpdb;
        $t = self::table('strings');
        $now = self::now();
        $n = 0;
        foreach (array_chunk($map, 150, true) as $chunk) {
            $values = [];
            $args = [];
            foreach ($chunk as $pl => $en) {
                $pl = (string) $pl;
                $en = (string) $en;
                if ($pl === '' || $en === '') { continue; }
                $values[] = '(%d, %s, %s, %s, %s, %s, %d, 0, %s, %s, %s)';
                array_push($args, $scope, self::hash($pl), $pl, $en, $kind, $origin, $shared ? 1 : 0, '', $now, $now);
            }
            if (!$values) { continue; }
            $sql = "INSERT IGNORE INTO {$t} (scope, hash, pl, en, kind, origin, shared, manual, model, created_at, updated_at) VALUES " . implode(',', $values);
            $n += (int) $wpdb->query($wpdb->prepare($sql, $args));
        }
        return $n;
    }

    /** Translations of the memory shown on every English page from now on (menu, footer, titles). */
    public static function mark_shared(array $hashes): void {
        global $wpdb;
        foreach (array_chunk(array_values(array_unique($hashes)), 300) as $chunk) {
            $wpdb->query($wpdb->prepare('UPDATE ' . self::table('strings') . ' SET shared = 1 WHERE scope = 0 AND shared = 0 AND hash IN (' . implode(',', array_fill(0, count($chunk), '%s')) . ')', $chunk));
        }
    }

    public static function string_edit(int $id, string $en): void {
        global $wpdb;
        $wpdb->update(self::table('strings'), ['en' => $en, 'manual' => 1, 'origin' => 'manual', 'updated_at' => self::now()], ['id' => $id]);
    }

    /** @return object|null */
    public static function string_row(int $id) {
        global $wpdb;
        $row = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . self::table('strings') . ' WHERE id = %d', $id));
        return $row ?: null;
    }

    public static function strings_delete(int $scope, array $hashes): void {
        global $wpdb;
        foreach (array_chunk(array_values(array_unique($hashes)), 300) as $chunk) {
            $wpdb->query($wpdb->prepare('DELETE FROM ' . self::table('strings') . ' WHERE scope = %d AND manual = 0 AND hash IN (' . implode(',', array_fill(0, count($chunk), '%s')) . ')', array_merge([$scope], $chunk)));
        }
    }

    /**
     * Translations whose Polish or English text contains $q (newest first); without $q the latest ones.
     * @return object[] rows with id, scope, pl, en, origin, shared, manual, model, updated_at
     */
    public static function find_strings(string $q, int $limit = 50, int $offset = 0): array {
        global $wpdb;
        $t = self::table('strings');
        $cols = 'id, scope, hash, pl, en, kind, origin, shared, manual, model, updated_at';
        if ($q === '') {
            $rows = $wpdb->get_results($wpdb->prepare("SELECT {$cols} FROM {$t} WHERE en <> '' ORDER BY updated_at DESC, id DESC LIMIT %d OFFSET %d", $limit, $offset));
        } else {
            $like = '%' . $wpdb->esc_like($q) . '%';
            $rows = $wpdb->get_results($wpdb->prepare("SELECT {$cols} FROM {$t} WHERE pl LIKE %s OR en LIKE %s ORDER BY scope ASC, updated_at DESC LIMIT %d OFFSET %d", $like, $like, $limit, $offset));
        }
        return is_array($rows) ? $rows : [];
    }

    public static function count_strings(array $where = []): int {
        global $wpdb;
        $parts = [];
        if (isset($where['scope'])) { $parts[] = 'scope ' . ((int) $where['scope'] === 0 ? '= 0' : '> 0'); }
        if (isset($where['origin'])) { $parts[] = $wpdb->prepare('origin = %s', (string) $where['origin']); }
        if (!empty($where['shared'])) { $parts[] = 'shared = 1'; }
        return (int) $wpdb->get_var('SELECT COUNT(*) FROM ' . self::table('strings') . ($parts ? ' WHERE ' . implode(' AND ', $parts) : ''));
    }

    // ------------------------------------------------------------------- maps

    /** @return array{map:array,keys:array,rev:int}|null */
    public static function map_row(int $page_id): ?array {
        global $wpdb;
        $row = $wpdb->get_row($wpdb->prepare('SELECT map, keys_list, rev FROM ' . self::table('maps') . ' WHERE page_id = %d', $page_id), ARRAY_A);
        if (!$row) { return null; }
        $map = json_decode((string) $row['map'], true);
        $keys = json_decode((string) $row['keys_list'], true);
        return ['map' => is_array($map) ? $map : [], 'keys' => is_array($keys) ? $keys : [], 'rev' => (int) $row['rev']];
    }

    /** Only the compiled map (serving). */
    public static function map_only(int $page_id): ?array {
        global $wpdb;
        $raw = $wpdb->get_var($wpdb->prepare('SELECT map FROM ' . self::table('maps') . ' WHERE page_id = %d', $page_id));
        if (!is_string($raw)) { return null; }
        $map = json_decode($raw, true);
        return is_array($map) ? $map : null;
    }

    public static function keys_of(int $page_id): array {
        global $wpdb;
        $raw = $wpdb->get_var($wpdb->prepare('SELECT keys_list FROM ' . self::table('maps') . ' WHERE page_id = %d', $page_id));
        $keys = is_string($raw) ? json_decode($raw, true) : null;
        return is_array($keys) ? $keys : [];
    }

    /** @param array<string,string> $map  @param array<string,string> $keys hash => kind */
    public static function map_save(int $page_id, string $path_hash, array $map, ?array $keys = null): int {
        global $wpdb;
        $t = self::table('maps');
        $flags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;
        $json = wp_json_encode((object) $map, $flags);
        $rev = (int) (sprintf('%u', crc32((string) $json)) % 4000000000);
        $exists = $wpdb->get_var($wpdb->prepare("SELECT page_id FROM {$t} WHERE page_id = %d", $page_id));
        $data = ['path_hash' => $path_hash, 'rev' => $rev, 'map' => (string) $json, 'updated_at' => self::now()];
        if ($keys !== null) { $data['keys_list'] = (string) wp_json_encode((object) $keys, $flags); }
        if ($exists !== null) {
            $wpdb->update($t, $data, ['page_id' => $page_id]);
        } else {
            $data['page_id'] = $page_id;
            if (!isset($data['keys_list'])) { $data['keys_list'] = '{}'; }
            $wpdb->insert($t, $data);
        }
        return $rev;
    }

    /** Remember which texts the Polish page has now (hash => kind); the served map stays until it is compiled. */
    public static function keys_save(int $page_id, string $path_hash, array $keys): void {
        global $wpdb;
        $t = self::table('maps');
        $json = (string) wp_json_encode((object) $keys, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $exists = $wpdb->get_var($wpdb->prepare("SELECT page_id FROM {$t} WHERE page_id = %d", $page_id));
        if ($exists !== null) {
            $wpdb->update($t, ['keys_list' => $json, 'path_hash' => $path_hash, 'updated_at' => self::now()], ['page_id' => $page_id]);
        } else {
            $wpdb->insert($t, ['page_id' => $page_id, 'path_hash' => $path_hash, 'rev' => 0, 'map' => '{}', 'keys_list' => $json, 'updated_at' => self::now()]);
        }
    }

    /** 3.1.1: add keys (hash => kind) to a page's list, e.g. the short version of its title once its translation is known. */
    public static function keys_add(int $page_id, array $keys): void {
        $row = self::page($page_id);
        if (!$row || !$keys) { return; }
        self::keys_save($page_id, self::hash((string) $row->path), self::keys_of($page_id) + $keys);
    }

    /** Pages whose current Polish version uses one of these keys. @return int[] page ids */
    public static function pages_using(array $hashes): array {
        global $wpdb;
        $want = array_flip($hashes);
        $out = [];
        $rows = $wpdb->get_results('SELECT page_id, keys_list FROM ' . self::table('maps') . ' WHERE page_id > 0', ARRAY_A);
        foreach ((array) $rows as $r) {
            $keys = json_decode((string) $r['keys_list'], true);
            if (!is_array($keys)) { continue; }
            if (array_intersect_key($keys, $want)) { $out[] = (int) $r['page_id']; }
        }
        return $out;
    }

    // ------------------------------------------------------------------- jobs

    /** A request to OpenAI (state new), or a list of texts a page waits for (kind and state "wait"). */
    public static function job_add(int $page_id, string $kind, array $items, int $chars, string $state = 'new'): int {
        global $wpdb;
        $now = self::now();
        $wpdb->insert(self::table('jobs'), ['page_id' => $page_id, 'kind' => $kind, 'state' => $state, 'items' => (string) wp_json_encode($items, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), 'chars' => $chars, 'created_at' => $now, 'updated_at' => $now]);
        return (int) $wpdb->insert_id;
    }

    /** Polish texts in requests not answered yet, of any page: hash => page id. */
    public static function pending_texts(): array {
        global $wpdb;
        $out = [];
        $rows = $wpdb->get_results('SELECT page_id, items FROM ' . self::table('jobs') . " WHERE kind = 'units' AND state IN ('new','sent','unknown')", ARRAY_A);
        foreach ((array) $rows as $r) {
            $p = json_decode((string) $r['items'], true);
            foreach (array_keys(is_array($p['items'] ?? null) ? $p['items'] : []) as $k) { $out[self::hash((string) $k)] = (int) $r['page_id']; }
        }
        return $out;
    }

    public static function job_update(int $id, array $data): void {
        global $wpdb;
        $data['updated_at'] = self::now();
        $wpdb->update(self::table('jobs'), $data, ['id' => $id]);
    }

    /** @return object[] */
    public static function jobs(array $states, int $limit = 50, int $page_id = 0): array {
        global $wpdb;
        $states = array_values($states);
        $sql = 'SELECT * FROM ' . self::table('jobs') . ' WHERE state IN (' . implode(',', array_fill(0, count($states), '%s')) . ')' . ($page_id ? ' AND page_id = ' . (int) $page_id : '') . ' ORDER BY id ASC LIMIT ' . (int) $limit;
        $rows = $wpdb->get_results($wpdb->prepare($sql, $states));
        return is_array($rows) ? $rows : [];
    }

    public static function count_jobs(array $states, int $page_id = 0): int {
        global $wpdb;
        $states = array_values($states);
        $sql = 'SELECT COUNT(*) FROM ' . self::table('jobs') . ' WHERE state IN (' . implode(',', array_fill(0, count($states), '%s')) . ')' . ($page_id ? ' AND page_id = ' . (int) $page_id : '');
        return (int) $wpdb->get_var($wpdb->prepare($sql, $states));
    }

    /** Keep the jobs table small: finished requests older than 30 days go. */
    public static function prune_jobs(): void {
        global $wpdb;
        $wpdb->query($wpdb->prepare('DELETE FROM ' . self::table('jobs') . " WHERE state IN ('done','dropped') AND updated_at < %s", gmdate('Y-m-d H:i:s', time() - 30 * DAY_IN_SECONDS)));
    }

    public static function drop_all(): void {
        global $wpdb;
        foreach (['jobs', 'maps', 'strings', 'pages'] as $t) { $wpdb->query('DROP TABLE IF EXISTS ' . self::table($t)); }
        delete_option(self::DB_OPTION);
    }
}
