<?php
namespace ZPL;

if (!defined('ABSPATH')) { exit; }

/** Ustawienia → Języki PL/EN */
final class Admin {
    const SLUG = 'zpl-languages';

    public static function boot(): void {
        add_action('admin_menu', static function () {
            add_options_page('Języki PL/EN', 'Języki PL/EN', 'manage_options', self::SLUG, [self::class, 'page']);
        });
        add_action('admin_post_zpl_save', [self::class, 'save']);
        add_action('admin_notices', [self::class, 'notices']);
        add_filter('plugin_action_links_' . plugin_basename(ZPL_HOST), static function ($links) {
            array_unshift($links, '<a href="' . esc_url(admin_url('options-general.php?page=' . self::SLUG)) . '">' . (ZPL_HOST === ZPL_FILE ? 'Ustawienia' : 'Języki PL/EN') . '</a>');
            return $links;
        });
        add_action('admin_init', static function () {
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

    private static function url(array $args = []): string {
        return add_query_arg(array_merge(['page' => self::SLUG], $args), admin_url('options-general.php'));
    }

    public static function page(): void {
        if (!current_user_can('manage_options')) { return; }
        $tab = sanitize_key($_GET['tab'] ?? 'status');
        $tabs = ['status' => 'Status', 'missing' => 'Brakujące tłumaczenia', 'overrides' => 'Poprawki tłumaczeń', 'routes' => 'Adresy URL', 'cleanup' => 'Stare wtyczki'];
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
