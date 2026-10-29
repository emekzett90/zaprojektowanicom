<?php
/**
 * SearchFit audit of 14.10.2026, points 2 and 5 (Polish pages; report raport-20261014T071629-a4e9.md).
 *
 * Point 5: Google cuts titles over about 60 characters and descriptions over about 155. 16 SEO titles were still
 * longer after 3.1.0 (63 to 109 characters) and 20 descriptions longer than 160 (up to 217). They get the audit's
 * proposals (tytuly-pl.tsv and opisy-meta-pl.tsv): 60 and 155 characters at most, the post's phrase first, the
 * same tone. Point 2: the description of /strony-internetowe/woocommerce-czy-shopify/ had the description of
 * another post pasted around it (277 characters, the same in og:description and twitter:description); it is one
 * of the 20. The English pages follow through the translator (it takes the changed Polish text).
 *
 * Runs once, late on admin_init, on an administrator's ordinary wp-admin visit after the update, like the titles
 * of 13.10 (seo-audit-20261013-titles.php). A title or description someone has already made short is left alone:
 * the proposal replaces only an empty field (Rank Math then builds it from its template, which made them long), a
 * template with variables, or a value over the limit. A Facebook or X description that repeats the old one or is
 * over the limit gets the new one too; an empty one already follows the description. Every old value is saved
 * first (option zp_searchfit_meta_backup); the notice after the run links to putting them back (only values
 * nobody has changed since). Addresses, post titles and the visible pages stay as they are.
 */
if (!defined('ABSPATH')) { exit; }

const ZP_SEARCHFIT_META_VERSION = '2026-10-14';
const ZP_SEARCHFIT_META_BACKUP = 'zp_searchfit_meta_backup';
const ZP_SEARCHFIT_META_RESULT = 'zp_searchfit_meta_result';
const ZP_SEARCHFIT_META_LOCK = 'zp_searchfit_meta_lock';

