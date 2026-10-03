<?php
if (!defined('ABSPATH')) { exit; }

/**
 * One-time database changes of the keyword plan (runs once per plan version):
 *  1. creates the nationwide service pages (copies of the Katowice pages),
 *  2. moves merged duplicate posts to drafts (their URLs redirect with 301),
 *  3. fixes the "ampanie-…" typo slug,
 *  4. writes SEO titles, descriptions and focus keywords into Rank Math,
 *  5. sets the plan's H1 as the post title of the posts that get a new H1,
 *  6. marks thank-you pages noindex in Rank Math,
 *  7. clears the Rank Math sitemap cache and LiteSpeed cache.
 * Every changed value is backed up in zp_seo_plan_backup; Narzędzia → Plan SEO 2.3.0 can
 * restore them. Values changed by hand after the migration are never overwritten.
 */

const ZP_SEO_PLAN_BACKUP = 'zp_seo_plan_backup';
const ZP_SEO_PLAN_LOG = 'zp_seo_plan_log';

add_action('admin_init', function () {
  if (current_user_can('manage_options') && !wp_doing_ajax()) { zp_seo_plan_maybe_migrate(); }
}, 99);

// Also on the first ordinary page view, so the new pages exist even if nobody opens wp-admin.
add_action('wp_loaded', function () {
  if (is_admin() || wp_doing_ajax() || wp_doing_cron() || (defined('WP_CLI') && WP_CLI) || zp_seo_plan_is_en()) { return; }
  if (!in_array($_SERVER['REQUEST_METHOD'] ?? 'GET', ['GET', 'HEAD'], true)) { return; }
  zp_seo_plan_maybe_migrate();
}, 1);

function zp_seo_plan_maybe_migrate(): void {
  if (!zp_seo_plan_active() || get_option('zp_seo_plan_migrated') === ZP_SEO_PLAN_VERSION) { return; }
  $lock = (int) get_option('zp_seo_plan_lock', 0);
  if ($lock && $lock < time() - 300) { delete_option('zp_seo_plan_lock'); }
  if (!add_option('zp_seo_plan_lock', time(), '', false)) { return; }
  try {
    zp_seo_plan_migrate();
  } catch (\Throwable $e) {
    zp_seo_plan_log(['BŁĄD migracji: ' . $e->getMessage()]);
  } finally {
    delete_option('zp_seo_plan_lock');
  }
}

function zp_seo_plan_log(array $lines): void {
  $log = (array) get_option(ZP_SEO_PLAN_LOG, []);
  $stamp = wp_date('Y-m-d H:i');
  foreach ($lines as $line) { $log[] = $stamp . '  ' . $line; }
  update_option(ZP_SEO_PLAN_LOG, array_slice($log, -400), false);
}

function zp_seo_plan_backup(): array {
  return array_merge(['meta' => [], 'title' => [], 'status' => [], 'slug' => [], 'option' => []], (array) get_option(ZP_SEO_PLAN_BACKUP, []));
}

/** Published post or page at a path (0 if none). */
function zp_seo_plan_find_post(string $path): int {
  if ($path === '/') {
    return get_option('show_on_front') === 'page' ? (int) get_option('page_on_front') : 0;
  }
  $id = url_to_postid(home_url($path));
  if ($id && get_post_status($id) === 'publish' && zp_seo_plan_path((string) get_permalink($id)) === $path) { return $id; }
  $slug = sanitize_title(basename(trim($path, '/')));
  if ($slug === '') { return 0; }
  $found = get_posts(['name' => $slug, 'post_type' => ['post', 'page'], 'post_status' => 'publish', 'numberposts' => 5, 'fields' => 'ids', 'suppress_filters' => true]);
  foreach ($found as $candidate) {
    if (zp_seo_plan_path((string) get_permalink($candidate)) === $path) { return (int) $candidate; }
  }
  return 0;
}

/**
 * Writes a post meta value with a backup. $force: the plan's value wins over an existing
 * value on the first run. A value edited by hand after an earlier run is kept.
 */
function zp_seo_plan_set_meta(array &$backup, int $id, string $key, $value, bool $force): string {
  $slot = $id . ':' . $key;
  $current = get_post_meta($id, $key, true);
  if (isset($backup['meta'][$slot])) {
    if ($current !== $backup['meta'][$slot]['written']) { return 'kept'; }
    if ($current === $value) { return 'same'; }
  } else {
    if ($current === $value) { return 'same'; }
    if (!$force && $current !== '' && $current !== [] && $current !== null) { return 'kept'; }
    $backup['meta'][$slot] = ['id' => $id, 'key' => $key, 'existed' => metadata_exists('post', $id, $key), 'old' => $current, 'written' => $value];
  }
  $backup['meta'][$slot]['written'] = $value;
  update_post_meta($id, $key, $value);
  return 'written';
}

