<?php
/**
 * SEO audit 13.10.2026 — the focus phrase in the title. Rank Math reported "11 stron i 53 wpisy, w których
 * ustawiona fraza kluczowa nie pojawia się w tytule": it compares the first focus phrase with the WordPress
 * title (for posts that is the visible H1).
 *
 * The table comes from the audit of 12.10 (zp-suite-3/analiza/audit.md, section 9): per address the new first
 * focus phrase (a natural variant of a broken one; the old phrase stays second, except three posts whose phrase
 * was wrong), the new SEO title (60 characters or fewer) and the new post title. Each new title holds its phrase.
 * Addresses and slugs stay. /o-nas/, /kontakt/ and /studio-wyceny/ are left to the work on those pages.
 * The same values are in seo-plan/data/plan.php for the 45 addresses the plan has.
 *
 * Runs once, late on admin_init, on an administrator's ordinary wp-admin visit after the update. A focus
 * phrase changed by hand since the audit is left alone. Every old value is saved first (option
 * zp_seo_audit_titles_backup); the notice after the run links to putting them all back (only values nobody has
 * changed since). Posts whose article prints its own H1 show the post title in it, as the plan's posts do; the
 * English page keeps its translated title until the translator handles the changed post.
 */
if (!defined('ABSPATH')) { exit; }

const ZP_SEO_AUDIT_TITLES_VERSION = '2026-10-13';
const ZP_SEO_AUDIT_TITLES_BACKUP = 'zp_seo_audit_titles_backup';
const ZP_SEO_AUDIT_TITLES_RESULT = 'zp_seo_audit_titles_result';
const ZP_SEO_AUDIT_TITLES_LOCK = 'zp_seo_audit_titles_lock';
const ZP_SEO_AUDIT_TITLES_H1 = 'zp_seo_audit_titles_h1';

