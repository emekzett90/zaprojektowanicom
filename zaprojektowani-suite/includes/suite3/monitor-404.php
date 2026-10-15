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
/** 3.2.0: the check that a page really links to a 404 address (bots send our own address as Referer). */
const ZP_SUITE3_404_VERIFY = 'zp_suite3_404_verify';
const ZP_SUITE3_404_CHECK_UA = 'ZP Suite link check';

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
  // 3.1.0: an address the SEO plan marks as gone for good (410, e.g. /rutpoz_web/) is not a broken link.
  if (http_response_code() === 410 || zp_suite3_404_is_noise($path)) { return; }
  zp_suite3_404_record($path);
}, 5);

function zp_suite3_404_record(string $path): void {
  // The link check itself opens our pages; a page it finds missing is reported through its result, not logged again.
  if (stripos((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), ZP_SUITE3_404_CHECK_UA) !== false) { return; }
  $log = get_option(ZP_SUITE3_404_LOG, []);
  $log = is_array($log) ? $log : [];
  $now = time();
  $ua = (string) ($_SERVER['HTTP_USER_AGENT'] ?? '');
  $google = (bool) preg_match('~Googlebot|Google-InspectionTool|GoogleOther~i', $ua);
  $bot = $google || preg_match('~bot|crawl|spider|slurp|semrush|ahrefs|seobility|petal|bytespider|python|curl|wget|headless~i', $ua);
  // A burst of hits on one address (a bot retrying) costs one write per 10 seconds; Google's visits always count.
  if (!$google && isset($log[$path]['last']) && $now - (int) $log[$path]['last'] < 10) { return; }
  $entry = $log[$path] ?? ['n' => 0, 'google' => 0, 'bots' => 0, 'first' => $now, 'last' => 0, 'ref' => '', 'internal' => false];
  // 3.2.1: when Google last asked, so the dashboard can tell a page Google still looks for from an old visit. An entry
  // from before 3.2.1 keeps its last visit as Google's latest possible one before another visitor moves it.
  if (!$google && !empty($entry['google']) && !isset($entry['glast'])) { $entry['glast'] = (int) ($entry['last'] ?? 0); }
  $entry['n']++; $entry['last'] = $now;
  if ($google) { $entry['google']++; $entry['glast'] = $now; }
  if ($bot) { $entry['bots']++; }
  $ref = (string) wp_get_raw_referer();
  // Opening the address from wp-admin (this panel) is not a broken link on the site.
  $refPath = (string) wp_parse_url($ref, PHP_URL_PATH);
  if ($ref !== '' && strpos($refPath, '/wp-admin') !== 0 && zp_suite3_404_path($ref) !== $path) {
    $host = (string) wp_parse_url($ref, PHP_URL_HOST); $own = (string) wp_parse_url(home_url('/'), PHP_URL_HOST);
    $entry['ref'] = substr($host . (string) wp_parse_url($ref, PHP_URL_PATH), 0, 200);
    if ($host !== '' && strcasecmp(preg_replace('~^www\.~', '', $host), preg_replace('~^www\.~', '', $own)) === 0) {
      // 3.2.0: a Referer from our own site is only a lead. Scanners send it with addresses no page links to
      // (/ads.txt, /sellers.json…), so the link check opens that page and looks for the link before it counts.
      $iref = zp_suite3_404_path((string) wp_parse_url($ref, PHP_URL_PATH) ?: '/');   // "https://host" alone is the home page
      if ($iref !== '' && (empty($entry['internal']) || ($entry['iref'] ?? '') !== $iref)) { $entry['iref'] = $iref; $entry['linked'] = null; $entry['tries'] = 0; }
      $entry['internal'] = true;
    }
  }
  $log[$path] = $entry;
  if (count($log) > 300) {
    uasort($log, static fn($a, $b) => (int) ($b['last'] ?? 0) <=> (int) ($a['last'] ?? 0));
    $log = array_slice($log, 0, 280, true);
  }
  update_option(ZP_SUITE3_404_LOG, $log, false);
  if (!empty($entry['internal']) && ($entry['linked'] ?? null) === null) { zp_suite3_404_verify_soon(); }
}

