<?php
/**
 * Zapytania: one inbox in wp-admin for everything clients send. It replaces the old "Formularze / leady"
 * and "Zamówienia" screens.
 *
 * Records stay where the forms save them: the contact forms in option zp_suite_leads (forms.php) and
 * Studio wyceny in option zp_studio_wyceny_orders (studio-wyceny.php). Each store keeps its own status keys;
 * the inbox shows one simple status set and writes the matching key back only when the status really changes.
 * Every change goes through admin-post.php and comes back with a notice, so a refresh never repeats it.
 */
if (!defined('ABSPATH')) { exit; }

/** Statuses shown in the inbox. */
function zp_inbox_statuses(): array {
  return [
    'new' => 'Nowe',
    'contact' => 'W kontakcie',
    'quote' => 'Wycena wysłana',
    'won' => 'Zlecenie',
    'closed' => 'Zamknięte',
    'spam' => 'Spam',
  ];
}

/** Stored status => inbox status. Old keys keep their meaning ("Przeczytane", "Zaakceptowane", "W realizacji"...). */
function zp_inbox_status_read(string $type, string $native): string {
  if ($native === '') { return 'new'; }
  $map = $type === 'studio'
    ? ['new' => 'new', 'contact' => 'contact', 'quote_sent' => 'quote', 'accepted' => 'won', 'payment_pending' => 'won', 'production' => 'won', 'done' => 'closed', 'archived' => 'closed', 'spam' => 'spam']
    : ['new' => 'new', 'read' => 'contact', 'progress' => 'contact', 'called' => 'contact', 'offer' => 'quote', 'won' => 'won', 'closed' => 'closed', 'spam' => 'spam'];
  return $map[$native] ?? 'contact';
}

/** Inbox status => key stored in the record. */
function zp_inbox_status_write(string $type, string $status): string {
  $map = $type === 'studio'
    ? ['new' => 'new', 'contact' => 'contact', 'quote' => 'quote_sent', 'won' => 'accepted', 'closed' => 'done', 'spam' => 'spam']
    : ['new' => 'new', 'contact' => 'progress', 'quote' => 'offer', 'won' => 'won', 'closed' => 'closed', 'spam' => 'spam'];
  return $map[$status] ?? 'new';
}

function zp_inbox_type_label(string $type): string {
  return $type === 'studio' ? 'Studio wyceny' : 'Formularz';
}

function zp_inbox_store(string $type): string {
  if ($type === 'studio') { return defined('ZP_STUDIO_OPT_ORDERS') ? ZP_STUDIO_OPT_ORDERS : 'zp_studio_wyceny_orders'; }
  return 'zp_suite_leads';
}

function zp_inbox_raw(string $type): array {
  $rows = get_option(zp_inbox_store($type), []);
  return is_array($rows) ? array_values($rows) : [];
}

function zp_inbox_write(string $type, array $rows): void {
  update_option(zp_inbox_store($type), array_values($rows), false);
}

/** Form values as text; the "—" the forms store for an empty field counts as empty. */
function zp_inbox_val($v): string {
  if (is_array($v) || is_object($v)) { return ''; }
  $v = trim((string) $v);
  return in_array($v, ['—', '–', '-'], true) ? '' : $v;
}

function zp_inbox_lower(string $s): string {
  return function_exists('mb_strtolower') ? mb_strtolower($s, 'UTF-8') : strtolower($s);
}

/** Every inquiry from both stores, newest first, in one shape. */
function zp_inbox_items(): array {
  $items = [];
  foreach (zp_inbox_raw('form') as $r) { if (is_array($r) && !empty($r['id'])) { $items[] = zp_inbox_from_lead($r); } }
  foreach (zp_inbox_raw('studio') as $r) { if (is_array($r) && !empty($r['id'])) { $items[] = zp_inbox_from_order($r); } }
  usort($items, function ($a, $b) { return $b['ts'] <=> $a['ts']; });
  return $items;
}

function zp_inbox_item(string $type, string $id, string $native, string $created): array {
  return [
    'type' => $type,
    'id' => $id,
    'key' => $type . ':' . $id,
    'anchor' => 'zp-' . $type . '-' . sanitize_html_class($id),
    // Stored as Warsaw time; read the same way as current_time('timestamp').
    'ts' => $created !== '' ? (int) strtotime($created) : 0,
    'native' => $native,
    'status' => zp_inbox_status_read($type, $native),
    'name' => '', 'email' => '', 'phone' => '', 'company' => '', 'service' => '', 'kind' => '',
    'source' => '', 'message' => '', 'website' => '', 'note' => '', 'files' => [], 'log' => [], 'details' => [],
  ];
}