/** path => new SEO title and/or meta description. */
function zp_searchfit_meta_rows(): array {
  return (array) apply_filters('zp_searchfit_meta_rows', [
    '/kampanie-reklamowe-meta-ads/landing-page-czy-strona-internetowa-co-wybrac-pod-kampanie/' => ['title' => 'Landing page czy strona internetowa pod kampanię?'],
    '/logo-branding/branding-firmy-od-podstaw/' => ['description' => 'Branding firmy od podstaw: kiedy ma sens, czym różni się od rebrandingu i jak przygotować firmę do procesu budowy marki.'],
    '/logo-branding/dlaczego-profesjonalne-logo-ma-znaczenie-dla-firmy/' => ['title' => 'Dlaczego profesjonalne logo ma znaczenie dla firmy?'],
    '/logo-branding/gadzety-z-logo-firmy-rozne-kolory-porownanie-probek/' => ['title' => 'Gadżety z logo firmy – różne kolory i porównanie próbek'],
    '/logo-branding/jakie-cechy-powinno-miec-dobre-logo-firmowe/' => ['description' => 'Jakie cechy powinno mieć dobre logo firmowe? Czytelność, prostota, skalowalność, kolory i przygotowanie do druku oraz internetu.'],
    '/logo-branding/kalendarze-z-logo-firmy-notatki-daty-kontrola-przed-drukiem/' => ['title' => 'Kalendarze z logo firmy – projekt i kontrola przed drukiem'],
    '/logo-branding/logo-firmy-odziezowej-marka-i-kolekcje-na-metkach-oraz-w-sklepie/' => ['title' => 'Logo firmy odzieżowej – marka i kolekcje na metkach'],
    '/logo-branding/projektowanie-logo-a-wspolpraca-zdalna/' => ['title' => 'Projektowanie logo zdalnie – jak wygląda współpraca', 'description' => 'Projektowanie logo zdalnie: brief, feedback, poprawki, umowa, prawa autorskie i pliki końcowe. Cały proces bez spotkań na żywo.'],
    '/logo-branding/remarketing-dla-malej-firmy-kiedy-ma-sens/' => ['title' => 'Remarketing dla małej firmy – kiedy ma sens?', 'description' => 'Remarketing dla małej firmy: kiedy ma sens, jak działa w Meta Ads i jak przygotować stronę, żeby odzyskiwać wartościowy ruch.'],
    '/meta-ads/jak-zwiekszyc-sprzedaz-dzieki-facebook-ads/' => ['title' => 'Jak zwiększyć sprzedaż sklepu dzięki Facebook Ads?'],
    '/meta-ads/kampanie-reklamowe-facebook-i-instagram-najczestsze-bledy/' => ['description' => 'Najczęstsze błędy w kampaniach Facebook i Instagram: cel kampanii, piksel, kreacje, budżet i targetowanie. Zobacz, jak ich uniknąć.'],
    '/seo-content-marketing/strona-uslugowa-pod-google-ads-jak-przygotowac-oferte-i-landingi/' => ['title' => 'Strona usługowa pod Google Ads – oferta i landing page'],
    '/sklepy-internetowe/jakie-funkcje-sklepu-internetowego-zwiekszaja-sprzedaz/' => ['title' => 'Funkcje sklepu internetowego, które zwiększają sprzedaż', 'description' => 'Funkcje sklepu internetowego, które zwiększają sprzedaż: checkout, płatności, dostawy, karta produktu, wyszukiwarka i opinie.'],
    '/sklepy-internetowe/sklep-internetowy-dla-marki-premium-jak-polaczyc-estetyke-ux-i-sprzedaz/' => ['title' => 'Sklep internetowy dla marki premium – estetyka, UX, sprzedaż', 'description' => 'Sklep internetowy dla marki premium: jak połączyć estetykę, zaufanie i UX, żeby sklep wspierał sprzedaż od pierwszego kliknięcia.'],
    '/strony-internetowe/woocommerce-czy-shopify/' => ['description' => 'WooCommerce czy Shopify? Porównaj koszty, hosting, kontrolę, płatności, integracje i utrzymanie, aby dobrać platformę do procesu sprzedaży.'],
    '/strony-www/audyt-strony-internetowej-firmy-przed-reklamami-i-seo/' => ['description' => 'Audyt strony firmy przed reklamami i SEO: znajdź błędy, które obniżają konwersję, i nie przepalaj budżetu na ruch bez zapytań.'],
    '/strony-www/jak-sprawdzic-czy-strona-internetowa-dziala-skutecznie/' => ['description' => 'Jak sprawdzić, czy strona internetowa działa skutecznie? Wskaźniki, konwersje, raporty GA4 i SEO, które pokazują realny wpływ strony na biznes.'],
    '/strony-www/kiedy-warto-przebudowac-strone-internetowa-firmy/' => ['description' => 'Kiedy warto przebudować stronę internetową firmy? Sygnały, że wystarczy lifting, i takie, które wymagają nowej struktury, CMS i SEO.'],
    '/strony-www/lokalna-agencja-strony-internetowe/' => ['description' => 'Lokalna agencja czy zdalny wykonawca strony? Zobacz, kiedy bliskość pomaga, a kiedy ważniejsze są proces, portfolio, SEO i wsparcie.'],
    '/strony-www/oferta-uslug-na-stronie-www-jak-opisac-pakiety/' => ['title' => 'Oferta usług na stronie www – jak opisać pakiety', 'description' => 'Oferta usług na stronie www: jak opisać pakiety, zakres i wartość dla klienta, żeby strona była czytelna i zbierała więcej zapytań.'],
    '/strony-www/projektowanie-stron-www-a-obsluga-klientow-z-calej-polski/' => ['title' => 'Projektowanie stron www zdalnie – współpraca online', 'description' => 'Projektowanie stron www dla klientów z całej Polski: jak działa współpraca online, akceptacje, umowa, płatności i zdalna realizacja.'],
    '/strony-www/strona-internetowa-dla-malej-firmy/' => ['description' => 'Strona internetowa dla małej firmy: co musi mieć strona główna, jakie podstrony wybrać i jak zadbać o wersję mobilną oraz lokalne SEO.'],
    '/strony-www/strona-internetowa-dla-uslug-premium-jak-uzasadnic-wyzsza-cene/' => ['title' => 'Strona internetowa dla usług premium – jak uzasadnić cenę', 'description' => 'Strona internetowa dla usług premium powinna budować zaufanie przed kontaktem. Zobacz, jak układ i treści pomagają uzasadnić wyższą cenę.'],
    '/ux-cro-analityka/case-studies-na-stronie-firmowej-jak-pokazac-realizacje/' => ['title' => 'Case studies na stronie firmowej – jak pokazać realizacje'],
    '/ux-cro-analityka/formularz-kontaktowy-na-stronie-firmowej-jak-zwiekszyc-liczbe-zapytan/' => ['description' => 'Formularz kontaktowy na stronie firmowej: jak ograniczyć tarcie, poprawić UX i zwiększyć liczbę zapytań bez większego budżetu na reklamę.'],
    '/ux-cro-analityka/jakie-bledy-obnizaja-skutecznosc-strony-firmowej/' => ['title' => 'Błędy, które obniżają skuteczność strony firmowej', 'description' => 'Błędy, które obniżają skuteczność strony firmowej: UX, oferta, SEO, szybkość, formularz i analityka. Zobacz, jak je znaleźć.'],
    '/ux-cro-analityka/projektowanie-sklepu-internetowego-a-doswiadczenie-klienta/' => ['description' => 'Projektowanie sklepu internetowego a doświadczenie klienta: jak UX, checkout, mobile i karty produktów wpływają na konwersję.'],
    '/ux-cro-analityka/projektowanie-stron-www-z-mysla-o-uzytkowniku/' => ['description' => 'Projektowanie stron www z myślą o użytkowniku: jak UX wpływa na nawigację, formularze, wersję mobilną i konwersję.'],
  ]);
}

