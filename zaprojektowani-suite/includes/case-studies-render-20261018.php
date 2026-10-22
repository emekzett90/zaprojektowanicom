<?php
/** Public case-study presentation. Routing, metadata and assets are owned by the caller. */
if (!defined('ABSPATH')) { exit; }

function zp_cs18_data(): array {
    static $data = null;
    if ($data === null) {
        $file = dirname(__DIR__) . '/content/case-studies-20261018.json';
        $decoded = is_readable($file) ? json_decode((string) file_get_contents($file), true) : null;
        $data = is_array($decoded) ? $decoded : [];
    }
    return $data;
}

function zp_cs18_url(string $path, string $lang = 'pl'): string {
    if ($path === '' || $path[0] === '#') { return $path; }
    $url = preg_match('~^https?://~i', $path) ? $path : home_url($path);
    return $lang === 'en' && class_exists('\\ZPL\\Router') ? \ZPL\Router::url($url, 'en') : $url;
}

function zp_cs18_asset(array $asset): string {
    if (!empty($asset['local'])) { return trailingslashit(ZP_SUITE_URL) . ltrim($asset['local'], '/'); }
    return (string) ($asset['url'] ?? '');
}

function zp_cs18_image(array $asset, string $alt, string $class = '', bool $eager = false): string {
    $src = zp_cs18_asset($asset);
    if (!$src) { return ''; }
    $sizes = '(max-width: 860px) calc(100vw - 48px), (max-width: 1520px) 46vw, 680px';
    $extra = '';
    if (preg_match('~/(marta|mateusz|stanislaw)_pointing\.webp$~', $src)) {
        $small = str_replace('_pointing.webp', '_pointing-680x1024.webp', $src);
        $extra = ' srcset="' . esc_attr($small . ' 680w, ' . $src . ' 1360w') . '"';
        $sizes = '(max-width: 760px) 84vw, (max-width: 1100px) 420px, 580px';
    }
    if (($asset['kind'] ?? '') === 'website_screenshot' && !empty($asset['srcset']) && is_array($asset['srcset'])) {
        $candidates = [];
        $narrow = null;
        foreach ($asset['srcset'] as $candidate) {
            if (!is_array($candidate) || empty($candidate['local']) || empty($candidate['width'])) { continue; }
            $candidates[] = zp_cs18_asset($candidate) . ' ' . (int) $candidate['width'] . 'w';
            if ($narrow === null || (int) $candidate['width'] < (int) $narrow['width']) { $narrow = $candidate; }
        }
        if ($candidates) { $extra = ' srcset="' . esc_attr(implode(', ', $candidates)) . '"'; }
        // Ahrefs 21.10.2026: src was the 3500px original (2.5 and 2.9 MB), flagged as an image too large. Browsers pick
        // a file from srcset and never load src next to it, so src is the narrowest copy (960px); the page is unchanged.
        if ($narrow !== null && ($small = zp_cs18_asset($narrow))) { $src = $small; }
        // Screenshots occupy one column of the site's standard 1750px content width.
        $sizes = '(max-width: 860px) calc(100vw - 40px), (max-width: 1846px) calc((100vw - 176px) / 2), 835px';
    }
    $dimensions = !empty($asset['width']) && !empty($asset['height']) ? ' width="' . (int) $asset['width'] . '" height="' . (int) $asset['height'] . '"' : '';
    return '<img class="' . esc_attr($class) . '" src="' . esc_url($src) . '" alt="' . esc_attr($alt) . '"' . $dimensions . $extra . ' sizes="' . esc_attr($sizes) . '" loading="' . ($eager ? 'eager' : 'lazy') . '" decoding="async"' . ($eager ? ' fetchpriority="high"' : '') . '>';
}

function zp_cs18_arrow(): string {
    return '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false"><path d="M6 18 18 6M6 6h12v12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>';
}

/** Existing project/client logos, kept distinct from technology marks. */
function zp_cs18_brand_logo(string $key, string $brand): string {
    $logos = [
        'siemianowski' => ['local' => 'assets/img/case-studies-20261018/siemianowski-brand-logo.png', 'width' => 2560, 'height' => 444],
        'apartament-piekna' => ['local' => 'assets/campaigns/apartament-logo.webp', 'width' => 1683, 'height' => 626],
        'altiora-linea' => ['local' => 'assets/img/case-studies-20261018/altiora-linea-brand-logo.png', 'width' => 868, 'height' => 208],
        'swiat-grilli' => ['local' => 'assets/img/case-studies-20261018/swiat-grilli-brand-logo.webp', 'width' => 1085, 'height' => 802],
    ];
    return isset($logos[$key]) ? '<div class="zpCS18__brandLogo zpCS18__brandLogo--' . esc_attr($key) . '">' . zp_cs18_image($logos[$key], $brand) . '</div>' : '';
}

