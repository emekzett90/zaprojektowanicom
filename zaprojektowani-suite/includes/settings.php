<?php
if (!defined('ABSPATH')) { exit; }

function zp_suite_defaults() {
  return [
    'brand' => [
      'admin_email' => 'kontakt@zaprojektowani.com',
      'from_name' => 'Zaprojektowani.com',
      'from_email' => 'kontakt@zaprojektowani.com',
      'phone' => '+48 501 054 253',
      'whatsapp' => '48501054253',
      'logo_light' => 'https://zaprojektowani.com/wp-content/uploads/2026/06/zp_sygnet-scaled.webp',
      'logo_dark' => 'https://zaprojektowani.com/wp-content/uploads/2026/06/zp_sygnet_ciemny-scaled.webp',
    ],
    'header' => [
      'logo_desktop_h' => '36',
      'logo_mobile_h' => '32',
      'logo_mobile_x' => '-8',
      'desktop_height' => '88',
      'mobile_height' => '74',
      'mobile_glass_opacity' => '0.86',
      'sticky_white_strength' => '85',
      'overlay_divider_width' => '1280',
      'overlay_divider_opacity' => '0.08',
      'header_line_width' => '1850',
      'header_line_opacity' => '0.38',
      'header_line_scrolled_opacity' => '0.24',
      'cta_text' => 'Szybka wycena',
      'cta_url' => '/studio-wyceny/',
      'cta_icon' => 'arrow-up-right',
      'contact_url' => '/kontakt/',
      'menu_start' => 'Start|/\nUsługi|/strony-internetowe-katowice/\nWiedza|/wiedza/\nFAQ|/#faq\nO nas|/o-nas/\nKontakt|/kontakt/',
    ],
    'wiedza' => [
      'hero_person_scale' => '1',
      'hero_person_y' => '0',
      'hero_person_x' => '0',
      'hero_person_scale_desktop' => '1',
      'hero_person_y_desktop' => '0',
      'hero_person_x_desktop' => '0',
      'hero_person_scale_mobile' => '1',
      'hero_person_y_mobile' => '0',
      'hero_person_x_mobile' => '0',
      'hero_video_opacity' => '74',
      'hero_video_brightness' => '74',
      'hero_video_navy_mask' => '82',
      'hero_text_shadow' => '72',
    ],
    'about' => [
      'team_image' => 'https://zaprojektowani.com/wp-content/uploads/2026/05/team_box.webp',
      'team_width' => '820',
      'team_top' => '-260',
      'team_right' => '22',
      'team_scale' => '1.18',
      'team_grayscale' => '0',
      'team_opacity' => '1',
      'cards_top' => '210',
    ],
    'about_page' => [
      'top_gap_desktop' => '174',
      'top_gap_mobile' => '138',
      'hero_visual_y_desktop' => '0',
      'hero_visual_y_mobile' => '0',
      'hero_image_y_desktop' => '-50',
      'hero_image_y_mobile' => '0',
    ],
    'stats' => [
      'projects_count' => '114',
      'facebook_rating' => '5.0',
      'facebook_reviews' => '60',
      'google_rating' => '5.0',
      'google_reviews' => '22',
      'recommendations' => '120',
    ],
    'visibility' => [
      'trust_logos' => '1',
      'services_path' => '1',
      'showcase_portfolio' => '1',
      'laptop_showcase' => '1',
      'about_experience' => '1',
      'showcase_services' => '1',
      'reviews_section' => '1',
      'seo_faq' => '1',
      'seo_industries' => '1',
      'home_audit_cta' => '0',
      'contact_system' => '1',
    ],
    'home_order' => [
      'order' => 'home_hero\ntrust_logos\nservices_path\nshowcase_portfolio\nlaptop_showcase\nabout_experience\nshowcase_services\nreviews_section\nseo_faq\nseo_industries\nhome_audit_cta\ncontact_system',
    ],
    'performance' => [
      'home_lite_enabled' => '1',
      'home_lite_mobile_enabled' => '1',
      'disable_blur' => '1',
      'reduce_reveals' => '1',
      'reduce_shadows_watermarks' => '1',
      'disable_mobile_heavy_motion' => '1',
      'respect_reduced_motion' => '1',
      'katowice_lite_enabled' => '1',
      'katowice_lite_mobile_enabled' => '1',
      'katowice_disable_blur' => '1',
      'katowice_reduce_reveals' => '1',
      'katowice_reduce_shadows_watermarks' => '1',
      'katowice_disable_mobile_heavy_motion' => '1',
      'katowice_respect_reduced_motion' => '1',
    ],


    'security' => [
      'turnstile_enabled' => '1',
      'turnstile_site_key' => '0x4AAAAAADwBPagTLXO_vS7j',
      'turnstile_secret_key' => '0x4AAAAAADwBPUyhwkwUybJ4I-6v5A__-X0',
      'turnstile_fail_open' => '1',
      'honeypot_enabled' => '1',
      'min_seconds_enabled' => '1',
      'min_seconds' => '6',
    ],
    'email_templates' => [
      'admin_subject_form' => 'Nowe zapytanie — Zaprojektowani.com',
      'admin_subject_phone' => 'Prośba o telefon — Zaprojektowani.com',
      'client_subject' => 'Potwierdzenie zapytania — Zaprojektowani.com',
      'client_intro' => 'Otrzymaliśmy Twoje zapytanie. Wrócimy z odpowiedzią i propozycją dalszych kroków.',
      'email_footer' => 'Wiadomość wygenerowana przez Zaprojektowani Suite.',
    ],
    'hero' => [
      'eyebrow' => 'Strony internetowe • sklepy • branding',
      'h1_html' => '<strong>Strony internetowe Katowice</strong> — sklepy internetowe, branding i kampanie dla firm z całej Polski.',
      'lead' => 'Strony internetowe Katowice to główny obszar naszej pracy: projektujemy strony WordPress, sklepy WooCommerce, logo, identyfikację wizualną i kampanie Meta Ads. Łączymy UX, SEO, treści i konwersję, żeby strona nie tylko wyglądała dobrze, ale też pomagała zdobywać zapytania.' ,
      'primary_text' => 'Wyceń projekt',
      'primary_url' => '/studio-wyceny/',
      'secondary_text' => 'Zobacz realizacje',
      'secondary_url' => '/realizacje/',
      'chips' => '01|strony internetowe Katowice\n02|sklepy internetowe Katowice\n03|logo i branding Katowice\n04|SEO i treści\n05|kampanie Meta Ads',
      'video_url' => 'https://zaprojektowani.com/wp-content/uploads/videos/zaprojektowani_video.mp4',
      'person_url' => 'https://zaprojektowani.com/wp-content/uploads/2026/05/team_zaprojektowani.webp',
      'video_dim' => '0.76',
      'video_color_opacity' => '0.76',
      'video_shadow_opacity' => '0.60',
      'navy_1' => '#020407',
      'navy_2' => '#071426',
      'navy_3' => '#102a4f',
      'navy_4' => '#1c477a',
      'font_desktop' => '65',
      'font_mobile' => '34',
      'desktop_top_space' => '60',
      'desktop_bottom_space' => '40',
      'person_width_desktop' => '650',
      'person_x_desktop' => '-76',
      'person_y_desktop' => '-38',
      'person_scale_desktop' => '1.13',
      'copy_y_desktop' => '0',
      'signature_y_desktop' => '0',
      'signature_y_mobile' => '0',
      'review_label_size' => '11',
      'review_rating_size' => '48',
      'review_stars_size' => '11',
      'review_opinions_size' => '15.5',
      'review_desc_size' => '13.5',
      'review_link_size' => '10.5',
      'review_logo_width' => '124',
      'review_card_scale' => '1',
      'review_card_bg_opacity' => '72',
      'review_label_size_mobile' => '8.5',
      'review_rating_size_mobile' => '31',
      'review_stars_size_mobile' => '8',
      'review_opinions_size_mobile' => '12.5',
      'review_desc_size_mobile' => '10.5',
      'review_link_size_mobile' => '9',
      'review_logo_width_mobile' => '96',
      'review_card_scale_mobile' => '1',
      'line_height' => '1.04',
      'letter_spacing' => '-0.036',
      'fb_reviews' => '60',
      'google_reviews' => '22',
      'projects_count' => '114',
    ],
    'trust_logos' => [
      'eyebrow' => 'Zaufali nam',
      'heading' => 'Firmy, dla których projektowaliśmy strony, sklepy i branding',
      'lead' => 'Projektujemy strony internetowe, sklepy WooCommerce, logo, branding i kampanie dla firm z Katowic, Śląska i całej Polski — od pierwszego wrażenia po zapytanie.',
      'logos' => '',
    ],
    'contact' => [
      'heading_html' => 'Opowiedz nam o projekcie — <span>dobierzemy zakres</span> i <strong>najlepszą ścieżkę działania</strong>.',
      'lead' => 'Wybierz, czego potrzebujesz: strony internetowej, sklepu WooCommerce, logo, brandingu, SEO albo kampanii reklamowej. Potem zdecyduj, czy chcesz wysłać formularz, umówić telefon czy napisać od razu na WhatsApp.',
      'success_title' => 'Dziękujemy, zapytanie zostało wysłane.',
      'success_text' => 'Otrzymaliśmy wiadomość i wrócimy z konkretną odpowiedzią. Na podany adres e-mail wysłaliśmy potwierdzenie.',
      'error_title' => 'Uzupełnij brakujące dane',
      'privacy_text' => 'Wyrażam zgodę na kontakt w sprawie przesłanego zapytania.',
    ],
    'sticky_estimate' => [
      'enabled' => '1',
      'title' => 'Chcesz otrzymać darmową wycenę strony, sklepu, logo lub brandingu?',
      'text' => 'Opisz krótko projekt — oddzwonimy albo odpiszemy mailowo z rekomendacją najlepszej ścieżki działania.',
      'cta' => 'Poproś o bezpłatną wycenę',
      'mail_cta' => 'Wolę kontakt mailowy',
    ],
    'front_polish' => [
      'enabled' => '1',
      'loader' => '1',
      'hero_poster' => '',
      'video_strategy' => 'idle',
      'video_delay' => '900',
      'smooth_scroll' => '1',
      'reveal_manager' => '1',
      'safari_safe' => '1',
      'cls_guard' => '1',
      'smart_images' => '1',
      'header_lock' => '1',
      'asset_audit' => '1',
    ],
    'footer' => [
      'cta_eyebrow' => 'Porozmawiajmy o projekcie',
      'cta_heading' => 'Masz pomysł na stronę internetową, sklep albo branding? <span>Zamieńmy go w projekt, który wygląda dobrze, ma sens SEO i prowadzi do zapytań.</span>',
      'cta_lead' => 'Opowiedz nam, czy potrzebujesz strony internetowej, sklepu WooCommerce, logo, brandingu, SEO albo kampanii Meta Ads. Dobierzemy zakres, priorytety i kolejność działań pod Twój budżet.',
      'cta_primary_text' => 'Otrzymaj wycenę',
      'cta_primary_url' => '/studio-wyceny/',
      'cta_secondary_text' => 'Napisz na WhatsApp',
      'team_photo' => 'https://zaprojektowani.com/wp-content/uploads/2026/05/team_zaprojektowani_finalnie.webp',
      'about' => 'Projektujemy strony internetowe, sklepy WooCommerce, logo, branding, SEO oraz kampanie reklamowe dla firm, które chcą wyglądać profesjonalnie i pozyskiwać klientów online.',
    ],
    'seo' => [
      'home_title' => 'Strony internetowe Katowice, sklepy i branding | Zaprojektowani.com',
      'home_description' => 'Zaprojektowani.com: strony internetowe Katowice, sklepy WooCommerce, logo, branding, SEO i kampanie Meta Ads dla firm ze Śląska i całej Polski.',
      'focus_keywords' => 'strony internetowe Katowice, projektowanie stron internetowych Katowice, sklepy internetowe Katowice, logo i branding Katowice, kampanie Meta Ads',
    ],
  ];
}

