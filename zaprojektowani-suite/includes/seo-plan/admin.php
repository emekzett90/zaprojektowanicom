<?php
if (!defined('ABSPATH')) { exit; }

/** Narzędzia → Plan SEO 2.3.0: migration log, re-run, undo and pause. */

add_action('admin_menu', function () {
  add_management_page('Plan SEO ' . ZP_SEO_PLAN_VERSION, 'Plan SEO ' . ZP_SEO_PLAN_VERSION, 'manage_options', 'zp-seo-plan', 'zp_seo_plan_admin_page');
});

add_action('admin_post_zp_seo_plan', function () {
  if (!current_user_can('manage_options')) { wp_die('Brak uprawnień.'); }
  check_admin_referer('zp_seo_plan');
  $do = sanitize_key($_POST['do'] ?? '');
  if ($do === 'rerun') {
    delete_option('zp_seo_plan_migrated');
    zp_seo_plan_maybe_migrate();
  } elseif ($do === 'restore') {
    $log = zp_seo_plan_restore();
    update_option('zp_seo_plan_paused', '1', false);
    $log[] = 'Plan wstrzymany: kod wtyczki działa jak w wersji 2.2.828.';
    zp_seo_plan_log($log);
  } elseif ($do === 'pause') {
    update_option('zp_seo_plan_paused', '1', false);
    zp_seo_plan_purge_caches();
    zp_seo_plan_log(['Plan wstrzymany bez cofania zmian w bazie.']);
  } elseif ($do === 'resume') {
    delete_option('zp_seo_plan_paused');
    zp_seo_plan_log(['Plan wznowiony.']);
    zp_seo_plan_maybe_migrate();
  }
  wp_safe_redirect(admin_url('tools.php?page=zp-seo-plan&done=' . $do));
  exit;
});

function zp_seo_plan_admin_page(): void {
  $active = zp_seo_plan_active();
  $migrated = get_option('zp_seo_plan_migrated');
  $at = (int) get_option('zp_seo_plan_migrated_at', 0);
  $log = array_reverse((array) get_option(ZP_SEO_PLAN_LOG, []));
  $button = static function (string $do, string $label, string $class = 'button', string $confirm = '') {
    echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '" style="display:inline-block;margin:0 8px 8px 0"' . ($confirm ? ' onsubmit="return confirm(' . esc_attr(wp_json_encode($confirm)) . ')"' : '') . '>';
    wp_nonce_field('zp_seo_plan');
    echo '<input type="hidden" name="action" value="zp_seo_plan"><input type="hidden" name="do" value="' . esc_attr($do) . '">';
    echo '<button class="' . esc_attr($class) . '">' . esc_html($label) . '</button></form>';
  };
  echo '<div class="wrap"><h1>Plan SEO ' . esc_html(ZP_SEO_PLAN_VERSION) . '</h1>';
  echo '<p>Strony ogólnopolskie (tworzenie stron, sklepów, projektowanie logo, identyfikacja wizualna), krótsze strony Katowice, przekierowania połączonych wpisów, tytuły i opisy w Rank Math, jedna firma w danych strukturalnych.</p>';
  echo '<table class="widefat striped" style="max-width:760px"><tbody>';
  echo '<tr><th>Stan</th><td>' . ($active ? '<strong style="color:#008a20">Włączony</strong>' : '<strong style="color:#b32d2e">Wstrzymany</strong>') . '</td></tr>';
  echo '<tr><th>Zmiany w bazie</th><td>' . ($migrated === ZP_SEO_PLAN_VERSION ? 'wprowadzone ' . esc_html($at ? wp_date('Y-m-d H:i', $at) : '') : 'jeszcze nie') . '</td></tr>';
  echo '<tr><th>Tytuły i opisy</th><td>Edytujesz je teraz w Rank Math przy każdej stronie i wpisie. Kod ich nie nadpisuje.</td></tr>';
  echo '</tbody></table><p style="margin-top:18px">';
  if ($active) {
    $button('rerun', 'Uruchom migrację ponownie');
    $button('pause', 'Wstrzymaj plan (bez cofania bazy)', 'button', 'Wstrzymać plan? Strony i wpisy zostaną, ale kod wróci do zachowania z wersji 2.2.828.');
    $button('restore', 'Cofnij zmiany w bazie i wstrzymaj', 'button button-link-delete', 'Cofnąć zmiany? Połączone wpisy wrócą, nowe strony usług trafią do szkiców, a pola Rank Math wrócą do poprzednich wartości (poza zmienionymi ręcznie).');
  } else {
    $button('resume', 'Wznów plan', 'button button-primary');
  }
  echo '</p><h2>Dziennik</h2>';
  echo '<pre style="background:#fff;border:1px solid #dcdcde;padding:12px;max-width:1100px;max-height:520px;overflow:auto;white-space:pre-wrap">' . esc_html($log ? implode("\n", $log) : 'Brak wpisów.') . '</pre></div>';
}
