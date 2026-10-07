<?php
if (!defined('ABSPATH')) { exit; }

/** ZP Suite → Publikacja wpisów: switch, mode, e-mail, buttons, the feed's posts and the log. */

add_action('admin_menu', function () {
  add_submenu_page('zp-suite', 'Publikacja wpisów', 'Publikacja wpisów', 'manage_options', 'zp-suite-publikacja', 'zp_feed_admin_page');
}, 40);

add_action('admin_post_zp_feed', function () {
  if (!current_user_can('manage_options')) { wp_die('Brak uprawnień.'); }
  check_admin_referer('zp_feed');
  $do = sanitize_key($_POST['do'] ?? '');
  if ($do === 'save') {
    $email = sanitize_email(wp_unslash((string) ($_POST['email'] ?? '')));
    zp_feed_update_settings([
      'enabled' => !empty($_POST['enabled']),
      'mode' => ($_POST['mode'] ?? '') === 'drafts' ? 'drafts' : 'schedule',
      'email' => $email,
    ]);
    zp_feed_log(['Zapisano ustawienia: ' . (!empty($_POST['enabled']) ? 'włączona' : 'wyłączona') . ', ' . (($_POST['mode'] ?? '') === 'drafts' ? 'tylko szkice' : 'publikacja według planu') . ', e-mail ' . ($email !== '' ? $email : 'wyłączony') . '.']);
  } elseif ($do === 'check') {
    zp_feed_run('przycisk');
  } elseif ($do === 'hold') {
    zp_feed_log(zp_feed_hold());
  } elseif ($do === 'resume') {
    zp_feed_log(zp_feed_resume());
  } elseif ($do === 'undo') {
    zp_feed_log(zp_feed_undo_all());
  }
  wp_safe_redirect(admin_url('admin.php?page=zp-suite-publikacja&done=' . $do));
  exit;
});

/** Three failed checks in a row: a notice on the dashboard and the ZP Suite pages. */
add_action('admin_notices', function () {
  if (!current_user_can('manage_options') || !zp_feed_settings()['enabled']) { return; }
  $screen = function_exists('get_current_screen') ? get_current_screen() : null;
  if ($screen && $screen->id !== 'dashboard' && strpos((string) $screen->id, 'zp-suite') === false) { return; }
  $status = zp_feed_status();
  if ((int) $status['fails'] < 3) { return; }
  echo '<div class="notice notice-error"><p><strong>Publikacja wpisów:</strong> ' . (int) $status['fails'] . ' nieudane sprawdzenia kanału z rzędu. Ostatni błąd: '
    . esc_html((string) $status['last_error']) . '. <a href="' . esc_url(admin_url('admin.php?page=zp-suite-publikacja')) . '">Szczegóły</a></p></div>';
});

function zp_feed_admin_state_label(array $s): string {
  if (!empty($s['skipped'])) { return 'pominięty: ' . $s['skipped']; }
  if (!empty($s['gone'])) { return 'usunięty w WordPressie'; }
  $id = (int) ($s['id'] ?? 0);
  $status = $id ? (string) get_post_status($id) : '';
  $held = (string) ($s['held'] ?? '');
  switch ($status) {
    case 'future': return 'zaplanowany';
    case 'publish': return 'opublikowany';
    case 'trash': return 'w koszu';
    case 'draft':
      if ($held === 'pause') { return 'szkic (wstrzymany)'; }
      if ($held === 'removed') { return 'szkic (zniknął z kanału)'; }
      if ($held === 'undo') { return 'szkic (cofnięty)'; }
      return ($s['set'] ?? '') === 'draft' ? 'szkic do akceptacji' : 'szkic (przeniesiony ręcznie)';
    default: return $status !== '' ? $status : '—';
  }
}

