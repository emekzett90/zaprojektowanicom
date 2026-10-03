<?php
if (!defined('ABSPATH')) { exit; }
final class ZPSSEO_Repair {
    const OPTION = 'zp_suite_seo_repair_v1';
    public static function data() { static $d; if (!$d) { $d = json_decode(file_get_contents(__DIR__ . '/audit.json'), true); } return $d; }
    public static function state() { return array_merge(['enabled'=>true,'maps'=>[], 'empty_css'=>[], 'results'=>[], 'custom'=>[], 'dead_links'=>[]], (array)get_option(self::OPTION, [])); }
    public static function save($s) { update_option(self::OPTION, $s, false); }
    public static function allowed() { return in_array(strtolower((string)wp_parse_url(home_url(), PHP_URL_HOST)), ['zaprojektowani.com','www.zaprojektowani.com'], true) && !is_multisite(); }
    public static function init() {
        add_action('admin_menu', [__CLASS__,'menu']);
        add_action('admin_notices', [__CLASS__,'notice']);
        add_action('admin_enqueue_scripts', [__CLASS__,'assets']);
        add_action('wp_ajax_zpsseo_job', [__CLASS__,'ajax']);
        add_action('wp_ajax_zpsseo_status', [__CLASS__,'status']);
        add_action('admin_post_zpsseo_save', [__CLASS__,'settings']);
        add_action('template_redirect', [__CLASS__,'front'], -PHP_INT_MAX);
        add_filter('script_loader_src', [__CLASS__,'filter_url'], 100);
        add_filter('style_loader_src', [__CLASS__,'filter_url'], 100);
    }
    public static function path($url) {
        $p = wp_parse_url($url);
        if (!is_array($p) || (isset($p['scheme']) && !in_array(strtolower($p['scheme']), ['http','https'], true))) { return ''; }
        if (isset($p['host']) && !in_array(strtolower($p['host']), ['zaprojektowani.com','www.zaprojektowani.com'], true)) { return ''; }
        if (isset($p['user']) || isset($p['pass']) || isset($p['port'])) { return ''; }
        $path = $p['path'] ?? '';
        if (!$path || $path[0] !== '/' || strpos($path, '//') !== false || preg_match('~(?:^|/)\.\.?(/|$)|[\\\\\x00-\x20]~', rawurldecode($path))) { return ''; }
        return $path;
    }
    public static function local($path) {
        if (self::path($path) !== $path) { return ''; }
        $u = wp_upload_dir(null, false);
        $roots = [$u['baseurl']=>$u['basedir'], content_url()=>WP_CONTENT_DIR];
        foreach ($roots as $url=>$dir) {
            $prefix = rtrim((string)wp_parse_url($url, PHP_URL_PATH), '/') . '/';
            if (strpos($path, $prefix) !== 0) { continue; }
            $root = realpath($dir); $file = realpath($dir . '/' . rawurldecode(substr($path, strlen($prefix))));
            if ($root && $file && strpos($file, $root . DIRECTORY_SEPARATOR) === 0 && is_file($file) && is_readable($file)) { return $file; }
        }
        return '';
    }
    public static function public_page($path) {
        $id = url_to_postid(home_url($path));
        if ($id && get_post_status($id) === 'publish' && !post_password_required($id) && get_post_field('post_password', $id) === '' && is_post_type_viewable(get_post_type($id))) {
            $url = get_permalink($id);
            return self::path($url) === $path ? $url : '';
        }
        if (preg_match('~^/category/([^/]+)/$~', $path, $m)) {
            $term = get_term_by('slug', $m[1], 'category');
            if ($term && $term->count > 0) { $url = get_term_link($term); return !is_wp_error($url) && self::path($url) === $path ? $url : ''; }
        }
        return '';
    }
    public static function map_url($url, $s = null) {
        if (!is_string($url)) { return $url; }
        $s = $s ?? self::state(); $p = self::path($url);
        if (!$p || empty($s['maps'][$p])) { return $url; }
        $m = $s['maps'][$p]; $target = $m['target'];
        if ($m['kind'] === 'page') {
            // Query-dependent views and functional requests must keep their original behavior.
            if (wp_parse_url($url, PHP_URL_QUERY) !== null) { return $url; }
            if (self::public_page($p) || !self::public_page($target)) { return $url; }
        } elseif (!self::local($target) || ($m['kind'] !== 'optimized' && self::local($p))) { return $url; }
        $out = home_url($target);
        $query = wp_parse_url($url, PHP_URL_QUERY); $hash = wp_parse_url($url, PHP_URL_FRAGMENT);
        if (is_string($query) && $query !== '') { $out .= '?' . $query; }
        if (is_string($hash) && $hash !== '') { $out .= '#' . $hash; }
        return $out;
    }
    public static function filter_url($url) {
        if (!self::allowed() || is_admin() || !self::state()['enabled']) { return $url; }
        return self::map_url($url);
    }
    public static function front() {
        if (!self::allowed() || !self::state()['enabled'] || is_admin() || wp_doing_ajax() || is_feed() || is_trackback() || is_preview() || (defined('REST_REQUEST') && REST_REQUEST) || isset($_GET['elementor-preview']) || !in_array($_SERVER['REQUEST_METHOD'] ?? '', ['GET','HEAD'], true)) { return; }
        $uri = isset($_SERVER['REQUEST_URI']) ? wp_unslash($_SERVER['REQUEST_URI']) : '';
        $p = self::path($uri); $s = self::state();
        if (is_404() && empty($_GET) && isset($s['maps'][$p]) && $s['maps'][$p]['kind'] === 'page') {
            $to = self::map_url($p, $s);
            if ($to !== $p && !headers_sent()) { wp_safe_redirect($to, 301, 'Zaprojektowani Ahrefs Repair'); exit; }
        }
        // Only the public HTML template is buffered, no database content is rewritten.
        ob_start([__CLASS__,'html']);
    }
    public static function html($html) {
        if (!is_string($html) || strlen($html) > 12 * 1024 * 1024 || stripos($html, '<html') === false || stripos($html, '</html>') === false) { return $html; }
        foreach (headers_list() as $header) {
            if (stripos($header, 'Content-Type:') === 0 && stripos($header, 'text/html') === false) { return $html; }
            if (stripos($header, 'Content-Encoding:') === 0) { return $html; }
        }
        $s = self::state();
        if (class_exists('ZPSSEO_Editorial')) { $html=ZPSSEO_Editorial::output($html); }
        if (!$s['enabled'] || !class_exists('WP_HTML_Tag_Processor')) { return $html; }
        // Tokenize comments and raw-text elements atomically: never edit strings inside JS/CSS.
        $pattern = '~<!--[\s\S]*?-->|<(script|style|textarea|title)\b(?:"[^"]*"|\x27[^\x27]*\x27|[^\x27">])*?>[\s\S]*?</\1\s*>|<(?:"[^"]*"|\x27[^\x27]*\x27|[^\x27">])+>~i';
        $head = false; $description = false;
        $out = preg_replace_callback($pattern, function($m) use (&$head, &$description, $s) {
            $tag = $m[0];
            if (stripos($tag, '<!--') === 0) { return $tag; }
            if (preg_match('~^<head\b~i', $tag)) { $head = true; }
            if (preg_match('~^</head\b~i', $tag)) { $head = false; }
            $proc = new WP_HTML_Tag_Processor($tag);
            if (!$proc->next_tag()) { return $tag; }
            $name = $proc->get_tag();
            if ($head && $name === 'META' && strtolower(trim((string)$proc->get_attribute('name'))) === 'description') {
                $content = trim((string)$proc->get_attribute('content'));
                if ($content === '' || $description) { return ''; }
                $description = true;
            }
            if ($name === 'LINK' && strtolower((string)$proc->get_attribute('rel')) === 'stylesheet') {
                $p = self::path((string)$proc->get_attribute('href'));
                if (isset($s['empty_css'][$p]) && !self::local($p)) {
                    $entry = $s['empty_css'][$p];
                    // Expire the decision if Elementor data changes.
                    if (self::css_signature($entry['id']) === $entry['signature']) { return ''; }
                }
            }
            if ($name === 'A' && $proc->get_attribute('onclick') === null) {
                $href = $proc->get_attribute('href'); $dead = is_string($href) ? self::path($href) : '';
                if ($dead && isset($s['dead_links'][$dead]) && $s['dead_links'][$dead] > time()-8*DAY_IN_SECONDS && empty($s['maps'][$dead]) && wp_parse_url($href, PHP_URL_QUERY) === null && !self::public_page($dead)) {
                    $proc->remove_attribute('href');
                    foreach (['target','rel','download','ping'] as $a) { $proc->remove_attribute($a); }
                    $proc->set_attribute('aria-disabled', 'true');
                }
            }
            $attrs = ['A'=>['href'], 'IMG'=>['src','data-src','data-lazy-src'], 'SOURCE'=>['src'], 'SCRIPT'=>['src'], 'LINK'=>['href']];
            foreach ($attrs[$name] ?? [] as $attr) {
                $value = $proc->get_attribute($attr);
                if (is_string($value)) { $new = self::map_url($value, $s); if ($new !== $value) { $proc->set_attribute($attr, $new); } }
            }
            if ($name === 'IMG' || $name === 'SOURCE') {
                foreach (['srcset','data-srcset','data-lazy-srcset'] as $attr) {
                    $value = $proc->get_attribute($attr);
                    if (!is_string($value) || stripos($value, 'data:') !== false) { continue; }
                    $parts = explode(',', $value); $changed = false;
                    foreach ($parts as &$part) {
                        if (preg_match('~^(\s*)(\S+)(.*)$~s', $part, $a)) {
                            $new = self::map_url($a[2], $s);
                            if ($new !== $a[2]) { $part = $a[1] . $new . $a[3]; $changed = true; }
                        }
                    } unset($part);
                    if ($changed) { $proc->set_attribute($attr, implode(',', $parts)); }
                }
            }
            return $proc->get_updated_html();
        }, $html);
        if (is_string($out) && $out !== $html && !headers_sent()) { header_remove('Content-Length'); }
        return is_string($out) ? $out : $html;
    }
    public static function css_signature($id) { return md5(serialize([get_post_meta($id, '_elementor_data', true), get_post_meta($id, '_elementor_page_settings', true), get_post_modified_time('U', true, $id)])); }
    public static function jobs() {
        $d = self::data(); $jobs = [];
        foreach ($d['routes'] as $r) { $jobs[] = ['type'=>'route','source'=>$r['source'],'target'=>$r['target']]; }
        foreach ($d['resources'] as $r) { $jobs[] = ['type'=>'resource','source'=>$r['source'],'target'=>$r['target']]; }
        foreach ($d['css'] as $p) { $jobs[] = ['type'=>'css','source'=>$p]; }
        foreach ($d['images'] as $p) { $jobs[] = ['type'=>'image','source'=>$p]; }
        if (class_exists('ZPSSEO_Editorial')) {
            // Suite includes the original shortcode provider; no fallback dependency job.
            foreach (ZPSSEO_Editorial::editorial() as $r) { $jobs[]=['type'=>'editorial','source'=>$r['path']]; }
            $jobs[]=['type'=>'purge','source'=>'@ultimate/cache'];
            foreach (['/','/strony-internetowe-katowice/','/sklepy-internetowe-katowice/','/logo-branding-katowice/','/kontakt/'] as $p) { $jobs[]=['type'=>'verify','source'=>'@verify'.$p,'path'=>$p]; }
        }
        return $jobs;
    }
    public static function discover_page($path) {
        $slug = basename(rtrim($path, '/'));
        if (!$slug || ctype_digit($slug)) { return ''; }
        $posts = get_posts(['name'=>$slug,'post_type'=>get_post_types(['public'=>true]),'post_status'=>'publish','posts_per_page'=>3,'suppress_filters'=>false]);
        $matches=[];
        foreach ($posts as $post) { $p=self::path(get_permalink($post->ID)); if ($p !== $path && self::public_page($p)) { $matches[]=$p; } }
        return count($matches)===1 ? $matches[0] : '';
    }
    public static function discover_resource($path) {
        global $wpdb;
        $name=basename($path);
        if (!preg_match('/\.(webp|png|jpe?g)$/i',$name)) { return ''; }
        // Exact basename, optionally the same original without WordPress -scaled suffix.
        $names=array_unique([$name,preg_replace('/-scaled(?=\.[^.]+$)/','',$name)]); $matches=[];
        foreach ($names as $n) {
            $rows=$wpdb->get_col($wpdb->prepare("SELECT meta_value FROM {$wpdb->postmeta} WHERE meta_key = '_wp_attached_file' AND meta_value LIKE %s LIMIT 10", '%/' . $wpdb->esc_like($n)));
            foreach ($rows as $relative) {
                if (basename($relative)!==$n) { continue; }
                $u=wp_upload_dir(null,false); $p=self::path($u['baseurl'].'/'.ltrim($relative,'/'));
                if ($p!==$path && self::local($p)) { $matches[$p]=true; }
            }
        }
        return count($matches)===1 ? key($matches) : '';
    }
    public static function check_http($path) {
        $r = wp_safe_remote_head(home_url($path), ['timeout'=>8,'redirection'=>0]);
        if (is_wp_error($r)) { return 'Nie udało się sprawdzić HTTP: ' . $r->get_error_message(); }
        $code = wp_remote_retrieve_response_code($r);
        return $code === 200 ? 'HTTP 200' : 'HTTP ' . $code . ' — sprawdź cache/CDN, prawa plików lub reguły serwera';
    }
    public static function process($j, &$s) {
        if ($j['type']==='editorial') { return ZPSSEO_Editorial::metadata_job($j['source']); }
        if ($j['type']==='purge') { ZPSSEO_Automatic::purge(); return 'Wywołano obsługiwane integracje czyszczenia cache przed kontrolą HTTP.'; }
        if ($j['type']==='verify') { return ZPSSEO_Editorial::verify($j['path']); }
        $p = $j['source']; $custom = $s['custom'][$p] ?? ''; $target = $custom ?: ($j['target'] ?? '');
        if ($j['type'] === 'route') {
            if (self::public_page($p)) { unset($s['maps'][$p], $s['dead_links'][$p]); return 'OK: strona źródłowa już istnieje.'; }
            if (!$target) { $target = self::discover_page($p); }
            if (!$target || !self::public_page($target)) {
                unset($s['maps'][$p]);
                $r = wp_safe_remote_head(home_url($p), ['timeout'=>8,'redirection'=>3]);
                if (!is_wp_error($r) && wp_remote_retrieve_response_code($r) === 404) {
                    $s['dead_links'][$p] = time();
                    return 'NAPRAWIONO ODNOŚNIKI: martwy link usunięty z HTML, zawartość zachowana. Sam brakujący adres nadal zwraca 404.';
                }
                unset($s['dead_links'][$p]);
                return 'DO SPRAWDZENIA: brak potwierdzonego celu albo statusu 404; bez zmiany linku.';
            }
            $http = self::check_http($target);
            if ($http !== 'HTTP 200') { unset($s['maps'][$p]); return 'DO SPRAWDZENIA: cel ' . $http; }
            unset($s['dead_links'][$p]);
            $s['maps'][$p] = ['target'=>$target,'kind'=>'page'];
            return 'GOTOWE: poprawa linków i 301 dla 404 → ' . $target;
        }
        if ($j['type'] === 'resource') {
            if (self::local($p)) { unset($s['maps'][$p]); return 'ISTNIEJE: ' . self::check_http($p); }
            if (!$target) { $target = self::discover_resource($p); }
            if (!$target || !self::local($target)) { unset($s['maps'][$p]); return 'DO UZUPEŁNIENIA: przywróć oryginalny plik lub wskaż właściwy zasób.'; }
            $http = self::check_http($target);
            if ($http !== 'HTTP 200') { unset($s['maps'][$p]); return 'DO SPRAWDZENIA: zasób docelowy ' . $http; }
            $s['maps'][$p] = ['target'=>$target,'kind'=>'resource'];
            return 'GOTOWE: adres zasobu w HTML → ' . $target;
        }
        if ($j['type'] === 'css') {
            if (self::local($p)) { unset($s['empty_css'][$p]); return 'ISTNIEJE: ' . self::check_http($p); }
            if (!preg_match('~post-(\d+)\.css$~', $p, $m)) { return 'POMINIĘTO: nieznany format CSS.'; }
            $id = (int)$m[1];
            if (!get_post($id) || !class_exists('Elementor\\Core\\Files\\CSS\\Post')) { return 'DO SPRAWDZENIA: brak dokumentu albo aktywnego Elementora.'; }
            $css = \Elementor\Core\Files\CSS\Post::create($id);
            $css->update();
            if (self::local($p)) { unset($s['empty_css'][$p]); return 'ODTWORZONO: CSS z danych Elementora; ' . self::check_http($p); }
            if (trim((string)$css->get_content()) === '') {
                $s['empty_css'][$p] = ['id'=>$id,'signature'=>self::css_signature($id)];
                return 'GOTOWE: Elementor nie generuje CSS dla dokumentu. Pominięto wyłącznie odnośnik do pustego, nieistniejącego arkusza.';
            }
            return 'DO SPRAWDZENIA: CSS nie został zapisany pod oczekiwanym adresem. Sprawdź uprawnienia uploads i konfigurację Elementora.';
        }
        if ($j['type'] === 'image') {
            $file = self::local($p);
            if (!$file) { return 'DO SPRAWDZENIA: brak źródłowego PNG.'; }
            if (filesize($file) <= 512000) { unset($s['maps'][$p]); return 'OK: plik ma mniej niż 512 000 bajtów.'; }
            $u = wp_upload_dir(); if (!empty($u['error'])) { return 'BŁĄD: ' . $u['error']; }
            $dir = $u['basedir'] . '/zp-suite-seo';
            if (!wp_mkdir_p($dir)) { return 'BŁĄD: brak prawa zapisu kopii obrazów.'; }
            $stem = $dir . '/' . sanitize_file_name(pathinfo($file, PATHINFO_FILENAME)) . '-' . substr(hash_file('sha256', $file),0,12);
            $out = ''; $editor = null;
            foreach ([82,76,70] as $quality) {
                $candidate = $stem . '-q' . $quality . '.webp';
                if (!file_exists($candidate)) {
                    if (!$editor) { $editor=wp_get_image_editor($file); }
                    if (is_wp_error($editor)) { return 'BŁĄD: ' . $editor->get_error_message(); }
                    $q=$editor->set_quality($quality);
                    if (is_wp_error($q)) { return 'BŁĄD: ' . $q->get_error_message(); }
                    $result=$editor->save($candidate,'image/webp');
                    if (is_wp_error($result)) { return 'BŁĄD: ' . $result->get_error_message(); }
                    if (($result['mime-type'] ?? '') !== 'image/webp') { return 'BŁĄD: serwer nie zapisał formatu WebP.'; }
                    $candidate=$result['path'];
                }
                if (is_file($candidate) && (!$out || filesize($candidate)<filesize($out))) { $out=$candidate; }
                if ($out && filesize($out)<=512000) { break; }
            }
            if (!is_file($out) || filesize($out) >= filesize($file)) { return 'BEZ ZMIAN: kopia nie jest mniejsza od oryginału.'; }
            $target = self::path($u['baseurl'] . '/zp-suite-seo/' . basename($out));
            if (!$target || !self::local($target)) { return 'BŁĄD: nie można ustalić lokalnego adresu kopii.'; }
            $http = self::check_http($target);
            if ($http !== 'HTTP 200') { return 'DO SPRAWDZENIA: kopia ' . $http; }
            $s['maps'][$p] = ['target'=>$target,'kind'=>'optimized'];
            return 'KOPIA WEBP: ' . size_format(filesize($file)) . ' → ' . size_format(filesize($out)) . (filesize($out)>512000 ? '. NADAL >512 kB: potrzebna decyzja o zmniejszeniu rozdzielczości / jakości.' : '. GOTOWE.');
        }
        return 'POMINIĘTO.';
    }
    public static function ajax() {
        if (!current_user_can('manage_options') || !self::allowed()) { wp_send_json_error(['message'=>'Brak uprawnień lub niewłaściwa domena.'], 403); }
        check_ajax_referer('zpsseo_jobs');
        $jobs = self::jobs(); $index = isset($_POST['index']) ? (int)$_POST['index'] : -1;
        if (!isset($jobs[$index])) { wp_send_json_error(['message'=>'Nieprawidłowe zadanie.'],400); }
        $lock = (int)get_option('zpsseo_job_lock', 0);
        if ($lock && $lock < time()-180) { delete_option('zpsseo_job_lock'); }
        if (!add_option('zpsseo_job_lock', time(), '', false)) { wp_send_json_error(['message'=>'Inna naprawa trwa. Ponów po jej zakończeniu.'],409); }
        $s = self::state();
        try { $message = self::process($jobs[$index], $s); }
        catch (\Throwable $e) { $message = 'BŁĄD: ' . $e->getMessage(); }
        $p = $jobs[$index]['source']; $s['results'][$p] = $message; self::save($s); delete_option('zpsseo_job_lock');
        wp_send_json_success(['index'=>$index,'message'=>$message,'source'=>$p]);
    }
    public static function status() {
        if (!current_user_can('manage_options')) { wp_send_json_error([],403); }
        check_ajax_referer('zpsseo_jobs');
        $q=get_option('zpsseo_auto_queue',[]);
        wp_send_json_success(['done'=>!empty($q['done']),'pending'=>count($q['pending'] ?? []),'results'=>self::state()['results'],'sources'=>array_column(self::jobs(),'source')]);
    }
    public static function menu() { add_management_page('Naprawy Ahrefs','Naprawy Ahrefs','manage_options','zp-suite-seo',[__CLASS__,'page']); }
    public static function notice() {
        if (!current_user_can('manage_options')) { return; }
        if (!self::allowed()) { echo '<div class="notice notice-warning"><p>Naprawy Ahrefs działają wyłącznie na zaprojektowani.com, w pojedynczej instalacji WordPress.</p></div>'; return; }
        if (empty(get_option('zpsseo_auto_queue', [])['done'])) { echo '<div class="notice notice-info"><p>Naprawy Ahrefs: automatyczna naprawa działa w tle. Opcjonalny podgląd: <a href="' . esc_url(admin_url('tools.php?page=zp-suite-seo')) . '">Narzędzia → Naprawy Ahrefs</a>. Przywracanie shortcode, H1 i meta działa automatycznie.</p></div>'; }
    }
    public static function assets($hook) {
        if ($hook !== 'tools_page_zp-suite-seo') { return; }
        wp_enqueue_script('zpar-admin', plugins_url('admin.js', __FILE__), [], '2.2.729', true);
        wp_localize_script('zpar-admin','ZPAR',['url'=>admin_url('admin-ajax.php'),'nonce'=>wp_create_nonce('zpsseo_jobs'),'total'=>count(self::jobs())]);
    }
    public static function settings() {
        if (!current_user_can('manage_options') || !self::allowed()) { wp_die('Brak uprawnień.'); }
        check_admin_referer('zpsseo_settings');
        if ((int)get_option('zpsseo_job_lock',0) > time()-180) { wp_die('Trwa automatyczna operacja. Ustawienia można zapisać po jej zakończeniu.'); }
        $s = self::state(); $s['enabled'] = !empty($_POST['enabled']); $errors = [];
        $raw = isset($_POST['targets']) && is_array($_POST['targets']) ? wp_unslash($_POST['targets']) : [];
        $jobs = self::jobs(); $sources = array_column($jobs,'source');
        foreach ($jobs as $i=>$j) {
            if (!in_array($j['type'], ['route','resource'], true) || !isset($raw[$i]) || !is_string($raw[$i])) { continue; }
            $value = trim($raw[$i]); $source = $j['source'];
            if ($value === '') { unset($s['custom'][$source],$s['maps'][$source]); continue; }
            $p = self::path($value);
            $valid = $p && !in_array($p, $sources, true) && wp_parse_url($value,PHP_URL_QUERY) === null && wp_parse_url($value,PHP_URL_FRAGMENT) === null;
            if ($j['type'] === 'route') { $valid = $valid && self::public_page($p); }
            else { $valid = $valid && self::local($p) && preg_match('~\.(webp|png|jpe?g|gif|svg|js)$~i', $p) && (pathinfo($source, PATHINFO_EXTENSION)==='js' ? pathinfo($p,PATHINFO_EXTENSION)==='js' : pathinfo($p,PATHINFO_EXTENSION)!=='js'); }
            if ($valid) { $s['custom'][$source] = $p; unset($s['maps'][$source]); }
            else { $errors[] = $source; }
        }
        self::save($s);
        if ($s['enabled']) { ZPSSEO_Automatic::start(); } else { ZPSSEO_Automatic::purge(); }
        set_transient('zpsseo_settings_message_' . get_current_user_id(), $errors ? 'Nie zapisano nieprawidłowych celów: ' . implode(', ', $errors) : 'Zapisano. Włączone poprawki zostaną przygotowane automatycznie.',60);
        wp_safe_redirect(admin_url('tools.php?page=zp-suite-seo')); exit;
    }
    public static function page() {
        if (!current_user_can('manage_options')) { return; }
        $s = self::state(); $jobs = self::jobs();
        $q = get_option('zpsseo_auto_queue', []);
        echo '<div class="notice notice-info"><p>' . esc_html(!empty($q['done']) ? 'Automatyczny przebieg zakończony. Wyniki poniżej obejmują także ograniczenia.' : 'Automatyczny przebieg w toku. Pozostałe operacje: ' . count($q['pending'] ?? [])) . '</p></div>';
        echo '<div class="wrap"><h1>Naprawy Ahrefs — Zaprojektowani</h1>';
        echo '<p>Zakres: raporty z 13.09.2026. Poprawki HTML są odwracalne. Wtyczka nie zmienia treści wpisów, nie usuwa skryptów, nie zmienia robots.txt ani ustawień indeksowania.</p>';
        echo '<p>Naprawy uruchamiają się samodzielnie po aktywacji, partiami w tle. Możesz zamknąć tę kartę. Obsługiwane cache WordPressa są czyszczone automatycznie po zakończeniu. Nowy audyt Ahrefs pokaże stan po ponownym skanowaniu.</p>';
        echo '<p>Nie ma gwarancji usunięcia wszystkich błędów: brakujących treści i oryginalnych zdjęć nie można odtworzyć z raportu. Istniejących reguł przekierowań serwera / innych wtyczek nie nadpisujemy. Podwójne opisy meta: pozostaje pierwszy niepusty opis z kodu strony.</p>';
        if (!self::allowed()) { echo '<p><strong>Nieobsługiwana domena lub multisite. Naprawy wyłączone.</strong></p></div>'; return; }
        $notice = get_transient('zpsseo_settings_message_' . get_current_user_id());
        if ($notice) { echo '<div class="notice notice-info"><p>' . esc_html($notice) . '</p></div>'; }
        echo '<p id="zpar-progress" role="status" aria-live="polite">Automatyczny przebieg — podgląd odświeża się sam.</p><progress id="zpar-bar" max="' . count($jobs) . '" value="0" style="width:100%"></progress>';
        echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '"><input type="hidden" name="action" value="zpsseo_save">';
        wp_nonce_field('zpsseo_settings');
        echo '<p><label><input type="checkbox" name="enabled" value="1" ' . checked($s['enabled'],true,false) . '> Włącz poprawki HTML, linków i przekierowania 301</label></p>';
        echo '<p>Pola poniżej są opcjonalne. Wpisz ścieżkę istniejącej strony lub zasobu, np. /kontakt/. Puste pole używa ustalonego odpowiednika, jeżeli istnieje. Zmianę zapisz; naprawa wznowi się automatycznie. Paginacja kategorii Suite korzysta ze wspólnego zapytania WordPressa i szablonu. Zewnętrzne reguły serwera pozostają poza zakresem.</p>';
        echo '<table class="widefat striped"><thead><tr><th>Adres i typ</th><th>Wynik ostatniej operacji</th><th>Opcjonalne ustawienie zaawansowane</th></tr></thead><tbody>';
        foreach ($jobs as $i=>$j) {
            $p = $j['source'];
            echo '<tr><td style="overflow-wrap:anywhere;max-width:430px"><small>' . esc_html($j['type']) . '</small><br>' . esc_html($p) . '</td><td id="zpar-result-' . $i . '" style="max-width:430px;overflow-wrap:anywhere">' . esc_html($s['results'][$p] ?? 'Oczekuje na uruchomienie') . '</td><td>';
            if (in_array($j['type'],['route','resource'],true)) { echo '<input aria-label="Cel dla ' . esc_attr($p) . '" style="width:100%;min-width:230px" name="targets[' . $i . ']" value="' . esc_attr($s['custom'][$p] ?? '') . '" placeholder="' . esc_attr($j['target'] ?: 'Brak ustalonego odpowiednika') . '">'; }
            echo '</td></tr>';
        }
        echo '</tbody></table>'; submit_button('Zapisz ustawienia'); echo '</form><p>Wycofanie: wyłącz poprawki lub dezaktywuj wtyczkę i wyczyść cache/CDN. Kopie WebP pozostają w uploads/zp-suite-seo; oryginały są nietknięte. Odtworzone CSS pozostają plikami cache Elementora. Przekierowania 301 mogą pozostawać w pamięci przeglądarki.</p></div>';
    }
}
ZPSSEO_Repair::init();
require_once __DIR__ . '/ultimate.php';
require_once __DIR__ . '/automatic.php';
