<?php
if (!defined('ABSPATH')) { exit; }

/**
 * Services related to the article, separate from the related-articles block.
 * Two image-led service cards are visible on desktop, with native horizontal scrolling
 * on small screens. Covers come from the linked service pages' actual hero renderers.
 *
 * Posts from the keyword plan link to the service page they support; other posts link
 * to the service page of their category. Targets that are not
 * published yet (pages planned for later phases) are skipped.
 */

function zp_seo_plan_category_service(int $post_id): ?array {
  $map = [
    'sklepy-internetowe' => ['tworzenie sklepów internetowych', '/tworzenie-sklepow-internetowych/'],
    'logo-branding' => ['projektowanie logo', '/projektowanie-logo/'],
    'strony-internetowe' => ['tworzenie stron internetowych', '/tworzenie-stron-internetowych/'],
    'strony-www' => ['tworzenie stron internetowych', '/tworzenie-stron-internetowych/'],
    'ux-cro-analityka' => ['projektowanie stron internetowych', '/tworzenie-stron-internetowych/'],
    'seo-i-konwersja' => ['tworzenie stron internetowych', '/tworzenie-stron-internetowych/'],
    'seo-content-marketing' => ['tworzenie stron internetowych', '/tworzenie-stron-internetowych/'],
    'meta-ads' => ['kampanie reklamowe Meta Ads i Google Ads', '/kampanie-reklamowe/'],
    'kampanie-reklamowe' => ['kampanie reklamowe Meta Ads i Google Ads', '/kampanie-reklamowe/'],
    'kampanie-reklamowe-meta-ads' => ['kampanie reklamowe Meta Ads i Google Ads', '/kampanie-reklamowe/'],
    'reklamy' => ['kampanie reklamowe Meta Ads i Google Ads', '/kampanie-reklamowe/'],
  ];
  foreach (wp_get_post_categories($post_id, ['fields' => 'slugs']) as $slug) {
    if (isset($map[$slug])) { return ['text' => $map[$slug][0], 'path' => $map[$slug][1]]; }
  }
  return null;
}

function zp_seo_plan_link_is_live(string $path): bool {
  static $cache = [];
  if (!isset($cache[$path])) {
    // Kept between page views by includes/speed-server.php (about 80 fewer queries per page).
    $known = function_exists('zp_speed_live_get') ? zp_speed_live_get($path) : null;
    if ($known === null) {
      $known = zp_seo_plan_published_path($path);
      if (function_exists('zp_speed_live_put')) { zp_speed_live_put($path, $known); }
    }
    $cache[$path] = $known;
  }
  return $cache[$path];
}

/** Resolve a public path to a post/page ID when WordPress knows one. */
function zp_seo_related_object_id(string $path): int {
  static $cache = [];
  if (isset($cache[$path])) { return $cache[$path]; }
  $id = (int) url_to_postid(home_url($path));
  if (!$id) {
    $obj = get_page_by_path(trim($path, '/'), OBJECT, ['post', 'page']);
    if ($obj instanceof WP_Post) { $id = (int) $obj->ID; }
  }
  return $cache[$path] = $id;
}

/** The service registry is the boundary: articles and utility pages never become offers. */
function zp_seo_related_is_service(string $path): bool {
  $registry = zp_seo_service_registry();
  return isset($registry[$path]) || $path === '/kampanie-reklamowe/';
}

