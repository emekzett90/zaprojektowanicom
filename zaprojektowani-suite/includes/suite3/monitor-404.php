<?php
/**
 * ZP Suite 3.0: log of addresses that end in 404, and 301 redirects added from the panel.
 *
 * Page views pay nothing: both run only when WordPress has already decided the request is a 404, after the
 * keyword-plan redirects (seo-plan/redirects.php, priority -1000) had their turn. Scanner noise (.php, .env,
 * wp-login…) is not logged. The log keeps the 300 most recent addresses in one option that is not autoloaded.
 */
if (!defined('ABSPATH')) { exit; }

const ZP_SUITE3_404_LOG = 'zp_suite3_404_log';
const ZP_SUITE3_REDIRECTS = 'zp_suite3_redirects';

/** Request path with a trailing slash (unless it names a file), without the query string. */
function zp_suite3_404_path(string $uri = ''): string {
  $uri = $uri !== '' ? $uri : (string) ($_SERVER['REQUEST_URI'] ?? '');
  $path = (string) wp_parse_url($uri, PHP_URL_PATH);
  $path = '/' . ltrim(rawurldecode($path), '/');
  if (strlen($path) > 300 || preg_match('/[\x00-\x1f<>"\\\\]/', $path)) { return ''; }
  if (substr($path, -1) !== '/' && !preg_match('~\.[a-z0-9]{2,5}$~i', $path)) { $path .= '/'; }
  return $path;
}

function zp_suite3_404_is_noise(string $path): bool {
  return (bool) preg_match('~(\.(php\d?|env|git|sql|bak|old|zip|tar|gz|rar|asp|aspx|jsp|cgi|ini|log|yml|yaml|config)$)|/\.|wp-login|xmlrpc|/wp-admin|/wp-includes|/wp-content/plugins/|/cgi-bin|phpmyadmin|/vendor/|/admin/|/administrator|/\.well-known/(?!security)~i', $path);
}

/** Saved redirects: path => ['to' => path or URL, 'at' => time, 'hits' => n]. */
function zp_suite3_redirects(): array {
  $r = get_option(ZP_SUITE3_REDIRECTS, []);
  return is_array($r) ? $r : [];
}

add_action('template_redirect', static function (): void {
  if (!is_404() || is_admin() || !in_array($_SERVER['REQUEST_METHOD'] ?? 'GET', ['GET', 'HEAD'], true)) { return; }
  // English requests: the language router hands WordPress the Polish path, the visitor asked for /en/….
  $english = class_exists('\\ZPL\\Router') && isset(\ZPL\Router::$lang, \ZPL\Router::$external) && \ZPL\Router::$lang === 'en';
  $path = zp_suite3_404_path($english ? (string) \ZPL\Router::$external : '');
  if ($path === '') { return; }
  $redirects = zp_suite3_redirects();
  if (isset($redirects[$path]['to']) && !headers_sent()) {
    $to = (string) $redirects[$path]['to'];
    $redirects[$path]['hits'] = (int) ($redirects[$path]['hits'] ?? 0) + 1;
    $redirects[$path]['last'] = time();
    update_option(ZP_SUITE3_REDIRECTS, $redirects, false);
    $query = (string) wp_parse_url((string) ($_SERVER['REQUEST_URI'] ?? ''), PHP_URL_QUERY);
    // The target is exactly what was saved in the panel; the router must not translate it again.
    if (class_exists('\\ZPL\\Runtime')) { remove_filter('wp_redirect', ['ZPL\\Runtime', 'localize_redirect'], PHP_INT_MAX); }
    wp_safe_redirect((strpos($to, '/') === 0 ? home_url($to) : $to) . ($query !== '' && strpos($to, '?') === false ? '?' . $query : ''), 301, 'ZP Suite');
    exit;
  }
  // A redirect added later must work at once, so the server cache keeps no copy of 404 pages.
  if (!headers_sent()) { header('X-LiteSpeed-Cache-Control: no-cache'); }
  if (zp_suite3_404_is_noise($path)) { return; }
  zp_suite3_404_record($path);
}, 5);

