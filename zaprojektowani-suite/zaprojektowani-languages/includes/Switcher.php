<?php
namespace ZPL;

if (!defined('ABSPATH')) { exit; }

/**
 * Server-rendered language switch (so it is visible at first paint and crawlable).
 * 1.2.0: with German and Spanish offered it becomes a menu in the header bar (the current language and a chevron,
 * the languages below it) and a segmented control with flags in the mobile drawer; with Polish and English only it is
 * the PL/EN switch it always was.
 */
final class Switcher {
    public static function markup(string $lang, string $source, string $variant): string {
        if (count(Lang::public()) > 2) { return self::multi($lang, $source, $variant); }
        $pl = esc_url(home_url($source));
        // A page without an English version yet: English opens the English home page, never a Polish /en/ copy.
        $hasEn = $lang === 'en' || (Router::has_route($source) && Dict::has_page($source));
        $en = esc_url(home_url($hasEn ? Router::en_path($source) : '/en/'));
        $isEn = $lang === 'en';
        $label = $isEn ? 'Language' : 'Język';
        $cls = 'zpl-switch zpl-switch--' . $variant . ($isEn ? ' is-en' : ' is-pl');
        $out = '<div class="' . $cls . '" role="group" aria-label="' . esc_attr($label) . '" data-zpl-switch translate="no">';
        if ($variant === 'drawer') { $out .= '<span class="zpl-switch__caption" aria-hidden="true">' . ($isEn ? 'Language' : 'Język') . '</span>'; }
        $drawer = $variant === 'drawer';
        // Mobile drawer: round flags instead of the PL/EN codes (names stay next to them).
        $plMark = $drawer ? self::flag('pl') : '<span class="zpl-switch__code">PL</span>';
        $enMark = $drawer ? self::flag('en') : '<span class="zpl-switch__code">EN</span>';
        $out .= '<span class="zpl-switch__track">'
            . '<span class="zpl-switch__thumb" aria-hidden="true"></span>'
            . '<a class="zpl-switch__opt" href="' . $pl . '" hreflang="pl" lang="pl" data-zpl-lang="pl" aria-current="' . ($isEn ? 'false' : 'true') . '">' . $plMark . ($drawer ? '<span class="zpl-switch__name">Polski</span>' : '') . '</a>'
            . '<a class="zpl-switch__opt" href="' . $en . '" hreflang="en" lang="en" data-zpl-lang="en" aria-current="' . ($isEn ? 'true' : 'false') . '">' . $enMark . ($drawer ? '<span class="zpl-switch__name">English</span>' : '') . '</a>'
            . '</span></div>';
        return $out;
    }

    /**
     * 1.2.0: three or four languages. Header bar and mobile bar: a button with the current language opens the list of
     * languages (links, so crawlers and visitors without JavaScript still reach every version). Drawer: one segmented
     * row with a flag and the code of each language, the dark thumb under the current one.
     */
    private static function multi(string $lang, string $source, string $variant): string {
        $langs = Lang::public();
        if (!in_array($lang, $langs, true)) { $langs[] = $lang; }
        $paths = Runtime::paths($source);
        $paths[$lang] = $lang === 'pl' ? $source : Router::path_for($source, $lang);
        $index = (int) array_search($lang, $langs, true);
        $label = Lang::LABEL[$lang] ?? 'Język';
        $drawer = $variant === 'drawer';
        $cls = 'zpl-switch zpl-switch--' . $variant . ' zpl-switch--multi ' . ($drawer ? 'zpl-switch--row' : 'zpl-switch--menu') . ' is-' . $lang;
        $style = '--zpl-n:' . count($langs) . ';--zpl-i:' . $index;
        $out = '<div class="' . $cls . '" role="group" aria-label="' . esc_attr($label) . '" data-zpl-switch data-zpl-current="' . $lang . '" style="' . $style . '" translate="no">';
        $opts = '';
        foreach ($langs as $l) {
            $href = esc_url(home_url($paths[$l] ?? Lang::prefix($l)));
            $cur = $l === $lang ? 'true' : 'false';
            $mark = $drawer ? self::flag($l, '-' . $variant) . '<span class="zpl-switch__code">' . Lang::CODE[$l] . '</span>'
                : self::flag($l, '-' . $variant) . '<span class="zpl-switch__name">' . esc_html(Lang::NAME[$l]) . '</span><span class="zpl-switch__code">' . Lang::CODE[$l] . '</span>';
            $opts .= '<a class="zpl-switch__opt" href="' . $href . '" hreflang="' . $l . '" lang="' . $l . '" data-zpl-lang="' . $l . '" aria-current="' . $cur . '"' . ($drawer ? ' aria-label="' . esc_attr(Lang::NAME[$l]) . '"' : '') . '>' . $mark . '</a>';
        }
        if ($drawer) {
            $out .= '<span class="zpl-switch__caption" aria-hidden="true">' . esc_html($label) . '</span>'
                . '<span class="zpl-switch__track"><span class="zpl-switch__thumb" aria-hidden="true"></span>' . $opts . '</span></div>';
            return $out;
        }
        $id = 'zpl-langs-' . $variant;
        $out .= '<button type="button" class="zpl-switch__btn" aria-haspopup="true" aria-expanded="false" aria-controls="' . $id . '" aria-label="' . esc_attr($label . ': ' . Lang::NAME[$lang]) . '">'
            . '<span class="zpl-switch__now"><span class="zpl-switch__code">' . Lang::CODE[$lang] . '</span></span>'
            . '<svg class="zpl-switch__chev" viewBox="0 0 12 12" aria-hidden="true" focusable="false"><path d="M2.5 4.5 6 8l3.5-3.5" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>'
            . '</button><div class="zpl-switch__menu" id="' . $id . '">' . $opts . '</div></div>';
        return $out;
    }

