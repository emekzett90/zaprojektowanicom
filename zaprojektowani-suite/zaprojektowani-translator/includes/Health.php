<?php
namespace ZPTE;

if (!defined('ABSPATH')) { exit; }

/**
 * What the ZP Suite panel shows about Tłumacz EN: the page to open, a short summary and the checks on
 * the "Stan strony" screen. Everything here reads small options and counts; nothing calls OpenAI.
 * 2.1.0: one more line for each of the German and Spanish versions.
 */
final class Health {
    public static function boot(): void {
        add_filter('zp_suite3_translator_page', [self::class, 'page']);
        add_filter('zp_suite3_translator_summary', [self::class, 'summary']);
        add_filter('zp_suite3_health_checks', [self::class, 'checks']);
    }

    /** @param mixed $page */
    public static function page($page = '') { return Admin::SLUG; }

    /**
     * @param mixed $summary
     * @return array{done:int,total:int,pending:int,problems:int,last_run:int,next_run:int}
     */
    public static function summary($summary = []): array {
        $s = Worker::state();
        $out = ['done' => Index::count(), 'total' => 0, 'pending' => 0, 'problems' => 0,
            'last_run' => (int) $s['daily_at'], 'next_run' => (int) (wp_next_scheduled(Worker::DAILY) ?: 0)];
        if (Store::ready()) {
            $out['total'] = Store::count_pages(['status' => ['new', 'done', 'partial', 'error']]);
            $out['pending'] = Store::queued_count();
            $out['problems'] = Store::count_pages(['attention' => true, 'status' => ['new', 'done', 'partial', 'error']]);
        }
        return $out;
    }

