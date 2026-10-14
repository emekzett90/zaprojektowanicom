<?php
/**
 * SEO audit of 13.10.2026 (Seobility and Rank Math on the home page and every subpage).
 *
 * Image descriptions: the audit counted 128 images without alt on the home page alone, and the same header
 * images on every subpage. Templates print alt="" for pictures they treat as decoration; SEO tools read
 * that as a missing description. The final HTML (zp_suite_quality_html) now gives every known plugin image
 * a short description, Polish or English after <html lang>, so the English pages get English text without
 * waiting for the translator. Where the template had alt="" the picture also gets aria-hidden="true": screen
 * readers keep skipping it exactly as before, and crawlers read the description.
 *
 * Links: Seobility asks that internal links with parameters be nofollow. Those are the Studio Wyceny presets
 * (?zpbs_…), which only pre-fill the form; Studio Wyceny itself stays linked without parameters from every page.
 * Where the Studio reads the preset after # (14.10.2026), the links carry it there and need no nofollow.
 * WhatsApp, Messenger and Google Maps or review links are contact tools, not recommendations, so they get
 * nofollow too. Profiles (Facebook, Instagram, LinkedIn) and client sites stay followed.
 *
 * Links to redirects (Search Console and Senuto audit of 13.10, list C: 99 internal links led to a 301): a Polish
 * internal link to an address the keyword plan redirects, or to an old category that redirects to its topic's main
 * category (blog-knowledge.php), points straight at the final address. The redirects themselves stay.
 */
if (!defined('ABSPATH')) { exit; }

// Focus phrase in the title (Rank Math): one backed-up change of phrases and titles.
require_once __DIR__ . '/seo-audit-20261013-titles.php';

/** File key: base name without folder, query, extension, WordPress size suffix or "-scaled". */
function zp_suite_audit_alt_key(string $src): string {
  $path = (string) (wp_parse_url(html_entity_decode($src, ENT_QUOTES | ENT_HTML5, 'UTF-8'), PHP_URL_PATH) ?? '');
  $name = strtolower(basename($path));
  $name = (string) preg_replace('/\.(?:webp|png|jpe?g|gif|svg|avif)$/', '', $name);
  $name = (string) preg_replace('/-\d+x\d+$/', '', $name);
  $name = (string) preg_replace('/-scaled$/', '', $name);
  // Industry pictures come in several widths: kancelarie-cover-640, kancelarie-ludzie-1-600.
  return (string) preg_replace('/^((?:kancelarie|lekarze|deweloperzy|beauty|trenerzy|fotografowie|restauracje|hotele)-(?:cover|krag|hero|ludzie-\d))(?:-\d{3,4})?$/', '$1', $name);
}

