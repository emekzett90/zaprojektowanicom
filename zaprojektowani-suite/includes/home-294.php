<?php
if (!defined('ABSPATH')) { exit; }

/**
 * ZP Suite 2.9.4 — two home page sections (Mat 8.10).
 *
 * "SEO • usługi • branże" (templates/seo-industries.php) has a tile for every industry page.
 * The four industries with a photo of a real project keep the photo tiles. The others get the
 * same tile without a photo (.zpHomeSeo__card): on computers a shorter row under the photo tiles,
 * with the same borders, icon circle, number and hover; on phones and tablets a list under the
 * swipeable photo tiles.
 *
 * "Strony internetowe dla firm z całej Polski" (templates/home-seo-intro.php, above the footer)
 * was four long paragraphs beside a list of four links. It keeps the same texts, now laid out as
 * a lead paragraph with the links beside it and three short points with headings below, so it
 * reads less like a block of text. The links get an arrow (drawn in CSS).
 *
 * Colours, fonts, borders and hover effects are taken from the existing tiles and lists.
 * No "[class...] *" or bare ":focus" rules (see speed-293.php).
 */
add_action('wp_head', function () {
  if (is_admin() || !is_front_page()) { return; }
  $line = 'rgba(255,255,255,.13)';
  // Lucide "arrow-up-right" as a mask, painted in the link's text colour.
  $arrow = 'url("data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 24 24%27 fill=%27none%27 stroke=%27%23000%27 stroke-width=%272%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27%3E%3Cpath d=%27M7 7h10v10%27/%3E%3Cpath d=%27M7 17 17 7%27/%3E%3C/svg%3E") center / contain no-repeat';
  echo '<style id="zp-home-294">'
    // Tiles without a photo, computers: one row of up to four under the photo tiles.
    . 'html body .zpHomeSeo .zpHomeSeo__more{position:relative;z-index:2;display:grid;grid-template-columns:repeat(4,minmax(0,1fr));border-left:1px solid ' . $line . ';background:rgba(255,255,255,.016)}'
    . 'html body .zpHomeSeo .zpHomeSeo__card{position:relative;display:flex;flex-direction:column;justify-content:flex-end;min-height:250px;padding:clamp(24px,2.1vw,34px);border-right:1px solid ' . $line . ';border-bottom:1px solid ' . $line . ';background:#05070b radial-gradient(circle at 18% 0%,rgba(59,110,168,.13),transparent 46%);color:#fff;text-decoration:none;overflow:hidden;transition:background .24s ease,border-color .24s ease,transform .24s cubic-bezier(.16,1,.3,1)}'
    . 'html body .zpHomeSeo .zpHomeSeo__card:hover,html body .zpHomeSeo .zpHomeSeo__card:focus-visible{transform:translateY(-3px);background:linear-gradient(rgba(255,255,255,.067),rgba(255,255,255,.024)),#05070b;border-color:rgba(59,110,168,.36)}'
    . 'html body .zpHomeSeo .zpHomeSeo__cardIcon{position:absolute;top:clamp(22px,2vw,30px);left:clamp(22px,2vw,30px);display:flex;align-items:center;justify-content:center;width:58px;height:58px;border:1px solid rgba(255,255,255,.14);border-radius:999px;background:rgba(255,255,255,.08);color:#fff}'
    . 'html body .zpHomeSeo .zpHomeSeo__cardIcon svg{width:22px;height:22px;stroke:currentColor}'
    . 'html body .zpHomeSeo .zpHomeSeo__cardNum{position:absolute;top:clamp(18px,1.7vw,28px);right:clamp(18px,1.7vw,28px);font-size:12px;line-height:1.2;font-weight:850;letter-spacing:.12em;color:rgba(255,255,255,.28)}'
    . 'html body .zpHomeSeo .zpHomeSeo__card strong{display:block;margin:0;font-size:clamp(21px,1.6vw,26px);line-height:1.05;font-weight:560;letter-spacing:-.045em;color:#fff}'
    . 'html body .zpHomeSeo .zpHomeSeo__cardText{display:block;margin-top:10px;max-width:420px;font-size:13px;line-height:1.58;color:rgba(255,255,255,.74)}'
    . '@media (max-width:1180px){html body .zpHomeSeo .zpHomeSeo__more{grid-template-columns:repeat(2,minmax(0,1fr))}}'
    // Phones and tablets: a list under the swipeable photo tiles.
    . '@media (max-width:900px){'
    . 'html body .zpHomeSeo .zpHomeSeo__more{grid-template-columns:1fr;gap:10px;margin-top:4px;border-left:0;background:none}'
    . 'html body .zpHomeSeo .zpHomeSeo__card{display:grid;grid-template-columns:46px minmax(0,1fr);column-gap:14px;align-content:center;align-items:center;min-height:0;padding:16px 46px 16px 16px;border:1px solid ' . $line . ';border-radius:22px}'
    . 'html body .zpHomeSeo .zpHomeSeo__card:hover,html body .zpHomeSeo .zpHomeSeo__card:focus-visible{transform:none}'
    . 'html body .zpHomeSeo .zpHomeSeo__cardIcon{position:static;grid-row:1 / span 2;width:46px;height:46px}'
    . 'html body .zpHomeSeo .zpHomeSeo__cardIcon svg{width:20px;height:20px}'
    . 'html body .zpHomeSeo .zpHomeSeo__cardNum{top:16px;right:18px}'
    . 'html body .zpHomeSeo .zpHomeSeo__card strong{grid-column:2;font-size:18px;line-height:1.15;letter-spacing:-.035em}'
    . 'html body .zpHomeSeo .zpHomeSeo__cardText{grid-column:2;margin-top:4px;font-size:12.5px;line-height:1.5}'
    . '}'
    // "Strony internetowe dla firm z całej Polski": lead and links, then three points.
    . 'html body .zpHomeSeoIntro--294 .zpHomeSeoIntro__inner{align-items:end;row-gap:0}'
    . 'html body .zpHomeSeoIntro--294 .zpHomeSeoIntro__copy p{max-width:780px;font-size:clamp(16px,1.3vw,19px);line-height:1.7}'
    // The arrow is drawn in CSS, so the links' text and its English version stay as they were.
    . 'html body .zpHomeSeoIntro--294 .zpHomeSeoIntro__links a::after{content:"";flex:0 0 18px;width:18px;height:18px;margin-left:auto;background:currentColor;opacity:.42;-webkit-mask:' . $arrow . ';mask:' . $arrow . ';transition:transform .22s ease,opacity .22s ease}'
    . 'html body .zpHomeSeoIntro--294 .zpHomeSeoIntro__links a:hover::after,html body .zpHomeSeoIntro--294 .zpHomeSeoIntro__links a:focus-visible::after{opacity:1;transform:translate(2px,-2px)}'
    . 'html body .zpHomeSeoIntro--294 .zpHomeSeoIntro__points{grid-column:1 / -1;display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:clamp(28px,3.4vw,64px);margin-top:clamp(52px,5vw,84px)}'
    . 'html body .zpHomeSeoIntro--294 .zpHomeSeoIntro__point{position:relative;padding-top:26px;border-top:1px solid rgba(6,16,31,.12)}'
    . 'html body .zpHomeSeoIntro--294 .zpHomeSeoIntro__point::before{content:"";position:absolute;top:-1px;left:0;width:44px;height:2px;background:#071426}'
    . 'html body .zpHomeSeoIntro--294 .zpHomeSeoIntro__point h3{margin:0 0 12px;font-size:clamp(19px,1.45vw,22px);line-height:1.22;font-weight:680;letter-spacing:-.03em;color:#05101f}'
    . 'html body .zpHomeSeoIntro--294 .zpHomeSeoIntro__point p{margin:0;max-width:none;font-size:clamp(15px,1.08vw,16px);line-height:1.72;color:#445165}'
    . '@media (max-width:900px){'
    . 'html body .zpHomeSeoIntro--294 .zpHomeSeoIntro__inner{row-gap:20px}'
    . 'html body .zpHomeSeoIntro--294 .zpHomeSeoIntro__points{grid-template-columns:1fr;gap:26px;margin-top:20px}'
    . 'html body .zpHomeSeoIntro--294 .zpHomeSeoIntro__point{padding-top:20px}'
    . 'html body .zpHomeSeoIntro--294 .zpHomeSeoIntro__point h3{margin-bottom:8px;font-size:19px}'
    . 'html body .zpHomeSeoIntro--294 .zpHomeSeoIntro__point p{font-size:15px;line-height:1.65}'
    . '}'
    . '@media (prefers-reduced-motion:reduce){html body .zpHomeSeo .zpHomeSeo__card,html body .zpHomeSeoIntro--294 .zpHomeSeoIntro__links a::after{transition:none}}'
    . '</style>' . "\n";
}, 60);

