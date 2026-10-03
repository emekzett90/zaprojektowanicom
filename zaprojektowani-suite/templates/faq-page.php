<?php
if (!defined('ABSPATH')) { exit; }

$data = function_exists('zp_suite_faq_get') ? zp_suite_faq_get() : ['categories'=>[], 'items'=>[]];
$categories_all = is_array($data['categories'] ?? null) ? $data['categories'] : [];
$items_all = is_array($data['items'] ?? null) ? $data['items'] : [];

$categories = array_values(array_filter($categories_all, function($c){
  return is_array($c) && (!isset($c['visible']) || (string)$c['visible'] !== '0') && !empty($c['slug']);
}));
$items = array_values(array_filter($items_all, function($i){
  return is_array($i) && (!isset($i['visible']) || (string)$i['visible'] !== '0') && !empty($i['q']);
}));

$items_by_cat = [];
foreach ($items as $item) {
  $cat = sanitize_title($item['category'] ?? '');
  if (!isset($items_by_cat[$cat])) $items_by_cat[$cat] = [];
  $items_by_cat[$cat][] = $item;
}

$visible_categories = [];
foreach ($categories as $cat) {
  $slug = sanitize_title($cat['slug'] ?? '');
  if ($slug && !empty($items_by_cat[$slug])) $visible_categories[] = $cat;
}

$question_count = count($items);
$category_count = count($visible_categories);

$cluster_links = [
  'strony' => [
    'service_url' => home_url('/strony-internetowe-katowice/'),
    'service_label' => 'Projektowanie stron',
    'knowledge_url' => home_url('/category/strony-internetowe/'),
    'knowledge_label' => 'Poradniki o stronach',
  ],
  'sklepy' => [
    'service_url' => home_url('/sklepy-internetowe-katowice/'),
    'service_label' => 'Sklepy WooCommerce',
    'knowledge_url' => home_url('/category/sklepy-internetowe/'),
    'knowledge_label' => 'Poradniki o sklepach',
  ],
  'branding' => [
    'service_url' => home_url('/logo-branding-katowice/'),
    'service_label' => 'Logo i branding',
    'knowledge_url' => home_url('/category/logo-branding/'),
    'knowledge_label' => 'Poradniki o brandingu',
  ],
  'seo' => [
    'service_url' => home_url('/wiedza/'),
    'service_label' => 'Baza wiedzy',
    'knowledge_url' => home_url('/category/seo/'),
    'knowledge_label' => 'Poradniki SEO',
  ],
  'reklamy' => [
    'service_url' => home_url('/kampanie-reklamowe/'),
    'service_label' => 'Kampanie reklamowe',
    'knowledge_url' => home_url('/category/reklamy/'),
    'knowledge_label' => 'Poradniki o reklamach',
  ],
  'wspolpraca' => [
    'service_url' => home_url('/studio-wyceny/'),
    'service_label' => 'Studio Wyceny',
    'knowledge_url' => home_url('/realizacje/'),
    'knowledge_label' => 'Zobacz realizacje',
  ],
];

