<?php
/** Topic-aware links inside the existing article service widget. Display only. */
if (!defined('ABSPATH')) { exit; }

/** Work from the Polish source path even when a permalink filter returns English. */
function zp_seo_connections_source(string $path): string {
    $path = zp_seo_plan_path($path);
    if (strpos($path, '/en/') === 0 && class_exists('\\ZPL\\Router')) {
        return (string) (\ZPL\Router::reverse()[$path] ?? '');
    }
    return $path;
}

function zp_seo_connections_english(): bool {
    return zp_seo_plan_is_en() || (function_exists('zpl_language') && zpl_language() === 'en');
}

/** No guessed /en/ slugs and no cross-language fallback. */
function zp_seo_connections_url(string $path, bool $english): string {
    $url = home_url($path);
    if (!$english) { return $url; }
    if (!function_exists('zpl_url')) { return ''; }
    $url = (string) zpl_url($url, 'en');
    return strpos((string) wp_parse_url($url, PHP_URL_PATH), '/en/') === 0 ? $url : '';
}

/** Project write-ups already have their own service and related-project navigation. */
function zp_seo_connections_is_case(string $source): bool {
    if (strpos($source, '/case-studies/') === 0 || strpos($source, '/case-study/') === 0) { return true; }
    if ($source === '/strony-internetowe/strona-internetowa-polerstone/') { return true; }
    if (function_exists('zp_cs18_data')) {
        foreach (zp_cs18_data() as $case) {
            if ($source === ($case['path'] ?? '')) { return true; }
        }
    }
    return false;
}

/**
 * Slug intent precedes historical taxonomy: several web guides live in the shop
 * category, and WooCommerce guides also live in the website category. Category
 * fallback covers future articles without changing their stored content or terms.
 */
function zp_seo_connections_topic(string $source, array $categories = []): string {
    $source = zp_seo_connections_source($source);
    if ($source === '' || zp_seo_connections_is_case($source)
        || preg_match('~^/(?:en|oferty|briefy|category|tag|author|wp-json)/~', $source)) { return ''; }
    $parts = explode('/', trim($source, '/'));
    if (count($parts) < 2) { return ''; }
    $slug = end($parts);
    $categories[] = $parts[0];
    $has = static function (string $pattern) use ($slug): bool { return (bool) preg_match('~' . $pattern . '~i', $slug); };
    if (in_array('case-study', $categories, true)) { return ''; }
    if (in_array('logo-branding', $categories, true)) {
        if ($has('remarketing|reklam|google-ads|facebook-ads|meta-ads')) { return 'ads'; }
        return $has('key-visual|papier-firmowy|wizytow|identyfikac|branding|brandbook|brand-book|ksiega-znaku|kolory|typografi|strategia-marki|archetyp|nazwe-firmy|teczki|gadzety|kalendarze|mockup') ? 'identity' : 'logo';
    }
    if ($has('woocommerce|prestashop|shopify|sklep|e-commerce|koszyk|bramka-platnicza|karta-produktu|kategorie-produktow|merchant-center')) { return 'shop'; }
    if ($has('landing|landingi')) { return 'landing'; }
    if ($has('utrzymanie-strony|opieka-wordpress|wordpress-nie-|aktualizac|kopi[ae]-zapas|bezpieczenstwo-wordpress')) { return 'care'; }
    if ($has('google-ads|facebook-ads|meta-ads|remarketing|reklama-na-instagramie|reklamy-na-facebooku|kampani[ae]')) { return 'ads'; }
    if ($has('wordpress')) { return 'wordpress'; }
    if ($has('stron[ayie]|witryn|seo|konwersj|rezerwac|teksty-na')) { return 'web'; }
    foreach ($categories as $category) {
        if ($category === 'sklepy-internetowe') { return 'shop'; }
        if (in_array($category, ['meta-ads','kampanie-reklamowe','kampanie-reklamowe-meta-ads','kampanie-meta-ads','reklamy'], true)) { return 'ads'; }
        if (in_array($category, ['strony-internetowe','strony-www','ux-cro-analityka','seo-i-konwersja','seo-content-marketing','seo','poradniki','wiedza-strony-internetowe-seo-i-konwersja'], true)) { return 'web'; }
    }
    return '';
}

