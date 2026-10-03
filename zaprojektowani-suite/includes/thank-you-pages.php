<?php
/**
 * ZAPROJEKTOWANI — Thank You Pages for Studio Wyceny v1.0
 * Shortcodes:
 * [zp_thank_logo]
 * [zp_thank_wycena_strony]
 * [zp_thank_wycena_sklepu]
 */
if (!defined('ABSPATH')) { exit; }

/**
 * v2.2.54 — Thank you pages: body classes + fixed white header mode.
 */
function zp_suite_is_thank_page_context(){
  if (is_admin()) { return false; }

  $slugs = [
    'dziekujemy-za-zamowienie-logo',
    'dziekujemy-za-zapytanie-o-wycene-strony-internetowej',
    'dziekujemy-za-zapytanie-o-wycene-sklepu-internetowego',
  ];

  if (function_exists('is_page')) {
    foreach ($slugs as $slug) {
      if (is_page($slug)) { return true; }
    }
  }

  if (function_exists('is_singular') && is_singular()) {
    $post_id = get_queried_object_id();
    $content = $post_id ? (string) get_post_field('post_content', $post_id) : '';
    if ($content && (
      has_shortcode($content, 'zp_thank_logo') ||
      has_shortcode($content, 'zp_thank_wycena_strony') ||
      has_shortcode($content, 'zp_thank_wycena_sklepu') ||
      has_shortcode($content, 'zp_dziekujemy_logo') ||
      has_shortcode($content, 'zp_dziekujemy_wycena_strony') ||
      has_shortcode($content, 'zp_dziekujemy_wycena_sklepu')
    )) {
      return true;
    }
  }

  return false;
}

add_filter('body_class', function($classes){
  if (zp_suite_is_thank_page_context()) {
    $classes[] = 'zp-thank-page';
    $classes[] = 'zp-thank-shortcode';
  }
  return array_values(array_unique($classes));
}, 22);

add_action('wp_head', function(){
  if (!zp_suite_is_thank_page_context()) { return; }
  ?>
  <style id="zp-suite-thank-header-mode-v2254">html body.zp-thank-page #zpNewNav,html body.zp-thank-shortcode #zpNewNav{position:fixed!important;top:0!important;left:0!important;right:auto!important;width:100vw!important;transform:none!important;translate:none!important;z-index:2147482000!important;background:transparent!important}html body.admin-bar.zp-thank-page #zpNewNav,html body.admin-bar.zp-thank-shortcode #zpNewNav{top:32px!important}@media(max-width:782px){html body.admin-bar.zp-thank-page #zpNewNav,html body.admin-bar.zp-thank-shortcode #zpNewNav{top:46px!important}}html body.zp-thank-page #zpNewNav .zpNewNav__fixedSpacer,html body.zp-thank-shortcode #zpNewNav .zpNewNav__fixedSpacer{height:0!important;display:none!important}html body.zp-thank-page #zpNewNav:not(.is-mega-open) .zpNewNav__shell,html body.zp-thank-shortcode #zpNewNav:not(.is-mega-open) .zpNewNav__shell{background:rgba(255,255,255,.965)!important;color:#071426!important;box-shadow:0 14px 42px rgba(5,10,18,.08)!important;border-bottom:1px solid rgba(7,20,38,.055)!important;backdrop-filter: none !important;-webkit-backdrop-filter: none !important}html body.zp-thank-page #zpNewNav:not(.is-mega-open) .zpNewNav__link,html body.zp-thank-page #zpNewNav:not(.is-mega-open) .zpNewNav__round,html body.zp-thank-page #zpNewNav:not(.is-mega-open) .zpNewNav__contactIcon,html body.zp-thank-page #zpNewNav:not(.is-mega-open) .zpNewNav__mobileSearch,html body.zp-thank-page #zpNewNav:not(.is-mega-open) .zpNewNav__burger,html body.zp-thank-shortcode #zpNewNav:not(.is-mega-open) .zpNewNav__link,html body.zp-thank-shortcode #zpNewNav:not(.is-mega-open) .zpNewNav__round,html body.zp-thank-shortcode #zpNewNav:not(.is-mega-open) .zpNewNav__contactIcon,html body.zp-thank-shortcode #zpNewNav:not(.is-mega-open) .zpNewNav__mobileSearch,html body.zp-thank-shortcode #zpNewNav:not(.is-mega-open) .zpNewNav__burger{color:#071426!important}html body.zp-thank-page #zpNewNav:not(.is-mega-open) .zpNewNav__logo--light,html body.zp-thank-shortcode #zpNewNav:not(.is-mega-open) .zpNewNav__logo--light,html body.zp-thank-page #zpNewNav:not(.is-mega-open) .zpNewNav__mobileLogo--light,html body.zp-thank-shortcode #zpNewNav:not(.is-mega-open) .zpNewNav__mobileLogo--light{display:none!important;opacity:0!important;visibility:hidden!important}html body.zp-thank-page #zpNewNav:not(.is-mega-open) .zpNewNav__logo--dark,html body.zp-thank-shortcode #zpNewNav:not(.is-mega-open) .zpNewNav__logo--dark,html body.zp-thank-page #zpNewNav:not(.is-mega-open) .zpNewNav__mobileLogo--dark,html body.zp-thank-shortcode #zpNewNav:not(.is-mega-open) .zpNewNav__mobileLogo--dark{display:block!important;opacity:1!important;visibility:visible!important}</style>
  <?php
}, 30);



