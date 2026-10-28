<?php
/**
 * Service FAQ: the same Mateusz cut-out as the home trust-logo widget, plus a few
 * practical questions missing from the reviewed service FAQs. No database writes.
 * Runs through zp_service_widgets_document after PL/EN translation and before the
 * existing single-source FAQ schema synchronizer. All unrelated markup is retained.
 */
if (!defined('ABSPATH')) { exit; }

/** The actual home widget source is templates/trust-logos.php:175-181, not the older FAQ portrait. */
function zp_services_faq_person_html(): string {
    $url = 'https://zaprojektowani.com/wp-content/uploads/2026/05/mateusz_nowy.webp';
    return '<figure class="faq-figure zpServiceFaqPerson" data-zp-service-faq-person="1" aria-hidden="true">'
        . '<img src="' . esc_url($url) . '" alt="" width="1042" height="1729" loading="lazy" decoding="async">'
        . '</figure>';
}

function zp_services_faq_person_css(): string {
    // A proportional crop retains his face and open hand, omits the lower legs and reserves
    // the complete figure space before its lazy image loads. Existing hero/team CSS
    // and per-industry artwork outside the FAQ remain untouched.
    $roots = '#faq,#zpFaqShopKatNavy,#zpFaqLogoKatNavy,#zpFaqKatNavy,#zpHomeFaqKatNavy';
    $figures = []; $images = []; $intros = [];
    foreach (explode(',', $roots) as $root) {
        $figures[] = 'html body ' . $root . ' .zpServiceFaqPerson';
        $images[] = 'html body ' . $root . ' .zpServiceFaqPerson img';
        $intros[] = 'html body ' . $root . ' .' . ($root === '#faq' ? 'faq-intro' : substr($root, 1) . '__copy');
    }
    $figure = implode(',', $figures); $image = implode(',', $images); $intro = implode(',', $intros);
    // Keep portrait and heading together at the grid start, without the old sticky
    // 130px inset, bottom alignment or reveal translation lowering the whole column.
    return $intro . '{align-self:start!important;position:relative!important;top:auto!important;margin-top:0!important;padding-top:0!important;transform:none!important}'
        . $figure . '{display:block!important;position:relative!important;float:none!important;inset:auto!important;'
        . 'width:481.39px!important;max-width:100%!important;height:auto!important;aspect-ratio:14/15!important;margin:0 0 24px!important;'
        . 'padding:0!important;overflow:hidden!important;pointer-events:none;transform:none!important;'
        . '-webkit-mask-image:linear-gradient(to bottom,#000 84%,transparent 100%);mask-image:linear-gradient(to bottom,#000 84%,transparent 100%)}'
        . $image . '{display:block!important;position:static!important;inset:auto!important;width:100%!important;height:100%!important;'
        . 'max-width:none!important;object-fit:cover!important;object-position:center top!important;transform:none!important;'
        . 'margin:0!important;padding:0!important;filter:none!important;-webkit-mask-image:none!important;mask-image:none!important}'
        . '@media(max-width:760px){' . $figure . '{width:378.235px!important;aspect-ratio:44/49!important;margin-bottom:20px!important}}';
}

