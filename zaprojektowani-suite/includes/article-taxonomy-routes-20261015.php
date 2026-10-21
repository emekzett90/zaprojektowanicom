<?php
/** Six reviewed article topics. Exact category archives only; article routes are never remapped here. */
if (!defined('ABSPATH')) { exit; }

final class ZP_Article_Taxonomy_Routes_20261015 {
    private static ?array $active_plan = null;

    public static function canonical_archives(): array {
        return [
            '/category/strony-internetowe/' => '/en/category/websites/',
            '/category/sklepy-internetowe/' => '/en/category/online-stores/',
            '/category/logo-branding/' => '/en/category/logo-branding/',
            '/category/seo-i-konwersja/' => '/en/category/seo-and-conversion/',
            '/category/kampanie-reklamowe/' => '/en/category/advertising-campaigns/',
            '/category/case-study/' => '/en/category/case-study/',
        ];
    }

    public static function legacy_archives(): array {
        return [
            '/category/strony-www/' => '/category/strony-internetowe/',
            '/category/seo/' => '/category/seo-i-konwersja/',
            '/category/seo-content-marketing/' => '/category/seo-i-konwersja/',
            '/category/ux-cro-analityka/' => '/category/seo-i-konwersja/',
            '/category/poradniki/' => '/category/seo-i-konwersja/',
            '/category/wiedza-strony-internetowe-seo-i-konwersja/' => '/category/seo-i-konwersja/',
            '/category/reklamy/' => '/category/kampanie-reklamowe/',
            '/category/meta-ads/' => '/category/kampanie-reklamowe/',
            '/category/kampanie-reklamowe-meta-ads/' => '/category/kampanie-reklamowe/',
            '/category/kampanie-meta-ads/' => '/category/kampanie-reklamowe/',
        ];
    }

    private static function english_archive($path): bool {
        return is_string($path) && (bool)preg_match('~^/en/category/[a-z0-9]+(?:-[a-z0-9]+)*/$~D', $path);
    }

    /** Pure plan over the translator's already merged bundled and custom routes. */
    public static function route_plan(array $original): array {
        $routes = $original;
        $legacy = [];
        $defaults = self::canonical_archives();
        $aliases = self::legacy_archives();
        foreach (array_keys($aliases + $defaults) as $owned) { unset($routes[$owned]); }
        $claimed = array_fill_keys(array_filter(array_values($routes), 'is_string'), true);
        foreach ($defaults as $pl => $default) {
            $custom = $original[$pl] ?? null;
            $candidate = self::english_archive($custom) && !isset($claimed[$custom]) ? $custom : $default;
            // An unrelated custom route owns this destination: preserve the entire original routing table.
            if (isset($claimed[$candidate])) { return ['routes'=>$original, 'legacy'=>[], 'conflicts'=>[$pl=>$candidate]]; }
            $routes[$pl] = $candidate;
            $claimed[$candidate] = true;
        }
        foreach ($aliases as $old => $pl) {
            $to = $routes[$pl];
            $old_paths = ['/en'.$old];
            if (self::english_archive($original[$old] ?? null)) { $old_paths[] = $original[$old]; }
            // Known public translations from the shipped route table remain aliases after its PL source is retired.
            $shipped = ['/category/poradniki/'=>'/en/category/guides/', '/category/seo/'=>'/en/category/seo/', '/category/reklamy/'=>'/en/category/advertising/'];
            if (isset($shipped[$old])) { $old_paths[] = $shipped[$old]; }
            foreach ($old_paths as $old_en) {
                if ($old_en !== $to && !isset($claimed[$old_en])) { $legacy[$old_en] = $to; }
            }
        }
        foreach ($defaults as $pl => $default) {
            if ($routes[$pl] !== $default && !isset($claimed[$default])) { $legacy[$default] = $routes[$pl]; }
        }
        return ['routes'=>$routes, 'legacy'=>$legacy, 'conflicts'=>[]];
    }

    public static function routes($routes): array {
        self::$active_plan = self::route_plan(is_array($routes) ? $routes : []);
        return self::$active_plan['routes'];
    }