function zp_seo_connections_primary(string $topic): string {
    return [
        'shop'=>'/tworzenie-sklepow-internetowych/', 'identity'=>'/identyfikacja-wizualna/',
        'logo'=>'/projektowanie-logo/', 'web'=>'/tworzenie-stron-internetowych/',
        'wordpress'=>'/strony-wordpress/', 'care'=>'/opieka-wordpress/',
        'landing'=>'/tworzenie-landing-page/', 'ads'=>'/kampanie-reklamowe/',
    ][$topic] ?? '';
}

/** Reviewed English UI for this widget, independent of cached page dictionaries. */
function zp_seo_connections_profile(string $path, array $meta, bool $english): array {
    if (!$english) { return $meta; }
    $profiles = [
        '/tworzenie-stron-internetowych/'=>['Website development','Websites','We design and build websites around your offer, combining clear content, UX/UI and WordPress to make it easier for customers to contact you.'],
        '/tworzenie-sklepow-internetowych/'=>['Online store development','E-commerce','A WooCommerce store built around your products: categories, product pages, basket, payments, delivery and integrations.'],
        '/projektowanie-logo/'=>['Logo design','Logo and branding','A logo shaped around your brand, with considered concepts, useful variants and final files for print and digital use.'],
        '/identyfikacja-wizualna/'=>['Visual identity','Brand identity','We bring your logo, colours, typography and materials together into a consistent identity for your website, social media and print.'],
        '/kampanie-reklamowe/'=>['Digital advertising','Meta Ads + Google Ads','We plan campaigns, creative assets and conversion measurement around your offer and the action you want customers to take.'],
        '/strony-wordpress/'=>['WordPress websites','WordPress','A custom website with an editor you can use yourself, designed with performance, SEO and future development in mind.'],
        '/opieka-wordpress/'=>['WordPress website care','Support and maintenance','Updates, backups, security checks and monitoring, with help for content changes and ongoing website maintenance.'],
        '/tworzenie-landing-page/'=>['Landing page design','Campaign landing pages','A page focused on one offer and one clear action, with content and a contact form that help visitors take the next step.'],
        '/strona-wizytowka/'=>['Business card website','Business websites','A clear introduction to your business, services and contact details, with a lightweight layout that works on mobile.'],
    ];
    $aliases = ['/strony-internetowe-katowice/'=>'/tworzenie-stron-internetowych/', '/sklepy-internetowe-katowice/'=>'/tworzenie-sklepow-internetowych/', '/logo-branding-katowice/'=>'/projektowanie-logo/'];
    $key = $aliases[$path] ?? $path;
    $industry = [
        '/strony-internetowe-dla-kancelarii/'=>'Websites for law firms', '/strony-internetowe-dla-lekarzy/'=>'Websites for doctors',
        '/strony-internetowe-dla-deweloperow/'=>'Websites for property developers', '/strony-internetowe-dla-fotografow/'=>'Websites for photographers',
        '/strony-internetowe-dla-hoteli/'=>'Websites for hotels', '/strony-internetowe-dla-restauracji/'=>'Websites for restaurants',
        '/strony-internetowe-dla-salonow-beauty/'=>'Websites for beauty salons', '/strony-internetowe-dla-trenerow-personalnych/'=>'Websites for personal trainers',
        '/agencja-reklamowa-katowice/'=>'Advertising agency in Katowice',
    ];
    $p = $profiles[$key] ?? (isset($industry[$key]) ? [$industry[$key], 'Services for your business', 'Explore the project scope, useful features and examples for your industry.'] : []);
    if (!$p) { return []; }
    return array_replace($meta, ['title'=>$p[0], 'label'=>$p[1], 'excerpt'=>$p[2]]);
}

/** One example, selected for what the published case actually demonstrates. */
function zp_seo_connections_case(string $source, string $topic): string {
    if ($topic === '') { return ''; }
    if ($topic === 'shop') { return 'swiat-grilli'; }
    if (preg_match('~beauty|kosmetycz|rezerwac~', $source)) { return 'apartament-piekna'; }
    if (preg_match('~kancelari|prawni|papier-firmowy|wizytow|teczki~', $source)) { return 'siemianowski'; }
    if (in_array($topic, ['identity','logo'], true)) { return 'siemianowski'; }
    if (in_array($topic, ['landing','ads'], true) || preg_match('~seo|konwersj|zapytan|formularz~', $source)) { return 'apartament-piekna'; }
    return 'altiora-linea';
}

