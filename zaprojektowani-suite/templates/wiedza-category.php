<?php
if (!defined('ABSPATH')) { exit; }
get_header();

$term = get_queried_object();
if (!$term || is_wp_error($term)) {
  get_footer();
  return;
}

$group = function_exists('zp_suite_wiedza_group_for_term') ? zp_suite_wiedza_group_for_term($term) : null;
$label = !empty($group['label']) ? $group['label'] : $term->name;
$description = !empty($group['description']) ? $group['description'] : trim(wp_strip_all_tags(term_description((int)$term->term_id, 'category')));
if ($description === '') {
  $description = 'Praktyczne poradniki, checklisty i materiały Zaprojektowani związane z tym obszarem projektowania i marketingu.';
}
$term_ids = !empty($group['term_ids']) ? array_values(array_map('intval', (array)$group['term_ids'])) : [(int)$term->term_id];
$paged = max(1, (int)get_query_var('paged'));

// The main query determines both HTTP status and the visible pagination.
global $wp_query;
$archive_query = $wp_query;

$topics = function_exists('zp_suite_wiedza_topic_groups_resolved') ? zp_suite_wiedza_topic_groups_resolved() : [];
$total = (int)$archive_query->found_posts;

$all_post_ids = get_posts([
  'post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => -1,
  'fields' => 'ids', 'category__in' => $term_ids, 'orderby' => 'date', 'order' => 'DESC',
  'ignore_sticky_posts' => true, 'no_found_rows' => true, 'update_post_meta_cache' => false, 'update_post_term_cache' => false,
]);
$all_post_ids = array_values(array_unique(array_map('intval', (array)$all_post_ids)));
$total_reads = 0;
if (function_exists('zp_suite_wiedza_read_count')) {
  foreach ($all_post_ids as $pid) { $total_reads += (int) zp_suite_wiedza_read_count($pid); }
}

