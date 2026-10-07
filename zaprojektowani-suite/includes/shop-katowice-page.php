<?php
if (!defined('ABSPATH')) { exit; }

/**
 * ZAPROJEKTOWANI SUITE — Sklepy internetowe Katowice CMS v2.2.820
 * Shortcode: [zp_sklepy_internetowe_katowice]
 * Cel: pełna podstrona z sekcji HTML, z CSS/JS ładowanym zewnętrznie.
 */


/* v2.1.67: wymuszamy świeże domyślne sekcje po aktualizacji, żeby stare zapisy z CMS nie trzymały błędnego procesu. */
add_action('init', function(){
  $ver_key = 'zp_suite_shop_katowice_template_version';
  if (get_option($ver_key) !== '2.2.113') {
    // v2.2.113: pełny SEO refresh wszystkich sekcji podstrony Sklepy internetowe Katowice.
    // Poprzednia wersja odświeżała głównie górę/why, więc stare treści z CMS mogły zostać w pozostałych widgetach.
    $sections = [];
    foreach (zp_suite_shop_katowice_sections_map() as $zp_key => $zp_data) {
      $sections[$zp_key] = zp_suite_shop_katowice_default_section($zp_key);
    }
    update_option('zp_suite_shop_katowice_sections', $sections, false);
    update_option($ver_key, '2.2.113', false);
  }
}, 3);


/* v2.2.138: force refresh ONLY hero section so the anti-flash HTML is applied even when older hero was saved in CMS. */
add_action('init', function(){
  $ver_key = 'zp_suite_shop_katowice_hero_antiflash_version';
  if (get_option($ver_key) !== '2.2.138') {
    $sections = get_option('zp_suite_shop_katowice_sections', []);
    if (!is_array($sections)) { $sections = []; }
    $sections['hero'] = zp_suite_shop_katowice_default_section('hero');
    update_option('zp_suite_shop_katowice_sections', $sections, false);
    update_option($ver_key, '2.2.138', false);
  }
}, 4);

/* v2.2.159: dodaje sekcję SEO boost dla /sklepy-internetowe-katowice/ bez resetowania ręcznie edytowanych sekcji. */
add_action('init', function(){
  $ver_key = 'zp_suite_shop_katowice_seo_boost_version';
  if (get_option($ver_key) !== '2.2.159') {
    $sections = get_option('zp_suite_shop_katowice_sections', []);
    if (!is_array($sections)) { $sections = []; }
    $sections['seo_boost'] = zp_suite_shop_katowice_default_section('seo_boost');
    update_option('zp_suite_shop_katowice_sections', $sections, false);
    update_option($ver_key, '2.2.159', false);
  }
}, 5);

/* v2.2.160: wizualne korekty etapu 1 Sklepy Katowice — krótsze headingi, full-bleed CTA i odświeżenie sekcji. */
add_action('init', function(){
  $ver_key = 'zp_suite_shop_katowice_visual_fix_160_version';
  if (get_option($ver_key) !== '2.2.160') {
    $sections = get_option('zp_suite_shop_katowice_sections', []);
    if (!is_array($sections)) { $sections = []; }
    foreach (['portfolio','seo_boost','process','industries','faq'] as $zp_key) {
      $sections[$zp_key] = zp_suite_shop_katowice_default_section($zp_key);
    }
    update_option('zp_suite_shop_katowice_sections', $sections, false);
    update_option($ver_key, '2.2.160', false);
  }
}, 6);



/* v2.2.164: poprawki UX/wizualne: header SEO boost, ciemniejsze karty, mobile scroll procesu, krótszy heading branż. */
add_action('init', function(){
  $ver_key = 'zp_suite_shop_katowice_visual_fix_161_version';
  if (get_option($ver_key) !== '2.2.164') {
    $sections = get_option('zp_suite_shop_katowice_sections', []);
    if (!is_array($sections)) { $sections = []; }
    foreach (['seo_boost','process','industries'] as $zp_key) {
      $sections[$zp_key] = zp_suite_shop_katowice_default_section($zp_key);
    }
    update_option('zp_suite_shop_katowice_sections', $sections, false);
    update_option($ver_key, '2.2.164', false);
  }
}, 7);


/* v2.2.164: finalne poprawki /sklepy-internetowe-katowice — FAQ 15 pytań, szerszy header SEO boost, mockup shape i mobile scroll procesu. */
add_action('init', function(){
  $ver_key = 'zp_suite_shop_katowice_visual_fix_162_version';
  if (get_option($ver_key) !== '2.2.164') {
    $sections = get_option('zp_suite_shop_katowice_sections', []);
    if (!is_array($sections)) { $sections = []; }
    foreach (['seo_boost','process','industries','faq','why'] as $zp_key) {
      $sections[$zp_key] = zp_suite_shop_katowice_default_section($zp_key);
    }
    update_option('zp_suite_shop_katowice_sections', $sections, false);
    update_option($ver_key, '2.2.164', false);
  }
}, 8);


