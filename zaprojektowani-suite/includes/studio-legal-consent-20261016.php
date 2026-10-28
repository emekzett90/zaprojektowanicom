<?php
/** Required acceptance of the free Studio enquiry service; no privacy/marketing consent. */
if (!defined('ABSPATH')) { exit; }

function zp_studio_legal_version() {
  return function_exists('zp_suite_legal_document_version') ? (string)zp_suite_legal_document_version() : '2026-10-16';
}

function zp_studio_legal_urls($lang = 'pl') {
  $lang = $lang === 'en' ? 'en' : 'pl';
  $paths = ['terms'=>'/regulamin/', 'privacy'=>'/polityka-prywatnosci/'];
  $fallback_en = ['terms'=>'/en/terms/', 'privacy'=>'/en/privacy-policy/'];
  $urls = [];
  foreach ($paths as $key => $path) {
    $urls[$key] = function_exists('zpl_url') ? zpl_url(home_url($path), $lang) : home_url($lang === 'en' ? $fallback_en[$key] : $path);
  }
  return $urls;
}

function zp_studio_legal_language() {
  if (function_exists('zpl_language')) { return zpl_language() === 'en' ? 'en' : 'pl'; }
  return preg_match('~^/en(?:/|$)~', (string)parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH)) ? 'en' : 'pl';
}

function zp_studio_legal_copy($lang = 'pl') {
  if ($lang === 'en') {
    return [
      'prefix' => 'I accept the ',
      'terms' => 'Terms for submitting enquiries and cooperation',
      'suffix' => '. I understand that submitting an enquiry is free of charge and does not constitute entering into a contract for the project.',
      'privacy' => 'Information about the processing of your enquiry data is provided in the ',
      'privacy_link' => 'Privacy Policy',
      'error' => 'Accept the Terms before sending your enquiry.',
      'version_error' => 'The Terms have been updated. Refresh the page and read the current version before sending your enquiry.',
      'error_title' => 'The enquiry could not be sent',
    ];
  }
  return [
    'prefix' => 'Akceptuję ',
    'terms' => 'Regulamin składania zapytań i współpracy',
    'suffix' => '. Wiem, że wysłanie zapytania jest bezpłatne i nie oznacza zawarcia umowy o realizację projektu.',
    'privacy' => 'Informacje o przetwarzaniu danych z zapytania znajdziesz w ',
    'privacy_link' => 'Polityce prywatności',
    'error' => 'Zaakceptuj Regulamin przed wysłaniem zapytania.',
    'version_error' => 'Regulamin został zaktualizowany. Odśwież stronę i przeczytaj aktualną wersję przed wysłaniem zapytania.',
    'error_title' => 'Nie udało się wysłać zapytania',
  ];
}

function zp_studio_legal_render() {
  $lang = zp_studio_legal_language();
  $copy = zp_studio_legal_copy($lang);
  $urls = zp_studio_legal_urls($lang);
  // The block lives outside data-final-summary: refresh() replaces that container.
  // It has no data-field: the legacy collector reads .value even for unchecked inputs.
  return '<div class="zpStudioLegal" data-zp-studio-legal data-zpl-skip translate="no" lang="'.esc_attr($lang).'">'
    .'<label class="zpStudioLegal__label" for="zpStudioLegalAccepted">'
    .'<input id="zpStudioLegalAccepted" type="checkbox" name="legalAccepted" value="1" required aria-describedby="zpStudioLegalError">'
    .'<span><span data-zp-legal-prefix>'.esc_html($copy['prefix']).'</span><a data-zp-legal-terms href="'.esc_url($urls['terms']).'" target="_blank" rel="noopener">'.esc_html($copy['terms']).'</a><span data-zp-legal-suffix>'.esc_html($copy['suffix']).'</span></span></label>'
    .'<input type="hidden" name="legalVersion" value="'.esc_attr(zp_studio_legal_version()).'">'
    .'<input type="hidden" name="formLang" value="'.esc_attr($lang).'">'
    .'<p class="zpStudioLegal__error" id="zpStudioLegalError" role="alert" hidden>'.esc_html($copy['error']).'</p>'
    .'<p class="zpStudioLegal__privacy"><span data-zp-legal-privacy>'.esc_html($copy['privacy']).'</span><a data-zp-legal-privacy-link href="'.esc_url($urls['privacy']).'" target="_blank" rel="noopener">'.esc_html($copy['privacy_link']).'</a>.</p></div>'
    .'<style id="zp-studio-legal-20261016">#zpbsUltimate .zpStudioLegal{margin:24px 0 0;padding:20px;border:1px solid rgba(7,20,38,.15);border-radius:18px;background:#fff;color:#071426;text-align:left}#zpbsUltimate .zpStudioLegal__label{display:flex;align-items:flex-start;gap:12px;cursor:pointer;font-size:14px;line-height:1.65;font-weight:500}#zpbsUltimate .zpStudioLegal__label input{appearance:auto;flex:0 0 20px;width:20px;height:20px;margin:3px 0 0;accent-color:#102a4f;cursor:pointer}#zpbsUltimate .zpStudioLegal a{color:#102a4f;text-decoration:underline;text-underline-offset:3px;font-weight:700}#zpbsUltimate .zpStudioLegal__privacy{margin:12px 0 0 32px;font-size:12px;line-height:1.65;color:#526174}#zpbsUltimate .zpStudioLegal__error{margin:10px 0 0 32px;font-size:13px;line-height:1.5;color:#a31d28;font-weight:700}#zpbsUltimate .zpStudioLegal__error[hidden]{display:none!important}#zpbsUltimate .zpStudioLegal input:focus-visible{outline:3px solid #74a6d8;outline-offset:3px}@media(max-width:600px){#zpbsUltimate .zpStudioLegal{padding:16px}#zpbsUltimate .zpStudioLegal__label{font-size:13px}}</style>';
}