/** [Polish, English] description for each plugin image the templates print without one. */
function zp_suite_audit_alt_map(): array {
  static $map = null;
  if ($map !== null) { return $map; }
  $team = static function (string $pl_names, string $en_names): array {
    return [$pl_names . ' z zespołu Zaprojektowani', $en_names . ' from the Zaprojektowani team'];
  };
  $logo = static function (string $name): array { return ['Logo ' . $name, $name . ' logo']; };
  $map = [
    // Team photos.
    'mateusz' => $team('Mateusz', 'Mateusz'),
    'mateusz_nowy' => $team('Mateusz', 'Mateusz'),
    'mateusz_pointing' => $team('Mateusz', 'Mateusz'),
    'mateusz-pointing' => $team('Mateusz', 'Mateusz'),
    'mateusz-sitting' => $team('Mateusz', 'Mateusz'),
    'crew-mateusz' => $team('Mateusz', 'Mateusz'),
    'mateusz-wybierz' => ['Mateusz z zespołu Zaprojektowani wskazuje ofertę', 'Mateusz from the Zaprojektowani team pointing to the offer'],
    'mateusz-faq-280' => ['Mateusz z zespołu Zaprojektowani odpowiada na pytania', 'Mateusz from the Zaprojektowani team answering questions'],
    'marta' => $team('Marta', 'Marta'),
    'marta_pointing' => $team('Marta', 'Marta'),
    'marta_pointing-_2_' => $team('Marta', 'Marta'),
    'crew-marta' => $team('Marta', 'Marta'),
    'stanislaw' => $team('Stanisław', 'Stanisław'),
    'stanislaw_pointing' => $team('Stanisław', 'Stanisław'),
    'stanislaw-pointing' => $team('Stanisław', 'Stanisław'),
    'crew-stanislaw' => $team('Stanisław', 'Stanisław'),
    'team_kontakt' => $team('Marta i Stanisław', 'Marta and Stanisław'),
    'wiedza_ekipa' => $team('Mateusz, Marta i Stanisław', 'Mateusz, Marta and Stanisław'),
    'team_box' => ['Zespół Zaprojektowani', 'The Zaprojektowani team'],
    'trusted-team' => ['Zespół Zaprojektowani przy laptopie', 'The Zaprojektowani team at a laptop'],
    // Projects and mock-ups.
    'zgorecki_oferta' => ['Identyfikacja wizualna Zgórecki Nieruchomości: katalog, teczka i wizytówki', 'Zgórecki Nieruchomości brand identity: catalogue, folder and business cards'],
    'zgorecki_bez_tla-2' => ['Identyfikacja wizualna Zgórecki Nieruchomości: książka, wizytówki, koperta i brelok', 'Zgórecki Nieruchomości brand identity: book, business cards, envelope and key fob'],
    'promo-strona' => ['Strona internetowa Krawiec z dojazdem na laptopie', 'The Krawiec z dojazdem website on a laptop'],
    'promo-sklep' => ['Sklep internetowy Pro Scarves z szalikami i czapkami na zamówienie', 'The Pro Scarves online store with custom scarves and beanies'],
    'laptop_sklepy_internetowe_katowice' => ['Sklep internetowy Pro Scarves na laptopie i telefonie', 'The Pro Scarves online store on a laptop and a phone'],
    'apartament_realizacja_compressed' => ['Projekt strony internetowej Apartament Piękna', 'The Apartament Piękna website design'],
    'apartament_piekna_pokaz' => ['Strona internetowa Apartament Piękna', 'The Apartament Piękna website'],
    'laptop_zaprojektowani' => ['Laptop z projektem Zaprojektowani', 'A laptop with a Zaprojektowani project'],
    'meta_ads_glowna' => ['Menedżer reklam Meta z wynikami kampanii na laptopie', 'Meta Ads Manager with campaign results on a laptop'],
    'portfolio-siemianowski' => ['Identyfikacja wizualna Kancelarii Adwokackiej Siemianowski', 'Siemianowski law firm brand identity'],
    'zp-sygnet-ciemny' => ['Sygnet Zaprojektowani', 'Zaprojektowani logo mark'],
    'zp-sygnet-jasny' => ['Sygnet Zaprojektowani', 'Zaprojektowani logo mark'],
    // Service heroes, also on the service cards under articles (seo-plan/service-heroes.php, seo-plan/links.php): the words of the hero.
    'polerstone-na-skalnej-podstawie' => ['Polerstone — projekt strony internetowej na laptopie i skalnej podstawie', 'Polerstone website design on a laptop on a stone base'],
    'swiat-grili-na-smartfonie-i-laptopie' => ['Świat Grilli — sklep internetowy na smartfonie i laptopie', 'Świat Grilli online store on a smartphone and a laptop'],
    'hero-laptop' => ['Projekt strony internetowej — laptop z witryną kancelarii', 'A website design — a laptop with a law firm website'],
    'zaprojektowani_logo_branding_zaufali_widget_alt_premium' => ['Mockup projektów logo i brandingu — Zaprojektowani', 'Logo and branding design mock-up — Zaprojektowani'],
    // Client logos.
    'vista' => $logo('Vista Architekci'),
    'logo-vista' => $logo('Vista Architekci'),
    'siemianowski-1' => ['Logo Kancelarii Adwokackiej Siemianowski', 'Siemianowski law firm logo'],
    'logo-siemianowski' => ['Logo Kancelarii Adwokackiej Siemianowski', 'Siemianowski law firm logo'],
    'sfera-1' => $logo('Sfera Felicja Szałapska'),
    'logo-sfera' => $logo('Sfera Felicja Szałapska'),
    'raxo' => $logo('Raxo'),
    'client-raxo' => $logo('Raxo'),
    'proscarves' => $logo('Pro Scarves'),
    'client-proscarves' => $logo('Pro Scarves'),
    'prisma_dent' => $logo('PrismaDent'),
    'logo-prisma-dent' => $logo('PrismaDent'),
    'polerstone' => $logo('Polerstone'),
    'client-polerstone' => $logo('Polerstone'),
    'piotr_mazur' => ['Logo Piotr Mazur Adwokat', 'Piotr Mazur law office logo'],
    'logo-piotr-mazur' => ['Logo Piotr Mazur Adwokat', 'Piotr Mazur law office logo'],
    'kaminski' => $logo('Kaminski & Sons'),
    'logo-kaminski' => $logo('Kaminski & Sons'),
    'gravia' => $logo('Gravia'),
    'client-gravia' => $logo('Gravia'),
    'ap' => $logo('Apartament Piękna'),
    'logo-ap' => $logo('Apartament Piękna'),
    'apartament-logo' => $logo('Apartament Piękna'),
    'logo-eat-tasty' => $logo('Eat Tasty'),
    'krawiec-logo' => $logo('Krawiec z dojazdem'),
    // Tools and platforms.
    'google_logo' => $logo('Google'),
    'review-google' => $logo('Google'),
    'fb_logo' => $logo('Facebook'),
    'review-facebook' => $logo('Facebook'),
    'google-ads-logo' => $logo('Google Ads'),
    'manage-wp-logo' => $logo('ManageWP'),
    'elementorpro-logo' => $logo('Elementor Pro'),
    'tool-elementor' => $logo('Elementor'),
    'copilot-logo-2' => $logo('Copilot'),
    'meta-ads-logo' => $logo('Meta Ads'),
    'meta-ads-logo-1' => $logo('Meta Ads'),
    'google-search-console-logo' => $logo('Google Search Console'),
    'tool-gsc' => $logo('Google Search Console'),
    'pagespeed-insights-logo' => $logo('PageSpeed Insights'),
    'tech-figma' => $logo('Figma'),
    'tech-wordpress' => $logo('WordPress'),
    'wordpress_blue_logo.svg-1' => $logo('WordPress'),
    'tech-woocommerce' => $logo('WooCommerce'),
    'tech-php' => $logo('PHP'),
    'adobe-illustrator' => $logo('Adobe Illustrator'),
    'adobe-indesign' => $logo('Adobe InDesign'),
    'trustindex' => ['Znak weryfikacji Trustindex', 'Trustindex verification badge'],
  ];
  // Industry pictures: the menu circle, the tile cover and the hero show the same scene.
  $industries = [
    'kancelarie' => [['Prawniczka w rozmowie z klientem w kancelarii', 'A lawyer talking with a client in a law office'],
      ['Prawnik i prawniczka w garniturach z teczką i tabletem', 'Two lawyers in suits with a folder and a tablet'],
      ['Prawnik i prawniczka z teczką i tabletem', 'Two lawyers with a folder and a tablet'],
      ['Prawniczka i prawnik przeglądają dokumenty', 'Two lawyers reviewing documents']],
    'lekarze' => [['Lekarka omawia wyniki z pacjentem w gabinecie', 'A doctor discussing results with a patient'],
      ['Lekarka i lekarz w białych fartuchach z tabletem', 'Two doctors in white coats with a tablet'],
      ['Lekarka i lekarz ze stetoskopem i tabletem', 'Two doctors with a stethoscope and a tablet'],
      ['Lekarka z notatnikiem i lekarz ze stetoskopem', 'A doctor with a clipboard and a doctor with a stethoscope']],
    'deweloperzy' => [['Doradcy dewelopera przy makiecie osiedla', 'Property developer advisers at a housing estate model'],
      ['Deweloper z tabletem i architektka z kaskiem i planami', 'A developer with a tablet and an architect with a hard hat and plans'],
      ['Architektka z planem osiedla i deweloper z kaskiem', 'An architect with an estate plan and a developer with a hard hat'],
      ['Deweloper z planami budynku i architektka z tabletem', 'A developer with building plans and an architect with a tablet']],
    'beauty' => [['Makijaż klientki w salonie beauty', 'A client having her make-up done in a beauty salon'],
      ['Kosmetyczki z salonu beauty z tabletem i kosmetykiem', 'Beauty salon therapists with a tablet and a cosmetic'],
      ['Kosmetyczki z lusterkiem i pędzlami do makijażu', 'Beauty therapists with a mirror and make-up brushes'],
      ['Kosmetyczka z serum i stylistka z tabletem', 'A beauty therapist with a serum and a stylist with a tablet']],
    'trenerzy' => [['Trening personalny na siłowni', 'A personal training session at the gym'],
      ['Trenerzy personalni w strojach sportowych', 'Personal trainers in sportswear'],
      ['Trenerzy personalni z bidonem, matą i tabletem', 'Personal trainers with a bottle, a mat and a tablet'],
      ['Trenerzy personalni z ręcznikiem i tabletem', 'Personal trainers with a towel and a tablet']],
    'fotografowie' => [['Fotograf podczas sesji w studiu', 'A photographer during a studio shoot'],
      ['Fotograf z aparatem i asystentka z tabletem', 'A photographer with a camera and an assistant with a tablet'],
      ['Fotografka z tabletem i fotograf z aparatem', 'A photographer with a tablet and a photographer with a camera'],
      ['Fotografka z aparatem i fotograf ze statywem', 'A photographer with a camera and a photographer with a tripod']],
    'restauracje' => [['Właściciel restauracji i kelnerka z tabletem', 'A restaurant owner and a waitress with a tablet'],
      ['Menedżerka restauracji z tabletem i barista z kawą', 'A restaurant manager with a tablet and a barista with coffee'],
      ['Kelnerka z tacą i menedżer restauracji z tabletem', 'A waitress with a tray and a restaurant manager with a tablet'],
      ['Cukierniczka z deserem i barista z kawą', 'A pastry chef with a dessert and a barista with coffee']],
    'hotele' => [['Goście przy recepcji hotelu', 'Guests at a hotel reception desk'],
      ['Recepcjonistka z tabletem i menedżer hotelu z kartą do pokoju', 'A receptionist with a tablet and a hotel manager with a key card'],
      ['Pokojowa z ręcznikami i menedżer hotelu z tabletem', 'A housekeeper with towels and a hotel manager with a tablet'],
      ['Recepcjonistka i concierge z walizką gościa', 'A receptionist and a concierge with a guest suitcase']],
  ];
  foreach ($industries as $slug => $pictures) {
    $map[$slug . '-cover'] = $map[$slug . '-krag'] = $map[$slug . '-hero'] = $pictures[0];
    $map[$slug . '-ludzie-1'] = $pictures[1];
    $map[$slug . '-ludzie-2'] = $pictures[2];
    $map[$slug . '-ludzie-3'] = $pictures[3];
  }
  return $map;
}

