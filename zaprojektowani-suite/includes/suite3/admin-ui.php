<?php
/**
 * ZP Suite 3.0 panel: one bar with the sections, flat styles and the dashboard. Loaded for admin requests only.
 *
 * Codex's pages keep their renderers: zp_admin20_open()/zp_admin20_close() (admin-experience) and the native-page
 * wrapper (admin-workspace-chrome) now draw this frame instead of the fixed side bar.
 */
if (!defined('ABSPATH')) { exit; }

function zp_suite3_translator_page(): string {
  $page = function_exists('zp_astra_translator_page') ? zp_astra_translator_page() : 'zp-tlumacz-en';
  return (string) apply_filters('zp_suite3_translator_page', $page);
}

/** Panel sections: key => [label, dashicon, [page slug => tab label]]. A section opens on its first page. */
function zp_suite3_sections(): array {
  $sections = [
    'dashboard' => ['Pulpit', 'dashboard', ['zp-suite' => 'Pulpit']],
    'content' => ['Treści', 'media-document', ['zp-suite-calendar' => 'Kalendarz', 'zp-suite-publikacja' => 'Publikacja wpisów', 'zp-suite-prepare' => 'Nowe artykuły', 'zp-suite-article-photos' => 'Zdjęcia artykułów']],
    'visibility' => ['Widoczność', 'chart-line', ['zp-suite-stats' => 'Google', 'zp-suite-positions' => 'Pozycje w Senuto', 'zp-suite-seo-plan' => 'Plan SEO', 'zp-suite-ai' => 'Widoczność AI']],
    'inquiries' => ['Zapytania', 'email-alt', ['zp-suite-zapytania' => 'Zapytania']],
    'translator' => ['Tłumacz EN', 'translation', [zp_suite3_translator_page() => 'Tłumacz EN']],
    'site' => ['Strona', 'admin-home', ['zp-suite-home-cms' => 'Strona główna', 'zp-suite-performance' => 'Wydajność', 'zp-suite-speed' => 'Przyspieszenie']],
    'health' => ['Stan strony', 'heart', ['zp-suite-health' => 'Stan strony', 'zp-suite-404' => 'Błędy 404 i przekierowania']],
    'settings' => ['Ustawienia', 'admin-generic', ['zp-suite-settings' => 'Ustawienia']],
  ];
  $sections = apply_filters('zp_suite3_sections', $sections);
  return is_array($sections) ? $sections : [];
}

/** Pages that belong to a section without a tab of their own. */
function zp_suite3_section_of(string $page): string {
  foreach (zp_suite3_sections() as $key => $section) {
    if (isset($section[2][$page])) { return $key; }
  }
  $extra = [
    'zp-suite-content-20261009' => 'content', 'zp-suite-photos-20261009' => 'content',
    'zp-suite-html-cache' => 'site',
    'zp-suite-translations' => 'translator', 'zp-tlumacz-en' => 'translator', 'zp-translator-v2' => 'translator',
    'zpl-languages' => 'translator', 'zpl-skaner' => 'translator',
  ];
  return $extra[$page] ?? '';
}

function zp_suite3_current_page(): string {
  return isset($_GET['page']) && is_string($_GET['page']) ? sanitize_key(wp_unslash($_GET['page'])) : '';
}

function zp_suite3_url(string $page, array $extra = []): string {
  return add_query_arg(array_merge(['page' => $page], $extra), admin_url('admin.php'));
}

function zp_suite3_num($value, int $decimals = 0): string {
  return is_numeric($value) ? number_format_i18n((float) $value, $decimals) : '—';
}

/** Polish plural: 1 adres, 2 adresy, 5 adresów (forms are given without the number). */
function zp_suite3_plural(int $n, string $one, string $few, string $many): string {
  $form = $n === 1 ? $one : (($n % 10 >= 2 && $n % 10 <= 4 && ($n % 100 < 12 || $n % 100 > 14)) ? $few : $many);
  return $n . ' ' . $form;
}

