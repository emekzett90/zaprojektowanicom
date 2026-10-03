<?php
if (!defined('ABSPATH')) { exit; }
/**
 * New articles (content batch 2, tresci/paczka-2). The article body is data/articles/<slug>.html;
 * {{IMG:name}} is a photo from assets/img/wpisy/, imported into the media library on publishing.
 * 'source' is the post whose Elementor layout and article styles are copied.
*/
return [
  '/strony-internetowe/jak-stworzyc-strone-internetowa/' => [
    'slug' => 'jak-stworzyc-strone-internetowa',
    'category' => 'strony-internetowe',
    'title' => 'Jak stworzyć stronę internetową dla firmy? Kreator, WordPress czy agencja',
    'source' => '/strony-internetowe/strona-internetowa-dla-firmy-co-powinna-zawierac/',
    'featured' => 'strona-salonu-urody-mockup-okladka',
  ],
  '/logo-branding/jak-zastrzec-logo/' => [
    'slug' => 'jak-zastrzec-logo',
    'category' => 'logo-branding',
    'title' => 'Zastrzeżenie logo – jak zastrzec logo w Polsce i w Unii Europejskiej?',
    'source' => '/logo-branding/projektowanie-logo-jak-powstaje-znak-ktory-dziala-w-internecie-druku-i-sprzedazy/',
    'featured' => 'znak-marki-medycznej-okladka',
  ],
  '/logo-branding/key-visual-co-to-jest/' => [
    'slug' => 'key-visual-co-to-jest',
    'category' => 'logo-branding',
    'title' => 'Key visual – co to jest i jak zaprojektować główny motyw kampanii?',
    'source' => '/logo-branding/projektowanie-logo-jak-powstaje-znak-ktory-dziala-w-internecie-druku-i-sprzedazy/',
    'featured' => 'key-visual-formaty-okladka',
  ],
  '/logo-branding/logo-logotyp-sygnet-roznice/' => [
    'slug' => 'logo-logotyp-sygnet-roznice',
    'category' => 'logo-branding',
    'title' => 'Logo a logotyp i sygnet – czym się różnią i co wybrać dla firmy?',
    'source' => '/logo-branding/projektowanie-logo-jak-powstaje-znak-ktory-dziala-w-internecie-druku-i-sprzedazy/',
    'featured' => 'znak-pracowni-architektury-okladka',
  ],
  '/logo-branding/logo-wektorowe-pliki-logo/' => [
    'slug' => 'logo-wektorowe-pliki-logo',
    'category' => 'logo-branding',
    'title' => 'Logo wektorowe – jakie pliki logo powinna mieć firma i do czego służą?',
    'source' => '/logo-branding/projektowanie-logo-jak-powstaje-znak-ktory-dziala-w-internecie-druku-i-sprzedazy/',
    'featured' => 'wersje-znaku-restauracji-okladka',
  ],
];