function zp_seo_plan_set_title(array &$backup, int $id, string $title): string {
  $post = get_post($id);
  if (!$post || $title === '') { return 'same'; }
  if (isset($backup['title'][$id]) && $post->post_title !== $backup['title'][$id]['written']) { return 'kept'; }
  if ($post->post_title === $title) { return 'same'; }
  if (!isset($backup['title'][$id])) { $backup['title'][$id] = ['old' => $post->post_title]; }
  $backup['title'][$id]['written'] = $title;
  wp_update_post(wp_slash(['ID' => $id, 'post_title' => $title]));
  return 'written';
}

function zp_seo_plan_migrate(): void {
  $log = ['Start migracji planu SEO ' . ZP_SEO_PLAN_VERSION . '.'];
  $backup = zp_seo_plan_backup();
  $redirects = zp_seo_plan_data('redirects');
  $plan = zp_seo_plan_data('plan');

  // 1. Nationwide service pages.
  $log = array_merge($log, zp_seo_service_create_pages());

  // 2. The shop post's content moves to /tworzenie-sklepow-internetowych/; remember it before drafting.
  $shop_path = '/sklepy-internetowe/tworzenie-sklepow-internetowych-od-pomyslu-na-oferte-do-gotowego-sklepu-online/';
  if (!get_option('zp_seo_plan_shop_post')) {
    $shop = zp_seo_plan_find_post($shop_path);
    if ($shop) {
      update_option('zp_seo_plan_shop_post', $shop, false);
      $log[] = 'Treść wpisu o tworzeniu sklepów (ID ' . $shop . ') pokazuje się teraz na /tworzenie-sklepow-internetowych/.';
    } else {
      $log[] = 'UWAGA: nie znaleziono wpisu ' . $shop_path . ' — strona sklepów pokaże się bez przeniesionego poradnika.';
    }
  }

  // 3. Typo slug (before drafting: the old address is also in the redirect map).
  foreach ((array) ($redirects['rename'] ?? []) as $from => $to) {
    $id = zp_seo_plan_find_post($from);
    $new_slug = basename(trim($to, '/'));
    if (!$id) { if (!zp_seo_plan_find_post($to)) { $log[] = 'UWAGA: nie znaleziono wpisu ' . $from; } continue; }
    if (get_posts(['name' => $new_slug, 'post_type' => 'post', 'post_status' => 'any', 'numberposts' => 1, 'fields' => 'ids', 'suppress_filters' => true])) {
      $log[] = 'UWAGA: adres ' . $to . ' jest zajęty, literówka w ' . $from . ' została bez zmian.';
      continue;
    }
    $backup['slug'][$id] = ['old' => get_post_field('post_name', $id), 'new' => $new_slug];
    wp_update_post(['ID' => $id, 'post_name' => $new_slug]);
    $log[] = 'Poprawiono adres wpisu: ' . $from . ' → ' . zp_seo_plan_path((string) get_permalink($id));
  }
  // 4. Merged duplicates (and any published post at a redirected address) go to drafts.
  $to_draft = (array) ($redirects['drafts'] ?? []);
  foreach ((array) ($redirects['exact'] ?? []) as $from => $to) { $to_draft[] = $from; }
  $drafted = 0;
  foreach (array_unique($to_draft) as $path) {
    if (isset($redirects['rename'][$path])) { continue; }
    $id = zp_seo_plan_find_post($path);
    if (!$id || get_post_type($id) !== 'post') { continue; }
    $target = $redirects['exact'][$path] ?? '';
    if ($target !== '' && zp_seo_plan_find_post($target) === $id) { continue; }
    $backup['status'][$id] = ['old' => 'publish', 'path' => $path];
    wp_update_post(['ID' => $id, 'post_status' => 'draft']);
    $drafted++;
    $log[] = 'Wpis połączony, przeniesiony do szkiców (301 → ' . ($target ?: 'docelowy wpis') . '): ' . $path;
  }
  $log[] = 'Szkice z połączonych wpisów: ' . $drafted . '.';

  update_option(ZP_SEO_PLAN_BACKUP, $backup, false);

  // 5–6. Rank Math fields and H1 (post title) from the plan.
  $written = $kept = $missing = 0;
  $editorial = class_exists('ZPSSEO_Editorial') ? (array) ZPSSEO_Editorial::editorial() : [];
  $editorial_by_path = [];
  foreach ($editorial as $e) { if (!empty($e['path'])) { $editorial_by_path[$e['path']] = $e; } }
  foreach ($plan as $path => $e) {
    $id = zp_seo_plan_find_post($path);
    if (!$id) {
      if ($path === '/') { $log = array_merge($log, zp_seo_plan_home_options($backup, $e)); continue; }
      $missing++;
      $log[] = 'UWAGA: brak opublikowanej strony ' . $path . ' — pominięto tytuł i opis.';
      continue;
    }
    $title = $e['title'] !== '' ? $e['title'] : $e['editorial_title'];
    $description = $e['description'] !== '' ? $e['description'] : $e['editorial_description'];
    $results = [];
    if ($title !== '') { $results[] = zp_seo_plan_set_meta($backup, $id, 'rank_math_title', $title, $e['title'] !== ''); }
    if ($description !== '') { $results[] = zp_seo_plan_set_meta($backup, $id, 'rank_math_description', $description, $e['description'] !== ''); }
    if ($e['focus'] !== '') { $results[] = zp_seo_plan_set_meta($backup, $id, 'rank_math_focus_keyword', $e['focus'], true); }
    $h1 = $e['h1'] !== '' ? $e['h1'] : (string) ($editorial_by_path[$path]['h1'] ?? '');
    if ($e['kind'] === 'post' && $h1 !== '') { $results[] = zp_seo_plan_set_title($backup, $id, $h1); }
    $written += count(array_keys($results, 'written', true));
    $kept += count(array_keys($results, 'kept', true));
  }

  // Pages whose title/description used to be set in code (editorial module) keep them, now in Rank Math.
  $legacy = [];
  foreach ($editorial_by_path as $path => $e) {
    if (!isset($plan[$path])) { $legacy[$path] = ['title' => $e['title'] ?? '', 'description' => $e['description'] ?? '', 'h1' => $e['h1'] ?? '', 'post' => true]; }
  }
  $legacy['/kontakt/'] = ['title' => 'Kontakt | Zaprojektowani — strony, sklepy i branding', 'description' => 'Skontaktuj się z Zaprojektowani w sprawie strony internetowej, sklepu WooCommerce lub brandingu. Opisz potrzeby i zakres swojego projektu.', 'h1' => '', 'post' => false];
  $legacy['/kampanie-reklamowe/'] = ['title' => 'Kampanie Meta Ads i Google Ads | Zaprojektowani', 'description' => 'Kampanie Meta Ads i Google Ads: oferta, kreacje, strona docelowa i pomiar zapytań. Poznaj ofertę Zaprojektowani i zaplanuj działania reklamowe.', 'h1' => '', 'post' => false];
  $legacy['/wiedza/'] = ['title' => 'Wiedza: strony WWW, sklepy, SEO i branding | Zaprojektowani', 'description' => 'Praktyczne poradniki o stronach internetowych, sklepach WooCommerce, SEO, logo, brandingu i Meta Ads. Sprawdź koszty, procesy, checklisty i przykłady.', 'h1' => '', 'post' => false];
  foreach ($legacy as $path => $e) {
    $id = zp_seo_plan_find_post($path);
    if (!$id || (isset($backup['status'][$id]))) { continue; }
    $results = [];
    if ($e['title'] !== '') { $results[] = zp_seo_plan_set_meta($backup, $id, 'rank_math_title', $e['title'], false); }
    if ($e['description'] !== '') { $results[] = zp_seo_plan_set_meta($backup, $id, 'rank_math_description', $e['description'], false); }
    if ($e['post'] && $e['h1'] !== '' && get_post_type($id) === 'post') { $results[] = zp_seo_plan_set_title($backup, $id, $e['h1']); }
    $written += count(array_keys($results, 'written', true));
  }
  update_option(ZP_SEO_PLAN_BACKUP, $backup, false);
  $log[] = 'Rank Math: zapisano ' . $written . ' pól (tytuły SEO, opisy, frazy, H1)' . ($kept ? ', ' . $kept . ' pól zmienionych ręcznie zostawiono' : '') . ($missing ? ', ' . $missing . ' adresów z planu nie ma na stronie' : '') . '.';

  // 7. Thank-you pages: noindex, follow (visible in Rank Math).
  $thanks = 0;
  foreach (zp_seo_plan_thank_you_slugs() as $slug) {
    $page = get_page_by_path($slug, OBJECT, 'page');
    if (!$page || $page->post_status !== 'publish') { continue; }
    if (zp_seo_plan_set_meta($backup, (int) $page->ID, 'rank_math_robots', ['noindex', 'follow'], true) === 'written') { $thanks++; }
  }
  update_option(ZP_SEO_PLAN_BACKUP, $backup, false);
  $log[] = 'Strony podziękowań z noindex: ' . $thanks . '.';

  // 8. Caches.
  zp_seo_plan_purge_caches();
  $log[] = 'Wyczyszczono pamięć podręczną map witryny Rank Math i LiteSpeed.';

  update_option('zp_seo_plan_migrated', ZP_SEO_PLAN_VERSION, false);
  update_option('zp_seo_plan_migrated_at', time(), false);
  $log[] = 'Migracja zakończona.';
  zp_seo_plan_log($log);
}

