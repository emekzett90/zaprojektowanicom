<?php
/**
 * 3.2.95 — obrazy strony głównej (s95img). Filtr zp_s95_html, priorytet 20 (patrz loader.php).
 *
 * Cel: mniej bajtów obrazów, które kończą się przed malowaniem LCP na telefonie, bez widocznej
 * zmiany. Te same grafiki — lżejsze pliki albo późniejsze pobranie grafik ukrytych.
 *
 *  1. Odznaka Meta Verified: oryginały 1373x451 (67,7 KB na live) i 1616x452 (79,6 KB) zastępują
 *     kopie z assets/img/szybkosc-3295/: 685x225 (Lanczos w RGBA z przemnożoną alfą, kolor WebP q90,
 *     alfa bezstratnie, proporcje 3,0444 jak oryginał) i 404x113 (dokładnie poziom mip 2 oryginału,
 *     bezstratnie). Chrome rysuje obraz z najmniejszego poziomu mip >= rozmiaru docelowego; łańcuch
 *     685 (342/171/85) pokrywa się z łańcuchem oryginału (343/171/85), więc ostrość zostaje (kopia
 *     411x135 była przy DPR 1 wyraźnie bardziej miękka). Dokładne poziomy mip białej odznaki
 *     (686x225, 343x112) mają inne proporcje, więc contain rysuje je o piksel szerzej: odpadły.
 *     Krótki <style> w <head> powtarza trzy reguły background-image z meta-verified.css i podzbioru
 *     3233 z dodatkiem :not(#zpS95img) w każdej, więc wygrywa z oryginałami, a między sobą zachowuje
 *     ich kolejność ważności. Bez media query, jak oryginały. Największe użycie: szuflada 150x36
 *     (109,6 px CSS szerokości, x3 = 329 px), mega menu 132x32 (czarna 114,4 px, x3 = 343 px).
 *  2. Zdjęcie zespołu na telefonie (crew-mobile-676.webp, zawsze 225,333x122 px CSS): wariant 399x216
 *     (Lanczos, WebP q90 + bezstratna alfa). srcset na <img> i imagesrcset na preloadzie są identyczne,
 *     z deskryptorami x: 399 = 1.77x (399/225,333 = 1,7707, więc nigdy nie jest powiększany), 676 = 2x.
 *     Chrome 153, Firefox i Safari biorą najmniejszy plik o gęstości >= DPR: DPR 1,25-1,77 (m.in.
 *     Lighthouse 1,75) dostaje 399, DPR > 1,77 (iPhone, większość Androidów) nadal 676; przy DPR 1 src
 *     liczy się jako 1x, więc też zostaje 676 (sprawdzone w Chromium 153: zawsze jedno pobranie, to samo
 *     co z preloadu). Starsza reguła Chromium ze średnią geometryczną (sqrt(1,77*2) = 1,88) też daje 676
 *     od DPR 1,88.
 *     Deskryptory w + sizes nie spełniają obu reguł naraz (przy sizes <= 228 px stara reguła dałaby
 *     399 dla DPR 2). Rozmiar w CSS jest ustalony przez 3233 (!important), więc deklarowana gęstość nie
 *     zmienia wyglądu; krok działa tylko, gdy ta reguła jest na stronie. href preloadu zostaje 676
 *     (przeglądarki bez imagesrcset, np. Safari < 17.2 na iPhone DPR >= 2, i tak wybiorą 676).
 *  3. Logotypy desktopowego nagłówka (.zpNewNav__logo, wewnątrz .zpNewNav__inner, które
 *     #zp-header-responsive-3268 ukrywa do 1360 px): <picture style="display:contents"> ze
 *     <source media="(max-width:1360px)"> i przezroczystym GIF-em (wzorzec zdjęć zespołu z 3233).
 *     Do 1360 px nie są pobierane, powyżej bez zmian. Tylko gdy reguła 3268 jest na stronie.
 *  4. Logo w stopce (<= 760 px, ::before z zp_sygnet_ciemny-scaled.webp, 2560 px, 51,6 KB):
 *     ta sama reguła w tym samym @media z kopią 284x222 (6,8 KB; zapas do DPR 3,8). Tylko gdy
 *     oryginalna reguła jest w arkuszu podpiętym na stronie.
 *  5. trustindex-wordmark-white.webp: identyczne piksele, bez profilu ICC (sRGB) i bloku PSAI
 *     (8 700 -> 5 462 B). Mniejszy rozmiar nie zachowuje proporcji 446:78, więc go nie ma.
 *  6. Popup exit-intent z Code Snippets (#zp-exit-v42, tylko na live): jego dwa zdjęcia miały
 *     loading="eager". Popup jest ukryty przez opacity/visibility, ale leży w układzie strony, więc
 *     samo loading="lazy" go nie wstrzymuje (sprawdzone w Chromium). Dlatego: loading="lazy",
 *     fetchpriority="low", a src/srcset czekają w data-zp-s95-src/srcset do zdarzenia load
 *     (najpóźniej 3 s po parsowaniu) albo do otwarcia popupu (klasa zp-open). Popup uzbraja się
 *     po 4 s (desktop) / 12 s (telefon), więc pobieranie zdjęcia rusza przed pierwszym możliwym
 *     otwarciem (wyjątek: tryb testowy ?zp_exit_test=1 otwiera po 650 ms; wtedy rusza przy otwarciu).
 *
 * Każdy krok ma własne warunki i w razie wątpliwości zostawia swój fragment HTML bez zmian.
 * Robot w hero (LCP na desktopie) świadomie bez zmian: patrz raport 3.2.95.
 * Wyłączniki: opcja zp_s95_img_off = '1' albo ?zp_s95_img=0 (parametr omija też cache HTML).
 */
