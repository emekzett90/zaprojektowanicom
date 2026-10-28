<?php
if (!defined('ABSPATH')) { exit; }

/**
 * Nationwide service pages and the local Katowice pages.
 *
 * The nationwide pages are copies of the Katowice pages (same shortcode, same Elementor
 * layout), so they get every stylesheet, script and fix the Katowice pages have. What
 * differs is decided here from the URL: H1, hero copy, headings, FAQ and links follow the
 * keyword plan, and the Katowice pages drop the sections that now live on the nationwide
 * pages. English Katowice pages (/en/…-katowice/) keep the original templates and translations;
 * the nationwide pages have their own English versions (2.8.0).
 */

/** Path => what the page shows. 'source' is the Katowice page a new page is copied from. */
function zp_seo_service_registry(): array {
  static $content = null;
  if ($content === null) {
    $content = [];
    foreach (zp_seo_content_pages() as $path => $page) {
      $content[$path] = ['kind' => 'strony', 'variant' => $page['variant'], 'source' => '/strony-internetowe-katowice/', 'title' => $page['title'], 'shortcode' => 'zp_strony_internetowe_katowice'];
    }
  }
  return $content + [
    '/tworzenie-stron-internetowych/'   => ['kind' => 'strony', 'variant' => 'strony-national',   'source' => '/strony-internetowe-katowice/', 'title' => 'Tworzenie stron internetowych', 'shortcode' => 'zp_strony_internetowe_katowice'],
    '/tworzenie-sklepow-internetowych/' => ['kind' => 'sklepy', 'variant' => 'sklepy-national',   'source' => '/sklepy-internetowe-katowice/', 'title' => 'Tworzenie sklepów internetowych', 'shortcode' => 'zp_sklepy_internetowe_katowice'],
    '/projektowanie-logo/'              => ['kind' => 'logo',   'variant' => 'logo-national',     'source' => '/logo-branding-katowice/',      'title' => 'Projektowanie logo', 'shortcode' => 'zp_logo_branding_katowice'],
    '/identyfikacja-wizualna/'          => ['kind' => 'logo',   'variant' => 'identity-national', 'source' => '/logo-branding-katowice/',      'title' => 'Identyfikacja wizualna', 'shortcode' => 'zp_logo_branding_katowice'],
    '/strony-internetowe-katowice/'     => ['kind' => 'strony', 'variant' => 'strony-local'],
    '/sklepy-internetowe-katowice/'     => ['kind' => 'sklepy', 'variant' => 'sklepy-local'],
    '/logo-branding-katowice/'          => ['kind' => 'logo',   'variant' => 'logo-local'],
  ];
}

/** Shortcodes that render a service template => its kind. */
function zp_seo_service_shortcodes(): array {
  return [
    'zp_strony_internetowe_katowice' => 'strony', 'zp_page_strony_katowice' => 'strony', 'zp_page_strony_internetowe_katowice' => 'strony',
    'zp_sklepy_internetowe_katowice' => 'sklepy', 'zp_page_sklepy_katowice' => 'sklepy',
    'zp_logo_branding_katowice' => 'logo', 'zp_page_logo_branding_katowice' => 'logo',
  ];
}

/**
 * Variant for the current request ('' = original template). English pages of the Katowice templates keep
 * the original; the nationwide and content pages have English versions of their own since 2.8.0
 * (zaprojektowani-languages routes them, e.g. /en/website-development/), rendered from the same variant.
 */
function zp_seo_service_variant(): string {
  if (!zp_seo_plan_active() || is_admin() || !did_action('wp')) { return ''; }
  $path = '';
  if (is_singular()) {
    $id = get_queried_object_id();
    if ($id) { $path = zp_seo_plan_path((string) get_permalink($id)); }
  }
  if ($path === '') { $path = zp_seo_plan_path(); }
  $reg = zp_seo_service_registry();
  // English requests: copied pages show their own content (translated by their page dictionary, or by
  // Tłumacz EN for pages newer than the shipped dictionary); the Katowice originals keep their templates.
  if (zp_seo_plan_is_en() && empty($reg[$path]['source'])) { return ''; }
  return $reg[$path]['variant'] ?? '';
}

add_filter('do_shortcode_tag', function ($output, $tag) {
  $kinds = zp_seo_service_shortcodes();
  if (!isset($kinds[$tag]) || !is_string($output) || $output === '') { return $output; }
  $variant = zp_seo_service_variant();
  if ($variant === '') { return $output; }
  $spec = zp_seo_service_spec($variant);
  if (!$spec || $spec['kind'] !== $kinds[$tag]) { return $output; }
  if (!empty($spec['content'])) {
    $html = zp_seo_content_transform($output, $spec['content']);
    // 2.9.0: a local content page (variant ending -local) keeps the #zasieg links on the Katowice pages.
    if (substr($variant, -6) === '-local') { $html = zp_seo_content_in_section($html, 'id="zasieg"', 'zp_seo_service_keep_local'); }
    return zp_seo_service_remap_links($html);
  }
  return zp_seo_service_transform($output, $spec, $variant);
}, 20, 2);

// The nationwide shop page is built from the bundled section files, not from the
// sections edited in the Katowice page's CMS, so its copy stays predictable.
add_filter('pre_option_zp_suite_shop_katowice_sections', function ($pre) {
  if (!did_action('template_redirect') || zp_seo_service_variant() !== 'sklepy-national') { return $pre; }
  return [];
});

add_action('wp_head', function () {
  if (!zp_seo_plan_active() || (zp_seo_service_variant() === '' && !is_singular('post') && !is_front_page())) { return; }
  echo '<style id="zp-seo-plan-230">'
    . '.zpSeoLink{color:inherit!important;font-weight:600;text-decoration:underline;text-decoration-thickness:1px;text-underline-offset:3px}'
    . '.zpSeoLink:hover{text-decoration-thickness:2px}'
    // Guide sections on the content pages: the template's wrapper is dark, light sections set their own background.
    . 'section.section.zpSeoPlanGuide{background:#fff}#proces~section.section.zpSeoPlanGuide{background:#f7f8fa}'
    . (zp_seo_guide_page() ? zp_seo_guide_css() : '')
    // 3.2.50-sklepy: only the shop page's guide folds (zp_seo_shop_guide_html).
    . (zp_seo_service_variant() === 'sklepy-national' ? zp_seo_guide_fold_css() : '')
    // Website pages: on phones the hero H1 was 120% wide and its first line ran past the screen edge.
    . (zp_suite_service_kind() === 'strony' ? '@media(max-width:680px){html body .hero h1{width:auto}}' : '')
    // 2.6.4: shop heroes, also from a hero saved in the Katowice page's CMS: the gradient phrase takes the
    // H1's line height (it had 1.14, so the lines around it stood further apart than the rest).
    . (zp_suite_service_kind() === 'sklepy' ? 'html body #zp-sklepy-internetowe-katowice #zhHero.zpShopHeroUnified h1.zh__title span.zh__grad{line-height:inherit!important}' : '')
    . '</style>' . "\n";
}, 40);

/* ------------------------------------------------------------------ transform */

function zp_seo_service_transform(string $html, array $spec, string $variant): string {
  foreach ($spec['drop'] ?? [] as $marker) { $html = zp_seo_html_drop_section($html, $marker); }
  if (!empty($spec['faq'])) {
    // 3.2.50-sklepy: the shop FAQ template has the website/logo <details> markup now. A Katowice FAQ
    // still saved in the CMS with the old <article> markup keeps the shop style: the style follows the markup.
    $faq_style = $spec['faq_style'] ?? 'details';
    if ($faq_style === 'shop' && strpos($html, 'class="zpFaqShopKatNavy__item') === false) { $faq_style = 'details'; }
    $html = zp_seo_faq_rebuild($html, $spec['faq'], $faq_style);
  }
  if (!empty($spec['h1'])) {
    $h1 = $spec['h1'];
    $html = preg_replace_callback('~(<h1\b[^>]*>)(.*?)(</h1>)~is', static function ($m) use ($h1) { return $m[1] . $h1 . $m[3]; }, $html, 1);
  }
  $missed = [];
  foreach ($spec['replace'] ?? [] as $pair) {
    $before = $html;
    $html = zp_seo_ws_replace($html, $pair[0], $pair[1]);
    if ($html === $before) { $missed[] = mb_substr($pair[0], 0, 70); }
  }
  foreach ($spec['regex'] ?? [] as $pair) {
    $new = preg_replace($pair[0], $pair[1], $html, 1, $count);
    if (is_string($new) && $count) { $html = $new; } else { $missed[] = $pair[0]; }
  }
  $html = zp_seo_service_remap_links($html);
  foreach ($spec['after'] ?? [] as $marker => $callback) {
    $insert = is_callable($callback) ? (string) call_user_func($callback) : '';
    if ($insert !== '') { $html = zp_seo_html_insert_after_section($html, $marker, $insert); }
  }
  // 'before': [marker of a section, callback, fallback marker to insert after when that section is missing].
  foreach ($spec['before'] ?? [] as $rule) {
    $insert = is_callable($rule[1]) ? (string) call_user_func($rule[1]) : '';
    if ($insert === '') { continue; }
    $range = zp_seo_html_section_range($html, $rule[0]);
    if ($range) { $html = substr($html, 0, $range[0]) . $insert . "\n" . substr($html, $range[0]); }
    elseif (!empty($rule[2])) { $html = zp_seo_html_insert_after_section($html, $rule[2], $insert); }
  }
  // Deliberate links to the local pages; data-zp-local keeps them out of the site-wide remap (links.php).
  $html = strtr($html, [
    'href="{{strony-katowice}}"' => 'data-zp-local="1" href="' . esc_url(home_url('/strony-internetowe-katowice/')) . '"',
    'href="{{sklepy-katowice}}"' => 'data-zp-local="1" href="' . esc_url(home_url('/sklepy-internetowe-katowice/')) . '"',
    'href="{{logo-katowice}}"'   => 'data-zp-local="1" href="' . esc_url(home_url('/logo-branding-katowice/')) . '"',
  ]);
  if ($missed && defined('WP_DEBUG') && WP_DEBUG) {
    error_log('[zp-seo-plan] ' . $variant . ': not found in template: ' . implode(' | ', $missed));
  }
  return zp_seo_service_sync_shop_faq($html, $variant);
}

/** Kept for callers; complete-document synchronization runs after the translator. */
function zp_seo_service_sync_shop_faq(string $html, string $variant): string { return $html; }

/** Early path gate only: never initialize Router caches or query/auth state here. */
function zp_seo_service_faq_candidate($uri): bool {
  if (!is_string($uri) || $uri === '' || $uri[0] !== '/' || substr($uri, 0, 2) === '//') { return false; }
  $path = zp_seo_plan_path($uri);
  $paths = array_fill_keys(array_merge(array_keys(zp_seo_plan_data('pages')), [
    '/tworzenie-stron-internetowych/', '/tworzenie-sklepow-internetowych/', '/projektowanie-logo/',
    '/identyfikacja-wizualna/', '/strony-internetowe-katowice/', '/sklepy-internetowe-katowice/', '/logo-branding-katowice/',
    '/kampanie-reklamowe/', '/',
  ]), true);
  if (isset($paths[$path])) { return true; }
  if (strpos($path, '/en/') !== 0) { return false; }
  $file = defined('ZPL_DIR') ? ZPL_DIR . 'data/routes.php' : '';
  $data = $file !== '' && is_file($file) ? (array) include $file : [];
  $routes = is_array($data['routes'] ?? null) ? $data['routes'] : [];
  $custom = get_option('zpl_routes', []);
  foreach ($paths as $pl => $_) {
    $en = is_array($custom) && is_string($custom[$pl] ?? null) ? $custom[$pl] : ($routes[$pl] ?? '/en' . $pl);
    if (is_string($en) && $en === $path) { return true; }
  }
  return false;
}

function zp_seo_service_faq_context(): bool {
  if (!did_action('wp') || !zp_seo_plan_active() || is_admin() || !is_singular('page') || is_404()
    || is_feed() || is_preview() || isset($_GET['elementor-preview'])
    || (defined('REST_REQUEST') && REST_REQUEST) || (function_exists('wp_doing_ajax') && wp_doing_ajax())
    || (function_exists('wp_doing_cron') && wp_doing_cron())) { return false; }
  $id = (int) get_queried_object_id();
  if (!$id || get_post_status($id) !== 'publish' || (string) get_post_field('post_password', $id) !== '') { return false; }
  $path = zp_seo_plan_path((string) get_permalink($id));
  return $path === '/' || $path === '/kampanie-reklamowe/' || isset(zp_seo_service_registry()[$path]);
}

