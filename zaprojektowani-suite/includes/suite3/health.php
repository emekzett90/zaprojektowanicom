<?php
/**
 * ZP Suite 3.0: "Stan strony". Checks in plain Polish, each with a state (ok, warn, bad, info) and where to fix it.
 * Quick checks read settings and options only. The deeper ones (sitemaps, robots.txt, home page) fetch the site
 * itself and run when the page is opened, at most every 6 hours. Other modules add their checks through the
 * zp_suite3_health_checks filter.
 */
if (!defined('ABSPATH')) { exit; }

/* Remember when WP-Cron last ran (at most one write per 5 minutes), so a stopped cron is visible. 3.1.1: runs started by the
   server's cron (zp-cron.php) are noted apart, so "Stan strony" can say whether that cron works. */
add_action('init', static function (): void {
  if (!wp_doing_cron()) { return; }
  $last = (int) get_option('zp_suite3_cron_last', 0);
  if (time() - $last > 300) { update_option('zp_suite3_cron_last', time(), false); }
  if (strpos((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 'ZP Suite cron') !== false && time() - (int) get_option('zp_suite3_cron_server', 0) > 240) { update_option('zp_suite3_cron_server', time(), false); }
}, 1);

/** 3.1.1: the line for the server's cron. The nazwa.pl panel runs only "php <file>" (no "?" or quotes), every 5 minutes. */
function zp_suite3_cron_command(): string {
  return 'php ' . WP_PLUGIN_DIR . '/' . basename(dirname(__DIR__, 2)) . '/zp-cron.php ' . (string) wp_parse_url(home_url('/'), PHP_URL_HOST);
}

/** True when the server's cron started WP-Cron in the last 15 minutes. */
function zp_suite3_cron_server_on(): bool {
  return time() - (int) get_option('zp_suite3_cron_server', 0) < 900;
}

function zp_suite3_bytes($value): int {
  $value = trim((string) $value);
  if ($value === '' || $value === '-1') { return -1; }
  $n = (int) $value; $unit = strtolower(substr($value, -1));
  return $unit === 'g' ? $n * 1073741824 : ($unit === 'm' ? $n * 1048576 : ($unit === 'k' ? $n * 1024 : $n));
}

function zp_suite3_check(string $id, string $title, string $state, string $text, string $url = '', string $action = '', string $confirm = ''): array {
  return ['id' => $id, 'title' => $title, 'state' => $state, 'text' => $text, 'url' => $url, 'action' => $action, 'confirm' => $confirm];
}

/** Hooks this site's modules schedule, with a readable name (others are counted, not named). */
function zp_suite3_cron_names(): array {
  return [
    'zp_feed_check' => 'Publikacja wpisów z kalendarza', 'zpte2_daily' => 'Codzienne sprawdzenie tłumacza EN', 'zpte2_work' => 'Tłumacz EN w tle',
    'zp_admin_stats_daily_20261009' => 'Raporty Google', 'zp_admin_stats_hourly_20261009' => 'Ruch GA4 dziś',
    'zp_senuto_positions_daily_20261010' => 'Pozycje Senuto', 'zp_admin_lighthouse_daily_20261010' => 'Lighthouse',
    'zp_ai_indexnow_flush' => 'IndexNow (zgłaszanie zmian)', 'zp_ai_daily' => 'Widoczność AI i llms.txt',
    'zp_en_public_currency_daily_20261011' => 'Kurs USD z NBP',
  ];
}

/** Overdue scheduled events: [count, oldest delay in seconds, names of the overdue site jobs]. */
function zp_suite3_cron_overdue(): array {
  $crons = function_exists('_get_cron_array') ? _get_cron_array() : [];
  $now = time(); $count = 0; $oldest = 0; $names = []; $known = zp_suite3_cron_names();
  foreach ((array) $crons as $ts => $hooks) {
    if ($ts > $now - 900) { continue; }
    foreach ((array) $hooks as $hook => $events) {
      $count += count((array) $events); $oldest = max($oldest, $now - (int) $ts);
      if (isset($known[$hook])) { $names[$known[$hook]] = true; }
    }
  }
  return [$count, $oldest, array_keys($names)];
}

function zp_suite3_health_checks(bool $deep = false): array {
  static $cache = [];
  if (isset($cache[$deep ? 1 : 0])) { return $cache[$deep ? 1 : 0]; }
  $checks = [];
  $health = zp_suite3_url('zp-suite-health');

  // Search engines allowed
  if ((string) get_option('blog_public', '1') === '0') {
    $checks[] = zp_suite3_check('indexing', 'Strona jest ukryta przed Google', 'bad', 'W Ustawieniach → Czytanie jest zaznaczone „Proś wyszukiwarki o nieindeksowanie”. Odznacz to.', admin_url('options-reading.php'));
  }

  // WP-Cron
  [$overdue, $oldest, $names] = zp_suite3_cron_overdue();
  $last = (int) get_option('zp_suite3_cron_last', 0);
  $cron_off = defined('DISABLE_WP_CRON') && DISABLE_WP_CRON;
  if ($overdue > 3 && $oldest > 3 * HOUR_IN_SECONDS) {
    $checks[] = zp_suite3_check('cron', 'Zaplanowane zadania się spóźniają', 'bad', sprintf('%s dłużej niż kwadrans, najstarsze %s.%s Automatyczna publikacja i tłumaczenie mogą stać. %s', zp_suite3_plural($overdue, 'zadanie czeka', 'zadania czekają', 'zadań czeka'), human_time_diff(time() - $oldest), $names ? ' Wśród nich: ' . implode(', ', $names) . '.' : '', zp_suite3_cron_server_on() ? 'Cron na serwerze działa, więc sprawdź zadania w sekcji Zaplanowane zadania.' : 'Najpewniej pomoże cron na serwerze (nazwa.pl → Zadania CRON, co 5 minut, polecenie z sekcji Zaplanowane zadania).'), $health . '#cron');
  } elseif ($overdue > 0) {
    $checks[] = zp_suite3_check('cron', 'Zadania zaplanowane działają z opóźnieniem', 'warn', sprintf('%s dłużej niż kwadrans (najstarsze %s). WP-Cron uruchamia się dopiero przy odwiedzinach strony.', zp_suite3_plural($overdue, 'zadanie czeka', 'zadania czekają', 'zadań czeka'), human_time_diff(time() - $oldest)), $health . '#cron');
  } else {
    $checks[] = zp_suite3_check('cron', 'Zaplanowane zadania działają', 'ok', (zp_suite3_cron_server_on() ? 'Cron na serwerze uruchamia je co 5 minut.' : ($cron_off ? 'Cron uruchamia serwer.' : 'WP-Cron uruchamia się przy odwiedzinach.')) . ($last ? ' Ostatnie uruchomienie: ' . human_time_diff($last) . ' temu.' : ''), $health . '#cron');
  }

  // PHP and server
  $opcache = function_exists('opcache_get_status') ? @opcache_get_status(false) : false;
  $opcache_on = is_array($opcache) ? !empty($opcache['opcache_enabled']) : filter_var(ini_get('opcache.enable'), FILTER_VALIDATE_BOOLEAN);
  $checks[] = $opcache_on
    ? zp_suite3_check('opcache', 'OPcache włączony', 'ok', 'PHP nie kompiluje kodu przy każdej odsłonie.')
    : zp_suite3_check('opcache', 'OPcache wyłączony', 'warn', 'Każda odsłona kompiluje cały kod PHP od nowa, co dokłada czas odpowiedzi. Włącz OPcache w panelu nazwa.pl (Ustawienia PHP).');
  if (version_compare(PHP_VERSION, '8.1', '<')) {
    $checks[] = zp_suite3_check('php', 'Stara wersja PHP: ' . PHP_VERSION, 'warn', 'Ustaw w panelu hostingu PHP 8.2 lub 8.3. Strona będzie szybsza i bezpieczniejsza.');
  }
  $vars = (int) ini_get('max_input_vars');
  if ($vars > 0 && $vars < 3000) {
    // 3.1.1: the update adds max_input_vars = 5000 to .user.ini (upgrade.php); PHP reads that file again within 5 minutes.
    $iv = get_option('zp_suite3_input_vars', []); $added = is_array($iv) && ($iv['result'] ?? '') === 'added';
    if ($added && time() - (int) ($iv['at'] ?? 0) < 900) {
      $checks[] = zp_suite3_check('input_vars', 'Limit pól formularza: ' . $vars . ' (zmiana w drodze)', 'info', 'Aktualizacja dopisała max_input_vars = 5000 do pliku .user.ini w folderze WordPressa. PHP wczyta go w ciągu kilku minut.', zp_suite3_url('zp-suite-home-cms'));
    } else {
      $checks[] = zp_suite3_check('input_vars', 'Limit pól formularza: ' . $vars, 'warn', ($added ? 'Plik .user.ini w folderze WordPressa ma max_input_vars = 5000, ale serwer go nie stosuje. Poproś nazwa.pl o ustawienie max_input_vars na 5000. ' : '') . 'Formularz „Strona główna” w ZP Suite ma blisko tylu pól; gdy serwer przyjmie tylko część, ZP Suite nic nie zapisze i pokaże komunikat.' . ($added ? '' : ' Ustaw max_input_vars na 5000 w ustawieniach PHP hostingu.'), zp_suite3_url('zp-suite-home-cms'));
    }
  }
  $mem = zp_suite3_bytes(ini_get('memory_limit'));
  if ($mem > 0 && $mem < 256 * 1048576) {
    $checks[] = zp_suite3_check('memory', 'Mało pamięci dla PHP: ' . ini_get('memory_limit'), 'info', 'Zalecane 256M. Przy dużych operacjach (zdjęcia, tłumaczenia) zadanie może się przerwać.');
  }
  if (defined('WP_DEBUG') && WP_DEBUG && (!defined('WP_DEBUG_DISPLAY') || WP_DEBUG_DISPLAY) && ini_get('display_errors')) {
    $checks[] = zp_suite3_check('debug', 'Błędy PHP są pokazywane odwiedzającym', 'bad', 'W wp-config.php jest WP_DEBUG włączony z wyświetlaniem błędów. Ustaw WP_DEBUG_DISPLAY na false.');
  }

  // Autoloaded options
  global $wpdb;
  $autoload = (int) $wpdb->get_var("SELECT SUM(LENGTH(option_value)) FROM {$wpdb->options} WHERE autoload IN ('yes','on','auto-on','auto')");
  if ($autoload > 1572864) {
    $checks[] = zp_suite3_check('autoload', 'Dużo danych wczytywanych przy każdej odsłonie', 'warn', sprintf('Opcje z autoload mają %s. Powyżej 1 MB spowalniają każdą stronę. Lista największych jest niżej.', size_format($autoload, 1)), $health . '#autoload');
  }

  // Page cache (ZP Suite's own HTML cache for Polish visitors without cookies other than consent and statistics, and robots)
  if (class_exists('ZP_HTML_Cache_20261009')) {
    $c = ZP_HTML_Cache_20261009::status();
    $url = zp_suite3_url('zp-suite-html-cache');
    $upgrade = get_option('zp_suite3_cache_301', []);
    $upgrade_error = is_array($upgrade) && strpos((string) ($upgrade['result'] ?? ''), 'error') === 0 ? (string) $upgrade['result'] : '';
    if (!$c['installed']) {
      $checks[] = zp_suite3_check('page_cache', 'Cache stron nie jest zainstalowany', 'info', 'Każda odsłona składa stronę w PHP. Cache włączysz na stronie Cache HTML.', $url);
    } elseif ($c['enabled'] && $c['wp_cache'] && $c['dropin_current'] && $c['error'] === '') {
      $checks[] = zp_suite3_check('page_cache', 'Cache stron działa', 'ok', sprintf('%s po polsku, odświeżane co %d min, osobno dla telefonu i komputera. Gotową stronę dostają odwiedzający bez cookies albo tylko z cookies zgody i statystyk oraz roboty wyszukiwarek.', zp_suite3_plural($c['paths'], 'adres', 'adresy', 'adresów'), max(1, (int) round($c['ttl'] / 60))), $url);
    } elseif ($c['enabled'] && !$c['dropin_current']) {
      $checks[] = zp_suite3_check('page_cache', 'Cache stron ma starą wersję pliku advanced-cache.php', 'bad', 'Plik nie pasuje do tej wersji ZP Suite, więc cache nie ma ostatnich poprawek. ' . ($upgrade_error !== '' ? 'Aktualizacja zgłosiła: ' . $upgrade_error . '. ' : '') . 'Otwórz Cache HTML i użyj „Odśwież własny cache”.', $url);
    } elseif ($c['enabled']) {
      $checks[] = zp_suite3_check('page_cache', 'Cache stron wymaga uwagi', 'warn', $c['error'] !== '' ? $c['error'] : 'WP_CACHE jest wyłączone w wp-config.php, więc cache nie podaje stron.', $url);
    } else {
      $checks[] = zp_suite3_check('page_cache', 'Cache stron wyłączony', 'warn', 'Strony są składane w PHP przy każdej odsłonie (około 1 s zamiast 0,1 s). ' . ($upgrade_error !== '' ? 'Aktualizacja zgłosiła: ' . $upgrade_error . '. ' : '') . 'Włącz go przyciskiem „Włącz ponownie cache HTML” na stronie Cache HTML.', $url);
    }
  }

  // Titles with the Rank Math phrase (SEO audit 13.10): their undo link is otherwise only in the notice shown once, to
  // whoever opens wp-admin first after the update.
  if (function_exists('zp_seo_audit_titles_restore') && defined('ZP_SEO_AUDIT_TITLES_RESULT')) {
    $titles = get_option(ZP_SEO_AUDIT_TITLES_RESULT, []);
    if (is_array($titles) && !empty($titles['rows']) && empty($titles['restored'])) {
      $changed = count(array_filter((array) $titles['rows'], static fn($row) => is_array($row) && in_array('updated', $row, true)));
      $undo = wp_nonce_url(admin_url('admin-post.php?action=zp_seo_audit_titles_restore'), 'zp_seo_audit_titles_restore');
      $checks[] = zp_suite3_check('seo_titles', 'Tytuły z frazą z audytu SEO 13.10', 'info', sprintf('Zmienione strony i wpisy: %d. Poprzednie tytuły i frazy Rank Math są zapisane. Przycisk je przywraca; tytuły, które później zmienisz ręcznie, zostają.', $changed), $undo, 'Cofnij tytuły', 'Przywrócić poprzednie tytuły i frazy Rank Math?');
    }
  }

  // Google data
  if (function_exists('zp_admin20_stats')) {
    $stats = zp_admin20_stats(); $sync = is_array($stats['sync'] ?? null) ? $stats['sync'] : []; $conn = is_array($stats['connection'] ?? null) ? $stats['connection'] : [];
    $updated = !empty($sync['updated_at']) ? (is_numeric($sync['updated_at']) ? (int) $sync['updated_at'] : (int) strtotime((string) $sync['updated_at'])) : 0;
    if (empty($conn['authorized'])) {
      $checks[] = zp_suite3_check('google', 'Brak połączenia z Google', 'warn', 'Raporty Search Console i GA4 pobiera Rank Math. Połącz konto Google w Rank Math → Analityka.', admin_url('admin.php?page=rank-math-options-general&view=analytics'));
    } elseif (!$updated || time() - $updated > 2 * DAY_IN_SECONDS) {
      $checks[] = zp_suite3_check('google', 'Dane z Google są nieaktualne', 'warn', $updated ? 'Ostatnie pobranie ' . human_time_diff($updated) . ' temu. Powinno być codziennie.' : 'Raporty nie zostały jeszcze pobrane.', zp_suite3_url('zp-suite-stats'));
    } else {
      $checks[] = zp_suite3_check('google', 'Dane z Google aktualne', 'ok', 'Ostatnie pobranie ' . human_time_diff($updated) . ' temu.', zp_suite3_url('zp-suite-stats'));
    }
  }

  // Automatic publication from the content calendar
  if (function_exists('zp_feed_status')) {
    $feed = zp_feed_status();
    if ((int) ($feed['fails'] ?? 0) > 0) {
      $checks[] = zp_suite3_check('feed', 'Publikacja wpisów zgłasza błąd', 'warn', 'Kolejne nieudane próby: ' . (int) $feed['fails'] . '. Ostatni błąd: ' . wp_strip_all_tags((string) ($feed['last_error'] ?? '')), zp_suite3_url('zp-suite-publikacja'));
    } elseif (!empty($feed['last_ok'])) {
      $checks[] = zp_suite3_check('feed', 'Publikacja wpisów działa', 'ok', 'Ostatnie udane sprawdzenie: ' . human_time_diff((int) $feed['last_ok']) . ' temu.', zp_suite3_url('zp-suite-publikacja'));
    }
  }

  // 404 log
  if (function_exists('zp_suite3_404_summary')) {
    $s = zp_suite3_404_summary();
    if ($s['internal'] > 0) {
      $checks[] = zp_suite3_check('404_internal', 'Linki na stronie prowadzą do błędu 404', 'bad', zp_suite3_plural($s['internal'], 'adres z błędem 404 ma', 'adresy z błędem 404 mają', 'adresów z błędem 404 ma') . ' odnośnik z Twojej własnej strony. Przekieruj je albo popraw link.', zp_suite3_url('zp-suite-404'));
    } elseif (($s['act'] ?? $s['open']) > 0) {
      // 3.2.1: a task only for page addresses Google asked for in the last 30 days that nothing redirects yet.
      $checks[] = zp_suite3_check('404', 'Adresy z błędem 404', 'warn', zp_suite3_plural((int) $s['act'], 'adres strony bez przekierowania, o który', 'adresy stron bez przekierowania, o które', 'adresów stron bez przekierowania, o które') . ' Google pytał w ostatnich 30 dniach. Sprawdź podpowiedź celu i przekieruj.', zp_suite3_url('zp-suite-404'));
    } elseif ($s['open'] > 0) {
      // Files, scanner requests, addresses Google no longer asks for and ones that answer again: listed in the panel, nothing to do now.
      $checks[] = zp_suite3_check('404', 'Adresy z błędem 404', 'info', zp_suite3_plural($s['open'], 'adres', 'adresy', 'adresów') . ' z błędem 404 w ostatnich 30 dniach: pliki, zapytania robotów i adresy, które już przekierowują albo działają. Żaden adres strony, o który Google pytał w tym czasie, nie czeka na przekierowanie.', zp_suite3_url('zp-suite-404'));
    } else {
      $checks[] = zp_suite3_check('404', 'Brak nowych błędów 404', 'ok', 'Od 30 dni każdy błędny adres ma przekierowanie albo nikt go nie odwiedził.', zp_suite3_url('zp-suite-404'));
    }
  }

  if ($deep) { $checks = array_merge($checks, zp_suite3_health_deep()); }
  $extra = apply_filters('zp_suite3_health_checks', [], $deep);
  foreach ((array) $extra as $check) {
    if (is_array($check) && isset($check['title'], $check['state'])) {
      $checks[] = zp_suite3_check((string) ($check['id'] ?? sanitize_title((string) $check['title'])), (string) $check['title'], in_array($check['state'], ['ok', 'warn', 'bad', 'info'], true) ? $check['state'] : 'info', (string) ($check['text'] ?? ''), (string) ($check['url'] ?? ''));
    }
  }
  $order = ['bad' => 0, 'warn' => 1, 'info' => 2, 'ok' => 3];
  usort($checks, static fn($a, $b) => $order[$a['state']] <=> $order[$b['state']]);
  $problems = count(array_filter($checks, static fn($c) => in_array($c['state'], ['bad', 'warn'], true)));
  if ($deep || get_transient('zp_suite3_health_problems') === false) { set_transient('zp_suite3_health_problems', $problems, 6 * HOUR_IN_SECONDS); }
  return $cache[$deep ? 1 : 0] = $checks;
}

/** Number on the "Stan strony" tab. Uses the last full check, or the quick checks when there is none. */
function zp_suite3_health_problem_count(): int {
  $n = get_transient('zp_suite3_health_problems');
  if ($n === false) {
    $n = count(array_filter(zp_suite3_health_checks(false), static fn($c) => in_array($c['state'], ['bad', 'warn'], true)));
  }
  return (int) $n;
}

/** Words that show a Polish address under /en/ (slug parts, split on "-" and "/"). */
function zp_suite3_polish_slug(string $path): bool {
  if (preg_match('/[ąćęłńóśźż]|%c4%85|%c4%87|%c4%99|%c5%82|%c5%84|%c3%b3|%c5%9b|%c5%ba|%c5%bc/iu', $path)) { return true; }
  $words = ['jak', 'czy', 'dla', 'oraz', 'ile', 'kosztuje', 'strony', 'stron', 'strona', 'sklep', 'sklepy', 'sklepu', 'internetowe', 'internetowa', 'internetowych', 'internetowej', 'tworzenie', 'projektowanie', 'firmy', 'firmowa', 'firmowej', 'marki', 'reklama', 'reklamy', 'cennik', 'poradnik', 'wiedza', 'uslugi', 'kategoria', 'realizacje', 'kontakt', 'wyceny', 'wycena', 'ktory', 'ktora', 'czego', 'sie', 'przez', 'bez', 'zespol', 'klientow', 'kampanie', 'pozycjonowanie', 'zaprojektowac', 'najlepsze', 'darmowe', 'logo-firmy', 'identyfikacja', 'wizualna'];
  $parts = preg_split('~[-/]+~', strtolower($path), -1, PREG_SPLIT_NO_EMPTY);
  return (bool) array_intersect($parts, $words);
}

/** Fetches the site from the server itself. Results are kept for 6 hours. */
function zp_suite3_health_deep(): array {
  $cached = get_transient('zp_suite3_health_deep');
  if (is_array($cached)) { return $cached; }
  $checks = []; $args = ['timeout' => 12, 'redirection' => 2, 'headers' => ['Cache-Control' => 'no-cache'], 'user-agent' => 'ZP Suite health check'];

  // Home page response
  $t = microtime(true); $home = wp_remote_get(add_query_arg('zp_health', time(), home_url('/')), $args); $ms = (int) round((microtime(true) - $t) * 1000);
  if (is_wp_error($home)) {
    $checks[] = zp_suite3_check('home', 'Nie udało się otworzyć strony z serwera', 'info', 'Serwer nie łączy się sam ze sobą (' . $home->get_error_message() . '). To nie wpływa na odwiedzających, ale tej części sprawdzenia nie da się zrobić.');
  } else {
    $code = (int) wp_remote_retrieve_response_code($home);
    $checks[] = $code === 200
      ? zp_suite3_check('home', 'Strona główna odpowiada', $ms > 1500 ? 'warn' : 'ok', sprintf('Kod 200 w %s ms (pomiar z serwera, bez sieci).%s', number_format_i18n($ms), $ms > 1500 ? ' To długo: sprawdź OPcache i dane z autoload.' : ''))
      : zp_suite3_check('home', 'Strona główna zwraca kod ' . $code, 'bad', 'Strona główna powinna zwracać 200.');
  }

  // robots.txt
  $robots = wp_remote_get(home_url('/robots.txt'), $args);
  if (!is_wp_error($robots) && wp_remote_retrieve_response_code($robots) === 200) {
    $body = (string) wp_remote_retrieve_body($robots);
    if (preg_match('~^\s*Disallow:\s*/\s*$~mi', $body)) {
      $checks[] = zp_suite3_check('robots', 'robots.txt blokuje całą stronę', 'bad', 'W robots.txt jest „Disallow: /”. Google nie wejdzie na żadną podstronę.');
    } elseif (stripos($body, 'sitemap') === false) {
      $checks[] = zp_suite3_check('robots', 'robots.txt nie wskazuje mapy strony', 'warn', 'Dodaj linię „Sitemap: ' . home_url('/sitemap_index.xml') . '”.');
    } else {
      $checks[] = zp_suite3_check('robots', 'robots.txt w porządku', 'ok', 'Nie blokuje strony i wskazuje mapę strony.');
    }
  }

  // Sitemaps: index lists both languages; English maps hold no Polish addresses
  $index = wp_remote_get(home_url('/sitemap_index.xml'), $args);
  if (!is_wp_error($index) && wp_remote_retrieve_response_code($index) === 200) {
    preg_match_all('~<loc>([^<]+)</loc>~', (string) wp_remote_retrieve_body($index), $m);
    $maps = array_map('html_entity_decode', $m[1] ?? []);
    $english = array_values(array_filter($maps, static fn($u) => stripos($u, 'english-') !== false));
    if (!$english) {
      $checks[] = zp_suite3_check('sitemap_en', 'Brak angielskich map strony w sitemap_index.xml', 'bad', 'Google nie dostaje listy stron /en/. Mapa powinna mieć english-page-sitemap.xml i english-post-sitemap.xml.');
    } else {
      $total = 0; $polish = [];
      foreach ($english as $url) {
        $r = wp_remote_get($url, $args);
        if (is_wp_error($r)) { continue; }
        preg_match_all('~<loc>([^<]+)</loc>~', (string) wp_remote_retrieve_body($r), $u);
        foreach ($u[1] ?? [] as $loc) {
          $total++; $path = (string) wp_parse_url(html_entity_decode($loc), PHP_URL_PATH);
          if (zp_suite3_polish_slug($path)) { $polish[] = $path; }
        }
      }
      $checks[] = $polish
        ? zp_suite3_check('sitemap_en', zp_suite3_plural(count($polish), 'polski adres', 'polskie adresy', 'polskich adresów') . ' w angielskiej mapie strony', 'bad', 'Na przykład: ' . implode(', ', array_slice($polish, 0, 4)) . '. Tłumacz EN powinien nadać im angielskie adresy.', zp_suite3_url(zp_suite3_translator_page()))
        : zp_suite3_check('sitemap_en', 'Angielska mapa strony: ' . zp_suite3_plural($total, 'adres', 'adresy', 'adresów'), 'ok', 'Wszystkie adresy w mapach /en/ są po angielsku. Mapy są w sitemap_index.xml razem z polskimi.');
    }
  } else {
    $checks[] = zp_suite3_check('sitemap', 'Nie udało się pobrać sitemap_index.xml', 'info', 'Sprawdzenie map strony wymaga, żeby serwer mógł otworzyć własną stronę.');
  }
  set_transient('zp_suite3_health_deep', $checks, 6 * HOUR_IN_SECONDS);
  return $checks;
}

function zp_suite3_health_page(): void {
  if (!current_user_can('manage_options')) { return; }
  if (isset($_GET['zp_recheck']) && check_admin_referer('zp_suite3_recheck')) { delete_transient('zp_suite3_health_deep'); }
  $checks = zp_suite3_health_checks(true);
  $counts = array_count_values(array_column($checks, 'state'));
  zp_suite3_open('Stan strony', 'Co działa, co wymaga uwagi i gdzie to poprawić.');
  echo '<div class="zpA-dashboard-tools"><div class="health-score">';
  foreach (['bad' => ['Do naprawy', 'is-bad'], 'warn' => ['Do sprawdzenia', 'is-warn'], 'ok' => ['W porządku', 'is-ok']] as $state => [$label, $class]) {
    echo '<span class="tag ' . $class . '">' . esc_html($label . ': ' . (int) ($counts[$state] ?? 0)) . '</span>';
  }
  echo '</div><a class="button" href="' . esc_url(wp_nonce_url(zp_suite3_url('zp-suite-health', ['zp_recheck' => 1]), 'zp_suite3_recheck')) . '">Sprawdź ponownie</a></div>';
  $icons = ['bad' => 'warning', 'warn' => 'flag', 'ok' => 'yes-alt', 'info' => 'info-outline'];
  echo '<section class="card"><div class="health-list">';
  foreach ($checks as $c) {
    echo '<div class="health-row is-' . esc_attr($c['state']) . '" id="health-' . esc_attr($c['id']) . '"><span class="dashicons dashicons-' . esc_attr($icons[$c['state']]) . '" aria-hidden="true"></span><div><h3>' . esc_html($c['title']) . '</h3><p>' . esc_html($c['text']) . '</p></div>';
    $confirm = ($c['confirm'] ?? '') !== '' ? ' onclick="return confirm(' . esc_attr(wp_json_encode($c['confirm'])) . ')"' : '';
    echo $c['url'] !== '' && $c['state'] !== 'ok' ? '<a class="button health-act" href="' . esc_url($c['url']) . '"' . $confirm . '>' . esc_html(($c['action'] ?? '') !== '' ? $c['action'] : 'Otwórz') . '</a>' : '<span></span>';
    echo '</div>';
  }
  echo '</div><div class="subtle-foot">Sprawdzenie map strony, robots.txt i strony głównej: co 6 godzin albo po „Sprawdź ponownie”.</div></section>';

  // Scheduled jobs
  $names = zp_suite3_cron_names(); $now = time();
  echo '<div class="zpAdmin20__grid" style="margin-top:20px"><section class="zpAdmin20__card" id="cron"><h2>Zaplanowane zadania</h2><div class="zpAdmin20__tableWrap"><table class="zpS3-table"><thead><tr><th>Zadanie</th><th>Następne</th></tr></thead><tbody>';
  foreach ($names as $hook => $label) {
    $next = wp_next_scheduled($hook);
    if (!$next) { continue; }
    $late = $next < $now - 900;
    echo '<tr><td>' . esc_html($label) . '</td><td>' . ($late ? '<span class="tag is-warn">spóźnione ' . esc_html(human_time_diff($next)) . '</span>' : esc_html(zp_suite3_date($next))) . '</td></tr>';
  }
  $last = (int) get_option('zp_suite3_cron_last', 0); $server = (int) get_option('zp_suite3_cron_server', 0);
  echo '</tbody></table></div><p class="zpAdmin20__muted" style="margin-top:12px">Ostatnie uruchomienie WP-Cron: ' . esc_html($last ? human_time_diff($last) . ' temu' : 'brak zapisu (pojawi się po pierwszym uruchomieniu)') . '. ' . esc_html(zp_suite3_cron_server_on() ? 'Cron na serwerze: ostatnio ' . human_time_diff($server) . ' temu.' : ($server ? 'Cron na serwerze nie odezwał się od ' . zp_suite3_date($server) . '.' : (defined('DISABLE_WP_CRON') && DISABLE_WP_CRON ? 'Cron uruchamia serwer.' : 'Cron startuje przy odwiedzinach; stały cron na serwerze co 5 minut jest pewniejszy.'))) . '</p>';
  // 3.1.1: the exact line for nazwa.pl → Zadania CRON (every 5 minutes). It asks this site for wp-cron.php and waits for it.
  echo '<p class="zpAdmin20__muted">Polecenie dla crona na serwerze (nazwa.pl → Zadania CRON, co 5 minut): <code style="user-select:all;word-break:break-all">' . esc_html(zp_suite3_cron_command()) . '</code>. Nie ustawiaj DISABLE_WP_CRON.</p></section>';

  // Largest autoloaded options
  global $wpdb;
  $rows = $wpdb->get_results("SELECT option_name, LENGTH(option_value) AS size FROM {$wpdb->options} WHERE autoload IN ('yes','on','auto-on','auto') ORDER BY size DESC LIMIT 8");
  echo '<section class="zpAdmin20__card" id="autoload"><h2>Największe dane wczytywane przy każdej odsłonie</h2><div class="zpAdmin20__tableWrap"><table class="zpS3-table"><thead><tr><th>Opcja</th><th class="num">Rozmiar</th></tr></thead><tbody>';
  foreach ((array) $rows as $row) { echo '<tr><td><code>' . esc_html($row->option_name) . '</code></td><td class="num">' . esc_html(size_format((int) $row->size, 1)) . '</td></tr>'; }
  echo '</tbody></table></div></section>';

  // Versions
  $versions = ['WordPress' => get_bloginfo('version'), 'PHP' => PHP_VERSION, 'ZP Suite' => ZP_SUITE_VERSION, 'Języki PL/EN' => defined('ZPL_VERSION') ? ZPL_VERSION : '—', 'Tłumacz EN' => defined('ZPTE_VERSION') ? ZPTE_VERSION : '—', 'Rank Math' => defined('RANK_MATH_VERSION') ? RANK_MATH_VERSION : '—'];
  echo '<section class="zpAdmin20__card"><h2>Wersje</h2><div class="zpAdmin20__tableWrap"><table class="zpS3-table"><tbody>';
  foreach ($versions as $k => $v) { echo '<tr><td>' . esc_html($k) . '</td><td class="num">' . esc_html((string) $v) . '</td></tr>'; }
  echo '</tbody></table></div></section></div>';
  zp_suite3_close();
}
