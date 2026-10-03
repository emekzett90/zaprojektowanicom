<?php
if (!defined('ABSPATH')) { exit; }

add_action('wp_ajax_zp_suite_contact', 'zp_suite_handle_contact');
add_action('wp_ajax_nopriv_zp_suite_contact', 'zp_suite_handle_contact');

function zp_suite_clean($key) {
  return isset($_POST[$key]) ? sanitize_text_field(wp_unslash($_POST[$key])) : '';
}


function zp_suite_leads_all(){
  $leads = get_option('zp_suite_leads', []);
  return is_array($leads) ? $leads : [];
}

function zp_suite_leads_new_count(){
  $count = 0;
  foreach (zp_suite_leads_all() as $lead) {
    if (($lead['status'] ?? 'new') === 'new') { $count++; }
  }
  return $count;
}

function zp_suite_save_lead($data){
  $leads = zp_suite_leads_all();
  $id = 'lead_' . gmdate('Ymd_His') . '_' . wp_generate_password(6, false, false);
  $lead = array_merge([
    'id' => $id,
    'created_at' => current_time('mysql'),
    'status' => 'new',
    'note' => '',
    'ip' => isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : '',
    'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_USER_AGENT'])) : '',
  ], $data);
  array_unshift($leads, $lead);
  $leads = array_slice($leads, 0, 500);
  update_option('zp_suite_leads', $leads, false);
  return $lead;
}


function zp_suite_post_text($key, $default = '') {
  return isset($_POST[$key]) ? sanitize_text_field(wp_unslash($_POST[$key])) : $default;
}

function zp_suite_post_textarea($key, $default = '') {
  return isset($_POST[$key]) ? sanitize_textarea_field(wp_unslash($_POST[$key])) : $default;
}

function zp_suite_post_services(){
  if (isset($_POST['services']) && is_array($_POST['services'])) {
    $items = array_map(function($v){ return sanitize_text_field(wp_unslash($v)); }, $_POST['services']);
    return implode(', ', array_filter($items));
  }
  if (isset($_POST['services[]']) && is_array($_POST['services[]'])) {
    $items = array_map(function($v){ return sanitize_text_field(wp_unslash($v)); }, $_POST['services[]']);
    return implode(', ', array_filter($items));
  }
  return isset($_POST['services']) ? sanitize_text_field(wp_unslash($_POST['services'])) : '';
}

function zp_suite_handle_uploads(&$errors){
  $attachments = [];
  $files_meta = [];
  if (empty($_FILES['files'])) { return [$attachments, $files_meta]; }
  if (!function_exists('wp_handle_upload')) { require_once ABSPATH . 'wp-admin/includes/file.php'; }
  $allowed = ['jpg','jpeg','png','webp','gif','pdf','doc','docx','xls','xlsx','ppt','pptx','zip','rar','7z','txt','svg','ai','eps'];
  $max_size = 12 * 1024 * 1024;
  $file_field = $_FILES['files'];
  $names = is_array($file_field['name']) ? $file_field['name'] : [$file_field['name']];
  $count = min(count($names), 8);
  for ($i=0; $i<$count; $i++) {
    $name = is_array($file_field['name']) ? $file_field['name'][$i] : $file_field['name'];
    if (!$name) { continue; }
    $tmp_name = is_array($file_field['tmp_name']) ? $file_field['tmp_name'][$i] : $file_field['tmp_name'];
    $error = is_array($file_field['error']) ? $file_field['error'][$i] : $file_field['error'];
    $size = is_array($file_field['size']) ? (int)$file_field['size'][$i] : (int)$file_field['size'];
    $type = is_array($file_field['type']) ? $file_field['type'][$i] : $file_field['type'];
    if ($error === UPLOAD_ERR_NO_FILE) { continue; }
    if ($error !== UPLOAD_ERR_OK) { $errors[] = 'Nie udało się wgrać pliku: ' . sanitize_file_name($name); continue; }
    if ($size > $max_size) { $errors[] = 'Plik jest za duży: ' . sanitize_file_name($name) . ' — maksymalnie 12 MB.'; continue; }
    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    if ($ext && !in_array($ext, $allowed, true)) { $errors[] = 'Niedozwolony format pliku: ' . sanitize_file_name($name); continue; }
    $file = ['name'=>$name, 'type'=>$type, 'tmp_name'=>$tmp_name, 'error'=>$error, 'size'=>$size];
    $move = wp_handle_upload($file, ['test_form'=>false]);
    if (!empty($move['error'])) { $errors[] = 'Nie udało się zapisać pliku: ' . sanitize_file_name($name); continue; }
    if (!empty($move['file'])) {
      $attachments[] = $move['file'];
      $files_meta[] = [
        'name' => sanitize_file_name($name),
        'url' => esc_url_raw($move['url'] ?? ''),
        'file' => $move['file'],
        'size' => $size,
        'type' => sanitize_text_field($type),
      ];
    }
  }
  return [$attachments, $files_meta];
}



