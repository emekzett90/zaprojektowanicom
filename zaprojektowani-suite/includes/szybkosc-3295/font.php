<?php
/**
 * 3.2.95 — fonty strony głównej (filtr zp_s95_html, priorytet 40): kursywa Jakarty (400 italic
 * i 700 italic) na wąskich ekranach (≤1100 px) dopiero po zwolnieniu strony. Bez widocznej zmiany.
 *
 * Pomiar (CDP CSS.getPlatformFontsForNode na każdym węźle tekstu, 390/412/600/760/820/900/1024/1100 px,
 * PL i EN, menu mobilne otwarte ~300 ms po pierwszym malowaniu i po load, rozwinięte akordeony):
 * kursywy używają tylko podpis zdjęcia zespołu w stopce (figcaption.zpFootCta__team, ≥16 900 px
 * od góry), etykieta .zpLaptopShowcase__sitePill (≥7 400 px) i <em> w h2#zpShowcaseServicesTitle
 * (≥9 200 px). Nic w pierwszym ekranie, w menu mobilnym ani w pasku #zpMobileStickyCta. Pozostałe
 * kroje (400/500/600/700/800) mają użytkowników w pierwszym ekranie, w menu albo tuż pod nim — bez zmian.
 *
 * Bazowo obie reguły @font-face kursywy stoją w krytycznym <style data-zp-critical="3233"
 * media="(max-width:1100px)"> w <head>, więc telefon pobiera oba pliki (na produkcji 19 008
 * + 19 740 B, transfer 19 378 + 20 110 B) przy pierwszym układzie strony, razem z krojami
 * pierwszego ekranu, czyli zwykle przed LCP. Tu te same dwie reguły (bajt w bajt) przechodzą do
 * <style media="not all" data-zp-s95-font="(max-width:1100px)"> zaraz za tym stylem. Mały skrypt
 * na początku <body> (za wczesnym skryptem menu mobilnego 532) przywraca im media przy pierwszym z:
 * zdarzenie dokumentu 'zp:s95-release' (albo już ustawione window.zpS95Released), window load,
 * beforeprint, kotwica w adresie, pierwsze działanie użytkownika (wheel, touchstart, pointerdown,
 * keydown), przewinięcie o pół ekranu (także sprzed uruchomienia skryptu — sprawdzane w pierwszej
 * klatce). Pierwsza interakcja i tak kończy pomiar LCP, więc wcześniejsze pobranie wtedy nic nie
 * kosztuje, a plik zdąży przyjść, zanim przewijanie dojdzie do tekstu z kursywą (≥ 7 400 px).
 * Szerokie ekrany: styl 3233 i tak ich nie dotyczy (media), więc nic się tam nie zmienia.
 * Bez JS: <noscript> z tymi samymi regułami.
 * zp-global.min.css deklaruje te same kroje pod tymi samymi adresami: dopóki telefon podpina go
 * przed zwolnieniem strony (bez modułu css), kursywa przychodzi razem z nim. Po jego podpięciu
 * i po zwolnieniu zestaw krojów jest taki sam jak bazowy.
 *
 * Wyłączniki: opcja zp_s95_font_off = '1', ?zp_s95_font=0 (jedna odsłona, omija cache HTML),
 * oraz wspólne z loader.php (zp_s95_off, ?zp_s95=0).
 */
if (!defined('ABSPATH')) { exit; }

