<?php
/** Client-reported booking enquiries. This is not a traffic or completed-visit measure. */
if (!defined('ABSPATH')) { exit; }

function zp_cs66_results(string $key, string $lang = 'pl'): string {
    if ($key !== 'apartament-piekna') { return ''; }

    $en = $lang === 'en';
    $copy = $en ? [
        'eyebrow' => 'The system in everyday use',
        'heading' => 'More enquiries.',
        'accent' => 'One place to manage them.',
        'intro' => 'The salon receives appointment requests through its own website. Here is how the estimated weekly number of enquiries through “Book a visit” compares with the beginning of the year.',
        'current' => 'enquiries per week · October 2026',
        'chart' => 'Enquiries through “Book a visit”',
        'unit' => 'Number of enquiries per week',
        'before' => 'Beginning of 2026',
        'after' => 'October 2026',
        'per_week' => 'per week',
        'legend' => 'Shaded end of each bar: estimated range',
        'source' => 'Estimated number of enquiries through the “Book a visit” system, based on information supplied by the client (23 October 2026).',
        'note' => 'An enquiry is a request for an appointment. The salon confirms the date separately.',
        'cta' => 'Discuss your booking system',
    ] : [
        'eyebrow' => 'System w codziennej pracy',
        'heading' => 'Więcej zgłoszeń.',
        'accent' => 'Jedno miejsce do ich obsługi.',
        'intro' => 'Salon przyjmuje zgłoszenia na wizyty przez własną stronę. Tak zmieniła się szacunkowa liczba zapytań przesyłanych co tydzień przez „Umów wizytę” względem początku roku.',
        'current' => 'zgłoszeń tygodniowo · październik 2026',
        'chart' => 'Zgłoszenia przez „Umów wizytę”',
        'unit' => 'Liczba zgłoszeń tygodniowo',
        'before' => 'Początek 2026',
        'after' => 'Październik 2026',
        'per_week' => 'tygodniowo',
        'legend' => 'Cieniowana końcówka słupka: szacunkowy przedział',
        'source' => 'Szacunkowa liczba zgłoszeń przez system „Umów wizytę”, według danych przekazanych przez klienta (23.10.2026).',
        'note' => 'Zgłoszenie jest prośbą o wizytę — termin potwierdza salon.',
        'cta' => 'Porozmawiajmy o Twoim systemie',
    ];

    $out = '<section id="wyniki" class="zpCS18__section zpCS66Results" aria-labelledby="zpCS66Results-title"><div class="zpCS18__inner">';
    $out .= '<div class="zpCS66Results__layout"><div class="zpCS66Results__intro">';
    $out .= '<p class="zpCS18__eyebrow"><span aria-hidden="true"></span>' . esc_html($copy['eyebrow']) . '</p>';
    $out .= '<h2 id="zpCS66Results-title">' . esc_html($copy['heading']) . ' <strong>' . esc_html($copy['accent']) . '</strong></h2>';
    $out .= '<p class="zpCS66Results__lead">' . esc_html($copy['intro']) . '</p>';
    $out .= '<p class="zpCS66Results__highlight"><strong>100–150</strong><span>' . esc_html($copy['current']) . '</span></p>';
    $out .= '<a class="zpCS18__button zpCS66Results__cta" href="#szybki-kontakt">' . esc_html($copy['cta']) . '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false"><path d="M6 18 18 6M6 6h12v12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></a></div>';

    $out .= '<figure class="zpCS66Results__chart" aria-describedby="zpCS66Results-source"><figcaption>' . esc_html($copy['chart']) . '</figcaption>';
    $out .= '<p class="zpCS66Results__unit">' . esc_html($copy['unit']) . '</p><div class="zpCS66Results__plot">';
    // Both bars share a zero-origin scale of 0–150. The striped section represents the supplied range.
    foreach ([['before', '40–50', '26.666667%', '6.666666%'], ['after', '100–150', '66.666667%', '33.333333%']] as $range) {
        $out .= '<div class="zpCS66Results__row zpCS66Results__row--' . esc_attr($range[0]) . '"><div class="zpCS66Results__rowLabel"><span>' . esc_html($copy[$range[0]]) . '</span><strong>' . esc_html($range[1]) . '<small>' . esc_html($copy['per_week']) . '</small></strong></div>';
        $out .= '<div class="zpCS66Results__track" aria-hidden="true" style="--range-start:' . esc_attr($range[2]) . ';--range-width:' . esc_attr($range[3]) . '"><span class="zpCS66Results__solid"></span><span class="zpCS66Results__range"></span></div></div>';
    }
    $out .= '<div class="zpCS66Results__axis" aria-hidden="true"><span>0</span><span>50</span><span>100</span><span>150</span></div></div>';
    $out .= '<p class="zpCS66Results__legend"><span aria-hidden="true"></span>' . esc_html($copy['legend']) . '</p>';
    $out .= '<p id="zpCS66Results-source" class="zpCS66Results__source">' . esc_html($copy['source']) . '</p>';
    $out .= '<p class="zpCS66Results__note">' . esc_html($copy['note']) . '</p></figure></div></div></section>';
    return $out;
}
