<?php
if (!defined('ABSPATH')) { exit; }

/** Scoped, backed-up repairs from the 9 October audit; independent of the broad SEO-plan migration. */
const ZP_RANKMATH_REPAIR_2914_BACKUP = 'zp_rankmath_repair_2914_backup';
const ZP_RANKMATH_REPAIR_2914_RESULT = 'zp_rankmath_repair_2914_result';
const ZP_RANKMATH_REPAIR_2914_LOCK = 'zp_rankmath_repair_2914_lock';

/** Only the 23 page paths edited in the audit's native Rank Math bulk editor. */
function zp_rankmath_repair_2914_focus_paths(): array {
  return [
    '/agencja-reklamowa-katowice/',
    '/identyfikacja-wizualna/',
    '/kampanie-reklamowe/',
    '/logo-branding-katowice/',
    '/opieka-wordpress/',
    '/projektowanie-logo/',
    '/sklepy-internetowe-katowice/',
    '/strona-wizytowka/',
    '/strony-internetowe-dla-deweloperow/',
    '/strony-internetowe-dla-fotografow/',
    '/strony-internetowe-dla-hoteli/',
    '/strony-internetowe-dla-kancelarii/',
    '/strony-internetowe-dla-lekarzy/',
    '/strony-internetowe-dla-restauracji/',
    '/strony-internetowe-dla-salonow-beauty/',
    '/strony-internetowe-dla-trenerow-personalnych/',
    '/strony-internetowe-katowice/',
    '/',
    '/strony-wordpress/',
    '/studio-wyceny/',
    '/tworzenie-landing-page/',
    '/tworzenie-sklepow-internetowych/',
    '/tworzenie-stron-internetowych/',
  ];
}

function zp_rankmath_repair_2914_article_targets(): array {
  return [
    '/sklepy-internetowe/seo-lokalne-w-katowicach-jak-pozyskiwac-klientow-z-miasta-i-okolic/' => [
      'focus' => 'seo lokalne katowice',
      'title' => 'SEO lokalne w Katowicach: jak zdobywać klientów | Zaprojektowani',
      'description' => 'Sprawdź, jak SEO lokalne w Katowicach pomaga zdobywać klientów. Poznaj rolę strony, Profilu Firmy w Google, opinii i treści dopasowanych do usług.',
      'en_title' => 'Local SEO in Katowice: how to win clients | Zaprojektowani',
      'en_description' => 'Learn how local SEO in Katowice helps attract customers. Explore your website, Google Business Profile, reviews and content matched to your services.',
    ],
    '/sklepy-internetowe/strona-internetowa-dla-firmy-z-katowic-co-powinna-zawierac-zeby-zdobywac-lokalnych-klientow/' => [
      'focus' => 'strona internetowa firmy z katowic',
      'title' => 'Strona firmy z Katowic: co powinna zawierać | Zaprojektowani',
      'description' => 'Zobacz, co powinna zawierać strona internetowa firmy z Katowic: czytelną ofertę, lokalny kontekst, opinie, mocne CTA i łatwy kontakt z firmą.',
      'en_title' => 'Katowice business website: essential elements | Zaprojektowani',
      'en_description' => 'See what a Katowice business website should include: a clear offer, local context, reviews, strong calls to action and an easy way to get in touch.',
    ],
  ];
}

function zp_rankmath_repair_2914_tokens(string $value): array {
  return array_values(array_filter(array_map('trim', explode(',', $value)), static function ($v) { return $v !== ''; }));
}

/** Reject custom primaries or secondary phrases, accepting only the known bulk-editor artefact. */
function zp_rankmath_repair_2914_focus_safe($current, string $expected): bool {
  if (!is_string($current)) { return false; }
  $want = zp_rankmath_repair_2914_tokens($expected);
  $have = zp_rankmath_repair_2914_tokens($current);
  if (!$want || !$have || $have[0] !== $want[0]) { return false; }
  foreach ($have as $token) {
    if (!in_array($token, $want, true) && $token !== 'number of tags exceeded') { return false; }
  }
  return true;
}

/** Persist and read back the backup before any field or dictionary write. */
function zp_rankmath_repair_2914_save_backup(array $backup): bool {
  update_option(ZP_RANKMATH_REPAIR_2914_BACKUP, $backup, false);
  return get_option(ZP_RANKMATH_REPAIR_2914_BACKUP) === $backup;
}