function zp_suite3_date($value): string {
  if (is_string($value) && $value !== '' && !is_numeric($value)) { $value = strtotime($value); }
  return is_numeric($value) && (int) $value > 0 ? wp_date('j.m.Y, H:i', (int) $value) : '—';
}

/** Small counts on the section tabs: new inquiries and problems found on "Stan strony". */
function zp_suite3_badge(string $section): int {
  if ($section === 'inquiries') { return function_exists('zp_inbox_new_count') ? max(0, (int) zp_inbox_new_count()) : 0; }
  if ($section === 'health' && function_exists('zp_suite3_health_problem_count')) { return zp_suite3_health_problem_count(); }
  return 0;
}

function zp_suite3_open(string $title, string $subtitle = ''): void {
  if (!current_user_can('manage_options')) { return; }
  $page = zp_suite3_current_page();
  $current = zp_suite3_section_of($page);
  $sections = zp_suite3_sections();
  $version = defined('ZP_SUITE_VERSION') ? ZP_SUITE_VERSION : '';
  echo '<div class="wrap zpAdmin20 zpA zpS3"><header class="zpS3-bar"><a class="zpS3-brand" href="' . esc_url(zp_suite3_url('zp-suite')) . '"><img src="' . esc_url(ZP_SUITE_URL . 'assets/campaigns/zp-sygnet-ciemny.webp') . '" alt="" width="32" height="32"><span><b>ZP Suite</b><small>wersja ' . esc_html($version) . '</small></span></a><nav class="zpS3-tabs" aria-label="ZP Suite">';
  foreach ($sections as $key => $section) {
    [$label, $icon, $pages] = $section;
    $count = zp_suite3_badge($key);
    $active = $key === $current;
    echo '<a href="' . esc_url(zp_suite3_url((string) array_key_first($pages))) . '"' . ($active ? ' class="is-active" aria-current="' . (isset($pages[$page]) && array_key_first($pages) === $page ? 'page' : 'true') . '"' : '') . '><span class="dashicons dashicons-' . esc_attr($icon) . '" aria-hidden="true"></span>' . esc_html($label);
    if ($count > 0) { echo '<span class="zpS3-count' . ($key === 'health' ? ' is-warn' : '') . '" aria-label="' . esc_attr($key === 'health' ? $count . ' do sprawdzenia' : $count . ' nowych') . '">' . (int) $count . '</span>'; }
    echo '</a>';
  }
  echo '</nav><a class="zpS3-site" href="' . esc_url(home_url('/')) . '" target="_blank" rel="noopener">Zobacz stronę ↗</a></header>';
  echo '<div class="zpS3-head"><div><h1>' . esc_html($title) . '</h1>' . ($subtitle !== '' ? '<p>' . esc_html($subtitle) . '</p>' : '') . '</div></div>';
  if ($current !== '' && count($sections[$current][2]) > 1) {
    echo '<nav class="zpS3-sub" aria-label="' . esc_attr($sections[$current][0]) . '">';
    foreach ($sections[$current][2] as $slug => $label) {
      echo '<a href="' . esc_url(zp_suite3_url($slug)) . '"' . ($slug === $page ? ' class="is-active" aria-current="page"' : '') . '>' . esc_html($label) . '</a>';
    }
    echo '</nav>';
  }
  echo '<hr class="wp-header-end"><main class="main" id="zp-astra-main">';
}

function zp_suite3_close(): void {
  if (!current_user_can('manage_options')) { return; }
  do_action('zp_admin20_close');
  $host = (string) wp_parse_url(home_url('/'), PHP_URL_HOST);
  echo '</main><footer class="zpS3-foot"><span>ZP Suite ' . esc_html(defined('ZP_SUITE_VERSION') ? ZP_SUITE_VERSION : '') . ' · ' . esc_html($host) . '</span><a href="' . esc_url(zp_suite3_url('zp-suite-health')) . '">Stan strony</a></footer></div>';
}

