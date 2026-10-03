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
?>
<script id="zp-realizacje-cms-data">
window.zpRealizacjeProjects = <?php echo wp_json_encode($zp_real_projects, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
</script>
<section id="zpRealizacjePage" aria-labelledby="zpRealizacjeTitle">

<header class="hero zpRealizacjePage__hero">
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
              <small>51 opinii klientów · zweryfikowane w Trustindex</small>
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
</header>

<section class="portfolioPaths" aria-label="Obszary realizacji">
  <div class="portfolioPaths__in">
    <a href="<?php echo esc_url(home_url('/strony-internetowe-katowice/')); ?>"><span class="portfolioPaths__icon"><i data-lucide="monitor"></i></span><span><small><?php echo (int)$zp_real_counts['web']; ?> realizacji</small><strong>Strony internetowe</strong><em>UX, WordPress, systemy customowe i SEO</em></span><i data-lucide="arrow-up-right"></i></a>
    <a href="<?php echo esc_url(home_url('/sklepy-internetowe-katowice/')); ?>"><span class="portfolioPaths__icon"><i data-lucide="shopping-bag"></i></span><span><small><?php echo (int)$zp_real_counts['shop']; ?> realizacji</small><strong>Sklepy internetowe</strong><em>WooCommerce, checkout, płatności i B2B</em></span><i data-lucide="arrow-up-right"></i></a>
    <a href="<?php echo esc_url(home_url('/logo-branding-katowice/')); ?>"><span class="portfolioPaths__icon"><i data-lucide="pen-tool"></i></span><span><small><?php echo (int)$zp_real_counts['branding']; ?> realizacji</small><strong>Logo i branding</strong><em>Identyfikacja, system marki i materiały</em></span><i data-lucide="arrow-up-right"></i></a>
  </div>
</section>

<div class="tools" id="portfolio-realizacji">
  <div class="toolsIn">
    <label class="search" aria-label="Szukaj realizacji"><i data-lucide="search"></i><input type="search" placeholder="Szukaj projektu, branży, usługi…" data-search autocomplete="off"></label>
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

<section class="spot" aria-label="Wyróżnione realizacje">
  <div class="sectionEyebrow"><span>Wybrane case studies</span><p>Projekty, w których szczególnie dobrze widać połączenie strategii, designu i wdrożenia.</p></div>
  <div class="spotIn" data-spot-slider aria-roledescription="karuzela" aria-label="Wyróżnione realizacje">
    <div class="spotSlides" data-spot-slides>
      <?php if (!empty($zp_featured[0])): $first=$zp_featured[0]['p']; ?>
        <article class="spotSlide is-active spotSlide--server" aria-hidden="false">
          <img class="spotImg" src="<?php echo esc_url($first['img'] ?? ''); ?>" alt="<?php echo esc_attr(($first['brand'] ?? '') . ' — ' . ($first['sub'] ?? '')); ?>" fetchpriority="high" decoding="async">
          <div class="spotShade" aria-hidden="true"></div>
          <div class="spotCopy"><div class="spotBadges"><span class="spotBadge"><i data-lucide="star"></i> Wyróżniona realizacja</span></div><h2><?php echo esc_html($first['brand'] ?? 'Projekt'); ?></h2><div class="sub"><?php echo esc_html($first['sub'] ?? ''); ?></div><p class="desc"><?php echo esc_html($first['desc'] ?? ''); ?></p></div>
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

  <div class="grid" data-grid aria-live="polite">
    <?php foreach ($zp_real_projects as $i => $p):
      $services = array_slice($zp_real_service_list($p['services'] ?? ''),0,3);
      $slug = $zp_real_project_slug($p);
      $cat = (string)($p['cat'] ?? 'web');
    ?>
      <article class="card card--server" id="case-<?php echo esc_attr($slug); ?>" data-cat="<?php echo esc_attr($cat); ?>" data-i="<?php echo (int)$i; ?>" data-project-slug="<?php echo esc_attr($slug); ?>" tabindex="0" aria-label="<?php echo esc_attr(($p['brand'] ?? '') . ' — zobacz case study'); ?>">
        <div class="cardMedia">
          <img src="<?php echo esc_url($p['img'] ?? ''); ?>" alt="<?php echo esc_attr(($p['brand'] ?? '') . ' — ' . ($p['sub'] ?? '')); ?>" loading="lazy" decoding="async">
          <span class="cat"><i data-lucide="<?php echo esc_attr($zp_real_cat_icon($cat)); ?>"></i><?php echo esc_html($zp_real_cat_label($cat)); ?></span>
          <span class="cardGo" aria-hidden="true"><i data-lucide="arrow-up-right"></i></span>
        </div>
        <div class="cardBody">
          <div class="microRow"><span><?php echo esc_html($p['type'] ?? 'Projekt'); ?></span><b><?php echo esc_html($p['year'] ?? ''); ?></b></div>
          <h3><?php echo esc_html($p['brand'] ?? 'Projekt'); ?></h3>
          <div class="cardSub"><?php echo esc_html($p['sub'] ?? ''); ?></div>
          <p class="cardDesc"><?php echo esc_html($p['desc'] ?? ''); ?></p>
          <?php if ($services): ?><div class="chips"><?php foreach ($services as $service): ?><span><?php echo esc_html($service); ?></span><?php endforeach; ?></div><?php endif; ?>
          <div class="cardActions">
            <a class="more" href="#case-<?php echo esc_attr($slug); ?>" data-open="<?php echo (int)$i; ?>"><span>Zobacz case study</span><i data-lucide="arrow-up-right"></i></a>
            <?php if (!empty($p['live'])): ?><a class="livePill" href="<?php echo esc_url($p['live']); ?>" target="_blank" rel="noopener"><span>live</span><i data-lucide="external-link"></i></a><?php endif; ?>
          </div>
        </div>
      </article>
    <?php endforeach; ?>
  </div>

  <div class="empty" data-empty><i data-lucide="search-x"></i><h3>Brak realizacji dla tego filtra</h3><p>Spróbuj innej frazy albo kategorii — możesz szukać po nazwie marki, branży, technologii lub usłudze.</p><button class="btn btn--ghost" data-reset><span>Wyczyść filtry</span><i data-lucide="rotate-ccw"></i></button></div>
</main>

<section class="portfolioSeoBridge" aria-labelledby="portfolioSeoBridgeTitle">
  <div class="portfolioSeoBridge__in">
    <div class="portfolioSeoBridge__copy"><span class="headKick">Od inspiracji do decyzji</span><h2 id="portfolioSeoBridgeTitle">Zobacz, jak podobny zakres może wyglądać w Twojej firmie.</h2><p>Portfolio pokazuje efekt. Jeśli chcesz wejść głębiej w proces, ceny i zakres, przejdź do odpowiedniej usługi albo poradników w naszej bazie Wiedza.</p></div>
    <div class="portfolioSeoBridge__links">
      <a href="<?php echo esc_url(home_url('/strony-internetowe-katowice/')); ?>"><span>Projektowanie stron WWW</span><i data-lucide="arrow-up-right"></i></a>
      <a href="<?php echo esc_url(home_url('/sklepy-internetowe-katowice/')); ?>"><span>Sklepy WooCommerce</span><i data-lucide="arrow-up-right"></i></a>
      <a href="<?php echo esc_url(home_url('/logo-branding-katowice/')); ?>"><span>Logo i branding</span><i data-lucide="arrow-up-right"></i></a>
      <a href="<?php echo esc_url(home_url('/wiedza/')); ?>"><span>Poradniki i wiedza</span><i data-lucide="arrow-up-right"></i></a>
    </div>
    <div class="portfolioSeoBridge__cta"><span><small>Masz podobny projekt?</small><strong>Opisz zakres — wrócimy z konkretną wyceną.</strong></span><a href="<?php echo esc_url(home_url('/studio-wyceny/')); ?>">Studio wyceny <i data-lucide="arrow-up-right"></i></a></div>
  </div>
</section>

<div class="modal" data-modal aria-hidden="true"><div class="sheet" role="dialog" aria-modal="true" aria-labelledby="sheetTitle"><button class="sheetClose" data-close aria-label="Zamknij szczegóły"><i data-lucide="x"></i></button><div data-sheet></div></div></div>
</section>
