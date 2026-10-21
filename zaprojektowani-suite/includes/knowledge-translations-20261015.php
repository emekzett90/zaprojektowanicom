<?php
/** Reviewed English copy for the Knowledge index introduced in Suite 3.2.11. */
if (!defined('ABSPATH')) { exit; }

function zp_knowledge_translations_20261015($layers, $lang, $source): array {
    $layers = is_array($layers) ? $layers : [];
    if ($lang !== 'en' || $source !== '/wiedza/' || is_admin() || !is_singular('page')
        || !class_exists('ZPL\\Html')) { return $layers; }
    $id = (int)get_queried_object_id();
    $page = get_post($id);
    if (!$page instanceof WP_Post || $page->post_status !== 'publish' || $page->post_password !== ''
        || (string)wp_parse_url(get_permalink($id), PHP_URL_PATH) !== $source) { return $layers; }
    $copy = [
        'Wybierz obszar' => 'Choose a topic',
        'Jak korzystać z bazy wiedzy' => 'How to use the knowledge base',
        'Przewodnik po tematach' => 'Topic guide',
        'Poradniki i case studies' => 'Guides and case studies',
        'Wybierz obszar, aby zobaczyć powiązane poradniki i realizacje. Licznik przy temacie pokazuje, ile materiałów znajdziesz w kategorii.'
            => 'Choose a topic to browse related guides and projects. The number next to each topic shows how many articles are available in that category.',
        'Wybierz temat, który odpowiada temu, czego teraz potrzebujesz: strony, sklepu, brandingu, SEO albo kampanii. W każdym obszarze znajdziesz poradniki i praktyczne przykłady.'
            => 'Choose the topic that matches what you need right now: a website, an online store, branding, SEO or a campaign. Each topic includes guides and practical examples.',
        'Zacznij od wybranego tematu albo wpisz hasło w wyszukiwarce artykułów. Jeśli szukasz przykładu gotowego projektu, wybierz kategorię Case study lub przejdź do realizacji.'
            => 'Start with a topic or enter a phrase in the article search. To see an example of a completed project, choose the Case study category or browse our portfolio.',
    ];
    $map = [];
    foreach ($copy as $pl => $en) { $map[\ZPL\Html::norm($pl)] = $en; }
    // Dict always prepends the owner's manual overrides before these curated layers.
    array_unshift($layers, $map);
    return $layers;
}
add_filter('zpl_priority_dictionary', 'zp_knowledge_translations_20261015', PHP_INT_MAX, 3);
