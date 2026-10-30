<?php
/**
 * 3.2.95 — desktop: robot 3D (Spline) startuje dopiero po namalowaniu plakatu.
 *
 * Problem (live 29.10, desktop LCP raz 0,9 s, raz 2,1 s): skrypt #zh-spline-loader z templates/home-hero.php
 * wołał startSpline() jeszcze w trakcie parsowania HTML. To od razu dodawało preconnect do unpkg/jsDelivr,
 * preload sceny assets/spline/home-scene-117.splinecode (1 349 766 B) i moduł @splinetool/viewer (~640 KB).
 * Lighthouse (Lantern) pomija w szacowaniu LCP tylko żądania, które skończyły się PO obserwowanym LCP;
 * gdy scena i viewer zdążyły przed namalowaniem plakatu, wchodziły do grafu LCP (+~1,1 s przy 10 Mbit/s).
 *
 * Zmiana (tylko na poziomie bufora strony głównej, filtr zp_s95_html, priorytet 30): ostatnie wywołanie
 * startSpline() w tym skrypcie zastępuje bramka, która uruchamia robota dopiero, gdy nastąpi pierwsze z:
 *  - wpis largest-contentful-paint dla #zhPoster (plakat jest na ekranie) + 2 klatki animacji,
 *  - zdarzenie load okna (+ 2 klatki, w ukrytej karcie od razu),
 *  - pierwszy zamiar odwiedzającego (pointerdown, keydown, wheel, touchstart, przewinięcie strony),
 *  - powrót strony z bfcache (pageshow.persisted).
 * Reszta logiki zostaje bez zmian: plakat jest widoczny, dopóki viewer nie zgłosi wczytanej sceny
 * (load-complete → .is-spline-loaded), telefony i tablety (≤1100 px) nadal nie pobierają 3D w ogóle
 * (startSpline() na wąskim ekranie tylko czeka na poszerzenie okna), a ?zp_no_spline / brak sprzętowego
 * WebGL / prefers-reduced-motion / Save-Data kończą skrypt przed bramką, jak dotąd.
 * Każda klatka wygląda jak dziś; jedynie zamiana plakatu na 3D przychodzi nieco później.
 *
 * Wyłączniki: opcja zp_s95_desk_off = '1', parametr ?zp_s95_desk=0 (jedna odsłona; każdy parametr omija cache).
 * Znacznik w HTML (do kontroli po wdrożeniu): <script id="zh-spline-loader" data-zp-s95-desk="3295">.
 */
if (!defined('ABSPATH')) { exit; }

if (!function_exists('zp_s95_desk_enabled')) {
  function zp_s95_desk_enabled(): bool {
    if (isset($_GET['zp_s95_desk']) && (string) $_GET['zp_s95_desk'] === '0') { return false; }
    return !function_exists('get_option') || (string) get_option('zp_s95_desk_off', '0') !== '1';
  }
}

