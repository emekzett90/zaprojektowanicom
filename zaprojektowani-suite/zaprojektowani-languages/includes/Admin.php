<?php
namespace ZPL;

if (!defined('ABSPATH')) { exit; }

/** ZP Suite → Języki PL/EN (Ustawienia → Języki PL/EN when the Suite menu is missing) */
final class Admin {
    const SLUG = 'zpl-languages';

    public static function boot(): void {
        // After the Suite trims its submenu (priority 9999), so this entry stays visible.
        add_action('admin_menu', [self::class, 'menu'], 10000);
        add_action('admin_post_zpl_save', [self::class, 'save']);
        add_action('admin_notices', [self::class, 'notices']);
        add_filter('plugin_action_links_' . plugin_basename(ZPL_HOST), static function ($links) {
            array_unshift($links, '<a href="' . esc_url(self::url()) . '">' . (ZPL_HOST === ZPL_FILE ? 'Ustawienia' : 'Języki PL/EN') . '</a>');
            return $links;
        });
        add_action('admin_init', static function () {
            // Old address (Ustawienia → Języki PL/EN) from bookmarks and earlier notes.
            global $pagenow;
            if ($pagenow === 'options-general.php' && ($_GET['page'] ?? '') === self::SLUG && self::in_suite()) {
                wp_safe_redirect(self::url(array_diff_key(wp_unslash($_GET), ['page' => 1])));
                exit;
            }
            // An old translator re-activated by hand would conflict with this plugin.
            if (current_user_can('activate_plugins') && array_filter(Cleanup::found(), static function ($p) { return $p['active']; })) {
                Cleanup::deactivate();
                set_transient('zpl_notice', 'Stara wtyczka tłumacząca została wyłączona — dwie wtyczki językowe nie mogą działać jednocześnie.', 60);
            }
        });
    }

    public static function notices(): void {
        $n = get_transient('zpl_notice');
        if ($n) { delete_transient('zpl_notice'); echo '<div class="notice notice-info is-dismissible"><p>' . esc_html($n) . '</p></div>'; }
    }

    public static function menu(): void {
        if (self::in_suite()) {
            add_submenu_page('zp-suite', 'Języki PL/EN', 'Języki PL/EN', 'manage_options', self::SLUG, [self::class, 'page']);
        } else {
            add_options_page('Języki PL/EN', 'Języki PL/EN', 'manage_options', self::SLUG, [self::class, 'page']);
        }
    }

    /** True when the Suite's own menu (ZP Suite) exists, so the page sits next to Tłumacz EN. */
    private static function in_suite(): bool {
        return defined('ZP_SUITE_VERSION');
    }

    private static function url(array $args = []): string {
        return add_query_arg(array_merge(['page' => self::SLUG], $args), admin_url(self::in_suite() ? 'admin.php' : 'options-general.php'));
    }

    public static function page(): void {
        if (!current_user_can('manage_options')) { return; }
        $tab = sanitize_key($_GET['tab'] ?? 'status');
        $tabs = ['status' => 'Status', 'geo' => 'Goście z zagranicy', 'missing' => 'Brakujące tłumaczenia', 'overrides' => 'Poprawki tłumaczeń', 'routes' => 'Adresy URL', 'cleanup' => 'Stare wtyczki'];
        echo '<div class="wrap"><h1>Języki PL/EN</h1>';
        if (!empty($_GET['msg'])) { echo '<div class="notice notice-success"><p>' . esc_html(wp_unslash($_GET['msg'])) . '</p></div>'; }
        echo '<nav class="nav-tab-wrapper">';
        foreach ($tabs as $k => $label) { echo '<a class="nav-tab' . ($k === $tab ? ' nav-tab-active' : '') . '" href="' . esc_url(self::url(['tab' => $k])) . '">' . esc_html($label) . '</a>'; }
        echo '</nav><div style="max-width:1100px;margin-top:18px">';
        $fn = 'tab_' . (isset($tabs[$tab]) ? $tab : 'status');
        self::$fn();
        echo '</div></div>';
    }

