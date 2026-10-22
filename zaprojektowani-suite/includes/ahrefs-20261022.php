<?php
/**
 * Ahrefs Site Audit of 21.10.2026 (338 pages): descriptions, titles and two large photos. The other fixes from that
 * crawl sit where the cause was: seo-plan/ai.php (business schema), seo-plan/posts.php (FAQ JSON-LD of one post),
 * seo-audit-20261013.php (links to old /wiedza/ addresses) and case-studies-render-20261018.php (screenshots).
 *
 * Descriptions and titles: 40 meta descriptions were too long (over 160 characters) or too short (under 100) and 5
 * titles were over 60, all but two of them on English pages. Tłumacz EN translates the Polish description, and the
 * English comes out shorter or longer; two Polish case study descriptions were 161 and 162 characters. A page keeps
 * its own text unless it is still out of that range; then it gets the one below (descriptions of 120 to 155
 * characters, titles of at most 60), on the same subject and in the same tone. English prices are written in PLN:
 * the English pages show them in dollars, as everywhere else (en-public-currency-20261011.php runs after this). A
 * Facebook or X copy that repeats the old text follows it, and a text from the language module's manual overrides
 * (zpl_overrides) stays as it is. Keys are the addresses Ahrefs listed; nothing is saved in WordPress.
 */
if (!defined('ABSPATH')) { exit; }