/** The own-site page an entry came from: 3.2.0 keeps it as a path; older entries only have "host/path" in 'ref'. */
function zp_suite3_404_iref(array $e): string {
  if (!empty($e['iref'])) { return (string) $e['iref']; }
  $ref = (string) ($e['ref'] ?? ''); $slash = strpos($ref, '/');
  return $slash === false ? '/' : zp_suite3_404_path(substr($ref, $slash));
}

/** Entries with an own-site Referer whose link was not checked yet (or a week ago). */
function zp_suite3_404_unchecked(array $log): array {
  $out = []; $week = time() - 7 * DAY_IN_SECONDS;
  foreach ($log as $path => $e) {
    if (!is_array($e) || empty($e['internal']) || !empty($e['hidden'])) { continue; }
    $linked = $e['linked'] ?? null;
    if ($linked === null || (int) ($e['checked'] ?? 0) < $week) { $out[(string) $path] = zp_suite3_404_iref($e); }
  }
  return $out;
}

function zp_suite3_404_verify_soon(): void {
  if (function_exists('wp_next_scheduled') && !wp_next_scheduled(ZP_SUITE3_404_VERIFY)) { wp_schedule_single_event(time() + 60, ZP_SUITE3_404_VERIFY); }
}

/**
 * Own-site paths a page's HTML points to: links, images (srcset too), forms, and addresses written in scripts
 * (JSON escapes them as https:\/\/…). Paths are normalised like the log's keys.
 */