function zp_suite_security_bool($key, $default = '0') {
  return (string) zp_suite_opt('security.' . $key, $default) === '1';
}

function zp_suite_turnstile_enabled() {
  return zp_suite_security_bool('turnstile_enabled', '0') && zp_suite_opt('security.turnstile_site_key', '') && zp_suite_opt('security.turnstile_secret_key', '');
}

function zp_suite_contact_security_errors() {
  return zp_suite_antispam_base_errors('contact');
}

function zp_suite_request_ip() {
  $keys = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
  foreach ($keys as $key) {
    if (empty($_SERVER[$key])) { continue; }
    $raw = sanitize_text_field(wp_unslash($_SERVER[$key]));
    $ip = trim(explode(',', $raw)[0]);
    if (filter_var($ip, FILTER_VALIDATE_IP)) { return $ip; }
  }
  return '0.0.0.0';
}

function zp_suite_antispam_base_errors($context = 'contact') {
  $errors = [];

  if (zp_suite_security_bool('honeypot_enabled', '1')) {
    $hp = isset($_POST['zp_hp_url']) ? trim((string) wp_unslash($_POST['zp_hp_url'])) : '';
    if ($hp !== '') {
      return ['Nie udało się wysłać formularza. Odśwież stronę i spróbuj ponownie.'];
    }
  }

  if (zp_suite_security_bool('min_seconds_enabled', '1')) {
    $min = max(0, (float) zp_suite_opt('security.min_seconds', '6'));
    $started = isset($_POST['zp_form_started']) ? (float) wp_unslash($_POST['zp_form_started']) : 0;
    if ($min > 0) {
      if ($started <= 0) {
        $errors[] = 'Nie udało się wysłać formularza. Odśwież stronę i spróbuj ponownie.';
      } else {
        $elapsed = (microtime(true) * 1000) - $started;
        if ($elapsed < ($min * 1000)) {
          $errors[] = 'Formularz został wysłany zbyt szybko. Spróbuj ponownie za chwilę.';
        }
      }
    }
  }

  $rate = zp_suite_antispam_rate_limit($context);
  if (is_wp_error($rate)) {
    $errors[] = $rate->get_error_message();
  }

  if (zp_suite_turnstile_enabled()) {
    $token = '';
    if (isset($_POST['cf-turnstile-response'])) {
      $token = sanitize_text_field(wp_unslash($_POST['cf-turnstile-response']));
    } elseif (isset($_POST['zp_turnstile_token'])) {
      $token = sanitize_text_field(wp_unslash($_POST['zp_turnstile_token']));
    }
    if (!$token) {
      /* v2.2.582: gdy skrypt Turnstile nie załadował się w przeglądarce (adblock, sieć),
         front raportuje zp_turnstile_status=frontend_unavailable. Przy włączonym fail-open
         nie blokujemy wysyłki — chronią nas honeypot, min-time, rate-limit i scoring treści. */
      $frontend_unavailable = isset($_POST['zp_turnstile_status']) && sanitize_text_field(wp_unslash($_POST['zp_turnstile_status'])) === 'frontend_unavailable';
      if (!$frontend_unavailable || !zp_suite_security_bool('turnstile_fail_open', '1')) {
        $errors[] = 'Nie udało się wysłać formularza. Odśwież stronę i spróbuj ponownie.';
      }
    } else {
      $verify = zp_suite_verify_turnstile($token);
      if (is_wp_error($verify)) {
        $frontend_ok = isset($_POST['zp_turnstile_status']) && sanitize_text_field(wp_unslash($_POST['zp_turnstile_status'])) === 'ok';
        if (!$frontend_ok && !zp_suite_security_bool('turnstile_fail_open', '1')) {
          $errors[] = $verify->get_error_message();
        }
      } elseif (!$verify) {
        $errors[] = 'Nie udało się potwierdzić, że formularz wysyła człowiek. Spróbuj ponownie.';
      }
    }
  }

  return $errors;
}

