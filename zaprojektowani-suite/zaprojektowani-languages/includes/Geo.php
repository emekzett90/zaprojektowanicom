<?php
namespace ZPL;

if (!defined('ABSPATH')) { exit; }

/**
 * Lightweight geo language routing.
 *
 * Goals:
 * - Poland => keep Polish URLs by default.
 * - Any other detected country => redirect Polish URLs to their /en/ equivalent.
 * - Never geo-redirect crawlers / Lighthouse / PageSpeed.
 * - Prefer CDN/hosting country headers (zero extra request).
 * - If the server exposes no country header, use a tiny same-origin REST lookup once,
 *   cache the result in a first-party cookie, and only then redirect.
 * - Explicit manual language choice disables automatic geo redirection.
 */
final class Geo {
    const COUNTRY_COOKIE = 'zpl_geo_country';
    const PREF_COOKIE = 'zpl_lang_pref';
    const COOKIE_TTL = 604800; // 7 days

    /** A manual PL/EN selection made with the language switcher. */
    public static function preference(): ?string {
        $v = strtolower(trim((string) ($_COOKIE[self::PREF_COOKIE] ?? '')));
        return in_array($v, ['pl', 'en'], true) ? $v : null;
    }

    /** Cached/detected ISO 3166-1 alpha-2 country code. No remote HTTP here. */
    public static function country(): ?string {
        $cookie = self::clean_country((string) ($_COOKIE[self::COUNTRY_COOKIE] ?? ''));
        if ($cookie !== null) { return $cookie; }

        $header = self::header_country();
        if ($header !== null) {
            self::remember_country($header);
            return $header;
        }

        // WooCommerce/MaxMind, when already available on the site. No remote fallback here.
        if (class_exists('\\WC_Geolocation') && is_callable(['\\WC_Geolocation', 'geolocate_ip'])) {
            try {
                $g = \WC_Geolocation::geolocate_ip(self::ip(), false, false);
                $wc = self::clean_country(is_array($g) ? (string) ($g['country'] ?? '') : '');
                if ($wc !== null) {
                    self::remember_country($wc);
                    return $wc;
                }
            } catch (\Throwable $e) {}
        }

        return null;
    }

    /** Remote fallback used only by /wp-json/zpl/v1/geo, never during normal page TTFB. */
    public static function country_remote(): ?string {
        $local = self::country();
        if ($local !== null) { return $local; }

        $ip = self::ip();
        if ($ip === '' || !filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return null;
        }

        $key = 'zpl_geo_' . substr(hash('sha256', $ip), 0, 24);
        $cached = self::clean_country((string) get_transient($key));
        if ($cached !== null) { return $cached; }

        // Small, country-only lookup. Short timeout so a provider issue cannot stall the UI.
        $url = 'https://ipapi.co/' . rawurlencode($ip) . '/country/';
        $res = wp_remote_get($url, [
            'timeout' => 1.8,
            'redirection' => 1,
            'user-agent' => 'Zaprojektowani-Languages/' . (defined('ZPL_VERSION') ? ZPL_VERSION : '1'),
        ]);
        if (is_wp_error($res) || (int) wp_remote_retrieve_response_code($res) !== 200) { return null; }

        $country = self::clean_country(trim((string) wp_remote_retrieve_body($res)));
        if ($country !== null) {
            set_transient($key, $country, 7 * DAY_IN_SECONDS);
        }
        return $country;
    }

