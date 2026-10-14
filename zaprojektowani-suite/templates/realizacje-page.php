<?php if (!defined('ABSPATH')) { exit; } ?>
<?php
$zp_real_projects = function_exists('zp_suite_realizacje_projects') ? zp_suite_realizacje_projects() : [];
$zp_real_categories = function_exists('zp_suite_realizacje_categories') ? zp_suite_realizacje_categories() : [];
$zp_real_count = count($zp_real_projects);

$zp_real_service_list = static function($value){
  if (is_array($value)) { return array_values(array_filter(array_map('trim', $value))); }
  return array_values(array_filter(array_map('trim', preg_split('/[|,]/', (string)$value))));
};
$zp_real_cat_label = static function($cat){
  $cat = (string)$cat;
  if (strpos($cat,'shop') !== false) { return 'Sklep internetowy'; }
  if (strpos($cat,'branding') !== false && strpos($cat,'web') !== false) { return 'Strona + branding'; }
  if (strpos($cat,'branding') !== false) { return 'Logo & branding'; }
  return 'Strona internetowa';
};
$zp_real_cat_icon = static function($cat){
  $cat = (string)$cat;
  if (strpos($cat,'shop') !== false) { return 'shopping-bag'; }
  if (strpos($cat,'branding') !== false && strpos($cat,'web') === false) { return 'pen-tool'; }
  if (strpos($cat,'branding') !== false) { return 'layers'; }
  return 'monitor';
};
$zp_real_project_slug = static function($project){
  return sanitize_title(($project['brand'] ?? '') . '-' . ($project['type'] ?? 'projekt'));
};

$zp_real_counts = ['web'=>0,'shop'=>0,'branding'=>0];
foreach ($zp_real_projects as $project) {
  $cat = (string)($project['cat'] ?? '');
  if (strpos($cat,'web') !== false) { $zp_real_counts['web']++; }
  if (strpos($cat,'shop') !== false) { $zp_real_counts['shop']++; }
  if (strpos($cat,'branding') !== false) { $zp_real_counts['branding']++; }
}

$zp_featured = [];
foreach ($zp_real_projects as $i => $project) {
  if (!empty($project['featured'])) { $zp_featured[] = ['i'=>$i,'p'=>$project]; }
}
if (count($zp_featured) < 3) {
  foreach ($zp_real_projects as $i => $project) {
    $exists = false;
    foreach ($zp_featured as $row) { if ((int)$row['i'] === (int)$i) { $exists = true; break; } }
    if (!$exists) { $zp_featured[] = ['i'=>$i,'p'=>$project]; }
    if (count($zp_featured) >= 3) { break; }
  }
}
$zp_featured = array_slice($zp_featured,0,3);

/* 3.0: two groups — websites and shops (3:2 screens) first, then logo and branding (square boards).
   Texts added in 3.0 carry their English version here, so /en/our-work/ is complete before the EN dictionary knows them. */