function zp_suite_antispam_rate_limit($context = 'contact') {
  $ip = zp_suite_request_ip();
  $key = 'zp_suite_rl_' . md5($context . '|' . $ip);
  $count = (int) get_transient($key);
  $max = ($context === 'studio') ? 20 : 30;
  if ($count >= $max) {
    return new WP_Error('zp_rate_limit', 'Wysłano zbyt wiele formularzy z tego połączenia. Spróbuj ponownie za kilka minut.');
  }
  set_transient($key, $count + 1, 15 * MINUTE_IN_SECONDS);
  return true;
}

function zp_suite_antispam_randomish($value) {
  $v = trim((string) $value);
  $len = strlen($v);
  if ($len < 8) { return false; }
  if (preg_match('/https?:\/\//i', $v)) { return false; }
  $letters = preg_replace('/[^A-Za-z]/', '', $v);
  if (strlen($letters) < 8) { return false; }
  $spaces = substr_count($v, ' ');
  $upper = preg_match_all('/[A-Z]/', $letters, $m1);
  $lower = preg_match_all('/[a-z]/', $letters, $m2);
  $vowels = preg_match_all('/[aeiouyAEIOUYąćęłńóśźżĄĆĘŁŃÓŚŹŻ]/u', $letters, $m3);
  $ratio_upper = strlen($letters) ? $upper / strlen($letters) : 0;
  $ratio_vowels = strlen($letters) ? $vowels / strlen($letters) : 0;
  if ($spaces === 0 && $len >= 10 && $upper > 2 && $lower > 2 && $ratio_upper > .18 && $ratio_upper < .82) { return true; }
  if ($spaces === 0 && $len >= 12 && $ratio_vowels < .18) { return true; }
  if (preg_match('/[A-Za-z]{14,}/', $v) && $spaces === 0) { return true; }
  return false;
}

function zp_suite_antispam_score_fields($fields) {
  $score = 0;
  $reasons = [];
  $name = (string)($fields['name'] ?? '');
  $company = (string)($fields['company'] ?? '');
  $message = (string)($fields['message'] ?? '');
  $website = (string)($fields['website'] ?? '');
  $services = (string)($fields['services'] ?? '');
  $email = (string)($fields['email'] ?? '');

  if (zp_suite_antispam_randomish($name)) { $score += 3; $reasons[] = 'losowe imię'; }
  if (zp_suite_antispam_randomish($company)) { $score += 3; $reasons[] = 'losowa firma'; }
  if (zp_suite_antispam_randomish($message)) { $score += 3; $reasons[] = 'losowy opis'; }
  if (strlen(trim($message)) > 0 && strlen(trim($message)) < 18) { $score += 2; $reasons[] = 'bardzo krótki opis'; }
  if ($website) {
    $host = wp_parse_url($website, PHP_URL_HOST);
    if (!$host || strpos($host, '.') === false || zp_suite_antispam_randomish((string)$host)) { $score += 4; $reasons[] = 'podejrzany link'; }
  }
  if ($services && substr_count($services, ',') >= 4) { $score += 1; $reasons[] = 'zaznaczono prawie wszystko'; }
  if ($email && preg_match('/@(mailinator|tempmail|guerrillamail|10minutemail|example)\./i', $email)) { $score += 4; $reasons[] = 'tymczasowy e-mail'; }

  return ['score' => $score, 'reasons' => $reasons];
}

function zp_suite_antispam_content_errors($fields, $threshold = 7) {
  $result = zp_suite_antispam_score_fields($fields);
  if ((int)$result['score'] >= $threshold) {
    return ['Nie udało się wysłać formularza. Odśwież stronę i spróbuj ponownie albo napisz bezpośrednio na kontakt@zaprojektowani.com.'];
  }
  return [];
}