if (!function_exists('zp_faq_template_cluster_links')) {
  function zp_faq_template_cluster_links($slug, $cluster_links){
    $slug = sanitize_title($slug);
    return $cluster_links[$slug] ?? [
      'service_url' => home_url('/wiedza/'),
      'service_label' => 'Baza wiedzy',
      'knowledge_url' => home_url('/kontakt/'),
      'knowledge_label' => 'Kontakt',
    ];
  }
}
?>
<section class="zpFaqPage" data-zp-faq-page>
  <div class="zpFaqPage__heroBgWord" aria-hidden="true">faq</div>

  <div class="zpFaqPage__wrap">
    <nav class="zpFaqPage__breadcrumbs" aria-label="Ścieżka strony">
      <a href="<?php echo esc_url(home_url('/')); ?>">Strona główna</a>
      <span>/</span>
      <span>FAQ</span>
    </nav>

    <header class="zpFaqHero">
      <div class="zpFaqHero__copy">
        <div class="zpFaqPage__eyebrow">Centrum odpowiedzi</div>
        <h1>Najczęściej zadawane pytania</h1>
        <p class="zpFaqHero__lead">
          Odpowiedzi o stronach internetowych, sklepach WooCommerce, logo i brandingu, SEO, kampaniach reklamowych oraz współpracy z Zaprojektowani. Wybierz temat albo wyszukaj konkretne pytanie.
        </p>

        <div class="zpFaqHero__actions">
          <a class="zpFaqBtn zpFaqBtn--dark" href="#faq-topics">
            <span>Przejdź do tematów</span>
            <i data-lucide="arrow-down" aria-hidden="true"></i>
          </a>
          <a class="zpFaqBtn" href="<?php echo esc_url(home_url('/wiedza/')); ?>">
            <span>Otwórz bazę Wiedza</span>
            <i data-lucide="arrow-up-right" aria-hidden="true"></i>
          </a>
        </div>
      </div>

      <aside class="zpFaqHero__panel" aria-label="Wyszukaj odpowiedź">
        <div class="zpFaqHero__panelTop">
          <span class="zpFaqHero__panelIcon" aria-hidden="true"><i data-lucide="search"></i></span>
          <div>
            <span class="zpFaqHero__panelKicker">Szybka odpowiedź</span>
            <h2>Znajdź odpowiedź w kilka sekund.</h2>
          </div>
        </div>

        <label class="zpFaqHero__search" aria-label="Szukaj w FAQ">
          <i data-lucide="search" aria-hidden="true"></i>
          <input type="search" placeholder="Np. koszt strony, WooCommerce, logo, SEO…" data-faq-search autocomplete="off">
          <kbd>/</kbd>
        </label>

        <div class="zpFaqHero__stats" aria-label="Statystyki FAQ">
          <span><strong><?php echo esc_html($question_count); ?></strong><small>odpowiedzi</small></span>
          <span><strong><?php echo esc_html($category_count); ?></strong><small>tematów</small></span>
          <span><strong>24/7</strong><small>dostęp do wiedzy</small></span>
        </div>

        <p class="zpFaqHero__hint">
          Nie znalazłeś odpowiedzi? <a href="<?php echo esc_url(home_url('/studio-wyceny/')); ?>">Opisz projekt w Studio Wyceny</a>.
        </p>
      </aside>
    </header>

    <section class="zpFaqTopics" id="faq-topics" aria-labelledby="zpFaqTopicsTitle">
      <div class="zpFaqTopics__head">
        <div>
          <div class="zpFaqPage__eyebrow">Wybierz obszar</div>
          <h2 id="zpFaqTopicsTitle">Przejdź od razu do właściwego tematu.</h2>
        </div>
        <p>
          FAQ jest podzielone według decyzji, które najczęściej pojawiają się przed rozpoczęciem projektu i w trakcie współpracy.
        </p>
      </div>

      <div class="zpFaqTopics__grid">
        <?php foreach ($visible_categories as $cat): ?>
          <?php
            $slug = sanitize_title($cat['slug'] ?? '');
            $name = $cat['name'] ?? ($cat['title'] ?? $slug);
            $desc = wp_strip_all_tags($cat['desc'] ?? '');
            $icon = sanitize_text_field($cat['icon'] ?? 'circle-help');
            $count = count($items_by_cat[$slug] ?? []);
          ?>
          <a class="zpFaqTopicCard" href="#faq-<?php echo esc_attr($slug); ?>">
            <span class="zpFaqTopicCard__top">
              <span class="zpFaqTopicCard__icon"><i data-lucide="<?php echo esc_attr($icon); ?>" aria-hidden="true"></i></span>
              <span class="zpFaqTopicCard__count"><?php echo esc_html($count); ?> pytań</span>
            </span>
            <strong><?php echo esc_html($name); ?></strong>
            <span class="zpFaqTopicCard__desc"><?php echo esc_html(wp_trim_words($desc, 15, '…')); ?></span>
            <span class="zpFaqTopicCard__foot">Otwórz temat <i data-lucide="arrow-down-right" aria-hidden="true"></i></span>
          </a>
        <?php endforeach; ?>
      </div>
    </section>

    <section class="zpFaqWorkspace" aria-label="Odpowiedzi FAQ">
      <aside class="zpFaqWorkspace__side">
        <div class="zpFaqWorkspace__sideCard">
          <div class="zpFaqPage__eyebrow">Nawigacja FAQ</div>
          <h2>Wybierz temat</h2>
          <p>Przeskocz do sekcji albo użyj wyszukiwarki u góry strony.</p>

          <nav class="zpFaqWorkspace__toc" aria-label="Kategorie FAQ">
            <?php foreach ($visible_categories as $cat): ?>
              <?php
                $slug = sanitize_title($cat['slug'] ?? '');
                $name = $cat['name'] ?? ($cat['title'] ?? $slug);
                $count = count($items_by_cat[$slug] ?? []);
              ?>
              <a href="#faq-<?php echo esc_attr($slug); ?>">
                <span><?php echo esc_html($name); ?></span>
                <em><?php echo esc_html($count); ?></em>
              </a>
            <?php endforeach; ?>
          </nav>

          <div class="zpFaqWorkspace__miniCta">
            <span class="zpFaqWorkspace__miniIcon"><i data-lucide="wand-sparkles" aria-hidden="true"></i></span>
            <strong>Masz konkretny projekt?</strong>
            <p>Opisz zakres i szybciej przejdź od pytania do wyceny.</p>
            <a href="<?php echo esc_url(home_url('/studio-wyceny/')); ?>">Studio Wyceny <i data-lucide="arrow-up-right" aria-hidden="true"></i></a>
          </div>
        </div>
      </aside>

      <main class="zpFaqWorkspace__content" data-faq-content>
        <div class="zpFaqWorkspace__resultBar">
          <span><i data-lucide="list-filter" aria-hidden="true"></i> Odpowiedzi</span>
          <strong data-faq-counter aria-live="polite">Wszystkie pytania</strong>
        </div>

        <div class="zpFaqPage__noResults" data-faq-no-results>
          <strong>Nie znaleźliśmy takiego pytania.</strong>
          <span>Spróbuj krótszej frazy, np. „logo”, „sklep”, „SEO”, „płatność” albo przejdź do kontaktu.</span>
          <a href="<?php echo esc_url(home_url('/kontakt/')); ?>">Skontaktuj się z nami <i data-lucide="arrow-up-right" aria-hidden="true"></i></a>
        </div>

        <?php foreach ($visible_categories as $cat): ?>
          <?php
            $slug = sanitize_title($cat['slug'] ?? '');
            $cat_items = $items_by_cat[$slug] ?? [];
            if (!$cat_items) continue;
            $links = zp_faq_template_cluster_links($slug, $cluster_links);
          ?>
          <section class="zpFaqCat" id="faq-<?php echo esc_attr($slug); ?>" data-faq-category="<?php echo esc_attr($slug); ?>">
            <header class="zpFaqCat__head">
              <span class="zpFaqCat__icon"><i data-lucide="<?php echo esc_attr(sanitize_text_field($cat['icon'] ?? 'circle-help')); ?>" aria-hidden="true"></i></span>
              <div class="zpFaqCat__copy">
                <span class="zpFaqCat__kicker"><?php echo esc_html(count($cat_items)); ?> odpowiedzi</span>
                <h2><?php echo esc_html($cat['title'] ?? ($cat['name'] ?? $slug)); ?></h2>
                <p><?php echo wp_kses_post($cat['desc'] ?? ''); ?></p>
                <div class="zpFaqCat__links">
                  <a href="<?php echo esc_url($links['service_url']); ?>"><?php echo esc_html($links['service_label']); ?> <i data-lucide="arrow-up-right" aria-hidden="true"></i></a>
                  <a href="<?php echo esc_url($links['knowledge_url']); ?>"><?php echo esc_html($links['knowledge_label']); ?> <i data-lucide="book-open" aria-hidden="true"></i></a>
                </div>
              </div>
            </header>

            <div class="zpFaqCat__items">
              <?php foreach ($cat_items as $item): ?>
                <details class="zpFaqItem" data-faq-item data-category="<?php echo esc_attr($slug); ?>" <?php echo (!empty($item['open']) && (string)$item['open'] !== '0') ? 'open' : ''; ?>>
                  <summary>
                    <span><?php echo esc_html($item['q'] ?? ''); ?></span>
                    <span class="zpFaqItem__plus" aria-hidden="true"></span>
                  </summary>
                  <div class="zpFaqItem__body"><?php echo wp_kses_post(apply_filters('zp_suite_faq_answer', (string) ($item['a'] ?? ''), $item)); ?></div>
                </details>
              <?php endforeach; ?>
            </div>
          </section>
        <?php endforeach; ?>
      </main>
    </section>

    <section class="zpFaqExplore" aria-labelledby="zpFaqExploreTitle">
      <div>
        <div class="zpFaqPage__eyebrow">Zobacz więcej</div>
        <h2 id="zpFaqExploreTitle">FAQ to punkt startowy. Dalej możesz wejść głębiej.</h2>
        <p>Jeżeli chcesz porównać zakres, koszty i proces, przejdź do poradników albo zobacz gotowe realizacje.</p>
      </div>
      <div class="zpFaqExplore__links">
        <a href="<?php echo esc_url(home_url('/wiedza/')); ?>"><i data-lucide="book-open-text" aria-hidden="true"></i><span><strong>Baza Wiedza</strong><small>Poradniki i decyzje projektowe</small></span><i data-lucide="arrow-up-right" aria-hidden="true"></i></a>
        <a href="<?php echo esc_url(home_url('/realizacje/')); ?>"><i data-lucide="layout-grid" aria-hidden="true"></i><span><strong>Realizacje</strong><small>Strony, sklepy i branding</small></span><i data-lucide="arrow-up-right" aria-hidden="true"></i></a>
        <a href="<?php echo esc_url(home_url('/studio-wyceny/')); ?>"><i data-lucide="sparkles" aria-hidden="true"></i><span><strong>Studio Wyceny</strong><small>Dobierz zakres projektu</small></span><i data-lucide="arrow-up-right" aria-hidden="true"></i></a>
      </div>
    </section>

    <section class="zpFaqFinalCta">
      <div>
        <span class="zpFaqFinalCta__kicker">Nie znalazłeś odpowiedzi?</span>
        <h2>Opisz, czego potrzebuje Twoja firma.</h2>
        <p>Powiedz nam, czy chodzi o stronę, sklep, logo, branding, SEO czy kampanię. Dobierzemy właściwy następny krok.</p>
      </div>
      <div class="zpFaqFinalCta__actions">
        <a class="zpFaqBtn zpFaqBtn--light" href="<?php echo esc_url(home_url('/studio-wyceny/')); ?>">Otwórz Studio Wyceny <i data-lucide="arrow-up-right" aria-hidden="true"></i></a>
        <a class="zpFaqBtn zpFaqBtn--ghostLight" href="<?php echo esc_url(home_url('/kontakt/')); ?>">Kontakt <i data-lucide="message-circle" aria-hidden="true"></i></a>
      </div>
    </section>
  </div>
</section>