    /** Round flag (inline SVG, cropped to a circle by CSS). */
    private static function flag(string $lang, string $id = ''): string {
        if ($lang === 'pl') {
            $svg = '<svg viewBox="0 0 16 10" preserveAspectRatio="xMidYMid slice" focusable="false"><rect width="16" height="5" fill="#fff"/><rect y="5" width="16" height="5" fill="#dc143c"/></svg>';
        } elseif ($lang === 'de') {
            $svg = '<svg viewBox="0 0 15 9" preserveAspectRatio="xMidYMid slice" focusable="false"><rect width="15" height="3" fill="#000"/><rect y="3" width="15" height="3" fill="#dd0000"/><rect y="6" width="15" height="3" fill="#ffce00"/></svg>';
        } elseif ($lang === 'es') {
            $svg = '<svg viewBox="0 0 15 10" preserveAspectRatio="xMidYMid slice" focusable="false"><rect width="15" height="10" fill="#aa151b"/><rect y="2.5" width="15" height="5" fill="#f1bf00"/></svg>';
        } else {
            $svg = '<svg viewBox="0 0 60 30" preserveAspectRatio="xMidYMid slice" focusable="false"><clipPath id="zpl-flag-uk-t' . $id . '"><path d="M30,15h30v15zv15h-30zh-30v-15zv-15h30z"/></clipPath>'
                . '<rect width="60" height="30" fill="#012169"/><path d="M0,0L60,30M60,0L0,30" stroke="#fff" stroke-width="6"/>'
                . '<path d="M0,0L60,30M60,0L0,30" clip-path="url(#zpl-flag-uk-t' . $id . ')" stroke="#c8102e" stroke-width="4"/>'
                . '<path d="M30,0v30M0,15h60" stroke="#fff" stroke-width="10"/><path d="M30,0v30M0,15h60" stroke="#c8102e" stroke-width="6"/></svg>';
        }
        return '<span class="zpl-switch__flag zpl-switch__flag--' . $lang . '" aria-hidden="true">' . $svg . '</span>';
    }