/* v2.2.172: Etap 2 SEO dla sklepów — FAQ/schema + linkowanie wewnętrzne + CTA mobile. */
add_action('init', function(){
  $ver_key = 'zp_suite_shop_katowice_stage2_seo_172_version';
  if (get_option($ver_key) !== '2.2.172') {
    $sections = get_option('zp_suite_shop_katowice_sections', []);
    if (!is_array($sections)) { $sections = []; }
    foreach (['seo_boost','faq'] as $zp_key) {
      $sections[$zp_key] = zp_suite_shop_katowice_default_section($zp_key);
    }
    update_option('zp_suite_shop_katowice_sections', $sections, false);
    update_option($ver_key, '2.2.172', false);
  }
}, 9);



/* v2.2.173: drobne korekty wizualne Etapu 2 — trust pills, CTA i SEO boost spacing. */
add_action('init', function(){
  $ver_key = 'zp_suite_shop_katowice_stage2_visual_173_version';
  if (get_option($ver_key) !== '2.2.173') {
    $sections = get_option('zp_suite_shop_katowice_sections', []);
    if (!is_array($sections)) { $sections = []; }
    foreach (['trust','seo_boost'] as $zp_key) {
      $sections[$zp_key] = zp_suite_shop_katowice_default_section($zp_key);
    }
    update_option('zp_suite_shop_katowice_sections', $sections, false);
    update_option($ver_key, '2.2.173', false);
  }
}, 10);

/* v2.2.709: nowa realizacja Świat Grilli jako pierwsza karta portfolio sklepów.
 * Odświeżamy wyłącznie sekcję portfolio, aby zapisany wcześniej HTML w CMS nie blokował nowej karty. */
add_action('init', function(){
  $ver_key = 'zp_suite_shop_katowice_portfolio_swiatgrilli_version';
  if (get_option($ver_key) !== '2.2.709-swiatgrilli') {
    $sections = get_option('zp_suite_shop_katowice_sections', []);
    if (!is_array($sections)) { $sections = []; }
    $sections['portfolio'] = zp_suite_shop_katowice_default_section('portfolio');
    update_option('zp_suite_shop_katowice_sections', $sections, false);
    update_option($ver_key, '2.2.709-swiatgrilli', false);
  }
}, 11);


/* v2.2.709: spójny hero /sklepy-internetowe-katowice/ z pozostałymi podstronami usługowymi.
 * Odświeżamy wyłącznie hero, aby zachować wszystkie pozostałe sekcje i ustawienia CMS. */
add_action('init', function(){
  $ver_key = 'zp_suite_shop_katowice_hero_consistency_709_version';
  if (get_option($ver_key) !== '2.2.709-consistent-hero-v3') {
    $sections = get_option('zp_suite_shop_katowice_sections', []);
    if (!is_array($sections)) { $sections = []; }
    $sections['hero'] = zp_suite_shop_katowice_default_section('hero');
    update_option('zp_suite_shop_katowice_sections', $sections, false);
    update_option($ver_key, '2.2.709-consistent-hero-v3', false);
  }
}, 12);


/* v2.7.8: odśwież hero po dodaniu badge Meta Verified, żeby zapisany HTML z CMS nie blokował nowej wersji. */
add_action('init', function(){
  $ver_key = 'zp_suite_shop_katowice_meta_verified_278_version';
  if (get_option($ver_key) !== '2.7.8') {
    $sections = get_option('zp_suite_shop_katowice_sections', []);
    if (!is_array($sections)) { $sections = []; }
    $sections['hero'] = zp_suite_shop_katowice_default_section('hero');
    update_option('zp_suite_shop_katowice_sections', $sections, false);
    update_option($ver_key, '2.7.8', false);
  }
}, 13);

/* v2.8.0: zapisana kopia sekcji „Trust / opinie” trzymała stare liczby (54 opinie w Trustindex)
 * i stary adres Facebooka. Poprawiamy tylko te fragmenty, pozostałe edycje z CMS zostają. */
