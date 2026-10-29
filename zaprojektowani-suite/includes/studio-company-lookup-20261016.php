<?php
/** Optional Polish company data for Studio's branding enquiry. Public registry reads only. */
if (!defined('ABSPATH')) { exit; }

function zp_studio_company_nip($value) {
    if (!is_string($value) || strlen($value) > 32) { return ''; }
    $value = preg_replace('/^PL\s*/i', '', trim($value));
    $value = preg_replace('/[\s-]+/u', '', $value);
    if (!is_string($value) || !preg_match('/^[0-9]{10}$/D', $value) || preg_match('/^([0-9])\1{9}$/D', $value)) { return ''; }
    $weights = [6, 5, 7, 2, 3, 4, 5, 6, 7]; $sum = 0;
    foreach ($weights as $i => $weight) { $sum += ((int)$value[$i]) * $weight; }
    return $sum % 11 < 10 && $sum % 11 === (int)$value[9] ? $value : '';
}

function zp_studio_company_text($value) {
    return is_scalar($value) ? trim((string)preg_replace('/\s+/u', ' ', sanitize_text_field((string)$value))) : '';
}

function zp_studio_company_ceidg_token() {
    // Configure only on the server (e.g. wp-config.php). It is never sent to the browser.
    $token = defined('ZP_STUDIO_CEIDG_TOKEN') ? constant('ZP_STUDIO_CEIDG_TOKEN') : (defined('CEIDG_TOKEN') ? constant('CEIDG_TOKEN') : '');
    return is_string($token) && strlen($token) <= 8192 && !preg_match('/[\r\n]/', $token) ? trim($token) : '';
}

function zp_studio_company_record($source, $nip, $name, $address, $regon, $date) {
    $name = zp_studio_company_text($name);
    if ($name === '') { return null; }
    $address = zp_studio_company_text($address); $regon = zp_studio_company_text($regon);
    return [
        'nip' => $nip, 'name' => $name, 'address' => $address !== '' ? $address : null,
        'regon' => preg_match('/^(?:[0-9]{9}|[0-9]{14})$/D', $regon) ? $regon : null,
        'source' => $source,
        'sourceLabel' => $source === 'ceidg' ? 'CEIDG' : ($source === 'skanfirmy-regon' ? 'REGON przez skanfirmy.pl' : 'Wykaz podatników VAT (MF)'),
        'sourceUrl' => $source === 'ceidg' ? 'https://www.biznes.gov.pl/pl/wyszukiwarka-firm' : ($source === 'skanfirmy-regon' ? 'https://skanfirmy.pl/api-gus' : 'https://wykazpodatnikowvat.mf.gov.pl/'),
        'asOf' => $date, 'retrievedAt' => gmdate('Y-m-d\TH:i:s\Z'),
    ];
}

function zp_studio_company_parse_mf($data, $nip, $date) {
    $subject = is_array($data) ? ($data['result']['subject'] ?? null) : null;
    if (!is_array($subject) || zp_studio_company_nip($subject['nip'] ?? null) !== $nip) { return null; }
    $address = zp_studio_company_text($subject['workingAddress'] ?? '');
    if ($address === '') { $address = zp_studio_company_text($subject['residenceAddress'] ?? ''); }
    // Never expose accounts, PESEL, representatives or the full upstream response.
    return zp_studio_company_record('mf', $nip, $subject['name'] ?? '', $address, $subject['regon'] ?? '', $date);
}

function zp_studio_company_parse_ceidg($data, $nip, $date) {
    $firms = is_array($data) ? ($data['firma'] ?? null) : null;
    if (!is_array($firms)) { return null; }
    foreach ($firms as $firm) {
        if (!is_array($firm)) { continue; }
        $owner = $firm['wlasciciel'] ?? $firm['właściciel'] ?? [];
        if (!is_array($owner) || zp_studio_company_nip($owner['nip'] ?? null) !== $nip) { continue; }
        $a = isset($firm['adresDzialalnosci']) && is_array($firm['adresDzialalnosci']) ? $firm['adresDzialalnosci'] : [];
        $street = zp_studio_company_text($a['ulica'] ?? '');
        $building = zp_studio_company_text($a['budynek'] ?? ''); $unit = zp_studio_company_text($a['lokal'] ?? '');
        $first = trim($street . ' ' . $building . ($unit !== '' ? '/' . $unit : ''));
        $second = trim(zp_studio_company_text($a['kod'] ?? '') . ' ' . zp_studio_company_text($a['miasto'] ?? ''));
        $address = implode(', ', array_filter([$first, $second], static function($part) { return $part !== ''; }));
        return zp_studio_company_record('ceidg', $nip, $firm['nazwa'] ?? '', $address, $owner['regon'] ?? '', $date);
    }
    return null;
}