function zp_suite3_404_links(string $html, string $base): array {
  $own = strtolower((string) preg_replace('~^www\.~', '', (string) wp_parse_url(home_url('/'), PHP_URL_HOST)));
  $html = str_replace('\\/', '/', $html);
  $out = [];
  $add = static function (string $url) use (&$out, $own, $base): void {
    $url = trim(html_entity_decode($url, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    if ($url === '' || $url[0] === '#' || preg_match('~^(?:mailto|tel|javascript|data|blob|about):~i', $url)) { return; }
    if (strpos($url, '//') === 0) { $url = 'https:' . $url; }
    if (preg_match('~^[a-z][a-z0-9+.-]*:~i', $url)) {
      if (!preg_match('~^https?://~i', $url) || strtolower((string) preg_replace('~^www\.~', '', (string) wp_parse_url($url, PHP_URL_HOST))) !== $own) { return; }
      $path = (string) wp_parse_url($url, PHP_URL_PATH);
    } elseif ($url[0] === '/') {
      $path = (string) wp_parse_url($url, PHP_URL_PATH);
    } else {
      $path = (substr($base, -1) === '/' ? $base : dirname($base) . '/') . (string) wp_parse_url($url, PHP_URL_PATH);
    }
    $path = zp_suite3_404_path($path === '' ? '/' : $path);
    if ($path !== '') { $out[$path] = true; }
  };
  if (preg_match_all('~\s(href|src|data-src|data-href|action|poster|srcset|data-srcset|imagesrcset)\s*=\s*(["\'])(.*?)\2~is', $html, $m, PREG_SET_ORDER)) {
    foreach ($m as $a) {
      if (stripos($a[1], 'srcset') !== false) { foreach (explode(',', $a[3]) as $c) { $add((string) strtok(trim($c), " \t\n")); } }
      else { $add($a[3]); }
    }
  }
  // Full addresses of this site anywhere else in the page (scripts, JSON-LD, CSS url()).
  if (preg_match_all('~(?:https?:)?//(?:www\.)?' . preg_quote($own, '~') . '(/[^\s"\'<>()\\\\]*)~i', $html, $m)) {
    foreach ($m[1] as $p) { $add($p); }
  }
  return $out;
}

/**
 * 3.2.0: opens each own-site page that sent a visitor to a 404 address and records whether it really links there.
 * Up to 6 pages per run (WP-Cron); the rest runs a minute later. Our own pages only, read like a visitor without cookies.
 */
function zp_suite3_404_verify(): void {
  $log = get_option(ZP_SUITE3_404_LOG, []);
  if (!is_array($log)) { return; }
  $todo = zp_suite3_404_unchecked($log);
  $recheck = array_slice(zp_suite3_404_recheck_due($log), 0, 6);
  if (!$todo && !$recheck) { return; }
  $pages = array_slice(array_values(array_unique($todo)), 0, 6);
  $results = [];
  // 3.2.1: what each page address Google asked for answers now, without following redirects.
  $answers = [];
  foreach ($recheck as $path) {
    $response = wp_remote_head(home_url($path), [
      'timeout' => 15, 'redirection' => 0, 'user-agent' => ZP_SUITE3_404_CHECK_UA . '; ' . home_url('/'),
      'sslverify' => apply_filters('https_local_ssl_verify', false),
      'headers' => ['Accept-Language' => strpos($path, '/en/') === 0 ? 'en-US,en;q=0.9' : 'pl-PL,pl;q=0.9'],
    ]);
    $answers[$path] = is_wp_error($response) ? 0 : (int) wp_remote_retrieve_response_code($response);
  }
  foreach ($pages as $page) {
    $response = wp_remote_get(home_url($page), [
      'timeout' => 15, 'redirection' => 3, 'user-agent' => ZP_SUITE3_404_CHECK_UA . '; ' . home_url('/'),
      'sslverify' => apply_filters('https_local_ssl_verify', false),
      'headers' => ['Accept-Language' => strpos($page, '/en/') === 0 ? 'en-US,en;q=0.9' : 'pl-PL,pl;q=0.9'],
    ]);
    $code = is_wp_error($response) ? 0 : (int) wp_remote_retrieve_response_code($response);
    if ($code === 200) { $results[$page] = zp_suite3_404_links((string) wp_remote_retrieve_body($response), $page); }
    elseif (in_array($code, [404, 410], true)) { $results[$page] = []; }   // the page itself is gone: it links to nothing
    else { $results[$page] = null; }                                       // try again later (at most 3 times)
  }
  // Read again: visits, "Pomiń" or "Wyczyść listę" may have changed the log meanwhile; only the verdict fields are written.
  // The option is not autoloaded, so WordPress keeps the first read in memory: drop that copy to read the database.
  wp_cache_delete(ZP_SUITE3_404_LOG, 'options');
  $log = get_option(ZP_SUITE3_404_LOG, []);
  if (!is_array($log)) { return; }
  $now = time(); $changed = false;
  foreach ($todo as $path => $page) {
    if (!array_key_exists($page, $results) || !isset($log[$path]) || !is_array($log[$path]) || zp_suite3_404_iref($log[$path]) !== $page) { continue; }
    if ($results[$page] === null) {
      $log[$path]['tries'] = (int) ($log[$path]['tries'] ?? 0) + 1;
      // The page did not open three times in a row: not counted, the panel says so, and it is tried again in a week.
      if ($log[$path]['tries'] >= 3) { $log[$path]['linked'] = false; $log[$path]['checked'] = $now; $log[$path]['unreachable'] = true; }
    } else {
      $log[$path]['linked'] = isset($results[$page][$path]); $log[$path]['checked'] = $now; $log[$path]['tries'] = 0; unset($log[$path]['unreachable']);
    }
    $changed = true;
  }
  foreach ($answers as $path => $code) {
    if (!isset($log[$path]) || !is_array($log[$path])) { continue; }
    // Only a clear answer is kept: the page (2xx), a redirect (3xx), 404 or 410. A busy server (5xx), a refusal
    // (403, 429) or no answer is tried again; after 3 such tries the address counts as still missing.
    $final = ($code >= 200 && $code < 400) || $code === 404 || $code === 410;
    $log[$path]['rtries'] = $final ? 0 : (int) ($log[$path]['rtries'] ?? 0) + 1;
    if ($final || $log[$path]['rtries'] >= 3) { $log[$path]['now'] = $final ? $code : 0; $log[$path]['nowat'] = $now; $log[$path]['rtries'] = 0; }
    $changed = true;
  }
  if ($changed) {
    update_option(ZP_SUITE3_404_LOG, $log, false);
    // 3.2.1: the "Stan strony" badge counts again with the new results instead of keeping its count for 6 hours.
    delete_transient('zp_suite3_health_problems');
  }
  if (zp_suite3_404_unchecked($log) || zp_suite3_404_recheck_due($log)) { zp_suite3_404_verify_soon(); }
}
add_action(ZP_SUITE3_404_VERIFY, 'zp_suite3_404_verify');

/** Counts for the dashboard and "Stan strony": open addresses in the last 30 days (no redirect, not hidden). */
function zp_suite3_404_summary(): array {
  $log = get_option(ZP_SUITE3_404_LOG, []); $log = is_array($log) ? $log : [];
  $redirects = zp_suite3_redirects(); $since = time() - 30 * DAY_IN_SECONDS;
  $out = ['open' => 0, 'google' => 0, 'internal' => 0, 'hits' => 0, 'act' => 0];
  foreach ($log as $path => $e) {
    if (!is_array($e) || (int) ($e['last'] ?? 0) < $since || isset($redirects[$path]) || zp_suite3_404_plan_mapped((string) $path) || !empty($e['hidden'])) { continue; }
    $out['open']++; $out['hits'] += (int) ($e['n'] ?? 0);
    if (!empty($e['google'])) { $out['google']++; }
    // 3.2.0: only a link the check found on the page counts; a Referer alone does not (scanners send ours).
    if (!empty($e['internal']) && ($e['linked'] ?? null) === true) { $out['internal']++; }
    if (zp_suite3_404_actionable((string) $path, $e, $since)) { $out['act']++; }
  }
  if (zp_suite3_404_unchecked($log) || zp_suite3_404_recheck_due($log)) { zp_suite3_404_verify_soon(); }
  return $out;
}

/**
 * 3.2.1: a page address (no file name, or .html) that Google asked for in the last 30 days and that the SEO plan's
 * own map does not redirect or mark as gone. Files (/ads.txt, old pictures), scanner requests and the home page
 * (a 404 there is a query such as /?p=…) are listed in the panel but are not a task. Entries from before 3.2.1 have
 * no 'glast': their last visit counts instead.
 */
function zp_suite3_404_candidate(string $path, array $e, int $since): bool {
  if ($path === '/' || !empty($e['hidden'])) { return false; }
  $glast = (int) ($e['glast'] ?? (!empty($e['google']) ? ($e['last'] ?? 0) : 0));
  if ($glast < $since) { return false; }
  // A file name in the last segment, also with a slash added (/site.webmanifest/), except .html pages.
  if (preg_match('~\.[a-z0-9]{2,11}/?$~i', $path) && !preg_match('~\.html?/?$~i', $path)) { return false; }
  return !zp_suite3_404_plan_mapped($path);
}

/** 3.2.1: the SEO plan's own map redirects the address or marks it as gone (logged before that entry existed). */
function zp_suite3_404_plan_mapped(string $path): bool {
  return function_exists('zp_seo_plan_data') && isset(zp_seo_plan_data('redirects')['exact'][$path]);
}

/** What the address answered when the background check last opened it, if that was after its last 404 visit. */
function zp_suite3_404_answers_now(array $e): int {
  return isset($e['now'], $e['nowat']) && (int) $e['nowat'] >= (int) ($e['last'] ?? 0) ? (int) $e['now'] : 0;
}

/**
 * 3.2.1: an address worth a redirect: a candidate that still answers 404. The background check opens each candidate
 * (a page published again, a redirect 3.2.0 or Rank Math added, an English address the language module now sends
 * to /en/), so the dashboard does not keep counting what is already fixed.
 */
function zp_suite3_404_actionable(string $path, array $e, int $since): bool {
  if (!zp_suite3_404_candidate($path, $e, $since)) { return false; }
  $now = zp_suite3_404_answers_now($e);
  return $now === 0 || $now === 404;
}

/**
 * Candidates not opened since their last 404 visit (at most 3 failed tries; then they count as still missing), and
 * those that still answered 404 (or nothing) over 12 hours ago: a redirect in Rank Math, a page published again or a
 * new English address answer before a 404 is logged, so only a new check sees that they are fixed.
 */
function zp_suite3_404_recheck_due(array $log): array {
  $out = []; $since = time() - 30 * DAY_IN_SECONDS; $stale = time() - 12 * HOUR_IN_SECONDS; $redirects = zp_suite3_redirects();
  foreach ($log as $path => $e) {
    if (!is_array($e) || isset($redirects[$path]) || !zp_suite3_404_candidate((string) $path, $e, $since)) { continue; }
    $nowat = (int) ($e['nowat'] ?? 0);
    if ($nowat < (int) ($e['last'] ?? 0) || (in_array((int) ($e['now'] ?? 0), [0, 404], true) && $nowat < $stale)) { $out[] = (string) $path; }
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
  echo '<section class="metrics"><section class="metric"><div class="metric-head"><span>Adresy z błędem 404</span></div><div class="metric-value">' . (int) $s['open'] . '</div><div class="metric-foot">ostatnie 30 dni, bez przekierowania</div></section><section class="metric"><div class="metric-head"><span>Do przekierowania</span></div><div class="metric-value">' . (int) $s['act'] . '</div><div class="metric-foot">strony, o które Google pytał w 30 dniach</div></section><section class="metric"><div class="metric-head"><span>Z linku na Twojej stronie</span></div><div class="metric-value">' . (int) $s['internal'] . '</div><div class="metric-foot">zepsuty link wewnętrzny</div></section><section class="metric"><div class="metric-head"><span>Przekierowania z panelu</span></div><div class="metric-value">' . count($redirects) . '</div><div class="metric-foot">poza przekierowaniami planu SEO</div></section></section>';

  // 3.2.1: links on the site first (the red dashboard row), then the addresses the yellow row counts ("do przekierowania"),
  // then the rest by Google visits and hits. The mark is not saved.
  $since = time() - 30 * DAY_IN_SECONDS;
  foreach ($log as $p => $x) { if (is_array($x)) { $log[$p]['_act'] = zp_suite3_404_actionable((string) $p, $x, $since); } }
  uasort($log, static function ($a, $b): int {
    $tier = static fn(array $e): int => !empty($e['internal']) && ($e['linked'] ?? null) === true ? 2 : (!empty($e['_act']) ? 1 : 0);
    $w = static fn(array $e): int => (int) ($e['google'] ?? 0) * 3 + (int) ($e['n'] ?? 0);
    return ($tier($b) <=> $tier($a)) ?: (($w($b) <=> $w($a)) ?: ((int) ($b['last'] ?? 0) <=> (int) ($a['last'] ?? 0)));
  });
  echo '<section class="card"><header class="card-head"><div><h2>Adresy z błędem 404</h2><p>Najpierw te z linkiem na Twojej stronie, potem adresy do przekierowania (strony, o które Google pytał w ostatnich 30 dniach). Podpowiedź to najbardziej podobny istniejący adres; możesz go zmienić przed zapisaniem.</p></div>' . ($showHidden ? '<a class="button" href="' . esc_url(zp_suite3_url('zp-suite-404')) . '">Ukryj pominięte</a>' : '<a class="button" href="' . esc_url(zp_suite3_url('zp-suite-404', ['zp_hidden' => 1])) . '">Pokaż pominięte</a>') . '</header>';
  $rows = 0;
  echo '<div class="table-scroller"><table class="content-table"><thead><tr><th>Adres</th><th class="num">Wejścia</th><th>Ostatnio</th><th>Przekieruj na</th><th></th></tr></thead><tbody>';
  foreach ($log as $path => $e) {
    // 3.2.1: an address the SEO plan redirects now (logged before that) is fixed, like one with a panel redirect.
    if (!is_array($e) || isset($redirects[$path]) || zp_suite3_404_plan_mapped((string) $path) || (!$showHidden && !empty($e['hidden']))) { continue; }
    $rows++;
    $suggest = zp_suite3_404_suggest((string) $path);
    $meta = [];
    $linked = !empty($e['internal']) ? ($e['linked'] ?? null) : false;
    if ($linked === true) { $meta[] = '<span class="tag is-bad">link na Twojej stronie</span>'; }
    if (!empty($e['_act'])) { $meta[] = '<span class="tag is-warn">do przekierowania</span>'; }
    if (!empty($e['google'])) { $meta[] = '<span class="tag is-warn">Google: ' . (int) $e['google'] . '</span>'; }
    $answers = zp_suite3_404_answers_now($e);
    if ($answers > 0 && $answers !== 404) { $meta[] = '<small>teraz odpowiada ' . $answers . ($answers >= 300 && $answers < 400 ? ' (przekierowanie)' : '') . '</small>'; }
    if ($linked === true) { $meta[] = '<small>link na: ' . esc_html(zp_suite3_404_iref($e)) . '</small>'; }
    elseif (!empty($e['internal'])) { $meta[] = '<small>z: ' . esc_html(zp_suite3_404_iref($e)) . ($linked === null ? ' · sprawdzam, czy ta strona ma ten link' : (!empty($e['unreachable']) ? ' · nie udało się otworzyć tej strony, sprawdzę ponownie za tydzień' : ' · ta strona nie ma tego linku (zapytanie robota)')) . '</small>'; }
    elseif (!empty($e['ref'])) { $meta[] = '<small>z: ' . esc_html((string) $e['ref']) . '</small>'; }
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