/** Read text only; the source DOM/markup is never saved back to the document. */
function zp_seo_service_faq_plain(DOMNode $node): string {
  $copy = $node->cloneNode(true);
  $doc = new DOMDocument('1.0', 'UTF-8');
  $copy = $doc->importNode($copy, true); $doc->appendChild($copy);
  $xp = new DOMXPath($doc);
  foreach ($xp->query('//script|//style|//svg|//i|//*[contains(concat(" ",normalize-space(@class)," ")," faq-no ")]') as $remove) {
    if ($remove->parentNode) { $remove->parentNode->removeChild($remove); }
  }
  $value = (string) $doc->saveHTML($copy);
  $value = preg_replace('~</(?:p|div|li|ul|ol|h[1-6]|tr)\s*>|<br\b[^>]*>~i', ' ', $value);
  $value = html_entity_decode(wp_strip_all_tags((string) $value), ENT_QUOTES | ENT_HTML5, 'UTF-8');
  return trim((string) preg_replace('~[\s\x{00a0}]+~u', ' ', $value));
}

/** Only the actual template FAQ section, never unrelated sidebar/footer details. */
function zp_seo_service_faq_items(string $html): ?array {
  if (!class_exists('DOMDocument')) { return null; }
  if (!preg_match_all('~<section\b[^>]*\bid\s*=\s*(["\'])(faq|zpFaqShopKatNavy)\1[^>]*>~i', $html, $sections)) { return []; }
  if (count($sections[0]) !== 1) { return null; }
  $range = zp_seo_html_section_range($html, $sections[0][0]);
  if (!$range) { return null; }
  $fragment = substr($html, $range[0], $range[1] - $range[0]);
  if (strlen($fragment) > 120000) { return null; }
  $doc = new DOMDocument('1.0', 'UTF-8'); $previous = libxml_use_internal_errors(true);
  try { $loaded = $doc->loadHTML('<?xml encoding="UTF-8"?>' . $fragment, LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING); }
  finally { libxml_clear_errors(); libxml_use_internal_errors($previous); }
  if (!$loaded) { return null; }
  $xp = new DOMXPath($doc); $items = []; $seen = [];
  $blocks = $xp->query('//section[@id="faq"]//details[contains(concat(" ",normalize-space(@class)," ")," faq-item ")]'
    . '|//section[@id="zpFaqShopKatNavy"]//article[contains(concat(" ",normalize-space(@class)," ")," zpFaqShopKatNavy__item ")]');
  if (!$blocks || $blocks->length > 50) { return null; }
  foreach ($blocks as $block) {
    // Closed/collapsed answers are legitimate; hidden whole items/sidebars are not.
    if ($xp->query('ancestor-or-self::*[@hidden or @inert or translate(@aria-hidden,"TRUE","true")="true"]|ancestor::aside|ancestor::nav|ancestor::header|ancestor::footer', $block)->length) { continue; }
    $hidden = false;
    for ($parent = $block; $parent instanceof DOMElement; $parent = $parent->parentNode) {
      if (preg_match('~(?:display\s*:\s*none|visibility\s*:\s*hidden)~i', $parent->getAttribute('style'))) { $hidden = true; break; }
    }
    if ($hidden) { continue; }
    $shop = strtolower($block->nodeName) === 'article';
    $q = $xp->query($shop ? './button//strong' : './summary', $block)->item(0);
    $a = $xp->query('.//div[contains(concat(" ",normalize-space(@class)," ")," ' . ($shop ? 'zpFaqShopKatNavy__answer' : 'faq-answer') . ' ")]', $block)->item(0);
    if (!$q || !$a) { return null; }
    $question = zp_seo_service_faq_plain($q); $answer = zp_seo_service_faq_plain($a);
    if ($question === '' || $answer === '' || strlen($question) > 600 || strlen($answer) > 12000 || isset($seen[$question])) { return null; }
    $seen[$question] = true;
    $items[] = ['@type' => 'Question', 'name' => $question, 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $answer]];
  }
  return $items;
}

/** Preserve non-FAQ JSON-LD nodes, including object/array shape and multi-type nodes. */
function zp_seo_service_faq_prune($value, bool &$changed) {
  if ($value instanceof stdClass) {
    $type = $value->{'@type'} ?? null;
    $types = is_array($type) ? $type : [$type];
    $remaining = array_values(array_filter($types, static function ($t) { return !in_array($t, ['FAQPage', 'https://schema.org/FAQPage', 'http://schema.org/FAQPage'], true); }));
    if (count($remaining) !== count($types)) {
      $changed = true;
      if (!$remaining) { return null; }
      $value->{'@type'} = is_array($type) ? $remaining : $remaining[0];
    }
    foreach (get_object_vars($value) as $key => $part) {
      $new = zp_seo_service_faq_prune($part, $changed);
      if ($new === null && $part !== null) { unset($value->{$key}); } else { $value->{$key} = $new; }
    }
  } elseif (is_array($value)) {
    $out = [];
    foreach ($value as $part) {
      $new = zp_seo_service_faq_prune($part, $changed);
      if ($new !== null || $part === null) { $out[] = $new; }
    }
    $value = $out;
  }
  return $value;
}

