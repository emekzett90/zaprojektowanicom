<?php
/**
 * ZP Suite 3.2.94 — logotypy klientów w paskach: jeden model wielkości dla wszystkich logo.
 *
 * Mat 29.10.2026: „loga w pasku co się przewijają zrób tak, żeby wszystkie były do rozczytania … nie ważne czy logo jest
 * długie czy wysokie, wszystkie mają mieć podobne proporcje” (Raxo i Gravia za duże, Vista Architekci, Sfera i ProScarves
 * za małe). Dotąd każdy pasek wpisywał logo w to samo pudełko (np. 115 × 42 px, object-fit: contain), więc o wielkości
 * decydował kształt pliku: długie napisy i masywne znaki wypełniały pudełko, a wysokie, cienkie znaki dostawały skrawek.
 *
 * MODEL (wspólny dla wszystkich pasków; liczby w „px modelu”, czyli przy jednostce paska --zp-lu = 1px):
 *
 *   pole_i  = A · (d_ref / d_i)^k                    równe pole pudełka, z poprawką na gęstość „tuszu”
 *   w_i     = √(pole_i · ar_i),  h_i = √(pole_i / ar_i)
 *   potem   h_i ≤ H_MAX (wysokie znaki nie wychodzą z paska), w_i ≤ W_MAX, h_i ≥ H_MIN (długie napisy zostają czytelne)
 *
 *   ar_i = szerokość / wysokość pliku (pliki są przycięte do obrysu znaku, więc to proporcje samego logo),
 *   d_i  = gęstość tuszu: średnia alfa wewnątrz obrysu (0–1). Cienkie, ażurowe logo (AP 0.065, Vista 0.099) przy tym
 *          samym polu wygląda na mniejsze niż pełne, masywne (Raxo 0.616), więc dostaje więcej pola — ale tylko
 *          w potędze k (k = 1 wyrównałoby sam tusz i rozdmuchało cienkie znaki, k = 0 to czyste równe pole).
 *   Stałe dobrane na oko na ciemnym pasku (białe sylwetki) i sprawdzone na jasnym (czarne): A = 3100, k = 0.5,
 *   d_ref = 0.2, H_MAX = 56, H_MIN = 20, W_MAX = 150.
 *
 * Gęstości i proporcje zmierzono raz z plików (assets/strony-internetowe/client-*.webp, assets/logo-branding/logo-*.webp;
 * pliki z biblioteki mediów na stronie głównej, /wp-content/uploads/2026/05/*.webp, to te same obrazy). Nowe logo:
 * dopisać wiersz do zp_w94_logos_metrics() (alfa: średnia kanału alfa w obrysie). Logo bez wiersza zostaje w pasku
 * po staremu (pudełko z 3.2.93), więc nic się nie rozjedzie.
 *
 * Każdy pasek mnoży rozmiary modelu przez swoją jednostkę --zp-lu (assets/css/wyglad-3294/logos.css) i trzyma długość
 * pętli z 3.2.93: grupa logotypów ma dawną długość (liczba logo × (dawne pudełko + odstęp)), a logo stoją w niej
 * w równych odstępach (justify-content: space-around), więc prędkość przewijania i szew pętli się nie zmieniają.
 * Wymiary pudełek są w CSS (custom properties kluczowane nazwą pliku), więc pudełko jest znane, zanim plik dojdzie.
 *
 * Paski: hero szablonu „Strony internetowe” (tworzenie stron, strony Katowice, WordPress, landing page, wizytówka,
 * opieka WordPress, agencja reklamowa, 8 stron branżowych; EN to te same obiekty stron) i siatka „Zaufali nam” niżej
 * na tych samych stronach (te same logo, ten sam problem), pasek hero szablonu „Logo i branding” (projektowanie logo,
 * identyfikacja wizualna, logo branding Katowice) i „Zaufali nam” na stronie głównej (dwa przewijane rzędy: tablet /
 * mały laptop i telefon). Układ strony głównej od 1501 px (siatka przypięta przy przewijaniu) zostaje bez zmian.
 */
if (!defined('ABSPATH')) { exit; }

/**
 * key => [szerokość pliku, wysokość pliku, gęstość tuszu, fragmenty nazwy pliku dla selektora [src*=…]].
 * Fragmenty pasują do plików wtyczki (logo-vista.webp, client-raxo.webp) i biblioteki mediów (vista.webp, raxo.webp,
 * sfera-1.webp, prisma_dent.webp, piotr_mazur.webp …). Selektory działają tylko wewnątrz pasków logo.
 */