function zp_cs18_client_logos(array $data, string $lang): string {
    $out = '<div class="zpCS18__heroFoot zpCS18__heroClientLogos">';
    foreach (['siemianowski', 'apartament-piekna', 'altiora-linea', 'swiat-grilli'] as $key) {
        if (function_exists('zp_cs18_case_public') && !zp_cs18_case_public($key)) { continue; }
        $case = $data[$key];
        $path = $lang === 'en' ? $case['en_path'] : $case['path'];
        $out .= '<a href="' . esc_url(zp_cs18_url($path, $lang)) . '" aria-label="' . esc_attr($case['brand'] . ' — case study') . '">' . zp_cs18_brand_logo($key, $case['brand']) . '</a>';
    }
    return $out . '<b>' . esc_html($lang === 'en' ? 'Four brands. Four different tasks.' : 'Cztery marki. Cztery różne zadania.') . '</b></div>';
}

function zp_cs18_button(string $text, string $path, string $lang, string $class = ''): string {
    return '<a class="zpCS18__button ' . esc_attr($class) . '" href="' . esc_url(zp_cs18_url($path, $lang)) . '">' . esc_html($text) . zp_cs18_arrow() . '</a>';
}

function zp_cs18_eyebrow(string $text): string {
    return '<p class="zpCS18__eyebrow"><span aria-hidden="true"></span>' . esc_html($text) . '</p>';
}

/** Accent a reviewed phrase while keeping every part of the heading escaped. */
function zp_cs18_heading(string $text, string $accent = ''): string {
    $position = $accent !== '' ? strpos($text, $accent) : false;
    if ($position === false) { return esc_html($text); }
    return esc_html(substr($text, 0, $position))
        . '<strong>' . esc_html($accent) . '</strong>'
        . esc_html(substr($text, $position + strlen($accent)));
}

function zp_cs18_prose(string $html, string $lang): string {
    $html = str_replace('\\"', '"', $html);
    $html = wp_kses_post($html);
    if ($lang === 'en') {
        $html = preg_replace_callback('~href=([\'"])(.*?)\1~i', static function ($m) use ($lang) {
            return 'href="' . esc_url(zp_cs18_url(html_entity_decode($m[2], ENT_QUOTES, 'UTF-8'), $lang)) . '"';
        }, $html);
    }
    return '<div class="zpCS18__prose">' . $html . '</div>';
}

function zp_cs18_tags(array $tags): string {
    $out = '<ul class="zpCS18__tags">';
    foreach ($tags as $tag) { $out .= '<li>' . esc_html($tag) . '</li>'; }
    return $out . '</ul>';
}

function zp_cs18_tech_logo(array $tech): string {
    $name = (string) ($tech['name'] ?? '');
    // Only original supplied brand assets are displayed as technology marks.
    if (empty($tech['logo']) && empty($tech['logo_url'])) { return ''; }
    $modifiers = ['WooCommerce'=>'woo', 'Elementor'=>'elementor', 'imoje / ING'=>'imoje', 'Google Ads'=>'google-ads', 'Google Tag Manager'=>'gtm', 'Google Analytics 4'=>'ga4', 'Google Search Console'=>'gsc', 'Meta Ads'=>'meta', 'PHP'=>'php', 'Cookie consent'=>'cookiebot', 'CAPTCHA'=>'recaptcha'];
    $modifier = isset($modifiers[$name]) ? ' zpCS18__techLogo--'.$modifiers[$name] : '';
    $asset = ['width'=>$tech['logo_width'] ?? 64, 'height'=>$tech['logo_height'] ?? 64];
    if ($name === 'imoje / ING' && empty($tech['logo']) && function_exists('zp_cs41_imoje_url')) {
        $asset['url'] = zp_cs41_imoje_url();
        return '<span class="zpCS41__providerLogo">' . zp_cs18_image($asset, 'imoje — płatności ING', 'zpCS18__techLogo'.$modifier) . '</span>';
    }
    if (!empty($tech['logo'])) { $asset['local']=$tech['logo']; }
    else { $asset['url']=$tech['logo_url']; }
    $alt = ['Cookie consent'=>'Cookiebot by Usercentrics', 'CAPTCHA'=>'reCAPTCHA'][$name] ?? $name;
    return zp_cs18_image($asset, $alt, 'zpCS18__techLogo'.$modifier);
}

