<?php
// ZP Suite managed HTML cache 20261009 v1. Direct requests execute no cached content.
// ZP Suite 3.0.1: one copy per device class (phone or computer), decided exactly like wp_is_mobile(),
// because the theme strips hero videos and lazy-loads some images for phones on the server.
// 14.10.2026: search robots and speed tests get the copy too (they see the same Polish page), and so do visitors
// whose only cookies are the consent and statistics ones; copies are still made only from visits without cookies.
// 3.1.1: a MISS sends the same no-cache headers as a HIT, and a path's version (EN pages) is part of the key.
// 3.1.1: English pages ("en:<version>" from Tłumacz EN) are served from the cache to every browser language: Geo never
// redirects an English page and its HTML reads no language cookie. A visitor whom Geo places in Poland gets the Polish copy
// whatever the browser's language, and so does Lighthouse 10+, which no longer says "Lighthouse" in its user agent.
// 3.2.31: a verified explicit Polish preference may read the same Polish copy. A session's English choice,
// unknown/auth cookies and ambiguous raw cookie headers still run WordPress; only cookie-free visits write copies.
if (!defined('ABSPATH') || !defined('WP_CONTENT_DIR')) { exit; }

final class ZP_Cache_Early_20261009 {
    public const PREFIX = "<?php exit; ?>\n";
    public const MAX_HTML = 4194304;
    /** Sent with every page this file answers or lets WordPress build: browsers and the server's own cache ask again each time. */
    public const CACHE_CONTROL = 'Cache-Control: private, no-cache, max-age=0';
    /**
     * Cookies that never change the page: the consent choice (Cookiebot) and statistics and ads identifiers set by
     * scripts (Google, Meta, Microsoft, Hotjar, TikTok, Cloudflare), plus the country noted for the language switch,
     * which a Polish browser and a robot never use. Any other cookie (logged in, comment, password,
     * shop) skips the cache.
     */
    /** Language cookies are harmless on EN, and on PL only after an unambiguous effective Polish choice is verified. */
    public const EN_COOKIES = '~^zpl_lang_(?:pref|session)$~D';
    public const HARMLESS_COOKIES = '~^(?:CookieConsent(?:BulkSetting-[A-Za-z0-9-]+|BulkTicket)?|_ga(?:_[A-Za-z0-9]+)?|_gid|_gat(?:_[A-Za-z0-9_-]+)?|_gcl_[a-z]+|_gac_[A-Za-z0-9_.-]+|_dc_gtm_[A-Za-z0-9_-]+|FP(?:ID|LC|AU|GCL[A-Z]+)|_fbp|_fbc|_clck|_clsk|_uet[a-z]+|_hj[A-Za-z0-9_]+|_ttp|_tt_enable_cookie|__cf_bm|__cfruid|__cflb|_cfuvid|cf_clearance|zpl_geo_country)$~D';

    public static function policy(string $dir): ?array {
        $file = $dir . '/policy.php';
        if (is_link($dir) || is_link($file) || !is_file($file) || filesize($file) > 1048576) { return null; }
        $raw = @file_get_contents($file);
        if (!is_string($raw) || strpos($raw, self::PREFIX) !== 0) { return null; }
        $p = json_decode(substr($raw, strlen(self::PREFIX)), true);
        if (!is_array($p) || ($p['schema'] ?? null) !== 1 || ($p['enabled'] ?? null) !== true || !is_int($p['ttl'] ?? null) || $p['ttl'] < 60 || $p['ttl'] > 1800 || !is_string($p['generation'] ?? null) || !preg_match('/^[a-f0-9]{32}$/D', $p['generation']) || !is_string($p['host'] ?? null) || !is_array($p['paths'] ?? null) || !is_int($p['next_publication'] ?? null)) { return null; }
        return $p;
    }

