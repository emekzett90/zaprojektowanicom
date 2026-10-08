<section class="zpHomeSeo zpHomeSeo--dark" id="zpHomeSeo" aria-labelledby="zpHomeSeoTitle">
  <div class="zpHomeSeo__bleed" aria-hidden="true"></div>
  <div class="zpHomeSeo__mark" aria-hidden="true">branże</div>

  <div class="zpHomeSeo__inner">
    <header class="zpHomeSeo__head">
      <div class="zpHomeSeo__headCopy">
        <span class="zpHomeSeo__kicker">SEO • USŁUGI • BRANŻE</span>
        <h2 id="zpHomeSeoTitle">Strony internetowe i sklepy dla branż, w których decyzja zaczyna się od zaufania</h2>
      </div>
      <p>
        Nie projektujemy każdej strony według tego samego schematu. Inaczej prowadzimy użytkownika w kancelarii, inaczej przy inwestycji deweloperskiej, a jeszcze inaczej w salonie beauty lub sklepie producenta. Dzięki temu treść, UX, portfolio i formularze pasują do sposobu, w jaki klient podejmuje decyzję.
      </p>
    </header>

    <?php
      // Suite 2.9.4 (Mat 8.10: tiles for every industry page). The industries with a photo of a
      // real project keep the photo tiles; the others get the same tile without a photo, in a
      // shorter row underneath (styles in includes/home-294.php). A tile added in 2.9.4 shows
      // only once its page is published.
      $zp_home_industries = [
        [
          'num' => '01',
          'icon' => 'scale',
          'title' => 'Strony internetowe dla kancelarii',
          'text' => 'Układ pod zaufanie, eksperckość i szybki kontakt z klientem.',
          'image' => 'https://zaprojektowani.com/wp-content/uploads/2026/05/strony_internetowe_dla_prawnikow.webp',
          'url' => '/strony-internetowe-dla-kancelarii/',
        ],
        [
          'num' => '02',
          'icon' => 'building-2',
          'title' => 'Strony dla deweloperów',
          'text' => 'Prezentacja inwestycji, lokalizacji, standardu i formularzy zapytań.',
          'image' => 'https://zaprojektowani.com/wp-content/uploads/2026/05/strona_internetowa_dla_developera.webp',
          'url' => '/strony-internetowe-dla-deweloperow/',
        ],
        [
          'num' => '03',
          'icon' => 'stethoscope',
          'title' => 'Strony dla lekarzy i gabinetów',
          'text' => 'Rzeczowa informacja o usługach, umawianie wizyt i lokalne SEO.',
          'image' => 'https://zaprojektowani.com/wp-content/uploads/2026/05/strona_internetowa_dla_lekarza.webp',
          'url' => '/strony-internetowe-dla-lekarzy/',
        ],
        [
          'num' => '04',
          'icon' => 'flower-2',
          'title' => 'Strony dla salonów beauty',
          'text' => 'Opisy zabiegów, czytelny cennik i rezerwacja wizyt online.',
          'image' => 'https://zaprojektowani.com/wp-content/uploads/2026/05/strona_internetowa_dla_branzy_beauty.webp',
          'url' => '/strony-internetowe-dla-salonow-beauty/',
          'new' => true,
        ],
        [
          'num' => '05',
          'icon' => 'dumbbell',
          'title' => 'Strony dla trenerów personalnych',
          'text' => 'Oferta, pakiety i zapis na pierwszy trening bez wymiany wiadomości.',
          'url' => '/strony-internetowe-dla-trenerow-personalnych/',
          'new' => true,
        ],
        [
          'num' => '06',
          'icon' => 'camera',
          'title' => 'Strony dla fotografów',
          'text' => 'Portfolio w kategoriach, szybkie galerie i zapytania o termin.',
          'url' => '/strony-internetowe-dla-fotografow/',
          'new' => true,
        ],
        [
          'num' => '07',
          'icon' => 'utensils-crossed',
          'title' => 'Strony dla restauracji i kawiarni',
          'text' => 'Menu online, rezerwacja stolika i zamówienia na wynos.',
          'url' => '/strony-internetowe-dla-restauracji/',
          'new' => true,
        ],
        [
          'num' => '08',
          'icon' => 'bed-double',
          'title' => 'Strony dla hoteli i pensjonatów',
          'text' => 'Pokoje, ceny na wybrany termin i rezerwacje bezpośrednie.',
          'url' => '/strony-internetowe-dla-hoteli/',
          'new' => true,
        ],
      ];
      $zp_home_industries = array_values(array_filter($zp_home_industries, static function ($industry) {
        if (empty($industry['new'])) { return true; }
        return function_exists('zp_seo_plan_link_is_live') && zp_seo_plan_link_is_live($industry['url']);
      }));
      $zp_home_industries_photo = array_values(array_filter($zp_home_industries, static function ($industry) { return !empty($industry['image']); }));
      $zp_home_industries_more = array_values(array_filter($zp_home_industries, static function ($industry) { return empty($industry['image']); }));
    ?>
    <div class="zpHomeSeo__grid" role="group" aria-label="Branże i kierunki pozycjonowania">
      <?php foreach ($zp_home_industries_photo as $industry) : ?>
        <a class="zpHomeSeo__tile" href="<?php echo esc_url($industry['url'] ?? '/strony-internetowe-katowice/'); ?>">
          <span class="zpHomeSeo__tileBg" aria-hidden="true"></span>
          <span class="zpHomeSeo__icon"><i data-lucide="<?php echo esc_attr($industry['icon']); ?>"></i></span>
          <span class="zpHomeSeo__num"><?php echo esc_html($industry['num']); ?></span>
          <strong><?php echo esc_html($industry['title']); ?></strong>
          <span><?php echo esc_html($industry['text']); ?></span>
          <img class="zpHomeSeo__mock" src="<?php echo esc_url($industry['image']); ?>" alt="" loading="lazy" decoding="async">
        </a>
      <?php endforeach; ?>
    </div>
    <?php if ($zp_home_industries_more) : ?>
    <div class="zpHomeSeo__more" role="group" aria-label="Więcej branż">
      <?php foreach ($zp_home_industries_more as $industry) : ?>
        <a class="zpHomeSeo__card" href="<?php echo esc_url($industry['url']); ?>">
          <span class="zpHomeSeo__cardIcon"><i data-lucide="<?php echo esc_attr($industry['icon']); ?>"></i></span>
          <span class="zpHomeSeo__cardNum"><?php echo esc_html($industry['num']); ?></span>
          <strong><?php echo esc_html($industry['title']); ?></strong>
          <span class="zpHomeSeo__cardText"><?php echo esc_html($industry['text']); ?></span>
        </a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

  </div>
</section>
