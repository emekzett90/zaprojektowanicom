<?php
namespace ZPL;

if (!defined('ABSPATH')) { exit; }

/**
 * Lightweight geo language routing.
 *
 * Goals:
 * - Poland => keep Polish URLs by default.
 * - Any other detected country => redirect Polish URLs to their /en/ equivalent (only pages that have
 *   a real English version, the same ones that get hreflang).
 * - Never geo-redirect crawlers / Lighthouse / PageSpeed.
 * - Prefer CDN/hosting country headers (zero extra request); otherwise the bundled list of Polish IP
 *   ranges (data/geo, tools/geo_pl_ranges.py) decides on the server, before any HTML is sent. Both are
 *   checked on every request, so a trip abroad or a VPN is not remembered.
 * - Only if the visitor's address is unknown (private/proxy IP): a tiny same-origin REST lookup once,
 *   cached in a first-party cookie. No outside service is ever asked.
 * - A browser that knows Polish (pl anywhere in Accept-Language) stays on Polish pages (Poles abroad),
 *   setting "polish_stays".
 * - Logged-in WordPress users are never redirected (previews and editing keep working).
 * - A manual language choice wins: PL keeps Polish pages, EN sends Polish addresses to their English version,
 *   except in a browser that knows Polish (Suite 2.9.12).
 * - Only plain page views are redirected: no form posts, background fetches or addresses with their own
 *   parameters (search, feeds…); campaign tags such as utm_* and gclid go along to the English page.
 */
final class Geo {
    const COUNTRY_COOKIE = 'zpl_geo_country'; // only the result of the REST fallback below
    const PREF_COOKIE = 'zpl_lang_pref';
    const COOKIE_TTL = 604800; // 7 days
    const PREF_TTL = 31536000; // 365 days: set by the server, so Safari keeps it too
    /** Query keys that may go along to the English page; any other parameter keeps the Polish page. */
    const QUERY_OK = '~^(?:utm_[a-z0-9_]+|gclid|gclsrc|gbraid|wbraid|dclid|gad_[a-z0-9_]+|fbclid|msclkid|ttclid|twclid|li_fat_id|srsltid|_gl|_ga|mc_cid|mc_eid|igshid|igsh|yclid|ref)$~i';
    const SETTINGS = 'zpl_geo';        // ['enabled' => 0|1, 'polish_stays' => 0|1], both on by default
    const STATS = 'zpl_geo_stats';     // ['Y-m-d' => server-side redirects that day], last 60 days

    const REASONS = [
        'off' => 'Przekierowanie wyłączone w ustawieniach.',
        'en' => 'Adres jest już angielski.',
        'bot' => 'Robot lub narzędzie (Google, Bing, Lighthouse, PageSpeed…): nigdy nie przekierowujemy.',
        'method' => 'To nie jest zwykłe wejście na stronę (np. wysłanie formularza albo ładowanie w tle).',
        'query' => 'Adres ma własne parametry (np. wyszukiwanie ?s=): zostaje po polsku.',
        'pref' => 'Gość sam wybrał wersję polską przełącznikiem PL/EN.',
        'logged-in' => 'Zalogowany użytkownik WordPressa: bez przekierowania (podgląd i edycja działają normalnie).',
        'switch' => 'Gość przeszedł z wersji angielskiej na polską: zapamiętujemy wybór polskiego.',
        'pref-en' => 'Gość sam wybrał wersję angielską przełącznikiem PL/EN: przekierowanie na wersję angielską.',
        'no-en' => 'Ta strona nie ma jeszcze wersji angielskiej.',
        'polish-browser' => 'Przeglądarka zna polski: zostaje po polsku.',
        'unknown' => 'Nie udało się ustalić kraju: zostaje po polsku.',
        'pl' => 'Gość z Polski: zostaje po polsku.',
        'redirect' => 'Gość spoza Polski: przekierowanie na wersję angielską.',
    ];

    public static function settings(): array {
        $o = get_option(self::SETTINGS, []);
        $o = is_array($o) ? $o : [];
        return [
            'enabled' => !array_key_exists('enabled', $o) || !empty($o['enabled']),
            'polish_stays' => !array_key_exists('polish_stays', $o) || !empty($o['polish_stays']),
        ];
    }

    /** A manual PL/EN selection made with the language switcher. */
    public static function preference(): ?string {
        $v = strtolower(trim((string) ($_COOKIE[self::PREF_COOKIE] ?? '')));
        return in_array($v, ['pl', 'en'], true) ? $v : null;
    }