function zp_cs18_tech_marks(array $case, string $lang, bool $offering = false, bool $hero = false): string {
    $label = $offering ? ($lang === 'en' ? 'Tools selected for each project' : 'Narzędzia dobierane do projektu') : ($lang === 'en' ? 'Project technologies' : 'Technologie projektu');
    $heroNames = ['WordPress', 'Elementor', 'Google Analytics 4', 'Google Search Console'];
    if (($case['key'] ?? '') === 'index') { $heroNames = ['WordPress', 'Elementor', 'WooCommerce', 'Google Analytics 4']; }
    elseif (($case['key'] ?? '') === 'apartament-piekna') { $heroNames = ['WordPress', 'Elementor', 'Google Analytics 4', 'Google Tag Manager']; }
    elseif (($case['key'] ?? '') === 'swiat-grilli') { $heroNames = ['WordPress', 'WooCommerce', 'imoje / ING', 'Google Analytics 4']; }
    $out = '<div class="zpCS18__techMarks' . ($hero ? ' zpCS49__heroTech' : '') . '" role="region" tabindex="0" aria-label="' . esc_attr($label) . '">';
    foreach (($case['technologies'] ?? []) as $tech) {
        if ($hero && !in_array($tech['name'], $heroNames, true)) { continue; }
        $logo = zp_cs18_tech_logo($tech);
        if ($logo === '') { continue; }
        $labels = ['Custom booking'=>'Autorski system wizyt', 'Administrator listings'=>'System ofert syndyka', 'Responsive layouts'=>'Wersja mobilna', 'Catalogues and contact'=>'Katalogi i kontakt', 'Custom systems'=>'Autorskie strony, sklepy i funkcje', 'Cookie consent'=>'Cookiebot by Usercentrics', 'CAPTCHA'=>'reCAPTCHA'];
        $labelText = $lang === 'pl' ? ($labels[$tech['name']] ?? $tech['name']) : $tech['name'];
        if (in_array($tech['name'], ['Cookie consent', 'CAPTCHA'], true)) { $labelText = $labels[$tech['name']]; }
        $out .= '<span class="zpCS48__techMark" role="img" tabindex="0" title="' . esc_attr($labelText) . '" aria-label="' . esc_attr($labelText) . '">' . $logo . '</span>';
    }
    return $out . '</div>';
}

function zp_cs18_figure(array $asset, string $lang, string $class = '', bool $eager = false): string {
    $alt = (string) ($asset['alt_' . $lang] ?? $asset['alt_pl'] ?? '');
    $caption = (string) ($asset['caption_' . $lang] ?? $asset['caption_pl'] ?? '');
    if (!empty($asset['transparent'])) { $class .= ' zpCS18__figure--transparent'; }
    if (!empty($asset['transparent']) && ($asset['kind'] ?? '') === 'project_mockup') { $class .= ' zpCS18__figure--floating'; }
    return '<figure class="zpCS18__figure ' . esc_attr($class) . '">' . zp_cs18_image($asset, $alt, '', $eager) . ($caption ? '<figcaption>' . esc_html($caption) . '</figcaption>' : '') . '</figure>';
}

/** The project list keeps all original marks in one accessible scrolling row. */
function zp_cs50_project_technologies(array $case, string $lang): string {
    $id = 'zpCaseTech-' . $case['key'];
    $marks = str_replace(' role="region"', ' id="' . esc_attr($id) . '" role="region"', zp_cs18_tech_marks($case, $lang));
    $hand = '<svg class="zpCS50__swipeHand" viewBox="0 0 28 28" fill="none" aria-hidden="true" focusable="false"><path d="M10 15V6a2 2 0 0 1 4 0v7-2a2 2 0 0 1 4 0v3-1a2 2 0 0 1 4 0v3-1a2 2 0 0 1 4 0v5c0 4-3 6-6 6h-4c-2 0-3-1-4-2l-6-7a2 2 0 0 1 3-3l3 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
    $label = $lang === 'en' ? 'Swipe through technologies' : 'Przesuń po technologie';
    $action = $lang === 'en' ? 'Scroll right to see all project technologies' : 'Przewiń w prawo, aby zobaczyć wszystkie technologie projektu';
    return '<div class="zpCS50__projectTech" data-zp-tech-stack><button type="button" class="zpCS50__techHint" data-zp-tech-next aria-controls="' . esc_attr($id) . '" aria-label="' . esc_attr($action) . '">' . $hand . '<span>' . esc_html($label) . '</span><span aria-hidden="true">→</span></button>' . $marks . '</div>';
}

function zp_cs18_card(array $case, array $card, int $number, string $lang): string {
    $path = $lang === 'en' ? $case['en_path'] : $case['path']; $en=$lang==='en';
    $out='<article id="case-'.$case['key'].'" class="zpCS18__project zpCS41__project"><a class="zpCS18__projectImage" href="'.esc_url(zp_cs18_url($path,$lang)).'">'.zp_cs18_image($case['gallery'][0],$case[$lang]['cover_alt']);
    if($case['key']==='altiora-linea'){$out.='<span class="zpCS41__new">NEW</span>';}
    $out.='</a><div class="zpCS18__projectCopy"><div class="zpCS41__projectBrand">'.zp_cs18_brand_logo($case['key'],$case['brand']).'<span>'.sprintf('%02d',$number).' / '.$case['brand'].'</span></div><h3>'.zp_cs18_heading($card['heading'], $card['heading_accent'] ?? '').'</h3><p>'.esc_html($card['text']).'</p>';
    if (!empty($card['detail'])) { $out.='<p>'.esc_html($card['detail']).'</p>'; }
    $out.='<ul class="zpCS41__deliverables" aria-label="'.esc_attr($en?'Selected deliverables':'Wybrane elementy wdrożenia').'">';
    foreach(array_slice($case[$lang]['scope'],0,3) as $item){$out.='<li><span aria-hidden="true">✓</span>'.esc_html($item).'</li>';}
    $out.='</ul>'.zp_cs50_project_technologies($case,$lang).'<a class="zpCS18__button" href="'.esc_url(zp_cs18_url($path,$lang)).'">'.esc_html($en?'Explore the project':'Poznaj projekt od środka').zp_cs18_arrow().'</a></div></article>';
    return $out;
}