add_action('init', function(){
  $ver_key = 'zp_suite_shop_katowice_trust_280_version';
  if (get_option($ver_key) === '2.8.0') { return; }
  $sections = get_option('zp_suite_shop_katowice_sections', []);
  if (is_array($sections) && !empty($sections['trust'])) {
    $trust = preg_replace('~<b>\d+ opini(?:e|i) klientów</b>(<em>średnia ocena w Trustindex</em>)~u', '<b>120 opinii klientów</b>$1', (string) $sections['trust']);
    $sections['trust'] = strtr((string) $trust, [
      'href="https://www.facebook.com/zaprojektowani"' => 'href="https://www.facebook.com/zaprojektowanicom"',
      '<b>Rekomendacje klientów</b><em>kontakt, wdrożenia i projekty marek</em>' => '<b>60 opinii klientów</b><em>rekomendacje i kontakt na Facebooku</em>',
    ]);
    update_option('zp_suite_shop_katowice_sections', $sections, false);
  }
  update_option($ver_key, '2.8.0', false);
}, 14);

/* v2.2.819: pakiety sklepów premium — rozbudowany zakres, zdjęcia zespołu,
 * pełny zakres hover/tap i przeniesienie sekcji nad portfolio. */
add_action('init', function(){
  $ver_key = 'zp_suite_shop_katowice_packages_premium_819_version';
  if (get_option($ver_key) !== '2.2.819') {
    $sections = get_option('zp_suite_shop_katowice_sections', []);
    if (!is_array($sections)) { $sections = []; }
    $sections['packages'] = zp_suite_shop_katowice_default_section('packages');
    update_option('zp_suite_shop_katowice_sections', $sections, false);
    update_option($ver_key, '2.2.819', false);
  }
}, 13);

function zp_suite_shop_katowice_mock_settings(){
  $defaults = [
    // Sterowanie CAŁĄ prawą sekcją wizualną z widgetu „Dlaczego sklep u nas?”
    // X: lewo/prawo, Y: góra/dół, scale: skala całego visuala.
    'x' => 0,
    'y' => -100,
    'scale' => 1,
    'rotate' => -3.2,
    'mobile_scale' => 1,
    'mobile_x' => 0,
    'mobile_y' => 0,
    'chips_x' => 0,
    'chips_y' => 0,
    'chips_scale' => 1,
    'card1_x' => 0,
    'card1_y' => 0,
    // v2.2.178: finalna pozycja Y karty Checkout sterowana z CMS. Większa wartość = niżej, mniejsza = wyżej.
    'checkout_final_y' => -360,
    'card1_scale' => 1,
    'card2_x' => 0,
    'card2_y' => 0,
    'card2_scale' => 1,
    'mobile_chips_x' => 0,
    'mobile_chips_y' => 0,
    'mobile_chips_scale' => 1,
    'mobile_card1_x' => 0,
    'mobile_card1_y' => 0,
    'mobile_card1_scale' => 1,
    'mobile_card2_x' => 0,
    'mobile_card2_y' => 0,
    'mobile_card2_scale' => 1,
  ];
  $saved = get_option('zp_suite_shop_katowice_mock_settings', []);
  if (!is_array($saved)) $saved = [];
  $out = [];
  foreach ($defaults as $k=>$v) {
    $raw = array_key_exists($k, $saved) ? $saved[$k] : $v;
    $out[$k] = is_numeric($raw) ? (float)$raw : (float)$v;
  }
  return $out;
}


/* v2.2.500: force refresh hero section — nowe hero Sklepy internetowe Katowice 1:1, bez starego hero z CMS. */
add_action('init', function(){
  $ver_key = 'zp_suite_shop_katowice_hero_497_version';
  if (get_option($ver_key) !== '2.2.500') {
    $sections = get_option('zp_suite_shop_katowice_sections', []);
    if (!is_array($sections)) { $sections = []; }
    $sections['hero'] = zp_suite_shop_katowice_default_section('hero');
    update_option('zp_suite_shop_katowice_sections', $sections, false);
    update_option($ver_key, '2.2.500', false);
  }
}, 9);

function zp_suite_shop_katowice_sections_map(){
  return [
    'hero'       => ['label'=>'Hero', 'file'=>'hero.html'],
    'why'        => ['label'=>'Dlaczego sklep u nas', 'file'=>'why.html'],
    'trust'      => ['label'=>'Trust / opinie', 'file'=>'trust.html'],
    'packages'   => ['label'=>'Pakiety sklepów', 'file'=>'packages.html'],
    'portfolio'  => ['label'=>'Portfolio e-commerce', 'file'=>'portfolio.html'],
    'seo_boost'  => ['label'=>'SEO boost / WooCommerce', 'file'=>'seo-boost.html'],
    'process'    => ['label'=>'Jak budujemy sklep', 'file'=>'process.html'],
    'industries' => ['label'=>'Sklepy dla branż', 'file'=>'industries.html'],
    'faq'        => ['label'=>'FAQ', 'file'=>'faq.html'],
  ];
}

