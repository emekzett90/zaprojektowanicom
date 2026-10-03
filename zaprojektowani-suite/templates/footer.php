<!-- =========================================================
ZAPROJEKTOWANI — MEGA FOOTER / DARK CTA + TEAM PHOTO + WHITE LINKS + DARK BLOG v1.7 CLEAN FINAL
Zaprojektowani Suite template / v0.2 assets moved to /assets/css/blocks and /assets/js/blocks

v1.7:
- podmienione zdjęcie zespołu:
  https://zaprojektowani.com/wp-content/uploads/2026/05/team_zaprojektowani_finalnie.webp
- osobne ustawienia zdjęcia dla desktop / tablet / mobile
  X/Y/scale/width/height
- zdjęcie zespołu po prawej może wychodzić poza kontener CTA
- mocno zmniejszone ikony Lucide w nawigacji stopki, także po init Lucide
- powiększone nazwy kategorii stopki
- mniejsze strzałki w pozycjach menu
- naprawione ucięte "Czytaj wpis" przed hoverem na kartach bloga
- dark CTA + white links + dark blog
- lokalny Lucide: /wp-content/web-font/lucide.min.js
========================================================= -->


<?php
$zp_footer_cta_eyebrow = zp_suite_opt('footer.cta_eyebrow','Porozmawiajmy o projekcie');
$zp_footer_cta_heading = zp_suite_opt('footer.cta_heading','Masz pomysł na stronę, sklep albo markę? <span>Zamieńmy go w projekt, który wygląda premium i realnie sprzedaje.</span>');
$zp_footer_cta_lead = zp_suite_opt('footer.cta_lead','');
$zp_footer_cta_primary_text = zp_suite_opt('footer.cta_primary_text','Otrzymaj wycenę');
$zp_footer_cta_primary_url = zp_suite_opt('footer.cta_primary_url','/studio-wyceny/');
$zp_footer_cta_secondary_text = zp_suite_opt('footer.cta_secondary_text','Napisz na WhatsApp');
$zp_footer_team_photo = zp_suite_opt('footer.team_photo','https://zaprojektowani.com/wp-content/uploads/2026/05/team_zaprojektowani_finalnie.webp');
$zp_footer_about = zp_suite_opt('footer.about','');
$zp_brand_whatsapp = zp_suite_opt('brand.whatsapp','48501054253');
$zp_logo_light = 'https://zaprojektowani.com/wp-content/uploads/2026/05/ZP_CIEMNE_BEZ_CIENIA-scaled.webp'; /* v2.2.669: pelne ciemne logo w stopce (bylo: sygnet) */
?>
<?php if (!defined('ZP_SUITE_FOOTER_GAP_185_PRINTED')) { define('ZP_SUITE_FOOTER_GAP_185_PRINTED', true); ?>
<style id="zp-suite-footer-pre-gap-185">html body .zpFooterPreGap{display:block!important;width:100%!important;height:250px!important;min-height:250px!important;max-height:250px!important;margin:0!important;padding:0!important;background:#fff!important;background-image:none!important;position:relative!important;z-index:0!important;pointer-events:none!important;overflow:hidden!important;box-sizing:border-box!important}html body .zpMegaFooter{position:relative!important;z-index:20!important;overflow:visible!important;clip-path:none!important;contain:initial!important;isolation:auto!important}html body .zpMegaFooter,html body .zpMegaFooter__darkBand,html body .zpMegaFooter__darkBand--cta,html body .zpMegaFooter__inner--cta,html body .zpMegaFooter__cta,html body .zpMegaFooter__teamPhoto{overflow:visible!important;clip-path:none!important;contain:initial!important}html body .zpMegaFooter__teamPhoto{z-index:80!important}html body #zpbsUltimate .zpbsSticky{position:fixed!important;z-index:2147483647!important;isolation:isolate!important;pointer-events:auto!important}@media(max-width:760px){html body .zpFooterPreGap{height:160px!important;min-height:160px!important;max-height:160px!important}}</style>
<?php } ?>
<div class="zpFooterPreGap" aria-hidden="true"></div>
<footer class="zpMegaFooter zpMegaFooter--subpage" id="zpMegaFooter" aria-label="Stopka Zaprojektowani.com">
<!-- DARK CTA FULL BLEED -->
  <section class="zpMegaFooter__darkBand zpMegaFooter__darkBand--cta" aria-labelledby="zpMegaFooterCtaTitle">
    <div class="zpMegaFooter__darkBg" aria-hidden="true"></div>
    <div class="zpMegaFooter__mark zpMegaFooter__mark--dark" aria-hidden="true">zaprojektowani</div>

    <div class="zpMegaFooter__inner zpMegaFooter__inner--cta">
      <div class="zpMegaFooter__cta">
        <div class="zpMegaFooter__ctaCopy">
          <span class="zpMegaFooter__eyebrow zpMegaFooter__eyebrow--dark"><?php echo esc_html($zp_footer_cta_eyebrow); ?></span>

          <h2 id="zpMegaFooterCtaTitle"><?php echo wp_kses($zp_footer_cta_heading, zp_suite_allowed_html()); ?></h2>

          <p><?php echo esc_html($zp_footer_cta_lead); ?></p>
        </div>

        <figure class="zpMegaFooter__teamPhoto" aria-hidden="true">
          <img
            src="<?php echo esc_url($zp_footer_team_photo); ?>"
            alt=""
            loading="lazy"
            decoding="async"
          >
        </figure>

        <div class="zpMegaFooter__ctaActions">
          <a class="zpMegaFooter__btn zpMegaFooter__btn--light" href="<?php echo esc_url($zp_footer_cta_primary_url); ?>">
            <span><?php echo esc_html($zp_footer_cta_primary_text); ?></span>
            <i data-lucide="arrow-up-right"></i>
          </a>

          <a class="zpMegaFooter__btn zpMegaFooter__btn--glass" href="https://wa.me/<?php echo esc_attr($zp_brand_whatsapp); ?>" target="_blank" rel="noopener">
            <span><?php echo esc_html($zp_footer_cta_secondary_text); ?></span>
            <i data-lucide="message-circle"></i>
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- WHITE MIDDLE -->
  <section class="zpMegaFooter__whiteBand" aria-label="Linki i kontakt">
    <div class="zpMegaFooter__whiteBg" aria-hidden="true"></div>

    <div class="zpMegaFooter__inner zpMegaFooter__inner--whiteMiddle" style="width:min(1740px, calc(100% - clamp(28px, 6vw, 112px))); max-width:1740px; margin-left:auto; margin-right:auto;">
      <section class="zpMegaFooter__main" aria-label="Główna nawigacja stopki">

        <div class="zpMegaFooter__brandCol">
          <div class="zpMegaFooter__brandIntro">
            <a class="zpMegaFooter__logo" href="/" aria-label="Zaprojektowani.com — strona główna">
              <img
                src="<?php echo esc_url($zp_logo_light); ?>"
                alt="Zaprojektowani.com"
                loading="lazy"
                decoding="async"
              >
            </a>

            <p class="zpMegaFooter__about"><?php echo esc_html($zp_footer_about); ?></p>

            <div class="zpMegaFooter__socials" role="group" aria-label="Zaprojektowani w sieci">
              <a href="https://www.instagram.com/zaprojektowanicom" target="_blank" rel="noopener" aria-label="Instagram" title="Instagram"><?php echo zp_footer_v3_icon('instagram'); ?></a>
              <a href="https://www.facebook.com/zaprojektowanicom" target="_blank" rel="noopener" aria-label="Facebook" title="Facebook"><?php echo zp_footer_v3_icon('facebook'); ?></a>
              <a href="https://search.google.com/local/reviews?placeid=<?php echo esc_attr(ZP_FOOTER_V3_PLACE_ID); ?>" target="_blank" rel="noopener" aria-label="Opinie w Google" title="Opinie w Google"><?php echo zp_footer_v3_icon('google'); ?></a>
              <a href="https://wa.me/<?php echo esc_attr($zp_brand_whatsapp); ?>" target="_blank" rel="noopener" aria-label="WhatsApp" title="WhatsApp"><?php echo zp_footer_v3_icon('whatsapp'); ?></a>
            </div>
          </div>

          <address class="zpMegaFooter__contactCards" aria-label="Kontakt">
            <a href="tel:+48501054253" class="zpMegaFooter__contactCard">
              <i data-lucide="phone-call"></i>
              <span>
                <em>Telefon</em>
                <strong>+48 501 054 253</strong>
              </span>
            </a>

            <a href="mailto:kontakt@zaprojektowani.com" class="zpMegaFooter__contactCard">
              <i data-lucide="mail"></i>
              <span>
                <em>E-mail</em>
                <strong>kontakt@zaprojektowani.com</strong>
              </span>
            </a>

            <a href="https://www.google.com/maps/place/?q=place_id:<?php echo esc_attr(ZP_FOOTER_V3_PLACE_ID); ?>" target="_blank" rel="noopener" class="zpMegaFooter__contactCard zpMegaFooter__contactCard--company">
              <i data-lucide="map-pin"></i>
              <span>
                <em>Biuro w Katowicach</em>
                <strong>ul. Modelarska 18/2, 40-142 Katowice</strong>
                <small>Pon.–pt. 9:00–17:00 · NIP 9930682613</small>
              </span>
            </a>
          </address>
        </div>

        <nav class="zpMegaFooter__links zpFootNav" aria-label="Mapa serwisu">
          <?php foreach (zp_footer_v3_groups() as $zp_fg_key => $zp_fg) : $zp_fg_id = 'zpFootNav-' . $zp_fg_key; ?>
          <div class="zpFootNav__group<?php echo $zp_fg['open'] ? ' is-open' : ''; ?>" data-zp-foot-group>
            <p class="zpFootNav__title" id="<?php echo esc_attr($zp_fg_id); ?>-title"><?php echo esc_html($zp_fg['title']); ?></p>
            <ul class="zpFootNav__list" id="<?php echo esc_attr($zp_fg_id); ?>" aria-labelledby="<?php echo esc_attr($zp_fg_id); ?>-title">
              <?php foreach ($zp_fg['items'] as $zp_fi) : $zp_fi_ext = !empty($zp_fi['external']); ?>
              <li><a class="zpFootNav__link<?php echo $zp_fi_ext ? ' zpFootNav__link--ext' : ''; ?>" href="<?php echo esc_url($zp_fi['href']); ?>"<?php echo $zp_fi_ext ? ' target="_blank" rel="noopener"' : ''; ?>><span><?php echo esc_html($zp_fi['text']); ?></span></a><?php if (!empty($zp_fi['price'])) : ?><span class="zpFootNav__price"><?php echo esc_html(zp_footer_v3_price($zp_fi['price'])); ?></span><?php endif; ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
          <?php endforeach; ?>
        </nav>
      </section>

      <section class="zpFootLocal" aria-label="Strony lokalne">
        <p class="zpFootLocal__text"><span class="zpFootLocal__pin"><?php echo zp_footer_v3_icon('pin'); ?></span><span><strong>Studio w Katowicach.</strong> <span>Projektujemy dla firm z całej Polski.</span></span></p>
        <ul class="zpFootLocal__links">
          <?php foreach (zp_footer_v3_local_links() as $zp_fl) : ?>
          <li><a href="<?php echo esc_url($zp_fl['path']); ?>" data-zp-local="1"><?php echo esc_html($zp_fl['text']); ?></a></li>
          <?php endforeach; ?>
        </ul>
      </section>
    </div>
  </section>

  <!-- DARK BLOG FULL BLEED -->
  <section class="zpMegaFooter__darkBand zpMegaFooter__darkBand--blog" aria-labelledby="zpMegaFooterBlogTitle">
    <div class="zpMegaFooter__darkBg" aria-hidden="true"></div>
    <div class="zpMegaFooter__mark zpMegaFooter__mark--blog" aria-hidden="true">blog</div>

    <div class="zpMegaFooter__inner">
      <div class="zpMegaFooter__blogHead">
        <div>
          <span class="zpMegaFooter__eyebrow zpMegaFooter__eyebrow--dark">Wiedza i poradniki</span>
          <h2 id="zpMegaFooterBlogTitle">Ostatnie wpisy na blogu</h2>
          <p class="zpMegaFooter__blogLead">Praktycznie o stronach, e-commerce, brandingu, SEO i reklamach — konkretnie, bez lania wody.</p>
        </div>

        <div class="zpMegaFooter__blogActions">
          <button class="zpMegaFooter__blogNav" type="button" data-zp-footer-posts-prev aria-label="Przewiń wpisy w lewo"><i data-lucide="arrow-left"></i></button>
          <button class="zpMegaFooter__blogNav" type="button" data-zp-footer-posts-next aria-label="Przewiń wpisy w prawo"><i data-lucide="arrow-right"></i></button>
          <a href="/wiedza/" class="zpMegaFooter__blogAll">
            <span>Zobacz wszystkie wpisy</span>
            <i data-lucide="arrow-right"></i>
          </a>
        </div>
      </div>

      <?php
      $zp_footer_posts = get_posts([
        'post_type'           => 'post',
        'post_status'         => 'publish',
        'posts_per_page'      => 10,
        'ignore_sticky_posts' => true,
        'orderby'             => 'date',
        'order'               => 'DESC',
      ]);
      ?>

      <div class="zpMegaFooter__posts" data-zp-footer-posts>
        <?php if (!empty($zp_footer_posts)) : ?>
          <?php foreach ($zp_footer_posts as $zp_post) :
            $zp_post_id = $zp_post->ID;
            $zp_permalink = get_permalink($zp_post_id);
            $zp_title = get_the_title($zp_post_id);
            $zp_excerpt = get_the_excerpt($zp_post_id);
            if (!$zp_excerpt) {
              $zp_excerpt = wp_trim_words(wp_strip_all_tags($zp_post->post_content), 22, '…');
            } else {
              $zp_excerpt = wp_trim_words(wp_strip_all_tags($zp_excerpt), 22, '…');
            }
            $zp_thumb = get_the_post_thumbnail_url($zp_post_id, 'large');
            if (!$zp_thumb) {
              $zp_thumb = 'https://zaprojektowani.com/wp-content/uploads/2026/05/kiedy-warto-zalozyc-sklep-2-scaled.webp';
            }
            $zp_cats = get_the_category($zp_post_id);
            $zp_cat_names = [];
            if (!empty($zp_cats) && !is_wp_error($zp_cats)) {
              foreach (array_slice($zp_cats, 0, 2) as $zp_cat) {
                $zp_cat_names[] = $zp_cat->name;
              }
            }
            $zp_meta = $zp_cat_names ? implode(' • ', $zp_cat_names) : 'Wiedza';
            $zp_words = str_word_count(wp_strip_all_tags($zp_post->post_content));
            $zp_read_min = max(3, (int) ceil($zp_words / 190));
          ?>
            <article class="zpMegaFooter__post">
              <a href="<?php echo esc_url($zp_permalink); ?>">
                <img
                  src="<?php echo esc_url($zp_thumb); ?>"
                  alt="<?php echo esc_attr($zp_title); ?>"
                  loading="lazy"
                  decoding="async"
                >
                <div class="zpMegaFooter__postOverlay"></div>
                <div class="zpMegaFooter__postContent">
                  <div class="zpMegaFooter__postTop">
                    <span class="zpMegaFooter__postMeta"><?php echo esc_html($zp_meta); ?></span>
                    <span class="zpMegaFooter__reads"><i data-lucide="calendar-days"></i> <?php echo esc_html(get_the_date('d.m.Y', $zp_post_id)); ?></span>
                  </div>

                  <h3><?php echo esc_html($zp_title); ?></h3>

                  <p><?php echo esc_html($zp_excerpt); ?></p>

                  <div class="zpMegaFooter__postMore">
                    <span><i data-lucide="clock-4"></i> ~<?php echo esc_html($zp_read_min); ?> min czytania</span>
                    <span><i data-lucide="sparkles"></i> poradnik</span>
                  </div>

                  <em>Czytaj wpis <i data-lucide="arrow-up-right"></i></em>
                </div>
              </a>
            </article>
          <?php endforeach; ?>
        <?php else : ?>
          <article class="zpMegaFooter__post">
            <a href="/wiedza/">
              <img src="https://zaprojektowani.com/wp-content/uploads/2026/05/kiedy-warto-zalozyc-sklep-2-scaled.webp" alt="Wiedza Zaprojektowani" loading="lazy" decoding="async">
              <div class="zpMegaFooter__postOverlay"></div>
              <div class="zpMegaFooter__postContent">
                <div class="zpMegaFooter__postTop"><span class="zpMegaFooter__postMeta">Wiedza</span><span class="zpMegaFooter__reads"><i data-lucide="calendar-days"></i> blog</span></div>
                <h3>Ostatnie wpisy na blogu</h3>
                <p>Zobacz poradniki o stronach internetowych, sklepach, brandingu, SEO i reklamach.</p>
                <div class="zpMegaFooter__postMore"><span><i data-lucide="clock-4"></i> poradniki</span><span><i data-lucide="sparkles"></i> Zaprojektowani</span></div>
                <em>Czytaj wpisy <i data-lucide="arrow-up-right"></i></em>
              </div>
            </a>
          </article>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <!-- DARK BOTTOM / CONTINUATION OF BLOG -->
  <section class="zpMegaFooter__bottomBand" aria-label="Informacje prawne">
    <div class="zpMegaFooter__inner">
      <div class="zpMegaFooter__bottom">
        <p>
          © <span data-zp-year>2026</span> Zaprojektowani.com. Wszystkie prawa zastrzeżone.
        </p>

        <div class="zpMegaFooter__bottomLinks">
          <a href="/polityka-prywatnosci/">Polityka prywatności</a>
          <a href="/cookies/">Cookies</a>
          <a
            href="/cookies/#cookie-settings"
            data-zp-cookie-settings
            aria-label="Ustawienia cookies"
          >Ustawienia cookies</a>
          <a href="/regulamin/">Regulamin</a>
          <a href="/rodo/">RODO</a>
        </div>

        <button class="zpMegaFooter__toTop" type="button" data-zp-footer-top aria-label="Wróć na górę strony">
          <i data-lucide="arrow-up"></i>
        </button>
      </div>
    </div>
  </section>
</footer>





<!-- =========================================================
/ZAPROJEKTOWANI — MEGA FOOTER / DARK CTA + TEAM PHOTO + WHITE LINKS + DARK BLOG v1.7 CLEAN FINAL
========================================================= -->