/** Final HTML, after ZPL translation and all head/body emitters; no DOM reserialization. */
function zp_seo_service_faq_sync_document(string $html): string {
  if (strlen($html) > 8000000 || stripos($html, '<html') === false || stripos($html, '</head>') === false
    || stripos($html, '</body>') === false || !preg_match('//u', $html)) { return $html; }
  $items = zp_seo_service_faq_items($html);
  if ($items === null) { return $html; }
  $failed = false;
  $flags = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_PRESERVE_ZERO_FRACTION;
  $result = preg_replace_callback('~(<script\b(?:"[^"]*"|\'[^\']*\'|[^\'">])*>)(.*?)(</script\s*>)~is', static function ($m) use ($flags, &$failed) {
    if (!preg_match('~\stype\s*=\s*(["\'])application/ld\+json\1(?=\s|/?>)~i', $m[1]) ) { return $m[0]; }
    if (strlen($m[2]) > 1000000) { $failed = true; return $m[0]; }
    $data = json_decode($m[2], false, 64);
    if (json_last_error() !== JSON_ERROR_NONE) { if (stripos($m[2], 'FAQPage') !== false) { $failed = true; } return $m[0]; }
    $changed = false; $new = zp_seo_service_faq_prune($data, $changed);
    if (!$changed) { return $m[0]; }
    if ($new === null) { return ''; }
    $json = wp_json_encode($new, $flags);
    if (!is_string($json)) { $failed = true; return $m[0]; }
    return $m[1] . $json . $m[3];
  }, $html);
  if (!is_string($result) || $failed) { return $html; }
  if (!$items) { return $result; }
  $schema = wp_json_encode(['@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $items], $flags);
  if (!is_string($schema)) { return $html; }
  $at = strripos($result, '</body>');
  return substr($result, 0, $at) . '<script type="application/ld+json" id="zp-service-faq-schema">' . $schema . '</script>' . substr($result, $at);
}

function zp_seo_service_faq_document_buffer(string $chunk, int $phase): string {
  static $pending = ''; static $bypass = false;
  if ($phase & PHP_OUTPUT_HANDLER_CLEAN) { $pending = ''; $bypass = false; return ''; }
  if ($bypass) { if ($phase & PHP_OUTPUT_HANDLER_FINAL) { $bypass = false; } return $chunk; }
  $pending .= $chunk;
  if (strlen($pending) > 8000000) { $out = $pending; $pending = ''; $bypass = !($phase & PHP_OUTPUT_HANDLER_FINAL); return $out; }
  if (!($phase & PHP_OUTPUT_HANDLER_FINAL)) { return ''; }
  $html = $pending; $pending = '';
  if (!zp_seo_service_faq_context()) { return $html; }
  foreach (headers_list() as $header) {
    if ((stripos($header, 'Content-Type:') === 0 && stripos($header, 'text/html') === false)
      || stripos($header, 'Content-Encoding:') === 0) { return $html; }
  }
  try {
    $updated = apply_filters('zp_service_widgets_document', $html);
    // Home already owns FAQ microdata; retain it without introducing another FAQPage.
    $path = zp_seo_plan_path((string) get_permalink(get_queried_object_id()));
    return $path === '/' ? $updated : zp_seo_service_faq_sync_document($updated);
  } catch (Throwable $error) { return $html; }
}

// Included before plugins_loaded: outer to Runtime::boot (PHP_INT_MIN), so EN is already translated.
// The early gate is pure; actual public page/status checks occur only after WordPress queried the page.
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'GET' && !(defined('WP_CLI') && WP_CLI)
  && zp_seo_service_faq_candidate($_SERVER['REQUEST_URI'] ?? '') && empty($GLOBALS['zp_seo_service_faq_buffer'])) {
  $GLOBALS['zp_seo_service_faq_buffer'] = true;
  ob_start('zp_seo_service_faq_document_buffer');
}

/** Replace the first occurrence of $old, treating any run of whitespace as flexible. */
function zp_seo_ws_replace(string $html, string $old, string $new): string {
  $parts = preg_split('~\s+~u', trim($old));
  $re = '~' . implode('\s+', array_map(static function ($p) { return preg_quote($p, '~'); }, $parts)) . '~u';
  $out = preg_replace_callback($re, static function () use ($new) { return $new; }, $html, 1);
  return is_string($out) ? $out : $html;
}

/** Marks the links to the Katowice service pages as deliberate (data-zp-local), so no remap moves them. */
function zp_seo_service_keep_local(string $html): string {
  $out = preg_replace('~<a\b(?![^>]*data-zp-local)([^>]*\shref=["\'](?:https?://(?:www\.)?zaprojektowani\.com)?/(?:strony-internetowe-katowice|sklepy-internetowe-katowice|logo-branding-katowice)/)~i', '<a data-zp-local="1"$1', $html);
  return is_string($out) ? $out : $html;
}

/** Katowice service URLs inside the templates point to the nationwide pages (links marked data-zp-local stay). */
function zp_seo_service_remap_links(string $html): string {
  $map = [
    'strony-internetowe-katowice' => '/tworzenie-stron-internetowych/',
    'sklepy-internetowe-katowice' => '/tworzenie-sklepow-internetowych/',
    'logo-branding-katowice'      => '/projektowanie-logo/',
  ];
  $out = preg_replace_callback('~(?<!data-zp-local="1" )href=(["\'])(?:https?://(?:www\.)?zaprojektowani\.com)?/(strony-internetowe-katowice|sklepy-internetowe-katowice|logo-branding-katowice)/(#[^"\']*)?\1~i', static function ($m) use ($map) {
    return 'href=' . $m[1] . esc_url(home_url($map[strtolower($m[2])])) . ($m[3] ?? '') . $m[1];
  }, $html);
  return is_string($out) ? $out : $html;
}

/** [start, end) offsets of the first <section> whose opening tag contains $marker. */
function zp_seo_html_section_range(string $html, string $marker): ?array {
  $offset = 0;
  while (preg_match('~<section\b[^>]*>~i', $html, $m, PREG_OFFSET_CAPTURE, $offset)) {
    $tag = $m[0][0];
    $start = (int) $m[0][1];
    if (strpos($tag, $marker) === false) { $offset = $start + strlen($tag); continue; }
    $depth = 0;
    $pos = $start;
    while (preg_match('~<(/?)section\b[^>]*>~i', $html, $t, PREG_OFFSET_CAPTURE, $pos)) {
      $depth += $t[1][0] === '/' ? -1 : 1;
      $pos = (int) $t[0][1] + strlen($t[0][0]);
      if ($depth === 0) { return [$start, $pos]; }
    }
    return null;
  }
  return null;
}

function zp_seo_html_drop_section(string $html, string $marker): string {
  $range = zp_seo_html_section_range($html, $marker);
  return $range ? substr($html, 0, $range[0]) . substr($html, $range[1]) : $html;
}

function zp_seo_html_insert_after_section(string $html, string $marker, string $insert): string {
  $range = zp_seo_html_section_range($html, $marker);
  return $range ? substr($html, 0, $range[1]) . "\n" . $insert . substr($html, $range[1]) : $html;
}

/**
 * Rebuild a FAQ list. $items: ['keep', n] keeps the template's n-th question (1-based),
 * ['keep', n, sentence_html] keeps it and adds a sentence at the end of its answer (2.8.0;
 * an empty sentence changes nothing), ['new', question, answer_html] adds one. Numbers are
 * rewritten in order.
 */
function zp_seo_faq_rebuild(string $html, array $items, string $style): string {
  $re = $style === 'shop'
    ? '~<article class="zpFaqShopKatNavy__item[^"]*">.*?</article>~s'
    : '~<details class="faq-item">.*?</details>~s';
  if (!preg_match_all($re, $html, $m, PREG_OFFSET_CAPTURE) || !$m[0]) { return $html; }
  $blocks = array_map(static function ($x) { return $x[0]; }, $m[0]);
  $first = (int) $m[0][0][1];
  $lastBlock = end($m[0]);
  $end = (int) $lastBlock[1] + strlen($lastBlock[0]);
  $out = [];
  foreach ($items as $item) {
    if ($item[0] === 'keep' && isset($blocks[$item[1] - 1])) {
      $block = $blocks[$item[1] - 1];
      $add = (string) ($item[2] ?? '');
      if ($add !== '' && ($at = strrpos($block, '</p>')) !== false) { $block = rtrim(substr($block, 0, $at)) . ' ' . $add . substr($block, $at); }
      $out[] = $block;
    }
    if ($item[0] === 'new') { $out[] = zp_seo_faq_item($style, $item[1], $item[2]); }
  }
  $n = 0;
  foreach ($out as $i => $block) {
    $n++;
    $no = str_pad((string) $n, 2, '0', STR_PAD_LEFT);
    if ($style === 'shop') {
      $block = preg_replace('~(<button class="zpFaqShopKatNavy__question"[^>]*>\s*<span>)\d+(</span>)~', '${1}' . $no . '${2}', $block, 1);
      $block = preg_replace('~<article class="zpFaqShopKatNavy__item[^"]*">~', '<article class="zpFaqShopKatNavy__item' . ($i === 0 ? ' is-open' : '') . '">', $block, 1);
      $block = preg_replace('~aria-expanded="(?:true|false)"~', 'aria-expanded="' . ($i === 0 ? 'true' : 'false') . '"', $block, 1);
    } else {
      $block = preg_replace('~<span class="faq-no">\d+</span>~', '<span class="faq-no">' . $no . '</span>', $block, 1);
    }
    $out[$i] = $block;
  }
  $separator = $style === 'shop' ? "\n\n        " : "\n          ";
  return substr($html, 0, $first) . implode($separator, $out) . substr($html, $end);
}

function zp_seo_faq_item(string $style, string $question, string $answer): string {
  if ($style === 'shop') {
    return '<article class="zpFaqShopKatNavy__item"><button class="zpFaqShopKatNavy__question" type="button" aria-expanded="false"><span>00</span><strong>'
      . $question . '</strong><i data-lucide="plus"></i></button><div class="zpFaqShopKatNavy__answer"><p>' . $answer . '</p></div></article>';
  }
  return '<details class="faq-item"><summary><span class="faq-no">00</span><span>' . $question
    . '</span><i class="faq-plus"></i></summary><div class="faq-answer">' . $answer . '</div></details>';
}

/* --------------------------------------------------------- moved shop guide */

/**
 * The post /sklepy-internetowe/tworzenie-sklepow-internetowych-od-pomyslu-na-oferte-do-gotowego-sklepu-online/
 * (the strongest non-brand URL in Search Console) moves onto the nationwide shop page;
 * the post itself becomes a draft with a 301 here. Its text is read from the database.
 */
function zp_seo_shop_guide_html(): string {
  $id = (int) get_option('zp_seo_plan_shop_post', 0);
  $post = $id ? get_post($id) : null;
  if (!$post || $post->post_type !== 'post') { return ''; }
  $content = zp_seo_guide_post_source($post);
  if ($content === '') { return ''; }
  if (function_exists('has_blocks') && has_blocks($content)) { $content = do_blocks($content); }
  $content = zp_seo_guide_clean_post_html((string) $content);
  $content = preg_replace('~<(script|style|noscript|template|form|iframe)\b[^>]*>.*?</\1>~is', '', $content);
  $content = strip_shortcodes((string) $content);
  if (stripos($content, '<p') === false) { $content = wpautop($content); }
  $content = preg_replace('~<h1\b[^>]*>.*?</h1>~is', '', $content);
  // Attributes go in zp_seo_guide_clean_post_html() (the guide classes stay); this is the fallback without DOM.
  if (!class_exists('DOMDocument')) { $content = preg_replace('~\s(?:style|class|id|data-[a-z0-9_-]+)=("[^"]*"|\'[^\']*\')~i', '', (string) $content); }
  $content = wp_kses_post((string) $content);
  $content = preg_replace('~<p>\s*(?:&nbsp;)?\s*</p>~i', '', $content);
  $content = zp_seo_guide_strip_notes((string) $content);
  if (trim(wp_strip_all_tags((string) $content)) === '') { return ''; }
  $lead = 'Pełny przewodnik po tym, jak powstaje sklep: od modelu sprzedaży i oferty, przez strukturę kategorii i '
    . '<a class="zpSeoLink" href="' . esc_url(home_url('/sklepy-internetowe/karta-produktu-w-sklepie-internetowym/')) . '">kartę produktu</a>, '
    . 'po płatności, dostawy i start sprzedaży. Jeśli przenosisz istniejący sklep, przeczytaj o tym, jak wygląda '
    . '<a class="zpSeoLink" href="' . esc_url(home_url('/strony-internetowe/migracja-sklepu-na-woocommerce/')) . '">migracja sklepu internetowego</a>. '
    . 'Orientacyjne koszty opisujemy w poradniku <a class="zpSeoLink" href="' . esc_url(home_url('/seo-i-konwersja/ile-kosztuje-sklep-internetowy-woocommerce-w-2026-roku/')) . '">ile kosztuje sklep internetowy</a>, '
    . 'a <a class="zpSeoLink" href="' . esc_url(home_url('/studio-wyceny/')) . '">wycenę sklepu internetowego</a> przygotujemy po kilku pytaniach w Studio Wyceny.';
  return '<section class="zpShopSeoBoost zpSeoPlanGuide" id="poradnik-tworzenie-sklepu" aria-labelledby="zpSeoPlanGuideTitle">'
    . '<div class="zpShopSeoBoost__inner"><header class="zpShopSeoBoost__head">'
    . '<span class="zpShopSeoBoost__kicker">Poradnik / tworzenie sklepu internetowego krok po kroku</span>'
    . '<div class="zpShopSeoBoost__headline"><h2 class="zpShopSeoBoost__title" id="zpSeoPlanGuideTitle">Od pomysłu na ofertę do gotowego sklepu online.</h2>'
    . '<p class="zpShopSeoBoost__lead">' . $lead . '</p></div></header>'
    . zp_seo_guide_body((string) $content, '/tworzenie-sklepow-internetowych/', true) . '</div></section>';
}

/* ------------------------------------------------------------------- specs */

function zp_seo_service_spec(string $variant): ?array {
  // Local originals bypass the spec in English; the national shop has its own EN
  // dictionary, so the new Polish demand copy must not change its translation input.
  $demand_pl = !zp_seo_plan_is_en();
  $L = static function (string $path, string $text): string {
    return '<a class="zpSeoLink" href="' . esc_url(home_url($path)) . '">' . $text . '</a>';
  };
  $cost_www  = '/strony-www/ile-kosztuje-stworzenie-strony-internetowej-dla-firmy/';
  $cost_logo = '/logo-branding/ile-kosztuje-logo-dla-firmy-i-co-obejmuje-cena/';
  $local = static function (string $key, string $text): string {
    return '<a class="zpSeoLink" href="{{' . $key . '}}">' . $text . '</a>';
  };
  // Articles published by the plan (2.5.0+): linked only once they are live, plain text otherwise.
  $A = static function (string $path, string $text) use ($L): string {
    return zp_seo_plan_link_is_live($path) ? $L($path, $text) : $text;
  };
  // A whole sentence that points to such an article: shown only once the article is live (2.6.0).
  $AS = static function (string $path, string $before, string $text, string $after) use ($L): string {
    return zp_seo_plan_link_is_live($path) ? $before . $L($path, $text) . $after : '';
  };

  $content = zp_seo_content_page_by_variant($variant);
  if ($content) { return ['kind' => $content['kind'], 'content' => $content]; }

  switch ($variant) {

    case 'strony-national':
      return [
        'kind' => 'strony',
        // 3.2.5x: "dla firm" stays together, so the hero heading ends with it on its own line (industries.css, 8).
        'h1' => 'Tworzenie i projektowanie <strong class="gradient-text">stron internetowych dla&nbsp;firm</strong>',
        'faq' => [
          ['new', 'Czy tworzycie strony internetowe dla firm z całej Polski?', 'Tak. Projektujemy i tworzymy strony internetowe dla firm z całej Polski — <strong>strony firmowe, ' . $L('/strona-wizytowka/', 'strony wizytówki') . ', landing page’e i rozbudowane serwisy</strong>. Brief, prezentacje i odbiory prowadzimy online, a z firmami ze Śląska spotykamy się też w naszym biurze w Katowicach.' . $AS('/strony-internetowe/rodzaje-stron-internetowych/', ' Czym różnią się ', 'rodzaje stron internetowych', ' i który wybrać, wyjaśniamy w osobnym poradniku.')],
          ['keep', 2], ['keep', 3], ['keep', 4],
          ['new', 'Ile kosztuje stworzenie strony internetowej?', 'Cena zależy od zakresu: liczby podstron, poziomu projektu, treści, funkcji, SEO czy sklepu WooCommerce. Od czego zależy cena strony wizytówki, firmowej i rozbudowanej, wyjaśniamy w poradniku ' . $L($cost_www, 'ile kosztuje strona internetowa') . '. Dokładną wycenę przygotujemy po ' . $L('/studio-wyceny/', 'uzupełnieniu Studia Wyceny') . ' — dobierzemy wtedy zakres: landing page, Starter, Premium albo projekt indywidualny.'],
          ['new', 'Na czym robicie strony internetowe?', 'Najczęściej ' . $L('/strony-wordpress/', 'na WordPressie') . '. Projekt powstaje indywidualnie, bez gotowego szablonu, a wdrożenie daje <strong>łatwą edycję treści, szybkie działanie i solidne podstawy SEO</strong>. Przy prostszych projektach, takich jak strona wizytówka czy landing page, dobieramy lżejszą strukturę — zasada zostaje ta sama: strona ma prowadzić do kontaktu. Jeśli rozważasz kreator albo samodzielną pracę, przeczytaj, ' . $A('/strony-internetowe/jak-stworzyc-strone-internetowa/', 'jak stworzyć stronę internetową') . ' i kiedy warto oddać ją agencji.'],
          ['new', 'Czy wykonujecie sklepy internetowe WooCommerce?', 'Tak. ' . $L('/tworzenie-sklepow-internetowych/', 'Tworzenie sklepów internetowych') . ' na WooCommerce to nasza druga główna specjalizacja — od prostych sklepów produktowych po wdrożenia z wariantami, płatnościami, dostawami, katalogiem B2B i konfiguratorami.'],
          ['keep', 7], ['keep', 8],
          ['new', 'Ile trwa stworzenie strony internetowej?', 'Termin zależy od zakresu i tempa przekazywania materiałów. Strona wizytówka lub landing page powstaje szybciej niż serwis firmowy z kilkunastoma podstronami i treściami SEO. Po briefie podajemy harmonogram z etapami i datą publikacji.' . $AS('/strony-internetowe/makieta-strony-internetowej/', ' Jednym z pierwszych etapów jest ', 'makieta strony internetowej', ', czyli plan układu podstron przed projektem graficznym.')],
        ],
        'replace' => array_merge([
          ['<p class="hero-eyebrow">Strony internetowe • Katowice</p>', '<p class="hero-eyebrow">Strony internetowe • cała Polska</p>'],
          ['Tworzymy strony dla firm z Katowic, Śląska i całej Polski: od', 'Projektujemy i tworzymy strony internetowe dla firm z całej Polski: od'],
          ['<h2 class="section-title">Wybierz zakres — od landing page po <strong>serwis premium.</strong></h2>', '<h2 class="section-title">Strona wizytówka, firmowa czy <strong>rozbudowany serwis?</strong></h2>'],
          ['<p class="section-lead">Ten wybór pomaga szybko określić punkt startu. W Studio Wyceny doprecyzujesz', '<p class="section-lead">Ten wybór pomaga szybko określić punkt startu. Od czego zależy cena, wyjaśniamy w poradniku ' . $L($cost_www, 'ile kosztuje strona internetowa') . ', a dokładną ' . $L('/studio-wyceny/', 'wycenę strony internetowej') . ' przygotujemy na podstawie Studia Wyceny. Doprecyzujesz w nim'],
          ['<h2 class="section-title">Duże projekty. <strong>Jeszcze większy kontekst.</strong></h2>', '<h2 class="section-title">Realizacje stron internetowych. <strong>Duże projekty, większy kontekst.</strong></h2>'],
          ['<h2 class="section-title">Od briefu do publikacji. <strong>Bez chaosu między etapami.</strong></h2>', '<h2 class="section-title">Etapy tworzenia strony internetowej. <strong>Od briefu do publikacji.</strong></h2>'],
          ['<strong>jasny zakres, terminy i kryterium akceptacji.</strong></p>', '<strong>jasny zakres, terminy i kryterium akceptacji.</strong> Szczegółowo opisujemy je w poradniku ' . $L('/strony-internetowe/tworzenie-stron-internetowych-profesjonalny-proces-od-strategii-do-wdrozenia/', 'etapy tworzenia strony internetowej') . '.</p>'],
          ['<h2 class="section-title">Nie tylko wygląd. <strong>Cały system strony.</strong></h2>', '<h2 class="section-title">Projektowanie stron www. <strong>UX, treści i design w jednym systemie.</strong></h2>'],
          ['Tak. Inaczej projektuje się stronę kancelarii, inaczej dewelopera, salonu beauty, lekarza czy producenta.', 'Tak. Inaczej projektuje się stronę ' . $L('/strony-internetowe-dla-kancelarii/', 'kancelarii') . ', inaczej ' . $L('/strony-internetowe-dla-deweloperow/', 'dewelopera') . ', ' . $L('/strony-internetowe-dla-salonow-beauty/', 'salonu beauty') . ', ' . $L('/strony-internetowe-dla-lekarzy/', 'lekarza') . ' czy producenta. Osobno opisujemy też strony dla ' . $L('/strony-internetowe-dla-trenerow-personalnych/', 'trenerów personalnych') . ', ' . $L('/strony-internetowe-dla-fotografow/', 'fotografów') . ', ' . $L('/strony-internetowe-dla-restauracji/', 'restauracji') . ' i ' . $L('/strony-internetowe-dla-hoteli/', 'hoteli') . '.'],
          ['<p>Aktualizacje, kopie zapasowe, drobne zmiany i rozwój: nowe sekcje, podstrony SEO, landing pages pod kampanie.</p>', '<p>Aktualizacje, kopie zapasowe i drobne zmiany w ramach usługi ' . $L('/opieka-wordpress/', 'opieka nad stroną WordPress') . ', a do tego rozwój: nowe sekcje, podstrony SEO, landing pages pod kampanie.</p>'],
          ['<p class="section-kicker">Katowice • Śląsk • cała Polska</p>', '<p class="section-kicker">Cała Polska • siedziba w Katowicach</p>'],
          ['Z Katowic projektujemy marki dla firm z całej Polski. Brief, prezentacje kierunków, konsultacje i przekazanie plików możemy przeprowadzić online — etap po etapie.', 'Pracujemy z firmami z całej Polski, a nasze studio mieści się w Katowicach. Brief, makiety, prezentacje projektu, konsultacje i odbiór strony prowadzimy online — etap po etapie. Firmy ze Śląska zapraszamy też do biura: ' . $local('strony-katowice', 'strony internetowe w Katowicach') . '.'],
        ], $demand_pl ? zp_seo_service_national_copy('strony') : []),
      ];

    case 'strony-local':
      return [
        'kind' => 'strony',
        // 2.7.0 (Mat): packages, projects, industries and scope are back, so the page is not empty.
        'drop' => ['class="section manifesto"', 'id="oferta"', 'class="pointing"', 'id="analityka"'],
        'h1' => 'Strony internetowe <strong class="gradient-text">w Katowicach i na Śląsku</strong>',
        'faq' => [
          ['new', 'Czy możemy spotkać się w Katowicach?', 'Tak. Nasze biuro mieści się w Katowicach przy <strong>ul. Modelarskiej 18/2</strong>. Na spotkaniu omawiamy brief, cele strony i zakres prac. Kolejne etapy — makiety, projekt i odbiory — prowadzimy w biurze albo online, jak wygodniej.'],
          ['new', 'Czy tworzycie strony internetowe dla firm z całego Śląska?', 'Tak. Pracujemy z firmami z Katowic i całego województwa śląskiego, a także z innych regionów Polski. Firmom działającym lokalnie układamy strukturę treści pod <strong>wyszukiwania lokalne</strong> i łączymy stronę z Profilem Firmy w Google.' . $AS('/agencja-reklamowa-katowice/', ' Jeśli oprócz strony potrzebujesz logo i reklam, zobacz, co robi nasza ', 'agencja reklamowa w Katowicach', '.')],
          ['keep', 2],
          ['new', 'Ile kosztuje strona internetowa w Katowicach?', 'Strony firmowe realizujemy od <strong>3 999 zł</strong>. Cena zależy od liczby podstron, projektu, treści i funkcji; osobno ustalamy zakres rezerwacji, katalogu czy integracji. Pakiety porównasz wyżej, a czynniki wyceny opisujemy w poradniku ' . $L($cost_www, 'ile kosztuje strona internetowa') . '. Dokładną propozycję przygotujemy po uzupełnieniu ' . $L('/studio-wyceny/', 'Studia Wyceny') . '.'],
          ['keep', 3],
          ['new', 'Gdzie znajdę więcej o tworzeniu stron internetowych?', 'Pakiety i realizacje pokazujemy wyżej na tej stronie. Więcej o procesie i o tym, czym różni się strona wizytówka, strona firmowa i rozbudowany serwis, piszemy na stronie ' . $L('/tworzenie-stron-internetowych/', 'tworzenie stron internetowych') . '.'],
          ['keep', 8],
          ['new', 'Co przygotować przed spotkaniem o stronie internetowej w Katowicach?', 'Przygotuj listę usług, grupy klientów, obszar działania, przykładowe realizacje i sposób przyjmowania zapytań. Warto zaznaczyć, czy potrzebujesz formularza, rezerwacji wizyt czy katalogu. Dzięki temu brief w naszym biurze przy Modelarskiej 18/2 dotyczy konkretnego zakresu. Zobacz, jak ułożyć ' . $L('/seo-i-konwersja/struktura-podstrony-uslugowej-premium/', 'podstronę konkretnej usługi') . ' oraz jakie elementy powinna zawierać ' . $L('/sklepy-internetowe/strona-internetowa-dla-firmy-z-katowic-co-powinna-zawierac-zeby-zdobywac-lokalnych-klientow/', 'strona internetowa firmy z Katowic') . '.'],
          ['new', 'Jak dopasować stronę do firmy, która obsługuje klientów lokalnie?', 'Dane kontaktowe, opis rzeczywistego obszaru obsługi i podstrony usług powinny odpowiadać temu, czego klient szuka przed telefonem lub wizytą. W portfolio Apartament Piękna pokazuje stronę z rezerwacjami, a Siemianowski — strukturę usług kancelarii. To różne zakresy, dlatego dopasowujemy stronę do sposobu pracy firmy. Więcej o widoczności lokalnej: ' . $L('/sklepy-internetowe/seo-lokalne-w-katowicach-jak-pozyskiwac-klientow-z-miasta-i-okolic/', 'SEO lokalne w Katowicach') . '.'],
        ],
        'replace' => [
          ['Tworzymy strony dla firm z Katowic, Śląska i całej Polski: od', 'Studio z Katowic, ul. Modelarska 18/2. Tworzymy strony internetowe dla firm ze Śląska: od'],
          ['<h2 class="section-title">Wybierz zakres — od landing page po <strong>serwis premium.</strong></h2>', '<h2 class="section-title">Pakiety stron internetowych <strong>dla firm z Katowic i Śląska.</strong></h2>'],
          ['<p class="section-kicker">Katowice • Śląsk • cała Polska</p>', '<p class="section-kicker">Katowice • ul. Modelarska 18/2 • Śląsk</p>'],
          ['<h2 class="section-title"> Jedno studio. <strong>Współpraca bez granic lokalizacji.</strong> </h2>', '<h2 class="section-title">Spotkajmy się <strong>w Katowicach.</strong></h2>'],
          ['Z Katowic projektujemy marki dla firm z całej Polski. Brief, prezentacje kierunków, konsultacje i przekazanie plików możemy przeprowadzić online — etap po etapie.', 'Zapraszamy do naszego biura przy ul. Modelarskiej 18/2 w Katowicach — tam omówimy brief, cele i zakres strony. Jeśli wygodniej, cały proces przeprowadzimy online. Pełny zakres usług opisujemy na stronie ' . $L('/tworzenie-stron-internetowych/', 'tworzenie stron internetowych') . '.'],
          ['Duże projekty. <strong>Jeszcze większy kontekst.</strong>', 'Projektowanie stron w Katowicach. <strong>Zobacz zakres naszych realizacji.</strong>'],
          ['Jedenaście wybranych realizacji — z pełniejszym opisem, zakresem, efektem i bezpośrednim podglądem strony. Każdy projekt ma <strong>własny cel, język, kolor i ścieżkę do kontaktu.</strong>', 'Studio w Katowicach, projekty dla różnych branż: <strong>Apartament Piękna</strong> łączy stronę z rezerwacjami, <strong>Siemianowski</strong> prezentuje specjalizacje kancelarii, a <strong>Gravia</strong> pokazuje realizacje infrastrukturalne i moduł kariery. Wybierz przykład zbliżony do zakresu Twojej firmy i sprawdź opis prac oraz stronę live.'],
        ],
        // Audit 21.10.2026, item 02: the local section about the studio follows the portfolio (katowice-local.php).
        'after' => ['id="portfolio"' => static function () { return zp_seo_katowice_local_html('strony'); }],
      ];

    case 'sklepy-national':
      return [
        'kind' => 'sklepy',
        'faq_style' => 'shop',
        'h1' => 'Tworzenie sklepów internetowych <span class="zh__grad">na WooCommerce</span>',
        'faq' => [
          ['new', 'Czy tworzycie sklepy internetowe dla firm z całej Polski?', 'Tak. Projektujemy i wdrażamy <strong>sklepy internetowe dla firm z całej Polski</strong>. Najczęściej pracujemy na WordPressie i WooCommerce, ponieważ daje to dużą elastyczność przy sprzedaży produktów, usług, zapytań B2B, konfiguratorów, płatności, dostaw, SEO oraz późniejszym rozwoju sklepu. Firmy ze Śląska zapraszamy też do biura — zobacz ' . $local('sklepy-katowice', 'sklepy internetowe w Katowicach') . '.'],
          ['keep', 2], ['keep', 3], ['keep', 4], ['keep', 5],
          ['new', 'Ile kosztuje stworzenie sklepu internetowego?', $demand_pl ? 'Sklepy WooCommerce realizujemy od <strong>6 499 zł</strong>. Wycena zależy od liczby produktów i kategorii, projektu, wariantów, płatności, dostaw oraz integracji. Sklep detaliczny i system z zamówieniami hurtowymi wymagają innego zakresu. Porównaj pakiety wyżej i przeczytaj, ' . $L('/seo-i-konwersja/ile-kosztuje-sklep-internetowy-woocommerce-w-2026-roku/', 'ile kosztuje sklep internetowy') . '. Dokładną ' . $L('/studio-wyceny/', 'wycenę sklepu internetowego') . ' przygotujemy na podstawie Twojej oferty i wymaganych funkcji.' : 'Cena zależy od zakresu: liczby produktów i kategorii, projektu graficznego, treści, integracji płatności i dostaw, wariantów, filtrów, automatyzacji i SEO. Orientacyjne kwoty opisujemy w poradniku ' . $L('/seo-i-konwersja/ile-kosztuje-sklep-internetowy-woocommerce-w-2026-roku/', 'ile kosztuje sklep internetowy') . ', a dokładną ' . $L('/studio-wyceny/', 'wycenę sklepu internetowego') . ' przygotujemy po kilku pytaniach w Studio Wyceny.'],
          // 2.8.0: payments and B2B answers point to the articles from content batch 4.
          ['keep', 7, $AS('/sklepy-internetowe/bramka-platnicza/', 'Jak wybrać operatora i podłączyć go do WooCommerce, wyjaśniamy w poradniku o tym, jak działa ', 'bramka płatnicza', '.')],
          $demand_pl ? ['new', 'Czy tworzycie sklepy B2B, hurtowe lub katalogi zapytań?', 'Tak. W portfolio <strong>ProScarves</strong> łączy sklep detaliczny z osobną ścieżką zapytań hurtowych dla klubów, firm i organizacji, a <strong>Bransoletka24</strong> obsługuje detal i hurt w jednym sklepie WooCommerce. Przed wyceną ustalamy, czy klient ma kupować od razu, czy przesyłać zapytanie, oraz jak mają działać ceny i warianty. Zobacz ' . $L('/tworzenie-sklepow-internetowych/#portfolio-sklepy', 'realizacje sklepów') . ' i poradnik ' . $L('/sklepy-internetowe/sklep-b2b/', 'sklep B2B') . '.'] : ['keep', 8, $AS('/sklepy-internetowe/sklep-b2b/', 'Funkcje i platformy opisujemy w poradniku o tym, jak zbudować ', 'sklep B2B', '.')],
          ['keep', 9], ['keep', 10], ['keep', 11], ['keep', 12],
          ['new', 'Czy projektujecie sklepy WooCommerce pod konkretne branże?', $demand_pl ? 'Tak. <strong>Świat Grilli</strong> porządkuje ofertę wielu marek, <strong>Papeterio</strong> łączy papeterię z wariantami i personalizacją, a <strong>ProScarves</strong> rozdziela sprzedaż gotowych produktów i zapytania hurtowe. Te realizacje pokazują, dlaczego kategorie, ' . $L('/sklepy-internetowe/karta-produktu-w-sklepie-internetowym/', 'karta produktu') . ' i sposób zamówienia wynikają z oferty, a nie z jednego szablonu. Zakres dopasowujemy do Twojego modelu sprzedaży.' : 'Tak. Sklep dla producenta, marki premium, firmy B2B, salonu, dystrybutora albo marki lokalnej powinien mieć inną strukturę kategorii, kart produktów, filtrów i ścieżki zakupu. Dlatego dopasowujemy WooCommerce do produktu, marży, sposobu sprzedaży i dalszego SEO e-commerce.' . $AS('/sklepy-internetowe/seo-sklepu-internetowego/', ' Jak przygotować sklep pod Google już przy budowie, opisujemy w poradniku ', 'SEO sklepu internetowego', '.')],
          ['new', 'Czy przenosicie istniejące sklepy na WooCommerce?', 'Tak. Przenosimy produkty, kategorie, klientów i zamówienia, a przede wszystkim adresy URL i przekierowania, żeby sklep nie stracił widoczności w Google. Jak wygląda taki proces, opisujemy w poradniku ' . $L('/strony-internetowe/migracja-sklepu-na-woocommerce/', 'migracja sklepu na WooCommerce') . '.'],
          ['keep', 14], ['keep', 15],
        ],
        'replace' => array_merge([
          ['aria-label="Hero Sklepy internetowe Katowice"', 'aria-label="Hero Tworzenie sklepów internetowych"'],
          ['<span class="dot"></span>Sklepy internetowe • Katowice</p>', '<span class="dot"></span>Sklepy internetowe • cała Polska</p>'],
          ['Projektujemy sklepy dla firm z Katowic, Śląska i całej Polski: od', 'Tworzymy sklepy internetowe WooCommerce dla firm z całej Polski: od'],
          ['<a class="zh__chip" href="/sklepy-internetowe-katowice/"><b>01</b><span>sklepy internetowe Katowice</span></a>', '<a class="zh__chip" href="{{sklepy-katowice}}"><b>01</b><span>sklepy internetowe w Katowicach</span></a>'],
          ['Tworzenie sklepów internetowych Katowice — WooCommerce, który porządkuje ofertę i prowadzi klienta do zakupu.', 'Tworzenie sklepów internetowych — WooCommerce, który porządkuje ofertę i prowadzi klienta do zakupu.'],
          ['Katowice / Śląsk / cała Polska', 'Cała Polska / siedziba w Katowicach'],
          ['alt="Zespół Zaprojektowani.com — projektowanie sklepów internetowych Katowice"', 'alt="Zespół Zaprojektowani.com — projektowanie sklepów internetowych"'],
          ['<span class="zpShopSeoBoost__kicker">Sklepy internetowe Katowice / WooCommerce / sprzedaż online</span>', '<span class="zpShopSeoBoost__kicker">Dlaczego WooCommerce / sprzedaż online</span>'],
          ['Sklepy internetowe Katowice — WooCommerce i sprzedaż online.', 'Dlaczego WooCommerce? Sklep, który rośnie razem z firmą.'],
          ['Tworzymy sklepy internetowe dla firm z Katowic, Śląska i całej Polski. Łączymy projekt graficzny, WooCommerce, płatności online, UX koszyka, SEO i techniczne wdrożenie, żeby sklep był gotowy do sprzedaży.', 'Tworzymy sklepy internetowe dla firm z całej Polski. Łączymy projekt graficzny, WooCommerce, płatności online, UX koszyka, SEO i techniczne wdrożenie, żeby sklep był gotowy do sprzedaży. Porównania platform znajdziesz w poradnikach ' . $L('/strony-internetowe/woocommerce-czy-shopify/', 'WooCommerce czy Shopify') . ' oraz ' . $L('/strony-internetowe/woocommerce-czy-prestashop/', 'WooCommerce czy PrestaShop') . '.'],
          ['<span class="zpShopSeoBoost__cardKicker">Sklep WooCommerce Katowice</span>', '<span class="zpShopSeoBoost__cardKicker">Sklep WooCommerce</span>'],
          ['<span class="zpShopSeoBoost__cardKicker">Projektowanie sklepów internetowych Katowice</span>', '<span class="zpShopSeoBoost__cardKicker">Projektowanie sklepów internetowych</span>'],
          ['<span>Sklepy internetowe Katowice / WooCommerce</span>', '<span>Tworzenie sklepów internetowych / WooCommerce</span>'],
          ['<span class="zpFaqShopKatNavy__kicker">FAQ / Sklepy internetowe Katowice</span>', '<span class="zpFaqShopKatNavy__kicker">FAQ / Tworzenie sklepów internetowych</span>'],
          // 3.2.50-sklepy: kicker of the FAQ in the website/logo markup (templates/shop-katowice/sections/faq.html).
          ['FAQ / Sklepy internetowe Katowice</p>', 'FAQ / Tworzenie sklepów internetowych</p>'],
          ['FAQ — sklepy internetowe Katowice i WooCommerce.', 'FAQ — tworzenie sklepów internetowych i WooCommerce.'],
          ['albo sklep lokalnej firmy z Katowic.', 'albo sklep lokalnej firmy.'],
        ], $demand_pl ? [
          ['Poniżej znajdziesz krótką selekcję projektów e-commerce: od sklepów B2B i hurtowych, przez marki z personalizacją produktów, po rozbudowany multi-brandowy sklep dla branży grillowej.', '<strong>Świat Grilli</strong> — wiele marek i uporządkowane kategorie. <strong>ProScarves</strong> — detal oraz osobna ścieżka zapytań hurtowych. <strong>Papeterio</strong> — warianty i personalizacja papeterii. Porównaj zakres tych realizacji z własną ofertą; niżej znajdziesz opisy prac i linki do działających sklepów.'],
          ['Nie zaczynamy od przypadkowego szablonu. Najpierw układamy ofertę, kategorie, produkty i ścieżkę zakupu, a dopiero potem projektujemy wygląd, wdrażamy WooCommerce i przygotowujemy sklep do realnej sprzedaży online.', 'Projektowanie sklepu zaczynamy od produktów i sposobu zamawiania. Ustalamy strukturę kategorii, warianty, płatności i dostawy, następnie projektujemy widoki i wdrażamy WooCommerce. Przed publikacją sprawdzamy zakup i wiadomości transakcyjne. Zobacz ' . $L('/tworzenie-sklepow-internetowych/#portfolio-sklepy', 'przykłady sklepów o różnych modelach sprzedaży') . ' i opisz swój zakres w ' . $L('/studio-wyceny/', 'Studio Wyceny') . '.'],
        ] : []),
        // The guide goes last, right above the FAQ (Mat, 2.5.0); the FAQ section is section#faq since 3.2.50-sklepy.
        'before' => [['id="faq"', 'zp_seo_shop_guide_html', 'id="sklepy-woocommerce-katowice"']],
      ];

    case 'sklepy-local':
      return [
        'kind' => 'sklepy',
        'faq_style' => 'shop',
        // 2.7.0 (Mat): packages, the local WooCommerce section, process and industries are back.
        'drop' => ['id="zpShopWhyLight"'],
        'h1' => 'Sklepy internetowe <span class="zh__grad">w Katowicach i na Śląsku</span>',
        'faq' => [
          ['new', 'Czy tworzycie sklepy internetowe dla firm z Katowic?', 'Tak. Projektujemy i wdrażamy <strong>sklepy internetowe dla firm z Katowic i całego Śląska</strong>. Zapraszamy do naszego biura przy ul. Modelarskiej 18/2 w Katowicach — tam omówimy model sprzedaży, produkty i zakres wdrożenia. Pełny zakres usług opisujemy na stronie ' . $L('/tworzenie-sklepow-internetowych/', 'tworzenie sklepów internetowych') . '.'],
          ['new', 'Czy prowadzicie projekt sklepu w biurze, czy online?', 'Jak wygodniej. Brief i warsztat sprzedażowy możemy zrobić w biurze w Katowicach, a projekt, wdrożenie i odbiory prowadzić online. Po starcie sklepu pomagamy w rozbudowie, treściach i kampaniach.' . $AS('/agencja-reklamowa-katowice/', ' Sklep, logo i kampanie może prowadzić jedna ', 'agencja reklamowa w Katowicach i na Śląsku', '.')],
          ['new', 'Ile kosztuje sklep internetowy?', 'Cena zależy od zakresu: liczby produktów, integracji płatności i dostaw, projektu i SEO. Orientacyjne kwoty opisujemy w poradniku ' . $L('/seo-i-konwersja/ile-kosztuje-sklep-internetowy-woocommerce-w-2026-roku/', 'ile kosztuje sklep internetowy') . ', a dokładną wycenę przygotujemy w ' . $L('/studio-wyceny/', 'Studio Wyceny') . '.'],
          ['keep', 7], ['keep', 9], ['keep', 15],
        ],
        'replace' => [
          ['Projektujemy sklepy dla firm z Katowic, Śląska i całej Polski: od', 'Studio z Katowic, ul. Modelarska 18/2. Projektujemy sklepy WooCommerce dla firm ze Śląska: od'],
          ['<a class="zh__chip" href="/sklepy-internetowe-katowice/"><b>01</b><span>sklepy internetowe Katowice</span></a>', '<a class="zh__chip" href="/tworzenie-sklepow-internetowych/"><b>01</b><span>pełna oferta sklepów</span></a>'],
          // Audit 21.10.2026, item 02: headings the page shared word for word with /tworzenie-sklepow-internetowych/.
          ['Wybierz sklep, który nie tylko wygląda dobrze, ale <em>sprzedaje, mierzy i rośnie razem z Twoją marką</em>.', 'Pakiety sklepów WooCommerce. Wybierz sklep, który <em>sprzedaje, mierzy i rośnie razem z Twoją marką</em>.'],
          ['Portfolio sklepów internetowych — realizacje WooCommerce, UX i prezentacja produktów.', 'Projektowanie sklepów internetowych — realizacje WooCommerce, UX i prezentacja produktów.'],
          ['Jak tworzymy sklepy WooCommerce — od strategii i UX po SEO oraz sprzedaż.', 'Tworzenie sklepu internetowego krok po kroku — od strategii i UX po SEO oraz sprzedaż.'],
        ],
        'after' => ['id="portfolio-sklepy"' => static function () { return zp_seo_katowice_local_html('sklepy'); }],
      ];

    case 'logo-national':
      return [
        'kind' => 'logo',
        'h1' => 'Projektowanie logo <strong class="gradient-text">i logotypów dla firm</strong>',
        'faq' => [
          ['new', 'Czy projektujecie logo dla firm z całej Polski?', 'Tak. Projektujemy <strong>logo i logotypy</strong> dla firm z całej Polski. Cały proces — od briefu po odbiór uporządkowanych plików — prowadzimy online, a firmy ze Śląska mogą spotkać się z nami w biurze w Katowicach.'],
          ['new', 'Czym różni się logo, logotyp i sygnet?', '<strong>Logotyp</strong> to znak zbudowany z samego napisu — nazwy firmy w dopracowanym kroju. <strong>Sygnet</strong> to symbol graficzny, który działa też samodzielnie, np. jako ikona czy avatar. <strong>Logo</strong> to potoczna nazwa całego znaku: logotypu, sygnetu albo ich połączenia. Formę dobieramy do nazwy, branży i miejsc użycia. Przykłady pokazujemy w poradniku ' . $A('/logo-branding/logo-logotyp-sygnet-roznice/', 'logo, logotyp czy sygnet') . '.'],
          ['keep', 2], ['keep', 3], ['keep', 4], ['keep', 5], ['keep', 8], ['keep', 9], ['keep', 10], ['keep', 13], ['keep', 14], ['keep', 15],
        ],
        'replace' => array_merge([
          ['<p class="hero-eyebrow">Logo &amp; branding • Katowice</p>', '<p class="hero-eyebrow">Projektowanie logo • cała Polska</p>'],
          ['Tworzymy <strong>logo, identyfikacje wizualne i brandbooki</strong>, które dobrze wyglądają nie tylko na prezentacji. Projektujemy system gotowy na stronę internetową, social media, druk, opakowania i materiały sprzedażowe.', 'Projektujemy <strong>logo i logotypy dla firm z całej Polski</strong> — od strategii i koncepcji, przez warianty znaku, po komplet plików do druku i internetu. Każdy projekt sprawdzamy na stronie, w social mediach, w druku i na materiałach sprzedażowych.'],
          ['z pełną księgą znaku i zasadami wdrożenia.', 'z pełną księgą znaku i zasadami wdrożenia. Orientacyjne kwoty opisujemy w poradniku ' . $L($cost_logo, 'ile kosztuje logo') . '.'],
          ['na dokumentach, wizytówkach, stronie i materiałach firmowych.', 'na dokumentach, wizytówkach, stronie i materiałach firmowych. Gdy znak potrzebuje całego systemu, zajmujemy się też projektem, jakim jest ' . $L('/identyfikacja-wizualna/', 'identyfikacja wizualna') . '.' . $AS('/logo-branding/mockup-co-to-jest/', ' Jak oceniać logo na wizualizacjach, wyjaśniamy w poradniku ', 'mockup – co to jest', '.')],
          ['<h2 class="section-title"> Różne branże. <strong>Żadnego jednego stylu na wszystko.</strong> </h2>', '<h2 class="section-title">Realizacje logo. <strong>Żadnego jednego stylu na wszystko.</strong></h2>'],
          ['<h2 class="section-title"> Od briefu do plików <strong>gotowych do wdrożenia.</strong> </h2>', '<h2 class="section-title">Jak projektujemy logo: <strong>od briefu do plików.</strong></h2>'],
          ['Wiesz, nad czym pracujemy, po co i co wydarzy się dalej.', 'Wiesz, nad czym pracujemy, po co i co wydarzy się dalej. Krok po kroku opisujemy to w poradniku ' . $L('/logo-branding/projektowanie-logo-jak-powstaje-znak-ktory-dziala-w-internecie-druku-i-sprzedazy/', 'jak zaprojektować logo') . '.'],
          ['<h2> System gotowy do pracy. <strong class="gradient-text">Nie pojedynczy plik.</strong> </h2>', '<h2>Warianty i pliki logo. <strong class="gradient-text">Nie pojedynczy plik.</strong></h2>'],
          ['<p class="section-kicker">Katowice • Śląsk • cała Polska</p>', '<p class="section-kicker">Cała Polska • siedziba w Katowicach</p>'],
          ['Z Katowic projektujemy marki dla firm z całej Polski. Brief, prezentacje kierunków, konsultacje i przekazanie plików możemy przeprowadzić online — etap po etapie.', 'Projektujemy logo dla firm z całej Polski. Brief, prezentacje kierunków, konsultacje i przekazanie plików prowadzimy online — etap po etapie. Firmy ze Śląska zapraszamy do biura: ' . $local('logo-katowice', 'projektowanie logo w Katowicach') . '.'],
          ['<p class="section-kicker">FAQ / logo i branding</p>', '<p class="section-kicker">FAQ / projektowanie logo</p>'],
          ['dzięki którym marka pozostaje spójna na stronie, w social mediach i druku.', 'dzięki którym marka pozostaje spójna na stronie, w social mediach i druku. Ten zakres opisujemy na stronie ' . $L('/identyfikacja-wizualna/', 'identyfikacja wizualna') . '.'],
          ['Dodatkowe materiały, opakowania lub rozbudowane wdrożenie wyceniamy indywidualnie.', 'Dodatkowe materiały, opakowania lub rozbudowane wdrożenie wyceniamy indywidualnie. Więcej w poradniku ' . $L($cost_logo, 'ile kosztuje logo dla firmy') . '.'],
          ['uporządkowany zestaw gotowy dla drukarni i zespołu digital.', 'uporządkowany zestaw gotowy dla drukarni i zespołu digital. Czym jest ' . $A('/logo-branding/logo-wektorowe-pliki-logo/', 'logo wektorowe') . ' i do czego służy każdy format, wyjaśniamy w osobnym poradniku.'],
          ['przygotowany do legalnego, codziennego użycia przez firmę.', 'przygotowany do legalnego, codziennego użycia przez firmę. Jeśli chcesz mieć wyłączność na znak, sprawdź, jak wygląda ' . $A('/logo-branding/jak-zastrzec-logo/', 'zastrzeżenie logo') . ' w Urzędzie Patentowym.'],
        ], $demand_pl ? zp_seo_service_national_copy('logo') : []),
      ];

    case 'identity-national':
      return [
        'kind' => 'logo',
        'drop' => ['class="section manifesto"'],
        'h1' => 'Identyfikacja wizualna <strong class="gradient-text">i branding dla firm</strong>',
        'faq' => [
          ['new', 'Co obejmuje identyfikacja wizualna firmy?', 'Identyfikacja wizualna to system: <strong>logo i jego warianty, kolory, typografia, styl grafik i zdjęć</strong>, wzory materiałów firmowych oraz zasady ich użycia zebrane w księdze znaku. Dzięki niemu marka wygląda tak samo na stronie, w social mediach, w ofertach i w druku. Przy kampaniach system uzupełnia ' . $A('/logo-branding/key-visual-co-to-jest/', 'key visual kampanii') . ', czyli wspólny motyw reklam. Więcej w poradniku ' . $L('/logo-branding/identyfikacja-wizualna-firmy/', 'identyfikacja wizualna firmy: co zawiera') . '.'],
          ['new', 'Od czego zaczyna się projekt identyfikacji wizualnej?', 'Od briefu i strategii marki. Poznajemy branżę, odbiorców, konkurencję i miejsca, w których marka będzie używana, a potem porządkujemy skojarzenia, ton, kolorystykę i typografię. Dopiero na tej podstawie powstają koncepcje logo i cały system.' . $AS('/logo-branding/strategia-marki/', ' Jak przygotować się do tego etapu, piszemy w poradnikach ', 'strategia marki', ' i ' . $A('/logo-branding/archetypy-marki/', 'archetyp marki') . '.')],
          ['keep', 2],
          ['new', 'Ile kosztuje identyfikacja wizualna?', 'Zakres zaczyna się od <strong>Mini Brandingu od 1499 zł</strong> (logo, kolory i typografia), a pełny system z księgą znaku to <strong>Branding Premium od 2999 zł</strong>. Dodatkowe materiały wyceniamy indywidualnie. Szczegóły opisujemy w poradniku ' . $L($cost_logo, 'ile kosztuje logo i identyfikacja wizualna') . '.'],
          ['keep', 4], ['keep', 6], ['keep', 7], ['keep', 11], ['keep', 12], ['keep', 14],
          ['new', 'Czy pracujecie z firmami z całej Polski?', 'Tak. Identyfikację wizualną projektujemy dla firm z całej Polski — warsztat marki, prezentacje i odbiory prowadzimy online. Z firmami ze Śląska spotykamy się też w naszym biurze w Katowicach.'],
        ],
        'replace' => array_merge([
          ['<p class="hero-eyebrow">Logo &amp; branding • Katowice</p>', '<p class="hero-eyebrow">Identyfikacja wizualna • branding</p>'],
          ['Tworzymy <strong>logo, identyfikacje wizualne i brandbooki</strong>, które dobrze wyglądają nie tylko na prezentacji. Projektujemy system gotowy na stronę internetową, social media, druk, opakowania i materiały sprzedażowe.', 'Projektujemy <strong>identyfikację wizualną firm: logo, kolory, typografię i księgę znaku</strong>. Budujemy system, dzięki któremu marka wygląda spójnie na stronie, w social mediach, w druku i w materiałach sprzedażowych — dla firm z całej Polski.'],
          ['<h2 class="section-title"> Wybierz zakres dopasowany do <strong>etapu Twojej marki.</strong> </h2>', '<h2 class="section-title">Ile kosztuje identyfikacja wizualna? <strong>Wybierz zakres.</strong></h2>'],
          ['z pełną księgą znaku i zasadami wdrożenia.', 'z pełną księgą znaku i zasadami wdrożenia. Ceny logo i identyfikacji wizualnej opisujemy w poradniku ' . $L($cost_logo, 'ile kosztuje identyfikacja wizualna') . '.'],
          ['<h2 class="section-title"> Logo otwiera system. <strong class="gradient-text" >Branding pilnuje spójności.</strong > </h2>', '<h2 class="section-title">Co obejmuje identyfikacja wizualna? <strong class="gradient-text">Cały system marki.</strong></h2>'],
          ['na dokumentach, wizytówkach, stronie i materiałach firmowych.', 'na dokumentach, wizytówkach, stronie i materiałach firmowych. Punktem wyjścia jest zawsze znak — zobacz, jak wygląda ' . $L('/projektowanie-logo/', 'projektowanie logo') . '.' . $AS('/logo-branding/projekt-wizytowki/', ' Jak przygotować ', 'projekt wizytówki', ' do druku, pokazujemy w osobnym poradniku.') . $AS('/logo-branding/papier-firmowy/', ' Co powinien zawierać ', 'wzór papieru firmowego', ', opisujemy w poradniku o papierze firmowym.')],
          ['<h2 class="section-title"> Różne branże. <strong>Żadnego jednego stylu na wszystko.</strong> </h2>', '<h2 class="section-title">Realizacje brandingowe. <strong>Żadnego jednego stylu na wszystko.</strong></h2>'],
          ['<h2 class="section-title"> Od briefu do plików <strong>gotowych do wdrożenia.</strong> </h2>', '<h2 class="section-title">Proces: od strategii marki <strong>do wdrożenia.</strong></h2>'],
          ['<h2> System gotowy do pracy. <strong class="gradient-text">Nie pojedynczy plik.</strong> </h2>', '<h2>Księga znaku i brandbook. <strong class="gradient-text">System gotowy do pracy.</strong></h2>'],
          ['gotowe do przekazania drukarni, webmasterowi albo zespołowi marketingu.', 'gotowe do przekazania drukarni, webmasterowi albo zespołowi marketingu. Zobacz, co zawiera ' . $L('/logo-branding/ksiega-znaku-co-to-jest-co-zawiera-i-kiedy-firma-jej-potrzebuje/', 'księga znaku') . '.'],
          ['<p class="section-kicker">Katowice • Śląsk • cała Polska</p>', '<p class="section-kicker">Cała Polska • siedziba w Katowicach</p>'],
          ['Z Katowic projektujemy marki dla firm z całej Polski. Brief, prezentacje kierunków, konsultacje i przekazanie plików możemy przeprowadzić online — etap po etapie.', 'Projektujemy identyfikację wizualną dla firm z całej Polski. Warsztat marki, prezentacje kierunków, konsultacje i przekazanie plików prowadzimy online — etap po etapie. Firmy ze Śląska zapraszamy do biura: ' . $local('logo-katowice', 'branding w Katowicach') . '.'],
          ['<p class="section-kicker">FAQ / logo i branding</p>', '<p class="section-kicker">FAQ / identyfikacja wizualna</p>'],
          ['dzięki którym marka pozostaje spójna na stronie, w social mediach i druku.', 'dzięki którym marka pozostaje spójna na stronie, w social mediach i druku. Sam znak projektujemy w ramach usługi ' . $L('/projektowanie-logo/', 'projektowanie logo') . '.'],
          ['odpowiadały obecnemu etapowi rozwoju firmy.', 'odpowiadały obecnemu etapowi rozwoju firmy. Szczegóły w poradniku ' . $L('/logo-branding/rebranding-firmy/', 'rebranding firmy') . '.'],
        ], $demand_pl ? zp_seo_service_national_copy('identity') : []),
      ];

    case 'logo-local':
      return [
        'kind' => 'logo',
        // 2.7.0 (Mat): packages, process and files are back.
        'drop' => ['class="section manifesto"', 'id="system"'],
        'h1' => 'Projektowanie logo <strong class="gradient-text">i branding w Katowicach</strong>',
        'faq' => [
          ['keep', 1],
          ['keep', 3],
          ['new', 'Czy możemy zrobić warsztat marki w Katowicach?', 'Tak. Warsztat marki prowadzimy w naszym biurze przy <strong>ul. Modelarskiej 18/2 w Katowicach</strong>. Omawiamy charakter firmy, odbiorców, konkurencję i miejsca użycia znaku. Kolejne etapy — prezentacje i odbiory — mogą odbywać się w biurze albo online.'],
          ['keep', 8], ['keep', 9],
          ['new', 'Gdzie znajdę więcej o logo i identyfikacji wizualnej?', 'Pakiety, proces i pliki, które otrzymujesz, pokazujemy wyżej na tej stronie. Więcej o samym znaku i o całym systemie marki piszemy na stronach ' . $L('/projektowanie-logo/', 'projektowanie logo') . ' oraz ' . $L('/identyfikacja-wizualna/', 'identyfikacja wizualna') . '.' . $AS('/agencja-reklamowa-katowice/', ' Stronę i reklamy w nowej estetyce przygotuje ta sama ', 'agencja reklamowa z siedzibą w Katowicach', '.')],
          ['new', 'Co przygotować na warsztat logo i brandingu w Katowicach?', 'Opisz odbiorców, ofertę i miejsca, w których znak ma działać: stronę, profil społecznościowy, szyld, opakowanie czy dokumenty. Przynieś dotychczasowe materiały, jeśli marka już istnieje. Na spotkaniu przy Modelarskiej 18/2 łatwiej wtedy ustalić, czy wystarczy projekt logo, czy potrzebny jest szerszy system identyfikacji.'],
          ['new', 'Czy otrzymam logo do internetu i do druku?', 'Tak, zgodnie z wybranym pakietem przekazujemy uporządkowane warianty i formaty. W sekcji „Co otrzymujesz” pokazujemy SVG, PDF, PNG i JPG oraz zastosowania na jasnym i ciemnym tle. <strong>Medical Friend</strong> to przykład systemu obejmującego oznakowanie i materiały informacyjne, a <strong>Vista Architekci</strong> — monogramu z papeterią i zasadami użycia. Różnice wyjaśniamy w poradniku ' . $L('/logo-branding/logo-wektorowe-pliki-logo/', 'pliki logo i format wektorowy') . '.'],
        ],
        'replace' => [
          ['Tworzymy <strong>logo, identyfikacje wizualne i brandbooki</strong>, które dobrze wyglądają nie tylko na prezentacji. Projektujemy system gotowy na stronę internetową, social media, druk, opakowania i materiały sprzedażowe.', 'Projektowanie logo Katowice — studio przy ul. Modelarskiej 18/2. Projektujemy <strong>logo i identyfikację wizualną</strong> dla firm z Katowic i Śląska — z warsztatem marki w naszym biurze albo w pełni online.'],
          ['<p class="section-kicker">Katowice • Śląsk • cała Polska</p>', '<p class="section-kicker">Katowice • ul. Modelarska 18/2 • Śląsk</p>'],
          ['<h2 class="section-title"> Jedno studio. <strong>Współpraca bez granic lokalizacji.</strong> </h2>', '<h2 class="section-title">Warsztat marki <strong>w Katowicach.</strong></h2>'],
          ['Z Katowic projektujemy marki dla firm z całej Polski. Brief, prezentacje kierunków, konsultacje i przekazanie plików możemy przeprowadzić online — etap po etapie.', 'Na warsztat marki zapraszamy do naszego biura przy ul. Modelarskiej 18/2 w Katowicach. Pełną ofertę opisujemy na stronach ' . $L('/projektowanie-logo/', 'projektowanie logo') . ' i ' . $L('/identyfikacja-wizualna/', 'identyfikacja wizualna') . '.'],
          ['Możemy spotkać się lokalnie albo przeprowadzić cały proces online — od briefu po odbiór uporządkowanych plików.', 'Spotykamy się w naszym biurze przy ul. Modelarskiej 18/2 albo prowadzimy cały proces online — od briefu po odbiór uporządkowanych plików.'],
          ['Dodatkowe materiały, opakowania lub rozbudowane wdrożenie wyceniamy indywidualnie.', 'Dodatkowe materiały, opakowania lub rozbudowane wdrożenie wyceniamy indywidualnie. Więcej w poradniku ' . $L($cost_logo, 'ile kosztuje logo') . '.'],
          ['Różne branże. <strong>Żadnego jednego stylu na wszystko.</strong>', 'Projektowanie logo w Katowicach. <strong>Różne branże i zastosowania.</strong>'],
          ['Każdy projekt zaczyna się od innego problemu i kończy systemem dopasowanym do odbiorców, zastosowań i poziomu marki.', '<strong>Siemianowski</strong> — logo i materiały kancelarii. <strong>Vista Architekci</strong> — monogram, brandbook i papeteria. <strong>Medical Friend</strong> — identyfikacja oraz oznakowanie. Wybierz realizację i zobacz, jak znak pracuje w różnych materiałach; zakres dla Twojej firmy ustalimy na warsztacie w Katowicach lub online.'],
        ],
      ];
  }
  return null;
}

/**
 * Polish copy of the nationwide pages in the sections they shared word for word with their Katowice page
 * (SEO audit of 13.10.2026, list B: 72% common text on /tworzenie-stron-internetowych/, 67% between logo and
 * identity). Same elements and about the same length, so the layout stays; headings carry the plan's phrases
 * instead of slogans alone. Polish only: the English pages keep the text their dictionary was made from.
 * Kind => [old, new] pairs for zp_seo_service_transform().
 */
function zp_seo_service_national_copy(string $kind): array {
  $copy = [
    'strony' => [
      ['po wdrożenie WordPress, SEO techniczne, analitykę i szybkie formularze. Wszystko pod realne zapytania — nie tylko ładną wizytówkę.', 'po wdrożenie na WordPressie, SEO techniczne i pomiar zapytań. Każda strona ma prowadzić klienta do kontaktu, a nie tylko dobrze wyglądać.'],
      ['<h2 class="section-title">Strona porządkuje ofertę. <strong>Sklep domyka sprzedaż.</strong></h2>', '<h2 class="section-title">Strona firmowa porządkuje ofertę. <strong>Sklep domyka sprzedaż.</strong></h2>'],
      ['<h2 class="section-title">Inna branża. <strong>Inna droga do klienta.</strong></h2>', '<h2 class="section-title">Strony dla branż. <strong>Inna droga do klienta.</strong></h2>'],
      ['Inaczej projektuje się stronę kancelarii, inaczej kliniki, salonu beauty, dewelopera czy producenta. Dlatego strukturę, treści, UX i SEO budujemy <strong>pod konkretną branżę</strong> — tak, żeby strona realnie prowadziła klienta do kontaktu.', 'Klient kancelarii szuka zaufania, pacjent kliniki spokoju, a kupujący od dewelopera konkretów o lokalach. Zanim zaprojektujemy stronę, poznajemy <strong>pytania i obawy klientów Twojej branży</strong> — od nich zależą struktura, treści, UX i SEO.'],
      ['Nie widzisz swojej branży? Opisz projekt w Studio Wyceny — dopasujemy strukturę strony, SEO i zakres wdrożenia do Twojej firmy.', 'Twojej branży nie ma na liście? Opisz firmę w Studio Wyceny — zaproponujemy strukturę strony, treści i zakres wdrożenia.'],
      ['Nie zaczynamy od przypadkowego układu ani szablonowej sekcji. Najpierw poznajemy firmę, ofertę i cel strony — a każdy etap ma', 'Tworzenie strony internetowej dzielimy na sześć etapów: od celu i briefu po publikację. Każdy etap ma'],
      ['Wybierasz punkt startu: landing page, strona firmowa Starter, Premium albo projekt indywidualny — zawsze pod konkretny cel biznesowy.', 'Ustalamy, co strona ma przynosić: zapytania, rezerwacje albo sprzedaż. Do celu dobieramy pakiet, od landing page po projekt indywidualny.'],
      ['W konfiguratorze podajesz branżę, funkcje, materiały i termin. Możesz wgrać logo, teksty, inspiracje i link do obecnej strony.', 'Brief wypełniasz online: branża, funkcje, termin, logo, teksty i strony, które Ci się podobają.'],
      ['Sprawdzamy zakres, cele, SEO i wymagania techniczne. Interaktywną ofertę otrzymujesz zwykle w 3–4 dni robocze.', 'Sprawdzamy cele, frazy SEO i technikę. Ofertę z zakresem, ceną i terminem dostajesz zwykle w 3–4 dni robocze.'],
      ['Projektujemy architekturę podstron, makiety, treści i wersję mobilną — z myślą o ścieżce od wejścia do kontaktu.', 'Układamy podstrony, makiety i treści, a potem projekt graficzny. Wersję na telefon projektujemy od początku, nie na końcu.'],
      ['Budujemy stronę, konfigurujemy SEO techniczne, analitykę, formularze, szybkość ładowania i bezpieczeństwo.', 'Budujemy na WordPressie: szybko, bezpiecznie i z łatwą edycją. Ustawiamy SEO techniczne, analitykę i formularze.'],
      ['Nie zamykamy projektu na sztucznej liczbie poprawek. Dopracowujemy stronę, aż będzie spójna z marką i zaakceptowana.', 'Liczba poprawek nie jest sztucznie ograniczona. Dopracowujemy stronę, aż będzie spójna z marką i gotowa do publikacji.'],
      ['<h2 class="section-title">Dwie marki. Dwa cele. <strong>Jeden sposób myślenia.</strong></h2>', '<h2 class="section-title">Dwie strony. Dwa cele. <strong>Jeden sposób myślenia.</strong></h2>'],
      ['Wynik strony powstaje na styku struktury, treści, techniki i obsługi zapytań. Dlatego pokazujemy nie tylko projekt, ale też liczby, które przyszły po wdrożeniu.', 'Projekt strony internetowej oceniamy po tym, co dzieje się po publikacji. Dlatego przy realizacjach pokazujemy liczby z pierwszych sześciu miesięcy.'],
      ['Kompleksowy redesign wizerunku online: odświeżona identyfikacja, pełny projekt strony i autorski system rezerwacji wizyt. Struktura i treści ułożone pod lokalne frazy oraz szybkie umówienie wizyty — od pierwszego wejścia po potwierdzoną rezerwację.', 'Nowa identyfikacja, pełny projekt strony i autorski system rezerwacji wizyt. Treści ułożyliśmy pod lokalne frazy, a droga od pierwszego wejścia do potwierdzonej rezerwacji ma dwa kroki i nie wymaga telefonu.'],
      ['Elegancka strona firmowa z rozbudowaną strukturą podstron usługowych, premium oprawą wizualną i systemem umawiania wizyt. Projekt łączy zaufanie, ekspercki charakter i wygodną ścieżkę pierwszego kontaktu.', 'Strona firmowa kancelarii z 14 podstronami usług, każdą pod osobne frazy. Oprawa premium buduje zaufanie, a system umawiania wizyt skraca drogę od eksperckiej treści do konsultacji.'],
      ['Zakres dobieramy do etapu firmy i celu strony. Bez pakowania niepotrzebnych elementów do oferty.', 'Projektowanie stron www to więcej niż wygląd. Zakres dobieramy do celu, bez zbędnych dodatków.'],
      ['Zaczynamy od celu biznesowego: co strona ma przynosić i komu ma służyć. Projektujemy architekturę podstron, ścieżkę użytkownika od wejścia do kontaktu, plan treści oraz strukturę pod SEO lokalne i usługowe — zanim powstanie pierwszy piksel projektu.', 'Zanim powstanie pierwszy piksel, ustalamy, komu strona ma służyć i co ma przynosić. Na tej podstawie planujemy podstrony, ścieżkę od wejścia do kontaktu, treści i strukturę pod frazy, którymi klienci szukają Twoich usług w Google.'],
      ['Makiety, projekt graficzny, copywriting, mikrocopy i sekcje zaufania — spójne z marką i pisane pod decyzję użytkownika.', 'Makiety, projekt i teksty powstają razem, więc nagłówki, przyciski i opinie prowadzą do jednej decyzji.'],
      ['Czyste, rozwijalne wdrożenie — po starcie łatwo dodasz blog, podstrony, landing pages albo sklep.', 'Strona na WordPressie, którą edytujesz sam. Blog, landing page czy sklep dodasz bez przebudowy.'],
      ['Nagłówki, schema, indeksacja, Core Web Vitals, optymalizacja obrazów i mobile-first od pierwszego dnia.', 'Nagłówki, dane strukturalne, mapa witryny, lekkie obrazy i dobre Core Web Vitals od dnia publikacji.'],
      ['GA4, zdarzenia, szybkie formularze, integracje i pomiar zapytań — żeby było widać, co strona naprawdę przynosi.', 'GA4, zdarzenia i formularze ustawiamy tak, żeby każde zapytanie było policzone, a rozwój strony oparty na danych.'],
      ['<h2 class="section-title">Strona ma wyglądać premium. <strong class="gradient-text">I działać jak premium.</strong></h2>', '<h2 class="section-title">Strona www ma wyglądać premium. <strong class="gradient-text">I działać jak premium.</strong></h2>'],
      ['<h2>Strona nie żyje <strong class="gradient-text">w osobnym silosie.</strong></h2>', '<h2>Strony www <strong class="gradient-text">od jednego zespołu.</strong></h2>'],
      ['W jednym zespole łączymy projekt, treść, WordPress, sklepy, branding i kampanie. Dzięki temu strona od początku powstaje pod SEO i marketing — a problem nie krąży między pięcioma wykonawcami.', 'Projekt, teksty, wdrożenie, logo i kampanie prowadzi u nas jeden zespół. Dzięki temu strona od początku powstaje pod SEO i reklamy, a zadania nie krążą między pięcioma wykonawcami.'],
      ['<h2 class="section-title"> Jedno studio. <strong>Współpraca bez granic lokalizacji.</strong> </h2>', '<h2 class="section-title">Jedno studio. <strong>Strony dla firm z całej Polski.</strong></h2>'],
      ['<h2 class="section-title">Zanim klikniesz <strong>„start”.</strong></h2>', '<h2 class="section-title">Pytania o <strong>tworzenie stron.</strong></h2>'],
      ['Konkretnie o zakresie, cenach, materiałach, SEO i tym, czego potrzebujemy, żeby zacząć projekt strony.', 'Odpowiadamy na pytania, które słyszymy przed każdym projektem: o cenę, czas, materiały, SEO i opiekę po publikacji.'],
      ['<h2>Pokaż firmę tak, jak <strong class="gradient-text">na to zasługuje.</strong></h2>', '<h2>Ile kosztuje strona <strong class="gradient-text">dla Twojej firmy?</strong></h2>'],
      ['Opisz projekt w Studio Wyceny: branżę, cel strony, funkcje i materiały. Interaktywną ofertę z zakresem i terminem otrzymasz zwykle w 3–4 dni robocze.', 'Strony tworzymy od 3 999 zł, z domeną i hostingiem w cenie. W Studio Wyceny policzysz swój projekt, a ofertę dostaniesz zwykle w 3–4 dni robocze.'],
    ],
    'logo' => [
      ['Każdy etap kończy się konkretną decyzją.', 'Projektowanie logo to siedem kroków i siedem decyzji.'],
      ['Poznajemy branżę, odbiorców, konkurencję i miejsca użycia logo.', 'Pytamy o branżę, klientów, konkurencję i miejsca użycia znaku.'],
      ['<p>Porządkujemy skojarzenia, ton, kolorystykę i typografię.</p>', '<p>Wybieramy skojarzenia, kolory i kroje pisma dla znaku.</p>'],
      ['Przygotowujemy kierunki wynikające ze strategii i wybranego pakietu.', 'Pokazujemy koncepcje oparte na strategii; ich liczba zależy od pakietu.'],
      ['<p>Pracujemy nad proporcjami, typografią i wariantami logo.</p>', '<p>Szlifujemy proporcje, odstępy i krój pisma logotypu.</p>'],
      ['Sprawdzamy znak na stronie, wizytówce, avatarze, dokumencie, opakowaniu i szyldzie.', 'Znak sprawdzamy w małym i dużym formacie: od faviconu i avatara po szyld i opakowanie.'],
      ['Budujemy system kolorów, fontów, układów i materiałów marki.', 'W większych pakietach dokładamy kolory, fonty i materiały marki.'],
      ['Przekazujemy uporządkowane formaty oraz jasne zasady użycia.', 'Dostajesz logo w SVG, PDF, PNG i JPG oraz zasady jego użycia.'],
      ['Zakres zależy od pakietu, ale zawsze przygotowujemy materiały z myślą o realnym wdrożeniu.', 'Logo dostajesz w wariantach i formatach na stronę, do social mediów, na wizytówkę i do druku.'],
      ['<p>Układy poziome i pionowe oraz wersje na jasne i ciemne tło.</p>', '<p>Układ poziomy i pionowy, gotowy na jasne i ciemne tło.</p>'],
      ['<p>SVG, PDF, PNG i JPG — gotowe do digitalu, dokumentów i druku.</p>', '<p>Wektorowe SVG i PDF oraz PNG i JPG do sieci i dokumentów.</p>'],
      ['<p>Spójne wartości kolorów, fonty i zasady budowania układów.</p>', '<p>Wartości kolorów do druku i na ekran oraz fonty użyte w znaku.</p>'],
      ['<p>Karta stylu albo pełny brandbook — zgodnie z wybranym zakresem.</p>', '<p>Zasady użycia znaku w karcie stylu albo brandbooku.</p>'],
      ['<p>Favicon, avatar, cover i elementy do social mediów.</p>', '<p>Favicon, avatar i cover do profili społecznościowych.</p>'],
      ['<p>Pliki przygotowane pod konkretne materiały i ustalony zakres.</p>', '<p>Pliki dla drukarni pod materiały objęte zakresem.</p>'],
      ['Każdy projekt zaczyna się od innego problemu i kończy systemem dopasowanym do odbiorców, zastosowań i poziomu marki.', 'Każde logo projektujemy od zera: pod branżę, odbiorców i miejsca, w których znak będzie pracować na co dzień.'],
      ['<h2> Branding nie kończy się <strong class="gradient-text">na pliku.</strong> </h2>', '<h2>Logo firmy nie kończy się <strong class="gradient-text">na pliku.</strong></h2>'],
      ['Ten sam zespół może przenieść markę na stronę, sklep, social media i kampanie. Dzięki temu identyfikacja nie rozjeżdża się przy pierwszym wdrożeniu.', 'Nowe logo możemy od razu wdrożyć na stronie, w sklepie, social mediach i kampaniach, więc znak wszędzie wygląda tak, jak go zaprojektowaliśmy.'],
      ['<h2 class="section-title"> Jedno studio. <strong>Współpraca bez granic lokalizacji.</strong> </h2>', '<h2 class="section-title">Jedno studio. <strong>Projektowanie logo online.</strong></h2>'],
      ['<h2> Zbudujmy markę, którą da się rozpoznać <strong class="gradient-text">bez czytania nazwy.</strong> </h2>', '<h2>Projekt logo, które klienci rozpoznają <strong class="gradient-text">bez czytania nazwy.</strong></h2>'],
      ['<h2 class="section-title"> Zanim zaczniemy <strong>budować markę.</strong> </h2>', '<h2 class="section-title">Pytania o <strong>projekt logo.</strong></h2>'],
      ['W Studio Wyceny opiszesz etap rozwoju marki, potrzebny zakres identyfikacji i miejsca wdrożenia. Na tej podstawie zaproponujemy właściwy punkt startu.', 'Projekt logo kosztuje od 999 zł. W Studio Wyceny opiszesz firmę i miejsca, w których znak ma działać, a my zaproponujemy właściwy pakiet.'],
    ],
    'identity' => [
      ['Każdy etap kończy się konkretną decyzją. Wiesz, nad czym pracujemy, po co i co wydarzy się dalej.', 'Identyfikację budujemy w siedmiu etapach. Po każdym wiesz, co ustaliliśmy i co dalej.'],
      ['Poznajemy branżę, odbiorców, konkurencję i miejsca użycia logo.', 'Ustalamy, kim są odbiorcy marki i jak ma wyróżnić się na rynku.'],
      ['<p>Porządkujemy skojarzenia, ton, kolorystykę i typografię.</p>', '<p>Strategię zamieniamy na ton, paletę i typografię.</p>'],
      ['Przygotowujemy kierunki wynikające ze strategii i wybranego pakietu.', 'Projektujemy znak, który będzie sercem całego systemu identyfikacji.'],
      ['<p>Pracujemy nad proporcjami, typografią i wariantami logo.</p>', '<p>Dopracowujemy logo i warianty, na których oprze się system.</p>'],
      ['Sprawdzamy znak na stronie, wizytówce, avatarze, dokumencie, opakowaniu i szyldzie.', 'System testujemy na realnych nośnikach: stronie, dokumentach, opakowaniu i szyldzie.'],
      ['Budujemy system kolorów, fontów, układów i materiałów marki.', 'Projektujemy kolory, kroje, układy i wzory materiałów.'],
      ['Przekazujemy uporządkowane formaty oraz jasne zasady użycia.', 'Przekazujemy pliki i księgę znaku albo kartę stylu.'],
      ['Zakres zależy od pakietu, ale zawsze przygotowujemy materiały z myślą o realnym wdrożeniu.', 'Identyfikacja wizualna to zasady i pliki dla każdego, kto tworzy materiały Twojej marki.'],
      ['<p>Układy poziome i pionowe oraz wersje na jasne i ciemne tło.</p>', '<p>Logo i jego wersje dopasowane do każdego nośnika i tła.</p>'],
      ['<p>SVG, PDF, PNG i JPG — gotowe do digitalu, dokumentów i druku.</p>', '<p>Pliki wektorowe i rastrowe dla drukarni, webmastera i agencji.</p>'],
      ['<p>Spójne wartości kolorów, fonty i zasady budowania układów.</p>', '<p>Paleta, kroje pisma i zasady układów dla materiałów.</p>'],
      ['<p>Karta stylu albo pełny brandbook — zgodnie z wybranym zakresem.</p>', '<p>Brandbook albo karta stylu: zasady dla Ciebie i podwykonawców.</p>'],
      ['<p>Favicon, avatar, cover i elementy do social mediów.</p>', '<p>Grafiki do social mediów i favicon w nowych barwach.</p>'],
      ['<p>Pliki przygotowane pod konkretne materiały i ustalony zakres.</p>', '<p>Wzory materiałów firmowych gotowe do druku.</p>'],
      ['<h2> Branding nie kończy się <strong class="gradient-text">na pliku.</strong> </h2>', '<h2>Branding firmy nie kończy się <strong class="gradient-text">na pliku.</strong></h2>'],
      ['Ten sam zespół może przenieść markę na stronę, sklep, social media i kampanie. Dzięki temu identyfikacja nie rozjeżdża się przy pierwszym wdrożeniu.', 'Ten sam zespół może wdrożyć identyfikację na stronie, w sklepie, social mediach i kampaniach, więc system nie rozjedzie się przy pierwszym użyciu.'],
      ['<h2 class="section-title"> Jedno studio. <strong>Współpraca bez granic lokalizacji.</strong> </h2>', '<h2 class="section-title">Jedno studio. <strong>Branding bez granic lokalizacji.</strong></h2>'],
      ['<h2 class="section-title"> Zanim zaczniemy <strong>budować markę.</strong> </h2>', '<h2 class="section-title">Pytania o <strong>branding firmy.</strong></h2>'],
      ['W Studio Wyceny opiszesz etap rozwoju marki, potrzebny zakres identyfikacji i miejsca wdrożenia. Na tej podstawie zaproponujemy właściwy punkt startu.', 'Mini Branding kosztuje od 1499 zł, a Branding Premium z księgą znaku od 2999 zł. W Studio Wyceny opiszesz markę, a my zaproponujemy zakres.'],
    ],
  ];
  return $copy[$kind] ?? [];
}

/* ------------------------------------------------------------ page creation */

/**
 * Creates the nationwide pages as copies of their Katowice page (content, template and
 * Elementor layout). Existing pages are left alone. Returns log lines.
 */
function zp_seo_service_create_pages(): array {
  $log = [];
  $skip = ['_edit_lock', '_edit_last', '_wp_old_slug', '_wp_old_date', '_elementor_css', '_elementor_page_assets', '_elementor_element_cache', '_elementor_screenshot', '_wp_trash_meta_status', '_wp_trash_meta_time', '_zp_seo_plan_created'];
  foreach (zp_seo_service_registry() as $path => $r) {
    if (empty($r['source'])) { continue; }
    $slug = trim($path, '/');
    $existing = get_page_by_path($slug, OBJECT, 'page');
    if ($existing) {
      if ($existing->post_status !== 'publish' && get_post_meta($existing->ID, '_zp_seo_plan_created', true)) {
        wp_update_post(['ID' => $existing->ID, 'post_status' => 'publish']);
        $log[] = 'Opublikowano ponownie stronę ' . $path;
      } else {
        $log[] = 'Strona ' . $path . ' już istnieje (ID ' . $existing->ID . ') — bez zmian.';
      }
      continue;
    }
    $source = get_page_by_path(trim($r['source'], '/'), OBJECT, 'page');
    $content = $source ? (string) $source->post_content : '';
    if (strpos($content, '[' . $r['shortcode']) === false && !preg_match('~\[zp_page_(?:strony|sklepy|logo_branding)~', $content)) {
      $content = "[zp_header]\n[" . $r['shortcode'] . "]\n[zp_footer]";
    }
    $id = wp_insert_post(wp_slash([
      'post_type' => 'page', 'post_status' => 'publish', 'post_title' => $r['title'], 'post_name' => $slug,
      'post_content' => $content, 'post_author' => $source ? (int) $source->post_author : get_current_user_id(),
      'comment_status' => 'closed', 'ping_status' => 'closed',
    ]), true);
    if (is_wp_error($id) || !$id) {
      $log[] = 'BŁĄD: nie udało się utworzyć ' . $path . (is_wp_error($id) ? ' — ' . $id->get_error_message() : '');
      continue;
    }
    if ($source) {
      foreach (get_post_meta($source->ID) as $key => $values) {
        if (in_array($key, $skip, true) || strpos($key, 'rank_math_') === 0 || strpos($key, '_yoast_') === 0) { continue; }
        foreach ((array) $values as $value) { add_post_meta($id, $key, wp_slash(maybe_unserialize($value))); }
      }
    }
    update_post_meta($id, '_zp_seo_plan_created', ZP_SEO_PLAN_VERSION);
    $real = zp_seo_plan_path((string) get_permalink($id));
    $log[] = 'Utworzono stronę ' . $path . ' (ID ' . $id . ($source ? ', kopia ' . $r['source'] : ', prosty układ') . ')' . ($real !== $path ? ' — UWAGA: adres to ' . $real : '');
  }
  return $log;
}