$zp_real_en = function_exists('zp_seo_plan_is_en') && zp_seo_plan_is_en();
$zp_t = static function($pl, $en) use ($zp_real_en){ return $zp_real_en ? $en : $pl; };
$zp_real_plural = static function($n, $one, $few, $many){
  $n = (int)$n; $n10 = $n % 10; $n100 = $n % 100;
  if ($n === 1) { return $one; }
  return ($n10 >= 2 && $n10 <= 4 && ($n100 < 12 || $n100 > 14)) ? $few : $many;
};
$zp_real_groups = ['web'=>[], 'brand'=>[]];
foreach ($zp_real_projects as $i => $project) {
  $cat = (string)($project['cat'] ?? 'web');
  $is_site = $cat === '' || strpos($cat,'web') !== false || strpos($cat,'shop') !== false;
  $zp_real_groups[$is_site ? 'web' : 'brand'][] = $i;
}
$zp_real_group_meta = [
  'web' => ['title'=>$zp_t('Strony i sklepy internetowe', 'Websites and online stores'), 'limit'=>4, 'more'=>$zp_t('Pokaż wszystkie strony i sklepy', 'Show all websites and stores')],
  'brand' => ['title'=>$zp_t('Logo i identyfikacja wizualna', 'Logo and visual identity'), 'limit'=>8, 'more'=>$zp_t('Pokaż wszystkie projekty logo', 'Show all logo projects')],
];
$zp_real_first = !empty($zp_featured[0]) ? $zp_featured[0] : null;
?>
<script id="zp-realizacje-cms-data">
window.zpRealizacjeProjects = <?php echo wp_json_encode($zp_real_projects, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
</script>
<section id="zpRealizacjePage" aria-labelledby="zpRealizacjeTitle">

<?php /* A div, not a header: the live heading check in ultimate-seo/ultimate.php skips headers outside main and would promote the featured h2 to a second H1. */ ?>
<div class="hero zpRealizacjePage__hero">
  <div class="wrap">
    <nav class="heroCrumbs" aria-label="Okruszki">
      <a href="<?php echo esc_url(home_url('/')); ?>">Strona główna</a>
      <i data-lucide="chevron-right" aria-hidden="true"></i>
      <span>Realizacje</span>
    </nav>

    <div class="heroTop">
      <div class="heroCopy">
        <span class="kick">Portfolio / case studies</span>
        <h1 id="zpRealizacjeTitle">Realizacje, które łączą <em>design, technologię i wynik.</em></h1>
        <p>Strony internetowe, sklepy WooCommerce i branding projektowane jako jeden system — od strategii i UX po wdrożenie, SEO i sprzedaż. Zobacz konkretne projekty, zakres prac i decyzje, które stały za efektem.</p>

        <?php if (function_exists('zp_suite_meta_verified_markup')) { echo zp_suite_meta_verified_markup('light', 'realizacje'); } ?>

        <div class="heroActions">
          <a class="heroBtn heroBtn--primary" href="#portfolio-realizacji"><span>Zobacz case studies</span><i data-lucide="arrow-down"></i></a>
          <a class="heroBtn" href="<?php echo esc_url(home_url('/studio-wyceny/')); ?>"><span>Wyceń podobny projekt</span><i data-lucide="arrow-up-right"></i></a>
        </div>

        <div class="heroTrust" aria-label="Opinie klientów">
          <a class="heroTrust__card" href="<?php echo esc_url(home_url('/opinie/')); ?>">
            <span class="heroTrust__logo">
              <img src="https://zaprojektowani.com/wp-content/uploads/2026/05/trustindex_logo_wieksze.webp" alt="Trustindex" loading="lazy" decoding="async">
            </span>
            <span class="heroTrust__meta">
              <strong>5.0 / 5</strong>
              <small>120 opinii klientów · zweryfikowane w Trustindex</small>
            </span>
            <span class="heroTrust__score">★★★★★</span>
          </a>
        </div>

      </div>

      <figure class="heroTeam" aria-label="Zespół Zaprojektowani">
        <div class="heroTeam__word" aria-hidden="true">realizacje</div>
        <div class="heroTeam__media">
          <img
            src="https://zaprojektowani.com/wp-content/uploads/2026/06/team_kontakt.webp"
            alt="Zespół Zaprojektowani — projektowanie stron internetowych, sklepów i identyfikacji wizualnej"
            loading="eager"
            decoding="async"
            fetchpriority="high"
          >
        </div>
      </figure>
    </div>
  </div>
</div>

<section class="spot" id="portfolio-realizacji" aria-label="Wyróżnione realizacje">
  <div class="sectionEyebrow"><span>Wybrane case studies</span><p>Projekty, w których szczególnie dobrze widać połączenie strategii, designu i wdrożenia.</p></div>
  <div class="spotIn" data-spot-slider data-label-live="<?php echo esc_attr($zp_t('Zobacz live', 'View live')); ?>" aria-roledescription="karuzela" aria-label="Wyróżnione realizacje">
    <div class="spotSlides" data-spot-slides>
      <?php if ($zp_real_first): $first = $zp_real_first['p']; $first_i = (int)$zp_real_first['i']; $first_cat = (string)($first['cat'] ?? 'web'); ?>
        <article class="spotSlide is-active spotSlide--server" data-spot-slide data-project-index="<?php echo $first_i; ?>" data-open="<?php echo $first_i; ?>" aria-hidden="false">
          <div class="spotMedia"><img class="spotImg" src="<?php echo esc_url($first['img'] ?? ''); ?>" alt="<?php echo esc_attr(($first['brand'] ?? '') . ' — ' . ($first['sub'] ?? '')); ?>" fetchpriority="high" decoding="async"></div>
          <div class="spotCopy">
            <div class="spotBadges"><span class="spotBadge"><i data-lucide="star"></i> Wyróżniona realizacja</span><span class="spotBadge"><i data-lucide="<?php echo esc_attr($zp_real_cat_icon($first_cat)); ?>"></i> <?php echo esc_html($zp_real_cat_label($first_cat)); ?></span></div>
            <h2><?php echo esc_html($first['brand'] ?? 'Projekt'); ?></h2>
            <div class="sub"><?php echo esc_html($first['sub'] ?? ''); ?></div>
            <p class="desc"><?php echo esc_html($first['desc'] ?? ''); ?></p>
            <?php $first_points = array_slice($zp_real_service_list($first['scope'] ?? ''), 0, 3); if ($first_points): ?><ul class="spotList"><?php foreach ($first_points as $point): ?><li><i data-lucide="check"></i><?php echo esc_html($point); ?></li><?php endforeach; ?></ul><?php endif; ?>
            <div class="spotActions">
              <button class="btn btn--light" data-open="<?php echo $first_i; ?>" type="button"><span>Zobacz case study</span><i data-lucide="arrow-up-right"></i></button>
              <?php if (!empty($first['live'])): ?><a class="btn btn--ghostD" href="<?php echo esc_url($first['live']); ?>" target="_blank" rel="noopener"><span><?php echo esc_html($zp_t('Zobacz live', 'View live')); ?></span><i data-lucide="external-link"></i></a><?php endif; ?>
            </div>
          </div>
        </article>
      <?php endif; ?>
    </div>
    <div class="spotControls" aria-label="Sterowanie sliderem"><button class="spotNav" data-spot-prev type="button" aria-label="Poprzedni projekt"><i data-lucide="arrow-left"></i></button><div class="spotDots" data-spot-dots aria-label="Wybór projektu"></div><button class="spotNav" data-spot-next type="button" aria-label="Następny projekt"><i data-lucide="arrow-right"></i></button></div>
  </div>
</section>

<main class="main">
  <div class="head">
    <div><span class="headKick">Pełne portfolio</span><h2>Case studies zamiast galerii obrazków.</h2></div>
    <p>Każda karta pokazuje kontekst biznesowy projektu, zakres współpracy i technologie. Kliknij realizację, żeby wejść w szczegóły procesu, rozwiązania i efektu.</p>
  </div>

  <div class="tools">
    <div class="toolsIn">
      <label class="search" aria-label="Szukaj realizacji"><i data-lucide="search"></i><input type="search" name="zp_work_search" placeholder="Szukaj projektu, branży, usługi…" data-search autocomplete="off"></label>
      <div class="filterSwipeHint" aria-hidden="true"><span>Przesuń kategorie</span><i data-lucide="arrow-right"></i></div>
      <div class="filters" role="tablist" aria-label="Kategorie realizacji">
        <?php foreach ($zp_real_categories as $cat):
          $slug = sanitize_title((string)($cat['slug'] ?? ''));
          $name = (string)($cat['name'] ?? $slug);
          $icon = sanitize_text_field((string)($cat['icon'] ?? 'layout-grid'));
          $is_all = $slug === 'all';
          $count = $is_all ? $zp_real_count : ($zp_real_counts[$slug] ?? 0);
        ?>
          <button class="filter<?php echo $is_all ? ' is-on' : ''; ?>" data-f="<?php echo esc_attr($slug); ?>" role="tab" aria-selected="<?php echo $is_all ? 'true' : 'false'; ?>"><i data-lucide="<?php echo esc_attr($icon); ?>"></i><span><?php echo esc_html($name); ?></span><em><?php echo (int)$count; ?></em></button>
        <?php endforeach; ?>
      </div>
      <div class="count"><b data-count><?php echo (int)$zp_real_count; ?></b><span>z <?php echo (int)$zp_real_count; ?> projektów</span></div>
    </div>
  </div>

  <?php foreach ($zp_real_groups as $group_key => $group_items): if (!$group_items) { continue; } $group = $zp_real_group_meta[$group_key]; ?>
  <div class="workGroup workGroup--<?php echo esc_attr($group_key); ?>" data-group="<?php echo esc_attr($group_key); ?>" data-limit="<?php echo (int)$group['limit']; ?>">
    <div class="workGroup__head"><span class="workGroup__title"><?php echo esc_html($group['title']); ?></span><span class="workGroup__count" data-group-count><?php echo count($group_items); ?></span></div>
    <div class="grid grid--<?php echo esc_attr($group_key); ?>" data-grid aria-live="polite">
      <?php foreach ($group_items as $i):
        $p = $zp_real_projects[$i];
        $slug = $zp_real_project_slug($p);
        $cat = (string)($p['cat'] ?? 'web');
        $is_tile = $group_key === 'brand';
      ?>
        <article class="card<?php echo $is_tile ? ' card--tile' : ''; ?>" id="case-<?php echo esc_attr($slug); ?>" data-cat="<?php echo esc_attr($cat); ?>" data-i="<?php echo (int)$i; ?>" data-project-slug="<?php echo esc_attr($slug); ?>">
          <div class="cardMedia">
            <img src="<?php echo esc_url($p['img'] ?? ''); ?>" alt="<?php echo esc_attr(($p['brand'] ?? '') . ' — ' . ($p['sub'] ?? '')); ?>" loading="lazy" decoding="async">
            <span class="cardGo" aria-hidden="true"><i data-lucide="arrow-up-right"></i></span>
          </div>
          <div class="cardBody">
            <?php if (!$is_tile): ?><div class="microRow"><span><?php echo esc_html($p['type'] ?? 'Projekt'); ?></span><b><?php echo esc_html($p['year'] ?? ''); ?></b></div><?php endif; ?>
            <h3><a href="#case-<?php echo esc_attr($slug); ?>" data-open="<?php echo (int)$i; ?>"><?php echo esc_html($p['brand'] ?? 'Projekt'); ?></a></h3>
            <div class="cardSub"><?php echo esc_html($p['sub'] ?? ''); ?></div>
            <?php if (!$is_tile): ?>
            <p class="cardDesc"><?php echo esc_html($p['desc'] ?? ''); ?></p>
            <div class="cardActions">
              <span class="more" aria-hidden="true"><span>Zobacz case study</span><i data-lucide="arrow-up-right"></i></span>
              <?php if (!empty($p['live'])): ?><a class="livePill" href="<?php echo esc_url($p['live']); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr(($p['brand'] ?? '') . ' — ' . $zp_t('strona na żywo', 'live website')); ?>"><span>live</span><i data-lucide="external-link"></i></a><?php endif; ?>
            </div>
            <?php endif; ?>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
    <?php if (count($group_items) > (int)$group['limit']): ?><button class="workGroup__more" type="button" data-more hidden><span><?php echo esc_html($group['more']); ?></span><em><?php echo count($group_items); ?></em><i data-lucide="chevron-down"></i></button><?php endif; ?>
  </div>
  <?php endforeach; ?>

  <div class="empty" data-empty><i data-lucide="search-x"></i><h3>Brak realizacji dla tego filtra</h3><p>Spróbuj innej frazy albo kategorii — możesz szukać po nazwie marki, branży, technologii lub usłudze.</p><button class="btn btn--ghost" data-reset><span>Wyczyść filtry</span><i data-lucide="rotate-ccw"></i></button></div>
</main>

<section class="portfolioSeoBridge" aria-labelledby="portfolioSeoBridgeTitle">
  <div class="portfolioSeoBridge__in">
    <div class="portfolioSeoBridge__copy"><span class="headKick">Od inspiracji do decyzji</span><h2 id="portfolioSeoBridgeTitle">Zobacz, jak podobny zakres może wyglądać w Twojej firmie.</h2><p>Portfolio pokazuje efekt. Jeśli chcesz wejść głębiej w proces, ceny i zakres, przejdź do odpowiedniej usługi albo poradników w naszej bazie Wiedza.</p></div>
    <div class="portfolioSeoBridge__links">
      <a href="<?php echo esc_url(home_url('/strony-internetowe-katowice/')); ?>"><span class="portfolioSeoBridge__icon"><i data-lucide="monitor"></i></span><span class="portfolioSeoBridge__text"><strong>Projektowanie stron WWW</strong><small><?php echo esc_html($zp_real_counts['web'] . ' ' . $zp_t($zp_real_plural($zp_real_counts['web'], 'realizacja', 'realizacje', 'realizacji'), $zp_real_counts['web'] === 1 ? 'project' : 'projects') . ' · UX, WordPress, SEO'); ?></small></span><i data-lucide="arrow-up-right"></i></a>
      <a href="<?php echo esc_url(home_url('/sklepy-internetowe-katowice/')); ?>"><span class="portfolioSeoBridge__icon"><i data-lucide="shopping-bag"></i></span><span class="portfolioSeoBridge__text"><strong>Sklepy WooCommerce</strong><small><?php echo esc_html($zp_real_counts['shop'] . ' ' . $zp_t($zp_real_plural($zp_real_counts['shop'], 'realizacja', 'realizacje', 'realizacji'), $zp_real_counts['shop'] === 1 ? 'project' : 'projects') . ' · ' . $zp_t('checkout, płatności, B2B', 'checkout, payments, B2B')); ?></small></span><i data-lucide="arrow-up-right"></i></a>
      <a href="<?php echo esc_url(home_url('/logo-branding-katowice/')); ?>"><span class="portfolioSeoBridge__icon"><i data-lucide="pen-tool"></i></span><span class="portfolioSeoBridge__text"><strong>Logo i branding</strong><small><?php echo esc_html($zp_real_counts['branding'] . ' ' . $zp_t($zp_real_plural($zp_real_counts['branding'], 'realizacja', 'realizacje', 'realizacji'), $zp_real_counts['branding'] === 1 ? 'project' : 'projects') . ' · ' . $zp_t('identyfikacja i materiały', 'identity and brand materials')); ?></small></span><i data-lucide="arrow-up-right"></i></a>
      <a href="<?php echo esc_url(home_url('/wiedza/')); ?>"><span class="portfolioSeoBridge__icon"><i data-lucide="book-open"></i></span><span class="portfolioSeoBridge__text"><strong>Poradniki i wiedza</strong><small><?php echo esc_html($zp_t('Ceny, proces, porównania', 'Prices, process, comparisons')); ?></small></span><i data-lucide="arrow-up-right"></i></a>
    </div>
    <div class="portfolioSeoBridge__cta"><span><small>Masz podobny projekt?</small><strong>Opisz zakres — wrócimy z konkretną wyceną.</strong></span><a href="<?php echo esc_url(home_url('/studio-wyceny/')); ?>">Studio wyceny <i data-lucide="arrow-up-right"></i></a></div>
  </div>
</section>

<div class="modal" data-modal aria-hidden="true"><div class="sheet" role="dialog" aria-modal="true" aria-labelledby="sheetTitle"><button class="sheetClose" data-close aria-label="Zamknij szczegóły"><i data-lucide="x"></i></button><div data-sheet></div></div></div>
</section>