/** True on every screen drawn in the Suite frame (the same list Codex's styles used). */
function zp_suite3_route(): bool {
  $page = zp_suite3_current_page();
  return is_admin() && $page !== '' && (strpos($page, 'zp-suite') === 0 || zp_suite3_section_of($page) !== '');
}

add_action('admin_enqueue_scripts', static function (): void {
  if (!zp_suite3_route()) { return; }
  $file = ZP_SUITE_PATH . 'assets/admin/zp-suite-3.css';
  wp_enqueue_style('zp-suite-3-admin', ZP_SUITE_URL . 'assets/admin/zp-suite-3.css', [], ZP_SUITE_VERSION . '.' . (is_readable($file) ? filemtime($file) : '0'));
});

/* The WordPress menu shows one entry per section; the other pages stay registered (links and forms keep working). */
add_action('admin_menu', static function (): void {
  add_submenu_page('zp-suite', 'Stan strony', 'Stan strony', 'manage_options', 'zp-suite-health', 'zp_suite3_health_page');
  add_submenu_page('zp-suite', 'Błędy 404 i przekierowania', 'Błędy 404', 'manage_options', 'zp-suite-404', 'zp_suite3_404_page');
}, 20);

function zp_suite3_menu(): void {
  global $submenu;
  if (empty($submenu['zp-suite']) || !is_array($submenu['zp-suite'])) { return; }
  $order = []; $entry = [];
  foreach (zp_suite3_sections() as $section) {
    $first = (string) array_key_first($section[2]);
    $entry[$first] = $section[0];
    foreach (array_keys($section[2]) as $slug) { $order[] = (string) $slug; }
  }
  $rank = array_flip($order);
  foreach ($submenu['zp-suite'] as &$item) {
    $slug = (string) ($item[2] ?? '');
    if (isset($entry[$slug])) {
      // "Zapytania" keeps its own title, which carries the count of new messages.
      if ($slug !== 'zp-suite-zapytania') { $item[0] = $entry[$slug]; }
    } else {
      $item[4] = trim(($item[4] ?? '') . ' zp-s3-hidden');
    }
  }
  unset($item);
  usort($submenu['zp-suite'], static function ($a, $b) use ($rank): int {
    return ($rank[$a[2] ?? ''] ?? 999) <=> ($rank[$b[2] ?? ''] ?? 999);
  });
}
// Runs after every other admin_menu callback (the translator modules add and sort entries at the latest priorities).
add_action('admin_menu', static function (): void { add_action('admin_menu', 'zp_suite3_menu', PHP_INT_MAX); }, PHP_INT_MAX - 1);

add_filter('submenu_file', static function ($submenu_file, $parent_file) {
  if ($parent_file !== 'zp-suite') { return $submenu_file; }
  $section = zp_suite3_section_of(zp_suite3_current_page());
  $sections = zp_suite3_sections();
  return $section !== '' && isset($sections[$section]) ? (string) array_key_first($sections[$section][2]) : $submenu_file;
}, 10, 2);

add_action('admin_head', static function (): void {
  echo '<style id="zp-suite-3-menu">#adminmenu .zp-s3-hidden{display:none}</style>';
});

add_filter('admin_body_class', static function (string $classes): string {
  return zp_suite3_route() && strpos($classes, 'zpAdmin20-route') === false ? $classes . ' zpAdmin20-route' : $classes;
});

/* ---------- Dashboard ---------- */

/** SVG line for the last values of a series (no axes; the number above it is the reading). */
function zp_suite3_spark(array $values): string {
  $values = array_values(array_filter($values, 'is_numeric'));
  if (count($values) < 2) { return ''; }
  $max = max($values); $min = min($values); $span = $max - $min ?: 1; $n = count($values) - 1; $d = '';
  foreach ($values as $i => $v) { $d .= ($i ? 'L' : 'M') . round($i * 200 / $n, 1) . ' ' . round(30 - (($v - $min) / $span) * 28, 1); }
  return '<svg class="spark" viewBox="0 0 200 32" preserveAspectRatio="none" aria-hidden="true"><path d="' . esc_attr($d) . '"/></svg>';
}