/** Native form is intentionally not called while extracting content/translation units. */
function zp_cs18_callback(array $data, string $lang, bool $include_contact): string {
    // Only the native contact widget owns its avatars. No standalone team picture above it.
    $form = '';
    if ($include_contact) {
        if (!function_exists('zp_suite_quick_contact_section')) { return ''; }
        $form = zp_suite_quick_contact_section('case-studies');
        if (!$form) { return ''; }
    }
    $en = $lang === 'en';
    return '<section class="zpCS18__callback zpCS41__callback" aria-labelledby="zpCS18CallbackTitle"><div class="zpCS18__inner zpCS18__callbackIntro"><div>'.zp_cs18_eyebrow($en ? 'Your next project' : 'Twój kolejny projekt').'<h2 id="zpCS18CallbackTitle">'.zp_cs18_heading($en ? 'A similar challenge? Let’s discuss it.' : 'Masz podobne wyzwanie? Porozmawiajmy.', $en ? 'Let’s discuss it.' : 'Porozmawiajmy.').'</h2></div><p>'.esc_html($en ? 'Tell us what your visitors need to do and how your team will handle it. We will match the structure, content and functions to that process.' : 'Opowiedz, co klient ma zrobić na Twojej stronie i jak obsłuży to Twój zespół. Dobierzemy strukturę, treści i funkcje do tego procesu.').'</p></div>'.$form.'</section>';
}

function zp_cs18_studio(array $case, array $data, string $lang): string {
    $copy = $case[$lang]['studio'];
    $heading = zp_cs18_heading($copy['heading'], $copy['heading_accent'] ?? ($lang === 'pl' ? 'będzie właściwy?' : 'which scope is right?'));
    $out = '<section class="zpCS18__studio" aria-labelledby="zpCS18StudioTitle"><div class="zpCS18__inner zpCS18__studioGrid"><div class="zpCS18__studioPerson">' . zp_cs18_image($data['index']['team']['stanislaw'], $lang === 'en' ? 'Stanisław points towards the Quote Studio' : 'Stanisław wskazuje Studio Wyceny', 'zpCS18__teamFade');
    $out .= '<div class="zpCS18__pointedAction">' . zp_cs18_button($copy['button'], $copy['path'], $lang, 'zpCS18__button--light') . '</div></div><div>' . zp_cs18_eyebrow($lang === 'en' ? 'Quote Studio' : 'Studio Wyceny') . '<h2 id="zpCS18StudioTitle">' . $heading . '</h2><p>' . esc_html($copy['paragraph']) . '</p></div></div></section>';
    return $out;
}

function zp_cs18_faq(array $items, string $lang, array $data = []): string {
    $en = $lang === 'en';
    $person = $data['index']['team']['faq'] ?? ['local' => 'assets/img/home/mateusz-faq-560.webp', 'width' => 560, 'height' => 684];
    $out = '<section class="zpCS18__section zpCS18__faq"><div class="zpCS18__inner zpCS18__editorial"><div class="zpCS18__faqIntro"><figure class="zpCS18__faqPerson" aria-hidden="true">' . zp_cs18_image($person, '') . '</figure>' . zp_cs18_eyebrow($en ? 'Before your project' : 'Przed Twoim projektem') . '<h2>' . zp_cs18_heading($en ? 'From inspiration to specifics.' : 'Od inspiracji do konkretów.', $en ? 'to specifics.' : 'do konkretów.') . '</h2></div><div>';
    foreach ($items as $item) { $out .= '<details><summary>' . esc_html($item[0]) . '<span aria-hidden="true">+</span></summary><p>' . esc_html($item[1]) . '</p></details>'; }
    return $out . '</div></div></section>';
}

function zp_cs18_technology(array $case, string $lang): string {
    if (empty($case['technologies'])) { return ''; }
    $copy = $case[$lang]['technology'];
    $out = '<section class="zpCS18__section zpCS18__technology"><div class="zpCS18__inner"><div class="zpCS18__editorial"><div>' . zp_cs18_eyebrow($lang === 'en' ? 'Technology and function' : 'Technologia i funkcje') . '<h2>' . zp_cs18_heading($copy['heading'], $copy['heading_accent'] ?? '') . '</h2></div><p class="zpCS18__lead">' . esc_html($copy['intro']) . '</p></div><div class="zpCS18__technologyGrid">';
    foreach ($case['technologies'] as $tech) {
        $out .= '<div class="zpCS18__technologyItem">';
        $logo = zp_cs18_tech_logo($tech);
        if ($logo !== '') { $out .= '<div class="zpCS18__techVisual">' . $logo . '</div>'; }
        $out .= '<h3>' . esc_html($tech[$lang]['title']) . '</h3><p>' . esc_html($tech[$lang]['text']) . '</p></div>';
    }
    return $out . '</div></div></section>';
}