function zp_suite_thank_order_id_from_request(){
  $id = '';
  if (isset($_GET['zp_order'])) { $id = sanitize_text_field(wp_unslash($_GET['zp_order'])); }
  elseif (isset($_GET['order'])) { $id = sanitize_text_field(wp_unslash($_GET['order'])); }
  return $id;
}

function zp_suite_thank_find_order($id=''){
  $id = $id ? sanitize_text_field($id) : zp_suite_thank_order_id_from_request();
  if (!$id || !defined('ZP_STUDIO_OPT_ORDERS')) { return null; }
  $orders = get_option(ZP_STUDIO_OPT_ORDERS, []);
  if (!is_array($orders)) { return null; }
  foreach ($orders as $order) {
    if (!empty($order['id']) && hash_equals((string)$order['id'], (string)$id)) { return $order; }
  }
  return null;
}

function zp_suite_thank_payload_value($payload, $keys, $fallback=''){
  if (!is_array($keys)) { $keys = [$keys]; }
  foreach ($keys as $key) {
    if (isset($payload[$key]) && trim((string)$payload[$key]) !== '') { return trim((string)$payload[$key]); }
  }
  return $fallback;
}

function zp_suite_thank_summary_rows($type, $payload){
  $rows = [];
  if ($type === 'logo') {
    $rows = [
      ['Usługa', zp_suite_thank_payload_value($payload, 'Usługa', 'Logo & Branding')],
      ['Pakiet', zp_suite_thank_payload_value($payload, 'Pakiet', 'Wybrany pakiet logo')],
      ['Termin', zp_suite_thank_payload_value($payload, 'Termin', 'Po akceptacji briefu')],
      ['Kontakt', zp_suite_thank_payload_value($payload, 'Kontakt', 'E-mail')],
      ['Marka', zp_suite_thank_payload_value($payload, 'Marka', '')],
      ['Branża', zp_suite_thank_payload_value($payload, 'Branża', '')],
      ['Dodatki', zp_suite_thank_payload_value($payload, 'Dodatki', '')],
    ];
  } elseif ($type === 'shop') {
    $rows = [
      ['Usługa', zp_suite_thank_payload_value($payload, 'Usługa', 'Wycena sklepu internetowego')],
      ['Pakiet', zp_suite_thank_payload_value($payload, 'Pakiet', 'Sklep internetowy')],
      ['Budżet', zp_suite_thank_payload_value($payload, 'Budżet', 'Do ustalenia po analizie')],
      ['Kontakt', zp_suite_thank_payload_value($payload, 'Kontakt', 'E-mail')],
      ['Produkty', zp_suite_thank_payload_value($payload, ['Produkty','Podstrony'], '')],
      ['Płatności', zp_suite_thank_payload_value($payload, 'Płatności', '')],
      ['Dostawy', zp_suite_thank_payload_value($payload, 'Dostawy', '')],
      ['Funkcje', zp_suite_thank_payload_value($payload, 'Funkcje', '')],
      ['Domena', zp_suite_thank_payload_value($payload, ['Preferowane domeny','Preferowana domena','Pomysł na domenę'], '')],
    ];
  } else {
    $rows = [
      ['Usługa', zp_suite_thank_payload_value($payload, 'Usługa', 'Wycena strony internetowej')],
      ['Pakiet', zp_suite_thank_payload_value($payload, 'Pakiet', 'Strona internetowa')],
      ['Budżet', zp_suite_thank_payload_value($payload, 'Budżet', 'Do ustalenia po analizie')],
      ['Termin', zp_suite_thank_payload_value($payload, 'Termin', '')],
      ['Kontakt', zp_suite_thank_payload_value($payload, 'Kontakt', 'E-mail')],
      ['Branża', zp_suite_thank_payload_value($payload, 'Branża', '')],
      ['Cel', zp_suite_thank_payload_value($payload, 'Cel', '')],
      ['Podstrony', zp_suite_thank_payload_value($payload, 'Podstrony', '')],
      ['Domena', zp_suite_thank_payload_value($payload, ['Preferowane domeny','Preferowana domena','Pomysł na domenę'], '')],
    ];
  }
  return array_values(array_filter($rows, function($row){ return isset($row[1]) && trim((string)$row[1]) !== ''; }));
}

