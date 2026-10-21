<?php
if (!defined('ABSPATH')) { exit; }

/**
 * Suite 3.2.20 — versioned legal documents, original-content backups and shared styling.
 * - działa na aktywacji i w admin_init,
 * - nie uruchamia się na froncie,
 * - aktualizuje istniejące strony po slugu zamiast tworzyć duplikaty,
 * - zapisuje też poprawny Elementor HTML widget, ale zostawia klasyczną treść jako fallback.
 */

function zp_suite_legal_company_data(){
  return [
    'company' => 'AP Studio',
    'nip' => '9930682613',
    'regon' => '389224502',
    'registered_address' => 'ul. Krakowska 30, 33-100 Tarnów',
    'correspondence_address' => 'ul. Modelarska 18/2, 40-142 Katowice',
    'address' => 'ul. Modelarska 18/2, 40-142 Katowice',
    'phone' => '+48 501 054 253',
    'phone_raw' => '48501054253',
    'email' => 'kontakt@zaprojektowani.com',
    'site' => 'zaprojektowani.com',
  ];
}

require_once __DIR__.'/legal-content-20261016.php';

function zp_suite_legal_document_version(){ return '2026-10-16'; }

// Content deployment revision is independent of the human-readable document date.
function zp_suite_legal_content_revision(){ return '20261016-3.2.23'; }

function zp_suite_legal_enqueue_assets(){
  if (!is_page(array_keys(zp_suite_legal_document_labels()))) { return; }
  $relative = 'assets/js/legal-pages-20261016.js';
  wp_enqueue_script('zp-legal-documents', ZP_SUITE_URL.$relative, [], (string)filemtime(ZP_SUITE_PATH.$relative), true);
}
add_action('wp_enqueue_scripts', 'zp_suite_legal_enqueue_assets');

function zp_suite_legal_stylesheet_url(){
  $relative = 'assets/css/legal-pages-20261016.css';
  $url = ZP_SUITE_URL . $relative;
  $version = is_file(ZP_SUITE_PATH . $relative) ? (string) filemtime(ZP_SUITE_PATH . $relative) : zp_suite_legal_document_version();
  return add_query_arg('ver', $version, $url);
}

function zp_suite_legal_css(){
  return '<link id="zp-legal-pages-style" rel="stylesheet" href="'.esc_url(zp_suite_legal_stylesheet_url()).'">';
}

function zp_suite_legal_document_labels(){
  return ['regulamin'=>'Regulamin', 'polityka-prywatnosci'=>'Polityka prywatności', 'rodo'=>'Informacja RODO', 'cookies'=>'Polityka cookies'];
}