if (!defined('ABSPATH')) { exit; }

const ZP_S95_IMG_BLANK = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';
const ZP_S95_IMG_CREW_CSS = 'width:225.333333px!important;height:122px!important;min-width:225.333333px!important';

if (!function_exists('zp_s95_img_enabled')) {
  function zp_s95_img_enabled(): bool {
    if (isset($_GET['zp_s95_img']) && (string) $_GET['zp_s95_img'] === '0') { return false; }
    return !function_exists('get_option') || (string) get_option('zp_s95_img_off', '0') !== '1';
  }
}

/** URL pliku z assets/img/szybkosc-3295/ z wersją z treści pliku, albo '' gdy go brak. */
function zp_s95_img_url(string $file): string {
  static $urls = [];
  if (!isset($urls[$file])) {
    $path = ZP_SUITE_PATH . 'assets/img/szybkosc-3295/' . $file;
    $hash = is_readable($path) ? md5_file($path) : false;
    $urls[$file] = $hash ? ZP_SUITE_URL . 'assets/img/szybkosc-3295/' . $file . '?ver=' . substr($hash, 0, 10) : '';
  }
  return $urls[$file];
}

/** Atrybuty start-tagu (pierwsze wystąpienie wygrywa, jak w przeglądarce) i liczba wystąpień nazw. */
function zp_s95_img_attrs(string $tag): array {
  $attrs = [];
  $counts = [];
  if (preg_match_all('~\s([^\s=/>"\']+)(?:\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|([^\s>"\']+)))?~', $tag, $m, PREG_SET_ORDER)) {
    foreach ($m as $a) {
      $name = strtolower($a[1]);
      $counts[$name] = ($counts[$name] ?? 0) + 1;
      if (!array_key_exists($name, $attrs)) {
        $attrs[$name] = html_entity_decode(($a[2] ?? '') . ($a[3] ?? '') . ($a[4] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8');
      }
    }
  }
  return [$attrs, $counts];
}

/**
 * Start-tagi <$name> (poza skryptami/stylami/komentarzami) z dokładnie tą wartością class, zaczynające się
 * w [$from, $to): [start, end+1, tag]. Szuka najpierw nazwy klasy (rzadkiej), potem jej tagu, jak
 * zp_speed_matching_tags; gdy '<' przed trafieniem nie otwiera tego tagu, trafienie przepada (ostrożnie).
 */
function zp_s95_img_tags_by_class(string $html, array $scan, string $name, string $class, int $from, int $to): array {
  $found = [];
  $seen = [];
  $len = strlen($html);
  $pos = $from;
  while (($pos = strpos($html, $class, $pos)) !== false && $pos < $to) {
    $hit = $pos;
    $pos += strlen($class);
    if ($hit === 0 || zp_speed_in_skip($scan['skip'], $hit)) { continue; }
    $lt = strrpos($html, '<', $hit - $len - 1);
    if ($lt === false || $lt < $from || isset($seen[$lt]) || !preg_match('~\G<' . preg_quote($name, '~') . '(?=[\s/>])~i', $html, $mm, 0, $lt)) { continue; }
    $gt = zp_speed_tag_end($html, $lt);
    if ($gt < 0) { return []; }
    if ($gt < $hit) { continue; }
    $seen[$lt] = true;
    $tag = substr($html, $lt, $gt + 1 - $lt);
    [$attrs] = zp_s95_img_attrs($tag);
    if (($attrs['class'] ?? null) === $class) { $found[] = [$lt, $gt + 1, $tag]; }
  }
  return $found;
}

/** Czy w CSS reguła $rule stoi bezpośrednio w bloku $media (np. "@media(max-width:760px){"), bez zagnieżdżenia. */
function zp_s95_img_rule_in_media(string $css, string $rule, string $media): bool {
  $offset = 0;
  while (($at = strpos($css, $rule, $offset)) !== false) {
    $offset = $at + 1;
    $open = strrpos(substr($css, 0, $at), $media);
    if ($open === false) { continue; }
    $inside = substr($css, $open + strlen($media), $at - $open - strlen($media));
    // Głębokość nawiasów między początkiem bloku @media a regułą: 0 = reguła leży wprost w tym bloku.
    if (substr_count($inside, '{') === substr_count($inside, '}') && ($at === 0 || in_array($css[$at - 1], ['{', '}', ';', ' ', "\n"], true))) {
      return true;
    }
  }
  return false;
}

/** Ścieżka lokalna arkusza wtyczki z href, albo ''. */
function zp_s95_img_plugin_css_path(string $href): string {
  $href = html_entity_decode($href, ENT_QUOTES | ENT_HTML5, 'UTF-8');
  $base = ZP_SUITE_URL . 'assets/css/';
  if (strpos($href, $base) !== 0) { return ''; }
  $rel = (string) parse_url(substr($href, strlen(ZP_SUITE_URL)), PHP_URL_PATH);
  if ($rel === '' || strpos($rel, '..') !== false || substr($rel, -4) !== '.css') { return ''; }
  $path = ZP_SUITE_PATH . $rel;
  return is_readable($path) ? $path : '';
}

/** 1. Odznaka Meta Verified: reguły CSS z mniejszymi plikami albo ''. */
function zp_s95_img_meta_css(string $html): string {
  if (strpos($html, 'zpMetaVerified__brand') === false) { return ''; }
  $white = zp_s95_img_url('meta-verified-white-685x225.webp');
  $black = zp_s95_img_url('meta-verified-black-404x113.webp');
  if ($white === '' || $black === '') { return ''; }
  $u = 'https://zaprojektowani.com/wp-content/uploads/2026/10/';
  $r1 = '.zpMetaVerified--theme-dark .zpMetaVerified__brand,.zpMetaVerified--theme-auto .zpMetaVerified__brand{background-image:url("' . $u . 'meta_verified_white.webp")!important}';
  $r2 = '.zpMetaVerified--theme-light .zpMetaVerified__brand{background-image:url("' . $u . 'meta_verified_black.webp")!important}';
  $r3 = '.zpNewNav__mega--portal.is-light-menu .zpMetaVerified--mega .zpMetaVerified__brand{background-image:url("' . $u . 'meta_verified_black.webp")!important}';
  // Te same trzy reguły muszą być w arkuszu, który nadpisujemy (inne obrazy = inna odznaka, nie ruszamy).
  $sheet = ZP_SUITE_PATH . 'assets/css/blocks/meta-verified.css';
  $css = is_readable($sheet) ? (string) file_get_contents($sheet) : '';
  if (strpos($css, $r1) === false || strpos($css, $r2) === false || strpos($css, $r3) === false) { return ''; }
  // Każde wystąpienie oryginalnych plików w HTML musi pochodzić z tych samych reguł (podzbiór 3233).
  $inline = substr_count($html, $u . 'meta_verified_white.webp') + substr_count($html, $u . 'meta_verified_black.webp');
  if ($inline !== substr_count($html, $r1) + substr_count($html, $r2) + substr_count($html, $r3)) { return ''; }
  $n = ':not(#zpS95img)';
  return '.zpMetaVerified--theme-dark' . $n . ' .zpMetaVerified__brand,.zpMetaVerified--theme-auto' . $n . ' .zpMetaVerified__brand{background-image:url("' . $white . '")!important}'
    . '.zpMetaVerified--theme-light' . $n . ' .zpMetaVerified__brand{background-image:url("' . $black . '")!important}'
    . '.zpNewNav__mega--portal.is-light-menu' . $n . ' .zpMetaVerified--mega .zpMetaVerified__brand{background-image:url("' . $black . '")!important}';
}

/** 4. Logo w stopce: reguła CSS z mniejszym plikiem albo ''. */
function zp_s95_img_footer_css(string $html, array $scan): string {
  if (strpos($html, 'zpMegaFooter__logo') === false) { return ''; }
  $logo = zp_s95_img_url('zp-sygnet-ciemny-284x222.webp');
  if ($logo === '') { return ''; }
  $old = 'background-image:url(https://zaprojektowani.com/wp-content/uploads/2026/06/zp_sygnet_ciemny-scaled.webp)!important;background-repeat:no-repeat!important;background-position:left center!important;background-size:contain!important';
  $rules = [
    'html body .zpMegaFooter .zpMegaFooter__logo::before{content:""!important;display:block!important;width:100%!important;height:58px!important;min-height:58px!important;' . $old,
    'html body .zpMegaFooter .zpMegaFooter__logo:before{content:""!important;display:block!important;width:168px!important;height:42px!important;min-height:42px!important;' . $old,
  ];
  $media = '@media(max-width:760px){';
  $found = false;
  foreach ($scan['sheet_links'] as [$start, $end]) {
    [$attrs] = zp_s95_img_attrs(substr($html, $start, $end - $start));
    $path = zp_s95_img_plugin_css_path((string) ($attrs['href'] ?? ''));
    if ($path === '' || strpos($path, '/first-screen-3236/') === false) { continue; }
    $css = (string) file_get_contents($path);
    if (strpos($css, 'zp_sygnet_ciemny-scaled.webp') === false) { continue; }
    foreach ($rules as $rule) {
      if (strpos($css, $rule) !== false) {
        if (!zp_s95_img_rule_in_media($css, $rule, $media)) { return ''; }
        $found = true;
      }
    }
    // Ten plik ma inne użycia tego logo: nie zgadujemy.
    if (substr_count($css, 'zp_sygnet_ciemny-scaled.webp') !== substr_count($css, $old)) { return ''; }
  }
  if (!$found) { return ''; }
  return '@media(max-width:760px){html body .zpMegaFooter:not(#zpS95img) .zpMegaFooter__logo::before{background-image:url("' . $logo . '")!important}}';
}

/** 2. Zdjęcie zespołu: operacje dla <img> i preloadu (obie albo żadna). */
function zp_s95_img_crew_ops(string $html, array $scan): array {
  $old = ZP_SUITE_URL . 'assets/img/hero-team/crew-mobile-676.webp';
  $new = zp_s95_img_url('crew-mobile-399.webp');
  // Gęstości 1.77x/2x zakładają rozmiar 225,333x122 px CSS z 3233 (patrz opis na górze).
  if ($new === '' || strpos($html, 'zh__crewMobile') === false || strpos($html, ZP_S95_IMG_CREW_CSS) === false) { return []; }
  $srcset = $new . ' 1.77x, ' . $old . ' 2x';
  $imgs = zp_s95_img_tags_by_class($html, $scan, 'img', 'zh__crewMobile', $scan['body_open'], strlen($html));
  if (count($imgs) !== 1) { return []; }
  [$at, $end, $tag] = $imgs[0];
  [$attrs, $counts] = zp_s95_img_attrs($tag);
  if (($attrs['src'] ?? '') !== $old || ($counts['src'] ?? 0) !== 1 || isset($attrs['srcset']) || isset($attrs['sizes'])) { return []; }
  // Obraz musi być w <picture class="zh__crewMobilePicture"> z <source> dla (min-width:1101px).
  $before = substr($html, max(0, $at - 400), min(400, $at));
  if (!preg_match('~<picture\s+class="zh__crewMobilePicture">\s*<source\s+media="\(min-width:1101px\)"\s+srcset="data:image/gif;base64,[^"]+">\s*$~', $before)) { return []; }
  $src_pos = strpos($tag, ' src="' . $old . '"');
  if ($src_pos === false) { return []; }
  $src_end = $src_pos + strlen(' src="' . $old . '"');
  $new_tag = substr($tag, 0, $src_end) . ' srcset="' . esc_attr($srcset) . '"' . substr($tag, $src_end);
  $links = [];
  if (preg_match_all('~<link(?=[\s/>])~i', $html, $lm, PREG_OFFSET_CAPTURE)) {
    foreach ($lm[0] as [$token, $lat]) {
      if ($lat >= $scan['head_end'] || zp_speed_in_skip($scan['skip'], $lat)) { continue; }
      $lgt = zp_speed_tag_end($html, $lat);
      if ($lgt < 0) { return []; }
      $ltag = substr($html, $lat, $lgt + 1 - $lat);
      if (strpos($ltag, 'crew-mobile-676.webp') === false) { continue; }
      [$la, $lc] = zp_s95_img_attrs($ltag);
      if (strtolower($la['rel'] ?? '') === 'preload' && strtolower($la['as'] ?? '') === 'image' && ($la['href'] ?? '') === $old) {
        if (isset($la['imagesrcset']) || isset($la['imagesizes']) || ($lc['href'] ?? 0) !== 1 || ($la['media'] ?? '') !== '(max-width:1100px)') { return []; }
        $links[] = [$lat, $lgt + 1, $ltag];
      }
    }
  }
  if (count($links) !== 1) { return []; }
  [$lat, $lend, $ltag] = $links[0];
  $close = substr($ltag, -2) === '/>' ? strlen($ltag) - 2 : strlen($ltag) - 1;
  $new_link = rtrim(substr($ltag, 0, $close)) . ' imagesrcset="' . esc_attr($srcset) . '"' . substr($ltag, $close);
  return [[$lat, $lend, $new_link], [$at, $end, $new_tag]];
}

/** 3. Logotypy desktopowego nagłówka: owinięcie w <picture> pomijany do 1360 px. */
function zp_s95_img_header_logo_ops(string $html, array $scan): array {
  // Dowód ukrycia: reguła 3268 (#zpNewNav x3, !important) chowa .zpNewNav__inner do 1360 px
  // i leży wprost w @media(max-width:1360px){...} (bez zagnieżdżenia w innym bloku).
  if (!preg_match('~<style id="zp-header-responsive-3268">(.*?)</style>~s', $html, $sm)
      || !preg_match('~#zpNewNav#zpNewNav#zpNewNav \.zpNewNav__inner,\s*#zpNewNav#zpNewNav#zpNewNav \.zpNewNav__mobileSearch,\s*#zpNewNav#zpNewNav#zpNewNav \.zpNewNav__drawerSearch\s*\{\s*display:\s*none\s*!important;?\s*\}~', $sm[1], $rm)
      || !zp_s95_img_rule_in_media($sm[1], $rm[0], '@media(max-width:1360px){')) {
    return [];
  }
  $brands = zp_s95_img_tags_by_class($html, $scan, 'a', 'zpNewNav__brand', $scan['body_open'], strlen($html));
  if (count($brands) !== 1) { return []; }
  [$bat, $bopen_end] = $brands[0];
  $bend = zp_speed_nested_end($html, 'a', $bopen_end);
  if ($bend < 0) { return []; }
  // .zpNewNav__brand musi leżeć w .zpNewNav__inner.
  $inner = zp_s95_img_tags_by_class($html, $scan, 'div', 'zpNewNav__inner', $scan['body_open'], $bat);
  if (count($inner) !== 1 || zp_speed_nested_end($html, 'div', $inner[0][1]) < $bend) { return []; }
  $ops = [];
  foreach (['zpNewNav__logo zpNewNav__logo--light', 'zpNewNav__logo zpNewNav__logo--dark'] as $class) {
    $imgs = zp_s95_img_tags_by_class($html, $scan, 'img', $class, $bopen_end, $bend);
    if (count($imgs) !== 1) { return []; }
    [$at, $end, $tag] = $imgs[0];
    [$attrs, $counts] = zp_s95_img_attrs($tag);
    if (($attrs['src'] ?? '') === '' || ($counts['src'] ?? 0) !== 1 || strpos($attrs['src'], 'data:') === 0) { return []; }
    $ops[] = [$at, $end, '<picture style="display:contents"><source media="(max-width:1360px)" srcset="' . ZP_S95_IMG_BLANK . '">' . $tag . '</picture>'];
  }
  // Między <a> a logotypami nic poza białymi znakami (żadnego <picture> z wcześniej).
  $between = substr($html, $bopen_end, $ops[0][0] - $bopen_end) . substr($html, $ops[0][1], $ops[1][0] - $ops[0][1]);
  if (trim($between) !== '') { return []; }
  return $ops;
}

/** 5. Znak Trustindex: ten sam obraz bez profilu ICC i PSAI. */
function zp_s95_img_trustindex_ops(string $html, array $scan): array {
  $old = ZP_SUITE_URL . 'assets/img/trustindex-wordmark-white.webp';
  $new = zp_s95_img_url('trustindex-wordmark-white.webp');
  if ($new === '' || strpos($html, $old) === false) { return []; }
  $ops = [];
  foreach (['zpTrustProof__logo', 'zpReviews24__trustLogo'] as $class) {
    foreach (zp_s95_img_tags_by_class($html, $scan, 'img', $class, $scan['body_open'], strlen($html)) as [$at, $end, $tag]) {
      [$attrs, $counts] = zp_s95_img_attrs($tag);
      if (($attrs['src'] ?? '') !== $old || ($counts['src'] ?? 0) !== 1 || isset($attrs['srcset'])) { continue; }
      $ops[] = [$at, $end, str_replace(' src="' . $old . '"', ' src="' . esc_attr($new) . '"', $tag)];
    }
  }
  foreach ($ops as $op) { if (strpos($op[2], $new) === false) { return []; } }
  return $ops;
}

/** 6. Zdjęcia ukrytego popupu exit-intent: [operacje na <img>, pozycja na skrypt] albo []. */
function zp_s95_img_exit_ops(string $html, array $scan): array {
  if (strpos($html, 'zp-exit-v42') === false) { return []; }
  $roots = zp_speed_matching_tags($html, $scan, '#zp-exit-v42', $scan['body_open'], strlen($html));
  if (count($roots) !== 1 || $roots[0][2] !== 'div') { return []; }
  $open_end = $roots[0][1] + 1;
  $end = zp_speed_nested_end($html, 'div', $open_end);
  if ($end < 0) { return []; }
  [$root_attrs] = zp_s95_img_attrs(substr($html, $roots[0][0], $open_end - $roots[0][0]));
  if (($root_attrs['aria-hidden'] ?? '') !== 'true') { return []; }
  $ops = [];
  foreach (['zp-exit-v42__mobile-person', 'zp-exit-v42__person'] as $class) {
    $imgs = zp_s95_img_tags_by_class($html, $scan, 'img', $class, $open_end, $end);
    if (count($imgs) !== 1) { return []; }
    [$at, $iend, $tag] = $imgs[0];
    [$a, $c] = zp_s95_img_attrs($tag);
    foreach (['src', 'srcset', 'loading'] as $name) { if (($c[$name] ?? 0) !== 1) { return []; } }
    if (strtolower($a['loading']) !== 'eager' || isset($a['fetchpriority']) || isset($a['data-zp-s95-src'])
        || !preg_match('~^https://zaprojektowani\.com/wp-content/uploads/[^"\s<>]+\.(?:webp|png|jpe?g)$~', $a['src'])
        || strpos($a['srcset'], '"') !== false || strpos($a['srcset'], '<') !== false) { return []; }
    $new = preg_replace(
      ['~(?<=\s)loading\s*=\s*(["\']?)eager\1~i', '~(?<=\s)srcset(?=\s*=)~i', '~(?<=\s)src(?=\s*=)~i'],
      ['loading="lazy" fetchpriority="low"', 'data-zp-s95-srcset', 'data-zp-s95-src'],
      $tag, 1, $count
    );
    if (!is_string($new) || $count !== 3) { return []; }
    [$na, $nc] = zp_s95_img_attrs($new);
    if (isset($na['src']) || isset($na['srcset']) || ($na['data-zp-s95-src'] ?? '') !== $a['src'] || ($na['data-zp-s95-srcset'] ?? '') !== $a['srcset']
        || ($na['loading'] ?? '') !== 'lazy' || ($na['fetchpriority'] ?? '') !== 'low') { return []; }
    $ops[] = [$at, $iend, $new];
  }
  $js = '<script id="zp-s95-img-exit">(function(d,w){var m=d.getElementById("zp-exit-v42"),o,t,done=0;if(!m)return;'
    . 'function go(){if(done)return;done=1;try{if(o)o.disconnect();}catch(e){}if(t)w.clearTimeout(t);'
    . 'var a=m.querySelectorAll("img[data-zp-s95-src]");for(var i=0;i<a.length;i++){var g=a[i],s=g.getAttribute("data-zp-s95-srcset");'
    . 'if(s!==null){g.setAttribute("srcset",s);g.removeAttribute("data-zp-s95-srcset");}g.setAttribute("src",g.getAttribute("data-zp-s95-src"));g.removeAttribute("data-zp-s95-src");}}'
    . 'function chk(){if(m.classList.contains("zp-open")||m.getAttribute("aria-hidden")==="false")go();}'
    . 'try{o=new MutationObserver(chk);o.observe(m,{attributes:true,attributeFilter:["class","aria-hidden"]});}catch(e){}chk();'
    . 'if(d.readyState==="complete")go();else{w.addEventListener("load",go);t=w.setTimeout(go,3000);}})(document,window);</script>';
  $ops[] = [$end, $end, $js];
  return $ops;
}

function zp_s95_img_html($html) {
  if (!is_string($html) || $html === '' || !zp_s95_img_enabled()) { return $html; }
  // Już przetworzone (np. drugi bufor): nie dublujemy <style>/<script>.
  if (strpos($html, 'id="zp-s95-img') !== false) { return $html; }
  foreach (['zp_speed_scan', 'zp_speed_tag_end', 'zp_speed_in_skip', 'zp_speed_nested_end', 'zp_speed_matching_tags'] as $fn) {
    if (!function_exists($fn)) { return $html; }
  }
  try {
    if (function_exists('headers_list')) {
      foreach (headers_list() as $header) {
        if (stripos($header, 'Content-Security-Policy:') === 0) { return $html; }
      }
    }
    if (preg_match('~<meta[^>]+http-equiv\s*=\s*["\']?content-security-policy~i', $html)) { return $html; }
    $scan = zp_speed_scan($html);
    if (!$scan || $scan['head_end'] < 0 || $scan['body_open'] < 0) { return $html; }
    $ops = [];
    $steps = [];
    $css = '';
    $part = zp_s95_img_meta_css($html);
    if ($part !== '') { $css .= $part; $steps[] = 'meta'; }
    $part = zp_s95_img_footer_css($html, $scan);
    if ($part !== '') { $css .= $part; $steps[] = 'footer'; }
    foreach (['crew' => 'zp_s95_img_crew_ops', 'logo' => 'zp_s95_img_header_logo_ops', 'trustindex' => 'zp_s95_img_trustindex_ops', 'exit' => 'zp_s95_img_exit_ops'] as $key => $fn) {
      $more = $fn($html, $scan);
      if ($more) { $ops = array_merge($ops, $more); $steps[] = $key; }
    }
    if ($css !== '') {
      $ops[] = [$scan['head_end'], $scan['head_end'], '<style id="zp-s95-img">' . $css . '</style>'];
    }
    if (!$ops) { return $html; }
    usort($ops, static function ($a, $b) { return $a[0] <=> $b[0] ?: $a[1] <=> $b[1]; });
    // Jeden przebieg; operacje nie mogą na siebie zachodzić (wtedy nic nie zmieniamy).
    $pieces = [];
    $prev = 0;
    foreach ($ops as [$start, $end, $replacement]) {
      if ($start < $prev || $end < $start || $end > strlen($html)) { return $html; }
      $pieces[] = substr($html, $prev, $start - $prev);
      $pieces[] = $replacement;
      $prev = $end;
    }
    $pieces[] = substr($html, $prev);
    $html = implode('', $pieces);
    if (!headers_sent()) { header('X-ZP-S95-Img: ' . implode(',', $steps)); }
    return $html;
  } catch (\Throwable $error) {
    return $html;
  }
}

add_filter('zp_s95_html', 'zp_s95_img_html', 20);