    private static function form_open(string $action, string $confirm = ''): void {
        echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '"' . ($confirm ? ' onsubmit="return confirm(' . esc_attr(wp_json_encode($confirm)) . ')"' : '') . '>';
        echo '<input type="hidden" name="action" value="zpl_save"><input type="hidden" name="do" value="' . esc_attr($action) . '">';
        wp_nonce_field('zpl_save');
    }

    private static function tab_status(): void {
        $m = Dict::meta();
        $missing = count(Missing::all());
        $ov = count(Dict::overrides('en'));
        echo '<table class="widefat striped" style="max-width:760px"><tbody>';
        $rows = [
            'Wersja wtyczki' => ZPL_VERSION,
            'Wersja słownika' => ($m['version'] ?? '—') . (isset($m['built']) ? ' (' . wp_date('Y-m-d H:i', (int) $m['built']) . ')' : ''),
            'Przetłumaczone fragmenty' => number_format_i18n((int) ($m['entries'] ?? 0)),
            'Strony w słowniku' => number_format_i18n(count((array) ($m['pages'] ?? []))),
            'Adresy EN' => number_format_i18n(count(Router::routes())),
            'Poprawki ręczne' => number_format_i18n($ov),
            'Brakujące tłumaczenia (do przejrzenia)' => number_format_i18n($missing),
            'Wykluczone ścieżki' => implode(', ', Router::excludes()),
            'Mapy witryny EN' => implode('<br>', array_map(static function ($u) { return '<a href="' . esc_url($u) . '" target="_blank">' . esc_html($u) . '</a>'; }, Sitemap::urls())),
        ];
        foreach ($rows as $k => $v) { echo '<tr><th style="width:320px">' . esc_html($k) . '</th><td>' . wp_kses_post((string) $v) . '</td></tr>'; }
        echo '</tbody></table>';
        echo '<h2>Ustawienia</h2>';
        self::form_open('settings');
        $ex = implode("\n", Router::excludes());
        echo '<p><label><strong>Ścieżki bez wersji angielskiej</strong> (jedna na linię; wszystko, co zaczyna się od tej ścieżki)</label><br><textarea name="exclude" rows="4" cols="60" class="code">' . esc_textarea($ex) . '</textarea></p>';
        submit_button('Zapisz ustawienia');
        echo '</form>';
        echo defined('ZPLS_VERSION')
            ? '<p><a class="button button-primary" href="' . esc_url(admin_url('admin.php?page=zpl-skaner')) . '">Skaner EN — znajdź i napraw polskie teksty w wersji angielskiej</a></p>'
            : '<p class="description">Polskie teksty w wersji angielskiej (także w okienkach, menu i skryptach) znajdzie i naprawi wtyczka <strong>Zaprojektowani Skaner EN</strong>.</p>';
        $fx = ['zpl_extra_attrs' => 'Atrybuty tłumaczone dodatkowo', 'zpl_extra_schema' => 'Dodatkowe pola danych dla Google'];
        foreach ($fx as $opt => $label) { $v = get_option($opt, []); if (is_array($v) && $v) { echo '<p class="description">' . esc_html($label) . ': <code>' . esc_html(implode(', ', $v)) . '</code></p>'; } }
        echo '<p class="description">Sprawdź stronę jako administrator z parametrem <code>?zpl_debug=1</code> — nieprzetłumaczone fragmenty zostaną podświetlone, a lista trafi do zakładki „Brakujące tłumaczenia”.</p>';
    }

