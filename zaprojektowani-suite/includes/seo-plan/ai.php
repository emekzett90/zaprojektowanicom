<?php
if (!defined('ABSPATH')) { exit; }

/**
 * ZP Suite — Widoczność AI (ChatGPT, Claude, Gemini, Perplexity, Copilot), thread "Widoczność w AI", 7.10.2026.
 *
 * AI assistants use search services and their own fetchers. Bing is one useful search index, not the
 * only source of ChatGPT answers. Nothing here changes what people
 * see on the pages:
 *  - robots.txt names the AI crawlers and lets them in on the same rules as search engines,
 *  - /llms.txt: a short, current description of the studio for language models (services, prices, deadlines,
 *    contact, key pages and guides, English pages), built from the plugin's own data. An old static llms.txt
 *    in the site root (it listed only the Katowice pages) is kept in the database and removed (2.9.1 renamed
 *    it to llms.txt.bak, which stayed public; 2.9.6 deletes that copy). Guides that show another article's
 *    text, and posts that redirect, are left out of the list (2.9.6),
 *  - structured data: the prices ("od 3 999 zł") as offers of the business and of each service page, the team
 *    as employees, more profiles in sameAs (Zaprojektowani Suite → Widoczność AI); English pages get them
 *    translated by the languages module,
 *  - IndexNow: Bing (ChatGPT, Copilot), Yandex, Seznam and Naver learn about new and changed pages within
 *    minutes (unless Rank Math's Instant Indexing module already sends them) and get the full list of
 *    addresses once after install. Posts that change at display time through the content feed (answer
 *    sections, links to new articles) are sent too and get the date of the change in the sitemap (2.9.6),
 *  - Zaprojektowani Suite → Widoczność AI: visits of AI crawlers, visitors coming from ChatGPT, Gemini, Claude,
 *    Perplexity and Copilot (counted from the suite's own page-view beacon), the state of llms.txt and IndexNow,
 *    an access test with the crawlers' user agents and the profile links.
 * Pausing the SEO plan (Narzędzia → Plan SEO) switches all of it off.
 */

/**
 * Bump to run the one-time setup again (full IndexNow submission, llms.txt file check).
 * 2: 2.9.6 corrected texts in older posts and the project count on the website pages; Bing gets every
 * address again so ChatGPT and Copilot read the new versions sooner. It also deletes /llms.txt.bak.
 * 3: 2.9.6 also follows the posts changed by the content feed; the first run dates the answer sections and
 * links the feed already shows, so their sitemap entries say they changed.
 */
const ZP_AI_VERSION = '4';
const ZP_AI_LOG = 'zp_ai_log';
const ZP_AI_LLMS_MARK = '<!-- Zaprojektowani Suite: llms.txt -->';

function zp_ai_on(): bool {
  return zp_seo_plan_active();
}

function zp_ai_log($lines): void {
  $log = (array) get_option(ZP_AI_LOG, []);
  $stamp = wp_date('Y-m-d H:i');
  foreach ((array) $lines as $line) { $log[] = $stamp . '  ' . $line; }
  update_option(ZP_AI_LOG, array_slice($log, -200), false);
}

/* ---------------------------------------------------------------------------------------------------------
 * Facts: one place for what llms.txt and the schema say. The prices and deadlines are the ones Mat confirmed
 * (Studio wyceny, studio.php); change them there and here together.
 * ------------------------------------------------------------------------------------------------------ */

function zp_ai_offers(): array {
  return (array) apply_filters('zp_ai_offers', [
    'strony' => [
      'name' => 'Strona internetowa', 'name_en' => 'Website', 'min' => 3999, 'unit' => '',
      'path' => '/tworzenie-stron-internetowych/', 'time' => 'zwykle 2–4 tygodnie', 'time_en' => 'usually 2–4 weeks',
      'note' => 'domena i hosting w cenie', 'note_en' => 'domain and hosting included',
    ],
    'sklepy' => [
      'name' => 'Sklep internetowy WooCommerce', 'name_en' => 'WooCommerce online store', 'min' => 6499, 'unit' => '',
      'path' => '/tworzenie-sklepow-internetowych/', 'time' => 'zwykle 3–6 tygodni', 'time_en' => 'usually 3–6 weeks',
      'note' => '', 'note_en' => '',
    ],
    'logo' => [
      'name' => 'Projekt logo', 'name_en' => 'Logo design', 'min' => 999, 'unit' => '',
      'path' => '/projektowanie-logo/', 'time' => 'zwykle 7–14 dni', 'time_en' => 'usually 7–14 days',
      'note' => 'stałe pakiety: Logo Startowe 999 zł, Mini Branding 1 499 zł, Branding Premium 2 999 zł',
      'note_en' => 'fixed packages: PLN 999, 1,499 and 2,999',
    ],
    'identyfikacja' => [
      'name' => 'Identyfikacja wizualna', 'name_en' => 'Visual identity', 'min' => 1499, 'unit' => '',
      'path' => '/identyfikacja-wizualna/', 'time' => '', 'time_en' => '',
      'note' => 'pełny system z księgą znaku w pakiecie Branding Premium: 2 999 zł',
      'note_en' => 'full system with a brand book in the Branding Premium package: PLN 2,999',
    ],
    'kampanie' => [
      'name' => 'Kampanie Meta Ads i Google Ads', 'name_en' => 'Meta Ads and Google Ads campaigns', 'min' => 1200, 'unit' => 'MON',
      'path' => '/kampanie-reklamowe/', 'time' => 'start w 7 dni', 'time_en' => 'start within 7 days',
      'note' => 'stała obsługa: strategia, kreacje, pomiar i optymalizacja; budżet mediowy osobno',
      'note_en' => 'ongoing management; media budget paid separately',
    ],
  ]);
}

/** Offer key for a service page variant (schema.php), '*' for all offers, or ''. */
function zp_ai_offer_for_variant(string $variant): string {
  // /agencja-reklamowa-katowice/ (2.9.0) covers every service.
  if (strpos($variant, 'agencja-reklamowa') !== false) { return '*'; }
  if ($variant === 'identity-national') { return 'identyfikacja'; }
  if ($variant === 'content-opieka-wordpress') { return ''; }
  if (strpos($variant, 'sklepy-') === 0) { return 'sklepy'; }
  if (strpos($variant, 'logo-') === 0) { return 'logo'; }
  if (strpos($variant, 'strony-') === 0 || strpos($variant, 'content-') === 0) { return 'strony'; }
  return '';
}

/** "3 999 zł" with a no-break-free plain space (llms.txt is plain text). */
function zp_ai_pln(int $amount): string {
  return number_format($amount, 0, ',', ' ') . ' zł';
}

function zp_ai_offer_text(array $o, bool $with_time = true): string {
  $out = 'od ' . zp_ai_pln((int) $o['min']) . ($o['unit'] === 'MON' ? '/mies.' : '');
  if ($o['note'] !== '') { $out .= ' (' . $o['note'] . ')'; }
  if ($with_time && $o['time'] !== '') { $out .= ', ' . $o['time']; }
  return $out;
}

/* ---------------------------------------------------------------------------------------------------------
 * Crawlers.
 * ------------------------------------------------------------------------------------------------------ */

/**
 * AI crawlers and fetchers: user-agent token => [service, purpose]. Purpose: search (answers with links),
 * user (opens a page someone asked about), train (model training), token (a robots.txt switch only: Google
 * and Apple crawl as Googlebot and Applebot). bingbot and Googlebot are counted for comparison: ChatGPT and
 * Copilot answer from Bing's index, Gemini from Google's.
 */
function zp_ai_bots(): array {
  return [
    'OAI-SearchBot' => ['ChatGPT (wyszukiwanie)', 'search'],
    'ChatGPT-User' => ['ChatGPT (otwiera stronę dla użytkownika)', 'user'],
    'GPTBot' => ['OpenAI (trening modeli)', 'train'],
    'Claude-SearchBot' => ['Claude (wyszukiwanie)', 'search'],
    'Claude-User' => ['Claude (otwiera stronę dla użytkownika)', 'user'],
    'ClaudeBot' => ['Anthropic (trening modeli)', 'train'],
    'PerplexityBot' => ['Perplexity (wyszukiwanie)', 'search'],
    'Perplexity-User' => ['Perplexity (otwiera stronę dla użytkownika)', 'user'],
    'Google-Extended' => ['Gemini (trening i grounding; token robots.txt)', 'token'],
    'Applebot-Extended' => ['Apple Intelligence (zgoda na użycie treści)', 'token'],
    'DuckAssistBot' => ['DuckDuckGo (odpowiedzi AI)', 'search'],
    'MistralAI-User' => ['Mistral Le Chat', 'user'],
    'meta-externalagent' => ['Meta AI (trening modeli)', 'train'],
    'meta-externalfetcher' => ['Meta AI (otwiera stronę dla użytkownika)', 'user'],
    'Amazonbot' => ['Amazon (Alexa, Rufus)', 'search'],
    'CCBot' => ['Common Crawl (dane do trenowania wielu modeli)', 'train'],
    'bingbot' => ['Bing (indeks wyszukiwarki)', 'search'],
    'Googlebot' => ['Google: indeks Gemini i AI Overviews', 'search'],
  ];
}

/** Token of the AI crawler in a user agent, or ''. Longer tokens first (Claude-SearchBot before ClaudeBot). */
function zp_ai_bot_of(string $ua): string {
  if ($ua === '') { return ''; }
  static $tokens = null;
  if ($tokens === null) {
    $tokens = array_keys(array_filter(zp_ai_bots(), static function ($b) { return $b[1] !== 'token'; }));
    usort($tokens, static function ($a, $b) { return strlen($b) <=> strlen($a); });
  }
  foreach ($tokens as $token) {
    if (stripos($ua, $token) !== false) { return $token; }
  }
  return '';
}

/** Allow and Disallow lines of the "User-agent: *" group of a robots.txt. */
function zp_ai_robots_rules_for_all(string $robots): array {
  $rules = [];
  $in = false;
  $after_agent = false;
  foreach (preg_split('~\r?\n~', $robots) as $line) {
    $line = trim((string) preg_replace('~#.*$~', '', $line));
    if ($line === '') { continue; }
    if (preg_match('~^user-agent\s*:\s*(.*)$~i', $line, $m)) {
      // Consecutive User-agent lines share one group; a User-agent line after rules starts a new one.
      if (!$after_agent) { $in = false; }
      $after_agent = true;
      if (trim($m[1]) === '*') { $in = true; }
      continue;
    }
    $after_agent = false;
    if ($in && preg_match('~^(allow|disallow|crawl-delay)\s*:\s*(.*)$~i', $line, $m)) {
      $rules[] = ucfirst(strtolower($m[1])) . ': ' . trim($m[2]);
    }
  }
  return $rules;
}

// robots.txt: after Rank Math's editor (10) and the languages module (99), before the plan's clean-up (PHP_INT_MAX).
add_filter('robots_txt', function ($output, $public = true) {
  if (!zp_ai_on() || !$public) { return $output; }
  $output = (string) $output;
  // A crawler that already has its own group keeps it (e.g. a block set in Rank Math's robots.txt editor).
  preg_match_all('~^\s*user-agent\s*:\s*(\S+)~im', $output, $m);
  $named = array_map('strtolower', $m[1]);
  $tokens = [];
  foreach (zp_ai_bots() as $token => $bot) {
    if (in_array($token, ['bingbot', 'Googlebot'], true) || in_array(strtolower($token), $named, true)) { continue; }
    $tokens[] = $token;
  }
  if (!$tokens) { return $output; }
  // The same rules as for every other crawler: a named group replaces the "*" group for that crawler.
  $rules = zp_ai_robots_rules_for_all($output) ?: ['Disallow: /wp-admin/', 'Allow: /wp-admin/admin-ajax.php'];
  $group = "# Asystenci i wyszukiwarki AI (ChatGPT, Claude, Gemini, Perplexity, Copilot): te same zasady co dla wyszukiwarek\n";
  foreach ($tokens as $token) { $group .= 'User-agent: ' . $token . "\n"; }
  $group .= implode("\n", $rules) . "\n\n";
  $group .= '# Opis firmy dla modeli językowych: ' . home_url('/llms.txt') . "\n";
  $lines = preg_split('~\r?\n~', rtrim($output));
  $out = [];
  $placed = false;
  foreach ($lines as $line) {
    if (!$placed && stripos(ltrim($line), 'sitemap:') === 0) {
      $out[] = rtrim($group);
      $out[] = '';
      $placed = true;
    }
    $out[] = $line;
  }
  if (!$placed) { $out[] = ''; $out[] = rtrim($group); }
  return implode("\n", $out) . "\n";
}, 120, 2);

/* ---------------------------------------------------------------------------------------------------------
 * Early routes: /llms.txt and the IndexNow key file. The languages module leaves addresses with a file
 * extension alone, so nothing redirects them first.
 * ------------------------------------------------------------------------------------------------------ */