/** path => type, the audited first focus phrase(s), new focus, what happens to the old list, new SEO title, new post title. */
function zp_seo_audit_titles(): array {
  return [
    '/kampanie-reklamowe/' => ['type' => 'page', 'was' => ['kampanie facebook ads'], 'post_title' => 'Kampanie Facebook Ads i Google Ads dla firm'],
    '/logo-branding-katowice/' => ['type' => 'page', 'was' => ['projektowanie logo katowice'], 'post_title' => 'Projektowanie logo Katowice i branding'],
    '/najczesciej-zadawane-pytania/' => ['type' => 'page', 'was' => ['najczęściej zadawane pytania'], 'seo_title' => 'Najczęściej zadawane pytania o strony | Zaprojektowani'],
    '/strony-internetowe-dla-fotografow/' => ['type' => 'page', 'was' => ['strona internetowa dla fotografa'], 'post_title' => 'Strona internetowa dla fotografa'],
    '/strony-internetowe-dla-kancelarii/' => ['type' => 'page', 'was' => ['strony internetowe dla prawników'], 'post_title' => 'Strony internetowe dla prawników i kancelarii'],
    '/strony-internetowe-dla-restauracji/' => ['type' => 'page', 'was' => ['strona internetowa dla restauracji'], 'post_title' => 'Strona internetowa dla restauracji'],
    '/strony-internetowe-dla-salonow-beauty/' => ['type' => 'page', 'was' => ['strona internetowa dla salonu kosmetycznego'], 'post_title' => 'Strona internetowa dla salonu kosmetycznego'],
    '/strony-internetowe-dla-trenerow-personalnych/' => ['type' => 'page', 'was' => ['strona internetowa dla trenera personalnego'], 'post_title' => 'Strona internetowa dla trenera personalnego'],
    '/wiedza/' => ['type' => 'page', 'was' => ['wiedza o stronach internetowych'], 'seo_title' => 'Wiedza o stronach internetowych i SEO | Zaprojektowani'],
    '/kampanie-reklamowe/google-ads-cala-polska-czy-lokalnie/' => ['type' => 'post', 'was' => ['Google Ads cała Polska czy lokalnie'], 'focus' => 'Google Ads lokalnie', 'seo_title' => 'Google Ads lokalnie czy na całą Polskę? Jak ustawić zasięg', 'post_title' => 'Google Ads lokalnie czy na całą Polskę? Jak dobrać obszar kampanii'],
    '/logo-branding/branding-firmy-od-podstaw/' => ['type' => 'post', 'was' => ['branding co to'], 'focus' => 'branding firmy', 'seo_title' => 'Branding firmy – co to jest i jak zbudować markę', 'post_title' => 'Branding firmy od podstaw – co to jest i kiedy jest potrzebny'],
    '/logo-branding/brief-do-logo-i-identyfikacji-wizualnej/' => ['type' => 'post', 'was' => ['brief logo'], 'focus' => 'brief do logo', 'post_title' => 'Brief do logo i identyfikacji wizualnej – jak go przygotować'],
    '/logo-branding/czym-rozni-sie-logo-od-brandingu/' => ['type' => 'post', 'was' => ['czym różni się logo od brandingu'], 'focus' => 'logo a branding'],
    '/logo-branding/identyfikacja-wizualna-firmy/' => ['type' => 'post', 'was' => ['identyfikacja wizualna firmy'], 'post_title' => 'Identyfikacja wizualna firmy – elementy, przykłady i wdrożenie'],
    '/logo-branding/ile-kosztuje-logo-dla-firmy-i-co-obejmuje-cena/' => ['type' => 'post', 'was' => ['projekt logo ceny'], 'focus' => 'ile kosztuje logo'],
    '/logo-branding/jak-wymyslic-nazwe-firmy/' => ['type' => 'post', 'was' => ['nazwa dla firmy'], 'focus' => 'jak wymyślić nazwę firmy', 'seo_title' => 'Jak wymyślić nazwę firmy? Metody i przykłady'],
    '/logo-branding/jakie-cechy-powinno-miec-dobre-logo-firmowe/' => ['type' => 'post', 'was' => ['cechy dobrego logo'], 'focus' => 'dobre logo firmowe', 'seo_title' => 'Jakie cechy powinno mieć dobre logo firmowe?'],
    '/logo-branding/kolory-i-typografia-marki/' => ['type' => 'post', 'was' => ['czcionka do logo'], 'focus' => 'kolory i typografia marki', 'seo_title' => 'Kolory i typografia marki – czcionka i kolory logo'],
    '/logo-branding/ksiega-znaku-mini-brandbook-pelna-identyfikacja-wizualna-co-wybrac/' => ['type' => 'post', 'was' => ['mini księga znaku'], 'post_title' => 'Mini księga znaku, brandbook czy pełna identyfikacja – co wybrać?'],
    '/logo-branding/logo-branding-i-strona-jaka-kolejnosc-wdrozenia-ma-sens/' => ['type' => 'post', 'was' => ['jak połączyć logo branding i stronę internetową'], 'focus' => 'branding i strona', 'seo_title' => 'Logo, branding i strona – jaka kolejność wdrożenia?', 'post_title' => 'Logo, branding i strona – jaka kolejność wdrożenia ma sens?'],
    '/logo-branding/logo-na-koszulce-kiedy-uproscic-do-haftu-lub-nadruku/' => ['type' => 'post', 'was' => ['koszulki z logo firmy'], 'focus' => 'logo na koszulce', 'seo_title' => 'Logo na koszulce: kiedy uprościć znak do haftu?', 'post_title' => 'Logo na koszulce: kiedy uprościć znak do haftu lub nadruku?'],
    '/logo-branding/projekt-logo-firmy-od-czego-zaczac-i-czego-unikac/' => ['type' => 'post', 'was' => ['jak zrobić logo firmy'], 'post_title' => 'Jak zrobić logo firmy – od czego zacząć i czego unikać'],
    '/logo-branding/projektowanie-logo-jak-powstaje-znak-ktory-dziala-w-internecie-druku-i-sprzedazy/' => ['type' => 'post', 'was' => ['jak zaprojektować logo'], 'post_title' => 'Jak zaprojektować logo, które działa w internecie, druku i sprzedaży?'],
    '/logo-branding/strategia-marki/' => ['type' => 'post', 'was' => ['strategia marki'], 'seo_title' => 'Strategia marki – co to jest i jak ją zbudować?', 'post_title' => 'Strategia marki – jak ją zbudować? Elementy, przykład i plan'],
    '/meta-ads/facebook-ads-dla-firm-lokalnych/' => ['type' => 'post', 'was' => ['lokalne reklamy facebook'], 'focus' => 'reklama na Facebooku dla firm lokalnych', 'seo_title' => 'Reklama na Facebooku dla firm lokalnych – co działa', 'post_title' => 'Reklama na Facebooku dla firm lokalnych – co działa w Facebook Ads?'],
    '/meta-ads/kampanie-facebook-ads-jakie-cele-wybierac-na-poczatku/' => ['type' => 'post', 'was' => ['kampanie Facebook Ads – jakie cele wybierać na początku'], 'focus' => 'cele kampanii Facebook Ads', 'seo_title' => 'Cele kampanii Facebook Ads – co wybrać na początku', 'post_title' => 'Cele kampanii Facebook Ads – jakie wybierać na początku'],
    '/meta-ads/kampanie-reklamowe-facebook-i-instagram-najczestsze-bledy/' => ['type' => 'post', 'was' => ['kampanie reklamowe Facebook i Instagram najczęstsze błędy'], 'focus' => 'błędy w kampaniach Facebook i Instagram', 'seo_title' => 'Błędy w kampaniach Facebook i Instagram – jak ich unikać', 'post_title' => 'Najczęstsze błędy w kampaniach Facebook i Instagram'],
    '/poradniki/jak-pisac-teksty-na-strone-zeby-nie-brzmialy-jak-katalog-uslug/' => ['type' => 'post', 'was' => ['teksty na stronę internetową'], 'focus' => 'teksty na stronę', 'seo_title' => 'Teksty na stronę – jak pisać, by nie brzmiały jak katalog', 'post_title' => 'Teksty na stronę – jak pisać, żeby nie brzmiały jak katalog usług?'],
    '/seo-i-konwersja/dlaczego-strona-ma-ruch-ale-nie-generuje-zapytan/' => ['type' => 'post', 'was' => ['dlaczego strona ma ruch ale nie generuje zapytań'], 'focus' => 'strona ma ruch', 'seo_title' => 'Strona ma ruch, ale nie generuje zapytań? Przyczyny', 'post_title' => 'Strona ma ruch, ale nie generuje zapytań – dlaczego?'],
    '/seo-i-konwersja/jak-polaczyc-branding-strone-reklamy-i-seo-w-jeden-system/' => ['type' => 'post', 'was' => ['Strony internetowe SEO i konwersja'], 'focus' => 'branding i SEO', 'drop_old' => 'first', 'seo_title' => 'Branding i SEO, strona i reklamy w jednym systemie', 'post_title' => 'Jak połączyć branding i SEO, stronę i reklamy w jeden system?'],
    '/seo-i-konwersja/landing-page-przyklady/' => ['type' => 'post', 'was' => ['landing page przykłady'], 'focus' => 'przykłady landing page', 'seo_title' => 'Przykłady landing page – skuteczne strony i wnioski', 'post_title' => 'Przykłady landing page – omówienie stron pod zapytania'],
    '/seo-i-konwersja/struktura-podstrony-uslugowej-premium/' => ['type' => 'post', 'was' => ['struktura podstrony usługowej premium'], 'focus' => 'struktura podstrony usługowej', 'seo_title' => 'Struktura podstrony usługowej premium – jak opisać ofertę', 'post_title' => 'Struktura podstrony usługowej premium – jak opisać ofertę?'],
    '/sklepy-internetowe/co-powinien-zawierac-sklep-internetowy-przed-startem-reklamy/' => ['type' => 'post', 'was' => ['sklep przed reklamą'], 'focus' => 'sklep internetowy przed startem reklamy', 'seo_title' => 'Sklep internetowy przed startem reklamy – co musi mieć?', 'post_title' => 'Co powinien zawierać sklep internetowy przed startem reklamy?'],
    '/sklepy-internetowe/koszyk-w-sklepie-internetowym-jak-ograniczyc-porzucone-zakupy/' => ['type' => 'post', 'was' => ['porzucone koszyki'], 'post_title' => 'Porzucone koszyki – jak je ograniczyć w sklepie internetowym?'],
    '/sklepy-internetowe/seo-lokalne-w-katowicach-jak-pozyskiwac-klientow-z-miasta-i-okolic/' => ['type' => 'post', 'was' => ['sklep internetowy na zamówienie', 'seo lokalne katowice'], 'focus' => 'SEO lokalne Katowice', 'drop_old' => 'all', 'seo_title' => 'SEO lokalne Katowice – jak zdobywać klientów z miasta', 'post_title' => 'SEO lokalne Katowice – jak pozyskiwać klientów z miasta i okolic'],
    '/sklepy-internetowe/sklep-internetowy-na-zamowienie/' => ['type' => 'post', 'was' => ['dedykowany sklep internetowy'], 'focus' => 'sklep internetowy na zamówienie', 'seo_title' => 'Sklep internetowy na zamówienie – kiedy lepszy niż szablon'],
    '/sklepy-internetowe/strona-internetowa-dla-branzy-beauty-jakie-sekcje-zwiekszaja-zapytania/' => ['type' => 'post', 'was' => ['strona internetowa dla branży beauty co powinna zawierać'], 'focus' => 'strona internetowa dla branży beauty', 'seo_title' => 'Strona internetowa dla branży beauty – jakie sekcje?'],
    '/sklepy-internetowe/strona-internetowa-dla-firmy-budowlanej-jak-pokazac-realizacje-i-zdobywac-wyceny/' => ['type' => 'post', 'was' => ['strona internetowa dla firmy budowlanej jak zdobywać zapytania'], 'focus' => 'strona internetowa dla firmy budowlanej', 'seo_title' => 'Strona internetowa dla firmy budowlanej – realizacje'],
    '/sklepy-internetowe/strona-internetowa-dla-firmy-z-katowic-co-powinna-zawierac-zeby-zdobywac-lokalnych-klientow/' => ['type' => 'post', 'was' => ['sklep internetowy na zamówienie', 'strona internetowa firmy z katowic'], 'focus' => 'strona internetowa dla firmy z Katowic', 'drop_old' => 'all', 'seo_title' => 'Strona internetowa dla firmy z Katowic – co zawrzeć?', 'post_title' => 'Strona internetowa dla firmy z Katowic – co powinna zawierać?'],
    '/strony-internetowe/agencja-czy-freelancer-do-stworzenia-strony/' => ['type' => 'post', 'was' => ['freelancer strony internetowe'], 'focus' => 'agencja czy freelancer'],
    '/strony-internetowe/makieta-strony-internetowej/' => ['type' => 'post', 'was' => ['makieta strony internetowej'], 'seo_title' => 'Makieta strony internetowej – co to jest i jak ją zrobić', 'post_title' => 'Makieta strony internetowej – wireframe, mockup i prototyp'],
    '/strony-internetowe/migracja-sklepu-na-woocommerce/' => ['type' => 'post', 'was' => ['migracja sklepu internetowego'], 'focus' => 'migracja sklepu na WooCommerce'],
    '/strony-internetowe/projektowanie-stron-internetowych-co-decyduje-o-zapytaniach/' => ['type' => 'post', 'was' => ['zasady tworzenia stron internetowych'], 'post_title' => 'Zasady tworzenia stron internetowych – układ, UX i treści'],
    '/strony-internetowe/przebudowa-strony-bez-utraty-widocznosci-w-google/' => ['type' => 'post', 'was' => ['migracja strony internetowej'], 'focus' => 'przebudowa strony bez utraty widoczności'],
    '/strony-internetowe/rodzaje-stron-internetowych/' => ['type' => 'post', 'was' => ['rodzaje stron internetowych'], 'seo_title' => 'Rodzaje stron internetowych – 12 typów i ich zastosowanie', 'post_title' => 'Rodzaje stron internetowych – który wybrać dla firmy?'],
    '/strony-internetowe/sklep-internetowy-swiat-grilli/' => ['type' => 'post', 'was' => ['sklep internetowy Świat Grilli'], 'post_title' => 'Sklep internetowy Świat Grilli – od brandingu do WooCommerce'],
    '/strony-internetowe/strona-internetowa-dla-firmy-co-powinna-zawierac/' => ['type' => 'post', 'was' => ['co powinna zawierać strona internetowa'], 'post_title' => 'Co powinna zawierać strona internetowa firmy?'],
    '/strony-internetowe/strona-internetowa-polerstone/' => ['type' => 'post', 'was' => ['strona internetowa Polerstone'], 'post_title' => 'Strona internetowa Polerstone i branding marki kamieniarskiej'],
    '/strony-internetowe/strona-na-abonament-czy-na-wlasnosc/' => ['type' => 'post', 'was' => ['strona internetowa w abonamencie'], 'focus' => 'strona na abonament'],
    '/strony-internetowe/szablon-czy-projekt-indywidualny-strony/' => ['type' => 'post', 'was' => ['strona internetowa szablon'], 'focus' => 'szablon czy projekt indywidualny'],
    '/strony-internetowe/tworzenie-stron-internetowych-profesjonalny-proces-od-strategii-do-wdrozenia/' => ['type' => 'post', 'was' => ['etapy tworzenia strony internetowej'], 'post_title' => 'Etapy tworzenia strony internetowej – terminy i odbiór projektu'],
    '/strony-internetowe/woocommerce-czy-shopify/' => ['type' => 'post', 'was' => ['shopify czy woocommerce'], 'focus' => 'WooCommerce czy Shopify'],
    '/strony-internetowe/wordpress-czy-strona-dedykowana/' => ['type' => 'post', 'was' => ['dedykowana strona internetowa'], 'focus' => 'WordPress czy strona dedykowana'],
    '/strony-internetowe/wordpress-wix-czy-webflow/' => ['type' => 'post', 'was' => ['webflow czy wordpress'], 'focus' => 'Wix czy Webflow'],
    '/strony-www/jak-przygotowac-brief-do-projektowania-strony-internetowej/' => ['type' => 'post', 'was' => ['brief strony internetowej'], 'post_title' => 'Brief strony internetowej – jak go przygotować do projektu'],
    '/strony-www/jak-sprawdzic-czy-strona-internetowa-dziala-skutecznie/' => ['type' => 'post', 'was' => ['skuteczna strona internetowa'], 'focus' => 'czy strona internetowa działa skutecznie', 'seo_title' => 'Jak sprawdzić, czy strona internetowa działa skutecznie?'],
    '/strony-www/kiedy-warto-przebudowac-strone-internetowa-firmy/' => ['type' => 'post', 'was' => ['przebudowa strony internetowej'], 'post_title' => 'Przebudowa strony internetowej firmy – kiedy warto?'],
    '/strony-www/lokalna-agencja-strony-internetowe/' => ['type' => 'post', 'was' => ['lokalna agencja strony internetowe'], 'focus' => 'lokalna agencja', 'seo_title' => 'Lokalna agencja czy zdalny wykonawca strony internetowej?', 'post_title' => 'Strona internetowa – czy lokalna agencja ma znaczenie?'],
    '/strony-www/strona-internetowa-dla-malej-firmy/' => ['type' => 'post', 'was' => ['strona www dla małej firmy'], 'focus' => 'strona internetowa dla małej firmy', 'seo_title' => 'Strona internetowa dla małej firmy – niezbędne elementy'],
    '/ux-cro-analityka/projektowanie-sklepu-internetowego-a-doswiadczenie-klienta/' => ['type' => 'post', 'was' => ['projektowanie sklepu internetowego a doświadczenie klienta'], 'focus' => 'projektowanie sklepu internetowego', 'seo_title' => 'Projektowanie sklepu internetowego a doświadczenie klienta', 'post_title' => 'Projektowanie sklepu internetowego a doświadczenie klienta (UX)'],
    '/ux-cro-analityka/projektowanie-stron-www-z-mysla-o-uzytkowniku/' => ['type' => 'post', 'was' => ['projektowanie stron www z myślą o użytkowniku'], 'focus' => 'projektowanie stron www', 'seo_title' => 'Projektowanie stron www z myślą o użytkowniku – UX', 'post_title' => 'Projektowanie stron www z myślą o użytkowniku – podstawy UX'],
    '/ux-cro-analityka/strona-internetowa-a-konwersja/' => ['type' => 'post', 'was' => ['optymalizacja konwersji'], 'post_title' => 'Optymalizacja konwersji – co naprawdę ma znaczenie na stronie'],
  ];
}

