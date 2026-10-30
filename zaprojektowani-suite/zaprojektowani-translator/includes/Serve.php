<?php
namespace ZPTE;

if (!defined('ABSPATH')) { exit; }

/**
 * What English visitors get. The language module (zaprojektowani-languages) renders every /en/ page; this class
 * hands it the translations and addresses of Tłumacz EN through its filters:
 *  - zp_en_rewrite_routes     Polish => English addresses (Routes::all());
 *  - zpl_legacy_routes        old English addresses => current ones (301);
 *  - zpl_priority_dictionary  [site-wide translations, translations of this page], above the shipped dictionary;
 *  - zpl_has_page             the page has an English version (hreflang, no noindex, sitemap);
 *  - zpl_sitemap_pages        pages listed in the English sitemaps;
 *  - REST zpl/v1/approved-page the same translations for the no-reload switch in the browser.
 * Hooks are added when the module file loads: the language module reads routes at plugins_loaded (first).
 *
 * 2.1.0: German and Spanish through the language module's own filters (zpl_lang_routes, zpl_lang_legacy,
 * zpl_lang_dictionary, zpl_lang_has_page, zpl_lang_sitemap_pages, zpl_lang_revision, zpl_lang_public, zpl_lang_dyn),
 * each answered in its language (Lang::with). The English filters always answer in English, whichever language the
 * request is working on. REST zpl/v1/dyn gives the browser the German or Spanish texts of page scripts.
 */