/** Home page without a static front page: Rank Math's own home title/description options. */
function zp_seo_plan_home_options(array &$backup, array $e): array {
  $opts = get_option('rank-math-options-titles');
  if (!is_array($opts)) { return ['UWAGA: brak ustawień Rank Math dla strony głównej — pominięto.']; }
  foreach (['homepage_title' => $e['title'], 'homepage_description' => $e['description']] as $key => $value) {
    if ($value === '') { continue; }
    $current = (string) ($opts[$key] ?? '');
    if (isset($backup['option'][$key]) && $current !== $backup['option'][$key]['written']) { continue; }
    if (!isset($backup['option'][$key])) { $backup['option'][$key] = ['old' => $current]; }
    $backup['option'][$key]['written'] = $value;
    $opts[$key] = $value;
  }
  update_option('rank-math-options-titles', $opts);
  return ['Zapisano tytuł i opis strony głównej w ustawieniach Rank Math.'];
}

function zp_seo_plan_purge_caches(): void {
  if (class_exists('\RankMath\Sitemap\Cache')) { \RankMath\Sitemap\Cache::invalidate_storage(); }
  wp_cache_delete('zp_seo_plan_thank_you_slugs');
  if (defined('LSCWP_V')) { do_action('litespeed_purge_all', 'Zaprojektowani SEO ' . ZP_SEO_PLAN_VERSION); }
  foreach (['rocket_clean_domain', 'w3tc_flush_all', 'wp_cache_clear_cache'] as $fn) {
    if (function_exists($fn)) { try { call_user_func($fn); } catch (\Throwable $e) { /* cache plugin error is not fatal */ } }
  }
}