function zp_inbox_from_lead(array $r): array {
  $it = zp_inbox_item('form', (string) $r['id'], (string) ($r['status'] ?? ''), (string) ($r['created_at'] ?? ''));
  $services = zp_inbox_val($r['services'] ?? '');
  $name = zp_inbox_val($r['name'] ?? '');
  $popup = stripos($services, 'exit popup') !== false;
  $modes = ['phone' => 'Prośba o oddzwonienie', 'call' => 'Prośba o oddzwonienie', 'brief' => 'Pełny brief projektu', 'quick' => 'Szybki kontakt', 'form' => 'Szybki kontakt', 'email' => 'Szybki kontakt', 'short' => 'Krótki formularz'];
  $mode = zp_inbox_val($r['contact_mode'] ?? '');
  $it['kind'] = $popup ? 'Popup: prośba o telefon' : ($modes[$mode] ?? ($mode !== '' ? $mode : 'Formularz'));
  $it['email'] = is_email(zp_inbox_val($r['email'] ?? '')) ? zp_inbox_val($r['email']) : '';
  $it['phone'] = zp_inbox_val($r['phone'] ?? '');
  $it['company'] = zp_inbox_val($r['company'] ?? '');
  // The exit popup sends a placeholder instead of a name.
  $it['name'] = ($popup || $name === 'Popup — darmowa konsultacja') ? '' : $name;
  $it['service'] = $popup ? '' : $services;
  $it['message'] = zp_inbox_val($r['message'] ?? '');
  // The source repeats the form's name ("Krótki formularz — strona główna"); the popup's is only a default label.
  $source = $popup ? '' : zp_inbox_val($r['source'] ?? '');
  if ($source !== '' && stripos($source, $it['kind'] . ' — ') === 0) { $source = substr($source, strlen($it['kind'] . ' — ')); }
  $it['source'] = $source;
  $it['website'] = zp_inbox_val($r['website'] ?? '');
  $it['note'] = (string) ($r['note'] ?? '');
  $it['files'] = isset($r['files']) && is_array($r['files']) ? $r['files'] : [];
  $it['log'] = isset($r['status_log']) && is_array($r['status_log']) ? $r['status_log'] : [];

  $labels = ['budget' => 'Budżet', 'deadline' => 'Termin', 'website' => 'Strona www', 'callback_time' => 'Kiedy zadzwonić', 'callback_topic' => 'Temat rozmowy', 'priority' => 'Priorytet', 'tags' => 'Tagi', 'reminder' => 'Przypomnienie'];
  $skip = ['id', 'created_at', 'updated_at', 'status', 'status_log', 'note', 'ip', 'user_agent', 'name', 'company', 'phone', 'email', 'message', 'services', 'files', 'source', 'contact_mode'];
  foreach ($r as $k => $v) {
    if (in_array($k, $skip, true)) { continue; }
    $v = is_array($v) ? implode(', ', array_filter(array_map('zp_inbox_val', $v), 'strlen')) : zp_inbox_val($v);
    if ($v !== '') { $it['details'][] = [$labels[$k] ?? ucfirst(str_replace('_', ' ', (string) $k)), $v]; }
  }
  return zp_inbox_finish($it);
}

function zp_inbox_from_order(array $r): array {
  $it = zp_inbox_item('studio', (string) $r['id'], (string) ($r['status'] ?? ''), (string) ($r['created'] ?? ''));
  $p = isset($r['payload']) && is_array($r['payload']) ? $r['payload'] : [];
  $pick = function (array $keys) use ($p) {
    foreach ($keys as $k) { $v = zp_inbox_val($p[$k] ?? ''); if ($v !== '') { return $v; } }
    return '';
  };
  $it['kind'] = 'Studio wyceny';
  $it['name'] = $pick(['Imię i nazwisko', 'Imię', 'Imię / firma', 'Name']);
  $email = $pick(['E-mail', 'Email', 'email']);
  $it['email'] = is_email($email) ? $email : '';
  $it['phone'] = $pick(['Telefon', 'Phone']);
  $it['company'] = $pick(['Firma', 'Company']);
  // The English studio may store translated labels (Service / Package).
  $it['service'] = implode(' · ', array_filter([$pick(['Usługa', 'Service']), $pick(['Pakiet', 'Package'])], 'strlen'));
  $it['message'] = $pick(['Brief']);
  $it['note'] = (string) ($r['admin_note'] ?? '');
  $it['files'] = isset($r['files']) && is_array($r['files']) ? $r['files'] : [];
  $it['log'] = isset($r['status_log']) && is_array($r['status_log']) ? $r['status_log'] : [];

  $shown = ['Imię i nazwisko', 'Imię', 'Imię / firma', 'Name', 'E-mail', 'Email', 'email', 'Telefon', 'Phone', 'Firma', 'Company', 'Usługa', 'Service', 'Pakiet', 'Package', 'Brief', 'Pliki'];
  $labels = ['Kontakt' => 'Woli kontakt przez', 'Linki / inspiracje' => 'Linki i inspiracje', 'instagram' => 'Instagram', 'facebook' => 'Facebook', 'content' => 'Treści'];
  foreach ($p as $k => $v) {
    if (in_array($k, $shown, true)) { continue; }
    $v = zp_inbox_val($v);
    if ($v !== '') { $it['details'][] = [$labels[$k] ?? (string) $k, $v]; }
  }
  return zp_inbox_finish($it);
}

/** Display name and the text searched by the search box. */
function zp_inbox_finish(array $it): array {
  if ($it['name'] === '') { $it['name'] = $it['company'] !== '' ? $it['company'] : ($it['phone'] !== '' ? $it['phone'] : ($it['email'] !== '' ? $it['email'] : 'Bez imienia')); }
  $parts = [$it['id'], $it['name'], $it['email'], $it['phone'], preg_replace('/\D+/', '', $it['phone']), $it['company'], $it['service'], $it['kind'], $it['source'], $it['message'], $it['note']];
  foreach ($it['details'] as $d) { $parts[] = $d[1]; }
  $it['search'] = zp_inbox_lower(implode("\n", $parts));
  return $it;
}