function zp_suite_deep_merge($defaults, $saved) {
  foreach ($defaults as $k => $v) {
    if (is_array($v)) {
      $saved[$k] = isset($saved[$k]) && is_array($saved[$k]) ? zp_suite_deep_merge($v, $saved[$k]) : $v;
    } elseif (!array_key_exists($k, $saved)) {
      $saved[$k] = $v;
    }
  }
  return $saved;
}

function zp_suite_options() {
  $saved = get_option('zp_suite_options', []);
  if (!is_array($saved)) { $saved = []; }
  return zp_suite_deep_merge(zp_suite_defaults(), $saved);
}

function zp_suite_opt($path, $default = '') {
  $data = zp_suite_options();
  foreach (explode('.', $path) as $part) {
    if (!is_array($data) || !array_key_exists($part, $data)) { return $default; }
    $data = $data[$part];
  }
  return $data;
}

function zp_suite_allowed_html() {
  return [
    'strong' => [], 'span' => ['class'=>[]], 'br' => [], 'em' => [], 'b' => [], 'i' => []
  ];
}

function zp_suite_parse_lines($text, $parts = 2) {
  $text = str_replace(['\\r\\n','\\n','\\r'], "\n", (string)$text);
  $out = [];
  $lines = preg_split('/\r\n|\r|\n/', $text);
  foreach ($lines as $line) {
    $line = trim($line);
    if ($line === '') { continue; }
    $chunks = array_map('trim', explode('|', $line));
    while (count($chunks) < $parts) { $chunks[] = ''; }
    $out[] = $chunks;
  }
  return $out;
}