function zp_w94_logos_metrics(): array {
  return [
    'vista'        => [1047, 1001, 0.099, ['vista']],
    'siemianowski' => [1051, 175, 0.162, ['siemianowski']],
    'sfera'        => [730, 1048, 0.303, ['sfera']],
    'raxo'         => [1050, 328, 0.616, ['raxo']],
    'proscarves'   => [906, 1038, 0.328, ['proscarves']],
    'prisma-dent'  => [1048, 646, 0.283, ['prisma']],
    'polerstone'   => [1228, 730, 0.112, ['polerstone']],
    'piotr-mazur'  => [1042, 812, 0.132, ['piotr']],
    'kaminski'     => [1053, 210, 0.143, ['kaminski']],
    'gravia'       => [1044, 628, 0.307, ['gravia']],
    'ap'           => [1052, 392, 0.065, ['/ap.', 'logo-ap.']],
    'eat-tasty'    => [1046, 795, 0.286, ['eat_tasty', 'eat-tasty']],
  ];
}

/** Stałe modelu (px modelu). */
function zp_w94_logos_model(): array {
  return ['A' => 3100.0, 'k' => 0.5, 'd_ref' => 0.2, 'h_max' => 56.0, 'h_min' => 20.0, 'w_max' => 150.0];
}

/** [w, h] jednego logo w px modelu (zaokrąglone do 0,1 px), albo null dla nieznanego klucza. */
function zp_w94_logo_size(string $key): ?array {
  $m = zp_w94_logos_metrics()[$key] ?? null;
  if (!$m || $m[0] <= 0 || $m[1] <= 0 || $m[2] <= 0) { return null; }
  $c = zp_w94_logos_model();
  $ar = $m[0] / $m[1];
  $area = $c['A'] * pow($c['d_ref'] / $m[2], $c['k']);
  $w = sqrt($area * $ar);
  $h = sqrt($area / $ar);
  if ($h > $c['h_max']) { $h = $c['h_max']; $w = $h * $ar; }
  if ($w > $c['w_max']) { $w = $c['w_max']; $h = $w / $ar; }
  if ($h < $c['h_min']) { $h = $c['h_min']; $w = $h * $ar; }
  return [round($w, 1), round($h, 1)];
}

/** Klucz modelu dla adresu pliku logo ('' gdy logo nie ma w tabeli); te same fragmenty co w selektorach [src*=…]. */
function zp_w94_logo_key(string $src): string {
  $file = '/' . strtolower(basename((string) wp_parse_url($src, PHP_URL_PATH)));
  foreach (zp_w94_logos_metrics() as $key => $m) {
    foreach ($m[3] as $frag) {
      if (strpos($file, $frag) !== false) { return $key; }
    }
  }
  return '';
}

/** Selektory korzeni pasków (poza nimi reguły nie działają). */
function zp_w94_logos_roots(): array {
  return [
    'hero' => '#zp-strony-internetowe-katowice .hero-client-logos .hero-client-logos__group',
    'ct'   => '#zp-strony-internetowe-katowice .client-trust__logos',
    'lb'   => '#zp-logo-branding-katowice .logo-marquee .logo-group',
    'home' => '#zpTrustPinned .zpTrustMobileCodepen .logos__track',
  ];
}

/**
 * CSS modelu: wymiary każdego znanego logo jako custom properties (liczby w px modelu) na <img> w paskach danej strony,
 * plus flaga --zp-l94-max: none, która zdejmuje dawne max-width/max-height tylko ze znanych logo. Na stronie głównej
 * dochodzi długość dawnego okresu każdego rzędu (zależy od logotypów z CMS).
 */
function zp_w94_logos_vars_css(array $bars): string {
  $roots = array_values(array_intersect_key(zp_w94_logos_roots(), array_flip($bars)));
  if (!$roots) { return ''; }
  $css = '';
  $known = [];
  foreach (zp_w94_logos_metrics() as $key => $m) {
    $size = zp_w94_logo_size($key);
    if (!$size) { continue; }
    $attr = [];
    foreach ($m[3] as $frag) { $attr[] = '[src*="' . $frag . '"]'; }
    $known = array_merge($known, $attr);
    $sel = [];
    foreach ($roots as $root) { $sel[] = $root . ' img:is(' . implode(',', $attr) . ')'; }
    $css .= implode(',', $sel) . '{--zp-l94-lw:' . $size[0] . ';--zp-l94-lh:' . $size[1] . '}';
  }
  $sel = [];
  foreach ($roots as $root) { $sel[] = $root . ' img:is(' . implode(',', $known) . ')'; }
  $css .= implode(',', $sel) . '{--zp-l94-w:calc(var(--zp-l94-lw) * var(--zp-lu));--zp-l94-h:calc(var(--zp-l94-lh) * var(--zp-lu));--zp-l94-max:none}';
  if (in_array('home', $bars, true)) { $css .= zp_w94_logos_home_rows_css(); }
  return $css;
}