function zp_cs18_screenshots(array $case, string $lang): string {
    $remaining = array_slice($case['screenshots'] ?? [], 2);
    if (!$remaining) { return ''; }
    $en = $lang === 'en';
    $out = '<section class="zpCS18__section zpCS18__screenshots"><div class="zpCS18__inner"><div class="zpCS18__editorial"><div>' . zp_cs18_eyebrow($en ? 'A closer look' : 'Z bliska') . '<h2>' . zp_cs18_heading($en ? 'The website in use.' : 'Strona w działaniu.', $en ? 'in use.' : 'w działaniu.') . '</h2></div><p class="zpCS18__lead">' . esc_html($en ? 'Selected views from the public website show the information structure and interface in their real context.' : 'Wybrane widoki publicznej strony pokazują strukturę informacji i interfejs w rzeczywistym kontekście.') . '</p></div>';
    foreach ($remaining as $shot) { $out .= zp_cs18_figure($shot, $lang, 'zpCS18__screenFigure'); }
    return $out . '</div></section>';
}

/** Real client photography and UI captures are kept distinct and interleaved with the narrative. */
function zp_cs18_visual_band(array $asset, string $lang, bool $screen = false): string {
    $portrait = !empty($asset['height']) && !empty($asset['width']) && $asset['height'] > $asset['width'] * 1.15;
    $class = $screen ? 'zpCS18__screenFigure' : 'zpCS18__referenceFigure' . ($portrait ? ' zpCS18__referenceFigure--portrait' : '') . (!empty($asset['fade_bottom']) ? ' zpCS18__referenceFigure--fade' : '');
    $context = $asset['context_' . $lang] ?? [];
    $caption = (string) ($asset['caption_' . $lang] ?? $asset['alt_' . $lang] ?? '');
    $title = (string) ($context['heading'] ?? ($lang === 'en' ? ($screen ? 'A closer look at the interface.' : 'Details that support the story.') : ($screen ? 'Rozwiązanie w praktyce.' : 'Materiały projektu.')));
    $copy = '<p class="zpCS18__lead">' . esc_html($context['text'] ?? $caption) . '</p>';
    foreach (($context['paragraphs'] ?? []) as $paragraph) { $copy .= '<p class="zpCS18__lead">' . esc_html($paragraph) . '</p>'; }
    $figure = zp_cs18_figure($asset, $lang, $class);
    $label = $lang === 'en' ? ($screen ? 'Selected website view' : 'Project materials') : ($screen ? 'Wybrany widok strony' : 'Materiały realizacji');
    return '<section class="zpCS18__galleryBand zpCS18__galleryBand--editorial' . (!$screen ? ' zpCS18__galleryBand--clientPhoto' : '') . '"><div class="zpCS18__inner zpCS18__visualPair">' . $figure . '<div>' . zp_cs18_eyebrow($label) . '<h2>' . zp_cs18_heading($title, $context['heading_accent'] ?? '') . '</h2>' . $copy . '</div></div></section>';
}

/** The index passes its filtered public cards, so the next anchor always exists. */
function zp_cs18_continuation(array $cards, int $shown, string $lang): string {
    $total = count($cards);
    $remaining = $total - $shown;
    if ($shown < 1 || $remaining < 1 || empty($cards[$shown]['key'])) { return ''; }
    if ($lang === 'en') {
        $label = sprintf('There’s more — %d more %s below', $remaining, $remaining === 1 ? 'project' : 'projects');
    } elseif ($remaining === 1) {
        $label = 'To jeszcze nie wszystko — poniżej jeszcze 1 realizacja';
    } elseif ($remaining % 10 >= 2 && $remaining % 10 <= 4 && ($remaining % 100 < 12 || $remaining % 100 > 14)) {
        $label = sprintf('To jeszcze nie wszystko — poniżej kolejne %d realizacje', $remaining);
    } else {
        $label = sprintf('To jeszcze nie wszystko — poniżej kolejnych %d realizacji', $remaining);
    }
    return '<a class="zpCS18__continuation" href="' . esc_url('#case-' . $cards[$shown]['key']) . '">'
        . '<span class="zpCS18__continuationCount" aria-hidden="true">' . esc_html(sprintf('%02d / %02d', $shown, $total)) . '</span>'
        . '<span class="zpCS18__continuationText">' . esc_html($label) . '</span>' . zp_cs18_arrow() . '</a>';
}