function zp_suite_legal_shell($title, $lead, $sections, $slug = ''){
  $d = zp_suite_legal_company_data();
  $toc = ''; $body = ''; $number = 0;
  foreach ($sections as $sec) {
    $number++;
    $id = 'legal-'.str_pad((string)$number, 2, '0', STR_PAD_LEFT).'-'.sanitize_title($sec['title']);
    $n = str_pad((string)$number, 2, '0', STR_PAD_LEFT);
    $toc .= '<li><a href="#'.esc_attr($id).'"><span>'.esc_html($n).'</span>'.esc_html($sec['title']).'</a></li>';
    $body .= '<section class="zpLegal__section" id="'.esc_attr($id).'" aria-labelledby="'.esc_attr($id).'-title"><span class="zpLegal__sectionNumber" aria-hidden="true">'.esc_html($n).'</span><div class="zpLegal__sectionBody"><h2 id="'.esc_attr($id).'-title">'.esc_html($sec['title']).'</h2>'.$sec['html'].'</div></section>';
  }
  $nav = '';
  foreach (zp_suite_legal_document_labels() as $path => $label) {
    $current = $path === $slug ? ' aria-current="page"' : '';
    $nav .= '<a href="'.esc_url(home_url('/'.$path.'/')).'"'.$current.'>'.esc_html($label).'<span aria-hidden="true">↗</span></a>';
  }
  $heading = esc_html($title);
  if ($slug === 'regulamin') { $heading = 'Regulamin <strong>współpracy.</strong>'; }
  elseif ($slug === 'polityka-prywatnosci') { $heading = 'Polityka <strong>prywatności.</strong>'; }
  elseif ($slug === 'rodo') { $heading = 'Ochrona <strong>danych osobowych.</strong>'; }
  elseif ($slug === 'cookies') { $heading = 'Twoje ustawienia.<br><strong>Polityka cookies.</strong>'; }
  $intro = $slug === 'regulamin' ? 'Zapytanie jest bezpłatne. Zakres i warunki realizacji projektu ustalamy osobno.' : 'Przejrzyste zasady. Łatwy dostęp do informacji i kontaktu.';
  $meta = '<aside class="zpLegal__meta" aria-label="Dane usługodawcy i administratora"><span class="zpLegal__metaLabel">Zaprojektowani · AP Studio</span><h2>Porozmawiajmy<br>o zasadach.</h2><dl><div><dt>Usługodawca i administrator</dt><dd>'.esc_html($d['company']).'</dd></div><div><dt>NIP</dt><dd>'.esc_html($d['nip']).'</dd></div><div><dt>REGON</dt><dd>'.esc_html($d['regon']).'</dd></div><div><dt>Adres firmy</dt><dd>'.esc_html($d['registered_address']).'</dd></div><div><dt>Adres do korespondencji</dt><dd>'.esc_html($d['correspondence_address']).'</dd></div></dl><a class="zpLegal__contact" href="mailto:'.esc_attr($d['email']).'">'.esc_html($d['email']).'<span aria-hidden="true">↗</span></a><a class="zpLegal__phone" href="tel:+'.esc_attr($d['phone_raw']).'">'.esc_html($d['phone']).'</a></aside>';
  return zp_suite_legal_css().'<article class="zpLegal" data-zp-legal-version="'.esc_attr(zp_suite_legal_document_version()).'" data-zp-legal-document="'.esc_attr($slug).'"><div class="zpLegal__wrap"><nav class="zpLegal__breadcrumbs" aria-label="Okruszki"><a href="'.esc_url(home_url('/')).'">Strona główna</a><span aria-hidden="true">/</span><span>'.esc_html(zp_suite_legal_document_labels()[$slug] ?? $title).'</span></nav><header class="zpLegal__hero"><div class="zpLegal__heroCopy"><p class="zpLegal__kicker">Zasady · prywatność · współpraca</p><h1>'.$heading.'</h1><p class="zpLegal__lead">'.esc_html($lead).'</p><div class="zpLegal__heroActions"><a class="zpLegal__btn zpLegal__btn--primary" href="#legal-document-start">Przeczytaj dokument <span aria-hidden="true">↓</span></a><a class="zpLegal__btn" href="mailto:'.esc_attr($d['email']).'">Napisz do nas <span aria-hidden="true">↗</span></a></div><p class="zpLegal__version">Wersja z 16 października 2026 <span aria-hidden="true">·</span> '.esc_html((string)$number).' sekcji</p></div>'.$meta.'</header><nav class="zpLegal__documents" aria-label="Dokumenty Zaprojektowani">'.$nav.'</nav><div class="zpLegal__intro"><span aria-hidden="true">↘</span><p>'.esc_html($intro).'</p></div><div class="zpLegal__layout" id="legal-document-start"><aside class="zpLegal__sidebar"><nav class="zpLegal__toc zpLegal__toc--desktop" aria-label="Spis treści"><p>W tym dokumencie</p><ol>'.$toc.'</ol></nav><nav class="zpLegal__toc zpLegal__toc--mobile" aria-label="Spis treści"><details><summary>Spis treści <span aria-hidden="true">+</span></summary><ol>'.$toc.'</ol></details></nav><div class="zpLegal__help"><span>Masz pytanie?</span><p>Wyjaśnijmy je<br><strong>przed projektem.</strong></p><a href="mailto:'.esc_attr($d['email']).'">Zapytaj o zasady <span aria-hidden="true">↗</span></a></div></aside><div class="zpLegal__content">'.$body.'<footer class="zpLegal__documentFooter"><p>Dokument w wersji z 16 października 2026 r.</p><a href="#legal-document-start">Wróć do początku <span aria-hidden="true">↑</span></a></footer></div></div></div></article>';
}

function zp_suite_legal_pages_definitions(){
  return zp_suite_legal_content_20261016();
}

function zp_suite_legal_make_elementor_data($html){
  return [
    [
      'id' => substr(md5('zp-legal-section'), 0, 7),
      'elType' => 'section',
      'settings' => ['layout' => 'full_width', 'gap' => 'no'],
      'elements' => [[
        'id' => substr(md5('zp-legal-column'), 0, 7),
        'elType' => 'column',
        'settings' => ['_column_size' => 100],
        'elements' => [[
          'id' => substr(md5('zp-legal-html'), 0, 7),
          'elType' => 'widget',
          'widgetType' => 'html',
          'settings' => ['html' => $html],
          'elements' => [],
        ]],
      ]],
    ],
  ];
}