function zp_suite_shop_katowice_default_section($key){
  $m = zp_suite_shop_katowice_sections_map();
  $file = ZP_SUITE_PATH.'templates/shop-katowice/sections/'.($m[$key]['file'] ?? '');
  return file_exists($file) ? file_get_contents($file) : '';
}

function zp_suite_shop_katowice_get_section($key){
  $saved = get_option('zp_suite_shop_katowice_sections', []);
  if (is_array($saved) && isset($saved[$key]) && trim((string)$saved[$key]) !== '') {
    return (string)$saved[$key];
  }
  return zp_suite_shop_katowice_default_section($key);
}

function zp_suite_shop_katowice_clean_html($html){
  $html = str_replace('http://zaprojektowani.com/', 'https://zaprojektowani.com/', (string)$html);
  if (function_exists('zp_suite_meta_verified_tokens')) { $html = zp_suite_meta_verified_tokens($html); }
  $html = preg_replace('#<script\s+src=["\'][^"\']*lucide\.min\.js[^"\']*["\']\s*></script>#i', '', $html);
  if (strpos($html, 'zh--shops') === false && strpos($html, 'zpShopHeroUnified') === false) {
    $html = preg_replace('#<style\b[^>]*>.*?</style>#is', '', $html);
  }
  if (function_exists('zp_suite_mobile_hero_strip_video')) {
    $html = zp_suite_mobile_hero_strip_video($html);
  }
  if (function_exists('zp_suite_defer_hero_video_sources')) {
    $html = zp_suite_defer_hero_video_sources($html);
  }
  return $html;
}

function zp_suite_shop_katowice_render(){
  if (function_exists('zp_suite_enqueue_block_assets')) {
    zp_suite_enqueue_block_assets('zp_sklepy_internetowe_katowice');
    zp_suite_enqueue_block_assets('zp_contact_system');
  }
  ob_start();
  include ZP_SUITE_PATH.'templates/shop-katowice/page.php';
  return ob_get_clean();
}

add_shortcode('zp_sklepy_internetowe_katowice', 'zp_suite_shop_katowice_render');
add_shortcode('zp_page_sklepy_katowice', 'zp_suite_shop_katowice_render');

add_action('admin_menu', function(){
  add_submenu_page('zp-suite', 'Sklepy internetowe Katowice', 'Sklepy Katowice', 'manage_options', 'zp-suite-shop-katowice', 'zp_suite_shop_katowice_admin_page');
}, 40);