    private static function tab_geo(): void {
        $set = Geo::settings();
        $stats = get_option(Geo::STATS, []);
        $stats = is_array($stats) ? $stats : [];
        $sum = static function (int $days) use ($stats): int {
            $n = 0;
            for ($i = 0; $i < $days; $i++) { $n += (int) ($stats[wp_date('Y-m-d', time() - $i * DAY_IN_SECONDS)] ?? 0); }
            return $n;
        };
        $where = static function (?string $c): string {
            if ($c === 'PL') { return 'Polska'; }
            if ($c === 'ZZ') { return 'poza Polską'; }
            return $c === null ? 'nie wiadomo (adres prywatny lub brak listy)' : 'poza Polską (' . $c . ')';
        };
        echo '<div style="max-width:820px">';
        echo '<p>Gość spoza Polski, który wchodzi na polską stronę mającą wersję angielską, od razu dostaje wersję angielską (np. z <code>/kontakt/</code> na <code>/en/contact/</code>). '
            . 'Kraj rozpoznajemy na serwerze po adresie IP, z listy polskich adresów dołączonej do wtyczki, więc nic nie miga i nie wysyłamy adresów gości do zewnętrznych usług.</p>';
        echo '<ul style="list-style:disc;padding-left:20px">'
            . '<li>Roboty Google, Bing, ChatGPT i narzędzia typu PageSpeed nigdy nie są przekierowywane: polskie adresy w wynikach wyszukiwania i oznaczenia hreflang zostają bez zmian.</li>'
            . '<li>Gość, który sam przełączy język (PL/EN), zostaje przy swoim wyborze przez 30 dni. Przejście z wersji angielskiej na polską też liczy się jako wybór.</li>'
            . '<li>Strony bez wersji angielskiej zostają po polsku, a zalogowani do WordPressa nie są przekierowywani.</li>'
            . '</ul>';
        self::form_open('geo');
        echo '<p><label><input type="checkbox" name="enabled" value="1"' . checked($set['enabled'], true, false) . '> <strong>Goście spoza Polski dostają wersję angielską</strong></label></p>';
        echo '<p><label><input type="checkbox" name="polish_stays" value="1"' . checked($set['polish_stays'], true, false) . '> Zostaw wersję polską, gdy główny język przeglądarki to polski (np. Polacy mieszkający za granicą)</label></p>';
        submit_button('Zapisz', 'primary', 'submit', false);
        echo '</form>';

        echo '<h2>Przekierowania na wersję angielską</h2>';
        echo '<p>Dziś: <strong>' . esc_html(number_format_i18n($sum(1))) . '</strong> · ostatnie 7 dni: <strong>' . esc_html(number_format_i18n($sum(7))) . '</strong> · ostatnie 30 dni: <strong>' . esc_html(number_format_i18n($sum(30))) . '</strong></p>';
        echo '<p class="description">Liczymy tylko pierwsze wejścia, które serwer przekierował. Kolejne strony gość ogląda już po angielsku, z linków wersji angielskiej.</p>';

        $info = Geo::data_info();
        $ip = Geo::visitor_ip();
        $header = Geo::header_country();
        echo '<h2>Rozpoznawanie kraju</h2><table class="widefat striped" style="max-width:820px"><tbody>';
        echo '<tr><th style="width:300px">Lista polskich adresów IP</th><td>' . ($info['v4'] > 0
            ? esc_html(number_format_i18n($info['v4']) . ' zakresów IPv4 i ' . number_format_i18n($info['v6']) . ' IPv6, z dnia ' . $info['built']) . '<br><small>Źródło: ip-location-db (domena publiczna). Aktualizacja przychodzi z nowymi wersjami wtyczki.</small>'
            : '<strong>brak plików listy</strong>: przekierowanie działa tylko z krajem podanym przez serwer') . '</td></tr>';
        echo '<tr><th>Kraj z nagłówków serwera (Cloudflare itp.)</th><td>' . esc_html($header !== null ? $header : 'serwer nie podaje kraju, decyduje lista adresów') . '</td></tr>';
        echo '<tr><th>Twoje połączenie</th><td><code>' . esc_html($ip !== '' ? $ip : '—') . '</code>: ' . esc_html($where($header ?? Geo::country_for_ip($ip))) . '</td></tr>';
        echo '</tbody></table>';

        $tIp = isset($_GET['ip']) ? trim(sanitize_text_field(wp_unslash($_GET['ip']))) : '';
        $tAccept = isset($_GET['accept']) ? trim(sanitize_text_field(wp_unslash($_GET['accept']))) : 'en-US,en;q=0.9';
        $tPath = isset($_GET['path']) ? trim(sanitize_text_field(wp_unslash($_GET['path']))) : '/kontakt/';
        echo '<h2>Sprawdź, co zobaczy gość</h2>';
        echo '<form method="get" action="' . esc_url(admin_url(self::in_suite() ? 'admin.php' : 'options-general.php')) . '"><input type="hidden" name="page" value="' . esc_attr(self::SLUG) . '"><input type="hidden" name="tab" value="geo">';
        echo '<table class="form-table" role="presentation"><tbody>'
            . '<tr><th><label for="zpl-geo-ip">Adres IP gościa</label></th><td><input id="zpl-geo-ip" name="ip" class="regular-text code" value="' . esc_attr($tIp) . '" placeholder="np. 8.8.8.8 (USA) albo 83.0.0.1 (Polska)"></td></tr>'
            . '<tr><th><label for="zpl-geo-accept">Języki przeglądarki</label></th><td><input id="zpl-geo-accept" name="accept" class="regular-text code" value="' . esc_attr($tAccept) . '"><p class="description">Np. <code>en-US,en;q=0.9</code> albo <code>pl-PL,pl;q=0.9,en;q=0.8</code></p></td></tr>'
            . '<tr><th><label for="zpl-geo-path">Strona</label></th><td><input id="zpl-geo-path" name="path" class="regular-text code" value="' . esc_attr($tPath) . '"></td></tr>'
            . '</tbody></table>';
        submit_button('Sprawdź', 'secondary', '', false);
        echo '</form>';
        if ($tIp !== '') {
            [$lang, $source, $mapped, $redirect] = Router::resolve((string) (wp_parse_url($tPath, PHP_URL_PATH) ?: '/'));
            if ($redirect !== null) { [$lang, $source, $mapped] = Router::resolve($redirect); }
            $d = Geo::decide($lang, $source, $mapped, false, ['ip' => $tIp, 'accept' => $tAccept, 'ua' => 'Mozilla/5.0']);
            $country = $d['country'] ?? Geo::country_for_ip($tIp);
            echo '<div class="notice notice-' . ($d['target'] !== null ? 'success' : 'info') . ' inline" style="margin-top:14px"><p><strong>'
                . esc_html($d['target'] !== null ? 'Wersja angielska: ' . home_url($d['target']) : 'Wersja polska') . '</strong><br>'
                . esc_html(Geo::REASONS[$d['reason']] ?? $d['reason']) . '<br><small>Kraj tego adresu IP: ' . esc_html($where($country)) . '</small></p></div>';
        }
        echo '</div>';
    }