    /**
     * @param mixed $checks list of ['id','title','state' => ok|warn|bad|info,'text','url']
     * @return array<int,array<string,string>>
     */
    public static function checks($checks = []): array {
        $checks = is_array($checks) ? $checks : [];
        $url = Admin::url();
        $add = static function (string $id, string $title, string $state, string $text, string $link = '') use (&$checks, $url) {
            $checks[] = ['id' => $id, 'title' => $title, 'state' => $state, 'text' => $text, 'url' => $link !== '' ? $link : $url];
        };
        if (!Bridge::ready()) {
            $add('zpte-module', 'Tłumacz EN', 'bad', 'Brak modułu Języki PL/EN, więc strony angielskie nie działają.');
            return $checks;
        }
        $s = Worker::state();

        // OpenAI key and pauses.
        if (Settings::api_key() === '') {
            $add('zpte-key', 'Tłumacz EN: klucz OpenAI', 'bad', Settings::key_unreadable()
                ? 'Zapisanego klucza nie da się odczytać (zmieniły się klucze bezpieczeństwa WordPressa). Wklej go ponownie.'
                : 'Brak klucza OpenAI: nowe i zmienione teksty nie dostają tłumaczenia.', Admin::url(['tab' => 'settings']));
        } elseif ($a = Log::current_alert()) {
            $add('zpte-key', 'Tłumacz EN: OpenAI', 'bad', 'Wstrzymany: ' . (string) $a['m'], Admin::url(['tab' => 'settings']));
        } else {
            $add('zpte-key', 'Tłumacz EN: OpenAI', 'ok', 'Klucz zapisany, model ' . Settings::model() . '.');
        }

        // Daily check at 03:00.
        if (!Settings::get('enabled')) {
            $add('zpte-daily', 'Tłumacz EN: codzienne sprawdzenie', 'warn', 'Wyłączone w ustawieniach: nowe wpisy nie dostaną wersji angielskiej same.', Admin::url(['tab' => 'settings']));
        } else {
            $last = (int) $s['daily_at'];
            $next = (int) (wp_next_scheduled(Worker::DAILY) ?: 0);
            if ($last > 0 && $last < time() - 26 * HOUR_IN_SECONDS) {
                $add('zpte-daily', 'Tłumacz EN: codzienne sprawdzenie', 'bad', 'Ostatnie sprawdzenie: ' . Admin::day_time($last) . '. Nocne zadania nie ruszają; ustaw w hostingu cron z adresem z Ustawień tłumacza.', Admin::url(['tab' => 'settings']));
            } else {
                $add('zpte-daily', 'Tłumacz EN: codzienne sprawdzenie', 'ok', ($last ? 'Ostatnie: ' . Admin::day_time($last) . '. ' : '') . 'Następne: ' . Admin::day_time($next ?: Worker::next_daily()) . '.');
            }
        }

        // Copy of the earlier translations.
        if (!Import::done()) {
            $add('zpte-import', 'Tłumacz EN: przeniesienie tłumaczeń', 'info', 'Przenoszę obecne tłumaczenia i adresy EN (bez kosztów OpenAI).');
        }

        // English addresses made of Polish words.
        $polish = 0;
        foreach (\ZPL\Router::routes() as $pl => $en) {
            if (is_string($en) && strpos($en, '/en/') === 0 && Routes::polish($en)) { $polish++; }
        }
        $add('zpte-routes', 'Adresy stron EN', $polish ? 'warn' : 'ok', $polish
            ? $polish . ' ' . Admin::pages_word($polish) . ' EN ma jeszcze polskie słowa w adresie; tłumacz nada angielskie adresy z przekierowaniem 301.'
            : 'Wszystkie adresy stron EN są po angielsku.', Admin::url(['tab' => 'pages']));

        // Pages to look at, queue and missing pages.
        if (Store::ready()) {
            $problems = Store::count_pages(['attention' => true, 'status' => ['new', 'done', 'partial', 'error']]);
            $add('zpte-pages', 'Strony EN', $problems ? 'warn' : 'ok', $problems
                ? $problems . ' ' . Admin::pages_word($problems) . ' z brakującymi fragmentami lub błędem. Wersji EN: ' . Index::count() . '.'
                : 'Wersji EN: ' . Index::count() . ', bez błędów.', Admin::url(['tab' => 'pages', 'f' => 'attention']));
            $queued = Store::queued_count();
            if ($queued) {
                $add('zpte-queue', 'Tłumacz EN: kolejka', 'info', 'W kolejce ' . $queued . ' ' . Admin::pages_word($queued) . '. ' . (string) $s['message']);
            }
        }
        $missing = count(Admin::missing_pages());
        if ($missing) {
            $add('zpte-missing', 'Wpisy bez wersji EN', Import::done() ? 'warn' : 'info', $missing . ' ' . Admin::pages_word($missing) . ' bez wersji angielskiej: goście z zagranicy widzą je po polsku. Tłumacz zajmie się nimi przy najbliższym przebiegu.');
        }
        foreach (Lang::AI as $l) {
            Lang::with($l, static function () use ($add) {
                $id = 'zpte-' . Lang::code();
                $title = 'Wersja ' . Lang::CODE[Lang::code()];
                if ((int) Settings::get('on') !== 1) {
                    $add($id, $title, 'info', 'Wyłączona w ustawieniach tłumacza.', Admin::url(['tab' => 'settings']));
                    return;
                }
                if (!Store::ready()) {
                    $add($id, $title, 'info', 'Tłumaczenie ruszy przy najbliższym przebiegu.', Admin::url());
                    return;
                }
                if ($a = Log::current_alert()) {
                    $add($id, $title, 'bad', 'Wstrzymana: ' . (string) $a['m'], Admin::url(['tab' => 'settings']));
                    return;
                }
                $done = Index::count();
                $queued = Store::queued_count();
                $problems = Store::count_pages(['attention' => true, 'status' => ['new', 'done', 'partial', 'error']]);
                $public = \ZPL\Lang::is_public(Lang::code());
                $text = 'Gotowych stron: ' . $done . '.' . ($queued ? ' W kolejce: ' . $queued . ' ' . Admin::pages_word($queued) . '.' : '')
                    . ($problems ? ' Do poprawy: ' . $problems . '.' : '')
                    . ($public ? ' Widoczna dla gości i Google.' : ' Jeszcze niewidoczna: pokaże się po pierwszym pełnym tłumaczeniu.');
                $add($id, $title, $problems ? 'warn' : ($public ? 'ok' : 'info'), $text, Admin::url($problems ? ['tab' => 'pages', 'f' => 'attention'] : []));
            });
        }
        return $checks;
    }
}