/** Attribute parser with browser-like first-value handling; no dependency on CSS optimization being enabled. */
function zp_services_faq_attributes(string $tag): array {
    $attrs = [];
    if (preg_match_all('~\s([^\s=/>"\']+)\s*(?:=\s*(?:"([^"]*)"|\'([^\']*)\'|([^\s>"\']+)))?~', $tag, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $match) {
            $name = strtolower($match[1]);
            if (!array_key_exists($name, $attrs)) {
                $attrs[$name] = html_entity_decode(($match[2] ?? '') . ($match[3] ?? '') . ($match[4] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }
        }
    }
    return $attrs;
}

/** Reuse the HTML-aware scanner for fragments, so examples inside scripts/templates are never markup targets. */
function zp_services_faq_fragment_skip(string $fragment): ?array {
    $prefix = '<!doctype html><html><head></head><body>';
    $scan = zp_speed_scan($prefix . $fragment . '</body></html>');
    if (!$scan) { return null; }
    $skip = [];
    foreach ($scan['skip'] as $range) { $skip[] = [$range[0] - strlen($prefix), $range[1] - strlen($prefix)]; }
    return $skip;
}

/** Return a real element opening tag with this exact class, never a partial class-name match. */
function zp_services_faq_class_open(string $html, string $tag, string $class): ?array {
    if (!preg_match_all('~<' . preg_quote($tag, '~') . '(?=[\s/>])~i', $html, $matches, PREG_OFFSET_CAPTURE)) { return null; }
    $skip = zp_services_faq_fragment_skip($html);
    if ($skip === null) { return null; }
    $found = [];
    foreach ($matches[0] as $match) {
        $at = $match[1]; $end = zp_speed_tag_end($html, $at);
        if (zp_speed_in_skip($skip, $at)) { continue; }
        if ($end < 0) { return null; }
        $attrs = zp_services_faq_attributes(substr($html, $at, $end + 1 - $at));
        if (in_array($class, preg_split('~\s+~', trim($attrs['class'] ?? '')) ?: [], true)) { $found[] = [$at, $end + 1]; }
    }
    return count($found) === 1 ? $found[0] : null;
}

/** Remove only a complete, known figure wrapper; malformed/ambiguous markup is left alone. */
function zp_services_faq_remove_figure(string $fragment, string $tag, string $class): string {
    $range = zp_services_faq_class_open($fragment, $tag, $class);
    if (!$range) { return $fragment; }
    $end = zp_speed_nested_end($fragment, $tag, $range[1]);
    return $end > 0 ? substr($fragment, 0, $range[0]) . substr($fragment, $end) : $fragment;
}

function zp_services_faq_normalized_questions(array $items): string {
    return implode("\n", array_map(static function ($item) {
        return trim((string) preg_replace('~[\s\x{00a0}]+~u', ' ', (string) ($item['name'] ?? '')));
    }, $items));
}

/** Append only to the exact reviewed question set; owner-edited questions are never rebuilt. */
function zp_services_faq_add_missing(string $fragment, string $source, bool $english): string {
    $entry = zp_services_faq_additions_20261015()[$source][$english ? 'en' : 'pl'] ?? null;
    if (!$entry || !function_exists('zp_seo_service_faq_items') || strpos($fragment, 'data-zp-faq-extra="20261015"') !== false) { return $fragment; }
    $items = zp_seo_service_faq_items($fragment);
    if (!is_array($items) || count($items) !== $entry['count']
        || !hash_equals($entry['questions_sha256'], hash('sha256', zp_services_faq_normalized_questions($items)))) { return $fragment; }
    foreach ($items as $item) { if (($item['name'] ?? '') === $entry['question']) { return $fragment; } }
    $shop = strpos($fragment, 'id="zpFaqShopKatNavy"') !== false;
    $tag = $shop ? 'article' : 'details'; $class = $shop ? 'zpFaqShopKatNavy__item' : 'faq-item';
    if (!preg_match_all('~<' . $tag . '(?=[\s/>])~i', $fragment, $matches, PREG_OFFSET_CAPTURE)) { return $fragment; }
    $skip = zp_services_faq_fragment_skip($fragment);
    if ($skip === null) { return $fragment; }
    $end = -1;
    foreach ($matches[0] as $match) {
        $start = $match[1]; $gt = zp_speed_tag_end($fragment, $start);
        if (zp_speed_in_skip($skip, $start)) { continue; }
        if ($gt < 0) { return $fragment; }
        $attrs = zp_services_faq_attributes(substr($fragment, $start, $gt + 1 - $start));
        if (!in_array($class, preg_split('~\s+~', trim($attrs['class'] ?? '')) ?: [], true)) { continue; }
        $close = zp_speed_nested_end($fragment, $tag, $gt + 1);
        if ($close < 0) { return $fragment; }
        $end = max($end, $close);
    }
    if ($end < 0) { return $fragment; }
    $number = esc_html(str_pad((string) (count($items) + 1), 2, '0', STR_PAD_LEFT));
    $q = esc_html($entry['question']); $a = esc_html($entry['answer']);
    $new = $shop
        ? '<article class="zpFaqShopKatNavy__item" data-zp-faq-extra="20261015"><button class="zpFaqShopKatNavy__question" type="button" aria-expanded="false"><span>' . $number . '</span><strong>' . $q . '</strong><i data-lucide="plus"></i></button><div class="zpFaqShopKatNavy__answer"><p>' . $a . '</p></div></article>'
        : '<details class="faq-item" data-zp-faq-extra="20261015"><summary><span class="faq-no">' . $number . '</span><span>' . $q . '</span><i class="faq-plus" aria-hidden="true"></i></summary><div class="faq-answer">' . $a . '</div></details>';
    return substr($fragment, 0, $end) . $new . substr($fragment, $end);
}

/** Pure transform used by the public-request filter and by offline regression tests. */
function zp_services_faq_render_document(string $html, string $source, bool $english): string {
    if (strlen($html) > 8000000 || !preg_match('//u', $html) || stripos($html, '</head>') === false || !function_exists('zp_speed_scan')) { return $html; }
    $scan = zp_speed_scan($html);
    if (!$scan) { return $html; }
    if (!preg_match_all('~<section(?=[\s/>])~i', $html, $matches, PREG_OFFSET_CAPTURE)) { return $html; }
    $sections = [];
    foreach ($matches[0] as $match) {
        $at = $match[1];
        if (zp_speed_in_skip($scan['skip'], $at)) { continue; }
        $gt = zp_speed_tag_end($html, $at);
        if ($gt < 0) { return $html; }
        $open = substr($html, $at, $gt + 1 - $at); $attrs = zp_services_faq_attributes($open);
        if (!in_array($attrs['id'] ?? '', ['faq', 'zpFaqShopKatNavy', 'zpFaqLogoKatNavy', 'zpFaqKatNavy', 'zpHomeFaqKatNavy'], true)) { continue; }
        $end = zp_speed_nested_end($html, 'section', $gt + 1);
        if ($end < 0) { return $html; }
        $sections[] = [$at, $end, $attrs['id']];
    }
    // The service/home templates have exactly one FAQ, unlike the separate FAQ directory.
    if (count($sections) !== 1) { return $html; }
    [$at, $end, $id] = $sections[0]; $fragment = substr($html, $at, $end - $at);
    if (strpos($fragment, 'data-zp-service-faq-person="1"') === false) {
        $introClass = $id === 'faq' ? 'faq-intro' : $id . '__copy';
        $intro = zp_services_faq_class_open($fragment, 'div', $introClass);
        if (!$intro) { return $html; }
        $introEnd = zp_speed_nested_end($fragment, 'div', $intro[1]);
        if ($introEnd < 0 || stripos(substr($fragment, $intro[1], $introEnd - $intro[1]), '<h2') === false) { return $html; }
        $introHtml = substr($fragment, $intro[0], $introEnd - $intro[0]);
        $oldFigure = zp_services_faq_class_open($introHtml, 'div', 'faq-figure');
        if ($oldFigure) {
            $withoutFigure = zp_services_faq_remove_figure($introHtml, 'div', 'faq-figure');
            if ($withoutFigure === $introHtml) { return $html; }
            $introHtml = $withoutFigure;
        }
        $introOpenEnd = zp_speed_tag_end($introHtml, 0);
        if ($introOpenEnd < 0) { return $html; }
        $introHtml = substr($introHtml, 0, $introOpenEnd + 1) . zp_services_faq_person_html() . substr($introHtml, $introOpenEnd + 1);
        $fragment = substr($fragment, 0, $intro[0]) . $introHtml . substr($fragment, $introEnd);
        // Home already had a different desktop-only portrait next to the side card.
        if ($id === 'zpHomeFaqKatNavy') { $fragment = zp_services_faq_remove_figure($fragment, 'figure', 'zpHomeFaqKatNavy__person'); }
    }
    $fragment = zp_services_faq_add_missing($fragment, $source, $english);
    $result = substr($html, 0, $at) . $fragment . substr($html, $end);
    if (strpos($result, 'id="zp-service-faq-person-css"') === false) {
        $head = stripos($result, '</head>');
        $result = substr($result, 0, $head) . '<style id="zp-service-faq-person-css">' . zp_services_faq_person_css() . '</style>' . substr($result, $head);
    }
    return $result;
}

function zp_services_faq_widgets_document(string $html): string {
    if (!function_exists('zp_seo_service_faq_context') || !zp_seo_service_faq_context()) { return $html; }
    $source = class_exists('\\ZPL\\Router') ? (string) \ZPL\Router::$source : '';
    if ($source === '' && function_exists('get_queried_object_id')) { $source = zp_seo_plan_path((string) get_permalink(get_queried_object_id())); }
    return zp_services_faq_render_document($html, $source, function_exists('zp_seo_plan_is_en') && zp_seo_plan_is_en());
}
add_filter('zp_service_widgets_document', 'zp_services_faq_widgets_document', 20);

/** Reviewed public questions from production3.2.10; source guard protects owner edits. */
function zp_services_faq_additions_20261015(): array {
    return [
        '/projektowanie-logo/' => [
            'pl' => ['count' => 12, 'questions_sha256' => 'cc7c8248f0b4cfd9ee3ba1a670a7983c92f6a36e6c7292f1ba315c53e21fc35a', 'question' => 'Jak sprawdzić, czy wybrane logo będzie czytelne w codziennym użyciu?', 'answer' => 'Oceń znak w małej skali, w jednym kolorze oraz na jasnym i ciemnym tle. Porównaj jego użycie na stronie, w avatarze, na dokumencie i w druku. Zwróć uwagę na czytelność nazwy, proporcje i rozpoznawalność symbolu. Makieta pomaga zobaczyć zastosowanie, ale nie powinna zastępować oceny samego znaku.'],
            'en' => ['count' => 12, 'questions_sha256' => '733a0137bb174615b462723b656fdf6a3a068643c2a0aa81590dece59e8ab046', 'question' => 'How can I check whether a logo will remain clear in everyday use?', 'answer' => 'Review the mark at a small size, in one colour, and against light and dark backgrounds. Compare how it works on a website, as an avatar, on a document and in print. Check the readability of the name, the proportions and whether the symbol is recognisable. A mockup helps show an application, but should not replace reviewing the mark itself.'],
        ],
        '/logo-branding-katowice/' => [
            'pl' => ['count' => 8, 'questions_sha256' => 'eb6972a38d4a880b9cc68fad43c309a10a6b2eb29913fb85f534d6e557a5750c', 'question' => 'Na co zwrócić uwagę podczas oceny propozycji logo?', 'answer' => 'Odnieś propozycję do odbiorców marki i miejsc, w których znak ma być używany. Sprawdź czytelność nazwy w małym rozmiarze, wariant jednokolorowy oraz jasne i ciemne tło. Przy przekazywaniu uwag opisz konkretny problem, np. trudny do odczytania napis lub zbyt drobny detal. Ułatwia to rozmowę o projekcie bez sprowadzania jej wyłącznie do gustu.'],
            'en' => ['count' => 15, 'questions_sha256' => 'f14b205edca3b662b6a11c907c231eaae97c40d7263f74b906ca6b161bf3bd4e', 'question' => 'What should I look for when reviewing a logo proposal?', 'answer' => 'Relate the proposal to the brand’s audience and the places where the mark will be used. Check the name at a small size, the single-colour version, and light and dark backgrounds. When giving feedback, describe the specific issue, such as hard-to-read lettering or a detail that is too small. This keeps the review focused on the project rather than personal taste alone.'],
        ],
        '/identyfikacja-wizualna/' => [
            'pl' => ['count' => 11, 'questions_sha256' => '6fdf00593c11fea24e99e5e0fb661908b520c8d63390449ce765e43d9b1d3b23', 'question' => 'Jak sprawdzić, czy identyfikacja wizualna tworzy spójny system?', 'answer' => 'Porównaj kilka zastosowań obok siebie: stronę internetową, profil społecznościowy, ofertę i materiał drukowany. Logo, kolory, typografia i styl grafik powinny tworzyć rozpoznawalną całość, ale nie muszą wyglądać identycznie w każdym formacie. Księga znaku porządkuje zasady, dzięki którym kolejne materiały mogą zachować tę spójność.'],
            'en' => ['count' => 11, 'questions_sha256' => '6ea918c2647f9b77d98d06870ac766ddee12eaa4947b7f40403098498471d112', 'question' => 'How can I check whether a visual identity works as a coherent system?', 'answer' => 'Compare several applications side by side: a website, a social media profile, a proposal and a printed item. The logo, colours, typography and graphic style should form a recognisable whole without looking identical in every format. Brand guidelines organise the rules that help future materials remain consistent.'],
        ],
        '/tworzenie-stron-internetowych/' => [
            'pl' => ['count' => 10, 'questions_sha256' => '64f855f3fb6c89f714bac211193529b17015233787ee855c008c76aeb4bf5cd9', 'question' => 'Jakie informacje są potrzebne do wyceny strony internetowej?', 'answer' => 'Opisz cel strony, odbiorców i planowane podstrony. Wskaż, które teksty, zdjęcia i elementy identyfikacji już masz, czy istniejąca strona wymaga przeniesienia oraz jakie formularze, języki lub integracje są potrzebne. Przykłady stron, które Ci odpowiadają, pomogą określić kierunek projektu. Na tej podstawie łatwiej porównać zakresy ofert.'],
            'en' => ['count' => 10, 'questions_sha256' => '27cf50fa450ab3798c0a2252f0bc2b783d0bea0d341a048e97dfcc951798dae6', 'question' => 'What information is needed to quote a website project?', 'answer' => 'Describe the website’s goal, audience and planned pages. Explain which texts, photos and brand assets you already have, whether an existing site needs to be migrated, and which forms, languages or integrations are required. Examples of websites you like help establish a design direction. This information makes it easier to compare the scope of different proposals.'],
        ],
        '/sklepy-internetowe-katowice/' => [
            'pl' => ['count' => 6, 'questions_sha256' => 'b706dfcc642a14e449a69166f40a7479bd47337b591528fcc57d0b385c6f3846', 'question' => 'Co przygotować przed rozmową o sklepie WooCommerce?', 'answer' => 'Przygotuj orientacyjną liczbę produktów, kategorie i warianty, np. rozmiary lub kolory. Opisz sposób obsługi płatności i dostaw oraz systemy, z którymi sklep ma współpracować. Warto też wskazać, czy masz zdjęcia i opisy produktów oraz czy trzeba przenieść dane z istniejącego sklepu. Te informacje pomagają określić zakres wdrożenia.'],
        ],
    ];
}
