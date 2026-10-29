<?php
if (!defined('ABSPATH')) { exit; }

/**
 * ZP Suite 2.9.0 — "Publikacja wpisów": new articles in waves without a plugin release
 * (Mat 7.10: "czy mozesz sam dodawac artykuly falami…").
 *
 * The cloud cannot reach the server, so the site pulls finished articles itself from the
 * public branch "tresci-wpisy" of the plugin repository (written by the content thread):
 * manifest.json, wpisy/<slug>.json and obrazy/<name>.webp|jpg|png. Every 6 hours (WP-Cron,
 * with a fallback after a page view and the "Sprawdź teraz" button) the module imports new
 * articles as scheduled posts on their dates, built like zp_seo_articles_create(): the
 * Elementor layout of a template post, category, excerpt, author, featured image, Rank Math
 * fields and the FAQPage JSON-LD. WordPress publishes them; a guard publishes posts whose
 * time passed when WP-Cron did not run. At most one feed article a day.
 *
 * The feed is public, so only what an article needs gets in: HTML through a tag allowlist
 * (no style, script, iframe, form or on* attributes), links to the site, WhatsApp, e-mail
 * and phone only, photos only from the feed (checked sha256, JPEG/PNG/WebP up to 3 MB,
 * cover exactly 1600×1000) or from the site's own uploads.
 *
 * A post the module created belongs to Mat: a newer feed file updates it only while nobody
 * edited it in WordPress, a post he moved to drafts or the bin never comes back, and the
 * module never unpublishes a published post. Settings and log: ZP Suite → Publikacja wpisów.
 */

const ZP_FEED_SETTINGS = 'zp_feed_settings';
const ZP_FEED_STATE = 'zp_feed_state';
const ZP_FEED_LOG = 'zp_feed_log';
const ZP_FEED_DISPLAY = 'zp_feed_display';
const ZP_FEED_STATUS = 'zp_feed_status';
const ZP_FEED_MEDIA = 'zp_feed_media';
const ZP_FEED_MAX_MANIFEST = 204800;
const ZP_FEED_MAX_POST = 1048576;
const ZP_FEED_MAX_IMAGE = 3145728;

function zp_feed_settings(): array {
  $s = (array) get_option(ZP_FEED_SETTINGS, []);
  return [
    'enabled' => ($s['enabled'] ?? '1') === '1',
    'mode' => ($s['mode'] ?? 'schedule') === 'drafts' ? 'drafts' : 'schedule',
    'email' => array_key_exists('email', $s) ? (string) $s['email'] : 'kontakt@zaprojektowani.com',
    'paused' => ($s['paused'] ?? '') === '1',
  ];
}

function zp_feed_update_settings(array $changes): void {
  $s = (array) get_option(ZP_FEED_SETTINGS, []);
  foreach ($changes as $key => $value) {
    $s[$key] = is_bool($value) ? ($value ? '1' : '') : (string) $value;
  }
  update_option(ZP_FEED_SETTINGS, $s, false);
}

function zp_feed_log(array $lines): void {
  if (!$lines) { return; }
  $log = (array) get_option(ZP_FEED_LOG, []);
  $stamp = wp_date('Y-m-d H:i');
  foreach ($lines as $line) { $log[] = $stamp . '  ' . $line; }
  update_option(ZP_FEED_LOG, array_slice($log, -200), false);
}

function zp_feed_status(): array {
  return array_merge(['fails' => 0, 'last_error' => '', 'last_try' => 0, 'last_ok' => 0, 'mail' => []], (array) get_option(ZP_FEED_STATUS, []));
}

function zp_feed_state(): array {
  return (array) get_option(ZP_FEED_STATE, []);
}

/* ------------------------------------------------------------------ fetching */

function zp_feed_base_url(): string {
  return (string) apply_filters('zp_feed_base_url', 'https://raw.githubusercontent.com/emekzett90/zaprojektowanicom/tresci-wpisy/');
}

/** A file from the feed branch (string), or WP_Error. Only the feed's own file names are fetched. */
function zp_feed_get(string $rel, int $max, string $sha = '') {
  if (!preg_match('~^(?:manifest\.json|wpisy/[a-z0-9-]+\.json|obrazy/[a-z0-9-]+\.(?:webp|jpe?g|png))$~', $rel)) {
    return new WP_Error('zp_feed_path', 'niedozwolona ścieżka pliku ' . $rel);
  }
  $body = apply_filters('zp_feed_pre_get', null, $rel);
  if ($body === null) {
    // The query string only skips stale CDN copies; the file itself is checked by its sha256.
    $url = zp_feed_base_url() . $rel . '?v=' . ($sha !== '' ? substr($sha, 0, 12) : (string) time());
    $res = wp_remote_get($url, ['timeout' => 20, 'redirection' => 2, 'limit_response_size' => $max + 1]);
    if (is_wp_error($res)) { return new WP_Error('zp_feed_http', $rel . ': ' . $res->get_error_message()); }
    $code = (int) wp_remote_retrieve_response_code($res);
    if ($code !== 200) { return new WP_Error('zp_feed_http', $rel . ': odpowiedź HTTP ' . $code); }
    $body = (string) wp_remote_retrieve_body($res);
  }
  if (!is_string($body)) { return new WP_Error('zp_feed_http', $rel . ': brak treści'); }
  if (strlen($body) > $max) { return new WP_Error('zp_feed_size', $rel . ': plik większy niż ' . size_format($max)); }
  if ($sha !== '' && !hash_equals(strtolower($sha), hash('sha256', $body))) {
    return new WP_Error('zp_feed_sha', $rel . ': suma sha256 nie zgadza się z manifestem');
  }
  return $body;
}

function zp_feed_is_path(string $path): bool {
  return (bool) preg_match('~^/(?:[a-z0-9-]+/)+$~', $path);
}