function zp_cs18_index(array $data, string $lang, bool $include_contact): string {
    $case=$data['index'];$c=$case[$lang];$en=$lang==='en';
    $cards = [];
    foreach ($c['cards'] as $card) {
        $key = (string) ($card['key'] ?? '');
        if (!isset($data[$key]) || (function_exists('zp_cs18_case_public') && !zp_cs18_case_public($key))) { continue; }
        $cards[] = $card;
    }
    $out='<section class="zpCS18__hero zpCS41__indexHero"><div class="zpCS18__inner"><div class="zpCS18__heroGrid"><div class="zpCS18__heroCopy">'.zp_cs18_eyebrow($c['eyebrow']).'<h1>';
    foreach($c['hero_lines'] as $i=>$line){$out.=($i===2?'<strong>'.esc_html($line).'</strong>':esc_html($line)).($i<2?'<br> ':'');}
    $out.='</h1><p>'.esc_html($c['hero_lead']).'</p><div class="zpCS18__heroActions">'.zp_cs18_button($en?'Explore the projects':'Poznaj realizacje','#realizacje',$lang,'zpCS18__button--light').zp_cs18_button($en?'Discuss your project':'Omów swój projekt','#szybki-kontakt',$lang,'zpCS18__button--outline').'</div>'.zp_cs18_tech_marks($case,$lang,true,true).'</div><div class="zpCS18__heroPerson">'.zp_cs18_image($case['team']['mateusz'],$en?'Mateusz from the Zaprojektowani team':'Mateusz z zespołu Zaprojektowani','zpCS18__teamFade',true).'</div></div>'.zp_cs18_client_logos($data,$lang).'</div></section>';
    $out.='<section class="zpCS18__section zpCS41__overview"><div class="zpCS18__inner"><div class="zpCS18__editorial"><div>'.zp_cs18_eyebrow($en?'Find your challenge':'Znajdź swoje wyzwanie').'<h2>'.zp_cs18_heading($en?'Different businesses. Specific solutions.':'Różne firmy. Konkretne rozwiązania.', $en?'Specific solutions.':'Konkretne rozwiązania.').'</h2></div><p class="zpCS18__lead">'.esc_html($en?'See how a project connects the brand, content and the action a visitor needs to take. Choose the scope closest to your business.':'Zobacz, jak projekt łączy markę, treści i działanie, które ma wykonać klient. Wybierz zakres najbliższy Twojej firmie.').'</p></div><nav id="zpCaseChoices" class="zpCS41__choices" aria-label="'.esc_attr($en?'Choose a project type':'Wybierz typ realizacji').'">';
    $choices=[['siemianowski','Administrator listings','Serwis i system ofert','Website and listings','Usługi prawne + publikacja ofert','Legal services + publishing listings'],['apartament-piekna','Custom booking','Marka i wizyty','Brand and appointments','Beauty + własny proces zapisu','Beauty + a bespoke booking flow'],['altiora-linea','PL / EN / IT','Katalog w trzech językach','A trilingual catalogue','Marki wnętrzarskie + współpraca B2B','Interior brands + B2B cooperation'],['swiat-grilli','imoje / ING','Kompletny e-commerce','Complete e-commerce','Produkty + koszyk + płatności','Products + cart + payments']];
    foreach($choices as $i=>$q){if(function_exists('zp_cs18_case_public')&&!zp_cs18_case_public($q[0])){continue;}$out.='<a href="#case-'.esc_attr($q[0]).'">'.zp_refine_tech_icon_3240($q[1]).'<span><strong>'.esc_html($q[$en?3:2]).'</strong><small>'.esc_html($q[$en?5:4]).'</small></span>'.zp_cs18_arrow().'</a>';}
    $out.='</nav><div class="zpCS46__railControls"><span>'.esc_html($en?'Swipe right to explore more projects':'Przesuń w prawo — zobacz kolejne projekty').'</span><button type="button" data-zp-cases-prev aria-controls="zpCaseChoices" aria-label="'.esc_attr($en?'Scroll left':'Przesuń w lewo').'">←</button><button type="button" data-zp-cases-next aria-controls="zpCaseChoices" aria-label="'.esc_attr($en?'Scroll right':'Przesuń w prawo').'">→</button></div></div></section><section id="realizacje" class="zpCS18__section zpCS18__projects"><div class="zpCS18__inner">';
    foreach($cards as $i=>$card){
        $out.=zp_cs18_card($data[$card['key']],$card,$i+1,$lang);

    }
    $out.='<div class="zpCS18__portfolioLink"><p>'.esc_html($en?'Looking for more examples? Browse our full website and branding portfolio.':'Szukasz innych przykładów? Zobacz pozostałe strony, sklepy i identyfikacje w naszym portfolio.').'</p>'.zp_cs18_button($en?'Full portfolio':'Wszystkie realizacje','/realizacje/',$lang,'zpCS18__button--inkOutline').'</div></div></section>';
    $process='';
            $process.='<section class="zpCS18__section zpCS41__tone zpCS18__toneWithPerson"><div class="zpCS18__inner"><div class="zpCS18__editorial"><div class="zpCS18__toneIntro"><figure class="zpCS18__tonePerson" aria-hidden="true">'.zp_cs18_image($case['team']['laptop'],'').'</figure>'.zp_cs18_eyebrow($en?'How we work':'Sposób pracy').'<h2>'.zp_cs18_heading($en?'A visual direction is only the beginning.':'Kierunek wizualny to dopiero początek.', $en?'only the beginning.':'dopiero początek.').'</h2></div>'.zp_cs18_prose($c['sections'][0][2],$lang).'</div><div class="zpCS41__principles">';
            $principles=$en?[['01','Understand','First, define the audience, offer and way the business serves customers.'],['02','Design','Connect the brand, content hierarchy and the next action in one interface.'],['03','Implement','Choose the publishing system and custom functions around that process.']]:[['01','Zrozumieć','Najpierw odbiorcy, oferta i sposób obsługi klienta w firmie.'],['02','Zaprojektować','Marka, hierarchia treści i następny krok w jednym interfejsie.'],['03','Wdrożyć','System publikacji i własne funkcje dobrane do tego procesu.']];
            foreach($principles as $p){$process.='<div><span>'.$p[0].'</span><h3>'.esc_html($p[1]).'</h3><p>'.esc_html($p[2]).'</p></div>';}
            $process.='</div></div></section>';
    $out.=zp_cs18_callback($data,$lang,$include_contact).zp_cs18_technology($case,$lang).$process.zp_cs18_faq($c['faq'],$lang,$data);
    return $out;
}


