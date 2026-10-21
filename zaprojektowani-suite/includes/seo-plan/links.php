<?php
if (!defined('ABSPATH')) { exit; }

/**
 * Services related to the article, separate from the related-articles block.
 * Widget redesign (Mat, 21.10.2026, point 2): a light panel in the article's own style with one card per service in a grid
 * (two columns, three for three services, stacked on phones), nothing scrolls sideways
 * and every service has its own real project photo from the plugin library.
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
  $lead = trim((string) preg_replace('/\s+/u', ' ', wp_strip_all_tags((string) ($pages[$path]['hero']['lead'] ?? $entry['description'] ?? ''))));
  // Widget redesign 21.10.2026: the lead's whole first sentence instead of 31 words cut mid-phrase ("…układamy w jedną,…"), else the
  // industry tile's own card copy (industries.php) when that sentence runs long, else the old 31-word trim.
  $excerpt = preg_match('/^(.{40,220}?[.!?])(?=\s+\p{Lu}|$)/u', $lead, $match) ? $match[1] : '';
  if ($excerpt === '' && function_exists('zp_seo_industry_key') && function_exists('zp_seo_industries')) {
    $industry = zp_seo_industry_key($path);
    $excerpt = $industry !== '' ? (string) (zp_seo_industries()[$industry]['text'] ?? '') : '';
  }
  if ($excerpt === '') {
    $excerpt = wp_trim_words($lead !== '' ? $lead : 'Poznaj zakres usługi i zobacz, jak możemy pomóc w Twoim projekcie.', 31, '…');
  }
  return [
    'title' => (string) ($registry[$path]['title'] ?? $entry['h1'] ?? 'Poznaj naszą usługę'),
    'label' => 'Strony dla Twojej branży',
    'excerpt' => $excerpt,
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
  // 2.9.7: an industry page shows its cover photo, not the laptop its hero no longer has (industries.php).
  $industry = function_exists('zp_seo_industry_key') ? zp_seo_industry_key($path) : '';
  if ($industry !== '') { return zp_seo_industry_img($industry . '-cover-1100.webp'); }
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

/**
 * Widget redesign 21.10.2026: one real project photo per service, so two cards never share a cover (before, every
 * strony-kind service showed the same laptop and both logo services the same mock-up). Files are
 * the plugin's own article illustrations (1344x756); industry pages keep their cover photo.
 */
function zp_seo_related_service_image(string $path): array {
  $aliases = [
    '/strony-internetowe-katowice/' => '/tworzenie-stron-internetowych/',
    '/sklepy-internetowe-katowice/' => '/tworzenie-sklepow-internetowych/',
    '/logo-branding-katowice/' => '/projektowanie-logo/',
  ];
  $covers = [
    '/tworzenie-stron-internetowych/' => 'strona-krawca-z-umawianiem-wizyt.webp',
    '/tworzenie-sklepow-internetowych/' => 'sklep-bransoletka24-laptop.webp',
    '/projektowanie-logo/' => 'wersje-znaku-marki.webp',
    '/identyfikacja-wizualna/' => 'od-strategii-do-identyfikacji.webp',
    '/kampanie-reklamowe/' => 'key-visual-formaty.webp',
    '/strony-wordpress/' => 'strona-kancelarii-laptop.webp',
    '/opieka-wordpress/' => 'rozmowa-z-klientem-zespol.webp',
    '/tworzenie-landing-page/' => 'strona-docelowa-apartament-piekna.webp',
    '/strona-wizytowka/' => 'papeterio-laptop-telefon.webp',
  ];
  $key = $aliases[$path] ?? $path;
  if (isset($covers[$key])) {
    return ['image' => ZP_SUITE_URL . 'assets/img/wpisy/' . $covers[$key], 'width' => 1344, 'height' => 756];
  }
  $industry = function_exists('zp_seo_industry_key') ? zp_seo_industry_key($path) : '';
  if ($industry !== '') {
    return ['image' => zp_seo_industry_img($industry . '-cover-1100.webp'), 'width' => 1100, 'height' => 880];
  }
  return ['image' => zp_seo_related_fallback_image($path), 'width' => 0, 'height' => 0];
}