/** Copy describes the deliverable, not a reading time or a generic article excerpt. */
function zp_seo_related_service_profile(string $path): array {
  $profiles = [
    '/tworzenie-stron-internetowych/' => ['Tworzenie stron internetowych', 'Strony WWW', 'Projektujemy i wdrażamy strony dopasowane do Twojej oferty. Łączymy UX/UI, WordPress, szybkość i SEO, by ułatwić klientom kontakt.'],
    '/tworzenie-sklepow-internetowych/' => ['Tworzenie sklepów internetowych', 'E-commerce', 'Sklep WooCommerce zaprojektowany pod Twoje produkty: od kategorii i kart produktów po wygodny koszyk, płatności, dostawy i integracje.'],
    '/projektowanie-logo/' => ['Projektowanie logo', 'Logo i branding', 'Tworzymy znak dopasowany do charakteru Twojej marki. Otrzymujesz przemyślane koncepcje, dopracowane warianty oraz pliki do druku i internetu.'],
    '/identyfikacja-wizualna/' => ['Identyfikacja wizualna', 'Wizerunek marki', 'Porządkujemy logo, kolory, typografię i materiały marki w spójny system, który działa na stronie, w social media i w druku.'],
    '/kampanie-reklamowe/' => ['Kampanie reklamowe', 'Meta Ads + Google Ads', 'Łączymy strategię, kreacje i pomiar konwersji. Prowadzimy kampanie, które kierują odbiorcę od reklamy do zapytania lub zakupu.'],
    '/strony-wordpress/' => ['Strony WordPress', 'WordPress', 'Indywidualny projekt i wygodny panel do samodzielnej edycji. Wdrażamy strony WordPress z myślą o szybkości, SEO i dalszym rozwoju.'],
    '/opieka-wordpress/' => ['Opieka WordPress', 'Wsparcie i rozwój', 'Dbamy o aktualizacje, kopie zapasowe, bezpieczeństwo i monitoring strony. Pomagamy też w zmianach treści oraz bieżącej obsłudze.'],
    '/tworzenie-landing-page/' => ['Tworzenie landing page', 'Strony do kampanii', 'Projektujemy stronę skupioną na jednej ofercie i konkretnym działaniu. Układ, treści i formularz prowadzą odbiorcę do kontaktu.'],
    '/strona-wizytowka/' => ['Strona wizytówka', 'Strona dla firmy', 'Czytelna prezentacja firmy, oferty i danych kontaktowych. Projektujemy lekką stronę, którą wygodnie przegląda się także na telefonie.'],
  ];
  $aliases = [
    '/strony-internetowe-katowice/' => '/tworzenie-stron-internetowych/',
    '/sklepy-internetowe-katowice/' => '/tworzenie-sklepow-internetowych/',
    '/logo-branding-katowice/' => '/projektowanie-logo/',
  ];
  $key = $aliases[$path] ?? $path;
  if (isset($profiles[$key])) {
    return ['title' => $profiles[$key][0], 'label' => $profiles[$key][1], 'excerpt' => $profiles[$key][2]];
  }
  $registry = zp_seo_service_registry();
  $pages = zp_seo_content_pages();
  $entry = zp_seo_plan_entry($path);
  return [
    'title' => (string) ($registry[$path]['title'] ?? $entry['h1'] ?? 'Poznaj naszą usługę'),
    'label' => 'Strony dla Twojej branży',
    'excerpt' => wp_trim_words(wp_strip_all_tags((string) ($pages[$path]['hero']['lead'] ?? $entry['description'] ?? 'Poznaj zakres usługi i zobacz, jak możemy pomóc w Twoim projekcie.')), 31, '…'),
  ];
}

/**
 * Reuse the destination's actual hero, including the national page overrides.
 * Bundled templates are read locally; visitors never wait for an HTTP page fetch.
 */
function zp_seo_related_fallback_image(string $path): string {
  if (function_exists('zp_seo_service_hero_image')) {
    $image = zp_seo_service_hero_image($path);
    if ($image !== '') { return $image; }
  }
  $registry = zp_seo_service_registry();
  $kind = $registry[$path]['kind'] ?? '';
  if ($path === '/kampanie-reklamowe/') { return ZP_SUITE_URL . 'assets/campaigns/team-hero.webp'; }
  if ($kind === 'logo' || $kind === 'sklepy') {
    $loader = $kind === 'logo' ? 'zp_suite_logo_branding_katowice_get_section' : 'zp_suite_shop_katowice_get_section';
    $hero = function_exists($loader) ? (string) $loader('hero') : '';
    if ($hero === '') {
      $template = ZP_SUITE_PATH . 'templates/' . ($kind === 'logo' ? 'logo-branding-katowice' : 'shop-katowice') . '/sections/hero.html';
      $hero = is_file($template) ? (string) file_get_contents($template) : '';
    }
    if (preg_match('~<img\b[^>]*\bclass=["\'][^"\']*\bzh__brandMock\b[^"\']*["\'][^>]*\bsrc=(["\'])(.*?)\1~is', $hero, $match)) {
      return (string) esc_url_raw(html_entity_decode($match[2], ENT_QUOTES, 'UTF-8'));
    }
  }
  return ZP_SUITE_URL . 'assets/strony-internetowe/hero-laptop.webp';
}

