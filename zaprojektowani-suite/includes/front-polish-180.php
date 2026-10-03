<?php
if (!defined('ABSPATH')) { exit; }

/**
 * ZAPROJEKTOWANI SUITE v1.8.0
 * Front Polish & Performance Pack
 * - smooth loading without mobile performance degradation
 * - hero poster / video fallback
 * - smart image attributes + home image audit
 * - smooth anchors, reveal manager, Safari/iOS safe mode
 * - header lock, CLS guard, font polish, asset audit, Front Health
 */

function zp_suite_180_enabled($key, $default = '1'){
  return (string) zp_suite_opt('front_polish.' . $key, $default) !== '0';
}

function zp_suite_180_setting($key, $default = ''){
  return zp_suite_opt('front_polish.' . $key, $default);
}

add_action('wp_enqueue_scripts', function(){
  if (is_admin()) return;
  if (!zp_suite_180_enabled('enabled')) return;

  if (function_exists('zp_suite_enqueue_global_assets')) { zp_suite_enqueue_global_assets(); }

  wp_enqueue_style(
    'zp-suite-front-polish-180',
    ZP_SUITE_URL . 'assets/css/zp-front-polish-180.css',
    ['zp-suite-global'],
    zp_suite_asset_version('assets/css/zp-front-polish-180.css')
  );

  wp_enqueue_script(
    'zp-suite-front-polish-180',
    ZP_SUITE_URL . 'assets/js/zp-front-polish-180.js',
    ['zp-suite-global'],
    zp_suite_asset_version('assets/js/zp-front-polish-180.js'),
    true
  );

  wp_localize_script('zp-suite-front-polish-180', 'zpSuiteFrontPolish', [
    'smoothScroll' => zp_suite_180_enabled('smooth_scroll'),
    'reveal' => zp_suite_180_enabled('reveal_manager'),
    'safariSafe' => zp_suite_180_enabled('safari_safe'),
    'loader' => zp_suite_180_enabled('loader'),
    'videoStrategy' => sanitize_key(zp_suite_180_setting('video_strategy', 'idle')),
    'videoDelay' => max(0, (int) zp_suite_180_setting('video_delay', '900')),
    'headerOffset' => 92,
  ]);
}, 55);