/** Number of inquiries with status "Nowe" (menu badge). Reads the raw stores, so it stays cheap on every admin page. */
function zp_inbox_new_count(): int {
  static $n = null;
  if ($n !== null) { return $n; }
  $n = 0;
  foreach (['form', 'studio'] as $type) {
    foreach (zp_inbox_raw($type) as $r) {
      if (is_array($r) && !empty($r['id']) && zp_inbox_status_read($type, (string) ($r['status'] ?? '')) === 'new') { $n++; }
    }
  }
  return $n;
}

function zp_inbox_item_url(array $it): string {
  return add_query_arg(['page' => 'zp-suite-zapytania', 'zp_id' => $it['key']], admin_url('admin.php'));
}

function zp_inbox_status_pill(string $status): string {
  $statuses = zp_inbox_statuses();
  $class = ['new' => 'is-new', 'quote' => 'is-quote', 'won' => 'is-ok', 'closed' => 'is-off', 'spam' => 'is-spam'][$status] ?? '';
  return '<span class="zpxPill ' . esc_attr($class) . '">' . esc_html($statuses[$status] ?? $status) . '</span>';
}

/** "dziś, 09:12", "wczoraj, 18:40", "3.10, 12:05" or "14.11.2025"; $ts is Warsaw time like current_time('timestamp'). */
function zp_inbox_date(int $ts): string {
  if ($ts <= 0) { return ''; }
  $now = current_time('timestamp');
  $day = gmdate('Y-m-d', $ts);
  if ($day === gmdate('Y-m-d', $now)) { return 'dziś, ' . gmdate('H:i', $ts); }
  if ($day === gmdate('Y-m-d', $now - DAY_IN_SECONDS)) { return 'wczoraj, ' . gmdate('H:i', $ts); }
  if (gmdate('Y', $ts) === gmdate('Y', $now)) { return gmdate('j.m, H:i', $ts); }
  return gmdate('j.m.Y', $ts);
}

function zp_inbox_excerpt(string $text, int $len): string {
  $text = trim(preg_replace('/\s+/u', ' ', $text));
  if (function_exists('mb_strlen') && mb_strlen($text) > $len) { return rtrim(mb_substr($text, 0, $len - 1)) . '…'; }
  return $text;
}

/** Filters from the query string: type tab, status, search, one inquiry by key. */
function zp_inbox_filters(array $src): array {
  $typ = sanitize_key(wp_unslash($src['typ'] ?? ''));
  $status = sanitize_key(wp_unslash($src['status'] ?? ''));
  return [
    'typ' => in_array($typ, ['form', 'studio'], true) ? $typ : '',
    'status' => isset(zp_inbox_statuses()[$status]) ? $status : '',
    'q' => trim(sanitize_text_field(wp_unslash($src['q'] ?? ''))),
    'zp_id' => sanitize_text_field(wp_unslash($src['zp_id'] ?? '')),
  ];
}

function zp_inbox_filter(array $items, array $f): array {
  $q = $f['q'] !== '' ? zp_inbox_lower($f['q']) : '';
  $digits = preg_replace('/\D+/', '', $f['q']);
  $digits = strlen($digits) >= 5 && preg_match('/^[\d\s+()-]+$/', $f['q']) ? $digits : '';
  return array_values(array_filter($items, function ($it) use ($f, $q, $digits) {
    if ($f['zp_id'] !== '' && $it['key'] !== $f['zp_id']) { return false; }
    if ($f['typ'] !== '' && $it['type'] !== $f['typ']) { return false; }
    if ($f['status'] !== '' && $it['status'] !== $f['status']) { return false; }
    if ($q !== '' && strpos($it['search'], $q) === false && ($digits === '' || strpos($it['search'], $digits) === false)) { return false; }
    return true;
  }));
}

/* ----------------------------------------------------------------------------------------------
 * Changes: save one, delete one, bulk, CSV. All POST-redirect-GET through admin-post.php.
 * ---------------------------------------------------------------------------------------------- */

function zp_inbox_guard(string $action): void {
  if (!current_user_can('manage_options')) { wp_die('Brak uprawnień.', 403); }
  check_admin_referer($action);
}

/** Back to the list with the filters the person had, plus a notice. */
function zp_inbox_back(string $msg, int $n = 0, string $anchor = ''): void {
  $args = ['page' => 'zp-suite-zapytania'];
  wp_parse_str((string) wp_unslash($_POST['back'] ?? ''), $back);
  foreach (['typ', 'status', 'q', 'paged', 'zp_id'] as $k) {
    if (isset($back[$k]) && is_scalar($back[$k]) && $back[$k] !== '') { $args[$k] = sanitize_text_field((string) $back[$k]); }
  }
  $args['zp_msg'] = $msg;
  if ($n) { $args['zp_n'] = $n; }
  wp_safe_redirect(add_query_arg(array_map('rawurlencode', $args), admin_url('admin.php')) . ($anchor !== '' ? '#' . $anchor : ''));
  exit;
}

function zp_inbox_post_type(): string {
  return sanitize_key(wp_unslash($_POST['type'] ?? '')) === 'studio' ? 'studio' : 'form';
}