/** Puts back every value the migration changed (unless it was edited by hand since). */
function zp_seo_plan_restore(): array {
  $backup = zp_seo_plan_backup();
  $log = [];
  $n = 0;
  foreach ($backup['meta'] as $slot => $m) {
    if (get_post_meta($m['id'], $m['key'], true) !== $m['written']) { continue; }
    if ($m['existed']) { update_post_meta($m['id'], $m['key'], $m['old']); } else { delete_post_meta($m['id'], $m['key']); }
    $n++;
  }
  $log[] = 'Przywrócono pola Rank Math: ' . $n . '.';
  $n = 0;
  foreach ($backup['title'] as $id => $t) {
    if (get_post_field('post_title', $id) !== ($t['written'] ?? null)) { continue; }
    wp_update_post(wp_slash(['ID' => $id, 'post_title' => $t['old']]));
    $n++;
  }
  $log[] = 'Przywrócono tytuły wpisów: ' . $n . '.';
  $n = 0;
  foreach ($backup['status'] as $id => $s) {
    if (get_post_status($id) !== 'draft') { continue; }
    wp_update_post(['ID' => $id, 'post_status' => $s['old']]);
    $n++;
  }
  $log[] = 'Opublikowano ponownie połączone wpisy: ' . $n . '.';
  foreach ($backup['slug'] as $id => $s) {
    if (get_post_field('post_name', $id) === $s['new']) { wp_update_post(['ID' => $id, 'post_name' => $s['old']]); $log[] = 'Przywrócono adres wpisu ID ' . $id . '.'; }
  }
  if ($backup['option']) {
    $opts = (array) get_option('rank-math-options-titles', []);
    foreach ($backup['option'] as $key => $o) { if (($opts[$key] ?? '') === $o['written']) { $opts[$key] = $o['old']; } }
    update_option('rank-math-options-titles', $opts);
  }
  $n = 0;
  foreach (get_posts(['post_type' => 'page', 'post_status' => 'publish', 'numberposts' => 20, 'fields' => 'ids', 'meta_key' => '_zp_seo_plan_created', 'suppress_filters' => true]) as $id) {
    wp_update_post(['ID' => $id, 'post_status' => 'draft']);
    $n++;
  }
  $log[] = 'Nowe strony usług przeniesione do szkiców: ' . $n . '.';
  delete_option(ZP_SEO_PLAN_BACKUP);
  delete_option('zp_seo_plan_migrated');
  zp_seo_plan_purge_caches();
  return $log;
}