    private static function tab_missing(): void {
        $all = Missing::all();
        uasort($all, static function ($a, $b) { return ($b['t'] ?? 0) <=> ($a['t'] ?? 0); });
        echo '<p>Teksty zauważone na stronach EN bez tłumaczenia (np. nowe treści lub teksty wstawiane przez skrypty). Wpisz tłumaczenie i zapisz — zadziała od razu. Znaczniki typu <code>&lt;1&gt;…&lt;/1&gt;</code> i <code>&lt;2/&gt;</code> oznaczają pogrubienia, linki itp. i muszą zostać zachowane.</p>';
        if (!$all) { echo '<p><em>Brak — wszystko przetłumaczone.</em></p>'; return; }
        self::form_open('missing');
        echo '<table class="widefat striped"><thead><tr><th style="width:44%">Tekst polski</th><th>Tłumaczenie angielskie</th><th style="width:90px">Usuń</th></tr></thead><tbody>';
        $i = 0;
        foreach (array_slice($all, 0, 300, true) as $h => $row) {
            $pages = implode(', ', array_map('esc_html', (array) ($row['pages'] ?? [])));
            echo '<tr><td><code style="white-space:pre-wrap">' . esc_html($row['k']) . '</code><br><small>' . $pages . '</small><input type="hidden" name="m[' . $i . '][h]" value="' . esc_attr($h) . '"></td>'
                . '<td><textarea name="m[' . $i . '][t]" rows="2" style="width:100%"></textarea></td>'
                . '<td><label><input type="checkbox" name="m[' . $i . '][x]" value="1"> ignoruj</label></td></tr>';
            $i++;
        }
        echo '</tbody></table>';
        submit_button('Zapisz tłumaczenia');
        echo '</form>';
        self::form_open('missing_prune');
        submit_button('Usuń z listy teksty, które mają już tłumaczenie', 'secondary');
        echo '</form>';
    }

