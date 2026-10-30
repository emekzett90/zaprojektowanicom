<?php
/**
 * 3.2.95 — CSS na wąskich ekranach (filtr zp_s95_html, priorytet 50).
 *
 * Problem: na telefonach pełne arkusze „odroczone” (pliki 3236 z data-zp-full-css-3236, linki 3233
 * z media='(min-width:1101px)…' i onload przywracającym media, linki print optymalizatora) nie
 * blokują pierwszego malowania, ale przeglądarka i tak pobiera je od razu (VeryLow) i kończy przed
 * LCP, więc Lighthouse liczy je w LCP. Pierwszy ekran malują wbudowane podzbiory 3233/3236, których
 * ten moduł nie dotyka.
 *
 * Co robi (tylko kopia 'm' strony głównej PL/EN, ten sam klasyfikator co cache-dropin-20261009.php):
 * - każdy taki link w <head> zamienia na obojętny <template data-zp-s95-css> z DOKŁADNIE tym samym
 *   tagiem, w tym samym miejscu; za nim krótki skrypt slotu i <noscript> z tym samym tagiem (bez JS
 *   strona dostaje dokładnie to, co wcześniej; istniejące noscript zostają);
 * - przed pierwszym slotem jeden mały skrypt sterujący:
 *   * ekran ≥1101 px w chwili parsowania (np. tablet z Androidem poziomo) albo #kotwica w adresie:
 *     każdy skrypt slotu od razu wpisuje (document.write) oryginalny link — parser tworzy go tak jak
 *     przed 3.2.95, więc blokuje renderowanie i skrypty tam, gdzie pasuje media; preload as=style dla
 *     pasujących adresów startuje na początku, żeby pobrania nie szły jedno po drugim;
 *   * ekran ≤1100 px: wszystkie linki wracają jedną paczką, każdy na swoje miejsce (ta sama kolejność
 *     kaskady, te same media i onload), przy najwcześniejszym z: wpis LCP albo window load, gdy obrazy
 *     bohatera (tło #zhPoster, znak #zhStage::before, obrazy eager w #zhHero) są już pobrane, + 3 klatki
 *     (każdy nowy wpis LCP liczy od nowa; najpóźniej 1 s po load); pierwsza prawdziwa intencja
 *     (pointerdown, touchstart, keydown, wheel, przewinięcie strony; scrollY>0), hashchange, pageshow
 *     z bfcache, przejście ekranu na ≥1101 px, beforeprint (po geście i #kotwicy dodatkowo preload
 *     z fetchpriority=high dla tych samych adresów); potem window.zpS95Released = true
 *     i jednorazowe zdarzenie 'zp:s95-release' na document.
 * - header.min.css, mobile-sticky-cta, trust-logos i services-path zostają bez zmian (szybkie dotknięcie
 *   menu, pasek CTA, układ sekcji tuż pod pierwszym ekranem — patrz zp_s95_css_keep()).
 *
 * Kopia 'd' (komputery, iPadOS z UA Maca) nie zmienia się wcale. Wyłączniki: opcja zp_s95_css_off = '1',
 * ?zp_s95_css=0 (jedna odsłona) albo zp_s95_off / ?zp_s95=0 dla całej 3.2.95. Każda polityka CSP
 * (nagłówek albo meta) zostawia dotychczasowe dostarczanie, tak jak w 3280.
 */
if (!defined('ABSPATH')) { exit; }

/** 'm' albo 'd' — dokładnie ten sam test co kopia strony w cache (ZP_Cache_Early_20261009::device). */
function zp_s95_css_device(array $server): string {
  if (class_exists('ZP_Cache_Early_20261009', false) && is_callable(['ZP_Cache_Early_20261009', 'device'])) {
    return (string) ZP_Cache_Early_20261009::device($server);
  }
  if (isset($server['HTTP_SEC_CH_UA_MOBILE'])) { return $server['HTTP_SEC_CH_UA_MOBILE'] === '?1' ? 'm' : 'd'; }
  $ua = (string) ($server['HTTP_USER_AGENT'] ?? '');
  if ($ua === '') { return 'd'; }
  foreach (['Mobile', 'Android', 'Silk/', 'Kindle', 'BlackBerry', 'Opera Mini', 'Opera Mobi'] as $needle) { if (strpos($ua, $needle) !== false) { return 'm'; } }
  return 'd';
}