/* ---------------------------------------------------------------------------------------------------------
 * Home page FAQ (templates/seo-faq.php), Mat 8.10: "dostosowane pod SEO i AI".
 *
 * The questions are the ones people type into Google and ask ChatGPT, Gemini or Claude. Each answer opens
 * with the full answer and the numbers (Mat's prices, the deadlines from llms.txt), reads on its own when
 * quoted without the question, and names the studio the same way as /llms.txt. Four answers link to the
 * service page they talk about. The FAQPage markup in the template reads the same items.
 *
 * Shown instead of the FAQ the plugin wrote earlier (cms.php defaults and the 2.2.107 / 2.2.109 lists,
 * recognised by their text): a question added or edited in ZP Suite → CMS stays, after the new ones.
 * ------------------------------------------------------------------------------------------------------ */

function zp_home_faq_294_norm(string $s): string {
  $t = html_entity_decode(wp_strip_all_tags($s), ENT_QUOTES | ENT_HTML5, 'UTF-8');
  return trim((string) preg_replace('~\s+~u', ' ', str_replace("\xC2\xA0", ' ', $t)));
}

/** md5 (first 12 characters) of "question|answer" of every FAQ item the plugin itself stored. */
function zp_home_faq_294_plugin_items(): array {
  return [
    '4c8c6b9592d1', 'ce8595105164', '54a09ee6e622', '06153bfbda7f', '028ab7992d2c', 'dd56a1fdd375',
    '0505a9050be0', '005a7e8476f3', 'fe351ab5ea85', '39772b8d0a45', '689e46309372', '5e4ccf677820',
    'c753e8a46ca6', 'f5b81862e7c8', '9c23996a0194', 'fbdad3a9d736', 'ac0f68537a76', '8eea35372b53',
    '29f92a5583aa', '9a3e7a001318', '01a890a27da5', '72f33b259c2e',
  ];
}