/** Requested address => new 'title' and/or 'description'. */
function zp_ahrefs_20261022_meta_rows(): array {
  return [
    '/case-studies/' => ['description' => 'Poznaj realizacje Zaprojektowani: Siemianowski, Apartament Piękna, Altiora Linea i Świat Grilli. Zakres, decyzje projektowe, autorskie funkcje i wdrożenia.'],
    '/strony-internetowe/strona-internetowa-altiora-linea/' => ['description' => 'Strona internetowa Altiora Linea od Zaprojektowani: WordPress, trzy języki, katalogi producentów i formularze. Realizacja dla marki wnętrzarskiej premium.'],
    '/en/websites-for-law-firms/' => ['description' => 'Websites for lawyers and law firms: practice areas in plain language, team profiles, consultation booking and content that follows professional ethics.'],
    '/en/about-us/' => ['description' => 'Marta, Mateusz and Stanisław: a Katowice studio designing websites, WooCommerce stores, logos and ad campaigns for businesses across Poland since 2022.'],
    '/en/logo-branding/company-letterhead-design/' => ['description' => 'Company letterhead step by step: what to include (also for a limited company), A4 size, layout, Word and Google Docs templates and a print-ready file.'],
    '/en/digital-advertising/' => ['description' => 'Facebook, Instagram and Google Ads campaigns for Katowice and all of Poland: strategy, creatives, landing pages and lead tracking. From PLN 1,200/month.'],
    '/en/websites-for-property-developers/' => ['description' => 'Websites for property developers: unit search and listings, transparent prices with history, location, specifications and mobile-friendly enquiry forms.'],
    '/en/quote-studio/' => ['description' => 'Websites from PLN 3,999, WooCommerce stores from PLN 6,499, logos from PLN 999, ad campaigns from PLN 1,200/mo. Answer a few questions and get a quote.'],
    '/en/ux-cro-analytics/online-store-design-and-customer-experience/' => ['description' => 'Online store customer experience depends on navigation, product pages, the cart and payments. See how UX helps customers choose and complete purchases.'],
    '/en/meta-ads/facebook-advertising-cost/' => ['title' => 'Facebook advertising cost: budgets, pricing and management', 'description' => 'How much does Facebook advertising cost? What drives the price, how to set a test budget and what campaign management costs (with us, from PLN 1,200/mo).'],
    '/en/advertising-campaigns/google-ads-cost/' => ['description' => 'How much does Google Ads cost? You pay per click and set the budget yourself: how Google charges, a test budget, and management from PLN 1,200/mo.'],
    '/en/meta-ads-campaigns/when-to-launch-facebook-ads-and-prepare-your-website/' => ['description' => 'Facebook Ads campaigns need a clear offer, measurement and a landing page. See what your business should prepare before spending its first ad budget.'],
    '/en/logo-branding/logo-vs-wordmark-symbol-differences-choice/' => ['description' => 'Logo, wordmark and symbol: the differences explained with examples from well-known brands, and when a business needs only a wordmark or also a symbol.'],
    '/en/logo-branding/build-brand-strategy-step-by-step/' => ['description' => 'A brand strategy defines who your company is for, what sets it apart and how it speaks: the 9 elements, a 7-step plan and an example for a small business.'],
    '/en/websites/business-website-audit-before-ads-and-seo/' => ['description' => 'Audit your business website before ads or SEO: find the issues that hurt conversions and stop spending budget on traffic that brings no inquiries.'],
    '/en/logo-branding/rebranding-without-losing-brand-recognition/' => ['description' => 'Rebranding without losing recognition means keeping key elements and planning the rollout. Review changes to your logo, messaging and business materials.'],
    '/en/ux-cro-analytics/mistakes-that-hurt-business-website-performance/' => ['description' => 'What makes a business website less effective: an unclear offer, UX issues, slow pages and forms. How to identify the problems and prioritise improvements.'],
    '/en/websites/when-to-redesign-your-business-website/' => ['description' => 'A redesign needs an assessment of your offer, UX, technology and results. Recognise the signs that change is needed and plan the scope and priorities.'],
    '/en/logo-branding/logo-branding-website-what-comes-first/' => ['description' => 'Logo, branding or website first? The order of implementation that avoids confusion, rework and inconsistencies between your brand, content and UX.'],
    '/en/online-stores/local-seo-katowice-how-to-attract-customers/' => ['description' => 'How local SEO in Katowice connects your website, Google Business Profile, reviews and service content. Plan your visibility in the city and around it.'],
    '/en/online-stores/custom-online-store/' => ['description' => 'When a bespoke online shop gives you more than a ready-made template, which businesses it suits, and how it affects SEO, performance and integrations.'],
    '/en/logo-branding/logo-registration-poland-eu-guide/' => ['description' => 'Logo registration step by step: checking similar marks, choosing classes, filing with the Polish Patent Office (UPRP) or EUIPO, costs and copyright.'],
    '/en/websites/facebook-page-vs-business-website/' => ['description' => 'When a Facebook business page needs the support of your own website, and how to divide services, communication and enquiry handling between the two.'],
    '/en/online-stores/beauty-business-website-sections-that-drive-inquiries/' => ['description' => 'A beauty business website brings together services, results, qualifications and reviews. How to make contact and booking easier without pushy selling.'],
    '/en/websites/create-website-business-builder-wordpress-agency/' => ['description' => 'How do you create a website for your business? Compare a website builder, WordPress and an agency: costs, time, domain and hosting, and what to prepare.'],
    '/en/logo-branding/clothing-brand-logo-labels-and-collections/' => ['description' => 'Find out how to distinguish the brand logo from collection marks on labels and in the shop, so that every collection is still recognisable as one brand.'],
    '/en/logo-branding/logo-t-shirt-simplified-embroidery-printing/' => ['description' => 'Losing detail in your T-shirt logo? See when to simplify it for embroidery or printing, and what to change in thin lines, small text and colours.'],
    '/en/seo-content-marketing/service-pages-and-landing-pages-for-google-ads/' => ['description' => 'Find out how to prepare a service page for Google Ads so clicks don’t go to waste: the offer, sections, CTAs, landing page and UX, step by step.'],
    '/en/websites/website-wireframes-mockups-prototypes-guide/' => ['description' => 'A website mock-up is a plan of the page layout created before the visual design. See how wireframes, mock-ups and prototypes differ and when to use each.'],
    '/en/websites/woocommerce-vs-prestashop/' => ['description' => 'WooCommerce or PrestaShop? Compare the catalogue, integrations, multistore, costs, maintenance and the role of content before choosing a store platform.'],
    '/en/logo-branding/business-visual-identity-essentials/' => ['description' => 'Discover the elements of visual identity: logos, colours, typography and business materials, with examples and a plan for rolling out a consistent brand.'],
    '/en/logo-branding/logo-guidelines-examples-and-checklist/' => ['description' => 'Find out what logo guidelines include: logo variations, colours, clear space and usage rules, with examples and a checklist for signing off brand files.'],
    '/en/logo-branding/business-rebranding/' => ['description' => 'Find out what rebranding is, when to change your brand and how to plan the process: the stages, the risks, and a logo refresh versus a full change.'],
    '/en/logo-branding/logo-company-folders-design-print-die-line/' => ['title' => 'Branded presentation folders: design, pockets and dieline', 'description' => 'How do you order folders with your company logo? Design, the documents they hold, pockets and the printer’s dieline: what to agree before printing.'],
    '/en/ux-cro-analytics/user-centered-web-design/' => ['description' => 'User-focused website design: how UX affects navigation, forms, mobile experience and conversions, and the basic principles worth applying first.'],
    '/en/logo-branding/branded-merchandise-logo-color-differences/' => ['description' => 'Compare the logo colours on promotional items from different suppliers: why samples differ and how to check them before you order the whole batch.'],
    '/en/websites/online-booking-system-website/' => ['description' => 'Online booking system for your website: an app, widget, WordPress plugin, or custom-built system? Compare costs, features and day-to-day upkeep.'],
    '/en/logo-branding/vector-logo-essential-logo-files-uses/' => ['description' => 'A vector logo can be enlarged without losing quality. Which logo files you need (SVG, PDF, EPS, PNG) and where to use each, from print to the web.'],
    '/en/logo-branding/beauty-salon-logo-styles-colors-examples/' => ['description' => 'Beauty salon logo: styles, colours and typography, illustrated with examples from our work, and how to choose a mark that suits your salon and clients.'],
    '/en/logo-branding/how-to-choose-company-name-methods-examples/' => ['description' => 'A company name should be short, memorable, and available in official registers. Methods for finding one, examples, and checks before you register it.'],
    '/en/advertising-campaigns/google-ads-vs-meta-ads-for-service-businesses/' => ['title' => 'Google Ads or Meta Ads for a service business? How to choose'],
    '/en/logo-branding/branding-agency-vs-freelancer/' => ['title' => 'Branding agency or freelancer? Who should design your logo'],
    '/en/websites/website-redesign-without-losing-google-rankings/' => ['title' => 'Website redesign without losing Google rankings: SEO plan'],
  ];
}

