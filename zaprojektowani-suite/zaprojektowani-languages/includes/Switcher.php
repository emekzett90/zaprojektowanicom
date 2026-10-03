<?php
namespace ZPL;

if (!defined('ABSPATH')) { exit; }

/** Server-rendered PL/EN switch (so it is visible at first paint and crawlable). */
final class Switcher {
    public static function markup(string $lang, string $source, string $variant): string {
        $pl = esc_url(home_url($source));
        $en = esc_url(home_url(Router::en_path($source)));
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

    /** Round flag (inline SVG, cropped to a circle by CSS). */
    private static function flag(string $lang): string {
        if ($lang === 'pl') {
            $svg = '<svg viewBox="0 0 16 10" preserveAspectRatio="xMidYMid slice" focusable="false"><rect width="16" height="5" fill="#fff"/><rect y="5" width="16" height="5" fill="#dc143c"/></svg>';
        } else {
            $svg = '<svg viewBox="0 0 60 30" preserveAspectRatio="xMidYMid slice" focusable="false"><clipPath id="zpl-flag-uk-t"><path d="M30,15h30v15zv15h-30zh-30v-15zv-15h30z"/></clipPath>'
                . '<rect width="60" height="30" fill="#012169"/><path d="M0,0L60,30M60,0L0,30" stroke="#fff" stroke-width="6"/>'
                . '<path d="M0,0L60,30M60,0L0,30" clip-path="url(#zpl-flag-uk-t)" stroke="#c8102e" stroke-width="4"/>'
                . '<path d="M30,0v30M0,15h60" stroke="#fff" stroke-width="10"/><path d="M30,0v30M0,15h60" stroke="#c8102e" stroke-width="6"/></svg>';
        }
        return '<span class="zpl-switch__flag zpl-switch__flag--' . $lang . '" aria-hidden="true">' . $svg . '</span>';
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