function zp_seo_related_card_meta(string $path): array {
  return zp_seo_related_service_profile($path) + zp_seo_related_service_image($path);
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
    if (count($seen) >= 4) { break; }
    $path = (string) ($link['path'] ?? '');
    if ($path === '' || $path === $self || isset($seen[$path]) || !zp_seo_related_is_service($path) || !zp_seo_plan_link_is_live($path)) { continue; }
    $seen[$path] = true;
    $meta = zp_seo_related_card_meta($path);
    $size = ($meta['width'] > 0 && $meta['height'] > 0) ? ' width="' . (int) $meta['width'] . '" height="' . (int) $meta['height'] . '"' : '';
    // No aria-label (SearchFit 14.10.2026): the card's name is its visible text (tag, title, snippet,
    // "Poznaj usługę"), so the name a voice user reads out always matches what is on screen.
    $cards .= '<a class="zpu-related__card" href="' . esc_url(home_url($path)) . '">'
      . '<span class="zpu-related__media"><img src="' . esc_url($meta['image']) . '"' . $size . ' alt="" loading="lazy" decoding="async"><span class="zpu-related__tag">' . esc_html($meta['label']) . '</span></span>'
      . '<div class="zpu-related__body"><p class="zpu-related__name">' . esc_html($meta['title']) . '</p>'
      . '<p class="zpu-related__snippet">' . esc_html($meta['excerpt']) . '</p>'
      . '<span class="zpu-related__go">Poznaj usługę <span aria-hidden="true">↗</span></span></div></a>';
  }
  if ($cards === '') { return ''; }
  // Audit of thread 31 (21.10.2026): the box is navigation, not article content, so its title and the card names are
  // paragraphs styled as headings and no longer add an H2 and H3s to every post's outline. The section is still
  // named by its visible title (aria-labelledby), so the English page needs no extra attribute text.
  return '<section class="zpu-related zpu-related--v2 zpu-related--cards-' . count($seen) . '" aria-labelledby="zpu-related-title">'
    . '<header class="zpu-related__head"><div><p class="zpu-related__eyebrow">Od wiedzy do działania</p><p class="zpu-related__title" id="zpu-related-title">Usługi, które mogą Cię zainteresować</p></div>'
    . '<p class="zpu-related__lead">Chcesz wykorzystać te pomysły w swojej firmie? Zobacz, w czym możemy Ci pomóc.</p></header>'
    . '<div class="zpu-related__grid">' . $cards . '</div></section>';
}

/**
 * Widget redesign 21.10.2026: light panel (#f5f6f8, 36 px radius, 1 px line) like the article's editorial boxes, white cards with a
 * dark navy pill button; full width of the article container, a full-bleed band on phones. Every declaration
 * carries !important because the theme and Elementor rules for links, headings and images otherwise win.
 * The first three rules (no sideways scrolling on posts) are unchanged from 2.7.7.
 */