add_action('wp_head', function(){
  if (is_admin()) return;
  if (!zp_suite_180_enabled('enabled')) return;

  $poster = esc_url(zp_suite_180_setting('hero_poster', ''));
  $font400 = esc_url('/wp-content/web-font/jakarta/plus-jakarta-sans-v12-latin_latin-ext-regular.woff2');
  $font600 = esc_url('/wp-content/web-font/jakarta/plus-jakarta-sans-v12-latin_latin-ext-600.woff2');
  $font700 = esc_url('/wp-content/web-font/jakarta/plus-jakarta-sans-v12-latin_latin-ext-700.woff2');

  echo "\n".'<script id="zp-front-polish-early">document.documentElement.classList.add("zp-loading");document.documentElement.classList.remove("zp-ready");</script>' . "\n";
  echo '<link rel="preload" href="'.$font600.'" as="font" type="font/woff2" crossorigin>' . "\n";
  echo '<link rel="preload" href="'.$font700.'" as="font" type="font/woff2" crossorigin>' . "\n";
  if ($poster && !(function_exists('is_front_page') && is_front_page())) {
    echo '<link rel="preload" href="'.$poster.'" as="image" fetchpriority="high">' . "\n";
  }

  $poster_css = $poster ? "url('" . esc_url($poster) . "')" : 'none';
  ?>
<style id="zp-front-polish-critical">
  :root{--zp-hero-poster:<?php echo $poster_css; ?>;--zp-safe-header-h:88px;scroll-padding-top:104px}
  html.zp-loading body{overflow-x:clip}
  .zpNewNav{min-height:88px;contain:layout style paint;}
  .zpNewNav__shell{min-height:88px;}
  .zpNewHero{min-height:440px;contain:layout paint style;}
  .zpNewHero__bg{background:#030509;background-image:var(--zp-hero-poster),linear-gradient(135deg,#020407 0%,#071426 48%,#102a4f 100%);background-size:cover;background-position:center;background-repeat:no-repeat;}
  .zpNewHero__inner{min-height:440px;}
  .zpNewHero__video:not([src]){opacity:0!important;}
  html.zp-loading .zpNewHero__copy,html.zp-loading .zpNewHero__visual{opacity:.001;transform:translate3d(0,10px,0)}
  html.zp-ready .zpNewHero__copy,html.zp-ready .zpNewHero__visual{opacity:1;transition:opacity .38s cubic-bezier(.16,1,.3,1),transform .38s cubic-bezier(.16,1,.3,1)}
  .zpFrontSkeleton{position:absolute;inset:0;pointer-events:none;z-index:3;opacity:0;transition:opacity .32s ease;background:radial-gradient(circle at 22% 18%,rgba(59,110,168,.18),transparent 34%),linear-gradient(90deg,rgba(3,5,9,.55),rgba(3,5,9,.1));}
  html.zp-loading .zpNewHero .zpFrontSkeleton{opacity:1}
  .zpSuitePreventCls img{height:auto;}
  @media(max-width:980px){.zpNewHero{min-height:540px}.zpNewHero__inner{min-height:540px}.zpNewNav,.zpNewNav__shell{min-height:74px}:root{scroll-padding-top:82px}}
</style>
  <?php
}, 0);

/**
 * HTML polish buffer: add image attrs, ALT fallbacks, hero poster and skeleton without editing each legacy template.
 */
add_action('template_redirect', function(){
  if (is_admin() || wp_doing_ajax() || is_feed()) return;
  if (!zp_suite_180_enabled('enabled')) return;
  ob_start('zp_suite_180_html_polish');
}, 1);

function zp_suite_180_html_polish($html){
  if (!is_string($html) || $html === '') return $html;

  $poster = esc_url(zp_suite_180_setting('hero_poster', ''));
  if ($poster && strpos($html, 'class="zpNewHero__video"') !== false && strpos($html, 'class="zpNewHero__video" poster=') === false) {
    $html = preg_replace('/(<video\b[^>]*class="[^"]*zpNewHero__video[^"]*"[^>]*)(>)/i', '$1 poster="'.$poster.'"$2', $html, 1);
  }

  if (strpos($html, 'class="zpNewHero__bg"') !== false && strpos($html, 'zpFrontSkeleton') === false) {
    $html = preg_replace('/(<div\s+class="zpNewHero__bg"[^>]*>)/i', '$1<span class="zpFrontSkeleton" aria-hidden="true"></span>', $html, 1);
  }

  if (zp_suite_180_enabled('smart_images')) {
    $img_index = 0;
    $html = preg_replace_callback('/<img\b[^>]*>/i', function($m) use (&$img_index){
      $tag = $m[0];
      $img_index++;
      if (stripos($tag, ' alt=') === false && function_exists('zp_seo_plan_active') && zp_seo_plan_active()) {
        // 2.3.0: media library alt or decorative alt="", never a file name.
        $src = preg_match('/src=["\']([^"\']+)["\']/i', $tag, $srcm) ? $srcm[1] : '';
        $tag = preg_replace('/<img\b/i', '<img alt="'.esc_attr(function_exists('zp_suite_seo_alt_library') ? zp_suite_seo_alt_library($src) : '').'"', $tag, 1);
      }
      if (stripos($tag, ' alt=') === false) {
        $alt = 'Zaprojektowani.com — projektowanie stron, sklepów i brandingu';
        if (preg_match('/src=["\']([^"\']+)["\']/i', $tag, $srcm)) {
          $name = basename(parse_url($srcm[1], PHP_URL_PATH) ?: '');
          $name = preg_replace('/\.(webp|png|jpe?g|gif|svg)$/i','',$name);
          $name = trim(str_replace(['-','_'], ' ', $name));
          if ($name) $alt = 'Zaprojektowani.com — ' . $name;
        }
        $tag = preg_replace('/<img\b/i', '<img alt="'.esc_attr($alt).'"', $tag, 1);
      }
      if (stripos($tag, ' decoding=') === false) {
        $tag = preg_replace('/<img\b/i', '<img decoding="async"', $tag, 1);
      }
      $above = (stripos($tag, 'zpNewHero__person') !== false || stripos($tag, 'zpNewNav__logo') !== false || $img_index <= 2);
      if (stripos($tag, ' loading=') === false) {
        $tag = preg_replace('/<img\b/i', '<img loading="'.($above?'eager':'lazy').'"', $tag, 1);
      }
      if ($above && stripos($tag, ' fetchpriority=') === false) {
        $tag = preg_replace('/<img\b/i', '<img fetchpriority="high"', $tag, 1);
      }
      return $tag;
    }, $html);
  }

  if (zp_suite_180_enabled('cls_guard') && stripos($html, '<body') !== false && stripos($html, 'zpSuitePreventCls') === false) {
    $html = preg_replace('/<body([^>]*)>/i', '<body$1 class="zpSuitePreventCls">', $html, 1);
  }

  return $html;
}

/** Admin menu and save for Front Polish. */
add_action('admin_menu', function(){
  add_submenu_page('zp-suite','Front Polish','Front Polish','manage_options','zp-suite-front-polish','zp_suite_180_render_front_polish_page');
});

add_action('admin_init', function(){
  if (!current_user_can('manage_options')) return;
  if (empty($_POST['zp_suite_180_save'])) return;
  check_admin_referer('zp_suite_180_save');
  $opts = zp_suite_options();
  $keys = ['enabled','loader','smooth_scroll','reveal_manager','safari_safe','cls_guard','smart_images','header_lock','asset_audit'];
  foreach($keys as $k){ $opts['front_polish'][$k] = '0'; }
  if (!empty($_POST['zp_front_polish']) && is_array($_POST['zp_front_polish'])) {
    $raw = wp_unslash($_POST['zp_front_polish']);
    foreach($raw as $k=>$v){
      $k = sanitize_key($k);
      if (!isset($opts['front_polish'][$k])) $opts['front_polish'][$k] = '';
      $opts['front_polish'][$k] = is_array($v) ? '' : sanitize_text_field($v);
    }
  }
  update_option('zp_suite_options', $opts, false);
  add_action('admin_notices', function(){ echo '<div class="notice notice-success"><p>Zapisano Front Polish & Performance Pack.</p></div>'; });
});

function zp_suite_180_used_images(){
  $urls = [];
  $opts = zp_suite_options();
  $cms = function_exists('zp_suite_cms') ? zp_suite_cms() : [];
  $paths = [
    'hero.person_url','hero.video_url','front_polish.hero_poster','brand.logo_light','brand.logo_dark','footer.team_photo'
  ];
  foreach($paths as $path){
    $val = zp_suite_opt($path, '');
    if ($val && preg_match('/\.(webp|png|jpe?g|gif|svg)(\?.*)?$/i', $val)) $urls[$val] = ['url'=>$val,'section'=>$path];
  }
  $walker = function($arr, $section='CMS') use (&$walker, &$urls){
    if (!is_array($arr)) return;
    foreach($arr as $k=>$v){
      if (is_array($v)) $walker($v, $section);
      elseif (is_string($v) && preg_match('~^https?://.*\.(webp|png|jpe?g|gif|svg)(\?.*)?$~i', $v)) $urls[$v] = ['url'=>$v,'section'=>$section];
    }
  };
  $walker($cms, 'CMS');
  return array_values($urls);
}

function zp_suite_180_speed_score(){
  if (function_exists('zp_suite_analytics_summary')) {
    $s = zp_suite_analytics_summary();
    if (!empty($s['speed_score'])) return (int)$s['speed_score'];
  }
  $rows = get_option('zp_suite_perf_events', []);
  if (!is_array($rows) || empty($rows)) return 82;
  $recent = array_slice($rows, -30);
  $load = 0; $size = 0; $n = 0;
  foreach($recent as $r){ $load += (float)($r['load']??0); $size += (float)($r['transfer']??0); $n++; }
  if (!$n) return 82;
  $avgLoad = $load/$n; $avgMb = ($size/$n)/1024/1024;
  $score = 100 - max(0,($avgLoad-1800)/40) - max(0,($avgMb-2.5)*8);
  return max(35, min(100, (int)round($score)));
}

function zp_suite_180_render_front_polish_page(){
  if (!current_user_can('manage_options')) return;
  $imgs = zp_suite_180_used_images();
  $score = zp_suite_180_speed_score();
  $poster = zp_suite_180_setting('hero_poster','');
  $strategy = sanitize_key(zp_suite_180_setting('video_strategy','idle'));
  ?>
  <div class="zpSuiteAdmin zpFrontPolishAdmin">
    <div class="zpSuiteHero">
      <span class="zpSuiteBadge">Front Polish v1.8.0</span>
      <h1>Front Polish & Performance Pack</h1>
      <p>Płynne ładowanie hero, video fallback, smart image loading, Safari safe mode, CLS guard, smooth anchors, audyt obrazów i Front Health — bez ograniczania efektów mobile.</p>
      <div class="zpStatus">
        <div class="zpStat"><strong><?php echo esc_html($score); ?>/100</strong><span>Front Health</span></div>
        <div class="zpStat"><strong><?php echo esc_html(count($imgs)); ?></strong><span>Obrazów w CMS</span></div>
        <div class="zpStat"><strong><?php echo zp_suite_180_enabled('smooth_scroll')?'ON':'OFF'; ?></strong><span>Smooth anchors</span></div>
      </div>
    </div>

    <form method="post" style="margin-top:18px">
      <?php wp_nonce_field('zp_suite_180_save'); ?>
      <input type="hidden" name="zp_suite_180_save" value="1">
      <div class="zpGrid">
        <div class="zpCard">
          <h2>Strategia hero/video</h2>
          <label class="zpField"><strong>Poster hero / fallback image</strong><div class="zpMediaRow"><input class="zpMediaInput" type="text" name="zp_front_polish[hero_poster]" value="<?php echo esc_attr($poster); ?>"><button class="button zpPickMedia" data-target="input[name=&quot;zp_front_polish[hero_poster]&quot;]">Wybierz</button></div><?php echo $poster?'<img class="zpPreviewImg" src="'.esc_url($poster).'" alt="">':''; ?><small>Statyczny kadr pokazany zanim odpali video. Najlepiej WebP 1600–1920px.</small></label>
          <label class="zpField"><strong>Video loading strategy</strong><select name="zp_front_polish[video_strategy]"><option value="idle" <?php selected($strategy,'idle'); ?>>Idle / po starcie strony</option><option value="visible" <?php selected($strategy,'visible'); ?>>Dopiero gdy hero jest widoczne</option><option value="interaction" <?php selected($strategy,'interaction'); ?>>Po pierwszej interakcji</option><option value="off" <?php selected($strategy,'off'); ?>>Wyłącz video, zostaw poster</option></select></label>
          <label class="zpField"><strong>Opóźnienie video / ms</strong><input type="number" name="zp_front_polish[video_delay]" value="<?php echo esc_attr(zp_suite_180_setting('video_delay','900')); ?>"></label>
        </div>
        <div class="zpCard">
          <h2>Przełączniki frontu</h2>
          <div class="zpSwitchGrid">
            <?php foreach([
              'enabled'=>'Front Polish aktywny','loader'=>'Smooth loading','smooth_scroll'=>'Smooth anchors','reveal_manager'=>'Reveal manager','safari_safe'=>'Safari/iOS safe mode','cls_guard'=>'CLS guard','smart_images'=>'Smart image loading','header_lock'=>'Header load lock','asset_audit'=>'Asset audit'
            ] as $k=>$label): ?>
              <label class="zpSwitch"><input type="checkbox" name="zp_front_polish[<?php echo esc_attr($k); ?>]" value="1" <?php checked(zp_suite_180_enabled($k)); ?>> <span><?php echo esc_html($label); ?></span></label>
            <?php endforeach; ?>
          </div>
          <p class="description">Nie dodajemy mobile performance mode — mobile zostaje wizualnie bez cięcia efektów.</p>
        </div>
      </div>
      <div class="zpCard">
        <h2>Home image audit</h2>
        <div class="zpTableWrap">
          <table class="widefat striped"><thead><tr><th>Obraz</th><th>Sekcja</th><th>Status</th><th>Rekomendacja</th></tr></thead><tbody>
          <?php foreach(array_slice($imgs,0,80) as $img):
            $url = $img['url']; $status = 'OK'; $rec = 'ALT/smart loading obsłużone przez Front Polish.';
            if (stripos($url,'.png')!==false) { $status='Do sprawdzenia'; $rec='Rozważ WebP/SVG, jeśli obraz jest duży.'; }
            if (stripos($url,'scaled')!==false) { $status='Średni'; $rec='Sprawdź, czy nie da się użyć mniejszej wersji WebP.'; }
          ?>
          <tr><td style="max-width:520px;word-break:break-all"><?php echo esc_html($url); ?></td><td><?php echo esc_html($img['section']); ?></td><td><strong><?php echo esc_html($status); ?></strong></td><td><?php echo esc_html($rec); ?></td></tr>
          <?php endforeach; ?>
          </tbody></table>
        </div>
      </div>
      <div class="zpSave"><button class="button button-primary">Zapisz Front Polish</button></div>
    </form>
  </div>
  <?php
}

/** Add Front Health card to Command Center if possible. */
add_action('admin_head', function(){
  $screen = function_exists('get_current_screen') ? get_current_screen() : null;
  if (!$screen || strpos($screen->id,'zp-suite') === false) return;
  ?>
  <style id="zp-front-polish-admin-css">.zpFrontPolishAdmin .widefat th{font-weight:900;color:#071426}.zpFrontPolishAdmin .widefat td{vertical-align:middle}.zpFrontPolishAdmin .zpTableWrap{max-height:520px;overflow:auto;border-radius:14px;border:1px solid #dfe4ec}.zpFrontPolishAdmin .zpTableWrap table{border:0}.zpFrontPolishAdmin .zpSave{display:flex;justify-content:flex-end}.zpFrontPolishAdmin select{width:100%;max-width:100%;border-radius:12px;border:1px solid #d9e0eb;padding:10px 12px;background:#f8fafc;min-height:42px}</style>
  <?php
});