/** The limits Ahrefs checks: a description of 100 to 160 characters, a title of at most 60. */
function zp_ahrefs_20261022_meta_fits(string $kind, string $text): bool {
  $length = mb_strlen(trim($text), 'UTF-8');
  return $kind === 'title' ? $length <= 60 : $length >= 100 && $length <= 160;
}

/** Filter zp_en_public_price_html (languages module, both languages), before the dollar prices. */
function zp_ahrefs_20261022_meta_html(string $html, string $lang, string $source): string {
  if (!class_exists('ZPL\\Router') || !class_exists('ZPL\\Html') || !class_exists('ZPL\\Dict')) { return $html; }
  $path = \ZPL\Router::norm_path(\ZPL\Router::rel((string) (wp_parse_url(\ZPL\Router::$external, PHP_URL_PATH) ?: '/')));
  $row = zp_ahrefs_20261022_meta_rows()[$path] ?? null;
  if ($row === null || ($lang === 'en') !== (strpos($path, '/en/') === 0)) { return $html; }
  if (!preg_match('~<head\b[^>]*>.*?</head>~is', $html, $head, PREG_OFFSET_CAPTURE)) { return $html; }
  $part = $head[0][0];
  $kept = $lang === 'en' ? array_map('strval', array_values(\ZPL\Dict::overrides('en'))) : [];
  $old = ['title' => null, 'description' => null];

  if (isset($row['title']) && preg_match('~(<title\b[^>]*>)(.*?)</title>~is', $part, $t, PREG_OFFSET_CAPTURE)) {
    $now = trim(\ZPL\Html::decode($t[2][0]));
    if (!zp_ahrefs_20261022_meta_fits('title', $now) && !in_array($now, $kept, true)) {
      $old['title'] = $now;
      $part = substr_replace($part, esc_html($row['title']), $t[2][1], strlen($t[2][0]));
    }
  }
  if (isset($row['description']) && preg_match_all('~<meta\b[^>]*>~i', $part, $tags)) {
    foreach ($tags[0] as $tag) {
      $a = \ZPL\Html::attrs($tag);
      if (strtolower($a['name'] ?? '') !== 'description' || !isset($a['content'])) { continue; }
      $now = trim($a['content']);
      if (!zp_ahrefs_20261022_meta_fits('description', $now) && !in_array($now, $kept, true)) { $old['description'] = $now; }
      break;
    }
  }
  if ($old['title'] === null && $old['description'] === null) { return $html; }

  $social = ['title' => ['og:title', 'twitter:title'], 'description' => ['description', 'og:description', 'twitter:description']];
  $part = preg_replace_callback('~<meta\b[^>]*>~i', static function ($m) use ($row, $old, $social) {
    $a = \ZPL\Html::attrs($m[0]);
    $key = strtolower((string) ($a['name'] ?? $a['property'] ?? ''));
    foreach ($social as $kind => $keys) {
      if ($old[$kind] === null || !isset($a['content']) || !in_array($key, $keys, true)) { continue; }
      if ($key === 'description' || trim($a['content']) === $old[$kind]) { return \ZPL\Html::set_attrs($m[0], ['content' => $row[$kind]]); }
    }
    return $m[0];
  }, $part) ?? $part;
  return substr_replace($html, $part, $head[0][1], strlen($head[0][0]));
}

