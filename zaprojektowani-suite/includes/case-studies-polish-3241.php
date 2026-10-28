<?php
/** 3.2.41: native portfolio connection and explicitly approved case-study reviews. */
if (!defined('ABSPATH')) { exit; }

function zp_cs41_imoje_url(): string {
    $uploads = wp_upload_dir(null, false);
    $file = '/zp-suite-assets/imoje-official.png';
    if (empty($uploads['error']) && is_readable($uploads['basedir'] . $file)) {
        return $uploads['baseurl'] . $file;
    }
    // Logo published by imoje's official WordPress.org plugin, not a drawn substitute.
    return 'https://ps.w.org/imoje/assets/icon-256x256.png?rev=2540032';
}

/** Copy the official provider image locally only in admin; never fetch while rendering pages. */
function zp_cs41_cache_provider_logo(): void {
    if (!current_user_can('manage_options')) { return; }
    $u = wp_upload_dir(null, false);
    if (!empty($u['error'])) { return; }
    $target = $u['basedir'] . '/zp-suite-assets/imoje-official.png';
    if (is_readable($target) || (int) get_option('zp_cs41_logo_next_try', 0) > time()) { return; }
    update_option('zp_cs41_logo_next_try', time() + DAY_IN_SECONDS, false);
    $response = wp_safe_remote_get('https://ps.w.org/imoje/assets/icon-256x256.png?rev=2540032', [
        'timeout'=>5, 'redirection'=>2, 'limit_response_size'=>524288,
    ]);
    if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) { return; }
    $bytes = wp_remote_retrieve_body($response);
    if (!is_string($bytes) || strlen($bytes) < 100 || !function_exists('getimagesizefromstring')) { return; }
    $image = @getimagesizefromstring($bytes);
    if (!$image || ($image['mime'] ?? '') !== 'image/png' || $image[0] > 2048 || $image[1] > 2048) { return; }
    if (!wp_mkdir_p(dirname($target))) { return; }
    $temp = $target . '.tmp';
    if (@file_put_contents($temp, $bytes, LOCK_EX) !== false) {
        if (!@rename($temp, $target)) { @unlink($temp); }
    }
}
add_action('admin_init', 'zp_cs41_cache_provider_logo', 115);

function zp_cs41_index_url(string $lang = 'pl'): string {
    $page = get_page_by_path('case-studies', OBJECT, 'page');
    if (!$page || get_post_status($page) !== 'publish') { return ''; }
    return zp_cs18_url(get_permalink($page), $lang);
}

