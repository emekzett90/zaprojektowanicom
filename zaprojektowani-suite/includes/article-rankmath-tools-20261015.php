<?php
/**
 * Explicit administrator-only adapter for the native Rank Math Tools analyzer.
 * The native JavaScript calculates scores and its native REST endpoint stores them.
 * This adapter supplies saved article HTML, sample future URLs and a bounded scope.
 */
if (!defined('ABSPATH')) { exit; }

final class ZP_Article_RankMath_Tools_20261015 {
    public const FLAG = 'zp_article_rankmath';
    public const USER_KEY = 'zp_article_rankmath_20261015';
    private const HEADER = 'X-ZP-Article-RankMath';
    private const TTL = 3600;
    private static $active = false;
    private static $offset = 0;
    private static $queried = false;
    private static $accepted = [];
    private static $failure = '';

    public static function register(): void {
        add_action('admin_post_zp_article_rankmath_20261015', [self::class, 'activate']);
        add_action('admin_enqueue_scripts', [self::class, 'client'], 100);
        add_action('admin_notices', [self::class, 'notice']);
        add_filter('rank_math/json_data', [self::class, 'json_data'], PHP_INT_MAX);
        add_filter('rank_math/recalculate_scores_batch_size', [self::class, 'batch_size'], PHP_INT_MAX);
        add_filter('rank_math/tools/update_seo_score', [self::class, 'begin'], 0, 2);
        add_action('pre_get_posts', [self::class, 'query'], PHP_INT_MAX);
        add_filter('rank_math/recalculate_score/data', [self::class, 'input'], PHP_INT_MAX, 2);
        add_filter('rank_math/tools/update_seo_score', [self::class, 'finish'], PHP_INT_MAX, 2);
        add_filter('rest_pre_dispatch', [self::class, 'guard_score'], 20, 3);
        add_filter('rest_request_after_callbacks', [self::class, 'saved_score'], 20, 3);
    }

    public static function ids(): array {
        if (!function_exists('zp_article_seo_manifest_20261015')) { return []; }
        $ids = array_map('intval', array_keys(zp_article_seo_manifest_20261015()));
        $ids = array_values(array_unique(array_filter($ids, static function ($id) { return $id > 0; })));
        sort($ids, SORT_NUMERIC);
        return count($ids) === 162 ? $ids : [];
    }

    private static function scope_hash(): string {
        return hash('sha256', (string)wp_json_encode(zp_article_seo_manifest_20261015()));
    }

    private static function session(): array {
        if (!current_user_can('manage_options') || self::ids() === []) { return []; }
        $state = get_user_meta(get_current_user_id(), self::USER_KEY, true);
        if (!is_array($state) || ($state['expires'] ?? 0) < time()
            || !is_string($state['token'] ?? null) || strlen($state['token']) !== 64
            || !hash_equals(self::scope_hash(), (string)($state['scope_hash'] ?? ''))) { return []; }
        return $state;
    }

    private static function screen(): bool {
        return is_admin() && ($_SERVER['REQUEST_METHOD'] ?? '') === 'GET'
            && ($_GET['page'] ?? '') === 'rank-math-status'
            && ($_GET['view'] ?? '') === 'tools' && ($_GET[self::FLAG] ?? '') === '1'
            && self::session() !== [];
    }

    private static function authorized_request($request, string $route): bool {
        if (!$request instanceof WP_REST_Request || $request->get_method() !== 'POST'
            || $request->get_route() !== $route) { return false; }
        $state = self::session();
        $token = $request->get_header(self::HEADER);
        return $state !== [] && is_string($token) && hash_equals($state['token'], $token);
    }