/* ZP Suite v1.1.0:
 * Portfolio i opinie są zarządzane wyłącznie w panelu ZP Suite → Strona główna CMS.
 * Usunięto osobne menu CPT „ZP Portfolio” i „ZP Opinie”, żeby panel był prosty i spójny.
 */
function zp_suite_portfolio_meta_box($post){
  wp_nonce_field('zp_suite_meta','zp_suite_meta_nonce');
  $fields = ['client'=>'Klient','industry'=>'Branża','type'=>'Typ projektu','url'=>'Adres URL','image_url'=>'Mockup / obraz URL','accent'=>'Kolor akcentu'];
  echo '<div class="zpMetaGrid">';
  foreach($fields as $key=>$label){
    $v = get_post_meta($post->ID, '_zp_'.$key, true);
    echo '<p><label><strong>'.esc_html($label).'</strong><input style="width:100%;margin-top:6px" type="text" name="zp_meta['.esc_attr($key).']" value="'.esc_attr($v).'"></label></p>';
  }
  echo '</div><p><em>Te dane będą używane w dynamicznym portfolio w kolejnym kroku. Obecny front zostaje 1:1.</em></p>';
}

function zp_suite_review_meta_box($post){
  wp_nonce_field('zp_suite_meta','zp_suite_meta_nonce');
  $fields = ['source'=>'Źródło (Google/Facebook)','rating'=>'Ocena','author_role'=>'Podpis / firma','project'=>'Projekt powiązany'];
  foreach($fields as $key=>$label){
    $v = get_post_meta($post->ID, '_zp_'.$key, true);
    echo '<p><label><strong>'.esc_html($label).'</strong><input style="width:100%;margin-top:6px" type="text" name="zp_meta['.esc_attr($key).']" value="'.esc_attr($v).'"></label></p>';
  }
}