function zp_rankmath_repair_2914_write_meta(int $id, string $path, string $key, string $value, array &$backup): string {
  if (get_post_meta($id, $key, true) === $value) { return 'unchanged'; }
  if (!isset($backup['posts'][$id])) {
    $rankmath = [];
    foreach ((array) get_post_meta($id) as $meta_key => $rows) {
      if (strpos((string) $meta_key, 'rank_math_') === 0) { $rankmath[$meta_key] = get_post_meta($id, $meta_key, false); }
    }
    $backup['posts'][$id] = ['id' => $id, 'path' => $path, 'rank_math_meta' => $rankmath];
  }
  $slot = $id . ':' . $key;
  if (!isset($backup['fields'][$slot])) {
    $backup['fields'][$slot] = ['id' => $id, 'path' => $path, 'key' => $key, 'existed' => metadata_exists('post', $id, $key), 'old_values' => get_post_meta($id, $key, false), 'written' => $value];
  }
  if (!zp_rankmath_repair_2914_save_backup($backup)) { return 'backup_failed'; }
  update_post_meta($id, $key, wp_slash($value));
  if (get_post_meta($id, $key, true) !== $value) { return 'write_failed'; }
  clean_post_cache($id);
  return 'updated';
}

/** Add only new exact metadata translations to the existing Languages override storage. */
function zp_rankmath_repair_2914_translation(string $pl, string $en, array &$backup): string {
  if (!class_exists('\ZPL\Dict')) { return 'skipped_languages_unavailable'; }
  $overrides = get_option('zpl_overrides', []);
  if (!is_array($overrides)) { return 'skipped_dictionary_format'; }
  if (array_key_exists($pl, $overrides)) { return $overrides[$pl] === $en ? 'unchanged' : 'skipped_manual_translation'; }
  if (!isset($backup['options']['zpl_overrides'])) {
    $backup['options']['zpl_overrides'] = ['existed' => get_option('zpl_overrides', null) !== null, 'old' => $overrides, 'added' => []];
  }
  $backup['options']['zpl_overrides']['added'][$pl] = $en;
  if (!zp_rankmath_repair_2914_save_backup($backup)) { return 'backup_failed'; }
  $overrides[$pl] = $en;
  update_option('zpl_overrides', $overrides, false);
  if (get_option('zpl_overrides') !== $overrides) { return 'write_failed'; }
  \ZPL\Dict::flush();
  return 'updated';
}