if (!function_exists('zp_s95_font_html')) {
  function zp_s95_font_html($html) {
    if (!is_string($html) || $html === '') { return $html; }
    if (isset($_GET['zp_s95_font']) && (string) $_GET['zp_s95_font'] === '0') { return $html; }
    if ((string) get_option('zp_s95_font_off', '0') === '1') { return $html; }
    if (strpos($html, 'data-zp-s95-font') !== false || !function_exists('zp_speed_scan') || !function_exists('zp_speed_in_skip')) { return $html; }
    try {
      // Bez polityki CSP: skrypt zwalniający jest inline (jak reszta strony głównej).
      foreach (headers_list() as $header) {
        if (stripos($header, 'Content-Security-Policy:') === 0) { return $html; }
      }
      if (stripos($html, 'content-security-policy') !== false) { return $html; }

      $open = '<style data-zp-critical="3233" media="(max-width:1100px)">';
      $face = '@font-face{font-family:"Plus Jakarta Sans Local";';
      $src = 'src:url("/wp-content/web-font/jakarta/plus-jakarta-sans-v12-latin_latin-ext-';
      $rules = [
        $face . $src . 'italic.woff2") format("woff2");font-weight:400;font-style:italic;font-display:swap}',
        $face . $src . '700italic.woff2") format("woff2");font-weight:700;font-style:italic;font-display:swap}',
      ];
      foreach ($rules as $rule) {
        if (substr_count($html, $rule) !== 1) { return $html; }
      }

      $scan = zp_speed_scan($html);
      if (!$scan || $scan['head_end'] < 0) { return $html; }
      $node = null;
      foreach ($scan['styles'] as $style) {
        [$start, $end, $in_body] = $style;
        if ($in_body || $start >= $scan['head_end']) { continue; }
        $at = strpos($html, $rules[0], $start);
        if ($at !== false && $at < $end) { $node = [$start, $end]; break; }
      }
      if ($node === null) { return $html; }
      [$start, $end] = $node;
      $css = substr($html, $start, $end - $start);
      if (strpos($css, $open . $face) !== 0 || substr($css, -8) !== '</style>') { return $html; }
      foreach ($rules as $rule) {
        if (substr_count($css, $rule) !== 1) { return $html; }
      }
      $kept = str_replace($rules, '', $css);
      if (strpos($kept, $face) === false) { return $html; }

      // Skrypt zwalniający stoi zaraz za istniejącym wczesnym skryptem <body> (menu mobilne 532). Tamten i tak
      // czeka na arkusze blokujące skrypty, więc nie powstaje nowy punkt blokowania parsera, a wyzwalacze działają,
      // zanim parser dojdzie do treści z kursywą. Bez niego: przed </body>.
      $insert_at = -1;
      $anchor = '<script id="zp-suite-532-early-mobile-drawer">';
      if (substr_count($html, $anchor) === 1) {
        $a = strpos($html, $anchor);
        if ($a > $scan['body_open']) {
          foreach ($scan['skip'] as $range) {
            if ($range[0] === $a) { $insert_at = $range[1]; break; }
            if ($range[0] > $a) { break; }
          }
        }
      }
      if ($insert_at < 0) {
        $body_end = strripos($html, '</body>');
        if ($body_end === false || $body_end < $scan['body_open'] || zp_speed_in_skip($scan['skip'], $body_end)) { return $html; }
        $insert_at = $body_end;
      }
      if ($insert_at < $end) { return $html; }

      $held = '<style media="not all" data-zp-s95-font="(max-width:1100px)">' . implode('', $rules) . '</style>'
        . '<noscript><style media="(max-width:1100px)">' . implode('', $rules) . '</style></noscript>';
      // Wyzwalacze: zwolnienie strony, load, druk, kotwica, pierwsze działanie użytkownika (kółko, dotyk, wskaźnik,
      // klawisz) i przewinięcie o pół ekranu. Pierwsza interakcja i tak kończy pomiar LCP, a Lighthouse jej nie robi.
      $js = '<script id="zp-s95-font">(function(w,d){var done=0,h=d.documentElement,o={passive:true,capture:true},'
        . 'ev=["wheel","touchstart","pointerdown","keydown"],i;'
        . 'function go(){if(done)return;done=1;try{var s=d.querySelectorAll("style[data-zp-s95-font]"),k;'
        . 'for(k=0;k<s.length;k++)s[k].media=s[k].getAttribute("data-zp-s95-font")||"all";}catch(e){}'
        . 'try{w.removeEventListener("scroll",sc,o);for(k=0;k<ev.length;k++)w.removeEventListener(ev[k],go,o);}catch(e){}}'
        . 'function sc(){if(done)return;try{if((w.pageYOffset||h.scrollTop||0)>(w.innerHeight||h.clientHeight||800)/2)go();}catch(e){go();}}'
        . 'if(w.zpS95Released||d.readyState==="complete"||w.location.hash){go();return;}'
        . 'd.addEventListener("zp:s95-release",go);w.addEventListener("zp:s95-release",go);'
        . 'w.addEventListener("load",go);w.addEventListener("beforeprint",go);w.addEventListener("hashchange",go);'
        . 'w.addEventListener("scroll",sc,o);for(i=0;i<ev.length;i++)w.addEventListener(ev[i],go,o);'
        . 'if(w.requestAnimationFrame)w.requestAnimationFrame(sc);})(window,document);</script>';

      // Najpierw późniejsza wstawka (w <body>), żeby przesunięcia wcześniejszej zostały ważne.
      $out = substr($html, 0, $insert_at) . $js . substr($html, $insert_at);
      return substr($out, 0, $start) . $kept . $held . substr($out, $end);
    } catch (\Throwable $e) {
      return $html;
    }
  }
  add_filter('zp_s95_html', 'zp_s95_font_html', 40);
}