add_action('save_post', function($post_id){
  if (empty($_POST['zp_suite_meta_nonce']) || !wp_verify_nonce($_POST['zp_suite_meta_nonce'], 'zp_suite_meta')) return;
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
  if (!current_user_can('edit_post', $post_id)) return;
  if (empty($_POST['zp_meta']) || !is_array($_POST['zp_meta'])) return;
  foreach($_POST['zp_meta'] as $k=>$v){
    update_post_meta($post_id, '_zp_'.sanitize_key($k), sanitize_text_field(wp_unslash($v)));
  }
});


/**
 * v2.2.107 — Home SEO migration after May 2026 strategy.
 * Forces the main homepage copy/metadata once, so existing saved CMS options
 * do not keep the old less-SEO-focused wording.
 */
add_action('init', function () {
  if (get_option('zp_suite_home_seo_migration_2_2_107') === '1') {
    return;
  }

  $opts = get_option('zp_suite_options', []);
  if (!is_array($opts)) {
    $opts = [];
  }

  $opts['hero']['eyebrow'] = 'Strony internetowe • sklepy • branding';
  $opts['hero']['h1_html'] = '<strong>Strony internetowe</strong>, <strong>sklepy internetowe</strong> i <strong>branding</strong> dla firm z Katowic i całej Polski.';
  $opts['hero']['lead'] = 'Projektujemy strony WordPress, sklepy WooCommerce, logo, identyfikację wizualną i kampanie Meta Ads. Łączymy UX, SEO, treści i konwersję, żeby strona nie tylko wyglądała dobrze, ale też pomagała zdobywać zapytania.';
  $opts['hero']['primary_text'] = 'Wyceń projekt';
  $opts['hero']['primary_url'] = '/studio-wyceny/';
  $opts['hero']['secondary_text'] = 'Zobacz realizacje';
  $opts['hero']['secondary_url'] = '/realizacje/';
  $opts['hero']['chips'] = "01|strony internetowe Katowice\n02|sklepy internetowe Katowice\n03|logo i branding Katowice\n04|SEO i treści\n05|kampanie Meta Ads";

  $opts['trust_logos']['eyebrow'] = 'Zaufali nam';
  $opts['trust_logos']['heading'] = 'Firmy, dla których projektowaliśmy strony, sklepy i branding';
  $opts['trust_logos']['lead'] = 'Projektujemy strony internetowe, sklepy WooCommerce, logo, branding i kampanie dla firm z Katowic, Śląska i całej Polski — od pierwszego wrażenia po zapytanie.';

  $opts['footer']['cta_eyebrow'] = 'Porozmawiajmy o projekcie';
  $opts['footer']['cta_heading'] = 'Masz pomysł na stronę internetową, sklep albo branding? <span>Zamieńmy go w projekt, który wygląda dobrze, ma sens SEO i prowadzi do zapytań.</span>';
  $opts['footer']['cta_lead'] = 'Opowiedz nam, czy potrzebujesz strony internetowej, sklepu WooCommerce, logo, brandingu, SEO albo kampanii Meta Ads. Dobierzemy zakres, priorytety i kolejność działań pod Twój budżet.';
  $opts['footer']['cta_primary_text'] = 'Wyceń projekt';
  $opts['footer']['cta_primary_url'] = '/studio-wyceny/';
  $opts['footer']['about'] = 'Projektujemy strony internetowe, sklepy WooCommerce, logo, branding, SEO oraz kampanie Meta Ads dla firm z Katowic, Śląska i całej Polski.';

  $opts['seo']['home_title'] = 'Strony internetowe Katowice, sklepy i branding | Zaprojektowani.com';
  $opts['seo']['home_description'] = 'Zaprojektowani.com: strony internetowe Katowice, sklepy WooCommerce, logo, branding, SEO i kampanie Meta Ads dla firm ze Śląska i całej Polski.';
  $opts['seo']['focus_keywords'] = 'strony internetowe Katowice, projektowanie stron internetowych Katowice, sklepy internetowe Katowice, logo i branding Katowice, kampanie Meta Ads';

  update_option('zp_suite_options', $opts, false);
  update_option('zp_suite_home_seo_migration_2_2_107', '1', false);
}, 18);