/** The service's visible hero takes precedence over its often unrelated SEO thumbnail. */
function zp_seo_related_card_meta(string $path): array {
  return zp_seo_related_service_profile($path) + ['image' => zp_seo_related_fallback_image($path)];
}

/**
 * Keep only published services, then add a closely related service if the plan has one.
 * Articles continue to be handled by the separate "Rozwiń ten temat" block below.
 */
function zp_seo_related_expand_links(array $links, int $post_id, string $self, int $target = 2): array {
  $target = max(1, min(4, $target));
  $out = [];
  $seen = [];
  $append = static function (string $path) use (&$out, &$seen, $self): void {
    if ($path === '' || $path === $self || isset($seen[$path]) || !zp_seo_related_is_service($path) || !zp_seo_plan_link_is_live($path)) { return; }
    $seen[$path] = true;
    $profile = zp_seo_related_service_profile($path);
    $out[] = ['path' => $path, 'text' => $profile['title']];
  };
  foreach ($links as $link) { $append((string) ($link['path'] ?? '')); }
  $category = zp_seo_plan_category_service($post_id);
  if ($category) { $append($category['path']); }

  $companions = [
    '/tworzenie-stron-internetowych/' => ['/strony-wordpress/', '/tworzenie-landing-page/'],
    '/strony-internetowe-katowice/' => ['/strony-wordpress/', '/opieka-wordpress/'],
    '/strony-wordpress/' => ['/tworzenie-stron-internetowych/', '/opieka-wordpress/'],
    '/opieka-wordpress/' => ['/strony-wordpress/', '/tworzenie-stron-internetowych/'],
    '/strona-wizytowka/' => ['/strony-wordpress/', '/tworzenie-stron-internetowych/'],
    '/tworzenie-sklepow-internetowych/' => ['/kampanie-reklamowe/', '/opieka-wordpress/'],
    '/sklepy-internetowe-katowice/' => ['/kampanie-reklamowe/', '/opieka-wordpress/'],
    '/projektowanie-logo/' => ['/identyfikacja-wizualna/', '/tworzenie-stron-internetowych/'],
    '/logo-branding-katowice/' => ['/identyfikacja-wizualna/', '/tworzenie-stron-internetowych/'],
    '/identyfikacja-wizualna/' => ['/projektowanie-logo/', '/tworzenie-stron-internetowych/'],
    '/kampanie-reklamowe/' => ['/tworzenie-landing-page/', '/tworzenie-stron-internetowych/'],
    '/tworzenie-landing-page/' => ['/kampanie-reklamowe/', '/tworzenie-stron-internetowych/'],
  ];
  $primary = $out[0]['path'] ?? '';
  foreach ($companions[$primary] ?? [] as $path) {
    if (count($out) >= $target) { break; }
    $append($path);
  }
  return $out;
}

