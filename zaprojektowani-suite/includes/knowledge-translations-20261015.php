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
        // 3.2.65: editorial recommendations and the hero's practical project brief.
        // Keep numbered inline markers as well as individual text nodes: the
        // translator groups a heading containing <strong>/<em> into one segment.
        'Z praktyki naszego studia' => 'From our studio experience',
        'Co warto ustalić <1>przed pierwszą wyceną?</1>' => 'What should you decide <1>before requesting a quote?</1>',
        'Co warto ustalić' => 'What should you decide',
        'przed pierwszą wyceną?' => 'before requesting a quote?',
        'Budżet, zakres i priorytety. Pokazujemy, o co zapytać i co przygotować, zanim zlecisz stronę, sklep lub branding.'
            => 'Budget, scope and priorities. Find out what to ask and prepare before commissioning a website, online store or branding project.',
        'W centrum uwagi' => 'In focus',
        'Polecane wpisy z bazy wiedzy' => 'Recommended knowledge base articles',
        'Polecamy na początek' => 'Start here',
        'Zanim zaczniesz <1>projekt.</1>' => 'Before you start <1>your project.</1>',
        'Zanim zaczniesz' => 'Before you start',
        'projekt.' => 'your project.',
        'Porównaj budżet, wybierz zakres i sprawdź, co przygotować przed współpracą.'
            => 'Compare costs, choose your scope and find out what to prepare before we work together.',
        'Strona firmowa — co powinna zawierać?' => 'Business website: what should it include?',
        'Oferta, realizacje i kontakt — poznaj elementy przydatnej strony firmowej.'
            => 'Services, projects and contact details — the essentials of a useful business website.',
        'Ile kosztuje sklep WooCommerce?' => 'How much does a WooCommerce store cost?',
        'Zaplanuj budżet na sklep, produkty, płatności i potrzebne integracje.'
            => 'Plan your budget for the store, products, payments and integrations.',
        'Księga znaku czy pełny branding?' => 'Logo guidelines or full branding?',
        'Porównaj księgę znaku, brandbook i identyfikację. Wybierz potrzebny zakres.'
            => 'Compare logo guidelines, a brand book and a visual identity. Choose the scope you need.',
        'Ile kosztuje logo i identyfikacja?' => 'How much do a logo and visual identity cost?',
        'Porównaj zakres projektu, poprawki, pliki i zasady używania znaku.'
            => 'Compare the project scope, revisions, files and rules for using your logo.',
    ];
    $map = [];
    foreach ($copy as $pl => $en) { $map[\ZPL\Html::norm($pl)] = $en; }
    // Dict always prepends the owner's manual overrides before these curated layers.
    array_unshift($layers, $map);
    return $layers;
}
add_filter('zpl_priority_dictionary', 'zp_knowledge_translations_20261015', PHP_INT_MAX, 3);