function zp_cs41_portfolio_bridge(): string {
    $lang = function_exists('zpl_language') && zpl_language() === 'en' ? 'en' : 'pl';
    $en = $lang === 'en'; $url = zp_cs41_index_url($lang);
    if ($url === '' || !function_exists('zp_cs18_data') || !function_exists('zp_cs18_case_public')) { return ''; }
    $data = zp_cs18_data();
    $summaries = $en ? [
        'siemianowski' => ['Law firm', 'Branding, website and a custom system for publishing administrator sale offers.'],
        'apartament-piekna' => ['Beauty & aesthetics', 'A complete brand, custom appointment requests and ongoing SEO.'],
        'altiora-linea' => ['Interiors & B2B', 'A premium website and product catalogue in Polish, English and Italian.'],
        'swiat-grilli' => ['E-commerce', 'Branding and a WooCommerce store, from product selection to payment.'],
    ] : [
        'siemianowski' => ['Kancelaria', 'Branding, strona i autorski system publikowania ofert sprzedaży syndyka.'],
        'apartament-piekna' => ['Beauty i estetyka', 'Pełna marka, własny system zapisów na wizyty i rozwój widoczności w Google.'],
        'altiora-linea' => ['Wnętrza i B2B', 'Strona premium i katalog produktów w języku polskim, angielskim i włoskim.'],
        'swiat-grilli' => ['E-commerce', 'Branding i sklep WooCommerce — od wyboru produktu do płatności.'],
    ];
    $arrow = zp_cs18_arrow(); $cards = '';
    foreach ($summaries as $key => $summary) {
        if (!zp_cs18_case_public($key) || empty($data[$key])) { continue; }
        $case = $data[$key];
        $path = $case[$en ? 'en_path' : 'path'] ?? '';
        if ($path === '' || empty($case['brand'])) { continue; }
        $asset = $case['gallery'][0] ?? [];
        $src = is_array($asset) ? zp_cs18_asset($asset) : '';
        $image = '';
        if ($src !== '') {
            $dimensions = !empty($asset['width']) && !empty($asset['height']) ? ' width="'.(int)$asset['width'].'" height="'.(int)$asset['height'].'"' : '';
            // The adjacent project name labels this linked preview; keep its image decorative.
            $image = '<span class="zpCS41Bridge__media"><img src="'.esc_url($src).'" alt=""'.$dimensions.' loading="lazy" decoding="async"></span>';
        }
        $cards .= '<li><a class="zpCS41Bridge__card" href="'.esc_url(zp_cs18_url($path, $lang)).'">'.$image.'<div class="zpCS41Bridge__body"><span class="zpCS41Bridge__tag">'.esc_html($summary[0]).'</span><h3>'.esc_html($case['brand']).'</h3><p>'.esc_html($summary[1]).'</p><span class="zpCS41Bridge__cardLink">'.esc_html($en ? 'Read the case study' : 'Poznaj cały projekt').$arrow.'</span></div></a></li>';
    }
    if ($cards === '') { return ''; }
    return '<section id="zpPortfolioCaseStudies" class="zpCS41Bridge" aria-labelledby="zpCS41BridgeTitle"><div class="zpCS41Bridge__inner"><div class="zpCS41Bridge__head"><div><p class="zpCS41Bridge__kicker">'.esc_html($en ? 'Case studies · Behind the projects' : 'Case studies · Projekty od środka').'</p><h2 id="zpCS41BridgeTitle">'.esc_html($en ? 'See the project.' : 'Zobacz projekt.').' <strong>'.esc_html($en ? 'Understand the decisions.' : 'Poznaj decyzje.').'</strong></h2></div><div class="zpCS41Bridge__intro"><p>'.esc_html($en ? 'The portfolio shows our work across different industries. These in-depth case studies take you through the brief, design choices and functions we developed.' : 'W portfolio pokazujemy przekrój naszych realizacji. W tych rozbudowanych case studies przechodzimy przez potrzeby firmy, decyzje projektowe i wdrożone funkcje.').'</p><a class="zpCS41Bridge__index" href="'.esc_url($url).'">'.esc_html($en ? 'Explore all case studies' : 'Przejdź do case studies').$arrow.'</a></div></div><ul class="zpCS41Bridge__grid" role="list">'.$cards.'</ul></div></section>';
}

function zp_cs41_review_rows(): array {
    $rows = get_option('zp_cs41_owner_reviews', []);
    return is_array($rows) ? $rows : [];
}

function zp_cs41_owner_review(string $key, string $lang = 'pl'): string {
    $rows = zp_cs41_review_rows(); $row = $rows[$key] ?? [];
    if (!is_array($row) || empty($row['approved']) || empty($row['quote']) || empty($row['author'])) { return ''; }
    $en = $lang === 'en';
    $quote = $en && !empty($row['quote_en']) ? $row['quote_en'] : $row['quote'];
    $quote_lang = $en && !empty($row['quote_en']) ? 'en' : 'pl';
    $role = $en && !empty($row['role_en']) ? $row['role_en'] : ($row['role'] ?? '');
    $source = !empty($row['source']) ? '<a class="zpCS41__reviewSource" href="'.esc_url($row['source']).'" target="_blank" rel="noopener">'.esc_html($en?'Read the source':'Zobacz źródło opinii').'</a>' : '';
    return '<section class="zpCS41__ownerReview"><div class="zpCS18__inner"><div class="zpCS41__reviewCard"><div>'.zp_cs18_eyebrow($en?'Client feedback':'Głos klienta').'<blockquote lang="'.esc_attr($quote_lang).'"><p>'.nl2br(esc_html($quote)).'</p></blockquote></div><div><p class="zpCS41__reviewSignature">'.esc_html($row['author']).'</p><span class="zpCS41__reviewRole">'.esc_html($role).'</span>'.$source.'</div></div></div></section>';
}

