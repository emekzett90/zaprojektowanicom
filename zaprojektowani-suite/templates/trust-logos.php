<!-- =========================================================
ZAPROJEKTOWANI — TRUST LOGOS / PINNED LOGO REVEAL + MATEUSZ v5.0 MOBILE FASTER + BIGGER LOGOS
Zaprojektowani Suite template / v0.2 assets moved to /assets/css/blocks and /assets/js/blocks

v5.0:
- desktop zostaje jak v4.8/v4.9: pinned/sticky, scroll reveal, Mateusz po prawej, progress, watermark
- mobile/tablet do 980px: całkowicie wyłączony pinned/sticky scroll-track, żeby nie skakał viewport
- mobile: sekcja jest zwykłym statycznym blokiem, bez 420vh, bez position: sticky, bez scroll-driven JS
- mobile: jedno duże logo zmienia się szybciej automatycznym timerem, bez liczenia scrolla i bez scrollTo
- mobile: logotypy powiększone o ok. 25% względem v4.9
- mobile: resize paska adresu nie przelicza wysokości sekcji i nie rusza viewportu
- Lucide przez data-lucide, bez zewnętrznych bibliotek
========================================================= -->

<?php $zp_projects_count = (int) zp_suite_opt('stats.projects_count', zp_suite_opt('hero.projects_count', 114)); $zp_google_rating = zp_suite_opt('stats.google_rating','5.0'); $zp_recommendations = zp_suite_opt('stats.recommendations','120'); ?>
<section class="zpTrustPinned" id="zpTrustPinned" data-zp-trust-pinned aria-labelledby="zpTrustPinnedTitle">
  <div class="zpTrustPinned__spacer" data-zp-trust-spacer>
    <div class="zpTrustPinned__sticky" data-zp-trust-sticky>
      <div class="zpTrustPinned__inner">

        <header class="zpTrustPinned__head">
          <div class="zpTrustPinned__copy">
            <span class="zpTrustPinned__kicker"><?php echo esc_html(zp_suite_opt('trust_logos.eyebrow','Zaufali nam')); ?></span>

            <h2 id="zpTrustPinnedTitle" class="zpTrustPinned__title"><?php echo wp_kses(zp_suite_opt('trust_logos.heading','Marki, które powierzyły nam swój <span>wizerunek</span>'), zp_suite_allowed_html()); ?></h2>
          </div>

          <div class="zpTrustPinned__leadBox">
            <nav class="zpTrustPinned__pills" aria-label="Zakres usług Zaprojektowani">
              <a href="/strony-internetowe-katowice/"><i data-lucide="monitor-smartphone"></i> Strony www</a>
              <a href="/sklepy-internetowe-katowice/"><i data-lucide="shopping-bag"></i> Sklepy internetowe</a>
              <a href="/logo-branding-katowice/"><i data-lucide="pen-tool"></i> Branding</a>
              <a href="/kampanie-reklamowe/"><i data-lucide="megaphone"></i> Kampanie</a>
            </nav>

            <p class="zpTrustPinned__lead"><?php echo esc_html(zp_suite_opt('trust_logos.lead','Projektujemy identyfikacje wizualne, strony internetowe, sklepy i kampanie dla firm, które chcą wyglądać profesjonalnie od pierwszego kontaktu.')); ?></p>
          </div>
        </header>

        <div class="zpTrustPinned__stats" role="group" aria-label="Dowody zaufania">
          <div class="zpTrustPinned__stat">
            <i class="zpTrustPinned__statIcon" data-lucide="briefcase-business" aria-hidden="true"></i>
            <strong><?php echo esc_html($zp_projects_count); ?></strong>
            <span>realizacji</span>
          </div>

          <div class="zpTrustPinned__stat">
            <i class="zpTrustPinned__statIcon" data-lucide="star" aria-hidden="true"></i>
            <strong><?php echo esc_html($zp_google_rating); ?></strong>
            <span>Google</span>
          </div>

          <div class="zpTrustPinned__stat">
            <i class="zpTrustPinned__statIcon" data-lucide="thumbs-up" aria-hidden="true"></i>
            <strong><?php echo esc_html($zp_recommendations); ?></strong>
            <span>poleceń</span>
          </div>
        </div>

        <div class="zpTrustPinned__progress" aria-hidden="true">
          <span data-zp-trust-current>01</span>
          <i><b data-zp-trust-bar></b></i>
          <span data-zp-trust-total><?php echo esc_html(max(1, count(array_values(array_filter(zp_suite_cms_get('logos', []), function($l){ return !isset($l['visible']) || (string)$l['visible'] !== '0'; }))))); ?></span>
        </div>

        <div class="zpTrustPinned__scene" data-zp-trust-scene>

          <div class="zpTrustPinned__logos" role="group" aria-label="Logotypy klientów">
            <?php
            $zp_logos = array_values(array_filter(zp_suite_cms_get('logos', []), function($l){ return !isset($l['visible']) || (string)$l['visible'] !== '0'; }));
            /* v2.2.393 — Vista i Apartament Piękna zostają w sekwencji; usuwamy tylko tło/karty CSS-em. */
            /* v2.2.88 — pełne logotypy WEBP z CMS; CTA na desktopie pokazują się dopiero po ostatnim logo, mobile bez CTA. */
            $half = max(1, (int)ceil(count($zp_logos)/2));
            $rows = [array_slice($zp_logos,0,$half), array_slice($zp_logos,$half)];
            ?>
            <?php foreach ($rows as $row_i => $row) : ?>
              <div class="zpTrustPinned__row <?php echo $row_i === 0 ? 'zpTrustPinned__row--one' : 'zpTrustPinned__row--two'; ?>">
                <?php foreach ($row as $logo) : ?>
                  <?php
                    $zp_logo_name_l = strtolower((string)($logo['name'] ?? ''));
                    $zp_logo_img_l = strtolower((string)($logo['image'] ?? ''));
                    $zp_logo_classes = ['zpTrustPinned__logo'];
                    if (strpos($zp_logo_name_l, 'proscarves') !== false || strpos($zp_logo_name_l, 'siemianowski') !== false || strpos($zp_logo_name_l, 'polerstone') !== false || strpos($zp_logo_name_l, 'piotr') !== false) {
                      $zp_logo_classes[] = 'zpTrustPinned__logo--wide';
                    }
                    if (strpos($zp_logo_name_l, 'gravia') !== false || strpos($zp_logo_name_l, 'raxo') !== false || strpos($zp_logo_name_l, 'vista') !== false || strpos($zp_logo_img_l, 'sfera') !== false) {
                      $zp_logo_classes[] = 'zpTrustPinned__logo--compact';
                    }
                    if (strpos($zp_logo_name_l, 'apartament') !== false || strpos($zp_logo_img_l, '/ap.webp') !== false) {
                      $zp_logo_classes[] = 'zpTrustPinned__logo--scale115';
                    }
                    if (strpos($zp_logo_name_l, 'siemianowski') !== false || strpos($zp_logo_name_l, 'kami') !== false || strpos($zp_logo_img_l, 'siemianowski') !== false || strpos($zp_logo_img_l, 'kaminski') !== false) {
                      $zp_logo_classes[] = 'zpTrustPinned__logo--scale115';
                    }
                    if (strpos($zp_logo_name_l, 'prisma') !== false || strpos($zp_logo_name_l, 'gravia') !== false || strpos($zp_logo_img_l, 'prisma_dent') !== false || strpos($zp_logo_img_l, 'gravia') !== false) {
                      $zp_logo_classes[] = 'zpTrustPinned__logo--scale090';
                    }
                  ?>
                  <div class="<?php echo esc_attr(implode(' ', $zp_logo_classes)); ?>" data-zp-logo data-logo-name="<?php echo esc_attr($logo['name'] ?? 'Logo klienta'); ?>" style="--zp-logo-url:url('<?php echo esc_url($logo['image'] ?? ''); ?>')">
                    <?php if (!empty($logo['url'])) : ?><a href="<?php echo esc_url($logo['url']); ?>" target="_blank" rel="noopener"><?php endif; ?>
                    <img class="zpTrustPinned__logoImg" src="<?php echo esc_url($logo['image'] ?? ''); ?>" alt="<?php echo esc_attr($logo['name'] ?? 'Logo klienta'); ?>" loading="lazy" decoding="async" fetchpriority="low" data-zp-full-logo="1">
                    <?php if (!empty($logo['url'])) : ?></a><?php endif; ?>
                  </div>
                <?php endforeach; ?>
                <?php /* v2.2.388 — mobile marquee needs a second, non-indexed copy for seamless infinite rows. Hidden on desktop. */ ?>
                <?php foreach ($row as $logo) : ?>
                  <?php
                    $zp_logo_name_l = strtolower((string)($logo['name'] ?? ''));
                    $zp_logo_img_l = strtolower((string)($logo['image'] ?? ''));
                    $zp_logo_classes = ['zpTrustPinned__logo','zpTrustPinned__logo--clone'];
                    if (strpos($zp_logo_name_l, 'proscarves') !== false || strpos($zp_logo_name_l, 'siemianowski') !== false || strpos($zp_logo_name_l, 'polerstone') !== false || strpos($zp_logo_name_l, 'piotr') !== false) {
                      $zp_logo_classes[] = 'zpTrustPinned__logo--wide';
                    }
                    if (strpos($zp_logo_name_l, 'gravia') !== false || strpos($zp_logo_name_l, 'raxo') !== false || strpos($zp_logo_name_l, 'vista') !== false || strpos($zp_logo_img_l, 'sfera') !== false) {
                      $zp_logo_classes[] = 'zpTrustPinned__logo--compact';
                    }
                    if (strpos($zp_logo_name_l, 'apartament') !== false || strpos($zp_logo_img_l, '/ap.webp') !== false) {
                      $zp_logo_classes[] = 'zpTrustPinned__logo--scale115';
                    }
                    if (strpos($zp_logo_name_l, 'siemianowski') !== false || strpos($zp_logo_name_l, 'kami') !== false || strpos($zp_logo_img_l, 'siemianowski') !== false || strpos($zp_logo_img_l, 'kaminski') !== false) {
                      $zp_logo_classes[] = 'zpTrustPinned__logo--scale115';
                    }
                    if (strpos($zp_logo_name_l, 'prisma') !== false || strpos($zp_logo_name_l, 'gravia') !== false || strpos($zp_logo_img_l, 'prisma_dent') !== false || strpos($zp_logo_img_l, 'gravia') !== false) {
                      $zp_logo_classes[] = 'zpTrustPinned__logo--scale090';
                    }
                  ?>
                  <div class="<?php echo esc_attr(implode(' ', $zp_logo_classes)); ?>" aria-hidden="true" style="--zp-logo-url:url('<?php echo esc_url($logo['image'] ?? ''); ?>')">
                    <img class="zpTrustPinned__logoImg" src="<?php echo esc_url($logo['image'] ?? ''); ?>" alt="" loading="lazy" decoding="async" fetchpriority="low" data-zp-full-logo="1">
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endforeach; ?>
            <div class="zpTrustPinned__logoCtas" aria-label="Następny krok">
              <a class="zpTrustPinned__logoBtn zpTrustPinned__logoBtn--primary" href="/studio-wyceny/">
                <i data-lucide="sparkles" aria-hidden="true"></i>
                <span>Otrzymaj wycenę</span>
              </a>
              <a class="zpTrustPinned__logoBtn zpTrustPinned__logoBtn--ghost" href="/realizacje/">
                <i data-lucide="layout-grid" aria-hidden="true"></i>
                <span>Zobacz realizacje</span>
              </a>
            </div>
          </div>

          <?php /* v2.2.390 — mobile-only CodePen-style background carousel. Desktop uses original markup above. */ ?>
          <div class="zpTrustMobileCodepen" aria-label="Logotypy klientów — przewijane" role="group">
            <?php
              $zp_mobile_rows = [$rows[0] ?? [], $rows[1] ?? []];
              foreach ($zp_mobile_rows as $zp_mobile_row_i => $zp_mobile_row) :
                if (empty($zp_mobile_row)) { continue; }
            ?>
              <div class="carousel carousel--<?php echo $zp_mobile_row_i === 0 ? 'left' : 'right'; ?>">
                <div class="logos">
                  <div class="logos__track">
                    <?php for ($zp_repeat = 0; $zp_repeat < 4; $zp_repeat++) : ?>
                      <?php foreach ($zp_mobile_row as $logo) : ?>
                        <span class="logos__item" data-logo-name="<?php echo esc_attr($logo['name'] ?? 'Logo klienta'); ?>" aria-hidden="true">
                          <img
                            src="<?php echo esc_url($logo['image'] ?? ''); ?>"
                            alt=""
                            loading="lazy"
                            decoding="async"
                            fetchpriority="low"
                          >
                        </span>
                      <?php endforeach; ?>
                    <?php endfor; ?>
                  </div>
                </div>
                <div class="mask" aria-hidden="true"></div>
              </div>
            <?php endforeach; ?>
          </div>

          <figure class="zpTrustPinned__person" aria-hidden="true" data-zp-person>
            <img
              src="https://zaprojektowani.com/wp-content/uploads/2026/05/mateusz_nowy.webp"
              alt=""
              loading="lazy"
              decoding="async"
            >
          </figure>

        </div>

        <div class="zpTrustPinned__mark" aria-hidden="true">customers</div>

        <div class="zpTrustPinned__hint" aria-hidden="true">
          <span>Nie wiesz, od czego zacząć?</span>
          <i></i>
          <em data-lucide="arrow-down"></em>
        </div>

      </div>
    </div>
  </div>
</section>





<!-- =========================================================
/ZAPROJEKTOWANI — TRUST LOGOS / PINNED LOGO REVEAL + MATEUSZ v5.0 MOBILE FASTER + BIGGER LOGOS
========================================================= -->