/**
 * v2.2.575 — domyślne wzmocnienie antyspamu dla formularzy.
 */
add_action('init', function () {
  if (get_option('zp_suite_security_defaults_2_2_575') === '1') {
    return;
  }
  $opts = get_option('zp_suite_options', []);
  if (!is_array($opts)) { $opts = []; }
  if (empty($opts['security']) || !is_array($opts['security'])) { $opts['security'] = []; }
  $opts['security']['turnstile_enabled'] = '1';
  $opts['security']['turnstile_fail_open'] = '1';
  $opts['security']['honeypot_enabled'] = '1';
  $opts['security']['min_seconds_enabled'] = '1';
  $opts['security']['min_seconds'] = '6';
  update_option('zp_suite_options', $opts, false);
  update_option('zp_suite_security_defaults_2_2_575', '1', false);
}, 19);


/**
 * v2.2.576 — wpisane klucze Cloudflare Turnstile i wymuszenie ochrony formularzy.
 */
add_action('init', function () {
  if (get_option('zp_suite_security_turnstile_keys_2_2_576') === '1') {
    return;
  }
  $opts = get_option('zp_suite_options', []);
  if (!is_array($opts)) { $opts = []; }
  if (empty($opts['security']) || !is_array($opts['security'])) { $opts['security'] = []; }
  $opts['security']['turnstile_enabled'] = '1';
  $opts['security']['turnstile_site_key'] = '0x4AAAAAADwBPagTLXO_vS7j';
  $opts['security']['turnstile_secret_key'] = '0x4AAAAAADwBPUyhwkwUybJ4I-6v5A__-X0';
  $opts['security']['turnstile_fail_open'] = '1';
  $opts['security']['honeypot_enabled'] = '1';
  $opts['security']['min_seconds_enabled'] = '1';
  $opts['security']['min_seconds'] = '6';
  update_option('zp_suite_options', $opts, false);
  update_option('zp_suite_security_turnstile_keys_2_2_576', '1', false);
}, 20);