function zp_suite_verify_turnstile($token) {
  $secret = zp_suite_opt('security.turnstile_secret_key', '');
  if (!$secret || !$token) { return false; }
  $remote_ip = isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : '';
  $body = [
    'secret' => $secret,
    'response' => $token,
  ];
  // remoteip is intentionally omitted because some hosts sit behind proxies/CDN and may pass a different IP than the browser challenge.
  $res = wp_remote_post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
    'timeout' => 8,
    'body' => $body,
  ]);
  if (is_wp_error($res)) {
    return new WP_Error('zp_turnstile_unavailable', 'Chwilowy problem z weryfikacją antyspamową. Spróbuj ponownie za moment.');
  }
  $code = (int) wp_remote_retrieve_response_code($res);
  $json = json_decode((string) wp_remote_retrieve_body($res), true);
  if ($code < 200 || $code >= 300 || !is_array($json)) {
    return new WP_Error('zp_turnstile_bad_response', 'Nie udało się sprawdzić zabezpieczenia antyspamowego. Spróbuj ponownie.');
  }
  return !empty($json['success']);
}

add_action('wp_footer', function(){
  if (is_admin()) { return; }
  $site_key = zp_suite_opt('security.turnstile_site_key', '');
  $enabled = zp_suite_turnstile_enabled();
  $min = max(0, (float) zp_suite_opt('security.min_seconds', '3'));
  ?>
  <script id="zp-suite-contact-security-574">
  (function(w,d){
    'use strict';
    var cfg={enabled:<?php echo $enabled ? 'true' : 'false'; ?>,siteKey:<?php echo wp_json_encode($site_key); ?>,minMs:<?php echo (int) round($min * 1000); ?>};
    var started=Date.now();
    w.zpSuiteFormStartedAt=w.zpSuiteFormStartedAt||started;
    var widgetId=null,pending=null,scriptLoading=false;
    function loadScript(){
      return new Promise(function(resolve,reject){
        if(!cfg.enabled||!cfg.siteKey){resolve();return;}
        if(w.turnstile&&typeof w.turnstile.render==='function'){resolve();return;}
        var existing=d.querySelector('script[src*="challenges.cloudflare.com/turnstile"]');
        if(existing){
          var done=false;
          var finish=function(ok){ if(done)return; done=true; ok?resolve():reject(new Error('turnstile-load')); };
          if(w.turnstile&&typeof w.turnstile.render==='function'){finish(true);return;}
          existing.addEventListener('load',function(){finish(true);},{once:true});
          existing.addEventListener('error',function(){finish(false);},{once:true});
          setTimeout(function(){ if(w.turnstile&&typeof w.turnstile.render==='function')finish(true); else finish(false); },5000);
          return;
        }
        if(scriptLoading){
          var startedWait=Date.now();
          var wait=function(){
            if(w.turnstile&&w.turnstile.render){resolve();return;}
            if(Date.now()-startedWait>5000){reject(new Error('turnstile-timeout-load'));return;}
            setTimeout(wait,60);
          };
          wait();return;
        }
        scriptLoading=true;
        var s=d.createElement('script');
        s.src='https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit';
        s.async=true; s.defer=true;
        s.onload=function(){resolve();}; s.onerror=function(){reject(new Error('turnstile-load'));};
        (d.head||d.documentElement).appendChild(s);
      });
    }
    function container(){
      var el=d.getElementById('zpTurnstileInvisible');
      if(!el){el=d.createElement('div');el.id='zpTurnstileInvisible';el.style.position='fixed';el.style.left='-9999px';el.style.bottom='0';el.style.width='1px';el.style.height='1px';el.style.overflow='hidden';d.body.appendChild(el);}
      return el;
    }
    function token(){
      if(!cfg.enabled||!cfg.siteKey){return Promise.resolve('');}
      return loadScript().then(function(){return new Promise(function(resolve,reject){
        if(!w.turnstile||typeof w.turnstile.render!=='function'){reject(new Error('turnstile-missing'));return;}
        pending={resolve:resolve,reject:reject};
        var opts={sitekey:cfg.siteKey,size:'invisible',execution:'execute',callback:function(t){var p=pending;pending=null; if(p)p.resolve(t||'');},'error-callback':function(){var p=pending;pending=null; if(p)p.reject(new Error('turnstile-error'));},'expired-callback':function(){try{w.turnstile.reset(widgetId);}catch(e){}}};
        try{
          if(widgetId===null){widgetId=w.turnstile.render(container(),opts);} else {w.turnstile.reset(widgetId);}
          w.turnstile.execute(widgetId);
        }catch(e){reject(e);}
        setTimeout(function(){if(pending){var p=pending;pending=null;p.reject(new Error('turnstile-timeout'));}},9000);
      });});
    }
    w.zpSuiteSecureFormData=function(fd){
      fd=fd||new FormData();
      if(!fd.has('zp_form_started')) fd.set('zp_form_started', String(w.zpSuiteFormStartedAt||Date.now()));
      if(!fd.has('zp_hp_url')) fd.set('zp_hp_url','');
      return token().then(function(t){ if(t){fd.set('cf-turnstile-response',t);fd.set('zp_turnstile_token',t);fd.set('zp_turnstile_status','ok');} return fd; }).catch(function(){ fd.set('zp_turnstile_status','frontend_unavailable'); return fd; });
    };
    /* v2.2.809: do not download Cloudflare Turnstile during initial page load.
       zpSuiteSecureFormData() loads it on demand when a form is actually submitted. */
  })(window,document);
  </script>
  <?php
}, 40);


