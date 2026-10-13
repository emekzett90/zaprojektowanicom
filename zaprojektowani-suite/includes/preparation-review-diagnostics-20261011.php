<?php
/** Private, read-only diagnosis of the ten reviewed native drafts. */
if (!defined('ABSPATH')) { exit; }

final class ZP_Preparation_Review_Diagnostics_20261011 {
    private const IDS = [5943,5951,5955,5957,5959,5961,5963,5965,5967,5969];
    private const QUALITY_CODES = ['draft_changed','actor_permissions','metadata_incomplete','draft_invalid','draft_links','draft_images','article_word_count','article_faq','canonical_failed','invalid_template','invalid_template_css'];

    public static function allowed(int $id = 0): bool {
        return class_exists('ZP_Content_Preparation_20261010') && current_user_can('manage_options') && current_user_can('edit_posts')
            && (!$id || (in_array($id,self::IDS,true) && current_user_can('edit_post',$id)));
    }
    private static function hash_value($value): ?string {
        return is_string($value) && preg_match('/^[a-f0-9]{64}$/D',$value) === 1 ? $value : null;
    }
    private static function same_hash(?string $a, ?string $b): ?bool { return $a === null || $b === null ? null : hash_equals($a,$b); }
    private static function type($value): string {
        if ($value === null) return 'null';
        return in_array(gettype($value),['integer','string','boolean','double','array','object'],true) ? gettype($value) : 'other';
    }
    private static function url($url): ?string {
        if (!is_string($url)) return null;
        $u = wp_parse_url($url); $origin = wp_parse_url(home_url('/'));
        if (!is_array($u) || !is_array($origin) || ($u['scheme']??'') !== 'https' || strtolower($u['host']??'') !== strtolower($origin['host']??'')
            || array_intersect(['user','pass','port','query','fragment'],array_keys($u)) || !is_string($u['path']??null) || !str_starts_with($u['path'],'/') || str_starts_with($u['path'],'//')) return null;
        return $url;
    }
    /** Returns only booleans, safe enum labels, native IDs and canonical URLs; no private hashes or job IDs. */
    public static function rows(): array {
        if (!self::allowed()) return [];
        wp_cache_delete(ZP_Content_Preparation_20261010::DATA,'options');
        wp_cache_delete('notoptions','options');
        $data = get_option(ZP_Content_Preparation_20261010::DATA,[]);
        $jobs = is_array($data) && is_array($data['jobs']??null) && count($data['jobs']) <= 500 ? $data['jobs'] : null;
        $rows = [];
        foreach (self::IDS as $id) {
            if (!self::allowed($id)) { $rows[]=['post_id'=>$id,'access'=>'forbidden']; continue; }
            wp_cache_delete($id,'posts'); wp_cache_delete($id,'post_meta');
            $post = get_post($id);
            $status = $post && in_array($post->post_status,['draft','future','publish','pending','private','trash','auto-draft'],true) ? $post->post_status : 'missing';
            $row = ['post_id'=>$id,'access'=>'allowed','native_status'=>$status,'quality'=>'not_checked','review_present'=>null,'review_type'=>'unknown','review_positive'=>null,
                'job_present'=>null,'job_key_matches'=>null,'job_post_id_matches'=>null,'job_marker_matches'=>null,'batch_marker_matches'=>null,
                'job_hash_valid'=>null,'meta_hash_valid'=>null,'current_hash_valid'=>null,'job_equals_meta'=>null,'job_equals_current'=>null,'meta_equals_current'=>null,
                'canonical_matches'=>null,'canonical_url'=>null,'source_post_id'=>null];
            if (!$post || $post->post_type !== 'post') { $row['quality']='missing_post'; $rows[]=$row; continue; }
            if ($jobs === null) { $row['quality']='invalid_job_inventory'; $rows[]=$row; continue; }
            $matches = [];
            foreach ($jobs as $key=>$job) if (is_array($job) && ($job['post_id']??null) === $id) $matches[]=['key'=>$key,'job'=>$job];
            $row['job_present'] = count($matches) > 0;
            if (count($matches) !== 1) { $row['quality']=count($matches) ? 'ambiguous_job' : 'missing_job'; $rows[]=$row; continue; }
            $entry=$matches[0]; $job=$entry['job']; $prefix=ZP_Content_Preparation_20261010::PREFIX;
            $row['review_present']=array_key_exists('reviewed_at',$job); $row['review_type']=self::type($job['reviewed_at']??null);
            $row['review_positive']=is_int($job['reviewed_at']??null) ? $job['reviewed_at'] > 0 : null;
            $row['job_key_matches']=is_string($entry['key']) && is_string($job['id']??null) && $entry['key'] === $job['id'];
            $row['job_post_id_matches']=($job['post_id']??null) === $id;
            $row['job_marker_matches']=is_string($job['id']??null) && get_post_meta($id,$prefix.'job',true) === $job['id'];
            $row['batch_marker_matches']=is_string($job['batch']??null) && get_post_meta($id,$prefix.'batch',true) === $job['batch'];
            if (is_int($job['source_id']??null) && $job['source_id']>0) $row['source_post_id']=$job['source_id'];
            $jobHash=self::hash_value($job['ready_hash']??null); $metaHash=self::hash_value(get_post_meta($id,$prefix.'ready_hash',true)); $currentHash=null;
            try { $currentHash=self::hash_value(ZP_Content_Preparation_20261010::fingerprint($id)); } catch (Throwable $ignored) {}
            $row['job_hash_valid']=$jobHash !== null; $row['meta_hash_valid']=$metaHash !== null; $row['current_hash_valid']=$currentHash !== null;
            $row['job_equals_meta']=self::same_hash($jobHash,$metaHash); $row['job_equals_current']=self::same_hash($jobHash,$currentHash); $row['meta_equals_current']=self::same_hash($metaHash,$currentHash);
            try { $canonical=self::url(ZP_Content_Preparation_20261010::canonical($id)); $row['canonical_url']=$canonical; $row['canonical_matches']=$canonical === null ? null : get_post_meta($id,'rank_math_canonical_url',true) === $canonical; } catch (Throwable $ignored) { $row['canonical_matches']=null; }
            if ($post->post_password !== '') $row['quality']='protected_post';
            elseif (!in_array($status,['draft','future'],true)) $row['quality']='not_a_draft';
            elseif (!$row['job_key_matches'] || !$row['job_marker_matches'] || !$row['batch_marker_matches']) $row['quality']='ownership_changed';
            else {
                try { ZP_Content_Preparation_20261010::quality($id,$job,false,$status==='future'); $row['quality']='pass'; }
                catch (Throwable $error) { $row['quality']=$error instanceof RuntimeException && in_array($error->getMessage(),self::QUALITY_CODES,true) ? $error->getMessage() : 'quality_unavailable'; }
            }
            $rows[]=$row;
        }
        return $rows;
    }
    private static function flag(?bool $value): string { return $value === null ? '—' : ($value ? 'tak' : 'nie'); }
    private static function quality_label(string $code): string {
        return ['pass'=>'Kontrola treści i metadanych: poprawna','draft_changed'=>'Stan lub własność wpisu wymaga sprawdzenia','actor_permissions'=>'Brak uprawnień do wpisu','metadata_incomplete'=>'Sprawdź metadane SEO lub kanoniczny adres','draft_invalid'=>'Sprawdź zgodność HTML widgetu i treści wpisu','draft_links'=>'Sprawdź odnośniki i aktualny stan usług','draft_images'=>'Sprawdź dwa obrazy i zdjęcie wyróżniające','article_word_count'=>'Sprawdź długość tekstu','article_faq'=>'Sprawdź odpowiedzi FAQ i zapisane metadane FAQ','canonical_failed'=>'Nie potwierdzono docelowego adresu','invalid_template'=>'Sprawdź źródłowy szablon','invalid_template_css'=>'Sprawdź źródłowy CSS szablonu','missing_post'=>'Brak wpisu','invalid_job_inventory'=>'Nie potwierdzono stanu kolejki','ambiguous_job'=>'Więcej niż jedno zadanie dla wpisu','missing_job'=>'Brak zadania dla wpisu','protected_post'=>'Wpis jest chroniony hasłem','not_a_draft'=>'Wpis ma inny stan niż szkic lub zaplanowany','ownership_changed'=>'Znaczniki własności nie są zgodne','quality_unavailable'=>'Kontrola nie zwróciła znanego wyniku'][$code]??'Kontrola wymaga sprawdzenia';
    }
    public static function render(): void {
        if (!is_admin() || !self::allowed() || ($_SERVER['REQUEST_METHOD']??'') !== 'GET' || !is_string($_GET['page']??null) || $_GET['page'] !== ZP_Content_Preparation_20261010::PAGE
            || (function_exists('wp_doing_ajax') && wp_doing_ajax()) || (function_exists('wp_doing_cron') && wp_doing_cron()) || (defined('REST_REQUEST') && REST_REQUEST)) return;
        echo '<section class="zpAdmin20__card" style="margin:16px 0"><details><summary>Kontrola zapisanej gotowości dziesięciu nowych artykułów</summary><p>Odczyt bieżącego stanu WordPressa. Kontrola treści, zapis zatwierdzenia i zgodność wersji są osobnymi wynikami. Ten odczyt niczego nie zatwierdza ani nie zmienia.</p><div class="zpAdmin20__tableWrap"><table class="zpAdmin20__table"><thead><tr><th>Wpis / stan / URL</th><th>Zapis zatwierdzenia</th><th>Zgodność wersji</th><th>Własność i kontrola</th></tr></thead><tbody>';
        foreach (self::rows() as $row) {
            echo '<tr><td>#'.(int)$row['post_id'];
            if (($row['access']??'') !== 'allowed') { echo '</td><td colspan="3">Brak uprawnień do wpisu</td></tr>'; continue; }
            echo '<br>'.esc_html($row['native_status']); if ($row['canonical_url']) echo '<br><a href="'.esc_url($row['canonical_url']).'" target="_blank" rel="noopener">'.esc_html($row['canonical_url']).'</a>';
            echo '</td><td>Timestamp istnieje: '.self::flag($row['review_present']).'<br>Typ: '.esc_html($row['review_type']).'<br>Dodatnia liczba całkowita: '.self::flag($row['review_positive']).'</td><td>Hash zadania poprawny: '.self::flag($row['job_hash_valid']).'<br>Hash meta poprawny: '.self::flag($row['meta_hash_valid']).'<br>Hash aktualny poprawny: '.self::flag($row['current_hash_valid']).'<br>Zadanie = meta: '.self::flag($row['job_equals_meta']).'<br>Zadanie = aktualna wersja: '.self::flag($row['job_equals_current']).'<br>Meta = aktualna wersja: '.self::flag($row['meta_equals_current']).'</td><td>ID wpisu zgodny: '.self::flag($row['job_post_id_matches']).'<br>Klucz zadania zgodny: '.self::flag($row['job_key_matches']).'<br>Znacznik zadania zgodny: '.self::flag($row['job_marker_matches']).'<br>Znacznik partii zgodny: '.self::flag($row['batch_marker_matches']).'<br>Kanoniczny URL zgodny: '.self::flag($row['canonical_matches']);
            if ($row['source_post_id']) echo '<br>Źródłowy wpis: #'.(int)$row['source_post_id'];
            echo '<p>'.esc_html(self::quality_label($row['quality'])).'</p></td></tr>';
        }
        echo '</tbody></table></div></details></section>';
    }
    public static function register(): void { add_action('admin_notices',[self::class,'render'],51); }
}