function zp_suite_shop_katowice_admin_page(){
  if (!current_user_can('manage_options')) return;
  if (isset($_POST['zp_shop_kat_save'])) {
    check_admin_referer('zp_shop_kat_save');
    $incoming = isset($_POST['sections']) && is_array($_POST['sections']) ? $_POST['sections'] : [];
    $clean = [];
    foreach (zp_suite_shop_katowice_sections_map() as $key=>$data) {
      $clean[$key] = isset($incoming[$key]) ? wp_unslash($incoming[$key]) : '';
    }
    update_option('zp_suite_shop_katowice_sections', $clean, false);
    $contact_html = isset($_POST['shop_contact_html']) ? wp_unslash($_POST['shop_contact_html']) : '';
    update_option('zp_suite_shop_katowice_contact_html', $contact_html, false);
    $mock_in = isset($_POST['shop_mock']) && is_array($_POST['shop_mock']) ? wp_unslash($_POST['shop_mock']) : [];
    $mock_clean = [
      'x' => isset($mock_in['x']) ? (float)$mock_in['x'] : 34,
      'y' => isset($mock_in['y']) ? (float)$mock_in['y'] : -100,
      'scale' => isset($mock_in['scale']) ? (float)$mock_in['scale'] : 1,
      'rotate' => isset($mock_in['rotate']) ? (float)$mock_in['rotate'] : -3.2,
      'mobile_scale' => isset($mock_in['mobile_scale']) ? (float)$mock_in['mobile_scale'] : 1,
      'mobile_x' => isset($mock_in['mobile_x']) ? (float)$mock_in['mobile_x'] : 0,
      'mobile_y' => isset($mock_in['mobile_y']) ? (float)$mock_in['mobile_y'] : 0,
      'chips_x' => isset($mock_in['chips_x']) ? (float)$mock_in['chips_x'] : 0,
      'chips_y' => isset($mock_in['chips_y']) ? (float)$mock_in['chips_y'] : 0,
      'chips_scale' => isset($mock_in['chips_scale']) ? (float)$mock_in['chips_scale'] : 1,
      'card1_x' => isset($mock_in['card1_x']) ? (float)$mock_in['card1_x'] : 0,
      'card1_y' => isset($mock_in['card1_y']) ? (float)$mock_in['card1_y'] : 0,
      'checkout_final_y' => isset($mock_in['checkout_final_y']) ? (float)$mock_in['checkout_final_y'] : -360,
      'card1_scale' => isset($mock_in['card1_scale']) ? (float)$mock_in['card1_scale'] : 1,
      'card2_x' => isset($mock_in['card2_x']) ? (float)$mock_in['card2_x'] : 0,
      'card2_y' => isset($mock_in['card2_y']) ? (float)$mock_in['card2_y'] : 0,
      'card2_scale' => isset($mock_in['card2_scale']) ? (float)$mock_in['card2_scale'] : 1,
      'mobile_chips_x' => isset($mock_in['mobile_chips_x']) ? (float)$mock_in['mobile_chips_x'] : 0,
      'mobile_chips_y' => isset($mock_in['mobile_chips_y']) ? (float)$mock_in['mobile_chips_y'] : 0,
      'mobile_chips_scale' => isset($mock_in['mobile_chips_scale']) ? (float)$mock_in['mobile_chips_scale'] : 1,
      'mobile_card1_x' => isset($mock_in['mobile_card1_x']) ? (float)$mock_in['mobile_card1_x'] : 0,
      'mobile_card1_y' => isset($mock_in['mobile_card1_y']) ? (float)$mock_in['mobile_card1_y'] : 0,
      'mobile_card1_scale' => isset($mock_in['mobile_card1_scale']) ? (float)$mock_in['mobile_card1_scale'] : 1,
      'mobile_card2_x' => isset($mock_in['mobile_card2_x']) ? (float)$mock_in['mobile_card2_x'] : 0,
      'mobile_card2_y' => isset($mock_in['mobile_card2_y']) ? (float)$mock_in['mobile_card2_y'] : 0,
      'mobile_card2_scale' => isset($mock_in['mobile_card2_scale']) ? (float)$mock_in['mobile_card2_scale'] : 1,
    ];
    update_option('zp_suite_shop_katowice_mock_settings', $mock_clean, false);
    echo '<div class="notice notice-success"><p>Podstrona Sklepy internetowe Katowice została zapisana.</p></div>';
  }
  if (isset($_POST['zp_shop_kat_seed'])) {
    check_admin_referer('zp_shop_kat_save');
    delete_option('zp_suite_shop_katowice_sections');
    delete_option('zp_suite_shop_katowice_contact_html');
    delete_option('zp_suite_shop_katowice_mock_settings');
    echo '<div class="notice notice-success"><p>Przywrócono sekcje domyślne z plików wtyczki.</p></div>';
  }
  echo '<div class="wrap zpSuiteAdmin"><h1>Sklepy internetowe Katowice</h1><p>Shortcode: <code>[zp_sklepy_internetowe_katowice]</code>. Poniżej możesz edytować HTML każdej sekcji podstrony osobno. CSS/JS nadal ładuje się z zewnętrznych assetów wtyczki.</p>';
  echo '<p><a class="button button-primary" target="_blank" href="'.esc_url(home_url('/sklepy-internetowe-katowice/')).'">Zobacz podstronę</a></p>';
  echo '<form method="post">';
  wp_nonce_field('zp_shop_kat_save');
  foreach (zp_suite_shop_katowice_sections_map() as $key=>$data) {
    echo '<h2>'.esc_html($data['label']).'</h2>';
    echo '<textarea name="sections['.esc_attr($key).']" rows="18" style="width:100%;font-family:ui-monospace,SFMono-Regular,Menlo,monospace;font-size:12px;">'.esc_textarea(zp_suite_shop_katowice_get_section($key)).'</textarea>';
  }

  $mock = zp_suite_shop_katowice_mock_settings();
  echo '<h2>Ustawienia mockupu — „Dlaczego sklep u nas?”</h2>';
  echo '<p>Te ustawienia sterują laptopem/mockupem i elementami pływającymi widgetu 1:1. <strong>Y większe = niżej</strong>, X większe = bardziej w prawo. Po zmianie zapisz i wyczyść cache.</p>';
  echo '<table class="form-table" role="presentation"><tbody>';
  echo '<tr><th scope="row"><label for="zp_shop_mock_y">Góra / dół — Y</label></th><td><input id="zp_shop_mock_y" name="shop_mock[y]" type="number" step="1" value="'.esc_attr($mock['y']).'" class="small-text"> px <p class="description">Domyślnie -100. Zwiększ, żeby obniżyć laptop; zmniejsz, żeby podnieść.</p></td></tr>';
  echo '<tr><th scope="row"><label for="zp_shop_mock_x">Lewo / prawo — X</label></th><td><input id="zp_shop_mock_x" name="shop_mock[x]" type="number" step="1" value="'.esc_attr($mock['x']).'" class="small-text"> px <p class="description">Domyślnie 34. Wartość dodatnia przesuwa w prawo, ujemna w lewo.</p></td></tr>';
  echo '<tr><th scope="row"><label for="zp_shop_mock_scale">Skala</label></th><td><input id="zp_shop_mock_scale" name="shop_mock[scale]" type="number" step="0.01" value="'.esc_attr($mock['scale']).'" class="small-text"> <p class="description">1 = normalnie, 0.95 = mniejszy, 1.05 = większy laptop.</p></td></tr>';
  echo '<tr><th scope="row"><label for="zp_shop_mock_rotate">Obrót</label></th><td><input id="zp_shop_mock_rotate" name="shop_mock[rotate]" type="number" step="0.1" value="'.esc_attr($mock['rotate']).'" class="small-text"> deg <p class="description">Domyślnie -3.2.</p></td></tr>';
  echo '<tr><th scope="row"><label for="zp_shop_mock_mobile_scale">Rozmiar mockupu na mobile</label></th><td><input id="zp_shop_mock_mobile_scale" name="shop_mock[mobile_scale]" type="number" step="0.01" value="'.esc_attr($mock['mobile_scale']).'" class="small-text"> <p class="description">1 = domyślnie, 1.1 = większy, 0.9 = mniejszy.</p></td></tr>';
  echo '<tr><th scope="row">Mockup mobile — pozycja</th><td>X <input name="shop_mock[mobile_x]" type="number" step="1" value="'.esc_attr($mock['mobile_x']).'" class="small-text"> px &nbsp; Y <input name="shop_mock[mobile_y]" type="number" step="1" value="'.esc_attr($mock['mobile_y']).'" class="small-text"> px <p class="description">Mobile: X dodatnie = w prawo, Y dodatnie = niżej.</p></td></tr>';
  echo '<tr><th scope="row">Chipy — desktop</th><td>X <input name="shop_mock[chips_x]" type="number" step="1" value="'.esc_attr($mock['chips_x']).'" class="small-text"> px &nbsp; Y <input name="shop_mock[chips_y]" type="number" step="1" value="'.esc_attr($mock['chips_y']).'" class="small-text"> px &nbsp; Skala <input name="shop_mock[chips_scale]" type="number" step="0.01" value="'.esc_attr($mock['chips_scale']).'" class="small-text"><p class="description">Sterowanie grupą chipów na desktopie.</p></td></tr>';
  echo '<tr><th scope="row">Karta 1 — desktop</th><td>X <input name="shop_mock[card1_x]" type="number" step="1" value="'.esc_attr($mock['card1_x']).'" class="small-text"> px &nbsp; Y <input name="shop_mock[card1_y]" type="number" step="1" value="'.esc_attr($mock['card1_y']).'" class="small-text"> px &nbsp; Skala <input name="shop_mock[card1_scale]" type="number" step="0.01" value="'.esc_attr($mock['card1_scale']).'" class="small-text"><p class="description">Sterowanie pływającą kartą Checkout na desktopie.</p></td></tr>';
  echo '<tr><th scope="row">Checkout — pozycja finalna desktop</th><td>Y <input name="shop_mock[checkout_final_y]" type="number" step="1" value="'.esc_attr($mock['checkout_final_y']).'" class="small-text"> px <p class="description"><strong>Główne ustawienie góra/dół dla karty Checkout / prosty zakup.</strong> Większa wartość = niżej, mniejsza = wyżej. Aktualnie ustawione pod tę wersję: -360.</p></td></tr>';
  echo '<tr><th scope="row">Karta 2 — desktop</th><td>X <input name="shop_mock[card2_x]" type="number" step="1" value="'.esc_attr($mock['card2_x']).'" class="small-text"> px &nbsp; Y <input name="shop_mock[card2_y]" type="number" step="1" value="'.esc_attr($mock['card2_y']).'" class="small-text"> px &nbsp; Skala <input name="shop_mock[card2_scale]" type="number" step="0.01" value="'.esc_attr($mock['card2_scale']).'" class="small-text"><p class="description">Sterowanie pływającą kartą Sprzedaż na desktopie.</p></td></tr>';
  echo '<tr><th scope="row">Chipy — mobile</th><td>X <input name="shop_mock[mobile_chips_x]" type="number" step="1" value="'.esc_attr($mock['mobile_chips_x']).'" class="small-text"> px &nbsp; Y <input name="shop_mock[mobile_chips_y]" type="number" step="1" value="'.esc_attr($mock['mobile_chips_y']).'" class="small-text"> px &nbsp; Skala <input name="shop_mock[mobile_chips_scale]" type="number" step="0.01" value="'.esc_attr($mock['mobile_chips_scale']).'" class="small-text"><p class="description">Osobne ustawienia chipów tylko dla mobile.</p></td></tr>';
  echo '<tr><th scope="row">Karta 1 — mobile</th><td>X <input name="shop_mock[mobile_card1_x]" type="number" step="1" value="'.esc_attr($mock['mobile_card1_x']).'" class="small-text"> px &nbsp; Y <input name="shop_mock[mobile_card1_y]" type="number" step="1" value="'.esc_attr($mock['mobile_card1_y']).'" class="small-text"> px &nbsp; Skala <input name="shop_mock[mobile_card1_scale]" type="number" step="0.01" value="'.esc_attr($mock['mobile_card1_scale']).'" class="small-text"><p class="description">Osobne ustawienia karty Checkout tylko dla mobile.</p></td></tr>';
  echo '<tr><th scope="row">Karta 2 — mobile</th><td>X <input name="shop_mock[mobile_card2_x]" type="number" step="1" value="'.esc_attr($mock['mobile_card2_x']).'" class="small-text"> px &nbsp; Y <input name="shop_mock[mobile_card2_y]" type="number" step="1" value="'.esc_attr($mock['mobile_card2_y']).'" class="small-text"> px &nbsp; Skala <input name="shop_mock[mobile_card2_scale]" type="number" step="0.01" value="'.esc_attr($mock['mobile_card2_scale']).'" class="small-text"><p class="description">Osobne ustawienia karty Sprzedaż tylko dla mobile.</p></td></tr>';
  echo '</tbody></table>';

  echo '<h2>Formularz kontaktowy pod FAQ</h2>';
  echo '<p>Domyślnie zostaw puste — wtedy wtyczka wyrenderuje aktualny formularz [zp_contact_system] z treściami pod sklepy. Możesz wkleić własny HTML, jeśli chcesz nadpisać tę sekcję.</p>';
  echo '<textarea name="shop_contact_html" rows="18" style="width:100%;font-family:ui-monospace,SFMono-Regular,Menlo,monospace;font-size:12px;">'.esc_textarea(get_option('zp_suite_shop_katowice_contact_html', '')).'</textarea>';
  echo '<p><button class="button button-primary" name="zp_shop_kat_save" value="1">Zapisz sekcje</button> <button class="button" name="zp_shop_kat_seed" value="1" onclick="return confirm(\'Przywrócić domyślne sekcje z plików wtyczki?\')">Przywróć domyślne</button></p>';
  echo '</form></div>';
}