final class Serve {
    /**
     * Words of the menu, footer and other parts shown on every page. Same as the earlier site dictionary, with
     * "Wiedza" and "Studio wyceny" named like their English addresses (/en/knowledge/, /en/quote-studio/).
     * 3.1.1: "Wiedza" is Knowledge (Mat, 14.10; it was Insights): these exact texts outrank the shipped dictionaries,
     * which still say Insights. "<szybka_strona />" is the code label on the website service pages.
     */
    const SITE_TERMS = [
        'Wiedza' => 'Knowledge', 'Wiedza i poradniki' => 'Knowledge and guides', 'Baza Wiedza' => 'Knowledge base', 'Otwórz bazę Wiedza' => 'Open the Knowledge base',
        'Jak korzystać z Wiedzy?' => 'How to use the Knowledge base?', 'Hero Wiedza — Zaprojektowani' => 'Knowledge hero — Zaprojektowani',
        'Zostań w tym samym obszarze tematycznym albo przejdź do całej bazy Wiedza.' => 'Stay in the same topic area or go to the full Knowledge base.',
        'Portfolio pokazuje efekt. Jeśli chcesz wejść głębiej w proces, ceny i zakres, przejdź do odpowiedniej usługi albo poradników w naszej bazie Wiedza.' => 'The portfolio shows the results. If you want to dive deeper into the process, prices and scope, go to the relevant service or to the guides in our Knowledge base.',
        'Wiedza: strony WWW, sklepy, SEO i branding | Zaprojektowani' => 'Knowledge: Websites, Stores, SEO & Branding | Zaprojektowani',
        'Wiedza o stronach internetowych, SEO i brandingu — Zaprojektowani' => 'Knowledge about websites, SEO and branding — Zaprojektowani',
        'Wiedza — poradniki o stronach internetowych, sklepach, SEO i brandingu' => 'Knowledge — guides on websites, stores, SEO and branding',
        'Wiedza o stronach internetowych, sklepach WooCommerce, SEO, brandingu i kampaniach Meta Ads. Praktyczne poradniki dla firm, które chcą lepiej działać online.' => 'Knowledge about websites, WooCommerce stores, SEO, branding and Meta Ads campaigns. Practical guides for businesses that want to perform better online.',
        '<szybka_strona />' => '<fast_website />',
        'Studio wyceny' => 'Quote Studio', 'Studio Wyceny' => 'Quote Studio', 'Przejdź do Studio Wyceny' => 'Go to the Quote Studio',
        'Strona główna' => 'Home', 'Usługi' => 'Services', 'Realizacje' => 'Our work', 'O nas' => 'About us', 'Kontakt' => 'Contact', 'Menu główne' => 'Main menu',
        'Strony internetowe' => 'Websites', 'Sklepy internetowe' => 'Online stores', 'Strony & sklepy' => 'Websites & stores', 'Strony i sklepy' => 'Websites & stores', 'Strony WordPress' => 'WordPress websites',
        'Strony dla branż' => 'Websites by industry', 'Menu mobilne' => 'Mobile menu',
        'Kancelarie i prawnicy' => 'Law firms and lawyers', 'Specjalizacje, zespół i kontakt' => 'Practice areas, team and contact',
        'Lekarze i gabinety' => 'Doctors and medical practices', 'Usługi, cennik i rejestracja' => 'Services, prices and appointments',
        'Deweloperzy i inwestycje' => 'Property developers', 'Inwestycje, mieszkania i zapytania' => 'Developments, apartments and inquiries',
        'Salony beauty' => 'Beauty salons', 'Zabiegi, cennik i rezerwacje' => 'Treatments, prices and bookings',
        'Trenerzy personalni' => 'Personal trainers', 'Oferta, pakiety i zapisy' => 'Services, packages and sign-ups',
        'Fotografowie' => 'Photographers', 'Portfolio, sesje i zapytania' => 'Portfolio, photo shoots and inquiries',
        'Restauracje i kawiarnie' => 'Restaurants and cafés', 'Menu, rezerwacje i dojazd' => 'Menu, bookings and directions',
        'Hotele i pensjonaty' => 'Hotels and guesthouses', 'Pokoje, oferta i rezerwacje' => 'Rooms, offers and bookings',
        'Logo & branding' => 'Logo & branding', 'Logo i branding' => 'Logo & branding', 'Identyfikacja wizualna' => 'Visual identity', 'Projekt logo' => 'Logo design', 'Brandbook' => 'Brand book', 'Start marki' => 'Brand launch',
        'Kampanie reklamowe' => 'Advertising campaigns', 'Pozycjonowanie stron' => 'SEO', 'Wszystkie usługi' => 'All services', 'Zobacz wszystkie' => 'View all', 'Zobacz realizacje' => 'View our work',
        'Szukaj' => 'Search', 'Wyszukiwarka' => 'Search', 'Szukaj w Zaprojektowani' => 'Search Zaprojektowani', 'Zamknij wyszukiwarkę' => 'Close search', 'Otwórz menu' => 'Open menu', 'Zamknij menu' => 'Close menu',
        'Polityka prywatności' => 'Privacy policy', 'Polityka cookies' => 'Cookie policy', 'Najczęściej zadawane pytania' => 'Frequently asked questions', 'Dołącz do nas' => 'Join us', 'Porozmawiajmy' => 'Let’s talk',
        'Rano' => 'Morning', 'Po południu' => 'Afternoon', 'Wieczorem' => 'Evening',
    ];
    /**
     * 2.1.0: the same fixed words in German and Spanish (menu, footer, the industry menu, the Quote Studio): one wording on
     * every page, never sent to OpenAI. Everything else is written by the model.
     */
    const LANG_TERMS = [
        'de' => [
            'Wiedza' => 'Wissen', 'Wiedza i poradniki' => 'Wissen & Ratgeber', 'Baza Wiedza' => 'Wissensdatenbank', 'Otwórz bazę Wiedza' => 'Zur Wissensdatenbank',
            '<szybka_strona />' => '<schnelle_website />',
            'Studio wyceny' => 'Angebotsrechner', 'Studio Wyceny' => 'Angebotsrechner', 'Przejdź do Studio Wyceny' => 'Zum Angebotsrechner',
            'Strona główna' => 'Startseite', 'Start' => 'Start', 'Usługi' => 'Leistungen', 'Realizacje' => 'Referenzen', 'O nas' => 'Über uns', 'Kontakt' => 'Kontakt', 'Menu główne' => 'Hauptmenü',
            'Strony internetowe' => 'Webdesign', 'Sklepy internetowe' => 'Onlineshops', 'Strony & sklepy' => 'Websites & Shops', 'Strony i sklepy' => 'Websites & Shops', 'Strony WordPress' => 'WordPress-Websites',
            'Strony dla branż' => 'Websites nach Branche', 'Menu mobilne' => 'Mobiles Menü',
            'Kancelarie i prawnicy' => 'Kanzleien & Anwälte', 'Specjalizacje, zespół i kontakt' => 'Rechtsgebiete, Team und Kontakt',
            'Lekarze i gabinety' => 'Ärzte & Praxen', 'Usługi, cennik i rejestracja' => 'Leistungen, Preise und Terminbuchung',
            'Deweloperzy i inwestycje' => 'Bauträger & Immobilien', 'Inwestycje, mieszkania i zapytania' => 'Projekte, Wohnungen und Anfragen',
            'Salony beauty' => 'Beauty-Salons', 'Zabiegi, cennik i rezerwacje' => 'Behandlungen, Preise und Buchung',
            'Trenerzy personalni' => 'Personal Trainer', 'Oferta, pakiety i zapisy' => 'Angebot, Pakete und Anmeldung',
            'Fotografowie' => 'Fotografen', 'Portfolio, sesje i zapytania' => 'Portfolio, Shootings und Anfragen',
            'Restauracje i kawiarnie' => 'Restaurants & Cafés', 'Menu, rezerwacje i dojazd' => 'Speisekarte, Reservierung und Anfahrt',
            'Hotele i pensjonaty' => 'Hotels & Pensionen', 'Pokoje, oferta i rezerwacje' => 'Zimmer, Angebote und Buchung',
            'Logo & branding' => 'Logo & Branding', 'Logo i branding' => 'Logo & Branding', 'Identyfikacja wizualna' => 'Corporate Design', 'Projekt logo' => 'Logodesign', 'Brandbook' => 'Brandbook', 'Start marki' => 'Markenstart',
            'Kampanie reklamowe' => 'Werbekampagnen', 'Pozycjonowanie stron' => 'SEO', 'Wszystkie usługi' => 'Alle Leistungen', 'Zobacz wszystkie' => 'Alle ansehen', 'Zobacz realizacje' => 'Referenzen ansehen',
            'Szukaj' => 'Suchen', 'Wyszukiwarka' => 'Suche', 'Szukaj w Zaprojektowani' => 'Zaprojektowani durchsuchen', 'Zamknij wyszukiwarkę' => 'Suche schließen', 'Otwórz menu' => 'Menü öffnen', 'Zamknij menu' => 'Menü schließen',
            'Polityka prywatności' => 'Datenschutzerklärung', 'Polityka cookies' => 'Cookie-Richtlinie', 'Najczęściej zadawane pytania' => 'Häufig gestellte Fragen',
            'Rano' => 'Vormittags', 'Po południu' => 'Nachmittags', 'Wieczorem' => 'Abends',
        ],
        'es' => [
            'Wiedza' => 'Blog', 'Wiedza i poradniki' => 'Blog y guías', 'Baza Wiedza' => 'Blog', 'Otwórz bazę Wiedza' => 'Ir al blog',
            '<szybka_strona />' => '<web_rapida />',
            'Studio wyceny' => 'Calculadora de presupuesto', 'Studio Wyceny' => 'Calculadora de presupuesto', 'Przejdź do Studio Wyceny' => 'Ir a la calculadora de presupuesto',
            'Strona główna' => 'Inicio', 'Start' => 'Inicio', 'Usługi' => 'Servicios', 'Realizacje' => 'Proyectos', 'O nas' => 'Nosotros', 'Kontakt' => 'Contacto', 'Menu główne' => 'Menú principal',
            'Strony internetowe' => 'Diseño web', 'Sklepy internetowe' => 'Tiendas online', 'Strony & sklepy' => 'Webs y tiendas', 'Strony i sklepy' => 'Webs y tiendas', 'Strony WordPress' => 'Webs en WordPress',
            'Strony dla branż' => 'Webs por sector', 'Menu mobilne' => 'Menú móvil',
            'Kancelarie i prawnicy' => 'Despachos de abogados', 'Specjalizacje, zespół i kontakt' => 'Especialidades, equipo y contacto',
            'Lekarze i gabinety' => 'Médicos y clínicas', 'Usługi, cennik i rejestracja' => 'Servicios, tarifas y citas',
            'Deweloperzy i inwestycje' => 'Promotoras inmobiliarias', 'Inwestycje, mieszkania i zapytania' => 'Promociones, viviendas y consultas',
            'Salony beauty' => 'Centros de estética', 'Zabiegi, cennik i rezerwacje' => 'Tratamientos, tarifas y reservas',
            'Trenerzy personalni' => 'Entrenadores personales', 'Oferta, pakiety i zapisy' => 'Servicios, packs e inscripciones',
            'Fotografowie' => 'Fotógrafos', 'Portfolio, sesje i zapytania' => 'Portfolio, sesiones y consultas',
            'Restauracje i kawiarnie' => 'Restaurantes y cafeterías', 'Menu, rezerwacje i dojazd' => 'Carta, reservas y cómo llegar',
            'Hotele i pensjonaty' => 'Hoteles y hostales', 'Pokoje, oferta i rezerwacje' => 'Habitaciones, ofertas y reservas',
            'Logo & branding' => 'Logo y branding', 'Logo i branding' => 'Logo y branding', 'Identyfikacja wizualna' => 'Identidad corporativa', 'Projekt logo' => 'Diseño de logotipo', 'Brandbook' => 'Manual de marca', 'Start marki' => 'Lanzamiento de marca',
            'Kampanie reklamowe' => 'Campañas publicitarias', 'Pozycjonowanie stron' => 'Posicionamiento SEO', 'Wszystkie usługi' => 'Todos los servicios', 'Zobacz wszystkie' => 'Ver todos', 'Zobacz realizacje' => 'Ver proyectos',
            'Szukaj' => 'Buscar', 'Wyszukiwarka' => 'Buscador', 'Szukaj w Zaprojektowani' => 'Buscar en Zaprojektowani', 'Zamknij wyszukiwarkę' => 'Cerrar el buscador', 'Otwórz menu' => 'Abrir el menú', 'Zamknij menu' => 'Cerrar el menú',
            'Polityka prywatności' => 'Política de privacidad', 'Polityka cookies' => 'Política de cookies', 'Najczęściej zadawane pytania' => 'Preguntas frecuentes',
            'Rano' => 'Por la mañana', 'Po południu' => 'Por la tarde', 'Wieczorem' => 'Por la noche',
        ],
    ];
    const CACHE_GROUP = 'zpte2';

