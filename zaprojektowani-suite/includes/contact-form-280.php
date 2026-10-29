<?php
if (!defined('ABSPATH')) { exit; }

/**
 * ZP Suite 2.8.0 — przyjaźniejszy długi formularz kontaktowy.
 *
 * Mat (7.10.2026): formularz kontaktowy na całej stronie ma być bardziej przyjazny. Nowe teksty,
 * dopiski „(opcjonalnie)” i podpowiedzi przy polach są w templates/contact-page/form.html;
 * tu są ich angielskie wersje dla modułu językowego (PL → EN i z powrotem).
 */

/** Dokłada słownik PL => EN do mapy modułu językowego (dla 'pl' w drugą stronę), bez nadpisywania istniejących wpisów. */
function zp_suite_contact_280_merge_dictionary($map, string $lang, array $dict): array {
  $map = is_array($map) ? $map : [];
  foreach ($dict as $pl => $en) {
    $from = $lang === 'pl' ? $en : $pl;
    if (!isset($map[$from])) { $map[$from] = $lang === 'pl' ? $pl : $en; }
  }
  return $map;
}

function zp_suite_contact_form_280_dictionary(): array {
  return [
    'Wystarczy imię i numer telefonu: oddzwonimy i razem ustalimy szczegóły. Możesz też opisać sprawę w kilku zdaniach albo wysłać pełny brief strony internetowej, sklepu WooCommerce, logo, brandingu, SEO lub kampanii.' => 'Your name and phone number are enough: we will call you back and work out the details together. You can also describe your project in a few sentences or send a full brief for a website, a WooCommerce store, a logo, branding, SEO or a campaign.',
    'Wystarczy telefon albo e-mail, żebyśmy mogli odpisać lub oddzwonić.' => 'A phone number or an email is enough for us to reply or call you back.',
    'Wystarczy telefon albo e-mail. O szczegóły dopytamy, jeśli będą potrzebne.' => 'A phone number or an email is enough. We will ask about the details if we need them.',
    'Nazwa firmy <1>(opcjonalnie)</1>' => 'Company name <1>(optional)</1>',
    'Budżet orientacyjny <1>(opcjonalnie)</1>' => 'Estimated budget <1>(optional)</1>',
    'Preferowany termin <1>(opcjonalnie)</1>' => 'Preferred timeline <1>(optional)</1>',
    'Adres obecnej strony lub inspiracje <1>(opcjonalnie)</1>' => 'Current website address or inspiration <1>(optional)</1>',
    'Kiedy najlepiej oddzwonić? <1>(opcjonalnie)</1>' => 'When is the best time to call? <1>(optional)</1>',
    'Temat rozmowy <1>(opcjonalnie)</1>' => 'Call topic <1>(optional)</1>',
    'Krótka notatka do rozmowy <1>(opcjonalnie)</1>' => 'A short note for the call <1>(optional)</1>',
    'Zaznacz, czego dotyczy wiadomość. Ten krok możesz pominąć.' => 'Select what your message is about. You can skip this step.',
    'Wyrażam zgodę na kontakt w sprawie przesłanego zapytania. Administratorem danych jest Zaprojektowani.com (<1>polityka prywatności</1>).' => 'I agree to be contacted about my inquiry. The data controller is Zaprojektowani.com (<1>privacy policy</1>).',
    'Wpisz imię, żebyśmy wiedzieli, jak się do Ciebie zwracać.' => 'Please enter your first name so we know how to address you.',
    'Wpisz numer telefonu, np. 501 054 253.' => 'Please enter your phone number, e.g. +48 501 054 253.',
    'Numer wygląda na niepełny. Wpisz 9 cyfr, np. 501 054 253.' => 'The number looks incomplete. Please enter the full number, e.g. +48 501 054 253.',
    'Wpisz telefon albo e-mail. Wystarczy jedno z nich.' => 'Please enter a phone number or an email. One of them is enough.',
    'Sprawdź adres e-mail, np. anna@firma.pl.' => 'Please check the email address, e.g. anna@company.com.',
    'Dopisz kilka słów o sprawie. Wystarczy jedno, dwa zdania.' => 'Add a few words about your project. A sentence or two is enough.',
    'Zaznacz zgodę na kontakt, żebyśmy mogli odpowiedzieć.' => 'Please tick the consent box so we can reply.',
  ];
}

add_filter('zpl_dictionary', function ($map, $lang = 'en') {
  return zp_suite_contact_280_merge_dictionary($map, (string) $lang, zp_suite_contact_form_280_dictionary());
}, 10, 2);