/** The description for an image address, or '' when the image is not one of the plugin's own. */
function zp_suite_audit_alt_text(string $src, bool $en): string {
  $map = zp_suite_audit_alt_map();
  $key = zp_suite_audit_alt_key($src);
  if (!isset($map[$key])) { return ''; }
  return $map[$key][$en ? 1 : 0];
}

/** True for the English pages: the language module's own answer, else <html lang="en…">. */
function zp_suite_audit_html_is_en(string $html): bool {
  if (function_exists('zpl_language')) { return zpl_language() === 'en'; }
  return (bool) preg_match('~<html\b[^>]*\blang\s*=\s*["\']?en\b~i', substr($html, 0, 4096));
}

/**
 * Gives every image in the markup its description. Script, style, textarea and comment contents are left as they
 * are; <template> and <noscript> markup is included. One pass with string splicing: the Tag Processor rewrites the
 * whole document on every changed tag, which cost about 90 ms for the 140 images of the home page.
 */
function zp_suite_audit_alt_html(string $html): string {
  if (stripos($html, '<img') === false) { return $html; }
  $parts = preg_split('~(<script\b[^>]*>.*?</script\s*>|<style\b[^>]*>.*?</style\s*>|<textarea\b[^>]*>.*?</textarea\s*>|<!--.*?-->)~is', $html, -1, PREG_SPLIT_DELIM_CAPTURE);
  if (!is_array($parts)) { return $html; }
  $en = zp_suite_audit_html_is_en($html);
  foreach ($parts as $i => $part) {
    if ($i % 2 === 1 || stripos($part, '<img') === false) { continue; }
    $done = preg_replace_callback('~<img\b[^>]*>~i', static function (array $m) use ($en): string {
      return zp_suite_audit_img_tag($m[0], $en);
    }, $part);
    if (is_string($done)) { $parts[$i] = $done; }
  }
  return implode('', $parts);
}