function zp_suite_send_html_mail_resilient($recipients, $subject, $body, $headers = [], $attachments = []) {
  $recipients = is_array($recipients) ? $recipients : [$recipients];
  $recipients = array_values(array_unique(array_filter($recipients, 'is_email')));
  if (!$recipients) { return false; }

  $sent = wp_mail($recipients, $subject, $body, $headers, $attachments);
  if ($sent) { return true; }

  $single_ok = false;
  foreach ($recipients as $recipient) {
    if (wp_mail($recipient, $subject, $body, $headers, $attachments)) {
      $single_ok = true;
    }
  }
  return $single_ok;
}

function zp_suite_handle_contact() {
  check_ajax_referer('zp_suite_contact', 'nonce');

  $name = zp_suite_clean('name');
  $company = zp_suite_clean('company');
  $phone = zp_suite_clean('phone');
  $email = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
  $message = zp_suite_post_textarea('message');
  $contact_mode = zp_suite_clean('contact_mode');
  $mode_label = $contact_mode;
  $services = zp_suite_post_services();
  $budget = zp_suite_clean('budget');
  $deadline = zp_suite_clean('deadline');
  $website = isset($_POST['website']) ? esc_url_raw(wp_unslash($_POST['website'])) : '';
  $callback_time = zp_suite_clean('callback_time');
  $callback_topic = zp_suite_clean('callback_topic');
  $source = zp_suite_clean('source');

  $is_phone = in_array($contact_mode, ['phone','call','Prośba o oddzwonienie'], true);
  $is_brief = in_array($contact_mode, ['brief','Pełny brief projektu'], true);
  $is_quick = in_array($contact_mode, ['quick','Szybki kontakt','form','email',''], true);
  if ($contact_mode === 'phone') { $mode_label = 'Prośba o oddzwonienie'; }
  if ($contact_mode === 'brief') { $mode_label = 'Pełny brief projektu'; }
  if ($contact_mode === 'quick' || $contact_mode === 'form' || !$contact_mode) { $mode_label = 'Szybki kontakt'; }

  $errors = array_merge(zp_suite_contact_security_errors(), zp_suite_antispam_content_errors([
    'name' => $name,
    'company' => $company,
    'phone' => $phone,
    'email' => $email,
    'message' => $message,
    'website' => $website,
    'services' => $services,
  ], 7));
  if (mb_strlen($name) < 2) { $errors[] = 'Podaj imię lub nazwę firmy.'; }
  if ($is_phone) {
    if (mb_strlen($phone) < 5) { $errors[] = 'Podaj numer telefonu, na który mamy oddzwonić.'; }
    if (mb_strlen($message) < 3) { $message = 'Prośba o telefon z formularza kontaktowego.'; }
  } else {
    if (!$phone && !$email) { $errors[] = 'Podaj telefon albo adres e-mail, żebyśmy mogli wrócić z odpowiedzią.'; }
    if (mb_strlen($message) < 10) { $errors[] = 'Dopisz krótki opis projektu — wystarczy kilka zdań.'; }
  }
  if ($email && !is_email($email)) { $errors[] = 'Adres e-mail wygląda nieprawidłowo.'; }
  $consent = isset($_POST['consent']) ? (string) wp_unslash($_POST['consent']) : '';
  if (!$consent || !in_array($consent, ['1','on','true','yes'], true)) { $errors[] = 'Zaznacz zgodę na kontakt w sprawie zapytania.'; }

  list($attachments, $files_meta) = zp_suite_handle_uploads($errors);

  if ($errors) { wp_send_json_error(['title'=>zp_suite_opt('contact.error_title','Uzupełnij brakujące dane'), 'messages'=>$errors], 422); }

  $admin_email = zp_suite_opt('brand.admin_email', 'kontakt@zaprojektowani.com');
  if (!$admin_email || !is_email($admin_email)) { $admin_email = 'kontakt@zaprojektowani.com'; }
  $from_name = zp_suite_opt('brand.from_name', 'Zaprojektowani.com');
  $from_email = zp_suite_opt('brand.from_email', 'kontakt@zaprojektowani.com');
  if (!$from_email || !is_email($from_email)) { $from_email = $admin_email; }
  $logo = zp_suite_opt('brand.logo_dark', 'https://zaprojektowani.com/wp-content/uploads/2026/06/zp_sygnet_ciemny-scaled.webp');

  $subject = $is_phone ? zp_suite_opt('email_templates.admin_subject_phone','Prośba o telefon — Zaprojektowani.com') : zp_suite_opt('email_templates.admin_subject_form','Nowe zapytanie — Zaprojektowani.com');
  if ($is_brief) { $subject = 'Nowy pełny brief — Zaprojektowani.com'; }
  if ($is_quick && !$is_phone) { $subject = 'Szybkie zapytanie — Zaprojektowani.com'; }

  $file_links = '—';
  if ($files_meta) {
    $links = [];
    foreach ($files_meta as $f) {
      $links[] = '<a href="'.esc_url($f['url']).'">'.esc_html($f['name']).'</a>';
    }
    $file_links = implode('<br>', $links);
  }

  $admin_rows = [
    'Tryb kontaktu' => $mode_label ?: '—',
    'Imię / firma' => $name,
    'Firma' => $company ?: '—',
    'Telefon' => $phone ?: '—',
    'E-mail' => $email ?: '—',
    'Wybrane usługi' => $services ?: '—',
    'Budżet' => $budget ?: '—',
    'Termin' => $deadline ?: '—',
    'Link / inspiracje' => $website ? '<a href="'.esc_url($website).'">'.esc_html($website).'</a>' : '—',
    'Oddzwonić' => trim(($callback_time ?: '—') . ($callback_topic ? ' / ' . $callback_topic : '')),
    'Opis projektu' => nl2br(esc_html($message)),
    'Załączniki' => $file_links,
    'Źródło' => $source ?: 'Formularz kontaktowy',
  ];

  $admin_body = zp_suite_email_template('Nowe zapytanie ze strony', 'Ktoś wysłał formularz kontaktowy Zaprojektowani.com.', $admin_rows, $logo);

  $headers = ['Content-Type: text/html; charset=UTF-8', 'From: '.$from_name.' <'.$from_email.'>'];
  if ($email) { $headers[] = 'Reply-To: '.$name.' <'.$email.'>'; }

  $lead = zp_suite_save_lead([
    'name' => $name,
    'company' => $company,
    'phone' => $phone,
    'email' => $email,
    'services' => $services,
    'message' => $message,
    'budget' => $budget,
    'deadline' => $deadline,
    'website' => $website,
    'callback_time' => $callback_time,
    'callback_topic' => $callback_topic,
    'files' => $files_meta,
    'source' => $source ?: ($is_phone ? 'Prośba o telefon — kontakt' : 'Formularz kontaktowy — kontakt'),
    'contact_mode' => $mode_label ?: $contact_mode,
  ]);

  $admin_recipients = array_unique(array_filter([$admin_email, 'kontakt@zaprojektowani.com'], 'is_email'));
  $sent = zp_suite_send_html_mail_resilient($admin_recipients, $subject, $admin_body, $headers, $attachments);

  if ($email) {
    $client_body = zp_suite_email_template('Dziękujemy za wiadomość', zp_suite_opt('email_templates.client_intro','Otrzymaliśmy Twoje zapytanie. Wrócimy z odpowiedzią i propozycją dalszych kroków.'), [
      'Tryb kontaktu' => $mode_label ?: '—',
      'Wybrane usługi' => $services ?: '—',
      'Co dalej?' => 'Przejrzymy opis projektu i odezwiemy się możliwie szybko. Jeżeli sprawa jest pilna, możesz też odpisać na tę wiadomość albo zadzwonić pod numer 501 054 253.',
    ], $logo);
    wp_mail($email, zp_suite_opt('email_templates.client_subject','Potwierdzenie zapytania — Zaprojektowani.com'), $client_body, ['Content-Type: text/html; charset=UTF-8', 'From: '.$from_name.' <'.$from_email.'>']);
  }

  if (!$sent) { wp_send_json_error(['title'=>'Nie udało się wysłać wiadomości', 'messages'=>['Serwer pocztowy nie przyjął wiadomości. Spróbuj ponownie albo napisz na kontakt@zaprojektowani.com.']], 500); }
  wp_send_json_success([
    'title'=>($is_phone ? 'Przyjęliśmy prośbę o telefon.' : zp_suite_opt('contact.success_title','Dziękujemy, zapytanie zostało wysłane.')),
    'message'=>($is_phone ? 'Oddzwonimy możliwie szybko. Jeśli chcesz doprecyzować projekt, możesz też wysłać pełny formularz.' : zp_suite_opt('contact.success_text','Otrzymaliśmy wiadomość i wrócimy z konkretną odpowiedzią.')),
    'lead_id'=> isset($lead['id']) ? $lead['id'] : '',
    'source'=> $source ?: ($is_phone ? 'Prośba o telefon — kontakt' : 'Formularz kontaktowy — kontakt'),
    'contact_mode'=> $mode_label ?: $contact_mode,
  ]);
}