    /** @var array<string,array> 2.1.0: per language, then per source */
    private static $pageMaps = [];
    /** @var array<string,array> per language */
    private static $shared = [];
    /** @var array<string,array> per language, then per source */
    private static $pageRows = [];

    /** 2.1.0: the fixed words of the current language. */
    public static function site_terms(): array {
        return Lang::is_en() ? self::SITE_TERMS : (self::LANG_TERMS[Lang::code()] ?? []);
    }

    public static function hooks(): void {
        add_filter('zp_en_rewrite_routes', [self::class, 'routes'], 40);
        add_filter('zpl_legacy_routes', [self::class, 'legacy']);
        add_filter('zpl_priority_dictionary', [self::class, 'dictionary'], 10, 3);
        add_filter('zpl_has_page', [self::class, 'has_page'], 10, 2);
        add_filter('zpl_sitemap_pages', [self::class, 'sitemap_pages']);
        add_filter('zpl_approved_revision', [self::class, 'revision_filter'], 10, 2);
        add_filter('zpl_cache_versions', [self::class, 'cache_versions'], 10, 2);
        // 2.1.0: German and Spanish.
        add_filter('zpl_lang_routes', [self::class, 'lang_routes'], 10, 2);
        add_filter('zpl_lang_legacy', [self::class, 'lang_legacy'], 10, 2);
        add_filter('zpl_lang_dictionary', [self::class, 'lang_dictionary'], 10, 3);
        add_filter('zpl_lang_has_page', [self::class, 'lang_has_page'], 10, 3);
        add_filter('zpl_lang_sitemap_pages', [self::class, 'lang_sitemap_pages'], 10, 2);
        add_filter('zpl_lang_revision', [self::class, 'lang_revision'], 10, 3);
        add_filter('zpl_lang_public', [self::class, 'lang_public'], 10, 2);
        add_filter('zpl_lang_dyn', [self::class, 'lang_dyn_url'], 10, 2);
        add_action('rest_api_init', [self::class, 'rest'], 20);
    }