function zp_seo_audit_titles_lower(string $text): string {
  return mb_strtolower(trim($text), 'UTF-8');
}

/** The phrases of a Rank Math focus list, without repeats and the bulk editor's "number of tags exceeded". */
function zp_seo_audit_titles_tokens(string $list): array {
  $out = [];
  foreach (explode(',', $list) as $token) {
    $token = trim($token);
    if ($token === '' || $token === 'number of tags exceeded') { continue; }
    $key = zp_seo_audit_titles_lower($token);
    if (!isset($out[$key])) { $out[$key] = $token; }
  }
  return array_values($out);
}

/** The new focus list; the current one when it already starts with the new phrase; null when changed by hand. */
function zp_seo_audit_titles_focus(string $current, array $row): ?string {
  $tokens = zp_seo_audit_titles_tokens($current);
  if ($tokens && zp_seo_audit_titles_lower($tokens[0]) === zp_seo_audit_titles_lower($row['focus'])) { return $current; }
  if (!$tokens || !in_array(zp_seo_audit_titles_lower($tokens[0]), array_map('zp_seo_audit_titles_lower', $row['was']), true)) { return null; }
  $drop = $row['drop_old'] ?? '';
  if ($drop === 'all') { $tokens = []; }
  if ($drop === 'first') { array_shift($tokens); }
  array_unshift($tokens, $row['focus']);
  // Rank Math without Pro keeps five phrases; the last ones give way.
  return implode(', ', array_slice(zp_seo_audit_titles_tokens(implode(',', $tokens)), 0, 5));
}

