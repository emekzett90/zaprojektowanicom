<?php
/** Read-only presentation of existing Suite GA4/GSC reports. No hooks, transport, storage or jobs. */
if (!defined('ABSPATH')) { exit; }

final class ZP_Admin_Stats_Presentation_20261010 {
    private const GA = ['totalUsers'=>'Użytkownicy', 'screenPageViews'=>'Odsłony', 'sessions'=>'Sesje'];
    private const GSC = ['clicks'=>'Kliknięcia', 'impressions'=>'Wyświetlenia'];
    private const COLORS = ['totalUsers'=>'#1c477a', 'screenPageViews'=>'#6841a5', 'sessions'=>'#176c65', 'clicks'=>'#1c477a', 'impressions'=>'#6841a5'];
    private const MAX_DAYS = 62;

    private static function date($value): ?DateTimeImmutable {
        if (!is_string($value) || !preg_match('/^\d{4}-\d{2}-\d{2}$/D', $value)) { return null; }
        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value, new DateTimeZone('UTC'));
        return $date && $date->format('Y-m-d') === $value ? $date : null;
    }
    private static function count_value($value): ?int {
        if ((!is_int($value) && !is_float($value) && !is_string($value)) || !is_numeric($value)) { return null; }
        $n = (float) $value;
        return is_finite($n) && $n >= 0 && $n <= 1000000000000 && floor($n) === $n ? (int) $n : null;
    }
    private static function range(array $range): ?array {
        $start = self::date($range['start'] ?? null); $end = self::date($range['end'] ?? null);
        if (!$start || !$end || $end < $start || (int) $start->diff($end)->days >= self::MAX_DAYS) { return null; }
        return ['start'=>$start, 'end'=>$end];
    }
    private static function status(array $report): string {
        $status = $report['status'] ?? 'unavailable';
        return is_string($status) && in_array($status, ['ready','limited','awaiting_data','unavailable'], true) ? $status : 'unavailable';
    }
    /** Missing dates and invalid/duplicate values remain null. Dates follow the actual report bounds. */
    public static function series(array $rows, array $range, array $report, array $metrics): array {
        $bounds = self::range($range); $source = self::range($report);
        if (!$bounds) { return []; }
        $metrics = array_values(array_intersect($metrics, array_keys(self::GA + self::GSC)));
        $byDate = []; $duplicates = [];
        if ($source && in_array(self::status($report), ['ready','limited'], true)) {
            foreach (array_slice($rows, 0, 1000) as $row) {
                if (!is_array($row)) { continue; }
                $date = self::date($row['date'] ?? null);
                if (!$date || $date < $source['start'] || $date > $source['end'] || $date < $bounds['start'] || $date > $bounds['end']) { continue; }
                $key = $date->format('Y-m-d');
                if (isset($byDate[$key])) { $duplicates[$key] = true; continue; }
                $values = []; foreach ($metrics as $metric) { $values[$metric] = self::count_value($row[$metric] ?? null); }
                $byDate[$key] = $values;
            }
        }
        $days = [];
        for ($date = $bounds['start']; $date <= $bounds['end']; $date = $date->modify('+1 day')) {
            $key = $date->format('Y-m-d');
            $values = isset($byDate[$key]) && !isset($duplicates[$key]) ? $byDate[$key] : array_fill_keys($metrics, null);
            $days[] = ['date'=>$key, 'end'=>$key, 'days'=>1, 'reported_days'=>isset($byDate[$key]) && !isset($duplicates[$key]) ? 1 : 0, 'values'=>$values];
        }
        return $days;
    }
    /** Additive metrics only. A missing value in a visible week makes that weekly metric unknown. */
    public static function weekly(array $days): array {
        $weeks = [];
        foreach (array_slice($days, 0, self::MAX_DAYS) as $day) {
            $date = self::date($day['date'] ?? null); if (!$date || !is_array($day['values'] ?? null)) { continue; }
            $monday = $date->modify('-' . ((int) $date->format('N') - 1) . ' days')->format('Y-m-d');
            if (!isset($weeks[$monday])) { $weeks[$monday] = ['date'=>$day['date'],'end'=>$day['date'],'week_start'=>$monday,'days'=>0,'reported_days'=>0,'values'=>array_fill_keys(array_keys($day['values']), 0),'missing'=>[]]; }
            $w = &$weeks[$monday]; $w['end'] = $day['date']; $w['days']++; $w['reported_days'] += ($day['reported_days'] ?? 0) === 1 ? 1 : 0;
            foreach ($w['values'] as $metric => $sum) {
                if ($metric === 'totalUsers') { $w['values'][$metric] = null; continue; }
                $value = self::count_value($day['values'][$metric] ?? null);
                if ($value === null) { $w['missing'][$metric] = true; }
                elseif (!isset($w['missing'][$metric])) { $w['values'][$metric] += $value; }
            }
            unset($w);
        }
        foreach ($weeks as &$week) { foreach ($week['missing'] as $metric=>$unused) { $week['values'][$metric] = null; } unset($week['missing']); }
        unset($week); return array_values($weeks);
    }
    private static function number($value): string {
        $count = self::count_value($value);
        return $count === null ? '—' : (function_exists('number_format_i18n') ? number_format_i18n($count) : number_format($count, 0, ',', ' '));
    }
    private static function axis_number(float $value): string {
        $precision = floor($value) === $value ? 0 : 1;
        return function_exists('number_format_i18n') ? number_format_i18n($value, $precision) : number_format($value, $precision, ',', ' ');
    }
    private static function date_label(string $value): string { $date = self::date($value); return $date ? $date->format('d.m') : '—'; }
    private static function fetched($value): string {
        if (!is_int($value) && !(is_string($value) && ctype_digit($value))) { return 'brak daty pobrania'; }
        if ((int) $value <= 0) { return 'brak daty pobrania'; }
        return function_exists('wp_date') ? wp_date('d.m.Y H:i', (int) $value) : gmdate('d.m.Y H:i', (int) $value);
    }
    /** Pure model, source periods remain independent: GSC retains its actual final-data 28-day range. */
    public static function model(array $data, string $period = 'today', string $grain = 'day'): array {
        $period = in_array($period, ['today','7d','28d'], true) ? $period : 'today';
        $grain = $grain === 'week' ? 'week' : 'day';
        $ga = is_array($data['ga4'] ?? null) ? $data['ga4'] : [];
        $total = is_array($ga['periods'][$period] ?? null) ? $ga['periods'][$period] : [];
        $dailyReport = is_array($ga['dailyReport'] ?? null) ? $ga['dailyReport'] : [];
        $gaReport = $dailyReport; $gaRows = is_array($ga['daily'] ?? null) ? $ga['daily'] : [];
        if ($period === 'today') {
            $gaReport = $total; $gaRows = [];
            if (($total['start'] ?? null) === ($total['end'] ?? null) && self::range($total) && in_array(self::status($total), ['ready','limited'], true)) {
                $row = ['date'=>$total['start']];
                foreach (self::GA as $metric=>$label) { $row[$metric] = $total[$metric] ?? ($total['metrics'][$metric] ?? null); }
                $gaRows[] = $row;
            }
        }
        $gaDays = self::series($gaRows, $total, $gaReport, array_keys(self::GA));
        $gsc = is_array($data['gsc'] ?? null) ? $data['gsc'] : [];
        $gscReport = is_array($gsc['dailyReport'] ?? null) ? $gsc['dailyReport'] : [];
        $gscRange = self::range($gscReport) ? $gscReport : $gsc;
        $gscDays = self::series(is_array($gsc['daily'] ?? null) ? $gsc['daily'] : [], $gscRange, $gscReport, array_keys(self::GSC));
        $rangeLabel = static function(array $rows): ?array { return $rows ? ['start'=>$rows[0]['date'],'end'=>$rows[count($rows)-1]['end']] : null; };
        return [
            'period'=>$period,'grain'=>$grain,
            'ga4'=>['daily'=>$gaDays,'points'=>$grain==='week'?self::weekly($gaDays):$gaDays,'range'=>$rangeLabel($gaDays),'report'=>$gaReport,'timezone'=>is_string($gaReport['timezone']??null)?$gaReport['timezone']:(is_string($ga['timezone']??null)?$ga['timezone']:null),'today_point'=>$period==='today'],
            'gsc'=>['daily'=>$gscDays,'points'=>$grain==='week'?self::weekly($gscDays):$gscDays,'range'=>$rangeLabel($gscDays),'report'=>$gscReport,'timezone'=>'America/Los_Angeles','data_through'=>self::date($gsc['data_through']??null)?$gsc['data_through']:null],
        ];
    }
    /** Numeric-only SVG paths. Split at every unknown point; zero is a visible valid measurement. */
    public static function geometry(array $points, string $metric): array {
        if (!array_key_exists($metric, self::GA + self::GSC) || count($points) > self::MAX_DAYS) { return ['segments'=>[],'points'=>[],'max'=>1]; }
        $values = []; foreach ($points as $point) { $values[] = self::count_value(is_array($point['values']??null)?($point['values'][$metric]??null):null); }
        $max = max(array_merge([1], array_map(static fn($value)=>$value ?? 0, $values))); $segments=[];$segment=[];$dots=[];$n=count($points);
        foreach ($values as $i=>$value) {
            if ($value === null) { if ($segment) { $segments[]=$segment; $segment=[]; } continue; }
            $x = $n === 1 ? 470.0 : 60.0 + 820.0 * $i / ($n - 1); $y = 200.0 - 165.0 * $value / $max;
            $xy = number_format($x, 2, '.', '') . ',' . number_format($y, 2, '.', '');
            $segment[]=$xy; $dots[]=['x'=>$x,'y'=>$y,'value'=>$value,'index'=>$i];
        }
        if ($segment) { $segments[]=$segment; }
        return ['segments'=>$segments,'points'=>$dots,'max'=>$max];
    }
    private static function query_choice(string $name, array $allowed, string $default): string {
        $value = $_GET[$name] ?? null;
        return is_string($value) && in_array($value, $allowed, true) ? $value : $default;
    }
    private static function url(string $period, string $grain, string $gaMetric, string $gscMetric): string {
        $base = function_exists('zp_admin20_url') ? zp_admin20_url('zp-suite-stats') : admin_url('admin.php?page=zp-suite-stats');
        $args = ['zp_period'=>$period,'zp_trend_grain'=>$grain,'zp_trend_ga'=>$gaMetric,'zp_trend_gsc'=>$gscMetric];
        if (isset($_GET['zp_query']) && is_string($_GET['zp_query']) && mb_strlen($_GET['zp_query'])<=200) { $args['zp_query'] = wp_unslash($_GET['zp_query']); }
        if (function_exists('zp_suite_workspace_active') && zp_suite_workspace_active()) { $args['zp_workspace']='1'; }
        return add_query_arg($args, $base) . '#zp-stats-trends';
    }
    private static function freshness(array $report): string {
        $parts=[];
        if (self::status($report)==='limited' || !empty($report['limited'])) { $parts[]='Ograniczony raport Google — pokazujemy tylko otrzymane dane.'; }
        if (!empty($report['stale'])) { $parts[]='Poprzedni pomiar: dane wymagają odświeżenia.'; }
        $reasons=['range_mismatch'=>'Zakres raportu różni się od bieżącego okresu.','refresh_error'=>'Ostatnia aktualizacja nie powiodła się.','connection_unavailable'=>'Sprawdź połączenie Google.','age_exceeded'=>'Pomiar jest starszy niż termin aktualizacji.','missing_fetch_time'=>'Brak daty pobrania.','clock_mismatch'=>'Sprawdź datę pobrania.'];
        foreach (array_slice((array)($report['stale_reasons']??[]),0,6) as $reason) { if (is_string($reason) && isset($reasons[$reason])) { $parts[]=$reasons[$reason]; } }
        return implode(' ',array_unique($parts));
    }
    private static function chart(array $source, string $metric, string $label, string $grain): void {
        $points=$source['points'];$plot=self::geometry($points,$metric);$color=self::COLORS[$metric];
        if (!$plot['points']) {
            $empty = self::status($source['report'])==='awaiting_data' ? 'Google nie udostępnił jeszcze pomiaru. Brak danych nie oznacza zera.' : 'Brak pomiarów dla tego widoku. Nie zastępujemy brakujących dni zerami.';
            echo '<div class="zpAdmin20__empty">'.esc_html($empty).'</div>'; return;
        }
        $range=$source['range'];$description=$label.' · '.($grain==='week'?'tygodniowo':'dzień po dniu').' · '.$range['start'].' — '.$range['end'];
        echo '<svg class="zpAdmin20__chart" viewBox="0 0 920 250" role="img" aria-label="'.esc_attr($description).'" style="max-height:300px"><title>'.esc_html($description).'</title><desc>Przerwa na linii oznacza brak pomiaru. Szczegółowe wartości są w tabeli poniżej.</desc>';
        foreach ($plot['max'] === 1 ? [0.0,1.0] : [0.0,0.5,1.0] as $scale) { $y=200-165*$scale;echo '<line x1="60" y1="'.esc_attr((string)$y).'" x2="880" y2="'.esc_attr((string)$y).'" stroke="#e4e9f0"/><text x="50" y="'.esc_attr((string)($y+4)).'" text-anchor="end" fill="#5f6d80" font-size="11">'.esc_html(self::axis_number($plot['max']*$scale)).'</text>'; }
        foreach ($plot['segments'] as $segment) { echo '<polyline fill="none" stroke="'.esc_attr($color).'" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" points="'.esc_attr(implode(' ',$segment)).'"/>'; }
        foreach ($plot['points'] as $dot) {
            $row=$points[$dot['index']];$period=$row['date'].($row['end']!==$row['date']?' — '.$row['end']:'');
            echo '<circle cx="'.esc_attr(number_format($dot['x'],2,'.','')).'" cy="'.esc_attr(number_format($dot['y'],2,'.','')).'" r="4" fill="'.esc_attr($color).'"><title>'.esc_html($period.': '.self::number($dot['value']).' · '.$label).'</title></circle>';
        }
        $ticks=array_values(array_unique([0,(int)floor((count($points)-1)/2),count($points)-1]));
        foreach ($ticks as $i) { $x=count($points)===1?470:60+820*$i/(count($points)-1);echo '<text x="'.esc_attr(number_format($x,2,'.','')).'" y="232" text-anchor="middle" fill="#5f6d80" font-size="11">'.esc_html(self::date_label($points[$i]['date'])).'</text>'; }
        echo '</svg><p class="zpAdmin20__muted"><span style="color:'.esc_attr($color).'">●</span> '.esc_html($label).' · przerwy = brak danych; zero = otrzymany pomiar równy 0.</p>';
    }
    private static function table(array $source, array $metrics, string $grain, string $summary): void {
        echo '<details class="zpAdmin20__details"><summary>'.esc_html($summary).'</summary><div class="zpAdmin20__tableWrap"><table class="zpAdmin20__table"><caption class="screen-reader-text">'.esc_html($summary).'</caption><thead><tr><th scope="col">'.($grain==='week'?'Dni objęte tygodniem':'Data').'</th>';
        foreach ($metrics as $metric=>$label) { echo '<th scope="col">'.esc_html($label).'</th>'; }
        echo '<th scope="col">Dni z raportu</th></tr></thead><tbody>';
        foreach (array_reverse($source['points']) as $row) {
            echo '<tr><th scope="row">'.esc_html($row['date'].($row['end']!==$row['date']?' — '.$row['end']:'')).'</th>';
            foreach ($metrics as $metric=>$label) { echo '<td>'.esc_html(self::number($row['values'][$metric]??null)).'</td>'; }
            echo '<td>'.esc_html($row['reported_days'].' / '.$row['days']).'</td></tr>';
        }
        if (!$source['points']) { echo '<tr><td colspan="'.esc_attr((string)(count($metrics)+2)).'">Brak zakresu raportu.</td></tr>'; }
        echo '</tbody></table></div></details>';
    }
    /** Caller supplies existing cached data; rendering never performs an API request or schedules a job. */
    public static function render(array $data, string $period='today'): void {
        if (!current_user_can('manage_options')) { return; }
        $period=in_array($period,['today','7d','28d'],true)?$period:'today';
        $grain=self::query_choice('zp_trend_grain',['day','week'],'day');
        $gaMetric=self::query_choice('zp_trend_ga',array_keys(self::GA),'screenPageViews');
        if ($grain==='week' && $gaMetric==='totalUsers') { $gaMetric='screenPageViews'; }
        $gscMetric=self::query_choice('zp_trend_gsc',array_keys(self::GSC),'clicks');
        $model=self::model($data,$period,$grain);
        echo '<div id="zp-stats-trends" class="zpAdmin20__stack"><section class="zpAdmin20__card"><h2>Jak zmienia się ruch?</h2><p class="zpAdmin20__muted">Wybierz jedną miarę, aby porównać dni lub tygodnie. Każdy wykres ma własną skalę i własny zakres źródłowego raportu.</p><nav class="zpAdmin20__tabs" aria-label="Szczegółowość wykresów">';
        foreach (['day'=>'Dzień po dniu','week'=>'Tygodniami'] as $key=>$label) { echo '<a href="'.esc_url(self::url($period,$key,$gaMetric,$gscMetric)).'"'.($grain===$key?' class="is-active" aria-current="page"':'').'>'.esc_html($label).'</a>'; }
        echo '</nav><p class="zpAdmin20__muted">Użytkownicy to odrębni użytkownicy raportowani przez GA4; sesja to wizyta, a odsłona to otwarcie strony. Kliknięcie i wyświetlenie w Google opisują wyniki wyszukiwania — nie są liczbą osób na stronie.</p></section>';
        $ga=$model['ga4'];
        echo '<section class="zpAdmin20__card"><div class="zpAdmin20__sectionTitle"><div><h2>Ruch na stronie</h2><p class="zpAdmin20__muted">Google Analytics 4 · '.esc_html($ga['range']?$ga['range']['start'].' — '.$ga['range']['end']:'brak zakresu').'</p></div><span class="zpAdmin20__pill">GA4</span></div><nav class="zpAdmin20__tabs" aria-label="Miara ruchu na stronie">';
        foreach (self::GA as $key=>$label) { $metricGrain=$key==='totalUsers'?'day':$grain;echo '<a href="'.esc_url(self::url($period,$metricGrain,$key,$gscMetric)).'"'.($gaMetric===$key?' class="is-active" aria-current="page"':'').'>'.esc_html($label.($grain==='week'&&$key==='totalUsers'?' · dni':'')).'</a>'; }
        echo '</nav><p class="zpAdmin20__muted">Pobrano '.esc_html(self::fetched($ga['report']['fetched_at']??null)).($ga['timezone']?' · strefa raportu: '.esc_html($ga['timezone']):'').'. '.esc_html(self::freshness($ga['report'])).'</p>';
        if ($ga['today_point']) { echo '<p class="zpAdmin20__muted">Dziś: jeden częściowy pomiar dnia z raportu GA4. Nie mamy rozkładu godzinowego; dzisiejszy wynik może jeszcze wzrosnąć.</p>'; }
        else { echo '<p class="zpAdmin20__muted">To pobrany raport dzienny; może być starszy od kart z podsumowaniem okresu. Brakujący dzień pozostaje przerwą na wykresie.</p>'; }
        self::chart($ga,$gaMetric,self::GA[$gaMetric],$grain);self::table($ga,self::GA,$grain,'Wartości GA4 w tabeli');
        if ($grain==='week') { echo '<p class="zpAdmin20__muted">Sesje i odsłony sumujemy tylko dla dni z kompletnymi pomiarami w widocznym tygodniu. Przy brakującym pomiarze suma pozostaje nieznana. Pierwszy lub ostatni tydzień może obejmować część tygodnia. Unikalnych użytkowników nie sumujemy z dni — liczba dla całego wybranego okresu pozostaje w karcie „Użytkownicy” nad wykresami.</p>'; }
        echo '</section>';
        $gsc=$model['gsc'];
        echo '<section class="zpAdmin20__card"><div class="zpAdmin20__sectionTitle"><div><h2>Widoczność i wejścia z Google</h2><p class="zpAdmin20__muted">Search Console · '.esc_html($gsc['range']?$gsc['range']['start'].' — '.$gsc['range']['end']:'brak zakresu').'</p></div><span class="zpAdmin20__pill">GSC</span></div><nav class="zpAdmin20__tabs" aria-label="Miara wyników wyszukiwania">';
        foreach (self::GSC as $key=>$label) { echo '<a href="'.esc_url(self::url($period,$grain,$gaMetric,$key)).'"'.($gscMetric===$key?' class="is-active" aria-current="page"':'').'>'.esc_html($label).'</a>'; }
        echo '</nav><p class="zpAdmin20__muted">Ostatni dzień zwrócony przez Google: '.esc_html($gsc['data_through']??'brak danych').'. Pobrano '.esc_html(self::fetched($gsc['report']['fetched_at']??null)).'. '.esc_html(self::freshness($gsc['report'])).'</p><p class="zpAdmin20__muted">Search Console ma osobny, zakończony zakres około 28 dni i opóźnienie danych. Wybór „Dziś / 7 dni / 28 dni” nad kartami dotyczy GA4. Dni Google są liczone w strefie America/Los_Angeles.</p>';
        self::chart($gsc,$gscMetric,self::GSC[$gscMetric],$grain);self::table($gsc,self::GSC,$grain,'Wartości Search Console w tabeli');
        if ($grain==='week') { echo '<p class="zpAdmin20__muted">Tydzień obejmuje tylko daty widoczne w zakresie raportu. Brak pomiaru pozostaje kreską; częściowych tygodni nie porównujemy z pełnymi jak równych okresów.</p>'; }
        echo '</section><p class="zpAdmin20__muted">Wykresy korzystają z istniejących raportów. Nie pokazują zapytań konkretnego użytkownika, treści rozmów z AI ani cytowań przez asystentów.</p></div>';
    }
}
function zp_admin_stats_trends_render(array $data, string $period='today'): void { ZP_Admin_Stats_Presentation_20261010::render($data,$period); }