/** Apply a status and/or a note to one record; returns [record, changed]. Each change is logged with date and user. */
function zp_inbox_apply(string $type, array $r, ?string $status, ?string $note): array {
  $now = current_time('mysql');
  $user = wp_get_current_user();
  $who = $user && $user->exists() ? $user->user_login : '';
  $log = isset($r['status_log']) && is_array($r['status_log']) ? $r['status_log'] : [];
  $changed = false;
  if ($status !== null && isset(zp_inbox_statuses()[$status]) && zp_inbox_status_read($type, (string) ($r['status'] ?? '')) !== $status) {
    $r['status'] = zp_inbox_status_write($type, $status);
    $log[] = ['status' => $r['status'], 'date' => $now, 'user' => $who];
    $changed = true;
  }
  $field = $type === 'studio' ? 'admin_note' : 'note';
  if ($note !== null && $note !== (string) ($r[$field] ?? '')) {
    $r[$field] = $note;
    $log[] = ['note' => '1', 'date' => $now, 'user' => $who];
    $changed = true;
  }
  if ($changed) {
    $r['status_log'] = array_slice($log, -40);
    if ($type === 'form') { $r['updated_at'] = $now; }
  }
  return [$r, $changed];
}

add_action('admin_post_zp_inbox_save', function () {
  zp_inbox_guard('zp_inbox_save');
  $type = zp_inbox_post_type();
  $id = sanitize_text_field(wp_unslash($_POST['id'] ?? ''));
  $status = sanitize_key(wp_unslash($_POST['status'] ?? ''));
  $note = sanitize_textarea_field(wp_unslash($_POST['note'] ?? ''));
  $rows = zp_inbox_raw($type);
  foreach ($rows as $i => $r) {
    if (!is_array($r) || (string) ($r['id'] ?? '') !== $id) { continue; }
    [$rows[$i], $changed] = zp_inbox_apply($type, $r, $status, $note);
    if ($changed) { zp_inbox_write($type, $rows); }
    zp_inbox_back('saved', 0, 'zp-' . $type . '-' . sanitize_html_class($id));
  }
  zp_inbox_back('missing');
});

/** Remove records ($by = ['form' => [id => true], 'studio' => [...]]) and the files their visitors uploaded. */
function zp_inbox_delete(array $by): int {
  $n = 0;
  $files = [];
  foreach (['form', 'studio'] as $type) {
    if (empty($by[$type])) { continue; }
    $rows = zp_inbox_raw($type);
    $keep = [];
    foreach ($rows as $r) {
      if (is_array($r) && isset($by[$type][(string) ($r['id'] ?? '')])) {
        $n++;
        foreach ((array) ($r['files'] ?? []) as $f) { if (is_array($f)) { $files[] = $f; } }
      } else {
        $keep[] = $r;
      }
    }
    if (count($keep) !== count($rows)) { zp_inbox_write($type, $keep); }
  }
  if ($files) { zp_inbox_delete_files($files); }
  return $n;
}

/** Deletes uploaded files only inside wp-content/uploads and only when no remaining inquiry points to them. */
function zp_inbox_delete_files(array $files): int {
  $up = wp_upload_dir(null, false);
  $base = !empty($up['basedir']) ? realpath($up['basedir']) : false;
  if (!$base) { return 0; }
  $still = [];
  foreach (['form', 'studio'] as $type) {
    foreach (zp_inbox_raw($type) as $r) {
      foreach ((array) (is_array($r) ? ($r['files'] ?? []) : []) as $f) {
        if (is_array($f) && !empty($f['file'])) { $still[(string) $f['file']] = true; }
      }
    }
  }
  $n = 0;
  foreach ($files as $f) {
    $path = (string) ($f['file'] ?? '');
    if ($path === '' || isset($still[$path])) { continue; }
    $real = realpath($path);
    if (!$real || !is_file($real) || strpos($real, $base . DIRECTORY_SEPARATOR) !== 0) { continue; }
    if (@unlink($real)) { $n++; }
  }
  return $n;
}

add_action('admin_post_zp_inbox_delete', function () {
  zp_inbox_guard('zp_inbox_delete');
  $type = zp_inbox_post_type();
  $id = sanitize_text_field(wp_unslash($_POST['id'] ?? ''));
  $n = $id !== '' ? zp_inbox_delete([$type => [$id => true]]) : 0;
  zp_inbox_back($n ? 'deleted' : 'missing');
});