/** Saves the backup and reads it back before any change. */
function zp_seo_audit_titles_save_backup(array $backup): bool {
  update_option(ZP_SEO_AUDIT_TITLES_BACKUP, $backup, false);
  return get_option(ZP_SEO_AUDIT_TITLES_BACKUP) === $backup;
}

function zp_seo_audit_titles_write_meta(int $id, string $path, string $key, string $value, array &$backup): string {
  $current = get_post_meta($id, $key, true);
  if ($current === $value) { return 'unchanged'; }
  if (!isset($backup['meta'][$id . ':' . $key])) {
    $backup['meta'][$id . ':' . $key] = ['id' => $id, 'path' => $path, 'key' => $key, 'existed' => metadata_exists('post', $id, $key), 'old' => $current, 'written' => $value];
  }
  $backup['meta'][$id . ':' . $key]['written'] = $value;
  if (!zp_seo_audit_titles_save_backup($backup)) { return 'backup_failed'; }
  update_post_meta($id, $key, wp_slash($value));
  return get_post_meta($id, $key, true) === $value ? 'updated' : 'write_failed';
}

function zp_seo_audit_titles_write_title(int $id, string $path, string $title, array &$backup): string {
  $post = get_post($id);
  if (!$post) { return 'missing'; }
  if ($post->post_title === $title) { return 'unchanged'; }
  if (!isset($backup['titles'][$id])) { $backup['titles'][$id] = ['id' => $id, 'path' => $path, 'old' => $post->post_title, 'slug' => $post->post_name]; }
  $backup['titles'][$id]['written'] = $title;
  if (!zp_seo_audit_titles_save_backup($backup)) { return 'backup_failed'; }
  // The slug is passed as it is, so the address cannot follow the new title.
  $data = ['ID' => $id, 'post_title' => $title, 'post_name' => $post->post_name];
  $result = function_exists('zp_seo_plan_update_post') ? zp_seo_plan_update_post($data, true) : wp_update_post(wp_slash($data), true);
  if (is_wp_error($result) || !$result) { return 'write_failed'; }
  clean_post_cache($id);
  $after = get_post($id);
  return $after && $after->post_title === $title && $after->post_name === $post->post_name ? 'updated' : 'write_failed';
}