/**
 * Strona główna, „Zaufali nam”: rząd ma 4 powtórzenia logotypów z CMS (templates/trust-logos.php), a jego dawny okres
 * zależał od pudełek z 3.2.93 — na telefonie część logo miała własne szerokości (trust-logos.css, po data-logo-name),
 * od 761 px Polerstone szersze pudełko (front-fixes-245.php). Tu liczymy ten sam okres dla bieżących logotypów, żeby
 * tor zachował długość (prędkość) i żeby przesunięcie −50 % trafiało dokładnie w dwa powtórzenia (dawniej brakowało
 * pół odstępu i rząd przeskakiwał o 14–28 px na końcu każdej pętli).
 */
function zp_w94_logos_home_rows_css(): string {
  if (!function_exists('zp_suite_cms_get')) { return ''; }
  $logos = array_values(array_filter((array) zp_suite_cms_get('logos', []), static function ($l) {
    return is_array($l) && (!isset($l['visible']) || (string) $l['visible'] !== '0');
  }));
  if (!$logos) { return ''; }
  $half = max(1, (int) ceil(count($logos) / 2));
  $rows = [array_slice($logos, 0, $half), array_slice($logos, $half)];
  // Dawne szerokości pudełek na telefonie: [do 390 px, 391–760 px]; pozostałe logo 116 / 126 px.
  $phone = ['vista' => [139, 151], 'sfera' => [139, 151], 'piotr-mazur' => [139, 151], 'proscarves' => [125, 136], 'raxo' => [93, 101], 'polerstone' => [153, 166]];
  $css = '';
  foreach ($rows as $i => $row) {
    if (!$row) { continue; }
    $n = count($row);
    $s390 = 0; $s760 = 0; $pol = 0;
    foreach ($row as $logo) {
      $key = zp_w94_logo_key((string) ($logo['image'] ?? ''));
      $s390 += $phone[$key][0] ?? 116;
      $s760 += $phone[$key][1] ?? 126;
      if ($key === 'polerstone') { $pol++; }
    }
    $css .= '#zpTrustPinned .zpTrustMobileCodepen .carousel--' . ($i === 0 ? 'left' : 'right') . ' .logos__track{'
      . '--zp-l94-n:' . $n . ';--zp-l94-npol:' . $pol . ';--zp-l94-s390:' . $s390 . 'px;--zp-l94-s760:' . $s760 . 'px}';
  }
  return $css;
}

/** Paski na bieżącej stronie: po arkuszach szablonów, które je rysują (PL i EN to te same obiekty stron). */
function zp_w94_logos_bars(): array {
  if (is_admin() || !function_exists('wp_style_is')) { return []; }
  $bars = [];
  if (wp_style_is('zp-suite-strony-internetowe-katowice')) { $bars[] = 'hero'; $bars[] = 'ct'; }
  if (wp_style_is('zp-suite-logo-branding-katowice')) { $bars[] = 'lb'; }
  if (wp_style_is('zp-suite-trust-logos') && (is_front_page() || is_home())) { $bars[] = 'home'; }
  return $bars;
}

add_action('wp_enqueue_scripts', static function () {
  $bars = zp_w94_logos_bars();
  if (!$bars) { return; }
  wp_enqueue_style('zp-w94-logos', ZP_SUITE_URL . 'assets/css/wyglad-3294/logos.css', [], ZP_SUITE_VERSION);
  wp_add_inline_style('zp-w94-logos', zp_w94_logos_vars_css($bars));
}, 1003);

/**
 * Strona główna: pasek „Zaufali nam” jest daleko pod pierwszym ekranem, a jego własny arkusz (trust-logos) ładuje się
 * asynchronicznie (optimizer.php) — ten też, żeby nie dokładać blokującego żądania. Zmienne modelu (inline) są od razu.
 */
add_filter('style_loader_tag', static function ($html, $handle, $href) {
  if ($handle !== 'zp-w94-logos' || is_admin() || !(is_front_page() || is_home())) { return $html; }
  return sprintf(
    "<link rel='stylesheet' id='zp-w94-logos-css' href='%s' media='print' onload=\"this.media='all';this.onload=null\" />\n<noscript><link rel='stylesheet' href='%s' /></noscript>\n",
    esc_url($href),
    esc_url($href)
  );
}, 20, 3);