/** Shared renderer also keeps the legacy Ultimate SEO fallback service-only. */
function zp_seo_related_render(array $links, string $self = ''): string {
  $cards = '';
  $seen = [];
  foreach ($links as $link) {
    $path = (string) ($link['path'] ?? '');
    if ($path === '' || $path === $self || isset($seen[$path]) || !zp_seo_related_is_service($path) || !zp_seo_plan_link_is_live($path)) { continue; }
    $seen[$path] = true;
    $meta = zp_seo_related_card_meta($path);
    $cards .= '<a class="zpu-related__card" href="' . esc_url(home_url($path)) . '" aria-label="' . esc_attr('Poznaj usługę: ' . $meta['title']) . '">'
      . '<span class="zpu-related__media"><img src="' . esc_url($meta['image']) . '" alt="" loading="lazy" decoding="async"><span class="zpu-related__tag">' . esc_html($meta['label']) . '</span></span>'
      . '<div class="zpu-related__body"><h3>' . esc_html($meta['title']) . '</h3>'
      . '<p class="zpu-related__snippet">' . esc_html($meta['excerpt']) . '</p>'
      . '<span class="zpu-related__go">Poznaj usługę <span aria-hidden="true">↗</span></span></div></a>';
  }
  if ($cards === '') { return ''; }
  return '<section class="zpu-related' . (count($seen) === 1 ? ' zpu-related--single' : '') . '" aria-label="Usługi, które mogą Cię zainteresować">'
    . '<header class="zpu-related__head"><div><span class="zpu-related__eyebrow">Od wiedzy do działania</span><h2>Usługi, które mogą Cię zainteresować</h2></div>'
    . '<p>Chcesz wykorzystać te pomysły w swojej firmie? Zobacz, w czym możemy Ci pomóc.</p></header>'
    . '<div class="zpu-related__track">' . $cards . '</div></section>';
}

