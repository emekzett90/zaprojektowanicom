<?php
namespace ZPL;

if (!defined('ABSPATH')) { exit; }

/** Public read-only lookups used by the no-reload switch and for dynamic (JS inserted) text. */
final class Rest {
    public static function register(): void {
        register_rest_route('zpl/v1', '/lookup', [
            'methods' => 'POST',
            'permission_callback' => '__return_true',
            'callback' => [self::class, 'lookup'],
        ]);
        register_rest_route('zpl/v1', '/missing', [
            'methods' => 'POST',
            'permission_callback' => '__return_true',
            'callback' => [self::class, 'missing'],
        ]);
        register_rest_route('zpl/v1', '/overrides', [
            'methods' => 'GET',
            'permission_callback' => '__return_true',
            'callback' => [self::class, 'overrides'],
        ]);
        register_rest_route('zpl/v1', '/geo', [
            'methods' => 'GET',
            'permission_callback' => '__return_true',
            'callback' => [self::class, 'geo'],
        ]);
        register_rest_route('zpl/v1', '/pref', [
            'methods' => 'POST',
            'permission_callback' => '__return_true',
            'callback' => [self::class, 'pref'],
        ]);
    }

    private static function input(\WP_REST_Request $r): array {
        $lang = $r->get_param('lang') === 'pl' ? 'pl' : 'en';
        $src = Router::norm_path((string) $r->get_param('src'));
        $keys = $r->get_param('keys');
        $keys = is_array($keys) ? array_slice(array_values(array_filter($keys, 'is_string')), 0, 400) : [];
        return [$lang, $src, $keys];
    }

    public static function lookup(\WP_REST_Request $r) {
        [$lang, $src, $keys] = self::input($r);
        $map = [];
        foreach ($keys as $k) {
            if (strlen($k) > 20000) { continue; }
            $t = Dict::get(Html::norm($k), $lang, $src);
            if ($t !== null) { $map[$k] = $t; }
        }
        $res = new \WP_REST_Response(['map' => (object) $map]);
        $res->header('Cache-Control', 'no-store');
        return $res;
    }

    /** Anonymous reports of untranslated dynamic text (rate limited, capped, admin-reviewed). */
    public static function missing(\WP_REST_Request $r) {
        [$lang, $src, $keys] = self::input($r);
        if ($lang !== 'en' || !$keys) { return ['ok' => true]; }
        $ip = (string) ($_SERVER['REMOTE_ADDR'] ?? '');
        $rk = 'zpl_rl_' . substr(md5($ip), 0, 12);
        $n = (int) get_transient($rk);
        if ($n > 30) { return new \WP_REST_Response(['ok' => false], 429); }
        set_transient($rk, $n + 1, HOUR_IN_SECONDS);
        $rows = [];
        foreach (array_slice($keys, 0, 60) as $k) {
            $k = Html::norm($k);
            if (strlen($k) > 1500 || !preg_match('~[ąćęłńóśźżĄĆĘŁŃÓŚŹŻ]|\b(?:i|w|z|na|do|się|jest|nie|dla|oraz|lub|że|jak|czy|od|po|przez|jest|są|Twoj\w*|nas|Wasz\w*)\b~u', $k)) { continue; }
            $rows[$k] = 'dynamic';
        }
        Missing::remember($rows, $src);
        return ['ok' => true];
    }

    public static function overrides(\WP_REST_Request $r) {
        $lang = $r->get_param('lang') === 'pl' ? 'pl' : 'en';
        $res = new \WP_REST_Response(['map' => (object) Dict::overrides($lang)]);
        $res->header('Cache-Control', 'public, max-age=300');
        return $res;
    }


    /**
     * The visitor's PL/EN choice from the switch, set again by the server: Safari keeps cookies written by
     * scripts for 7 days only, a cookie from the server for the full year.
     */
    public static function pref(\WP_REST_Request $r) {
        $origin = (string) ($_SERVER['HTTP_ORIGIN'] ?? '');
        $same = $origin === '' || strtolower((string) wp_parse_url($origin, PHP_URL_HOST)) === strtolower((string) wp_parse_url(home_url('/'), PHP_URL_HOST));
        $lang = (string) $r->get_param('lang');
        $ok = $same && in_array($lang, ['pl', 'en'], true);
        if ($ok) { Geo::set_preference_cookie($lang); }
        $res = new \WP_REST_Response(['ok' => $ok], $ok ? 200 : 400);
        $res->header('Cache-Control', 'private, no-store, max-age=0');
        return $res;
    }

    /** Country-only GEO endpoint for the first visit when the CDN exposes no country header. */
    public static function geo(\WP_REST_Request $r) {
        // Robots never get a country: no remote lookup and no redirect for them.
        $country = Geo::is_bot() ? null : Geo::country_remote();
        if ($country !== null) { Geo::remember_country($country); }
        $res = new \WP_REST_Response(['country' => $country ?: '']);
        $res->header('Cache-Control', 'private, no-store, max-age=0');
        $res->header('Vary', 'Cookie');
        return $res;
    }
}