    /** 2.1.0: $fn in English (the English filters may be asked while a German or Spanish step runs). @return mixed */
    private static function en(callable $fn) { return Lang::is_en() ? $fn() : Lang::with('en', $fn); }

    /** 2.1.0: German or Spanish as asked by the language module ('' for anything else). @param mixed $lang */
    private static function ai($lang): string { return is_string($lang) && in_array($lang, Lang::AI, true) ? $lang : ''; }

    /** 2.1.0: the language is made (switched on in its settings) and its tables exist. */
    private static function made(): bool { return (int) Settings::get('on') === 1 && Store::ready(); }

    /**
     * 3.1.1: versions of this module's English texts for ZP Suite's HTML cache (filter zpl_cache_versions, see
     * \ZPL\Router::cache_paths): '*' for the menu, footer and post titles (shared map) and the module version, and each
     * Polish address its page map's version. Both change only when a translation really changes (Serve::bump). One query,
     * no translation is loaded (Serve::revision() loads them: 1-2 ms a page).
     * @param mixed $v
     * @param mixed $paths
     */
    public static function cache_versions($v, $paths) {
        if (!Lang::is_en()) { return self::en(static function () use ($v, $paths) { return self::cache_versions($v, $paths); }); }
        $v = is_array($v) ? $v : [];
        $mv = get_option(Lang::opt('zpte2_map_versions'), []);
        $mv = is_array($mv) ? $mv : [];
        $v['*'] = (string) ($v['*'] ?? '') . '|zpte:' . ZPTE_VERSION . ':' . (int) ($mv[Store::SHARED] ?? 0) . ':' . (Import::done() ? 1 : 0);
        if (!Store::ready() || !is_array($paths) || !$paths) { return $v; }
        global $wpdb;
        $want = array_flip(array_map('strval', $paths));
        foreach ((array) $wpdb->get_results('SELECT id, path FROM ' . Store::table('pages'), ARRAY_N) as $r) {
            if (isset($want[(string) $r[1]])) { $v[(string) $r[1]] = (string) (int) ($mv[(int) $r[0]] ?? 0); }
        }
        return $v;
    }