/** Single shared full-bleed background avoids the former central gradient's hard edges. */
add_action('wp_head', function () {
  if (is_admin() || !is_singular('post')) { return; }
  ?>
  <style id="zp-related-services">
  html,body.single-post{max-width:100%!important;overflow-x:hidden!important}
  @supports (overflow:clip){html,body.single-post{overflow-x:clip!important}}
  body.single-post .elementor,body.single-post .elementor-page,body.single-post .site,body.single-post #page{max-width:100%!important}
  .zpu-related{--zpu-ink:#f8fbff;--zpu-muted:#aebbd0;box-sizing:border-box!important;position:relative!important;width:min(1240px,calc(100% - 32px))!important;max-width:1240px!important;margin:clamp(58px,6vw,94px) auto clamp(46px,4.8vw,74px)!important;padding:clamp(40px,4.3vw,64px) clamp(10px,2.4vw,30px)!important;color:var(--zpu-ink)!important;font-family:"Plus Jakarta Sans Local","Plus Jakarta Sans",system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif!important;background:#061221!important;overflow:visible!important;isolation:isolate!important}
  .zpu-related:before{content:""!important;position:absolute!important;z-index:-1!important;inset:0!important;background:#061221!important;box-shadow:0 0 0 100vmax #061221!important;clip-path:inset(0 -100vmax)!important;pointer-events:none!important}
  .zpu-related:after{content:none!important}
  .zpu-related *{box-sizing:border-box!important}
  .zpu-related__head{position:relative!important;display:grid!important;grid-template-columns:minmax(0,1.45fr) minmax(220px,1fr)!important;gap:clamp(24px,4vw,60px)!important;align-items:end!important;margin:0 0 34px!important}
  .zpu-related__head>div{min-width:0!important;text-align:left!important}
  .zpu-related__eyebrow{display:inline-flex!important;align-items:center!important;gap:10px!important;margin:0 0 18px!important;color:#99b4d5!important;font-size:10px!important;line-height:1.3!important;font-weight:750!important;letter-spacing:.13em!important;text-transform:uppercase!important;text-align:left!important}
  .zpu-related__eyebrow:before{content:""!important;width:7px!important;height:7px!important;border-radius:50%!important;background:#8ec8f7!important;box-shadow:0 0 18px rgba(142,200,247,.4)!important}
  .zpu-related h2{margin:0!important;color:#fff!important;font-size:clamp(34px,3.8vw,52px)!important;line-height:1.06!important;letter-spacing:-.05em!important;font-weight:650!important;text-wrap:balance!important;text-align:left!important}
  .zpu-related__head p{margin:0 0 3px!important;color:var(--zpu-muted)!important;font-size:14px!important;line-height:1.75!important;text-align:left!important;max-width:360px!important}
  .zpu-related__track{position:relative!important;display:grid!important;grid-auto-flow:column!important;grid-auto-columns:calc((100% - 20px)/2)!important;gap:20px!important;overflow-x:auto!important;overflow-y:hidden!important;overscroll-behavior-x:contain!important;scroll-snap-type:x proximity!important;scrollbar-width:thin!important;scrollbar-color:rgba(198,215,239,.42) transparent!important;padding:3px 2px 16px!important}
  .zpu-related__track::-webkit-scrollbar{height:5px!important}.zpu-related__track::-webkit-scrollbar-thumb{background:#617692!important;border-radius:99px!important}
  .zpu-related__card{display:flex!important;flex-direction:column!important;min-width:0!important;overflow:hidden!important;scroll-snap-align:start!important;border:1px solid rgba(255,255,255,.13)!important;border-radius:24px!important;background:#0b1a2d!important;color:#fff!important;text-decoration:none!important;transition:border-color .25s ease,background .25s ease!important}
  .zpu-related__card:hover{border-color:rgba(142,200,247,.58)!important;background:#10233b!important}
  .zpu-related__card:focus-visible{outline:3px solid #8ec8f7!important;outline-offset:-3px!important}
  .zpu-related__media{position:relative!important;display:block!important;aspect-ratio:16/10!important;overflow:hidden!important;background:radial-gradient(ellipse at 65% 35%,#24486f,#091626 75%)!important}
  .zpu-related__media:after{content:""!important;position:absolute!important;inset:0!important;background:linear-gradient(180deg,transparent 60%,rgba(6,18,33,.7))!important;pointer-events:none!important}
  .zpu-related__media img{display:block!important;width:100%!important;height:100%!important;max-width:none!important;object-fit:cover!important;object-position:center!important;transform:scale(1.002)!important;transition:transform .55s cubic-bezier(.16,1,.3,1)!important}
  .zpu-related__card:hover .zpu-related__media img{transform:scale(1.04)!important}
  .zpu-related__tag{position:absolute!important;z-index:2!important;left:20px!important;bottom:18px!important;display:inline-flex!important;align-items:center!important;min-height:28px!important;max-width:calc(100% - 40px)!important;padding:5px 11px!important;border:1px solid rgba(255,255,255,.25)!important;border-radius:999px!important;background:rgba(6,18,33,.88)!important;color:#d6e7fb!important;font-size:10px!important;line-height:1.4!important;font-weight:700!important;letter-spacing:.04em!important}
  .zpu-related__body{display:flex!important;flex:1 1 auto!important;flex-direction:column!important;align-items:flex-start!important;padding:26px!important;text-align:left!important}
  .zpu-related__body h3{display:block!important;width:100%!important;margin:0!important;color:#fff!important;font-size:clamp(24px,2.3vw,30px)!important;line-height:1.14!important;letter-spacing:-.035em!important;font-weight:700!important;text-wrap:balance!important;text-align:left!important}
  .zpu-related__snippet{display:block!important;width:100%!important;margin:15px 0 26px!important;color:#b3c0d2!important;font-size:14px!important;line-height:1.7!important;text-align:left!important}
  .zpu-related__go{display:inline-flex!important;align-items:center!important;justify-content:space-between!important;gap:26px!important;min-height:44px!important;margin-top:auto!important;padding:12px 19px!important;border:1px solid rgba(255,255,255,.9)!important;border-radius:999px!important;background:#f4f8fd!important;color:#071426!important;font-size:12px!important;line-height:1.3!important;font-weight:800!important;text-decoration:none!important;transition:background .25s ease!important}
  .zpu-related__go>span{font-size:18px!important;line-height:1!important;transition:transform .25s ease!important}.zpu-related__card:hover .zpu-related__go{background:#cce8ff!important}.zpu-related__card:hover .zpu-related__go>span{transform:translate(2px,-2px)!important}
  .zpu-related--single .zpu-related__track{grid-auto-columns:100%!important}.zpu-related--single .zpu-related__card{display:grid!important;grid-template-columns:1fr 1fr!important}.zpu-related--single .zpu-related__media{height:100%!important;min-height:280px!important;aspect-ratio:auto!important}
  @media(max-width:980px){.zpu-related__track{grid-auto-columns:82%!important}.zpu-related__head{grid-template-columns:1fr!important;gap:18px!important}.zpu-related__head p{max-width:600px!important}.zpu-related h2{max-width:660px!important}}
  @media(max-width:620px){.zpu-related{width:calc(100% - 32px)!important;margin:42px 16px 46px!important;padding:36px 0 28px!important}.zpu-related__head{margin-bottom:26px!important}.zpu-related__eyebrow{font-size:9px!important;margin-bottom:14px!important}.zpu-related__track{grid-auto-columns:88%!important;gap:14px!important;padding-bottom:14px!important}.zpu-related__card{border-radius:20px!important}.zpu-related__body{padding:21px!important}.zpu-related__snippet{font-size:13px!important}.zpu-related__head p{font-size:13px!important}.zpu-related h2{font-size:clamp(30px,8.5vw,42px)!important}.zpu-related__body h3{font-size:24px!important}.zpu-related__tag{left:16px!important;bottom:14px!important}.zpu-related--single .zpu-related__card{display:flex!important}.zpu-related--single .zpu-related__media{height:auto!important;min-height:0!important;aspect-ratio:16/10!important}}
  @media(prefers-reduced-motion:reduce){.zpu-related__card,.zpu-related__media img,.zpu-related__go,.zpu-related__go>span{transition:none!important}.zpu-related__card:hover .zpu-related__media img,.zpu-related__card:hover .zpu-related__go>span{transform:none!important}.zpu-related__track{scroll-behavior:auto!important}}
  </style>
  <?php
}, 90);

add_filter('the_content', function ($content) {
  if (!zp_seo_plan_active() || is_admin() || zp_seo_plan_is_en() || !is_singular('post') || !in_the_loop() || !is_main_query()) { return $content; }
  if (strpos((string) $content, 'zpu-related') !== false) { return $content; }
  $id = get_the_ID();
  $self = zp_seo_plan_path((string) get_permalink($id));
  $entry = zp_seo_plan_entry($self);
  $links = zp_seo_related_expand_links($entry['links'] ?? [], (int) $id, $self, 2);
  $related = zp_seo_related_render($links, $self);
  return $related === '' ? $content : $content . $related;
}, 30);

function zp_seo_plan_ucfirst(string $text): string {
  if ($text === '') { return $text; }
  return mb_strtoupper(mb_substr($text, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr($text, 1, null, 'UTF-8');
}

/* ------------------------------------------------------------ site-wide links */

/** $path while the plan is on and the page is published, else $fallback (for templates). */
function zp_seo_plan_url(string $path, string $fallback): string {
  return (zp_seo_plan_active() && zp_seo_plan_link_is_live($path)) ? $path : $fallback;
}

/**
 * Slug => path for site-wide links: Katowice service pages => nationwide pages, and
 * industry pages planned for a later phase (linked from the home page, not published yet)
 * => the closest existing page. Each rule applies only while its target is published and,
 * for industry pages, only until the page itself is published.
 */
function zp_seo_plan_link_map(): array {
  static $map = null;
  if ($map === null) {
    $map = [];
    foreach (['strony-internetowe-katowice' => '/tworzenie-stron-internetowych/', 'sklepy-internetowe-katowice' => '/tworzenie-sklepow-internetowych/', 'logo-branding-katowice' => '/projektowanie-logo/'] as $slug => $to) {
      if (zp_seo_plan_link_is_live($to)) { $map[$slug] = $to; }
    }
    $later = [
      'strony-internetowe-dla-kancelarii' => '/tworzenie-stron-internetowych/',
      'strony-internetowe-dla-deweloperow' => '/tworzenie-stron-internetowych/',
      'strony-internetowe-dla-salonow-beauty' => '/sklepy-internetowe/strona-internetowa-dla-branzy-beauty-jakie-sekcje-zwiekszaja-zapytania/',
      'sklep-internetowy-dla-producenta' => '/tworzenie-sklepow-internetowych/',
    ];
    foreach ($later as $slug => $to) {
      if (!zp_seo_plan_link_is_live('/' . $slug . '/') && zp_seo_plan_link_is_live($to)) { $map[$slug] = $to; }
    }
  }
  return $map;
}

/**
 * Links to the Katowice service pages (menu, footer, home sections, blog posts, CMS
 * content) point to the nationwide pages, which now carry the main service phrases.
 * Links marked data-zp-local="1" stay on the local pages. Only <a href> is touched:
 * canonical, hreflang and structured data keep their own URLs.
 */
function zp_seo_plan_remap_html(string $html): string {
  $map = zp_seo_plan_link_map();
  if (!$map) { return $html; }
  $slugs = implode('|', array_map(static function ($s) { return preg_quote($s, '~'); }, array_keys($map)));
  // 2.8.0: a link whose text names Katowice or Śląsk (e.g. "strony internetowe Katowice" in a post)
  // keeps the local page, where that phrase is the keyword.
  $html = (string) preg_replace_callback('~<a\b(?![^>]*data-zp-local)([^>]*\shref=["\'](?:https?://(?:www\.)?zaprojektowani\.com)?/(?:' . $slugs . ')/[^>]*)>(.{0,400}?)</a>~is', static function ($m) {
    return preg_match('~katowic|śląsk|slask~iu', wp_strip_all_tags($m[2])) ? '<a data-zp-local="1"' . $m[1] . '>' . $m[2] . '</a>' : $m[0];
  }, $html);
  $href = '~(\shref=)(["\'])(?:https?://(?:www\.)?zaprojektowani\.com)?/(' . $slugs . ')/(#[^"\']*)?\2~i';
  $out = preg_replace_callback('~<a\b[^>]*>~i', static function ($m) use ($map, $href) {
    $tag = $m[0];
    if (stripos($tag, 'href') === false || stripos($tag, 'data-zp-local') !== false) { return $tag; }
    $new = preg_replace_callback($href, static function ($h) use ($map) {
      $slug = strtolower($h[3]);
      return isset($map[$slug]) ? $h[1] . $h[2] . esc_url(home_url($map[$slug])) . ($h[4] ?? '') . $h[2] : $h[0];
    }, $tag);
    return is_string($new) ? $new : $tag;
  }, $html);
  return is_string($out) ? $out : $html;
}

add_action('template_redirect', function () {
  if (!zp_seo_plan_active() || is_admin() || wp_doing_ajax() || is_feed() || is_preview() || zp_seo_plan_is_en() || isset($_GET['elementor-preview'])) { return; }
  if (!in_array($_SERVER['REQUEST_METHOD'] ?? 'GET', ['GET', 'HEAD'], true)) { return; }
  ob_start(static function ($html) {
    if (!is_string($html) || $html === '' || stripos($html, '<html') === false) { return $html; }
    foreach (headers_list() as $header) {
      if (stripos($header, 'Content-Type:') === 0 && stripos($header, 'text/html') === false) { return $html; }
    }
    return zp_seo_plan_remap_html($html);
  });
}, 2);

/*
 * 2.5.0: on the FAQ page, the answer about the scope of a visual identity links "key visual"
 * to the new article once it is live (the answers are stored in the FAQ editor, so the link
 * is added when the page is shown and disappears when the plan is paused).
 */
add_filter('zp_suite_faq_answer', function ($answer, $item) {
  if (!zp_seo_plan_active() || zp_seo_plan_is_en() || strpos((string) ($item['q'] ?? ''), 'Co może obejmować projekt identyfikacji wizualnej') !== 0) { return $answer; }
  $path = '/logo-branding/key-visual-co-to-jest/';
  if (stripos($answer, $path) !== false || !zp_seo_plan_link_is_live($path)) { return $answer; }
  return (string) preg_replace('~(?<![\w>/-])key visual(?![\w<])~u', '<a href="' . esc_url(home_url($path)) . '">key visual</a>', $answer, 1);
}, 10, 2);
