<?php
/** One reusable, accessible coverage section immediately before the service FAQ. */
if (!defined('ABSPATH')) { exit; }

function zp_coverage_source_20261015(): string {
    $source = class_exists('\\ZPL\\Router') ? (string) \ZPL\Router::$source : '';
    return $source !== '' ? $source : zp_seo_plan_path((string) get_permalink(get_queried_object_id()));
}

function zp_coverage_kind_20261015(string $source): string {
    if ($source === '/kampanie-reklamowe/') { return 'kampanie'; }
    return (string) (zp_seo_service_registry()[$source]['kind'] ?? '');
}

function zp_coverage_url_20261015(string $path, bool $english): string {
    $url = home_url($path);
    return function_exists('zpl_url') ? zpl_url($url, $english ? 'en' : 'pl') : $url;
}

function zp_coverage_markup_20261015(string $kind, bool $english): string {
    $lead = $english
        ? [
            'logo' => 'We design logos and brand identities for businesses across Poland. We can meet at our Katowice studio or handle every stage online.',
            'sklepy' => 'We build online stores for businesses across Poland. We agree the scope, review the design and plan the launch online or at our Katowice studio.',
            'kampanie' => 'We plan and run advertising campaigns for businesses across Poland. We discuss goals, materials and results online or at our Katowice studio.',
            'strony' => 'We design websites for businesses across Poland. We can discuss the brief, present the design and coordinate the launch online or at our Katowice studio.',
        ]
        : [
            'logo' => 'Projektujemy logo i branding dla firm z całej Polski. W Katowicach spotkamy się w studiu, a każdy etap projektu możemy przeprowadzić online.',
            'sklepy' => 'Tworzymy sklepy internetowe dla firm z całej Polski. Zakres, projekt i uruchomienie sprzedaży omówimy online lub w naszym studiu w Katowicach.',
            'kampanie' => 'Planujemy i prowadzimy kampanie reklamowe dla firm z całej Polski. Cele, materiały i wyniki omówimy online lub w naszym studiu w Katowicach.',
            'strony' => 'Projektujemy strony internetowe dla firm z całej Polski. Brief, prezentacje projektu i wdrożenie możemy przeprowadzić online lub omówić w naszym studiu w Katowicach.',
        ];
    $localMode = $english ? 'Katowice studio or online' : 'Studio w Katowicach lub online';
    $remoteMode = $english ? 'Online collaboration' : 'Współpraca online';
    $localCopy = $english
        ? 'Meet us at our studio at Modelarska 18/2 or work with us remotely throughout the project. Choose the way that suits you.'
        : 'Spotkanie w naszym studiu przy ul. Modelarskiej 18/2 albo cały projekt zdalnie. Wybierz wygodny sposób współpracy.';
    $remoteCopy = $english
        ? 'You do not need to travel to Katowice. We handle the brief, presentations and consultations online, with a clear scope for each stage.'
        : 'Nie musisz dojeżdżać do Katowic. Brief, prezentacje i konsultacje prowadzimy online, z jasnym zakresem każdego etapu.';
    $cities = [
        ['gdansk', 'Gdańsk', '45%', '10%'], ['poznan', 'Poznań', '28.5%', '42%'],
        ['warszawa', $english ? 'Warsaw' : 'Warszawa', '68%', '45%'],
        ['wroclaw', 'Wrocław', '29.5%', '63.5%'], ['katowice', 'Katowice', '49%', '77.5%'], ['krakow', 'Kraków', '61%', '82%'],
        ['lodz', $english ? 'Łódź' : 'Łódź', '53%', '52%'], ['szczecin', 'Szczecin', '12%', '25%'],
        ['lublin', 'Lublin', '82%', '62%'], ['bialystok', 'Białystok', '85%', '31%'],
    ];
    $benefits = $english
        ? ['One team from the brief to delivery', 'Project presentations and consultations online', 'Organised materials and a clear next step']
        : ['Jeden zespół od briefu do gotowego projektu', 'Prezentacje projektu i konsultacje online', 'Uporządkowane materiały i jasny kolejny krok'];
    $flow = $english ? ['Brief and conversation', 'Plan and scope', 'Project and feedback', 'Delivery and handover']
        : ['Brief i rozmowa', 'Plan i zakres', 'Projekt i konsultacje', 'Przekazanie projektu'];
    // Rendering runs inside the complete-document output handler: nested buffers are forbidden.
    $markup = '';
    $markup .= '    <section class="zpCoverage" id="zasieg" data-zp-coverage="20261015" aria-labelledby="zpCoverageTitle">
      <div class="zpCoverage__wrap">
        <div class="zpCoverage__copy">
          <p class="zpCoverage__eyebrow">';
$markup .= esc_html($english ? 'Across Poland · Katowice studio' : 'Cała Polska · studio w Katowicach');     $markup .= '</p>
          <h2 id="zpCoverageTitle">';
$markup .= $english ? 'Good collaboration.<br><strong>Wherever you are.</strong>' : 'Dobra współpraca.<br><strong>Bez względu na odległość.</strong>';     $markup .= '</h2>
          <p class="zpCoverage__lead">';
$markup .= esc_html($lead[$kind] ?? $lead['strony']);     $markup .= '</p>
          <ul class="zpCoverage__benefits">';
foreach ($benefits as $benefit):     $markup .= '<li>';
$markup .= esc_html($benefit);     $markup .= '</li>';
endforeach;     $markup .= '</ul>
          <div class="zpCoverage__links">
            <a class="zpCoverage__primary" href="';
$markup .= esc_url(zp_coverage_url_20261015('/studio-wyceny/', $english));     $markup .= '">';
$markup .= esc_html($english ? 'Let’s discuss your project' : 'Porozmawiajmy o projekcie');     $markup .= ' <span aria-hidden="true">↗</span></a>
            <a href="';
$markup .= esc_url(zp_coverage_url_20261015('/kontakt/', $english));     $markup .= '">';
$markup .= esc_html($english ? 'Our Katowice studio' : 'Studio w Katowicach');     $markup .= '</a>
          </div>
        </div>
        <div class="zpCoverage__visual">
          <div class="zpCoverage__map">
            ';
// Small, trusted local SVG enables a stroke drawing animation without a map SDK.
            $markup .= file_get_contents(ZP_SUITE_PATH . 'assets/poland-coverage.svg');     $markup .= '            <div class="zpCoverage__cities" role="group" aria-label="';
$markup .= esc_attr($english ? 'Select a city for collaboration' : 'Wybierz miasto współpracy');     $markup .= '">
              ';
foreach ($cities as [$slug, $name, $x, $y]): $local = $slug === 'katowice';     $markup .= '              <button class="zpCoverage__city" type="button" data-cov-city="';
$markup .= esc_attr($slug);     $markup .= '" data-name="';
$markup .= esc_attr($name);     $markup .= '" data-mode="';
$markup .= esc_attr($local ? $localMode : $remoteMode);     $markup .= '" data-copy="';
$markup .= esc_attr($local ? $localCopy : $remoteCopy);     $markup .= '" aria-pressed="';
$markup .= $local ? 'true' : 'false';     $markup .= '" aria-controls="zpCoverageStatus" style="--city-x:';
$markup .= esc_attr($x);     $markup .= ';--city-y:';
$markup .= esc_attr($y);     $markup .= '"><span>';
$markup .= esc_html($name);     $markup .= '</span></button>
              ';
endforeach;     $markup .= '            </div>
          </div>
          <div class="zpCoverage__status" id="zpCoverageStatus" role="status" aria-live="polite" aria-atomic="true" data-cov-status>
            <p class="zpCoverage__status-label">';
$markup .= esc_html($english ? 'How we work together' : 'Model współpracy');     $markup .= '</p>
            <h3 data-cov-name>Katowice</h3><span class="zpCoverage__mode" data-cov-mode>';
$markup .= esc_html($localMode);     $markup .= '</span><p data-cov-copy>';
$markup .= esc_html($localCopy);     $markup .= '</p>
          </div>
          <p class="zpCoverage__caption">';
$markup .= esc_html($english ? 'The cities show where we work with clients online. Our studio is in Katowice.' : 'Miasta pokazują zasięg współpracy online. Nasze studio znajduje się w Katowicach.');     $markup .= '</p>
          <ol class="zpCoverage__flow" aria-label="';
$markup .= esc_attr($english ? 'Stages of collaboration' : 'Etapy współpracy');     $markup .= '">';
foreach ($flow as $step):     $markup .= '<li>';
$markup .= esc_html($step);     $markup .= '</li>';
endforeach;     $markup .= '</ol>
        </div>
      </div>
    </section>
    ';

    return $markup;
}