/** Manifest entries checked field by field; invalid entries are left out with a reason. */
function zp_feed_parse_manifest(string $json, array &$problems): ?array {
  $m = json_decode($json, true);
  if (!is_array($m) || (int) ($m['format'] ?? 0) !== 1) { return null; }
  $tz = wp_timezone();
  $posts = [];
  foreach ((array) ($m['wpisy'] ?? []) as $e) {
    if (!is_array($e)) { continue; }
    $slug = (string) ($e['slug'] ?? '');
    if (!preg_match('~^[a-z0-9]+(?:-[a-z0-9]+)*$~', $slug) || strlen($slug) > 120) { $problems[] = 'zły slug w manifeście: ' . sanitize_text_field($slug); continue; }
    $path = (string) ($e['sciezka'] ?? '');
    $file = (string) ($e['plik'] ?? '');
    $sha = strtolower((string) ($e['sha256'] ?? ''));
    // The date and time are read as the site's clock time (Warsaw), whatever UTC offset the feed
    // wrote, so an offset left at +02:00 after the October time change cannot move 8:00 to 7:00.
    $date = null;
    if (preg_match('~^(\d{4}-\d{2}-\d{2})[T ](\d{2}):(\d{2})~', (string) ($e['publikacja'] ?? ''), $dm)) {
      try { $date = new DateTimeImmutable($dm[1] . ' ' . $dm[2] . ':' . $dm[3] . ':00', $tz); } catch (\Throwable $x) { $date = null; }
    }
    if (!zp_feed_is_path($path) || basename(rtrim($path, '/')) !== $slug || $file !== 'wpisy/' . $slug . '.json' || !preg_match('~^[a-f0-9]{64}$~', $sha) || !$date) {
      $problems[] = 'niepełny wpis w manifeście: ' . $slug;
      continue;
    }
    $posts[$slug] = ['slug' => $slug, 'path' => $path, 'file' => $file, 'sha' => $sha, 'date' => $date->setTimezone($tz), 'batch' => (int) ($e['paczka'] ?? 0)];
  }
  uasort($posts, static function ($a, $b) { return $a['date'] <=> $b['date']; });

  $links = [];
  foreach ((array) ($m['linki'] ?? []) as $l) {
    if (!is_array($l)) { continue; }
    $in = (string) ($l['we_wpisie'] ?? '');
    $fragment = (string) ($l['fragment'] ?? '');
    $words = (string) ($l['slowa'] ?? '');
    $to = (string) ($l['do'] ?? '');
    if (!zp_feed_is_path($in) || !zp_feed_is_path($to) || $fragment === '' || strlen($fragment) > 1000 || $words === '' || strlen($words) > 120
      || $words !== wp_strip_all_tags($words) || strpos(wp_strip_all_tags($fragment), $words) === false) {
      $problems[] = 'pominięto link do ' . sanitize_text_field($to) . ' (zły format)';
      continue;
    }
    $links[$in][] = [$fragment, $words, $to];
  }

  $sections = [];
  foreach ((array) ($m['sekcje'] ?? []) as $s) {
    if (!is_array($s)) { continue; }
    $in = (string) ($s['we_wpisie'] ?? '');
    $id = (string) ($s['id'] ?? '');
    $toc = sanitize_text_field((string) ($s['toc'] ?? ''));
    $html = zp_feed_sanitize_html((string) ($s['html'] ?? ''), [], $problems);
    if (!zp_feed_is_path($in) || !preg_match('~^[a-z0-9-]{1,60}$~', $id) || strpos($html, 'id="' . $id . '"') === false || stripos(ltrim($html), '<section') !== 0) {
      $problems[] = 'pominięto sekcję ' . sanitize_text_field($id) . ' (zły format)';
      continue;
    }
    $sections[$in][] = ['id' => $id, 'toc' => mb_substr($toc, 0, 80), 'html' => $html];
  }
  return ['posts' => $posts, 'links' => $links, 'sections' => $sections];
}

/* ------------------------------------------------------------------ sanitizing */

function zp_feed_allowed_html(): array {
  $common = ['class' => true, 'id' => true, 'role' => true, 'tabindex' => true];
  foreach (['label', 'hidden', 'labelledby', 'describedby', 'current', 'expanded', 'controls', 'level'] as $aria) { $common['aria-' . $aria] = true; }
  $out = [];
  foreach (['article', 'main', 'header', 'nav', 'aside', 'section', 'div', 'p', 'span', 'h1', 'h2', 'h3', 'h4', 'ul', 'ol', 'li', 'a', 'strong', 'b', 'em', 'br',
    'figure', 'figcaption', 'img', 'table', 'thead', 'tbody', 'tr', 'th', 'td', 'blockquote', 'details', 'summary'] as $tag) {
    $out[$tag] = $common;
  }
  $out['article'] += ['data-zp-article' => true, 'data-rankmath-main' => true];
  $out['img'] += ['src' => true, 'alt' => true, 'width' => true, 'height' => true, 'loading' => true, 'decoding' => true];
  $out['a'] += ['href' => true, 'rel' => true, 'target' => true];
  $out['details'] += ['open' => true];
  return $out;
}

/** Links an article may carry: the site, the studio's WhatsApp, e-mail and phone, anchors. */
function zp_feed_href_ok(string $href): bool {
  if ($href === '' || $href[0] === '#') { return (bool) preg_match('~^#[A-Za-z0-9_-]*$~', $href); }
  if ($href[0] === '/') { return strpos($href, '//') !== 0 && strpos($href, '\\') === false; }
  foreach (['https://zaprojektowani.com/', 'https://www.zaprojektowani.com/', home_url('/')] as $base) {
    if (strpos($href, $base) === 0 || $href === rtrim($base, '/')) { return true; }
  }
  return (bool) preg_match('~^(?:https://wa\.me/48501054253(?:\?[^\s"<>]*)?|mailto:kontakt@zaprojektowani\.com(?:\?[^\s"<>]*)?|tel:\+48501054253)$~', $href);
}

/** Photos an article may show: the site's own uploads (feed photos are imported there). */
function zp_feed_src_ok(string $src): bool {
  if (strpos($src, '..') !== false) { return false; }
  foreach (['https://zaprojektowani.com/wp-content/uploads/', trailingslashit((string) (wp_get_upload_dir()['baseurl'] ?? ''))] as $base) {
    if ($base !== '/' && strpos($src, $base) === 0) { return true; }
  }
  return false;
}

function zp_feed_rebuild_attrs(string $tag, array $attrs): string {
  $out = '<' . $tag;
  foreach ($attrs as $name => $a) {
    $out .= ($a['vless'] ?? 'n') === 'y' ? ' ' . $name : ' ' . $name . '="' . esc_attr(html_entity_decode((string) $a['value'], ENT_QUOTES, 'UTF-8')) . '"';
  }
  return $out . '>';
}

/**
 * Article HTML reduced to the allowlist. $images maps {{IMG:name}} placeholders to imported
 * photo URLs. Removed links and photos are reported in $notes.
 */
function zp_feed_sanitize_html(string $html, array $images, array &$notes): string {
  $html = (string) preg_replace('~<!--.*?-->~s', '', $html);
  $html = (string) preg_replace('~<(script|style|iframe|noscript|template|textarea|select|form|svg|math|object|embed|video|audio|canvas)\b[^>]*>.*?</\1\s*>~is', '', $html);
  $html = (string) preg_replace_callback('~src="\{\{IMG:([a-z0-9-]+)\}\}"~', static function ($m) use ($images) {
    return 'src="' . esc_attr((string) ($images[$m[1]] ?? '')) . '"';
  }, $html);
  $html = wp_kses($html, zp_feed_allowed_html(), ['http', 'https', 'mailto', 'tel']);
  $protocols = ['http', 'https', 'mailto', 'tel'];
  $html = (string) preg_replace_callback('~<a\b([^>]*)>~i', static function ($m) use (&$notes, $protocols) {
    $attrs = [];
    foreach (wp_kses_hair($m[1], $protocols) as $a) { $attrs[strtolower($a['name'])] = $a; }
    $href = html_entity_decode((string) ($attrs['href']['value'] ?? ''), ENT_QUOTES, 'UTF-8');
    if (isset($attrs['href']) && !zp_feed_href_ok($href)) {
      $notes[] = 'usunięto link ' . $href;
      unset($attrs['href'], $attrs['target'], $attrs['rel']);
    }
    if (isset($attrs['target'])) {
      if (html_entity_decode((string) $attrs['target']['value'], ENT_QUOTES, 'UTF-8') !== '_blank') { unset($attrs['target']); }
      else { $attrs['rel'] = ['name' => 'rel', 'value' => 'noopener', 'vless' => 'n']; }
    }
    return zp_feed_rebuild_attrs('a', $attrs);
  }, $html);
  $html = (string) preg_replace_callback('~<img\b([^>]*)>~i', static function ($m) use (&$notes, $protocols) {
    $attrs = [];
    foreach (wp_kses_hair($m[1], $protocols) as $a) { $attrs[strtolower($a['name'])] = $a; }
    $src = html_entity_decode((string) ($attrs['src']['value'] ?? ''), ENT_QUOTES, 'UTF-8');
    if (!zp_feed_src_ok($src)) {
      $notes[] = 'usunięto zdjęcie ' . ($src !== '' ? $src : 'bez adresu');
      return '';
    }
    return zp_feed_rebuild_attrs('img', $attrs);
  }, $html);
  return trim($html);
}