/** Run only on an ordinary authenticated administrator request; successful runs never repeat. */
function zp_rankmath_repair_2914_run(): void {
  if (!is_admin() || !current_user_can('manage_options') || wp_doing_ajax() || wp_doing_cron()) { return; }
  if (!function_exists('zp_seo_plan_find_post') || !function_exists('zp_seo_plan_entry') || !function_exists('zp_seo_plan_path')) { return; }
  $previous = get_option(ZP_RANKMATH_REPAIR_2914_RESULT, []);
  if (is_array($previous) && ($previous['version'] ?? '') === '2.9.14' && !empty($previous['complete'])) { return; }
  $lock = (int) get_option(ZP_RANKMATH_REPAIR_2914_LOCK, 0);
  if ($lock && $lock < time() - 300) { delete_option(ZP_RANKMATH_REPAIR_2914_LOCK); }
  if (!add_option(ZP_RANKMATH_REPAIR_2914_LOCK, time(), '', false)) { return; }
  $backup = get_option(ZP_RANKMATH_REPAIR_2914_BACKUP, []);
  $backup = is_array($backup) ? $backup : [];
  $backup += ['version' => '2.9.14', 'created_utc' => gmdate('c'), 'posts' => [], 'fields' => [], 'options' => []];
  $report = ['version' => '2.9.14', 'started_utc' => gmdate('c'), 'complete' => false, 'focus' => [], 'articles' => [], 'translations' => [], 'errors' => []];
  try {
    foreach (zp_rankmath_repair_2914_focus_paths() as $path) {
      $id = (int) zp_seo_plan_find_post($path);
      $entry = zp_seo_plan_entry($path);
      $expected = is_array($entry) ? (string) ($entry['focus'] ?? '') : '';
      $state = 'skipped_missing_page';
      if ($id && get_post_type($id) === 'page' && get_post_status($id) === 'publish' && zp_seo_plan_path((string) get_permalink($id)) === $path) {
        $current = get_post_meta($id, 'rank_math_focus_keyword', true);
        if ($expected === '') { $state = 'skipped_empty_plan'; }
        elseif (!zp_rankmath_repair_2914_focus_safe($current, $expected)) { $state = 'skipped_manual_focus'; }
        else { $state = zp_rankmath_repair_2914_write_meta($id, $path, 'rank_math_focus_keyword', $expected, $backup); }
      }
      $report['focus'][] = ['id' => $id, 'path' => $path, 'state' => $state];
    }

    $wrong_title = 'Sklep internetowy na zamówienie – kiedy lepszy niż gotowy szablon';
    $wrong_description = 'Sprawdź, kiedy sklep internetowy na zamówienie daje większą przewagę niż gotowy szablon, dla jakich firm ma sens i jak wpływa na SEO, wydajność oraz integracje.';
    $shop_entry = zp_seo_plan_entry('/sklepy-internetowe/sklep-internetowy-na-zamowienie/');
    $shop_keywords = zp_rankmath_repair_2914_tokens(is_array($shop_entry) ? (string) ($shop_entry['focus'] ?? '') : '');
    foreach (zp_rankmath_repair_2914_article_targets() as $path => $target) {
      $id = (int) zp_seo_plan_find_post($path);
      if (!$id || get_post_type($id) !== 'post' || get_post_status($id) !== 'publish' || zp_seo_plan_path((string) get_permalink($id)) !== $path) {
        $report['articles'][] = ['id' => $id, 'path' => $path, 'state' => 'skipped_missing_post']; continue;
      }
      $focus = get_post_meta($id, 'rank_math_focus_keyword', true);
      $tokens = is_string($focus) ? zp_rankmath_repair_2914_tokens($focus) : [];
      $known_wrong = $shop_keywords && $tokens && $tokens[0] === 'sklep internetowy na zamówienie';
      foreach ($tokens as $token) {
        if (!in_array($token, $shop_keywords, true) && $token !== 'number of tags exceeded') { $known_wrong = false; }
      }
      if ($focus === $target['focus']) { $focus_state = 'unchanged'; }
      elseif (!$known_wrong) { $focus_state = 'skipped_manual_focus'; }
      else { $focus_state = zp_rankmath_repair_2914_write_meta($id, $path, 'rank_math_focus_keyword', $target['focus'], $backup); }
      $report['articles'][] = ['id' => $id, 'path' => $path, 'field' => 'focus_keyword', 'state' => $focus_state];
      $title = get_post_meta($id, 'rank_math_title', true);
      $slot = $id . ':rank_math_title';
      // An interrupted run may have saved the corrected title before saving the description.
      $resume = isset($backup['fields'][$slot]) && $title === $target['title'] && in_array($wrong_title, $backup['fields'][$slot]['old_values'] ?? [], true);
      if ($title !== $wrong_title && !$resume) {
        $report['articles'][] = ['id' => $id, 'path' => $path, 'state' => 'skipped_manual_title']; continue;
      }
      foreach (['title' => $wrong_title, 'description' => $wrong_description] as $field => $old_wrong) {
        $key = 'rank_math_' . $field;
        $current = get_post_meta($id, $key, true);
        if ($current === $target[$field]) { $state = 'unchanged'; }
        elseif ($current !== $old_wrong) { $state = 'skipped_manual_' . $field; }
        else { $state = zp_rankmath_repair_2914_write_meta($id, $path, $key, $target[$field], $backup); }
        $report['articles'][] = ['id' => $id, 'path' => $path, 'field' => $field, 'state' => $state];
        if ($state === 'updated' || $state === 'unchanged') {
          $tr = zp_rankmath_repair_2914_translation($target[$field], $target['en_' . $field], $backup);
          $report['translations'][] = ['path' => $path, 'field' => $field, 'state' => $tr];
        }
      }
    }
    foreach (['focus', 'articles', 'translations'] as $group) {
      foreach ($report[$group] as $row) {
        if (in_array($row['state'], ['backup_failed', 'write_failed'], true)) { $report['errors'][] = $group . ':' . $row['path'] . ':' . $row['state']; }
      }
    }
    $report['complete'] = !$report['errors'];
  } catch (\Throwable $e) {
    $report['errors'][] = $e->getMessage();
  } finally {
    $report['finished_utc'] = gmdate('c');
    update_option(ZP_RANKMATH_REPAIR_2914_RESULT, $report, false);
    $GLOBALS['zp_rankmath_repair_2914_notice'] = $report;
    delete_option(ZP_RANKMATH_REPAIR_2914_LOCK);
  }
}
add_action('admin_init', 'zp_rankmath_repair_2914_run', 101);

add_action('admin_notices', static function () {
  $report = $GLOBALS['zp_rankmath_repair_2914_notice'] ?? null;
  if (!current_user_can('manage_options') || !is_array($report)) { return; }
  $focus = count(array_filter($report['focus'], static function ($row) { return $row['state'] === 'updated'; }))
    + count(array_filter($report['articles'], static function ($row) { return ($row['field'] ?? '') === 'focus_keyword' && $row['state'] === 'updated'; }));
  $meta = count(array_filter($report['articles'], static function ($row) { return ($row['field'] ?? '') !== 'focus_keyword' && $row['state'] === 'updated'; }));
  $skips = 0;
  foreach (['focus', 'articles', 'translations'] as $group) {
    foreach ($report[$group] as $row) { if (strpos($row['state'], 'skipped_') === 0) { $skips++; } }
  }
  $message = sprintf('Zaprojektowani: naprawa Rank Math 2.9.14 — poprawione listy fraz: %d; tytuły/opisy: %d; pominięte pozycje: %d. Zachowano kopię poprzednich metadanych i raport wykonania.', $focus, $meta, $skips);
  echo '<div class="notice ' . (!empty($report['complete']) ? 'notice-success' : 'notice-error') . ' is-dismissible"><p>' . esc_html($message) . '</p></div>';
});