/** Extract the existing full-width step group; do not render the duplicated journey. */
function zp_cs41_split_steps(string $html): array {
    $pattern='~<div class="zpArticleNew__steps">.*?</div>~s';
    if(preg_match($pattern,$html,$m)){return [str_replace($m[0],'',$html),$m[0]];}
    return [$html,''];
}
function zp_cs41_chapter_media(array $case, string $section): array {
    $maps=[
        'siemianowski'=>['branding'=>['gallery',1],'strona'=>['gallery',2],'system-syndyka'=>['screenshots',0],'wdrozenie'=>['gallery',3]],
        'apartament-piekna'=>['branding'=>['gallery',1],'strona'=>['reference_photos',0],'wlasne-rezerwacje'=>['screenshots',0]],
        'altiora-linea'=>['projekt'=>['reference_photos',0],'struktura'=>['screenshots',0],'katalogi'=>['reference_photos',1]],
        'swiat-grilli'=>['branding'=>['gallery',1],'kategorie'=>['screenshots',0],'produkty'=>['reference_photos',1]],
    ];
    $ref=$maps[$case['key']][$section]??null;
    return $ref?($case[$ref[0]][$ref[1]]??[]):[];
}
function zp_cs41_chapter_figure(array $asset,string $lang): string {
    $screen=($asset['kind']??'')==='website_screenshot';
    $portrait = !$screen && !empty($asset['height']) && !empty($asset['width']) && $asset['height'] > $asset['width'] * 1.15;
    $figure_class = $screen ? 'zpCS18__screenFigure' : 'zpCS18__referenceFigure';
    if ($portrait) { $figure_class .= ' zpCS41__portrait'; }
    $out=zp_cs18_figure($asset,$lang,$figure_class);
    if($screen){$out.='<a class="zpCS41__enlarge" href="'.esc_url(zp_cs18_asset($asset)).'" target="_blank" rel="noopener">'.esc_html($lang==='en'?'Open the full interface view':'Otwórz pełny widok interfejsu').zp_cs18_arrow().'</a>';}
    return '<div class="zpCS41__media">'.$out.'</div>';
}