function zp_suite_thank_summary_html($prefix, $rows){
  $out = '<div class="'.esc_attr($prefix).'__summary">';
  foreach ($rows as $row) {
    $out .= '<div class="'.esc_attr($prefix).'__summaryItem">';
    $out .= '<span class="'.esc_attr($prefix).'__summaryLabel">'.esc_html($row[0]).'</span>';
    $out .= '<strong class="'.esc_attr($prefix).'__summaryValue">'.esc_html($row[1]).'</strong>';
    $out .= '</div>';
  }
  $out .= '</div>';
  return $out;
}

function zp_suite_thank_render_template($type){
  $map = [
    'logo' => ['file'=>'logo.html','prefix'=>'zpThankLogo'],
    'web'  => ['file'=>'strona.html','prefix'=>'zpThankWww'],
    'shop' => ['file'=>'sklep.html','prefix'=>'zpThankShop'],
  ];
  if (empty($map[$type])) { $type = 'web'; }
  $file = ZP_SUITE_PATH . 'templates/thank-you/' . $map[$type]['file'];
  if (!file_exists($file)) { return '<!-- ZP Thank You: brak template -->'; }
  $prefix = $map[$type]['prefix'];
  $html = file_get_contents($file);
  $order = zp_suite_thank_find_order();
  $payload = is_array($order) && !empty($order['payload']) && is_array($order['payload']) ? $order['payload'] : [];
  $order_id = is_array($order) && !empty($order['id']) ? (string)$order['id'] : '';
  $created = is_array($order) && !empty($order['created']) ? (string)$order['created'] : '';

  $display_id = $order_id ?: ($type === 'logo' ? 'ZP-LOGO' : ($type === 'shop' ? 'ZP-SKLEP' : 'ZP-STRONA'));
  $html = preg_replace('/<p class="'.preg_quote($prefix,'/').'__orderNo">[\s\S]*?<\/p>/', '<p class="'.$prefix.'__orderNo">'.esc_html($display_id).'</p>', $html, 1);

  if ($order_id) {
    $txt = $type === 'logo'
      ? 'Zapisaliśmy Twoje zamówienie i przejdziemy teraz do weryfikacji informacji potrzebnych do rozpoczęcia projektu.'
      : 'Zapisaliśmy Twoje zapytanie i przejdziemy teraz do analizy informacji potrzebnych do przygotowania wyceny.';
    if ($created) { $txt .= ' Data zgłoszenia: ' . $created . '.'; }
  } else {
    $txt = $type === 'logo'
      ? 'Zamówienie zostało wysłane. Szczegóły z formularza zostaną pokazane tutaj po wejściu z linku potwierdzającego.'
      : 'Zapytanie zostało wysłane. Szczegóły z formularza zostaną pokazane tutaj po wejściu z linku potwierdzającego.';
  }
  $html = preg_replace('/<p class="'.preg_quote($prefix,'/').'__orderText">[\s\S]*?<\/p>/', '<p class="'.$prefix.'__orderText">'.esc_html($txt).'</p>', $html, 1);

  $rows = zp_suite_thank_summary_rows($type, $payload);
  $html = preg_replace('/<div class="'.preg_quote($prefix,'/').'__summary">[\s\S]*?<\/div>\s*<div class="'.preg_quote($prefix,'/').'__price">/', zp_suite_thank_summary_html($prefix, $rows)."\n\n        <div class=\"{$prefix}__price\">", $html, 1);

  if ($type === 'logo') {
    $sum = zp_suite_thank_payload_value($payload, ['Suma','Podsumowanie'], 'do potwierdzenia');
    $price = '<div class="'.$prefix.'__price"><span>Wartość zamówienia</span><strong>'.esc_html($sum).'</strong><small>Kwota wynika z wybranego pakietu, terminu i dodatków zapisanych w formularzu.</small></div>';
  } else {
    $budget = zp_suite_thank_payload_value($payload, 'Budżet', 'po analizie');
    $price = '<div class="'.$prefix.'__price"><span>Status wyceny</span><strong>po analizie</strong><small>Budżet z formularza: '.esc_html($budget).'. Finalny koszt przygotujemy po sprawdzeniu zakresu i materiałów.</small></div>';
  }
  $html = preg_replace('/<div class="'.preg_quote($prefix,'/').'__price">[\s\S]*?<\/div>/', $price, $html, 1);

  return $html;
}

add_shortcode('zp_thank_logo', function(){ return zp_suite_thank_render_template('logo'); });
add_shortcode('zp_thank_wycena_strony', function(){ return zp_suite_thank_render_template('web'); });
add_shortcode('zp_thank_wycena_sklepu', function(){ return zp_suite_thank_render_template('shop'); });

// Przyjazne aliasy, gdyby Elementor / klient użył innej nazwy.
add_shortcode('zp_dziekujemy_logo', function(){ return zp_suite_thank_render_template('logo'); });
add_shortcode('zp_dziekujemy_wycena_strony', function(){ return zp_suite_thank_render_template('web'); });
add_shortcode('zp_dziekujemy_wycena_sklepu', function(){ return zp_suite_thank_render_template('shop'); });