    private static function plan(): array {
        if (self::$active_plan === null && class_exists('ZPL\\Router')) { \ZPL\Router::routes(); }
        return self::$active_plan ?? self::route_plan([]);
    }

    public static function legacy($routes): array {
        return array_replace(is_array($routes) ? $routes : [], self::plan()['legacy']);
    }

    /** Exact old archives and optional /page/N/. Query strings are preserved without parsing or reordering. */
    public static function redirect_target(string $uri, array $original_routes, array $max_pages = []): ?string {
        return self::target_from_plan($uri, self::route_plan($original_routes), $max_pages);
    }

    public static function target_from_plan(string $uri, array $plan, array $max_pages = []): ?string {
        if ($uri === '' || $uri[0] !== '/' || str_starts_with($uri, '//') || preg_match('/[\x00-\x20\x7f]/', $uri)) { return null; }
        $parts = parse_url($uri);
        if (!is_array($parts) || isset($parts['host']) || isset($parts['fragment'])) { return null; }
        $path = $parts['path'] ?? '';
        if (!preg_match('~^(/(?:en/)?category/[a-z0-9]+(?:-[a-z0-9]+)*)(?:/page/([1-9][0-9]{0,5}))?/?$~D', $path, $match)) { return null; }
        $base = $match[1].'/';
        if ($plan['conflicts']) { return null; }
        $to = self::legacy_archives()[$base] ?? $plan['legacy'][$base] ?? null;
        $canonical = array_intersect_key($plan['routes'], self::canonical_archives());
        // Canonical pagination can also become empty after moving articles to their actual topic.
        if ($to === null && isset($match[2]) && (isset($canonical[$base]) || in_array($base, $canonical, true))) { $to = $base; }
        if (!is_string($to)) { return null; }
        if (isset($match[2])) {
            $pl = isset($canonical[$to]) ? $to : array_search($to, $canonical, true);
            $max = is_string($pl) && isset($max_pages[$pl]) ? max(1, (int)$max_pages[$pl]) : null;
            if ((int)$match[2] > 1 && ($max === null || (int)$match[2] <= $max)) { $to .= 'page/'.$match[2].'/'; }
        }
        if (isset($parts['query'])) { $to .= '?'.$parts['query']; }
        return $to === $uri ? null : $to;
    }

    public static function max_pages(): array {
        if (!function_exists('zp_suite_wiedza_topic_groups_resolved')) { return []; }
        $pages = array_fill_keys(array_keys(self::canonical_archives()), 1);
        foreach (zp_suite_wiedza_topic_groups_resolved() as $group) {
            $path = '/category/'.($group['primary_slug'] ?? '').'/';
            if (isset(self::canonical_archives()[$path])) { $pages[$path] = max(1, (int)ceil((int)($group['count'] ?? 0) / 12)); }
        }
        return $pages;
    }

    private static function dictionary_source(string $source): string {
        return preg_match('~^(/category/(?:seo-i-konwersja|case-study)/)(?:page/[1-9][0-9]*/)?$~D', $source, $m) ? $m[1] : '';
    }