    /** A fresh click starts a fresh run; no publication or SEO metadata is changed. */
    public static function activate(): void {
        if (!current_user_can('manage_options')) { wp_die('Brak uprawnień.', '', ['response'=>403]); }
        check_admin_referer('zp_article_rankmath_20261015');
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST' || self::ids() === []
            || !class_exists('RankMath\\Tools\\Update_Score')) {
            wp_die('Brak zgodnego modułu Rank Math lub manifestu 162 artykułów.', '', ['response'=>409]);
        }
        if (isset($_POST['stop'])) {
            delete_user_meta(get_current_user_id(), self::USER_KEY);
            wp_safe_redirect(admin_url('tools.php?page=zp-article-seo-maintenance')); exit;
        }
        foreach (self::ids() as $id) {
            if (!self::eligible($id)) { wp_die('Przerwano: zmieniony zakres lub brakujące dane artykułu ID '.(int)$id.'.', '', ['response'=>409]); }
        }
        update_user_meta(get_current_user_id(), self::USER_KEY, [
            'token'=>bin2hex(random_bytes(32)), 'expires'=>time()+self::TTL,
            'scope_hash'=>self::scope_hash(), 'next'=>0, 'pending'=>[], 'results'=>[],
        ]);
        wp_safe_redirect(admin_url('admin.php?page=rank-math-status&view=tools&'.self::FLAG.'=1')); exit;
    }

    public static function controls(array $ids): void {
        if (!current_user_can('manage_options')) { return; }
        $expected = self::ids(); $given = array_values(array_unique(array_map('intval', $ids))); sort($given);
        if ($expected === [] || $given !== $expected) { echo '<p>Rank Math: niezgodny zakres manifestu — tryb niedostępny.</p>'; return; }
        echo '<section><h2>Rzeczywista punktacja Rank Math</h2><p>Otwiera natywne Narzędzia Rank Math dla dokładnie 162 artykułów, w tym zaplanowanych. Uruchom tam „Przelicz wyniki SEO”. Oficjalny JavaScript obliczy wynik z zapisanej treści artykułu, bieżących pól SEO i docelowego adresu. Nie zmienia publikacji, dat ani treści. Wynik może pozostać poniżej 70 i wymaga wtedy poprawy wskazanych testów.</p>';
        echo '<form method="post" action="'.esc_url(admin_url('admin-post.php')).'"><input type="hidden" name="action" value="zp_article_rankmath_20261015">';
        wp_nonce_field('zp_article_rankmath_20261015');
        echo '<button class="button button-primary" type="submit">Otwórz natywne przeliczanie 162 artykułów</button></form>';
        $state = self::session();
        if ($state !== []) {
            echo '<p>Ostatni uruchomiony zakres: '.count($state['results'] ?? []).'/162 potwierdzonych wyników. Tryb wygasa po godzinie; inne karty zwykłych Narzędzi działają bez zmian.</p>';
            if (!empty($state['results'])) {
                echo '<details><summary>Potwierdzone wyniki natywnego analizatora (JSON)</summary><pre id="zpArticleRankMathResults" style="white-space:pre-wrap">'.esc_html((string)wp_json_encode($state['results'])).'</pre></details>';
            }
            echo '<form method="post" action="'.esc_url(admin_url('admin-post.php')).'"><input type="hidden" name="action" value="zp_article_rankmath_20261015"><input type="hidden" name="stop" value="1">';
            wp_nonce_field('zp_article_rankmath_20261015'); echo '<button class="button">Zakończ tryb artykułów</button></form>';
        }
        echo '</section>';
    }

    public static function notice(): void {
        if (!self::screen()) { return; }
        echo '<div class="notice notice-info"><p><strong>Jawny tryb 162 artykułów:</strong> przeliczanie użyje zapisanej treści Elementor i obejmie także wpisy zaplanowane, bez ich publikowania. Uruchom tylko narzędzie „Przelicz wyniki SEO”. Wynik oblicza Rank Math; limit 70 nie jest wymuszany. Nie otwieraj równolegle drugiego przebiegu w tym trybie.</p></div>';
    }

    public static function json_data($data) {
        if (self::screen() && is_array($data) && isset($data['assessor'], $data['totalPosts'], $data['batchSize'])) {
            $data['totalPosts'] = 162; $data['totalPostsWithoutScore'] = 162; $data['batchSize'] = 1;
        }
        return $data;
    }

    public static function batch_size($size) { return self::screen() ? 1 : $size; }

    /** Attach only to the two official requests made from this explicitly selected Tools card. */
    public static function client(): void {
        if (!self::screen()) { return; }
        $token = wp_json_encode(self::session()['token']);
        $js = '(function($){"use strict";if(!$||window.zpArticleRankMathBound)return;window.zpArticleRankMathBound=true;var token='.$token.';$.ajaxPrefilter(function(o,original,xhr){var u;try{u=new URL(o.url,location.href);}catch(e){return;}if(u.origin!==location.origin)return;var route=u.searchParams.get("rest_route")||u.pathname;var tool=/\\/rankmath\\/v1\\/toolsAction\\/?$/.test(route),save=/\\/rankmath\\/v1\\/updateSeoScore\\/?$/.test(route);var data=typeof o.data==="string"?new URLSearchParams(o.data):new URLSearchParams(original.data||{});if((tool&&data.get("action")==="update_seo_score")||save)xhr.setRequestHeader("'.self::HEADER.'",token);});})(window.jQuery);';
        wp_add_inline_script('jquery-core', $js, 'after');
    }

    public static function begin($message, $request = null) {
        self::$active = false; self::$queried = false; self::$accepted = []; self::$failure = '';
        if (!self::authorized_request($request, '/rankmath/v1/toolsAction')
            || $request->get_param('action') !== 'update_seo_score') { return $message; }
        self::$active = true;
        // The older public-page adapter intentionally rejects future posts. Replace it only in this request.
        $old = 'ZP_RankMath_Tools_Content_Bridge_20261012';
        remove_filter('rank_math/tools/update_seo_score', [$old, 'begin'], 1);
        remove_filter('rank_math/tools/update_seo_score', [$old, 'finish'], PHP_INT_MAX);
        remove_filter('rank_math/recalculate_score/data', [$old, 'input'], PHP_INT_MAX);
        $args = $request->get_param('args'); $offset = is_array($args) ? ($args['offset'] ?? 0) : null;
        $state = self::session();
        if ((!is_int($offset) && !(is_string($offset) && ctype_digit($offset)))
            || (int)$offset !== (int)$state['next'] || (int)$offset > 162 || !empty($state['pending'])) {
            self::$failure = 'offset_or_pending'; return $message;
        }
        self::$offset = (int)$offset;
        return $message;
    }

    public static function query($query): void {
        if (!self::$active || self::$queried
            || $query->get('orderby') !== 'ID' || $query->get('order') !== 'ASC'
            || $query->get('status') !== 'any') { return; }
        self::$queried = true;
        if (self::$failure !== '' || (int)$query->get('offset') !== self::$offset) {
            self::$failure = self::$failure ?: 'query_offset_changed';
            // Native hooks do not short-circuit on an earlier WP_Error. Even a
            // rejected marked request must not fall back to the broad native query.
            $query->set('post__in', [0]); $query->set('posts_per_page', 1);
            return;
        }
        $query->set('post_type', 'post'); $query->set('post_status', ['publish', 'future']);
        $query->set('post__in', self::ids()); $query->set('posts_per_page', 1);
        $query->set('meta_query', []); $query->set('has_password', false);
        $query->set('ignore_sticky_posts', true);
    }

    private static function eligible(int $id): bool {
        $manifest = function_exists('zp_article_seo_manifest_20261015') ? zp_article_seo_manifest_20261015() : [];
        $spec = $manifest[$id] ?? null; $post = get_post($id);
        if (!is_array($spec) || !$post || !current_user_can('edit_post', $id)
            || $post->post_type !== 'post' || !in_array($post->post_status, ['publish','future'], true)
            || $post->post_password !== '' || $post->post_status !== ($spec['expected_status'] ?? '')
            || $post->post_name !== ($spec['expected_slug'] ?? '') || $post->post_date !== ($spec['expected_date'] ?? '')
            || trim((string)get_post_meta($id, 'rank_math_focus_keyword', true)) === '') { return false; }
        return true;
    }

    /** Hash actual saved inputs; score meta itself is deliberately excluded. */
    public static function fingerprint(int $id): string {
        $post = get_post($id);
        if (!$post) { return ''; }
        $meta = [];
        foreach (['_elementor_data','_elementor_edit_mode','_thumbnail_id','rank_math_title','rank_math_description',
            'rank_math_focus_keyword','rank_math_robots','rank_math_canonical_url','rank_math_contentai_score'] as $key) {
            $meta[$key] = get_post_meta($id, $key, false);
        }
        $thumb = (int)get_post_thumbnail_id($id);
        $meta['thumbnail_alt'] = $thumb ? get_post_meta($thumb, '_wp_attachment_image_alt', false) : [];
        return hash('sha256', (string)wp_json_encode([
            'id'=>$id,'type'=>$post->post_type,'status'=>$post->post_status,'slug'=>$post->post_name,
            'title'=>$post->post_title,'date'=>$post->post_date,'date_gmt'=>$post->post_date_gmt,
            'modified'=>$post->post_modified_gmt,'password'=>$post->post_password,'content'=>$post->post_content,
            'excerpt'=>$post->post_excerpt,'meta'=>$meta,'url'=>self::intended_url($id),
            'title_settings'=>get_option('rank-math-options-titles', []),
        ]));
    }

    private static function refresh(int $id): void { clean_post_cache($id); wp_cache_delete($id, 'post_meta'); }

    public static function intended_url(int $id): string {
        $post = get_post($id);
        if (!$post) { return ''; }
        if ($post->post_status !== 'future') { return (string)get_permalink($id); }
        if (!function_exists('get_sample_permalink')) { require_once ABSPATH.'wp-admin/includes/post.php'; }
        $sample = get_sample_permalink($id);
        if (!is_array($sample) || !isset($sample[0], $sample[1])) { return ''; }
        return str_replace(['%postname%', '%pagename%'], $sample[1], $sample[0]);
    }

    /** All 162 approved exports use Elementor HTML widgets. Unknown widgets fail closed. */
    public static function content(int $id): string {
        $saved = get_post_meta($id, '_elementor_data', true);
        if (is_string($saved) && trim($saved) !== '') {
            $nodes = json_decode($saved, true);
            if (!is_array($nodes)) { throw new RuntimeException('invalid_elementor'); }
            $parts = [];
            $walk = static function (array $nodes) use (&$walk, &$parts): void {
                foreach ($nodes as $node) {
                    if (!is_array($node)) { throw new RuntimeException('invalid_elementor_node'); }
                    if (($node['elType'] ?? '') === 'widget') {
                        if (($node['widgetType'] ?? '') !== 'html' || !is_string($node['settings']['html'] ?? null)) {
                            throw new RuntimeException('unsupported_elementor_widget');
                        }
                        $parts[] = $node['settings']['html'];
                    }
                    if (isset($node['elements'])) {
                        if (!is_array($node['elements'])) { throw new RuntimeException('invalid_elementor_children'); }
                        $walk($node['elements']);
                    }
                }
            };
            $walk($nodes); $html = implode("\n", $parts);
        } else { $html = wpautop((string)get_post($id)->post_content); }
        if (strlen($html) > 2*1024*1024 || !class_exists('DOMDocument')) { throw new RuntimeException('unsupported_article_html'); }
        $previous = libxml_use_internal_errors(true);
        try {
            $dom = new DOMDocument();
            if (!$dom->loadHTML('<?xml encoding="UTF-8"><!DOCTYPE html><html><body>'.$html.'</body></html>', LIBXML_NONET|LIBXML_NOERROR|LIBXML_NOWARNING)) { throw new RuntimeException('invalid_article_html'); }
            $xp = new DOMXPath($dom);
            foreach ($xp->query('//script|//style|//template|//noscript|//*[@hidden]|//*[@aria-hidden="true"]') as $node) { $node->parentNode->removeChild($node); }
            // Related cards can also use <article>; select the one enclosing article.
            $articles = $xp->query('//article[not(ancestor::article)]');
            if ($articles->length !== 1) { throw new RuntimeException('ambiguous_article'); }
            $html = $dom->saveHTML($articles->item(0));
            if (!is_string($html) || trim(wp_strip_all_tags($html)) === '' || preg_match('~\[(?:zp_|zaprojektowani_)~', $html)) { throw new RuntimeException('empty_or_dynamic_article'); }
            return $html;
        } finally { libxml_clear_errors(); libxml_use_internal_errors($previous); }
    }

    public static function input($values, $raw_id) {
        if (!self::$active || self::$failure !== '') { return $values; }
        $ids = self::ids(); $id = (int)$raw_id;
        if (!is_array($values) || !isset($ids[self::$offset]) || $id !== $ids[self::$offset]) { self::$failure = 'unexpected_post'; return $values; }
        try {
            self::refresh($id);
            if (!self::eligible($id)) { throw new RuntimeException('source_scope_changed'); }
            $before = self::fingerprint($id); $url = self::intended_url($id);
            $origin = wp_parse_url(home_url('/')); $parsed = wp_parse_url($url);
            if (!is_array($parsed) || ($parsed['scheme'] ?? '') !== ($origin['scheme'] ?? '')
                || ($parsed['host'] ?? '') !== ($origin['host'] ?? '') || isset($parsed['query']) || isset($parsed['fragment'])
                || empty($parsed['path']) || strpos($url, '%') !== false) { throw new RuntimeException('invalid_intended_url'); }
            $values['content'] = self::content($id); $values['url'] = urldecode($url);
            // Rebuild with the official replacements after the stable fresh read. The
            // earlier native values could have preceded a concurrent metadata edit.
            if (!class_exists('RankMath\\Helper')) { throw new RuntimeException('missing_native_replacements'); }
            $post = get_post($id);
            $values['title'] = \RankMath\Helper::replace_vars('%seo_title%', $post);
            $values['description'] = \RankMath\Helper::replace_vars('%seo_description%', $post);
            $values['keywords'] = array_map('trim', explode(',', (string)get_post_meta($id, 'rank_math_focus_keyword', true)));
            $values['keyword'] = $values['keywords'][0];
            $values['hasContentAi'] = !empty(get_post_meta($id, 'rank_math_contentai_score', true));
            unset($values['thumbnail'], $values['thumbnailAlt']);
            $thumb = (int)get_post_thumbnail_id($id);
            if ($thumb) {
                $values['thumbnail'] = get_the_post_thumbnail_url($id);
                $values['thumbnailAlt'] = (string)get_post_meta($thumb, '_wp_attachment_image_alt', true);
            }
            if (!is_string($values['title'] ?? null) || !is_string($values['description'] ?? null)
                || !is_array($values['keywords'] ?? null) || !is_string($values['keyword'] ?? null)
                || trim($values['keyword']) === '') { throw new RuntimeException('invalid_native_metadata'); }
            self::refresh($id);
            if (!self::eligible($id) || !hash_equals($before, self::fingerprint($id))) { throw new RuntimeException('source_changed'); }
            self::$accepted[$id] = ['values'=>$values,'source_hash'=>$before,
                'input_hash'=>hash('sha256', (string)wp_json_encode($values))];
        } catch (Throwable $e) { self::$failure = $e->getMessage(); }
        return $values;
    }

    public static function finish($data, $request = null) {
        if (!self::$active) { return $data; }
        self::$active = false;
        if (!self::authorized_request($request, '/rankmath/v1/toolsAction') || self::$failure !== '' || !self::$queried) {
            return new WP_Error('zp_article_rm_input', 'Zatrzymano analizę artykułów: '.(self::$failure ?: 'niepotwierdzony zakres zapytania').'. Żaden nowy wynik tej partii nie został zapisany.', ['status'=>409]);
        }
        if (is_wp_error($data)) { return $data; }
        if (self::$offset === 162 && $data === 'complete') { return 'complete'; }
        if (!is_array($data) || count($data) !== 1 || count(self::$accepted) !== 1) {
            return new WP_Error('zp_article_rm_batch', 'Zatrzymano analizę: niepełna partia. Wcześniejsze wyniki zachowano.', ['status'=>409]);
        }
        $id = array_key_first(self::$accepted); $proof = self::$accepted[$id];
        if (!isset($data[$id]) || $data[$id] !== $proof['values']) { return new WP_Error('zp_article_rm_data', 'Dane analizy zostały zmienione przez inny filtr.', ['status'=>409]); }
        $state = self::session();
        $state['pending'] = ['id'=>$id,'source_hash'=>$proof['source_hash'],'input_hash'=>$proof['input_hash'],'at'=>time()];
        update_user_meta(get_current_user_id(), self::USER_KEY, $state);
        return $data;
    }

    /** The official endpoint remains the sole writer; refuse stale or out-of-scope input. */
    public static function guard_score($result, $server, $request) {
        if (!$request instanceof WP_REST_Request || $request->get_method() !== 'POST'
            || $request->get_route() !== '/rankmath/v1/updateSeoScore'
            || $request->get_header(self::HEADER) === '') { return $result; }
        if (!self::authorized_request($request, '/rankmath/v1/updateSeoScore')) {
            return new WP_Error('zp_article_rm_session', 'Tryb artykułów wygasł lub zmienił się jego zakres. Uruchom nowy przebieg.', ['status'=>403]);
        }
        if ($result !== null) { return $result; }
        $state = self::session(); $pending = $state['pending'] ?? []; $scores = $request->get_param('postScores');
        $id = (int)($pending['id'] ?? 0);
        if (!is_array($scores) || count($scores) !== 1 || !array_key_exists($id, $scores)
            || !in_array($id, self::ids(), true) || ($pending['at'] ?? 0) < time()-300
            || !is_string($pending['input_hash'] ?? null) || strlen($pending['input_hash']) !== 64) {
            return new WP_Error('zp_article_rm_score_scope', 'Brak aktualnego potwierdzonego wejścia dla wyniku Rank Math.', ['status'=>409]);
        }
        $score = $scores[$id];
        if ((!is_int($score) && !(is_string($score) && ctype_digit($score))) || (int)$score < 0 || (int)$score > 100) {
            return new WP_Error('zp_article_rm_score_value', 'Nieprawidłowy wynik oficjalnego analizatora.', ['status'=>409]);
        }
        self::refresh($id);
        if (!self::eligible($id) || !hash_equals((string)$pending['source_hash'], self::fingerprint($id))) {
            return new WP_Error('zp_article_rm_source_changed', 'Treść lub SEO artykułu zmieniły się po analizie. Wynik nie został zapisany.', ['status'=>409]);
        }
        return $result;
    }

    public static function saved_score($response, $handler, $request) {
        if (!self::authorized_request($request, '/rankmath/v1/updateSeoScore') || is_wp_error($response)) { return $response; }
        if ($response instanceof WP_REST_Response && $response->get_status() >= 300) { return $response; }
        $state = self::session(); $pending = $state['pending'] ?? []; $id = (int)($pending['id'] ?? 0);
        $scores = $request->get_param('postScores');
        if (!$id || !is_array($scores) || count($scores) !== 1 || !array_key_exists($id, $scores)) { return $response; }
        wp_cache_delete($id, 'post_meta'); $stored = get_post_meta($id, 'rank_math_seo_score', true);
        if ((string)$stored !== (string)$scores[$id]) { return $response; }
        $state['results'][$id] = ['score'=>(int)$stored,'source_hash'=>$pending['source_hash'],'input_hash'=>$pending['input_hash'],'at'=>time()];
        $state['pending'] = []; $state['next'] = (int)$state['next']+1;
        update_user_meta(get_current_user_id(), self::USER_KEY, $state);
        return $response;
    }
}

function zp_article_seo_rankmath_controls_20261015(array $ids): void {
    ZP_Article_RankMath_Tools_20261015::controls($ids);
}
ZP_Article_RankMath_Tools_20261015::register();
