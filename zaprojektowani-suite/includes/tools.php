<?php
if (!defined('ABSPATH')) { exit; }

add_action('admin_menu', function(){
  add_submenu_page('zp-suite','Narzędzia / backup','Narzędzia / backup','manage_options','zp-suite-tools','zp_suite_render_tools_page');
});

add_action('admin_init', function(){
  if (!current_user_can('manage_options')) { return; }
  if (empty($_GET['page']) || $_GET['page'] !== 'zp-suite-tools') { return; }
  if (!empty($_GET['zp_export']) && check_admin_referer('zp_suite_export')) {
    $payload = zp_suite_export_payload();
    header('Content-Type: application/json; charset=utf-8');
    header('Content-Disposition: attachment; filename="zaprojektowani-suite-backup-'.gmdate('Ymd-His').'.json"');
    echo wp_json_encode($payload, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    exit;
  }
});

function zp_suite_export_payload(){
  return [
    'exported_at' => current_time('mysql'),
    'version' => ZP_SUITE_VERSION,
    'options' => get_option('zp_suite_options', []),
    'cms' => get_option('zp_suite_cms', []),
    'leads' => get_option('zp_suite_leads', []),
  ];
}

function zp_suite_render_tools_page(){
  if (!current_user_can('manage_options')) { return; }
  $notice = '';
  if (!empty($_POST['zp_suite_import']) && check_admin_referer('zp_suite_import')) {
    $json = isset($_POST['zp_suite_import_json']) ? wp_unslash($_POST['zp_suite_import_json']) : '';
    $data = json_decode($json, true);
    if (!is_array($data)) {
      $notice = '<div class="zpNotice" style="background:#fff7ed;border-color:#fed7aa;color:#9a3412">Nie udało się odczytać JSON. Sprawdź plik lub zawartość.</div>';
    } else {
      update_option('zp_suite_last_backup_before_import', zp_suite_export_payload(), false);
      if (isset($data['options']) && is_array($data['options'])) { update_option('zp_suite_options', $data['options'], false); }
      if (isset($data['cms']) && is_array($data['cms'])) { update_option('zp_suite_cms', $data['cms'], false); }
      if (isset($data['leads']) && is_array($data['leads'])) { update_option('zp_suite_leads', $data['leads'], false); }
      $notice = '<div class="zpNotice">Import zakończony. Wyczyść cache i sprawdź front.</div>';
    }
  }
  if (!empty($_POST['zp_suite_restore_legacy']) && check_admin_referer('zp_suite_restore_legacy')) {
    $mode = sanitize_key($_POST['zp_suite_legacy_mode'] ?? 'all');
    $defaults = zp_suite_cms_defaults();
    $cms = zp_suite_cms();
    update_option('zp_suite_last_backup_before_legacy_restore', zp_suite_export_payload(), false);
    if ($mode === 'all' || $mode === 'portfolio') { $cms['portfolio'] = $defaults['portfolio']; }
    if ($mode === 'all' || $mode === 'reviews') { $cms['reviews'] = $defaults['reviews']; }
    if ($mode === 'all' || $mode === 'logos') { $cms['logos'] = $defaults['logos']; }
    if ($mode === 'all' || $mode === 'faq') { $cms['faq'] = $defaults['faq']; }
    if ($mode === 'all' || $mode === 'industries') { $cms['industries'] = $defaults['industries']; }
    update_option('zp_suite_cms', $cms, false);
    update_option('zp_suite_content_version', '1.3.0-legacy-restore-'.$mode, false);
    $notice = '<div class="zpNotice">Przywrócono content legacy: '.esc_html($mode).'.</div>';
  }
  $export_url = wp_nonce_url(admin_url('admin.php?page=zp-suite-tools&zp_export=1'), 'zp_suite_export');
  ?>
  <div class="zpSuiteAdmin">
    <div class="zpSuiteHero"><span class="zpSuiteBadge">Narzędzia / bezpieczeństwo</span><h1>Backup, import i przywracanie contentu.</h1><p>Przed większymi zmianami pobierz JSON. Możesz też przywrócić portfolio, opinie i logotypy z contentu legacy bez ręcznego przepisywania.</p></div>
    <?php echo $notice; ?>
    <div class="zpGrid" style="margin-top:18px">
      <div class="zpCard"><h2>Eksport ustawień CMS</h2><p>Pobiera opcje, portfolio, opinie, FAQ, logotypy, branże oraz leady.</p><a class="button button-primary" href="<?php echo esc_url($export_url); ?>">Pobierz backup JSON</a></div>
      <div class="zpCard"><h2>Import JSON</h2><form method="post"><?php wp_nonce_field('zp_suite_import'); ?><textarea name="zp_suite_import_json" style="width:100%;min-height:220px;border-radius:14px" placeholder="Wklej tutaj zawartość pliku JSON"></textarea><p><button class="button button-primary" name="zp_suite_import" value="1">Importuj ustawienia</button></p></form></div>
      <div class="zpCard"><h2>Przywróć content legacy</h2><p>Przywracanie robi automatyczny backup aktualnych danych przed zmianą.</p><form method="post"><?php wp_nonce_field('zp_suite_restore_legacy'); ?><select name="zp_suite_legacy_mode"><option value="all">Wszystko</option><option value="portfolio">Tylko portfolio</option><option value="reviews">Tylko opinie</option><option value="logos">Tylko logotypy</option><option value="faq">Tylko FAQ</option><option value="industries">Tylko branże</option></select> <button class="button" name="zp_suite_restore_legacy" value="1" onclick="return confirm('Przywrócić wybrany content legacy?')">Przywróć</button></form></div>
      <div class="zpCard"><h2>Ostatnie automatyczne backupy</h2><p><strong>Przed importem:</strong> <?php echo get_option('zp_suite_last_backup_before_import') ? 'dostępny w bazie opcji' : 'brak'; ?></p><p><strong>Przed restore legacy:</strong> <?php echo get_option('zp_suite_last_backup_before_legacy_restore') ? 'dostępny w bazie opcji' : 'brak'; ?></p></div>
    </div>
  </div>
  <?php
}