/* ------------------------------------------------------------------ dates */

/** Polish public holidays (month-day) of a year. */
function zp_feed_holidays(int $y): array {
  $a = $y % 19; $b = intdiv($y, 100); $c = $y % 100; $d = intdiv($b, 4); $e = $b % 4; $f = intdiv($b + 8, 25); $g = intdiv($b - $f + 1, 3);
  $h = (19 * $a + $b - $d - $g + 15) % 30; $i = intdiv($c, 4); $k = $c % 4; $l = (32 + 2 * $e + 2 * $i - $h - $k) % 7; $m = intdiv($a + 11 * $h + 22 * $l, 451);
  $easter = new DateTimeImmutable(sprintf('%04d-%02d-%02d', $y, intdiv($h + $l - 7 * $m + 114, 31), (($h + $l - 7 * $m + 114) % 31) + 1), wp_timezone());
  return ['01-01', '01-06', '05-01', '05-03', '08-15', '11-01', '11-11', '12-24', '12-25', '12-26',
    $easter->modify('+1 day')->format('m-d'), $easter->modify('+60 days')->format('m-d')];
}

function zp_feed_is_workday(DateTimeImmutable $d): bool {
  return (int) $d->format('N') <= 5 && !in_array($d->format('m-d'), zp_feed_holidays((int) $d->format('Y')), true);
}

/** Days (Y-m-d, site time) taken by scheduled or published feed posts. */
function zp_feed_taken_days(int $except = 0): array {
  $days = [];
  $ids = get_posts(['post_type' => 'post', 'post_status' => ['future', 'publish'], 'numberposts' => -1, 'fields' => 'ids', 'meta_key' => '_zp_feed_slug', 'suppress_filters' => true]);
  foreach ($ids as $id) {
    if ((int) $id !== $except) { $days[substr((string) get_post_field('post_date', $id), 0, 10)] = true; }
  }
  return $days;
}

/**
 * The publication time for a wanted date: the date itself when it is still ahead and its day
 * is free, otherwise the nearest free working day at 8:00. Marks the day as taken.
 */
function zp_feed_slot(DateTimeImmutable $wanted, array &$taken): DateTimeImmutable {
  $tz = wp_timezone();
  $soon = (new DateTimeImmutable('now', $tz))->modify('+10 minutes');
  $d = $wanted->setTimezone($tz);
  if ($d <= $soon || isset($taken[$d->format('Y-m-d')])) {
    $c = ($d > $soon ? $d : $soon)->setTime(8, 0);
    if ($c <= $soon) { $c = $c->modify('+1 day'); }
    for ($i = 0; $i < 400 && (!zp_feed_is_workday($c) || isset($taken[$c->format('Y-m-d')])); $i++) { $c = $c->modify('+1 day'); }
    $d = $c;
  }
  $taken[$d->format('Y-m-d')] = true;
  return $d;
}

/**
 * wp_update_post() for the module's status and date changes. WordPress saves the content again
 * on every update, and from WP-Cron or a visitor's request (no user with unfiltered_html) its
 * HTML filter would strip the article's classes, data attributes and JSON-LD.
 */
function zp_feed_update_post(array $post): void {
  $kses = has_filter('content_save_pre', 'wp_filter_post_kses');
  if ($kses) { kses_remove_filters(); }
  wp_update_post($post);
  if ($kses) { kses_init_filters(); }
}

function zp_feed_set_date(int $id, DateTimeImmutable $d, string $status): void {
  zp_feed_update_post([
    'ID' => $id, 'post_status' => $status, 'edit_date' => true,
    'post_date' => $d->format('Y-m-d H:i:s'), 'post_date_gmt' => $d->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d H:i:s'),
  ]);
}

function zp_feed_day_label(string $local): string {
  $t = strtotime($local);
  return $t ? date('j.m', $t) . ' ' . date('G:i', $t) : $local;
}

/* ------------------------------------------------------------------ media */

/** Imports a feed photo once (by sha256) and returns its attachment ID, or WP_Error. */
function zp_feed_media(array $img, bool $cover): int|WP_Error {
  $file = (string) ($img['plik'] ?? '');
  $sha = strtolower((string) ($img['sha256'] ?? ''));
  $alt = sanitize_text_field((string) ($img['alt'] ?? ''));
  if (!preg_match('~^obrazy/[a-z0-9-]+\.(?:webp|jpe?g|png)$~', $file) || !preg_match('~^[a-f0-9]{64}$~', $sha)) {
    return new WP_Error('zp_feed_img', 'zły opis zdjęcia ' . sanitize_text_field($file));
  }
  $map = (array) get_option(ZP_FEED_MEDIA, []);
  if (!empty($map[$sha]) && get_post_type((int) $map[$sha]) === 'attachment') { return (int) $map[$sha]; }
  $bytes = zp_feed_get($file, ZP_FEED_MAX_IMAGE, $sha);
  if (is_wp_error($bytes)) { return $bytes; }
  $info = @getimagesizefromstring($bytes);
  $types = [IMAGETYPE_JPEG => ['image/jpeg', 'jpg'], IMAGETYPE_PNG => ['image/png', 'png']];
  if (defined('IMAGETYPE_WEBP')) { $types[IMAGETYPE_WEBP] = ['image/webp', 'webp']; }
  if (!$info || !isset($types[$info[2]])) { return new WP_Error('zp_feed_img', $file . ': to nie jest JPEG, PNG ani WebP'); }
  if ($cover && ((int) $info[0] !== 1600 || (int) $info[1] !== 1000)) {
    return new WP_Error('zp_feed_cover', $file . ': okładka ma ' . (int) $info[0] . '×' . (int) $info[1] . ' zamiast 1600×1000');
  }
  [$mime, $ext] = $types[$info[2]];
  $name = preg_replace('~\.[a-z]+$~', '', basename($file)) . '.' . $ext;
  $upload = wp_upload_bits($name, null, $bytes);
  if (!empty($upload['error'])) { return new WP_Error('zp_feed_img', $file . ': ' . $upload['error']); }
  $id = wp_insert_attachment(['post_mime_type' => $mime, 'post_title' => $alt !== '' ? $alt : $name, 'post_name' => 'wpis-' . sanitize_title(preg_replace('~\.[a-z]+$~', '', $name)), 'post_status' => 'inherit'], $upload['file'], 0, true);
  if (is_wp_error($id) || !$id) { return new WP_Error('zp_feed_img', $file . ': nie udało się dodać do biblioteki mediów'); }
  if (!function_exists('wp_generate_attachment_metadata')) { require_once ABSPATH . 'wp-admin/includes/image.php'; }
  wp_update_attachment_metadata($id, wp_generate_attachment_metadata($id, $upload['file']));
  if ($alt !== '') { update_post_meta($id, '_wp_attachment_image_alt', $alt); }
  update_post_meta($id, '_zp_feed_sha256', $sha);
  $map[$sha] = (int) $id;
  update_option(ZP_FEED_MEDIA, $map, false);
  return (int) $id;
}

