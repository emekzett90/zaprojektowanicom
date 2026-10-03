<?php

/**
 * ZAPROJEKTOWANI — REVIEWS EDITORIAL / GOOGLE + FACEBOOK v2.7 MOBILE STABLE + SCORE FIX
 * Shortcode: [zp_reviews_section]
 *
 * v2.7:
 * - FIX: równy shine na liczbie 5.0 — pseudo-element ma identyczne metryki fontu jak główna liczba
 * - 5.0 pogrubione o ok. +300 względem poprzedniej wersji: 560 -> 860
 * - mobile scroll-jump fix: reveal na mobile bez transformów/blurów, lżejszy rail, bez JS drag/capture na touch
 * - mobile GPU-safe: wyłączone ciężkie animacje shine/aura/hint/kicker na telefonie
 * - desktop zostawiony wizualnie jak v2.6
 */

if (!defined('ABSPATH')) {
  exit;
}

if (!function_exists('zp_reviews_section_shortcode')) {
  function zp_reviews_section_shortcode() {
    ob_start();
    ?>


<?php
$zp_reviews = array_values(array_filter(zp_suite_cms_get('reviews', []), function($r){ return !isset($r['visible']) || (string)$r['visible'] !== '0'; }));
$zp_featured = $zp_reviews[0] ?? [];
foreach ($zp_reviews as $r) { if (!empty($r['featured'])) { $zp_featured = $r; break; } }
$zp_review_logo = function($source){ return zp_suite_source_icon($source); };
?>
<section class="zpRevEd" id="zpRevEd" data-zp-rev-ed aria-labelledby="zpRevEdTitle">
  <div class="zpRevEd__bgText" aria-hidden="true">reviews</div>

  <div class="zpRevEd__inner">

    <header class="zpRevEd__hero">
      <div class="zpRevEd__heroLeft zpRevEd__reveal" data-reveal-delay="0">
        <span class="zpRevEd__kicker">Opinie klientów</span>

        <h2 id="zpRevEdTitle" class="zpRevEd__title">
          Klienci wracają, bo widzą różnicę w projekcie, komunikacji i efekcie.
        </h2>

        <div class="zpRevEd__scoreBox zpRevEd__reveal" data-reveal-delay="120">
          <span>Średnia ocena</span>

          <div class="zpRevEd__scoreGlow" role="group" aria-label="Średnia ocena 5.0">
            <strong data-score="5.0">5.0</strong>
          </div>

          <em>★★★★★</em>

          <p>
            Opinie z Google i Facebooka potwierdzają, że klienci najczęściej doceniają
            sprawny proces, estetykę, kontakt, terminowość i dopracowanie szczegółów.
          </p>
        </div>
      </div>

      <div class="zpRevEd__heroRight zpRevEd__reveal" data-reveal-delay="100">
        <p class="zpRevEd__intro">
          Wybraliśmy opinie, które najlepiej pokazują, jak wygląda współpraca z Zaprojektowani:
          od strony internetowej i sklepu, przez logo oraz branding, po SEO techniczne,
          kampanie reklamowe, analitykę i bieżące poprawki.
        </p>

        <div class="zpRevEd__platforms" role="group" aria-label="Źródła opinii">
          <a class="zpRevEd__platform zpRevEd__reveal" data-reveal-delay="180" href="https://www.trustindex.io/reviews/zaprojektowani.com" target="_blank" rel="noopener nofollow" aria-label="Zobacz opinie Zaprojektowani w Trustindex">
            <img src="https://zaprojektowani.com/wp-content/uploads/2026/05/google_logo-scaled.webp" alt="Google" loading="lazy" decoding="async">
            <div>
              <strong>5/5</strong>
              <span>opinie z Trustindex</span>
            </div>
          </a>

          <a class="zpRevEd__platform zpRevEd__reveal" data-reveal-delay="240" href="https://www.facebook.com/zaprojektowanicom/reviews" target="_blank" rel="noopener nofollow" aria-label="Zobacz opinie Zaprojektowani na Facebooku">
            <img src="https://zaprojektowani.com/wp-content/uploads/2026/05/fb_logo-scaled.webp" alt="Facebook" loading="lazy" decoding="async">
            <div>
              <strong>5/5</strong>
              <span>rekomendacje Facebook</span>
            </div>
          </a>
        </div>

        <div class="zpRevEd__actions zpRevEd__reveal" data-reveal-delay="300">
          <a href="/kontakt/" class="zpRevEd__btn">
            Porozmawiajmy o projekcie
            <svg viewBox="0 0 24 24" aria-hidden="true">
              <path d="M7 17 17 7"></path>
              <path d="M7 7h10v10"></path>
            </svg>
          </a>

          <a href="/realizacje/" class="zpRevEd__btn zpRevEd__btn--ghost">
            Zobacz realizacje
            <svg viewBox="0 0 24 24" aria-hidden="true">
              <path d="M7 17 17 7"></path>
              <path d="M7 7h10v10"></path>
            </svg>
          </a>
        </div>
      </div>
    </header>

    <div class="zpRevEd__featured zpRevEd__reveal" data-reveal-delay="80" role="group" aria-label="Wyróżniona opinia">
      <div class="zpRevEd__featuredMeta">
        <div class="zpRevEd__avatar"><?php echo esc_html($zp_featured['avatar'] ?? mb_substr($zp_featured['name'] ?? 'ZP',0,2)); ?></div>
        <div>
          <strong><?php echo esc_html($zp_featured['name'] ?? 'Klient Zaprojektowani'); ?></strong>
          <span><?php echo esc_html(($zp_featured['rating'] ?? '5.0') . '/5 · ' . ($zp_featured['source'] ?? 'Google')); ?></span>
        </div>
      </div>

      <blockquote><?php echo esc_html($zp_featured['text'] ?? 'Profesjonalny proces, dobry kontakt i dopracowany efekt końcowy.'); ?></blockquote>

      <div class="zpRevEd__featuredSource">
        <img src="<?php echo esc_url($zp_review_logo($zp_featured['source'] ?? 'Google')); ?>" alt="<?php echo esc_attr($zp_featured['source'] ?? 'Google'); ?>" loading="lazy" decoding="async">
        <span>Opinia z <?php echo esc_html($zp_featured['source'] ?? 'Google'); ?></span>
      </div>
    </div>

    <div class="zpRevEd__quoteList" role="group" aria-label="Wybrane opinie klientów">
      <?php foreach (array_slice($zp_reviews, 0, 3) as $idx => $review) : ?>
        <article class="zpRevEd__quote zpRevEd__reveal" data-reveal-delay="<?php echo esc_attr($idx * 70); ?>">
          <div class="zpRevEd__quoteMeta">
            <strong><?php echo esc_html($review['name'] ?? 'Klient'); ?></strong>
            <span><?php echo esc_html(($review['rating'] ?? '5.0') . '/5 · ' . ($review['source'] ?? 'Google') . (!empty($review['date']) ? ' · ' . $review['date'] : '')); ?></span>
          </div>
          <p><?php echo esc_html($review['text'] ?? ''); ?></p>
          <div class="zpRevEd__quoteSource">
            <img src="<?php echo esc_url($zp_review_logo($review['source'] ?? 'Google')); ?>" alt="<?php echo esc_attr($review['source'] ?? 'Google'); ?>" loading="lazy" decoding="async">
            <span>Opinia z <?php echo esc_html($review['source'] ?? 'Google'); ?></span>
          </div>
        </article>
      <?php endforeach; ?>
    </div>

    <div class="zpRevEd__chips zpRevEd__reveal" data-reveal-delay="120" role="group" aria-label="Najczęściej powtarzające się zalety">
      <span>Profesjonalizm</span>
      <span>Świetny kontakt</span>
      <span>Nowoczesny design</span>
      <span>Spójny branding</span>
      <span>SEO techniczne</span>
      <span>Terminowość</span>
      <span>Szybkie poprawki</span>
      <span>Sklepy internetowe</span>
      <span>Kampanie Ads</span>
      <span>Estetyka premium</span>
    </div>

    <div class="zpRevEd__more zpRevEd__reveal" data-reveal-delay="80">
      <div class="zpRevEd__moreHead">
        <div>
          <span class="zpRevEd__kicker">Więcej głosów klientów</span>
          <h3>Przesuń w prawo i zobacz, co najczęściej pojawia się w opiniach.</h3>
        </div>

        <div class="zpRevEd__dragHint" aria-hidden="true">
          <span>Przewiń</span>
          <i>
            <svg viewBox="0 0 24 24">
              <path d="M5 12h14"></path>
              <path d="m13 6 6 6-6 6"></path>
            </svg>
          </i>
        </div>
      </div>

      <div class="zpRevEd__railWrap">
        <div class="zpRevEd__rail" data-zp-rev-rail>

          <?php foreach ($zp_reviews as $review) : ?>
            <article class="zpRevEd__miniCard">
              <strong><?php echo esc_html($review['name'] ?? 'Klient'); ?></strong>
              <p><?php echo esc_html($review['text'] ?? ''); ?></p>
              <span><img src="<?php echo esc_url($zp_review_logo($review['source'] ?? 'Google')); ?>" alt=""> <?php echo esc_html($review['source'] ?? 'Google'); ?></span>
            </article>
          <?php endforeach; ?>

          <article class="zpRevEd__miniCard zpRevEd__miniCard--cta">
            <strong>Twój projekt może być następny</strong>
            <p>Opisz nam, czego potrzebujesz — dobierzemy zakres, styl, technologię, SEO i plan wdrożenia.</p>
            <a href="/kontakt/">Otrzymaj wycenę<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 17 17 7"></path><path d="M7 7h10v10"></path></svg></a>
          </article>

        </div>
      </div>
    </div>

    <footer class="zpRevEd__final zpRevEd__reveal" data-reveal-delay="100">
      <div class="zpRevEd__finalBgText" aria-hidden="true">trusted by ambitious brands</div>

      <div class="zpRevEd__finalInner">
        <div class="zpRevEd__finalCopy">
          <span class="zpRevEd__kicker">Zaprojektowani.com</span>
          <h3>Dołącz do marek, które chcą wyglądać lepiej, działać szybciej i sprzedawać skuteczniej.</h3>
          <p>
            Strony internetowe, sklepy, logo, branding, SEO i kampanie — projektowane tak,
            żeby całość wyglądała spójnie i realnie wspierała sprzedaż.
          </p>
        </div>

        <div class="zpRevEd__finalActions">
          <a href="/kontakt/" class="zpRevEd__btn">
            Darmowa wycena
            <svg viewBox="0 0 24 24" aria-hidden="true">
              <path d="M7 17 17 7"></path>
              <path d="M7 7h10v10"></path>
            </svg>
          </a>

          <a href="/strony-internetowe-katowice/" class="zpRevEd__btn zpRevEd__btn--ghost zpRevEd__btn--ghostDark">
            Oferta stron i sklepów
            <svg viewBox="0 0 24 24" aria-hidden="true">
              <path d="M7 17 17 7"></path>
              <path d="M7 7h10v10"></path>
            </svg>
          </a>
        </div>
      </div>
    </footer>

  </div>
</section>

<style>.zpRevEd,.zpRevEd *,.zpRevEd *::before,.zpRevEd *::after{box-sizing: border-box;font-family: "Plus Jakarta Sans Local","Plus Jakarta Sans Local",system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;font-style: normal}.zpRevEd{--black: #050505;--muted: rgba(5,5,5,.58);--soft: rgba(5,5,5,.045);--line: rgba(5,5,5,.105);--line2: rgba(5,5,5,.075);--navy: #102a4f;--navy2: #1c477a;--navy3: #3b6ea8;--brand1: #A67CFF;--brand2: #1c477a;--brand3: #FFC271;--brandGrad: linear-gradient(90deg,var(--brand1),var(--brand2),var(--brand3));--grad: linear-gradient(90deg,var(--navy),var(--navy2),var(--navy3));--btnGrad: linear-gradient(90deg,#05070b 0%,#0b1830 42%,#102a4f 72%,#1c477a 100%);width: 100%;max-width: 100%;margin: 0;padding: clamp(76px,7vw,124px) 0 0;position: relative;isolation: isolate;overflow: hidden;color: var(--black);background: radial-gradient(circle at 10% 0%,rgba(16,42,79,.040),transparent 36%),radial-gradient(circle at 92% 8%,rgba(59,110,168,.038),transparent 34%),linear-gradient(180deg,#fbfaf8 0%,#ffffff 48%,#f8f9fb 100%);-webkit-font-smoothing: antialiased;-moz-osx-font-smoothing: grayscale;text-rendering: geometricPrecision}.zpRevEd__bgText{position: absolute;z-index: -1;top: .045em;left: 50%;right: auto;width: 100vw;max-width: 100vw;transform: translateX(-50%) translateZ(0);display: flex;justify-content: flex-end;align-items: flex-start;padding-right: max(-46px,calc((100vw - 1500px) / 2 - 74px));color: #050505;opacity: .030;font-size: clamp(118px,18.8vw,330px);line-height: .78;letter-spacing: -.105em;font-weight: 760;font-variation-settings: "wght" 760;pointer-events: none;user-select: none;white-space: nowrap;text-transform: lowercase;will-change: transform,opacity;backface-visibility: hidden;-webkit-mask-image: linear-gradient(90deg,transparent 0%,#000 10%,#000 90%,transparent 100%),linear-gradient(180deg,transparent 0%,#000 18%,#000 84%,transparent 100%);mask-image: linear-gradient(90deg,transparent 0%,#000 10%,#000 90%,transparent 100%),linear-gradient(180deg,transparent 0%,#000 18%,#000 84%,transparent 100%);-webkit-mask-composite: source-in;mask-composite: intersect}.zpRevEd__inner{width: min(1740px,calc(100% - clamp(28px,6vw,112px)));margin: 0 auto;position: relative;z-index: 2}.zpRevEd__reveal{opacity: 0;transform: translate3d(0,32px,0) scale(.992);filter:none;transition: opacity .78s cubic-bezier(.16,1,.3,1),transform .88s cubic-bezier(.16,1,.3,1),filter .88s cubic-bezier(.16,1,.3,1)}.zpRevEd__reveal.is-in{opacity: 1;transform: translate3d(0,0,0) scale(1);filter:none}.zpRevEd__hero{display: grid;grid-template-columns: minmax(0,1.05fr) minmax(360px,.72fr);gap: clamp(44px,7vw,128px);align-items: start;padding-bottom: clamp(48px,5vw,78px);border-bottom: 1px solid var(--line)}.zpRevEd__kicker{display: inline-flex;width: max-content;align-items: center;gap: 10px;margin: 0 0 18px;color: rgba(5,5,5,.52);text-transform: uppercase;letter-spacing: .14em;font-size: 10px;line-height: 1;font-weight: 820}.zpRevEd__kicker::before{content: "";width: 26px;height: 1px;background: var(--grad);transform-origin: left center;animation: zpRevLinePulse 2.8s ease-in-out infinite}@keyframes zpRevLinePulse{0%,100%{transform: scaleX(.72);opacity: .55}50%{transform: scaleX(1);opacity: 1}}.zpRevEd__title{max-width: 900px;margin: 0;color: #050505;font-size: clamp(42px,4.75vw,65px);line-height: .94;letter-spacing: -.062em;font-weight: 560;font-variation-settings: "wght" 560;text-wrap: balance}.zpRevEd__scoreBox{width: min(360px,100%);margin-top: clamp(42px,4.6vw,74px);position: relative}.zpRevEd__scoreBox > span{display: block;margin-bottom: 18px;color: rgba(5,5,5,.48);text-transform: uppercase;letter-spacing: .14em;font-size: 10px;line-height: 1;font-weight: 820}.zpRevEd__scoreGlow{width: max-content;position: relative;isolation: isolate}.zpRevEd__scoreGlow::before{content: "";position: absolute;z-index: -1;inset: 4% -12% 0 -8%;border-radius: 999px;background: radial-gradient(circle at 18% 45%,rgba(16,42,79,.12),transparent 38%),radial-gradient(circle at 70% 50%,rgba(59,110,168,.10),transparent 34%);filter:none;opacity: .8;animation: zpRevScoreAura 3.8s ease-in-out infinite}@keyframes zpRevScoreAura{0%,100%{transform: scale(.96);opacity: .54}50%{transform: scale(1.04);opacity: .9}}.zpRevEd__scoreGlow strong{display: block;margin: 0;color: var(--navy);font-size: clamp(72px,7vw,108px);line-height: .78;letter-spacing: -.055em;font-weight: 860;font-variation-settings: "wght" 860;font-kerning: none;font-feature-settings: "tnum" 1,"kern" 0;position: relative;transform: translateZ(0);backface-visibility: hidden}.zpRevEd__scoreGlow strong::after{content: attr(data-score);position: absolute;left: 0;top: 0;width: 100%;height: 100%;z-index: 2;pointer-events: none;color: transparent;font: inherit;font-size: inherit;line-height: inherit;letter-spacing: inherit;font-weight: inherit;font-variation-settings: inherit;font-kerning: inherit;font-feature-settings: inherit;text-align: left;white-space: nowrap;transform: translateZ(0);background: linear-gradient( 104deg,transparent 0%,transparent 34%,rgba(255,255,255,0) 40%,rgba(255,255,255,.98) 49%,rgba(255,255,255,.28) 55%,transparent 64%,transparent 100% );background-size: 230% 100%;background-position: -130% 0;-webkit-background-clip: text;background-clip: text;-webkit-text-fill-color: transparent;animation: zpRevDigitShine 3.6s cubic-bezier(.16,1,.3,1) infinite}@keyframes zpRevDigitShine{0%,18%{background-position: -130% 0;opacity: 0}28%{opacity: .95}58%{background-position: 230% 0;opacity: .85}70%,100%{background-position: 230% 0;opacity: 0}}.zpRevEd__scoreBox em{display: block;margin-top: 16px;color: var(--navy);font-size: 16px;line-height: 1;letter-spacing: .06em;font-style: normal}.zpRevEd__scoreBox p{margin: 18px 0 0;color: rgba(5,5,5,.58);font-size: 13px;line-height: 1.66;letter-spacing: -0.004em}.zpRevEd__heroRight{padding-top: clamp(22px,3vw,42px)}.zpRevEd__intro{max-width: 620px;margin: 0;color: rgba(5,5,5,.62);font-size: clamp(14px,1.08vw,16px);line-height: 1.78;letter-spacing: -0.008em}.zpRevEd__platforms{display: grid;grid-template-columns: 1fr 1fr;gap: 12px;margin-top: 30px;padding-bottom: 24px;border-bottom: 1px solid var(--line)}.zpRevEd__platform{min-width: 0;display: grid;grid-template-columns: 38px minmax(0,1fr);gap: 12px;align-items: center;padding: 12px;margin: -12px;border-radius: 22px;transition: background .24s ease,transform .24s cubic-bezier(.16,1,.3,1)}.zpRevEd__platform:hover{background: rgba(255,255,255,.74);transform: translateY(-2px)}.zpRevEd__platform img{width: 32px;height: 32px;object-fit: contain;display: block;transition: transform .32s cubic-bezier(.16,1,.3,1)}.zpRevEd__platform:hover img{transform: rotate(-4deg) scale(1.08)}.zpRevEd__platform strong{display: block;color: #050505;font-size: 25px;line-height: .9;letter-spacing: -.052em;font-weight: 600}.zpRevEd__platform span{display: block;margin-top: 5px;color: rgba(5,5,5,.50);font-size: 11px;line-height: 1.2;letter-spacing: .05em;text-transform: uppercase;font-weight: 720}.zpRevEd__actions,.zpRevEd__finalActions{display: flex;flex-wrap: wrap;gap: 10px;margin-top: 24px}.zpRevEd__btn{min-height: 52px;display: inline-flex;align-items: center;justify-content: center;gap: 10px;padding: 0 23px;border-radius: 999px;background: #05070b;color: #ffffff !important;text-decoration: none !important;font-size: 13px;line-height: 1;font-weight: 760;letter-spacing: -0.006em;border: 1px solid #05070b;position: relative;overflow: hidden;z-index: 1;transition: transform .2s ease,border-color .2s ease,color .2s ease,background .2s ease}.zpRevEd__btn::before{content: "";position: absolute;inset: 0;z-index: -1;background: var(--btnGrad);transform: scaleX(1);transform-origin: left;transition: transform .28s cubic-bezier(.4,0,.2,1)}.zpRevEd__btn:hover{transform: translateY(-2px);border-color: rgba(28,71,122,.45);color: #ffffff !important}.zpRevEd__btn svg{width: 16px;height: 16px;stroke: currentColor;fill: none;stroke-width: 2;stroke-linecap: round;stroke-linejoin: round;transition: transform .2s ease}.zpRevEd__btn:hover svg{transform: translate(3px,-3px)}.zpRevEd__btn--ghost{background: #ffffff;color: #050505 !important;border-color: rgba(5,5,5,.12)}.zpRevEd__btn--ghost::before{transform: scaleX(0)}.zpRevEd__btn--ghost:hover{color: #ffffff !important;background: #05070b}.zpRevEd__btn--ghost:hover::before{transform: scaleX(1)}.zpRevEd__btn--ghostDark{background: rgba(255,255,255,.08);color: #ffffff !important;border-color: rgba(255,255,255,.18)}.zpRevEd__btn--ghostDark::before{transform: scaleX(0)}.zpRevEd__btn--ghostDark:hover{color: #ffffff !important;border-color: rgba(255,255,255,.30);background: rgba(255,255,255,.08)}.zpRevEd__btn--ghostDark:hover::before{transform: scaleX(1)}.zpRevEd__featured{display: grid;grid-template-columns: minmax(220px,330px) minmax(0,1fr) minmax(160px,240px);gap: clamp(28px,5vw,78px);align-items: center;padding: clamp(44px,5vw,76px) clamp(18px,2vw,30px);border: 1px solid var(--line2);border-radius: 28px;margin-top: 26px;margin-bottom: 20px;background: rgba(255,255,255,.4);position: relative;overflow: hidden;transition: background .28s ease,border-color .28s ease,transform .28s cubic-bezier(.16,1,.3,1),box-shadow .28s ease}.zpRevEd__featured::before{content: "";position: absolute;inset: 0;background: linear-gradient(90deg,transparent,rgba(255,255,255,.62),transparent);transform: translateX(-120%);opacity: 0;pointer-events: none}.zpRevEd__featured:hover{background: #ffffff;border-color: rgba(5,5,5,.13);transform: translateY(-4px);box-shadow: none}.zpRevEd__featured:hover::before{animation: zpRevCardSweep 1.15s cubic-bezier(.16,1,.3,1)}@keyframes zpRevCardSweep{0%{opacity: 0;transform: translateX(-120%)}22%{opacity: .42}100%{opacity: 0;transform: translateX(120%)}}.zpRevEd__featuredMeta{display: flex;align-items: center;gap: 14px;position: relative;z-index: 2}.zpRevEd__avatar{width: 58px;height: 58px;border-radius: 22px;display: grid;place-items: center;background: #05070b;color: #ffffff;font-size: 14px;line-height: 1;font-weight: 780;letter-spacing: -.02em;transition: transform .28s cubic-bezier(.16,1,.3,1)}.zpRevEd__featured:hover .zpRevEd__avatar{transform: rotate(-3deg) scale(1.04)}.zpRevEd__featuredMeta strong{display: block;color: #050505;font-size: clamp(17px,1.22vw,22px);line-height: 1.06;letter-spacing: -.035em;font-weight: 760}.zpRevEd__featuredMeta span{display: block;margin-top: 8px;color: var(--navy);font-size: 10px;line-height: 1;letter-spacing: .13em;text-transform: uppercase;font-weight: 820}.zpRevEd__featured blockquote{margin: 0;padding-left: clamp(24px,3vw,54px);border-left: 4px solid #050505;color: #050505;font-size: clamp(32px,3.6vw,54px);line-height: 1;letter-spacing: -.06em;font-weight: 520;font-variation-settings: "wght" 520;text-wrap: balance;position: relative;z-index: 2}.zpRevEd__featuredSource,.zpRevEd__quoteSource{display: flex;align-items: center;gap: 10px;justify-content: flex-start;position: relative;z-index: 2}.zpRevEd__featuredSource{justify-content: flex-end}.zpRevEd__featuredSource img,.zpRevEd__quoteSource img{width: 28px;height: 28px;object-fit: contain;display: block;transition: transform .28s cubic-bezier(.16,1,.3,1)}.zpRevEd__featured:hover .zpRevEd__featuredSource img,.zpRevEd__quote:hover .zpRevEd__quoteSource img{transform: scale(1.08)}.zpRevEd__featuredSource span,.zpRevEd__quoteSource span{color: rgba(5,5,5,.48);text-transform: uppercase;letter-spacing: .13em;font-size: 10px;line-height: 1;font-weight: 780}.zpRevEd__quoteList{display: grid;gap: 20px;margin-top: 20px}.zpRevEd__quote{display: grid;grid-template-columns: minmax(190px,280px) minmax(0,1fr) minmax(150px,240px);gap: clamp(28px,4vw,68px);align-items: center;padding: clamp(34px,3.8vw,58px) clamp(18px,2vw,30px);border: 1px solid var(--line2);border-radius: 26px;background: rgba(255,255,255,.4);position: relative;overflow: hidden;transition: background .28s ease,border-color .28s ease,transform .28s cubic-bezier(.16,1,.3,1),box-shadow .28s ease}.zpRevEd__quote::before{content: "";position: absolute;inset: 0;background: linear-gradient(90deg,transparent,rgba(255,255,255,.58),transparent);transform: translateX(-120%);opacity: 0;pointer-events: none}.zpRevEd__quote:hover{background: #ffffff;border-color: rgba(5,5,5,.13);transform: translateY(-4px);box-shadow: none}.zpRevEd__quote:hover::before{animation: zpRevCardSweep 1.15s cubic-bezier(.16,1,.3,1)}.zpRevEd__quoteMeta,.zpRevEd__quote p{position: relative;z-index: 2}.zpRevEd__quoteMeta strong{display: block;color: #050505;font-size: clamp(17px,1.18vw,21px);line-height: 1.06;letter-spacing: -.034em;font-weight: 760}.zpRevEd__quoteMeta span{display: block;margin-top: 9px;color: var(--navy);text-transform: uppercase;letter-spacing: .13em;font-size: 10px;line-height: 1.25;font-weight: 820}.zpRevEd__quote p{margin: 0;padding-left: clamp(22px,2.2vw,42px);border-left: 4px solid #050505;color: #050505;font-size: clamp(26px,2.7vw,42px);line-height: 1.04;letter-spacing: -.058em;font-weight: 520;font-variation-settings: "wght" 520;text-wrap: balance}.zpRevEd__quoteSource{justify-content: flex-end}.zpRevEd__chips{display: flex;flex-wrap: wrap;gap: 10px;padding: 34px 0;border-bottom: 1px solid var(--line)}.zpRevEd__chips span{min-height: 39px;display: inline-flex;align-items: center;padding: 0 16px;border: 1px solid rgba(5,5,5,.12);background: rgba(255,255,255,.68);color: rgba(5,5,5,.62);text-transform: uppercase;letter-spacing: .105em;font-size: 10px;line-height: 1;font-weight: 780;transition: background .22s ease,color .22s ease,border-color .22s ease,transform .22s ease}.zpRevEd__chips span:hover{background: #ffffff;color: #050505;border-color: rgba(5,5,5,.18);transform: translateY(-2px)}.zpRevEd__more{padding: clamp(54px,5vw,82px) 0 clamp(58px,5vw,88px);border-bottom: 1px solid var(--line);overflow: visible}.zpRevEd__moreHead{display: flex;align-items: end;justify-content: space-between;gap: 24px;margin-bottom: 26px}.zpRevEd__moreHead h3{max-width: 780px;margin: 0;color: #050505;font-size: clamp(30px,3.4vw,56px);line-height: .98;letter-spacing: -.06em;font-weight: 540;font-variation-settings: "wght" 540;text-wrap: balance}.zpRevEd__dragHint{display: inline-flex;align-items: center;gap: 14px;color: var(--navy);text-transform: uppercase;letter-spacing: .14em;font-size: 10px;line-height: 1;font-weight: 820;flex: 0 0 auto}.zpRevEd__dragHint i{width: 48px;height: 48px;border: 1px solid rgba(16,42,79,.16);background: #ffffff;display: grid;place-items: center;animation: zpRevHintPulse 1.65s ease-in-out infinite}.zpRevEd__dragHint svg{width: 22px;height: 22px;stroke: currentColor;fill: none;stroke-width: 2;stroke-linecap: round;stroke-linejoin: round}@keyframes zpRevHintPulse{0%,100%{transform: translateX(0);box-shadow: 0 10px 28px rgba(7,20,38,.045)}50%{transform: translateX(8px);box-shadow: 0 18px 48px rgba(7,20,38,.085)}}.zpRevEd__railWrap{width: 100vw;margin-left: calc(50% - 50vw);overflow-x: clip;overflow-y: visible}.zpRevEd__rail{display: flex;gap: 0;overflow-x: auto;overflow-y: hidden;scroll-snap-type: x mandatory;overscroll-behavior-x: contain;scrollbar-width: none;-webkit-overflow-scrolling: touch;padding: 0 max(calc((100vw - 1740px) / 2),calc(clamp(28px,6vw,112px) / 2)) 20px;cursor: grab}.zpRevEd__rail.is-dragging{cursor: grabbing}.zpRevEd__rail::-webkit-scrollbar{display: none}.zpRevEd__miniCard{flex: 0 0 min(430px,78vw);min-height: 255px;scroll-snap-align: start;scroll-snap-stop: always;padding: 28px 30px;border-left: 1px solid var(--line);background: rgba(255,255,255,.28);display: flex;flex-direction: column;justify-content: space-between;transition: background .26s ease,transform .26s cubic-bezier(.16,1,.3,1),box-shadow .26s ease,border-color .26s ease}.zpRevEd__miniCard:hover{background: #ffffff;transform: translateY(-5px);border-color: rgba(5,5,5,.13);box-shadow: none}.zpRevEd__miniCard:last-child{border-right: 1px solid var(--line)}.zpRevEd__miniCard strong{display: block;color: var(--navy);text-transform: uppercase;letter-spacing: .12em;font-size: clamp(13px,.9vw,16px);line-height: 1.12;font-weight: 820}.zpRevEd__miniCard p{margin: 22px 0;color: #050505;font-size: clamp(24px,2.05vw,34px);line-height: 1.04;letter-spacing: -.055em;font-weight: 520;font-variation-settings: "wght" 520}.zpRevEd__miniCard span{display: flex;align-items: center;gap: 8px;color: rgba(5,5,5,.52);text-transform: uppercase;letter-spacing: .13em;font-size: 10px;line-height: 1;font-weight: 760}.zpRevEd__miniCard span img{width: 20px;height: 20px;object-fit: contain;display: block}.zpRevEd__miniCard--cta{background: #05070b;color: #ffffff;border-left-color: rgba(255,255,255,.16)}.zpRevEd__miniCard--cta:hover{background: #05070b;box-shadow: none}.zpRevEd__miniCard--cta strong,.zpRevEd__miniCard--cta p{color: #ffffff}.zpRevEd__miniCard--cta p{color: rgba(255,255,255,.86)}.zpRevEd__miniCard--cta a{width: max-content;min-height: 46px;display: inline-flex;align-items: center;gap: 10px;padding: 0 18px;border-radius: 999px;background: #ffffff;color: #050505 !important;text-decoration: none !important;font-size: 13px;line-height: 1;font-weight: 760}.zpRevEd__miniCard--cta svg{width: 15px;height: 15px;stroke: currentColor;fill: none;stroke-width: 2;stroke-linecap: round;stroke-linejoin: round}.zpRevEd__final{width: 100vw;max-width: 100vw;margin-left: calc(50% - 50vw);margin-top: clamp(54px,5vw,84px);padding: clamp(72px,7vw,118px) 0;position: relative;isolation: isolate;overflow: hidden;background: radial-gradient(circle at 12% 0%,rgba(59,110,168,.16),transparent 38%),radial-gradient(circle at 88% 20%,rgba(28,71,122,.13),transparent 34%),linear-gradient(135deg,#030407 0%,#05070b 48%,#07101d 100%);color: #ffffff}.zpRevEd__final::before{content: "";position: absolute;inset: 0;z-index: -2;opacity: .045;background-image: linear-gradient(rgba(255,255,255,.18) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.18) 1px,transparent 1px);background-size: 54px 54px;pointer-events: none}.zpRevEd__final::after{content: "";position: absolute;left: 50%;bottom: 42px;z-index: -1;width: min(1500px,calc(100% - 56px));height: 1px;transform: translateX(-50%);background: linear-gradient(90deg,transparent,rgba(255,255,255,.30),transparent);opacity: .72}.zpRevEd__finalInner{width: min(1500px,calc(100% - 56px));max-width: 100%;margin: 0 auto;display: grid;grid-template-columns: minmax(0,1fr) auto;gap: clamp(28px,5vw,80px);align-items: center;position: relative;z-index: 2}.zpRevEd__finalBgText{position: absolute;z-index: -1;left: 50%;right: auto;bottom: -.13em;width: 100vw;max-width: 100vw;transform: translateX(-50%) translateZ(0);display: flex;justify-content: flex-end;align-items: flex-end;padding-right: max(-54px,calc((100vw - 1500px) / 2 - 68px));color: #ffffff;opacity: .052;font-size: clamp(70px,9.5vw,168px);line-height: .78;letter-spacing: -.105em;font-weight: 760;font-variation-settings: "wght" 760;white-space: nowrap;pointer-events: none;user-select: none;text-transform: lowercase;will-change: transform,opacity;backface-visibility: hidden;-webkit-mask-image: linear-gradient(90deg,transparent 0%,#000 10%,#000 90%,transparent 100%),linear-gradient(180deg,#000 0%,#000 54%,rgba(0,0,0,.70) 72%,transparent 100%);mask-image: linear-gradient(90deg,transparent 0%,#000 10%,#000 90%,transparent 100%),linear-gradient(180deg,#000 0%,#000 54%,rgba(0,0,0,.70) 72%,transparent 100%);-webkit-mask-composite: source-in;mask-composite: intersect}.zpRevEd__final .zpRevEd__kicker{color: rgba(255,255,255,.64)}.zpRevEd__final .zpRevEd__kicker::before{background: linear-gradient(90deg,rgba(255,255,255,.26),rgba(255,255,255,.68))}.zpRevEd__final h3{max-width: 920px;margin: 0;color: #ffffff;font-size: clamp(34px,4.55vw,65px);line-height: .96;letter-spacing: -.064em;font-weight: 540;font-variation-settings: "wght" 540;text-wrap: balance}.zpRevEd__final p{max-width: 700px;margin: 22px 0 0;color: rgba(255,255,255,.68);font-size: clamp(13px,1vw,15px);line-height: 1.72;letter-spacing: -0.006em;font-weight: 400}.zpRevEd__finalActions{justify-content: flex-end;margin-top: 0;position: relative;z-index: 2}@media (min-width: 1181px){.zpRevEd__bgText{top: .03em;font-size: clamp(150px,19.4vw,350px);opacity: .030}.zpRevEd__finalBgText{bottom: -.11em;opacity: .052}}@media (max-width: 1180px){.zpRevEd__hero{grid-template-columns: 1fr;gap: 38px}.zpRevEd__heroRight{padding-top: 0;max-width: 760px}.zpRevEd__scoreBox{margin-top: 44px}.zpRevEd__featured,.zpRevEd__quote{grid-template-columns: 1fr;gap: 22px}.zpRevEd__featuredSource,.zpRevEd__quoteSource{justify-content: flex-start}.zpRevEd__finalInner{grid-template-columns: 1fr}.zpRevEd__finalActions{justify-content: flex-start}.zpRevEd__bgText{top: .04em;font-size: clamp(118px,22vw,260px);opacity: .028}}@media (max-width: 760px){html,body{overflow-x: clip}@supports not (overflow-x: clip){html,body{overflow-x: hidden}}.zpRevEd{padding: 60px 0 0;overflow-x: hidden;contain: layout;background: radial-gradient(circle at 14% 0%,rgba(16,42,79,.032),transparent 34%),linear-gradient(180deg,#fbfaf8 0%,#ffffff 50%,#f8f9fb 100%)}.zpRevEd__bgText{top: .16em;justify-content: center;padding-right: 0;font-size: clamp(92px,30vw,150px);letter-spacing: -.10em;opacity: .026;will-change: auto;transform: translateX(-50%);-webkit-mask-image: linear-gradient(90deg,transparent 0%,#000 8%,#000 92%,transparent 100%),linear-gradient(180deg,transparent 0%,#000 20%,#000 82%,transparent 100%);mask-image: linear-gradient(90deg,transparent 0%,#000 8%,#000 92%,transparent 100%),linear-gradient(180deg,transparent 0%,#000 20%,#000 82%,transparent 100%)}.zpRevEd__inner{width: calc(100% - 20px)}.zpRevEd__reveal,.zpRevEd__reveal.is-in{opacity: 1 !important;filter: none !important;transform: none !important;transition: none !important;will-change: auto !important}.zpRevEd__title{font-size: clamp(38px,11vw,50px);line-height: .96;letter-spacing: -.062em}.zpRevEd__scoreGlow::before,.zpRevEd__scoreGlow strong::after,.zpRevEd__dragHint i,.zpRevEd__kicker::before{animation: none !important}.zpRevEd__scoreGlow strong{font-weight: 860;font-variation-settings: "wght" 860;letter-spacing: -.05em}.zpRevEd__scoreGlow strong::after{opacity: 0 !important;display: none !important}.zpRevEd__platforms{grid-template-columns: 1fr}.zpRevEd__actions,.zpRevEd__finalActions{display: grid;grid-template-columns: 1fr;width: 100%}.zpRevEd__btn{width: 100%;min-height: 52px}.zpRevEd__featured{padding: 32px 16px;border-radius: 22px}.zpRevEd__featured blockquote{padding-left: 20px;border-left-width: 3px;font-size: clamp(29px,8.5vw,40px);line-height: 1.02}.zpRevEd__quote{padding: 30px 14px;border-radius: 22px}.zpRevEd__quote p{padding-left: 18px;border-left-width: 3px;font-size: clamp(25px,7.4vw,35px);line-height: 1.04}.zpRevEd__quoteMeta strong,.zpRevEd__featuredMeta strong{font-size: 18px;line-height: 1.08}.zpRevEd__chips{gap: 8px;padding: 28px 0}.zpRevEd__chips span{min-height: 36px;padding: 0 12px;font-size: 9px;letter-spacing: .095em}.zpRevEd__moreHead{align-items: start;display: grid;grid-template-columns: 1fr}.zpRevEd__moreHead h3{font-size: clamp(30px,9vw,43px)}.zpRevEd__dragHint{justify-content: flex-end}.zpRevEd__railWrap{overflow-x: hidden;overflow-y: visible;contain: layout;transform: translateZ(0)}.zpRevEd__rail{padding-left: 10px;padding-right: calc(16vw + 10px);padding-bottom: 10px;scroll-snap-type: none !important;overscroll-behavior-x: auto;-webkit-overflow-scrolling: touch;touch-action: pan-x pan-y;cursor: default;will-change: auto;contain: none}.zpRevEd__miniCard{flex: 0 0 84vw;min-height: 250px;padding: 24px 22px;scroll-snap-align: none !important;scroll-snap-stop: normal !important;transform: none;backface-visibility: hidden;transition: none}.zpRevEd__miniCard p{font-size: clamp(24px,7.4vw,32px)}.zpRevEd__miniCard strong{font-size: 14px}.zpRevEd__final{padding: 58px 0 70px}.zpRevEd__finalInner{width: calc(100% - 20px);grid-template-columns: 1fr;gap: 30px}.zpRevEd__final h3{font-size: clamp(31px,9.2vw,44px);line-height: .99;letter-spacing: -.058em}.zpRevEd__final p{font-size: 13px;line-height: 1.62}.zpRevEd__final::after{width: calc(100% - 20px);bottom: 28px}.zpRevEd__finalBgText{justify-content: flex-end;padding-right: 0;right: auto;bottom: -.08em;font-size: clamp(56px,16vw,96px);letter-spacing: -.10em;white-space: normal;max-width: 100vw;text-align: right;opacity: .050;will-change: auto;transform: translateX(-50%);-webkit-mask-image: linear-gradient(90deg,transparent 0%,#000 8%,#000 92%,transparent 100%),linear-gradient(180deg,#000 0%,#000 50%,rgba(0,0,0,.62) 70%,transparent 100%);mask-image: linear-gradient(90deg,transparent 0%,#000 8%,#000 92%,transparent 100%),linear-gradient(180deg,#000 0%,#000 50%,rgba(0,0,0,.62) 70%,transparent 100%)}}@media (max-width: 420px){.zpRevEd__bgText{top: .20em;font-size: clamp(84px,31vw,132px);opacity: .026}.zpRevEd__finalBgText{font-size: clamp(52px,15vw,82px);opacity: .048}}@media (hover: none) and (pointer: coarse){.zpRevEd__featured:hover,.zpRevEd__quote:hover,.zpRevEd__miniCard:hover,.zpRevEd__platform:hover,.zpRevEd__chips span:hover,.zpRevEd__btn:hover{transform: none !important;box-shadow: none !important}.zpRevEd__featured:hover::before,.zpRevEd__quote:hover::before{animation: none !important;opacity: 0 !important}.zpRevEd__platform:hover img,.zpRevEd__featured:hover .zpRevEd__avatar,.zpRevEd__featured:hover .zpRevEd__featuredSource img,.zpRevEd__quote:hover .zpRevEd__quoteSource img,.zpRevEd__btn:hover svg{transform: none !important}}@media (max-width: 760px) and (hover: none) and (pointer: coarse){.zpRevEd__scoreGlow::before,.zpRevEd__scoreGlow strong::after,.zpRevEd__dragHint i,.zpRevEd__kicker::before{animation: none !important}}@media (prefers-reduced-motion: reduce){.zpRevEd__reveal,.zpRevEd__featured,.zpRevEd__quote,.zpRevEd__miniCard,.zpRevEd__chips span,.zpRevEd__btn,.zpRevEd__dragHint i,.zpRevEd__scoreGlow::before,.zpRevEd__scoreGlow strong::after,.zpRevEd__kicker::before{animation: none !important;transition: none !important;transform: none !important;filter: none !important}.zpRevEd__reveal{opacity: 1 !important}}</style>

<script>
(function(){
  var root = document.querySelector('[data-zp-rev-ed]');
  if (!root || root.dataset.zpReady === '1') return;
  root.dataset.zpReady = '1';

  var rail = root.querySelector('[data-zp-rev-rail]');
  var revealEls = root.querySelectorAll('.zpRevEd__reveal');
  var mqMobile = window.matchMedia ? window.matchMedia('(max-width: 760px)') : null;
  var isMobile = mqMobile ? mqMobile.matches : false;

  /* MOBILE STABLE:
     Na telefonie nie odpalamy reveal przez IntersectionObserver,
     bo transformy/zmiany layerów potrafią powodować skoki przy scrollu.
  */
  if (isMobile) {
    revealEls.forEach(function(el) {
      el.classList.add('is-in');
    });
  } else if ('IntersectionObserver' in window) {
    var io = new IntersectionObserver(function(entries) {
      entries.forEach(function(entry) {
        if (!entry.isIntersecting) return;

        var delay = Number(entry.target.getAttribute('data-reveal-delay') || 0);

        window.setTimeout(function() {
          window.requestAnimationFrame(function(){
            entry.target.classList.add('is-in');
          });
        }, delay);

        io.unobserve(entry.target);
      });
    }, {
      threshold: 0.14,
      rootMargin: '0px 0px -8% 0px'
    });

    revealEls.forEach(function(el) {
      io.observe(el);
    });
  } else {
    revealEls.forEach(function(el) {
      el.classList.add('is-in');
    });
  }

  if (!rail) return;

  /* Desktop mouse-only drag.
     Mobile/touch zostaje natywny — bez pointer capture, bez JS scrollLeft,
     żeby nie powodować vertical scroll jumpów.
  */
  if (isMobile) return;

  var isDown  = false;
  var startX  = 0;
  var startLeft = 0;

  rail.addEventListener('pointerdown', function(e) {
    if (e.pointerType !== 'mouse') return;
    isDown    = true;
    startX    = e.clientX;
    startLeft = rail.scrollLeft;
    rail.classList.add('is-dragging');
    if (rail.setPointerCapture) rail.setPointerCapture(e.pointerId);
  });

  rail.addEventListener('pointermove', function(e) {
    if (!isDown || e.pointerType !== 'mouse') return;
    rail.scrollLeft = startLeft - (e.clientX - startX);
  });

  function stopDrag() {
    isDown = false;
    rail.classList.remove('is-dragging');
  }

  rail.addEventListener('pointerup', stopDrag);
  rail.addEventListener('pointercancel', stopDrag);
  rail.addEventListener('mouseleave', stopDrag);
})();
</script>

    <?php
    return ob_get_clean();
  }

  add_shortcode('zp_reviews_section', 'zp_reviews_section_shortcode');
}