add_filter('zp_en_public_price_html', 'zp_ahrefs_20261022_meta_html', 5, 3);

/**
 * "Image file size too large" (2.2 and 2.3 MB): the post /kampanie-reklamowe/google-ads-czy-meta-ads-firma-uslugowa/
 * and its English page show two PNG photos with the full-size file in src. Browsers take a file from srcset and never
 * load src next to it, so src becomes the 768px copy that srcset already lists (about 650 KB); the page looks and
 * loads the same. Only these two files, and only when their srcset lists that copy. Called from the final HTML
 * (zp_suite_quality_html), after optimizer.php has put the full-size file back in src and added the srcset.
 */
function zp_ahrefs_20261022_image_src_html(string $html): string {
  $files = [
    '/wp-content/uploads/2026/09/meta-ads-efekt-uslugi.png' => '/wp-content/uploads/2026/09/meta-ads-efekt-uslugi-768x512.png',
    '/wp-content/uploads/2026/09/jakosc-leada-rozmowa-sprzedazowa.png' => '/wp-content/uploads/2026/09/jakosc-leada-rozmowa-sprzedazowa-768x512.png',
  ];
  $found = false;
  foreach ($files as $full => $copy) { if (strpos($html, $full) !== false) { $found = true; break; } }
  if (!$found || !class_exists('WP_HTML_Tag_Processor')) { return $html; }
  $tags = new WP_HTML_Tag_Processor($html);
  while ($tags->next_tag('IMG')) {
    $src = (string) $tags->get_attribute('src');
    $copy = $files[(string) wp_parse_url($src, PHP_URL_PATH)] ?? null;
    if ($copy === null) { continue; }
    foreach (preg_split('~,(?=\s*\S)~', (string) $tags->get_attribute('srcset')) ?: [] as $candidate) {
      $url = (string) (preg_split('~\s+~', trim($candidate))[0] ?? '');
      if ($url !== '' && (string) wp_parse_url($url, PHP_URL_PATH) === $copy) { $tags->set_attribute('src', $url); break; }
    }
  }
  return $tags->get_updated_html();
}