    private static function tab_overrides(): void {
        $ov = Dict::overrides('en');
        echo '<p>Ręczne poprawki mają pierwszeństwo przed słownikiem dostarczonym z wtyczką. Aby poprawić istniejące tłumaczenie, wklej dokładny tekst polski (tak jak widać go na stronie) i nowe tłumaczenie.</p>';
        self::form_open('override_add');
        echo '<table class="form-table"><tr><th>Tekst polski</th><td><textarea name="pl" rows="3" cols="80"></textarea></td></tr><tr><th>Tłumaczenie EN</th><td><textarea name="en" rows="3" cols="80"></textarea></td></tr></table>';
        submit_button('Dodaj / zmień');
        echo '</form>';
        if ($ov) {
            self::form_open('override_delete');
            echo '<table class="widefat striped"><thead><tr><th>PL</th><th>EN</th><th style="width:70px">Usuń</th></tr></thead><tbody>';
            foreach ($ov as $pl => $en) {
                echo '<tr><td>' . esc_html($pl) . '</td><td>' . esc_html($en) . '</td><td><input type="checkbox" name="del[]" value="' . esc_attr(substr(md5((string) $pl), 0, 12)) . '"></td></tr>';
            }
            echo '</tbody></table>';
            submit_button('Usuń zaznaczone', 'delete');
            echo '</form>';
        }
        echo '<h2>Eksport / import</h2>';
        self::form_open('export');
        submit_button('Pobierz poprawki i brakujące teksty (JSON)', 'secondary', 'submit', false);
        echo '</form><br>';
        echo '<form method="post" enctype="multipart/form-data" action="' . esc_url(admin_url('admin-post.php')) . '"><input type="hidden" name="action" value="zpl_save"><input type="hidden" name="do" value="import">';
        wp_nonce_field('zpl_save');
        echo '<input type="file" name="file" accept="application/json"> ';
        submit_button('Importuj poprawki (JSON)', 'secondary', 'submit', false);
        echo '<p class="description">Format: <code>{"overrides": {"tekst PL": "EN text"}}</code></p></form>';
    }

    private static function tab_routes(): void {
        $routes = Router::routes();
        $custom = get_option('zpl_routes', []);
        echo '<p>Adres angielski każdej polskiej podstrony. Nowe wpisy bez własnego adresu otrzymują automatycznie <code>/en/</code> + polski adres (bez indeksowania do czasu tłumaczenia). Tutaj możesz nadać własny adres.</p>';
        self::form_open('route_add');
        echo '<p><input name="pl" placeholder="/polski-adres/" class="regular-text code"> → <input name="en" placeholder="/en/english-address/" class="regular-text code"> ';
        submit_button('Zapisz adres', 'primary', 'submit', false);
        echo '</p></form>';
        echo '<table class="widefat striped"><thead><tr><th>Polski</th><th>Angielski</th><th></th></tr></thead><tbody>';
        foreach ($routes as $pl => $en) {
            $own = isset($custom[$pl]) ? ' <em>(ręczny)</em>' : '';
            echo '<tr><td><a href="' . esc_url(home_url($pl)) . '" target="_blank">' . esc_html($pl) . '</a></td><td><a href="' . esc_url(home_url($en)) . '" target="_blank">' . esc_html($en) . '</a>' . $own . '</td><td>';
            if ($own) {
                self::form_open('route_delete');
                echo '<input type="hidden" name="pl" value="' . esc_attr($pl) . '"><button class="button-link-delete" type="submit">usuń</button></form>';
            }
            echo '</td></tr>';
        }
        echo '</tbody></table>';
    }

