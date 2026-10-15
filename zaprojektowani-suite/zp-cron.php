<?php
/*
 * ZP Suite 3.1.1: WP-Cron started by the server's cron. The nazwa.pl panel runs only "php <file>", every 5 minutes:
 *   php /home/…/wp-content/plugins/zaprojektowani-suite/zp-cron.php zaprojektowani.com
 * (Stan strony → Zaplanowane zadania shows the exact line.) The site's jobs need the site's own PHP (its version,
 * extensions and limits), and the panel's command-line PHP can be another one, so this file only requests
 * https://<host>/wp-cron.php and waits until it has finished: a dropped connection can stop the job on LiteSpeed.
 * wp-cron.php without a parameter takes the same lock as any outside call, so nothing runs twice.
 * Old PHP syntax on purpose. Opened in a browser, or without a host, it does nothing.
 */
if (isset($_SERVER['REQUEST_METHOD']) || isset($_SERVER['HTTP_HOST']) || isset($_SERVER['GATEWAY_INTERFACE'])) { exit; }
$zp_args = isset($argv) && is_array($argv) ? $argv : (isset($_SERVER['argv']) && is_array($_SERVER['argv']) ? $_SERVER['argv'] : array());
$zp_host = isset($zp_args[1]) ? strtolower(trim($zp_args[1])) : '';
if (strlen($zp_host) > 253 || !preg_match('/^[a-z0-9](?:[a-z0-9-]*[a-z0-9])?(?:\.[a-z0-9](?:[a-z0-9-]*[a-z0-9])?)+$/', $zp_host)) {
    echo "ZP Suite cron: no site address after the file name (e.g. zp-cron.php zaprojektowani.com).\n";
    exit(2);
}
$zp_url = 'https://' . $zp_host . '/wp-cron.php';
// WordPress's own call to wp-cron.php does not check the certificate either (https_local_ssl_verify); nothing is sent.
$zp_agent = 'WordPress; https://' . $zp_host . '/; ZP Suite cron';
// A host may disable set_time_limit or curl_exec; on PHP 8 calling a disabled function is a fatal error.
if (function_exists('set_time_limit')) { @set_time_limit(0); }
$zp_status = 0;
$zp_error = '';
if (function_exists('curl_init') && function_exists('curl_exec')) {
    $zp_curl = curl_init($zp_url);
    curl_setopt_array($zp_curl, array(CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 900, CURLOPT_CONNECTTIMEOUT => 20, CURLOPT_USERAGENT => $zp_agent, CURLOPT_SSL_VERIFYPEER => false, CURLOPT_SSL_VERIFYHOST => 0, CURLOPT_FOLLOWLOCATION => false));
    if (curl_exec($zp_curl) === false) { $zp_error = curl_error($zp_curl); }
    else { $zp_status = (int) curl_getinfo($zp_curl, CURLINFO_HTTP_CODE); }
    curl_close($zp_curl);
} else {
    $zp_context = stream_context_create(array('http' => array('method' => 'GET', 'timeout' => 900, 'user_agent' => $zp_agent, 'follow_location' => 0, 'ignore_errors' => true), 'ssl' => array('verify_peer' => false, 'verify_peer_name' => false)));
    if (@file_get_contents($zp_url, false, $zp_context) === false) { $zp_error = 'no connection (no curl, and the https stream failed)'; }
    else {
        // PHP 8.4+ has a function for the response headers (the old variable is deprecated in 8.5); older PHP sets the variable here.
        $zp_name = 'http_response_header';
        $zp_headers = function_exists('http_get_last_response_headers') ? http_get_last_response_headers() : (isset($$zp_name) ? $$zp_name : null);
        if (is_array($zp_headers) && isset($zp_headers[0]) && preg_match('#^HTTP/\S+\s+(\d{3})#', $zp_headers[0], $zp_m)) { $zp_status = (int) $zp_m[1]; }
    }
}
// Only a failure prints anything, so a panel that mails cron output stays quiet when all is well.
if ($zp_status < 200 || $zp_status > 399) {
    echo 'ZP Suite cron: ' . $zp_url . ' failed: ' . ($zp_error !== '' ? $zp_error : 'HTTP ' . $zp_status) . "\n";
    exit(1);
}
exit(0);