add_action('wp_head', function () {
  if (is_admin() || !is_singular('post')) { return; }
  ?>
  <style id="zp-related-services">
  html,body.single-post{max-width:100%!important;overflow-x:hidden!important}
  @supports (overflow:clip){html,body.single-post{overflow-x:clip!important}}
  body.single-post .elementor,body.single-post .elementor-page,body.single-post .site,body.single-post #page{max-width:100%!important}
  .zpu-related{--zpu-ink:#07111f!important;--zpu-muted:#536075!important;--zpu-muted2:#7a8496!important;--zpu-line:rgba(7,17,31,.105)!important;--zpu-navy:#071426!important;--zpu-navy3:#102a4f!important;--zpu-ease:cubic-bezier(.22,1,.36,1)!important;box-sizing:border-box!important;position:relative!important;display:block!important;width:100%!important;max-width:1240px!important;margin:clamp(6px,1.5vw,20px) auto clamp(44px,5vw,76px)!important;padding:clamp(28px,3.6vw,54px) clamp(22px,3.4vw,50px) clamp(26px,3.2vw,50px)!important;border:1px solid var(--zpu-line)!important;border-radius:36px!important;background:#f5f6f8!important;color:var(--zpu-ink)!important;font-family:"Plus Jakarta Sans Local","Plus Jakarta Sans",system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif!important;line-height:1.6!important;text-align:left!important;overflow:visible!important;isolation:isolate!important;-webkit-font-smoothing:antialiased!important}
  .zpu-related:before,.zpu-related:after{content:none!important}
  .zpu-related *{box-sizing:border-box!important}
  .zpu-related__head{display:grid!important;grid-template-columns:minmax(0,1.5fr) minmax(240px,1fr)!important;gap:clamp(20px,3vw,48px)!important;align-items:end!important;margin:0 0 clamp(22px,2.8vw,34px)!important;padding:0!important}
  .zpu-related__head>div{min-width:0!important}
  .zpu-related__eyebrow{display:inline-flex!important;align-items:center!important;gap:9px!important;margin:0 0 14px!important;padding:0!important;color:var(--zpu-muted2)!important;font-size:11px!important;line-height:1!important;font-weight:850!important;letter-spacing:.16em!important;text-transform:uppercase!important}
  .zpu-related__eyebrow:before{content:""!important;display:block!important;width:28px!important;height:1px!important;border-radius:0!important;background:#071426!important;box-shadow:none!important}
  .zpu-related__title{display:block!important;max-width:none!important;margin:0!important;padding:0!important;color:var(--zpu-ink)!important;font-size:clamp(28px,3.1vw,44px)!important;line-height:1.06!important;letter-spacing:-.045em!important;font-weight:650!important;text-wrap:balance!important;overflow-wrap:break-word!important}
  .zpu-related__lead{display:block!important;max-width:420px!important;margin:0 0 4px!important;padding:0!important;color:var(--zpu-muted)!important;font-size:15px!important;line-height:1.7!important;letter-spacing:-.01em!important}
  .zpu-related__grid{display:grid!important;grid-template-columns:repeat(2,minmax(0,1fr))!important;gap:clamp(14px,1.8vw,22px)!important;margin:0!important;padding:0!important;overflow:visible!important}
  .zpu-related--cards-1 .zpu-related__grid{grid-template-columns:minmax(0,1fr)!important}
  .zpu-related--cards-3 .zpu-related__grid{grid-template-columns:repeat(3,minmax(0,1fr))!important}
  .zpu-related__card{position:relative!important;display:flex!important;flex-direction:column!important;min-width:0!important;margin:0!important;padding:0!important;overflow:hidden!important;border:1px solid rgba(7,17,31,.09)!important;border-radius:26px!important;background:#fff!important;color:var(--zpu-ink)!important;text-decoration:none!important;box-shadow:none!important;transition:border-color .28s var(--zpu-ease),transform .28s var(--zpu-ease)!important}
  .zpu-related__card:hover{border-color:rgba(16,42,79,.28)!important;transform:translateY(-3px)!important;color:var(--zpu-ink)!important;text-decoration:none!important}
  .zpu-related__card:focus-visible{outline:3px solid #102a4f!important;outline-offset:3px!important}
  .zpu-related__media{position:relative!important;display:block!important;width:100%!important;aspect-ratio:16/10!important;margin:0!important;overflow:hidden!important;background:#e8ecf2!important}
  .zpu-related__media:after{content:none!important}
  .zpu-related__media img{display:block!important;width:100%!important;height:100%!important;max-width:none!important;margin:0!important;padding:0!important;border:0!important;border-radius:0!important;box-shadow:none!important;object-fit:cover!important;object-position:center!important;transform:none!important;transition:transform .6s var(--zpu-ease)!important}
  .zpu-related__card:hover .zpu-related__media img{transform:scale(1.035)!important}
  .zpu-related__tag{position:absolute!important;z-index:2!important;left:16px!important;top:16px!important;display:inline-flex!important;align-items:center!important;max-width:calc(100% - 32px)!important;min-height:30px!important;margin:0!important;padding:8px 12px!important;border:1px solid rgba(7,17,31,.08)!important;border-radius:999px!important;background:rgba(255,255,255,.92)!important;color:var(--zpu-ink)!important;font-size:10px!important;line-height:1!important;font-weight:850!important;letter-spacing:.12em!important;text-transform:uppercase!important;white-space:nowrap!important;overflow:hidden!important;text-overflow:ellipsis!important;-webkit-backdrop-filter:blur(8px)!important;backdrop-filter:blur(8px)!important}
  .zpu-related__body{display:flex!important;flex:1 1 auto!important;flex-direction:column!important;align-items:flex-start!important;margin:0!important;padding:clamp(20px,2.1vw,28px)!important;text-align:left!important}
  .zpu-related__name{display:block!important;width:100%!important;max-width:none!important;margin:0 0 10px!important;padding:0!important;color:var(--zpu-ink)!important;font-size:clamp(21px,1.7vw,26px)!important;line-height:1.18!important;letter-spacing:-.03em!important;font-weight:650!important;text-transform:none!important;text-wrap:balance!important;overflow-wrap:break-word!important}
  .zpu-related__snippet{display:block!important;width:100%!important;max-width:none!important;margin:0 0 22px!important;padding:0!important;color:var(--zpu-muted)!important;font-size:15px!important;line-height:1.7!important;letter-spacing:-.01em!important}
  .zpu-related__go{display:inline-flex!important;align-items:center!important;justify-content:center!important;gap:12px!important;min-height:46px!important;margin:auto 0 0!important;padding:12px 18px 12px 20px!important;border:1px solid var(--zpu-navy)!important;border-radius:999px!important;background:var(--zpu-navy)!important;color:#fff!important;font-size:13px!important;line-height:1!important;font-weight:800!important;letter-spacing:-.01em!important;text-decoration:none!important;white-space:nowrap!important;transition:background .25s var(--zpu-ease),border-color .25s var(--zpu-ease)!important}
  .zpu-related__go>span{display:inline-block!important;font-size:16px!important;line-height:1!important;transition:transform .25s var(--zpu-ease)!important}
  .zpu-related__card:hover .zpu-related__go{background:var(--zpu-navy3)!important;border-color:var(--zpu-navy3)!important;color:#fff!important}
  .zpu-related__card:hover .zpu-related__go>span{transform:translate(2px,-2px)!important}
  @media(min-width:761px){.zpu-related--cards-1 .zpu-related__card{display:grid!important;grid-template-columns:minmax(0,1.15fr) minmax(0,1fr)!important;align-items:stretch!important}.zpu-related--cards-1 .zpu-related__media{height:100%!important;min-height:320px!important;aspect-ratio:auto!important}.zpu-related--cards-1 .zpu-related__body{justify-content:center!important;padding:clamp(28px,3vw,44px)!important}}
  @media(min-width:761px) and (max-width:1000px){.zpu-related--cards-3 .zpu-related__grid{grid-template-columns:minmax(0,1fr)!important}.zpu-related--cards-3 .zpu-related__card{display:grid!important;grid-template-columns:minmax(0,1fr) minmax(0,1.1fr)!important;align-items:stretch!important}.zpu-related--cards-3 .zpu-related__media{height:100%!important;min-height:240px!important;aspect-ratio:auto!important}.zpu-related--cards-3 .zpu-related__body{justify-content:center!important}}
  @media(max-width:1000px){.zpu-related__head{grid-template-columns:minmax(0,1fr)!important;gap:14px!important}.zpu-related__lead{max-width:560px!important}}
  @media(max-width:760px){.zpu-related{width:100%!important;max-width:100%!important;margin:0 0 40px!important;padding:30px 16px 28px!important;border-left:0!important;border-right:0!important;border-radius:0!important}.zpu-related__head{margin-bottom:20px!important}.zpu-related__eyebrow{font-size:10px!important;margin-bottom:12px!important}.zpu-related__title{font-size:clamp(27px,7.8vw,34px)!important;letter-spacing:-.04em!important}.zpu-related__lead{font-size:14.5px!important}.zpu-related__grid,.zpu-related--cards-3 .zpu-related__grid{grid-template-columns:minmax(0,1fr)!important;gap:14px!important}.zpu-related__card{border-radius:22px!important}.zpu-related__body{padding:18px 18px 20px!important}.zpu-related__name{font-size:22px!important}.zpu-related__snippet{font-size:14.5px!important;margin-bottom:18px!important}.zpu-related__tag{left:14px!important;top:14px!important}}
  @media(prefers-reduced-motion:reduce){.zpu-related__card,.zpu-related__media img,.zpu-related__go,.zpu-related__go>span{transition:none!important}.zpu-related__card:hover{transform:none!important}.zpu-related__card:hover .zpu-related__media img,.zpu-related__card:hover .zpu-related__go>span{transform:none!important}}
  </style>
  <?php
}, 90);

add_filter('the_content', function ($content) {
  if (!zp_seo_plan_active() || is_admin() || !is_singular('post') || !in_the_loop() || !is_main_query()) { return $content; }
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