    /**
     * Head script of every page. After a switch (sessionStorage zpl_vt, written by assets/zpl-switch.js) it turns
     * on the cross-fade between the two pages (View Transitions, where the browser has them) and puts the reader
     * back at the same place of the page. Nothing happens on any other page view.
     */
    public static function early(string $lang): string {
        $l = Lang::valid($lang) ? $lang : 'pl';
        // pagereveal can fire before deferred assets. Handle only this native transition's promises here.
        $guard = <<<'JS'
(function(w){
  if(w.ZPLTrackTransition)return;
  w.ZPLTrackTransition=function(e){
    var t=e&&e.viewTransition;
    if(!t)return;
    var active=true;
    function report(error){
      w.ZPLTransitionError=error;
      w.dispatchEvent(new CustomEvent('zpl-transition-error',{detail:{error:error}}));
    }
    function resize(){
      if(active&&typeof t.skipTransition==='function'){
        try{t.skipTransition();}catch(error){report(error);}
      }
    }
    function cleanup(){
      active=false;w.removeEventListener('resize',resize);
      // The opt-in belongs to this language switch only. Leaving it on the incoming page
      // starts another native transition on an ordinary link, whose destination never opted in.
      var style=w.document.getElementById('zpl-vt-css');
      if(style&&style.parentNode)style.parentNode.removeChild(style);
    }
    w.addEventListener('resize',resize,{passive:true});
    if(t.ready&&typeof t.ready.catch==='function')t.ready.catch(function(error){
      var name=error&&error.name,message=String(error&&error.message||'');
      if(name==='AbortError'||(name==='InvalidStateError'&&/(?:viewport.*(?:size|chang)|viewtransition opt-in disabled)/i.test(message)))return;
      report(error);
    });
    if(t.finished&&typeof t.finished.then==='function')t.finished.then(cleanup,cleanup);
    else cleanup();
  };
  w.addEventListener('pagereveal',w.ZPLTrackTransition);
})(window);
JS;
        return '<script id="zpl-vt">' . $guard . '(function(){try{var s=sessionStorage.getItem("zpl_vt");if(!s)return;sessionStorage.removeItem("zpl_vt");var v=JSON.parse(s);'
            . 'if(!v||Date.now()-v.t>20000||v.to!=="' . $l . '")return;var d=document,h=d.head||d.getElementsByTagName("head")[0];'
            . 'if(!(window.matchMedia&&matchMedia("(prefers-reduced-motion: reduce)").matches)&&window.CSS&&CSS.supports&&CSS.supports("view-transition-name: none")){'
            . 'var st=d.createElement("style");st.id="zpl-vt-css";st.textContent="@view-transition{navigation:auto}::view-transition-group(root){animation-duration:.3s}";h.appendChild(st);'
            . 'var l=d.createElement("link");l.rel="expect";l.href="#zpl-config";l.setAttribute("blocking","render");h.appendChild(l)}'
            . 'if(location.hash||v.src!==window.ZPLSourcePath||!(v.y>0))return;var done=0,go=function(e){if(done||(e&&e.type==="pagereveal"&&!d.getElementById("zpl-config")))return;done=1;'
            . 'var x=Math.max(0,d.documentElement.scrollHeight-innerHeight),y=v.r>0&&x>0?Math.round(v.r*x):v.y;if(y>0)window.scrollTo(0,y)};'
            . 'window.addEventListener("pagereveal",go);d.addEventListener("DOMContentLoaded",go)}catch(e){}})();</script>';
    }

    public static function inject(string $html, string $lang, string $source): string {
        // Only a real rendered switch counts (an attribute inside a tag) — inline scripts/CSS may mention the name.
        if (preg_match('~<[a-z][^<>]*\sdata-zpl-switch[\s=>]~i', $html)) { return $html; }
        $desktop = self::markup($lang, $source, 'nav');
        $done = false;
        // Desktop: before the divider that precedes the header CTA.
        $html = preg_replace_callback('~(<div\b[^>]*\bclass=["\'][^"\']*\bzpNewNav__actions\b[^>]*>)(.*?)(<span\b[^>]*\bclass=["\']zpNewNav__divider["\'][^>]*>\s*</span>\s*<a\b[^>]*\bzpNewNav__cta\b)~is', static function ($m) use ($desktop, &$done) {
            $done = true;
            return $m[1] . $m[2] . $desktop . $m[3];
        }, $html, 1) ?? $html;
        if (!$done) {
            $html = preg_replace('~(<a\b[^>]*\bclass=["\'][^"\']*\bzpNewNav__cta\b)~i', $desktop . '$1', $html, 1) ?? $html;
        }
        // Mobile drawer: at the top of the drawer body.
        $drawer = self::markup($lang, $source, 'drawer');
        $html = preg_replace('~(<div\b[^>]*\bclass=["\']zpNewNav__drawerBody["\'][^>]*>)~i', '$1' . $drawer, $html, 1) ?? $html;
        // Mobile bar: compact chip next to the burger.
        $chip = self::markup($lang, $source, 'chip');
        $html = preg_replace('~(<button\b[^>]*\bclass=["\'][^"\']*\bzpNewNav__burger\b)~i', $chip . '$1', $html, 1) ?? $html;
        return $html;
    }
}