    public static function eligible(array $p, array $server, array $cookies, ?int $now = null): string {
        if (($server['REQUEST_METHOD'] ?? '') !== 'GET') { return 'method'; }
        $uri = (string) ($server['REQUEST_URI'] ?? '');
        $en = self::is_en($p, $uri);
        $language = self::language_cookies($server, $cookies);
        if ($language === null) { return 'cookies'; }
        // Geo::decide() checks the session first, then the remembered preference, before browser language/country.
        // No other combination is newly allowed on PL; in particular a session's EN choice must still redirect.
        $polish_choice = $language['session'] === 'pl' || ($language['session'] === null && $language['pref'] === 'pl');
        if (($cookies || !empty($server['HTTP_COOKIE'])) && !self::harmless_cookies($server, $cookies, $en, $polish_choice)) { return 'cookies'; }
        if (!empty($server['HTTP_AUTHORIZATION']) || !empty($server['REDIRECT_HTTP_AUTHORIZATION']) || !empty($server['PHP_AUTH_USER'])) { return 'authorization'; }
        if (strtolower((string) ($server['HTTPS'] ?? '')) !== 'on' && ($server['HTTPS'] ?? '') !== '1') { return 'https'; }
        if (($server['HTTP_HOST'] ?? '') !== $p['host']) { return 'host'; }
        if ($uri === '' || strpos($uri, '?') !== false || strpos($uri, '#') !== false) { return 'query'; }
        // 3.1.1: an English page: one copy for every browser language and country (Geo::decide answers 'en' at once).
        if ($en) { return $p['next_publication'] && $p['next_publication'] <= ($now ?? time()) ? 'publication-due' : ''; }
        if (!isset($p['paths'][$uri]) || $p['paths'][$uri] !== true) { return 'path'; }
        if (empty($p['geo_polish_safe'])) { return 'geo-policy'; }
        $ref = parse_url((string) ($server['HTTP_REFERER'] ?? ''));
        if (is_array($ref) && ($ref['host'] ?? '') === $p['host'] && preg_match('~^/en(?:/|$)~', $ref['path'] ?? '')) { return 'language-switch'; }
        // Other languages must run Suite's PHP country/preference redirect on every request. Robots are never
        // redirected (Geo::decide) and get the Polish page whatever language they send.
        $polish = $polish_choice || self::is_bot((string) ($server['HTTP_USER_AGENT'] ?? ''));
        foreach ($polish ? [] : explode(',', (string) ($server['HTTP_ACCEPT_LANGUAGE'] ?? '')) as $part) {
            if (preg_match('/^\s*pl(?:-[a-z0-9]+)?\s*(?:;\s*q=([01](?:\.\d+)?))?\s*$/iD', $part, $m) && (!isset($m[1]) || (float) $m[1] > 0)) { $polish = true; }
        }
        // 3.1.1: a browser in another language gets the Polish copy when Geo places the visitor in Poland too. Anywhere else
        // Geo redirects a page with an English version on the server (302 to /en/…), so WordPress has to answer.
        if (!$polish && !self::geo_polish($p, $server, $cookies)) { return 'language'; }
        if ($p['next_publication'] && $p['next_publication'] <= ($now ?? time())) { return 'publication-due'; }
        return '';
    }

    /** 3.1.1: the policy lists this address as an English page with the version of its text ("en:<version>", Tłumacz EN). */
    public static function is_en(array $p, string $uri): bool {
        $entry = $p['paths'][$uri] ?? null;
        return is_string($entry) && strpos($entry, 'en:') === 0 && strpos($uri, '/en/') === 0;
    }