/** Full-document scanner skips inert script, template and comment examples. */
function zp_coverage_section_20261015(string $html, string $id): ?array {
    $scan = zp_speed_scan($html);
    if (!$scan || !preg_match_all('~<section(?=[\s/>])~i', $html, $matches, PREG_OFFSET_CAPTURE)) { return null; }
    $found = [];
    foreach ($matches[0] as $match) {
        $at = $match[1];
        if (zp_speed_in_skip($scan['skip'], $at)) { continue; }
        $gt = zp_speed_tag_end($html, $at);
        if ($gt < 0) { return null; }
        $attrs = zp_services_faq_attributes(substr($html, $at, $gt + 1 - $at));
        if (($attrs['id'] ?? '') !== $id) { continue; }
        $end = zp_speed_nested_end($html, 'section', $gt + 1);
        if ($end < 0) { return null; }
        $found[] = [$at, $end];
    }
    return count($found) === 1 ? $found[0] : null;
}

function zp_coverage_render_document_20261015(string $html, string $source, bool $english): string {
    $kind = zp_coverage_kind_20261015($source);
    if ($kind === '' || strpos($html, 'data-zp-coverage="20261015"') !== false) { return $html; }
    $faq = zp_coverage_section_20261015($html, 'faq') ?? zp_coverage_section_20261015($html, 'zpFaqShopKatNavy');
    if (!$faq) { return $html; }
    $old = zp_coverage_section_20261015($html, 'zasieg');
    if ($old) { $html = substr($html, 0, $old[0]) . substr($html, $old[1]); }
    $faq = zp_coverage_section_20261015($html, 'faq') ?? zp_coverage_section_20261015($html, 'zpFaqShopKatNavy');
    return $faq ? substr($html, 0, $faq[0]) . zp_coverage_markup_20261015($kind, $english) . substr($html, $faq[0]) : $html;
}

add_filter('zp_service_widgets_document', static function (string $html): string {
    if (!zp_seo_service_faq_context()) { return $html; }
    return zp_coverage_render_document_20261015($html, zp_coverage_source_20261015(), zp_seo_plan_is_en());
}, 10);

add_action('wp_enqueue_scripts', static function (): void {
    if (!zp_seo_service_faq_context() || zp_coverage_kind_20261015(zp_coverage_source_20261015()) === '') { return; }
    wp_enqueue_style('zp-service-coverage', ZP_SUITE_URL . 'assets/css/service-coverage-20261015.css', [], ZP_SUITE_VERSION);
    wp_enqueue_script('zp-service-coverage', ZP_SUITE_URL . 'assets/js/service-coverage-20261015.js', [], ZP_SUITE_VERSION, true);
    wp_script_add_data('zp-service-coverage', 'strategy', 'defer');
}, 35);