/** Whether a field already holds a plain value within the limit (left alone then). */
function zp_searchfit_meta_is_short(string $current, int $limit): bool {
  $current = trim($current);
  return $current !== '' && strpos($current, '%') === false && mb_strlen($current, 'UTF-8') <= $limit;
}

function zp_searchfit_meta_save_backup(array $backup): bool {
  update_option(ZP_SEARCHFIT_META_BACKUP, $backup, false);
  return get_option(ZP_SEARCHFIT_META_BACKUP) === $backup;
}

function zp_searchfit_meta_write(int $id, string $path, string $key, string $value, array &$backup): string {
  $current = get_post_meta($id, $key, true);
  if ($current === $value) { return 'unchanged'; }
  $slot = $id . ':' . $key;
  if (!isset($backup['meta'][$slot])) {
    $backup['meta'][$slot] = ['id' => $id, 'path' => $path, 'key' => $key, 'existed' => metadata_exists('post', $id, $key), 'old' => $current];
  }
  $backup['meta'][$slot]['written'] = $value;
  if (!zp_searchfit_meta_save_backup($backup)) { return 'backup_failed'; }
  update_post_meta($id, $key, wp_slash($value));
  return get_post_meta($id, $key, true) === $value ? 'updated' : 'write_failed';
}

/** Once, on an ordinary administrator request; a run with errors repeats on the next one. */
function zp_searchfit_meta_run(): void {
  if (!is_admin() || !current_user_can('manage_options') || wp_doing_ajax() || wp_doing_cron() || (defined('REST_REQUEST') && REST_REQUEST)) { return; }
  if (!function_exists('zp_seo_plan_find_post') || !function_exists('zp_seo_plan_path')) { return; }
  $previous = get_option(ZP_SEARCHFIT_META_RESULT, []);
  if (is_array($previous) && ($previous['version'] ?? '') === ZP_SEARCHFIT_META_VERSION && (!empty($previous['complete']) || !empty($previous['restored']))) { return; }
  $lock = (int) get_option(ZP_SEARCHFIT_META_LOCK, 0);
  if ($lock && $lock < time() - 300) { delete_option(ZP_SEARCHFIT_META_LOCK); }
  if (!add_option(ZP_SEARCHFIT_META_LOCK, time(), '', false)) { return; }
  $backup = get_option(ZP_SEARCHFIT_META_BACKUP, []);
  $backup = is_array($backup) ? $backup : [];
  $backup += ['version' => ZP_SEARCHFIT_META_VERSION, 'created_utc' => gmdate('c'), 'meta' => []];
  $report = ['version' => ZP_SEARCHFIT_META_VERSION, 'started_utc' => gmdate('c'), 'complete' => false, 'rows' => [], 'errors' => []];
  try {
    foreach (zp_searchfit_meta_rows() as $path => $row) {
      $id = (int) zp_seo_plan_find_post($path);
      $line = ['path' => $path, 'id' => $id];
      if (!$id || get_post_type($id) !== 'post' || get_post_status($id) !== 'publish' || zp_seo_plan_path((string) get_permalink($id)) !== $path) {
        $report['rows'][] = $line + ['state' => 'missing'];
        continue;
      }
      if (isset($row['title'])) {
        $line['title'] = zp_searchfit_meta_is_short((string) get_post_meta($id, 'rank_math_title', true), 60)
          ? 'kept_short' : zp_searchfit_meta_write($id, $path, 'rank_math_title', $row['title'], $backup);
      }
      if (isset($row['description'])) {
        $old = (string) get_post_meta($id, 'rank_math_description', true);
        $line['description'] = zp_searchfit_meta_is_short($old, 160)
          ? 'kept_short' : zp_searchfit_meta_write($id, $path, 'rank_math_description', $row['description'], $backup);
        if ($line['description'] === 'updated') {
          foreach (['rank_math_facebook_description' => 'facebook', 'rank_math_twitter_description' => 'twitter'] as $key => $field) {
            $social = trim((string) get_post_meta($id, $key, true));
            if ($social !== '' && ($social === trim($old) || mb_strlen($social, 'UTF-8') > 160 || strpos($social, '%') !== false)) {
              $line[$field] = zp_searchfit_meta_write($id, $path, $key, $row['description'], $backup);
            }
          }
        }
      }
      foreach (['title', 'description', 'facebook', 'twitter'] as $field) {
        if (in_array($line[$field] ?? '', ['backup_failed', 'write_failed'], true)) { $report['errors'][] = $path . ':' . $field . ':' . $line[$field]; }
      }
      $report['rows'][] = $line;
    }
    $report['complete'] = !$report['errors'];
  } catch (\Throwable $e) {
    $report['errors'][] = $e->getMessage();
  } finally {
    $report['finished_utc'] = gmdate('c');
    update_option(ZP_SEARCHFIT_META_RESULT, $report, false);
    $GLOBALS['zp_searchfit_meta_notice'] = $report;
    delete_option(ZP_SEARCHFIT_META_LOCK);
  }
}
add_action('admin_init', 'zp_searchfit_meta_run', 103);

