<?php
namespace ZPTE;

if (!defined('ABSPATH')) { exit; }

/**
 * Database tables:
 *  zpte_paths   — one row per Polish page the module looks after (new post, older post, existing English page);
 *  zpte_strings — translation memory: Polish fragment (exactly as the language module keys it) => English;
 *  zpte_links   — which fragments a page uses. A page gets its own fragments plus the global ones
 *                 (post titles/excerpts shown in lists, fragments shared by several pages, e.g. a new footer line).
 */
final class Store {
    const DB_VERSION = '1';

    public static function table(string $name): string {
        global $wpdb;
        return $wpdb->prefix . 'zpte_' . $name;
    }

    public static function now(): string { return current_time('mysql', true); }

    public static function install(): void {
        if (get_option('zpte_db') === self::DB_VERSION) { return; }
        global $wpdb;
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        $c = $wpdb->get_charset_collate();
        $paths = self::table('paths');
        $strings = self::table('strings');
        $links = self::table('links');
        dbDelta("CREATE TABLE {$paths} (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  path_hash char(32) NOT NULL,
  path varchar(1000) NOT NULL DEFAULT '',
  post_id bigint(20) unsigned NOT NULL DEFAULT 0,
  kind varchar(12) NOT NULL DEFAULT 'new',
  status varchar(12) NOT NULL DEFAULT 'queued',
  queued tinyint(1) NOT NULL DEFAULT 0,
  priority smallint(5) NOT NULL DEFAULT 50,
  en_path varchar(1000) NOT NULL DEFAULT '',
  route_added tinyint(1) NOT NULL DEFAULT 0,
  keys_total int(11) NOT NULL DEFAULT 0,
  keys_ai int(11) NOT NULL DEFAULT 0,
  keys_failed int(11) NOT NULL DEFAULT 0,
  tries smallint(5) NOT NULL DEFAULT 0,
  source_modified datetime NULL DEFAULT NULL,
  checked_at datetime NULL DEFAULT NULL,
  translated_at datetime NULL DEFAULT NULL,
  message text NULL,
  created_at datetime NOT NULL,
  PRIMARY KEY  (id),
  UNIQUE KEY path_hash (path_hash),
  KEY post_id (post_id),
  KEY queued (queued,priority)
) {$c};");
        dbDelta("CREATE TABLE {$strings} (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  hash char(32) NOT NULL,
  pl longtext NOT NULL,
  en longtext NOT NULL,
  kind varchar(10) NOT NULL DEFAULT 'text',
  pinned tinyint(1) NOT NULL DEFAULT 0,
  is_global tinyint(1) NOT NULL DEFAULT 0,
  manual tinyint(1) NOT NULL DEFAULT 0,
  model varchar(60) NOT NULL DEFAULT '',
  created_at datetime NOT NULL,
  updated_at datetime NOT NULL,
  PRIMARY KEY  (id),
  UNIQUE KEY hash (hash),
  KEY is_global (is_global)
) {$c};");
        dbDelta("CREATE TABLE {$links} (
  path_id bigint(20) unsigned NOT NULL,
  string_id bigint(20) unsigned NOT NULL,
  PRIMARY KEY  (path_id,string_id),
  KEY string_id (string_id)
) {$c};");
        update_option('zpte_db', self::DB_VERSION, true);
    }

    public static function hash(string $s): string { return md5($s); }

    // ------------------------------------------------------------------ paths