function zp_suite3_metric(string $label, string $value, string $foot, string $icon, string $extra = ''): void {
  echo '<section class="metric"><div class="metric-head"><span>' . esc_html($label) . '</span><span class="dashicons dashicons-' . esc_attr($icon) . '" aria-hidden="true"></span></div><div class="metric-value">' . esc_html($value) . '</div><div class="metric-foot">' . $foot . '</div>' . $extra . '</section>';
}

/** Translator status for the dashboard. The translator module can answer the zp_suite3_translator_summary filter. */
function zp_suite3_translator_summary(): ?array {
  $summary = apply_filters('zp_suite3_translator_summary', null);
  if (is_array($summary)) { return $summary; }
  if (!function_exists('zp_astra_translator_status') || !function_exists('zp_astra_counts')) { return null; }
  $status = zp_astra_translator_status();
  if (!$status) { return null; }
  $c = zp_astra_counts($status);
  return ['done' => $c['published'], 'total' => $c['total'], 'pending' => $c['new'] + $c['changed'] + $c['active'], 'problems' => $c['attention'], 'last_run' => $status['scan']['completed_at'] ?? ($status['scan']['at'] ?? null), 'next_run' => $status['next_scan'] ?? null];
}

/** Senuto TOP3/TOP10 for the "Polska" project on desktop: latest day and the change against a week earlier. */
function zp_suite3_senuto_tops(): ?array {
  if (!class_exists('ZP_Senuto_Positions_20261010')) { return null; }
  try {
    $data = ZP_Senuto_Positions_20261010::cached(); $cfg = ZP_Senuto_Positions_20261010::config();
    $report = $data['reports'][($cfg['polska'] ?? '') . '_desktop'] ?? null;
    if (!is_array($report)) { return null; }
    $history = ZP_Senuto_Positions_20261010::history($report, 7);
    if (!$history) { return null; }
    $last = $history[count($history) - 1]; $first = $history[0];
    return ['top3' => $last['top3'] ?? null, 'top10' => $last['top10'] ?? null, 'top10_before' => $first['top10'] ?? null, 'day' => $last['day'] ?? ($report['range']['end'] ?? '')];
  } catch (Throwable $e) { return null; }
}

function zp_suite3_todo(): array {
  $todo = [];
  $row = static function (string $title, string $text, string $url, string $icon, string $level = '') use (&$todo): void {
    $todo[] = '<a class="action-row" href="' . esc_url($url) . '"><span class="mini-icon' . ($level !== '' ? ' is-' . esc_attr($level) : '') . '"><span class="dashicons dashicons-' . esc_attr($icon) . '" aria-hidden="true"></span></span><div class="body"><h3>' . esc_html($title) . '</h3><p>' . esc_html($text) . '</p></div><span class="row-arrow" aria-hidden="true">→</span></a>';
  };
  $new = function_exists('zp_inbox_new_count') ? (int) zp_inbox_new_count() : 0;
  if ($new > 0) { $row(zp_suite3_plural($new, 'nowe zapytanie', 'nowe zapytania', 'nowych zapytań') . ' od klientów', 'Formularze kontaktowe i Studio Wyceny, wspólna skrzynka.', zp_suite3_url('zp-suite-zapytania', ['status' => 'new']), 'email-alt'); }
  if (function_exists('zp_suite3_health_checks')) {
    foreach (zp_suite3_health_checks(false) as $check) {
      if (in_array($check['state'], ['bad', 'warn'], true)) { $row($check['title'], $check['text'], $check['url'] ?: zp_suite3_url('zp-suite-health'), $check['state'] === 'bad' ? 'warning' : 'flag', $check['state']); }
    }
  }
  $t = zp_suite3_translator_summary();
  if ($t && (int) ($t['problems'] ?? 0) > 0) { $row('Tłumaczenia EN do sprawdzenia', (int) $t['problems'] . ' treści z błędem lub bez wyniku.', zp_suite3_url(zp_suite3_translator_page()), 'translation', 'warn'); }
  if (class_exists('ZP_Admin_Queue_Health_20261010')) {
    try {
      $queues = ZP_Admin_Queue_Health_20261010::data();
      foreach (($queues['queues'] ?? []) as $key => $queue) {
        if (is_array($queue) && !empty($queue['issues'])) { $row((string) ($queue['label'] ?? 'Kolejka treści'), 'Kolejka zatrzymała się. Otwórz moduł, żeby zobaczyć przyczynę.', zp_suite3_url($key === 'photos' ? 'zp-suite-article-photos' : 'zp-suite-prepare'), 'update', 'warn'); }
      }
    } catch (Throwable $e) {}
  }
  return $todo;
}

