<?php
/**
 * 3.2.96 — wcześniejsze pobranie 4 arkuszy, które 3.2.95 zostawia na telefonach (filtr zp_s95_html, priorytet 55).
 *
 * Problem (Lighthouse/PageSpeed, telefon): header.min.css, mobile-sticky-cta, trust-logos i services-path
 * (zp_s95_css_keep()) mają na ekranach ≤1100 px media, które nie pasuje ('(min-width:1101px)…' albo 'print'),
 * więc Chrome pobiera je z najniższym priorytetem (VeryLow). Symulacja Lighthouse (Lantern) startuje każde
 * pobranie według chwili odkrycia + kary za priorytet (VeryLow +2 s, Low +1 s, High +0,25 s), więc te arkusze
 * stają w kolejce za każdym leniwym logotypem klienta (Low), który skończył się przed LCP, a ich koniec wyznacza
 * LCP. Przy tym samym HTML wynik skakał między 3,9 a 5,0 s; z tym modułem 3,3–3,5 s (scratchpad s96/e1).
 *
 * Co robi (tylko kopia 'm' strony głównej PL/EN i tylko tam, gdzie moduł CSS 3.2.95 zadziałał, czyli bez CSP):
 * dla każdego z tych arkuszy, który jest w <head> jako asynchroniczny link (media niepasujące na telefonie
 * i onload), dodaje <link rel="preload" as="style"> z tym samym adresem, media="(max-width:1100px)"
 * i fetchpriority="low" (w Chrome daje to priorytet High), zaraz za preloadem obrazu bohatera.
 * Sam link arkusza zostaje bez zmian (ten sam onload, to samo media, to samo miejsce w kaskadzie);
 * przeglądarka bierze już pobraną odpowiedź, więc każdy plik pobiera się raz. Na ekranach ≥1101 px
 * preload się nie pobiera (media), więc tam nic się nie zmienia; kopia 'd' (komputery) też nie.
 *
 * Wyłączniki: opcja zp_s96_off = '1' albo ?zp_s96=0 (jedna odsłona, omija też cache). Bez 3.2.95
 * (zp_s95_off / ?zp_s95=0 / zp_s95_css_off) moduł nic nie robi.
 */
if (!defined('ABSPATH')) { exit; }

/** Czy link to jeden z zostawionych arkuszy, dziś niepasujący na telefonie i włączany przez onload. */
function zp_s96_preloadable(array $a, array $keep): bool {
  if (strtolower(trim($a['rel'] ?? '')) !== 'stylesheet' || trim($a['href'] ?? '') === '' || trim($a['onload'] ?? '') === '') { return false; }
  // Atrybuty, przy których preload mógłby nie zostać użyty (inny tryb CORS, polityka) albo link działa inaczej.
  foreach (['disabled', 'integrity', 'blocking', 'title', 'crossorigin', 'nonce', 'referrerpolicy'] as $k) { if (isset($a[$k])) { return false; } }
  $media = trim($a['media'] ?? '');
  $narrow_off = $media === 'print' || $media === '(min-width:1101px)' || strpos($media, '(min-width:1101px) and ') === 0;
  if (!$narrow_off || strpos($media, ',') !== false) { return false; }
  $href = (string) $a['href'];
  $u = wp_parse_url($href); $home = wp_parse_url(home_url('/'));
  if (!is_array($u) || !is_array($home) || isset($u['user']) || isset($u['pass']) || isset($u['fragment'])
      || strcasecmp((string) ($u['host'] ?? ''), (string) ($home['host'] ?? '')) !== 0
      || strpos((string) ($u['path'] ?? ''), '/wp-content/') !== 0) { return false; }
  $path = (string) $u['path'];
  foreach ($keep as $k) { if ($k !== '' && substr($path, -strlen($k)) === $k) { return true; } }
  return false;
}

function zp_s96_preload_html($html) {
  if (!is_string($html) || $html === '' || strpos($html, 'data-zp-s96') !== false) { return $html; }
  if ((string) get_option('zp_s96_off', '0') === '1' || (isset($_GET['zp_s96']) && (string) $_GET['zp_s96'] === '0')) { return $html; }
  // Znacznik modułu CSS 3.2.95: kopia 'm', strona główna, brak CSP, arkusze przytrzymane.
  if (strpos($html, 'id="zp-s95-css-3295"') === false) { return $html; }
  foreach (['zp_speed_scan', 'zp_speed_in_skip', 'zp_speed_tag_end', 'zp_s95_css_attrs', 'zp_s95_css_keep', 'zp_s95_css_device'] as $fn) { if (!function_exists($fn)) { return $html; } }
  if (zp_s95_css_device($_SERVER) !== 'm') { return $html; }
  try {
    $scan = zp_speed_scan($html);
    if (!$scan || $scan['head_end'] < 0) { return $html; }
    // Linki w <head> poza komentarzami, <noscript> i <template>: preloady już obecne i miejsce wstawienia.
    $after_hero = -1; $narrow_preloaded = [];
    if (preg_match_all('~<link\b~i', substr($html, 0, $scan['head_end']), $found, PREG_OFFSET_CAPTURE)) {
      foreach ($found[0] as [$token, $at]) {
        if (zp_speed_in_skip($scan['skip'], $at)) { continue; }
        $gt = zp_speed_tag_end($html, $at); if ($gt < 0) { return $html; }
        $a = zp_s95_css_attrs(substr($html, $at, $gt + 1 - $at));
        if (strtolower(trim($a['rel'] ?? '')) !== 'preload') { continue; }
        $as = strtolower(trim($a['as'] ?? ''));
        if ($as === 'style' && strpos(trim($a['media'] ?? ''), '(min-width:1101px)') !== 0) { $narrow_preloaded[(string) ($a['href'] ?? '')] = true; }
        if ($after_hero < 0 && $as === 'image' && strtolower(trim($a['fetchpriority'] ?? '')) === 'high') { $after_hero = $gt + 1; }
      }
    }
    $keep = zp_s95_css_keep();
    $tags = ''; $seen = []; $first = -1;
    foreach ($scan['sheet_links'] as [$start, $end]) {
      if ($start >= $scan['head_end']) { continue; }
      $a = zp_s95_css_attrs(substr($html, $start, $end - $start));
      if (!zp_s96_preloadable($a, $keep)) { continue; }
      $href = (string) $a['href'];
      if (isset($seen[$href]) || isset($narrow_preloaded[$href])) { continue; }
      $seen[$href] = true;
      if ($first < 0) { $first = $start; }
      $tags .= '<link rel="preload" as="style" href="' . htmlspecialchars($href, ENT_QUOTES, 'UTF-8') . '" media="(max-width:1100px)" fetchpriority="low" data-zp-s96="3296">';
    }
    if ($tags === '') { return $html; }
    // Zaraz za preloadem obrazu bohatera; bez niego tuż przed pierwszym z tych linków.
    $at = ($after_hero >= 0 && $after_hero <= $first) ? $after_hero : $first;
    return substr($html, 0, $at) . $tags . substr($html, $at);
  } catch (\Throwable $error) {
    return $html;
  }
}
add_filter('zp_s95_html', 'zp_s96_preload_html', 55);