    /** @return object|null */
    public static function path(int $id) {
        global $wpdb;
        $row = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . self::table('paths') . ' WHERE id = %d', $id));
        return $row ?: null;
    }

    /** @return object|null */
    public static function path_by_path(string $path) {
        global $wpdb;
        $row = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . self::table('paths') . ' WHERE path_hash = %s', self::hash($path)));
        return $row ?: null;
    }

    /** @return object|null */
    public static function path_by_post(int $post_id) {
        global $wpdb;
        if ($post_id <= 0) { return null; }
        $row = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . self::table('paths') . ' WHERE post_id = %d ORDER BY id ASC LIMIT 1', $post_id));
        return $row ?: null;
    }

    /** Insert a page (or return the existing row). */
    public static function path_add(string $path, array $data = []): int {
        global $wpdb;
        $row = self::path_by_path($path);
        if ($row) { return (int) $row->id; }
        $data = array_merge(['kind' => 'new', 'status' => 'queued', 'queued' => 0, 'priority' => 50, 'post_id' => 0], $data);
        $data['path'] = $path;
        $data['path_hash'] = self::hash($path);
        $data['created_at'] = self::now();
        $ok = $wpdb->insert(self::table('paths'), $data);
        if (!$ok) {
            $row = self::path_by_path($path);
            return $row ? (int) $row->id : 0;
        }
        return (int) $wpdb->insert_id;
    }

    public static function path_update(int $id, array $data): void {
        global $wpdb;
        if (isset($data['path'])) { $data['path_hash'] = self::hash((string) $data['path']); }
        $wpdb->update(self::table('paths'), $data, ['id' => $id]);
    }

    public static function path_delete(int $id): void {
        global $wpdb;
        $wpdb->delete(self::table('links'), ['path_id' => $id]);
        $wpdb->delete(self::table('paths'), ['id' => $id]);
    }

    /** Put a page in the queue; a page already waiting keeps the more urgent priority. */
    public static function queue(int $id, int $priority): void {
        $row = self::path($id);
        if (!$row || $row->status === 'off') { return; }
        $prio = (int) $row->queued ? min((int) $row->priority, $priority) : $priority;
        self::path_update($id, ['queued' => 1, 'priority' => $prio]);
    }

    /** @return object|null */
    public static function next_queued() {
        global $wpdb;
        $row = $wpdb->get_row('SELECT * FROM ' . self::table('paths') . " WHERE queued = 1 AND status <> 'off' ORDER BY priority ASC, id ASC LIMIT 1");
        return $row ?: null;
    }

    public static function queued_count(): int {
        global $wpdb;
        return (int) $wpdb->get_var('SELECT COUNT(*) FROM ' . self::table('paths') . " WHERE queued = 1 AND status <> 'off'");
    }

    /** @return object[] */
    public static function paths(array $where = [], int $limit = 0, int $offset = 0, string $order = 'id DESC'): array {
        global $wpdb;
        [$sql, $args] = self::where($where);
        $q = 'SELECT * FROM ' . self::table('paths') . $sql . ' ORDER BY ' . $order . ($limit > 0 ? ' LIMIT ' . (int) $limit . ' OFFSET ' . (int) $offset : '');
        $rows = $args ? $wpdb->get_results($wpdb->prepare($q, $args)) : $wpdb->get_results($q);
        return is_array($rows) ? $rows : [];
    }

    public static function count_paths(array $where = []): int {
        global $wpdb;
        [$sql, $args] = self::where($where);
        $q = 'SELECT COUNT(*) FROM ' . self::table('paths') . $sql;
        return (int) ($args ? $wpdb->get_var($wpdb->prepare($q, $args)) : $wpdb->get_var($q));
    }

    /** ['kind' => 'new', 'status' => ['draft', 'error'], 'post' => true, 'ai' => true] */
    private static function where(array $where): array {
        $parts = [];
        $args = [];
        foreach (['kind', 'status'] as $col) {
            if (!isset($where[$col])) { continue; }
            $vals = (array) $where[$col];
            if (!$vals) { continue; }
            $parts[] = $col . ' IN (' . implode(',', array_fill(0, count($vals), '%s')) . ')';
            foreach ($vals as $v) { $args[] = (string) $v; }
        }
        if (!empty($where['attention'])) { $parts[] = "(status = 'error' OR keys_failed > 0 OR tries > 0)"; }
        if (!empty($where['post'])) { $parts[] = 'post_id > 0'; }
        if (!empty($where['ai'])) { $parts[] = 'keys_ai > 0'; }
        if (isset($where['queued'])) { $parts[] = 'queued = ' . ((int) $where['queued'] ? 1 : 0); }
        return [$parts ? ' WHERE ' . implode(' AND ', $parts) : '', $args];
    }

    // ---------------------------------------------------------------- strings

    /** @return array<string,object> hash => row */
    public static function strings_by_hash(array $hashes): array {
        global $wpdb;
        $out = [];
        foreach (array_chunk(array_values(array_unique($hashes)), 200) as $chunk) {
            $q = 'SELECT id, hash, pl, en, kind, manual FROM ' . self::table('strings') . ' WHERE hash IN (' . implode(',', array_fill(0, count($chunk), '%s')) . ')';
            foreach ((array) $wpdb->get_results($wpdb->prepare($q, $chunk)) as $r) { $out[$r->hash] = $r; }
        }
        return $out;
    }

    /** Save a machine translation; a manual correction of the same fragment is kept. Returns the row id. */
    public static function string_save(string $pl, string $en, string $kind, string $model): int {
        global $wpdb;
        $h = self::hash($pl);
        $t = self::table('strings');
        $row = $wpdb->get_row($wpdb->prepare("SELECT id, manual FROM {$t} WHERE hash = %s", $h));
        $now = self::now();
        if ($row) {
            if (!(int) $row->manual) { $wpdb->update($t, ['en' => $en, 'kind' => $kind, 'model' => $model, 'updated_at' => $now], ['id' => (int) $row->id]); }
            return (int) $row->id;
        }
        $ok = $wpdb->insert($t, ['hash' => $h, 'pl' => $pl, 'en' => $en, 'kind' => $kind, 'model' => substr($model, 0, 60), 'created_at' => $now, 'updated_at' => $now]);
        if ($ok) { return (int) $wpdb->insert_id; }
        $id = $wpdb->get_var($wpdb->prepare("SELECT id FROM {$t} WHERE hash = %s", $h));
        return (int) $id;
    }

    public static function string_edit(int $id, string $en): void {
        global $wpdb;
        $wpdb->update(self::table('strings'), ['en' => $en, 'manual' => 1, 'updated_at' => self::now()], ['id' => $id]);
    }

    public static function pin(array $ids): void {
        global $wpdb;
        foreach (array_chunk(array_values(array_unique(array_map('intval', $ids))), 200) as $chunk) {
            $wpdb->query('UPDATE ' . self::table('strings') . ' SET pinned = 1 WHERE id IN (' . implode(',', $chunk) . ')');
        }
    }

    /** Fragments of one page (for the correction screen). @return object[] */
    public static function strings_of(int $path_id, int $limit = 200, int $offset = 0): array {
        global $wpdb;
        $rows = $wpdb->get_results($wpdb->prepare(
            'SELECT s.* FROM ' . self::table('strings') . ' s INNER JOIN ' . self::table('links') . ' l ON l.string_id = s.id WHERE l.path_id = %d ORDER BY s.id ASC LIMIT %d OFFSET %d',
            $path_id, $limit, $offset
        ));
        return is_array($rows) ? $rows : [];
    }

    public static function count_strings_of(int $path_id): int {
        global $wpdb;
        return (int) $wpdb->get_var($wpdb->prepare('SELECT COUNT(*) FROM ' . self::table('links') . ' WHERE path_id = %d', $path_id));
    }

    /** Replace the fragments a page uses. */
    public static function links_replace(int $path_id, array $string_ids): void {
        global $wpdb;
        $t = self::table('links');
        $want = array_values(array_unique(array_filter(array_map('intval', $string_ids))));
        $have = array_map('intval', (array) $wpdb->get_col($wpdb->prepare("SELECT string_id FROM {$t} WHERE path_id = %d", $path_id)));
        $drop = array_diff($have, $want);
        foreach (array_chunk(array_values($drop), 200) as $chunk) {
            $wpdb->query($wpdb->prepare("DELETE FROM {$t} WHERE path_id = %d AND string_id IN (" . implode(',', $chunk) . ')', $path_id));
        }
        foreach (array_diff($want, $have) as $sid) {
            $wpdb->insert($t, ['path_id' => $path_id, 'string_id' => $sid]);
        }
    }

    /** Add fragments to a page without dropping the others (progress saved in the middle of a long page). */
    public static function links_add(int $path_id, array $string_ids): void {
        global $wpdb;
        $t = self::table('links');
        $have = array_map('intval', (array) $wpdb->get_col($wpdb->prepare("SELECT string_id FROM {$t} WHERE path_id = %d", $path_id)));
        foreach (array_diff(array_values(array_unique(array_filter(array_map('intval', $string_ids)))), $have) as $sid) {
            $wpdb->insert($t, ['path_id' => $path_id, 'string_id' => $sid]);
        }
    }

    /**
     * Translations served on one page: its own fragments (published pages; drafts too in preview)
     * plus global ones. @return array<string,string> Polish key => English
     */
    public static function map_for(string $path_hash, bool $drafts = false): array {
        global $wpdb;
        $s = self::table('strings');
        $l = self::table('links');
        $p = self::table('paths');
        $statuses = $drafts ? "'published','draft','error'" : "'published'";
        $rows = $wpdb->get_results($wpdb->prepare(
            "SELECT s.pl, s.en FROM {$s} s WHERE s.en <> '' AND (s.is_global = 1 OR s.id IN (SELECT l.string_id FROM {$l} l INNER JOIN {$p} p ON p.id = l.path_id WHERE p.path_hash = %s AND p.status IN ({$statuses})))",
            $path_hash
        ), ARRAY_N);
        $out = [];
        foreach ((array) $rows as $r) { $out[(string) $r[0]] = (string) $r[1]; }
        return $out;
    }

    /** A fragment is global when it is a pinned title/excerpt of a published page or used by 2+ published pages. */
    public static function recompute_globals(): void {
        global $wpdb;
        $s = self::table('strings');
        $l = self::table('links');
        $p = self::table('paths');
        $wpdb->query("UPDATE {$s} SET is_global = CASE
  WHEN (SELECT COUNT(DISTINCT l.path_id) FROM {$l} l INNER JOIN {$p} p ON p.id = l.path_id WHERE l.string_id = {$s}.id AND p.status = 'published') >= 2 THEN 1
  WHEN {$s}.pinned = 1 AND (SELECT COUNT(*) FROM {$l} l INNER JOIN {$p} p ON p.id = l.path_id WHERE l.string_id = {$s}.id AND p.status = 'published') >= 1 THEN 1
  ELSE 0 END");
    }

    public static function has_globals(): bool {
        global $wpdb;
        return (bool) $wpdb->get_var('SELECT id FROM ' . self::table('strings') . ' WHERE is_global = 1 LIMIT 1');
    }

    /** Hashes of published pages that have their own fragments. @return string[] */
    public static function published_hashes(): array {
        global $wpdb;
        return array_map('strval', (array) $wpdb->get_col('SELECT DISTINCT p.path_hash FROM ' . self::table('paths') . ' p INNER JOIN ' . self::table('links') . " l ON l.path_id = p.id WHERE p.status = 'published'"));
    }

    /** Fragments no page uses any more (left after edits). */
    public static function prune_orphans(): int {
        global $wpdb;
        $s = self::table('strings');
        $l = self::table('links');
        return (int) $wpdb->query("DELETE FROM {$s} WHERE manual = 0 AND id NOT IN (SELECT string_id FROM {$l})");
    }

    public static function drop_all(): void {
        global $wpdb;
        foreach (['links', 'strings', 'paths'] as $t) { $wpdb->query('DROP TABLE IF EXISTS ' . self::table($t)); }
        delete_option('zpte_db');
    }
}