function zp_suite_legal_create_or_update_pages(){
  if (!function_exists('wp_insert_post')) { return; }
  $defs = zp_suite_legal_pages_definitions();
  $completed = [];
  foreach ($defs as $slug => $def) {
    $html = zp_suite_legal_shell($def['title'], $def['lead'], $def['sections'], $slug);
    $page = get_page_by_path($slug, OBJECT, 'page');
    $postarr = [
      'post_title' => $def['title'],
      'post_name' => $slug,
      'post_content' => $html,
      'post_status' => 'publish',
      'post_type' => 'page',
      'comment_status' => 'closed',
      'ping_status' => 'closed',
    ];
    if ($page && !empty($page->ID)) {
      $backup_key = '_zp_legal_before_20261016';
      if (!metadata_exists('post', $page->ID, $backup_key)) {
        $backup = ['title'=>$page->post_title, 'content'=>$page->post_content, 'elementor_data'=>get_post_meta($page->ID, '_elementor_data', true), 'elementor_edit_mode'=>get_post_meta($page->ID, '_elementor_edit_mode', true), 'captured_at'=>gmdate('c')];
        if (!add_post_meta($page->ID, $backup_key, wp_slash($backup), true)) { continue; }
      }
      if (function_exists('wp_save_post_revision')) { wp_save_post_revision($page->ID); }
      $postarr['ID'] = (int) $page->ID;
      $post_id = wp_update_post(wp_slash($postarr), true);
    } else {
      $post_id = wp_insert_post(wp_slash($postarr), true);
    }
    if (is_wp_error($post_id) || !$post_id) { continue; }
    update_post_meta($post_id, '_elementor_edit_mode', 'builder');
    update_post_meta($post_id, '_elementor_template_type', 'wp-page');
    update_post_meta($post_id, '_elementor_version', defined('ELEMENTOR_VERSION') ? ELEMENTOR_VERSION : '3.0.0');
    $elementor_json = wp_json_encode(zp_suite_legal_make_elementor_data($html), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    update_post_meta($post_id, '_elementor_data', wp_slash($elementor_json));
    // update_post_meta(false) also means "unchanged"; verify the effective builder values.
    if ((string)get_post_meta($post_id, '_elementor_data', true) !== $elementor_json
      || get_post_meta($post_id, '_elementor_edit_mode', true) !== 'builder'
      || get_post_meta($post_id, '_elementor_template_type', true) !== 'wp-page'
      || get_post($post_id)->post_content !== $html) { continue; }
    foreach (['_elementor_element_cache', '_elementor_css', '_elementor_page_assets'] as $cache_key) {
      delete_post_meta($post_id, $cache_key);
    }
    update_post_meta($post_id, '_zp_legal_version', zp_suite_legal_document_version());
    if (get_post_meta($post_id, '_zp_legal_version', true) !== zp_suite_legal_document_version()) { continue; }
    clean_post_cache($post_id);
    $completed[] = $slug;
  }
  if (count($completed) === count($defs)) {
    update_option('zp_suite_legal_pages_version', zp_suite_legal_document_version(), false);
    // The stylesheet link is stored in both post_content and Elementor, independently of the legal text date.
    update_option('zp_suite_legal_stylesheet_url', zp_suite_legal_stylesheet_url(), false);
    update_option('zp_suite_legal_content_revision', zp_suite_legal_content_revision(), false);
    if (class_exists('ZP_HTML_Cache_20261009')) { ZP_HTML_Cache_20261009::queue_purge(); }
  }
}

function zp_suite_legal_admin_maybe_create_pages(){
  if (!is_admin() || !current_user_can('manage_options') || wp_doing_ajax() || (defined('WP_CLI') && WP_CLI)) { return; }
  if (get_option('zp_suite_legal_pages_version') !== zp_suite_legal_document_version()
    || get_option('zp_suite_legal_stylesheet_url', '') !== zp_suite_legal_stylesheet_url()
    || get_option('zp_suite_legal_content_revision', '') !== zp_suite_legal_content_revision()) {
    zp_suite_legal_create_or_update_pages();
  }
}
add_action('admin_init', 'zp_suite_legal_admin_maybe_create_pages', 20);