    private static function tab_cleanup(): void {
        $found = Cleanup::found();
        $left = Cleanup::leftovers();
        echo '<p>Poprzednie wtyczki tłumaczące (Zaprojektowani Ultimate English AI, AI Languages, AI Translator, Ultimate AI Languages) są automatycznie wyłączane. Tutaj możesz je całkowicie usunąć razem z danymi (tabele tłumaczeń AI, ustawienia, klucze API zapisane w bazie).</p>';
        if (!$found && !$left['tables'] && !$left['options']) { echo '<p><strong>Nic do usunięcia — stare wtyczki i ich dane zostały już usunięte.</strong></p>'; return; }
        echo '<h3>Wtyczki</h3><ul style="list-style:disc;padding-left:20px">';
        foreach ($found as $file => $p) { echo '<li>' . esc_html($p['name'] . ' ' . $p['version']) . ' <code>' . esc_html($file) . '</code> — ' . ($p['active'] ? 'aktywna' : 'nieaktywna') . '</li>'; }
        if (!$found) { echo '<li>brak</li>'; }
        echo '</ul><h3>Tabele w bazie</h3><p><code>' . esc_html(implode(', ', $left['tables']) ?: 'brak') . '</code></p>';
        echo '<h3>Opcje w bazie</h3><p><code>' . esc_html(implode(', ', array_slice($left['options'], 0, 40)) ?: 'brak') . (count($left['options']) > 40 ? ' …' : '') . '</code></p>';
        self::form_open('purge', 'Usunąć stare wtyczki tłumaczące wraz z ich tabelami i ustawieniami? Tej operacji nie można cofnąć.');
        submit_button('Usuń stare wtyczki i ich dane', 'delete');
        echo '</form>';
    }