function zp_cs18_detail(array $case, array $data, string $lang, bool $include_contact): string {
    $c=$case[$lang];$en=$lang==='en';$sections=$c['sections'];
    $out='<section class="zpCS18__hero zpCS18__hero--detail"><div class="zpCS18__inner"><div class="zpCS18__heroGrid"><div class="zpCS18__heroCopy">'.zp_cs18_eyebrow($case['brand'].' / case study').'<h1>';
    foreach($c['hero_lines'] as $i=>$line){$out.=($i===0?'<strong>'.esc_html($line).'</strong>':esc_html($line)).($i<count($c['hero_lines'])-1?'<br> ':'');}
    $out.='</h1><p>'.esc_html($c['lead']).'</p><a class="zpCS18__liveLink" href="'.esc_url($case['live']).'" target="_blank" rel="noopener">'.esc_html(preg_replace('~^https?://|/$~','',$case['live'])).zp_cs18_arrow().'</a>'.zp_cs18_tech_marks($case,$lang,false,true).'</div><div class="zpCS18__heroPerson"><div class="zpCS18__heroClientMark" aria-hidden="true">'.zp_cs18_brand_logo($case['key'],$case['brand']).'</div>'.zp_cs18_image($case['team'][$case['hero_team']],$en?'The Zaprojektowani project team':'Zespół projektowy Zaprojektowani','zpCS18__teamFade',true).'<div class="zpCS18__pointedAction">'.zp_cs18_button($en?'View the project':'Zobacz projekt','#projekt-wizualizacja',$lang,'zpCS18__button--light').'</div></div></div></div></section>';
    if(function_exists('zp_cs41_owner_review')){$out.=zp_cs41_owner_review($case['key'],$lang);}
    $out.='<section id="projekt-wizualizacja" class="zpCS18__section zpCS18__cover"><div class="zpCS18__inner"><div class="zpCS41__chapterGrid">'.zp_cs41_chapter_figure($case['gallery'][0],$lang).'<div class="zpCS41__chapterCopy">'.zp_cs18_eyebrow($en?'01 / Context and scope':'01 / Kontekst i zakres').'<h2>'.zp_cs18_heading($sections[0][1], $c['heading_accents'][$sections[0][0]] ?? '').'</h2>'.zp_cs18_prose($sections[0][2],$lang).'</div></div><div id="zakres" class="zpCS18__scope"><div><h3>'.esc_html($en?'Client':'Klient').'</h3><p>'.esc_html($case['brand']).'</p>'.zp_cs18_tags($c['tags']).'</div><div><h3>'.esc_html($en?'Delivered scope':'Zakres realizacji').'</h3><ul>';
    foreach($c['scope'] as $scope){$out.='<li>'.esc_html($scope).'</li>';}
    $out.='</ul></div></div></div></section>';
    $last=count($sections)-1;
    $has_content_steps = strpos(implode('', array_column($sections, 2)), 'zpArticleNew__steps') !== false;
    foreach($sections as $i=>$section){
        if($i===0||$i===$last){continue;}
        if($case['key']==='altiora-linea'&&$section[0]==='jezyki'&&function_exists('zp_refine_altiora_languages_3240')){
            $out.=zp_refine_altiora_languages_3240($lang);continue;
        }
        [$prose,$steps]=zp_cs41_split_steps($section[2]);
        $media=zp_cs41_chapter_media($case,$section[0]);$dark=$i%3===0;
        $out.='<section id="cs-'.esc_attr($section[0]).'" class="zpCS18__section zpCS41__chapter'.($dark?' zpCS41__tone':'').'"><div class="zpCS18__inner"><div class="'.($media?'zpCS41__chapterGrid':'zpCS18__editorial').'">';
        $heading=zp_cs18_eyebrow(sprintf('%02d',$i+1).' / '.($en?'Inside the project':'Wewnątrz projektu')).'<h2>'.zp_cs18_heading($section[1], $c['heading_accents'][$section[0]] ?? '').'</h2>';
        if($media){$out.=zp_cs41_chapter_figure($media,$lang).'<div class="zpCS41__chapterCopy">'.$heading.zp_cs18_prose($prose,$lang).'</div>';}
        else{$out.='<div>'.$heading.'</div>'.zp_cs18_prose($prose,$lang);}
        $out.='</div>';
        if($steps){$out.='<div class="zpCS41__stepsWrap">'.wp_kses_post($steps).'</div>';}
        elseif($i===3&&!$has_content_steps&&!empty($c['journey'])){$out.='<ol class="zpCS18__journey">';foreach($c['journey'] as $n=>$step){$out.='<li class="zpCS18__journeyCard"><span>'.sprintf('%02d',$n+1).'</span><h3>'.esc_html($step[0]).'</h3><p>'.esc_html($step[1]).'</p></li>';}$out.='</ol>';}
        $out.='</div></section>';
    }
    $out.=zp_cs18_callback($data,$lang,$include_contact).zp_cs18_technology($case,$lang);
    $out.='<section class="zpCS18__section zpCS18__outcomes"><div class="zpCS18__inner"><div class="zpCS18__editorial"><div>'.zp_cs18_eyebrow($en?'Delivered work':'Efekt prac').'<h2>'.zp_cs18_heading($sections[$last][1], $c['heading_accents'][$sections[$last][0]] ?? '').'</h2></div>'.zp_cs18_prose($sections[$last][2],$lang).'</div><div class="zpCS18__outcomeGrid">';
    foreach($c['outcomes'] as $item){$out.='<div><span class="zpCS18__check" aria-hidden="true">✓</span><h3>'.esc_html($item[0]).'</h3><p>'.esc_html($item[1]).'</p></div>';}
    $out.='</div></div></section>'.zp_cs18_faq($c['faq'],$lang,$data);
    $out.='<section class="zpCS18__section zpCS41__next"><div class="zpCS18__inner zpCS18__nextGrid"><figure class="zpCS18__nextPerson" aria-hidden="true">'.zp_cs18_image($data['index']['team']['pencil'],'').'</figure><div class="zpCS18__nextCopy">'.zp_cs18_eyebrow($en?'Next step':'Następny krok').'<h2>'.zp_cs18_heading($en?'A similar scope. Your own direction.':'Podobny zakres. Twój własny kierunek.', $en?'Your own direction.':'Twój własny kierunek.').'</h2><p>'.esc_html($en?'Compare the projects or describe what you need for your own business.':'Porównaj realizacje albo opisz, czego potrzebujesz dla swojej firmy.').'</p></div><div class="zpCS18__heroActions zpCS18__nextActions">'.zp_cs18_button($en?'All case studies':'Wszystkie case studies','/case-studies/',$lang,'zpCS18__button--outline').zp_cs18_button($en?'Get a quote':'Wyceń swój projekt','/studio-wyceny/',$lang,'zpCS18__button--light').'</div></div></section>';
    return $out;
}

function zp_cs18_render(string $key, string $lang = 'pl', bool $include_contact = true): string {
    $data = zp_cs18_data();
    if (!isset($data[$key])) { return ''; }
    $lang = $lang === 'en' ? 'en' : 'pl';
    $body = $key === 'index' ? zp_cs18_index($data, $lang, $include_contact) : zp_cs18_detail($data[$key], $data, $lang, $include_contact);
    return '<div class="zpCS18 zpCS41 zpCS18--' . esc_attr($key) . '" data-zp-cs18="' . esc_attr($key) . '" lang="' . esc_attr($lang) . '">' . $body . '</div>';
}