function zp_suite3_404_record(string $path): void {
  $log = get_option(ZP_SUITE3_404_LOG, []);
  $log = is_array($log) ? $log : [];
  $now = time();
  $ua = (string) ($_SERVER['HTTP_USER_AGENT'] ?? '');
  $google = (bool) preg_match('~Googlebot|Google-InspectionTool|GoogleOther~i', $ua);
  $bot = $google || preg_match('~bot|crawl|spider|slurp|semrush|ahrefs|seobility|petal|bytespider|python|curl|wget|headless~i', $ua);
  // A burst of hits on one address (a bot retrying) costs one write per 10 seconds; Google's visits always count.
  if (!$google && isset($log[$path]['last']) && $now - (int) $log[$path]['last'] < 10) { return; }
  $entry = $log[$path] ?? ['n' => 0, 'google' => 0, 'bots' => 0, 'first' => $now, 'last' => 0, 'ref' => '', 'internal' => false];
  $entry['n']++; $entry['last'] = $now;
  if ($google) { $entry['google']++; }
  if ($bot) { $entry['bots']++; }
  $ref = (string) wp_get_raw_referer();
  // Opening the address from wp-admin (this panel) is not a broken link on the site.
  $refPath = (string) wp_parse_url($ref, PHP_URL_PATH);
  if ($ref !== '' && strpos($refPath, '/wp-admin') !== 0 && zp_suite3_404_path($ref) !== $path) {
    $host = (string) wp_parse_url($ref, PHP_URL_HOST); $own = (string) wp_parse_url(home_url('/'), PHP_URL_HOST);
    $entry['ref'] = substr($host . (string) wp_parse_url($ref, PHP_URL_PATH), 0, 200);
    if ($host !== '' && strcasecmp(preg_replace('~^www\.~', '', $host), preg_replace('~^www\.~', '', $own)) === 0) { $entry['internal'] = true; }
  }
  $log[$path] = $entry;
  if (count($log) > 300) {
    uasort($log, static fn($a, $b) => (int) ($b['last'] ?? 0) <=> (int) ($a['last'] ?? 0));
    $log = array_slice($log, 0, 280, true);
  }
  update_option(ZP_SUITE3_404_LOG, $log, false);
}

/** Counts for the dashboard and "Stan strony": open addresses in the last 30 days (no redirect, not hidden). */
function zp_suite3_404_summary(): array {
  $log = get_option(ZP_SUITE3_404_LOG, []); $log = is_array($log) ? $log : [];
  $redirects = zp_suite3_redirects(); $since = time() - 30 * DAY_IN_SECONDS;
  $out = ['open' => 0, 'google' => 0, 'internal' => 0, 'hits' => 0];
  foreach ($log as $path => $e) {
    if (!is_array($e) || (int) ($e['last'] ?? 0) < $since || isset($redirects[$path]) || !empty($e['hidden'])) { continue; }
    $out['open']++; $out['hits'] += (int) ($e['n'] ?? 0);
    if (!empty($e['google'])) { $out['google']++; }
    if (!empty($e['internal'])) { $out['internal']++; }
  }
  return $out;
}