add_action('admin_post_zp_inbox_bulk', function () {
  zp_inbox_guard('zp_inbox_bulk');
  $op = sanitize_key(wp_unslash($_POST['op'] ?? ''));

  // "Close everything still marked Nowe that is older than 30 days" (offered when old inquiries pile up).
  if ($op === 'close-old') {
    $limit = current_time('timestamp') - 30 * DAY_IN_SECONDS;
    $n = 0;
    foreach (['form', 'studio'] as $type) {
      $rows = zp_inbox_raw($type);
      $dirty = false;
      foreach ($rows as $i => $r) {
        if (!is_array($r) || empty($r['id']) || zp_inbox_status_read($type, (string) ($r['status'] ?? '')) !== 'new') { continue; }
        $ts = (int) strtotime((string) ($type === 'studio' ? ($r['created'] ?? '') : ($r['created_at'] ?? '')));
        if (!$ts || $ts >= $limit) { continue; }
        [$rows[$i], $changed] = zp_inbox_apply($type, $r, 'closed', null);
        if ($changed) { $dirty = true; $n++; }
      }
      if ($dirty) { zp_inbox_write($type, $rows); }
    }
    zp_inbox_back('bulk', $n);
  }

  $by = ['form' => [], 'studio' => []];
  foreach ((array) ($_POST['ids'] ?? []) as $key) {
    $parts = explode(':', sanitize_text_field(wp_unslash((string) $key)), 2);
    if (count($parts) === 2 && isset($by[$parts[0]]) && $parts[1] !== '') { $by[$parts[0]][$parts[1]] = true; }
  }
  if (!$by['form'] && !$by['studio']) { zp_inbox_back('none'); }

  if ($op === 'delete') { zp_inbox_back('bulk-deleted', zp_inbox_delete($by)); }
  if (!isset(zp_inbox_statuses()[$op])) { zp_inbox_back('none'); }

  $n = 0;
  foreach ($by as $type => $ids) {
    if (!$ids) { continue; }
    $rows = zp_inbox_raw($type);
    $dirty = false;
    foreach ($rows as $i => $r) {
      if (!is_array($r) || !isset($ids[(string) ($r['id'] ?? '')])) { continue; }
      [$rows[$i], $changed] = zp_inbox_apply($type, $r, $op, null);
      if ($changed) { $dirty = true; $n++; }
    }
    if ($dirty) { zp_inbox_write($type, $rows); }
  }
  zp_inbox_back('bulk', $n);
});

/** CSV cell that Excel shows as text: a leading = + - @ starts a formula unless the cell is just a phone number. */
function zp_inbox_csv_cell($v): string {
  $v = str_replace(["\r\n", "\r"], "\n", (string) $v);
  if ($v !== '' && strpbrk($v[0], "=+-@\t") !== false && !preg_match('/^[+\-]?[\d\s().\/-]+$/', $v)) { $v = "'" . $v; }
  return $v;
}

add_action('admin_post_zp_inbox_csv', function () {
  if (!current_user_can('manage_options')) { wp_die('Brak uprawnień.', 403); }
  check_admin_referer('zp_inbox_csv');
  $items = zp_inbox_filter(zp_inbox_items(), zp_inbox_filters($_GET));
  $statuses = zp_inbox_statuses();
  nocache_headers();
  header('Content-Type: text/csv; charset=utf-8');
  header('Content-Disposition: attachment; filename="zapytania-' . wp_date('Y-m-d') . '.csv"');
  $out = fopen('php://output', 'w');
  // BOM and semicolons: Polish Excel opens the file with the right letters and columns.
  fwrite($out, "\xEF\xBB\xBF");
  fputcsv($out, ['Data', 'Rodzaj', 'Status', 'Imię i nazwisko', 'Telefon', 'E-mail', 'Firma', 'Usługa', 'Wiadomość', 'Szczegóły', 'Notatka', 'Skąd', 'ID'], ';', '"', '');
  foreach ($items as $it) {
    $details = implode("\n", array_map(function ($d) { return $d[0] . ': ' . $d[1]; }, $it['details']));
    $row = [$it['ts'] ? gmdate('Y-m-d H:i', $it['ts']) : '', $it['kind'], $statuses[$it['status']] ?? $it['status'], $it['name'], $it['phone'], $it['email'], $it['company'], $it['service'], $it['message'], $details, $it['note'], $it['source'], $it['id']];
    fputcsv($out, array_map('zp_inbox_csv_cell', $row), ';', '"', '');
  }
  fclose($out);
  exit;
});

/* ----------------------------------------------------------------------------------------------
 * Menu and screen.
 * ---------------------------------------------------------------------------------------------- */

add_action('admin_menu', function () {
  $new = zp_inbox_new_count();
  $badge = $new > 0 ? ' <span class="awaiting-mod"><span class="pending-count">' . (int) $new . '</span></span>' : '';
  add_submenu_page('zp-suite', 'Zapytania', 'Zapytania' . $badge, 'manage_options', 'zp-suite-zapytania', 'zp_inbox_render_page');
});

/** One line of the change history under an inquiry. */
function zp_inbox_log_line(string $type, array $entry): string {
  $when = !empty($entry['date']) ? zp_inbox_date((int) strtotime((string) $entry['date'])) : '';
  $who = !empty($entry['user']) ? (string) $entry['user'] : '';
  if (!empty($entry['note'])) {
    $what = 'zmieniona notatka';
  } else {
    $status = zp_inbox_status_read($type, (string) ($entry['status'] ?? ''));
    $what = 'status: ' . (zp_inbox_statuses()[$status] ?? $status);
  }
  return implode(' · ', array_filter([$when, $who, $what], 'strlen'));
}