function zp_studio_company_skanfirmy_restricted($data) {
    $firm = is_array($data['dane'] ?? null) ? $data['dane'] : [];
    // Treat unknown/missing privacy or entity type conservatively. Never reconstruct hidden data.
    if (!array_key_exists('privacy', $data) || $data['privacy'] !== null || ($firm['typ'] ?? '') !== 'P') { return true; }
    $description = zp_studio_company_text(($firm['forma'] ?? '') . ' ' . ($firm['nazwa'] ?? ''));
    return (bool)preg_match('/sp[oó]łka\s+cywilna|sp[oó]łki\s+cywilnej|\bs\s*\.\s*c(?:\.|\b)/iu', $description);
}

function zp_studio_company_parse_skanfirmy($data, $nip, $date) {
    $firm = is_array($data) && is_array($data['dane'] ?? null) ? $data['dane'] : null;
    if (!$firm || ($data['restricted'] ?? false) === true || ($data['source'] ?? '') !== 'regon-gus'
        || zp_studio_company_nip($data['nip'] ?? null) !== $nip
        || zp_studio_company_nip($firm['nip'] ?? null) !== $nip) { return null; }
    $restricted = zp_studio_company_skanfirmy_restricted($data);
    $city = zp_studio_company_text($firm['miejscowosc'] ?? '');
    $address = ''; $regon = '';
    if (!$restricted) {
        $unit = zp_studio_company_text($firm['nrLokalu'] ?? '');
        $street = trim(zp_studio_company_text($firm['ulica'] ?? '') . ' ' . zp_studio_company_text($firm['nrNieruchomosci'] ?? ''));
        if ($street !== '' && $unit !== '') { $street .= '/' . $unit; }
        // A town alone is not a complete company address.
        if ($street !== '') {
            $postal = trim(zp_studio_company_text($firm['kodPocztowy'] ?? '') . ' ' . $city);
            $address = $street . ($postal !== '' ? ', ' . $postal : '');
        }
        $regon = $firm['regon'] ?? $data['regon'] ?? '';
    }
    $checked = $data['checkedAt'] ?? '';
    if (is_string($checked) && preg_match('/^(\d{4})-(\d{2})-(\d{2})$/D', $checked, $parts)
        && checkdate((int)$parts[2], (int)$parts[3], (int)$parts[1])) { $date = $checked; }
    $record = zp_studio_company_record('skanfirmy-regon', $nip, $firm['nazwa'] ?? '', $address, $regon, $date);
    if (!$record) { return null; }
    $record['city'] = $city !== '' ? $city : null;
    $record['partial'] = $restricted;
    return $record;
}

function zp_studio_company_skanfirmy_cache_ttl($response) {
    $control = wp_remote_retrieve_header($response, 'cache-control');
    $control = is_array($control) ? implode(', ', array_filter($control, 'is_string')) : (is_string($control) ? $control : '');
    if (preg_match('/\b(?:private|no-store|no-cache)\b/i', $control)) { return 0; }
    $ttl = 6 * HOUR_IN_SECONDS;
    if (preg_match_all('/(?:^|,)\s*(?:s-maxage|max-age)\s*=\s*"?([0-9]+)/i', $control, $matches)) {
        foreach ($matches[1] as $age) { $ttl = min($ttl, (int)$age); }
    }
    return $ttl;
}

function zp_studio_company_error($code, $status = 503, $retry = 0) {
    return new WP_Error($code, 'Company lookup is optional; company data can be entered manually.', ['status' => $status, 'retryAfter' => $retry]);
}