/**
 * Atrybuty tagu jak w przeglądarce (pierwsze wystąpienie wygrywa). Własny parser, bo wspólny
 * zp_css_delivery_attributes() gubi atrybut stojący po atrybucie bez wartości
 * (np. „data-zp-full-css-3236 onload=…” daje brak onload).
 */
function zp_s95_css_attrs(string $tag): array {
  $attrs = [];
  $gt = strrpos($tag, '>'); if ($gt !== false) { $tag = substr($tag, 0, $gt); }
  $tag = preg_replace('~^<[a-zA-Z][^\s/>]*~', '', $tag);
  if (preg_match_all('~([^\s=/>"\']+)(?:\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|([^\s>"\']+)))?~', (string) $tag, $matches, PREG_SET_ORDER)) {
    foreach ($matches as $m) {
      $name = strtolower($m[1]);
      if (!array_key_exists($name, $attrs)) {
        $attrs[$name] = html_entity_decode(($m[2] ?? '') . ($m[3] ?? '') . ($m[4] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8');
      }
    }
  }
  return $attrs;
}

/**
 * Pliki zostawione bez zmian (pobierane od razu jak dziś): menu (szuflada) przy szybkim dotknięciu,
 * dolny pasek CTA oraz dwie sekcje tuż pod pierwszym ekranem (logotypy klientów, ścieżka usług) —
 * bez ich CSS te sekcje układają się w wysoką, nieostylowaną kolumnę i leniwe zdjęcia
 * (loading=lazy) wpadają w zasięg pobierania jeszcze przed LCP.
 */
function zp_s95_css_keep(): array {
  $keep = ['assets/css/blocks/header.min.css', 'assets/css/blocks/mobile-sticky-cta.min.css',
    'assets/css/blocks/trust-logos.min.css', 'assets/css/blocks/services-path.css'];
  $keep = function_exists('apply_filters') ? apply_filters('zp_s95_css_keep', $keep) : $keep;
  return is_array($keep) ? array_values(array_filter($keep, 'is_string')) : [];
}

/**
 * Czy link można przytrzymać: arkusz w <head>, który na ekranach ≤1100 px dziś nie pasuje (media
 * '(min-width:1101px)…' albo 'print'), ma onload zmieniający media i należy do jednej z trzech znanych
 * dróg dostarczania. Wszystko inne (motyw, obce arkusze, inne atrybuty) zostaje bez zmian.
 */
function zp_s95_css_holdable(array $a): bool {
  if (strtolower(trim($a['rel'] ?? '')) !== 'stylesheet' || trim($a['href'] ?? '') === '' || trim($a['onload'] ?? '') === '') { return false; }
  foreach (['disabled', 'integrity', 'blocking', 'title', 'crossorigin', 'nonce'] as $k) { if (isset($a[$k])) { return false; } }
  $media = trim($a['media'] ?? '');
  $narrow_off = $media === 'print' || $media === '(min-width:1101px)' || strpos($media, '(min-width:1101px) and ') === 0;
  if (!$narrow_off || strpos($media, ',') !== false) { return false; }
  $kind = isset($a['data-zp-full-css-3236']) || ($a['data-zp-home-deferred'] ?? '') === '3233' || $media === 'print';
  if (!$kind) { return false; }
  $href = (string) $a['href'];
  $path = (string) wp_parse_url($href, PHP_URL_PATH);
  foreach (zp_s95_css_keep() as $keep) { if ($path !== '' && substr($path, -strlen($keep)) === $keep) { return false; } }
  // Tylko lokalne pliki strony (ten sam host co strona, bez danych logowania i fragmentu).
  $u = wp_parse_url($href); $home = wp_parse_url(home_url('/'));
  if (!is_array($u) || !is_array($home) || isset($u['user']) || isset($u['pass']) || isset($u['fragment'])
      || strcasecmp((string) ($u['host'] ?? ''), (string) ($home['host'] ?? '')) !== 0
      || strpos((string) ($u['path'] ?? ''), '/wp-content/') !== 0) { return false; }
  return true;
}

function zp_s95_css_script(array $preloads): string {
  $base = ZP_SUITE_URL; $list = [];
  foreach ($preloads as [$href, $low]) {
    $list[] = strpos($href, $base) === 0 ? [substr($href, strlen($base)), $low ? 1 : 0] : [$href, $low ? 1 : 0, 1];
  }
  $json = wp_json_encode($list, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP);
  $b = wp_json_encode($base, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP);
  if (!is_string($json) || !is_string($b)) { return ''; }
  $js = <<<'JS'
(function(w,d){'use strict';
if(w.__zpS95)return;
var S=w.__zpS95={r:0,why:''},B=%B%,P=%P%,po=null,offs=[];
function swap(t){try{var p=t.parentNode,l=t.content&&t.content.firstElementChild;if(!p)return;if(l)p.replaceChild(l,t);else{while(t.firstChild)p.insertBefore(t.firstChild,t);p.removeChild(t);}}catch(e){}}
function sweep(){var ts=d.querySelectorAll('template[data-zp-s95-css]');for(var i=0;i<ts.length;i++)swap(ts[i]);}
// Po geście użytkownika (przewija, dotyka, #kotwica) arkusze są potrzebne od razu: preload z wysokim
// priorytetem dla już sparsowanych slotów, żeby nie czekały w kolejce za obrazami (Lowest dla media, które nie pasują).
function boost(){try{var ts=d.querySelectorAll('template[data-zp-s95-css]'),h=d.head;for(var i=0;h&&i<ts.length;i++){var l=ts[i].content&&ts[i].content.firstElementChild,u=l&&l.getAttribute('href');if(!u)continue;var k=d.createElement('link');k.rel='preload';k.as='style';k.href=u;k.setAttribute('fetchpriority','high');h.appendChild(k);}}catch(e){}}
function release(why){
if(S.r)return;S.r=1;S.why=why;try{S.at=Math.round(performance.now());}catch(e){}
for(var i=0;i<offs.length;i++){try{offs[i]();}catch(e){}}offs=[];
if(po){try{po.disconnect();}catch(e){}po=null;}
if(/^(pointerdown|touchstart|keydown|wheel|scroll|hashchange|hash)$/.test(why))boost();
sweep();w.zpS95Released=true;
try{d.dispatchEvent(new CustomEvent('zp:s95-release',{detail:{reason:why}}));}catch(e){}
}
S.release=release;
S.s=function(){
if(!S.r)return;
var c=d.currentScript,t=c&&c.previousElementSibling;
if(!t||t.tagName!=='TEMPLATE'||!t.hasAttribute('data-zp-s95-css'))return;
var l=t.content&&t.content.firstElementChild;
if(l&&d.readyState==='loading'){try{d.write(l.outerHTML);t.parentNode.removeChild(t);return;}catch(e){}}
swap(t);
};
function on(o,n,f,x){o.addEventListener(n,f,x);offs.push(function(){o.removeEventListener(n,f,x);});}
// LCP na telefonie to tło #zhPoster albo znak w #zhStage::before (który większy) — czekamy, aż obrazy tła
// widocznych elementów bohatera i obrazy eager w #zhHero są pobrane, potem 3 klatki bez nowego wpisu LCP
// (obraz zdąży się namalować, zanim ruszą pobrania arkuszy).
var hero=null,lcpSeen=0,gen=0,pend=0;
function urls(el,ps){var o=[];try{var cs=getComputedStyle(el,ps);if(cs.display==='none'||(ps&&(cs.content==='none'||cs.content==='normal')))return o;var v=cs.backgroundImage||'',m,re=/url\(["']?([^"')]+)["']?\)/g;while((m=re.exec(v)))o.push(m[1]);}catch(e){}return o;}
function heroReady(){
if(!hero){hero={sets:[],imgs:[]};var p=d.getElementById('zhPoster'),s=d.getElementById('zhStage'),h=d.getElementById('zhHero');
if(p&&p.getClientRects().length)hero.sets.push(urls(p,null));if(s&&s.getClientRects().length){hero.sets.push(urls(s,'::before'));hero.sets.push(urls(s,null));}
if(h)hero.imgs=Array.prototype.slice.call(h.querySelectorAll('img:not([loading="lazy"])'));}
for(var i=0;i<hero.sets.length;i++){var u=hero.sets[i],ok=!u.length;for(var j=0;j<u.length&&!ok;j++){var e=performance.getEntriesByName(u[j]);ok=!!(e.length&&e[e.length-1].responseEnd>0);}if(!ok)return false;}
for(var k=0;k<hero.imgs.length;k++){if(!hero.imgs[k].complete)return false;}
return true;}
function arm(why){var id=++gen,r=w.requestAnimationFrame,n=0;pend=1;if(!r){release(why);return;}var f=function(){if(id!==gen||S.r)return;if(++n<3)r(f);else release(why);};r(f);}
d.addEventListener('DOMContentLoaded',function(){if(S.r)sweep();});
w.addEventListener('load',function(){if(!S.r){if(!pend){try{if(heroReady())arm('load');}catch(e){release('load');}}setTimeout(function(){release('load');},1000);}sweep();});
try{
var mq=w.matchMedia?w.matchMedia('(min-width:1101px)'):null;
if(mq&&mq.matches){
for(var i=0;i<P.length;i++){var k=d.createElement('link');k.rel='preload';k.as='style';k.href=(P[i][2]?'':B)+P[i][0];if(P[i][1])k.setAttribute('fetchpriority','low');d.head.appendChild(k);}
release('wide');
}else if(w.location.hash){release('hash');}
else{
// Tylko prawdziwe zdarzenia; „scroll” liczy się dla przewinięcia strony, nie dla przewijanego elementu
// (pasek logotypów sam ustawia scrollLeft w trakcie ładowania).
var fire=function(e){if(e){if(e.isTrusted===false)return;if(e.type==='scroll'&&e.target!==d&&e.target!==w&&e.target!==d.documentElement&&e.target!==d.body&&!((w.scrollY||w.pageYOffset||0)>0))return;}release(e&&e.type||'intent');},o={passive:true,capture:true},ev=['pointerdown','touchstart','keydown','wheel','scroll'];
for(var j=0;j<ev.length;j++)on(w,ev[j],fire,o);
on(w,'hashchange',fire);on(w,'beforeprint',fire);
on(w,'pageshow',function(e){if(e.persisted)release('pageshow');});
if(mq){var mf=function(e){if(e.matches)release('wide');};if(mq.addEventListener)on(mq,'change',mf);else if(mq.addListener){mq.addListener(mf);offs.push(function(){mq.removeListener(mf);});}}
if((w.scrollY||w.pageYOffset||0)>0){release('scroll');}
else if(w.PerformanceObserver&&PerformanceObserver.supportedEntryTypes&&PerformanceObserver.supportedEntryTypes.indexOf('largest-contentful-paint')>-1){
// Każdy nowy wpis LCP zaczyna odliczanie od nowa; pobranie obrazu bohatera uzbraja je raz.
po=new PerformanceObserver(function(list){if(S.r)return;lcpSeen=1;if(heroReady())arm('lcp');});
po.observe({type:'largest-contentful-paint',buffered:true});
if(PerformanceObserver.supportedEntryTypes.indexOf('resource')>-1){var ro=new PerformanceObserver(function(){if(S.r||!lcpSeen||pend)return;if(heroReady())arm('lcp');});ro.observe({type:'resource'});offs.push(function(){ro.disconnect();});}
}
}
}catch(e){release('error');}
})(window,document);
JS;
  // Komentarze (linie zaczynające się od //) zostają w źródle, nie w HTML.
  $js = (string) preg_replace('~^//[^\n]*\n~m', '', $js);
  return str_replace(['%B%', '%P%'], [$b, $json], $js);
}

function zp_s95_css_html($html) {
  if (!is_string($html) || $html === '' || strpos($html, 'data-zp-s95-css') !== false) { return $html; }
  if ((string) get_option('zp_s95_css_off', '0') === '1' || (isset($_GET['zp_s95_css']) && (string) $_GET['zp_s95_css'] === '0')) { return $html; }
  if (zp_s95_css_device($_SERVER) !== 'm') { return $html; }
  foreach (['zp_speed_scan', 'zp_speed_in_skip', 'zp_speed_tag_end'] as $fn) { if (!function_exists($fn)) { return $html; } }
  if (strpos($html, 'id="zhHero"') === false) { return $html; }
  try {
    // Nowy kod inline wymagałby zgody CSP; przy jakiejkolwiek polityce zostaje dotychczasowe dostarczanie.
    foreach (headers_list() as $header) { if (stripos($header, 'Content-Security-Policy:') === 0) { return $html; } }
    $scan = zp_speed_scan($html);
    if (!$scan || $scan['head_end'] < 0) { return $html; }
    if (preg_match_all('~<meta\b~i', $html, $metas, PREG_OFFSET_CAPTURE)) {
      foreach ($metas[0] as [$token, $at]) {
        if ($at >= $scan['head_end'] || zp_speed_in_skip($scan['skip'], $at)) { continue; }
        $gt = zp_speed_tag_end($html, $at); if ($gt < 0) { return $html; }
        $m = zp_s95_css_attrs(substr($html, $at, $gt + 1 - $at));
        if (strtolower(trim($m['http-equiv'] ?? '')) === 'content-security-policy') { return $html; }
      }
    }
    $ops = []; $preloads = []; $seen = [];
    foreach ($scan['sheet_links'] as [$start, $end]) {
      if ($start >= $scan['head_end']) { continue; }
      $node = substr($html, $start, $end - $start);
      $a = zp_s95_css_attrs($node);
      if (!zp_s95_css_holdable($a)) { continue; }
      $ops[] = [$start, $end, '<template data-zp-s95-css>' . $node . '</template><script data-cfasync="false">window.__zpS95&&__zpS95.s()</script><noscript>' . $node . '</noscript>'];
      $media = trim($a['media'] ?? ''); $href = (string) $a['href'];
      if (strpos($media, '(min-width:1101px)') === 0 && !isset($seen[$href])) {
        $seen[$href] = true; $preloads[] = [$href, strtolower(trim($a['fetchpriority'] ?? '')) === 'low'];
      }
    }
    if (!$ops) { return $html; }
    $script = zp_s95_css_script($preloads);
    if ($script === '') { return $html; }
    $ops[0][2] = '<script id="zp-s95-css-3295" data-held="' . count($ops) . '" data-cfasync="false">' . $script . '</script>' . $ops[0][2];
    $out = ''; $last = 0;
    foreach ($ops as [$start, $end, $replacement]) {
      if ($start < $last) { return $html; }
      $out .= substr($html, $last, $start - $last) . $replacement; $last = $end;
    }
    return $out . substr($html, $last);
  } catch (\Throwable $error) {
    return $html;
  }
}
add_filter('zp_s95_html', 'zp_s95_css_html', 50);