function zp_home_faq_294_new(): array {
  $L = static function (string $path, string $text, string $attrs = ''): string {
    return function_exists('zp_seo_plan_live_link') ? zp_seo_plan_live_link($path, $text, '', $attrs) : $text;
  };
  return [
    ['q' => 'Czym zajmuje się Zaprojektowani?',
     'a' => 'Zaprojektowani to studio projektowe z Katowic, które tworzy strony internetowe, sklepy WooCommerce, logo, identyfikację wizualną i kampanie reklamowe dla firm z całej Polski. Studio działa od 2022 roku, nasz zespół ma ponad 10 lat doświadczenia, a na koncie mamy ponad 110 zrealizowanych projektów.'],
    ['q' => 'Ile kosztuje strona internetowa dla firmy?',
     'a' => 'Strona internetowa dla firmy kosztuje u nas od 3 999 zł, a domena i hosting są w cenie. Gotowa strona powstaje zwykle w 2–4 tygodnie. Cena rośnie z liczbą podstron, treściami, animacjami, SEO i integracjami; zakres i przykłady opisujemy na stronie ' . $L('/tworzenie-stron-internetowych/', 'tworzenie stron internetowych') . '.'],
    ['q' => 'Ile trwa stworzenie strony internetowej?',
     'a' => 'Strona firmowa powstaje zwykle w 2–4 tygodnie od briefu, sklep internetowy w 3–6 tygodni, a projekt logo w 7–14 dni. Termin zależy głównie od zakresu i od tego, kiedy dostaniemy teksty i zdjęcia. Dokładny harmonogram podajemy razem z wyceną.'],
    ['q' => 'Ile kosztuje sklep internetowy?',
     'a' => 'Sklep internetowy WooCommerce kosztuje u nas od 6 499 zł i powstaje zwykle w 3–6 tygodni. W projekcie są kategorie i karty produktów, koszyk, płatności, dostawy i podstawy SEO. Cena zależy od liczby produktów, integracji i funkcji na zamówienie; więcej o zakresie piszemy na stronie ' . $L('/tworzenie-sklepow-internetowych/', 'tworzenie sklepów internetowych') . '.'],
    ['q' => 'Ile kosztuje projekt logo?',
     'a' => 'Projekt logo kosztuje u nas od 999 zł i trwa zwykle 7–14 dni. Do wyboru są trzy pakiety o stałej cenie: Logo Startowe za 999 zł, Mini Branding za 1 499 zł i Branding Premium za 2 999 zł, który obejmuje pełną identyfikację wizualną z księgą znaku. Szczegóły pakietów są na stronie ' . $L('/projektowanie-logo/', 'projektowanie logo') . '.'],
    ['q' => 'Ile kosztuje prowadzenie kampanii Meta Ads?',
     'a' => 'Stała obsługa kampanii Meta Ads na Facebooku i Instagramie albo Google Ads kosztuje u nas od 1 200 zł miesięcznie, a kampanię uruchamiamy zwykle w 7 dni. W tej kwocie są strategia, kreacje, konfiguracja pomiaru i bieżąca optymalizacja. Budżet na same reklamy płacisz osobno; zakres obsługi opisujemy na stronie ' . $L('/kampanie-reklamowe/', 'kampanie reklamowe') . '.'],
    ['q' => 'Czy strona będzie przygotowana pod SEO i Google?',
     'a' => 'Tak. Każdą stronę przygotowujemy pod SEO od pierwszego dnia: osobna podstrona dla każdej ważnej usługi, nagłówki z frazami, których używają klienci, szybkie ładowanie na telefonie, mapa witryny i dane strukturalne. Pierwsze efekty w Google widać zwykle po kilku tygodniach, a stabilne pozycje budują się przez kilka miesięcy.'],
    ['q' => 'Czy strona będzie widoczna w ChatGPT i innych asystentach AI?',
     'a' => 'Projektujemy strony tak, żeby ChatGPT, Gemini, Claude i Copilot mogły je przeczytać i zacytować: najważniejsze treści są w kodzie strony, a nie tylko w skryptach, odpowiedzi stoją na początku sekcji, a ceny, adres i zakres usług są wszędzie takie same. Cytowania nikt nie gwarantuje, ale tak przygotowana strona ma na nie większą szansę.'],
    ['q' => 'Czy pracujecie z firmami z całej Polski?',
     'a' => 'Tak. Mamy siedzibę w Katowicach przy ul. Modelarskiej 18/2, ale strony, sklepy i logo projektujemy dla firm z całej Polski. Z firmami ze Śląska możemy spotkać się na miejscu (zobacz ' . $L('/strony-internetowe-katowice/', 'strony internetowe Katowice', 'data-zp-local="1"') . '), a z pozostałymi pracujemy zdalnie: brief, prezentacje projektu i odbiór robimy online.'],
    ['q' => 'Dla jakich branż tworzycie strony internetowe?',
     'a' => 'Mamy osobne oferty stron internetowych dla kancelarii, lekarzy i gabinetów, deweloperów, salonów beauty, trenerów personalnych, fotografów, restauracji i hoteli. Projektujemy też sklepy dla producentów i marek premium oraz strony dla firm usługowych i B2B. W każdej branży układ strony prowadzi klienta do decyzji w inny sposób.'],
    ['q' => 'Czy można zamówić stronę, logo i branding w jednym projekcie?',
     'a' => 'Tak, i często to najlepszy wybór: logo, kolory, typografia i strona powstają jako jeden system, więc marka wygląda tak samo na stronie, w social mediach i w reklamach. Zwykle zaczynamy od logo i identyfikacji wizualnej, a potem przenosimy je na stronę lub sklep. Cały zakres i kolejność prac dostajesz w jednej wycenie.'],
    ['q' => 'Jak zamówić wycenę strony internetowej?',
     'a' => 'Najprościej przez ' . $L('/studio-wyceny/', 'Studio Wyceny') . ': wybierasz usługę, zaznaczasz zakres i wysyłasz krótki brief. Bezpłatną i niezobowiązującą wycenę z proponowanym zakresem dostajesz w 48–72 godziny. Możesz też zadzwonić pod numer +48 501 054 253 albo napisać na kontakt@zaprojektowani.com.'],
  ];
}

/** The items the home FAQ shows: the new ones instead of the plugin's earlier ones, then the site's own. */
function zp_home_faq_294_items(array $stored): array {
  if (!$stored || !function_exists('zp_seo_plan_active') || !zp_seo_plan_active()) { return $stored; }
  $known = zp_home_faq_294_plugin_items();
  $own = [];
  $plugin = false;
  foreach ($stored as $row) {
    $hash = substr(md5(zp_home_faq_294_norm((string) ($row['q'] ?? '')) . '|' . zp_home_faq_294_norm((string) ($row['a'] ?? ''))), 0, 12);
    if (in_array($hash, $known, true)) { $plugin = true; } else { $own[] = $row; }
  }
  if (!$plugin) { return $stored; }
  $new = array_map(static function ($row) { return $row + ['html' => true]; }, zp_home_faq_294_new());
  return array_merge($new, $own);
}