/** Existing links in the actual article count; script examples and external URLs do not. */
function zp_seo_connections_has_link(string $html, string $url): bool {
    $html = (string) preg_replace('~<!--[\s\S]*?-->|<(script|style|template)\b[^>]*>[\s\S]*?</\1\s*>~i', '', $html);
    $target = rtrim((string) wp_parse_url($url, PHP_URL_PATH), '/') . '/';
    $host = strtolower((string) wp_parse_url(home_url('/'), PHP_URL_HOST));
    if (!preg_match_all('~<a\b[^>]*\bhref\s*=\s*(["\'])(.*?)\1~is', $html, $matches)) { return false; }
    foreach ($matches[2] as $href) {
        $parts = wp_parse_url(html_entity_decode($href, ENT_QUOTES, 'UTF-8'));
        if (!is_array($parts) || (isset($parts['scheme']) && !in_array(strtolower($parts['scheme']), ['http','https'], true))) { continue; }
        if (isset($parts['host']) && preg_replace('~^www\.~', '', strtolower($parts['host'])) !== preg_replace('~^www\.~', '', $host)) { continue; }
        if (rtrim((string) ($parts['path'] ?? ''), '/') . '/' === $target) { return true; }
    }
    return false;
}

function zp_seo_connections_case_note(string $source, string $topic, bool $english, string $content): string {
    $key = zp_seo_connections_case($source, $topic);
    if ($key === '' || !function_exists('zp_cs18_data') || !function_exists('zp_cs18_case_public') || !zp_cs18_case_public($key)) { return ''; }
    $case = zp_cs18_data()[$key] ?? [];
    $path = (string) ($case[$english ? 'en_path' : 'path'] ?? '');
    if ($path === '' || ($english && strpos($path, '/en/') !== 0)) { return ''; }
    $url = home_url($path);
    if (zp_seo_connections_has_link($content, $url)
        || ($english && zp_seo_connections_has_link($content, home_url((string) ($case['path'] ?? ''))))) { return ''; }
    $copy = [
        'swiat-grilli'=>['Zobacz, jak połączyliśmy kategorie produktów, płatności i dostawy w realizacji ', 'See how product categories, payments and delivery come together in our ', 'Świat Grilli', 'Świat Grilli case study'],
        'siemianowski'=>['Przykład spójnego logo, materiałów marki i strony znajdziesz w realizacji ', 'Explore a coordinated logo, brand materials and website in our ', 'kancelarii Siemianowski', 'Siemianowski law firm case study'],
        'apartament-piekna'=>['Zobacz stronę usługową z własną ścieżką zgłoszenia wizyty w realizacji ', 'Explore a service website with its own appointment request flow in our ', 'Apartament Piękna', 'Apartament Piękna case study'],
        'altiora-linea'=>['Zobacz, jak uporządkowaliśmy ofertę, katalogi i kontakt w realizacji ', 'See how we organised the offer, catalogues and contact options in our ', 'Altiora Linea', 'Altiora Linea case study'],
    ][$key];
    return '<p class="zpu-related__case" data-zp-article-case="' . esc_attr($key) . '">' . esc_html($copy[$english ? 1 : 0])
        . '<a href="' . esc_url($url) . '">' . esc_html($copy[$english ? 3 : 2]) . '</a>.</p>';
}

function zp_seo_connections_filter($content) {
    if (!is_string($content) || $content === '' || !zp_seo_plan_active() || is_admin() || !is_singular('post')
        || !in_the_loop() || !is_main_query() || is_feed() || is_preview() || wp_doing_ajax()
        || (defined('REST_REQUEST') && REST_REQUEST) || isset($_GET['elementor-preview'])
        || strpos($content, 'zpu-related') !== false) { return $content; }
    $id = (int) get_the_ID();
    if (!$id || $id !== (int) get_queried_object_id() || get_post_status($id) !== 'publish' || post_password_required($id)) { return $content; }
    $self = zp_seo_connections_source((string) get_permalink($id));
    $categories = (array) wp_get_post_categories($id, ['fields'=>'slugs']);
    $topic = zp_seo_connections_topic($self, $categories);
    if ($topic === '') { return $content; }
    $entry = zp_seo_plan_entry($self);
    $links = zp_seo_related_expand_links($entry['links'] ?? [], $id, $self, 2);
    $related = zp_seo_related_render($links, $self, $content, $topic);
    return $related === '' ? $content : $content . $related;
}