    /**
     * Read only canonical language choices, agreeing with PHP's parsed cookies. A duplicate/malformed language pair,
     * missing/extra parsed language cookie or encoded/ambiguous value fails closed; PHP/Geo resolves it instead.
     * Geo accepts some noncanonical values too, but those need not use the early cache. No cookie value is logged.
     * Other names retain the existing harmless_cookies() policy, including harmless cookies repeated across scopes.
     */
    public static function language_cookies(array $server, array $cookies): ?array {
        $raw = [];
        foreach (explode(';', (string) ($server['HTTP_COOKIE'] ?? '')) as $pair) {
            $pair = ltrim($pair, " \t");
            if (trim($pair) === '') { continue; }
            $parts = explode('=', $pair, 2);
            $name = trim($parts[0]);
            if (!preg_match(self::EN_COOKIES, $name)) { continue; }
            if (count($parts) !== 2 || $name !== $parts[0] || array_key_exists($name, $raw)) { return null; }
            $raw[$name] = $parts[1];
        }
        $language = ['pref' => null, 'session' => null];
        foreach ($language as $kind => $unused) {
            $name = 'zpl_lang_' . $kind;
            $has_raw = array_key_exists($name, $raw);
            $has_parsed = array_key_exists($name, $cookies);
            if (!$has_raw && !$has_parsed) { continue; }
            if (!$has_raw || !$has_parsed || !is_string($cookies[$name])
                || !in_array($raw[$name], ['pl', 'en'], true) || $raw[$name] !== $cookies[$name]) { return null; }
            $language[$kind] = $raw[$name];
        }
        return $language;
    }

    /**
     * True when every cookie of the request is one of HARMLESS_COOKIES (names from the raw header and from $_COOKIE);
     * language cookie names are allowed only on EN or after language_cookies() proved an effective Polish choice.
     */
    public static function harmless_cookies(array $server, array $cookies, bool $en = false, bool $polish_choice = false): bool {
        $names = array_map('strval', array_keys($cookies));
        foreach (explode(';', (string) ($server['HTTP_COOKIE'] ?? '')) as $pair) {
            $name = trim(explode('=', $pair, 2)[0]);
            if ($name !== '') { $names[] = $name; }
        }
        foreach ($names as $name) { if (!preg_match(self::HARMLESS_COOKIES, $name) && !(($en || $polish_choice) && preg_match(self::EN_COOKIES, $name))) { return false; } }
        return true;
    }

    /**
     * 3.1.1: Geo::decide() gives this request the Polish page with no redirect and no country script: Geo is off, or it
     * places the visitor in Poland the way Geo::country() does (CDN country header, then the bundled Polish address
     * ranges, then the country cookie of its lookup). The policy names the range files (key 'geo'); without it, false.
     */
    public static function geo_polish(array $p, array $server, array $cookies): bool {
        $g = $p['geo'] ?? null;
        if (!is_array($g)) { return false; }
        if (($g['on'] ?? true) === false) { return true; }
        $country = self::header_country($server);
        if ($country === null) { $country = self::local_country(self::ip($server), $g); }
        if ($country === null) {
            $c = strtoupper(trim((string) ($cookies['zpl_geo_country'] ?? '')));
            $country = preg_match('/^[A-Z]{2}$/D', $c) ? $c : null;
        }
        return $country === 'PL';
    }

    /** The same as Geo::header_country() in zaprojektowani-languages/includes/Geo.php: keep the two lists equal. */
    public static function header_country(array $server): ?string {
        foreach (['HTTP_CF_IPCOUNTRY', 'HTTP_CLOUDFRONT_VIEWER_COUNTRY', 'HTTP_X_VERCEL_IP_COUNTRY', 'HTTP_X_COUNTRY_CODE', 'HTTP_X_GEO_COUNTRY', 'GEOIP_COUNTRY_CODE', 'HTTP_X_APPENGINE_COUNTRY'] as $key) {
            $v = strtoupper(trim((string) ($server[$key] ?? '')));
            if (preg_match('/^[A-Z]{2}$/D', $v) && $v !== 'XX' && $v !== 'T1') { return $v; }
        }
        $edge = (string) ($server['HTTP_X_AKAMAI_EDGESCAPE'] ?? '');
        if ($edge !== '' && preg_match('~(?:^|,)\s*country_code=([A-Z]{2})\b~i', $edge, $m)) { return strtoupper($m[1]); }
        return null;
    }

