<?php
/**
 * 3.2.95 (s95img): test kroku 6 z includes/szybkosc-3295/img.php (zdjęcia ukrytego popupu
 * exit-intent #zp-exit-v42 z Code Snippets, który jest tylko na live) i całego filtra zp_s95_img_html.
 *
 * Uruchomienie, tylko z wiersza poleceń, z katalogu wtyczki:
 *   php docs/3.2.95/test-s95-img.php [zapisany-html-strony-glownej.html]
 * Z argumentem sprawdza też całą stronę główną z doklejonym popupem (wszystkie kroki naraz); HTML musi
 * być nieprzetworzony, np. zapisany z /?zp_s95_img=0 (przetworzony ma już <style id="zp-s95-img">).
 * Próbka popupu: znacznik z live (14.10.2026), nonce zamaskowany. Kod wyjścia 0 = wszystko OK.
 */
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }

define('ABSPATH', __DIR__ . '/');
define('ZP_SUITE_URL', 'https://zaprojektowani.com/wp-content/plugins/zaprojektowani-suite/');
define('ZP_SUITE_PATH', dirname(__DIR__, 2) . '/');
$GLOBALS['zp_test_options'] = [];
function add_filter(...$args) { return true; }
function add_action(...$args) { return true; }
function is_admin() { return true; } // speed-293.php nie startuje wtedy własnego bufora
function wp_doing_ajax() { return false; }
function wp_doing_cron() { return false; }
function get_option($name, $default = false) { return $GLOBALS['zp_test_options'][$name] ?? $default; }
function esc_attr($text) { return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8'); }
require ZP_SUITE_PATH . 'includes/speed-293.php';
require ZP_SUITE_PATH . 'includes/szybkosc-3295/img.php';

$popup = <<<'HTML'
<div id="zp-exit-v42" aria-hidden="true" data-ajax-url="https://zaprojektowani.com/wp-admin/admin-ajax.php"
> <div class="zp-exit-v42__backdrop" data-zp-close ></div> <section class="zp-exit-v42__dialog" role="dialog" aria-modal="true" aria-labelledby="zp-exit-v42-title" aria-describedby="zp-exit-v42-description" > <button type="button" class="zp-exit-v42__close" data-zp-close aria-label="Zamknij" ></button> <div class="zp-exit-v42__mobile-hero" aria-hidden="true" > <div class="zp-exit-v42__mobile-hour"> 1H </div> <div class="zp-exit-v42__mobile-label"> <span></span> SZYBKI KONTAKT </div> <div class="zp-exit-v42__mobile-copy"> Zostaw kontakt.<br> Resztę ogarniemy razem. </div> <img style="--zp-ar:1201/1860" sizes="(max-width: 640px) 92vw, (max-width: 1180px) 50vw, 33vw" srcset="https://zaprojektowani.com/wp-content/uploads/2026/06/stanislaw_pointing.webp 1201w, https://zaprojektowani.com/wp-content/uploads/2026/06/stanislaw_pointing-194x300.webp 194w, https://zaprojektowani.com/wp-content/uploads/2026/06/stanislaw_pointing-661x1024.webp 661w, https://zaprojektowani.com/wp-content/uploads/2026/06/stanislaw_pointing-768x1189.webp 768w, https://zaprojektowani.com/wp-content/uploads/2026/06/stanislaw_pointing-992x1536.webp 992w" height="1860" width="1201" class="zp-exit-v42__mobile-person" src="https://zaprojektowani.com/wp-content/uploads/2026/06/stanislaw_pointing.webp" alt="Stanisław z zespołu Zaprojektowani" aria-hidden="true" loading="eager" decoding="async" > </div> <div class="zp-exit-v42__content"> <div class="zp-exit-v42__eyebrow"> <span class="zp-exit-v42__dot" aria-hidden="true" ></span> Zanim zamkniesz kartę </div> <h2 id="zp-exit-v42-title" class="zp-exit-v42__title" > Zostaw numer.<br> <span class="zp-exit-v42__gradient"> Oddzwonimy nawet w ciągu 1h. </span> </h2> <p id="zp-exit-v42-description" class="zp-exit-v42__lead" > Bez długiego briefu i bez zobowiązań. Powiedz nam tylko, czego potrzebujesz, a podczas krótkiej rozmowy ustalimy konkretny kierunek działania. <strong> Do 1h w godzinach pracy. </strong> </p> <div class="zp-exit-v42__chips" aria-label="Wybierz interesującą usługę" > <button type="button" class="zp-exit-v42__chip" data-service="Strona internetowa" aria-pressed="false" > Strona WWW </button> <button type="button" class="zp-exit-v42__chip" data-service="Sklep internetowy" aria-pressed="false" > Sklep </button> <button type="button" class="zp-exit-v42__chip" data-service="Logo i branding" aria-pressed="false" > Logo / branding </button> <button type="button" class="zp-exit-v42__chip" data-service="Kampanie reklamowe" aria-pressed="false" > Reklamy </button> <button type="button" class="zp-exit-v42__chip" data-service="Inny temat" aria-pressed="false" > Inny temat </button> </div> <form id="zp-exit-v42-form" class="zp-exit-v42__form" novalidate > <input type="hidden" name="action" value="zp_exit_submit_v42" > <input type="hidden" name="nonce" value="0000000000" > <input type="hidden" name="service" id="zp-exit-v42-service" value="" > <input type="hidden" name="page_url" id="zp-exit-v42-url" value="" > <input type="hidden" name="referrer" id="zp-exit-v42-referrer" value="" > <input type="hidden" name="started" id="zp-exit-v42-started" value="" > <label class="zp-exit-v42__hp" aria-hidden="true" > Strona <input type="text" name="website" tabindex="-1" autocomplete="off" > </label> <div class="zp-exit-v42__fields"> <div class="zp-exit-v42__field"> <input type="text" class="zp-exit-v42__input" name="name" id="zp-exit-v42-name" autocomplete="given-name" placeholder="Twoje imię" required > </div> <div class="zp-exit-v42__field"> <input type="tel" class="zp-exit-v42__input" name="phone" id="zp-exit-v42-phone" inputmode="tel" autocomplete="tel" placeholder="Numer telefonu" required > </div> </div> <button type="submit" class="zp-exit-v42__submit" > <span> Oddzwońcie do mnie </span> <svg viewBox="0 0 24 24" fill="none" aria-hidden="true" > <path d="M5 12H19M14 7L19 12L14 17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /> </svg> </button> <div id="zp-exit-v42-error" class="zp-exit-v42__error" role="alert" ></div> </form> <div id="zp-exit-v42-success" class="zp-exit-v42__success" role="status" aria-live="polite" > <strong> Mamy Twój numer ✓ </strong> <p> Dzięki! Odezwie się do Ciebie ktoś z naszego zespołu. </p> </div> <div class="zp-exit-v42__meta"> <span> Wysyłając formularz, prosisz nas o kontakt w sprawie projektu. </span> <a href="https://zaprojektowani.com/polityka-prywatnosci/" target="_blank" rel="noopener" > Polityka prywatności </a> </div> <div class="zp-exit-v42__proof"> <span> <i>✓</i> bezpłatna konsultacja </span> <span> <i>✓</i> konkretny zakres </span> <span> <i>✓</i> bez zobowiązań </span> </div> </div> <aside class="zp-exit-v42__visual" aria-hidden="true" > <div class="zp-exit-v42__big-hour"> 1H </div> <img style="--zp-ar:1201/1860" sizes="(max-width: 640px) 92vw, (max-width: 1180px) 50vw, 33vw" srcset="https://zaprojektowani.com/wp-content/uploads/2026/06/stanislaw_pointing.webp 1201w, https://zaprojektowani.com/wp-content/uploads/2026/06/stanislaw_pointing-194x300.webp 194w, https://zaprojektowani.com/wp-content/uploads/2026/06/stanislaw_pointing-661x1024.webp 661w, https://zaprojektowani.com/wp-content/uploads/2026/06/stanislaw_pointing-768x1189.webp 768w, https://zaprojektowani.com/wp-content/uploads/2026/06/stanislaw_pointing-992x1536.webp 992w" height="1860" width="1201" class="zp-exit-v42__person" src="https://zaprojektowani.com/wp-content/uploads/2026/06/stanislaw_pointing.webp" alt="Stanisław z zespołu Zaprojektowani" aria-hidden="true" loading="eager" decoding="async" > <div class="zp-exit-v42__visual-card"> <div> <strong> Ty zostawiasz numer. </strong> <small> My wracamy z konkretem. </small> </div> <span class="zp-exit-v42__badge"> SZYBKI KONTAKT </span> </div> </aside> </section> </div>
HTML;

function zp_test_page(string $popup, string $head = ''): string {
  return '<!doctype html><html lang="pl"><head><meta charset="utf-8"><title>Test</title>' . $head . '</head>'
    . '<body class="home"><main><h1>Strona</h1><p>Treść strony.</p></main>' . "\n" . $popup . "\n"
    . '<script id="zp-exit-v42-js">(function(){var m=document.getElementById("zp-exit-v42");})();</script></body></html>';
}

$failed = 0;
function zp_test(string $name, bool $ok, string $detail = ''): void {
  global $failed;
  if (!$ok) { $failed++; }
  echo ($ok ? 'ok   ' : 'FAIL ') . $name . ($ok || $detail === '' ? '' : ' — ' . $detail) . "\n";
}

/** Cofa zmiany kroku 6: musi wyjść dokładnie wejście. */
function zp_test_undo(string $html): string {
  $html = (string) preg_replace('~<script id="zp-s95-img-exit">.*?</script>~s', '', $html);
  return str_replace(['loading="lazy" fetchpriority="low"', ' data-zp-s95-srcset=', ' data-zp-s95-src='], ['loading="eager"', ' srcset=', ' src='], $html);
}

/** Atrybuty grafik popupu w kolejności z HTML. */
function zp_test_popup_imgs(string $html): array {
  $start = strpos($html, '<div id="zp-exit-v42"');
  $end = strpos($html, '</section> </div>', (int) $start);
  preg_match_all('~<img\s[^>]*>~', substr($html, (int) $start, (int) $end - (int) $start), $m);
  return array_map(static function ($tag) { return zp_s95_img_attrs($tag); }, $m[0]);
}

/* 1. Realistyczny popup. */
$page = zp_test_page($popup);
$out = zp_s95_img_html($page);
zp_test('popup: HTML zmieniony', $out !== $page);
zp_test('popup: cofnięcie zmian daje dokładnie wejście', zp_test_undo($out) === $page);
zp_test('popup: jeden skrypt, tuż za zamknięciem popupu', substr_count($out, '<script id="zp-s95-img-exit">') === 1
  && strpos($out, '</aside> </section> </div><script id="zp-s95-img-exit">') !== false);
$before = zp_test_popup_imgs($page);
$after = zp_test_popup_imgs($out);
zp_test('popup: dwie grafiki', count($before) === 2 && count($after) === 2);
foreach ($after as $i => [$a, $c]) {
  [$b] = $before[$i];
  $label = 'popup: ' . ($b['class'] ?? '?');
  zp_test($label . ' bez src i srcset', !isset($a['src']) && !isset($a['srcset']));
  zp_test($label . ' data-zp-s95-src = dawny src', ($a['data-zp-s95-src'] ?? null) === $b['src']);
  zp_test($label . ' data-zp-s95-srcset = dawny srcset', ($a['data-zp-s95-srcset'] ?? null) === $b['srcset']);
  zp_test($label . ' loading=lazy, fetchpriority=low', ($a['loading'] ?? '') === 'lazy' && ($a['fetchpriority'] ?? '') === 'low');
  $rest_a = array_diff_key($a, array_flip(['loading', 'fetchpriority', 'data-zp-s95-src', 'data-zp-s95-srcset']));
  $rest_b = array_diff_key($b, array_flip(['loading', 'src', 'srcset']));
  zp_test($label . ' pozostałe atrybuty bez zmian', $rest_a === $rest_b);
}
zp_test('popup: drugi przebieg nic nie zmienia', zp_s95_img_html($out) === $out);
$ops = zp_s95_img_exit_ops($page, zp_speed_scan($page));
zp_test('popup: trzy operacje (2 grafiki + skrypt)', count($ops) === 3);

/* 2. Grafika z tą samą klasą poza popupem: ruszamy tylko popup. */
$outside = '<img class="zp-exit-v42__person" src="https://zaprojektowani.com/wp-content/uploads/2026/06/x.webp" srcset="https://zaprojektowani.com/wp-content/uploads/2026/06/x.webp 10w" loading="eager" alt="">';
$page2 = zp_test_page($outside . $popup);
$out2 = zp_s95_img_html($page2);
zp_test('grafika poza popupem bez zmian', strpos($out2, $outside) !== false && zp_test_undo($out2) === $page2);

/* 3. Przypadki, w których nic nie wolno zmienić. */
$person = strpos($popup, '<img', strpos($popup, 'zp-exit-v42__visual'));
$person_end = strpos($popup, '>', $person) + 1;
$person_tag = substr($popup, $person, $person_end - $person);
$csp = '<meta http-equiv="Content-Security-Policy" content="default-src \'self\'">';
$cases = [
  'inna klasa grafiki' => zp_test_page(str_replace('class="zp-exit-v42__person"', 'class="zp-exit-v42__person zp-x"', $popup)),
  'trzecia grafika z tą samą klasą' => zp_test_page(substr($popup, 0, $person_end) . $person_tag . substr($popup, $person_end)),
  'grafika już lazy' => zp_test_page(preg_replace('~loading="eager"~', 'loading="lazy"', $popup, 1)),
  'grafika bez loading' => zp_test_page(preg_replace('~ loading="eager"~', '', $popup, 1)),
  'fetchpriority już ustawione' => zp_test_page(preg_replace('~ loading="eager"~', ' loading="eager" fetchpriority="high"', $popup, 1)),
  'popup otwarty (aria-hidden=false)' => zp_test_page(str_replace('<div id="zp-exit-v42" aria-hidden="true"', '<div id="zp-exit-v42" aria-hidden="false"', $popup)),
  'dwa popupy' => zp_test_page($popup . $popup),
  'popup w komentarzu' => zp_test_page('<!-- ' . $popup . ' -->'),
  'popup w szablonie <script type=text/template>' => zp_test_page('<script type="text/template">' . $popup . '</script>'),
  'grafika z innej domeny' => zp_test_page(str_replace(' src="https://zaprojektowani.com/wp-content/uploads/2026/06/stanislaw_pointing.webp"', ' src="https://cdn.example.com/stanislaw_pointing.webp"', $popup)),
  'podwójny src' => zp_test_page(preg_replace('~ loading="eager"~', ' src="https://zaprojektowani.com/wp-content/uploads/2026/06/y.webp" loading="eager"', $popup, 1)),
  'podwójny srcset' => zp_test_page(preg_replace('~ loading="eager"~', ' srcset="https://zaprojektowani.com/wp-content/uploads/2026/06/y.webp 5w" loading="eager"', $popup, 1)),
  'Content-Security-Policy w <meta>' => zp_test_page($popup, $csp),
  'popup bez zamknięcia' => zp_test_page(substr($popup, 0, (int) strrpos($popup, '</div>'))),
];
foreach ($cases as $name => $variant) {
  zp_test('bez zmian: ' . $name, zp_s95_img_html($variant) === $variant);
}
$_GET['zp_s95_img'] = '0';
zp_test('bez zmian: ?zp_s95_img=0', zp_s95_img_html($page) === $page);
unset($_GET['zp_s95_img']);
$GLOBALS['zp_test_options']['zp_s95_img_off'] = '1';
zp_test('bez zmian: opcja zp_s95_img_off=1', zp_s95_img_html($page) === $page);
$GLOBALS['zp_test_options'] = [];
zp_test('po wyłącznikach znów działa', zp_s95_img_html($page) === $out);

/* 4. Opcjonalnie: cała strona główna (zapisany HTML z tej wersji wtyczki) z doklejonym popupem. */
if (!empty($argv[1])) {
  $home = (string) file_get_contents($argv[1]);
  $at = strripos($home, '</body>');
  $full = substr($home, 0, (int) $at) . $popup . substr($home, (int) $at);
  $res = zp_s95_img_html($full);
  zp_test('strona: odznaka Meta (3 reguły)', preg_match('~<style id="zp-s95-img">(.*?)</style>~s', $res, $sm) === 1
    && substr_count($sm[1], 'meta-verified-white-685x225.webp') === 1 && substr_count($sm[1], 'meta-verified-black-404x113.webp') === 2);
  zp_test('strona: logo w stopce', isset($sm[1]) && substr_count($sm[1], 'zp-sygnet-ciemny-284x222.webp') === 1);
  zp_test('strona: zdjęcie zespołu (img + preload, ten sam srcset)', substr_count($res, 'crew-mobile-399.webp') === 2
    && preg_match('~ srcset="([^"]*crew-mobile-399[^"]*)"~', $res, $s1) === 1 && preg_match('~ imagesrcset="([^"]*crew-mobile-399[^"]*)"~', $res, $s2) === 1
    && $s1[1] === $s2[1] && substr_count($s1[1], ' 1.77x, ') === 1 && substr($s1[1], -3) === ' 2x');
  zp_test('strona: logotypy nagłówka w <picture>', substr_count($res, '<picture style="display:contents"><source media="(max-width:1360px)"') === 2);
  zp_test('strona: Trustindex', substr_count($res, 'szybkosc-3295/trustindex-wordmark-white.webp') === 2);
  zp_test('strona: popup', substr_count($res, 'data-zp-s95-src=') === 2 && substr_count($res, '<script id="zp-s95-img-exit">') === 1);
  zp_test('strona: drugi przebieg nic nie zmienia', zp_s95_img_html($res) === $res);
}

echo $failed ? "\n$failed FAIL\n" : "\nWSZYSTKO OK\n";
exit($failed ? 1 : 0);