/** Once, on an ordinary administrator request; a run with errors repeats on the next one. */
function zp_seo_audit_titles_run(): void {
  if (!is_admin() || !current_user_can('manage_options') || wp_doing_ajax() || wp_doing_cron() || (defined('REST_REQUEST') && REST_REQUEST)) { return; }
  if (!function_exists('zp_seo_plan_find_post') || !function_exists('zp_seo_plan_path')) { return; }
  $previous = get_option(ZP_SEO_AUDIT_TITLES_RESULT, []);
  if (is_array($previous) && ($previous['version'] ?? '') === ZP_SEO_AUDIT_TITLES_VERSION && (!empty($previous['complete']) || !empty($previous['restored']))) { return; }
  $lock = (int) get_option(ZP_SEO_AUDIT_TITLES_LOCK, 0);
  if ($lock && $lock < time() - 300) { delete_option(ZP_SEO_AUDIT_TITLES_LOCK); }
  if (!add_option(ZP_SEO_AUDIT_TITLES_LOCK, time(), '', false)) { return; }
  $backup = get_option(ZP_SEO_AUDIT_TITLES_BACKUP, []);
  $backup = is_array($backup) ? $backup : [];
  $backup += ['version' => ZP_SEO_AUDIT_TITLES_VERSION, 'created_utc' => gmdate('c'), 'meta' => [], 'titles' => []];
  $report = ['version' => ZP_SEO_AUDIT_TITLES_VERSION, 'started_utc' => gmdate('c'), 'complete' => false, 'rows' => [], 'errors' => []];
  try {
    foreach (zp_seo_audit_titles() as $path => $row) {
      $id = (int) zp_seo_plan_find_post($path);
      $line = ['path' => $path, 'id' => $id];
      if (!$id || get_post_type($id) !== $row['type'] || get_post_status($id) !== 'publish' || zp_seo_plan_path((string) get_permalink($id)) !== $path) {
        $report['rows'][] = $line + ['state' => 'missing'];
        continue;
      }
      if (isset($row['focus'])) {
        $focus = zp_seo_audit_titles_focus((string) get_post_meta($id, 'rank_math_focus_keyword', true), $row);
        $line['focus'] = $focus === null ? 'skipped_manual' : zp_seo_audit_titles_write_meta($id, $path, 'rank_math_focus_keyword', $focus, $backup);
      }
      if (isset($row['seo_title'])) { $line['seo_title'] = zp_seo_audit_titles_write_meta($id, $path, 'rank_math_title', $row['seo_title'], $backup); }
      if (isset($row['post_title'])) { $line['post_title'] = zp_seo_audit_titles_write_title($id, $path, $row['post_title'], $backup); }
      foreach (['focus', 'seo_title', 'post_title'] as $field) {
        if (in_array($line[$field] ?? '', ['backup_failed', 'write_failed', 'missing'], true)) { $report['errors'][] = $path . ':' . $field . ':' . $line[$field]; }
      }
      $report['rows'][] = $line;
    }
    $report['complete'] = !$report['errors'];
  } catch (\Throwable $e) {
    $report['errors'][] = $e->getMessage();
  } finally {
    $ids = [];
    foreach ((array) ($backup['titles'] ?? []) as $id => $t) { if (get_post_type((int) $id) === 'post') { $ids[] = (int) $id; } }
    update_option(ZP_SEO_AUDIT_TITLES_H1, $ids, true);
    $report['finished_utc'] = gmdate('c');
    update_option(ZP_SEO_AUDIT_TITLES_RESULT, $report, false);
    $GLOBALS['zp_seo_audit_titles_notice'] = $report;
    delete_option(ZP_SEO_AUDIT_TITLES_LOCK);
  }
}
add_action('admin_init', 'zp_seo_audit_titles_run', 102);