    /** The same as Geo::ip(): the first public address of the usual headers, else any valid one. */
    public static function ip(array $server): string {
        $candidates = [];
        foreach (['HTTP_CF_CONNECTING_IP', 'HTTP_TRUE_CLIENT_IP', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'] as $k) { if (!empty($server[$k])) { $candidates[] = trim((string) $server[$k]); } }
        if (!empty($server['HTTP_X_FORWARDED_FOR'])) { foreach (explode(',', (string) $server['HTTP_X_FORWARDED_FOR']) as $x) { $candidates[] = trim($x); } }
        foreach ($candidates as $ip) { if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) { return $ip; } }
        foreach ($candidates as $ip) { if (filter_var($ip, FILTER_VALIDATE_IP)) { return $ip; } }
        return '';
    }

    /** The same as Geo::local_country(): PL inside the bundled Polish ranges, ZZ for another public address, else null. */
    public static function local_country(string $ip, array $g): ?string {
        if ($ip === '' || !filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) { return null; }
        $bin = @inet_pton($ip);
        if ($bin === false) { return null; }
        if (strlen($bin) === 16 && substr($bin, 0, 12) === str_repeat("\0", 10) . "\xff\xff") { $bin = substr($bin, 12); }
        $w = strlen($bin);
        $file = (string) ($g[$w === 4 ? 'v4' : 'v6'] ?? '');
        if ($file === '' || is_link($file) || !is_file($file) || filesize($file) > 4194304) { return null; }
        $data = (string) @file_get_contents($file);
        if ($data === '' || strlen($data) % (2 * $w) !== 0) { return null; }
        $lo = 0; $hi = intdiv(strlen($data), 2 * $w) - 1;
        while ($lo <= $hi) {
            $mid = ($lo + $hi) >> 1;
            if (strcmp($bin, substr($data, $mid * 2 * $w, $w)) < 0) { $hi = $mid - 1; continue; }
            if (strcmp($bin, substr($data, $mid * 2 * $w + $w, $w)) > 0) { $lo = $mid + 1; continue; }
            return 'PL';
        }
        return 'ZZ';
    }

    /** The same test as Geo::is_bot_ua() in zaprojektowani-languages/includes/Geo.php: keep the two lists equal. */
    public static function is_bot(string $ua): bool {
        $ua = strtolower(trim($ua));
        if ($ua === '') { return true; }
        return (bool) preg_match('~(?:bot|crawl|spider|slurp|bingpreview|microsoftpreview|skypeuripreview|facebookexternalhit|whatsapp|embedly|iframely|lighthouse|pagespeed|android 11; moto g power \(2022\)\)|headless|google-|googleother|mediapartners-google|feedfetcher|apis-google|chatgpt-user|claude-user|claude-web|anthropic|perplexity|mistralai-user|meta-external|cohere-ai|gptbot|ccbot|gtmetrix|pingdom|uptime|ptst|validator|curl/|wget/|python-|go-http-client|okhttp|axios/|node-fetch|java/|libwww|httpclient|scrapy)~i', $ua);
    }

    /**
     * WordPress does not run for a copy, so Suite's AI visibility panel would miss these robot visits: one line each
     * (time, address, user agent) waits here until the panel's next count (seo-plan/ai.php), at most 256 KB.
     */
    public static function note_bot(string $dir, array $server): void {
        $file = $dir . '/bots.log';
        if (is_link($file) || (is_file($file) && filesize($file) > 262144)) { return; }
        $line = json_encode([time(), substr((string) ($server['REQUEST_URI'] ?? '/'), 0, 200), substr((string) ($server['HTTP_USER_AGENT'] ?? ''), 0, 300)], JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE);
        if (is_string($line)) { @file_put_contents($file, $line . "\n", FILE_APPEND | LOCK_EX); }
    }

    /** 'm' or 'd': the same test as WordPress's wp_is_mobile() (Client Hint first, then the user agent). */
    public static function device(array $server): string {
        if (isset($server['HTTP_SEC_CH_UA_MOBILE'])) { return $server['HTTP_SEC_CH_UA_MOBILE'] === '?1' ? 'm' : 'd'; }
        $ua = (string) ($server['HTTP_USER_AGENT'] ?? '');
        if ($ua === '') { return 'd'; }
        foreach (['Mobile', 'Android', 'Silk/', 'Kindle', 'BlackBerry', 'Opera Mini', 'Opera Mobi'] as $needle) { if (strpos($ua, $needle) !== false) { return 'm'; } }
        return 'd';
    }

    /** A Polish page's entry is true and its key is the same as in 3.1.0; an entry with a version (EN pages) adds it to the key. */
    public static function key(array $p, array $server): string {
        $entry = $p['paths'][$server['REQUEST_URI']] ?? true;
        return hash('sha256', $p['generation'] . "\nhttps\n" . $server['HTTP_HOST'] . "\n" . $server['REQUEST_URI'] . "\n" . self::device($server) . (is_string($entry) ? "\n" . $entry : ''));
    }

    public static function headers(): void {
        header(self::CACHE_CONTROL); header('Vary: Accept-Encoding, Cookie, Accept-Language, User-Agent, Sec-CH-UA-Mobile'); header('X-LiteSpeed-Cache-Control: no-cache');
    }

    public static function hit(string $dir, array $p, array $server, ?int $now = null): ?array {
        $key = self::key($p, $server); $file = $dir . '/zpc-' . $key . '.php';
        if (is_link($file) || !is_file($file) || filesize($file) > self::MAX_HTML + 8192) { return null; }
        $raw = @file_get_contents($file);
        if (!is_string($raw) || strpos($raw, self::PREFIX) !== 0) { return null; }
        $end = strpos($raw, "\n", strlen(self::PREFIX)); if ($end === false) { return null; }
        $meta = json_decode(substr($raw, strlen(self::PREFIX), $end - strlen(self::PREFIX)), true); $html = substr($raw, $end + 1); $now = $now ?? time();
        if (!is_array($meta) || ($meta['kind'] ?? '') !== 'zp-public-html-v1' || ($meta['key'] ?? '') !== $key || ($meta['generation'] ?? '') !== $p['generation'] || !is_int($meta['created'] ?? null) || !is_int($meta['expires'] ?? null) || $meta['created'] > $now || $meta['expires'] <= $now || $meta['expires'] > $meta['created'] + $p['ttl'] || ($meta['length'] ?? null) !== strlen($html) || !is_string($meta['html_sha256'] ?? null) || !hash_equals($meta['html_sha256'], hash('sha256', $html))) { return null; }
        return ['html' => $html, 'age' => $now - $meta['created']];
    }

    public static function store(string $dir, array $p, array $server, string $html, int $status, array $headers, ?int $now = null): bool {
        // Only a visit without cookies makes a copy: whatever a consent cookie might change on the server stays out of it.
        if (!empty($server['HTTP_COOKIE'])) { return false; }
        if ($status !== 200 || strlen($html) > self::MAX_HTML || stripos($html, '<!doctype html') === false || stripos($html, '</html>') === false || stripos($html, '<html') === false) { return false; }
        $content_type = false;
        foreach ($headers as $header) {
            if (stripos($header, 'Content-Type: text/html') === 0) { $content_type = true; }
            // The drop-in's own Cache-Control line (sent before WordPress ran) is fine; any other no-store/private/no-cache is not.
            if (preg_match('/^(?:Set-Cookie|Location|WWW-Authenticate):/i', $header) || ($header !== self::CACHE_CONTROL && preg_match('/^Cache-Control:.*(?:no-store|private|no-cache)/i', $header)) || preg_match('/^X-Robots-Tag:.*noindex/i', $header)) { return false; }
        }
        if (!$content_type || preg_match('~<meta\b(?=[^>]*name=["\']robots["\'])(?=[^>]*content=["\'][^"\']*noindex)[^>]*>~i', $html)) { return false; }
        $canonicals = [];
        preg_match_all('~<link\b[^>]*>~i', $html, $links);
        foreach ($links[0] as $link) {
            if (preg_match('~\srel\s*=\s*(["\'])canonical\1~i', $link) && preg_match('~\shref\s*=\s*(["\'])(.*?)\1~is', $link, $href)) { $canonicals[] = html_entity_decode($href[2], ENT_QUOTES | ENT_HTML5, 'UTF-8'); }
        }
        if ($canonicals !== ['https://' . $server['HTTP_HOST'] . $server['REQUEST_URI']]) { return false; }
        $uri = $server['REQUEST_URI'];
        $fresh = self::policy($dir); if (!$fresh || $fresh['generation'] !== $p['generation'] || ($fresh['paths'][$uri] ?? null) !== ($p['paths'][$uri] ?? null) || self::eligible($fresh, $server, [], $now) !== '') { return false; }
        $now = $now ?? time(); $expiry = $now + $p['ttl']; if ($p['next_publication']) { $expiry = min($expiry, $p['next_publication']); }
        if ($expiry <= $now) { return false; }
        $key = self::key($p, $server); $meta = ['kind' => 'zp-public-html-v1', 'key' => $key, 'generation' => $p['generation'], 'created' => $now, 'expires' => $expiry, 'length' => strlen($html), 'html_sha256' => hash('sha256', $html)];
        $file = $dir . '/zpc-' . $key . '.php'; if (is_link($file)) { return false; }
        $temp = $dir . '/.zpc-' . bin2hex(random_bytes(12)) . '.php'; $data = self::PREFIX . json_encode($meta) . "\n" . $html;
        $handle = @fopen($temp, 'xb'); if (!$handle) { return false; }
        @chmod($temp, 0600); $written = fwrite($handle, $data); fclose($handle);
        if ($written !== strlen($data) || is_link($file) || !@rename($temp, $file)) { @unlink($temp); return false; }
        return true;
    }

    public static function boot(): void {
        if (headers_sent()) { return; }
        $dir = WP_CONTENT_DIR . '/cache/zp-suite-html'; $p = self::policy($dir);
        if (!$p) { if (!headers_sent()) { header('X-ZP-Cache: BYPASS; policy'); } return; }
        $reason = self::eligible($p, $_SERVER, $_COOKIE);
        if ($reason !== '') { if (!headers_sent()) { header('X-ZP-Cache: BYPASS; ' . $reason); } return; }
        $hit = self::hit($dir, $p, $_SERVER);
        if ($hit) {
            header('Content-Type: text/html; charset=UTF-8'); self::headers(); header('X-ZP-Cache: HIT'); header('X-ZP-Cache-Device: ' . self::device($_SERVER)); header('X-ZP-Cache-Age: ' . $hit['age']);
            if (self::is_bot((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''))) { self::note_bot($dir, $_SERVER); }
            echo $hit['html']; exit;
        }
        self::headers(); header('X-ZP-Cache: MISS'); header('X-ZP-Cache-Device: ' . self::device($_SERVER)); $server = $_SERVER; $flushed = false;
        // Start before plugins: this outermost buffer sees Suite's final transformed HTML.
        ob_start(static function ($html, $phase) use ($dir, $p, $server, &$flushed) {
            if (($phase & PHP_OUTPUT_HANDLER_FINAL) === 0) { $flushed = true; return $html; }
            if (!$flushed && (!defined('DONOTCACHEPAGE') || !DONOTCACHEPAGE)) { self::store($dir, $p, $server, $html, http_response_code() ?: 200, headers_list()); }
            return $html;
        });
    }
}
ZP_Cache_Early_20261009::boot();