/**
 * v2.2.577 — bezpieczny fallback Turnstile dla realnych użytkowników.
 */
add_action('init', function () {
  if (get_option('zp_suite_security_turnstile_fallback_2_2_577') === '1') {
    return;
  }
  $opts = get_option('zp_suite_options', []);
  if (!is_array($opts)) { $opts = []; }
  if (empty($opts['security']) || !is_array($opts['security'])) { $opts['security'] = []; }
  $opts['security']['turnstile_enabled'] = '1';
  $opts['security']['turnstile_site_key'] = '0x4AAAAAADwBPagTLXO_vS7j';
  $opts['security']['turnstile_secret_key'] = '0x4AAAAAADwBPUyhwkwUybJ4I-6v5A__-X0';
  $opts['security']['turnstile_fail_open'] = '1';
  $opts['security']['honeypot_enabled'] = '1';
  $opts['security']['min_seconds_enabled'] = '1';
  $opts['security']['min_seconds'] = '6';
  update_option('zp_suite_options', $opts, false);
  update_option('zp_suite_security_turnstile_fallback_2_2_577', '1', false);
}, 21);


add_action('init', function(){
  if (get_option('zp_suite_security_turnstile_delivery_2_2_578') === '1') {
    return;
  }
  $opts = get_option('zp_suite_options', []);
  if (!is_array($opts)) { $opts = []; }
  if (empty($opts['security']) || !is_array($opts['security'])) { $opts['security'] = []; }
  $opts['security']['turnstile_enabled'] = '1';
  $opts['security']['turnstile_site_key'] = '0x4AAAAAADwBPagTLXO_vS7j';
  $opts['security']['turnstile_secret_key'] = '0x4AAAAAADwBPUyhwkwUybJ4I-6v5A__-X0';
  $opts['security']['turnstile_fail_open'] = '1';
  $opts['security']['honeypot_enabled'] = '1';
  $opts['security']['min_seconds_enabled'] = '1';
  $opts['security']['min_seconds'] = '6';
  update_option('zp_suite_options', $opts, false);
  update_option('zp_suite_security_turnstile_delivery_2_2_578', '1', false);
}, 8);

