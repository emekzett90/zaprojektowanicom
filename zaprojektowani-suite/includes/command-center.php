<?php
if (!defined('ABSPATH')) { exit; }

/* Exit popup and Safe Mode settings plus the redirect list (option zp_suite_command_center).
 * The page-view, click, scroll and 404 counters were removed in the panel clean-up: nothing read them
 * any more, and every page view rewrote this option. */
function zp_suite_cc_defaults(){
  return [
    'redirects'=>[], 'settings'=>[
      'exit_popup_enabled'=>'1','exit_popup_delay'=>'8500','safe_mode'=>'0'
    ]
  ];
}
function zp_suite_cc_get(){ $d=get_option('zp_suite_command_center', []); return array_replace_recursive(zp_suite_cc_defaults(), is_array($d)?$d:[]); }
function zp_suite_cc_save($d){ update_option('zp_suite_command_center', $d, false); }
function zp_suite_cc_path(){ return isset($_SERVER['REQUEST_URI']) ? esc_url_raw(wp_unslash($_SERVER['REQUEST_URI'])) : '/'; }

add_action('template_redirect', function(){
  if (is_admin()) return;
  // Redirect manager first
  $d = zp_suite_cc_get();
  $req = trailingslashit(parse_url(zp_suite_cc_path(), PHP_URL_PATH) ?: '/');
  foreach (($d['redirects'] ?? []) as $i=>$r) {
    if (empty($r['from']) || empty($r['to']) || (string)($r['enabled'] ?? '1') === '0') continue;
    $from = trailingslashit('/'.trim((string)$r['from'], '/').'/');
    $target_url = home_url((string) $r['to']);
    $target_path = trailingslashit((string) (wp_parse_url($target_url, PHP_URL_PATH) ?: '/'));
    /* Nigdy nie przekierowuj adresu na dokładnie ten sam adres. */
    if ($req === $from && $req !== $target_path) {
      wp_safe_redirect($target_url, (int)($r['code'] ?? 301)); exit;
    }
  }
  // Auto SEO alt pass for raw template images.
  ob_start('zp_suite_seo_alt_buffer');
}, 0);

function zp_suite_seo_alt_from_src($src){
  $base = basename(parse_url($src, PHP_URL_PATH) ?: '');
  $base = preg_replace('/\.(webp|png|jpe?g|gif|svg)$/i','',$base);
  $base = preg_replace('/[-_]+/', ' ', $base);
  $base = preg_replace('/\bscaled\b|\bcopy\b|\bfinal\b|\bweb\b/i', '', $base);
  $base = trim(preg_replace('/\s+/', ' ', $base));
  if (!$base) $base = 'Zaprojektowani.com — projekt strony internetowej, sklepu lub brandingu';
  return 'Zaprojektowani.com — '.$base;
}
/**
 * 2.3.0: alt text comes from the media library; an image without one gets alt="" (decorative)
 * instead of a file name ("Zaprojektowani.com — crew mateusz"). An explicit empty alt stays empty.
 */
function zp_suite_seo_alt_library($src){
  static $cache = [];
  $src = (string) $src;
  if ($src === '' || stripos($src, '/wp-content/uploads/') === false) return '';
  if (!isset($cache[$src])) {
    $id = attachment_url_to_postid(preg_replace('/-\d+x\d+(?=\.[a-z0-9]+$)/i', '', strtok($src, '?')));
    $alt = $id ? trim((string) get_post_meta($id, '_wp_attachment_image_alt', true)) : '';
    $cache[$src] = $alt;
  }
  return $cache[$src];
}
function zp_suite_seo_alt_buffer($html){
  if (!is_string($html) || stripos($html, '<img') === false) return $html;
  if (function_exists('zp_seo_plan_active') && zp_seo_plan_active()) {
    return preg_replace_callback('/<img\b([^>]*?)>/i', function($m){
      $tag=$m[0];
      if (preg_match('/\salt\s*=/i', $tag)) return $tag;
      $src=''; if (preg_match('/\bsrc\s*=\s*(["\'])(.*?)\1/i', $tag, $sm)) $src=$sm[2];
      return preg_replace('/<img\b/i', '<img alt="'.esc_attr(zp_suite_seo_alt_library($src)).'"', $tag, 1);
    }, $html);
  }
  return preg_replace_callback('/<img\b([^>]*?)>/i', function($m){
    $tag=$m[0];
    if (preg_match('/\balt\s*=\s*(["\'])(.*?)\1/i', $tag, $am) && trim($am[2]) !== '') return $tag;
    $src=''; if (preg_match('/\bsrc\s*=\s*(["\'])(.*?)\1/i', $tag, $sm)) $src=$sm[2];
    $alt=esc_attr(zp_suite_seo_alt_from_src($src));
    if (preg_match('/\balt\s*=\s*(["\'])\s*\1/i', $tag)) return preg_replace('/\balt\s*=\s*(["\'])\s*\1/i', 'alt="'.$alt.'"', $tag, 1);
    return preg_replace('/<img\b/i', '<img alt="'.$alt.'"', $tag, 1);
  }, $html);
}


