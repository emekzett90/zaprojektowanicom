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
          'icon' => 'sparkles',
          'title' => 'Strony dla branży beauty',
          'text' => 'Estetyka premium, oferta zabiegów, lokalne SEO i rezerwacje.',
          'image' => 'https://zaprojektowani.com/wp-content/uploads/2026/05/strona_internetowa_dla_branzy_beauty.webp',
          'url' => '/strony-internetowe-dla-salonow-beauty/',
        ],
        [
          'num' => '04',
          'icon' => 'factory',
          'title' => 'Strony i sklepy dla producentów',
          'text' => 'Katalog produktów, zapytania ofertowe, B2B i sprzedaż online.',
          'image' => 'https://zaprojektowani.com/wp-content/uploads/2026/05/strony_i_sklepy_dla_producentow.webp',
          'url' => '/sklep-internetowy-dla-producenta/',
        ],
      ];
    ?>
    <div class="zpHomeSeo__grid" role="group" aria-label="Branże i kierunki pozycjonowania">
      <?php foreach ($zp_home_industries as $industry) : ?>
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

  </div>
</section>