function zp_feed_admin_page(): void {
  $settings = zp_feed_settings();
  $status = zp_feed_status();
  $state = zp_feed_state();
  $log = array_reverse((array) get_option(ZP_FEED_LOG, []));
  $button = static function (string $do, string $label, string $class = 'button', string $confirm = '') {
    echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '" style="display:inline-block;margin:0 8px 8px 0"' . ($confirm ? ' onsubmit="return confirm(' . esc_attr(wp_json_encode($confirm)) . ')"' : '') . '>';
    wp_nonce_field('zp_feed');
    echo '<input type="hidden" name="action" value="zp_feed"><input type="hidden" name="do" value="' . esc_attr($do) . '">';
    echo '<button class="' . esc_attr($class) . '">' . esc_html($label) . '</button></form>';
  };
  $next = wp_next_scheduled('zp_feed_check');
  echo '<div class="wrap"><h1>Publikacja wpisów</h1>';
  echo '<p style="max-width:760px">Nowe artykuły z planu treści wchodzą na stronę same: wtyczka co 6 godzin sprawdza kanał z gotowymi wpisami i planuje je na ich daty (najwyżej jeden dziennie, o 8:00). '
    . 'Zaplanowane widzisz też w Wpisy → Zaplanowane; każdy możesz edytować, przenieść do szkiców albo usunąć, a wtyczka tego nie cofnie.</p>';
  if ((int) $status['fails'] >= 3) {
    echo '<div class="notice notice-error inline"><p>' . (int) $status['fails'] . ' nieudane sprawdzenia z rzędu. Ostatni błąd: ' . esc_html((string) $status['last_error']) . '</p></div>';
  }
  echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '">';
  wp_nonce_field('zp_feed');
  echo '<input type="hidden" name="action" value="zp_feed"><input type="hidden" name="do" value="save">';
  echo '<table class="form-table" role="presentation" style="max-width:760px"><tbody>';
  echo '<tr><th scope="row">Moduł</th><td><label><input type="checkbox" name="enabled" value="1"' . checked($settings['enabled'], true, false) . '> Włączona</label>'
    . ($settings['paused'] ? '<p class="description" style="color:#b32d2e">Publikacja wstrzymana: nowe wpisy trafiają do szkiców. Wznowisz ją przyciskiem niżej.</p>' : '') . '</td></tr>';
  echo '<tr><th scope="row">Tryb</th><td><label><input type="radio" name="mode" value="schedule"' . checked($settings['mode'], 'schedule', false) . '> publikuj według planu</label><br>'
    . '<label><input type="radio" name="mode" value="drafts"' . checked($settings['mode'], 'drafts', false) . '> tylko szkice do akceptacji</label></td></tr>';
  echo '<tr><th scope="row"><label for="zp-feed-email">E-mail powiadomień</label></th><td><input type="email" class="regular-text" id="zp-feed-email" name="email" value="' . esc_attr($settings['email']) . '">'
    . '<p class="description">Lista zaplanowanych artykułów i e-mail w dniu publikacji z gotowym postem do wizytówki Google. Puste pole: bez e-maili.</p></td></tr>';
  echo '</tbody></table><p><button class="button button-primary">Zapisz ustawienia</button></p></form>';

  echo '<h2>Działania</h2><p>';
  $button('check', 'Sprawdź teraz', 'button button-primary');
  if ($settings['paused']) {
    $button('resume', 'Wznów publikację według planu');
  } else {
    $button('hold', 'Przenieś zaplanowane do szkiców', 'button', 'Przenieść zaplanowane artykuły do szkiców? Nowe z kanału też trafią do szkiców, dopóki nie wznowisz publikacji.');
  }
  $button('undo', 'Cofnij wszystko', 'button button-link-delete', 'Przenieść WSZYSTKIE artykuły z kanału do szkiców, także opublikowane, i wstrzymać moduł?');
  echo '</p><p class="description">Ostatnie sprawdzenie: ' . esc_html($status['last_try'] ? wp_date('Y-m-d H:i', (int) $status['last_try']) : 'jeszcze nie')
    . ($status['last_ok'] ? ' · ostatnie udane: ' . esc_html(wp_date('Y-m-d H:i', (int) $status['last_ok'])) : '')
    . ($next ? ' · następne: ' . esc_html($next > time() ? wp_date('Y-m-d H:i', (int) $next) : 'przy najbliższym wejściu na stronę') : '') . '</p>';

  echo '<h2>Artykuły z kanału</h2>';
  if (!$state) {
    echo '<p>Jeszcze żadnych.</p>';
  } else {
    uasort($state, static function ($a, $b) { return strcmp((string) ($a['date'] ?? $a['planned'] ?? ''), (string) ($b['date'] ?? $b['planned'] ?? '')); });
    echo '<table class="widefat striped" style="max-width:1100px"><thead><tr><th>Tytuł</th><th>Data</th><th>Stan</th></tr></thead><tbody>';
    foreach ($state as $s) {
      $id = (int) ($s['id'] ?? 0);
      $title = esc_html((string) ($s['title'] ?? ''));
      $date = (string) ($s['date'] ?? '');
      if ($id && get_post_status($id) === 'publish') { $date = (string) get_post_field('post_date', $id); }
      $planned = $date === '' && !empty($s['planned']);
      if ($planned) { $date = wp_date('Y-m-d H:i', strtotime((string) $s['planned'])); }
      echo '<tr><td>' . ($id && get_post($id) ? '<a href="' . esc_url(admin_url('post.php?post=' . $id . '&action=edit')) . '">' . $title . '</a>' : $title)
        . '<br><code>' . esc_html((string) ($s['path'] ?? '')) . '</code></td><td>' . esc_html(substr($date, 0, 16) . ($planned ? ' (w planie)' : '')) . '</td><td>' . esc_html(zp_feed_admin_state_label($s)) . '</td></tr>';
    }
    echo '</tbody></table>';
  }
  echo '<h2>Dziennik</h2>';
  echo '<pre style="background:#fff;border:1px solid #dcdcde;padding:12px;max-width:1100px;max-height:520px;overflow:auto;white-space:pre-wrap">' . esc_html($log ? implode("\n", $log) : 'Brak wpisów.') . '</pre></div>';
}
