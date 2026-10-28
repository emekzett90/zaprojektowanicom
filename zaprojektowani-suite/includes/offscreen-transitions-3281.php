<?php
/** Avoid hundreds of invisible startup transitions when full home CSS arrives. */
if (!defined('ABSPATH')) { exit; }

function zp_home_offscreen_transitions_3281_html(string $html): string {
    if (strpos($html, 'id="zp-offscreen-transitions-3281"') !== false
        || !function_exists('zp_speed_scan') || !function_exists('zp_speed_tag_end')
        || !function_exists('zp_speed_in_skip') || !function_exists('zp_css_delivery_attributes')) { return $html; }
    try {
        // An enforced CSP may reject this inline recovery script. Keep the
        // original document rather than leave a transition guard behind.
        foreach (headers_list() as $header) { if (stripos($header, 'Content-Security-Policy:') === 0) { return $html; } }
        $scan = zp_speed_scan($html);
        if (!$scan || $scan['head_end'] < 0 || $scan['body_open'] < 0) { return $html; }
        if (preg_match_all('~<meta\b[^>]*>~i', $html, $metas, PREG_OFFSET_CAPTURE)) {
            foreach ($metas[0] as [$node, $at]) {
                if ($at >= $scan['head_end'] || zp_speed_in_skip($scan['skip'], $at)) { continue; }
                $attrs = zp_css_delivery_attributes($node);
                if (strtolower($attrs['http-equiv'] ?? '') === 'content-security-policy') { return $html; }
            }
        }
        $roots = ['zpMegaFooter' => ['footer', 'zpMegaFooter'], 'zpContactFormLight' => ['section', 'zpContactFormLight']];
        $ops = []; $found = [];
        if (!preg_match_all('~<[a-z][a-z0-9:-]*\b~i', $html, $matches, PREG_OFFSET_CAPTURE)) { return $html; }
        foreach ($matches[0] as [$start_tag, $at]) {
            if ($at < $scan['body_open'] || zp_speed_in_skip($scan['skip'], $at)) { continue; }
            $end = zp_speed_tag_end($html, $at); if ($end < 0) { return $html; }
            $tag = substr($html, $at, $end + 1 - $at); $attrs = zp_css_delivery_attributes($tag);
            $id = $attrs['id'] ?? ''; if (!isset($roots[$id])) { continue; }
            if (isset($found[$id])) { return $html; } // Ambiguous IDs are not eligible.
            $found[$id] = true;
            if (strtolower(substr($start_tag, 1)) !== $roots[$id][0]
                || !in_array($roots[$id][1], preg_split('/\s+/', trim($attrs['class'] ?? '')), true)
                || isset($attrs['data-zp-offscreen-transitions']) || substr($tag, -2) === '/>') { return $html; }
            $ops[] = [$at, $end + 1, substr($tag, 0, -1) . ' data-zp-offscreen-transitions="3281">'];
        }
        if (!$ops) { return $html; }
        $body_end = -1;
        if (preg_match_all('~</body\s*>~i', $html, $closings, PREG_OFFSET_CAPTURE)) {
            foreach ($closings[0] as [$tag, $at]) { if (!zp_speed_in_skip($scan['skip'], $at)) { $body_end = $at; } }
        }
        if ($body_end < 0) { return $html; }
        // scripting:none (and browsers without this media feature) keep all original transitions.
        $style = '<style id="zp-offscreen-transitions-3281">@layer zp-offscreen-transitions3281{@media(scripting:enabled){'
            . ':is(#zpMegaFooter,#zpContactFormLight)[data-zp-offscreen-transitions="3281"],'
            . ':is(#zpMegaFooter,#zpContactFormLight)[data-zp-offscreen-transitions="3281"] *,'
            . ':is(#zpMegaFooter,#zpContactFormLight)[data-zp-offscreen-transitions="3281"]::before,'
            . ':is(#zpMegaFooter,#zpContactFormLight)[data-zp-offscreen-transitions="3281"]::after,'
            . ':is(#zpMegaFooter,#zpContactFormLight)[data-zp-offscreen-transitions="3281"] *::before,'
            . ':is(#zpMegaFooter,#zpContactFormLight)[data-zp-offscreen-transitions="3281"] *::after{transition:none!important}}}</style>';
        $script = <<<'JS'
<script id="zp-offscreen-transitions-3281-js">(function(w,d){
  'use strict';
  var attr='data-zp-offscreen-transitions';
  var roots=[].slice.call(d.querySelectorAll('#zpMegaFooter['+attr+'="3281"],#zpContactFormLight['+attr+'="3281"]'));
  var observer=null,remaining=roots.length;
  if(!remaining)return;
  function clean(){
    if(observer){try{observer.disconnect();}catch(e){}}
    ['pointerdown','focusin','click'].forEach(function(name){d.removeEventListener(name,intent,true);});
    w.removeEventListener('hashchange',hash);
    w.removeEventListener('pageshow',pageshow);
  }
  function release(root){
    if(root.getAttribute(attr)!=='3281')return;
    root.removeAttribute(attr);
    if(observer){try{observer.unobserve(root);}catch(e){}}
    if(--remaining===0)clean();
  }
  function all(){roots.forEach(release);}
  function target(node){
    if(!node)return;
    roots.forEach(function(root){if(root===node||root.contains(node))release(root);});
  }
  function hash(){
    try{var value=w.location.hash||'';if(value)target(d.getElementById(decodeURIComponent(value.slice(1))));}catch(e){}
  }
  function intent(event){
    target(event.target);
    var link=event.target&&event.target.closest&&event.target.closest('a[href*="#"]');
    if(!link)return;
    try{
      var url=new URL(link.getAttribute('href'),w.location.href);
      if(url.origin===w.location.origin&&url.pathname===w.location.pathname&&url.hash)target(d.getElementById(decodeURIComponent(url.hash.slice(1))));
    }catch(e){}
  }
  function pageshow(event){if(event.persisted)all();}
  if(!('IntersectionObserver' in w)){all();return;}
  try{
    observer=new w.IntersectionObserver(function(entries){entries.forEach(function(entry){if(entry.isIntersecting)release(entry.target);});},{rootMargin:'1600px 0px',threshold:0});
    roots.forEach(function(root){observer.observe(root);});
  }catch(e){all();return;}
  ['pointerdown','focusin','click'].forEach(function(name){d.addEventListener(name,intent,{capture:true,passive:true});});
  w.addEventListener('hashchange',hash,{passive:true});
  w.addEventListener('pageshow',pageshow,{passive:true});
  hash();
})(window,document);</script>
JS;
        $ops[] = [$scan['head_end'], $scan['head_end'], $style];
        $ops[] = [$body_end, $body_end, $script];
        usort($ops, static fn($a, $b) => $b[0] <=> $a[0]);
        foreach ($ops as [$start, $end, $replacement]) { $html = substr_replace($html, $replacement, $start, $end - $start); }
        return $html;
    } catch (Throwable $ignored) { return $html; }
}