function zp_studio_company_lock($name, $ttl = 20) {
    $key = 'zp_studio_company_lock_' . $name;
    if (add_option($key, time() + $ttl, '', false)) { return $key; }
    if ((int)get_option($key, 0) < time()) {
        delete_option($key);
        if (add_option($key, time() + $ttl, '', false)) { return $key; }
    }
    return false;
}

function zp_studio_company_rate_limit() {
    // Trust REMOTE_ADDR only. Persist a keyed digest for one minute, never a raw IP or request log.
    $ip = isset($_SERVER['REMOTE_ADDR']) && is_string($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'unknown';
    $digest = substr(hash_hmac('sha256', $ip, wp_salt('nonce')), 0, 32);
    $lock = zp_studio_company_lock('rate_' . $digest, 5);
    if (!$lock) { return false; }
    try {
        $key = 'zp_studio_company_rate_' . $digest;
        $count = (int)get_transient($key);
        if ($count >= 8) { return false; }
        set_transient($key, $count + 1, MINUTE_IN_SECONDS);
        return true;
    } finally { delete_option($lock); }
}

function zp_studio_company_provider_allow($source, $date) {
    if (get_transient('zp_studio_company_pause_' . $source)) { return false; }
    $lock = zp_studio_company_lock('quota_' . $source, 5);
    if (!$lock) { return false; }
    try {
        $key = 'zp_studio_company_quota_' . $source;
        $quota = get_option($key, []);
        $count = is_array($quota) && ($quota['date'] ?? '') === $date ? (int)($quota['count'] ?? 0) : 0;
        // MF's documented search limit is 100/day per IP. Keep a margin for other uses.
        // The same conservative local ceiling also bounds optional CEIDG traffic.
        if ($count >= 80) { return false; }
        if ($source === 'ceidg' && is_array($quota) && time() - (int)($quota['last'] ?? 0) < 4) { return false; }
        update_option($key, ['date' => $date, 'count' => $count + 1, 'last' => time()], false);
        return true;
    } finally { delete_option($lock); }
}

function zp_studio_company_request($source, $nip, $date, $token = '') {
    if (!in_array($source, ['ceidg', 'mf', 'skanfirmy'], true)) { return zp_studio_company_error('unavailable'); }
    if (!zp_studio_company_provider_allow($source, $date)) { return zp_studio_company_error('limited', 429, 60); }
    $url = $source === 'ceidg'
        ? 'https://dane.biznes.gov.pl/api/ceidg/v3/firma?nip=' . rawurlencode($nip)
        : ($source === 'skanfirmy' ? 'https://skanfirmy.pl/regon/' . rawurlencode($nip) . '?format=json'
        : 'https://wykazpodatnikowvat.mf.gov.pl/api/search/nip/' . rawurlencode($nip) . '?date=' . rawurlencode($date));
    $headers = ['Accept' => 'application/json'];
    if ($source === 'ceidg') { $headers['Authorization'] = 'Bearer ' . $token; }
    $response = wp_safe_remote_get($url, [
        'timeout' => 4, 'redirection' => 0, 'limit_response_size' => 131072,
        'headers' => $headers, 'user-agent' => 'Zaprojektowani-Studio-Company/20261016; ' . home_url('/'),
    ]);
    if (is_wp_error($response)) { return zp_studio_company_error('unavailable'); }
    $code = (int)wp_remote_retrieve_response_code($response);
    if ($code === 204 || $code === 404) {
        $result = ['status' => 'not_found', 'source' => $source, 'asOf' => $date];
        if ($source === 'skanfirmy') { $result['_cacheTtl'] = min(10 * MINUTE_IN_SECONDS, zp_studio_company_skanfirmy_cache_ttl($response)); }
        return $result;
    }
    if ($code !== 200) {
        if ($code === 429 || ($source === 'ceidg' && in_array($code, [401, 403], true))) {
            set_transient('zp_studio_company_pause_' . $source, true, $code === 429 ? 3 * MINUTE_IN_SECONDS : HOUR_IN_SECONDS);
        }
        return zp_studio_company_error($code === 429 ? 'limited' : ($source === 'skanfirmy' && $code === 403 ? 'restricted' : 'unavailable'), $code === 429 ? 429 : 503, $code === 429 ? 180 : 0);
    }
    $data = json_decode(wp_remote_retrieve_body($response), true);
    if (!is_array($data) || json_last_error() !== JSON_ERROR_NONE) { return zp_studio_company_error('unavailable'); }
    if ($source === 'skanfirmy' && ($data['restricted'] ?? false) === true) { return zp_studio_company_error('restricted'); }
    if ($source === 'mf' && (!isset($data['result']) || !is_array($data['result']) || !array_key_exists('subject', $data['result']))) { return zp_studio_company_error('unavailable'); }
    if ($source === 'ceidg' && !array_key_exists('firma', $data)) { return zp_studio_company_error('unavailable'); }
    if ($source === 'skanfirmy' && !is_array($data['dane'] ?? null)) { return zp_studio_company_error('unavailable'); }
    $record = $source === 'ceidg' ? zp_studio_company_parse_ceidg($data, $nip, $date)
        : ($source === 'skanfirmy' ? zp_studio_company_parse_skanfirmy($data, $nip, $date) : zp_studio_company_parse_mf($data, $nip, $date));
    $result = $record ? ['status' => 'found', 'record' => $record] : ['status' => 'not_found', 'source' => $source, 'asOf' => $date];
    if ($source === 'skanfirmy') {
        $result['_cacheTtl'] = !empty($record['partial']) ? 0 : zp_studio_company_skanfirmy_cache_ttl($response);
        if (!$record) { $result['_cacheTtl'] = min(10 * MINUTE_IN_SECONDS, $result['_cacheTtl']); }
    }
    return $result;
}

function zp_studio_company_lookup($raw_nip) {
    $nip = zp_studio_company_nip($raw_nip);
    if ($nip === '') { return zp_studio_company_error('invalid_nip', 422); }
    if (!zp_studio_company_rate_limit()) { return zp_studio_company_error('limited', 429, 60); }
    $date = wp_date('Y-m-d', null, new DateTimeZone('Europe/Warsaw'));
    $token = zp_studio_company_ceidg_token();
    // Revision avoids reusing an old MF-only negative result after introducing REGON fallback.
    $cache_key = 'zp_studio_company_' . substr(hash_hmac('sha256', 'v2|' . $nip . '|' . $date . '|' . ($token !== '' ? 'ceidg-mf-regon' : 'mf-regon'), wp_salt('nonce')), 0, 40);
    $cached = get_transient($cache_key);
    if (is_array($cached) || is_wp_error($cached)) { return $cached; }
    $lock = zp_studio_company_lock(substr(hash('sha256', $nip), 0, 24));
    if (!$lock) { return zp_studio_company_error('busy', 429, 2); }
    try {
        $cached = get_transient($cache_key);
        if (is_array($cached) || is_wp_error($cached)) { return $cached; }
        $result = $token !== '' ? zp_studio_company_request('ceidg', $nip, $date, $token) : null;
        if (!$result || is_wp_error($result) || ($result['status'] ?? '') !== 'found') { $result = zp_studio_company_request('mf', $nip, $date); }
        if (is_wp_error($result) || ($result['status'] ?? '') !== 'found') { $result = zp_studio_company_request('skanfirmy', $nip, $date); }
        if (is_wp_error($result)) {
            if ($result->get_error_code() !== 'restricted') { set_transient($cache_key, $result, MINUTE_IN_SECONDS); }
            return $result;
        }
        // Provider's private JDG/civil replies allow browser-only caching: never persist them in a shared transient.
        $ttl = isset($result['_cacheTtl']) ? (int)$result['_cacheTtl'] : (($result['status'] ?? '') === 'found' ? 6 * HOUR_IN_SECONDS : 10 * MINUTE_IN_SECONDS);
        unset($result['_cacheTtl']);
        if ($ttl > 0) { set_transient($cache_key, $result, $ttl); }
        return $result;
    } finally { delete_option($lock); }
}

function zp_studio_company_ajax() {
    nocache_headers();
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') { wp_send_json_error(['code' => 'method'], 405); }
    $nonce = isset($_POST['nonce']) && is_string($_POST['nonce']) ? wp_unslash($_POST['nonce']) : '';
    if (!wp_verify_nonce($nonce, 'zp_studio_company_lookup')) { wp_send_json_error(['code' => 'expired'], 403); }
    $result = zp_studio_company_lookup(isset($_POST['nip']) && is_string($_POST['nip']) ? wp_unslash($_POST['nip']) : null);
    if (is_wp_error($result)) {
        $data = $result->get_error_data();
        wp_send_json_error(['code' => $result->get_error_code(), 'retryAfter' => (int)($data['retryAfter'] ?? 0)], (int)($data['status'] ?? 503));
    }
    wp_send_json_success($result);
}
add_action('wp_ajax_zp_studio_company_lookup', 'zp_studio_company_ajax');
add_action('wp_ajax_nopriv_zp_studio_company_lookup', 'zp_studio_company_ajax');

/** Call before the Studio upload/save/mail pipeline. An empty NIP remains optional. */
function zp_studio_company_payload_errors($payload, $language = 'pl') {
    if ($language === 'en') { return []; }
    if (!is_array($payload)) { return []; }
    $value = $payload['NIP'] ?? $payload['nip'] ?? '';
    if ($value === '' || (is_string($value) && trim($value) === '')) { return []; }
    if (zp_studio_company_nip($value) !== '') { return []; }
    return [$language === 'en'
        ? 'Check the NIP: enter a valid 10-digit Polish Tax ID, or leave the optional field empty.'
        : 'Sprawdź NIP: wpisz prawidłowy polski NIP z 10 cyframi albo pozostaw opcjonalne pole puste.'];
}

function zp_studio_company_payload($payload, $language = 'pl') {
    if (!is_array($payload)) { return $payload; }
    if ($language === 'en') {
        foreach (['NIP', 'nip', 'Adres firmy', 'companyAddress', 'REGON', 'regon', 'Źródło danych firmy', 'companySource', 'Company address', 'Company data source', 'Polish Tax ID / NIP'] as $key) { unset($payload[$key]); }
        return $payload;
    }
    if (isset($payload['NIP']) || isset($payload['nip'])) {
        $number = zp_studio_company_nip($payload['NIP'] ?? $payload['nip']);
        unset($payload['nip']);
        if ($number !== '') { $payload['NIP'] = $number; }
        else { unset($payload['NIP']); }
    }
    return $payload;
}

function zp_studio_company_enqueue() {
    if (is_admin()) { return; }
    $language = function_exists('zp_studio_legal_language') ? zp_studio_legal_language()
        : (function_exists('zpl_language') ? zpl_language()
        : (preg_match('~^/en(?:/|$)~', (string)wp_parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH)) ? 'en' : 'pl'));
    if ($language === 'en') { return; }
    $path = (string)wp_parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    $on = rtrim($path, '/') . '/' === '/studio-wyceny/' || is_page('studio-wyceny');
    if (!$on && is_singular()) {
        $content = (string)get_post_field('post_content', get_queried_object_id());
        $on = has_shortcode($content, 'zp_studio_wyceny') || has_shortcode($content, 'zp_studio_wyceny_cms');
    }
    if (!$on) { return; }
    $version = defined('ZP_SUITE_VERSION') ? ZP_SUITE_VERSION : '20261016';
    wp_enqueue_style('zp-studio-company-lookup', ZP_SUITE_URL . 'assets/css/studio-company-lookup-20261016.css', [], $version);
    wp_enqueue_script('zp-studio-company-lookup', ZP_SUITE_URL . 'assets/js/studio-company-lookup-20261016.js', [], $version, true);
    wp_localize_script('zp-studio-company-lookup', 'zpStudioCompanyLookup', [
        'ajax' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce('zp_studio_company_lookup'),
        'provider' => zp_studio_company_ceidg_token() !== '' ? 'ceidg-mf-regon' : 'mf-regon',
    ]);
}
add_action('wp_enqueue_scripts', 'zp_studio_company_enqueue', 100);