    public static function save(): void {
        if (!current_user_can('manage_options')) { wp_die('Brak uprawnień'); }
        check_admin_referer('zpl_save');
        $do = sanitize_key($_POST['do'] ?? '');
        $tab = 'status';
        $msg = 'Zapisano.';
        switch ($do) {
            case 'settings':
                $ex = preg_split('~[\r\n]+~', (string) wp_unslash($_POST['exclude'] ?? '')) ?: [];
                $opt = get_option('zpl_settings', []);
                $opt = is_array($opt) ? $opt : [];
                $opt['exclude'] = array_values(array_filter(array_map('trim', $ex)));
                update_option('zpl_settings', $opt, true);
                break;
            case 'geo':
                $tab = 'geo';
                update_option(Geo::SETTINGS, ['enabled' => empty($_POST['enabled']) ? 0 : 1, 'polish_stays' => empty($_POST['polish_stays']) ? 0 : 1], true);
                break;
            case 'missing':
                $tab = 'missing';
                $ov = Dict::overrides('en');
                $all = Missing::all();
                $drop = [];
                $n = 0;
                foreach ((array) ($_POST['m'] ?? []) as $row) {
                    $h = sanitize_key($row['h'] ?? '');
                    if (!isset($all[$h])) { continue; }
                    $t = trim((string) wp_unslash($row['t'] ?? ''));
                    if ($t !== '') {
                        $k = (string) $all[$h]['k'];
                        if (preg_match('~</?\d+/?>~', $k) && !Html::markers_match($k, $t)) { $msg = 'Część tłumaczeń pominięto: znaczniki <1>…</1> muszą się zgadzać z tekstem polskim.'; continue; }
                        $ov[$k] = $t; $drop[] = $h; $n++;
                    } elseif (!empty($row['x'])) { $drop[] = $h; }
                }
                self::store_overrides($ov);
                Missing::forget($drop);
                if ($msg === 'Zapisano.') { $msg = 'Zapisano ' . $n . ' tłumaczeń.'; }
                break;
            case 'missing_prune':
                $tab = 'missing';
                $msg = 'Usunięto z listy: ' . Missing::prune();
                break;
            case 'override_add':
                $tab = 'overrides';
                $pl = Html::norm((string) wp_unslash($_POST['pl'] ?? ''));
                $en = trim((string) wp_unslash($_POST['en'] ?? ''));
                if ($pl !== '' && $en !== '') {
                    if (preg_match('~</?\d+/?>~', $pl) && !Html::markers_match($pl, $en)) { $msg = 'Znaczniki <1>…</1> muszą się zgadzać.'; break; }
                    $ov = Dict::overrides('en'); $ov[$pl] = $en; self::store_overrides($ov);
                }
                break;
            case 'override_delete':
                $tab = 'overrides';
                $del = array_map('sanitize_key', (array) ($_POST['del'] ?? []));
                $ov = Dict::overrides('en');
                foreach ($ov as $pl => $en) { if (in_array(substr(md5((string) $pl), 0, 12), $del, true)) { unset($ov[$pl]); } }
                self::store_overrides($ov);
                break;
            case 'export':
                nocache_headers();
                header('Content-Type: application/json; charset=utf-8');
                header('Content-Disposition: attachment; filename=zpl-tlumaczenia-' . gmdate('Ymd') . '.json');
                echo wp_json_encode(['overrides' => (object) Dict::overrides('en'), 'missing' => array_values(array_map(static function ($r) { return ['pl' => $r['k'], 'pages' => $r['pages'] ?? []]; }, Missing::all()))], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                exit;
            case 'import':
                $tab = 'overrides';
                $f = $_FILES['file']['tmp_name'] ?? '';
                $j = $f && is_uploaded_file($f) ? json_decode((string) file_get_contents($f), true) : null;
                if (!is_array($j) || !isset($j['overrides']) || !is_array($j['overrides'])) { $msg = 'Nieprawidłowy plik.'; break; }
                $ov = Dict::overrides('en');
                $n = 0;
                foreach ($j['overrides'] as $pl => $en) {
                    $pl = Html::norm((string) $pl); $en = trim((string) $en);
                    if ($pl === '' || $en === '') { continue; }
                    if (preg_match('~</?\d+/?>~', $pl) && !Html::markers_match($pl, $en)) { continue; }
                    $ov[$pl] = $en; $n++;
                }
                self::store_overrides($ov);
                Missing::prune();
                $msg = 'Zaimportowano ' . $n . ' tłumaczeń.';
                break;
            case 'route_add':
                $tab = 'routes';
                $pl = Router::norm_path((string) wp_unslash($_POST['pl'] ?? ''));
                $en = Router::norm_path((string) wp_unslash($_POST['en'] ?? ''));
                if (strpos($en, '/en/') !== 0) { $en = '/en' . $en; }
                if ($pl === '/' || $en === '/en/' || strpos($pl, '/en/') === 0) { $msg = 'Nieprawidłowy adres.'; break; }
                $taken = array_search($en, Router::routes(), true);
                if ($taken !== false && $taken !== $pl) { $msg = 'Ten adres EN jest już użyty dla ' . $taken; break; }
                $custom = get_option('zpl_routes', []); $custom = is_array($custom) ? $custom : [];
                $custom[$pl] = $en;
                update_option('zpl_routes', $custom, true);
                break;
            case 'route_delete':
                $tab = 'routes';
                $pl = Router::norm_path((string) wp_unslash($_POST['pl'] ?? ''));
                $custom = get_option('zpl_routes', []); $custom = is_array($custom) ? $custom : [];
                unset($custom[$pl]);
                update_option('zpl_routes', $custom, true);
                break;
            case 'purge':
                $tab = 'cleanup';
                $r = Cleanup::purge();
                $msg = 'Usunięto wtyczki: ' . count($r['plugins']) . ', tabele: ' . count($r['tables']) . ', opcje: ' . count($r['options']) . ($r['errors'] ? ' — błędy: ' . implode('; ', $r['errors']) : '');
                break;
        }
        wp_safe_redirect(self::url(['tab' => $tab, 'msg' => $msg]));
        exit;
    }

    public static function store_overrides(array $ov): void {
        ksort($ov);
        update_option('zpl_overrides', $ov, false);
        update_option('zpl_overrides_version', (string) time(), true);
        Dict::flush();
        if (function_exists('wp_cache_flush_group')) { wp_cache_flush_group('zpl'); }
    }
}