add_action('wp_head', function(){
  $d = zp_suite_cc_get();
  if ((string)($d['settings']['safe_mode'] ?? '0') !== '1') return;
  echo '<style id="zp-suite-safe-mode">html body [class*="zp"] *,html body [class*="zp"] *::before,html body [class*="zp"] *::after{animation-duration:.001ms!important;animation-iteration-count:1!important;transition-duration:.001ms!important;scroll-behavior:auto!important}html body [class*="zp"]{backdrop-filter: none;-webkit-backdrop-filter:none!important}</style>';
}, 3);

add_action('wp_footer', function(){
  if (is_admin()) return;
  $d=zp_suite_cc_get(); $s=$d['settings'] ?? [];
  $ajax=admin_url('admin-ajax.php'); $nonce=wp_create_nonce('zp_suite_contact');
  ?>
  <div class="zpExitConsult" id="zpExitConsult" aria-hidden="true">
    <div class="zpExitConsult__backdrop" data-zp-exit-close></div>
    <section class="zpExitConsult__box" role="dialog" aria-modal="true" aria-labelledby="zpExitConsultTitle">
      <button class="zpExitConsult__close" type="button" data-zp-exit-close aria-label="Zamknij">×</button>
      <span class="zpExitConsult__eyebrow">Bezpłatna konsultacja</span>
      <h2 id="zpExitConsultTitle">Zanim zamkniesz stronę — chcesz, żebyśmy oddzwonili?</h2>
      <p>Zostaw numer telefonu. Krótko dopytamy o projekt i podpowiemy, czy lepiej zacząć od strony, sklepu, brandingu, SEO czy kampanii.</p>
      <form class="zpExitConsult__form" data-zp-exit-form>
        <input type="hidden" name="action" value="zp_suite_contact"><input type="hidden" name="nonce" value="<?php echo esc_attr($nonce); ?>"><input type="hidden" name="contact_mode" value="phone"><input type="hidden" name="name" value="Popup — darmowa konsultacja"><input type="hidden" name="email" value=""><input type="hidden" name="services" value="Bezpłatna konsultacja / exit popup"><input type="hidden" name="message" value="Prośba o darmową konsultację przed wyjściem ze strony."><input type="hidden" name="consent" value="1">
        <label><span>Numer telefonu</span><input name="phone" type="tel" placeholder="np. 500 600 700" required></label>
        <button type="submit">Poproś o telefon</button>
      </form>
      <small>Bez spamu. Jeden krótki kontakt w sprawie Twojego projektu.</small>
    </section>
  </div>
  <style id="zp-exit-consult-css">.zpExitConsult,.zpExitConsult *{box-sizing:border-box}.zpExitConsult{position:fixed;inset:0;z-index:999999;display:none;font-family:var(--zp-font,"Plus Jakarta Sans Local",system-ui,sans-serif)}.zpExitConsult.is-open{display:block}.zpExitConsult__backdrop{position:absolute;inset:0;background:rgba(3,5,9,.42);backdrop-filter: none;-webkit-backdrop-filter:none}.zpExitConsult__box{position:absolute;left:50%;top:50%;width:min(520px,calc(100% - 28px));transform:translate(-50%,-50%);border-radius:28px;background:linear-gradient(180deg,#fff,#f7f9fc);border:1px solid rgba(7,20,38,.10);box-shadow:0 32px 90px rgba(7,20,38,.28);padding:28px;color:#071426;overflow:hidden}.zpExitConsult__box:before{content:"";position:absolute;inset:0 0 auto 0;height:5px;background:linear-gradient(90deg,#05070b,#102a4f,#1c477a)}.zpExitConsult__close{position:absolute;right:16px;top:14px;width:34px;height:34px;border:0;border-radius:999px;background:#edf2f7;color:#071426;font-size:22px;line-height:1;cursor:pointer}.zpExitConsult__eyebrow{display:inline-flex;margin-bottom:14px;text-transform:uppercase;letter-spacing:.14em;font-size:10px;font-weight:850;color:#1c477a}.zpExitConsult h2{margin:0;font-size:clamp(28px,5vw,42px);line-height:.98;letter-spacing:-.055em;color:#071426}.zpExitConsult p{margin:14px 0 20px;color:#5b6675;font-size:14px;line-height:1.58}.zpExitConsult__form{display:grid;grid-template-columns:1fr auto;gap:10px;align-items:end}.zpExitConsult__form label span{display:block;margin:0 0 8px;font-size:10px;text-transform:uppercase;letter-spacing:.12em;font-weight:850;color:#647084}.zpExitConsult__form input{width:100%;min-height:54px;border-radius:999px;border:1px solid #dfe5ee;background:#fff;padding:0 18px;font-weight:750;color:#071426}.zpExitConsult__form button{min-height:54px;border:0;border-radius:999px;background:linear-gradient(90deg,#05070b,#0b1830 42%,#102a4f 72%,#1c477a);color:#fff;padding:0 22px;font-weight:850;cursor:pointer;white-space:nowrap}.zpExitConsult small{display:block;margin-top:13px;color:#7a8494;font-size:11px}.zpExitConsult__msg{margin-top:12px;padding:12px 14px;border-radius:16px;background:#edf7ff;color:#102a4f;font-weight:800}@media(max-width:560px){.zpExitConsult__box{padding:24px 20px}.zpExitConsult__form{grid-template-columns:1fr}.zpExitConsult__form button{width:100%}}</style>
  <script id="zp-command-center-front-js">
  (function(){
    var ajax='<?php echo esc_js($ajax); ?>';
    var enabled='<?php echo esc_js($s['exit_popup_enabled'] ?? '1'); ?>'==='1'; if(enabled){
      var shown=sessionStorage.getItem('zpExitConsultShown')==='1'; var box=document.getElementById('zpExitConsult'); var form=box&&box.querySelector('[data-zp-exit-form]');
      /* 2.8.0: bez popupu po wysłanym zgłoszeniu i w trakcie wpisywania danych w formularzu na stronie. */
      function leadSent(){ try{ return sessionStorage.getItem('zpLeadSent')==='1'; }catch(e){ return false; } }
      function typing(){ var a=document.activeElement; return !!(a && a!==document.body && /^(INPUT|TEXTAREA|SELECT)$/.test(a.tagName) && !(box && box.contains(a))); }
      function openPopup(reason){ if(shown||!box||leadSent()) return; if(typing()){ if(reason==='time-delay'){ setTimeout(function(){ openPopup(reason); },15000); } return; } shown=true; sessionStorage.setItem('zpExitConsultShown','1'); box.classList.add('is-open'); box.setAttribute('aria-hidden','false'); setTimeout(function(){var i=box.querySelector('input[name="phone"]'); if(i) i.focus();},120); }
      function closePopup(){ if(!box) return; box.classList.remove('is-open'); box.setAttribute('aria-hidden','true'); }
      document.addEventListener('mouseleave',function(e){ if(e.clientY<=4) openPopup('exit-intent'); });
      setTimeout(function(){ if(!shown && (window.scrollY>500 || document.visibilityState==='visible')) openPopup('time-delay'); }, parseInt('<?php echo esc_js($s['exit_popup_delay'] ?? '8500'); ?>',10)||8500);
      document.addEventListener('click',function(e){ if(e.target.matches('[data-zp-exit-close]')) closePopup(); });
      if(form){ form.addEventListener('submit',function(e){ e.preventDefault(); var btn=form.querySelector('button'); if(btn) btn.disabled=true; (window.zpSuiteSecureFormData?window.zpSuiteSecureFormData(new FormData(form)):Promise.resolve(new FormData(form))).then(function(secureFd){return fetch(ajax,{method:'POST',body:secureFd,credentials:'same-origin'});}).then(function(r){return r.json()}).then(function(j){ var m=document.createElement('div'); m.className='zpExitConsult__msg'; m.textContent=(j&&j.success)?'Dziękujemy — przyjęliśmy prośbę o telefon.':'Sprawdź numer telefonu i spróbuj ponownie.'; form.appendChild(m); if(j&&j.success){setTimeout(closePopup,1800);} }).catch(function(){alert('Nie udało się wysłać. Spróbuj ponownie.');}).finally(function(){if(btn) btn.disabled=false;}); }); }
    }
  })();
  </script>
  <?php
}, 120);

/* ------------------------------------------------------------------------------------------------
 * Command Center: the ZP Suite start screen (new inquiries, module status, popup settings).
 * ---------------------------------------------------------------------------------------------- */

/** One-time clean-up after the stats removal: the old page-view and click data (nothing reads it any more)
 *  goes, so the option read on every page view is small again, and so does the old "unread orders" counter
 *  (the Zapytania badge counts status "Nowe"). Redirects and popup settings stay. */
add_action('admin_init', function () {
  if ((int) get_option('zp_suite_panel_cleanup', 0) >= 2) { return; }
  $cc = get_option('zp_suite_command_center', null);
  if (is_array($cc)) {
    unset($cc['events'], $cc['cta'], $cc['scroll'], $cc['sections'], $cc['404']);
    update_option('zp_suite_command_center', $cc, false);
  }
  delete_option('zp_suite_analytics');
  delete_option('zp_studio_wyceny_unread_orders');
  update_option('zp_suite_panel_cleanup', '2', false);
});

add_action('admin_post_zp_cc_settings', function () {
  if (!current_user_can('manage_options')) { wp_die('Brak uprawnień.', 403); }
  check_admin_referer('zp_cc_settings');
  $d = get_option('zp_suite_command_center', []);
  $d = is_array($d) ? $d : [];
  $s = isset($d['settings']) && is_array($d['settings']) ? $d['settings'] : [];
  $seconds = (float) str_replace(',', '.', sanitize_text_field(wp_unslash($_POST['exit_popup_delay'] ?? '8.5')));
  $s['exit_popup_enabled'] = empty($_POST['exit_popup_enabled']) ? '0' : '1';
  $s['exit_popup_delay'] = (string) (int) round(max(2, min(120, $seconds ?: 8.5)) * 1000);
  $s['safe_mode'] = empty($_POST['safe_mode']) ? '0' : '1';
  $d['settings'] = $s;
  update_option('zp_suite_command_center', $d, false);
  wp_safe_redirect(add_query_arg(['page' => 'zp-suite', 'zp_msg' => 'saved'], admin_url('admin.php')) . '#zpx-popup');
  exit;
});

/** Status of the modules that run by themselves, as rows: [name, state text, pill class, pill text, url]. */
function zp_suite_cc_modules(): array {
  $rows = [];

  if (function_exists('zp_feed_settings')) {
    $fs = zp_feed_settings();
    $next = get_posts(['post_type' => 'post', 'post_status' => 'future', 'posts_per_page' => 1, 'orderby' => 'date', 'order' => 'ASC', 'no_found_rows' => true, 'suppress_filters' => true]);
    $future = (int) (wp_count_posts('post')->future ?? 0);
    if (!$fs['enabled']) {
      $rows[] = ['Publikacja wpisów', 'Moduł wyłączony.', 'is-off', 'wyłączona', admin_url('admin.php?page=zp-suite-publikacja')];
    } elseif ($fs['paused']) {
      $rows[] = ['Publikacja wpisów', 'Wstrzymana: nowe artykuły nie wchodzą na stronę.', 'is-warn', 'pauza', admin_url('admin.php?page=zp-suite-publikacja')];
    } else {
      $text = $next ? 'Następny wpis ' . wp_date('j.m, H:i', get_post_timestamp($next[0])) . ': ' . get_the_title($next[0]) . '. Zaplanowanych: ' . $future . '.' : 'Brak zaplanowanych wpisów.';
      $rows[] = ['Publikacja wpisów', $text, 'is-ok', 'działa', admin_url('admin.php?page=zp-suite-publikacja')];
    }
  }

  if (class_exists('\ZPTE\Settings') && method_exists('\ZPTE\Settings', 'key_source')) {
    $url = admin_url('admin.php?page=zp-tlumacz-en');
    if (\ZPTE\Settings::key_source() === '') {
      $rows[] = ['Tłumacz EN', 'Brak klucza OpenAI: nowe wpisy nie dostaną wersji angielskiej.', 'is-warn', 'wymaga klucza', $url];
    } else {
      $queued = class_exists('\ZPTE\Store') && method_exists('\ZPTE\Store', 'queued_count') ? (int) \ZPTE\Store::queued_count() : 0;
      $errors = class_exists('\ZPTE\Store') && method_exists('\ZPTE\Store', 'count_paths') ? (int) \ZPTE\Store::count_paths(['attention' => true]) : 0;
      $text = 'Tłumaczy nowe wpisy codziennie.' . ($queued ? ' W kolejce: ' . $queued . '.' : '') . ($errors ? ' Do sprawdzenia: ' . $errors . '.' : '');
      $rows[] = ['Tłumacz EN', $text, $errors ? 'is-warn' : 'is-ok', $errors ? 'sprawdź' : 'działa', $url];
    }
  }

  if (class_exists('\ZPL\Geo')) {
    $on = method_exists('\ZPL\Geo', 'settings') ? \ZPL\Geo::settings()['enabled'] : true;
    $stats = get_option(\ZPL\Geo::STATS, []);
    $stats = is_array($stats) ? $stats : [];
    $today = (int) ($stats[wp_date('Y-m-d')] ?? 0);
    $week = 0;
    for ($i = 0; $i < 7; $i++) { $week += (int) ($stats[wp_date('Y-m-d', time() - $i * DAY_IN_SECONDS)] ?? 0); }
    $text = $on ? 'Goście spoza Polski dostają wersję angielską. Przekierowania dziś: ' . $today . ', w 7 dni: ' . $week . '.' : 'Automatyczna wersja angielska dla gości z zagranicy jest wyłączona.';
    $rows[] = ['Języki PL/EN', $text, $on ? 'is-ok' : 'is-off', $on ? 'działa' : 'wyłączone', admin_url('admin.php?page=zpl-languages&tab=geo')];
  }

  if (function_exists('zp_ai_stats')) {
    $s = zp_ai_stats();
    $visits = 0;
    for ($i = 0; $i < 7; $i++) {
      foreach ((array) ($s['refs'][wp_date('Y-m-d', time() - $i * DAY_IN_SECONDS)] ?? []) as $n) { $visits += (int) $n; }
    }
    $rows[] = ['Widoczność AI', 'Wejścia z ChatGPT, Gemini, Claude i innych w 7 dni: ' . $visits . '.', 'is-ok', 'działa', admin_url('admin.php?page=zp-suite-ai')];
  }

  if (function_exists('zp_speed_enabled')) {
    $on = zp_speed_enabled();
    $rows[] = ['Przyspieszenie', $on ? 'Strona ładuje się w trybie przyspieszonym.' : 'Przyspieszenie wyłączone.', $on ? 'is-ok' : 'is-off', $on ? 'włączone' : 'wyłączone', admin_url('admin.php?page=zp-suite-speed')];
  }

  return $rows;
}

function zp_suite_render_command_center_page(){
  if (!current_user_can('manage_options')) { return; }
  $set = zp_suite_cc_get()['settings'];
  $inbox = function_exists('zp_inbox_items') ? zp_inbox_items() : null;
  $new = $week = 0;
  if (is_array($inbox)) {
    $since = current_time('timestamp') - 7 * DAY_IN_SECONDS;
    foreach ($inbox as $it) {
      if ($it['status'] === 'new') { $new++; }
      if ($it['ts'] >= $since) { $week++; }
    }
  }
  $future = (int) (wp_count_posts('post')->future ?? 0);
  $inbox_url = admin_url('admin.php?page=zp-suite-zapytania');
  ?>
  <div class="wrap zpx">
    <header class="zpxHead">
      <span class="zpxKicker">ZP Suite <?php echo esc_html(defined('ZP_SUITE_VERSION') ? ZP_SUITE_VERSION : ''); ?></span>
      <h1>Command Center</h1>
      <p>Nowe zapytania od klientów, stan modułów, które pracują same, i ustawienia popupu w jednym miejscu.</p>
      <div class="zpxStats">
        <?php if (is_array($inbox)) : ?>
          <a class="zpxStat<?php echo $new ? ' is-hot' : ''; ?>" href="<?php echo esc_url(add_query_arg('status', 'new', $inbox_url)); ?>"><strong><?php echo (int) $new; ?></strong><span>nowe zapytania</span></a>
          <a class="zpxStat" href="<?php echo esc_url($inbox_url); ?>"><strong><?php echo (int) $week; ?></strong><span>zapytania z 7 dni</span></a>
        <?php endif; ?>
        <a class="zpxStat" href="<?php echo esc_url(admin_url('edit.php?post_status=future&post_type=post')); ?>"><strong><?php echo (int) $future; ?></strong><span>zaplanowane wpisy</span></a>
        <a class="zpxStat" href="<?php echo esc_url(admin_url('admin.php?page=zp-suite-home-cms')); ?>"><strong>CMS</strong><span>strona główna</span></a>
      </div>
    </header>
    <hr class="wp-header-end">
    <?php zp_panel_notice(); ?>

    <div class="zpxGrid">
      <?php if (is_array($inbox)) : ?>
      <section class="zpxCard">
        <h2>Ostatnie zapytania</h2>
        <?php if (!$inbox) : ?>
          <div class="zpxEmpty">Jeszcze nikt nie napisał. Nowe zapytania z formularzy i ze Studio wyceny pojawią się tutaj.</div>
        <?php else : ?>
          <div class="zpxRows">
            <?php foreach (array_slice($inbox, 0, 6) as $it) : ?>
              <a class="zpxRow" href="<?php echo esc_url(zp_inbox_item_url($it)); ?>">
                <span><b><?php echo esc_html($it['name']); ?></b><small><?php echo esc_html(implode(' · ', array_filter([$it['kind'], $it['service']], 'strlen'))); ?></small></span>
                <span class="zpxRowEnd"><?php echo zp_inbox_status_pill($it['status']); ?><small><?php echo esc_html(zp_inbox_date($it['ts'])); ?></small></span>
              </a>
            <?php endforeach; ?>
          </div>
          <a class="zpxMore" href="<?php echo esc_url($inbox_url); ?>">Wszystkie zapytania →</a>
        <?php endif; ?>
      </section>
      <?php endif; ?>

      <section class="zpxCard">
        <h2>Moduły</h2>
        <div class="zpxRows">
          <?php foreach (zp_suite_cc_modules() as $m) : ?>
            <a class="zpxRow" href="<?php echo esc_url($m[4]); ?>">
              <span><b><?php echo esc_html($m[0]); ?></b><small><?php echo esc_html($m[1]); ?></small></span>
              <span class="zpxPill <?php echo esc_attr($m[2]); ?>"><?php echo esc_html($m[3]); ?></span>
            </a>
          <?php endforeach; ?>
        </div>
      </section>

      <section class="zpxCard" id="zpx-popup">
        <h2>Popup i tryb awaryjny</h2>
        <form class="zpxForm" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
          <input type="hidden" name="action" value="zp_cc_settings">
          <?php wp_nonce_field('zp_cc_settings'); ?>
          <label><input type="checkbox" name="exit_popup_enabled" value="1" <?php checked(($set['exit_popup_enabled'] ?? '1'), '1'); ?>><span>Popup „Zanim zamkniesz stronę” z prośbą o telefon<small>Pojawia się raz na wizytę, gdy gość chce wyjść albo po czasie podanym niżej.</small></span></label>
          <label class="zpxNum">Pokaż po (sekundach)<br><input type="number" name="exit_popup_delay" min="2" max="120" step="0.5" value="<?php echo esc_attr(rtrim(rtrim(number_format(max(2000, (int) ($set['exit_popup_delay'] ?? 8500)) / 1000, 1, '.', ''), '0'), '.')); ?>"></label>
          <label><input type="checkbox" name="safe_mode" value="1" <?php checked(($set['safe_mode'] ?? '0'), '1'); ?>><span>Tryb awaryjny<small>Wyłącza animacje na stronie. Włącz tylko wtedy, gdy coś się zacina; normalnie zostaw wyłączony.</small></span></label>
          <p><button class="button button-primary">Zapisz</button></p>
        </form>
      </section>
    </div>
  </div>
  <?php
}
