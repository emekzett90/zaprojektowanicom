<?php
namespace ZPL;

if (!defined('ABSPATH')) { exit; }

/** Strings seen on English pages without a translation (admin review list). */
final class Missing {
    const OPTION = 'zpl_missing';
    const MAX = 3000;

    public static function all(): array {
        $v = get_option(self::OPTION, []);
        return is_array($v) ? $v : [];
    }

    /** $keys: key => kind */
    public static function remember(array $keys, string $source): void {
        if (!$keys) { return; }
        $all = self::all();
        $changed = false;
        foreach ($keys as $k => $kind) {
            $k = (string) $k;
            if ($k === '' || strlen($k) > 4000 || !Html::human($k)) { continue; }
            if (Dict::get($k, 'en', $source) !== null) { continue; }
            $h = substr(md5($k), 0, 12);
            if (isset($all[$h])) {
                if (empty($all[$h]['pages']) || !in_array($source, $all[$h]['pages'], true)) { $all[$h]['pages'][] = $source; $all[$h]['pages'] = array_slice($all[$h]['pages'], -5); $changed = true; }
                continue;
            }
            if (count($all) >= self::MAX) { break; }
            $all[$h] = ['k' => $k, 'kind' => (string) $kind, 'pages' => [$source], 't' => time()];
            $changed = true;
        }
        if ($changed) { update_option(self::OPTION, $all, false); }
    }

    public static function forget(array $hashes): void {
        $all = self::all();
        foreach ($hashes as $h) { unset($all[$h]); }
        update_option(self::OPTION, $all, false);
    }

    /** Drop entries that now have a translation. */
    public static function prune(): int {
        $all = self::all();
        $n = count($all);
        foreach ($all as $h => $row) {
            $src = $row['pages'][0] ?? '/';
            if (Dict::get((string) $row['k'], 'en', $src) !== null) { unset($all[$h]); }
        }
        update_option(self::OPTION, $all, false);
        return $n - count($all);
    }
}