/* ------------------------------------------------------------------ building a post */

/**
 * Reads and checks one feed post. Returns the data for wp_insert_post or WP_Error.
 * 'network' => true in the error data means a retry may help (download or checksum).
 */
function zp_feed_build(array $entry, array &$notes): array|WP_Error {
  $json = zp_feed_get($entry['file'], ZP_FEED_MAX_POST, $entry['sha']);
  if (is_wp_error($json)) { $json->add_data(['network' => true]); return $json; }
  $p = json_decode($json, true);
  if (!is_array($p) || (int) ($p['format'] ?? 0) !== 1 || ($p['slug'] ?? '') !== $entry['slug']) {
    return new WP_Error('zp_feed_post', 'plik wpisu ma zły format');
  }
  $category = (string) ($p['kategoria'] ?? '');
  $template = (string) ($p['wzor'] ?? '');
  $title = sanitize_text_field((string) ($p['tytul'] ?? ''));
  $seo_title = sanitize_text_field((string) ($p['seo_tytul'] ?? ''));
  $description = sanitize_text_field((string) ($p['opis'] ?? ''));
  $focus = mb_strtolower(sanitize_text_field((string) ($p['fraza'] ?? '')));
  if (!preg_match('~^[a-z0-9-]+$~', $category) || !zp_feed_is_path($template) || $title === '' || $focus === '' || $description === '') {
    return new WP_Error('zp_feed_post', 'brakuje kategorii, wzoru, tytułu, opisu albo frazy');
  }
  if ($entry['path'] !== '/' . $category . '/' . $entry['slug'] . '/') {
    return new WP_Error('zp_feed_post', 'adres ' . $entry['path'] . ' nie pasuje do kategorii ' . $category);
  }
  $html = base64_decode((string) ($p['html_b64'] ?? ''), true);
  if (!is_string($html) || stripos($html, '<article') === false || strpos($html, 'zpArticleNew') === false) {
    return new WP_Error('zp_feed_post', 'brak artykułu w szablonie zpArticleNew');
  }

  // Photos: the cover (exactly 1600×1000) and the article's photos, each imported once.
  $cover = (array) ($p['okladka'] ?? []);
  if (!$cover) { return new WP_Error('zp_feed_post', 'brak okładki'); }
  $thumb = zp_feed_media($cover, true);
  if (is_wp_error($thumb)) { $thumb->add_data(['network' => in_array($thumb->get_error_code(), ['zp_feed_http', 'zp_feed_sha'], true)]); return $thumb; }
  $images = [];
  foreach ((array) ($p['obrazy'] ?? []) as $img) {
    $name = (string) (((array) $img)['nazwa'] ?? '');
    if (!preg_match('~^[a-z0-9-]+$~', $name)) { $notes[] = 'pominięto zdjęcie bez poprawnej nazwy'; continue; }
    $id = zp_feed_media((array) $img, false);
    if (is_wp_error($id)) { $id->add_data(['network' => in_array($id->get_error_code(), ['zp_feed_http', 'zp_feed_sha'], true)]); return $id; }
    $images[$name] = (string) wp_get_attachment_url($id);
  }

  $article = zp_feed_sanitize_html($html, $images, $notes);
  // FAQPage JSON-LD inside the article, as in data/articles/*.html.
  $faq = [];
  foreach ((array) ($p['faq'] ?? []) as $qa) {
    $q = sanitize_text_field((string) (((array) $qa)['q'] ?? ''));
    $a = sanitize_text_field((string) (((array) $qa)['a'] ?? ''));
    if ($q !== '' && $a !== '') { $faq[] = ['@type' => 'Question', 'name' => $q, 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $a]]; }
  }
  if ($faq) {
    $ld = '<script type="application/ld+json">' . wp_json_encode(['@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $faq], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP) . '</script>';
    $at = strripos($article, '</article>');
    $article = $at === false ? $article . "\n" . $ld : substr($article, 0, $at) . $ld . "\n" . substr($article, $at);
  }

  $side = [];
  foreach ((array) ($p['frazy_poboczne'] ?? []) as $k) {
    $k = mb_strtolower(sanitize_text_field((string) $k));
    if ($k !== '' && $k !== $focus && count($side) < 4) { $side[] = $k; }
  }
  $links = [];
  foreach ((array) ($p['linki_uslug'] ?? []) as $l) {
    $path = (string) (((array) $l)['path'] ?? '');
    if (zp_feed_is_path($path)) { $links[] = ['text' => sanitize_text_field((string) (((array) $l)['text'] ?? '')), 'path' => $path]; }
  }
  return [
    'category' => $category, 'template' => $template, 'title' => $title, 'seo_title' => $seo_title !== '' ? $seo_title : $title,
    'description' => $description, 'focus' => $focus, 'focus_all' => implode(', ', array_merge([$focus], $side)), 'links' => $links,
    'article' => $article, 'thumb' => (int) $thumb, 'gbp' => sanitize_textarea_field(mb_substr((string) ($p['post_wizytowki'] ?? ''), 0, 1500)),
  ];
}

/** Published post or page (other than $except) whose main focus keyword is the same phrase. */
function zp_feed_focus_owner(string $focus, int $except): int {
  global $wpdb;
  $rows = $wpdb->get_results($wpdb->prepare(
    "SELECT pm.post_id, pm.meta_value FROM {$wpdb->postmeta} pm INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
     WHERE pm.meta_key = 'rank_math_focus_keyword' AND p.post_status = 'publish' AND p.post_type IN ('post','page') AND pm.meta_value LIKE %s",
    $wpdb->esc_like($focus) . '%'
  ));
  foreach ((array) $rows as $r) {
    $first = trim(mb_strtolower(explode(',', (string) $r->meta_value)[0]));
    if ((int) $r->post_id !== $except && $first === $focus) { return (int) $r->post_id; }
  }
  return 0;
}

function zp_feed_hashes(int $id): array {
  return [
    'c' => md5((string) get_post_field('post_content', $id)),
    'e' => md5((string) get_post_meta($id, '_elementor_data', true)),
    't' => md5((string) get_post_field('post_title', $id)),
  ];
}

function zp_feed_edited(int $id, array $state): bool {
  return zp_feed_hashes($id) !== (array) ($state['hash'] ?? []);
}

/** Writes the built article into a new post ($id = 0) or an existing one. Returns the post ID or WP_Error. */
function zp_feed_write(array $b, array $entry, int $id, string $status, ?DateTimeImmutable $when, array &$notes): int|WP_Error {
  $term = get_term_by('slug', $b['category'], 'category');
  if (!$term) { return new WP_Error('zp_feed_post', 'brak kategorii ' . $b['category']); }
  $source = zp_seo_plan_find_post($b['template']);
  $elementor = $source ? zp_seo_articles_elementor($source, $b['article'], $notes) : null;
  if ($elementor === null) {
    $notes[] = 'brak wpisu-wzoru z Elementorem ' . $b['template'] . ': zapisano jako szkic';
    $status = 'draft';
  }
  $post = [
    'post_type' => 'post', 'post_status' => $status, 'post_title' => $b['title'], 'post_content' => $b['article'],
    'post_excerpt' => $b['description'], 'post_category' => [(int) $term->term_id],
  ];
  if ($id) {
    $post['ID'] = $id;
    unset($post['post_status']);
  } else {
    $post += [
      'post_name' => $entry['slug'], 'ping_status' => 'closed',
      'post_author' => $source ? (int) get_post_field('post_author', $source) : get_current_user_id(),
      'comment_status' => $source ? (string) get_post_field('comment_status', $source) : 'closed',
    ];
    if ($status === 'future' && $when) {
      $post['post_date'] = $when->format('Y-m-d H:i:s');
      $post['post_date_gmt'] = $when->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d H:i:s');
    }
  }
  // Runs from WP-Cron without a logged-in user, where the HTML filter would strip the
  // article's classes, data attributes and JSON-LD (the HTML is already sanitized above).
  $kses = has_filter('content_save_pre', 'wp_filter_post_kses');
  if ($kses) { kses_remove_filters(); }
  $result = $id ? wp_update_post(wp_slash($post), true) : wp_insert_post(wp_slash($post), true);
  if ($kses) { kses_init_filters(); }
  if (is_wp_error($result) || !$result) { return is_wp_error($result) ? $result : new WP_Error('zp_feed_post', 'WordPress nie zapisał wpisu'); }
  $id = (int) $result;
  update_post_meta($id, '_zp_feed_slug', $entry['slug']);
  update_post_meta($id, '_zp_feed_gbp', $b['gbp']);
  update_post_meta($id, 'rank_math_primary_category', (int) $term->term_id);
  if ($elementor !== null) {
    foreach (['_elementor_edit_mode', '_elementor_template_type', '_elementor_version', '_elementor_pro_version', '_elementor_page_settings', '_wp_page_template'] as $key) {
      $value = get_post_meta($source, $key, true);
      if ($value !== '' && $value !== null) { update_post_meta($id, $key, wp_slash($value)); }
    }
    update_post_meta($id, '_elementor_data', wp_slash($elementor));
  }
  if ($b['thumb']) { set_post_thumbnail($id, $b['thumb']); }
  return $id;
}

/** Rank Math fields: written on import, later only while they still hold what the module wrote. */
function zp_feed_write_rank_math(int $id, array $b, array $old): array {
  $new = ['rank_math_title' => $b['seo_title'], 'rank_math_description' => $b['description'], 'rank_math_focus_keyword' => $b['focus_all']];
  foreach ($new as $key => $value) {
    $current = (string) get_post_meta($id, $key, true);
    if (!isset($old[$key]) || $current === $old[$key] || $current === '') { update_post_meta($id, $key, $value); }
    else { $new[$key] = $old[$key]; }
  }
  return $new;
}

/* ------------------------------------------------------------------ the run */

/** One check of the feed. $why: "cron", "przycisk" or "zapas" (after a page view). */
function zp_feed_run(string $why = 'cron'): array {
  $settings = zp_feed_settings();
  if (!$settings['enabled'] && $why !== 'przycisk') { return ['Moduł wyłączony.']; }
  $lock = (int) get_option('zp_feed_lock', 0);
  if ($lock && $lock < time() - 900) { delete_option('zp_feed_lock'); }
  if (!add_option('zp_feed_lock', time(), '', false)) { return ['Sprawdzanie już trwa.']; }
  $status = zp_feed_status();
  $status['last_try'] = time();
  update_option(ZP_FEED_STATUS, $status, false);
  @set_time_limit(180);
  $log = [];
  try {
    $log = zp_feed_sync($settings, $status);
  } catch (\Throwable $e) {
    $status['fails']++;
    $status['last_error'] = $e->getMessage();
    $log[] = 'BŁĄD: ' . $e->getMessage();
  } finally {
    update_option(ZP_FEED_STATUS, $status, false);
    delete_option('zp_feed_lock');
  }
  zp_feed_log($log);
  return $log;
}

function zp_feed_sync(array $settings, array &$status): array {
  $log = [];
  $problems = [];
  $raw = zp_feed_get('manifest.json', ZP_FEED_MAX_MANIFEST);
  $manifest = is_wp_error($raw) ? null : zp_feed_parse_manifest($raw, $problems);
  if (!$manifest) {
    $status['fails']++;
    $status['last_error'] = is_wp_error($raw) ? $raw->get_error_message() : 'manifest.json ma zły format';
    return ['Nie udało się pobrać kanału (' . $status['last_error'] . '). Spróbuję przy następnym sprawdzeniu.'];
  }
  // Format problems of the manifest are logged once, not on every check.
  $seen = md5(implode('|', $problems));
  if ($problems && ($status['problems'] ?? '') !== $seen) { foreach ($problems as $p) { $log[] = 'UWAGA: ' . $p . '.'; } }
  $status['problems'] = $seen;

  $state = zp_feed_state();
  $taken = zp_feed_taken_days();
  $deadline = microtime(true) + 25;
  $network = '';
  $new = [];
  $hold = $settings['paused'] ? 'pause' : '';
  $schedule = $settings['enabled'] && !$settings['paused'] && $settings['mode'] === 'schedule';
  $complete = true;

  foreach ($manifest['posts'] as $slug => $entry) {
    $s = (array) ($state[$slug] ?? []);
    $id = (int) ($s['id'] ?? 0);
    $post = $id ? get_post($id) : null;

    if ($id && !$post) {
      if (empty($s['gone'])) { $log[] = 'Wpis „' . ($s['title'] ?? $slug) . '” został usunięty w WordPressie: nie wraca.'; }
      $state[$slug]['gone'] = 1;
      continue;
    }
    if ($post) {
      $current = $post->post_status;
      if ($current === 'trash') { continue; }
      $held = (string) ($s['held'] ?? '');
      // Taken out of the feed earlier and back in it now: schedule again.
      if ($held === 'removed' && $current === 'draft' && $schedule && !zp_feed_edited($id, $s)) {
        $when = zp_feed_slot($entry['date'], $taken);
        zp_feed_set_date($id, $when, 'future');
        $state[$slug] = array_merge($s, ['held' => '', 'set' => 'future', 'date' => $when->format('Y-m-d H:i:s'), 'planned' => $entry['date']->format(DATE_ATOM)]);
        $s = $state[$slug];
        $log[] = 'Wpis „' . $s['title'] . '” wrócił do kanału: zaplanowany na ' . zp_feed_day_label($s['date']) . '.';
      }
      $mine = $current === ($s['set'] ?? '') || $current === 'publish' || ($held !== '' && $current === 'draft');
      if (!$mine) { continue; } // Mat moved it (e.g. to drafts): it stays where he put it.

      if ($entry['sha'] !== ($s['sha'] ?? '')) {
        if (zp_feed_edited($id, $s)) {
          if (($s['skip_sha'] ?? '') !== $entry['sha']) { $log[] = 'Nowa wersja „' . $s['title'] . '” w kanale, ale wpis był edytowany w WordPressie: zostaje bez zmian.'; }
          $state[$slug]['skip_sha'] = $entry['sha'];
        } elseif (microtime(true) < $deadline) {
          $notes = [];
          $b = zp_feed_build($entry, $notes);
          if (is_wp_error($b)) {
            if ($b->get_error_data()['network'] ?? false) { $network = $b->get_error_message(); }
            $log[] = 'UWAGA: nowa wersja „' . $s['title'] . '” nie wczytana: ' . $b->get_error_message() . '.';
          } else {
            $written = zp_feed_write($b, $entry, $id, $current, null, $notes);
            if (!is_wp_error($written)) {
              $state[$slug] = array_merge($s, ['sha' => $entry['sha'], 'title' => $b['title'], 'entry' => zp_feed_plan_entry($b), 'rm' => zp_feed_write_rank_math($id, $b, (array) ($s['rm'] ?? [])), 'hash' => zp_feed_hashes($id)]);
              $log[] = 'Zaktualizowano „' . $b['title'] . '” nową wersją z kanału' . ($notes ? ' (' . implode('; ', array_unique($notes)) . ')' : '') . '.';
            }
          }
        } else {
          $complete = false;
        }
      }
      // A new date in the feed for a post that is still waiting (unless its date was changed by hand).
      if ($current === 'future' && ($s['planned'] ?? '') !== $entry['date']->format(DATE_ATOM) && (string) get_post_field('post_date', $id) === ($s['date'] ?? '')) {
        unset($taken[substr((string) $s['date'], 0, 10)]);
        $when = zp_feed_slot($entry['date'], $taken);
        zp_feed_set_date($id, $when, 'future');
        $state[$slug]['date'] = $when->format('Y-m-d H:i:s');
        $state[$slug]['planned'] = $entry['date']->format(DATE_ATOM);
        $log[] = 'Nowa data „' . $s['title'] . '”: ' . zp_feed_day_label($state[$slug]['date']) . '.';
      }
      continue;
    }

    // A new article.
    if (microtime(true) >= $deadline) { $complete = false; break; }
    $skip = '';
    $taken_by = get_posts(['name' => $slug, 'post_type' => ['post', 'page'], 'post_status' => ['publish', 'future', 'draft', 'pending', 'private'], 'numberposts' => 1, 'fields' => 'ids', 'suppress_filters' => true]);
    if ($taken_by) { $skip = 'adres zajęty przez inny wpis (ID ' . (int) $taken_by[0] . ')'; }
    $notes = [];
    $b = null;
    if ($skip === '') {
      $b = zp_feed_build($entry, $notes);
      if (is_wp_error($b)) {
        if ($b->get_error_data()['network'] ?? false) { $network = $b->get_error_message(); continue; }
        $skip = $b->get_error_message();
      }
    }
    if ($skip === '' && ($owner = zp_feed_focus_owner($b['focus'], 0))) {
      $skip = 'fraza „' . $b['focus'] . '” należy już do ' . zp_seo_plan_path((string) get_permalink($owner));
    }
    if ($skip === '') {
      foreach ($state as $other => $os) {
        if ($other !== $slug && !empty($os['id']) && ($os['entry']['focus'] ?? '') !== '' && explode(',', (string) $os['entry']['focus'])[0] === $b['focus'] && get_post_status((int) $os['id']) !== 'trash') {
          $skip = 'fraza „' . $b['focus'] . '” należy już do wpisu z kanału ' . $other;
          break;
        }
      }
    }
    if ($skip !== '') {
      if (($s['skipped'] ?? '') !== $skip || ($s['sha'] ?? '') !== $entry['sha']) { $log[] = 'Pominięto ' . $entry['path'] . ': ' . $skip . '.'; }
      $state[$slug] = ['skipped' => $skip, 'sha' => $entry['sha'], 'path' => $entry['path'], 'title' => $b && !is_wp_error($b) ? $b['title'] : $slug, 'planned' => $entry['date']->format(DATE_ATOM)];
      continue;
    }
    $when = $schedule ? zp_feed_slot($entry['date'], $taken) : null;
    $id = zp_feed_write($b, $entry, 0, $schedule ? 'future' : 'draft', $when, $notes);
    if (is_wp_error($id)) {
      $log[] = 'BŁĄD: nie udało się zapisać ' . $entry['path'] . ': ' . $id->get_error_message() . '.';
      continue;
    }
    $set = (string) get_post_status($id);
    if ($when && $set !== 'future') { unset($taken[$when->format('Y-m-d')]); }
    if (get_post_field('post_name', $id) !== $slug) { $notes[] = 'UWAGA: adres to ' . zp_seo_plan_path((string) get_permalink($id)); }
    $state[$slug] = [
      'id' => $id, 'path' => $entry['path'], 'title' => $b['title'], 'sha' => $entry['sha'], 'set' => $set, 'held' => $set === 'draft' ? $hold : '',
      'planned' => $entry['date']->format(DATE_ATOM), 'date' => $set === 'future' ? (string) get_post_field('post_date', $id) : '',
      'entry' => zp_feed_plan_entry($b), 'rm' => zp_feed_write_rank_math($id, $b, []), 'hash' => zp_feed_hashes($id), 'batch' => $entry['batch'],
    ];
    $new[] = $slug;
    $log[] = ($set === 'future' ? 'Zaplanowano na ' . zp_feed_day_label($state[$slug]['date']) . ': ' : 'Zapisano jako szkic: ') . $b['title'] . ' (' . $entry['path'] . ', ID ' . $id . ')'
      . ($notes ? ' — ' . implode('; ', array_unique($notes)) : '') . '.';
  }

  // Taken out of the feed before publication: back to drafts. Published posts stay.
  foreach ($state as $slug => $s) {
    if (isset($manifest['posts'][$slug])) { continue; }
    $id = (int) ($s['id'] ?? 0);
    if (!$id) { unset($state[$slug]); continue; }
    if (get_post_status($id) === 'future' && ($s['held'] ?? '') === '') {
      zp_feed_update_post(['ID' => $id, 'post_status' => 'draft']);
      $state[$slug]['held'] = 'removed';
      $state[$slug]['hash'] = zp_feed_hashes($id);
      $log[] = 'Wpis „' . $s['title'] . '” zniknął z kanału przed publikacją: przeniesiony do szkiców.';
    }
  }

  update_option(ZP_FEED_STATE, $state, false);
  update_option(ZP_FEED_DISPLAY, [
    'entries' => zp_feed_entries_from_state($state),
    'paths' => array_values(array_map(static function ($e) { return $e['path']; }, $manifest['posts'])),
    'links' => $manifest['links'],
    'sections' => $manifest['sections'],
  ], false);

  if ($new) {
    $status['mail'] = array_values(array_unique(array_merge((array) $status['mail'], $new)));
    if (class_exists('\Elementor\Plugin') && isset(\Elementor\Plugin::$instance->files_manager)) {
      try { \Elementor\Plugin::$instance->files_manager->clear_cache(); } catch (\Throwable $e) { /* not fatal */ }
    }
  }
  if (!$complete) {
    wp_schedule_single_event(time() + 120, 'zp_feed_check');
    $log[] = 'Reszta wpisów wczyta się przy kolejnym przebiegu za ok. 2 minuty.';
  } elseif ($status['mail']) {
    zp_feed_mail_imported((array) $status['mail'], $state, $settings);
    $status['mail'] = [];
  }
  if ($network !== '') {
    $status['fails']++;
    $status['last_error'] = $network;
    $log[] = 'Nie udało się pobrać części plików (' . $network . '). Spróbuję przy następnym sprawdzeniu.';
  } else {
    $status['fails'] = 0;
    $status['last_error'] = '';
    $status['last_ok'] = time();
  }
  $log[] = 'Sprawdzono kanał: wpisów w kanale ' . count($manifest['posts']) . ', nowych ' . count($new) . '.';
  return $log;
}

/** What zp_seo_plan_entry() returns for an imported article (service cards, H1 handling). */
function zp_feed_plan_entry(array $b): array {
  return [
    'kind' => 'post', 'focus' => $b['focus_all'], 'title' => $b['seo_title'], 'description' => $b['description'], 'h1' => $b['title'],
    'editorial_title' => '', 'editorial_description' => '', 'links' => $b['links'],
  ];
}

function zp_feed_entries_from_state(array $state): array {
  $out = [];
  foreach ($state as $s) {
    if (!empty($s['id']) && !empty($s['entry']) && !empty($s['path'])) { $out[$s['path']] = $s['entry']; }
  }
  return $out;
}

/** Plan entry for an imported article, or null (used by zp_seo_plan_entry()). */
function zp_feed_entry(string $path): ?array {
  $entries = (array) (zp_feed_display()['entries'] ?? []);
  return $entries[$path] ?? null;
}

function zp_feed_display(): array {
  static $display = null;
  if ($display === null) { $display = (array) get_option(ZP_FEED_DISPLAY, []); }
  return $display;
}

/* ------------------------------------------------------------------ actions from the panel */

/** Scheduled feed posts to drafts and new ones as drafts until resumed. */
function zp_feed_hold(): array {
  zp_feed_update_settings(['paused' => true]);
  $state = zp_feed_state();
  $n = 0;
  foreach ($state as $slug => $s) {
    $id = (int) ($s['id'] ?? 0);
    if ($id && get_post_status($id) === 'future') {
      zp_feed_update_post(['ID' => $id, 'post_status' => 'draft']);
      $state[$slug]['held'] = 'pause';
      $state[$slug]['hash'] = zp_feed_hashes($id);
      $n++;
    }
  }
  update_option(ZP_FEED_STATE, $state, false);
  return ['Wstrzymano publikację: zaplanowane wpisy w szkicach (' . $n . '), nowe z kanału też trafią do szkiców.'];
}

/** Back to the plan: posts held by the pause get new dates (nearest free working days if theirs passed). */
function zp_feed_resume(): array {
  zp_feed_update_settings(['paused' => false]);
  $state = zp_feed_state();
  $taken = zp_feed_taken_days();
  $log = [];
  $order = $state;
  uasort($order, static function ($a, $b) { return strcmp((string) ($a['planned'] ?? ''), (string) ($b['planned'] ?? '')); });
  foreach ($order as $slug => $s) {
    $id = (int) ($s['id'] ?? 0);
    if (!$id || ($s['held'] ?? '') !== 'pause' || get_post_status($id) !== 'draft') { continue; }
    try { $wanted = new DateTimeImmutable((string) $s['planned'], wp_timezone()); } catch (\Throwable $e) { $wanted = new DateTimeImmutable('now', wp_timezone()); }
    $when = zp_feed_slot($wanted, $taken);
    zp_feed_set_date($id, $when, 'future');
    $state[$slug] = array_merge($s, ['held' => '', 'set' => 'future', 'date' => $when->format('Y-m-d H:i:s'), 'hash' => zp_feed_hashes($id)]);
    $log[] = 'Zaplanowano ponownie na ' . zp_feed_day_label($state[$slug]['date']) . ': ' . $s['title'] . '.';
  }
  update_option(ZP_FEED_STATE, $state, false);
  $log[] = 'Publikacja według planu wznowiona.';
  return $log;
}

/** Every feed post (published ones too) to drafts, and the module paused. */
function zp_feed_undo_all(): array {
  zp_feed_update_settings(['paused' => true]);
  $state = zp_feed_state();
  $n = 0;
  foreach ($state as $slug => $s) {
    $id = (int) ($s['id'] ?? 0);
    if ($id && in_array(get_post_status($id), ['future', 'publish'], true)) {
      zp_feed_update_post(['ID' => $id, 'post_status' => 'draft']);
      $state[$slug]['held'] = 'undo';
      $state[$slug]['hash'] = zp_feed_hashes($id);
      $n++;
    }
  }
  update_option(ZP_FEED_STATE, $state, false);
  return ['Cofnięto: wpisy z kanału w szkicach (' . $n . '), moduł wstrzymany.'];
}

/* ------------------------------------------------------------------ e-mails */

function zp_feed_mail_imported(array $slugs, array $state, array $settings): void {
  if ($settings['email'] === '' || !is_email($settings['email'])) { return; }
  $rows = [];
  $dates = [];
  foreach ($slugs as $slug) {
    $s = (array) ($state[$slug] ?? []);
    if (empty($s['id'])) { continue; }
    $date = (string) ($s['date'] ?? '');
    if ($date !== '') { $dates[] = $date; }
    $rows[] = ($date !== '' ? zp_feed_day_label($date) . ' — ' : 'szkic — ') . $s['title'] . "\n   " . admin_url('post.php?post=' . (int) $s['id'] . '&action=edit');
  }
  if (!$rows) { return; }
  sort($dates);
  $range = $dates ? ' (' . date('j.m', strtotime($dates[0])) . (count($dates) > 1 ? '–' . date('j.m', strtotime(end($dates))) : '') . ')' : '';
  $drafts = count($dates) < count($rows);
  $subject = ($drafts && !$dates ? 'Artykuły do akceptacji: ' : 'Zaplanowane artykuły: ') . count($rows) . $range;
  $body = ($dates ? "Nowe artykuły z planu treści są zaplanowane na stronie. WordPress opublikuje je sam o 8:00 w podanych dniach.\n\n"
                  : "Nowe artykuły z planu treści czekają w szkicach. Opublikuj je w WordPressie, gdy je przejrzysz.\n\n")
    . implode("\n\n", $rows)
    . "\n\nŻeby wstrzymać publikację, otwórz ZP Suite → Publikacja wpisów i kliknij „Przenieś zaplanowane do szkiców”: "
    . admin_url('admin.php?page=zp-suite-publikacja') . "\n";
  wp_mail($settings['email'], $subject, $body);
}

add_action('transition_post_status', function ($new, $old, $post) {
  if ($new !== 'publish' || $old === 'publish' || !$post instanceof WP_Post || $post->post_type !== 'post') { return; }
  $slug = (string) get_post_meta($post->ID, '_zp_feed_slug', true);
  if ($slug === '' || get_post_meta($post->ID, '_zp_feed_mailed', true)) { return; }
  update_post_meta($post->ID, '_zp_feed_mailed', time());
  $url = (string) get_permalink($post->ID);
  zp_feed_log(['Opublikowano: ' . $post->post_title . ' (' . zp_seo_plan_path($url) . ').']);
  $settings = zp_feed_settings();
  if ($settings['email'] === '' || !is_email($settings['email'])) { return; }
  $gbp = trim((string) get_post_meta($post->ID, '_zp_feed_gbp', true));
  if ($gbp !== '' && strpos($gbp, 'zaprojektowani.com' . zp_seo_plan_path($url)) === false) { $gbp .= "\n" . $url; }
  $body = 'Na stronie jest nowy artykuł:' . "\n" . $post->post_title . "\n" . $url . "\n"
    . ($gbp !== '' ? "\nPost do wizytówki Google (Google Business Profile → Dodaj aktualność, skopiuj i wklej):\n\n" . $gbp . "\n" : '')
    . "\nZP Suite → Publikacja wpisów: " . admin_url('admin.php?page=zp-suite-publikacja') . "\n";
  wp_mail($settings['email'], 'Nowy artykuł na stronie: ' . $post->post_title, $body);
}, 10, 3);

/* ------------------------------------------------------------------ scheduling */

add_filter('cron_schedules', function ($schedules) {
  $schedules['zp_feed_6h'] = ['interval' => 6 * HOUR_IN_SECONDS, 'display' => 'Co 6 godzin (Publikacja wpisów)'];
  return $schedules;
});

add_action('zp_feed_check', function () { zp_feed_run('cron'); });

add_action('init', function () {
  $settings = zp_feed_settings();
  $next = wp_next_scheduled('zp_feed_check');
  if ($settings['enabled'] && !$next) { wp_schedule_event(time() + 300, 'zp_feed_6h', 'zp_feed_check'); }
  if (!$settings['enabled'] && $next) { wp_clear_scheduled_hook('zp_feed_check'); }
  zp_feed_guard();
}, 20);

/**
 * Publishes a feed post whose time passed more than 5 minutes ago when WP-Cron did not run
 * ("missed schedule"). Only the oldest one goes out; any other late posts move to the next free
 * working days, so readers never get several articles at once. At most every 15 minutes.
 */
function zp_feed_guard(): void {
  // An autoloaded option rather than a transient: no extra database query on page views.
  if (wp_doing_ajax() || (int) get_option('zp_feed_guard_at', 0) > time() - 15 * MINUTE_IN_SECONDS) { return; }
  update_option('zp_feed_guard_at', time(), true);
  $late = get_posts([
    'post_type' => 'post', 'post_status' => 'future', 'numberposts' => 20, 'orderby' => 'date', 'order' => 'ASC', 'meta_key' => '_zp_feed_slug', 'suppress_filters' => true,
    'date_query' => [['column' => 'post_date_gmt', 'before' => gmdate('Y-m-d H:i:s', time() - 300)]],
  ]);
  if (!$late) { return; }
  $first = array_shift($late);
  wp_publish_post($first);
  $log = ['Opublikowano spóźniony wpis „' . $first->post_title . '” (WP-Cron nie zadziałał o czasie).'];
  if ($late) {
    $state = zp_feed_state();
    $taken = zp_feed_taken_days();
    foreach ($late as $p) {
      unset($taken[substr($p->post_date, 0, 10)]);
      $when = zp_feed_slot(new DateTimeImmutable('now', wp_timezone()), $taken);
      zp_feed_set_date($p->ID, $when, 'future');
      $slug = (string) get_post_meta($p->ID, '_zp_feed_slug', true);
      if (isset($state[$slug])) { $state[$slug]['date'] = $when->format('Y-m-d H:i:s'); }
      $log[] = 'Spóźniony wpis „' . $p->post_title . '” przesunięty na ' . zp_feed_day_label($when->format('Y-m-d H:i:s')) . '.';
    }
    update_option(ZP_FEED_STATE, $state, false);
  }
  zp_feed_log($log);
}

/**
 * Fallback when WP-Cron does not run: after a page has been sent to the visitor, a check that
 * is more than 7 hours overdue runs in the same request. Without a way to finish the response
 * first, this happens only in wp-admin.
 */
add_action('shutdown', function () {
  if (wp_doing_cron() || wp_doing_ajax() || (defined('REST_REQUEST') && REST_REQUEST) || (defined('WP_CLI') && WP_CLI)) { return; }
  if (!in_array($_SERVER['REQUEST_METHOD'] ?? 'GET', ['GET', 'HEAD'], true) || !zp_feed_settings()['enabled']) { return; }
  if (time() - (int) zp_feed_status()['last_try'] < 7 * HOUR_IN_SECONDS || get_option('zp_feed_lock')) { return; }
  if (function_exists('fastcgi_finish_request')) { fastcgi_finish_request(); }
  elseif (function_exists('litespeed_finish_request')) { litespeed_finish_request(); }
  elseif (!is_admin()) { return; }
  zp_feed_run('zapas');
}, 1000);

/* ------------------------------------------------------------------ display (posts.php) */

/** Links from older posts to feed articles: path => [[exact fragment, words, target]]. */
function zp_feed_links_for(string $path): array {
  return (array) (((array) (zp_feed_display()['links'] ?? []))[$path] ?? []);
}

function zp_feed_sections_for(string $path): array {
  return (array) (((array) (zp_feed_display()['sections'] ?? []))[$path] ?? []);
}

/** The fragment with its words linked, outside tags and existing links; null when not possible. */
function zp_feed_link_fragment(string $fragment, string $words, string $to): ?string {
  $parts = preg_split('~(<[^>]*>)~', $fragment, -1, PREG_SPLIT_DELIM_CAPTURE);
  $in_link = 0;
  foreach ($parts as $i => $part) {
    if ($part !== '' && $part[0] === '<') {
      if (preg_match('~^<a\b~i', $part)) { $in_link++; } elseif (preg_match('~^</a\s*>~i', $part)) { $in_link = max(0, $in_link - 1); }
      continue;
    }
    if ($in_link || ($at = strpos($part, $words)) === false) { continue; }
    $parts[$i] = substr($part, 0, $at) . '<a href="' . esc_url(home_url($to)) . '">' . $words . '</a>' . substr($part, $at + strlen($words));
    return implode('', $parts);
  }
  return null;
}

/** Links to feed articles that are not published yet show as plain text (no links to 404 pages). */
function zp_feed_unlink_pending(string $html): string {
  $paths = (array) (zp_feed_display()['paths'] ?? []);
  if (!$paths || stripos($html, '<a') === false) { return $html; }
  $alt = implode('|', array_map(static function ($p) { return preg_quote($p, '~'); }, $paths));
  return (string) preg_replace_callback('~<a\b[^>]*\shref=["\'](?:https?://(?:www\.)?zaprojektowani\.com)?(' . $alt . ')(?:[#?][^"\']*)?["\'][^>]*>(.*?)</a>~is', static function ($m) {
    return zp_seo_plan_link_is_live($m[1]) ? $m[0] : $m[2];
  }, $html);
}