    public static function flush(): void {
        self::$pageMaps = [];
        self::$shared = [];
        self::$pageRows = [];
        Legacy::forget();
        if (class_exists('ZPL\\Dict')) { \ZPL\Dict::flush(); }
    }

    // ---------------------------------------------------------------- routes

    /** @param mixed $routes */
    public static function routes($routes) {
        if (!Lang::is_en()) { return self::en(static function () use ($routes) { return self::routes($routes); }); }
        $routes = is_array($routes) ? $routes : [];
        $mine = Routes::all();
        if (!Import::done()) {
            // Until the copy is finished, the addresses of the earlier translators keep working as they did.
            foreach (Legacy::all_routes() as $pl => $en) {
                if (!isset($routes[$pl]) && !isset($mine[$pl])) { $routes[$pl] = $en; }
            }
            foreach (Routes::FIXED as $pl => $en) { if (!isset($mine[$pl])) { $routes[$pl] = $en; } }
        }
        if (!$mine) { return $routes; }
        $taken = array_flip($mine);
        foreach ($routes as $pl => $en) {
            // One English address belongs to one Polish page: this module's choice wins.
            if (isset($taken[$en]) && $taken[$en] !== $pl) { unset($routes[$pl]); }
        }
        foreach ($mine as $pl => $en) { $routes[$pl] = $en; }
        return $routes;
    }

    /** @param mixed $legacy */
    public static function legacy($legacy) {
        if (!Lang::is_en()) { return self::en(static function () use ($legacy) { return self::legacy($legacy); }); }
        $legacy = is_array($legacy) ? $legacy : [];
        foreach (Routes::legacy() as $old => $new) { $legacy[$old] = $new; }
        return $legacy;
    }

    /** 2.1.0: Polish path => German (Spanish) path. @param mixed $routes  @param mixed $lang */
    public static function lang_routes($routes, $lang) {
        $routes = is_array($routes) ? $routes : [];
        $l = self::ai($lang);
        if ($l === '') { return $routes; }
        return array_merge($routes, Lang::with($l, static function () { return self::made() ? Routes::all() : []; }));
    }

    /** 2.1.0: old German (Spanish) address => current one. @param mixed $legacy  @param mixed $lang */
    public static function lang_legacy($legacy, $lang) {
        $legacy = is_array($legacy) ? $legacy : [];
        $l = self::ai($lang);
        if ($l === '') { return $legacy; }
        return array_merge($legacy, Lang::with($l, static function () { return self::made() ? Routes::legacy() : []; }));
    }

    // ------------------------------------------------------------ dictionary

    /** @param mixed $layers */
    public static function dictionary($layers, $lang, $source) {
        if (!Lang::is_en()) { return self::en(static function () use ($layers, $lang, $source) { return self::dictionary($layers, $lang, $source); }); }
        $layers = is_array($layers) ? $layers : [];
        $source = (string) $source;
        $shared = self::shared_map();
        $page = self::page_map($source);
        if ($lang === 'pl') {
            $layers[] = self::reverse($shared + $page);
            return $layers;
        }
        $layers[] = $shared;
        if ($page) { $layers[] = $page; }
        return $layers;
    }