function zp_suite3_dashboard(): void {
  if (!current_user_can('manage_options')) { return; }
  $stats = function_exists('zp_admin20_stats') ? zp_admin20_stats() : [];
  $gsc = is_array($stats['gsc'] ?? null) ? $stats['gsc'] : [];
  $totals = is_array($gsc['totals'] ?? null) ? $gsc['totals'] : [];
  $daily = is_array($gsc['daily'] ?? null) ? $gsc['daily'] : [];
  usort($daily, static fn($a, $b) => strcmp((string) ($a['date'] ?? ''), (string) ($b['date'] ?? '')));
  $range = !empty($gsc['start']) && !empty($gsc['end']) ? wp_date('j.m', strtotime((string) $gsc['start'])) . '–' . wp_date('j.m', strtotime((string) $gsc['end'])) : '28 dni';

  zp_suite3_open('Pulpit', 'Najważniejsze liczby i sprawy do zrobienia.');
  echo '<section class="metrics" aria-label="Podsumowanie">';
  zp_suite3_metric('Kliknięcia z Google', zp_suite3_num($totals['clicks'] ?? null), esc_html('Search Console · ' . $range), 'chart-line', zp_suite3_spark(array_column($daily, 'clicks')));
  zp_suite3_metric('Wyświetlenia w Google', zp_suite3_num($totals['impressions'] ?? null), esc_html('Search Console · ' . $range), 'visibility', zp_suite3_spark(array_column($daily, 'impressions')));
  $tops = zp_suite3_senuto_tops();
  $trend = '';
  if ($tops && is_numeric($tops['top10']) && is_numeric($tops['top10_before'])) {
    $diff = (int) $tops['top10'] - (int) $tops['top10_before'];
    $trend = $diff === 0 ? 'bez zmian w tygodniu' : '<span class="metric-trend ' . ($diff > 0 ? 'is-up' : 'is-down') . '">' . ($diff > 0 ? '+' : '−') . abs($diff) . '</span> w tygodniu';
  }
  zp_suite3_metric('Frazy w TOP 10', zp_suite3_num($tops['top10'] ?? null), $tops ? 'Senuto · Polska · ' . ($trend !== '' ? $trend : esc_html((string) $tops['day'])) : 'Senuto · brak pobranego raportu', 'awards');
  $new = function_exists('zp_inbox_new_count') ? (int) zp_inbox_new_count() : null;
  zp_suite3_metric('Nowe zapytania', zp_suite3_num($new), 'formularze i Studio Wyceny', 'email-alt');
  echo '</section>';

  $todo = zp_suite3_todo();
  echo '<div class="columns"><section class="card"><header class="card-head"><div><h2>Do zrobienia</h2><p>Sprawy, które czekają na Ciebie albo na decyzję.</p></div><span class="count">' . count($todo) . '</span></header><div class="action-list">';
  echo $todo ? implode('', array_slice($todo, 0, 10)) : '<div class="zpA-empty"><b>Nic nie czeka.</b><p>Nie ma nowych zapytań ani problemów na stronie.</p></div>';
  echo '</div><div class="subtle-foot">Stan z ' . esc_html(wp_date('j.m.Y, H:i')) . '. Pulpit tylko czyta zapisane dane, niczego nie uruchamia.</div></section><aside class="side-stack">';

  // Translator progress
  $t = zp_suite3_translator_summary();
  echo '<section class="card"><header class="card-head"><div><h2>Tłumacz EN</h2><p>Angielska wersja strony i wpisów.</p></div><a class="button" href="' . esc_url(zp_suite3_url(zp_suite3_translator_page())) . '">Otwórz</a></header><div class="card-body">';
  if ($t && (int) ($t['total'] ?? 0) > 0) {
    $done = (int) ($t['done'] ?? 0); $total = (int) $t['total']; $pct = (int) floor(100 * $done / max(1, $total));
    echo '<div class="progress' . ($pct >= 100 ? ' is-ok' : '') . '" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="' . $pct . '"><span style="width:' . $pct . '%"></span></div><div class="progress-meta"><span>' . esc_html($done . ' z ' . $total . ' gotowe (' . $pct . '%)') . '</span><span>' . esc_html((int) ($t['pending'] ?? 0) . ' w kolejce') . '</span></div>';
    echo '<p class="zpAdmin20__muted" style="margin-top:10px">Ostatni skan: ' . esc_html(zp_suite3_date($t['last_run'] ?? null)) . ' · następny: ' . esc_html(zp_suite3_date($t['next_run'] ?? null)) . '</p>';
  } else {
    echo '<p class="zpAdmin20__muted">Postęp pojawi się po pierwszym skanie tłumacza.</p>';
  }
  echo '</div></section>';

  // Next scheduled posts
  $future = get_posts(['post_type' => 'post', 'post_status' => 'future', 'numberposts' => 4, 'orderby' => 'date', 'order' => 'ASC', 'suppress_filters' => true]);
  echo '<section class="card"><header class="card-head"><div><h2>Najbliższe wpisy</h2><p>Zaplanowane do publikacji.</p></div><a class="button" href="' . esc_url(zp_suite3_url('zp-suite-calendar')) . '">Kalendarz</a></header><div class="status-list">';
  if ($future) {
    foreach ($future as $post) { echo '<a class="status-row" href="' . esc_url((string) get_edit_post_link($post->ID, 'raw')) . '"><span><b>' . esc_html(get_the_title($post)) . '</b><small>' . esc_html(wp_date('l, j.m.Y, H:i', (int) get_post_time('U', true, $post))) . '</small></span><span class="row-arrow" aria-hidden="true">→</span></a>'; }
  } else {
    echo '<div class="zpA-empty"><p>Brak zaplanowanych wpisów. Publikacja z kalendarza treści dodaje je sama.</p></div>';
  }
  echo '</div></section>';

  echo '<section class="navy-card"><div class="eyebrow">Nowe w wersji ' . esc_html(ZP_SUITE_VERSION) . '</div><h2>Lżejsza wtyczka i prostszy panel.</h2><p>Strona wczytuje mniej kodu, panel ma jedno menu u góry, a nowa zakładka „Stan strony” pokazuje problemy i błędy 404 z gotowymi przekierowaniami.</p><a class="button" href="' . esc_url(zp_suite3_url('zp-suite-health')) . '">Stan strony</a></section>';
  echo '</aside></div>';

  echo '<div class="quick-grid">';
  foreach ([
    ['chart-line', 'Widoczność w Google', 'Kliknięcia, frazy i strony z Search Console.', 'zp-suite-stats'],
    ['awards', 'Pozycje w Senuto', 'Frazy w TOP 3, TOP 10 i zmiany.', 'zp-suite-positions'],
    ['calendar-alt', 'Kalendarz treści', 'Wpisy zaplanowane i opublikowane.', 'zp-suite-calendar'],
  ] as [$icon, $title, $text, $slug]) {
    echo '<a class="quick" href="' . esc_url(zp_suite3_url($slug)) . '"><span class="dashicons dashicons-' . esc_attr($icon) . '" aria-hidden="true"></span><div><h3>' . esc_html($title) . '</h3><p>' . esc_html($text) . '</p></div></a>';
  }
  echo '</div>';
  zp_suite3_close();
}