/**
 * One <img> tag with its description filled. An existing real description stays. "Zaprojektowani.com — google logo"
 * (a file name the 2.2 plugin wrote into the media library) is replaced where a written description exists. The
 * portfolio dialog's empty preview <img> (its script sets the address and the project's alt on click) gets a name.
 * Where the template had alt="" the picture also gets aria-hidden="true", so screen readers keep skipping it.
 */
function zp_suite_audit_img_tag(string $tag, bool $en): string {
  if (!preg_match_all('~\s([a-zA-Z_:][-a-zA-Z0-9_:.]*)(?:\s*=\s*("[^"]*"|\'[^\']*\'|[^\s"\'=<>`]+))?~', substr($tag, 4), $found, PREG_SET_ORDER | PREG_OFFSET_CAPTURE)) {
    return $tag;
  }
  $attrs = [];
  foreach ($found as $a) {
    $name = strtolower($a[1][0]);
    if (isset($attrs[$name])) { continue; }
    $raw = isset($a[2]) ? $a[2][0] : '';
    $value = ($raw !== '' && ($raw[0] === '"' || $raw[0] === "'")) ? substr($raw, 1, -1) : $raw;
    $attrs[$name] = ['value' => html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8'), 'at' => $a[0][1] + 4, 'len' => strlen($a[0][0])];
  }
  $alt = isset($attrs['alt']) ? trim($attrs['alt']['value']) : null;
  if ($alt !== null && $alt !== '' && strpos($alt, 'Zaprojektowani.com — ') !== 0) { return $tag; }
  $src = '';
  foreach (['src', 'data-src', 'data-zp-mobile-src'] as $name) {
    $value = isset($attrs[$name]) ? trim($attrs[$name]['value']) : '';
    if ($value !== '' && stripos($value, 'data:') !== 0) { $src = $value; break; }
  }
  $hide = false;
  if ($src === '') {
    if (isset($attrs['src']) || ($attrs['id']['value'] ?? '') !== 'projectDialogImage') { return $tag; }
    $text = $en ? 'Project preview' : 'Podgląd projektu';
  } else {
    $text = zp_suite_audit_alt_text($src, $en);
    if ($text === '') { return $tag; }
    $hide = $alt === '' && !isset($attrs['aria-hidden']);
  }
  $new = ' alt="' . esc_attr($text) . '"' . ($hide ? ' aria-hidden="true"' : '');
  if (isset($attrs['alt'])) {
    return substr($tag, 0, $attrs['alt']['at']) . $new . substr($tag, $attrs['alt']['at'] + $attrs['alt']['len']);
  }
  return '<img' . $new . substr($tag, 4);
}

/** Adds rel="nofollow" (and noopener in a new tab) to parameter links and contact-tool links; see the top of this file. */
function zp_suite_audit_links_html(string $html): string {
  if (stripos($html, '<a') === false) { return $html; }
  $parts = preg_split('~(<script\b[^>]*>.*?</script\s*>|<style\b[^>]*>.*?</style\s*>|<textarea\b[^>]*>.*?</textarea\s*>|<!--.*?-->)~is', $html, -1, PREG_SPLIT_DELIM_CAPTURE);
  if (!is_array($parts)) { return $html; }
  $host = strtolower((string) wp_parse_url(home_url('/'), PHP_URL_HOST));
  foreach ($parts as $i => $part) {
    if ($i % 2 === 1 || stripos($part, '<a') === false) { continue; }
    // A link's content ends at its own closing tag, also when written "</a\n>", and never runs into the next link.
    $done = preg_replace_callback('~(<a\s[^>]*>)([^<]*+(?:<(?!a\s|/a\s*>)[^<]*+)*+</a\s*>)?~is', static function (array $m) use ($host): string {
      return zp_suite_audit_link_tag($m[1], $host, (string) ($m[2] ?? '')) . ($m[2] ?? '');
    }, $part);
    if (is_string($done)) { $parts[$i] = $done; }
  }
  return implode('', $parts);
}

/** Whether a link should not pass ranking: a Studio Wyceny preset on this site, or a chat, map or review tool. */
function zp_suite_audit_link_nofollow(string $href, string $host): bool {
  if ($href === '' || $href[0] === '#') { return false; }
  $url = wp_parse_url($href);
  if (!is_array($url)) { return false; }
  $to = strtolower((string) ($url['host'] ?? ''));
  $path = (string) ($url['path'] ?? '');
  if ($to === '' || $to === $host || in_array($to, ['zaprojektowani.com', 'www.zaprojektowani.com'], true)) {
    return isset($url['query']) && preg_match('~(?:^|&)zpbs_~', (string) $url['query']) === 1;
  }
  if (in_array($to, ['wa.me', 'api.whatsapp.com', 'm.me', 'maps.app.goo.gl', 'g.page', 'maps.google.com'], true)) { return true; }
  if ($to === 'search.google.com') { return strpos($path, '/local') === 0; }
  return (bool) preg_match('~(?:^|\.)google\.(?:com|pl)$~', $to) && strpos($path, '/maps') === 0;
}

/**
 * The final address of an internal Polish link that leads to a redirect, or null. $text is the link's content: the
 * old /logo-branding/ address goes to logo design when the link speaks of the logo alone, else to visual identity.
 */
function zp_suite_audit_link_target(string $href, string $host, string $text): ?string {
  static $exact = null, $base = '', $categories = [];
  if ($exact === null) {
    $exact = (function_exists('zp_seo_plan_active') && zp_seo_plan_active()) ? (array) (zp_seo_plan_data('redirects')['exact'] ?? []) : [];
    $base = rtrim((string) wp_parse_url(home_url('/'), PHP_URL_PATH), '/');
  }
  if ($href === '' || $href[0] === '#' || strpos($href, '?') !== false) { return null; }
  $url = wp_parse_url($href);
  if (!is_array($url) || !isset($url['path']) || isset($url['query'])) { return null; }
  $to = strtolower((string) ($url['host'] ?? ''));
  if ($to !== '' && $to !== $host && !in_array($to, ['zaprojektowani.com', 'www.zaprojektowani.com'], true)) { return null; }
  // The same normalization as zp_seo_plan_path(), without its per-call home_url().
  $path = (string) $url['path'];
  if ($base !== '' && strpos($path, $base) === 0) { $path = substr($path, strlen($base)); }
  $path = (string) preg_replace('~/{2,}~', '/', '/' . ltrim(rawurldecode($path), '/'));
  if ($path !== '/' && substr($path, -1) !== '/' && !preg_match('~\.[a-z0-9]{2,5}$~i', $path)) { $path .= '/'; }
  $final = null;
  if ($exact && $path === '/logo-branding/' && preg_match('~\blogo~iu', $words = wp_strip_all_tags($text)) && !preg_match('~brand|identyfikac|wizualn~iu', $words)) {
    $final = '/projektowanie-logo/';
  } elseif (isset($exact[$path]) && function_exists('zp_seo_plan_redirect_final')) {
    $next = zp_seo_plan_redirect_final($path);
    if ($next !== $path && strpos($next, '/') === 0 && strpos($next, '/wp-') !== 0) { $final = $next; }
  } elseif (preg_match('~^/category/([^/]+)/$~', $path, $c) && function_exists('zp_suite_wiedza_group_for_term')) {
    if (!array_key_exists($c[1], $categories)) {
      $categories[$c[1]] = null;
      $term = get_term_by('slug', $c[1], 'category');
      $group = $term ? zp_suite_wiedza_group_for_term($term) : null;
      if ($group && !empty($group['primary_term_id']) && (int) $group['primary_term_id'] !== (int) $term->term_id) {
        $link = get_category_link((int) $group['primary_term_id']);
        if (is_string($link) && $link !== '') { $categories[$c[1]] = zp_seo_plan_path($link); }
      }
    }
    $final = $categories[$c[1]];
  }
  if ($final === null || $final === $path) { return null; }
  $fragment = isset($url['fragment']) ? '#' . $url['fragment'] : '';
  return ($to !== '' ? home_url($final) : $final) . $fragment;
}

/**
 * 14.10.2026: a link to Studio wyceny with a preset (?zpbs_service=web&zpbs_package=…) gets the same values after # instead
 * (/studio-wyceny/#zpbs_service=web&zpbs_package=…), which the Studio reads since thread 23's change (zp_studio_ux_310 in
 * includes/studio-wyceny.php): the address has no parameters any more and one URL is left for Google. zpbs_service goes
 * first, because the Studio looks for "#zpbs_service=". Nothing reads zpbs_source and zpbs_step; they stay after # too.
 */
function zp_suite_audit_studio_hash(string $href, string $host): ?string {
  if (strpos($href, 'zpbs_') === false || !function_exists('zp_studio_ux_310')) { return null; }
  $url = wp_parse_url($href);
  if (!is_array($url) || !isset($url['path'], $url['query'])) { return null; }
  $to = strtolower((string) ($url['host'] ?? ''));
  if ($to !== '' && $to !== $host && !in_array($to, ['zaprojektowani.com', 'www.zaprojektowani.com'], true)) { return null; }
  if (!preg_match('~^/(?:studio-wyceny|en/quote-studio)/?$~', (string) $url['path'])) { return null; }
  if (isset($url['fragment']) && $url['fragment'] !== 'zpbsUltimate') { return null; }
  // On the Studio page itself a link after # would not reload the page, and the Studio reads the preset only on load.
  $here = (string) wp_parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH);
  if (rtrim($here, '/') === rtrim((string) $url['path'], '/')) { return null; }
  $first = []; $rest = [];
  foreach (explode('&', (string) $url['query']) as $pair) {
    if ($pair === '') { continue; }
    if (!preg_match('~^zpbs_[a-z]+=~', $pair)) { return null; }
    if (strpos($pair, 'zpbs_service=') === 0) { $first[] = $pair; } else { $rest[] = $pair; }
  }
  if (!$first && !$rest) { return null; }
  $path = rtrim((string) $url['path'], '/') . '/';
  return ($to !== '' ? home_url($path) : $path) . '#' . implode('&', array_merge($first, $rest));
}