add_action('init', function () {
  if (!zp_ai_on() || is_admin() || wp_doing_ajax() || wp_doing_cron()) { return; }
  $method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
  if (!in_array($method, ['GET', 'HEAD'], true)) { return; }
  $path = zp_seo_plan_path();
  if ($path === '/llms.txt') {
    zp_ai_send_text(zp_ai_llms_text(), true);
  }
  if (preg_match('~^/([a-f0-9]{32})\.txt$~', $path, $m) && hash_equals(zp_ai_indexnow_key(), $m[1])) {
    zp_ai_send_text($m[1], false);
  }
}, 0);

function zp_ai_send_text(string $text, bool $markdown): void {
  zp_ai_count_bot(200);
  status_header(200);
  header('Content-Type: ' . ($markdown ? 'text/markdown' : 'text/plain') . '; charset=UTF-8');
  header('X-Robots-Tag: noindex, follow');
  header('Cache-Control: public, max-age=3600');
  header('X-Content-Type-Options: nosniff');
  if (strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET')) !== 'HEAD') { echo $text; }
  exit;
}

/* ---------------------------------------------------------------------------------------------------------
 * llms.txt
 * ------------------------------------------------------------------------------------------------------ */

/** Meta description without the call to action at its end ("Zobacz realizacje i sprawdź ceny."). */
function zp_ai_plain_description(string $text): string {
  $text = trim(html_entity_decode(wp_strip_all_tags($text), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
  $text = str_replace("\xC2\xA0", ' ', $text);
  $parts = preg_split('~(?<=[.!?])\s+~u', $text);
  while (count($parts) > 1 && preg_match('~^(Zobacz|Sprawdź|Opisz|Poznaj|Umów|Zamów|Napisz|Porozmawiajmy|Zadzwoń|Wyceń|Dowiedz)~u', (string) end($parts))) {
    array_pop($parts);
  }
  return trim(implode(' ', $parts));
}

/** Published page/post ID, cached for this llms.txt build only. */
function zp_ai_llms_post_id(string $path): int {
  static $ids = [];
  if (!array_key_exists($path, $ids)) { $ids[$path] = function_exists('zp_seo_plan_find_post') ? zp_seo_plan_find_post($path) : (int) url_to_postid(home_url($path)); }
  return (int) $ids[$path];
}

/** Apply the same privacy/indexation checks to every group of llms.txt links. */
function zp_ai_llms_public_path(string $path): bool {
  if (isset(zp_seo_plan_data('redirects')['exact'][$path])) { return false; }
  if (function_exists('zp_seo_plan_is_hidden_path') && zp_seo_plan_is_hidden_path($path)) { return false; }
  if (function_exists('zp_seo_plan_path_is_live') && !zp_seo_plan_path_is_live($path)) { return false; }
  $id = zp_ai_llms_post_id($path);
  if ($id && (get_post_status($id) !== 'publish' || get_post_field('post_password', $id) !== '' || in_array('noindex', (array) get_post_meta($id, 'rank_math_robots', true), true))) { return false; }
  return true;
}

/** Markdown link line for a live, public, indexable page. */
function zp_ai_llms_link(string $path, string $label, string $text = ''): string {
  if (!zp_ai_llms_public_path($path)) { return ''; }
  $label = str_replace(['[', ']'], ['(', ')'], $label);
  return '- [' . $label . '](' . home_url($path) . ')' . ($text !== '' ? ': ' . $text : '') . "\n";
}

/**
 * 2.9.6: posts that show another article's text under their own title (article review, widocznosc-ai/artykuly):
 * path => the borrowed opening words (ASCII, so they match in Elementor's JSON too). They stay out of llms.txt
 * while that text is in the post and come back on their own once their own article is published there.
 */
function zp_ai_other_article_posts(): array {
  return [
    '/kampanie-reklamowe/ile-kosztuje-google-ads/' => 'Search zwykle jest bezpieczniejszym wyborem',
    '/sklepy-internetowe/karta-produktu-w-sklepie-internetowym/' => 'Brief do logo i identyfikacji wizualnej to dokument',
    '/poradniki/jak-pisac-teksty-na-strone-zeby-nie-brzmialy-jak-katalog-uslug/' => 'WooCommerce ma sens wtedy, gdy sklep internetowy',
  ];
}

function zp_ai_post_shows_other_article(string $path): bool {
  $needle = zp_ai_other_article_posts()[$path] ?? '';
  $id = $needle !== '' && function_exists('zp_seo_plan_find_post') ? zp_seo_plan_find_post($path) : 0;
  if (!$id) { return false; }
  return strpos((string) get_post_field('post_content', $id), $needle) !== false
    || strpos((string) get_post_meta($id, '_elementor_data', true), $needle) !== false;
}

/** Resolve saved metadata first; unresolved Rank Math variables fall back to plain plan/post text. */
function zp_ai_saved_text(int $id, string $key): string {
  $text = $id ? trim((string) get_post_meta($id, $key, true)) : '';
  if (preg_match('~%[a-z][a-z0-9_]*(?:\([^%]*\))?%~i', $text)) { return ''; }
  return trim(html_entity_decode(wp_strip_all_tags($text), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
}

/** Current saved title, then the plan, then the post title; no brand suffix in llms.txt. */
function zp_ai_plan_title(string $path, string $fallback = ''): string {
  $entry = zp_seo_plan_entry($path) ?? [];
  $id = zp_ai_llms_post_id($path);
  $title = zp_ai_saved_text($id, 'rank_math_title');
  if ($title === '') { $title = trim((string) (($entry['title'] ?? '') ?: ($entry['editorial_title'] ?? ''))); }
  if ($title === '' && $id) { $title = trim(html_entity_decode(wp_strip_all_tags((string) get_the_title($id)), ENT_QUOTES | ENT_HTML5, 'UTF-8')); }
  $title = trim((string) preg_replace('~\s*[|–-]\s*Zaprojektowani(\.com)?\s*$~u', '', $title));
  return $title !== '' ? $title : $fallback;
}

function zp_ai_plan_description(string $path): string {
  $entry = zp_seo_plan_entry($path) ?? [];
  $id = zp_ai_llms_post_id($path);
  $text = zp_ai_saved_text($id, 'rank_math_description');
  if ($text === '') { $text = (string) (($entry['description'] ?? '') ?: ($entry['editorial_description'] ?? '')); }
  if ($text === '' && $id) { $text = (string) get_post_field('post_excerpt', $id); }
  return zp_ai_plain_description($text);
}

/** English path of a Polish page, or ''. */
function zp_ai_en_path(string $path): string {
  if (!class_exists('\ZPL\Router')) { return ''; }
  try {
    $routes = \ZPL\Router::routes();
  } catch (\Throwable $e) {
    return '';
  }
  return isset($routes[$path]) && is_string($routes[$path]) ? $routes[$path] : '';
}

function zp_ai_llms_text(): string {
  $key = 'zp_ai_llms_' . md5(ZP_SUITE_VERSION . '|' . ZP_AI_VERSION . '|' . home_url('/'));
  $cached = get_transient($key);
  if (is_string($cached) && $cached !== '') { return $cached; }
  $text = zp_ai_llms_build();
  set_transient($key, $text, 12 * HOUR_IN_SECONDS);
  update_option('zp_ai_llms_built', time(), false);
  return $text;
}

function zp_ai_llms_forget(): void {
  delete_transient('zp_ai_llms_' . md5(ZP_SUITE_VERSION . '|' . ZP_AI_VERSION . '|' . home_url('/')));
}

function zp_ai_llms_build(): string {
  $offers = zp_ai_offers();
  $projects = function_exists('zp_seo_plan_projects_count') ? zp_seo_plan_projects_count() : 114;
  $team = function_exists('zp_seo_plan_team') ? zp_seo_plan_team() : [];
  $home = home_url('/');

  $t = "# Zaprojektowani\n\n";
  $t .= '> Zaprojektowani (' . wp_parse_url($home, PHP_URL_HOST) . ') to studio projektowe z Katowic. Od 2022 roku projektujemy i wdrażamy '
    . 'strony internetowe na WordPressie, sklepy internetowe WooCommerce, logo i identyfikację wizualną oraz prowadzimy kampanie '
    . "Meta Ads i Google Ads dla firm z całej Polski.\n\n";

  $t .= "## Najważniejsze informacje\n\n";
  $t .= "- Siedziba: ul. Modelarska 18/2, 40-142 Katowice (województwo śląskie). Pracujemy z firmami z całej Polski, także w pełni zdalnie.\n";
  $prices = [];
  foreach (['strony', 'sklepy', 'logo', 'kampanie'] as $k) {
    if (isset($offers[$k])) { $prices[] = mb_strtolower(mb_substr($offers[$k]['name'], 0, 1)) . mb_substr($offers[$k]['name'], 1) . ' ' . zp_ai_offer_text($offers[$k], false); }
  }
  if ($prices) { $t .= '- Ceny: ' . implode('; ', $prices) . ". Ceny „od” pokazują, od jakiej kwoty zaczynamy; konkretną kwotę podajemy w bezpłatnej wycenie.\n"; }
  $times = [];
  foreach (['strony' => 'strona', 'sklepy' => 'sklep', 'logo' => 'logo', 'kampanie' => 'kampanie'] as $k => $label) {
    if (!empty($offers[$k]['time'])) { $times[] = $label . ': ' . $offers[$k]['time']; }
  }
  if ($times) { $t .= '- Terminy: ' . implode('; ', $times) . ".\n"; }
  $t .= '- Wycena: bezpłatna i niezobowiązująca, w ciągu 48–72 godzin od wysłania briefu w Studio Wyceny (' . home_url('/studio-wyceny/') . ").\n";
  $t .= "- W cenie strony: indywidualny projekt UX/UI, wdrożenie na WordPressie z samodzielną edycją treści, wersja mobilna, podstawy SEO technicznego, formularze, analityka, domena i hosting oraz publikacja z pełnymi dostępami dla klienta.\n";
  $t .= '- Doświadczenie: 10+ lat doświadczenia zespołu (studio działa od 2022 roku) i ' . (int) $projects . "+ zrealizowanych projektów: strony firmowe, sklepy internetowe, logo i identyfikacje wizualne.\n";
  if ($team) {
    $people = [];
    foreach ($team as $person) { $people[] = $person['name'] . ' (' . $person['job'] . ')'; }
    $t .= '- Zespół: ' . implode(', ', $people) . ".\n";
  }
  $t .= "- Kontakt: kontakt@zaprojektowani.com, tel. +48 501 054 253, WhatsApp: https://wa.me/48501054253; pon.–pt. 9:00–17:00.\n";
  $t .= "- Dane firmy: NIP 9930682613. Wizytówka Google z opiniami klientów: https://www.google.com/maps/place/?q=place_id:ChIJh0fbrHpbFkcRPEegRF9aX_I\n";
  $profiles = zp_ai_profiles();
  if ($profiles) { $t .= '- Profile: ' . implode(', ', $profiles) . "\n"; }
  $t .= "- Język obsługi: polski i angielski.\n\n";

  $t .= "## Usługi\n\n";
  $services = [
    '/tworzenie-stron-internetowych/' => ['Tworzenie stron internetowych', 'strony'],
    '/tworzenie-sklepow-internetowych/' => ['Tworzenie sklepów internetowych WooCommerce', 'sklepy'],
    '/projektowanie-logo/' => ['Projektowanie logo', 'logo'],
    '/identyfikacja-wizualna/' => ['Identyfikacja wizualna i branding', 'identyfikacja'],
    '/kampanie-reklamowe/' => ['Kampanie reklamowe Meta Ads i Google Ads', 'kampanie'],
    '/strony-wordpress/' => ['Strony WordPress', ''],
    '/strona-wizytowka/' => ['Strona wizytówka (one page)', ''],
    '/tworzenie-landing-page/' => ['Landing page pod reklamy', ''],
    '/opieka-wordpress/' => ['Opieka nad stroną WordPress', ''],
    '/studio-wyceny/' => ['Cennik i bezpłatna wycena (Studio Wyceny)', ''],
  ];
  foreach ($services as $path => [$label, $offer]) {
    $desc = zp_ai_plan_description($path);
    if ($offer !== '' && isset($offers[$offer])) {
      // The description may already name the price ("Obsługa od 1 200 zł/mc."): then only the deadline is added.
      $add = strpos($desc, 'zł') === false ? 'Cena: ' . zp_ai_offer_text($offers[$offer]) : ucfirst($offers[$offer]['time']);
      if (trim($add) !== '') { $desc = rtrim($desc, '.') . ($desc !== '' ? '. ' : '') . $add . '.'; }
    }
    $t .= zp_ai_llms_link($path, $label, $desc);
  }

  $industry = '';
  foreach (zp_seo_content_pages() as $path => $page) {
    if (strpos($path, '/strony-internetowe-dla-') !== 0) { continue; }
    $industry .= zp_ai_llms_link($path, zp_ai_plan_title($path, (string) ($page['title'] ?? '')), zp_ai_plan_description($path));
  }
  if ($industry !== '') { $t .= "\n## Strony internetowe dla branż\n\n" . $industry; }

  $local = '';
  foreach (['/agencja-reklamowa-katowice/', '/strony-internetowe-katowice/', '/sklepy-internetowe-katowice/', '/logo-branding-katowice/'] as $path) {
    $local .= zp_ai_llms_link($path, zp_ai_plan_title($path, $path), zp_ai_plan_description($path));
  }
  if ($local !== '') { $t .= "\n## Katowice i Śląsk (spotkania na miejscu)\n\n" . $local; }

  $t .= "\n## O firmie\n\n";
  $t .= zp_ai_llms_link('/o-nas/', 'O nas: zespół i historia studia', zp_ai_plan_description('/o-nas/'));
  $t .= zp_ai_llms_link('/realizacje/', 'Realizacje: portfolio stron, sklepów i brandingu');
  $t .= zp_ai_llms_link('/najczesciej-zadawane-pytania/', 'Najczęściej zadawane pytania', zp_ai_plan_description('/najczesciej-zadawane-pytania/'));
  $t .= zp_ai_llms_link('/kontakt/', 'Kontakt');

  $guides = '';
  $count = 0;
  $first = [
    '/strony-www/ile-kosztuje-stworzenie-strony-internetowej-dla-firmy/', '/seo-i-konwersja/ile-kosztuje-sklep-internetowy-woocommerce-w-2026-roku/',
    '/logo-branding/ile-kosztuje-logo-dla-firmy-i-co-obejmuje-cena/', '/seo-i-konwersja/ile-kosztuje-utrzymanie-strony-internetowej/',
    '/meta-ads/ile-kosztuje-reklama-na-facebooku/',
  ];
  $plan = zp_seo_plan_data('plan');
  // Articles from the content feed (feed.php, 2.9.0) count like plan entries; the newest come first.
  $feed = function_exists('zp_feed_display') ? array_reverse((array) (zp_feed_display()['entries'] ?? []), true) : [];
  $paths = array_values(array_unique(array_merge($first, array_keys($feed), array_keys($plan))));
  $redirected = (array) (zp_seo_plan_data('redirects')['exact'] ?? []);
  // 2.9.8: every published guide. The list stopped at 40, so older guides dropped out as the feed
  // published new ones; 400 only bounds the file.
  $max = 400;
  foreach ($paths as $path) {
    if ($count >= $max) { break; }
    $entry = zp_seo_plan_entry($path) ?? [];
    if (($entry['kind'] ?? '') !== 'post') { continue; }
    if (isset($redirected[$path]) || zp_ai_post_shows_other_article($path)) { continue; }
    $title = zp_ai_plan_title($path);
    if ($title === '') { continue; }
    $line = zp_ai_llms_link($path, $title, zp_ai_plan_description($path));
    if ($line === '') { continue; }
    $guides .= $line;
    $count++;
  }
  // Published posts outside the plan and the feed (e.g. the Google Ads comparisons), newest first,
  // with their own title and Rank Math description; noindex posts stay out.
  $seen = array_flip($paths);
  foreach (get_posts(['post_type' => 'post', 'post_status' => 'publish', 'numberposts' => $max, 'orderby' => 'date', 'order' => 'DESC', 'fields' => 'ids', 'suppress_filters' => true]) as $id) {
    if ($count >= $max) { break; }
    $path = zp_seo_plan_path((string) get_permalink($id));
    if (isset($seen[$path]) || isset($redirected[$path])) { continue; }
    $seen[$path] = true;
    if (function_exists('zp_seo_plan_is_hidden_path') && zp_seo_plan_is_hidden_path($path)) { continue; }
    if (in_array('noindex', (array) get_post_meta($id, 'rank_math_robots', true), true)) { continue; }
    $title = zp_ai_plan_title($path);
    if ($title === '') { continue; }
    $line = zp_ai_llms_link($path, $title, zp_ai_plan_description($path));
    if ($line === '') { continue; }
    $guides .= $line;
    $count++;
  }
  if ($guides !== '') { $t .= "\n## Poradniki (ceny, proces, decyzje)\n\n" . $guides; }

  $en = '';
  $en_pages = [
    '/' => ['Zaprojektowani in English', 'Design studio in Katowice, Poland: websites, WooCommerce online stores, logo and branding, Meta Ads and Google Ads for companies across Poland. We also work in English.'],
    '/tworzenie-stron-internetowych/' => ['Website development', 'strony'],
    '/tworzenie-sklepow-internetowych/' => ['WooCommerce online store development', 'sklepy'],
    '/projektowanie-logo/' => ['Logo design', 'logo'],
    '/identyfikacja-wizualna/' => ['Visual identity and branding', 'identyfikacja'],
    '/kampanie-reklamowe/' => ['Meta Ads and Google Ads campaigns', 'kampanie'],
    '/o-nas/' => ['About us', ''],
    '/kontakt/' => ['Contact', ''],
  ];
  foreach ($en_pages as $pl => [$label, $what]) {
    if (!zp_ai_llms_public_path($pl)) { continue; }
    $path = zp_ai_en_path($pl);
    if ($path === '') { continue; }
    $desc = $what;
    if (isset($offers[$what])) {
      $o = $offers[$what];
      $desc = 'From PLN ' . number_format((int) $o['min'], 0, '.', ',') . ($o['unit'] === 'MON' ? ' per month' : '')
        . ($o['note_en'] !== '' ? ' (' . $o['note_en'] . ')' : '') . ($o['time_en'] !== '' ? ', ' . $o['time_en'] : '') . '.';
    }
    $en .= '- [' . $label . '](' . home_url($path) . ')' . ($desc !== '' ? ': ' . $desc : '') . "\n";
  }
  if ($en !== '') { $t .= "\n## English\n\n" . $en; }

  $t .= "\n## Optional\n\n";
  $t .= zp_ai_llms_link('/wiedza/', 'Wszystkie artykuły i poradniki');
  $t .= '- [Mapa witryny](' . home_url('/sitemap_index.xml') . "): wszystkie publiczne adresy po polsku i po angielsku\n";
  $t .= "\n" . ZP_AI_LLMS_MARK . "\n";
  return $t;
}

// Fresh text after content changes; the static file (if any) follows on the next scheduled refresh.
foreach (['save_post', 'deleted_post', 'trashed_post'] as $zp_ai_hook) {
  add_action($zp_ai_hook, function () {
    zp_ai_llms_forget();
    if (!wp_next_scheduled('zp_ai_refresh')) { wp_schedule_single_event(time() + 300, 'zp_ai_refresh'); }
  });
}
unset($zp_ai_hook);
// Rank Math's quick edits can update metadata without saving the WordPress post itself.
foreach (['added_post_meta', 'updated_post_meta', 'deleted_post_meta'] as $zp_ai_hook) {
  add_action($zp_ai_hook, function ($meta_id, $post_id, $key) {
    if (!in_array($key, ['rank_math_title', 'rank_math_description', 'rank_math_robots'], true)) { return; }
    zp_ai_llms_forget();
    if (!wp_next_scheduled('zp_ai_refresh')) { wp_schedule_single_event(time() + 300, 'zp_ai_refresh'); }
  }, 10, 3);
}
unset($zp_ai_hook);
add_action('add_option_zp_ai_profiles', 'zp_ai_llms_forget');
add_action('update_option_zp_ai_profiles', 'zp_ai_llms_forget');

/**
 * A static llms.txt in the site root wins over WordPress (the web server serves it before WordPress runs). The
 * live one listed only the Katowice pages and no prices. Its text goes into the database once (shown in the
 * panel) and the file is renamed to llms.txt.bak, so /llms.txt comes from the plugin; since 2.9.6 that copy is
 * deleted right away (zp_ai_llms_backup_cleanup). If it cannot be renamed, it is overwritten with the current
 * text; if neither works, the panel says what to do.
 */
function zp_ai_llms_file_sync(string $why): string {
  $file = ABSPATH . 'llms.txt';
  if (!file_exists($file)) { update_option('zp_ai_llms_file_state', 'dynamic', false); return 'dynamic'; }
  $current = (string) @file_get_contents($file);
  if (strpos($current, ZP_AI_LLMS_MARK) === false && get_option('zp_ai_llms_previous') === false) {
    update_option('zp_ai_llms_previous', ['at' => time(), 'text' => substr($current, 0, 60000)], false);
  }
  if (strpos($current, ZP_AI_LLMS_MARK) === false) {
    $backup = ABSPATH . 'llms.txt.bak';
    if (!file_exists($backup) && @rename($file, $backup)) {
      zp_ai_log('llms.txt: stary plik z katalogu strony zapisany w bazie i przemianowany na llms.txt.bak (' . $why . '); /llms.txt podaje teraz wtyczka.');
      zp_ai_llms_backup_cleanup();
      update_option('zp_ai_llms_file_state', 'dynamic', false);
      return 'dynamic';
    }
  }
  $text = zp_ai_llms_text();
  if ($current === $text) { update_option('zp_ai_llms_file_state', 'static', false); return 'static'; }
  if (wp_is_writable($file) && @file_put_contents($file, $text, LOCK_EX) !== false) {
    zp_ai_log('llms.txt: plik w katalogu strony nadpisany aktualną treścią (' . $why . ').');
    update_option('zp_ai_llms_file_state', 'static', false);
    return 'static';
  }
  zp_ai_log('llms.txt: stary plik w katalogu strony zasłania nowy i nie da się go zmienić (brak uprawnień).');
  update_option('zp_ai_llms_file_state', 'blocked', false);
  return 'blocked';
}

/**
 * 2.9.6: the old file renamed by 2.9.1 stayed public at /llms.txt.bak (old text, no prices). Its text is in
 * zp_ai_llms_previous (stored first if it is not there yet), so the copy is deleted.
 */
function zp_ai_llms_backup_cleanup(): void {
  $backup = ABSPATH . 'llms.txt.bak';
  if (!file_exists($backup)) { return; }
  $previous = get_option('zp_ai_llms_previous');
  if (!is_array($previous) || trim((string) ($previous['text'] ?? '')) === '') {
    update_option('zp_ai_llms_previous', ['at' => time(), 'text' => substr((string) @file_get_contents($backup), 0, 60000)], false);
  }
  if (@unlink($backup)) {
    zp_ai_log('llms.txt.bak: usunięta kopia starego pliku (jego treść jest w bazie, widać ją w tym panelu).');
  } else {
    zp_ai_log('llms.txt.bak: nie udało się usunąć kopii starego pliku (brak uprawnień). Można ją skasować z katalogu strony.');
  }
}

add_action('zp_ai_refresh', function () {
  if (!zp_ai_on()) { return; }
  zp_ai_llms_forget();
  if (file_exists(ABSPATH . 'llms.txt')) { zp_ai_llms_file_sync('zmiana treści'); }
});

/* ---------------------------------------------------------------------------------------------------------
 * Structured data (schema.php filters): offers, team, profiles.
 * ------------------------------------------------------------------------------------------------------ */

/** Profile links for sameAs: the defaults plus the ones saved in the panel. */
function zp_ai_profiles(): array {
  $saved = (array) get_option('zp_ai_profiles', []);
  $all = array_merge(['https://www.instagram.com/zaprojektowanicom', 'https://www.facebook.com/zaprojektowanicom', 'https://clutch.co/profile/zaprojektowani'], $saved);
  $out = [];
  foreach ($all as $url) {
    $url = esc_url_raw(trim((string) $url), ['https']);
    if ($url === '' || stripos($url, 'zaprojektowani.com') !== false && wp_parse_url($url, PHP_URL_HOST) === wp_parse_url(home_url('/'), PHP_URL_HOST)) { continue; }
    $out[untrailingslashit(strtolower($url))] = untrailingslashit($url);
  }
  return array_values($out);
}

/**
 * Offer node. Always in Polish, like the rest of the JSON-LD: on English pages the languages module translates
 * names and descriptions (the English texts come from zp_ai_dictionary()).
 */
function zp_ai_offer_node(array $o): array {
  $spec = ['@type' => $o['unit'] === 'MON' ? 'UnitPriceSpecification' : 'PriceSpecification', 'minPrice' => (int) $o['min'], 'priceCurrency' => 'PLN'];
  if ($o['unit'] === 'MON') { $spec['unitCode'] = 'MON'; }
  $node = [
    '@type' => 'Offer',
    'name' => $o['name'],
    'url' => home_url($o['path']),
    'priceCurrency' => 'PLN',
    'priceSpecification' => $spec,
    // SearchFit 14.10, point 8: a Service names its provider, the business node of schema.php.
    'itemOffered' => ['@type' => 'Service', 'name' => $o['name'], 'provider' => ['@id' => home_url('/') . '#organization']],
    'areaServed' => ['@type' => 'Country', 'name' => 'Polska'],
  ];
  if ($o['note'] !== '') { $node['description'] = $o['note']; }
  return $node;
}

/** Topics added to knowsAbout, Polish => English. */
function zp_ai_topics(): array {
  return [
    'projektowanie stron internetowych' => 'website design', 'strony WordPress' => 'WordPress websites', 'landing page' => 'landing page',
    'UX/UI' => 'UX/UI', 'SEO techniczne' => 'technical SEO', 'kampanie Meta Ads' => 'Meta Ads campaigns', 'Google Ads' => 'Google Ads',
    'brandbook' => 'brand book', 'księga znaku' => 'logo guidelines',
  ];
}

/** English for the texts this file adds to the JSON-LD (used only where the shipped dictionaries have none). */
function zp_ai_dictionary(): array {
  $d = zp_ai_topics() + ['Polska' => 'Poland', 'Katowice' => 'Katowice'];
  foreach (zp_ai_offers() as $o) {
    $d[$o['name']] = $o['name_en'];
    if ($o['note'] !== '' && $o['note_en'] !== '') { $d[$o['note']] = $o['note_en']; }
  }
  return $d;
}

add_filter('zpl_dictionary', function ($dict, $lang = 'en') {
  if ($lang !== 'en' || !is_array($dict) || !zp_ai_on()) { return $dict; }
  return $dict + zp_ai_dictionary();
}, 10, 2);

add_filter('zp_seo_plan_business_node', function ($node) {
  if (!is_array($node) || !zp_ai_on()) { return $node; }
  $offers = [];
  foreach (zp_ai_offers() as $o) { $offers[] = zp_ai_offer_node($o); }
  $node['makesOffer'] = $offers;
  // Not translated by the languages module, so chosen here.
  $node['priceRange'] = zp_seo_plan_is_en() ? 'from PLN 999' : 'od 999 zł';
  $node['currenciesAccepted'] = 'PLN';
  $node['knowsAbout'] = array_values(array_unique(array_merge((array) ($node['knowsAbout'] ?? []), array_keys(zp_ai_topics()))));
  $node['sameAs'] = array_values(array_unique(array_merge((array) ($node['sameAs'] ?? []), zp_ai_profiles())));
  $node['areaServed'] = [['@type' => 'Country', 'name' => 'Polska'], ['@type' => 'City', 'name' => 'Katowice']];
  $node['availableLanguage'] = ['pl', 'en'];
  if (function_exists('zp_seo_plan_team') && function_exists('zp_seo_plan_person_node')) {
    $node['employee'] = [];
    foreach (zp_seo_plan_team() as $person) { $node['employee'][] = ['@id' => zp_seo_plan_person_node($person)['@id']]; }
  }
  return $node;
});

add_filter('zp_seo_plan_service_node', function ($node, $variant = '') {
  if (!is_array($node) || !zp_ai_on()) { return $node; }
  $key = zp_ai_offer_for_variant((string) $variant);
  $offers = zp_ai_offers();
  if ($key === '*') {
    $node['offers'] = [];
    foreach ($offers as $o) {
      $offer = zp_ai_offer_node($o);
      unset($offer['areaServed']);
      $node['offers'][] = $offer;
    }
    return $node;
  }
  if ($key === '' || !isset($offers[$key])) { return $node; }
  $offer = zp_ai_offer_node($offers[$key]);
  $offer['url'] = (string) ($node['url'] ?? $offer['url']);
  unset($offer['itemOffered'], $offer['areaServed']);
  $node['offers'] = $offer;
  return $node;
}, 10, 2);

/* ---------------------------------------------------------------------------------------------------------
 * Visits: AI crawlers (server side) and people coming from AI assistants (the suite's page-view beacon).
 * ------------------------------------------------------------------------------------------------------ */

function zp_ai_stats(): array {
  $s = get_option('zp_ai_stats', []);
  $s = is_array($s) ? $s : [];
  $s = array_merge(['bots' => [], 'bot_last' => [], 'refs' => [], 'ref_last' => []], $s);
  $merged = zp_ai_cached_bot_visits($s);
  if ($merged !== $s) { zp_ai_stats_save($merged); }
  return $merged;
}

/**
 * 14.10.2026: robot visits answered by the HTML cache (cache-dropin-20261009.php), where WordPress does not run, wait in
 * the cache folder's bots.log; they are counted here once (the file is taken by renaming it, so two requests never
 * count it twice).
 */
function zp_ai_cached_bot_visits(array $s): array {
  if (!zp_ai_on() || !class_exists('ZP_HTML_Cache_20261009')) { return $s; }
  $file = ZP_HTML_Cache_20261009::dir() . '/bots.log';
  if (is_link($file) || !is_file($file)) { return $s; }
  $taken = $file . '.' . bin2hex(random_bytes(6)) . '.tmp';
  if (!@rename($file, $taken)) { return $s; }
  $lines = @file($taken, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
  @unlink($taken);
  foreach (array_slice(is_array($lines) ? $lines : [], 0, 5000) as $line) {
    $visit = json_decode((string) $line, true);
    if (!is_array($visit) || !is_int($visit[0] ?? null) || !is_string($visit[1] ?? null) || !is_string($visit[2] ?? null)) { continue; }
    $bot = zp_ai_bot_of($visit[2]);
    if ($bot === '') { continue; }
    $day = wp_date('Y-m-d', $visit[0]);
    $s['bots'][$day][$bot] = (int) ($s['bots'][$day][$bot] ?? 0) + 1;
    if ((int) ($s['bot_last'][$bot]['at'] ?? 0) <= $visit[0]) {
      $s['bot_last'][$bot] = ['at' => $visit[0], 'path' => substr($visit[1], 0, 200), 'code' => 200];
    }
  }
  return $s;
}

function zp_ai_stats_save(array $s): void {
  ksort($s['bots']);
  ksort($s['refs']);
  $s['bots'] = array_slice($s['bots'], -60, null, true);
  $s['refs'] = array_slice($s['refs'], -60, null, true);
  $s['ref_last'] = array_slice((array) $s['ref_last'], 0, 30);
  update_option('zp_ai_stats', $s, false);
}

/** Counts the current request when it comes from an AI crawler (once per request). */
function zp_ai_count_bot(int $status = 0): void {
  static $done = false;
  // The panel's access test (zp_ai_test) is not a visit.
  if ($done || !zp_ai_on() || isset($_GET['zp_ai_test'])) { return; }
  $bot = zp_ai_bot_of((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''));
  if ($bot === '') { return; }
  $done = true;
  $s = zp_ai_stats();
  $day = wp_date('Y-m-d');
  $s['bots'][$day][$bot] = (int) ($s['bots'][$day][$bot] ?? 0) + 1;
  $s['bot_last'][$bot] = [
    'at' => time(),
    'path' => substr((string) ($_SERVER['REQUEST_URI'] ?? '/'), 0, 200),
    'code' => $status ?: (int) http_response_code(),
  ];
  zp_ai_stats_save($s);
}

// After the response is built, so the status code is known (200, 301, 404…).
add_action('init', function () {
  if (!zp_ai_on() || is_admin() || wp_doing_cron() || zp_ai_bot_of((string) ($_SERVER['HTTP_USER_AGENT'] ?? '')) === '') { return; }
  add_action('shutdown', static function () { zp_ai_count_bot(); }, 0);
}, 1);

/**
 * AI source from an exact case-insensitive name/domain UTM token or a parsed referrer host.
 * Known hosts and their DNS subdomains are accepted; UTM URL strings and lookalike names are ignored.
 */
function zp_ai_source_of(string $ref, string $landing): string {
  $map = [
    'chatgpt.com' => 'ChatGPT', 'chat.openai.com' => 'ChatGPT', 'openai.com' => 'ChatGPT',
    'gemini.google.com' => 'Gemini', 'bard.google.com' => 'Gemini', 'aistudio.google.com' => 'Gemini',
    'claude.ai' => 'Claude', 'perplexity.ai' => 'Perplexity',
    'copilot.microsoft.com' => 'Copilot', 'copilot.cloud.microsoft' => 'Copilot', 'm365.cloud.microsoft' => 'Copilot',
    'chat.mistral.ai' => 'Mistral', 'chat.deepseek.com' => 'DeepSeek', 'grok.com' => 'Grok', 'meta.ai' => 'Meta AI', 'you.com' => 'You.com',
  ];
  $match_host = static function (string $candidate) use ($map): string {
    $host = strtolower($candidate);
    if (substr($host, -1) === '.') { $host = substr($host, 0, -1); }
    if ($host === '' || strlen($host) > 253 || !preg_match('/^[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?(?:\.[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?)*$/D', $host)) { return ''; }
    foreach ($map as $domain => $name) {
      if ($host === $domain || substr($host, -strlen('.' . $domain)) === '.' . $domain) { return $name; }
    }
    return '';
  };
  $query = wp_parse_url($landing, PHP_URL_QUERY);
  if (is_string($query) && $query !== '') {
    parse_str($query, $q);
    $utm = isset($q['utm_source']) && is_string($q['utm_source']) ? strtolower(trim($q['utm_source'])) : '';
    if ($utm !== '') {
      foreach ($map as $name) {
        if ($utm === strtolower($name)) { return $name; }
      }
      $source = $match_host($utm);
      if ($source !== '') { return $source; }
    }
  }
  $host = wp_parse_url($ref, PHP_URL_HOST);
  return is_string($host) ? $match_host($host) : '';
}

// Runs before the suite's own handler (analytics.php), which saves the page view and ends the request.
foreach (['wp_ajax_zp_suite_track', 'wp_ajax_nopriv_zp_suite_track'] as $zp_ai_hook) {
  add_action($zp_ai_hook, function () {
    if (!zp_ai_on()) { return; }
    $ref = isset($_POST['ref']) ? (string) wp_unslash($_POST['ref']) : '';
    $path = isset($_POST['path']) ? (string) wp_unslash($_POST['path']) : '';
    $source = zp_ai_source_of($ref, $path);
    if ($source === '') { return; }
    $s = zp_ai_stats();
    $day = wp_date('Y-m-d');
    $s['refs'][$day][$source] = (int) ($s['refs'][$day][$source] ?? 0) + 1;
    array_unshift($s['ref_last'], ['at' => time(), 'source' => $source, 'path' => substr((string) wp_parse_url($path, PHP_URL_PATH), 0, 200)]);
    zp_ai_stats_save($s);
  }, 1);
}
unset($zp_ai_hook);

/* ---------------------------------------------------------------------------------------------------------
 * IndexNow (https://www.indexnow.org): one submission reaches Bing, Yandex, Seznam, Naver and the others.
 * ------------------------------------------------------------------------------------------------------ */

function zp_ai_indexnow_key(): string {
  $key = (string) get_option('zp_ai_indexnow_key', '');
  if (!preg_match('~^[a-f0-9]{32}$~', $key)) {
    $key = bin2hex(random_bytes(16));
    update_option('zp_ai_indexnow_key', $key, false);
  }
  return $key;
}

/** Rank Math's Instant Indexing module sends IndexNow on publish by itself. */
function zp_ai_indexnow_by_rank_math(): bool {
  return class_exists('\RankMath\Helper') && method_exists('\RankMath\Helper', 'is_module_active') && \RankMath\Helper::is_module_active('instant-indexing');
}

function zp_ai_indexnow_status_text(int $code): string {
  $texts = [
    200 => 'przyjęte', 202 => 'przyjęte, klucz w trakcie weryfikacji', 400 => 'błędne zgłoszenie',
    403 => 'klucz nieprawidłowy (plik klucza niedostępny)', 422 => 'adresy nie pasują do domeny', 429 => 'za dużo zgłoszeń, spróbuj później',
  ];
  return $texts[$code] ?? 'odpowiedź ' . $code;
}

function zp_ai_indexnow_send(array $urls, string $why): array {
  $host = (string) wp_parse_url(home_url('/'), PHP_URL_HOST);
  $list = [];
  foreach ($urls as $url) {
    $url = esc_url_raw((string) $url);
    if ($url !== '' && strtolower((string) wp_parse_url($url, PHP_URL_HOST)) === strtolower($host)) { $list[$url] = true; }
  }
  $list = array_slice(array_keys($list), 0, 10000);
  if (!$list) { return [0, 'brak adresów']; }
  $key = zp_ai_indexnow_key();
  $res = wp_remote_post('https://api.indexnow.org/indexnow', [
    'timeout' => 20,
    'headers' => ['Content-Type' => 'application/json; charset=utf-8'],
    'body' => wp_json_encode(['host' => $host, 'key' => $key, 'keyLocation' => home_url('/' . $key . '.txt'), 'urlList' => $list], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
  ]);
  $code = is_wp_error($res) ? 0 : (int) wp_remote_retrieve_response_code($res);
  $msg = is_wp_error($res) ? 'błąd połączenia: ' . $res->get_error_message() : zp_ai_indexnow_status_text($code);
  zp_ai_log(sprintf('IndexNow (%s): %d adresów, %s.', $why, count($list), $code ? $code . ' ' . $msg : $msg));
  update_option('zp_ai_indexnow_last', ['at' => time(), 'count' => count($list), 'code' => $code, 'msg' => $msg, 'why' => $why], false);
  $retry = is_wp_error($res) ? '' : (string) wp_remote_retrieve_header($res, 'retry-after');
  $retry_after = ctype_digit(trim($retry)) ? (int) trim($retry) : ($retry !== '' ? max(0, (int) strtotime($retry) - time()) : 0);
  return [$code, $msg, min(86400, $retry_after)];
}

function zp_ai_indexnow_queue(array $urls): void {
  $queue = (array) get_option('zp_ai_indexnow_queue', []);
  $queue = array_values(array_unique(array_merge($queue, array_filter($urls))));
  update_option('zp_ai_indexnow_queue', array_slice($queue, -10000), false);
  $retry = (array) get_option('zp_ai_indexnow_retry', []);
  if (empty($retry['blocked']) && !wp_next_scheduled('zp_ai_indexnow_flush')) { wp_schedule_single_event(max(time() + 120, (int) ($retry['next'] ?? 0)), 'zp_ai_indexnow_flush'); }
}

/** Keep failed batches; at most five attempts, with 15m/1h/4h/12h backoff and Retry-After support. */
function zp_ai_indexnow_flush(string $why = 'nowe i zmienione strony'): void {
  $queue = (array) get_option('zp_ai_indexnow_queue', []);
  $retry = (array) get_option('zp_ai_indexnow_retry', []);
  if (!$queue || !zp_ai_on() || !empty($retry['blocked'])) { return; }
  if ((int) ($retry['next'] ?? 0) > time()) {
    if (!wp_next_scheduled('zp_ai_indexnow_flush')) { wp_schedule_single_event((int) $retry['next'], 'zp_ai_indexnow_flush'); }
    return;
  }
  $lock = (int) get_option('zp_ai_indexnow_flush_lock', 0);
  if ($lock && $lock < time() - 300) { delete_option('zp_ai_indexnow_flush_lock'); }
  if (!add_option('zp_ai_indexnow_flush_lock', time(), '', false)) { return; }
  try {
    [$code, $msg, $retry_after] = array_pad(zp_ai_indexnow_send($queue, $why), 3, 0);
    if (in_array($code, [200, 202], true)) {
      // A post published during the HTTP request can append new URLs: do not lose them.
      $remaining = array_values(array_diff((array) get_option('zp_ai_indexnow_queue', []), $queue));
      update_option('zp_ai_indexnow_queue', $remaining, false);
      delete_option('zp_ai_indexnow_retry');
      if ($remaining && !wp_next_scheduled('zp_ai_indexnow_flush')) { wp_schedule_single_event(time() + 120, 'zp_ai_indexnow_flush'); }
      return;
    }
    $attempts = (int) ($retry['attempts'] ?? 0) + 1;
    $transient = $code === 0 || $code === 429 || $code >= 500;
    $blocked = !$transient || $attempts >= 5;
    $delays = [900, 3600, 14400, 43200];
    $next = $blocked ? 0 : time() + max($delays[min($attempts - 1, 3)], (int) $retry_after);
    update_option('zp_ai_indexnow_retry', ['attempts' => $attempts, 'next' => $next, 'blocked' => $blocked, 'code' => $code], false);
    if (!$blocked && !wp_next_scheduled('zp_ai_indexnow_flush')) { wp_schedule_single_event($next, 'zp_ai_indexnow_flush'); }
    if ($blocked) { zp_ai_log('IndexNow: kolejka zachowana; automatyczne próby wstrzymane. Sprawdź błąd i ponów kolejkę w panelu.'); }
  } finally {
    delete_option('zp_ai_indexnow_flush_lock');
  }
}
add_action('zp_ai_indexnow_flush', 'zp_ai_indexnow_flush');

/** Public addresses of a post: Polish and, when it has one, English. Empty for noindex and hidden pages. */
function zp_ai_post_urls(WP_Post $post): array {
  if (!in_array($post->post_type, ['post', 'page'], true) || $post->post_password !== '') { return []; }
  $robots = get_post_meta($post->ID, 'rank_math_robots', true);
  if (is_array($robots) && in_array('noindex', $robots, true)) { return []; }
  $url = (string) get_permalink($post);
  if ($url === '' || strpos($url, '?') !== false) { return []; }
  $path = zp_seo_plan_path($url);
  if (function_exists('zp_seo_plan_is_hidden_path') && zp_seo_plan_is_hidden_path($path)) { return []; }
  $urls = [$url];
  $en = zp_ai_en_path($path);
  if ($en !== '') { $urls[] = home_url($en); }
  return $urls;
}

add_action('transition_post_status', function ($new, $old, $post) {
  if (!zp_ai_on() || !$post instanceof WP_Post || $new !== 'publish') { return; }
  if (wp_is_post_revision($post) || wp_is_post_autosave($post) || zp_ai_indexnow_by_rank_math()) { return; }
  $urls = zp_ai_post_urls($post);
  if ($urls) { zp_ai_indexnow_queue($urls); }
}, 20, 3);

// A published page that goes to the trash: its address now redirects or returns 404.
add_action('wp_trash_post', function ($post_id) {
  $post = get_post((int) $post_id);
  if (!zp_ai_on() || !$post instanceof WP_Post || $post->post_status !== 'publish' || zp_ai_indexnow_by_rank_math()) { return; }
  $urls = zp_ai_post_urls($post);
  if ($urls) { zp_ai_indexnow_queue($urls); }
});

/* ---------------------------------------------------------------------------------------------------------
 * Posts changed by the content feed (feed.php), 2.9.6. Answer sections and links from the feed show in
 * published posts at display time: nothing is saved, so neither the hooks above nor Rank Math's sitemap date
 * notice the change. Such a post goes to IndexNow (Polish address only: the feed additions are not shown on
 * /en/) and its sitemap entry gets the date of the change. The same happens to the posts that link to a feed
 * article when it is published, because their links only then become clickable.
 * ------------------------------------------------------------------------------------------------------ */

/** Feed additions per post: path => fingerprint of its sections and links. */
function zp_ai_feed_fingerprints(array $display): array {
  $out = [];
  foreach (['sections', 'links'] as $kind) {
    foreach ((array) ($display[$kind] ?? []) as $path => $items) {
      if (is_string($path) && $items) { $out[$path] = md5(($out[$path] ?? '') . $kind . serialize($items)); }
    }
  }
  return $out;
}

/** Compares the feed additions with the previous check; the posts that changed get a date and go to IndexNow. */
function zp_ai_feed_check(?array $display = null, bool $ping = true): void {
  if (!zp_ai_on()) { return; }
  $now = zp_ai_feed_fingerprints($display ?? (array) get_option('zp_feed_display', []));
  $before = (array) get_option('zp_ai_feed_seen', []);
  update_option('zp_ai_feed_seen', $now, false);
  $paths = [];
  foreach (array_keys($now + $before) as $path) {
    if (($now[$path] ?? '') !== ($before[$path] ?? '')) { $paths[] = (string) $path; }
  }
  if ($paths) { zp_ai_changed_at_display($paths, 'sekcje i linki z kanału treści', $ping); }
}

/** Published posts whose text changed without a save: the date for the sitemap and, with $ping, IndexNow. */
function zp_ai_changed_at_display(array $paths, string $why, bool $ping = true): void {
  $changed = (array) get_option('zp_ai_changed', []);
  $urls = [];
  foreach (array_unique($paths) as $path) {
    if ($path === '/') {
      $id = get_option('show_on_front') === 'page' ? (int) get_option('page_on_front') : 0;
    } else {
      $id = function_exists('zp_seo_related_object_id') ? zp_seo_related_object_id($path) : (int) url_to_postid(home_url($path));
    }
    $post = $id ? get_post($id) : null;
    if (!$post instanceof WP_Post || $post->post_status !== 'publish' || !($u = zp_ai_post_urls($post))) { continue; }
    $changed['paths'][zp_seo_plan_path($u[0])] = time();
    $changed['types'][$post->post_type] = time();
    $urls[] = $u[0];
  }
  if (!$urls) { return; }
  asort($changed['paths']);
  $changed['paths'] = array_slice($changed['paths'], -300, null, true);
  update_option('zp_ai_changed', $changed, false);
  if (class_exists('\RankMath\Sitemap\Cache')) { \RankMath\Sitemap\Cache::invalidate_storage(); }
  // The English maps cache their entries until a post date changes, which these changes do not do (2.9.8).
  if (method_exists('ZPL\\Sitemap', 'forget')) { \ZPL\Sitemap::forget(); }
  if ($ping) { zp_ai_indexnow_queue($urls); }
  zp_ai_log(sprintf('Zmiany przy wyświetlaniu (%s), liczba wpisów: %d. Nowa data w mapie witryny%s.', $why, count($urls), $ping ? ' i zgłoszenie do IndexNow' : ''));
}

add_action('add_option_zp_feed_display', function ($option, $value) {
  zp_ai_feed_check(is_array($value) ? $value : null);
}, 10, 2);
add_action('update_option_zp_feed_display', function ($old, $value) {
  zp_ai_feed_check(is_array($value) ? $value : null);
}, 10, 2);

// A feed article goes live: the posts that link to it now show the link.
add_action('transition_post_status', function ($new, $old, $post) {
  if (!zp_ai_on() || !$post instanceof WP_Post || $new !== 'publish' || $old === 'publish' || $post->post_type !== 'post') { return; }
  $to = zp_seo_plan_path((string) get_permalink($post));
  $from = [];
  foreach ((array) (((array) get_option('zp_feed_display', []))['links'] ?? []) as $in => $links) {
    foreach ((array) $links as $l) {
      if (($l[2] ?? '') === $to && $in !== $to) { $from[] = (string) $in; }
    }
  }
  if ($from) { zp_ai_changed_at_display($from, 'linki do nowego wpisu ' . $to); }
}, 30, 3);

// The date of the change in Rank Math's sitemap: the post's own entry and the post sitemap in the index.
add_filter('rank_math/sitemap/entry', function ($url, $type, $object) {
  if (!is_array($url) || empty($url['loc']) || !zp_ai_on()) { return $url; }
  $at = (int) (((array) get_option('zp_ai_changed', []))['paths'][zp_seo_plan_path((string) $url['loc'])] ?? 0);
  if ($at > (int) strtotime((string) ($url['mod'] ?? ''))) { $url['mod'] = gmdate('Y-m-d H:i:s', $at); }
  return $url;
}, 20, 3);
add_filter('rank_math/sitemap/index/entry', function ($item, $type, $post_type = '') {
  if (!is_array($item) || $type !== 'post' || !zp_ai_on()) { return $item; }
  $at = (int) (((array) get_option('zp_ai_changed', []))['types'][(string) $post_type] ?? 0);
  if ($at > (int) strtotime((string) ($item['lastmod'] ?? ''))) { $item['lastmod'] = gmdate('Y-m-d H:i:s', $at); }
  return $item;
}, 20, 3);

/** Every public address: the Polish and English sitemaps and the old addresses that now redirect. */
function zp_ai_all_urls(): array {
  $urls = [home_url('/')];
  foreach (['page', 'post'] as $type) {
    $links = null;
    if (class_exists('RankMath\\Sitemap\\Providers\\Post_Type')) {
      try {
        $provider = new \RankMath\Sitemap\Providers\Post_Type();
        $links = $provider->handles_type($type) ? (array) $provider->get_sitemap_links($type, 5000, 1) : [];
      } catch (\Throwable $e) {
        $links = null;
      }
    }
    if ($links === null) {
      $links = [];
      $ids = get_posts(['post_type' => $type, 'post_status' => 'publish', 'has_password' => false, 'numberposts' => 5000, 'fields' => 'ids']);
      foreach ($ids as $id) {
        $post = get_post($id);
        if ($post && zp_ai_post_urls($post)) { $links[] = ['loc' => get_permalink($post)]; }
      }
    }
    foreach ($links as $l) { if (is_array($l) && !empty($l['loc'])) { $urls[] = (string) $l['loc']; } }
  }
  if (class_exists('\ZPL\Sitemap') && method_exists('\ZPL\Sitemap', 'entries')) {
    foreach (['page', 'post'] as $type) {
      try {
        foreach (\ZPL\Sitemap::entries($type) as $row) { if (!empty($row['en'])) { $urls[] = (string) $row['en']; } }
      } catch (\Throwable $e) {
        // The English maps are optional here.
      }
    }
  }
  foreach (array_keys((array) (zp_seo_plan_data('redirects')['exact'] ?? [])) as $old) { $urls[] = home_url($old); }
  // Old addresses from the redirect manager (ZP Suite → Redirecty i 404), so search engines drop them sooner.
  if (function_exists('zp_suite_cc_get')) {
    foreach ((array) (zp_suite_cc_get()['redirects'] ?? []) as $r) {
      $from = trim((string) ($r['from'] ?? ''));
      if ($from === '' || (string) ($r['enabled'] ?? '1') === '0' || preg_match('~[*?^$()\[\]\s:]~', $from)) { continue; }
      $urls[] = home_url('/' . trim($from, '/') . '/');
    }
  }
  $urls[] = home_url('/llms.txt');
  return array_values(array_unique($urls));
}

add_action('zp_ai_indexnow_bulk', function () {
  if (zp_ai_on()) { zp_ai_indexnow_queue(zp_ai_all_urls()); zp_ai_indexnow_flush('pełna lista adresów'); }
});

/* ---------------------------------------------------------------------------------------------------------
 * One-time setup and daily refresh.
 * ------------------------------------------------------------------------------------------------------ */

function zp_ai_maybe_setup(): void {
  if (!zp_ai_on() || get_option('zp_ai_version') === ZP_AI_VERSION) { return; }
  $lock = (int) get_option('zp_ai_setup_lock', 0);
  if ($lock && $lock < time() - 300) { delete_option('zp_ai_setup_lock'); }
  if (!add_option('zp_ai_setup_lock', time(), '', false)) { return; }
  try {
    update_option('zp_ai_version', ZP_AI_VERSION, false);
    zp_ai_indexnow_key();
    zp_ai_llms_forget();
    $state = zp_ai_llms_file_sync('instalacja');
    zp_ai_llms_backup_cleanup();
    zp_ai_feed_check(null, false);
    if (!wp_next_scheduled('zp_ai_indexnow_bulk')) { wp_schedule_single_event(time() + 300, 'zp_ai_indexnow_bulk'); }
    if (!wp_next_scheduled('zp_ai_daily')) { wp_schedule_event(time() + HOUR_IN_SECONDS, 'daily', 'zp_ai_daily'); }
    zp_ai_log('Widoczność AI ' . ZP_AI_VERSION . ' włączona: robots.txt z robotami AI, /llms.txt (' . ($state === 'blocked' ? 'zablokowany starym plikiem' : 'aktualny') . '), ceny w danych strukturalnych, IndexNow (pełna lista za 5 minut).');
  } catch (\Throwable $e) {
    zp_ai_log('BŁĄD konfiguracji Widoczności AI: ' . $e->getMessage());
  } finally {
    delete_option('zp_ai_setup_lock');
  }
}

add_action('admin_init', function () {
  if (current_user_can('manage_options') && !wp_doing_ajax()) { zp_ai_maybe_setup(); }
}, 100);

add_action('wp_loaded', function () {
  if (is_admin() || wp_doing_ajax() || wp_doing_cron() || (defined('WP_CLI') && WP_CLI)) { return; }
  if (!in_array($_SERVER['REQUEST_METHOD'] ?? 'GET', ['GET', 'HEAD'], true)) { return; }
  zp_ai_maybe_setup();
}, 2);

add_action('zp_ai_daily', function () {
  if (!zp_ai_on()) { return; }
  zp_ai_llms_forget();
  if (file_exists(ABSPATH . 'llms.txt')) { zp_ai_llms_file_sync('codzienne odświeżenie'); }
});

/* ---------------------------------------------------------------------------------------------------------
 * Zaprojektowani Suite → Widoczność AI
 * ------------------------------------------------------------------------------------------------------ */

// After the menu clean-up (admin-menu-consolidation-186.php, 9999), like Tłumacz EN, so the item stays visible.
add_action('admin_menu', function () {
  add_submenu_page('zp-suite', 'Widoczność AI', 'Widoczność AI', 'manage_options', 'zp-suite-ai', 'zp_ai_admin_page');
}, 10000);

add_action('admin_post_zp_ai', function () {
  if (!current_user_can('manage_options')) { wp_die('Brak uprawnień.'); }
  check_admin_referer('zp_ai');
  $do = sanitize_key($_POST['do'] ?? '');
  $done = $do;
  if ($do === 'profiles') {
    $lines = preg_split('~\s+~', (string) wp_unslash($_POST['profiles'] ?? ''));
    $urls = [];
    foreach ((array) $lines as $line) {
      $url = esc_url_raw(trim($line), ['https']);
      if ($url !== '' && wp_http_validate_url($url)) { $urls[] = $url; }
    }
    update_option('zp_ai_profiles', array_values(array_unique($urls)), false);
    zp_ai_log('Profile firmy (sameAs): zapisano ' . count($urls) . ' linków.');
  } elseif ($do === 'bulk') {
    $last = (array) get_option('zp_ai_indexnow_last', []);
    if (($last['why'] ?? '') === 'pełna lista adresów (ręcznie)' && (int) ($last['at'] ?? 0) > time() - HOUR_IN_SECONDS && in_array((int) ($last['code'] ?? 0), [200, 202], true)) {
      $done = 'wait';
    } else {
      zp_ai_indexnow_queue(zp_ai_all_urls());
      zp_ai_indexnow_flush('pełna lista adresów (ręcznie)');
    }
  } elseif ($do === 'retry') {
    $retry = (array) get_option('zp_ai_indexnow_retry', []);
    if (!empty($retry['blocked'])) {
      delete_option('zp_ai_indexnow_retry');
      wp_clear_scheduled_hook('zp_ai_indexnow_flush');
      zp_ai_indexnow_queue([]);
    } else { $done = 'wait'; }
  } elseif ($do === 'llms') {
    zp_ai_llms_forget();
    zp_ai_llms_file_sync('ręcznie');
  } elseif ($do === 'test') {
    update_option('zp_ai_access_test', zp_ai_access_test(), false);
  }
  wp_safe_redirect(admin_url('admin.php?page=zp-suite-ai&done=' . $done));
  exit;
});

/** Effective robots.txt permission: most specific matching agent group, then longest path rule. */
function zp_ai_robots_allows(string $text, string $agent, string $path): bool {
  $groups = []; $group = ['agents' => [], 'rules' => []]; $has_directive = false;
  foreach (preg_split('~\r?\n~', $text) as $line) {
    $line = trim((string) preg_replace('~#.*$~', '', $line));
    if (preg_match('~^user-agent\s*:\s*(\S+)~i', $line, $m)) {
      if ($has_directive) { $groups[] = $group; $group = ['agents' => [], 'rules' => []]; $has_directive = false; }
      $group['agents'][] = $m[1];
    } elseif ($group['agents'] && preg_match('~^(allow|disallow)\s*:\s*(.*)$~i', $line, $m)) {
      $has_directive = true;
      $group['rules'][] = [strtolower($m[1]) === 'allow', trim($m[2])];
    } elseif ($group['agents'] && strpos($line, ':') !== false) { $has_directive = true; }
  }
  if ($group['agents']) { $groups[] = $group; }
  $specificity = -1; $rules = [];
  foreach ($groups as $g) {
    $match = -1;
    foreach ($g['agents'] as $token) {
      if ($token === '*') { $match = max($match, 0); }
      elseif (stripos($agent, $token) !== false) { $match = max($match, strlen($token)); }
    }
    if ($match < 0 || $match < $specificity) { continue; }
    if ($match > $specificity) { $rules = []; $specificity = $match; }
    $rules = array_merge($rules, $g['rules']);
  }
  $allowed = true; $length = -1;
  foreach ($rules as [$allow, $pattern]) {
    if ($pattern === '') { continue; }
    $end = substr($pattern, -1) === '$';
    $plain = $end ? substr($pattern, 0, -1) : $pattern;
    $rx = '~^' . str_replace('\\*', '.*', preg_quote($plain, '~')) . ($end ? '$' : '') . '~';
    $size = strlen(str_replace('*', '', $plain));
    if (preg_match($rx, $path) && ($size > $length || ($size === $length && $allow))) { $allowed = $allow; $length = $size; }
  }
  return $allowed;
}

/** Attribute reader for diagnostics, accepting either quote style and arbitrary attribute order. */
function zp_ai_test_attribute(string $tag, string $name): string {
  if (!preg_match('~(?:^|\s)' . preg_quote($name, '~') . '\s*=\s*(?:"([^"]*)"|\x27([^\x27]*)\x27|([^\s>]+))~i', $tag, $m)) { return ''; }
  return html_entity_decode((string) ($m[1] !== '' ? $m[1] : (($m[2] ?? '') !== '' ? $m[2] : ($m[3] ?? ''))), ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/** HTTP 200 alone can be a challenge page: inspect the actual payload and search directives. */
function zp_ai_access_result($res, string $path, string $agent, ?string $robots): array {
  $code = is_wp_error($res) ? 0 : (int) wp_remote_retrieve_response_code($res);
  $row = ['bot' => $agent, 'path' => $path, 'code' => $code, 'ok' => false, 'note' => ''];
  if (is_wp_error($res)) { $row['note'] = $res->get_error_message(); return $row; }
  if ($code !== 200) { $row['note'] = 'HTTP ' . $code . ' ' . (string) wp_remote_retrieve_header($res, 'location'); return $row; }
  $notes = []; $ok = true;
  if ($robots === null) { $notes[] = 'Nie odczytano robots.txt'; $ok = false; }
  elseif (!zp_ai_robots_allows($robots, $agent, $path)) { $notes[] = 'robots.txt blokuje adres'; $ok = false; }
  else { $notes[] = 'robots.txt pozwala'; }
  $type = strtolower((string) wp_remote_retrieve_header($res, 'content-type'));
  $body = (string) wp_remote_retrieve_body($res);
  if ($path === '/llms.txt') {
    if (!preg_match('~^text/(?:plain|markdown)\b~', $type) || strpos($body, '# Zaprojektowani') === false || strpos($body, ZP_AI_LLMS_MARK) === false) { $notes[] = 'Brak oczekiwanego llms.txt lub błędny typ treści'; $ok = false; }
    else { $notes[] = 'Aktualny plik llms.txt'; }
  } else {
    preg_match('~<title\b[^>]*>(.*?)</title>~is', $body, $title);
    if (!preg_match('~^(?:text/html|application/xhtml\+xml)\b~', $type) || stripos((string) ($title[1] ?? ''), 'Zaprojektowani') === false) { $notes[] = 'Brak oczekiwanej strony HTML'; $ok = false; }
    if (preg_match('~cf-chl-|challenge-platform|/_Incapsula_Resource~i', $body) || preg_match('~captcha|access denied|verify.*human|checking your browser~i', (string) ($title[1] ?? ''))) { $notes[] = 'Możliwa strona weryfikacji zamiast treści'; $ok = false; }
    $canonical = ''; $directives = (string) wp_remote_retrieve_header($res, 'x-robots-tag');
    preg_match_all('~<(?:meta|link)\b[^>]*>~i', $body, $tags);
    foreach ($tags[0] as $tag) {
      if (strcasecmp(zp_ai_test_attribute($tag, 'rel'), 'canonical') === 0) { $canonical = zp_ai_test_attribute($tag, 'href'); }
      if (in_array(strtolower(zp_ai_test_attribute($tag, 'name')), ['robots', strtolower($agent)], true)) { $directives .= ',' . zp_ai_test_attribute($tag, 'content'); }
    }
    if ($canonical === '' || strcasecmp((string) wp_parse_url($canonical, PHP_URL_HOST), (string) wp_parse_url(home_url('/'), PHP_URL_HOST)) !== 0 || zp_seo_plan_path($canonical) !== $path) { $notes[] = 'Brak lub inny canonical'; $ok = false; }
    if (preg_match('~(?:^|[\s,:])(?:noindex|nosnippet)(?:$|[\s,])|max-snippet\s*:\s*0\b~i', $directives)) { $notes[] = 'Ograniczenie indeksacji lub snippetu'; $ok = false; }
    if ($ok) { $notes[] = 'HTML i canonical zgodne'; }
  }
  $row['ok'] = $ok; $row['note'] = implode(' · ', $notes);
  return $row;
}

/** Server-side User-Agent probe, not verification of real crawler IPs or CDN cache traffic. */
function zp_ai_access_test(): array {
  $agents = [
    'OAI-SearchBot' => 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko); compatible; OAI-SearchBot/1.0; +https://openai.com/searchbot',
    'Claude-SearchBot' => 'Mozilla/5.0 (compatible; Claude-SearchBot/1.0; +Claude-SearchBot@anthropic.com)',
    'Googlebot' => 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)',
    'ChatGPT-User' => 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko); compatible; ChatGPT-User/1.0; +https://openai.com/bot',
    'Claude-User' => 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; Claude-User/1.0; +Claude-User@anthropic.com)',
    'PerplexityBot' => 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; PerplexityBot/1.0; +https://perplexity.ai/perplexitybot)',
    'bingbot' => 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm) Chrome/116.0.1938.76 Safari/537.36',
    'GPTBot' => 'Mozilla/5.0 (compatible; GPTBot/1.4; +https://openai.com/gptbot)',
    'ClaudeBot' => 'Mozilla/5.0 (compatible; ClaudeBot/1.0; +claudebot@anthropic.com)',
  ];
  $out = ['at' => time(), 'rows' => []];
  $deadline = microtime(true) + 40;
  $robot_response = wp_remote_get(home_url('/robots.txt'), ['timeout' => 8, 'redirection' => 0, 'sslverify' => true, 'limit_response_size' => 262144]);
  $robot_code = is_wp_error($robot_response) ? 0 : (int) wp_remote_retrieve_response_code($robot_response);
  $robots = $robot_code === 200 ? (string) wp_remote_retrieve_body($robot_response) : ($robot_code === 404 ? '' : null);
  if (is_string($robots) && preg_match('~<(?:!doctype|html)\b~i', $robots)) { $robots = null; }
  $out['robots_code'] = $robot_code;
  foreach ($agents as $name => $ua) {
    foreach (['/', '/llms.txt'] as $path) {
      $left = $deadline - microtime(true);
      if ($left < 1) { $out['rows'][] = ['bot' => $name, 'path' => $path, 'code' => 0, 'ok' => false, 'note' => 'Pominięto: osiągnięto limit czasu testu.']; continue; }
      $res = wp_remote_get(add_query_arg('zp_ai_test', (string) time(), home_url($path)), ['timeout' => min(8, $left), 'redirection' => 0, 'user-agent' => $ua, 'sslverify' => true, 'limit_response_size' => 2097152]);
      $out['rows'][] = zp_ai_access_result($res, $path, $name, $robots);
    }
  }
  return $out;
}

/** Private presentation helpers. Read cached data only; do not refresh Google or run crawler tests. */
function zp_ai_admin_ga4(array $ga, string $period): array {
  $report = is_array($ga['sourceReports'][$period] ?? null) ? $ga['sourceReports'][$period] : [];
  $health = is_array($ga['sourceHealth'][$period] ?? null) ? $ga['sourceHealth'][$period] : [];
  $rows = is_array($ga['aiByPeriod'][$period] ?? null) ? $ga['aiByPeriod'][$period] : null;
  $out = ['count' => null, 'rows' => $rows ?? [], 'report' => $report, 'status' => 'unavailable'];
  if (!in_array($report['status'] ?? '', ['ready', 'limited'], true) || $rows === null) { return $out; }
  $out['status'] = !empty($report['limited']) ? 'limited' : (!empty($report['stale']) ? 'stale' : 'unavailable');
  if (($report['status'] ?? '') !== 'ready' || ($health['totals_usable'] ?? false) !== true || !empty($report['limited']) || !empty($report['stale'])) { return $out; }
  $sum = 0;
  foreach ($rows as $row) {
    $n = is_array($row) ? ($row['sessions'] ?? null) : null;
    if (!is_numeric($n) || !is_finite((float) $n) || (float) $n < 0 || floor((float) $n) !== (float) $n) { return $out; }
    $sum += (int) $n;
  }
  $out['count'] = $sum; $out['status'] = 'ready'; return $out;
}

function zp_ai_admin_sum(array $days, int $n, string $today): array {
  $date = DateTimeImmutable::createFromFormat('!Y-m-d', $today, new DateTimeZone('UTC'));
  if (!$date || $date->format('Y-m-d') !== $today || $n < 1 || $n > 60) { return []; }
  $from = $date->modify('-' . ($n - 1) . ' days')->format('Y-m-d'); $out = [];
  foreach ($days as $day => $counts) {
    if (!is_string($day) || !preg_match('/^\d{4}-\d{2}-\d{2}$/D', $day) || !checkdate((int) substr($day, 5, 2), (int) substr($day, 8, 2), (int) substr($day, 0, 4)) || $day < $from || $day > $today || !is_array($counts)) { continue; }
    foreach ($counts as $key => $value) { if (is_numeric($value) && (float) $value >= 0 && is_finite((float) $value)) { $out[$key] = ($out[$key] ?? 0) + (int) $value; } }
  }
  return $out;
}

function zp_ai_admin_number($value): string { return $value === null ? '—' : number_format_i18n((int) $value); }
function zp_ai_admin_when($at): string { return is_numeric($at) && (int) $at > 0 ? wp_date('j.m.Y H:i', (int) $at) : 'brak zapisanego pomiaru'; }

function zp_ai_admin_page(): void {
  if (!current_user_can('manage_options')) { return; }
  $s = zp_ai_stats(); $bots = zp_ai_bots(); $today = wp_date('Y-m-d');
  $b7 = zp_ai_admin_sum((array) $s['bots'], 7, $today); $b30 = zp_ai_admin_sum((array) $s['bots'], 30, $today);
  $r7 = zp_ai_admin_sum((array) $s['refs'], 7, $today); $r30 = zp_ai_admin_sum((array) $s['refs'], 30, $today);
  $google = [];
  if (function_exists('zp_admin_stats_data')) { try { $cached = zp_admin_stats_data(); $google = is_array($cached['ga4'] ?? null) ? $cached['ga4'] : []; } catch (Throwable $error) { $google = []; } }
  $traffic = []; foreach (['today', '7d', '28d'] as $period) { $traffic[$period] = zp_ai_admin_ga4($google, $period); }
  $stats_url = function_exists('zp_admin20_url') ? zp_admin20_url('zp-suite-stats') : admin_url('admin.php?page=zp-suite-stats');
  $llms_state = get_option('zp_ai_llms_file_state', ''); $llms_state = is_string($llms_state) ? $llms_state : '';
  $last = (array) get_option('zp_ai_indexnow_last', []); $retry = (array) get_option('zp_ai_indexnow_retry', []); $queue = (array) get_option('zp_ai_indexnow_queue', []);
  $test = get_option('zp_ai_access_test', []); $test = is_array($test) ? $test : []; $test_rows = is_array($test['rows'] ?? null) ? $test['rows'] : [];
  $search_fail = 0; $training_fail = 0; $incomplete = false;
  foreach ($test_rows as $row) {
    if (!is_array($row)) { continue; }
    if (!array_key_exists('ok', $row)) { $incomplete = true; continue; }
    if (!$row['ok']) { $purpose = $bots[(string) ($row['bot'] ?? '')][1] ?? ''; if ($purpose === 'train') { $training_fail++; } else { $search_fail++; } }
  }
  $manual_robots = file_exists(ABSPATH . 'robots.txt'); $enabled = zp_ai_on();
  $button = static function (string $do, string $label, string $class = 'button') {
    echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '">'; wp_nonce_field('zp_ai');
    echo '<input type="hidden" name="action" value="zp_ai"><input type="hidden" name="do" value="' . esc_attr($do) . '"><button class="' . esc_attr($class) . '">' . esc_html($label) . '</button></form>';
  };
  $shared = function_exists('zp_admin20_open') && function_exists('zp_admin20_close');
  if ($shared) { zp_admin20_open('Widoczność AI', 'ai', 'Ruch z asystentów, żądania robotów i ustawienia udostępniania treści.'); }
  else { echo '<div class="wrap zpAdmin20"><h1>Widoczność AI</h1>'; }
  $notice = isset($_GET['done']) && is_string($_GET['done']) ? sanitize_key($_GET['done']) : '';
  if ($notice === 'wait') { echo '<div class="notice notice-warning"><p>Nie rozpoczęto kolejnego zgłoszenia. Sprawdź zapisany stan kolejki; pełna lista ma odstęp co najmniej godziny po przyjęciu.</p></div>'; }
  elseif (in_array($notice, ['profiles', 'bulk', 'retry', 'llms', 'test'], true)) { echo '<div class="notice notice-info"><p>Zakończono działanie. Wynik i zapisany stan znajdziesz w odpowiedniej sekcji poniżej.</p></div>'; }
  echo '<p class="zpAdmin20__muted">Dostęp do strony, indeksacja, odesłanie użytkownika i cytowanie firmy to różne rzeczy. Ten panel pokazuje dane i narzędzia, które możesz sprawdzić; nie mierzy liczby cytowań w odpowiedziach AI.</p>';
  echo '<div class="zpAIAdminCards" style="display:grid;gap:20px">';
  $attention = [];
  if (!$enabled) { $attention[] = ['Moduł jest wstrzymany razem z Planem SEO.', admin_url('tools.php?page=zp-seo-plan'), 'Otwórz Plan SEO']; }
  if ($traffic['today']['count'] === null) { $attention[] = ['Dzisiejszy raport ruchu GA4 jest niedostępny, niepełny albo wymaga aktualizacji.', $stats_url, 'Sprawdź Statystyki']; }
  if ($llms_state === 'blocked') { $attention[] = ['Nie udało się zaktualizować pliku llms.txt.', '#zp-ai-settings', 'Sprawdź plik']; }
  if (!empty($retry['blocked'])) { $attention[] = ['Zgłoszenia IndexNow są wstrzymane po błędzie. Adresy zostały w kolejce.', '#zp-ai-indexnow', 'Sprawdź kolejkę']; }
  if ($search_fail) { $attention[] = ['Zapisany test ma nieudane sprawdzenia wyszukiwania lub pobierania stron: ' . $search_fail . '.', '#zp-ai-access', 'Zobacz test']; }
  if ($training_fail) { $attention[] = ['Zapisany test ma nieudane sprawdzenia robotów treningowych: ' . $training_fail . '. To osobny zakres od wyszukiwania AI.', '#zp-ai-access', 'Zobacz szczegóły']; }
  if (!$test_rows || $incomplete) { $attention[] = ['Brak pełnego zapisanego testu dostępu. Otwórz diagnostykę, gdy chcesz sprawdzić obecny stan.', '#zp-ai-access', 'Diagnostyka dostępu']; }
  echo '<section class="zpAdmin20__card"><h2>Co warto sprawdzić?</h2>';
  if (!$attention) { echo '<p>W zapisanych danych nie ma sygnałów wymagających działania. To nie jest potwierdzenie indeksacji ani cytowania.</p>'; }
  else { echo '<div class="zpAdmin20__rows">'; foreach ($attention as $item) { echo '<a href="' . esc_url($item[1]) . '"><span>' . esc_html($item[0]) . '</span><b>' . esc_html($item[2]) . ' →</b></a>'; } echo '</div>'; }
  echo '</section><section class="zpAdmin20__card" id="zp-ai-traffic"><div class="zpAdmin20__sectionTitle"><div><h2>Ruch z asystentów AI</h2><p class="zpAdmin20__muted">Sesje GA4 z rozpoznanych odesłań, m.in. ChatGPT, Claude, Gemini, Perplexity i Copilot. Sesja nie oznacza unikalnej osoby ani cytowania.</p></div><a href="' . esc_url($stats_url) . '">Pełne Statystyki →</a></div><div class="zpAdmin20__metrics" style="grid-template-columns:repeat(auto-fit,minmax(160px,1fr))">';
  foreach (['today' => 'Dzisiaj', '7d' => '7 dni', '28d' => '28 dni'] as $period => $label) {
    $metric = $traffic[$period]; $report = $metric['report'];
    echo '<div class="zpAdmin20__metric"><strong>' . esc_html(zp_ai_admin_number($metric['count'])) . '</strong><span>' . esc_html($label . ' · sesje z AI') . '</span><p class="zpAdmin20__muted">' . esc_html(!empty($report['start']) && !empty($report['end']) ? $report['start'] . ' — ' . $report['end'] : 'Brak pełnego raportu') . '<br>' . esc_html('Pobrano: ' . zp_ai_admin_when($report['fetched_at'] ?? null)) . '</p></div>';
  }
  echo '</div><p class="zpAdmin20__muted">„—” oznacza brak kompletnego, aktualnego raportu dla tego okresu. Nie jest zerowym ruchem. Część aplikacji nie przekazuje źródła i wtedy GA4 może zaliczyć wizytę do ruchu bezpośredniego. Dzisiaj jest okresem częściowym.</p>';
  $ga_rows = $traffic['28d']['rows'];
  if ($ga_rows && in_array($traffic['28d']['report']['status'] ?? '', ['ready', 'limited'], true)) {
    echo '<details class="zpAdmin20__details"><summary>Źródła AI w raporcie GA4 z 28 dni</summary><p class="zpAdmin20__muted">' . esc_html(($traffic['28d']['report']['start'] ?? '') . ' — ' . ($traffic['28d']['report']['end'] ?? '')) . ($traffic['28d']['status'] !== 'ready' ? ' · Zapisany raport jest niepełny lub starszy; wiersze nie stanowią aktualnej sumy.' : '') . '</p><div class="zpAdmin20__tableWrap"><table class="zpAdmin20__table"><thead><tr><th>Źródło</th><th>Medium</th><th>Sesje</th></tr></thead><tbody>';
    foreach (array_slice($ga_rows, 0, 50) as $row) { if (!is_array($row)) { continue; } echo '<tr><td>' . esc_html((string) ($row['source'] ?? '')) . '</td><td>' . esc_html((string) ($row['medium'] ?? '')) . '</td><td>' . esc_html(is_numeric($row['sessions'] ?? null) ? zp_ai_admin_number($row['sessions']) : '—') . '</td></tr>'; }
    echo '</tbody></table></div></details>';
  }
  echo '<details class="zpAdmin20__details"><summary>Dodatkowy licznik Suite: referrer i UTM</summary><p class="zpAdmin20__muted">Odrębne zdarzenia własnego beacona. Nie dodajemy ich do sesji GA4. Brak wpisów może wynikać także z braku przekazanego źródła lub niewykonania beacona.</p><div class="zpAdmin20__tableWrap"><table class="zpAdmin20__table"><thead><tr><th>Źródło</th><th>7 dni</th><th>30 dni</th></tr></thead><tbody>';
  arsort($r30); foreach ($r30 as $source => $n) { echo '<tr><td>' . esc_html((string) $source) . '</td><td>' . esc_html(zp_ai_admin_number($r7[$source] ?? 0)) . '</td><td>' . esc_html(zp_ai_admin_number($n)) . '</td></tr>'; }
  if (!$r30) { echo '<tr><td colspan="3">Nie zapisano rozpoznanych zdarzeń z AI w tym okresie.</td></tr>'; }
  echo '</tbody></table></div>';
  if (!empty($s['ref_last'])) { echo '<h3>Ostatnie zapisane odesłania</h3><ul>'; foreach (array_slice((array) $s['ref_last'], 0, 15) as $r) { if (is_array($r)) { echo '<li>' . esc_html(zp_ai_admin_when($r['at'] ?? null) . ' · ' . ($r['source'] ?? '') . ' → ' . ($r['path'] ?? '')) . '</li>'; } } echo '</ul>'; }
  echo '</details></section><section class="zpAdmin20__card" id="zp-ai-bots"><h2>Żądania robotów</h2><p class="zpAdmin20__muted">Poniżej wyszukiwarki i roboty pobierające treści. To żądania deklarujące dany User-Agent, bez weryfikacji IP. Cache/CDN może omijać WordPressa; zero nie potwierdza blokady.</p>';
  $primary_bots = ['OAI-SearchBot', 'Claude-SearchBot', 'PerplexityBot', 'Googlebot', 'bingbot'];
  $bot_table = static function (bool $other) use ($bots, $s, $b7, $b30, $primary_bots): void {
    $labels = ['search' => 'wyszukiwanie', 'user' => 'pobranie dla użytkownika', 'train' => 'trening modeli'];
    echo '<div class="zpAdmin20__tableWrap"><table class="zpAdmin20__table"><thead><tr><th>Robot</th><th>Rola</th><th>7 dni</th><th>30 dni</th><th>Ostatnie żądanie</th></tr></thead><tbody>';
    foreach ($bots as $token => $bot) {
      if ($bot[1] === 'token' || ($other === in_array($token, $primary_bots, true))) { continue; } $last_bot = $s['bot_last'][$token] ?? null;
      $when = is_array($last_bot) ? zp_ai_admin_when($last_bot['at'] ?? null) . ' · ' . ($last_bot['path'] ?? '') . ' · HTTP ' . (int) ($last_bot['code'] ?? 0) : '—';
      echo '<tr><td><code>' . esc_html($token) . '</code><br>' . esc_html($bot[0]) . '</td><td>' . esc_html($labels[$bot[1]]) . '</td><td>' . esc_html(zp_ai_admin_number($b7[$token] ?? 0)) . '</td><td>' . esc_html(zp_ai_admin_number($b30[$token] ?? 0)) . '</td><td>' . esc_html($when) . '</td></tr>';
    }
    echo '</tbody></table></div>';
  };
  $bot_table(false); echo '<details class="zpAdmin20__details"><summary>Pozostałe roboty, pobrania dla użytkownika i trening</summary><p class="zpAdmin20__muted">Pobranie dla użytkownika nie dowodzi cytowania. Dostęp do treningu i dostęp do wyszukiwania AI są osobnymi ustawieniami.</p>'; $bot_table(true); echo '</details></section>';

  echo '<div class="zpAdmin20__grid" id="zp-ai-settings"><section class="zpAdmin20__card"><h2>Opis firmy: llms.txt</h2>';
  $states = ['dynamic' => ['Generowany przez wtyczkę', 'Opis jest generowany i odświeżany po zmianach treści oraz pól Rank Math.'], 'static' => ['Plik aktualizowany przez Suite', 'Plik w katalogu strony jest odświeżany przez wtyczkę.'], 'blocked' => ['Aktualizacja zablokowana', 'Zapisany stan wskazuje, że plik w katalogu strony zasłania aktualny opis i wtyczka nie może go zmienić. Sprawdź uprawnienia lub plik w hostingu, a następnie odśwież opis.']];
  $state = $states[$llms_state] ?? ['Brak zapisanego stanu', 'Otwórz plik, aby sprawdzić jego zawartość. Odświeżenie jest osobnym działaniem.'];
  echo '<p><span class="zpAdmin20__status' . ($llms_state === 'blocked' ? ' is-warn' : '') . '">' . esc_html($state[0]) . '</span></p><p>' . esc_html($state[1]) . '</p><p class="zpAdmin20__muted">Stan zapisany przez Suite. Udostępnienie pliku nie gwarantuje, że asystent go wykorzysta.</p><div class="zpAdmin20__actions"><a class="button" href="' . esc_url(home_url('/llms.txt')) . '" target="_blank" rel="noopener">Otwórz llms.txt</a>'; $button('llms', 'Odśwież opis firmy'); echo '</div>';
  $prev = get_option('zp_ai_llms_previous');
  if (is_array($prev) && !empty($prev['text'])) { echo '<details class="zpAdmin20__details"><summary>Poprzednia kopia pliku</summary><p class="zpAdmin20__muted">Zapisano: ' . esc_html(zp_ai_admin_when($prev['at'] ?? null)) . '</p><pre style="white-space:pre-wrap;max-height:280px;overflow:auto">' . esc_html((string) $prev['text']) . '</pre></details>'; }
  echo '</section><section class="zpAdmin20__card"><h2>Robots i dane firmy</h2><p><span class="zpAdmin20__status">' . esc_html($enabled ? 'Plan SEO aktywny' : 'Plan SEO wstrzymany') . '</span></p><p>Suite udostępnia reguły robotów oraz wzbogaca wspólne dane firmy i usług o ofertę i profile.</p><p class="zpAdmin20__muted">' . ($manual_robots ? 'W katalogu strony jest własny robots.txt. Reguły z tego pliku obsługuje serwer; filtry WordPressa go nie zmieniają. Sprawdź faktyczne zasady w pliku.' : 'Reguły robots.txt obsługują WordPress i istniejące moduły. Zapisane reguły konkretnych robotów mają pierwszeństwo.') . ' Ten ekran nie sprawdza danych strukturalnych każdej podstrony.</p><div class="zpAdmin20__actions"><a class="button" href="' . esc_url(home_url('/robots.txt')) . '" target="_blank" rel="noopener">Otwórz robots.txt</a><a class="button" href="#zp-ai-access">Diagnostyka dostępu</a></div><details class="zpAdmin20__details"><summary>Profile firmy w danych strukturalnych</summary><p class="zpAdmin20__muted">Adresy własnych profili firmy trafiają do sameAs i llms.txt. Instagram, Facebook i Clutch są dodawane przez istniejący moduł. Dodaj rzeczywiste profile, po jednym adresie w wierszu.</p><form method="post" action="' . esc_url(admin_url('admin-post.php')) . '">';
  wp_nonce_field('zp_ai'); echo '<input type="hidden" name="action" value="zp_ai"><input type="hidden" name="do" value="profiles"><label for="zp-ai-profiles">Dodatkowe profile firmy</label><textarea id="zp-ai-profiles" name="profiles" rows="5" style="width:100%" placeholder="https://www.linkedin.com/company/…">' . esc_textarea(implode("\n", (array) get_option('zp_ai_profiles', []))) . '</textarea><p><button class="button button-primary">Zapisz profile</button></p></form></details></section></div>';

  echo '<section class="zpAdmin20__card" id="zp-ai-indexnow"><h2>Zgłaszanie zmian wyszukiwarkom</h2><p class="zpAdmin20__muted">IndexNow informuje obsługujące go wyszukiwarki, m.in. Bing, o nowych i zmienionych adresach. HTTP 200/202 oznacza przyjęcie zgłoszenia; indeksację sprawdza się oddzielnie.</p><p>' . esc_html(zp_ai_indexnow_by_rank_math() ? 'Nowe strony zgłasza Instant Indexing w Rank Math. Suite zachowuje swoje zgłaszanie pełnej listy i zmian z kanału.' : 'Suite kolejkuje nowe lub zmienione strony po publikacji.') . '</p><div class="zpAdmin20__tableWrap"><table class="zpAdmin20__table"><tbody><tr><th>W kolejce</th><td>' . esc_html(zp_ai_admin_number(count($queue))) . '</td></tr><tr><th>Ostatnie zgłoszenie</th><td>' . ($last ? esc_html(zp_ai_admin_when($last['at'] ?? null) . ' · ' . (int) ($last['count'] ?? 0) . ' adresów · ' . ((int) ($last['code'] ?? 0) ? 'HTTP ' . (int) $last['code'] . ' · ' : '') . ($last['msg'] ?? '') . ' (' . ($last['why'] ?? '') . ')') : 'Brak zapisanego zgłoszenia') . '</td></tr>';
  if ($retry) { echo '<tr><th>Ponowienia</th><td>' . esc_html(!empty($retry['blocked']) ? 'Wstrzymane po błędzie lub pięciu próbach. Kolejka pozostaje zachowana.' : 'Próba ' . (int) ($retry['attempts'] ?? 0) . ' · następna: ' . zp_ai_admin_when($retry['next'] ?? null)) . '</td></tr>'; }
  echo '</tbody></table></div><div class="zpAdmin20__actions">'; $button('bulk', 'Zgłoś pełną listę adresów'); if (!empty($retry['blocked']) && $queue) { $button('retry', 'Ponów zachowaną kolejkę'); } echo '</div><details class="zpAdmin20__details"><summary>Plik klucza i sygnały dla Google</summary>';
  $key = get_option('zp_ai_indexnow_key', '');
  if (is_string($key) && preg_match('~^[a-f0-9]{32}$~', $key)) { echo '<p>Publiczny plik klucza: <a href="' . esc_url(home_url('/' . $key . '.txt')) . '" target="_blank" rel="noopener">/' . esc_html($key) . '.txt</a></p>'; }
  else { echo '<p>Brak zapisanego klucza. Tworzy go istniejący mechanizm zgłaszania, nie samo otwarcie tego ekranu.</p>'; }
  do_action('zp_ai_screen_after_indexnow'); echo '</details></section>';

  echo '<section class="zpAdmin20__card" id="zp-ai-access"><h2>Diagnostyka dostępu</h2><p class="zpAdmin20__muted">Własny serwer wykonuje żądania z nazwami robotów i sprawdza HTTP, treść, canonical i robots.txt. To nie test z prawdziwych IP robotów ani potwierdzenie indeksacji. Google-Extended jest tokenem robots.txt, a nie osobnym robotem HTTP.</p><div class="zpAdmin20__actions">'; $button('test', 'Sprawdź dostęp teraz', 'button button-primary'); echo '</div><p class="zpAdmin20__muted">Ostatni zapisany test: ' . esc_html(zp_ai_admin_when($test['at'] ?? null)) . '. Otwarcie panelu nie wykonuje nowych żądań.</p>';
  if ($test_rows) {
    echo '<details class="zpAdmin20__details"><summary>Wyniki poszczególnych robotów' . ($search_fail || $training_fail ? ' — są sprawdzenia do przejrzenia' : '') . '</summary><div class="zpAdmin20__tableWrap"><table class="zpAdmin20__table"><thead><tr><th>Robot</th><th>Zakres</th><th>Adres</th><th>HTTP / wynik</th><th>Uwagi</th></tr></thead><tbody>';
    foreach ($test_rows as $row) { if (!is_array($row)) { continue; } $checked = array_key_exists('ok', $row); $ok = $checked && (bool) $row['ok']; $purpose = $bots[(string) ($row['bot'] ?? '')][1] ?? ''; $scope = $purpose === 'train' ? 'trening' : ($purpose === 'user' ? 'pobranie dla użytkownika' : 'wyszukiwanie'); echo '<tr><td>' . esc_html((string) ($row['bot'] ?? '')) . '</td><td>' . esc_html($scope) . '</td><td>' . esc_html((string) ($row['path'] ?? '')) . '</td><td><span class="zpAdmin20__status' . ($checked ? ($ok ? ' is-ok' : ' is-warn') : '') . '">' . esc_html(((int) ($row['code'] ?? 0) ?: '—') . ' · ' . (!$checked ? 'starszy wynik HTTP' : ($ok ? 'test poprawny' : 'sprawdź'))) . '</span></td><td>' . esc_html((string) ($row['note'] ?? '')) . '</td></tr>'; }
    echo '</tbody></table></div></details>';
  } else { echo '<p>Nie zapisano jeszcze testu. Wynik pojawi się po użyciu przycisku.</p>'; }
  echo '<p class="zpAdmin20__muted">Gdy pojawi się błąd, sprawdź odpowiedź i zakres robota. WAF/CDN oraz rzeczywiste żądania sprawdza się w logach hostingu; blokada treningu nie oznacza blokady wyszukiwania AI.</p></section>';
  $log = array_reverse((array) get_option(ZP_AI_LOG, []));
  echo '<section class="zpAdmin20__card"><details class="zpAdmin20__details"><summary>Dziennik działań Suite</summary><p class="zpAdmin20__muted">Zapisane działania i odpowiedzi mechanizmów. To log diagnostyczny, nie lista cytowań.</p><pre style="max-height:320px;overflow:auto;white-space:pre-wrap">' . esc_html($log ? implode("\n", $log) : 'Brak zapisanych działań.') . '</pre></details></section>';
  echo '</div>'; // Card stack.
  if ($shared) { zp_admin20_close(); } else { echo '</div>'; }
}