/** Closest published address for a 404 path: same language, most similar last segment. */
function zp_suite3_404_suggest(string $path): string {
  static $pl = null, $en = null;
  $english = strpos($path, '/en/') === 0;
  if ($english) {
    if ($en === null) { $en = class_exists('\ZPL\Router') ? array_values(array_filter(array_map('strval', (array) \ZPL\Router::routes()))) : []; }
    $candidates = $en;
  } else {
    if ($pl === null) {
      $pl = get_transient('zp_suite3_404_paths');
      if (!is_array($pl)) {
        $pl = [];
        foreach (get_posts(['post_type' => ['page', 'post'], 'post_status' => 'publish', 'numberposts' => 1500, 'fields' => 'ids', 'suppress_filters' => true]) as $id) {
          $p = (string) wp_parse_url((string) get_permalink($id), PHP_URL_PATH);
          if ($p !== '') { $pl[] = $p; }
        }
        set_transient('zp_suite3_404_paths', $pl, HOUR_IN_SECONDS);
      }
    }
    $candidates = $pl;
  }
  $parts = array_values(array_filter(explode('/', trim($path, '/')), 'strlen'));
  $slug = (string) end($parts);
  if ($slug === '' || !$candidates) { return ''; }
  $best = ''; $score = 0.0;
  foreach ($candidates as $candidate) {
    $c = array_values(array_filter(explode('/', trim($candidate, '/')), 'strlen'));
    $cslug = (string) end($c);
    if ($cslug === '') { continue; }
    similar_text($slug, $cslug, $pct);
    if (str_contains($cslug, $slug) || str_contains($slug, $cslug)) { $pct = max($pct, 80.0); }
    if ($pct > $score) { $score = $pct; $best = $candidate; }
  }
  // A weak match is worse than the section the address came from (a removed case study goes to /realizacje/).
  $section = count($parts) > ($english ? 2 : 1) ? '/' . implode('/', array_slice($parts, 0, $english ? 2 : 1)) . '/' : '';
  if ($score < 75 && $section !== '' && in_array($section, $candidates, true)) { return $section; }
  return $score >= 55 ? $best : '';
}

/** Target typed in the panel: a path on this site, or a full URL on this site. Anything else is refused. */
function zp_suite3_redirect_target(string $raw): string {
  $raw = trim($raw);
  if ($raw === '') { return ''; }
  $home = (string) wp_parse_url(home_url('/'), PHP_URL_HOST);
  if (preg_match('~^https?://~i', $raw)) {
    if (strcasecmp((string) wp_parse_url($raw, PHP_URL_HOST), $home) !== 0) { return ''; }
    $raw = (string) wp_parse_url($raw, PHP_URL_PATH) . (($q = wp_parse_url($raw, PHP_URL_QUERY)) ? '?' . $q : '');
  }
  if ($raw === '' || $raw[0] !== '/' || strpos($raw, '//') === 0) { return ''; }
  return esc_url_raw($raw) !== '' ? $raw : '';
}

add_action('admin_post_zp_suite3_404', static function (): void {
  if (!current_user_can('manage_options')) { wp_die('Brak uprawnień.', 'ZP Suite', ['response' => 403]); }
  check_admin_referer('zp_suite3_404');
  $do = sanitize_key((string) ($_POST['do'] ?? ''));
  $from = zp_suite3_404_path((string) wp_unslash($_POST['from'] ?? ''));
  $notice = 'error';
  if ($do === 'redirect' && $from !== '') {
    $to = zp_suite3_redirect_target((string) wp_unslash($_POST['to'] ?? ''));
    if ($to !== '' && rtrim($to, '/') !== rtrim($from, '/')) {
      $r = zp_suite3_redirects(); $r[$from] = ['to' => $to, 'at' => time(), 'hits' => 0];
      update_option(ZP_SUITE3_REDIRECTS, $r, false); $notice = 'added';
    }
  } elseif ($do === 'delete' && $from !== '') {
    $r = zp_suite3_redirects(); unset($r[$from]); update_option(ZP_SUITE3_REDIRECTS, $r, false); $notice = 'deleted';
  } elseif (in_array($do, ['hide', 'unhide'], true) && $from !== '') {
    $log = get_option(ZP_SUITE3_404_LOG, []);
    if (is_array($log) && isset($log[$from])) { $log[$from]['hidden'] = $do === 'hide'; update_option(ZP_SUITE3_404_LOG, $log, false); $notice = 'hidden'; }
  } elseif ($do === 'clear') {
    update_option(ZP_SUITE3_404_LOG, [], false); $notice = 'cleared';
  }
  wp_safe_redirect(zp_suite3_url('zp-suite-404', ['zp_notice' => $notice]));
  exit;
});

function zp_suite3_404_form(string $do, string $from, string $inner, string $label, string $class = 'button'): string {
  $hidden = strpos($inner, 'name="from"') === false ? '<input type="hidden" name="from" value="' . esc_attr($from) . '">' : '';
  return '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '" class="inline-form"><input type="hidden" name="action" value="zp_suite3_404">' . wp_nonce_field('zp_suite3_404', '_wpnonce', true, false) . '<input type="hidden" name="do" value="' . esc_attr($do) . '">' . $hidden . $inner . '<button class="' . esc_attr($class) . '">' . esc_html($label) . '</button></form>';
}

