<?php
/** Explicit article maintenance: native categories/social images, durable backups, unchanged public URLs. */
if (!defined('ABSPATH')) { exit; }

final class ZP_Article_SEO_Maintenance_20261015 {
    public const PAGE = 'zp-article-seo-maintenance';
    public const PINS = 'zps_article_seo_20261015_pins';
    public const LOCK = 'zps_article_seo_20261015_lock';
    public const ROW = 'zps_article_seo_20261015_row_';
    public const VERSION = 1;
    public const LIMIT = 20;
    private const META = ['_thumbnail_id', 'rank_math_primary_category', 'rank_math_facebook_image', 'rank_math_facebook_image_id', 'rank_math_twitter_image', 'rank_math_twitter_image_id', 'rank_math_twitter_use_facebook', 'rank_math_focus_keyword', 'rank_math_title', 'rank_math_description'];
    private const SEO = ['rank_math_focus_keyword'=>500, 'rank_math_title'=>200, 'rank_math_description'=>400];

    public static function register(): void {
        add_action('admin_menu', static function (): void {
            add_management_page('Porządkowanie artykułów', 'Porządkowanie artykułów', 'manage_options', self::PAGE, [self::class, 'page']);
        });
        foreach (['zp_article_seo_apply', 'zp_article_seo_recover', 'zp_article_seo_export'] as $action) {
            add_action('admin_post_'.$action, static function () use ($action): void { self::handle($action); });
        }
        add_filter('post_link', [self::class, 'post_link'], PHP_INT_MAX, 3);
        add_filter('request', [self::class, 'request'], PHP_INT_MAX);
        add_filter('url_to_postid', [self::class, 'lookup_url'], PHP_INT_MAX);
        add_filter('zp_seo_plan_breadcrumb_category', [self::class, 'breadcrumb_category'], 20, 2);
        // Administrative snapshots do not change the frontend. Actual taxonomy/meta writes use native invalidation.
        add_filter('zp_html_cache_ignore_option', static function ($ignore, $name) {
            return $ignore || (is_string($name) && (strpos($name, self::ROW) === 0 || $name === self::LOCK));
        }, 10, 2);
    }