$item_list = [];
foreach ((array)$archive_query->posts as $index => $post_obj) {
  $item_list[] = [
    '@type' => 'ListItem',
    'position' => (($paged - 1) * 12) + $index + 1,
    'url' => get_permalink($post_obj->ID),
    'name' => get_the_title($post_obj->ID),
  ];
}
?>
<main id="zpKnowledgeCategory" class="zpKBCatPage">
  <section class="zpKBCatHero">
    <div class="zpKBCatInner">
      <nav class="zpKBCatCrumbs" aria-label="Okruszki">
        <a href="<?php echo esc_url(home_url('/')); ?>">Strona główna</a>
        <span aria-hidden="true">/</span>
        <a href="<?php echo esc_url(home_url('/wiedza/')); ?>">Wiedza</a>
        <span aria-hidden="true">/</span>
        <span><?php echo esc_html($label); ?></span>
      </nav>

      <div class="zpKBCatHero__grid">
        <div class="zpKBCatHero__copy">
          <span class="zpKBCatEyebrow">Baza wiedzy · <?php echo esc_html($label); ?></span>
          <h1><?php echo esc_html($label); ?></h1>
          <p><?php echo esc_html($description); ?></p>
          <div class="zpKBCatHero__actions">
            <a class="zpKBCatBtn zpKBCatBtn--primary" href="#zpKBCatArticles">Przejdź do poradników <span aria-hidden="true">↓</span></a>
            <a class="zpKBCatBtn" href="<?php echo esc_url(home_url('/wiedza/')); ?>">Cała baza wiedzy</a>
            <?php if (!empty($group['service_url']) && $group['service_url'] !== home_url('/wiedza/')): ?>
              <a class="zpKBCatBtn" href="<?php echo esc_url($group['service_url']); ?>">Zobacz ofertę</a>
            <?php endif; ?>
          </div>
        </div>
        <aside class="zpKBCatHero__stat" aria-label="Statystyki kategorii">
          <span class="zpKBCatHero__statLabel">W tym dziale</span>
          <strong><?php echo number_format_i18n($total); ?></strong>
          <span class="zpKBCatHero__statTitle"><?php echo $total === 1 ? 'opublikowany poradnik' : 'opublikowanych poradników'; ?></span>
          <div class="zpKBCatHero__statMeta">
            <span><b><?php echo $total_reads > 0 ? number_format_i18n($total_reads) : 'Nowe'; ?></b><em><?php echo $total_reads > 0 ? 'realnych czytań' : 'liczniki czytań'; ?></em></span>
            <span><b>SEO</b><em>klaster tematyczny</em></span>
          </div>
        </aside>
      </div>

      <?php if (!empty($topics)): ?>
        <nav class="zpKBCatTopics" aria-label="Pozostałe obszary wiedzy">
          <?php foreach ($topics as $topic_key => $topic):
            $is_active = $group && !empty($group['key']) && $group['key'] === $topic_key;
          ?>
            <a class="zpKBCatTopic<?php echo $is_active ? ' is-active' : ''; ?>" href="<?php echo esc_url($topic['url']); ?>">
              <span><?php echo esc_html($topic['label']); ?></span>
              <b><?php echo (int)$topic['count']; ?></b>
            </a>
          <?php endforeach; ?>
        </nav>
      <?php endif; ?>
    </div>
  </section>

  <section class="zpKBCatContent" id="zpKBCatArticles">
    <div class="zpKBCatInner">
      <header class="zpKBCatContent__head">
        <div>
          <span class="zpKBCatEyebrow">Najnowsze materiały</span>
          <h2>Poradniki: <?php echo esc_html($label); ?></h2>
          <p>Konkretnie, bez lania wody: decyzje projektowe, koszty, struktura, UX, SEO i rzeczy, które warto sprawdzić przed wdrożeniem.</p>
        </div>
        <div class="zpKBCatContent__tools">
          <span><b><?php echo number_format_i18n($total); ?></b> artykułów w tym temacie</span>
          <a href="<?php echo esc_url(home_url('/wiedza/#zpKBIndex')); ?>">Przeszukaj całą bazę <span aria-hidden="true">↗</span></a>
        </div>
      </header>

      <?php if ($archive_query->have_posts()): ?>
        <div class="zpKBCatGrid">
          <?php $card_i=0; while ($archive_query->have_posts()): $archive_query->the_post();
            $post_id = get_the_ID();
            $read_time = function_exists('zp_suite_wiedza_read_time') ? zp_suite_wiedza_read_time($post_id) : '';
            $reads = function_exists('zp_suite_wiedza_read_count') ? zp_suite_wiedza_read_count($post_id) : 0;
            $reads_label = function_exists('zp_suite_wiedza_read_label') ? zp_suite_wiedza_read_label($reads) : ($reads ? number_format_i18n($reads) . ' czytań' : 'Nowy wpis');
            $excerpt = function_exists('zp_suite_wiedza_fast_excerpt') ? zp_suite_wiedza_fast_excerpt($post_id, $card_i === 0 ? 42 : 34) : wp_trim_words(wp_strip_all_tags(get_post_field('post_content', $post_id)), $card_i === 0 ? 42 : 34, '…');
            $cats = get_the_category($post_id);
            $cat_name = !empty($cats) ? $cats[0]->name : $label;
            $is_featured = ($card_i === 0 && $paged === 1);
          ?>
            <article class="zpKBCatCard<?php echo $is_featured ? ' is-featured' : ''; ?>">
              <a class="zpKBCatCard__media" href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr(get_the_title()); ?>">
                <?php if (function_exists('zp_suite_wiedza_image_markup')): ?>
                  <?php echo zp_suite_wiedza_image_markup($post_id, $is_featured ? 'large' : 'medium_large', 'zpKBCatCard__img', get_the_title($post_id), '(max-width: 760px) calc(100vw - 32px), (max-width: 1100px) 50vw, 720px'); ?>
                <?php elseif (has_post_thumbnail()): ?>
                  <?php the_post_thumbnail($is_featured ? 'large' : 'medium_large', ['class'=>'zpKBCatCard__img','loading'=>'lazy','decoding'=>'async']); ?>
                <?php endif; ?>
                <?php if ($is_featured): ?><span class="zpKBCatCard__featured">Najnowszy poradnik</span><?php endif; ?>
                <span class="zpKBCatCard__reads"><?php echo esc_html($reads_label); ?></span>
              </a>
              <div class="zpKBCatCard__body">
                <div class="zpKBCatCard__meta">
                  <span><?php echo esc_html(get_the_date('d.m.Y')); ?></span>
                  <?php if ($read_time): ?><span><?php echo esc_html($read_time); ?></span><?php endif; ?>
                  <span><?php echo esc_html($cat_name); ?></span>
                </div>
                <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                <p><?php echo esc_html($excerpt); ?></p>
                <a class="zpKBCatCard__link" href="<?php the_permalink(); ?>">Czytaj artykuł <span aria-hidden="true">↗</span></a>
              </div>
            </article>
          <?php $card_i++; endwhile; ?>
        </div>

        <?php
        $pagination = paginate_links([
          'total' => max(1, (int)$archive_query->max_num_pages),
          'current' => $paged,
          'type' => 'list',
          'prev_text' => '← Poprzednie',
          'next_text' => 'Następne →',
        ]);
        if ($pagination): ?>
          <nav class="zpKBCatPagination" aria-label="Paginacja artykułów"><?php echo wp_kses_post($pagination); ?></nav>
        <?php endif; ?>
      <?php else: ?>
        <div class="zpKBCatEmpty"><h2>Brak opublikowanych artykułów.</h2><p>Wróć do głównej bazy wiedzy i wybierz inny obszar.</p></div>
      <?php endif; wp_reset_postdata(); ?>

      <section class="zpKBCatCTA">
        <div>
          <span class="zpKBCatEyebrow">Zaprojektowani</span>
          <h2>Masz projekt i chcesz przejść od wiedzy do działania?</h2>
          <p>Opisz zakres, który planujesz. Dobierzemy rozwiązanie, zamiast wciskać gotowy pakiet.</p>
        </div>
        <div class="zpKBCatCTA__actions">
          <a href="<?php echo esc_url(home_url('/studio-wyceny/')); ?>">Szybka wycena</a>
          <a href="<?php echo esc_url(home_url('/kontakt/')); ?>">Kontakt</a>
        </div>
      </section>
    </div>
  </section>
