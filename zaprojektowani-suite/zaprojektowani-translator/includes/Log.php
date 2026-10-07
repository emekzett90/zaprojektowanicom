<?php
namespace ZPTE;

if (!defined('ABSPATH')) { exit; }

/** Activity log, daily OpenAI usage and the alert shown in wp-admin (all small options, not autoloaded). */
final class Log {
    const OPTION = 'zpte_log';
    const USAGE = 'zpte_usage';
    const ALERT = 'zpte_alert';
    const MAX = 200;

    public static function add(string $message, string $level = 'info'): void {
        $log = get_option(self::OPTION, []);
        if (!is_array($log)) { $log = []; }
        array_unshift($log, ['t' => time(), 'l' => $level, 'm' => $message]);
        update_option(self::OPTION, array_slice($log, 0, self::MAX), false);
    }

    public static function all(): array {
        $log = get_option(self::OPTION, []);
        return is_array($log) ? $log : [];
    }

    // ------------------------------------------------------------------ usage

    private static function day(): string { return wp_date('Y-m-d'); }

    public static function usage(?string $day = null): array {
        $u = get_option(self::USAGE, []);
        $d = $day ?? self::day();
        $row = is_array($u) && isset($u[$d]) && is_array($u[$d]) ? $u[$d] : [];
        return array_merge(['chars' => 0, 'requests' => 0, 'in' => 0, 'out' => 0], $row);
    }

    public static function count(int $chars, int $in, int $out): void {
        $u = get_option(self::USAGE, []);
        if (!is_array($u)) { $u = []; }
        $d = self::day();
        $row = self::usage($d);
        $row['chars'] += $chars;
        $row['requests'] += 1;
        $row['in'] += $in;
        $row['out'] += $out;
        $u[$d] = $row;
        krsort($u);
        update_option(self::USAGE, array_slice($u, 0, 62, true), false);
    }

    /** Usage summed over the current month. */
    public static function month(): array {
        $u = get_option(self::USAGE, []);
        $sum = ['chars' => 0, 'requests' => 0, 'in' => 0, 'out' => 0];
        $prefix = wp_date('Y-m-');
        foreach (is_array($u) ? $u : [] as $d => $row) {
            if (strpos((string) $d, $prefix) !== 0 || !is_array($row)) { continue; }
            foreach ($sum as $k => $v) { $sum[$k] = $v + (int) ($row[$k] ?? 0); }
        }
        return $sum;
    }

    public static function can_send(int $chars): bool {
        $limit = (int) Settings::get('daily_chars');
        return $limit <= 0 || self::usage()['chars'] + $chars <= $limit;
    }

    public static function over_limit(): bool {
        $limit = (int) Settings::get('daily_chars');
        return $limit > 0 && self::usage()['chars'] >= $limit;
    }

    // ------------------------------------------------------------------ alert

    /** A problem only the administrator can fix (bad key, no credit): shown on every wp-admin screen until fixed. */
    public static function alert(string $message): void {
        update_option(self::ALERT, ['t' => time(), 'm' => $message], false);
        self::add($message, 'error');
    }

    public static function clear_alert(): void { delete_option(self::ALERT); }

    public static function current_alert(): ?array {
        $a = get_option(self::ALERT, null);
        return is_array($a) && !empty($a['m']) ? $a : null;
    }
}