/** One <a> tag with its final address (see above) and the rel tokens it needs merged into its rel attribute. */
function zp_suite_audit_link_tag(string $tag, string $host, string $text = ''): string {
  if (stripos($tag, 'href') === false || !preg_match_all('~\s([a-zA-Z_:][-a-zA-Z0-9_:.]*)(?:\s*=\s*("[^"]*"|\'[^\']*\'|[^\s"\'=<>`]+))?~', substr($tag, 2), $found, PREG_SET_ORDER | PREG_OFFSET_CAPTURE)) {
    return $tag;
  }
  $attrs = [];
  foreach ($found as $a) {
    $name = strtolower($a[1][0]);
    if (isset($attrs[$name])) { continue; }
    $raw = isset($a[2]) ? $a[2][0] : '';
    $value = ($raw !== '' && ($raw[0] === '"' || $raw[0] === "'")) ? substr($raw, 1, -1) : $raw;
    $attrs[$name] = ['value' => html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8'), 'at' => $a[0][1] + 2, 'len' => strlen($a[0][0])];
  }
  if (!isset($attrs['href'])) { return $tag; }
  $final = zp_suite_audit_studio_hash(trim($attrs['href']['value']), $host) ?? zp_suite_audit_link_target(trim($attrs['href']['value']), $host, $text);
  if ($final !== null) {
    // A rewritten link is internal and has no query, so it never needs nofollow.
    $at = $attrs['href']['at'];
    return substr($tag, 0, $at) . ' href="' . esc_url($final) . '"' . substr($tag, $at + $attrs['href']['len']);
  }
  if (!zp_suite_audit_link_nofollow(trim($attrs['href']['value']), $host)) { return $tag; }
  $rel = isset($attrs['rel']) ? preg_split('~\s+~', strtolower(trim($attrs['rel']['value'])), -1, PREG_SPLIT_NO_EMPTY) : [];
  $want = ['nofollow'];
  if (strtolower(trim($attrs['target']['value'] ?? '')) === '_blank') { $want[] = 'noopener'; }
  $add = array_diff($want, $rel);
  if (!$add) { return $tag; }
  $new = ' rel="' . esc_attr(implode(' ', array_merge($rel, $add))) . '"';
  if (isset($attrs['rel'])) {
    return substr($tag, 0, $attrs['rel']['at']) . $new . substr($tag, $attrs['rel']['at'] + $attrs['rel']['len']);
  }
  return '<a' . $new . substr($tag, 2);
}