/** Puts back every value this file wrote that nobody has changed since. */
function zp_searchfit_meta_restore(): array {
  $backup = get_option(ZP_SEARCHFIT_META_BACKUP, []);
  $done = ['meta' => 0, 'kept' => 0];
  if (!is_array($backup)) { return $done; }
  foreach ((array) ($backup['meta'] ?? []) as $m) {
    if (get_post_meta((int) $m['id'], $m['key'], true) !== $m['written']) { $done['kept']++; continue; }
    if ($m['existed']) { update_post_meta((int) $m['id'], $m['key'], wp_slash($m['old'])); } else { delete_post_meta((int) $m['id'], $m['key']); }
    $done['meta']++;
  }
  $result = get_option(ZP_SEARCHFIT_META_RESULT, []);
  $result = is_array($result) ? $result : [];
  update_option(ZP_SEARCHFIT_META_RESULT, array_merge($result, ['version' => ZP_SEARCHFIT_META_VERSION, 'restored' => gmdate('c'), 'restore' => $done]), false);
  return $done;
}

add_action('admin_post_zp_searchfit_meta_restore', function () {
  if (!current_user_can('manage_options')) { wp_die('Brak uprawnień.'); }
  check_admin_referer('zp_searchfit_meta_restore');
  $done = zp_searchfit_meta_restore();
  wp_die(esc_html(sprintf('Przywrócono %d pól Rank Math; %d zmienionych od tego czasu zostawiono.', $done['meta'], $done['kept'])) . ' <a href="' . esc_url(admin_url()) . '">Kokpit</a>', 'Tytuły i opisy z audytu SearchFit', ['response' => 200]);
});

add_action('admin_notices', static function () {
  $report = $GLOBALS['zp_searchfit_meta_notice'] ?? null;
  if (!current_user_can('manage_options') || !is_array($report)) { return; }
  $count = ['title' => 0, 'description' => 0];
  $kept = $missing = 0;
  foreach ($report['rows'] as $row) {
    foreach ($count as $field => $n) { if (($row[$field] ?? '') === 'updated') { $count[$field]++; } }
    foreach (['title', 'description'] as $field) { if (($row[$field] ?? '') === 'kept_short') { $kept++; } }
    if (($row['state'] ?? '') === 'missing') { $missing++; }
  }
  $undo = wp_nonce_url(admin_url('admin-post.php?action=zp_searchfit_meta_restore'), 'zp_searchfit_meta_restore');
  $message = sprintf('Zaprojektowani, audyt SearchFit 14.10: krótsze tytuły i opisy w Google. Zmienione tytuły SEO: %d, opisy: %d; już krótkie zostawione: %d; nie znaleziono wpisów: %d. Adresy i strony bez zmian, poprzednie wartości zapisane.', $count['title'], $count['description'], $kept, $missing);
  echo '<div class="notice ' . (!empty($report['complete']) ? 'notice-success' : 'notice-error') . ' is-dismissible"><p>' . esc_html($message) . ' <a href="' . esc_url($undo) . '">Cofnij te zmiany</a></p>'
    . ($report['errors'] ? '<p>' . esc_html('Błędy (ponowna próba przy następnym wejściu): ' . implode(', ', array_slice($report['errors'], 0, 8))) . '</p>' : '') . '</div>';
});