/** Puts back every value this file wrote that nobody has changed since. */
function zp_seo_audit_titles_restore(): array {
  $backup = get_option(ZP_SEO_AUDIT_TITLES_BACKUP, []);
  $done = ['meta' => 0, 'titles' => 0, 'kept' => 0];
  if (!is_array($backup)) { return $done; }
  foreach ((array) ($backup['meta'] ?? []) as $m) {
    if (get_post_meta((int) $m['id'], $m['key'], true) !== $m['written']) { $done['kept']++; continue; }
    if ($m['existed']) { update_post_meta((int) $m['id'], $m['key'], wp_slash($m['old'])); } else { delete_post_meta((int) $m['id'], $m['key']); }
    $done['meta']++;
  }
  foreach ((array) ($backup['titles'] ?? []) as $t) {
    $post = get_post((int) $t['id']);
    if (!$post || $post->post_title !== $t['written']) { $done['kept']++; continue; }
    $data = ['ID' => (int) $t['id'], 'post_title' => $t['old'], 'post_name' => $post->post_name];
    if (function_exists('zp_seo_plan_update_post')) { zp_seo_plan_update_post($data); } else { wp_update_post(wp_slash($data)); }
    clean_post_cache((int) $t['id']);
    $done['titles']++;
  }
  update_option(ZP_SEO_AUDIT_TITLES_H1, [], true);
  $result = get_option(ZP_SEO_AUDIT_TITLES_RESULT, []);
  $result = is_array($result) ? $result : [];
  update_option(ZP_SEO_AUDIT_TITLES_RESULT, array_merge($result, ['version' => ZP_SEO_AUDIT_TITLES_VERSION, 'restored' => gmdate('c'), 'restore' => $done]), false);
  return $done;
}