    public static function manifest(): array {
        $rows = function_exists('zp_article_seo_manifest_20261015') ? zp_article_seo_manifest_20261015() : [];
        if (!is_array($rows) || count($rows) > 162) { return []; }
        $out = [];
        foreach ($rows as $id => $spec) {
            if ((!is_int($id) && !(is_string($id) && ctype_digit($id))) || (int)$id < 1 || !is_array($spec)
                || !is_string($spec['category_slug'] ?? null) || !preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/D', $spec['category_slug'])) { return []; }
            $out[(int)$id] = $spec;
        }
        return $out;
    }

    public static function allowed(int $id = 0): bool {
        return current_user_can('manage_options') && current_user_can('edit_posts')
            && (!$id || (isset(self::manifest()[$id]) && current_user_can('edit_post', $id)));
    }

    /** Canonical local article paths only. No decoding, prefix guessing or private/admin routing. */
    public static function safe_path($path): bool {
        if (!is_string($path) || strlen($path) > 400 || !preg_match('~^/(?:[a-z0-9]+(?:-[a-z0-9]+)*/){2,5}$~D', $path)) { return false; }
        return !preg_match('~^/(?:en|oferty|briefy|offers|briefs|wp-admin|wp-json|wp-content|wp-includes|feed|author|tag|category|attachment|search)/~i', $path);
    }

    private static function path_from_url($url, string $slug): string {
        $parts = is_string($url) ? wp_parse_url($url) : false;
        $home = wp_parse_url(home_url('/'));
        if (!is_array($parts) || !is_array($home) || ($parts['scheme'] ?? '') !== ($home['scheme'] ?? '')
            || ($parts['host'] ?? '') !== ($home['host'] ?? '') || ($parts['port'] ?? null) !== ($home['port'] ?? null)
            || isset($parts['user'], $parts['pass']) || isset($parts['user']) || isset($parts['pass'])
            || isset($parts['query']) || isset($parts['fragment']) || !self::safe_path($parts['path'] ?? null)
            || basename($parts['path']) !== $slug) { throw new RuntimeException('unsafe_article_url'); }
        return $parts['path'];
    }

    public static function pins(): array {
        $pins = get_option(self::PINS, []);
        return is_array($pins) ? $pins : [];
    }

    private static function valid_pin(int $id, $pin, $post): bool {
        return isset(self::manifest()[$id]) && is_array($pin) && $post instanceof WP_Post
            && $post->post_type === 'post' && in_array($post->post_status, ['publish', 'future'], true)
            && $post->post_password === '' && is_string($pin['slug'] ?? null) && $pin['slug'] === $post->post_name
            && self::safe_path($pin['path'] ?? null) && basename($pin['path']) === $post->post_name;
    }

    public static function post_link($url, $post, $leavename = false) {
        if (!$post instanceof WP_Post) { return $url; }
        $pin = self::pins()[(int)$post->ID] ?? null;
        return self::valid_pin((int)$post->ID, $pin, $post) ? home_url($pin['path']) : $url;
    }

    /** Keep native ID lookup (translator/sitemaps/related links) consistent with the pinned HTTP route. */
    public static function lookup_url($url) {
        if (!is_string($url)) { return $url; }
        $parts = wp_parse_url($url); $home = wp_parse_url(home_url('/'));
        if (!is_array($parts) || !is_array($home) || ($parts['scheme'] ?? '') !== ($home['scheme'] ?? '')
            || ($parts['host'] ?? '') !== ($home['host'] ?? '') || ($parts['port'] ?? null) !== ($home['port'] ?? null)
            || isset($parts['user']) || isset($parts['pass']) || isset($parts['query']) || isset($parts['fragment'])
            || !self::safe_path($parts['path'] ?? null)) { return $url; }
        foreach (self::pins() as $id=>$pin) {
            if (!is_array($pin) || ($pin['path'] ?? null) !== $parts['path']) { continue; }
            $post = get_post((int)$id);
            return self::valid_pin((int)$id,$pin,$post) && $post->post_status === 'publish' && is_post_publicly_viewable($post)
                ? home_url('/?p='.(int)$id) : $url;
        }
        return $url;
    }

    public static function breadcrumb_category($category, $id) {
        $id = (int)$id;
        if (!isset(self::manifest()[$id]) || get_post_status($id) !== 'publish') { return $category; }
        $row = get_option(self::ROW.$id, []);
        if (!is_array($row) || ($row['state'] ?? '') !== 'applied') { return $category; }
        $primary = (int)get_post_meta($id, 'rank_math_primary_category', true);
        $terms = wp_get_post_categories($id);
        $term = $primary > 0 && is_array($terms) && in_array($primary, array_map('intval',$terms),true) ? get_term($primary,'category') : null;
        $url = $term instanceof WP_Term ? get_category_link($term) : null;
        return $term instanceof WP_Term && is_string($url) ? ['name'=>$term->name,'url'=>$url] : $category;
    }

    /** Languages rewrites /en/ to its original PL source before WordPress parses the request. */
    public static function request($vars) {
        if (!is_array($vars) || is_admin() || !in_array($_SERVER['REQUEST_METHOD'] ?? 'GET', ['GET', 'HEAD'], true)
            || (defined('REST_REQUEST') && REST_REQUEST) || (function_exists('wp_doing_ajax') && wp_doing_ajax())
            || isset($_GET['rest_route']) || isset($_GET['preview']) || isset($vars['preview'])
            || isset($vars['feed']) || isset($vars['attachment']) || isset($vars['attachment_id'])
            || isset($vars['s']) || isset($vars['post_type']) && $vars['post_type'] !== 'post') { return $vars; }
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        if (!is_string($uri) || !str_starts_with($uri, '/') || str_starts_with($uri, '//')) { return $vars; }
        $path = wp_parse_url($uri, PHP_URL_PATH);
        if (!self::safe_path($path)) { return $vars; }
        foreach (self::pins() as $id => $pin) {
            if (!is_array($pin) || ($pin['path'] ?? null) !== $path) { continue; }
            $post = get_post((int)$id);
            // A pin is never a preview/authentication bypass, including after trash/private transitions.
            if (!self::valid_pin((int)$id, $pin, $post) || $post->post_status !== 'publish'
                || !is_post_publicly_viewable($post)) { return ['error'=>'404']; }
            foreach (['p', 'page_id', 'name', 'pagename', 'category_name', 'cat', 'taxonomy', 'term', 'error'] as $key) { unset($vars[$key]); }
            $vars['p'] = (int)$id;
            $vars['post_type'] = 'post';
            return $vars;
        }
        return $vars;
    }

    private static function fresh(int $id): void { clean_post_cache($id); wp_cache_delete($id, 'post_meta'); }

    private static function digest($value, int $flags = 0): string {
        $json = wp_json_encode($value, $flags);
        if (!is_string($json)) { throw new RuntimeException('unreadable_post_state'); }
        return hash('sha256',$json);
    }

    /** Preserves dates, status, source content/Elementor and other editorial SEO fields across every write. */
    public static function guard(int $id): string {
        self::fresh($id);
        $p = get_post($id, OBJECT, 'raw');
        if (!$p instanceof WP_Post) { throw new RuntimeException('missing_post'); }
        $fields = [];
        foreach (['ID','post_type','post_status','post_password','post_title','post_name','post_parent','post_author','post_content','post_excerpt','post_date','post_date_gmt','post_modified','post_modified_gmt'] as $key) { $fields[$key] = $p->$key; }
        foreach (['_elementor_data','_elementor_page_settings','rank_math_canonical_url','rank_math_robots','rank_math_schema_Article'] as $key) { $fields['meta'][$key] = get_post_meta($id, $key, false); }
        $fields['future_cron'] = wp_next_scheduled('publish_future_post', [$id]);
        return self::digest($fields, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }

    private static function owned(int $id): array {
        $cats = wp_get_post_categories($id); if (is_wp_error($cats)) { throw new RuntimeException('category_read_failed'); }
        $cats = array_map('intval', $cats); sort($cats, SORT_NUMERIC);
        $meta = []; foreach (self::META as $key) { $meta[$key] = get_post_meta($id, $key, false); }
        return ['categories' => $cats, 'meta' => $meta];
    }

    private static function media(int $id): string {
        $attachment = get_post($id);
        $url = wp_get_attachment_url($id);
        $parts = is_string($url) ? wp_parse_url($url) : false;
        if (!$attachment instanceof WP_Post || $attachment->post_type !== 'attachment' || !wp_attachment_is_image($id)
            || !is_array($parts) || !in_array($parts['scheme'] ?? '', ['http','https'], true)
            || strtolower((string)($parts['host'] ?? '')) !== strtolower((string)wp_parse_url(home_url('/'), PHP_URL_HOST))
            || isset($parts['user']) || isset($parts['pass']) || isset($parts['query']) || isset($parts['fragment'])) { throw new RuntimeException('invalid_featured_media'); }
        return $url;
    }

    private static function path(int $id, WP_Post $post): string {
        if ($post->post_status === 'future') {
            if (!function_exists('get_sample_permalink')) { require_once ABSPATH.'wp-admin/includes/post.php'; }
            $sample = get_sample_permalink($id, $post->post_title, $post->post_name);
            if (!is_array($sample) || !is_string($sample[0] ?? null) || !is_string($sample[1] ?? null)) { throw new RuntimeException('sample_permalink_failed'); }
            $url = str_replace(['%postname%','%pagename%'], $sample[1], $sample[0]);
        } else { $url = get_permalink($id); }
        return self::path_from_url($url, $post->post_name);
    }

    public static function inspect(int $id): array {
        if (!self::allowed($id)) { throw new RuntimeException('forbidden'); }
        $spec = self::manifest()[$id]; self::fresh($id); $p = get_post($id, OBJECT, 'raw');
        if (!$p instanceof WP_Post || $p->post_type !== 'post' || !in_array($p->post_status, ['publish','future'], true) || $p->post_password !== '') { throw new RuntimeException('ineligible_post'); }
        $term = get_term_by('slug', $spec['category_slug'], 'category');
        if (!$term instanceof WP_Term) { throw new RuntimeException('missing_target_category'); }
        $guard = self::guard($id); $before = self::owned($id); $saved = get_option(self::ROW.$id, []);
        $manifest_hash = self::digest($spec);
        foreach ($before['meta'] as $values) { if (count($values) > 1) { throw new RuntimeException('ambiguous_metadata'); } }
        $already = is_array($saved) && ($saved['state'] ?? '') === 'applied' && ($saved['manifest_hash'] ?? '') === $manifest_hash && ($saved['guard'] ?? '') === $guard && ($saved['after'] ?? null) === $before;
        foreach (['expected_title'=>'post_title','expected_status'=>'post_status','expected_slug'=>'post_name','expected_date'=>'post_date'] as $expected => $native) {
            if (isset($spec[$expected]) && $spec[$expected] !== $p->$native) { throw new RuntimeException('manifest_identity_changed'); }
        }
        $current_featured = (int)get_post_thumbnail_id($id);
        if (!$already && isset($spec['expected_featured_id']) && (int)$spec['expected_featured_id'] !== $current_featured) { throw new RuntimeException('featured_changed'); }
        $featured = isset($spec['featured_id']) ? (int)$spec['featured_id'] : $current_featured;
        $featured_url = self::media($featured);
        $facebook = isset($spec['facebook_image_id']) ? (int)$spec['facebook_image_id'] : $featured;
        $facebook_url = self::media($facebook);
        $desired = $before;
        $desired['categories'] = [(int)$term->term_id];
        $values = ['_thumbnail_id'=>(string)$featured, 'rank_math_primary_category'=>(string)$term->term_id,
            'rank_math_facebook_image'=>$facebook_url, 'rank_math_facebook_image_id'=>(string)$facebook,
            'rank_math_twitter_image'=>$facebook_url, 'rank_math_twitter_image_id'=>(string)$facebook,
            'rank_math_twitter_use_facebook'=>'on'];
        // Preserve a manifest-reviewed existing custom Facebook choice when its actual attachment still matches.
        if (!empty($spec['preserve_existing_facebook'])) {
            if ((string)get_post_meta($id,'rank_math_facebook_image_id',true) !== (string)$facebook
                || (string)get_post_meta($id,'rank_math_facebook_image',true) !== $facebook_url) { throw new RuntimeException('facebook_choice_changed'); }
        }
        foreach ($values as $key => $value) { $desired['meta'][$key] = [$value]; }
        if (isset($spec['seo_meta'])) {
            if (!is_array($spec['seo_meta']) || !is_array($spec['expected_seo_meta'] ?? null)) { throw new RuntimeException('invalid_seo_manifest'); }
            foreach ($spec['seo_meta'] as $key=>$value) {
                if (!isset(self::SEO[$key]) || !is_string($value) || trim($value) === '' || mb_strlen($value,'UTF-8') > self::SEO[$key]
                    || preg_match('/[<>\x00-\x1f\x7f]/u',$value) || !array_key_exists($key,$spec['expected_seo_meta'])) { throw new RuntimeException('invalid_seo_manifest'); }
                $expected = $spec['expected_seo_meta'][$key];
                if (is_string($expected)) { $expected = [$expected]; }
                if (!is_array($expected) || count($expected) > 1 || array_filter($expected,static fn($v)=>!is_string($v))) { throw new RuntimeException('invalid_seo_manifest'); }
                if (!$already && $before['meta'][$key] !== $expected) { throw new RuntimeException('seo_fields_changed'); }
                $desired['meta'][$key] = [$value];
            }
        }
        $path = self::path($id, $p); $pin = ['path'=>$path, 'slug'=>$p->post_name];
        foreach (self::pins() as $other => $existing) {
            if ((int)$other !== $id && is_array($existing) && ($existing['path'] ?? null) === $path) { throw new RuntimeException('duplicate_pin'); }
        }
        $review = self::digest([self::VERSION,$id,$manifest_hash,$guard,$before,$desired,$pin,self::pins()[$id] ?? null]);
        return ['id'=>$id,'title'=>$p->post_title,'status'=>$p->post_status,'date'=>$p->post_date,'path'=>$path,'category_slug'=>$spec['category_slug'],
            'featured_id'=>$featured,'featured_url'=>$featured_url,'facebook_id'=>$facebook,'facebook_url'=>$facebook_url,
            'stored_rankmath_score'=>get_post_meta($id,'rank_math_seo_score',true),'guard'=>$guard,'manifest_hash'=>$manifest_hash,
            'before'=>$before,'after'=>$desired,'pin'=>$pin,'review_hash'=>$review,'already_applied'=>$already,
            'backup_state'=>is_array($saved)?($saved['state'] ?? ''):''];
    }

    private static function save(string $name, array $value): void {
        if (get_option($name, null) === null) { add_option($name, $value, '', false); }
        else { update_option($name, $value, false); }
        if (get_option($name, null) !== $value) { throw new RuntimeException('backup_storage_failed'); }
    }

    private static function lock(): string {
        $token = wp_generate_uuid4(); $old = get_option(self::LOCK, null);
        if (is_array($old) && (int)($old['until'] ?? PHP_INT_MAX) < time()) {
            global $wpdb;
            if ($wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->options} WHERE option_name=%s AND option_value=%s", self::LOCK, maybe_serialize($old)))) { wp_cache_delete(self::LOCK, 'options'); }
        }
        if (!add_option(self::LOCK, ['token'=>$token,'until'=>time()+180], '', false)) { throw new RuntimeException('busy'); }
        return $token;
    }

    private static function owns(string $token): void {
        $value = get_option(self::LOCK, []);
        if (!is_array($value) || ($value['token'] ?? '') !== $token || (int)($value['until'] ?? 0) <= time()) { throw new RuntimeException('lock_expired'); }
    }

    private static function unlock(string $token): void {
        $value = get_option(self::LOCK, []);
        if (!is_array($value) || ($value['token'] ?? '') !== $token) { return; }
        global $wpdb;
        if ($wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->options} WHERE option_name=%s AND option_value=%s", self::LOCK, maybe_serialize($value)))) { wp_cache_delete(self::LOCK, 'options'); }
    }

    private static function assert_guard(int $id, string $guard, string $token): void {
        self::owns($token);
        if (!self::allowed($id) || !hash_equals($guard, self::guard($id))) { throw new RuntimeException('concurrent_edit'); }
    }

    private static function assert_owned(int $id, array $row, string $token): void {
        self::assert_guard($id, $row['guard'], $token);
        if (!self::partial_matches($id,$row)) { throw new RuntimeException('owned_fields_changed'); }
    }

    private static function write_meta(int $id, string $key, array $values): void {
        if (!in_array($key, self::META, true)) { throw new RuntimeException('invalid_metadata_key'); }
        if (get_post_meta($id, $key, false) === $values) { return; }
        if (count($values) > 1) { throw new RuntimeException('ambiguous_metadata'); }
        if ($values === []) { delete_post_meta($id, $key); }
        else { update_post_meta($id, $key, wp_slash($values[0])); }
        wp_cache_delete($id, 'post_meta');
        if (get_post_meta($id, $key, false) !== $values) { throw new RuntimeException('metadata_write_failed'); }
    }

    private static function partial_matches(int $id, array $row): bool {
        $current = self::owned($id);
        // Native wp_set_object_terms adds the new relationship before deleting old ones.
        $union = array_values(array_unique(array_merge($row['before']['categories'],$row['after']['categories']))); sort($union,SORT_NUMERIC);
        if ($current['categories'] !== $row['before']['categories'] && $current['categories'] !== $row['after']['categories'] && $current['categories'] !== $union) { return false; }
        foreach (self::META as $key) {
            $now = $current['meta'][$key];
            if ($now !== $row['before']['meta'][$key] && $now !== $row['after']['meta'][$key]) { return false; }
        }
        return true;
    }

    private static function restore(int $id, array &$row, string $token): void {
        self::assert_guard($id, $row['guard'], $token);
        if (!self::partial_matches($id, $row)) { throw new RuntimeException('owned_fields_changed'); }
        $pins = self::pins();
        if (($pins[$id] ?? null) !== $row['pin'] && ($pins[$id] ?? null) !== $row['prior_pin']) { throw new RuntimeException('pin_changed'); }
        $row['state'] = 'recovering'; self::save(self::ROW.$id, $row);
        foreach (self::META as $key) { self::assert_owned($id, $row, $token); self::write_meta($id, $key, $row['before']['meta'][$key]); }
        self::assert_owned($id, $row, $token);
        $result = wp_set_post_terms($id, $row['before']['categories'], 'category', false);
        if (is_wp_error($result) || self::owned($id) !== $row['before']) { throw new RuntimeException('restore_readback_failed'); }
        if ($row['prior_pin'] === null) { unset($pins[$id]); } else { $pins[$id] = $row['prior_pin']; }
        self::save(self::PINS, $pins);
        self::assert_guard($id, $row['guard'], $token);
        $row['state'] = 'recovered'; $row['recovered_at'] = time(); self::save(self::ROW.$id, $row);
    }

    public static function apply_one(int $id, string $review, string $token): string {
        self::owns($token); $view = self::inspect($id);
        if ($view['already_applied']) { return 'already_applied'; }
        if (!hash_equals($view['review_hash'], $review)) { throw new RuntimeException('stale_preview'); }
        $prior = get_option(self::ROW.$id, []);
        if (is_array($prior) && $prior && !in_array($prior['state'] ?? '', ['recovered','rolled_back'], true)) { throw new RuntimeException('recovery_required'); }
        $row = ['version'=>self::VERSION,'id'=>$id,'manifest_hash'=>$view['manifest_hash'],'guard'=>$view['guard'],
            'before'=>$view['before'],'after'=>$view['after'],'pin'=>$view['pin'],'prior_pin'=>self::pins()[$id] ?? null,
            'state'=>'prepared','actor'=>get_current_user_id(),'prepared_at'=>time()];
        self::save(self::ROW.$id, $row); // Durable backup must be verified before the first mutation.
        self::assert_guard($id, $row['guard'], $token);
        if (self::owned($id) !== $row['before']) { throw new RuntimeException('concurrent_edit'); }
        $pins = self::pins(); $pins[$id] = $row['pin']; self::save(self::PINS, $pins);
        $row['state'] = 'applying'; self::save(self::ROW.$id, $row);
        try {
            foreach (self::META as $key) { self::assert_owned($id, $row, $token); self::write_meta($id, $key, $row['after']['meta'][$key]); }
            self::assert_owned($id, $row, $token);
            if (self::owned($id)['categories'] !== $row['after']['categories']) {
                $result = wp_set_post_terms($id, $row['after']['categories'], 'category', false);
                if (is_wp_error($result)) { throw new RuntimeException('category_write_failed'); }
            }
            self::assert_guard($id, $row['guard'], $token);
            if (self::owned($id) !== $row['after'] || self::path($id, get_post($id)) !== $row['pin']['path']) { throw new RuntimeException('apply_readback_failed'); }
            $row['state'] = 'applied'; $row['applied_at'] = time(); self::save(self::ROW.$id, $row);
            return 'applied';
        } catch (Throwable $error) {
            $row['state'] = 'needs_attention'; $row['error'] = self::error_code($error);
            try { self::restore($id, $row, $token); $row['state'] = 'rolled_back'; } catch (Throwable $ignored) { $row['state'] = 'needs_attention'; }
            self::save(self::ROW.$id, $row);
            throw $error;
        }
    }

    public static function batch(array $selection, bool $recover = false): array {
        if (!self::allowed() || !$selection || count($selection) > self::LIMIT) { throw new RuntimeException('invalid_batch'); }
        foreach ($selection as $id => $hash) { if (!self::allowed((int)$id) || !is_string($hash) || !preg_match('/^[a-f0-9]{64}$/D', $hash)) { throw new RuntimeException('invalid_batch'); } }
        $token = self::lock(); $out = [];
        try {
            foreach ($selection as $id => $hash) {
                $id = (int)$id;
                try {
                    if (!$recover) { $out[$id] = self::apply_one($id, $hash, $token); }
                    else {
                        $row = get_option(self::ROW.$id, []);
                        if (!is_array($row) || !$row || !hash_equals(self::recovery_hash($id, $row), $hash)) { throw new RuntimeException('stale_recovery_preview'); }
                        self::restore($id, $row, $token); $out[$id] = 'recovered';
                    }
                } catch (Throwable $error) { $out[$id] = self::error_code($error); }
            }
        } finally { self::unlock($token); }
        return $out;
    }

    private static function recovery_hash(int $id, array $row): string { return self::digest([$id,$row,self::guard($id),self::owned($id),self::pins()[$id] ?? null]); }

    private static function error_code(Throwable $error): string {
        $code = $error instanceof RuntimeException ? $error->getMessage() : 'unexpected_error';
        return preg_match('/^[a-z_]{3,70}$/D', $code) ? $code : 'unexpected_error';
    }

    public static function inventory(): array {
        if (!self::allowed()) { return []; }
        $out = [];
        foreach (self::manifest() as $id => $spec) {
            try { $out[$id] = self::inspect($id); }
            catch (Throwable $error) { $out[$id] = ['id'=>$id,'title'=>(string)get_the_title($id),'category_slug'=>$spec['category_slug'],'error'=>self::error_code($error)]; }
        }
        return $out;
    }

    public static function handle(string $action): void {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST' || !self::allowed()
            || !is_string($_POST['_wpnonce'] ?? null) || !wp_verify_nonce(wp_unslash($_POST['_wpnonce']), $action)) { wp_die('Brak uprawnień lub nieprawidłowe potwierdzenie.', '', ['response'=>403]); }
        if ($action === 'zp_article_seo_export') {
            nocache_headers(); header('Content-Type: application/json; charset=utf-8');
            header('Content-Disposition: attachment; filename="zaprojektowani-article-seo-inventory-'.gmdate('Ymd-His').'.json"');
            echo wp_json_encode(['generated_at'=>gmdate(DATE_ATOM),'version'=>self::VERSION,'score_note'=>'Stored Rank Math scores; fresh analysis requires the native analyzer.','rows'=>self::inventory()], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); exit;
        }
        $selection = [];
        try {
            $ids = $_POST['ids'] ?? null; $hashes = $_POST['review'] ?? null;
            if (!is_array($ids) || !is_array($hashes) || count($ids) > self::LIMIT || ($_POST['ack'] ?? '') !== '1') { throw new RuntimeException('invalid_batch'); }
            foreach ($ids as $id) { if (!is_string($id) || !ctype_digit($id) || isset($selection[(int)$id]) || !is_string($hashes[$id] ?? null)) { throw new RuntimeException('invalid_batch'); } $selection[(int)$id] = $hashes[$id]; }
            $result = self::batch($selection, $action === 'zp_article_seo_recover');
        } catch (Throwable $error) { $result = ['batch'=>self::error_code($error)]; }
        set_transient('zps_article_seo_notice_'.get_current_user_id(), $result, 300);
        wp_safe_redirect(admin_url('tools.php?page='.self::PAGE)); exit;
    }

    private static function form(string $action): void {
        echo '<form method="post" action="'.esc_url(admin_url('admin-post.php')).'"><input type="hidden" name="action" value="'.esc_attr($action).'">'; wp_nonce_field($action);
    }

    public static function page(): void {
        if (!self::allowed()) { wp_die('Brak uprawnień.'); }
        $rows = self::inventory(); $counts = []; $fb = 0; $featured = 0;
        foreach ($rows as $row) { if (isset($row['error'])) { continue; } $counts[$row['category_slug']] = ($counts[$row['category_slug']] ?? 0)+1;
            if ($row['before']['meta']['rank_math_facebook_image'] !== $row['after']['meta']['rank_math_facebook_image']) { $fb++; }
            if ($row['before']['meta']['_thumbnail_id'] !== $row['after']['meta']['_thumbnail_id']) { $featured++; }
        }
        echo '<div class="wrap"><h1>Porządkowanie artykułów</h1><p>Podgląd dotyczy wyłącznie zatwierdzonego manifestu '.count($rows).' artykułów. Każdy zapis ma kopię bezpieczeństwa. Treść, Elementor, status i daty publikacji pozostają bez zmian. Istniejące adresy zachowujemy przed zmianą kategorii.</p>';
        $notice = get_transient('zps_article_seo_notice_'.get_current_user_id());
        if (is_array($notice)) { echo '<div class="notice notice-info"><p>'; foreach ($notice as $id=>$value) { echo esc_html('#'.$id.': '.$value).' · '; } echo '</p></div>'; }
        echo '<p>Plan kategorii: '; foreach ($counts as $slug=>$count) { echo esc_html($slug.': '.$count).' · '; }
        echo '</p><p>Obrazy Facebook do uzupełnienia/poprawy: <strong>'.(int)$fb.'</strong>. Zmiany obrazu wyróżniającego: <strong>'.(int)$featured.'</strong>.</p>';
        self::form('zp_article_seo_export'); echo '<p><button class="button">Pobierz aktualny podgląd JSON</button></p></form>';
        echo '<p>Kolumna Rank Math pokazuje wyłącznie zapisany wynik. Nie traktujemy jej jako świeżego pomiaru ani nie zapisujemy punktów ręcznie.</p>';
        self::form('zp_article_seo_apply'); $preselected = 0;
        echo '<table class="widefat striped"><thead><tr><th>Wybierz ≤20</th><th>Artykuł / termin</th><th>Kategoria docelowa / zachowany adres</th><th>Własny obraz</th><th>Zapisany Rank Math / stan</th></tr></thead><tbody>';
        foreach ($rows as $id=>$row) {
            echo '<tr><td>';
            if (!isset($row['error']) && !in_array($row['backup_state'], ['prepared','applying','recovering','needs_attention'], true)) {
                $selected = !$row['already_applied'] && $preselected < self::LIMIT;
                if ($selected) { $preselected++; }
                echo '<input type="checkbox" name="ids[]" value="'.(int)$id.'"'.($selected?' checked':'').' aria-label="'.esc_attr('Wybierz '.$row['title']).'"><input type="hidden" name="review['.(int)$id.']" value="'.esc_attr($row['review_hash']).'">';
            }
            echo '</td><td>#'.(int)$id.' <a href="'.esc_url(get_edit_post_link($id, 'raw')).'">'.esc_html($row['title']).'</a>';
            if (isset($row['error'])) { echo '</td><td colspan="3">'.esc_html($row['error']).'</td></tr>'; continue; }
            echo '<br>'.esc_html($row['status'].' · '.$row['date']).'</td><td>'.esc_html($row['category_slug']).'<br><code>'.esc_html($row['path']).'</code></td><td>#'.(int)$row['featured_id'].'<br><img src="'.esc_url($row['featured_url']).'" alt="" width="100" style="height:65px;object-fit:contain">';
            if ($row['facebook_id'] !== $row['featured_id']) { echo '<br>Własny Facebook #'.(int)$row['facebook_id'].'<br><img src="'.esc_url($row['facebook_url']).'" alt="" width="100" style="height:65px;object-fit:contain">'; }
            echo '</td><td>'.esc_html((string)$row['stored_rankmath_score']).'<br>'.esc_html($row['already_applied']?'Zapisane i zweryfikowane':$row['backup_state']);
            foreach (self::SEO as $key=>$max) { if ($row['before']['meta'][$key] !== $row['after']['meta'][$key]) { echo '<details><summary>'.esc_html($key).'</summary><p>Przed: '.esc_html((string)($row['before']['meta'][$key][0] ?? '')).'</p><p>Po: '.esc_html((string)($row['after']['meta'][$key][0] ?? '')).'</p></details>'; } }
            echo '</td></tr>';
        }
        echo '</tbody></table><p><label><input type="checkbox" name="ack" value="1" required> Sprawdziłem wybrane artykuły; zapisuję kategorie i własne obrazy według podglądu (maks. 20).</label></p><p><button class="button button-primary">Zapisz wybrane z kopią bezpieczeństwa</button></p></form>';
        $recoverable = [];
        foreach ($rows as $id=>$view) { $row = get_option(self::ROW.$id, []); if (is_array($row) && in_array($row['state'] ?? '', ['prepared','applying','applied','recovering','needs_attention'], true)) { $recoverable[$id] = $row; } }
        if ($recoverable) {
            echo '<details><summary>Przywróć poprzednie kategorie i obrazy</summary><p>Przywracamy tylko własne wartości z kopii, jeżeli treść, status, daty i pola nie zostały zmienione równolegle. Nie usuwamy obrazów z Biblioteki.</p>';
            self::form('zp_article_seo_recover');
            foreach ($recoverable as $id=>$row) { try { $hash = self::recovery_hash($id,$row); } catch (Throwable $ignored) { continue; }
                echo '<p><label><input type="checkbox" name="ids[]" value="'.(int)$id.'"> #'.(int)$id.' '.esc_html((string)get_the_title($id)).' · '.esc_html($row['state']).'</label><input type="hidden" name="review['.(int)$id.']" value="'.esc_attr($hash).'"></p>'; }
            echo '<p><label><input type="checkbox" name="ack" value="1" required> Przywracam wybrane własne zmiany z kopii (maks.20).</label></p><button class="button">Przywróć wybrane</button></form></details>';
        }
        if (function_exists('zp_article_seo_rankmath_controls_20261015')) { zp_article_seo_rankmath_controls_20261015(array_keys(self::manifest())); }
        echo '</div>';
    }
}
ZP_Article_SEO_Maintenance_20261015::register();