function zp_studio_legal_request_language($request) {
  return isset($request['formLang']) && $request['formLang'] === 'en' ? 'en' : 'pl';
}

function zp_studio_legal_errors($request) {
  $copy = zp_studio_legal_copy(zp_studio_legal_request_language($request));
  // Deliberately exact: arrays, false, truthy strings and payload summaries are not acceptance.
  if (!isset($request['legalAccepted']) || $request['legalAccepted'] !== '1') { return [$copy['error']]; }
  if (!isset($request['legalVersion']) || $request['legalVersion'] !== zp_studio_legal_version()) { return [$copy['version_error']]; }
  return [];
}

function zp_studio_legal_evidence($request) {
  // This helper must only be called after successful validation. Never backfill old enquiries.
  if (zp_studio_legal_errors($request)) { return null; }
  $lang = zp_studio_legal_request_language($request);
  $copy = zp_studio_legal_copy($lang);
  $urls = zp_studio_legal_urls($lang);
  $key = isset($request['serviceKey']) && is_string($request['serviceKey']) ? $request['serviceKey'] : '';
  return [
    'accepted' => true,
    'legalVersion' => zp_studio_legal_version(),
    'accepted_at_utc' => gmdate('Y-m-d\TH:i:s\Z'),
    'terms_url' => $urls['terms'],
    'language' => $lang,
    'statement' => $copy['prefix'].$copy['terms'].$copy['suffix'],
    'service_key' => in_array($key, ['web','shop','brand','ads'], true) ? $key : '',
  ];
}

function zp_studio_legal_payload($payload, $evidence) {
  // Authoritative server values override any forged summary rows with the same names.
  return array_merge($payload, [
    'Akceptacja regulaminu' => 'Tak',
    'Wersja regulaminu' => $evidence['legalVersion'],
    'Data akceptacji regulaminu (UTC)' => $evidence['accepted_at_utc'],
    'Treść akceptacji regulaminu' => $evidence['statement'],
    'Adres regulaminu' => $evidence['terms_url'],
    'Język formularza' => $evidence['language'],
  ]);
}

/** Curated new enquiry labels, including client-side updates, without translating payload keys. */
function zp_studio_legal_translations($layers, $lang, $source) {
  $layers = is_array($layers) ? $layers : [];
  if ($source !== '/studio-wyceny/' || !class_exists('ZPL\\Html')) { return $layers; }
  $copy = [
    'Zapytanie o <1>logo i branding</1>.' => 'An enquiry about <1>logo and branding</1>.',
    'Zapytanie o' => 'An enquiry about',
    'logo i branding' => 'logo and branding',
    'Zapytanie Logo & Branding' => 'Logo & Branding enquiry',
    'Zapytanie' => 'Enquiry',
    'Zapytanie wysłane' => 'Enquiry sent',
    'Przy zapytaniu o logo i branding wpisz nazwę marki, dla której mamy przygotować projekt.' => 'When enquiring about logo and branding, enter the name of the brand we are to prepare the design for.',
    'Sprawdź szczegóły i wyślij zapytanie.' => 'Review the details and send an enquiry.',
    'Wyślij zapytanie' => 'Send an enquiry',
    'Wybierz pakiet, termin realizacji i dodatki do identyfikacji wizualnej. Na końcu zobaczysz podsumowanie ceny i wyślesz bezpłatne zapytanie.' => 'Choose a package, timeline and visual identity extras. At the end, you will see the price summary and send a free enquiry.',
    'Wybierz pakiet logo i brandingu, dopasuj termin realizacji, dodaj potrzebne materiały i wyślij bezpłatne zapytanie. Po otrzymaniu zgłoszenia wrócimy z potwierdzeniem oraz kolejnymi krokami.' => 'Choose a logo and branding package, adjust the timeline, add the materials you need and send a free enquiry. We will confirm receipt and explain the next steps.',
    'Wybierasz pakiet, termin oraz dodatki, a na końcu widzisz podsumowanie ceny. Po wysłaniu zapytania potwierdzimy szczegóły oraz kolejne kroki.' => 'Choose a package, timeline and extras, then review the price summary. After you send an enquiry, we will confirm the details and next steps.',
  ];
  $map = [];
  foreach ($copy as $pl => $en) {
    $map[\ZPL\Html::norm($lang === 'pl' ? $en : $pl)] = $lang === 'pl' ? $pl : $en;
  }
  array_unshift($layers, $map);
  return $layers;
}
add_filter('zpl_priority_dictionary', 'zp_studio_legal_translations', PHP_INT_MAX, 3);