add_action('admin_post_zp_seo_audit_titles_restore', function () {
  if (!current_user_can('manage_options')) { wp_die('Brak uprawnień.'); }
  check_admin_referer('zp_seo_audit_titles_restore');
  $done = zp_seo_audit_titles_restore();
  wp_die(esc_html(sprintf('Przywrócono %d pól Rank Math i %d tytułów; %d zmienionych od tego czasu zostawiono.', $done['meta'], $done['titles'], $done['kept'])) . ' <a href="' . esc_url(admin_url()) . '">Kokpit</a>', 'Tytuły z audytu SEO', ['response' => 200]);
});

add_action('admin_notices', static function () {
  $report = $GLOBALS['zp_seo_audit_titles_notice'] ?? null;
  if (!current_user_can('manage_options') || !is_array($report)) { return; }
  $count = ['focus' => 0, 'seo_title' => 0, 'post_title' => 0];
  $manual = 0;
  foreach ($report['rows'] as $row) {
    foreach ($count as $field => $n) { if (($row[$field] ?? '') === 'updated') { $count[$field]++; } }
    if (($row['focus'] ?? '') === 'skipped_manual') { $manual++; }
  }
  $undo = wp_nonce_url(admin_url('admin-post.php?action=zp_seo_audit_titles_restore'), 'zp_seo_audit_titles_restore');
  $message = sprintf('Zaprojektowani, audyt SEO 13.10: fraza kluczowa w tytule. Zmienione frazy: %d, tytuły SEO: %d, tytuły wpisów i stron: %d; frazy zmienione ręcznie zostawione: %d. Adresy bez zmian, poprzednie wartości zapisane.', $count['focus'], $count['seo_title'], $count['post_title'], $manual);
  echo '<div class="notice ' . (!empty($report['complete']) ? 'notice-success' : 'notice-error') . ' is-dismissible"><p>' . esc_html($message) . ' <a href="' . esc_url($undo) . '">Cofnij te zmiany</a></p>'
    . ($report['errors'] ? '<p>' . esc_html('Błędy (ponowna próba przy następnym wejściu): ' . implode(', ', array_slice($report['errors'], 0, 8))) . '</p>' : '') . '</div>';
});