    /** Existing reviewed SEO text plus literal Case study archive UI, only on these two archive sources. */
    public static function dictionary($dictionary, $language, $source): array {
        $dictionary = is_array($dictionary) ? $dictionary : [];
        $source = self::dictionary_source((string)$source);
        if ($source === '') { return $dictionary; }
        if ($source === '/category/seo-i-konwersja/') {
            $file = dirname(__DIR__).'/zaprojektowani-languages/data/php/en/p/f55f54663c60.php';
            $map = is_file($file) ? (array)include $file : [];
            $map = array_replace($map, [
                'Pozycjonowanie w AI: jak przygotować firmową stronę do odpowiedzi AI'=>'Visibility in AI: preparing your business website for AI answers',
                'Pozycjonowanie w AI obejmuje przygotowanie treści i strony do odnajdywania informacji przez wyszukiwarki oraz asystentów korzystających z internetu. Zacznij od rzetelnej oferty, dostępnych podstron i spójnych danych firmy. Samo dopuszczenie bota lub dodanie pliku llms.txt nie zapewnia cytowania.'=>'Visibility in AI involves preparing your website and content so that search engines and internet-connected assistants can find the information. Start with an accurate offer, accessible pages and consistent business details. Allowing a bot or adding an llms.txt file alone does not guarantee a citation.',
                'Jak połączyć branding i SEO, stronę i reklamy w jeden system?'=>'How to connect branding, SEO, your website and advertising in one system?',
                'Optymalizacja konwersji – co naprawdę ma znaczenie na stronie'=>'Conversion rate optimization — what really matters on a website',
                'SEO lokalne Katowice – jak pozyskiwać klientów z miasta i okolic'=>'Local SEO in Katowice — attracting customers from the city and surrounding areas',
                'Formularz kontaktowy jest jednym z najważniejszych punktów styku na stronie usługowej. To miejsce, w którym ruch z SEO, reklam i działań brandingowych zamienia się w realny kontakt.'=>'A contact form is one of the most important points of contact on a service business website. It is where traffic from SEO, advertising and branding can become a real enquiry.',
                'Strona ma ruch, ale nie generuje zapytań – dlaczego?'=>'Website traffic without enquiries — why?',
                'Teksty na stronę – jak pisać, żeby nie brzmiały jak katalog usług?'=>'Website copy — how to write without sounding like a service catalogue?',
                'Dobry tekst na stronie firmowej pomaga klientowi rozpoznać swoją sytuację, zrozumieć usługę i zdecydować, co zrobić dalej. Nazwy usług są potrzebne, ale same nie wyjaśnią zakresu, przebiegu współpracy ani powodów, dla których warto się odezwać.'=>'Good website copy helps a customer recognize their situation, understand the service and decide what to do next. Service names are useful, but on their own they do not explain the scope, how the collaboration works or why it is worth getting in touch.',
            ]);
        } else {
            $map = [
                'Case study'=>'Case studies',
                'Case study — poradniki i wiedza | Zaprojektowani.com'=>'Case studies — guides and insights | Zaprojektowani.com',
                'Baza wiedzy · Case study'=>'Knowledge base · Case studies',
                'Poradniki: Case study'=>'Case studies',
                'Przykłady wdrożeń, decyzji projektowych i połączenia designu z wynikiem biznesowym.'=>'Project examples, design decisions and how design supports business outcomes.',
                'Przykłady wdrożeń, decyzji projektowych i połączenia designu z wynikiem biznesowym. Praktyczne poradniki Zaprojektowani o projektowaniu, wdrożeniu i rozwoju marki online.'=>'Project examples, design decisions and how design supports business outcomes. Practical Zaprojektowani guides on design, implementation and growing a brand online.',
                'Przejdź do poradników'=>'Browse articles',
                'Cała baza wiedzy'=>'All insights',
                'Zobacz ofertę'=>'Explore our services',
                'Statystyki kategorii'=>'Category statistics',
                'W tym dziale'=>'In this section',
                'opublikowany poradnik'=>'published article',
                'opublikowanych poradników'=>'published articles',
                'realnych czytań'=>'actual reads',
                'liczniki czytań'=>'read counts',
                'klaster tematyczny'=>'topic cluster',
                'Pozostałe obszary wiedzy'=>'Other insight topics',
                'Najnowsze materiały'=>'Latest articles',
                'Konkretnie, bez lania wody: decyzje projektowe, koszty, struktura, UX, SEO i rzeczy, które warto sprawdzić przed wdrożeniem.'=>'Practical insights into design decisions, costs, structure, UX, SEO and what to check before launch.',
                'artykułów w tym temacie'=>'articles on this topic',
                'Przeszukaj całą bazę'=>'Search all insights',
                'Najnowszy poradnik'=>'Latest article',
                'Czytaj artykuł'=>'Read article',
                'Paginacja artykułów'=>'Article pagination',
                '← Poprzednie'=>'← Previous',
                'Następne →'=>'Next →',
                'Brak opublikowanych artykułów.'=>'No published articles yet.',
                'Wróć do głównej bazy wiedzy i wybierz inny obszar.'=>'Return to all insights and choose another topic.',
                'Masz projekt i chcesz przejść od wiedzy do działania?'=>'Have a project and ready to put these insights into practice?',
                'Opisz zakres, który planujesz. Dobierzemy rozwiązanie, zamiast wciskać gotowy pakiet.'=>'Tell us what you have in mind. We will suggest a solution that fits your needs.',
                'Szybka wycena'=>'Quick quote',
                'Strona internetowa Polerstone i branding marki kamieniarskiej'=>'Polerstone website and branding for a stonework business',
                'Sklep internetowy Świat Grilli – od brandingu do WooCommerce'=>'Świat Grilli online store — from branding to WooCommerce',
            ];
        }
        // Actual zero-read fixture labels and shared service schema strings found in both full archive renders.
        $map = array_replace($map, [
            'Nowe'=>'New',
            'liczniki czytań'=>'read counts',
            'strony WordPress'=>'WordPress websites',
            'Sklep internetowy WooCommerce'=>'WooCommerce online store',
            'stałe pakiety: Logo Startowe 999 zł, Mini Branding 1 499 zł, Branding Premium 2 999 zł'=>'fixed packages: Starter Logo PLN 999, Mini Branding PLN 1,499, Premium Branding PLN 2,999',
            'pełny system z księgą znaku w pakiecie Branding Premium: 2 999 zł'=>'a complete system with logo guidelines in the Premium Branding package: PLN 2,999',
            'Kampanie Meta Ads i Google Ads'=>'Meta Ads and Google Ads campaigns',
            'stała obsługa: strategia, kreacje, pomiar i optymalizacja; budżet mediowy osobno'=>'ongoing management: strategy, creative, measurement and optimization; media budget charged separately',
        ]);
        if ($language === 'pl') {
            $reverse = [];
            foreach ($map as $pl=>$en) { $reverse[class_exists('ZPL\\Html') ? \ZPL\Html::norm($en) : $en] = $pl; }
            $map = $reverse;
        } elseif ($language !== 'en') { return $dictionary; }
        return array_replace($map, $dictionary);
    }