if (!function_exists('zp_s95_desk_spline_gate_js')) {
  /** Bramka wstawiana w miejsce końcowego startSpline(); działa w domknięciu skryptu (d, w, startSpline). */
  function zp_s95_desk_spline_gate_js(): string {
    return <<<'JS'
  /* 3.2.95 (szybkosc-3295/desk.php): the 3D starts after the poster's first paint (its largest-contentful-paint
     entry plus two frames), at window load or at the visitor's first intent, whichever comes first. The poster
     stays until the viewer reports a loaded scene, as before; only the swap to 3D comes a little later. */
  (function(){
    var on=false,po=null,poster=d.getElementById('zhPoster'),EV=['pointerdown','keydown','wheel','touchstart'];
    function stop(){
      if(po){try{po.disconnect();}catch(e){}po=null;}
      for(var i=0;i<EV.length;i++){try{w.removeEventListener(EV[i],intent,true);}catch(e){}}
      try{w.removeEventListener('scroll',intent);w.removeEventListener('load',loaded);w.removeEventListener('pageshow',shown);}catch(e){}
    }
    function go(via){
      if(on)return;on=true;stop();
      try{w.__zpSplineGate={via:via,t:(w.performance&&performance.now)?Math.round(performance.now()):0};}catch(e){}
      try{startSpline();}catch(e){}
    }
    function frames(via){
      if(on)return;
      if(d.visibilityState==='hidden'||typeof w.requestAnimationFrame!=='function'){go(via);return;}
      w.requestAnimationFrame(function(){w.requestAnimationFrame(function(){go(via);});});
    }
    function intent(){go('intent');}
    function loaded(){frames('load');}
    function shown(ev){if(ev&&ev.persisted)go('pageshow');}
    function seen(es){
      for(var i=0;es&&i<es.length;i++){
        if(es[i].element===poster){if(po){try{po.disconnect();}catch(e){}po=null;}frames('lcp');return true;}
      }
      return false;
    }
    /* While the document is still being parsed the observer's callback task can wait long after the paint;
       takeRecords() in an animation frame picks up the queued entry at the next frame. */
    function poll(){
      if(on||!po)return;
      try{if(seen(po.takeRecords()))return;}catch(e){}
      w.requestAnimationFrame(poll);
    }
    if(d.readyState==='complete'){frames('load');return;}
    try{
      var PO=w.PerformanceObserver,types=PO&&PO.supportedEntryTypes;
      if(poster&&types&&types.indexOf('largest-contentful-paint')>-1){
        po=new PO(function(list){seen(list.getEntries());});
        po.observe({type:'largest-contentful-paint',buffered:true});
        if(typeof w.requestAnimationFrame==='function')w.requestAnimationFrame(poll);
      }
    }catch(e){po=null;}
    w.addEventListener('load',loaded);
    w.addEventListener('pageshow',shown);
    w.addEventListener('scroll',intent,{passive:true});
    for(var i=0;i<EV.length;i++){try{w.addEventListener(EV[i],intent,{capture:true,passive:true});}catch(e){}}
  })();
JS;
  }
}

if (!function_exists('zp_s95_desk_spline_gate')) {
  /**
   * Zamienia końcowe startSpline(); w <script id="zh-spline-loader"> na bramkę. Jeśli szablon się zmienił
   * i wzorca nie ma, HTML zostaje bez zmian (brak znacznika data-zp-s95-desk).
   */
  function zp_s95_desk_spline_gate(string $html): string {
    if (!preg_match('#<script\b[^>]*\bid=(["\'])zh-spline-loader\1[^>]*>#i', $html, $m, PREG_OFFSET_CAPTURE)) { return $html; }
    $open = $m[0][0];
    $open_at = (int) $m[0][1];
    if (stripos($open, 'data-zp-s95-desk') !== false) { return $html; }
    $body_at = $open_at + strlen($open);
    $close_at = stripos($html, '</script>', $body_at);
    if ($close_at === false) { return $html; }
    $body = substr($html, $body_at, $close_at - $body_at);
    // Ostatnia instrukcja IIFE: (opcjonalny komentarz „3.2.10: …”) + startSpline(); + })();
    $re = '#(?:\n[ \t]*// 3\.2\.10:[^\n]*(?:\n[ \t]*//[^\n]*)*)?\n[ \t]*startSpline\(\);(\s*\}\)\(\);\s*)$#';
    if (!preg_match($re, $body, $t, PREG_OFFSET_CAPTURE)) { return $html; }
    $new_body = substr($body, 0, (int) $t[0][1]) . "\n" . zp_s95_desk_spline_gate_js() . $t[1][0];
    $new_open = substr($open, 0, -1) . ' data-zp-s95-desk="3295">';
    return substr($html, 0, $open_at) . $new_open . $new_body . substr($html, $close_at);
  }
}

if (!function_exists('zp_s95_desk_html')) {
  function zp_s95_desk_html($html) {
    if (!is_string($html) || $html === '' || !zp_s95_desk_enabled()) { return $html; }
    try {
      $out = zp_s95_desk_spline_gate($html);
      return ($out !== '' && stripos($out, '</html>') !== false) ? $out : $html;
    } catch (\Throwable $e) {
      return $html;
    }
  }
}

if (function_exists('add_filter')) {
  add_filter('zp_s95_html', 'zp_s95_desk_html', 30);
}
