<?php
namespace ZPTE;

if (!defined('ABSPATH')) { exit; }

/**
 * Activity log, OpenAI usage (characters, tokens and cost per day) and the one alert shown in wp-admin.
 * Small options, never autoloaded. The earlier translator's options (zpte_log, zpte_usage) stay as they were.
 * 2.1.0: German and Spanish keep their own log, usage, limits and alert (zpte2_de_log…, see Lang::opt).
 */
final class Log {
    const OPTION = 'zpte2_log';
    const USAGE = 'zpte2_usage';
    const ALERT = 'zpte2_alert';
    const MAX = 300;

    public static function add(string $message, string $level = 'info'): void {
        $log = get_option(Lang::opt(self::OPTION), []);
        if (!is_array($log)) { $log = []; }
        array_unshift($log, ['t' => time(), 'l' => $level, 'm' => OpenAI::short($message)]);
        update_option(Lang::opt(self::OPTION), array_slice($log, 0, self::MAX), false);
    }

    public static function all(): array {
        $log = get_option(Lang::opt(self::OPTION), []);
        return is_array($log) ? $log : [];
    }

    // ------------------------------------------------------------------ usage

    private static function day(): string { return wp_date('Y-m-d'); }

    public static function usage(?string $day = null): array {
        $u = get_option(Lang::opt(self::USAGE), []);
        $d = $day ?? self::day();
        $row = is_array($u) && isset($u[$d]) && is_array($u[$d]) ? $u[$d] : [];
        return array_merge(['chars' => 0, 'requests' => 0, 'in' => 0, 'out' => 0, 'usd' => 0.0], $row);
    }

    /** One finished OpenAI request: Polish characters sent and tokens billed. */
    public static function count(int $chars, int $in, int $out, string $model): void {
        $u = get_option(Lang::opt(self::USAGE), []);
        if (!is_array($u)) { $u = []; }
        $d = self::day();
        $row = self::usage($d);
        $price = Settings::price($model);
        $row['chars'] += $chars;
        $row['requests'] += 1;
        $row['in'] += $in;
        $row['out'] += $out;
        $row['usd'] = round((float) $row['usd'] + ($in * $price['in'] + $out * $price['out']) / 1000000, 6);
        $u[$d] = $row;
        krsort($u);
        update_option(Lang::opt(self::USAGE), array_slice($u, 0, 70, true), false);
    }

    /** Usage summed over the current calendar month. */
    public static function month(): array {
        $u = get_option(Lang::opt(self::USAGE), []);
        $sum = ['chars' => 0, 'requests' => 0, 'in' => 0, 'out' => 0, 'usd' => 0.0];
        $prefix = wp_date('Y-m-');
        foreach (is_array($u) ? $u : [] as $d => $row) {
            if (strpos((string) $d, $prefix) !== 0 || !is_array($row)) { continue; }
            foreach ($sum as $k => $v) { $sum[$k] = $v + ($k === 'usd' ? (float) ($row[$k] ?? 0) : (int) ($row[$k] ?? 0)); }
        }
        return $sum;
    }

    /** '' when a request of $chars may go out now, otherwise why not ('day' or 'month'). */
    public static function limit_reached(int $chars = 0): string {
        $daily = (int) Settings::get('daily_chars');
        if ($daily > 0 && self::usage()['chars'] + $chars > $daily) { return 'day'; }
        $monthly = (float) Settings::get('monthly_usd');
        if ($monthly > 0 && self::month()['usd'] >= $monthly) { return 'month'; }
        return '';
    }

    // ------------------------------------------------------------------ alert

    /** A problem only the administrator can fix (key, credit, model): shown in wp-admin until it is fixed. */
    public static function alert(string $message): void {
        $message = OpenAI::short($message);
        $a = self::current_alert();
        if (!$a || $a['m'] !== $message) { self::add($message, 'error'); }
        update_option(Lang::opt(self::ALERT), ['t' => time(), 'm' => $message], false);
    }

    public static function clear_alert(): void { delete_option(Lang::opt(self::ALERT)); }

    public static function current_alert(): ?array {
        $a = get_option(Lang::opt(self::ALERT), null);
        return is_array($a) && !empty($a['m']) ? $a : null;
    }
}