    /**
     * ISO 3166-1 alpha-2 country code, or ZZ for "not Poland", decided on the server: CDN/hosting header,
     * then the bundled Polish ranges, then the REST fallback's cookie (addresses the server can't place),
     * then WooCommerce. Nothing is stored, so the next request is checked afresh. No remote HTTP here.
     */
    public static function country(): ?string {
        $header = self::header_country();
        if ($header !== null) { return $header; }

        // Bundled Polish IP ranges: PL or ZZ (anywhere else), decided on the server with no remote call.
        $local = self::local_country(self::ip());
        if ($local !== null) { return $local; }

        $cookie = self::clean_country((string) ($_COOKIE[self::COUNTRY_COOKIE] ?? ''));
        if ($cookie !== null) { return $cookie; }

        // WooCommerce/MaxMind, when already available on the site. No remote fallback here.
        if (class_exists('\\WC_Geolocation') && is_callable(['\\WC_Geolocation', 'geolocate_ip'])) {
            try {
                $g = \WC_Geolocation::geolocate_ip(self::ip(), false, false);
                $wc = self::clean_country(is_array($g) ? (string) ($g['country'] ?? '') : '');
                if ($wc !== null) { return $wc; }
            } catch (\Throwable $e) {}
        }

        return null;
    }

    /** Country for /wp-json/zpl/v1/geo: the same checks as a page view. No outside service is asked. */
    public static function country_remote(): ?string {
        return self::country();
    }

    /** Server-side redirect decision. Only redirects PL => EN. */
    public static function redirect_target(string $lang, string $source, ?bool $mapped = null): ?string {
        $d = self::decide($lang, $source, $mapped, true);
        return $d['target'];
    }

    /**
     * Why a request does (not) go to /en/. With $live the real request is used (cookies, headers, IP) and the
     * country is looked up only when everything else allows a redirect; the admin tester passes $ctx instead
     * (ip, accept, ua, query, pref).
     * Returns ['target' => EN path|null, 'reason' => key of REASONS, 'country' => ?string].
     */
    public static function decide(string $lang, string $source, ?bool $mapped, bool $live, array $ctx = []): array {
        $out = static function (string $reason, ?string $target = null, ?string $country = null): array {
            return ['target' => $target, 'reason' => $reason, 'country' => $country];
        };
        $set = self::settings();
        if (!$set['enabled']) { return $out('off'); }
        if ($lang !== 'pl') { return $out('en'); }
        $ua = $live ? (string) ($_SERVER['HTTP_USER_AGENT'] ?? '') : (string) ($ctx['ua'] ?? '');
        if (self::is_bot_ua($ua)) { return $out('bot'); }
        $method = $live ? strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET')) : 'GET';
        if (!in_array($method, ['GET', 'HEAD'], true) || ($live && !self::is_navigation())) { return $out('method'); }

        // Search, feeds, ?p= and other parameters of their own keep the Polish page; campaign tags go along.
        if ($live) {
            $keys = array_keys($_GET);
        } else {
            parse_str((string) ($ctx['query'] ?? ''), $q);
            $keys = array_keys($q);
        }
        foreach ($keys as $k) {
            if (!preg_match(self::QUERY_OK, (string) $k)) { return $out('query'); }
        }

        // A conscious switch to Polish is stronger than automatic country routing.
        $pref = $live ? self::preference() : (in_array($ctx['pref'] ?? '', ['pl', 'en'], true) ? $ctx['pref'] : null);
        if ($pref === 'pl') { return $out('pref'); }

        if ($mapped === null) { $mapped = isset(Router::routes()[Router::norm_path($source)]); }
        if (!$mapped || !Dict::has_page($source)) { return $out('no-en'); }

        if ($live && function_exists('is_user_logged_in') && is_user_logged_in()) { return $out('logged-in'); }

        // Coming from our own English page to this Polish one = the switch without JavaScript.
        if ($live && self::from_english_page()) {
            self::set_preference_cookie('pl');
            return $out('switch');
        }

        // A browser that reads Polish opens Polish pages, also after English was chosen once (Suite 2.9.12: a tap on
        // EN, even by mistake, sent every Polish address of a Polish visitor to English for a year). The switch still
        // gives English in one tap, and the rest of that visit follows it.
        $accept = $live ? (string) ($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '') : (string) ($ctx['accept'] ?? '');
        if ($set['polish_stays'] && self::prefers_polish($accept)) { return $out('polish-browser'); }

        // A conscious switch to English: Polish addresses (e.g. from Google) open in English too.
        if ($pref === 'en') { return $out('pref-en', Router::en_path($source)); }

        $country = $live ? self::country() : (isset($ctx['ip']) ? self::country_for_ip((string) $ctx['ip']) : null);
        if ($country === null) { return $out('unknown'); }
        if ($country === 'PL') { return $out('pl', null, 'PL'); }

        return $out('redirect', Router::en_path($source), $country);
    }