    /**
     * 2.1.0: German (Spanish) translations for a page: the site-wide ones and the page's own. A lookup of texts page scripts
     * wrote (REST zpl/v1/lookup) also finds the translations of those texts.
     * @param mixed $layers  @param mixed $lang  @param mixed $source
     */
    public static function lang_dictionary($layers, $lang, $source) {
        $layers = is_array($layers) ? $layers : [];
        $l = self::ai($lang);
        if ($l === '') { return $layers; }
        return Lang::with($l, static function () use ($layers, $source) {
            if (!self::made()) { return $layers; }
            $layers[] = self::shared_map();
            $page = self::page_map((string) $source);
            if ($page) { $layers[] = $page; }
            if (defined('REST_REQUEST') && REST_REQUEST && ($dyn = self::page_map(Source::DYN))) { $layers[] = $dyn; }
            return $layers;
        });
    }

    /** Site-wide translations: fixed site terms, then saved shared translations (post titles, excerpts…). */
    public static function shared_map(): array {
        $l = Lang::code();
        if (isset(self::$shared[$l])) { return self::$shared[$l]; }
        $map = self::site_terms();
        if (Store::ready()) {
            $key = $l . ':shared:' . self::map_version(0);
            $saved = wp_cache_get($key, self::CACHE_GROUP);
            if (!is_array($saved)) {
                $saved = Store::map_only(Store::SHARED) ?? [];
                wp_cache_set($key, $saved, self::CACHE_GROUP, HOUR_IN_SECONDS);
            }
            foreach ($saved as $pl => $en) { $map[(string) $pl] = (string) $en; }
        }
        if (!Import::done()) {
            foreach (Legacy::zpte_global() as $pl => $en) { if (!isset($map[$pl])) { $map[$pl] = $en; } }
        }
        // Manual site terms stay as the owner set them in the language module (its overrides come first anyway).
        return self::$shared[$l] = $map;
    }

    /** Translations of one page (compiled map), or the earlier translators' map while the copy is running. */
    public static function page_map(string $source): array {
        $l = Lang::code();
        if (isset(self::$pageMaps[$l][$source])) { return self::$pageMaps[$l][$source]; }
        $map = [];
        $row = self::row($source);
        if ($row) {
            $key = $l . ':page:' . $row['id'] . ':' . self::map_version((int) $row['id']);
            $cached = wp_cache_get($key, self::CACHE_GROUP);
            if (is_array($cached)) {
                $map = $cached;
            } else {
                $map = Store::map_only((int) $row['id']) ?? [];
                wp_cache_set($key, $map, self::CACHE_GROUP, HOUR_IN_SECONDS);
            }
        }
        if (!$map && !Import::done()) { $map = Legacy::page_map($source); }
        return self::$pageMaps[$l][$source] = $map;
    }

    /** Small per-request index of pages: source => [id, en, rev]. */
    private static function row(string $source): ?array {
        if (!Store::ready()) { return null; }
        $l = Lang::code();
        if (!isset(self::$pageRows[$l])) { self::$pageRows[$l] = []; }
        if (array_key_exists($source, self::$pageRows[$l])) { return self::$pageRows[$l][$source]; }
        global $wpdb;
        $r = $wpdb->get_row($wpdb->prepare('SELECT p.id, p.en_path, m.rev FROM ' . Store::table('pages') . ' p LEFT JOIN ' . Store::table('maps') . ' m ON m.page_id = p.id WHERE p.path_hash = %s', Store::hash($source)), ARRAY_A);
        self::$pageRows[$l][$source] = $r ? ['id' => (int) $r['id'], 'en' => (string) $r['en_path'], 'rev' => (int) $r['rev']] : null;
        return self::$pageRows[$l][$source];
    }

    /** Version of a compiled map (changes whenever it is saved): cache keys and the browser copy use it. */
    private static function map_version(int $page_id): int {
        $v = get_option(Lang::opt('zpte2_map_versions'), []);
        return is_array($v) ? (int) ($v[$page_id] ?? 0) : 0;
    }

    /** Called after a map is saved. Page 0 is the shared map. */
    public static function bump(int $page_id, int $rev): void {
        $v = get_option(Lang::opt('zpte2_map_versions'), []);
        $v = is_array($v) ? $v : [];
        // 3.1.1: a page read again with the same translations keeps its copies in ZP Suite's HTML cache. A changed map
        // changes the version of its English page (of every English page for the shared map: menu, footer, post titles,
        // see cache_versions()); the cache then lists its pages again at the end of the request, keeping the other copies.
        $same = isset($v[$page_id]) && (int) $v[$page_id] === $rev;
        $v[$page_id] = $rev;
        update_option(Lang::opt('zpte2_map_versions'), $v, true);
        self::flush();
        // 2.1.0: German and Spanish pages are not kept in that cache.
        if (!$same && Lang::is_en()) { do_action('zp_html_cache_refresh'); }
    }

