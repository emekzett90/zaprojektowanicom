<?php
/** Observe one bounded Images HTTP429 result; never alter or repeat a provider request. */
if (!defined('ABSPATH')) { exit; }

final class ZP_Preparation_Image_HTTP_Diagnostics_20261011 {
    public const OPTION = 'zps_preparation_image_http_diag_20261011';
    public const SUCCESS = 'zps_preparation_image_http_success_20261011';
    private const URL = 'https://api.openai.com/v1/images/generations';
    private const MODELS = ['gpt-image-2.5-sunburst','gpt-image-2.5-flare'];
    private const CODES = ['rate_limit_exceeded','slow_down','credit_balance_exhausted','organization_spend_limit_exceeded','project_spend_limit_exceeded','organization_usage_limit_exceeded','insufficient_quota'];
    private const TYPES = ['rate_limit_error','insufficient_quota'];

    private static function request_matches($args,$url): bool {
        if ($url !== self::URL || !is_array($args) || ($args['method']??null) !== 'POST'
            || ($args['sslverify']??null) !== true || ($args['redirection']??null) !== 0
            || !is_string($args['body']??null) || strlen($args['body']) > 65536) return false;
        $body = json_decode($args['body'],true,16);
        if (!is_array($body) || json_last_error() !== JSON_ERROR_NONE) return false;
        $keys = array_keys($body); sort($keys);
        if ($keys !== ['background','model','n','output_format','prompt','quality','size']) return false;
        return in_array($body['model'],self::MODELS,true) && $body['n'] === 1 && $body['size'] === '1536x1024'
            && in_array($body['quality'],['medium','high'],true) && $body['background'] === 'opaque'
            && $body['output_format'] === 'webp' && is_string($body['prompt']) && $body['prompt'] !== '' && strlen($body['prompt']) <= 30000;
    }
    private static function enum($value,array $allowed): string { return is_string($value) && in_array($value,$allowed,true) ? $value : 'unknown'; }
    private static function retry_after($value): ?int {
        if (is_int($value)) return $value >= 0 && $value <= 3600 ? $value : null;
        if (!is_string($value) || strlen($value) > 8) return null;
        $value = trim($value);
        return preg_match('/^[0-9]{1,4}$/D',$value) === 1 && (int)$value <= 3600 ? (int)$value : null;
    }
    private static function valid_image(array $data): bool {
        if (!is_array($data['data']??null)||count($data['data'])!==1||!is_string($data['data'][0]['b64_json']??null)||strlen($data['data'][0]['b64_json'])>14000000) return false;
        $bytes=base64_decode($data['data'][0]['b64_json'],true);if (!is_string($bytes)||strlen($bytes)<100||strlen($bytes)>10000000) return false;
        $size=@getimagesizefromstring($bytes);return is_array($size)&&($size['mime']??'')==='image/webp'&&$size[0]===1536&&$size[1]===1024;
    }
    /** Return the original response in every case; storage failure cannot affect the native pipeline. */
    public static function observe($response,$args,$url) {
        try {
            if (!class_exists('ZP_Content_Preparation_20261010') || !current_user_can('manage_options') || !current_user_can('edit_posts')
                || is_wp_error($response) || !is_array($response) || !self::request_matches($args,$url)
                ) return $response;
            $status=wp_remote_retrieve_response_code($response);
            if ($status===200) {
                $raw=wp_remote_retrieve_body($response);if (!is_string($raw)||strlen($raw)>16000000) return $response;
                $data=json_decode($raw,true,16);if (!is_array($data)||json_last_error()!==JSON_ERROR_NONE||!empty($data['error'])) return $response;
                if (!self::valid_image($data)) return $response;
                update_option(self::SUCCESS,['at'=>time(),'http_code'=>200,'valid_image'=>true],false);return $response;
            }
            if ($status!==429) return $response;
            $raw = wp_remote_retrieve_body($response); $error = null;
            if (is_string($raw) && strlen($raw) <= 32768) {
                $data = json_decode($raw,true,16);
                if (json_last_error() === JSON_ERROR_NONE && is_array($data) && is_array($data['error']??null)) $error = $data['error'];
            }
            $observation = ['at'=>time(),'http_code'=>429,'code'=>self::enum($error['code']??null,self::CODES),
                'type'=>self::enum($error['type']??null,self::TYPES),'retry_after_seconds'=>self::retry_after(wp_remote_retrieve_header($response,'retry-after'))];
            update_option(self::OPTION,$observation,false);
        } catch (Throwable $ignored) { /* Diagnostics must never change the original request result. */ }
        return $response;
    }
    private static function category(array $row): string {
        $code = $row['code']; $type = $row['type'];
        if (in_array($code,['rate_limit_exceeded','slow_down'],true)) return $type === 'insufficient_quota' ? 'unknown' : 'rate';
        if (in_array($code,['credit_balance_exhausted','organization_spend_limit_exceeded','project_spend_limit_exceeded','organization_usage_limit_exceeded','insufficient_quota'],true)) return $type === 'rate_limit_error' ? 'unknown' : 'quota';
        return $type === 'insufficient_quota' ? 'quota' : ($type === 'rate_limit_error' ? 'rate' : 'unknown');
    }
    public static function render(): void {
        if (!is_admin() || !class_exists('ZP_Content_Preparation_20261010') || !current_user_can('manage_options')
            || ($_SERVER['REQUEST_METHOD']??'') !== 'GET' || !is_string($_GET['page']??null) || $_GET['page'] !== ZP_Content_Preparation_20261010::PAGE
            || (function_exists('wp_doing_ajax') && wp_doing_ajax()) || (function_exists('wp_doing_cron') && wp_doing_cron()) || (defined('REST_REQUEST') && REST_REQUEST)) return;
        $row = get_option(self::OPTION,null);
        if (!is_array($row) || !is_int($row['at']??null) || $row['at'] <= 0 || $row['at'] > time()+30 || $row['at'] < time()-86400
            || ($row['http_code']??null) !== 429 || !is_string($row['code']??null) || !in_array($row['code'],array_merge(self::CODES,['unknown']),true)
            || !is_string($row['type']??null) || !in_array($row['type'],array_merge(self::TYPES,['unknown']),true)
            || !array_key_exists('retry_after_seconds',$row) || ($row['retry_after_seconds'] !== null && (!is_int($row['retry_after_seconds']) || $row['retry_after_seconds'] < 0 || $row['retry_after_seconds'] > 3600))) return;
        $success=get_option(self::SUCCESS,null);$resolved=is_array($success) && is_int($success['at']??null) && $success['at']>$row['at'] && $success['at']<=time()+30 && $success['at']>=time()-86400 && ($success['http_code']??null)===200 && ($success['valid_image']??null)===true;
        $category = self::category($row);
        if ($resolved) echo '<details class="notice notice-info inline"><summary>Historia limitu generowania zdjęć — później otrzymano poprawny obraz</summary><p>Po tym błędzie inne obserwowane żądanie otrzymało poprawną odpowiedź HTTP200 z obrazem WebP 1536×1024. To nie potwierdza zakończenia wszystkich zadań ani usunięcia każdego limitu.</p>';
        $labels = ['rate'=>'Dostawca wskazał limit tempa żądań. Odczekaj przed kolejnym ręcznym ponowieniem.',
            'quota'=>'Dostawca wskazał limit kredytów, wydatków lub przydziału. Sprawdź prywatne ustawienia konta OpenAI; samo ponowienie nie usuwa tego limitu.',
            'unknown'=>'Nie potwierdzono przyczyny limitu. Sam status HTTP429 nie rozróżnia tempa żądań i budżetu.'];
        echo '<div class="notice notice-warning'.($resolved?' inline':'').'"><p><strong>Ostatni zaobserwowany błąd 429 przy generowaniu zdjęcia</strong><br>'.esc_html($labels[$category]).'</p><details><summary>Bezpieczne szczegóły odpowiedzi</summary><p>Odczyt: '.esc_html(wp_date('d.m.Y H:i:s',$row['at'],wp_timezone())).'. Kod: '.esc_html($row['code']).'. Typ: '.esc_html($row['type']).'.</p>';
        if ($row['retry_after_seconds'] !== null) echo '<p>Dostawca podał minimalny czas oczekiwania: '.(int)$row['retry_after_seconds'].' s. Moduł nie planuje ani nie wykonuje ponowienia.</p>';
        echo '<p>To wynik jednego ostatniego żądania po włączeniu diagnostyki. Nie odtwarza wcześniejszych błędów i nie potwierdza przyczyny błędów w innych etapach.</p></details></div>';
        if ($resolved) echo '</details>';
    }
    public static function register(): void {
        add_filter('http_response',[self::class,'observe'],10,3);
        add_action('admin_notices',[self::class,'render'],54);
    }
}