function zp_suite3_404_page(): void {
  if (!current_user_can('manage_options')) { return; }
  $log = get_option(ZP_SUITE3_404_LOG, []); $log = is_array($log) ? $log : [];
  $redirects = zp_suite3_redirects();
  $showHidden = !empty($_GET['zp_hidden']);
  zp_suite3_open('Błędy 404 i przekierowania', 'Adresy, pod którymi ktoś trafił na „nie znaleziono”, i przekierowania 301 dodane w panelu.');
  $messages = ['added' => 'Przekierowanie dodane. Działa od razu.', 'deleted' => 'Przekierowanie usunięte.', 'hidden' => 'Zapisano.', 'cleared' => 'Lista błędów wyczyszczona.', 'error' => 'Nie zapisano. Cel musi być adresem na tej stronie, np. /tworzenie-stron-internetowych/.'];
  $n = isset($_GET['zp_notice']) ? sanitize_key((string) $_GET['zp_notice']) : '';
  if (isset($messages[$n])) { echo '<div class="zpAdmin20__notice" role="status">' . esc_html($messages[$n]) . '</div>'; }
  $s = zp_suite3_404_summary();
  echo '<section class="metrics"><section class="metric"><div class="metric-head"><span>Adresy z błędem 404</span></div><div class="metric-value">' . (int) $s['open'] . '</div><div class="metric-foot">ostatnie 30 dni, bez przekierowania</div></section><section class="metric"><div class="metric-head"><span>Odwiedzane przez Google</span></div><div class="metric-value">' . (int) $s['google'] . '</div><div class="metric-foot">te warto przekierować najpierw</div></section><section class="metric"><div class="metric-head"><span>Z linku na Twojej stronie</span></div><div class="metric-value">' . (int) $s['internal'] . '</div><div class="metric-foot">zepsuty link wewnętrzny</div></section><section class="metric"><div class="metric-head"><span>Przekierowania z panelu</span></div><div class="metric-value">' . count($redirects) . '</div><div class="metric-foot">poza przekierowaniami planu SEO</div></section></section>';

  uasort($log, static function ($a, $b): int {
    $wa = (int) ($a['google'] ?? 0) * 3 + (!empty($a['internal']) ? 50 : 0) + (int) ($a['n'] ?? 0);
    $wb = (int) ($b['google'] ?? 0) * 3 + (!empty($b['internal']) ? 50 : 0) + (int) ($b['n'] ?? 0);
    return $wb <=> $wa ?: ((int) ($b['last'] ?? 0) <=> (int) ($a['last'] ?? 0));
  });
  echo '<section class="card"><header class="card-head"><div><h2>Adresy z błędem 404</h2><p>Najpierw te z linkiem na Twojej stronie i odwiedzane przez Google. Podpowiedź to najbardziej podobny istniejący adres; możesz go zmienić przed zapisaniem.</p></div>' . ($showHidden ? '<a class="button" href="' . esc_url(zp_suite3_url('zp-suite-404')) . '">Ukryj pominięte</a>' : '<a class="button" href="' . esc_url(zp_suite3_url('zp-suite-404', ['zp_hidden' => 1])) . '">Pokaż pominięte</a>') . '</header>';
  $rows = 0;
  echo '<div class="table-scroller"><table class="content-table"><thead><tr><th>Adres</th><th class="num">Wejścia</th><th>Ostatnio</th><th>Przekieruj na</th><th></th></tr></thead><tbody>';
  foreach ($log as $path => $e) {
    if (!is_array($e) || isset($redirects[$path]) || (!$showHidden && !empty($e['hidden']))) { continue; }
    $rows++;
    $suggest = zp_suite3_404_suggest((string) $path);
    $meta = [];
    if (!empty($e['internal'])) { $meta[] = '<span class="tag is-bad">link na Twojej stronie</span>'; }
    if (!empty($e['google'])) { $meta[] = '<span class="tag is-warn">Google: ' . (int) $e['google'] . '</span>'; }
    if (!empty($e['ref']) && empty($e['internal'])) { $meta[] = '<small>z: ' . esc_html((string) $e['ref']) . '</small>'; }
    elseif (!empty($e['ref'])) { $meta[] = '<small>link na: ' . esc_html((string) $e['ref']) . '</small>'; }
    echo '<tr><td><strong><a href="' . esc_url(home_url((string) $path)) . '" target="_blank" rel="noopener noreferrer">' . esc_html((string) $path) . '</a></strong>' . ($meta ? '<div style="margin-top:6px;display:flex;gap:6px;flex-wrap:wrap;align-items:center">' . implode('', $meta) . '</div>' : '') . '</td><td class="num">' . (int) ($e['n'] ?? 0) . '</td><td>' . esc_html(human_time_diff((int) ($e['last'] ?? time()))) . ' temu</td><td>';
    echo zp_suite3_404_form('redirect', (string) $path, '<label class="screen-reader-text" for="zp404-' . md5((string) $path) . '">Cel przekierowania</label><input type="text" id="zp404-' . md5((string) $path) . '" name="to" value="' . esc_attr($suggest) . '" placeholder="/adres-docelowy/">', 'Przekieruj', 'button primary');
    echo '</td><td>' . zp_suite3_404_form(!empty($e['hidden']) ? 'unhide' : 'hide', (string) $path, '', !empty($e['hidden']) ? 'Przywróć' : 'Pomiń', 'button ghost') . '</td></tr>';
  }
  echo '</tbody></table></div>';
  if (!$rows) { echo '<div class="card-body"><div class="zpA-empty"><b>Brak adresów do poprawy.</b><p>Lista zbiera się od instalacji wersji 3.0. Adresy ze skanerów (np. .php, wp-login) są pomijane.</p></div></div>'; }
  echo '<div class="subtle-foot">Lista trzyma 300 ostatnich adresów. ' . zp_suite3_404_form('clear', '/', '', 'Wyczyść listę', 'button ghost') . '</div></section>';

  echo '<section class="card" style="margin-top:20px"><header class="card-head"><div><h2>Przekierowania 301 z panelu</h2><p>Działają tylko dla adresów, których nie ma na stronie (istniejących stron nie przesłonią).</p></div></header>';
  if ($redirects) {
    uasort($redirects, static fn($a, $b): int => (int) ($b['at'] ?? 0) <=> (int) ($a['at'] ?? 0));
    echo '<div class="table-scroller"><table class="content-table"><thead><tr><th>Stary adres</th><th>Nowy adres</th><th class="num">Użyte</th><th>Dodane</th><th></th></tr></thead><tbody>';
    foreach ($redirects as $from => $r) {
      if (!is_array($r)) { continue; }
      $to = (string) ($r['to'] ?? '');
      echo '<tr><td><strong>' . esc_html((string) $from) . '</strong></td><td><a href="' . esc_url(strpos($to, '/') === 0 ? home_url($to) : $to) . '" target="_blank" rel="noopener noreferrer">' . esc_html($to) . '</a></td><td class="num">' . (int) ($r['hits'] ?? 0) . '</td><td>' . esc_html(zp_suite3_date($r['at'] ?? null)) . '</td><td>' . zp_suite3_404_form('delete', (string) $from, '', 'Usuń', 'button ghost') . '</td></tr>';
    }
    echo '</tbody></table></div>';
  }
  $add = '<label for="zp404-new-from">Stary adres</label><input type="text" id="zp404-new-from" name="from" placeholder="/stary-adres/" required>'
    . '<label for="zp404-new-to">Nowy adres</label><input type="text" id="zp404-new-to" name="to" placeholder="/nowy-adres/" required>';
  echo '<div class="card-body zpS3-add">' . zp_suite3_404_form('redirect', '', $add, 'Dodaj przekierowanie', 'button primary') . '</div>';
  echo '</section>';
  zp_suite3_close();
}