/**
 * The H1 of a post whose title this file changed shows the post title, when the article prints its own H1 and
 * the plan's code (seo-plan/posts.php, the same rule) did not already do it. Polish page only.
 */
function zp_seo_audit_titles_h1(string $html): string {
  if ($html === '' || is_admin() || !is_singular('post')) { return $html; }
  if (function_exists('zp_seo_plan_is_en') && zp_seo_plan_is_en()) { return $html; }
  $id = (int) get_queried_object_id();
  if (!$id || (in_the_loop() && get_the_ID() !== $id) || !in_array($id, (array) get_option(ZP_SEO_AUDIT_TITLES_H1, []), true)) { return $html; }
  $title = trim((string) get_post_field('post_title', $id));
  $at = strpos($html, 'class="zpArticleNew');
  if ($title === '' || !preg_match('~(<h1\b[^>]*>)(.*?)(</h1>)~is', $html, $m, PREG_OFFSET_CAPTURE, $at === false ? 0 : $at)) { return $html; }
  if (trim(html_entity_decode(wp_strip_all_tags($m[2][0]), ENT_QUOTES, 'UTF-8')) === $title) { return $html; }
  return substr($html, 0, $m[2][1]) . esc_html($title) . substr($html, $m[2][1] + strlen($m[2][0]));
}
add_filter('the_content', 'zp_seo_audit_titles_h1', 21);
add_filter('elementor/frontend/the_content', 'zp_seo_audit_titles_h1', 21);