</main>

<?php if ($item_list): ?>
<script type="application/ld+json"><?php echo wp_json_encode([
  '@context' => 'https://schema.org',
  '@type' => 'ItemList',
  'name' => 'Poradniki: ' . $label,
  'numberOfItems' => count($item_list),
  'itemListElement' => $item_list,
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?></script>
<?php endif; ?>

<style id="zp-wiedza-category-css">
#zpKnowledgeCategory,#zpKnowledgeCategory *{box-sizing:border-box}
#zpKnowledgeCategory{--ink:#07111f;--muted:#647085;--line:#e4e9ef;--soft:#f5f8fb;--navy:#0b1729;--blue:#315d91;background:#fff;color:var(--ink);font-family:"Plus Jakarta Sans Local","Plus Jakarta Sans",system-ui,sans-serif;min-height:100vh;-webkit-font-smoothing:antialiased}
#zpKnowledgeCategory a{color:inherit}.zpKBCatInner{width:min(1640px,calc(100% - 72px));margin:0 auto}
/* keep archive header pure white instead of the old grey archive state */
html body.category #zpNewNav.zpNewNav--knowledgeArchive:not(.is-mega-open),html body.category #zpNewNav.zpNewNav--knowledgeArchive:not(.is-mega-open) .zpNewNav__shell{background:#fff!important;background-color:#fff!important;background-image:none!important;box-shadow:none!important;backdrop-filter:none!important;-webkit-backdrop-filter:none!important}
.zpKBCatHero{position:relative;overflow:hidden;padding:clamp(108px,8vw,142px) 0 clamp(48px,5.5vw,78px);background:linear-gradient(180deg,#f7faff 0%,#fff 82%);border-bottom:1px solid var(--line)}
.zpKBCatHero:before{content:"";position:absolute;width:760px;height:760px;right:-330px;top:-430px;border-radius:50%;background:radial-gradient(circle,rgba(49,93,145,.13),transparent 67%);pointer-events:none}.zpKBCatHero:after{content:"";position:absolute;left:-220px;bottom:-380px;width:620px;height:620px;border-radius:50%;background:radial-gradient(circle,rgba(142,200,247,.09),transparent 68%);pointer-events:none}
.zpKBCatHero .zpKBCatInner{position:relative;z-index:1}.zpKBCatCrumbs{display:flex;align-items:center;gap:9px;flex-wrap:wrap;margin-bottom:34px;font-size:12px;color:#8791a1}.zpKBCatCrumbs a{text-decoration:none;font-weight:650;color:#536176}.zpKBCatCrumbs a:hover{color:var(--blue)}
.zpKBCatHero__grid{display:grid;grid-template-columns:minmax(0,1fr) 360px;gap:clamp(38px,6vw,96px);align-items:end}.zpKBCatEyebrow{display:block;margin-bottom:14px;font-size:11px;line-height:1.4;letter-spacing:.105em;text-transform:uppercase;color:#496b93;font-weight:800}.zpKBCatHero h1{max-width:1080px;margin:0 0 22px;font-size:clamp(50px,5.7vw,88px);line-height:1;letter-spacing:-.062em;font-weight:650;color:var(--ink);text-wrap:balance}.zpKBCatHero__copy>p{max-width:920px;margin:0;color:var(--muted);font-size:18px;line-height:1.8}
.zpKBCatHero__actions{display:flex;gap:10px;flex-wrap:wrap;margin-top:30px}.zpKBCatBtn{display:inline-flex;align-items:center;justify-content:center;gap:10px;min-height:50px;padding:0 20px;border:1px solid #dce3eb;border-radius:999px;text-decoration:none;font-size:12.5px;font-weight:800;background:#fff;transition:border-color .2s ease,transform .2s ease}.zpKBCatBtn--primary{background:var(--navy);border-color:var(--navy);color:#fff!important}.zpKBCatBtn:hover{border-color:#9fb2c8;transform:translateY(-1px)}
.zpKBCatHero__stat{position:relative;overflow:hidden;padding:30px;border-radius:28px;background:#0b1729;color:#fff}.zpKBCatHero__stat:after{content:"";position:absolute;width:180px;height:180px;right:-90px;top:-105px;border-radius:50%;border:1px solid #ffffff20}.zpKBCatHero__statLabel{display:block;color:#9fb3ca;font-size:10px;font-weight:800;letter-spacing:.1em;text-transform:uppercase}.zpKBCatHero__stat>strong{position:relative;z-index:1;display:block;margin-top:18px;color:#fff;font-size:64px;line-height:.92;letter-spacing:-.06em}.zpKBCatHero__statTitle{display:block;margin-top:8px;color:#fff;font-size:13px;font-weight:750}.zpKBCatHero__statMeta{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-top:24px;padding-top:20px;border-top:1px solid #ffffff1f}.zpKBCatHero__statMeta span{min-width:0}.zpKBCatHero__statMeta b{display:block;color:#fff;font-size:14px}.zpKBCatHero__statMeta em{display:block;margin-top:3px;color:#9fb0c4;font-size:10px;font-style:normal;line-height:1.4}
.zpKBCatTopics{display:flex;gap:9px;overflow:auto;margin-top:44px;padding:2px 0 6px;scrollbar-width:none}.zpKBCatTopics::-webkit-scrollbar{display:none}.zpKBCatTopic{display:flex;align-items:center;gap:10px;flex:0 0 auto;min-height:42px;padding:0 15px;border:1px solid #dfe5ec;border-radius:999px;text-decoration:none;background:#fff;font-size:12px;font-weight:750;color:#455368!important;transition:background .2s,border-color .2s,color .2s}.zpKBCatTopic span{color:inherit!important}.zpKBCatTopic b{display:grid;place-items:center;min-width:25px;height:25px;padding:0 7px;border-radius:999px;background:#eff4f9;color:#315d91!important;font-size:10px}.zpKBCatTopic:hover{border-color:#aebdcd}.zpKBCatTopics .zpKBCatTopic.is-active{background:var(--navy)!important;border-color:var(--navy)!important;color:#fff!important}.zpKBCatTopics .zpKBCatTopic.is-active span,.zpKBCatTopics .zpKBCatTopic.is-active b{color:#fff!important}.zpKBCatTopics .zpKBCatTopic.is-active b{background:#ffffff20!important}
.zpKBCatContent{scroll-margin-top:94px;padding:clamp(62px,7vw,104px) 0 100px;background:#fff}.zpKBCatContent__head{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:48px;align-items:end;margin-bottom:34px;padding-bottom:30px;border-bottom:1px solid var(--line)}.zpKBCatContent__head h2{margin:0;font-size:clamp(36px,3.8vw,58px);line-height:1.08;letter-spacing:-.048em;font-weight:650}.zpKBCatContent__head>div:first-child>p{max-width:850px;margin:14px 0 0;color:var(--muted);font-size:15px;line-height:1.8}.zpKBCatContent__tools{display:flex;flex-direction:column;align-items:flex-end;gap:10px}.zpKBCatContent__tools>span{color:#748196;font-size:11px}.zpKBCatContent__tools>span b{color:#07111f;font-size:14px}.zpKBCatContent__tools a{display:inline-flex;align-items:center;gap:8px;min-height:40px;padding:0 14px;border:1px solid var(--line);border-radius:999px;text-decoration:none;color:#315d91!important;font-size:11px;font-weight:800}
.zpKBCatGrid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:22px}.zpKBCatCard{min-width:0;border:1px solid var(--line);border-radius:26px;background:#fff;overflow:hidden;display:flex;flex-direction:column;transition:transform .2s ease,border-color .2s ease}.zpKBCatCard:hover{transform:translateY(-3px);border-color:#b5c4d4}.zpKBCatCard__media{position:relative;display:block;aspect-ratio:1.55/1;background:#eef2f6;overflow:hidden}.zpKBCatCard__img{display:block;width:100%;height:100%;object-fit:cover;transition:transform .35s ease}.zpKBCatCard:hover .zpKBCatCard__img{transform:scale(1.025)}.zpKBCatCard__reads,.zpKBCatCard__featured{position:absolute;z-index:2;padding:7px 10px;border-radius:999px;backdrop-filter:blur(8px);font-size:10px;font-weight:800}.zpKBCatCard__reads{left:14px;bottom:14px;background:rgba(7,17,31,.88);color:#fff}.zpKBCatCard__featured{left:14px;top:14px;background:rgba(255,255,255,.91);color:#07111f}.zpKBCatCard__body{padding:24px;display:flex;flex:1;flex-direction:column}.zpKBCatCard__meta{display:flex;gap:9px;flex-wrap:wrap;margin-bottom:15px;color:#8490a1;font-size:10.5px}.zpKBCatCard__meta span+span:before{content:"·";margin-right:9px}.zpKBCatCard h2{margin:0 0 13px;font-size:clamp(21px,1.7vw,28px);line-height:1.24;letter-spacing:-.035em;font-weight:650}.zpKBCatCard h2 a{text-decoration:none}.zpKBCatCard h2 a:hover{color:#315d91}.zpKBCatCard p{margin:0 0 22px;color:var(--muted);font-size:14px;line-height:1.72}.zpKBCatCard__link{margin-top:auto;display:inline-flex;gap:8px;align-items:center;text-decoration:none;font-size:12px;font-weight:800;color:#315d91!important}
.zpKBCatCard.is-featured{grid-column:span 2;display:grid;grid-template-columns:minmax(0,1.16fr) minmax(300px,.84fr);min-height:420px}.zpKBCatCard.is-featured .zpKBCatCard__media{aspect-ratio:auto;min-height:100%}.zpKBCatCard.is-featured .zpKBCatCard__body{justify-content:center;padding:clamp(28px,3vw,42px)}.zpKBCatCard.is-featured h2{font-size:clamp(28px,2.4vw,40px);line-height:1.13}.zpKBCatCard.is-featured p{font-size:15px;line-height:1.8}
.zpKBCatPagination{margin-top:52px}.zpKBCatPagination ul{display:flex;gap:8px;flex-wrap:wrap;justify-content:center;padding:0;margin:0;list-style:none}.zpKBCatPagination a,.zpKBCatPagination span{display:grid;place-items:center;min-width:42px;height:42px;padding:0 13px;border:1px solid var(--line);border-radius:999px;text-decoration:none;font-size:12px;font-weight:750}.zpKBCatPagination .current{background:var(--navy);border-color:var(--navy);color:#fff}.zpKBCatEmpty{padding:50px;border-radius:26px;background:var(--soft);text-align:center}
.zpKBCatCTA{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:40px;align-items:center;margin-top:70px;padding:clamp(34px,5vw,64px);border-radius:30px;background:var(--navy);color:#fff}.zpKBCatCTA .zpKBCatEyebrow{color:#9eb9d6}.zpKBCatCTA h2{max-width:850px;margin:0 0 14px;color:#fff;font-size:clamp(30px,3vw,48px);line-height:1.12;letter-spacing:-.045em;font-weight:650}.zpKBCatCTA p{margin:0;max-width:780px;color:#c5ceda;font-size:15px;line-height:1.75}.zpKBCatCTA__actions{display:flex;gap:10px;flex-wrap:wrap}.zpKBCatCTA__actions a{display:inline-flex;align-items:center;justify-content:center;min-height:48px;padding:0 19px;border:1px solid #ffffff45;border-radius:999px;text-decoration:none;color:#fff;font-size:12px;font-weight:800}.zpKBCatCTA__actions a:first-child{background:#fff;color:var(--navy);border-color:#fff}
@media(max-width:1100px){.zpKBCatInner{width:min(100% - 48px,1180px)}.zpKBCatHero__grid{grid-template-columns:1fr 290px}.zpKBCatGrid{grid-template-columns:repeat(2,minmax(0,1fr))}.zpKBCatCard.is-featured{grid-column:1/-1}.zpKBCatCTA{grid-template-columns:1fr}.zpKBCatCTA__actions{justify-content:flex-start}}
@media(max-width:760px){.zpKBCatInner{width:calc(100% - 32px)}.zpKBCatHero{padding-top:96px}.zpKBCatHero__grid,.zpKBCatContent__head{grid-template-columns:1fr}.zpKBCatHero h1{font-size:clamp(42px,13vw,62px)}.zpKBCatHero__copy>p{font-size:16px}.zpKBCatHero__stat{padding:24px}.zpKBCatHero__stat>strong{font-size:48px}.zpKBCatTopics{margin-top:30px}.zpKBCatContent{padding-top:52px}.zpKBCatContent__tools{align-items:flex-start}.zpKBCatGrid{grid-template-columns:1fr;gap:18px}.zpKBCatCard,.zpKBCatCard.is-featured{display:flex;grid-column:auto;min-height:0;border-radius:22px}.zpKBCatCard.is-featured .zpKBCatCard__media{min-height:0;aspect-ratio:1.55/1}.zpKBCatCard__body,.zpKBCatCard.is-featured .zpKBCatCard__body{padding:21px}.zpKBCatCard.is-featured h2{font-size:27px}.zpKBCatCTA{margin-top:50px;border-radius:24px}.zpKBCatCTA__actions{display:grid;grid-template-columns:1fr}.zpKBCatCTA__actions a{text-align:center}}
</style>
<?php get_footer();