add_action('wp_head', function(){
  if (is_admin() || !is_singular()) return;
  global $post;
  if (!$post || empty($post->post_content)) return;
  if (has_shortcode($post->post_content, 'zp_sklepy_internetowe_katowice') || has_shortcode($post->post_content, 'zp_page_sklepy_katowice')) {
    echo "\n<!-- ZP SEO hint: sklepy internetowe Katowice / sklep internetowy WooCommerce / sklepy WooCommerce Katowice -->\n";
  }
}, 2);

/** v2.2.111 — CLEAN WHY controls from ZP Suite CMS. Desktop + mobile controls for mockup, chips and floating cards. */
add_action('wp_head', function(){
  if (is_admin()) return;
  $mock = zp_suite_shop_katowice_mock_settings();
  $vars = [
    'mockX' => [(float)$mock['x'], 'px'],
    'mockY' => [(float)$mock['y'], 'px'],
    'mockScale' => [(float)$mock['scale'], ''],
    'mockRotate' => [(float)$mock['rotate'], 'deg'],
    'mockMobileX' => [(float)$mock['mobile_x'], 'px'],
    'mockMobileY' => [(float)$mock['mobile_y'], 'px'],
    'mockMobileScale' => [(float)$mock['mobile_scale'], ''],
    'chipsX' => [(float)$mock['chips_x'], 'px'],
    'chipsY' => [(float)$mock['chips_y'], 'px'],
    'chipsScale' => [(float)$mock['chips_scale'], ''],
    'checkoutCardX' => [(float)$mock['card1_x'], 'px'],
    'checkoutCardY' => [(float)$mock['card1_y'], 'px'],
    'checkoutFinalY' => [(float)$mock['checkout_final_y'], 'px'],
    'checkoutCardScale' => [(float)$mock['card1_scale'], ''],
    'salesCardX' => [(float)$mock['card2_x'], 'px'],
    'salesCardY' => [(float)$mock['card2_y'], 'px'],
    'salesCardScale' => [(float)$mock['card2_scale'], ''],
    'chipsXMobile' => [(float)$mock['mobile_chips_x'], 'px'],
    'chipsYMobile' => [(float)$mock['mobile_chips_y'], 'px'],
    'chipsScaleMobile' => [(float)$mock['mobile_chips_scale'], ''],
    'checkoutCardXMobile' => [(float)$mock['mobile_card1_x'], 'px'],
    'checkoutCardYMobile' => [(float)$mock['mobile_card1_y'], 'px'],
    'checkoutCardScaleMobile' => [(float)$mock['mobile_card1_scale'], ''],
    'salesCardXMobile' => [(float)$mock['mobile_card2_x'], 'px'],
    'salesCardYMobile' => [(float)$mock['mobile_card2_y'], 'px'],
    'salesCardScaleMobile' => [(float)$mock['mobile_card2_scale'], ''],
  ];
  ?>
  <style id="zp-suite-2191-shop-why-clean-controls">
    #zpShopWhyLight{
      <?php foreach ($vars as $name=>$data) { echo '--'.esc_html($name).': '.esc_html($data[0]).esc_html($data[1])." !important;\n      "; } ?>
      --floatCardsX:0px!important;
      --floatCardsY:0px!important;
      --visualMoveX:0px!important;
      --visualMoveY:0px!important;
      --visualScale:1!important;
    }
    #zpShopWhyLight .zpShopWhyLight__mockWrap{
      transform:translate(calc(-50% + var(--mockX)),calc(-50% + var(--mockY))) rotate(var(--mockRotate)) scale(var(--mockScale))!important;
    }
    #zpShopWhyLight .zpShopWhyLight__floatCard--a{
      left:calc(0px + var(--checkoutCardX))!important;
      top:calc(14% + var(--checkoutCardY))!important;
      animation:zpShopWhyFloatACtrl 6.8s ease-in-out infinite!important;
      transform-origin:center center!important;
    }
    #zpShopWhyLight .zpShopWhyLight__floatCard--b{
      right:calc(4% - var(--salesCardX))!important;
      bottom:calc(14% - var(--salesCardY))!important;
      animation:zpShopWhyFloatBCtrl 7.2s ease-in-out infinite!important;
      transform-origin:center center!important;
    }
    #zpShopWhyLight .zpShopWhyLight__chips{
      left:calc(6% + var(--chipsX))!important;
      bottom:calc(6% - var(--chipsY))!important;
      transform:scale(var(--chipsScale))!important;
      transform-origin:left bottom!important;
    }
    @keyframes zpShopWhyFloatACtrl{
      0%,100%{transform:rotate(-2deg) scale(var(--checkoutCardScale));}
      50%{transform:translate3d(8px,-14px,0) rotate(1deg) scale(var(--checkoutCardScale));}
    }
    @keyframes zpShopWhyFloatBCtrl{
      0%,100%{transform:rotate(2deg) scale(var(--salesCardScale));}
      50%{transform:translate3d(-10px,-12px,0) rotate(-1deg) scale(var(--salesCardScale));}
    }
    @media(max-width:760px){
      #zpShopWhyLight{
        --mockX:var(--mockMobileX)!important;
        --mockY:var(--mockMobileY)!important;
        --mockScale:var(--mockMobileScale)!important;
        --chipsX:var(--chipsXMobile)!important;
        --chipsY:var(--chipsYMobile)!important;
        --chipsScale:var(--chipsScaleMobile)!important;
        --checkoutCardX:var(--checkoutCardXMobile)!important;
        --checkoutCardY:var(--checkoutCardYMobile)!important;
        --checkoutCardScale:var(--checkoutCardScaleMobile)!important;
        --salesCardX:var(--salesCardXMobile)!important;
        --salesCardY:var(--salesCardYMobile)!important;
        --salesCardScale:var(--salesCardScaleMobile)!important;
      }
      #zpShopWhyLight .zpShopWhyLight__chips{
        left:calc(0px + var(--chipsX))!important;
        bottom:calc(28px - var(--chipsY))!important;
      }
      #zpShopWhyLight .zpShopWhyLight__floatCard--a{
        left:calc(2px + var(--checkoutCardX))!important;
        top:calc(3% + var(--checkoutCardY))!important;
      }
      #zpShopWhyLight .zpShopWhyLight__floatCard--b{
        right:calc(0px - var(--salesCardX))!important;
        bottom:calc(18% - var(--salesCardY))!important;
      }
    }
  </style>
  <?php
}, 9999);