function zp_inbox_render_page(): void {
  if (!current_user_can('manage_options')) { return; }
  $statuses = zp_inbox_statuses();
  $all = zp_inbox_items();
  $f = zp_inbox_filters($_GET);

  $by_type = ['' => 0, 'form' => 0, 'studio' => 0];
  $by_status = array_fill_keys(array_keys($statuses), 0);
  $tab_status = array_fill_keys(array_keys($statuses), 0);
  $old_new = 0;
  $limit = current_time('timestamp') - 30 * DAY_IN_SECONDS;
  foreach ($all as $it) {
    $by_type['']++;
    $by_type[$it['type']]++;
    $by_status[$it['status']]++;
    if ($f['typ'] === '' || $it['type'] === $f['typ']) { $tab_status[$it['status']]++; }
    if ($it['status'] === 'new' && $it['ts'] && $it['ts'] < $limit) { $old_new++; }
  }

  $found = zp_inbox_filter($all, $f);
  $per = 25;
  $pages = max(1, (int) ceil(count($found) / $per));
  $paged = min($pages, max(1, (int) ($_GET['paged'] ?? 1)));
  $items = array_slice($found, ($paged - 1) * $per, $per);
  $one = $f['zp_id'] !== '';

  $page_url = admin_url('admin.php?page=zp-suite-zapytania');
  $keep = array_filter(['typ' => $f['typ'], 'status' => $f['status'], 'q' => $f['q']], 'strlen');
  $back = http_build_query($keep + ($paged > 1 ? ['paged' => $paged] : []) + ($one ? ['zp_id' => $f['zp_id']] : []));
  $filtered = $keep || $one;
  $csv_url = wp_nonce_url(add_query_arg(array_map('rawurlencode', ['action' => 'zp_inbox_csv'] + $keep), admin_url('admin-post.php')), 'zp_inbox_csv');
  ?>
  <div class="wrap zpx zpxInbox">
    <header class="zpxHead">
      <span class="zpxKicker">ZP Suite</span>
      <h1>Zapytania</h1>
      <p>Wszystko, co klienci wysłali przez formularze na stronie i przez Studio wyceny. Zadzwoń albo odpisz jednym kliknięciem, potem ustaw status i dopisz notatkę.</p>
      <div class="zpxStats">
        <a class="zpxStat<?php echo $by_status['new'] ? ' is-hot' : ''; ?>" href="<?php echo esc_url(add_query_arg('status', 'new', $page_url)); ?>"><strong><?php echo (int) $by_status['new']; ?></strong><span>nowe</span></a>
        <a class="zpxStat" href="<?php echo esc_url(add_query_arg('status', 'contact', $page_url)); ?>"><strong><?php echo (int) $by_status['contact']; ?></strong><span>w kontakcie</span></a>
        <a class="zpxStat" href="<?php echo esc_url(add_query_arg('status', 'quote', $page_url)); ?>"><strong><?php echo (int) $by_status['quote']; ?></strong><span>wycena wysłana</span></a>
        <a class="zpxStat" href="<?php echo esc_url($page_url); ?>"><strong><?php echo (int) $by_type['']; ?></strong><span>wszystkie</span></a>
      </div>
    </header>
    <hr class="wp-header-end">
    <?php zp_panel_notice(); ?>

    <nav class="zpxTabs" aria-label="Rodzaj zapytań">
      <?php foreach (['' => 'Wszystkie', 'form' => 'Formularze', 'studio' => 'Studio wyceny'] as $t => $label) : ?>
        <a class="<?php echo $f['typ'] === $t && !$one ? 'is-active' : ''; ?>" href="<?php echo esc_url(add_query_arg(array_map('rawurlencode', array_filter(['typ' => $t, 'status' => $f['status'], 'q' => $f['q']], 'strlen')), $page_url)); ?>"><?php echo esc_html($label); ?> <em><?php echo (int) $by_type[$t]; ?></em></a>
      <?php endforeach; ?>
    </nav>

    <form class="zpxTools" method="get" action="<?php echo esc_url(admin_url('admin.php')); ?>">
      <input type="hidden" name="page" value="zp-suite-zapytania">
      <?php if ($f['typ'] !== '') : ?><input type="hidden" name="typ" value="<?php echo esc_attr($f['typ']); ?>"><?php endif; ?>
      <label>Status
        <select name="status">
          <option value="">Wszystkie</option>
          <?php foreach ($statuses as $k => $label) : ?><option value="<?php echo esc_attr($k); ?>" <?php selected($f['status'], $k); ?>><?php echo esc_html($label . ' (' . $tab_status[$k] . ')'); ?></option><?php endforeach; ?>
        </select>
      </label>
      <label class="zpxGrow">Szukaj
        <input type="search" name="q" value="<?php echo esc_attr($f['q']); ?>" placeholder="imię, telefon, e-mail, firma, usługa…">
      </label>
      <button class="button button-primary">Pokaż</button>
      <?php if ($filtered) : ?><a class="button" href="<?php echo esc_url($page_url); ?>">Wyczyść filtry</a><?php endif; ?>
      <a class="button" href="<?php echo esc_url($csv_url); ?>">Pobierz CSV (Excel)</a>
    </form>

    <?php if ($old_new && !$one) : ?>
      <form class="zpxHint" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <input type="hidden" name="action" value="zp_inbox_bulk">
        <input type="hidden" name="op" value="close-old">
        <input type="hidden" name="back" value="<?php echo esc_attr($back); ?>">
        <?php wp_nonce_field('zp_inbox_bulk'); ?>
        <span>Zapytania starsze niż 30 dni, które wciąż mają status „Nowe”: <b><?php echo (int) $old_new; ?></b>. Jeśli są już załatwione, zamknij je jednym kliknięciem, a licznik w menu pokaże tylko świeże.</span>
        <button class="button">Oznacz je jako zamknięte</button>
      </form>
    <?php endif; ?>

    <?php if (!$all) : ?>
      <div class="zpxEmpty zpxList">Jeszcze nikt nie napisał. Zapytania z formularzy na stronie i ze Studio wyceny pojawią się tutaj, a powiadomienie przyjdzie też na kontakt@zaprojektowani.com.</div>
    <?php elseif (!$items) : ?>
      <div class="zpxEmpty zpxList">Nic nie pasuje do filtrów. <a href="<?php echo esc_url($page_url); ?>">Pokaż wszystkie zapytania</a></div>
    <?php else : ?>
      <?php if (!$one) : ?>
        <form id="zpxBulk" class="zpxBulk" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
          <input type="hidden" name="action" value="zp_inbox_bulk">
          <input type="hidden" name="back" value="<?php echo esc_attr($back); ?>">
          <?php wp_nonce_field('zp_inbox_bulk'); ?>
          <label class="zpxAll"><input type="checkbox" id="zpxAll"> Zaznacz wszystkie na tej stronie</label>
          <select name="op" aria-label="Co zrobić z zaznaczonymi">
            <option value="">Zaznaczone…</option>
            <?php foreach ($statuses as $k => $label) : ?><option value="<?php echo esc_attr($k); ?>">Ustaw status: <?php echo esc_html($label); ?></option><?php endforeach; ?>
            <option value="delete">Usuń</option>
          </select>
          <button class="button">Zastosuj</button>
          <span class="zpxCount"><?php echo esc_html(count($found) . ' ' . ($filtered ? 'pasujących' : 'zapytań')); ?></span>
        </form>
      <?php else : ?>
        <p class="zpxBackLink"><a href="<?php echo esc_url($page_url); ?>">← Wszystkie zapytania</a></p>
      <?php endif; ?>

      <div class="zpxList">
        <?php foreach ($items as $it) :
          $tel = preg_replace('/[^0-9+]/', '', $it['phone']);
          $subject = 'Re: ' . ($it['service'] !== '' ? $it['service'] : 'zapytanie ze strony Zaprojektowani');
          $log = array_reverse(array_slice($it['log'], -8));
          $meta = implode(' · ', array_filter([$it['kind'], $it['name'] !== $it['company'] ? $it['company'] : '', $it['source']], 'strlen'));
        ?>
          <article class="zpxItem<?php echo $it['status'] === 'new' ? ' is-new' : ''; ?>" id="<?php echo esc_attr($it['anchor']); ?>">
            <div class="zpxTop">
              <?php if (!$one) : ?><input type="checkbox" form="zpxBulk" name="ids[]" value="<?php echo esc_attr($it['key']); ?>" aria-label="<?php echo esc_attr('Zaznacz: ' . $it['name']); ?>"><?php else : ?><span></span><?php endif; ?>
              <div class="zpxWho"><b><?php echo esc_html($it['name']); ?></b><?php if ($meta !== '') : ?><small><?php echo esc_html($meta); ?></small><?php endif; ?></div>
              <div class="zpxMeta"><?php echo zp_inbox_status_pill($it['status']); ?><time title="<?php echo esc_attr($it['ts'] ? gmdate('j.m.Y, H:i', $it['ts']) : ''); ?>"><?php echo esc_html(zp_inbox_date($it['ts'])); ?></time></div>
            </div>
            <?php if ($it['service'] !== '' || $it['message'] !== '') : ?>
              <p class="zpxWhat"><?php if ($it['service'] !== '') : ?><strong><?php echo esc_html($it['service']); ?></strong><?php endif; ?><?php echo ($it['service'] !== '' && $it['message'] !== '') ? ': ' : ''; ?><?php echo esc_html(zp_inbox_excerpt($it['message'], 180)); ?></p>
            <?php endif; ?>
            <div class="zpxQuick">
              <?php if ($tel !== '') : ?><a class="button button-primary" href="<?php echo esc_url('tel:' . $tel); ?>">Zadzwoń: <?php echo esc_html($it['phone']); ?></a><?php endif; ?>
              <?php if ($it['email'] !== '') : ?><a class="button<?php echo $tel === '' ? ' button-primary' : ''; ?>" href="<?php echo esc_url('mailto:' . $it['email'] . '?subject=' . rawurlencode($subject)); ?>">Odpisz: <?php echo esc_html($it['email']); ?></a><?php endif; ?>
              <?php if ($it['website'] !== '' && wp_http_validate_url($it['website'])) : ?><a class="button" href="<?php echo esc_url($it['website']); ?>" target="_blank" rel="noopener noreferrer">Strona klienta</a><?php endif; ?>
            </div>
            <details class="zpxDetails"<?php echo $one ? ' open' : ''; ?>>
              <summary>Szczegóły, status i notatka<?php echo $it['note'] !== '' ? ' · jest notatka' : ''; ?></summary>
              <div class="zpxBody">
                <div>
                  <?php if ($it['message'] !== '') : ?><div class="zpxMsg"><?php echo esc_html($it['message']); ?></div><?php endif; ?>
                  <?php if ($it['details']) : ?>
                    <dl class="zpxDl">
                      <?php foreach ($it['details'] as $d) : ?>
                        <div<?php echo (function_exists('mb_strlen') ? mb_strlen($d[1]) : strlen($d[1])) > 60 ? ' class="is-wide"' : ''; ?>><dt><?php echo esc_html($d[0]); ?></dt><dd><?php echo esc_html($d[1]); ?></dd></div>
                      <?php endforeach; ?>
                    </dl>
                  <?php endif; ?>
                  <?php
                  $links = [];
                  foreach ($it['files'] as $file) {
                    if (is_array($file) && !empty($file['url'])) { $links[] = '<a href="' . esc_url($file['url']) . '" target="_blank" rel="noopener">' . esc_html('Plik: ' . ($file['name'] ?? basename((string) $file['url']))) . '</a>'; }
                  }
                  if ($it['type'] === 'studio' && function_exists('zp_studio_order_admin_url')) {
                    if ($links) { $links[] = '<a href="' . esc_url(zp_studio_order_admin_url('zp_studio_files_zip', $it['id'])) . '">Wszystkie pliki (ZIP)</a>'; }
                    $links[] = '<a href="' . esc_url(zp_studio_order_admin_url('zp_studio_order_pdf', $it['id'])) . '">Podsumowanie PDF</a>';
                  }
                  if ($links) { echo '<div class="zpxFiles">' . implode('', $links) . '</div>'; }
                  ?>
                  <p class="zpxId">Numer: <?php echo esc_html($it['id']); ?><?php echo $it['ts'] ? esc_html(' · wysłane ' . gmdate('j.m.Y, H:i', $it['ts'])) : ''; ?></p>
                </div>
                <div class="zpxSide">
                  <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                    <input type="hidden" name="action" value="zp_inbox_save">
                    <input type="hidden" name="type" value="<?php echo esc_attr($it['type']); ?>">
                    <input type="hidden" name="id" value="<?php echo esc_attr($it['id']); ?>">
                    <input type="hidden" name="back" value="<?php echo esc_attr($back); ?>">
                    <?php wp_nonce_field('zp_inbox_save'); ?>
                    <label>Status
                      <select name="status"><?php foreach ($statuses as $k => $label) : ?><option value="<?php echo esc_attr($k); ?>" <?php selected($it['status'], $k); ?>><?php echo esc_html($label); ?></option><?php endforeach; ?></select>
                    </label>
                    <label>Notatka (widzi ją tylko zespół)
                      <textarea name="note" rows="4" placeholder="Np. dzwoniłem 8.10, oddzwonić w piątek po wycenę."><?php echo esc_textarea($it['note']); ?></textarea>
                    </label>
                    <button class="button button-primary">Zapisz</button>
                  </form>
                  <?php if ($log) : ?>
                    <ul class="zpxLog"><?php foreach ($log as $entry) : ?><li><?php echo esc_html(zp_inbox_log_line($it['type'], (array) $entry)); ?></li><?php endforeach; ?></ul>
                  <?php endif; ?>
                  <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" onsubmit="return confirm('Usunąć to zapytanie razem z przesłanymi plikami? Tego nie da się cofnąć.');">
                    <input type="hidden" name="action" value="zp_inbox_delete">
                    <input type="hidden" name="type" value="<?php echo esc_attr($it['type']); ?>">
                    <input type="hidden" name="id" value="<?php echo esc_attr($it['id']); ?>">
                    <input type="hidden" name="back" value="<?php echo esc_attr($one ? '' : $back); ?>">
                    <?php wp_nonce_field('zp_inbox_delete'); ?>
                    <button class="button zpxDelete">Usuń zapytanie</button>
                  </form>
                </div>
              </div>
            </details>
          </article>
        <?php endforeach; ?>
      </div>

      <?php if ($pages > 1) : ?>
        <nav class="zpxPager" aria-label="Strony">
          <?php if ($paged > 1) : ?><a class="button" href="<?php echo esc_url(add_query_arg(array_map('rawurlencode', $keep + ['paged' => $paged - 1]), $page_url)); ?>">← Nowsze</a><?php endif; ?>
          <span>Strona <?php echo (int) $paged; ?> z <?php echo (int) $pages; ?></span>
          <?php if ($paged < $pages) : ?><a class="button" href="<?php echo esc_url(add_query_arg(array_map('rawurlencode', $keep + ['paged' => $paged + 1]), $page_url)); ?>">Starsze →</a><?php endif; ?>
        </nav>
      <?php endif; ?>
    <?php endif; ?>
  </div>
  <script>
  (function () {
    var all = document.getElementById('zpxAll');
    if (all) {
      all.addEventListener('change', function () {
        document.querySelectorAll('input[form="zpxBulk"][name="ids[]"]').forEach(function (c) { c.checked = all.checked; });
      });
    }
    var bulk = document.getElementById('zpxBulk');
    if (bulk) {
      bulk.addEventListener('submit', function (e) {
        var op = bulk.querySelector('select[name="op"]').value;
        if (!op) { e.preventDefault(); return; }
        if (op === 'delete' && !confirm('Usunąć zaznaczone zapytania razem z przesłanymi plikami? Tego nie da się cofnąć.')) { e.preventDefault(); }
      });
    }
    // A link to one inquiry (#zp-form-…) opens its details.
    if (location.hash) {
      var el = document.getElementById(location.hash.slice(1));
      if (el && el.classList.contains('zpxItem')) { var d = el.querySelector('details'); if (d) { d.open = true; } }
    }
  })();
  </script>
  <?php
}