function zp_suite_email_template($title, $lead, $rows, $logo) {
  ob_start(); ?>
  <div style="margin:0;padding:0;background:#f4f6fb;font-family:Arial,Helvetica,sans-serif;color:#071426">
    <div style="max-width:720px;margin:0 auto;padding:34px 18px">
      <div style="background:linear-gradient(135deg,#05070b,#071426 54%,#102a4f);border-radius:28px 28px 0 0;padding:28px 30px;color:#fff">
        <img src="<?php echo esc_url($logo); ?>" alt="Zaprojektowani.com" style="max-width:172px;height:auto;display:block;margin:0 0 28px">
        <h1 style="margin:0;font-size:30px;line-height:1.05;letter-spacing:-.04em"><?php echo esc_html($title); ?></h1>
        <p style="margin:12px 0 0;color:rgba(255,255,255,.76);font-size:15px;line-height:1.55"><?php echo esc_html($lead); ?></p>
      </div>
      <div style="background:#fff;border:1px solid #e6e8ef;border-top:0;border-radius:0 0 28px 28px;padding:26px 30px">
        <?php foreach($rows as $label=>$value): ?>
          <div style="padding:14px 0;border-bottom:1px solid #edf0f5">
            <div style="font-size:11px;letter-spacing:.14em;text-transform:uppercase;font-weight:700;color:#6d7688;margin-bottom:7px"><?php echo esc_html($label); ?></div>
            <div style="font-size:16px;line-height:1.55;color:#071426"><?php echo wp_kses_post($value); ?></div>
          </div>
        <?php endforeach; ?>
        <p style="margin:22px 0 0;color:#778196;font-size:12px;line-height:1.5"><?php echo esc_html(zp_suite_opt('email_templates.email_footer','Wiadomość wygenerowana przez Zaprojektowani Suite.')); ?></p>
      </div>
    </div>
  </div>
  <?php return ob_get_clean();
}