    private static function reverse(array $map): array {
        $out = [];
        foreach ($map as $pl => $en) {
            $k = \ZPL\Html::norm((string) $en);
            // A short title or description (3.1.1) switches back to its Polish text, without the mark of its key.
            if ($k !== '' && !isset($out[$k])) { $out[$k] = \ZPL\Page::plain((string) $pl); }
        }
        return $out;
    }

    // ------------------------------------------------------------ has page

    /** @param mixed $has */
    public static function has_page($has, $source) {
        if ($has) { return true; }
        if (!Lang::is_en()) { return self::en(static function () use ($source) { return self::has_page(false, $source); }); }
        $source = (string) $source;
        if (Index::has($source)) { return true; }
        if (!Import::done()) {
            $legacy = Legacy::all_routes();
            if (isset($legacy[$source]) || isset(Routes::FIXED[$source]) || array_key_exists($source, Legacy::codex_active())) { return (bool) Legacy::page_map($source); }
        }
        return false;
    }

    /** @param mixed $pages */
    public static function sitemap_pages($pages) {
        if (!Lang::is_en()) { return self::en(static function () use ($pages) { return self::sitemap_pages($pages); }); }
        $pages = is_array($pages) ? $pages : [];
        foreach (Index::pages() as $pl) { $pages[] = (string) $pl; }
        if (!Import::done()) {
            foreach (array_keys(Legacy::all_routes()) as $pl) { $pages[] = (string) $pl; }
            foreach (array_keys(Legacy::codex_active()) as $pl) { $pages[] = (string) $pl; }
        }
        return array_values(array_unique($pages));
    }

    /** 2.1.0 @param mixed $has  @param mixed $source  @param mixed $lang */
    public static function lang_has_page($has, $source, $lang) {
        $l = self::ai($lang);
        if ($has || $l === '') { return (bool) $has; }
        return Lang::with($l, static function () use ($source) { return self::made() && Index::has((string) $source); });
    }

    /** 2.1.0 @param mixed $pages  @param mixed $lang */
    public static function lang_sitemap_pages($pages, $lang) {
        $pages = is_array($pages) ? $pages : [];
        $l = self::ai($lang);
        if ($l === '') { return $pages; }
        return array_values(array_unique(array_merge($pages, Lang::with($l, static function () { return self::made() ? Index::pages() : []; }))));
    }

    /**
     * 2.1.0: German (Spanish) is offered to visitors (switch, hreflang, sitemaps, the visitor redirect) once its home page is
     * translated and the first full translation of the site is finished, so nobody lands on a half-translated version. In
     * the panel the owner can show it at once ("on") or hide it ("off"); until then its pages exist with noindex.
     * @param mixed $ok  @param mixed $lang
     */
    public static function lang_public($ok, $lang) {
        $l = self::ai($lang);
        if ($ok || $l === '') { return (bool) $ok; }
        return Lang::with($l, static function () {
            if (!self::made()) { return false; }
            $mode = (string) Settings::get('public');
            if ($mode === 'off' || !Index::has('/')) { return false; }
            return $mode === 'on' || (int) Worker::state()['first'] > 0;
        });
    }

    // ------------------------------------------------------------------ REST

    public static function rest(): void {
        register_rest_route('zpl/v1', '/approved-page', [
            'methods' => 'GET',
            'permission_callback' => '__return_true',
            'callback' => [self::class, 'approved'],
        ], true);
        // 2.1.0: German and Spanish texts of page scripts (zpl.js; English reads the language module's own file).
        register_rest_route('zpl/v1', '/dyn', [
            'methods' => 'GET',
            'permission_callback' => '__return_true',
            'callback' => [self::class, 'dyn'],
        ], true);
    }

    /** Revision of what the browser gets for a page (part of the cached REST address). */
    public static function revision(string $source): string {
        $map = self::shared_map() + self::page_map($source);
        if (!$map) { return ''; }
        $row = self::row($source);
        return hash('sha256', ZPTE_VERSION . '|' . (Lang::is_en() ? '' : Lang::code() . '|') . $source . '|' . self::map_version(0) . '|' . ($row ? $row['rev'] : 0) . '|' . count($map) . '|' . (Import::done() ? 1 : 0) . '|' . md5((string) wp_json_encode(array_slice($map, 0, 50, true))));
    }