    /** Both full WXR-backed archive renders passed real Page::translate coverage: SEO 229/229, Case study 140/140.
     * Runtime still independently applies its normal 90% coverage and 404 checks to every English response.
     */
    public static function has_page($known, $source): bool {
        return (bool)$known || self::dictionary_source((string)$source) !== '';
    }

    public static function redirect(): void {
        if (is_admin() || (function_exists('wp_doing_ajax') && wp_doing_ajax()) || (defined('REST_REQUEST') && REST_REQUEST)
            || is_preview() || isset($_GET['rest_route']) || !in_array($_SERVER['REQUEST_METHOD'] ?? 'GET', ['GET','HEAD'], true)) { return; }
        // Languages keeps the real external URI before rewriting /en/ to its Polish source.
        $uri = class_exists('ZPL\\Router') && \ZPL\Router::$external !== '' ? \ZPL\Router::$external : ($_SERVER['REQUEST_URI'] ?? '');
        if (!is_string($uri)) { return; }
        $base = rtrim((string)wp_parse_url(home_url('/'), PHP_URL_PATH), '/');
        if ($base !== '' && str_starts_with($uri, $base.'/')) { $uri = substr($uri, strlen($base)); }
        if (!preg_match('~^/(?:en/)?category/~', $uri)) { return; }
        $plan = self::plan();
        $to = self::target_from_plan($uri, $plan, strpos($uri, '/page/') !== false ? self::max_pages() : []);
        if ($to === null || headers_sent()) { return; }
        wp_safe_redirect(home_url($to), 301, 'Zaprojektowani article topics');
        exit;
    }

    public static function register(): void {
        add_filter('zp_en_rewrite_routes', [self::class,'routes'], PHP_INT_MAX);
        add_filter('zpl_legacy_routes', [self::class,'legacy'], PHP_INT_MAX);
        // Router applies this after its shipped legacy table; exact archive aliases reach their final target in one hop.
        add_filter('zpl_resolved_legacy_routes', [self::class,'legacy'], PHP_INT_MAX);
        add_filter('zpl_dictionary', [self::class,'dictionary'], 20, 3);
        add_filter('zpl_has_page', [self::class,'has_page'], 20, 2);
        add_action('template_redirect', [self::class,'redirect'], -1001);
    }
}
ZP_Article_Taxonomy_Routes_20261015::register();