    /** Server-side redirect decision. Only redirects PL => EN. */
    public static function redirect_target(string $lang, string $source): ?string {
        if ($lang !== 'pl') { return null; }
        if (self::is_bot()) { return null; }
        if (!in_array(strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET')), ['GET', 'HEAD'], true)) { return null; }

        // A conscious switch is stronger than automatic country routing.
        if (self::preference() !== null) { return null; }

        $country = self::country();
        if ($country === null || $country === 'PL') { return null; }

        return Router::en_path($source);
    }

    /**
     * Early client fallback for sites where page cache/CDN does not expose a country header to PHP.
     * It calls our own REST endpoint once; the endpoint may do the country-only remote lookup.
     */
    public static function inject_client(string $html, string $lang, string $source): string {
        if ($lang !== 'pl' || self::preference() !== null || self::is_bot()) { return $html; }
        if (self::country() !== null) { return $html; }
        if (stripos($html, '</head>') === false) { return $html; }

        $rest = esc_url_raw(rest_url('zpl/v1/geo'));
        $target = Router::base() . Router::en_path($source);
        $restJs = wp_json_encode($rest, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
        $targetJs = wp_json_encode($target, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);

        // Keep a wrong-language Cookiebot banner hidden while the one-time geo lookup is pending.
        $tag = '<style id="zpl-geo-pending-css">html.zpl-geo-pending #CybotCookiebotDialog{visibility:hidden!important}</style>'
            . '<script id="zpl-geo-boot">(function(){try{var d=document.documentElement;if(document.cookie.indexOf("' . self::COUNTRY_COOKIE . '=")!==-1||document.cookie.indexOf("' . self::PREF_COOKIE . '=")!==-1)return;d.classList.add("zpl-geo-pending");var c=new AbortController(),t=setTimeout(function(){c.abort()},2200);fetch(' . $restJs . ',{credentials:"same-origin",cache:"no-store",signal:c.signal}).then(function(r){return r.ok?r.json():null}).then(function(x){clearTimeout(t);var cc=x&&typeof x.country==="string"?x.country.toUpperCase():"";if(/^[A-Z]{2}$/.test(cc)){document.cookie="' . self::COUNTRY_COOKIE . '="+cc+";path=/;max-age=' . self::COOKIE_TTL . ';SameSite=Lax;Secure";if(cc!=="PL"){location.replace(' . $targetJs . '+location.search+location.hash);return}}d.classList.remove("zpl-geo-pending")}).catch(function(){clearTimeout(t);d.classList.remove("zpl-geo-pending")})}catch(e){document.documentElement.classList.remove("zpl-geo-pending")}})();</script>';

        return preg_replace('~<head\b[^>]*>~i', '$0' . $tag, $html, 1) ?? $html;
    }

    /**
     * Force Cookiebot's banner language to match the actual PL/EN URL and switch
     * the CMP bootstrap to manual mode.
     *
     * Cookiebot automatic blocking intentionally loads uc.js synchronously. That is
     * safe but expensive on mobile: the page parser waits for the CMP before it can
     * continue. In manual mode Cookiebot officially supports async loading. The
     * matching non-essential resources are marked by cookiebot_manual_blocking().
     */
    public static function cookiebot_culture(string $html, string $lang): string {
        $culture = $lang === 'en' ? 'en' : 'pl';
        $rx = '~<script\b(?=[^>]*(?:\bid=["\']Cookiebot["\']|\bdata-cbid=|consent\.cookiebot\.(?:com|eu)/uc\.js))[^>]*>~i';

        return preg_replace_callback($rx, static function ($m) use ($culture) {
            $tag = $m[0];

            // Language must follow the URL, not browser preference.
            if (preg_match('~\bdata-culture\s*=~i', $tag)) {
                $tag = preg_replace('~\bdata-culture\s*=\s*(["\'])[^"\']*\1~i', 'data-culture="' . $culture . '"', $tag, 1) ?? $tag;
            } else {
                $tag = preg_replace('~>$~', ' data-culture="' . $culture . '">', $tag, 1) ?? $tag;
            }

            // v1.0.11 / Suite 2.2.786: manual Cookiebot mode for performance.
            // Automatic mode (data-blockingmode="auto") must be synchronous; manual
            // mode is allowed to be async when trackers are explicitly marked.
            $tag = preg_replace('~\sdata-blockingmode\s*=\s*(["\'])[^"\']*\1~i', '', $tag) ?? $tag;
            $tag = preg_replace('~\sdefer(?:\s*=\s*(["\'])[^"\']*\1)?~i', '', $tag) ?? $tag;
            if (!preg_match('~\sasync(?:\s|=|>)~i', $tag)) {
                $tag = preg_replace('~>$~', ' async data-zp-cookiebot-manual="1">', $tag, 1) ?? $tag;
            } elseif (!preg_match('~\bdata-zp-cookiebot-manual\s*=~i', $tag)) {
                $tag = preg_replace('~>$~', ' data-zp-cookiebot-manual="1">', $tag, 1) ?? $tag;
            }

            return $tag;
        }, $html) ?? $html;
    }

    /**
     * Manual prior-consent markup for the third-party resources used by the site.
     *
     * This deliberately targets only known tracking/advertising resources. Necessary
     * site scripts, Cloudflare Turnstile and first-party UI code are untouched.
     * Existing Cookiebot markup is also preserved.
     */
    public static function cookiebot_manual_blocking(string $html): string {
        if ($html === '' || stripos($html, 'consent.cookiebot.') === false) { return $html; }

        $html = preg_replace_callback('~<script\b[^>]*>[\s\S]*?</script>~i', static function ($m) {
            $tag = $m[0];
            $open = '';
            if (!preg_match('~^<script\b[^>]*>~i', $tag, $om)) { return $tag; }
            $open = $om[0];

            // Never touch Cookiebot itself, structured data, or scripts already marked.
            if (preg_match('~(?:\bid=["\']Cookiebot["\']|\bdata-cbid=|consent\.cookiebot\.(?:com|eu)/uc\.js)~i', $open)) { return $tag; }
            if (preg_match('~\btype\s*=\s*(["\'])application/(?:ld\+json|json)\1~i', $open)) { return $tag; }
            if (preg_match('~\bdata-cookieconsent\s*=~i', $open)) { return $tag; }

            $id = strtolower((string) self::script_attr($open, 'id'));
            // Our own loader contains literal strings like gtag()/fbq() in its source.
            if ($id !== '' && (strpos($id, 'zp-suite-') === 0 || strpos($id, 'zpl-') === 0)) { return $tag; }

            $src = (string) self::script_attr($open, 'src');
            if ($src === '') { $src = (string) self::script_attr($open, 'data-zp-src'); }
            $category = self::cookie_category_for_script($src, $tag);
            if ($category === null) { return $tag; }

            $newOpen = self::manualize_script_open($open, $category);
            return $newOpen . substr($tag, strlen($open));
        }, $html) ?? $html;

        // Manual blocking for common third-party embeds/pixels. This is a safety net;
        // most requests in the current site are created by scripts and are stopped above.
        $html = preg_replace_callback('~<(iframe|img)\b[^>]*>~i', static function ($m) {
            $tag = $m[0];
            if (preg_match('~\bdata-cookieconsent\s*=~i', $tag)) { return $tag; }
            $src = (string) self::script_attr($tag, 'src');
            if ($src === '') { return $tag; }
            $srcL = strtolower(html_entity_decode($src, ENT_QUOTES | ENT_HTML5, 'UTF-8'));

            $marketing = preg_match('~(?:youtube(?:-nocookie)?\.com/(?:embed|watch)|youtu\.be/|player\.vimeo\.com|facebook\.com/(?:plugins|tr)|doubleclick\.net|googleadservices\.com|google\.com/maps/embed)~i', $srcL);
            if (!$marketing) { return $tag; }

            $tag = preg_replace('~\s+src\s*=\s*(["\'])[^"\']*\1~i', '', $tag, 1) ?? $tag;
            if (!preg_match('~\bdata-cookieblock-src\s*=~i', $tag)) {
                $tag = preg_replace('~>$~', ' data-cookieblock-src="' . esc_attr($src) . '" data-cookieconsent="marketing">', $tag, 1) ?? $tag;
            }
            return $tag;
        }, $html) ?? $html;

        return $html;
    }

    /** Extract an HTML attribute from a tag. */
    private static function script_attr(string $tag, string $name): ?string {
        $n = preg_quote($name, '~');
        // Require actual attribute whitespace so asking for `src` cannot accidentally
        // match the suffix of `data-zp-src`.
        if (preg_match('~\s+' . $n . '\s*=\s*(["\'])(.*?)\1~is', $tag, $m)) { return $m[2]; }
        return null;
    }

    /** Decide which Cookiebot category should gate a script. */
    private static function cookie_category_for_script(string $src, string $tag): ?string {
        $srcL = strtolower(html_entity_decode($src, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        $tagL = strtolower($tag);

        // Marketing / advertising first. GTM is treated as marketing because this site's
        // container also boots advertising/Meta tags. GA4 is present separately as statistics.
        if ($srcL !== '' && preg_match('~(?:googletagmanager\.com/gtm\.js|googleadservices\.com|googlesyndication\.com|doubleclick\.net|connect\.facebook\.net|facebook\.net/.+fbevents|facebook\.com/tr|snap\.licdn\.com|linkedin\.com/insight|analytics\.tiktok\.com)~i', $srcL)) {
            return 'marketing';
        }
        if ($srcL !== '' && preg_match('~googletagmanager\.com/gtag/js~i', $srcL)) {
            return preg_match('~(?:[?&](?:id|tid)=AW-|AW-)~i', $srcL) ? 'marketing' : 'statistics';
        }
        if ($srcL !== '' && preg_match('~(?:google-analytics\.com|analytics\.google\.com)~i', $srcL)) {
            return 'statistics';
        }

        // Inline bootstraps. Keep the signatures strict so our own helper code is not caught.
        if (preg_match('~(?:fbq\s*\(\s*["\']init["\']|connect\.facebook\.net/.+fbevents\.js)~i', $tag)) { return 'marketing'; }
        if (preg_match('~googletagmanager\.com/gtm\.js~i', $tag) && preg_match('~GTM-[A-Z0-9]+~i', $tag)) { return 'marketing'; }
        if (preg_match('~gtag\s*\(\s*["\']config["\']\s*,\s*["\']AW-[^"\']+["\']~i', $tag)) { return 'marketing'; }
        if (preg_match('~(?:googleadservices\.com|doubleclick\.net)~i', $tagL)) { return 'marketing'; }
        if (preg_match('~gtag\s*\(\s*["\']config["\']\s*,\s*["\']G-[A-Z0-9]+["\']~i', $tag)) { return 'statistics'; }

        // Legacy Suite-delayed scripts are known third-party scripts. If a clear category
        // is present in their source/text, keep them gated rather than reactivating on scroll.
        if (stripos($tag, 'data-zp-delay-thirdparty') !== false) {
            if (preg_match('~(?:facebook|fbevents|fbq\s*\(|GTM-|googleadservices|doubleclick)~i', $tag)) { return 'marketing'; }
            if (preg_match('~(?:gtag\s*\(|google-analytics|analytics\.google)~i', $tag)) { return 'statistics'; }
        }

        return null;
    }

    /** Turn a script opening tag into Cookiebot manual prior-consent markup. */
    private static function manualize_script_open(string $open, string $category): string {
        $storedSrc = self::script_attr($open, 'data-zp-src');
        $hasSrc = self::script_attr($open, 'src') !== null;

        // Remove our legacy delay attributes; Cookiebot becomes the single source of truth.
        $open = preg_replace('~\sdata-zp-delay-thirdparty(?:\s*=\s*(["\'])[^"\']*\1)?~i', '', $open) ?? $open;
        $open = preg_replace('~\sdata-zp-inline-thirdparty(?:\s*=\s*(["\'])[^"\']*\1)?~i', '', $open) ?? $open;
        $open = preg_replace('~\sdata-zp-src\s*=\s*(["\'])[^"\']*\1~i', '', $open) ?? $open;
        $open = preg_replace('~\stype\s*=\s*(["\'])[^"\']*\1~i', '', $open) ?? $open;
        $open = preg_replace('~\sdata-cookieconsent\s*=\s*(["\'])[^"\']*\1~i', '', $open) ?? $open;

        if (!$hasSrc && $storedSrc !== null && $storedSrc !== '') {
            $open = preg_replace('~>$~', ' src="' . esc_attr($storedSrc) . '">', $open, 1) ?? $open;
        }

        return preg_replace('~>$~', ' type="text/plain" data-cookieconsent="' . esc_attr($category) . '">', $open, 1) ?? $open;
    }

    public static function remember_country(string $country): void {
        $country = self::clean_country($country);
        if ($country === null || headers_sent()) { return; }
        self::set_cookie(self::COUNTRY_COOKIE, $country, self::COOKIE_TTL);
        $_COOKIE[self::COUNTRY_COOKIE] = $country;
    }

    public static function set_preference_cookie(string $lang): void {
        if (!in_array($lang, ['pl', 'en'], true) || headers_sent()) { return; }
        self::set_cookie(self::PREF_COOKIE, $lang, 30 * DAY_IN_SECONDS);
        $_COOKIE[self::PREF_COOKIE] = $lang;
    }

    private static function set_cookie(string $name, string $value, int $ttl): void {
        $secure = is_ssl();
        setcookie($name, $value, [
            'expires' => time() + $ttl,
            'path' => '/',
            'secure' => $secure,
            'httponly' => false,
            'samesite' => 'Lax',
        ]);
    }

    private static function clean_country(string $v): ?string {
        $v = strtoupper(trim($v));
        return preg_match('~^[A-Z]{2}$~', $v) ? $v : null;
    }

    private static function header_country(): ?string {
        $keys = [
            'HTTP_CF_IPCOUNTRY',                 // Cloudflare
            'HTTP_CLOUDFRONT_VIEWER_COUNTRY',    // AWS CloudFront
            'HTTP_X_VERCEL_IP_COUNTRY',          // Vercel
            'HTTP_X_COUNTRY_CODE',
            'HTTP_X_GEO_COUNTRY',
            'GEOIP_COUNTRY_CODE',
            'HTTP_X_APPENGINE_COUNTRY',
        ];
        foreach ($keys as $key) {
            $v = self::clean_country((string) ($_SERVER[$key] ?? ''));
            if ($v !== null && !in_array($v, ['XX', 'T1'], true)) { return $v; }
        }
        $edge = (string) ($_SERVER['HTTP_X_AKAMAI_EDGESCAPE'] ?? '');
        if ($edge !== '' && preg_match('~(?:^|,)\s*country_code=([A-Z]{2})\b~i', $edge, $m)) {
            return self::clean_country($m[1]);
        }
        return null;
    }

    private static function ip(): string {
        $candidates = [];
        foreach (['HTTP_CF_CONNECTING_IP', 'HTTP_TRUE_CLIENT_IP', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'] as $k) {
            if (!empty($_SERVER[$k])) { $candidates[] = trim((string) $_SERVER[$k]); }
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            foreach (explode(',', (string) $_SERVER['HTTP_X_FORWARDED_FOR']) as $x) { $candidates[] = trim($x); }
        }
        foreach ($candidates as $ip) {
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) { return $ip; }
        }
        foreach ($candidates as $ip) {
            if (filter_var($ip, FILTER_VALIDATE_IP)) { return $ip; }
        }
        return '';
    }

    private static function is_bot(): bool {
        $ua = strtolower((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''));
        if ($ua === '') { return false; }
        // AI fetchers without "bot" in their name (Claude-User, Perplexity-User…) get the address they asked for too.
        return (bool) preg_match('~(?:bot|crawler|spider|slurp|bingpreview|facebookexternalhit|whatsapp|telegrambot|twitterbot|linkedinbot|pinterest|chrome-lighthouse|lighthouse|pagespeed|google-inspectiontool|googleother|chatgpt-user|claude-user|perplexity-user|mistralai-user|meta-external|cohere-ai)~i', $ua);
    }
}