    /**
     * A top-level page view. Browsers say so in Sec-Fetch-Mode/Dest; requests without those headers
     * (older browsers) count as page views, background fetches, frames and the like do not.
     */
    private static function is_navigation(): bool {
        $mode = strtolower((string) ($_SERVER['HTTP_SEC_FETCH_MODE'] ?? ''));
        $dest = strtolower((string) ($_SERVER['HTTP_SEC_FETCH_DEST'] ?? ''));
        return ($mode === '' || $mode === 'navigate') && ($dest === '' || $dest === 'document');
    }

    /** Country for an IP address without cookies (admin tester): header-free, local ranges, then WooCommerce. */
    public static function country_for_ip(string $ip): ?string {
        $local = self::local_country($ip);
        if ($local !== null) { return $local; }
        return null;
    }

    /** True when the browser lists Polish (pl, pl-PL…) among its languages with q > 0: its owner reads Polish. */
    public static function prefers_polish(string $header): bool {
        foreach (explode(',', $header) as $part) {
            $bits = explode(';', trim($part));
            $tag = strtolower(trim((string) $bits[0]));
            if ($tag !== 'pl' && strpos($tag, 'pl-') !== 0) { continue; }
            $q = 1.0;
            foreach (array_slice($bits, 1) as $p) {
                if (preg_match('~^\s*q\s*=\s*([0-9.]+)~i', $p, $m)) { $q = (float) $m[1]; }
            }
            if ($q > 0) { return true; }
        }
        return false;
    }

    /** Same-site referrer on an English page (/en/...). */
    private static function from_english_page(): bool {
        $ref = (string) ($_SERVER['HTTP_REFERER'] ?? '');
        if ($ref === '') { return false; }
        $p = wp_parse_url($ref);
        $home = wp_parse_url(home_url('/'));
        if (!is_array($p) || empty($p['host']) || !is_array($home)) { return false; }
        $h = strtolower((string) $p['host']);
        $hh = strtolower((string) ($home['host'] ?? ''));
        if ($h !== $hh && $h !== 'www.' . $hh && 'www.' . $h !== $hh) { return false; }
        $rel = Router::norm_path(Router::rel((string) ($p['path'] ?? '/')));
        return $rel === '/en/' || strpos($rel, '/en/') === 0;
    }

    /**
     * PL when the address is in the bundled Polish ranges, ZZ for any other public address,
     * null for private/invalid addresses or when the data files are missing.
     */
    public static function local_country(string $ip): ?string {
        if ($ip === '' || !filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) { return null; }
        $bin = @inet_pton($ip);
        if ($bin === false) { return null; }
        if (strlen($bin) === 16 && substr($bin, 0, 12) === str_repeat("\0", 10) . "\xff\xff") { $bin = substr($bin, 12); }
        $file = ZPL_DIR . 'data/geo/' . (strlen($bin) === 4 ? 'pl-v4.bin' : 'pl-v6.bin');
        static $cache = [];
        if (!isset($cache[$file])) { $cache[$file] = is_readable($file) ? (string) file_get_contents($file) : ''; }
        $data = $cache[$file];
        $w = strlen($bin);
        if ($data === '' || strlen($data) % (2 * $w) !== 0) { return null; }
        $lo = 0;
        $hi = intdiv(strlen($data), 2 * $w) - 1;
        while ($lo <= $hi) {
            $mid = ($lo + $hi) >> 1;
            $start = substr($data, $mid * 2 * $w, $w);
            if (strcmp($bin, $start) < 0) { $hi = $mid - 1; continue; }
            $end = substr($data, $mid * 2 * $w + $w, $w);
            if (strcmp($bin, $end) > 0) { $lo = $mid + 1; continue; }
            return 'PL';
        }
        return 'ZZ';
    }

    /** Info about the bundled ranges for the admin page. */
    public static function data_info(): array {
        $f = ZPL_DIR . 'data/geo/meta.php';
        $m = is_file($f) ? (array) include $f : [];
        return ['built' => (string) ($m['built'] ?? ''), 'v4' => (int) ($m['v4'] ?? 0), 'v6' => (int) ($m['v6'] ?? 0)];
    }