function zp_cs41_reviews_admin_page(): void {
    if (!current_user_can('manage_options')) { return; }
    $data = zp_cs18_data(); $rows = zp_cs41_review_rows();
    echo '<div class="wrap"><h1>Opinie właścicieli — case studies</h1><p>Po zatwierdzeniu opinia pojawia się bezpośrednio pod hero projektu. Podpis korzysta z tej samej pisanej czcionki co strona główna. Wprowadź wyłącznie prawdziwą wypowiedź i autora; puste lub niezatwierdzone sekcje nie są widoczne.</p>';
    if (isset($_GET['updated'])) { echo '<div class="notice notice-success"><p>Zapisano opinie.</p></div>'; }
    echo '<form method="post" action="'.esc_url(admin_url('admin-post.php')).'"><input type="hidden" name="action" value="zp_cs41_save_reviews">';
    wp_nonce_field('zp_cs41_save_reviews');
    foreach (['siemianowski','apartament-piekna','altiora-linea','swiat-grilli'] as $key) {
        $r = $rows[$key] ?? [];
        echo '<h2>'.esc_html($data[$key]['brand']).'</h2><table class="form-table"><tbody>';
        foreach (['quote'=>'Opinia PL (dosłowna treść)','quote_en'=>'Zatwierdzone tłumaczenie EN (opcjonalne)','author'=>'Autor podpisu','role'=>'Rola / firma PL','role_en'=>'Rola / firma EN','source'=>'Adres źródła (opcjonalny)'] as $field=>$label) {
            $id = 'cs41-'.$key.'-'.$field; $name = 'reviews['.$key.']['.$field.']';
            echo '<tr><th scope="row"><label for="'.esc_attr($id).'">'.esc_html($label).'</label></th><td>';
            if (strpos($field, 'quote') === 0) {
                echo '<textarea class="large-text" rows="4" id="'.esc_attr($id).'" name="'.esc_attr($name).'">'.esc_textarea($r[$field] ?? '').'</textarea>';
            } else {
                echo '<input class="regular-text" type="'.($field==='source'?'url':'text').'" id="'.esc_attr($id).'" name="'.esc_attr($name).'" value="'.esc_attr($r[$field] ?? '').'">';
            }
            echo '</td></tr>';
        }
        echo '<tr><th>Publikacja</th><td><label><input type="checkbox" name="reviews['.esc_attr($key).'][approved]" value="1" '.checked(!empty($r['approved']), true, false).'> Potwierdzam autentyczność wypowiedzi i zgodę na jej publikację z tym podpisem.</label></td></tr></tbody></table>';
    }
    submit_button('Zapisz opinie'); echo '</form></div>';
}
add_action('admin_menu', static function(): void {
    add_management_page('Opinie case studies','Opinie case studies','manage_options','zp-case-reviews','zp_cs41_reviews_admin_page');
});
add_action('admin_post_zp_cs41_save_reviews', static function(): void {
    if (!current_user_can('manage_options')) { wp_die('Brak uprawnień.', '', ['response'=>403]); }
    check_admin_referer('zp_cs41_save_reviews');
    $input = isset($_POST['reviews']) && is_array($_POST['reviews']) ? wp_unslash($_POST['reviews']) : [];
    $rows = [];
    foreach (['siemianowski','apartament-piekna','altiora-linea','swiat-grilli'] as $key) {
        $r = isset($input[$key]) && is_array($input[$key]) ? $input[$key] : [];
        $clean = [];
        foreach (['quote','quote_en','author','role','role_en','source'] as $field) {
            $value = isset($r[$field]) && is_scalar($r[$field]) ? (string)$r[$field] : '';
            $clean[$field] = $field==='source' ? esc_url_raw($value, ['https','http']) : (strpos($field,'quote')===0 ? sanitize_textarea_field($value) : sanitize_text_field($value));
        }
        $clean['approved'] = !empty($r['approved']) && $clean['quote']!=='' && $clean['author']!=='';
        $rows[$key] = $clean;
    }
    update_option('zp_cs41_owner_reviews', $rows, false);
    do_action('zp_html_cache_purge');
    wp_safe_redirect(admin_url('tools.php?page=zp-case-reviews&updated=1')); exit;
});

add_action('wp_enqueue_scripts', static function(): void {
    $key = class_exists('ZP_Case_Pages_20261018') ? ZP_Case_Pages_20261018::current_key() : '';
    if ($key !== '') {
        wp_enqueue_style('zp-case-studies-polish-3241', ZP_SUITE_URL.'assets/css/case-studies-polish-3241.css', ['zp-case-studies-20261018'], ZP_SUITE_VERSION);
        wp_enqueue_style('zp-case-studies-layout-3242', ZP_SUITE_URL.'assets/css/case-studies-layout-3242.css', ['zp-case-studies-polish-3241'], ZP_SUITE_VERSION);
        if ($key === 'index') { wp_enqueue_script('zp-case-navigation-3246', ZP_SUITE_URL.'assets/js/case-studies-navigation-3246.js', [], ZP_SUITE_VERSION, true); }
    }
    $path = trim((string)parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/');
    if (preg_match('~^(?:en/)?(?:realizacje|portfolio)/?$~D', $path)) {
        wp_enqueue_style('zp-case-portfolio-bridge-3241', ZP_SUITE_URL.'assets/css/case-portfolio-bridge-3241.css', [], ZP_SUITE_VERSION);
    }
}, 101);
