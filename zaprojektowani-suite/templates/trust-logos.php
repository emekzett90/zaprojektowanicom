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

<?php $zp_projects_count = function_exists('zp_seo_plan_projects_count') ? zp_seo_plan_projects_count() : (int) zp_suite_opt('stats.projects_count', zp_suite_opt('hero.projects_count', 114)); $zp_trust_en = function_exists('zp_home_refresh_english') && zp_home_refresh_english(); $zp_trust_aggregate = zp_reviews_aggregate(); $zp_google_rating = number_format($zp_trust_aggregate['rating'], 1, $zp_trust_en ? '.' : ',', ''); $zp_recommendations = $zp_trust_aggregate['count']; $zp_trust_url = 'https://www.trustindex.io/reviews/zaprojektowani.com'; $zp_fb_reviews = zp_reviews_snapshot()['facebookVerified'] ?? []; $zp_fb_percent = (int) ($zp_fb_reviews['recommendPercent'] ?? 100); $zp_fb_url = 'https://www.facebook.com/zaprojektowanicom/reviews'; ?>
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

        <?php /* Mat 21.10.2026, point 8: the figures under the hero as cards, the Trustindex card with its logo and stars. Every text sits in its own element, so the EN dictionary keys stay plain. 22.10: 114 realizacji links to Realizacje, the review count sits under the rating, the third card is Facebook's share of recommendations (facebookVerified in the reviews JSON). */ ?>
        <div class="zpTrustPinned__stats zpTrustProof" role="group" aria-label="Dowody zaufania">
          <a class="zpTrustProof__item zpTrustProof__item--projects" href="/realizacje/">
            <span class="zpTrustProof__top"><span class="zpTrustProof__icon" aria-hidden="true"><i data-lucide="briefcase-business"></i></span><span class="zpTrustProof__open" aria-hidden="true">↗</span></span>
            <span class="zpTrustProof__body"><strong class="zpTrustProof__num"><?php echo esc_html($zp_projects_count); ?></strong><span class="zpTrustProof__label">realizacji</span></span>
          </a>

          <a class="zpTrustProof__item zpTrustProof__item--rating" href="<?php echo esc_url($zp_trust_url); ?>" target="_blank" rel="noopener nofollow">
            <span class="zpTrustProof__top"><img class="zpTrustProof__logo" src="<?php echo esc_url(ZP_SUITE_URL . 'assets/img/trustindex-wordmark-white.webp'); ?>" alt="Trustindex" width="446" height="78" loading="lazy" decoding="async"><span class="zpTrustProof__open" aria-hidden="true">↗</span></span>
            <span class="zpTrustProof__body"><span class="zpTrustProof__value"><strong class="zpTrustProof__num"><?php echo esc_html($zp_google_rating); ?><small>/5</small></strong><span class="zpTrustProof__stars" aria-hidden="true">★★★★★</span></span><span class="zpTrustProof__label">średnia z <?php echo esc_html($zp_recommendations); ?> opinii</span></span>
          </a>

          <a class="zpTrustProof__item zpTrustProof__item--reviews" href="<?php echo esc_url($zp_fb_url); ?>" target="_blank" rel="noopener nofollow">
            <span class="zpTrustProof__top"><span class="zpTrustProof__icon zpTrustProof__icon--facebook"><svg viewBox="0 0 24 24" width="32" height="32" role="img" aria-label="Facebook" focusable="false"><path fill="#0866FF" d="M9.101 23.691v-7.98H6.627v-3.667h2.474v-1.58c0-4.085 1.848-5.978 5.858-5.978.401 0 .955.042 1.468.103a8.68 8.68 0 0 1 1.141.195v3.325a8.623 8.623 0 0 0-.653-.036 26.805 26.805 0 0 0-.733-.009c-.707 0-1.259.096-1.675.309a1.686 1.686 0 0 0-.679.622c-.258.42-.374.995-.374 1.752v1.297h3.919l-.386 2.103-.287 1.564h-3.246v8.245C19.396 23.238 24 18.179 24 12.044c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.628 3.874 10.35 9.101 11.647Z"/></svg></span><span class="zpTrustProof__open" aria-hidden="true">↗</span></span>
            <span class="zpTrustProof__body"><strong class="zpTrustProof__num"><?php echo esc_html($zp_fb_percent); ?>%</strong><span class="zpTrustProof__label">pozytywnych opinii</span></span>
          </a>
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

<style id="zp-trust-reliable-start-26">
html body .zpTrustPinned:not([data-zp-trust-pinned-ready="1"]) .zpTrustPinned__head,html body .zpTrustPinned:not([data-zp-trust-pinned-ready="1"]) .zpTrustPinned__stats{opacity:1!important;transform:none!important;filter:none!important}
</style>
<noscript><style>html body .zpTrustPinned .zpTrustPinned__spacer{min-height:0!important;height:auto!important}html body .zpTrustPinned .zpTrustPinned__sticky{position:relative!important;height:auto!important;min-height:0!important}html body .zpTrustPinned .zpTrustPinned__head,html body .zpTrustPinned .zpTrustPinned__stats,html body .zpTrustPinned .zpTrustPinned__logo{opacity:1!important;visibility:visible!important;transform:none!important}html body .zpTrustPinned .zpTrustPinned__logo--clone,html body .zpTrustPinned .zpTrustPinned__person,html body .zpTrustPinned .zpTrustPinned__mobileMarquee{display:none!important}</style></noscript>