    /** @param mixed $rev */
    public static function revision_filter($rev, $source) {
        return self::en(static function () use ($source) { return self::revision((string) $source); });
    }

    /** 2.1.0 @param mixed $rev  @param mixed $source  @param mixed $lang */
    public static function lang_revision($rev, $source, $lang) {
        $l = self::ai($lang);
        if ($l === '') { return $rev; }
        return Lang::with($l, static function () use ($source) { return self::made() ? self::revision((string) $source) : ''; });
    }

    /** @param \WP_REST_Request $r */
    public static function approved($r) {
        $src = $r->get_param('src');
        $lang = $r->get_param('lang');
        if (!is_string($src) || !is_string($lang) || !in_array($lang, ['en', 'pl', 'de', 'es'], true) || $src === '' || $src[0] !== '/' || strlen($src) > 600) {
            return new \WP_Error('zpte_input', 'Nieprawidłowe parametry.', ['status' => 400]);
        }
        $src = \ZPL\Router::norm_path($src);
        // 2.1.0: German and Spanish answer from their own translations (Polish and English from the English ones, as before).
        return Lang::with(self::ai($lang) ?: 'en', static function () use ($r, $src, $lang) {
            $route = in_array($lang, Lang::AI, true) ? \ZPL\Router::has_route($src, $lang) : \ZPL\Router::has_route($src);
            if (\ZPL\Router::is_excluded($src) || \ZPL\Router::never($src) || !$route || (!Lang::is_en() && !self::made())) {
                $res = new \WP_REST_Response(['ok' => true, 'source' => $src, 'lang' => $lang, 'revision' => '', 'map' => (object) []]);
                $res->header('Cache-Control', 'public, max-age=300');
                return $res;
            }
            $map = self::shared_map() + self::page_map($src);
            $rev = self::revision($src);
            if ($lang === 'pl') { $map = self::reverse($map); }
            $res = new \WP_REST_Response(['ok' => true, 'source' => $src, 'lang' => $lang, 'revision' => $rev, 'map' => (object) $map]);
            $asked = $r->get_param('rev');
            // The page names the revision it was rendered with: that exact answer never changes, so browsers keep it.
            $res->header('Cache-Control', is_string($asked) && $asked !== '' && hash_equals($rev, $asked) ? 'public, max-age=31536000, immutable' : 'public, max-age=60');
            $res->header('X-Robots-Tag', 'noindex, nofollow');
            return $res;
        });
    }

    /** 2.1.0: version of the German (Spanish) texts of page scripts ('' while there are none). */
    private static function dyn_version(): string {
        $row = self::row(Source::DYN);
        $v = $row ? self::map_version((int) $row['id']) : 0;
        return $v > 0 ? substr(hash('sha256', ZPTE_VERSION . '|' . Lang::code() . '|' . $v), 0, 16) : '';
    }

    /** 2.1.0: address of those texts for zpl.js (filter zpl_lang_dyn). @param mixed $url  @param mixed $lang */
    public static function lang_dyn_url($url, $lang) {
        $l = self::ai($lang);
        if ($l === '') { return $url; }
        return Lang::with($l, static function () use ($l) {
            $v = self::made() ? self::dyn_version() : '';
            return $v === '' ? '' : add_query_arg(['lang' => $l, 'v' => $v], rest_url('zpl/v1/dyn'));
        });
    }

    /** @param \WP_REST_Request $r */
    public static function dyn($r) {
        $l = self::ai($r->get_param('lang'));
        if ($l === '') { return new \WP_Error('zpte_input', 'Nieprawidłowe parametry.', ['status' => 400]); }
        return Lang::with($l, static function () use ($r) {
            $map = self::made() ? self::page_map(Source::DYN) : [];
            $v = self::made() ? self::dyn_version() : '';
            $res = new \WP_REST_Response((object) $map);
            $asked = $r->get_param('v');
            $res->header('Cache-Control', $v !== '' && is_string($asked) && hash_equals($v, $asked) ? 'public, max-age=31536000, immutable' : 'public, max-age=300');
            $res->header('X-Robots-Tag', 'noindex, nofollow');
            return $res;
        });
    }
}