    /** One more server-side geo redirect today (non-autoloaded option, last 60 days). */
    public static function count_redirect(): void {
        $s = get_option(self::STATS, []);
        $s = is_array($s) ? $s : [];
        $day = function_exists('wp_date') ? wp_date('Y-m-d') : gmdate('Y-m-d');
        $s[$day] = (int) ($s[$day] ?? 0) + 1;
        krsort($s);
        update_option(self::STATS, array_slice($s, 0, 60, true), false);
    }

    /**
     * Early client fallback for sites where page cache/CDN does not expose a country header to PHP.
     * It calls our own REST endpoint once; the endpoint may do the country-only remote lookup.
     */
    public static function inject_client(string $html, string $lang, string $source): string {
        if ($lang !== 'pl' || self::preference() !== null || self::is_bot()) { return $html; }
        $set = self::settings();
        if (!$set['enabled'] || !Router::$mapped || !Dict::has_page($source)) { return $html; }
        if ($set['polish_stays'] && self::prefers_polish((string) ($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? ''))) { return $html; }
        if (self::country() !== null) { return $html; }
        if (stripos($html, '</head>') === false) { return $html; }

        $rest = esc_url_raw(rest_url('zpl/v1/geo'));
        $target = Router::base() . Router::en_path($source);
        $restJs = wp_json_encode($rest, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
        $targetJs = wp_json_encode($target, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
        $polish = $set['polish_stays'] ? 'if([].concat(navigator.languages||[],navigator.language||"").some(function(l){return /^pl(?:-|$)/i.test(l)}))return;' : '';
        // An unknown country is remembered as "--" for a day, so a failed lookup is not repeated on every page.
        $tag = '<style id="zpl-geo-pending-css">html.zpl-geo-pending #CybotCookiebotDialog{visibility:hidden!important}</style>'
            . '<script id="zpl-geo-boot">(function(){try{var d=document.documentElement,k=document.cookie;if(k.indexOf("' . self::COUNTRY_COOKIE . '=")!==-1||k.indexOf("' . self::PREF_COOKIE . '=")!==-1)return;if(/bot|crawl|spider|lighthouse|pagespeed|headless/i.test(navigator.userAgent||""))return;' . $polish . 'd.classList.add("zpl-geo-pending");var c=new AbortController(),t=setTimeout(function(){c.abort()},2200);fetch(' . $restJs . ',{credentials:"same-origin",cache:"no-store",signal:c.signal}).then(function(r){return r.ok?r.json():null}).then(function(x){clearTimeout(t);var cc=x&&typeof x.country==="string"?x.country.toUpperCase():"",v=/^[A-Z]{2}$/.test(cc)?cc:"--";document.cookie="' . self::COUNTRY_COOKIE . '="+v+";path=/;max-age="+(v==="--"?86400:' . self::COOKIE_TTL . ')+";SameSite=Lax"+(location.protocol==="https:"?";Secure":"");if(v!=="--"&&v!=="PL"){location.replace(' . $targetJs . '+location.search+location.hash);return}d.classList.remove("zpl-geo-pending")}).catch(function(){clearTimeout(t);d.classList.remove("zpl-geo-pending")})}catch(e){document.documentElement.classList.remove("zpl-geo-pending")}})();</script>';

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
        self::set_cookie(self::PREF_COOKIE, $lang, self::PREF_TTL);
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

    /** Country from CDN/hosting headers (Cloudflare, CloudFront…), null when the server sends none. */
    public static function header_country(): ?string {
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

    /** Visitor address as seen by the geo check (shown on the admin page). */
    public static function visitor_ip(): string { return self::ip(); }

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

    public static function is_bot(): bool {
        return self::is_bot_ua((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''));
    }

    public static function is_bot_ua(string $ua): bool {
        $ua = strtolower(trim($ua));
        // Every browser sends a User-Agent; a request without one is a script.
        if ($ua === '') { return true; }
        // AI fetchers without "bot" in their name (Claude-User, Perplexity-User…) and link previews (Outlook, Teams,
        // Skype) get the address they asked for too.
        return (bool) preg_match('~(?:bot|crawl|spider|slurp|bingpreview|microsoftpreview|skypeuripreview|facebookexternalhit|whatsapp|embedly|iframely|lighthouse|pagespeed|headless|google-|googleother|mediapartners-google|feedfetcher|apis-google|chatgpt-user|claude-user|claude-web|anthropic|perplexity|mistralai-user|meta-external|cohere-ai|gptbot|ccbot|gtmetrix|pingdom|uptime|ptst|validator|curl/|wget/|python-|go-http-client|okhttp|axios/|node-fetch|java/|libwww|httpclient|scrapy)~i', $ua);
    }
}
